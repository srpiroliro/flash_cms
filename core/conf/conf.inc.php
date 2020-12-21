<?php

session_start();

define('WEBSITE', '****');
define('FULL_WEBSITE', "http://".$_SERVER['SERVER_NAME']);
define('ROOT', $_SERVER['DOCUMENT_ROOT']."/");
define('CLASSES', '/core/classes/');
define('LOGS', '/core/logs/');

foreach (glob(ROOT.CLASSES.'*.php') as $value) {
    require_once($value);
}


$db=new mysqli('localhost','****','****','****','3308');

if ($db->connect_errno) {
    printf("Connect failed: %s\n", $mysqli->connect_error);
    exit();
} 

$conf_array=
[
    'rooturl'=>'http://'.WEBSITE,

    'backend'=>'control',
    'fe_template'=>ROOT.'core/media/templates/fe_template.inc.php',
    
    'be_template'=>[
        'full'=>ROOT.'core/media/templates/be_template.inc.php',
        'top'=>ROOT.'core/media/templates/be_template_top.inc.php',
        'bottom'=>ROOT.'core/media/templates/be_template_bottom.inc.php',
    ],

    'custom_ids'=>[
        '0'=>'Default Template',
        '1'=>'Custom'
    ],

    'delim'=>'rs',
    'default_lang'=>'en',
    'multilang'=>['es','cat','en'],

    'statuses'=>[
        '0'=>'Inactive',
        '1'=>'Active',
        '2'=>'Removed'
    ],
    
    'roles'=>[
        0=>'editor',
        1=>'admin'
    ],


    'msgs'=>[
        'auth_success'=>'Authentication succeeded. Access granted.',
        'auth_error'=>'Authentication denied. Check your username and password.',
    ],

    'db'=>$db
];

?>
