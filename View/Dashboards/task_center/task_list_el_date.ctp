<style type="text/css">
	.box-header i, .box-header h3 {
	    cursor: default !important;
	}
	.cr-icon.glyphicon{
		cursor: pointer !important;
	}
	 .estimated-cost label{
		padding-top: 7px;
	 }

	.cr-icon.glyphicon.disabled{
		cursor: default !important;
	}

	/*.depend-table .table.table-striped tr:last-child .elementChanger .dropdown-menu.status-dropdown{

	    top: 100%;
	}

	.depend-table .table.table-striped tr:last-child .elementChanger .dropdown-menu.status-dropdown{

	    top: -60px;
	}*/


	.form-control[disabled], .form-control[readonly], fieldset[disabled] .form-control{ cursor : default;}

		.dropup {position: relative;}
			@media (min-width:1200px) and (max-width:1366px) {
			.modal-lg{ width:800px;}
		}
		@media (min-width:768px) and (max-width:1199px) {
			.modal-lg{ width:700px;}
			.depend-table tr th:nth-child(1){width:25% !important;}
			.depend-table tr th:nth-child(3){width:50% !important;}
			.elements-list .depend-wrap .depend-input-icon {
				padding: 0 5px !important;
			}
		}

		#el-status-dd.open ul.dropdown-menu>li a {
		   color: #777 !important;
		}

		.disabledli {
			pointer-events:none;
			/*opacity:0.6;       */
		}
	#el-status-dd.open ul.dropdown-menu > li {
		border-bottom: 1px solid #dedede;
	    margin: 0;
	}
	    #el-status-dd.open ul.dropdown-menu > li:last-child {
		border-bottom:none;
	}


</style>
<?php
echo $this->Html->css('projects/bs.checkbox');
echo $this->Html->css('projects/taskshecdule');
?>
<?php if( isset($response) && !empty($response) )  { ?>

<?php

	$mindate_workspace = $maxdate_workspace = 'N/A';

	if( isset($response['area_id']) && !empty($response['area_id']) )  {

		$workspace_id = area_workspace_id($response['area_id'],0);

		$project_id = workspace_pid($workspace_id);

		$project_detail = $this->ViewModel->getProjectDetail($project_id, -1);

	} ?>

	<?php
		$date = $this->Common->getDateStartOrEnd($project_id);
		$mindate =  date("d-m-Y");
		$maxdate = isset($date['end_date']) && !empty($date['end_date']) ? date("d-m-Y", strtotime($date['end_date'])) : '';

	?>

	<?php
		$date_workspace = $this->Common->getDateStartOrEnd_elm($workspace_id);


		$cur_date = date("d-m-Y");
		$mindate_project = isset($project_detail['Project']['start_date']) && !empty($project_detail['Project']['start_date']) ? $project_detail['Project']['start_date'] : '';
		$maxdate_project = isset($project_detail['Project']['end_date']) && !empty($project_detail['Project']['end_date']) ? $project_detail['Project']['end_date'] : '';


		$mindate_workspace = isset($date_workspace['start_date']) && !empty($date_workspace['start_date']) ? date("d M Y", strtotime($date_workspace['start_date'])) : '';
		$maxdate_workspace = isset($date_workspace['end_date']) && !empty($date_workspace['end_date']) ? date("d M Y", strtotime($date_workspace['end_date'])) : '';
		//$mindate_workspace = ($mindate_workspace < $cur_date) ? $cur_date : $mindate_workspace;

		$mindate_elm = isset($this->request->data['Element']['start_date']) && !empty($this->request->data['Element']['start_date']) ? date("d-m-Y", strtotime($this->request->data['Element']['start_date'])) : '';
		$maxdate_elm = isset($this->request->data['Element']['end_date']) && !empty($this->request->data['Element']['end_date']) ? date("d-m-Y", strtotime($this->request->data['Element']['end_date'])) : '';

		$messageVar = 'Element';
		if (!isset($mindate_elm) || empty($mindate_elm)) {
			if (isset($mindate_workspace) && !empty($mindate_workspace)) {
				$mindate_elm = $mindate_workspace;

				$messageVar = 'Workspace';
			} else if (isset($mindate_workspace) && empty($mindate_workspace)) {
				$mindate_elm = $mindate_project;
				$messageVar = 'Project';
			} else {
				$mindate_elm = '';
			}
		}
		else if (isset($mindate_elm) && !empty($mindate_elm)) {

		}
		if (isset($maxdate_elm) && empty($maxdate_elm)) {
			if (isset($maxdate_workspace) && !empty($maxdate_workspace)) {
				$maxdate_elm = $maxdate_workspace;
				$messageVar = 'Workspace';
			} else if (isset($maxdate_workspace) && empty($maxdate_workspace)) {
				$maxdate_elm = $maxdate_project;
				$messageVar = 'Project';
			} else {
				$maxdate_elm = '';
			}
		}


		$mindate_elm_cal = $cur_date;

		$wp_updated_date = get_workspace_updated_date($workspace_id);

		// Check Element Permission =====================================================================
		$current_user_id = $this->Session->read('Auth.User.id');
		$eleproject_id = element_project($elementid);
		$project_permit_type = $this->TaskCenter->project_permit_type( $eleproject_id, $current_user_id );
		//==================================================================


		$task_status = '';
		$ele_disabled = '';
		if( isset($response['all_elements']) && !empty($response['all_elements']) )  {
			$el = $response['all_elements']['Element'];
			$task_status = element_status($el['id']);
		}
		if( isset($task_status) && $task_status == 'completed' ){
			$ele_disabled = 'disabled';
		}
		if( isset($date_workspace['sign_off']) && $date_workspace['sign_off'] ==1 ){
			$ele_disabled = 'disabled';
		}
	?>

		<?php
			echo $this->Form->create('Element', array('url' => array('controller' => 'dashboards', 'action' => 'task_list_save_elements', $response['area_id']), 'class' => 'form-bordered', 'id' => 'modelFormUpdateElement', 'data-async' => ""));
		?>
<!-- POPUP MODEL BOX CONTENT HEADER -->
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h3 class="modal-title" id="createModelLabel">Task Manager</h3>

	</div>

	<!-- POPUP MODAL BODY -->
	<div class="modal-body elements-list-noscroll allpopuptabs">

		<ul class="nav nav-tabs ">
	        <li class="active">
	            <a id="tab_dates" class="active" href="#element_dates" data-toggle="tab">Dates</a>
	        </li>
			<?php if( isset($project_permit_type) && $project_permit_type == true ) {?>
	        <li class="">
	            <a id="tab_dependency" href="#element_dependency" data-toggle="tab">Dependency</a>
	        </li>

			<?php /* ?><li class="<?php if( $signoff_status >= 0 && isset($signoff_details['SignoffTask']['task_comment']) && !empty($signoff_details['SignoffTask']['task_comment']) ){ echo ''; } else { echo "disabledli"; } ?>">
	            <a id="tab_signoff" href="#element_signoff" data-toggle="tab">Sign Off</a>
	        </li><?php */ ?>

	        <li class="">
	            <a id="tab_cost" href="#element_cost" data-toggle="tab">Costs</a>
	        </li>
			<?php } ?>
	    </ul>

		<div id="elementTabContent" class="tab-content">
	        <div class="tab-pane fade active in" id="element_dates">
				<?php if( isset($response['all_elements']) && !empty($response['all_elements']) )  {  ?>

					<?php
						$el = $response['all_elements']['Element'];
					?>
						<div class="panel panel-default panel-els task-schedules-deta">
							<div class="panel-body">

								<?php
								echo $this->Form->input('Element.id', [  'name'=>'data[element_date][0][id]', 'type' => 'hidden', 'value' => $el['id'] ] );
								?>
							<?php $element_status = element_status($el['id']); ?>
                            <div class="row date-picks">
								<div class="col-sm-6">
                                <div class="form-group">
									<label class=" " for="title">Title:</label>
                                    <div class="title-group-input cell_<?php echo $element_status; ?>"  >
											<?php echo strip_tags($el['title']); ?>
										</div>
								</div></div>
								<div class="col-sm-3">
								<div class="form-group ">
										<label class="" for="start_date">Start Date:</label>
										<div class="input-group">
											<?php
											$stdate = isset($el['start_date']) && !empty($el['start_date']) ? date("d M Y", strtotime($el['start_date'])) : '';

											if( isset($ele_disabled) && !empty($ele_disabled) ){
												echo $this->Form->input('Element.start_date', [ 'name'=>'data[element_date][0][start_date]', 'type' => 'text', 'label' => false, 'div' => false,  'required' => false,  'readonly' => 'readonly', 'class' => 'form-control input-small', 'value' => $stdate ]);
											} else {
												echo $this->Form->input('Element.start_date', [ 'name'=>'data[element_date][0][start_date]', 'type' => 'text', 'label' => false, 'div' => false,  'required' => false,  'readonly' => 'readonly', 'class' => 'form-control dates input-small start_date', 'value' => $stdate ]);
											}
											?>

											<div class="input-group-addon  open-start-date-picker calendar-trigger">
												<i class="fa fa-calendar"></i>
											</div>
										</div>
										<span id="start_date_err" class="error-message text-danger"> </span>
									</div></div>
							<div class="col-sm-3">
									<div class="form-group ">
										<label class="" for="end_date">End Date:</label>
										<div class="input-group">
											<?php
											$endate = isset($el['end_date']) && !empty($el['end_date']) ? date("d M Y", strtotime($el['end_date'])) : '';

											if( isset($ele_disabled) && !empty($ele_disabled) ){
												echo $this->Form->input('Element.end_date', [ 'name'=>'data[element_date][0][end_date]', 'type' => 'text', 'label' => false, 'div' => false,   'required' => false,  'readonly' => 'readonly', 'class' => 'form-control dates input-small', 'value' => $endate]);
											} else {
												echo $this->Form->input('Element.end_date', [ 'name'=>'data[element_date][0][end_date]', 'type' => 'text', 'label' => false, 'div' => false,   'required' => false,  'readonly' => 'readonly', 'class' => 'form-control dates input-small end_date', 'value' => $endate]);
											}
											?>

											<div class="input-group-addon  open-end-date-picker calendar-trigger">
												<i class="fa fa-calendar"></i>
											</div>
										</div>
										<span id="end_date_err" class="error-message text-danger"> </span>
									</div>
								</div>
								</div>
							</div>
						</div>

				<?php } ?>
			</div>

			<div class="tab-pane fade" id="element_dependency">
				<?php
				if( isset($response['all_elements']) && !empty($response['all_elements']) )  {

						$el = $response['all_elements']['Element'];
						$element_workspace_id = element_workspace($el['id']);
						$workspace_elements = workspace_elements($element_workspace_id);

						$el_dependency = $this->Common->element_dependencies_crictial($this->Session->read('Auth.User.id'),$el['id']);

						if( isset($el_dependency) && !empty($el_dependency) ){

							$used_dependency_elements = $this->Common->used_dependency_elements($el_dependency['ElementDependency']['id'], $el['id'],true);
						}
						$is_criticalfirst = '';
						if( isset($el_dependency) && !empty($el_dependency) ){
								if( isset($el_dependency['ElementDependency']['is_critical']) && !empty($el_dependency['ElementDependency']['is_critical']) ){

										if( $el_dependency['ElementDependency']['is_critical'] == 1 ){
											$is_criticalfirst = 'checked="checked"';
										}

								}
						}

					?>
						<div class="panel panel-default panel-els">
							<div class="panel-body">
							<?php
								echo $this->Form->input('Element.id', [  "id" => "", 'name'=>'data[0][id]', 'type' => 'hidden', 'value' => $el['id'] ] );
							?>
								<div class="col-sm-12 clearfix nopadding" >
									<div class="col-sm-12 clearfix nopadding-left dependency-title">
										<label class="" for="start_date">Workspace Start:&nbsp;<?php
											echo $stdate = isset($mindate_workspace) && !empty($mindate_workspace) ? date("d M Y", strtotime($mindate_workspace)) : 'NA'; ?></label>
                                            <label class="" for="end_date" style="padding-left: 0px;">End:&nbsp;<?php
											echo $endate = isset($maxdate_workspace) && !empty($maxdate_workspace) ? date("d M Y", strtotime($maxdate_workspace)) : 'NA'; ?></label>
									</div>

								</div>
							</div>
						</div>
						<div class="depend-wrap">
							<div align="center" id="depencies_msg" style="padding-bottom:16px;color:green; display:none;">Element dependency is saved</div>
							<div class="wsp-detail"><span class="lastupdate">Last Update: <?php echo (isset($el_dependency['ElementDependency']) && !empty($el_dependency['ElementDependency'])) ?   $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',$el_dependency['ElementDependency']['modified']),$format = 'd M Y g:i A') : "NA";
							 ?></span> <span>Updated by: <?php
							 if(isset($el_dependency['ElementDependency']) && !empty($el_dependency['ElementDependency'])){
							 $userdetailss =  get_user_data($el_dependency['ElementDependency']['user_id']);
							 echo $userdetailss['UserDetail']['full_name'];
							  }else{
								  echo "NA";
							  }
						  ?></span></div>
							<div class="depend-table" >
								<table class="table table-striped" style=" ">
				                    <tbody class="table-striped-body">
				                    	<tr class="not-sel">
					                      <th style="width: 34%;">Title</th>
					                      <th style="width: 6%;">Priority</th>
					                      <th style="<?php echo ( GATE_ENABLED == true ) ? "width: 18%;" : "width: 20%;" ?>">Start</th>
					                      <th style="<?php echo ( GATE_ENABLED == true ) ? "width: 18%;" : "width: 20%;" ?>">End</th>
					                      <th style="<?php echo ( GATE_ENABLED == true ) ? "width: 19%;" : "width: 20%;" ?>">Dependencies</th>
										  <?php if( GATE_ENABLED == true ){ ?>
					                      <th style="width: 5%;">Gate</th>
										  <?php } ?>
					                    </tr>
					                   <?php

											$element_status = element_status($el['id']);
									   ?>
									   <tr class="not-sel">
					                      <td style="text-align: left;">
					                      	<div class="element_title"><?php echo strip_tags($el['title']); ?>
											<?php
												echo $this->Form->input('DefaultDependency.element_id', [ 'type' => 'hidden', 'label' => false, 'div' => false, 'required' => false,  'readonly' => 'readonly', 'class' => 'form-control depend-input', 'value' => $el['id'] ]);

												echo $this->Form->input('DefaultDependency.is_chk', [ 'type' => 'hidden', 'label' => false, 'div' => false, 'required' => false,  'readonly' => 'readonly', 'class' => 'form-control depend-input', 'value' => ""  ]);

												echo $this->Form->input('DefaultDependency.is_chked', [ 'type' => 'hidden', 'label' => false, 'div' => false, 'required' => false,  'readonly' => 'readonly', 'class' => 'form-control depend-input', 'value' => ""  ]);



												echo $this->Form->input('DefaultDependency.is_gate_chk', [ 'type' => 'hidden', 'label' => false, 'div' => false, 'required' => false,  'readonly' => 'readonly', 'class' => 'form-control depend-input', 'value' => ""  ]);
												echo $this->Form->input('DefaultDependency.is_gate_chked', [ 'type' => 'hidden', 'label' => false, 'div' => false, 'required' => false,  'readonly' => 'readonly', 'class' => 'form-control depend-input', 'value' => ""  ]);

												if( isset($el_dependency) && !empty($el_dependency) ){

													$used_dependency_elements = $this->Common->used_dependency_elements($el_dependency['ElementDependency']['id'], $el['id'],true);

													echo $this->Form->input('ElementDependency.id', [ 'type' => 'hidden', 'label' => false, 'div' => false, 'required' => false,  'readonly' => 'readonly',"value"=>$el_dependency['ElementDependency']['id'], 'class' => 'form-control depend-input',   ]);
												}

											?>
											</div>
					                      </td>

										  <td style="text-align: center;">
					                      	<div class="bs-checkbox">
					                      		<label>
					                      			<input <?php echo $ele_disabled;?> type="checkbox" data-crit="<?php echo $el['id'];?>" <?php echo $is_criticalfirst;?> name="data[ElementDependency][<?php echo $el['id'];?>][is_critical]" class="owner_level">
					                      			<span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>
					                      		</label>
					                      	</div>
					                      </td>
					                      <td>
					                      	<div class="col-sm-12 clearfix nopadding">
						                        <div class="col-sm-12 clearfix nopadding-left">
						                        	<div class="input-group depend_<?php echo($element_status); ?>">
						                        		<?php
						                        		$we_stdate = isset($el['start_date']) && !empty($el['start_date']) ? date("d M, Y", strtotime($el['start_date'])) : 'Not Set';
						                        		echo $this->Form->input('Element.start_date', [ 'name'=>'data[0][start_date]', 'type' => 'text', 'label' => false, 'div' => false,  'required' => false,  'readonly' => 'readonly', 'class' => 'form-control depend-input', 'value' => $we_stdate ]); ?>

						                        		<!-- <div class="input-group-addon depend-input-icon">
						                        			<i class="fa fa-calendar"></i>
						                        		</div>-->
						                        	</div>
						                        	<span id="start_date_err" class="error-message text-danger"> </span>
						                        </div>


					                        </div>

					                      </td>
																																								<td>
					                      	<div class="col-sm-12 clearfix nopadding">
						                        <div class="col-sm-12 clearfix nopadding-left">
						                        	<div class="input-group depend_<?php echo($element_status); ?>">
						                        		<?php
						                        		$we_endate = isset($el['end_date']) && !empty($el['end_date']) ? date("d M, Y", strtotime($el['end_date'])) : 'Not Set';
						                        		echo $this->Form->input('Element.end_date', [ 'name'=>'data[0][end_date]', 'type' => 'text', 'label' => false, 'div' => false,   'required' => false,  'readonly' => 'readonly', 'class' => 'form-control depend-input', 'value' => $we_endate]); ?>

						                        		<!-- <div class="input-group-addon depend-input-icon">
						                        			<i class="fa fa-calendar"></i>
						                        		</div>-->
						                        	</div>
						                        	<span id="end_date_err" class="error-message text-danger"> </span>
						                        </div>
					                        </div>

					                      </td>

					                      <td style="text-align: center;" class="elementChanger">
					                      </td>
										  <?php if( GATE_ENABLED == true ){ ?>
										  <td style="text-align: center;">
					                      	&nbsp;
					                      </td>
										  <?php } ?>
					                    </tr>

									   <?php

										if( isset($used_dependency_elements) && !empty(isset($used_dependency_elements)) ){
										foreach ($used_dependency_elements as $we_key => $element_id) {

						                    $element_status = element_status($element_id);
						                    $we_element_detail  = getByDbId('Element',$element_id);
						                    $we_element  = $we_element_detail['Element'];

											$saveDependencies = $this->Common->element_dependencies_crictial($this->Session->read('Auth.User.id'), $element_id);


											if( isset($el_dependency) && !empty($el_dependency) ){

												$getdependenciesrelations = $this->Common->element_dependencies_relationship($el_dependency['ElementDependency']['id'], $element_id);
											}
											$dependenciesrows = ( isset($saveDependencies) && !empty($saveDependencies) ) ? count($saveDependencies) : 0;

											$is_critical = '';
											$is_gated = '';
											$dependency = '';
											if( isset($saveDependencies) && !empty($saveDependencies) ){
													if( $saveDependencies['ElementDependency']['element_id'] == $we_element['id'] ){

															if( $saveDependencies['ElementDependency']['is_critical'] == 1 ){
																$is_critical = 'checked="checked"';
															}

															if( isset($saveDependencies['ElementDependency']['dependency']) && $saveDependencies['ElementDependency']['dependency'] > 0 ){
																$dependency = $saveDependencies['ElementDependency']['dependency'];
															}
													}
											}
										if( GATE_ENABLED == true ){
											if( isset($getdependenciesrelations['ElementDependancyRelationship']['is_gated']) && $getdependenciesrelations['ElementDependancyRelationship']['is_gated'] == 1 ){
												$is_gated = 'checked="checked"';
											}
										}
										 ?>
										<tr>
					                      <td style="text-align: left;">
											  <div class="btn-arrow">
													<?php if( isset($getdependenciesrelations['ElementDependancyRelationship']['dependency']) && $getdependenciesrelations['ElementDependancyRelationship']['dependency'] == 1 ){?>
														<i class="fa fa-arrow-left"></i>
													<?php } else {?>
														<i class="fa fa-arrow-right"></i>
													<?php } ?>
											   </div>
					                      	<div class="element_title"><?php echo strip_tags($we_element['title']); ?></div>
					                      </td>
										  <td style="text-align: center;">
					                      	<div class="bs-checkbox">
					                      		<label>
					                      			<input <?php echo $ele_disabled;?> type="checkbox" data-crit="<?php echo $element_id;?>" <?php echo $is_critical;?> name="data[ElementDependency][<?php echo $element_id;?>][is_critical]" class="owner_level">
					                      			<span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>
					                      		</label>
					                      	</div>
					                      </td>
					                      <td>
					                      	<div class="col-sm-12 clearfix nopadding">
						                        <div class="col-sm-12 clearfix nopadding-left">
						                        	<div class="input-group depend_<?php echo($element_status); ?>">
						                        		<?php
						                        		$we_stdate = isset($we_element['start_date']) && !empty($we_element['start_date']) ? date("d M, Y", strtotime($we_element['start_date'])) : 'Not Set';
						                        		echo $this->Form->input('Element.start_date', [ 'name'=>'data[0][start_date]', 'type' => 'text', 'label' => false, 'div' => false,  'required' => false,  'readonly' => 'readonly', 'class' => 'form-control depend-input start_date', 'value' => $we_stdate ]); ?>

						                        		<!-- <div class="input-group-addon depend-input-icon">
						                        			<i class="fa fa-calendar"></i>
						                        		</div> -->
						                        	</div>
						                        	<span id="start_date_err" class="error-message text-danger"> </span>
						                        </div>


					                        </div>

					                      </td>

										  <td>
					                      	<div class="col-sm-12 clearfix nopadding">

						                        <div class="col-sm-12 clearfix nopadding-left">
						                        	<div class="input-group depend_<?php echo($element_status); ?>">
						                        		<?php
						                        		$we_endate = isset($we_element['end_date']) && !empty($we_element['end_date']) ? date("d M, Y", strtotime($we_element['end_date'])) : 'Not Set';
						                        		echo $this->Form->input('Element.end_date', [ 'name'=>'data[0][end_date]', 'type' => 'text', 'label' => false, 'div' => false,   'required' => false,  'readonly' => 'readonly', 'class' => 'form-control depend-input', 'value' => $we_endate]); ?>

						                        		<!-- <div class="input-group-addon depend-input-icon">
						                        			<i class="fa fa-calendar"></i>
						                        		</div> -->
						                        	</div>
						                        	<span id="end_date_err" class="error-message text-danger"> </span>
						                        </div>
					                        </div>

					                      </td>


										  <?php
										  $relationship = 0;
										  $relationmsg = 'None';
										  $stylerel = 'style="display:none"';
										  $stylerelpre = 'style="display:none"';
										  $stylerelsuc = 'style="display:none"';

										  if( isset($getdependenciesrelations['ElementDependancyRelationship']['dependency']) && $getdependenciesrelations['ElementDependancyRelationship']['dependency'] == 1 ){
												$relationship = 1;
												$relationmsg = 'Predecessor';
												$stylerelpre = 'style="display:inline-block;"';

											} else if(isset($getdependenciesrelations['ElementDependancyRelationship']['dependency']) && $getdependenciesrelations['ElementDependancyRelationship']['dependency'] == 2) {
												$relationship = 2;
												$relationmsg = 'Successor';
												$stylerelsuc = 'style="display:inline-block;"';

											} else {
												$relationship = 0;
												$relationmsg = 'None';
												$stylerel = 'style="display:none;"';

											} ?>

					                      <td style="text-align: center;" class="elementChanger">
											<span href="#" class="btn btn-xs btn-control dropdown el-status-dd" id="el-status-dd">
												<span href="#" class="dropdown-toggle status-drop <?php echo $ele_disabled;?>" id="status-drop" data-toggle="dropdown" aria-controls="status-dropdown" aria-expanded="false"><span class="relationshipnone"><?php echo $relationmsg;?></span> <span data-estatus="<?php echo $ele_disabled;?>" class="fa fa-times bg-red clear_status_filters"></span></span>
												<ul class="dropdown-menu status-dropdown <?php echo $ele_disabled;?>" aria-labelledby="status-drop" class="status-dropdown">
													<li><a id="predecessor-tab" data-dep="1" aria-controls="dropdown1" data-text="Predecessor" >Predecessor<i <?php echo $stylerelpre;?> class="fa fa-check"></i></a></li>
	                  						    	<li><a id="successor-tab" data-dep="2" aria-controls="dropdown1" data-text="Successor" >Successor<i  <?php echo $stylerelsuc;?> class="fa fa-check"></i></a></li>
												</ul>
											</span>

												<?php

												echo $this->Form->input('ElementDependancyRelationship.dependency', ['type' => 'hidden', 'label' => false, 'div' => false, 'required' => false,  'readonly' => 'readonly', 'class' => 'form-control dependency', 'value' => $relationship, 'name'=>'ElementDependancyRelationship['.$we_element["id"].'][dependency]']) ;


											?>
	                  					    </span>
					                      </td>
										  <?php if( GATE_ENABLED == true ){ ?>
										  <td style="text-align: center;">
					                      	<div class="bs-checkbox">
					                      		<label>
					                      			<input type="checkbox" data-critgate="<?php echo $we_element["id"];?>" <?php echo $is_gated;?> name="data[ElementDependancyRelationship][<?php echo $we_element["id"];?>][is_gated]" class="owner_level_gated" >
					                      			<span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>
					                      		</label>
					                      	</div>
					                      </td>
										  <?php } ?>
					                    </tr>
											<?php
											}
										} ?>
				                  </tbody>
				              </table>
							</div>
							<?php //======================================================================== ?>
							<div class="depend-table depend-table-new">
								<table class="table table-striped" style="margin-bottom: 60px;">
				                    <tbody class="append-body">
				                    	<tr>
					                      <th style="width: 34%;">Title</th>
					                      <th style="width: 6%;">Priority</th>
					                       <th style="<?php echo ( GATE_ENABLED == true ) ? "width: 18%;" : "width: 20%;" ?>">Start</th>
					                      <th style="<?php echo ( GATE_ENABLED == true ) ? "width: 18%;" : "width: 20%;" ?>">End</th>
					                      <th style="<?php echo ( GATE_ENABLED == true ) ? "width: 19%;" : "width: 20%;" ?>">Dependencies</th>
										  <?php if( GATE_ENABLED == true ){ ?>
					                      <th style="width: 5%;">Gate</th>
										  <?php } ?>
					                    </tr>
					                    <?php
										$used_dependency_elements = (isset($used_dependency_elements) && !empty($used_dependency_elements) )? $used_dependency_elements : array(0=>0);

										if( isset($workspace_elements) && !empty($workspace_elements) ){
										foreach ($workspace_elements as $we_key => $we_value) {

											if((!in_array($we_value['Element']['id'],$used_dependency_elements)) && ($we_value['Element']['id'] !=$el['id'])){

					                    	$we_element = $we_value['Element'];
						                    $element_status = element_status($we_element['id']);

											$saveDependencies = $this->Common->element_dependencies($this->Session->read('Auth.User.id'), $we_element['id']);
											$is_critical = '';
											$dependency = '';



											$saveDependencieslst = $this->Common->element_dependencies_crictial($this->Session->read('Auth.User.id'), $we_element['id']);

											$is_criticalst = '';
											$is_getcalst = '';
											$dependency = '';
											//pr($saveDependencies);
											if( isset($saveDependencieslst) && !empty($saveDependencieslst) ){
													if( $saveDependencieslst['ElementDependency']['element_id'] == $we_element['id'] ){

															if( $saveDependencieslst['ElementDependency']['is_critical'] == 1 ){
																$is_criticalst = 'checked="checked"';
															}
													}
											}
											if( isset($el_dependency) && !empty($el_dependency) ){

												$getdependenciesrelations = $this->Common->element_dependencies_relationship($el_dependency['ElementDependency']['id'], $we_element['id']);

											}


										 ?>
					                    <tr>
					                      <td style="text-align: left;" class="td-slow">
											<div class="btn-arrow">

														<i class="fa  " style="display:none;"></i>



											</div>
					                      	<div class="element_title"><?php echo strip_tags($we_element['title']); ?>
											</div>
					                      </td>
										  <td style="text-align: center;">
					                      	<div class="bs-checkbox">
					                      		<label>
					                      			<input <?php echo $ele_disabled;?> type="checkbox" data-crit="<?php echo $we_element['id'];?>" <?php echo $is_criticalst;?> name="data[ElementDependency][<?php echo $we_element['id'];?>][is_critical]" class="owner_level">
					                      			<span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>
					                      		</label>
					                      	</div>
					                      </td>
					                      <td>
					                      	<div class="col-sm-12 clearfix nopadding start_date_sort">
						                        <div class="col-sm-12 clearfix nopadding-left">
						                        	<div class="input-group depend_<?php echo($element_status); ?>">
						                        		<?php
						                        		$we_stdate = isset($we_element['start_date']) && !empty($we_element['start_date']) ? date("d M, Y", strtotime($we_element['start_date'])) : 'Not Set';
						                        		echo $this->Form->input('Element.start_date', [ 'name'=>'data[0][start_date]', 'type' => 'text', 'label' => false, 'div' => false,   'required' => false,  'readonly' => 'readonly', 'class' => 'form-control depend-input start_date ', 'value' => $we_stdate ]); ?>

						                        		<!-- <div class="input-group-addon depend-input-icon">
						                        			<i class="fa fa-calendar"></i>
						                        		</div>-->
						                        	</div>
						                        	<span id="start_date_err" class="error-message text-danger"> </span>
						                        </div>

					                        </div>

					                      </td>

										  <td>
					                      	<div class="col-sm-12 clearfix nopadding">

						                        <div class="col-sm-12 clearfix nopadding-left">
						                        	<div class="input-group depend_<?php echo($element_status); ?>">
						                        		<?php
						                        		$we_endate = isset($we_element['end_date']) && !empty($we_element['end_date']) ? date("d M, Y", strtotime($we_element['end_date'])) : 'Not Set';
						                        		echo $this->Form->input('Element.end_date', [ 'name'=>'data[0][end_date]', 'type' => 'text', 'label' => false, 'div' => false,   'required' => false,  'readonly' => 'readonly', 'class' => 'form-control depend-input', 'value' => $we_endate]); ?>

						                        		<!-- <div class="input-group-addon depend-input-icon">
						                        			<i class="fa fa-calendar"></i>
						                        		</div> -->
						                        	</div>
						                        	<span id="end_date_err" class="error-message text-danger"> </span>
						                        </div>
					                        </div>

					                      </td>




										  <?php
										  $relationship = 0;
										  $relationmsg = 'None';
										  $stylerel = 'style="display:none"';
										  $stylerelpre = 'style="display:none"';
										  $stylerelsuc = 'style="display:none"';

										  if( isset($getdependenciesrelations['ElementDependancyRelationship']['dependency']) && $getdependenciesrelations['ElementDependancyRelationship']['dependency'] == 1 ){
												$relationship = 1;
												$relationmsg = 'Predecessor';
												$stylerelpre = 'style="display:inline-block;"';

											} else if(isset($getdependenciesrelations['ElementDependancyRelationship']['dependency']) && $getdependenciesrelations['ElementDependancyRelationship']['dependency'] == 2) {
												$relationship = 2;
												$relationmsg = 'Successor';
												$stylerelsuc = 'style="display:inline-block;"';

											} else {
												$relationship = 0;
												$relationmsg = 'None';
												$stylerel = 'style="display:none;"';

											} ?>

					                      <td style="text-align: center;" class="elementChanger">
											<span href="#" class="btn btn-xs btn-control dropdown el-status-dd" id="el-status-dd">
												<span href="#" class="dropdown-toggle status-drop <?php echo $ele_disabled;?>" id="status-drop" data-toggle="dropdown" aria-controls="status-dropdown" aria-expanded="false"><span class="relationshipnone"><?php echo $relationmsg;?></span> <span data-estatus="<?php echo $ele_disabled;?>"  class="fa fa-times bg-red clear_status_filters"></span></span>
												<ul class="dropdown-menu status-dropdown <?php echo $ele_disabled;?>" aria-labelledby="status-drop" class="status-dropdown">
													<li><a id="predecessor-tab" data-dep="1" aria-controls="dropdown1" data-text="Predecessor" >Predecessor<i <?php echo $stylerelpre;?> class="fa fa-check"></i></a></li>
	                  						    	<li><a id="successor-tab" data-dep="2" aria-controls="dropdown1" data-text="Successor" >Successor<i  <?php echo $stylerelsuc;?> class="fa fa-check"></i></a></li>
												</ul>
											</span>

												<?php

												echo $this->Form->input('ElementDependancyRelationship.dependency', ['type' => 'hidden', 'label' => false, 'div' => false, 'required' => false, "id" => "", 'readonly' => 'readonly', 'class' => 'form-control dependency', 'value' => $relationship, 'name'=>'ElementDependancyRelationship['.$we_element["id"].'][dependency]']) ;


											?>
	                  					    </span>
					                      </td>
										  <?php if( GATE_ENABLED == true ){ ?>
										  <td style="text-align: center;">
					                      	<div class="bs-checkbox">
					                      		<label>
					                      			<input disabled="disabled" type="checkbox" name="data[ElementDependancyRelationship][<?php echo $we_element["id"];?>][is_gated]" class="owner_level_gated disabled" >
					                      			<span class="cr"><i class="cr-icon glyphicon glyphicon-ok disabled" style="cursor: default !important;" ></i></span>
					                      		</label>
					                      	</div>
					                      </td>
										  <?php } ?>
					                    </tr>
											<?php }
										}
									}	?>
				                  </tbody>
				              </table>
							</div>
						</div>
				<?php } ?>
			</div>


	        <div class="tab-pane fade" id="element_signoff">
				<?php
				if( isset($signoff_details) && !empty($signoff_details) ){
					$userDetail = get_user_data($signoff_details['SignoffTask']['user_id']);
				?>
					<div class="elements-list">
						<p><strong>Signed off by:</strong> <?php echo $userDetail['UserDetail']['full_name'];?> on <?php echo date('d M, Y h:i A',strtotime($signoff_details['SignoffTask']['created']));?></p>
					</div>
					<div class="" id="impact_assessment">
							<div class="row">
								<div class="col-md-12">
									<div class="response-description">
										<label><strong>Comment:</strong></label>
										<textarea class="form-control" rows="4" placeholder="Max chars allowed 250" id="signoff_comment" name="signoff_comment" readonly="readonly" style="resize: vertical; min-height:90px; background-color:#fff;"><?php
											echo $signoff_details['SignoffTask']['task_comment'];
										?></textarea>
									</div>
								</div>
								<?php if( isset($signoff_details['SignoffTask']['task_evidence']) && !empty($signoff_details['SignoffTask']['task_evidence']) ){?>
								<div class="col-md-12" style="margin-top:10px;">
									<div class="response-description">
										<label><strong>Evidence:</strong></label>
										<a href="<?php echo SITEURL.'entities/download_signoff/'.$signoff_details['SignoffTask']['id']?>"><?php echo $signoff_details['SignoffTask']['task_evidence']; ?></a>
									</div>
								</div>
								<?php } ?>
							</div>
					</div>
					<?php } ?>

			</div>

			<div class="tab-pane fade" id="element_cost">
				<?php
				if( isset($response['all_elements']) && !empty($response['all_elements']) )  {

						$el = $response['all_elements']['Element'];
						$element_workspace_id = element_workspace($el['id']);
						$workspace_elements = workspace_elements($element_workspace_id);
						// pr($workspace_elements);
						$costESDetails = $this->Common->getElementCostLast($el['id'],1);
						$costSPDetails = $this->Common->getElementCostLast($el['id'],2);

						$project_id = element_project($el['id']);

						$project_detail = getByDbId("Project", $project_id, ['id', 'title', 'start_date', 'end_date', 'currency_id', 'budget']);
					    $project_detail = $project_detail['Project'];

				    	$currency_symbol = '';
				    	// $currency_symbol = '<i class="fa fa-gbp"></i>';
					    if(isset($project_detail['currency_id']) && !empty($project_detail['currency_id'])) {
					        $currency_detail = getByDbId("Currency", $project_detail['currency_id'], ['id',  'name',  'sign']);
					        $currency_detail = $currency_detail['Currency'];
					        $currency_symbol = $currency_detail['sign'];
					        // pr($currency_detail);
					    }
					    /*if($currency_symbol == 'USD') {
					        $currency_symbol = '<i class="fa fa-dollar"></i>';
					    }
					    else if($currency_symbol == 'GBP') {
					        $currency_symbol = '<i class="fa fa-gbp"></i>';
					    }
					    else if($currency_symbol == 'EUR') {
					        $currency_symbol = '<i class="fa fa-eur"></i>';
					    }
					    else if($currency_symbol == 'DKK' || $currency_symbol == 'ISK') {
					        $currency_symbol = '<span style="font-weight: 600">Kr</span>';
					    }*/
					?>
						<div class="panel panel-default panel-els">
							<div class="panel-body" style="padding-bottom: 10px; border-bottom: 2px solid #ccc;">

							<?php
							echo $this->Form->input('Element.id', [  'name'=>'data[0][id]', 'type' => 'hidden', 'value' => $el['id'] ] );
							?>
							<?php $element_status = element_status($el['id']); ?>


								<div class="col-sm-12 clearfix nopadding date_constraints_cost" >
                                <div class="row">
								<div class="col-sm-6">
                                <div class="form-group">
									<label class=" " for="title">Title:</label>
									<div class="element-title">
										<div class="cell_<?php echo $element_status; ?>"  >
											<?php echo strip_tags($el['title']); ?>
										</div>
									</div>
								</div></div>
								<div class="col-sm-3">
										<label class="" for="start_date">Start Date:</label>
										<div class="input-group">
											<?php
											$stdate = isset($el['start_date']) && !empty($el['start_date']) ? date("d M Y", strtotime($el['start_date'])) : '';
											echo $this->Form->input('Element.start_date', [  'name'=>'data[0][start_date]', 'type' => 'text', 'label' => false, 'div' => false, 'required' => false,  'readonly' => 'readonly', 'class' => 'form-control elementStartDateCost ', 'value' => $stdate ]); ?>

											<div class="input-group-addon hide">
												<i class="fa fa-calendar"></i>
											</div>

										</div>
										<span id="start_date_err" class="error-message text-danger"> </span>
									</div>
								<div class="col-sm-3">
										<label class="" for="end_date">End Date:</label>
										<div class="input-group">
											<?php
											$endate = isset($el['end_date']) && !empty($el['end_date']) ? date("d M Y", strtotime($el['end_date'])) : '';
											echo $this->Form->input('Element.end_date', [ 'name'=>'data[0][end_date]', 'type' => 'text', 'label' => false, 'div' => false, 'required' => false,  'readonly' => 'readonly', 'class' => 'form-control elementEndDateCost', 'value' => $endate]); ?>

											<div class="input-group-addon hide">
												<i class="fa fa-calendar"></i>
											</div>

										</div>
										<span id="end_date_err" class="error-message text-danger"> </span>
									</div>
								</div>
                                </div>
							</div>
						</div>
						<div class="depend-wrap">
							<div class="depend-table-cost">
								<div align="center" id="element_success_msg" style="padding-bottom:16px;color:green; display:none;">Element Cost saved</div>
								<div class="row">
									<div class="col-sm-2 estimated-cost"><label>Budget (<?php echo $currency_symbol;?>):</label></div>
									<div class="col-sm-4 col-md-4 col-lg-3 estimated-cost">

										<?php
											 if(isset($costESDetails['ElementCost']['estimated_cost']) && !empty($costESDetails['ElementCost']['estimated_cost'])){
												$valueconst = $costESDetails['ElementCost']['estimated_cost'];
											} else {
												$valueconst = 0;
											}
										$estimatedcost = $this->Common->element_cost_history($el['id']);
										$spendcost = $this->Common->element_cost_history($el['id']);

										?>
										<div class="input-group readonly">
											<!-- <span class="input-group-addon"><?php //echo $currency_symbol;?></span> -->
											<input class="form-control total-spend readonly" value="<?php echo $valueconst;?>" type="text" readonly="readonly">
										</div>
									</div>

									<div class="col-sm-5 col-md-5 col-lg-6">
										<div id="updatedcostestim" style="font-size: 12px;">
											<ul>
												<?php
													if(!isset($costESDetails['ElementCost']['modified']) && empty($costESDetails['ElementCost']['modified']) ){
												?>
													<li id="dcet" style="display:none">Last Updated: <span id="dcurrentEstimateTime"></li>
													<li id="dceu" style="display:none">By <span id="dcurrentEstimateUser"></li>
												<?php } ?>

												<?php
													if( isset($costESDetails['ElementCost']['modified']) &&  ($valueconst > 0 || $valueconst == 0.00)){
													?>
													<li style="width: 100%;">Last Updated: <span id="currentEstimateTime" > <?php /* echo $this->Wiki->_displayDate($date = date('Y-m-d h:i:s',strtotime($costDetails['ElementCost']['modified'])),$format = 'd M, Y: h:iA'); */

													echo date('d M, Y: h:iA',strtotime($costESDetails['ElementCost']['modified']));
													?></span></li>
													<?php }
													if( isset($costESDetails['ElementCost']['updated_by']) &&  ($valueconst > 0 || $valueconst == 0.00) ){ ?>
													<li>By: <span id="currentEstimateUser"> <?php echo $this->Common->userFullname($costESDetails['ElementCost']['updated_by']);?> </span></li>
													<?php } ?>
											</ul>
										</div>
									</div>

									<?php /* <div id="estimatedHistoryCost" data-historytype="estimated_cost" data-elementid="<?php echo $el['id'];?>" class="col-sm-1 <?php if( count($estimatedcost)> 0 ){?>pophovers<?php } ?>"  title="Cost History" data-popover-content="#myPopoverEstimateCost" ><i  class="fa fa-history estimated_history" id="estimated_cost_history" data-historytype="estimated_cost" data-elementid="<?php echo $el['id'];?>" aria-hidden="true" style="cursor: pointer !important;"  ></i></div> */ ?>

									<i
										data-historytype="estimated_cost"
										id="estimatedHistoryCost"
										data-elementid="<?php echo $el['id'];?>"
										aria-hidden="true"
										style="cursor: pointer !important;"
										id="estimatedHistoryCost"
										data-historytype="estimated_cost"
										data-elementid="<?php echo $el['id'];?>"
										class="col-sm-1 fa fa-history estimated_history <?php if( !empty($estimatedcost) && count($estimatedcost) > 0 ){?>pophovers<?php } ?>"
										data-title="Budget History (<?php echo $currency_symbol;?>)"
										data-popover-content="#myPopoverEstimateCost" ></i>

								</div>
								<div class="row"><div class="col-sm-12"><span id="estimated_cost_err" class="error-message text-danger"> </span></div></div>
								<div class="row">&nbsp;</div>
								<div class="row">
									<div class="col-sm-2 estimated-cost"><label>Actual (<?php echo $currency_symbol;?>):</label></div>
									<div class="col-sm-4 col-md-4 col-lg-3 estimated-cost">
										<?php
											if(isset($costSPDetails['ElementCost']['spend_cost']) && !empty($costSPDetails['ElementCost']['spend_cost'])){
												$valuesconst = $costSPDetails['ElementCost']['spend_cost'];
											} else {
												$valuesconst = 0;
											}
										?>
										<div class="input-group readonly">
											<!-- <span class="input-group-addon"><?php //echo $currency_symbol;?></span> -->
											<input class="form-control total-spend readonly" value="<?php echo $valuesconst;?>" type="text" readonly="readonly">
										</div>
									</div>
									<div class="col-sm-5 col-md-5 col-lg-6">
										<div id="updatedcostspend"  style="font-size: 12px;">
											<ul>
											<?php
												if(!isset($costSPDetails['ElementCost']['modified']) && empty($costSPDetails['ElementCost']['modified'])){
											?>
												<li id="dcst" style="display:none;">Last Updated: <span id="dcurrentSpendTime"></li>
												<li id="dcsu" style="display:none;">By: <span id="dcurrentSpendUser"></li>
											<?php } ?>

												<?php
													if(isset($costSPDetails['ElementCost']['modified']) &&  ($valuesconst > 0 || $valuesconst == 0.00)){
													?>
													<li style="width: 100%;">Last Updated: <span id="currentSpendTime"><?php /* echo $this->Wiki->_displayDate($date = date('Y-m-d h:i:s',strtotime($costDetails['ElementCost']['modified'])),$format = 'd M, Y: h:iA'); */
													echo date('d M, Y: h:iA',strtotime($costSPDetails['ElementCost']['modified']));

													?></span></li>
													<?php } if( isset($costSPDetails['ElementCost']['updated_by']) && ($valuesconst > 0 || $valuesconst == 0.00)){?>
													<li>By: <span id="currentSpendUser"><?php echo $this->Common->userFullname($costSPDetails['ElementCost']['updated_by']);?></span></li>
													<?php }?>
											</ul>
										</div>
									</div>
									<?php /* <div id="spendHistoryCost" data-historytype="spend_cost" data-elementid="<?php echo $el['id'];?>" class="col-sm-1  <?php if( count($spendcost)> 0 ){?>pophoversw<?php } ?>" title="Spend History"  data-popover-content="#myPopoverSpendCost" ><i rel="popover" class="fa fa-history estimated_history" id="spend_cost_history"  data-historytype="spend_cost" data-elementid="<?php echo $el['id'];?>" aria-hidden="true" style="cursor: pointer !important;" ></i></div>*/?>

									<i data-historytype="spend_cost" data-elementid="<?php echo $el['id'];?>" data-title="Actual History (<?php echo $currency_symbol;?>)"  data-popover-content="#myPopoverSpendCost" rel="popover" class="fa fa-history estimated_history col-sm-1 <?php if( !empty($spendcost) && count($spendcost)> 0 ){?>pophoversw<?php } ?>" id="spendHistoryCost"  data-historytype="spend_cost" aria-hidden="true" style="cursor: pointer !important;" ></i>



								</div>
				 				<div class="row"><div class="col-sm-12"><span id="spend_cost_err" class="error-message text-danger"> </span></div></div>

							</div>

							<div id="myPopoverEstimateCost" class="hide" style="height:350px; width:300px; overflow-y:auto;"></div>

							<div id="myPopoverSpendCost" class="hide" style="height:350px; width:300px; overflow-y:auto;">

							</div>

						</div>

				<?php } ?>
			</div>

		</div>

	</div>

	<!-- POPUP MODAL FOOTER -->
	<div class="modal-footer">
		 <button type="button" data-projectid="<?php echo $project_id;?>" id="dependency_submit_update" data-btntype="update" class="btn btn-success submit_dependency <?php echo $ele_disabled ;?>">Update</button>

		 <button type="button" id="dependency_submit"  data-projectid="<?php echo $project_id;?>" data-btntype="save"  class="btn btn-success submit_dependency <?php echo $ele_disabled ;?>">Save</button>
		 <button type="button" id="other_submit"   data-projectid="<?php echo $project_id;?>"  class="btn btn-success submit_element submitted <?php echo $ele_disabled ;?>">Save</button>
		 <button type="button" class="btn btn-danger"  data-projectid="<?php echo $project_id;?>"  id="cancel_button" data-dismiss="modal">Close</button>
	</div>
<?php echo $this->Form->end();

$username = get_user_data($this->Session->read('Auth.User.id'));
$userfullname = '';
if(isset($username)){
$userfullname = $username['UserDetail']['first_name'].' '.$username['UserDetail']['last_name'];
}
?>
<script type="text/javascript" >
	var start = '<?php echo (isset($mindate_workspace) && !empty($mindate_workspace) )? $mindate_workspace : date('d M Y'); ?>';
    var end = '<?php echo ( isset($maxdate_workspace) && !empty($maxdate_workspace) ) ? $maxdate_workspace : date('d M Y');?>';
    $(".open-start-date-picker").click(function() {
        $(this).parents('.input-group:first').find(".start_date").datepicker('show').focus();
    })
    $(".open-end-date-picker").click(function() {
        $(this).parents('.input-group:first').find(".end_date").datepicker('show').focus();
    })

    $(".start_date").datepicker({
        minDate: start,
        maxDate: end,
        dateFormat: 'dd M yy',
        changeMonth: true,
        beforeShow: function(input, inst) {
            setTimeout(function() {
                inst.dpDiv.zIndex(9999999);
            }, 2)
        },
        onClose: function(selectedDate) {
            if (selectedDate == '') {
                $(this).parents('.date-picks:first').find(".end_date").datepicker("option", "minDate", start);
            } else {
                $(this).parents('.date-picks:first').find(".end_date").datepicker("option", "minDate", selectedDate);
            }

        },
        onSelect: function(selectedDate) {
            if (start == '') {
                this.value = '';
                $("#dateAlertBox").modal("show");
            } else {

                $(this).parents('.date-picks:first').find(".end_date").datepicker("setDate", selectedDate);
                $(this).parents('.date-picks:first').find(".end_date").datepicker("option", "minDate", selectedDate);
            }
        }
    });
	//console.log($('.date-picks').find(".start_date").val())
    $(".end_date").datepicker({
        minDate: $('.date-picks').find(".start_date").val(),
        maxDate: end,
        dateFormat: 'dd M yy',
        changeMonth: true,
        beforeShow: function(input, inst) {
            setTimeout(function() {
                inst.dpDiv.zIndex(9999999)
            }, 2)
        },
        onClose: function(selectedDate) {
            if (selectedDate != '') {
                start = selectedDate;
                $(this).datepicker("setDate", selectedDate);
            }



        },
    });
    // end
    $(function(){
    	/*$('.estimated_history').popover({
    		container: ".modal-open",
    		placement: 'top'
    	})*/
    })
	</script>
	<?php echo $this->Html->script('projects/task_center_popup', array('inline' => true)); ?>
<?php } ?>