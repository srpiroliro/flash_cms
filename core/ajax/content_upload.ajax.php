<?php

require_once($_SERVER['DOCUMENT_ROOT']."/core/conf/conf.inc.php");
$db=$conf_array['db'];


$token=                                                                     Validator::varAccept('token',[]);

if(Auth::verify_formToken($token)){
    $err_msg=                                                               [];
    
    $url_valid=                                                             False;
    $lang_valid=                                                            False;
    $page_catalog_exists=                                                   True;
    
    $content_id=                                                            Validator::varAccept('id',[]);
    $action=                                                                Validator::varAccept('action',[]);

    $content_title=                                                         Validator::varAccept('content_title',[]);
    $lang_id=                                                               Validator::varAccept('lang_id',[]);
    $url=                                                                   Validator::varAccept('content_catalog',[]);
    $body=                                                                  Validator::varAccept('content_body',[]);
    $status=                                                                Validator::varAccept('page_status',[]);
    $custom=                                                                Validator::varAccept('page_custom',[]);

    $custom=                                                                (strtolower(strval($custom))=="true"?'1':'0');


    if(!isset($content_title)&&empty($content_title))                       $err_msg[].="Missing title";

    if(!isset($status)&&empty($status)&&$status!==0)                        $err_msg[].="Missing status!";
    elseif(!isset($conf_array['statuses'][$status]))                        $err_msg[].="Invalid status!";
    
    if(!in_array($lang_id, $conf_array['multilang']))                       $err_msg[].="Wrong language";
    else                                                                    $lang_valid=True;
    
    if(!isset($url)&&empty($url))                                           $err_msg[].="Missing url";
    
    
    else {
        $content_filename=                                                  ($custom==0?"cms_":"").str_replace('/','_',Tools::urlCleaner(trim($url, '/')));
        
        if($custom=='0')                                                    $content_path=ROOT.'core/content/'.$lang_id.'/'.$content_filename.'.view.php';
        else                                                                $content_path=ROOT.'core/controller/'.$content_filename.'.contr.php';
        


        if($lang_valid && file_exists($content_path) && $action=="add")     $err_msg[]="Content already exists";

        $query_pages="SELECT page_id FROM `db_pages` WHERE page_catalog= ? LIMIT 1";
        $stmt=$db->prepare($query_pages);
        $stmt->bind_param('s',$url);
        $stmt->execute();
        $results=$stmt->get_result();
        if($results->num_rows==0){
            $page_catalog_exists=False;
        }
        $stmt->close();

        if($lang_valid && $action=="add"){
            $url=Tools::urlCleaner($url);
            $sql="SELECT content_id FROM db_content WHERE page_catalog= ? AND lang_id= ?";
            $stmt=$db->prepare($sql);
            $stmt->bind_param('ss',$lang_id,$url);
            $stmt->execute();
            $results=$stmt->get_result();
            if($results->num_rows>0){
                                                                            $err_msg[].="Url already taken";
            }
            $stmt->close();
        }
    }
    if(!$body&&!$custom)                                                    $err_msg[].="Missing body";
    elseif($custom)                                                         $body=False;
    
    
    
    if(!$err_msg){
        if(!$page_catalog_exists){
            $query_pages="INSERT INTO `db_pages`(page_catalog, page_status ,page_custom) VALUES (?,?,?)";
            $stmt=$db->prepare($query_pages);
            $stmt->bind_param('sss',$url,$status,$custom);
            $stmt->execute();

            $stmt->close();
        } else {
            $sql="UPDATE `db_pages` SET page_status= ?, page_custom= ? WHERE page_catalog= ?";
            $stmt=$db->prepare($sql);
            $stmt->bind_param('sss',$status,$custom,$url);
            $stmt->execute();
            $stmt->close();
        }

        if($action=="add"){
            $query_pages="SELECT page_id FROM `db_pages` WHERE page_catalog= ?";
            $stmt=$db->prepare($query_pages);
            $stmt->bind_param('s',$url);
            $stmt->execute();
            
            $results=$stmt->get_result();
            $results=$results->fetch_assoc();
            $page_id=$results['page_id'];

            $stmt->close();
        }

        
        if($body and !$custom){
            $body=Tools::cleanBody($body);
            $clean_body=strtolower(strval(trim(strip_tags($body))));

            if($body and !empty($clean_body)){
                if($action=="edit"){
                    $sql="SELECT content_filename, lang_id FROM db_content WHERE content_id= ?";
                    $stmt=$db->prepare($sql);
                    $stmt->bind_param('i',$content_id);
                    $stmt->execute();
                    $result=$stmt->get_result();
                    $result=$result->fetch_assoc();
                    
                    $old_filename=trim($result['content_filename'], '/');
                    $old_lang=$result['lang_id'];
                    
                    if(trim($content_filename, '/')!=$old_filename OR $old_lang!=$lang_id and ($clean_body!='404' or $clean_body!='403')){
                        $old_filepath=($custom=='0'?ROOT.'core/content/'.$old_lang.'/'.$old_filename.'.view.php':ROOT.'core/controller/'.$old_filename.'.contr.php');
                        $add="$old_filename - $old_lang<br>$content_filename - $lang_id<br>$old_filepath - $content_path";
                        rename($old_filepath,$content_path);
                    }
                    
                    $stmt->close();
                }
                
                file_put_contents($content_path,$body);
            }
        }


        $query=($action=="add"?"INSERT INTO `db_content` (`page_id`, `content_filename`, `content_title`, `page_catalog`, `lang_id`) VALUES (?,?,?,?,?)":"UPDATE `db_content` SET `content_filename`= ? , `content_title`= ? , `page_catalog`= ? , `lang_id`= ?  WHERE `content_id`= ? ");
        $stmt=$db->prepare($query);
        if($action=="edit")     $stmt->bind_param('ssssi',$content_filename,$content_title,$url,$lang_id,$content_id);
        else                    $stmt->bind_param('issss',$page_id,$content_filename,$content_title,$url,$lang_id);
      
        $stmt->execute();
        //echo $stmt->error;
        $stmt->close();

	if($action=="add"){
	    $query="SELECT content_id FROM db_content WHERE page_catalog=? and lang_id=? and page_id=?";
	    $stmt=$db->prepare($query);
            $stmt->bind_param('ssi',$url,$lang_id,$page_id);
	    $stmt->execute();
	    //echo $stmt->error;
	    $res=$stmt->get_result();
	    $res=$res->fetch_assoc();
            $content_id=$res['content_id'];
            $stmt->close();

	    echo "redirect_to:/control/content/edit/rs/id/$content_id/; ";
	}

        echo "200:Page uploaded!";
    } else {
        $err='';
        foreach ($err_msg as $value) {
            $err.="$value<br>";
        }

        echo $err;
    }
} else echo "Something went wrong."; 

?>
