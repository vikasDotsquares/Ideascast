<?php
echo $this->Html->css('projects/bs-selectbox/bootstrap-multiselect');
echo $this->Html->script('projects/plugins/selectbox/bootstrap-multiselect', array('inline' => true));
?>
<?php
$all_task_types = [];
$project_task_type = $this->Permission->listing_task_types(false)[0][0]['types'];
$project_task_type = json_decode($project_task_type, true);

$listing_members = $this->Permission->listing_members(false);
$listing_members = json_decode($listing_members[0][0]['users'], true);

if(isset($programs) && !empty($programs)){
	$programs = Set::combine($programs, '{n}.prog.id', '{n}.prog.name');
	$programs = array_map(function($v){
		return htmlentities($v ,ENT_QUOTES, "UTF-8");
	}, $programs);
}
if(isset($projects) && !empty($projects)){
	$projects = Set::combine($projects, '{n}.p.id', '{n}.p.title');
	$projects = array_map(function($v){
		return htmlentities($v ,ENT_QUOTES, "UTF-8");
	}, $projects);
}
// pr($projects);
 ?>
<!-- POPUP MODEL BOX CONTENT HEADER -->
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h3 class="modal-title" id="myModalLabel">Filter Projects</h3>
	</div>

	<!-- POPUP MODAL BODY -->
	<div class="modal-body popup-select-icon popup-multselect-list clearfix">
		<?php  ?><div class="form-group">
			<?php echo $this->Form->input('Program.id', array('type' => 'select', 'options' => $programs, 'label' => false, 'div' => false, 'class' => 'form-control', 'multiple' => 'multiple', 'id' => 'programs' )); ?>
		</div>

		<div class="form-group">
			<?php echo $this->Form->input('Project.id', array('type' => 'select', 'options' => $projects, 'label' => false, 'div' => false, 'class' => 'form-control', 'multiple' => 'multiple', 'id' => 'projects' )); ?>
		</div><?php  ?>
		<div class="form-group">
			<select class="form-control" id="statuses" multiple="">
				<option value="5">Not Set</option>
				<option value="3">Not Started</option>
				<option value="2">In Progress</option>
				<option value="1">Overdue</option>
				<option value="4">Completed</option>
			</select>
		</div>
		<?php  ?><div class="form-group">
			<select class="form-control" id="rag" multiple="">
				<option value="red">Red</option>
				<option value="yellow">Amber</option>
				<option value="green-bg">Green</option>
			</select>
		</div><?php  ?>
		<div class="form-group">
			<select class="form-control" id="roles" multiple="">
				<option value="Creator">Creator</option>
				<option value="Owner">Owner</option>
				<option value="Sharer">Sharer</option>
			</select>
		</div>
		<div class="form-group">
			<select class="form-control" id="task_types" multiple="">
				<?php if(isset($project_task_type) && !empty($project_task_type)){ ?>
					<?php foreach ($project_task_type as $key => $value) { ?>
						<option value="<?php echo $value['id']; ?>"><?php echo $value['title']; ?></option>
					<?php } ?>
				<?php } ?>
			</select>
		</div>
		<div class="form-group mb0">
			<select class="form-control" id="team_members" multiple="">
				<?php if(isset($listing_members) && !empty($listing_members)){ ?>
					<?php foreach ($listing_members as $key => $value) { ?>
						<option value="<?php echo $value['id']; ?>"><?php echo $value['full_name']; ?></option>
					<?php } ?>
				<?php } ?>
			</select>
		</div>

		</div>
	<!-- POPUP MODAL FOOTER -->
	<div class="modal-footer">
		<button type="button"  class="btn btn-success submit-filter ftbtndisable">Filter</button>
		<button type="button"  class="btn btn-danger clear-fields">Clear</button>
		<button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
	</div>
<script type="text/javascript" >
	$(function(){


		if($.selected_filters.programs.length > 0){
			$('#programs').val($.selected_filters.programs);
			$('.submit-filter').removeClass('ftbtndisable');
		}
		if($.selected_filters.projects.length > 0){
			$('#projects').val($.selected_filters.projects);
			$('.submit-filter').removeClass('ftbtndisable');
		}
		if($.selected_filters.status.length > 0){
			$('#statuses').val($.selected_filters.status);
			$('.submit-filter').removeClass('ftbtndisable');
		}
		if($.selected_filters.roles.length > 0){
			$('#roles').val($.selected_filters.roles);
			$('.submit-filter').removeClass('ftbtndisable');
		}
		if($.selected_filters.rag.length > 0){
			$('#rag').val($.selected_filters.rag);
			$('.submit-filter').removeClass('ftbtndisable');
		}
		if($.selected_filters.task_types.length > 0){
			$('#task_types').val($.selected_filters.task_types);
			$('.submit-filter').removeClass('ftbtndisable');
		}
		if($.selected_filters.members.length > 0){
			$('#team_members').val($.selected_filters.members);
			$('.submit-filter').removeClass('ftbtndisable');
		}

		$('#programs, #projects, #statuses, #roles, #rag, #task_types, #team_members').off('change').on('change', function(event) {
			event.preventDefault();
			var programs = $('#programs').val() || [],
				projects = $('#projects').val() || [],
				status = $('#statuses').val() || [],
				rag = $('#rag').val() || [],
				roles = $('#roles').val() || [],
				task_types = $('#task_types').val() || [],
				members = $('#team_members').val() || [];

			if(programs.length <= 0 && projects.length <= 0 && status.length <= 0 && rag.length <= 0 && roles.length <= 0 && task_types.length <= 0 && members.length <= 0){
				$('.submit-filter').addClass('ftbtndisable');
			}
			else{
				$('.submit-filter').removeClass('ftbtndisable');
			}
		});

		$('.submit-filter').off('click').on('click', function(event) {
			event.preventDefault();
			var programs = $('#programs').val() || [],
				projects = $('#projects').val() || [],
				status = $('#statuses').val() || [],
				rag = $('#rag').val() || [],
				roles = $('#roles').val() || [],
				task_types = $('#task_types').val() || [],
				members = $('#team_members').val() || [];

			$.selected_filters = {
				programs: programs,
				projects: projects,
				status: status,
				rag: rag,
				roles: roles,
				task_types: task_types,
				members: members
			}
			if(programs.length > 0 || projects.length > 0 || status.length > 0 || rag.length > 0 || roles.length > 0 || task_types.length > 0 || members.length > 0){
				$.filterByType = true;
				$('#mid_model_box').modal('hide');
			}
		});

		$('.clear-fields').off('click').on('click', function(event) {
			event.preventDefault();
			$.selected_filters = {programs: [], projects: [], status: [], roles: [], rag: [], task_types: [], members: []};
			$('#programs').val([]).multiselect('refresh');
			$('#projects').val([]).multiselect('refresh');
			$('#statuses').val([]).multiselect('refresh');
			$('#rag').val([]).multiselect('refresh');
			$('#roles').val([]).multiselect('refresh');
			$('#task_types').val([]).multiselect('refresh');
			$('#team_members').val([]).multiselect('refresh');

			$('.analytic-btn[data-type="projects"] .filter-icon').removeClass('filterblue').addClass('filterblack');
			var data = {};
			$.show_filtered_data(data);
			$('#mid_model_box').modal('hide');
		});

		$programs = $('#programs').multiselect({
            enableUserIcon: false,
            buttonClass: 'btn btn-default aqua',
            buttonWidth: '100%',
            buttonContainerWidth: '100%',
            numberDisplayed: 2,
            maxHeight: '318',
            // checkboxName: 'dept[]',
            includeSelectAllOption: true,
            enableFiltering: true,
            filterPlaceholder: 'Search Programs',
            enableCaseInsensitiveFiltering: true,
            nonSelectedText: 'Select Programs'
        });

		$projects = $('#projects').multiselect({
            enableUserIcon: false,
            buttonClass: 'btn btn-default aqua',
            buttonWidth: '100%',
            buttonContainerWidth: '100%',
            numberDisplayed: 2,
            maxHeight: '318',
            // checkboxName: 'dept[]',
            includeSelectAllOption: true,
            enableFiltering: true,
            filterPlaceholder: 'Search Projects',
            enableCaseInsensitiveFiltering: true,
            nonSelectedText: 'Select Projects'
        });

		$rag = $('#rag').multiselect({
            enableUserIcon: false,
            buttonClass: 'btn btn-default aqua',
            buttonWidth: '100%',
            buttonContainerWidth: '100%',
            numberDisplayed: 2,
            maxHeight: '318',
            // checkboxName: 'dept[]',
            includeSelectAllOption: true,
            enableFiltering: true,
            filterPlaceholder: 'Search RAG Status',
            enableCaseInsensitiveFiltering: true,
            nonSelectedText: 'Select RAG Status'
        });

		$statuses = $('#statuses').multiselect({
            enableUserIcon: false,
            buttonClass: 'btn btn-default aqua',
            buttonWidth: '100%',
            buttonContainerWidth: '100%',
            numberDisplayed: 2,
            maxHeight: '318',
            // checkboxName: 'dept[]',
            includeSelectAllOption: true,
            enableFiltering: true,
            filterPlaceholder: 'Search Schedule Status',
            enableCaseInsensitiveFiltering: true,
            nonSelectedText: 'Select Schedule Status'
        });
		$roles = $('#roles').multiselect({
            enableUserIcon: false,
            buttonClass: 'btn btn-default aqua',
            buttonWidth: '100%',
            buttonContainerWidth: '100%',
            numberDisplayed: 2,
            maxHeight: '318',
            // checkboxName: 'dept[]',
            includeSelectAllOption: true,
            enableFiltering: true,
            filterPlaceholder: 'Search Roles',
            enableCaseInsensitiveFiltering: true,
            nonSelectedText: 'Select Roles'
        });
		$task_types = $('#task_types').multiselect({
            enableUserIcon: false,
            buttonClass: 'btn btn-default aqua',
            buttonWidth: '100%',
            buttonContainerWidth: '100%',
            numberDisplayed: 2,
            maxHeight: '318',
            // checkboxName: 'dept[]',
            includeSelectAllOption: true,
            enableFiltering: true,
            filterPlaceholder: 'Search Types',
            enableCaseInsensitiveFiltering: true,
            nonSelectedText: 'Select Types'
        });
		$team_members = $('#team_members').multiselect({
            enableUserIcon: false,
            buttonClass: 'btn btn-default aqua',
            buttonWidth: '100%',
            buttonContainerWidth: '100%',
            numberDisplayed: 2,
            maxHeight: '318',
            // checkboxName: 'dept[]',
            includeSelectAllOption: true,
            enableFiltering: true,
            filterPlaceholder: 'Search Team Members',
            enableCaseInsensitiveFiltering: true,
            nonSelectedText: 'Select Team Members'
        });
	})
</script>
