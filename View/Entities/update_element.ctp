<?php
    // pr($permit_data, 1);
    echo $this->Html->script('jquery.cookie', array('inline' => true));
    //echo $this->Html->script('projects/plugins/jquery.dot', array('inline' => true));
    echo $this->Html->script('projects/plugins/wysi-b3-editor/lib/js/wysihtml5-0.3.0', array('inline' => true));
    echo $this->Html->script('projects/plugins/wysi-b3-editor/bootstrap3-wysihtml5', array('inline' => true));
    echo $this->Html->script('projects/plugins/cd.tabs', array('inline' => true));
    //echo $this->Html->script('projects/plugins/wysihtml5.editor', array('inline' => true));

    echo $this->Html->script('projects/plugins/paste.image.browser', array('inline' => true));
    echo $this->Html->script('projects/plugins/advanced_editor_options', array('inline' => true));
    //echo $this->Html->script('projects/plugins/ellipsis-word', array('inline' => true));
    // echo $this->Html->script('projects/plugins/editInPlace', array('inline' => true));

    echo $this->Html->script('projects/update_element', array('inline' => true));
    echo $this->Html->script('projects/date_format', array('inline' => true));
    //echo $this->Html->script('projects/feedback_votes', array('inline' => true));
    echo $this->Html->css('projects/feedback-vote');

    echo $this->Html->css('projects/uploadfile');
    echo $this->Html->script('jquery.validate', array('inline' => true));
    echo $this->Html->script('custom_validate', array('inline' => true));

    echo $this->Html->css('token-input-facebook');
    // echo $this->Html->css('easyui');

    echo $this->Html->script('jquery.tokeninput', array('inline' => true));

    echo $this->Html->script('projects/mm_settings', array('inline' => true));
    echo $this->Html->css('projects/bootstrap-input');
    echo $this->Html->css('projects/advanced_editor_options');



    echo $this->Html->css('projects/bs-selectbox/bootstrap-multiselect');
    echo $this->Html->script('projects/plugins/selectbox/bootstrap-multiselect', array('inline' => true));


?>
<!-- 08 jul 2016 For delete popup -->


<?php echo $this->Html->css('projects/update_element'); ?>
<?php
echo $this->Html->css('projects/bs.checkbox');
echo $this->Html->css('projects/taskshecdule');
?>
<style>
    .bootstrap-dialog.type-success .modal-header {
        background-color: #67a028 ;
    }
    .popover-content {
    	height:auto !important;
    }
    #confirm-box #modal_header {
        background: #d9534f none repeat scroll 0 0;
        color: #fff;
        font-size: 16px;
    }

    #confirm-box .modal-footer .btn+.btn {
        margin-left: 0px;
    }

    .documents-create .btn_progress span.text{
    	display: inline;
    }
    .showdecision{
    	opacity:0.6;
    }
    .signofftask{
    	pointer-events:none;
    }
    .signoffpointer{
    	pointer-events:none;
    }
	section.content{
		padding-top: 0;
	}
	.asset_counter .progress-assets i{
		cursor: default !important;
	}
</style>
<?php
    $date_workspace = $this->Common->getDateStartOrEnd_elm($workspace_id);
    $cur_date = date("d-m-Y");
    $mindate_project = isset($prj[0]['Project']['start_date']) && !empty($prj[0]['Project']['start_date']) ? $prj[0]['Project']['start_date'] : '';
    $maxdate_project = isset($prj[0]['Project']['end_date']) && !empty($prj[0]['Project']['end_date']) ? $prj[0]['Project']['end_date'] : '';


    $mindate_workspace = isset($date_workspace['start_date']) && !empty($date_workspace['start_date']) ? date("d-m-Y", strtotime($date_workspace['start_date'])) : '';

    if(isset($mindate_workspace) && !empty($mindate_workspace)){
    //$mindate_workspace = $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($mindate_workspace)),$format = 'd-m-Y');
    $mindate_workspace =   date('d-m-Y', strtotime($mindate_workspace));

    }

    $maxdate_workspace = isset($date_workspace['end_date']) && !empty($date_workspace['end_date']) ? date("d-m-Y", strtotime($date_workspace['end_date'])) : '';

    if(isset($maxdate_workspace) && !empty($maxdate_workspace)){
    //$maxdate_workspace = $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($maxdate_workspace)),$format = 'd-m-Y');
    $maxdate_workspace = date('d-m-Y', strtotime($maxdate_workspace));
    }

    //$mindate_workspace = ($mindate_workspace < $cur_date) ? $cur_date : $mindate_workspace;

    $mindate_elm = isset($this->request->data['Element']['start_date']) && !empty($this->request->data['Element']['start_date']) ? date("d-m-Y", strtotime($this->request->data['Element']['start_date'])) : '';

    if(isset($mindate_elm) && !empty($mindate_elm)){
   // $mindate_elm = $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($mindate_elm)),$format = 'd-m-Y');
    $mindate_elm = date('d-m-Y', strtotime($mindate_elm));
    }

    $maxdate_elm = isset($this->request->data['Element']['end_date']) && !empty($this->request->data['Element']['end_date']) ? date("d-m-Y", strtotime($this->request->data['Element']['end_date'])) : '';


    if(isset($maxdate_elm) && !empty($maxdate_elm)){
    //$maxdate_elm = $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($maxdate_elm)),$format = 'd-m-Y');
    $maxdate_elm = date('d-m-Y', strtotime($maxdate_elm));
    }


    $messageVar = 'Task';

    if (isset($mindate_elm) && empty($mindate_elm)) {
        if (isset($mindate_workspace) && !empty($mindate_workspace)) {
            $mindate_elm = $mindate_workspace;

            $messageVar = 'Workspace';
        } else if (isset($mindate_workspace) && empty($mindate_workspace)) {
            $mindate_elm = $mindate_project;
            $messageVar = 'Project';
        } else {
            $mindate_elm = '';
        }
    }
    if (isset($maxdate_elm) && empty($maxdate_elm)) {
        if (isset($maxdate_workspace) && !empty($maxdate_workspace)) {
            $maxdate_elm = $maxdate_workspace;
            $messageVar = 'Workspace';
        } else if (isset($maxdate_workspace) && empty($maxdate_workspace)) {
            $maxdate_elm = $maxdate_project;
            $messageVar = 'Project';
        } else {
            $maxdate_elm = '';
        }
    }
    if( !empty($mindate_workspace) ){
    	$mindate_elm_cal = $mindate_workspace;
    } else {
    	$mindate_elm_cal = $cur_date;
    }

    $ele_signoff = false;
    $tasksignoffcls = '';
    if( isset($date_workspace['sign_off']) && !empty($date_workspace['sign_off']) && $date_workspace['sign_off'] > 0 ){

    	$ele_signoff = true;
    	$tasksignoffcls = 'signofftask';

    } else if( isset($this->data['Element']) && !empty($this->data['Element']['sign_off']) && $this->data['Element']['sign_off'] > 0 ){
    		$ele_signoff = true;
    		$tasksignoffcls = 'signofftask';
    	}

    include 'element_files/datepicker.ctp';

    $created = '';
    if (isset($projectDetail) && !empty($projectDetail)) {
    	$created = $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',$projectDetail['Project']['created']),$format = 'F j, Y, g:i a');
    }
    //$stdate = $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($this->data['Element']['start_date'])),$format = 'd-m-Y');
    $stdate = date('d-m-Y', strtotime($this->data['Element']['start_date']));
?>

<?php

$dates = false;
if (isset($this->data['Element']['start_date']) && !empty($this->data['Element']['start_date'])) {
    $dates = true;
    ?>
    <script type="text/javascript" >
        $(function () {
            $("#el_dc_yes").trigger('change');
        });
    </script>
<?php } else { ?>
    <script type="text/javascript" >
        $(function () {
            $("#el_dc_no").trigger('change');

            $('#modal_task_assignment').on('hidden.bs.modal', function(e){
                $(this).removeData('bs.modal');
                $(this).find('.modal-content').html('');

            })

        });

    </script>
<?php } ?>


<?php include 'element_files/js_file.ctp'; ?>


<?php //include 'element_files/css_file.ctp'; ?>
<div class="row">

    <div class="col-xs-12">

            <section class="main-heading-wrap pb6">
			<?php include 'element_files/crumb.ctp'; ?>
            </section>


		<?php echo $this->element('../Projects/partials/project_header_image', array('p_id' => $prj[0]['Project']['id'])); ?>
        <div class="box-content update-prj-wsp">
            <?php echo $this->Session->flash(); ?>
            <div class="row">
                <div class="col-xs-12">
                    <div class="box noborder">

                        <!-- // PASSWORD DELETE -->
                        <div class="modal modal-danger fade" id="modal_delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content"></div>
                            </div>
                        </div>
						<?php include 'element_files/confirm_model.ctp'; ?>
                        <div class="box-header filter nopadding" >

							<?php
$message = null;
$messagepre = null;
$class_disabled = $class_d = $class_prevant = '';

if (isset($this->data['Element']['sign_off']) && $this->data['Element']['sign_off'] == 1) {

	$message = 'You cannot add to this Task because it has been signed off.';
	$class_disabled = 'element_warning disabled';
	$class_d = 'element_warning disable';

}

if (!isset($this->data['Element']['start_date']) && empty($this->data['Element']['start_date']) ) {

	$messagepre = 'You cannot add a Decision/Feedback/Vote because this Task has no schedule.';
	$class_disabled_pre = 'element_warning disabled';
	$class_prevant = 'element_warning disable';

}

							?>
							<div class="el_opsaa">
							<?php
							include 'element_files/element_options.ctp'; ?>
							</div>

                        </div>

						<div class="competencies-tab mt0 background-gray ">

							<div class="row">
						<div class="col-md-12">
    						<ul class="nav nav-tabs" id="task-header-tabs">
                                <li class="active">
                                     <a href="#task_information" data-toggle="tab" data-type="information" class="tab_wsp" >Information</a>
                                </li>
								<li class=" " >
                                     <a href="#task_team" data-toggle="tab" data-type="team" class="tab_wsp" >Team</a>
                                </li>
    							<li class="">
    							     <a href="#task_assets" data-toggle="tab" data-type="assets" class="tab_wsp" >Assets</a>
    							</li>
    						</ul>
						</div>
						</div>
						</div>

                        <div class="box-body bordert0">
							<div class="tab-content">
							<div id="task_information" class="tab-pane fade active in">
							<div class="task_information_inner">
								<div class="row">
                                    <?php
                                    $eda = $this->Permission->task_detals($this->data['Element']['id']);
                                    // pr($eda);
                                    $task_data = $eda[0]['e'];
                                    $creator_name = $eda[0][0]['creator'];
                                    $user_data = $eda[0]['ud'];
                                    $type_title = (isset($eda[0]['types']['type_title']) && !empty($eda[0]['types']['type_title'])) ? $eda[0]['types']['type_title'] : 'General';

                                    $profile_pic = $user_data['profile_pic'];
                                    if(!empty($profile_pic) && file_exists(USER_PIC_PATH.$profile_pic)){
                                        $profilesPic = SITEURL.USER_PIC_PATH.$profile_pic;
                                    } else {
                                        $profilesPic = SITEURL.'images/placeholders/user/user_1.png';
                                    }
                                    $current_org = $this->Permission->current_org();
                                    $html = '';
                                    if( $user_data['user_id'] != $this->Session->read('Auth.User.id') ) {
                                        $html = CHATHTML($user_data['user_id'], $project_id);
                                    }

                                    ?>
						<div class="col-sm-4">
							<label>Created By:</label> 
							<div class="style-people-com ">
								<span class="style-popple-icon-out">
									<div class="style-people-com">
										<a class="style-popple-icons " data-remote="<?php echo Router::Url(array('controller' => 'shares', 'action' => 'show_profile', $user_data['user_id'], 'admin' => FALSE), TRUE); ?>" id="task_profile" data-target="#popup_modal" data-toggle="modal">
											<span class="style-popple-icon-out">
												<span class="style-popple-icon" style="cursor: default;">
													<img src="<?php echo $profilesPic; ?>" class="pophoverssss" style="cursor:pointer;" align="left" width="36" height="36" data-content="<div><p><?php echo $creator_name; ?></p><p><?php echo $user_data['job_title']; ?></p><?php echo $html; ?></div>" >
                                                </span>
                                                <?php if($current_org['organization_id'] != $user_data['organization_id']){ ?>
                                                    <i class="communitygray18 community-g tipText"  style="cursor: pointer;" data-user="<?php echo $user_data['user_id']; ?>" title="" data-original-title="Not In Your Organization"></i>
                                                <?php } ?>
											</span>
										</a>
									</div>
								</span>
								<div class="style-people-info">
									<a href="#">
										<span class="style-people-name"><?php echo htmlentities($creator_name, ENT_QUOTES, "UTF-8"); ?></span>
										<span class="style-people-title" style="cursor: default;"><?php echo $user_data['job_title'] ; ?></span>
									</a>
								</div>
							</div>
						</div>
						<div class="col-sm-4">
							<label>Created On:</label>
							<div class="created-info"><?php echo date("d M, Y", strtotime($task_data['created'])); ?></div>
						</div>
						<div class="col-sm-4">
							<label>Type:</label>
							<div class="created-info"><?php echo htmlentities($type_title, ENT_QUOTES, "UTF-8"); ?></div>
						</div>
					</div>


                                <div class="row">
                                    <div class="col-lg-12">

									<div class="proj-task-description">
										<label> Description:</label>
										<p><?php echo nl2br(htmlentities($task_data['description'], ENT_QUOTES, "UTF-8")); ?></p>
										<label> Outcome:</label>
										<p><?php echo (isset($task_data['comments']) && !empty($task_data['comments'])) ? nl2br(htmlentities($task_data['comments'], ENT_QUOTES, "UTF-8")) : 'None'; ?></p>
									</div>
									</div>
                                </div>

							</div>
							<div class="prj-detail-task-header"> <h4>Task Details</h4></div>



                            </div>

							<div id="task_team" class="tab-pane fade white-bg">
                                <input type="hidden" name="paging_offset" id="paging_offset" value="0">
                                <input type="hidden" name="paging_total" id="paging_total" value="0">
                                <div class="project-summary-wrap">
                                    <div class="ps-col-header">
                                        <div class="ps-col tm-col-1">
                                            <span class="ps-h-one">Name <span class="total-data">(2)</span>
                                                <span class="sort_order active" data-by="first_name" data-order="desc" data-type="teams" title="Sort By First Name">
                                                    <i class="fa fa-sort" aria-hidden="true"></i>
                                                    <i class="fa fa-sort-asc" aria-hidden="true"></i>
                                                    <i class="fa fa-sort-desc" aria-hidden="true"></i>
                                                </span>
                                                <span  class="sort_order" data-by="last_name" data-order="desc" data-type="teams" title="Sort By Last Name">
                                                    <i class="fa fa-sort" aria-hidden="true"></i>
                                                    <i class="fa fa-sort-asc" aria-hidden="true"></i>
                                                    <i class="fa fa-sort-desc" aria-hidden="true"></i>
                                                </span>
                                            </span>
                                            <span class="ps-h-two sort_order"  data-by="job_title" data-order="desc" data-type="teams" title="Sort By Job Title">
                                                Title
                                                <i class="fa fa-sort" aria-hidden="true"></i>
                                                <i class="fa fa-sort-asc" aria-hidden="true"></i>
                                                <i class="fa fa-sort-desc" aria-hidden="true"></i>
                                            </span>
                                            <span class="ps-h-two sort_order"  data-by="role" data-order="desc" data-type="teams" title="Sort By Role">
                                                Role
                                                <i class="fa fa-sort" aria-hidden="true"></i>
                                                <i class="fa fa-sort-asc" aria-hidden="true"></i>
                                                <i class="fa fa-sort-desc" aria-hidden="true"></i>
                                            </span>
                                        </div>
                                        <div class="ps-col tm-col-2">
                                            Effort
											<span  class="sort_order" data-by="completed_hours" data-order="desc" data-type="teams" title="Sort By Completed Hours">
                                                <i class="fa fa-sort" aria-hidden="true"></i>
                                                <i class="fa fa-sort-asc" aria-hidden="true"></i>
                                                <i class="fa fa-sort-desc" aria-hidden="true"></i>
                                            </span>
                                            <span  class="sort_order" data-by="remaining_hours" data-order="desc" data-type="teams" title="Sort By Remaining Hours">
                                                <i class="fa fa-sort" aria-hidden="true"></i>
                                                <i class="fa fa-sort-asc" aria-hidden="true"></i>
                                                <i class="fa fa-sort-desc" aria-hidden="true"></i>
                                            </span>
                                            <span  class="sort_order" data-by="change_hours" data-order="desc" data-type="teams" title="Sort By Change">
                                                <i class="fa fa-sort" aria-hidden="true"></i>
                                                <i class="fa fa-sort-asc" aria-hidden="true"></i>
                                                <i class="fa fa-sort-desc" aria-hidden="true"></i>
                                            </span>
                                        </div>
                                        <div class="ps-col tm-col-3">
                                            Costs
                                            <span  class="sort_order" data-by="cost_estimate" data-order="desc" data-type="teams" title="Sort By Budget">
                                                <i class="fa fa-sort" aria-hidden="true"></i>
                                                <i class="fa fa-sort-asc" aria-hidden="true"></i>
                                                <i class="fa fa-sort-desc" aria-hidden="true"></i>
                                            </span>
                                            <span  class="sort_order" data-by="cost_spend" data-order="desc" data-type="teams" title="Sort By Actual">
                                                <i class="fa fa-sort" aria-hidden="true"></i>
                                                <i class="fa fa-sort-asc" aria-hidden="true"></i>
                                                <i class="fa fa-sort-desc" aria-hidden="true"></i>
                                            </span>
                                            <span  class="sort_order" data-by="cost_status" data-order="desc" data-type="teams" title="Sort By Status">
                                                <i class="fa fa-sort" aria-hidden="true"></i>
                                                <i class="fa fa-sort-asc" aria-hidden="true"></i>
                                                <i class="fa fa-sort-desc" aria-hidden="true"></i>
                                            </span>
                                        </div>
                                        <div class="ps-col tm-col-4">
                                            Risks
                                            <span  class="sort_order" data-by="high_pending_risks" data-order="desc" data-type="teams" title="Sort By High Pending Risks">
                                                <i class="fa fa-sort" aria-hidden="true"></i>
                                                <i class="fa fa-sort-asc" aria-hidden="true"></i>
                                                <i class="fa fa-sort-desc" aria-hidden="true"></i>
                                            </span>
                                            <span  class="sort_order" data-by="severe_pending_risks" data-order="desc" data-type="teams" title="Sort By Severe Pending Risks">
                                                <i class="fa fa-sort" aria-hidden="true"></i>
                                                <i class="fa fa-sort-asc" aria-hidden="true"></i>
                                                <i class="fa fa-sort-desc" aria-hidden="true"></i>
                                            </span>
                                            <span  class="sort_order" data-by="total_risks" data-order="desc" data-type="teams" title="Sort By Total Risks Count">
                                                <i class="fa fa-sort" aria-hidden="true"></i>
                                                <i class="fa fa-sort-asc" aria-hidden="true"></i>
                                                <i class="fa fa-sort-desc" aria-hidden="true"></i>
                                            </span>
                                        </div>
                                        <div class="ps-col tm-col-5">
                                            Competencies
                                            <span  class="sort_order" data-by="user_skills" data-order="desc" data-type="teams" title="Sort By Skills Count">
                                                <i class="fa fa-sort" aria-hidden="true"></i>
                                                <i class="fa fa-sort-asc" aria-hidden="true"></i>
                                                <i class="fa fa-sort-desc" aria-hidden="true"></i>
                                            </span>
                                            <span  class="sort_order" data-by="user_subjects" data-order="desc" data-type="teams" title="Sort By Subjects Count">
                                                <i class="fa fa-sort" aria-hidden="true"></i>
                                                <i class="fa fa-sort-asc" aria-hidden="true"></i>
                                                <i class="fa fa-sort-desc" aria-hidden="true"></i>
                                            </span>
                                            <span  class="sort_order" data-by="user_domains" data-order="desc" data-type="teams" title="Sort By Domains Count">
                                                <i class="fa fa-sort" aria-hidden="true"></i>
                                                <i class="fa fa-sort-asc" aria-hidden="true"></i>
                                                <i class="fa fa-sort-desc" aria-hidden="true"></i>
                                            </span>
                                        </div>
                                        <div class="ps-col tm-col-6">
                                            Last Activity
                                            <span  class="sort_order" data-by="message" data-order="desc" data-type="teams" title="Sort By Last Activity">
                                                <i class="fa fa-sort" aria-hidden="true"></i>
                                                <i class="fa fa-sort-asc" aria-hidden="true"></i>
                                                <i class="fa fa-sort-desc" aria-hidden="true"></i>
                                            </span>
                                            <span  class="sort_order" data-by="updated" data-order="desc" data-type="teams" title="Sort By Last Activity Date">
                                                <i class="fa fa-sort" aria-hidden="true"></i>
                                                <i class="fa fa-sort-asc" aria-hidden="true"></i>
                                                <i class="fa fa-sort-desc" aria-hidden="true"></i>
                                            </span>
                                        </div>
                                        <div class="ps-col tm-col-7">
                                            Actions
                                        </div>
                                    </div>
                                    <div class="project-summary-data team-data list-wrapper">
                                        <?php //echo $this->element('../Entities/team/team'); ?>
                                    </div>
                                </div>
							</div>

                            <div id="task_assets" class="tab-pane fade">
                                <div id="workspace">

                                <div class="row">
                                    <div class="col-lg-12">
                                        <button class="btn cd-toggle cd-tabs-button" type="button" data-toggle="collapse" data-target="#element_tabs"> <span class="fa fa-bars"></span> </button>

                                        <div class="cd-tabs">

                                            <nav>
                                <?php include 'element_files/element_type.ctp'; ?>
                                            </nav>



                                            <ul class="cd-tabs-content clearfix ">

                                                <li data-content="tasks" >
												<div class="table_wrapper">
                                <?php


                                //include 'element_files/task.ctp'; ?>
                                <div class="table_wrapperx">
                                <?php include 'activity/task_activity.ctp'; ?>
                                </div>
                                                </li>

                                                <li class="ipad-scroll-assets" data-content="links" >

                                <?php include 'element_files/links.ctp'; ?>

                                                </li>

                                                <!-- End Links Tab	-->

                                                <li class="ipad-scroll-assets" data-content="notes" >
                                    <?php include 'element_files/notes.ctp'; ?>
                                                </li>


                                                <li class="ipad-scroll-assets" data-content="documents"  >

                                            <?php include 'element_files/documents.ctp'; ?>
                                                </li>

                                                <!-- End documents Tab	-->

                                                <li class="ipad-scroll-assets" data-content="mind_maps"  >
                                    <?php include 'element_files/mindmaps.ctp'; ?>
                                                </li>

                                                <!-- End MindMap Tab	-->

                                <?php
                                	$p_permission = $this->Common->project_permission_details_ele($project_id, $this->Session->read('Auth.User.id'));
                                	$project_level = 0;

                                	if( isset($p_permission) && !empty($p_permission) && ( $p_permission[0]['user_permissions']['role'] == 'Creator' || $p_permission[0]['user_permissions']['role'] == 'Owner' || $p_permission[0]['user_permissions']['role'] == 'Group Owner' ) ){
                                		$project_level = 1;
                                	}

                                	$elementPermission = $this->Common->element_manage_permission($this->data['Element']['id'], $project_id, $this->Session->read('Auth.User.id'));

                                		$is_editaa = 0;
                                		$is_add_shares = 0;
                                		$is_edit_shares = 0;
                                		$is_read_shares = 0;
                                		$is_delete_shares = 0;
                                		if( isset($elementPermission) && !empty($elementPermission[0]['user_permissions']['permit_edit']) ){
                                			$is_editaa = $elementPermission[0]['user_permissions']['permit_edit'];
                                		}
                                		if( isset($elementPermission) && !empty($elementPermission[0]['user_permissions']['permit_read']) ){
                                			$is_edit_shares = $elementPermission[0]['user_permissions']['permit_edit'];
                                			$is_edit_share = $elementPermission[0]['user_permissions']['permit_edit'];
                                		}
                                		if( isset($elementPermission) && !empty($elementPermission[0]['user_permissions']['permit_read']) ){
                                			$is_read_shares = $elementPermission[0]['user_permissions']['permit_read'];
                                		}
                                		if( isset($elementPermission) && !empty($elementPermission[0]['user_permissions']['permit_delete']) ){
                                			$is_delete_shares = $elementPermission[0]['user_permissions']['permit_delete'];
                                		}
                                		if( isset($elementPermission) && !empty($elementPermission[0]['user_permissions']['permit_add']) ){
                                			$is_add_shares = $elementPermission[0]['user_permissions']['permit_add'];
                                		}


                                 if ((isset($is_owner) && !empty($is_owner)) || (isset($project_level) && $project_level == 1) || (isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level'] == 1) || (isset($is_editaa) && $is_editaa > 0)) {  ?>

                                                    <?php
                                                    $message = null;
                                                    //echo $mindate_elm.$messageVar.$cur_date; die;

                                                    $class_disabled = $class_d = $overdue = $notstarted = '';

                                                    //if(isset($this->data['Element']['date_constraints']) && $this->data['Element']['date_constraints'] ==1){

                                                    if (isset($prj[0]['Workspace']['sign_off']) && $prj[0]['Workspace']['sign_off'] == 1) {
                                                        $message = 'Workspace has been Sign off.';
                                                        $class_disabled = 'element_warning disabled';
                                                        $class_d = 'element_warning disable';
                                                    } else if (isset($this->data['Element']['sign_off']) && $this->data['Element']['sign_off'] == 1) {
                                                        // $message = 'Task has been Sign off.';
                                                        $message = 'You cannot add to this Task because it has been signed off.';
                                                        $class_disabled = 'element_warning disabled';
                                                        $class_d = 'element_warning disable';
                                                    } else if (!empty($mindate_elm) && strtotime($mindate_elm) > strtotime($cur_date) && strtotime($maxdate_elm) > strtotime($cur_date)) {

                                                        $notstarted = $message = $messageVar . ' start date not reached yet.';
                                                        $class_disabled = 'element_warning disabled';
                                                        $class_d = 'element_warning disable';
                                                    } else if (!empty($maxdate_elm) && strtotime($maxdate_elm) < strtotime($cur_date)) {

                                                        $message = $messageVar . ' date has expired.';
                                                        $class_disabled = 'element_warning disabled';
                                                        $class_d = 'element_warning disable';
														$overdue = 'overdue_task';

                                                    } else if (isset($mindate_elm) && empty($mindate_elm)) {
                                                        $message = 'You are not allowed to add element, Please schedule workspace first.';
                                                        $class_disabled = 'element_warning disabled';
                                                        $class_d = 'element_warning disable';
                                                    } else if (empty($mindate_elm) && strtotime($mindate_elm) > strtotime($cur_date) && strtotime($maxdate_elm) > strtotime($cur_date)) {
                                                        $message = 'You are not allowed to add workspace because workspace hasn\'t started yet.';
                                                        $class_disabled = 'element_warning disabled';
                                                        $class_d = 'element_warning disable';
                                                    }

                                                    //}

												//	echo $message;
                                                    ?>
                                                    <li class="ipad-scroll-assets" data-content="decisions" >

                                                    <?php include 'element_files/decisions.ctp'; ?>
                                                    </li>

                                                    <!-- End decision Tab	-->

                                                    <li class="ipad-scroll-assets" data-content="feedbacks" >
													<?php include 'element_files/feedbacks.ctp'; ?>
                                                    </li>

                                                    <!-- End feedback Tab	-->


                                                    <li class="ipad-scroll-assets" data-content="votes">
													<?php include 'element_files/votes.ctp'; ?>
                                                    </li>
                                    <?php } ?>
                                                <!-- End MindMap Tab	-->

                                                <!-- End votes Tab	-->

                                                <!--  <li data-content="services" >
                                                      <div>Services</div>
                                                  </li>-->

                                            </ul>
                                        </div>
                                        <!-- End conversations Tab	-->
                                    </div>
                                </div>
                            </div>
							</div>





							</div>
                        </div>

                    </div><!-- /.box-body -->
                </div><!-- /.box -->
            </div>
        </div>
    </div>
</div>

<script type="text/javascript" >


		function isNumberKey(evt){
			var charCode = (evt.which) ? evt.which : event.keyCode
			if (charCode > 31 && (charCode < 48 || charCode > 57)) {
				return false;
			}
			return true;
		}
     $(function(){

		 //$('#workspace ul li a').trigger('click');
		 if(location.hash =="#tasks"){
			 $('.hide_ele').css('display', 'inline-block');
			 $('.hide_ele_else').hide();
		 }

		 if(location.hash !="#tasks"){

			 $('.hide_ele').hide();
			 $('.hide_ele_else').show();
		 }



		 $('#workspace ul#element_tabs li a.topic').click(function(){

			// $('.hide_ele').show();
			 $('.hide_ele').css('display', 'inline-block');
			 $('.hide_ele_else').hide();
		 })

		 $('#workspace ul#element_tabs li a:not(.topic)').click(function(){
			 $('.hide_ele').hide();
			 $('.hide_ele_else').show();
		 })


        var lastX = 0;
        var currentX = 0;
        var page = 1;
        $('.scrolling-history').scroll(function () {
            //console.log($js_config.element_id+ ' == '+$js_config.element_tasks);
            currentX = $(this).scrollTop();

            if($(this).scrollTop() + $(this).innerHeight() >= ($(this)[0].scrollHeight - 100)) {
                if ((currentX - lastX) > (20 * page)) {
                    lastX = currentX;
                    page++;
                    $.post( $js_config.base_url+'entities/more_history/'+$js_config.element_id+'/element_tasks'+'/page:' + page, {element_id: $js_config.element_id,element_tasks: 'element_tasks'}, function(data) {
                            $('.hmgo').append(data);
                    });
                }
            }
        });
    })
    function number_of_options(type_id, val) {
        html = '';
        for (i = 0; i < val; i++) {
            html += '<div class="col-sm-4  col-md-3 col-lg-2 form-group"><input type="text" id="VoteQuestion' + i + '" class="form-control task_vote_option" name="data[VoteQuestionOption][option][' + type_id + '][' + i + ']" /><span class="error-messagess text-danger"></span></div>';
        }
        $('#options').show();
        $('#options_questions').html(html);
    }


    $(function () {
        // PASSWORD DELETE
        $.current_delete = {};
        $('body').delegate('.delete-an-item', 'click', function(event) {
            event.preventDefault();
            $.current_delete = $(this);
        });

        $('#modal_delete').on('hidden.bs.modal', function () {
            $(this).removeData('bs.modal');
            $(this).find('.modal-content').html('');
            $.current_delete = {};
        });

        //$('.element-sign-off-restrict').on('click', function (e) {
		$('body').on('click','.element-sign-off-restrict', function(e) {
            e.preventDefault();

            var $this = $(this),
                    $cbox = $('#confirm-box'),
                    $yes = $cbox.find('#sign_off_yes'),
                    $no = $cbox.find('#sign_off_no');

            var $span_text = $yes.find('span.text'),
                    $div_progress = $yes.find('div.btn_progressbar');

            // set message
            var body_text = $('#dsptest').text();

            $yes.find('span.text').text('Close')
            $no.hide();
            $('#confirm-box #modal_body').text(body_text)
			$('#confirm-box #modal_header').addClass('bg-red');
			$('#confirm-box').removeClass('modal-success');

			BootstrapDialog.show({
                    title: '<h3 class="h3_style">Sign Off</h3>',
                    message: body_text,
                    type: BootstrapDialog.TYPE_DANGER,
                    draggable: true,
                    buttons: [

                        {
                            label: ' Close',
                            //icon: '',
                            cssClass: 'btn-success',
                            action: function (dialogRef) {
                                dialogRef.close();
                            }

                        }
                    ]
                })

			//$('#confirm-box').find('#modal_header').css('background-color','#d9534f');
			$('#confirm-box').find('#modal_header').html('<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button><h3  id="myModalLabel" class="modal-title">Sign Off</h3>');

            //$('#confirm-box #modal_header').addClass('bg-red').html('<i class="fa fa-exclamation-triangle"></i> Warning')

			/* $('#confirm-box').modal({keyboard: true}).one('click', '#sign_off_yes', function () {
				$('#confirm-box').modal('hide')
			}); */


        });

        $('.element-sign-off-restrict_Pro').on('click', function (e) {
            e.preventDefault();
            var $this = $(this),
                    $cbox = $('#confirm-box'),
                    $yes = $cbox.find('#sign_off_yes'),
                    $no = $cbox.find('#sign_off_no');

            var $span_text = $yes.find('span.text'),
                    $div_progress = $yes.find('div.btn_progressbar');

            // set message
            var body_text = $this.attr('data-msg');

            $yes.find('span.text').text('Close')
            $no.hide();
            $('#confirm-box #modal_body').text(body_text)
            $('#confirm-box #modal_header').addClass('bg-red').html('Reopen')

            $('#confirm-box').modal({keyboard: true})
                    .one('click', '#sign_off_yes', function () {
                        $('#confirm-box').modal('hide')
                    });
        });

        $.u_list = $("ul#decisionList"),
                $element_decision_title = $.u_list.find('input[name="data[ElementDecision][title]"]');

        $.f_list = $("ul#feedbackList");

        /* var elm = [$('#txa_title'),  $("#txa_comments")];
        wysihtml5_editor.set_elements(elm)
        $.wysihtml5_config = $.get_wysihtml5_config()

        var title_config = $.extend({}, {'remove_underline': true}, $.wysihtml5_config)
        var description_config = $.extend({}, {}, $.wysihtml5_config)

        $.extend(description_config, {"emphasis": true, //Italics, bold, etc. Default true
            "lists": false, //(Un)ordered lists, e.g. Bullets, Numbers. Default true
            "html": false, //Button which allows you to edit the generated HTML. Default false
            "link": false, //Button to insert a link. Default true
            "image": false, //Button to insert an image. Default true,
            "color": false, //Button to change color of font
            "size": 'sm', //Button size like sm, xs etc.
            "placeholderText": 'My Placeholder Text', 'limit': 750,
            'parserRules': {'tags': {'br': {'remove': 0}, 'ul': {'remove': 0}, 'li': {'remove': 0}, 'b': {'remove': 0}, 'u': {'remove': 0}, 'i': {'remove': 0}, 'blockquote': {'remove': 0}, 'ol': {'remove': 0}}}

        })


        var comments_config = $.extend($.extend({}, {}, $.wysihtml5_config), {"font-styles": false, //Font styling, e.g. h1, h2, etc. Default true
            "emphasis": true, //Italics, bold, etc. Default true
            "lists": false, //(Un)ordered lists, e.g. Bullets, Numbers. Default true
            "html": false, //Button which allows you to edit the generated HTML. Default false
            "link": false, //Button to insert a link. Default true
            "image": false, //Button to insert an image. Default true,
            "color": false, //Button to change color of font
            "size": 'sm', //Button size like sm, xs etc.
            "placeholderText": 'My Placeholder Text', 'limit': 2000,
            'parserRules': {'tags': {'br': {'remove': 0}, 'ul': {'remove': 0}, 'li': {'remove': 0}, 'b': {'remove': 0}, 'u': {'remove': 0}, 'i': {'remove': 0}, 'blockquote': {'remove': 0}, 'ol': {'remove': 0}}}

        })
        $("#txa_title").wysihtml5(title_config); */
        //$("#txa_description").wysihtml5(description_config);

		/* var characters = 750;
		$("#txa_description").keyup(function(){
			var $ts = $(this)
			if($(this).val().length > characters){
				$(this).val($(this).val().substr(0, characters));
			}
			var remaining = characters -  $(this).val().length;
			$(this).next().html("Char 750 , <strong>" +$(this).val().length+ "</strong> characters entered.");
			if(remaining <= 10)
			{
				$(this).next().css("color","#dd4b39");
				$(this).next().css("font-size","11px");
			}
			else
			{
				$(this).next().css("color","#dd4b39");
				$(this).next().css("font-size","11px");
			}
		});

		var characters1 = 2000;
		$("#txa_comments").keyup(function(){
			var $ts = $(this)
			if($(this).val().length > characters1){
				$(this).val($(this).val().substr(0, characters1));
			}
			var remaining = characters1 -  $(this).val().length;
			$(this).next().html("Char 2000 , <strong>" +$(this).val().length+ "</strong> characters entered.");
			if(remaining <= 10)
			{
				$(this).next().css("color","#dd4b39");
				$(this).next().css("font-size","11px");
			}
			else
			{
				$(this).next().css("color","#dd4b39");
				$(this).next().css("font-size","11px");
			}
		}); */


        //$("#txa_comments").wysihtml5(comments_config);
        /* $(".vote_desc").wysihtml5(comments_config);
        $(".feedback_for").wysihtml5(comments_config);
        $(".feedback_res").wysihtml5(comments_config);
        $("#feedbackfor_desc").wysihtml5(comments_config); */

		/*$('body').delegate('#txa_title', 'keyup focus', function(event){
            var characters = 50;
            event.preventDefault();
            var $error_el = $(this).parent().find('.error');
            if (typeof $.input_char_count !== 'undefined' && $.isFunction($.input_char_count)) {
                $.input_char_count(this, characters, $error_el);
            }
        })

		$('body').delegate('#txa_description', 'keyup focus', function(event){
            var characters = 750;
            event.preventDefault();
            var $error_el = $(this).parent().find('.error');
            if (typeof $.input_char_count !== 'undefined' && $.isFunction($.input_char_count)) {
                $.input_char_count(this, characters, $error_el);
            }
        })

		$('body').delegate('#txa_comments', 'keyup focus', function(event){
            var characters = 2000;
            event.preventDefault();
            var $error_el = $(this).parent().find('.error');
            if (typeof $.input_char_count !== 'undefined' && $.isFunction($.input_char_count)) {
                $.input_char_count(this, characters, $error_el);
            }
        })*/



        /********* Votes *********/
        $('#votesystem').on('change', function () {
            $('#options').hide();
            $('#number_of_options').hide();
            $('#options_count').html('');
            type_id = $(this).val();
            if (type_id == '1') {
			$('#distributed_options').hide();
                html = '';
                html += '<div class=" col-sm-2 form-group"><input type="text" id="Voteoption' + type_id + '-1" class="form-control task_vote_option" readonly="true" value="Yes" name="data[VoteQuestionOption][option][' + type_id + '][0]"> <span style="" class="error-messagess_dep text-danger"></span> </div><div class=" col-sm-2 form-group"><input type="text" id="VoteQuestion' + type_id + '-2" class="form-control task_vote_option" readonly="true" value="No" name="data[VoteQuestionOption][option][' + type_id + '][1]"> <span style="" class="error-messagess_dep text-danger"></span> </div>';
                $('#options').show();
                $('#options_questions').html(html);
            } else if (type_id == '2') {
			$('#distributed_options').hide();
                html = '';
                html += '<div class=" col-sm-2 form-group"><input type="text" pattern="[0-9]{6}" min="0" minlength="1" maxlength="6" onkeypress="return isNumberKey(event)" id="Voteoption' + type_id + '" class="form-control task_vote_options task_vote_option_range_min" value="0" name="data[VoteQuestionOption][option][' + type_id + '][0]"><span style="" class="error-messagess text-danger"></div>';
                html += '<div class=" col-sm-1 form-group text-center"> - </div>';
                html += '<div class=" col-sm-2 form-group"><input type="text" pattern="[0-9]{6}" min="0" minlength="1" maxlength="6" onkeypress="return isNumberKey(event)" id="VoteQuestion' + type_id + '" class="form-control task_vote_options task_vote_option_range_max" value="9" name="data[VoteQuestionOption][option][' + type_id + '][1]"><span style="" class="error-messagess text-danger"></div>';
                $('#options').show();
                $('#options_questions').html(html);
            } else if (type_id == '3') {
			$('#distributed_options').hide();
                html = '';
                html += '<div class=" col-sm-2 form-group"><input type="text" id="Voteoption' + type_id + '" class="form-control task_vote_option" value="Yes" name="data[VoteQuestionOption][option][' + type_id + '][0]"><span style="" class="error-messagess text-danger"></div><div class=" col-sm-2 form-group"><input type="text" id="VoteQuestion' + type_id + '" class="form-control task_vote_option" value="Maybe" name="data[VoteQuestionOption][option][' + type_id + '][1]"><span style="" class="error-messagess text-danger"></div><div class=" col-sm-2 form-group"><input type="text" id="VoteQuestion' + type_id + '" class="form-control task_vote_option" value="Don’t know" name="data[VoteQuestionOption][option][' + type_id + '][2]"><span style="" class="error-messagess text-danger"></div><div class=" col-sm-2 form-group"><input type="text" id="VoteQuestion' + type_id + '" class="form-control task_vote_option" value="No" name="data[VoteQuestionOption][option][' + type_id + '][3]"><span style="" class="error-messagess text-danger"></div>';
                $('#options').show();
                $('#options_questions').html(html);
            } else if (type_id == '4') {
                html = '';
                $('#distributed_options').hide();
                html = '<select id="question_option_count" required onchange="number_of_options(' + type_id + ', this.value)"  name="data[VoteQuestionOption][option_count]" class="form-control">';
                html += '<option selected value=""> - Select - </option>';
                for (i = 2; i <= 10; i++) {
                    html += '<option value="' + i + '">' + i + '</option>';
                }
                html += '</select> <span class="error-message text-danger"> </span>';
               $('#distributed_options').hide();
                $('#number_of_options').show();
                $('#options_count').html(html);
                $('#options').hide();
                $('#options_questions').html('');
            } else if (type_id == '5' ) {
              $('#distributed_options').hide();
                html = '<select id="question_option_count" required onchange="number_of_options(' + type_id + ', this.value)" name="data[VoteQuestionOption][option_count]" class="form-control ">';
                html += '<option selected value=""> - Select - </option>';
                for (i = 2; i <= 10; i++) {
                    html += '<option value="' + i + '">' + i + '</option>';
                }
                html += '</select> <span class="error-message text-danger"> </span>';

                $('#number_of_options').show();
                $('#options_count').html(html);
                $('#options').hide();
                $('#options_questions').html('');
            } else if ( type_id == '6') {

                html = '<select id="question_option_count" required onchange="number_of_options(' + type_id + ', this.value)" name="data[VoteQuestionOption][option_count]" class="form-control distributed_typec  distributed_typec_new">';
                html += '<option selected value=""> - Select - </option>';
                for (i = 2; i <= 10; i++) {
                    html += '<option value="' + i + '">' + i + '</option>';
                }
                html += '</select> <span class="error-message text-danger"> </span>';

				var tota = '';
				$("body").delegate(".distributed_typec_new", "change", function (event) {

					tota = $(this).val();

					html = '<select id="dist_count" required  name="data[VoteQuestion][distributed_count]" class="form-control distributed_typec">';
					html += '<option selected value=""> - Select - </option>';
					for (i = tota; i <= 10; i++) {
						html += '<option value="' + i + '">' + i + '</option>';
					}
                html += '</select> <span class="error-message text-danger"> </span>';


					$('#distributed_count').html(html);
					$('#distributed_options').show();
				})

                $('#number_of_options').show();
                $('#options_count').html(html);
                $('#options').hide();
                $('#options_questions').html('');
            } else {
                $('#options').hide();
				$('#distributed_options').hide();
                $('#options_questions').html('');
            }
        });


        $('#prev_step2').on('click', function () {
            $('#step1').show();
            $('#step2').hide();
			if($('#question_id').val() > 0){
				// $('#cancelvotefirststep').hide();
			}else{
				// $('#cancelvotefirststep').show();
			}
        });

        $('#prev_step3').on('click', function () {
            $('#step2').show();
            $('#step3').hide();
			if($('#question_id').val() > 0){
				// $('#cancelvote').hide();
			}else{
				// $('#cancelvote').show();
			}

        });


        $("body").delegate(".save_vote", "click", function (event) {
            $('#multi_participant_vote_users').multiselect('enable')
            event.preventDefault();
            var $t = $(this),
                    $row = $t.parents(".row:first"),
                    $formDiv = $t.parent().parent().parent(),
                    $form = $formDiv.find('.formAddElementVote'),
                    action = $form.attr("action"),
                    data_string = $form.serialize(),
                    $addForm = $formDiv.parent(),
                    perform = 'update';
            var $txtdesc = $form.find('#vote_desc'),
                    $editor = $txtdesc.data('editor'),
                    formData = $form.serializeArray();
            if ($editor != undefined) {
                desc = $editor.Editor('getText'),
                        formData.push({'name': 'data[Vote][reason]', 'value': desc});
            }
            if ($addForm.length) {
                perform = 'create'
            }

            /* if (!$formDiv.parent().is($(".vote_form"))) {
             if ($formDiv.is(":visible")) {
             // $formDiv.addClass("bg-warning disabled");
             } else {
             $formDiv.removeClass("bg-warning").removeClass("disabled");
             }
             } */


            $.ajax({
                type: 'POST',
                data: $.param(formData),
                url: action,
                global: false,
                dataType: 'JSON',
                beforeSend: function () {
                    // $form.find('a.submit').addClass('disabled')
                },
                complete: function () {
                    setTimeout(function () {
                        // $form.find('a.submit').removeClass('disabled')
                    }, 300)
                },
                success: function (response, statusText, jxhr) {

                    $form.find('span').html("");
                    if (response.success) {
                        var emp = 0;
                        /* if (!$.isEmptyObject(response.content) && emp != '0') {
                         var data = response.content.Vote;
                         var sid = response.session_id;
                         if (response.action !== '' && response.action === 'update') {
                         var $currentRow = $row;
                         $currentRow.find('.col-sm-5').animate({
                         color: '#FFFFFF'
                         }, 1000, function () {
                         $(this).removeAttr('style')
                         $currentRow.find('.col-sm-5:eq(0)').html(data.title)
                         $currentRow.find('.col-sm-5:eq(1)').html(data.reason)//$form.find('a.submit').addClass('disabled')
                         $currentRow.find('.list-form').slideUp();
                         })

                         }else if (perform === 'create') {

                         $form.get(0).reset()

                         $editor.Editor('setText', '')
                         var l = window.location.href;
                         //console.log(data);
                         //SERVER IP MINDMAP

                         $editor.Editor('setText', '')


                         var rowHtmls = $.create_vote_row(data),
                         $cloneRow = $('<div />')
                         .addClass('row')
                         .hide();

                         $cloneRow.html(rowHtmls)

                         $tableRows = $("#vote_table").find('.table-rows')
                         $cloneRow.appendTo($tableRows)
                         $cloneRow.slideDown(700, function () {
                         $(this).removeAttr('style')
                         })

                         // Add wysihtml5 editor with dynamically cteated textarea
                         var $edtor = $cloneRow.find('textarea[id^=vote_desc]');


                         $cloneRow.appendTo($tableRows)
                         $cloneRow.slideDown(700, function () {
                         $(this).removeAttr('style')
                         })
                         }
                         } */
                        var step = response.step;
						$('#VoteUpdateElementForm').show();
                        if (step == '1') {
                            vote_id = response.content.Vote.id;
                            $('.vote_id').val(vote_id);
                            $('#step1').hide();
                            $('#step2').show();



                            $(window).on('beforeunload', function () {
                                return 'Are you want to leave this ? Please make sure that you have completed all steps before leaving else data may not be saved.';
                            });



                        } else if (step == '2') {
                            question_id = response.content.VoteQuestion.id;
                            $('.question_id').val(question_id);
                            $('#step2').hide();
                            $('#step3').show();
                            $(window).unbind('beforeunload');
                        } else if (step == '3') {
                            //location.reload();
                            window.location.href = "<?php echo SITEURL; ?>entities/update_element/<?php echo $this->params->params['pass'][0]; ?>#votes";
                        }
                    }
                    else {
                        if (!$.isEmptyObject(response.content)) {
                            $.each(response.content,
                                    function (ele, msg) {
                                        //console.log(ele+'---'+msg);
                                        if ($('[name="data[Vote][' + ele + ']"]').length > 0) {
                                            var $inpEle = $('[name="data[Vote][' + ele + ']"]');
                                            if (ele == 'title') {
                                                $inpEle.parent().find('span.error-message').html(msg);
                                            } else {
                                                $inpEle.parent().find('span.error-message').html(msg);
                                            }
                                        }

                                        if ($('[name="data[VoteQuestion][' + ele + ']"]').length > 0) {
                                            //console.log(ele);
                                            var $inpEle = $('[name="data[VoteQuestion][' + ele + ']"]');
                                            if (ele == 'title') {
                                                $inpEle.parent().find('span.error-message').html(msg);
                                            } else {
                                                $inpEle.parent().find('span.error-message').html(msg);
                                            }
                                        }


                                        if ($('[name="data[VoteUser][' + ele + ']"]').length > 0) {
                                            //console.log(ele);
                                            var $inpEle = $('[name="data[VoteUser][' + ele + ']"]');
                                            if (ele == 'title') {
                                                $inpEle.parent().find('span.error-message').html(msg);
                                            } else {
                                                $inpEle.parent().find('span.error-message').html(msg);
                                            }
                                        }

                                        if ($('[name="data[VoteQuestionOption][' + ele + ']"]').length > 0) {
                                            //console.log(ele);
                                            var $inpEle = $('[name="data[VoteQuestionOption][' + ele + ']"]');
                                            if (ele == 'title') {
                                                $inpEle.parent().find('span.error-message').html(msg);
                                            } else {
                                                $inpEle.parent().find('span.error-message').html(msg);
                                            }
                                        }

                                        //
                                        // //console.log($('input[name^="data[VoteQuestionOption][option]["').text);

                                        $('input[name^="data[VoteQuestionOption][option]["]').each(function () {
                                            // //console.log($(this).val())
                                            //   //console.log(ele);
                                            if ($.trim($(this).val()).length == '') {
                                                $(this).next('.error-messagess:first').html("This is required.");
                                            } else if (ele == 'option') {
                                                $('.task_vote_option_range_max').next('.error-messagess:first').html("Please enter valid Range.");
                                            }
                                            else {
                                                //$(this).next('.error-messagess:first').html("");
                                            }

                                        })

                                    })
                        }
                    }
                }
            });
        });


        /********* Feedbacks *********/
        $("body").delegate(".save_feedback", "click", function (event) {

            event.preventDefault();
            var $t = $(this),
                    $row = $t.parents(".row:first"),
                    $formDiv = $t.parent().parent().parent(),
                    $form = $formDiv.find('.formAddElementFeedback'),
                    action = $form.attr("action"),
                    data_string = $form.serialize(),
                    $addForm = $formDiv.parent(),
                    perform = 'update';
            var $txtdesc = $form.find('#feedback_desc'),
                    $editor = $txtdesc.data('editor'),
                    formData = $form.serializeArray();

            if ($editor != undefined) {
                desc = $editor.Editor('getText'),
                        formData.push({'name': 'data[Feedback][reason]', 'value': desc});
            }
            if ($addForm.length) {
                perform = 'create'
            }
            $.ajax({
                type: 'POST',
                data: $.param(formData),
                url: action,
                global: false,
                dataType: 'JSON',
                beforeSend: function () {
                    $form.find('a.submit').addClass('disabled')
                },
                complete: function () {
                    setTimeout(function () {
                        $form.find('a.submit').removeClass('disabled')
                    }, 300)
                },
                success: function (response, statusText, jxhr) {
                    $('#multiselect_groups').multiselect('rebuild');
                    $('#multiselect_groups').multiselect('enable');

                    $form.find('span').html("");
                    if (response.success) {
                        var emp = 0;
                        var step = response.step;
                        if (step == '1') {
                            feedback_id = response.content.Feedback.id;
                            $('#feedback_id').val(feedback_id);
                            $('#FeedbackAttachment').val(feedback_id);
                            $('#feedbackstep1').hide();
                            $('#feedbackstep2').show();
                            $('#feedbackstep2 #formAddFeedbackDoc').show();

                            $(window).on('beforeunload', function () {

                                return 'Are you want to leave this ? Please make sure that you have completed all steps before leaving else data may not be saved.';
                            });

							/*
							$(window).unload(function(){

								$.ajax({
										type: 'POST',
										data: 'feedback_id=' + $('#feedbackstep2 #feedback_id').val() + '&element_id=' + $js_config.currentElementId,
										url: '<?php echo Router::url("/") ?>entities/cancel_feedback',
										global: false,
										success: function (response) {
												//location.reload();
										}
								})

							}); */

                        } else if (step == '2') {
                            $(window).unbind('beforeunload');
                            window.location.href = "<?php echo SITEURL; ?>entities/update_element/<?php echo $this->params->params['pass'][0]; ?>#votes";
                        }
                    }
                    else {
                        if (!$.isEmptyObject(response.content)) {
                            $.each(response.content,
                                    function (ele, msg) {
                                        //console.log(ele + '---' + msg);
                                        if ($('[name="data[Feedback][' + ele + ']"]').length > 0) {
                                            var $inpEle = $('[name="data[Feedback][' + ele + ']"]');
                                            if (ele == 'title') {
                                                $inpEle.parent().find('span.error-message').html(msg);
                                            } else {
                                                $inpEle.parent().find('span.error-message').html(msg);
                                            }
                                        }
									})
                        }
                    }
                }
            });
        });

        $(document).on('click', '#prev_2_feedback', function () {
            $('#feedbackstep1').show();
            $('#feedbackstep2').hide();
			// $('#cancel_feedback').hide();
			var nfeedbackid = $("#feedback_id").val();
			$('#newFeedbackid').val(nfeedbackid);

        });

        $("body").delegate(".update_vote_btn", "click", function (event) {
            event.preventDefault();
            var $t = $(this),
                    $row = $t.parents(".row:first"),
                    $formDiv = $t.parent().parent().parent(),
                    $form = $formDiv.find('.formupdateVote'),
                    action = $form.attr("action"),
                    data_string = $form.serialize(),
                    $addForm = $formDiv.parent(),
                    perform = 'update';
            id = $(this).attr('id');
            var $txtdesc = $form.find('#vote_desc_' + id),
                    $editor = $txtdesc.data('editor'),
                    formData = $form.serializeArray();
            if ($editor != undefined) {
                desc = $editor.Editor('getText'),
                        formData.push({'name': 'data[Vote][reason]', 'value': desc});
            }
            if ($addForm.length) {
                perform = 'create'
            }

            if (!$formDiv.parent().is($(".vote_form"))) {
                if ($formDiv.is(":visible")) {
                    $formDiv.addClass("bg-warning");
                } else {
                    $formDiv.removeClass("bg-warning");
                }
            }

            var values = {};
            $.each(formData, function (i, field) {
                values[field.name] = field.value;
            });

            //Value Retrieval Function
            var getValue = function (valueName) {
                return values[valueName];
            };

            //Retrieve the Values
            var vote_start_d = $("#start_date_" + id).val();
            var vote_end_d = $("#end_date_" + id).val();

            $.ajax({
                type: 'POST',
                data: $.param(formData),
                url: action,
                global: false,
                dataType: 'JSON',
                beforeSend: function () {
                    $form.find('a.submit').addClass('disabled')
                },
                complete: function () {
                    setTimeout(function () {
                        $form.find('a.submit').removeClass('disabled')
                    }, 300)
                },
                success: function (response, statusText, jxhr) {



                    $form.find('span').html("");
                    if (response.success) {

                        $('#title' + id).text(getValue("data[Vote][title]"));
                        $('#startdate' + id).text(date_format_new(vote_start_d, "dd M, yy"));
                        $('#enddate' + id).text(date_format_new(vote_end_d, "dd M, yy"));

                        $('.update-form').each(function () {
                            $row = $(this).parents(".row:first"),
                                    $formDiv = $row.find('#votes_table'),
                                    $row.removeClass('bg-warning');
                            $(this).slideUp('slow');
                        });
						$.mission_settings();
						$t.attr('data-original-title','Update Vote');
						window.location.href += "#votes";
						location.reload();
                        // $('#title'+id).parent('.row').focus();
                    } else {
                        if (!$.isEmptyObject(response.content)) {
                            $.each(response.content,
                                    function (ele, msg) {
                                        //console.log(ele+'---'+msg);
                                        if ($('[name="data[Vote][' + ele + ']"]').length > 0) {
                                            var $inpEle = $('[name="data[Vote][' + ele + ']"]');
                                            if (ele == 'title') {
                                                $inpEle.parent().find('span.error-message').html(msg);
                                            } else {
                                                $inpEle.parent().find('span.error-message').html(msg);
                                            }
                                        }

                                        if ($('[name="data[VoteQuestion][' + ele + ']"]').length > 0) {
                                            //console.log(ele);
                                            var $inpEle = $('[name="data[VoteQuestion][' + ele + ']"]');
                                            if (ele == 'title') {
                                                $inpEle.parent().find('span.error-message').html(msg);
                                            } else {
                                                $inpEle.parent().find('span.error-message').html(msg);
                                            }
                                        }


                                        if ($('[name="data[VoteUser][' + ele + ']"]').length > 0) {
                                            //console.log(ele);
                                            var $inpEle = $('[name="data[VoteUser][' + ele + ']"]');
                                            if (ele == 'title') {
                                                $inpEle.parent().find('span.error-message').html(msg);
                                            } else {
                                                $inpEle.parent().find('span.error-message').html(msg);
                                            }
                                        }

                                        //
                                        // //console.log($('input[name^="data[VoteQuestionOption][option]["').text);

                                        $('input[name^="data[VoteQuestionOption][option]["]').each(function () {
                                            //console.log($(this).val())
                                            //console.log(ele);
                                            if ($.trim($(this).val()) == '') {
                                                $(this).next('.error-messagess:first').html("This is required.");
                                            }
                                            else {
                                                //$(this).next('.error-messagess:first').html("");
                                            }

                                        })

                                    })
                        }
                    }
                }
            });
        })



        $("body").delegate(".update_feedback_btn", "click", function (event) {
            event.preventDefault();
            var $t = $(this),
                    $row = $t.parents(".row:first"),
                    $formDiv = $t.parent().parent().parent(),
                    $form = $formDiv.find('.formupdateFeedback'),
                    action = $form.attr("action"),
                    data_string = $form.serialize(),
                    $addForm = $formDiv.parent(),
                    perform = 'update';
            id = $(this).attr('id');
            var $txtdesc = $form.find('#feedback_desc_' + id),
                    $editor = $txtdesc.data('editor'),
                    formData = $form.serializeArray();
            if ($editor != undefined) {
                desc = $editor.Editor('getText'),
                        formData.push({'name': 'data[Feedback][reason]', 'value': desc});
            }
            if ($addForm.length) {
                perform = 'create'
            }

            if (!$formDiv.parent().is($(".feedback_form"))) {
                if ($formDiv.is(":visible")) {
                    $formDiv.addClass("bg-warning");
                } else {
                    $formDiv.removeClass("bg-warning");
                }
            }

            var values = {};
            $.each(formData, function (i, field) {
                values[field.name] = field.value;
            });

            //Value Retrieval Function
            var getValue = function (valueName) {
                return values[valueName];
            };

            //Retrieve the Values
            var feedback_start = $("#feedbackstart_date_" + id).val();
            var feedback_end = $("#feedbackend_date_" + id).val();

            $.ajax({
                type: 'POST',
                data: $.param(formData),
                url: action,
                global: false,
                dataType: 'JSON',
                beforeSend: function () {
                    $form.find('a.submit').addClass('disabled')
                },
                complete: function () {
                    setTimeout(function () {
                        $form.find('a.submit').removeClass('disabled')
                    }, 300)
                },
                success: function (response, statusText, jxhr) {


                    //$form.find('span').html("");
                    if (response.success) {

                        $('#title' + id).text(getValue("data[Feedback][title]"));
                        $('#feedbackstartdate' + id).text(date_format_new(feedback_start, "dd M, yy"));
                        $('#feedbackenddate' + id).text(date_format_new(feedback_end, "dd M, yy"));

                        $('.update-form').each(function () {
                            $row = $(this).parents(".row:first"),
                                    $formDiv = $row.find('#feedbacks_table'),
                                    $row.removeClass('bg-warning');
                            $(this).slideUp('slow');
                        });
						$.mission_settings();
						$t.attr('data-original-title','Update Feedback');
						window.location.href += "#feedbacks";
						location.reload();
                        // $('#title'+id).parent('.row').focus();
                    } else {
                        if (!$.isEmptyObject(response.content)) {
                            $.each(response.content,
                                    function (ele, msg) {
                                        //console.log(ele+'---'+msg);
                                        if ($('[name="data[Feedback][' + ele + ']"]').length > 0) {
                                            var $inpEle = $('[name="data[Feedback][' + ele + ']"]');
                                            if (ele == 'title') {
                                                $inpEle.parent().find('span.error-message').html(msg);
                                            } else {
                                                $inpEle.parent().find('span.error-message').html(msg);
                                            }
                                        }
                                    })
                        }
                    }
                }
            });
        })

        function date_format(datestr, format) {
            var datestr_arr = datestr.split("-");
            var d = datestr_arr[2] + '-' + datestr_arr[1] + '-' + datestr_arr[0];
            var formated_date = $.datepicker.formatDate(format, new Date(d));
            return formated_date;

        }
		function date_format_new(datestr, format) {
			var datestr_arr = datestr.split(" ");
			var formated_date = datestr_arr[0] + ' ' + datestr_arr[1] + ', ' + datestr_arr[2];
			return formated_date;
		}


        // Send Vote Reminder
        $(document).on("click", ".reminder_vote_user", function (event) {
            $('.reminderloading').show();
            event.preventDefault();
            vote_id = $(this).attr('rel');
            action = $js_config.base_url + "entities/reminder_vote_user/" + vote_id;
            $.ajax({
                type: 'POST',
                url: action,
                global: false,
                dataType: 'JSON',
                beforeSend: function () {
                },
                complete: function (res) {
                    //console.log(res);
                    res = res.responseText;
                    if (res != 'error') {
                        //$('.reminderloading').hide();
                        $.modal_alert('A reminder notification has been sent to those participants who have not responded.','Reminder Notification');
                        return;
                    } else {
                        //$('.reminderloading').hide();
                        $.modal_alert('No reminder is needed as all participants have responded.','Reminder Notification');
                        return;
                    }
                },
                success: function (response) {

                },
                error: function (response) {
                    $('.reminderloading').hide();
                    $.modal_alert(' There is seems to be some technical issue. Please try again. ');
                    return;
                }
            });
        });

        // Send Feedback Reminder
        $(document).on("click", ".reminder_feedback_user", function (event) {
            $('.reminderloading').show();
            event.preventDefault();
            feedback_id = $(this).attr('rel');
            action = $js_config.base_url + "entities/reminder_feedback_user/" + feedback_id;
            $.ajax({
                type: 'POST',
                url: action,
                global: false,
                dataType: 'JSON',
                beforeSend: function () {
                },
                complete: function (res) {
                    //console.log(res);
                    res = res.responseText;
                    if (res != 'error') {
                        $('.reminderloading').hide();
                        $.modal_alert('A reminder notification has been sent to those participants who have not responded.','Reminder Notification');
                        return;
                    } else {
                        $('.reminderloading').hide();
                        $.modal_alert('No reminder is needed as all participants have responded.','Reminder Notification');
                        return;
                    }
                },
                success: function (response) {

                },
                error: function (response) {
                    $('.reminderloading').hide();
                    $.modal_alert(' There is seems to be some technical issue. Please try again. ');
                    return;
                }
            });
        });

        $(document).on("click", ".update_vote_user", function (event) {
            event.preventDefault();
            id = $(this).attr('id');
            //var g = $('#user_'+id).combogrid('grid');	// get datagrid object
            //var r = g.datagrid('getSelected');	// get the selected row
            //alert(r.name);
            // if(r != null && r.id != ''){
            var $t = $(this),
                    $row = $t.parents(".row:first"),
                    //$formDiv = $t.parent().parent().parent().parent().parent(),
					$formDiv = $t.parents('.update-form-user:first'),
                    $form = $formDiv.find('.formupdateVoteUsers'),
                    action = $form.attr("action"),
                    data_string = $form.serialize(),
                    $addForm = $formDiv.parent(),
                    perform = 'update';
            formData = $form.serializeArray();

            //console.log(formData)

            if ($addForm.length) {
                perform = 'create'
            }

            if (!$formDiv.parent().is($(".vote_form"))) {
                if ($formDiv.is(":visible")) {
                    $formDiv.addClass("bg-warning disabled");
                } else {
                    $formDiv.removeClass("bg-warning").removeClass("disabled");
                }
            }

            var values = {};
            $.each(formData, function (i, field) {
                values[field.name] = field.value;
            });

            //Value Retrieval Function
            var getValue = function (valueName) {
                return values[valueName];
            };

            //Retrieve the Values
            //console.log(action);

            $.ajax({
                type: 'POST',
                data: $.param(formData),
                url: action,
                global: false,
                dataType: 'JSON',
                beforeSend: function () {
                    $form.find('a.submit').addClass('disabled')
                },
                complete: function () {
                    setTimeout(function () {
                        $form.find('a.submit').removeClass('disabled')
                    }, 300)
                    location.reload();
                },
                success: function (response, statusText, jxhr) {

                    //$form.find('span').html("");
                    if (response.success) {
                        location.reload();
                    } else {
                        location.reload();
                    }
                }
            });
        })

        $(document).on("click", ".update_feedback_user", function (event) {
            event.preventDefault();
            id = $(this).attr('id');
            var $t = $(this),
                    $row = $t.parents(".row:first"),
                    $formDiv = $t.parents('.update-form-user') ,
                    $form = $formDiv.find('.formupdateFeedbackUsers'),
                    action = $form.attr("action"),
                    data_string = $form.serialize(),
                    $addForm = $formDiv.parent(),
                    perform = 'update';
            formData = $form.serializeArray();

            if ($addForm.length) {
                perform = 'create'
            }

            if (!$formDiv.parent().is($(".feedback_form"))) {
                if ($formDiv.is(":visible")) {
                } else {
                    $formDiv.removeClass("bg-warning").removeClass("disabled");
                }
            }

            var values = {};
            $.each(formData, function (i, field) {
                values[field.name] = field.value;
            });

            //Value Retrieval Function
            var getValue = function (valueName) {
                return values[valueName];
            };


            $.ajax({
                type: 'POST',
                data: $.param(formData),
                url: action,
                global: true,
                dataType: 'JSON',
                beforeSend: function () {
                    $form.find('a.submit').addClass('disabled')
                },
                complete: function (response) {
                    setTimeout(function () {
                        $form.find('a.submit').removeClass('disabled')
                    }, 300)
                    var r = response.responseJSON;

                    location.reload();
                    $('#feedbackparicipants' + id).text(r.content);
                    var users = r.users;
                    // //console.log(r);
                    //	//console.log(users);
                    date = r.date;
                    $.each(users, function (index, value) {
                        userid = value.id;
                        name = value.name;
                        //console.log(value);
                        htmlcontent = '<div class="user_list"><div class="name col-sm-4">' + name + '<input type="hidden" value="' + userid + '" name="data[FeedbackUser][list][]" class="textbox-value"></div><div class=" col-sm-4">' + date + '</div><div class=" col-sm-4">-</div></div><span id="more' + id + '"></span>';
                    });
                    $form.get(0).reset();
                },
                success: function (response, statusText, jxhr) {
                    if (response.success) {
                        $('#feedbackparicipants' + id).text(response.content);
                    } else {
					return;
                        location.reload();
                    }
                }
            });
        })

        /********* Votes *********/
		$(".newVoteId").val('');
		$("body").delegate("#cancelvotefirststep", "click", function (event) {
			var nvote_id = $(".newVoteId").val();
			$('#confirm-boxs #s_off_no').removeAttr('disabled');
			if(nvote_id.length > 0 ){
					$('#confirm-boxs').find('#modal_header').html('<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button><h3 class="modal-title">Abandon Vote</h3>');
					$('#confirm-boxs').find('#modal_body').text("Are you sure you want to abandon this Vote?");
					$('#confirm-boxs').find('#modal_footer #s_off_yes').text('Abandon');
					$('#confirm-boxs').find('#modal_footer #s_off_no').text('Cancel');

					$('#confirm-boxs').modal({keyboard: true}).on('click', '#s_off_yes', function () {
						$(window).unbind('beforeunload');

						$(this).closest('form').find('input[type=text], textarea').val('');
						$.mission_settings();
                        $.ajax({
                            url: $js_config.base_url + 'entities/cancel_vote',
                            type: 'POST',
                            // dataType: 'json',
                            data: {'id': nvote_id},
                            success: function(response){
                                console.log(response)
                                if(response == 'success'){
                                    $(this).closest('form').find('input[type=text], textarea').val('');
                                    location.reload();
                                }
                            }
                        })

					});
			} else {
				$.mission_settings();
				$(this).closest('form').find('input[type=text], textarea').val('');
				// $('#vote_desc').data("wysihtml5").editor.setValue('');
			}
        });

		$("body").delegate("#cancelvote, #cancel_final_vote", "click", function (event) {
            event.preventDefault()
			$('#confirm-boxs #s_off_no').removeAttr('disabled');
			$('#confirm-boxs').find('#modal_header').html('<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button><h3 class="modal-title">Abandon Vote</h3>');
            $('#confirm-boxs').find('#modal_body').text("Are you sure you want to abandon this Vote?");
			$('#confirm-boxs').find('#modal_footer #s_off_yes').text('Abandon');
			$('#confirm-boxs').find('#modal_footer #s_off_no').text('Cancel');

            $('#confirm-boxs').modal({keyboard: true}).on('click', '#s_off_yes', function () {

                $(window).unbind('beforeunload');
				$.mission_settings();
                var $form = $('.vote_form'),
                    voteid = $form.find('.vote_id').val();

                $.ajax({
                    url: $js_config.base_url + 'entities/cancel_vote',
                    type: 'POST',
                    // dataType: 'json',
                    data: {'id': voteid},
                    success: function(response){
                        console.log(response)
                        if(response == 'success'){
				            $(this).closest('form').find('input[type=text], textarea').val('');
                            location.reload();
                        }
                    }
                })

                //
				// $('#vote_desc').data("wysihtml5").editor.setValue('');
			});
        });

		$("body").delegate("#cancel_feedback", "click", function (event) {


		    $('#confirm-boxs #s_off_no').removeAttr('disabled');
            $('#confirm-boxs').find('#modal_header').html('<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button><h3 class="modal-title">Abandon Feedback</h3>');
			$('#confirm-boxs').find('#modal_body').text("Are you sure you want to abandon this Feedback?");
			$('#confirm-boxs').find('#modal_footer #s_off_yes').text('Abandon');
			$('#confirm-boxs').find('#modal_footer #s_off_no').text('Cancel');

			if($('#feedbackstep2 #feedback_id').val() > 0){
                BootstrapDialog.show({
                    title: '<h3 class="h3_style">Abandon Feedback</h3>',
                    message: 'Are you sure you want to abandon this Feedback?',
                    type: BootstrapDialog.TYPE_SUCCESS,
                    draggable: true,
                    buttons: [
                        {
                            label: ' Abandon',
                            cssClass: 'btn-success',
                            autospin: true,
                            action: function (dialogRef) {
                                $(window).unbind('beforeunload');
                                $.mission_settings();
                                $.ajax({
                                    type: 'POST',
                                    data: 'feedback_id=' + $('#feedbackstep2 #feedback_id').val() + '&element_id=' + $js_config.currentElementId,
                                    url: '<?php echo Router::url("/") ?>entities/cancel_feedback',
                                    global: false,
                                    success: function (response) {
                                            location.reload();
                                    }
                                })
                                dialogRef.enableButtons(false);
                                dialogRef.setClosable(false);

                            }
                        },
                        {
                            label: ' Cancel',
                            //icon: '',
                            cssClass: 'btn-danger',
                            action: function (dialogRef) {
                                dialogRef.close();
                            }

                        }
                    ]
                })
                /*$('#confirm-boxs').modal({keyboard: true}).on('click', '#s_off_yes', function () {
                    $.mission_settings();
    				$(window).unbind('beforeunload');
    				location.reload();
    				$(this).closest('form').find('input[type=text], textarea').val('');
    			});*/
			}else{
			$(this).closest('form').find('input[type=text], textarea').val('');
				$.mission_settings();
			}
        });

		$("body").delegate("#cancelfeedback_step2", "click", function (event) {
			var feedback_id = $('#feedbackstep2 input#feedback_id').val();
            BootstrapDialog.show({
                title: '<h3 class="h3_style">Abandon Feedback</h3>',
                message: 'Are you sure you want to abandon this Feedback?',
                type: BootstrapDialog.TYPE_SUCCESS,
                draggable: true,
                buttons: [
                    {
                        //icon: '',
                        label: ' Abandon',
                        cssClass: 'btn-success',
                        autospin: true,
                        action: function (dialogRef) {
                            $(window).unbind('beforeunload');
                            $.mission_settings();
                            $.ajax({
                                type: 'POST',
                                data: 'feedback_id=' + feedback_id + '&element_id=' + $js_config.currentElementId,
                                url: '<?php echo Router::url("/") ?>entities/cancel_feedback',
                                global: false,
                                success: function (response) {
                                        location.reload();
                                }
                            })
                            dialogRef.enableButtons(false);
                            dialogRef.setClosable(false);

                        }
                    },
                    {
                        label: ' Cancel',
                        //icon: '',
                        cssClass: 'btn-danger',
                        action: function (dialogRef) {
                            dialogRef.close();
                        }

                    }
                ]
            })
            $(this).closest('form').find('input[type=text], textarea').val('');

        });


        $("body").delegate(".remove_vote_btn", "click", function (event) {

            $('#confirm-boxs').find('#modal_body').text("Are you sure you want to delete this Vote?");
            id = $(this).attr('data-id');
			$('#confirm-boxs #s_off_no').removeAttr('disabled');

			BootstrapDialog.show({
    			title: '<h3 class="h3_style">Delete Vote</h3>',
    			message: 'Are you sure you want to delete this Vote?',
    			type: BootstrapDialog.TYPE_DANGER,
    			draggable: true,
    			buttons: [
    				{
    					//icon: '',
    					label: ' Delete',
    					cssClass: 'btn-success',
    					autospin: true,
    					action: function (dialogRef) {
    						$.when(
    							$.ajax({
                                type: 'POST',
                                data: 'id=' + id + '&element_id=' + $js_config.currentElementId,
                                url: '<?php echo Router::url("/") ?>entities/remove_vote',
                                global: false,
                                //dataType: 'JSON',
                                beforeSend: function () {
                                    // $form.find('a.submit').addClass('disabled')
                                },
                                complete: function () {
                                    setTimeout(function () {
                                        $('#confirm-boxs').modal('hide');
                                        $('#vote_row_' + id).slideUp();
                                        $('#historyvote_' + id).slideUp();
                                        window.location.href = $js_config.base_url + 'entities/update_element/' + $js_config.currentElementId + '#votes';
                                    }, 300)
                                },
                                success: function (response) {
                                    if (response == 'success') {

										$.ajax({
											type: 'POST',
											data: $.param({ project_id: $js_config.currentProjectId, workspace_id: $js_config.currentWorkspaceId, taskID : $js_config.currentElementId }),
											url: $js_config.base_url + 'entities/el_assets/',
											global: false,
											success: function(response) {
												$('.asset_counter').html(response);

												//dfd.resolve('');
											}

										})
                                        $('#confirm-boxs').modal('hide');
                                        $('#vote_row_' + id).slideUp();
                                        $('#historyvote_' + id).slideUp();
                                        window.location.href = $js_config.base_url + 'entities/update_element/' + $js_config.currentElementId + '#votes';
                                    }
                                }
                            })
    						).then(function( data, textStatus, jqXHR ) {
    							dialogRef.enableButtons(false);
    							dialogRef.setClosable(false);
    							dialogRef.getModalBody().html('<div class="loader"></div>');
    							 $.ajax({
                                        type: 'POST',
                                        url: $js_config.base_url + 'shares/countTotalElementParts/' + $js_config.currentElementId + '/Vote',
                                        global: false,
                                        success: function (data) {
                                            $('#element_tabs a.act_vote').html('<i class="asset-all-icon re-VoteBlack"></i>  (' + data + ')');
                                            //console.log($('#element_tabs a.act_vote'));
    										dialogRef.close();
											var vlengths = $('.nodatashow.vote').length ;
											if(vlengths < 1){
												$('#votes_table .table-rows.data_catcher').append('<span class="nodatashow vote"> No votes <div>');
											}
											if(data > 0){
												$('.nodatashow.vote').hide();
											}else{
												$('.nodatashow.vote').show();
											}
											}

                                    })
    						})
    					}
    				},
    				{
    					label: ' Cancel',
    					//icon: '',
    					cssClass: 'btn-danger',
    					action: function (dialogRef) {
    						dialogRef.close();
    					}

    				}
    			]
    		})

        });



		 $("body").delegate(".remove_feedback_btn", "click", function (event) {

		   event.preventDefault();
		  var id = $(this).attr('data-id');
          var element_id = $js_config.currentElementId;

			BootstrapDialog.show({
			title: '<h3 class="h3_style">Delete Feedback</h3>',
			message: 'Are you sure you want to delete this Feedback?',
			type: BootstrapDialog.TYPE_DANGER,
			draggable: true,
			buttons: [
				{
					//icon: '',
					label: ' Delete',
					cssClass: 'btn-success',
					autospin: true,
					action: function (dialogRef) {
						$.when(
						$.ajax({
                            type: 'POST',
                            data: 'feedback_id=' + id + '&element_id=' + element_id,
                            url: '<?php echo Router::url("/") ?>entities/remove_feedback',
                            global: false,
                            dataType: 'JSON',
                            beforeSend: function () {
                                // $form.find('a.submit').addClass('disabled')
                            },
                            success: function (response) {
                                if (response.success == true) {
									$.ajax({
										type: 'POST',
										data: $.param({ project_id: $js_config.currentProjectId, workspace_id: $js_config.currentWorkspaceId, taskID : $js_config.currentElementId }),
										url: $js_config.base_url + 'entities/el_assets/',
										global: false,
										success: function(response) {
											$('.asset_counter').html(response);

											//dfd.resolve('');
										}

									})
                                    setTimeout(function () {

                                        $('#feedback_row_' + id).slideUp();
                                        $('#historyfeedback_' + id).slideUp();
                                        location.href = '<?php echo SITEURL; ?>entities/update_element/' + element_id + '#feedbacks';
                                        //console.log('<?php echo SITEURL; ?>entities/update_element/' + $js_config.currentElementId + '#feedbacks');
                                    }, 300);
                                }

                            }
                        })
						).then(function( data, textStatus, jqXHR ) {
							dialogRef.enableButtons(false);
							dialogRef.setClosable(false);
							dialogRef.getModalBody().html('<div class="loader"></div>');
							$.ajax({
                                    type: 'POST',
                                    url: $js_config.base_url + 'shares/countTotalElementParts/' + $js_config.currentElementId + '/Feedback',
                                    global: false,
                                    success: function (data) {
                                        //console.log(data);
                                        $('#element_tabs a.act_feedback').html('<i class="asset-all-icon re-FeedbackBlack"></i>  (' + data + ')');


                                        //console.log($('#element_tabs a.act_feedback'));
										dialogRef.close();

										var flengths = $('.nodatashow.feedback').length ;
										if(flengths < 1){
											$('#feedbacks_table .table-rows.data_catcher').append('<span class="nodatashow feedback"> No feedback <div>');
										}
										if(data > 0){
											$('.nodatashow.feedback').hide();
										}else{
											$('.nodatashow.feedback').show();
										}
                                    }

                                })
						})
					}
				},
				{
					label: ' Cancel',
					//icon: '',
					cssClass: 'btn-danger',
					action: function (dialogRef) {
						dialogRef.close();
					}
				}
			]
		})
		});





        $("body").delegate(".save_vote_final", "click", function (event) {
            event.preventDefault();
            // var g = $('#demo-input-local').combogrid('grid');	// get datagrid object
            // var r = g.datagrid('getSelected');	// get the selected row
            //alert(r.name);

            // if(r != null && r.id != ''){
		  event.preventDefault();
            // var g = $('#demo-input-local-feedback').combogrid('grid');	// get datagrid object
            // var r = g.datagrid('getSelected');	// get the selected row
            //alert(r.name);
            var selectedUsers = []

            $('#VoteUpdateElementForm').find('input[name="data[VoteUser][list][]"]').each(function () {
                if ($(this).prop('checked')) {
                    selectedUsers.push('yes')
                }
            })
            // var selectedUsers = $('#formAddFeedbackDoc').find('input[name="data[FeedbackUser][list][]"]').length
            //console.log($('#formAddFeedbackDoc').serializeArray())
            if (selectedUsers.length) {

            $('#s_off_no').attr('disabled', false);
            var $t = $(this),
                    $row = $t.parents(".row:first"),
                    $formDiv = $t.parent().parent().parent(),
                    $form = $formDiv.find('#VoteUpdateElementForm'),
                    action = $form.attr("action"),
                    data_string = $form.serialize(),
                    $addForm = $formDiv.parent(),
                    perform = 'update';
            var $txtdesc = $form.find('#vote_desc'),
                    $editor = $txtdesc.data('editor'),
                    formData = $form.serializeArray();
            if ($editor != undefined) {
                desc = $editor.Editor('getText'),
                        formData.push({'name': 'data[Vote][reason]', 'value': desc});
            }
            if ($addForm.length) {
                perform = 'create'
            }

            if (!$formDiv.parent().is($(".vote_form"))) {
                if ($formDiv.is(":visible")) {
                    //$formDiv.addClass("bg-warning disabled");
                } else {
                    $formDiv.removeClass("bg-warning").removeClass("disabled");
                }
            }



            var $this = $(this),
                    data = $this.data(),
                    id = data.id,
                    title = data.header,
                    $cbox = $('#confirm-boxs'),
                    $yes = $cbox.find('#s_off_yes');
            $no = $cbox.find('#s_off_no');

            var $span_text = $yes.find('span.text'),
                    $div_progress = $yes.find('div.btn_progressbar');

            // set message


            $('#confirm-boxs').find('#modal_body').text("Are you sure ? Once you save this Vote, You will not able to edit question details.")
            $('#confirm-boxs').find('#modal_header').text(title)

  /*           $('#confirm-boxs').modal({keyboard: true})
                    .on('click', '#s_off_yes', function () { */

                        //$('#confirm-boxs').modal('hide')

						BootstrapDialog.show({
						title: 'New Vote',
						message: 'Are you sure you want to submit this Vote?',
						type: BootstrapDialog.TYPE_DANGER,
						draggable: true,
						buttons: [
							{
								//icon: '',
								label: ' Submit',
								cssClass: 'btn-success',
								autospin: true,
								action: function (dialogRef) {
									$.when(

									$.ajax({
										type: 'POST',
										data: $.param(formData),
										url: action,
										global: false,
										dataType: 'JSON',
										beforeSend: function () {
										   //$form.find('a.submit').addClass('disabled')
										},
										complete: function () {
											setTimeout(function () {
											   //$form.find('a.submit').removeClass('disabled')
											}, 300)
										},
										success: function (response, statusText, jxhr) {

											//$form.find('span').html("");
											if (response.success) {
												var emp = 0;
												$.mission_settings();
												var step = response.step;
												if (step == '1') {
													vote_id = response.content.Vote.id;
													$('#vote_id').val(vote_id);
													$('#step1').hide();
													$('#step2').show();
												} else if (step == '2') {
													vote_id = response.content.VoteQuestion.vote_id;
													$('#VoteUservote_id').val(vote_id);
													$('#step2').hide();
													$('#step3').show();
												} else if (step == '3') {
													if(response.hasOwnProperty('socket_content')){
														$.socket.emit('socket:notification', response.socket_content.socket, function() {});
													}
													window.location.href += "#votes";
													location.reload();
												}
											}
											else {
												if (!$.isEmptyObject(response.content)) {
													$.each(response.content,
															function (ele, msg) {
																//console.log(ele+'---'+msg);
																if ($('[name="data[Vote][' + ele + ']"]').length > 0) {
																	var $inpEle = $('[name="data[Vote][' + ele + ']"]');
																	if (ele == 'title') {
																		$inpEle.parent().find('span.error-message').html(msg);
																	} else {
																		$inpEle.parent().find('span.error-message').html(msg);
																	}
																}

																if ($('[name="data[VoteQuestion][' + ele + ']"]').length > 0) {
																	// //console.log(ele);
																	var $inpEle = $('[name="data[VoteQuestion][' + ele + ']"]');
																	if (ele == 'title') {
																		$inpEle.parent().find('span.error-message').html(msg);
																	} else {
																		$inpEle.parent().find('span.error-message').html(msg);
																	}
																}


																if ($('[name="data[VoteUser][' + ele + ']"]').length > 0) {
																	//console.log(ele);
																	var $inpEle = $('[name="data[VoteUser][' + ele + ']"]');
																	if (ele == 'title') {
																		$inpEle.parent().find('span.error-message').html(msg);
																	} else {
																		$inpEle.parent().find('span.error-message').html(msg);
																	}
																}


															})
												}
											}
										}
									})
									).then(function( data, textStatus, jqXHR ) {
										dialogRef.enableButtons(false);
										dialogRef.setClosable(false);
										//dialogRef.getModalBody().html('<div class="loader"></div>');
										 $.ajax({
												type: 'POST',
												url: $js_config.base_url + 'shares/countTotalElementParts/' + $js_config.currentElementId + '/Vote',
												global: false,
												success: function (data) {
													$('#element_tabs a.act_vote').html('<i class="asset-all-icon re-VoteBlack"></i>  (' + data + ')');
													dialogRef.close();
												}

											})

									})
								}
							},
							{
								label: ' Cancel',
								//icon: '',
								cssClass: 'btn-danger',
								action: function (dialogRef) {
									dialogRef.close();
								}
							}
						]
					})

					}else {
						$.modal_alert('Please select at least one User or Group.','New Vote');
						return;
					}


        });


		$("body").delegate(".save_feedback_final", "click", function (event) {

		  event.preventDefault();
            // var g = $('#demo-input-local-feedback').combogrid('grid');	// get datagrid object
            // var r = g.datagrid('getSelected');	// get the selected row
            //alert(r.name);
            var selectedUsers = []

            $('#formAddFeedbackDoc').find('input[name="data[FeedbackUser][list][]"]').each(function () {
                if ($(this).prop('checked')) {
                    selectedUsers.push('yes')
                }
            })
            // var selectedUsers = $('#formAddFeedbackDoc').find('input[name="data[FeedbackUser][list][]"]').length
            //console.log($('#formAddFeedbackDoc').serializeArray())
            if (selectedUsers.length) {


                var $t = $(this),
                        $row = $t.parents(".row:first"),
                        $formDiv = $t.parent().parent().parent(),
                        $form = $formDiv.find('#formAddFeedbackDoc'),
                        action = $form.attr("action"),
                        data_string = $form.serialize(),
                        $addForm = $formDiv.parent(),
                        perform = 'update';
                var $txtdesc = $form.find('#feedback_desc'),
                        $editor = $txtdesc.data('editor'),
                        formData = $form.serializeArray();
                if ($editor != undefined) {
                    desc = $editor.Editor('getText'),
                            formData.push({'name': 'data[Feedback][reason]', 'value': desc});
                }
                if ($addForm.length) {
                    perform = 'create'
                }

                /* if (!$formDiv.parent().is($(".feedback_form"))) {
                 if ($formDiv.is(":visible")) {
                 $formDiv.addClass("bg-warning disabled");
                 } else {
                 $formDiv.removeClass("bg-warning").removeClass("disabled");
                 }
                 } */



                var $this = $(this),
                        data = $this.data(),
                        id = data.id,
                        title = data.header,
                        $cbox = $('#confirm-boxs'),
                        $yes = $cbox.find('#s_off_yes');
                $no = $cbox.find('#s_off_no');

                var $span_text = $yes.find('span.text'),
                        $div_progress = $yes.find('div.btn_progressbar');


			BootstrapDialog.show({
			title: 'New Feedback',
			message: 'Are you sure you would like to submit this Feedback request?',
			type: BootstrapDialog.TYPE_DANGER,
			draggable: true,
			buttons: [
				{
					//icon: '',
					label: ' Submit',
					cssClass: 'btn-success',
					autospin: true,
					action: function (dialogRef) {
						$.when(
						$.ajax({
                                type: 'POST',
                                data: $.param(formData),
                                url: action,
                                global: false,
                                dataType: 'JSON',
                                beforeSend: function () {
                                    $form.find('a.submit').addClass('disabled')
                                },
                                complete: function () {
                                    setTimeout(function () {
                                        $form.find('a.submit').removeClass('disabled')
                                    }, 300)
                                },
                                success: function (response, statusText, jxhr) {

                                    //$form.find('span').html("");
                                    if (response.success) {
                                        var emp = 0;
										$.mission_settings();
                                        var step = response.step;
                                        if (step == '1') {
                                            feedback_id = response.content.Feedback.id;
                                            $('#feedback_id').val(feedback_id);
                                            $('#step1').hide();
                                            $('#step2').show();
                                        } else if (step == '2') {

                                            $(window).unbind('beforeunload');
											if(response.hasOwnProperty('socket_content')){
												$.socket.emit('socket:notification', response.socket_content.socket, function() {});
											}
											window.location.href += "#feedbacks";
											location.reload();
                                        }
                                    }
                                    else {
                                        if (!$.isEmptyObject(response.content)) {
                                            $.each(response.content,
                                                    function (ele, msg) {
                                                        //console.log(ele+'---'+msg);
                                                        if ($('[name="data[Vote][' + ele + ']"]').length > 0) {
                                                            var $inpEle = $('[name="data[Vote][' + ele + ']"]');
                                                            if (ele == 'title') {
                                                                $inpEle.parent().find('span.error-message').html(msg);
                                                            } else {
                                                                $inpEle.parent().find('span.error-message').html(msg);
                                                            }
                                                        }

                                                        if ($('[name="data[VoteQuestion][' + ele + ']"]').length > 0) {
                                                            // //console.log(ele);
                                                            var $inpEle = $('[name="data[VoteQuestion][' + ele + ']"]');
                                                            if (ele == 'title') {
                                                                $inpEle.parent().find('span.error-message').html(msg);
                                                            } else {
                                                                $inpEle.parent().find('span.error-message').html(msg);
                                                            }
                                                        }


                                                        if ($('[name="data[VoteUser][' + ele + ']"]').length > 0) {
                                                            //console.log(ele);
                                                            var $inpEle = $('[name="data[VoteUser][' + ele + ']"]');
                                                            if (ele == 'title') {
                                                                $inpEle.parent().find('span.error-message').html(msg);
                                                            } else {
                                                                $inpEle.parent().find('span.error-message').html(msg);
                                                            }
                                                        }


                                                    })
                                        }
                                    }

                                }
                            })
						).then(function( data, textStatus, jqXHR ) {
							dialogRef.enableButtons(false);
							dialogRef.setClosable(false);
							//dialogRef.getModalBody().html('<div class="loader"></div>');
							$.ajax({
                                        type: 'POST',
                                        url: $js_config.base_url + 'shares/countTotalElementParts/' + $js_config.currentElementId + '/Feedback',
                                        global: false,
                                        success: function (data) {

                                            $('#element_tabs a.act_feedback').html('<i class="asset-all-icon re-FeedbackBlack"></i>  (' + data + ')');
                                            //console.log($('#element_tabs a.act_feedback'));
                                        }

                                    })
						})
					}
				},
				{
					label: ' Cancel',
					//icon: '',
					cssClass: 'btn-danger',
					action: function (dialogRef) {
						dialogRef.close();
					}
				}
			]
		})

		}else {
                $.modal_alert('Please select at least one User or Group.','New Feedback');
                return;
            }

		});





        $("body").delegate(".update_vote", "click", function (event) {
            $('.formupdateFeedback').show();
            $('.formupdateVote').show();
            event.preventDefault()
            var $that = $(this),
                    $icon = $that.find("i"),
                    $row = $that.parents(".row:first"),
                    $formDiv = $row.find('.update-form'),
                    $form = $formDiv.find('.update-form-user');

            if ($row.hasClass('bg-warning')) {
                $row.removeClass('bg-warning')
                $row.find('.update-form').slideUp('slow')
                return;
            }
            $('.update-form-user').each(function () {
                $(this).slideUp('slow');
                $rowdata = $(this).parents(".row:first");
                if ($rowdata.hasClass('bg-warning')) {
                    $rowdata.removeClass('bg-warning');
                }
                if ($rowdata.hasClass('bg-warning1')) {
                    $rowdata.removeClass('bg-warning1');
                }
                if ($rowdata.hasClass('bg-warning2')) {
                    $rowdata.removeClass('bg-warning2');
                }
            });

            $('.update-form').each(function () {
                $(this).slideUp('slow');
                $rowdata = $(this).parents(".row:first");
                if ($rowdata.hasClass('bg-warning')) {
                    $rowdata.removeClass('bg-warning');
                }
                if ($rowdata.hasClass('bg-warning1')) {
                    $rowdata.removeClass('bg-warning1');
                }
                if ($rowdata.hasClass('bg-warning2')) {
                    $rowdata.removeClass('bg-warning2');
                }
            });

            $('.View-result-form').each(function () {
                $(this).slideUp('slow');
                $rowdata = $(this).parents(".row:first");
                if ($rowdata.hasClass('bg-warning')) {
                    $rowdata.removeClass('bg-warning');
                }
                if ($rowdata.hasClass('bg-warning1')) {
                    $rowdata.removeClass('bg-warning1');
                }
                if ($rowdata.hasClass('bg-warning2')) {
                    $rowdata.removeClass('bg-warning2');
                }
            });
            // console.clear()
            //	$textarea_desc = $form.find('.vote_desc')
            // //console.log($form);
            $(this).find('i').parent().toggleAttr('data-original-title', 'Update Vote', 'Update Vote');

            // Add wysihtml5 editor with dynamically cteated textarea
            var $edtor = $form.find('textarea[id^=vote_desc_]'),
                    editorData = $edtor.data('editor');
            //console.log($edtor.val());
            //console.log(editorData);
            if (editorData == undefined) {
                $edtor.Editor();
                var edata = $edtor.data('editor');
                $edtor.Editor('setText', $edtor.val())
            } else {
                $edtor.Editor('setText', $edtor.val())
            }

            if ($row.hasClass('bg-warning')) {
                $row.removeClass('bg-warning')
                $row.find('.update-form').slideUp('slow')
            } else {
                $row.addClass('bg-warning')
                $row.find('.update-form').slideDown('slow')
            }


            $('#votes_table .row.bg-warning').not($row[0]).removeClass('bg-warning')

            var $visible_forms = $('.list-form').not(".vote_form > .list-form").filter(function () {

                return $(this).is(":visible") == true;

            })

            if ($('#votes_table .row.bg-warning').length <= 0) {

                $('.vote_form #VotesUpdateElementForm').slideDown(1000);
            }
            else {
                $('.vote_form #VotesUpdateElementForm').slideUp(1000);

            }

        })


        $("body").delegate(".update_feedback", "click", function (event) {
            $('.formupdateFeedback').show();
            $('.formupdateVote').show();
            event.preventDefault()
            var $that = $(this),
                    $icon = $that.find("i"),
                    $row = $that.parents(".row:first"),
                    $formDiv = $row.find('.update-form'),
                    $form = $formDiv.find('.update-form-user');

            if ($row.hasClass('bg-warning')) {
                $row.removeClass('bg-warning')
                $row.find('.update-form').slideUp('slow')
                return;
            }
            $('.update-form-user').each(function () {
                $(this).slideUp('slow');
                $rowdata = $(this).parents(".row:first");
                if ($rowdata.hasClass('bg-warning')) {
                    $rowdata.removeClass('bg-warning');
                }
                if ($rowdata.hasClass('bg-warning1')) {
                    $rowdata.removeClass('bg-warning1');
                }
                if ($rowdata.hasClass('bg-warning2')) {
                    $rowdata.removeClass('bg-warning2');
                }
            });

            $('.update-form').each(function () {
                $(this).slideUp('slow');
                $rowdata = $(this).parents(".row:first");
                if ($rowdata.hasClass('bg-warning')) {
                    $rowdata.removeClass('bg-warning');
                }
                if ($rowdata.hasClass('bg-warning1')) {
                    $rowdata.removeClass('bg-warning1');
                }
                if ($rowdata.hasClass('bg-warning2')) {
                    $rowdata.removeClass('bg-warning2');
                }
            });

            $('.View-result-form').each(function () {
                $(this).slideUp('slow');
                $rowdata = $(this).parents(".row:first");
                if ($rowdata.hasClass('bg-warning')) {
                    $rowdata.removeClass('bg-warning');
                }
                if ($rowdata.hasClass('bg-warning1')) {
                    $rowdata.removeClass('bg-warning1');
                }
                if ($rowdata.hasClass('bg-warning2')) {
                    $rowdata.removeClass('bg-warning2');
                }
            });

            $(this).find('i').parent().toggleAttr('data-original-title', 'Update Feedback', 'Update Feedback');

            // Add wysihtml5 editor with dynamically cteated textarea
            var $edtor = $form.find('textarea[id^=feedback_desc_]'),
                    editorData = $edtor.data('editor');

            if (editorData == undefined) {
                $edtor.Editor();
                var edata = $edtor.data('editor');
                $edtor.Editor('setText', $edtor.val())
            } else {
                $edtor.Editor('setText', $edtor.val())
            }

            if ($row.hasClass('bg-warning')) {
                $row.removeClass('bg-warning')
                $row.find('.update-form').slideUp('slow')
            } else {
                $row.addClass('bg-warning')
                $row.find('.update-form').slideDown('slow')
            }


            $('#feedback_table .row.bg-warning').not($row[0]).removeClass('bg-warning')

            var $visible_forms = $('.list-form').not(".feedback_form > .list-form").filter(function () {

                return $(this).is(":visible") == true;

            })

            if ($('#votes_table .row.bg-warning').length <= 0) {

                $('.vote_form #FeedbackUpdateElementForm').slideDown(1000);
            }
            else {
                $('.vote_form #FeedbackUpdateElementForm').slideUp(1000);

            }

        })


        $("body").delegate(".update-form-user1", "click", function (event) {

            $('.formupdateFeedbackUsers').slideDown(1000);
            $('.formupdateVoteUsers').slideDown(1000);
            event.preventDefault()
            var $that = $(this),
                    $icon = $that.find("i"),
                    $row = $that.parents(".row:first"),
                    $formDiv = $row.find('.update-form'),
                    $form = $formDiv.find('.update-form-user');
            if ($row.hasClass('bg-warning1')) {
                $row.removeClass('bg-warning1')
                $row.find('.update-form-user').slideUp('slow')
                return;
            }


            $('.update-form-user').each(function () {
                $(this).slideUp('slow');
                $rowdata = $(this).parents(".row:first");
                if ($rowdata.hasClass('bg-warning')) {
                    $rowdata.removeClass('bg-warning');
                }
                if ($rowdata.hasClass('bg-warning1')) {
                    $rowdata.removeClass('bg-warning1');
                }
                if ($rowdata.hasClass('bg-warning2')) {
                    $rowdata.removeClass('bg-warning2');
                }
            });

            $('.update-form').each(function () {
                $(this).slideUp('slow');
                $rowdata = $(this).parents(".row:first");
                if ($rowdata.hasClass('bg-warning')) {
                    $rowdata.removeClass('bg-warning');
                }
                if ($rowdata.hasClass('bg-warning1')) {
                    $rowdata.removeClass('bg-warning1');
                }
                if ($rowdata.hasClass('bg-warning2')) {
                    $rowdata.removeClass('bg-warning2');
                }
            });

            $('.View-result-form').each(function () {
                $(this).slideUp('slow');
                $rowdata = $(this).parents(".row:first");
                if ($rowdata.hasClass('bg-warning')) {
                    $rowdata.removeClass('bg-warning');
                }
                if ($rowdata.hasClass('bg-warning1')) {
                    $rowdata.removeClass('bg-warning1');
                }
                if ($rowdata.hasClass('bg-warning2')) {
                    $rowdata.removeClass('bg-warning2');
                }
            });



            if ($row.hasClass('bg-warning1')) {
                $row.removeClass('bg-warning1')
                $row.find('.update-form-user').slideUp('slow')
            } else {

                $row.addClass('bg-warning1')
                $row.find('.update-form-user').slideDown('slow')
                //console.log('0');
                //$row.find('.update-form').slideup('slow')
            }

            if ($row.hasClass('bg-warning')) {
                $row.removeClass('bg-warning')
                $row.find('.update-form-user').slideUp('slow')
            }

            var $visible_forms = $('.list-form').not(".vote_form > .list-form").filter(function () {

                return $(this).is(":visible") == true;

            })
            $('#FeedbackUpdateElementForm').show();
            if ($('#feedbacks_table .row.bg-warning1').length <= 0) {

                $('.feedback_form #FeedbackUpdateElementForm').slideDown(1000);


            }
            else {
                $('.feedback_form #FeedbackUpdateElementForm').slideUp(1000);

            }
        })

        $("body").delegate(".viewuser", "click", function (event) {
            vote_id = $(this).attr('rel');
			$('.history_update').hide();

            event.preventDefault()
            var $that = $(this),
                    $icon = $that.find("i"),
                    $row = $that.parents(".row:first"),
					$elem_type = $that.data('elem');
            if ($row.hasClass('bg-warning2')) {
                $row.find('.View-result-form').slideUp('slow');
                $row.removeClass('bg-warning2')
                //console.log('dd');
                return;
            }


            $('.update-form-user').each(function () {
                $(this).slideUp('slow');
                $rowdata = $(this).parents(".row:first");
                if ($rowdata.hasClass('bg-warning')) {
                    $rowdata.removeClass('bg-warning');
                }
                if ($rowdata.hasClass('bg-warning1')) {
                    $rowdata.removeClass('bg-warning1');
                }
                if ($rowdata.hasClass('bg-warning2')) {
                    $rowdata.removeClass('bg-warning2');
                }
            });

            $('.update-form').each(function () {
                $(this).slideUp('slow');
                $rowdata = $(this).parents(".row:first");
                if ($rowdata.hasClass('bg-warning')) {
                    $rowdata.removeClass('bg-warning');
                }
                if ($rowdata.hasClass('bg-warning1')) {
                    $rowdata.removeClass('bg-warning1');
                }
                if ($rowdata.hasClass('bg-warning2')) {
                    $rowdata.removeClass('bg-warning2');
                }
            });

            $('.View-result-form').each(function () {
                $(this).slideUp('slow');
                $rowdata = $(this).parents(".row:first");
                if ($rowdata.hasClass('bg-warning')) {
                    $rowdata.removeClass('bg-warning');
                }
                if ($rowdata.hasClass('bg-warning1')) {
                    $rowdata.removeClass('bg-warning1');
                }

                if ($rowdata.hasClass('bg-warning2')) {
                    $rowdata.removeClass('bg-warning2');
                }
            });



            if ($row.hasClass('bg-warning2')) {
                $row.removeClass('bg-warning2')
                $row.find('.vote-result-form').slideUp('slow')
            } else {

                $row.addClass('bg-warning2')
                $row.find('.vote-result-form').slideDown('slow')
            }



            var $visible_forms = $('.list-form').not(".vote_form > .list-form").filter(function () {

                return $(this).is(":visible") == true;

            })

            if ($('#votes_table .row.bg-warning2').length <= 0) {

                $('.vote_form #VotesUpdateElementForm').slideDown(1000);
            }
            else {
                $('.vote_form #VotesUpdateElementForm').slideUp(1000);

            }

            var formURL = $(this).attr('data-whatever') // Extract info from data-* attributes
            $('#View-result-form' + vote_id).html('<img src="' + $js_config.base_url + 'images/ajax-loader.gif" alt="loading..."  style="margin: auto; padding: 10px 0px 25px;"/>');

            $.ajax({
                url: formURL,
                async: false,
                success: function (response) {
                    if ($.trim(response) != 'success') {
						if($elem_type == 'vote') {
							$('#votes_table #vote_row_' + vote_id + ' #View-result-form' + vote_id).html(response);
						} else if($elem_type == 'feedback') {
							$('#feedbacks_table #feedback_row_' + vote_id + ' #View-result-form' + vote_id).html(response);
						} else {
							$('#View-result-form' + vote_id).html(response);
						}
                    } else {
						if($elem_type == 'vote') {
							$('#votes_table #vote_row_' + vote_id + ' #View-result-form' + vote_id).html('<span style="text-align: center; display: block; padding: 0px 0px 20px;">There is some techanical issue. Please try again. </span>');
						} else if($elem_type == 'feedback') {
							$('#feedbacks_table #feedback_row_' + vote_id + ' #View-result-form' + vote_id).html('<span style="text-align: center; display: block; padding: 0px 0px 20px;">There is some techanical issue. Please try again. </span>');
						} else {
							$('#View-result-form' + vote_id).html('<span style="text-align: center; display: block; padding: 0px 0px 20px;">There is some techanical issue. Please try again. </span>');
						}
                    }
                }
            });
			if($elem_type == 'vote') {
				$('#votes_table #vote_row_' + vote_id + ' #View-result-form' + vote_id).slideDown('slow');
			} else if($elem_type == 'feedback') {
				$('#feedbacks_table #feedback_row_' + vote_id + ' #View-result-form' + vote_id).slideDown('slow');
			} else {
				$('#View-result-form' + vote_id).slideDown('slow');
			}
        })

        $('.sign_off_vote').on('click', function (e) {
            e.preventDefault();

            var $this = $(this),
                    data = $this.data(),
                    id = data.id,
					daction = data.action,
                    title = data.header,
                    eid = data.eid,
                    reopen = 'signoff';

			var dyn_text = '';
			var dyn_btn = 'Reopen';
			if( daction == 'Sign-Off' ){
				dyn_text = 'Sign Off Vote';
				dyn_btn = 'Sign Off';
			} else {
				dyn_text = 'Reopen Vote';
				dyn_btn = 'Reopen';
                reopen = 'reopen';
			}

            $('#sign_off_yes span.text').text(dyn_btn);
            $('#sign_off_no').text('Cancel');


            // set message
            //var body_text = 'Are you sure you want to Sign off?';
            //if( $(this).is($('.reopen_decision')) )
            body_text = $this.attr('data-msg');


                BootstrapDialog.show({
                    title: dyn_text,
                    message: body_text,
                    type: BootstrapDialog.TYPE_SUCCESS,
                    draggable: true,
                    buttons: [{
                        // icon: 'fa fa-cog',
                        label: dyn_btn,
                        cssClass: 'btn-success',
                        autospin: true,
                        action: function(dialogRef) {
                            $.when(
                                $.ajax({
                                    url: $js_config.base_url + 'entities/vote_signoff/' + eid,
                                    type: "POST",
                                    data: $.param({'data[Vote][id]': id, 'data[Vote][element_id]': eid, reopen: reopen}),
                                    dataType: "JSON",
                                    success: function(response) {
                                        location.reload();
                                    }
                                })
                            ).then(function(data, textStatus, jqXHR) {
                                dialogRef.enableButtons(false);
                                dialogRef.setClosable(false);
                                setTimeout(function() {
                                    dialogRef.close();
                                }, 1500);
                            })
                        }
                    },
                    {
                        label: ' Cancel',
                        //icon: '',
                        cssClass: 'btn-danger',
                        action: function(dialogRef) {
                            dialogRef.close();
                        }
                    }
                ]
            });

        });
        $('.sign_off_feedback').on('click', function (e) {
            e.preventDefault();

            var $this = $(this),
                    data = $this.data(),
                    id = data.id,
					daction = data.action,
                    title = data.header,
                    eid = data.eid,
                    $cbox = $('#confirm-box'),
                    $yes = $cbox.find('#sign_off_yes'),
                    reopen = 'signoff';

            $('#sign_off_yes span.text').text('Sign Off');
			$cbox.addClass('modal-success');
            var $span_text = $yes.find('span.text'),
                    $div_progress = $yes.find('div.btn_progressbar');



			var dyn_text = '';
			var dyn_btn = 'Reopen';
			if( daction == 'Sign-Off' ){
				dyn_text = 'Sign Off Feedback';
				dyn_btn = 'Sign Off';
			} else {
				dyn_text = 'Reopen Feedback';
				dyn_btn = 'Reopen';
                reopen = 'reopen';
			}

            // set message
            //var body_text = 'Are you sure you want to Sign off?';
            //if( $(this).is($('.reopen_decision')) )
            body_text = $this.attr('data-msg');

            $('#confirm-box').find('#modal_body').text(body_text)
            //$('#confirm-box').find('#modal_header').text('Confirmation')

			$('#confirm-box').find('#modal_header').html('<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button><span style="font-size:16px;color:#fff;" id="myModalLabel" class="modal-title">'+dyn_text+'</span>');


			BootstrapDialog.show({
            // title: 'Message',
			title: dyn_text,
            type: BootstrapDialog.TYPE_SUCCESS,
            message: body_text,
            draggable: true,
            buttons: [{

                label: dyn_btn,
                cssClass: 'btn-success',
                autospin: true,
                action: function(dialogRef) {
                    // Ajax request to sign-off/reopen
                        dialogRef.enableButtons(false);
                        dialogRef.setClosable(false);
                        $.ajax({
                            type: 'POST',
                            data: {'data[Feedback][id]': id, 'data[Feedback][element_id]': eid, reopen: reopen},
                            url: $js_config.base_url + 'entities/feedback_signoff/' + eid,
                            global: false,
                            dataType: 'JSON',
                            success: function (response, statusText, jxhr) {
                                if (response.success) {
                                    setTimeout(function () {
                                        location.reload(true)
                                    }, 2500)
                                    //console.log('success')
                                }
                                else {
                                    //console.log('fail')
                                }
                            }
                        })
                }
            },
            {
                label: ' Cancel',
                //icon: '',
                cssClass: 'btn-danger',
                action: function(dialogRef) {
                    dialogRef.close();
                }
            }]
        });


					/* $('#confirm-box').modal({keyboard: true}).on('click', '#sign_off_yes', function () {
                        // Ajax request to sign-off/reopen
                        var post = {'data[Feedback][id]': id, 'data[Feedback][element_id]': eid},
                        data_string = $.param(post);

                        $.ajax({
                            type: 'POST',
                            data: data_string,
                            url: $js_config.base_url + 'entities/feedback_signoff/' + eid,
                            global: false,
                            dataType: 'JSON',
                            beforeSend: function () {
                                $span_text.css({'opacity': 0.5, 'color': '#222222'})
                                $div_progress.css({'width': '100%'})
                            },
                            complete: function () {
                                setTimeout(function () {
                                    $('#confirm-box').modal('hide')
                                    $span_text.css({'opacity': 1, 'color': '#ffffff'})
                                    $div_progress.css({'width': '0%'})
									$cbox.removeClass('modal-success');

                                }, 3000)
                            },
                            success: function (response, statusText, jxhr) {
                                if (response.success) {
                                    setTimeout(function () {
                                        location.reload(true)
                                    }, 2500)
                                    //console.log('success')
                                }
                                else {
                                    //console.log('fail')
                                }
                            }
                        })
                    }); */
        });

        $('.note_form .list-group-item').on('click', function () {
            //console.log("hide all");
			// $(this).find("input[name='data[Notes][title]']").val('');
			// $('#notes_table .list-form').hide();
			// $('#notes_table .row ').removeClass('bg-warning');
        })

        $('#MindmapsUpdateElementForm , #NotesUpdateElementForm , #formAddEditElementDecision,#FeedbackUpdateElementForm, #VoteUpdateElementForm,#NotesAddNoteForm,#MindmapsAddMindmapForm,#FeedbackAddFeedbackForm,#VoteAddVoteForm').hide();

        $('.sign_off_vote').on('click', function () {
            $('#sign_off_no').removeAttr('disabled');
        });
    });

// Open View Form
    $(document).on('click', '.viewuserPP', function (e) {
        var formURL = $(this).attr('data-whatever') // Extract info from data-* attributes
        $.ajax({
            url: formURL,
            async: false,
            success: function (response) {
                if ($.trim(response) != 'success') {
                    $('#popup_modal').html(response);
                } else {
                    location.reload(); // Saved successfully
                }
            }
        });
    })

// Participant user for votes
    $(document).on('click', '.viewuserPIU', function (e) {
        var formURL = $(this).attr('data-whatever') // Extract info from data-* attributes
        id = $(this).attr('rel');
        if ($.trim($('#participants_user_' + id).html()) == '') {
            $.ajax({
                url: formURL,
                async: false,
                global: true,
                success: function (response) {
                    if ($.trim(response) != 'success') {
                        $("div[id^='participants_user_']").html("");
                        $("div[id^='participants_feedback_user_']").html("");
                        $('#participants_user_' + id).html(response);

                    } else {
                        location.reload(); // Saved successfully
                    }
                }
            });
        }
    })



// Participant user for feedbacks
    $(document).on('click', '.viewuserPIUF', function (e) {
        var formURL = $(this).attr('data-whatever') // Extract info from data-* attributes
        id = $(this).attr('rel');
        if ($.trim($('#participants_feedback_user_' + id).html()) == '') {
            $.ajax({
                url: formURL,
                async: false,
                global: true,
                success: function (response) {
                    if ($.trim(response) != 'success') {
                        $("div[id^='participants_user_']").html("");
                        $("div[id^='participants_feedback_user_']").html("");
                        $('#participants_feedback_user_' + id).html(response);
                    } else {
                        location.reload(); // Saved successfully
                    }
                }
            });
        }
    })

    $(function () {

        var q = "<?php echo $_SERVER['QUERY_STRING']; ?>";
        var selected = $('#element_tabs li a.selected').attr('data-content');
		console.log(selected, "==selected==selected");
        if (selected == 'votes') {

            $('#votes_table .table-rows .row .viewuser').each(function () {
                var bar = $(this).attr('rel')
                if (bar == q) {
                    $(this).trigger('click');
                }
            });
        }

        if (selected == 'feedbacks') {

            $('#feedbacks_table .table-rows .row .viewuser').each(function () {
                var bar = $(this).attr('rel')
                if (bar == q) {
                    $(this).trigger('click');
                }
            });
        }




		$.checkRestrictTask = function(){

			var remote_url = $js_config.base_url + 'entities/check_task_restrict';
					params = { element_id: $js_config.element_id };

			$.ajax({
				url: remote_url,
				type: "POST",
				data: $.param(params),
				global: false,
				dataType:'json',
				success: function(response) {

					if( response.content == 1 ){

						//append_html = '<span id="dsptest" style="display:none">'+response.msg+'</span><a class="btn btn-app signing-off element-sign-off-restrict"><i class="fa fa-sign-out"></i> Sign Off</a><a href="#" class=" btn btn-app signing-off element-sign-off" data-msg="Are you sure you want to reopen this Task?" data-toggle="confirmation" data-header="Confirmation" data-value="0" data-id="'+$js_config.element_id+'"><i class="fa fa-sign-out fa-rotate-180"></i> Reopen</a>';
						append_html = '<span id="dsptest" style="display:none">'+response.msg+'</span><a class="element-sign-off-restrict"><i class="signoffblack"></i></a>';
						$('.signoff_reopen').html(append_html);


					} else if( response.content == 2 ){

						append_html = '<span id="dsptest" style="display:none">1</span><a data-original-title="Sign-off Task" class="tipText convertbtn" data-target="#modal_signofftask" data-toggle="modal" data-remote="'+$js_config.base_url + 'entities/signoff_task/'+$js_config.element_id+'" ><i class="signoffblack"></i></a>';
						$('.signoff_reopen').html(append_html);

					}else if( response.content == 4 ){

						append_html = '<span id="dsptest" style="display:none">'+response.msg+'</span><a href="javascript:void(0);" title="" class=" reopen-signoff task_evidence tipText" data-toggle="modal" data-target="#signoff_comment_show" data-remote="'+$js_config.base_url + 'entities/show_signoff/'+$js_config.element_id+'"data-original-title="Click to see Comment and Evidence disableS"><i class="signoffblack"></i> </a><a href="#" class="tipText element-sign-off" data-msg="Are you sure you want to reopen this Task?" data-original-title="Reopen Task" data-toggle="confirmation" data-header="Confirmation" data-value="0" data-id="'+$js_config.element_id+'"><i class="reopenblack"></i> </a>';
						$('.signoff_reopen').html(append_html);



					}else if( response.content == 5 ){

						append_html = '<span id="dsptest" style="display:none">'+response.msg+'</span><a href="javascript:void(0);" title="" class="tipText  reopen-signoff task_evidence" data-toggle="modal" data-target="#signoff_comment_show" data-remote="'+$js_config.base_url + 'entities/show_signoff/'+$js_config.element_id+'"data-original-title="Click to see Comment and Evidence"><i class="signoffblack"></i> Sign Off</a><a href="#" data-msg="Cannot reopen this Task because the Workspace is signed off."  class="tipText element-sign-off-restrict_Pro disable" data-original-title="Reopen Task"><i class="reopenblack"></i> Reopen</a>';
						$('.signoff_reopen').html(append_html);



					} else {

						append_html = '<a class=" tipText changesignoff" title="" data-toggle="modal" data-target="#signoff_comment_box" data-value="1" data-remote="'+$js_config.base_url + 'entities/tasks_signoff/'+$js_config.element_id+'" data-id="'+$js_config.element_id+'" data-original-title="Sign Off"><i class="signoffblack"></i></a>';

						$('.signoff_reopen').html(append_html);
					}
				}
			})
		}


		setInterval(function(){
			//$("#element_tabs li a.selected").trigger('click');
			//$.checkRestrictTask();
		},5000);


		$("body").on("click","#element_tabs li a.selected", function(){
			 var append_html = ''
			 if( $.trim($(this).text()) == 'Task' ){

					$.checkRestrictTask();


			 }

		})

    })

</script>
<style>
    .disabled {cursor:not-allowed !important; pointer-events:none}
    /* #trigger_edit_profile{ display:block; line-height:24px;} */
    .link_form{clear:both;}
    .input-group-btn .btn.btn-default.multiselect-clear-filter{line-height:19px !important;}


</style>

<script type="text/javascript" >
    $(document).ready(function () {
        $('#signoff_comment_box').on('hide.bs.modal', function(event) {
            $(this).removeData('bs.modal');
            $(this).find('.modal-content').html('')
        })
        //$(document).on("click", ".element_warning", function(e) {
        $(".element_warning").click(function (e) {
            var message = $(this).attr("data-msg");
			var headertitle = $(this).attr("data-title");
            BootstrapDialog.show({
                title: ( headertitle ) ? headertitle : 'Signed Off',
                type: BootstrapDialog.TYPE_DANGER,
                message: message,
                draggable: true,
                buttons: [{
                    label: 'Close',
                    cssClass: 'btn-danger',
                    action: function(dialogRef) {
                        dialogRef.close();
                    }
                }]
            });

         //   e.stopPropagation();
        });


    });
</script>


<script type="text/javascript" >
	$(function() {
		var url = '';
		var hash = location.hash;
		if( hash != '') {
			url = location.href.split('#')[0];
		}
		else {
			url = location.href;
		}

		var isLastSlash = (url[url.length - 1]=="/") ? true: false;
		var url= url.split("/");
		var element_id = url[url.length - (isLastSlash? 4: 3)];
		var asset_id = url[url.length - (isLastSlash? 3: 2)];
		var mission_text = url[url.length - (isLastSlash? 2: 1)];


		setTimeout(function() {
			if( $.isNumeric(element_id) && $.isNumeric(asset_id) ) {
				if( hash != '' && hash == '#notes' ) {
					$('#notes_table .update_note[data-id='+asset_id+']').trigger('click')
				}
				else if( hash != '' && hash == '#decisions' ) {
					$('#decisions_table .update_decision[data-id='+asset_id+']').trigger('click')
					//console.log($('#decisions_table .update_decision[data-id='+asset_id+']'))
				}
				else if( hash != '' && hash == '#feedbacks' ) {
					$('#feedbacks_table .viewuser[rel='+asset_id+']').trigger('click')
				}
				else if( hash != '' && hash == '#votes' ) {
					$('#votes_table .viewuser[rel='+asset_id+']').trigger('click')
				}
				else if( hash != '' && hash == '#mind_maps' && mission_text != '' ) {
					$('#mindmap_table .view_mindmap[data-id='+asset_id+']').trigger('click')
				}
			}
		}, 600);


		if( $("#votesystem").length > 0 ){
		if( $("#votesystem option:selected").val().length > 0 ){
			var voteTdesc = $("#votesystem :selected").attr("title");
			$("#voteTypeDescription").show();
			$("#voteTypeDescriptionInput").val(voteTdesc);
		}
		}
		$('body').delegate('#votesystem', 'change', function (event) {

			var voteTdesc = $("#votesystem :selected").attr("title");
			if( voteTdesc.length > 0 ){
				$("#voteTypeDescription").show();
				$("#voteTypeDescriptionInput").val(voteTdesc);
			}

		});

    $('.modal').not("#confirm-box").on('hidden.bs.modal', function(event) {
        /*  $(this).removeData('bs.modal');
		 $(this).find('.modal-content').html(''); */
    })


    $.save_assignment = false;
	$('#modal_task_assignment').on('hidden.bs.modal', function(event) {
         $(this).removeData('bs.modal');
		 $(this).find('.modal-content').html('');
         if($.save_assignment) {
            $.ajax({
                url: $js_config.base_url + 'entities/task_assignment_button',
                type: 'POST',
                dataType: 'JSON',
                data: {'element_id': $.save_assignment},
                success: function(response) {
                    $.save_assignment = false;
                    $('.eassignment').html(response);
					var remote_url = $js_config.base_url + 'entities/element_options_partial';
						params = { element_id: $js_config.element_id };

						 $.ajax({
							url: remote_url,
							type: "POST",
							data: $.param(params),

							success: function(response) {

									 $('.el_ops').html(response);
									 $('.tooltip').hide();

							}

						})

						var remote_url_assign_image = $js_config.base_url + 'entities/task_assignment_image';
						params_image = { element_id: $js_config.element_id, current_user_id : $js_config.USER.id };

						$.load_team();
						$.task_progress_bar();

						$.ajax({
							url: remote_url_assign_image,
							type: "POST",
							data: $.param(params_image),
							// datatype: 'json',
							success: function(responses) {

									 $('.assign_div').html(responses);
							}

						})



                }
            })
         }
	})





    $('body').delegate('.delete_resource', 'click', function(event) {
        event.preventDefault();

        var $that = $(this),
            data = $that.data(),
            data_id = data.id,
            remote = data.remote,
            message = data.msg,
            parent = data.parent,
            $row = $that.parents(".row:first");

        var type = data.type,
            params = {},
            $history_row = $();
		var dy_title = '';
        if(type == 'link') {
            dy_title = 'Delete Link';
			params = { link_id: data_id };
            $history_row = $('#historylink_'+data_id);
        }
        else if(type == 'note'){
			dy_title = 'Delete Note';
            params = { note_id: data_id };
            $history_row = $('#historynote_'+data_id);
        }
        else if(type == 'doc'){
			dy_title = 'Delete Document';
            params = { doc_id: data_id };
            $history_row = $('#historydocument_'+data_id);
        }
        else if(type == 'mindmap'){
			dy_title = 'Delete Mind Map';
            params = { mind_map_id: data_id };
            $history_row = $('#historymindmap_'+data_id);
        }
        else if(type == 'decision'){
			dy_title = 'Delete Decision';
            params = { decision_id: data_id };
            $history_row = $('#historydecision_'+data_id);
        }

        BootstrapDialog.show({
            title: '<h3 class="h3_style">'+dy_title+'</h3>',
            message: message,
            type: BootstrapDialog.TYPE_DANGER,
            draggable: false,
            buttons: [{
                label: ' Delete',
                cssClass: 'btn-success',
                autospin: false,
                action: function(dialogRef) {
                    var $button = this;
                    $button.disable();
                    $button.spin();
                    dialogRef.setClosable(false);
                    $.when(
                        $.ajax({
                            url: remote,
                            type: "POST",
                            data: $.param(params),
                            dataType: "JSON",
                            global: false,
                            success: function(response) {
                                if (response.success) {
                                    $row.effect("size", {
                                        to: { height: 0  }
                                    }, 400, function() {
                                        $row.remove();
                                        if($history_row.length > 0){
                                            $history_row.remove();
                                        }
                                        var $data_catcher = $(parent).find('.data_catcher');
                                        if ($data_catcher.find('.row').length <= 0){
                                            $data_catcher.next('.ajax-pagination:first').remove();
                                        }
                                        if(type != 'decision'){
                                            $.update_counters(type);
                                        }
                                        else{
                                            window.location.reload(true);
                                        }
                                    });
                                }
                            }
                        })
                    ).then(function(data, textStatus, jqXHR) {
                        dialogRef.close();
                    })
                }
            },
            {
                label: 'Cancel',
                cssClass: 'btn-danger',
                action: function(dialogRef) {
                    dialogRef.close();
                }
            }],
            onshow: function(dialogRef){
                //console.log('show')
                $('body').css('padding-right', 0);
                $('html').addClass('modal-open');
            },
            onhide: function(dialogRef){
                //console.log('hide')
                $('body').removeAttr('style');
                $('html').removeClass('modal-open');
            }
        });
    })


	// Dependancy Icon will be update after update or save ====
	$.dependancy = $({});
	$.dependancy_saved = false;
	$('#modal_task_status').on('show.bs.modal', function(event){
		$.dependancy = $(event.relatedTarget);
	})
	$('#modal_task_status1').on('hide.bs.modal', function(event){
		if($.dependancy_saved){
			var element_id = $js_config.currentElementId;
			var remote_url = $js_config.base_url + 'entities/dependancy_status_count';
			params = { element_id: element_id };

				$.ajax({
					url: remote_url,
					type: "POST",
					data: $.param(params),
					global: false,
					success: function(response) {
						 $.dependancy.parent().html(response);
						 $.dependancy = $({});
					}
				})

		}
		$.dependancy_saved = false;


	})

	$('#modal_task_status').on('hidden.bs.modal', function (event) {
		$(this).removeData('bs.modal');
		$(this).find('.modal-content').html('');

		var remote_url = $js_config.base_url + 'entities/element_options_partial';
		params = { element_id: $js_config.element_id };
		//console.log('start');
		 $.ajax({
			url: remote_url,
			type: "POST",
			data: $.param(params),
			//global: true,
			//dataType:'json',
			success: function(response) {
				//console.log('start1');
				//console.log(response);
				//if( response){
					//$('.el_ops').html('');
					 $('.el_ops').html(response);
					  $('.tooltip').hide();

					  var remote_url_assign_image = $js_config.base_url + 'entities/task_assignment_image';
						params_image = { element_id: $js_config.element_id, current_user_id : $js_config.USER.id };

						$.ajax({
							url: remote_url_assign_image,
							type: "POST",
							data: $.param(params_image),
							 //datatype: 'json',
							success: function(responses) {

									 $('.assign_div').html(responses);
                                     $('.popover').hide();

							}

						})


					//alert('done');
				//}
			}

			})

		/* $.ajax({
			url: remote_url,
			type: "POST",
			data: $.param(params),
			global: false,
			dataType:'json',
			success: function(response) {
				if( response.content == 1 ){
					$(".convertbtn").hide().attr('style','display:none !important;');
					$(".changesignoff").show().attr('style','display:block !important;');

					if($(".changesignoff").length < 1){
					$('<a class="btn   btn-app signing-off  tipText  changesignoff" title="Sign Off" data-toggle="modal" data-target="#signoff_comment_box" data-value="1" data-remote="'+$js_config.base_url+'entities/tasks_signoff/'+$js_config.element_id+'" data-id="'+$js_config.element_id+'" ><i class="fa fa-sign-out"></i> Sign Off</a>').insertAfter( ".convertbtn" );
					}

				} else {
					$(".convertbtn").show().attr('style','display:block !important;');;
					$(".changesignoff").hide().attr('style','display:none !important;');
					if($(".convertbtn").length < 1){
					$('<a data-original-title="Sign-off tasks" class="btn   btn-app signing-off tipText convertbtn" data-target="#modal_signofftask" data-toggle="modal" data-remote="'+$js_config.base_url+'entities/tasks_signoff/'+$js_config.element_id+'" data-id="'+$js_config.element_id+'" ><i class="fa fa-sign-out"></i> Sign Off</a>').insertAfter( ".convertbtn" );
					}

				}
			}
		}) */


	});

	// Task cost Icon will be update if cost define in element ====
	$.taskCost = $({});
	$.taskCostSaved = false;
	$('#modal_cost_status').on('show.bs.modal', function(event){
		$.taskCost = $(event.relatedTarget);
		//console.log($.taskCost)
	})
	$('#modal_cost_status1').on('hide.bs.modal', function(event){
		//if($.taskCostSaved){
			//console.log('clicked', $.taskCost);
			var element_id = $js_config.currentElementId;
			var remote_url = $js_config.base_url + 'entities/cost_status_count';
			params = { element_id: element_id };

				$.ajax({
					url: remote_url,
					type: "POST",
					data: $.param(params),
					global: false,
					success: function(response) {
						 //console.log($.taskCost.parent());
						 $.taskCost.parent().html(response);
						 $.taskCost = $({});
					}
				})

		//}
		//$.taskCostSaved = false;
	})


	/*$('#signoff_comment_box').on('hide.bs.modal', function(event) {
        var $this = $(this);

		$(this).find('.modal-body').find('#signoff_comment').css('height': '0');


		$(this).find('.modal-body').find('#signoff_comment').val('');
		$(this).find('.modal-body').find('#task_evidence').val('');

    })*/

	 /* $('#signoff_comment_show').on('hidden.bs.modal', function(event) {

		$(this).removeData('bs.modal');
        $(this).find('.modal-content').html('');

		$('.reopen-signoff').tooltip({
			 container: 'body', placement: 'auto', 'template': '<div class="tooltip reopen-signoffer" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner" style="text-transform:none !important;"> </div></div>'
		})

    }) */


	/* $('.reopen-signoff').tooltip({
			 container: 'body', placement: 'auto', 'template': '<div class="tooltip reopen-signoffer" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner" style="text-transform:none !important;"> </div></div>'
	}) */

	$('#signoff_comment_show').on('hidden.bs.modal', function(event) {
		$(this).removeData('bs.modal');
		$(this).find('.modal-content').html('');
		//$(".reopen-signoff").tooltip('hide');
	})

    $('#modal_signofftask').on('hidden.bs.modal', function(event) {
        $(this).removeData('bs.modal');
        $(this).find('.modal-content').html('');
        //console.log($(this).parents(), "==88888888==");
    })

    $.save_confidence = false;
    $.save_effort = false;
    $.effort_user = false;
	$('#element_level').on('hidden.bs.modal', function(event) {
		$(this).removeData('bs.modal');
		$(this).find('.modal-content').html('');
		if($.save_confidence){
            $.save_confidence = false;
            $.load_team();
            $.ajax({
                url: $js_config.base_url + 'entities/update_confidence/' + $js_config.currentElementId,
                type: 'POST',
                data: {},
                success: function(response){
                    $('.confidence-col').html(response);
                }
            })
        }
        if($.save_effort){
            $.save_effort = false;
            $.load_team($.effort_user);
            $.effort_user = false;
            $.task_progress_bar();
			$.task_icons_reload();
        }
	})


})
</script>
<div class="modal modal-success fade" id="modal_task_status" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-radius-top">

		</div>
    </div>
</div>

<div class="modal modal-success fade" id="modal_cost_status" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-radius-top">

		</div>
    </div>
</div>

<!--<div class="modal modal-success fade" id="modal_large" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content"></div>
	</div>
</div>-->


<div class="modal modal-success fade" id="confirm-boxs" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content border-radius-top">
            <div class="modal-header border-radius-top" id="modal_header"> <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>Confirmation </div>

            <div class="modal-body" id="modal_body"></div>

            <div class="modal-footer" id="modal_footer">
                <a class="btn btn-success btn-ok btn_progress btn_progress_wrapper" id="s_off_yes">
                    <div class="btn_progressbar"></div>
                    <span class="text">Yes</span>
                </a>
                <button type="button" id="s_off_no" class="btn btn-danger" data-dismiss="modal">No</button>
            </div>

            <div class="modal-footer" id="modal_footer_2" style="display: none;">
                <a class="btn btn-success btn-ok" id="confirm-yes">Yes</a>
                <a class="btn btn-danger " id="confirm-no" data-dismiss="modal">No</a>
            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="confirm-box_new" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content border-radius-top">
            <div class="modal-header border-radius-top" id="modal_header"> </div>

            <div class="modal-body" id="modal_body"></div>

            <div class="modal-footer" id="modal_footer">
                <a class="btn btn-success btn-ok btn_progress btn_progress_wrapper" id="s_off_yes">
                    <div class="btn_progressbar"></div>
                    <span class="text">Yes</span>
                </a>
                <button type="button" id="s_off_no" class="btn btn-danger" data-dismiss="modal">No</button>
            </div>

            <div class="modal-footer" id="modal_footer_2" style="display: none;">
                <a class="btn btn-success btn-ok" id="confirm-yes">Yes</a>
                <a class="btn btn-danger " id="confirm-no" data-dismiss="modal">No</a>
            </div>

        </div>
    </div>
</div>

<div class="modal modal-danger fade" id="modal_signofftask" tabindex="-1"  >
	<div class="modal-dialog">
		<div class="modal-content"></div>
	</div>
</div>

<div class="modal modal-danger fade" id="signoff_comment_box" tabindex="-1" >
    <div class="modal-dialog">
        <div class="modal-content border-radius">

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>

<div class="modal modal-danger fade" id="signoff_comment_show" tabindex="-1" >
    <div class="modal-dialog">
        <div class="modal-content border-radius">

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>


<div class="modal modal-success fade" id="element_level" tabindex="-1" >
    <div class="modal-dialog">
        <div class="modal-content ">

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>



<style>
.content-header.clearfix > h1{ width:70%;}
.chars_error {
    color: #dd4b39;
    font-size: 11px;
}

#successFlashMsg p{ margin:5px 0px;}


.modal-danger#modal_signofftask .modal-header {
    background-color: #d9534f !important;
}
.no-scroll {
        overflow: hidden;
    }
    .cd-tabs-content.clearfix {
        overflow-x: hidden;
        overflow-y: overlay;
    }

    .row-fluid.Editor-container #menuBarDiv.row-fluid .btn-group {
        display: unset !important;
    }

</style>
<script type="text/javascript">
    $(function(){

        /************* SORTING *************/
        $('body').on('click', '.sort_order', function(event) {
            var $that = $(this),
                order = $that.data('order') || 'asc',
                type = $that.data('type'),
                coloumn = $that.data('by');

            if( order == 'desc' ){
                $(this).attr('data-order', 'asc');
            }
            else{
                $(this).attr('data-order', 'desc');
            }


            $(this).parents('.tab-pane:first').find('.sort_order.active').not(this).removeClass('active');

            $that.addClass('active');
            $('.tooltip').remove();

            var data = {task_id: $js_config.currentElementId, project_id: $js_config.currentProjectId, order: order, type: type, coloumn: coloumn }
            // console.log(data);
            // return;

            $.ajax({
                url: $js_config.base_url + 'entities/filter_team',
                type: 'POST',
                // dataType: 'json',
                data: data,
                success: function(response) {

                    // $that.parents('.tab-pane:first').find("#paging_offset").val(0);

                    if( order == 'asc' ){
                        $that.data('order', 'desc');
                    } else {
                        $that.data('order', 'asc');
                    }
                    // console.log(response)
                    $that.parents('.tab-pane:first').find('.list-wrapper').html(response);
                    $('.tooltip').remove();

                }
            })
        })

        ;($.load_team = (user_id) => {
            var $that = $('.sort_order.active'),
                order = $that.data('order') || 'asc',
                type = $that.data('type'),
                coloumn = $that.data('by');

            var user_id = user_id || 0,
            order = ( order == 'asc' ) ? 'desc' : 'asc';

            var data = {user_id: user_id, task_id: $js_config.currentElementId, project_id: $js_config.currentProjectId, order: order, type: type, coloumn: coloumn}
            $.ajax({
                url: $js_config.base_url + 'entities/filter_team',
                type: 'POST',
                // dataType: 'json',
                data: data,
                success: function(response) {
                    if(user_id > 0 && user_id != 0 && user_id != undefined){
                        $('.team-data.list-wrapper .ps-data-row[data-user="'+user_id+'"]').html(response);
                        $('.tooltip').remove();
                    }
                    else{
                        $('.team-data.list-wrapper').html(response);
                        $('.tooltip').remove();
                        $('.total-data').html('('+$('.team-data.list-wrapper .ps-data-row').length+')')
                    }
                }
            })
        })();
        $('.total-data').html('('+$('.team-data.list-wrapper .ps-data-row').length+')');

        $.task_progress_bar = () => {

            var data = {task_id: $js_config.currentElementId}
            $.ajax({
                url: $js_config.base_url + 'entities/task_progress_bar',
                type: 'POST',
                data: data,
                success: function(response) {
                    $('.task-progress-bar').html(response);
                    $('.tooltip').remove();

                }
            })
        }

		$.task_icons_reload = () => {

            var remote_url = $js_config.base_url + 'entities/element_options_partial';
    			params = { element_id: $js_config.element_id };

			$.ajax({
				url: remote_url,
				type: "POST",
				data: $.param(params),

				success: function(response) {

						 $('.el_ops').html(response);
						 $('.tooltip').hide();

				}

			})
        }

        $('html,body').scrollTop(0).addClass('no-scroll');
        // RESIZE MAIN FRAME
        ($.adjust_resize = function(){
            $('.team-data.list-wrapper').animate({
                minHeight: (($(window).height() - $('.team-data.list-wrapper').offset().top) ) - 17,
                maxHeight: (($(window).height() - $('.team-data.list-wrapper').offset().top) ) - 17
            }, 1 )
            $('.cd-tabs-content.clearfix').animate({
                minHeight: (($(window).height() - $('.cd-tabs-content.clearfix').offset().top) ) - 17,
                maxHeight: (($(window).height() - $('.cd-tabs-content.clearfix').offset().top) ) - 17
            }, 1 )
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

        $("#task-header-tabs").on('show.bs.tab', function(e){
            const fix = setInterval( () => { window.dispatchEvent(new Event('resize')); }, 300 );
            setTimeout( () => clearInterval(fix), 1000);
        })
        /*$("#task-header-tabs").on('shown.bs.tab', function (e) {
            activeTab = $(e.target);
            if(activeTab.is($('a[data-type="team"]')) || activeTab.is($('a[data-type="assets"]'))){

                $('html,body').scrollTop(0).addClass('no-scroll');
            }
            else{
                $('html,body').removeClass('no-scroll');
            }
        })*/

        $('.sort_order').tooltip({
            placement: 'top',
            container: 'body'
        })
//
    })
</script>