<?php
include "dbinc.php";


$r = time();
$rd = substr($r, 0, 8);

$bbid = $_GET['bbid'];
$FULL = $_GET['full'];
$user = $_SESSION['user'];

include "functions.php";

if(!$user) { // bail
   file_put_contents("/tmp/do_comms.log", "No user\n", FILE_APPEND);
   exit;
}

if($pid) {
  print getCommentsForGriddle($bbid, $user);
  exit;
}
 
$bbid = $_POST['bbid'];
if($bbid) {
   file_put_contents("/tmp/do_comms.log", "Found bbid = $bbid\n", FILE_APPEND);
   $user = $_SESSION['user'];
   $comm = addslashes($_POST['comment']);
   if(!$comm) { file_put_contents("/tmp/do_comms.log", "No comment\n", FILE_APPEND); exit; }
   $cCount = addCommentToGriddle($user, $bbid, $comm);
   $comrow = getCommentsForGriddle($bbid, $user, "0, 5");
   $comrow = preg_replace('/\n/', '', $comrow);
   $comrow = preg_replace('/\r/', '', $comrow);
   print "{ \"comments\":$cCount, \"bbid\":$bbid, \"comms\":\"$comrow\" }";
}


?>
