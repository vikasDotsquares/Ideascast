<?php if( $_SERVER['SERVER_NAME'] != 'www.ideascast.com' )  { ?>
<footer class="footer-fix">
	<div class="footer-our-view" style="text-transform: uppercase; font-size: 12px;">
		 Company Registered in England and Wales (number 9384490)
	</div>
</footer>
<?php  } else { ?>
<footer class="footer clearfix">
	<div class="container">
		<div class="row">

			<div class="footer-content" >
				<?php

					$fb = $this->Common->sett('fb');
					$tw = $this->Common->sett('twitter');
					$lkin = $this->Common->sett('linkedin');

				?>
				<?php   if( $_SERVER['SERVER_NAME'] == 'www.ideascast.com' || $_SERVER['SERVER_NAME'] == LOCALIP ) { ?>
				<div class="col-sm-4 col-md-3 first geninfo">
					<ul>
						<li><a href="javascript:void(0);" style="cursor:pointer;">FOR MORE</a></li>
						<li><a href="javascript:void(0);" style="cursor:pointer;">INFORMATION</a></li>
						<li><a href="javascript:void(0);" style="cursor:pointer;">PLEASE CONTACT</a></li>
						<li><a href="mailto:info@Ideascast.com"><span class="f-green">E:</span> <span class="f-white">info@Ideascast.com</span></a></li>
						<li><a href="tel:+44 (0)1926 354000"><span class="f-green">T:</span> <span class="f-white">+44 (0)1926 354000</span></a></li>
					</ul>
				</div>
				<?php } ?>
				<div class="col-sm-4 col-md-2">
					<ul>
					<?php   if( $_SERVER['SERVER_NAME'] == 'www.ideascast.com' || $_SERVER['SERVER_NAME'] == LOCALIP ) { ?>
						<li><a href="<?php echo $this->Html->url( SITEURL.'about', true ); ?>">ABOUT</a></li>
						<li><a href="<?php echo $this->Html->url( SITEURL.'why-opusview', true ); ?>">WHY OPUSVIEW</a></li>
						<li><a href="<?php echo $this->Html->url( SITEURL.'features', true ); ?>">FEATURES</a></li>
						<li><a href="<?php echo $this->Html->url( SITEURL.'request-demo', true ); ?>">DEMO</a></li>
						<li><a href="<?php echo $this->Html->url( SITEURL.'how-buy', true ); ?>">HOW TO BUY</a></li>
						<li><a href="<?php echo $this->Html->url( SITEURL.'partners', true ); ?>">PARTNERS</a></li>
					<?php } else { ?>
						<!--<li><a href="<?php echo $this->Html->url( SITEURL.'about', true ); ?>">ABOUT</a></li>-->
					<?php } ?>
					</ul>
				</div>
				<div class="col-sm-4 col-md-3">
					<ul>
						<?php  if( $_SERVER['SERVER_NAME'] == 'www.ideascast.com' || $_SERVER['SERVER_NAME'] == LOCALIP ) { ?>
							<li><a href="<?php echo $this->Html->url( SITEURL.'faq', true ); ?>">FAQ</a></li>
							<li><a href="<?php echo $this->Html->url( SITEURL.'downloads', true ); ?>">DOWNLOADS</a></li>
							<li><a href="<?php echo $this->Html->url( SITEURL.'contactus', true ); ?>">CONTACT US</a></li>
							<li><a href="<?php echo $this->Html->url( SITEURL.'blog', true ); ?>">BLOG</a></li>
							<li><a href="<?php echo $this->Html->url( SITEURL.'opusview-demo', true ); ?>">TRY</a></li>
							<li><a href="<?php echo $this->Html->url( SITEURL.'templates', true ); ?>">TEMPLATES</a></li>
						<?php } else { ?>
							<!--<li><a href="<?php echo $this->Html->url( SITEURL.'contactus', true ); ?>">CONTACT US</a></li>-->
						<?php } ?>
					</ul>
				</div>
				<?php   if( $_SERVER['SERVER_NAME'] == 'www.ideascast.com' || $_SERVER['SERVER_NAME'] == LOCALIP ) { ?>
				<div class="col-sm-12 col-md-4">
                <div class="social-footer">
					<span>SOCIAL</span>
					<ul>
					<!--	<li><a target="_blank" href="<?php echo $fb; ?>"><i class="fa fa-facebook"></i></a></li>-->
						<li><a target="_blank" href="<?php echo $tw; ?>"><i class="fa fa-twitter"></i></a></li>
						<li><a target="_blank" href="<?php echo $lkin; ?>"><i class="fa fa-linkedin"></i></a></li>
					</ul>
                    </div>
				</div>
					<?php }  ?>


			</div>

		</div>
		<?php   if( $_SERVER['SERVER_NAME'] == 'www.ideascast.com' || $_SERVER['SERVER_NAME'] == LOCALIP ) { ?>
		<div class="row">
			<div class="footer-bottom">
					<?php  // if( $_SERVER['SERVER_NAME'] == 'www.ideascast.com' || $_SERVER['SERVER_NAME'] == LOCALIP ) { ?>
					<div class="col-sm-12 col-md-12">
                    <div class="row">
						<div class="col-sm-6 col-md-6">
							<img class="img-responsive" src="<?php echo SITEURL?>images/2017/footer-logo.png" alt="footer-logo" />
						</div>
						<div class="col-sm-6 col-md-6">
                        <div class="copyright">
							<?php $copy = $this->requestAction('/settings/sett/'.'copy'); ?>
							<p>
							<?php echo nl2br($copy); ?>
							<br>
							&copy; <?php echo date('Y'); ?> IdeasCast Limited. All rights reserved.</p>
						</div>
                        </div>
					</div>
                    </div>
					<?php // } ?>
					<div class="col-sm-12 col-md-12">
                    <div class="row">
                   <div class="col-sm-12"> <div class="dots-bottom"> &nbsp;</div></div>
						<div class="col-sm-6 col-md-6">
							<ul class="footer-link-b">
								<li><a href="https://www.ideascast.com/terms">Terms of use</a></li>
								<li><a href="https://www.ideascast.com/privacy">Privacy Policy</a></li>
							</ul>
						</div>
						<div class="col-sm-6 col-md-6">
						<!-- 	<ul class="footer-link-site">
								<li><a href="#">Sitemap</a></li>
							</ul> -->
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php } ?>
	</div>
</footer>
<?php } ?>
<script>
	$(function () {

			 if ($("#successFlashMsgs").length > 0) {

                setTimeout(function () {
                    $("#successFlashMsg").animate({
                        opacity: 0,
                        height: 0
                    }, 1000, function () {
                        $(this).remove()
                    })

                }, 4000)

            }


				/* Search box Start */
					  /* var $searchlink = $('#searchtoggl i');
					  var $searchbar  = $('#searchbar');

					  $('#searchtoggl').on('click', function(e){
						e.preventDefault();

						if($(this).attr('id') == 'searchtoggl') {
						  if(!$searchbar.is(":visible")) {
							// if invisible we switch the icon to appear collapsable
							//$searchlink.removeClass('fa-search').addClass('fa-search-minus');
						  } else {
							// if visible we switch the icon to appear as a toggle
							//$searchlink.removeClass('fa-search-minus').addClass('fa-search');
						  }

						  $searchbar.slideToggle(400, function(){
								//callback after search bar animation
						  });
						}
					  });
					  $('#searchform').submit(function(e){
						e.preventDefault(); // stop form submission
					  }); */
				/* Search box End */


				$('.footer-content a.cd-top').click(function(e) {
						e.preventDefault()
						$('html, body').animate({
							scrollTop: 0
						}, 800)
				});

			 

		})


		// Function that validates email address through a regular expression.
		function validateEmail(sEmail) {
			var filter = /^[\w\-\.\+]+\@[a-zA-Z0-9\.\-]+\.[a-zA-z0-9]{2,4}$/;
			if (filter.test(sEmail)) {
			return true;
			}
			else {
			return false;
			}
		}

		

	</script>
	<?php  echo $this->Html->script(array('bootstrap.min',)); ?>
	<!-- <script  type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script> -->
	<?php  echo $this->Html->script(array(
       // '/plugins/morris/morris.min',
        '/plugins/sparkline/jquery.sparkline.min',
        '/plugins/jvectormap/jquery-jvectormap-1.2.2.min',
        '/plugins/jvectormap/jquery-jvectormap-world-mill-en',
        '/plugins/knob/jquery.knob',
        '/plugins/daterangepicker/daterangepicker',
        '/plugins/datepicker/bootstrap-datepicker',
        '/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min',
        '/plugins/iCheck/icheck.min',
        '/plugins/slimScroll/jquery.slimscroll.min',
        '/plugins/fastclick/fastclick.min',
        'app',
        'pages/dashboard',
        'demo',
        'jquery.flexslider',
        'front.custom'
        ));


?>
