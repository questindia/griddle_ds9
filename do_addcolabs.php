<?php
include "dbinc.php";
include "functions.php";

$user = $_SESSION['user'];
$uid = getUser($user);

$action = addslashes($_POST['action']);
$bbid   = addslashes($_POST['bbid']);

foreach ($_POST['colabs'] as $col) {
          $colt = addslashes($col);
          $colabs .= ",$colt,";
}

if($action=='add') {
    
    $bi = getGriddleInfo($bbid);
    $buid = $bi{'uid'};
    if($buid==$uid) {
       $old = $bi{'colabs'};
       $gid = $bi{'gid'};
       $new = "$old,$colabs,";
       $fnow = time();
       $res = mysql_query("UPDATE griddle_bb SET colabs='$new' WHERE bbid=$bbid");
       $sql = mysql_query("INSERT INTO triggers_bb VALUES(DEFAULT, $gid, $bbid, $uid, 3, $fnow, 0, 0, 0)");
    }
    
    header( "Location: http://$baseSRV/feed.php" );


}
   