<?php echo $this->Html->css('projects/modal.popover'); ?>
<?php echo $this->Html->css('projects/menus'); ?>

<?php echo $this->Html->script('drag-drop-context/drag-move', array('inline' => true)) ?>

<style>
td .el .toggle_status .status_check {
    color: #67a028;
}
td .el .toggle_status .status_ban {
    color: #cc0000;
}

.error-inline {
	display: inline-block;
	padding-left: 5px;
	vertical-align: middle;
	font-size: 11px;
	color: #cc0000;
	margin-top: -5px;
}

span.popover-markup h3.has-trigger {
		font-size: 15px !important;
}
.box-header i, .box-header h3 {
		cursor: pointer !important;
}
/* incrementer/decrementer css */
	.spinner {
		width: 61px;
	}
	.spinner input {
		font-size: 14px;
		height: 24px;
		max-width: 40px;
		padding: 2px;
		text-align: right;
	}
	.input-group-btn-vertical {
		position: relative;
		white-space: nowrap;
		width: 1%;
		vertical-align: middle;
		display: table-cell;
		max-width: 20px;
	}
	.input-group-btn-vertical > .btn {
		display: block;
		float: none;
		width: 100%;
		max-width: 100%;
		padding: 8px;
		margin-left: -1px;
		position: relative;
		border-radius: 0;
	}
	.input-group-btn-vertical > .btn:first-child {
		border-top-right-radius: 4px;
		font-size: 12px;
		height: 13px;
		max-width: 20px;
		padding: 5px;
		width: 20px;
	}
	.input-group-btn-vertical > .btn:last-child {
		border-bottom-right-radius: 4px;
		font-size: 12px;
		height: 13px;
		margin-top: -1px;
		padding: 5px 0;
	}
	.input-group-btn-vertical i{
		position: absolute;
		top: 0;
		left: 4px;
	}
		div.el-icons span.btn input.error {
		border: 1px solid #CB0000;
	}
#contextMenu {
	display: block;
	left: 56%;
	position: absolute;
	top: 20%;
}
/*
	.spinner {
	width: 100px;
	}
	.spinner input {
	text-align: right;
	}
	.input-group-btn-vertical {
	position: relative;
	white-space: nowrap;
	width: 1%;
	vertical-align: middle;
	display: table-cell;
	}
	.input-group-btn-vertical > .btn {
	display: block;
	float: none;
	width: 100%;
	max-width: 100%;
	padding: 8px;
	margin-left: -1px;
	position: relative;
	border-radius: 0;
	}
	.input-group-btn-vertical > .btn:first-child {
	border-top-right-radius: 4px;
	}
	.input-group-btn-vertical > .btn:last-child {
	margin-top: -2px;
	border-bottom-right-radius: 4px;
	}
	.input-group-btn-vertical i{
	position: absolute;
	top: 0;
	left: 4px;
	} 
  */
</style>

 
<?php $elements_details = [];
// pr($data['templateRows'], 1);
if(isset($data['templateRows']) && !empty($data['templateRows'])){ ?>
	
	<table class="table table-bordered" id="tbl" >
		
			<?php // echo $max_boxes = max(array_map('count', $data['templateRows']));

			$row_group = $data['templateRows'];
			foreach( $row_group as $row_id => $row_data ) {
			
				echo '<tr class="">';
			
				foreach( $row_data as $row_index => $row_detail ) {
						
					 $last = false;
					$colspan = $rowspan = '';

					if( $row_detail['size_w'] > 0 && $row_detail['size_h'] > 0 )
					{
						if( $row_detail['size_w'] > 1 ) {
							$colspan = ' colspan="'.$row_detail['size_w'].'" ';
						}
						if( $row_detail['size_h'] > 1 ) {
							$rowspan = ' rowspan="'.$row_detail['size_h'].'" ' ;
						}
					}
					?>
					<td <?php echo (!empty($colspan)) ? $colspan : ''; ?> <?php echo (!empty($rowspan)) ? $rowspan : ''; ?> valign="top" width="33.33%" class="area_box" id="<?php echo $row_detail['area_id']; ?>" style="border: 1px solid #ccc !important" >
 
						<div class="box box-success nomargin box-area" >
							<div class="box-header" >
							
								<a href="#" class="btn btn-xs area_elements_toggle el-toggle-tooltip" data-toggle="tooltip" data-trigger="hover" data-placement="auto" role="tooltip" data-original-title="Toggle All Elements">
									<i class="fa fa-fw fa-bars" style="font-size: 24px;"></i>
								</a>
								
								<span class="popover-markup">
									
									<h3 class="box-title area-box-title has-trigger" id="area_<?php echo $row_detail['area_id'] ?>-toggler"  data-title="Update Title" data-html="true" data-remote="<?php echo Router::Url(array('controller' => 'workspaces', 'action' => 'update_area', $row_detail['area_id'], 'admin' => TRUE ), TRUE); ?>">
									
										<?php echo $this->ViewModel->_substr_text( $row_detail['title'], 18 ) ; ?>

									</h3>

									<div class="head hide">Update Title <button style="margin-left: 20px" class="close" type="button">×</button></div>
									<div class="content hide">
									
										<input type='text' name='data[Area][title]' value='<?php echo $row_detail['title'] ?>' class='form-control input_holder'>
										<input type='hidden' name='data[Area][id]' value='<?php echo $row_detail['area_id'] ?>'>

										<button type="submit" id="<?php echo $row_detail['area_id'] ?>" class="btn btn-jeera-submit">
											Submit
										</button>
										<button type="submit" class="btn btn-jeera-dismiss">
											Close
										</button>
									</div>
								</span>

								<div class="pull-right box-tools">
									<div class="btn-group ">
									
										<button class="btn btn-default btn-sm tipText" title="<?php echo tipText('Add Element') ?>"  data-toggle="modal" data-target="#popup_modal" id="add_element" data-area="<?php echo $row_detail['area_id']; ?>"  data-id="<?php echo $row_detail['area_id']; ?>" data-remote="<?php echo Router::Url(array('controller' => 'entities', 'action' => 'get_popup', $row_detail['area_id'], 'admin' => TRUE ), TRUE); ?>">
											<i class="fa fa-fw fa-plus"></i>
										</button>
										
										<button class="btn btn-default btn-sm tipText" title="Drag and drop is only available within the Workspace. Copy and paste also available across Workspaces only." ><i class="fa fa-fw fa-question"></i></button>

									</div>
								</div>
							</div>
						</div>

						<div class="box-body clearfix in">
								<?php
								// CREATE ELEMENTS OF EACH AREA IF EXISTS
								if( isset($row_detail['elements']) && !empty($row_detail['elements']) ) {

									$elements_data = $row_detail['elements'];
									$elements_details[$row_detail['area_id']]['el'] = $row_detail['elements'];
									// pr($elements_details);
									$area_id = $row_detail['area_id'];

									foreach( $elements_data as $element_index => $element_detail ) {
										// echo $element_detail['id'].' = '.$element_detail['sort_order'].'<br />';
									?>

									<?php
										// LOAD PARTIAL ELEMENT FILE
										$response = [ 'id' => $element_detail['id'], 'data' => ['Element' => $element_detail ] ];
										
									?>
								<?php }
								}	?>
						</div> 
					</td>
				<?php
				} // end first foreach 
			// End table row started just after first foreach
			echo '</tr>';
		} // end second foreach ?>
		
		<?php // STAND-BY AREA WILL BE LOADED HERE. ?>
		
		</table>
<?php  } ?>
 
<script type="text/javascript" >
$(function(){
	
	$(".area_box").each( function() { 
		$(this).data("clonedObject", {});
	})
	
	// CALL REARRANGING FUNCTION ON PAGE LOAD
	$.rearrange_elements()

	// SET ALL DATA PASSED FROM PHP SCRIPT ON PAGE LOAD
	$js_config.elements_details = <?php echo json_encode($elements_details); ?>;
	
	
})


</script>

<script id="standByAreaTemplate" type="jeera/tpl">
    <tr>
		<td colspan="{colspan}" style="border: 1px solid #ccc;" class="stand_by_area_box" id="standByArea">
			<div class="box box-success nomargin box-area" >
				<div class="box-header" > 
					<h3 class="box-title" id=""  data-title="Update Title" >
						<i class="fa fa-fw fa-bars" style="font-size: 24px;"></i> Stand-by Area
					</h3>
					
					<div class="pull-right box-tools">
						<div class="btn-group ">
							<button class="btn btn-default btn-sm tipText" title="Drag and drop are available in the stand-by area. But this area not supporting `Copy` operations." ><i class="fa fa-fw fa-question"></i></button>
						</div>
					</div>	
				</div>
			</div>
			<div class="box-body clearfix in">
				<!-- Dummy Element -->
				<div class="row"><div class="col-sm-6" style="display: block;"><div class="el stand-by-element panel no-box-shadow panel-navy" data-remove-remote="http://192.168.4.32/ideascomposer/entities/remove_element" data-remote="http://192.168.4.32/ideascomposer/entities/create_element" data-current-area="14" data-prev-area="0" data-element="273" id="el_14_273" data-highlighted="false"> 
					
					<div style="" class="element_highlight element_disable_highlight"> </div>
					
					<div style="padding-left: 5px; padding-right: 5px;" class="panel-heading clearfix">
						
						<i style="cursor: pointer;" class="fa fa-fw fa-bars toggle_body pull-left" title="Collapse  in/out detail" href="#collapse_body_14_273" data-target="#collapse_body_14_273" data-toggle="collapse"></i> 
						
						<h3 style=" font-size: 13px !important; cursor: default;" class="panel-title pull-left"> Sed ullamcorper lorem vel dapi.. </h3>
						<div class="btn-group pull-right">
							
						</div>
					</div>
					
					<div style="padding: 10px 5px;" class="panel-footer clearfix"> 
						
						<div class="input-group spinner pull-left sort_order_form">
							
							<input type="hidden" value="0" name="element_before" class="element_before">
							
							<input type="text" readonly="" data-id="273" data-value="1" id="sort_order_273" value="1" name="sort_order" class="form-control sort_order_input">
							
							<input type="hidden" value="274" name="element_after" class="element_after">
							
							<input type="hidden" value="14" name="area_id">
							<div style="" class="input-group-btn-vertical btn_group">
								
								
								<button data-remote="http://192.168.4.32/ideascomposer/entities/update_sort_order/273/up" title="Increase Order" class="btn btn-default btn-xs up "><i class="fa fa-caret-up"></i></button>
								
								<button data-remote="http://192.168.4.32/ideascomposer/entities/update_sort_order/273/down" title="Decrease Order" class="btn btn-default btn-xs down disabled"><i class="fa fa-caret-down"></i></button>
								
							</div> 
						</div>
						
						<div class=" pull-right el-icons">
							
							<a data-status="1" data-remote="http://192.168.4.32/ideascomposer/entities/update_status/273" title="" class="btn btn-default btn-xs tipText toggle_status" href="#" data-original-title="Change status">
								<i class="fa fa-check status_check"></i>
								
								
								</a><a title="" class="btn btn-default btn-xs tipText flash light_off" href="#" data-original-title="Highlight Toggle">
								<i class="fa fa-lightbulb-o"></i>
								<!-- fa-bolt, fa-certificate, fa-cog, fa-flash, fa-lightbulb-o, fa-star, fa-star-o, fa-sun-o, fa-circle, fa-circle-o	-->
								
								
							</a><a title="" class="btn btn-default btn-xs tipText" href="#" data-original-title="Number of links"><i class="fa fa-link"></i><span class="label label-success ">4</span></a>
							<a title="" class="btn btn-default btn-xs tipText" href="#" data-original-title="Number of notes"><i class="fa fa-file-text"></i><span class="label label-danger ">4</span></a>
							<a title="" class="btn btn-default btn-xs tipText" href="#" data-original-title="Number of documents"><i class="fa fa-folder"></i><span class="label label-primary ">4</span></a>
						</div>
					</div>
					
					<div id="collapse_body_14_273" class="panel-body collapse">
						<div class="body-content">Fusce nibh massa, sollicitudin
							id hendrerit quis, vulputate ac leo. Phasellus egestas sed orci et 
							viverra. Donec in facilisis turpis. Donec auctor, sapien ut cursus 
							euismod, quam velit eleifend arcu, vel lacinia sapien urna id erat. 
							Phasellus aliquam ultrices nunc eleifend interdum. Sed vitae magna ut 
							ipsum tincidunt viverra semper sed orci.
						<br> </div>
					</div>
					</div></div><div class="col-sm-6" style="display: block;"><div class="el panel panel-purple no-box-shadow" data-remove-remote="http://192.168.4.32/ideascomposer/entities/remove_element" data-remote="http://192.168.4.32/ideascomposer/entities/create_element" data-current-area="14" data-prev-area="0" data-element="274" id="el_14_274" data-highlighted="false"> 
					
					<div style="" class="element_highlight element_disable_highlight"> </div>
					
					<div style="padding-left: 5px; padding-right: 5px;" class="panel-heading clearfix">
						
						<i style="cursor: pointer;" class="fa fa-fw fa-bars toggle_body pull-left" title="Collapse  in/out detail" href="#collapse_body_14_274" data-target="#collapse_body_14_274" data-toggle="collapse"></i> 
						
						<h3 style=" font-size: 13px !important; cursor: default;" class="panel-title pull-left"> Proin 
						pellentesque fermentum.. </h3>
						<div class="btn-group pull-right">
							
						</div>
					</div>
					
					<div style="padding: 10px 5px;" class="panel-footer clearfix"> 
						
						<div class="input-group spinner pull-left sort_order_form">
							
							<input type="hidden" value="273" name="element_before" class="element_before">
							
							<input type="text" readonly="" data-id="274" data-value="2" id="sort_order_274" value="2" name="sort_order" class="form-control sort_order_input">
							
							<input type="hidden" value="0" name="element_after" class="element_after">
							
							<input type="hidden" value="14" name="area_id">
							<div style="" class="input-group-btn-vertical btn_group">
								
								
								<button data-remote="http://192.168.4.32/ideascomposer/entities/update_sort_order/274/up" title="Increase Order" class="btn btn-default btn-xs up disabled"><i class="fa fa-caret-up"></i></button>
								
								<button data-remote="http://192.168.4.32/ideascomposer/entities/update_sort_order/274/down" title="Decrease Order" class="btn btn-default btn-xs down "><i class="fa fa-caret-down"></i></button>
								
							</div> 
						</div>
						
						<div class=" pull-right el-icons">
							
							<a data-status="1" data-remote="http://192.168.4.32/ideascomposer/entities/update_status/274" title="" class="btn btn-default btn-xs tipText toggle_status" href="#" data-original-title="Change status">
								<i class="fa fa-check status_check"></i>
								
								
								</a><a title="" class="btn btn-default btn-xs tipText flash light_off" href="#" data-original-title="Highlight Toggle">
								<i class="fa fa-lightbulb-o"></i>
								<!-- fa-bolt, fa-certificate, fa-cog, fa-flash, fa-lightbulb-o, fa-star, fa-star-o, fa-sun-o, fa-circle, fa-circle-o	-->
								
								
							</a><a title="" class="btn btn-default btn-xs tipText" href="#" data-original-title="Number of links"><i class="fa fa-link"></i><span class="label label-success ">4</span></a>
							<a title="" class="btn btn-default btn-xs tipText" href="#" data-original-title="Number of notes"><i class="fa fa-file-text"></i><span class="label label-danger ">4</span></a>
							<a title="" class="btn btn-default btn-xs tipText" href="#" data-original-title="Number of documents"><i class="fa fa-folder"></i><span class="label label-primary ">4</span></a>
						</div>
					</div>
					
					<div id="collapse_body_14_274" class="panel-body collapse">
						<div class="body-content">Maecenas efficitur ex eros, et cursus diam lobortis vel. Proin 
							pellentesque fermentum ipsum, quis sodales ipsum tempor eu. Cras 
							pellentesque erat sed fringilla accumsan. <br><br>Maecenas efficitur ex eros, et cursus diam lobortis vel. Proin 
							pellentesque fermentum ipsum, quis sodales ipsum tempor eu. Cras 
						pellentesque erat sed fringilla accumsan. <br><br> </div>
					</div>
				</div></div></div>
				<!-- Dummy Element -->
				
			</div>
		</td>
	</tr>
</script>

	<script id="elementTemplate" type="jeera/tpl">
	
		<div id="el_{area_id}_{id}" data-element="{id}" data-prev-area="0" data-current-area="{area_id}" data-remote="<?php echo Router::Url(array( "controller" => "entities", "action" => "create_element", 'admin' => TRUE ), true); ?>"  data-remove-remote="<?php echo Router::Url(array( "controller" => "entities", "action" => "remove_element", 'admin' => TRUE ), true); ?>" class="el panel no-box-shadow {color_code}" > 
		
			<div class="element_highlight element_disable_highlight" style=""> </div>
			
			<div class="panel-heading clearfix" style="padding-left: 5px; padding-right: 5px;">
				
				<i data-toggle="collapse" data-target="#collapse_body_{area_id}_{id}"
				href="#collapse_body_{area_id}_{id}" title="<?php echo tipText('Collapse  in/out detail') ?>" class="fa fa-fw fa-bars toggle_body pull-left" style="cursor: pointer;"></i> 
				
				<h3 class="panel-title pull-left"  style=" font-size: 13px !important; cursor: default;"> {title} </h3>
				<div class="btn-group pull-right">
				
				</div>
			</div>

			<div class="panel-footer clearfix" style="padding: 10px 5px;"> 
			
					<div class="input-group spinner pull-left sort_order_form">
					
						<input type="hidden" class="element_before" name="element_before" value="{element_before}">
						  
						<input type="text" class="form-control sort_order_input" name="sort_order" value="{sort_order}" id="sort_order_{id}" data-value="{sort_order}" data-id="{id}" readonly />
						
						<input type="hidden" class="element_after" name="element_after" value="{element_after}">
						
							<input type="hidden" name="area_id" value="{area_id}" />
						<div class="input-group-btn-vertical btn_group" style="">
							
							
							<button class="btn btn-default btn-xs up {next_disabled}"  title="<?php echo tipText('Increase Order') ?>"  data-remote="<?php echo SITEURL.'entities/update_sort_order'; ?>/{id}/up"><i class="fa fa-caret-up"></i></button>
						
							<button class="btn btn-default btn-xs down {prev_disabled}"  title="<?php echo tipText('Decrease Order') ?>"  data-remote="<?php echo SITEURL.'entities/update_sort_order'; ?>/{id}/down"><i class="fa fa-caret-down"></i></button>
							 
						</div> 
					</div>
					
					<div class=" pull-right el-icons">
					
						<a href="#" class="btn btn-default btn-xs tipText toggle_status" title="<?php echo tipText('Change status') ?>" data-remote="<?php echo SITEURL.'entities/update_status'; ?>/{id}" data-status="{status}">
							<i class="fa {status_icon}"></i>
						</button>
						
						<a href="#" class="btn btn-default btn-xs tipText flash" title="<?php echo tipText('Highlight Toggle') ?>">
							<i class="fa fa-lightbulb-o"></i>
							<!-- fa-bolt, fa-certificate, fa-cog, fa-flash, fa-lightbulb-o, fa-star, fa-star-o, fa-sun-o, fa-circle, fa-circle-o	-->
						</button>
						
					<a href="#" class="btn btn-default btn-xs tipText" title="<?php echo tipText('Number of links') ?>"><i class="fa fa-link"></i><span class="label label-success ">4</span></a>
					<a href="#" class="btn btn-default btn-xs tipText" title="<?php echo tipText('Number of notes') ?>"><i class="fa fa-file-text"></i><span class="label label-danger ">4</span></a>
					<a href="#" class="btn btn-default btn-xs tipText" title="<?php echo tipText('Number of documents') ?>"><i class="fa fa-folder"></i><span class="label label-primary ">4</span></a>
				</div>
			</div>
			
			<div class="panel-body collapse" id="collapse_body_{area_id}_{id}">
				<div class="body-content" >{description} </div>
			</div>
	</div>



	</script>


<ul id="areaContextMenu" class="dropdown-menu context-menu click-context-menu" role="menu" style="" >
	<li  name="paste" class="info click-menu style-4 rounded bordered blocked"> 
		<i class="fa fa-fw fa-paste"></i> Paste 
	</li> 
</ul>
<ul id="elementContextMenu" class="dropdown-menu context-menu click-context-menu" role="menu" style="display:none" >
	<li name='cut' class="error click-menu style-4 rounded bordered">  
		<i class="fa fa-fw fa-cut"></i> Cut 
	</li>
	<li name='copy' class="info click-menu style-4 rounded bordered"> 
		<i class="fa fa-fw fa-copy"></i> Copy
	</li>
</ul>
	

