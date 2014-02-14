<?php
include "dbinc.php";

$bbid = addslashes($_GET['bbid']);
$user = $_SESSION['user'];

include "functions.php";

$uid = getUser($user);

if(!$uid) {
	exit;
}

$PICS = getFBShareProfiles($bbid);

if(!$PICS) {
  $PICS = "No shares yet";
}

?>
 <div id='FBShareDiv'>
  <h4>Shared By:</h4>
  <?php echo $PICS; ?>
  <hr>
  <form id='formFBShare' role="form" class="" method='post' action='/fb_upload.php'>
     <input type=hidden name=bbid value='<?php echo $bbid; ?>'>
        <textarea cols=4 class='form-control' name='fbmess' placeholder='Write Something...'></textarea><br>
        <a class="FBSHARE btn btn-sm btn-primary"><i class='fa fa-facebook-square'></i>&nbsp; Share on Facebook</a>
  </form>     
 </div>
