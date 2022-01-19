<style type="text/css">
.box-header i, .box-header h3 {
    cursor: default !important;
}
.cr-icon.glyphicon{
	cursor: pointer !important;
}
 .estimated-cost label{
	padding-top: 0;
 }
 #dependency_submit {
    display: inline-block !important;
}
 
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
		//================================================================================================
	?>

		<?php
			echo $this->Form->create('Element', array('url' => array('controller' => 'dashboards', 'action' => 'task_list_save_elements', $response['area_id']), 'class' => 'form-bordered', 'id' => 'modelFormUpdateElement', 'data-async' => ""));
		?>
<!-- POPUP MODEL BOX CONTENT HEADER -->
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h3 class="modal-title" id="createModelLabel">Task Schedules</h3>

	</div>

	<!-- POPUP MODAL BODY -->
	<div class="modal-body elements-list">

		<ul class="nav nav-tabs elements-tabs">
	        <?php /*<li class="active">
	            <a id="tab_dates" class="active" href="#element_dates" data-toggle="tab">Dates</a>
	        </li> */ ?>
			<?php if( isset($project_permit_type) && $project_permit_type == true ) { /*?>
	        <li class="">
	            <a id="tab_cost" href="#element_cost" data-toggle="tab">Costs</a>
	        </li>*/ ?>
			<li class="active">
	            <a id="tab_dependency" href="#element_dependency" data-toggle="tab">Dependency</a>
	        </li>
			<?php } ?>
	    </ul>

		<div id="elementTabContent" class="tab-content">	        
			<div class="tab-pane fade active in" id="element_dependency">
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
								echo $this->Form->input('Element.id', [  'name'=>'data[0][id]', 'type' => 'hidden', 'value' => $el['id'] ] );
							?>
								<div class="col-sm-12 clearfix nopadding" >
									<div class="col-sm-12 clearfix nopadding-left dependency-title">
										<label class="" for="start_date" style="padding-left: 5px;">Workspace Start:&nbsp;<?php
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
					                      <th style="width: 20%;">Start</th>
					                      <th style="width: 20%;">End</th>
					                      <th style="width: 20%;">Dependencies</th>
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
					                      			<input type="checkbox" data-crit="<?php echo $el['id'];?>" <?php echo $is_criticalfirst;?> name="data[ElementDependency][<?php echo $el['id'];?>][is_critical]" class="owner_level">
					                      			<span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>
					                      		</label>
					                      	</div>
					                      </td>
					                      <td>
					                      	<div class="col-sm-12 clearfix nopadding">
						                        <div class="col-sm-12 clearfix nopadding-left">
						                        	<div class="input-group depend_<?php echo($element_status); ?>">
						                        		<?php
						                        		$we_stdate = isset($el['start_date']) && !empty($el['start_date']) ? date("d M, Y", strtotime($el['start_date'])) : '';
						                        		echo $this->Form->input('Element.start_date', [ 'name'=>'data[0][start_date]', 'type' => 'text', 'label' => false, 'div' => false, 'id' => 'start_date_0', 'required' => false,  'readonly' => 'readonly', 'class' => 'form-control depend-input', 'value' => $we_stdate ]); ?>

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
						                        		$we_endate = isset($el['end_date']) && !empty($el['end_date']) ? date("d M, Y", strtotime($el['end_date'])) : '';
						                        		echo $this->Form->input('Element.end_date', [ 'name'=>'data[0][end_date]', 'type' => 'text', 'label' => false, 'div' => false, 'id' => 'end_date_0', 'required' => false,  'readonly' => 'readonly', 'class' => 'form-control depend-input', 'value' => $we_endate]); ?>

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
					                    </tr>

									   <?php
										// pr($used_dependency_elements);
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
					                      			<input type="checkbox" data-crit="<?php echo $element_id;?>" <?php echo $is_critical;?> name="data[ElementDependency][<?php echo $element_id;?>][is_critical]" class="owner_level">
					                      			<span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>
					                      		</label>
					                      	</div>
					                      </td>
					                      <td>
					                      	<div class="col-sm-12 clearfix nopadding">
						                        <div class="col-sm-12 clearfix nopadding-left">
						                        	<div class="input-group depend_<?php echo($element_status); ?>">
						                        		<?php
						                        		$we_stdate = isset($we_element['start_date']) && !empty($we_element['start_date']) ? date("d M, Y", strtotime($we_element['start_date'])) : '';
						                        		echo $this->Form->input('Element.start_date', [ 'name'=>'data[0][start_date]', 'type' => 'text', 'label' => false, 'div' => false, 'id' => 'start_date_0', 'required' => false,  'readonly' => 'readonly', 'class' => 'form-control depend-input', 'value' => $we_stdate ]); ?>

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
						                        		$we_endate = isset($we_element['end_date']) && !empty($we_element['end_date']) ? date("d M, Y", strtotime($we_element['end_date'])) : '';
						                        		echo $this->Form->input('Element.end_date', [ 'name'=>'data[0][end_date]', 'type' => 'text', 'label' => false, 'div' => false, 'id' => 'end_date_0', 'required' => false,  'readonly' => 'readonly', 'class' => 'form-control depend-input', 'value' => $we_endate]); ?>

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
												<span href="#" class="dropdown-toggle status-drop" id="status-drop" data-toggle="dropdown" aria-controls="status-dropdown" aria-expanded="false"><span class="relationshipnone"><?php echo $relationmsg;?></span> <span class="fa fa-times bg-red clear_status_filters"></span></span>
												<ul class="dropdown-menu status-dropdown" aria-labelledby="status-drop" class="status-dropdown">
													<li><a id="predecessor-tab" data-dep="1" aria-controls="dropdown1" data-text="Predecessor" >Predecessor<i <?php echo $stylerelpre;?> class="fa fa-check"></i></a></li>
	                  						    	<li><a id="successor-tab" data-dep="2" aria-controls="dropdown1" data-text="Successor" >Successor<i  <?php echo $stylerelsuc;?> class="fa fa-check"></i></a></li>
												</ul>
											</span>

												<?php

												echo $this->Form->input('ElementDependancyRelationship.dependency', ['type' => 'hidden', 'label' => false, 'div' => false, 'required' => false,  'readonly' => 'readonly', 'class' => 'form-control dependency', 'value' => $relationship, 'name'=>'ElementDependancyRelationship['.$we_element["id"].'][dependency]']) ;


											?>
	                  					    </span>
					                      </td>
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
					                      <th style="width: 20%;">Start</th>
					                      <th style="width: 20%;">End</th>
					                      <th style="width: 20%;">Dependencies</th>
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
					                      			<input type="checkbox" data-crit="<?php echo $we_element['id'];?>" <?php echo $is_criticalst;?> name="data[ElementDependency][<?php echo $we_element['id'];?>][is_critical]" class="owner_level">
					                      			<span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>
					                      		</label>
					                      	</div>
					                      </td>
					                      <td>
					                      	<div class="col-sm-12 clearfix nopadding">
						                        <div class="col-sm-12 clearfix nopadding-left">
						                        	<div class="input-group depend_<?php echo($element_status); ?>">
						                        		<?php
						                        		$we_stdate = isset($we_element['start_date']) && !empty($we_element['start_date']) ? date("d M, Y", strtotime($we_element['start_date'])) : '';
						                        		echo $this->Form->input('Element.start_date', [ 'name'=>'data[0][start_date]', 'type' => 'text', 'label' => false, 'div' => false, 'id' => 'start_date_0', 'required' => false,  'readonly' => 'readonly', 'class' => 'form-control depend-input', 'value' => $we_stdate ]); ?>

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
						                        		$we_endate = isset($we_element['end_date']) && !empty($we_element['end_date']) ? date("d M, Y", strtotime($we_element['end_date'])) : '';
						                        		echo $this->Form->input('Element.end_date', [ 'name'=>'data[0][end_date]', 'type' => 'text', 'label' => false, 'div' => false, 'id' => 'end_date_0', 'required' => false,  'readonly' => 'readonly', 'class' => 'form-control depend-input', 'value' => $we_endate]); ?>

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
												<span href="#" class="dropdown-toggle status-drop" id="status-drop" data-toggle="dropdown" aria-controls="status-dropdown" aria-expanded="false"><span class="relationshipnone"><?php echo $relationmsg;?></span> <span class="fa fa-times bg-red clear_status_filters"></span></span>
												<ul class="dropdown-menu status-dropdown" aria-labelledby="status-drop" class="status-dropdown">
													<li><a id="predecessor-tab" data-dep="1" aria-controls="dropdown1" data-text="Predecessor" >Predecessor<i <?php echo $stylerelpre;?> class="fa fa-check"></i></a></li>
	                  						    	<li><a id="successor-tab" data-dep="2" aria-controls="dropdown1" data-text="Successor" >Successor<i  <?php echo $stylerelsuc;?> class="fa fa-check"></i></a></li>
												</ul>
											</span>

												<?php

												echo $this->Form->input('ElementDependancyRelationship.dependency', ['type' => 'hidden', 'label' => false, 'div' => false, 'required' => false,  'readonly' => 'readonly', 'class' => 'form-control dependency', 'value' => $relationship, 'name'=>'ElementDependancyRelationship['.$we_element["id"].'][dependency]']) ;


											?>
	                  					    </span>
					                      </td>



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

		</div>

	</div>

	<!-- POPUP MODAL FOOTER -->
	<div class="modal-footer">
		 <button type="button" id="dependency_submit_update" data-btntype="update" class="btn btn-success submit_dependency">Update</button>
		 <button type="button" id="dependency_submit"  data-btntype="save"  class="btn btn-success submit_dependency " >Save</button>		 
		 <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
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
		
		 
 		
    	$('.estimated_history').popover({
    		container: ".modal-open",
    		placement: 'top'
    	})
    })
	</script>
	<?php echo $this->Html->script('projects/task_center_popup', array('inline' => true)); ?>
<?php } ?>