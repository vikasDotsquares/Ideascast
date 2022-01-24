<?php
echo $this->Html->css('projects/bs-selectbox/bootstrap-multiselect');
echo $this->Html->script('projects/plugins/selectbox/bootstrap-multiselect', array('inline' => true));
?>
<?php

$all_types = [];
$program_types = $this->Scratch->program_selected_types();
// pr($program_types);
if(isset($program_types) && !empty($program_types)){
	$program_types = Set::combine($program_types, '{n}.pt.id', '{n}.pt.type');
	$program_types = array_map(function($v){
		return htmlentities($v ,ENT_QUOTES, "UTF-8");
	}, $program_types);
}

// pr($program_types);
 ?>
<!-- POPUP MODEL BOX CONTENT HEADER -->
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h3 class="modal-title" id="myModalLabel">Filter Programs</h3>
	</div>

	<!-- POPUP MODAL BODY -->
	<div class="modal-body popup-select-icon popup-multselect-list clearfix">

		<div class="form-group">
			<select class="form-control" id="roles" multiple="">
				<option value="creator">Creator</option>
				<option value="stakeholder">Stakeholder</option>
			</select>
		</div>
		<div class="form-group">
			<select class="form-control" id="status" multiple="">
				<option value="5">Not Set</option>
				<option value="3">Not Started</option>
				<option value="2">In Progress</option>
				<option value="1">Overdue</option>
				<option value="4">Completed</option>
			</select>
		</div>
		<div class="form-group">
			<?php echo $this->Form->input('ProgramType.id', array('type' => 'select', 'options' => $program_types, 'label' => false, 'div' => false, 'class' => 'form-control', 'multiple' => 'multiple', 'id' => 'types' )); ?>
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
		if($.program_filters.roles.length > 0){
			$('#roles').val($.program_filters.roles);
			$('.submit-filter').removeClass('ftbtndisable');
		}
		if($.program_filters.status.length > 0){
			$('#status').val($.program_filters.status);
			$('.submit-filter').removeClass('ftbtndisable');
		}
		if($.program_filters.types.length > 0){
			$('#types').val($.program_filters.types);
			$('.submit-filter').removeClass('ftbtndisable');
		}

		$('#roles, #status, #types').off('change').on('change', function(event) {
			event.preventDefault();
			var roles = $('#roles').val() || [],
				status = $('#status').val() || [],
				types = $('#types').val() || [];

			if(roles.length <= 0 && status.length <= 0 && types.length <= 0){
				$('.submit-filter').addClass('ftbtndisable');
			}
			else{
				$('.submit-filter').removeClass('ftbtndisable');
			}
		});

		$('.submit-filter').off('click').on('click', function(event) {
			event.preventDefault();
			var roles = $('#roles').val() || [],
				status = $('#status').val() || [],
				types = $('#types').val() || [];

			$.program_filters = {
				roles: roles,
				status: status,
				types: types
			}
			if(status.length > 0 || roles.length > 0 || types.length > 0){
				$.program_filtered = true;
				$('#prog_filter_model_box').modal('hide');
			}
		});

		$('.clear-fields').off('click').on('click', function(event) {
			event.preventDefault();
			$.program_filters = { roles: [], status: [], types: [] };
			$('#roles').val([]).multiselect('refresh');
			$('#status').val([]).multiselect('refresh');
			$('#types').val([]).multiselect('refresh');

			$('.analytic-btn[data-type="programs"] .filter-icon').removeClass('filterblue').addClass('filterblack');
			var data = {};
			$.show_filtered_programs(data).done(()=>{
				$('#prog_filter_model_box').modal('hide');
			});

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
        $status = $('#status').multiselect({
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
        $types = $('#types').multiselect({
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


	})
</script>
