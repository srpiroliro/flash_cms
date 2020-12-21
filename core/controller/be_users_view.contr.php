<?php

$auth= new Auth($this->conf_array);
if($auth->loginCheck()=="admin"){
    require_once($this->conf_array['be_template']['top']);

    $query="SELECT * FROM db_moderators";
    

    require_once(ROOT.'core/content/en/be_users_search.view.php');



        # search page
    $name=      Validator::varAccept('name',$this->url_vars);
    $status=    Validator::varAccept('status',$this->url_vars);
    $role=      Validator::varAccept('role',$this->url_vars);
    $month=     Validator::varAccept('month',$this->url_vars);
    $year=      Validator::varAccept('year',$this->url_vars);

    $rpg=       Validator::varAccept('ppg',$this->url_vars);
    $start=     Validator::varAccept('start',$this->url_vars);


    if(!isset($rpg))        $rpg=10;
    if(!isset($start))      $start=0;

    if(isset($name))        $url1='name/'.$name;
    if(isset($status))      $url2='status/'.$status;
    if(isset($role))        $url3='role/'.$role;
    if(isset($month))       $url4='month/'.$month;
    if(isset($year))        $url5='year/'.$year;

    $sql_params=
    (isset($month)&&!empty($month)?" AND MONTH(mod_added)=".$month:"").
    (isset($year)&&!empty($year)?" AND YEAR(mod_added)=".$year:"").
    (isset($status)&&!empty($status)?" AND mod_status=".$status:"").
    (isset($role)&&!empty($role)?" AND mod_role=".$role:"").
    (isset($name)&&!empty($name)?" AND ( mod_name LIKE ? OR mod_email LIKE ? )":"");
    
    
    
    $url='/'.$this->conf_array['backend'].'/users/'.$this->conf_array['delim'].'/';
    for ($i=0; $i <= 5; $i++) { 
        $var='url'.$i;
        $url.=(isset($$var)?$$var.'/':'');
    }
    
    $total_sql="SELECT mod_id FROM db_moderators WHERE 1 ".$sql_params;
    $stmt1=$this->db->prepare($total_sql);


    if(isset($name)&&!empty($name)){
        $name="%$name%";
        $stmt1->bind_param('ss',$name,$name);
    }
    $stmt1->execute();
    $total_result=$stmt1->get_result();
    $total_result=$total_result->num_rows;
    $stmt1->close();


    $sql="SELECT * FROM db_moderators WHERE 1 ".$sql_params." LIMIT ?,?";
    $stmt=$this->db->prepare($sql);

    if(isset($name)&&!empty($name)){
        $name="%$name%";
        $stmt->bind_param('ssii',$name,$name,$start,$rpg);
    } else $stmt->bind_param('ii',$start,$rpg);

    $stmt->execute();
    $stmt=$stmt->get_result();


    $table="<div class='table-responsive pt-5'><table width='100%' id='dt_pag' class='w-auto table table-hover table-bordered'><tr class='header'>
            <th id='mod_id'>#</th>
            <th >Email</th>
            <th >Password</th>
            <th >Name</th>
            <th >Hash</th>
            <th >Status</th>
            <th >Role</th>
            <th >Added</th>
            
        </tr>";

    $cnt=0;
    while($row=$stmt->fetch_assoc()){
        $table.=
            '<tr '.
                (strtolower($this->conf_array['statuses'][$row['mod_status']])=='removed'? "class='table-danger'":'').
                (strtolower($this->conf_array['statuses'][$row['mod_status']])=='inactive'? "class='table-warning'":'').
            '>'.
            '<td><b>'.$row['mod_id'].'</b></td>'.
            '<td>'.$row['mod_email'].'</td>'.
            '<td style="text-align:center;"> ... </td>'.
            '<td>'.$row['mod_name'].'</td>'.
            '<td style="text-align:center;"> ... </td>'.
            #'<td>'.$row['page_date'].'</td>'.
            '<td>'.$this->conf_array['statuses'][$row['mod_status']].'</td>'.
            '<td>'.$this->conf_array["roles"][$row['mod_role']].'</td>'.
            '<td>'.$row['mod_added'].'</td>'.
            '<td><a href="edit/'.$this->conf_array['delim'].'/id/'.$row['mod_id'].'/">Edit</a></td>'. 
            #'<td>'.($row['page_custom']==0?'<a href="edit/'.$this->conf_array['delim'].'/id/'.$row['page_id'].'/">Edit</a>':'').'</td>'.               
        '</tr>';
        $cnt++;
    }

    $table.="</table>".($cnt==0?"<p class='text-bold text-center'>OOPS no results were found</p>":"")."</div>";

    echo $table;


    $data['start']=$start;
    $data['total']=$total_result;
    $data['rpg']=$rpg;
    $data['url']=$url;

    echo Content::contentPagination($data);


    require_once($this->conf_array['be_template']['bottom']);
} else {
    # error msg
    header('location: /control/');
}

?>
