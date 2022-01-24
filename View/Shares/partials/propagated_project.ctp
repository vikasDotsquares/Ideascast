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
				$arrow.css('left', '31%')
			}

		})
	}

	if( !$('body').hasClass('sidebar-collapse') ) {
		$.popover_hack();
	}

})
</script>

<?php // SHOWS THE LIST OF PROJECTS THAT ARE SHARED WITH THE CURRENT USER ARE USED TO SHARE WITH OTHER USERS  ?>
<?php // Used in propagated_projects.ctp as Element ?>

<?php
$inc = 0;
$current_org = $this->Permission->current_org();
if( isset($user_permissions) && !empty($user_permissions) ) {
	foreach($user_permissions as $key => $val ) {

		$ProjectPermission = $val['ProjectPermission'];

		$pp_id = $ProjectPermission['id'];
		$project_permit_id = $ProjectPermission['parent_id'];
		$user_project_id = $ProjectPermission['user_project_id'];
		$project_id = project_primary_id($user_project_id);

		$parent_data = $val['Parent'];

		$propagated_rows = $this->ViewModel->propagated_projects( $project_permit_id, $user_project_id );

		$con = ( isset($propagated_rows) && !empty($propagated_rows) ) ? count($propagated_rows) : 0;

	 	if( isset($propagated_rows) && !empty($propagated_rows) ) {
			$pdata = $this->ViewModel->getProjectDetail( $project_id );

			if( isset($pdata) && !empty($pdata) ) {
				$Project = $pdata['Project'];

				$total_users = 0;
				foreach($propagated_rows as $ukey => $uval ) {
					if( isset($uval['ProjectPermission']) && !empty($uval['ProjectPermission']) &&  (isset($uval['ProjectPermission']['user_id']) && !empty($uval['ProjectPermission']['user_id'])) ) {
						$total_users++;
					}
				}

		$grp_id = user_group_count($this->Session->read("Auth.User.id"),project_upid($Project['id']));

		$totalGroupUsersCount = 0;
		$projectGroupDetail = user_group_details($this->Session->read("Auth.User.id"),project_upid($Project['id']));

		$allgroupusrescnt = 0;
		$allgroupusres = null;
		if( isset( $projectGroupDetail ) && !empty($projectGroupDetail) ){
			foreach($projectGroupDetail as $gkey => $gval ) {

					$allgroupusres = group_users_all($gval['ProjectGroup']['id']);

					$allgroupusrescnt += (isset($allgroupusres) && !empty($allgroupusres))?  count($allgroupusres) : 0;
			}
		}

		$totalGroupUsersCount = $allgroupusrescnt+$total_users;

	?>

	<div class="panel panel-default pointer">
		<div class="panel-heading">
			<h4 class="panel-title">
				<div class="project-net-title">
				<a href="javascript:;" class="nopadding-right hover-grn">
					<i class="pjblack"></i>
					<span class="open_panel" data-toggle="collapse" data-parent="#project_accordion"  data-target="#collapse_<?php echo $key; ?>_project">
					<?php echo strip_tags($Project['title']); ?>:</span>
				</a>

				<span class="sharedlinks">
					<span class="showgroupbymember" data-mcnt="<?php echo $total_users;?>"><?php echo ($total_users > 1) ? 'Individual Shares: '.$total_users :  'Individual Shares: '.$total_users; ?></span> | <span <?php if( $grp_id > 0){ ?> class="showgroupbygrp" <?php } else { ?>style="cursor:default;"<?php }?> data-gcnt="<?php echo $grp_id; ?>">Group Shares: <?php echo $grp_id; ?></span> | <span <?php if( $totalGroupUsersCount > 0){ ?> class="showgroupbytotalgrp" <?php } else { ?>style="cursor:default;"<?php }?> data-gcnt="<?php echo $totalGroupUsersCount; ?>">Total Team Member Shares: <?php echo $totalGroupUsersCount;?></span>
				</span>
				</div>
				<span class="project-net-btn">
					<span class="btn btn-xs col-exp-panel tipText" title="Open Project" data-pid="<?php echo $Project['id'];?>">
						<i class="openblack"></i>
					</span>

					<?php
					$ProjectLevel = ProjectLevel($Project['id']);
					if(isset($ProjectLevel) && !empty($ProjectLevel)){
					?>
					<span class="btn btn-xs col-exp-panel-sharing tipText" title="Sharing Map" data-pid="<?php echo $Project['id'];?>">
						<i class="fbtn mysharingblack18 tipText"></i>
					</span>
					<?php } ?>

				</span>
			</h4>
		</div>

		<div id="collapse_<?php echo $key; ?>_project" class="panel-collapse collapse">
			<div class="panel-body">
					<div class="sort-search  clearfix">
					   <div class="col-sm-6 col-md-6 col-lg-4 pull-right sort-search-inside" style="">
						  <div class="input-group">
							 <span class="input-group-btn">
							 <a href="#" class="btn btn-primary user_list_sorting" style="
								" data-sptype="desc" >Sort Desc</a>
							 </span>
							 <input type="text" class="form-control searchusershare" name="" style=""><span class="input-group-btn">
							 <button class="btn bg-gray share_user_search_btn" type="button" style="display: inline-block;"><i class="fa fa-search"></i></button>
							 </span>
						  </div>
					   </div>
					</div>
					<div style="max-height: 300px; overflow-y: auto; margin: 10px 0 0 0;">
				<div class="table-responsive">
					<table class="table table-bordered custom-table-border grpprojectlists " data-id="" >
						<thead>
							<tr>
								<th width="25%"   class="text-left">Shared With</th>
								<th width="20%" class="text-center">Shared On</th>
								<th width="15%" class="text-center">Propagation</th>
								<th width="20%" class="text-center" >Action</th>
							</tr>
						</thead>
						<tbody>
				<?php

					 foreach($propagated_rows as $key => $val ) {
						$unbind = [
									'hasOne' => ['UserInstitution'],
									'hasMany' => ['UserProject', 'UserPlan', 'UserTransctionDetail', 'ProjectPermission', 'WorkspacePermission', 'ElementPermission'],
								];

						$permit_user_data = $this->ViewModel->get_user( $val['User']['id'], $unbind, 1 );

						if( isset($val['ProjectPermission']) && !empty($val['ProjectPermission']) && (isset($val['ProjectPermission']['user_id']) && !empty($val['ProjectPermission']['user_id']))  ) {


							$project_permit = $val['ProjectPermission'];

							$current_user_id = $this->Session->read('Auth.User.id');

							$pic = $permit_user_data['UserDetail']['profile_pic'];
							$profiles = SITEURL . USER_PIC_PATH . $pic;


							if (!empty($pic) && file_exists(USER_PIC_PATH . $pic)) {
								$profiles = SITEURL . USER_PIC_PATH . $pic;
							} else {
								$profiles = SITEURL . 'img/image_placeholders/logo_placeholder.gif';
							}
							$html = '';
							if( $permit_user_data['UserDetail']['id'] != $current_user_id ) {
									$html = CHATHTML($permit_user_data['UserDetail']['user_id'], $Project['id']);
							}

					?>
							<tr data-userproject="<?php echo $permit_user_data['UserDetail']['full_name']; ?>" class="dataprjsorting">
								<td class="text-left">
									<div class="style-people-com">
										<span class="style-popple-icon-out">
											<a class="style-popple-icon pophover-extra" href="javascript:void(0);" data-toggle="modal" data-remote="<?php echo Router::url(array('controller' => 'shares', 'action' => 'show_profile', $permit_user_data['UserDetail']['user_id'])); ?>" data-target="#popup_modal" data-content="<div><p><?php echo $permit_user_data['UserDetail']['first_name']." ".$permit_user_data['UserDetail']['last_name']; ?></p><p><?php echo htmlentities($permit_user_data['UserDetail']['job_title'], ENT_QUOTES, "UTF-8"); ?></p><?php echo $html; ?></div>">
												<img alt="image" src="<?php echo $profiles; ?>" class="user-image " align="left" data-original-title="" title="">
											</a>
											<?php if($permit_user_data['UserDetail']['organization_id'] != $current_org['organization_id']){ ?>
											<i class="communitygray18 tipText community-g" style="cursor: pointer;" title="" data-original-title="Not In Your Organization" data-target="#popup_modal" data-toggle="modal" data-remote="<?php echo Router::url(array('controller' => 'shares', 'action' => 'show_profile', $permit_user_data['UserDetail']['user_id'])); ?>"></i>
											<?php } ?>
										</span>

										<div class="style-people-info">
											<span class="style-people-name" data-toggle="modal" data-remote="<?php echo Router::url(array('controller' => 'shares', 'action' => 'show_profile', $permit_user_data['UserDetail']['user_id'])); ?>" data-target="#popup_modal"><?php echo $permit_user_data['UserDetail']['full_name']; ?></span>
											<span class="style-people-title"><?php echo htmlentities($permit_user_data['UserDetail']['job_title'], ENT_QUOTES, "UTF-8"); ?></span>
										</div>
								 	</div>

								<!-- <td class="text-left">

									<a href="#" data-remote="<?php echo Router::url(array('controller' => 'shares', 'action' => 'show_profile', $permit_user_data['UserDetail']['user_id'])); ?>" align="left" data-target="#popup_modal" data-toggle="modal" >
										<?php
											$content = "<div><p>".$permit_user_data['UserDetail']['first_name']." ".$permit_user_data['UserDetail']['last_name']."</p><p>".$permit_user_data['UserDetail']['job_title']."</p>".$html."</div>";
											echo $this->Html->image($profiles, array("data-content" => $content, "class" => "user-image comment-people-pic border-darks pophover-extra"));
										?>
									</a>
									<span class="hidden-sm"><?php echo $permit_user_data['UserDetail']['first_name']; ?><br /><?php echo $permit_user_data['UserDetail']['last_name']; ?></span>

								</td> -->

								<td class="text-center">
									<?php
										echo (isset($project_permit['created']) && !empty($project_permit['created'])) ? $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($project_permit['created'])),$format = 'd M, Y g:iA') : "N/A";
									?>
								</td>

								<td class="text-center">
									<?php echo (isset($project_permit['share_permission']) && $project_permit['share_permission'] == 1) ? 'On' : ( ($project_permit['share_permission'] == 0) ? 'Off': 'N/A' ); ?>
								</td>

								<td class="text-center">
									<div class="btn-group">
										<?php if( ( isset($parent_data['project_level']) && !empty($parent_data['project_level'])  )  ) {  ?>
										<a class="btn btn-xs tipText" title="Edit Sharing Permissions" href="<?php echo Router::Url(array('controller' => 'shares', 'action' => 'update_sharing', $Project['id'], $permit_user_data['User']['id'], 2, 'admin' => FALSE ), TRUE); ?>" >
											<i class="mysharingblack18"></i>
										</a>
										<?php }
											if( ( isset($parent_data['project_level']) && !empty($parent_data['project_level'])  ) && ( isset($parent_data['share_permission']) && $parent_data['share_permission'] == 1  )  ) { ?>
											<?php if( isset($project_permit['project_level']) && $project_permit['project_level'] != 1 &&  isset($project_permit['share_permission']) && $project_permit['share_permission'] == 1 ) { ?>
											<a class="btn btn-xs tipText"  title="Edit Propagation Permissions" href="<?php echo Router::Url(array('controller' => 'shares', 'action' => 'update_propagation', $Project['id'], $permit_user_data['User']['id'], 2, 'admin' => FALSE ), TRUE); ?>" >
												<i class="propagationblack"></i>
											</a>
											<?php } ?>
										<?php } ?>

										<?php
											if( already_propagated($val['ProjectPermission']['id']) && !empty($val['ProjectPermission']['share_permission']) ) {


										?>
											<a class="btn btn-xs tipText propagated" title="Cannot Remove Sharing - Propagation Active">
												<i class="deleteblack"></i>
											</a>
										<?php } else {
/* echo "projectid = ".$Project['id'];
echo "permist user data = ".$permit_user_data['User']['id']; */
											?>
											<a class="btn btn-xs tipText remove_propagation " title="Delete" href="<?php echo Router::Url(array('controller' => 'shares', 'action' => 'delete_shared_propagation', $Project['id'], $permit_user_data['User']['id'], 'admin' => FALSE ), TRUE); ?>" data-toggle="confirmation" data-header="Confirmation" data-msg="Are you sure you want to remove propagation for this user?" data-value="1" data-id="" >
												<i class="deleteblack"></i>
												<!-- <i class="strik"></i>  -->
											</a>
										<?php } ?>
									</div>
								</td>
							</tr>

				<?php
						}
					 }
				 ?>
				 			<tr class="no-result" style="display: none;">
								<td colspan="5" align="center" style="font-size: 16px; font-weight: normal;">No Result</td>
							</tr>

						</tbody>
					</table>



						<?php $projectGroupDetail = user_group_details($this->Session->read("Auth.User.id"),project_upid($Project['id']));
							if( isset($projectGroupDetail) && !empty($projectGroupDetail) ){
								?>
								<table class="table table-bordered custom-table-border grouplists" data-id="" >
									<thead>
										<tr>
											<th width="25%"   class="text-left">Shared With</th>
											<th width="20%" class="text-center">Group Members</th>
											<th width="20%" class="text-center">Shared On</th>
											<th width="20%" class="text-center">Role Level</th>
											<th width="15%" class="text-center">Propagation</th>
											<th width="20%" class="text-center" >Action</th>
										</tr>
									</thead>
									<tbody>
							<?php
									foreach($projectGroupDetail as $gkey => $gval ) {
										  $grp_id = $gval['ProjectGroup']['id'];
										  $groupusers = group_users_all($gval['ProjectGroup']['id']);

										  $pdata = project_primary_id($gval['ProjectGroup']['user_project_id'], 1);

										  $group_permission = $this->Group->group_permission_details($Project['id'], $grp_id);
										  $userHtml = "";
										  $user_fullname ="";
										  if( isset($groupusers) && !empty($groupusers) ){
											 $userHtml .= "<div class='el_users' >";
											 foreach($groupusers as $usrelists){
												$group_sharer_id = $usrelists['ProjectGroupUser']['user_id'];

												$user_fullname = $userFullName = $this->Common->userFullname($group_sharer_id);
												$extrahtml = '';
												if( $usrelists['ProjectGroupUser']['approved'] == 1 ){
													$extrahtml = "<i class='fa fa-check text-blue'></i>";
												}

												$userHtml .= "<a data-toggle='modal' data-target='#popup_modal' data-remote='".SITEURL."shares/show_profile/".$group_sharer_id."' href='#'><i class='fa fa-user text-maroon'></i> ".$userFullName."&nbsp;".$extrahtml."</a><br />";

											 }
											 $userHtml .= "</div>";
										  }
							?>
									<tr class="dataprjsorting" data-user="<?php  echo htmlentities($gval['ProjectGroup']['title']); ?>" >
										<td class="text-left"><span class="hidden-sm"><?php echo htmlentities($gval['ProjectGroup']['title'],ENT_QUOTES, "UTF-8");?></span></td>
										<td class="text-center"><span>

											<a class="btn  btn-xs users_popovers" id="" style="margin: 9px 0px 0px;" data-content="<?php echo $userHtml;?>" data-original-title="" title="">
										<i class="broupblack"></i>
									</a>

										</span></td>
										<td class="text-center"><?php
											echo (isset($gval['ProjectGroup']['created']) && !empty($gval['ProjectGroup']['created'])) ? $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($gval['ProjectGroup']['created'])),$format = 'd M, Y g:iA') : "N/A"; ?>
										</td>

										<td class="text-center">
										<?php echo (isset($group_permission['ProjectPermission']['project_level']) && $group_permission['ProjectPermission']['project_level'] == 1) ? 'Owner' : ( ($group_permission['ProjectPermission']['project_level'] == 0) ? 'Sharer': 'N/A' ); ?>
										</td>
										<td class="text-center">
											<?php echo (isset($gval['ProjectGroup']['share_permission']) && $gval['ProjectGroup']['share_permission'] == 1) ? 'On' : ( ($gval['ProjectGroup']['share_permission'] == 0) ? 'Off': 'Off' );
											?>
										</td>

										<td class="text-center">

											<a title="Delete" class="btn btn-xs tipText pull-right view_share_map delete-an-item" title="" href="" data-target="#modal_delete" data-toggle="modal" data-remote="<?php echo Router::Url(array('controller' => 'shares', 'action' => 'delete_an_item', $pdata['id'], $gval['ProjectGroup']['id'], 'admin' => FALSE ), TRUE); ?>" > <i class="deleteblack"></i></a>

										</td>
									</tr>
								<?php
									}
								?>
									<tr class="no-result" style="display: none;">
										<td colspan="6" align="center" style="font-size: 16px; font-weight: normal;">No Result</td>
									</tr>
								</tbody>
							</table>
						<?php  } ?>

			</div>
			</div>
			</div>
		</div>
	</div>
	<?php
		}
	 $inc ++;
	  }
	}

	?>

<?php }

if( $inc == 0 ) { ?>
<div  style="padding-left: 6px;">You have not shared any Received Projects.</div>
<?php } ?>

<style>

.fa-user-victor {
    background: url(../images/icons/user-victor-black.png) no-repeat center;
    height: 18px;
    width: 18px;
    background-size: 100%;
    margin-bottom: -4px;
    display: inline-block;
}

.col-exp-panel {
    float: right !important;
    margin: 9px 10px 0 0;
}
.col-exp-panel-sharing {
    float: right !important;
    margin: 9px 0px 0 0;
}
.grouplists{
	display:none;
}
.popover .el_users{
	overflow-y: auto;
	max-height:200px;
	min-height:auto;
}
.popover .el_users a {
    display: inline-block;
    color: #333333;
    font-size: 12px;
    padding: 1px 0;
}
.popover .el_users .fa-check {
	cursor: default;
    float: right;
    padding-top: 2px;
    font-size: 12px;
}


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
.propagated {
    cursor: not-allowed;
	opacity: 0.65;
}

.accordion-group table {
    margin-top: 0;
}



.sort-search-inside {
    max-width: 300px;
    padding: 6px 1px 2px 10px;
}
.sort-search-inside .user_list_sorting {
	font-size:12px;
}
.sort-search-inside .input-group .input-group-btn {
    vertical-align: top;
}
.sort-search-inside .input-group .form-control {
height: 31px;
}
.sort-search-inside .input-group .input-group-btn .share_user_search_btn{
    font-size: 12px;
}

.no-result {
	display:none;
}

.panel-group{margin-bottom:0px !important;}
.table{margin-bottom:5px !important;}
</style>
<script>
$(function(){

	$('body').delegate('.col-exp-panel', 'click', function(){
		var prid = $(this).data('pid');
		location.href = $js_config.base_url+'projects/index/'+prid;
	})
	$('body').delegate('.col-exp-panel-sharing', 'click', function(){
		var prid = $(this).data('pid');
		location.href = $js_config.base_url+'shares/sharing_map/'+prid;
	})
	$('.users_popovers').popover({
		placement : 'bottom',
		trigger : 'hover',
		html : true,
		container: 'body',
		delay: {show: 50, hide: 400}
	});

	$(".grouplists").hide();

	if( !$(".grpprojectlists").hasClass( "active" ) ){
		$(".grpprojectlists").addClass('active');
	}
	if( $(".grouplists").hasClass( "active" ) ){
		$(".grouplists").removeClass('active');
	}

	$('body').delegate('.showgroupbymember', 'click', function(event){
		event.preventDefault();

		var acrdivid = $(this).data('target');

		/*========= Panel will show selected as green ===================*/
		$(".panel").removeClass('expanded', 300, 'swing');
		setTimeout($.proxy(function(){
			if( $(this).parents('.panel').find('.open_panel').hasClass('collapsed') ) {
				$(this).parents(".panel").removeClass('expanded', 300, 'swing');
			}
			else {
				$(this).parents(".panel").addClass('expanded', 300, 'swing');
			}
		}, this), 100);
		/*================================================================*/

		if( !$(acrdivid).hasClass('in') ){
			$(this).parents('.panel').find('.panel-collapse').collapse('show');
			$('.panel-collapse').not($(this).parents('.panel').find('.panel-collapse')).collapse('hide');
		}

		$(".share_user_search_btn").trigger('click');

		if( $(this).data('mcnt') > 0 ){

			var $parentsss = $(this).parents('.panel');
			datasortingm = $parentsss.find(".user_list_sorting").attr('data-stype');
			$parentsss.find(".user_list_sorting").trigger('click', ["asc"]);
			$(this).parents('div.panel').find(".grouplists").removeClass('active').hide();
			$(this).parents('div.panel').find(".grpprojectlists").addClass('active').show();

		}
		$('.no-result').hide();
	})

	$('body').delegate('.showgroupbygrp', 'click', function(event){
		event.preventDefault();

		var acrdivid = $(this).data('target');

		/*========= Panel will show selected as green ===================*/
		$(".panel").removeClass('expanded', 300, 'swing');
		setTimeout($.proxy(function(){
			if( $(this).parents('.panel').find('.open_panel').hasClass('collapsed') ) {
				$(this).parents(".panel").removeClass('expanded', 300, 'swing');
			}
			else {
				$(this).parents(".panel").addClass('expanded', 300, 'swing');
			}
		}, this), 100);
		/*================================================================*/

		if( !$(acrdivid).hasClass('in') ){
			$(this).parents('.panel').find('.panel-collapse').collapse('show');
			$('.panel-collapse').not($(this).parents('.panel').find('.panel-collapse')).collapse('hide');
		}

		$(".share_user_search_btn").trigger('click');

		if( $(this).data('gcnt') > 0 ){

			var $parentsss = $(this).parents('.panel');
			datasortingp = $parentsss.find(".user_list_sorting").attr('data-stype');
			$parentsss.find(".user_list_sorting").trigger('click', ["asc"]);
			$(this).parents('div.panel').find(".grpprojectlists").removeClass('active').hide();
			$(this).parents('div.panel').find(".grouplists").addClass('active').show();

		}
		$('.no-result').hide();

	})


	function sorting(t, sot){

		var rows = $(t).find('tr').get();

		rows.sort(function(a, b) {

			var keyA = $(a).data('userproject');
			var keyB = $(b).data('userproject');
			//keyA = keyA.toLowerCase();
			//keyB = keyB.toLowerCase();

			keyA = (keyA) ? keyA.toLowerCase() : keyA;
			keyB = (keyB) ? keyB.toLowerCase() : keyB;

			if( sot == "desc" ){
				if (keyA < keyB) return 1;
				if (keyA > keyB) return -1;
			} else {
				if (keyA < keyB) return -1;
				if (keyA > keyB) return 1;
			}
			return 0;

		});
		return rows;
	}

	$('.user_list_sorting').on('click', function(event,sort_val){
		event.preventDefault();
		datasorting = '';
		//datasorting = $(this).attr('data-sptype');

		if( sort_val != undefined ){
			datasorting = sort_val;
		} else {
			datasorting = $(this).attr('data-sptype');
		}

		if( datasorting == "desc" ){
			$(this).text("Sort Asc");
			$(this).attr('data-sptype', 'asc');
		} else {
			$(this).text("Sort Desc");
			$(this).attr('data-sptype', 'desc');
		}
		var $table = $(".custom-table-border.active tbody", $(this).parents('.panel:first'));

		$table.each(function(index, row) {
			$(this).append(sorting(this, datasorting));
		});
	});


	/*====   Searching ===================================   */

	$('body').delegate('.share_user_search_btn', 'click', function(event){
		event.preventDefault();
		$('.searchusershare', $(this).parents('.panel:first')).val('').trigger('keyup');


		$(this).parents('.panel:first').find('.sort-search').find('i').removeClass('fa-times').addClass('fa-search')
		$(this).parents('.panel:first').find('.sort-search').find('button').removeClass('btn-danger').addClass('bg-gray');

	})

	$('.searchusershare').keyup(function(e) {
		e.preventDefault();
		var searchTerms = $(this).val(),
			hashVal = document.location.hash,
			parent = $(this).parents('.panel:first');
			$('.no-result').hide();

		if( searchTerms.length > 0) {
			$('.share_user_search_btn i',parent).removeClass('fa-search').addClass('fa-times');
			$('.share_user_search_btn',parent).removeClass('bg-gray').addClass('btn-danger');
			$( '.dataprjsorting',parent).hide();
		} else {
			$('.share_user_search_btn i',parent).removeClass('fa-times').addClass('fa-search')
			$('.share_user_search_btn',parent).removeClass('btn-danger').addClass('bg-gray');
			$( '.dataprjsorting',parent).show();
		}
		/* $('.panel-body .custom-table-border.active .dataprjsorting', parent).each(function() {
			var hasMatch = searchTerms.length == 0 || $(this).find('.hidden-sm').is(':contains(' + searchTerms  + ')');
			$(this).toggle(hasMatch);
			if( !hasMatch ) {
				$(this).hide()
			}
			else {
				$(this).show()
			}
		});
 */

		var tdtext = $('.panel-body .custom-table-border.active .dataprjsorting', parent).filter(function () {
        //return $(this).text() == searchTerms;
        return $(this).text().toLowerCase().indexOf(searchTerms.toLowerCase())>=0;
		});



		tdtext.each(function(){
			$(this).show();

		})


		if($('.panel-body .custom-table-border.active .dataprjsorting:visible', parent).length <= 0) {
		//if($('.dataprjsorting:visible', parent).length <= 0) {
			$('.no-result', parent).show();
		}
	});


		$.current_delete = {};
        $('body').delegate('.delete-an-item', 'click', function(event) {
            console.log(' delete')
            event.preventDefault();
            $.current_delete = $(this);
            console.log('$.current_delete', $.current_delete)
        });

		$('#modal_delete').on('hidden.bs.modal', function () {
			$(this).removeData('bs.modal');
			$(this).find('.modal-content').html('');
			$.current_delete = {};
		});


	$('body').delegate('a .open_panel', 'click', function(event) {

		$('.panel-collapse').not($(this).parents('.panel').find('.panel-collapse')).collapse('hide');
		//$(".panel").removeClass('opened', 300, 'swing');
		$(".panel").removeClass('expanded', 300, 'swing');
		setTimeout($.proxy(function(){
			if( $(this).hasClass('collapsed')) {
				$(this).parents(".panel").removeClass('expanded', 300, 'swing');
				//$(this).parents(".panel").removeClass('opened', 300, 'swing');
			}
			else {
				$(this).parents(".panel").addClass('expanded', 300, 'swing');
				//$(this).parents(".panel").addClass('opened', 300, 'swing');
			}
		}, this), 100);
		event.preventDefault()
	})

});

	jQuery(function($) {
		$('#modal_small').on('hidden.bs.modal', function () {
		$(this).removeData('bs.modal');
		$(this).find('.modal-content').html('');
	})
});
</script>
<div class="modal modal-success fade" id="modal_small" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content border-radius"></div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
<div class="modal modal-danger fade" id="modal_delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content"></div>
	</div>
</div>