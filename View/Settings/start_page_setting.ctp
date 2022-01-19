<?php // pr($projects); ?>
<style>
.checkbox-row {
	margin: 0 0 10px;
}
#message_box {
	font-size: 12px; text-align: center; margin: 0px 0px 10px; background: rgb(95, 147, 35) none repeat scroll 0% 0%; padding: 10px 5px; color: #ffffff; font-weight: 600; display: none;
}
.set-rows {
	border-bottom: 1px solid rgb(204, 204, 204);
	margin-bottom: 10px;
	padding-bottom: 10px;
}
.set-rows:last-child {
	border-bottom: medium none;
}
</style>
<?php echo $this->Html->script('projects/plugins/bootstrap-checkbox', array('inline' => true)); ?>
<?php echo $this->Html->css('projects/bootstrap-input') ?>
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h3 class="modal-title" id="myModalLabel" style="display: inline"> Set Start Page </h3>
	</div>

	<div class="modal-body">
		<h3 class="" id="message_box" ></h3>
		<?php
			echo $this->Form->create('setStartPage', array('url' => ['controller' => 'settings', 'action' => 'start_page'], 'class' => 'form-horizontal', 'id' => 'frmStartPage'));
		?>
			<div class="row set-rows">
				<div class="col-sm-12 clearfix">
					<label class="" for="page_setting_toggle">Set Page Setting:</label>
					<input type="checkbox" value="1" class="page_on_off tipText" name="page_setting_toggle" id="page_setting_toggle" checked="checked">
				</div>
			</div>
			
			<div class="row set-rows">
				<div class="clearfix col-sm-12 checkbox-row">
					<div class="radio radio-warning col-sm-4">
						<input type="radio" value="dashboards/project_center" class="fancy_input landing_parent" name="landing_parent" id="page_project_center" checked="checked">
						<label for="page_project_center" class="fancy_labels">Project Center</label>
					</div>
					<div class="radio radio-warning col-sm-4">
						<input type="radio"  value="projects/lists" class="fancy_input landing_parent" name="landing_parent" id="page_my_projects">
						<label for="page_my_projects" class="fancy_labels">My Projects</label>
					</div>
					<div class="radio radio-warning col-sm-4">
						<input type="radio"  value="projects/share_lists" class="fancy_input landing_parent" name="landing_parent" id="page_received">
						<label for="page_received" class="fancy_labels">Received Projects</label>
					</div>
				</div>
			</div>
			
			<div class="row set-rows">

				<div class="col-sm-12 clearfix">
					<label style="width: 100%; margin-top: 3px;" class="custom-dropdown">
						<select id="landing_id" name="landing_id" class="aqua landing_child">
							<option value="" selected="selected">Select Project</option>
							<option value="1" >Project 1</option>
							<option value="2" >Project 2</option>
						</select>
					</label>
				</div>

				<div class="clearfix col-sm-12 col-md-12 col-lg-12 checkbox-row">
					<div class="radio radio-warning col-sm-4">
						<input type="radio" value="projects/objectives" class="fancy_input landing_child" name="landing_url" id="page_dashboard" disabled>
						<label for="page_dashboard" class="fancy_labels">Dashboard</label>
					</div>
					<div class="radio radio-warning col-sm-4">
						<input type="radio"  value="studios/index" class="fancy_input landing_child" name="landing_url" id="page_studio" disabled>
						<label for="page_studio" class="fancy_labels">Studio</label>
					</div>
					<div class="radio radio-warning col-sm-4">
						<input type="radio"  value="team_talks/index" class="fancy_input landing_child" name="landing_url" id="page_team_talk" disabled>
						<label for="page_team_talk" class="fancy_labels">Team Talk</label>
					</div>
				</div>

				<div class="clearfix col-sm-12 col-md-12 col-lg-12 checkbox-row">
					<div class="radio radio-warning col-sm-4">
						<input type="radio" value="todos/index" class="fancy_input landing_child" name="landing_url" id="page_gantt" disabled>
						<label for="page_todo" class="fancy_labels">Gantt</label>
					</div>
					<div class="radio radio-warning col-sm-4">
						<input type="radio" value="boards/index" class="fancy_input landing_child" name="landing_url" id="page_project" disabled>
						<label for="page_board" class="fancy_labels">Project</label>
					</div>
					<div class="radio radio-warning col-sm-4">
						<input type="radio"  value="entities/task_list" class="fancy_input landing_child" name="landing_url" id="page_task_list" disabled>
						<label for="page_task_list" class="fancy_labels">Task Lists</label>
					</div>
				</div>

				<div class="clearfix col-sm-12 col-md-12 col-lg-12 checkbox-row">
					
					<div class="radio radio-warning col-sm-4">
						<input type="radio"  value="entities/task_list" class="fancy_input landing_child" name="landing_url" id="page_project_report" disabled>
						<label for="page_task_list" class="fancy_labels">Project Report</label>
					</div>
				</div>

			</div>
		<?php  echo $this->Form->end(); ?>

	</div>

	<div class="modal-footer">
		<button class="btn btn-success" id="save_page" >Save</button>
		<button class="btn btn-danger" id="close_modal" data-dismiss="modal">Cancel</button>
	</div>

<script type="text/javascript" >
$(function(){
	$(".page_on_off").checkboxpicker({
		style: true,
		defaultClass: 'tick_default',
		disabledCursor: 'not-allowed',
		offClass: 'tick_off',
		onClass: 'tick_on',
		offTitle: "Off",
		onTitle: "On",
		offLabel: 'Off',
		onLabel: 'On',
	})

	$('body').delegate('.page_on_off', 'change', function(event) {
		event.preventDefault();

		if( $(this).prop('checked') ) {
				
				$('input.landing_parent').attr('disabled', false);
				$('select.aqua').attr('disabled', false);
				
				if( $('#landing_id').val() != '' ) {
						$('input.landing_child').attr('disabled', false); 
				}
				else {
						$('input.landing_child').attr('disabled', true); 
				}
		}
		else {
				$('input.landing_parent,input.landing_child').attr('disabled', true);
				$('select.aqua').attr('disabled', true); 
		}
	})
	
	$('body').delegate('#landing_id', 'change', function(event) {
		event.preventDefault();

		if( $(this).val() != '' ) {
			$('input.landing_child').attr('disabled', false); 
		}
		else {
			$('input.landing_child').attr('disabled', true); 
		}
	})


	$('#popup_modal').on('hidden.bs.modal', function(){
		$(this).removeData('bs.modal')
		$(this).find('.modal-content').html('')
	})

})

</script>