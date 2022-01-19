<?php
echo $this->Html->css('projects/bs-selectbox/bootstrap-multiselect');
echo $this->Html->script('projects/plugins/selectbox/bootstrap-multiselect', array('inline' => true));
?>
<?php
$workspaceUsers = $this->Permission->workspaceUsers($project_id, $workspace_id);
if(isset($workspaceUsers) && !empty($workspaceUsers)){
	$workspaceUsers = Set::combine($workspaceUsers, '{n}.user_details.user_id', '{n}.0.fullname');
}
asort($workspaceUsers);
$all_types = [];
$project_task_type = $this->ViewModel->project_task_type($project_id, $workspace_id);
if(isset($project_task_type) && !empty($project_task_type)){
	$all_types = Set::combine($project_task_type, '{n}.project_element_types.id', '{n}.project_element_types.title');
}
asort($all_types);

 ?>
<!-- POPUP MODEL BOX CONTENT HEADER -->
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h3 class="modal-title" id="myModalLabel">Filter Tasks</h3>
	</div>

	<!-- POPUP MODAL BODY -->
	<div class="modal-body popup-select-icon popup-multselect-list clearfix">
		<div class="form-group">
			<select class="form-control" id="statuses" multiple="">
				<option value="not_spacified">Not Set</option>
				<option value="not_started">Not Started</option>
				<option value="progress">In Progress</option>
				<option value="overdue">Overdue</option>
				<option value="completed">Completed</option>
			</select>
		</div>
		<div class="form-group">
			<select class="form-control" id="task_types" multiple="">
			<?php if( isset($all_types) && !empty($all_types) ){
					foreach( $all_types as $key => $type ){
			?>
				<option value="<?php echo $key; ?>"><?php echo htmlentities($type,ENT_QUOTES, "UTF-8"); ?></option>
				<?php } ?>
			<?php } ?>
			</select>
		</div>

		<?php  ?><div class="form-group mb0">
			<select class="form-control" id="assigned_user" multiple="">
			<?php if( isset($workspaceUsers) && !empty($workspaceUsers) ){
					foreach( $workspaceUsers as $key => $user ){
			?>
				<option value="<?php echo $key; ?>"><?php echo htmlentities($user,ENT_QUOTES, "UTF-8"); ?></option>
				<?php } ?>
			<?php } ?>
			</select>
		</div><?php  ?>

		</div>
	<!-- POPUP MODAL FOOTER -->
	<div class="modal-footer">
		<button type="button"  class="btn btn-success submit-filter ftbtndisable">Filter</button>
		<button type="button"  class="btn btn-danger clear-fields">Clear</button>
		<button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
	</div>
<script type="text/javascript" >
	$(function(){

		if($.selected_filters.status.length > 0){
			$('#statuses').val($.selected_filters.status);
			$('.submit-filter').removeClass('ftbtndisable');
		}
		if($.selected_filters.type.length > 0){
			$('#task_types').val($.selected_filters.type);
			$('.submit-filter').removeClass('ftbtndisable');
		}
		if($.selected_filters.assign.length > 0){
			$('#assigned_user').val($.selected_filters.assign);
			$('.submit-filter').removeClass('ftbtndisable');
		}

		$('#statuses, #task_types, #assigned_user').off('change').on('change', function(event) {
			event.preventDefault();
			var status = $('#statuses').val() || [],
				type = $('#task_types').val() || [],
				assign = $('#assigned_user').val() || [];

			if(status.length <= 0 && type.length <= 0 && assign.length <= 0){
				$('.submit-filter').addClass('ftbtndisable');
			}
			else{
				$('.submit-filter').removeClass('ftbtndisable');
			}
		});

		$('.submit-filter').off('click').on('click', function(event) {
			event.preventDefault();
			var status = $('#statuses').val() || [],
				type = $('#task_types').val() || [],
				assign = $('#assigned_user').val() || [];

			$.selected_filters = {
				status: status,
				type: type,
				assign: assign
			}
			if(status.length > 0 || type.length > 0 || assign.length > 0){
				$.filterByType = true;
				$('#mid_model_box').modal('hide');
			}
		});

		$('.clear-fields').off('click').on('click', function(event) {
			event.preventDefault();
			$.selected_filters = {
				status: [],
				type: [],
				assign: []
			}
			$('#statuses').val([]).multiselect('refresh');
			$('#task_types').val([]).multiselect('refresh');
			$('#assigned_user').val([]).multiselect('refresh');
			$('.submit-filter').addClass('disabled');
			$('.filter-icon').removeClass('filterblue').addClass('filterblack');
			var data = {
					project_id: $js_config.project_id,
					workspace_id: $js_config.workspace_id,
					// project_task_type: [],
					task_type: 'project_type',
					// status: [],
					generalflag: 0
				};
			$.show_filtered_data(data);
			$.getWspTaskActivities();
			$('#mid_model_box').modal('hide');
		});

		$statuses = $('#statuses').multiselect({
            enableUserIcon: false,
            buttonClass: 'btn btn-default aqua',
            buttonWidth: '100%',
            buttonContainerWidth: '100%',
            numberDisplayed: 2,
            maxHeight: '318',
            checkboxName: 'dept[]',
            includeSelectAllOption: true,
            enableFiltering: true,
            filterPlaceholder: 'Search Status',
            enableCaseInsensitiveFiltering: true,
            nonSelectedText: 'Select Schedule Status'
        });
		$task_types = $('#task_types').multiselect({
            enableUserIcon: false,
            buttonClass: 'btn btn-default aqua',
            buttonWidth: '100%',
            buttonContainerWidth: '100%',
            numberDisplayed: 2,
            maxHeight: '318',
            checkboxName: 'dept[]',
            includeSelectAllOption: true,
            enableFiltering: true,
            filterPlaceholder: 'Search Types',
            enableCaseInsensitiveFiltering: true,
            nonSelectedText: 'Select Types'
        });
		$assigned_user = $('#assigned_user').multiselect({
            enableUserIcon: false,
            buttonClass: 'btn btn-default aqua',
            buttonWidth: '100%',
            buttonContainerWidth: '100%',
            numberDisplayed: 2,
            maxHeight: '318',
            checkboxName: 'dept[]',
            includeSelectAllOption: true,
            enableFiltering: true,
            filterPlaceholder: 'Search User',
            enableCaseInsensitiveFiltering: true,
            nonSelectedText: 'Select Assigned To'
        });
	})
</script>
