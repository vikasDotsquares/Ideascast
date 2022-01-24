<?php
echo $this->Html->css('projects/bs-selectbox/bootstrap-multiselect');
echo $this->Html->script('projects/plugins/selectbox/bootstrap-multiselect', array('inline' => true));
if(isset($projects) && !empty($projects)){
	$projects = array_map(function($v){
		return  htmlentities($v  ,ENT_QUOTES, "UTF-8");
	}, $projects);
}
?>
<div class="modal-header">
	<button type="button" class="close close-prog" data-dismiss="modal" aria-hidden="true">&times;</button>
	<h4 class="modal-title">Add Program</h4>
</div><div class="modal-body popup-select-icon">
	<div class="row">
		<!-- LEFT -->
		<div class="col-sm-6">
			<div class="form-group">
				<label for="UserUser" class="control-label">Name: <sup class="text-danger">*</sup></label>
				<div class="form-control-skill">
					<?php echo $this->Form->input('Program.name', array('type' => 'text', 'label' => false, 'div' => false, 'class' => 'form-control', 'maxlength'=>100, 'autocomplete'=>'off', 'id'=>'program_name', 'placeholder' => '100 chars')); ?>
					<span class="error text-red"></span>
				</div>
			</div>
			<div class="form-group">
				<label for="UserUser" class="control-label">Projects:</label>
				<div class="form-control-skill">
					<?php echo $this->Form->input('Program.projects', array('type' => 'select', 'options' => $projects, 'label' => false, 'div' => false, 'class' => 'form-control', 'multiple' => 'multiple', 'id' => 'projects' )); ?>
					<span class="error text-red"></span>
				</div>
			</div>
		</div>
		<!-- RIGHT -->
		<div class="col-sm-6">
			<div class="form-group">
				<label for="UserUser" class="control-label">Type: <sup class="text-danger">*</sup> </label>
				<div class="form-control-skill">
					<?php echo $this->Form->input('Program.type_id', array('type' => 'select', 'options' => $types, 'label' => false, 'div' => false, 'class' => 'form-control',  'id' => 'program_types', 'empty' => 'Select Type' )); ?>
					<span class="error text-red"></span>
				</div>
			</div>
			<div class="form-group">
				<label for="UserUser" class="control-label">Stakeholders:</label>
				<div class="form-control-skill">
					<?php echo $this->Form->input('Program.projects', array('type' => 'select', 'options' => $users, 'label' => false, 'div' => false, 'class' => 'form-control', 'multiple' => 'multiple', 'id' => 'stakeholders' )); ?>
					<span class="error text-red"></span>
				</div>
			</div>
		</div>
		<div class="col-sm-12">
			<div class="form-group description-filed">
				<label for="UserUser" class="control-label">Description: </label>
				<div class="form-control-skill">
					<?php echo $this->Form->input('Program.description', array('type' => 'textarea', 'rows'=> 3, 'cols'=>35, 'label' => false, 'div' => false, 'class' => 'form-control description', 'maxlength'=>500, 'id' => 'prog_desc', 'placeholder' => '500 chars' )); ?>
					<span class="error text-red"></span>
				</div>
			</div>
		</div>
		<div class="col-sm-12">
			<div class="form-group outcome-filed">
				<label for="UserUser" class="control-label">Outcome: </label>
				<div class="form-control-skill">
					<?php echo $this->Form->input('Program.outcome', array('type' => 'textarea', 'rows'=> 3, 'cols'=>35, 'label' => false, 'div' => false, 'class' => 'form-control outcome', 'maxlength'=>500, 'id' => 'prog_outcome', 'placeholder' => '500 chars' )); ?>
					<span class="error text-red"></span>
				</div>
			</div>
		</div>
	</div>

</div>
<div class="modal-footer clearfix">
	<button type="button" class="btn btn-primary submit_data">Add</button>
	<button type="button" id="discard" class="btn cancel-add" data-dismiss="modal">Cancel</button>
</div>

<style>
</style>

<script>
$(function(){
	$('input[name="data[Program][name]"]').focus();

	$projects = $('#projects').multiselect({
            enableUserIcon: false,
            buttonClass: 'btn btn-default aqua',
            buttonWidth: '100%',
            buttonContainerWidth: '100%',
            numberDisplayed: 2,
            maxHeight: '318',
            checkboxName: 'projects[]',
            includeSelectAllOption: true,
            enableFiltering: true,
            filterPlaceholder: 'Search Projects',
            enableCaseInsensitiveFiltering: true,
            nonSelectedText: 'Select Projects',
            onSelectAll:function(){
            	var selected = $('#projects').val();
                // $.temp_data.main.locations = selected;
			},
			onDeselectAll:function(){
                // $.temp_data.main.locations = '';
			},
            onChange: function(element, checked) {
            	var selected = $('#projects').val();
                // $.temp_data.main.locations = selected;
            }
        });

	$stakeholders = $('#stakeholders').multiselect({
            enableUserIcon: false,
            buttonClass: 'btn btn-default aqua',
            buttonWidth: '100%',
            buttonContainerWidth: '100%',
            numberDisplayed: 2,
            maxHeight: '318',
            checkboxName: 'stakeholders[]',
            includeSelectAllOption: true,
            enableFiltering: true,
            filterPlaceholder: 'Search People',
            enableCaseInsensitiveFiltering: true,
            nonSelectedText: 'Select People',
            onSelectAll:function(){
            	var selected = $('#stakeholders').val();
                // $.temp_data.main.edomains = selected;
			},
			onDeselectAll:function(){
                // $.temp_data.main.edomains = '';
			},
            onChange: function(element, checked) {
            	var selected = $('#stakeholders').val();
                // $.temp_data.main.edomains = selected;
            }
        });



	$('.submit_data').off('click').on('click', function(event) {
		event.preventDefault();
		$('.error').text('');
		var error = false;

		if($.trim($("#program_name").val()) == ''){
			$("#program_name").parent().find('.error').text('Name is required');
			error = true;
		}
		if( $("#program_types").val() == ''){
			$("#program_types").parent().find('.error').text('Type is required');
			error = true;
		}

		if(error){
			return;
		}
		var data = {
			name: $.trim($("#program_name").val()),
			type_id: $("#program_types").val(),
			projects: $("#projects").val(),
			stakeholders: $("#stakeholders").val(),
			description: $.trim($("#prog_desc").val()),
			outcome: $.trim($("#prog_outcome").val()),
		};

		$(this).prop('disabled', true);

		$.ajax({
			url: $.module_url + 'add_program',
			type: 'POST',
			dataType: 'json',
			data: data,
			context: this,
			success: function(response){
				if(response.content){
					// SEND WEB NOTIFICATION
					$.socket.emit('socket:notification', response.content.socket, function(userdata){});
				}

				if(response.success){
					$(this).prop('disabled', false);
					$.program_save = true;
					$('#modal_program').modal('hide');
				}
			}
		})
	})

	/*$('.info-icon').tooltip({
		html: true,
		template: '<div class="tooltip" style="text-transform:none !important;"><div class="tooltip-arrow"></div><div class="tooltip-inner" style="text-transform:none !important;"> </div></div>',
		container: 'body',
		placement: "top"
	});*/
})

</script>