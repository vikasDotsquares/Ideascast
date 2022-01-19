<!DOCTYPE html>

<html lang="en">
<head>
    <!-- start: Meta -->
    <meta charset="utf-8">
    <title><?php echo $title_for_layout; ?></title>
    <!-- end: Meta -->
    <?php
    $user_theme = user_theme();
    echo $this->element('front/resources_head'); ?>

</head>
    <?php
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

	<div id="ajax_overlays" style="display: none;"><div class="ajax_overlay_loader"></div></div>
    <div class="wrapper">
	<!-- start: Header -->
    <?php echo $this->element('front/header_inner',array('user_theme' => $user_theme)); ?>

	<div class="inner-wrapper">

        <?php echo $this->element('front/sidebar-left_inner',array('user_theme' => $user_theme)); ?>

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
 				<?php echo $this->element('front/footer_inner',array('user_theme' => $user_theme)); ?>
			 </div><!--/.fluid-container-->
			 </div>

		<?php
			//echo $this->Html->script(array('projects/chat_common'));
			echo $this->Html->css(array('projects/chat_common','projects/dropdown'));

		?>
    <?php
	    $project_id = (isset($project_id) && !empty($project_id)) ? $project_id : null;
	    $_sidebarProjectId = (isset($_sidebarProjectId) && !empty($_sidebarProjectId)) ? $_sidebarProjectId : null;
		//echo $this->element('front/chat_7');
	   ?>
</div>


    </body>
</html>



