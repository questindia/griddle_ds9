<?php
/*
 * jQuery File Upload Plugin PHP Example 5.14
 * https://github.com/blueimp/jQuery-File-Upload
 *
 * Copyright 2010, Sebastian Tschan
 * https://blueimp.net
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

require 'Predis/Autoloader.php';

Predis\Autoloader::register();

$redis = new Predis\Client('tcp://grd-mysql-01.griddle.com:6379');

define('UPLOAD_DIR', '/var/www/griddle_images/');
define('FULL_DIR', '/var/www/full_images/');
define('THUMB_DIR', '/var/www/thumb_images/');
define('MIDSIZE_DIR', '/var/www/mid_images/');

error_reporting(E_ALL | E_STRICT);
include "dbinc.php";
include "functions.php";

$IMAGES_TO_PROC = "";
$bbid = "";
$PIDLINE = "";

function updateLastPost($gid) {

    $res = mysql_query("SELECT posts FROM griddles WHERE gid=$gid");
    $resr = mysql_fetch_row($res);
    $rposts = $resr{'posts'};
    $rposts++;
    $fnow = time();
    $res = mysql_query("UPDATE griddles SET last_post=$fnow, posts=$rposts WHERE gid=$gid");

}

function do_griddle($ftopic, $fuid, $ftype) {


   $utest = preg_replace("/[^A-Za-z0-9]/", '', $ftopic);
   $res = mysql_query("SELECT username FROM users WHERE username='$utest'");
   $uthere = mysql_num_rows($res);

   if($uthere) {
     // Logit and Bail for now
     //logit("Found that $utest already exists as a username - grid / username conflict");
    header( "Location: http://$baseSRV" );
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

function handle_post($fuid, $fgid, $fpost_text, $imageline, $status, $geo, $purpose, $colabs, $bbcheck) {

   GLOBAL $IMAGES_TO_PROC;

   if(!$bbcheck) {
      $bbcheck = "0";
   }
   
   $sql = mysql_query("SELECT pid FROM posts ORDER BY pid DESC LIMIT 1");
   $row = mysql_fetch_array($sql);
   $pid = $row{'pid'} + 1;
   $ppid = $pid;
   $fnow = time();
   
   $images = explode("|", $imageline);

   $max = sizeof($images);

   for($i=1;$i<=$max -1;$i++) {
      
      $filename = $fuid . "-" . $pid . "-" . $fgid . "-" . $fnow;
      $sql = mysql_query("INSERT INTO posts VALUES($pid, $fgid, 0, $fuid, '$fpost_text', '$filename', 0, '$geo', $fnow, $status, 0)");
      $sql = mysql_query("INSERT INTO pending_post VALUES(DEFAULT, $fuid, $fgid, $pid, '$images[$i]', '$purpose', '$colabs', $bbcheck, '$geo', 0)");
      $IMAGES_TO_PROC .= ":$pid";
 
      $pid++;
      $coms++;
   }
 
  

}


require('UploadHandler.php');
$upload_handler = new UploadHandler();

$IMGS = explode(":", $IMAGES_TO_PROC);

file_put_contents("/tmp/do_post.log", "IMG - $IMAGES_TO_PROC\n", FILE_APPEND);

$IC = sizeof($IMGS);

for($i=1;$i<=$IC-1;$i++) {
      
      file_put_contents("/tmp/do_post.log", "IMG - $IMGS[$i]\n", FILE_APPEND);
      $res = mysql_query("SELECT * FROM pending_post WHERE pid=$IMGS[$i] AND status=0");
      $row = mysql_fetch_array($res);
      $ppid = $row{'ppid'};
      $oldname = $row{'imgname'};
      
      $oldname = preg_replace('/|/', '', $oldname);
      
      $res = mysql_query("SELECT images FROM posts WHERE pid=$IMGS[$i]");
      $row = mysql_fetch_array($res);
      $filename = $row{'images'};

      system("/bin/mv '/var/www/test_images/$oldname' '" . FULL_DIR . $filename . "'");
      $redis->lpush("img.process", $filename);
      //system("/usr/bin/convert -auto-orient -strip -resize 290x290 -quality 60 '" . FULL_DIR . $filename . "' '" . UPLOAD_DIR . $filename . "'", $blah);
      //system("/usr/bin/convert -auto-orient -strip -resize 640x640 -quality 60 '" . FULL_DIR . $filename . "' '" . MIDSIZE_DIR . $filename . "'", $blah);
      //system("/usr/bin/convert -auto-orient -strip -resize 75x75 -quality 60 '" . FULL_DIR . $filename . "' '" . THUMB_DIR . $filename . "'", $blah);

}




