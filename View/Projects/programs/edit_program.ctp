<?php
echo $this->Html->css('projects/bs-selectbox/bootstrap-multiselect');
echo $this->Html->script('projects/plugins/selectbox/bootstrap-multiselect', array('inline' => true));
if(isset($projects) && !empty($projects)){
	$projects = array_map(function($v){
		return  htmlentities($v  ,ENT_QUOTES, "UTF-8");
	}, $projects);
}
$program_id = $progs[0]['progs']['id'];

$progName = html_entity_decode(html_entity_decode($progs[0]['progs']['name'] ,ENT_QUOTES, "UTF-8"));
$progDesc = html_entity_decode(html_entity_decode($progs[0]['progs']['description'] ,ENT_QUOTES, "UTF-8"));
$progOutcome = html_entity_decode(html_entity_decode($progs[0]['progs']['outcome'] ,ENT_QUOTES, "UTF-8"));
$typeId = $progs[0]['progs']['type_id'];

$sel_projects = (isset($progs[0]['prjs']['cp']) && !empty($progs[0]['prjs']['cp'])) ? explode(",", $progs[0]['prjs']['all_projects']) : [];
$sel_users = (isset($progs[0]['pusr']['cu']) && !empty($progs[0]['pusr']['cu'])) ? explode(",", $progs[0]['pusr']['all_users']) : [];
?>
<div class="modal-header">
	<button type="button" class="close close-prog" data-dismiss="modal" aria-hidden="true">&times;</button>
	<h4 class="modal-title">Edit Program</h4>
</div><div class="modal-body popup-select-icon">
	<div class="row">
		<?php echo $this->Form->hidden('Program.id', ['value' => $program_id, 'id' => 'program_id']); ?>
		<!-- LEFT -->
		<div class="col-sm-6">
			<div class="form-group">
				<label for="UserUser" class="control-label">Name: <sup class="text-danger">*</sup></label>
				<div class="form-control-skill">
					<?php echo $this->Form->input('Program.name', array('type' => 'text', 'label' => false, 'div' => false, 'class' => 'form-control', 'maxlength'=>100, 'autocomplete'=>'off', 'id'=>'program_name', 'placeholder' => '100 chars', 'value' => $progName)); ?>

					<span class="error text-red"></span>
				</div>
			</div>
			<div class="form-group">
				<label for="UserUser" class="control-label">Projects:</label>
				<div class="form-control-skill">
					<?php echo $this->Form->input('Program.projects', array('type' => 'select', 'options' => $projects, 'label' => false, 'div' => false, 'class' => 'form-control', 'multiple' => 'multiple', 'id' => 'projects', 'default' => $sel_projects )); ?>
					<span class="error text-red"></span>
				</div>
			</div>
		</div>
		<!-- RIGHT -->
		<div class="col-sm-6">
			<div class="form-group">
				<label for="UserUser" class="control-label">Type: <sup class="text-danger">*</sup> </label>
				<div class="form-control-skill">
					<?php echo $this->Form->input('Program.type_id', array('type' => 'select', 'options' => $types, 'label' => false, 'div' => false, 'class' => 'form-control',  'id' => 'program_types', 'empty' => 'Select Type', 'default' => $typeId )); ?>
					<span class="error text-red"></span>
				</div>
			</div>
			<div class="form-group">
				<label for="UserUser" class="control-label">Stakeholders:</label>
				<div class="form-control-skill">
					<?php echo $this->Form->input('Program.projects', array('type' => 'select', 'options' => $users, 'label' => false, 'div' => false, 'class' => 'form-control', 'multiple' => 'multiple', 'id' => 'stakeholders', 'default' => $sel_users )); ?>
					<span class="error text-red"></span>
				</div>
			</div>
		</div>
		<div class="col-sm-12">
			<div class="form-group description-filed">
				<label for="UserUser" class="control-label">Description: </label>
				<div class="form-control-skill">
					<?php echo $this->Form->input('Program.description', array('type' => 'textarea', 'rows'=> 3, 'cols'=>35, 'label' => false, 'div' => false, 'class' => 'form-control description', 'maxlength'=>500, 'id' => 'prog_desc', 'placeholder' => '500 chars', 'value' => $progDesc )); ?>
					<span class="error text-red"></span>
				</div>
			</div>
		</div>
		<div class="col-sm-12">
			<div class="form-group outcome-filed">
				<label for="UserUser" class="control-label">Outcome: </label>
				<div class="form-control-skill">
					<?php echo $this->Form->input('Program.outcome', array('type' => 'textarea', 'rows'=> 3, 'cols'=>35, 'label' => false, 'div' => false, 'class' => 'form-control outcome', 'maxlength'=>500, 'id' => 'prog_outcome', 'placeholder' => '500 chars', 'value' => $progOutcome )); ?>
					<span class="error text-red"></span>
				</div>
			</div>
		</div>
	</div>

</div>
<div class="modal-footer clearfix">
	<button type="button" class="btn btn-primary submit_data">Save</button>
	<button type="button" id="discard" class="btn cancel-add" data-dismiss="modal">Close</button>
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
			id: $("#program_id").val(),
			name: $.trim($("#program_name").val()),
			type_id: $("#program_types").val(),
			projects: $("#projects").val(),
			stakeholders: $("#stakeholders").val(),
			description: $.trim($("#prog_desc").val()),
			outcome: $.trim($("#prog_outcome").val()),
		};

		// $(this).prop('disabled', true);

		$.ajax({
			url: $.module_url + 'edit_program',
			type: 'POST',
			dataType: 'json',
			data: data,
			context: this,
			success: function(response){
				if(response.success){
					if(response.content){
						// SEND WEB NOTIFICATION
						$.socket.emit('socket:notification', response.content.socket, function(userdata){});
					}
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