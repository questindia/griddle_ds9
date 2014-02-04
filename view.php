<?php 
   include "dbinc.php";
   include "header.php"; 
   include "functions.php";

?>
  <body>

<?php
  include "fbinc.php"; 
  include "navbar.php";
  
  $bbid = $_GET['bbid'];
  
  if(!$bbid) { // It happens sometimes
     header( "Location: http://$baseSRV/feed.php" );
  }
  
  $NEXT_URL = "/do_scroll.php?type=grid&gid=$gid&offset=6";
  
?>

    <div class="container wide">

      <div class="row row-offcanvas row-offcanvas-right">

        <div class="col-xs-12 col-sm-12 col-lg-10 pull-right">
          
            <?php echo getGriddleBlock($bbid, 'col-lg-6 col-12 col-xs-12 col-sm-12'); ?>
            
            <div class='col-lg-4 col-sm-12 col-xs-12'>
            <?php echo getFormForGriddle($bbid, $_SESSION['user']); ?>
            </div>
          
        </div><!--/span-->
         <?php include "sidebar.php"; ?>
       </div><!--/span-->
      </div><!--/row-->
      
      <div class='row pull-right'>
        <div class='col-lg-3'></div>
        <div class='col-xs-12 col-sm-12 col-lg-6'>
         <?php echo getGriddlePosts($bbid); ?>
        </div>
        <div class='col-lg-3'></div>
      </div>

    </div><!--/.container-->



    <?php include "jsinc.php"; ?>
  </body>
</html>

<!-- Modals -->

<div id="myModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-body">
      </div>
      <div class="modal-footer">
          <button class="btn btn-default" data-dismiss="modal" aria-hidden="true">Close</button>
      </div>
    </div>
  </div>
</div>
