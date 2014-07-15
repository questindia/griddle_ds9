<?php
   include "/var/www/dbinc.php";
   include "/var/www/functions.php";

require '/var/www/Predis/Autoloader.php';

Predis\Autoloader::register();

$redis = new Predis\Client('tcp://grd-mysql-01.griddle.com:6379');



$user    = addslashes($_POST['username']);
$pass    = addslashes($_POST['password']);
$sel     = addslashes($_POST['sel_grid']);
$typed   = addslashes($_POST['typed_grid']);
$message = addslashes($_POST['message']);
$MASTER  = addslashes($_POST['master']);
	
$pic1 = $_FILES["pic1"]["size"];
$pic2 = $_FILES["pic2"]["size"];
$pic3 = $_FILES["pic3"]["size"];
$pic4 = $_FILES["pic4"]["size"];
$pic5 = $_FILES["pic5"]["size"];
$pic6 = $_FILES["pic6"]["size"];
$pic7 = $_FILES["pic7"]["size"];
$pic8 = $_FILES["pic8"]["size"];
$pic9 = $_FILES["pic9"]["size"];
$pic10 = $_FILES["pic10"]["size"];
$pic11 = $_FILES["pic11"]["size"];
$pic12 = $_FILES["pic12"]["size"];
$pic13 = $_FILES["pic13"]["size"];
$pic14 = $_FILES["pic14"]["size"];
$pic15 = $_FILES["pic15"]["size"];
$pic16 = $_FILES["pic16"]["size"];
$pic17 = $_FILES["pic17"]["size"];
$pic18 = $_FILES["pic18"]["size"];
$pic19 = $_FILES["pic19"]["size"];
$pic20 = $_FILES["pic20"]["size"];

if($pic1 > 0) { $line  = " pic1"; }
if($pic2 > 0) { $line .= " pic2"; }
if($pic3 > 0) { $line .= " pic3"; }
if($pic4 > 0) { $line .= " pic4"; }
if($pic5 > 0) { $line .= " pic5"; }
if($pic6 > 0) { $line .= " pic6"; }
if($pic7 > 0) { $line .= " pic7"; }
if($pic8 > 0) { $line .= " pic8"; }
if($pic9 > 0) { $line .= " pic9"; }
if($pic10 > 0) { $line .= " pic10"; }
if($pic11 > 0) { $line .= " pic11"; }
if($pic12 > 0) { $line .= " pic12"; }
if($pic13 > 0) { $line .= " pic13"; }
if($pic14 > 0) { $line .= " pic14"; }
if($pic15 > 0) { $line .= " pic15"; }
if($pic16 > 0) { $line .= " pic16"; }
if($pic17 > 0) { $line .= " pic17"; }
if($pic18 > 0) { $line .= " pic18"; }
if($pic19 > 0) { $line .= " pic19"; }
if($pic20 > 0) { $line .= " pic20"; }

if($MASTER != "secret123") {

   $res = mysql_query("SELECT password FROM users WHERE username='$user'");
   $pw_row = mysql_fetch_array($res);
   $pw_crypt = $pw_row{'password'};

   $attempt = md5($pass);
   
   if($attempt != $pw_crypt) {
       print "{ \"return\": \"ERROR\" }";
       exit; 
   }

   $uid = getUser($user);

} else {
   $uid = $_POST['uid'];
   $ui = getName($uid);
   $user = $ui{'username'};
}


$type = 3;

define('UPLOAD_DIR', '/var/www/griddle_images/');
define('FULL_DIR', '/var/www/full_images/');
define('THUMB_DIR', '/var/www/thumb_images/');
define('MIDSIZE_DIR', '/var/www/mid_images/');

file_put_contents("/tmp/do_post.log", "$user - $line|$typed|$sel|$message\n");

if(!$line) {
    print "{ \"return\": \"ERROR\" }";
    exit;
}

if (strpos($sel, '#') !== false) { 
    $topic = $sel;
} else {
    $typed = preg_replace('/"/', '', $typed);
    $typed = preg_replace('/\'/', '', $typed);
	$typed = preg_replace('/\n/', '', $typed);
    $typed = preg_replace('/\r/', '', $typed);
	$typed = preg_replace('/ /', '', $typed);
	$typed = preg_replace('/#/', '', $typed);
    $typed = strtolower($typed);
	$typed = "#" . $typed;
	$topic = $typed;
}

if($topic == "#") {
   // This is blank, bail
   //print "{ \"return\": \"ERROR\" }";
   $topic = "#random";
}


$gid = do_griddle($topic, $uid, 3);
$gi = getGridInfo($gid);

if($gi{'uid'} == $uid) {
   $status = 1;
} elseif ($gi{'public'} == 1) {
   $status = 1;
}

if (($gi{'premoderate'} == 1) && ($gi{'uid'} != $uid)) {
    $status = 2;
}

if(($gi{'public'} == 1) || ($gi{'uid'} == $uid)) {     
   handle_post($uid, $gid, $message, $line, $status);
   if($status == 1) {
       updateLastPost($gid);
   }
} 

print "{ \"return\": \"SUCCESS\",
         \"hits\": \"3\",
         \"score\": \"500\" }";
   

function do_griddle($ftopic, $fuid, $ftype) {


 $utest = preg_replace("/[^A-Za-z0-9]/", '', $ftopic);
 $res = mysql_query("SELECT username FROM users WHERE username='$utest'");
 $uthere = mysql_num_rows($res);

 if($uthere) {
    // Logit and Bail for now
    //logit("Found that $utest already exists as a username - grid / username conflict");
   print "{ \"return\": \"GRIDCONFLICT\" }";
    exit;
 } 

 $ftopic = "#" . $utest;

 $res = mysql_query("SELECT gid FROM griddles WHERE topic='$ftopic'");
 $there = mysql_num_rows($res);
 $fnow = time();

 if($there) { // This griddle exists, update the last_post
    $grow = mysql_fetch_array($res);
    $gid = $grow{'gid'};
    


 } else { // This is a new griddle add it to the griddles table
    $res = mysql_query("SELECT gid FROM griddles ORDER BY gid DESC LIMIT 1");
    $grow = mysql_fetch_array($res);
    $gid = $grow{'gid'} + 1;
    $sql = mysql_query("INSERT INTO griddles VALUES($gid, 0, 0, $ftype, 1, 0, 0, '$ftopic', $fnow, $fnow, '', 1, 0, 1, 1, '', '', '')");
 }

 return $gid;

}

function updateLastPost($gid) {

    $res = mysql_query("SELECT posts FROM griddles WHERE gid=$gid");
    $resr = mysql_fetch_row($res);
    $rposts = $resr{'posts'};
    $rposts++;
    $fnow = time();
    $res = mysql_query("UPDATE griddles SET last_post=$fnow, posts=$rposts WHERE gid=$gid");

}

function handle_post($fuid, $fgid, $fpost_text, $imageline, $status) {

   
   GLOBAL $redis;

   $sql = mysql_query("SELECT pid FROM posts ORDER BY pid DESC LIMIT 1");
   $row = mysql_fetch_array($sql);
   $pid = $row{'pid'} + 1;
   $fnow = time();


   if($status == 1) {  // Get the next tid and set a trigger to send notifications

       $sql = mysql_query("SELECT tid FROM triggers ORDER BY tid DESC LIMIT 1");
       $row = mysql_fetch_array($sql);
       $tid = $row{'tid'} + 1;

   
       $sql = mysql_query("INSERT INTO triggers VALUES($tid, $fgid, $pid, $fuid, 1, $fnow, 0, 0, 0)");
   }
  

   $images = explode(" ", $imageline);

   $max = sizeof($images);

   for($i=1;$i<=$max -1;$i++) {
      
      $filename = $fuid . "-" . $pid . "-" . $fgid . "-" . $fnow;
      $sql = mysql_query("INSERT INTO posts VALUES($pid, $fgid, 0, $fuid, '$fpost_text', '$filename', 0, '', $fnow, $status, 0)");

      move_uploaded_file($_FILES[$images[$i]]['tmp_name'], FULL_DIR . $filename);
      $redis->lpush("img.process", $filename);
      
      //system("/usr/bin/convert -auto-orient -strip -resize 290x290 -quality 60 " . FULL_DIR . $filename . " " . UPLOAD_DIR . $filename, $blah);
      //system("/usr/bin/convert -auto-orient -strip -resize 640x640 -quality 60 " . FULL_DIR . $filename . " " . MIDSIZE_DIR . $filename, $blah);
      //system("/usr/bin/convert -auto-orient -strip -resize 75x75 -quality 60 " . FULL_DIR . $filename . " " . THUMB_DIR . $filename, $blah);
      $pids .= "$pid,";
      $pid++;
      $coms++;
   }

   $sql = mysql_query("INSERT INTO griddle_bb VALUES(DEFAULT, $fgid, $fuid, '', '$pids', 4, $fnow, '', 0, 0, 0, 0, 0, 0, 1)");

}


