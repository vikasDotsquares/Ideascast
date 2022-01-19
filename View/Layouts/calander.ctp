<!DOCTYPE html>

<html lang="en">
<head>
    <!-- start: Meta -->
    <meta charset="utf-8">
    <title><?php echo $title_for_layout; ?></title>
    <!-- end: Meta -->
    <?php echo $this->element('front/calender'); ?>
	<style>
		.fixed-position {
		position: fixed;
		right: 130px;
		bottom: 10px;
		z-index: 2147483647;
		}
		.chat-icon-box {
		width: 54px;
		height: 54px;
		background-color: #58c6ff;
		border-radius: 50%;
		cursor: pointer;
		}
		.chat-icon-box:hover {
		background-color: #67a028;
		}
		.circle {
		background-color: rgba(255, 255, 255, 0.2);
		border: 2px solid #fff;
		border-radius: 50%;
		color: #fff;
		font-size: 28px;
		font-weight: bold;
		height: 100%;
		left: 50%;
		line-height: 45px;
		position: absolute;
		text-align: center;
		top: 50%;
		transform: translate(-50%, -50%);
		width: 100%;
		}
		.notify {
		animation: animSpin 4s ease-in-out forwards infinite, animFade 0s ease forwards infinite;
		}

		@keyframes animSpin {
		0% { -webkit-transform: perspective(120px) rotateY(180deg) rotateX(0deg); transform: perspective(120px) rotateY(181deg) rotateX(0deg);
		}
		50% { -webkit-transform: perspective(120px) rotateY(0deg) rotateX(0deg); transform: perspective(120px) rotateY(0deg) rotateX(0deg);
		}
		100% { -webkit-transform: perspective(120px) rotateY(180deg); transform: perspective(120px) rotateY(181deg); }
		}
		@keyframes animFade {
		0% { opacity: .95; }
		100% { opacity: 1; }
		}
		.ico_cht {
		background-image: url("<?php echo SITEURL;?>images/icons/tollfree.png");
		background-repeat: no-repeat;
		background-size: 100% auto;
		display: block;
		/* filter: invert(100%); */
		height: 35px;
		margin: 6px 0 0 7px;
		width: 35px;
		border-radius: 50%;
		}
		.chat-icon-box:hover .ico_cht {
		background-color: #fbc760;
		}
	</style>
</head>
    <?php
    	$user_theme = user_theme();
        $sidebar_status = sidebar_status($this->Session->read('Auth.User.id'));
        if ($sidebar_status == 1) {
            $class = "";
        } else {
            $class = "sidebar-collapse";
        }
    ?>
<body class="<?php echo $class; ?> skin-blue inner-view fixed " >

	<div class="ajax_text_overlay">
		<div id="ajax_overlay_text" class="ajax_overlay_text"  ></div>
	</div>

	<div id="ajax_overlay" class="ajax_overlay_preloader">
		<div id="" class="gif_preloader" style="">
			<div id="" class="loading_text" style="">Loading..</div>
		</div>
	</div>

	<?php // Below div is an overlay of body, while AJAX request is in progress. ?>
	<!-- <div id="ajax_overlayd"></div>	-->
	<div id="ajax_overlays" style="display: none;"><div class="ajax_overlay_loader"></div></div>
    <div class="wrapper">
	<!-- start: Header -->
    <?php echo $this->element('front/header_inner',array('user_theme' => $user_theme)); ?>

	<div class="inner-wrapper">
	<!-- start: Main Menu -->
        <?php echo $this->element('front/sidebar-left_inner',array('user_theme' => $user_theme)); ?>
   <!-- end: Main Menu -->


			 <!-- start: Content -->
			 <div class="content-wrapper">
                 <noscript>
                     <div class="alert alert-block span10">
                         <h4 class="alert-heading">Warning!</h4>
                         <p>You need to have JavaScript enabled to use OpusView.</p>
                     </div>
                 </noscript>


                 <section class="content-header">

                   <?php echo $this->element('front/breadcrumb'); ?>
                 </section>
                <section class="content">
                 <?php echo $this->fetch('content');	?>
                </section>
 			<?php echo $this->element('front/footer_cal',array('user_theme' => $user_theme)); ?>
			 </div><!--/.fluid-container-->
			 </div>
    <?php
	    $project_id = (isset($project_id) && !empty($project_id)) ? $project_id : null;
	    $_sidebarProjectId = (isset($_sidebarProjectId) && !empty($_sidebarProjectId)) ? $_sidebarProjectId : null;
		//echo $this->element('front/chat_7');
	   ?>
</div>
	<div class="modal modal-success fade" id="modal_medium" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
     					<div class="modal-dialog modal-md">
     					     <div class="modal-content" style="width: 600px;"></div>
     					</div>
     				   </div>

    </body>
</html>



