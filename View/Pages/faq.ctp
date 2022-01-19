<div class="smaill-banner-inner why-jeera-banner">
<img class="img-responsive" src="<?php echo SITEURL?>images/2017/why-jeerr-banner4.jpg" alt="" />
  <div class="smaill-banner-contant">
 
        <h2>FAQ</h2>
     
  </div>
</div>
<div class="container">
  <div class="row">
    <div class="col-sm-6">
      <div class="whyjeera-left">
		<ul>
			<li><a href="<?php echo SITEURL?>why-jeera" title="In summary">In summary</a></li>
			<li><a href="<?php echo SITEURL?>why-jeera-benefits" title="The benefits">The benefits</a></li>
			<li class="active"><a href="<?php echo SITEURL?>faq" title="The benefits">FAQ</a></li>						
		</ul>	
      </div>
    </div>
    <div class="col-sm-6">
      <div class="whyjeera-right">
        <h3>FAQ</h3>
        <h4>GENERAL</h4>
		<div class="summary-small-text"></div>
		<div class="whyjeera-content">
			<h4>When can I start using OpusView?</h4>
			<p>OpusView is available now.</p>
			<h4>Can I download OpusView?</h4>
			<p>No, however we do support on-premise installations and will be happy to discuss this with you.</p>
			<h4>Do you offer reduced pricing to NPOs and Charities?</h4>
			<p>Yes, please contact us to learn more.</p>

			<h4>PRICING</h4>
			<h4>What is the cost of OpusView?</h4>
			<p>Please contact us to learn more.</p>
			<h4>How do I pay?</h4>
			<p>We will liaise with your purchasing department.</p>
			<h4>How long is the contract/agreement?</h4>
			<p>You will be able to use OpusView for as long as you want. <br><span>Billing is on a 12 month subscription basis.</span></p>

			<!-- <h4>SECURITY</h4>
			<h4>Information about hosting</h4>
			<p>As well as on-premise we offer SaaS from a UK based Data Centre providing a flexible and scalable infrastructure to support your needs.</p>
			<h4>Information about security</h4>
			<p>Information exchange is underpinned with SSL and database back-ups taken on behalf of customers are fully secured on our back-up servers. Further, the back-ups servers are connected to our private network.</p>			
			<h4>Information and vulnerabilities</h4>
			<p>The OpusView infrastructure components, including database and web servers, are continuously monitored per environment for the latest security updates. If there are any service impacting updates we will inform customers before applying them.</p>
			<h4>Information about access</h4>
			<p>All customer data is considered by Ideascast to be private and confidential. Therefore we have implemented robust identity and access management processes and mechanisms.</p> -->
					
		</div>
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
