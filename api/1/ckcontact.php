<?php

include "/var/www/dbinc.php";
include "/var/www/functions.php";

$mobile = addslashes($_POST['mobile']);
$email  = addslashes($_POST['email']);

if($mobile) {
   $WHEREM = " mobile='$mobile' ";
}

if($email) {
   $WHEREE = " email='$email' ";
}

if($mobile && $email) {
   $WHERE = "$WHEREM OR $WHEREE";
} else {
   $WHERE = "$WHEREM $WHEREE";
}



$res = mysql_query("SELECT uid, name, username FROM users WHERE $WHERE");
$row = mysql_fetch_array($res);
$uid = $row{'uid'};

if($uid) {
     print "{ \"return\": \"MATCH\" }";
} else {
     print "{ \"return\": \"NOMATCH\" }";
}

?>
