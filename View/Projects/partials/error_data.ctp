<?php  

if( isset($error_data) && !empty($error_data) ) {
 ?>
		<section class="content" style="border-top: 1px solid #ccc;">
			<div class="error-page">
				<h2 class="headline text-yellow"> <span class="glyphicon glyphicon-th-large"></span> </h2>
				<div class="error-content" style="padding-top: 17px;">
					<h3><span class="glyphicon glyphicon-info-sign text-yellow"></span> &nbsp; <?php echo $error_data['message'] ?></h3>
					<p class="text-center">
						<?php echo $error_data['html'] ?>
					</p>
				</div><!-- /.error-content -->
			</div><!-- /.error-page -->
		</section>
<?php } ?>
