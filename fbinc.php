<div id="fb-root"></div>
<script>
  // Additional JS functions here
  window.fbAsyncInit = function() {
    FB.init({
      appId      : '436926926413614', // App ID
      channelUrl : '//<?php echo $baseSRV; ?>/channel.html', // Channel File
      status     : true, // check login status
      cookie     : true, // enable cookies to allow the server to access the session
      xfbml      : true  // parse XFBML
    });
  

  FB.Event.subscribe('auth.authResponseChange', function(response) {

    if (response.status === 'connected') {
      FB_Callback();
    } else if (response.status === 'not_authorized') {
      //FB.login();
    } else {
      //FB.login();
    }
  });
};

  // Load the SDK asynchronously
  (function(d){
     var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
     if (d.getElementById(id)) {return;}
     js = d.createElement('script'); js.id = id; js.async = true;
     js.src = "//connect.facebook.net/en_US/all.js";
     ref.parentNode.insertBefore(js, ref);
   }(document));

</script>

