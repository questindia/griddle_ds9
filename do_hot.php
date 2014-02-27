<?php

$r = time();
include "dbinc.php";
include "functions.php";


if($_SESSION['user'] == '') {
   exit;
} 

$action = $_GET['action'];

if($action == "list") {

    $bbid = $_GET['bbid'];

    $res = mysql_query("SELECT uid, din FROM hots_bb WHERE bbid=$bbid LIMIT 25");

    while($prow = mysql_fetch_array($res)) {
      $uid = $prow{'uid'};

      $output = $output . makeUserRow($uid); 

    }

    if(!$output) {
       $output = " &nbsp;<h4>This Griddle has no votes</h4>&nbsp; ";
    }
    
    print "<table border=0>$output</table>";
}

if($action == "listfollows") {
   

   $gid = $_GET['gid'];

   $res = mysql_query("SELECT follows.uid fuid, users.uid FROM follows, users WHERE follows.gid=$gid AND follows.uid=users.uid AND users.status=1 ORDER BY follows.din DESC LIMIT 20");

   while($prow = mysql_fetch_array($res)) {
      $uid = $prow{'fuid'};

      $output = $output . makeUserRow($uid);

   }
    print "$output";
    //print "{ \"gid\":$gid, \"content\":\"$output\" }";
 

}

if($action == "listongrids") {


   $gid = $_GET['gid'];

   $res = mysql_query("SELECT posts.uid puid, users.uid FROM posts, users WHERE posts.gid=$gid AND posts.uid=users.uid AND users.status=1 ORDER BY posts.din DESC LIMIT 20");

   while($prow = mysql_fetch_array($res)) {
      $uid = $prow{'puid'};

      if($SEENIT{$uid} != 1) {
        $SEENIT{$uid} = 1;
        $output = $output . makeUserRow($uid);
      }
   }

   print "{ \"gid\":$gid, \"content\":\"$output\" }";

}


if(!$action) {

   $vote = $_GET['vote'];

   if(!$vote) {
      exit;
   }

   $bbid = $_GET['bbid'];
   $user = $_SESSION['user'];

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
      $content = " <span class='glyphicon glyphicon-heart'></span>";
   } elseif ($vote == "down") {
      $hots = $hots - 1;
      $content = " <span class='glyphicon glyphicon-hand-up'></span>";
   }

   
   $res = mysql_query("UPDATE griddle_bb SET hots=$hots WHERE bbid=$bbid");
   
   $din = time();

   if($new!="no") {
      $res = mysql_query("INSERT INTO hots_bb VALUES($uid, $bbid, $din)");
   }
   print "{ \"hots\":$hots, \"bbid\":$bbid, \"content\":\"$content\" }";



} 

function makeUserRow($muid) {
  
      $outrow = "";
      GLOBAL $imgSRV;
 
      $ures = mysql_query("SELECT username, name, posts, followers FROM users WHERE uid=$muid");
      $urow = mysql_fetch_array($ures);
      $tuser = $urow{'username'};
      $tname = $urow{'name'};
      $tpos = $urow{'posts'};
      $tfol = $urow{'followers'};

      $outrow = "<tr><td align=left width=45px><a class=userButton id=userButton-$tuid href='/m/do_rels.php?target=$muid'><img class=cropimgSmall src=$imgSRV/thumb_profiles/$tuser></a></td>
                       <td align=left width=240px><span style='font-size: medium;'><a class=userButton id=userButton-$muid href='/m/do_rels.php?target=$muid'>$tname</a></span><br><span style='font-size: xx-small;'> +$tpos Posts | +$tfol Followers</span></td>
                       </tr>";


      //$outrow = $outrow . "<table border=0><tr><td align=left width=45><img class=cropimgSmall src=$imgSRV/thumb_profiles/$tuser?$r></td>";
      //$outrow = $outrow . "  <td align=left><p style='font-size:x-small'>$tname<br>$tuser|+$tpos Posts|+$tfol Followers</p></td></tr></table>";
  
      return $outrow;

}


?>
