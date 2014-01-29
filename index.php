<?php 
include "dbinc.php";
include "functions.php";

if($_COOKIE['griddle_remember'] == "remember_me") {
  $REMCHECKED = " checked ";
}

$user = $_SESSION['user'];

if($user == "") {

   include "header.php";

?>

  <body>

     <style>
     body {
	  padding-top: 40px;
  	  padding-bottom: 40px;
      background-color: #54b4eb;
	 }
     </style>


    <div class="container">
 
   
      <form role="form" class="form-signin" method=post action='https://<?php echo $baseSRV; ?>/handle_login.php'>
         <input type=hidden name=action value=login>
         <center><img src='/img/logo_5.png'></center>
        <h3 class="form-signin-heading">Please sign in</h3>
        <input type="text" class="form-control" placeholder="Username" name=username value="<?php echo $_COOKIE['griddle_username']; ?>">
        <input type="password" class="form-control" placeholder="Password" name=password>
        <label class="checkbox">
          <input name="remember_me" type="checkbox" value="1" <?php echo $REMCHECKED; ?>> Remember me
        </label>
        <button class="btn btn-lg btn-info" type="submit">Sign in</button>&nbsp;&nbsp;&nbsp;<a class="btn btn-lg btn-info " href="https://<?php echo $baseSRV; ?>/handle_signup.php?command=start">Sign Up</a>
      </form>
      </div>

    </div> <!-- /container -->
    
    <!-- Le javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
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

   exit;


} else { // End if the user session vairable was not set.  If the user is known, forward them to feed.php

   header( "Location: http://$baseSRV/feed.php" );

} // <-- End if the user session variable was not set.


?>













