<?php

include "/var/www/dbinc.php";
include "/var/www/functions.php";

define('UPLOAD_DIR', '/var/www/griddle_profiles/');
define('THUMB_DIR', '/var/www/thumb_profiles/');

$user   = addslashes($_POST['username']);
$pass   = addslashes($_POST['password']);
$count  = addslashes($_POST['count']);
$bbid   = addslashes($_POST['bbid']);
$pid    = addslashes($_POST['pid']);
$gid    = addslashes($_POST['gid']);
$tuid   = addslashes($_POST['uid']);
$page   = addslashes($_POST['page']);

if(!$user || !$pass || !$count) {
   print "{ \"return\": \"ERROR\", \"details\": \"Must provide at least a username, password and count\" }";
   exit;
}

if(apiAuth($user, $pass) < 1) { 
     print "{ \"return\": \"ERROR\" }";
     exit;
}

$uid = getUser($user);

$JSON = "{ \"return\": \"SUCCESS\", \"posts\": [ ";

if($bbid) { 
   $JSON .= getBBIDFeed($bbid, $uid, $count);
}

if($gid) {
   $JSON .= getGIDFeed($gid, $uid, $count, $page);
}

if($pid) {
   $JSON .= getPIDFeed($pid, $uid, $count);
}

if($tuid) {
   $JSON .= getUIDFeed($tuid, $uid, $count, $page);
   $ui  = getUserInfo($tuid);
   $n   = $ui{'name'};
   $u   = $ui{'username'};
   $fol = $ui{'followers'};
   $pst = $ui{'posts'};
   $pic = "http://www.griddle.com/griddle_profiles/$u";
   $fl  = isFollowing($uid, $tuid);
   
   if(!$fl) { $fl = "0"; }
   
   $EXTRA = ", \"name\": \"$n\",
               \"username\": \"$u\",
               \"followers\": \"$fol\",
               \"posts\": \"$pst\",
               \"pimg\": \"$pic\",
               \"fl\": \"$fl\" ";
   
}

if(($bbid=="") && ($gid=="") && ($pid=="") && ($tuid=="")) {
   $JSON .= getRandomFeed($uid, $count, $page);
} 

$JSON .= " ] $EXTRA }";
print "$JSON";


function getGIDFeed($gid, $uid, $count, $page) {


    if($page) {
      $offset = $count * $page;
      $count = "$offset, $count";
    }


    $SQL = "SELECT bbid, uid FROM griddle_bb WHERE gid=$gid AND status=1 LIMIT $count";
    $res = mysql_query($SQL);
    while($row = mysql_fetch_array($res)) {	
     $bbid  = $row{'bbid'};
     $buid  = $row{'uid'};
     $bbi   = getGriddleInfo($bbid);
     $PLIST = explode(",", $bbi{'ppid'});
     
     $maxr  = count($PLIST);
     $picr  = rand(0, $maxr);
     $pid   = $PLIST[$picr];
     $gid   = $row{'gid'};
  
     $pi    = getPostInfo($pid);
     $puid  = $pi{'uid'};
     $img   = shardImg($pi{'images'}) . "/mid_images/" . $pi{'images'};
     $timg  = shardImg($pi{'images'}) . "/thumb_images/" . $pi{'images'};
   
     $comms = $pi{'comments'};
     $hots  = $pi{'hots'};
     $ui    = getUserInfo($puid);
     $n     = $ui{'name'};
     $un    = $ui{'username'};
     $pimg  = "http://www.griddle.com/thumb_profiles/$un";
   
     if(didHotG($uid, $bbid)) { $didhot = "1"; } else { $didhot="0"; }
     if(didCommG($uid, $bbid)) { $didcom = "1"; } else { $didcom="0"; }

     $more    = "1";
     $when    = secondsToTime(time() - $pi{'din'});
     $din     = $pi{'din'};
   
     $JSON .= "{ \"n\": \"$n\",
               \"un\": \"$un\",
               \"uid\": \"$buid\",
               \"img\": \"$img\",
               \"timg\": \"$timg\",
               \"pimg\": \"$pimg\",
               \"comms\": \"$comms\",
               \"hots\": \"$hots\",
               \"didhot\": \"$didhot\",
               \"didcom\": \"$didcom\",
               \"more\": \"$more\",
               \"pid\": \"$pid\",
               \"bbid\": \"$bbid\",
               \"when\": \"$when\",
               \"din\": \"$din\",
               \"gid\": \"$gid\" },\n";
  
  }
   
  $JSON = rtrim($JSON, ",\n"); 
  
  return $JSON;

}
    
function getPIDFeed($pid, $uid, $count) {

        $pi    = getPostInfo($pid);
        $puid  = $pi{'uid'};
        $img   = shardImg($pi{'images'}) . "/mid_images/" . $pi{'images'};
        $timg  = shardImg($pi{'images'}) . "/thumb_images/" . $pi{'images'};
        $gid   = $pi{'gid'};
        $gi    = getGridInfo($gid);
        $ht    = $gi{'topic'};
   
        $comms = $pi{'comments'};
        $hots  = $pi{'hots'};
        $ui    = getUserInfo($puid);
        $n     = $ui{'name'};
        $un    = $ui{'username'};
        $pimg  = "http://www.griddle.com/thumb_profiles/$un";
   
        if(didHot($uid, $pid)) { $didhot = "1"; } else { $didhot="0"; }
        if(didComm($uid, $pid)) { $didcom = "1"; } else { $didcom="0"; }

        $more    = "1";
        $when    = secondsToTime(time() - $pi{'din'});
        $din     = $pi{'din'};
   
        $JSON .= "{ \"n\": \"$n\",
               \"un\": \"$un\",
               \"uid\": \"$puid\",
               \"img\": \"$img\",
               \"timg\": \"$timg\",
               \"pimg\": \"$pimg\",
               \"comms\": \"$comms\",
               \"hots\": \"$hots\",
               \"didhot\": \"$didhot\",
               \"didcom\": \"$didcom\",
               \"more\": \"$more\",
               \"pid\": \"$pid\",
               \"bbid\": \"$bbid\",
               \"gid\": \"$gid\",
               \"when\": \"$when\",
               \"din\": \"$din\",
               \"hashtag\": \"$ht\" },\n";
  
            
     $JSON = rtrim($JSON, ",\n"); 
  
     return $JSON;



}


function getBBIDFeed($bbid, $uid, $count) {
     
     $bbi   = getGriddleInfo($bbid);
     $PPIDS = $bbi{'ppid'};
     $PPIDS = rtrim($PPIDS, ","); 
     $PLIST = explode(",", $PPIDS);
     foreach ($PLIST as $pid) {
        $pi    = getPostInfo($pid);
        $puid  = $pi{'uid'};
        $img   = shardImg($pi{'images'}) . "/mid_images/" . $pi{'images'};
        $timg  = shardImg($pi{'images'}) . "/thumb_images/" . $pi{'images'};
        $gid   = $pi{'gid'};
        $gi    = getGridInfo($gid);
        $ht    = $gi{'topic'};
   
        $comms = $pi{'comments'};
        $hots  = $pi{'hots'};
        $ui    = getUserInfo($puid);
        $n     = $ui{'name'};
        $un    = $ui{'username'};
        $pimg  = "http://www.griddle.com/thumb_profiles/$un";
   
        if(didHotG($uid, $bbid)) { $didhot = "1"; } else { $didhot="0"; }
        if(didCommG($uid, $bbid)) { $didcom = "1"; } else { $didcom="0"; }

        $more    = "1";
        $when    = secondsToTime(time() - $pi{'din'});
        $din     = $pi{'din'};
   
        $JSON .= "{ \"n\": \"$n\",
               \"un\": \"$un\",
               \"uid\": \"$puid\",
               \"img\": \"$img\",
               \"timg\": \"$timg\",
               \"pimg\": \"$pimg\",
               \"comms\": \"$comms\",
               \"hots\": \"$hots\",
               \"didhot\": \"$didhot\",
               \"didcom\": \"$didcom\",
               \"more\": \"$more\",
               \"pid\": \"$pid\",
               \"bbid\": \"$bbid\",
               \"gid\": \"$gid\",
               \"when\": \"$when\",
               \"din\": \"$din\",
               \"hashtag\": \"$ht\" },\n";
  
     }          
     $JSON = rtrim($JSON, ",\n"); 
  
     return $JSON;

}

function getRandomFeed($uid, $count, $page) {


   if($page) {
      $offset = $count * $page;
      $count = "$offset, $count";
   }

   //$ORDER_BY = "RAND()";
   $ORDER_BY = "griddle_bb.din";
   $SQL = "SELECT DISTINCT(griddle_bb.bbid), griddle_bb.uid, relations.uid, users.name, griddle_bb.gid 
                  FROM relations, users, griddle_bb 
                  WHERE griddle_bb.status=1 AND 
                  (griddle_bb.uid=$uid OR (relations.friend=2 AND relations.target=$uid)) 
                  AND users.uid=relations.uid AND griddle_bb.uid=users.uid 
                  ORDER BY $ORDER_BY DESC LIMIT $count";
   $res = mysql_query($SQL);
   
   while($row = mysql_fetch_array($res)) {

     $bbid  = $row{'bbid'};
     $buid  = $row{'uid'};
     $bbi   = getGriddleInfo($bbid);
     $PPIDS = $bbi{'ppid'};
     $PPIDS = rtrim($PPIDS, ","); 
     $PLIST = explode(",", $PPIDS);
   
     $maxr  = count($PLIST) -1;
     $picr  = rand(0, $maxr);
     $pid   = $PLIST[$picr];
     $gid   = $row{'gid'};
     $gi    = getGridInfo($gid);
     $ht    = $gi{'topic'};
  
     $pi    = getPostInfo($pid);
     $puid  = $pi{'uid'};
     $img   = shardImg($pi{'images'}) . "/mid_images/" . $pi{'images'};
     $timg  = shardImg($pi{'images'}) . "/thumb_images/" . $pi{'images'};
   
     $comms = $pi{'comments'};
     $hots  = $pi{'hots'};
     $ui    = getUserInfo($puid);
     $n     = $ui{'name'};
     $un    = $ui{'username'};
     $pimg  = "http://www.griddle.com/thumb_profiles/$un";
   
     if(didHotG($uid, $bbid)) { $didhot = "1"; } else { $didhot="0"; }
     if(didCommG($uid, $bbid)) { $didcom = "1"; } else { $didcom="0"; }

     $more    = "1";
     $when    = secondsToTime(time() - $pi{'din'});
     $din     = $pi{'din'};
   
     $JSON .= "{ \"n\": \"$n\",
               \"un\": \"$un\",
               \"uid\": \"$buid\",
               \"img\": \"$img\",
               \"timg\": \"$timg\",
               \"pimg\": \"$pimg\",
               \"comms\": \"$comms\",
               \"hots\": \"$hots\",
               \"didhot\": \"$didhot\",
               \"didcom\": \"$didcom\",
               \"more\": \"$more\",
               \"pid\": \"$pid\",
               \"bbid\": \"$bbid\",
               \"when\": \"$when\",
               \"din\": \"$din\",
               \"gid\": \"$gid\",
               \"hashtag\": \"$ht\" },\n";
  
  } 
   
  $JSON = rtrim($JSON, ",\n"); 
  
  return $JSON;

}

function getUIDFeed($tuid, $uid, $count, $page) {

   if($page) {
      $offset = $count * $page;
      $count = "$offset, $count";
   }


   //$ORDER_BY = "RAND()";
   $ORDER_BY = "din";
   /* $SQL = "SELECT DISTINCT(griddle_bb.bbid), griddle_bb.uid, relations.uid, users.name, griddle_bb.gid 
                  FROM relations, users, griddle_bb 
                  WHERE griddle_bb.status=1 AND 
                  (griddle_bb.uid=$uid OR (relations.friend=2 AND relations.target=$uid)) 
                  AND users.uid=relations.uid AND griddle_bb.uid=users.uid 
                  ORDER BY $ORDER_BY DESC LIMIT $count"; */
                  
   $SQL = "SELECT bbid, gid, uid FROM griddle_bb WHERE status=1 AND uid=$tuid ORDER BY $ORDER_BY LIMIT $count";               
   
   
   $ui    = getUserInfo($tuid);
   $n     = $ui{'name'};
   $un    = $ui{'username'};
   $pimg  = "http://www.griddle.com/thumb_profiles/$un";
   
                  
   $res = mysql_query($SQL);
   
   while($row = mysql_fetch_array($res)) {

     $bbid  = $row{'bbid'};
     $buid  = $row{'uid'};
     $bbi   = getGriddleInfo($bbid);
     $PPIDS = $bbi{'ppid'};
     $PPIDS = rtrim($PPIDS, ","); 
     $PLIST = explode(",", $PPIDS);
   
     $maxr  = count($PLIST) -1;
     $picr  = rand(0, $maxr);
     $pid   = $PLIST[$picr];
     $gid   = $row{'gid'};
     $gi    = getGridInfo($gid);
     $ht    = $gi{'topic'};
  
     $pi    = getPostInfo($pid);
     $puid  = $pi{'uid'};
     $img   = shardImg($pi{'images'}) . "/mid_images/" . $pi{'images'};
     $timg  = shardImg($pi{'images'}) . "/thumb_images/" . $pi{'images'};
   
     $comms = $pi{'comments'};
     $hots  = $pi{'hots'};
     
   
     if(didHotG($uid, $bbid)) { $didhot = "1"; } else { $didhot="0"; }
     if(didCommG($uid, $bbid)) { $didcom = "1"; } else { $didcom="0"; }

     $more    = "1";
     $when    = secondsToTime(time() - $pi{'din'});
     $din     = $pi{'din'};
   
     $JSON .= "{ \"n\": \"$n\",
               \"un\": \"$un\",
               \"uid\": \"$buid\",
               \"img\": \"$img\",
               \"timg\": \"$timg\",
               \"pimg\": \"$pimg\",
               \"comms\": \"$comms\",
               \"hots\": \"$hots\",
               \"didhot\": \"$didhot\",
               \"didcom\": \"$didcom\",
               \"more\": \"$more\",
               \"pid\": \"$pid\",
               \"bbid\": \"$bbid\",
               \"when\": \"$when\",
               \"din\": \"$din\",
               \"gid\": \"$gid\",
               \"hashtag\": \"$ht\" },\n";
  
  } 
   
  $JSON = rtrim($JSON, ",\n"); 
  
  return $JSON;

}





?>
