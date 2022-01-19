<?php
/**
 * Gantts controller.
 *
 * This file will render views from views/Gantts/
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
App::uses('Controller/Component', 'Auth', 'Session', 'RequestHandler');
App::uses('CakeEmail', 'Network/Email');
App::uses('SimplePasswordHasher', 'Controller/Component/Auth');
App::import('Vendor', 'google-api');
App::uses('HttpSocket', 'Network/Http');

App::import('Lib', 'XmlApi');
/**
 * Gantts Controller
 *
 * @property User $User
 * @property PaginatorComponent $Paginator
 */
class GanttsController extends AppController {

	var $name = 'Gantts';	


	public $uses = array('User', 'UserDetail', 'UserProject', 'CakeSession', 'Element', 'Workspace', 'Area', 'Project');	

	/**
	 * check login for admin and frontend user
	 * allow and deny user
	 */
	public $components = array('Email', 'Common', 'Image', 'CommonEmail', 'Auth', 'Group', 'Users');

	public $live_setting;

	public $mongoDB = null;
	/**
	 * Helpers
	 *
	 * @var array
	 */
	public $helpers = array('Html', 'Form', 'Session', 'Time', 'Text', 'Common', 'Group', 'Wiki', 'User', 'ViewModel');

	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow();

		$view = new View();
		$this->objView = $view;

		$this->live_setting = LIVE_SETTING;
	}

	/*     * ********************* Front End Panel Common Functions Start ************************* */

	public function index() {
		if (!$this->Auth->loggedIn()) {
			return $this->redirect(array('controller' => 'pages', 'action' => 'display', 'home', 'admin' => false));
		}
		return $this->redirect(array('controller' => 'pages', 'action' => 'display', 'home', 'admin' => false));
	}	

	public function event_gantt($pid = null) {
		$this->set('title_for_layout', __('Planner', true));
		$this->layout = "calander";
		$user_id = $this->Auth->user('id');
		$project_id = '';


		if( $_SERVER['HTTP_HOST'] != LOCALIP )  {

			if( $this->Session->read('Auth.User.role_id') == 3 ){
				$this->redirect(['controller' => 'organisations','action' => 'dashboard']);
			}
			if( $this->Session->read('Auth.User.role_id') == 1 ){
				$this->redirect(['controller' => 'dashboard','action' => 'index']);
			}
		}

		$project_type = 'm_project';
		// project type coming from UserPermission
		if (!isset($this->params->named) || empty($this->params->named)) {
			$project_type = CheckProjectType($pid, $user_id);
			$project_id = $pid;
		}

		if (isset($this->params->named['m_project']) && !empty($this->params->named['m_project'])) {
			$project_type = 'm_project';
			$project_id = $this->params->named['m_project'];
		} else if (isset($this->params->named['r_project']) && !empty($this->params->named['r_project'])) {
			$project_type = 'r_project';
			$project_id = $this->params->named['r_project'];
		} else if (isset($this->params->named['g_project']) && !empty($this->params->named['g_project'])) {
			$project_type = 'g_project';
			$project_id = $this->params->named['g_project'];
		}

		if (isset($this->params['named']['mode']) && !empty($this->params['named']['mode']) && $this->params['named']['mode'] == 'month') {
			$this->set('mode', $mode = 'month');
		} else if (isset($this->params['named']['mode']) && !empty($this->params['named']['mode']) && $this->params['named']['mode'] == 'year') {
			$this->set('mode', $mode = 'year');
		} else if (isset($this->params['named']['mode']) && !empty($this->params['named']['mode']) && $this->params['named']['mode'] == 'week') {
			$this->set('mode', $mode = 'week');
		} else {
			$this->set('mode', $mode = 'year');
		}

		$project = $this->Project->find("first", array("conditions" => array("Project.id" => $project_id),  'recursive' => -1 ));

		$project_name = htmlentities(str_replace("&nbsp;", " ", $project['Project']['title']), ENT_QUOTES);
		$workspace_id = isset($this->params->named['workspace']) && !empty($this->params->named['workspace']) ? $this->params->named['workspace'] : '';
		$this->Project->id = $project_id;

		if ($project_id == '' || !$this->Project->exists() && $project_type == 'm_project') {
			return $this->redirect(SITEURL . 'projects/lists');
		}
		if ($project_id == '' || !$this->Project->exists() && $project_type == 'r_project') {
			return $this->redirect(SITEURL . 'projects/share_lists');
		}
		if ($project_id == '' || !$this->Project->exists() && $project_type == 'g_project') {
			return $this->redirect(SITEURL . 'groups/shared_projects');
		}


		// owner level project coming from UserPermission
		$allProjects  = array();
		$allProjects = $this->Common->getAssestsProjects($this->Session->read('Auth.User.id'),2);

		// Find All workspaces by project id
		$myWorkspaceslistByproject = [];
		$all_wsp = $this->objView->loadHelper('Permission')->wsp_of_project($project_id, $user_id);
		if(isset($all_wsp) && !empty($all_wsp)){
			foreach ($all_wsp as $key => $value) {
				$myWorkspaceslistByproject[$value['Workspace']['id']] = $value['Workspace']['title'];
			}
		}	


		$crumb = ['last' => ['Gantt']];

		if (isset($project_id) && !empty($project_id)) {
			$project_title = (isset($project) && !empty($project)) ? $project['Project']['title'] : '';
			$crumb1 = [
				'Project' => [
					'data' => [
						'url' => '/projects/index/' . $project_id,
						'class' => 'tipText',
						'title' => $project_title,
						'data-original-title' => $project_title,
					],
				],
			];
			$crumb = array_merge($crumb1, $crumb);
		} else {
			$crumb = array_merge(['Projects' => '/projects/lists'], $crumb);
		}

		$priorty_data = array();
		$wsp_tasks_data = $this->objView->loadHelper('Permission')->wsp_tasks_data($project_id); 
		
		$this->set('project_type', $project_type);
		$this->set("projects", $project);
		$this->set('crumb', $crumb);
		$this->set(compact(
				"project_id", "project_name", "workspace_id", "project_type", "myWorkspaceslistByproject","priorty_data", "allProjects"
			)
		);
		
		
		//$this->set('wsp_tasks_data', $wsp_tasks_data);
		/************ Json ******************/
		

$mindate = '';
$maxdate = '';
$curr_date = date("Y-m-d");
$data = $link = '';
$i = 1;
$project_level = 0;
$workspacEstimateCost = 0;
$workspacSpendCost = 0; 

// pr($wsp_tasks_data);
$priorty_data = 0;
if( isset($project_id) && !empty($project_id) ){
	$project_crictical_cnt = $this->objView->loadHelper('Permission')->project_critical_count($project_id);
	if( isset($project_crictical_cnt) && !empty($project_crictical_cnt) ){
		$priorty_data = $project_crictical_cnt[0][0]['total_critical'];
	}
	
}
			
if(isset($wsp_tasks_data) && !empty($wsp_tasks_data)){
	foreach ($wsp_tasks_data as $key => $value) {
		$project_role = $value['user_permissions']['role'];
		$project_data = $value['projects'];
		$project_id = $value['projects']['id'];
		$project_name = str_replace("&nbsp;", " ", $value['projects']['title']);
		$currency_sign = $value['currencies']['sign'];
		$wsp_data = $value['workspaces'];
		$other_data = $value[0];
		$all_tasks = $other_data['all_tasks'];
		$total_tasks = $other_data['total_tasks'];
		$wsp_status = $other_data['wsp_status'];
		$non_tasks = $other_data['NON'];
		$pnd_tasks = $other_data['PND'];
		$prg_tasks = $other_data['PRG'];
		$ovd_tasks = $other_data['OVD'];
		$cmp_tasks = $other_data['CMP'];
		$spend_total = $other_data['spend_total'];
		$estimate_total = $other_data['estimate_total'];
		$prj_type = $other_data['prj_type'];
		$high_risk = (!empty($other_data['high_risk'])) ? $other_data['high_risk'] : 0;
		$severe_risk = (!empty($other_data['severe_risk'])) ? $other_data['severe_risk'] : 0;
		 
		$project_level = 0;
		if($project_role == 'Creator' || $project_role == 'Owner' || $project_role == 'Group Owner'){
			$project_level = 1;
		}

		$crysymbol = html_entity_decode('&pound;');
		if($currency_sign == 'USD') {
			$projectCurrencysymbol = html_entity_decode('&dollar;');
		}
		else if($currency_sign == 'GBP') {
			$projectCurrencysymbol = html_entity_decode('&pound;');
		}
		else if($currency_sign == 'EUR') {
			$projectCurrencysymbol = html_entity_decode('&euro;');
		}
		else if($currency_sign == 'DKK' || $currency_sign == 'ISK') {
			$projectCurrencysymbol = 'Kr';
		}
		if( isset($projectCurrencysymbol) && !empty($projectCurrencysymbol) ){
			$crysymbol = $projectCurrencysymbol;
		}


		$startdate = empty($wsp_data['start_date']) ? date("d-m-Y") : date("d-m-Y", strtotime($wsp_data['start_date']));
     	$enddate = empty($wsp_data['end_date']) ? date("d-m-Y") : date("d-m-Y", strtotime($wsp_data['end_date']));

    	$total_elements = $total_tasks;
    	$total_completed = $cmp_tasks;

		$percent = 0;
		if( $total_elements > 0 ) {
			$percent = round((( $total_completed/$total_elements ) * 100), 0, 1);
		}
		if(isset($wsp_data['sign_off']) && !empty($wsp_data['sign_off'])){
			$percent = 100;
			$toc = 'bg-red';
		}

		$total_elem = 0;
		if(isset($total_elements) && ($total_elements > 0)){
			$total_elem = $total_elements - $total_completed;
		}

		$percent = $percent/100;
		$percent = ($percent > 0) ? $percent : 0.0010;

		$startdate_tip = empty($wsp_data['start_date']) ? date("d F Y") : date("d F Y", strtotime($wsp_data['start_date']));

		$enddate_tip = empty($wsp_data['end_date']) ? date("d F Y") : date("d F Y", strtotime($wsp_data['end_date']));

		$enddate_tip_o = empty($wsp_data['end_date']) ? date("d F Y") : date("d F Y", strtotime($wsp_data['end_date']));

		$durations = 0;
		if( !empty($wsp_data['start_date']) && !empty($wsp_data['end_date']) ){
			$enddate_tip = empty($enddate_tip) ? '' : date("d F Y", strtotime("+1 day",strtotime( $enddate_tip)));
			$durations = dateDiff($startdate_tip, $enddate_tip);
		}

		if( empty($wsp_data['start_date']) ){
			$startdate_tip = 'N/A';
		}

		if( empty($wsp_data['end_date']) ){
			$enddate_tip_o = 'N/A';
		}


		$workspacEstimateCost = $estimate_total;
		$workspacSpendCost = $spend_total;

		$all_elements = explode(',', $all_tasks);

		if( $project_level == 1 ) {
			$wsp_html = "<a class='perm' href='".SITEURL."costs/index/".$prj_type.":".$project_id."'><i class='fa-manage-cost' style='height:22px;width:23px;'></i></a>";
			$wsp_html .= "<a class='perm exclamation' href='".SITEURL."risks/index/".$project_id."/wsp:".$wsp_data['id']."'><i class='fa fa-exclamation' aria-hidden='true'></i></a>";
			$wsp_html .= "<a class='perm' href='".TASK_CENTERS.$project_id."'><i class='ico-task-center' style='height:23px;width:23px;'></i></a>";

		}else{
			$wsp_html = "<a class='perm' href='".SITEURL."costs/index/".$prj_type.":".$project_id."'><i class='fa-manage-cost' style='height:22px;width:23px;'></i></a>";
			$wsp_html .= "<a class='perm exclamation' href='".SITEURL."risks/index/".$project_id."/wsp:".$wsp_data['id']."'><i class='fa fa-exclamation' aria-hidden='true'></i></a>";
			$wsp_html .= "<a class='perm' href='".TASK_CENTERS.$project_id."'><i class='ico-task-center' style='height:23px;width:23px;'></i></a>";
		}
			$wsp_html .="<a href='".SITEURL."projects/manage_elements/".$project_id."/".$wsp_data['id']."'><i class='fa fa-th'></i></a>";


		$wetip = str_replace("'", "", $wsp_data['title']);
		$wetip = str_replace('"', "", $wetip);

		$tip = "<div class='gantWorkspace'><span class='wpheader'><span class='ganttDep-tital workspace-title'>Workspace Details</span><div class='anchor_wrap'>".$wsp_html."</div></span><span class='wptitle'>".str_replace("&nbsp;"," ",$wetip)."</span><div class='wpdeails'><span class='wpstdate'>Start: ".$startdate_tip."</span><span class='wpenddate'>End: ".$enddate_tip_o."</span><span class='wpduration'>Duration: ".$durations." days</span><span style='border-bottom:1px solid #ccc; margin-top:5px !important; margin-bottom:5px !important;'></span><span class='wptcomped'>Tasks Completed: ".$total_completed."</span><span class='wptcomped'>Tasks Outstanding: ".$total_elem."</span>";

		if( $project_level == 1) {
			$tip .= "<span style='border-bottom:1px solid #ccc; margin-top:5px !important; margin-bottom:5px !important;'></span><span class='wpbudget'>Estimated: ".$crysymbol.number_format($workspacEstimateCost,2, '.', '')."</span><span class='wpbudget'>Spend: ".$crysymbol.number_format($workspacSpendCost,2, '.', '')."</span><span style='border-bottom:1px solid #ccc; margin-top:5px !important; margin-bottom:5px !important;'></span>";
			$tip .= "<span class='wpbudget'>Task Risks: ".$severe_risk." SEVERE, ".$high_risk." HIGH</span>";
			$tip .= "</div></div>";

		}

		$wstatus = $wsp_status;
		$data .= '{"id": "parent_' . $wsp_data['id'] . '","start_date":"'.$startdate.'","end_date":"'.$enddate.'","dur":"'.$durations.'","color":"'.$wsp_data['color_code'] .'", "t_id":"workspace_' . $wsp_data['id'] . '","status":"'.$wstatus.'","tip": "'.$tip.'","text": "' . str_replace("&nbsp;"," ",$wetip) . '","type":"workspace", "order": "' . $i . '", progress: "'.$percent.'", open: true},';

		if(isset($all_tasks) && !empty($all_tasks)){
			$all_element_data = $this->objView->loadHelper('Permission')->task_detail($all_tasks);
			//pr($all_element_data);
			foreach ($all_element_data as $task_key => $task_val) {
				$risk_det = $task_val[0];
				$element_detail = $task_val['elements'];
				$otdata = $task_val[0];
				$element_status = $otdata['task_status'];
				$element_assignment = $task_val['element_assignments'];
				$assigned_to = $element_assignment['assigned_to'];
				$created_by = $element_assignment['created_by'];
				$el_high_risk = $otdata['high_risk'];
				$el_severe_risk = $otdata['severe_risk'];
				$task_all_dependancy = $task_val[0]['ele_all_dependancy'];

				if((!empty($element_detail['start_date']) && date("Y-m-d",strtotime($element_detail['start_date'])) < $mindate)  || empty($mindate)){
                    $mindate = !empty($element_detail['start_date']) ? date("Y-m-d",strtotime($element_detail['start_date'])) : '';
                }
                if((!empty($element_detail['end_date']) && date("Y-m-d",strtotime($element_detail['end_date'])) > $maxdate )  || empty($maxdate) ){
                    $maxdate = !empty($element_detail['end_date']) ? date("Y-m-d",strtotime($element_detail['end_date'])) : '';
                }

                $start = empty($element_detail['start_date']) ? $enddate : date("d-m-Y", strtotime($element_detail['start_date']));
			    $end = empty($element_detail['end_date']) ? $enddate : date("d-m-Y", strtotime($element_detail['end_date']));

			    $progress_data = $this->objView->loadHelper('Permission')->element_progress_days($start, $end, $element_status);
			    $days_data = ["total_days"=>$progress_data['total_days'], "remaining_days"=>$progress_data['total_days'], "left_days"=>$progress_data['total_days']];
			    $progress = $progress_data['progress'];

			    $days = $days_data;

			    $daysLeft = daysLeft(date('Y-m-d', strtotime($start)), date('Y-m-d', strtotime($end)));
				$remainingDays = 100 - $daysLeft;
				$day_text = "N/A";
				if(  $element_status == 'PND' ) {
					$daysLeft = daysLeft( date('Y-m-d'), date('Y-m-d', strtotime($element_detail['start_date'])));
					echo "<style>#element_".$element_detail['id']." .gantt_task_progress{ display :none ;}</style>";
					echo "<style>#element_".$element_detail['id'].".gantt_task_line { border-color: #000 !important; background:none;}</style>";
				}
				else if(  $element_status == 'PRG' ) {
					$daysLeft = daysLeft( date('Y-m-d'), date('Y-m-d', strtotime($element_detail['end_date'])));

					if($mode=='year'){
						echo "<style>#element_".$element_detail['id'].".gantt_task_line .gantt_task_progress { width:100% !important;}</style>";
					}

				}
				else if(  $element_status == 'CMP' ) {
					$remainingDays = 100;
					$daysLeft = 0;

				}
				else if(  $element_status == 'OVD' ) {
					$daysLeft = daysLeft( date('Y-m-d', strtotime($element_detail['end_date'])), date('Y-m-d'));
				}


				if( isset($otdata['dep_is_critical']) && $otdata['dep_is_critical'] == 1 ){
					$crit = 'television-arrow-red';
				} else {
					$crit = 'television-arrow';
				}
				if( isset($otdata['dependencies'] ) && !empty($otdata['dependencies']) ){
					$dep_el_id = explode(",", $otdata['dependencies']);
					//$link .= '{id:"' . $element_detail['id'] . '",source:"' . $element_detail['id'] . '",target:"' . $otdata['dependencies'] . '",type:"1"},';
				} 
				
				if( isset($task_all_dependancy) && !empty($task_all_dependancy) ){
					$listDepndencese = json_decode($task_all_dependancy);				 
					if( count($listDepndencese) > 0 ){
						foreach($listDepndencese as $list_dep){
							$link .= '{id:"' . $element_detail['id'] . '",source:"' . $element_detail['id'] . '",target:"' . $list_dep. '",type:"1"},';
						}
					}
				}
				

				$eleStart = empty($element_detail['start_date']) ? '' : date("d F Y", strtotime($element_detail['start_date']));
				$eleEndDate = empty($element_detail['end_date']) ? '' : date("d F Y", strtotime($element_detail['end_date']));
				$edurations = 0;
				if( !empty($element_detail['start_date']) && !empty($element_detail['end_date']) ){
					$eleEndDateN = empty($element_detail['end_date']) ? '' : date("d F Y", strtotime("+1 day",strtotime( $element_detail['end_date'])));
					$edurations = dateDiff($eleStart, $eleEndDateN);
				} else {
					$eleStart = 'N/A';
					$eleEndDate = 'N/A';
				}
				$elementStatus = 'N/A';
				if($element_status == 'PND'){
                    $elementStatus = 'Starts in '.$daysLeft.' days';
                }else if($element_status == 'OVD'){
                    $elementStatus = 'Overdue '.$daysLeft.' days';
                }else if($element_status == 'PRG'){
                    $elementStatus = 'Due '.$daysLeft.' days';
                }else if($element_status == 'CMP'){
                    $elementStatus = 'Completed';
                }

				$setPriority = 'No';
				if( isset($otdata['dep_is_critical']) && $otdata['dep_is_critical'] == 1 ){
					$setPriority = 'Yes';
				}

				$assignedto = 'N/A';
				$assignedby = 'N/A';
				if(!empty($assigned_to)){
					//$assignedto = $this->Common->userFullname($assigned_to);
					$assignedto = (isset($otdata['assigned_touser']) && !empty($otdata['assigned_touser'])) ? $otdata['assigned_touser'] : 'N/A';
				}
				if(!empty($created_by)){
					//$assignedby = $this->Common->userFullname($created_by);
					$assignedby = (isset($otdata['assigned_createuser']) && !empty($otdata['assigned_createuser'])) ? $otdata['assigned_createuser'] : 'N/A';
				}

				if( $project_level == 1) {
					$task_html = "<a class='perm' href='".SITEURL."costs/index/".$prj_type.":".$project_id."'><i class='fa-manage-cost' style='height:22px;width:23px;'></i></a>";
					$task_html .= "<a class='perm exclamation' href='".SITEURL."risks/index/".$project_id."/".$element_detail['id']."'><i class='fa fa-exclamation' aria-hidden='true'></i></a>";
					$task_html .= "<a class='perm' href='".TASK_CENTERS.$project_id."'><i class='ico-task-center' style='height:23px;width:23px;'></i></a>";

				}else{
					$task_html = "<a class='perm' href='".SITEURL."costs/index/".$prj_type.":".$project_id."'><i class='fa-manage-cost' style='height:22px;width:23px;'></i></a>";
					$task_html .= "<a class='perm exclamation' href='".SITEURL."risks/index/".$project_id."/".$element_detail['id']."'><i class='fa fa-exclamation' aria-hidden='true'></i></a>";
				}

				$engaged_by = '';
				$assign_class = 'not-avail';
				$assign_tip = 'No Assignment';
				$engTip = 'N/A';
				if(isset($element_detail['engaged_by']) && !empty($element_detail['engaged_by'])){
					//$engaged_by = $this->Common->userFullname($element_detail['engaged_by']);
					//$engaged_by = $otdata['engaged_touser'];

					if($element_detail['reaction'] == 1) {
						$engTip = "<i class='fa fa-check text-success' aria-hidden='true'></i> Accepted";
					}
					else if($element_detail['reaction'] == 2) {
						$engTip = "<i class='fa fa-check text-danger' aria-hidden='true'></i> Not accepted";
					}
					else if($element_detail['reaction'] == 3) {
						$engTip = "<i class='fa fa-square' aria-hidden='true'></i> Disengaged";
					}
					else{
						$engTip = 'N/A';
					}
				}

				$eetip = str_replace("'", "", $element_detail['title']);
				$eetip = str_replace('"', "", $eetip);

				$etip_html = "<div class='ganttDep' style='display:none;min-width: 240px'><span class='wpheader'><span class='ganttDep-tital'>Task Details</span><div class='anchor_wrap'>".$task_html."<i class='fa ".$crit." get_depend_new' task_id='".$element_detail['id']."'></i><a task_id='".$element_detail['id']."' id='element_".$element_detail['id']."' data-toggle='modal' data-target='#modal_pop' data-remote='".SITEURL."users/popup_box/".$element_detail['id']."/element/".$project_id."' style='cursor:pointer;' ><i class='icon_element_add_black' ></i></a></div></span><div class='dep_data'></div></div>   <div class='gantWorkspace'><span class='wpheader'><span class='ganttDep-tital'>Task Details</span><div class='anchor_wrap'>".$task_html."<i class='fa ".$crit." get_depend' task_id='".$element_detail['id']."'></i><a task_id='".$element_detail['id']."' id='element_".$element_detail['id']."' data-toggle='modal' data-target='#modal_pop' data-remote='".SITEURL."users/popup_box/".$element_detail['id']."/element/".$project_id."' style='cursor:pointer;' ><i class='icon_element_add_black' ></i></a></div></span><span class='wptitle'>".str_replace("&nbsp;"," ",$eetip)."</span><div class='wpdeails'><span class='wpstdate'>Start: ".$eleStart."</span><span class='wpenddate'>End: ".$eleEndDate."</span><span class='wpduration'>Duration: ".$edurations." days</span><span class='wptcomped'>Priority: ".$setPriority."</span><span class='wptcomped'>Status: ".$elementStatus."</span><span style='border-bottom:1px solid #ccc; margin-top:5px !important; margin-bottom:5px !important;'></span><span class='wptcomped'>Assigned to: ".$assignedto."</span><span class='wptcomped'>Assigned by: ".$assignedby."</span><span class='wptcomped'>Schedule: ".$engTip."</span>";

				if( $project_level == 1) {
					$etip_html .= "<span style='border-bottom:1px solid #ccc; margin-top:5px !important; margin-bottom:5px !important;'></span><span class='wpbudget'>Estimated: ".$crysymbol.number_format($workspacEstimateCost,2, '.', '')."</span><span class='wpbudget'>Spend: ".$crysymbol.number_format($workspacSpendCost,2, '.', '')."</span><span style='border-bottom:1px solid #ccc; margin-top:5px !important; margin-bottom:5px !important;'></span>";

					$rr = null;
					// $RTip = element_risks($project_id, $element_detail['id']);
					// if(isset($RTip) && !empty($RTip)){
					// 	$rr = $RTip;
					// }
					//high_risk
					$risk_high = (isset($risk_det['high_risk']) && !empty($risk_det['high_risk'])) ? $risk_det['high_risk'] : 0;
					$risk_severe_risk =  (isset($risk_det['severe_risk']) && !empty($risk_det['severe_risk'])) ? $risk_det['severe_risk'] : 0;

					$etip_html .= "<span class='wpbudget'>Risks: $risk_high SEVERE, $risk_severe_risk HIGH</span></div></div>";

				}

				$etip = str_replace("'", "", $element_detail['title']);
				$etip = str_replace('"', "", $etip);

	            $data .= '{"id": "' . $element_detail['id'] . '","element_tip": "'.$etip_html.'","area":"'.$wsp_data['title'].'","dur":"'.$days['total_days'].'","total_days":"'.$days['total_days'].'","remaining_days":"'.$days['remaining_days'].'","left_days":"'.$days['left_days'].'","color":"'.$element_detail['color_code'] .'", "t_id":"element_' . $element_detail['id'] . '","text": "' . str_replace("&nbsp;"," ",$etip) . '","start_date": "' . $start . '", "end_date":"' . $end . '", "status":"' . $element_status . '", "order": "' . $element_detail['id'] . '", "progress": "'.$progress.'", "parent": "parent_' . $wsp_data['id'] . '", open: true},';
	            $i++;
			}
		}
	}
}
			
			
		
		$this->set('data',$data);
		$this->set('link',$link);
		/********************************/
		$this->render("gantt/event_gantt");
	}

	public function get_workspaces_by_project($pid = null) {
		$this->autoRender = false;
		$this->layout = false;

		$user_id = $this->Auth->user('id');
		$project_id = $workspace_id = '';

		if (!isset($this->params->named) || empty($this->params->named)) {

			$project_type = CheckProjectType($pid, $user_id);
			$project_id = $pid;
		}

		if (isset($this->params->named['m_project']) && !empty($this->params->named['m_project'])) {
			$project_type = 'm_project';
			$project_id = $this->params->named['m_project'];
		} else if (isset($this->params->named['r_project']) && !empty($this->params->named['r_project'])) {
			$project_type = 'r_project';
			$project_id = $this->params->named['r_project'];
		} else if (isset($this->params->named['g_project']) && !empty($this->params->named['g_project'])) {
			$project_type = 'g_project';
			$project_id = $this->params->named['g_project'];
		}
		if (isset($this->params->named['workspace_id']) && !empty($this->params->named['workspace_id'])) {
			$workspace_id = $this->params->named['workspace_id'];
		}
		if (isset($this->params['named']['mode']) && !empty($this->params['named']['mode']) && $this->params['named']['mode'] == 'month') {
			$this->set('mode', $mode = 'month');
		} else if (isset($this->params['named']['mode']) && !empty($this->params['named']['mode']) && $this->params['named']['mode'] == 'year') {
			$this->set('mode', $mode = 'year');
		} else if (isset($this->params['named']['mode']) && !empty($this->params['named']['mode']) && $this->params['named']['mode'] == 'week') {
			$this->set('mode', $mode = 'week');
		} else {
			$this->set('mode', $mode = 'year');
		}

		$status = isset($this->data['status']) && !empty($this->data['status']) ? $this->data['status'] : '';

		$criticalStatus = isset($this->data['criticalStatus']) && !empty($this->data['criticalStatus']) && $this->data['criticalStatus'] == 1 ? $this->data['criticalStatus'] : '';

		$sharing_type = isset($this->data['sharing_type']) && !empty($this->data['sharing_type']) ? $this->data['sharing_type'] : '';

		$assignmentStatus = isset($this->data['assign_type']) && !empty($this->data['assign_type']) && $this->data['assign_type'] == 'assignfilter' ? $this->data['assign_type'] : '';

		$uId = (isset($this->data['user_id']) && !empty($this->data['user_id'])) ? $this->data['user_id'] : 0;
		$gId = (isset($this->data['group_id']) && !empty($this->data['group_id'])) ? $this->data['group_id'] : 0;

		if ($this->RequestHandler->isAjax()) {
			// Find All workspaces by project id

			$assignUserId = $uId = (isset($this->data['user_id']) && !empty($this->data['user_id'])) ? $this->data['user_id'] : 0;
			$gId = (isset($this->data['group_id']) && !empty($this->data['group_id'])) ? $this->data['group_id'] : 0;

			$this->set( compact("project_id", "workspace_id", "assignUserId","status","criticalStatus","assignmentStatus") );

			$wsp_tasks_data = $this->objView->loadHelper('Permission')->wsp_tasks_data_filter($project_id,$workspace_id,$assignmentStatus);
			$this->set("wsp_tasks_data_filter",$wsp_tasks_data);


			$this->render('gantt/get_workspaces_by_project');
			$this->layout = false;
			// exit;
		}
	}

	public function get_element_by_workspaces() {
		$this->autoRender = false;
		$this->layout = false;

		$user_id = $this->Auth->user('id');
		$pid = $project_id = $workspace_id = '';
		$project_id = $pid;

		if (!isset($this->params->named) || empty($this->params->named)) {

			$project_type = CheckProjectType($pid, $user_id);
			$project_id = $pid;
		}

		if (isset($this->params->named['m_project']) && !empty($this->params->named['m_project'])) {
			$project_type = 'm_project';
			$project_id = $this->params->named['m_project'];
		} else if (isset($this->params->named['r_project']) && !empty($this->params->named['r_project'])) {
			$project_type = 'r_project';
			$project_id = $this->params->named['r_project'];
		} else if (isset($this->params->named['g_project']) && !empty($this->params->named['g_project'])) {
			$project_type = 'g_project';
			$project_id = $this->params->named['g_project'];
		}


		if (isset($this->params['named']['mode']) && !empty($this->params['named']['mode']) && $this->params['named']['mode'] == 'month') {
			$this->set('mode', $mode = 'month');
		} else if (isset($this->params['named']['mode']) && !empty($this->params['named']['mode']) && $this->params['named']['mode'] == 'year') {
			$this->set('mode', $mode = 'year');
		} else if (isset($this->params['named']['mode']) && !empty($this->params['named']['mode']) && $this->params['named']['mode'] == 'week') {
			$this->set('mode', $mode = 'week');
		} else {
			$this->set('mode', $mode = 'year');
		}
		if (isset($this->params->named['workspace_id']) && !empty($this->params->named['workspace_id'])) {
			$workspace_id = $this->params->named['workspace_id'];
		}
		$status = isset($this->data['status']) && !empty($this->data['status']) ? $this->data['status'] : '';
		$criticalStatus = isset($this->data['criticalStatus']) && !empty($this->data['criticalStatus']) && $this->data['criticalStatus'] == 1 ? $this->data['criticalStatus'] : '';

		$sharing_type = isset($this->data['sharing_type']) && !empty($this->data['sharing_type']) ? $this->data['sharing_type'] : '';

		$assignmentStatus = isset($this->data['assign_type']) && !empty($this->data['assign_type']) && $this->data['assign_type'] == 'assignfilter' ? $this->data['assign_type'] : '';

		if ($this->RequestHandler->isAjax()) {
			// Find All workspaces by project id

			$assignUserId = $uId = (isset($this->data['user_id']) && !empty($this->data['user_id'])) ? $this->data['user_id'] : 0;
			$gId = (isset($this->data['group_id']) && !empty($this->data['group_id'])) ? $this->data['group_id'] : 0;

			$this->set( compact("project_id", "workspace_id", "assignUserId","status","criticalStatus","assignmentStatus") );

			$wsp_tasks_data = $this->objView->loadHelper('Permission')->wsp_tasks_data_filter($project_id,$workspace_id,$assignmentStatus);
			$this->set("wsp_tasks_data",$wsp_tasks_data);

			$this->render('gantt/get_element_by_workspaces');
			$this->layout = false;
			// exit;
		}
	}

	
	/*****************************************************************/
	function daysRemaining($start,$end,$status){
		error_reporting(0);
		$end = date('d-m-Y', strtotime($end . ' +1 day'));
		$curr_date = date("d-m-Y");
		$total_days = round(abs(strtotime($end) - strtotime($start))/(60*60*24));
	   // $remaining_days = floor(abs(strtotime($end) - strtotime($curr_date))/(60*60*24))+1;
		$remaining_days = round(abs(strtotime($end) - strtotime($curr_date))/(60*60*24));
		$left_days = round(abs(strtotime($curr_date) - strtotime($start))/(60*60*24))-1;
		if ($status == 'NON') {
			return array("total_days"=>0,"remaining_days"=>0,"left_days"=>0);
		} else if ($status == 'PND') {
			$remaining = round(abs(strtotime($curr_date) - strtotime($start))/(60*60*24));
			return array("total_days"=>$total_days,"remaining_days"=>$remaining,"left_days"=>0);
		} else if ($status == 'PRG') {
			return array("total_days"=>$total_days,"remaining_days"=>$remaining_days,"left_days"=>$left_days);
		} else if ($status == 'OVD') {
			$left = round(abs(strtotime($curr_date) - strtotime($end))/(60*60*24));
			return array("total_days"=>$total_days,"remaining_days"=>0,"left_days"=>$left);
		} else if ($status == 'CMP') {
			return array("total_days"=>$total_days,"remaining_days"=>0,"left_days"=>0);
		} else {
			return array("total_days"=>0,"remaining_days"=>0,"left_days"=>0);
		}
	}
	function progressing($start,$end,$status){
		error_reporting(0);
		if ($status == 'NON') {
			return $progress = '0.0';
		} else if ($status == 'PND') {
			return $progress = '0.0';
		} else if ($status == 'PRG') {
			$curr_date = date("d-m-Y");
			$total_days = round(abs(strtotime($end) - strtotime($start))/(60*60*24));
			$remaining_days = round(abs(strtotime($end) - strtotime($curr_date))/(60*60*24));
			$left_days = round(abs(strtotime($curr_date) - strtotime($start))/(60*60*24));
		   // return round( $left_days/$total_days*1, 1, PHP_ROUND_HALF_UP);
			return $left_days/$total_days*1;
		} else if ($status == 'OVD') {
			return $progress = '1.0';
		} else if ($status == 'CMP') {
			return $progress = '1.0';
		} else {
			return '0.0';
		}
	} 
	
	
	public function gantt_demo(){
		
		$this->set('title_for_layout', __('Planner', true));
		$this->layout = "calander";
		$user_id = $this->Auth->user('id');
		$project_id = '';
		 
		if( $_SERVER['HTTP_HOST'] != LOCALIP )  {

			if( $this->Session->read('Auth.User.role_id') == 3 ){
				$this->redirect(['controller' => 'organisations','action' => 'dashboard']);
			}
			if( $this->Session->read('Auth.User.role_id') == 1 ){
				$this->redirect(['controller' => 'dashboard','action' => 'index']);
			}
		}

		$project_type = 'm_project';
		// project type coming from UserPermission
		if (!isset($this->params->named) || empty($this->params->named)) {
			$project_type = CheckProjectType($pid, $user_id);
			$project_id = $pid;
		}

		if (isset($this->params->named['m_project']) && !empty($this->params->named['m_project'])) {
			$project_type = 'm_project';
			$project_id = $this->params->named['m_project'];
		} else if (isset($this->params->named['r_project']) && !empty($this->params->named['r_project'])) {
			$project_type = 'r_project';
			$project_id = $this->params->named['r_project'];
		} else if (isset($this->params->named['g_project']) && !empty($this->params->named['g_project'])) {
			$project_type = 'g_project';
			$project_id = $this->params->named['g_project'];
		}

		if (isset($this->params['named']['mode']) && !empty($this->params['named']['mode']) && $this->params['named']['mode'] == 'month') {
			$this->set('mode', $mode = 'month');
		} else if (isset($this->params['named']['mode']) && !empty($this->params['named']['mode']) && $this->params['named']['mode'] == 'year') {
			$this->set('mode', $mode = 'year');
		} else if (isset($this->params['named']['mode']) && !empty($this->params['named']['mode']) && $this->params['named']['mode'] == 'week') {
			$this->set('mode', $mode = 'week');
		} else {
			$this->set('mode', $mode = 'year');
		}

		$project = $this->Project->find("first", array("conditions" => array("Project.id" => $project_id),  'recursive' => -1 ));

		$project_name = htmlentities(str_replace("&nbsp;", " ", $project['Project']['title']), ENT_QUOTES);
		$workspace_id = isset($this->params->named['workspace']) && !empty($this->params->named['workspace']) ? $this->params->named['workspace'] : '';
		$this->Project->id = $project_id;

		if ($project_id == '' || !$this->Project->exists() && $project_type == 'm_project') {
			return $this->redirect(SITEURL . 'projects/lists');
		}
		if ($project_id == '' || !$this->Project->exists() && $project_type == 'r_project') {
			return $this->redirect(SITEURL . 'projects/share_lists');
		}
		if ($project_id == '' || !$this->Project->exists() && $project_type == 'g_project') {
			return $this->redirect(SITEURL . 'groups/shared_projects');
		}


		// owner level project coming from UserPermission
		$allProjects  = array();
		$allProjects = $this->Common->getAssestsProjects($this->Session->read('Auth.User.id'),2);

		// Find All workspaces by project id
		$myWorkspaceslistByproject = [];
		$all_wsp = $this->objView->loadHelper('Permission')->wsp_of_project($project_id, $user_id);
		if(isset($all_wsp) && !empty($all_wsp)){
			foreach ($all_wsp as $key => $value) {
				$myWorkspaceslistByproject[$value['Workspace']['id']] = $value['Workspace']['title'];
			}
		}	


		$crumb = ['last' => ['Gantt']];

		if (isset($project_id) && !empty($project_id)) {
			$project_title = (isset($project) && !empty($project)) ? $project['Project']['title'] : '';
			$crumb1 = [
				'Project' => [
					'data' => [
						'url' => '/projects/index/' . $project_id,
						'class' => 'tipText',
						'title' => $project_title,
						'data-original-title' => $project_title,
					],
				],
			];
			$crumb = array_merge($crumb1, $crumb);
		} else {
			$crumb = array_merge(['Projects' => '/projects/lists'], $crumb);
		}

		$priorty_data = array();
		$wsp_tasks_data = $this->objView->loadHelper('Permission')->wsp_tasks_data($project_id); 
		
		$this->set('project_type', $project_type);
		$this->set("projects", $project);
		$this->set('crumb', $crumb);
		$this->set(compact(
				"project_id", "project_name", "workspace_id", "project_type", "myWorkspaceslistByproject","priorty_data", "allProjects"
			)
		);
		
		
		/************************************************************/
		
		/************ Json ******************/
		

$mindate = '';
$maxdate = '';
$curr_date = date("Y-m-d");
$data = $link = '';
$i = 1;
$project_level = 0;
$workspacEstimateCost = 0;
$workspacSpendCost = 0; 

// pr($wsp_tasks_data);
$priorty_data = 0;
if( isset($project_id) && !empty($project_id) ){
	$project_crictical_cnt = $this->objView->loadHelper('Permission')->project_critical_count($project_id);
	if( isset($project_crictical_cnt) && !empty($project_crictical_cnt) ){
		$priorty_data = $project_crictical_cnt[0][0]['total_critical'];
	}
	
}
			
if(isset($wsp_tasks_data) && !empty($wsp_tasks_data)){
	foreach ($wsp_tasks_data as $key => $value) {
		$project_role = $value['user_permissions']['role'];
		$project_data = $value['projects'];
		$project_id = $value['projects']['id'];
		$project_name = str_replace("&nbsp;", " ", $value['projects']['title']);
		$currency_sign = $value['currencies']['sign'];
		$wsp_data = $value['workspaces'];
		$other_data = $value[0];
		$all_tasks = $other_data['all_tasks'];
		$total_tasks = $other_data['total_tasks'];
		$wsp_status = $other_data['wsp_status'];
		$non_tasks = $other_data['NON'];
		$pnd_tasks = $other_data['PND'];
		$prg_tasks = $other_data['PRG'];
		$ovd_tasks = $other_data['OVD'];
		$cmp_tasks = $other_data['CMP'];
		$spend_total = $other_data['spend_total'];
		$estimate_total = $other_data['estimate_total'];
		$prj_type = $other_data['prj_type'];
		$high_risk = (!empty($other_data['high_risk'])) ? $other_data['high_risk'] : 0;
		$severe_risk = (!empty($other_data['severe_risk'])) ? $other_data['severe_risk'] : 0;
		 
		$project_level = 0;
		if($project_role == 'Creator' || $project_role == 'Owner' || $project_role == 'Group Owner'){
			$project_level = 1;
		}

		$crysymbol = html_entity_decode('&pound;');
		if($currency_sign == 'USD') {
			$projectCurrencysymbol = html_entity_decode('&dollar;');
		}
		else if($currency_sign == 'GBP') {
			$projectCurrencysymbol = html_entity_decode('&pound;');
		}
		else if($currency_sign == 'EUR') {
			$projectCurrencysymbol = html_entity_decode('&euro;');
		}
		else if($currency_sign == 'DKK' || $currency_sign == 'ISK') {
			$projectCurrencysymbol = 'Kr';
		}
		if( isset($projectCurrencysymbol) && !empty($projectCurrencysymbol) ){
			$crysymbol = $projectCurrencysymbol;
		}


		$startdate = empty($wsp_data['start_date']) ? date("d-m-Y") : date("d-m-Y", strtotime($wsp_data['start_date']));
     	$enddate = empty($wsp_data['end_date']) ? date("d-m-Y") : date("d-m-Y", strtotime($wsp_data['end_date']));

    	$total_elements = $total_tasks;
    	$total_completed = $cmp_tasks;

		$percent = 0;
		if( $total_elements > 0 ) {
			$percent = round((( $total_completed/$total_elements ) * 100), 0, 1);
		}
		if(isset($wsp_data['sign_off']) && !empty($wsp_data['sign_off'])){
			$percent = 100;
			$toc = 'bg-red';
		}

		$total_elem = 0;
		if(isset($total_elements) && ($total_elements > 0)){
			$total_elem = $total_elements - $total_completed;
		}

		$percent = $percent/100;
		$percent = ($percent > 0) ? $percent : 0.0010;

		$startdate_tip = empty($wsp_data['start_date']) ? date("d F Y") : date("d F Y", strtotime($wsp_data['start_date']));

		$enddate_tip = empty($wsp_data['end_date']) ? date("d F Y") : date("d F Y", strtotime($wsp_data['end_date']));

		$enddate_tip_o = empty($wsp_data['end_date']) ? date("d F Y") : date("d F Y", strtotime($wsp_data['end_date']));

		$durations = 0;
		if( !empty($wsp_data['start_date']) && !empty($wsp_data['end_date']) ){
			$enddate_tip = empty($enddate_tip) ? '' : date("d F Y", strtotime("+1 day",strtotime( $enddate_tip)));
			$durations = dateDiff($startdate_tip, $enddate_tip);
		}

		if( empty($wsp_data['start_date']) ){
			$startdate_tip = 'N/A';
		}

		if( empty($wsp_data['end_date']) ){
			$enddate_tip_o = 'N/A';
		}


		$workspacEstimateCost = $estimate_total;
		$workspacSpendCost = $spend_total;

		$all_elements = explode(',', $all_tasks);

		if( $project_level == 1 ) {
			$wsp_html = "<a class='perm' href='".SITEURL."costs/index/".$prj_type.":".$project_id."'><i class='fa-manage-cost' style='height:22px;width:23px;'></i></a>";
			$wsp_html .= "<a class='perm exclamation' href='".SITEURL."risks/index/".$project_id."/wsp:".$wsp_data['id']."'><i class='fa fa-exclamation' aria-hidden='true'></i></a>";
			$wsp_html .= "<a class='perm' href='".TASK_CENTERS.$project_id."'><i class='ico-task-center' style='height:23px;width:23px;'></i></a>";

		}else{
			$wsp_html = "<a class='perm' href='".SITEURL."costs/index/".$prj_type.":".$project_id."'><i class='fa-manage-cost' style='height:22px;width:23px;'></i></a>";
			$wsp_html .= "<a class='perm exclamation' href='".SITEURL."risks/index/".$project_id."/wsp:".$wsp_data['id']."'><i class='fa fa-exclamation' aria-hidden='true'></i></a>";
			$wsp_html .= "<a class='perm' href='".TASK_CENTERS.$project_id."'><i class='ico-task-center' style='height:23px;width:23px;'></i></a>";
		}
			$wsp_html .="<a href='".SITEURL."projects/manage_elements/".$project_id."/".$wsp_data['id']."'><i class='fa fa-th'></i></a>";


		$wetip = str_replace("'", "", $wsp_data['title']);
		$wetip = str_replace('"', "", $wetip);

		$tip = "<div class='gantWorkspace'><span class='wpheader'><span class='ganttDep-tital workspace-title'>Workspace Details</span><div class='anchor_wrap'>".$wsp_html."</div></span><span class='wptitle'>".str_replace("&nbsp;"," ",$wetip)."</span><div class='wpdeails'><span class='wpstdate'>Start: ".$startdate_tip."</span><span class='wpenddate'>End: ".$enddate_tip_o."</span><span class='wpduration'>Duration: ".$durations." days</span><span style='border-bottom:1px solid #ccc; margin-top:5px !important; margin-bottom:5px !important;'></span><span class='wptcomped'>Tasks Completed: ".$total_completed."</span><span class='wptcomped'>Tasks Outstanding: ".$total_elem."</span>";

		if( $project_level == 1) {
			$tip .= "<span style='border-bottom:1px solid #ccc; margin-top:5px !important; margin-bottom:5px !important;'></span><span class='wpbudget'>Estimated: ".$crysymbol.number_format($workspacEstimateCost,2, '.', '')."</span><span class='wpbudget'>Spend: ".$crysymbol.number_format($workspacSpendCost,2, '.', '')."</span><span style='border-bottom:1px solid #ccc; margin-top:5px !important; margin-bottom:5px !important;'></span>";
			$tip .= "<span class='wpbudget'>Task Risks: ".$severe_risk." SEVERE, ".$high_risk." HIGH</span>";
			$tip .= "</div></div>";

		}

		$wstatus = $wsp_status;
		$data .= '{"id": "parent_' . $wsp_data['id'] . '","start_date":"'.$startdate.'","end_date":"'.$enddate.'","dur":"'.$durations.'","color":"'.$wsp_data['color_code'] .'", "t_id":"workspace_' . $wsp_data['id'] . '","status":"'.$wstatus.'","tip": "'.$tip.'","text": "' . str_replace("&nbsp;"," ",$wetip) . '","type":"workspace", "order": "' . $i . '", progress: "'.$percent.'", open: true},';

		if(isset($all_tasks) && !empty($all_tasks)){
			$all_element_data = $this->objView->loadHelper('Permission')->task_detail($all_tasks);
			//pr($all_element_data);
			foreach ($all_element_data as $task_key => $task_val) {
				$risk_det = $task_val[0];
				$element_detail = $task_val['elements'];
				$otdata = $task_val[0];
				$element_status = $otdata['task_status'];
				$element_assignment = $task_val['element_assignments'];
				$assigned_to = $element_assignment['assigned_to'];
				$created_by = $element_assignment['created_by'];
				$el_high_risk = $otdata['high_risk'];
				$el_severe_risk = $otdata['severe_risk'];
				$task_all_dependancy = $task_val[0]['ele_all_dependancy'];

				if((!empty($element_detail['start_date']) && date("Y-m-d",strtotime($element_detail['start_date'])) < $mindate)  || empty($mindate)){
                    $mindate = !empty($element_detail['start_date']) ? date("Y-m-d",strtotime($element_detail['start_date'])) : '';
                }
                if((!empty($element_detail['end_date']) && date("Y-m-d",strtotime($element_detail['end_date'])) > $maxdate )  || empty($maxdate) ){
                    $maxdate = !empty($element_detail['end_date']) ? date("Y-m-d",strtotime($element_detail['end_date'])) : '';
                }

                $start = empty($element_detail['start_date']) ? $enddate : date("d-m-Y", strtotime($element_detail['start_date']));
			    $end = empty($element_detail['end_date']) ? $enddate : date("d-m-Y", strtotime($element_detail['end_date']));

			    $progress_data = $this->objView->loadHelper('Permission')->element_progress_days($start, $end, $element_status);
			    $days_data = ["total_days"=>$progress_data['total_days'], "remaining_days"=>$progress_data['total_days'], "left_days"=>$progress_data['total_days']];
			    $progress = $progress_data['progress'];

			    $days = $days_data;

			    $daysLeft = daysLeft(date('Y-m-d', strtotime($start)), date('Y-m-d', strtotime($end)));
				$remainingDays = 100 - $daysLeft;
				$day_text = "N/A";
				if(  $element_status == 'PND' ) {
					$daysLeft = daysLeft( date('Y-m-d'), date('Y-m-d', strtotime($element_detail['start_date'])));
					echo "<style>#element_".$element_detail['id']." .gantt_task_progress{ display :none ;}</style>";
					echo "<style>#element_".$element_detail['id'].".gantt_task_line { border-color: #000 !important; background:none;}</style>";
				}
				else if(  $element_status == 'PRG' ) {
					$daysLeft = daysLeft( date('Y-m-d'), date('Y-m-d', strtotime($element_detail['end_date'])));

					if($mode=='year'){
						echo "<style>#element_".$element_detail['id'].".gantt_task_line .gantt_task_progress { width:100% !important;}</style>";
					}

				}
				else if(  $element_status == 'CMP' ) {
					$remainingDays = 100;
					$daysLeft = 0;

				}
				else if(  $element_status == 'OVD' ) {
					$daysLeft = daysLeft( date('Y-m-d', strtotime($element_detail['end_date'])), date('Y-m-d'));
				}


				if( isset($otdata['dep_is_critical']) && $otdata['dep_is_critical'] == 1 ){
					$crit = 'television-arrow-red';
				} else {
					$crit = 'television-arrow';
				}
				if( isset($otdata['dependencies'] ) && !empty($otdata['dependencies']) ){
					$dep_el_id = explode(",", $otdata['dependencies']);
					//$link .= '{id:"' . $element_detail['id'] . '",source:"' . $element_detail['id'] . '",target:"' . $otdata['dependencies'] . '",type:"1"},';
				} 
				
				if( isset($task_all_dependancy) && !empty($task_all_dependancy) ){
					$listDepndencese = json_decode($task_all_dependancy);				 
					if( count($listDepndencese) > 0 ){
						foreach($listDepndencese as $list_dep){
							$link .= '{id:"' . $element_detail['id'] . '",source:"' . $element_detail['id'] . '",target:"' . $list_dep. '",type:"1"},';
						}
					}
				}
				

				$eleStart = empty($element_detail['start_date']) ? '' : date("d F Y", strtotime($element_detail['start_date']));
				$eleEndDate = empty($element_detail['end_date']) ? '' : date("d F Y", strtotime($element_detail['end_date']));
				$edurations = 0;
				if( !empty($element_detail['start_date']) && !empty($element_detail['end_date']) ){
					$eleEndDateN = empty($element_detail['end_date']) ? '' : date("d F Y", strtotime("+1 day",strtotime( $element_detail['end_date'])));
					$edurations = dateDiff($eleStart, $eleEndDateN);
				} else {
					$eleStart = 'N/A';
					$eleEndDate = 'N/A';
				}
				$elementStatus = 'N/A';
				if($element_status == 'PND'){
                    $elementStatus = 'Starts in '.$daysLeft.' days';
                }else if($element_status == 'OVD'){
                    $elementStatus = 'Overdue '.$daysLeft.' days';
                }else if($element_status == 'PRG'){
                    $elementStatus = 'Due '.$daysLeft.' days';
                }else if($element_status == 'CMP'){
                    $elementStatus = 'Completed';
                }

				$setPriority = 'No';
				if( isset($otdata['dep_is_critical']) && $otdata['dep_is_critical'] == 1 ){
					$setPriority = 'Yes';
				}

				$assignedto = 'N/A';
				$assignedby = 'N/A';
				if(!empty($assigned_to)){
					//$assignedto = $this->Common->userFullname($assigned_to);
					$assignedto = (isset($otdata['assigned_touser']) && !empty($otdata['assigned_touser'])) ? $otdata['assigned_touser'] : 'N/A';
				}
				if(!empty($created_by)){
					//$assignedby = $this->Common->userFullname($created_by);
					$assignedby = (isset($otdata['assigned_createuser']) && !empty($otdata['assigned_createuser'])) ? $otdata['assigned_createuser'] : 'N/A';
				}

				if( $project_level == 1) {
					$task_html = "<a class='perm' href='".SITEURL."costs/index/".$prj_type.":".$project_id."'><i class='fa-manage-cost' style='height:22px;width:23px;'></i></a>";
					$task_html .= "<a class='perm exclamation' href='".SITEURL."risks/index/".$project_id."/".$element_detail['id']."'><i class='fa fa-exclamation' aria-hidden='true'></i></a>";
					$task_html .= "<a class='perm' href='".TASK_CENTERS.$project_id."'><i class='ico-task-center' style='height:23px;width:23px;'></i></a>";

				}else{
					$task_html = "<a class='perm' href='".SITEURL."costs/index/".$prj_type.":".$project_id."'><i class='fa-manage-cost' style='height:22px;width:23px;'></i></a>";
					$task_html .= "<a class='perm exclamation' href='".SITEURL."risks/index/".$project_id."/".$element_detail['id']."'><i class='fa fa-exclamation' aria-hidden='true'></i></a>";
				}

				$engaged_by = '';
				$assign_class = 'not-avail';
				$assign_tip = 'No Assignment';
				$engTip = 'N/A';
				if(isset($element_detail['engaged_by']) && !empty($element_detail['engaged_by'])){
					//$engaged_by = $this->Common->userFullname($element_detail['engaged_by']);
					//$engaged_by = $otdata['engaged_touser'];

					if($element_detail['reaction'] == 1) {
						$engTip = "<i class='fa fa-check text-success' aria-hidden='true'></i> Accepted";
					}
					else if($element_detail['reaction'] == 2) {
						$engTip = "<i class='fa fa-check text-danger' aria-hidden='true'></i> Not accepted";
					}
					else if($element_detail['reaction'] == 3) {
						$engTip = "<i class='fa fa-square' aria-hidden='true'></i> Disengaged";
					}
					else{
						$engTip = 'N/A';
					}
				}

				$eetip = str_replace("'", "", $element_detail['title']);
				$eetip = str_replace('"', "", $eetip);

				$etip_html = "<div class='ganttDep' style='display:none;min-width: 240px'><span class='wpheader'><span class='ganttDep-tital'>Task Details</span><div class='anchor_wrap'>".$task_html."<i class='fa ".$crit." get_depend_new' task_id='".$element_detail['id']."'></i><a task_id='".$element_detail['id']."' id='element_".$element_detail['id']."' data-toggle='modal' data-target='#modal_pop' data-remote='".SITEURL."users/popup_box/".$element_detail['id']."/element/".$project_id."' style='cursor:pointer;' ><i class='icon_element_add_black' ></i></a></div></span><div class='dep_data'></div></div>   <div class='gantWorkspace'><span class='wpheader'><span class='ganttDep-tital'>Task Details</span><div class='anchor_wrap'>".$task_html."<i class='fa ".$crit." get_depend' task_id='".$element_detail['id']."'></i><a task_id='".$element_detail['id']."' id='element_".$element_detail['id']."' data-toggle='modal' data-target='#modal_pop' data-remote='".SITEURL."users/popup_box/".$element_detail['id']."/element/".$project_id."' style='cursor:pointer;' ><i class='icon_element_add_black' ></i></a></div></span><span class='wptitle'>".str_replace("&nbsp;"," ",$eetip)."</span><div class='wpdeails'><span class='wpstdate'>Start: ".$eleStart."</span><span class='wpenddate'>End: ".$eleEndDate."</span><span class='wpduration'>Duration: ".$edurations." days</span><span class='wptcomped'>Priority: ".$setPriority."</span><span class='wptcomped'>Status: ".$elementStatus."</span><span style='border-bottom:1px solid #ccc; margin-top:5px !important; margin-bottom:5px !important;'></span><span class='wptcomped'>Assigned to: ".$assignedto."</span><span class='wptcomped'>Assigned by: ".$assignedby."</span><span class='wptcomped'>Schedule: ".$engTip."</span>";

				if( $project_level == 1) {
					$etip_html .= "<span style='border-bottom:1px solid #ccc; margin-top:5px !important; margin-bottom:5px !important;'></span><span class='wpbudget'>Estimated: ".$crysymbol.number_format($workspacEstimateCost,2, '.', '')."</span><span class='wpbudget'>Spend: ".$crysymbol.number_format($workspacSpendCost,2, '.', '')."</span><span style='border-bottom:1px solid #ccc; margin-top:5px !important; margin-bottom:5px !important;'></span>";

					$rr = null;
					// $RTip = element_risks($project_id, $element_detail['id']);
					// if(isset($RTip) && !empty($RTip)){
					// 	$rr = $RTip;
					// }
					//high_risk
					$risk_high = (isset($risk_det['high_risk']) && !empty($risk_det['high_risk'])) ? $risk_det['high_risk'] : 0;
					$risk_severe_risk =  (isset($risk_det['severe_risk']) && !empty($risk_det['severe_risk'])) ? $risk_det['severe_risk'] : 0;

					$etip_html .= "<span class='wpbudget'>Risks: $risk_high SEVERE, $risk_severe_risk HIGH</span></div></div>";

				}

				$etip = str_replace("'", "", $element_detail['title']);
				$etip = str_replace('"', "", $etip);

	            $data .= '{"id": "' . $element_detail['id'] . '","element_tip": "'.$etip_html.'","area":"'.$wsp_data['title'].'","dur":"'.$days['total_days'].'","total_days":"'.$days['total_days'].'","remaining_days":"'.$days['remaining_days'].'","left_days":"'.$days['left_days'].'","color":"'.$element_detail['color_code'] .'", "t_id":"element_' . $element_detail['id'] . '","text": "' . str_replace("&nbsp;"," ",$etip) . '","start_date": "' . $start . '", "end_date":"' . $end . '", "status":"' . $element_status . '", "order": "' . $element_detail['id'] . '", "progress": "'.$progress.'", "parent": "parent_' . $wsp_data['id'] . '", open: true},';
	            $i++;
			}
		}
		
	}
}
		
		$this->set('data',$data);
		$this->set('link',$link);
		/********************************/
		$this->render("gantt/event_gantt");
		
	}
	
}
