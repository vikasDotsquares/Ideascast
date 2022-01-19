<!DOCTYPE html>
<html lang="en">
<head>
    <!-- start: Meta -->
    <meta charset="utf-8">
    <title><?php echo $title_for_layout; ?></title>
    <!-- end: Meta -->
    <?php echo $this->element('templates/head'); ?>

</head>

<body class="skin-blue inner-view">

    <div class="wrapper">
	<!-- start: Header -->
    <?php echo $this->element('templates/header'); ?>

	<div class="inner-wrapper">
	<!-- start: Main Menu -->
        <?php echo $this->element('templates/sidebar-left'); ?>
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

                    <?php echo $this->element('templates/breadcrumb'); ?>
                 </section>
                <section class="content">
                 <?php echo $this->fetch('content');	?>

                </section>
 <?php echo $this->element('templates/footer'); ?>
			 </div><!--/.fluid-container-->
			 </div>

	<?php //echo $this->element('templates/footer'); ?>
<script type="text/javascript" >
$(function() {

})
</script>
	<?php echo $this->Js->writeBuffer(); // Write cached scripts ?>
</div>

<?php

	echo $this->element('templates/logs');

?>

    </body>
</html>