<?php

include "/var/www/dbinc.php";
include "/var/www/functions.php";

$user = $_POST['username'];
if(!$user) { $user = $_GET['username']; }

$pass = $_POST['password'];
if(!$pass) { $pass = $_GET['password']; }


$pass = addslashes($pass);
$user = addslashes($user);
$targ = addslashes($_POST['target']);
$swap = addslashes($_POST['swap']);


if(apiAuth($user, $pass) < 1) { 
     print "{ \"return\": \"ERROR\" }";
     exit;
}

$uid = getUser($user);

if(!$swap) {
    $ttype="relations.target";
    $tswap="relations.uid";
} else {
    $ttype="relations.uid";
    $tswap="relations.target";
}

$res = mysql_query("SELECT relations.uid, users.name, users.username, users.mobile, users.email, relations.follower, relations.target FROM relations, users WHERE relations.follower=1 AND $ttype=$targ AND users.uid=$tswap ORDER BY users.name LIMIT 2000");

$JSON = "{ \"return\": \"SUCCESS\", \"followers\": [ ";

while($row = mysql_fetch_array($res)) {
   $u  = $row{'uid'};
   $n  = $row{'name'};
   $un = $row{'username'};
   $fl = isFollowing($uid, $u);
   if(!$fl) { $fl = "0"; }
   $tg = $row{'target'};
   
   
   $fi = getFacebookInfo($u);
   $fu = $fi{'fbuid'};


   $JSON .= "{ \"uid\": \"$u\",
               \"n\": \"$n\",
               \"un\": \"$un\",
               \"pimg\": \"http://www.griddle.com/thumb_profiles/$un\",
               \"fl\": \"$fl\",
               \"fu\": \"$fu\" },\n";
}

$JSON = rtrim($JSON, ",\n");

$JSON .= " ] }";



print "$JSON";
?>
