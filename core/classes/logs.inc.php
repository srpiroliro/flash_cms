<?php

class Logs {
    public static function maker($db,$desc,$sql, $user=0 ){
    
        $user=($user==0 AND isset($_SESSION["mod_id"]) ? $_SESSION["mod_id"]:$user);

        $log='User IP: '.$_SERVER['REMOTE_ADDR'].' | '.
            'Domain: '.$_SERVER['SERVER_NAME'].' | '.
            'Page: '.Validator::varFilter($_SERVER['REQUEST_URI']).' | '.
            #($page_id!='' ? 'Internal page id: '.$page_id.' | ':'').
            $desc;

   	    
        $query="INSERT INTO db_log SET log_time=NOW(),".
            " log_ip= ? , ". 
            " moderator_id= ? , ".
            " log_details= ? , ".
            " log_query= ? "; 

        $stmt=$db->prepare($query);

        $encoded_sql=base64_encode($sql);
        $stmt->bind_param('ssss', $_SERVER['REMOTE_ADDR'], $user, $log, $encoded_sql);

        return $stmt;
    }

    public static function write($data,$db) {
        	   	  	
        if($data['user']>0){
               $sql=Logs::maker($db,$data['desc'],$data['query'],$data['user']);
        } else $sql=Logs::maker($db,$data['desc'],$data['query']);

        if($sql->execute() == FALSE){
            return 'Log: sql error: '.$sql->error; 
        }                            
    } 
}

?>
