 
<style>
.date_box {
	display: block;
	border: 1px solid #ccc;
	padding: 20px 5px 10px 5px;
	display: none;
}
.start-date-errors, .end-date-errors, .start-end-date-errors {
	display: block;
}
 
.form-control[disabled], .form-control[readonly], fieldset[disabled] .form-control{ cursor : default;} 
.date_constraints_wrappers { overflow : hidden;}
</style>


<?php
$workspace_id = $response['workspace_id'];
$wsp_status = $this->Common->restrict_copy_paste($workspace_id);
 
 if( isset($response) && !empty($response) )  { ?>


		<?php
			echo $this->Form->create('Element', array('url' => array('controller' => 'entities', 'action' => 'task_list_save_elements', $response['area_id']), 'class' => 'form-bordered', 'id' => 'modelFormUpdateElement', 'data-async' => "")); 
		?>
<!-- POPUP MODEL BOX CONTENT HEADER -->
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h3 class="modal-title" id="createModelLabel">Task Schedules</h3>

	</div>

	<!-- POPUP MODAL BODY -->
	<div class="modal-body elements-list">
		
		<?php if( isset($response['all_elements']) && !empty($response['all_elements']) )  {  ?>
		
			<?php foreach( $response['all_elements'] as $key => $val )  {
				$el = $val['Element'];
			?>
			
				<div class="panel panel-default panel-els">
					<div class="panel-body">
					
						<?php  
						echo $this->Form->input('Element.id', [  'name'=>'data['.$key.'][id]', 'type' => 'hidden', 'value' => $el['id'] ] );
						?>
					<?php $element_status = element_status($el['id']); ?>
						<div class="form-group">
							<label class=" " for="title">Title:</label>
							<div class="title-group">
								<div class="title-group-input cell_<?php echo $element_status; ?>"  >
									<?php echo strip_tags($el['title']); ?>
								</div>
								<div class="title-group-addon"  data-toggle="collapse" href="#collapseDates_<?php echo $el['id'] ?>" aria-expanded="false" aria-controls="collapseDates_<?php echo $el['id'] ?>">
									<i class="fa"></i>
								</div>
							</div>
						</div>
					
						<div class="date_constraints_wrappers collapse" id="collapseDates_<?php echo $el['id'] ?>" data-section="<?php echo $el['id'] ?>" >
							
							<span id="" style="" class="error-message text-danger start-date-errors err"></span>
							<span id="" style="" class="error-message text-danger end-date-errors err"></span>
							<span id="" style="" class="error-message text-danger start-end-date-errors err"></span>
							
							<div class="form-group clearfix col-sm-6">
								<label class="" for="start_date" style="padding-top: 6px; padding-left: 0px;">Start Date:</label>
								<div class="input-group">
									<?php 
									$stdate = isset($el['start_date']) && !empty($el['start_date']) ? date("d M Y", strtotime($el['start_date'])) : '';
									echo $this->Form->input('Element.start_date', [ 'name'=>'data['.$key.'][start_date]', 'type' => 'text', 'label' => false, 'div' => false, 'id' => 'start_date_'.$key, 'required' => false,  'readonly' => 'readonly', 'class' => 'form-control dates input-small start_date', 'value' => $stdate ]); ?>
									
									<div class="input-group-addon  open-start-date-picker calendar-trigger">
										<i class="fa fa-calendar"></i>
									</div>
								</div>
								<span id="start_date_err" class="error-message text-danger"> </span> 
							</div>
							
							<div class="form-group clearfix col-sm-6">
								<label class="" for="end_date" style="padding-top: 6px; padding-left: 0px;">End Date:</label>
								<div class="input-group">
									<?php 
									$endate = isset($el['end_date']) && !empty($el['end_date']) ? date("d M Y", strtotime($el['end_date'])) : '';
									echo $this->Form->input('Element.end_date', [ 'name'=>'data['.$key.'][end_date]', 'type' => 'text', 'label' => false, 'div' => false, 'id' => 'end_date_'.$key, 'required' => false,  'readonly' => 'readonly', 'class' => 'form-control dates input-small end_date', 'value' => $endate]); ?>
									
									<div class="input-group-addon  open-end-date-picker calendar-trigger">
										<i class="fa fa-calendar"></i>
									</div>
								</div>
								<span id="end_date_err" class="error-message text-danger"> </span> 
							</div>
							<div class="form-group clearfix error-message text-danger col-sm-12 hide" style="text-align: center; font-size: 13px; margin-bottom: 2px;" id="overduewsp">
								<?php echo $wsp_status['message']; ?>
							</div>
						</div>
					</div>
				</div>
			
			<?php } ?>
		
		<?php } ?>
		
	</div>
	
	<!-- POPUP MODAL FOOTER -->
	<div class="modal-footer">
		 <?php if( isset($wsp_status['success']) && $wsp_status['success'] == true ){ ?>
		 <button type="button"  class="btn btn-success submit_element submitted">Save</button><?php } ?>
		 <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
	</div>
	<?php echo $this->Form->end(); ?>
	
	
	<?php if( isset($response['area_id']) && !empty($response['area_id']) )  {
			
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
		
		$cur_date = date("d M Y");
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
		
		// echo $mindate_elm_cal .'  ' .$maxdate_workspace;
	
	if( !isset($wsp_status['success']) || empty($wsp_status['success']) || $wsp_status['success'] == false ){
	?>	 
	<script type="text/javascript" >
		$(function() { 
			$(".open-start-date-picker").css("pointer-events", "none");
			$(".open-end-date-picker").css("pointer-events", "none");
			$(".start_date").prop('disabled',true);
			$(".end_date").prop('disabled',true); 
			$("#overduewsp").removeClass('hide').show();	
		});	
	</script>
	<?php } ?>
	
	<script type="text/javascript" >
		$(function() { 
		var start = '<?php echo $mindate_workspace; ?>';
		var current = '<?php echo $cur_date; ?>';
        var end = '<?php echo $maxdate_workspace; ?>';
		var wsp_end_date = '<?php echo strtotime($maxdate_workspace); ?>';
		var wsp_cur_date = '<?php echo strtotime($cur_date); ?>';
		
		
		
		// var start = '<?php echo $mindate; ?>';
        // var end = '<?php echo $maxdate; ?>';
		$(".open-start-date-picker").click(function () {
			$(this).parents('.input-group:first').find(".start_date").datepicker('show').focus();
        })
        $(".open-end-date-picker").click(function () {
            $(this).parents('.input-group:first').find(".end_date").datepicker('show').focus();
        })
		
        $(".start_date").datepicker({
            minDate: current,
            maxDate: end,
            dateFormat: 'dd M yy',
            changeMonth: true,
			beforeShow: function( input, inst ) { 
				setTimeout(function(){
					inst.dpDiv.zIndex(9999999)
				}, 2)
			},
            onClose: function (selectedDate) { 
                if (selectedDate == '') {
                    $(this).parents('.date_constraints_wrappers:first').find(".end_date").datepicker("option", "minDate", start);
                } else {
                    $(this).parents('.date_constraints_wrappers:first').find(".end_date").datepicker("option", "minDate", selectedDate);
                }

            },
            onSelect: function (selectedDate) {
                if (start == '') {
                    this.value = '';
                    $("#dateAlertBox").modal("show");
                } else {

                    $(this).parents('.date_constraints_wrappers:first').find(".end_date").datepicker("setDate", selectedDate);
                    $(this).parents('.date_constraints_wrappers:first').find(".end_date").datepicker("option", "minDate", selectedDate);
                }
            }
        });
		//console.log(start)
        $(".end_date").datepicker({
            minDate: start,
            maxDate: end,
            dateFormat: 'dd M yy',
            changeMonth: true,
			beforeShow: function( input, inst ) {
				setTimeout(function(){
					inst.dpDiv.zIndex(9999999)
				}, 2)
			},
			onClose: function (selectedDate) { 
                if (selectedDate != '') {
                    start = selectedDate;
                    $(this).datepicker("setDate", selectedDate);
                    // $(this).datepicker("option", "minDate", start);
                    // $(this).datepicker("option", "maxDate", end);
                   // $("#start_date").datepicker("option", "maxDate", start);
                }


            },
            /* onSelect: function (selectedDate) {
					console.log('onSelect= ',$(this))
                if (selectedDate != '') {
                    start = selectedDate;
                    $(this).datepicker("setDate", selectedDate);
                    $(this).datepicker("option", "minDate", start);
                    $(this).datepicker("option", "maxDate", end);
                  //  $("#start_date").datepicker("option", "maxDate", start);
                }
            }	 */		
			
			
        });
        // end
		
		$('#modal_box').on('hidden.bs.modal', function () {
			$(this).removeData('bs.modal');
		});
		
		

		
			

	})
	</script> 
<?php } ?> 