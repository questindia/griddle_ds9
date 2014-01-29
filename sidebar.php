 <?php
 
    $uid = getUser($_SESSION['user']);
 
    $NOTES  = gotNotes($uid);
    $COLAB  = gotColabs($uid);
    $FRIEND = gotFriends($uid);
    
    if($MOBILE) { $narrowRight = "narrowRight"; }
    
    if($NOTES)  { $NOTELINE = "<span class='$narrowRight badge pull-right'>$NOTES</span>"; }
    if($COLAB)  { $COLBLINE = "<span class='$narrowRight badge pull-right'>$COLAB</span>"; }
    if($FRIEND) { $FRNDLINE = "<span class='$narrowRight badge pull-right'>$FRIEND</span>"; }
    
     
 ?>  
   
      <div class="col-xs-6 col-sm-3 col-lg-2 sidebar-offcanvas <?php echo $narrowRight; ?>" id="sidebar" role="navigation">
          <div class="list-group">
            <a href="http://<?php echo $baseSRV;?>" class="list-group-item"><span class="glyphicon glyphicon-list"></span> News Feed</a>
            <a href="/do_notes.php" class="list-group-item"><span class="glyphicon glyphicon-envelope"></span> Notifications <?php echo $NOTELINE; ?></a>
            <a href="/do_notes.php?type=friend" class="list-group-item"><span class="glyphicon glyphicon-user"></span> Friends <?php echo $FRNDLINE; ?></a>
     
            <?php if($MOBILE) { ?>
            <a href="/edit_profile.php" class="list-group-item"><span class="glyphicon glyphicon-cog"></span> Edit Profile</a>
            <a href="/do_post.php" class="list-group-item"><span class="glyphicon glyphicon-camera"></span> Make Griddle </a>
            <a href="/do_colab.php" class="list-group-item"><span class="glyphicon glyphicon-th"></span> Finish Griddle <?php echo $COLBLINE; ?></a>
            <a href="/handle_logout.php" class="list-group-item"><span class="glyphicon glyphicon-lock"></span> Logout</a>
            <?php } ?>
            
            
            
              </div>
        </div><!--/span-->
      </div><!--/row-->
