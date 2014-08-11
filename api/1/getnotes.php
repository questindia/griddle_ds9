<?php

include "/var/www/dbinc.php";
include "/var/www/functions.php";

$user   = addslashes($_POST['username']);
$pass   = addslashes($_POST['password']);
$page   = addslashes($_POST['page']);

if(!$user || !$pass || !$page ) {
   print "{ \"return\": \"ERROR\", \"details\": \"Must provide all fields\" }";
   exit;
}

if(apiAuth($user, $pass) < 1) { 
     print "{ \"return\": \"ERROR\", \"details\": \"Username or Password incorrect\" }";
     exit;
}

$uid = getUser($user);

$ls = ($page - 1) * 10;

$limit = "$ls, 10";

$count = gotNotes($uid);

$JSON = "{ \"return\": \"SUCCESS\", \"count\": \"$count\", \"notes\": [ ";

$JSON .= getNoteRows($uid, $limit);

$JSON .= " ] }";

#print "$limit<br><br>";

print $JSON;  




function getNoteRows($uid, $limit) {

  GLOBAL $MOBILE;

   // Get all Pending Notifications
  $res = mysql_query("SELECT nid, req, type, note, pid, status, cid, din FROM notes WHERE type!=3 AND uid=$uid ORDER BY din DESC LIMIT $limit");
  //$count = mysql_num_rows($res);


  while($row = mysql_fetch_array($res)) {
   $nid = $row{'nid'};
   $req = $row{'req'};
   $type = $row{'type'};
   $note = $row{'note'};
   $pid = $row{'pid'};
   $cid = $row{'cid'};
   $status = $row{'status'};
   $din = $row{'din'};
   
   $ago = time() - $din;
   $when = secondsToTime($ago);

   $rres = mysql_query("SELECT name, username, posts, followers FROM users WHERE uid=$req");
   $rrow = mysql_fetch_array($rres);
   $tuser = $rrow{'username'};
   $tpos = $rrow{'posts'};
   $tfol = $rrow{'followers'};
   $tnam = $rrow{'name'};

   //$tnam = wrapName($req, $tnam);

   if($type == 1) {
      $ntype = "friendrequest";
      $note = "<a href='/person.php?target=$req'>$tnam</a> has sent you a friend request!";
      $JSnote = "$tnam has sent you a friend request!";
      if($status == 1) { // This is a friend request
         $actionLine = "<a href=/do_friends.php?action=acceptfriend&req=$req&nid=$nid>Accept</a>|";
         $actionLine = $actionLine . "<a href=/do_friends.php?action=rejectfriend&req=$req&nid=$nid>Decline</a>";
      } else {
         //$actionLine = "<a class=actLink href=/do_notes.php?action=dismiss&nid=$nid>Dismiss (x)</a>";
        $res2 = mysql_query("SELECT friend FROM relations WHERE uid=$req AND target=$uid");
        $row2 = mysql_fetch_array($res2);
        if($row2{'friend'} == 1) {
           $actionLine = "You declined the friend request.";
        } else {
           $actionLine = "You are now Friends.";
        }  
        $JSnote .= " - $actionLine";
      }
   } elseif($type == 2) {
      $ntype = "friendaccept";
      $note = "<a href='/person.php?target=$req'>$tnam</a> has accepted your friend request!";
      $JSnote = "$tnam has accepted your friend request!";
   } else { $actionLine = ""; }

   if($type == 3) { continue; }

   if($pid > 0) {
     $pi    = getPostInfo($pid);
     $image = $pi{'images'};
     $pgid  = $pi{'gid'};
     $gi    = getGridInfo($pgid);
     $ptop  = $gi{'topic'};
     
     $imgSRV = shardImg($din);
     
     if($MOBILE) { $COL_LINK = "/do_colab.php"; } else { $COL_LINK = "/griddles.php?gid=$pgid"; }
     
     $IMGLINE = "<td align=left valign=top width=45><a id=comText-$bbid class=viewPost $comStyle href=/view.php?bbid=$bbid><img class='cropimgPro' src=$imgSRV/griddles/$image></a></td>";
     if($cid > 0) {
          $ntype = "comment";
          $ci = getPostCommentInfo($cid);
          $message = $ci{'comment'};
          $message = stripslashes($message); 
          if(strlen($message) > 47) { $message = substr($message, 0, 47) . "..."; }
          $note = "<a href='/person.php?target=$req'>$tnam</a> commented <a style='color:#0088cc;' href=/griddles.php?gid=$pgid>$ptop</a> - $message <span style='font-size: xx-small;'>$when</span>";
          $JSnote = "$tnam commented - $message";
     } else {
         if($type==5) {
            $note = "<a href='/person.php?target=$req'>$tnam</a> wants your help to create a Griddle! <a style='color:#0088cc;' href='$COL_LINK'>$ptop</a> <span style='font-size: xx-small;'>$when</span>";
            $IMGLINE = "";
         } elseif($type==6) {
             $note = "A Griddle - <a style='color:#0088cc;' href='$COL_LINK'>$ptop</a> has been completed! <span style='font-size: xx-small;'>$when</span>"; 
         }
     }
     $COLSPAN = "";
   } else {
     $image = "";
     $IMGLINE = "";
     $COLSPAN = "colspan=2";
   }



   if($status == 1) {
      if($type > 1) {
         $cres = mysql_query("UPDATE notes_bb SET status=2 WHERE nid=$nid");
      }
      $TABLECOLOR = "style='background-color: #e9eaed;'";
      $count++;
   } else {
      $TABLECOLOR = "";
   }
   
   if($image) {
      $JSimg = "$imgSRV/thumb_images/$image";
   }
   
   $JSnote = preg_replace('/\n/', ' ', $JSnote);
   $JSnote = preg_replace('/\r/', ' ', $JSnote);
   
   $JSON .= "{ \"nid\": \"$nid\",
               \"note\": \"$JSnote\",
               \"pid\": \"$pid\",
               \"gid\": \"$pgid\",
               \"req\": \"$req\",
               \"ntype\": \"$ntype\",
               \"pimg\": \"http://www.griddle.com/thumb_profiles/$tuser\",
               \"timg\": \"$JSimg\",
               \"when\": \"$when\"},\n";

   $content .= "<tr style='border-bottom:1pt solid #cdcecf;'>
                     <td align=left valign=top width=45><img class='cropimgPro' src=$imgSRV/thumb_profiles/$tuser?$rd></td>
                     <td align=left $COLSPAN valign=top><p style='max-width:300px; margin-top:3px; line-height:120%; font-size:small;'>$note</p><p style='line-height:110%; font-size:small;'>$actionLine</p></td>
                     $IMGLINE          
                  </tr>";
   
  
  }
  
  $JSON = rtrim($JSON, ",\n");
  
  return $JSON;

}





?>
