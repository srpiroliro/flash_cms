<?php

$auth= new Auth($this->conf_array);
$user_role=$auth->loginCheck();
if($user_role){
    $err=0;

    $exploded_url=explode('/',$this->page_catalog);
    $action=$exploded_url[sizeof($exploded_url)-1];
    
    $content_id=Validator::varAccept('id',$this->url_vars);

    if($action=="edit"){
        $sql="select content_title from db_content where content_id= ?";
        $stmt=$this->db->prepare($sql);
        $stmt->bind_param('i',$content_id);
        $stmt->execute();
        $result=$stmt->get_result();

        if($result->num_rows>0){
            #select data from DB_PAGES + DB_CONTENT

            $query="SELECT db_content.content_title, db_content.lang_id, db_content.page_catalog, db_content.content_filename, db_pages.page_custom, db_pages.page_status FROM db_content RIGHT JOIN db_pages ON db_content.page_id=db_pages.page_id WHERE db_content.content_id= ?";
            $stmt=$this->db->prepare($query);
            $stmt->bind_param('i',$content_id);
            $stmt->execute();
            $result=$stmt->get_result();

            $results=$result->fetch_assoc();


            if($results['page_custom']!=1 OR $user_role=="admin"){

                $content_title=$results['content_title'];
                $url=$results['page_catalog'];
                $language=$results['lang_id'];
                $status=$results['page_status'];
                $custom=$results['page_custom'];
                
                $file=($custom==1?ROOT.'core/controller/'.$results['content_filename'].'.contr.php':ROOT.'core/content/'.$language.'/'.$results['content_filename'].'.view.php');

                if($custom==0 and file_exists($file)){
                    $body=file_get_contents($file);
                } else {
                    # file not found or custom==1
                    $body="";
                }
            
            
            } else {
                $err="Access denied.";
            }
        }else $err="No content was found.";

        if($err) header('Location: /'.$this->conf_array['backend'].'/content/');
    }

    require_once($this->conf_array['be_template']['top']);
    require_once(ROOT.'core/content/en/be_content_manage.view.php');
    require_once($this->conf_array['be_template']['bottom']);
    
} else {
    # error msg
    header('location: /'.$this->conf_array['backend']);
}

?>