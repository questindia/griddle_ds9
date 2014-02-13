<?php
include "dbinc.php";
include "functions.php";

$user = $_SESSION['user'];
$uid = getUser($user);

$action = addslashes($_GET['action']);
if(!$action) {
   $action = addslashes($_POST['action']);
}


if($MOBILE) { $FONTSIZE = "commLine"; }


if(!$action) {

    include "header.php";
    print "<body>\n";
    include "navbar.php";
    print "
  <div class='container'>
    <div class='row row-offcanvas row-offcanvas-right'>

      <div class='col-xs-12 col-md-12 col-sm-12 col-lg-10 pull-right'>
      
         <div class='col-lg-5 col-md-5 col-sm-5'>
           <div class='noColabs'>
            <form action=/do_feedback.php method=post>
               <input type=hidden name='action' value='feedback'>
               <h3>Please let us know what's on your mind!</h3>
               <textarea rows=8 name='feedback' class='form-control' placeholder='Feedback...'></textarea><br>
               <button type=submit class='btn btn-lg btn-primary'>Submit Feedback</button>
            </form>
           </div>
         </div>
      ";
        
   
   print "</div>";
   include "sidebar.php";
   print "</div>
     </div><!--/span-->
    </div>";
    
   include "jsinc.php";
   print "</body></html>";
    

}

if($action=='feedback') {

    $feedback = addslashes($_POST['feedback']);
    $din      = time();
    
    $res = mysql_query("INSERT INTO feedback VALUES(DEFAULT, $uid, '$feedback', $din)");
    
    include "header.php";
    print "<body>\n";
    include "navbar.php";
    print "
  <div class='container'>
    <div class='row row-offcanvas row-offcanvas-right'>

      <div class='col-xs-12 col-md-12 col-sm-12 col-lg-10 pull-right'>";
       
   
   print " <div class='col-lg-5 col-md-5 col-sm-5'><div class='noColabs'><h3>Thanks for the feedback!</h3></div></div></div>";
   include "sidebar.php";
   print "</div>
     </div><!--/span-->
    </div>";
    
   include "jsinc.php";
   print "</body></html>";




}