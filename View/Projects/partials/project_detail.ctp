<?php
$current_user_id = $this->Session->read('Auth.User.id');

$item = getByDbId('Project', $project_id);
$item = $item['Project'];

if(isset($type) && !empty($type)){
	if($type == 'created'){
		$permit_edit = $permit_delete = $permit_propagate  = $full_permit = $project_level = $show_elements = 1;
	}
	elseif($type == 'received'){
		$permit_edit = $permit_delete = $permit_propagate  = $full_permit = $project_level = $show_elements = 0;

		$user_project = $this->Common->userproject($item['id'],$current_user_id);
		$p_permission = $this->Common->project_permission_details($item['id'], $current_user_id);

		$share_by_id = $p_permission['ProjectPermission']['share_by_id'];

		if( $p_permission['ProjectPermission']['project_level'] == 1) {
			$permit_edit = 1;
			$permit_delete = 0;
			$full_permit = 1;
			$project_level = 1;
		}
		else
		{
			if( $p_permission['ProjectPermission']['share_permission'] == 1) {
				$p_propagate = $this->ViewModel->project_propagation( $item['id'], $current_user_id);
				if( isset($p_propagate) && !empty($p_propagate) ) {
					$permit_propagate = 1;
				}
			}
			$permit_edit = (isset($p_permission['ProjectPermission']['permit_edit'])) ? $p_permission['ProjectPermission']['permit_edit'] : 0;

			$permit_delete = 0;
		}

	}
	elseif($type == 'group_received'){
		$permit_edit = $permit_delete = $permit_propagate  = $full_permit = $project_level = $show_elements = 0;
		$user_project = $this->Common->userproject($item['id'],$current_user_id);
		$grp_id = $this->Group->GroupIDbyUserID($item['id'], $current_user_id);
		$gpp = $this->Group->group_permission_details($item['id'], $grp_id);
		$p_permission = (isset($gpp['ProjectPermission'])) ? $gpp : null;
		$share_by_id = $p_permission['ProjectPermission']['share_by_id'];

		$project_propagation = $this->ViewModel->project_propagation( $item['id'], $current_user_id );
		$permit_propagate = ( isset($project_propagation) && !empty($project_propagation)) ? 1 : 0;

		$project_level = ( isset($p_permission['ProjectPermission']['project_level']) && !empty($p_permission['ProjectPermission']['project_level'])) ? 1 : 0;

		if( $project_level == 1) {
			$full_permit = 1;
		}
	}
}



$prj_signoff = '';
$prj_tip = '';
$prj_cursor = '';
if( isset($item['sign_off']) && !empty($item['sign_off']) && $item['sign_off'] == 1 ){
	$prj_signoff = 'disable';
	$prj_tip = 'Project Is Signed Off';
	$prj_cursor = 'cursor:default !important;';
}

?>

<div class="proj-options">
	<div class="btn-group grid-btn-group pull-left">
			<?php  if(!empty($permit_edit)  ) { ?>
			<?php if( isset($prj_signoff) && !empty($prj_signoff) ){ ?>
				<a class="btn btn-default btn-xs tipText <?php echo $prj_signoff;?>" title="<?php tipText($prj_tip); ?>" style="<?php echo $prj_cursor;?>" ><i class="fa fa-paint-brush"></i></a>
			<?php } else { ?>
				<a href="#" class="btn btn-default btn-xs color_bucket tipText " title="<?php tipText('Color Options' ); ?>" ><i class="fa fa-paint-brush"></i></a>
			<?php } ?>

				<div class="color_box color_box_bottom" style="display:none">
					<div class="colors btn-group">
						<a href="#" data-color="panel-red" data-remote="<?php echo SITEURL.'projects/update_color/'.$item['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Red"><i class="fa fa-square text-red"></i></a>
						<a href="#" data-color="panel-blue" data-remote="<?php echo SITEURL.'projects/update_color/'.$item['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Blue"><i class="fa fa-square text-blue"></i></a>
						<a href="#" data-color="panel-maroon" data-remote="<?php echo SITEURL.'projects/update_color/'.$item['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Maroon"><i class="fa fa-square text-maroon"></i></a>
						<a href="#" data-color="panel-aqua" data-remote="<?php echo SITEURL.'projects/update_color/'.$item['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Aqua"><i class="fa fa-square text-aqua"></i></a>
						<a href="#" data-color="panel-yellow" data-remote="<?php echo SITEURL.'projects/update_color/'.$item['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Yellow"><i class="fa fa-square text-yellow"></i></a>
						<a href="#" data-color="panel-green" data-remote="<?php echo SITEURL.'projects/update_color/'.$item['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Green"><i class="fa fa-square text-green"></i></a>
						<a href="#" data-color="panel-teal" data-remote="<?php echo SITEURL.'projects/update_color/'.$item['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Teal"><i class="fa fa-square text-teal"></i></a>
						<a href="#" data-color="panel-purple" data-remote="<?php echo SITEURL.'projects/update_color/'.$item['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Purple"><i class="fa fa-square text-purple"></i></a>
						<a href="#" data-color="panel-navy" data-remote="<?php echo SITEURL.'projects/update_color/'.$item['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Navy"><i class="fa fa-square text-navy"></i></a>
					</div>
				</div>


			<?php if( isset($prj_signoff) && !empty($prj_signoff) ){ ?>
			<a class="btn btn-default btn-xs tipText  <?php echo $prj_signoff;?>" title="<?php tipText($prj_tip); ?>" style="<?php echo $prj_cursor;?>" >
				<i class="fa fa-pencil"></i>
			</a>
			<?php } else { ?>
			<a class="btn btn-default btn-xs tipText  <?php echo $prj_signoff;?>" title="<?php tipText('Update Project Details' ); ?>" href="<?php echo SITEURL.'projects/manage_project/'.$item['id']; ?>" >
				<i class="fa fa-pencil"></i>
			</a>
			<?php } } ?>

			<?php /*if(!empty($permit_delete)  ){ ?>
				<a id="confirm_deletes" data-toggle="modal" data-target="#modal_delete" data-remote="<?php echo Router::Url( array( "controller" => "projects", "action" => "delete_an_item", $item['id'], 'admin' => FALSE ), true ); ?>" type="button" class="btn btn-default btn-xs tipText delete-an-item" title="Delete">
					<i class="fa fa-trash"></i>
				</a>
			<?php }*/

				$ptype =  CheckProjectType($item['id'],$current_user_id);
			?>

			<a class="btn btn-default btn-xs tipText planner"  title="<?php tipText('Gantt' ); ?>" href="<?php echo SITEURL; ?>users/event_gantt/<?php echo $ptype; ?>:<?php echo $item['id'] ?>" >
				<i class="fa fa-calendar"></i>
			</a>

			<?php  if(!empty($project_level)  ){ ?>
				<a class="btn btn-default btn-xs tipText" title="<?php tipText('Project Assets' ); ?>" href="<?php echo SITEURL; ?>users/projects/<?php echo $ptype; ?>:<?php echo $item['id'] ?>" >
					<i class="projectassetsicon"></i>
				</a>
			<?php } ?>

			<?php if( $full_permit && $type != 'group_received' ) { ?>

			<?php if( isset($prj_signoff) && !empty($prj_signoff) ){ ?>
				<a title="<?php echo $prj_tip;?>" class="btn btn-default btn-xs text-bold more tipText <?php echo $prj_signoff;?>" style="<?php echo $prj_cursor;?>" > <i class="fa fa-user-plus"></i></a>
			<?php } else { /*?>
				<a href="<?php echo SITEURL . 'shares/index/' . $item['id'] ?>" title="Project Sharing" class="btn btn-default btn-xs text-bold more tipText"   > <i class="fa fa-user-plus"></i></a>
			<?php */ } ?>

			<?php }
			 else if( $permit_propagate ) { ?>

			 <?php if( isset($prj_signoff) && !empty($prj_signoff) ){ ?>
				<a class="btn btn-default btn-xs text-bold tipText <?php echo $prj_signoff;?>" title="<?php echo $prj_tip;?>" style="<?php echo $prj_cursor;?>"  > <i class="fa fa-fw fa-retweet" style="font-size: 14px"></i></a>
			 <?php } else { ?>
				<a href="<?php echo Router::Url( array( 'controller' => 'shares', 'action' => 'propagate_sharing', $item['id'] , 'admin' => FALSE ), TRUE ); ?>" class="btn btn-default btn-xs text-bold share_propagations tipText" data-remote="<?php echo Router::Url( array( 'controller' => 'shares', 'action' => 'save_propagate_sharing', 'admin' => FALSE ), TRUE ); ?>" data-pop-form="<?php echo Router::Url( array( 'controller' => 'shares', 'action' => 'propagate_sharing', $item['id'], 3, $share_by_id, 'admin' => FALSE ), TRUE ); ?>" title="Project Propagation"  > <i class="fa fa-fw fa-retweet" style="font-size: 14px"></i></a>
			 <?php } } ?>

			<?php
			$further_shared = $this->viewModel->ProjectSharedByThisUser($item['id'],$current_user_id);
			if($project_level == 1 || $further_shared > 0) {
				if( $this->ViewModel->is_shared($item['id']) ) { ?>
				<?php if( isset($prj_signoff) && !empty($prj_signoff) ){ ?>
					<a title="<?php echo $prj_tip;?>" class="btn btn-default btn-xs text-bold more tipText <?php echo $prj_signoff;?>" style="<?php echo $prj_cursor;?>"> <i class="fa fa-fw fa-share"></i></a>
				<?php } else { ?>
					<?php
					$ProjectLevel = ProjectLevel($item['id']);
					if(isset($ProjectLevel) && !empty($ProjectLevel)){
					?>
					<a href="<?php echo Router::Url(array('controller' => 'shares', 'action' => 'sharing_map', $item['id'], 'admin' => FALSE ), TRUE); ?>" title="Sharing Map" class="btn btn-default btn-xs text-bold more tipText" > <i class="fa fa-fw fa-share"></i></a>
					<?php } }
				}
			} ?>

		</div>

 
</div>
<div class="project-image" id="image_file_<?php echo $item['id']; ?>">

	<?php
		$project_image = $item['image_file'];
		if( !empty($project_image) && file_exists(PROJECT_IMAGE_PATH . $project_image)){
			$project_image = SITEURL . PROJECT_IMAGE_PATH . $project_image;
			echo $this->Image->resize( $item['image_file'], 780, 150, array(), 100);
			if($full_permit) {
		?>
			<div class="img-options" >
				<a class="btn btn-primary btn-xs image-upload project_image_upload" data-remote="<?php echo Router::Url(array('controller' => 'projects', 'action' => 'project_image_upload', $item['id'], 'admin' => FALSE ), TRUE); ?>" data-id="<?php echo $item['id']; ?>" style="right: 70px;">Update</a>

				<a class="btn btn-danger btn-xs image-upload remove_pimage"  data-id="<?php echo $item['id']; ?>">Remove</a>
			</div>
		<?php
			}
		}
		else {
			if($full_permit) {
	?>
	<div class="upload-text">
		Add a photo here to show off your project.<br />
		<a class="btn btn-primary btn-sm project_image_upload" style="margin-top: 10px;" data-remote="<?php echo Router::Url(array('controller' => 'projects', 'action' => 'project_image_upload', $item['id'], 'admin' => FALSE ), TRUE); ?>" data-id="<?php echo $item['id']; ?>">Upload</a>
	</div>
		<?php }
		else{
			echo '<span class="upload-text-cent">No Project Image</span>';
		}
	} ?>
</div>

<div class="projectslistwrap">
	<div class="projectslistinside">
		<div class="main-content" style="">
			<div class="left-content projectslist-border" style="">

				<div class="text-content" style="">
<div class="projectslist-col-two">
					<div class="projectslist-col right-b">

					<h5 style="" class="text-black sub-heading padding-sm">Project Type</h5>
					<div class="text-detail alignement" style="">
						<?php
							$alignement = get_alignment($item['aligned_id']);
							if( !empty($alignement) )
								echo $alignement['title'];
							else
								echo "N/A";
						?>
					</div>
</div>
					<div class="projectslist-col">

					<h5 style="" class="text-black sub-heading padding-sm">Project Schedule</h5>
					<div class="text-detail description project-schedule" style="">
						<span>Start:
							<?php
								echo ( isset($item['start_date']) && !empty($item['start_date'])) ? $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($item['start_date'])),$format = 'd M, Y') : 'N/A';
							?></span>
							<span>End:    <?php echo ( isset($item['end_date']) && !empty($item['end_date'])) ? $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($item['end_date'])),$format = 'd M, Y') : 'N/A';?></span>
						</div>
					</div>

					</div>
	</div>
					<div class="projectslist-col-three">
<h5  class="text-black sub-heading padding-sm" style=" ">Project Objective</h5>
					<div class="text-detail objective" style="font-size:13px;">
						<?php
							echo htmlentities($item['objective']);
						?>
					</div>
						</div>
				</div>

			</div><!-- end left-content -->



		</div><!-- end main-content -->

	</div>
</div>
<style type="text/css">
	.upload-text-cent {
	    color: #7b7b7b;
	    font-size: 14px;
	    padding-top: 55px;
	    min-height: 137px;
	     border-right: none;
	    display: inline-block;
	    text-align: center;
	    width: 100%;
	}
</style>
<script type="text/javascript">
	$(function(){
		$('.color_bucket').each(function () {
			$(this).data('color_box', $(this).parent().find('.color_box'))
			$(this).parent().find('.color_box').data('color_bucket', $(this))
		})

		$(".datepick").daterange({
			defaultDate: "+1w",
			numberOfMonths: 2,
			showButtonPanel: false,
			onSelect: function(selected, inst) {

				var divPane = inst.input
					.datepicker("widget")
				divPane.css('width', '34em')

			},
			beforeShow: function(input, inst) {
				var divPane = $(input)
					.datepicker("widget")
				divPane.css('width', '34em')
			},
			onClose: function(dateText, inst) {
				var $input = inst.input,
					$cal_text = $input.parents('.text-content:first').find('.selected-dates:first'),
					$cal_icon = $input.parent('.sub-heading').find('.calendar_trigger:first'),
					$hidden_md = $input.parent('.sub-heading').find('.hidden-md:first');

				if( dateText ) {
					var dates = dateText.split(' - ');
						firstDate = $.datepicker.formatDate('M dd, yy', new Date(dates[0])),
						secDate = (dates[1]) ? $.datepicker.formatDate('M dd, yy', new Date(dates[1])) : '',
						dateStr = firstDate + ( (secDate) ? ' - ' + secDate : ' - ' + firstDate) ,
						data = $cal_icon.data();

					$cal_text.hide().css('display', 'block').html(dateStr + '<i class="fa fa-times empty-dates" style=" "></i>').fadeIn(500)
					$hidden_md.text('(Date Range Selected)');

					$cal_icon.attr('data-original-title', dateStr);
					setTimeout(function(){
						// Add ajax to load the elements according to the date
						$.ajax({
							type:'POST',
							dataType:'json',
							data: $.param({ project_id: data.pid, dateStr: dateText }),
							url: $js_config.base_url + 'projects/ending_elements',
							global: false,
							success: function( response, status, jxhr ) {
								$cal_text.next('.task:first').html(response).css('padding', '40px 10px 10px')
							},
						});
					}, 500)
				}
				else {
					// $cal_text.css('opacity', 0)
					$cal_text.css('display', 'none')
					$cal_text.next('.task:first').css('padding', '0px 10px 10px')
				}
			},
			showOptions: { direction: "down" },
		});

	})
</script>