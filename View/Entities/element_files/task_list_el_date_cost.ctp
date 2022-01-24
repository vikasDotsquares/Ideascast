<style type="text/css">
	.box-header i, .box-header h3 {
	    cursor: default !important;
	}
	.cr-icon.glyphicon{
		cursor: pointer !important;
	}
	 .estimated-cost label{
		padding-top: 8px;
	 }

	.popover-content {
		height:300px;
	}
	.cell_progress {
	    max-width: 100%;
	    white-space: nowrap;
	    overflow: hidden;
	    text-overflow: ellipsis;
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
		<h3 class="modal-title" id="createModelLabel">Task Costs</h3>

	</div>

	<!-- POPUP MODAL BODY -->
	<div class="modal-body elements-list">

		<div id="elementTabContent" class="tab-content">
			<div class="tab-pane fade active in" id="element_cost">
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

				    	$currency_symbol = '<i class="fa fa-gbp"></i>';
					    if(isset($project_detail['currency_id']) && !empty($project_detail['currency_id'])) {
					        $currency_detail = getByDbId("Currency", $project_detail['currency_id'], ['id', 'country', 'name', 'symbol', 'sign']);
					        $currency_detail = $currency_detail['Currency'];
					        $currency_symbol = $currency_detail['sign'];
					        // pr($currency_detail);
					    }
					    if($currency_symbol == 'USD') {
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
					    }
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
										<div class="cell_<?php echo $element_status; ?>" style=""  >
											<?php /* if( strlen(strip_tags(trim($el['title']))) > 35 ){
												echo substr(strip_tags(trim($el['title'])),0,35)."...";
											} else { */
												echo strip_tags(trim($el['title']));
											//}
											 ?>
										</div>
									</div>
								</div></div>
								<div class="col-sm-3">
										<label class="" for="start_date">Start Date:</label>
										<div class="input-group">
											<?php
											$stdate = isset($el['start_date']) && !empty($el['start_date']) ? date("d M Y", strtotime($el['start_date'])) : '';
											echo $this->Form->input('Element.start_date', ['id'=>'coststartdate', 'name'=>'data[0][start_date]', 'type' => 'text', 'label' => false, 'div' => false, 'required' => false,  'readonly' => 'readonly', 'class' => 'form-control elementStartDateCost ', 'value' => $stdate ]); ?>

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
											echo $this->Form->input('Element.end_date', ['id'=>'costenddate', 'name'=>'data[0][end_date]', 'type' => 'text', 'label' => false, 'div' => false, 'required' => false,  'readonly' => 'readonly', 'class' => 'form-control elementEndDateCost', 'value' => $endate]); ?>

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
							<?php
							$estimatedcost = $this->Common->element_cost_history($el['id']);
										$spendcost = $this->Common->element_cost_history($el['id']);
										 ?>
							<div class="depend-table-cost">
								<div align="center" id="element_success_msg" style="padding-bottom:16px;color:green; display:none;">Element Cost saved</div>
								<div class="row">
									<div class="col-sm-2 estimated-cost"><label>Budget:</label></div>
									<div class="col-sm-4 col-md-4 col-lg-3 estimated-cost">

										<?php
											 if(isset($costESDetails['ElementCost']['estimated_cost']) && !empty($costESDetails['ElementCost']['estimated_cost'])){
												$valueconst = $costESDetails['ElementCost']['estimated_cost'];
											} else {
												$valueconst = 0;
											}


										?>
										<div class="input-group readonly">
											<span class="input-group-addon"><?php echo $currency_symbol;?></span>
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
										class="col-sm-1 fa fa-history estimated_history <?php if( !empty($estimatedcost) && count($estimatedcost)> 0 ){?>pophovers<?php } ?>"
										title="Budget History"
										data-popover-content="#myPopoverEstimateCost" ></i>

								</div>
								<div class="row"><div class="col-sm-12"><span id="estimated_cost_err" class="error-message text-danger"> </span></div></div>
								<div class="row">&nbsp;</div>
								<div class="row">
									<div class="col-sm-2 estimated-cost"><label>Actual:</label></div>
									<div class="col-sm-4 col-md-4 col-lg-3 estimated-cost">
										<?php
											if(isset($costSPDetails['ElementCost']['spend_cost']) && !empty($costSPDetails['ElementCost']['spend_cost'])){
												$valuesconst = $costSPDetails['ElementCost']['spend_cost'];
											} else {
												$valuesconst = 0;
											}
										?>
										<div class="input-group readonly">
											<span class="input-group-addon"><?php echo $currency_symbol;?></span>
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

									<i data-historytype="spend_cost" data-elementid="<?php echo $el['id'];?>" title="Actual History"  data-popover-content="#myPopoverSpendCost" rel="popover" class="fa fa-history estimated_history col-sm-1 <?php if( !empty($spendcost) && count($spendcost)> 0 ){?>pophoversw<?php } ?>" id="spendHistoryCost"  data-historytype="spend_cost" aria-hidden="true" style="cursor: pointer !important;" ></i>



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
	<?php
	$cky = $this->requestAction('/projects/CheckProjectType/'.$eleproject_id.'/'.$this->Session->read('Auth.User.id'));
	?>

		 <a href="<?php echo SITEURL.'costs/index/'.$cky.':'.$eleproject_id;?>" class="btn btn-success " >Open Cost Center</a>
		 <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
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
	<?php echo $this->Html->script('projects/task_entities_popup', array('inline' => true)); ?>
<?php } ?>