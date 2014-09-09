<?php

include "/var/www/dbinc.php";
include "/var/www/functions.php";

define('UPLOAD_DIR', '/var/www/griddle_profiles/');
define('THUMB_DIR', '/var/www/thumb_profiles/');

$user   = addslashes($_POST['username']);
$pass   = addslashes($_POST['password']);
$target = addslashes($_POST['target']);
$action = addslashes($_POST['action']);

if(!$user || !$pass || !$target || !$action) {
   print "{ \"return\": \"ERROR\", \"details\": \"Must provide all fields\" }";
   exit;
}

if(apiAuth($user, $pass) < 1) { 
     print "{ \"return\": \"ERROR\" }";
     exit;
}

$uid = getUser($user);
$r   = time();

file_put_contents("/tmp/frmanage.log", "Recieved Stuff - $user pass $target $action $uid\n", FILE_APPEND);

if($action == "friend")  {


   $res = mysql_query("SELECT rid, friend, follower FROM relations WHERE uid=$uid AND target=$target");
   $row = mysql_fetch_array($res);
   $rid = $row{'rid'};

   if(!$rid) { // No relation existed 
      
      $res = mysql_query("INSERT INTO relations VALUES(DEFAULT, $uid, 1, 0, $target, 1, $r)");
   } else {
      $res = mysql_query("UPDATE relations SET friend=1 WHERE uid=$uid AND target=$target");
   }


   $res = mysql_query("INSERT INTO notes_bb VALUES(DEFAULT, $target, 0, 0, $uid, 1, $r, '$user has sent you a friend request.', 1)");

}

if($action == "follow")  {

   $res = mysql_query("SELECT rid, friend, follower FROM relations WHERE uid=$uid AND target=$target");
   $row = mysql_fetch_array($res);
   $rid = $row{'rid'};

   if(!$rid) { // No relation existed 

      
      $res = mysql_query("INSERT INTO relations VALUES(DEFAULT, $uid, 0, 1, $target, 1, $r)");
   } else {
      $res = mysql_query("UPDATE relations SET follower=1 WHERE uid=$uid AND target=$target");
   }
   $tfol++;
   $res = mysql_query("UPDATE users SET followers=(followers + 1) WHERE uid=$target");

}
 
if($action == "unfollow")  { 
  $res = mysql_query("UPDATE relations SET follower=0 WHERE uid=$uid AND target=$target");
  $tfol--;
  $res = mysql_query("UPDATE users SET followers=(followers-1) WHERE uid=$target");
}

if($action == "unfriend") {
  $res = mysql_query("UPDATE relations SET friend=0 WHERE uid=$uid AND target=$target");
  $res = mysql_query("UPDATE relations SET friend=0 WHERE uid=$target AND target=$uid");
}

if($action == "unrequest") {
  $res = mysql_query("DELETE FROM relations WHERE uid=$uid AND target=$target");
  $res = mysql_query("DELETE FROM relations WHERE uid=$target AND target=$uid");
  $res = mysql_query("DELETE FROM notes_bb WHERE uid=$target AND req=$uid AND type=1");
  $res = mysql_query("DELETE FROM notes_bb WHERE uid=$uid AND target=$target AND type=2");
}

print "{ \"return\": \"SUCCESS\" }";
