<?php
$auth= new Auth($this->conf_array);
$user_role=$auth->loginCheck();
if($user_role){
    $content='';

    $start=0;
    $rpg=5;

    if($user_role=="admin"){    
        $sql="SELECT * FROM db_moderators WHERE 1 ORDER BY mod_id DESC LIMIT ?,?";
        $stmt=$this->db->prepare($sql);

        $stmt->bind_param('ii',$start,$rpg);

        $stmt->execute();
        $stmt=$stmt->get_result();


        $content.="<div class='table-responsive pt-5'> <h2>Users</h2> <table width='100%' id='dt_pag' class='w-auto table table-hover table-bordered'><tr class='header'>
                <th id='mod_id'>#</th>
                <th >Email</th>
                <th class='w-25 text-center'>...</th>
                <th >Status</th>
                <th >Role</th>
                <th >Added</th>
            </tr>";

        $cnt=0;
        while($row=$stmt->fetch_assoc()){
            $content.=
                '<tr '.
                    (strtolower($this->conf_array['statuses'][$row['mod_status']])=='removed'? "class='table-danger'":'').
                    (strtolower($this->conf_array['statuses'][$row['mod_status']])=='inactive'? "class='table-warning'":'').
                '>'.
                '<td><b>'.$row['mod_id'].'</b></td>'.
                '<td>'.$row['mod_email'].'</td>'.
                '<td class="text-center"> ... </td>'.
                '<td>'.$this->conf_array['statuses'][$row['mod_status']].'</td>'.
                '<td>'.$this->conf_array["roles"][$row['mod_role']].'</td>'.
                '<td>'.$row['mod_added'].'</td>'.
                #'<td><a href="edit/'.$this->conf_array['delim'].'/id/'.$row['mod_id'].'/">Edit</a></td>'. 
                #'<td>'.($row['page_custom']==0?'<a href="edit/'.$this->conf_array['delim'].'/id/'.$row['page_id'].'/">Edit</a>':'').'</td>'.               
            '</tr>';
            $cnt++;
        }

        $content.="</table>".($cnt==0?"<p class='text-bold text-center'>OOPS no results were found</p>":"");
        $content.="<a href='/".$this->conf_array['backend']."/users/'>View all</a>";
        $content.="</div><br><br><br><br>";
    }
    

    $sql_lim="SELECT * FROM db_content ".
        " WHERE 1 ORDER BY ".
        " content_id DESC LIMIT ?,?";
        #." ORDER BY db_content.content_id ";

    $ss=$this->db->prepare($sql_lim);
    $ss->bind_param('ii',$start,$rpg);
    $ss->execute();
    $result=$ss->get_result();


    $content.="<div class='table-responsive pt-5'> <h2>Content</h2> <table width='100%' class='w-auto table table-hover table-bordered'><tr class='header'>
            <th id='page_id'>#</th>
            <th>Url</th>
            <th class='w-25 text-center'>...</th>
            
            <th>Title</th>
            <th>Filename</th>
            <th>Language</th>
        </tr>";


    $cnt=0;
    while($row=$result->fetch_assoc()){
        $page_url=(strpos($row['page_catalog'],$this->conf_array['backend'])!==false||$row['page_catalog']==404?FULL_WEBSITE.'/'.$row['page_catalog']:FULL_WEBSITE.'/'.$row['lang_id'].'/'.$row['page_catalog']);

        $content.='<tr>'.
            '<td><b>'.$row['content_id'].'</b></td>'.
            '<td><a href="'.$page_url.'" target="_blank">'.$page_url.'/</a></td>'.
            '<td class="text-center">...</td>'.
            #'<td>'.$row['page_catalog'].'</td>'.
            '<td>'.$row['content_title'].'</td>'.
            '<td>'.$row['content_filename'].'</td>'.
            '<td>'.$row['lang_id'].'</td>'.
            '</tr>';
        $cnt++;
    }

    $ss->close();

    $content.="</table>";
    $content.=($cnt==0?"<p class='text-bold text-center'>OOPS no results were found</p>":"");
    $content.="<a href='/".$this->conf_array['backend']."/content/'>View all</a>";
    $content.="</div>";



    require_once($this->conf_array['be_template']['full']);
} else {
    # error msg
    header('location: /control/');
}

?>
