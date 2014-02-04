<?php 

   include "dbinc.php";

if($USER = "") {
  header( "Location: http://$baseSRV" );
}

   include "header.php";
   include "functions.php";



?>
   <body>

<?php 
   include "fbinc.php";
   include "navbar.php";
  
  $NEXT_URL = '/do_scroll.php?type=feed&offset=6';
  
?>

    <div class="container narrow">

      <div class="row row-offcanvas row-offcanvas-right">

        <div class="col-xs-12 col-sm-9 col-lg-10 scroll pull-right">
                     
            <?php echo generateFeed(6);  ?>
            
            <a href='<?php echo $NEXT_URL; ?>'>Loading...</a>
        </div><!--/span-->
         <?php include "sidebar.php"; ?>
       </div><!--/span-->
      </div><!--/row-->
      

    </div><!--/.container-->



    <?php include "jsinc.php"; ?>
    <script>
      var t = setTimeout(function() { checkForNewPost() }, 180000);
    </script>
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
