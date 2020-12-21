<?php


require_once($_SERVER['DOCUMENT_ROOT']."/core/conf/conf.inc.php");


$db=                                                                $conf_array['db'];

$err_msg=                                                           [];
$user_exists=                                                       False;

$mod_id=                                                            Validator::varAccept('id',[]);
$mod_email=                                                         Validator::varAccept('mod_email',[]);
$mod_password=                                                      Validator::varAccept('mod_password',[]);
$mod_name=                                                          Validator::varAccept('mod_name',[]);
$mod_hash=                                                          '';
$mod_status=                                                        Validator::varAccept('mod_status',[]);
$mod_role=                                                          Validator::varAccept('mod_role',[]);

$action=                                                            Validator::varAccept('hidden',[]);
$token=                                                             Validator::varAccept('token',[]);

$s=0;

if(Auth::verify_formToken($token)){
    if(!isset($mod_email)&&empty($mod_name))                        $err_msg[].="Email address empty!";
    elseif(!filter_var($mod_email, FILTER_VALIDATE_EMAIL))          $err_msg[].="Email address isn't valid!";
    else{
        $mod_hash=                                                  password_hash($mod_email, PASSWORD_DEFAULT);

        if($action=="add"){
            $sql="SELECT mod_id FROM db_moderators WHERE mod_email= ?";

            $stmt=$db->prepare($sql);
            $stmt->bind_param('s',$mod_email);
            $stmt->execute();
            $results=$stmt->get_result();
            if($results->num_rows>0)                                    $err_msg[].="Email address already used!";
        }
    }

    if(!isset($mod_password)&&empty($mod_password))                 $err_msg[].="Missing password!";
    if(!isset($mod_name)&&empty($mod_name))                         $err_msg[].="Missing name!";
    
    if($mod_status=='' OR !ctype_digit($mod_status))                $err_msg[].="Missing status!";
    elseif(!isset($conf_array['statuses'][$mod_status]))            $err_msg[].="Invalid status!";
    
    if($mod_role=='' OR !ctype_digit($mod_role))                    $err_msg[].="Missing role!";
    elseif(!isset($conf_array['roles'][$mod_role]))                 $err_msg[].="Invalid role!";


    if(!$err_msg){
        if($action=="add") $mod_password=password_hash($mod_password, PASSWORD_DEFAULT);

        $query=($action=="add"?"INSERT INTO `db_moderators` (`mod_email`,`mod_password`, `mod_name`, `mod_hash`, `mod_status`, `mod_role`) VALUES (?,?,?,?,?,?)":"UPDATE `db_moderators` SET `mod_email`= ? , `mod_password`= ? , `mod_name`= ? , `mod_hash` = ? , `mod_status` = ? , `mod_role` = ?  WHERE `mod_id`= ? ");
        $stmt=$db->prepare($query);

        if($action=="add")  $stmt->bind_param('ssssii',$mod_email,$mod_password,$mod_name,$mod_hash,$mod_status,$mod_role);
        else                $stmt->bind_param('ssssiii',$mod_email,$mod_password,$mod_name,$mod_hash,$mod_status,$mod_role,$mod_id);
        $stmt->execute();
        $stmt->close();
        
        echo "200";
    } else {
        $err='';
        foreach ($err_msg as $value) {
            $err.="$value<br>";
        }

        echo $err;
    }
} else {
    echo "something went terriblly wrong!";
}

?>
