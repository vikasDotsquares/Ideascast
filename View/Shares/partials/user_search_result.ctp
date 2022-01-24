

<?php // PROJECTS THAT ARE OWNED BY THE CURRENT USER ARE DISPLAYING USERWISE ?>
<?php // Used in my_sharing.ctp as Element View ?>
<?php
$shared_project_users = $this->ViewModel->user_sharing_projects( );

 ?>
<div class="panel-wrapper">
<?php
if(isset($shared_project_users['user_count']) && !empty($shared_project_users['user_count'])) {
?>
	<?php $val['user_id'] = 0;
	foreach($shared_project_users['all_users'] as  $vals ) {
 		if(isset($vals) && !empty($vals)){

			$val['user_id'] = $vals;
			$key = $vals;

		$shared_projects = $this->ViewModel->user_sharing_projects( $val['user_id'] );

	?>
		<?php  if(isset($val['user_id'])){
			$userDetail = $this->ViewModel->get_user_data( $val['user_id'] );
			$user_name = '';
			if(isset($userDetail) && !empty($userDetail)) {
				$user_name = $userDetail['UserDetail']['first_name'] . ' ' . $userDetail['UserDetail']['last_name'];
			}

			// user group count with details
			$gropuids = group_by_participants($val['user_id']);



			$total_projects = 0;
			if( isset($shared_projects['project_count']) && !empty($shared_projects['project_count']) ) {

				foreach($shared_projects['shared_projects'] as $pkey => $pval ) {

					$project_data = $this->ViewModel->user_project_permissions( $val['user_id'],$pval['ProjectPermission']['user_project_id'] );

					if( (isset($project_data) && !empty($project_data))) {

						$project_detail = $this->ViewModel->getProjectDetail( project_primary_id($pval['ProjectPermission']['user_project_id']) );

						if( (isset($project_detail) && !empty($project_detail))) {
							$total_projects++;
						}
					}
				}
			}

		?>
	<div class="panel panel-default" data-user="<?php  echo $user_name ?>">
		<div class="panel-heading">
			<h4 class="panel-title">
				<div class="project-net-title">
			    <a style="width:auto; padding-right:0;" href="#" data-remote="<?php echo Router::url(array('controller' => 'shares', 'action' => 'show_profile', $val['user_id'])); ?>"  data-target="#popup_modal" data-toggle="modal" ><i class="fa fa-user text-maroon"></i>
			    </a>
				<a href="javascript:;" class="nopadding-left nopadding-right" >
					<span class="open_panel" data-toggle="collapse" data-parent="#user_accordion" data-target="#collapse_<?php echo $key; ?>_user"><?php  echo $user_name ?>:
					</span>
				</a>
				<span class="sharedlinks"><span class="projectbyuser" data-mcnt="<?php echo $total_projects;?>"><?php echo ($total_projects > 1) ? 'Individual Shares: '.$total_projects :  'Individual Shares: '.$total_projects; ?></span> | <span <?php if( isset($gropuids) && !empty($gropuids)  ){ ?> class="groupbyuser" <?php } else { ?>style="cursor:default;"<?php } ?> data-gcnt="<?php echo ( isset($gropuids) && !empty($gropuids) )? count($gropuids) : 0 ; ?>" >Group Shares:
					<?php

					if( isset($gropuids) && !empty($gropuids) ){
						echo count($gropuids);
					} else {
						echo 0;
					}
					?></span>
				</span>

				</div>
				<span class="project-net-btn">
				<!-- <span class="btn btn-xs btn-default col-exp-panel">
					<i class="fa fa-plus"></i>
				</span>-->
					</span>
<!-- 				<i class="pull-right  plus-minus-icon" data-plus="&#xf067;" data-minus="&#xf068;"></i> -->
			</h4>
		</div>

		<div id="collapse_<?php echo $key; ?>_user" class="panel-collapse collapse" data-value="<?php  echo $user_name ?>">
			<div class="panel-body">

				<div class="sort-search  clearfix">
				   <div class="col-sm-3 pull-right sort-search-inside" style="">
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
			<div style="max-height: 300px; overflow-y: auto;">
				<div class="table-responsive">

					<table class="table table-bordered custom-table-border projectlists" data-id="" >
						<thead>
							<tr>
								<th width="25%" class="text-left">Project</th>
								<th width="20%" class="text-center">Shared On</th>
								<th width="20%" class="text-center">Role Level</th>
								<th width="15%" class="text-center">Propagation</th>
								<th width="20%" class="text-center" >Action</th>
							</tr>
						</thead>
						<tbody>


				<?php

				if( isset($shared_projects['project_count']) && !empty($shared_projects['project_count']) ) {

					foreach($shared_projects['shared_projects'] as $pkey => $pval ) {

						$project_data = $this->ViewModel->user_project_permissions( $val['user_id'],$pval['ProjectPermission']['user_project_id'] );


					if( (isset($project_data) && !empty($project_data))) {

							$project_detail = $this->ViewModel->getProjectDetail( project_primary_id($pval['ProjectPermission']['user_project_id']) );

					if( (isset($project_detail) && !empty($project_detail))) {

							// pr($project_detail, 1);
							$project_permitions = isset($project_data['ProjectPermission']) ? $project_data['ProjectPermission']  : null ;

							$project_detail = isset($project_detail['Project']) ? $project_detail['Project']  : null ;

					?>
						<tr class="dataprjsorting" data-userproject="<?php echo htmlentities($project_detail['title'], ENT_QUOTES); ?>" >
							<td class="text-left hidden-sm-userproject openproject" data-pid="<?php echo $project_detail['id']; ?>"><?php echo html_entity_decode(htmlentities($project_detail['title'], ENT_QUOTES), ENT_QUOTES); ?></td>

							<td class="text-center"><?php
							//echo (isset($project_permitions['created']) && !empty($project_permitions['created'])) ? date('d M, Y g:iA', strtotime($project_permitions['created'])) : "N/A";
							echo (isset($project_permitions['created']) && !empty($project_permitions['created'])) ? $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($project_permitions['created'])),$format = 'd M, Y g:iA') : "N/A";

							?></td>

							<td class="text-center"><?php echo (isset($project_permitions['project_level']) && $project_permitions['project_level'] == 1) ? 'Owner' : ( ($project_permitions['project_level'] == 0) ? 'Sharer': 'N/A' ); ?></td>

							<td class="text-center"><?php echo (isset($project_permitions['share_permission']) && $project_permitions['share_permission'] == 1) ? 'On' : ( ($project_permitions['share_permission'] == 0) ? 'Off': 'N/A' ); ?></td>
								<td class="text-center">

										<a class="btn btn-xs tipText" title="Edit Sharing Permissions" href="<?php echo Router::Url(array('controller' => 'shares', 'action' => 'update_sharing', $project_detail['id'], $val['user_id'], 2, $project_permitions['id'], 'admin' => FALSE ), TRUE); ?>" >
											<i class="mysharingblack18"></i>
										</a>
									<?php

									if( ( isset($project_permitions['project_level']) && empty($project_permitions['project_level'])  ) && ( isset($project_permitions['share_permission']) && $project_permitions['share_permission'] == 1 ) ) { ?>
										<a class="btn btn-xs tipText"  title="Edit Propagation Permissions" href="<?php echo Router::Url(array('controller' => 'shares', 'action' => 'update_propagation', $project_detail['id'], $val['user_id'], 2, 'admin' => FALSE ), TRUE); ?>" >
											<i class="propagationblack"></i>
										</a>
									<?php } ?>

								</td>
							</tr>

				<?php
						} }
					}
				} ?>
							<tr class="no-result" style="display: none;">
								<td colspan="5" align="center" style="font-size: 16px; font-weight: normal;">No Result</td>
							</tr>
						</tbody>
					</table>


		<?php if( isset($gropuids) && !empty($gropuids) ){ ?>
				<table class="table table-bordered custom-table-border grouplists" >
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
					foreach($gropuids as $gkey => $gval ) {

						  $grp_id = $gval['ProjectGroup']['id'];
						  $groupusers = group_users_all($gval['ProjectGroup']['id']);

						  $pdata = project_primary_id($gval['ProjectGroup']['user_project_id'], 1);

						  //pr($pdata);

						  $group_permission = $this->Group->group_permission_details($gval['ProjectGroup']['user_project_id'], $grp_id);
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
									<tr class="dataprjsorting" data-userproject="<?php echo htmlentities($gval['ProjectGroup']['title']); ?>" >
										<td class="text-left"><span class="hidden-sm-userproject"><?php echo htmlentities($gval['ProjectGroup']['title'],ENT_QUOTES, "UTF-8");?></span></td>
										<td class="text-center"><span>

											<a class="btn  btn-xs users_popovers" id=""  data-content="<?php echo $userHtml;?>" data-original-title="" title="">
										<i class="broupblack"></i>
									</a>

										</span></td>
										<td class="text-center"><?php
											echo (isset($gval['ProjectGroup']['created']) && !empty($gval['ProjectGroup']['created'])) ? $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($gval['ProjectGroup']['created'])),$format = 'd M, Y g:iA') : "N/A"; ?>
										</td>

										<td class="text-center">
										<?php
										echo ( isset($group_permission) && !empty($group_permission['ProjectPermission']['project_level']) && $group_permission['ProjectPermission']['project_level'] == 1) ? 'Owner' : ( ( isset($group_permission['ProjectPermission']['project_level']) && $group_permission['ProjectPermission']['project_level'] == 0) ? 'Sharer': 'N/A' );  ?>
										</td>
										<td class="text-center">
											<?php echo (isset($gval['ProjectGroup']['share_permission']) && $gval['ProjectGroup']['share_permission'] == 1) ? 'On' : ( ($gval['ProjectGroup']['share_permission'] == 0) ? 'Off': 'Off' );
											?>
										</td>

										<td class="text-center">

											<a title="Delete" class="btn btn-xs tipText delete-an-item" title="" href="" data-target="#modal_delete" data-toggle="modal" data-remote="<?php echo Router::Url(array('controller' => 'shares', 'action' => 'delete_an_item', $pdata['id'], $gval['ProjectGroup']['id'], 'admin' => FALSE ), TRUE); ?>" > <i class="deleteblack"></i></a>

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
	<?php } ?>
<?php } } }else{ ?>
<div  style="margin-bottom: -17px; padding-left: 6px;">You have not shared any Projects you created.</div>
<?php } ?>
</div>
<script type="text/javascript">
$(function(){

	var alphabeticallyOrderedDivs = $("#user_accordion .panel").sort(function(a, b) {
	    return String.prototype.localeCompare.call($(a).data('user').toLowerCase(), $(b).data('user').toLowerCase());
	});

	var container = $(".panel-wrapper");
	container.detach().empty().append(alphabeticallyOrderedDivs);
	$('#user_accordion').append(container);


	$(".openproject").css("cursor", "pointer");
	$('body').delegate('.openproject', 'click', function(){
		var prid = $(this).data('pid');
		location.href = $js_config.base_url+'projects/index/'+prid;
	})

	function sorting(t, sot){

		var rows = $(t).find('tr').get();

		rows.sort(function(a, b) {

			var keyA = $(a).data('userproject');
			var keyB = $(b).data('userproject');
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
	$('.user_list_sorting').on('click', function(event, sort_val){
		event.preventDefault();
		datasorting = '';

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
			// $('.no-result', parent).hide();
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
		/* $('.panel-body .table-responsive .custom-table-border.active .dataprjsorting', parent).each(function() {
			var data = $(this).find('.hidden-sm-userproject').data(),
				dataVal = data.value;

			var hasMatch = searchTerms.length == 0 || $(this).find('.hidden-sm-userproject').is(':contains(' + searchTerms  + ')');
			$(this).toggle(hasMatch);
			if( !hasMatch ) {
				$(this).hide()
			}
			else {
				$(this).show()
			}
		}); */
		
		var tdtext = $('.panel-body .table-responsive .custom-table-border.active .dataprjsorting').filter(function () {
        //return $(this).text() == searchTerms;
        return $(this).text().toLowerCase().indexOf(searchTerms.toLowerCase())>=0;
		});
		
		tdtext.each(function(){
			$(this).show();
			
		})

		if($('.panel-body .table-responsive .custom-table-border.active .dataprjsorting:visible', parent).length <= 0) {
			$('.no-result', parent).show();
		}
	});


	/*===========================================================*/
		$(".grouplists").hide();
		if( !$(".projectlists").hasClass( "active" ) ){
			$(".projectlists").addClass('active');
		}
		if( $(".grouplists").hasClass( "active" ) ){
			$(".grouplists").removeClass('active');
		}




		$('.project-net-title .sharedlinks .projectbyuser').each( function(event){
			
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
			
		})	
			
		$('body').delegate('.projectbyuser', 'click', function(event){
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

			$(".share_user_search_btn").trigger('click');

			if( $(this).data('mcnt') > 0 ){

				var $parentsss = $(this).parents('.panel');
				datasortingm = $parentsss.find(".user_list_sorting").attr('data-stype');
				$parentsss.find(".user_list_sorting").trigger('click', ["asc"]);
				$(this).parents('div.panel').find(".grouplists").removeClass('active').hide();
				$(this).parents('div.panel').find(".projectlists").addClass('active').show();

			}
			$('.no-result').hide();
		})

		$('body').delegate('.groupbyuser', 'click', function(event){
			event.preventDefault();

			/* if($(this).parents('.panel-title').find('.open_panel').attr('aria-expanded') === undefined || !$(this).parents('.panel-title').find('.open_panel').attr('aria-expanded')){
				return;
			} */

			if($(this).data('gcnt') < 1){
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



			$(".share_user_search_btn").trigger('click');

			if( $(this).data('gcnt') > 0 ){

				var $parentsss = $(this).parents('.panel');
				datasortingp = $parentsss.find(".user_list_sorting").attr('data-stype');
				$parentsss.find(".user_list_sorting").trigger('click', ["asc"]);


				$(this).parents('div.panel').find(".projectlists").removeClass('active').hide();
				$(this).parents('div.panel').find(".grouplists").addClass('active').show();

			}
			$('.no-result').hide();

	   })

	   	$('.users_popovers').popover({
		placement : 'bottom',
		trigger : 'hover',
		html : true,
		container: 'body',
		delay: {show: 50, hide: 400}
		});

	/*===========================================================*/

})
</script>
<style>
.grouplists{
	display:none;
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
.col-exp-panel {
    float: right !important;
    margin: 9px 10px 0 0;
}
.table{margin-bottom:5px !important;}

.panel.panel-default.expanded .project-net-title a {
    color: #fff;
}


span[data-mcnt="0"]:hover,span[data-gcnt="0"]:hover {
   text-decoration:none;
   cursor:default;
}

</style>
