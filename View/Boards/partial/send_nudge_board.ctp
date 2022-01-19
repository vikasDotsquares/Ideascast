<style type="text/css">
	.checkbox-nudge label.fancy_label{font-weight: normal;}
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
	.send-nudge-tabs-nav .nav-tabs {
	    border-bottom: none;
	    vertical-align: top;
	}
	 .send-nudge-tabs-nav .nav-tabs>li {
	    margin-bottom: 0;
	     margin-right: 10px;
	     border-right: 2px solid #67a028;
	     padding-right: 10px;
	}
	 .send-nudge-tabs-nav .nav-tabs>li:last-child {
	     margin-right: 0;
	     border-right: none;
	     padding-right: 0;
	}
	.send-nudge-tabs-nav .nav-tabs>li a{
	    padding:4px 0;
	    margin-right: 0;
	    border: none;
	    font-weight: 600;
	}
	 .send-nudge-tabs-nav .nav-tabs>li.active>a, .send-nudge-tabs-nav .nav-tabs>li.active>a:focus, .send-nudge-tabs-nav .nav-tabs>li.active>a:hover {
	    color: #444;
	    border: none;
	     background: #fff;
	}
	.send-nudge-tabs-nav .nav-tabs>li>a:hover {
	    background: #fff;
	    color: #444;
	}

   .send-nudge-pop-tabs {
    /*padding-bottom: 15px;*/
    min-height: 50px;
	}
	.skills-tags-info {
	    min-height: 34px;
	    margin-bottom: 15px;
	    margin-top: 0;
	    padding-top: 10px;
	    color: #444;
	        font-weight: 400;
	}
	    .nudge-pop-group{
	        position: relative;
	    }
	 .checkbox-nudge {
	    position: absolute;
	    right: 0;
	    bottom: -20px;
	     margin: 0;
	}
	.checkbox-nudge label.fancy_label {
	    background-image: none;
	    padding-left: 0 !important;
	    font-size: 13px;
	}
	.checkbox-nudge label.fancy_label {
	    height: 14px;
	    line-height: 14px;
        margin: 0;
	}
	.checkbox-nudge .fancy_label {
	    position: relative;
	    top: -2px;
	}

</style>
<?php

$spclCase = 0;
$allUsers = $users = $selectedVal = [];
/*if($type == 'program') {
	$project_ids = program_projects($program_id, 0, 1);
	if(isset($project_ids) && !empty($project_ids)){
		$project_ids = Set::extract($project_ids, '/ProjectProgram/project_id');
	}
	$users = $this->ViewModel->projectPeople($project_ids);
}*/
if($type == 'program_team') {
	$users = $this->Scratch->program_team($program_id);
	if(isset($users) && !empty($users)){
		$selectedVal = Set::extract($users, '{n}.user_permissions.user_id');
	}
}
elseif($type == 'program_stakeholders') {
	$program_creator = $this->Scratch->program_creator($program_id);
	$stakeholders = $this->Scratch->program_stakeholders($program_id);
	if(isset($stakeholders) && !empty($stakeholders)){
		foreach ($stakeholders as $key => $value) {
			$users[] = [
				'user_permissions' => ['user_id' => $value['user_details']['user_id']],
				'0' => ['fullname' => $value[0]['fullname']]
			];
		}
		$users[] = [
			'user_permissions' => ['user_id' => $program_creator[0]['user_details']['user_id']],
			'0' => ['fullname' => $program_creator[0][0]['fullname']]
		];
		uasort($users, function($a, $b) {
		    $at = iconv('UTF-8', 'ASCII//TRANSLIT', $a[0]['fullname']);
		    $bt = iconv('UTF-8', 'ASCII//TRANSLIT', $b[0]['fullname']);
		    return strcasecmp($at, $bt);
		});
		$selectedVal = Set::extract($stakeholders, '{n}.user_details.user_id');
		$selectedVal[] = $program_creator[0]['user_details']['user_id'];
	}
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
else if($type == 'prj_board') {
	/* Code by SJ for change on Project*/
	$users = $this->Permission->board_project_users($project_id);

	$getProjData = $this->Common->get_ProjData($project_id);
	$skills = $subjects = $domains = array();
	if(!empty($getProjData)) {
		$projSkills  = $getProjData[0][0]['proj_skills'];
		if(!empty($projSkills)) {
			$projSkillsExp = explode(';', $projSkills);
			$inc = 0;
			foreach($projSkillsExp as $k => $v) {
				$skillsExp = explode(',', $v);
				$skills[$inc]['label'] = $skillsExp[0];
				$skills[$inc]['value'] = $skillsExp[1];
				$inc++;
			}
		}
		$projSubjects  = $getProjData[0][0]['proj_subjects'];
		if(!empty($projSubjects)) {
			$projSubjectsExp = explode(';', $projSubjects);
			$inc = 0;
			foreach($projSubjectsExp as $k => $v) {
				$subsExp = explode(',', $v);
				$subjects[$inc]['label'] = $subsExp[0];
				$subjects[$inc]['value'] = $subsExp[1];
				$inc++;
			}
		}
		$projDomains  = $getProjData[0][0]['proj_domains'];
		if(!empty($projDomains)) {
			$projDomainsExp = explode(';', $projDomains);
			$inc = 0;
			foreach($projDomainsExp as $k => $v) {
				$domsExp = explode(',', $v);
				$domains[$inc]['label'] = $domsExp[0];
				$domains[$inc]['value'] = $domsExp[1];
				$inc++;
			}
		}
	}
	$spclCase = 1;
}
else if($type == 'tags'){
	if(isset($search_tag_users) && !empty($search_tag_users)){
		$allUsers = $this->Scratch->users_detail($search_tag_users);
		if(isset($allUsers) && !empty($allUsers)) {
			$allUsers = Set::combine($allUsers, '{n}.u.id', '{n}.0.full_name');
		}
	}
	else{
		$sysusers = system_members();
		$sysusers = Set::extract($sysusers, '/User/id');
		foreach ($sysusers as $ind => $uid) {
			$allUsers[$uid] = $this->common->userFullname($uid);
		}
	}
	/**/
}
else {
	$users = $this->ViewModel->projectPeople($project_id);
}

/***********************************************************************************************/


if(isset($users) && !empty($users)){
	if($spclCase == 1) {
		foreach ($users as $key => $value) {
			$allUsers[$value['user_details']['user_id']] = $value[0]['fullname'];
		}
	} else {
		foreach ($users as $key => $value) {
			$allUsers[$value['user_permissions']['user_id']] = $value[0]['fullname'];
		}
	}
}

$nudgeUserList = array_keys($allUsers);
if(!empty($nudgeUserList) && $spclCase == 0) {
	$skillSet = $this->Common->get_skill_of_users($nudgeUserList);

	if(!empty($skillSet)) {
		$skills = [];
		foreach ($skillSet as $key => $value) {
			$skills[$key]['label'] = $value['skills']['title'];
			$skills[$key]['value'] = $value['user_skills']['skill_id'];
		}
	}
	$subjectSet = $this->Common->get_subject_of_users($nudgeUserList);
	if(!empty($subjectSet)) {
		$subjects = [];
		foreach ($subjectSet as $key => $value) {
			$subjects[$key]['label'] = $value['subjects']['title'];
			$subjects[$key]['value'] = $value['user_subjects']['subject_id'];
		}
	}
	$domainSet = $this->Common->get_domain_of_users($nudgeUserList);
	if(!empty($domainSet)) {
		$domains = [];
		foreach ($domainSet as $key => $value) {
			$domains[$key]['label'] = $value['knowledge_domains']['title'];
			$domains[$key]['value'] = $value['user_domains']['domain_id'];
		}
	}
}

if($type == 'task_team'){
	$tusers = $this->Permission->taskUsers($task_id);
	if(isset($tusers) && !empty($tusers)){
		foreach ($tusers as $ind => $uid) {
			$allUsers[$uid['user_details']['user_id']] = $uid[0]['fullname'];
		}
	}
	$selectedVal[] = $selected;
}

if($type == 'wsp_team'){
	$tusers = $this->Permission->workspaceUsers($project_id, $workspace_id);
	if(isset($tusers) && !empty($tusers)){
		foreach ($tusers as $ind => $uid) {
			$allUsers[$uid['user_details']['user_id']] = $uid[0]['fullname'];
		}
	}
	$selectedVal[] = $selected;
}

if($type == 'project_team'){
	$tusers = $this->Permission->project_all_users($project_id, $workspace_id);
	if(isset($tusers) && !empty($tusers)){
		foreach ($tusers as $ind => $uid) {
			$allUsers[$uid['user_details']['user_id']] = $uid[0]['fullname'];
		}
	}
	$selectedVal[] = $selected;
}
asort($allUsers, SORT_STRING | SORT_FLAG_CASE );

if($type == 'people'){
	$sysusers = system_members();
	$sysusers = Set::extract($sysusers, '/User/id');
	foreach ($sysusers as $ind => $uid) {
		$allUsers[$uid] = $this->common->userFullname($uid);
	}
}


// pr($nudgeUserList);
echo $this->Html->css('projects/bs-selectbox/bootstrap-multiselect');
echo $this->Html->script('projects/plugins/selectbox/bootstrap-multiselect', array('inline' => true));

echo $this->Html->css('projects/round-input') ?>
<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	<h3 class="modal-title" id="createModelLabel">Nudge</h3>

</div>

<!-- POPUP MODAL BODY -->
<div class="modal-body   popup-select-icon">
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
		<div class="col-sm-12 send-nudge-tabs-nav">
			<ul class="nav nav-tabs comments" style="cursor: move; display: inline-block;">
				<li class="active">
					<a data-toggle="tab" class="active" data-target="#text_search" href="#text_search" aria-expanded="true">Search</a>
				</li>
				<li class="">
					<a data-toggle="tab" data-target="#tag_search" href="#tag_search" aria-expanded="false">Tags</a>
				</li>
				<li class="">
					<a data-toggle="tab" data-target="#skills_search" href="#skills_search" aria-expanded="false">Skills</a>
				</li>
				<li class="">
					<a data-toggle="tab" data-target="#subjects_search" href="#subjects_search" aria-expanded="false">Subjects</a>
				</li>
				<li class="">
					<a data-toggle="tab" data-target="#domains_search" href="#domains_search" aria-expanded="false">Domains</a>
				</li>
			</ul>
		</div>
		<div class="col-sm-12 send-nudge-pop-tabs">
			<div class="tab-content" id="myTabContent">
				<div id="text_search" class="tab-pane fade active in">
					<h5 class="skills-tags-info">Search for People or filter on Tags and Competencies.</h5>
					<div class="form-group">
						<label for="RiskType">To: </label>
						<?php echo $this->Form->input('nudge_user_text_search', array(
							'options' => $allUsers,
							'class' => 'form-control',
							'id' => 'nudge_user_text_search',
							'label' => false,
							'div' => false,
							'multiple' => true,
							'selected' => $selectedVal
						)); ?>
						<span class="error-message nudge-user-err error text-danger"></span>
					</div>
				</div>
				<div id="tag_search" class="tab-pane fade">
                    <div class="form-group  nudge-pop-group">
						<select class="form-control sel_tags_nudge" id="sel_tags_nudge" style="height:30px;display:none;" multiple="multiple" name="tag_select[]">
							<?php
							if( isset($tags) && !empty($tags) ){
								foreach($tags as $k => $v){
									?>
									<option value="<?php echo $v['value']; ?>"><?php echo htmlentities($v['label']); ?></option>
									<?php
								}
							}
							?>
						</select>
                        <label class="checkbox-nudge">
							<input type="checkbox" id="match_all_tag_checkbox" name="match_all_tag_checkbox" class="" value="0">
							<label class="fancy_label text-black" for="match_all_tag_checkbox">Require All</label>
						</label>
					</div>
					<div class="form-group">
						<label for="RiskType">To: </label>
						<?php echo $this->Form->input('nudge_user_tag_search', array(
							'options' => $allUsers,
							'class' => 'form-control',
							'id' => 'nudge_user_tag_search',
							'label' => false,
							'div' => false,
							'multiple' => true,
						)); ?>
						<span class="error-message nudge-user-err error text-danger"></span>
					</div>
				</div>
				<div id="skills_search" class="tab-pane fade">
                    <div class="form-group  nudge-pop-group">
						<select class="form-control sel_skills_nudge" id="sel_skills_nudge" style="height:30px;display:none;" multiple="multiple" name="skill_select[]">
							<?php
							if( isset($skills) && !empty($skills) ){
								foreach($skills as $k => $v){
									?>
									<option value="<?php echo $v['value']; ?>"><?php echo htmlentities($v['label']); ?></option>
									<?php
								}
							}
							?>
						</select>
                        <label class="checkbox-nudge">
							<input type="checkbox" id="match_all_skill_checkbox" name="match_all_skill_checkbox" class="" value="0">
							<label class="fancy_label text-black" for="match_all_skill_checkbox">Require All</label>
						</label>
                    </div>
					<div class="form-group">
						<label for="RiskType">To: </label>
						<?php echo $this->Form->input('nudge_user_skill_search', array(
							'options' => $allUsers,
							'class' => 'form-control',
							'id' => 'nudge_user_skill_search',
							'label' => false,
							'div' => false,
							'multiple' => true,
						)); ?>
						<span class="error-message nudge-user-err error text-danger"></span>
					</div>
				</div>
				<div id="subjects_search" class="tab-pane fade">
                    <div class="form-group  nudge-pop-group">
						<select class="form-control sel_subjects_nudge" id="sel_subjects_nudge" style="height:30px;display:none;" multiple="multiple" name="subject_select[]">
							<?php
							if( isset($subjects) && !empty($subjects) ){
								foreach($subjects as $k => $v){
									?>
									<option value="<?php echo $v['value']; ?>"><?php echo htmlentities($v['label']); ?></option>
									<?php
								}
							}
							?>
						</select>
                        <label class="checkbox-nudge">
							<input type="checkbox" id="match_all_subject_checkbox" name="match_all_subject_checkbox" class="" value="0">
							<label class="fancy_label text-black" for="match_all_subject_checkbox">Require All</label>
						</label>
                    </div>
					<div class="form-group">
						<label for="RiskType">To: </label>
						<?php echo $this->Form->input('nudge_user_subject_search', array(
							'options' => $allUsers,
							'class' => 'form-control',
							'id' => 'nudge_user_subject_search',
							'label' => false,
							'div' => false,
							'multiple' => true,
						)); ?>
						<span class="error-message nudge-user-err error text-danger"></span>
					</div>
				</div>
				<div id="domains_search" class="tab-pane fade">
                    <div class="form-group  nudge-pop-group">
						<select class="form-control sel_domains_nudge" id="sel_domains_nudge" style="height:30px;display:none;" multiple="multiple" name="domain_select[]">
							<?php
							if( isset($domains) && !empty($domains) ){
								foreach($domains as $k => $v){
									?>
									<option value="<?php echo $v['value']; ?>"><?php echo htmlentities($v['label']); ?></option>
									<?php
								}
							}
							?>
						</select>
                        <label class="checkbox-nudge">
							<input type="checkbox" id="match_all_domain_checkbox" name="match_all_domain_checkbox" class="" value="0">
							<label class="fancy_label text-black" for="match_all_domain_checkbox">Require All</label>
						</label>
                    </div>
					<div class="form-group">
						<label for="RiskType">To: </label>
						<?php echo $this->Form->input('nudge_user_domain_search', array(
							'options' => $allUsers,
							'class' => 'form-control',
							'id' => 'nudge_user_domain_search',
							'label' => false,
							'div' => false,
							'multiple' => true,
						)); ?>
						<span class="error-message nudge-user-err error text-danger"></span>
					</div>
				</div>
			</div>
		</div>
		<div class="col-sm-12">
			<div class="form-group">
				<label for="RiskType">Subject: </label>
				<input type="text" class="form-control" id="nudge_subject" name="nudge_subject" placeholder="Max chars allowed 50" autocomplete="off">
				<span class="error-message error text-danger"></span>
			</div>
		</div>
		<div class="col-sm-12">
			<div class="form-group">
				<label for="RiskType">Message: </label>
				<textarea class="form-control" id="nudge_message" name="nudge_message" rows="7" style="resize: none;" placeholder="Max chars allowed 250"></textarea>
				<span class="error-message error text-danger"></span>
			</div>
		</div>
		<div class="col-sm-12">
			<div class="form-group">
				<div class="switch">
					<input type="checkbox" class="cmn-toggle cmn-toggle-round" <?php if(isset($type) && !empty($type) && ($type != 'tags' && $type != 'program_team' && $type != 'program_stakeholders')){ ?> checked="checked" <?php } ?> value="1" id="page_link" name="page_link">
					<label for="page_link"></label>
				</div>
				<label class="page_link extra-options">Include link to this page</label>
				<div class="switch">
					<input type="checkbox" class="cmn-toggle cmn-toggle-round" checked="checked" value="1" id="email" name="email">
					<label for="email"></label>
				</div>
				<label class="email extra-options">Send email</label>
			</div>
		</div>
	</div>
</div>

<!-- POPUP MODAL FOOTER -->
<div class="modal-footer">
	 <button type="button"  class="btn btn-success submit-nudge">Send</button>
	 <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
</div>

<script type="text/javascript">
	function arrayDiff(array1, array2) {
	    var newItems = [];
	    $.grep(array2, function(i) {
	        if ($.inArray(i, array1) == -1)
	        {
	            newItems.push(i);
	        }
	    });
	    return newItems;
	}
	$.availableAllUsers = <?php echo json_encode($nudgeUserList); ?>;
	$.availableAllUsersData = <?php echo json_encode(($allUsers)); ?>;
	$.text_search_users = [];
	$.skill_search_users = [];
	$.tag_search_users = [];
    $.subject_search_users = [];
    $.domain_search_users = [];
	$.target_tab = '#text_search';
	$.relatedTarget_tab = '';


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
	                $("#nudge_user_text_search").multiselect('dataprovider', content);
	                $("#nudge_user_tag_search").multiselect('dataprovider', content);
	                $("#nudge_user_skill_search").multiselect('dataprovider', content);
	                $("#nudge_user_subject_search").multiselect('dataprovider', content);
	                $("#nudge_user_domain_search").multiselect('dataprovider', content);
	            }
	         });
		}
		/* PEOPLE PAGE SCRIPT */

		$.sort_data = function(data){
			var temp = [];
			$.each(data, function(index, el) {
				temp.push(el);
			});
			temp.sort(function (a, b) {
				return a.label.localeCompare(b.label);
			});

			return temp;
		}
		$('body').delegate('#nudge_subject', 'keyup focus', function(event){
            var characters = 50;

            event.preventDefault();
            var $error_el = $(this).parent().find('.error');
            if (typeof $.input_char_count !== 'undefined' && $.isFunction($.input_char_count)) {
                $.input_char_count(this, characters, $error_el);
            }
        })
		$('body').delegate('#nudge_message', 'keyup focus', function(event){
            var characters = 250;

            event.preventDefault();
            var $error_el = $(this).parent().find('.error');
            if (typeof $.input_char_count !== 'undefined' && $.isFunction($.input_char_count)) {
                $.input_char_count(this, characters, $error_el);
            }
        })

	    $.nudge_user_text_search = $('#nudge_user_text_search').multiselect({
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
				var sel_users = $('#nudge_user_text_search option:selected');
				var selected = [];
				$(sel_users).each(function(index, brand){
					selected.push($(this).val());
				});
				$.text_search_users = selected;
			},
			onDeselectAll: function() {
				$.text_search_users = [];
			},
			onChange: function(element, checked) {
				var sel_users = $('#nudge_user_text_search option:selected');
				var selected = [];
				$(sel_users).each(function(index, brand){
					selected.push($(this).val());
				});

				$.text_search_users = selected;
			},
			onInitialized: function(element, checked) {
				var sel_users = $('#nudge_user_text_search option:selected');
				var selected = [];
				$(sel_users).each(function(index, brand){
					selected.push($(this).val());
				});

				$.text_search_users = selected;
			}
		});

		$.nudge_user_skill_search = $('#nudge_user_skill_search').multiselect({
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
				var sel_users = $('#nudge_user_skill_search option:selected');
				var selected = [];
				$(sel_users).each(function(index, brand){
					selected.push($(this).val());
				});
				$.skill_search_users = selected;
			},
			onDeselectAll: function() {
				$.skill_search_users = [];
			},
			onChange: function(element, checked) {
				var sel_users = $('#nudge_user_skill_search option:selected');
				var selected = [];
				$(sel_users).each(function(index, brand){
					selected.push($(this).val());
				});

				$.skill_search_users = selected;
			}
		});

		$.nudge_user_tag_search = $('#nudge_user_tag_search').multiselect({
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
				var sel_users = $('#nudge_user_tag_search option:selected');
				var selected = [];
				$(sel_users).each(function(index, brand){
					selected.push($(this).val());
				});
				$.tag_search_users = selected;
			},
			onDeselectAll: function() {
				$.tag_search_users = [];
			},
			onChange: function(element, checked) {
				var sel_users = $('#nudge_user_tag_search option:selected');
				var selected = [];
				$(sel_users).each(function(index, brand){
					selected.push($(this).val());
				});

				$.tag_search_users = selected;
			}
		});

        $.nudge_user_subject_search = $('#nudge_user_subject_search').multiselect({
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
				var sel_users = $('#nudge_user_subject_search option:selected');
				var selected = [];
				$(sel_users).each(function(index, brand){
					selected.push($(this).val());
				});
				$.subject_search_users = selected;
			},
			onDeselectAll: function() {
				$.subject_search_users = [];
			},
			onChange: function(element, checked) {
				var sel_users = $('#nudge_user_subject_search option:selected');
				var selected = [];
				$(sel_users).each(function(index, brand){
					selected.push($(this).val());
				});

				$.subject_search_users = selected;
			}
		});

        $.nudge_user_domain_search = $('#nudge_user_domain_search').multiselect({
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
				var sel_users = $('#nudge_user_domain_search option:selected');
				var selected = [];
				$(sel_users).each(function(index, brand){
					selected.push($(this).val());
				});
				$.domain_search_users = selected;
			},
			onDeselectAll: function() {
				$.domain_search_users = [];
			},
			onChange: function(element, checked) {
				var sel_users = $('#nudge_user_domain_search option:selected');
				var selected = [];
				$(sel_users).each(function(index, brand){
					selected.push($(this).val());
				});

				$.domain_search_users = selected;
			}
		});

		/* SJ code for skill and tags*/
		$('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
			$('.nudge-user-err').html('');

			if($(e.relatedTarget).attr("href") === undefined) {
				$.relatedTarget_tab = $.target_tab;
			} else {
				$.relatedTarget_tab = $(e.relatedTarget).attr("href");
			}
			$.target_tab = $(e.target).attr("href");

		})
		$.sel_skills_nudge = $('#sel_skills_nudge').multiselect({
	    	enableUserIcon: false,
	        buttonClass: 'btn btn-default aqua',
	        buttonWidth: '100%',
	        buttonContainerWidth: '100%',
	        numberDisplayed: 2,
	        maxHeight: '318',
	        checkboxName: 'tags_skills_sel[]',
	        includeSelectAllOption: true,
	        enableFiltering: true,
	        filterPlaceholder: 'Search text...',
	        enableCaseInsensitiveFiltering: true,
	        nonSelectedText: 'Select Skills',
			templates: {
                filterClearBtn: '<span class="input-group-btn"><button class="btn btn-default multiselect-clear-filter tipText" type="button" title="Clear Search"><i class="glyphicon glyphicon-remove-circle"></i></button></span>',
            },
			onSelectAll: function() {
				var sel_users = $('#sel_skills_nudge option:selected');
				var selected = [];
				$(sel_users).each(function(index, brand){
					selected.push([$(this).val()]);
				});
				var selStr = selected.join();
				apply_user_filter('skill', selStr, selected.length);
			},
			onDeselectAll: function() {
				apply_user_filter('skill', '', 0);
			},
			onChange: function(element, checked) {
				var sel_users = $('#sel_skills_nudge option:selected');
				var selected = [];
				$(sel_users).each(function(index, brand){
					selected.push([$(this).val()]);
				});
				var selStr = selected.join();
				apply_user_filter('skill', selStr, selected.length);
			}
		});
		$.sel_tags_nudge = $('#sel_tags_nudge').multiselect({
	    	enableUserIcon: false,
	        buttonClass: 'btn btn-default aqua',
	        buttonWidth: '100%',
	        buttonContainerWidth: '100%',
	        numberDisplayed: 2,
	        maxHeight: '318',
	        checkboxName: 'tags_skills_sel[]',
	        includeSelectAllOption: true,
	        enableFiltering: true,
	        filterPlaceholder: 'Search text...',
	        enableCaseInsensitiveFiltering: true,
	        nonSelectedText: 'Select Tags',
			templates: {
                filterClearBtn: '<span class="input-group-btn"><button class="btn btn-default multiselect-clear-filter tipText" type="button" title="Clear Search"><i class="glyphicon glyphicon-remove-circle"></i></button></span>',
            },
			onSelectAll: function() {
				var sel_users = $('#sel_tags_nudge option:selected');
				var selected = [];
				$(sel_users).each(function(index, brand){
					selected.push([$(this).val()]);
				});
				var selStr = selected.join();
				apply_user_filter('tag', selStr, selected.length);
			},
			onDeselectAll: function() {
				apply_user_filter('tag', '', 0);
			},
			onChange: function(element, checked) {
				var sel_users = $('#sel_tags_nudge option:selected');
				var selected = [];
				$(sel_users).each(function(index, brand){
					selected.push([$(this).val()]);
				});
				var selStr = selected.join();
				apply_user_filter('tag', selStr, selected.length);
			}
		});
        $.sel_subjects_nudge = $('#sel_subjects_nudge').multiselect({
	    	enableUserIcon: false,
	        buttonClass: 'btn btn-default aqua',
	        buttonWidth: '100%',
	        buttonContainerWidth: '100%',
	        numberDisplayed: 2,
	        maxHeight: '318',
	        checkboxName: 'tags_skills_sel[]',
	        includeSelectAllOption: true,
	        enableFiltering: true,
	        filterPlaceholder: 'Search text...',
	        enableCaseInsensitiveFiltering: true,
	        nonSelectedText: 'Select Subjects',
			templates: {
                filterClearBtn: '<span class="input-group-btn"><button class="btn btn-default multiselect-clear-filter tipText" type="button" title="Clear Search"><i class="glyphicon glyphicon-remove-circle"></i></button></span>',
            },
			onSelectAll: function() {
				var sel_users = $('#sel_subjects_nudge option:selected');
				var selected = [];
				$(sel_users).each(function(index, brand){
					selected.push([$(this).val()]);
				});
				var selStr = selected.join();
				apply_user_filter('subject', selStr, selected.length);
			},
			onDeselectAll: function() {
				apply_user_filter('subject', '', 0);
			},
			onChange: function(element, checked) {
				var sel_users = $('#sel_subjects_nudge option:selected');
				var selected = [];
				$(sel_users).each(function(index, brand){
					selected.push([$(this).val()]);
				});
				var selStr = selected.join();
				apply_user_filter('subject', selStr, selected.length);
			}
		});
        $.sel_domains_nudge = $('#sel_domains_nudge').multiselect({
	    	enableUserIcon: false,
	        buttonClass: 'btn btn-default aqua',
	        buttonWidth: '100%',
	        buttonContainerWidth: '100%',
	        numberDisplayed: 2,
	        maxHeight: '318',
	        checkboxName: 'tags_skills_sel[]',
	        includeSelectAllOption: true,
	        enableFiltering: true,
	        filterPlaceholder: 'Search text...',
	        enableCaseInsensitiveFiltering: true,
	        nonSelectedText: 'Select Domains',
			templates: {
                filterClearBtn: '<span class="input-group-btn"><button class="btn btn-default multiselect-clear-filter tipText" type="button" title="Clear Search"><i class="glyphicon glyphicon-remove-circle"></i></button></span>',
            },
			onSelectAll: function() {
				var sel_users = $('#sel_domains_nudge option:selected');
				var selected = [];
				$(sel_users).each(function(index, brand){
					selected.push([$(this).val()]);
				});
				var selStr = selected.join();
				apply_user_filter('domain', selStr, selected.length);
			},
			onDeselectAll: function() {
				apply_user_filter('domain', '', 0);
			},
			onChange: function(element, checked) {
				var sel_users = $('#sel_domains_nudge option:selected');
				var selected = [];
				$(sel_users).each(function(index, brand){
					selected.push([$(this).val()]);
				});
				var selStr = selected.join();
				apply_user_filter('domain', selStr, selected.length);
			}
		});
		$('#match_all_tag_checkbox').on('change', function(){
			var sel_users = $('#sel_tags_nudge option:selected');
			var selected = [];
			$(sel_users).each(function(index, brand){
				selected.push([$(this).val()]);
			});
			var selStr = selected.join();
			if(selStr.length > 0) {
				apply_user_filter('tag', selStr, selected.length);
			}
		});
		$('#match_all_skill_checkbox').on('change', function(){
			var sel_users = $('#sel_skills_nudge option:selected');
			var selected = [];
			$(sel_users).each(function(index, brand){
				selected.push([$(this).val()]);
			});
			var selStr = selected.join();
			if(selStr.length > 0) {
				apply_user_filter('skill', selStr, selected.length);
			}
		})
        $('#match_all_subject_checkbox').on('change', function(){
			var sel_users = $('#sel_subjects_nudge option:selected');
			var selected = [];
			$(sel_users).each(function(index, brand){
				selected.push([$(this).val()]);
			});
			var selStr = selected.join();
			if(selStr.length > 0) {
				apply_user_filter('subject', selStr, selected.length);
			}
		})
        $('#match_all_domain_checkbox').on('change', function(){
			var sel_users = $('#sel_domains_nudge option:selected');
			var selected = [];
			$(sel_users).each(function(index, brand){
				selected.push([$(this).val()]);
			});
			var selStr = selected.join();
			if(selStr.length > 0) {
				apply_user_filter('domain', selStr, selected.length);
			}
		})
		/* SJ code for skill and tags*/
	    $('.submit-nudge').off('click').on('click',function(event) {
	    	event.preventDefault();
	    	var formData = {};

			//var $merged_user = $.text_search_users.concat($.skill_search_users, $.tag_search_users);
			var $merged_user = [];
			if($.target_tab == '#text_search') {
				$merged_user = $.text_search_users;
			} else if($.target_tab == '#skills_search') {
				$merged_user = $.skill_search_users;
			} else if($.target_tab == '#tag_search') {
				$merged_user = $.tag_search_users;
			} else if($.target_tab == '#subjects_search') {
				$merged_user = $.subject_search_users;
			} else if($.target_tab == '#domains_search') {
				$merged_user = $.domain_search_users;
			}

			var $nudgeUserUnique = $merged_user.filter(function(item, pos){
				return $merged_user.indexOf(item)=== pos;
			});

			var $nudge_user = $('#nudge_user'),
	    		$nudge_subject = $('#nudge_subject'),
	    		$nudge_message = $('#nudge_message');

    		/*$('.error').html('');
    		if($nudge_user.val() == null) {
    			$nudge_user.parents('.form-group').find('.error').html('One or more Users is required');
    			return;
    		}
    		else{
    			formData['user'] = $nudge_user.val();
    		}*/
			$('.error').html('');
			if($nudgeUserUnique.length <= 0) {
				$('.nudge-user-err').html('One or more People are required');
    			return;
			} else {
				formData['user'] = $nudgeUserUnique;
			}

    		if($nudge_subject.val() == '' || $nudge_subject.val() === undefined) {
    			$nudge_subject.parents('.form-group').find('.error').html('Subject is required');
    			$nudge_subject.focus();
    			return;
    		}
    		else{
    			formData['subject'] = $nudge_subject.val();
    		}

    		if($nudge_message.val() == '' || $nudge_message.val() === undefined) {
    			$nudge_message.parents('.form-group').find('.error').html('Message is required');
    			$nudge_message.focus();
    			return;
    		}
    		else{
    			formData['message'] = $nudge_message.val();
    		}

    		formData['page_link'] = 0;
    		formData['email'] = 0;
    		if($('#page_link').prop('checked')){
    			formData['page_link'] = 1;
    		}
    		if($('#email').prop('checked')){
    			formData['email'] = 1;
    		}

    		$('.hidden_vals').each(function(index, el) {
    			var name = $(this).attr('name');
    				id = $(this).attr('id'),
    				val = $(this).val();
    				formData[id] = val;
				if(val != '' && val !== undefined){
				}
    		});

    		$(this).addClass('disabled');
			$.ajax({
				url: $js_config.base_url + 'boards/save_nudge',
				type: 'POST',
				dataType: 'json',
				data: formData,
				success: function(response) {
					$.nudge_list_flag = true;
					if(response.success && response.content){
						// send web notification
						// response.content.socket.notification.date_time = new Date(response.content.socket.notification.date_time)
						$.socket.emit('socket:notification', response.content.socket, function(userdata){
							if(jQuery.inArray($js_config.USER.id, response.content.socket.received_users) !== -1){
								$.create_notification(userdata);
							}
						});
					}
					$('#modal_nudge').modal('hide');
					$(this).removeClass('disabled');
				}
			});
	    });
	})
	function apply_user_filter(type, selected, selLength) {
		var is_match_all = 0;
		if(type == 'tag') {
			is_match_all = ($('#match_all_tag_checkbox').is(":checked")) ? 1 : 0;
		}
		if(type == 'skill') {
			is_match_all = ($('#match_all_skill_checkbox').is(":checked")) ? 1 : 0;
		}
		if(type == 'subject') {
			is_match_all = ($('#match_all_subject_checkbox').is(":checked")) ? 1 : 0;
		}
		if(type == 'domain') {
			is_match_all = ($('#match_all_domain_checkbox').is(":checked")) ? 1 : 0;
		}
		var project_id = <?php echo (is_null($project_id) ? 0 : $project_id);?>;

		if(selLength > 0) {
			$.ajax({
				url: $js_config.base_url + 'tags/apply_user_filter',
				type: 'POST',
				dataType: 'json',
				data: { type: type, selected: selected, is_match_all: is_match_all, project_id: project_id, all_users: $.availableAllUsers.join() },
				success: function(response) {
					if(response.success){
						if(type == 'tag') {
							$.nudge_user_tag_search.multiselect('deselectAll', false);
                        } else if(type == 'skill') {
                            $.nudge_user_skill_search.multiselect('deselectAll', false);
                        } else if(type == 'subject') {
                            $.nudge_user_subject_search.multiselect('deselectAll', false);
                        } else if(type == 'domain') {
                            $.nudge_user_domain_search.multiselect('deselectAll', false);
                        }
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
                            queryArr = $.sort_data(queryArr);
                        }
                        if(type == 'tag') {
							$.nudge_user_tag_search.multiselect('dataprovider', queryArr);
							$.tag_search_users = [];
						} else if(type == 'skill') {
                            $.nudge_user_skill_search.multiselect('dataprovider', queryArr);
							$.skill_search_users = [];
                        } else if(type == 'subject') {
                            $.nudge_user_subject_search.multiselect('dataprovider', queryArr);
							$.subject_search_users = [];
                        } else if(type == 'domain') {
                            $.nudge_user_domain_search.multiselect('dataprovider', queryArr);
							$.domain_search_users = [];
                        }
					}

				}
			});
		} else {
			if(type == 'tag') {
				$.nudge_user_tag_search.multiselect('deselectAll', false);
            } else if(type == 'skill') {
                $.nudge_user_skill_search.multiselect('deselectAll', false);
            } else if(type == 'subject') {
                $.nudge_user_subject_search.multiselect('deselectAll', false);
            } else if(type == 'domain') {
                $.nudge_user_domain_search.multiselect('deselectAll', false);
            }
            var queryArr = [];
            if($.availableAllUsersData) {
                $.each($.availableAllUsersData, function(index, el) {
                    var pieces = {
                        "label" :el, "value" :index
                    };
                    queryArr.push( pieces );
                });
                queryArr = $.sort_data(queryArr);
            }
            if(type == 'tag') {
				$.nudge_user_tag_search.multiselect('dataprovider', queryArr);
				$.tag_search_users = [];
			} else if(type == 'skill') {
                $.nudge_user_skill_search.multiselect('dataprovider', queryArr);
				$.skill_search_users = [];
            } else if(type == 'subject') {
                $.nudge_user_subject_search.multiselect('dataprovider', queryArr);
				$.subject_search_users = [];
            } else if(type == 'domain') {
                $.nudge_user_domain_search.multiselect('dataprovider', queryArr);
				$.domain_search_users = [];
            }
		}
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