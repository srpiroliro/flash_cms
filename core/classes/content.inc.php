<?php

class Content {
    public static function contentPagination($data){
        $output='';
        $nav_left='';
        $nav_right='';
        $nav_center='';

        $start=$data['start'];
        $total=$data['total'];
        $rpg=$data['rpg'];
        $url=$data['url'];


        $links_limit=$start+$rpg*10;
        $proportion=$total/$rpg;

        if($proportion>0){
            if($start>0){
                $previous=$start-$rpg;
                $nav_left='<a href="'.$url.'start/'.$previous.'/">&larr;</a>&nbsp;&nbsp;';
            }

            $old_start=$start;
            $start+=$rpg;

            if($total>$start){
                $nav_right='&nbsp;&nbsp;<a href="'.$url.'start/'.$start.'/">&rarr;</a>';
            }

            if($total>$rpg){
                $page=1;

                for($times=0; $times<=$proportion; $times++){ 
                    $current=$times*$rpg;
                    if($total>$current&&$links_limit>$current){
                        if($old_start==$current){
                            $nav_center.='<a href="'.$url.'start'.'/'.$current.'/"><strong>['.$page.']</strong></a>';
                        }elseif($current>$old_start){
                            $nav_center.='&nbsp;&nbsp;<a href="'.$url.'start'.'/'.$current.'/">'.$page.'</a>';
                        }
                    }

                    $page++;
                }
            }
        }

        if($total>$rpg){
            $output="<div class='content-pagination'>$nav_left$nav_center$nav_right</div>";
        }
        
        return $output;
    }
    public static function recent_list($db,$lang_id){
        $sql='select page_catalog,content_title from db_content where content_filename like ? and lang_id=?';
        $val="cms_%";
        $stmt=$db->prepare($sql);
        $stmt->bind_param('ss', $val,$lang_id);
        $stmt->execute();
        $res=$stmt->get_result();

        $recent="<div class='recent'><h2>Recent</h2><ul>";
        while($row=$res->fetch_assoc()){
            $recent.="<li><a href='/$lang_id/$row[page_catalog]'>$row[content_title]</a></li>";
        }
        $recent.="</ul>";

        return $recent;
    }
}

?>
