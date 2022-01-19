<div class="smaill-banner-inner why-jeerr-banner3">
<img class="img-responsive" src="<?php echo SITEURL?>images/2017/why-jeerr-banner4.jpg" alt="" />
  <div class="smaill-banner-contant">
  
        <h2>Why OpusView?</h2>
     
  </div>
</div>
<div class="container">
  <div class="row">
    <div class="col-sm-6">
      <div class="whyjeera-left">
        <ul>
          <li><a href="<?php echo SITEURL?>why-jeera" title="In summary">In summary</a></li>
          <li><a href="<?php echo SITEURL?>why-jeera-benefits" title="The benefits">The benefits</a></li>
          <li class="active"><a href="<?php echo SITEURL?>why-jeera-focus" title="Our focus">Our focus</a></li>
          <li><a href="<?php echo SITEURL?>why-jeera-approach" title="Our approach">Our approach</a></li>
          <li><a href="<?php echo SITEURL?>why-jeera-solution" title="Our solution">Our solution</a></li>
        </ul>
      </div>
    </div>
    <div class="col-sm-6">
      <div class="whyjeera-right">
        <h3>Our focus</h3>
        <div class="summary-small-text">TO INCREASE EFFICIENCY & ENGAGEMENT SO STRATEGIES ARE BETTER BUILT</div>
        <div class="whyjeera-content">
          <ul>
            <li>Great ideas come from collaboration. But too often these ideas never make it from the whiteboard, post-it note or corridor
              conversation, <span>so value is lost forever</span></li>
            <li>Post workshop activities and interactions can fail to achieve the fullest desired value because harnessing team alignment <span>is difficult and impacts outcomes</span></li>
            <li>Because information and knowledge resides in many fragmented tools and apps, people are <span>slowed down</span></li>
            <li>Siloed data makes monitoring performance and understanding team progress a <span>significant challenge</span></li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="whyjeera-focus">
  <div class="container">
    <div class="row">
      <div class="col-sm-12">
        <h3>HIGHER PERFORMANCE</h3>
        <img src="<?php echo SITEURL;?>images/2017/higher-performance.png" alt="higher-performance" /> </div>
    </div>
  </div>
</div>
<div class="why-jeera">
  <div class="container">
    <div class="row">
      <div class="col-sm-12 col-md-4">
        <div class="why-jeera-hading">
          <h2>WHY OpusView? </h2>
          <h3> <span>THE</span> <br />
            BENEFITS </h3>
          <a class="read-m" href="<?php echo SITEURL.'empoweringteamwork'?>"> TAKE A LOOK </a> </div>
      </div>
      <div class="col-sm-12 col-md-8">
        <div class="row">
          <div class="jeera-right-iocn">
            <div class="col-xs-4 col-sm-4 text-right"> <span class="iocn-bf"><img src="<?php echo SITEURL; ?>images/2017/business.png" /></span> </div>
            <div class="col-xs-4 col-sm-4 text-center"> <span class="iocn-bf"><img src="<?php echo SITEURL; ?>images/2017/framework.png" /></span> </div>
            <div class="col-xs-4 col-sm-4 text-left"> <span class="iocn-bf"><img src="<?php echo SITEURL; ?>images/2017/messaging.png" /></span> </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php /* <div class="container">
  <div class="row">
    <div class="col-sm-12">
		<?php echo $this->element('front/contactfordemo');?>
    </div>
  </div>
</div> */ ?>

<!-- ---------------- MODEL BOX INNER HTML LOADED BY JS ------------------------ -->

<div class="hide" > 
  <!-- POPUP MODEL BOX CONTENT HEADER -->
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
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
	
// Submit Add Form 
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
