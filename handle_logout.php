<?php

include "dbinc.php";

session_destroy();

$sc = $_COOKIE['griddle_session'];

$res = mysql_query("UPDATE session SET din=0 WHERE sess_cookie='$sc'");


header( "Location: http://$baseSRV" ); 

?>
