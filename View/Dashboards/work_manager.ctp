<?php
echo $this->Html->css('projects/bs-selectbox/bootstrap-multiselect');
echo $this->Html->script('projects/plugins/selectbox/bootstrap-multiselect', array('inline' => true));
echo $this->Html->css('projects/task_center');
echo $this->Html->script('projects/work_manager');
echo $this->Html->script('projects/plugins/ellipsis-word', array('inline' => true));
echo $this->Html->script('projects/plugins/calendar/jquery.daterange', array('inline' => true));

?>
<script type="text/javascript">
	$(function(){
		$('.pophover').popover({
	        placement : 'bottom',
	        trigger : 'hover',
	        html : true,
			container: 'body',
			delay: {show: 50, hide: 400}
	    });

	    $('#modal_small, #modal_large').on('hidden.bs.modal', function () {
	    	$(this).removeData('bs.modal');
	    	$(this).find('.modal-content').html('');
	    	// $.reminder_settings();
	    });
	})
</script>
<style type="text/css">
	.ui-datepicker {
		z-index: 20;
	}
	.tasks-ipad {
		display: none;
	}
	.reset-jai-filters {
	    float: right;
	}
	@media (min-width:768px) and (max-width:1199px) {
		.tasks-ipad{
			display:inline;
		}
	}
	@media (max-width:768px) {
		.reset-jai-filters {
		    margin: 5px 0px 3px 0px;
		}
	}
	@media (max-width:667px) {
		.reset-jai-filters {
		    margin: 5px 0px 3px 0px;
		    float: left;
		}
	}
</style>
<div class="row">
    <div class="col-xs-12">
		<div class="row">
	       <section class="content-header clearfix">
				<h1 class="pull-left">
					<?php echo $page_heading; ?>
					<p class="text-muted date-time">
						<span style="text-transform: none;"><?php echo $page_subheading; ?></span>
					</p>
				</h1>
	       </section>
		</div>

     	<div class="box-content">
			<div class="row ">
				<div class="col-xs-12">
					<div class="box noborder margin-top">
						<div class="box-header filters" style="">
							<!-- Modal Boxes -->
							<div class="modal modal-success fade" id="modal_large" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
								<div class="modal-dialog modal-lg">
									<div class="modal-content"></div>
								</div>
							</div>

							<div class="modal modal-success fade" id="modal_small" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
								<div class="modal-dialog ">
									<div class="modal-content"></div>
								</div>
							</div>
							<!-- /.modal -->
						<?php
							$current_user_id = $this->Session->read('Auth.User.id');
							$all_projects = false;
							$all_elements = null;

							$els = null;
							$allusers = null;
							$total_projects = 0;
							$total_elements = 0;

							$all_element_keys = $all_project_keys = $overdue_projects = $todays_projects = $engaged_projects = $overdue_projects1 = $todays_projects1 = $engaged_projects1 = [];
							if(isset($named_params) && !empty($named_params)) {
								// $params_status:
								// 1 = overdue tasks, 2 = tasks completing today & tomorrow, 3 = task with not engaged and disengaged
								// e($named_params);
							}
							if(isset($projects) && !empty($projects)) {
								// JAI functionality starts
								if(isset($named_params) && !empty($named_params)) {
									foreach ($projects as $prjid => $val) {
										// get all elements of the selected projects
										$all_element_keys = array_merge($all_element_keys, $this->TaskCenter->userElements($current_user_id, [$prjid] ));
										// pr($prjid);
									}
									$all_element_keys = array_unique($all_element_keys);

									foreach ($all_element_keys as $key => $elid) {

											if($named_params == 1) {
												// GET ALL OVERDUE ELEMENT'S PROJECTS
												$element_status = element_status($elid);
												if( $element_status == 'overdue' ) {
													$epoid = element_project($elid);
													if(!in_array($epoid, $overdue_projects)) {
														$overdue_projects[] = $epoid;
														$project_detail = getByDbId('Project', $epoid, ['title', 'id']);
														$overdue_projects1[$epoid] = $project_detail['Project']['title'];
													}
												}
											}
											else if($named_params == 2) {
												// GET PROJECTS THOSE HAVE ELEMENTS THAT ARE COMPLETING TODAY AND TOMORROW
												if(completing_tdto($elid) > 0){
													$epid = element_project($elid);
													if(!in_array($epid, $todays_projects)) {
														$todays_projects[] = $epid;
														$project_detail = getByDbId('Project', $epid, ['title', 'id']);
														$todays_projects1[$epid] = $project_detail['Project']['title'];
													}
												}
											}
											else if($named_params == 3) {
												// ALL NOT ENGAGED AND DISENGAGED ELMENT'S PROJECTS
												$el_is_engaged = getByDbId('Element', $elid, ['is_engaged', 'date_constraints']);
												$element_status = element_status($elid);

												if(isset($el_is_engaged) && !empty($el_is_engaged)) {
													if(($el_is_engaged['Element']['is_engaged'] == 0 || $el_is_engaged['Element']['is_engaged'] == 1) && ($element_status == 'progress' || $element_status == 'overdue')) {
														$epeid = element_project($elid);
														if(!in_array($epeid, $engaged_projects)) {
															$engaged_projects[] = $epeid;
															$project_detail = getByDbId('Project', $epeid, ['title', 'id']);
															$engaged_projects1[$epeid] = $project_detail['Project']['title'];
														}
													}
												}
											}
									}
									$projects = $overdue_projects1 + $todays_projects1 + $engaged_projects1;
								}// JAI functionality ends
								// pr($projects);
								$element_keys = null;
								$all_projects = true;
								$total_projects = count($projects);
								$els = $this->TaskCenter->userElements($this->Session->read("Auth.User.id"), array_keys($projects));
						  		if (isset($els) && !empty($els)) {
						  			foreach ($els as $ekey => $evalue) {
						  				$wsp_area_studio_status = wsp_area_studio_status($evalue);
						  				if(!$wsp_area_studio_status) {
						  					// JAI functionality starts
						  					if(isset($named_params) && !empty($named_params)) {
												if($named_params == 1) {
													// GET ALL OVERDUE ELEMENT'S PROJECTS
													$element_status = element_status($evalue);
													if( $element_status == 'overdue' ) {
														$element_keys[] = $evalue;
													}
												}
												else if($named_params == 2) {
													// GET PROJECTS THOSE HAVE ELEMENTS THAT ARE COMPLETING TODAY AND TOMORROW
													if(completing_tdto($evalue) > 0){
														$element_keys[] = $evalue;
													}
												}
												else if($named_params == 3) {
													// ALL NOT ENGAGED AND DISENGAGED ELMENT'S PROJECTS
													$el_is_engaged = getByDbId('Element', $evalue, ['is_engaged', 'date_constraints']);
													$element_status = element_status($evalue);
													if(isset($el_is_engaged) && !empty($el_is_engaged)) {
														if(($el_is_engaged['Element']['is_engaged'] == 0 || $el_is_engaged['Element']['is_engaged'] == 1) && ($element_status == 'progress' || $element_status == 'overdue')) {
															$element_keys[] = $evalue;
														}
													}
												}
											}
						  					// JAI functionality ends
											else {
												$element_keys[] = $evalue;
											}
						  				}
						  			}
						  		}
								$total_elements = (isset($element_keys) && !empty($element_keys)) ? count($element_keys) : 0;
								$task_status = _elements_status($element_keys);

								$non = arraySearch($task_status, 'status', 'NON');
								$pnd = arraySearch($task_status, 'status', 'PND');
								$prg = arraySearch($task_status, 'status', 'PRG');
								$ovd = arraySearch($task_status, 'status', 'OVD');
								$cmp = arraySearch($task_status, 'status', 'CMP');


								$allOwnerusers = array();
								$allSharerusers = array();
								$allowners= array();
								$allownerdata = array();
								$allsharerdata = array();
								$allownerdataNew = array();
								if( isset( $userprojects ) && !empty($userprojects)  ){

									foreach($userprojects as $pid ){
										// ALL projects owner

										$alluserarray = $this->TaskCenter->userByProject($pid);
										$allownerdata[] = array_unique($alluserarray['allOwner']);
										$allsharerdata[] = array_unique($alluserarray['allSharer']);

									}

									if(isset($allownerdata) && !empty($allownerdata)){
										foreach($allownerdata as $key){
											foreach($key as $k){
												 $datas[] = $k;
											}
										}
									}
									$datasSH = array();
									if(isset($allsharerdata) && !empty($allsharerdata)){
										foreach($allsharerdata as $keys){
											foreach($keys as $ks){
												 $datasSH[] = $ks;
											}
										}
									}

									$datas = array_unique($datas);

									$datasSH = array_unique($datasSH);

								}
							}

						 ?>
							<div class="col-sm-12 top-cols">
								<div class="col-1">
									<a class="btn btn-sm btn-task-center">Projects (<?php echo $total_projects; ?>)</a>
									<a class="btn btn-sm btn-task-center">People (<?php echo count($datas); ?>)</a>
								</div>
							</div>
						</div>

						<div class="box-body clearfix" style="min-height: 800px">
							<div class="left-panel">
								<div class="col-sm-12 border-bottom" style="padding-bottom: 10px;">
									<div class="row">
										<div class="col-sm-6 col-md-4 col-lg-5 user_select">
											<label>Owner:&nbsp;</label>
											<select id="users_list_own" class="users_list"  name="users_list_own" class="hidden" placeholder="Select Users" multiple="multiple" >
												<?php
												$ascUser = [];

												foreach( $datas as $user){

													$username = $this->ViewModel->get_user_data($user);
													if(!isset($username) || empty($username)) continue;
													$userfullname = $username['UserDetail']['first_name'].' '.$username['UserDetail']['last_name'];
													$ascUser[$user] = $userfullname;

												}
												asort($ascUser);
												foreach($ascUser as $id => $user){
													if($id == 'N/A') continue;
													//$current = ($id == $this->Session->read("Auth.User.id")) ? 'selected="selected"' : '';
													$current = 'selected=""';
												?>
													<option value="<?php echo $id;?>" >
														<?php echo $user;?>
													</option>
												<?php } ?>
											</select>
											<a href="javascript:void(0);" class="btn btn-danger btn-sm clear-all tipText" title="Clear Users" style="line-height: 1.9;">
											    <i class="fa fa-times "></i>
											</a>
										</div>
										<div class="col-sm-6 col-md-4 col-lg-5 user_select" >
											<label>Sharers:&nbsp;</label>
											<select id="users_list_share" class="users_list_shares"  name="users_list_share" class="hidden" placeholder="Select Users" multiple="multiple" >
												<?php
												$ascUser = [];

												foreach($datasSH as $user){

													$username = $this->ViewModel->get_user_data($user);
													if(!isset($username) || empty($username)) continue;
													$userfullname = $username['UserDetail']['first_name'].' '.$username['UserDetail']['last_name'];
													$ascUser[$user] = $userfullname;

												}
												asort($ascUser);
												foreach($ascUser as $id => $user){
													if($id == 'N/A') continue;
													//$current = ($id == $this->Session->read("Auth.User.id")) ? 'selected="selected"' : '';
													$current = 'selected=""';
												?>
													<option value="<?php echo $id;?>" <?php //echo $current; ?>>
														<?php echo $user;?>
													</option>
												<?php } ?>
											</select>
											<a href="javascript:void(0);" class="btn btn-danger btn-sm clear-all-sharer tipText" title="Clear Users" style="line-height: 1.9;">
											    <i class="fa fa-times "></i>
											</a>
										</div>
									</div>
								</div>
							</div>

							<div class="right-panel">
								<div class="col-sm-12 col-md-3 col-lg-3 projects-icons" style="padding:0;">
									<div class="panel-default">
										<div class="panel-heading" style="height:57px; padding: 20px 15px;">Project</div>
									</div>
									<div class="projects-list">
									<div class="tt"></div>
									<?php if( $all_projects ) { ?>

									<?php
									 echo $this->element('../Dashboards/work_manager/filter_projects_wm', array('filter_users' => [$this->Session->read("Auth.User.id")], 'allprojects' => $projects, 'start' => true, 'named_params' => $named_params));
									 ?>
									<?php } ?>
									</div>
								</div>
								<div class="col-sm-12 col-md-9 col-lg-9 projects-data filtered_data">
									<?php
										//echo $this->element('../Dashboards/work_manager/filtered_data_wm', array('allprojects' => $projects, 'start' => true, 'named_params' => $named_params));
									?>
								</div>
						</div><!-- /.box-body -->
					</div><!-- /.box -->
     		    </div>
		   </div>
		</div>
    </div>
</div>