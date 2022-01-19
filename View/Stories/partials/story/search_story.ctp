<?php
echo $this->Html->css('projects/bs-selectbox/bootstrap-multiselect');
echo $this->Html->script('projects/plugins/selectbox/bootstrap-multiselect', array('inline' => true));

$type_list = $cuser_list = $muser_list = [];
if(isset($all_types) && !empty($all_types)){
	foreach ($all_types as $key => $value) {
		$type_list[$value['story_types']['id']] = $value['story_types']['type'];
	}
}
if(isset($created_users) && !empty($created_users)){
	foreach ($created_users as $key => $value) {
		$cuser_list[$value['user_details']['user_id']] = $value[0]['full_name'];
	}
}
if(isset($modified_users) && !empty($modified_users)){
	foreach ($modified_users as $key => $value) {
		$muser_list[$value['user_details']['user_id']] = $value[0]['full_name'];
	}
}

?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h3 class="modal-title" id="createModelLabel">Filter List</h3>
</div>
<div class="modal-body popup-select-icon clearfix">
	<div class="f-story-field">
		<?php echo $this->Form->input('Story.type_id', array('type' => 'select', 'options' => $type_list, 'label' => false, 'div' => false, 'class' => 'form-control', 'multiple' => 'multiple', 'id' => 'story_types' )); ?>
	</div>
	<div class="f-story-field">
		<?php echo $this->Form->input('Story.created_by', array('type' => 'select', 'options' => $cuser_list, 'label' => false, 'div' => false, 'class' => 'form-control', 'multiple' => 'multiple', 'id' => 'story_created' )); ?>
	</div>
	<div class="f-story-field">
		<?php echo $this->Form->input('Story.modified_by', array('type' => 'select', 'options' => $muser_list, 'label' => false, 'div' => false, 'class' => 'form-control', 'multiple' => 'multiple', 'id' => 'story_updated' )); ?>
	</div>
</div>
<!-- POPUP MODAL FOOTER -->
<div class="modal-footer">
	<button type="button" class="btn btn-success btn-filter filterdisabled" > Filter</button>
	<button type="button" class="btn btn-danger btn-clear"> Clear</button>
	<button type="button" class="btn btn-danger" data-dismiss="modal"> Cancel</button>
</div>

<script type="text/javascript">
	$(function(){

		var filter = false;
		if($.extraFilters.hasOwnProperty('types')){
			$('#story_types').val($.extraFilters['types']);
			filter = true;
		}
		if($.extraFilters.hasOwnProperty('created')){
			$('#story_created').val($.extraFilters['created']);
			filter = true;
		}
		if($.extraFilters.hasOwnProperty('updated')){
			$('#story_updated').val($.extraFilters['updated']);
			filter = true;
		}

		if(filter){
			$('.btn-filter').removeClass('filterdisabled');
		}

		$('#story_types, #story_created, #story_updated').off('change').on('change', function(event) {
			var types = ($('#story_types').val() != null || $('#story_types').val() != undefined) ? $('#story_types').val().length : false,
				created = ($('#story_created').val() != null || $('#story_created').val() != undefined) ? $('#story_created').val().length : false,
				updated = ($('#story_updated').val() != null || $('#story_updated').val() != undefined) ? $('#story_updated').val().length : false;
			if(types || created || updated){
				$('.btn-filter').removeClass('filterdisabled');
			}
			else{
				$('.btn-filter').addClass('filterdisabled');
			}
		});

		$('.btn-clear').off('click').on('click', function(event) {
			event.preventDefault();
			$('#story_types').val([]).multiselect('refresh');
			$('#story_created').val([]).multiselect('refresh');
			$('#story_updated').val([]).multiselect('refresh');
			$.extraFilters = {};

			var searchtext = $('.search-box[data-type="story"]').val();
			if(searchtext.length > 0){
				$('.search-box[data-type="story"]').trigger('keyup')
			}
			else {
				$.get_stories();
			}

			$('#tab_story .storie-heading[data-coloumn="story_type"], #tab_story .storie-heading[data-coloumn="created_by"], #tab_story .storie-heading.updated-head').removeClass('st-blue');
			$('.filter-black-icon').removeClass('filterblue');
			$('.btn-filter').addClass('filterdisabled');
			$('#story_search').modal('hide')
		});

		$('.btn-filter').off('click').on('click', function(event) {
			event.preventDefault();
			var parent = $('.story-list-wrapper').parents('.tab-pane:first')
			var types = ($('#story_types').val() != null || $('#story_types').val() != undefined) ? $('#story_types').val() : [],
				created = ($('#story_created').val() != null || $('#story_created').val() != undefined) ? $('#story_created').val() : [],
				updated = ($('#story_updated').val() != null || $('#story_updated').val() != undefined) ? $('#story_updated').val() : [];

			var order = 'asc',
	        coloumn = 'name';
	        if( $('.ssd-wrap .ssd-col-header', parent).find('.sort_order.active').length > 0 ) {
	            order = $('.ssd-wrap .ssd-col-header', parent).find('.sort_order.active').data('order'),
	            coloumn = $('.ssd-wrap .ssd-col-header', parent).find('.sort_order.active').data('coloumn');

	            if( order == 'asc' ){
	                order = 'desc';
	            } else {
	                order = 'asc';
	            }
	        }
			var searchtext = $('.search-box[data-type="story"]').val();
			var data = { type: 'story', order: order, coloumn: coloumn, q: searchtext, 'extra': {types: types, created: created, updated: updated}};

			$.ajax({
				url: $.module_url + 'filter_story',
				type: 'POST',
				dataType: 'json',
				data: data,
				success: function(response) {
					if(types.length > 0){
						$('#tab_story .storie-heading[data-coloumn="story_type"]').addClass('st-blue');
					}
					else{
						$('#tab_story .storie-heading[data-coloumn="story_type"]').removeClass('st-blue');
					}
					if(created.length > 0){
						$('#tab_story .storie-heading[data-coloumn="created_by"]').addClass('st-blue');
					}
					else{
						$('#tab_story .storie-heading[data-coloumn="created_by"]').removeClass('st-blue');
					}
					if(updated.length > 0){
						$('#tab_story .storie-heading.updated-head').addClass('st-blue');
					}
					else{
						$('#tab_story .storie-heading.updated-head').removeClass('st-blue');
					}
					// $('#tab_story .storie-heading[data-coloumn="story_type"], #tab_story .storie-heading[data-coloumn="created_by"], #tab_story .storie-heading.updated-head').addClass('st-blue');
					$('.filter-black-icon').addClass('filterblue');
					$.extraFilters = {types: types, created: created, updated: updated};

					$.countRows('story', parent);
					$('.list-wrapper', parent).scrollTop(0)

					$('.story-list-wrapper').html(response);
					$('#story_search').modal('hide')
				}
			})

		});

		$story_types = $('#story_types').multiselect({
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
	            nonSelectedText: 'Select Types',
	            onSelectAll:function(){
	            	var selected = $('#story_types').val();
				},
				onDeselectAll:function(){
				},
	            onChange: function(element, checked) {
	            	var selected = $('#story_types').val();
	            }
	        });

		$story_created = $('#story_created').multiselect({
	            enableUserIcon: false,
	            buttonClass: 'btn btn-default aqua',
	            buttonWidth: '100%',
	            buttonContainerWidth: '100%',
	            numberDisplayed: 2,
	            maxHeight: '318',
	            checkboxName: 'dept[]',
	            includeSelectAllOption: true,
	            enableFiltering: true,
	            filterPlaceholder: 'Search Created By',
	            enableCaseInsensitiveFiltering: true,
	            nonSelectedText: 'Select Created By',
	            onSelectAll:function(){
	            	var selected = $('#story_created').val();
				},
				onDeselectAll:function(){
				},
	            onChange: function(element, checked) {
	            	var selected = $('#story_created').val();
	            }
	        });

		$story_updated = $('#story_updated').multiselect({
	            enableUserIcon: false,
	            buttonClass: 'btn btn-default aqua',
	            buttonWidth: '100%',
	            buttonContainerWidth: '100%',
	            numberDisplayed: 2,
	            maxHeight: '318',
	            checkboxName: 'dept[]',
	            includeSelectAllOption: true,
	            enableFiltering: true,
	            filterPlaceholder: 'Search Updated By',
	            enableCaseInsensitiveFiltering: true,
	            nonSelectedText: 'Select Updated By',
	            onSelectAll:function(){
	            	var selected = $('#story_updated').val();
				},
				onDeselectAll:function(){
				},
	            onChange: function(element, checked) {
	            	var selected = $('#story_updated').val();
	            }
	        });


	})
</script>