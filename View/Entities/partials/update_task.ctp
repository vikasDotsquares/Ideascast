<style type="text/css">
	.high-z-index {
		z-index: 1050 !important;
	}
	.error-message {
		display: table;
	}
	#up_task input, #up_task select, #up_task textarea {
		margin-bottom: 0;
	}
</style>

<?php

	$date_workspace = $this->Common->getDateStartOrEnd_elm($workspace_id);
    $cur_date = date("d-m-Y");
    $mindate_project = isset($pdata[0]['Project']['start_date']) && !empty($pdata[0]['Project']['start_date']) ? $pdata[0]['Project']['start_date'] : '';
    $maxdate_project = isset($pdata[0]['Project']['end_date']) && !empty($pdata[0]['Project']['end_date']) ? $pdata[0]['Project']['end_date'] : '';


    $mindate_workspace = isset($date_workspace['start_date']) && !empty($date_workspace['start_date']) ? date("d-m-Y", strtotime($date_workspace['start_date'])) : '';

    if(isset($mindate_workspace) && !empty($mindate_workspace)){
    //$mindate_workspace = $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($mindate_workspace)),$format = 'd-m-Y');
	 $mindate_workspace =   date('d-m-Y', strtotime($mindate_workspace));
    }

    $maxdate_workspace = isset($date_workspace['end_date']) && !empty($date_workspace['end_date']) ? date("d-m-Y", strtotime($date_workspace['end_date'])) : '';

    if(isset($maxdate_workspace) && !empty($maxdate_workspace)){
    //$maxdate_workspace = $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($maxdate_workspace)),$format = 'd-m-Y');
	$maxdate_workspace = date('d-m-Y', strtotime($maxdate_workspace));
    }

    $mindate_elm = isset($edata['start_date']) && !empty($edata['start_date']) ? date("d-m-Y", strtotime($edata['start_date'])) : '';

    if(isset($mindate_elm) && !empty($mindate_elm)){
    	//$mindate_elm = $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($mindate_elm)),$format = 'd-m-Y');
    	 $mindate_elm = date('d-m-Y', strtotime($mindate_elm));
    }

    $maxdate_elm = isset($edata['end_date']) && !empty($edata['end_date']) ? date("d-m-Y", strtotime($edata['end_date'])) : '';


    if(isset($maxdate_elm) && !empty($maxdate_elm)){
    	//$maxdate_elm = $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($maxdate_elm)),$format = 'd-m-Y');
		$maxdate_elm = date('d-m-Y', strtotime($maxdate_elm)); ;
    }

    $messageVar = 'Task';

    if (isset($mindate_elm) && empty($mindate_elm)) {
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
    if( !empty($mindate_workspace) ){
    	$mindate_elm_cal = $mindate_workspace;
    } else {
    	$mindate_elm_cal = $cur_date;
    }

    if( empty($mindate_elm_cal) ){
		$mindate_elm_cal = date("d M Y");
	}
	if( empty($maxdate_workspace) ){
		$maxdate_workspace = date("d M Y");
	}
	$element_start_date = $mindate_elm_cal;


	$projectEleType = $this->ViewModel->project_element_type($project_id);

	$start_date_w = isset($edata['start_date']) && !empty($edata['start_date']) ? date("d M Y", strtotime($edata['start_date'])) : '';
	$end_date_w = isset($edata['end_date']) && !empty($edata['end_date']) ? date("d M Y", strtotime($edata['end_date'])) :'';

	echo $this->Form->create('Element', array('url' => array('controller' => 'entities', 'action' => 'create_element' ), 'class' => 'form-bordered', 'id' => 'modelFormAddElement'));


	echo $this->Form->create('Element', array('url' => array('controller' => 'entities', 'action' => 'create_element' ), 'class' => 'form-bordered', 'id' => 'modelFormAddElement'));

	echo $this->Form->input('Element.create_activity', [ 'type' => 'hidden','value'=>true]);

	echo $this->Form->input('workspace_id', [ 'type' => 'hidden', 'value' => $workspace_id, 'id' => 'workspace_id' ] );
	echo $this->Form->input('project_id', [ 'type' => 'hidden', 'value' => $project_id, 'id' => 'project_id' ] );
	echo $this->Form->input('element_id', [ 'type' => 'hidden', 'value' => $element_id, 'id' => 'element_id' ] );

	$date_const = (isset($edata['date_constraints']) && !empty($edata['date_constraints'])) ? true : false;
	?>

<!-- POPUP MODEL BOX CONTENT HEADER -->
	<div class="modal-header">
		<button type="button" class="close close-skill" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h4 class="modal-title" id="myModalLabel">Edit Task</h4>

	</div>

	<!-- POPUP MODAL BODY -->
	<div class="modal-body" id="up_task">
		<div class="row">
			<div class="col-lg-6">
				<div class="form-group">
					<label class=" control-label" for="area_title">Area: <sup>*</sup></label>
					<?php echo $this->Form->select('Element.area_id', $area_list, ['value' => $area_id, 'empty' => 'Select Area', 'class' => 'form-control', 'required' => false  ]); ?>
		            <span class="error error text-danger"></span>
				</div>
			</div>
			<div class="col-lg-6">
				<div class="form-group">
					<label class=" control-label" for="area_title">Type: <sup>*</sup></label>
					<?php echo $this->Form->select('ElementType.type_id', $projectEleType, array('default' => $selElementType, 'escape' => false, 'class' => 'form-control','empty'=>false, 'id' => 'type_id', 'empty'=>'Select Task Type' )); ?>
		            <span class="error error text-danger"></span>
				</div>
			</div>

		</div>
		<div class="form-group">
			<label class="  control-label" for="txa_title">Title: <sup>*</sup></label>
			<?php echo $this->Form->text('Element.title', [ 'class'	=> 'form-control', 'id' => 'txa_title', 'escape' => true,  'placeholder' => '100 chars', 'style'=>'','autocomplete' => 'off', 'value' => $edata['title'] ] );   ?>
			<span class="error text-danger" ></span>
		</div>

		<div class="form-group">
			<label class="  control-label" for="txa_description">Description: <sup>*</sup></label>
			<?php echo $this->Form->textarea('Element.description', [ 'class'	=> 'form-control task-desp', 'id' => 'txa_description', 'escape' => true, 'rows' => 3, 'placeholder' => '500 chars','style'=>'', 'value' => $edata['description'] ] ); ?>
			<span class="error text-danger" ></span>
		</div>
		<div class="form-group">
			<label class="control-label " for="txa_outcome">Outcome:</label>
			<?php echo $this->Form->textarea('Element.comments', [ 'class'	=> 'form-control outcome', 'id' => 'txa_outcome', 'escape' => true, 'rows' => 3, 'placeholder' => '500 chars','style'=>'', 'value' => $edata['comments'] ] ); ?>
			<span class="error text-danger" ></span>
		</div>

		<div class="row taskschedule">
			<div class="form-group">
				<label class="control-label col-lg-2" for="date_constraint">Schedule:</label>
				<div class="col-lg-4 ">
					<div class="switch-task">
						<input type="checkbox" class="cmn-toggle cmn-toggle-round" id="date_constraint" name="data[Element][date_constraints]"  <?php if($date_const){ ?> checked="checked" <?php } ?> value="1">
						<label for="date_constraint"></label>
					</div>
				</div>
			</div>
		</div>
		<div class="row task-start-end-date" <?php if(!$date_const){ ?> style="display: none;" <?php } ?>>
			<label class="control-label col-lg-2" for="start_date">Start Date:</label>
				<div class="col-lg-4 ">
	            <div class="input-group ">
	                <input name="data[Element][start_date]" value="<?php echo $start_date_w; ?>" id="start_date" class="form-control dates input-small" type="text" autocomplete="off">
	                <div class="input-group-addon data-new open-start-date-picker calendar-trigger">
	                    <i class="fa fa-calendar"></i>
	                </div>
				</div>
                <span class="sd_error error text-danger" ></span>
            </div>
			<label class="control-label col-lg-2" for="end_date">End Date:</label>
			<div class="col-lg-4 ">
	            <div class="input-group">
	                <input name="data[Element][end_date]" value="<?php echo $end_date_w; ?>" id="end_date" class="form-control dates input-small" type="text" autocomplete="off">
	                <div class="input-group-addon data-new open-end-date-picker calendar-trigger">
	                    <i class="fa fa-calendar"></i>
	                </div>
	 			</div>
                <span class="ed_error error text-danger" ></span>
            </div>

		</div>
	</div>

	<!-- POPUP MODAL FOOTER -->
	<div class="modal-footer">
		 <button type="button" id="save_task" class="btn btn-primary" >Save</button>
		 <button type="button" class="btn btn-primary outline-btn-t" data-dismiss="modal">Cancel</button>
	</div>

		<?php echo $this->Form->end(); ?>

<?php
$strpos1 = explode('-', $edata['color_code']);
$row_color = 'panel-color-gray';
if(isset($strpos1) && !empty($strpos1)) {
	$sz = count($strpos1) - 1;
	$row_color = 'panel-color-'.$strpos1[$sz];
}
 ?>
<script type="text/javascript" >

    $(function() {


	    $("#save_task").off('click').on("click", function(e) {
	        e.preventDefault();

	        var $this = $(this);
	        var area_id = $('#ElementAreaId').val();

	        var $form = $this.closest('form');

            var error = false;

            if(area_id == '' || area_id === undefined){
            	$('#ElementAreaId').parent().find('.error-message').html('Area is required');
            	error = true;
            }
            if($('#type_id').val() == ''){
	        	$('#type_id').parents('.form-group').find('.error-message.text-danger').html('Type is required');
	        	error = true;
	        }
            if($('#txa_title').val() == ''){
	        	$('#txa_title').parents('.form-group').find('.error-message.text-danger').html('Title is required');
	        	error = true;
	        }
            if($('#txa_description').val() == ''){
	        	$('#txa_description').parents('.form-group').find('.error-message.text-danger').html('Description is required');
	        	error = true;
	        }
            if($('#date_constraint').prop('checked') && $('#start_date').val() == ''){
	        	$('.sd_error').html('Start Date is required');
	        	error = true;
	        }
            if($('#date_constraint').prop('checked') && $('#end_date').val() == ''){
	        	$('.ed_error').html('End Date is required');
	        	error = true;
	        }

	        if(error){
	        	return;
	        }

	        $this.addClass('disabled');
	        $.ajax({
	            url: $js_config.base_url + 'entities/save_task_detail',
	            type: 'POST',
	            dataType: 'json',
	            data: $form.serializeArray(),
	            success: function(response, status, jxhr) {
	                $this.removeClass('disabled');

	                if (response.success) {
                        $('#popup_model_box').modal('hide');
                        location.reload();
	                }
	            }
	        })
	    })


		$('body').delegate("#txa_title", "keyup focus", function(event){
			var characters = 100;
			event.preventDefault();
			var $error_el = $(this).next('.error:first');
			if (typeof $.input_char_count !== 'undefined' && $.isFunction($.input_char_count)) {
				$.input_char_count(this, characters, $error_el);
			}
		})

		$('body').delegate("#txa_description", "keyup focus", function(event){
			var characters = 500;
			event.preventDefault();
			var $error_el = $(this).next('.error:first');
			if (typeof $.input_char_count !== 'undefined' && $.isFunction($.input_char_count)) {
				$.input_char_count(this, characters, $error_el);
			}
		})

		$('body').delegate("#txa_outcome", "keyup focus", function(event){
			var characters = 500;
			event.preventDefault();
			var $error_el = $(this).next('.error:first');
			if (typeof $.input_char_count !== 'undefined' && $.isFunction($.input_char_count)) {
				$.input_char_count(this, characters, $error_el);
			}
		})

		var start = '<?php echo date("d M Y", strtotime($mindate_elm_cal)); ?>';
		var end_start = '<?php echo date("d M Y", strtotime($element_start_date)); ?>';
        var end = '<?php echo date("d M Y", strtotime($maxdate_workspace)); ?>';

        $(".open-start-date-picker").click(function () {
            $("#start_date").datepicker('show').focus();
        })
        $(".open-end-date-picker").click(function () {
            $("#end_date").datepicker('show').focus();
        })
        $("#start_date").datepicker({
            minDate: start,
            maxDate: end,
            dateFormat: 'dd M yy',
            changeMonth: true,
            beforeShow: function(el, inst){
            	inst.dpDiv.addClass('high-z-index');
            },
            onClose: function (selectedDate, inst) {
                if (selectedDate == '') {
                    $("#end_date").datepicker("option", "minDate", start);
                } else {
                    $("#end_date").datepicker("option", "minDate", selectedDate);
                }
                inst.dpDiv.removeClass('high-z-index');
            },
            onSelect: function (selectedDate) {
                if (start == '') {
                    this.value = '';
                    $("#dateAlertBox").modal("show");
                }
            }
        });

        $("#end_date").datepicker({
            minDate: end_start,
            maxDate: end,
            dateFormat: 'dd M yy',
            changeMonth: true,
            beforeShow: function(el, inst){
            	inst.dpDiv.addClass('high-z-index');
            },
			onClose: function (selectedDate, inst) {
                if (selectedDate != '') {
                    start = selectedDate;
                }
                inst.dpDiv.removeClass('high-z-index');
            },
            onSelect: function (selectedDate) {
                if (selectedDate != '') {
                    start = selectedDate;
                    $("#end_date").datepicker("setDate", selectedDate);
                }
            }
        });
        // end

		$("#date_constraint").on('change', function(e) {

			if( $(this).prop('checked') ) {
				$(".task-start-end-date").slideDown(300)
			}
			else {
				$(".task-start-end-date").slideUp(300);
				$('#start_date,#end_date').val('');
			}
		})

})

</script>