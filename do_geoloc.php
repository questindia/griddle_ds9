<?php

$r = time();
include "dbinc.php";
include "functions.php";


if($_SESSION['user'] == '') {
   exit;
} 

$user = $_SESSION['user'];
$uid = getUser($user);

$geo_lat  = addslashes($_POST['geo_lat']);
$geo_long = addslashes($_POST['geo_long']);


$res = mysql_query("REPLACE INTO current_geo VALUES($uid, $geo_lat, $geo_long, $r)");

?>
