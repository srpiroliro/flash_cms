<?php
require_once($_SERVER['DOCUMENT_ROOT']."core/conf/conf.inc.php");


$auth=new Auth($this->conf_array);

if($auth->loginCheck()=="admin"){
    $token=Validator::varAccept('token',$this->url_vars);
    if(password_verify($_SESSION['mod_email'], $token)){
        $err=0;

        $mod_id=Validator::varAccept('id',$this->url_vars);
        if($mod_id!=$_SESSION['mod_id'] and $mod_id){
            $sql="SELECT mod_name FROM db_moderators WHERE mod_id= ?";
            $stmt=$this->db->prepare($sql);
            $stmt->bind_param('i', $mod_id);
            $stmt->execute();
            $result=$stmt->get_result();

            if($result->num_rows()>0){
                $sql="DELETE FROM db_moderators WHERE mod_id= ?";
                $stmt=$this->db->prepare($sql);
                $stmt->bind_param('i', $mod_id);
                $result=$stmt->execute();

                if($result) echo "200";
                else $err="Something went terriblly wrong";
            }else{
                $err="User not found!";
            }
        }else{
            $err="Can't delete yourself!";
        }
    }
} else {
    # error msg
    $err="Access denied.";
}
?>