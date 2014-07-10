<?php

include "/var/www/dbinc.php";
include "/var/www/functions.php";

$user   = addslashes($_POST['username']);
$pass   = addslashes($_POST['password']);
$target = addslashes($_POST['target']);
$tnid   = addslashes($_POST['nid']);
$action = addslashes($_POST['action']);

if(!$user || !$pass || !$target || !$action || !$tnid) {
   print "{ \"return\": \"ERROR\", \"details\": \"Must provide all fields\" }";
   exit;
}

if(apiAuth($user, $pass) < 1) { 
     print "{ \"return\": \"ERROR\", \"details\": \"Username or Password incorrect\" }";
     exit;
}

$uid = getUser($user);
$r   = time();


if($action == "acceptfriend") {

   $res = mysql_query("UPDATE relations SET friend=2 WHERE uid=$target AND target=$uid");
   $res = mysql_query("UPDATE notes_bb SET status=2 WHERE nid=$tnid");

   $res = mysql_query("INSERT INTO notes_bb VALUES(DEFAULT, $target, 0, 0, $uid, 2, $r, '$user has acccepted your friend request!', 1)");

   $res = mysql_query("SELECT rid FROM relations WHERE uid=$uid and target=$target");
   $row = mysql_fetch_array($res);
   $rrid = $row{'rid'};
   if($rrid) {
       $res = mysql_query("UPDATE relations SET friend=2 WHERE uid=$uid and target=$target");
   } else {
    
       $res = mysql_query("INSERT INTO relations VALUES(DEFAULT, $uid, 2, 0, $target, 2, $r)");
   }
   $action = "";
}

if($action == "rejectfriend") {
   $res = mysql_query("UPDATE relations SET friend=1 WHERE uid=$target AND target=$uid");
   $res = mysql_query("UPDATE notes_bb SET status=2 WHERE nid=$tnid");
   $action = "";

}

if($action == "dismiss") {
   $res = mysql_query("UPDATE notes_bb SET status=2 WHERE nid=$tnid");
   $action = "";
}

print "{ \"return\": \"SUCCESS\" }";











?>
