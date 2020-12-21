<?php

class Router {

    private $uri;
    private $url;
    private $delim;
    private $site_conf;
    private $db;

    public $catalog;
    public $url_vars;
    public $language;


    public function __construct($conf_array){
        $this->site_conf=$conf_array;  
        $this->delim=$this->site_conf['delim'];
        $this->url='http://'.WEBSITE.$_SERVER['REQUEST_URI'];
        $this->db=$this->site_conf['db'];
    }

    private function processUri(){
        $url_data=parse_url($this->url);
        $this->uri=$url_data['path'];

        $this->fetchVariables();
    }

    private function fetchVariables(){
        if(strpos($this->uri,'/'.$this->delim.'/')){
            $container=explode('/'.$this->delim.'/', $this->uri);
            $catalog=$container[0]; # clean uri without any variables.
            
            if(strlen($container[1])>1){
                $vv=explode('/',trim($container[1],'/')); # variables and values
                $lim=count($vv);
                if($lim>0){
                    # var1 value var2 value2 var3 patata
                    for($i=0;$i<$lim;$i+=2){
                        $data[strval($vv[$i])]=$vv[$i+1];
                    }

                    $this->url_vars=$data;
                }
            }

            $this->uri=$catalog;
            

        } else $this->url_vars='';
    }

    public function processUrl(){
        $this->processUri();
        
        if(isset($this->uri) AND $this->uri!='/'){ 
            $trimmed_uri=trim($this->uri,'/');
            $exploded_uri=explode('/', $trimmed_uri);

            if(count($this->site_conf['multilang'])>0){

                if($exploded_uri[0]==$this->site_conf['backend']){
                    $this->catalog=$trimmed_uri;
                    $this->language=$this->site_conf['default_lang']; 
                } else {
                    $data=explode('/',$trimmed_uri,2);

                    if (count($data)>=2){
                        $this->language=$data[0];
                        $this->catalog=$data[1];

                        $error_check=1;
                    }else{
                        $lang_found=0;
                        foreach ($this->site_conf['multilang'] as $lang) {
                            if($data[0]==$lang){
                                $lang_found=1;
                                break;
                            }
                        }

                        if($lang_found AND !isset($data[1])){
                            $this->language=$data[0];
                            $this->catalog="home";
                        }
                    }
                }
            
                if(!in_array($this->language, $this->site_conf['multilang'])){
                    $error_check=0;
                    $this->language=$this->site_conf['default_lang'];
                } else $error_check=1;



                if($error_check>0){

                    $query="SELECT db_pages.page_id FROM db_pages ".
                    "RIGHT JOIN db_content ". 
                    "ON db_pages.page_id=db_content.page_id ".
                    "WHERE db_pages.page_status='1' AND ( db_pages.page_catalog= ? OR ".
                    "db_content.page_catalog= ? ) ".
                    "AND db_content.lang_id= ? LIMIT 1";
                    
                    
                    $result=$this->db->prepare($query);
                    $result->bind_param("sss", $this->catalog, $this->catalog, $this->language);
                    $result->execute();
                    $result->store_result();
                    
                    if($result->num_rows>0){
                        $error_check=1;
                    } else $error_check=0;
                }
                
            } else {
                # there shouldn't be a language in the url
                $query="SELECT page_id FROM db_pages WHERE ".
                "page_status='1' AND page_catalog=? LIMIT 1";
                
                $result=$this->db->prepare($query);
                $result->bind_param("s", $trimmed_uri);
                $result->execute();
                $result->store_result();

                if($result->num_rows>0){
                    $error_check=1;
                } else $error_check=0;
            }

            if($error_check!=1){
                $this->catalog='404';
                $this->language=$this->site_conf['default_lang'];
    
                $this->redirect404();
            }

        } else $this->catalog='home';


    }

    private function redirect404(){
        header('HTTP/1.1 404 Not Found');
    }
}

?>