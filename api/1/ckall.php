<?php

include "/var/www/dbinc.php";
include "/var/www/functions.php";

$user = addslashes($_POST['username']);
$pass = addslashes($_POST['password']);
$json = $_POST['json'];


//$json = '{ "mobile": [ "mhash1", "mhash2" ],  "email": [ "ehash1", "ehash2" ] }';



if(apiAuth($user, $pass) < 1) { 
     print "{ \"return\": \"ERROR\", \"details\": \"Invalid Username or Password.\" }";
     exit;
}

if(!$json) { 
     print "{ \"return\": \"ERROR\", \"details\": \"Username, Password, and json are required\" }";
     exit;
}

file_put_contents("/tmp/ckall.log", "Got JSON -------------------\n$json\n--------------------", FILE_APPEND);

$decoded = json_decode($json);

//print "<pre>";
//print "\n$json\n";

foreach ($decoded as $key => $value) {
   //print "$key = $value\n";
   foreach ($value as $htarg) {
        $matchuid = hashMatch($key, $htarg);
        if($matchuid) {
            $JSON .= " 
            
             { \"type\": \"$key\",
             \"hash\": \"$htarg\",
             \"uid\": \"$matchuid\" }, ";          
        
        
            $MATCHES{$key} .= " \"$htarg\", ";
        }       
    }
}

$JSON = rtrim($JSON, ", ");
     
$EMATCHES = rtrim($MATCHES{"email"}, ", ");
$MMATCHES = rtrim($MATCHES{"mobile"}, ", ");
     
     
//print "</pre><br>";


//print "{ \"return\": \"SUCCESS\", \"ematch\": [ $EMATCHES ], \"mmatch\": [ $MMATCHES ] }";

print "{ \"return\": \"SUCCESS\", \"matches\": [ $JSON ] }";


function hashMatch($type, $hash) {

    if($type=="mobile") { $WHERE = "mhash='$hash' "; }
    if($type=="email")  { $WHERE = "ehash='$hash' "; }

    $res = mysql_query("SELECT * FROM pii_hash WHERE $WHERE LIMIT 1");
    $row = mysql_fetch_array($res);
    
    if($row{'uid'}) { 
        return $row{'uid'};
    } else {
        return 0;
    }

}




?>
