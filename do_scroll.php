<?php

include "dbinc.php";

include "functions.php";

$uid = getUser($USER);

$TYPE = $_GET['type'];
$OFFSET = $_GET['offset'];

$ATTATIME = 6;

$ROWCOUNT = "$OFFSET, $ATTATIME";
$OFFSET += $ATTATIME;

if($TYPE=="feed") {
   $GRIDROWS = generateFeed($ROWCOUNT);
} elseif($TYPE=="grid") {
   $GID = $_GET['gid'];
   $GRIDROWS = generateGrid($GID, $ROWCOUNT, 'col-6 col-sm-6 col-lg-5');
}

$NEXT_URL = "/do_scroll.php?type=$TYPE&gid=$GID&offset=$OFFSET";

echo $GRIDROWS;

echo "<a href='$NEXT_URL'>Loading...</a>";

?>
