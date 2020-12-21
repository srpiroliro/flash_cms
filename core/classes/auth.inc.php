<?php

class Auth {
    protected $conf_array;
    protected $language;
    protected $db;

    public $html;
    public $logged_in;
    public $user_results;
    public $err;
    public $error_msg;

    public function __construct($conf_array) {
        $this->conf_array=$conf_array;
        $this->language=$this->conf_array['default_lang'];
        $this->db=$this->conf_array['db'];

        $this->logged_in=0;
        $this->html=0;
        $this->error_msg=[];
    }

    public function login($type='creds',$username='',$password='',$remember_me=0) {
        

        if($type=='creds'){
            if ($username && $password) {
                $query="SELECT mod_id, mod_name, mod_hash, mod_email, mod_role, mod_password FROM db_moderators".
                        " WHERE mod_status= 1".
                        " AND mod_email = ?";
                $stmt=$this->db->prepare($query);
                $stmt->bind_param('s', $username);
                $stmt->execute();
            
                $result=$stmt->get_result();
    
                if($result->num_rows>0){
                    $this->user_results=$result->fetch_assoc();
                    $this->logged_in=(password_verify($password, $this->user_results['mod_password'])?1:0);
                }
            } else {
                # make and array of errors msg's
                $this->error_msg="You have to enter the password and the username!";
            }
        } elseif ($type=='cookie'){
            $user_hash=$_COOKIE['user_hash'];
            
            $current_time=time();
            $current_date=date("Y-m-d H:i:s", $current_time);
            $cookie_expiration_time=$current_time+(31*24*3600);

            $verified=False;

            $token_data=$this->getTokenByUserHash($user_hash,0);

            if(password_verify($_COOKIE['random_token'], $token_data['token_hash']) AND $token_data["expiry_date"]>=$current_date){
                $verified=True;
            }
            var_dump($token_data["expiry_date"]>=$current_date);
            var_dump(password_verify($_COOKIE['random_token'], $token_data['token_hash']));
            var_dump($verified);
            
            if($verified AND isset($token_data['id'])){
                $query="SELECT mod_id, mod_name, mod_hash, mod_email, mod_role, mod_password FROM db_moderators".
                        " WHERE mod_status= 1".
                        " AND mod_hash = ?";
                $stmt=$this->db->prepare($query);
                $stmt->bind_param('s', $user_hash);
                $stmt->execute();
            
                $result=$stmt->get_result();

                if($result->num_rows>0){
                    $this->logged_in=1;
                    $this->user_results=$result->fetch_assoc();
                } else $this->logged_in=0;
            } else {
                if(isset($token_data['id'])){
                    $this->markAsExpired($token_data['id']);
                }
                $this->clearAuthCookies();
            }
        }


        if($this->logged_in==1){
            $results=$this->user_results;

            $mod_id=$results["mod_id"];
            $mod_hash=$results['mod_hash'];

            $this->setSession($mod_id,$results["mod_email"],$results["mod_name"],$mod_hash,$results["mod_role"]);
        
            if($remember_me=='on' OR $remember_me=='1'){
                $cookie_expiration_time=time()+(31*24*3600);  // 1 month

                # Moderator hash cookie
                setcookie("user_hash",$mod_hash,$cookie_expiration_time,'/');

                # Random token cookie
                $random_token=$this->tokenGenerator(78);
                setcookie("random_token",$random_token,$cookie_expiration_time,'/');

                # Remove old token from DB
                $token_data=$this->getTokenByUserHash($mod_hash,0);
                $token_id=$token_data['id'];
                if($token_id){
                    $this->markAsExpired($token_id);
                }
                
                $random_token_hash=password_hash($random_token, PASSWORD_DEFAULT);
                $expiry_date=date("Y-m-d H:i:s", $cookie_expiration_time);

                # New token to DB
                $this->insertToken($mod_hash,$random_token_hash,$expiry_date);

            } else {
                if($type!="cookie"){
                    $this->clearAuthCookies();
                }
            }

            $vdata["message"]=$this->conf_array['msgs']['auth_success'].'<br>';
            $this->html=Website::gotoUrl($this->conf_array['rooturl'].'/'.$this->conf_array['backend'].'/dashboard/',$vdata);

            $desc="Access granted".($type=="cookie"?" (from cookie) ":"").": $username - $password";

        }elseif($this->logged_in==0){
            
            if(!$this->error_msg) $this->error_msg="Access denied! Wrong credentials!";

            session_destroy();
            $desc="Access denied".($type=="cookie"?" (from cookie) ":"").": $username - $password";
        }

        $log_data["user"]=(isset($mod_id)?$mod_id:'');
        $log_data["desc"]=$desc;
        $log_data["url"]=$_SERVER['REQUEST_URI'];#$this->page_id;
        $log_data["query"]='';#$query;

        Logs::write($log_data, $this->db);
            
        
    }

    private function tokenGenerator($length){ 
        return bin2hex(openssl_random_pseudo_bytes($length));
    }

    private function markAsExpired($id){
        $query="UPDATE token_auth SET is_expired = 1 WHERE id = ? ";
        $stmt=$this->db->prepare($query);
        $stmt->bind_param('i',$id);
        $stmt->execute();
    }

    public function insertToken($user_hash, $token_hash, $exp_date){
        $sql="INSERT INTO token_auth (`user_hash`, `token_hash`, `expiry_date`) VALUES ( ? , ? , ? )";
        $stmt=$this->db->prepare($sql);
        $stmt->bind_param('sss', $user_hash, $token_hash, $exp_date);
        $stmt->execute();
    }

    private function getTokenByUserHash($hash,$expired){
        $query="SELECT * FROM token_auth WHERE user_hash = ? AND is_expired = ? LIMIT 1";
        $stmt=$this->db->prepare($query);
        $stmt->bind_param('si', $hash, $expired);
        $stmt->execute();
        $results=$stmt->get_result();
        
        return ($results->num_rows>0 ? $results->fetch_assoc() : False);
    }

    public static function get_roles($role_id){
        $query="SELECT ";
    }

    public static function clearAuthCookies(){
        setcookie("user_hash", "", time()-3600, '/');
        setcookie("random_token", "", time()-3600, '/');
    }

    public function setSession($id,$email,$name,$hash,$role){
        $_SESSION['mod_id']=$id;
        $_SESSION['mod_email']=$email;
        $_SESSION['mod_name']=$name;
        $_SESSION['mod_hash']=$hash;
        $_SESSION['mod_role']=$role;
    }

    public function loginCheck(){
        if(isset($_SESSION['mod_id']) AND isset($_SESSION['mod_role'])) {
            $role_id=$_SESSION['mod_role'];

            $sql="select role_name from db_roles where role_id= ?";
            $stmt=$this->db->prepare($sql);
            $stmt->bind_param('i', $role_id);
            $stmt->execute();

            $result=$stmt->get_result();
            if($result->num_rows>0){
                $result=$result->fetch_assoc();
                return strtolower($result['role_name']);

            } else return False;
            
        } elseif (isset($_COOKIE['user_hash']) AND isset($_COOKIE['random_token'])) {
            echo "cookie";
            $this->login("cookie");
            header('location: '.$_SERVER['REQUEST_URI']);
        } else return False;
    }



    public static function formToken($txt='flash_cms'){
        return password_hash($txt, PASSWORD_DEFAULT);
    }
    
    public static function verify_formToken($hash ,$txt='flash_cms'){
        return password_verify($txt, $hash);
    }
}
?>