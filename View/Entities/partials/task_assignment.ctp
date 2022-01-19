<?php
echo $this->Html->css('projects/bs-selectbox/bootstrap-multiselect');
echo $this->Html->script('projects/plugins/selectbox/bootstrap-multiselect', array('inline' => true));
?>

<style type="text/css">
	label.radio input {
		/*opacity: 1 !important;*/
	}
	.nav li.disabled-li a {
	    cursor: not-allowed;
	    color: #aaa;
	    /*text-decoration: line-through;*/
	}
	.nav li.disabled-li a:focus, .nav li.disabled-li a:hover {
	    background-color: transparent;
	    color: #aaa;
	}
	.multiselect-container > li > a > label {
	    margin: 0;
	    height: 100%;
	    cursor: pointer;
	    font-weight: 400;
	    padding: 5px 20px 5px 40px;
	    display: block;
	}
	.multiselect-container > li > a > label.username.radio {
	    padding: 5px 20px 5px 10px;
	}
	.invalid {
	    color: #c00;
	    font-size: 25px;
	    text-align: center;
	    text-transform: uppercase;
	    display: block;
	    padding-bottom: 15px;
	}
</style>

<?php
if(isset($element_detail) && !empty($element_detail)) {
	$element_detail = $element_detail['Element'];
}
/* pr($element_detail);
pr($element_assigned); */

$ele_signoff = false;
$pointer_event = '';
if( isset($element_detail) && !empty($element_detail['sign_off']) && $element_detail['sign_off'] > 0 ){
	$ele_signoff = true;
	$pointer_event = 'signoffpointer';
}

$creator = $receiver = $openMySelf = $openSendTo = $openRecieved = false;
$creatorID = $receiverID = $assignedToMe = $receiverFromAnother = null;
$assign_class = 'on-close';
if($element_assigned) {

	$creatorID = $element_assigned['ElementAssignment']['created_by'];
	$receiverID = $element_assigned['ElementAssignment']['assigned_to'];

	$creator_detail = get_user_data($element_assigned['ElementAssignment']['created_by']);
	$creator_name = (isset($creator_detail['UserDetail']['full_name']) && !empty($creator_detail['UserDetail']['full_name'])) ? $creator_detail['UserDetail']['full_name'] : 'N/A';

	$receiver_detail = get_user_data($element_assigned['ElementAssignment']['assigned_to']);
	$receiver_name = (isset($receiver_detail['UserDetail']['full_name']) && !empty($receiver_detail['UserDetail']['full_name']) )? $receiver_detail['UserDetail']['full_name'] : 'N/A';


	$creator = ($element_assigned['ElementAssignment']['created_by'] == $this->Session->read('Auth.User.id')) ? true : false;
	$receiver = ($creatorID == $receiverID) ? true : false;
	$assignedToMe = ($element_assigned['ElementAssignment']['assigned_to'] == $this->Session->read('Auth.User.id')) ? true : false;
	$receiverFromAnother =  (($element_assigned['ElementAssignment']['assigned_to'] == $this->Session->read('Auth.User.id')) && $element_assigned['ElementAssignment']['created_by'] != $this->Session->read('Auth.User.id') )? true : false;


	if($creator && $receiver){
		$openRecieved = true;
	}
	else if($creator && !$receiver){
		$openMySelf = false;
		$openSendTo = true;
	}
	else if(!$creator && $assignedToMe){
		$openMySelf = false;
		$openSendTo = false;
		$openRecieved = true;
	}

	if($element_assigned['ElementAssignment']['reaction'] == 1) {
		$assign_class = 'on-check';
	}
	else if($element_assigned['ElementAssignment']['reaction'] == 2) {
		$assign_class = 'on-checkred';
	}
	else if($element_assigned['ElementAssignment']['reaction'] == 3) {
		$assign_class = 'on-stop';
	}
}

	$invalid = true;
	if($creator && $receiver){
		$invalid = false;
	}
	if((!$element_assigned || ($creator)) && (!$receiverFromAnother) ){
		if(!$receiver){
			$invalid = false;
		}
	}
	if(!$creator && $assignedToMe){
		$invalid = false;
	}

	$assignee_name = 'None Assigned';
	if(isset($receiver_detail) && !empty($receiver_detail)) {
		$assignee_name = ucfirst($receiver_detail['UserDetail']['first_name']) . ' ' ;
		$assignee_name .= (!empty($receiver_detail['UserDetail']['last_name'])) ? ucwords($receiver_detail['UserDetail']['last_name']) : '';
	}
	// pr($this->Session->read('Auth'));
 ?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h3 class="modal-title" id="createModelLabel">Task Assignment</h3>
</div>
<div class="modal-body task-engaged-modal assignment-popup allpopuptabs">

	<?php if($element_assigned){ ?>
	<input type="hidden" name="id" value="<?php echo $element_assigned['ElementAssignment']['id']; ?>">
	<?php }  ?>
	<input type="hidden" name="element_id" value="<?php echo $element_detail['id']; ?>">
	<input type="hidden" name="created_by" value="<?php echo $this->Session->read('Auth.User.id'); ?>">

        <ul class="nav nav-tabs" id="assignment_tab">
            <!--<li <?php if($creator && $receiver){ ?> class="active" <?php }else{ ?>  class="disabled-li" <?php } ?>>
                <a id="myself_tab"  <?php if($creator && $receiver){ ?> class="active" data-toggle="tab" aria-expanded="true" <?php } ?> href="#myself" >Myself</a>
            </li>-->
            <li <?php if( (  (!$element_assigned || ($creator)) && (!$receiverFromAnother)) ||   $element_assigned['ElementAssignment']['reaction'] == 3){ if(!$receiver || $element_assigned['ElementAssignment']['reaction'] == 3){  ?> class="active" <?php } }else{ ?>  class="disabled-li" <?php } ?>>
                <a id="sendto_tab" href="#sendto" <?php if(((!$element_assigned || ($creator)) && (!$receiverFromAnother)) || $element_assigned['ElementAssignment']['reaction'] == 3 ){ ?> data-toggle="tab" aria-expanded="false" <?php } ?>>Assign To <?php /* if(!$element_assigned){ ?>(0)<?php }elseif($creator){ ?>(1)<?php } */ ?></a>
            </li>




            <li <?php if(( (!$creator && $assignedToMe) || ($creator && $receiver) )  &&   $element_assigned['ElementAssignment']['reaction'] != 3)   { ?> class="active" <?php }else{ ?>  class="disabled-li" <?php } ?>>
                <a id="received_tab" href="#receivedform" <?php if(!$creator && $assignedToMe && $element_assigned['ElementAssignment']['reaction'] != 3){ ?> data-toggle="tab" aria-expanded="false"<?php } ?>   <?php if($creator && $receiver && $element_assigned['ElementAssignment']['reaction'] != 3){ ?>  data-toggle="tab" aria-expanded="false"<?php } ?>>Respond To <?php if(!$creator && $assignedToMe && $element_assigned['ElementAssignment']['reaction'] != 3){ ?>(<?php echo $creator_name; ?>)<?php } ?></a>
            </li>
        </ul>

    <div class="tab-content <?php echo $pointer_event;?>">
        <div  class="tab-pane fade <?php if($openMySelf){echo 'active in'; } ?>" id="myself">
            <div class="taskname">
			
                <label>Task Name:</label> <?php echo strip_tags($element_detail['title']); ?></div>
            <div class="taskname">
                <div class="start-sec"><span class="deta-title">Start Date:</span> <span class="deta"><?php echo date('d M, Y', strtotime($element_detail['start_date'])); ?></span>
                </div>
                <div class="start-sec"><span class="deta-title">End Date:</span> <span class="deta"><?php
				 echo date('d M, Y', strtotime($element_detail['end_date'])); ?></span>
                </div>
            </div>
            <div class="taskname">
                <div class="radio radio-warning">
                    <input id="myself_ScheduleAccepted" name="myself_reaction" class="fancy_input" value="1" type="radio" <?php if($element_assigned){if($element_assigned['ElementAssignment']['reaction'] == 1) { ?> checked <?php }} ?>>
                    <label class="fancy_labels" for="myself_ScheduleAccepted">Accept Schedule</label>
                </div>
                <div class="radio radio-warning">
                    <input id="myself_ScheduleNotAccepted" name="myself_reaction" class="fancy_input" value="2" type="radio"<?php if($element_assigned){if($element_assigned['ElementAssignment']['reaction'] == 2) { ?> checked <?php }} ?>>
                    <label class="fancy_labels" for="myself_ScheduleNotAccepted">Do Not Accept Schedule, But Begin Work</label>
                </div>
                <div class="radio radio-warning">
                    <input id="myself_Disengage" name="myself_reaction" class="fancy_input" value="3" type="radio"<?php if($element_assigned){if($element_assigned['ElementAssignment']['reaction'] == 3) { ?> checked <?php }} ?>>
                    <label class="fancy_labels" for="myself_Disengage">Disengage</label>
                </div>
            </div>
            <!--<div class="it-buttton <?php echo $assign_class; ?>">Engagement Status:<span class="on-button" style="" >I'm On it</span>
            </div>-->
			<?php if($element_assigned && $element_assigned['ElementAssignment']['reaction'] != 3){ ?>
			<div class="it-butttons ">Assigned By: <?php echo $creator_name; ?></div>
			<?php } ?>
        </div>
        <div class="tab-pane fade <?php if( ($openSendTo || !$element_assigned) || $element_assigned['ElementAssignment']['reaction'] == 3 ){echo 'active in'; } ?>" id="sendto">
            <div class="taskname">
                <label>Task Name:</label> <?php echo strip_tags($element_detail['title']); ?></div>
            <div class="taskname">
				<?php if( isset($element_detail['date_constraints']) && $element_detail['date_constraints'] == 1 ){?>
					<div class="start-sec"><span class="deta-title">Start Date:</span> <span class="deta"><?php echo date('d M, Y', strtotime($element_detail['start_date'])); ?></span>
					</div>
					<div class="start-sec"><span class="deta-title">End Date:</span> <span class="deta"><?php
					 echo date('d M, Y', strtotime($element_detail['end_date']));
					?></span></div>
				<?php } else { ?>
					<div class="start-sec"><span class="deta">No Schedule</span>
					</div>
					
			<?php } ?>
                
            </div>
                <?php
                $element_participants = $this->ViewModel->element_participants($element_detail['id'],true);
				$elm_users = [];
                if(isset($element_participants) && !empty($element_participants)) {
					if (isset($element_participants['participantsOwners']) && !empty($element_participants['participantsOwners'])) {
							$elm_users = array_merge($elm_users, $element_participants['participantsOwners']);
					}

					if (isset($element_participants['participantsGpOwner']) && !empty($element_participants['participantsGpOwner'])) {
						$elm_users = array_merge($elm_users, $element_participants['participantsGpOwner']);
					}

					if (isset($element_participants['sharers']) && !empty($element_participants['sharers'])) {
						$elm_users = array_merge($elm_users, $element_participants['sharers']);
					}

					if (isset($elm_users) && !empty($elm_users)) {
						$elm_users = array_unique($elm_users);
					}
				}


				$el_project_id = element_project($element_detail['id']);

				$grp_id = $this->Group->GroupIDbyUserID($el_project_id, $this->Session->read('Auth.User.id'));
				if (isset($grp_id) && !empty($grp_id)) {
					$pr_permission = $this->Group->group_permission_details($el_project_id, $grp_id);
				}
				else{
					$grp_data = $this->Group->ProjectGroupDetail(null, $el_project_id, $this->Session->read('Auth.User.id'));
					if (isset($grp_data) && !empty($grp_data)) {
						$grp_id = $grp_data['ProjectGroup']['id'];
						$pr_permission = $this->Group->group_permission_details($el_project_id, $grp_id);
					}
				}

				$grp_sharer_users = [];

				if ( (isset($pr_permission['ProjectPermission']['project_level']) && $pr_permission['ProjectPermission']['project_level'] != 1)) {
					if (isset($grp_id) && !empty($grp_id)) {
						$grp_sharer_users = $this->Group->group_users($grp_id, true);
						$elm_users = array_merge($elm_users, $grp_sharer_users);
					}
				}

                $selectedUser = null;
                if($element_assigned) {
                	$selectedUser[] = $element_assigned['ElementAssignment']['assigned_to'];
                }
                $user_select = null;
                if(isset($elm_users) && !empty($elm_users)) {
					foreach($elm_users as $ou => $ov) {
						$userDetail = $this->ViewModel->get_user_data( $ov );
						if(isset($userDetail) && !empty($userDetail)) {
							$user_found = true;
							$user_select[$ov] = $userDetail['UserDetail']['full_name'];
						}
					}
				}

				if(isset($element_assigned)){
					if($element_assigned['ElementAssignment']['reaction'] == 3) {
						$selectedUser = null;
					}
				}
				asort($user_select);

                ?>
            <div class="taskname assigntosec">
                <leble>Assign To:</leble>
                <?php echo $this->Form->select('user_select', $user_select, array('escape' => false, 'empty' => false, 'class' => 'form-control aqua', 'id' => 'user_select', 'default' => $selectedUser)); ?>
                <?php if($ele_signoff == false) {?>
					<button class="btn btn-danger deselect-users" type="button"><i class="fa fa-times" aria-hidden="true"></i></button>
				<?php } else {?>
					<button class="btn btn-danger disabled" type="button"><i class="fa fa-times" aria-hidden="true"></i></button>
				<?php } ?>
            </div>
            <?php if($element_assigned && $element_assigned['ElementAssignment']['reaction'] != 3){ ?>
            <!--<div class="it-buttton <?php echo $assign_class; ?>">Engagement Status:
                <span class="on-button">I'm On it</span>
            </div>-->

			<div class="it-butttons ">Assigned By: <?php echo $creator_name; ?></div>

            <?php } ?>
        </div>
        <div class="tab-pane fade <?php if($openRecieved &&  $element_assigned['ElementAssignment']['reaction'] != 3){echo 'active in'; } ?>" id="receivedform">
            <div class="taskname">
                <label>Task Name:</label> <?php echo strip_tags($element_detail['title']); ?>
            </div>
            <div class="taskname">
				<?php if( isset($element_detail['date_constraints']) && $element_detail['date_constraints'] == 1 ){?>
					<div class="start-sec"><span class="deta-title">Start Date:</span> <span class="deta"><?php echo date('d M, Y', strtotime($element_detail['start_date'])); ?></span>
					</div>
					<div class="start-sec"><span class="deta-title">End Date:</span> <span class="deta"><?php echo date('d M, Y', strtotime($element_detail['end_date'])); ?></span>
					</div>
				<?php } else { ?>
					<div class="start-sec"> <span class="deta">No Schedule</span>
					</div>
					 
					 
				<?php } ?>
            </div>
            <div class="taskname">
                <div class="radio radio-warning">
                    <input id="received_ScheduleAccepted" name="received_reaction" class="fancy_input" value="1" type="radio"<?php if($element_assigned){if($element_assigned['ElementAssignment']['reaction'] == 1) { ?> checked <?php }} ?>>
                    <label class="fancy_labels" for="received_ScheduleAccepted">Accept Schedule</label>
                </div>
                <div class="radio radio-warning">
                    <input id="received_ScheduleNotAccepted" name="received_reaction" class="fancy_input" value="2" type="radio"<?php if($element_assigned){if($element_assigned['ElementAssignment']['reaction'] == 2) { ?> checked <?php }} ?>>
                    <label class="fancy_labels" for="received_ScheduleNotAccepted">Do Not Accept Schedule, But Begin Work</label>
                </div>
                <div class="radio radio-warning">
                    <input id="received_Disengage" name="received_reaction" class="fancy_input" value="3" type="radio"<?php if($element_assigned){if($element_assigned['ElementAssignment']['reaction'] == 3) { ?> checked <?php }} ?>>
                    <label class="fancy_labels" for="received_Disengage">Disengage</label>
                </div>
            </div>
            <!--<div class="it-buttton <?php echo $assign_class; ?>">Engagement Status:
                <span class="on-button">I'm On it</span>
            </div>-->
			<?php if($element_assigned && $element_assigned['ElementAssignment']['reaction'] != 3){ ?>
			<div class="it-butttons ">Assigned By: <?php echo $creator_name; ?></div>
			<?php } ?>
        </div>
    </div>
    <?php if($invalid){ ?>
    	<div class="invalid"> Invalid Access. </div>
    <?php } ?>
</div>
<?php if($ele_signoff == false) {?>
<div class="modal-footer">
    <button type="button" class="btn btn-success submit_btn <?php if($invalid){ ?>disabled<?php } ?>" > Save</button>
    <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
</div>
<?php } else { ?>
<div class="modal-footer">
	 <button type="button" class="btn btn-success disabled"> Save</button>
    <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
</div>
<?php } ?>
<script type="text/javascript">
$(function() {
	<?php if( ($element_assigned && (isset($element_assigned['ElementAssignment']['assigned_to']) && !empty($element_assigned['ElementAssignment']['assigned_to']))) && $element_assigned['ElementAssignment']['reaction'] != 3){ ?>
	    // USER'S MULTISELECT BOX INITIALIZATION
	    $.user_select = $('#user_select').multiselect({
	        multiple: false,
	        buttonClass: 'btn btn-default aqua',
	        buttonWidth: '100%',
	        buttonContainerWidth: '100%',
	        maxHeight: '318',
	        checkboxName: 'users[]',
	        enableFiltering: true,
	        filterPlaceholder: 'Search User',
	        enableCaseInsensitiveFiltering: true,
	        enableUserIcon: false,
	        nonSelectedText: 'All Users',
	        nonSelectedText: 'None selected',
	        onChange: function(selected, flag) {
	        	$("#sendto_tab").text('Assign To');
	        	if(selected.length > 0){
	        		$("#sendto_tab").text('Assign To');
	        	}
	        },
	    });
    <?php }else{ ?>
	    // USER'S MULTISELECT BOX INITIALIZATION
	    $.user_select = $('#user_select').val('').multiselect({
	        multiple: false,
	        buttonClass: 'btn btn-default aqua',
	        buttonWidth: '100%',
	        buttonContainerWidth: '100%',
	        maxHeight: '318',
	        checkboxName: 'users[]',
	        enableFiltering: true,
	        filterPlaceholder: 'Search User',
	        enableCaseInsensitiveFiltering: true,
	        enableUserIcon: false,
	        nonSelectedText: 'All Users',
	        nonSelectedText: 'None selected',
	        onChange: function(selected, flag) {
	        	$("#sendto_tab").text('Assign To');
	        	if(selected.length > 0){
	        		$("#sendto_tab").text('Assign To');
	        	}
	        },
	    });
	<?php } ?>

	$.save_element_assignment = function(data) {
		var dfd = new $.Deferred();
		$.ajax({
			url: $js_config.base_url + 'entities/save_assignment',
			type: 'POST',
			// context: this,
			dataType: 'JSON',
			data: data,
			success: function(response) {
				if(response.success) {
					$.assignment_submited = true;
					if(response.content){
						// send web notification
						$.socket.emit('socket:notification', response.content.socket, function(userdata){});
					}
					if(response.removed){
						setTimeout(function(){
							// send web notification
							$.socket.emit('socket:notification', response.removed, function(userdata){});
						}, 500)
					}
					dfd.resolve('project risk types updated');
					$('.mytasksCount .user_projects_total').text(response.myTcount);
				}
			}
		})

		return dfd.promise();
	}

    $('.deselect-users').on('click', function(event) {
    	event.preventDefault();
    	$('#user_select').val('');
    	$("#sendto_tab").text('Assign To');
        $('#user_select').multiselect('refresh');
        var data = {};

        data = {
			'assigned_to': '',
			'created_by': $('[name=created_by]').val(),
			'element_id': $('[name=element_id]').val(),
			'type': 'sendto',
		};
		if($('[name=id]').length > 0) {
			data['id'] = $('[name=id]').val();
		}

		$.save_element_assignment(data).done(function() {
            $.save_assignment = $('[name=element_id]').val();
            $('#modal_task_assignment').modal('hide');
        });
    });


    $.activeTab = $('#assignment_tab li.active a')[0];
	
    $.activeTabs = $('#assignment_tab li.active a').attr('href') ;
	
	
	if( $.activeTabs == "#receivedform"){
		$('.submit_btn').text('Respond');
	}else if( $.activeTabs == "#sendto"){
		$('.submit_btn').text('Assign');
	}
	
	$('#assignment_tab li a').click(function(){
		 
		 $.activeTabsH = $(this).attr('href') ;
		 if( $.activeTabsH == "#receivedform"){
			$('.submit_btn').text('Respond');
		 }else if( $.activeTabsH == "#sendto"){
			$('.submit_btn').text('Assign');
		 }
	})
	
	
	 
	$('#assignment_tab a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
	  	$.activeTab = e.target;
	 
	})
	

	$('.submit_btn').on('click', function(event){
		event.preventDefault();
		var $this = $(this);
		$this.prop("disabled",true);
			data = {};
		if($($.activeTab).is('#myself_tab')) {
			data = {
				'element_id': $('[name=element_id]').val(),
				'id': $('[name=id]').val(),
				'reaction': $('[name=myself_reaction]:checked').val(),
				'type': 'myself',
			};
		}
		else if($($.activeTab).is('#sendto_tab')) {
			data = {
				'assigned_to': $('#user_select').val(),
				'created_by': $('[name=created_by]').val(),
				'element_id': $('[name=element_id]').val(),
				'type': 'sendto',
			};
			if($('[name=id]').length > 0) {
				data['id'] = $('[name=id]').val();
			}
		}
		else if($($.activeTab).is('#received_tab')) {
			data = {
				'element_id': $('[name=element_id]').val(),
				'id': $('[name=id]').val(),
				'reaction': $('[name=received_reaction]:checked').val(),
				'type': 'received',
			};
		}

		if(data.reaction == 3 && ($js_config.currentUserRole =='Sharer' || $js_config.currentUserRole =='Group Sharer') ){
			$('.confidence-col').remove();
		}

		$.save_element_assignment(data).done(function() {
            $.save_assignment = $('[name=element_id]').val();
			$('#modal_task_assignment').modal('hide');
			$this.prop("disabled",false);
        });
	})

    $.reaction_icon = function(value){
    	var value = value || 0;
    	var classname = 'on-close';
    	if(value == 1) {
    		classname = 'on-check';
    	}
    	else if(value == 2) {
    		classname = 'on-checkred';
    	}
    	else if(value == 3) {
    		classname = 'on-stop';
    	}
    	return classname;
    }

    $('[name=myself_reaction]').on('change', function(event) {
    	event.preventDefault();
/*     	var value = $(this).val(),
    		class_name = $.reaction_icon(value),
    		$btn_on_it = $(this).parents('.tab-pane:first').find('.it-buttton'),
    		cls = $btn_on_it.attr('class');

    	var foundClass = (cls.match(/(^|\s)on-\S+/g) || []).join('');
        if (foundClass != '') {
            $btn_on_it.removeClass(foundClass);
        }
        $btn_on_it.addClass(class_name); */
    });

    $('[name=received_reaction]').on('change', function(event) {
    	event.preventDefault();
/*     	var value = $(this).val(),
    		class_name = $.reaction_icon(value),
    		$btn_on_it = $(this).parents('.tab-pane:first').find('.it-buttton'),
    		cls = $btn_on_it.attr('class');

    	var foundClass = (cls.match(/(^|\s)on-\S+/g) || []).join('');
        if (foundClass != '') {
            $btn_on_it.removeClass(foundClass);
        }
        $btn_on_it.addClass(class_name); */
    });

    $('.disabled-li a').on('click', function(event) {
    	event.preventDefault();
    });

    <?php if($invalid){ ?>
    	$.save_assignment = $('[name=element_id]').val();
    <?php } ?>

})
</script>

<script>
$(function(){

$('.cost-tooltip').tooltip({
	'placement': 'top',
	'container': 'body',
	'html': true
})

})
</script>