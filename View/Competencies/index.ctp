<?php echo $this->Html->css('projects/competency'); ?>
<style>
  .select{opacity:0;}
  .content{
  	padding-top: 0;
  }
  /*section.content {
      min-height: 578px;
  }*/
  .ssd-data-row  .ssd-col-10 > a{
  	display:none;
  }
  .ssd-data-row:hover .ssd-col-10 >a{
  	display:block !important;
  }
  .no-scroll {
    overflow: hidden;
  }
  .watch-to-engagement, .watch-to-compare {
    display: none;
  }

</style>

<?php
   // asort($allUsers);
   echo $this->Html->css('projects/bs-selectbox/bootstrap-multiselect');
   echo $this->Html->script('projects/plugins/selectbox/bootstrap-multiselect', array('inline' => true));
	 echo $this->Html->script('projects/competencies', array('inline' => true));

$data_type = '';
$data_id = '';
if( isset($this->request->params['pass']['0']) && !empty($this->request->params['pass']['0']) && isset($this->request->params['pass']['1']) && !empty($this->request->params['pass']['1']) ){
	$data_type = $this->request->params['pass']['0'];
	$data_id = $this->request->params['pass']['0'];
}
$resourcer = $this->Session->read('Auth.User.UserDetail.resourcer');
$resourcer_permit = (isset($resourcer) && !empty($resourcer)) ? $resourcer : false;

   ?>

<div class="row">
   <div class="col-xs-12">
      <section class="main-heading-wrap">
         <div class="main-heading-sec">
            <h1><?php   echo $page_heading; ?></h1>
            <div class="subtitles"><?php echo $page_subheading; ?></div>
         </div>
         <div class="header-right-side-icon">
            <span class="headertag ico-project-summary tipText" title="Tag" data-toggle="modal" data-target="#modal_nudge" data-remote="<?php echo Router::Url( array( "controller" => "tags", "action" => "add_tags_team_members", 'type' => 'search_compare', 'admin' => FALSE ), true ); ?>" data-original-title="Tag Team Members"></span>
         </div>
      </section>
      <div class="box-content">
         <div class="row ">
            <div class="col-xs-12">

				<div class="competencies-tab">
					<div class="row">
					<div class="col-md-9">
						<ul class="nav nav-tabs" id="competency_tabs">
							<li class="active">
								<a data-toggle="tab" data-type="searchs" class="active competencies_tab " data-target="#tab_search" href="#tab_search" aria-expanded="true">SEARCH</a>
							</li>
                            <li class="">
                                <a data-toggle="tab" data-type="compare" class="compares_tab compare-tab" data-target="#tab_compare" href="#tab_compare" aria-expanded="false">Compare</a>
                            </li>
							<li class="">
								<a data-toggle="tab" data-type="watch" class="watch_tab" data-target="#tab_watch" href="#tab_watch" aria-expanded="false">Watch</a>
							</li>
							<li class="">
								<a data-toggle="tab" data-type="skills" class="competencies_tab skill-tab" data-target="#tab_skills" href="#tab_skills" aria-expanded="false">SKILLS</a>
							</li>
							<li class="">
								<a data-toggle="tab" data-type="subjects" class="competencies_tab subject-tab"  data-target="#tab_subjects" href="#tab_subjects" aria-expanded="false">SUBJECTS</a>
							</li>
							<li class="">
								<a data-toggle="tab" data-type="domains" class="competencies_tab domain-tab"  data-target="#tab_domains" href="#tab_domains" aria-expanded="false">DOMAINS</a>
							</li>
						</ul>
						</div>
						<div class="col-md-3 right text-right">
							<div class="skill-link-top-right">
                                <?php if($resourcer_permit){ ?>
                                    <a href="#" class="tipText common-btns search-to-planning hide" data-type="search" title="Go To Planning"><i class="planningblack18"></i></a>
     								<a href="#" class="tipText common-btns compare-to-planning hide" data-type="compare" title="Go To Planning"><i class="planningblack18"></i></a>
                                <?php } ?>
                                <a href="#" class="tipText to-compare" style="display: none; margin-right: 3px; margin-top: 2px;" data-type="search" title="Go To Compare"><i class="compareblack18"></i></a>

                                <a href="#" class="tipText to-engagement" style="display: none;" data-type="search" title="Go To People"><i class="peopleblack18"></i></a>

  							<a href="<?php echo Router::Url( array( 'controller' => 'subdomains', 'action' => 'knowledge_analytics','skill', 'admin' => FALSE ), TRUE ); ?>" class="tipText skills-button hide common-btns analytic-btn" title="Go To Analytic" data-type="skills" ><i class="analytic-icon"></i></a>

  							<a href="<?php echo Router::Url( array( 'controller' => 'subdomains', 'action' => 'knowledge_analytics','subject', 'admin' => FALSE ), TRUE ); ?>" class="tipText subjects-button  common-btns analytic-btn hide" title="Go To Analytic" data-type="subjects" ><i class="analytic-icon"></i></a>

  							<a href="<?php echo Router::Url( array( 'controller' => 'subdomains', 'action' => 'knowledge_analytics','domain', 'admin' => FALSE ), TRUE ); ?>" class="tipText domains-button common-btns  analytic-btn hide" title="Go To Analytic" data-type="domains" ><i class="analytic-icon"></i></a>

						<?php
							if( $user_is_admin ){ ?>
							<a class="tipText skills-button hide common-btns" data-remote="<?php echo SITEURL;?>competencies/bulk_skill_update" data-area="" data-target="#modal_bulk_update" data-toggle="modal" data-original-title="Bulk Delete" data-type="skills" ><i class="bulk-update-icon"></i></a>

							<a class="tipText skills-button hide common-btns" data-remote="<?php echo SITEURL;?>competencies/add_skills" data-area="" data-target="#modal_create_skills" data-toggle="modal" data-original-title="Add Skill" data-type="skills" ><i class="add-skill-icon"></i></a>


							<a class="tipText subjects-button hide common-btns" data-remote="<?php echo SITEURL;?>competencies/bulk_subject_update" data-area="" data-target="#modal_bulk_update" data-toggle="modal" data-original-title="Bulk Delete" data-type="skills" ><i class="bulk-update-icon"></i></a>
							<a class="tipText subjects-button hide common-btns" data-remote="<?php echo SITEURL;?>competencies/add_subjects" data-area="" data-target="#modal_create_skills" data-toggle="modal" data-original-title="Add Subject" data-type="subjects" ><i class="add-skill-icon"></i></a>


							<a class="tipText domains-button hide common-btns" data-remote="<?php echo SITEURL;?>competencies/bulk_domain_update" data-area="" data-target="#modal_bulk_update" data-toggle="modal" data-original-title="Bulk Delete" data-type="skills" ><i class="bulk-update-icon"></i></a>
							<a class="tipText domains-button hide common-btns" data-remote="<?php echo SITEURL;?>competencies/add_domains" data-area="" data-target="#modal_create_skills" data-toggle="modal" data-original-title="Add Domain" data-type="domains" ><i class="add-skill-icon"></i></a>

						<?php } ?>
							</div>
							<div class="input-group search-skills-box">
                                <input id="temp_search " type="text" class="form-control search-box" data-type="skills" placeholder="Search for Skills...">
                                <input id="temp_search " type="text" class="form-control search-box" data-type="subjects" placeholder="Search for Subjects...">
								<input id="temp_search " type="text" class="form-control search-box" data-type="domains" placeholder="Search for Domains...">
								<span class="input-group-btn" >
									<button class="btn search-btn disabled" type="button"><i class="search-skill"></i></button>
									<button class="btn clear-btn" type="button"><i class="clearblackicon search-clear"></i></button>
								</span>
							</div>


						</div>
						</div>
					</div>

               <div class="box noborder">

				   <div class="modal modal-success fade" id="com_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                            <div class="modal-dialog ">
                                <div class="modal-content"></div>
                            </div>
                        </div>

			<div id="box_body">
				<div class="tab-content">
                     <div class="tab-pane fade active in"  id="tab_search" >
                        <input type="hidden" name="search_user_list" id="search_user_list" value="0">
				 <div class="box-header filters competencies-page-header" style="">
 <div class="competencies-header-sec">

                        <div class="competencies-select-sec  popup-select-icon">
                           <div class="competencies-select-input">
                              <?php
                              $sk_default = '';
                               if((isset($sel_type) && !empty($sel_type) && $sel_type == 'sk') && (isset($sel_id) && !empty($sel_id))){
                                $sk_default = $sel_id;
                               }
                              $skills = htmlentity($skills);
                              echo $this->Form->input('skills', array(
                                        'options' => $skills,
                                        'class' => 'form-control select',
                                        'id' => 'skills',
                                        'multiple' => 'multiple',
                                        'label' => false,
                                        'div' => false,
                                        "size" => 1,
                                        'default' => $sk_default
                                    ));
                              ?>
                              <div class="competenciesmatch-all">
                                 <label>
                                    <input type="checkbox" id="match_all_skills" name="match_all_skills"  class="" value="0" disabled>
                                 <label class="fancy_label text-black" for="match_all_skills">Require All Selected Skills</label>
                                 </label>
                              </div>
                           </div>
                           <div class="competencies-select-input">
                              <?php
                              $sb_default = '';
                               if((isset($sel_type) && !empty($sel_type) && $sel_type == 'sb') && (isset($sel_id) && !empty($sel_id))){
                                $sb_default = $sel_id;
                               }
                                $subjects = htmlentity($subjects);
                                echo $this->Form->input('subjects', array(
                                        'options' => $subjects,
                                        'class' => 'form-control select',
                                        'id' => 'subjects',
                                        'multiple' => 'multiple',
                                        'label' => false,
                                        'div' => false,
                                        "size" => 1,
                                        'default' => $sb_default
                                    ));
                              ?>
                              <div class="competenciesmatch-all">
                                 <label>
                                    <input type="checkbox" id="match_all_subject" name="match_all_subject" class="" value="0" disabled>
                                 <label class="fancy_label text-black" for="match_all_subject">Require All Selected Subjects</label>
                                 </label>
                              </div>
                           </div>
                           <div class="competencies-select-input">
                              <?php
                              $dm_default = '';
                               if((isset($sel_type) && !empty($sel_type) && $sel_type == 'dm') && (isset($sel_id) && !empty($sel_id))){
                                $dm_default = $sel_id;
                               }
                                $domains = htmlentity($domains);
                                echo $this->Form->input('domains', array(
                                        'options' => $domains,
                                        'class' => 'form-control select',
                                        'id' => 'domains',
                                        'multiple' => 'multiple',
                                        'label' => false,
                                        'div' => false,
                                        "size" => 1,
                                        'default' => $dm_default
                                    ));
                              ?>
                              <div class="competenciesmatch-all">
                                 <label>
                                    <input type="checkbox" id="match_all_domains" name="match_all_domains"  class="" value="0" disabled>
                                 <label class="fancy_label text-black" for="match_all_domains">Require All Selected Domains</label>
                                 </label>
                              </div>
                           </div>
                        </div>
                        <div class="competencies-show-match-right">
                           <div class="show-reset">
                              <input type="button" name="show_btn" id="" title="Show Matches" class="btn btn-success tipText show_btn btn-disabled"  value="Show">
							    <!--<button class="btn btn-success watchbtn"><i class="watchwhite18"></i> </button>-->
                              <button class="btn btn-danger tipText btn_reset" title="Reset" type="button"><i class="fa fa-times" aria-hidden="true"></i>
                              </button>
                           </div>
                           <div class="competenciesmatch-all">
                                <label>
                                    <input type="checkbox" id="match_all_checkbox" name="match_all_checkbox"  class="" value="0" disabled>

                                    <label class="fancy_label text-black" for="match_all_checkbox">Require All</label>
                                </label>
                           </div>
                        </div>
 </div>
				</div>
					<div class="competencies-body clearfix" style="/*min-height: 380px;*/" >
                     <div class="competencies-data-container">
                        <div class="competencies-buttons-container">
                           <div class="competencies-col-data competencies-col-data-1">
                              <div class="panel-heading">
                                   <span class="peoplecount-info">People (<span class="total-data">0</span>)</span>

                                 <span class="short-arrow-wrap">
								  <span class="short-arrow sorting sort-btns sort-fname tipText" title="Sort by First Name" data-sorted="asc">
									<i class="fa fa-sort" aria-hidden="true"></i>
									<i class="fa fa-sort-asc" aria-hidden="true"></i>
									<i class="fa fa-sort-desc" aria-hidden="true"></i>
								  </span>
									  <span class="short-arrow sorting sort-btns sort-lname tipText" title="Sort by Last Name" data-sorted="asc">
									<i class="fa fa-sort" aria-hidden="true"></i>
									<i class="fa fa-sort-asc" aria-hidden="true"></i>
									<i class="fa fa-sort-desc" aria-hidden="true"></i>
								  </span>
                                 </span>
                              </div>
                           </div>
                           <div class="competencies-col-data competencies-col-data-2">
                              <div class="panel-heading">
                                 Competencies
                                <span class="short-arrow-wrap">
                                   <span class="short-arrow sort-type tipText sorting" title="Sort by Type" data-sorted="asc" data-type="asc">
                                    <i class="fa fa-sort" aria-hidden="true"></i>
                                    <i class="fa fa-sort-asc" aria-hidden="true"></i>
                                    <i class="fa fa-sort-desc" aria-hidden="true"></i>
                                  </span>
                                   <span class="short-arrow sort-level tipText sorting" title="Sort by Level"  data-sorted="asc" data-type="asc">
                                    <i class="fa fa-sort" aria-hidden="true"></i>
                                    <i class="fa fa-sort-asc" aria-hidden="true"></i>
                                    <i class="fa fa-sort-desc" aria-hidden="true"></i>
                                  </span>
                                   <span class="short-arrow  sort-exp tipText sorting" title="Sort by Experience" data-sorted="asc" data-type="asc">
                                    <i class="fa fa-sort" aria-hidden="true"></i>
                                    <i class="fa fa-sort-asc" aria-hidden="true"></i>
                                    <i class="fa fa-sort-desc" aria-hidden="true"></i>
                                  </span>
                                   <span class="short-arrow sort-name tipText sorting" title="Sort by Name"  data-sorted="asc" data-type="asc">
                                    <i class="fa fa-sort" aria-hidden="true"></i>
                                    <i class="fa fa-sort-asc" aria-hidden="true"></i>
                                    <i class="fa fa-sort-desc" aria-hidden="true"></i>
                                  </span>
                                  <span class="short-arrow sort-match  tipText sorting"  title="Sort by Matches"  data-sorted="asc" data-type="asc">
                                    <i class="fa fa-sort" aria-hidden="true"></i>
                                    <i class="fa fa-sort-asc" aria-hidden="true"></i>
                                    <i class="fa fa-sort-desc" aria-hidden="true"></i>
                                  </span>
                                 </span>
                              </div>
                           </div>
                           <div class="competencies-col-data competencies-col-data-3">
                              <div class="panel-heading">
                                 Action
                              </div>
                           </div>
                        </div>
                        <div class="competencies-data data-wrapper" style="height: 100vh;min-height: 100vh;">
                           <div class="no-res-found">select skills, subjects and domains</div>
                        </div>
                     </div>
                  </div>
                  <!-- /.box-body -->
				</div>

            	<div id="tab_watch" class="tab-pane fade sks-tabs">
                    <input type="hidden" name="paging_offset" id="paging_offset" value="1">
                    <input type="hidden" name="paging_total" id="paging_total" value="0">
                    <input type="hidden" name="watch_users" id="watch_users" value="0">
                    <div class="watch-cont-main">
                        <div class="watch-icon-top-right">
                            <?php if($resourcer_permit){ ?>
                                <a href="#" class="tipText watch-to-planning common-btns hide" data-type="watch" title="Go To Planning"><i class="planningblack18"></i></a>
                            <?php } ?>
                            <a href="#" class="tipText watch-to-compare" data-original-title="Compare"><i class="compareblack18"></i></a>
                            <a href="#" class="tipText watch-to-engagement" data-original-title="People"><i class="peopleblack18"></i></a>
                        </div>
                        <div class="watch-header-wrap popup-select-icon">
                            <div class="watch-header-col1">
                                <label class="custom-dropdown" style="width: 100%;">
                                    <?php
                                    echo $this->Form->input('skills', array(
                                        'options' => $skills,
                                        'class' => 'form-control aqua',
                                        'id' => 'watch_skill',
                                        'multiple' => 'multiple',
                                        'label' => false,
                                        'div' => false,
                                        "size" => 1
                                    ));
                                     ?>
                                </label>
                                <label class="custom-dropdown" style="width: 100%;">
                                    <select class="form-control aqua" id="watch_skill_level" multiple="">
                                        <option value="Beginner">Beginner</option>
                                        <option value="Intermediate">Intermediate</option>
                                        <option value="Advanced">Advanced</option>
                                        <option value="Expert">Expert</option>
                                    </select>
                                </label>
                                <label class="custom-dropdown" style="width: 100%;">
                                    <select class="form-control aqua" id="watch_skill_exp" multiple="">
                                        <option value="1">1 Year</option>
                                        <option value="2">2 Years</option>
                                        <option value="3">3 Years</option>
                                        <option value="4">4 Years</option>
                                        <option value="5">5 Years</option>
                                        <option value="6-10">6-10 Years</option>
                                        <option value="11-15">11-15 Years</option>
                                        <option value="16-20">16-20 Years</option>
                                        <option value="Over 20">Over 20 Years</option>
                                    </select>
                                </label>
                            </div>
                            <div class="watch-header-col2">
                                <label class="custom-dropdown" style="width: 100%;">
                                    <?php
                                    // $subjects = htmlentity($subjects);
                                    echo $this->Form->input('subjects', array(
                                        'options' => $subjects,
                                        'class' => 'form-control aqua',
                                        'id' => 'watch_subject',
                                        'multiple' => 'multiple',
                                        'label' => false,
                                        'div' => false,
                                        "size" => 1,
                                        'default' => $sb_default
                                    ));
                                    ?>
                                </label>
                                <label class="custom-dropdown" style="width: 100%;">
                                    <select class="form-control aqua" id="watch_subject_level" multiple="">
                                        <option value="Beginner">Beginner</option>
                                        <option value="Intermediate">Intermediate</option>
                                        <option value="Advanced">Advanced</option>
                                        <option value="Expert">Expert</option>
                                    </select>
                                </label>
                                <label class="custom-dropdown" style="width: 100%;">
                                    <select class="form-control aqua" id="watch_subject_exp" multiple="">
                                        <option value="1">1 Year</option>
                                        <option value="2">2 Years</option>
                                        <option value="3">3 Years</option>
                                        <option value="4">4 Years</option>
                                        <option value="5">5 Years</option>
                                        <option value="6-10">6-10 Years</option>
                                        <option value="11-15">11-15 Years</option>
                                        <option value="16-20">16-20 Years</option>
                                        <option value="Over 20">Over 20 Years</option>
                                    </select>
                                </label>
                            </div>
                            <div class="watch-header-col3">
                                <label class="custom-dropdown" style="width: 100%;">
                                    <?php
                                    // $domains = htmlentity($domains);
                                    echo $this->Form->input('domains', array(
                                        'options' => $domains,
                                        'class' => 'form-control aqua',
                                        'id' => 'watch_domain',
                                        'multiple' => 'multiple',
                                        'label' => false,
                                        'div' => false,
                                        "size" => 1,
                                        'default' => $dm_default
                                    ));
                                    ?>
                                </label>
                                <label class="custom-dropdown" style="width: 100%;">
                                    <select class="form-control aqua" id="watch_domain_level" multiple="">
                                        <option value="Beginner">Beginner</option>
                                        <option value="Intermediate">Intermediate</option>
                                        <option value="Advanced">Advanced</option>
                                        <option value="Expert">Expert</option>
                                    </select>
                                </label>
                                <label class="custom-dropdown" style="width: 100%;">
                                    <select class="form-control aqua" id="watch_domain_exp" multiple="">
                                        <option value="1">1 Year</option>
                                        <option value="2">2 Years</option>
                                        <option value="3">3 Years</option>
                                        <option value="4">4 Years</option>
                                        <option value="5">5 Years</option>
                                        <option value="6-10">6-10 Years</option>
                                        <option value="11-15">11-15 Years</option>
                                        <option value="16-20">16-20 Years</option>
                                        <option value="Over 20">Over 20 Years</option>
                                    </select>
                                </label>
                            </div>
                            <div class="watch-header-col4">
                                <input type="button" name="show_user_btn" id="watch_showbtn" title="Show" class="btn btn-success tipText" disabled="true" value="Show">
                                <input type="button" name="show_reset_btn" id="watch_resetbtn" title="Reset" class="btn btn-danger tipText" value="Reset">
                                <input type="button" title="Watch" class="btn btn-success tipText" value="Watch" data-toggle="modal" data-target="#modal_set_watch" data-remote="<?php echo Router::Url(['controller' => 'competencies', 'action' => 'set_watch', 'admin' => false], true); ?>">
                            </div>
                        </div>
                        <div class="watch-data-wrap">
                            <div class="watch-data-scroll">
                                <div class="watch-col-header in-active">
                                    <div class="watch-col watch-col-1">
                                        People <span class="watch-count">(0)</span>
                                        <span class="com-short sort_order tipText" data-type="watch" data-by="first_name" data-order="" title="Sort By First Name">
                                            <i class="fa fa-sort" aria-hidden="true"></i>
                                            <i class="fa fa-sort-asc" aria-hidden="true"></i>
                                            <i class="fa fa-sort-desc" aria-hidden="true"></i>
                                        </span>
                                        <span class="com-short sort_order tipText" data-type="watch" data-by="last_name" data-order="" title="Sort By Last Name">
                                            <i class="fa fa-sort" aria-hidden="true"></i>
                                            <i class="fa fa-sort-asc" aria-hidden="true"></i>
                                            <i class="fa fa-sort-desc" aria-hidden="true"></i>
                                        </span>
                                    </div>
                                    <div class="watch-col watch-col-2">
                                        Competencies
                                        <span class="com-short sort_order tipText" data-type="watch" data-by="comp_type" data-order="" title="Sort By Type">
                                            <i class="fa fa-sort" aria-hidden="true"></i>
                                            <i class="fa fa-sort-asc" aria-hidden="true"></i>
                                            <i class="fa fa-sort-desc" aria-hidden="true"></i>
                                        </span>
                                        <span class="com-short sort_order tipText" data-type="watch" data-by="comp_level" data-order="" title="Sort By Level">
                                            <i class="fa fa-sort" aria-hidden="true"></i>
                                            <i class="fa fa-sort-asc" aria-hidden="true"></i>
                                            <i class="fa fa-sort-desc" aria-hidden="true"></i>
                                        </span>
                                        <span class="com-short sort_order tipText" data-type="watch" data-by="comp_experience" data-order="" title="Sort By Experience">
                                            <i class="fa fa-sort" aria-hidden="true"></i>
                                            <i class="fa fa-sort-asc" aria-hidden="true"></i>
                                            <i class="fa fa-sort-desc" aria-hidden="true"></i>
                                        </span>
                                        <span class="com-short sort_order tipText" data-type="watch" data-by="comp_name" data-order="" title="Sort By Name">
                                            <i class="fa fa-sort" aria-hidden="true"></i>
                                            <i class="fa fa-sort-asc" aria-hidden="true"></i>
                                            <i class="fa fa-sort-desc" aria-hidden="true"></i>
                                        </span>
                                        <span class="com-short sort_order tipText" data-type="watch" data-by="matches" data-order="" title="Sort By Matches">
                                            <i class="fa fa-sort" aria-hidden="true"></i>
                                            <i class="fa fa-sort-asc" aria-hidden="true"></i>
                                            <i class="fa fa-sort-desc" aria-hidden="true"></i>
                                        </span>
                                    </div>
                                    <div class="watch-col watch-col-3">
                                        Action
                                    </div>
                                </div>
                                <div class="watch-summary-data" data-flag="true">
                                    <!-- watch data -->
                                    <div class="no-res-found">select skills, subjects or domains</div>
                                </div>
                            </div>
                        </div>
                    </div>
            	</div>
				<div id="tab_compare" class="tab-pane fade sks-tabs">
                    <input type="hidden" name="user_offset" id="user_offset" value="0">
                    <input type="hidden" name="user_total" id="user_total" value="0">
                    <input type="hidden" name="paging_offset" id="paging_offset" value="1">
                    <input type="hidden" name="paging_total" id="paging_total" value="0">
                    <input type="hidden" name="compare_user_list" id="compare_user_list" value="0">
					<div class="compare-cont-main">
					<div class="compare-header-wrap">
                        <div class="compare-header-col1  popup-select-icon">
                            <label class="custom-dropdown comp-mb10" style="width: 100%;">
                                <select class="form-control aqua" id="sel_people_from">
                                    <option value="">Select People From</option>
                                        <option value="community">All Community</option>

                                        <option value="organizations">Specific Organizations</option>
                                        <option value="locations">Specific Locations</option>
                                        <option value="departments">Specific Departments</option>
                                        <option value="users">Specific People</option>
                                        <option value="tags">Specific Tags</option>
                                        <option value="skills">Specific Skills</option>
                                        <option value="subjects">Specific Subjects</option>
                                        <option value="domains">Specific Domains</option>
                                        <option value="all_projects">All My Projects</option>
                                        <option value="created_projects">Projects I Created</option>
                                        <option value="owner_projects">Projects I Own</option>
                                        <option value="shared_projects">Projects Shared With Me</option>
                                        <option value="project">Specific Projects</option>
                                </select>
                            </label>
                            <label class="custom-dropdown" style="width: 100%;">
                                <?php
								  $sk_default = '';
								   if((isset($sel_type) && !empty($sel_type) && $sel_type == 'sk') && (isset($sel_id) && !empty($sel_id))){
									$sk_default = $sel_id;
								   }
								  // $skills = htmlentity($skills);
								  echo $this->Form->input('skills', array(
											'options' => $skills,
											'class' => 'form-control select',
											'id' => 'skills_dd',
											'multiple' => 'multiple',
											'label' => false,
											'div' => false,
											"size" => 1,
											'default' => $sk_default
										));
								  ?>
                            </label>
                        </div>
                        <div class="compare-header-col2 popup-select-icon">
                            <div class="compare-selectbox comp-mb10">
								<?php if(isset($param_type) && !empty($param_type) && $resourcer){ ?>
                                <?php echo $this->Form->input('users', array('type' => 'select', 'options' => $people_list, 'label' => false, 'div' => false, 'class' => 'form-control aqua', 'id' => 'specific_item_1', 'multiple' => 'multiple', 'default' => array_keys($people_list) )); ?>
								<?php }else{ ?>
                                <select class="form-control aqua" id="specific_item_1" multiple="" disabled="">
                                </select>
								<?php } ?>
							</div>
                            <div class="compare-selectbox">
                                <?php
								  $sb_default = '';
								   if((isset($sel_type) && !empty($sel_type) && $sel_type == 'sb') && (isset($sel_id) && !empty($sel_id))){
									$sb_default = $sel_id;
								   }
									// $subjects = htmlentity($subjects);
									echo $this->Form->input('subjects', array(
											'options' => $subjects,
											'class' => 'form-control select',
											'id' => 'subjects_dd',
											'multiple' => 'multiple',
											'label' => false,
											'div' => false,
											"size" => 1,
											'default' => $sb_default
										));
								  ?>
                           </div>
                        </div>
                        <div class="compare-header-col3  popup-select-icon ">
							<div class="compare-sel-view">
								<span class="compare-sel-viewbox">
                                    <?php echo $this->Form->input('select_view', array('type' => 'select', 'options' => $userViewsList, 'label' => false, 'div' => false, 'class' => 'form-control aqua select-cview',  'id' => 'select-cview', 'empty' => 'Select View' )); ?>
								</span>
								<span class="compare-sel-view-icon">
								    <a href="#" class="disabled save-cview tipText" title="Save"> <i class="saveblack24"></i> </a>
									<a href="#" class="disabled save-as-cview open-save-as tipText" title="Save As"> <i class="saveasblack24"></i> </a>
									<a href="#" class="disabled delete-cview tipText" title="Delete"> <i class="deleteblack24"></i> </a>
								</span>

							</div>

                            <div class="compare-selct-dom">

                                    <label class="custom-dropdown" style="width: 100%;">
                                        <?php
										  $dm_default = '';
										   if((isset($sel_type) && !empty($sel_type) && $sel_type == 'dm') && (isset($sel_id) && !empty($sel_id))){
											$dm_default = $sel_id;
										   }
											// $domains = htmlentity($domains);
											echo $this->Form->input('domains', array(
													'options' => $domains,
													'class' => 'form-control select',
													'id' => 'domains_dd',
													'multiple' => 'multiple',
													'label' => false,
													'div' => false,
													"size" => 1,
													'default' => $dm_default
												));
										  ?>
                                    </label>


                            </div>

                                                                        </div>
                        <div class="compare-header-col4">
                            <input type="button" name="show_user_btn" id="compare_showbtn" title="Compare " class="btn btn-success compare_showbtn tipText" disabled="true" value="Compare">
                            <div class="compare-button">
                                <button class="btn btn-success tipText prev compare_prev" title="Previous" disabled="true"> &lt; </button>
                                <button class="btn btn-success tipText next compare_next" title="Next" disabled="true"> &gt; </button>
                            </div>
                        </div>
                    </div>


						<div class="compare-data-wrap" data-flag="true">
                            <div class="no-res-found">Select People and Competencies </div>
                        </div>


					   </div>
					</div>





					<div id="tab_skills" class="tab-pane fade sks-tabs">
                        <input type="hidden" name="paging_offset" id="paging_offset" value="1">
                        <input type="hidden" name="paging_total" id="paging_total" value="0">
						<div class="ssd-wrap">
						 <div class="ssd-col-header">
						  <div class="ssd-col ssd-col-1 sort_order active" data-coloumn="title" data-order="desc" data-type="skills">
							Name <span class="total-data">(0)</span> <i class="fa fa-sort" aria-hidden="true"></i> <i class="fa fa-sort-asc" aria-hidden="true"></i> <i class="fa fa-sort-desc" aria-hidden="true"></i>
						  </div>
							<div class="ssd-col ssd-col-2 sort_order" data-coloumn="totalpeople"  data-order="" data-type="skills">
							People <i class="fa fa-sort" aria-hidden="true"></i>  <i class="fa fa-sort-asc" aria-hidden="true"></i> <i class="fa fa-sort-desc" aria-hidden="true"></i>
						  </div>
						  <div class="ssd-col ssd-col-3">
							Community
              <span class="com-short sort_order tipText" title="Sort By Organizations" data-coloumn="totalorganization"  data-order="" data-type="skills">
                <i class="fa fa-sort" aria-hidden="true"></i>
                <i class="fa fa-sort-asc" aria-hidden="true"></i>
                <i class="fa fa-sort-desc" aria-hidden="true"></i>
              </span>
              <span class="com-short sort_order tipText" title="Sort By Locations" data-coloumn="totallocation"  data-order="" data-type="skills">
                <i class="fa fa-sort" aria-hidden="true"></i>
                <i class="fa fa-sort-asc" aria-hidden="true"></i>
                <i class="fa fa-sort-desc" aria-hidden="true"></i>
              </span><span class="com-short sort_order tipText" title="Sort By Departments" data-coloumn="totaldepartment"  data-order="" data-type="skills">
                <i class="fa fa-sort" aria-hidden="true"></i>
                <i class="fa fa-sort-asc" aria-hidden="true"></i>
                <i class="fa fa-sort-desc" aria-hidden="true"></i>
              </span>
						  </div>

						<!--<div class="ssd-col ssd-col-4 sort_order"  data-coloumn="totallocation"  data-order="" data-type="skills">
							Locations <i class="fa fa-sort" aria-hidden="true"></i>  <i class="fa fa-sort-asc" aria-hidden="true"></i> <i class="fa fa-sort-desc" aria-hidden="true"></i>
						</div>-->
						<div class="ssd-col ssd-col-5 sort_order" data-coloumn="total_story"  data-order="" data-type="skills">
							Stories <i class="fa fa-sort" aria-hidden="true"></i>
                <i class="fa fa-sort-asc" aria-hidden="true"></i>
                <i class="fa fa-sort-desc" aria-hidden="true"></i>
						  </div>
						<div class="ssd-col ssd-col-6 sort_order"  data-coloumn="linktotal"  data-order="" data-type="skills">
							Links <i class="fa fa-sort" aria-hidden="true"></i>  <i class="fa fa-sort-asc" aria-hidden="true"></i> <i class="fa fa-sort-desc" aria-hidden="true"></i>
						  </div>
						 <div class="ssd-col ssd-col-7 sort_order"  data-coloumn="filetotal"  data-order="" data-type="skills">
							Files <i class="fa fa-sort" aria-hidden="true"></i>  <i class="fa fa-sort-asc" aria-hidden="true"></i> <i class="fa fa-sort-desc" aria-hidden="true"></i>
						 </div>
						 <div class="ssd-col ssd-col-8 sort_order"  data-coloumn="totalkeyword"  data-order="" data-type="skills">
							Keywords <i class="fa fa-sort" aria-hidden="true"></i>  <i class="fa fa-sort-asc" aria-hidden="true"></i> <i class="fa fa-sort-desc" aria-hidden="true"></i>
						 </div>
						 <div class="ssd-col ssd-col-9 sort_order" data-coloumn="fullname"  data-order="" data-type="skills">
							Updated By  <i class="fa fa-sort" aria-hidden="true"></i>  <i class="fa fa-sort-asc" aria-hidden="true"></i> <i class="fa fa-sort-desc" aria-hidden="true"></i>
						  </div>
						 <div class="ssd-col ssd-col-10 sort_order" data-coloumn="modified"  data-order="" data-type="skills">
							Updated On <i class="fa fa-sort" aria-hidden="true"></i>  <i class="fa fa-sort-asc" aria-hidden="true"></i> <i class="fa fa-sort-desc" aria-hidden="true"></i>
						  </div>
							 <div class="ssd-col ssd-col-11">
							Actions
						  </div>

						</div>
							<div class="ssd-data skills-list-wrapper list-wrapper" data-type="skills" data-target="#tab_skills" data-flag="true">
							</div>
						</div>
					</div>
					<div id="tab_subjects" class="tab-pane fade ssd-tabs">
                        <input type="hidden" name="paging_offset" id="paging_offset" value="1">
                        <input type="hidden" name="paging_total" id="paging_total" value="0">
						<div class="ssd-wrap">
							<div class="ssd-col-header">

							<div class="ssd-col ssd-col-1 sort_order active" data-coloumn="title" data-order="desc" data-type="subjects">
							Name <span class="total-data">(0)</span> <i class="fa fa-sort" aria-hidden="true"></i> <i class="fa fa-sort-asc" aria-hidden="true"></i> <i class="fa fa-sort-desc" aria-hidden="true"></i>
							</div>

							<div class="ssd-col ssd-col-2 sort_order" data-coloumn="totalpeople"  data-order="" data-type="subjects">
							People <i class="fa fa-sort" aria-hidden="true"></i> <i class="fa fa-sort-asc" aria-hidden="true"></i> <i class="fa fa-sort-desc" aria-hidden="true"></i>
							</div>
							<div class="ssd-col ssd-col-3">
							Community
              <span class="com-short sort_order tipText" title="Sort By Organizations" data-coloumn="totalorganization" data-order="" data-type="subjects"> <i class="fa fa-sort" aria-hidden="true"></i>  <i class="fa fa-sort-asc" aria-hidden="true"></i> <i class="fa fa-sort-desc" aria-hidden="true"></i></span>
              <span class="com-short sort_order tipText" title="Sort By Locations" data-coloumn="totallocation" data-order="" data-type="subjects"> <i class="fa fa-sort" aria-hidden="true"></i>  <i class="fa fa-sort-asc" aria-hidden="true"></i> <i class="fa fa-sort-desc" aria-hidden="true"></i></span>
              <span class="com-short sort_order tipText" title="Sort By Departments" data-coloumn="totaldepartment" data-order="" data-type="subjects"> <i class="fa fa-sort" aria-hidden="true"></i>  <i class="fa fa-sort-asc" aria-hidden="true"></i> <i class="fa fa-sort-desc" aria-hidden="true"></i></span>
							</div>
							<!--<div class="ssd-col ssd-col-4 sort_order" data-coloumn="totallocation" data-order="" data-type="subjects">
							Locations <i class="fa fa-sort" aria-hidden="true"></i> <i class="fa fa-sort-asc" aria-hidden="true"></i> <i class="fa fa-sort-desc" aria-hidden="true"></i>
							</div>-->
							<div class="ssd-col ssd-col-5 sort_order" data-coloumn="total_story" data-order="" data-type="subjects">
							Stories <i class="fa fa-sort" aria-hidden="true"></i> <i class="fa fa-sort-asc" aria-hidden="true"></i> <i class="fa fa-sort-desc" aria-hidden="true"></i>
							</div>
							<div class="ssd-col ssd-col-6 sort_order" data-coloumn="linktotal" data-order="" data-type="subjects">
							Links <i class="fa fa-sort" aria-hidden="true"></i> <i class="fa fa-sort-asc" aria-hidden="true"></i> <i class="fa fa-sort-desc" aria-hidden="true"></i>
							</div>
							<div class="ssd-col ssd-col-7 sort_order"  data-coloumn="filetotal"  data-order="" data-type="subjects">
							Files <i class="fa fa-sort" aria-hidden="true"></i> <i class="fa fa-sort-asc" aria-hidden="true"></i> <i class="fa fa-sort-desc" aria-hidden="true"></i>
							</div>
							<div class="ssd-col ssd-col-8 sort_order"  data-coloumn="totalkeyword"  data-order="" data-type="subjects">
							Keywords <i class="fa fa-sort" aria-hidden="true"></i> <i class="fa fa-sort-asc" aria-hidden="true"></i> <i class="fa fa-sort-desc" aria-hidden="true"></i>
							</div>
							<div class="ssd-col ssd-col-9 sort_order" data-coloumn="fullname"  data-order="" data-type="subjects">
							Updated By  <i class="fa fa-sort" aria-hidden="true"></i> <i class="fa fa-sort-asc" aria-hidden="true"></i> <i class="fa fa-sort-desc" aria-hidden="true"></i>
							</div>
							<div class="ssd-col ssd-col-10 sort_order" data-coloumn="modified"  data-order="" data-type="subjects">
							Updated On <i class="fa fa-sort" aria-hidden="true"></i> <i class="fa fa-sort-asc" aria-hidden="true"></i> <i class="fa fa-sort-desc" aria-hidden="true"></i>
							</div>
							 <div class="ssd-col ssd-col-11">
							Actions
							</div>

							</div>
							<div class="ssd-data subjects-list-wrapper list-wrapper" data-type="subjects" data-target="#tab_subjects" data-flag="true">

						</div>

						</div>
					</div>
					<div id="tab_domains" class="tab-pane fade sdd-tabs">
                        <input type="hidden" name="paging_offset" id="paging_offset" value="1">
                        <input type="hidden" name="paging_total" id="paging_total" value="0">
						<div class="ssd-wrap">
							<div class="ssd-col-header">

								<div class="ssd-col ssd-col-1 sort_order active" data-coloumn="title" data-order="desc" data-type="domains">
								Name <span class="total-data">(0)</span> <i class="fa fa-sort" aria-hidden="true"></i><i class="fa fa-sort-asc" aria-hidden="true"></i> <i class="fa fa-sort-desc" aria-hidden="true"></i>

								</div>
								<div class="ssd-col ssd-col-2 sort_order" data-coloumn="totalpeople"  data-order="" data-type="domains">
								People <i class="fa fa-sort" aria-hidden="true"></i><i class="fa fa-sort-asc" aria-hidden="true"></i> <i class="fa fa-sort-desc" aria-hidden="true"></i>
								</div>
								<div class="ssd-col ssd-col-3">
								Community
                <span class="com-short sort_order tipText" title="Sort By Organizations"  data-coloumn="totalorganization"  data-order="" data-type="domains"> <i class="fa fa-sort" aria-hidden="true"></i>  <i class="fa fa-sort-asc" aria-hidden="true"></i> <i class="fa fa-sort-desc" aria-hidden="true"></i></span>
                <span class="com-short sort_order tipText" title="Sort By Locations"  data-coloumn="totallocation"  data-order="" data-type="domains"> <i class="fa fa-sort" aria-hidden="true"></i>  <i class="fa fa-sort-asc" aria-hidden="true"></i> <i class="fa fa-sort-desc" aria-hidden="true"></i></span>
                <span class="com-short sort_order tipText" title="Sort By Departments"  data-coloumn="totaldepartment"  data-order="" data-type="domains"> <i class="fa fa-sort" aria-hidden="true"></i>  <i class="fa fa-sort-asc" aria-hidden="true"></i> <i class="fa fa-sort-desc" aria-hidden="true"></i></span>
								</div>
								<!--<div class="ssd-col ssd-col-4 sort_order"  data-coloumn="totallocation"  data-order="" data-type="domains">
								Locations <i class="fa fa-sort" aria-hidden="true"></i><i class="fa fa-sort-asc" aria-hidden="true"></i> <i class="fa fa-sort-desc" aria-hidden="true"></i>
								</div>-->
								<div class="ssd-col ssd-col-5 sort_order"  data-coloumn="total_story"  data-order="" data-type="domains">
								Stories <i class="fa fa-sort" aria-hidden="true"></i><i class="fa fa-sort-asc" aria-hidden="true"></i> <i class="fa fa-sort-desc" aria-hidden="true"></i>
								</div>
								<div class="ssd-col ssd-col-6 sort_order"  data-coloumn="linktotal"  data-order="" data-type="domains">
								Links <i class="fa fa-sort" aria-hidden="true"></i><i class="fa fa-sort-asc" aria-hidden="true"></i> <i class="fa fa-sort-desc" aria-hidden="true"></i>
								</div>
								<div class="ssd-col ssd-col-7 sort_order"  data-coloumn="filetotal"  data-order="" data-type="domains">
								Files <i class="fa fa-sort" aria-hidden="true"></i><i class="fa fa-sort-asc" aria-hidden="true"></i> <i class="fa fa-sort-desc" aria-hidden="true"></i>
								</div>
								<div class="ssd-col ssd-col-8 sort_order"  data-coloumn="totalkeyword"  data-order="" data-type="domains">
								Keywords <i class="fa fa-sort" aria-hidden="true"></i><i class="fa fa-sort-asc" aria-hidden="true"></i> <i class="fa fa-sort-desc" aria-hidden="true"></i>
								</div>
								<div class="ssd-col ssd-col-9 sort_order" data-coloumn="fullname"  data-order="" data-type="domains">
								Updated By  <i class="fa fa-sort" aria-hidden="true"></i><i class="fa fa-sort-asc" aria-hidden="true"></i> <i class="fa fa-sort-desc" aria-hidden="true"></i>
								</div>
								<div class="ssd-col ssd-col-10 sort_order" data-coloumn="modified"  data-order="" data-type="domains">
								Updated On <i class="fa fa-sort" aria-hidden="true"></i><i class="fa fa-sort-asc" aria-hidden="true"></i> <i class="fa fa-sort-desc" aria-hidden="true"></i>
								</div>
								<div class="ssd-col ssd-col-11">
								Actions
								</div>

							</div>
							<div class="ssd-data domains-list-wrapper list-wrapper" data-type="domains" data-target="#tab_domains" data-flag="true">
						</div>

						</div>


					</div>

				</div>
			</div>

               </div>
               <!-- /.box -->
            </div>
         </div>
      </div>
   </div>
</div>

<div class="modal modal-danger fade " id="modal_delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content"></div>
	</div>
</div>

<div class="modal modal-primary fade " id="modal_create_skills" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content add-skill-t add-skill-popup-cont"></div>
	</div>
</div>
<div class="modal modal-success fade " id="modal_bulk_update" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content"></div>
	</div>
</div>

<div class="modal modal-danger fade " id="modal_confirm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h3 class="modal-title">Delete Selected <span class="selected-type-text"></span></h3>
            </div>
            <div class="modal-body">Are you sure you want to delete all selected <span class="selected-type-text"></span>?
                <input type="hidden" name="selected_ids" id="selected_ids">
                <input type="hidden" name="selected_type" id="selected_type">
            </div>
            <div class="modal-footer clearfix">
                <button type="button" class="btn btn-success bulk-delete-confirm">Delete</button>
                <button type="button" id="discard" class="btn btn-danger" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<div class="modal modal-success fade " id="modal_set_watch" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content"></div>
  </div>
</div>

<div class="modal modal-success fade " id="modal_save_compare_view" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog save-compare-view-popup">
    <div class="modal-content"></div>
  </div>
</div>

<input type="hidden" id="paging_page" value="0" />
<input type="hidden" id="paging_max_page" value="" />
<script type="text/javascript">
$(function(){

    $.resourcer_permit = <?php echo $resourcer_permit; ?>

    $.saveParameters = function(data, url, el, rpermit){

        if(data && rpermit) {
            $.ajax({
                url: $js_config.base_url + 'competencies/save_parameters',
                type:'POST',
                data: data,
                dataType: 'json',
                success: function( response ) {
                    if( response.success ) {
                        var paramID = response.content,
                            link = url + '/params:' + paramID;
                        $(el).attr('href', link);
                    }
                }
            });
        }
    }




    $('body').delegate('.sort-fname', 'click', function(event) {
        event.preventDefault();
        var params = {
            this: $(this)[0],
            attr: 'data-fname'
        }
        $.alphabate_sort(params);
    })

    $('#modal_confirm').on('hidden.bs.modal', function(event) {
        $(this).removeData('bs.modal');
        $(this).find('input[name=selected_ids]').val('');
        $(this).find('input[name=selected_type]').val('');
        $(this).find('.selected-type-text').text('');
    });
    $('.bulk-delete-confirm').on('click', function(event) {
        event.preventDefault();
        var $modal = $(this).parents('#modal_confirm'),
            ids = $modal.find('input[name=selected_ids]').val(),
            type = $modal.find('input[name=selected_type]').val();

        if(ids != '' && type != ''){
            $.ajax({
                url: $js_config.base_url + 'competencies/bulk_delete',
                type:'POST',
                data: $.param({'id': ids.split(','), 'dtype': type}),
                dataType: 'json',
                success: function( response ) {
                    if( response.success ) {
                        if(type == 'Skill') $.get_skills();
                        if(type == 'Subject') $.get_subjects();
                        if(type == 'Domain') $.get_domains();
                        $modal.modal('hide');
                        $modal.find('input[name=selected_ids]').val('');
                        $modal.find('input[name=selected_type]').val('');
                    }
                }
            });
        }
    });


    $('.competencies-tab .nav').css('cursor', 'default');
		$.current_delete = {};
		$('body').delegate('.delete_skill', 'click', function(event) {
			event.preventDefault();
			$.current_delete = $(this);
		});

		$('body').delegate('.delete_subject', 'click', function(event) {
			event.preventDefault();
			$.current_delete = $(this);
		});

		$('#modal_delete').on('hidden.bs.modal', function () {
			$(this).removeData('bs.modal');
			$(this).find('.modal-content').html('');
			$.current_delete = {};
		});


		$('#modal_view_skill').on('show.bs.modal', function(event) {
            $('.tooltip').hide();
        });

        $('#modal_create_skills').on('show.bs.modal', function(event) {
            $.current_skills_row = $(event.relatedTarget);
        })

		$('#modal_create_skills').on('hidden.bs.modal', function(event) {

			$(this).removeData('bs.modal');
            $(this).find('.modal-content').html('');

                //remove temp uploaded files
                var delete_files = [];
                if($.temp_data.main.filename != ''){
                    delete_files.push($.temp_data.main.filename);
                }

                $.each($.temp_data.file, function(index, el) {
                    if(el.filename != ''){
                        delete_files.push(el.filename);
                    }
                })
                if(delete_files.length > 0){
                    $.ajax({
                        url: $js_config.base_url + 'competencies/delete_temp_files',
                        type: 'POST',
                        dataType: 'json',
                        data: {files: delete_files},
                        success: function(response){
                            if(response.success){
                            }
                        }
                    })
                }
                //remove temp uploaded files
			if( $.modal_target == true && $.updatedata == 'add'  ){
                $.modal_target = false;
                if( $.add_type == "skill" ) {
                    $.get_skills();
                }
                else if( $.add_type == "subject" ){
                    $.get_subjects();
                }
                else if( $.add_type == "domain" ){
                    $.get_domains();
                }
			} else if( $.modal_target == true && $.updatedata == 'edit' ){
					$.modal_target = false;
				if( $.current_skills_row.length != undefined  ){
					$.updateCurrentRow($.current_skills_row.data('id'), $.current_skills_row, $.current_skills_row.data('type'));
				}

			}



        });

		$('#modal_bulk_update').on('hidden.bs.modal', function(event) {
			$(this).removeData('bs.modal');
            $(this).find('.modal-content').html('');
            // $('#modal_confirm').find('.selected-type-text').text('')
        });



        $('#com_modal').on('hidden.bs.modal', function(event) {
            event.preventDefault();
            $(this).find('.modal-content').html('');
            $(this).removeData('bs.modal')
            /* Act on the event */
        });

        var numberDisplayed = 1;
        if($(window).width() > 1024){
            numberDisplayed = 2;
        }

        $.skill_dd = $('#skills').multiselect({
            enableUserIcon: false,
            buttonClass: 'btn btn-default aqua',
            buttonWidth: '100%',
            buttonContainerWidth: '100%',
            numberDisplayed: numberDisplayed,
            maxHeight: '318',
            checkboxName: 'skills[]',
            includeSelectAllOption: true,
            enableFiltering: true,
            filterPlaceholder: 'Search Skills',
            enableCaseInsensitiveFiltering: true,
            nonSelectedText: 'Select Skills',
        });
        $.subject_dd = $('#subjects').multiselect({
            enableUserIcon: false,
            buttonClass: 'btn btn-default aqua',
            buttonWidth: '100%',
            buttonContainerWidth: '100%',
            numberDisplayed: numberDisplayed,
            maxHeight: '318',
            checkboxName: 'subjects[]',
            includeSelectAllOption: true,
            enableFiltering: true,
            filterPlaceholder: 'Search Subjects',
            enableCaseInsensitiveFiltering: true,
            nonSelectedText: 'Select Subjects',
        });
        $.domain_dd = $('#domains').multiselect({
            enableUserIcon: false,
            buttonClass: 'btn btn-default aqua',
            buttonWidth: '100%',
            buttonContainerWidth: '100%',
            numberDisplayed: numberDisplayed,
            maxHeight: '318',
            checkboxName: 'domains[]',
            includeSelectAllOption: true,
            enableFiltering: true,
            filterPlaceholder: 'Search Domains',
            enableCaseInsensitiveFiltering: true,
            nonSelectedText: 'Select Domains',
        });


		    $.skill_dd = $('#skills_dd').multiselect({
            enableUserIcon: false,
            buttonClass: 'btn btn-default aqua',
            buttonWidth: '100%',
            buttonContainerWidth: '100%',
            numberDisplayed: numberDisplayed,
            maxHeight: '318',
            checkboxName: 'skills[]',
            includeSelectAllOption: true,
            enableFiltering: true,
            filterPlaceholder: 'Search Skills',
            enableCaseInsensitiveFiltering: true,
            nonSelectedText: 'Select Skills',
			       onSelectAll:function(){
                $.btn_status();
            },
            onDeselectAll:function(){
                $.btn_status();
            },
            onChange: function(element, checked) {
                $.btn_status();
            }
        });
        $.subject_dd = $('#subjects_dd').multiselect({
            enableUserIcon: false,
            buttonClass: 'btn btn-default aqua',
            buttonWidth: '100%',
            buttonContainerWidth: '100%',
            numberDisplayed: numberDisplayed,
            maxHeight: '318',
            checkboxName: 'subjects[]',
            includeSelectAllOption: true,
            enableFiltering: true,
            filterPlaceholder: 'Search Subjects',
            enableCaseInsensitiveFiltering: true,
            nonSelectedText: 'Select Subjects',
			       onSelectAll:function(){
                $.btn_status();
            },
            onDeselectAll:function(){
                $.btn_status();
            },
            onChange: function(element, checked) {
                $.btn_status();
            }
        });
        $.domain_dd = $('#domains_dd').multiselect({
            enableUserIcon: false,
            buttonClass: 'btn btn-default aqua',
            buttonWidth: '100%',
            buttonContainerWidth: '100%',
            numberDisplayed: numberDisplayed,
            maxHeight: '318',
            checkboxName: 'domains[]',
            includeSelectAllOption: true,
            enableFiltering: true,
            filterPlaceholder: 'Search Domains',
            enableCaseInsensitiveFiltering: true,
            nonSelectedText: 'Select Domains',
			       onSelectAll:function(){
                $.btn_status();
            },
            onDeselectAll:function(){
                $.btn_status();
            },
            onChange: function(element, checked) {
                $.btn_status();
            }
        });


		$specific_items_1 = $('#specific_item_1').multiselect({
            enableUserIcon: false,
            buttonClass: 'btn btn-info aqua',
            buttonWidth: '100%',
            buttonContainerWidth: '100%',
            numberDisplayed: 0,
            maxHeight: '327',
            checkboxName: 'dept[]',
            includeSelectAllOption: true,
            enableFiltering: true,
            disableIfEmpty: true,
            filterPlaceholder: 'Search Specific Items',
            enableCaseInsensitiveFiltering: true,
            nonSelectedText: 'Select Specific Items',
            onSelectAll:function(){
                $.btn_status();
            },
            onDeselectAll:function(){
                $.btn_status();
            },
            onChange: function(element, checked) {
                $.btn_status();
            }
        });


        $('body').on('click', '.btn_reset', function(event){
            event.preventDefault();
            window.history.pushState(null,"", $js_config.base_url + "competencies");
            location.reload();
        })
        $('body').on('click', '.action-col-exp', function(event){
            event.preventDefault();
            $('.competencies-data-row').not($(this).parents('.competencies-data-row')).removeClass('expand-row')
            $(this).parents('.competencies-data-row').toggleClass('expand-row');
            if($(this).parents('.competencies-data-row').hasClass('expand-row')){
                $(this).attr('title', 'Collapse')
                          .tooltip('fixTitle')
                          .tooltip('show');
            }
            else{
                $(this).attr('title', 'Expand')
                          .tooltip('fixTitle')
                          .tooltip('show');
            }
        })

        $('body').on('change', '#match_all_checkbox', function(event){
            event.preventDefault();
			$(this).prop('checked')
        })

        $('body').on('change', 'select#skills, select#subjects, select#domains', function(event){
            event.preventDefault();
            var skills = $('select#skills').val(),
                subjects = $('select#subjects').val(),
                domains = $('select#domains').val();
            if((skills === undefined || skills == null) && (subjects === undefined || subjects == null) && (domains === undefined || domains == null)){
                $('.show_btn').addClass('btn-disabled');
                $('#match_all_skills,#match_all_subject,#match_all_domains,#match_all_checkbox').prop('disabled', true)
            }
            else{
                $('.show_btn').removeClass('btn-disabled');
                $('#match_all_skills,#match_all_subject,#match_all_domains,#match_all_checkbox').prop('disabled', false)
            }
        })
        <?php
        if((isset($sel_type) && !empty($sel_type) && ($sel_type == 'sk' || $sel_type == 'sb' || $sel_type == 'dm')) && (isset($sel_id) && !empty($sel_id))){
        ?>
        $('.show_btn').removeClass('btn-disabled');
        setTimeout(function(){
            $('.show_btn').trigger('click');
        }, 1000)
        <?php
        }
        ?>

        $('body').on('click', '.show_btn', function(event){
            event.preventDefault();

            var selectedSkills = $('#skills').val(),
                selectedSubjects = $('#subjects').val(),
                selectedDomains = $('#domains').val(),
                skill_match = $('#match_all_skills').prop('checked') ? 1 : 0,
                subject_match = $('#match_all_subject').prop('checked') ? 1 : 0,
                domain_match = $('#match_all_domains').prop('checked') ? 1 : 0,
                match_all = $('#match_all_checkbox').prop('checked') ? 1 : 0,
                data = {skills: selectedSkills, subjects: selectedSubjects, domains: selectedDomains, skill_match: skill_match, subject_match: subject_match, domain_match: domain_match,match_all:match_all};

            $.ajax({
                url: $js_config.base_url + 'competencies/data_listing',
                type: 'POST',
                dataType: 'json',
                data: data,
                success: function(response) {
                   $('.competencies-data-container').html(response);
                   $.pagingCount(data).done(function(users){
                        $(".search-to-planning").removeClass('hide');
                        $(".to-engagement").show();
                        $(".to-compare").show();
                   });

                   $.getSearchUsers(data).done(function(users){
                        var link = $js_config.base_url+'resources/planning';
                        var el = $('.search-to-planning')[0];
                        var params = {type: 'people', 'params': users};
                        $.saveParameters(params, link, el, $.resourcer_permit);

                        var link = $js_config.base_url+'resources/people';
                        var el = $('.to-engagement')[0];
                        var params = {type: 'user', 'params': users};
                        $.saveParameters(params, link, el, true);
                   })
                   ;
                    $.adjust_resize();
                }
            })
       })

    // RESIZE MAIN FRAME
    $('html').addClass('no-scroll');
    ($.adjust_resize = function(){
        $(".competencies-data.data-wrapper").animate({
            minHeight: (($(window).height() - $(".competencies-data.data-wrapper").offset().top) ) - 17,
            maxHeight: (($(window).height() - $(".competencies-data.data-wrapper").offset().top) ) - 17
        }, 1)
        $(".compare-data-wrap").animate({
            minHeight: (($(window).height() - $(".compare-data-wrap").offset().top) ) - 17,
            maxHeight: (($(window).height() - $(".compare-data-wrap").offset().top) ) - 17
        }, 1)
        $('.skills-list-wrapper').animate({
            minHeight: (($(window).height() - $('.skills-list-wrapper').offset().top) ) - 17,
            maxHeight: (($(window).height() - $('.skills-list-wrapper').offset().top) ) - 17
        }, 1)
        $('.subjects-list-wrapper').animate({
            minHeight: (($(window).height() - $('.subjects-list-wrapper').offset().top) ) - 17,
            maxHeight: (($(window).height() - $('.subjects-list-wrapper').offset().top) ) - 17
        }, 1)
        $('.domains-list-wrapper').animate({
            minHeight: (($(window).height() - $('.domains-list-wrapper').offset().top) ) - 17,
            maxHeight: (($(window).height() - $('.domains-list-wrapper').offset().top) ) - 17
        }, 1)
        $('.watch-summary-data').animate({
            minHeight: (($(window).height() - $('.watch-summary-data').offset().top) ) - 17,
            maxHeight: (($(window).height() - $('.watch-summary-data').offset().top) ) - 17
        }, 1)
    })();

    // WHEN DOM STOP LOADING CHECK AGAIN FOR MAIN FRAME RESIZING
    var interval = setInterval(function() {
        if (document.readyState === 'complete') {
            $.adjust_resize();
            clearInterval(interval);
        }
    }, 1);

    // RESIZE FRAME ON SIDEBAR TOGGLE EVENT
    $(".sidebar-toggle").on('click', function() {
        $.adjust_resize();
        const fix = setInterval( () => { window.dispatchEvent(new Event('resize')); }, 300 );
        setTimeout( () => clearInterval(fix), 1500);
    })

    // RESIZE FRAME ON WINDOW RESIZE EVENT
    $(window).resize(function() {
        $.adjust_resize();
    })

    $("#competency_tabs").on('show.bs.tab', function(e){
        // $.adjust_resize();
        const fix = setInterval( () => { window.dispatchEvent(new Event('resize')); }, 300 );
        setTimeout( () => clearInterval(fix), 1000);
    })

        $.default_state = function(type){
            $('.search-box').hide();
            $('.common-btns').addClass('hide');
            if(type != ''){
                $('.search-box[data-type="'+type+'"]').show();
                $('.'+type+'-button').removeClass('hide');
            }
        }
        $.current_tab = '';
        $("#competency_tabs").on('shown.bs.tab', function(e){

            var $this = $(e.target);
            if($this.is('.skill-tab')){
				$(".analytic-btn").removeClass('hide');
                var parent = $('.skill-list-wrapper').parents('.tab-pane:first');
                $.countRows('skills', parent);
                $('.search-skills-box').addClass('viewed');
                $.default_state('skills');
                $(".clear-btn").hide();
                $(".search-btn").show();
                $(".to-engagement").hide();
                $(".to-compare").hide();
                $(".ico-project-summary").hide();
                if($('.search-box[data-type="skills"]').val() != ''){
                    $(".clear-btn").show();
                    $(".search-btn").hide();
                }
            }
            else if($this.is('.subject-tab')){
                $(".analytic-btn").removeClass('hide');
                var parent = $('.subjects-list-wrapper').parents('.tab-pane:first');
                $.countRows('subjects', parent);
                $('.search-skills-box').addClass('viewed');
                $.default_state('subjects');
                $(".clear-btn").hide();
                $(".search-btn").show();
                $(".to-engagement").hide();
                $(".to-compare").hide();
                $(".ico-project-summary").hide();
                if($('.search-box[data-type="subjects"]').val() != ''){
                    $(".clear-btn").show();
                    $(".search-btn").hide();
                }
            }
            else if($this.is('.domain-tab')){
                $(".analytic-btn").removeClass('hide');
                var parent = $('.domains-list-wrapper').parents('.tab-pane:first');
                $.countRows('domains', parent);
                $('.search-skills-box').addClass('viewed');
                $.default_state('domains');
                $(".clear-btn").hide();
                $(".search-btn").show();
                $(".to-engagement").hide();
                $(".to-compare").hide();
                $(".ico-project-summary").hide();
                if($('.search-box[data-type="domains"]').val() != ''){
                    $(".clear-btn").show();
                    $(".search-btn").hide();
                }
            }
            else if($this.is('.compares_tab')){
                $(".to-engagement").hide();
                $(".to-compare").hide();
                $(".clear-btn").hide();
                $(".search-btn").hide();
                $('.common-btns[data-type="skills"],.common-btns[data-type="subjects"],.common-btns[data-type="domains"]').addClass('hide');
                $('.search-box').hide();
                if($('.compare-left-cont-outer .compare-row').length > 0){
                  $(".compare-to-planning").removeClass('hide');
                }
                $(".ico-project-summary").show();

                $(".search-to-planning").addClass('hide');
                $(".watch-to-planning").addClass('hide');
            }
            else if($this.is('.watch_tab')) {
                $(".to-engagement").hide();
                $(".to-compare").hide();
                $(".clear-btn").hide();
                $(".search-btn").hide();
                $('.common-btns[data-type="skills"],.common-btns[data-type="subjects"],.common-btns[data-type="domains"]').addClass('hide');
                $('.search-box').hide();
                $(".ico-project-summary").hide();

                if($('.watch-summary-data .watch-col-row').length > 0){
                  $(".watch-to-planning").removeClass('hide');
                }

                $(".compare-to-planning").addClass('hide');
                $(".search-to-planning").addClass('hide');
            }
            else{
                $(".ico-project-summary").show();
                $('.search-skills-box').removeClass('viewed');
                $(".analytic-btn").addClass('hide');
                if(!$('.show_btn').hasClass('btn-disabled')){
                    // $('.show_btn').trigger('click');
                }

                $(".compare-to-planning").addClass('hide');
                $(".watch-to-planning").addClass('hide');
                $.default_state('');
                if($(".competencies-data.data-wrapper .competencies-data-row").length > 0){
                    setTimeout(function(){
                        $(".to-engagement").show();
                        $(".to-compare").show();
                        setTimeout(function(){ $(".search-to-planning").removeClass('hide'); },100)
                    },100)
                }
            }
            $.adjust_resize();
        })

        /* TABS PAGINATION */

        $('.list-wrapper').scroll(function() { //watches scroll of the div
            $('.tooltip').hide()
            var $this = $(this);
            var $parent = $this.parents('.tab-pane:first');
            clearTimeout($.data(this, 'scrollTimer'));
            $.data(this, 'scrollTimer', setTimeout(function() {
                if($this.scrollTop() + $this.innerHeight() + 5 >= $this[0].scrollHeight)  {
					$.updateOffset($this, $parent);
                }
            }, 250));
        });

        $.countRows = function(type, parent, searchfilter = 0) {
            var dfd = $.Deferred();

            var order = 'asc',
            coloumn = 'title';
            if( $('.ssd-wrap .ssd-col-header', parent).find('.sort_order.active').length > 0 ) {
                order = $('.ssd-wrap .ssd-col-header', parent).find('.sort_order.active').data('order'),
                coloumn = $('.ssd-wrap .ssd-col-header', parent).find('.sort_order.active').data('coloumn');

                if( order == 'asc' ){
                    order = 'desc';
                } else {
                    order = 'asc';
                }
            }
            //

            var searchtext = $.trim($('.search-box[data-type="'+type+'"]').val());
            if(searchtext != ''){
                $('.list-wrapper', parent).scrollTop(0)
            }

            var data = { type: type, order: order, coloumn: coloumn, q: searchtext};


            $.ajax({
                url: $js_config.base_url + 'competencies/tab_paging_count',
                data: data,
                type: 'post',
                dataType: 'JSON',
                success: function(response) {
                    $('#paging_offset', parent).val(0);
                    $('#paging_total', parent).val(response);
                    $('.ssd-wrap .total-data', parent).text('('+response+')');

  					if( searchfilter == 0 ){
  						if(response <= 0){
  							$('.search-box[data-type="'+type+'"]').addClass('searchdisabled').prop('disabled', true);
  						}
  						else{
  							$('.search-box[data-type="'+type+'"]').removeClass('searchdisabled').prop('disabled', false);
  						}
  					}
                    dfd.resolve('paging count');
                }
            })
            return dfd.promise();
        }

        $.tab_paging_offset = 50;
        $.updateOffset = function(wrapper, parent){
            var page = parseInt($('#paging_offset', parent).val());
            var max_page = parseInt($('#paging_total', parent).val());
            var last_page = Math.ceil(max_page/$.tab_paging_offset);
            //console.log(page, max_page, last_page, wrapper.data('flag'), wrapper, parent)

            if(page < last_page - 1 && wrapper.data('flag')){
                $('#paging_offset', parent).val(page + 1);
                offset = ( parseInt($('#paging_offset', parent).val()) * $.tab_paging_offset);
                $.getPagingData(offset, wrapper, parent);
            }
        }

        $.getPagingData = function(page, wrapper, parent){
            wrapper.data('flag', false);
            var $wrapper = wrapper;

      			//added by me ******************
      			var order = 'asc',
      			coloumn = 'title';
      			if( $wrapper.parents('.ssd-wrap').find('.active') ) {
      				order = $wrapper.parents('.ssd-wrap').find('.active').data('order'),
      				coloumn = $wrapper.parents('.ssd-wrap').find('.active').data('coloumn');

      				if( order == 'asc' ){
      					order = 'desc';
      				} else {
      					order = 'asc';
      				}
      			}

                  var type = $wrapper.data('type');
      			var searchtext = $('.search-box[data-type="'+type+'"]').val();

      			/**********************************/

            var data = {page: page, type: type, order: order, coloumn: coloumn, q: searchtext};

            $.ajax({
                type: "POST",
                url: $js_config.base_url + "competencies/tab_paging_data",
                data: data,
                dataType: 'JSON',
                success: function(html) {
                    $wrapper.append(html);
                    wrapper.data('flag', true);
                }
             });
        }
        /* TABS PAGINATION */

        /* PAGINATION */
        $.pagingCount = function(data) {
            var dfd = $.Deferred();
            $.ajax({
                url: $js_config.base_url + 'competencies/paging_count',
                data: data,
                type: 'post',
                dataType: 'JSON',
                success: function(response) {
                    $('#paging_page').val(0);
                    $('#paging_max_page').val(response);
                    $('.peoplecount-info .people-count').text(response);

                      var allUsers = $('.competencies-data.data-wrapper .competencies-data-row').slice(0, $.tab_paging_offset).map(function() {
                          return $(this).data('user');
                      }).get();
                      allUsers = allUsers.join(',');
                      $('#search_user_list').val(allUsers)
                    if(response > 0){
                          $('.to-engagement').show();
                          $('.to-compare').show();
                          $(".search-to-planning").removeClass('hide');
                      }
                    else{
                      $(".search-to-planning").addClass('hide');
                      $('.to-engagement').hide();
                      $('.to-compare').hide();
                    }

                    var resultUsers = $('.competencies-data.data-wrapper .competencies-data-row').map(function() {
                          return $(this).data('user');
                    }).get();
                    resultUsers = resultUsers.join(',');
                    dfd.resolve(resultUsers);
                }
            })
            return dfd.promise();
        }
        $.getSearchUsers = function(data) {
            var dfd = $.Deferred();
            data.list = true;
            $.ajax({
                url: $js_config.base_url + 'competencies/paging_count',
                data: data,
                type: 'post',
                dataType: 'JSON',
                success: function(response) {
                    dfd.resolve(response);
                }
            })
            return dfd.promise();
        }

        $.paging_offset = 50;
        $.loading_data = true;
        $.pageCountUpdate = function(){
            var page = parseInt($('#paging_page').val());
            var max_page = parseInt($('#paging_max_page').val());
            var last_page = Math.ceil(max_page/$.paging_offset);

            if(page < last_page - 1 && $.loading_data){
                $('#paging_page').val(page + 1);
                offset = ( parseInt($('#paging_page').val()) * $.paging_offset);
                $.getPosts(offset);
            }
        }

        $.getPosts = function(page){
            $.loading_data = false;
            var $outerPane = $('.competencies-data.data-wrapper');
            $('#loading').remove();

            var selectedSkills = $('#skills').val(),
                selectedSubjects = $('#subjects').val(),
                selectedDomains = $('#domains').val(),
                skill_match = $('#match_all_skills').prop('checked') ? 1 : 0,
                subject_match = $('#match_all_subject').prop('checked') ? 1 : 0,
                domain_match = $('#match_all_domains').prop('checked') ? 1 : 0,
                match_all = $('#match_all_checkbox').prop('checked') ? 1 : 0,
                data = {skills: selectedSkills, subjects: selectedSubjects, domains: selectedDomains, skill_match: skill_match, subject_match: subject_match, domain_match: domain_match,match_all:match_all, page: page};

            $.ajax({
                type: "POST",
                url: $js_config.base_url + "competencies/paging_data",
                data: data,
                dataType: 'JSON',
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

        }
        /* PAGINATION */

		$('body').on("click", ".skill-button, .subject-button, .domain-button", function(){

			// $('.search-box').val('').trigger('keyup');

		})


	$('body').on('mouseenter','.sks-title', function(){

		$(this).tooltip({
			html: true,
			template: '<div class="tooltip" style="text-transform:none !important;"><div class="tooltip-arrow"></div><div class="tooltip-inner" style="text-transform:none !important;"> </div></div>',
			container: 'body',
			placement: "top"
		}).tooltip('show');

	})

    $.watch_skill = $('#watch_skill').multiselect({
        enableUserIcon: false,
        buttonClass: 'btn btn-default aqua',
        buttonWidth: '100%',
        buttonContainerWidth: '100%',
        numberDisplayed: numberDisplayed,
        maxHeight: '318',
        checkboxName: 'skills[]',
        includeSelectAllOption: true,
        enableFiltering: true,
        filterPlaceholder: 'Search Skills',
        enableCaseInsensitiveFiltering: true,
        nonSelectedText: 'Select Skills',
    });
    $.watch_skill_level = $('#watch_skill_level').multiselect({
        enableUserIcon: false,
        buttonClass: 'btn btn-default aqua',
        buttonWidth: '100%',
        buttonContainerWidth: '100%',
        numberDisplayed: numberDisplayed,
        maxHeight: '318',
        checkboxName: 'skills[]',
        includeSelectAllOption: true,
        enableFiltering: true,
        filterPlaceholder: 'Search Skill Levels',
        enableCaseInsensitiveFiltering: true,
        nonSelectedText: 'Select Skill Levels',
    });
    $.watch_skill_exp = $('#watch_skill_exp').multiselect({
        enableUserIcon: false,
        buttonClass: 'btn btn-default aqua',
        buttonWidth: '100%',
        buttonContainerWidth: '100%',
        numberDisplayed: numberDisplayed,
        maxHeight: '318',
        checkboxName: 'skills[]',
        includeSelectAllOption: true,
        enableFiltering: true,
        filterPlaceholder: 'Search Skill Experience',
        enableCaseInsensitiveFiltering: true,
        nonSelectedText: 'Select Skill Experience',
    });
    $.watch_subject = $('#watch_subject').multiselect({
        enableUserIcon: false,
        buttonClass: 'btn btn-default aqua',
        buttonWidth: '100%',
        buttonContainerWidth: '100%',
        numberDisplayed: numberDisplayed,
        maxHeight: '318',
        checkboxName: 'subjects[]',
        includeSelectAllOption: true,
        enableFiltering: true,
        filterPlaceholder: 'Search Subjects',
        enableCaseInsensitiveFiltering: true,
        nonSelectedText: 'Select Subjects',
    });
    $.watch_subject_level = $('#watch_subject_level').multiselect({
        enableUserIcon: false,
        buttonClass: 'btn btn-default aqua',
        buttonWidth: '100%',
        buttonContainerWidth: '100%',
        numberDisplayed: numberDisplayed,
        maxHeight: '318',
        checkboxName: 'subjects[]',
        includeSelectAllOption: true,
        enableFiltering: true,
        filterPlaceholder: 'Search Subject Levels',
        enableCaseInsensitiveFiltering: true,
        nonSelectedText: 'Select Subject Levels',
    });
    $.watch_subject_exp = $('#watch_subject_exp').multiselect({
        enableUserIcon: false,
        buttonClass: 'btn btn-default aqua',
        buttonWidth: '100%',
        buttonContainerWidth: '100%',
        numberDisplayed: numberDisplayed,
        maxHeight: '318',
        checkboxName: 'subjects[]',
        includeSelectAllOption: true,
        enableFiltering: true,
        filterPlaceholder: 'Search Subject Experience',
        enableCaseInsensitiveFiltering: true,
        nonSelectedText: 'Select Subject Experience',
    });
    $.watch_domain = $('#watch_domain').multiselect({
        enableUserIcon: false,
        buttonClass: 'btn btn-default aqua',
        buttonWidth: '100%',
        buttonContainerWidth: '100%',
        numberDisplayed: numberDisplayed,
        maxHeight: '318',
        checkboxName: 'domains[]',
        includeSelectAllOption: true,
        enableFiltering: true,
        filterPlaceholder: 'Search Domains',
        enableCaseInsensitiveFiltering: true,
        nonSelectedText: 'Select Domains',
    });
    $.watch_domain_level = $('#watch_domain_level').multiselect({
        enableUserIcon: false,
        buttonClass: 'btn btn-default aqua',
        buttonWidth: '100%',
        buttonContainerWidth: '100%',
        numberDisplayed: numberDisplayed,
        maxHeight: '318',
        checkboxName: 'domains[]',
        includeSelectAllOption: true,
        enableFiltering: true,
        filterPlaceholder: 'Search Domain Levels',
        enableCaseInsensitiveFiltering: true,
        nonSelectedText: 'Select Domain Levels',
    });
    $.watch_domain_exp = $('#watch_domain_exp').multiselect({
        enableUserIcon: false,
        buttonClass: 'btn btn-default aqua',
        buttonWidth: '100%',
        buttonContainerWidth: '100%',
        numberDisplayed: numberDisplayed,
        maxHeight: '318',
        checkboxName: 'domains[]',
        includeSelectAllOption: true,
        enableFiltering: true,
        filterPlaceholder: 'Search Domain Experience',
        enableCaseInsensitiveFiltering: true,
        nonSelectedText: 'Select Domain Experience',
    });
   })
</script>

