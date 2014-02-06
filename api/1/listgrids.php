<?php

include "/var/www/dbinc.php";
include "/var/www/functions.php";

$trending = getTrending(100, "yes");

$stuff = explode(",", $trending);

$JSON = "{ \"grids\": [ ";


foreach ($stuff as $value) {
   $JSON .= "{ \"gn\": \"$value\" },\n";
}

$JSON = rtrim($JSON, ",\n");

$JSON .= " ] }";



print "$JSON";
?>
