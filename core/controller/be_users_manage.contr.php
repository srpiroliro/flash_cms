<?php

$auth= new Auth($this->conf_array);
if($auth->loginCheck()=="admin"){
    $err=0;

    $exploded_url=explode('/',$this->page_catalog);
    $action=$exploded_url[sizeof($exploded_url)-1];

    $this->action=$action;
    
    $mod_id=Validator::varAccept('id',$this->url_vars);
    

    if($action=="edit"){
        if(isset($mod_id)){
            #select data from DB_PAGES + DB_CONTENT

            $query="SELECT * FROM db_moderators WHERE mod_id= ?";
            $stmt=$this->db->prepare($query);
            $stmt->bind_param('i',$mod_id);
            $stmt->execute();
            $result=$stmt->get_result();

            $results=$result->fetch_assoc();

            $mod_email=$results['mod_email'];
            $mod_password=$results['mod_password'];
            $mod_name=$results['mod_name'];
            $mod_hash=$results['mod_hash'];
            $mod_status=$results['mod_status'];
            $mod_role=$results['mod_role'];

        }else{
            $err=1;
        }

        if($err) header('Location: /'.$this->conf_array['backend'].'/content/');
    }

    require_once($this->conf_array['be_template']['top']);
    require_once(ROOT.'core/content/en/be_users_manage.view.php');
    require_once($this->conf_array['be_template']['bottom']);
    
} else {
    # error msg
    header('location: /'.$this->conf_array['backend']);
}

?>