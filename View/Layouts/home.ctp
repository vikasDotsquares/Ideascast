<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Layouts
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">

<title><?php echo (isset($title_for_layout)&&!empty($title_for_layout)?$title_for_layout:'IdeasCast');?></title>
  <meta name="description" content="<?php echo (isset($keywords_for_layout)&&!empty($keywords_for_layout)?$keywords_for_layout:'IdeasCast');?>">
    <meta content="<?php if(isset($description_for_layout)  && !empty($description_for_layout)){ echo $description_for_layout; }else{ echo 'IdeasCast'; } ?>" name="keywords"/>
	<?php echo $this->element('front/head'); ?>
<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no">

<!--[if IE 9]> <link href="<?php echo SITEURL; ?>css/only_ie.css"> <![endif]-->
<!--[if IE 10]> <link href="<?php echo SITEURL; ?>css/only_ie.css"> <![endif]-->
<!--[if IE 11]> <link href="<?php echo SITEURL; ?>css/only_ie.css"> <![endif]-->
<style>

_:-ms-fullscreen, :root #idea_video{
		max-width:85% !important;
		max-height:85% !important;
		border-bottom:1px solid #999;
		border-right:1px solid #999;
	}

_:-ms-fullscreen, :root .video_icon{ right:-65px }

</style>
</head>
<body class="<?php echo isset($is_home)?$is_home:'inner_page_view';?> home">

<?php echo $this->element('front/header'); ?>
	<?php  echo $this->Session->flash();?>
<div id="ajax_overlay" class="ajax_overlay_preloader" style="display:none">
            <div id="" class="gif_preloader" style="">
                <div id="" class="loading_text" style="">Loading..</div>
            </div>
        </div>

<div class="slider clearfix">
	<?php echo $this->element('front/flexslider'); ?>
</div>
<div class="main clearfix">
<div class="welcome-outer">
	<div class="container">
		<div class="row">
			<div class="col-sm-6">
				<div class="welcome-text">
					<!--<h2>OpusView</h2>-->
					<h3><a href="javascript:;" style="cursor: default;">Efficient Teams. Better Outcomes.</a></h3>
					 <p> OpusView will optimize team coordination and working, <br />simplify communication, and provide instant visibility of <br /> project costs and risks. With OpusView achieve outstanding <br /> results with improved project monitoring and control. <?php if( $_SERVER['SERVER_NAME'] == 'jeera.ideascast.com' || $_SERVER['SERVER_NAME'] == SERVER_NAME ) { ?> <br /> <span class="featurelink"><a style="color:#6eb243;" href="<?php echo SITEURL?>features">Features <i class="fa fa-arrow-right" style="color:#6eb243;"></i></a></span><?php } ?></p>
				</div>

			</div>
			<div class="col-sm-6">
				<ul class="homethreebutton">
					<li><div class="threebutton"><span class="count">1</span><span class="butbg"><img src="<?php echo SITEURL?>/images/2017/plan.png" alt="plan"></span></div></li>
					<li><div class="threebutton"><span class="count">2</span><span class="butbg"><img src="<?php echo SITEURL?>/images/2017/collaborate.png" alt="collaborate"></span></div></li>
					<li> <div class="threebutton"> <span class="count">3</span><span class="butbg"><img src="<?php echo SITEURL?>/images/2017/execute.png" alt="execute"></span></div></li>
				</ul>
			</div>
		</div>
	</div>
</div>
<div class="all-solution-heading" style="margin:0px 0px 30px 0px;">
    <div class="container">
      <div class="row">
        <div class="col-sm-12">
          <h2>Create best practice templates aligned to leading methodologies such
as PRINCE2 <br /> and PMI to rapidly set up and deliver effective project management.</h2>
		</div>
      </div>
    </div>
  </div>
<div class="jeera-glance">
 <div class="container">
  <div class="row">
    <div class="col-md-12 text-center"><h2>OpusView at a glance</h2> </div>
    <ul>
    <li>
	<div class="glance-text">
    <span class="iocn-glance"><img src="<?php echo SITEURL?>/images/2017/iocn-glance-one.png" alt="iocn-glance-one"></span>
    <p>Executives and project leaders easily access real-time performance and status information.</p>
    </div>
    </li>
     <li>
	<div class="glance-text">
    <span class="iocn-glance"><img src="<?php echo SITEURL?>/images/2017/iocn-glance-two.png" alt="iocn-glance-two"></span>
    <p>Coordinate teams and stakeholders, and align their work purpose to priorities and goals.</p>
    </div>
    </li>
         <li>
	<div class="glance-text">
    <span class="iocn-glance"><img src="<?php echo SITEURL?>/images/2017/iocn-glance-three.png" alt="iocn-glance-three"></span>
    <p>Full set of productivity tools removing the need
to toggle between multiple apps.</p>
    </div>
    </li>

      <li>
	<div class="glance-text">
    <span class="iocn-glance"><img src="<?php echo SITEURL?>/images/2017/iocn-glance-four.png" alt="iocn-glance-four"></span>
    <p>Break down large initiatives into
smaller projects to simplify execution
and delivery.</p>
    </div>
    </li>

       <li>
	<div class="glance-text">
        <span class="iocn-glance"><img src="<?php echo SITEURL?>/images/2017/iocn-glance-five.png" alt="iocn-glance-five"></span>
    <p>Build your own or utilize out-of-the-box templates to rapidly set up projects.</p>
    </div>
    </li>
    </ul>
		<?php if( $_SERVER['SERVER_NAME'] == 'www.ideascast.com' || $_SERVER['SERVER_NAME'] == LOCALIP ) { ?>
	  <div class="request-button-wrap">
		  <div class="request-button requestdemoone"> &nbsp; </div>
			<div class="request-button"><a href="<?php echo SITEURL.'request-demo'?>">REQUEST DEMO</a></div>
			<div class="request-button"><a href="<?php echo SITEURL.'how-buy'?>">REQUEST PRICING</a></div>
			<div class="request-button"><a href="<?php echo SITEURL.'jeera-demo'?>">REQUEST TRIAL</a></div>
	  </div>
		<?php } else { ?>
			<div class="higher-success-text">Superior Project Delivery for Higher Success</div>
		<?php } ?>

     </div>
    </div>
</div>
</div>

<!-- New Section  21 May 2018 -->

	<div class="all-solution-heading">
    <div class="container">
      <div class="row">
        <div class="col-sm-12">
          <h2>All-in-one solution where social working meets project management <br />meets real-time communications.</h2>
		</div>
      </div>
    </div>
  </div>
  <div class="all-solution">
    <div class="container">
      <div class="row">
      <div class="all-solution-sec">
        <div class="col-sm-6">
          <div class="project-intelligence-image magnify"><div class="large"></div><img class="img-responsive magniflier" src="<?php echo SITEURL?>images/2017/teamworking/Picture1.png" alt="" /></div>
        </div>
        <div class="col-sm-6">
          <div class="project-intelligence">
            <h3>Project Intelligence</h3>
            <ul>
              <li>Virtual assistant delivers understanding faster.</li>
              <li>No need to search for key information.</li>
              <li>Intelligence from all your work and projects.</li>
            </ul>
          </div>
        </div>
        </div>
        <div class="all-solution-sec">
        <div class="col-sm-6 right-pull">
          <div class="project-intelligence-image magnify"><div class="large"></div><img class="img-responsive magniflier" src="<?php echo SITEURL?>images/2017/teamworking/Picture2.png" alt="" /></div>
        </div>
        <div class="col-sm-6">
          <div class="project-intelligence">
            <h3>Manage and monitor activities</h3>
            <ul>
              <li>Keep teams coordinated and working optimally.</li>
              <li>Spot resourcing opportunities to get work done faster.</li>
              <li>View activity schedules, current status and reminders.</li>
            </ul>
          </div>
        </div>
        </div>
        <div class="all-solution-sec">
        <div class="col-sm-6">
          <div class="project-intelligence-image magnify"><div class="large"></div><img class="img-responsive magniflier" src="<?php echo SITEURL?>images/2017/teamworking/Picture3.png" alt="" /></div>
        </div>
        <div class="col-sm-6">
          <div class="project-intelligence">
            <h3>Communication made simple</h3>
            <ul>
              <li>Teams conversations are organized and easily accessible.</li>
              <li>Real-time messaging and voice calls to share viewpoints and knowledge.</li>
              <li>Remove communication latency between stakeholders.</li>
            </ul>
          </div>
        </div>
        </div>
        <div class="all-solution-sec">
        <div class="col-sm-6 right-pull">
          <div class="project-intelligence-image magnify"><div class="large"></div><img class="img-responsive magniflier" src="<?php echo SITEURL?>images/2017/teamworking/Picture4.png" alt="" /></div>
        </div>
        <div class="col-sm-6">
          <div class="project-intelligence">
            <h3>Plan and streamline workflows</h3>
            <ul>
              <li>Visualize and share tasks with internal and external stakeholders.</li>
              <li>Align work activity to goals and strategy. </li>
              <li>Manage changes and updates to requirements.</li>
            </ul>
          </div>
        </div>
        </div>
        <div class="all-solution-sec">
        <div class="col-sm-6">
          <div class="project-intelligence-image magnify"><div class="large"></div><img class="img-responsive magniflier" src="<?php echo SITEURL?>images/2017/teamworking/Picture5.png" alt="" /></div>
        </div>
        <div class="col-sm-6">
          <div class="project-intelligence">
            <h3>Dashboards to view team performance</h3>
            <ul>
              <li>Access in real-time key performance metrics.</li>
              <li>Know about off plan events through multi-channel notifications.</li>
              <li>Make effective interventions based on the latest information.</li>
            </ul>
          </div>
        </div>
        </div>
        <div class="all-solution-sec">
        <div class="col-sm-6 right-pull">
          <div class="project-intelligence-image magnify"><div class="large"></div><img class="img-responsive magniflier" src="<?php echo SITEURL?>images/2017/teamworking/Picture6.png" alt="" /></div>
        </div>
        <div class="col-sm-6">
          <div class="project-intelligence">
            <h3>Preserve digital knowledge</h3>
            <ul>
              <li>Teams blogs and wikis to share articles, discussions and documents.</li>
              <li>Turn successful, proven project patterns into reusable templates.</li>
              <li>Smart search your digital memory to find information for your next  project.</li>
            </ul>
          </div>
        </div>
        </div>

		<div class="all-solution-sec">
			<div class="col-sm-6">
			  <div class="project-intelligence-image magnify"><div class="large"></div><img class="img-responsive magniflier" src="<?php echo SITEURL?>images/2017/teamworking/risk-center.png" alt="" /></div>
			</div>

			<div class="col-sm-6">
			  <div class="project-intelligence">
				<h3>Manage project risks</h3>
				<ul>
				  <li>Identify, analyze and create response plans for project risks.</li>
				  <li>Create custom risk events for projects.</li>
				  <li>View risk exposure levels using project heat maps.</li>
				</ul>
			  </div>
			</div>
        </div>
		<div class="all-solution-sec">
			<div class="col-sm-6 right-pull">
			  <div class="project-intelligence-image magnify"><div class="large"></div><img class="img-responsive magniflier" src="<?php echo SITEURL?>images/2017/teamworking/cost-center.png" alt="" /></div>
			</div>
			<div class="col-sm-6">
			  <div class="project-intelligence">
				<h3>Manage project costs</h3>
				<ul>
				  <li>View projects costs at individual and team levels.</li>
				  <li>Track history of costs to make sure project budgets are adhered to.</li>
				  <li>Use rate cards to define worker fees at project level.</li>
				</ul>
			  </div>
			</div>
        </div>
      </div>
    </div>
  </div>
  <div class="all-solution-heading jeera-incorporates">
    <div class="container">
      <div class="row">
        <div class="col-sm-12">
		  <p>OpusView incorporates a powerful mix of social working, project management and real-time communications that blend seamlessly to deliver unparalleled result maximizing the return on your teams and business projects.</p>
        </div>
      </div>
    </div>
  </div>
<!-- New Section 21 May 2018 -->


	<div class="quotes-section">
		<div class="container">
			<div class="row quotes-first">
				<div class="col-md-6">
					<div class="qut_left">
						<p>Executives and those charged with managing project portfolios can easily align teams and their activities more closely with business strategy.</p>
						<p class="authore_name">
						<b>Martin Shaw</b>
						CTO at IdeasCast</p>
					</div>
				</div>
				<div class="col-md-6">
					<div class="qut_right">
						<?php /* <p>Having used several task management products, OpusView is by far the most integrated, comprehensive and it simplifies complexity.</p>
						<p class="authore_name">
						<b>Paul Nelmes</b>
						Project Manager</p> */ ?>
						<p>An ideal solution for any business that has teams working collaboratively on projects. It has all the tools you need in one place and it’s highly intuitive.</p>
						<p class="authore_name">
						<b>Denise Taylor</b>
						Managing Director</p>
					</div>
				</div>
			</div>

			<div class="row quotes-second">
				<div class="col-md-6">
					<div class="qut_left">
						<p>The value that we see and experience with OpusView continues to grow on a daily basis, by allowing us to quickly set up business projects using a series of pre-defined templates, to the ability to collaborate with all team members, whether they are internal or external to the organisation.</p>
						<p class="authore_name">
						<b>Helen Barge</b>
						Managing Director</p>
					</div>
				</div>
				<div class="col-md-6">
					<div class="qut_right">
						<p>OpusView offers some of the richest functionality available in any PPM solution and extends this a long way by utilising social-networking capability and principles.</p>
						<p class="authore_name">
						<b>Tim Burfoot</b>
						Independent Consultant, Business Change and Transformation Director</p>
					</div>
				</div>
			</div>

		</div>
	</div>
</div>
<?php echo $this->element('front/footer');?>

<style>
	video::-internal-media-controls-download-button {
		display:none;
	}

	video::-webkit-media-controls-enclosure {
		overflow:hidden;
	}

	video::-webkit-media-controls-panel {
		width: calc(100% + 30px); /* Adjust as needed */
	}
</style>

<script type="text/javascript">
	// Do not name the function "play()"
		/* var video=$('#idea_video').get(0);
		function ideaVideo(){
			video.play();
		}
		video.addEventListener('ended',function(){
			video.load();
			$(".video_icon").show();
		});
		video.addEventListener('playing',function(){
			$(".video_icon").hide();
		});
		video.addEventListener('pause',function(){
			$(".video_icon").show();
		});
		video.addEventListener('click',function(){
		  $(".video_icon").show();
		});
		video.addEventListener('touchstart', function videoStart() {
			  video.play();
			  console.log('first touch');
			  this.removeEventListener('touchstart', videoStart);
		}); */

		<?php if( isset($this->request->query['start']) && $this->request->query['start'] == 'play' ){ ?>

			setTimeout(function(){

				window.addEventListener('touchstart', function videoStart() {
				  video.play();
				  console.log('first touch');
				  this.removeEventListener('touchstart', videoStart);
				});
				ideaVideo();
			}, 2000);

		<?php } ?>

	// =================================
    $(function(){
		//$("#ajax_overlay").show();
		if( $("#UserEmail").length > 0 ) {
			$("#UserEmail").focus()
		}
    });

    $(function(){
      $('.flexslider').flexslider({
        animation: "slide",
        start: function(slider){
          $('body').removeClass('loading');
		  $(".slide11").hide();
        }
      });

		if( $("#successFlashMsg").length > 0 ) {
			setTimeout(function() {

				$("#successFlashMsg").animate({
					opacity: 0,
					height: 0
					}, 1000, function() {
					$(this).remove()
				})

			}, 4000)
		}
    });


	/********************************************************************/

	 $(function () {

                    /*
                     $('.modal').change( function() { alert('something');  });
                     */
                    $('.modal').on('show.bs.modal', function (event) {
                        $('body').css('padding-right', 0);
                    })
                    $('.modal').on('shown.bs.modal', function (event) {
                        $(event.relatedTarget).tooltip('destroy')
                        $('body').css('padding-right', 0);

                    })
                    $('.modal').on('hidden.bs.modal', function (event) {
                        $('.tooltip').hide();
                        // $(".tooltip").hide();
                    })
                    /*
                     * @todo  Hide each success flash message after 4 seconds
                     * */
                    if ($("#successFlashMsg").length > 0) {

                        setTimeout(function () {
                            $("#successFlashMsg").animate({
                                opacity: 0,
                                height: 0
                            }, 1000, function () {
                                $(this).remove()
                            })

                        }, 4000)

                    }

                    /*
                     * @todo  Global setup of AJAX on document. It can be used when any ajax call return response.
                     * */

                    $(document).ajaxSuccess(function (event, jqXHR, ajaxSettings, data) {
                        $('.tooltip').hide()

                        if ($(".ajax_overlay_preloader").length > 0) {
                            $(".ajax_overlay_preloader").fadeOut(150);
                            $("body").removeClass('noscroll');
                        }

                        if ($.inArray('msg', data) == -1) {
                            if (data['msg'] != '' && !data['success'] && !data['msg'] == 'undefined') {
                                $(".ajax_flash").text(data['msg']).fadeIn(500)
                                setTimeout(function () {
                                    if ($(".ajax_flash").length > 0) {
                                        $(".ajax_flash").fadeOut(600).text('');
                                    }
                                }, 3000)
                            }
                        }
                    });

                    /*
                     * @todo  Global setup of AJAX on document. It can be used when any ajax call is performed
                     * */
                    $(document).ajaxSend(function (e, xhr) {

                        window.theAJAXInterval = 1;
                        // $("#ajax_overlay_text").textAnimate("..........");
                        $(".ajax_overlay_preloader")
                                .fadeIn(300)
                                .bind('click', function (e) {
                                    $(this).fadeOut(300);
                                });

                        $("body").addClass('noscroll');
                    })
                            .ajaxComplete(function () {
                                setTimeout(function () {
                                    $(".ajax_overlay_preloader").fadeOut(300);
                                    $("body").removeClass('noscroll');
                                    clearInterval(window.theAJAXInterval);
                                }, 2000)

                                // console.clear()
                            });
                    /*
                     * @todo  Initially stop all global AJAX events.
                     * */
                    $.ajaxSetup({
                        global: false,
                        headers: {
                            'X-CSRF-Token': $('meta[name="_token"]').attr('content')
                        }

                    })

                    /*
                     * @todo  Also stop Global AJAX events on any modal window will triggered to shown.
                     * 			It ensure that not to show ajax overlay twice.
                     * 			Because modal window already have its own ajax overlay.
                     * @see  To start all global ajax setup, just turn on the global setup.
                     * @example  $.ajax({
                     * 					... ..,
                     * 					global: true,
                     * 					...
                     * 				})
                     * */
                    $(".modal").on('show', function (e) {
                        $.ajax({
                            global: false
                        });
                    })


                })

 </script>
</body>
</html>
