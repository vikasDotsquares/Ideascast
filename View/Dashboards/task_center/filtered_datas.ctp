<style type="text/css">
	.small-caps.tipText+.tooltip>.tooltip-inner {
	    text-transform: initial !important;
	}

	.projects-line-wrapper {
		opacity:1 !important;
	}
	.no-more-data{
		display:none;
	}
	#el-status-dd.open ul.dropdown-menu>li a {
		/* color:#fff !important; */
	}
	#people-dd.open ul.dropdown-menu>li.sel_assign a{
		color:#f00 !important;
	}

	.multiselect-container>li {
		padding: 5px 0;
	}

	#el-status-dd ul li a{
		color:#fff !important;
	}

	#el-status-dd.open ul.dropdown-menu>li a {
		padding: 7px 8px 7px 15px;
	}

	#status-dropdown li.sel-filter a i.fa-check {
		float: right;
		margin-right: 0px;
	}

	#people-dd .dropdown-menu {
		min-width:290px;
	}

	.user-name-n {
		padding-left: 8px;
		flex-grow: 1;
		text-overflow: ellipsis;
		overflow: hidden;
		white-space: nowrap;
		text-align:left;
	}
	.data-block .thumb {
		width: 95%;
	}
	/* .filtered_data, .list-group{
		z-index:0;
	} */
</style>
<?php
$current_org = $this->Permission->current_org();
$current_user_id = $this->Session->read('Auth.User.id');
$perPageWspLimit = 2;
$currentWspPage = 0;

$elementStartSortClsaa = '';
$elementEndSortClsaa = '';
$workspaceStartSortClsaa = '';
$workspaceEndSortClsaa = '';

if( isset($dateStartSorttype) && !empty($dateStartSorttype) && $dateStartSorttype == 'elements' ){
	$elementStartSortClsaa = 'usedSort';
}
if( isset($dateEndSorttype) && !empty($dateEndSorttype) && $dateEndSorttype == 'elements' ){
	$elementEndSortClsaa = 'usedSort';
}

if( isset($dateStartSorttype) && !empty($dateStartSorttype) && $dateStartSorttype == 'workspaces' ){
	$workspaceStartSortClsaa = 'usedSort';
}
if( isset($dateEndSorttype) && !empty($dateEndSorttype) && $dateEndSorttype == 'workspaces' ){
	$workspaceEndSortClsaa = 'usedSort';
}
$assigned_tiptext = '';
if( isset($assigned_reaction) && !empty($assigned_reaction) ){

	if($assigned_reaction == 4){
		$assigned_tiptext = "Unassigned";
	} else if ($assigned_reaction == 5){
		$assigned_tiptext = "Assigned And Schedule Acceptance Pending";
	} else if ($assigned_reaction == 1){
		$assigned_tiptext = "Assigned And Schedule Accepted";
	} else if ($assigned_reaction == 2){
		$assigned_tiptext = "Assigned And Schedule Not Accepted";
	} else if ($assigned_reaction == 3){
		$assigned_tiptext = "Unassigned And Disengaged";
	}
}

?>
<div class="buttons-container">
    <div class="col-xs-3 col-md-3 col-lg-3 col-data col-data-1">
        <div class="panel panel-default">
            <div class="panel-heading people-section">
                <span class="people-filter">

					Assigned To
            	</span>
                <div class="btn-group pull-right">
                    <span href="#" title="<?php echo $assigned_tiptext;?>" class="btn btn-xs btn-control dropdown tipText disabled <?php if( isset($assigned_reaction) && !empty($assigned_reaction) ){?>box-shadow<?php } ?>" id="people-dd">
					    <span href="#" class="dropdown-toggle" id="people-drop" data-toggle="dropdown" aria-controls="people-dropdown" aria-expanded="false">Status <span class="fa fa-times bg-red clear_people_filter"></span></span>
		                <ul class="dropdown-menu" aria-labelledby="people-drop" id="assign_status_dropdown">
		                    <li class="assigned_status <?php echo ( isset($assigned_reaction) && !empty($assigned_reaction) && $assigned_reaction == 4 ) ? "sel_assign" : ""; ?>" data-status="assigned4"  ><a id="dropdown0-tab" aria-controls="dropdown0" data-text="Unassigned" ><i class="fa fa-times text-black icon-task-assign tipText"></i> Unassigned</a></li>
		                    <li class="assigned_status <?php echo ( isset($assigned_reaction) && !empty($assigned_reaction) && $assigned_reaction == 5 ) ? "sel_assign" : ""; ?>" data-status="assigned0" ><a id="dropdown1-tab" aria-controls="dropdown1" data-text="Awaiting Response" ><i class="fa no-reaction text-black icon-task-assign task-assigned"></i> Assigned And Schedule Acceptance Pending</a></li>
		                    <li class="assigned_status <?php echo ( isset($assigned_reaction) && !empty($assigned_reaction) && $assigned_reaction == 1 ) ? "sel_assign" : ""; ?>" data-status="assigned1" ><a id="dropdown2-tab" aria-controls="dropdown2" data-text="Schedule Accepted" ><i class="fa accepted text-black icon-task-assign task-assigned"></i> Assigned And Schedule Accepted</a></li>
		                    <li class="assigned_status <?php echo ( isset($assigned_reaction) && !empty($assigned_reaction) && $assigned_reaction == 2 ) ? "sel_assign" : ""; ?>" data-status="assigned2"   ><a id="dropdown3-tab" aria-controls="dropdown3" data-text="Schedule Not Accepted" ><i class="fa not-accepted text-black icon-task-assign task-assigned"></i> Assigned And Schedule Not Accepted</a></li>
		                    <li class="assigned_status <?php echo ( isset($assigned_reaction) && !empty($assigned_reaction) && $assigned_reaction == 3 ) ? "sel_assign" : ""; ?>" data-status="assigned3"   ><a id="dropdown4-tab" aria-controls="dropdown4" data-text="Disengaged" ><i class="fa disengaged  text-black icon-task-assign task-assigned"></i> Unassigned and Disengaged</a></li>
		                </ul>
	                </span>
					<a class="btn btn-xs btn-control tipText <?php if( isset($assign_sorting) && !empty($assign_sorting) ) { echo 'alphaSelected'; } ?> disabled assign_alphabetical " title="Alphabetical Sort" data-sorted="<?php if( isset($assign_sorting) && !empty($assign_sorting) && $assign_sorting == 'asc' ) { echo 'desc'; } else { echo 'asc'; }?>" data-parent=".filter_people_by_project" data-type="people"><?php if( isset($assign_sorting) && !empty($assign_sorting) && $assign_sorting == 'asc' ) { echo 'ZA'; } else { echo 'AZ'; }?></a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xs-3 col-md-3 col-lg-3 col-data col-data-2">
        <div class="panel panel-default">
            <div class="panel-heading task-section">Task
                <input type="text" class="select_dates" style="opacity: 0; width: 0; height: 0;" readonly="readonly">
                <div class="btn-group pull-right">

                    <span href="#" class="btn btn-xs btn-control dropdown disabled" id="el-status-dd">
					    <span href="#" title="" class="dropdown-toggle " id="status-drop" data-toggle="dropdown" aria-controls="status-dropdown" aria-expanded="false"><?php if( isset($filter_task_staus) && !empty($filter_task_staus) ){
							if( count($filter_task_staus) <= 2 ){
							echo implode(",",$filter_task_staus);
							} else {
								echo "Status (".count($filter_task_staus).")";
							}
						} else {?><span>Status</span><?php } ?> <span class="fa fa-times bg-red clear_status_filter"></span></span>
	                    <ul class="dropdown-menu multiple" aria-labelledby="status-drop" id="status-dropdown">
	                        <li class=" <?php if( isset($filter_task_staus) && in_array("NON",$filter_task_staus) ){ ?>sel-filter <?php }else{ ?>rem-filter<?php } ?>"><a id="dropdown1-tab" aria-controls="dropdown1" data-text="NON" data-status="not_spacified">NON <i class="fa fa-check"></i><input type="checkbox" name="non" value="NON" <?php if(isset($filter_task_staus) && in_array("NON",$filter_task_staus) ){ echo "checked='checked'"; } ?>></a></li>
	                        <li class="<?php if(isset($filter_task_staus) &&  in_array("PND",$filter_task_staus) ){ ?>sel-filter <?php }else{ ?>rem-filter<?php } ?>"><a id="dropdown1-tab" aria-controls="dropdown1" data-text="PND" data-status="not_started">PND <i class="fa fa-check"></i><input type="checkbox" name="pnd" value="PND" <?php if(isset($filter_task_staus) && in_array("PND",$filter_task_staus) ){ echo "checked='checked'"; } ?>></a></li>
	                        <li class="<?php if(isset($filter_task_staus) &&  in_array("PRG",$filter_task_staus) ){ ?>sel-filter<?php }else{ ?>rem-filter<?php } ?>" ><a id="dropdown1-tab" aria-controls="dropdown1" data-text="PRG" data-status="progress">PRG <i class="fa fa-check"></i><input type="checkbox" name="prg" value="PRG" <?php if(isset($filter_task_staus) &&  in_array("PRG",$filter_task_staus) ){ echo "checked='checked'"; } ?>></a></li>
	                        <li class="<?php if(isset($filter_task_staus) &&  in_array("OVD",$filter_task_staus) ){ ?>sel-filter<?php }else{ ?>rem-filter<?php } ?>" ><a id="dropdown1-tab" aria-controls="dropdown1" data-text="OVD" data-status="overdue">OVD <i class="fa fa-check"></i><input type="checkbox" name="ovd" value="OVD" <?php if(isset($filter_task_staus) &&  in_array("OVD",$filter_task_staus) ){ echo "checked='checked'"; } ?>></a></li>
	                        <li class="<?php if(isset($filter_task_staus) &&  in_array("CMP",$filter_task_staus) ){ ?>sel-filter<?php }else{ ?>rem-filter<?php } ?>" ><a id="dropdown1-tab" aria-controls="dropdown1" data-text="CMP" data-status="completed">CMP <i class="fa fa-check"></i><input type="checkbox" name="cmp" value="CMP"  <?php if(isset($filter_task_staus) &&  in_array("CMP",$filter_task_staus) ){ echo "checked='checked'"; } ?> ></a></li>
	                    </ul>
                    </span>
                    <a class="btn btn-xs btn-control tipText calendar_trigger disabled" title="Select Dates" data-type="element"><i class="fa fa-calendar-check-o"></i></a>
                    <a class="btn btn-xs btn-control alphabetical tipText <?php if( isset($element_sorting) && !empty($element_sorting) ) { echo 'alphaSelected'; } ?> disabled" title="Alphabetical Sort" data-sorted="<?php if( isset($element_sorting) && !empty($element_sorting) && $element_sorting == 'asc' ) { echo 'desc'; } else { echo 'asc'; }?>" data-parent=".filter_task_by_project" data-type="element"><?php if( isset($element_sorting) && !empty($element_sorting) && $element_sorting == 'asc' ) { echo 'ZA'; } else { echo 'AZ'; }?></a>
                    <a class="btn btn-xs btn-control start_date_sort sort tipText disabled <?php echo $elementStartSortClsaa;?>" title="Start Date First" data-parent=".filter_task_by_project" data-type="element"><i class="fa fa-chevron-circle-up"></i></a>
                    <a class="btn btn-xs btn-control end_date_sort sort tipText disabled <?php echo $elementEndSortClsaa;?>" title="End Date First" data-parent=".filter_task_by_project" data-type="element"><i class="fa fa-chevron-circle-down"></i></a>
                </div>
				<?php
				if( isset($selected_dates) && !empty($selected_dates) ){
				?><div class="selected_dates" style="display: block;"><?php $selString = explode(" - ",$selected_dates);

				echo $startSelDate = date('d M, Y',strtotime($selString[0]));
				echo ' - ';
				//echo $selString[1]."-end date";
				if( isset($selString[1]) && !empty($selString[1]) && $selString[1] != 'NaN-undefined-NaN' ){
					echo $endSelDate = date('d M, Y',strtotime($selString[1]));
				} else {
					echo $startSelDate;
				}
				//echo trim($selected_dates); ?><i class="fa fa-times pull-right empty-dates" style=" "></i></div>
				<?php } else { ?>
                <div class="selected_dates" >
                    <i class="fa fa-times pull-right" style=""></i>
                </div>
				<?php } ?>

            </div>
        </div>
    </div>
    <div class="col-xs-3 col-md-3 col-lg-3 col-data col-data-3">
        <div class="panel panel-default">
            <div class="panel-heading wsp-section">Workspace
                <div class="btn-group pull-right">
                    <a class="btn btn-xs btn-control wsp_alphabetical tipText <?php if( isset($wsp_sorting) && !empty($wsp_sorting) ) { echo 'alphaSelected'; } ?> disabled" title="Alphabetical Sort" data-sorted="<?php if( isset($wsp_sorting) && !empty($wsp_sorting) && $wsp_sorting == 'asc' ) { echo 'desc'; } else { echo 'asc'; }?>" data-parent=".filter_wsp_by_project" data-type="workspace"><?php if( isset($wsp_sorting) && !empty($wsp_sorting) && $wsp_sorting == 'asc' ) { echo 'ZA'; } else { echo 'AZ'; }?></a>
                    <a class="btn btn-xs btn-control start_date_sort sort tipText disabled  <?php echo $workspaceStartSortClsaa;?>" title="Start Date First" data-parent=".filter_wsp_by_project" data-type="workspace"><i class="fa fa-chevron-circle-up"></i></a>
                    <a class="btn btn-xs btn-control end_date_sort sort tipText disabled  <?php echo $workspaceEndSortClsaa;?>" title="End Date First" data-parent=".filter_wsp_by_project" data-type="workspace"><i class="fa fa-chevron-circle-down"></i></a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xs-3 col-md-3 col-lg-3 col-data col-data-4">
        <div class="panel panel-default">
            <div class="panel-heading project-section">Task Team Members</div>
        </div>
    </div>
</div>
<?php
if(empty($this->request->params['named']['status']) && empty($this->request->params['named']['assigned']) && empty($this->request->params['pass'])  ){
	$bb = 'block';
}else{
	$bb = 'none';
}
?>
<div class="col-sm-12 projects-line-wrapper" style="display: <?php echo $bb; ?>;">
    <!-- <div class="no-result">No Result</div> -->
    <?php
	$element_keys = $element_users = [];
	// Thai filter user data is coming from User filter DD
	$userid = ( isset($filter_users[0]) && !empty($filter_users[0]) )? $filter_users[0] : 0;
	/********************** GET USER DATA ***************************/
	$userDetail = get_user_data( $userid );

	$user_image = SITEURL . 'images/placeholders/user/user_1.png';
	$user_name = 'Not Available';
	$job_title = 'Not Available';
	$html = '';
	if(isset($userDetail) && !empty($userDetail)) {

		$user_name = htmlentities($userDetail['UserDetail']['first_name'],ENT_QUOTES) . ' ' . htmlentities($userDetail['UserDetail']['last_name'],ENT_QUOTES);
		$profile_pic = $userDetail['UserDetail']['profile_pic'];
		$job_title = htmlentities($userDetail['UserDetail']['job_title'],ENT_QUOTES);

		if( $userid != $current_user_id && !empty($prjid) ) {
			$html = CHATHTML($userid, $prjid);
		}

		if(!empty($profile_pic) && file_exists(USER_PIC_PATH.$profile_pic)) {
			$user_image = SITEURL . USER_PIC_PATH . $profile_pic;
		}
	}
	/*****************************************************************/

	if (isset($filter_projects) && !empty($filter_projects)) {
		$projects_count = count($filter_projects);
		foreach ($filter_projects as $pvalue) {
			$defaultVal = 1;

			 $project_premission = $pvalue['user_permissions'];
			 $project_detail = $pvalue['projects'];
			 $prjid = $pvalue['projects']['id'];

			/********************************* GET PROJECT DATA *****************************************/

			$project_permit_type = false;

			if($project_premission['role'] =='Creator'){
				$projectType = $cky = "m_project";
			}
			if($project_premission['role'] =='Owner'){
				$projectType = $cky = "r_project";
			}
			if($project_premission['role'] =='Group Owner'){
				$projectType = $cky = "g_project";
			}
			if($project_premission['role'] =='Group Sharer'){
				$projectType = $cky = "g_project";
			}
			if($project_premission['role'] =='Sharer'){
				$projectType = $cky = "r_project";
			}

			if($project_premission['role'] =='Creator' || $project_premission['role'] =='Owner' || $project_premission['role'] =='Group Owner' ){
				$project_permit_type = true;
			}

			$project_title = htmlentities($project_detail['title']);
			$project_start_date = $project_detail['start_date'];
			$project_end_date = $project_detail['end_date'];
			$project_color_code = $project_detail['color_code'];

			$prj_start_date = (isset($project_start_date) && !empty($project_start_date)) ? $this->Wiki->_displayDate($project_start_date,'m-d-Y') : false;
			$prj_end_date = (isset($project_end_date) && !empty($project_end_date)) ? $this->Wiki->_displayDate($project_end_date,'Y-m-d') : false;
			$project_cc = str_replace("panel", "bg", $project_color_code);
	?>
	<div class="line-header " style="opacity:0;" data-target="#project_row_<?php echo $prjid; ?>">
	    <span class="project-name"><span class="project-name-ellipsis"><?php echo html_entity_decode($project_title); ?>&nbsp;</span> <span class="task-project-name-date">(<b>Start:</b> <?php echo ($prj_start_date) ? $this->Wiki->_displayDate($project_start_date,'d M, Y') : 'N/A'; ?>  <b>End:</b> <?php echo ($prj_end_date) ? $this->Wiki->_displayDate($project_end_date,'d M, Y') : 'N/A'; ?>)</span></span>
	    <div class="pull-right taskuser_sec-middle">

			<div class="taskuser_select">
				<select data-prjid="<?php echo $prjid ?>" id="element_task_type" name="element_task_type_list" class="element_task_type unavailablett" placeholder="Select Task Type" multiple="multiple">
				<?php
				$tasktype_lists = $this->Permission->projectTaskType($prjid,$userid);
				if( isset($tasktype_lists) && !empty($tasktype_lists) ) {
					foreach($tasktype_lists as $id => $typelist){ ?><option value="<?php echo $typelist['project_element_types']['ele_type_id'];?>"><?php echo $typelist['project_element_types']['ele_type_title'];?></option>
				<?php	}
					}
				?>
				</select>
				<a href="javascript:void(0);" class="btn btn-danger btn-sm tasktype-clear tipText" title="Clear Task Type" style="line-height: 1.8;">
					<i class="fa fa-times "></i>
				</a>
			</div>


            <a data-original-title="Project Schedule" class="tipText btn btn-default btn-xs" title="Gantt" href="<?php echo Router::Url(array("controller" => "users", "action" => "event_gantt", $cky => $prjid ), true); ?>"><i class="fa fa-calendar"></i></a>
            <?php /* if($project_permit_type) { ?>
            	<a data-updated="<?php echo $prjid; ?>" data-type="project" data-toggle="modal" data-target="#modal_small" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "task_list_project_date", $prjid ), true); ?>" data-original-title="Project Schedule" class="tipText btn btn-default btn-xs" title="Project Schedule" href="#">SH</a>
            <?php } */ ?>
			<?php /* if($projects_count > 1){ ?>
	    		<a href="#" class="btn btn-default btn-xs show_more tipText" title="Expand"><i class="fa"></i></a>
	    	<?php } */ ?>
	    	<a href="<?php echo Router::Url(array("controller" => "projects", "action" => "index", $prjid ), true); ?>" class="btn btn-default btn-xs tipText" title="Open Project"><i class="fa fa-folder-open"></i></a>
	    </div>
	</div>

		 <?php if($projects_count > 1){ ?>
                <style> .scrollable{max-height:  350px; }</style>
		<?php
				}else{ ?>
				   <style> .scrollable{ max-height: 645px !important; }</style>
			<?php   }

		 ?>


		<div style="opacity:0;" class="projects-line <?php if($projects_count >= 1){ ?>scrollable<?php } ?>"       id="project_row_<?php echo $prjid; ?>" data-project-title="<?php echo htmlentities($project_title); ?>" <?php if($prj_start_date) { ?> data-project-start-date="
            <?php echo $prj_start_date; ?>"
            <?php } ?>
            <?php if($prj_end_date) { ?> data-project-end-date="
            <?php echo $prj_end_date; ?>"
            <?php } ?> data-project="<?php echo $prjid; ?>" <?php if($projects_count == 1){ ?> style="max-height:628px;" <?php } ?> >
            <div class="no-result">No Results</div>
            <?php

		$projects_data = array();
		$totalElementcount = 0;
		$page = 1;
		$pcount = null;
		if( $projects_count <= 1  ){
			$pcount = 1;
		}

		$projects_data = $this->Permission->task_center_filtered_data($prjid, $userid,'','',$assigned_user_ids,$assigned_reaction,$filter_task_staus,$assign_sorting,$element_sorting,$wsp_sorting,$selected_dates,$element_title,$dateStartSorttype,$dateEndSorttype,$element_task_type);

		//pr($projects_data);

		$projects_data_count = $this->Permission->task_center_filtered_data_count($prjid, $userid,'','',$assigned_user_ids,$assigned_reaction,$filter_task_staus,$assign_sorting,$element_sorting,$wsp_sorting,$selected_dates,$element_title,$dateStartSorttype,$dateEndSorttype,$element_task_type);

		if( isset($projects_data_count) && !empty($projects_data_count[0][0]["count(user_permissions.element_id)"]) ){
			$totalElementcount = $projects_data_count[0][0]["count(user_permissions.element_id)"];
		}
		//echo $totalElementcount."=totalElementcount=";
		 ?>
		 <input type="hidden" name="total_count" value="<?php echo $totalElementcount; ?>">
		<?php

		if (isset($projects_data) && !empty($projects_data)) {
			foreach ($projects_data as $ekey => $elid) {

				/******************** GET ELEMENT DATA ************************/
				if(isset($elid['elements']) && !empty($elid['elements'])) {

				$dependancy_type = array();
				$elm_users = json_decode($elid[0]['user_detail'],TRUE);
				
				if( isset($elid[0]['dependency_type']) && !empty($elid[0]['dependency_type']) ){
					$dependancy_type = json_decode($elid[0]['dependency_type'],true);
				}


				$element_detail['Element'] = $elid['elements'];
				$element_detail['user_permissions'] = $elid['user_permissions'];

				$element_id = $element_detail['Element']['ele_id'];
				$element_titles = htmlentities($element_detail['Element']['ele_title']);
				$element_start_date = $element_detail['Element']['ele_start'];
				$element_end_date = $element_detail['Element']['ele_end'];

				$element_permit_type = false;
				if($element_detail['user_permissions']['role'] =='Creator'){
					$projectType = $cky = "m_project";
				}
				if($element_detail['user_permissions']['role'] =='Owner'){
					$projectType = $cky = "r_project";
				}
				if($element_detail['user_permissions']['role'] =='Group Owner'){
					$projectType = $cky = "g_project";
				}
				if($element_detail['user_permissions']['role'] =='Group Sharer'){
					$projectType = $cky = "g_project";
				}
				if($element_detail['user_permissions']['role'] =='Sharer'){
					$projectType = $cky = "r_project";
				}

				if($element_detail['user_permissions']['role'] =='Creator' || $element_detail['user_permissions']['role'] =='Owner' || $element_detail['user_permissions']['role'] =='Group Owner' ){
					//$element_permit_type = true;
				}
				if( isset($element_detail['user_permissions']) && !empty($element_detail['user_permissions']['permit_edit']) ){
					$element_permit_type = true;
				}

				$el_start_date = (isset($element_start_date) && !empty($element_start_date)) ? $this->Wiki->_displayDate($element_start_date,'Y-m-d') : false;
				$el_end_date = (isset($element_end_date) && !empty($element_end_date)) ? $this->Wiki->_displayDate($element_end_date,'Y-m-d') : false;

				$element_status = $elid[0]['ele_status'];

					/********************** GET WORKSPACE DATA *********************/
					$workspace_id = $elid['workspaces']['ws_id'];
					$workspace_title = htmlentities($elid['workspaces']['ws_title']);
					$workspace_start_date = $elid['workspaces']['ws_start'];
					$workspace_end_date = $elid['workspaces']['ws_end'];
					$workspace_permit_type = $elid[0]['wsp_permit_edit'];

					$wsp_start_date = (isset($workspace_start_date) && !empty($workspace_start_date)) ? $this->Wiki->_displayDate($workspace_start_date,'Y-m-d') : false;
					$wsp_end_date = (isset($workspace_end_date) && !empty($workspace_end_date)) ? $this->Wiki->_displayDate($workspace_end_date,'Y-m-d') : false;


                    	$element_assigned = false;
                    	$assigned_user_image = SITEURL . 'uploads/user_images/no_photo_small.png';
                    	$not_assigned_user_image = SITEURL . 'uploads/user_images/no_photo_small.png';
						$element_project = $prjid;
						$click_html = '';
						$hover_html = '';
						$receiver_name = 'N/A';
						$receiver_job_title = 'N/A';
						$assigned_class = 'no-reaction';

						$element_assigned = $elid['element_assignments'];

						$assign_creator = '';
						$assign_receiver = '';

						$receiver_detail['UserDetail']='';
						$taskAssigned = array();
						$creator_detail['UserDetail'] = '';
						$taskAssignedSender = array();

//pr($element_detail['Element']);
                    	//if(!empty($element_detail['Element']['ele_date_constraints'])) {

	                    	if( isset($element_assigned) && !empty($element_assigned['created_by']) && !empty($element_assigned['assigned_to']) ) {

	                    		$hover_html .= '<div class="assign-hover">';
								$assign_creator = $element_assigned['created_by'];
								$assign_receiver = $element_assigned['assigned_to'];
								$reaction = $element_assigned['reaction'];
								$assign_modified = $element_assigned['modified'];

								/* $creator_detail = get_user_data($assign_creator);
								$receiver_detail = get_user_data($assign_receiver); */
					
								if( isset($elid[0]['assign_received_user']) && !empty($elid[0]['assign_received_user']) ){
									$taskAssigned = json_decode($elid[0]['assign_received_user'],TRUE);
								}
								if( isset($elid[0]['assign_created_user']) && !empty($elid[0]['assign_created_user']) ){
									$taskAssignedSender = json_decode($elid[0]['assign_created_user'],TRUE);
								}

								if( isset($taskAssigned) && !empty($taskAssigned) ) {
									$receiver_detail['UserDetail'] = $taskAssigned[0];
								}
								if( isset($taskAssignedSender) && !empty($taskAssignedSender) ) {
									$creator_detail['UserDetail'] = $taskAssignedSender[0];
								}

								//pr($receiver_detail['UserDetail']);
								
								$profile_pic = '';
								$receiver_name = 'N/A';
								$receiver_selected_flname = 'N/A';

								if( isset($receiver_detail['UserDetail']) && !empty($receiver_detail['UserDetail']) ){

									//pr($receiver_detail['UserDetail']);

									if( isset($receiver_detail['UserDetail']['profile_pic']) && !empty($receiver_detail['UserDetail']['profile_pic']) ){
										$profile_pic = $receiver_detail['UserDetail']['profile_pic'];
									}
									if( isset($receiver_detail['UserDetail']['full_name']) && !empty($receiver_detail['UserDetail']['full_name']) ){
										$receiver_name = $receiver_detail['UserDetail']['full_name'];

										$receiver_selected_flname = $receiver_detail['UserDetail']['first_name'];
										if(isset($receiver_detail['UserDetail']['last_name']) && !empty($receiver_detail['UserDetail']['last_name'])){
										$receiver_selected_flname = $receiver_detail['UserDetail']['first_name']."<br />".$receiver_detail['UserDetail']['last_name'];
										}
									}
								}

								$receiver_job_title = $userDetail['UserDetail']['job_title'];

								if( $assign_receiver != $current_user_id ) {
									$click_html = CHATHTML($assign_receiver, $element_project);
							  	}
								 
								if(!empty($profile_pic) && file_exists(USER_PIC_PATH.$profile_pic)) {
									$assigned_user_image = SITEURL . USER_PIC_PATH . $profile_pic;
								}

							  	$assigned_label = 'Asssigned to: ';
							  	$assigned_to = $receiver_name;
							  	if( $assign_receiver == $current_user_id ) {
							  		$assigned_to = 'Me';
							  	}
							  	if($reaction == 3){
							  		$assigned_label = 'Disengaged by: ';
							  	}
							  	$hover_html .= '<span>'.$assigned_label.$assigned_to.'</span>';

								if( isset($creator_detail['UserDetail']['full_name']) && !empty($creator_detail['UserDetail']['full_name']) ){
									$assigned_by = $creator_detail['UserDetail']['full_name'];
								}


							  	if( $assign_creator == $current_user_id ) {
							  		$assigned_by = 'Me';
							  	}
							  	$hover_html .= '<span>Assigned by: '.$assigned_by.'</span>';

							  	$reaction_label = '';
							  	if($reaction == 1){
							  		$reaction_label = 'Schedule: Accepted';
							  	}
							  	else if($reaction == 2){
							  		$reaction_label = 'Schedule: Not Accepted';
							  	}
							  	$hover_html .= '<span>'.$this->Wiki->_displayDate($assign_modified, 'd M, Y g:iA').'</span>';
							  	$hover_html .= $reaction_label . '</div>';

							  	if( isset($reaction) && !empty($reaction) && $reaction == 1){
							  		$assigned_class = 'accepted';
							  	}
							  	else if( isset($reaction) && !empty($reaction) && $reaction == 2){
							  		$assigned_class = 'not-accepted';
							  	}
							  	else if( isset($reaction) && !empty($reaction) && $reaction == 3){
							  		$assigned_class = 'disengaged';
							  	} else {
									$assigned_class = 'no-reaction';
								}
	                    	}
	                    //}

	                    if(isset($named_params) && !empty($named_params)) {
							if($named_params == 1) {
								if($element_status != 'overdue') continue;
							}
							else if($named_params == 2) {
								if(completing_tdto($element_detail['Element']['id']) <= 0) continue;
							}
							else if($named_params == 3) {
								$el_is_assigned = element_assigned( $element_id );
								// pr($element_id);
								if( ( !isset($el_is_assigned) || empty($el_is_assigned) ) && ($element_status == 'progress' || $element_status == 'overdue') ){
									// die('hahahahah');
								}
								else {
									continue;
								}
							}
							else if($named_params == 4) {
								if($element_status != 'not_spacified') continue;
							}
							else if($named_params == 5) {
								if($element_status != 'completed') continue;
							}
							else if($named_params == 6) {
								if($element_status != 'not_started') continue;
							}
							else if($named_params == 7) {
								if($element_status != 'progress') continue;
							}
						}

						if($element_status != 'not_spacified' && ( !isset($elid['elements']['ele_date_constraints']) || $elid['elements']['ele_date_constraints'] == 0) ){
							 $element_status = 'not_spacified';
						}

						$workspace_title_tip = $workspace_title;
						$workspace_title_tip = html_entity_decode(htmlentities($workspace_title_tip));

						$assignedUserImage = '';
						if( isset($receiver_detail['UserDetail']['profile_pic']) && !empty($receiver_detail['UserDetail']['profile_pic']) && file_exists(USER_PIC_PATH.$receiver_detail['UserDetail']['profile_pic'])) {
							$assignedUserImage = SITEURL . USER_PIC_PATH . $receiver_detail['UserDetail']['profile_pic'];
						} else {
						 
							$assignedUserImage = SITEURL.'images/placeholders/user/user_1.png';
						}

					?>

				<div class="line-inner" data-people-title="<?php echo htmlentities($user_name); ?>" data-default="<?php echo $defaultVal++; ?>" data-element-title="<?php echo htmlentities($element_titles); ?>" data-workspace-title="<?php echo htmlentities($workspace_title_tip); ?>" data-element-status="<?php echo htmlentities($element_status); ?>" <?php if($el_start_date) { ?> data-element-start-date="
                        <?php echo $el_start_date; ?>"
                        <?php } ?>
                        <?php if($el_end_date) { ?> data-element-end-date="
                        <?php echo $el_end_date; ?>"
                        <?php } ?>
                        <?php if($wsp_start_date) { ?> data-workspace-start-date="
                        <?php echo $wsp_start_date; ?>"
                        <?php } ?>
                        <?php if($wsp_end_date) { ?> data-workspace-end-date="
                        <?php echo $wsp_end_date; ?>"
                        <?php } ?>
                        <?php if( isset($element_assigned) && !empty($element_assigned['created_by']) && !empty($element_assigned['assigned_to']) ) { ?>
                        data-element-assigned="<?php echo htmlentities($receiver_name); ?>"
                        <?php }  ?>
                        data-element-original="<?php echo htmlentities($user_name); ?>" >
						<!-- people section -->
                        <div class="col-xs-6 col-sm-6 col-md-3 col-lg-3 line-data line-1 prj-people">
                            <div class="img-box data-block">
                                <div class="thumb original-user" style="text-align: center;">
								<?php

								if( isset($element_assigned) && !empty($element_assigned['created_by']) && !empty($element_assigned['assigned_to']) ) {
								$ahtml = '';

								if( $element_assigned['assigned_to'] != $current_user_id  ) {
									$ahtml = CHATHTML($element_assigned['assigned_to'],$prjid);
								}
								$receiver_fullname = '';
								$receiver_job_title = '';
								
								if( isset($receiver_detail) && !empty($receiver_detail) && isset($receiver_detail['UserDetail']['full_name']) && !empty($receiver_detail['UserDetail']['full_name']) ){
									$receiver_fullname = $receiver_detail['UserDetail']['full_name'];
								}
								if( isset($receiver_detail) && !empty($receiver_detail) && isset($receiver_detail['UserDetail']['job_title']) && !empty($receiver_detail['UserDetail']['job_title']) ){
									$receiver_job_title = $receiver_detail['UserDetail']['job_title'];
								}
								 

									?>
								<span class="style-popple-icon-out">
									<span class="style-popple-icon">
                                    <a data-target="#popup_modal" data-toggle="modal" data-remote="<?php echo Router::Url( array( 'controller' => 'shares', 'action' => 'show_profile', $element_assigned['assigned_to'], 'admin' => FALSE ), TRUE ); ?>" class="pophover" data-content="<div><p class='pop_username'><?php echo htmlentities($receiver_fullname); ?></p><p><?php echo htmlentities($receiver_job_title); ?></p><?php echo $ahtml; ?></div>">
										<img  src="<?php echo $assignedUserImage; ?>" align="left" width="40" height="40"  />
									</a>
	
									</span>
									<?php 									
									if( $receiver_detail['UserDetail']['org_id'] != $current_org['organization_id']){ ?>	
										<i class="communitygray18 community-g tipText" title="" data-original-title="Not In Your Organization" ></i>
									<?php } ?>	
</span>
									<?php if( isset($receiver_selected_flname) && !empty($receiver_selected_flname) &&  $receiver_selected_flname != 'N/A' ){?>
									<div class="user-name-n"><?php
										echo $receiver_selected_flname;
									?></div>
									<?php }  ?>
								<?php } else {
									if( isset($elid[0]['selected_assign_user']) && !empty($elid[0]['selected_assign_user']) ){


									?>


									<p>Unassigned</p>

								<?php }
								} ?>



                                </div>
                                <?php if( isset($element_assigned) && !empty($element_assigned['created_by']) && !empty($element_assigned['assigned_to']) ) { ?>
                                <div class="thumb assigned-user" style="text-align: center; display: none;">
                                    <a data-target="#popup_modal" data-toggle="modal" data-remote="<?php echo Router::Url( array( 'controller' => 'shares', 'action' => 'show_profile', $assign_receiver, 'admin' => FALSE ), TRUE ); ?>" class="pophover" data-content="<div><p  class='pop_username'><?php echo htmlentities($receiver_name); ?></p><p><?php echo htmlentities($receiver_job_title); ?></p><?php echo $click_html; ?></div>">
										<img  src="<?php echo $assigned_user_image; ?>" class="user-image" align="left" width="40" height="40"  />
									</a>
									
								</div>
								<?php }else{ ?>
								<!--<div class="thumb assigned-user" style="text-align: center; display: none;">
                                    <a>
										<img  src="<?php echo $not_assigned_user_image; ?>" class="user-image" align="left" width="40" height="40"  />
									</a>
								</div>-->
								<?php } ?>




                            </div>
                        </div>
                        <!-- element section -->
						<?php
							$element_title_tip = htmlentities($element_titles);
							$element_title_tip = htmlentities($element_title_tip);
						?>
						<div class="col-xs-6 col-sm-6 col-md-3 col-lg-3 line-data line-2 prj-elements">
                            <div class="data-block" data-title="<?php echo html_entity_decode(htmlentities(ucfirst($element_title_tip))); ?>">
                            	<?php if( empty($element_assigned['created_by']) && empty($element_assigned['assigned_to']) ) {  ?>
                            		<i class="fa fa-times text-black icon-task-assign tipText" title="No Assignment"></i>
                            	<?php }
                            	else{ ?>
                            		<i class="fa <?php echo $assigned_class; ?> text-black icon-task-assign task-assigned" title="Task Leader" data-content='<?php echo $hover_html; ?>'></i>
                            	<?php } ?>
                                <a href="<?php echo Router::Url(array("controller" => "entities", "action" => "update_element", $element_id), true); ?>#tasks" class="data-block-title pop tipText" title="<?php echo ucfirst($element_titles); ?>">
                                    <?php echo html_entity_decode(htmlentities(ucfirst($element_titles))); ?>
                                </a>
                                <div class="data-block-in">
                                    <div class="data-block-sec cell_<?php echo $element_status; ?>">
                                        <span>Start: <?php echo (empty($element_start_date)) ? 'N/A' : $this->Wiki->_displayDate($element_start_date,'d M, Y');  ?>
											</span>
                                        <span>End: <?php echo (empty($element_end_date)) ? 'N/A' : $this->Wiki->_displayDate($element_end_date,'d M, Y'); ?>
											</span>
                                    </div>
                                    <div class="pull-right edit-date">
                                        <div class="top-btn but-fa-arrow" style="display: block;">
                                            <?php
												//$getCriticalStatus = $this->Common->critical_status($element_id);

												$getCriticalStatus = $elid[0]['dep_is_critical'];

												//$getDependancyStatus = $this->Common->dependancy_status($element_id);

												$predessorCount =0;
												$successorCount =0;

												//$predessorCount = $this->Common->ele_dependency_count($element_id, 1);
												//$successorCount = $this->Common->ele_dependency_count($element_id, 2);

												//$dependancy_type
												$getDependancyStatus = '';
												if( isset($dependancy_type) && !empty($dependancy_type) ){
													$newDependancyArray = $dependancy_type;
													$newDependancyArraypre = $dependancy_type;
													if( in_array("predecessor",$dependancy_type) && in_array("successor",$dependancy_type) ){
														$getDependancyStatus = "both";
													} else if ( in_array("predecessor",$dependancy_type) && $dependancy_type > 0 ) {
														$getDependancyStatus = 'predessor';
													} else if ( in_array("successor",$dependancy_type) && $dependancy_type > 0) {
														$getDependancyStatus = 'successor';
													} else {
														$getDependancyStatus = 'none';
													}

													//successor count
													$successorCount = count(array_diff($newDependancyArray, ["predecessor"]));
													//predecessor count
													$predessorCount = count(array_diff($newDependancyArraypre, ["successor"]));

												}


												// Critical Status
												if( isset($getCriticalStatus) && $getCriticalStatus == 1 ){
											?>
                                                <i id="redrightarrow_<?php echo $element_id;?>" class="fa fa-arrow-right red-arrow-task tipText" title="Priority Task" style="border: 1px solid #ccc; padding: 0px 6px; color: #dd4c3a; cursor: default;"></i>
                                                <?php } ?>
                                                <i id="redrightarrow_<?php echo $element_id;?>" class="fa fa-arrow-right red-arrow-task tipText" title="Critical Task" style="border: 1px solid #ccc; padding: 0px 6px; color: #dd4c3a; cursor: default; display:none;"></i>
                                                <?php
											// Dependancy Status
											if( isset($getDependancyStatus) && $getDependancyStatus == 'predessor' ){

											?>
                                                    <i rel="popover" data-popover-content="#myPopoverDependencyElement" id="leftarrow_<?php echo $element_id;?>" data-dependancytype="1" data-elementid="<?php echo $element_id;?>" class="fa fa-arrow-left tipTexts element_list" data-original-title="Predecessors" data-elecount="<?php echo $predessorCount;?>" style="border: 1px solid #ccc; padding: 0px 6px; color: #01b051; "></i>
                                                    <?php } else if(isset($getDependancyStatus) && $getDependancyStatus == 'successor'){ ?>
                                                    <i id="rightarrow_<?php echo $element_id;?>" data-dependancytype="2" data-elementid="<?php echo $element_id;?>" class="fa fa-arrow-right tipTexts element_list" data-original-title="Successors" data-elecount="<?php echo $successorCount;?>" style="border: 1px solid #ccc; padding: 0px 6px; color: #01b051; "></i>
                                                    <?php } else if(isset($getDependancyStatus) && $getDependancyStatus == 'both'){?>
                                                    <i id="botharrow_<?php echo $element_id;?>" data-dependancytype="3" data-elementid="<?php echo $element_id;?>" class="fa double-arrow tipTexts small-caps element_list" data-original-title="Predecessor and Successor" style="border: 1px solid #ccc; padding: 0px 6px; color: #01b051; "></i>
                                                    <?php } //else { ?>
                                                    <i rel="popover" data-popover-content="#myPopoverDependencyElement" id="leftarrow_<?php echo $element_id;?>" data-dependancytype="1" data-elementid="<?php echo $element_id;?>" class="fa fa-arrow-left tipTexts element_list" data-original-title="Predecessors" data-elecount="<?php echo $predessorCount;?>" style="border: 1px solid #ccc; padding: 0px 6px; color: #01b051; display:none;"></i>
                                                    <i id="rightarrow_<?php echo $element_id;?>" data-dependancytype="2" data-elementid="<?php echo $element_id;?>" class="fa fa-arrow-right tipTexts element_list" data-original-title="Successors" data-elecount="<?php echo $successorCount;?>" style="border: 1px solid #ccc; padding: 0px 6px; color: #01b051; display:none;"></i>
                                                    <i id="botharrow_<?php echo $element_id;?>" data-dependancytype="3" data-elementid="<?php echo $element_id;?>" class="fa   double-arrow tipTexts small-caps element_list" data-original-title="Predecessor and Successor" style="border: 1px solid #ccc; padding: 0px 6px; color: #01b051; display:none;"></i>
                                                    <?php //}
														/*
															<a href="javascript:void(0);" data-toggle="modal" data-target="#modal_large" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "task_list_el_date", $element_id), true); ?>" >
																<i class="fa fa-arrow-right" style="border: 1px solid #ccc; padding: 0px 6px; color: #dd4c3a; cursor: pointer;"></i>
																</a>
																<a href="javascript:void(0);" data-toggle="modal" data-target="#modal_large" data-remote="<?php echo Router::Url(array(" controller " => "dashboards ", "action " => "task_list_el_date ", $element_id), true); ?>">
															<i class="fa fa-arrow-left" style="border: 1px solid #ccc; padding: 0px 6px; color: #01b051;"></i>
														</a>
													*/ ?>
                                        </div>
                                        <div class="task-sh-remindar">
                                            <?php


											//echo $element_status;
											//echo $project_permit_type;
											//if($project_permit_type && ($element_status == 'progress' || $element_status == 'not_started' || $element_status == 'overdue')) {
											if( $project_permit_type && ($element_status == 'progress' || $element_status == 'not_started') ) {

											$reminder_html = '';

											//$get_element_reminder = get_element_reminder($element_id);
											$get_element_reminder = $elid['Reminder'];
											$reminder_id = 0;
											$remind_btn_class = 'btn-default';
											$remind_icon_class = 'black';
											$remind_tooltip_class = 'tipText';
											$remind_tooltip_attr = 'title="Set Reminder"';
											if( !empty($get_element_reminder['rem_userid']) && !empty($get_element_reminder['rem_elementid'] ) ) {

												$reminder_id = $get_element_reminder['rem_id'];
												$reminder_user = $get_element_reminder['rem_userid'];
												// pr($get_element_reminder);
												$unbind = ['hasMany' => ['ProjectPermission', 'WorkspacePermission', 'ElementPermission', 'UserProject', 'UserSetting'], 'hasOne' => ['UserInstitution']];
												$userDetail = $this->ViewModel->get_user( $reminder_user, $unbind, 1 );
												$user_name = 'N/A';
												if(isset($userDetail) && !empty($userDetail)) {
													$user_found = true;
													$user_name = $userDetail['UserDetail']['first_name'] . ' ' . $userDetail['UserDetail']['last_name'];
												}

												$remind_btn_class = 'btn-success';
												$remind_icon_class = 'wht';

												$remind_tooltip_class = 'pophover';
												$reminder_html = '<div class="reminder_popup">';
												// $reminder_html .= '<div class="reminder_title">Reminder:</div>';
												$reminder_html .= '<div class="reminder_by"><b>Reminder by: </b>'.$user_name.'</div>';
												$reminder_html .= '<div class="reminder_time"><b>For: </b>'.date('M d, Y g:00A',strtotime($get_element_reminder['rem_date'])).'</div>';
												$reminder_html .= '</div>';
												$remind_tooltip_attr = '';
											}
											 ?>
											
											 
                                                    <a data-toggle="modal" <?php echo $remind_tooltip_attr; ?> data-target="#modal_small" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "element_reminder", $element_id, $reminder_id), true); ?>"  class="btn btn-xs but-remindar <?php echo $remind_btn_class; ?> <?php echo $remind_tooltip_class; ?> calender_modal" data-content='<?php echo $reminder_html; ?>'  href="#" style="padding: 1px 3px;"><i class="icon_reminder <?php echo $remind_icon_class; ?>"></i></a>
											 <?php 
											  } else if( !empty($elid['Reminder']['rem_id']) && $element_status == 'overdue' ){ 
											  
												$reminder_html = '';

											//$get_element_reminder = get_element_reminder($element_id);
											$get_element_reminder = $elid['Reminder'];
											$reminder_id = 0;
											$remind_btn_class = 'btn-default';
											$remind_icon_class = 'black';
											$remind_tooltip_class = 'tipText';
											$remind_tooltip_attr = 'title="Set Reminder"';
											if( !empty($get_element_reminder['rem_userid']) && !empty($get_element_reminder['rem_elementid'] ) ) {

												$reminder_id = $get_element_reminder['rem_id'];
												$reminder_user = $get_element_reminder['rem_userid'];
												// pr($get_element_reminder);
												$unbind = ['hasMany' => ['ProjectPermission', 'WorkspacePermission', 'ElementPermission', 'UserProject', 'UserSetting'], 'hasOne' => ['UserInstitution']];
												$userDetail = $this->ViewModel->get_user( $reminder_user, $unbind, 1 );
												$user_name = 'N/A';
												if(isset($userDetail) && !empty($userDetail)) {
													$user_found = true;
													$user_name = $userDetail['UserDetail']['first_name'] . ' ' . $userDetail['UserDetail']['last_name'];
												}

												$remind_btn_class = 'btn-success';
												$remind_icon_class = 'wht';

												$remind_tooltip_class = 'pophover';
												$reminder_html = '<div class="reminder_popup">';
												// $reminder_html .= '<div class="reminder_title">Reminder:</div>';
												$reminder_html .= '<div class="reminder_by"><b>Reminder by: </b>'.$user_name.'</div>';
												$reminder_html .= '<div class="reminder_time"><b>For: </b>'.date('M d, Y g:00A',strtotime($get_element_reminder['rem_date'])).'</div>';
												$reminder_html .= '</div>';
												$remind_tooltip_attr = '';
											}
												
													
												?>
													<a data-toggle="modal" <?php echo $remind_tooltip_attr; ?> data-target="#modal_small" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "element_reminder", $element_id, $reminder_id), true); ?>"  class="btn btn-xs but-remindar <?php echo $remind_btn_class; ?> <?php echo $remind_tooltip_class; ?> calender_modal" data-content='<?php echo $reminder_html; ?>'  href="#" style="padding: 1px 3px;"><i class="icon_reminder <?php echo $remind_icon_class; ?>"></i></a>
											  
											  <?php } ?>
                                                    <?php if($element_permit_type) { ?>

													<a data-updated="<?php echo $element_id; ?>" id="tasksech_<?php echo $element_id;?>" data-type="element" data-toggle="modal" data-target="#modal_large" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "task_list_el_date", $element_id), true); ?>" data-original-title="Task Manager" class="but-sh calender_modal tipText"  href="#"><i class="btn btn-sm btn-default active">TM</i></a>

												<?php
												} ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

						<?php
								$workspace_title_tip = html_entity_decode(htmlentities($workspace_title));
								$workspace_title_tip = html_entity_decode(htmlentities($workspace_title_tip));
							?>
                        <!-- workspace section -->
                        <div class="col-xs-6 col-sm-6 col-md-3 col-lg-3 line-data line-3 prj-workspaces">
                            <div class="data-block" data-title="<?php echo ucfirst($workspace_title_tip); ?>">
                                <a href="<?php echo Router::Url(array("controller" => "projects", "action" => "manage_elements", $prjid,$workspace_id), true);; ?>" class="data-block-title pop tipText" title="<?php echo $workspace_title_tip; ?>">
                                    <?php echo html_entity_decode(htmlentities($workspace_title)); ?>
                                </a>
                                <?php $workspace_status = workspace_status($workspace_id); ?>
                                <div class="data-block-in">
                                    <div class="data-block-sec cell_<?php echo $workspace_status; ?>">
                                        <span>Start: <?php echo (empty($workspace_start_date)) ? 'N/A' : $this->Wiki->_displayDate($workspace_start_date,'d M, Y');  ?>
											</span>
                                        <span>End: <?php echo (empty($workspace_end_date)) ? 'N/A' : $this->Wiki->_displayDate($workspace_end_date,'d M, Y'); ?>
											</span>
                                    </div>
                                    <?php if($workspace_permit_type) { ?>
                                    <!--<div class="pull-right edit-date  task-center-arrow"><a data-updated="<?php echo $workspace_id; ?>" data-type="workspace" data-toggle="modal" data-target="#modal_small" data-remote="<?php echo Router::Url(array("controller" => "dashboards", "action" => "task_list_ws_date", $prjid, $workspace_id), true); ?>" data-original-title="Workspace Schedule" class=" calender_modal tipText" title="Workspace Schedule" href="#"><i class="btn btn-sm btn-default active">SH</i></a></div>-->
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                        <!-- project section -->
                        <div class="col-xs-6 col-sm-6 col-md-3 col-lg-3 line-data line-4 prj-projects">
                        	<div class="project-block">
								<div class="container1 project-thumb-slider">
                        			<div class="owl-carousel">
										
                				<?php
                				if(isset($elm_users) && !empty($elm_users)) {
									 
	                        		foreach ($elm_users as $userval) {
										
										$userId = $userval['user_id'];										 
										$org_ids = $userval['org_id'];
										
	                        			$user_chat_popover = user_chat_popover($userId, $prjid);
                    			?>
	                        			<div class="item" >
		                    				
													<img data-target="#popup_modal" data-toggle="modal" data-remote="<?php echo Router::Url( array( 'controller' => 'shares', 'action' => 'show_profile', $userId, 'admin' => FALSE ), TRUE ); ?>" class="user_popover" data-user="<?php echo $userId; ?>" src="<?php echo $user_chat_popover['user_image']; ?>" data-content="<div class=''><p  class='pop_username'><?php echo htmlentities($user_chat_popover['user_name']); ?></p><p><?php echo htmlentities($user_chat_popover['job_title']); ?></p><?php echo $user_chat_popover['html']; ?></div>" > 
													
		                    			 
										<?php 	
											if( $org_ids != $current_org['organization_id'] ){ ?>	
												<i class="communitygray18 community-g tipText" title="" data-original-title="Not In Your Organization" ></i>
											<?php } ?>	
										</div>	
                    			<?php
	                        		}
                        		}
                        		?>
								
		                        	</div>
		                        </div>
                        	</div>
							<?php
								$project_title_tip = htmlentities($project_title);
								$project_title_tip = htmlentities($project_title_tip);
							?>

                        </div>



				</div>
                    <?php
						}
					}
				}

		?>
			<div class="no-row-found no-more-data projectcls_<?php echo $prjid; ?>">No More Data.</div>
			<input type="hidden" id="paging_page_<?php echo $prjid; ?>" value="0" />
			<input type="hidden" id="paging_max_page_<?php echo $prjid; ?>" value="<?php echo $totalElementcount;?>" />
        </div>
        <?php
		}
	} else {

		if(empty($this->request->params['named']['status']) && empty($this->request->params['named']['assigned']) && empty($this->request->params['pass'])  ){
	?>
            <div class="no-row-wrapper">SELECT AND SHOW PROJECT(S)</div>
            <?php
		}


		}
	?>
</div>

<script type="text/javascript">
function updateElement(element_id) {
	var ele_url = '<?php echo SITEURL?>entities/update_element/' + element_id + '#tasks';
	window.location.href = ele_url;
}

$(function() {

	//console.log('filter test')
	$('#el-status-dd').on('hidden.bs.dropdown', function(){
        //console.log('run ajax here',$.statusParams)
        // && $.statusParams['users'] && $.statusParams['task_status']
		//var sts_checked = $('#el-status-dd ul>li').find('input[type="checkbox"]').is(":checked");
        //if ($.statusParams && sts_checked == true) {
        if ($.statusParams ) {
            $.task_update_status($.statusParams);
        }
    })

	$('.show_more').on('click', function(event) {
		event.preventDefault();
		var data = $(this).parents('.line-header').data(),
			target = data.target,
			$row = $(target);

		$row.toggleClass('scrollable');
		$(this).toggleClass('open');

		if($(this).hasClass('open')){
			$(this).attr('title', 'Collapse').tooltip('fixTitle').tooltip('show');
		}
		else{
			$(this).attr('title', 'Expand').tooltip('fixTitle').tooltip('show');
		}

	});

	$('.owl-carousel').owlCarousel({
        loop:false,
        margin:2,
        nav:true,
        dots:false,
        autoWidth: true,
        navText : ['<i class="fa fa-angle-left" aria-hidden="true"></i>','<i class="fa fa-angle-right" aria-hidden="true"></i>']
    })

/*     $('.user_popover').popover({
        placement: 'bottom',
        trigger: 'hover',
        html: true,
        container: 'body',
        delay: { show: 50, hide: 400 }
    });

    $('.users_popovers,.pophover').popover({
        placement: 'bottom',
        trigger: 'hover',
        html: true,
        container: 'body',
        delay: { show: 50, hide: 400 }
    }); */

    $('.task-assigned').popover({
        placement: 'bottom',
        trigger: 'hover',
        html: true,
        container: 'body',
        /*delay: { show: 50, hide: 100 }*/
    });

    $.selectedDates;

    $(".select_dates").daterange({
        defaultDate: new Date(),
        // defaultDate: "+1w",
        numberOfMonths: 2,
        showButtonPanel: false,
        onSelect: function(selected, inst) {
            var divPane = inst.input
                .datepicker("widget")
        },
        beforeShow: function(input, inst) {
            var divPane = $(input)
                .datepicker("widget")
        },
        onClose: function(dateText, inst) {
        	$('.no-result').hide();
            var $input = inst.input,
                $cal_text = $input.parents('.task-section:first').find('.selected_dates:first'),
                $cal_icon = $('.calendar_trigger');

            if (dateText) {
                var dates = dateText.split(' - ');
                	firstDate = $.datepicker.formatDate('dd M, yy', new Date(dates[0])),
                    secDate = (dates[1]) ? $.datepicker.formatDate('dd M, yy', new Date(dates[1])) : '',
                    dateStr = firstDate + ((secDate) ? ' - ' + secDate : ' - ' + firstDate),
                    data = $cal_icon.data(),

					firstDateSelect = $.datepicker.formatDate('yy-M-dd', new Date(dates[0])),
					secDateSelect = $.datepicker.formatDate('yy-M-dd', new Date(dates[1])),


				$cal_text.html(dateStr + '<i class="fa fa-times pull-right empty-dates" style=" "></i>').slideDown(500);

                $('.projects-line').find('.line-inner').show();
                var showElements = [];

                var cal_startDate = new Date(dates[0]),
                    cal_endDate = (dates[1]) ? new Date(dates[1]) : cal_startDate;

				var status = $("#status-dropdown li a").data('status');
				var task_status = [];
				$("input:checked", $('#status-dropdown')).each(function(){
					task_status.push($(this).val());
				});
				var ass_status = $("#assign_status_dropdown").find('.sel_assign').data('status');
				var users = new Array();
				var assigned_user_id = new Array();
				assigned_user_id = $('#assigned_users_list').val();
				users = $('#users_list').val();
				var selected_value = $('input.project_name:checked').map(function() {
					return this.value;
				}).get();

				var element_sorting = '';
				var wsp_sorting = '';
				var assign_sorting = '';

				if( $(".alphabetical").data() && $(".alphabetical").data('sorted') != null && $(".alphabetical").hasClass('alphaSelected') ) {

					if( $(".alphabetical").data('sorted') == 'desc' ){
						element_sorting = 'asc';
					} else {
						element_sorting = 'desc';
					}

				} else if( $(".wsp_alphabetical").data() && $(".wsp_alphabetical").data('sorted') != null && $(".wsp_alphabetical").hasClass('alphaSelected') ) {

					if( $(".wsp_alphabetical").data('sorted') == 'desc' ){
						wsp_sorting = 'asc';
					} else {
						wsp_sorting = 'desc';
					}


				} else if( $(".assign_alphabetical").data() && $(".assign_alphabetical").data('sorted') != null && $(".assign_alphabetical").hasClass('alphaSelected')) {

					if( $(".assign_alphabetical").data('sorted') == 'desc' ){
						assign_sorting = 'asc';
					} else {
						assign_sorting = 'desc';
					}

				}


				var element_text = $("input[name='task_search']").val();

				var params = {
					assigned_user_ids: [assigned_user_id],
					user_ids: [users],
					project_ids: selected_value,
					assigned_status:ass_status,
					task_status:task_status,
					assign_sorting:assign_sorting,
					wsp_sorting:wsp_sorting,
					element_sorting:element_sorting,
					element_title:element_text,
					selectedDates:firstDateSelect+" - "+secDateSelect

				};
				if(dateText){
					$.task_update_status(params);
				}
				return false;

            } else {
                // $cal_text.css('opacity', 0)
                $cal_text.css('display', 'none')
                $cal_text.next('.task:first').css('padding', '0px 10px 10px')
            }
        },
        showOptions: { direction: "down" },
    });

    $(".calendar_trigger").on('click', function(event) {
        event.preventDefault();
        $('.select_dates').trigger('focus');
    });


    $("body").on("click", function(e) {
        $(".element_list").popover('destroy');
    });

    $("body").on("mouseover", function(e) {
        //$(".element_list").popover('destroy');
    });

    $(".line-inner").on("mouseover", function(e) {
        $(".element_list").popover('destroy');
    });

    $(".projects-line").on('scroll', function(event) {
        $(".element_list").popover('hide');
    });

    $(".element_list").on("mouseover", function(e) {
        $that = $(this);
        $(".element_list").popover('destroy');

        var element_id = $that.data("elementid");
        var preSucEleCount = $that.data("elecount");
        var depndncytype = $that.data("dependancytype");
        var ele_list_url = $js_config.base_url + 'dashboards/element_list';

        $.ajax({
            url: ele_list_url,
            type: 'POST',
            data: { element_id: element_id, dependancytype: depndncytype },
            dataType: 'json',
            success: function(response, status, jxhr) {

                $that.attr('data-content', response);
                $that.attr('rel', 'popover');
                $that.popover({
                    container: 'body',
                    html: true,
                });


				//Predecessors Successors
				if (depndncytype == 1) {
					var predetitle = 'Predecessors (' + preSucEleCount + ')';
					$that.attr('data-original-title', predetitle);
				}
				if (depndncytype == 2) {
					var succetitle = 'Successors (' + preSucEleCount + ')';
					$that.attr('data-original-title', succetitle);
				}

                $that.popover('show');
                $that.tooltip('hide');

                /* if (depndncytype == 1) {
                    //$that.attr('title','Predecessors');
                    $that.attr('data-original-title', 'Predecessors');
                }
                if (depndncytype == 2) {
                    //	$that.attr('title','Successors');
                    $that.attr('data-original-title', 'Successors');
                } */

            }
        })
    })

    })
</script>

<script type="text/javascript">
$(function(){

	$('.owl-carousel').owlCarousel({
        loop:false,
        margin:2,
        nav:true,
        dots:false,
        autoWidth: true,
        navText : ['<i class="fa fa-angle-left" aria-hidden="true"></i>','<i class="fa fa-angle-right" aria-hidden="true"></i>']
    })

	/*============== filter data pagging ========================*/

	var outerPane = $('.projects-line',$(this)),
	didScroll = false;

	outerPane.scroll(function() { //watches scroll of the div
		outerPane = $(this);
		didScroll = true;
	});

    //Sets an interval so your div.scroll event doesn't fire constantly. This waits for the user to stop scrolling for not even a second and then fires the pageCountUpdate function (and then the getPost function)
    setInterval(function() {
        if (didScroll){
			didScroll = false;


            if(outerPane.scrollTop() + outerPane.innerHeight() + 5 >= (outerPane[0].scrollHeight ))
            {
                $.pageCountUpdate(outerPane);
            }
       }
    }, 150);

	/* $(window).resize(function(){
		setInterval(function() {
			if (didScroll){
				didScroll = false;
 alert(1);

				if(outerPane.scrollTop() + outerPane.innerHeight() + 5 >= (outerPane[0].scrollHeight ))
				{ alert(2);
					$.pageCountUpdate(outerPane);
				}
       }
		}, 150);
	});
		 */


    $.loading_data = true;


	/***********************************************************/
	$.tasttypeonchage = true;
	// ASSIGNED Task Type MULTISELECT BOX INITIALIZATION
    $.element_task_type = $('.element_task_type').multiselect({
        buttonClass: 'btn btn-default aqua',
        buttonWidth: '100%',
        buttonContainerWidth: '85%',
        numberDisplayed: 2,
        maxHeight: '318',
        checkboxName: 'eletasktype',
        includeSelectAllOption: false,
        enableFiltering: true,
        multiple: true,
        filterPlaceholder: 'Search',
        enableCaseInsensitiveFiltering: true,
		enableUserIcon:false,
        nonSelectedText: 'All Task Types',
        onDropdownHidden: function(option, checked, select) {
				$that = $(this);
				$select = select;


				$parentDiv = $(option.target).parents('.line-header:first');


				var ele_task_type = [];
				ele_task_type = $('input[name="eletasktype"]:checked', $(option.target)).map(function()
				{
					return this.value;
				}).get();

				if(ele_task_type.length > 0){

					$parentDiv.find('.element_task_type').removeClass('unavailablett') ;
				}


				//var projectid = $parentDiv.next('.projects-line').data('project');
				var projectid = $($parentDiv.data('target')).data('project');
				var total_count = outerPane.find('input[name="total_count"]');
				var user_id = $("#users_list").val();
				var task_status = [];
				$("input:checked", $('#status-dropdown')).each(function(){
					task_status.push($(this).val());
				});
				var ass_status = $("#assign_status_dropdown").find('.sel_assign').data('status');
				var assigned_user_id = new Array();
				assigned_user_id = $('#assigned_users_list').val();

				var startDateSorting = '';
				if( $(".start_date_sort.sort").hasClass("usedSort") ){
					startDateSorting = $(".start_date_sort.sort.usedSort").data('type');
				}

				var endDateSorting = '';
				if( $(".end_date_sort.sort").hasClass("usedSort") ){
					endDateSorting = $(".end_date_sort.sort.usedSort").data('type');
				}

				var element_sorting = '';
				var wsp_sorting = '';
				var assign_sorting = '';

				if( $(".alphabetical").data() && $(".alphabetical").data('sorted') != null && $(".alphabetical").hasClass('alphaSelected') ) {

					if( $(".alphabetical").data('sorted') == 'desc' ){
						element_sorting = 'asc';
					} else {
						element_sorting = 'desc';
					}

				} else if( $(".wsp_alphabetical").data() && $(".wsp_alphabetical").data('sorted') != null && $(".wsp_alphabetical").hasClass('alphaSelected') ) {

					if( $(".wsp_alphabetical").data('sorted') == 'desc' ){
						wsp_sorting = 'asc';
					} else {
						wsp_sorting = 'desc';
					}


				} else if( $(".assign_alphabetical").data() && $(".assign_alphabetical").data('sorted') != null && $(".assign_alphabetical").hasClass('alphaSelected')) {
						console.log("assign alphabetical");
					if( $(".assign_alphabetical").data('sorted') == 'desc' ){
						assign_sorting = 'asc';
					} else {
						assign_sorting = 'desc';
					}

				}

				var selectedDates = $.trim($(".selected_dates").text());
				var element_text = $("input[name='task_search']").val();


				var params = {

					project: projectid,
					user_id:user_id,
					assigned_userid:[assigned_user_id],
					assigned_status:ass_status,
					task_status:task_status,
					dateStartSort_type: startDateSorting,
					dateEndSort_type: endDateSorting,
					assign_sorting:assign_sorting,
					wsp_sorting:wsp_sorting,
					element_sorting:element_sorting,
					selectedDates: selectedDates,
					element_title:element_text,
					eletasktype:ele_task_type

				}

				/* console.log($.tasttypeonchage,"before");
				console.log("trying");
				 console.log($parentDiv.find('.element_task_type')); */

				/* if($that.hasClass('unavailablett') && ele_task_type.length < 0 ){
					$that.removeClass('unavailablett');
					$.tasktype_update_data(params,$parentDiv);
					$.tasttypeonchage = false;
				} */

				//if( $.tasttypeonchage ){
					 if(!$parentDiv.find('.element_task_type').hasClass('unavailablett') ){

						$.tasktype_update_data(params,$parentDiv).done(function(){


							console.log("Vikas Finalsss");
							var selectbox = $parentDiv.find(".element_task_type");
							//$("option:selected",selectbox).prop("selected", false);

							var ele_task_type = [];
							ele_task_type = $('input[name="eletasktype"]:checked', $(option.target)).map(function()
							{
								return this.value;
							}).get();

							if(ele_task_type.length < 1){
							$parentDiv.find('.element_task_type').addClass('unavailablett');
							}
							//console.log(ele_task_type.length);

							$parentDiv.find(".element_task_type").multiselect('refresh');


						})

						$.tasttypeonchage = false;


					//}
				 }
				console.log($.tasttypeonchage,"after");
				$('.tooltip').remove();
				return false;


        },
        onChange: function(option, closed, select) {
			$tasttypeonchage= true;
        },
    });



	/***********************************************************/



})
</script>