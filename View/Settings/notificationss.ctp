<style>
.checkbox-row {
	margin: 0 0 10px;
}
#message_box {
	font-size: 12px; 
	text-align: center; 
	margin: 0px 0px 10px; 
	background: rgb(95, 147, 35) none repeat scroll 0% 0%; 
	padding: 10px 5px; 
	color: #ffffff; 
	font-weight: 600; 
	display: none;
}
.notification_wrapper {
	clear: both;
	display: block;
	min-height: 450px;
	overflow-x: hidden;
	overflow-y: auto;
}
.isd {
	max-width: 100px;
}
</style>

<?php echo $this->Html->script('projects/plugins/bootstrap-checkbox', array('inline' => true)); ?>
<?php echo $this->Html->css('projects/bootstrap-input') ?>
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<h3 class="modal-title" id="myModalLabel" style="display: inline"> Notifications </h3>
	</div>

	<div class="modal-body">
		<h3 class="" id="message_box" ></h3>
		<?php
			echo $this->Form->create('setStartPage', array('url' => ['controller' => 'settings', 'action' => 'start_page'], 'class' => 'form-horizontal', 'id' => 'frmStartPage'));
		?>
			<div class="clearfix col-sm-6 col-md-6 col-lg-6 ">
				
				<div class="form-group clearfix">  
					<label class="" for="page_setting_toggle">Notifications Setting:</label>
					<input type="checkbox" value="1" class="page_on_off tipText" name="page_setting_toggle" id="page_setting_toggle" checked="checked">					
				</div>
			</div>
			<div class="clearfix col-sm-6 col-md-6 col-lg-6 ">
				
				<div class="form-group clearfix">  
					<label class="" for="page_setting_toggle">Mob/Cell:</label>
					<input type="text" value="" class="form-control isd" name="" id="">					
				</div>
			</div>
			<div class="notification_wrapper">

				<div class="clearfix col-sm-12 col-md-12 col-lg-12 checkbox-row">
					<div class="radio radio-warning col-sm-4">
						<input type="radio" value="dashboards/project_center" class="fancy_input" name="landing_url" id="page_project_center" checked="checked">
						<label for="page_project_center" class="fancy_labels">Project Center</label>
					</div>
					<div class="radio radio-warning col-sm-4">
						<input type="radio"  value="projects/lists" class="fancy_input" name="landing_url" id="page_my_projects">
						<label for="page_my_projects" class="fancy_labels">My Projects</label>
					</div>
					<div class="radio radio-warning col-sm-4">
						<input type="radio"  value="projects/share_lists" class="fancy_input" name="landing_url" id="page_received">
						<label for="page_received" class="fancy_labels">Received Projects</label>
					</div>
				</div>

				<div class="clearfix col-sm-12 col-md-12 col-lg-12 checkbox-row">
					<div class="radio radio-warning col-sm-4">
						<input type="radio" value="projects/objectives" class="fancy_input" name="landing_url" id="page_dashboard">
						<label for="page_dashboard" class="fancy_labels">Dashboards</label>
					</div>
					<div class="radio radio-warning col-sm-4">
						<input type="radio"  value="studios/index" class="fancy_input" name="landing_url" id="page_studio">
						<label for="page_studio" class="fancy_labels">Studio</label>
					</div>
					<div class="radio radio-warning col-sm-4">
						<input type="radio"  value="team_talks/index" class="fancy_input" name="landing_url" id="page_team_talk">
						<label for="page_team_talk" class="fancy_labels">Team Talk</label>
					</div>
				</div>

				<div class="clearfix col-sm-12 col-md-12 col-lg-12 checkbox-row">
					<div class="radio radio-warning col-sm-4">
						<input type="radio" value="todos/index" class="fancy_input" name="landing_url" id="page_todo">
						<label for="page_todo" class="fancy_labels">To-dos</label>
					</div>
					<div class="radio radio-warning col-sm-4">
						<input type="radio" value="boards/index" class="fancy_input" name="landing_url" id="page_board">
						<label for="page_board" class="fancy_labels">Project Board</label>
					</div>
					<div class="radio radio-warning col-sm-4">
						<input type="radio"  value="entities/task_list" class="fancy_input" name="landing_url" id="page_task_list">
						<label for="page_task_list" class="fancy_labels">Task Lists</label>
					</div>
				</div>

			</div>
		<?php  echo $this->Form->end(); ?>

	</div>

	<div class="modal-footer">
		<button class="btn btn-success" id="save_page" >Save</button>
		<button class="btn btn-danger" id="close_modal" data-dismiss="modal">Close</button>
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
			$('input.fancy_input').attr('disabled', false);
		}
		else {
			$('input.fancy_input').attr('disabled', true); 
		}
	})
	
	<?php if(isset($userData) && !empty($userData)) { ?>
		<?php if(isset($userData['page_setting_toggle']) && empty($userData['page_setting_toggle'])) { ?>
			$('.page_on_off').prop('checked', false);
		<?php } ?>
	
		
		<?php if(isset($userData['landing_url']) && !empty($userData['landing_url'])) { ?>
			$('input[value="<?php echo $userData['landing_url']; ?>"]').prop('checked', true)
		<?php } ?>
	<?php } ?>
	
	$('#popup_modal').on('hidden.bs.modal', function(){
		$(this).removeData('bs.modal')
		$(this).find('.modal-content').html('')
	})
		
})

</script>