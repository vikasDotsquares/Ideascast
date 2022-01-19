<style type="text/css">
	.high-z-index {
		z-index: 1050 !important;
	}
	.error-message {
		display: table;
	}
</style>

<?php

	$date_workspace = $this->Common->getDateStartOrEnd_elm($workspace_id);
    $cur_date = date("d-m-Y");
    $mindate_project = isset($pdata[0]['Project']['start_date']) && !empty($pdata[0]['Project']['start_date']) ? $pdata[0]['Project']['start_date'] : '';
    $maxdate_project = isset($pdata[0]['Project']['end_date']) && !empty($pdata[0]['Project']['end_date']) ? $pdata[0]['Project']['end_date'] : '';


    $mindate_workspace = isset($date_workspace['start_date']) && !empty($date_workspace['start_date']) ? date("d-m-Y", strtotime($date_workspace['start_date'])) : '';

    if(isset($mindate_workspace) && !empty($mindate_workspace)){
    $mindate_workspace = $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($mindate_workspace)),$format = 'd-m-Y');
    }

    $maxdate_workspace = isset($date_workspace['end_date']) && !empty($date_workspace['end_date']) ? date("d-m-Y", strtotime($date_workspace['end_date'])) : '';

    if(isset($maxdate_workspace) && !empty($maxdate_workspace)){
    $maxdate_workspace = $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($maxdate_workspace)),$format = 'd-m-Y');
    }

    $mindate_elm = isset($edata['start_date']) && !empty($edata['start_date']) ? date("d-m-Y", strtotime($edata['start_date'])) : '';

    if(isset($mindate_elm) && !empty($mindate_elm)){
    	$mindate_elm = $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($mindate_elm)),$format = 'd-m-Y');
    }

    $maxdate_elm = isset($edata['end_date']) && !empty($edata['end_date']) ? date("d-m-Y", strtotime($edata['end_date'])) : '';


    if(isset($maxdate_elm) && !empty($maxdate_elm)){
    	$maxdate_elm = $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($maxdate_elm)),$format = 'd-m-Y');
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
		<h4 class="modal-title" id="myModalLabel">Duplicate Task</h4>

	</div>

	<!-- POPUP MODAL BODY -->
	<div class="modal-body">
		<div class="row">
			<div class="col-lg-6">
				<div class="form-group">
					<label class=" control-label" for="area_title">Area: <sup>*</sup></label>
					<?php echo $this->Form->select('Element.area_id', $area_list, ['value' => $area_id, 'empty' => 'Select Area', 'class' => 'form-control', 'required' => false  ]); ?>
		            <span class="error-message error text-danger"></span>
				</div>
			</div>
			<div class="col-lg-6">
				<div class="form-group">
					<label class=" control-label" for="area_title">Type: <sup>*</sup></label>
					<?php echo $this->Form->select('ElementType.type_id', $projectEleType, array('default' => $selElementType, 'escape' => false, 'class' => 'form-control','empty'=>false, 'id' => 'type_id', 'empty'=>'Select Task Type' )); ?>
		            <span class="error-message error text-danger"></span>
				</div>
			</div>

		</div>
		<div class="form-group">
			<label class="  control-label" for="txa_title">Title: <sup>*</sup></label>
			<?php echo $this->Form->text('Element.title', [ 'class'	=> 'form-control', 'id' => 'txa_title', 'escape' => true,  'placeholder' => '', 'style'=>'','autocomplete' => 'off', 'value' => $edata['title'] ] );   ?>
			<span class="error-message text-danger" ></span>
		</div>

		<div class="form-group">
			<label class="  control-label" for="txa_description">Description: <sup>*</sup></label>
			<?php echo $this->Form->textarea('Element.description', [ 'class'	=> 'form-control task-desp', 'id' => 'txa_description', 'escape' => true, 'rows' => 3, 'placeholder' => '','style'=>'', 'value' => $edata['description'] ] ); ?>
			<span class="error-message text-danger" ></span>
		</div>
		<div class="form-group">
			<label class="control-label " for="txa_outcome">Outcome:</label>
			<?php echo $this->Form->textarea('Element.comments', [ 'class'	=> 'form-control outcome', 'id' => 'txa_outcome', 'escape' => true, 'rows' => 3, 'placeholder' => '','style'=>'', 'value' => $edata['comments'] ] ); ?>
			<span class="error-message text-danger" ></span>
		</div>

		<div class="row taskschedule">
			<div class="form-group">
				<label class="control-label col-lg-2" for="start_date">Schedule:</label>
				<div class="col-lg-4 ">
					<div class="switch-task">
						<input type="checkbox" class="cmn-toggle cmn-toggle-round" id="date_constraint" name="data[Element][date_constraints]" <?php if($date_const){ ?> checked="checked" <?php } ?> value="1">
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
                <span class="sd_error error error-message text-danger" ></span>
            </div>
			<label class="control-label col-lg-2" for="start_date">End Date:</label>
			<div class="col-lg-4 ">
	            <div class="input-group">
	                <input name="data[Element][end_date]" value="<?php echo $end_date_w; ?>" id="end_date" class="form-control dates input-small" type="text" autocomplete="off">
	                <div class="input-group-addon data-new open-end-date-picker calendar-trigger">
	                    <i class="fa fa-calendar"></i>
	                </div>
	 			</div>
                <span class="ed_error error error-message text-danger" ></span>
            </div>

		</div>
	</div>

	<!-- POPUP MODAL FOOTER -->
	<div class="modal-footer">
		 <button type="submit" id="submitSave" class="btn btn-primary" >Duplicate</button>
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

	    $('#ElementAreaId').off('change').on("change", function(e) {
	    	$('.form-error').html('');
	    })

	    $("#submitSave").off('click').on("click", function(e) {
	        e.preventDefault();

	        var $this = $(this);
	        var area_id = $('#ElementAreaId').val();
	        var workspace_id = $('#workspace_id').val();
	        var project_id = $('#project_id').val();

	        var $form = $this.closest('form'),
	            action = $form.attr('action'),
	            $mBody = $form.find('.modal-body');

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
	            url: $js_config.base_url + 'entities/add_duplicate_task',
	            type: 'POST',
	            dataType: 'json',
	            data: $form.serializeArray(),
	            success: function(response, status, jxhr) {
	                $mBody.find("span.error-message").html('');
	                $this.removeClass('disabled');

	                if (response.success) {

	                    setTimeout(function() {
	                        $('#popup_model_box').modal('hide');
	                    }, 200);
	                    $.reload_wsp_progress().done(function(res){
	                    	$.adjust_resize();
	                    });
	                    $.reload_workspace();
	                    $.getWspTaskActivities();

	                }
	                else if(response.error != ''){
	                	$('.form-error').html(response.error);
	                }
	                else {
	                    $this.html('Save');
	                    if (!$.isEmptyObject(response.content)) {

	                        $form.find('.error-message.text-danger').html('');
	                        $.each(response.content, function(ele, msg) {
                                var $this = $("#" + ele, $form);
                                $this.parents('.form-group').find('.error-message.text-danger').html(msg);

                                if (ele == 'area_id'){
                                    $('#ElementAreaId').parent().find('.error').text(msg);
                                }
                                else if (ele == 'title'){
                                    $('.title_error').text(msg);
                                }
                                else if (ele == 'description' ){
                                    $('.desc_error').text(msg);
                                }
                                else if (ele == 'start_date' && $("#date_constraint").prop('checked') ){
                                    $('.sd_error').text(msg);
                                }
                                else if (ele == 'end_date' && $("#date_constraint").prop('checked') ){
                                    $('.ed_error').text(msg);
                                }
                                else if (ele == 'ElementType' ){
                                    console.log(response.content.ElementType.type_id)
                                    $('#type_id').parents('.form-group').find('.error-message.text-danger').html(response.content.ElementType.type_id);
                                }
                            })

	                    }
	                }
	            }
	        })
	    })

    	$('#txa_title').focus()


		$('body').delegate("#txa_title", "keyup focus", function(event){
			var characters = 50;
			event.preventDefault();
			$('.form-error').html('');
			var $error_el = $(this).next('.error-message:first');
			if (typeof $.input_char_count !== 'undefined' && $.isFunction($.input_char_count)) {
				$.input_char_count(this, characters, $error_el);
			}
		})

		$('body').delegate("#txa_description", "keyup focus", function(event){
			var characters = 750;
			event.preventDefault();
			$('.form-error').html('');
			var $error_el = $(this).next('.error-message:first');
			if (typeof $.input_char_count !== 'undefined' && $.isFunction($.input_char_count)) {
				$.input_char_count(this, characters, $error_el);
			}
		})

		$('body').delegate("#txa_outcome", "keyup focus", function(event){
			var characters = 2000;
			event.preventDefault();
			// var $error_el = $(this).parents("#NotesUpdateElementForm").find('.error-message:first');
			var $error_el = $(this).next('.error-message:first');
			if (typeof $.input_char_count !== 'undefined' && $.isFunction($.input_char_count)) {
				$.input_char_count(this, characters, $error_el);
			}
		})

		$("#date_constraint").on('change', function(e) {

			if( $(this).prop('checked') ) {
				$(".task-start-end-date").slideDown(300)
			}
			else {
				$(".task-start-end-date").slideUp(300);
				$('#start_date,#end_date').val('');
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

})

</script>