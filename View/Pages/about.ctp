
<div class="smaill-banner-inner about-us">
<img class="img-responsive" src="<?php echo SITEURL?>images/2017/about-us.jpg" alt="" />
	<div class="smaill-banner-contant">
		
				<h2>About</h2>
		
	</div>
</div>
<div class="container">
	<div class="row">
		<div class="col-sm-6">
			<div class="about-left">
				<div class="about-address">
					<strong>IdeasCast</strong> established at the start of 2015. Headquartered in Coventry, United Kingdom with an office in Toronto, Canada.</div>
				<div class="country-image">
					<img class="img-responsive" src="<?php echo SITEURL?>images/2017/country.jpg" alt="" />
							<h5>Coventry Cathedral</h5>
						</div>
						<div class="aboutleft-text">
							<p>IdeasCast is a provider of enterprise social software to make working on projects and business initiatives more effective, successful and fun.</p>
							<p>Our commitment to you is we will build our
            products with you in mind; we would like to be
            your partner. It is through your successes that
            we will be successful.</p>
						 
						</div>
					</div>
				</div>
				<div class="col-sm-6">
					<div class="about-right">
						<h3>Management Team</h3>
						<ul class="management-team-about">
							<li>
								<span class="teampic"><img src="<?php echo SITEURL; ?>images/2017/ceo.png" /></span>
								<h4>CEO, Founder</h4>
								<h5>Bal Mattu</h5>
								<h4 class="destination">Software Innovator</h4>
								<p>Serial enovtrepreneur with extensive experience in the software industry.</p>
							</li>
							<!--<li>
								<h4>COO</h4>
								<h5>Bob Fedorciow</h5>
								<p>International business builder with
              exceptional serial sales and general
              management success</p>
							</li>-->
							<li>
								<span class="teampic"><img src="<?php echo SITEURL; ?>images/2017/cto.png" /></span>
								<h4>CTO</h4>
								<h5>Martin Shaw</h5>
								<h4 class="destination">Project and Technology Delivery</h4>
								<p>25 years experience delivering projects and running PMOs.</p>
							</li>
							<li>
								<span class="teampic"><img src="<?php echo SITEURL; ?>images/2017/vp.png" /></span>
								<h4>VP Product Management</h4>
								<h5>David Williams</h5>
								<h4 class="destination">Product Management</h4>
								<p>25 years of success building modern, innovative software.</p>
							</li>
							<li>
								<span class="teampic"><img src="<?php echo SITEURL; ?>images/2017/paul-jackson.png" /></span>
								<h4>Chief Architect</h4>
								<h5>Paul Jackson</h5>
								<h4 class="destination">Enterprise and Solutions Architect</h4>
								<p>20 years of implementing design and architecture deliverables.</p>
							</li>
						</ul>
					</div>
				</div>
			</div>
		</div>
		<div class="we-are-about" style="padding:0px 0px 20px;">
			<div class="container">
				<div class="row">
					<div class="col-sm-12"><h2>"We are passionate about empowering business teams with <br />technology and
        solutions that enable them to perform to <br />higher levels and do great things."</h2> </div>
				</div>
			</div>
		</div>
		<!--<div class="why-jeera">
			<div class="container">
				<div class="row">
					<div class="col-sm-12 col-md-4">       
						<div class="why-jeera-hading">
							<h2>WHY OpusView?  </h2>
							<h3>
								<span>THE</span>
								<br />
BENEFITS
							</h3>
							<a class="read-m" href="<?php echo SITEURL.'features'?>"> TAKE A LOOK </a>
						</div>
					</div>
					<div class="col-sm-12 col-md-8">
						<div class="row">     
							<div class="jeera-right-iocn">
								<div class="col-xs-4 col-sm-4 text-right">
									<span class="iocn-bf">
										<img src="<?php echo SITEURL; ?>images/2017/business.png" /></span>
										</div>
										<div class="col-xs-4 col-sm-4 text-center">
											<span class="iocn-bf">
												<img src="<?php echo SITEURL; ?>images/2017/framework.png" /></span>
												</div>
												<div class="col-xs-4 col-sm-4 text-left">
													<span class="iocn-bf">
														<img src="<?php echo SITEURL; ?>images/2017/messaging.png" /></span>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>-->
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
										<button type="button" class="close" data-dismiss="modal" aria-label="Close">
											<span aria-hidden="true">&times;</span>
										</button>
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