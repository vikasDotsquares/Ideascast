<!-- <div class="smaill-banner-inner faq-banner">
  <div class="container">
    <div class="row">
      <div class="col-sm-12">
        <h2>Downloads</h2>
      </div>
    </div>
  </div>
</div> -->
<div class="smaill-banner-inner why-jeera-banner">
	<img class="img-responsive" src="<?php echo SITEURL?>images/2017/why-jeerr-banner3.jpg" alt="" />
	<div class="smaill-banner-contant">
        <h2>Downloads</h2>
    </div>    
</div>
<div class="pdf-downloads">
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <h2>Pdf Downloads</h2>
      </div>
      <div class="col-sm-4 col-md-3">
        <div class="pdf-text"> <a href="<?php echo SITEURL.'pages/downloads_doc/OpusViewBrochure2018.pdf'?>" ><img src="<?php echo SITEURL?>images/2017/brothumb.png" alt="OpusView brochure 2017"/></a>
          <h3>OpusView brochure</h3>
        </div>
      </div>
      <div class="col-sm-4 col-md-3">
        <div class="pdf-text"> <a href="<?php echo SITEURL.'pages/downloads_doc/OpusViewDigitalEnterprise2018.pdf'?>" ><img src="<?php echo SITEURL?>images/2017/degthumb.png" alt="OpusView View Digital Enterprise"/></a>
          <h3>OpusView digital enterprise</h3>
        </div>
      </div>
      <div class="col-sm-4 col-md-3">
        <div class="pdf-text"> <a href="<?php echo SITEURL.'pages/downloads_doc/OpusViewDataSheet2018.pdf'?>" ><img src="<?php echo SITEURL?>images/2017/datasheetthumbl.png" alt="OpusView data sheet"/></a>
          <h3>OpusView data sheet</h3>
        </div>
      </div>
      <div class="col-sm-4 col-md-3">
        <div class="pdf-text"> <a href="<?php echo SITEURL.'pages/downloads_doc/OpusViewLeadership2018.pdf'?>" ><img src="<?php echo SITEURL?>images/2017/ledthumb.png" alt="OpusView leadership"/></a>
          <h3>OpusView leadership</h3>
        </div>
      </div>
      <div class="col-sm-4 col-md-3">
        <div class="pdf-text"> <a href="<?php echo SITEURL.'pages/downloads_doc/OpusViewManufacturing2018.pdf'?>" ><img src="<?php echo SITEURL?>images/2017/manthumb.png" alt="OpusView manufacturing entreprise"/></a>
          <h3>OpusView manufacturing enterprise</h3>
        </div>
      </div>
    </div>
  </div>
</div>
<?php /* <div class="why-jeera">
  <div class="container">
    <div class="row">
      <div class="col-sm-12 col-md-4">
        <div class="why-jeera-hading">
          <h2>WHY OpusView? </h2>
          <h3> <span>THE</span> <br />
            BENEFITS </h3>
          <a class="read-m" href="<?php echo SITEURL.'empoweringteamwork'; ?>"> TAKE A LOOK </a> </div>
      </div>
      <div class="col-sm-12 col-md-8">
        <div class="row">
          <div class="jeera-right-iocn">
            <div class="col-xs-4 col-sm-4 text-right"> <span class="iocn-bf"> <img src="<?php echo SITEURL; ?>images/2017/business.png" /></span> </div>
            <div class="col-xs-4 col-sm-4 text-center"> <span class="iocn-bf"> <img src="<?php echo SITEURL; ?>images/2017/framework.png" /></span> </div>
            <div class="col-xs-4 col-sm-4 text-left"> <span class="iocn-bf"> <img src="<?php echo SITEURL; ?>images/2017/messaging.png" /></span> </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
 <div class="container">
  <div class="row">
    <div class="col-sm-12"> <?php echo $this->element('front/contactfordemo');?> </div>
  </div>
</div> */ ?>

<!-- ---------------- MODEL BOX INNER HTML LOADED BY JS ------------------------ -->

<div class="hide" > 
  <!-- POPUP MODEL BOX CONTENT HEADER -->
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
    <h3 class="modal-title" id="myModalLabel">POPUP MODAL HEADING</h3>
  </div>
  
  <!-- POPUP MODAL BODY -->
  <div class="modal-body">
    <h5 class="project-name"> popup box heading </h5>
  </div>
  
  <!-- POPUP MODAL FOOTER -->
  <div class="modal-footer">
    <button type="submit" class="btn btn-warning">Save changes</button>
    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
  </div>
</div>

<!-- ---------------- JS TO OPEN MODEL BOX ------------------------ --> 
<script type="text/javascript" >
    $('#myModal').on('hidden.bs.modal', function () {  
        $(this).removeData('bs.modal');
    });
	
	$(".btnDownload").click(function(e) {
	  e.preventDefault();	  
	  var csv_download_link = $(this).data('download');	  
		
		/* jQuery.ajax({
            url: jQuery(this).data("actionurl"),
            type: "POST",
            data: {filenames:csv_download_link},
            success: function (response) {
                if (jQuery.trim(response) != 'success') {

                } else {

                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                // Error Found
            }
        }); */
	  
	  
	  
	  /* //open download link in new page
	  window.open( csv_download_link );
	  //redirect current page to success page 
	  window.focus(); */
	  
	});
	
	
	//Submit Add Form 
      jQuery("#formID").submit(function (e) {
        var postData = jQuery(this).serializeArray();

        jQuery.ajax({
            url: jQuery(this).attr("action"),
            type: "POST",
            data: postData,
            success: function (response) {
                if (jQuery.trim(response) != 'success') {

                } else {

                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                // Error Found
            }
        });
        e.preventDefault(); //STOP default action 
    });  
</script> 
