<?php

include "dbinc.php";

session_destroy();

$sc = $_COOKIE['griddle_session'];

$res = mysql_query("UPDATE session SET din=0 WHERE sess_cookie='$sc'");

if(!$MOBSERV) {
   header( "Location: http://$baseSRV" ); 
} else {
   header( "Location: http://$baseSRV/loggedout" );
}
?>
