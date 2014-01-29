<?php
include "dbinc.php";
include "functions.php";

$user = $_SESSION['user'];
$uid = getUser($user);

$action = addslashes($_GET['action']);

if($MOBILE) { $FONTSIZE = "commLine"; }


if(!$action) {

    // Grab a list of griddles this user has been asked to collaborate on
    // TODO this method of search is not going to work for long and it's an ugly hack
    $res = mysql_query("SELECT * FROM griddle_bb WHERE (colabs LIKE '%,$uid,%' OR uid=$uid) AND status=0");
    while($row = mysql_fetch_array($res)) {
       // Cycle through and get the general information
       $tgid = $row{'gid'};
       $tuid = $row{'uid'};
       $bbid = $row{'bbid'};
       
       $gi = getGridInfo($tgid);
       $topic = $gi{'topic'};
       
       $ppids = explode(",", $row{'ppid'});
       $left = 9 - sizeof($ppids);
       $uinfo = getName($tuid);
       $tname = $uinfo{'name'};
       
       $OUT .= "<tr><td>$topic</td><td>$tname</td><td><a class='postButton' href='/do_post.php?bbcheck=$bbid&gid=$tgid&purpose=complete'>$left Spots Left!</a></td></tr>\n";
    }
    include "header.php";
    print "<body>\n";
    include "navbar.php";
    print "
  <div class='container'>
    <div class='row row-offcanvas row-offcanvas-right'>

      <div class='col-xs-12 col-sm-9 col-lg-10 pull-right'>";
       
    if($OUT) {
        print "<h4>You can finish these Griddles</h4><br>";
        print "<table width=100% class='table-striped $FONTSIZE'><tbody>
        <tr><th align=left>Grid</th><th align=left>Creator</th><th align=left>Complete It!</th></tr>
        $OUT
        </tbody></table>";
    } else {
        print "<h4>You don't have any unfinished Griddles.  Why don't you <a href=/do_post.php>Start One!</a></h4><br><br>";
    }

   include "sidebar.php";
   print "</div>
     </div><!--/span-->
    </div>";
    
   include "jsinc.php";
   print "</body></html>";
    

}






function getImageSels($limit, $gid) {

      $res = mysql_query("SELECT pid, images FROM posts WHERE gid=$gid AND status=1 ORDER BY din DESC LIMIT $limit");
      while($row = mysql_fetch_array($res)) {
          $images = $row{'images'};
          $pid = $row{'pid'};

          $OPTS .= "<option data-img-src='$imgSRV/thumb_images/$images' value='$pid'>$pid</option>\n";
      }

      return $OPTS;

}