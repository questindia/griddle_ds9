<?php

include "/var/www/dbinc.php";
include "/var/www/functions.php";

define('UPLOAD_DIR', '/var/www/griddle_profiles/');
define('THUMB_DIR', '/var/www/thumb_profiles/');

$user    = addslashes($_POST['username']);
$pass    = addslashes($_POST['password']);
$newpass = addslashes($_POST['newpass']);
$name    = addslashes($_POST['name']);
$mobile  = addslashes($_POST['mobile']);
$email   = addslashes($_POST['email']);
$mopt    = addslashes($_POST['mopt']);
$eopt    = addslashes($_POST['eopt']);

file_put_contents("/tmp/do_postfinish.log", "$_POST", FILE_APPEND);

file_put_contents("/tmp/do_postfinish.log", "$user - $pass - $newpass - $name - $mobile - $email - $mopt - $eopt\n", FILE_APPEND);


if(!$user || !$pass || !$email) {
   print "{ \"return\": \"ERROR\", \"details\": \"Must provide at least a username, password and email\" }";
   exit;
}


if(apiAuth($user, $pass) < 1) { 
     print "{ \"return\": \"ERROR\" , \"details\": \"Invalid Username or Password\"}";
     exit;
}

$uid = getUser($user);

if(!$fbuid) { $fbuid = "0"; }

$filename = $user;
$image = $_FILES['pic']['tmp_name'];

if($image) {
   move_uploaded_file($_FILES['pic']['tmp_name'], UPLOAD_DIR . $filename);
   system("/usr/bin/convert -auto-orient -strip -resize 800x800 -quality 55 " . UPLOAD_DIR . $filename . " " . UPLOAD_DIR . $filename . ".jpg", $blah);
   system("/usr/bin/convert -auto-orient -strip -resize 160x160 -quality 55 " . UPLOAD_DIR . $filename . " " . THUMB_DIR . $filename . ".jpg", $blah);
   system("/bin/mv " . UPLOAD_DIR . $filename . ".jpg " . UPLOAD_DIR . $filename, $blah);
   system("/bin/mv " . THUMB_DIR . $filename . ".jpg " . THUMB_DIR . $filename, $blah);
} 
//else {
//   system("/bin/cp /var/www/img/profile.jpg " . UPLOAD_DIR . "/$filename");
//   system("/bin/cp /var/www/img/profile.jpg " . THUMB_DIR . "/$filename");
//}

if($newpass) {
   $crypt = md5($newpass);
   $NEWPASS = ", password='$crypt'";
}

$mhash = md5($mobile);
$ehash = md5($email);


file_put_contents("/tmp/do_postfinish.log", "REPLACE INTO pii_hash VALUES($uid, '$mhash', '$ehash')\n", FILE_APPEND);

$res = mysql_query("REPLACE INTO pii_hash VALUES($uid, '$mhash', '$ehash')");

file_put_contents("/tmp/do_postfinish.log", "UPDATE users SET name='$name', mobile='$mobile', email='$email', mobileopt=$mopt, emailopt=$eopt $NEWPASS WHERE uid=$uid\n", FILE_APPEND);

$res = mysql_query("UPDATE users SET name='$name', mobile='$mobile', email='$email', mobileopt=$mopt, emailopt=$eopt $NEWPASS WHERE uid=$uid");

$ui = getUserInfo($uid);

$n  = $ui{'name'};
$m  = $ui{'mobile'};
$e  = $ui{'email'};
$eo = $ui{'emailopt'};
$mo = $ui{'mobileopt'};

print "{ \"return\": \"SUCCESS\", 
         \"details\": \"The user's profile was updated\",
         \"name\": \"$n\",
         \"mobile\": \"$m\",
         \"email\": \"$e\",
         \"eopt\": \"$eo\",
         \"mopt\": \"$mo\" }";



?>
