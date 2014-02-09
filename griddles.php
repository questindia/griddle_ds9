<?php 
  include "dbinc.php";
  include "header.php"; 
  include "functions.php";
  print "<body>\n"; 
  include "fbinc.php";
  include "navbar.php";
  
  $gid = $_GET['gid'];
  
  $NEXT_URL = "/do_scroll.php?type=grid&gid=$gid&offset=6";
  
?>

    <div class="container">

      <div class="row row-offcanvas row-offcanvas-right">

        <div class="col-xs-12 col-md-12 col-sm-12 col-lg-10 scroll pull-right">
                    
            <?php echo generateGrid($gid, 6, 'col-lg-4 col-md-6'); ?>
            
            <a href='<?php echo $NEXT_URL; ?>'>Loading...</a>
        </div><!--/span-->
         <?php include "sidebar.php"; ?>
       </div><!--/span-->
      </div><!--/row-->
      

    </div><!--/.container-->



    <?php include "jsinc.php"; ?>
  </body>
  

</html>



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
