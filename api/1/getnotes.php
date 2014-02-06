<?php

include "/var/www/dbinc.php";
include "/var/www/functions.php";

$user = $_POST['username'];
if(!$user) { $user = $_GET['username']; }

$pass = $_POST['password'];
if(!$pass) { $pass = $_GET['password']; }


$pass = addslashes($pass);
$user = addslashes($user);


$res = mysql_query("SELECT password FROM users WHERE username='$user'");
$pw_row = mysql_fetch_array($res);
$pw_crypt = $pw_row{'password'};

$attempt = md5($pass);
   
if($attempt == $pw_crypt) {
     $uid = getUser($user);
     $notes = gotNotes($uid);
     print "{ \"return\": \"$notes\" }";  
   } else {
     print "{ \"return\": \"ERROR\" }"; 
}


?>
