<?php
include "dbinc.php";

$user = $_SESSION['user'];

$bbid = $_GET['bbid'];
$type = $_GET['type'];
$action = $_GET['action'];


include "functions.php";

if($action == "more") {

    $OFFSET = $_SESSION['COMM_OFFSET'];

    if(!$OFFSET) {
       $OFFSET = "5";
    } else { 
       $OFFSET += 5; 
    }
    
    $_SESSION['COMM_OFFSET'] = $OFFSET;
    
    $LIMIT = "$OFFSET, 5";
    $COMMS = getCommentsForGriddle($bbid, $user, $LIMIT);
    
    print "$COMMS";
    exit;
}



