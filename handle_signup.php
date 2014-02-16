<?php  
include "dbinc.php";

include "functions.php";

$action = $_GET['command'];
if($action == "") { $action = $_POST['command']; }

if(!$action && $MOBSERV) {
  $action = "start";
}

if($MOBSERV) {
   $color = "#6093bc";
} else {
   $color = "#54b4eb";
}


if($action == "start") {

   include "header.php";
   print "<body><style>
     body {
	  padding-top: 40px;
  	  padding-bottom: 40px;
      background-color: $color;
	 }
     </style>\n";
   print "<div class='container'>\n";
   genSignupForm("Griddle Signup");
   print "</div>\n";
   include "jsinc.php";
   print "</body></html>\n";

  exit;
}

if($action == "signup") {

   $count = getUser(addslashes($_POST['username']));
   $mob = getMobile(addslashes($_POST['username']));
   $rname = addslashes($_POST['rname']);
   $email = addslashes($_POST['email']);
   $uname = addslashes($_POST['username']);
   $mobile = addslashes($_POST['mobile']);
   $mobile = preg_replace('/\'/', '', $mobile);
   $mobile = preg_replace('/-/', '', $mobile);
   $mobile = preg_replace('/\(/', '', $mobile);
   $mobile = preg_replace('/\)/', '', $mobile);
   $gtest = "#" . addslashes($_POST['username']);

   $tgid = getGridID($gtest);

   if(($count > 0) || ($tgid > 0) || ($gtest == "www")) {
       bail_err("That Username is Taken", $rname, $email, $mobile, $uname);
       exit;
   }


   if($mobile && checkMobile($mobile)) {
      bail_err("Mobile number is already in use", $rname, $email, $mobile, $uname);
      exit;
   }

   // TODO - Validate all the rest of the input including sanitation, etc.

   // Do the signup

   $uid_q = mysql_query("SELECT uid FROM users ORDER BY uid DESC LIMIT 1");
   $uidrow = mysql_fetch_array($uid_q);
   $uid = $uidrow{'uid'};
   $uid = $uid + 1;

   // validate the invite code

   $icode = addslashes($_POST['icode']);

   if(!validateICode($icode, $uid)) {
      bail_err("Incorrect Invite Code", $rname, $email, $mobile, $uname);
      exit;
   }

   $password = addslashes($_POST['password']);

   if($password == "") {
      bail_err("Please enter a password", $rname, $email, $mobile, $uname);
      exit;
   }

   // TODO - Implement a more secure password hashing methodology

   $crypt = md5($password);
   $now = time();

   // Set status to 0 because the signup is incomplete.

   $sql_line = "INSERT INTO users VALUES($uid, '$rname', '$email', '$mobile', '$uname', '$crypt', '', '', '', $now, '', 0, 0, 0, 1, 1, 50)";

   $ins = mysql_query($sql_line);

   include "header.php";
   print "<body><style>
     body {
	  padding-top: 40px;
  	  padding-bottom: 40px;
      background-color: $color;
	 }
     </style>\n";
   print "<div class='container'>\n";

   genCustomizeForm($uname, $uid);
   
   print "</div>\n";
   include "jsinc.php";
   print "</body></html>\n";

}

if($action == "finish_signup") {

  $statement = addslashes($_POST['tagline']);
  $bio = addslashes($_POST['bio']);
  $uid = addslashes($_POST['uid']);
  $mobile = addslashes($_POST['mobile']);
  $rname = addslashes($_POST['rname']);

  $bio = preg_replace('/"/', '', $bio); 
  $bio = preg_replace('/\'/', '', $bio);
  $bio = preg_replace('/\n/', '', $bio);

  $statement = preg_replace('/"/', '', $statement);
  $statement = preg_replace('/\'/', '', $statement);
  $statement = preg_replace('/\n/', '', $statement);

  define('UPLOAD_DIR', '/var/www/griddle_profiles/');
  define('THUMB_DIR', '/var/www/thumb_profiles/');

  $filename = $_POST['user'];
  $image = $_FILES['image']['tmp_name'];

  if($image) {
    move_uploaded_file($_FILES['image']['tmp_name'], UPLOAD_DIR . $filename);
    system("/usr/bin/convert -auto-orient -strip -resize 800x800 -quality 55 " . UPLOAD_DIR . $filename . " " . UPLOAD_DIR . $filename . ".jpg", $blah);
    system("/usr/bin/convert -auto-orient -strip -resize 160x160 -quality 55 " . UPLOAD_DIR . $filename . " " . THUMB_DIR . $filename . ".jpg", $blah);
    system("/bin/mv " . UPLOAD_DIR . $filename . ".jpg " . UPLOAD_DIR . $filename, $blah);
    system("/bin/mv " . THUMB_DIR . $filename . ".jpg " . THUMB_DIR . $filename, $blah);
  } else {
    system("/bin/cp /var/www/img/profile.jpg " . UPLOAD_DIR . "/$filename");
    system("/bin/cp /var/www/img/profile.jpg " . THUMB_DIR . "/$filename");
  }
  
  $_SESSION['user'] = addslashes($_POST['user']);


  $res = mysql_query("UPDATE users SET name='$rname', mobile='$mobile', statement='$statement', bio='$bio', status=1 WHERE uid=$uid");

  if($mobile > 0) {
     $res = mysql_query("INSERT INTO mms_inform VALUES(DEFAULT, $uid, 'Welcome to Griddle!  You can post and view Griddles by text message.  To see the available commands visit www.griddle.com/help/', 0)"); 
     //system("/usr/bin/wget 'https://api.mogreet.com/moms/transaction.send?client_id=2417&token=228b0d99be4d97d6dd9f3954475583ab&campaign_id=36877&to=$mobile&message=Thanks%20for%20joining%20Griddle!%20%20The%20attached%20image%20should%20get%20you%20started.&content_url=http://www.griddle.com/images/example_cell.jpg&format=json' -q");
  }


  include "header.php";
   print "<body><style>
     body {
	  padding-top: 40px;
  	  padding-bottom: 40px;
      background-color: $color;
	 }
     </style>\n";
   include "fbinc.php";
   print "<div class='container'>
   <form class='form-signup id='formStartSignup'>";
   print "<h4>Would you like to Connect With Facebook to share your Griddles and connect with Friends?</h4>";
   
   print "<br><a id='FBCon' href=# class='FBLOGIN btn btn-sm btn-primary'><i class='fa fa-facebook-square'></i></a><br><br>
          <a id='noThanks' href='http://$baseSRV/feed.php' class='btn btn-sm btn-primary'>Continue to Griddle</a><br><br>";
   print "</form>";
   print "</div>\n";
   include "jsinc.php";
   print "</body></html>\n";

  //header( "Location: http://$baseSRV/handle_signup.php?command=facebook" );

}


if($action=="facebook") {

   include "header.php";
   print "<body><style>
     body {
	  padding-top: 40px;
  	  padding-bottom: 40px;
      background-color: $color;
	 }
     </style>\n";
   include "fbinc.php";
   print "<div class='container'>
   <form class='form-signup id='formStartSignup'>";
   print "<h4>Would you like to Connect With Facebook to share your Griddles and connect with Friends?</h4>";
   
   print "<br><a id='FBCon' href=# class='FBLOGIN btn btn-sm btn-primary'><i class='fa fa-facebook-square'></i></a><br><br>
          <a id='noThanks' href='/feed.php' class='btn btn-sm btn-primary'>Continue to Griddle</a><br><br>";
   print "</form>";
   print "</div>\n";
   include "jsinc.php";
   print "</body></html>\n";



}



function bail_err($err, $rname, $email, $mobile, $username) {
     include "header.php";
     print "<body><style>
     body {
	  padding-top: 40px;
  	  padding-bottom: 40px;
      background-color: $color;
	 }
     </style>\n";
     print "<div class='container'>\n";

     genSignupForm($err, $rname, $email, $mobile, $username);

     print "</div>\n";
     include "jsinc.php";
     print "</body></html>\n";

}



function genSignupForm($err, $rname, $email, $mobile, $username) {
 
 GLOBAL $baseSRV;
  
?>

 

 <div>  
 
     
        <form class="form-signup" id="formStartSignup" method=post action="https://<?php echo $baseSRV; ?>/handle_signup.php">
        <h4><?php echo $err; ?></h4>
        <input type=hidden name="command" value=signup>      
        <input type="text" class="form-control" placeholder="Email Address" name=email value='<?php echo $email; ?>'>
        <input type="text" class="form-control" placeholder="Username" name=username value='<?php echo $username; ?>'>
        <input type="password" class="form-control" placeholder="Password" name=password>
        <input type="password" class="form-control" placeholder="Invite Code" name=icode>

        <button class="btn btn-large btn-primary" type="submit" id="signupStart">Sign Up</button>
      </form>
</div>

<?php

}

function genCustomizeForm($user, $uid) {

GLOBAL $baseSRV;

?>
<div>
  <form class="form-signup" action="https://<?php echo $baseSRV; ?>/handle_signup.php" id="formFinishSignup" method=post enctype="multipart/form-data">
   <input type=hidden name="command" value=finish_signup>
   <input type=hidden name=user value="<?php echo $user; ?>">
   <input type=hidden name=uid value="<?php echo $uid; ?>">
   <h4 class="form-signup-heading">Thanks, just a few more details</h4>
   <input type="text" class="form-control" placeholder="Your Name" name=rname value='<?php echo $rname; ?>'>
   <input type="text" class="form-control" placeholder="Mobile (Optional)" name=mobile value='<?php echo $mobile; ?>'>
   <label for=image>Profile Picture:</label><input class="form-control" type=file name="image" id="image">
   <br>
   <button class="btn btn-lg btn-primary" type="submit" id="signupFinish">Finish</button>
  </form>
</div>
<?php

}


?>
