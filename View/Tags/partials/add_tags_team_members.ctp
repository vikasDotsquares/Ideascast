<?php
echo $this->Html->css('projects/tokenfield/bootstrap-tokenfield');
echo $this->Html->script('projects/plugins/tokenfield/bootstrap-tokenfield-tags', array('inline' => true));
?>
<style type="text/css">
	.modal .modal-body{
		overflow: unset;
	}
	.multiselect-container.dropdown-menu li:not(.multiselect-group) a label.checkbox {
		padding: 5px 20px 5px 40px !important;
	}
	.send-nudge-tabs-nav {
		padding-bottom: 15px;
	}
	.multiselect-container.dropdown-menu li .multiselect-clear-filter{
		background: #fff;
	}
	.multiselect-container.dropdown-menu li .multiselect-clear-filter:hover{
		background: #f5f5f5;
		border-color: #ddd;
	}



	.tokenfield .token .token-label {
		padding-left: 0;
	}
	.tokenfield .token-input {
		margin-bottom: 0;
		margin-top: 1px;
	}
	.modal-tag-container:before{
		display: table;
		content: " ";
	}
	.modal-tag-container{
		background: #eee;
		color: #333;
		border-top:1px solid #aaa;
		padding: 15px;
		float: left;
		width:	100%
	}
	.tags_panel-inner{
		display: flex;
		align-items: center;
		width: 100%;
		position: relative;
	}
	.tokenfield .token-input {
		width: auto !important;
		margin-bottom: 6px;
	}

	.tags_block{
		flex-grow: 1;
	}
	.tags_btn_block{
		float:right;
		margin-left: 9px;
	}
	.tags_btn_block .btn-add[disabled="disabled"], .modal-footer .save-tags[disabled="disabled"] {
		background-color: #919191;
		border-color: #858585;
	}

	.tags_btn_block .btn-add {
		min-width: 62px;
	   color: #fff;
	}
	.tokenfield.form-control.focus{
		box-shadow: none;
		border-color: #d2d6de;
	}
	.tokenfield.form-control {
		min-height: 34px;
		padding: 5px 5px 0px 5px;
		border-color: #d2d6de;
		max-height: 61px;
		width: 100%;
		overflow: auto;
		display: flex;
		white-space: normal;
		flex-wrap: wrap;
		height: auto;
	}
	.tokenfield .token {
		color: #555;
		margin: 0 5px 5px 0;
	}
	.tags_panel-inner .tokenfield .token .token-label {
		text-overflow: unset;
		padding-left: 0;
	}

	.ui-autocomplete{
		z-index: 9999;
	}
	.ui-autocomplete.ui-front.ui-menu.ui-widget.ui-widget-content {
		max-height: 170px;
		width: 195px !important;
		min-width: 195px !important;
	   font-size: 14px;
	}

	.ui-widget-content {
		background-color: #fff;

	}

	.ui-menu .ui-menu-item {
		margin: 0;
		padding: 1px 5px;
		list-style-image:none;
	}
	.ui-menu .ui-menu-item.ui-state-focus {
		margin: 0;
		padding: 1px 5px;
		border: none;
		font-weight: 400;
		background: #f5f5f5;
	}
	.ui-menu .ui-menu-item:hover {
		background: #f5f5f5;
	}
	.tags_panel-inner .error-message {
		   position: absolute;
		   bottom: -13px;
		}
	.ui-autocomplete.ui-front.ui-menu.ui-widget.ui-widget-content {
		z-index: 9999 !important;
	}

</style>
<?php
$allUsers = $users = $selectedVal = [];
if($type == 'program') {
	$project_ids = program_projects($program_id, 0, 1);
	if(isset($project_ids) && !empty($project_ids)){
		$project_ids = Set::extract($project_ids, '/ProjectProgram/project_id');
	}
	$users = $this->ViewModel->projectPeople($project_ids);
}
else if($type == 'todo' || $type == 'sub-todo') {
	if(isset($project_id) && !empty($project_id)) {
		$users = $this->ViewModel->projectPeople($project_id);
	}
	else{
		$todo_users = get_todo_users($todo_id);
		if(isset($todo_users) && !empty($todo_users)){
			$me[$this->Session->read('Auth.User.id')] = $this->common->userFullname($this->Session->read('Auth.User.id'));
			$tusers = [];
			$todo_users = Set::extract($todo_users, '/DoListUser/user_id');
			foreach ($todo_users as $ind => $uid) {
				$tusers[$uid] = $this->common->userFullname($uid);
			}
			$allUsers = $tusers + $me;
		}
		else{
			$allUsers[$this->Session->read('Auth.User.id')] = $this->common->userFullname($this->Session->read('Auth.User.id'));
		}

	}
}
else if($type == 'nudge'){
	$sysusers = system_members();
	$sysusers = Set::extract($sysusers, '/User/id');
	foreach ($sysusers as $ind => $uid) {
		$allUsers[$uid] = $this->common->userFullname($uid);
	}
}
else if($type == 'board'){
	$sysusers = system_members();
	$sysusers = Set::extract($sysusers, '/User/id');
	foreach ($sysusers as $ind => $uid) {
		$allUsers[$uid] = $this->common->userFullname($uid);
	}
}
else if($type == 'workspace'){
	$users = $this->ViewModel->workspaceUsersFromUserPermission($workspace_id);
}
else if($type == 'task'){
	$users = $this->ViewModel->elementUsersFromUserPermission($task_id);
}
else if($type == 'competency'){
	if(!is_null($cusers)) {
		$usersArr = explode('~', $cusers);
		foreach ($usersArr as $ind => $uid) {
			$allUsers[$uid] = $this->common->userFullname($uid);
		}
	}
	$selectedVal[] = $selected;
}
else if($type == 'task_team'){
	$tusers = $this->Permission->taskUsers($task_id);
	if(isset($tusers) && !empty($tusers)){
		foreach ($tusers as $ind => $uid) {
			$allUsers[$uid['user_details']['user_id']] = $uid[0]['fullname'];
		}
	}
	$selectedVal[] = $selected;
}
else {
	$users = $this->ViewModel->projectPeople($project_id);
}

// pr($users);


if(isset($users) && !empty($users)){
	foreach ($users as $key => $value) {
		$allUsers[$value['user_details']['user_id']] = $value[0]['fullname'];
	}
}
asort($allUsers);
$nudgeUserList = array_keys($allUsers);
echo $this->Html->css('projects/bs-selectbox/bootstrap-multiselect');
echo $this->Html->script('projects/plugins/selectbox/bootstrap-multiselect', array('inline' => true));

echo $this->Html->css('projects/round-input') ?>
<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	<h3 class="modal-title" id="createModelLabel">Tag</h3>

</div>

<!-- POPUP MODAL BODY -->
<div class="modal-body elements-list tag-model-elements">
	<?php
	echo $this->Form->input('type', [ 'type' => 'hidden', 'value' => $type, 'class' => 'hidden_vals', 'id' => 'type' ] );
	echo $this->Form->input('program_id', [ 'type' => 'hidden', 'value' => $program_id, 'class' => 'hidden_vals', 'id' => 'program_id' ] );
	echo $this->Form->input('project_id', [ 'type' => 'hidden', 'value' => $project_id, 'class' => 'hidden_vals', 'id' => 'project_id' ] );
	echo $this->Form->input('workspace_id', [ 'type' => 'hidden', 'value' => $workspace_id, 'class' => 'hidden_vals', 'id' => 'workspace_id' ] );
	echo $this->Form->input('task_id', [ 'type' => 'hidden', 'value' => $task_id, 'class' => 'hidden_vals', 'id' => 'task_id' ] );
	echo $this->Form->input('hash', [ 'type' => 'hidden', 'value' => $hash, 'class' => 'hidden_vals', 'id' => 'hash' ] );
	echo $this->Form->input('risk_id', [ 'type' => 'hidden', 'value' => $risk_id, 'class' => 'hidden_vals', 'id' => 'risk_id' ] );
	echo $this->Form->input('todo_id', [ 'type' => 'hidden', 'value' => $todo_id, 'class' => 'hidden_vals', 'id' => 'todo_id' ] );
	?>
	<div class="row">
		<div class="col-sm-12 team_member_tag_pop popup-select-icon">
			<div class="form-group">
				<label for="RiskType">People: </label>
				<?php echo $this->Form->input('team_member_search', array(
					'options' => $allUsers,
					'class' => 'form-control',
					'id' => 'team_member_search',
					'label' => false,
					'div' => false,
					'multiple' => true,
					'selected' => $selectedVal
				)); ?>
				<span class="error-message team-member-err error text-danger"></span>
			</div>
		</div>
		<div class="col-sm-12">
			<div class="form-group">
				<div class="" id="team_mem_tags" >
                    <label for="RiskType">Tags: </label>
					<div class="tags_panel-inner">
						<div class="tags_block">

							<input type="hidden" name="selectedTags" id="selectedTags">
							<div id="tagbox">
								<input type="text" name="tags_ids" onpaste="return false;" id="tags" class="form-control tokenfield " placeholder="Tag name..." />
							</div>
							<span class="error-message" id="input_validation"></span>
						</div>
						<div class="tags_btn_block">
							<input type="button" name="add_tag_btn" id="add_tag_btn" class="btn btn-success btn-add" disabled="disabled" value="Add" />
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- POPUP MODAL FOOTER -->
<div class="modal-footer">
	 <button type="button"  class="btn btn-success save-tags" disabled="disabled">Apply Tags</button>
	 <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
</div>

<script type="text/javascript">

$.availableAllUsers = <?php echo json_encode($nudgeUserList); ?>;

$.selectedUserId = <?php echo json_encode($selected); ?>;

$.isUserSelected = 0;
$.isTagSelected = 0;
$(function(){

	/* PEOPLE PAGE SCRIPT */
    function sort_object (a, b){
        var aName = a.label.toLowerCase();
        var bName = b.label.toLowerCase();
        return ((aName < bName) ? -1 : ((aName > bName) ? 1 : 0));
    }
	if($js_config.people && $js_config.people !== undefined){
		var type = 'people';
		var search_text = $('.search-box[data-type="'+type+'"]').val();
		var data = {page: 0, type: type, search_text: search_text }
            data = $.createParams(data);

        $.ajax({
            type: "POST",
            url: $js_config.base_url + 'searches/people_list',
            data: data,
            dataType: 'JSON',
            success: function(response) {
                var content = response.content.sort(sort_object);
                $("#team_member_search").multiselect('dataprovider', content);
            }
         });
	}
	/* PEOPLE PAGE SCRIPT */

	if($js_config.planning && $js_config.planning !== undefined){
		var type = 'planning';
		var users = $('#allSelectedUsers').val();
		var data = {users: users };
		if(users != '' && users !== undefined){
	        $.ajax({
	            type: "POST",
	            url: $js_config.base_url + 'searches/planning_users',
	            data: data,
	            dataType: 'JSON',
	            success: function(response) {
	                var content = response.content.sort(sort_object);
	                $("#team_member_search").multiselect('dataprovider', content);
	            }
	        });
	    }
	}
	/* PLANNING PAGE SCRIPT */

	if($js_config.search_compare && $js_config.search_compare !== undefined){

		var type = $('#competency_tabs li.active a').data('type');
		var users = '';
		if(type == 'searchs') {
			users = $('#search_user_list').val();
		}
		else if(type == 'compare') {
			users = $('#compare_user_list').val();
		}
		else if(type == 'watch') {
			users = $('#watch_users').val();
		}
		var data = {type: type, users: users};

		if(users != '' && users !== undefined){
	        $.ajax({
	            type: "POST",
	            url: $js_config.base_url + 'competencies/competencies_users',
	            data: data,
	            dataType: 'JSON',
	            success: function(response) {
	                var content = response.content.sort(sort_object);
	                $("#team_member_search").multiselect('dataprovider', content);
	                $("#team_member_search").multiselect('select', [$.selectedUserId]).multiselect('refresh');
	            }
	        });
	    }
	}
	/* SEARCH AND COMPARE ON COMPETENCIES PAGE SCRIPT */


	$.checkSpace = function(string) {
		var regx = /^[A-Za-z0-9\-]+$/;
		if (!regx.test(string)) {
			return false;
		}
		return true;
		//return string.indexOf(' ') === -1;
	}
	$.submitBtnStatus = function() {
		$.isUserSelected = $('#team_member_search option:selected').length;
		var getTokens = $('#tags').tokenfield('getTokens');
		$.isTagSelected = getTokens.length;
		if($.isUserSelected > 0 && $.isTagSelected > 0) {
			$('.save-tags').attr('disabled', false);
		} else {
			$('.save-tags').attr('disabled', true);
		}
	}

	$('#tagbox').keydown(function(event) {
		if (event.ctrlKey==true && (event.which == '118' || event.which == '86')) {
			event.preventDefault();
		}
	});
	$('#tagbox').on("contextmenu",function(){
		return false;
	});
	//On blur on input tag box, add placeholder
	$( "body" ).on( "blur", "#tags-tokenfield", function() {
		$('#tags').data('bs.tokenfield').$input.attr('placeholder', 'Tag name...');
	});
	$( "body" ).on( "blur", "#my_tags-tokenfield", function() {
		$('#my_tags').data('bs.tokenfield').$input.attr('placeholder', 'Tag name...');
	});
	//On focus on input tag box, remove placeholder
	$( "body" ).on( "focus", "#tags-tokenfield", function() {
		$('#tags').data('bs.tokenfield').$input.attr('placeholder', '');
	});

	$( "body" ).delegate( "#my_tags-tokenfield", "focus", function(e) {
		$(this).attr('placeholder', '');
	});

	if ( $('#tags').length ) {
		$("input#tags").tokenfield({
			autocomplete: {
				source: function( request, response ) {
					$('#input_validation').html('');
					if(request.term.length > 0) {
						$('#add_tag_btn').attr('disabled', false);
						if(!$.checkSpace(request.term)) {
							$('#input_validation').html('Only alphanumeric characters or a hyphen are allowed');
							return;
						}
					} else {
						$('#add_tag_btn').attr('disabled', true);
					}

					var selectedTags = $('#selectedTags').val();
					//if( request.term != '' && request.term.length >= 1 ) {
						$.getJSON( $js_config.base_url + 'tags/get_tags', { term: request.term, selectedTags: selectedTags  }, function(response_data){
							//console.log('input#tags#autocomplete');
							//$('#add_tag_btn').attr('disabled', false);
							var items = [];
							if( response_data.success ) {
								if( response_data.content != null ) {
									$.each( response_data.content, function( key, val ) {
										var item ;
										//item = {'label': val, 'value': key}
										item = {'label': val, 'value': val}
										items.push(item);
									});
									response(items)
								}
							} else {

								response(items)
							}
						});
					//}
				},
				focus: function( event, ui ) {
					return false;
				},
				delay: 100,
			},
			showAutocompleteOnFocus: true,
			allowEditing: false,
			allowPasting: false,
			beautify: false,
			maxTagWidth: 'auto',
			deleteOnBackspace: false,
			inputMaxLength: 30,
			onRemoveFocus: false,
			//delimiter: ''
		})
		.on('tokenfield:createtoken', function (event) {
			//console.log('input#tags#createtoken');
			var existingTokens = $(this).tokenfield('getTokens');
			$.each(existingTokens, function(index, token) {
				if (token.value.toLowerCase() === event.attrs.value.toLowerCase())
				event.preventDefault();
			});

			var regx = /^[A-Za-z0-9\-]+$/;
			if (!regx.test(event.attrs.value)) {
				event.preventDefault();
				return false;
			}
		})
		.on('tokenfield:createdtoken', function (event, save) {
			//console.log('input#tags#createdtoken');
			var selectedTags = $('#selectedTags').val();
			if(selectedTags != '') {
				var selectedTags = selectedTags.split(',');
			} else {
				var selectedTags = [];
			}
			selectedTags.push(event.attrs.value);

			selectedTags = selectedTags.join(',');
			$('#selectedTags').val(selectedTags);

			$('#tagbox .tokenfield').addClass('focus');
			setTimeout(function() {
				$('#tags-tokenfield').blur();
				$('#tags-tokenfield').focus()
			}, 0)
			$.submitBtnStatus()
		})
		.on('tokenfield:removedtoken', function (event) {
			//console.log('input#tags#removedtoken');
			event.preventDefault();

			var selectedTags = $('#selectedTags').val();
			if(selectedTags != '') {
				var selectedTags = selectedTags.split(',');
			} else {
				var selectedTags = [];
			}
			selectedTags.splice($.inArray(event.attrs.value, selectedTags),1);

			selectedTags = selectedTags.join(',');
			$('#selectedTags').val(selectedTags);

			setTimeout(function() {
				$('#tags-tokenfield').blur();
				$('#tags-tokenfield').focus()
			}, 0)

			$.submitBtnStatus()
		});
		//Save tags on button click
		$('#add_tag_btn').on('click', function(key, val){
			var new_tag = $('#tags').data('bs.tokenfield').$input.val();
			$('#tags').data('bs.tokenfield').$input.val('')
			if(new_tag != '') {
				$('#tags').tokenfield('createToken', new_tag);
			}
			if($.tag_added !== undefined){
				$.tag_added = true;
			}
		});
	}

	// USER'S MULTISELECT BOX INITIALIZATION
	$.team_member_search = $('#team_member_search').multiselect({
		enableUserIcon: false,
		buttonClass: 'btn btn-default aqua',
		buttonWidth: '100%',
		buttonContainerWidth: '100%',
		numberDisplayed: 2,
		maxHeight: '318',
		checkboxName: 'nudge_users[]',
		includeSelectAllOption: true,
		enableFiltering: true,
		filterPlaceholder: 'Search text...',
		enableCaseInsensitiveFiltering: true,
		nonSelectedText: 'Select People',
		templates: {
			filterClearBtn: '<span class="input-group-btn"><button class="btn btn-default multiselect-clear-filter tipText" type="button" title="Clear Search"><i class="glyphicon glyphicon-remove-circle"></i></button></span>',
		},
		onSelectAll: function() {
			var sel_users = $('#team_member_search option:selected');
			var selected = [];
			$(sel_users).each(function(index, brand){
				selected.push($(this).val());
			});
			$.text_search_users = selected;
			$.submitBtnStatus();
		},
		onDeselectAll: function() {console.log('f');
			$.text_search_users = [];
			$.submitBtnStatus();
		},
		onChange: function(element, checked) {
			var sel_users = $('#team_member_search option:selected');
			var selected = [];
			$(sel_users).each(function(index, brand){
				selected.push($(this).val());
			});
			$.text_search_users = selected;

			$.submitBtnStatus();
		}
	});
	/* SJ code for skill and tags*/

	$('.save-tags').click(function(event) {
		event.preventDefault();

		$.isUserSelected = $('#team_member_search option:selected').length;
		var getTokens = $('#tags').tokenfield('getTokens');
		$.isTagSelected = getTokens.length;
		if($.isUserSelected > 0 && $.isTagSelected > 0) {
			var formData = {};

			var sel_users = $('#team_member_search option:selected');
			var selected = [];
			$(sel_users).each(function(index, brand){
				selected.push($(this).val());
			});
			var selectedUsers = selected.join(',');

			formData['user'] = selectedUsers;
			formData['tags'] = $('#tags').tokenfield('getTokensList');

			$(this).addClass('disabled');

			$.ajax({
				url: $js_config.base_url + 'tags/save_tags_team_members',
				type: 'POST',
				dataType: 'json',
				data: formData,
				success: function(response) {
					$('.modal-body').parents('.modal').modal('hide');
					$(this).removeClass('disabled');
					if($js_config.people && $js_config.people !== undefined){
						$.getPeopleList();
					}
				}
			});
		} else {
			return;
		}
	});
})
function apply_user_filter(type, selected) {
	$.ajax({
		url: $js_config.base_url + 'tags/apply_user_filter',
		type: 'POST',
		dataType: 'json',
		data: { type: type, selected: selected },
		success: function(response) {
			if(response.success){
				if(type == 'tag') {
					$.nudge_user_tag_search.multiselect('deselectAll', false);
					var queryArr = [];
					if(response.content.length > 0) {
						$.each(response.content, function(index, el) {
							var pieces = {
								"label" :el.label, "value" :el.value
							};
							if($.inArray(parseInt(el.value), $.availableAllUsers) != -1) {
								queryArr.push( pieces );
							}
						});
					}
					$.nudge_user_tag_search.multiselect('dataprovider', queryArr);
					$.tag_search_users = [];
				}
				if(type == 'skill') {
					$.nudge_user_skill_search.multiselect('deselectAll', false);
					var queryArr = [];
					if(response.content.length > 0) {
						$.each(response.content, function(index, el) {
							var pieces = {
								"label" :el.label, "value" :el.value
							};
							if($.inArray(parseInt(el.value), $.availableAllUsers) != -1) {
								queryArr.push( pieces );
							}
						});
					}
					$.nudge_user_skill_search.multiselect('dataprovider', queryArr);
					$.skill_search_users = [];
				}
			}
		}
	});
}
</script>
<style type="text/css">
	#nudge_message {
		resize: vertical;
	}
	.extra-options {
		display: inline-block;
	    font-weight: 700;
	    margin-bottom: 5px;
	    margin-right: 5px;
	    max-width: 100%;
	    position: relative;
	    top: 3px;
	    vertical-align: top;
	}
.multiselect-container.dropdown-menu > li:not(.multiselect-group) {
	vertical-align: top;
}
</style>
<?php echo $this->Html->script('jquery.tokeninput', array('inline' => true)); ?>