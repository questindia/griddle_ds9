<?php

include "/var/www/dbinc.php";
include "/var/www/functions.php";

$user = addslashes($_POST['username']);
$pass = addslashes($_POST['password']);
$bbid = addslashes($_POST['bbid']);
$pid  = addslashes($_POST['pid']);

if(apiAuth($user, $pass) < 1) { 
     print "{ \"return\": \"ERROR\", \"details\": \"Invalid Username or Password.\" }";
     exit;
}

if($bbid) { $comms = getCommentsForBBID($user, $bbid, "9999"); } else { $comms = getCommentsForPID($user, $pid, "9999"); }

print "{ \"return\": \"SUCCESS\", \"comms\": [ $comms ] }";


?>
