<?php

include "/var/www/dbinc.php";
include "/var/www/functions.php";

$user = $_POST['username'];
if(!$user) { $user = $_GET['username']; }

$pass = $_POST['password'];
if(!$pass) { $pass = $_GET['password']; }


$pass = addslashes($pass);
$user = addslashes($user);


if(apiAuth($user, $pass) < 1) { 
     print "{ \"return\": \"ERROR\" }";
     exit;
}

$uid = getUser($user);

$res = mysql_query("SELECT relations.uid, users.name, users.username, users.mobile, users.email FROM relations, users WHERE relations.friend=2 AND relations.target=$uid AND users.uid=relations.uid ORDER BY users.name LIMIT 2000");

$JSON = "{ \"return\": \"SUCCESS\", \"friends\": [ ";

while($row = mysql_fetch_array($res)) {
   $u  = $row{'uid'};
   $n  = $row{'name'};
   $un = $row{'username'};
   $m  = md5($row{'mobile'});
   $em = md5($row{'email'});
   
   $fi = getFacebookInfo($u);
   $fu = $fi{'fbuid'};


   $JSON .= "{ \"n\": \"$n\",
               \"un\": \"$un\",
               \"m\": \"$m\",
               \"em\": \"$em\",
               \"fu\": \"$fu\" },\n";
}

$JSON = rtrim($JSON, ",\n");

$JSON .= " ] }";



print "$JSON";
?>
