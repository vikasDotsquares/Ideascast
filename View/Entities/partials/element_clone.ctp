

<?php
	// header('Content-Type: application/json');
	// echo json_encode( $response );

if( isset($response) && !empty($response) ) {
	$element_detail = $response['data']['Element'];
	$area_id = $element_detail['area_id'];
	$element_index = $element_detail['id'];
 ?>


<!-- 
<div style="float: left; margin: 0px 5px; width: 32.33%; min-height: 130px;">	-->
	 <div id="el_<?php echo $area_id.'_'.$element_index; ?>" class="el panel no-box-shadow <?php if( isset($element_detail['color_code']) && !empty($element_detail['color_code']) ) { echo $element_detail['color_code']; } else { echo "panel-success"; } ?>" >

		<div class="panel-heading clearfix">
			<h3 class="panel-title pull-left "><i class="fa fa-bars"></i> <?php echo String::truncate($element_detail['title'], 20, array('html' => true)) ; ?></h3>
				<div class="btn-group pull-right">
				<button data-toggle="collapse" data-target="#collapse_body_<?php echo $area_id.'_'.$element_index; ?>"
				href="#collapse_body_<?php echo $area_id.'_'.$element_index; ?>" title="<?php echo tipText('Collapse  in/out detail') ?>"  class="btn btn-default btn-xs toggle_body tipText">
					<i class="fa fa-fw fa-plus"></i></button>	 
				<?php $cls_status_bg = ($element_detail['status']) ? 'bg-green-active' : 'bg-red-active'; ?>
				<?php $cls_status = ($element_detail['status']) ? 'fa-check status_check' : 'fa-times status_ban'; ?>
				<button href="#" class="btn btn-default btn-xs toggle_status tipText" title="<?php echo tipText('Change status') ?>" data-remote="<?php echo SITEURL.'entities/update_status/'.$element_detail['id'] ?>" data-status="<?php echo $element_detail['status']; ?>">
					<i class="fa fa-fw <?php echo $cls_status; ?>"></i>
				</button>


			</div>
		</div>

		<div class="panel-footer clearfix" style="padding: 10px 5px;">

			<div class="btn-group pull-left" >
				<a href="<?php echo SITEURL.'entities/update_element/'.$element_detail['id'] ?>" class="btn btn-default btn-xs edit_pencil tipText" title="<?php echo tipText('Edit') ?>" ><i class="fa fa-pencil"></i></a>
				<a href="#" class="data_holder"></a>
				<button  title="<?php echo tipText('Cut') ?>" class="btn btn-default btn-xs btn_cut tipText"><i class="fa fa-cut"></i></button>
				<button  title="<?php echo tipText('Copy') ?>" class="btn btn-default btn-xs tipText"><i class="fa fa-copy"></i></button>
				<button  title="<?php echo tipText('Paste') ?>" class="btn btn-default btn-xs tipText"><i class="fa fa-paste"></i></button>
				<span class="color_box_wrapper" >
					<span class="btn btn-default btn-xs color_bucket tipText" title="<?php echo tipText('Edit Colors') ?>" ><i class="fa fa-paint-brush"></i></span>
					<div class="display_none el_colors">
						<div class="colors btn-group">
							<a href="#" data-color="panel-red" data-remote="<?php echo SITEURL.'entities/update_color/'.$element_detail['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Red"><i class="fa fa-square text-red"></i></a>
							<a href="#" data-color="panel-blue" data-remote="<?php echo SITEURL.'entities/update_color/'.$element_detail['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Blue"><i class="fa fa-square text-blue"></i></a>
							<a href="#" data-color="panel-maroon" data-remote="<?php echo SITEURL.'entities/update_color/'.$element_detail['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Maroon"><i class="fa fa-square text-maroon"></i></a>
							<a href="#" data-color="panel-aqua" data-remote="<?php echo SITEURL.'entities/update_color/'.$element_detail['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Aqua"><i class="fa fa-square text-aqua"></i></a>
							<a href="#" data-color="panel-yellow" data-remote="<?php echo SITEURL.'entities/update_color/'.$element_detail['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Yellow"><i class="fa fa-square text-yellow"></i></a>
							<a href="#" data-color="panel-orange" data-remote="<?php echo SITEURL.'entities/update_color/'.$element_detail['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Green"><i class="fa fa-square text-green"></i></a>
							<a href="#" data-color="panel-teal" data-remote="<?php echo SITEURL.'entities/update_color/'.$element_detail['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Teal"><i class="fa fa-square text-teal"></i></a>
							<a href="#" data-color="panel-purple" data-remote="<?php echo SITEURL.'entities/update_color/'.$element_detail['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Purple"><i class="fa fa-square text-purple"></i></a>

							<a href="#" data-color="panel-navy" data-remote="<?php echo SITEURL.'entities/update_color/'.$element_detail['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Navy"><i class="fa fa-square text-navy"></i></a>
						</div>
					</div>
				</span>


			</div>

			<div class=" pull-right el-icons">
				
				<span class="btn tipText" title="<?php echo tipText('Sort Order') ?>" style="padding: 0px; margin: 0px;">
					<div style="max-width: 80px; max-height: 30px;" class="input-group">
							
							<input type="hidden" name="area_id" value="<?php echo $area_id; ?>" />
							
							<input type="text" style="padding: 0px; margin: 0px; max-height: 22px;" class="form-control input_sort_order" name="sort_order" value="<?php echo $element_detail['sort_order'] ?>" id="sort_order_<?php echo $element_detail['id'] ?>" data-value="<?php echo $element_detail['sort_order'] ?>" data-id="<?php echo $element_detail['id'] ?>" >
							
						<span class="input-group-btn">
							<a type="button" class="btn btn-success btn-xs submit_sort_order" data-remote="<?php echo SITEURL.'entities/update_sort_order/'.$element_detail['id'] ?>"><i class="fa fa-fw fa-check"></i></a>
						</span>
						
					</div>
				</span>
				
				
				<a href="#" class="btn btn-default btn-xs tipText" title="<?php echo tipText('Number of links') ?>"><i class="fa fa-link"></i><span class="label label-success ">4</span></a>
				<a href="#" class="btn btn-default btn-xs tipText" title="<?php echo tipText('Number of notes') ?>"><i class="fa fa-file-text"></i><span class="label label-danger ">4</span></a>
				<a href="#" class="btn btn-default btn-xs tipText" title="<?php echo tipText('Number of documents') ?>"><i class="fa fa-folder"></i><span class="label label-primary ">4</span></a>
			</div>
		</div>



		<div class="panel-body collapse" id="collapse_body_<?php echo $area_id.'_'.$element_index; ?>">
			<div class="body-content" >
				<?php echo $element_detail['description']; ?>
			</div>
		</div>
	</div>
<!-- </div>	-->
<?php }
else
{
	echo "ERROR";

}
?>
 