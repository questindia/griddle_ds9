<?php 

include "dbinc.php";

if($USER = "") {
  header( "Location: http://$baseSRV" );
}

include "header.php";
include "functions.php";

$target = addslashes($_GET['target']);

$ui = getUserInfo($target);

$uname       = $ui{'username'};
$PROFILENAME = $ui{'name'};
$POSTS       = $ui{'posts'};

$PROFILEPIC  = "$imgSRV/griddle_profiles/$uname";


?>
   <body>

<?php 
   include "fbinc.php";
   include "navbar.php";
  
  $NEXT_URL = "/do_scroll.php?type=feed&offset=6&target=$target";
  
?>

    <div class="container narrow">

      <div class="row row-offcanvas row-offcanvas-right">

        <div class="col-xs-12 col-sm-9 col-lg-10 pull-right">
            <div class='row'>
               <br>
               <div class='col-lg-5'>
                 <img src='<?php echo $PROFILEPIC; ?>' class='cropimgFullPro'><br><br>
               </div>
               <div class='col-lg-5'>
                 <div class='well well-sm'>
                  <h2><?php echo $PROFILENAME; ?></h2>
                  <h3>@<?php echo $uname; ?></h3>
                  <h4>+ <?php echo $POSTS; ?> Posts</h4>
                  <ul class='ulNoList'>
                  <?php echo getFriendButton($target); ?>
                  </ul>
                 </div>
               </div>
             </div>
             
            <div class='scroll'><br>
               <h2>Activity:</h2>         
               <?php echo generateFeed(6, $target);  ?>
               <a href='<?php echo $NEXT_URL; ?>'>Loading...</a>
            </div><!-- scroll -->
        </div><!--/span-->
         <?php include "sidebar.php"; ?>
       </div><!--/span-->
      </div><!--/row-->
      

    </div><!--/.container-->



    <?php include "jsinc.php"; ?>
  </body>
</html>

<!-- Modals -->

<div id="myModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-body">
      </div>
      <div class="modal-footer">
          <button class="btn btn-default" data-dismiss="modal" aria-hidden="true">Close</button>
      </div>
    </div>
  </div>
</div>


<?php

function getFriendButton($target) {
 
    $uid = getUser($_SESSION['user']);
    
    $res = mysql_query("SELECT rid, friend FROM relations WHERE uid=$uid AND target=$target");
    $row = mysql_fetch_array($res);
    $rid = $row{'rid'};
    $friend = $row{'friend'};
    
    if($friend == 0) {
      $fLine = "<li id=relLink$target role='presentation'><a role='menuitem' class='btn btn-sm btn-primary relLink' href='/do_rels.php?action=friend&target=$target&type=button'><span class='glyphicon glyphicon-plus'></span> Add Friend</a></li>";
    } elseif ($friend == 1) {
      $fLine = "<li id=relLink$target role='presentation'><a role='menuitem' class='btn btn-sm btn-primary relLink' href='/do_rels.php?action=unrequest&target=$target&type=button'><span class='glyphicon glyphicon-remove'></span> Friends Pending</a></li>";
    } elseif ($friend == 2) {
      $fLine = "<li id=relLink$target role='presentation'><a role='menuitem' class='btn btn-sm btn-primary relLink' href='/do_rels.php?action=unfriend&target=$target&type=button'><span class='glyphicon glyphicon-remove'></span> Remove Friend</a></li>";
    } else {
      $fLine = "<li id=relLink$target role='presentation'><a role='menuitem' class='btn btn-sm btn-primary relLink' href='/do_rels.php?action=friend&target=$target&type=button'><span class='glyphicon glyphicon-plus'></span> Add Friend</a></li>";
    }

    if($target==$uid) { $fLine = ""; }

    return $fLine;

}

