<script type="text/javascript" >
$(function(){
	$(".grouplists").hide();

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

	if( !$(".grpprojectlists").hasClass( "active" ) ){
		$(".grpprojectlists").addClass('active');
	}
	if( $(".grouplists").hasClass( "active" ) ){
		$(".grouplists").removeClass('active');
	}



	$('body').delegate('.showgroupbymember', 'click', function(event){
		event.preventDefault();

		if($(this).data('mcnt') < 1){
				return;
			}

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

		$(".share_search_btn").trigger('click');

		if( $(this).data('mcnt') > 0 ){

			 var $parentsss = $(this).parents('.panel');
			datasortingm = $parentsss.find(".list_sorting").attr('data-stype');
		//	$parentsss.find(".list_sorting").trigger('click', ["asc"]);
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

		$(".share_search_btn").trigger('click');

		if( $(this).data('gcnt') > 0 ){

			var $parentsss = $(this).parents('.panel');
			datasortingp = $parentsss.find(".list_sorting").attr('data-stype');
			$parentsss.find(".list_sorting").trigger('click', ["asc"]);


			$(this).parents('div.panel').find(".grpprojectlists").removeClass('active').hide();
			$(this).parents('div.panel').find(".grouplists").addClass('active').show();

		}
		$('.no-result').hide();

	})


})



</script>


<?php // PROJECTS THAT ARE OWNED BY THE CURRENT USER ARE DISPLAYING PROJECTWISE ?>
<?php // Used in my_sharing.ctp as Element View

$current_org = $this->Permission->current_org();
if(isset($permit_data['pp_data']) && !empty($permit_data['pp_data'])) { ?>

	<?php
	foreach($permit_data['pp_data'] as $key => $val ) {
		//
		$UserProject = $val['UserProject'];
		$Project = $val['UserProject']['Project'];
		$User = $val['User'];
		$ProjectPermission = $val['ProjectPermission'];

		$permit_users = $this->ViewModel->project_sharing_users( $UserProject['id'] );

		$openProjectPanel = $project_key = 0;

		if( isset($this->params['pass'][0]) && !empty($this->params['pass'][0]) ) {
			$user_project_id = project_upid($Project['id']);
			if( $Project['id'] == $this->params['pass'][0] ) {
				$openProjectPanel = $this->params['pass'][0];
				$project_key = $this->params['pass'][0];
			}
		}
		$total_users = 0;
		foreach($permit_users['Permissions'] as $ukey => $uval ) {
			if( isset($uval['ProjectPermission']) && !empty($uval['ProjectPermission']) ) {
				$total_users++;
			}
		}
 if(isset($Project) && !empty($Project)){

	$totalGroupUsersCount = 0;

	$grp_id = user_group_count($this->Session->read("Auth.User.id"),project_upid($Project['id']));


	$projectGroupDetail = user_group_details($this->Session->read("Auth.User.id"),project_upid($Project['id']));

	$allgroupusrescnt = 0;
	$allgroupusres = null;
	if( isset( $projectGroupDetail ) && !empty($projectGroupDetail) ){
		foreach($projectGroupDetail as $gkey => $gval ) {
		// echo $gval['ProjectGroup']['id']."<br />";
				$allgroupusres = group_users_all($gval['ProjectGroup']['id']);
				$allgroupusrescnt +=  (isset($allgroupusres) && !empty($allgroupusres))?  count($allgroupusres) : 0;
		}
	}


	$totalGroupUsersCount = $allgroupusrescnt+$total_users;
?>
	<div class="panel panel-default">

		<div class="panel-heading" data-key="<?php echo $project_key; ?>">
			<h4 class="panel-title">
				<div class="project-net-title">
				<a href="javascript:;" class="nopadding-right hover-grn" >
					<i class="pjblack"></i> <span class="open_panel" data-toggle="collapse" data-parent="#project_accordion"  data-target="#collapse_<?php echo $key; ?>_project"><?php echo htmlentities($Project['title']); ?>:</span>
					<?php //echo ($total_users > 1) ? $total_users . ' People' :  $total_users . ' Person'; ?>
				</a><span class="sharedlinks"> <span class="showgroupbymember" data-mcnt="<?php echo $total_users;?>"><?php echo ($total_users > 1) ? 'Individual Shares: '.$total_users :  'Individual Shares: '.$total_users; ?></span> | <span <?php if( $grp_id > 0){ ?> class="showgroupbygrp" <?php } else { ?>style="cursor:default;"<?php }?> data-gcnt="<?php echo $grp_id; ?>">Group Shares: <?php echo $grp_id; ?></span> | <span <?php if( $totalGroupUsersCount > 0){ ?> class="showgroupbytotalgrp" <?php } else { ?>style="cursor:default;"<?php }?> data-gcnt="<?php echo $totalGroupUsersCount; ?>">Total Team Member Shares: <?php echo $totalGroupUsersCount;?></span>
				</span>
				</div>

				<!-- <i class="pull-right plus-minus-icon" data-plus="&#xf067;" data-minus="&#xf068;"></i>
				<span class="cst" href="login.php"><i class="fa fa-key"></i><span>Login</span></span> -->
				<span class="project-net-btn">


					<span class="btn btn-xs col-exp-panel tipText" title="Open Project" data-pid="<?php echo $Project['id'];?>">
						<i class="openblack" aria-hidden="true"></i>
					</span>

					<a title="Sharing Map" class="btn btn-xs tipText pull-right view_share_map" title="" href="<?php echo Router::Url(array('controller' => 'shares', 'action' => 'sharing_map', $Project['id'], 'admin' => FALSE ), TRUE); ?>" > <i class="fbtn mysharingblack18" style="font-size: 11px"> </i></a>
				</span>

			</h4>

		</div>

		<div id="collapse_<?php echo $key; ?>_project" class="panel-collapse collapse <?php if( $openProjectPanel <= 0 ) { ?> close_panel <?php } ?>">
			<div class="panel-body">

				<div class="sort-search  clearfix">
				   <div class="pull-right sort-search-inside" style="">
					  <div class="input-group">
						 <span class="input-group-btn">
						 <a href="#" class="btn btn-primary list_sorting" style="
							" data-stype="desc" >Sort Desc</a>
						 </span>
						 <input type="text" class="form-control searchprojectshare" name="" style=""><span class="input-group-btn">
						 <button class="btn bg-gray share_search_btn" type="button" style="display: inline-block;"><i class="fa fa-search"></i></button>
						 </span>
					  </div>
				   </div>
				</div>
<div style="max-height: 300px; overflow-y: auto;">
				<div class="table-responsive">
<?php
			if(isset($User['id'])){

		 ?>
					<table class="table table-bordered custom-table-border grpprojectlists" data-id="" >
						<thead>
							<tr>
								<th width="25%"   class="text-left">Shared With</th>
								<th width="20%" class="text-center">Shared On</th>
								<th width="20%" class="text-center">Role Level</th>
								<th width="15%" class="text-center">Propagation</th>
								<th width="20%" class="text-center" >Action</th>
							</tr>
						</thead>
						<tbody>


				<?php if( isset($permit_users['user_count']) && !empty($permit_users['user_count']) ) {

					foreach($permit_users['Permissions'] as $ukey => $uval ) {
						$unbind = [
									'hasOne' => ['UserInstitution'],
									'hasMany' => ['UserProject', 'UserPlan', 'UserTransctionDetail'],
								];
						$permit_user_data = $this->ViewModel->get_user( $uval['ProjectPermission']['user_id'], $unbind, 1 );


				if( isset($uval['ProjectPermission']) && !empty($uval['ProjectPermission']) ) {
						$project_permit = $uval['ProjectPermission'];
						$pic = $permit_user_data['UserDetail']['profile_pic'];
						// pr($permit_user_data);
						$profiles = SITEURL . USER_PIC_PATH . $pic;

						$current_user_id = $this->Session->read('Auth.User.id');

						if (!empty($pic) && file_exists(USER_PIC_PATH . $pic)) {
							$profiles = SITEURL . USER_PIC_PATH . $pic;
						} else {
							$profiles = SITEURL . 'img/image_placeholders/logo_placeholder.gif';
						}

						$html = '';
						if( $permit_user_data['UserDetail']['user_id'] != $current_user_id ) {
							$html = CHATHTML($permit_user_data['UserDetail']['user_id'], $Project['id']);
						}
					?>
					<tr class="datasorting" data-user="<?php echo $permit_user_data['UserDetail']['first_name']." ".$permit_user_data['UserDetail']['last_name']; ?>">
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
						 <?php /* ?>
						<a href="#" data-remote="<?php echo Router::url(array('controller' => 'shares', 'action' => 'show_profile', $permit_user_data['UserDetail']['user_id'])); ?>" data-content="<div><p><?php echo $permit_user_data['UserDetail']['first_name']." ".$permit_user_data['UserDetail']['last_name']; ?></p><p><?php echo htmlentities($permit_user_data['UserDetail']['job_title']); ?></p><?php echo $html; ?></div>" align="left" class="" data-original-title="" title=""  data-target="#popup_modal" data-toggle="modal" >

							<?php
							$content = "<div><p>".$permit_user_data['UserDetail']['first_name']." ".$permit_user_data['UserDetail']['last_name']."</p><p>".$permit_user_data['UserDetail']['job_title']."</p>".$html."</div>";
							echo $this->Html->image($profiles, array("data-content" => $content, "class" => "user-image comment-people-pic border-darks pophover-extra"));
							?>
						</a>
							<span class="hidden-sm"><?php echo $permit_user_data['UserDetail']['first_name']; ?><br /><?php echo $permit_user_data['UserDetail']['last_name']; ?></span>
							<?php */ ?>

						</td>

						<td class="text-center"><?php
						//echo (isset($project_permit['created']) && !empty($project_permit['created'])) ? date('d M, Y g:iA', strtotime($project_permit['created'])) : "N/A";
						echo (isset($project_permit['created']) && !empty($project_permit['created'])) ? $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($project_permit['created'])),$format = 'd M, Y g:iA') : "N/A";

						?></td>

						<td class="text-center"><?php echo (isset($project_permit['project_level']) && $project_permit['project_level'] == 1) ? 'Owner' : ( ($project_permit['project_level'] == 0) ? 'Sharer': 'N/A' ); ?></td>

						<td class="text-center"><?php echo (isset($project_permit['share_permission']) && $project_permit['share_permission'] == 1) ? 'On' : ( ($project_permit['share_permission'] == 0) ? 'Off': 'N/A' ); ?></td>

						<td class="text-center">

								<a class="btn btn-xs tipText" title="Edit Sharing Permissions" href="<?php echo Router::Url(array('controller' => 'shares', 'action' => 'update_sharing', $Project['id'], $permit_user_data['User']['id'], 2, $project_permit['id'], 'admin' => FALSE ), TRUE); ?>" >
									<i class="mysharingblack18"></i>
								</a>
							<?php

							if( ( isset($project_permit['project_level']) && empty($project_permit['project_level'])  ) && ( isset($project_permit['share_permission']) && $project_permit['share_permission'] == 1 ) ) { ?>
								<a class="btn btn-xs tipText"  title="Edit Propagation Permissions" href="<?php echo Router::Url(array('controller' => 'shares', 'action' => 'update_propagation', $Project['id'], $permit_user_data['User']['id'], 2, 'admin' => FALSE ), TRUE); ?>" >
									<i class="propagationblack"></i>
								</a>
							<?php } ?>

						</td>
					</tr>

				<?php
						}
					}
				} ?>
				<tr class="no-result" style="display: none;">
					<td colspan="5" align="center" style="font-size: 16px; font-weight: normal;">No Result</td>
				</tr>
				</tbody>
			</table>
					<?php  }else{  //echo '<div style="margin: 10px; padding-left: 6px;">You have not shared any Projects  with other users.</div>';
					}  ?>

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


						// pr($groupusers);

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
									<tr class="datasorting" data-user="<?php echo htmlentities($gval['ProjectGroup']['title']); ?>" >
										<td class="text-left"><span class="hidden-sm"><?php echo htmlentities($gval['ProjectGroup']['title'],ENT_QUOTES, "UTF-8");?></span></td>
										<td class="text-center"><span>

											<a class="btn btn-xs users_popovers" id=""  data-content="<?php echo $userHtml;?>" data-original-title="" title="">
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

											<a title="Delete" class="btn btn-xs tipText  delete-an-item" title="" href="" data-target="#modal_delete" data-toggle="modal" data-remote="<?php echo Router::Url(array('controller' => 'shares', 'action' => 'delete_an_item', $pdata['id'], $gval['ProjectGroup']['id'], 'admin' => FALSE ), TRUE); ?>" > <i class="deleteblack"></i></a>

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
			<?php  } else {  //echo '<div  style="margin: 10px;">You have not shared any Projects  with other users.</div>';
			}  ?>
			</div>
			</div>
			</div>
		</div>

	</div>

<?php } } } else{ ?>
<div  style=" padding-left: 6px;">You have not shared any Projects you created.</div>
<?php } ?>
<?php /*<div class="abshowhoga" style="display: none;height: 30px;">
	<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
		<div class="no-data"> No Project Found </div>
	</div>
</div>*/?>

<script type="text/javascript">
$(function(){


	$('body').delegate('.col-exp-panel', 'click', function(){
		var prid = $(this).data('pid');
		location.href = $js_config.base_url+'projects/index/'+prid;
	})


	$('.users_popovers').popover({
		placement : 'bottom',
		trigger : 'hover',
		html : true,
		container: 'body',
		delay: {show: 50, hide: 400}
	});

	function sorting(t, sot1){
		var rows = $(t).find('tr').get();

		rows.sort(function(a, b) {

			var keyAb = $(a).data('user');
			var keyBc = $(b).data('user');
			keyAb = (keyAb) ? keyAb.toLowerCase() : keyAb;
			keyBc = (keyBc) ? keyBc.toLowerCase() : keyBc;
			if( sot1 == "desc" ){
				if (keyAb < keyBc) return 1;
				if (keyAb > keyBc) return -1;
			} else {
				if (keyAb < keyBc) return -1;
				if (keyAb > keyBc) return 1;
			}
			return 0;

		});

		return rows;
	}


	$('body').delegate('.list_sorting', 'click', function(event, sort_val){

		event.preventDefault();
		datasorting = '';

		if( sort_val != undefined ){
			datasorting = sort_val;
		} else {
			datasorting = $(this).attr('data-stype');
		}

		if( datasorting == "desc" ){
			$(this).text("Sort Asc");
			$(this).attr('data-stype', 'asc');
		} else {
			$(this).text("Sort Desc");
			$(this).attr('data-stype', 'desc');
		}


		if( $(".custom-table-border").hasClass('active') ){

			var $table = $(".custom-table-border.active tbody", $(this).parents('.panel:first'));
			$table.each(function(index, row) {
				$(this).append(sorting(this, datasorting));
			});
		}

	});


	/*====   Searching ===================================   */

	$('body').delegate('.share_search_btn', 'click', function(event){
		event.preventDefault();
		$('.searchprojectshare', $(this).parents('.panel:first')).val('').trigger('keyup');

		$(this).parents('.panel:first').find('.sort-search').find('i').removeClass('fa-times').addClass('fa-search')
		$(this).parents('.panel:first').find('.sort-search').find('button').removeClass('btn-danger').addClass('bg-gray');

	})

	$('.searchprojectshare').keyup(function(e) {
		e.preventDefault();

		var searchTerms = $(this).val(),
			parent = $(this).parents('.panel:first');
		// $('.no-result', parent).hide();
		$('.no-result').hide();

		if( searchTerms.length > 0) {
				$('.share_search_btn i', parent).removeClass('fa-search').addClass('fa-times');
				$('.share_search_btn', parent).removeClass('bg-gray').addClass('btn-danger');
				$( '.datasorting',parent).hide();
		}
		else {
			$('.share_search_btn i', parent).removeClass('fa-times').addClass('fa-search')
			$('.share_search_btn', parent).removeClass('btn-danger').addClass('bg-gray');
			$( '.datasorting',parent).show();
		}

		/*
		$('.panel-body .table-responsive .custom-table-border.active .datasorting', parent).each(function() {
			var hasMatch = searchTerms.length == 0 || $(this).find('.hidden-sm').is(':contains(' + searchTerms  + ')');
			$(this).toggle(hasMatch);
			if( !hasMatch ) {
				$(this).hide()
			}
			else {
				$(this).show()
			}

		}); */
		var tdtext = $('.panel-body .table-responsive .custom-table-border.active .datasorting').filter(function () {
        //return $(this).text() == searchTerms;
        return $(this).text().toLowerCase().indexOf(searchTerms.toLowerCase())>=0;
		});



		tdtext.each(function(){
			$(this).show();

		})

		if($('.panel-body .table-responsive .custom-table-border.active .datasorting:visible', parent).length <= 0) {
			$('.no-result', parent).show();
		}


	});

/* 		$('.project-net-title .sharedlinks .projectbyuser').each( function(event){

				if( $(this).data('mcnt') > 0 ){

				var $parentsss = $(this).parents('.panel');
				datasortingm = $parentsss.find(".user_list_sorting").attr('data-stype');
				$parentsss.find(".user_list_sorting").trigger('click', ["asc"]);
				$(this).parents('div.panel').find(".grouplists").removeClass('active').hide();
				$(this).parents('div.panel').find(".projectlists").addClass('active').show();

				}else{
				$(this).parents('div.panel').find(".projectlists").removeClass('active').hide();
				$(this).parents('div.panel').find(".grouplists").addClass('active').show();
				}

		}) */





})
</script>
<style>
.no-data {
    color: #bbbbbb;
    font-size: 30px;
    left: 4px;
    position: absolute;
    text-align: center;
    text-transform: uppercase;
    top: 35%;
    width: 98%;
}
.panel-title .sharedlinks {
    display: inline-block;
    font-size: 13px;
    font-weight: normal;
	padding-left: 5px;
}

.accordion-group .panel .panel-heading .panel-title a:not(.view_share_map) {
    display: inline-block;
    font-size: 13px;
    font-weight: normal;
   width: auto;
}


.sharedlinks span:hover {
    text-decoration: underline;
}

.sharedlinks span {
    cursor: pointer;
}


.sharedlinks span.showgroupbytotalgrp:hover {
    text-decoration: none;
}

.sharedlinks span.showgroupbytotalgrp {
    cursor: default;
}


.col-exp-panel {
    float: right !important;
    margin: 9px 10px 0 0;
}
.col-exp-panel i{
	padding: 3px 0 2px 0;
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

.data-table {
    max-height: 385px;
    overflow-y: auto;
    display: block;
}

.sort-search-inside {
    max-width: 300px;
    padding: 6px 1px 2px 10px;
}
.sort-search-inside .list_sorting {
	font-size:12px;
}
.sort-search-inside .input-group .input-group-btn {
    vertical-align: top;
}
.sort-search-inside .input-group .form-control {
	height: 31px;
}
.sort-search-inside .input-group .input-group-btn .share_search_btn{
    font-size: 12px;
}

.no-result {
	display:none;
}

.panel-group{margin-bottom:0px !important;}
.table{margin-bottom:5px !important;}

span[data-mcnt="0"]:hover,span[data-gcnt="0"]:hover {
   text-decoration:none;
}
	
	.grouplists tbody tr td{
		vertical-align: middle;
		padding: 12px 8px
	}	
	
	
	
	
	
	
</style>