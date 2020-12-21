<?php

class Html {

    public static function selectList($data) {
		$ark=array_keys($data['data']);
		$data['excp']=(isset($data['excp'])?$data['excp']:"null");
		$data['excp1']=(isset($data['excp1'])?$data['excp1']:"null");
        $container='';

        switch($data['type']){     	
        	
			case 'key2value':
        	    foreach($ark as $k){
        	        if($data['excp']!==$k && $data['excp1']!==$k){ 
        	  	        $container.='<option value="'.$k.'">'.$data['data'][$k].'</option>';
                    }
                }
        	break;
        	
        	case 'value2value':
        	    foreach($data['data'] as $k){
        	 	    if($data['excp']!==$k){
        	            $container.='<option value="'.$k.'">'.$k.'</option>';
        	            }
                    }
        	break;  
        	
        	case 'key2id2value':
        	    foreach($ark as $k){
        	 	    if($data['excp']!=$k && $data['excp1']!=$k){
        	            $container.='<option value="'.$k.'">'.$data['data'][$k][$data['id']].'</option>';
        	        }
                }
        	break;  	
        	
        	  	
        	
        }
        
        return $container;
    }

}

?>