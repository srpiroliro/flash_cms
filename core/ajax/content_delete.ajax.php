<?php

require_once($_SERVER['DOCUMENT_ROOT']."/core/conf/conf.inc.php");

$auth=new Auth($conf_array);
$user_role=$auth->loginCheck();
if($user_role){
    $token=Validator::varAccept('token',[]);
    if(Auth::verify_formToken($token)){
        $err=False;
        $err_msg='';


        $status_removed=0;
        foreach($conf_array['statuses'] as $key=>$value){
            if(strtolower($value)=="removed") $status_removed=$key;
        }

        

        $content_id=                    Validator::varAccept('id',[]);
        $custom=                        Validator::varAccept('custom',[]);
        $lang_id=                       Validator::varAccept('lang_id',[]);

        
        $sql="SELECT page_id, content_filename FROM db_content WHERE content_id= ?";
        $stmt=$db->prepare($sql);
        $stmt->bind_param('i', $content_id);
        $stmt->execute();
        $result=$stmt->get_result();
        $results=$result->fetch_assoc();
        $content_filename=      $results['content_filename'];
        $page_id=               $results['page_id'];

        if($result->num_rows>0){

            if($user_role=="admin") {
                $sql="DELETE FROM `db_content` WHERE content_id= ?";
                $stmt=$db->prepare($sql);
                $stmt->bind_param('i',$content_id);


                $content_filepath=ROOT."core/".($custom==1?'controller/':'content/'.$lang_id.'/').$content_filename.($custom==1?'.contr.php':'.view.php');
                if(file_exists($content_filepath)){
                    unlink($content_filepath);
                }
                
                $sql="SELECT content_id FROM db_content WHERE page_id= ?";
                $st=$db->prepare($sql);
                $st->bind_param('i', $page_id);
                $st->execute();
                $result=$st->get_result();

                if($result->num_rows==1){
                    $sql1="DELETE FROM `db_pages` WHERE page_id= ?";
                    $stmt1=$db->prepare($sql1);
                    $stmt1->bind_param('i',$page_id);
                    $stmt1->execute();

                    $stmt1->close();
                }

                $st->close();

            }else{
                $sql="UPDATE `db_pages` SET `page_status`= ? WHERE page_id= ?";
                $stmt=$db->prepare($sql);
                $stmt->bind_param('ii',$status_removed,$page_id);
            }
            
            $result=$stmt->execute();
            $stmt->close();


            if(!$result) $err="Something went wrong";

        } else $err="No page with this id was found!";
    } else {
        $err="Oops!";
    }
    
    if($err)    echo $err; 
    else        echo "200:Deleted succesfully!";    

} else {
    # error msg
    echo 'Not logged in!';
}
?>
