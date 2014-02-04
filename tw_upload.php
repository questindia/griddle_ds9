<?php
include "dbinc.php";

$bbid = addslashes($_GET['bbid']);
$user = $_SESSION['user'];

include "functions.php";

$uid = getUser($user);

if(!$uid) {
	exit;
}

     $res = mysql_query("UPDATE griddle_bb SET twshare=twshare+1 WHERE bbid=$bbid");
     $res = mysql_query("SELECT twshare FROM griddle_bb WHERE bbid=$bbid");
     $row = mysql_fetch_array($res);
     $twshare = $row{'twshare'};

     print "{ \"bbid\":$bbid, \"twshare\":\"$twshare\" }";





