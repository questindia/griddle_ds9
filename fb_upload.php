<?php
  // Remember to copy files from the SDK's src/ directory to a
  // directory in your application on the server, such as php-sdk/
  require_once('facebook-php-sdk/src/facebook.php');
  include "dbinc.php";
  include "functions.php";
  
  $now = time();
  
  $config = array(
    'appId' => '436926926413614',
    'secret' => '37c380464160d4d0d950435e800d2422',
    'fileUpload' => true,
    'allowSignedRequest' => false // optional but should be set to false for non-canvas apps
  );

  $facebook = new Facebook($config);
  $user_id = $facebook->getUser();

  $uid     = getUser($_SESSION['user']);
  $fi      = getFacebookInfo($uid);
  $token   = $fi{'last_token'};
  //$user_id = $fi{'fbuid'};

  $bbid = addslashes($_POST['bbid']);
  $photo = "/var/www/griddles/$bbid-bb-latest.jpg";

  //$facebook->setAccessToken($token);

  $bi    = getGriddleInfo($bbid);
  $topic = getTopic($bi{'gid'});

  $message = addslashes($_POST['fbmess']);

  $message .= "\n$topic on  http://www.griddle.com";

  //include "header.php";
  //print "<body>\n";
  //include "navbar.php";

?>


  <?php
    if($user_id) {

      // We have a user ID, so probably a logged in user.
      // If not, we'll get an exception, which we handle below.
      try {

        // Upload to a user's profile. The photo will be in the
        // first album in the profile. You can also upload to
        // a specific album by using /ALBUM_ID as the path 
        $ret_obj = $facebook->api('/me/photos', 'POST', array(
                                         'source' => '@' . $photo,
                                         'message' => $message,
                                         )
                                      );
                                      
        $res = mysql_query("UPDATE griddle_bb SET fbshare=fbshare+1 WHERE bbid=$bbid");
        $res = mysql_query("SELECT fbshare FROM griddle_bb WHERE bbid=$bbid");
        $row = mysql_fetch_array($res);
        $fbshare = $row{'fbshare'};
        
        $res = mysql_query("SELECT uid FROM fb_shares WHERE bbid=$bbid");
        $row = mysql_fetch_array($res);
        $there = $row{'uid'};
        if(!$there) {
            $res = mysql_query("INSERT INTO fb_shares VALUES($uid, $bbid, $now)");
        }
        
        print "{ \"bbid\":$bbid, \"fbshare\":\"$fbshare\" }";
                                      
        //echo '<pre>Photo ID: ' . $ret_obj['id'] . '</pre>';
        //echo '<br /><a href="' . $facebook->getLogoutUrl() . '">logout</a>';
      } catch(FacebookApiException $e) {
        // If the user is logged out, you can have a 
        // user ID even though the access token is invalid.
        // In this case, we'll get an exception, so we'll
        // just ask the user to login again here.
        /* $login_url = $facebook->getLoginUrl( array(
                       'scope' => 'photo_upload'
                       ));
        print "<h4>You'll need to connect with Facebook to share this Griddle</h4>"; 
        print "<a href=# class='FBLOGIN'><img src='/img/fb-connect-large.png'></a>";
        */
        error_log($e->getType());
        error_log($e->getMessage());
      }   
    } else {

      // No user, print a link for the user to login
      // To upload a photo to a user's wall, we need photo_upload  permission
      // We'll use the current URL as the redirect_uri, so we don't
      // need to specify it here.
      /* $login_url = $facebook->getLoginUrl( array( 'scope' => 'photo_upload') );
      print "<a href=# class='FBLOGIN btn btn-sm btn-primary'>Facebook Login 2</a>"; */

    }

  ?>


