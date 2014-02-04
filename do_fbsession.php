<?php

$r = time();
include "dbinc.php";
include "functions.php";


if($_SESSION['user'] == '') {
   exit;
} 

$user = $_SESSION['user'];


require_once("facebook-php-sdk/src/facebook.php");

$config = array(
    'appId' => '436926926413614',
    'secret' => '37c380464160d4d0d950435e800d2422',
    'fileUpload' => true // optional
);

$facebook = new Facebook($config);

$uid = getUser($user);

$access_token = $facebook->getAccessToken();

$facebook->setExtendedAccessToken();

$access_token2 = $facebook->getAccessToken();

$fbuid = $facebook->getUser();

$res = mysql_query("REPLACE INTO fblink VALUES($uid, $fbuid, '$access_token', '$access_token2', 1, 0)");

?>
