<?php 
$acc=new Auth($this->conf_array);

if ($acc->loginCheck()){
    header("Location: /".$this->conf_array['backend']."/dashboard/");
    die();
} else {
    $error_msg='';

    $username=Validator::varAccept('username',$this->url_vars);
    $password=Validator::varAccept('password',$this->url_vars);
    $remember_me=Validator::varAccept('remember-me',$this->url_vars);
    $token=Validator::varAccept('token', $this->url_vars);


    if(isset($token) AND $token){
        $err=0;

        if(Auth::verify_formToken($token)){
            $acc->login("creds",$username,$password,$remember_me);
            
            if($acc->logged_in){
                header("Location: /".$this->conf_array['backend']."/dashboard/");
                die();
            } else $err=$acc->error_msg;
        } else {
            $err="Suspicious form submit!";
        }
        

        if($err){
            $error_msg='
            <div class=" mr-auto ml-auto mt-3 alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Error!</strong> '.$err.'
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            ';
        }
    }
    
    require_once(ROOT.'core/content/en/control_login.view.php');

}

?>