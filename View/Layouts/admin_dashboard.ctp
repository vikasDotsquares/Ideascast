<!DOCTYPE html>
<html lang="en">
<head>
    <!-- start: Meta -->
    <meta charset="utf-8">
    <title><?php echo $title_for_layout; ?></title>
    <!-- end: Meta -->
    <?php echo $this->element('admin/head_inner'); ?>
</head>

<body class="skin-blue inner-view fixed">
    <div class="wrapper">
	<!-- start: Header -->
    <?php echo $this->element('admin/header_inner'); ?>

	<div class="inner-wrapper">
	<!-- start: Main Menu -->
        <?php echo $this->element('admin/sidebar-left_inner'); ?>
   <!-- end: Main Menu -->


			 <!-- start: Content -->
			 <div class="content-wrapper">
                 <noscript>
                     <div class="alert alert-block span10">
                         <h4 class="alert-heading">Warning!</h4>
                         <p>You need to have <a href="//en.wikipedia.org/wiki/JavaScript" target="_blank">JavaScript</a> enabled to use this site.</p>
                     </div>
                 </noscript>


                 <section class="content-header">

                    <?php echo $this->element('admin/breadcrumb'); ?>
                 </section>
                <section class="content">
                 <?php echo $this->fetch('content');	?>
                <?php
			echo $this->element('admin/logs');
		?>
                </section>

			 </div><!--/.fluid-container-->
			 </div>


	<?php echo $this->element('admin/footer_inner'); ?>

	<?php 	echo $this->Js->writeBuffer(); // Write cached scripts ?>
</div>


    </body>
</html>
