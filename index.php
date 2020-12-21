<?php
require_once("core/conf/conf.inc.php");

$router=new Router($conf_array);
$router->processUrl();

header('Content-Type: text/html; charset=utf-8');

$website=new Website($conf_array, $router->language, $router->catalog, $router->url_vars);
$website->get_content();
$website->display();

?>
