var geo;
var fshow;

$(document).ready(function() {

  // This is a general purpose function for displaying a dynamic-content modal
  $("body").on('click', 'a.doModal', function(event) {
    event.preventDefault();
    
    //event.stopPropagation();
    $('.modal-body').html('');
    //$('.modal-body').addClass('loader');
    $('#myModal').modal('show');
    var url = $(this).attr('href');
    $.get(url, function(data) {

          //$('.modal-body').removeClass('loader');
          $('.modal-body').html(data);

      });
    });

  // This function handles the up and down voting of a griddle (only up for now)
  $('body').on('click', 'a.upHot, a.downHot', function (event)
  {
    event.preventDefault();
    event.stopPropagation();
    var url = $(this).attr('href');
    $.get(url, function(data) {
       HObj = JSON.parse(data);
       $("a#aHot" + HObj.bbid).html(HObj.hots + HObj.content);
    });
  });
  
   // This function handles the up and down voting of a griddle (only up for now)
  $('body').on('click', 'a.upHotp, a.downHotp', function (event)
  {
    event.preventDefault();
    event.stopPropagation();
    var url = $(this).attr('href');
    $.get(url, function(data) {
       HObj = JSON.parse(data);
       if(HObj.clear==1) {
           $("a.upHotp").html("<span class='glyphicon glyphicon-lock'></span>");
       }
       $("a#aHotp" + HObj.pid).html(HObj.hots + HObj.content);
    });
  });

  $('[data-toggle=offcanvas]').click(function() {
    window.scrollTo(0, 1);
    $('.row-offcanvas').toggleClass('active');
  });
  
  // This will initialize the auto scroll on the main feed
  $('.scroll').jscroll({autoTrigger: true, padding: 200});

  // This function handles the Relationship Requests.
  $('body').on('click', 'a.relLink', function(event) { 
      event.preventDefault(); 
      event.stopPropagation(); 
      var url = $(this).attr('href'); 
      $.get(url, function(data) { 
         HObj = JSON.parse(data);
         $('#relLink' + HObj.target).html(HObj.content);  
      }); 
  });

  $('body').on('click', 'a.actLink', function(event) {
      event.preventDefault(); 
      event.stopPropagation(); 
      var url = $(this).attr('href'); 
      $.get(url, function(data) { 
               
         $('#noteDiv').html(data); 
                               
      }); 
  });

  $("body").on('click', 'a.gRemove', function(event) {
    event.preventDefault();
    event.stopPropagation();
    
    var url = $(this).attr('href');
    $.get(url, function(data) {
      location.reload();
    });
  });


  $('body').on('click', 'a.FBSHARE', function (event) {
    event.preventDefault();
    event.stopPropagation();
   
    $theForm = $('#formFBShare');
    
    $('#FBLoader').show();
    
     // handle the response
     $.get('/do_fbsession.php', function(data) {
        $.ajax({
           type: $theForm.attr('method'),
           url: $theForm.attr('action'),
           enctype: $theForm.attr('enctype'),
           data: $theForm.serialize(),
           success: function(data) {
              // TODO - update the count
              HObj = JSON.parse(data);
              $("a#aFB" + HObj.bbid).html(HObj.fbshare + " <i class='fa fa-facebook-square'></i>");
              $('#FBLoader').hide();
              $('#myModal').modal('hide');
           }
        });
     });
    
  });
 
  
  $("body").on('click', 'a.TWITTER', function(event) {
    event.stopPropagation();
    
    var destination = $(this).attr('url');
    
    var url = $(this).attr('tw_upload');
    $.get(url, function(data) {
         HObj = JSON.parse(data);
          $("a#aTW" + HObj.bbid).html(HObj.twshare + " <i class='fa fa-twitter-square'></i>");
          window.location.href = destination;
      });
  });

  $("body").on('click', 'a.choose', function(event) {
    event.stopPropagation();
    
    var pgoal = $(this).attr('pgoal');
    document.getElementById("pgoal").value = pgoal; 
    
    $("#chooseType").html("<h5>It will take " + pgoal + " images to make this Griddle.  You can invite your friends to help!  Once the Griddle reaches " + pgoal + " images, it gets sent to everyone!</h5>");
    $("#hideForm").show();
    
   
  });

  $("body").on('click', 'a.showGriddle', function(event) {
     event.stopPropagation();
     event.preventDefault();
     var bbid = $(this).attr('bbid');
     
     $("#hide" + bbid).slideDown();
  });

  $("body").on('click', 'a.showRelated', function(event) {
     event.stopPropagation();
     event.preventDefault();
     var bbid = $(this).attr('bbid');
     
     $("#hide" + bbid).slideUp();
     setTimeout(function() {
         $(".hiddenFor" + bbid).slideDown();
      }, (1 * 1000));
     
     
  });


  $('body').on('click', 'a.FBLOGIN', function (event)
  {
     event.preventDefault();
     event.stopPropagation();
     FB.login(function(response) {
        // handle the response
        FB_Callback();
     }, {scope: 'user_about_me,user_photos,user_status,read_stream,publish_actions'});
  });

  initPost();
  initComm();
  initMore();
  initSearch();
  initIOSBugHack();
  
  if(fshow==1) {
     console.log("into frescoshow");
     fresco_show();
  }
  
  if(FBAttempt==0) {
     $('#FBCon').html("<i class='fa fa-facebook-square'></i>&nbsp; Connect with Facebook</a>");
  }
  
});

function initIOSBugHack() {
    // hack for iPhone 7.0.3 multiselects bug
    if(navigator.userAgent.match(/iPhone/i)) {
        $('select[multiple]').each(function(){
            var select = $(this).on({
                "focusout": function(){
                    var values = select.val() || [];
                    setTimeout(function(){
                        select.val(values.length ? values : ['']).change();
                    }, 1000);
                }
            });
            /* var firstOption = '<option value="" disabled="disabled"';
            firstOption += (select.val() || []).length > 0 ? '' : ' selected="selected"';
            firstOption += '>Choose some Friends to Help!';
            firstOption += '</option>';
            select.prepend(firstOption); */
        });
    }


}

function initMore() {

$('body').on('click', 'a.doMore', function (event)
{
   event.preventDefault();
   event.stopPropagation();
   
   var url = $(this).attr('href');
   var elmA = $(this).attr('elmAppend');
   
   $.get(url, function(data) {
      $(elmA).append(data);
   });

 });

$('body').on('click', 'a.doMoreTop', function (event)
{
   event.preventDefault();
   event.stopPropagation();
   
   var url = $(this).attr('href');
   var elmA = $(this).attr('elmAppend');
   
   $.get(url, function(data) {
      $(elmA).prepend(data);
   });

 });

}



function initComm() {

$('#commentForm').on('keyup', function(e){
  if(e.which == 13 && !e.shiftKey) {
     console.log('Saw Enter');
     e.preventDefault();
     e.stopPropagation();
     // send xhr request

     $theForm = $('#commForm');
     $.ajax({
       type: $theForm.attr('method'),
       url: $theForm.attr('action'),
       data: $theForm.serialize(),
       success: function(data) {
           HObj = JSON.parse(data);	
           $('#commentForm').val('');
           $('#commDiv').html(HObj.comms);
           $("a#aComm" + HObj.bbid).html(HObj.comments + " <span class='glyphicon glyphicon-comment'></span>");
       }
     });
  }
});

$('#commFormSubmit').on('click', function(e){
    e.preventDefault();
     e.stopPropagation();
     // send xhr request
     
     $theForm = $('#commForm');
     $.ajax({
       type: $theForm.attr('method'),
       url: $theForm.attr('action'),
       data: $theForm.serialize(),
       success: function(data) {
           HObj = JSON.parse(data);
           $('#commentForm').val('');
           $('#commDiv').html(HObj.comms);
           $("a#aComm" + HObj.bbid).html(HObj.comments + " <span class='glyphicon glyphicon-comment'></span>");
       }
     });
  
});



}

function initSearch() {

$('#searchBox').on('keyup', function(e){
  if(e.which == 13 && !e.shiftKey) {
     console.log('Saw Enter');
     e.preventDefault();
     e.stopPropagation();
     // send xhr request

     $theForm = $('#searchFriends');
     $.ajax({
       type: $theForm.attr('method'),
       url: $theForm.attr('action'),
       data: $theForm.serialize(),
       success: function(data) {	
           $('#searchBox').val('');
           $('#searchDiv').html(data);
       }
     });
  }
});

$('#searchFriends').on('submit', function(e) {
   e.preventDefault();
     e.stopPropagation();
     // send xhr request

     $theForm = $('#searchFriends');
     $.ajax({
       type: $theForm.attr('method'),
       url: $theForm.attr('action'),
       data: $theForm.serialize(),
       success: function(data) {	
           $('#searchBox').val('');
           $('#searchDiv').html(data);
       }
     });
});





}

function initPost() {

    // Initialize the jQuery File Upload widget:
    $('#fileupload').fileupload({
        // Uncomment the following to send cross-domain cookies:
        //xhrFields: {withCredentials: true},
        disableImageResize: /Android(?!.*Chrome)|Opera/
           .test(window.navigator && navigator.userAgent),
        imageMaxWidth: 1024,
        url: '/uploadindex.php'
    });

    // Enable iframe cross-domain access via redirect option:
    $('#fileupload').fileupload(
        'option',
        'redirect',
        window.location.href.replace(
            /\/[^\/]*$/,
            '/cors/result.html?%s'
        )
    );

    if (window.location.hostname === 'blueimp.github.io') {
        // Demo settings:
        $('#fileupload').fileupload('option', {
            url: '//jquery-file-upload.appspot.com/',
            // Enable image resizing, except for Android and Opera,
            // which actually support image resizing, but fail to
            // send Blob objects via XHR requests:
            disableImageResize: /Android(?!.*Chrome)|Opera/
                .test(window.navigator.userAgent),
            maxFileSize: 5000000,
            acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i
        });
        // Upload server status check for browsers with CORS support:
        if ($.support.cors) {
            $.ajax({
                url: '//jquery-file-upload.appspot.com/',
                type: 'HEAD'
            }).fail(function () {
                $('<div class="alert alert-danger"/>')
                    .text('Upload server currently unavailable - ' +
                            new Date())
                    .appendTo('#fileupload');
            });
        }
    } else {
        // Load existing files:
        $('#fileupload').addClass('fileupload-processing');
        $('#fileupload').bind('fileuploadstop', function(e) {
        
           var postdone = "/do_postfinish.php?action=finish";
           $.get(postdone, function(data) {
                // TODO - Fix this
                window.location.href = "/feed.php";
           });
        
           
        });
        
        // Add a "processing" upon file add
        
        
        $.ajax({
            // Uncomment the following to send cross-domain cookies:
            //xhrFields: {withCredentials: true},
            url: $('#fileupload').fileupload('option', 'url'),
            dataType: 'json',
            context: $('#fileupload')[0]
        }).always(function () {
            $(this).removeClass('fileupload-processing');
        }).done(function (result) {
            $(this).fileupload('option', 'done')
                .call(this, $.Event('done'), {result: result});
        });
    }
}

function FB_Callback() {
    console.log("FB Callback");    
    $.get('/do_fbsession.php', function(data) {
    });
}

function checkForNewPost() {
    var url = "http://www.griddle.com/last_post.php";
    $.get(url, function(data) {
       if(data == "reload") { // todo - make sure there isn't a modal open
           location.reload();
       } else {
           t = setTimeout(function() { checkForNewPost() }, 180000);
       }
    });
}

function checkForNewNotes() {
     var url = "http://www.griddle.com/do_notes.php?action=count";
     $.get(url, function(data) {
       if(data < 1) {
         $('#noteText').text("");
         document.title = 'Griddle';
       } else {
         $('#noteText').text(data);
         document.title = 'Griddle - ' + data + ' New';
       }
       t2 = setTimeout(function() { checkForNewNotes() }, 60000);
     });
}

function GEO_Callback() {
   
   if(navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(function(position) {
      mypos = new google.maps.LatLng(position.coords.latitude,
                                       position.coords.longitude);
      console.log("Geo lat  - " + position.coords.latitude);
      console.log("Geo long - " + position.coords.longitude);
      geo = position.coords.latitude + "|" + position.coords.longitude;
      $.post( "/do_geoloc.php", { geo_lat: position.coords.latitude, geo_long: position.coords.longitude } );
     });
   }

}