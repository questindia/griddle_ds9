<?php

    $USER = $_SESSION['user'];
?>

    <div class="navbar navbar-fixed-top navbar-default" role="navigation">
      <div class="container">
        <div class="col-lg-2"></div>
        <div class="navbar-header col-lg-8">
          <p class="pull-right visible-xs">
            <button type="button" class="btn btn-primary btn-xs navbar-toggle" data-toggle="offcanvas">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            </button>
          </p>
          <a class="navbar-brand" href="/"><img class='logoNavbar' src='/img/logo_5.png'></a>
          <div class="dropdown pull-right visible-lg">
             <a class="dropdown-toggle" id="dropdownMenu1" data-toggle="dropdown"><img class='cropimgProSmall' src='<?php echo $imgSRV; ?>/thumb_profiles/<?php echo $USER; ?>'><span class="caret"></span></a>
             <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1">
            	<li role="presentation"><a role="menuitem" tabindex="-1" href="/edit_profile.php">Edit Profile</a></li>
    			<li role="presentation" class="divider"></li>
    			<li role="presentation"><a role="menuitem" tabindex="-1" href="/handle_logout.php">Log Out</a></li>
  		  	 </ul>
          </div>
        </div>
        <div class="collapse navbar-collapse">
          <ul class="nav navbar-nav">

          </ul>
        </div><!-- /.nav-collapse -->
      </div><!-- /.container -->
    </div><!-- /.navbar -->

