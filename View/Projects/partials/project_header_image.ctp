<style>

.pop-content span {
	margin-bottom: 5px;
	font-size: 12px !important;
	display: block;
}

/*.pophover {
	float: left;
}*/
.popover {
	z-index: 999999 !important;
}
.popover span {
	margin-bottom: 2px !important;
}

.popover span:first-child {
	width: 170px !important;
}

ul.thirduser .left-icon-all{
	width:auto !important;
}

</style>
<?php
if(isset($p_id) && !empty($p_id)) {

	$styles = '';
	if(isset($style) && !empty($style)) {
		$styles = $style;
	}
	$p_detail = getByDbId( 'Project', $p_id );
	
	$user_name = 'N/A';
	$updated_on = 'N/A';

	if(isset($p_detail) && !empty($p_detail)) {

		if(isset($p_detail['Project']['image_updated_by']) && !empty($p_detail['Project']['image_updated_by'])) {
			$userFullname = $this->Common->userFullname( $p_detail['Project']['image_updated_by'], null, 1 );
			if(isset($userFullname) && !empty($userFullname)) {
				$user_name = $userFullname;
				$updated_on = _displayDate($p_detail['Project']['image_updated_on']);
			}
		}
		$project_image = $p_detail['Project']['image_file'];

		if( !empty($project_image) && file_exists(PROJECT_IMAGE_PATH . $project_image)){

			$project_image = SITEURL . PROJECT_IMAGE_PATH . $project_image;

			//echo $this->Image->resize( $p_detail['Project']['image_file'], 350, 106, array("class" => 'prd-img popimage', 'data-trigger' => 'hover', 'data-html' => true, 'data-toggle' => 'popover', 'data-content' => "<div class='pop-content'> <span><b>Image By:</b> ".$user_name."</span> <span><b>Uploaded:</b> ".$updated_on."</span></div>",  'data-placement' => 'bottom', 'style' => $styles), 100);


		}
		else {
		?>
		<!--<span id="" class="project_header_noimage" style="<?php echo $styles; ?>">
			<div class="noimage-text">No Project Image</div>
		</span>-->
		<?php
			// echo $this->Image->resize( 'noimage.jpg', 350, 105, array("class" => 'prd-img', 'style' => $styles), 100);
		}

	}

}
	?>

		<script type="text/javascript" >
			$(function(){
					$('.popimage').popover({
						container: 'body',
						placement: 'bottom',
					})
					// .on('show.bs.popover', function () {
						// console.log('do something…', $(this).data())
					// })
			})
		</script>