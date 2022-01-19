<?php $tags = array();?>
<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
		<span aria-hidden="true">&times;</span>
	</button>
	<h3 class="modal-title"><?php echo $viewData['title'];?></h3>
</div>
<div class="modal-body clearfix">
	<div class="model-message" style="padding: 0 0 10px 0;"><?php echo $viewData['message'];?></div>
	<?php if($viewData['type'] == 'add_remove_tags') { ?>
		<?php echo $this->Form->create('Tag', array('url' => array('controller' => 'tags', 'action' => 'add_remove_tags'), 'role' => 'form', 'id' => 'frm_add_remove_tags', 'class' => 'clearfix')); ?>
		<?php echo $this->Form->input('is_select_all', array('type' => 'hidden', 'label' => false, 'div' => false, 'id' => 'isSelectAllTags', 'value' => 0)); ?>
		<?php echo $this->Form->input('selected_multi_tags', array('type' => 'hidden', 'label' => false, 'div' => false, 'id' => 'selectedMultiTags')); ?>
		<div>
			<div class="col-sm-10 nopadding-left sel_tag_container">
				<div class="form-group">
					<select class="form-control sel_tags" id="sel_tags" style="height:30px;display:none;" multiple="multiple" name="sel_tags[]">
						<?php
						if( isset($viewData['tags']) && !empty($viewData['tags']) ){
							$tags = $viewData['tags'];
							foreach($tags as $k => $v){
								?>
								<option value="<?php echo $v; ?>"><?php echo htmlentities($v); ?></option>
								<?php
							}
						}
						?>
					</select>
					<span id="resp_msg" class="resp_msg"></span>
					<span class="loader-icon fa fa-spinner fa-pulse"></span>
				</div>
			</div>
		</div>
		<?php echo $this->Form->end(); ?>
		<?php
	} ?>
</div>
<div class="modal-footer">
	<?php if($viewData['type'] == 'add_remove_tags') { ?>
		<button class="btn btn-success btn-add-tags"> Add</button>
		<button class="btn btn-success btn-remove-tags"> Remove</button>
		<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
	<?php } else if($viewData['type'] == 'clear_all_tags') { ?>
		<button class="btn btn-success action_clear_all_tags"> Clear</button>
		<button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
	<?php } else if($viewData['type'] == 'delete_my_tags') { ?>
		<button class="btn btn-success action_delete_my_tags"> Delete</button>
		<button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
	<?php } ?>
</div>
<script>
$(function(){
	<?php if($viewData['type'] == 'add_remove_tags') { ?>
		$.multiselectTags = $('#sel_tags').multiselect({
			enableUserIcon: false,
			multiple: true,
			enableHTML: true,
			enableFiltering: true,
			includeSelectAllOption: true,
			nonSelectedText: 'Select Tags',
			numberDisplayed: 2,
			filterPlaceholder: 'Search text...',
			enableCaseInsensitiveFiltering: true,
			buttonWidth: '100%',
			maxHeight: 387,
			templates: {
                filterClearBtn: '<span class="input-group-btn"><button class="btn btn-default multiselect-clear-filter tipText" type="button" title="Clear Search"><i class="glyphicon glyphicon-remove-circle"></i></button></span>',
            },
			onSelectAll: function() {
			},
			onDeselectAll: function() {
			},
			onChange: function(element, checked) {
				$('#resp_msg').html('');
			}
		})
		$('.btn-add-tags').on('click', function(event){
			event.preventDefault();
			callAddRemoveFunc('add');
		})
		$('.btn-remove-tags').on('click', function(event){
			event.preventDefault();
			callAddRemoveFunc('remove');
		})
		/*$("#add_remove_tags_model").on('hidden.bs.modal', function () {
			$('#isSelectAllTags').val(0);
			$('#selectedMultiTags').val('');
			$('#resp_msg').html('');
			$("#sel_tags").multiselect('deselectAll', false);
			$("#sel_tags").multiselect('refresh');
		});*/
	<?php } ?>
	$("body").on('click', '.action_delete_my_tags', function(event){
		clearTextFromTagInput('my_tags');
		var userListType = $('#paging_type').val();
		var actionURL = $js_config.base_url+'tags/tags_actions'; // Extract info from data-* attributes

		$.when(
			$.ajax({
				url : actionURL,
				type: "POST",
				data: $.param({action: 'delete_my_tags', tags: ''}),
				global: false,
				dataType: 'JSON',
				success:function(response){
					//location.reload();
				}
			})
		).then(function( data, textStatus, jqXHR ) {
			if(data.status == true){
				//Remove all UI and destroy Autocomplete
				$('#show_user_btn').trigger('click');

				$.availableTags = [];
				$("input#my_tags").tokenfield('destroy');
				$.add_tokenfield();
				$('#selectedMyTags').val('');
				$('#showingUsersForTags').val('');
				$("#show_user_btn").attr('disabled', true);
				$('.tag-dd-menu a.clear_all_tags, .tag-dd-menu a.add_remove_tags').addClass('disabled');
				//$('#menu1').attr('disabled', false).removeClass('disabled');
				//Remove all UI and destroy Autocomplete

				setTimeout(function () {
					$("#dropdown_action_modal").modal('hide');
				}, 500);
			}
		})
	})

	$("body").on('click', '.action_clear_all_tags', function(event){
		clearTextFromTagInput('my_tags');
		var mytags = $('#showingUsersForTags').val();
		var match_all = 0;
		if($('input#match_all_checkbox').is(":checked")) {
			var match_all = 1;
		}
		var userListType = $('#paging_type').val();
		var actionURL = $js_config.base_url+'tags/tags_actions'; // Extract info from data-* attributes

		$.when(
			$.ajax({
				url : actionURL,
				type: "POST",
				data: $.param({action: 'clear_all_people_tags', tags: mytags, match_all: match_all, userListType: userListType}),
				global: false,
				dataType: 'JSON',
				success:function(response){
					//location.reload();
				}
			})
		).then(function( data, textStatus, jqXHR ) {
			if(data.status == true){
				//Remove all UI and destroy Autocomplete
				//$('#show_user_btn').trigger('click');
				setTimeout(function(){
					$.getJSON(
						$js_config.base_url + 'tags/get_saved_tags',
						{ },
						function(result){
							$.availableTags = result;
							
							$("input#my_tags").tokenfield('destroy');
							$.add_tokenfield();
							
							$('#selectedMyTags').val('');
							$('#showingUsersForTags').val('');
							$("#show_user_btn").attr('disabled', true);
							$('.tag-dd-menu a.clear_all_tags, .tag-dd-menu a.add_remove_tags').addClass('disabled');
							
							$('.tag-data-row .tag_container').html('');
						}
					);
				}, 500)
				
				//Remove all UI and destroy Autocomplete

				setTimeout(function () {
					$("#dropdown_action_modal").modal('hide');
				}, 500);
			}
		})
	})
})
function callAddRemoveFunc(action) {
	var selTagIds = $('#sel_tags').val();
	var userListType = $('#paging_type').val();
	var filteredTags = $('#showingUsersForTags').val();
	var matchAll = 0;
	if($('input#match_all_checkbox').is(":checked")) {
		var matchAll = 1;
	}

	if($('#sel_tags option:selected').length > 0) {
		$.ajax({
			url: $js_config.base_url + 'tags/add_remove_tags',
			data: $.param({action: action, sel_tag_ids: selTagIds, filtered_tags: filteredTags, match_all: matchAll, userListType: userListType}),
			type: 'post',
			dataType: 'JSON',
			success: function(response){
				if( response.status == true && response.message != '') {
					$('#resp_msg').html(response.message);
					//var selTagIdsExp = selTagIds.split(",");
					if(action == 'remove') {
						/*$.getJSON(
							$js_config.base_url + 'tags/get_saved_tags',
							{ },
							function(result){*/
								$.availableTags = response.data;
								$.multiselectTags.multiselect('deselectAll', false);
								var queryArr = [];
								$.each(response.data, function(index, el) {
									var pieces = {
								       "label" :el,
								       "value" :el
								    };
								    queryArr.push( pieces );
								});
								
								$.multiselectTags.multiselect('dataprovider', queryArr);
								// setTimeout(function(){
								// 	$.multiselectTags.multiselect('refresh');
								// }, 1)

								//$("input#my_tags").tokenfield('destroy');
								//$.add_tokenfield();
								//$("input#my_tags").tokenfield('setTokens', $('#showingUsersForTags').val())
								//$("#show_user_btn").focus();
								//$("#show_user_btn").attr('disabled', true);
							/*}
						);*/

						$.each(selTagIds, function(k,v){
							$.each($('.tag-data-row'), function(key, val) {
								$(this).find('.token[data-title="' + v + '"]').remove();
							})
						})
					}
				}
			}
		})
	}
}
//$.availableTags = <?php echo json_encode($tags); ?>;
</script>