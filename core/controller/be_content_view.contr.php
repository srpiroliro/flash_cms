<?php

$auth= new Auth($this->conf_array);
$user_role=$auth->loginCheck();
if($user_role){
    require_once($this->conf_array['be_template']['top']);
    require_once(ROOT.'core/content/en/be_content_search.view.php');



    $keyword=                                       Validator::varAccept('keyword',$this->url_vars);
    $status=                                        Validator::varAccept('status',$this->url_vars);
    $custom=                                        Validator::varAccept('page_custom',$this->url_vars);
    $month=                                         Validator::varAccept('page_month',$this->url_vars);
    $year=                                          Validator::varAccept('page_year',$this->url_vars);

    $rpg=                                           Validator::varAccept('ppg',$this->url_vars);
    $start=                                         Validator::varAccept('start',$this->url_vars);

    #if(Validator::varAccept('check',$this->url_vars)=='ok'){
        # search page


    if(!isset($rpg))                                $rpg=10;
    if(!isset($start))                              $start=0; 
    
    if(isset($keyword) && $keyword!='Keyword')      $url1='keyword/'.urlencode($keyword); 
    if(isset($status))                              $url2='status/'.$status;
    if(isset($custom))                              $url3='page_custom/'.$custom; 
    if(isset($month))                               $url4='page_month/'.$month;
    if(isset($year))                                $url5='page_year/'.$year;


    $sql_params=
    ($user_role!="admin"?                           " AND db_pages.page_status!=2":"").
    (isset($month)&&!empty($month)?                 " AND MONTH(db_pages.page_date)=".$month:"").
    (isset($year)&&!empty($year)?                   " AND YEAR(db_pages.page_date)=".$year:"").
    (isset($status)&&!empty($status)?               " AND db_pages.page_status=".$status:"").
    (isset($custom)&&!empty($custom)?               " AND db_pages.page_custom=".$custom:"").
    (isset($keyword)&&!empty($keyword)&&$keyword?   " AND ( db_pages.page_catalog LIKE ? OR db_content.page_catalog LIKE ? OR db_content.content_title LIKE ? ) ":" ");


    $total_sql="SELECT db_content.content_id FROM db_pages RIGHT JOIN db_content ON db_pages.page_id=db_content.page_id ".
    " WHERE 1 ".$sql_params;
    
    $stmt1=$this->db->prepare($total_sql);
    if(isset($keyword)&&!empty($keyword)&&$keyword){
        $keyword="%$keyword%";
        $stmt1->bind_param('sss',$keyword,$keyword,$keyword);
    }
    $stmt1->execute();
    $total_result=$stmt1->get_result();
    $total_result=$total_result->num_rows;

    $stmt1->close();

    
    $url='/'.$this->conf_array['backend'].'/content/'.$this->conf_array['delim'].'/';
    for ($i=0; $i <= 5; $i++) { 
        $var='url'.$i;
        $url.=(isset($$var)?$$var.'/':'');
    }


    $sql_lim="SELECT * FROM db_pages RIGHT JOIN db_content ON db_pages.page_id=db_content.page_id ".
        " WHERE 1 ".$sql_params." ORDER BY ".
        " db_content.content_id DESC LIMIT ?,?";
        #." ORDER BY db_content.content_id ";

    $ss=$this->db->prepare($sql_lim);

    if(isset($keyword)&&!empty($keyword)&&$keyword) $ss->bind_param('sssii',$keyword,$keyword,$keyword,$start,$rpg);
    else $ss->bind_param('ii',$start,$rpg);

    $ss->execute();
    $result=$ss->get_result();


    $table="<div class='table-responsive pt-5'><table width='100%' id='dt_pag' class='w-auto table table-hover table-bordered'><tr class='header'>
            <th id='page_id'>#</th>
            <th>Url</th>
            <th>Catalog</th>
            <th>Title</th>
            <th>Filename</th>
            <th>Language</th>
             
            <th>Uploaded</th>
            <th>Custom</th>
            <th>Status</th>
        </tr>";


    $cnt=0;
    while($row=$result->fetch_assoc()){


        $page_url=(strpos($row['page_catalog'],$this->conf_array['backend'])!==false||$row['page_catalog']==404?FULL_WEBSITE.'/'.$row['page_catalog']:FULL_WEBSITE.'/'.$row['lang_id'].'/'.$row['page_catalog']);
        
        $table.='<tr '.
            (strtolower($this->conf_array['statuses'][$row['page_status']])=='removed'? "class='table-danger'":'').
            (strtolower($this->conf_array['statuses'][$row['page_status']])=='inactive'? "class='table-warning'":'').
            '>'.
            '<td><b>'.$row['content_id'].'</b></td>'.
            '<td><a href="'.$page_url.'" target="_blank">'.$page_url.'/</a></td>'.
            '<td>'.$row['page_catalog'].'</td>'.
            '<td>'.$row['content_title'].'</td>'.
            '<td>'.$row['content_filename'].'</td>'.
            '<td>'.$row['lang_id'].'</td>'.
            #'<td>'.$row['page_date'].'</td>'.
            '<td>'.$row['page_uploaded'].'</td>'.
            '<td>'.$row['page_custom'].'</td>'.
            '<td>'.$this->conf_array['statuses'][$row['page_status']].'</td>'.
            '<td><a'.($row['page_status']==2||($row['page_custom']=='1' && $user_role!="admin")?" class='disabled' ":" ").'href="/' .$this->conf_array['backend'].'/content/edit/'.$this->conf_array['delim'].'/id/'.$row['content_id'].'/">Edit</a></td>'. 
            #'<td>'.($row['page_custom']==0?'<a href="edit/'.$this->conf_array['delim'].'/id/'.$row['page_id'].'/">Edit</a>':'').'</td>'.               
        '</tr>';
        $cnt++;
    }

    $ss->close();

    $table.="</table>".($cnt==0?"<p class='text-bold text-center'>OOPS no results were found</p>":"")."</div>";

    echo $table;



    $data['start']=$start;
    $data['total']=$total_result;
    $data['rpg']=$rpg;
    $data['url']=$url;

    echo Content::contentPagination($data);
    #} else {
    #    echo "<p class='mt-4'>Submit a search to get content. By just submitting it empty you will get all the results.</p>";
    #}

    require_once($this->conf_array['be_template']['bottom']);

} else {
    # error msg
    header('location: /control/');
}
?>
