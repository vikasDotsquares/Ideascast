<?php 
	$fb = $this->requestAction('/settings/sett/'.'fb');
	$tw = $this->requestAction('/settings/sett/'.'twitter');
	$lkin = $this->requestAction('/settings/sett/'.'linkedin');					
?>
<div class="smaill-banner-inner why-jeerr-banner4">
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
			<li><a href="<?php echo SITEURL?>why-jeera-focus" title="Our focus">Our focus</a></li>
			<li class="active"><a href="<?php echo SITEURL?>why-jeera-approach" title="Our approach">Our approach</a></li>
			<li><a href="<?php echo SITEURL?>why-jeera-solution" title="Our solution">Our solution</a></li>
		</ul>	
      </div>
    </div>
    <div class="col-sm-6">
      <div class="whyjeera-right">
        <h3>Our approach</h3>
        <div class="summary-small-text">DIGITIZATION: IMPROVING OPERATIONS</div>
		
		<div class="whyjeera-content">
			<ul>
				<li>Overcome the many challenges of coordinating the right people and aligning their work purpose to deliver on key business initiatives that drive <span>real value</span></li>
				<li>Generate insights into how resources are utilized, the knowledge that is shared, and the connections made to <span>achieve results</span></li>
				<li>Leverage social features evident in Facebook, LinkedIn and other popular social tools to increase <span>workplace productivity</span></li>				
			</ul>
		</div>
        <div class="socialiocn-approach">		
			<ul>
				<li><a  title="Facebook"><i class="fa fa-facebook"></i></a></li>
				<li><a  title="LinkedIn"><i class="fa fa-linkedin"></i></a></li>
				<li><a  title="Twitter"><i class="fa fa-twitter"></i></a></li>
				<li><a  title="Google+"><i class="fa fa-google-plus"></i></a></li>
				<li><a  title="instagram"><i class="fa fa-instagram"></i></a></li>
				<li><a  title="Pintrest"><i class="fa fa-pinterest-p"></i></a></li>
			</ul>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="whyjeera-approach">
	<div class="container">
		<div class="row">
        <div class="col-sm-12">  
			<h3>IMPROVING OPERATIONS</h3>
			<img src="<?php echo SITEURL;?>images/2017/improving-operations.png" alt="improving-operations" />			
		</div>
          <div class="col-sm-12">
          <div class="improving-operations">
          <ul>
          <li><a href="javascript:void(0);">INNOVATE</a></li>
          <li><a href="javascript:void(0);">SHARE</a></li>
          <li><a href="javascript:void(0);">COMMUNICATE</a></li>
          <li><a href="javascript:void(0);">ORGANIZE</a></li>
          </ul>
          </div>
        </div>
        </div>
	</div>
</div>
<div class="why-jeera">
  <div class="container">
    <div class="row">
      <div class="col-sm-12 col-md-4">       
      <div class="why-jeera-hading">
      <h2>WHY OpusView?  </h2>
     <h3> <span>THE</span> <br />
BENEFITS
</h3>
<a class="read-m" href="<?php echo SITEURL.'empoweringteamwork'?>"> TAKE A LOOK </a>
      </div>
      </div>
      <div class="col-sm-12 col-md-8">
      <div class="row">     
      <div class="jeera-right-iocn">
       <div class="col-xs-4 col-sm-4 text-right">
      <span class="iocn-bf"><img src="<?php echo SITEURL; ?>images/2017/business.png" /></span>
      </div>
        <div class="col-xs-4 col-sm-4 text-center">
      <span class="iocn-bf"><img src="<?php echo SITEURL; ?>images/2017/framework.png" /></span>
      </div>
        <div class="col-xs-4 col-sm-4 text-left">
      <span class="iocn-bf"><img src="<?php echo SITEURL; ?>images/2017/messaging.png" /></span>
      </div>
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
