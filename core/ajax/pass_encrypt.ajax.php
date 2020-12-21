
<?php

    if(strpos($_POST['ajax_pass'], '$')===False){
        echo password_hash($_POST['ajax_pass'], PASSWORD_DEFAULT);
    } else echo 'error';
?>