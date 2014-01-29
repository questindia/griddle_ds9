<?php

$username = "root";
$password = "8dogson42fence";
if($_SERVER['SERVER_NAME'] == "www.griddle.com") {
   $hostname = "localhost";
} else {
   $hostname = "grd-mysql-01.griddle.com";
}

$baseSRV = $_SERVER['SERVER_NAME'];
$imgSRV = "http://www.turknettlabs.com";

if($baseSRV == "mobile.griddle.com") {
   $MOBSERV = 1;
}


//connection to the database
$dbhandle = mysql_connect($hostname, $username, $password)
 or die("Unable to connect to MySQL");

//select a database to work with
$selected = mysql_select_db("griddleng",$dbhandle)
  or die("Could not select examples");

file_put_contents("/tmp/dbinc.log", "Into DBINC\n", FILE_APPEND);

ini_set('session.cookie_domain', '.griddle.com');
session_name('griddle');
session_set_cookie_params(0, "/", ".griddle.com");
session_start();

// Check the cookie, and if set and valid, set user session value

if(isset($_COOKIE['griddle_session'])) {
 
   file_put_contents("/tmp/dbinc.log", "Got Into isset...\n", FILE_APPEND);
   
   $sc = $_COOKIE['griddle_session'];
   $un = $_COOKIE['griddle_username'];
   
   $then = time() - (60*60*24*30);
   
   $res = mysql_query("SELECT sid FROM session WHERE username='$un' AND sess_cookie='$sc' AND din > $then");
   $row = mysql_fetch_array($res);
   $test = $row{'sid'};
   
   file_put_contents("/tmp/dbinc.log", "Passed mysql\n", FILE_APPEND);
   
   if($test) {
       file_put_contents("/tmp/dbinc.log", "Setting sessionuser = $un\n", FILE_APPEND);
       $_SESSION['user'] = $un;
       $USER = $_SESSION['user'];
       file_put_contents("/tmp/dbinc.log", $_SESSION['user'] . "\n", FILE_APPEND);
   }
   
}




require_once "Mobile_Detect.php";

$detect = new Mobile_Detect;
if ($detect->isMobile() && !$detect->isTablet()) {
   $MOBILE = 1;
}

// Any tablet device.
if( $detect->isTablet() ){
   $TABLET = 1;
}

?>
