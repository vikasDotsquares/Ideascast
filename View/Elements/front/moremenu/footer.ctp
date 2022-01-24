<footer class="footer clearfix">
	<div class="container">
		<div class="row">
			<div class="footer-content" >
				<?php

					$fb = $this->Common->sett('fb');
					$tw = $this->Common->sett('twitter');
					$lkin = $this->Common->sett('linkedin');

				?>
				<div class="col-sm-4 col-md-3 first geninfo">
					<ul>
						<li><a href="javascript:void(0);" style="cursor:pointer;">FOR MORE</a></li>
						<li><a href="javascript:void(0);" style="cursor:pointer;">INFORMATION</a></li>
						<li><a href="javascript:void(0);" style="cursor:pointer;">PLEASE CONTACT</a></li>
						<li><a href="mailto:info@Ideascast.com"><span class="f-green">E:</span> <span class="f-white">info@Ideascast.com</span></a></li>
						<li><a href="tel:+44 (0)2476 158 430"><span class="f-green">T:</span> <span class="f-white">+44 (0)2476 158 430</span></a></li>
					</ul>
				</div>

				<div class="col-sm-4 col-md-2">
					<ul>
					<?php if( $_SERVER['SERVER_NAME'] == 'jeera.ideascast.com' ) { ?>
						<li><a href="<?php echo $this->Html->url( SITEURL.'about', true ); ?>">ABOUT</a></li>
						<li><a href="<?php echo $this->Html->url( SITEURL.'why-jeera', true ); ?>">WHY OPUSVIEW</a></li>
						<li><a href="<?php echo $this->Html->url( SITEURL.'features', true ); ?>">FEATURES</a></li>
						<li><a href="<?php echo $this->Html->url( SITEURL.'request-demo', true ); ?>">DEMO</a></li>
					<?php } else if( $_SERVER['SERVER_NAME'] == 'www.opusview.com' || $_SERVER['SERVER_NAME'] == LOCALIP ) { ?>
						<li><a href="<?php echo $this->Html->url( SITEURL.'about', true ); ?>">ABOUT</a></li>
						<li><a href="<?php echo $this->Html->url( SITEURL.'why-jeera', true ); ?>">WHY OPUSVIEW</a></li>
						<li><a href="<?php echo $this->Html->url( SITEURL.'features', true ); ?>">FEATURES</a></li>
						<li><a href="<?php echo $this->Html->url( SITEURL.'request-demo', true ); ?>">DEMO</a></li>
						<li><a href="<?php echo $this->Html->url( SITEURL.'how-buy', true ); ?>">HOW TO BUY</a></li>
					<?php } else { ?>
						<li><a href="<?php echo $this->Html->url( SITEURL.'about', true ); ?>">ABOUT</a></li>
					<?php } ?>
					</ul>
				</div>
				<div class="col-sm-4 col-md-3">
					<ul>
						<?php if( $_SERVER['SERVER_NAME'] == 'jeera.ideascast.com' ) { ?>
							<li><a href="<?php echo $this->Html->url( SITEURL.'faq', true ); ?>">FAQ</a></li>
							<li><a href="<?php echo $this->Html->url( SITEURL.'downloads', true ); ?>">DOWNLOADS</a></li>
							<li><a href="<?php echo $this->Html->url( SITEURL.'contactus', true ); ?>">CONTACT US</a></li>
							<li><a href="<?php echo $this->Html->url( SITEURL.'blog', true ); ?>">BLOG</a></li>
						<?php } else if( $_SERVER['SERVER_NAME'] == 'www.opusview.com' || $_SERVER['SERVER_NAME'] == LOCALIP ) { ?>
							<li><a href="<?php echo $this->Html->url( SITEURL.'faq', true ); ?>">FAQ</a></li>
							<li><a href="<?php echo $this->Html->url( SITEURL.'downloads', true ); ?>">DOWNLOADS</a></li>
							<li><a href="<?php echo $this->Html->url( SITEURL.'contactus', true ); ?>">CONTACT US</a></li>
							<li><a href="<?php echo $this->Html->url( SITEURL.'blog', true ); ?>">BLOG</a></li>
							<li><a href="<?php echo $this->Html->url( SITEURL.'jeera-demo', true ); ?>">TRY</a></li>
						<?php } else { ?>
							<li><a href="<?php echo $this->Html->url( SITEURL.'contactus', true ); ?>">CONTACT US</a></li>
						<?php } ?>
					</ul>
				</div>

				<div class="col-sm-12 col-md-4">
                <div class="social-footer">
					<span>SOCIAL</span>
					<ul>
						<!--<li><a target="_blank" href="<?php echo $fb; ?>"><i class="fa fa-facebook"></i></a></li>-->
						<li><a target="_blank" href="<?php echo $tw; ?>"><i class="fa fa-twitter"></i></a></li>
						<li><a target="_blank" href="<?php echo $lkin; ?>"><i class="fa fa-linkedin"></i></a></li>
					</ul>
                    </div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="footer-bottom">
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
					<div class="col-sm-12 col-md-12">
                    <div class="row">
                   <div class="col-sm-12"> <div class="dots-bottom"> &nbsp;</div></div>
						<div class="col-sm-6 col-md-6">
							<ul class="footer-link-b">
								<li><a href="<?php echo $this->Html->url( SITEURL.'terms', true ); ?>">Terms of use</a></li>
								<li><a href="<?php echo $this->Html->url( SITEURL.'privacy', true ); ?>">Privacy Policy</a></li>
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

	</div>
</footer>
<?php if( !isset($_COOKIE['ideascvalid']) &&  $this->request->params['action'] != 'privacy'  ){ ?>
<div class="cookies-policy" id="webcookies">
	<div class="container">
		<form name="agreefrm" id="" method="post" >
			<div class="row">
				<div class="col-md-9 col-sm-8">
					<h3>We use cookies.</h3>
					<p>We use cookies to give you the best online experience. If you continue to use this site, you agree to the use of cookies. Please see our <a href="<?php echo SITEURL?>privacy">policy</a> for details.</p>
				</div>
				<input type="hidden" name="iCastAgree" value="checked" />
				<div class="col-md-3 col-sm-4"><button id="ideasagreed" name="iCastAgree" data-agree="1" type="button" class="agree-btn">Continue</button></div>
			</div>
		</form>
	</div>
</div>
<?php } ?>
<?php
if( $_SERVER['SERVER_NAME'] != 'www.opusview.com' && $_SERVER['SERVER_NAME'] != 'jeera.ideascast.com' ) {

	$whatINeed = explode(WEBDOMAIN, $_SERVER['HTTP_HOST']);
	$whatINeed = $whatINeed[0];
	$db = dbname;
	$db_user = dbuser;
	$db_pass = dbpass;
	$url = CHATURL.'/app/getdatauser/';

	?>

<script>

/*
 var date = new Date();
 var minutes = 1;
 date.setTime(date.getTime() + (minutes * 60 * 1000)); */



			 /* if($js_config.live_setting) {
    			 $.ajax({
                    url: "<?php echo $url; ?>",
                    data: {
                       domain : "<?php echo $whatINeed; ?>",

                    },
                    success: function(){

                    },
                    dataType: 'jsonp',
                    type: 'post',
    				crossDomain: true
                });
            } */
</script>
<?php } ?>
<script>
	$(function () {
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

			/* ================ Start Jquery for CONTACT for A DEMO	 section ============================= */
			$('body').delegate('#contactusdemoContatName', 'keypress', function(event){
				$('#contactusdemoContatName').css('border-color','');
			});
			$('body').delegate('#contactusdemoContactEmail', 'keypress', function(event){
				$('#contactusdemoContactEmail').css('border-color','');
			});

			$('body').delegate('#contactdemo', 'click', function(event){
			event.preventDefault();

				var $that = $(this);
				var demoContactNames = $('#contactusdemoContatName').val();
				var demoContactEmails = $('#contactusdemoContactEmail').val();
				var href = $js_config.base_url + 'pages/contactfordemo';

				if( $.trim(demoContactNames).length <= 0 && $.trim(demoContactEmails).length <= 0 ){
					$('#contactusdemoContatName').css('border-color','red');
					$('#contactusdemoContactEmail').css('border-color','red');
				} else if( $.trim(demoContactNames).length <= 0 ){
					$('#contactusdemoContatName').css('border-color','red');
				} else if( $.trim(demoContactEmails).length <= 0 || validateEmail(demoContactEmails) == false ){
					$('#contactusdemoContactEmail').css('border-color','red');
				} else {
					$('#contactusdemoContatName').css('border-color','');
					$('#contactusdemoContactEmail').css('border-color','');
					$.ajax({
							url: href,
							type: "POST",
							crossDomain: true,
							data: $.param({demoContactName:demoContactNames,demoContactEmail:demoContactEmails}),
							global: true,
							success: function (response) {
								$("#msghere").text(response).css('color','green');
								setTimeout(function(){
									$("#msghere").fadeOut('slow');
								}, 2000);
							}
					})
				}
			});
			/* ================ End jquery for CONTACT for A Demo section ============================= */

			$('body').delegate('#ideasagreed', 'click', function(event){

				var agreedval = $(this).data('agree');
				var href = $js_config.base_url + 'pages/sethome';

				$.ajax({
					url: href,
					type: "POST",
					crossDomain: true,
					data: $.param({iCastAgree:agreedval}),
					global: false,
					success: function (response) {
						console.log(response);
						if( response == true ){
							//$.cookie('ideascvalid', 1, { path: '/' });
							$("#webcookies").hide();
						}
					}
				})
			})

		})
		$.widget.bridge('uibutton', $.ui.button);

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

		// Twiteer JS for sharing your page
			window.twttr = (function(d, s, id) {
			  var js, fjs = d.getElementsByTagName(s)[0],
				t = window.twttr || {};
			  if (d.getElementById(id)) return t;
			  js = d.createElement(s);
			  js.id = id;
			  js.src = "https://platform.twitter.com/widgets.js";
			  fjs.parentNode.insertBefore(js, fjs);

			  t._e = [];
			  t.ready = function(f) {
				t._e.push(f);
			  };

			  return t;
			}(document, "script", "twitter-wjs"));

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

if( $_SERVER['SERVER_NAME'] != 'www.opusview.com' && $_SERVER['SERVER_NAME'] != 'jeera.ideascast.com' ) {
		$whatINeed = explode('.', $_SERVER['HTTP_HOST']);
		$whatINeed = $whatINeed[0];
		$db = dbname;
		$db_user = dbuser;
		$db_pass = dbpass;
		$url = 'https://'.$whatINeed.WEBDOMAIN.':90/app/getdatauser/';
?>


<script>

$(function () {
// if($js_config.live_setting) {
	/*  $.ajax({
			url: "<?php echo $url; ?>",
			data: {
			   domain : "<?php echo $whatINeed; ?>",

			},
			success: function(){

			},
			dataType: 'jsonp',
			type: 'post',
			crossDomain: true
		});
	}  */

});
</script>
<?php } ?>