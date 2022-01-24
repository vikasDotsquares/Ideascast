<script type="text/javascript" >
$(function(){

	$('body').delegate('.sidebar-toggle', 'click', function(){
		if( !$('body').hasClass('sidebar-collapse') ) {
			$.popover_hack();
		}
	})

/* 	$('.pophover-extra').popover({
		trigger: 'hover',
		placement: 'bottom',
		html: true,
		container: 'body',
		delay: {show: 50, hide: 400}
	}) */

	$.popover_hack = function() {

		$('.pophover-extra').on('shown.bs.popover', function () {
			var data = $(this).data('bs.popover'),
				$tip = data.$tip,
				$arrow = data.$arrow;

			if( !$('body').hasClass('sidebar-collapse') ) {
				$tip.animate({
					left: parseInt($tip.css('left')) + 45 + 'px'
					}, 200, function(){
				})
				// $arrow.css('left', '31%')
				$arrow.css('left', ($(this).outerWidth()/2)+2+'%')
			}

		})
	}

	if( !$('body').hasClass('sidebar-collapse') ) {
		$.popover_hack();
	}

})
</script>

<style>
	.click {
		color: #FD5353;
		cursor: pointer;
	}
	.no_user {
		display: block;
		padding: 20px 0;
		text-align: center;
	}
	.panel {
		background-color: #ffffff;
		border: 1px solid #dddddd;
		border-radius: 4px;
		box-shadow: 0 1px 1px rgba(0, 0, 0, 0.05);
		margin-bottom: 20px;
	}
	.add-user-heading .add-user-title {
		background-color: #f5f5f5;
		border-bottom-left-radius: 3px;
		border-bottom-right-radius: 3px;
		border-bottom: 1px solid #dddddd;
		padding: 10px 15px;
		margin-top: 0;
	}
	.multiselect-container  > li:not(.multiselect-all):not(.filter) {
		margin-top: -5px;
	}
	.group-users-list {
		max-height: 720px;
		/* margin-bottom: 20px; */
	}
	.group-users-list table tbody tr td:first-child span {
		padding-top: 0;
	}

	.remove_user {
		background-color: transparent;
	}
.my-group-h {
    padding-top: 2px;
    display: inline-block;
    vertical-align: top;
}

</style>
<?php // PROJECTS THAT ARE OWNED BY THE CURRENT USER ARE DISPLAYING PROJECTWISE ?>
<?php // Used in my_sharing.ctp as Element View

// echo $this->Html->script('projects/selectbox/bootstrap-multiselect', array('inline' => true));
$current_org = $this->Permission->current_org();
if(isset($permit_data['pp_data']) && !empty($permit_data['pp_data'])) {

	foreach($permit_data['pp_data'] as $key => $val ) {
	// pr($val['UserProject']['project_id']);
		$Project = NULL;

		$ProjectPermission = $val['ProjectPermission'];
		$ProjectGroup = $val['ProjectGroup'];

		$permit_users = $val ['ProjectGroupUser'];

		$openProjectPanel = $project_key = '';

		$pdata = project_primary_id($ProjectGroup['user_project_id'], 1);

		if(isset($pdata['title']) && !empty($pdata['title'])){
	?>

	<div class="panel panel-default panel-main" data-project="<?php echo $pdata['id']; ?>">
		<div class="panel-heading" <?php if( isset($this->params['pass'][0]) && !empty($this->params['pass'][0]) && $ProjectGroup['id'] == $this->params['pass'][0] ) { ?> data-key="<?php echo $this->params['pass'][0]; ?>" <?php } ?>>
			<h4 class="panel-title">
				<a href="#" class="open_panel" data-toggle="collapse" data-parent="#project_accordion"  data-target="#collapse_<?php echo $key; ?>_project">
					<i class="broupblack" style="margin-right: 5px;"></i><span class="my-group-h"><?php echo " <b>Group: </b><span class='group-name'>".htmlentities($ProjectGroup['title'],ENT_QUOTES, "UTF-8")."</span><span class='gpname'>(<b>Project:</b> ".strip_tags($pdata['title']).")</span>"; ?></span>
				</a>
<!-- 				<i class="pull-right plus-minus-icon" data-plus="&#xf067;" data-minus="&#xf068;"></i>
	<span class="cst" href="login.php"><i class="fa fa-key"></i><span>Login</span></span> -->

				<!-- <a title="Delete Group" class="btn btn-xs tipText pull-right view_share_map btn-danger delete_group" title="" href="" data-remote="<?php echo Router::Url(array('controller' => 'groups', 'action' => 'delete_group', $pdata['id'],  $ProjectGroup['id'], 'admin' => FALSE ), TRUE); ?>" > <i class="fa fa-trash-o" ></i></a> -->

				<!-- PASSWORD DELETE -->
				<a title="Delete" class="tipText pull-right view_share_map delete-an-item" title="" href="" data-target="#modal_delete" data-toggle="modal" data-remote="<?php echo Router::Url(array('controller' => 'shares', 'action' => 'delete_an_item', $pdata['id'], $ProjectGroup['id'], 'admin' => FALSE ), TRUE); ?>" > <i class="deleteblack"></i></a>

				<a title="Edit Sharing Permissions" class="tipText pull-right view_share_map update_permissions" title="" href="<?php echo Router::Url(array('controller' => 'groups', 'action' => 'update_permissions', $pdata['id'], $ProjectGroup['id'], 'admin' => FALSE ), TRUE); ?>" > <i class="mysharingblack18"></i></a>

				<a title="Edit" class="pull-right tipText view_share_map add_users" href="#" data-remote="<?php echo Router::Url(array('controller' => 'groups', 'action' => 'add_users_to_group', $pdata['id'], $ProjectGroup['id'], 'admin' => FALSE ), TRUE); ?>" data-grpid="<?php echo $ProjectGroup['id']; ?>" data-pid="<?php echo $pdata['id']; ?>" > <i class="edit-icon"></i></a>

			</h4>

		</div>
		<div id="collapse_<?php echo $key; ?>_project" class="panel-collapse collapse <?php if( $openProjectPanel <= 0 ) { ?> close_panel <?php } ?>">
			<div class="panel-body">
				<div class="panel panel-default panel_add_users clearfix" style="display: none; "> </div>
				<div class="table-responsive group-users-list">

				<?php if( isset($permit_users) && !empty($permit_users ) ) { ?>

					<table class="table table-bordered custom-table-border" data-id="" >
						<thead>
							<tr>
								<th width="15%" class="text-left">Shared With</th>
								<th width="10%" class="text-center">Request</th>
								<th width="20%" class="text-center">Shared On</th>
								<th width="20%" class="text-center">Role Level</th>
								<th width="15%" class="text-center">Propagation</th>
								<th width="20%" class="text-center" >Action</th>
							</tr>
						</thead>
						<tbody>


				<?php

					foreach($permit_users  as $ukey => $uval ) {

				?>
				<?php
						$project_permit = $uval;
						$request = 'Pending';
						if(isset($project_permit['approved']) && $project_permit['approved'] ==1 ){
							$request = 'Accepted';
						}else if(isset($project_permit['approved']) && $project_permit['approved'] > 1 ){
							$request = 'Rejected';
						}else{
						    $request = 'Pending';
						}

						$current_user_id = $this->Session->read('Auth.User.id');

						$permit_user_data = $this->Common->userDetail($project_permit['user_id']);

						$pic = ( !empty($permit_user_data['UserDetail']['profile_pic']) ) ? $permit_user_data['UserDetail']['profile_pic'] : '';
						$profiles = SITEURL . USER_PIC_PATH . $pic;


						if (!empty($pic) && file_exists(USER_PIC_PATH . $pic)) {
							$profiles = SITEURL . USER_PIC_PATH . $pic;
						} else {
							$profiles = SITEURL . 'img/image_placeholders/logo_placeholder.gif';
						}
						$html = '';
						if( !empty($permit_user_data['UserDetail']['user_id']) &&  $permit_user_data['UserDetail']['user_id'] != $current_user_id && (isset($project_permit['approved']) && $project_permit['approved'] == 1 ) ) {
							$html = CHATHTML($permit_user_data['UserDetail']['user_id'], $val['UserProject']['project_id']);
						}

						$userFirstName = ( isset($permit_user_data['UserDetail']['first_name']) && !empty($permit_user_data['UserDetail']['first_name']) ) ? $permit_user_data['UserDetail']['first_name'] : '';
						$userLastName = ( isset($permit_user_data['UserDetail']['last_name']) && !empty($permit_user_data['UserDetail']['last_name']) ) ? $permit_user_data['UserDetail']['last_name'] : '';
						$userJobDetail = ( isset($permit_user_data['UserDetail']['job_title']) && !empty($permit_user_data['UserDetail']['job_title']) ) ? $permit_user_data['UserDetail']['job_title'] : '';

						$userDetailUserId = ( !empty($permit_user_data['UserDetail']['user_id']) ) ? $permit_user_data['UserDetail']['user_id'] : '';
						$content = "<div><p>".$userFirstName." ".$userLastName."</p><p>".htmlentities($userJobDetail, ENT_QUOTES, "UTF-8")."</p>".$html."</div>";
					?>
							<tr class=" " data-uid="<?php echo $project_permit['user_id']; ?>">
								<td class="text-left">
									<div class="style-people-com">
										<span class="style-popple-icon-out">
											<a class="style-popple-icon" href="javascript:void(0);" data-toggle="modal" data-remote="<?php echo Router::url(array('controller' => 'shares', 'action' => 'show_profile', $userDetailUserId)); ?>" data-target="#popup_modal">
												<img alt="sender image" src="<?php echo $profiles; ?>" data-content="<?php echo $content; ?>" class="user-image pophover-extra" align="left" >
											</a>
											<?php if($permit_user_data['UserDetail']['organization_id'] != $current_org['organization_id']){ ?>
											<i class="communitygray18 tipText community-g" style="cursor: pointer;" title="" data-original-title="Not In Your Organization" data-target="#popup_modal" data-toggle="modal" data-remote="<?php echo Router::url(array('controller' => 'shares', 'action' => 'show_profile', $userDetailUserId)); ?>"></i>
											<?php } ?>
										</span>

										<div class="style-people-info">
											<span class="style-people-name" data-toggle="modal" data-remote="<?php echo Router::url(array('controller' => 'shares', 'action' => 'show_profile', $userDetailUserId)); ?>" data-target="#popup_modal"><?php echo $userFirstName." ".$userLastName; ?></span>
											<span class="style-people-title"><?php echo htmlentities($userJobDetail, ENT_QUOTES, "UTF-8"); ?></span>
										</div>
								 	</div>

							 	<?php /* ?>
								<a href="#" data-remote="<?php echo Router::url(array('controller' => 'shares', 'action' => 'show_profile', $userDetailUserId)); ?>" data-target="#popup_modal" data-toggle="modal" >
									<?php
										$content = "<div><p>".$userFirstName." ".$userLastName."</p><p>".$userJobDetail."</p>".$html."</div>";
										echo $this->Html->image($profiles, array("data-content" => $content, "class" => "user-image comment-people-pic border-darks pophover-extra"));
									?>
								</a>
								<span class="hidden-sm hidden-md"><?php echo $userFirstName; ?></span>
								<?php */ ?>

								</td>

								<td class="text-center"><?php echo $request; ?></td>

								<td class="text-center"><?php
								echo (isset($project_permit['created']) && !empty($project_permit['created'])) ? $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($project_permit['created'])),$format = 'd M, Y g:iA') : "N/A";

								?></td>

								<td class="text-center"><?php echo (isset($ProjectPermission['project_level']) && $ProjectPermission['project_level'] == 1) ? 'Owner' : ( ($ProjectPermission['project_level'] == 0) ? 'Sharer': 'N/A' ); ?></td>

								<td class="text-center">N/A<?php /*echo (isset($ProjectPermission['share_permission']) && $ProjectPermission['share_permission'] == 1) ? 'On' : ( ($ProjectPermission['share_permission'] == 0) ? 'Off': 'N/A' );*/ ?></td>

								<td class="text-center" data-upid="<?php echo $ProjectGroup['user_project_id']; ?>"  data-gid="<?php echo $ProjectGroup['id']; ?>" data-uid="<?php echo $project_permit['user_id']; ?>" style="vertical-align: middle">

									 <button data-target="85" id="remove_user" data-remote="<?php echo SITEURL.'shares/trashGroupUser'; ?>" type="button" class="btn btn-xs remove_user tipText" data-original-title="Remove">
									 <i class="clearblackicon"></i>
									</button>

								</td>
							</tr>

					<?php } ?>
						</tbody>

					</table>

				<?php }else{	?>
					<div class="no_user">

						No User found in this group. <span class="add_users click" title="" href="#" data-remote="<?php echo Router::Url(array('controller' => 'groups', 'action' => 'add_users_to_group', $pdata['id'], $ProjectGroup['id'], 'admin' => FALSE ), TRUE); ?>" data-grpid="<?php echo $ProjectGroup['id']; ?>" data-pid="<?php echo $pdata['id']; ?>">Click here</span> to add users to group.

					</div>
				<?php }	?>

			</div>

			</div>
		</div>


	</div>
	<?php } } ?>

<?php }else{ ?>

	<?php if(isset($type) && $type == 'group'){ ?>
		<div class="col-sm-12 partial_data box-borders">
			<div class="overview-box" id="projects">
				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 project_data">
					<div class="no-data"> NO GROUPS </div>
				</div>
			</div>
		</div>
	<?php } else { ?>
	<div class="panel panel-default margin padding">
		<?php e('You have not shared any Projects with other users.'); ?>
	</div>
<?php }
} ?>

<script type="text/javascript" >
$(function(){

	$('body').delegate('.close_add_user_panel', 'click', function(e){
		e.preventDefault();

		/*$(this).parents('.panel_add_users:first').slideUp(500, function(){
			$(this).html('');
		})*/

	})

	$('#popup_model_box').on('hidden.bs.modal', function(){
		$(this).removeData()
	})


	$('.remove_user').click(function(event) {
			event.preventDefault()
			var $t = $(this),
				$el =  $(this).parent().parent(),
				$tbody =  $el.parent('tbody'),
				data = $t.data(),
				url = data.remote,
				user_id = $(this).parent().attr('data-uid'),
				user_project_id = $(this).parent().attr('data-upid'),
				project_group_id = $(this).parent().attr('data-gid'),
				post = {
				   'data[ProjectGroupUser][user_project_id]': user_project_id,
				   'data[ProjectGroupUser][project_group_id]': project_group_id,
				   'data[ProjectGroupUser][user_id]': user_id
				};

			BootstrapDialog.show({
	            title: 'Remove User From Group',
	            message: 'Are you sure you want to remove this User from this Group?',
	            type: BootstrapDialog.TYPE_DANGER,
	            draggable: true,
	            buttons: [{
	                   /*  icon: 'fa fa-check', */
	                    label: ' Remove',
	                    cssClass: 'btn-success',
	                    autospin: false,
	                    action: function(dialogRef) {
	                        $.when(
	                        	$.ajax({
									type: 'POST',
									data: $.param(post),
									dataType: 'JSON',
									url: url,
									global: false,
									success: function (response, status, jxhr) {
										if (response.success) {
											console.log($el);
											$('td',$el).slideUp(1000, function () {
											   $el.remove()
												if( $tbody.find('tr').length <= 0 ) {
													location.reload();
												}
											})
										}
										else {
										  console.log('error');
										}
									}
								})
                        	)
                            .then(function(data, textStatus, jqXHR) {
                                dialogRef.enableButtons(false);
                                dialogRef.setClosable(false);
                               // dialogRef.getModalBody().html('<div class="loader"></div>');
                                setTimeout(function() {
                                    dialogRef.close();
                                }, 500);
                            })
	                    }
	                },
	                {
	                    label: ' Cancel',
	                    /* icon: 'fa fa-times', */
	                    cssClass: 'btn-danger',
	                    action: function(dialogRef) {
	                        dialogRef.close();
	                    }
	                }
	            ]
	        });
		});
	$('body').delegate(".remove_user123", 'click', function (event) {
		event.preventDefault()
		var $t = $(this),
			$el =  $(this).parent().parent(),
			$tbody =  $el.parent('tbody'),
			data = $t.data(),
			url = data.remote,
			user_id = $(this).parent().attr('data-uid'),
			user_project_id = $(this).parent().attr('data-upid'),
			project_group_id = $(this).parent().attr('data-gid'),
			post = {
			   'data[ProjectGroupUser][user_project_id]': user_project_id,
			   'data[ProjectGroupUser][project_group_id]': project_group_id,
			   'data[ProjectGroupUser][user_id]': user_id
			};

			$.when($.confirm({message: 'Are you sure you want to delete this User from the group?', title: 'Delete confirmation'})).then(
				function () {
				   $.ajax({
						type: 'POST',
						data: $.param(post),
						dataType: 'JSON',
						url: url,
						global: false,
						success: function (response, status, jxhr) {
							if (response.success) {
								console.log($el);
								$('td',$el).slideUp(1000, function () {
								   $el.remove()
									if( $tbody.find('tr').length <= 0 ) {
										location.reload();
									}
								})
							}
							else {
							  console.log('error');
							}
						}
				   });
			},
			function ( ) {
			   console.log('Error!!!')
			});

     })



})
</script>
<style>
.pop-content p {
	margin-bottom: 5px;
	font-size: 12px !important;
}

.pophover {
	float: left;
}
.popover {
	z-index: 999999 !important;
}
.popover p {
	margin-bottom: 2px !important;
}

.popover p:first-child {
	font-weight: 600 !important;
	width: 170px !important;
}
.popover p:nth-child(2) {
  font-size: 11px;
}

</style>

