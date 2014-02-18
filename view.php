<?php 
   include "dbinc.php";
   include "header.php"; 
   include "functions.php";

?>
  <body>

<?php
  include "fbinc.php"; 
  include "navbar.php";
  
  $bbid   = addslashes($_GET['bbid']);
  $GCLICK = addslashes($_GET['lightbox']);
  
  if(!$bbid) { // It happens sometimes
     header( "Location: http://$baseSRV/feed.php" );
  }
  
  $bi = getGriddleInfo($bbid);
  $st = $bi{'status'};
  if($st!=1) {
     header( "Location: http://$baseSRV/feed.php" );
  }
  
  
  
  $JAVA = "";
?>

    <div class="container wide">

      <div class="row row-offcanvas row-offcanvas-right">

        <div class="col-xs-12 col-md-12 col-sm-12 col-lg-10 pull-right">
          
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
        <div class='col-xs-12 col-md-12 col-sm-12 col-lg-6'>
         <div id='postRow'><br><br><br><br>
         <?php echo getGriddlePosts($bbid); ?>
         </div>
        </div>
        <div class='col-lg-3'></div>
      </div>

    </div><!--/.container-->



    <?php include "jsinc.php"; ?>
  </body>
  <script><?php if(($GCLICK) && ($MOBILE || $TABLET)) { ?>
          fshow = 1;
          function fresco_show() { 
             <?php echo $JAVA; ?> }
         </script><?php } ?>
  <script>
  </script>
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
