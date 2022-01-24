<?php

echo $this->Html->css('projects/bs-selectbox/bootstrap-multiselect');
echo $this->Html->script('projects/plugins/selectbox/bootstrap-multiselect', array('inline' => true));
?>

<?php
$data = $detail['Feedback'];
$users = $detail['FeedbackUser'];
// echo $this->Html->css('easyui');
// echo $this->Html->script('jquery.easyui.min', array( ));

$elementStatus = $this->Common->element_status($data['element_id']);
$disabledSignOff = '';
$disabledfeedback = '';

if(isset($data['sign_off']) && !empty($data['sign_off']) && $data['sign_off'] > 0){
	$disabledSignOff = 'disabled';
}


if(isset($data['end_date']) && !empty($data['end_date']) && $data['end_date'] < date('Y-m-d 00:00:00')){
	$disabledfeedback = 'disabled';
	$disabledSignOff = 'disabled';
}

if( $elementStatus == 'Overdue' || $elementStatus == 'Completed' ){
	$disabledSignOff = 'disabled';
}



/* if (isset($this->data['Element']['sign_off']) && $this->data['Element']['sign_off'] == 1) {
	$message = 'Element has been Sign off.';
	$class_disabled = 'element_warning disabled';
	$class_d = 'element_warning disable';
} else if (!empty($mindate_elm) && strtotime($mindate_elm) > strtotime($cur_date) && strtotime($maxdate_elm) > strtotime($cur_date)) {

	$message = $messageVar . ' start date not reached yet.';
	$class_disabled = 'element_warning disabled';
	$class_d = 'element_warning disable';
} */




?>

<!------------ Update Feedback Users ------------>
			<div class="panel-heading bg-green">Invite Users</div>
			 <?php
				$id = $data['id'];
				echo $this->Form->create('Feedback', array('url' => array('controller' => 'entities', 'action' => 'feedback_update_users', $id), 'class' => 'formupdateFeedbackUsers', 'style' => '', 'enctype' => 'multipart/form-data'));
				?>

				<input type="hidden" name="data[FeedbackUser][element_id]" class="form-control" value="<?php echo $data['element_id']; ?>" />
				<input type="hidden" name="data[FeedbackUser][feedback_id]" id="FeedbackUserfeedback_id" class="form-control feedback_id" value="<?php if (isset($data['id'])) echo $data['id']; ?>" />
				<div class="form-group clearfix" style="margin: 0px;">

			<div class="panel-body">
				<div class="col-sm-7 col-md-12 col-lg-7" style="text-align:center; ">
					<div class="titleuser"><b>Participants</b></div>
					<div class="col-sm-12 user_title" style="padding: 0px;">
						<div class="name col-sm-4"><b>Name</b> </div>
						<div class=" col-sm-4" style="padding-right: 41px;"><b>Invited</b></div>
						<div class=" col-sm-4 resp-feed-list"  ><b>Responded</b></div>
						<!-- <div class=" col-sm-1"><b>&nbsp;</b></div>-->
					</div>
					<div class="user_listing">
						<?php
							if(isset($users) && !empty($users)){

								$selectedusers = '';
								foreach($users as $user){
								if(isset($user['User']['UserDetail']) && !empty($user['User']['UserDetail'])){
						?>
						<div class="user_list">
							<div class="name col-sm-4">
							<a data-toggle='modal' data-target='#popup_modal' class='viewuserPP' data-whatever='<?php echo SITEURL; ?>shares/show_profile/<?php echo $user['User']['id']; ?>'>
							<?php
							if(isset($user['User']['UserDetail']['first_name']) && !empty($user['User']['UserDetail']['first_name'])){
							echo $user['User']['UserDetail']['first_name'].' '.$user['User']['UserDetail']['last_name'];
							}else{
							echo $user['User']['email'];
							}
							?>
							</a>
							<input type="hidden" class="textbox-value" name="data[FeedbackUser][list][]" value="<?php echo $user['User']['id'] ?>">
							</div>

							<?php
							$created = '';
							if(isset($user['created']) && !empty($user['created'])){ $created = $user['created']; } ?>
							<div class=" col-sm-4"><?php //echo date('d M,Y', $created);
								echo $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',$created),$format = 'd M, Y');
							?></div>
							<div class=" col-sm-4"><?php echo $this->Common->feedbackresponded($id, $user['User']['id']); ?></div>
						</div>

						<?php			}
								}
							}else{
						?>
						<div class="user_list">
							<div class="col-sm-12 text-center" >
								No participants.
							</div>
						</div>
							<?php } ?>
							<span id="more<?php echo $data['id']; ?>"></span>
					</div>
				</div>

				 <div class="col-lg-5 col-md-12 col-sm-5 invite-users-select <?php echo $disabledSignOff;?>">
					<label style="padding: 0px;" class="control-label col-sm-12" for="selectUsers"><a id="onn3" href="javascript:void(0)"> Select Users </a>| <a href="javascript:void(0)" id="off3"> Select Groups  </a></label>
					<div class="form-group select-g-feedback">

						<select  id="multi_participant_users" style="opacity: 0;height: 24px;width: 0px;"  multiple="multiple"></select>

						<!-- <select <?php if(!empty($disabledfeedback)){ echo $disabledfeedback; }else{ echo $disabledSignOff; } ?>  id="user_<?php echo $id; ?>" placeholder="Search by username" class="easyui-combogrid form-control" name="data[FeedbackUser][list][]" style="width:250px" data-options="
								panelWidth: 360,
								multiple: true,
								idField: 'id',
								textField: 'name',
								url: '<?php echo SITEURL; ?>entities/feedback_users_listing?feedback_id=<?php echo $id; ?>',
								method: 'get',
								mode: 'remote',
								columns: [[
					{id:'ckss',	field:'ckss', checkbox:true},	{field:'name',title:'All Users',width:120},
								]],
								fitColumns: true
							">
						</select> -->
					</div>
                   <div class="vote-reminder-send">


						<a <?php if(!empty($disabledfeedback)){ echo $disabledfeedback; }else{ echo $disabledSignOff; } ?> style="float: right; " id="<?php echo $id; ?>" href="#" class="btn btn-sm btn-success update_feedback_user submit disabled">
							Update
						</a>

						<a <?php if(!empty($disabledfeedback)){ echo $disabledfeedback; }else{ echo $disabledSignOff; } ?> style="float: right; margin-right:15px; " rel="<?php echo $id; ?>" href="#" class="btn btn-sm btn-success reminder_feedback_user">
							Send Reminder
						</a>

						<img style="float: right; margin: 7px 10px 0px 0px; display:none;" src="<?php echo SITEURL; ?>images/ajax-loader.gif" class="reminderloading" />
					</div>
				</div>



			</div>
			</div>
			</form>



<script type="text/javascript" >
$(function(){

	$('#off3').click(function(){

	$.ajax({
		url: '<?php echo SITEURL; ?>entities/feedback_users_listing_new?feedback_id=<?php echo $id; ?>',
		type: "POST",
		data: $.param({project: $js_config.currentProjectId}),
		dataType: "JSON",
		global: false,
		success: function (response) {

			if (response.success) {

				var selectUsers = response.users;
				var selectGroups = response.group_data;

				$('#multi_participant_users').empty();

 		if( selectGroups != null ) {

						var output = '';
						$.each(selectGroups, function(key, value) {

							if( !$.isEmptyObject(value['users']) ) {

								output += '<optgroup label="' + value[0].title + '">';

								var userlist = value['users'];
								userlist.sort($.SortByName);

								$.each(userlist , function(key1, value1) {

									output += '<option value="' + value1.id + '">' + value1.name + '</option>';

								});

								output += '</optgroup>';
							}

						});

					$('#multi_participant_users').html(output)
				}

				setTimeout(function(){

					$('#multi_participant_users').multiselect({
						showRating: true,
						maxHeight: '400',
						buttonWidth: '100%',
						buttonClass: 'btn btn-info',
						checkboxName: 'data[FeedbackUser][list][]',
						enableFiltering: true,
						filterBehavior: 'text',
						includeFilterClearBtn: true,
						enableCaseInsensitiveFiltering: true,
						// numberDisplayed: 3,
						includeSelectAllOption: true,
						includeSelectAllIfMoreThan: 5,
						selectAllText: ' Select all',
						// disableIfEmpty: true
						onInitialized: function() {

						},
						onChange: function() {
							var selected_opts = $('#feedbacks_table .row.bg-warning1 #multi_participant_users option:selected');
							var selected = [];
							$(selected_opts).each(function(index, opt){
								selected.push([$(this).val()]);
							});
							if(selected.length > 0){
								$('#feedbacks_table .row.bg-warning1 .update_feedback_user').removeClass('disabled');
							} else {
								$('#feedbacks_table .row.bg-warning1 .update_feedback_user').addClass('disabled');
							}
						},
						enableClickableOptGroups: true,
						enableCollapsibleOptGroups: true,
					});

					$('#multi_participant_users').multiselect('rebuild') ;

					// $("#users_list").slideUp().slideDown()
					$('.multiselect-container li:not(.multiselect-item.multiselect-group.multiselect-group-clickable)').each(function(i, v){
						// $(this).css({"padding-left": "20px", "margin-top": "-5px"})
					})
				}, 200)
			}
			else {
				$('#multi_participant_users').multiselect('disable')
				$('.multiselect.dropdown-toggle').parent().next('span.error-message:first').html(response.msg);
			}

		},// end success
		complete: function() {
		}

	})

	})


	$('#onn3').click(function(){

$.ajax({
		url: '<?php echo SITEURL; ?>entities/feedback_users_listing_new?feedback_id=<?php echo $id; ?>',
		type: "POST",
		data: $.param({project: $js_config.currentProjectId}),
		dataType: "JSON",
		global: false,
		success: function (response) {

			if (response.success) {

				var selectUsers = response.users;
				var selectGroups = response.group_data;

				$('#multi_participant_users').empty();

/* 				if( selectGroups != null ) {

						var output = '';
						$.each(selectGroups, function(key, value) {

							if( !$.isEmptyObject(value['users']) ) {

								output += '<optgroup label="' + value[0].title + '">';

								$.each(value['users'] , function(key1, value1) {

									output += '<option value="' + value1.id + '">' + value1.name + '</option>';

								});

								output += '</optgroup>';
							}

						});

					$('#multi_participant_users').html(output)
				} */
				if( selectUsers != null ) {
					selectUsers.sort($.SortByName);

					$('#multi_participant_users').append(function() {

						var output = '';

						// output += '<optgroup label="Individual">';

						$.each(selectUsers, function(key, value) {

							output += '<option value="' + value.id + '">' + value.name + '</option>';

						});

						// output += '</optgroup>';

						return output;
					});
				}
				else {
					$('.btn-select.btn-select-light').parent().find('span.error-message').html('No user found. Please select different project.');
					$(".user_selection").slideUp()
				}
				setTimeout(function(){

					$('#multi_participant_users').multiselect({
						showRating: true,
						maxHeight: '400',
						buttonWidth: '100%',
						buttonClass: 'btn btn-info',
						checkboxName: 'data[FeedbackUser][list][]',
						enableFiltering: true,
						filterBehavior: 'text',
						includeFilterClearBtn: true,
						enableCaseInsensitiveFiltering: true,
						// numberDisplayed: 3,
						includeSelectAllOption: true,
						includeSelectAllIfMoreThan: 5,
						selectAllText: ' Select all',
						// disableIfEmpty: true
						onInitialized: function() {

						},
						onChange: function() {
							var selected_opts = $('#feedbacks_table .row.bg-warning1 #multi_participant_users option:selected');
							var selected = [];
							$(selected_opts).each(function(index, opt){
								selected.push([$(this).val()]);
							});
							if(selected.length > 0){
								$('#feedbacks_table .row.bg-warning1 .update_feedback_user').removeClass('disabled');
							} else {
								$('#feedbacks_table .row.bg-warning1 .update_feedback_user').addClass('disabled');
							}
						},
						enableClickableOptGroups: true,
						enableCollapsibleOptGroups: true,
					});

					$('#multi_participant_users').multiselect('rebuild') ;

					// $("#users_list").slideUp().slideDown()
					$('.multiselect-container li:not(.multiselect-item.multiselect-group.multiselect-group-clickable)').each(function(i, v){
						// $(this).css({"padding-left": "20px", "margin-top": "-5px"})
					})
				}, 200)
			}
			else {
				$('#multi_participant_users').multiselect('disable')
				$('.multiselect.dropdown-toggle').parent().next('span.error-message:first').html(response.msg);
			}

		},// end success
		complete: function() {
		}

	})// end ajax

	})



	$.ajax({
		url: '<?php echo SITEURL; ?>entities/feedback_users_listing_new?feedback_id=<?php echo $id; ?>',
		type: "POST",
		data: $.param({project: $js_config.currentProjectId}),
		dataType: "JSON",
		global: false,
		success: function (response) {

			if (response.success) {

				var selectUsers = response.users;
				var selectGroups = response.group_data;

				$('#multi_participant_users').empty();

/* 				if( selectGroups != null ) {

						var output = '';
						$.each(selectGroups, function(key, value) {

							if( !$.isEmptyObject(value['users']) ) {

								output += '<optgroup label="' + value[0].title + '">';

								$.each(value['users'] , function(key1, value1) {

									output += '<option value="' + value1.id + '">' + value1.name + '</option>';

								});

								output += '</optgroup>';
							}

						});

					$('#multi_participant_users').html(output)
				} */
				if( selectUsers != null ) {
					selectUsers.sort($.SortByName);

					$('#multi_participant_users').append(function() {

						var output = '';

						// output += '<optgroup label="Individual">';

						$.each(selectUsers, function(key, value) {

							output += '<option value="' + value.id + '">' + value.name + '</option>';

						});

						// output += '</optgroup>';

						return output;
					});
				}
				else {
					$('.btn-select.btn-select-light').parent().find('span.error-message').html('No user found. Please select different project.');
					$(".user_selection").slideUp()
				}
				setTimeout(function(){

					$('#multi_participant_users').multiselect({
						showRating: true,
						maxHeight: '400',
						buttonWidth: '100%',
						buttonClass: 'btn btn-info',
						checkboxName: 'data[FeedbackUser][list][]',
						enableFiltering: true,
						filterBehavior: 'text',
						includeFilterClearBtn: true,
						enableCaseInsensitiveFiltering: true,
						// numberDisplayed: 3,
						includeSelectAllOption: true,
						includeSelectAllIfMoreThan: 5,
						selectAllText: ' Select all',
						// disableIfEmpty: true
						onInitialized: function() {

						},
						// onDropdownHidden
						onChange: function() {
							var selected_opts = $('#feedbacks_table .row.bg-warning1 #multi_participant_users option:selected');
							var selected = [];
							$(selected_opts).each(function(index, opt){
								selected.push([$(this).val()]);
							});
							if(selected.length > 0){
								$('#feedbacks_table .row.bg-warning1 .update_feedback_user').removeClass('disabled');
							} else {
								$('#feedbacks_table .row.bg-warning1 .update_feedback_user').addClass('disabled');
							}
						},
						enableClickableOptGroups: true,
						enableCollapsibleOptGroups: true,
					});

					$('#multi_participant_users').multiselect('rebuild') ;

					// $("#users_list").slideUp().slideDown()
					$('.multiselect-container li:not(.multiselect-item.multiselect-group.multiselect-group-clickable)').each(function(i, v){
						// $(this).css({"padding-left": "20px", "margin-top": "-5px"})
					})
				}, 200)
			}
			else {
				$('#multi_participant_users').multiselect('disable')
				$('.multiselect.dropdown-toggle').parent().next('span.error-message:first').html(response.msg);
			}

		},// end success
		complete: function() {
		}

	})// end ajax


	$('body').on( 'click', function(event) {

		setTimeout(function(){
			if( $('.multiselect.dropdown-toggle').parent('.btn-group').hasClass('open') ) {
				$('.main-header').parent('.wrapper').css('overflow', 'visible')
			}
			else {
				$('.main-header').parent('.wrapper').css('overflow', 'hidden')
			}
		}, 200)
	})
	/*$('.formupdateFeedbackUsers #multi_participant_users').change(function(){
		var checked_cnt = $('.formupdateFeedbackUsers .multiselect-container.dropdown-menu input[type="checkbox"]:checked').length;
		if(checked_cnt > 0) {
			$('.update_feedback_user').removeClass('disabled');
		} else {
			$('.update_feedback_user').addClass('disabled');
		}
	})*/


})
</script>
<style>.input-group-btn .btn.btn-default.multiselect-clear-filter{line-height:19px !important;}</style>