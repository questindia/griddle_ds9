<?php

include "/var/www/dbinc.php";
include "/var/www/functions.php";

$user = $_POST['username'];
if(!$user) { $user = $_GET['username']; }

$pass = $_POST['password'];
if(!$pass) { $pass = $_GET['password']; }


$pass = addslashes($pass);
$user = addslashes($user);
$apple_id = addslashes($_POST['apple_id']);
$android_id = addslashes($_POST['android_id']);

$res = mysql_query("SELECT password FROM users WHERE username='$user'");
$pw_row = mysql_fetch_array($res);
$pw_crypt = $pw_row{'password'};

$attempt = md5($pass);
   
if($attempt == $pw_crypt) {
     print "{ \"return\": \"SUCCESS\" }";  
   } else {
     print "{ \"return\": \"ERROR\" }"; 
}

if($attempt == $pw_crypt) {
     $uid = getUser($user);
     $res = mysql_query("SELECT uid FROM mobile_device");
     $there = mysql_num_rows($res);
     if($there) {
         if($apple_id) {
             $res = mysql_query("UPDATE mobile_device SET apple_id='$apple_id' WHERE uid=$uid");
         }
         if($android_id) {
             $res = mysql_query("UPDATE mobile_device SET android_id='$android_id' WHERE uid=$uid");
         }
     } else {
        $res = mysql_query("REPLACE INTO mobile_device VALUES($uid, '$apple_id', '$android_id')");
     }
}

$req_dump = print_r($_REQUEST, TRUE);
$fp = fopen('/tmp/user_auth_request.log', 'a');
fwrite($fp, $req_dump);
fclose($fp);


?>
