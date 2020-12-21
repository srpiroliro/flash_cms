<?php
class Tools {
    public static function urlCleaner($url){
        $tmp=str_replace(" ","-",strtolower(trim($url)));
        return $tmp;
    }

    public static function cleanBody($html){

        $doc = new DOMDocument();
        $doc->loadHTML($html);
        $script_tags = $doc->getElementsByTagName('script');

        $length = $script_tags->length;

        for ($i = 0; $i < $length; $i++) {
            $script_tags->item($i)->parentNode->removeChild($script_tags->item($i));
        }

        return preg_replace('/^<!DOCTYPE.+?>/', '', str_replace( array('<html>', '</html>', '<body>', '</body>', '<p>&nbsp;</p>'), array('', '', '', '', ''), $doc->saveHTML()));
    }
}
?>
