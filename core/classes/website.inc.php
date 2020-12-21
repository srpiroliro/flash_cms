<?php

class Website {
    private $conf_array;
    private $lang_id;
    private $page_catalog;
    private $url_vars;

    private $db;
    private $page_id;
    private $page_template;
    private $content_title;

    private $filename;
    private $filepath;

    public $body;
    public $action;
    

    
    public function __construct($conf_array, $language, $catalog, $url_vars){
        $this->conf_array=$conf_array;
        $this->db=$this->conf_array['db'];
        $this->lang_id=(isset($language) ? $language : $conf_array['default_lang']);
        $this->page_catalog=$catalog;
        $this->url_vars=(isset($url_vars) ? $url_vars : False);

        $this->body='';
	$this->test="qwe";
    }

    public function get_content(){
        $query="
            SELECT db_pages.page_id, db_pages.page_custom, db_content.content_filename, db_content.content_title FROM db_pages  
            RIGHT JOIN db_content 
            ON db_pages.page_id=db_content.page_id  
            WHERE ((db_pages.page_catalog= ? OR db_content.page_catalog= ? ) AND db_content.lang_id= ? ) 
            ORDER BY db_pages.page_id LIMIT 1
        ";
        $stmt=$this->db->prepare($query);
        $stmt->bind_param('sss', $this->page_catalog, $this->page_catalog,$this->lang_id);
        $stmt->execute();

        $result=$stmt->get_result();
        $result=$result->fetch_assoc();
        
        $filename=$result['content_filename'];
        
        $this->page_id=$result['page_id'];
        $this->content_title=$result['content_title'];
        $this->page_custom=$result['page_custom'];
        $this->filename=$filename;

        $path=( $this->page_custom==0 ? ROOT.'core/content/'.$this->lang_id.'/'.$filename.'.view.php' : ROOT.'core/controller/'.$filename.'.contr.php');
        $this->filepath=$path;

        if (file_exists($path)){
            if ($this->page_custom==0) $this->body=file_get_contents($path);
        } else {
            header('location:http://'.WEBSITE.'/404/');
        }
    }

    public function display(){
        $title=$this->content_title;
        $content=$this->body;

        if($this->page_custom==0){
            # default template
            # just include the template and echo the content

            if(preg_match('/'.$this->conf_array['backend'].'/', $this->page_catalog)){
                # backend template
                $template=$this->conf_array['be_template']['full'];
                
            } else {
                # frontend template
                $template=$this->conf_array['fe_template'];
            }


            require_once($template);
            
        } else {
            # custom template
            # page_custom = 1

            require_once($this->filepath);

            # just include the file
        }
    }

    public static function gotoUrl($url,$data) {
        $output='<div>';

        if(isset($data['animation'])) $output.='<img src="'.$data['animation'].'" border="0" /><br />';

        $output.=(isset($data['message'])?$data['message'].'</div>':'</div>');
        $output.="<script language=\"JavaScript\">setTimeout('window.location=\"".$url."\";', ".(isset($data['delay_time'])?$data['delay_time']:'1500').");</script>";
     

        return $output;
    }
}
?>
