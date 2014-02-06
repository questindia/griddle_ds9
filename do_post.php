<?php

include "dbinc.php";
include "functions.php";

$user = $_SESSION['user'];

if($user=="") {
   header( "Location: http://$baseSRV" );
}
$uid = getUser($user);

if(!$MOBILE && !$TABLET) {
   header( "Location: http://$baseSRV/do_posterr.php?action=mobile" );
   exit;
}


$gcheck = addslashes($_GET['gid']);
$purpose = addslashes($_GET['purpose']);
$bbcheck = addslashes($_GET['bbcheck']);
$gtopic = getTopic($gcheck);
$sel_friend = getFriendSel(9999, $uid);

$bbi = getGriddleInfo($bbcheck);

$tstat = $bbi{'status'};
if($tstat==1) {
   header( "Location: http://$baseSRV/do_posterr.php?action=done" );
   exit;
}




if(!$purpose) { $purpose = "griddle"; }

if(!$gcheck) {
      //$search     = getSearchSeed(1000);
      //$sel_grid   = getTrendingSel(200);
      
      $CHOOSE = "
       <div class='fluid-row'>
       <div class='span2'>
   	  	  	<!-- <select class='form-control' name='sel_grid'>
   	  	  		<option selected>Recent Hashtags</option>
   	  	  		$sel_grid
   	  	  	</select><br> -->
   	  	  </div>
   	  	  <div class='span2'>
   	  	  	<input type=text class='form-control' name='type_grid' placeholder='Add a #hashtag (Required)' data-provide='typeahead' data-items='4' data-source='$search'>
   	  	  </div>
   	  	  <div id='chooseFriend' class='span4'>
   	  	  <br>
   	  	    <select class='form-control' name='colabs[]' multiple>
   	  	       <option value='' disabled selected>Choose some Friends to Help!</option>
   	  	       $sel_friend
   	  	       </select>
   	  	   <br>
   	  	   </div>
   	    </div>
      ";
      
      $TEXTBOX = "<textarea class='form-control' placeholder=\"What's Happening?\" rows=3 cols=25 name='message'></textarea>";

   } elseif($gcheck) {
   	   $CHOOSE = "<input type=hidden name=type_grid value='$gtopic'>";
   }
   
   
   if(!$bbcheck) {
   	   $MESSAGE = "Make a Griddle!";
   } else {
       $PROGRESS = "<h3>Griddle Progress:</h3>";
   
       $SOFAR  = "<h5>Pictures: " . getSoFar($bbcheck) . "</h5>";
       
       $PEOPLE = "<h5>People: " . getSoFarPeople($bbcheck) . "</h5>";
       
       $MESSAGE = "Add to this Griddle!";
       $bi = getGriddleInfo($bbcheck);
       $buid = $bi{'uid'};
       if($uid==$buid) { // Owner
          $OWNER = "<form action=/do_addcolabs.php method=post>
                     <input type=hidden name=action value='add'>
                     <input type=hidden name=bbid value='$bbcheck'>
                     <select class='form-control' name='colabs[]' multiple>
   	  	                <option value='' disabled selected>Choose More Friends to Help!</option>
   	  	                $sel_friend
   	  	                </select><br>
   	  	                <button type=submit class='btn btn-md btn-success'><span class='glyphicon glyphicon-plus'></span> Add Friends</button>
   	  	            </form><br>";
   	  	  $MESSAGE = "Include more Friends!";          
       
       } else {
          $CHOOSE .= "
   	       <div id='chooseFriend' class='span4'><br>
   	  	    <select class='form-control' name='colabs[]' multiple>
   	  	       <option value='' disabled selected>Choose some Friends to Help!</option>
   	  	       $sel_friend
   	  	       </select><br>
   	  	   </div>";
   	  	}  
       
       
   }
     

   if($MOBILE || $TABLET) {
      $SINGLETON = "<span class='btn btn-success fileinput-button'>
                    <i class='glyphicon glyphicon-plus'></i>
                    <span>Take Photo</span>
                    <input type='file' name='files[]'>
                </span>";
   }

   include "header.php";
   print "<body>\n";
   include "navbar.php";


?>

<div class="container">
    <div class="row row-offcanvas row-offcanvas-right">

      <div class="col-xs-12 col-sm-9 col-lg-10 pull-right">
        <!-- The file upload form used as target for the file upload widget -->
    	
        <!-- Redirect browsers with JavaScript disabled to the origin page -->
        <!-- The fileupload-buttonbar contains buttons to add/delete files and start/cancel the upload -->
        <?php echo $PROGRESS; ?>
        <?php echo $SOFAR; ?>
        <?php echo $PEOPLE; ?>
        <h3><?php echo $MESSAGE; ?></h3>
        <?php echo $OWNER; ?>
        <form id="fileupload" action="/uploadindex.php" method="POST" enctype="multipart/form-data">
    	<input type=hidden name=geo id=geo>
    	<input type=hidden name=purpose id=purpose value='<?php echo $purpose; ?>'>
    	<input type=hidden name=bbcheck value='<?php echo $bbcheck; ?>'>
        <?php echo $CHOOSE; ?>
   	  	<?php echo $TEXTBOX; ?>
   	  	<input type='hidden' name='qid' value='<?php echo $qid; ?>'>
   	  	
   	        <div class="fileupload-buttonbar">
             <!-- The fileinput-button span is used to style the file input field as button -->
                <h3>Add Pictures!</h3>
                <span class="btn btn-success fileinput-button">
                    <i class="glyphicon glyphicon-plus"></i>
                    <span>Choose Existing</span>
                    <input type="file" name="files[]" multiple>
                </span>
                <?php echo $SINGLETON; ?>
                <button type="submit" class="btn btn-primary start">
                    <i class="glyphicon glyphicon-upload"></i>
                    <span>Post!</span>
                </button>
                <!-- <button type="reset" class="btn btn-warning cancel">
                    <i class="glyphicon glyphicon-ban-circle"></i>
                    <span>Cancel upload</span>
                </button>
                <button type="button" class="btn btn-danger delete">
                    <i class="glyphicon glyphicon-trash"></i>
                    <span>Delete</span>
                </button>
                <input type="checkbox" class="toggle"> -->
                <!-- The loading indicator is shown during file processing -->
                <span class="fileupload-loading"></span>
            </div>
            <!-- The global progress information -->
        <div class="col-lg-10 fileupload-progress fade">
                <!-- The global progress bar -->
                <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100">
                    <div class="progress-bar" style="width:0%;"></div>
                </div>
                <!-- The extended global progress information -->
                <!-- <div class="progress-extended">&nbsp;</div> -->
        
        </div>
        <table role="presentation" class="table table-striped"><tbody class="files"></tbody></table>
        </div>
        <!-- The table listing the files available for upload/download -->
        
    
    <br>
 
   </form>
   
     <?php include "sidebar.php"; ?>
     </div>
         
  </div><!--/span-->

  

<?php include "jsinc.php"; ?>

<!-- The template to display files available for upload -->
<script id="template-upload" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-upload fade">
        <td>
            <span class="preview"></span>
        </td>
        <td>
            <p class="name">{%=file.name%}</p>
            {% if (file.error) { %}
                <div><span class="label label-danger">Error</span> {%=file.error%}</div>
            {% } %}
        </td>
        <td>
            <p class="size">{%=o.formatFileSize(file.size)%}</p>
            {% if (!o.files.error) { %}
                <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"><div class="progress-bar" style="width:0%;"></div></div>
            {% } %}
        </td>
        <td>
            {% if (!o.files.error && !i && !o.options.autoUpload) { %}
                <button class="btn btn-primary start">
                    <i class="glyphicon glyphicon-upload"></i>
                    <span>Start</span>
                </button>
            {% } %}
            {% if (!i) { %}
                <button class="btn btn-warning cancel">
                    <i class="glyphicon glyphicon-ban-circle"></i>
                    <span>Cancel</span>
                </button>
            {% } %}
        </td>
    </tr>
{% } %}
</script>
<!-- The template to display files available for download -->
<script id="template-download" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-download" style="display: none;">
        <td>
            <span class="preview">
                {% if (file.thumbnailUrl) { %}
                    <a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" data-gallery><img src="{%=file.thumbnailUrl%}"></a>
                {% } %}
            </span>
        </td>
        <td>
            <p class="name">
                {% if (file.url) { %}
                    <a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" {%=file.thumbnailUrl?'data-gallery':''%}>{%=file.name%}</a>
                {% } else { %}
                    <span>{%=file.name%}</span>
                {% } %}
            </p>
            {% if (file.error) { %}
                <div><span class="label label-danger">Error</span> {%=file.error%}</div>
            {% } %}
        </td>
        <td>
            <span class="size">{%=o.formatFileSize(file.size)%}</span> 
        </td>
        <td>
            {% if (file.deleteUrl) { %}
               <button class="btn btn-danger delete" data-type="{%=file.deleteType%}" data-url="{%=file.deleteUrl%}"{% if (file.deleteWithCredentials) { %} data-xhr-fields='{"withCredentials":true}'{% } %}>
                    <i class="glyphicon glyphicon-trash"></i>
                    <span>Delete</span>
                </button>
                <input type="checkbox" name="delete" value="1" class="toggle">
            {% } else { %}
                <button class="btn btn-warning cancel">
                    <i class="glyphicon glyphicon-ban-circle"></i>
                    <span>Cancel</span>
                </button>
            {% } %}
        </td>
    </tr>
{% } %}
</script>

<script>
</script>
   <script>
   GEO_Callback();
   document.getElementById("geo").value = geo;
   </script>
  

</body>

<?php

function getSoFar($bbid) {
   
    $bi = getGriddleInfo($bbid);
    
    $plist = explode(",", $bi{'ppid'});

    foreach ($plist as $pid) {
       $pi = getPostInfo($pid);
       $img = $pi{'images'};
       $OUT .= "<img class='soFarImg' src='$imgSRV/thumb_images/$img'>&nbsp;&nbsp;";
       $count++;
    }
    
    if($count<9) {
        $left = (9 - $count);
        for($i=0; $i<$left; $i++) {
            $OUT .= "<img class='soFarImg' src='$baseSRV/img/profile.jpg'>&nbsp;&nbsp;";
        }
    }
    
    return $OUT;   
   
   
}   

function getSoFarPeople($bbid) {
   
   $bi = getGriddleInfo($bbid);
   $col = $bi{'colabs'};
   $inv = $bi{'uid'};

   $ULIST = explode(",", "$inv,$col");
   foreach ($ULIST as $cuid) {
      if($cuid) { 
         $cui = getUserInfo($cuid);
         $cn  = $cui{'name'};
         $cu  = $cui{'username'};
         if($DONE[$cn] != 1) {
            $byline .= "$cn, ";
            $proline .= "<a href='/person.php?target=$cuid'><img class='cropimgProTiny' src='$imgSRV/thumb_profiles/$cu'></a>&nbsp;";
            $procount++;
            $DONE[$cn] = 1;
         }
      }
   }

   return $proline;

}
