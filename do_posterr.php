<?php
include "dbinc.php";
include "functions.php";

$user = $_SESSION['user'];
$uid = getUser($user);

$action = addslashes($_GET['action']);

if($MOBILE) { $FONTSIZE = "commLine"; }

if($action == 'done') {
   $message = "Oops!  Someone beat you to it.  This Griddle is already complete. Why don't you <a href=/do_post.php>Start a new one!</a>";
} elseif($action == 'mobile') {
   $message = "Oops!  Pictures for Griddles need to be added with a Mobile or Tablet device.";
}




    include "header.php";
    print "<body>\n";
    include "navbar.php";
    print "
  <div class='container'>
    <div class='row row-offcanvas row-offcanvas-right'>

      <div class='col-xs-12 col-sm-9 col-lg-10 pull-right'>";
       
    print "<div class='noColabs'><h4>$message</h4></div><br><br></div>";
    

   include "sidebar.php";
   print "</div>
     </div><!--/span-->
    </div>";
    
   include "jsinc.php";
   print "</body></html>";
    
