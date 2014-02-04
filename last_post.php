<?php
include "dbinc.php";


$now = time();

include "functions.php";
# return yes if any of the 6 latest grids have anything newer than 1 minute posted

$res = mysql_query("SELECT last_post FROM griddles WHERE type=3 ORDER BY last_post DESC LIMIT 6");

while($row = mysql_fetch_array($res)) {
   if($row{'last_post'} > ($now - 300)) {
   	   print "reload";
   	   exit;
   }
}

?>
