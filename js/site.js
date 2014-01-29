$(document).ready(function() {

  // This is a general purpose function for displaying a dynamic-content modal
  $("body").on('click', 'a.doModal', function(event) {
    event.preventDefault();
    console.log("Into Click");
    //event.stopPropagation();
    $('.modal-body').html('');
    //$('.modal-body').addClass('loader');
    $('#myModal').modal('show');
    var url = $(this).attr('href');
    $.get(url, function(data) {
          console.log("Into Get");
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
       $("a#aHot" + HObj.bbid).html(HObj.hots + " <span class='glyphicon glyphicon-thumbs-up'></span>");
    });
  });

  $('[data-toggle=offcanvas]').click(function() {
    window.scrollTo(0, 1);
    $('.row-offcanvas').toggleClass('active');
  });
  
  // This will initialize the auto scroll on the main feed
  $('.scroll').jscroll({autoTrigger: true, padding: 0});

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

  initPost();
  initComm();
  initMore();
  
});



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
                window.location.href = "http://dev01.griddle.com";
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
