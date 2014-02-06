<?php

    $USER = $_SESSION['user'];
?>

    <div class="navbar navbar-fixed-top navbar-default" role="navigation">
      <div class="container">
        <div class="col-lg-2"></div>
        <div class="navbar-header col-lg-8">
          
          <a class="navbar-brand" href="/"><img class='logoNavbar' src='/img/logo_5.png'></a>
          
          <div class="dropdown pull-right visible-lg">
             <a class="dropdown-toggle" id="dropdownMenu1" data-toggle="dropdown"><img class='cropimgProSmall' src='<?php echo $imgSRV; ?>/thumb_profiles/<?php echo $USER; ?>'><span class="caret"></span></a>
             <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1">
            	<li role="presentation"><a role="menuitem" tabindex="-1" href="/edit_profile.php">Edit Profile</a></li>
    			<li role="presentation" class="divider"></li>
    			<li role="presentation"><a role="menuitem" tabindex="-1" href="/handle_logout.php">Log Out</a></li>
  		  	 </ul>
          </div>
          <p class="pull-right visible-md visible-sm visible-xs" style='margin-top: 7px;'>
            <button type="button" class="btn btn-primary btn-md" data-toggle="offcanvas">
            <span class="glyphicon glyphicon-align-justify"></span>
            </button>
          </p>
        </div>
        <div class="collapse navbar-collapse">
          <ul class="nav navbar-nav">

          </ul>
        </div><!-- /.nav-collapse -->
      </div><!-- /.container -->
    </div><!-- /.navbar -->

