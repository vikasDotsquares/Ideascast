<?php
echo $this->Html->css('projects/bs.checkbox');

echo $this->Html->css('projects/bs-selectbox/bootstrap-multiselect');
echo $this->Html->script('projects/plugins/selectbox/bootstrap-multiselect', array('inline' => true));
?>
<style>
	.perm_users {
	    max-height: 291px;
	    min-height: 291px;
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
		border: 1px solid #00c0ef;
		border-radius: 3px;
		padding: 3px;
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
		min-width: 40px;
		border-radius: 50%;
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
    .send-nudge-pop-tabs .filters .btn-default {
        background-color: #ffffff;
    }
    .send-nudge-pop-tabs .filters .btn-default:hover {
        background-color: #f5f5f5;
            border-color: #ddd;
    }

</style>
<?php $current_user_id = $this->Session->read('Auth.User.id');
$current_org = $this->Permission->current_org(); ?>
<?php if( isset($project_id) && !empty($project_id) )  { ?>


<!-- POPUP MODEL BOX CONTENT HEADER -->
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h3 class="modal-title" id="myModalLabel">Quick Project Share</h3>
	</div>

	<!-- POPUP MODAL BODY -->
	<div class="modal-body popup-select-icon">
	<?php $project_detail = getByDbId('Project', $project_id, ['title']); ?>
		<div class="project-name">
			<i class="fa fa-briefcase"></i> <?php echo htmlentities($project_detail['Project']['title']); ?>
		</div>

		<?php
			echo $this->Form->create('Project', array('url' => array('controller' => 'projects', 'action' => 'save_quick_share', $project_id), 'class' => 'form-bordered', 'id' => 'modelFormAddSharing', 'data-async' => ""));
			echo $this->Form->input('Share.share_user_id', [ 'type' => 'hidden',  'value' => $current_user_id ] );
			echo $this->Form->input('Share.project_id', [ 'type' => 'hidden',  'value' => $project_id ] );
		?>
<?php if( isset($perm_users) && !empty($perm_users) ) { ?>
		<div class="form-group">
			<label class="" for="title">Share With:</label>

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
							<button class="btn btn-default clear-filter" type="button"><i class="glyphicon glyphicon-remove-circle"></i></button>
							</span>
						</div>
					</li>
				</ul>-->
				<ul class="list-group perm_users">
					<?php
					if(!empty($perm_users)) {
						foreach($perm_users as $key => $value ) {
							$userDetail = $this->ViewModel->get_user( $key, null, 1 );
							$user_org = $this->Permission->current_org($key);
							$user_image = SITEURL . 'images/placeholders/user/user_1.png';
							$user_name = 'Not Available';
							$job_title = 'Not Available';
							$html = '';
							if( $key != $current_user_id ) {
								$html = ($this->ViewModel->is_project_shared($project_id, $key)) ? CHATHTML($key, $project_id) : CHATHTML($key);
							}
							if(isset($userDetail) && !empty($userDetail)) {
								$user_name = htmlentities($userDetail['UserDetail']['first_name'],ENT_QUOTES) . ' ' . htmlentities($userDetail['UserDetail']['last_name'],ENT_QUOTES);
								$profile_pic = $userDetail['UserDetail']['profile_pic'];
								$job_title = htmlentities($userDetail['UserDetail']['job_title'],ENT_QUOTES);

								if(!empty($profile_pic) && file_exists(USER_PIC_PATH . $profile_pic)) {
									$user_image = SITEURL . USER_PIC_PATH . $profile_pic;
								}
							}
							?>
							<li class="list-group-item users" data-value="">
								<span class="quick-org-icon">
									<img  src="<?php echo $user_image; ?>" class="user-image pophover1 tipText" title="<?php echo $user_name; ?>" align="left" width="40" height="40" data-content="<div class='user-pophover'><p><?php echo $user_name; ?></p><p><?php echo $job_title; ?></p><?php echo $html; ?></div>">
									<?php if($current_org != $user_org){ ?>
										<i class="communitygray18 tipText" title="Not In Your Organization"></i>
									<?php } ?>
								</span>
								<?php echo $value; ?>

								<input type="checkbox" value="<?php echo $key; ?>" class="user-check" name="data[Share][user_id]">
							</li>
							<?php
						}
					} else {
						echo '<li><div class="no-people-text">NO PEOPLE</div></li>';
					}
					?>
				</ul>
			</div>
		</div>

		<div class="form-group">
			<label class=" " for="description">Owner Role:</label>
			<div class="bs-checkbox">
				<label>
					<input type="checkbox" value="1" name="data[Share][project_level]" class="owner_level">
					<span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>&nbsp;
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
		<button type="submit" class="btn btn-primary pull-left disabled" id="advance">Advanced</button>

		<button type="submit"  class="btn btn-success submit_sharing disabled">Submit</button>
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
//$.perm_users_all = <?php //echo json_encode($perm_users_all);?>;
//$.perm_users_HTML = $('.perm_users').html();
$(function(){
	$('a[data-toggle="tab"]').on('show.bs.tab', function (e) {
		$('.list-group.perm_users').scrollTop(0);
		$('.list-group.perm_users').css('overflow', 'none');
		//$('.list-group.perm_users').html('');
		/*$('.list-group.perm_users').scrollTop(0);
		$('.list-group.perm_users').css('overflow', 'none')
		setTimeout(function(){
			$("[id*='paging_page_']").val(0);
			$('.list-group.perm_users').css('overflow', 'auto')
        	$('.list-group.perm_users').html('');
		}, 20)
		$.didScroll = false;*/


        $('.nudge-user-err').html('');
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
	$.loading_data = true;
	$.pageCountUpdate = function(){
		var paging_type = $('#paging_type').val();
		// setTimeout(function(){
			var page = parseInt($('#paging_page_'+paging_type).val());
			var max_page = parseInt($('#paging_max_page').val());
			var last_page = Math.ceil(max_page/$.paging_offset);
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

		//var selectedSkills = $('#sel_skills_nudge').val(),
			//selectedTags = $('#sel_tags_nudge').val(),
			//selectedSubjects = $('#sel_subjects_nudge').val(),
			//selectedDomains = $('#sel_domains_nudge').val(),
			//tag_match = $('#match_all_tag_checkbox').prop('checked') ? 1 : 0,
			//skill_match = $('#match_all_skill_checkbox').prop('checked') ? 1 : 0,
			//subject_match = $('#match_all_subject_checkbox').prop('checked') ? 1 : 0,
			//domain_match = $('#match_all_domain_checkbox').prop('checked') ? 1 : 0,
			var data = {selected: selStr, is_match_all: is_match_all, page: page, type:type};

		//if(selStr.length > 0 || type =='text'){
			$.ajax({
				type: "POST",
				url: $js_config.base_url + "projects/getQuickShareUserList/"+project_id,
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
				if(outerPane.innerHeight() > 0 && ((outerPane.scrollTop() + outerPane.innerHeight()) >= outerPane[0].scrollHeight))
                {
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
	/*if(type == 'text') {
		if(selected == '') {console.log('text');
			$('.perm_users').html($.perm_users_HTML);
			return;
		}
	} else {
		if(selLength == 0) {console.log('Skill/tags');
			$('.perm_users').html($.perm_users_HTML);
			return ;
		}
	}*/
	if(type == 'text') {
		$.ajax({
				url: $js_config.base_url + 'projects/getQuickShareUserList/'+project_id,
				type: 'POST',
				//dataType: 'json',
				data: { type: type, selected: selected, is_match_all: is_match_all, project_id: project_id},
				success: function(response) {
					$('.perm_users').html(response);
				}
			});
	} else {

		$('.list-group.perm_users').html('');

		if(selLength > 0) {
			$.ajax({
				url: $js_config.base_url + 'projects/getQuickShareUserList/'+project_id,
				type: 'POST',
				//dataType: 'json',
				data: { type: type, selected: selected, is_match_all: is_match_all, project_id: project_id},
				success: function(response) {
					$('.perm_users').html(response);
				}
			});
		} else {
			$('.perm_users').html('');
		}
	}

}

$(function() {
	$(window).keydown(function(event){
		if(event.keyCode == 13) {
			event.preventDefault();
			return false;
		}
	});

	$('.pophover').popover({
        placement : 'bottom',
        trigger : 'hover',
        html : true,
		container: 'body',
		delay: {show: 50, hide: 400}
    });

	$('.owner_level').on('change', function(e){
		if( $(this).prop('checked') ) {
			if( $('.perm_users li.list-group-item.active').length > 0 ) {
				$('#advance').addClass('disabled');
				$('.submit_sharing').removeClass('disabled');
			}
		}
		else {
			if( $('.perm_users li.list-group-item.active').length > 0 ) {
				$('#advance').removeClass('disabled');
				$('.submit_sharing').addClass('disabled');
			}
		}
	})
	/* select/deselect list items */
	$('.perm_users li.list-group-item').on('click', function(e){
			e.preventDefault();

			// if( $(e.target).is('img.user-image') )
				// return;
			$('.perm_users li.list-group-item').removeClass('active')
			$('.perm_users li.list-group-item').find('input.user-check').prop('checked', false)
			$(this).toggleClass('active');

			if( $(this).hasClass('active') ) {
				$(this).find('input.user-check').prop('checked', true);
			}
			else {
				$(this).find('input.user-check').prop('checked', false);
			}

			$('#advance').addClass('disabled');
			if( $('.perm_users li.list-group-item.active').length > 0 ) {
				if( !$('.owner_level').prop('checked') ) {
					$('#advance').removeClass('disabled')
				}
				else {
					$('.submit_sharing').removeClass('disabled');
				}
			}
	} )

	/* search within users list */
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


		// return;
		$(this).attr('disabled', true);
		// if( runAjax ) {
			runAjax = false;
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
						// console.log('socket_newuser', response)
						if(response.socket_newuser) {
							// send web notification
							$.socket.emit('socket:notification', response.socket_newuser.socket, function(userdata){});
						}
						if(response.socket_sharing) {
							// send web notification
							setTimeout(function(){
								// console.log('socket_sharing', response.socket_sharing)
								$.socket.emit('socket:notification', response.socket_sharing.socket, function(userdata){});
								$.socket.emit("project:share", {creator: $js_config.USER.id, project: response.socket_sharing.socket.notification.project_id, sharer:response.socket_sharing.socket.received_users });
							}, 1000)
						}
						setTimeout(function(){
							$this.attr('disabled', false);
							// location.reload();
							$('#modal_medium').modal('hide');
						}, 500)
					}
				}
			});
			// end ajax

		// }
	})

	$('#advance').on('click', function(e){
		e.preventDefault();

		if( $('input.user-check:checked').length > 0 )
			window.location = $js_config.base_url + 'shares/index/' + $('input#ShareProjectId').val() +'/' + $('input.user-check:checked').val() +'/1?refer='+window.location.href;
	})



})
</script>
<?php } ?>