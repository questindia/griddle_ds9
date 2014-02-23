<?php
include "dbinc.php";
include "functions.php";

$r = time(); 
$rd = substr($r, 0, 8);

$action = addslashes($_GET['action']);
if(!$action) { $action = addslashes($_POST['action']); }

$search = addslashes($_POST['search']);

$target = $_GET['target'];
$kind = "friend";

if($kind=='friend') { $type = "AND type<3"; } else { $type = "AND type>2"; } 

$user = $_SESSION['user'];

if(!$user) { // bail
   header( "Location: http://$baseSRV/" );
   exit;
}

$uid = getUser($user);

if($action == "acceptfriend") {
   $treq = $_GET['req'];
   $tnid = $_GET['nid'];

   $res = mysql_query("UPDATE relations SET friend=2 WHERE uid=$treq AND target=$uid");
   $res = mysql_query("UPDATE notes_bb SET status=2 WHERE nid=$tnid");

   $res = mysql_query("INSERT INTO notes_bb VALUES(DEFAULT, $treq, 0, 0, $uid, 2, $r, '$user has acccepted your friend request!', 1)");

   $res = mysql_query("SELECT rid FROM relations WHERE uid=$uid and target=$treq");
   $row = mysql_fetch_array($res);
   $rrid = $row{'rid'};
   if($rrid) {
       $res = mysql_query("UPDATE relations SET friend=2 WHERE uid=$uid and target=$treq");
   } else {
    
       $res = mysql_query("INSERT INTO relations VALUES(DEFAULT, $uid, 2, 0, $treq, 2, $r)");
   }
   $action = "";
}

if($action == "rejectfriend") {
   $treq = $_GET['req'];
   $tnid = $_GET['nid'];

   $res = mysql_query("UPDATE relations SET friend=1 WHERE uid=$treq AND target=$uid");
   $res = mysql_query("UPDATE notes_bb SET status=2 WHERE nid=$tnid");
   $action = "";

}

if($action == "dismiss") {
   $tnid = $_GET['nid'];
   $res = mysql_query("UPDATE notes_bb SET status=2 WHERE nid=$tnid");
   $action = "";
}

$count = "0";

if($action == "more") {

    $OFFSET = $_SESSION['FRIENDS_OFFSET'];

    if(!$OFFSET) {
       $OFFSET = "10";
    } else { 
       $OFFSET += 10; 
    }
    
    $_SESSION['FRIENDS_OFFSET'] = $OFFSET;
    
    $LIMIT = "$OFFSET, 10";
    
    $NOTES .= getNoteRows($LIMIT, $type);
        
    print "$NOTES";

    exit;

}

if($action == "search") {

    $FSEARCH = getSearchFriendRows(10, $search);
    if(!$MOBILE) {
       $C1 = $FSEARCH{'COL1'};
       $C2 = $FSEARCH{'COL2'};
       print "<div class='col-lg-5 col-md-5'><h4>Search Results:</h4><table width=90% cellpadding=5 border=0>$C1</table></div>
              <div class='col-lg-5 col-md-5'><h4>&nbsp;</h4><table width=90% cellpadding=5 border=0>$C2</table><br><br></div>";
              
    } else {
       $C1 = $FSEARCH{'COL1'};
       print "<div class='col-lg-5'><h4>Search Results:</h4><table cellpadding=5 border=0>$C1</table><br><br></div>";
    }
    exit;
}



if($action == "unfriend") {


   $res = mysql_query("UPDATE relations SET friend=0 WHERE uid=$uid AND target=$target");
   $res = mysql_query("UPDATE relations SET friend=0 WHERE uid=$target AND target=$uid");

   //header( "Location: http://$baseSRV/do_friends.php" );

   $action = "";

}


if(!$action) {

   $_SESSION['FRIENDS_OFFSET'] = "";

   include "header.php";
   print "<body>\n";
   include "fbinc.php";
   include "navbar.php";
   
   $FRIENDS = getFriendRows("9999", $uid);
   
   ?>
    <div class="container narrow">

      <div class="row row-offcanvas row-offcanvas-right">

        <div class="col-xs-12 col-md-12 col-sm-12 col-lg-10 pull-right">
         <div class='row'>
         <div class='col-lg-5'>
          <div id='noteDiv'>
      <?php
   
   $content = "<h4>Recent Requests:</h4><table id='noterows' $TWIDTH cellpadding=5 border=0>\n";

   $contentRows .= getNoteRows("0, 10", $type);
   if($contentRows == "") { 
      $content .= "<h3>No Recent Activity</h3>"; 
   } else {
      $content .= $contentRows;
   }

   $content .= "</table>";
  

   

    print "$content";
    
    print "</div><br><a href='/do_friends.php?action=more' elmAppend='#noterows' class='doMore btn btn-sm btn-primary'>More</a><br><br>";
    print "</div></div>";
    
    print "<div class='row'> <div class='col-lg-5'>
           <h4>Find Friends:</h4>
           <a id='FBCon' href=# class='FBLOGIN btn btn-sm btn-primary'><i class='fa fa-facebook-square'></i></a><br><br>
           <h6>Search: </h6> 
           <span><form id='searchFriends' action='/do_friends.php' method=post>
                 <input type=hidden name=action value='search'>
                 <input id='searchBox' class='form-control' type=text name='search' placeholder='Name or Username...'></form></span>
           
           <br><br>
           </div></div>
           <div id='searchDiv' class='row'>
           </div>
           ";
    
    print "<div class='row'>";
    
    if(!$MOBILE) {
       $C1 = $FRIENDS{'COL1'};
       $C2 = $FRIENDS{'COL2'};
       print "<div class='col-lg-5 col-md-5'><h4>Friends:</h4><table width=90% cellpadding=5 border=0>$C1</table></div>
              <div class='col-lg-5 col-md-5'><h4>&nbsp;</h4><table width=90% cellpadding=5 border=0>$C2</table></div>";
              
    } else {
       $C1 = $FRIENDS{'COL1'};
       print "<div class='col-lg-5'><h4>Friends:</h4><table cellpadding=5 border=0>$C1</table></div>";
    }
    
    print "</div>";

  ?>
           </div><!--/span-->
         <?php include "sidebar.php"; ?>
       </div><!--/span-->
      </div><!--/row-->
      <?php
      
     include "jsinc.php";
     
     print "</body></html>";
  

}


// TODO - This is inherited from do_notes.php
//         Needs to be trimmed down and updated

function getNoteRows($limit, $type) {

  GLOBAL $uid;
  GLOBAL $count;
  GLOBAL $MOBILE;
  
  $then = time() - (60*60*24*14); // Two weeks ago
  
   // Get all Pending Notifications
  $res = mysql_query("SELECT nid, req, type, note, bbid, status, cid, din FROM notes_bb WHERE din>$then AND uid=$uid $type ORDER BY nid DESC LIMIT $limit");
  //$count = mysql_num_rows($res);


  while($row = mysql_fetch_array($res)) {
   $nid = $row{'nid'};
   $req = $row{'req'};
   $type = $row{'type'};
   $note = $row{'note'};
   $bbid = $row{'bbid'};
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
      $note = "<a href='/person.php?target=$req'>$tnam</a> has sent you a friend request!";
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
      }
   } elseif($type == 2) {
      $note = "<a href='/person.php?target=$req'>$tnam</a> has accepted your friend request!";
   } else { $actionLine = ""; }

   if($bbid > 0) {
     $bi    = getGriddleInfo($bbid);
     $image = "$bbid-bb-latest.jpg";
     $pgid  = $bi{'gid'};
     $gi    = getGridInfo($pgid);
     $ptop  = $gi{'topic'};
     
     $imgSRV = shardImg($din);
     
     if($MOBILE) { $COL_LINK = "/do_colab.php"; } else { $COL_LINK = "/griddles.php?gid=$pgid"; }
     
     $IMGLINE = "<td align=left valign=top width=45><a id=comText-$bbid class=viewPost $comStyle href=/view.php?bbid=$bbid><img class='cropimgPro' src=$imgSRV/griddles/$image></a></td>";
     if($cid > 0) {
          $ci = getCommentInfo($cid);
          $message = $ci{'comment'};
          $message = stripslashes($message); 
          if(strlen($message) > 47) { $message = substr($message, 0, 47) . "..."; }
          $note = "<a href='/person.php?target=$req'>$tnam</a> commented <a style='color:#0088cc;' href=/griddles.php?gid=$pgid>$ptop</a> - $message <span style='font-size: xx-small;'>$when</span>";
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
   
   

   $content .= "<tr style='border-bottom:1pt solid #cdcecf;'>
                     <td align=left valign=top width=45><img class='cropimgPro' src=$imgSRV/thumb_profiles/$tuser?$rd></td>
                     <td align=left $COLSPAN valign=top><p style='max-width:300px; margin-top:3px; line-height:120%; font-size:small;'>$note</p><p style='line-height:110%; font-size:small;'>$actionLine</p></td>
                     $IMGLINE          
                  </tr>";
   
  
  }
  
  return $content;

}

