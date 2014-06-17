<?php

include "/var/www/dbinc.php";
include "/var/www/functions.php";

$user = addslashes($_POST['username']);
$pass = addslashes($_POST['password']);
$bbid = addslashes($_POST['bbid']);
$pid  = addslashes($_POST['pid']);
$comm = addslashes($_POST['comment']);

if(apiAuth($user, $pass) < 1) { 
     print "{ \"return\": \"ERROR\", \"details\": \"Invalid Username or Password.\" }";
     exit;
}

if(!$comm) { 
     print "{ \"return\": \"ERROR\", \"details\": \"Username, Password, BBID or PID, and Comment are required\" }";
     exit;
}



if($bbid) { $comms = addCommentToGriddle($user, $bbid, $comm); } else { $comms = addCommentToPost($user, $pid, $comm); }

print "{ \"return\": \"SUCCESS\", \"comms\":$comms }";


?>
