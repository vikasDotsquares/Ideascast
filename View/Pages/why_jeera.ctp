<div class="smaill-banner-inner why-jeera-banner">
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
			<li class="active"><a href="<?php echo SITEURL?>why-jeera" title="In summary">In summary</a></li>
			<li><a href="<?php echo SITEURL?>why-jeera-benefits" title="The benefits">The benefits</a></li>
			<li><a href="<?php echo SITEURL?>faq" title="The benefits">FAQ</a></li>			
		</ul>	
      </div>
    </div>
    <div class="col-sm-6">
      <div class="whyjeera-right">
        <h3>In summary</h3>
        <div class="summary-small-text">BUSINESS SOCIAL SOFTWARE FOR THE MODERN WORKFORCE</div>
		
		<div class="whyjeera-content">
			<!-- <h3>Why Jeera?</h3>-->
			<ul>
				<li>Power and flexibility to create high performance collaborative applications</li>
				<li>Full set of business-oriented tools that reduce the need to toggle between applications</li>
				<li>Working together and improving business decisions effortlessly</li>
			</ul>
		</div>
      </div>
    </div>
  </div>
</div>
<div class="container">
	<div class="row">
		  <div class="col-sm-12">
			  <div class="intelligent-team-sec" style="margin-bottom:40px;">
				<h2>Agile teamworking, for any business</h2>
				<div class="intelligent-team"><img class="img-responsive" src="<?php echo SITEURL?>images/2017/teamworking/intelligent-teamworking.png" alt="intelligent-teamworking" /></div>
			  </div>
		  </div>
	</div>
</div>
<!-- <div class="whyjeera-benefits">

		<div class="container">
			<div class="row">
				<div class="col-md-12"><h2>The benefits <span>in summary</span></h2></div>
				<div class="col-sm-6 col-md-3">	
					<div class="benefits-text">
					<span class="whyiocn-bf"><img src="<?php echo SITEURL?>images/2017/social.png" alt="social"/></span>
						<h3>SOCIAL COLLABORATIVE PLATFORM</h3>							
						<p>With a more accessible, intuitive interface encouraging deeper, purposeful crossfunctional engagement</p>
					</div>
				</div>
				<div class="col-sm-6 col-md-3">	
					<div class="benefits-text">
					<span class="whyiocn-bf"><img src="<?php echo SITEURL?>images/2017/highly.png" alt="highly"/></span>
						<h3>HIGHLY CUSTOMIZABLE WORKFLOW COORDINATION</h3>								
						<p>Extensive capabilities for users to streamline business initiatives maximizing opportunities and
minimizing risks</p>
					</div>
				</div>
				<div class="col-sm-6 col-md-3">	
					<div class="benefits-text">
					<span class="whyiocn-bf"><img src="<?php echo SITEURL?>images/2017/talent-extending.png" alt="talent-extending"/></span>
						<h3>PROJECT SERVICES, SINGLE-STACK SUITE</h3>								
						<p>Supporting tasks from knowledge-sharing to mapping of targets and results, and strategic organization of information</p>
					</div>
				</div>				
				<div class="col-sm-6 col-md-3">	
					<div class="benefits-text">
					<span class="whyiocn-bf"><img src="<?php echo SITEURL?>images/2017/flexible-deployment.png" alt="flexible-deployment"/></span>
						<h3>FLEXIBLE DEPLOYMENT OPTIONS</h3>
						<p>On-premise, private cloud or SaaS</p>
					</div>
				</div>
			</div>
		</div>
	</div>

<div class="whyjeera-opportunities">
    <div class="why-jeera-opportunities-h"><h2>A world <span> of opportunities</span></h2></div>
	<img src="<?php echo SITEURL;?>images/2017/why-jeera-opportunities.jpg" alt="A world of opportunities" />
	
</div>	-->
	
<?php /* <div class="container">
		<div class="row">
			<div class="col-sm-12">	
				<?php echo $this->element('front/contactfordemo');?>
			</div>			
		</div>
	</div>*/ ?>



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
