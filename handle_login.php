<?php

include "dbinc.php";
include "functions.php";

$action = addslashes($_POST['action']);
if(!$action) { $action = addslashes($_GET['action']); }

if(!$action && $MOBSERV) {
   $action = "login";
}

$user = addslashes($_POST['username']);
if(!$user) { addslashes($user = $_GET['username']); }

$pass = addslashes($_POST['password']);
if(!$pass) { addslashes($pass = $_GET['password']); }

$from = addslashes($_POST['from']);
if(!$from) { $from = addslashes($_GET['from']); }

$rem = addslashes($_POST['remember_me']);

if($action == 'login') {

   $res = mysql_query("SELECT password FROM users WHERE username='$user'");
   $pw_row = mysql_fetch_array($res);
   $pw_crypt = $pw_row{'password'};

   $attempt = md5($pass);

   if($attempt == $pw_crypt) {
     $_SESSION['user'] = $user;
      
     // check remember_me and if clicked, save a session cookie for this user
     if($rem == "1") {
      print "Into Remember...<br>";
      setcookie("griddle_username", $user, time() + 2419200, "/", ".griddle.com");
      setcookie("griddle_remember", "remember_me", time() + 2419200, "/", ".griddle.com");
      
      $rand1 = randString(5);
      $rand2 = randString(5);
      $rand3 = randString(5);
      $rand4 = randString(5);
      $rand5 = randString(5);
      
      $now = time();
      $sess_cookie = "$rand1-$rand2-$rand3-$rand4-$rand5";
      
      //print "$sess_cookie - $now<br>";
      
      $res = mysql_query("INSERT INTO session VALUES(DEFAULT, '$user', '$sess_cookie', '', $now)");
      
      //print "Hit mysql...<br>";
      
      setcookie("griddle_session", "$sess_cookie", time() + 2419200, "/", ".griddle.com");
     }

      if($from == "") {
        header( "Location: http://$baseSRV/feed.php" ); 
      } else {
        header( "Location: http://$from.griddle.com" );
      }
   }

   else {
 
     // TODO - more graceful login failure page
     echo "Login Fail!";

   }


}
?>
