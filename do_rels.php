<?php
include "dbinc.php";

$r = time();
$r2 = substr($r, 0, 8); 
$targetSEP = $_GET['target'];
$action = $_GET['action'];

$tline = explode("-", $targetSEP);

$target = $tline[0];



$user = $_SESSION['user'];

if(!$user) { // bail
   exit;
}


include "functions.php";
$res = mysql_query("SELECT uid FROM users WHERE username='$user'");
$row = mysql_fetch_array($res);
$uid = $row{'uid'};

$res = mysql_query("SELECT name, username, posts, followers FROM users WHERE uid=$target");
$row = mysql_fetch_array($res);
$tuser = $row{'username'};
$tpos = $row{'posts'};
$tfol = $row{'followers'};
$tnam = $row{'name'};

if($action == "friend")  {


   $res = mysql_query("SELECT rid, friend, follower FROM relations WHERE uid=$uid AND target=$target");
   $row = mysql_fetch_array($res);
   $rid = $row{'rid'};

   if(!$rid) { // No relation existed 
      $res = mysql_query("SELECT rid FROM relations ORDER BY rid DESC LIMIT 1");
      $row = mysql_fetch_array($res);
      $rid = $row{'rid'} + 1;
      
      $res = mysql_query("INSERT INTO relations VALUES($rid, $uid, 1, 0, $target, 1, $r)");
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
      $res = mysql_query("SELECT rid FROM relations ORDER BY rid DESC LIMIT 1");
      $row = mysql_fetch_array($res);
      $rid = $row{'rid'} + 1;
      
      $res = mysql_query("INSERT INTO relations VALUES($rid, $uid, 0, 1, $target, 1, $r)");
   } else {
      $res = mysql_query("UPDATE relations SET follower=1 WHERE uid=$uid AND target=$target");
   }
   $tfol++;
   $res = mysql_query("UPDATE users SET followers=$tfol WHERE uid=$target");

}
 
if($action == "unfollow")  { 
  $res = mysql_query("UPDATE relations SET follower=0 WHERE uid=$uid AND target=$target");
  $tfol--;
  $res = mysql_query("UPDATE users SET followers=$tfol WHERE uid=$target");
}

if($action == "unfriend") {
  $res = mysql_query("UPDATE relations SET friend=0 WHERE uid=$uid AND target=$target");
  $res = mysql_query("UPDATE relations SET friend=0 WHERE uid=$target AND target=$uid");
}



$res = mysql_query("SELECT rid, friend FROM relations WHERE uid=$uid AND target=$target");
$row = mysql_fetch_array($res);
$rid = $row{'rid'};
$friend = $row{'friend'};
   
if($friend == 0) {
  $fLine = "<a role='menuitem' tabindex='-1' class=relLink href=/do_rels.php?action=friend&target=$target><span class='glyphicon glyphicon-plus'></span> Add Friend</a>";
} elseif ($friend == 1) {
  $fLine = "<a role='menuitem' tabindex='-1' class=relLink href=/do_rels.php?action=unfriend&target=$target><span class='glyphicon glyphicon-remove'></span> Friends Pending</a>";
} elseif ($friend == 2) {
  $fLine = "<a role='menuitem' tabindex='-1' class=relLink href=/do_rels.php?action=unfriend&target=$target><span class='glyphicon glyphicon-remove'></span> Remove Friend</a>";
} else {
  $fLine = "<a role='menuitem' tabindex='-1' class=relLink href=/do_rels.php?action=friend&target=$target><span class='glyphicon glyphicon-plus'></span> Add Friend</a>";
}

if($target == $uid) {
     $fLine = "";
     $foLine = "";
}


if($MOBILE) {
     $MOBILE_BREAK = "</tr><tr>";
}

$content = $fLine;


print "{ \"target\":\"$targetSEP\", \"content\":\"$content\", \"head\":\"$header\" }";
