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

if($bbid) { doBBID($user, $bbid); } else { doPID($user, $pid); }




function doPID($user, $pid) {
$vote = "up";

$uid = getUser($user);

$res = mysql_query("SELECT uid, pid FROM hots WHERE uid=$uid AND pid=$pid");
$row = mysql_fetch_array($res);
$there = $row{'uid'};
if($there) {
    $vote = "down";
    $res = mysql_query("DELETE FROM hots WHERE uid=$uid AND pid=$pid");
    $new = "no";
} 

$res = mysql_query("SELECT hots, uid FROM posts WHERE pid=$pid");
$row = mysql_fetch_array($res);
$hots = $row{'hots'};
$puid = $row{'uid'};
if($vote == "up") {
   $hots = $hots + 1;
   $res = mysql_query("UPDATE users SET pounds = pounds + 1 WHERE uid=$puid");
} elseif ($vote == "down") {
   $hots = $hots - 1;
}

   
$res = mysql_query("UPDATE posts SET hots=$hots WHERE pid=$pid");
   
$din = time();

if($new!="no") {
    $res = mysql_query("INSERT INTO hots VALUES($uid, $pid, 0, $din)");
}

print "{ \"return\": \"SUCCESS\", \"hots\":$hots }";

}





function doBBID($user, $bbid) {

$vote = "up";

$uid = getUser($user);

$res = mysql_query("SELECT uid, bbid FROM hots_bb WHERE uid=$uid AND bbid=$bbid");
$row = mysql_fetch_array($res);
$there = $row{'uid'};
if($there) {
    $vote = "down";
    $res = mysql_query("DELETE FROM hots_bb WHERE uid=$uid AND bbid=$bbid");
    $new = "no";
} 

$res = mysql_query("SELECT hots, uid FROM griddle_bb WHERE bbid=$bbid");
$row = mysql_fetch_array($res);
$hots = $row{'hots'};
$puid = $row{'uid'};
if($vote == "up") {
   $hots = $hots + 1;
   $res = mysql_query("UPDATE users SET pounds = pounds + 1 WHERE uid=$puid");
} elseif ($vote == "down") {
   $hots = $hots - 1;
}

   
$res = mysql_query("UPDATE griddle_bb SET hots=$hots WHERE bbid=$bbid");
   
$din = time();

if($new!="no") {
    $res = mysql_query("INSERT INTO hots_bb VALUES($uid, $bbid, $din)");
}

print "{ \"return\": \"SUCCESS\", \"hots\":$hots }";

}



























?>
