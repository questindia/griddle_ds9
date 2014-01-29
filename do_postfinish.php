<?php

include "dbinc.php";
include "functions.php";
require 'Predis/Autoloader.php';

Predis\Autoloader::register();

$redis = new Predis\Client('tcp://grd-mysql-01.griddle.com:6379');

$user = $_SESSION['user'];

$uid = getUser($user);

$action = addslashes($_GET['action']);

if($action == "finish") {
   
   
   $res = mysql_query("SELECT * FROM pending_post WHERE uid=$uid AND status=0");
   $row = mysql_fetch_array($res);
   $pid = $row{'pid'};
   $pur = $row{'purpose'};
   $col = $row{'colabs'};
   $geo = $row{'geo'};
   $bbcheck = $row{'bbid'};
   $pi = getPostInfo($pid);
   $gid = $pi{'gid'};
   $fnow = time();
   
   if($pur == "grid") {
      // This is just a regular posting of pictures to a grid
      $status = $pi{'status'};
   
      if($status == 1) {  // Get the next tid and set a trigger to send notifications
         $sql = mysql_query("SELECT tid FROM triggers ORDER BY tid DESC LIMIT 1");
         $row = mysql_fetch_array($sql);
         $tid = $row{'tid'} + 1;

         $sql = mysql_query("INSERT INTO triggers VALUES($tid, $gid, $pid, $uid, 1, $fnow, 0, 0, 0)");
      }
      $res = mysql_query("UPDATE pending_post SET status=1 WHERE uid=$uid AND gid=$gid AND status=0");
      exit;
   } elseif ($pur == "griddle") {
     //
     // This is a start of a griddle
     mysql_data_seek($res, 0);
     
     $ptotal = mysql_num_rows($res);
     
     while($row = mysql_fetch_array($res)) {
        $pid = $row{'pid'};
        $pi = getPostInfo($pid);
        $img = $pi{'images'};
        $PPIDTMP .= "$pid,";
        $IMGLTMP .= "$img,";
     }
     $PIDLINE = rtrim($PPIDTMP, ",");
     $IMGLINE = rtrim($IMGLTMP, ",");
     

     
     if($ptotal>=9) {
       $bbsql = mysql_query("INSERT INTO griddle_bb VALUES(DEFAULT, $gid, $uid, ',$col,', '$PIDLINE', $fnow, '$geo', 0, 0, 1)");
       $bbsql = mysql_query("SELECT bbid FROM griddle_bb WHERE gid=$gid AND uid=$uid AND ppid='$PIDLINE' AND din=$fnow LIMIT 1");
       $row = mysql_fetch_array($bbsql);
       $bbid = $row{'bbid'};
       $redis->lpush("bb.process", "$bbid|$IMGLINE");
        $sql = mysql_query("INSERT INTO triggers_bb VALUES(DEFAULT, $gid, $bbid, $uid, 4, $fnow, 0, 0, 0)");
     } else {
       $bbsql = mysql_query("INSERT INTO griddle_bb VALUES(DEFAULT, $gid, $uid, ',$col,', '$PIDLINE', $fnow, '$geo', 0, 0, 0)");
       $bbsql = mysql_query("SELECT bbid FROM griddle_bb WHERE gid=$gid AND uid=$uid AND ppid='$PIDLINE' AND din=$fnow LIMIT 1");
       $row = mysql_fetch_array($bbsql);
       $bbid = $row{'bbid'};
       //doMMSInform($col, $bbid);
       $sql = mysql_query("INSERT INTO triggers_bb VALUES(DEFAULT, $gid, $bbid, $uid, 3, $fnow, 0, 0, 0)");
     }
     
     $res = mysql_query("UPDATE pending_post SET status=1 WHERE uid=$uid AND gid=$gid AND status=0");
     
   } elseif ($pur == "complete") {
     //
     // This is a continuation of a griddle
     mysql_data_seek($res, 0);
     
     file_put_contents("/tmp/do_postfinish.log", "Into pur=complete\n", FILE_APPEND);
     
     while($row = mysql_fetch_array($res)) {
        $pid = $row{'pid'};
        $PPIDTMP .= "$pid,";

     }
     file_put_contents("/tmp/do_postfinish.log", "Building PPIDTMP = $PPIDTMP\n", FILE_APPEND);
     $PIDLINE = rtrim($PPIDTMP, ",");
  
     
     $bbres = mysql_query("SELECT * FROM griddle_bb WHERE bbid=$bbcheck");
     $row = mysql_fetch_array($bbres);
     $PPIDS = $row{'ppid'};
     $colab = $row{'colabs'};
     $colab = $colab . ",$col,";
     
     $FINALPIDS = $PPIDS . "," . $PIDLINE;
     
     $PLIST = explode(",", $FINALPIDS);
     
     $ptotal = sizeof($PLIST);
     
     if($ptotal>=9) {
       $bbsql = mysql_query("UPDATE griddle_bb SET colabs='$colab', ppid='$FINALPIDS', status=1 WHERE bbid=$bbcheck");
       foreach($PLIST as $pid) {     
          $pi = getPostInfo($pid);
          $img = $pi{'images'};
          $IMGTMP .= "$img,";
          file_put_contents("/tmp/do_postfinish.log", "Building IMGLINE - $pid - $img\n", FILE_APPEND);
       }
       $IMGLINE = rtrim($IMGTMP, ",");
       file_put_contents("/tmp/do_postfinish.log", "Sending $bbcheck-$IMGLINE to redis queue\n", FILE_APPEND);
       $redis->lpush("bb.process", "$bbcheck|$IMGLINE");
        $sql = mysql_query("INSERT INTO triggers_bb VALUES(DEFAULT, $gid, $bbcheck, $uid, 4, $fnow, 0, 0, 0)");
     } else {
       $bbsql = mysql_query("UPDATE griddle_bb SET colabs='$colab', ppid='$FINALPIDS' WHERE bbid=$bbcheck");
       //doMMSInform($colab, $bbcheck);
       $sql = mysql_query("INSERT INTO triggers_bb VALUES(DEFAULT, $gid, $bbcheck, $uid, 3, $fnow, 0, 0, 0)");
     }
     
     $res = mysql_query("UPDATE pending_post SET status=1 WHERE uid=$uid AND gid=$gid AND status=0");
   
   }
     

}




?>


