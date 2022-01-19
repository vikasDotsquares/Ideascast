<style type="text/css">
	.multiselect-container.dropdown-menu li:not(.multiselect-group) a label.checkbox {
	    padding: 5px 20px 5px 40px !important;
	}
</style>
<?php
// pr($type);
// pr($program_id);
// pr($project_id);
// pr($workspace_id);
// pr($task_id);
// pr($risk_id);
// pr($todo_id);
$allUsers = $users = [];
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
else if($type == 'workspace'){
	$users = $this->ViewModel->workspaceUsersFromUserPermission($workspace_id);
}
else if($type == 'task'){
	$users = $this->ViewModel->elementUsersFromUserPermission($task_id);
}
else if($type == 'profile'){
	$allUsers[$user_id] = $this->common->userFullname($user_id);
}
else {
	$users = $this->ViewModel->projectPeople($project_id);
}

if(isset($users) && !empty($users)){
	foreach ($users as $key => $value) {
		$allUsers[$value['user_permissions']['user_id']] = $value[0]['fullname'];
	}
}
asort($allUsers);
echo $this->Html->css('projects/bs-selectbox/bootstrap-multiselect');
echo $this->Html->script('projects/plugins/selectbox/bootstrap-multiselect', array('inline' => true));

echo $this->Html->css('projects/round-input') ?>
<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	<h3 class="modal-title" id="createModelLabel">Nudge</h3>

</div>

<!-- POPUP MODAL BODY -->
<div class="modal-body elements-list popup-select-icon">
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
		<div class="col-sm-12">
			<div class="form-group">
				<label for="RiskType">To: </label>
				<?php if($type == 'profile'){?>
					<?php echo $this->Form->input('nudge_user_text', array(
						'class' => 'form-control',
						'id' => 'nudge_user_text',
						'label' => false,
						'div' => false,
						'type' => 'text',
						'value' => $allUsers[$user_id],
						'readonly' => true
					)); ?>
					<?php echo $this->Form->input('nudge_user', array(
						'id' => 'nudge_user_id',
						'label' => false,
						'div' => false,
						'type' => 'hidden',
						'value' => $user_id,
					)); ?>
				<?php  }else {?>
					<?php echo $this->Form->input('nudge_user', array(
						'options' => $allUsers,
						'class' => 'form-control',
						'id' => 'nudge_user',
						'label' => false,
						'div' => false,
						'multiple' => true,
					)); ?>
				<?php }?>
				<span class="error-message error text-danger"></span>
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
				<textarea class="form-control" id="nudge_message" name="nudge_message" rows="7" placeholder="Max chars allowed 250"></textarea>
				<span class="error-message error text-danger"></span>
			</div>
		</div>
		<div class="col-sm-12">
			<div class="form-group">
				<?php if($type != 'profile'){?>
				<div class="switch">
					<input type="checkbox" class="cmn-toggle cmn-toggle-round" checked="checked" value="1" id="page_link" name="page_link">
					<label for="page_link"></label>
				</div>
				<label class="page_link extra-options">Include link to this page</label>
				<?php }?>
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
	$(function(){
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

		// USER'S MULTISELECT BOX INITIALIZATION
	    $.nudge_user = $('#nudge_user').multiselect({
	    	enableUserIcon: false,
	        buttonClass: 'btn btn-default aqua',
	        buttonWidth: '100%',
	        buttonContainerWidth: '100%',
	        numberDisplayed: 2,
	        maxHeight: '318',
	        checkboxName: 'nudge_users[]',
	        includeSelectAllOption: true,
	        enableFiltering: true,
	        filterPlaceholder: 'Search User',
	        enableCaseInsensitiveFiltering: true,
	        nonSelectedText: 'Select Users',
	    });

	    $('.submit-nudge').click(function(event) {
	    	event.preventDefault();
	    	var formData = {};
			<?php if($type == 'profile'){?>
				var $nudge_user = $('#nudge_user_id');
			<?php }else{?>
				var $nudge_user = $('#nudge_user');
			<?php }?>
	    	var $nudge_subject = $('#nudge_subject'),
	    		$nudge_message = $('#nudge_message');

    		$('.error').html('');
    		if($nudge_user.val() == null) {
    			$nudge_user.parents('.form-group').find('.error').html('One or more Users is required');
    			return;
    		}
    		else{
    			formData['user'] = $nudge_user.val();
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