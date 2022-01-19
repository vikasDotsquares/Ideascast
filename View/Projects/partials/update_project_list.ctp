
			<div class="col-md-12" style="padding-top: 10px;">
				<label class="">Update Projects To Program:<br></label>
			</div>
			<div class="col-md-12">
				<?php 
					echo $this->Form->input('ProjectProgram.project_id_update', array(
						'options' => $allprojects,
						'type' => 'select',
						'multiple' => 'multiple',
						'selected' => $selectedProjectProgram,
						'label' => false,
						'div' => false,
						'class' => 'form-control aqua clear',
						'id'=>'multiple_program_project_update'
					));
				?>
				<span class="error-message-pid text-danger" ></span>
			</div>

<script>
$('#multiple_program_project_update').hide()
	setTimeout(function () {

		$('#multiple_program_project_update').multiselect({
			enableFiltering: true,
			buttonClass	: 'btn btn-default btn-sm',
			includeSelectAllOption: true,
			buttonWidth: '100%',
			checkboxName: 'data[ProjectProgram][project_id_update][]',
		});

		$('.span_profile').hide()
	}, 1);
</script>