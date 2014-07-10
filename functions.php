<?php

function getUserInfo($uid) {
   $res = mysql_query("SELECT * FROM users WHERE uid=$uid LIMIT 1");
   return(mysql_fetch_array($res));
}
function getName($uid) {
   $res = mysql_query("SELECT name, username, posts, followers FROM users WHERE uid=$uid");
   $row = mysql_fetch_array($res);
   return $row;
}
function getMobile($uname) {
   $res = mysql_query("SELECT mobile FROM users WHERE username='$uname'");
   $row = mysql_fetch_array($res);
   return $row{'mobile'};
}
function getGridID($topic) {
   $res = mysql_querY("SELECT gid FROM griddles WHERE topic='$topic'");
   $row = mysql_fetch_array($res);
   return $row{'gid'};
}
function getGridInfo($gid) {
   $res = mysql_query("SELECT * FROM griddles WHERE gid=$gid LIMIT 1");
   return(mysql_fetch_array($res));
}
function getCommentInfo($cid) {
  $res = mysql_query("SELECT * FROM comments_bb WHERE cid=$cid");
  return mysql_fetch_array($res);
}
function getPostCommentInfo($cid) {
  $res = mysql_query("SELECT * FROM comments WHERE cid=$cid");
  return mysql_fetch_array($res);
}
function getTopic($gid) {
   $res = mysql_query("SELECT topic FROM griddles WHERE gid=$gid");
   $row = mysql_fetch_array($res);
   return $row{'topic'};
}
function getFriendSel($count, $uid) {
   $res = mysql_query("SELECT relations.uid, users.name FROM relations, users WHERE relations.friend=2 AND relations.target=$uid AND users.uid=relations.uid ORDER BY users.name LIMIT $count");
   while($row = mysql_fetch_array($res)) {
       $tuid = $row{'uid'};
       //$trow = getName($tuid);
       $name = $row{'name'};
       $return .= "<option value=$tuid>$name</option>\n";
   }
   return $return;
}
function getTrendingSel($count) {
      $res = mysql_query("SELECT gid, topic FROM griddles WHERE posts>=1 AND type=3 ORDER BY last_post DESC LIMIT $count");
     while($row = mysql_fetch_array($res)) {
         $topic = $row{'topic'};
         $gid = $row{'gid'};
         $return .= "<option>$topic</option>\n";
     }
     return $return;
}
function getSearchSeed($count) {
     $return = "[";
     GLOBAL $GRIDS;
     $res = mysql_query("SELECT gid, topic FROM griddles WHERE posts>=1 AND type=3 ORDER BY last_post DESC LIMIT $count");
     while($row = mysql_fetch_array($res)) {
         $topic = $row{'topic'};
         $gid = $row{'gid'};
         $return .= "\"$topic\",";
         $GRIDS .= "<li role=\"presentation\"><a role=\"menuitem\" tabindex=\"-1\" href=\"/m/grid_m.php?gid=$gid\">$topic</a></li>";
     }
     $return .= "\"\"]";
     return $return;
}
function getGriddleInfo($bbid) {
   $res = mysql_query("SELECT * FROM griddle_bb WHERE bbid=$bbid LIMIT 1");
   return(mysql_fetch_array($res));
}

function getPostInfo($pid) {
   $res = mysql_query("SELECT * FROM posts WHERE pid=$pid LIMIT 1");
   return(mysql_fetch_array($res));
}

function getUser($uname) {
  $res = mysql_query("SELECT uid FROM users WHERE username='$uname'");
  $row = mysql_fetch_array($res);
  return $row{'uid'};
}
function getFacebookInfo($uid) {
  $res = mysql_query("SELECT * FROM fblink WHERE uid=$uid");
  return (mysql_fetch_array($res));
}
function getGriddleMembership($pid) {
  $res = mysql_query("SELECT bbid FROM griddle_bb WHERE ppid LIKE '%,$pid,%' OR ppid LIKE '$pid,%' OR ppid LIKE '%,$pid' LIMIT 1");
  $row = mysql_fetch_array($res);
  return $row{'bbid'};
}

function checkMobile($mobile) {
   $res = mysql_query("SELECT uid FROM users WHERE mobile='$mobile'");
   $row = mysql_fetch_array($res);
   return $row{'uid'};
}

function gotNotes($uid) {
    $res = mysql_query("SELECT nid, req, type, note FROM notes_bb WHERE uid=$uid AND status=1 AND type>2 ORDER BY nid DESC LIMIT 100");
    $count = mysql_num_rows($res);
    return $count;
}
function gotColabs($uid) {
    $res = mysql_query("SELECT bbid FROM griddle_bb WHERE (uid=$uid OR colabs LIKE '%,$uid,%') AND status=0");
    return mysql_num_rows($res);
}
function gotFriends($uid) {
    $res = mysql_query("SELECT nid FROM notes_bb WHERE uid=$uid AND type<3 AND status=1");
    return mysql_num_rows($res);
}
function getNotificationSettings($uid) {
    $res = mysql_query("SELECT * FROM notification_settings WHERE uid=$uid");
    $row = mysql_fetch_array($res);
    return $row;
}
function didHot($uid, $bbid) {
    $res = mysql_querY("SELECT uid FROM hots_bb WHERE uid=$uid AND bbid=$bbid");
    $row = mysql_fetch_array($res);
    return $row{'uid'};
}
function didComm($uid, $pid) {
    $res = mysql_query("SELECT uid FROM comments WHERE uid=$uid and pid=$pid");
    $row = mysql_fetch_array($res);
    return $row{'uid'};
}   
function didHotp($uid, $pid, $bbid) {
    $res = mysql_querY("SELECT uid FROM hots WHERE uid=$uid AND pid=$pid AND bbid=$bbid");
    $row = mysql_fetch_array($res);
    return $row{'uid'};
}
function didHotG($uid, $bbid) {
    $res = mysql_query("SELECT uid FROM hots WHERE uid=$uid AND bbid=$bbid");
    $row = mysql_fetch_array($res);
    return $row{'uid'};
}
function didCommG($uid, $bbid) {
    $res = mysql_query("SELECT uid FROM comments_bb WHERE uid=$uid and bbid=$bbid");
    $row = mysql_fetch_array($res);
    return $row{'uid'};
}   
function getTrending($count, $mob) {

     $res = mysql_query("SELECT gid, topic FROM griddles WHERE posts>=1 AND type=3 ORDER BY last_post DESC LIMIT $count");
     while($row = mysql_fetch_array($res)) {
         $topic = $row{'topic'};
         $gid = $row{'gid'};
         $return .= "<li><a style='color: #055366;' href=\"/m/grid_m.php?gid=$gid\">$topic</a></li>\n";
         $mobreturn .= "$topic,";
     }

     if($mob == "yes") {
        return $mobreturn;
     } else {
        return $return;
     }

}


function isFriend($uid, $target) {
    $res = mysql_query("SELECT rid, friend FROM relations WHERE uid=$uid AND target=$target");
    $row = mysql_fetch_array($res);
    $rid = $row{'rid'};
    $friend = $row{'friend'};
   
    $class = 'relLink';
   
   if($friend == 0) {
     $fLine = "<p id='relLink$target'><a role='menuitem' tabindex='-1' class='$class' href=/do_rels.php?action=friend&target=$target&type=$ttype><span class='glyphicon glyphicon-plus'></span> Add Friend</a></p>";
   } elseif ($friend == 1) {
     $fLine = "<p id='relLink$target'><a role='menuitem' tabindex='-1' class='$class' href=/do_rels.php?action=unrequest&target=$target&type=$ttype><span class='glyphicon glyphicon-remove'></span> Friends Pending</a></p>";
   } elseif ($friend == 2) {
     $fLine = "<p id='relLink$target'><a role='menuitem' tabindex='-1' class='$class' href=/do_rels.php?action=unfriend&target=$target&type=$ttype><span class='glyphicon glyphicon-remove'></span> Remove Friend</a></p>";
   } else {
     $fLine = "<p id='relLink$target'><a role='menuitem' tabindex='-1' class='$class' href=/do_rels.php?action=friend&target=$target&type=$ttype><span class='glyphicon glyphicon-plus'></span> Add Friend</a></p>";
   }

   if($target == $uid) {
        $fLine = "";
        $foLine = "";
   }

  return $fLine;

}



function getFriendRows($count, $uid) {

   global $MOBILE;

   $COL = "COL2";

   $res = mysql_query("SELECT relations.uid, users.name FROM relations, users WHERE relations.friend=2 AND relations.target=$uid AND users.uid=relations.uid ORDER BY users.name LIMIT $count");
   while($row = mysql_fetch_array($res)) {
       $tuid = $row{'uid'};
       $trow = getName($tuid);
       $user = $trow{'username'};
       $name = $trow{'name'};
       $posts = $trow{'posts'};
       $folls = $trow{'followers'};
       $imgSRV = shardImg($tuid);
       
       
       if(!$MOBILE) {
         if($COL=="COL1") {
            $COL="COL2";
         } elseif($COL=="COL2") {
            $COL="COL1";
         }
       } else { $COL="COL1"; }
       
          
       $return{$COL} .= "<tr style='border-bottom:1pt solid #cdcecf;'><td align=left width=60px><a class=userButton id=userButton-$tuid href='#'><img class=cropimgProLG src=$imgSRV/thumb_profiles/$user></a></td>
                       <td align=left><span style='font-size: medium;'><a href='/person.php?target=$tuid'>$name</a></span><br><span style='font-size: xx-small;'> +$posts Posts</span></td>
                       <td align=left><a style='font-size: xx-small;' href=/do_friends.php?action=unfriend&target=$tuid>Remove</a></td></tr>";
   }

   return $return;

}

function getSearchFriendRows($count, $search) {

   global $MOBILE;
   $muid = getUser($_SESSION['user']);
   
   $COL = "COL2";

   //$res = mysql_query("SELECT relations.uid, users.name FROM relations, users WHERE relations.friend=2 AND relations.target=$uid AND users.uid=relations.uid ORDER BY users.name LIMIT $count");
   $res = mysql_query("SELECT uid, name, username, posts, followers FROM users WHERE username LIKE '%$search%' OR name LIKE '%$search%' ORDER BY name LIMIT $count");
   
   while($row = mysql_fetch_array($res)) {
       $tuid = $row{'uid'};
       $user = $row{'username'};
       $name = $row{'name'};
       $posts = $row{'posts'};
       $folls = $row{'followers'};
       $imgSRV = shardImg($tuid);
   
       $fLine = isFriend($muid, $tuid);    
       
       if(!$MOBILE) {
         if($COL=="COL1") {
            $COL="COL2";
         } elseif($COL=="COL2") {
            $COL="COL1";
         }
       } else { $COL="COL1"; }
       
          
       $return{$COL} .= "<tr style='border-bottom:1pt solid #cdcecf;'><td align=left width=60px><a class=userButton id=userButton-$tuid href='#'><img class=cropimgProLG src=$imgSRV/thumb_profiles/$user></a></td>
                       <td align=left><span style='font-size: medium;'><a href='/person.php?target=$tuid'>$name</a></span><br><span style='font-size: xx-small;'> +$posts Posts</span></td>
                       <td align=left>$fLine</td></tr>";
   }

   return $return;

}


function shardImg($image) {
   	$last = substr($image, -1);
   	if(($last>-1) && ($last<4)) {
   	   $dom = "1";
   	}if(($last>3) && ($last<7)) {
   	   $dom = "2";
   	}if(($last>6) && ($last<10)) {
   	   $dom = "3";
   	}   	
   	$shardSRV = "http://grd-images-0$dom.griddle.com";
   	return $shardSRV;
}

function getFBShareProfiles($bbid) {

   $res = mysql_query("SELECT uid FROM fb_shares WHERE bbid=$bbid");


   while($row = mysql_fetch_array($res)) {
      $cuid = $row{'uid'};
      if($cuid) { 
         $cui = getUserInfo($cuid);
         $cn  = $cui{'name'};
         $cu  = $cui{'username'};
         if($DONE[$cn] != 1) {
            $byline .= "$cn, ";
            $proline .= "<img class='cropimgProTiny' src='$imgSRV/thumb_profiles/$cu'>&nbsp;";
            $procount++;
            $DONE[$cn] = 1;
         }
      }
   }

   return $proline;

}

function generateGrid($gid, $count, $column) {

   if(!$column) {
      $column = 'col-lg-6';
   }

   $res = mysql_query("SELECT bbid FROM griddle_bb WHERE gid=$gid AND status=1 ORDER BY din DESC LIMIT $count");

   while($row = mysql_fetch_array($res)) {
      $bbid = $row{'bbid'};
      $OUT .= "<div class='row griddleRow'>\n";
      $OUT .= getGriddleBlock($bbid, $column);
      $next = mysql_fetch_array($res);
      $bbid = $next{'bbid'};
      if($bbid) {
         $OUT .= getGriddleBlock($bbid, $column);
      }
      $OUT .= "</div><!-- /griddleRow -->";
   }

   return $OUT;

}

function wrapName($target, $name) {

    $uid = getUser($_SESSION['user']);
    
    $res = mysql_query("SELECT rid, friend FROM relations WHERE uid=$uid AND target=$target");
    $row = mysql_fetch_array($res);
    $rid = $row{'rid'};
    $friend = $row{'friend'};
    
    if($friend == 0) {
      $fLine = "<li id=relLink$target role='presentation'><a role='menuitem' tabindex='-1' class=relLink href=/do_rels.php?action=friend&target=$target><span class='glyphicon glyphicon-plus'></span> Add Friend</a></li>";
    } elseif ($friend == 1) {
      $fLine = "<li id=relLink$target role='presentation'><a role='menuitem' tabindex='-1' class=relLink href=/do_rels.php?action=unrequest&target=$target><span class='glyphicon glyphicon-remove'></span> Friends Pending</a></li>";
    } elseif ($friend == 2) {
      $fLine = "<li id=relLink$target role='presentation'><a role='menuitem' tabindex='-1' class=relLink href=/do_rels.php?action=unfriend&target=$target><span class='glyphicon glyphicon-remove'></span> Remove Friend</a></li>";
    } else {
      $fLine = "<li id=relLink$target role='presentation'><a role='menuitem' tabindex='-1' class=relLink href=/do_rels.php?action=friend&target=$target><span class='glyphicon glyphicon-plus'></span> Add Friend</a></li>";
    }

    if($target==$uid) { $fLine = ""; }

    $OUT = "<div class='dropdown'>
              <a class='commLine' id='dLabel-$uid' data-toggle='dropdown' href='#'>$name</a>
               <ul class='dropdown-menu' role='menu' aria-labelledby='dLabel'>
                 $fLine
               </ul>
            </div>";
    return $OUT;    

}

function wrapGriddleSettings($bbid) {

      $uid = getUser($_SESSION['user']);

      $bi = getGriddleInfo($bbid);
      if($bi{'uid'}!=$uid) {
          return "";
      }
      $OUT = "<div class='dropdown'>
              <a class='griddleSettings btn btn-xs' style='font-size: 7px;' id='bbset-$bbid' data-toggle='dropdown' href='#'><span class='glyphicon glyphicon-lock'></span></a>
               <ul class='dropdown-menu' role='menu' aria-labelledby='bbset-$bbid'>
                 <li role='presentation'><a class='gRemove' id='gr-$bbid' href='/do_remove.php?bbid=$bbid'><span class='glyphicon glyphicon-remove'></span> Remove</a></li>
               </ul>
            </div>";
      return $OUT;


}


function generateFeed($count, $uid, $type) {

   if($uid) {
      $WHERE = "AND (uid=$uid OR colabs LIKE '%,$uid,%')";      
   }
   
   $SQL   = "SELECT * FROM griddle_bb WHERE status=1 $WHERE ORDER BY din DESC LIMIT $count";
   
   if($type == "friends") {
      $SQL = "SELECT griddle_bb.bbid, griddle_bb.uid, relations.uid, users.name FROM relations, users, griddle_bb WHERE griddle_bb.status=1 AND relations.friend=2 AND relations.target=$uid AND users.uid=relations.uid AND griddle_bb.uid=users.uid ORDER BY griddle_bb.din DESC LIMIT $count";
   }
   
   $res = mysql_query("$SQL");

   while($row = mysql_fetch_array($res)) {
      $bbid = $row{'bbid'};
      //$next = mysql_fetch_array($res);
      //$gid  = $next{'gid'};
      
      // TODO - Hell no, don't use style tags
      $OUT .= "<div class='row griddleRow' style='margin-top: 0px; margin-bottom: 0px;'>\n";
      
      $format = rand(1,2);
      if($format==1) {
         $OUT .= getGriddleBlock($bbid, 'col-6 col-sm-6 col-lg-6');
         //$OUT .= getPostPair($gid);
      } else {
         //$OUT .= getPostPair($gid);
         $OUT .= getGriddleBlock($bbid, 'col-6 col-sm-6 col-lg-6');
      }
     
 
      $OUT .= "</div><!-- /griddleRow -->";

      // $OUT .= generateHiddenFeed(10, $bbid);

   }
   
   
   
   return $OUT;

}

function generateHiddenFeed($count, $target, $type) {

   $SQL = "SELECT bbid FROM griddle_bb WHERE status=1 ORDER BY RAND() LIMIT $count";
   
   $res = mysql_query("$SQL");

   while($row = mysql_fetch_array($res)) {
      $bbid = $row{'bbid'};
      //$next = mysql_fetch_array($res);
      //$gid  = $next{'gid'};
      
      // TODO - Hell no, don't use style tags
      $OUT .= "<div class='row griddleRow hideThis hiddenFor$target' style='margin-top: 0px; margin-bottom: 0px;'>\n";
      
      $format = rand(1,2);
      if($format==1) {
         $OUT .= getGriddleBlock($bbid, 'col-6 col-sm-6 col-lg-6', 'hideit');
         //$OUT .= getPostPair($gid);
      } else {
         //$OUT .= getPostPair($gid);
         $OUT .= getGriddleBlock($bbid, 'col-6 col-sm-6 col-lg-6', 'hideit');
      }
     
 
      $OUT .= "</div><!-- /griddleRow -->";

   }
   return $OUT;

}


function getGriddlePosts($bbid) {

    GLOBAL $MOBILE;
    GLOBAL $JAVA;
   
   $bi = getGriddleInfo($bbid);
   $PLIST = explode(",", $bi{'ppid'});
   $JAVA = "";
   
   $UID = getUser($_SESSION['user']);
   
   if(didHotG($UID, $bbid)) {
      $DEFAULT = " <span class='glyphicon glyphicon-lock'></span>";
   } else {
      $DEFAULT = " <span class='glyphicon glyphicon-hand-up'></span>";
   }
   
   foreach ($PLIST as $pid) {
      $row = getPostInfo($pid);
      $uid  = $row{'uid'};
      $img  = $row{'images'};
      $hots = $row{'hots'};

      //$mess = $row{'message'};

           
      if(strlen($mess) > 60) {
        $mess = substr($mess, 0, 55) . "...";
      }
      
      $ui = getUserInfo($uid);
      $realname = $ui{'name'};
      $uname    = $ui{'username'};
      
      $realname = wrapName($uid, $realname);
      
      $imgSRV = shardImg($img);
      # Trigger the slide show 
      if(!$JAVA) {
         $JID = "img$bbid";
         $JAVA = "$( '#img$bbid' ).trigger( 'click' );";
      } else {
         $JID = "";
      }
      
      if(didHotp(getUser($_SESSION['user']), $pid, $bbid)) {
          $hots_img = " <span class='glyphicon glyphicon-heart'></span>";
      } else {
          $hots_img = $DEFAULT;         
      }
      
      
      if($MOBILE) { $thumb_dir = "mid_images"; $full_dir = "mid_images"; } else { $thumb_dir = "mid_images"; $full_dir = "full_images"; }      
      if($MOBILE) { $HSIZE = "h4"; } else { $HSIZE = "h2"; }
      
      $OUT .= "<div class='col-6 col-sm-6 col-lg-3'>\n";
      
      
      $OUT .= "<!-- <div class='well well-sm narrowTop'> -->
                <div style='
              <!-- <br><a id='$JID' href='$imgSRV/$full_dir/$img' class='fresco' data-fresco-group='griddle'><img class='cropimgLarge' src='$imgSRV/$thumb_dir/$img'></a>
                <table class='tablePro' cellpadding=3 width=100%>
                <tr>
                  <td valign=top class='cropimgProTiny'><a href=#><img class='cropimgProTiny' src='$imgSRV/thumb_profiles/$uname'></a></td>
                  <td valign=top><table width=100%><tr><td><a href=#>$realname</a></td><td align=right><a href='/do_hotp.php?pid=$pid&bbid=$bbid&vote=up' type='button' id='aHotp$pid' class='btn btn-primary btn-xs upHotp'>$hots $hots_img</a></td></tr></table>
                </table>
              </div>";
      $OUT .="</div><!--/span-->\n";
              
  }           
  
    
  return $OUT;


}


function getPostPair($gid) {
 
   GLOBAL $MOBILE;
   GLOBAL $TABLET;
   
   $OUT = "<div class='col-6 col-sm-6 col-lg-4'>\n";
   
   $res = mysql_query("SELECT * FROM posts WHERE gid=$gid AND status=1 ORDER BY RAND() LIMIT 4");
   while($row = mysql_fetch_array($res)) {
      $uid  = $row{'uid'};
      $img  = $row{'images'};

      $mess = $row{'message'};
      $pid  = $row{'pid'};
      $bbid = getGriddleMembership($pid);

      $bi   = getGriddleInfo($bbid);
      $hots = $bi{'hots'};
      $coms = $bi{'comments'};
      $fbs  = $bi{'fbshare'};
      $tws  = $bi{'twshare'};
      
      
      if($bi{'status'} == 0) {
         continue;
      } else {
         $COUNT++;
      }
      
      if($COUNT > 2) {
         continue;
      }
      
      if(!$hots) { $hots="0"; }
      if(!$coms) { $coms="0"; }
      if(!$tws)  { $tws="0"; }
      if(!$fbs)  { $fbs="0"; }     
      if(strlen($mess) > 60) {
        $mess = substr($mess, 0, 55) . "...";
      }
      
      $ui = getUserInfo($uid);
      $realname = $ui{'name'};
      $uname    = $ui{'username'};
      $gi = getGridInfo($gid);
      $topic = $gi{'topic'};
      
      if(didHot(getUser($_SESSION['user']), $bbid)) {
          $hots_img = " <span class='glyphicon glyphicon-heart'></span>";
      } else {
          $hots_img = " <span class='glyphicon glyphicon-hand-up'></span>";         
      }
      
      $encode = urlencode("http://www.griddle.com/view.php?bbid=$bbid");
      $twurl = "https://twitter.com/intent/tweet?url=$encode";
      
      $realname = wrapName($uid, $realname);
      
      $imgSRV = shardImg($img);
      
      $CROPCLASS='cropimgFeed'; 
      
      if($MOBILE) { $thumb_dir = "griddle_images"; } else { $thumb_dir = "mid_images"; }      
      if($MOBILE) { $HSIZE = "h4"; $COMMDIV = "#commHeader"; $POSTDIV = "&lightbox=yes";  } else { $HSIZE = "h2"; }
      if($TABLET) { $HSIZE = "h4"; $COMMDIV = "#commHeader"; $POSTDIV = "&lightbox=yes"; $CROPCLASS='cropimgFeedTablet'; }
      
      $OUT .= "<div class='well well-sm narrowTop'>
      <$HSIZE><a href=/griddles.php?gid=$gid>$topic</a></$HSIZE>
              <a href='/view.php?bbid=$bbid$POSTDIV'><div class='$CROPCLASS' style='background-image: url(\"$imgSRV/$thumb_dir/$img\");'></div></a>
              <table class='tablePro' cellpadding=3 width=100%>
                <tr>
                  <td valign=top class='cropimgProTiny'><a href='/person.php?target=$uid'><img class='cropimgProTiny' src='$imgSRV/thumb_profiles/$uname'></a></td>
                  <td valign=top><table><tr><td><a href=#>$realname</a></td></tr><tr><td colspan=2><span class='commLine'>$mess</span></td></tr>
                  <td colspan=2><a href='/do_hot.php?bbid=$bbid&vote=up' type='button' id='aHot$bbid' class='btn btn-primary btn-xs upHot'>$hots $hots_img</span></a>&nbsp; &nbsp;
                      <a href='/view.php?bbid=$bbid$COMMDIV' type='button' id='aComm$bbid' class='btn btn-primary btn-xs'>$coms <span class='glyphicon glyphicon-comment'></span></a>&nbsp; &nbsp;
              <a id='aFB$bbid' href='/fb_share.php?bbid=$bbid' type='button' class='doModal btn btn-primary btn-xs'>$fbs <i class='fa fa-facebook-square'></i></a>&nbsp; &nbsp;
              <a id='aTW$bbid' tw_upload='/tw_upload.php?bbid=$bbid' href='$twurl' type='button' class='TWITTER btn btn-primary btn-xs'>$tws <i class='fa fa-twitter-square'></i></a></td></tr></table>
                </tr>
              </table></div>";
              
  }           
    
  $OUT .="</div><!--/span-->\n";
  
  return $OUT;

}

function getGriddleBlock($bbid, $columnsize, $hideit) {

   GLOBAL $imgSRV;
   GLOBAL $MOBILE;
   GLOBAL $TABLET;
   
   $row = getGriddleInfo($bbid);
   
   $gid  = $row{'gid'};
   $bbid = $row{'bbid'};
   $uid  = $row{'uid'};
   $col  = $row{'colabs'};
   $pids = $row{'ppid'};
   $hots = $row{'hots'};
   $coms = $row{'comments'};
   $din  = $row{'din'};
   $fbs  = $row{'fbshare'};
   $tws  = $row{'twshare'};
   
   $PLIST = explode(",", $pids);
   $pi = getPostInfo($PLIST[2]);
   $mess = $pi{'message'};
   $pimg = $pi{'images'};
   
   if(strlen($mess) > 60) {
      $mess = substr($mess, 0, 55) . "...";
   }
   
   $when = secondsToTime(time() - $din);
   
   $gi = getGridInfo($gid);
   $topic = $gi{'topic'};
   
   $ui = getUserInfo($uid);
   $realname = $ui{'name'};
   $uname    = $ui{'username'};
   
   $byline = "<span class='byLine'>by: ";
  
   $txcode = urlencode("Check out $topic on Griddle ");

   $encode = urlencode("http://www.griddle.com/view.php?bbid=$bbid");
   $twurl = "https://twitter.com/intent/tweet?url=$encode&text=$txcode";

   $col = ",$uid," . $col;

   // TODO - move this to an offline calculation
   $ULIST = explode(",", $col);
   foreach ($ULIST as $cuid) {
      if($cuid) { 
         $cui = getUserInfo($cuid);
         $cn  = $cui{'name'};
         $cu  = $cui{'username'};
         if($DONE[$cn] != 1) {
            $byline .= "$cn, ";
            $proline .= "<a href='/person.php?target=$cuid'><img class='cropimgProTiny' src='$imgSRV/thumb_profiles/$cu'></a>&nbsp;";
            $procount++;
            $DONE[$cn] = 1;
         }
      }
   }
   $bytmp  = rtrim($byline, ", ");
   $byline = $bytmp . "</span><span class='byLine pull-right narrowRight slightDrop'>$when</span>";
   
   if($procount < 2) { $proline = ""; }
   
   $NCOLS = explode(" ", $realname);
   $rname = $NCOLS[0];
   $realname = wrapName($uid, $realname);
   $gcontrol = wrapGriddleSettings($bbid);
   
   if(didHot(getUser($_SESSION['user']), $bbid)) {
          $hots_img = " <span class='glyphicon glyphicon-heart'></span>";
   } else {
          $hots_img = " <span class='glyphicon glyphicon-hand-up'></span>";         
   }
   
   $imgsample = "<span class='byLine pull-right'>&nbsp;<a class='showGriddle' bbid='$bbid'><img class='cropimgProTiny' src='$imgSRV/thumb_images/$pimg'></a>&nbsp;$when</span>";
   
   
   if($MOBILE) { $HSIZE = "h4"; $COMMDIV = "#commHeader"; $POSTDIV = "&lightbox=yes"; } else { $HSIZE = "h2"; }
   if($TABLET) { $POSTDIV="&lightbox=yes"; }
   
   if($hideit) { 
         $extra ="&nbsp;&nbsp;<span class='byLine'>by: $rname</span>";
         $hidethis = "hideThis";
         
    } else { $showRelated = "showRelated"; }
   // Add  class 'hideThis' to id='hide$bbid' in order to backout 2014-04-21   
  $OUT .="
   <div class='$columnsize widePicture'>
        <div class='well well-sm narrowTop widePicture'>
            <$HSIZE><a id='related$bbid' bbid=$bbid class='$showRelated' href='/'>$topic</a>$extra $imgsample</$HSIZE>
            <div class='$hidethis' id='hide$bbid'>
              <a href=/view.php?bbid=$bbid$POSTDIV><img class='feedImg' src='$imgSRV/griddles/$bbid-bb-latest.jpg'></a><br>$byline
              <table class='tablePro' cellpadding=5>
                <tr>
                  <td valign=top><a href='/person.php?target=$uid'><img class='cropimgPro' src='$imgSRV/thumb_profiles/$uname'></a></td>
                  <td valign=top><table><tr><td>$realname</td><td align=left>$gcontrol</td></tr><tr><td colspan=2><span class='commLine'>$mess</span></td></tr>
                  <td colspan=2><a href='/do_hot.php?bbid=$bbid&vote=up' type='button' id='aHot$bbid' class='btn btn-primary btn-xs upHot'>$hots $hots_img</span></a>&nbsp; &nbsp;
                      <a href='/view.php?bbid=$bbid$COMMDIV' type='button' id='aComm$bbid' class='btn btn-primary btn-xs'>$coms <span class='glyphicon glyphicon-comment'></span></a>&nbsp; &nbsp;
              <a id='aFB$bbid' href='/fb_share.php?bbid=$bbid' type='button' class='doModal btn btn-primary btn-xs'>$fbs <i class='fa fa-facebook-square'></i></a>&nbsp; &nbsp;
              <a id='aTW$bbid' tw_upload='/tw_upload.php?bbid=$bbid' href='$twurl' type='button' class='TWITTER btn btn-primary btn-xs'>$tws <i class='fa fa-twitter-square'></i></a></td></tr></table>
                </tr>
              </table>
            </div><!--hideThis-->
         </div><!--/griddleWell-->     
    </div><!--/span-->
    ";       
    
    $OUT2 .="
   <div class='$columnsize widePicture'>
        <a href=/><div style='background-image: url(\'$imgSRV/mid_images/$pimg\') center no-repeat;'>Blah!</div></a>
        <!-- <div class='well well-sm narrowTop widePicture'> -->
     
       <!--  </div>/griddleWell -->     
    </div><!--/span-->
    ";      
    
    
   return $OUT;

}


function getCommentsForGriddle($bbid, $user, $LIMIT, $ptype) {

  GLOBAL $imgSRV;
  
  $res = mysql_query("SELECT uid, din, comment FROM comments_bb WHERE bbid=$bbid ORDER BY din DESC LIMIT $LIMIT");

  $count = 0;
  
  while($prow = mysql_fetch_array($res)) {
 
     $tuid = $prow{'uid'};
     $comm = stripslashes($prow{'comment'});
   
     $ures = mysql_query("SELECT name, username FROM users WHERE uid=$tuid");
     $urow = mysql_fetch_array($ures);
     $tuser = $urow{'username'}; 
     $tname = $urow{'name'};
     $ago = time() - $prow{'din'};
     $when = secondsToTime($ago);

     $tname = wrapName($tuid, $tname);

     $comrow[$count] = "
<table border=0 width=100% cellpadding=3>
   <tr><td align=left valign=top width=45px><img class='cropimgPro' src=$imgSRV/thumb_profiles/$tuser?$rd></a></td>
       <td align=left valign=top>$tname <p style='font-size: small;'>$comm<br><span style='font-size: x-small;'>$when ago</span></p></td>
   </tr>
</table>\n";
     $count++;

  }

  krsort($comrow);
  foreach ($comrow as $row) {
      $return .= $row;
  }
      

  return "<div class='well well-sm'>$return</div>";

}

function getCommentsForBBID($user, $bbid, $LIMIT) {

  GLOBAL $imgSRV;
  
  $res = mysql_query("SELECT uid, din, comment FROM comments_bb WHERE bbid=$bbid ORDER BY din DESC LIMIT $LIMIT");

  $count = 0;
  
  while($prow = mysql_fetch_array($res)) {
 
     $tuid = $prow{'uid'};
     $comm = stripslashes($prow{'comment'});
   
     $ures = mysql_query("SELECT name, username FROM users WHERE uid=$tuid");
     $urow = mysql_fetch_array($ures);
     $tuser = $urow{'username'}; 
     $tname = $urow{'name'};
     $ago = time() - $prow{'din'};
     $when = secondsToTime($ago);

     $pimg = "$imgSRV/thumb_profiles/$tuser";

     $JSON .= "{ \"n\": \"$tname\",
               \"un\": \"$tuser\",
               \"pimg\": \"$pimg\",
               \"comment\": \"$comm\",
               \"when\": \"$when\" },\n";
         
  }

  $JSON = rtrim($JSON, ",\n");

  return $JSON;

}

function getCommentsForPID($user, $pid, $LIMIT) {

  GLOBAL $imgSRV;
  
  $res = mysql_query("SELECT uid, din, comment FROM comments WHERE pid=$pid ORDER BY din DESC LIMIT $LIMIT");

  $count = 0;
  
  while($prow = mysql_fetch_array($res)) {
 
     $tuid = $prow{'uid'};
     $comm = stripslashes($prow{'comment'});
   
     $ures = mysql_query("SELECT name, username FROM users WHERE uid=$tuid");
     $urow = mysql_fetch_array($ures);
     $tuser = $urow{'username'}; 
     $tname = $urow{'name'};
     $ago = time() - $prow{'din'};
     $when = secondsToTime($ago);

     $pimg = "$imgSRV/thumb_profiles/$tuser";

     $JSON .= "{ \"n\": \"$tname\",
               \"un\": \"$tuser\",
               \"pimg\": \"$pimg\",
               \"comment\": \"$comm\",
               \"when\": \"$when\" },\n";
         
  }

  $JSON = rtrim($JSON, ",\n");



  return $JSON;

}



function getFormForGriddle($bbid, $user, $ptype) {

  GLOBAL $imgSRV;
  GLOBAL $MOBILE;
  
  if($MOBILE) { $SPACER = "<br><br><br>";  }
  
  $comrow = getCommentsForGriddle($bbid, $user, "0, 5");

  $res = mysql_query("SELECT uid FROM comments_bb WHERE bbid=$bbid");
  $num = mysql_num_rows($res);
  if($num > 5) {
     $MORE = "<a href='/do_image.php?action=more&bbid=$bbid' elmAppend='#commDiv' class='doMoreTop' style='font-size: xx-small;'>See Earlier Comments...</a>";
  }

  if($comrow == "<div class='well well-sm'></div>") { $comrow = "<p class='commLine'>Be the first to comment.</p>"; }

  $return = "<div id='commHeader'></div>$SPACER<h4>Comments:</h4> <p class='commLine'>$MORE</p><div id='commDiv'>$comrow</div><table border=0 width=100%><tr><td align=left valign=top>
  <td align=left>
  <form name='commForm' id='commForm' method=post action=/do_comms.php>";
  
  if($user != "") { $return .= "
     <input type=hidden name=bbid value=$bbid>
     <textarea name='comment' class='commForm form-control' placeholder='Write Something...' FID='#commForm' BBID='$bbid' id='commentForm' rows=2></textarea><br>
     <button id='commFormSubmit' FID='#commForm' PID='$bbid' class='commFormSubmit btn btn-sm btn-primary'>Post</button>";
  }

  $return .= "</form></td></tr></table>";
  
  return $return;

}

function addCommentToGriddle($user, $bbid, $comm) {

   $comm = addslashes($comm);
   $r = time();

   $uid = getUser($user);

   $res = mysql_query("SELECT gid, comments FROM griddle_bb WHERE bbid=$bbid");
   $row = mysql_fetch_array($res);
   $cCount = $row{'comments'} + 1;
   $gid = $row{'gid'};

   $res = mysql_query("INSERT INTO comments_bb VALUES(DEFAULT, $uid, $gid, $bbid, $r, '$comm')");
   $res = mysql_query("UPDATE griddle_bb SET comments=$cCount WHERE bbid=$bbid");

   $res = mysql_query("INSERT INTO triggers_bb VALUES(DEFAULT, $gid, $bbid, $uid, 2, $r, 0, 0, 0)");

   return $cCount;

}


function addCommentToPost($user, $pid, $comm) {

   print "into addCommentToPost\n";

   $comment = addslashes($comm);
   $r = time();

   $uid = getUser($user);

   $res = mysql_query("SELECT gid, comments FROM posts WHERE pid=$pid");
   $row = mysql_fetch_array($res);
   $cCount = $row{'comments'} + 1;
   $gid = $row{'gid'};

   print "INSERT INTO comments VALUES(DEFAULT, $uid, $gid, $pid, $r, '$comment')<br><br>";

   $res = mysql_query("INSERT INTO comments VALUES(DEFAULT, $uid, $gid, $pid, $r, '$comment')");
   $res = mysql_query("UPDATE posts SET comments=$cCount WHERE pid=$pid");

   $res = mysql_query("INSERT INTO triggers VALUES(DEFAULT, $gid, $pid, $uid, 2, $r, 0, 0, 0)");

   return $cCount;

}

function secondsToTime($inputSeconds) {

    $ago = $inputSeconds;
    $secondsInAMinute = 60;
    $secondsInAnHour  = 60 * $secondsInAMinute;
    $secondsInADay    = 24 * $secondsInAnHour;

    // extract days
    $days = floor($inputSeconds / $secondsInADay);

    // extract hours
    $hourSeconds = $inputSeconds % $secondsInADay;
    $hours = floor($hourSeconds / $secondsInAnHour);

    // extract minutes
    $minuteSeconds = $hourSeconds % $secondsInAnHour;
    $minutes = floor($minuteSeconds / $secondsInAMinute);

    // extract the remaining seconds
    $remainingSeconds = $minuteSeconds % $secondsInAMinute;
    $seconds = ceil($remainingSeconds);

    // return the final array
    $obj = array(
        'd' => (int) $days,
        'h' => (int) $hours,
        'm' => (int) $minutes,
        's' => (int) $seconds,
    );
    
     if($ago > 3600 && $ago < 86400) {
         $line = $obj{'h'} . "h";
      } else if ( $ago < 3600 ) {
        $line = $obj{'m'} . "m"; 
      } else if ( $ago > 86400) {
        $line = $obj{'d'} . "d";
      }

    return $line;

}

function randString($length, $charset='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789')
{
    $str = '';
    $count = strlen($charset);
    while ($length--) {
        $str .= $charset[mt_rand(0, $count-1)];
    }
    return $str;
}

function doMMSInform($colabs, $bbid) {

   $uid = getUser($_SESSION['user']);
   $ui  = getUserInfo($uid);
   $urn = $ui{'name'};
   
   $ULIST = explode(",", $colabs);
   foreach ($ULIST as $cuid) {
      if($cuid) {
         if($cuid!=$uid) { 
            if($DONE[$cuid] != 1) {
                 $res = mysql_query("INSERT INTO mms_inform VALUES(DEFAULT, $cuid, '$urn wants your help to make a Griddle! http://dev01.griddle.com/do_colabs.php', 0)");  
                 $DONE[$cuid] = 1;
            }
         }
      }
   }


}
function validateICode($icode, $uid) {
   $res = mysql_query("SELECT iid, allowed, used FROM invite_code WHERE code='$icode'");
   $row = mysql_fetch_array($res);
   
   if(!$row{'iid'}) { return 0; }
   if($row{'used'} == $row{'allowed'}) { return 0; }
   $iid = $row{'iid'};
   
   $res = mysql_query("UPDATE invite_code SET used=used+1 WHERE iid=$iid");
   
   $now = time();
   $res = mysql_query("INSERT INTO code_redeems VALUES($iid, $uid, $now)");
   
   return 1;

}


function apiAuth($user, $pass) {

   $res = mysql_query("SELECT password FROM users WHERE username='$user'");
   $pw_row = mysql_fetch_array($res);
   $pw_crypt = $pw_row{'password'};

   $attempt = md5($pass);

   if($attempt == $pw_crypt) { return 1; } else { return 0; }

}

function tileGridRandom($uid, $limit, $type, $stype) {

   

   if($stype == "pending") {
       $poststatus = "2";
   } else {
       $poststatus = "1";
   }

   $styles = array("w2", "w3", "w4", "h2", "h3", "h4");

   //$res = mysql_query("SELECT uid, pid, images, hots, comments, status FROM posts WHERE $WHERE AND status=$poststatus ORDER BY din DESC LIMIT $limit"); 
   
   if($type == "friends") {
      $SQL = "SELECT DISTINCT(griddle_bb.bbid), griddle_bb.hots, griddle_bb.comments, griddle_bb.uid, relations.uid, users.name, griddle_bb.gid 
                  FROM relations, users, griddle_bb 
                  WHERE griddle_bb.status=1 AND 
                  (griddle_bb.uid=$uid OR (relations.friend=2 AND relations.target=$uid)) 
                  AND users.uid=relations.uid AND griddle_bb.uid=users.uid 
                  ORDER BY griddle_bb.din DESC LIMIT $limit";
   } elseif($type == "world") {
      $SQL = "SELECT bbid, hots, comments FROM griddle_bb WHERE status=1 ORDER BY din DESC LIMIT $limit";
   } elseif($type == "person") {
      $SQL = "SELECT bbid, hots, comments FROM griddle_bb WHERE status=1 AND uid=$uid ORDER BY din DESC LIMIT $limit";
   }              
   $res = mysql_query($SQL);

   while($grow = mysql_fetch_array($res)) {
   
     $bbid  = $grow{'bbid'};
     $hots  = $grow{'hots'};
     $bbi   = getGriddleInfo($bbid);
     $PPIDS = $bbi{'ppid'};
     $PPIDS = rtrim($PPIDS, ","); 
     $PLIST = explode(",", $PPIDS);
   
     $maxr  = count($PLIST) -1;
     $picr  = rand(0, $maxr);
     $pid   = $PLIST[$picr];
     $gid   = $row{'gid'};
   
     $row   = getPostInfo($pid);
  
     $images = $row{'images'};
     
     $coms = $row{'comments'};
     $puid = $row{'uid'};
     $ui = getName($puid);
     $puser = $ui{'username'};
     $gi = getGridInfo($gid);
     $guid = $gi{'uid'};
     $status = $row{'status'};
     $imgSRV = shardImg($images);
     $r = rand(0, 10);

      if ($coms == 1) {
           $comText = "$coms";
        } elseif ($coms > 1) {
           $comText = "$coms";
        } else {
           $comText = "0";
        }
        $comStyle = "style=\"font-weight:bold; font-size: xx-small; color: white;\"";

     if(didHot($uid, $pid)) {
        $hotText = " $hots";
        $style = "style=\"font-weight:bold; font-size: xx-small; color: red;\"";
     } else {
        $hotText = " $hots";
        $style = "style=\"font-weight:bold; font-size: xx-small; color: white;\"";
     }

     if($r > 6) {
        $rand = "h4";
        $imgfolder = "mid";
     } else {
        $rand = "h4";
        $imgfolder = "mid";
     }
     
     $REMOVE = "";
     
     if($guid == $uid) {
        if($status == 2) {
           $REMOVE  = "<li role=\"presentation\"><a class='approveButton' role=\"menuitem\" tabindex=\"-1\" href=\"/m/do_manage.php?action=allow&pid=$pid&gid=$gid\">Approve</a></li>";
        }
        $REMOVE .= "<li role=\"presentation\"><a class='removeButton' role=\"menuitem\" tabindex=\"-1\" href=\"/m/do_remove.php?pid=$pid\">Remove</a></li>";
        if(checkBlock($puid, $gid)) {
          $BLOCK = "<li role=\"presentation\"><a class='blockButton' role=\"menuitem\" tabindex=\"-1\" href=\"/m/do_block.php?action=unblock&pid=$pid\">Unblock</a></li>";
        } else {
          $BLOCK = "<li role=\"presentation\"><a class='blockButton' role=\"menuitem\" tabindex=\"-1\" href=\"/m/do_block.php?action=block&pid=$pid\">Block</a></li>";
        }
      } else { $REMOVE = "";}

     if(!$REMOVE && ($uid == $puid)) {
        $REMOVE = "<li role=\"presentation\"><a class='removeButton' role=\"menuitem\" tabindex=\"-1\" href=\"/m/do_remove.php?pid=$pid\">Remove</a></li>";
     }

     if(didHot(getUser($_SESSION['user']), $bbid)) {
          $hots_img = " <span class='glyphicon glyphicon-heart'></span>";
     } else {
          $hots_img = " <span class='glyphicon glyphicon-hand-up'></span>";         
     }


     $hot_button = "<a href='/do_hot.php?bbid=$bbid&vote=up' type='button' id='aHot$bbid' class='btn btn-primary btn-xs upHot'>$hots $hots_img</span></a>";
     $com_button = "<a href='/view.php?bbid=$bbid$COMMDIV' type='button' id='aComm$bbid' class='btn btn-primary btn-xs'>$coms <span class='glyphicon glyphicon-comment'></span></a>";

     $SETTINGS = "
     <li style='list-style: none; white-space: nowrap; overflow: visible;' class='dropdown dropup'><a style='font-size: x-small; color: black;' href=# id=\"dropGrid-$gid\" role=\"button\" class=\"dropdown-toggle\" data-toggle=\"dropdown\"><i class=\"icon-share-alt icon-white\"></i></a>
        <ul class=\"dropdown-menu\" style='min-width: 0px; left: -650%;' role=\"menu\" aria-labelledby=\"dropGrid-$gid\">
        $REMOVE
        $BLOCK
        <li role=\"presentation\"><a class=gridShare role=\"menuitem\" tabindex=\"-1\" href=\"/m/do_imgshare.php?pid=$pid\">Share</a></li>
        <li role='presentation'><a class=doRepost role='menuitem' tabindex='-1' href='/m/do_repost.php?pid=$pid'>Re-Post</a></li>
     </ul></li>";
     
     $MASONRY_ROWS .= "<div class='item $rand'>
                         <div style='position: absolute; left: 0px; top: 0px;'></div>
                         <a href=\"/view.php?bbid=$bbid\" role=\"button\">
                            <div class='imgBox $rand' style=\"background-image: url('$imgSRV/${imgfolder}_images/$images'); background-repeat: no-repeat; background-position: 50% 50%;\">
                            </div>
                         </a>
                         <div style='position: absolute; left: 5px; bottom: 5px;'>
                            <a href='/person.php?target=$puid'><img class='cropimgProSmall' src='$imgSRV/thumb_profiles/$puser'></a>
                         </div>
                         <div style='position: absolute; right: 5px; bottom: 5px;'>
                            <table border=0>
                               <tr><td align=left>$hot_button&nbsp;&nbsp;</td> 
                                   <td align=left>&nbsp;&nbsp;$com_button&nbsp;&nbsp;</td>
                               </tr>
                            </table>
                         </div>
                       </div>\n";

   } 
   return $MASONRY_ROWS;
}

?>