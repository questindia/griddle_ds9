<?php
include "dbinc.php";

include "functions.php";



if(!(isset($_SESSION['user']))) {
   header( "Location: http://$baseSRV" );
   exit;
}

$r = time();



if(!$_POST['action']) { // Show Edit Screen

   //include the database
   

   $user = $_SESSION['user'];

   $ud = mysql_query("SELECT * FROM users WHERE username='$user' LIMIT 1");
   
   $udrow = mysql_fetch_array($ud);

   $uid = getUser($user);


   $rname = $udrow{'name'};
   $email = $udrow{'email'};
   $statement = $udrow{'statement'};
   $bio = $udrow{'bio'};
   $start = $udrow{'din'}; 
   $mobile = $udrow{'mobile'};
   $searchable = $udrow{'status'};
   $mobileopt = $udrow{'mobileopt'};
   $emailopt = $udrow{'emailopt'};
   $posts = $udrow{'posts'};
   $follows = $udrow{'followers'};
   $pounds = $udrow{'pounds'};

   $nrow = getNotificationSettings($udrow{'uid'});

   if($nrow{'followgrids'} == 1) {
      $fgCheck = "checked";
   }
   if($nrow{'followpeople'} == 1) {
      $fpCheck = "checked";
   }
   if($nrow{'followfriends'} == 1) {
      $ffCheck = "checked";
   }
   if($nrow{'followGComms'} == 1) {
      $fgcCheck = "checked";
   }
   if($nrow{'followPComms'} == 1) {
      $fpcCheck = "checked";
   }
   
   if($do_scrape == 1) {
      $dcCheck = "checked";
   }

   
   if($searchable == 1) {
      $sCheck = "checked";
   }
   if($mobileopt == 1) {
      $mCheck = "checked";
   }
   if($emailopt == 1) {
      $eCheck = "checked";
   }

   include "header.php";
   print "<body>\n";
   include "fbinc.php";
   include "navbar.php";
   


   // show the profile screen
?>

<div class="container">

      <div class="row row-offcanvas row-offcanvas-right">

        <div class="col-xs-12 col-sm-9 col-lg-10 pull-right">

<form id="saveProfile" action="/edit_profile.php" method="post" enctype="multipart/form-data">
  
<div class="col-lg-6">
   <input type=hidden name='action' value="update_profile">
   <input type=hidden name='user' value=<?php echo $user; ?>>
   <input type=hidden name='uid' value=<?php echo $udrow{'uid'}; ?>>

   <h4><?php echo $rname; ?></h4>
   <table width=100%>
   <tr><td>Email:</td><td><input class="form-control" type=text name=email  placeholder="Email Address" value="<?php echo $email; ?>"></td></tr>
   <tr><td>Mobile: </td><td><input class="form-control" type=text name=mobile placeholder="Mobile (Optional)" value="<?php echo $mobile; ?>"></td></tr>
   </table><br><br>
   <table width=100%>
   <tr><td valign=top>
   <h4>Notifications:</h4>
   <label class=checkbox>
      <input type=checkbox name=mobileopt value=yes <?php echo $mCheck; ?>> <span style='font-size: xx-small'>By Mobile Text </span>
   </label>
   <label class=checkbox>
      <input type=checkbox name=emailopt value=yes <?php echo $eCheck; ?>> <span style='font-size: xx-small'>By Email </span>
   </label></td></tr><tr>
   <td><h4>Notify me about:</h4>
   <label class=checkbox>
      <input type=checkbox name='followfriends' value=yes <?php echo $ffCheck; ?>> <span style='font-size: x-small'>Griddle Invitations from Friends</span>
   </label>
   <label class=checkbox>
      <input type=checkbox name='followPComms' value=yes <?php echo $fpcCheck; ?>><span style='font-size: x-small'>Comments made on my Griddles</span>
   </label></td></tr>
   </table>
 

 </div>
 
 <div class="col-lg-4">   
   <br>
   <img src='<?php echo $imgSRV; ?>/griddle_profiles/<?php echo "$user?$r"; ?>' height='150' width='150'><br><br>
   <label for=image>Update Profile Image:</label><input type=file name="image" id="image"><br><br>
   <a href=# class='FBLOGIN btn btn-sm btn-primary'><i class='fa fa-facebook-square'></i>&nbsp; Connect with Facebook</a><br><br>
   <a href=# class='btn btn-sm btn-primary'><i class='fa fa-twitter-square'></i>&nbsp; Sign In with Twitter</a><br>
   
 </div>



  <div class="col-lg-10">
  <br>
      <center>
      <button class="btn btn-lg btn-primary" type="submit">Save Changes</button></center><br><br>
  </div>

</form>

   </div>

         <?php include "sidebar.php"; ?>
       
      </div><!--/row-->
      

    </div><!--/.container-->


<?php

    include "jsinc.php";
    print "</body></html>";


}

if($_POST['action'] == 'update_profile') {



   $user = $_SESSION['user'];

   $ud = mysql_query("SELECT * FROM users WHERE username='$user' LIMIT 1");
   
   $udrow = mysql_fetch_array($ud);
   $uid = $udrow{'uid'};

   $was_mobile = $udrow{'mobile'};
 
   

   $fgrids = addslashes($_POST['followgrids']);
   $fpeople = addslashes($_POST['followpeople']);
   $ffriends = addslashes($_POST['followfriends']);
   $fGComms = addslashes($_POST['followGComms']);
   $fPComms = addslashes($_POST['followPComms']);
   $do_scrape = addslashes($_POST['do_scrape']);
   
   if($fgrids == "yes") {
      $followgrids = "1";
   } else {
      $followgrids = "0";
   }

   if($fpeople == "yes") {
      $followpeople = "1";
   } else {
      $followpeople = "0";
   }

   if($ffriends == "yes") {
      $followfriends = "1";
   } else {
      $followfriends = "0";
   }

   if($fGComms == "yes") {
      $followGComms = "1";
   } else {
      $followGComms = "0";
   }

   if($fPComms == "yes") {
      $followPComms = "1";
   } else {
      $followPComms = "0";
   }

   $searchable = addslashes($_POST['searchable']);
   $mobileopt = addslashes($_POST['mobileopt']);
   $emailopt = addslashes($_POST['emailopt']);
   $email = addslashes($_POST['email']);
   $statement = $_POST['tagline'];
   $bio = $_POST['bio'];
   $mobile = addslashes($_POST['mobile']);

   $mobile = preg_replace('/\'/', '', $mobile);
   $mobile = preg_replace('/-/', '', $mobile);
   $mobile = preg_replace('/\(/', '', $mobile);
   $mobile = preg_replace('/\)/', '', $mobile);


   // TODO - make sure someone else isn't already using the mobile number.  Also text-verify.
   $bio = addslashes($bio);
   $bio = preg_replace('/\n/', '', $bio);

   $statement = addslashes($statement);
   $statement = preg_replace('/\n/', '', $statement);

   define('UPLOAD_DIR', '/var/www/griddle_profiles/');
   define('THUMB_DIR', '/var/www/thumb_profiles/');

   $filename = addslashes($_POST['user']);
   
   //if($_POST['image'] != "") {
      $image = $_POST['image'];
      
      move_uploaded_file($_FILES['image']['tmp_name'], UPLOAD_DIR . $filename);
      system("/usr/bin/convert -auto-orient -strip -resize 800x800 -quality 55 " . UPLOAD_DIR . $filename . " " . UPLOAD_DIR . $filename . ".jpg", $blah);
      system("/usr/bin/convert -auto-orient -strip -resize 160x160 -quality 55 " . UPLOAD_DIR . $filename . " " . THUMB_DIR . $filename . ".jpg", $blah);
      system("/bin/mv " . UPLOAD_DIR . $filename . ".jpg " . UPLOAD_DIR . $filename, $blah);
      system("/bin/mv " . THUMB_DIR . $filename . ".jpg " . THUMB_DIR . $filename, $blah);
   //}
   // show the feed screen

   if($searchable == "yes") {
      $search = "1";
   } else {
      $search = "2";
   }

   if($mobileopt == "yes") {
      $opt = "1";
   } else {
      $opt = "0";
   }

   if($emailopt == "yes") {
      $eopt = "1";
   } else {
      $eopt = "0";
   }
   
   if($do_scape == "yes") {
      $dc = "1";
   } else {
      $dc = "0";
   }

   $nrow = getNotificationSettings($uid);
   if(!$nrow{'uid'}) {
       $res = mysql_query("INSERT INTO notification_settings VALUES($uid, $followgrids, $followpeople, $followfriends, $followGComms, $followPComms)");
   } else {
       $res = mysql_query("UPDATE notification_settings SET followgrids=$followgrids, followpeople=$followpeople, followfriends=$followfriends, followGComms=$followGComms, followPComms=$followPComms WHERE uid=$uid");
   }


   $res = mysql_query("UPDATE users SET email='$email', statement='$statement', bio='$bio', mobile='$mobile', status=$search, mobileopt=$opt, emailopt=$eopt WHERE uid=$uid");

   //$res = mysql_query("UPDATE fblink SET do_scrape=$dc WHERE uid=$uid");
   
   $mob_change = strcmp($mobile, $was_mobile);
   
   if($mob_change != 0) {
       $res = mysql_query("INSERT INTO mms_inform VALUES(DEFAULT, $uid, 'Welcome to Griddle!  You can post and view pictures by text message.  To see the available commands visit www.griddle.com/help/', 0)"); 
   }

   header( "Location: http://$baseSRV/edit_profile.php" );

   //print "<h3>Profile Saved</h3>";

}



?>
