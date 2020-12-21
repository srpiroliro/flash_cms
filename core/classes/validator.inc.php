<?php 

class Validator {
    public static function varAccept($name,$url_vars, $x=1) {
        $value=(isset($_REQUEST[$name]) ? $_REQUEST[$name] : (isset($url_vars[$name]) ? $url_vars[$name] : Null));


        if(isset($value) && $value!=''){
            if($x==1 && strlen($value)>1 ) {
                return Validator::varFilter($value);
            } else {
                return $value;
            }
        } else { 
            return Null;
        }

    }
    public static function varFilter($value) {
        $value=filter_var($value,FILTER_UNSAFE_RAW,FILTER_FLAG_ENCODE_LOW); 
        return trim($value);
    }

    
}

?>