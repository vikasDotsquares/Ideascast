
<?php echo $this->Html->css('projects/list-grid'); ?>
<?php echo $this->Html->css('projects/smart_menu/sm-core-css'); ?>
<?php echo $this->Html->css('projects/smart_menu/sm-mint/sm-mint'); ?>
<?php echo $this->Html->css('projects/manage_elements'); ?>


<?php echo $this->Html->script('projects/plugins/jquery.dot', array('inline' => true));  ?>

<?php echo $this->Html->script('projects/plugins/ellipsis-word', array('inline' => true));  ?>

<?php echo $this->Html->script('projects/elements_library', array('inline' => true)) ?>

<?php echo $this->Html->script('projects/plugins/context-menu', array('inline' => true)) ?>

<?php echo $this->Html->script('projects/plugins/smart-menu', array('inline' => true)) ?>

<?php echo $this->Html->script('projects/manage_elements', array('inline' => true)); ?>

<?php echo $this->Html->script('projects/color_changer', array('inline' => true)); ?>

<style type="text/css">
/* 	.popover-content {
    padding: 9px 5px 9px 9px !important;
	text-align:center !important;
} */

.ws_color_box {
  background: rgb(255, 255, 255) none repeat scroll 0 0;
  border: 1px solid #dddddd;
  border-radius: 5px;
  left: auto;
  margin: 0;
  padding: 5px;
  position: absolute;
  right: 36px;
  top: -18px;
  width: 82px;
  z-index: 9999;
}

</style>

<script type="text/javascript" >
$(function(){
	$(window).on('resize', function(){
		$('.key_target').textdot();
	})
		$('.ellipsis-words').ellipsis_word();
		/* $('.pophover_txt').popover({
        placement : 'bottom',
        trigger : 'click',
        html : true,
		container: 'body',
		delay: {show: 50, hide: 400}
    }) */

	$("#lg_model_box").on('hidden.bs.modal', function() {
		$(this).removeData()
		$(this).find('.modal-content').html('');
	})


	$("#popup_modal").on('hidden.bs.modal', function() {
		$(this).removeData()
		$(this).find('.modal-content').html('');
	})



	$('#modal_medium').on('show.bs.modal', function (e) {

	 $(this).find('.modal-content').css({
		  width: $(e.relatedTarget).data('modal-width'), //probably not needed
	 });
	});

	$('#modal_medium').on('hidden.bs.modal', function () {
	 $(this).removeData('bs.modal');
	});


		if($( window ).width() > 1280 ){
		if( $('#tbl tr').length == 1){
			var extraH = $('.fliter.margin-top').height() + $('#table-responsive').height() +  $('.content-wrapper .content-header').height() + $('.row .content-header').height() +$('.navbar').height() ;
			 var TotH = $('.content-wrapper').innerHeight();
			availH = TotH - extraH; 
		 $('.box-body .drop_element').css({'max-height': availH});
		 $('.box-body .drop_element').css({'min-height': availH});

			}
		}

		function resizeStuff() {
		$('.ellipsis-words').ellipsis_word();
		$('.key_target').textdot();
	}

	var TO = false;
	$(window).on('resize', function(){

		if(TO !== false) {
			clearTimeout(TO);
		}

		TO = setTimeout(resizeStuff, 1000); //200 is time in miliseconds
	});

})



$(window).load(function() {

setTimeout(function(){
	$('.key_target').textdot();
	$('.ellipsis-words').ellipsis_word();
		}, 1500)

	$('.area_info').mouseover(function(){
		setTimeout(function(){
			$('.tooltip').css('text-transform','none');
		}, 200);
	})

	$('.small-box .inner').on('click', function(event) {
		event.preventDefault()
	});

})
</script>
<?php
			$class = 'collapse';
			if(isset($in) && !empty($in)){
				$class = 'in';
			}
			//pr($this->Session->read('user'));

			$per_page_show = $this->Session->read('project.per_page_show');
			$keyword = $this->Session->read('user.keyword');
			$status = $this->Session->read('project.status');
			$country = $this->Session->read('project.country');
			$stt = $this->Session->read('element.start');
			$endd = $this->Session->read('element.end');


?>
<div class="pull-right padright" style="display:none">
	<a class="btn btn-primary searchbtn" data-toggle="collapse" href="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
		Search
	</a>
	<button  class="btn btn-primary" id="resize_window">Resize</button>
</div>


 <div class="<?php echo $class; ?> search" id="collapseExample" style="display:none;">
	<div class="well">
		<?php echo $this->Form->create('User', array('type' => 'POST', 'class' => 'form-horizontal form-bordered', 'id' => 'search_page_show_form'));


		?>
			<div class="modal-body">
				<div class="form-group">
					<div class="col-lg-2">
						<label for="focusedInput" class="control-label">Keyword:</label>
					</div>
					<div class="col-lg-4">
						<?php echo $this->Form->input('keyword', array('placeholder' => 'Enter Keyword here...','type' => 'text','label' => false, 'value'=>$keyword,'div' => false, 'class' => 'form-control')); ?>
					</div>


				  <label for="UserUser" class="col-lg-2 control-label">Start:</label>
				  <div class="col-lg-4">
					<div class="input-group">

						<?php echo $this->Form->input('Element.start_date', [ 'type' => 'text', 'label' => false, 'div' => false, 'id' => 'start_date', 'required' => false, 'readonly' => 'readonly', 'class'	=> 'form-control dates input-small' ] ); ?>
						<div class="input-group-addon open-start-date-picker calendar-trigger">
							<i class="fa fa-calendar"></i>
						</div>
					</div>
				  </div>
				  <label for="UserUser" class="col-lg-2 control-label">End:</label>
				  <div class="col-lg-4">
				  <div class="input-group">
						<?php echo $this->Form->input('Element.end_date', [ 'type' => 'text', 'label' => false, 'div' => false, 'id' => 'end_date', 'required' => false, 'readonly' => 'readonly', 'class' => 'form-control dates input-small' ] ); ?>
						<div class="input-group-addon  open-end-date-picker calendar-trigger">
							<i class="fa fa-calendar"></i>
						</div>
					</div>
				  </div>

					<div class="col-lg-2">
						<label for="focusedInput" class="control-label">Status:</label>
					</div>
					<div class="col-lg-4">
						<?php $options = array( '0'=>'No Status Given','1'=>'Progressing','2' => 'Not Started', '3'=>'Completed','4'=>'Overdue');
						 echo $this->Form->input('status', array('options'=>$options, 'empty' => '- Select Status -','label' => false, 'selected'=>$status, 'div' => false, 'class' => 'form-control')); ?>
					</div>

					<div class="col-lg-12" style="text-align:right;margin:20px 0 0 0">
						<button type="submit" class="searchbtn btn btn-success">Go</button>
						<a class="btn btn-primary searchbtn" href="<?php echo SITEURL; ?>projects/element_resetfilter/<?php echo $this->params['pass'][0]."/".$this->params['pass'][1]; ?>" >Close</a>
					</div>
				</div>
			</div>
			</form>
	</div>
</div>



<div class="row">

	<div class="col-xs-12">

		<div class="row">
			<section class="content-header clearfix">
				<h1 class="pull-left"><?php echo $data['page_heading'] ?></h1>
					<?php
					$menu_project_id = null;
					if( isset($this->params['pass'][0]) && !empty($this->params['pass'][0])) {
						$menu_project_id = $this->params['pass'][0];
					}
					// LOAD PARTIAL FILE FOR TOP DD-MENUS

				?>
				<p class="text-muted date-time pull-left" style="min-width: 100%; padding: 5px 0px;">
					<span><?php echo $data['page_subheading'] ?>
					<?php

					if(isset($data['workspace']['Workspace']['template_relation_id']) && $data['workspace']['Workspace']['template_relation_id'] > 0){
					$ruid = $this->Common->tempRelationUser($data['workspace']['Workspace']['template_relation_id']);

						if($ruid != 'IdeasCast'){
							//$ruDetail = $this->Common->userFullname($ruid);
							echo ", Template Created by : ".$ruid;
						}else{
							echo ", Template Created by : ".$ruid;
						}
					}
					?></span>
				</p>
				<?php /*  ?>
					<p class="text-muted date-time pull-left" style="min-width:100%">Workspace:
						<span>Created: <?php echo date('d M Y h:i:s', strtotime($data['workspace']['Workspace']['created'])); ?></span>
						<span>Updated: <?php echo date('d M Y h:i:s', strtotime($data['workspace']['Workspace']['modified'])); ?></span>

					<?php $p_permission = $this->Common->project_permission_details($this->params['pass']['0'],$this->Session->read('Auth.User.id'));
					// pr($p_permission['ProjectPermission']['owner_id']);
					 if(isset($p_permission['ProjectPermission']['share_by_id']) && !empty($p_permission['ProjectPermission']['share_by_id'])){
					 ?>

							  <span>Shared by: <?php echo  $this->Common->userFullname($p_permission['ProjectPermission']['share_by_id']); ?></span>
							  <span>Date Shared: <?php echo date('d M Y h:i:s', strtotime($p_permission['ProjectPermission']['created'])); ?></span>

					 <?php
					 } ?>
					</p>
				<?php  */ ?>
			</section>
		</div>

	<?php echo $this->element('../Projects/partials/project_header_image', array('p_id' => $this->params['pass'][0])); ?>

		<div class="box-content">

			<div class="row ">


				<div class="col-xs-12">

				<div style="padding :15px 0; margin:  0;  border-top-left-radius: 3px; background-color: #f5f5f5; overflow:visible; border: 1px solid #ddd; min-height:63px;  border-top-right-radius: 3px;border-top:none;border-left:none;border-right:none; border-bottom:2px solid #ddd" class="fliter margin-top">


				 <?php echo $this->element('../Projects/partials/element_settings', array('menu_project_id' => $menu_project_id)); ?>

				</div>

					<div class="box noborder ">
						<!-- CONTENT HEADING -->
                        <div class="box-header nopadding noborder" style="background: none repeat scroll 0 0 #ecf0f5; height: auto">

							<div class="btn-group pull-right" style="opacity: 0; display: none;" id="element_options" >

								<button  title="Remove Element" data-remote="<?php echo SITEURL.'entities/remove_element'; ?>" id="btn_remove_element" class="btn bg-black btn-sm remove_element tipText"><i class="fa fa-trash"></i></button>

								<input type="hidden" name="element_id" id="element_id" value="" />

								<button  title="<?php echo tipText('Cut') ?>" id="btn_cut" class="btn bg-black btn-sm btn_cut tipText" style="border-right: 2px solid #fff;"><i class="fa fa-cut"></i></button>

								<button  title="<?php echo tipText('Copy') ?>" id="btn_copy" class="btn bg-black btn-sm tipText btn_copy"><i class="fa fa-copy"></i></button>

								<span class="btn bg-black btn-sm color_box_wrapper" style="border-radius: 0px 3px 3px 0px;" >
									<span class="color_bucket tipText" title="<?php echo tipText('Edit Colors') ?>" ><i class="fa fa-paint-brush"></i></span>
									<div class="el_colors" style="display: none;">
										<div class="colors btn-group" style="width:100%;">
											<a href="#" data-color="panel-red" data-remote="<?php echo SITEURL.'entities/update_color'; ?>/" class="btn btn-default btn-xs el_color_box" title="Red"><i class="fa fa-square text-red"></i></a>
											<a href="#" data-color="panel-blue" data-remote="<?php echo SITEURL.'entities/update_color'; ?>/" class="btn btn-default btn-xs el_color_box" title="Blue"><i class="fa fa-square text-blue"></i></a>
											<a href="#" data-color="panel-maroon" data-remote="<?php echo SITEURL.'entities/update_color'; ?>/" class="btn btn-default btn-xs el_color_box" title="Maroon"><i class="fa fa-square text-maroon"></i></a>
											<a href="#" data-color="panel-aqua" data-remote="<?php echo SITEURL.'entities/update_color'; ?>/" class="btn btn-default btn-xs el_color_box" title="Aqua"><i class="fa fa-square text-aqua"></i></a>
											<a href="#" data-color="panel-yellow" data-remote="<?php echo SITEURL.'entities/update_color'; ?>/" class="btn btn-default btn-xs el_color_box" title="Yellow"><i class="fa fa-square text-yellow"></i></a>
											<a href="#" data-color="panel-green" data-remote="<?php echo SITEURL.'entities/update_color'; ?>/" class="btn btn-default btn-xs el_color_box" title="Green"><i class="fa fa-square text-green"></i></a>
											<a href="#" data-color="panel-teal" data-remote="<?php echo SITEURL.'entities/update_color'; ?>/" class="btn btn-default btn-xs el_color_box" title="Teal"><i class="fa fa-square text-teal"></i></a>
											<a href="#" data-color="panel-purple" data-remote="<?php echo SITEURL.'entities/update_color'; ?>/" class="btn btn-default btn-xs el_color_box" title="Purple"><i class="fa fa-square text-purple"></i></a>

											<a href="#" data-color="panel-navy" data-remote="<?php echo SITEURL.'entities/update_color'; ?>/" class="btn btn-default btn-xs el_color_box" title="Navy"><i class="fa fa-square text-navy"></i></a>
										</div>
									</div>
								</span>
								<button  title="<?php echo tipText('Close Options') ?>" class="btn btn-danger btn-sm tipText" id="close_options"><i class="fa fa-times"></i></button>
							</div>

							<div id="myPopoverModal" class="popover popover-default">
								<div class="popover-content">
								</div>
								<div class="popover-footer">
									<button type="submit" class="btn btn-sm btn-primary">Submit</button><button type="reset" class="btn btn-sm btn-default">Reset</button>
								</div>
							</div>

							<!-- MODAL BOX WINDOW -->
                            <div class="modal modal-success fade " id="popup_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content"></div>
								</div>
                            </div>


							<!-- END MODAL BOX -->


							<!-- MODAL BOX WINDOW -->
                            <div class="modal modal-success fade " id="lg_model_box" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content"></div>
								</div>
                            </div>


							<!-- END MODAL BOX -->


                        </div>
						<!-- END CONTENT HEADING -->


					<div class="box-body border-top " style="padding: 0">
						<?php
						// pr($project_id, 1);
							$p_permission = $this->Common->project_permission_details($project_id, $this->Session->read('Auth.User.id'));

							$user_project = $this->Common->userproject($project_id, $this->Session->read('Auth.User.id'));

							if(isset($gpid) && !empty($gpid)) {
								$wwsid = $this->Group->group_work_permission_details($project_id, $gpid);
								//pr($wwsid); die;
							}

							if(isset($p_permission) && !empty($p_permission))
							{
								$wwsid = $this->Common->work_permission_details($project_id, $this->Session->read('Auth.User.id'));
							}


						$workspaceArray = $data['workspace']['Workspace'];
						$class_name = (isset($workspaceArray['color_code']) && !empty($workspaceArray['color_code'])) ? $workspaceArray['color_code'] : 'bg-gray';
							$workspace_areas = $this->ViewModel->workspace_areas($workspaceArray['id']);

							$w_a_total = $this->ViewModel->workspace_areas($workspaceArray['id'], true);

							$totalAreas = $totalActElements = $totalInActElements = $totalUsedArea = $percent = 0;

							if ($w_a_total > 0) {

								$progress_data = $this->ViewModel->countAreaElements($workspaceArray['id']);
								if (isset($progress_data) && !empty($progress_data)) {
									// pr($progress_data);
									$totalAreas = $progress_data['area_count'];
									$totalUsedArea = $progress_data['area_used'];
									$totalActElements = $progress_data['active_element_count'];
									$totalInActElements = 0;

									$percent = ($totalUsedArea > 0 && $totalAreas > 0) ? ($totalUsedArea * 100) / $totalAreas : 0;
								}
							}

						// pr($data['workspace'], 1);
						?>
						<div id="table-responsive" class="table-responsive">
							<table class="table table-bordered" id="">
								<thead class="sort-theader">
									<tr>
										<th width="30%" style="text-align:center">Workspace</th>
										<th width="25%" style="text-align:center">Key Result Target</th>
										<th width="8%" style="text-align:center">Tasks</th>
										<th width="24%" style="text-align:center">Resources</th>
										<th width="13%" style="text-align:center">Actions</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td>
											<div class="small-box task-inworks panel <?php echo $class_name ?>">

												<a class="inner" href="#">

													<strong class="ellipsis-word ellipsis-words tipText" title="<?php echo strip_tags($workspaceArray['title']); ?>"  style="text-transform:none !important"><?php // workspace_title truncate
													echo strip_tags($workspaceArray['title']) ; //echo _substr_text($workspaceArray['title'], 29); ?></strong>
													<div class="reminder-sharing-d-in">
													<span class="text-muted date-time">
														<span>Created:
														<?php
														//echo ( isset($workspaceArray['created']) && !empty($workspaceArray['created'])) ? date('d M Y', strtotime($workspaceArray['created'])) : 'N/A';

														echo ( isset($workspaceArray['created']) && !empty($workspaceArray['created'])) ? $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($workspaceArray['created'])),$format = 'd M Y') : 'N/A';


														?></span>
														<span>Updated:
														<?php
														//echo ( isset($workspaceArray['modified']) && !empty($workspaceArray['modified'])) ? date('d M Y', strtotime($workspaceArray['modified'])) : 'N/A';
														echo ( isset($workspaceArray['modified']) && !empty($workspaceArray['modified'])) ? $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($workspaceArray['modified'])),$format = 'd M Y') : 'N/A';

														?></span>
													</span>

													<span class="text-muted date-time" style="padding: 0px; margin: 0px ! important;">
														<span>Start:
														<?php echo ( isset($workspaceArray['start_date']) && !empty($workspaceArray['start_date'])) ? date('d M Y', strtotime($workspaceArray['start_date'])) : 'N/A'; ?></span>
														<span>End:
														<?php echo ( isset($workspaceArray['end_date']) && !empty($workspaceArray['end_date'])) ? date('d M Y', strtotime($workspaceArray['end_date'])) : 'N/A'; ?></span>
													</span>

												</a>

											</div>
										</td>
										<td style="vertical-align: top ! important; font-size: 12px; line-height: 16px; " class=" ">
											<div style="max-height: 65px;  overflow: hidden;" class="key_target">
												<?php echo  nl2br($workspaceArray['description']) ; ?>
											</div>
										</td>
										<td style="vertical-align: middle ! important;">
											<span class="text-center el-icons" >
												<ul class="list-unstyled">
													<li>
														<span class="label bg-mix" title=""><?php echo ($totalActElements ); ?></span>
														<span class="icon_elm btn btn-xs <?php echo $class_name ?> tipText" title="<?php echo tipText('Elements ') ?>"  ></span>
													</li>
													<li>
														<span class="label bg-mix">
															<?php
																// get areas
																$element_detail = null;
																$sum_value = 0;
																$area_id = $this->ViewModel->workspace_areas($workspaceArray['id'], false, true);


																$p_permission = $this->Common->project_permission_details($project_id, $this->Session->read('Auth.User.id'));

																$user_project = $this->Common->userproject($project_id, $this->Session->read('Auth.User.id'));

																$grp_id = $this->Group->GroupIDbyUserID($project_id, $this->Session->read('Auth.User.id'));

																$el_permission = $this->Common->element_permission_data($project_id,$this->Session->read('Auth.User.id'));

																if(isset($grp_id) && !empty($grp_id)){

																$p_permission = $this->Group->group_permission_details($project_id,$grp_id);
																$el_permission = $this->Group->group_element_permission_data($project_id,$grp_id);


																}

																//pr($el_permission );

																if((isset($el_permission) && !empty($el_permission)))
																{
																	$el = $this->ViewModel->area_elements_permissions($area_id, false,$el_permission);
																}

																if(((isset($user_project) && !empty($user_project)) || (isset($project_level) && $project_level==1)   ||  (isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level'] == 1) )){
																	$el = $this->ViewModel->area_elements($area_id);
																}



																if (!empty($el)) {
																	$element_detail = _element_detail(null, $el);

																	if (!empty($element_detail)) {
																		$filter = arraySearch($element_detail, 'date_constraint_flag');
																		if (!empty($filter)) {
																			$sum_value = array_sum(array_columns($element_detail, 'date_constraint_flag'));
																			if (!empty($sum_value)) {
																			}
																		}
																	}
																}
																echo $sum_value;
															?>
														</span>
														<span class="btn btn-xs <?php echo $class_name ?> tipText" title="<?php echo tipText('Overdue Statuses ') ?>"  href="#"><i class="fa fa-exclamation"></i></span>
													</li>
												</ul>
											</span>

										</td>
										<td style="vertical-align: middle ! important;">
											<span class="text-center el-icons" >
												<ul class="list-unstyled">
													<li>
														<span class="label bg-mix">
															<?php
																echo ( isset($progress_data['assets_count']) && !empty($progress_data['assets_count'])) ? ( ( isset($progress_data['assets_count']['links']) && !empty($progress_data['assets_count']['links'])) ? $progress_data['assets_count']['links'] : 0 ) : 0;
															?>
														</span>
														<span class="btn btn-xs <?php echo $class_name ?> tipText" title="<?php echo tipText('Links ') ?>"  href="#"><i class="fa fa-link"></i></span>
													</li>
													<li>
														<span class="label bg-mix">
															<?php
																echo ( isset($progress_data['assets_count']) && !empty($progress_data['assets_count'])) ? ( ( isset($progress_data['assets_count']['notes']) && !empty($progress_data['assets_count']['notes'])) ? $progress_data['assets_count']['notes'] : 0 ) : 0;
															?>
														</span>
														<span class="btn btn-xs <?php echo $class_name ?> tipText" title="<?php echo tipText('Notes ') ?>"  href="#"><i class="fa fa-file-text-o"></i></span>
													</li>
													<li>
														<span class="label bg-mix">
															<?php
																echo ( isset($progress_data['assets_count']) && !empty($progress_data['assets_count'])) ? ( ( isset($progress_data['assets_count']['docs']) && !empty($progress_data['assets_count']['docs'])) ? $progress_data['assets_count']['docs'] : 0 ) : 0;
															?>
														</span>
														<span class="btn btn-xs <?php echo $class_name ?> tipText" title="<?php echo tipText('Documents ') ?>"  href="#"><i class="fa fa-folder-o"></i></span>
													</li>

													<li>
														<span class="label bg-mix">
															<?php
																echo ( isset($progress_data['assets_count']) && !empty($progress_data['assets_count'])) ? ( ( isset($progress_data['assets_count']['mindmaps']) && !empty($progress_data['assets_count']['mindmaps'])) ? $progress_data['assets_count']['mindmaps'] : 0 ) : 0;
															?>
														</span>
														<span class="btn btn-xs <?php echo $class_name ?> tipText" title="<?php echo tipText('Mind Maps ') ?>"  href="#"><i class="fa fa-sitemap"></i></span>
													</li>


													<li>
														<span class="label bg-mix"><?php echo show_counters($workspaceArray['id'], 'decision'); ?></span>
														<span class="btn btn-xs <?php echo $class_name ?> tipText" title="<?php echo tipText('Incomplete Decisions ') ?>"  href="#"><i class="fa fa-expand"></i></span>
													</li>
													<li>
														<span class="label bg-mix"><?php
															echo ( isset($progress_data['assets_count']) && !empty($progress_data['assets_count'])) ? ( ( isset($progress_data['assets_count']['feedbacks']) && !empty($progress_data['assets_count']['feedbacks'])) ? $progress_data['assets_count']['feedbacks'] : 0 ) : 0;
														?></span>
														<span class="btn btn-xs <?php echo $class_name ?> tipText" title="<?php echo tipText('Live Feedbacks ') ?>"  href="#"><i class="fa fa-bullhorn"></i></span>
													</li>

													<li>
														<span class="label bg-mix">
															<?php
																echo ( isset($progress_data['assets_count']) && !empty($progress_data['assets_count'])) ? ( ( isset($progress_data['assets_count']['votes']) && !empty($progress_data['assets_count']['votes'])) ? $progress_data['assets_count']['votes'] : 0 ) : 0;
															?>
														</span>
														<span class="btn btn-xs <?php echo $class_name ?> tipText" title="<?php echo tipText('Live Votes ') ?>"  href="#"><i class="fa fa-inbox"></i></span>
													</li>
												</ul>
											</span>
										</td>
										<td  style="vertical-align: middle ! important; text-align: center ! important; ">
											<div class="btn-group btn-actions">
												<?php  $wid = encr($workspaceArray['id']); ?>
												<!--<a class="btn btn-sm <?php echo $class_name ?> tipText" title="Show Key Result"  href="<?php echo Router::Url(array('controller' => 'workspaces', 'action' => 'show_detail', $wid, 'admin' => FALSE), TRUE); ?>" data-remote="<?php echo Router::Url(array('controller' => 'workspaces', 'action' => 'show_detail', $wid, 'admin' => FALSE), TRUE); ?>" data-target="#popup_modal"  data-modal-width="600" data-toggle="modal" >
													<i class="fa fa-fw fa-eye"></i>
												</a>-->

												<?php
													if((isset($wwsid) && !empty($wwsid))  || (((isset($user_project)) && (!empty($user_project))) || (isset($project_level) && $project_level==1) ||  (isset($p_permission)  && $p_permission['ProjectPermission']['project_level'] ==1 )    )  )

													if(isset($gpid) && (isset($wwsid) && !empty($wwsid))){
														$wsEDDDIT =  $this->Group->group_wsp_permission_edit($this->ViewModel->workspace_pwid($workspaceArray['id']),$project_id,$gpid);

														$wsDELETE =  $this->Group->group_wsp_permission_delete($this->ViewModel->workspace_pwid($workspaceArray['id']),$project_id,$gpid);

														}else if((isset($wwsid) && !empty($wwsid))){
														$wsEDDDIT =  $this->Common->wsp_permission_edit($this->ViewModel->workspace_pwid($workspaceArray['id']),$project_id,$this->Session->read('Auth.User.id'));

														$wsDELETE =  $this->Common->wsp_permission_delete($this->ViewModel->workspace_pwid($workspaceArray['id']),$project_id,$this->Session->read('Auth.User.id'));
													}

													if(((isset($wwsid) && !empty($wwsid)) && ($wsEDDDIT==1))  || (((isset($user_project)) && (!empty($user_project))) || (isset($project_level) && $project_level==1) || (isset($p_permission['ProjectPermission']['project_level'])  && $p_permission['ProjectPermission']['project_level'] ==1 )   ) ) { ?>
													<a class="btn btn-sm <?php echo $class_name ?> tipText" title="<?php tipText('Update Workspace Details', false); ?>"  href="<?php echo Router::Url(array('controller' => 'workspaces', 'action' => 'update_workspace', $project_id, $workspaceArray['id'], 'admin' => FALSE), TRUE); ?>" id="btn_select_workspace" >
														<i class="fa fa-fw fa-pencil"></i>
													</a>
												<?php  } ?>
												<?php
													if(((isset($wwsid) && !empty($wwsid)) && ($wsEDDDIT==1))  || (((isset($user_project)) && (!empty($user_project))) || (isset($project_level) && $project_level==1) ||  (isset($p_permission['ProjectPermission']['project_level'])  && $p_permission['ProjectPermission']['project_level'] ==1 )    ) ) { ?>
													<a class="btn btn-sm <?php echo $class_name ?> tipText color_bucket" title="Color Options"  href="#" style="margin-right: 0 !important;">
														<i class="fa fa-paint-brush"></i>

													<small class="ws_color_box" style="display: none; ">
														<small class="colors btn-group" style="width:100%;">
															<b data-color="bg-red" data-remote="<?php echo SITEURL . 'workspaces/update_color/' . $workspaceArray['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Red"><i class="fa fa-square text-red"></i></b>
															<b data-color="bg-blue" data-remote="<?php echo SITEURL . 'workspaces/update_color/' . $workspaceArray['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Blue"><i class="fa fa-square text-blue"></i></b>
															<b data-color="bg-maroon" data-remote="<?php echo SITEURL . 'workspaces/update_color/' . $workspaceArray['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Maroon"><i class="fa fa-square text-maroon"></i></b>
															<b data-color="bg-aqua" data-remote="<?php echo SITEURL . 'workspaces/update_color/' . $workspaceArray['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Aqua"><i class="fa fa-square text-aqua"></i></b>
															<b data-color="bg-yellow" data-remote="<?php echo SITEURL . 'workspaces/update_color/' . $workspaceArray['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Yellow"><i class="fa fa-square text-yellow"></i></b>
															<!-- <b data-color="bg-orange" data-remote="<?php echo SITEURL . 'workspaces/update_color/' . $workspaceArray['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Orange"><i class="fa fa-square text-orange"></i></b>	-->
															<b data-color="bg-teal" data-remote="<?php echo SITEURL . 'workspaces/update_color/' . $workspaceArray['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Teal"><i class="fa fa-square text-teal"></i></b>
															<b  data-color="bg-purple" data-remote="<?php echo SITEURL . 'workspaces/update_color/' . $workspaceArray['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Purple"><i class="fa fa-square text-purple"></i></b>
															<b data-color="bg-navy" data-remote="<?php echo SITEURL . 'workspaces/update_color/' . $workspaceArray['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Navy"><i class="fa fa-square text-navy"></i></b>
															<b data-color="bg-gray" data-remote="<?php echo SITEURL . 'workspaces/update_color/' . $workspaceArray['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Remove Color"><i class="fa fa-times"></i></b>
														</small>
													</small>
													</a>
												<?php  } ?>

												<?php
													/* if(((isset($wwsid) && !empty($wwsid)) && ($wsDELETE==1))  || (isset($project_level) && $project_level==1) || (((isset($user_project)) && (!empty($user_project))) ||  (isset($p_permission['ProjectPermission']['project_level'])  && $p_permission['ProjectPermission']['project_level'] ==1 )   ) )  { ?>

													<a class="btn btn-xs <?php echo $class_name ?> tipText" title="<?php tipText('Move to Bin', false); ?>"  data-remote="<?php echo Router::url(array('controller' => 'projects', 'action' => 'trashWorkspace', $project_id, $workspaceArray['id'])); ?>" id="confirm_delete" data-target="<?php echo workspace_pwid($project_id, $workspaceArray['id']); ?>" >
														<i class="fa  fa-trash-o"></i>
													</a>
												<?php  } */ ?>

											</div>
										</td>
									</tr>
								</tbody>
							</table>
						</div>

						<div id="workspace">

							<?php
								// LOAD PARTIAL WORKSPACE LAYOUT FILE FOR LOADING DYNAMIC WORKSPACE AREAS
								echo $this->element('../Projects/partials/workspace_layout');
							?>

						</div>

						<!--<div class="border">

							<div class="title_filter margin">
								<input type="text" name="search" class="form-control" id="search" value="">
								<button class="btn btn-success bg-gray"><i class="fa fa-search"></i></button>
                            </div>
							<div class="row">
								<div class="col-md-12">
									<ul class="list-inline clearfix no-margin">
										<li class="list-group-item" style="min-height:100px;width:14%">

											<div class="list-hed" ><a data-content="links" class="act_links">
												<a data-content="links" class="act_links">
												<i class="fa fa-link"></i> Links (0)</a>

												<span class="pull-right">
													<button data-remote="" data-id="19" data-area="19" id="add_element" data-original-title="Add Element" class="btn btn-success text-white btn-xs workspace tipText add_element " data-title="You cannot add an Element because Workspace end date has passed.">
													<i class="fa fa-plus"></i>

												</button>
												</span>
											</div>

											<div class="list-fot"></div>
										</li>
										<li class="list-group-item" style="min-height:100px;width:14%">

											<div class="list-hed" >
											<a data-content="notes" class="act_notes">
												<i class="fa fa-file-text"></i> Notes (0)</a>
												<span class="pull-right">
													<button data-remote="" data-id="19" data-area="19" id="add_element" data-original-title="Add Element" class="btn btn-success text-white btn-xs workspace tipText add_element " data-title="You cannot add an Element because Workspace end date has passed.">
													<i class="fa fa-plus"></i>

												</button>
												</span>
											</div>

											<div class="list-fot"></div>
										</li>
										<li class="list-group-item" style="min-height:100px;width:14%">

											<div class="list-hed" >
											<a data-content="documents" class="act_document">
											<i class="fa fa-folder-o"></i> Documents (1)</a>
												<span class="pull-right">
													<button data-remote="" data-id="19" data-area="19" id="add_element" data-original-title="Add Element" class="btn btn-success text-white btn-xs workspace tipText add_element " data-title="You cannot add an Element because Workspace end date has passed.">
													<i class="fa fa-plus"></i>

												</button>
												</span>
											</div>

											<div class="list-fot"></div>
										</li>
										<li class="list-group-item" style="min-height:100px;width:14%">

											<div class="list-hed" >
											<a data-content="mind_maps" class="act_mind_map">
												<i class="fa fa-sitemap"></i> Mind Maps (0)</a>
												<span class="pull-right">
													<button data-remote="" data-id="19" data-area="19" id="add_element" data-original-title="Add Element" class="btn btn-success text-white btn-xs workspace tipText add_element " data-title="You cannot add an Element because Workspace end date has passed.">
													<i class="fa fa-plus"></i>

												</button>
												</span>
											</div>

											<div class="list-fot"></div>
										</li>
										<li class="list-group-item" style="min-height:100px;width:14%">

											<div class="list-hed" >
											<a data-content="decisions" class="act_decision ">
											<i class="fa fa-expand"></i> Decision (NOS)</a>
												<span class="pull-right">
													<button data-remote="" data-id="19" data-area="19" id="add_element" data-original-title="Add Element" class="btn btn-success text-white btn-xs workspace tipText add_element " data-title="You cannot add an Element because Workspace end date has passed.">
													<i class="fa fa-plus"></i>

												</button>
												</span>
											</div>

											<div class="list-fot"></div>
										</li>
										<li class="list-group-item" style="min-height:100px;width:14%">

											<div class="list-hed" >
											<a data-content="feedbacks" class="act_feedback ">
											<i class="fa fa-bullhorn"></i> Feedback (0)</a>
												<span class="pull-right">
													<button data-remote="" data-id="19" data-area="19" id="add_element" data-original-title="Add Element" class="btn btn-success text-white btn-xs workspace tipText add_element " data-title="You cannot add an Element because Workspace end date has passed.">
													<i class="fa fa-plus"></i>

												</button>
												</span>
											</div>

											<div class="list-fot"></div>
										</li>

										<li class="list-group-item" style="min-height:100px;width:14%">

											<div class="list-hed" >
											<a data-content="votes" class="act_vote ">
											<i class="fa fa-inbox"></i> Votes (0)</a>
												<span class="pull-right">
													<button data-remote="" data-id="19" data-area="19" id="add_element" data-original-title="Add Element" class="btn btn-success text-white btn-xs workspace tipText add_element " data-title="You cannot add an Element because Workspace end date has passed.">
													<i class="fa fa-plus"></i>

												</button>
												</span>
											</div>

											<div class="list-fot"></div>
										</li>

									</ul>
								</div>
							</div>

						</div>-->

					</div><!-- /.box-body -->
					</div><!-- /.box -->
				</div>
			</div>
		</div>
	</div>
</div>
    <!-- Modal Large -->
     				   <div class="modal modal-success fade" id="modal_medium" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
     					<div class="modal-dialog modal-md">
     					     <div class="modal-content"></div>
     					</div>
     				   </div>

	<!-- /.modal -->

	<style>
		/* ul.list-inline li { vertical-align:top; height: 180px !important; }
		ul.list-inline li .list-hed { border-bottom:2px solid #ddd; }
		#workspace{ height:280px; overflow:auto; } */

		.popover span:first-child {
			width: auto !important;
		}
	</style>


