<?php

session_destroy();
Auth::clearAuthCookies();

header('Location: /?logged_out');
die();


?>