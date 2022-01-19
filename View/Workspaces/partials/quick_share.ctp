<?php
echo $this->Html->css('projects/bs.checkbox');

echo $this->Html->css('projects/bs-selectbox/bootstrap-multiselect');
echo $this->Html->script('projects/plugins/selectbox/bootstrap-multiselect', array('inline' => true));
?>
<style>
	.perm_users {
	    max-height: 253px;
	    min-height: 253px;
		/*overflow-x: hidden;
		overflow-y: auto;*/
		margin-bottom: 0;
        overflow: auto;
        clear: both;
	}
	.perm_users .filters {
		margin-bottom: 5px;
	}
	.perm_users_wrapper {
		border-radius: 3px;
		padding: 3px;
		border: 1px solid #00c0ef;
	}
	.list-group.filter {
		margin-bottom: 15px;
	}
	.list-group-item.users {
		cursor: pointer;
		display: flex;
		width: 100%;
		align-items: center;
		  padding-left: 5px;
		padding-right: 10px;
	}
	.list-group-item.filters {
		padding: 0;
		border: medium none;
	}
	.list-group-item.active, .list-group-item.active:focus, .list-group-item.active:hover {
		border-color: #ffffff;
	}
	.list-group-item.users > input {
		display: none;
	}
	.users .user-image {
		margin-right: 5px;
		width: 40px;
		height: 40px;
		border: 2px solid #ccc;
		overflow: hidden;
		border-radius: 50%;
		min-width: 40px;
	}
	.user-pophover p {
		margin-bottom: 2px ;
	}
	.user-pophover p:first-child {
		font-weight: 600 !important;
		margin-bottom: 2px !important;
		width: 170px !important;
		font-size: 14px;
	}
	.user-pophover p:nth-child(2) {
		font-size: 11px;
		margin-bottom: 4px !important;
	}
	.project-name {
		font-size: 14px;
		font-weight: 600;
		margin-bottom: 10px;
		padding: 0 0 5px;
	}

	.sharing-icon {
		display: inline-block;
	}


	.clt {
		margin: 10px 0;
	}
	.clt, .clt ul, .clt li {
		position: relative;
	}

	.clt ul {
	    list-style: none;
	    padding-left: 32px;
		color: #4c4c4c;
	}
	.clt li.child::before, .clt li.child::after {
	    content: "";
	    position: absolute;
	    left: 2px;
	}
	.clt li.child::before {
	    border-top: 1px solid #00C0EF;
	    top: 16px;
	    width: 10px;
	    height: 0;
	}
	.clt li.child::after {
	    border-left: 1px solid #00C0EF;
	    height: 100%;
	    width: 0px;
	    top: 9px;
	}
	.clt ul > li.child:last-child::after {
	    height: 8px;
	}
	.clt input[type="checkbox"] {
	    display: none;
	}

	.users-select, .area-elements {
		border: 1px solid #00c0ef;
		border-radius: 3px;
		min-height: 300px;
		max-height: 300px;
		overflow-x: hidden;
		overflow-y: auto;
	}
	.containers {
		width: 100%;
		float: left;
		margin-bottom: 20px;
	}
	.col-half {
		width: 49%;
		float: left;
		margin: 2px 0 5px 2px;
	}
	.tree_icons {
		border: 1px solid #00c0ef;
		color: #333333;
		font-size: 10px;
		padding: 2px 2px 0;
	}
	.child {
		padding: 5px 0 0;
	}
	.tree-toggle, .tree-toggle:hover {
		color: #333333;
		cursor: pointer;
	}
	.el-title {
		color: #333333;
		cursor: pointer;
	}
	.children {
		margin-bottom: 10px;
	}
	.all-elements, .ico-element {
		cursor: default !important;
	}


	.list-group-item.wsp .tick {
	    background-color: #ffffff;
	    border-radius: 50%;
	    color: #333333;
	    padding: 2px;
	}
	.list-group-item.wsp .tick, .list-group-item.wsp input {
		opacity: 0;
	}
	.list-group-item.wsp.active .tick {
		opacity: 1;
	}
	.list-group-item.wsp {
		font-size: 14px;
		padding: 5px 10px;
	}
	.other-head {
		background: #cccccc none repeat scroll 0 0;
		padding: 5px 10px 2px;
		border-radius: 3px 3px 0 0;
	}
	.other-wsp {
		max-height: 163px;
		overflow-x: hidden;
		overflow-y: auto;
		padding: 5px;
	}
	.other_wsp {
		margin-bottom: 3px;
	}
	.other_wsp .wsp {
		cursor: pointer;
	}
	.no-user-avail {
	    display: block;
	    color: #dd4b39;
	}

	.multiselect-container.dropdown-menu li:not(.multiselect-group) a label.checkbox {
	    padding: 5px 20px 5px 40px !important;
	}
    .send-nudge-tabs-nav {
    padding-bottom: 10px;
    padding-top: 10px;
    padding-left: 10px;
    padding-right: 10px;
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
    /*padding-bottom: 15px;
       min-height: 50px;*/
       padding: 0 0 5px 0;
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
<?php $current_user_id = $this->Session->read('Auth.User.id');
$current_org = $this->Permission->current_org(); ?>
<?php if( isset($project_id) && !empty($project_id) ) {


	$p_permission = $this->Common->project_permission_details($project_id, $current_user_id);

	$user_project = $this->Common->userproject($project_id, $current_user_id);

	$other_wsp = get_project_workspace($project_id);
	// pr($other_wsp, 1);
?>


<!-- POPUP MODEL BOX CONTENT HEADER -->
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h3 class="modal-title" id="myModalLabel">Quick Workspaces Share</h3>
	</div>

	<!-- POPUP MODAL BODY -->
	<div class="modal-body popup-select-icon clearfix">
	<?php $project_detail = getByDbId('Project', $project_id, ['title']); ?>
		<div class="project-name">
			<span class="fa fa-briefcase" ></span> <?php echo strip_tags($project_detail['Project']['title']); ?>
		</div>
		<?php  ?>

		<?php
			echo $this->Form->create('Project', array('url' => array('controller' => 'workspaces', 'action' => 'save_quick_share', $project_id), 'class' => 'form-bordered', 'id' => 'modelFormAddSharing', 'data-async' => ""));
			echo $this->Form->input('Share.share_by_id', [ 'type' => 'hidden',  'value' => $current_user_id ] );
			echo $this->Form->input('Share.project_id', [ 'type' => 'hidden',  'value' => $project_id ] );
			// echo $this->Form->input('Share.workspace_id', [ 'type' => 'hidden',  'value' => $workspace_id ] );
		?>
<?php if( isset($perm_users) && !empty($perm_users) ) { ?>
		<?php if( isset($other_wsp) && !empty($other_wsp) ) { ?>
			<div class="form-group" style="border: 1px solid #cccccc; border-radius: 4px 4px 0 0;">
				<div class="other-head">
					<label class="" for=""> Workspaces:</label>
					<label class="pull-right btn btn-xs btn-default toggle-wsp" for="">
						<i class="fa fa-minus"></i>
					</label>
				</div>

				<div class="other-wsp">
					<ul class="list-group other_wsp">
					<?php foreach($other_wsp as $key => $value ) {
							$workspace = $value['Workspace'];
							// if( $workspace['id'] != $workspace_id ) {
					?>
						<li class="list-group-item wsp <?php echo $workspace['color_code']; ?> <?php if( $workspace['id'] == $workspace_id ) { ?>active<?php } ?>" data-value="">
							<i class="fa fa-check tick"></i>
							<?php echo strip_tags($workspace['title']); ?>
							<input type="checkbox" value="<?php echo $workspace['id']; ?>" class="wsp_check" name="data[Share][wid][]" <?php if( $workspace['id'] == $workspace_id ) { ?>checked="checked"<?php } ?>>
						</li>
					<?php //}
					} ?>
					</ul>
				</div>
			</div>
		<?php } ?>

		<div class="form-group">
			<label class="" for="">Share With:</label>

				<div class="perm_users_wrapper">
					<!-- SJ code starts-->
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
								<ul class="list-group filter">
									<li class="list-group-item filters" value="0">
										<div class="input-group">
											<span class="input-group-addon"><i class="glyphicon glyphicon-search"></i></span>
											<input class="form-control filter-search" placeholder="Search text..." type="text">
											<span class="input-group-btn">
												<button class="btn btn-default clear-filter-share" style=" " type="button"><i class="glyphicon glyphicon-remove-circle"></i></button>
											</span>
										</div>
									</li>
								</ul>
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
							</div>
						</div>
					</div>
					<!-- SJ code ends-->
					<!--<ul class="list-group filter">
						<li class="list-group-item filters" value="0">
							<div class="input-group">
								<span class="input-group-addon"><i class="glyphicon glyphicon-search"></i></span>
								<input class="form-control filter-search" placeholder="Search" type="text">
								<span class="input-group-btn">
									<button class="btn btn-default clear-filter" style=" " type="button"><i class="glyphicon glyphicon-remove-circle"></i></button>
								</span>
							</div>
						</li>
					</ul>-->
					<ul class="list-group perm_users">
						<?php
						//pr($not_shared_user);
						foreach($perm_users as $key => $value ) {
							$userDetail = $value['UserDetail'];
							$user_org = $this->Permission->current_org($userDetail['user_id']);
							$user_image = SITEURL . 'images/placeholders/user/user_1.png';
							$user_name = 'Not Available';
							$job_title = 'Not Available';
							$html = '';
							if( $userDetail['user_id'] != $current_user_id ) {
								$html = ($this->ViewModel->is_project_shared(workspace_pid($workspace_id), $userDetail['user_id'])) ? CHATHTML($userDetail['user_id'], workspace_pid($workspace_id)) : CHATHTML($userDetail['user_id']);
							}
							if(isset($userDetail) && !empty($userDetail)) {
								$user_name = htmlentities($value[0]['name'], ENT_QUOTES);
								$profile_pic = $userDetail['profile_pic'];
								$job_title = htmlentities($userDetail['job_title'], ENT_QUOTES);

								if(!empty($profile_pic) && file_exists(USER_PIC_PATH . $profile_pic)) {
									$user_image = SITEURL . USER_PIC_PATH . $profile_pic;
								}
							}
							?>
							<li class="list-group-item users" data-value=""  >
								<span class="quick-org-icon">
									<img  src="<?php echo $user_image; ?>" class="user-image pophover1 tipText" title="<?php echo $user_name; ?>" align="left" width="40" height="40" data-content="<div class='user-pophover'><p><?php echo $user_name; ?></p><p><?php echo $job_title; ?></p><?php echo $html; ?></div>">
									<?php if($current_org != $user_org){ ?>
										<i class="communitygray18 tipText" title="Not In Your Organization"></i>
									<?php } ?>
								</span>
								<?php echo $user_name; ?>
								<input type="checkbox" data-id="0" value="<?php echo $userDetail['user_id']; ?>" class="user-check" name="data[Share][user_id]" data-status="no">
							</li>
							<?php
						} ?>
					</ul>
				</div>
		</div>

		<div class="form-group" style="min-height: 30px;">
			<label class=" " for=" ">Sharing Permissions:</label>
			<div class="sharing-icon">
				<label class="permissions permit_read btn-circle btn-xs tipText active unchangable" data-original-title="Read" style="pointer-events: none; opacity: 0.4;">
					<input name="data[WorkspacePermission][permit_read]" value="1" id="" type="checkbox" checked >
					<i class="fa fa-eye lbl-icn"></i>
				</label>

				<label class="permissions permit_edit btn-circle btn-xs tipText " data-original-title="Update" style="pointer-events: none; opacity: 0.4;">
					<input name="data[WorkspacePermission][permit_edit]" value="1" type="checkbox" >
					<i class="fa fa-pencil"></i>
				</label>

				<label class="permissions permit_delete btn-circle btn-xs tipText " data-original-title="Delete" style="pointer-events: none; opacity: 0.4;">
					<input name="data[WorkspacePermission][permit_delete]" value="1" type="checkbox" >
					<i class="fa fa-trash"></i>
				</label>

				<label class="permissions permit_add btn-circle btn-xs tipText" title="" data-original-title="Add Tasks" style="pointer-events: none; opacity: 0.4;">
					<input name="data[WorkspacePermission][permit_add]" value="1" id="" type="checkbox">
					<i class="fa fa-plus"></i>
				</label>
			</div>
		</div>

		<?php }else{
		?>
		<div class="no-user-avail">No additional users available for sharing.</div>
		<?php
		} ?>
		<?php echo $this->Form->end(); ?>
	</div>

	<!-- POPUP MODAL FOOTER -->
	<div class="modal-footer">
		<button type="button" class="btn btn-primary pull-left disabled" id="advance">Advanced</button>

		<button type="button"  class="btn btn-success submit_sharing disabled">Submit</button>
		<button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
	</div>
<input type="hidden" id="paging_page_text" value="0" />
<input type="hidden" id="paging_page_skill" value="0" />
<input type="hidden" id="paging_page_tag" value="0" />
<input type="hidden" id="paging_page_subject" value="0" />
<input type="hidden" id="paging_page_domain" value="0" />
<input type="hidden" id="paging_max_page" value="<?php echo count($perm_users_all) ?>" />
<input type="hidden" id="paging_type" value="text" />
<script type="text/javascript" >
	$.target_tab = '#text_search';
	$.relatedTarget_tab = '';
	$(function(){
		/* SJ code for skill and tags*/
		$('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
			$('.list-group.perm_users').scrollTop(0);
			$('.list-group.perm_users').css('overflow', 'none');

			$('.nudge-user-err').html('');
			//$('.perm_users').html('');
			$('.sharing-icon label').css({'pointer-events':'none', 'opacity': 0.4});
			$('.submit_sharing').addClass('disabled');
			$('#advance').addClass('disabled');

			if($(e.relatedTarget).attr("href") === undefined) {
				$.relatedTarget_tab = $.target_tab;
			} else {
				$.relatedTarget_tab = $(e.relatedTarget).attr("href");
			}
			$.target_tab = $(e.target).attr("href");

			if($.target_tab == '#text_search') {
				$('#paging_type').val('text');
				var selStr = $.trim($('.filter-search').val());
				var len = 0;

				apply_user_filter('text', selStr, len);
			} else if($.target_tab == '#skills_search') {
				$('#paging_type').val('skill');
				var sel_users = $('#sel_skills_nudge option:selected');
				var selected = [];
				$(sel_users).each(function(index, brand){
					selected.push([$(this).val()]);
				});
				var selStr = selected.join();

				$('.perm_users').html('<li><div class="no-people-text">NO PEOPLE</div></li>');

				if(selStr.length > 0) {

					apply_user_filter('skill', selStr, selected.length);
				}
			} else if($.target_tab == '#tag_search') {
				$('#paging_type').val('tag');
				var sel_users = $('#sel_tags_nudge option:selected');
				var selected = [];
				$(sel_users).each(function(index, brand){
					selected.push([$(this).val()]);
				});
				var selStr = selected.join();
				$('.perm_users').html('<li><div class="no-people-text">NO PEOPLE</div></li>');
				if(selStr.length > 0) {
					apply_user_filter('tag', selStr, selected.length);
				}
			} else if($.target_tab == '#subjects_search') {
				$('#paging_type').val('subject');
				var sel_users = $('#sel_subjects_nudge option:selected');
				var selected = [];
				$(sel_users).each(function(index, brand){
					selected.push([$(this).val()]);
				});
				var selStr = selected.join();
				$('.perm_users').html('<li><div class="no-people-text">NO PEOPLE</div></li>');
				if(selStr.length > 0) {
					apply_user_filter('subject', selStr, selected.length);
				}
			} else if($.target_tab == '#domains_search') {
				$('#paging_type').val('domain');
				var sel_users = $('#sel_domains_nudge option:selected');
				var selected = [];
				$(sel_users).each(function(index, brand){
					selected.push([$(this).val()]);
				});
				var selStr = selected.join();
				$('.perm_users').html('<li><div class="no-people-text">NO PEOPLE</div></li>');
				if(selStr.length > 0) {
					apply_user_filter('domain', selStr, selected.length);
				}
			}
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

		/* -----Pagination -----*/
		$('.send-nudge-tabs-nav [data-toggle="tab"]').on('click', function(event) {
			$("[id*='paging_page_']").val(0);
			$('#paging_max_page').val(0);
			$.didScroll = false;
		});

		$.paging_offset = $js_config.quick_share_paging;
		//$.paging_offset = 8;
		$.loading_data = true;
		$.pageCountUpdate = function(){
			var paging_type = $('#paging_type').val();
			// setTimeout(function(){
				var page = parseInt($('#paging_page_'+paging_type).val());
				var max_page = parseInt($('#paging_max_page').val());
				var last_page = Math.ceil(max_page/$.paging_offset);
				console.log(page, max_page, last_page, $.paging_offset);
				if(page < last_page - 1 && $.loading_data ){
					$('#paging_page_'+paging_type).val(page + 1);
					offset = ( parseInt($('#paging_page_'+paging_type).val()) * $.paging_offset);
					$.getPosts(offset, paging_type);
					$.didScroll = false;
				}
			// }, 1000)
		}

		$.getPosts = function(page, paging_type){
			$.loading_data = false;
			var $outerPane = $('.list-group.perm_users');
			$('#loading').remove();

			var project_id = <?php echo (is_null($project_id) ? 0 : $project_id);?>;
			var workspace_id = <?php echo (is_null($workspace_id) ? 0 : $workspace_id);?>;

			var is_match_all = 0;
			var selStr = '';
			var type = $('#paging_type').val();
			if(type == 'text') {
				selStr = $('.filter-search').val();
			}
			if(type == 'tag') {
				is_match_all = ($('#match_all_tag_checkbox').is(":checked")) ? 1 : 0;

				var sel_users = $('#sel_tags_nudge option:selected');
				var selected = [];
				$(sel_users).each(function(index, brand){
					selected.push([$(this).val()]);
				});
				selStr = selected.join();
			}
			if(type == 'skill') {
				is_match_all = ($('#match_all_skill_checkbox').is(":checked")) ? 1 : 0;

				var sel_users = $('#sel_skills_nudge option:selected');
				var selected = [];
				$(sel_users).each(function(index, brand){
					selected.push([$(this).val()]);
				});
				selStr = selected.join();
			}
			if(type == 'subject') {
				is_match_all = ($('#match_all_subject_checkbox').is(":checked")) ? 1 : 0;

				var sel_users = $('#sel_subjects_nudge option:selected');
				var selected = [];
				$(sel_users).each(function(index, brand){
					selected.push([$(this).val()]);
				});
				selStr = selected.join();
			}
			if(type == 'domain') {
				is_match_all = ($('#match_all_domain_checkbox').is(":checked")) ? 1 : 0;

				var sel_users = $('#sel_domains_nudge option:selected');
				var selected = [];
				$(sel_users).each(function(index, brand){
					selected.push([$(this).val()]);
				});
				selStr = selected.join();
			}
			var data = {selected: selStr, is_match_all: is_match_all, page: page, type:type};

			//if(selStr.length > 0 || type =='text'){
				$.ajax({
					type: "POST",
					url: $js_config.base_url + "workspaces/getQuickShareUserList/"+project_id+"/"+workspace_id,
					data: data,
					//dataType: 'JSON',
					beforeSend: function(){
						$outerPane.append('<div class="loader_bar" id="loading"></div>');
					},
					complete: function(){
						$('#loading').remove();
					},
					success: function(html) {
						$outerPane.append(html);
						$.loading_data = true;
					}
				});
			//}
		}
		var outerPane = $('.list-group.perm_users' )
		$.didScroll = false;

			outerPane.scroll(function() { //watches scroll of the div
				//outerPane = $(this);
				$.didScroll = true;
			});

			//Sets an interval so your div.scroll event doesn't fire constantly. This waits for the user to stop scrolling for not even a second and then fires the pageCountUpdate function (and then the getPost function)
			setInterval(function() {
				if ($.didScroll){
					$.didScroll = false;
					outerPane = $('.list-group.perm_users' )
					//console.log((outerPane.scrollTop() +'+'+ outerPane.innerHeight()) +'>='+ outerPane[0].scrollHeight);
					if(outerPane.innerHeight() > 0 && ((outerPane.scrollTop() + outerPane.innerHeight()) + 5 >= outerPane[0].scrollHeight)){
						//console.log('scroll');
						$.pageCountUpdate();
					}
			   }
			}, 1);
		/* SJ code for skill and tags*/
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
		var workspace_id = <?php echo (is_null($workspace_id) ? 0 : $workspace_id);?>;

		if(type == 'text') {
			$.ajax({
				url: $js_config.base_url + 'workspaces/getQuickShareUserList/'+project_id+'/'+workspace_id,
				type: 'POST',
				data: { type: type, selected: selected, is_match_all: is_match_all, project_id: project_id, workspace_id: workspace_id},
				success: function(response) {
					$('.perm_users').html(response);
				}
			});
		} else {
			$('.list-group.perm_users').html('');

			if(selLength > 0) {
				$.ajax({
					url: $js_config.base_url + 'workspaces/getQuickShareUserList/'+project_id+'/'+workspace_id,
					type: 'POST',
					data: { type: type, selected: selected, is_match_all: is_match_all, project_id: project_id, workspace_id: workspace_id},
					success: function(response) {
						$('.perm_users').html(response);
					}
				});
			} else {
				$('.perm_users').html('<li><div class="no-people-text">NO PEOPLE</div></li>');
			}
		}
	}

	function checkWSPUser(u_id, wsp_id) {
		$.ajax({
			url: $js_config.base_url + 'workspaces/checkWSPUser',
			type: 'POST',
			dataType: 'JSON',
			data: { u_id: u_id, wsp_id: wsp_id},
			success: function(response) {
				$parent = $('.sharing-icon');
				$parent.find('.permit_read').addClass('active');
				$parent.find('.permit_read').find('input[type=checkbox]').prop('checked', true);
				if(response.success) {
					if(response.content){
						if(response.content.permit_edit == 1) {
							$parent.find('.permit_edit').addClass('active');
							$parent.find('.permit_edit').find('input[type=checkbox]').prop('checked', true);
						}
						if(response.content.permit_add == 1) {
							$parent.find('.permit_add').addClass('active');
							$parent.find('.permit_add').find('input[type=checkbox]').prop('checked', true);
						}
						if(response.content.permit_delete == 1) {
							$parent.find('.permit_delete').addClass('active');
							$parent.find('.permit_delete').find('input[type=checkbox]').prop('checked', true);
						}
					}
				}
			}
		});
	}


$(function() {

	/*
	 * Stop form submit on Enter key press
	 * */
	$(window).keydown(function(event){
		if(event.keyCode == 13) {
			event.preventDefault();
			return false;
		}
	});

	/*
	 * Show/Hide workspaces name panel
	 * */
	$('.toggle-wsp').on('click',function (e) {
		e.preventDefault();

		$('.other-wsp').fadeToggle(500);

		$(this).find('.fa').toggleClass('fa-plus fa-minus')
    });


	/*
	 * select/deselect workspace list items
	 */
	$('.list-group-item.wsp').on('click',function (e) {
		e.preventDefault();

				$('.perm_users li.list-group-item').removeClass('active');
				$('.perm_users li.list-group-item').find('input.user-check').prop('checked', false);
				$('.sharing-icon label').css({'pointer-events':'none', 'opacity': 0.4});
				$('.submit_sharing').addClass('disabled');
				$('#advance').addClass('disabled');

		if( $(this).hasClass('active') ) {
			$(this).find('input[type=checkbox]').prop('checked', false);
			$(this).removeClass("active");
		}
		else {
			$(this).find('input[type=checkbox]').prop('checked', true);
			$(this).addClass("active");
		}

		if( $('.perm_users li.list-group-item.active').length > 0 && $('.other_wsp li.list-group-item.active').length > 0 ) {
			$('.sharing-icon label').css({'pointer-events':'unset', 'opacity': 1});
			$('.submit_sharing').removeClass('disabled');
			$('#advance').removeClass('disabled');
		}
		else {
			$('.sharing-icon label').css({'pointer-events':'none', 'opacity': 0.4});
			$('.sharing-icon label:not(.permit_read)').removeClass('active');

			$('.sharing-icon label:not(.permit_read) input').prop('checked', false);

			$('.submit_sharing').addClass('disabled');
			$('#advance').addClass('disabled');
		}

    });

	$('.pophover').popover({
        placement : 'bottom',
        trigger : 'hover',
        html : true,
		container: 'body',
		delay: {show: 50, hide: 400}
    });

	/*
	* select/deselect users list items
	*/
	$('.perm_users li.list-group-item').on('click', function(e){
			e.preventDefault();
		if( !$(this).hasClass('active') ) {

			$('.perm_users li.list-group-item').removeClass('active');
			$('.perm_users li.list-group-item').find('input.user-check').prop('checked', false);

			$(this).toggleClass('active');

			if( $(this).hasClass('active') ) {
				$(this).find('input.user-check').prop('checked', true);
			}
			else {
				$(this).find('input.user-check').prop('checked', false);
			}

			if( $('.perm_users li.list-group-item.active').length > 0 && $('.other_wsp li.list-group-item.active').length > 0 ) {
				$('.sharing-icon label').css({'pointer-events':'none', 'opacity': 0.4});
				$('.submit_sharing').removeClass('disabled');
				$('#advance').removeClass('disabled');

				//Call Ajax here
				if($('.other_wsp li.list-group-item.active').length == 1) {
					var u_id = $(this).find('input[type=checkbox]:checked').val();
					var wsp_id = $('.other_wsp li.list-group-item.active').find('input[type=checkbox]:checked').val();
					if(u_id > 0 && wsp_id > 0) {
						checkWSPUser(u_id, wsp_id);
					}
				}

			}

			if( $('.perm_users li.list-group-item.active').length > 0 && $('.other_wsp li.list-group-item.active').length > 0 ) {
				$('.sharing-icon label').css({'pointer-events':'unset', 'opacity': 1});
			}
			else {
				$('.sharing-icon label').css({'pointer-events':'none', 'opacity': 0.4});
			}

			if( $('.perm_users li.list-group-item.active').length > 0 && $('.perm_users li.list-group-item.active input').data('status') =='not' ) {
				$('.sharing-icon label').css({'pointer-events':'none', 'opacity': 0.4});
				$('.submit_sharing').addClass('disabled');
				$('#advance').addClass('disabled');
			}

			/* var prms = {'type': ''};
			if( $('input.wsp_check:checked').length > 0 ) {
			}
				prms = {'type': 'all'}

			if( $(this).hasClass('active') ) {
				$('.sharing-icon').html('<i class="fa fa-spinner fa-pulse" style="font-size: 23px;"></i>');
				$.ajax({
					url: $js_config.base_url + 'workspaces/quick_share_permissions/' + $js_config.project_id + '/' + $(this).find('input.user-check').val() + '/' + $js_config.workspace_id,
					type:'POST',
					data: $.param(prms),
					success: function( response, status, jxhr ) {
						setTimeout( function(){
							$('.sharing-icon').html(response);
						}, 200 )
					}
				});
			} */
		}
		/*
		else {
			$(this).removeClass('active');
			$(this).find('input.user-check').prop('checked', false);
			$('.submit_sharing').addClass('disabled');
			$('#advance').addClass('disabled');
			// $('.sharing-icon').html('Select a user');
		} */
	} )

	/*
	 * search within users list
	 */
	$.expr[":"].contains = $.expr.createPseudo(function(arg) {
		return function( elem ) {
			return $(elem).text().toUpperCase().indexOf(arg.toUpperCase()) >= 0;
		};
	});

	$('body').delegate('.clear-filter-share', 'click', function(event){
		event.preventDefault();
		$('.filter-search').val('').trigger('keyup');
		return false;
	})

	$('.filter-search').keyup(function(e) {
		e.preventDefault();
		var searchTerms = $(this).val();

		apply_user_filter('text', searchTerms, searchTerms.length);

		/*$( '.perm_users li.list-group-item').each(function() {

			var that = $(this);

			var hasMatch = searchTerms.length == 0 || that.is(':contains(' + searchTerms  + ')');
			if( hasMatch ) {
				that.show();
			}
			else {
				if( that.hasClass('active') ) {
					that.removeClass('active');
					$(this).find('input.user-check').prop('checked', false);

					$('.submit_sharing').addClass('disabled');
					$('#advance').addClass('disabled');
				}
				that.hide();
			}
		});*/
	});

	/*
	 * Save sharing
	 * */
	$('.submit_sharing').on( "click", function(e) {

		e.preventDefault();

		var $this = $(this),
			$form = $('form#modelFormAddSharing'),
			add_share_url = $form.attr('action'),
			runAjax = true;

		if($( '.perm_users li.list-group-item.active').length <= 0) {
			//$('.error-user').show();
			//return;
		}

		$(this).attr('disabled', true);
		// return;
			$.ajax({
				url: add_share_url,
				type:'POST',
				data: $form.serialize(),
				dataType: 'json',
				beforeSend: function( response, status, jxhr ) {
					// Add a spinner in button html just after ajax starts
					$this.html('<i class="fa fa-spinner fa-pulse"></i>');
				},
				success: function( response, status, jxhr ) {
					$this.html('Submit');
					if(response.success) {
						if(response.content){
							// send web notification
							console.log('socket', response.content)
							$.socket.emit('socket:notification', response.content.socket, function(userdata){});
							$.socket.emit("project:share", {creator: $js_config.USER.id, project: response.content.socket.notification.project_id, sharer:response.content.socket.received_users });
						}
						if(response.socket_newuser) {
							// send web notification
							$.socket.emit('socket:notification', response.socket_newuser.socket, function(userdata){});
						}
						$('#popup_modal').modal('hide');
						$this.attr('disabled', false);
						location.reload();
					}
				}
			});
			// end ajax

	})

	/*
	 * Set url for Advanced button
	 */
	$('#advance').on('click', function(e){
		e.preventDefault();

		if( $('input.user-check:checked').length > 0 ){

			if($('input.user-check:checked').data('status') =='no'){
			window.location = $js_config.base_url + 'shares/index/' + $('input#ShareProjectId').val() +'/' + $('input.user-check:checked').val() +'/1?refer='+window.location.href;
			}else{
			window.location = $js_config.base_url + 'shares/update_sharing/' + $('input#ShareProjectId').val() +'/' + $('input.user-check:checked').val() +'/2/'+$('input.user-check:checked').data('id')+'?refer='+window.location.href;
			}

		}
	})


	/*
	 * --------------------------------------------------------------------------
	 * Toggle permission icons
	 * --------------------------------------------------------------------------
	 */
	$('body').delegate('label.permissions', 'click', function(event) {

		var e = $(this);
		if( e.hasClass('unchangable') ) return;

		var $input = $(this).find('input[type=checkbox]'),
		iName = $input.attr('name'),
		$options = $('.propogate-options');

		$input.prop("checked", !$input.prop("checked"));

		if($input.prop("checked")) {
			$(this).addClass('active');
		}
		else {
			$(this).removeClass('active');
		}

		var $parent = $(this).parent();

		var active_length = ($parent.find('label.active').length) ? $parent.find('label.active').length : 0;

		// if edit permission is to be deactivated then deactivate copy and move permissions also
		if( $(this).is($('.permit_edit')) ) {

			if( !$(this).hasClass( 'active' ) ) {

				// deactivate copy permission
				var $copy = $parent.find('.permit_copy');
				if( $copy.hasClass('active') ){
					$copy.removeClass('active');
					$copy.find('input[type=checkbox]').prop("checked", false);
				}

				// deactivate move permission
				var $move = $parent.find('.permit_move');
				if( $move.hasClass('active') ) {
					$move.removeClass('active');
					$move.find('input[type=checkbox]').prop("checked", false);
				}

			}

		}

		// if edit permission is not activated then restrict move and copy permissions
		if( ( $(this).hasClass('permit_move') ||  $(this).hasClass('permit_copy') ) &&  !$parent.find('.permit_edit').hasClass('active') ) {

			$(this).removeClass('active');
			$input.prop("checked", false);

			return;

		}

		// if clicked other than read permission and read button has not an active class
		// add it manually
		if( active_length > 0 && !$(this).hasClass('permit_read') && !$parent.find('.permit_read').hasClass('active') ) {

			$parent.find('.permit_read').addClass('active');
			$parent.find('.permit_read').find('input[type=checkbox]').prop('checked', true);

		}
		// if only one permission is given but its not the read permission
		else if( $(this).is($('.permit_read')) && active_length > 0 ) {

			$parent.find('.permit_read').addClass('active');
			$parent.find('.permit_read').find('input[type=checkbox]').prop('checked', true);

		}
	})


})
</script>
<?php } ?>