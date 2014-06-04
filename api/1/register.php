<?php

include "/var/www/dbinc.php";
include "/var/www/functions.php";

define('UPLOAD_DIR', '/var/www/griddle_profiles/');
define('THUMB_DIR', '/var/www/thumb_profiles/');

$user   = addslashes($_POST['user']);
$pass   = addslashes($_POST['pass']);
$name   = addslashes($_POST['name']);
$mobile = addslashes($_POST['mobile']);
$email  = addslashes($_POST['email']);
$fbuid  = addslashes($_POST['fbuid']);

if(!$user || !$pass || !$email) {
   print "{ \"return\": \"ERROR\", \"details\": \"Must provide at least a username, password and email\" }";
   exit;
}

$test = getUser($user);

if($test) {
   print "{ \"return\": \"ERROR\", \"details\": \"Username already in use\" }";
   exit;
}

if(!$fbuid) { $fbuid = "0"; }

$filename = $user;
$image = $_FILES['pic']['tmp_name'];

if($image) {
   move_uploaded_file($_FILES['pic']['tmp_name'], UPLOAD_DIR . $filename);
   system("/usr/bin/convert -auto-orient -strip -resize 800x800 -quality 55 " . UPLOAD_DIR . $filename . " " . UPLOAD_DIR . $filename . ".jpg", $blah);
   system("/usr/bin/convert -auto-orient -strip -resize 160x160 -quality 55 " . UPLOAD_DIR . $filename . " " . THUMB_DIR . $filename . ".jpg", $blah);
   system("/bin/mv " . UPLOAD_DIR . $filename . ".jpg " . UPLOAD_DIR . $filename, $blah);
   system("/bin/mv " . THUMB_DIR . $filename . ".jpg " . THUMB_DIR . $filename, $blah);
} else {
   system("/bin/cp /var/www/img/profile.jpg " . UPLOAD_DIR . "/$filename");
   system("/bin/cp /var/www/img/profile.jpg " . THUMB_DIR . "/$filename");
}

$crypt = md5($password);
$now   = time();

$res = mysql_query("INSERT INTO users VALUES(DEFAULT, $fbuid, '$name', '$email', '$mobile', '$user', '$crypt', '', '', '', $now, '', 1, 0, 0, 1, 1, 50)");

$test = getUser($user);

if($test) {
   print "{ \"return\": \"SUCCESS\" }";
   exit;
} else {
   print "{ \"return\": \"ERROR\", \"details\": \"An unknown error occured\" }";
   exit;
}


?>
