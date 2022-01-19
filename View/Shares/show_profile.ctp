<?php
	echo $this->Html->css('projects/tokenfield/bootstrap-tokenfield');
	echo $this->Html->script('projects/plugins/tokenfield/bootstrap-tokenfield-tags', array('inline' => true));
	echo $this->Html->css('projects/competency');

	$show_data = $show_data[0];
	$u_data = $show_data['u'];

	$ud_data = $show_data['ud'];
	$user_id = $ud_data['user_id'];
	$org_data = !empty($show_data['org']) ? $show_data['org'] : [];
	$ot_data = $show_data['ot'];
	$countries_data = $show_data['countries'];
	$dept_data = $show_data['dept'];
	$loc_data = $show_data['loc'];
	$lt_data = $show_data['lt'];

	$dotted_lines = (!empty($show_data[0]['dotted_users'])) ? json_decode($show_data[0]['dotted_users'], true) : false;
	$selected_stories = (!empty($show_data[0]['selected_stories'])) ? json_decode($show_data[0]['selected_stories'], true) : [];

	$user_skills = $this->Permission->get_user_skills($ud_data['user_id']);
	$user_subjects = $this->Permission->get_user_subjects($ud_data['user_id']);
	$user_domains = $this->Permission->get_user_domains($ud_data['user_id']);

	$org_data = htmlentity($org_data);
	$dept_data = htmlentity($dept_data);
	$loc_data = htmlentity($loc_data);
	$selected_stories = htmlentity($selected_stories, 'name');
	function asrt($a, $b) {
		$t1 = iconv('UTF-8', 'ASCII//TRANSLIT', $a['name']);
		$t2 = iconv('UTF-8', 'ASCII//TRANSLIT', $b['name']);
	    return strcasecmp($t1, $t2);
	}
	usort($selected_stories, 'asrt');
	if( isset($user_skills) && !empty($user_skills) ){
		foreach ($user_skills as $key => $value) {
			$updated = htmlentities($value['lib']['title'], ENT_QUOTES, "UTF-8");
			$user_skills[$key]['lib']['title'] =  $updated ;
		}
	}
	if( isset($user_subjects) && !empty($user_subjects) ){
		foreach ($user_subjects as $key => $value) {
			$updated = htmlentities($value['lib']['title'], ENT_QUOTES, "UTF-8");
			$user_subjects[$key]['lib']['title'] =  $updated ;
		}
	}
	if( isset($user_domains) && !empty($user_domains) ){
		foreach ($user_domains as $key => $value) {
			$updated = htmlentities($value['lib']['title'], ENT_QUOTES, "UTF-8");
			$user_domains[$key]['lib']['title'] =  $updated ;
		}
	}
// pr($selected_stories);

	// uasort($org_data, "compareASCII");

// mpr($org_data, $dept_data, $loc_data, $user_skills, $user_subjects, $user_domains);

$current_user_id = $u_data['id'];
?>
<style>
	.loc-name-list .loc-thumb, .loc-name-list .loc-name, .show-user-popup {
		cursor: pointer;
	}
	/*.user-profile {
		border-bottom: 1px solid #ccc;
	}*/

	.profile-view-header .modal-footer{
		margin-top: 0px;
	}
	.profile-view-header .modal-body {
		padding: 15px;
	}

	#tab_skills .form-group.userskills, #tab_subjects .form-group.userbio, #tab_domains .form-group.userinterest, #tab_behaviors .form-group.userinterest, #tab_bio .form-group.userbio, #tab_interests .form-group.userinterest{
		margin-bottom: 0;
	}

	.user-profile .form-group {
		margin-bottom: 10px;
	}
	.user-profile .main-profile-group {
		margin-bottom: 4px;
	}


	.user-profile .control-label {
		display: block;
		margin-bottom: 7px;
		font-size:13px;
		font-weight: 600;
	}
	.user-profile .skill-bio-box {
		background-color: #fff;
		display: block;
		max-height: 136px;
		min-height: 130px;
		overflow-x: hidden;
		overflow-y: auto;
		padding: 5px 8px;
	}

	.userskills .skill-bio-box, .userbio .skill-bio-box, .userinterest .skill-bio-box {
		max-height: 332px;
		min-height: 332px;
		width: 100%;
	}

	.skill-bio-box span.skill {
	    float: left;
		width: 100%;
		margin-bottom: 6px;
	}

	.skill-bio-box span.skill:hover {
	    background-color: #fff;
	}

	.skill-bio-box span.skill a{
	    padding-right: 0px;
	    line-height: 18px;
	}
	.skill-bio-box span.skill i {
		font-size: 13px;
		min-width: 8px;
		margin-right: 0;
	}
	.skill-bio-box span.skill .fa.fa-file-pdf-o:before {
	    padding-top: 3px;
	    display: inline-block;
	}
	/*********tabs*********/
	.skill-bio-interest {
	    padding: 0;
	}
	.skill-bio-interest .nav.nav-tabs.tab-list {
	    background-color: transparent;
	    margin-bottom: 5px;
	}
	.skill-bio-interest .nav-tabs {
	    border-bottom: medium none;
	}
	.skill-bio-interest .nav.nav-tabs.tab-list li {
	    border-right:1px solid #dcdcdc;
	}
	.skill-bio-interest .nav.nav-tabs.tab-list li:last-child {
	    border-right: none;
	}

	.skill-bio-interest .nav.nav-tabs.tab-list > li.active {
	    background-color: transparent;
	}
	.skill-bio-interest .nav.nav-tabs li {
	    margin-bottom: 0;
	}
	.tab-content.skill-bio-interest-content {
	    margin-top: -3px;
	}


	.skill-bio-interest .nav-tabs.tab-list > li > a {
	    color: #67A028;
	}

	.skill-bio-interest .nav > li > a:hover{
		background-color: transparent;
	}
	.skill-bio-interest .nav-tabs.tab-list > li.active > a, .skill-bio-interest .nav-tabs.tab-list > li.active > a:focus, .skill-bio-interest .nav-tabs.tab-list > li.active > a:hover {
	    background-color: transparent !important;
	    border: 1px solid transparent;
	    cursor: default;
	}

	.skill-bio-interest .nav-tabs.tab-list > li > a {
	    border: 1px solid #000000;
	    border-radius: 0;
	    color: #444;
	    font-weight: 400;
	    padding: 2px 10px;
	}

	.skill-bio-interest .nav-tabs > li > a, .skill-bio-interest .nav-tabs > li > a:focus, .skill-bio-interest .nav-tabs > li > a:hover {
	    border: 1px solid transparent !important;
	    color: #444;
	}

	.profile-cols.common-tab-sec .nav.nav-tabs > li > a{
		color: #444;
		font-weight: 400;
	}
	.skill-bio-interest .nav-tabs.tab-list > li.active > a, .profile-cols.common-tab-sec .nav.nav-tabs > li.active > a {
	    color: #5f9323 !important;
		font-weight: 600;
	}


	.skill-bio-interest-content {
		padding: 10px 15px 0px 10px;
	}
	.interests-list {
		white-space: nowrap;
	    text-overflow: ellipsis;
	    width: 100%;
	    display: block;
	    overflow: hidden;
	    cursor: default;
		padding-bottom: 4px;
	}
	.interests-list:last-child {
		padding-bottom: 0;
	}
	.linked-in-li {
		float: right !important;
	}

	#tab_behaviors ul{
		list-style:none;
		margin-top:0px;
		padding:0;
		margin:0 0 6px 0;
	}
	#tab_behaviors .control-label{
		font-weight:550;
		font-size: 12px;
	}
	.behaviors .control-label{
		font-weight:501;
		font-size: 13px;
	}
	#tab_behaviors .form-group {
		line-height: 15px;
	}
	#profile_send_email_btn p, #profile_send_nudge_btn p{
		margin:0;
	}
	.user-profile .profile-social-sec{
		margin-bottom: 0;
	}
	#tab_behaviors .profile-social-sec ul:last-child{
		margin-bottom: 0;
	}

	.user-profile .profile-address-sec {
		/*min-height: 166px;*/
		min-height: 151px;
		margin-bottom: 0px;
		padding-bottom: 24px;
	}
	.profile-cols.common-tab-sec .nav.nav-tabs li {
     border-right-width: 1px;
	 border-right-color: #dcdcdc;
	}
	.modal-header .close {
    color: #fff;
    text-shadow: none;
		opacity: 1;
		margin-top: 7px;
	}
	.profile-border-left{
		border-left: 1px solid #dcdcdc;
	}
    .profile-border{
		border-left: 1px solid #dcdcdc;
	}
	.modal-dialog .modal-content .modal-footer{
		    border-top-color: #dcdcdc;
	}
	.profile-address-sec .icon-linked-in {
		height: 17px;
		margin-top: 0px;
	}
    .chat_start_email{
        padding: 6px 12px;
        font-size: 14px;
    }
	.chat_start_email, #send_nudge_profile{
		background-color: transparent;
		border-color: #5f9323;
		color: #5f9323;
    }

	.chat_start_email:hover, #send_nudge_profile:hover, .chat_start_email:focus, #send_nudge_profile:focus{
		background-color: #67a028;
		color: #fff;
		border-color: #67a028;
	}

    .profile-sabject-list {
        padding: 0;
        margin: 0;
    }
      .profile-sabject-list li{
        padding: 1px 0;
        text-transform: uppercase;
          cursor: pointer;
    }
     .profile-sabject-list li:hover{
        background-color: #FFFFFF;
    }
    .skill-bio-box-sec{
            max-height: 324px;
            min-height: 324px;
            width: 100%;
            overflow-x: hidden;
            overflow-y: auto;

    }
	.interest-sec-scroll {
			max-height: 320px;
            min-height: 320px;
            width: 100%;
            overflow-x: hidden;
            overflow-y: auto;
	}


	.job-role-info {
		white-space: nowrap;
		text-overflow: ellipsis;
		overflow: hidden;
		width: 100%;
		display: block;
	}
	.nodata{
		font-size:14px;
	}
	.competencies-list-bg {
	    background-color: #ededed;
	    padding: 4px 8px 4px 5px;
	    border-radius: 3px;
	    border-left: 3px solid;
		font-size: 14px;
		display: flex;
	    max-width: 100%;
	    white-space: nowrap;
		align-items: center;
	}
		.competencies-list-bg i {
	    display: inline-block;
	    height: 18px;
	    background-position: center center;
	    background-repeat: no-repeat;
	    vertical-align: top;
	}
	.competencies-list-bg .skill-pop {
	    display: inline-block;
	    cursor: default;
	    overflow: hidden;
	    text-overflow: ellipsis;
	    white-space: nowrap;
		    padding-left: 2px;
		padding-right: 2px;
	}
	.competencies-list-bg .col_doc_filename {
		margin-left: 3px;
	}


	.skill-bio-box span.skill i.skills-icon, .skill-bio-box span.skill i.subjects-icon, .skill-bio-box span.skill i.domain-icon{
		min-width: 18px;
	}
	.skill-bio-box span.skill i.subjects-icon{
		margin-right: 1px;
	}

	.skill-bio-box span.skill span a:last-child i{
		margin-right: 0;
	}
	.skill-bio-box span.skill span .a-file-pdf-o{
		min-width: 10px;
		margin-right: 0;
	}


	.modal-content .skill .competencies-list-bg {
		    border-left-color: #3c8dbc;
	}
	.modal-content .skill.subjects-t .competencies-list-bg {
		    border-left-color: #c55a11;
	}
	.modal-content .skill.domains-t .competencies-list-bg {
		    border-left-color: #5f9322;
	}
	.outline-btn-profile{
		float:left;
		margin-right:5px;
	}
	.outline-btn-nudge-profile{
		margin-right:15px;
	}

	#user_edit, .modal-footer .btn.outline-btn-profile{
		background-color: transparent;
		color: #5f9323;
	}
	.modal-footer .btn-success {
     background-color: #5f9323;
	}
	.modal-footer .btn-success:hover, #user_edit:hover, .modal-footer  .btn.outline-btn-profile:hover {
     background-color: #67a028;
		color: #fff;
	}



	/*.analyzing-loading-text {
		font-size: 14px;
	}*/

	@media (min-width:769px) and (max-width:811px)  {
		.linked-in-li {
			float: none !important;
		}
	}
	@media  (max-width:767px)  {
	.user-profile .profile-address-sec {
		margin-bottom: 15px;
		}
		.skill-bio-interest-content {
		padding: 10px 15px 0px 15px;
	}

	.profile-border-left{
		border-left: none;
	}
     .profile-border{
		border-left: none;
	}

	}

	.closed_tags {
		display: none;
	}
	.show_tag{float: left;margin-top: 7px;}
	a.open_tags_panel{position: relative; display: inline-block;}
	.show_tag .tag-counter {
        font-size: 11px;
        line-height: 11px;
        padding:0;
        position: absolute;
        text-align: center;
        top: -4px;
        display: table;
        font-weight: 400;
        font-style: normal;
        color: #444;
        left: 17px;
    }
        .tokenfield .token .token-label {
            padding-left: 0;
        }
      .tokenfield .token-input {
            margin-bottom: 0;
            margin-top: 1px;
        }

	.modal-tag-container:before{
		display: table;
		content: " ";
	}
	.modal-tag-container{
		background: #eee;
		color: #333;
		border-top:1px solid #dcdcdc;
		padding: 15px;
		float: left;
		width:	100%
	}
        .tags_panel-inner{
            display: flex;
            align-items: center;
            width: 100%;
            position: relative;
        }
        .tokenfield .token-input {
            width: auto !important;
            margin-bottom: 6px;
        }

	.tags_block{
		flex-grow: 1;
	}
	.tags_btn_block{
		float:right;
		margin-left: 9px;
	}
        .tags_btn_block .btn-add[disabled="disabled"] {
            background-color: #919191;
            border-color: #858585;
        }

       .tags_btn_block .btn-add {
            min-width: 62px;
           color: #fff;
        }
        .tokenfield.form-control.focus{
            box-shadow: none;
            border-color: #d2d6de;
        }
        .tokenfield.form-control {
            min-height: 34px;
            padding: 5px 5px 0px 5px;
            border-color: #d2d6de;
            max-height: 61px;
            width: 100%;
            overflow: auto;
            display: flex;
            white-space: normal;
            flex-wrap: wrap;
            height: auto;
        }
        .tokenfield .token {
            color: #555;
            margin: 0 5px 5px 0;
        }
		.tags_panel-inner .tokenfield .token .token-label {
			text-overflow: unset;
			padding-left: 0;
		}

	.ui-autocomplete{
		z-index: 9999;
	}
       .ui-autocomplete.ui-front.ui-menu.ui-widget.ui-widget-content {
            max-height: 170px;
            width: 195px !important;
            min-width: 195px !important;
           font-size: 14px;
        }

        .ui-widget-content {
            background-color: #fff;

        }

        .ui-menu .ui-menu-item {
            margin: 0;
            padding: 1px 5px;
            list-style-image:none;
		}
        .ui-menu .ui-menu-item.ui-state-focus {
            margin: 0;
            padding: 1px 5px;
            border: none;
            font-weight: 400;
            background: #f5f5f5;
        }
        .ui-menu .ui-menu-item:hover {
            background: #f5f5f5;
        }
        .tags_panel-inner .error-message {
               position: absolute;
               bottom: -13px;
            }
        .ui-autocomplete.ui-front.ui-menu.ui-widget.ui-widget-content {
            z-index: 9999 !important;
        }
    /*.profile-org-scroll .loc-thumb, .profile-org-scroll .loc-name, .profile-org-scroll .sks-country, .profile-org-scroll .sks-city, .profile-org-scroll .skill-popple-icon img {
    	cursor: default !important;
    }*/
    span.no-msg {
	    display: block;
	    font-size: 14px;
	}
	.availability_modal_profile{
		cursor:pointer;
	}

</style>
<!-- <div class="modal-dialog profile-view-header modal-lg">
    <div class="modal-content"> -->
		<div class="modal-header">
             <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			 <h3 id="myModalLabel" class="modal-title">
			 <i class='user_icon'></i>
			<?php

			if (isset($ud_data['first_name']) && !empty($ud_data['first_name'])) {
				echo $ud_data['first_name'] . ' ' . $ud_data['last_name'];
			}else{
				echo "Not Given";
			}
			?>
			</h3>
        </div>
        <div class="modal-body user-profile-body">
			<div class="row d-flex-s user-profile">
                <div class="col-sm-3 col-md-3 col-lg-3 col-skill-1 skill-profile-cols">
                    <div class="form-group main-profile-group">
						<div class="different-user-profile">
							<span class="main-profile">
	                        <?php
	                        $menu_pic = isset($ud_data['profile_pic']) && !empty($ud_data['profile_pic']) ? $ud_data['profile_pic'] : '' ;
							if(!empty($menu_pic) && file_exists(USER_PIC_PATH.$menu_pic)){
								$profilesPic = SITEURL.USER_PIC_PATH.$menu_pic;
							}else{
								$profilesPic = SITEURL.'images/placeholders/user/user_1.png';
							}
							?>

	                        	<img class="" alt="Logo Image"  src="<?php echo $profilesPic ?>" alt="logo" />
							</span>
							<?php if($this->Session->read('Auth.User.id') != $ud_data['user_id'] && $loggedUser['organization_id'] != $ud_data['organization_id']){ ?>
							<i class="communitygray48 tip-text" title="Not In Your Organization"></i>
							<?php } ?>
						</div>
                    </div>

                    <?php $current_location = $this->User->current_location($ud_data['user_id']); ?>
                    <?php $user_unavailability = $this->User->user_unavailability($ud_data['user_id']);
					//availability_modal_profile
					?>
					<div class="form-group user-location">
                    	<span class="at-home-p"><?php echo $current_location; ?></span>
                    	<span class="working-p"><?php echo $user_unavailability; ?></span>
                    </div>

					<div class="skill-menu-left">
					<ul>
						<li><a class="phone-no" href="<?php echo 'tel:'.$ud_data['contact']; ?>"><span class="skill-menu-left-text"><i class="skill-menu-icon telephonegreenicon"></i><?php echo (!empty($ud_data['contact'])) ? $ud_data['contact'] : 'Not Given'; ?></span> </a></li>
						<?php
								if( isset($user_timezone['Timezone']['timezone']) && !empty($user_timezone['Timezone']['timezone']) ){
									$timezone = $user_timezone['Timezone']['timezone']." ".$user_timezone['Timezone']['name'] ;
								}else{
									$timezone = 'Not Set';
								}
								 ?>
						<li><a href="javascript:void(0);" class="location_user_name tipText"title="<?php echo $timezone; ?>"><span class="skill-menu-left-text"><i class="skill-menu-icon timezonegreenicon"></i>
								<?php
								echo $timezone;
								 ?>
							</span> </a>
						</li>
						<li class="fbli-icon">
							<a href="mailto:<?php echo $u_data['email']; ?>" class="tip-text" title="<?php echo $u_data['email']; ?>"><i class="skill-menu-icon emailgreenicon"></i></a>
							<?php if(isset($ud_data['linkedin_url']) && !empty($ud_data['linkedin_url'])){ ?>
							<a href="<?php echo $ud_data['linkedin_url']; ?>" target="_blank" class="tip-text" title="<?php echo $ud_data['linkedin_url']; ?>"><i class="skill-menu-icon linkedingreenicon"></i></a>
							<?php } ?>
						</li>
					</ul>
					<h6>Related</h6>
					<ul>
						<li><a href="javascript:void(0);"><span class="skill-menu-left-text"><i class="skill-menu-icon skillsgreenicon"></i> Skills </span> <span class="count"><?php echo (isset($user_skills) && !empty($user_skills)) ? count($user_skills) : 0; ?></span> </a></li>
						<li><a href="javascript:void(0);"><span class="skill-menu-left-text"><i class="skill-menu-icon subjectsgreenicon"></i> Subjects </span> <span class="count"><?php echo (isset($user_subjects) && !empty($user_subjects)) ? count($user_subjects) : 0; ?></span> </a></li>
						<li><a href="javascript:void(0);"><span class="skill-menu-left-text"><i class="skill-menu-icon domaingreenicon"></i> Domains </span> <span class="count"><?php echo (isset($user_domains) && !empty($user_domains)) ? count($user_domains) : 0; ?></span> </a></li>
						<li><a href="javascript:void(0);"><span class="skill-menu-left-text"><i class="skill-menu-icon storygreenicon"></i> Stories </span> <span class="count"><?php echo count($selected_stories); ?></span> </a></li>
					</ul>
					</div>


                </div>

                <div class="col-sm-9 col-md-9 col-sm-9 col-skill-2">
                <div class="row d-flex-s s-height100" style="" >
                <div class="col-sm-12 col-md-6 col-lg-6 common-tab-sec view-skills-tab left-border v-column-1" >
                    <ul class="nav nav-tabs tab-list" >
						<li class="active">
							<a data-toggle="tab" class="active" href="#profiledetails" aria-expanded="true">Details</a>
						</li>
						<li class="">
							<a data-toggle="tab" href="#tab_bio" aria-expanded="false">Bio</a>
						</li>
						<li class="">
							<a data-toggle="tab" href="#tab_interests" aria-expanded="false">Interests</a>
						</li>

					</ul>

                    <div class="tab-content">
                        <div id="profiledetails" class="tab-pane fade active in">

					<div class="form-group">
						<label  class="control-label">Job Title:</label>

						<?php
						if (isset($ud_data['job_title']) && !empty($ud_data['job_title'])) {
							echo $ud_data['job_title'];
						}
						else{
							echo "Not Given";
						}
						?>

					</div>
					<div class="form-group m0">
						<label  class="control-label">Job Role:</label>
						<div class="job-role-info-scroll">
						<?php
						if (isset($ud_data['job_role']) && !empty($ud_data['job_role'])) {
							echo $ud_data['job_role'];
						}
						else{
							echo "Not Given";
						}
						?>
						</div>
					</div>
                </div>



						<div id="tab_bio" class="tab-pane fade">
							<div class="form-group userbio">
								<!-- <label  class="control-label">Bio:</label> -->
								<div class="skill-bio-box-sec">
									<?php
									if (isset($ud_data['bio']) && !empty($ud_data['bio'])) {
										echo nl2br($ud_data['bio']);
									}
									else{
										echo "No Bio";
									}
									?>
								</div>
							</div>
						</div>
						<div id="tab_interests" class="tab-pane fade">
							<div class="form-group userinterest">
								<!-- <label  class="control-label">Bio:</label> -->
								<div class="interest-sec-scroll">
									<?php
									$user_interest = user_interest($ud_data['user_id']);
									if ($user_interest) {
										foreach ($user_interest as $key => $value) {
											$data = $value['UserInterest'];
											$strlen = strlen($data['title']);
										?>
										<span class="interests-list <?php if($strlen>58){ ?>tipText<?php } ?>" <?php if($strlen>58){ ?>title="<?php echo htmlspecialchars($data['title']); ?>"<?php } ?>><?php echo htmlentities($data['title'], ENT_QUOTES, "UTF-8"); ?></span>
										<?php
										}
									}
									else{
										echo "No Interests";
									}
									?>
								</div>
							</div>
						</div>

					</div>

                </div>
				<div class="col-sm-12 col-md-6 col-lg-6 common-tab-sec view-skills-tab left-border v-column-2">
					<ul class="nav nav-tabs tab-list" >
						<li class="active">
							<a data-toggle="tab" class="active" href="#profile-tab-org" aria-expanded="true">Community</a>
						</li>
						<li class="">
							<a data-toggle="tab" href="#tab_competencies" aria-expanded="false">Competencies</a>
						</li>
						<li>
							<a data-toggle="tab" href="#tab_stories" aria-expanded="false">Stories</a>
						</li>
					</ul>
					<div class="tab-content" id="sbiTabContent">
						<div id="profile-tab-org" class="tab-pane fade active in">
							<div class="profile-org-scroll">
								<div class="org-top-list">
									<?php if(isset($ud_data['organization_id']) && !empty($ud_data['organization_id'])){ ?>
									<span class="loc-name-list org-list" data-id="<?php echo $ud_data['organization_id']; ?>">
										<div class="community-diff-list">
										<span class="loc-thumb">
											<?php if(!empty($org_data['org_image'])){ ?>
											<img src="<?php echo SITEURL . ORG_IMAGE_PATH . $org_data['org_image'];?>">
											<?php } ?>
										</span>
											<!--<i class="communitygray18"></i>-->
											</div>
										<div class="loc-info">
											<span class="loc-name"><?php echo $org_data['org_name']; ?></span>
											<div class="loc-cc-name">
												<span class="sks-country"><?php echo $ot_data['ot_type']; ?></span>
											</div>
										</div>
									</span>
									<?php }else{ ?>
									<span class="no-msg no-org">No Organization</span>
									<?php } ?>
									<?php if(isset($ud_data['location_id']) && !empty($ud_data['location_id'])){ ?>
									<span class="loc-name-list loc-list" data-id="<?php echo $loc_data['loc_id']; ?>">
										<div class="community-diff-list">
										<span class="loc-thumb">
											<?php if(!empty($loc_data['loc_image'])){ ?>
											<img src="<?php echo SITEURL . LOC_IMAGE_PATH . $loc_data['loc_image'];?>">
											<?php } ?>
										</span>
											<!--<i class="communitygray18"></i>-->
											</div>
										<div class="loc-info">
											<span class="loc-name"><?php echo $loc_data['loc_name']; ?></span>
											<div class="loc-cc-name">
												<span class="sks-city"><?php echo $loc_data['loc_city']; ?>,</span>
												<span class="sks-country"><?php echo $countries_data['country_name']; ?></span>
											</div>
										</div>
									</span>
									<?php }else{ ?>
									<span class="no-msg no-location">No Location</span>
									<?php } ?>
									<?php if(isset($ud_data['department_id']) && !empty($ud_data['department_id'])){ ?>
									<span class="loc-name-list dept-list" data-id="<?php echo $ud_data['department_id']; ?>">
										<div class="community-diff-list">
										<span class="loc-thumb">
											<?php if(!empty($dept_data['dept_image'])){ ?>
											<img src="<?php echo SITEURL . COMM_IMAGE_PATH . $dept_data['dept_image'];?>">
											<?php } ?>
										</span>
										<?php //if($ud_data['reports_to_org'] !=$ud_data['reports_to_org']){?>
										<!--<i class="communitygray18"></i>-->
										<?php //} ?>
											</div>
										<div class="loc-info">
											<span class="loc-name"><?php echo $dept_data['dept_name']; ?></span>
											<div class="loc-cc-name">
												<span class="sks-country">Department</span>
											</div>
										</div>
									</span>
									<?php }else{ ?>
									<span class="no-msg">No Department</span>
									<?php } ?>
								</div>
								<div class="dotted-line-sec reportsto">

									<?php if(isset($ud_data['reports_to_id']) && !empty($ud_data['reports_to_id'])){
										$udd_data = $show_data['udd'];
										//pr($udd_data);
										$profile_pic = $udd_data['reports_to_pic'];
										if(!empty($profile_pic) && file_exists(USER_PIC_PATH.$profile_pic)){
											$profilesPic = SITEURL.USER_PIC_PATH.$profile_pic;
										} else {
											$profilesPic = SITEURL.'images/placeholders/user/user_1.png';
										}


									?>
									<h5>Reports To:</h5>
									<ul>
										<li>
											<div class="community-diff-list">
											<span class="skill-popple-icon show-user-popup" data-id="<?php echo $ud_data['reports_to_id']; ?>">
												<img class="" alt="<?php echo $show_data[0]['reports_to_user']; ?>" src="<?php echo $profilesPic; ?>">
											</span>
											<?php

											if($loggedUser['organization_id'] != $udd_data['reports_to_org']){?>
												<i class="communitygray18 tipText show-user-popup" data-id="<?php echo $ud_data['reports_to_id']; ?>" title="Not In Your Organization"></i>
											<?php } ?>
											</div>
											<span class="skill-popple-info">
											<h6 class="show-user-popup" data-id="<?php echo $ud_data['reports_to_id']; ?>"><?php echo $show_data[0]['reports_to_user']; ?></h6>
											<p><?php echo $udd_data['reports_to_job']; ?></p>
											</span>
										</li>
									</ul>
									<?php }else{ ?>
									<span class="no-msg no-reports">No Reports To</span>
									<?php } ?>
								</div>

								<?php $reports_from = $this->Permission->reports_from($u_data['id']);

								usort($reports_from, function($a, $b) {
									$t1 = iconv('UTF-8', 'ASCII//TRANSLIT', $a[0]['full_name']);
									$t2 = iconv('UTF-8', 'ASCII//TRANSLIT', $b[0]['full_name']);
								    return strcasecmp($t1, $t2);
								});
								?>
								<div class="dotted-line-sec">
									<?php if(isset($reports_from) && !empty($reports_from)){ ?>
									<h5>Reports From:</h5>
									<ul class="dotted-line-scroll">
										<?php foreach ($reports_from as $key => $listuser) {
											$profile_pic = $listuser['ud']['profile_pic'];
											if(!empty($profile_pic) && file_exists(USER_PIC_PATH.$profile_pic)){
												$profilesPic = SITEURL.USER_PIC_PATH.$profile_pic;
											} else {
												$profilesPic = SITEURL.'images/placeholders/user/user_1.png';
											}
										?>
										<li><div class="community-diff-list">
											<span class="skill-popple-icon show-user-popup"  data-id="<?php echo $listuser['ud']['user_id']; ?>">
												<img class="" alt="<?php echo $listuser[0]['full_name']; ?>" src="<?php echo $profilesPic; ?>">
											</span>
											<?php if($loggedUser['organization_id'] !=$listuser['ud']['organization_id']){?>
											<i class="communitygray18 tipText show-user-popup"  data-id="<?php echo $listuser['ud']['user_id']; ?>" title="Not In Your Organization"></i>
											<?php } ?>
											</div>
											<span class="skill-popple-info">
											<h6 class="show-user-popup" data-id="<?php echo $listuser['ud']['user_id']; ?>"><?php echo $listuser[0]['full_name']; ?></h6>
											<p><?php echo $listuser['ud']['job_title']; ?></p>
											</span>
										</li>
										<?php } ?>
									</ul>
									<?php }else{ ?>
									<span class="no-msg">No Reports From</span>
									<?php } ?>
								</div>

								<div class="dotted-line-sec">
									<?php if($dotted_lines){
										usort($dotted_lines, function($a, $b) {
											$t1 = iconv('UTF-8', 'ASCII//TRANSLIT', $a['full_name']);
											$t2 = iconv('UTF-8', 'ASCII//TRANSLIT', $b['full_name']);
										    return strcasecmp($t1, $t2);
										});
										?>
									<h5>Dotted Lines To:</h5>
									<ul class="dotted-line-scroll">
										<?php foreach ($dotted_lines as $key => $listuser) {
											// pr($listuser);
											$profile_pic = $listuser['profile_pic'];
											if(!empty($profile_pic) && file_exists(USER_PIC_PATH.$profile_pic)){
												$profilesPic = SITEURL.USER_PIC_PATH.$profile_pic;
											} else {
												$profilesPic = SITEURL.'images/placeholders/user/user_1.png';
											}
										?>
										<li><div class="community-diff-list">
											<span class="skill-popple-icon show-user-popup"  data-id="<?php echo $listuser['user_id']; ?>">
												<img class="" alt="<?php echo $listuser['full_name']; ?>" src="<?php echo $profilesPic; ?>">
											</span>
											<?php if($loggedUser['organization_id'] !=$listuser['dotted_org']){?>
											<i class="communitygray18 tipText show-user-popup"  data-id="<?php echo $listuser['user_id']; ?>" title="Not In Your Organization"></i>
											<?php } ?>
											</div>
											<span class="skill-popple-info">
											<h6 class="show-user-popup" data-id="<?php echo $listuser['user_id']; ?>"><?php echo $listuser['full_name']; ?></h6>
											<p><?php echo $listuser['job_title']; ?></p>
											</span>
										</li>
										<?php } ?>
									</ul>
									<?php }else{ ?>
									<span class="no-msg">No Dotted Lines To</span>
									<?php } ?>
								</div>


								<?php $dotted_from = $this->Permission->dotted_from($u_data['id']); ?>
								<div class="dotted-line-sec">
									<?php if(isset($dotted_from) && !empty($dotted_from)){
										usort($dotted_from, function($a, $b) {
											$t1 = iconv('UTF-8', 'ASCII//TRANSLIT', $a['full_name']);
											$t2 = iconv('UTF-8', 'ASCII//TRANSLIT', $b['full_name']);
										    return strcasecmp($t1, $t2);
										});
									?>
									<h5>Dotted Lines From:</h5>
									<ul class="dotted-line-scroll">
										<?php foreach ($dotted_from as $key => $listuser) {
											$profile_pic = $listuser['profile_pic'];
											if(!empty($profile_pic) && file_exists(USER_PIC_PATH.$profile_pic)){
												$profilesPic = SITEURL.USER_PIC_PATH.$profile_pic;
											} else {
												$profilesPic = SITEURL.'images/placeholders/user/user_1.png';
											}
										?>
										<li><div class="community-diff-list">
											<span class="skill-popple-icon show-user-popup"  data-id="<?php echo $listuser['user_id']; ?>">
												<img class="" alt="<?php echo $listuser['full_name']; ?>" src="<?php echo $profilesPic; ?>">
											</span>
											<?php if($loggedUser['organization_id'] !=$listuser['organization_id']){?>
											<i class="communitygray18 tipText show-user-popup"  data-id="<?php echo $listuser['user_id']; ?>"" title="Not In Your Organization"></i>
											<?php } ?>
											</div>
											<span class="skill-popple-info">
											<h6 class="show-user-popup" data-id="<?php echo $listuser['user_id']; ?>"><?php echo $listuser['full_name']; ?></h6>
											<p><?php echo $listuser['job_title']; ?></p>
											</span>
										</li>
										<?php } ?>
									</ul>
									<?php }else{ ?>
									<span class="no-msg">No Dotted Lines From</span>
									<?php } ?>
								</div>


							</div>
						</div>
						<div id="tab_competencies" class="tab-pane fade">
						<div class="com-list-wrap">
							<ul class="competencies-ul">
							<?php
							if(isset($user_skills) && !empty($user_skills)){
								foreach ($user_skills as $key => $value) {

									$user_data = $value['user_data'];
									$lib = $value['lib'];
									$ctip = (isset($user_data['created']) && !empty($user_data['created'])) ? 'Added: '.date('d M, Y', strtotime($user_data['created'])) : '';
									$details = $value['details'];
									$pdf_data = $value[0]['pdf_names'];

									$level_text = (!empty($details['user_level'])) ? $details['user_level'] : 'Beginner';
                                    $exp_text =( $details['user_experience'] == 1 || $details['user_experience'] == '') ? '1 Year' : $details['user_experience'].' Years';

									$level_icon = $this->Permission->level_exp_icon($details['user_level']);
									$exp_icon = $this->Permission->level_exp_icon($details['user_experience'], false);
									$exp_num = $this->Permission->exp_number($details['user_experience']);

									?>
									<li class="skill-border-left" >
										<span class="com-list-bg">
											<i class="com-skills-icon tipText" title="Skill"></i>
											<i class="<?php echo $level_icon ?> tipText" title="Level: <?php echo $level_text; ?>"></i>
											<i class="<?php echo $exp_icon; ?> tipText" title="Experience: <?php echo ($exp_num>1)?$details['user_experience'].' Years':$details['user_experience'].' Year'; ?>"></i>
											<span class="com-sks-title" data-html="true" data-type="skill" data-id="<?php echo $value['user_data']['skill_id']; ?>" title="<?php echo $ctip; ?>"><?php echo $lib['title']; ?></span>
												<?php
												if(isset($pdf_data) && !empty($pdf_data)){
													$fileNames = null;
													$fileNames = explode(',', $pdf_data);
													foreach ($fileNames as $key => $value1) {
														$id_name = explode('~', $value1);
														$fname = $id_name[1];
														/* if (file_exists(SKILL_PDF_PATH . $this->Session->read('Auth.User.id') . DS . $id_name[1])) {
															$pathinfo = pathinfo(SKILL_PDF_PATH . $this->Session->read('Auth.User.id') . DS . $id_name[1] );
															$fname = $pathinfo['filename'];
														} */
														//pr($fname);
														?>
														<a href="<?php echo Router::url(array('controller' => 'shares', 'action' => 'download_skills', $id_name[0], $u_data['id'], 'admin' => false)); ?>" class="col_doc_filename tipText" data-html="true" title="<?php echo $fname; ?>">
															<span class="fas fa-file-pdf"></span>
														</a>
														<?php
													}
												}?>
										</span>
									</li>
									<?php
								}
							} ?>

							<?php
							if(isset($user_subjects) && !empty($user_subjects)){
									// pr($user_skills);
								foreach ($user_subjects as $key => $value) {

									$user_data = $value['user_data'];
									$lib = $value['lib'];
									$details = $value['details'];
									$pdf_data = $value[0]['pdf_names'];

									$ctip = (isset($user_data['created']) && !empty($user_data['created'])) ? 'Added: '.date('d M, Y', strtotime($user_data['created'])) : '';

									$level_text = (!empty($details['user_level'])) ? $details['user_level'] : 'Beginner';
                                    $exp_text =( $details['user_experience'] == 1 || $details['user_experience'] == '') ? '1 Year' : $details['user_experience'].' Years';

									$level_icon = $this->Permission->level_exp_icon($details['user_level']);
									$exp_icon = $this->Permission->level_exp_icon($details['user_experience'], false);
									$exp_num = $this->Permission->exp_number($details['user_experience']);

									?>
									<li class="subjects-border-left">
										<span class="com-list-bg">
											<i class="com-subjects-icon tipText" title="Subject"></i>
											<i class="<?php echo $level_icon ?> tipText" title="Level: <?php echo $level_text; ?>"></i>
											<i class="<?php echo $exp_icon; ?> tipText" title="Experience: <?php echo ($exp_num>1)?$details['user_experience'].' Years':$details['user_experience'].' Year'; ?>"></i>
											<span class="com-sks-title" data-html="true" data-type="subjects" data-id="<?php echo $value['user_data']['subject_id']; ?>" title="<?php echo $ctip; ?>"><?php echo $lib['title']; ?></span>
												<?php
												if(isset($pdf_data) && !empty($pdf_data)){
													$fileNames = null;
													$fileNames = explode(',', $pdf_data);
													foreach ($fileNames as $key => $value1) {
														$id_name = explode('~', $value1);
														$fname = $id_name[1];
														/* if (file_exists(SKILL_PDF_PATH . $this->Session->read('Auth.User.id') . DS . $id_name[1])) {
															$pathinfo = pathinfo(SKILL_PDF_PATH . $this->Session->read('Auth.User.id') . DS . $id_name[1] );
															$fname = $pathinfo['filename'];
														} */
														//pr($fname);
														?>
														<a href="<?php echo Router::url(array('controller' => 'subjects', 'action' => 'download_pdf', $id_name[0], $u_data['id'], 'admin' => false)); ?>" class="col_doc_filename tipText" data-html="true" title="<?php echo $fname; ?>">
															<span class="fas fa-file-pdf"></span>
														</a>
														<?php
													}
												}?>
										</span>
									</li>
									<?php
								}
							} ?>

							<?php
							if(isset($user_domains) && !empty($user_domains)){
									// pr($user_skills);
								foreach ($user_domains as $key => $value) {
									$user_data = $value['user_data'];
									$lib = $value['lib'];
									$details = $value['details'];
									$pdf_data = $value[0]['pdf_names'];

									$ctip = (isset($user_data['created']) && !empty($user_data['created'])) ? 'Added: '.date('d M, Y', strtotime($user_data['created'])) : '';

									$level_text = (!empty($details['user_level'])) ? $details['user_level'] : 'Beginner';
                                    $exp_text =( $details['user_experience'] == 1 || $details['user_experience'] == '') ? '1 Year' : $details['user_experience'].' Years';

									$level_icon = $this->Permission->level_exp_icon($details['user_level']);
									$exp_icon = $this->Permission->level_exp_icon($details['user_experience'], false);
									$exp_num = $this->Permission->exp_number($details['user_experience']);

									?>
									<li class="domain-border-left">
										<span class="com-list-bg">
											<i class="com-domain-icon tipText" title="Domain"></i>
											<i class="<?php echo $level_icon ?> tipText" title="Level: <?php echo $level_text; ?>"></i>
											<i class="<?php echo $exp_icon; ?> tipText" title="Experience: <?php echo ($exp_num>1)?$details['user_experience'].' Years':$details['user_experience'].' Year'; ?>"></i>
											<span class="com-sks-title" data-html="true" data-type="domains" data-id="<?php echo $value['user_data']['domain_id']; ?>" title="<?php echo $ctip; ?>"><?php echo $lib['title']; ?></span>
												<?php
												if(isset($pdf_data) && !empty($pdf_data)){
													$fileNames = null;
													$fileNames = explode(',', $pdf_data);
													foreach ($fileNames as $key => $value1) {
														$id_name = explode('~', $value1);
														//pr($id_name);
														$fname = $id_name[1];
														/* if (file_exists(SKILL_PDF_PATH . $this->Session->read('Auth.User.id') . DS . $id_name[1])) {
															$pathinfo = pathinfo(SKILL_PDF_PATH . $this->Session->read('Auth.User.id') . DS . $id_name[1] );
															$fname = $pathinfo['filename'];
														} */
														//pr($fname);
														?>
														<a href="<?php echo Router::url(array('controller' => 'knowledge_domains', 'action' => 'download_pdf', $id_name[0], $u_data['id'], 'admin' => false)); ?>" class="col_doc_filename tipText" data-html="true" title="<?php echo $fname; ?>">
															<span class="fas fa-file-pdf"></span>
														</a>
														<?php
													}
												}?>
										</span>
									</li>
									<?php
								}
							} ?>
							<?php if((count($user_skills) + count($user_subjects) + count($user_domains)) <= 0){ ?>
								<li>No Competencies</li>
							<?php } ?>

							</ul>
						</div>

						</div>
						<div id="tab_stories" class="tab-pane fade">

							<div class="stories-popup-list">
								<div class="popuplocationlist">
									<ul class="loc-names-ul">
										<?php if($selected_stories){ ?>
								 		<?php foreach ($selected_stories as $key => $value) {  ?>
										<li class="loc-names-li location-list-item">
											<span class="loc-name-list">
												<span class="loc-thumb open-story" data-type="story" data-id="<?php echo $value['id']; ?>">
													<?php if(!empty($value['file'])){ ?>
														<img src="<?php echo SITEURL . STORY_IMAGE_PATH . $value['file']; ?>" >
													<?php } ?>
												</span>
												<div class="loc-info">
													<span class="loc-name open-story" data-type="story" data-id="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></span>
													<div class="loc-cc-name">
														<span class="sks-city"><?php echo $value['type']; ?></span>
													</div>
												</div>
											</span>
										</li>
										<?php } ?>
										<?php }else{ ?>
											<li>No Stories</li>
										<?php } ?>
									</ul>
								</div>
							</div>
						</div>

					</div>
				</div>
                </div>
				 </div>
            </div>
		</div>
		<div class=" close_tags_panel modal-tag-container closed_tags" id="collapse_tags" >
			<div class="tags_panel-inner">
				<div class="tags_block">
					<input type="hidden" name="selectedTags" id="selectedTags">
					<div id="tagbox">
						<input type="text" name="tags_ids" onpaste="return false;" id="tags" class="form-control tokenfield " placeholder="Tag name..." />
					</div>
					<span class="error-message" id="input_validation"></span>
				</div>
				<div class="tags_btn_block">
					<input type="button" name="add_tag_btn" id="add_tag_btn" class="btn btn-success btn-add" disabled="disabled" value="Add" />
				</div>
			</div>
		</div>
		<div class="modal-footer" style="float:left; width:100%">
			<?php
			$show_utill = false;
			if($ud_data['user_id'] == $this->Session->read("Auth.User.id")){
				$show_utill = true;
			}
			else if($this->Session->read('Auth.User.UserDetail.resourcer')){
				$show_utill = true;
			}

			if($show_utill){
			?>
			<button id="planning_utill" class="btn btn-success outline-btn-profile" type="button" data-user="<?php echo $ud_data['user_id']; ?>">Planning</button>
			<?php } ?>
			<button id="user_pepole" class="btn btn-success outline-btn-profile" type="button" data-user="<?php echo $ud_data['user_id']; ?>">People</button>
			<?php if($this->Session->read("Auth.User.id") != $ud_data['user_id']) {

			?>
				<button id="send_nudge_profile" class="btn btn-success outline-btn-profile outline-btn-nudge-profile" type="button">Send Nudge</button>
			<?php } ?>
			<!--CSS BY SJ-->

			<div class="tag_count show_tag" style="">
				<a class="open_tags_panel tipText" role="button" href="javascript:void(0)" title="Tags" aria-expanded="false" aria-controls="collapseExample">
					<i class="user-profile-tag-icon"></i>
					<i class="tag-counter" id="tag-counter"><?php echo $tagsCnt; ?></i>
				</a>
			</div>
			<?php

			$CheckProfileEdit = CheckProfileEdit($ud_data['user_id']);
			$check_admin_settings_self = check_admin_settings_self($ud_data['user_id']);


		   //if( ($this->Session->read("Auth.User.id") == $ud_data['user_id'] ) || ($CheckProfileEdit == 1 ))
		   if(   $CheckProfileEdit == 1 )

		   {

		       if($check_admin_settings_self != 5 ){
			   ?>
			   <button id="user_edit" class="btn btn-success" type="button">Edit</button><?php } } ?>
			<button data-dismiss="modal" class="btn btn-success" type="button">Close</button>


        </div>


<script type="text/javascript" >
var user_profile_view_id = <?php echo $user_id;?>;
$(function(){
	var active_tab = '<?php echo $tab;?>';
	$('.nav.nav-tabs.tab-list a[href="#'+active_tab+'"]').tab('show');

	$('#planning_utill').off('click').on('click', function(event) {
		event.preventDefault();
		var data = $(this).data();
		var url = $js_config.base_url + 'resources/planning/type:people/user:' + data.user
		location.href = url;
	});

	$('#user_pepole').off('click').on('click', function(event) {
		event.preventDefault();
		var data = $(this).data();
		var url = $js_config.base_url + 'resources/people/user:' + data.user
		location.href = url;
	});

	$('.com-list-wrap .com-sks-title').off('click').on('click', function(event) {
		event.preventDefault();
		var data = $(this).data();
		var url = $js_config.base_url + 'competencies/view_skills/' + data.id
		if(data.type == 'subjects'){
			url = $js_config.base_url + 'competencies/view_subjects/' + data.id
		}
		else if(data.type == 'domains'){
			url = $js_config.base_url + 'competencies/view_domains/' + data.id
		}
		$('#popup_modal').modal('hide');
		$('#modal_view_skill').modal({
			remote: url
		})
		.modal('show');
	});




	$('.show-user-popup').off('click').on('click', function(event) {
		event.preventDefault();
		var data = $(this).data();
		var url = $js_config.base_url + 'shares/show_profile/' + data.id
		$('#popup_modal').modal('hide');
		setTimeout(function(){
			$('#popup_modal').modal({
				remote: url
			})
			.modal('show');
		},1200)
	});

	$('.org-list').off('click').on('click', function(event) {
		event.preventDefault();
		var data = $(this).data();
		var url = $js_config.base_url + 'communities/view/org/' + data.id
		$('#popup_modal').modal('hide');
		$('#modal_view_org').modal({
			remote: url
		})
		.modal('show');
	});

	$('.loc-list').off('click').on('click', function(event) {
		event.preventDefault();
		var data = $(this).data();
		var url = $js_config.base_url + 'communities/view/loc/' + data.id
		$('#popup_modal').modal('hide');
		$('#modal_view_loc').modal({
			remote: url
		})
		.modal('show');
	});

	$('.dept-list').off('click').on('click', function(event) {
		event.preventDefault();
		var data = $(this).data();
		var url = $js_config.base_url + 'communities/view/dept/' + data.id
		$('#popup_modal').modal('hide');
		$('#modal_view_dept').modal({
			remote: url
		})
		.modal('show');
	});

	$('.open-story').off('click').on('click', function(event) {
		event.preventDefault();
		var data = $(this).data();
		var url = $js_config.base_url + 'stories/view/story/' + data.id
		$('#popup_modal').modal('hide');
		$('#story_view').modal({
			remote: url
		})
		.modal('show');
	});


	$('.availability_modal_profile').off('click').on('click', function(event) {
		event.preventDefault();
		var data = $(this).data();
		var url = "<?php echo Router::url(array('controller' => 'settings', 'action' => 'availability', 'admin' => false)); ?>";
		$('#popup_modal').modal('hide');
		$('#availability_modal').modal({
			remote: url
		})
		.modal('show');
	});


	$('.tip-text, .com-sks-title').tooltip({
		container: 'body',
		template: '<div class="tooltip" role="tooltip" style="text-transform: none;"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>',
		placement: 'top'
	});
	$(".skill-bio-box-sec").slimScroll({height: 324, alwaysVisible: true});
	$(".interest-sec-scroll").slimScroll({height: 320, alwaysVisible: true});
	$(".profile-org-scroll").slimScroll({height: 321, alwaysVisible: true});
	$(".com-list-wrap ul.competencies-ul").slimScroll({height: 324, alwaysVisible: true});

	$('#send_nudge_profile').on('click', function() {
		$('.modal.in ').modal('hide');
		setTimeout(function(){
			$('#modal_nudge').modal({
				remote: "<?php echo Router::url(array('controller' => 'boards', 'action' => 'send_nudge', 'type' => 'profile', 'user' => $user_id, 'admin' => false)); ?>",
			})
		}, 100)

	})
	$.popUserTagCnt = 0;
	$.popUserId = <?php echo $user_id;?>;
	//send email button will same as close button
	$("#profile_send_email_btn").find('a').removeClass('btn-xs');

	/* $("#show_behaviors").on('click',function(){
		var $that = $(this);
		if( !$that.hasClass('opened') ){
			var user_profile_id = $(this).data('profileid');
			$.ajax({
				url: $js_config.base_url + "shares/show_behaviors/",
				type: "POST",
				data: $.param({user_id:user_profile_id}),
				dataType: "json",
				global: false,
				success: function(response) {
					if (response) {
						 $that.addClass('opened');
						 $("#tab_behaviors").find('.skill-bio-box').html(response);
					}
				}
			})
		}
	}) */
// setTimeout(function(){
	/* ;($.show_social = function(){
		var user_profile_id = '<?php echo $current_user_id; ?>';
		$("#tab_social").html('Analyzing...');
		$.ajax({
			url: $js_config.base_url + "shares/show_behaviors/",
			type: "POST",
			data: $.param({user_id:user_profile_id}),
			dataType: "json",
			global: false,
			success: function(response) {
				if (response) {
					 // $that.addClass('opened');
					$("#tab_social").html(response);
				}
			}
		})
	})(); */
// }, 2000)
	$('#user_edit').on('click',function(){

		var url = "<?php echo SITEURL . 'users/myaccountedit/'.$user_id.'/?refer='.$referer; ?>";

		window.location = url;
    })


	$('.skill').on('mouseout', function(e){
		$('.tooltip').remove()
	})

	 $('#linkedingrayed').tooltip({
		container: 'body',
		placement: 'left',
		trigger: 'hover'
	})
	setTimeout(function(){
		 $('.skill-pop').tooltip({
			container: 'body',
			placement: 'top',
			trigger: 'hover',
			template: '<div class="tooltip" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner left-align"></div></div>'
		})
	}, 1)
	$('#popup_model_box').on('hidden.bs.modal', function () {
      $(this).removeData('bs.modal').find(".modal-content").html('');
    });
	// $(".modal-content").hide()
	setTimeout(function(){
		// $(".modal-content").show()
	}, 1500)



	//change skill list order by alphabetical ================
		var sortSelect = function (select, attr, order) {
			if(attr === 'text' && $(select).children('span').length > 0){

				if(order === 'asc'){

					$(select).html($(select).children('span').sort(function (x, y) {
						return $(x).text().toUpperCase() < $(y).text().toUpperCase() ? -1 : 1;
					}));
					$(select).get(0).selectedIndex = 0;
					//e.preventDefault();
				}// end asc
				if(order === 'desc'){
					$(select).html($(select).children('span').sort(function (y, x) {
						return $(x).text().toUpperCase() < $(y).text().toUpperCase() ? -1 : 1;
					}));
					$(select).get(0).selectedIndex = 0;
					//e.preventDefault();
				}// end desc
			}
		};
		sortSelect('#orderlistskills', 'text', 'asc');

	//=======================================================
	if(window.location.href.indexOf("competencies") > 0) {
	}
	$('.skill-pop').css('cursor', 'pointer');

	$('.skill-pop').off('click').on('click', function(event) {
		event.preventDefault();
		var data = $(this).data();
			var url = $js_config.base_url + 'competencies/view_skills/' + data.id
			if(data.type == 'subject'){
				url = $js_config.base_url + 'competencies/view_subjects/' + data.id
			}
			else if(data.type == 'domain'){
				url = $js_config.base_url + 'competencies/view_domains/' + data.id
			}
			$('#modal_view_skill').modal({
				remote: url
			})
			.modal('show');
			$('#popup_modal').modal('hide');
			$('#popup_model_box').modal('hide');
			//$('#modal_small').modal('hide');
			//$('#popup_model_box').modal('hide');
			/* if ( $(".modal.fade.in").find(".profile-view-header").length > 0 ) {
				$(".profile-view-header").parents("#popup_model_box").modal('hide');
			} */
	});

	$('.dept-title').off('click').on('click', function(event) {
		event.preventDefault();
		var data = $(this).data();
		var url = $js_config.base_url + 'communities/view/dept/' + data.id

		$('#modal_view_dept').modal({
			remote: url
		})
		.modal('show');
		$('#popup_modal').modal('hide');
		//$('#modal_small').modal('hide');
		//$('#popup_model_box').modal('hide');
	});

	$(".user-profile .skill-bio-box").slimScroll({height: 327});


})
</script>
<style>
.dept-title {
	cursor: pointer;
}
.skill-pop {
	display: inline-block;
	cursor: default;
}
.left-align {
	text-align: left !important;
}
</style>
<?php echo $this->Html->script('projects/users_tags', array('inline' => true)); ?>
<?php echo $this->Html->script('jquery.tokeninput', array('inline' => true)); ?>