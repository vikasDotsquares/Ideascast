<style>
.checkbox-row {
	margin: 0 0 10px;
}
#message_box {
	font-size: 12px; text-align: center; margin: 0px 0px 10px; background: rgb(95, 147, 35) none repeat scroll 0% 0%; padding: 10px 5px; color: #ffffff; font-weight: 600; display: none;
}

.set-start-f .btn {
	min-width: 70px;
}

</style>

<?php echo $this->Html->script('projects/plugins/bootstrap-checkbox', array('inline' => true)); ?>
<?php echo $this->Html->css('projects/bootstrap-input') ?>
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<h3 class="modal-title" id="myModalLabel" style="display: inline"> Start Page </h3>
	</div>

	<div class="modal-body">
		<h3 class="" id="message_box" ></h3>
		<?php
			echo $this->Form->create('setStartPage', array('url' => ['controller' => 'settings', 'action' => 'start_page'], 'class' => 'form-horizontal', 'id' => 'frmStartPage'));
		?>
			<div class="clearfix col-sm-12 col-md-12 col-lg-12 ">

				<!--<div class="form-group clearfix">
					<label class="" for="page_setting_toggle">Set Page Setting:</label>
					<input type="checkbox" value="1" class="page_on_off tipText" name="page_setting_toggle" id="page_setting_toggle" checked="checked">
				</div>-->
				<input type="hidden" value="1" class="page_on_offs tipText" name="page_setting_toggle" id="page_setting_toggle" checked="checked">
			</div>
			<div class="clearfix">
                <p>Select the page you want to display when you sign in.</p>
				<div class=" popup-start-page-select">

						<label for="page_project_center" >Start Page</label>
						<select name="landing_url" class="form-control">
							<?php /* ?><option value="users/projects">Assets</option><?php */ ?>
							<option value="analytics/knowledge">Capability Analytics</option>
							<option value="communities/index">Community</option>
							<option value="competencies/index">Competencies</option>
							<?php /* ?><option value="costs/index" >Cost Center</option><?php */ ?>
							<option value="studios/index">Design Board</option>
							<option value="templates/create_workspace/0">Knowledge Library</option>
							<?php /* <option value="team_talks/index">My Blogs</option> */?>
							<option value="shares/my_groups">My Groups</option>
							<option  value="boards/nudge_list">My Nudges</option>
							<option  value="dashboards/program_center">My Programs</option>
							<option  value="risks/index/0/0/1">My Risks</option>
							<option value="shares/my_sharing#user_view">My Sharing</option>
							<option value="tags/my_tags">My Tags</option>
							<option value="dashboards/task_centers/status:8/assigned:<?php echo $this->Session->read('Auth.User.id'); ?>">My Tasks</option>
							<option value="todos/index" >My To-dos</option>
							<option  value="projects/lists">My Work</option>
							<option value="boards/opportunity" >Opportunities</option>
							<?php /* ?><option value="dashboards/project_center">Project Center</option><?php */ ?>
							<option value="rewards/index">Reward Center</option>
							<option value="risks/index">Risk Center</option>
							<option value="analytics/social">Social Analytics</option>
							<option value="stories/index">Stories</option>
							<option value="dashboards/task_center">Task Center</option>
						</select>
					</div>

				<?php /* ?>

				<div class="clearfix col-sm-12 col-md-12 col-lg-12 checkbox-row">

					<div class="radio radio-warning col-sm-4">
						<input type="radio" value="dashboards/project_center" class="fancy_input" name="landing_url" id="page_project_center" >
						<label for="page_project_center" class="fancy_labels">Project Center</label>
					</div>
					<div class="radio radio-warning col-sm-4">
						<input type="radio"  value="costs/index" class="fancy_input" name="landing_url" id="page_costs">
						<label for="page_costs" class="fancy_labels">Cost Center</label>
					</div>
					<div class="radio radio-warning col-sm-4">
						<input type="radio"  value="team_talks/index" class="fancy_input" name="landing_url" id="page_team_talk">
						<label for="page_team_talk" class="fancy_labels">Info Center</label>
					</div>



				</div>

				<div class="clearfix col-sm-12 col-md-12 col-lg-12 checkbox-row">
					<div class="radio radio-warning col-sm-4">
						<input type="radio" value="projects/objectives" class="fancy_input" name="landing_url" id="page_dashboard">
						<label for="page_dashboard" class="fancy_labels">Status Center</label>
					</div>
					<div class="radio radio-warning col-sm-4">
						<input type="radio"  value="studios/index" class="fancy_input" name="landing_url" id="page_studio">
						<label for="page_studio" class="fancy_labels">Design Center</label>
					</div>

					<div class="radio radio-warning col-sm-4">
						<input type="radio" value="risks/index" class="fancy_input" name="landing_url" id="page_risk_center" >
						<label for="page_risk_center" class="fancy_labels">Risk Center</label>
					</div>

				</div>

				<div class="clearfix col-sm-12 col-md-12 col-lg-12 checkbox-row">
					<div class="radio radio-warning col-sm-4">
						<input type="radio" value="todos/index" class="fancy_input" name="landing_url" id="page_todo">
						<label for="page_todo" class="fancy_labels">To-dos</label>
					</div>
					<div class="radio radio-warning col-sm-4">
						<input type="radio" value="dashboards/program_center" class="fancy_input" name="landing_url" id="page_program_center" >
						<label for="page_program_center" class="fancy_labels">My Programs</label>
					</div>

					<div class="radio radio-warning col-sm-4">
						<input type="radio"  value="boards/index" class="fancy_input" name="landing_url" id="page_boards">
						<label for="page_boards" class="fancy_labels">Opportunities</label>
					</div>


				</div>

				<div class="clearfix col-sm-12 col-md-12 col-lg-12 checkbox-row">
					<div class="radio radio-warning col-sm-4">
						<input type="radio" value="dashboards/task_center" class="fancy_input" name="landing_url" id="page_taskcenter">
						<label for="page_taskcenter" class="fancy_labels">Task Center</label>
					</div>
					<div class="radio radio-warning col-sm-4">
						<input type="radio" value="rewards/index" class="fancy_input" name="landing_url" id="page_rewards" >
						<label for="page_rewards" class="fancy_labels">Reward Center</label>
					</div>

					<div class="radio radio-warning col-sm-4">
						<input type="radio" value="projects/lists" class="fancy_input" name="landing_url" id="page_projects" checked="checked">
						<label for="page_projects" class="fancy_labels">My Projects</label>
					</div>

				</div>
				<?php */  ?>
				<!--<div class="clearfix col-sm-12 col-md-12 col-lg-12 checkbox-row">
					<div class="radio radio-warning col-sm-4">
						<input type="radio" value="work_centers/index" class="fancy_input" name="landing_url" id="page_work_center" >
						<label for="page_work_center" class="fancy_labels">Work Center</label>
					</div>
				</div>-->


			</div>
		<?php  echo $this->Form->end(); ?>

	</div>

	<div class="modal-footer set-start-f">
		<button class="btn btn-success" id="save_page" >Set</button>
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
			$('input.fancy_input').attr('disabled', false);
		}
		else {
		//	$('input.fancy_input').attr('disabled', true);
		}
	})

	<?php if(isset($userData) && !empty($userData)) { ?>
		<?php if(isset($userData['page_setting_toggle']) && empty($userData['page_setting_toggle'])) { ?>
			$('.page_on_off').prop('checked', false);
		<?php } ?>


		<?php if(isset($userData['landing_url']) && !empty($userData['landing_url'])) { ?>
			$('input[value="<?php echo $userData['landing_url']; ?>"]').prop('checked', true)
			$('option[value="<?php echo $userData['landing_url']; ?>"]').prop('selected', true)
		<?php }else{ ?>
		$('option[value="projets/lists"]').prop('selected', true)
		<?php }  ?>
	<?php } ?>

	$('#popup_modal').on('hidden.bs.modal', function(){
		$(this).removeData('bs.modal')
		$(this).find('.modal-content').html('')
	})

})

</script>