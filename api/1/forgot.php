<?php

include "/var/www/dbinc.php";
include "/var/www/functions.php";

$user = addslashes($_POST['username']);
if(!$user) { $user = addslashes($_GET['username']); }

$uid = getUser($user);

if($uid) {

    $code = randString(5) . "-" . randString(10) . "-" .randString(5) . "-" .randString(3);
    $now = time();
    
    $res = mysql_query("INSERT INTO forgot_pass VALUES(DEFAULT, $uid, $now, '$code', 0)");
    
}

print "{ \"return\": \"SUCCESS\" }";





?>
