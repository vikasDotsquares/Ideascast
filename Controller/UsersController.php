<?php
/**
 * Users controller.
 *
 * This file will render views from views/Users/
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
 * Users Controller
 *
 * @property User $User
 * @property PaginatorComponent $Paginator
 */
class UsersController extends AppController {

	var $name = 'Users';

	const USER_INACTIVE = 0;
	const USER_ACTIVE = 1;
	const USER_PENDING = 2;

	var $questionArray = array(1 => "Where were you born?", 2 => "Mothers maiden name?", 3 => "Favorite football team?", 4 => "Name of first school?");
	var $questionArrayAns = array("Where were you born?" => "Where were you born?", "Mothers maiden name?" => "Mothers maiden name?", "Favorite football team?" => "Favorite football team?", "Name of first school?" => "Name of first school?");
	//public $uses = array('User', 'Language', 'Country', 'State', 'City','UserImage');
	public $uses = array('User', 'Skill', 'UserDetail', 'UserProject', 'CakeSession', 'Element', 'Workspace', 'Area', 'Project', 'ProjectGroupUser', 'UserProject', 'ProjectWorkspace', 'ProjectPermission', 'ElementPermission', 'ManageDomain', 'OrganisationUser', 'OrgPassPolicy', 'UserPassword', 'PasswordLockout', 'EmailNotification', 'OrgSetting', 'ElementAssignment', 'SkillPdf', 'UserInterest', 'DeleteData','SkillDetail','SignoffProject');

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
		$this->Auth->allow('index', 'register', 'confirm', 'logout', 'activate', 'activation', 'forgetpwd', 'reset', 'admin_login', 'admin_forgotpassword', 'admin_logout', 'admin_forgot_password', 'admin_check_old_password', 'admin_get_state_city', 'registration', 'checkSession', 'validateEmail', 'validate_email', 'list_program_policy', 'activate_account', 'chat_logout');

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

	public function getUpload($id) {
		$dataTT = strtotime(date('Y-m-d 00:00:00'));

		//$plans = $this->UserPlan->find('first', array('conditions'=>array('UserPlan.user_id'=> $id,'UserPlan.plan_id'=>4,' 	UserPlan.start_date !='=>'','UserPlan.end_date >='=>$dataTT), 'fields' => array('UserPlan.is_active')));

		//$plans = $this->UserPlan->find('first', array('conditions' => array('UserPlan.user_id' => $id, 'UserPlan.plan_id' => 4, ' 	UserPlan.start_date !=' => ''), 'fields' => array('UserPlan.is_active')));

		return isset($plans) ? $plans : array();
	}

	public function getUPlan($id) {
		//$plans = $this->UserPlan->find('all', array('conditions' => array('UserPlan.user_id' => $id, 'UserPlan.plan_id !=' => '', 'UserPlan.start_date !=' => ''), 'fields' => array('UserPlan.id', 'UserPlan.start_date', 'UserPlan.end_date', 'UserPlan.user_id', 'UserPlan.plan_id'), 'Order' => 'UserPlan.id'));
		return isset($plans) ? $plans : array();
	}

	public function event_calender() {
		$this->set('title_for_layout', __('Planner', true));
		$this->layout = "calander";
		$this->autoRender = false;
		$project_id = $status = $workspace_id = '';

		$this->Element->recursive = 1;
		$user_id = $this->Auth->user('id');

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
		} else {
			$this->set('mode', $mode = 'week');
		}
		if (isset($this->params->named['workspace_id']) && !empty($this->params->named['workspace_id'])) {
			$workspace_id = $this->params->named['workspace_id'];
		}
		if (isset($this->params->named['status']) && !empty($this->params->named['status'])) {
			$status = $this->params->named['status'];
		}

		$project = $this->Project->find("first", array("conditions" => array("Project.id" => $project_id), "fields" => array()));
		$project_name = strip_tags(str_replace("&nbsp;", " ", $project['Project']['title']));

		$this->Project->id = $project_id;

		if ($project_id == '' || !$this->Project->exists() && $project_type == 'm_project') {
			//$this->Session->setFlash(__('Invalid Project Id.'));
			return $this->redirect(SITEURL . 'projects/lists');
		}
		if ($project_id == '' || !$this->Project->exists() && $project_type == 'r_project') {
			//$this->Session->setFlash(__('Invalid Project Id.'));
			return $this->redirect(SITEURL . 'projects/share_lists');
		}
		if ($project_id == '' || !$this->Project->exists() && $project_type == 'g_project') {
			//$this->Session->setFlash(__('Invalid Project Id.'));
			return $this->redirect(SITEURL . 'groups/shared_projects');
		}

		// Find All current user's projects
		$myprojectlist = $this->__myproject_selectbox($user_id);
		// Find All current user's received projects
		$myreceivedprojectlist = $this->__receivedproject_selectbox($user_id);
		// Find All current user's group projects
		$mygroupprojectlist = $this->__groupproject_selectbox($user_id);

		// Find All workspaces by project id
		$myWorkspaceslistByproject = $this->__project_workspace_selectbox($project_id, $user_id, $project_type);

		// $cat_crumb = get_category_list($project_id);
		$crumb = ['last' => ['Calendar']];

		if (isset($project_id) && !empty($project_id)) {
			$prdata = $this->ProjectWorkspace->find('first', ['recursive' => 1, 'conditions' => ['ProjectWorkspace.project_id' => $project_id]]);

			$project_title = (isset($prdata) && !empty($prdata)) ? _strip_tags($prdata['Project']['title']) : '';
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

		$this->set('crumb', $crumb);

		$this->set(compact(
			"project", "project_id", "user_id", "status", "project_name", "workspace_id", "project_type", "myprojectlist", "myreceivedprojectlist", "myWorkspaceslistByproject", "mygroupprojectlist"
		)
		);
		if ($this->RequestHandler->isAjax()) {
			$this->layout = false;
			// Find All workspaces by project id
			$status = (isset($status) && !empty($status)) ? $status : 'all';
			$elementsArr = $this->element_by_ws_and_status($project_id, $user_id, $project_type, $workspace_id, $status, $type = 'calender');
			$this->set("elementsArr", $elementsArr);
			$this->render("calender/get_workspace_by_project");
		} else {
			// Find All workspaces by project id
			$elementsArr = $this->element_by_project($project_id, $user_id, $project_type, $type = 'calender', $status);
			$this->set("elementsArr", $elementsArr);
			$this->render("calender/event_calender");
		}
	}

	// Its use for all my project list for select box
	public function __myproject_selectbox($user_id = null, $aligned_id = null, $category_id = null) {

		if (isset($category_id) && !empty($category_id)) {

			$allmyproject = $this->UserProject->query("select Project.id as id,Project.title as title from user_projects as UserProject left join projects as Project on UserProject.project_id=Project.id  where Project.studio_status = 0 and UserProject.user_id = '" . $user_id . "' and Project.id != '' and Project.category_id = " . $category_id . " and  UserProject.id != '' order by Project.title");

		} else {
			if (isset($aligned_id) && !empty($aligned_id)) {
				$allmyproject = $this->UserProject->query("select Project.id as id,Project.title as title from user_projects as UserProject left join projects as Project on UserProject.project_id=Project.id  where Project.studio_status = 0 and UserProject.user_id = '" . $user_id . "' and Project.id != '' and Project.aligned_id = " . $aligned_id . " and  UserProject.id != '' order by Project.title");
			} else {
				$allmyproject = $this->UserProject->query("select Project.id as id,Project.title as title from user_projects as UserProject left join projects as Project on UserProject.project_id=Project.id  where Project.studio_status = 0 and UserProject.user_id = '" . $user_id . "' and Project.id != '' and  UserProject.id != '' order by Project.title");
			}
		}
		$myprojectlist = array();

		if (isset($allmyproject) && !empty($allmyproject)) {
			foreach ($allmyproject as $valP) {
				$myprojectlist[$valP['Project']['id']] = strip_tags(str_replace("&nbsp;", " ", $valP['Project']['title']));
			}
		}
		return $myprojectlist;
	}

	//Its user for all group project listbox
	public function __groupproject_selectbox($user_id = null, $level = null, $aligned_id = null, $category_id = null) {
		$cond = ['ProjectGroupUser.user_id' => $user_id, 'ProjectGroupUser.approved' => 1];
		if (isset($aligned_id) && !empty($aligned_id)) {
			$condn = ['Project.aligned_id' => $aligned_id];
		}

		if (isset($category_id) && !empty($category_id)) {
			$condn = ['Project.category_id' => $category_id];
		}

		if (isset($level) && !empty($level)) {
			$cond = ['ProjectGroupUser.user_id' => $user_id, 'ProjectGroupUser.approved' => 1];
		}
		$group_projects_list = null;
		$group_projects = $this->ProjectGroupUser->find('all', ['conditions' => $cond, 'recursive' => 2]);

		$group_projects_list = (isset($group_projects) && !empty($group_projects)) ? Set::combine($group_projects, '{n}.UserProject.project_id', '{n}.UserProject.Project.title') : null;

		if (isset($level) && !empty($level)) {

			$group_projects_list = (isset($group_projects) && !empty($group_projects)) ? Set::extract($group_projects, '{n}.ProjectGroup.id') : null;
			if (isset($group_projects_list) && !empty($group_projects_list)) {

				$group_projects_list = $this->ProjectPermission->find('all', array('conditions' => array('ProjectPermission.project_group_id' => $group_projects_list, 'ProjectPermission.project_level' => 1), 'recursive' => 2));

			}
			$group_projects_list = (isset($group_projects_list) && !empty($group_projects_list)) ? Set::combine($group_projects_list, '{n}.UserProject.Project.id', '{n}.UserProject.Project.title') : null;

			if (isset($group_projects_list) && !empty($group_projects_list)) {

				foreach ($group_projects_list as $key => $asg) {
					$group_projects_list[$key] = html_entity_decode(strip_tags($asg));
				}
			}
		}

		if (isset($condn) && !empty($condn)) {
			if (isset($group_projects_list) && !empty($group_projects_list)) {
				$group_projects_list = $this->Project->find('all', array('conditions' => array('Project.id' => array_keys($group_projects_list), $condn), 'recursive' => 2));
			}

			$group_projects_list = (isset($group_projects_list) && !empty($group_projects_list)) ? Set::combine($group_projects_list, '{n}.Project.id', '{n}.Project.title') : null;
		}

		// pr($group_projects_list, 1);
		return $group_projects_list;
	}

	// Its use for all received project list for select box
	public function __receivedproject_selectbox($user_id = null, $level = null, $aligned_id = null, $category_id = null) {

		$conditions = null;
		$conditions = array('ProjectPermission.share_by_id !=' => null);
		$conditions['ProjectPermission.user_id'] = $user_id;

		if (isset($level) && !empty($level)) {
			$conditions['ProjectPermission.project_level'] = 1;
		}
		$projects_received = $this->ProjectPermission->find('all', array('conditions' => $conditions, 'fields' => array('ProjectPermission.*'), 'order' => 'ProjectPermission.created DESC', 'recursive' => -1));

		$all_user_project_id = (implode(',', Hash::extract($projects_received, '{n}.ProjectPermission.user_project_id')) == '') ? '0' : implode(',', Hash::extract($projects_received, '{n}.ProjectPermission.user_project_id'));

		if (isset($category_id) && !empty($category_id)) {

			$allreceivedproject = $this->UserProject->query("select Project.id as id,Project.title as title from user_projects as UserProject left join projects as Project on UserProject.project_id=Project.id  where UserProject.id IN ( " . $all_user_project_id . " ) and Project.id != '' and Project.category_id = " . $category_id . " order by Project.title");

		} else {

			if (isset($aligned_id) && !empty($aligned_id)) {
				$allreceivedproject = $this->UserProject->query("select Project.id as id,Project.title as title from user_projects as UserProject left join projects as Project on UserProject.project_id=Project.id  where UserProject.id IN ( " . $all_user_project_id . " ) and Project.id != '' and Project.aligned_id = " . $aligned_id . " order by Project.title");
			} else {
				$allreceivedproject = $this->UserProject->query("select Project.id as id,Project.title as title from user_projects as UserProject left join projects as Project on UserProject.project_id=Project.id  where UserProject.id IN ( " . $all_user_project_id . " ) and Project.id != '' order by Project.title");
			}
		}

		$myreceivedprojectlist = array();
		if (isset($allreceivedproject) && !empty($allreceivedproject)) {
			foreach ($allreceivedproject as $valRP) {
				$myreceivedprojectlist[$valRP['Project']['id']] = strip_tags(str_replace("&nbsp;", " ", $valRP['Project']['title']));
			}
		}
// pr($myreceivedprojectlist );
		return $myreceivedprojectlist;
	}

	// Its use for all workspace by project id for select box
	public function __project_workspace_selectbox($project_id = null, $user_id = null, $project_type = null) {

		$us_permission = $this->Common->userproject($project_id, $user_id);
		$pr_permission = $this->Common->project_permission_details($project_id, $user_id);
		//   $ws_permission = $this->Common->work_permission_details($project_id, $user_id);

		$grp_id = $this->Group->GroupIDbyUserID($project_id, $user_id);
		if (isset($grp_id) && !empty($grp_id)) {
			$group_permission = $this->Group->group_permission_details($project_id, $grp_id);

			$pr_permission = $this->Group->group_permission_details($project_id, $grp_id);

			if (isset($group_permission['ProjectPermission']['project_level']) && $group_permission['ProjectPermission']['project_level'] == 1) {
				$project_level = $group_permission['ProjectPermission']['project_level'];
				$this->set('project_level', $project_level);
			}
		}

		if (isset($pr_permission) && !empty($pr_permission)) {
			$ws_permission = $this->Common->work_permission_details($project_id, $user_id);
			$ims = (implode(',', $ws_permission) == '') ? '0' : implode(',', $ws_permission);
		}
		if (isset($grp_id) && !empty($grp_id)) {
			$ws_permission = $this->Group->group_work_permission_details($project_id, $grp_id);
			$ims = (implode(',', $ws_permission) == '') ? '0' : implode(',', $ws_permission);
		}

		if (isset($project_type) && !empty($project_type) && $project_type == 'm_project') {
			$workspaces = $this->ProjectWorkspace->query("select Workspace.id as id,Workspace.title as title,Workspace.color_code as color_code from project_workspaces as ProjectWorkspace left join workspaces as Workspace on ProjectWorkspace.workspace_id=Workspace.id  where ProjectWorkspace.project_id = '$project_id' and Workspace.studio_status !=1 order by ProjectWorkspace.sort_order");
		}
		elseif (isset($project_type) && !empty($project_type) && ($project_type == 'r_project' || $project_type == 'g_project')) {

			if (isset($pr_permission['ProjectPermission']['project_level']) && $pr_permission['ProjectPermission']['project_level'] > 0) {
				$workspaces = $this->ProjectWorkspace->query("select Workspace.id as id,Workspace.title as title,Workspace.color_code as color_code from project_workspaces as ProjectWorkspace left join workspaces as Workspace on ProjectWorkspace.workspace_id=Workspace.id  where ProjectWorkspace.project_id = '" . $project_id . "'  and Workspace.studio_status !=1 order by ProjectWorkspace.sort_order");
			} else {
				$workspaces = $this->ProjectWorkspace->query("select Workspace.id as id,Workspace.title as title,Workspace.color_code as color_code from project_workspaces as ProjectWorkspace left join workspaces as Workspace on ProjectWorkspace.workspace_id=Workspace.id  where ProjectWorkspace.project_id = '" . $project_id . "' AND ProjectWorkspace.id in(" . $ims . ")  and Workspace.studio_status !=1 order by ProjectWorkspace.sort_order");
			}
		}
		// pr($workspaces);
		$myWorkspaceslistByproject = array();
		if (isset($workspaces) && !empty($workspaces)) {
			foreach ($workspaces as $valWP) {
				$myWorkspaceslistByproject[$valWP['Workspace']['id']] = str_replace("&nbsp;", " ", $valWP['Workspace']['title']);
			}
		}
		return $myWorkspaceslistByproject;
	}

	// Its use for all workspace and element by project id for array
	public function element_by_project($project_id = null, $user_id = null, $project_type = null, $type = null, $status = null) {
		$mindate = date("Y-m-d");
		$cur_date = date("Y-m-d");

		if ($this->RequestHandler->isAjax() && $type == 'calender') {
			$this->layout = false;
			$this->autoRender = false;
			$project_id = isset($this->params['pass'][0]) ? $this->params['pass'][0] : '';
			$user_id = isset($this->params['pass'][1]) ? $this->params['pass'][1] : '';
			$project_type = isset($this->params['pass'][2]) ? $this->params['pass'][2] : '';
			$type = isset($this->params['pass'][3]) ? $this->params['pass'][3] : '';
		}
		$us_permission = $this->Common->userproject($project_id, $user_id);
		$pr_permission = $this->Common->project_permission_details($project_id, $user_id);
		//   $ws_permission = $this->Common->work_permission_details($project_id, $user_id);

		$grp_id = $this->Group->GroupIDbyUserID($project_id, $user_id);
		$gpid = $grp_id;
		if (isset($grp_id) && !empty($grp_id)) {
			$group_permission = $this->Group->group_permission_details($project_id, $grp_id);
			$pr_permission = $this->Group->group_permission_details($project_id, $grp_id);
			if (isset($group_permission['ProjectPermission']['project_level']) && $group_permission['ProjectPermission']['project_level'] == 1) {
				$project_level = $group_permission['ProjectPermission']['project_level'];
			}
		}

		if (isset($pr_permission) && !empty($pr_permission)) {
			$ws_permission = $this->Common->work_permission_details($project_id, $user_id);
			$ims = (implode(',', $ws_permission) == '') ? '0' : implode(',', $ws_permission);
		}
		if (isset($grp_id) && !empty($grp_id)) {
			$ws_permission = $this->Group->group_work_permission_details($project_id, $grp_id);
			$ims = (implode(',', $ws_permission) == '') ? '0' : implode(',', $ws_permission);
		}

		/*  performace work 04 may 2020 added recursive */

		$project = $this->Project->find("first", array("conditions" => array("Project.id" => $project_id), "fields" => array(),'recursive'=> -1));

		$elementsArr = array();
		$calenderElementsArr = array();
		if (isset($project_type) && !empty($project_type) && $project_type == 'm_project') {
			$allWorkspaceByMyProject = $this->ProjectWorkspace->query("select Workspace.id as id,Workspace.title as title,Workspace.start_date as start_date,Workspace.end_date as end_date,Workspace.color_code as color_code,Workspace.created as created from project_workspaces as ProjectWorkspace left join workspaces as Workspace on ProjectWorkspace.workspace_id=Workspace.id  where ProjectWorkspace.project_id = '" . $project_id . "' and Workspace.studio_status != 1 order by ProjectWorkspace.sort_order");

			foreach ($allWorkspaceByMyProject as $keyW => $workspaceArr) {
				$wId = $workspaceArr['Workspace']['id'];
				$areaByWSid = $this->Area->query("select * from areas as Area where Area.workspace_id= '" . $wId . "' and Area.studio_status !=1 order by id asc");
				$elementsArr[] = $workspaceArr;
				foreach ($areaByWSid as $keyA => $areaV) {
					$elementsArr[$keyW]['Workspace']['Area'][] = $areaV['Area'];
					$areaId = $areaV['Area']['id'];

					$status_conditions = "Element.area_id= '" . $areaId . "'";
					if ($status == 'NON') {
						$status_conditions = 'Element.area_id= "' . $areaId . '" AND Element.date_constraints IS NOT NULL AND Element.date_constraints < 1';
					} else if ($status == 'PND') {
						$status_conditions = 'Element.area_id= "' . $areaId . '" AND Element.start_date > "' . $cur_date . '"';
					} else if ($status == 'PRG') {
						$status_conditions = 'Element.area_id= "' . $areaId . '" AND Element.start_date <= "' . $cur_date . '" AND Element.end_date >= "' . $cur_date . '" AND Element.sign_off !="1"';
					} else if ($status == 'OVD') {
						$status_conditions = 'Element.area_id= "' . $areaId . '" AND Element.end_date < "' . $cur_date . '"';
					} else if ($status == 'CMP') {
						$status_conditions = 'Element.area_id= "' . $areaId . '" AND Element.sign_off="1"';
					}

					// $elementByAreaid = $this->Element->query("select * from elements as Element where Element.area_id= '" . $areaId . "' and Element.studio_status !=1 order by area_id asc");
					$elementByAreaid = $this->Element->query("select Element.* from elements as Element where $status_conditions and Element.studio_status !=1 order by area_id asc");

					foreach ($elementByAreaid as $keyE => $valE) {

						$elementsArr[$keyW]['Workspace']['Area'][$keyA]['Element'][$keyE] = $elementByAreaid[$keyE]['Element'];
						if (!empty($valE['Element']['start_date']) && date("Y-m-d", strtotime($valE['Element']['start_date'])) < $mindate) {
							$mindate = date("Y-m-d", strtotime($valE['Element']['start_date']));
						}
						//its user for clender array

						$calenderElementsArr[$valE['Element']['id']]['id'] = strip_tags($valE['Element']['id']);
						$calenderElementsArr[$valE['Element']['id']]['color_code'] = strip_tags($valE['Element']['color_code']);
						$calenderElementsArr[$valE['Element']['id']]['descriptions'] = substr(strip_tags($project['Project']['description']), 0, 230);
						$calenderElementsArr[$valE['Element']['id']]['project_id'] = strip_tags($project['Project']['id']);
						$calenderElementsArr[$valE['Element']['id']]['user_id'] = $user_id;
						$calenderElementsArr[$valE['Element']['id']]['workspace_id'] = strip_tags($workspaceArr['Workspace']['id']);
						$calenderElementsArr[$valE['Element']['id']]['title'] = strip_tags($valE['Element']['title']);
						$calenderElementsArr[$valE['Element']['id']]['workspace'] = strip_tags($workspaceArr['Workspace']['title']);
						$calenderElementsArr[$valE['Element']['id']]['area'] = strip_tags($areaV['Area']['title']);
						$calenderElementsArr[$valE['Element']['id']]['project'] = strip_tags($project['Project']['title']);
						$calenderElementsArr[$valE['Element']['id']]['start_date'] = date('d M Y', strtotime($valE['Element']['start_date']));
						$calenderElementsArr[$valE['Element']['id']]['end_date'] = date('d M Y', strtotime($valE['Element']['end_date']));
						$calenderElementsArr[$valE['Element']['id']]['url'] = strip_tags(SITEURL . "/entities/update_element/" . $valE['Element']['id']);
						$calenderElementsArr[$valE['Element']['id']]['start'] = strtotime($valE['Element']['start_date']) * 1000;
						$calenderElementsArr[$valE['Element']['id']]['end'] = strtotime($valE['Element']['end_date']) * 1000;

						///// end here
					}
				}
			}
		} elseif (isset($project_type) && !empty($project_type)) {
			if (isset($pr_permission['ProjectPermission']) && $pr_permission['ProjectPermission']['project_level'] > 0) {
				$allWorkspaceByReceivedProjectId = $this->ProjectWorkspace->query("select Workspace.id as id,Workspace.title as title,Workspace.start_date as start_date,Workspace.end_date as end_date,Workspace.color_code as color_code ,Workspace.created as created  from project_workspaces as ProjectWorkspace left join workspaces as Workspace on ProjectWorkspace.workspace_id=Workspace.id  where ProjectWorkspace.project_id = '" . $project_id . "' and Workspace.studio_status != 1 order by ProjectWorkspace.sort_order");
			} else {
				$allWorkspaceByReceivedProjectId = $this->ProjectWorkspace->query("select Workspace.id as id,Workspace.title as title,Workspace.start_date as start_date,Workspace.end_date as end_date,Workspace.color_code as color_code ,Workspace.created as created from project_workspaces as ProjectWorkspace left join workspaces as Workspace on ProjectWorkspace.workspace_id=Workspace.id  where ProjectWorkspace.project_id = '" . $project_id . "' AND ProjectWorkspace.id in (" . $ims . ") and Workspace.studio_status != 1 order by ProjectWorkspace.sort_order");
			}

			//pr($allWorkspaceByReceivedProjectId,1);
			foreach ($allWorkspaceByReceivedProjectId as $keyW => $workspaceArr) {
				$wId = $workspaceArr['Workspace']['id'];
				$areaByWSid = $this->Area->query("select * from areas as Area where Area.workspace_id= '" . $wId . "' and Area.studio_status !=1");
				$elementsArr[] = $workspaceArr;
				foreach ($areaByWSid as $keyA => $areaV) {
					$elementsArr[$keyW]['Workspace']['Area'][] = $areaV['Area'];
					$areaId = $areaV['Area']['id'];
					if (isset($pr_permission) && $pr_permission['ProjectPermission']['project_level'] > 0) {
						$elementByAreaid = $this->Element->query("select * from elements as Element where Element.area_id= '" . $areaId . "' and Element.studio_status !=1");
					} else {

						$receivedElements = $this->Common->element_permission_details($wId, $project_id, $user_id);

						if ((isset($grp_id) && !empty($grp_id))) {
							$receivedElements = $this->Group->group_element_permission_details($wId, $project_id, $grp_id);
						}

						$elementsId = (implode(',', $receivedElements) == '') ? '0' : implode(',', $receivedElements);

						$elementByAreaid = $this->Element->query("select * from elements as Element where Element.area_id= '" . $areaId . "' and Element.id in (" . $elementsId . ") and  Element.studio_status !=1");
					}
					foreach ($elementByAreaid as $keyE => $valE) {
		//pr($valE);
						$elementsArr[$keyW]['Workspace']['Area'][$keyA]['Element'][] = $valE['Element'];

						if (!empty($valE['Element']['start_date']) && date("Y-m-d", strtotime($valE['Element']['start_date'])) < $mindate) {
							$mindate = date("Y-m-d", strtotime($valE['Element']['start_date']));
						}

						//its user for clender array

						$calenderElementsArr[$valE['Element']['id']]['id'] = strip_tags($valE['Element']['id']);
						$calenderElementsArr[$valE['Element']['id']]['color_code'] = strip_tags($valE['Element']['color_code']);
						$calenderElementsArr[$valE['Element']['id']]['descriptions'] = substr(strip_tags($project['Project']['description']), 0, 230);
						$calenderElementsArr[$valE['Element']['id']]['project_id'] = strip_tags($project['Project']['id']);
						$calenderElementsArr[$valE['Element']['id']]['user_id'] = $user_id;
						$calenderElementsArr[$valE['Element']['id']]['workspace_id'] = strip_tags($workspaceArr['Workspace']['id']);
						$calenderElementsArr[$valE['Element']['id']]['title'] = strip_tags($valE['Element']['title']);
						$calenderElementsArr[$valE['Element']['id']]['workspace'] = strip_tags($workspaceArr['Workspace']['title']);
						$calenderElementsArr[$valE['Element']['id']]['area'] = strip_tags($areaV['Area']['title']);
						$calenderElementsArr[$valE['Element']['id']]['project'] = strip_tags($project['Project']['title']);
						$calenderElementsArr[$valE['Element']['id']]['start_date'] = date('d M Y', strtotime($valE['Element']['start_date']));
						$calenderElementsArr[$valE['Element']['id']]['end_date'] = date('d M Y', strtotime($valE['Element']['end_date']));
						$calenderElementsArr[$valE['Element']['id']]['url'] = strip_tags(SITEURL . "/entities/update_element/" . $valE['Element']['id']);
						$calenderElementsArr[$valE['Element']['id']]['start'] = strtotime($valE['Element']['start_date']) * 1000;
						$calenderElementsArr[$valE['Element']['id']]['end'] = strtotime($valE['Element']['end_date']) * 1000;

						///// end here
					}
				}
			}
		}

		if (isset($type) && !empty($type) && $type == 'gantt') {
			return $elementsArr;
		} else if (isset($type) && !empty($type) && $type == 'calender') {
			$i = 0;
			$color = '';
			$vass = '{ "success": 1, "result": [';
			$vas = "";
			foreach ($calenderElementsArr as $elem) {
				$color = str_replace("panel", "bg", $elem['color_code']);
				$elem['class'] = $color;
				$vas .= json_encode($elem, JSON_UNESCAPED_SLASHES) . ",";
				$i++;
			}
			$vasss = ' ] } ';

			//echo $vass . rtrim($vas, ",") . $vasss; die;
			return $vass . rtrim($vas, ",") . $vasss;
			//return $calenderElementsArr;
		} else if (isset($type) && !empty($type) && $type == 'mindate') {

			return $mindate;
		}
	}

	public function get_project_date($project_id = null) {
		$this->layout = $this->autoRender = $dateArray = false;
		$this->Project->recursive = -1;

		$project = $this->Project->findById($project_id);
		$mindate = isset($project['Project']['start_date']) && !empty($project['Project']['start_date']) ? date("Y-m-d", strtotime($project['Project']['start_date'])) : '';
		$maxdate = isset($project['Project']['end_date']) && !empty($project['Project']['end_date']) ? date("Y-m-d", strtotime($project['Project']['end_date'])) : '';
		$curr_date = date("Y-m-d");
		//echo 'mindate: '.$mindate.' maxdate: ' .$maxdate .' curr_date: '.$curr_date;
		$leftdays = $totaldays = $remainingdays = 0;
		if (!empty($mindate) && !empty($maxdate)) {
			$totaldays = round(abs(strtotime($maxdate) - strtotime($mindate)) / (60 * 60 * 24)) + 1;
		} else {
			$totaldays = round(abs(strtotime($maxdate) - strtotime($mindate)) / (60 * 60 * 24));
		}
		if ($curr_date <= $maxdate && $mindate <= $curr_date) {
			if (!empty($mindate) && !empty($maxdate)) {
				$remainingdays = round(abs(strtotime($maxdate) - strtotime($curr_date)) / (60 * 60 * 24)) + 1;
			} else {
				$remainingdays = round(abs(strtotime($maxdate) - strtotime($curr_date)) / (60 * 60 * 24));
			}

			$leftdays = round(abs(strtotime($curr_date) - strtotime($mindate)) / (60 * 60 * 24));
		} else {
			$remainingdays = 0;

			if (!empty($mindate) && !empty($maxdate) && $mindate <= $curr_date) {

				$leftdays = round(abs(strtotime($maxdate) - strtotime($mindate)) / (60 * 60 * 24)) + 1;
			} else if (!empty($mindate) && !empty($maxdate) && $mindate >= $curr_date) {
				$leftdays = 0;
			} else {
				$leftdays = round(abs(strtotime($maxdate) - strtotime($mindate)) / (60 * 60 * 24));
			}
		}
		return array("leftdays" => $leftdays, "remainingdays" => $remainingdays, "totaldays" => $totaldays);
	}

	public function get_workspacedate($workspace_id = null, $project_id = null) {
		$this->layout = false;
		$this->autoRender = false;
		$dateArray = null;
		$dateprec = '-';
		$dateprecTool = '-';
		if ($this->RequestHandler->isAjax()) {
			$this->Workspace->recursive = -1;
			$this->Project->recursive = -1;

			$wsp_id = explode('_', $workspace_id);
			$wsp_ID = (isset($wsp_id[1]) && !empty($wsp_id[1])) ? $wsp_id[1] : 0;

		// pr($project );

			$date = $this->Workspace->findById($wsp_ID);
			$project = $this->Project->findById($project_id);

			if (isset($date['Workspace']) && !empty($date['Workspace']['start_date']) && !empty($date['Workspace']['end_date'])) {
				$duration = floor(abs(strtotime($date['Workspace']['end_date']) - strtotime($date['Workspace']['start_date'])) / (60 * 60 * 24)) + 1;
			} else {
				$duration = 0;
			}
			if ((isset($date['Workspace']['start_date']) && !empty($date['Workspace']['start_date'])) && date('Y-m-d', strtotime($date['Workspace']['start_date'])) > date('Y-m-d')) {

				//$dateprec = 'Not Started';//STATUS_NOT_STARTED;
				$dateprec = STATUS_NOT_STARTED;
			} else if ((isset($date['Workspace']['end_date']) && !empty($date['Workspace']['end_date'])) && date('Y-m-d', strtotime($date['Workspace']['end_date'])) < date('Y-m-d')) {

				//$dateprec = '<span class="text-red">Overdue</span>';//STATUS_OVERDUE;
				$dateprec = STATUS_OVERDUE;
				$dateprecTool = STATUS_OVERDUE;
			} else if (isset($date['Workspace']['sign_off']) && !empty($date['Workspace']['sign_off']) && isset($project['Project']['sign_off']) && $project['Project']['sign_off'] > 0) {

				//$dateprec = 'Completed';//STATUS_COMPLETED;
				$dateprec = STATUS_COMPLETED;
			} else if (((isset($date['Workspace']['end_date']) && !empty($date['Workspace']['end_date'])) && (isset($date['Workspace']['start_date']) && !empty($date['Workspace']['start_date']))) && (date('Y-m-d', strtotime($date['Workspace']['start_date'])) <= date('Y-m-d')) && date('Y-m-d', strtotime($date['Workspace']['end_date'])) >= date('Y-m-d')) {

				//$dateprec = 'In Process';//STATUS_PROGRESS;
				$dateprec = STATUS_PROGRESS;
			}

			return json_encode(
				array(
					"start_date" => isset($date['Workspace']['start_date']) && !empty($date['Workspace']['start_date']) ? date('d M y', strtotime($date['Workspace']['start_date'])) : '-',
					"end_date" => isset($date['Workspace']['end_date']) && !empty($date['Workspace']['end_date']) ? date('d M y', strtotime($date['Workspace']['end_date'])) : '-',
					"duration" => $duration,
					"status" => $dateprec,
					"statusTip" => $dateprecTool,
				)
			);
		}
	}

	public function element_by_ws_and_status($project_id = null, $user_id = null, $project_type = null, $workspace_id = null, $status = null, $type = null, $criticalEleStatus = 0, $uId = 0, $gId = 0, $sharing_type = 0, $assignmentStatus = null) {
		$mindate = date("Y-m-d");

		// echo 'project_id : '.$project_id, 'user_id : '.$user_id, 'project_type : '.$project_type, 'wrk_id : '.$workspace_id, 'status : '.$status, 'type : '.$type;
		// pr($this->params['pass'] );

		if ($this->RequestHandler->isAjax() && $type == 'calender') {
			$this->layout = false;
			$this->autoRender = false;
			$project_id = isset($this->params['pass'][0]) ? $this->params['pass'][0] : '';
			$user_id = isset($this->params['pass'][1]) ? $this->params['pass'][1] : '';
			$project_type = isset($this->params['pass'][2]) ? $this->params['pass'][2] : '';
			$workspace_id = isset($this->params['pass'][3]) ? $this->params['pass'][3] : '';
			$status = isset($this->params['pass'][4]) ? $this->params['pass'][4] : '';
			$type = isset($this->params['pass'][5]) ? $this->params['pass'][5] : '';
		}

		/*  performace work 04 may 2020 added recursive */

		$project = $this->Project->find("first", array("conditions" => array("Project.id" => $project_id), "fields" => array(),'recursive'=> -1));


		$cur_date = date("Y-m-d 12:00:00");
		$cur_end_date = date("Y-m-d");
		$elementsArr = array();
		$calenderElementsArr = array();
		$ws_cond = ($workspace_id != '') ? "ProjectWorkspace.workspace_id = '" . $workspace_id . "' and " : "";
		if (isset($project_type) && !empty($project_type) && $project_type == 'm_project') {



				$allWorkspaceByMyProject = $this->Workspace->query("select Workspace.id as id,Workspace.title as title,Workspace.start_date as start_date,Workspace.end_date as end_date,Workspace.color_code as color_code,Workspace.created as created from workspaces as Workspace
				left join user_permissions on user_permissions.workspace_id=Workspace.id
				left join project_workspaces as ProjectWorkspace on ProjectWorkspace.workspace_id=Workspace.id
				where $ws_cond user_permissions.project_id = '" . $project_id . "' and Workspace.studio_status != 1 and user_permissions.user_id =$user_id and user_permissions.area_id is null order by ProjectWorkspace.sort_order");



			foreach ($allWorkspaceByMyProject as $keyW => $workspaceArr) {
				$wId = $workspaceArr['Workspace']['id'];
				$areaByWSid = $this->Area->query("select * from areas as Area where Area.workspace_id= '" . $wId . "'   and Area.studio_status !=1 order by id asc");
				$elementsArr[] = $workspaceArr;
				foreach ($areaByWSid as $keyA => $areaV) {
					$elementsArr[$keyW]['Workspace']['Area'][] = $areaV['Area'];
					$areaId = $areaV['Area']['id'];

					$status_conditions = "Element.area_id= '" . $areaId . "'";

					if ($status == 'NON') {
						$status_conditions = 'Element.area_id= "' . $areaId . '" AND Element.date_constraints IS NOT NULL AND Element.date_constraints < 1';
					} else if ($status == 'PND') {
						$status_conditions = 'Element.area_id= "' . $areaId . '" AND Element.start_date > "' . $cur_date . '"';
					} else if ($status == 'PRG') {
						$status_conditions = 'Element.area_id= "' . $areaId . '" AND Element.start_date <= "' . $cur_date . '" AND Element.end_date >= "' . $cur_date . '" AND Element.sign_off !="1"';
					} else if ($status == 'OVD') {
						$status_conditions = 'Element.area_id= "' . $areaId . '" AND Element.end_date < "' . $cur_date . '" AND Element.sign_off !="1"';
					} else if ($status == 'CMP') {
						$status_conditions = 'Element.area_id= "' . $areaId . '" AND Element.sign_off="1"';
					}

					$elementByAreaid = $this->Element->query("select Element.* from elements as Element where $status_conditions and Element.studio_status !=1 order by start_date asc");

					if (!empty($criticalEleStatus) && $criticalEleStatus == 1) {
						$elementByAreaid = $this->Element->query("select Element.*,ElementDependency.* from elements as Element INNER JOIN element_dependencies as ElementDependency ON Element.id=ElementDependency.element_id  where $status_conditions and Element.studio_status !=1 and ElementDependency.is_critical =1 order by start_date asc");
					}

					if (isset($assignmentStatus) && $assignmentStatus == 'assignfilter') {

						$this->loadModel('ElementAssignment');
						$assignElement = $this->ElementAssignment->find('all',
							array(
								'conditions' => array('ElementAssignment.project_id' => $project_id),
								'fields' => array('ElementAssignment.element_id'),
							)
						);
						if (isset($assignElement) && !empty($assignElement)) {

							$elementids = Set::extract($assignElement, '/ElementAssignment/element_id');
							if (isset($elementids)) {
								$element_ids = implode(',', $elementids);
								$elementByAreaid = $this->Element->query("select Element.* from elements as Element where  $status_conditions and Element.studio_status !=1 and Element.id NOT IN (" . $element_ids . ") order by start_date asc");
							}

						}

					}


					if (!empty($uId)) {
						$elementAssignedByAreaid = $this->ElementAssignment->find("list", array('conditions' => array("ElementAssignment.assigned_to" => $uId, "ElementAssignment.project_id" => $project_id), 'fields' => array('ElementAssignment.id', 'ElementAssignment.element_id')));

						if (isset($elementAssignedByAreaid) && !empty($elementAssignedByAreaid)) {
							$explode = implode(",", $elementAssignedByAreaid);



							$elementByAreaid = $this->Element->query("select Element.*  from elements as Element    where $status_conditions and  Element.studio_status !=1  and Element.id in(" . $explode . ")  order by start_date asc");

						}


					}


					foreach ($elementByAreaid as $keyE => $valE) {

						$elementsArr[$keyW]['Workspace']['Area'][$keyA]['Element'][$keyE] = $elementByAreaid[$keyE]['Element'];

						if (!empty($valE['Element']['start_date']) && date("Y-m-d", strtotime($valE['Element']['start_date'])) < $mindate) {
							$mindate = date("Y-m-d", strtotime($valE['Element']['start_date']));
						}
						//its user for clender array

						$calenderElementsArr[$valE['Element']['id']]['id'] = strip_tags($valE['Element']['id']);
						$calenderElementsArr[$valE['Element']['id']]['color_code'] = strip_tags($valE['Element']['color_code']);
						$calenderElementsArr[$valE['Element']['id']]['descriptions'] = substr(strip_tags($project['Project']['description']), 0, 230);
						$calenderElementsArr[$valE['Element']['id']]['project_id'] = strip_tags($project['Project']['id']);
						$calenderElementsArr[$valE['Element']['id']]['user_id'] = $user_id;
						$calenderElementsArr[$valE['Element']['id']]['workspace_id'] = strip_tags($workspaceArr['Workspace']['id']);
						$calenderElementsArr[$valE['Element']['id']]['title'] = strip_tags($valE['Element']['title']);
						$calenderElementsArr[$valE['Element']['id']]['workspace'] = strip_tags($workspaceArr['Workspace']['title']);
						$calenderElementsArr[$valE['Element']['id']]['area'] = strip_tags($areaV['Area']['title']);
						$calenderElementsArr[$valE['Element']['id']]['project'] = strip_tags($project['Project']['title']);
						$calenderElementsArr[$valE['Element']['id']]['start_date'] = date('d M Y', strtotime($valE['Element']['start_date']));
						$calenderElementsArr[$valE['Element']['id']]['end_date'] = date('d M Y', strtotime($valE['Element']['end_date']));
						$calenderElementsArr[$valE['Element']['id']]['url'] = strip_tags(SITEURL . "/entities/update_element/" . $valE['Element']['id']);
						$calenderElementsArr[$valE['Element']['id']]['start'] = strtotime($valE['Element']['start_date']) * 1000;
						$calenderElementsArr[$valE['Element']['id']]['end'] = strtotime($valE['Element']['end_date']) * 1000;

						///// end here
					}
				}
			}
		} elseif (isset($project_type) && !empty($project_type) && ($project_type == 'r_project' || $project_type == 'g_project')) {


				$allWorkspaceByReceivedProjectId = $this->Workspace->query("select Workspace.id as id,Workspace.title as title,Workspace.start_date as start_date,Workspace.end_date as end_date,Workspace.color_code as color_code,Workspace.created as created from workspaces as Workspace
				left join user_permissions on user_permissions.workspace_id=Workspace.id
				left join project_workspaces as ProjectWorkspace on ProjectWorkspace.workspace_id=Workspace.id
				where $ws_cond user_permissions.project_id = '" . $project_id . "' and Workspace.studio_status != 1 and user_permissions.user_id =$user_id and user_permissions.area_id is null order by ProjectWorkspace.sort_order");


			foreach ($allWorkspaceByReceivedProjectId as $keyW => $workspaceArr) {
				$wId = $workspaceArr['Workspace']['id'];
				$areaByWSid = $this->Area->query("select * from areas as Area where Area.workspace_id= '" . $wId . "' and Area.studio_status !=1");
				$elementsArr[] = $workspaceArr;
				foreach ($areaByWSid as $keyA => $areaV) {
					$elementsArr[$keyW]['Workspace']['Area'][] = $areaV['Area'];
					$areaId = $areaV['Area']['id'];
					if (isset($pr_permission) && $pr_permission['ProjectPermission']['project_level'] > 0) {

						$status_conditions = "Element.area_id= '" . $areaId . "'";
						if ($status == 'NON') {
							$status_conditions = 'Element.area_id= "' . $areaId . '" AND date_constraints IS NOT NULL AND date_constraints < 1';
						} else if ($status == 'PND') {
							$status_conditions = 'Element.area_id= "' . $areaId . '" AND start_date > "' . $cur_date . '"';
						} else if ($status == 'PRG') {
							$status_conditions = 'Element.area_id= "' . $areaId . '" AND start_date <= "' . $cur_date . '" AND end_date >= "' . $cur_date . '" AND Element.sign_off !="1"';
						} else if ($status == 'OVD') {
							$status_conditions = 'Element.area_id= "' . $areaId . '" AND end_date < "' . $cur_date . '"';
						} else if ($status == 'CMP') {
							$status_conditions = 'Element.area_id= "' . $areaId . '" AND sign_off="1"';
						}

						$elementByAreaid = $this->Element->query("select * from elements as Element where $status_conditions and Element.studio_status !=1 order by start_date asc");

						if (!empty($criticalEleStatus) && $criticalEleStatus == 1) {
							$elementByAreaid = $this->Element->query("select Element.*,ElementDependency.* from elements as Element INNER JOIN element_dependencies as ElementDependency ON Element.id=ElementDependency.element_id  where   $status_conditions and Element.studio_status !=1 and ElementDependency.is_critical =1 order by start_date asc");
						}

						if (isset($assignmentStatus) && $assignmentStatus == 'assignfilter') {

							$this->loadModel('ElementAssignment');
							$assignElement = $this->ElementAssignment->find('all',
								array(
									'conditions' => array('ElementAssignment.project_id' => $project_id),
									'fields' => array('ElementAssignment.element_id'),
								)
							);
							if (isset($assignElement) && !empty($assignElement)) {

								$elementids = Set::extract($assignElement, '/ElementAssignment/element_id');
								if (isset($elementids)) {
									$element_ids = implode(',', $elementids);
									$elementByAreaid = $this->Element->query("select Element.* from elements as Element where  $status_conditions and Element.studio_status !=1 and Element.id NOT IN (" . $element_ids . ") order by start_date asc");
								}

							}

						}


						if (!empty($uId)) {

							$elementAssignedByAreaid = $this->ElementAssignment->find("list", array('conditions' => array("ElementAssignment.assigned_to" => $uId, "ElementAssignment.project_id" => $project_id), 'fields' => array('ElementAssignment.id', 'ElementAssignment.element_id')));

							if (isset($elementAssignedByAreaid) && !empty($elementAssignedByAreaid)) {
								$explode = implode(",", $elementAssignedByAreaid);

								$elementByAreaid = $this->Element->query("select Element.*  from elements as Element    where $status_conditions and  Element.studio_status !=1  and Element.id in(" . $explode . ")  order by start_date asc");

							}

						}

					} else {
						$receivedElements = $this->Common->element_permission_details($wId, $project_id, $user_id);
						if ((isset($grp_id) && !empty($grp_id))) {
							$receivedElements = $this->Group->group_element_permission_details($wId, $project_id, $grp_id);
						}

						$elementsId = (implode(',', $receivedElements) == '') ? '0' : implode(',', $receivedElements);
						$status_conditions = "Element.area_id= '" . $areaId . "' AND Element.id in (" . $elementsId . ")";
						if ($status == 'NON') {
							$status_conditions = 'Element.area_id= "' . $areaId . '" and Element.id in (' . $elementsId . ') AND date_constraints IS NOT NULL AND date_constraints < 1';
						} else if ($status == 'PND') {
							$status_conditions = 'Element.area_id= "' . $areaId . '" and Element.id in (' . $elementsId . ') AND start_date > "' . $cur_date . '"';
						} else if ($status == 'PRG') {
							$status_conditions = 'Element.area_id= "' . $areaId . '" and Element.id in (' . $elementsId . ') AND start_date <= "' . $cur_date . '" AND end_date >= "' . $cur_date . '" AND Element.sign_off !="1"';
						} else if ($status == 'OVD') {
							$status_conditions = 'Element.area_id= "' . $areaId . '" and Element.id in (' . $elementsId . ') AND end_date < "' . $cur_date . '"';
						} else if ($status == 'CMP') {
							$status_conditions = 'Element.area_id= "' . $areaId . '" and Element.id in (' . $elementsId . ') AND sign_off="1"';
						}

						$elementByAreaid = $this->Element->query("select * from elements as Element where $status_conditions and Element.studio_status !=1 order by start_date asc");

						if (!empty($criticalEleStatus) && $criticalEleStatus == 1) {
							$elementByAreaid = $this->Element->query("select Element.*,ElementDependency.* from elements as Element INNER JOIN element_dependencies as ElementDependency ON Element.id=ElementDependency.element_id where $status_conditions and Element.studio_status !=1 and ElementDependency.is_critical =1 order by start_date asc");
						}

						if (isset($assignmentStatus) && $assignmentStatus == 'assignfilter') {

							$this->loadModel('ElementAssignment');
							$assignElement = $this->ElementAssignment->find('all',
								array(
									'conditions' => array('ElementAssignment.project_id' => $project_id),
									'fields' => array('ElementAssignment.element_id'),
								)
							);
							if (isset($assignElement) && !empty($assignElement)) {

								$elementids = Set::extract($assignElement, '/ElementAssignment/element_id');
								if (isset($elementids)) {
									$element_ids = implode(',', $elementids);
									$elementByAreaid = $this->Element->query("select Element.* from elements as Element where  $status_conditions and Element.studio_status !=1 and Element.id NOT IN (" . $element_ids . ") order by start_date asc");
								}

							}

						}



						if (!empty($uId)) {

							$elementAssignedByAreaid = $this->ElementAssignment->find("list", array('conditions' => array("ElementAssignment.assigned_to" => $uId, "ElementAssignment.project_id" => $project_id), 'fields' => array('ElementAssignment.id', 'ElementAssignment.element_id')));

							if (isset($elementAssignedByAreaid) && !empty($elementAssignedByAreaid)) {
								$explode = implode(",", $elementAssignedByAreaid);

								$elementByAreaid = $this->Element->query("select Element.*  from elements as Element    where $status_conditions and  Element.studio_status !=1  and Element.id in(" . $explode . ")  order by start_date asc");

							}

						}

					}
					foreach ($elementByAreaid as $keyE => $valE) {
						//pr($valE);
						$elementsArr[$keyW]['Workspace']['Area'][$keyA]['Element'][] = $valE['Element'];

						if (!empty($valE['Element']['start_date']) && date("Y-m-d", strtotime($valE['Element']['start_date'])) < $mindate) {
							$mindate = date("Y-m-d", strtotime($valE['Element']['start_date']));
						}
						//its user for clender array

						$calenderElementsArr[$valE['Element']['id']]['id'] = strip_tags($valE['Element']['id']);
						$calenderElementsArr[$valE['Element']['id']]['color_code'] = strip_tags($valE['Element']['color_code']);
						$calenderElementsArr[$valE['Element']['id']]['descriptions'] = substr(strip_tags($project['Project']['description']), 0, 230);
						$calenderElementsArr[$valE['Element']['id']]['project_id'] = strip_tags($project['Project']['id']);
						$calenderElementsArr[$valE['Element']['id']]['user_id'] = $user_id;
						$calenderElementsArr[$valE['Element']['id']]['workspace_id'] = strip_tags($workspaceArr['Workspace']['id']);
						$calenderElementsArr[$valE['Element']['id']]['title'] = strip_tags($valE['Element']['title']);
						$calenderElementsArr[$valE['Element']['id']]['workspace'] = strip_tags($workspaceArr['Workspace']['title']);
						$calenderElementsArr[$valE['Element']['id']]['area'] = strip_tags($areaV['Area']['title']);
						$calenderElementsArr[$valE['Element']['id']]['project'] = strip_tags($project['Project']['title']);
						$calenderElementsArr[$valE['Element']['id']]['start_date'] = date('d M Y', strtotime($valE['Element']['start_date']));
						$calenderElementsArr[$valE['Element']['id']]['end_date'] = date('d M Y', strtotime($valE['Element']['end_date']));
						$calenderElementsArr[$valE['Element']['id']]['url'] = strip_tags(SITEURL . "/entities/update_element/" . $valE['Element']['id']);
						$calenderElementsArr[$valE['Element']['id']]['start'] = strtotime($valE['Element']['start_date']) * 1000;
						$calenderElementsArr[$valE['Element']['id']]['end'] = strtotime($valE['Element']['end_date']) * 1000;

						///// end here
					}
				}
			}
		}

		if (isset($type) && !empty($type) && $type == 'gantt') {

			return $elementsArr;
		} else if (isset($type) && !empty($type) && $type == 'calender') {

			//pr($calenderElementsArr);
			$i = 0;
			$color = '';
			$vass = '{ "success": 1, "result": [';
			$vas = "";
			foreach ($calenderElementsArr as $elem) {
				$color = str_replace("panel", "bg", $elem['color_code']);
				$elem['class'] = $color;
				$vas .= json_encode($elem, JSON_UNESCAPED_SLASHES) . ",";
				$i++;
			}
			$vasss = ' ] } ';
			//echo $vass . rtrim($vas, ",") . $vasss;
			return $vass . rtrim($vas, ",") . $vasss;
			//return $calenderElementsArr;
		} else if (isset($type) && !empty($type) && $type == 'mindate') {
			return $mindate;
		}
	}


	public function element_by_ws_and_status_old($project_id = null, $user_id = null, $project_type = null, $workspace_id = null, $status = null, $type = null, $criticalEleStatus = 0, $uId = 0, $gId = 0, $sharing_type = 0, $assignmentStatus = null) {
		$mindate = date("Y-m-d");

		// echo 'project_id : '.$project_id, 'user_id : '.$user_id, 'project_type : '.$project_type, 'wrk_id : '.$workspace_id, 'status : '.$status, 'type : '.$type;
		// pr($this->params['pass'] );

		if ($this->RequestHandler->isAjax() && $type == 'calender') {
			$this->layout = false;
			$this->autoRender = false;
			$project_id = isset($this->params['pass'][0]) ? $this->params['pass'][0] : '';
			$user_id = isset($this->params['pass'][1]) ? $this->params['pass'][1] : '';
			$project_type = isset($this->params['pass'][2]) ? $this->params['pass'][2] : '';
			$workspace_id = isset($this->params['pass'][3]) ? $this->params['pass'][3] : '';
			$status = isset($this->params['pass'][4]) ? $this->params['pass'][4] : '';
			$type = isset($this->params['pass'][5]) ? $this->params['pass'][5] : '';
		}

		/*  performace work 04 may 2020 added recursive */

		$project = $this->Project->find("first", array("conditions" => array("Project.id" => $project_id), "fields" => array(),'recursive'=> -1));

		$us_permission = $this->Common->userproject($project_id, $user_id);
		$pr_permission = $this->Common->project_permission_details($project_id, $user_id);
		//$ws_permission = $this->Common->work_permission_details($project_id, $user_id);

		$grp_id = $this->Group->GroupIDbyUserID($project_id, $user_id);
		$gpid = $grp_id;
		if (isset($grp_id) && !empty($grp_id)) {
			$group_permission = $this->Group->group_permission_details($project_id, $grp_id);

			$pr_permission = $this->Group->group_permission_details($project_id, $grp_id);

			if (isset($group_permission['ProjectPermission']['project_level']) && $group_permission['ProjectPermission']['project_level'] == 1) {
				$project_level = $group_permission['ProjectPermission']['project_level'];
				$this->set('project_level', $project_level);
			}
		}

		if (isset($pr_permission) && !empty($pr_permission)) {
			$ws_permission = $this->Common->work_permission_details($project_id, $user_id);
			$ims = (implode(',', $ws_permission) == '') ? '0' : implode(',', $ws_permission);
		}
		if (isset($grp_id) && !empty($grp_id)) {
			$ws_permission = $this->Group->group_work_permission_details($project_id, $grp_id);
			$ims = (implode(',', $ws_permission) == '') ? '0' : implode(',', $ws_permission);
		}

		$cur_date = date("Y-m-d 12:00:00");
		$cur_end_date = date("Y-m-d");
		$elementsArr = array();
		$calenderElementsArr = array();
		$ws_cond = ($workspace_id != '') ? "ProjectWorkspace.workspace_id = '" . $workspace_id . "' and " : "";
		if (isset($project_type) && !empty($project_type) && $project_type == 'm_project') {

			$allWorkspaceByMyProject = $this->ProjectWorkspace->query("select Workspace.id as id,Workspace.title as title,Workspace.start_date as start_date,Workspace.end_date as end_date,Workspace.color_code as color_code,Workspace.created as created from project_workspaces as ProjectWorkspace left join workspaces as Workspace on ProjectWorkspace.workspace_id=Workspace.id  where $ws_cond ProjectWorkspace.project_id = '" . $project_id . "' and Workspace.studio_status != 1 order by ProjectWorkspace.sort_order");

			if (!empty($uId) && $gId < 1 && $sharing_type != 'owner') {
				$this->loadModel('WorkspacePermission');
				$allWorkspaceidS = $this->WorkspacePermission->find("list", array('conditions' => array('WorkspacePermission.user_id' => $uId, 'WorkspacePermission.user_project_id' => project_upid($project_id)), 'fields' => 'WorkspacePermission.id,WorkspacePermission.project_workspace_id'));

				$iid = implode(',', $allWorkspaceidS);

				$allWorkspaceByMyProject = $this->ProjectWorkspace->query("select Workspace.id as id,Workspace.title as title,Workspace.start_date as start_date,Workspace.end_date as end_date,Workspace.color_code as color_code,Workspace.created as created from project_workspaces as ProjectWorkspace left join workspaces as Workspace on ProjectWorkspace.workspace_id=Workspace.id  where $ws_cond ProjectWorkspace.project_id = '" . $project_id . "' and Workspace.studio_status != 1 and ProjectWorkspace.id in ($iid) order by ProjectWorkspace.sort_order");

			}

			if (!empty($uId) && !empty($gId)) {
				$this->loadModel('WorkspacePermission');
				$allWorkspaceidS = $this->WorkspacePermission->find("list", array('conditions' => array('WorkspacePermission.project_group_id' => $gId, 'WorkspacePermission.user_project_id' => project_upid($project_id)), 'fields' => 'WorkspacePermission.id,WorkspacePermission.project_workspace_id'));

				$iid = implode(',', $allWorkspaceidS);

				$allWorkspaceByMyProject = $this->ProjectWorkspace->query("select Workspace.id as id,Workspace.title as title,Workspace.start_date as start_date,Workspace.end_date as end_date,Workspace.color_code as color_code,Workspace.created as created from project_workspaces as ProjectWorkspace left join workspaces as Workspace on ProjectWorkspace.workspace_id=Workspace.id  where $ws_cond ProjectWorkspace.project_id = '" . $project_id . "' and Workspace.studio_status != 1 and ProjectWorkspace.id in ($iid) order by ProjectWorkspace.sort_order");
			}

			foreach ($allWorkspaceByMyProject as $keyW => $workspaceArr) {
				$wId = $workspaceArr['Workspace']['id'];
				$areaByWSid = $this->Area->query("select * from areas as Area where Area.workspace_id= '" . $wId . "'   and Area.studio_status !=1 order by id asc");
				$elementsArr[] = $workspaceArr;
				foreach ($areaByWSid as $keyA => $areaV) {
					$elementsArr[$keyW]['Workspace']['Area'][] = $areaV['Area'];
					$areaId = $areaV['Area']['id'];

					$status_conditions = "Element.area_id= '" . $areaId . "'";

					if ($status == 'NON') {
						$status_conditions = 'Element.area_id= "' . $areaId . '" AND Element.date_constraints IS NOT NULL AND Element.date_constraints < 1';
					} else if ($status == 'PND') {
						$status_conditions = 'Element.area_id= "' . $areaId . '" AND Element.start_date > "' . $cur_date . '"';
					} else if ($status == 'PRG') {
						$status_conditions = 'Element.area_id= "' . $areaId . '" AND Element.start_date <= "' . $cur_date . '" AND Element.end_date >= "' . $cur_date . '" AND Element.sign_off !="1"';
					} else if ($status == 'OVD') {
						$status_conditions = 'Element.area_id= "' . $areaId . '" AND Element.end_date < "' . $cur_date . '" AND Element.sign_off !="1"';
					} else if ($status == 'CMP') {
						$status_conditions = 'Element.area_id= "' . $areaId . '" AND Element.sign_off="1"';
					}

					$elementByAreaid = $this->Element->query("select Element.* from elements as Element where $status_conditions and Element.studio_status !=1 order by start_date asc");

					if (!empty($criticalEleStatus) && $criticalEleStatus == 1) {
						$elementByAreaid = $this->Element->query("select Element.*,ElementDependency.* from elements as Element INNER JOIN element_dependencies as ElementDependency ON Element.id=ElementDependency.element_id  where $status_conditions and Element.studio_status !=1 and ElementDependency.is_critical =1 order by start_date asc");
					}

					if (isset($assignmentStatus) && $assignmentStatus == 'assignfilter') {

						$this->loadModel('ElementAssignment');
						$assignElement = $this->ElementAssignment->find('all',
							array(
								'conditions' => array('ElementAssignment.project_id' => $project_id),
								'fields' => array('ElementAssignment.element_id'),
							)
						);
						if (isset($assignElement) && !empty($assignElement)) {

							$elementids = Set::extract($assignElement, '/ElementAssignment/element_id');
							if (isset($elementids)) {
								$element_ids = implode(',', $elementids);
								$elementByAreaid = $this->Element->query("select Element.* from elements as Element where  $status_conditions and Element.studio_status !=1 and Element.id NOT IN (" . $element_ids . ") order by start_date asc");
							}

						}

					}

					/* if (!empty($uId) && !empty($gId)) {
						$elementByAreaid = array();
						$elementByAreaid = $this->Element->query("select Element.*,ElementPermission.* from elements as Element INNER JOIN element_permissions as ElementPermission ON Element.id=ElementPermission.element_id  where $status_conditions and Element.studio_status !=1 and ElementPermission.project_group_id =$gId order by start_date asc");

						$elementByAreaid2 = $this->Element->query("select Element.*,ElementPermission.* from elements as Element INNER JOIN element_permissions as ElementPermission ON Element.id=ElementPermission.element_id  where $status_conditions and Element.studio_status !=1 and ElementPermission.user_id =$uId order by start_date asc");

						if (isset($elementByAreaid2) && !empty($elementByAreaid2)) {
							$elementByAreaid = array_merge($elementByAreaid2, $elementByAreaid);
						}

					} */
					/* if (!empty($uId) && $gId < 1 ) { */

					if (!empty($uId)) {
						$elementAssignedByAreaid = $this->ElementAssignment->find("list", array('conditions' => array("ElementAssignment.assigned_to" => $uId, "ElementAssignment.project_id" => $project_id), 'fields' => array('ElementAssignment.id', 'ElementAssignment.element_id')));

						if (isset($elementAssignedByAreaid) && !empty($elementAssignedByAreaid)) {
							$explode = implode(",", $elementAssignedByAreaid);

							//echo "select Element.*  from elements as Element    where $status_conditions and  Element.studio_status !=1  and Element.id in(".$explode.")  order by start_date asc";

							$elementByAreaid = $this->Element->query("select Element.*  from elements as Element    where $status_conditions and  Element.studio_status !=1  and Element.id in(" . $explode . ")  order by start_date asc");
							//$elementByAreaid = $this->Element->query("select Element.* from elements as Element where Element.id in(".$explode.") order by start_date asc");

						}

						//pr($elementByAreaid ); die;
					}

					// pr($elementByAreaid);

					foreach ($elementByAreaid as $keyE => $valE) {

						$elementsArr[$keyW]['Workspace']['Area'][$keyA]['Element'][$keyE] = $elementByAreaid[$keyE]['Element'];

						if (!empty($valE['Element']['start_date']) && date("Y-m-d", strtotime($valE['Element']['start_date'])) < $mindate) {
							$mindate = date("Y-m-d", strtotime($valE['Element']['start_date']));
						}
						//its user for clender array

						$calenderElementsArr[$valE['Element']['id']]['id'] = strip_tags($valE['Element']['id']);
						$calenderElementsArr[$valE['Element']['id']]['color_code'] = strip_tags($valE['Element']['color_code']);
						$calenderElementsArr[$valE['Element']['id']]['descriptions'] = substr(strip_tags($project['Project']['description']), 0, 230);
						$calenderElementsArr[$valE['Element']['id']]['project_id'] = strip_tags($project['Project']['id']);
						$calenderElementsArr[$valE['Element']['id']]['user_id'] = $user_id;
						$calenderElementsArr[$valE['Element']['id']]['workspace_id'] = strip_tags($workspaceArr['Workspace']['id']);
						$calenderElementsArr[$valE['Element']['id']]['title'] = strip_tags($valE['Element']['title']);
						$calenderElementsArr[$valE['Element']['id']]['workspace'] = strip_tags($workspaceArr['Workspace']['title']);
						$calenderElementsArr[$valE['Element']['id']]['area'] = strip_tags($areaV['Area']['title']);
						$calenderElementsArr[$valE['Element']['id']]['project'] = strip_tags($project['Project']['title']);
						$calenderElementsArr[$valE['Element']['id']]['start_date'] = date('d M Y', strtotime($valE['Element']['start_date']));
						$calenderElementsArr[$valE['Element']['id']]['end_date'] = date('d M Y', strtotime($valE['Element']['end_date']));
						$calenderElementsArr[$valE['Element']['id']]['url'] = strip_tags(SITEURL . "/entities/update_element/" . $valE['Element']['id']);
						$calenderElementsArr[$valE['Element']['id']]['start'] = strtotime($valE['Element']['start_date']) * 1000;
						$calenderElementsArr[$valE['Element']['id']]['end'] = strtotime($valE['Element']['end_date']) * 1000;

						///// end here
					}
				}
			}
		} elseif (isset($project_type) && !empty($project_type) && ($project_type == 'r_project' || $project_type == 'g_project')) {
			if (isset($pr_permission) && $pr_permission['ProjectPermission']['project_level'] > 0) {
				$allWorkspaceByReceivedProjectId = $this->ProjectWorkspace->query("select Workspace.id as id,Workspace.title as title,Workspace.start_date as start_date,Workspace.end_date as end_date,Workspace.color_code as color_code ,Workspace.created as created  from project_workspaces as ProjectWorkspace left join workspaces as Workspace on ProjectWorkspace.workspace_id=Workspace.id  where $ws_cond ProjectWorkspace.project_id = '" . $project_id . "' and Workspace.studio_status != 1 order by ProjectWorkspace.sort_order");

				if (!empty($uId) && $gId < 1 && $sharing_type != 'owner') {
					$this->loadModel('WorkspacePermission');
					$allWorkspaceidS = $this->WorkspacePermission->find("list", array('conditions' => array('WorkspacePermission.user_id' => $uId, 'WorkspacePermission.user_project_id' => project_upid($project_id)), 'fields' => 'WorkspacePermission.id,WorkspacePermission.project_workspace_id'));

					$iid = implode(',', $allWorkspaceidS);

					$allWorkspaceByMyProject = $this->ProjectWorkspace->query("select Workspace.id as id,Workspace.title as title,Workspace.start_date as start_date,Workspace.end_date as end_date,Workspace.color_code as color_code,Workspace.created as created from project_workspaces as ProjectWorkspace left join workspaces as Workspace on ProjectWorkspace.workspace_id=Workspace.id  where $ws_cond ProjectWorkspace.project_id = '" . $project_id . "' and Workspace.studio_status != 1 and ProjectWorkspace.id in ($iid) order by ProjectWorkspace.sort_order");

				}

				if (!empty($uId) && !empty($gId) && $sharing_type != 'owner') {
					$this->loadModel('WorkspacePermission');
					$allWorkspaceidS = $this->WorkspacePermission->find("list", array('conditions' => array('WorkspacePermission.project_group_id' => $gId, 'WorkspacePermission.user_project_id' => project_upid($project_id)), 'fields' => 'WorkspacePermission.id,WorkspacePermission.project_workspace_id'));

					$iid = implode(',', $allWorkspaceidS);

					//pr($gId); die;
					$allWorkspaceByMyProject = $this->ProjectWorkspace->query("select Workspace.id as id,Workspace.title as title,Workspace.start_date as start_date,Workspace.end_date as end_date,Workspace.color_code as color_code,Workspace.created as created from project_workspaces as ProjectWorkspace left join workspaces as Workspace on ProjectWorkspace.workspace_id=Workspace.id  where $ws_cond ProjectWorkspace.project_id = '" . $project_id . "' and Workspace.studio_status != 1 and ProjectWorkspace.id in ($iid) order by ProjectWorkspace.sort_order");
				}

			} else {

				$allWorkspaceByReceivedProjectId = $this->ProjectWorkspace->query("select Workspace.id as id,Workspace.title as title,Workspace.start_date as start_date,Workspace.end_date as end_date,Workspace.color_code as color_code ,Workspace.created as created from project_workspaces as ProjectWorkspace left join workspaces as Workspace on ProjectWorkspace.workspace_id=Workspace.id  where $ws_cond ProjectWorkspace.project_id = '" . $project_id . "' AND ProjectWorkspace.id in ('" . $ims . "') and Workspace.studio_status != 1 order by ProjectWorkspace.sort_order");

			}

			foreach ($allWorkspaceByReceivedProjectId as $keyW => $workspaceArr) {
				$wId = $workspaceArr['Workspace']['id'];
				$areaByWSid = $this->Area->query("select * from areas as Area where Area.workspace_id= '" . $wId . "' and Area.studio_status !=1");
				$elementsArr[] = $workspaceArr;
				foreach ($areaByWSid as $keyA => $areaV) {
					$elementsArr[$keyW]['Workspace']['Area'][] = $areaV['Area'];
					$areaId = $areaV['Area']['id'];
					if (isset($pr_permission) && $pr_permission['ProjectPermission']['project_level'] > 0) {

						$status_conditions = "Element.area_id= '" . $areaId . "'";
						if ($status == 'NON') {
							$status_conditions = 'Element.area_id= "' . $areaId . '" AND date_constraints IS NOT NULL AND date_constraints < 1';
						} else if ($status == 'PND') {
							$status_conditions = 'Element.area_id= "' . $areaId . '" AND start_date > "' . $cur_date . '"';
						} else if ($status == 'PRG') {
							$status_conditions = 'Element.area_id= "' . $areaId . '" AND start_date <= "' . $cur_date . '" AND end_date >= "' . $cur_date . '" AND Element.sign_off !="1"';
						} else if ($status == 'OVD') {
							$status_conditions = 'Element.area_id= "' . $areaId . '" AND end_date < "' . $cur_date . '"';
						} else if ($status == 'CMP') {
							$status_conditions = 'Element.area_id= "' . $areaId . '" AND sign_off="1"';
						}

						$elementByAreaid = $this->Element->query("select * from elements as Element where $status_conditions and Element.studio_status !=1 order by start_date asc");

						if (!empty($criticalEleStatus) && $criticalEleStatus == 1) {
							$elementByAreaid = $this->Element->query("select Element.*,ElementDependency.* from elements as Element INNER JOIN element_dependencies as ElementDependency ON Element.id=ElementDependency.element_id  where   $status_conditions and Element.studio_status !=1 and ElementDependency.is_critical =1 order by start_date asc");
						}

						if (isset($assignmentStatus) && $assignmentStatus == 'assignfilter') {

							$this->loadModel('ElementAssignment');
							$assignElement = $this->ElementAssignment->find('all',
								array(
									'conditions' => array('ElementAssignment.project_id' => $project_id),
									'fields' => array('ElementAssignment.element_id'),
								)
							);
							if (isset($assignElement) && !empty($assignElement)) {

								$elementids = Set::extract($assignElement, '/ElementAssignment/element_id');
								if (isset($elementids)) {
									$element_ids = implode(',', $elementids);
									$elementByAreaid = $this->Element->query("select Element.* from elements as Element where  $status_conditions and Element.studio_status !=1 and Element.id NOT IN (" . $element_ids . ") order by start_date asc");
								}

							}

						}

						/* if (!empty($uId) && !empty($gId)) {
							$elementByAreaid = array();
							$elementByAreaid = $this->Element->query("select Element.*,ElementPermission.* from elements as Element INNER JOIN element_permissions as ElementPermission ON Element.id=ElementPermission.element_id  where $status_conditions and Element.studio_status !=1 and ElementPermission.project_group_id =$gId order by start_date asc");

							$elementByAreaid2 = $this->Element->query("select Element.*,ElementPermission.* from elements as Element INNER JOIN element_permissions as ElementPermission ON Element.id=ElementPermission.element_id  where $status_conditions and Element.studio_status !=1 and ElementPermission.user_id =$uId order by start_date asc");

							if (isset($elementByAreaid2) && !empty($elementByAreaid2)) {
								$elementByAreaid = array_merge($elementByAreaid2, $elementByAreaid);
							}

						} */
						/* if (!empty($uId) && $gId < 1) {
							$elementByAreaid = $this->Element->query("select Element.*,ElementPermission.* from elements as Element INNER JOIN element_permissions as ElementPermission ON Element.id=ElementPermission.element_id  where $status_conditions and Element.studio_status !=1 and ElementPermission.user_id =$uId order by start_date asc");
						} */
						if (!empty($uId)) {
							//	$elementByAreaid = $this->Element->query("select Element.*,ElementPermission.* from elements as Element INNER JOIN element_permissions as ElementPermission ON Element.id=ElementPermission.element_id  where $status_conditions and Element.studio_status !=1 and ElementPermission.user_id =$uId order by start_date asc");

							$elementAssignedByAreaid = $this->ElementAssignment->find("list", array('conditions' => array("ElementAssignment.assigned_to" => $uId, "ElementAssignment.project_id" => $project_id), 'fields' => array('ElementAssignment.id', 'ElementAssignment.element_id')));

							if (isset($elementAssignedByAreaid) && !empty($elementAssignedByAreaid)) {
								$explode = implode(",", $elementAssignedByAreaid);

								$elementByAreaid = $this->Element->query("select Element.*  from elements as Element    where $status_conditions and  Element.studio_status !=1  and Element.id in(" . $explode . ")  order by start_date asc");

							}

						}

					} else {
						$receivedElements = $this->Common->element_permission_details($wId, $project_id, $user_id);
						if ((isset($grp_id) && !empty($grp_id))) {
							$receivedElements = $this->Group->group_element_permission_details($wId, $project_id, $grp_id);
						}

						$elementsId = (implode(',', $receivedElements) == '') ? '0' : implode(',', $receivedElements);
						$status_conditions = "Element.area_id= '" . $areaId . "' AND Element.id in (" . $elementsId . ")";
						if ($status == 'NON') {
							$status_conditions = 'Element.area_id= "' . $areaId . '" and Element.id in (' . $elementsId . ') AND date_constraints IS NOT NULL AND date_constraints < 1';
						} else if ($status == 'PND') {
							$status_conditions = 'Element.area_id= "' . $areaId . '" and Element.id in (' . $elementsId . ') AND start_date > "' . $cur_date . '"';
						} else if ($status == 'PRG') {
							$status_conditions = 'Element.area_id= "' . $areaId . '" and Element.id in (' . $elementsId . ') AND start_date <= "' . $cur_date . '" AND end_date >= "' . $cur_date . '" AND Element.sign_off !="1"';
						} else if ($status == 'OVD') {
							$status_conditions = 'Element.area_id= "' . $areaId . '" and Element.id in (' . $elementsId . ') AND end_date < "' . $cur_date . '"';
						} else if ($status == 'CMP') {
							$status_conditions = 'Element.area_id= "' . $areaId . '" and Element.id in (' . $elementsId . ') AND sign_off="1"';
						}

						$elementByAreaid = $this->Element->query("select * from elements as Element where $status_conditions and Element.studio_status !=1 order by start_date asc");

						if (!empty($criticalEleStatus) && $criticalEleStatus == 1) {
							$elementByAreaid = $this->Element->query("select Element.*,ElementDependency.* from elements as Element INNER JOIN element_dependencies as ElementDependency ON Element.id=ElementDependency.element_id where $status_conditions and Element.studio_status !=1 and ElementDependency.is_critical =1 order by start_date asc");
						}

						if (isset($assignmentStatus) && $assignmentStatus == 'assignfilter') {

							$this->loadModel('ElementAssignment');
							$assignElement = $this->ElementAssignment->find('all',
								array(
									'conditions' => array('ElementAssignment.project_id' => $project_id),
									'fields' => array('ElementAssignment.element_id'),
								)
							);
							if (isset($assignElement) && !empty($assignElement)) {

								$elementids = Set::extract($assignElement, '/ElementAssignment/element_id');
								if (isset($elementids)) {
									$element_ids = implode(',', $elementids);
									$elementByAreaid = $this->Element->query("select Element.* from elements as Element where  $status_conditions and Element.studio_status !=1 and Element.id NOT IN (" . $element_ids . ") order by start_date asc");
								}

							}

						}

						/* if (!empty($uId) && !empty($gId)) {
							$elementByAreaid = array();

							$elementByAreaid = $this->Element->query("select Element.*,ElementPermission.* from elements as Element INNER JOIN element_permissions as ElementPermission ON Element.id=ElementPermission.element_id  where $status_conditions and Element.studio_status !=1 and ElementPermission.project_group_id =$gId order by start_date asc");

							$elementByAreaid2 = $this->Element->query("select Element.*,ElementPermission.* from elements as Element INNER JOIN element_permissions as ElementPermission ON Element.id=ElementPermission.element_id  where $status_conditions and Element.studio_status !=1 and ElementPermission.user_id =$uId order by start_date asc");

							if (isset($elementByAreaid2) && !empty($elementByAreaid2)) {
								$elementByAreaid = array_merge($elementByAreaid2, $elementByAreaid);
							}

						} */
						/* if (!empty($uId) && $gId < 1) {
							$elementByAreaid = $this->Element->query("select Element.*,ElementPermission.* from elements as Element INNER JOIN element_permissions as ElementPermission ON Element.id=ElementPermission.element_id  where $status_conditions and Element.studio_status !=1 and ElementPermission.user_id =$uId order by start_date asc");
						} */

						if (!empty($uId)) {
							//	$elementByAreaid = $this->Element->query("select Element.*,ElementPermission.* from elements as Element INNER JOIN element_permissions as ElementPermission ON Element.id=ElementPermission.element_id  where $status_conditions and Element.studio_status !=1 and ElementPermission.user_id =$uId order by start_date asc");

							$elementAssignedByAreaid = $this->ElementAssignment->find("list", array('conditions' => array("ElementAssignment.assigned_to" => $uId, "ElementAssignment.project_id" => $project_id), 'fields' => array('ElementAssignment.id', 'ElementAssignment.element_id')));

							if (isset($elementAssignedByAreaid) && !empty($elementAssignedByAreaid)) {
								$explode = implode(",", $elementAssignedByAreaid);

								$elementByAreaid = $this->Element->query("select Element.*  from elements as Element    where $status_conditions and  Element.studio_status !=1  and Element.id in(" . $explode . ")  order by start_date asc");

							}

						}

					}
					foreach ($elementByAreaid as $keyE => $valE) {
		//pr($valE);
						$elementsArr[$keyW]['Workspace']['Area'][$keyA]['Element'][] = $valE['Element'];

						if (!empty($valE['Element']['start_date']) && date("Y-m-d", strtotime($valE['Element']['start_date'])) < $mindate) {
							$mindate = date("Y-m-d", strtotime($valE['Element']['start_date']));
						}
						//its user for clender array

						$calenderElementsArr[$valE['Element']['id']]['id'] = strip_tags($valE['Element']['id']);
						$calenderElementsArr[$valE['Element']['id']]['color_code'] = strip_tags($valE['Element']['color_code']);
						$calenderElementsArr[$valE['Element']['id']]['descriptions'] = substr(strip_tags($project['Project']['description']), 0, 230);
						$calenderElementsArr[$valE['Element']['id']]['project_id'] = strip_tags($project['Project']['id']);
						$calenderElementsArr[$valE['Element']['id']]['user_id'] = $user_id;
						$calenderElementsArr[$valE['Element']['id']]['workspace_id'] = strip_tags($workspaceArr['Workspace']['id']);
						$calenderElementsArr[$valE['Element']['id']]['title'] = strip_tags($valE['Element']['title']);
						$calenderElementsArr[$valE['Element']['id']]['workspace'] = strip_tags($workspaceArr['Workspace']['title']);
						$calenderElementsArr[$valE['Element']['id']]['area'] = strip_tags($areaV['Area']['title']);
						$calenderElementsArr[$valE['Element']['id']]['project'] = strip_tags($project['Project']['title']);
						$calenderElementsArr[$valE['Element']['id']]['start_date'] = date('d M Y', strtotime($valE['Element']['start_date']));
						$calenderElementsArr[$valE['Element']['id']]['end_date'] = date('d M Y', strtotime($valE['Element']['end_date']));
						$calenderElementsArr[$valE['Element']['id']]['url'] = strip_tags(SITEURL . "/entities/update_element/" . $valE['Element']['id']);
						$calenderElementsArr[$valE['Element']['id']]['start'] = strtotime($valE['Element']['start_date']) * 1000;
						$calenderElementsArr[$valE['Element']['id']]['end'] = strtotime($valE['Element']['end_date']) * 1000;

						///// end here
					}
				}
			}
		}

		if (isset($type) && !empty($type) && $type == 'gantt') {

			return $elementsArr;
		} else if (isset($type) && !empty($type) && $type == 'calender') {

			//pr($calenderElementsArr);
			$i = 0;
			$color = '';
			$vass = '{ "success": 1, "result": [';
			$vas = "";
			foreach ($calenderElementsArr as $elem) {
				$color = str_replace("panel", "bg", $elem['color_code']);
				$elem['class'] = $color;
				$vas .= json_encode($elem, JSON_UNESCAPED_SLASHES) . ",";
				$i++;
			}
			$vasss = ' ] } ';
			//echo $vass . rtrim($vas, ",") . $vasss;
			return $vass . rtrim($vas, ",") . $vasss;
			//return $calenderElementsArr;
		} else if (isset($type) && !empty($type) && $type == 'mindate') {
			return $mindate;
		}
	}

	public function test($pid = null) {

$this->set('title_for_layout', __('Planner', true));
		$this->layout = "inner";
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
		if (!isset($this->params->named) || empty($this->params->named)) {
			// project type coming from UserPermission
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

		/*  performace work 04 may 2020 added recursive */

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
		//$this->Project->unbindAll();


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
		/* $conditions = array('conditions' => array('ElementAssignment.project_id' => $project_id, 'ElementAssignment.reaction !=' => 3), 'fields' => array('ElementAssignment.assigned_to'));
		$priorty_data = $this->ElementAssignment->find('list', $conditions); */

		$wsp_tasks_data = $this->objView->loadHelper('Permission')->wsp_tasks_data($project_id);


		$this->set('project_type', $project_type);
		$this->set("projects", $project);
		$this->set('crumb', $crumb);
		$this->set(compact(
				"project_id", "project_name", "workspace_id", "project_type", "myWorkspaceslistByproject","priorty_data", "allProjects"
			)
		);


		$this->set('wsp_tasks_data', $wsp_tasks_data);


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
		if (!isset($this->params->named) || empty($this->params->named)) {
			// project type coming from UserPermission
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

		/*  performace work 04 may 2020 added recursive */

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
		//$this->Project->unbindAll();


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
		/* $conditions = array('conditions' => array('ElementAssignment.project_id' => $project_id, 'ElementAssignment.reaction !=' => 3), 'fields' => array('ElementAssignment.assigned_to'));
		$priorty_data = $this->ElementAssignment->find('list', $conditions); */

		$wsp_tasks_data = $this->objView->loadHelper('Permission')->wsp_tasks_data($project_id);


		$this->set('project_type', $project_type);
		$this->set("projects", $project);
		$this->set('crumb', $crumb);
		$this->set(compact(
				"project_id", "project_name", "workspace_id", "project_type", "myWorkspaceslistByproject","priorty_data", "allProjects"
			)
		);


		$this->set('wsp_tasks_data', $wsp_tasks_data);


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

	public function project_popup_box($id = null, $type = null, $project_id = null) {
		$this->autoRender = false;
		$this->layout = 'ajax';
		$this->loadModel('Element');
		$this->loadModel('Workspace');
		$this->loadModel('Project');
		$this->loadModel('Area');
		$user_id = $this->Auth->user('id');
		if ($this->RequestHandler->isAjax()) {
			$project = $this->Project->findById($project_id);
			$projectData = '';
			if (isset($type) && !empty($type) && $type == "project") {
				$projectData = $this->Project->findById($id);
				$this->set(compact('projectData'));
			}
			$this->set(compact('project'));
			$this->render('gantt/project_popup_box');
		}
	}

	public function popup_box($id = null, $type = null, $project_id = null) {
		$this->autoRender = false;
		$this->layout = 'ajax';
		$this->loadModel('Element');
		$this->loadModel('Workspace');
		$this->loadModel('Project');
		$this->loadModel('Area');
		$user_id = $this->Auth->user('id');
		if ($this->RequestHandler->isAjax()) {

			$elementData = array();
			$workspaceData = array();

			if (isset($type) && $type == 'workspace') {
				$workspaceData = $this->Workspace->findById($id);
			} else {
				$elementData = $this->Element->findById($id);
			}
			$project = $this->Project->findById($project_id);
			$this->set(compact('project', 'elementData', 'workspaceData'));
			$this->render('gantt/popup_box');
		}
	}

	public function element_popup_box($id = null, $type = null, $project_id = null) {
		$this->autoRender = false;
		$this->layout = 'ajax';
		$this->loadModel('Element');
		$this->loadModel('Workspace');
		$this->loadModel('Project');
		$this->loadModel('Area');
		$user_id = $this->Auth->user('id');
		if ($this->RequestHandler->isAjax()) {
			$project = $this->Project->findById($project_id);

			$this->Element->recursive = 2;
			$elementData = '';

			if (isset($type) && !empty($type) && $type == "element") {
				$elementData = $this->Element->findById($id);
				$this->set(compact('elementData'));
			}

			$this->set(compact('project'));
			$this->render('gantt/element_popup_box');
		}
	}

	public function workspace_popup_box($id = null, $type = null, $project_id = null) {
		$this->autoRender = false;
		$this->layout = 'ajax';
		$this->loadModel('Element');
		$this->loadModel('Workspace');
		$this->loadModel('Project');
		$this->loadModel('Area');
		$user_id = $this->Auth->user('id');
		if ($this->RequestHandler->isAjax()) {
			$project = $this->Project->findById($project_id);

			$this->Element->recursive = 2;
			$workspaceData = '';

			if (isset($type) && !empty($type) && $type == "workspace") {
				$workspaceData = $this->Workspace->findById($id);
				$this->set(compact('workspaceData'));
			}
			$this->set(compact('project'));
			$this->render('gantt/workspace_popup_box');
		}
	}

	public function workspace($project_id) {
		$this->loadModel('Project');
		//$pd = $this->Project->find('first', array('conditions' => array('Project.id' => $id,'Project.studio_status !='=>1), 'recursive' => '2'));
		$pd = get_project_workspace($project_id);
		$this->set("projects", $pd);

		return $pd;
	}

	public function area($id) {
		$this->loadModel('Area');
		$data = $this->Area->find('all', array('conditions' => array('Area.workspace_id' => $id, 'Area.studio_status !=' => 1), 'fields' => array('Area.*'), 'recursive' => 1));
		return isset($data) ? $data : array();
	}

	public function documents($id) {
		$this->loadModel('ElementDocument');
		$data = $this->ElementDocument->find('all', array('conditions' => array('ElementDocument.element_id' => $id), 'fields' => array('ElementDocument.*'), 'recursive' => 1, 'order' => 'ElementDocument.title'));
		return isset($data) ? $data : array();
	}

	public function links($id) {
		$this->loadModel('ElementLink');
		$data = $this->ElementLink->find('all', array('conditions' => array('ElementLink.element_id' => $id), 'fields' => array('ElementLink.*'), 'recursive' => 1, 'order' => 'ElementLink.title'));
		return isset($data) ? $data : array();
	}

	public function mms($id) {
		$this->loadModel('ElementMindmap');
		$data = $this->ElementMindmap->find('all', array('conditions' => array('ElementMindmap.element_id' => $id), 'fields' => array('ElementMindmap.*'), 'recursive' => 1, 'order' => 'ElementMindmap.title'));
		return isset($data) ? $data : array();
	}

	public function notes($id) {
		$this->loadModel('ElementNote');
		$data = $this->ElementNote->find('all', array('conditions' => array('ElementNote.element_id' => $id), 'fields' => array('ElementNote.*'), 'recursive' => 1, 'order' => 'ElementNote.title'));
		return isset($data) ? $data : array();
	}

	public function feedbacks($id) {
		$this->loadModel('Feedback');
		$data = $this->Feedback->find('all', array('conditions' => array('Feedback.element_id' => $id), 'fields' => array('Feedback.*'), 'recursive' => 1, 'order' => 'Feedback.title'));
		return isset($data) ? $data : array();
	}

	public function votes($id) {
		$this->loadModel('Vote');
		$data = $this->Vote->find('all', array('conditions' => array('Vote.element_id' => $id, 'VoteQuestion.id !=' => ''), 'fields' => array('Vote.*'), 'order' => 'Vote.Title', 'recursive' => 2));

		return isset($data) ? $data : array();
	}

	public function decision($id) {
		$this->loadModel('ElementDecision');
		$data = $this->ElementDecision->find('all', array('conditions' => array('ElementDecision.element_id' => $id), 'fields' => array('ElementDecision.*'), 'order' => 'ElementDecision.title'));

		return isset($data) ? $data : array();
	}

	public function projects_old($project_id = null) {

		$this->layout = 'inner';
		$this->loadModel('Project');
		$this->set('title_for_layout', __('Show Resources', true));
		$this->set('page_heading', __('Show Resources', true));
		$user_id = $this->Auth->user('id');
		$this->set('user_id', $user_id);
		$this->set('session_id', $this->Session->id());
		$project_id = $project_type = '';
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
		$this->Project->id = $project_id;
		$this->set(compact("project_type", "project_id"));

		if ($project_id == '' || !$this->Project->exists() && $project_type == 'm_project') {
			$this->Session->setFlash(__('Invalid Project Id.'));
			$this->redirect(SITEURL . 'projects/lists');
		}
		if ($project_id == '' || !$this->Project->exists() && $project_type == 'r_project') {
			$this->Session->setFlash(__('Invalid Project Id.'));
			$this->redirect(SITEURL . 'projects/share_lists');
		}
		if ($project_id == '' || !$this->Project->exists() && $project_type == 'g_project') {
			$this->Session->setFlash(__('Invalid Project Id.'));
			$this->redirect(SITEURL . 'groups/shared_projects');
		}

		// Find All current user's projects
		$myprojectlist = $this->__myproject_selectbox($user_id);
		// Find All current user's received projects
		$myreceivedprojectlist = $this->__receivedproject_selectbox($user_id, 1);
		// Find All current user's group projects
		$mygroupprojectlist = $this->__groupproject_selectbox($user_id, 1);

		$this->set(compact("myprojectlist", "myreceivedprojectlist", "mygroupprojectlist"));

		if (isset($this->params->query['types']) && !empty($this->params->query['types'])) {
			$typesArr = explode(",", $this->params->query['types']);
			foreach ($typesArr as $key => $val_selectbox) {
				$this->set($val_selectbox, $val_selectbox);
			}
			$this->set('typesArr', $typesArr);
		}

		$project_where['UserProject.user_id'] = $user_id;
		$project_order = [];
		if (isset($project_id) && !empty($project_id)) {
			$project_where['Project.id'] = $project_id;
		} else {
			$project_order = array('UserProject.modified DESC');
		}

		$conditionsN = null;
		$conditionsN['ProjectPermission.user_id'] = $user_id;
		$conditionsN['ProjectPermission.user_project_id'] = $this->Common->get_up_id($project_id, $user_id);

		$this->loadModel('ProjectPermission');
		$projects_shared = $this->ProjectPermission->find('first', array(
			'conditions' => $conditionsN,
			'fields' => array('ProjectPermission.user_project_id'),
			'order' => 'ProjectPermission.created DESC',
			'recursive' => -1,
		));

		$grp_id = $this->Group->GroupIDbyUserID($project_id, $user_id);
		if (isset($grp_id) && !empty($grp_id)) {
			$group_permission = $this->Group->group_permission_details($project_id, $grp_id);

			if (isset($group_permission['ProjectPermission']['project_level']) && $group_permission['ProjectPermission']['project_level'] == 1) {
				$project_level = $group_permission['ProjectPermission']['project_level'];
				$this->set('project_level', $project_level);
			}
			$this->set('gpid', $grp_id);
		}

		$this->Project->recursive = 1;
		$projects = $this->UserProject->find('all', array('conditions' => $project_where, 'order' => $project_order));

		if (empty($projects) && !empty($projects_shared)) {
			$project_where = NULL;
			$project_where['Project.id'] = $project_id;
			$projects = $this->UserProject->find('first', array('conditions' => $project_where, 'order' => $project_order));
		}

		if (empty($projects) && !empty($group_permission)) {
			$project_where = NULL;
			$project_where['Project.id'] = $project_id;
			$projects = $this->UserProject->find('first', array('conditions' => $project_where, 'order' => $project_order));
		}

		$paginator = array(
			// 'fields' => array(
			// 'Template.id', 'Template.title', 'Template.description', 'Template.layout_preview'
			// ),
			'conditions' => array(
				'UserProject.status' => 1,
				$project_where,
			),
			'limit' => 8,
			"order" => "UserProject.id ASC",
		);

		$this->paginate = $paginator;
		$this->set('projects', $this->paginate('UserProject'));

		$project_title = '';
		$prdata = null;
		$this->loadModel('ProjectWorkspace');

		//$cat_crumb = get_category_list($project_id);

		$crumb = [
			'last' => ['Show Resources'],
		];

		if (isset($project_id) && !empty($project_id)) {
			$prdata = $this->ProjectWorkspace->find('first', ['recursive' => 1, 'conditions' => ['ProjectWorkspace.project_id' => $project_id, 'Workspace.studio_status !=' => 1]]);

			$project_title = (isset($prdata) && !empty($prdata)) ? _strip_tags($prdata['Project']['title']) : '';
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

		/*if (isset($cat_crumb) && !empty($cat_crumb)) {
			$crumb = array_merge($cat_crumb, $crumb);
		}*/

		$this->set('crumb', $crumb);

		$this->set("project_id", $project_id);
	}

	public function json($id) {
		$this->layout = false;
		$this->loadModel('Element');
		$this->Element->recursive = 1;
		$this->loadModel('Project');
		$user_id = $this->Auth->user('id');
		$this->loadModel('ProjectWorkspace');
		$this->set('title_for_layout', __('Calendar', true));

		if (!empty($id)) {
			$joins = array(
				array(
					'table' => 'workspaces',
					'alias' => 'Workspace',
					'type' => 'INNER',
					'conditions' => array(
						'Workspace.id = Area.workspace_id',
					),
				),
				array(
					'table' => 'project_workspaces',
					'alias' => 'ProjectWorkspace',
					'type' => 'INNER',
					'conditions' => array(
						'ProjectWorkspace.workspace_id = Area.workspace_id',
					)),
				array(
					'table' => 'projects',
					'alias' => 'Project',
					'type' => 'INNER',
					'conditions' => array(
						'Project.id = ProjectWorkspace.project_id', 'Project.id =' . $id,
					),
				),
				array(
					'table' => 'user_projects',
					'alias' => 'UserProject',
					'type' => 'INNER',
					'conditions' => array(
						'UserProject.project_id = ProjectWorkspace.project_id',
						'UserProject.user_id =' . $user_id,
					),
				),
			);

			$cond = array('Element.start_date !=' => '', 'Element.end_date !=' => '', 'Element.status' => '1', 'Element.studio_status !=' => 1);
			//$cond = '';

			$ele = $this->Element->find('all', array('conditions' => $cond, 'joins' => $joins, 'fields' => array('*')));
			//pr( $ele); die;
		} else {
			$ele = array();
		}
		$emtA = array();
		foreach ($ele as $emt) {
			$emtA[$emt['Element']['id']]['id'] = strip_tags($emt['Element']['id']);
			$emtA[$emt['Element']['id']]['descriptions'] = substr(strip_tags($emt['Project']['description']), 0, 230);
			$emtA[$emt['Element']['id']]['project_id'] = strip_tags($emt['Project']['id']);
			$emtA[$emt['Element']['id']]['user_id'] = $user_id;
			$emtA[$emt['Element']['id']]['workspace_id'] = strip_tags($emt['Workspace']['id']);
			$emtA[$emt['Element']['id']]['title'] = strip_tags($emt['Element']['title']);
			$emtA[$emt['Element']['id']]['workspace'] = strip_tags($emt['Workspace']['title']);
			$emtA[$emt['Element']['id']]['area'] = strip_tags($emt['Area']['title']);
			$emtA[$emt['Element']['id']]['project'] = strip_tags($emt['Project']['title']);
			$emtA[$emt['Element']['id']]['start_date'] = date('d M Y', strtotime($emt['Element']['start_date']));
			$emtA[$emt['Element']['id']]['end_date'] = date('d M Y', strtotime($emt['Element']['end_date']));
			$emtA[$emt['Element']['id']]['url'] = strip_tags(SITEURL . "/entities/update_element/" . $emt['Element']['id']);

			$emtA[$emt['Element']['id']]['start'] = strtotime($emt['Element']['start_date']) * 1000;
			$emtA[$emt['Element']['id']]['end'] = strtotime($emt['Element']['end_date']) * 1000;

			//$emtA[$emt['Element']['id']]['start'] = date('Y-m-d',strtotime($emt['Element']['start_date'])).'T'.date('H:i:s',strtotime($emt['Element']['start_date']));
			//  $emtA[$emt['Element']['id']]['end'] = date('Y-m-d',strtotime($emt['Element']['end_date'])).'T'.date('H:i:s',strtotime($emt['Element']['end_date']));
		}

		/* 	 		unset($emt['Element']);
			          unset($emt['ProjectWorkspace']);
			          unset($emt['Workspace']);
			          unset($emt['Project']);
			          unset($emt['UserProject']);
		*/

		$pd = $this->Project->find('first', array('conditions' => array('Project.id' => $id)));
		$this->set("projects", $pd);
		$this->set("project_id", $id);
		$this->set('crumb', ['Project' => '/projects/lists', 'last' => ['Calendar']]);
		$this->set('element', $emtA);

		$i = 0;
		$color = '';
		$vass = '{ "success": 1, "result": [';
		$vas = "";
		foreach ($emtA as $elem) {
			if ($i % 2 == 0) {
				$color = "event-info";
			} else {
				$color = "event-warning";
			}
			//$elem['backgroundColor'] = $color;
			$elem['class'] = $color;
			$vas .= json_encode($elem, JSON_UNESCAPED_SLASHES) . ",";

			//echo ;
			$i++;
		}

		$vasss = ' ] } ';

		echo $vass . rtrim($vas, ",") . $vasss;

		die;
		$this->render = false;
	}

	public function register() {
		// return $this->redirect(array('controller' => 'plans', 'action' => 'index', 'id' => $this->User->getLastInsertID(), 'admin' => false));

		if ($this->Auth->loggedIn()) {
			$this->Session->setFlash(__('You are already logged-in.'));
			return $this->redirect(SITEURL);
		}

		if (ORG_SETUP == true) {
			if (check_license() == false) {

				$this->Session->setFlash("All OpusView licences are allocated to users, Please contact to your administrator.", 'error');
				$_SESSION['data'] = $this->request->data;
				$this->redirect(array('controller' => 'users', 'action' => 'login', 'admin' => false));

			}
		}

		//$this->set('questionArray', $this->questionArray);

		$this->loadModel('UserPassword');

		if ($this->request->is('post') || $this->request->is('put')) {
			//$this->request->data['UserDetail']['question'] = $this->questionArray[$this->request->data['UserDetail']['question']];
			$this->request->data['User']['activation_key'] = $activatiinHash = $this->User->getActivationHash();
			$this->request->data['User']['role_id'] = SITE_USER;

			$this->User->create();

			$this->UserDetail->validator()->remove('org_name');
			$userData = null;
			/* if (isset($this->request->data['UserDetail']['membership_code']) && !empty($this->request->data['UserDetail']['membership_code'])) {
				$memcode = $this->request->data['UserDetail']['membership_code'];

				$userData = $this->UserInstitution->find('first', array('fields' => array('UserInstitution.*'), 'conditions' => array('UserInstitution.membership_code' => $memcode, 'UserInstitution.end >=' => date('Y-m-d 12:00:00'), 'UserInstitution.start <=' => date('Y-m-d 12:00:00'))));

				if (isset($userData) && !empty($userData)) {
					$userDetails = $this->UserDetail->find('first', array('fields' => array('UserDetail.*'), 'conditions' => array('UserDetail.user_id' => $userData['UserInstitution']['user_id'])));

					$userStatus = $this->User->find('first', array('fields' => array('User.status'), 'conditions' => array('User.id' => $userData['UserInstitution']['user_id'])));
				}
			} */

			if ((isset($userData) && !empty($userData)) && (!empty($userStatus['User']['status']))) {
				//pr($userStatus);
				//$associated_user_id = $userData['UserInstitution']['user_id'];
			} else {
				$associated_user_id = 0;
			}

			if (isset($memcode) && isset($associated_user_id) && ($associated_user_id == 0)) {

				$this->Session->setFlash(__('Invalid Membership code. Please, try again.'));
			} else {
				$this->request->data['UserDetail']['institution_id'] = $associated_user_id;
				unset($this->request->data['User']['individual']);
				//pr($this->request->data); die;

				//Updated 21th Nov 2016
				if (ORG_SETUP == true) {

					$userDomainName = explode("@", $this->request->data['User']['email']);
					//$userDomain = explode(".",$userDomainName[1]);
					$userDomain = $userDomainName[1];

					//$checkOrganisationDomain = $this->checkOrgDomain($userDomain[0]);
					// ========== Updated on 26th July 2017 =======================
					$checkOrganisationDomain = $this->checkOrgDomain($userDomain);
					// $checkOrganisationDomain = 1;
					if (!isset($checkOrganisationDomain) || empty($checkOrganisationDomain) || $checkOrganisationDomain <= 0) {

						$this->Session->setFlash("Supplied email domain does not allowed, please try again with valid email domain.", 'error');
						$_SESSION['data'] = $this->request->data;
						$this->redirect(array('controller' => 'users', 'action' => 'register'));

					}

					if (isset($checkOrganisationDomain) && !empty($checkOrganisationDomain) && $checkOrganisationDomain > 0) {

						if (isset($checkOrganisationDomain) && !empty($checkOrganisationDomain)) {

							$_SESSION['data'] = $this->request->data;

							$org_detail = $this->ManageDomain->findById($checkOrganisationDomain);

							$org_id = (isset($org_detail['ManageDomain']['org_id']) && !empty($org_detail['ManageDomain']['org_id'])) ? $org_detail['ManageDomain']['org_id'] : 1;

							$this->request->data['UserDetail']['org_id'] = $org_id;

							$this->request->data['User']['managedomain_id'] = $checkOrganisationDomain;

							$orgPasswordPolicy = $this->OrgPassPolicy->find('first', array('conditions' => array('OrgPassPolicy.org_id' => $org_id)));

							if (!isset($orgPasswordPolicy['OrgPassPolicy']['min_lenght']) && strlen($this->request->data['User']['password']) < 4) {
								$this->request->data['error']['password'] = "Password should be at least 4 character";
								$this->User->validationErrors['password'] = "Password should be at least 4 character";
								return false;

							}

							if (isset($orgPasswordPolicy['OrgPassPolicy']) && !empty($orgPasswordPolicy['OrgPassPolicy'])) {

								if (isset($orgPasswordPolicy['OrgPassPolicy']['min_lenght']) && strlen($this->request->data['User']['password']) < $orgPasswordPolicy['OrgPassPolicy']['min_lenght']) {

									//$this->Session->setFlash(__("Password should be at least " . $orgPasswordPolicy['OrgPassPolicy']['min_lenght'] . " char"), 'error');
									$this->request->data['error']['password'] = "Password should be at least " . $orgPasswordPolicy['OrgPassPolicy']['min_lenght'] . " character";
									$this->User->validationErrors['password'] = "Password should be at least " . $orgPasswordPolicy['OrgPassPolicy']['min_lenght'] . " character";
									return false;

								}
								if (isset($orgPasswordPolicy['OrgPassPolicy']['numeric_char']) && $orgPasswordPolicy['OrgPassPolicy']['numeric_char'] == 1) {

									if (!preg_match('/[0-9]/', $this->request->data['User']['password'])) {

										$this->request->data['error']['password'] = 'Password should have minimum one numeric character.';
										$this->User->validationErrors['password'] = 'Password should have minimum one numeric character.';
										return false;
									}

								}

								if (isset($orgPasswordPolicy['OrgPassPolicy']['alph_char']) && $orgPasswordPolicy['OrgPassPolicy']['alph_char'] == 1) {

									if (!preg_match('/[a-zA-Z]/', $this->request->data['User']['password'])) {

										$this->request->data['error']['password'] = 'Password should have minimum one alpha character.';
										$this->User->validationErrors['password'] = 'Password should have minimum one alpha character.';
										return false;

									}
								}

								if (isset($orgPasswordPolicy['OrgPassPolicy']['special_char']) && $orgPasswordPolicy['OrgPassPolicy']['special_char'] == 1) {

									if (!preg_match('/\W/', $this->request->data['User']['password'])) {

										$this->request->data['error']['password'] = 'Password should have minimum one special character.';
										$this->User->validationErrors['password'] = 'Password should have minimum one special character.';
										return false;

									}

								}

								if (isset($orgPasswordPolicy['OrgPassPolicy']['caps_char']) && $orgPasswordPolicy['OrgPassPolicy']['caps_char'] == 1) {

									if (!preg_match('/[A-Z]/', $this->request->data['User']['password'])) {

										$this->request->data['error']['password'] = 'Password should have minimum one capital character.';
										$this->User->validationErrors['password'] = 'Password should have minimum one capital character.';
										return false;

									}
								}

							}

						}

						/*}  else {
							echo "Orgnasation has been expired.";
							die;
						*/
					}
				}

				if ($this->User->validates()) {

					$this->request->data['UserDetail']['first_name'] = trim($this->request->data['UserDetail']['first_name']);

					$pans = AuthComponent::password($this->data['User']['password']);

					$this->request->data['UserPassword']['password'] = $pans;

					$this->request->data['UserDetail']['last_name'] = trim($this->request->data['UserDetail']['last_name']);

					if ($this->User->saveAssociated($this->request->data)) {
						if (isset($_SESSION['data'])) {
							unset($_SESSION['data']);
						}

						$this->Session->write('newRegistrationId', $this->User->getLastInsertID());

						$userId = $this->User->getLastInsertID();
						$this->request->data['UserPassword']['user_id'] = $userId;
						$this->UserPassword->save($this->request->data);

						/* $this->request->data['UserPassword']['user_id'] = $userId;
							$this->request->data['UserPassword']['password'] = $this->request->data['User']['password'];

						*/

						// $test = $this->__sendEmailConfirm($this->request->data, $this->User->getLastInsertID(), $activatiinHash);
						// $this->Session->setFlash(__('You have been sent an activation email'));
						//return $this->redirect(SITEURL);

						/* -----------------------18 nov 2015 uncomment below line for plan funtionality------------------ */
						//return $this->redirect( array( 'controller' => 'plans', 'action' => 'index', $this->User->getLastInsertID(), 'admin' => false ) );

						/* ----------------------------------------------------------------------------------------------------- */
						/* -------Free register remove below code before enable add feature code-----18 nov 2015---------- */

						if ($this->live_setting == true) {

							if (CHAT_VERSION == 'new') {
								$mongo = new MongoClient(MONGO_CONNECT);
								$this->mongoDB = $mongo->selectDB(MONGO_DATABASE);
								$mongo_collection = new MongoCollection($this->mongoDB, 'users');
							} else {
								$mongo = new MongoDB\Driver\Manager(MONGO_CONNECT);
								$bulk = new MongoDB\Driver\BulkWrite;
							}

						}

						/*$sql = "SELECT u.id,u.email,u.password,ud.first_name as firstname,ud.last_name as lastname, ud.profile_pic as thumb,ud.department_id, ud.job_title, ud.job_role, ud.bio,ud.org_name, ud.contact, timezones.name as timezone_name, timezones.timezone as timezone_offset, dept.name as dept_name FROM users as u LEFT JOIN user_details as ud ON u.id=ud.user_id  LEFT JOIN timezones ON u.id=timezones.user_id LEFT JOIN departments dept ON dept.id = ud.department_id WHERE u.id=" . $userId;

						$user_result = $this->User->query($sql);*/
						$this->loadModel('Timezone');
						//$loggedInTimzone = $this->Timezone->find('first', array('conditions'=>array('Timezone.user_id'=>63 )));

						if ($this->live_setting == true) {

							// INSERT USER DATA TO MONGO
							$this->Users->addUser($userId);

						}

						$userData = $this->User->find('first', array('fields' => array('User.email,User.activation_key, UserDetail.first_name,UserDetail.last_name'), 'conditions' => array('User.id' => $userId)));

						$activatiinHash = $userData['User']['activation_key'];
						$this->request->data['UserDetail']['first_name'] = $userData['UserDetail']['first_name'];
						$this->request->data['UserDetail']['last_name'] = $userData['UserDetail']['last_name'];
						$this->request->data['User']['email'] = $userData['User']['email'];
						$sendMail = $this->__sendEmailConfirm($this->request->data, $userId, $activatiinHash);

						return $this->redirect(array('controller' => 'plans', 'action' => 'thanks', $userId, 'sample', 'admin' => false));

						/* -------Free register remove above code before enable add feature code------------------------------ */
					} else {

						// print_r(array_keys($array, "blue"));
						//$datns = array_keys($this->questionArray, $this->request->data['UserDetail']['question']);
						//$this->request->data['UserDetail']['question'] = $datns['0'];
						// pr($this->request->data['UserDetail']['question']); die;
						$this->request->data = $this->request->data;
					}
				}
			}
			//  $this->Session->setFlash(__('The user could not be saved. Please, try again.'));
		}
	}

	public function __sendEmailConfirm($useData, $lastInsertID, $activatiinHash) {
		//$user = $this->User->find('first',array('conditions'=>array('User.id' => $user_id),'fields'=>array('User.id','User.email'),'recursive'=>0));

		if ( empty($useData) && !count($useData)) {
			debug(__METHOD__ . " failed to retrieve User data for user.id: {$user_id}");
			return false;
		}
		$activate_url = SITEURL . 'users/activate/' . $lastInsertID . '/' . $activatiinHash;
		$name = $useData['UserDetail']['first_name'] . ' ' . $useData['UserDetail']['last_name'];

		$email = new CakeEmail();
		$email->config('Smtp');
		// $email->from(array(ADMIN_EMAIL => SITENAME));
		$email->from(array(ADMIN_FROM_EMAIL => SITENAME));
		$email->to($useData['User']['email']);
		$email->subject(SITENAME . ': Please confirm your email address');
		$email->template('user_confirm');
		$email->emailFormat('html');
		$email->viewVars(array('activate_url' => $activate_url, 'name' => $name));
		return $email->send();
	}

	/**
	 * Activates a user account from an incoming link
	 *
	 *  @param Int $user_id User.id to activate
	 *  @param String $in_hash Incoming Activation Hash from the email
	 */
	public function activate($user_id = null, $in_hash = null) {
		$this->User->id = $user_id;
		$data = $this->User->findById($user_id);

		if ($this->User->exists() && !is_null($data['User']['activation_key']) && ($in_hash == $data['User']['activation_key'])) {

			// Update the active flag in the database
			$this->User->saveField('status', self::USER_ACTIVE);
			$this->User->saveField('activation_key', '');

			// Let the user know they can now log in!
			$this->Session->setFlash('Your account has been activated.', 'success');
			$this->redirect(SITEURL);
		} else if ($this->User->exists()) {
			$this->Session->setFlash('Your account has been activated.', 'success');
		} else {
			$this->Session->setFlash("Account doesn't exist.", 'error');
		}
		$this->redirect(SITEURL);
	}

	public function login() {


		$this->set('title_for_layout', __('Sign In - OpusView', true));
		if ($_SERVER['SERVER_NAME'] != 'www.ideascast.com') {
			$this->set('bodyclass', "bodyloginclass");

		} else {
			$this->set('bodyclass', '');
		}


		if ($this->Session->read('Auth.User.id') > 0 && $this->Session->read('Auth.User.role_id') == 2) {


					$userStartPageData = $this->User->find('first', ['conditions' => ['User.id' => $this->Auth->user('id')], 'recursive' => -1]);
					$page_setting_toggle = (isset($userStartPageData) && !empty($userStartPageData)) ? $userStartPageData['User']['page_setting_toggle'] : 0;
					$landing_url = (isset($userStartPageData) && !empty($userStartPageData)) ? $userStartPageData['User']['landing_url'] : null;



					if (isset($page_setting_toggle) && !empty($page_setting_toggle)) {
						if (isset($landing_url) && !empty($landing_url)) {
							$landing_url = explode('/', $landing_url);
							$landing_controller = $landing_url[0];
							if(isset($landing_url[1])){
							$landing_action = $landing_url[1];
							}
							if(isset($landing_url[2]) && !empty($landing_url[2])){
								$landing_action = $landing_url[1].'/'.$landing_url[2];
							}
							if(isset($landing_url[3]) && !empty($landing_url[3])){
								$landing_action = $landing_url[1].'/'.$landing_url[2].'/'.$landing_url[3];
							}
							if(isset($landing_url[4])){
								$landing_action = $landing_url[1].'/'.$landing_url[2].'/'.$landing_url[3].'/'.$landing_url[4];
							}

							$this->Auth->loginRedirect = array('controller' => $landing_controller, 'action' => $landing_action, 'admin' => false);

							return $this->redirect($this->Auth->redirectUrl());


						} else {


							return $this->redirect(array('controller' => 'projects', 'action' => 'lists', 'admin' => false));

						}
					}else{

					return $this->redirect(SITEURL . 'projects/lists');

					}
		} else if ($this->Session->read('Auth.User.id') > 0 && $this->Session->read('Auth.User.role_id') == 3) {
			//return $this->redirect(SITEURL . 'organisations/manage_users');
			return $this->redirect(SITEURL . 'organisations/dashboard');
		} else if ($this->Session->read('Auth.User.role_id') == 1) {
			return $this->redirect(SITEURL . 'templates/create_workspace/0/');
		}

		if ($this->request->is('post')) {

			$whatINeed = explode(WEBDOMAIN, $_SERVER['HTTP_HOST']);
			$whatINeed = $whatINeed[0];

			$userEmail = ( isset($this->request->data['User']['email']) && !empty($this->request->data['User']['email']) ) ? $this->request->data['User']['email'] : null ;

			$userlDetail13 = $this->User->find('first', array('conditions' => array('User.email' => $userEmail)));
			// Organisation can not login into main site, he can login only his domain

			if (isset($userlDetail13['OrganisationUser']) && !empty($userlDetail13['OrganisationUser']['domain_name'])) {

				$orgDomain = $this->OrgSetting->find('first', array('conditions' => array('OrgSetting.user_id' => $userlDetail13['User']['id'], 'OrgSetting.subdomain' => $whatINeed)));

				if (isset($orgDomain) && empty($orgDomain) && $userlDetail13['User']['role_id'] == 3) {
					$this->Session->setFlash(__('The Email and Password combination is incorrect.', 'error'));
					$this->set('loginerror', 'The Email and Password combination is incorrect.');
					$_SESSION['LoginDetails'] = $this->request->data;
					return $this->redirect(array('controller' => 'Users', 'action' => 'login', 'admin' => false));
				}

			}

			if (!empty($userlDetail13['OrganisationUser']['creator_id']) && !empty($userlDetail13['OrganisationUser']['domain_name'])) {

				$this->Session->setFlash(__('The Email and Password combination is incorrect.', 'error'));
				$this->set('loginerror', 'The Email and Password combination is incorrect.');
				$_SESSION['LoginDetails'] = $this->request->data;
				return $this->redirect(array('controller' => 'Users', 'action' => 'login', 'admin' => false));

			}

			if (isset($userlDetail13['UserDetail']['org_id']) && !empty($userlDetail13['UserDetail']['org_id'])) {

				$orgPasswordPolicy = $this->OrgPassPolicy->find('first', array('conditions' => array('OrgPassPolicy.org_id' => $userlDetail13['UserDetail']['org_id'])));

				$checkLoginAttempt = $this->PasswordLockout->find('first', array('conditions' => array('PasswordLockout.user_id' => $userlDetail13['User']['id'])));

				$currentTime = date('Y-m-d H:i:s');

				if (isset($checkLoginAttempt['PasswordLockout']['updated'])) {

					$existingtime = $checkLoginAttempt['PasswordLockout']['updated'];

					$interval = 0;
					if (isset($orgPasswordPolicy['OrgPassPolicy']['lockout_period'])) {

						$datetime1 = strtotime("+" . $orgPasswordPolicy['OrgPassPolicy']['lockout_period'] . " minutes", strtotime($existingtime));
						$datetime2 = strtotime($currentTime);

						$interval = $datetime1 - $datetime2;
						$minutes = $interval / 60;
					}

					if ((isset($orgPasswordPolicy) && !empty($orgPasswordPolicy['OrgPassPolicy']['lockout_period'])) && (isset($checkLoginAttempt) && !empty($checkLoginAttempt['PasswordLockout']))) {
						if ($checkLoginAttempt['PasswordLockout']['attempt_status'] == 0 && $checkLoginAttempt['PasswordLockout']['login_attempt'] == $orgPasswordPolicy['OrgPassPolicy']['temp_lockout'] && $interval > 0) {

							$this->Session->setFlash(__('Too many attempts, account suspended. Retry in ' . $orgPasswordPolicy['OrgPassPolicy']['lockout_period'] . ' min(s).', 'error'));

							$_SESSION['LoginDetails'] = $this->request->data;

							//pr($_SESSION); die;
							return $this->redirect(array('controller' => 'Users', 'action' => 'login', 'admin' => false));
						}
					}
				}
			}

			/* STOP MULTIPLE LOGIN WITH SAME USER.....
			$sess_token = $this->getToken(10);
			$UserDt = $this->User->find('first', ['conditions' => ['User.email' => $userEmail], 'recursive' => -1]);
			if(isset($UserDt) && !empty($UserDt)) {
				$this->User->id = $UserDt['User']['id'];
				$this->User->saveField('session_token', $sess_token);
				setcookie('session_token', $sess_token, (time() + 3600 * 24 * 7), '/');
			}*/

			if ($this->Auth->loggedIn()) {
				/* updated  by: GS
					 * date: 8/8/2017
					 * Remove blinker cookie if exists. it will automatically created by javascript code
					 * Remove reminder_popup cookie if exists. it will automatically created by javascript code
					 * NOTE: kripya is code me ungli na karen to behtar hoga... Dhanyawaad.. Note samapt
				*/
				if (isset($_COOKIE['blinker'])) {
					unset($_COOKIE['blinker']);
				}
				if (isset($_COOKIE['reminder_popup'])) {
					unset($_COOKIE['reminder_popup']);
				}
				/* End Remove blinker cookie */


				if ($this->live_setting == true) {
					$posted = $this->request->data;
					$is_mobile = $posted['is_mobile'];
					// CakeSession::write( 'stoken', $randomSessionStr );
					// if user login from mobile then mongodb update
					$this->Users->userLoginUpdate($is_mobile);

				}

				//return $this->redirect(array('controller' => 'dashboards', 'action' => 'index', 'admin' => false));
				// $this->Session->write('User.times', time());
				// $authData = $this->Session->read('Auth.User');
				// $authData['User']['times'] = time();
				//CakeSession::write( 'User.times', time() );
			}

			if (isset($this->data['User']['remember']) && !empty($this->data['User']['remember'])) {
				setcookie("username", $this->request->data['User']['email'], time() + 3600 * 24 * 7);
				setcookie("password", $this->request->data['User']['password'], time() + 3600 * 24 * 7);
			} else {
				setcookie("username", '');
				setcookie("password", '');
			}

			if ($this->Auth->login()) {

				// CREATE A SESSION FOR TOKEN TO UPDATE IN MONGO
				$randomSessionStr = generateRandomString(50);
				$this->Session->write('stoken', $randomSessionStr);
				$this->Users->UserToken($randomSessionStr, 1);

				// CREATE A SESSION FOR TOKEN TO UPDATE IN MONGO

				unset($_SESSION['LoginDetails']);

				//======== Start Organisation login with Password Policy ===========================

				$results = $this->User->find('first', array(
					'conditions' => array('User.email' => $this->Auth->user('email')),
					'fields' => array('User.status', 'User.modified'),
				));


				$this->User->id = $this->Auth->user('id');
				$this->User->saveField('is_login', 1);
				$this->Session->write('chat_loggedin', true);


				$this->Session->write('Auth.User.mm_time', time());

				if (empty($results['User']['status']) || ($results['User']['status'] == 0)) {
					// User has not confirmed account
					//$this->Session->setFlash('Your account has not been activated.');
					$this->Session->setFlash('Please activate your account before signing in.');
					// $this->Session->setFlash('Your account is not activated');
					return $this->redirect($this->Auth->logout());
					$this->redirect(array('action' => 'login'));
				}
				else {

					$this->User->updateAll(array('User.last_login' => "'".date('Y-m-d H:i:s')."'" ), array('User.id' => $this->Auth->user('id')));

					if (isset($results) && !empty($results)) {

						// =====================================================
						//			LOGIN ATTEMPT SUCCESSFULL
						// =====================================================

						$checkLoginAttempt = $this->PasswordLockout->find('first', array('conditions' => array('PasswordLockout.user_id' => $results['User']['id'])));

						if (isset($checkLoginAttempt) && !empty($checkLoginAttempt) && count($checkLoginAttempt) > 0) {

							$this->PasswordLockout->id = $checkLoginAttempt['PasswordLockout']['id'];
							$this->PasswordLockout->saveField('attempt_status', (int) 1);
							$this->PasswordLockout->saveField('login_attempt', (int) 0);
							$this->PasswordLockout->saveField('attempt_count', (int) $this->PasswordLockout->field('attempt_count') + 1);
						}

						// =====================================================

						$userdetail = $this->Common->userDetail($results['User']['id']);
						if (isset($userdetail['UserDetail']['org_id']) && $userdetail['UserDetail']['org_id'] > 0) {

							$orgPasswordPolicy = $this->OrgPassPolicy->find('first', array('conditions' => array('OrgPassPolicy.org_id' => $userdetail['UserDetail']['org_id'])));

							if (isset($orgPasswordPolicy['OrgPassPolicy']['change_pass_time']) && !empty($orgPasswordPolicy['OrgPassPolicy']['change_pass_time'])) {

								$userLastPassUpdated = $this->UserPassword->find('first', array('conditions' => array('UserPassword.user_id' => $this->Session->read('Auth.User.id')), 'order' => 'id DESC'));

								//pr($userLastPassUpdated); die;
								if (isset($userLastPassUpdated['UserPassword']['created'])) {

									$userCreatedDate = date('Y-m-d', strtotime($userLastPassUpdated['UserPassword']['created']));
									$todayDate = date('Y-m-d');
									//echo $userCreatedDate.'===='.$todayDate;
									$consumeDays = daysLeft($userCreatedDate, $todayDate);

									if ($consumeDays > $orgPasswordPolicy['OrgPassPolicy']['change_pass_time']) {
										$this->Session->setFlash('Your password has expired.');
										return $this->redirect(array('controller' => 'users', 'action' => 'changepassword', 'admin' => false));
									}

								}

							}

						}
					}

					$userRoleid = $this->Common->getRoles($this->Auth->user('id'));
					//Redirect for front admin user
					if (isset($userRoleid) && $userRoleid == 1) {
						return $this->redirect(array('controller' => 'templates', 'action' => 'create_workspace', 0, 'admin' => false));
					}

					if (isset($userRoleid) && $userRoleid == 3) {

						$domaintCount = $this->ManageDomain->find('count', array('conditions' => array('ManageDomain.user_id' => $this->Session->read('Auth.User.id'))));

						if (isset($domaintCount) && $domaintCount > 0) {
							//return $this->redirect(array('controller' => 'organisations', 'action' => 'manage_users', 'admin' => false));
							return $this->redirect(array('controller' => 'organisations', 'action' => 'dashboard', 'admin' => false));
						} else {
							return $this->redirect(array('controller' => 'organisations', 'action' => 'domain_settings', 'admin' => false));
						}
					}

					if ($this->Session->read('Auth.User.id') > 0 && $this->Session->read('Auth.User.role_id') == 3) {

						$domaintCount = $this->ManageDomain->find('count', array('conditions' => array('ManageDomain.user_id' => $this->Session->read('Auth.User.id'))));

						if (isset($domaintCount) && $domaintCount > 0) {
							return $this->redirect(SITEURL . 'organisations/manage_users');
						} else {
							return $this->redirect(SITEURL . 'organisations/manage_domain');
						}

					}

					if ($this->Session->read('Auth.User.role_id') == 1) {

						return $this->redirect(SITEURL . 'templates/create_workspace/0/');
					}

					// Get user landing url setting
					/* $landing_controller = 'dashboards';
					$landing_action = 'project_center';*/

					$userStartPageData = $this->User->find('first', ['conditions' => ['User.id' => $this->Auth->user('id')], 'recursive' => -1]);
					$page_setting_toggle = (isset($userStartPageData) && !empty($userStartPageData)) ? $userStartPageData['User']['page_setting_toggle'] : 0;
					$landing_url = (isset($userStartPageData) && !empty($userStartPageData)) ? $userStartPageData['User']['landing_url'] : null;

					$this->loadModel('ProjectStat');

					$data = $this->ProjectStat->updateAll(array('ProjectStat.status' => 0), array('ProjectStat.user_id' => $this->Auth->user('id')));


					/*=========== Skill Delete if folder is empty or upload status = 0 ====*/
					$skillFolderPath = SKILL_PDF_PATH . $this->Auth->user('id');
					if ($this->is_dir_empty($skillFolderPath)) {

						$this->SkillPdf->deleteAll(array('SkillPdf.user_id' => $this->Auth->user('id')),false);

					} else {

						$skillData = $this->SkillPdf->find('all',array('conditions'=>array('SkillPdf.user_id' => $this->Auth->user('id'), 'SkillPdf.upload_status'=>0)));
						if( isset($skillData) && !empty($skillData) ){
							foreach($skillData as $listSkillData){
								unlink($skillFolderPath.'/'.$listSkillData['SkillPdf']['pdf_name']);
							}
							$this->SkillPdf->deleteAll(array('SkillPdf.user_id' => $this->Auth->user('id'), 'SkillPdf.upload_status'=> 0 ),false);
						}

					}
					/*==================================================================*/

					if (isset($page_setting_toggle) && !empty($page_setting_toggle)) {
						if (isset($landing_url) && !empty($landing_url)) {
							$landing_url = explode('/', $landing_url);
							$landing_controller = $landing_url[0];
							if(isset($landing_url[1]) ){
							$landing_action = $landing_url[1];
							}
							if(isset($landing_url[2]) ){
								$landing_action = $landing_url[1].'/'.$landing_url[2];
							}
							if(isset($landing_url[3])){
								$landing_action = $landing_url[1].'/'.$landing_url[2].'/'.$landing_url[3];
							}
							if(isset($landing_url[4])){
								$landing_action = $landing_url[1].'/'.$landing_url[2].'/'.$landing_url[3].'/'.$landing_url[4];
							}


							//return $this->redirect(array('controller' => $landing_controller, 'action' => $landing_action, 'admin' => false));

							//$this->Auth->loginRedirect = array('controller' => $landing_controller, 'action' => $landing_action, 'admin' => false);

							/*----------------uncomment above url to on landing page functionality-------------------*/

							$auther = two_factor_check();
							if(isset($auther ) && !empty($auther )){

								$this->Auth->loginRedirect = array('controller' => 'subdomains', 'action' => 'auth', 'admin' => false);
							}else{
								$this->Auth->loginRedirect = array('controller' => $landing_controller, 'action' => $landing_action, 'admin' => false);
							}
							return $this->redirect($this->Auth->redirectUrl());


						} else {


							return $this->redirect(array('controller' => 'projects', 'action' => 'lists', 'admin' => false));

						}
					} else {

						$auther = two_factor_check();
						if(isset($auther ) && !empty($auther )){

							$this->Auth->loginRedirect = array('controller' => 'subdomains', 'action' => 'auth', 'admin' => false);
						}
						return $this->redirect($this->Auth->redirectUrl());

					}

					//}



				}

				//pr($this->Auth->redirectUrl()); dir;

				return $this->redirect($this->Auth->redirectUrl());
			}
			$_SESSION['LoginDetails'] = array();

			$checkOrganisationDomain = 0;
			if( isset($this->request->data['User']['email']) && !empty($this->request->data['User']['email']) ){
				$userEmail = $this->request->data['User']['email'];
				$userDomainName = explode("@", $userEmail);
				$userDomain = explode(".", $userDomainName[1]);
				$checkOrganisationDomain = $this->checkOrgDomainLogin($userDomainName[1]);
			}

			if ($checkOrganisationDomain > 0) {

				$orgPasswordPolicy = $this->OrgPassPolicy->find('first', array('conditions' => array('OrgPassPolicy.org_id' => $checkOrganisationDomain)));

				$userlDetail = $this->User->find('first', array('conditions' => array('User.email' => $userEmail)));

				if (isset($userlDetail) && !empty($userlDetail)) {

					$checkLoginAttempt = $this->PasswordLockout->find('first', array('conditions' => array('PasswordLockout.user_id' => $userlDetail['User']['id'])));

				} else {
					$checkLoginAttempt = array();
				}

				if (isset($checkLoginAttempt) && count($checkLoginAttempt) > 0 && (isset($orgPasswordPolicy['OrgPassPolicy']['temp_lockout']) && !empty($orgPasswordPolicy['OrgPassPolicy']['temp_lockout']))) {

					if (isset($orgPasswordPolicy['OrgPassPolicy']['temp_lockout']) && $orgPasswordPolicy['OrgPassPolicy']['temp_lockout'] > $checkLoginAttempt['PasswordLockout']['login_attempt']) {

						$this->PasswordLockout->id = $checkLoginAttempt['PasswordLockout']['id'];
						$this->PasswordLockout->saveField('attempt_status', (int) 0);
						$this->PasswordLockout->saveField('login_attempt', (int) $this->PasswordLockout->field('login_attempt') + 1);
						$this->PasswordLockout->saveField('attempt_count', (int) $this->PasswordLockout->field('attempt_count') + 1);

						if ($orgPasswordPolicy['OrgPassPolicy']['temp_lockout'] == $checkLoginAttempt['PasswordLockout']['login_attempt'] + 1) {
							$this->Session->setFlash(__('Too many attempts, account suspended. Retry in ' . $orgPasswordPolicy['OrgPassPolicy']['lockout_period'] . ' min(s).', 'error'));
							$_SESSION['LoginDetails'] = $this->request->data;

							//pr($_SESSION); die;
							return $this->redirect(array('controller' => 'Users', 'action' => 'login', 'admin' => false));
						}

					} else if (isset($orgPasswordPolicy['OrgPassPolicy']['temp_lockout']) && $orgPasswordPolicy['OrgPassPolicy']['temp_lockout'] == $checkLoginAttempt['PasswordLockout']['login_attempt']) {

						$this->PasswordLockout->id = $checkLoginAttempt['PasswordLockout']['id'];

						$this->PasswordLockout->saveField('attempt_count', (int) 0);

						$this->PasswordLockout->saveField('login_attempt', (int) 1);

						$checkLoginAttempt = $this->PasswordLockout->find('first', array('conditions' => array('PasswordLockout.user_id' => $userlDetail['User']['id'])));

						if ($orgPasswordPolicy['OrgPassPolicy']['temp_lockout'] == $checkLoginAttempt['PasswordLockout']['login_attempt']) {
							$this->Session->setFlash(__('Too many attempts, account suspended. Retry in ' . $orgPasswordPolicy['OrgPassPolicy']['lockout_period'] . ' min(s).', 'error'));
							$_SESSION['LoginDetails'] = $this->request->data;

							//pr($_SESSION); die;
							return $this->redirect(array('controller' => 'Users', 'action' => 'login', 'admin' => false));
						} else {
							$this->Session->setFlash(__('The Email and Password combination is incorrect.', 'error'));
							$this->set('loginerror', 'The Email and Password combination is incorrect.');

						}

						return $this->redirect(array('controller' => 'Users', 'action' => 'login', 'admin' => false));

					} else {

						$this->Session->setFlash(__('Your login attempt has completed, so please login after some time.', 'error'));
						$_SESSION['LoginDetails'] = $this->request->data;
						return $this->redirect(array('controller' => 'Users', 'action' => 'login', 'admin' => false));

					}

				} else {

					if (isset($userlDetail) && !empty($userlDetail)) {

						$this->request->data['PasswordLockout']['id'] = '';
						$this->request->data['PasswordLockout']['user_id'] = $userlDetail['User']['id'];
						$this->request->data['PasswordLockout']['login_attempt'] = 1;
						$this->request->data['PasswordLockout']['attempt_status'] = 0;
						$this->request->data['PasswordLockout']['attempt_count'] = 1;

						$this->PasswordLockout->save($this->request->data);

						if (isset($orgPasswordPolicy['OrgPassPolicy']['temp_lockout']) && $orgPasswordPolicy['OrgPassPolicy']['temp_lockout'] == $this->request->data['PasswordLockout']['login_attempt']) {
							$this->Session->setFlash(__('Too many attempts, account suspended. Retry in ' . $orgPasswordPolicy['OrgPassPolicy']['lockout_period'] . ' min(s).', 'error'));
							$_SESSION['LoginDetails'] = $this->request->data;

							//pr($_SESSION); die;
							return $this->redirect(array('controller' => 'Users', 'action' => 'login', 'admin' => false));
						}
					}

				}

			}



			$_SESSION['LoginDetails'] = $this->request->data;
			$this->set('loginerror', 'The Email and Password combination is incorrect.');
			$this->Session->setFlash(__('The Email and Password combination is incorrect.', 'error'));
			return $this->request->data;
		}

		//$this->Session->setFlash(__('Unauthorised access!', 'error'));
		//return $this->redirect(SITEURL);
	}

	public function logout($test = null) {

		$this->User->id = $this->Auth->user('id');
		$this->User->saveField('is_login', 0);
		$this->User->saveField('today_reminder', 1);
		$id = $this->Session->read('Auth.User.id');
		// UPDATE USER DATA TO MONGO
		$this->Users->updateUserStatus($id);
		if ($this->live_setting == true) {

			if ($_SERVER['SERVER_NAME'] != SERVER_NAME) {
				$HttpSocket = new HttpSocket([
					'ssl_verify_host' => false,
					'ssl_verify_peer_name' => false,
					'ssl_verify_peer' => false,
				]);


				if( CHAT_CLOUD != 'yes' ){
					$id = $this->Session->read('Auth.User.id');
					// UPDATE USER DATA TO MONGO
					$this->Users->updateUserStatus($id);
				}
			}
		}
		$this->Users->UserToken("");
		$this->Session->delete('stoken');

		$past = time() - 3600;
		if (isset($_COOKIE) && !empty($_COOKIE)) {
			foreach ($_COOKIE as $key => $value) {
				if(!is_array($value)){
					setcookie($key, $value, $past, '/');
				}
			}
		}
		/* updated  by: GS
		 * date: 1/8/2017
		 * Remove blinker cookie if exists. it will automatically created by javascript code
		 * Remove reminder_popup cookie if exists. it will show reminder popup
		 * NOTE: kripya is code me ungli na karen to behtar hoga... Dhanyawaad.. Note samapt
		 */
		if (isset($_COOKIE['blinker'])) {
			unset($_COOKIE['blinker']);
		}
		if (isset($_COOKIE['reminder_popup'])) {
			unset($_COOKIE['reminder_popup']);
		}
		/* End Remove blinker cookie */

		//$this->Session->setFlash(__('Logout Successful.'), 'success');


		return $this->redirect($this->Auth->logout());

	}

	public function chat_logout($user_id = null) {

			$this->autoLayout = false;
			$this->render(false);

			//$user_id = $this->request->data['user_id'];


			if(isset($user_id) && !empty($user_id)) {
				$this->Session->delete('Auth.User');
			    $this->Session->delete('User');
			    $this->Session->destroy();
			    $this->Cookie->destroy();
			// if ($this->request->is('post') || $this->request->is('put')) {
			// 	$post = $this->request->data;

			// 	$user_id = $post['user_id'];

				/*$this->User->id = $user_id;
				$this->User->saveField('is_login', 0);

				if ($this->live_setting == true) {

					if ($_SERVER['SERVER_NAME'] != SERVER_NAME) {
						$HttpSocket = new HttpSocket([
							'ssl_verify_host' => false,
							'ssl_verify_peer_name' => false,
							'ssl_verify_peer' => false,
						]);


						if( CHAT_CLOUD != 'yes' ){
							$id = $user_id;
							$this->Users->updateUserStatus($id);
						}
					}
				}

				$past = time() - 3600;
				if (isset($_COOKIE) && !empty($_COOKIE)) {
					foreach ($_COOKIE as $key => $value) {
						if(!is_array($value)){
							setcookie($key, $value, $past, '/');
						}
					}
				}
				if (isset($_COOKIE['blinker'])) {
					unset($_COOKIE['blinker']);
				}
				if (isset($_COOKIE['reminder_popup'])) {
					unset($_COOKIE['reminder_popup']);
				} */

				$this->Auth->logout();
			}
			$response = ['success' => true];
			$this->response->body(json_encode($response));
			$this->response->statusCode(200);
			$this->response->type('application/json');

			return $this->response;

			die;
	}

	public function myaccount() {
		$this->User->id = $this->Session->read('Auth.User.id');

		// pr($this->Session->read('Auth')); die;
		//die;
		if (!$this->User->exists()) {
			$this->Session->setFlash(__('Invalid User Profile.'), 'error');
			die('error');
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->User->saveAssociated($this->request->data)) {
				$this->Session->write('Auth', $this->User->read(null, $id));
				$this->Session->setFlash(__('The Admin Profile has been updated successfully.'), 'success');
				die('success');
			}
		} else {

			//$this->UserPlan->recursive = 2;

			//$this->UserPlan->bindModel(array('belongsTo' => array('Plan', 'PlanType' => array('className' => 'PlanType', 'foreignKey' => 'plan_type'))));

			//$userplan = $this->UserPlan->find('all', array('conditions' => array('UserPlan.user_id' => $this->User->id, 'UserPlan.plan_id !=' => 0, 'UserPlan.is_active' => 1)));

			//$this->set('userplan', $userplan);
			$this->request->data = $this->User->read(null, $this->User->id);

			if (isset($this->request->data['User']['password'])) {
				unset($this->request->data['User']['password']);
			}
		}
	}

	function forgetpwd() {
		//$this->layout="signup";
		//$this->User->recursive=-1;
		$this->set('questionArrayAns', $this->questionArrayAns);
		$this->set('err', 0);
		$this->set('title_for_layout', __('Change Password - OpusView', true));
		if (!empty($this->data)) {
			if (empty($this->data['User']['email'])) {
				$this->Session->setFlash(__('Please Provide Your Email Adress that You used to Register with Us'),'error');
				$this->set('err', 'Please Provide Your Email Adress that You used to Register with Us');
				return $this->redirect(array('action' => 'forgetpwd'));
			} else {
				$email = $this->data['User']['email'];

				//$fu = $this->User->find('first', array('conditions' => array('User.email' => $email, 'User.role_id' => 2)));
				$fu = $this->User->find('first', array('conditions' => array('User.email' => $email, 'User.role_id !=' => 1)));
				if ((isset($fu) && !empty($fu)) && (empty($this->data['UserDetail']['question']))) {
					//debug($fu);
					if (!empty($fu['User']['status'])) {
						$key = $this->User->getActivationHash(); //Security::hash(String::uuid(),'sha512',true);
						$hash = sha1($fu['User']['email'] . rand(0, 100));
						$url = Router::url(array('controller' => 'users', 'action' => 'reset'), true) . '/' . $key . '#' . $hash;
						$ms = $url;
						$ms = wordwrap($ms, 1000);
						//debug($url);
						$fu['User']['tokenhash'] = $key;
						$this->User->id = $fu['User']['id'];
						if ($this->User->saveField('tokenhash', $fu['User']['tokenhash'])) {

							$email = new CakeEmail();
							$email->config('Smtp');
							// $email->from(array(ADMIN_EMAIL => SITENAME));
							$email->from(array(ADMIN_FROM_EMAIL => SITENAME));
							$email->to($fu['User']['email']);
							$email->subject(SITENAME . ': Change password');
							$email->template('resetpw');
							$email->emailFormat('html');
							$email->viewVars(array('ms' => $ms, 'name' => $fu['UserDetail']['full_name']));
							$email->send();

							$this->Session->setFlash(__('Your request has been completed.'), 'success');
							$this->set('err', 'Your request has been completed.');
						//	return $this->redirect(array('action' => 'forgetpwd'));
							//============EndEmail=============//
						} else {
							$this->Session->setFlash(__('Your request has been completed.'), 'success');
							$this->set('err', 'Error Generating Reset link.');
							// return $this->redirect(array('action' => 'forgetpwd'));
						}
					} else {
						$this->Session->setFlash(__('This Account is not Active yet.Check Your mail to activate it'),'error');
						$this->set('err', 'This Account is not Active yet.Check Your mail to activate it.');
						// return $this->redirect(array('action' => 'forgetpwd'));
					}
				} else if ((isset($fu) && !empty($fu)) && (!empty($this->data['UserDetail']['question']) && !empty($this->data['UserDetail']['question']))) {

					$det = $this->UserDetail->find('first', array('conditions' => array('UserDetail.user_id' => $fu['User']['id'], 'UserDetail.question' => $this->data['UserDetail']['question'], 'UserDetail.answer' => $this->data['UserDetail']['answer'])));

					// pr($det); die;

					if (isset($det) && !empty($det)) {

						$key = $this->User->getActivationHash(); //Security::hash(String::uuid(),'sha512',true);
						$hash = sha1($fu['User']['email'] . rand(0, 100));
						$url = Router::url(array('controller' => 'users', 'action' => 'reset'), true) . '/' . $key . '#' . $hash;
						$fu['User']['tokenhash'] = $key;
						$this->User->id = $fu['User']['id'];
						if ($this->User->saveField('tokenhash', $fu['User']['tokenhash'])) {
							return $this->redirect($url);
						}
					} else {

						$this->Session->setFlash(__('Your request has been completed.'), 'success');
						$this->set('err', 'Incorrect details,please try again.');
						// return $this->redirect(array('action' => 'forgetpwd'));
					}
				} else {
					$this->Session->setFlash(__('Your request has been completed.'), 'success');
					$this->set('err', 'Incorrect details,please try again.');
					// return $this->redirect(array('action' => 'forgetpwd'));
				}
			}
		}
	}

	function reset($token = null) {
		//pr($token); die;
		//$this->layout="Login";
		$this->set('token', $token);
		$this->User->recursive = -1;
		$this->set('title_for_layout', __('Change Password - OpusView', true));
		if (!empty($token)) {

			$u = $this->User->findBytokenhash($token);

			$org_id = 1;
			if (isset($u['User']['id']) && !empty($u['User']['id'])) {
				// for ORGANISATION ID
				$uD = $this->UserDetail->findById(array('UserDetail.user_id' => $u['User']['id']));
				$org_id = (isset($uD['UserDetail']['org_id']) && !empty($uD['UserDetail']['org_id'])) ? $uD['UserDetail']['org_id'] : 1;
			}
			if (isset($u) && !empty($u)) {
				$this->User->id = $u['User']['id'];

				if (!empty($this->data)) {
					$this->User->data = $this->data;
					$this->User->data['User']['email'] = $u['User']['email'];
					$this->User->data['User']['id'] = $u['User']['id'];
					$new_hash = sha1($u['User']['email'] . rand(0, 100)); //created token
					$this->User->data['User']['tokenhash'] = $new_hash;

					//============================================================================

					$orgPasswordPolicy = $this->OrgPassPolicy->find('first', array('conditions' => array('OrgPassPolicy.org_id' => $org_id)));

					if (isset($orgPasswordPolicy['OrgPassPolicy']['pass_repeat'])) {
						$previousPassword = $this->checkUserPrePassword($this->User->data['User']['id'], $orgPasswordPolicy['OrgPassPolicy']['pass_repeat']);
					}
					$newPassword = AuthComponent::password(trim($this->request->data['User']['password']));

					if (!isset($orgPasswordPolicy['OrgPassPolicy']['min_lenght']) && strlen($this->request->data['User']['password']) < 4) {

						$this->User->validationErrors['password'] = "Password should be at least 4 character";

						$this->set('err', "Password should be at least 4 character");


					}

					if (isset($orgPasswordPolicy['OrgPassPolicy']['min_lenght']) && strlen($this->request->data['User']['password']) < $orgPasswordPolicy['OrgPassPolicy']['min_lenght']) {

						if (isset($_SESSION['data']) && !empty($_SESSION['data'])) {

							unset($_SESSION['data']);

						}

						$this->request->data['error']['password'] = "Password should be at least " . $orgPasswordPolicy['OrgPassPolicy']['min_lenght'] . " character";

						$this->User->validationErrors['password'] = "Password should be at least " . $orgPasswordPolicy['OrgPassPolicy']['min_lenght'] . " character";

						$this->set('err', "Password should be at least " . $orgPasswordPolicy['OrgPassPolicy']['min_lenght'] . " character");

					}

					if (isset($orgPasswordPolicy['OrgPassPolicy']['numeric_char']) && $orgPasswordPolicy['OrgPassPolicy']['numeric_char'] == 1) {

						if (isset($_SESSION['data']) && !empty($_SESSION['data'])) {
							unset($_SESSION['data']);
						}

						if (!preg_match('/[0-9]/', $this->request->data['User']['password'])) {

							$this->request->data['error']['password'] = "Password should have minimum one numeric character";
							$this->User->validationErrors['password'] = "Password should have minimum one numeric character";
							$this->set('err', 'Password should have minimum one numeric character.');

						}

					}

					if (isset($orgPasswordPolicy['OrgPassPolicy']['alph_char']) && $orgPasswordPolicy['OrgPassPolicy']['alph_char'] == 1) {

						if (isset($_SESSION['data']) && !empty($_SESSION['data'])) {

							unset($_SESSION['data']);
						}

						if (!preg_match('/[a-zA-Z]/', $this->request->data['User']['password'])) {

							$this->request->data['error']['password'] = "Password should have minimum one alpha character";
							$this->User->validationErrors['password'] = "Password should have minimum one alpha character";
							$this->set('err', 'Password should have minimum one alpha character.');

						}

					}

					if (isset($orgPasswordPolicy['OrgPassPolicy']['special_char']) && $orgPasswordPolicy['OrgPassPolicy']['special_char'] == 1) {

						if (!preg_match('/\W/', $this->request->data['User']['password'])) {

							$this->User->validationErrors['password'] = 'Password should have minimum one special character.';
							$this->set('err', 'Password should have minimum one special character.');

						}
					}

					if (isset($orgPasswordPolicy['OrgPassPolicy']['caps_char']) && $orgPasswordPolicy['OrgPassPolicy']['caps_char'] == 1) {

						if (!preg_match('/[A-Z]/', $this->request->data['User']['password'])) {

							$this->User->validationErrors['password'] = 'Password should have minimum one capital character.';
							$this->set('err', 'Password should have minimum one capital character.');

						}
					}

					if (isset($orgPasswordPolicy['OrgPassPolicy']['pass_repeat'])) {
						if (in_array($newPassword, $previousPassword)) {

							//$this->request->data['error']['password'] = "Password should not be from your last " . $orgPasswordPolicy['OrgPassPolicy']['pass_repeat'] . " passwords";
							//$this->User->validationErrors['password'] = "Password should not be from your last " . $orgPasswordPolicy['OrgPassPolicy']['pass_repeat'] . " passwords";

							$this->request->data['error']['password'] = "Enter a password you have not used recently";
							$this->User->validationErrors['password'] = "Enter a password you have not used recently";




							//$this->set('err', "Password should not be from your last " . $orgPasswordPolicy['OrgPassPolicy']['pass_repeat'] . " passwords");
							$this->set('err', "New Password cannot be this previous password");

							//$this->Session->setFlash("Password should not be from your last " . $orgPasswordPolicy['OrgPassPolicy']['pass_repeat'] . " passwords");

						}
					}
					//============================================================================

					if ($this->User->validates(array('fieldList' => array('password', 'cpassword')))) {

						$this->request->data['UserPassword']['user_id'] = $this->User->data['User']['id'];

						if ($this->User->save($this->User->data, false)) {

						$pans = AuthComponent::password($this->data['User']['password']);
						$this->request->data['UserPassword']['password'] = $pans;


							$this->UserPassword->save($this->request->data);

							$this->Session->setFlash('Your password has been changed.', 'success');
							//$this->redirect('/');
							$this->redirect(array('controller' => 'users', 'action' => 'login'));
						}
					} else {
						$this->set('errors', $this->User->invalidFields());
					}
				}
			} else {
				$this->Session->setFlash('Your change password request has expired.');
				$this->set('err', 'Your change password request has expired.');
			}
		} else {
			$this->redirect('/');
		}
	}

	/*     * ********************* Front End Panel Common Functions End ************************* */

	/*     * ********************* Admin Panel Functions Start ************************* */

	/**
	 * Displays a view
	 *
	 * @param mixed What page to display
	 * @return void
	 */
	public function admin_index() {

		$orConditions = array();
		// $andConditions = array('NOT' => array('User.role_id' => array(1)));
		$andConditions = array('User.role_id' => array(2));
		$finalConditions = array();

		$in = 0;
		$per_page_show = $this->Session->read('user.per_page_show');
		if (empty($per_page_show)) {
			$per_page_show = ADMIN_PAGING;
		}

		if (isset($this->data['User']['keyword'])) {
			$keyword = trim($this->data['User']['keyword']);
		} else {
			$keyword = $this->Session->read('user.keyword');
		}

		if (isset($this->data['UserDetail']['country_id']) && !empty($this->data['UserDetail']['country_id'])) {
			$county = trim($this->data['UserDetail']['country_id']);
			$this->Session->write('user.country', $county);
			$in = 1;
			$andConditions1 = array(
				'UserDetail.country_id LIKE' => '%' . $county . '%',
			);

			$andConditions = array_merge($andConditions, $andConditions1);
		}
		//pr($keyword); die;

		if (isset($keyword)) {
			$this->Session->write('user.keyword', $keyword);
			$keywords = explode(" ", $keyword);
			if (isset($keywords[0]) && !empty($keywords[0]) && count($keywords) < 2) {
				$keyword = $keywords[0];
				$in = 1;
				$orConditions = array('OR' => array(
					'User.email LIKE' => '%' . $keyword . '%',
					'UserDetail.first_name LIKE' => '%' . $keyword . '%',
					'UserDetail.last_name LIKE' => '%' . $keyword . '%',
					//'UserDetail.country_id LIKE' => '%' . $county . '%'
				));
			} else if (!empty($keywords) && count($keywords) > 1) {
				$first_name = $keywords[0];
				$last_name = $keywords[1];
				$in = 1;
				$andConditions = array('AND' => array(
					'UserDetail.first_name LIKE' => '%' . $first_name . '%',
					'UserDetail.last_name LIKE' => '%' . $last_name . '%',
					//'UserDetail.country_id LIKE' => '%' . $county . '%'
				));
			}
		}

		if (isset($this->data['User']['status'])) {
			$status = $this->data['User']['status'];
		} else {
			$status = $this->Session->read('user.status');
		}

		if (isset($status)) {
			$this->Session->write('user.status', $status);
			if ($status != '') {
				$in = 1;
				$andConditions = array_merge($andConditions, array('User.status' => $status));
			}
		}

		if (isset($this->data['User']['per_page_show']) && !empty($this->data['User']['per_page_show'])) {
			$per_page_show = $this->data['User']['per_page_show'];
		}

		if (!empty($orConditions)) {
			$finalConditions = array_merge($finalConditions, $orConditions);
		}

		if (!empty($andConditions)) {
			$finalConditions = array_merge($finalConditions, array('AND' => $andConditions));
		}

		//pr($finalConditions); die;

		//pr($this->paginate('User'));die;
		$allusers = $this->User->find('all', array('conditions' => $finalConditions));
		$count = $this->User->find('count', array('conditions' => $finalConditions));

		$this->set('count', $count);
		$this->set('title_for_layout', __('All Internal IdeasCast Users', true));
		$this->Session->write('user.per_page_show', $per_page_show);
		$this->User->recursive = 0;
		$this->paginate = array('conditions' => $finalConditions, "limit" => $per_page_show, "order" => "User.created DESC");
		$this->set('users', $this->paginate('User'));
		$this->set('in', $in);
	}

	public function admin_thirdparty_users() {

		$orConditions = array();
		$andConditions = array('User.role_id' => array(3));
		$finalConditions = array();

		$in = 0;
		$per_page_show = $this->Session->read('user.per_page_show');
		if (empty($per_page_show)) {
			$per_page_show = ADMIN_PAGING;
		}

		if (isset($this->data['User']['keyword'])) {
			$keyword = trim($this->data['User']['keyword']);
		} else {
			$keyword = $this->Session->read('user.keyword');
		}

		if (isset($keyword)) {
			$this->Session->write('user.keyword', $keyword);
			$keywords = explode(" ", $keyword);
			if (isset($keywords[0]) && !empty($keywords[0]) && count($keywords) < 2) {
				$keyword = $keywords[0];
				$in = 1;
				$orConditions = array('OR' => array(
					'User.email LIKE' => '%' . $keyword . '%',
					'UserDetail.first_name LIKE' => '%' . $keyword . '%',
					'UserDetail.last_name LIKE' => '%' . $keyword . '%',
				));
			} else if (!empty($keywords) && count($keywords) > 1) {
				$first_name = $keywords[0];
				$last_name = $keywords[1];
				$in = 1;
				$andConditions = array('AND' => array(
					'UserDetail.first_name LIKE' => '%' . $first_name . '%',
					'UserDetail.last_name LIKE' => '%' . $last_name . '%',
				));
			}
		}

		if (isset($this->data['User']['status'])) {
			$status = $this->data['User']['status'];
		} else {
			$status = $this->Session->read('user.status');
		}

		if (isset($status)) {
			$this->Session->write('user.status', $status);
			if ($status != '') {
				$in = 1;
				$andConditions = array_merge($andConditions, array('User.status' => $status));
			}
		}

		if (isset($this->data['UserDetail']['country_id']) && !empty($this->data['UserDetail']['country_id'])) {
			$county = trim($this->data['UserDetail']['country_id']);
			$this->Session->write('user.country', $county);
			$in = 1;
			$andConditions1 = array(
				'UserDetail.country_id LIKE' => '%' . $county . '%',
			);
			$andConditions = array_merge($andConditions, $andConditions1);
		}

		if (isset($this->data['User']['per_page_show']) && !empty($this->data['User']['per_page_show'])) {
			$per_page_show = $this->data['User']['per_page_show'];
		}

		if (!empty($orConditions)) {
			$finalConditions = array_merge($finalConditions, $orConditions);
		}
		if (!empty($andConditions)) {
			$finalConditions = array_merge($finalConditions, array('AND' => $andConditions));
		}

		//pr($finalConditions); die;
		$count = $this->User->find('count', array('conditions' => $finalConditions));
		$this->set('count', $count);

		$this->set('title_for_layout', __('All Users', true));
		$this->Session->write('user.per_page_show', $per_page_show);
		$this->User->recursive = 0;
		$this->paginate = array('conditions' => $finalConditions, "limit" => $per_page_show, "order" => "User.created DESC");
		$this->set('users', $this->paginate('User'));
		$this->set('in', $in);
	}

	public function admin_institution_users() {
		$orConditions = array();
		$andConditions = array('User.role_id' => array(4));
		$finalConditions = array();

		$in = 0;
		$per_page_show = $this->Session->read('user.per_page_show');
		if (empty($per_page_show)) {
			$per_page_show = ADMIN_PAGING;
		}

		if (isset($this->data['User']['keyword'])) {
			$keyword = trim($this->data['User']['keyword']);
		} else {
			$keyword = $this->Session->read('user.keyword');
		}

		if (isset($keyword)) {
			$this->Session->write('user.keyword', $keyword);
			$keywords = explode(" ", $keyword);
			if (isset($keywords[0]) && !empty($keywords[0]) && count($keywords) < 2) {
				$keyword = $keywords[0];
				$in = 1;
				$orConditions = array('OR' => array(
					'User.email LIKE' => '%' . $keyword . '%',
					'UserDetail.first_name LIKE' => '%' . $keyword . '%',
					'UserDetail.last_name LIKE' => '%' . $keyword . '%',
				));
			} else if (!empty($keywords) && count($keywords) > 1) {
				$first_name = $keywords[0];
				$last_name = $keywords[1];
				$in = 1;
				$andConditions = array('AND' => array(
					'UserDetail.first_name LIKE' => '%' . $first_name . '%',
					'UserDetail.last_name LIKE' => '%' . $last_name . '%',
				));
			}
		}

		if (isset($this->data['User']['status'])) {
			$status = $this->data['User']['status'];
		} else {
			$status = $this->Session->read('user.status');
		}

		if (isset($status)) {
			$this->Session->write('user.status', $status);
			if ($status != '') {
				$in = 1;
				$andConditions = array_merge($andConditions, array('User.status' => $status));
			}
		}

		if (isset($this->data['UserDetail']['country_id']) && !empty($this->data['UserDetail']['country_id'])) {
			$county = trim($this->data['UserDetail']['country_id']);
			$this->Session->write('user.country', $county);
			$in = 1;
			$andConditions1 = array(
				'UserDetail.country_id LIKE' => '%' . $county . '%',
			);

			$andConditions = array_merge($andConditions, $andConditions1);
		}

		if (isset($this->data['User']['per_page_show']) && !empty($this->data['User']['per_page_show'])) {
			$per_page_show = $this->data['User']['per_page_show'];
		}

		if (!empty($orConditions)) {
			$finalConditions = array_merge($finalConditions, $orConditions);
		}
		if (!empty($andConditions)) {
			$finalConditions = array_merge($finalConditions, array('AND' => $andConditions));
		}

		//pr($finalConditions); die;
		$count = $this->User->find('count', array('conditions' => $finalConditions));
		$this->set('count', $count);

		$this->set('title_for_layout', __('All Institutions', true));
		$this->Session->write('user.per_page_show', $per_page_show);
		$this->User->recursive = 0;
		$this->paginate = array('conditions' => $finalConditions, "limit" => $per_page_show, "order" => "User.created DESC");
		$this->set('users', $this->paginate('User'));
		$this->set('in', $in);
	}

	public function admin_institution($id = null) {

		$this->User->id = $id;
		$data = $this->User->findById($id);

		if (empty($id)) {
			$this->Session->setFlash(__('Invalid Access.'), 'error');

			$this->redirect(array('action' => 'institution_users'));
		}

		if (!$this->User->exists()) {
			$this->Session->setFlash(__('Invalid User.'), 'error');
			$this->redirect(array('action' => 'institution_users'));
		}

		$orConditions = array();
		$andConditions = array();
		$finalConditions = array();

		$in = 0;
		$per_page_show = $this->Session->read('user.per_page_show');
		if (empty($per_page_show)) {
			$per_page_show = ADMIN_PAGING;
		}

		if (isset($this->data['User']['keyword'])) {
			$keyword = trim($this->data['User']['keyword']);
		} else {
			$keyword = $this->Session->read('user.keyword');
		}

		if (isset($keyword)) {
			$this->Session->write('user.keyword', $keyword);
			$keywords = explode(" ", $keyword);
			if (isset($keywords[0]) && !empty($keywords[0]) && count($keywords) < 2) {
				$keyword = $keywords[0];
				$in = 1;
				$orConditions = array('OR' => array(
					'User.email LIKE' => '%' . $keyword . '%',
					'UserDetail.first_name LIKE' => '%' . $keyword . '%',
					'UserDetail.last_name LIKE' => '%' . $keyword . '%',
				));
			} else if (!empty($keywords) && count($keywords) > 1) {
				$first_name = $keywords[0];
				$last_name = $keywords[1];
				$in = 1;
				$andConditions = array('AND' => array(
					'UserDetail.first_name LIKE' => '%' . $first_name . '%',
					'UserDetail.last_name LIKE' => '%' . $last_name . '%',
				));
			}
		}

		if (!empty($id)) {
			$andConditions2 = array(
				'UserDetail.institution_id' => $id,
			);
			$andConditions = array_merge($andConditions, $andConditions2);
		}

		if (isset($this->data['UserDetail']['country_id']) && !empty($this->data['UserDetail']['country_id'])) {
			$county = trim($this->data['UserDetail']['country_id']);
			$this->Session->write('user.country', $county);
			$in = 1;
			$andConditions1 = array(
				'UserDetail.country_id LIKE' => '%' . $county . '%',
			);

			$andConditions = array_merge($andConditions, $andConditions1);
		}

		if (isset($this->data['User']['status'])) {
			$status = $this->data['User']['status'];
		} else {
			$status = $this->Session->read('user.status');
		}

		if (isset($status)) {
			$this->Session->write('user.status', $status);
			if ($status != '') {
				$in = 1;
				$andConditions = array_merge($andConditions, array('User.status' => $status));
			}
		}

		if (isset($this->data['User']['per_page_show']) && !empty($this->data['User']['per_page_show'])) {
			$per_page_show = $this->data['User']['per_page_show'];
		}

		if (!empty($orConditions)) {
			$finalConditions = array_merge($finalConditions, $orConditions);
		}
		if (!empty($andConditions)) {
			$finalConditions = array_merge($finalConditions, array('AND' => $andConditions));
		}

		//pr($finalConditions); die;

		$count = $this->User->find('count', array('conditions' => $finalConditions));
		$this->set('count', $count);

		$this->set('title_for_layout', __('Users', true));
		$this->Session->write('user.per_page_show', $per_page_show);
		$this->User->recursive = 0;
		$this->paginate = array('conditions' => $finalConditions, "limit" => $per_page_show, "order" => "User.created DESC");
		$this->set('users', $this->paginate('User'));
		$this->set('in', $in);
	}

	public function admin_planuser($id = null) {

		$orConditions = array();
		$andConditions = array();
		$finalConditions = array();

		//$this->User->unbindModel(array('hasMany' => array('UserPlan')), true);
		//$this->User->bindModel(array('hasOne' => array('UserPlan')), true);

		$in = 0;
		$per_page_show = $this->Session->read('user.per_page_show');
		if (empty($per_page_show)) {
			$per_page_show = ADMIN_PAGING;
		}

		if (isset($this->data['User']['keyword'])) {
			$keyword = trim($this->data['User']['keyword']);
		} else {
			$keyword = $this->Session->read('user.keyword');
		}

		if (isset($keyword)) {
			$this->Session->write('user.keyword', $keyword);
			$keywords = explode(" ", $keyword);
			if (isset($keywords[0]) && !empty($keywords[0]) && count($keywords) < 2) {
				$keyword = $keywords[0];
				$in = 1;
				$orConditions = array('OR' => array(
					'User.email LIKE' => '%' . $keyword . '%',
					'UserDetail.first_name LIKE' => '%' . $keyword . '%',
					'UserDetail.last_name LIKE' => '%' . $keyword . '%',
				));
			} else if (!empty($keywords) && count($keywords) > 1) {
				$first_name = $keywords[0];
				$last_name = $keywords[1];
				$in = 1;
				$andConditions = array('AND' => array(
					'UserDetail.first_name LIKE' => '%' . $first_name . '%',
					'UserDetail.last_name LIKE' => '%' . $last_name . '%',
				));
			}
		}

		if (!empty($id)) {
			$andConditions2 = array(
				'UserPlana.plan_id' => $id, 'UserPlana.is_active' => 1,
			);
			$andConditions = array_merge($andConditions, $andConditions2);
		}

		if (isset($this->data['User']['status'])) {
			$status = $this->data['User']['status'];
		} else {
			$status = $this->Session->read('user.status');
		}

		if (isset($status)) {
			$this->Session->write('user.status', $status);
			if ($status != '') {
				$in = 1;
				$andConditions = array_merge($andConditions, array('User.status' => $status));
			}
		}

		if (isset($this->data['UserDetail']['country_id']) && !empty($this->data['UserDetail']['country_id'])) {
			$county = trim($this->data['UserDetail']['country_id']);
			$this->Session->write('user.country', $county);
			$in = 1;
			$andConditions1 = array(
				'UserDetail.country_id LIKE' => '%' . $county . '%',
			);

			$andConditions = array_merge($andConditions, $andConditions1);
		}

		if (isset($this->data['User']['per_page_show']) && !empty($this->data['User']['per_page_show'])) {
			$per_page_show = $this->data['User']['per_page_show'];
		}

		if (!empty($orConditions)) {
			$finalConditions = array_merge($finalConditions, $orConditions);
		}
		if (!empty($andConditions)) {
			$finalConditions = array_merge($finalConditions, array('AND' => $andConditions));
		}

		//pr($finalConditions); die;

		$this->set('title_for_layout', __('Users', true));
		$this->Session->write('user.per_page_show', $per_page_show);

		$this->User->unbindModel(array('hasMany' => array('UserPlan')), true);

		$this->User->bindModel(array('hasOne' => array('UserPlan' => (array('conditions' => array('UserPlan.plan_id' => $id, 'UserPlan.is_active' => 1))))), true);

		//	pr( $this->User); die;

		$this->paginate = array(
			'joins' => array(
				array(
					'table' => 'user_plans',
					'alias' => 'UserPlana',
					'type' => 'INNER',
					'conditions' => array(
						'UserPlana.user_id = User.id',
					),
				),
			), 'conditions' => $finalConditions, "limit" => $per_page_show, "order" => "User.created DESC");

		$count = $this->User->find('count', array('joins' => array(
			array(
				'table' => 'user_plans',
				'alias' => 'UserPlana',
				'type' => 'INNER',
				'conditions' => array(
					'UserPlana.user_id = User.id',
				),
			),
		), 'conditions' => $finalConditions));
		$this->set('count', $count);

		$this->User->recursive = 3;

		$this->set('users', $this->paginate('User'));

		$this->set('in', $in);
	}

	public function admin_all_transaction($id = null) {

		$options = array('table' => 'user_details',
			'alias' => 'UserDetail',
			'type' => 'inner',
			'conditions' => array(
				'UserTransctionDetail.user_id = UserDetail.user_id',
			),
		);

		$this->set('searchOptions', array('1' => 'Users', '2' => 'Institution', '3' => 'Coupon', '4' => 'Plan', '5' => 'Author'));

		$andCond = array('AND' => array('User.role_id' => 2, 'UserTransctionDetail.user_id !=' => '',
			'UserDetail.user_id !=' => '', 'User.id !=' => ''));
		$orCond = array('OR' => array(
			'UserDetail.institution_id' => '', 'UserDetail.institution_id <' => 1,
		));
		$andCond = array_merge($orCond, $andCond);
		$joins = array(
			array(
				'table' => 'user_details',
				'alias' => 'UserDetail',
				'type' => 'INNER',
				'conditions' => array(
					'UserTransctionDetail.user_id = UserDetail.user_id',
				),
			),
		);
		//$this->UserTransctionDetail->recursive = 2;
		$usersData = $this->UserTransctionDetail->find('all', array('conditions' => $andCond, 'joins' => $joins, 'fields' => array('User.id', 'UserDetail.first_name', 'UserDetail.last_name'), 'order' => 'UserDetail.first_name'));

		//die;
		//pr($usersData); die;
		if (isset($usersData) && !empty($usersData)) {
			//
			foreach ($usersData as $id => $udata) {
				//pr($udata);
				$user[$udata['User']['id']] = $udata['UserDetail']['first_name'] . " " . $udata['UserDetail']['last_name'];
			}

			$this->set('userr', $user);
		}

		$parameters2 = array(
			'fields' => array('User.id', 'UserDetail.first_name', 'UserDetail.last_name'),
			'conditions' => array('User.role_id' => '4'),
			'order' => 'UserDetail.first_name',
			//'contain' => array('User')
		);

		$usersData3 = $this->User->find('all', $parameters2);

		if (isset($usersData3) && !empty($usersData3)) {

			foreach ($usersData3 as $id => $udatas) {

				$userD[$udatas['User']['id']] = $udatas['UserDetail']['first_name'] . " " . $udatas['UserDetail']['last_name'];
			}

			$this->set('instt', $userD);
		}

		$parameters3 = array(
			'fields' => array('User.id', 'UserDetail.first_name', 'UserDetail.last_name'),
			'conditions' => array('User.role_id' => '3'),
			'order' => 'UserDetail.first_name',
			//'contain' => array('User')
		);

		$usersData4 = $this->User->find('all', $parameters3);

		if (isset($usersData4) && !empty($usersData4)) {

			foreach ($usersData4 as $id => $udata4) {

				$user4[$udata4['User']['id']] = $udata4['UserDetail']['first_name'] . " " . $udata4['UserDetail']['last_name'];
			}

			$this->set('authorr', $user4);
		}

		$parameter_cu = array(
			'fields' => array('Coupon.id', 'Coupon.name'),
			'order' => 'Coupon.name',
		);

		$coupn = $this->Coupon->find('list', $parameter_cu);
		$this->set('couponn', $coupn);

		$parameter_plan = array(
			'fields' => array('Plan.id', 'Plan.description'),
			'order' => 'Plan.description',
		);

		$plansn = $this->Plan->find('list', $parameter_plan);
		$this->set('plann', $plansn);

		$orConditions = array();
		$andConditions = array();
		$finalConditions = array();

		$in = 0;
		$per_page_show = $this->Session->read('usertransctiondetail.per_page_show');
		$this->Session->write('usertransctiondetail.per_page_show', $per_page_show);

		if (empty($per_page_show)) {
			$per_page_show = ADMIN_PAGING;
		}

		//  sdsd
		if (isset($this->data['Coupon']['search_id'])) {
			$keyword = trim($this->data['Coupon']['search_id']);
		} else {
			$keyword = $this->Session->read('coupon.search_id');
		}

		if (isset($this->data['Coupon']['plan'])) {
			$plan = trim($this->data['Coupon']['plan']);
		} else {
			$plan = $this->Session->read('coupon.plan');
		}

		if (isset($this->data['Coupon']['author'])) {
			$author = trim($this->data['Coupon']['author']);
		} else {
			$author = $this->Session->read('coupon.author');
		}

		if (isset($this->data['Coupon']['coupon'])) {
			$coupon = trim($this->data['Coupon']['coupon']);
		} else {
			$coupon = $this->Session->read('coupon.coupon');
		}

		if (isset($this->data['Coupon']['inst'])) {
			$inst = trim($this->data['Coupon']['inst']);
		} else {
			$inst = $this->Session->read('coupon.inst');
		}

		if (isset($this->data['Coupon']['user'])) {
			$user = trim($this->data['Coupon']['user']);
		} else {
			$user = $this->Session->read('coupon.user');
		}

		// if (isset($this->data['User']['per_page_show']) && !empty($this->data['User']['per_page_show'])) {
		//   $per_page_show = $this->data['User']['per_page_show'];
		// dfdfd
		//}

		$andConditions = array_merge($andConditions, array('UserTransctionDetail.user_id !=' => '', 'UserDetail.user_id !=' => '', 'User.id !=' => ''));

		//pr($this->data); die;

		if ((isset($user) && !empty($user)) && ($keyword == '1')) {
			$in = 1;
			$this->Session->write('coupon.search_id', $keyword);
			$this->Session->write('coupon.user', $user);
			$andConditions = array_merge($andConditions, array('UserTransctionDetail.user_id' => $user));
			$orConditions = array_merge($orConditions, array('OR' => array(
				'UserDetail.institution_id' => '', 'UserDetail.institution_id <' => 1,
			)));
		} else if ((isset($user) && empty($user)) && ($keyword == '1')) {
			$in = 1;
			$this->Session->write('coupon.search_id', $keyword);
			$this->Session->write('coupon.user', $user);
			$andConditions = array_merge($andConditions, array('User.role_id' => 2));
			$orConditions = array_merge($orConditions, array('OR' => array(
				'UserDetail.institution_id' => '', 'UserDetail.institution_id <' => 1,
			)));
		}

		//pr($orConditions); die;

		if ((isset($inst) && !empty($inst)) && ($keyword == '2')) {
			$in = 1;
			$this->Session->write('coupon.search_id', $keyword);
			$this->Session->write('coupon.inst', $inst);
			$andConditions = array_merge($andConditions, array('UserDetail.institution_id' => $inst));
		} else if ((isset($inst) && empty($inst)) && ($keyword == '2')) {
			$in = 1;
			$this->Session->write('coupon.search_id', $keyword);
			$this->Session->write('coupon.inst', $inst);
			$andConditions = array_merge($andConditions, array('User.role_id' => 2, 'UserDetail.institution_id >' => 0, 'UserDetail.institution_id !=' => ''));
		}

		if ((isset($coupon) && !empty($coupon)) && ($keyword == '3')) {
			$in = 1;
			$this->Session->write('coupon.search_id', $keyword);
			$this->Session->write('coupon.coupon', $coupon);
			$andConditions = array_merge($andConditions, array('UserTransctionDetail.coupon_id' => $coupon));
		} else if ((isset($coupon) && empty($coupon)) && ($keyword == '3')) {
			$in = 1;
			$this->Session->write('coupon.search_id', $keyword);
			$this->Session->write('coupon.coupon', $coupon);
			$andConditions = array_merge($andConditions, array('User.role_id' => 2, 'UserTransctionDetail.coupon_id !=' => '', 'Coupon.id !=' => ''));
		}

		if ((isset($plan) && !empty($plan)) && ($keyword == '4')) {
			$in = 1;
			$this->Session->write('coupon.search_id', $keyword);
			$this->Session->write('coupon.plan', $plan);
			$andConditions = array_merge($andConditions, array('UserPlan.plan_id' => $plan));
			$joins = array(
				array(
					'table' => 'user_details',
					'alias' => 'UserDetail',
					'type' => 'INNER',
					'conditions' => array(
						'UserTransctionDetail.user_id = UserDetail.user_id',
					),
				),
				array(
					'table' => 'user_plans',
					'alias' => 'UserPlan',
					'type' => 'INNER',
					'conditions' => array(
						'UserTransctionDetail.user_id = UserPlan.user_id',
					),
				),
			);
		} else if ((isset($plan) && empty($plan)) && ($keyword == '4')) {
			$in = 1;
			$this->Session->write('coupon.search_id', $keyword);
			$this->Session->write('coupon.plan', $plan);
			$andConditions = array_merge($andConditions, array('User.role_id' => 2, 'UserPlan.plan_id !=' => ''));
			$joins = array(
				array(
					'table' => 'user_details',
					'alias' => 'UserDetail',
					'type' => 'INNER',
					'conditions' => array(
						'UserTransctionDetail.user_id = UserDetail.user_id',
					),
				),
				array(
					'table' => 'user_plans',
					'alias' => 'UserPlan',
					'type' => 'INNER',
					'conditions' => array(
						'UserTransctionDetail.user_id = UserPlan.user_id',
					),
				),
			);
		}

		if ((isset($author) && !empty($author)) && ($keyword == '5')) {
			$in = 1;
			$this->Session->write('coupon.search_id', $keyword);
			$this->Session->write('coupon.author', $author);
			//$andConditions = array_merge($andConditions, array('UserPlan.plan_id' => $plan));
			$andConditions = array_merge($andConditions, array('User.role_id' => 3, 'UserTransctionDetail.user_id' => $author));
		} else if ((isset($author) && empty($author)) && ($keyword == '5')) {
			$in = 1;
			$this->Session->write('coupon.search_id', $keyword);
			$this->Session->write('coupon.author', $author);
			$andConditions = array_merge($andConditions, array('User.role_id' => 3, 'UserTransctionDetail.user_id !=' => ''));
		}

		//pr($this->data); die;

		if (isset($this->data['UserTransctionDetail']['start'])) {
			$start = trim($this->data['UserTransctionDetail']['start']);
		} else {
			$start = $this->Session->read('usertransctiondetail.start');
		}

		if (isset($this->data['UserTransctionDetail']['end'])) {
			$end = trim($this->data['UserTransctionDetail']['end']);
		} else {
			$end = $this->Session->read('usertransctiondetail.end');
		}

		if ((isset($start) && !empty($start)) && (isset($end) && !empty($end))) {
			$in = 1;

			$this->Session->write('usertransctiondetail.start', $start);
			$this->Session->write('usertransctiondetail.end', $end);

			$start = strtotime($start . " 00:00:00");
			$end = strtotime($end . " 24:00:00");

			$andConditions = array_merge($andConditions, array('CAST(UserTransctionDetail.payment_date AS SIGNED) >=' => $start, 'CAST(UserTransctionDetail.payment_date AS SIGNED) <=' => $end));
		}

		if (isset($this->data['UserTransctionDetail']['per_page_show']) && !empty($this->data['UserTransctionDetail']['per_page_show'])) {

			$per_page_show = $this->data['UserTransctionDetail']['per_page_show'];
		}

		if (isset($this->data['Coupon']['keywords'])) {
			$keywordn = trim($this->data['Coupon']['keywords']);
		} else {
			$keywordn = $this->Session->read('coupon.keywords');
		}

		//	pr(count($keywordn)); die;

		if (isset($keywordn)) {
			$this->Session->write('coupon.keywords', $keywordn);
			$keywords = explode(" ", $keywordn);
			if (isset($keywords[0]) && !empty($keywords[0]) && count($keywords) < 2) {
				$keywordn = $keywords[0];
				$in = 1;
				//$orConditions = array_merge($andConditions, array('UserTransctionDetail.user_id' => $user));
				$orConditions = array_merge($orConditions, array('OR' => array(
					'User.email LIKE' => '%' . $keywordn . '%',
					'UserDetail.first_name LIKE' => '%' . $keywordn . '%',
					'UserDetail.last_name LIKE' => '%' . $keywordn . '%',
					'UserTransctionDetail.txn_id LIKE' => '%' . $keywordn . '%',
				)));
			} else if (!empty($keywords) && count($keywords) > 1) {
				$first_name = $keywords[0];
				$last_name = $keywords[1];
				$in = 1;

				$andConditions = array_merge($andConditions, array('AND' => array(
					'UserDetail.first_name LIKE' => '%' . $first_name . '%',
					'UserDetail.last_name LIKE' => '%' . $last_name . '%',
					//'UserDetail.country_id LIKE' => '%' . $county . '%'
				)));
			}
		}

		// pr($orConditions); die;
		if (!empty($finalConditions1)) {
			//$finalConditions = array_merge($finalConditions, $finalConditions1);
		}

		if (!empty($orConditions)) {
			// $finalConditions = array_merge($finalConditions, $orConditions);
		}

		if (!empty($andConditions)) {
			$andConditions = array_merge($finalConditions, $andConditions);
		}
		if (!empty($andConditions)) {
			$finalConditions = array_merge($finalConditions, $orConditions);
			$finalConditions = array_merge($finalConditions, array('AND' => $andConditions));
		}

		if (isset($finalConditions)) {
			$this->Session->write('user.finalConditions', $finalConditions);
		}

		//pr($finalConditions); die;

		$fd = $this->Session->read('user.finalConditions');

		$this->set('title_for_layout', __('Orders', true));
		$this->Session->write('usertransctiondetail.per_page_show', $per_page_show);
		//$this->UserTransctionDetail->recursive = 0;

		if (isset($fd) && !empty($fd)) {

			$finalConditions = $fd;
		} else {
			$finalConditions = $finalConditions;
		}

		//pr($finalConditions); die;

		$this->UserTransctionDetail->recursive = 2;
		$count = $this->UserTransctionDetail->find('count', array('joins' => $joins, 'conditions' => $finalConditions));
		$this->set('count', $count);

		$this->UserTransctionDetail->recursive = 2;

		//	$this->User->bindModel(array('hasOne'=>array('UserDetail')));

		$countTot = $this->UserTransctionDetail->find('all', array('conditions' => $finalConditions, 'joins' => $joins, 'fields' => array(
			'SUM(UserTransctionDetail.amount) as total', 'UserTransctionDetail.mc_currency',
		), 'group' => array('UserTransctionDetail.mc_currency')));

		$this->set('currencyTot', $countTot);

		if ((isset($plan) && empty($plan)) && ($keyword == '4')) {
			$this->paginate = array(
				'conditions' => $finalConditions, "join" => $options, "limit" => $per_page_show, 'joins' => $joins, 'fields' => array('UserPlan.*', 'UserDetail.*', 'UserTransctionDetail.*', 'Coupon.*'), 'group' => array('UserTransctionDetail.id'), "order" => "UserTransctionDetail.id DESC");
		} else {
			$this->paginate = array('conditions' => $finalConditions, "join" => $options, "limit" => $per_page_show, 'joins' => $joins, 'fields' => array('UserDetail.*', 'UserTransctionDetail.*', 'Coupon.*'), "order" => "UserTransctionDetail.id DESC");
		}

		$this->set('coupons', $this->paginate('UserTransctionDetail'));

		$this->set('in', $in);

		//$da = $this->UserProject->find('all');
		//pr($da);
	}

	public function admin_transaction($id = null) {

		$orConditions = array();
		$andConditions = array();
		$finalConditions = array();

		$in = 0;
		$per_page_show = $this->Session->read('usertransctiondetail.per_page_show');
		if (empty($per_page_show)) {
			$per_page_show = ADMIN_PAGING;
		}

		if (isset($id) && !empty($id)) {

			$andConditions = array_merge($andConditions, array('UserTransctionDetail.user_id' => $id));

			// $andConditions = array_merge($andConditions, array('Coupon.id' => $id));
		}

		if (isset($this->data['UserTransctionDetail']['per_page_show']) && !empty($this->data['UserTransctionDetail']['per_page_show'])) {
			$per_page_show = $this->data['UserTransctionDetail']['per_page_show'];
		}

		if (!empty($orConditions)) {
			$finalConditions = array_merge($finalConditions, $orConditions);
		}
		if (!empty($andConditions)) {
			$finalConditions = array_merge($finalConditions, array('AND' => $andConditions));
		}

		//$this->Coupon->unbindModel(array('hasMany' => array('UserTransctionDetail')), true);

		$count = $this->UserTransctionDetail->find('count', array('conditions' => $andConditions));
		$this->set('count', $count);

		$this->set('title_for_layout', __('Transactions', true));
		$this->Session->write('usertransctiondetail.per_page_show', $per_page_show);
		//$this->UserTransctionDetail->recursive = 0;
		$this->paginate = array(
			'conditions' => $andConditions, "order" => "UserTransctionDetail.id DESC");
		$this->set('coupons', $this->paginate('UserTransctionDetail'));
		$this->set('in', $in);
	}

	function admin_user_resetfilter() {
		$this->Session->write('user.keyword', '');
		$this->Session->write('user.status', '');
		$this->Session->write('user.country', '');
		$this->redirect(array('action' => 'index'));
	}

	function admin_trans_all_resetfilter() {
		$this->Session->write('UserTransctionDetail.keyword', '');
		$this->Session->write('UserTransctionDetail.status', '');
		$this->Session->write('usertransctiondetail.start', '');
		$this->Session->write('usertransctiondetail.end', '');
		$this->Session->write('coupon.user', '');
		$this->Session->write('coupon.inst', '');
		$this->Session->write('coupon.author', '');
		$this->Session->write('coupon.start', '');
		$this->Session->write('coupon.end', '');
		$this->Session->write('coupon.coupon', '');
		$this->Session->write('coupon.plan', '');
		$this->Session->write('coupon.search_id', '');
		$this->Session->write('coupon.keywords', '');

		$this->redirect(array('action' => 'all_transaction'));
	}

	function admin_user_third_resetfilter() {
		$this->Session->write('user.keyword', '');
		$this->Session->write('user.status', '');
		$this->Session->write('user.country', '');
		$this->redirect(array('action' => 'thirdparty_users'));
	}

	function admin_institution_users_resetfilter() {
		$this->Session->write('user.keyword', '');
		$this->Session->write('user.status', '');
		$this->Session->write('user.country', '');
		$this->redirect(array('action' => 'institution_users'));
	}

	function admin_institution_resetfilter($id = null) {
		$this->Session->write('user.keyword', '');
		$this->Session->write('user.status', '');
		$this->Session->write('user.country', '');
		$this->redirect(array('action' => 'institution', $id));
	}

	function admin_planuser_resetfilter($id = null) {
		$this->Session->write('user.keyword', '');
		$this->Session->write('user.status', '');
		$this->Session->write('user.country', '');
		$this->redirect(array('action' => 'planuser', $id));
	}

	/**
	 * admin_add method
	 *
	 * @return void
	 */
	public function admin_add() {
		$this->set('title_for_layout', __('Add User', true));
		if ($this->request->is('post') || $this->request->is('put')) {
			//pr($this->request->data); die;
			if ($this->User->saveAssociated($this->request->data)) {
				$this->Session->setFlash(__('The user has been saved successfully.'), 'success');
				$this->redirect(array('action' => 'index'));
			}
		}
	}

	public function admin_thirdparty_add() {
		$this->set('title_for_layout', __('Add Third Party User', true));

		if ($this->request->is('post') || $this->request->is('put')) {

			if ($this->User->saveAssociated($this->request->data)) {

				$lastUserID = $this->User->getLastInsertId();

				$this->request->data['OrganisationUser']['id'] = '';
				$this->request->data['OrganisationUser']['user_id'] = $lastUserID;
				$this->request->data['OrganisationUser']['creator_id'] = $this->request->data['OrganisationUser']['creator_id'];
				$this->request->data['OrganisationUser']['domain_name'] = $this->request->data['OrganisationUser']['domain_name'];

				$this->OrganisationUser->save($this->request->data);

				$this->Session->setFlash(__('The Author has been saved successfully.'), 'success');
				$this->redirect(array('action' => 'thirdparty_users'));
			}
		}
	}

	public function admin_institution_add() {
		$this->set('title_for_layout', __('Add Institution', true));
		if ($this->request->is('post') || $this->request->is('put')) {
			$from = $this->request->data['UserInstitution']['start'];
			$to = $this->request->data['UserInstitution']['end'];
			// $start_titmestamp = strtotime($from) - 86400 + (time() % 86400);
			//  $end_titmestamp = strtotime($to) + 86400;
			$start_titmestamp = strtotime($from);
			$end_titmestamp = strtotime($to);

			if (isset($this->request->data['User']['status']) && $this->request->data['User']['status'] == "on") {
				$this->request->data['User']['status'] = "1";
			} else {
				$this->request->data['User']['status'] = "0";
			}

			$this->request->data['UserInstitution']['start'] = date("Y-m-d h:i:s", $start_titmestamp);
			$this->request->data['UserInstitution']['end'] = date("Y-m-d h:i:s", $end_titmestamp);
			if (!isset($this->request->data['User']['password']) && empty($this->request->data['User']['password'])) {
				$this->request->data['User']['password'] = time();
			}
			$passwordHasher = new SimplePasswordHasher(array('hashType' => 'sha256'));
			$this->request->data['User']['password'] = $passwordHasher->hash($this->request->data['User']['password']);

			//pr($this->request->data); die;
			if ($this->User->saveAssociated($this->request->data)) {
				$this->Session->setFlash(__('The Institution has been saved successfully.'), 'success');
				$this->redirect(array('action' => 'institution_users'));
			} else {
				$this->request->data['UserInstitution']['start'] = date("m/d/Y", strtotime($this->request->data['UserInstitution']['start']));
				$this->request->data['UserInstitution']['end'] = date("m/d/Y", strtotime($this->request->data['UserInstitution']['end']));
			}
		}
	}

	/**
	 * admin_edit method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function admin_edit($id = null, $pid = null, $insid = null) {
		$this->set('title_for_layout', __('Edit User', true));
		$this->User->id = $id;
		if (!$this->User->exists()) {
			$this->Session->setFlash(__('Invalid User.'), 'error');
			$this->redirect(array('action' => 'index'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			// pr($this->request->data);die;
			if (isset($this->request->data['User']['status']) && $this->request->data['User']['status'] == "on") {
				$this->request->data['User']['status'] = "1";
			} else {
				$this->request->data['User']['status'] = "0";
			}

			if ($this->User->saveAssociated($this->request->data)) {
				$this->Session->setFlash(__('The user has been updated successfully.'), 'success');

				if ((isset($pid) && !empty($pid)) && (isset($insid) && !empty($insid))) {
					$this->redirect(array('action' => 'institution', $insid));
				} else if (isset($pid) && !empty($pid)) {
					$this->redirect(array('action' => 'planuser', $pid));
				} else {
					$this->redirect(array('action' => 'index'));
				}
			} else {

				$datas = $this->User->findById($id);
				$this->request->data['UserDetail']['profile_pic'] = $datas['UserDetail']['profile_pic'];
			}
		} else {
			$this->request->data = $this->User->read(null, $id);
			//pr($this->request->data);die;
			if (isset($this->request->data['User']['password'])) {
				unset($this->request->data['User']['password']);
			}
		}
	}

	public function admin_thirdparty_edit($id = null) {
		$this->set('title_for_layout', __('Edit User', true));
		$this->User->id = $id;
		if (!$this->User->exists()) {
			$this->Session->setFlash(__('Invalid User.'), 'error');
			$this->redirect(array('action' => 'index'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			// pr($this->request->data);die;

			if ($this->User->saveAssociated($this->request->data)) {

				$this->request->data['OrganisationUser']['id'] = '';
				$this->request->data['OrganisationUser']['user_id'] = $lastUserID;
				$this->request->data['OrganisationUser']['creator_id'] = $this->request->data['OrganisationUser']['creator_id'];
				$this->request->data['OrganisationUser']['domain_name'] = $this->request->data['OrganisationUser']['domain_name'];

				$this->OrganisationUser->save($this->request->data);

				$this->Session->setFlash(__('The Author has been updated successfully.'), 'success');
				$this->redirect(array('action' => 'thirdparty_users'));
			}
		} else {
			$this->request->data = $this->User->read(null, $id);
			//pr($this->request->data);die;
			if (isset($this->request->data['User']['password'])) {
				unset($this->request->data['User']['password']);
			}
		}
	}

	public function admin_edit_institution($id = null) {
		$this->set('title_for_layout', __('Edit Institution', true));
		$this->User->id = $id;
		if (!$this->User->exists()) {
			$this->Session->setFlash(__('Invalid Institution.'), 'error');
			$this->redirect(array('action' => 'index'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			// pr($this->request->data);die;
			$from = $this->request->data['UserInstitution']['start'];
			$to = $this->request->data['UserInstitution']['end'];
			$start_titmestamp = strtotime($from);
			$end_titmestamp = strtotime($to);
			$this->request->data['UserInstitution']['start'] = date("Y-m-d h:i:s", $start_titmestamp);
			$this->request->data['UserInstitution']['end'] = date("Y-m-d h:i:s", $end_titmestamp);

			if (isset($this->request->data['User']['status']) && $this->request->data['User']['status'] == "on") {
				$this->request->data['User']['status'] = "1";
			} else {
				$this->request->data['User']['status'] = "0";
			}

			if ($this->User->saveAssociated($this->request->data)) {
				$this->Session->setFlash(__('The Institution has been updated successfully.'), 'success');
				$this->redirect(array('action' => 'institution_users'));
			} else {
				$this->request->data['UserInstitution']['start'] = date("m/d/Y", strtotime($this->request->data['UserInstitution']['start']));
				$this->request->data['UserInstitution']['end'] = date("m/d/Y", strtotime($this->request->data['UserInstitution']['end']));
			}
		} else {
			$data = $this->User->read(null, $id);
			$data['UserInstitution']['start'] = date("m/d/Y", strtotime($data['UserInstitution']['start']));
			$data['UserInstitution']['end'] = date("m/d/Y", strtotime($data['UserInstitution']['end']));
			//pr($data); die;
			$this->request->data = $data;
			//pr($this->request->data);die;
			if (isset($this->request->data['User']['password'])) {
				unset($this->request->data['User']['password']);
			}
		}
	}

	/**
	 * admin_view method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function admin_view($id = null) {
		$this->set('title_for_layout', __('View User', true));
		$this->User->id = $id;
		if (!$this->User->exists()) {
			throw new NotFoundException(__('Invalid User'));
		}
		$this->User->recursive = 2;
		//$this->UserPlan->recursive = 2;

		//$this->UserPlan->bindModel(array('belongsTo' => array('Plan', 'PlanType' => array('className' => 'PlanType', 'foreignKey' => 'plan_type'))));

		//$userplan = $this->UserPlan->find('all', array('conditions' => array('UserPlan.user_id' => $this->User->id, 'UserPlan.plan_id !=' => 0, 'UserPlan.is_active' => 1)));

		//$this->set('userplan', $userplan);
		$this->request->data = $this->User->read(null, $this->User->id);

		if (isset($this->request->data['User']['password'])) {
			unset($this->request->data['User']['password']);
		}
	}

	/**
	 * admin_delete method
	 *
	 * @throws MethodNotAllowedException
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function project_delete($id = null) {

		// if (isset($id) && !empty($id)) {
		if (isset($this->data['id']) && !empty($this->data['id'])) {
			$this->loadModel('Project');

			// $id = $id;
			$id = $this->data['id'];

			$this->Project->id = $id;
			$user_id = $this->Auth->user('id');
			$this->set('user_id', $user_id);

			if (!$this->Project->exists()) {
				throw new NotFoundException(__('Invalid project'), 'error');
			}

			if ($this->Project->exists()) {
				if ($this->live_setting == true) {
					//user project delete from mongodb
					$this->Users->userProjectDelete($id);
				}


				/* exits sign-off entry delete ****************/
					$del = array('project_id'=>$id);
					$signoffdata = $this->SignoffProject->find('first',array('conditions'=>$del ));
					if( isset($signoffdata) ){
						if( !empty(!empty($signoffdata['SignoffProject']['task_evidence'])) ){
							$folder_url = WWW_ROOT . PROJECT_SIGNOFF_PATH;
							unlink($folder_url.'/'.$signoffdata['SignoffProject']['task_evidence']);
						}
						$this->SignoffProject->deleteAll($del);
					}
				/********************************/

				// ============== Start == Email which is send when project will be deleted ===============
				$projectdetail = $this->Project->findById($id);
				$project_name = '';
				if (isset($projectdetail['Project']['title']) && !empty($projectdetail['Project']['title'])) {
					$project_name = ucfirst(strip_tags($projectdetail['Project']['title']));
				}

				/************** socket messages **************/
				if (SOCKET_MESSAGES) {
					$current_user_id = $this->Auth->user('id');
					App::import('Controller', 'Risks');
					$Risks = new RisksController;
					$all_owner = $Risks->get_project_users($id, $current_user_id);
					if (isset($all_owner) && !empty($all_owner)) {
						if (($key = array_search($current_user_id, $all_owner)) !== false) {
							unset($all_owner[$key]);
						}
					}
					$p_users = $all_owner;
					$p_open_users = null;
					if (isset($p_users) && !empty($p_users)) {
						foreach ($p_users as $key => $value) {
							if (web_notify_setting($value, 'project', 'project_deleted')) {
								$p_open_users[] = $value;
							}
						}
					}
					$userDetail = get_user_data($current_user_id);
					$content = [
						'socket' => [
							'notification' => [
								'type' => 'project_delete',
								'created_id' => $current_user_id,
								'creator_name' => $userDetail['UserDetail']['full_name'],
								'subject' => 'Project deleted',
								'heading' => 'Project: ' . $project_name,
								'sub_heading' => '',
								'date_time' => timezoneTimeConvertor($current_user_id, date('Y-m-d g:iA'), $format = 'd M Y g:iA') . user_country($current_user_id),
							],
						],
					];
					if (is_array($p_open_users)) {
						$content['socket']['received_users'] = array_values($p_open_users);
					}
					$request = array(
						'header' => array(
							'Content-Type' => 'application/json',
						),
					);
					$HttpSocket = new HttpSocket([
						'ssl_verify_host' => false,
						'ssl_verify_peer_name' => false,
						'ssl_verify_peer' => false,
					]);
					$content = json_encode($content['socket']);

					$results = $HttpSocket->post(CHATURL . '/serveremit', $content, $request);
				}
				/************** socket messages **************/
				$this->loadModel('UserProject');
				$userasspro = $this->UserProject->find('all', array('fields' => array('UserProject.*'), 'conditions' => array('UserProject.user_id' => $user_id, 'UserProject.project_id' => $id, 'UserProject.owner_user' => 1)));

				$crntProject = $this->objView->loadHelper('ViewModel')->checkCurrentProject();

				if (isset($userasspro) && !empty($userasspro)) {
					$this->projectDeleteEmail($project_name, $id);
					// ============== End == Email which is send when project will be deleted =================
					$this->loadModel('ProjectWorkspace');
					$this->loadModel('UserPermission');

					$this->loadModel('Area');
					$this->loadModel('Workspace');
					$this->loadModel('Element');
					$this->loadModel('ElementLink');
					$this->loadModel('ElementDocument');
					$this->loadModel('Feedback');
					$this->loadModel('ElementMindmap');
					$this->loadModel('ElementNote');
					$this->loadModel('ElementDecision');

					$this->loadModel('ElementLevel');
					$this->loadModel('ElementEffort');

					$this->loadModel('UserElementCost');
					$this->loadModel('RmElement');
					$this->loadModel('ElementType');
					$this->loadModel('ElementDependency');
					$this->loadModel('ElementDependancyRelationship');
					$this->loadModel('ElementCostHistory');
					$this->loadModel('ElementCostComment');
					$this->loadModel('ElementCost');
					$this->loadModel('ElementAssignment');
					$this->loadModel('Reminder');
					$this->loadModel('CurrentTask');
					$this->loadModel('SignoffTask');
					$this->loadModel('WorkspaceComment');
					$this->loadModel('WorkspaceCommentLike');
					$this->loadModel('WorkspaceCostComment');
					$this->loadModel('SignoffWorkspace');
					$this->loadModel('CurrentWorkspace');




					$this->loadModel('ProjectElementType');
					$this->loadModel('ProjectComment');
					$this->loadModel('ProjectCostComment');
					$this->loadModel('ProjectDocument');
					$this->loadModel('ProjectStat');
					$this->loadModel('ProjectTile');
					//$this->loadModel('ProjectType');
					$this->loadModel('RmProjectRiskType');
					$this->loadModel('ProjectRag');
					//$this->loadModel('ProjectRagComment');
					$this->loadModel('ProjectOppOrg');
					$this->loadModel('ProjectNote');
					$this->loadModel('ProjectLink');



					$this->loadModel('ElementPermission');
					$this->loadModel('WorkspacePermission');
					$this->loadModel('ProjectPermission');

					$this->loadModel('ProjectGroup');
					$this->loadModel('ProjectGroupUser');
					$this->loadModel('Wiki');
					$this->loadModel('WikiPage');
					$this->loadModel('Blog');
					$this->loadModel('BlogDocument');
					$this->loadModel('BlogLike');
					$this->loadModel('BlogView');
					$this->loadModel('DoList');
					$this->loadModel('WorkspaceActivity');
					$this->loadModel('Activity');
					$this->loadModel('ProjectActivity');
					$this->loadModel('ProjectBoard');
					$this->loadModel('ProjectSkill');
					$this->loadModel('ProjectSubject');
					$this->loadModel('ProjectDomain');
					$this->loadModel('ProjectPropagate');

					$this->loadModel('WorkspacePropagate');
					$this->loadModel('ElementPropagate');

					$this->loadModel('Vote');
					$this->loadModel('WikiPageComment');

					$this->loadModel('ProgramProject');
					//$this->loadModel('Program');
					//$this->loadModel('ProgramUser');

					/* Reward Manager */
					$this->loadModel('RewardAccelerate');
					$this->loadModel('RewardAccelerationHistory');
					$this->loadModel('RewardAssignment');
					$this->loadModel('RewardCharity');
					$this->loadModel('RewardHistory');
					$this->loadModel('RewardOffer');
					$this->loadModel('RewardOfferShop');
					$this->loadModel('RewardRedeem');
					$this->loadModel('RewardSetting');
					$this->loadModel('RewardUserAcceleration');

					$this->loadModel('CurrentProject');

					$wsp_ids = $this->UserPermission->find('list', array('fields' => array('UserPermission.workspace_id','UserPermission.workspace_id'), 'conditions' => array('UserPermission.project_id' => $id,'UserPermission.role' => 'Creator')));

					$task_ids = $this->UserPermission->find('list', array('fields' => array('UserPermission.element_id','UserPermission.element_id'), 'conditions' => array('UserPermission.project_id' => $id,'UserPermission.role' => 'Creator')));

					$area_ids = $this->UserPermission->find('list', array('fields' => array('UserPermission.area_id','UserPermission.area_id'), 'conditions' => array('UserPermission.project_id' => $id,'UserPermission.role' => 'Creator')));


					$this->ElementPermission->deleteAll(array('ElementPermission.project_id' => $id), false);

					$this->Vote->deleteAll(array('Vote.project_id' => $id));

					$this->ProjectSkill->deleteAll(array('ProjectSkill.project_id' => $id), false);
					$this->ProjectSubject->deleteAll(array('ProjectSubject.project_id' => $id), false);
					$this->ProjectDomain->deleteAll(array('ProjectDomain.project_id' => $id), false);


					$up_id = $userasspro['0']['UserProject']['project_id'];

					$this->WorkspacePermission->deleteAll(array('WorkspacePermission.user_project_id' => $userasspro['0']['UserProject']['project_id']));

					$this->ProjectPermission->deleteAll(array('ProjectPermission.user_project_id' => $userasspro['0']['UserProject']['project_id']));

					$this->ProjectPropagate->deleteAll(array('ProjectPropagate.user_project_id' => $userasspro['0']['UserProject']['project_id']));

					$this->WorkspacePropagate->deleteAll(array('WorkspacePropagate.user_project_id' => $userasspro['0']['UserProject']['project_id']));

					$this->ElementPropagate->deleteAll(array('ElementPropagate.project_id' => $id));

					$this->ProjectGroup->deleteAll(array('ProjectGroup.user_project_id' => $userasspro['0']['UserProject']['project_id']));

					$this->ProjectGroupUser->deleteAll(array('ProjectGroupUser.user_project_id' => $userasspro['0']['UserProject']['project_id']));

					$this->DoList->deleteAll(array('DoList.project_id' => $id));

					$this->Blog->deleteAll(array('Blog.project_id' => $id));

					$this->BlogDocument->deleteAll(array('BlogDocument.project_id' => $id));

					$this->Wiki->deleteAll(array('Wiki.project_id' => $id));

					$this->WikiPage->deleteAll(array('WikiPage.project_id' => $id));

					$this->WikiPageComment->deleteAll(array('WikiPageComment.project_id' => $id));

					$this->BlogLike->deleteAll(array('BlogLike.project_id' => $id));

					$this->BlogView->deleteAll(array('BlogView.project_id' => $id));

					$this->ProjectActivity->deleteAll(array('ProjectActivity.project_id' => $id));

					$this->WorkspaceActivity->deleteAll(array('WorkspaceActivity.project_id' => $id));

					$this->Activity->deleteAll(array('Activity.project_id' => $id));

					$this->ProjectBoard->deleteAll(array('ProjectBoard.project_id' => $id));
					$this->ProjectComment->deleteAll(array('ProjectComment.project_id' => $id));
					$this->ProjectCostComment->deleteAll(array('ProjectCostComment.project_id' => $id));
					$this->ProjectDocument->deleteAll(array('ProjectDocument.project_id' => $id));
					$this->ProjectStat->deleteAll(array('ProjectStat.project_id' => $id));
					$this->ProjectTile->deleteAll(array('ProjectTile.project_id' => $id));
					//$this->ProjectType->deleteAll(array('ProjectType.project_id' => $id));
					$this->RmProjectRiskType->deleteAll(array('RmProjectRiskType.project_id' => $id));
					$this->ProjectRag->deleteAll(array('ProjectRag.project_id' => $id));
					//$this->ProjectRagComment->deleteAll(array('ProjectRagComment.project_id' => $id), false);
					$this->ProjectOppOrg->deleteAll(array('ProjectOppOrg.project_id' => $id), false);
					$this->ProjectNote->deleteAll(array('ProjectNote.project_id' => $id), false);
					$this->ProjectLink->deleteAll(array('ProjectLink.project_id' => $id), false);

					$usp = null;

					//$program_ids = $this->ProgramProject->find('list', ['conditions' => ['ProgramProject.project_id' => $id ],'fields' => array('ProgramProject.program_id')]);

					$this->ProgramProject->deleteAll(array('ProgramProject.project_id' => $id));
					//$this->Program->deleteAll(array('Program.id' => $program_ids));
					//$this->ProgramUser->deleteAll(array('ProgramUser.program_id' => $program_ids));



					//$this->ElementMindmap->deleteAll(array('ElementMindmap.element_id' => $elm['Element']['id'], 'ElementMindmap.user_id' => $user_id));

					/* Reward Manager */
					$this->RewardAccelerate->deleteAll(array('RewardAccelerate.project_id' => $id));
					$this->RewardAccelerationHistory->deleteAll(array('RewardAccelerationHistory.project_id' => $id));
					$this->RewardAssignment->deleteAll(array('RewardAssignment.project_id' => $id));
					$this->RewardCharity->deleteAll(array('RewardCharity.project_id' => $id));
					$this->RewardHistory->deleteAll(array('RewardHistory.project_id' => $id));
					$this->RewardOffer->deleteAll(array('RewardOffer.project_id' => $id));
					$this->RewardOfferShop->deleteAll(array('RewardOfferShop.project_id' => $id));
					$this->RewardRedeem->deleteAll(array('RewardRedeem.project_id' => $id));
					$this->RewardSetting->deleteAll(array('RewardSetting.project_id' => $id));
					$this->RewardUserAcceleration->deleteAll(array('RewardUserAcceleration.project_id' => $id));
					$this->CurrentProject->deleteAll(array('CurrentProject.project_id' => $id));
					$currentProjectStatus = true;
					$prjdetails = $this->Project->findById($id);
					// ================ save WSP delete data into DELETE DATA table =========
					$prjdeletedata['DeleteData']['id'] = null;
					$prjdeletedata['DeleteData']['item_id'] = $id;
					$prjdeletedata['DeleteData']['item_type'] = 'project';
					$prjdeletedata['DeleteData']['item_title'] = $prjdetails['Project']['title'];
					$prjdeletedata['DeleteData']['item_desc'] = $prjdetails['Project']['description'];
					$prjdeletedata['DeleteData']['item_deleted_by'] = $this->Auth->user('id');
					$prjdeletedata['DeleteData']['item_created_by'] = $prjdetails['Project']['updated_user_id'];
					$prjdeletedata['DeleteData']['user_role'] = $this->Auth->user('role_id');
					$prjdeletedata['DeleteData']['date_time'] = date('Y-m-d h:i:s');
					//$this->project_delete_data($prjdeletedata);
					// ===========================================================================



					$this->Feedback->deleteAll(array('Feedback.project_id' => $id));

					$this->ProjectWorkspace->deleteAll(array('ProjectWorkspace.project_id' => $id));

					$this->ProjectElementType->deleteAll(array('ProjectElementType.project_id' => $id));

					if(isset($wsp_ids ) && !empty($wsp_ids )){

					$this->Workspace->deleteAll(array('Workspace.id' => $wsp_ids));

					$wsp_comment_ids = $this->WorkspaceComment->find('list', array('fields' => array('WorkspaceComment.id','WorkspaceComment.workspace_id'), 'conditions' => array('WorkspaceComment.workspace_id' => $wsp_ids )));

					if(isset($wsp_comment_ids) && !empty($wsp_comment_ids)){
					$this->WorkspaceCommentLike->deleteAll(array('WorkspaceCommentLike.workspace_comment_id' => $wsp_comment_ids));
					}
					$this->WorkspaceComment->deleteAll(array('WorkspaceComment.workspace_id' => $wsp_ids));

					$this->WorkspaceCostComment->deleteAll(array('WorkspaceCostComment.workspace_id' => $wsp_ids));
					$this->SignoffWorkspace->deleteAll(array('SignoffWorkspace.workspace_id' => $wsp_ids));
					$this->CurrentWorkspace->deleteAll(array('CurrentWorkspace.workspace_id' => $wsp_ids));

					}

					if(isset($task_ids ) && !empty($task_ids )){

					$this->ElementLink->deleteAll(array('ElementLink.element_id' => $task_ids));

					$this->ElementDecision->deleteAll(array('ElementDecision.element_id' => $task_ids));

					$this->ElementDocument->deleteAll(array('ElementDocument.element_id' => $task_ids));

					/* $this->Feedback->deleteAll(array('Feedback.element_id' => $elm['Element']['id'])); */

					$this->ElementNote->deleteAll(array('ElementNote.element_id' => $task_ids));

					$this->ElementMindmap->deleteAll(array('ElementMindmap.element_id' => $task_ids ));

					$this->Vote->deleteAll(array('Vote.element_id' => $task_ids));

					$this->ElementLevel->deleteAll(array('ElementLevel.element_id' => $task_ids));

					$this->ElementEffort->deleteAll(array('ElementEffort.element_id' => $task_ids));

					$this->SignoffTask->deleteAll(array('SignoffTask.element_id' => $task_ids));

					$this->Element->deleteAll(array('Element.id' => $task_ids));

					$this->UserElementCost->deleteAll(array('UserElementCost.element_id' => $task_ids));
					$this->RmElement->deleteAll(array('RmElement.element_id' => $task_ids));
					$this->ElementType->deleteAll(array('ElementType.element_id' => $task_ids));
					$this->ElementDependency->deleteAll(array('ElementDependency.element_id' => $task_ids));
					$this->ElementDependancyRelationship->deleteAll(array('ElementDependancyRelationship.element_id' => $task_ids));
					$this->ElementCostHistory->deleteAll(array('ElementCostHistory.element_id' => $task_ids));
					$this->UserElementCost->deleteAll(array('UserElementCost.element_id' => $task_ids));
					$this->ElementCostComment->deleteAll(array('ElementCostComment.element_id' => $task_ids));
					$this->ElementCost->deleteAll(array('ElementCost.element_id' => $task_ids));
					$this->ElementAssignment->deleteAll(array('ElementAssignment.element_id' => $task_ids));
					$this->Reminder->deleteAll(['Reminder.element_id' => $task_ids]);
					$this->CurrentTask->deleteAll(['CurrentTask.task_id' => $task_ids]);



					}

					if(isset($area_ids ) && !empty($area_ids )){
					$this->Area->deleteAll(array('Area.id' => $area_ids));
					}


					/* foreach ($userasspro as $usp) {



						$this->Feedback->deleteAll(array('Feedback.project_id' => $usp['UserProject']['id']));

						if (isset($usp) && !empty($usp)) {

							$ProjectWorkspace = $this->ProjectWorkspace->find('all', array('fields' => array('ProjectWorkspace.*'), 'conditions' => array('ProjectWorkspace.project_id' => $usp['UserProject']['project_id'])));

							foreach ($ProjectWorkspace as $wsp) {

								$wspdetails = $this->Workspace->findById($wsp['ProjectWorkspace']['workspace_id']);

								// ================ save WSP delete data into DELETE DATA table =========
								$wspdeletedata['DeleteData']['id'] = null;
								$wspdeletedata['DeleteData']['item_id'] = $wsp['ProjectWorkspace']['workspace_id'];
								$wspdeletedata['DeleteData']['item_type'] = 'workspace';
								$wspdeletedata['DeleteData']['item_title'] = $wspdetails['Workspace']['title'];
								$wspdeletedata['DeleteData']['item_desc'] = $wspdetails['Workspace']['description'];
								$wspdeletedata['DeleteData']['item_deleted_by'] = $this->Auth->user('id');
								$wspdeletedata['DeleteData']['item_created_by'] = $wspdetails['Workspace']['updated_user_id'];
								$wspdeletedata['DeleteData']['user_role'] = $this->Auth->user('role_id');
								$wspdeletedata['DeleteData']['date_time'] = date('Y-m-d h:i:s');
								//$this->wsp_delete_data($wspdeletedata);
								// ===========================================================================

								$this->ProjectWorkspace->delete($wsp['ProjectWorkspace']['id']);
								$this->Workspace->deleteAll(array('Workspace.id' => $wsp['ProjectWorkspace']['workspace_id']));

								$area = $this->Area->find('all', array('fields' => array('Area.*'), 'conditions' => array('Area.workspace_id' => $wsp['ProjectWorkspace']['workspace_id'] )));

								foreach ($area as $ar) {

									$Element = $this->Element->find('all', array('fields' => array('Element.*'), 'conditions' => array('Element.area_id' => $ar['Area']['id'])));

									// ================ save element delete data into DELETE DATA table =========
									if (!empty($Element)) {

										foreach ($Element as $elm) {
											$eledeletedata['DeleteData']['id'] = null;
											$eledeletedata['DeleteData']['item_id'] = $elm['Element']['id'];
											$eledeletedata['DeleteData']['item_type'] = 'element';
											$eledeletedata['DeleteData']['item_title'] = $elm['Element']['title'];
											$eledeletedata['DeleteData']['item_desc'] = $elm['Element']['description'];
											$eledeletedata['DeleteData']['item_deleted_by'] = $this->Auth->user('id');
											$eledeletedata['DeleteData']['item_created_by'] = $elm['Element']['updated_user_id'];
											$eledeletedata['DeleteData']['user_role'] = $this->Auth->user('role_id');
											$eledeletedata['DeleteData']['date_time'] = date('Y-m-d h:i:s');
											//$this->element_delete_data($eledeletedata);
										//	$this->DeleteData->save($eledeletedata);
										}

									}
									// ===========================================================================

									foreach ($Element as $elm) {

										$this->ElementLink->deleteAll(array('ElementLink.element_id' => $elm['Element']['id']));

										$this->ElementDecision->deleteAll(array('ElementDecision.element_id' => $elm['Element']['id']));

										$this->ElementDocument->deleteAll(array('ElementDocument.element_id' => $elm['Element']['id']));



										$this->ElementNote->deleteAll(array('ElementNote.element_id' => $elm['Element']['id']));

										$this->ElementMindmap->deleteAll(array('ElementMindmap.element_id' => $elm['Element']['id'], 'ElementMindmap.user_id' => $user_id));


										$this->Vote->deleteAll(array('Vote.element_id' => $elm['Element']['id']));



									}

									$this->Element->deleteAll(array('Element.area_id' => $ar['Area']['id']));
								}

								$this->Area->deleteAll(array('Area.workspace_id' => $wsp['ProjectWorkspace']['workspace_id']));

							}

							$this->ProjectWorkspace->deleteAll(array('ProjectWorkspace.project_id' => $id));
						}
					} */

					$this->Project->delete($id);
					//$this->UserProject->delete($usp['UserProject']['id']);
					$this->UserProject->delete(array('UserProject.project_id' => $id));


					/* if (isset($crntProject) && !empty($crntProject)) {
						if( isset($crntProject['CurrentProject']['project_id']) && $crntProject['CurrentProject']['project_id'] == $id ){
							$currentProjectStatus = true;
						} else {
							$currentProjectStatus = false;
						}
					} else {
						$currentProjectStatus = false;
					} */

					echo json_encode(['success' => true,'currentproject'=>$currentProjectStatus]);
					exit;
					die('success');
				} else {
					$this->Session->setFlash(__('Project could not deleted.'), 'error');
					echo json_encode(['success' => true,'currentproject'=>false]);
					exit;
					die('error');
				}

			} else {
				$this->Session->setFlash(__('Project could not deleted.'), 'error');
			}
		}
		//echo json_encode(['success' => true]);
		echo json_encode(['success' => true,'currentproject'=>false]);
		exit;
		die('error');
	}

	public function admin_delete($id = null) {

		if (isset($this->data['id']) && !empty($this->data['id'])) {

			//if (isset($id)) {

			$id = $this->data['id'];

			$this->User->id = $id;

			if (!$this->User->exists()) {
				throw new NotFoundException(__('Invalid User'), 'error');
			}

			if ($this->User->delete()) {

				//if ($id) {

				$this->loadModel('UserProject');
				$this->loadModel('ProjectWorkspace');
				$this->loadModel('Project');
				$this->loadModel('Area');
				$this->loadModel('Workspace');
				$this->loadModel('Element');
				$this->loadModel('ElementLink');
				$this->loadModel('ElementDocument');
				$this->loadModel('ElementFeedback');
				$this->loadModel('ElementMindmap');
				$this->loadModel('ElementNote');
				$this->loadModel('ElementDecision');

				$this->UserDetail->deleteAll(array('UserDetail.user_id' => $id));
				//$this->UserPlan->deleteAll(array('UserPlan.user_id' => $id));

				//$userasspro = $this->UserProject->find('all', array('fields'=>array('UserProject.*'),'conditions' => array('UserProject.user_id' => $id, 'UserProject.owner_user'=>1)));

				$userasspro = $this->UserProject->find('all', array('fields' => array('UserProject.*'), 'conditions' => array('UserProject.user_id' => $id)));

				foreach ($userasspro as $usp) {

					$this->UserProject->delete($usp['UserProject']['id']);

					$this->Project->delete($usp['UserProject']['project_id']);

					$ProjectWorkspace = $this->ProjectWorkspace->find('all', array('fields' => array('ProjectWorkspace.*'), 'conditions' => array('ProjectWorkspace.project_id' => $usp['UserProject']['project_id'])));

					foreach ($ProjectWorkspace as $wsp) {

						$this->ProjectWorkspace->delete($wsp['ProjectWorkspace']['id']);

						$this->Workspace->deleteAll(array('Workspace.id' => $wsp['ProjectWorkspace']['workspace_id']));

						$area = $this->Area->find('all', array('fields' => array('Area.*'), 'conditions' => array('Area.workspace_id' => $wsp['ProjectWorkspace']['workspace_id'], 'Area.studio_status !=' => 1)));

						foreach ($area as $ar) {
							// pr($ar);

							$Element = $this->Element->find('all', array('fields' => array('Element.*'), 'conditions' => array('Element.area_id' => $ar['Area']['id'])));

							foreach ($Element as $elm) {

								//pr($elm);

								$this->ElementLink->deleteAll(array('ElementLink.element_id' => $elm['Element']['id']));

								$this->ElementDecision->deleteAll(array('ElementDecision.element_id' => $elm['Element']['id']));

								$this->ElementDocument->deleteAll(array('ElementDocument.element_id' => $elm['Element']['id']));

								$this->ElementFeedback->deleteAll(array('ElementFeedback.element_id' => $elm['Element']['id']));

								$this->ElementNote->deleteAll(array('ElementNote.element_id' => $elm['Element']['id']));

								$this->ElementMindmap->deleteAll(array('ElementMindmap.element_id' => $elm['Element']['id'], 'ElementMindmap.user_id' => $id));
							}

							$this->Element->deleteAll(array('Element.area_id' => $ar['Area']['id']));
						}

						$this->Area->deleteAll(array('Area.workspace_id' => $wsp['ProjectWorkspace']['workspace_id']));
					}
				}

				$this->UserProject->deleteAll(array('UserProject.user_id' => $id, 'UserProject.owner_user' => 1));

				//$this->UserTransctionDetail->deleteAll(array('UserTransctionDetail.user_id' => $id));

				$this->Session->setFlash(__('User has been deleted successfully.'), 'success');
				die('success');
			} else {
				$this->Session->setFlash(__('User could not deleted successfully.'), 'error');
			}
		}
		die('error');
	}

	public function deleteDoc($id = null) {
		$this->loadModel('ElementDocument');
		if (isset($this->data['id']) && !empty($this->data['id'])) {
			$id = $this->data['id'];
			$this->ElementDocument->id = $id;
			if (!$this->ElementDocument->exists()) {
				throw new NotFoundException(__('Invalid User'), 'error');
			}

			if ($this->ElementDocument->delete()) {
				//$this->UserDetail->delete($id);
				//$this->UserPlan->deleteAll(array('UserPlan.user_id' => $id));
				//$this->UserTransctionDetail->deleteAll(array('UserTransctionDetail.user_id' => $id));
				$this->Session->setFlash(__('Document has been deleted successfully.'), 'success');
				die('success');
			} else {
				$this->Session->setFlash(__('Document could not deleted successfully.'), 'error');
			}
		}
		die('error');
	}

	public function admin_deleteAuth($id = null) {
		if (isset($this->data['id']) && !empty($this->data['id'])) {
			$id = $this->data['id'];
			$this->User->id = $id;
			if (!$this->User->exists()) {
				throw new NotFoundException(__('Invalid Author'), 'error');
			}

			if ($this->User->delete()) {
				$this->UserDetail->delete($id);
				//$this->UserPlan->deleteAll(array('UserPlan.user_id' => $id));
				//$this->UserTransctionDetail->deleteAll(array('UserTransctionDetail.user_id' => $id));
				$this->Session->setFlash(__('Author has been deleted successfully.'), 'success');
				die('success');
			} else {
				$this->Session->setFlash(__('Author could not deleted successfully.'), 'error');
			}
		}
		die('error');
	}

	public function admin_deleteIns($id = null) {
		if (isset($this->data['id']) && !empty($this->data['id'])) {
			$id = $this->data['id'];
			$this->User->id = $id;
			if (!$this->User->exists()) {
				throw new NotFoundException(__('Invalid Institution'), 'error');
			}

			if ($this->User->delete()) {
				$this->UserDetail->delete($id);

				//$this->UserPlan->deleteAll(array('UserPlan.user_id' => $id));
				//$this->UserTransctionDetail->deleteAll(array('UserTransctionDetail.user_id' => $id));
				$this->Session->setFlash(__('Institution has been deleted successfully.'), 'success');
				die('success');
			} else {
				$this->Session->setFlash(__('Institution could not deleted successfully.'), 'error');
			}
		}
		die('error');
	}

	/**
	 * login function for  admin panel
	 *
	 */
	public function admin_login() {
		$this->layout = 'admin_login';

		/* if( $_SERVER['SERVER_NAME'] == "jeera.ideascast.com" || $_SERVER['SERVER_NAME'] == "ideascast.com" ){

				if ($this->Auth->loggedIn()) {
					return $this->redirect(array('controller' => 'dashboards', 'action' => 'index', 'admin' => true));
				}

			}else{

				return $this->redirect(array('controller' => 'users', 'action' => 'login', 'admin' => false));

		*/

		if ($_SERVER['SERVER_NAME'] != SERVER_NAME && $_SERVER['SERVER_NAME'] != LOCALIP && $_SERVER['SERVER_NAME'] != LOCALIP && $_SERVER['SERVER_NAME'] != "jeera.ideascast.com") {
			return $this->redirect(array('controller' => 'users', 'action' => 'login', 'admin' => false));
		}

		if ($this->Auth->loggedIn()) {
			return $this->redirect(array('controller' => 'dashboards', 'action' => 'index', 'admin' => true));
		}

		if ($this->request->is('post')) {
			// Remeber Me functionality
			if (isset($this->data['User']['remember']) && !empty($this->data['User']['remember'])) {
				setcookie("username", $this->request->data['User']['email'], time() + 3600 * 24 * 7);
				setcookie("password", $this->request->data['User']['password'], time() + 3600 * 24 * 7);
			} else {
				setcookie("username", '');
				setcookie("password", '');
			}
			if ($this->Auth->login()) {
				$this->Session->setFlash('You have logged in successfully ', 'success');
				$this->Session->write('Auth.User', $this->Session->read('Auth.Admin.User'));
				return $this->redirect($this->Auth->redirectUrl());
				// Prior to 2.3 use
				// `return $this->redirect($this->Auth->redirect());`
			} else {
				$this->Session->setFlash('Username or password is incorrect', 'error');
			}
		}
		$this->set('title_for_layout', 'Admin Login');
	}

	/**
	 * logout function for  admin panel
	 *
	 */
	public function admin_logout() {
		if ($this->Session->check('Auth.Admin')) {
			$this->Session->setFlash('', null, null, 'auth');
			$this->Session->delete('Auth.Admin');
			$this->Session->delete('Auth.User');
		}
		//$this->Session->setFlash(__('Logout Successfully.'), 'success');
		$this->redirect($this->Auth->logout());
	}

	/**
	 * update_status method
	 *
	 * @return void
	 */
	public function admin_user_updatestatus() {
		if ($this->request->is('ajax')) {
			$this->autoRender = false;
			$this->loadModel('User');
			$this->request->data['User'] = $this->request->data;
			if ($this->User->save($this->request->data)) {
				$this->Session->setFlash(__('User status has been updated successfully.'), 'success');
				die('success');
			} else {
				$this->Session->setFlash(__('User status could not updated successfully.'), 'error');
			}
		}
		die('error');
	}

	/**
	 * Admin Profile edit in admin panel
	 *
	 * @return void
	 */
	public function admin_profile() {
		$id = $detid= $this->Session->read('Auth.Admin.User.id');
		//$detid = $this->Session->read('Auth.Admin.User.UserDetail.id');
		//pr($this->Session->read('Auth')); //die;
		$this->User->id = $id;
		if (!$this->User->exists()) {
			$this->Session->setFlash(__('Invalid User Profile.'), 'error');
			die('error');
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->User->saveAssociated($this->request->data)) {
				//  $this->Session->write('Auth.Admin', $this->User->read(null, $id));
				$users = $this->User->find('first', array('conditions' => array('User.id' => $id)));
				$userDetails = $this->UserDetail->find('first', array('conditions' => array('UserDetail.user_id' => $detid)));
				$users['User']['UserDetail'] = $userDetails['UserDetail'];
				// pr($users);die;
				//
				$this->Session->write('Auth.Admin', $users);
				$this->Session->setFlash(__('The Admin Profile has been updated successfully.'), 'success');
				die('success');
			}
		} else {
			$this->request->data = $this->User->read(null, $id);
			if (isset($this->request->data['User']['password'])) {
				unset($this->request->data['User']['password']);
			}
		}
	}


	public function org_location(){
		if ($this->request->isAjax()) {
            $this->layout = false;
            $response = ['success' => false, 'content' => []];
            $data = [];
            if ($this->request->is('post') || $this->request->is('put')) {
            	$post = $this->request->data;
            	$org_id = $post['org_id'];

        		$this->loadModel('OrganizationLocation');
        		$data = $this->OrganizationLocation->query("SELECT JSON_ARRAYAGG(JSON_OBJECT( 'id', locations.id, 'name', locations.name)) AS locations FROM locations inner join organization_locations on organization_locations.location_id = locations.id inner join countries on countries.id = locations.country_id  WHERE organization_locations.organization_id = $org_id ORDER BY locations.name ASC");

        		$locations = [];
    			$details = json_decode($data[0][0]['locations'], true);
    			if(isset($details) && !empty($details)){
        			foreach ($details as $key => $value) {
        				$locations[] = ['id' => $value['id'], 'name' => $value['name']];
        			}
        		}
        		$response['success'] = true;
        		$response['content'] = $locations;
        		//
            }
            echo json_encode($response);
            exit;
        }
	}

	public function dottect_users(){
		if ($this->request->isAjax()) {
            $this->layout = false;
            $response = ['success' => false, 'content' => [], 'dotted_users' => []];
            $data = [];
            if ($this->request->is('post') || $this->request->is('put')) {
            	$post = $this->request->data;

            	$reports_to = $post['reports_to'];

            	$edited_user_id = (isset($post['edited_user_id']) && !empty($post['edited_user_id'])) ? $post['edited_user_id'] : null;


            	$id = $this->Session->read('Auth.User.id');
            	$exid = [$id, $reports_to];
            	if(isset($post['type']) && !empty($post['type'])){
					$exid = [$reports_to];
				}
            	$exid = implode(',', $exid);

        		$all_users = array();
				$ulist = $this->User->query("SELECT ud.user_id, ud.first_name, ud.last_name FROM users u INNER JOIN user_details ud ON ud.user_id = u.id WHERE u.role_id = 2 AND u.status = 1 AND u.is_activated = 1 AND u.id NOT IN ($exid) ORDER BY ud.first_name, ud.last_name ASC");
				// pr($ulist,1);
				if (isset($ulist) && !empty($ulist)) {
					foreach ($ulist as $u) {
						$all_users[] = ['value' => $u['ud']['user_id'], 'label' => $u['ud']['first_name'] . ' ' . $u['ud']['last_name']];
					}
				}
				$selectedDotted = [];


				if(isset($edited_user_id) && !empty($edited_user_id)){
				$dottedUsers = $this->User->query("SELECT dotted_user_id FROM user_dotted_lines WHERE user_id = $edited_user_id");
				if(isset($dottedUsers) && !empty($dottedUsers)){
					$selectedDotted = Set::extract($dottedUsers, '/user_dotted_lines/dotted_user_id');
				}
				}

				if(isset($post['type']) && !empty($post['type'])){
					$selectedDotted = [];
				}

        		$response['success'] = true;
        		$response['content'] = $all_users;
        		$response['dotted_users'] = $selectedDotted;
            }
            echo json_encode($response);
            exit;
        }
	}

	public function report_to_users(){
		if ($this->request->isAjax()) {
            $this->layout = false;
            $response = ['success' => false, 'content' => [], 'reports_to_id' => ''];
            $data = [];
            if ($this->request->is('post') || $this->request->is('put')) {
            	$post = $this->request->data;
            	// pr($post, 1);
            	$id = $this->Session->read('Auth.User.id');
            	$edited_user_id = (isset($post['edited_user_id']) && !empty($post['edited_user_id'])) ? $post['edited_user_id'] : null;



        		$all_users = array();
        		$ulist = $this->User->query("SELECT ud.user_id, ud.first_name, ud.last_name FROM users u INNER JOIN user_details ud ON ud.user_id = u.id WHERE u.role_id = 2 AND u.status = 1 AND u.is_activated = 1 AND u.id <> $id ORDER BY ud.first_name, ud.last_name ASC");
        		if(isset($post['type']) && !empty($post['type'])){
        			$ulist = $this->User->query("SELECT ud.user_id, ud.first_name, ud.last_name FROM users u INNER JOIN user_details ud ON ud.user_id = u.id WHERE u.role_id = 2 AND u.status = 1 AND u.is_activated = 1 ORDER BY ud.first_name, ud.last_name ASC");
        		}

				// pr($ulist,1);
				if (isset($ulist) && !empty($ulist)) {
					foreach ($ulist as $u) {
						$all_users[] = ['value' => $u['ud']['user_id'], 'label' => $u['ud']['first_name'] . ' ' . $u['ud']['last_name']];
					}
				}

				$reports_to_id = null;
				if(isset($edited_user_id) && !empty($edited_user_id)){
						$reportsUser = $this->User->query("SELECT reports_to_id FROM user_details WHERE user_id = $edited_user_id");
					if(isset($reportsUser) && !empty($reportsUser)){
						$reports_to_id = $reportsUser[0]['user_details']['reports_to_id'];
					}
				}


				if(isset($post['type']) && !empty($post['type'])){
					$reports_to_id = null;
				}

        		$response['success'] = true;
        		$response['content'] = $all_users;
        		$response['reports_to_id'] = $reports_to_id;
            }
            echo json_encode($response);
            exit;
        }
	}


	public function myaccountedit($id = null) {
		$this->layout = 'inner';
		if(empty($id)){
			$id = $this->Session->read('Auth.User.id');
			$detid = $this->Session->read('Auth.User.UserDetail.id');
		}else{
			$uds = $this->User->find('first', array('conditions' => array('User.id' => $id)));
			$detid = $uds['UserDetail']['id'];
		}

		$CheckProfileEdit = CheckProfileEdit($id);

		if($CheckProfileEdit !=1){
				$this->redirect(array('action' => 'lists', 'controller' => 'projects'));
		}
		// pr($this->Session->read('Auth')); die;

		$this->set('title_for_layout', __('Edit Profile', true));
		$this->set('id', $id);
		$this->set('edited_id', $id);
		$this->setJsVar('edited_id', $id);


		$this->User->id = $id;

		// SKILLS LIST
		$skills = array();
		$skillsAll = $this->Skill->query("SELECT * FROM skill");
		//pr($skillsAll); die;
		if (isset($skillsAll) && !empty($skillsAll)) {
			foreach ($skillsAll as $skil) {
				$skills[$skil['skill']['id']] = $skil['skill']['title'];
			}
		}


		// ORGANIZATIONS LIST
		$organizations = array();
		$check_admin_settings = check_admin_settings($id);

		if($check_admin_settings == 2){
			$current_org = $this->objView->loadHelper('Permission')->current_org();
			$current_org = $current_org['organization_id'];
			if(!empty($current_org)){
					$organizationsAll = $this->Skill->query("SELECT id, name FROM organizations WHERE id = '$current_org' ORDER BY name ASC");
					if (isset($organizationsAll) && !empty($organizationsAll)) {
						foreach ($organizationsAll as $org) {
							$organizations[$org['organizations']['id']] = html_entity_decode( ($org['organizations']['name'] ));
						}
					}
			}
		}
		else{
			$organizationsAll = $this->Skill->query("SELECT id, name FROM organizations ORDER BY name ASC");
			// pr($organizationsAll,1);
			if (isset($organizationsAll) && !empty($organizationsAll)) {
				foreach ($organizationsAll as $org) {
					$organizations[$org['organizations']['id']] = html_entity_decode( ($org['organizations']['name'] ));
				}
			}
		}
		$this->set("organizations", $organizations);

		$exuser[] = $id;
		$q = $this->User->query("SELECT reports_to_id FROM user_details WHERE user_id = $id");
		if(isset($q[0]['user_details']['reports_to_id']) && !empty($q[0]['user_details']['reports_to_id'])){
			$exuser[] = $q[0]['user_details']['reports_to_id'];
		}
		$exuser = implode(',', $exuser);

		// REPORTS TO USERS LIST
		$all_report_users = array();
		$ulist = $this->User->query("SELECT ud.user_id, ud.first_name, ud.last_name FROM users u INNER JOIN user_details ud ON ud.user_id = u.id WHERE u.role_id = 2 AND u.status = 1 AND u.is_activated = 1 AND u.id <> $id ORDER BY ud.first_name, ud.last_name ASC");
		// pr($ulist,1);
		if (isset($ulist) && !empty($ulist)) {
			foreach ($ulist as $u) {
				$all_report_users[$u['ud']['user_id']] = $u['ud']['first_name'] . ' ' . $u['ud']['last_name'];
			}
		}
		$this->set("all_report_users", $all_report_users);

		// DOTTED LINE USERS LIST
		$all_users = array();
		$ulist = $this->User->query("SELECT ud.user_id, ud.first_name, ud.last_name FROM users u INNER JOIN user_details ud ON ud.user_id = u.id WHERE u.role_id = 2 AND u.status = 1 AND u.is_activated = 1 AND u.id NOT IN($exuser) ORDER BY ud.first_name, ud.last_name ASC");
		// pr($ulist,1);
		if (isset($ulist) && !empty($ulist)) {
			foreach ($ulist as $u) {
				$all_users[$u['ud']['user_id']] = $u['ud']['first_name'] . ' ' . $u['ud']['last_name'];
			}
		}
		$this->set("all_users", $all_users);

		$this->UserInterest->recursive = -1;
		$interestlists = $this->UserInterest->find('all',
			array('conditions' => array('UserInterest.user_id' => $id), 'order' => 'UserInterest.id DESC')
		);

		$this->loadModel('UserPassword');

		if (isset($skills) && !empty($skills)) {
			$this->set("skills", $skills);
		}
		if (isset($interestlists) && !empty($interestlists)) {
			$this->set("interestlists", $interestlists);
			$this->set("interestcount", count($interestlists));
		}

		//pr($skills); die;

		if (!$this->User->exists()) {
			$this->Session->setFlash(__('Invalid User.'), 'error');
			$this->redirect(array('action' => 'index'));
		}


		if ($this->request->is('post') || $this->request->is('put')) {
			// pr($this->request->data ); die;
			if (isset($this->request->data['User']['email']) && !empty($this->request->data['User']['email'])) {
				$userDomainName = explode("@", $this->request->data['User']['email']);
				$userDomain = explode(".", $userDomainName[1]);

				$checkOrganisationDomain = $this->checkOrgDomainLogin($userDomain[0]);

				if (isset($checkOrganisationDomain) && !empty($checkOrganisationDomain)) {

					$_SESSION['data'] = $this->request->data;
					$this->request->data['UserDetail']['org_id'] = $this->checkOrgDomain($userDomain[0]);

					$orgPasswordPolicy = $this->OrgPassPolicy->find('first', array('conditions' => array('OrgPassPolicy.org_id' => $this->request->data['UserDetail']['org_id'])));

					//pr($orgPasswordPolicy); die;
					if (isset($orgPasswordPolicy['OrgPassPolicy']) && !empty($orgPasswordPolicy['OrgPassPolicy'])) {
						if (strlen($this->request->data['User']['password']) < $orgPasswordPolicy['OrgPassPolicy']['min_lenght']) {

							$this->request->data['error']['password'] = "Password should be at least " . $orgPasswordPolicy['OrgPassPolicy']['min_lenght'] . " character";

							$_SESSION['data'] = $this->request->data;
							$this->redirect(array('controller' => 'users', 'action' => 'myaccountedit', $id));

						} else if ($orgPasswordPolicy['OrgPassPolicy']['numeric_char'] == 1) {

							if (!preg_match('/[0-9]/', $this->request->data['User']['password'])) {

								$this->request->data['error']['password'] = "Password should have minimum one numeric character";

								$_SESSION['data'] = $this->request->data;
								$this->redirect(array('controller' => 'users', 'action' => 'myaccountedit', $id));

							}

						} else if ($orgPasswordPolicy['OrgPassPolicy']['alph_char'] == 1) {

							if (!preg_match('/[a-zA-Z]/', $this->request->data['User']['password'])) {

								$this->request->data['error']['password'] = "Password should have minimum one alpha character";

								$_SESSION['data'] = $this->request->data;
								$this->redirect(array('controller' => 'users', 'action' => 'myaccountedit', $id));

							}
						}
					}
				}
// UserSkill
				/* SKILL ADD/EDIT SECTION STARTS */
				if (isset($this->request->data['skill_pdf_ids']) && !empty($this->request->data['skill_pdf_ids'])) {
					$skillArray = explode(",", $this->request->data['skill_pdf_ids']);

					if (isset($skillArray) && !empty($skillArray) && count($skillArray) > 0) {
						// update skill pdf update status flag
						foreach ($skillArray as $listskill) {
							$this->request->data['Skill']['Skill'][] = $listskill;
							// User Skill pdf save
							$this->SkillPdf->query("UPDATE skill_pdfs SET upload_status=1 WHERE user_id=$id AND skill_id=$listskill");
							$getDetailData = $this->SkillDetail->find('first', ['conditions' => ['SkillDetail.user_id' => $id, 'SkillDetail.skill_id' => $listskill]]);
							if(!isset($getDetailData) || empty($getDetailData)){
								$skillDetailSave['SkillDetail'] = ['user_id' => $id, 'skill_id' => $listskill, 'user_level' => 'Beginner', 'user_experience' => '1'];
								$this->SkillDetail->save($skillDetailSave);
								$this->SkillDetail->id = null;
							}
						}
					}

					if (isset($skillArray) && !empty($skillArray) && count($skillArray) > 0) {
						// delete extra skills pdf files
						$extraSkill = $this->SkillPdf->find('all', array('conditions' => array("SkillPdf.user_id " => $id, "NOT" => array("SkillPdf.skill_id" => $skillArray))));

						if (isset($extraSkill) && !empty($extraSkill)) {
							foreach ($extraSkill as $listskillext) {
								if (file_exists(SKILL_PDF_PATH . $this->Session->read("Auth.User.id") . DS . $listskillext['SkillPdf']['pdf_name'])) {
									unlink(SKILL_PDF_PATH . $this->Session->read("Auth.User.id") . DS . $listskillext['SkillPdf']['pdf_name']);
								}
								$this->SkillPdf->delete(array('SkillPdf.id' => $listskillext['SkillPdf']['id']));
							}
						}
					}

				} else {

					// delete all files if SKILL array is empty
					$folder = SKILL_PDF_PATH . $this->Session->read("Auth.User.id");
					$files = glob($folder . '/*');
					foreach ($files as $file) {
						if (is_file($file)) {
							unlink($file);
						}
					}
					$this->SkillPdf->deleteAll(array('SkillPdf.user_id' => $this->Session->read("Auth.User.id")));
					$this->request->data['Skill']['Skill'][] = array();
				}

				// User Skill pdf delete which have upload_status 0
				$getskills = $this->SkillPdf->find('all', array('conditions' => array('SkillPdf.user_id' => $id, 'SkillPdf.upload_status' => 0)));
				if (isset($getskills) && !empty($getskills)) {
					foreach ($getskills as $listskill) {
						if (file_exists(SKILL_PDF_PATH . $this->Session->read("Auth.User.id") . DS . $listskill['SkillPdf']['pdf_name'])) {
							unlink(SKILL_PDF_PATH . $this->Session->read("Auth.User.id") . DS . $listskill['SkillPdf']['pdf_name']);
						}
						$this->SkillPdf->delete(array('SkillPdf.id' => $listskill['SkillPdf']['id']));
					}
				}
				/* SKILL ADD/EDIT SECTION ENDS */


				/* SUBJECT ADD/EDIT SECTION STARTS */
				$this->loadModel('Subject');
				$this->loadModel('SubjectDetail');
				$this->loadModel('SubjectPdf');
				if (isset($this->request->data['subject_pdf_ids']) && !empty($this->request->data['subject_pdf_ids'])) {
					$subjectArray = explode(",", $this->request->data['subject_pdf_ids']);

					if (isset($subjectArray) && !empty($subjectArray) && count($subjectArray) > 0) {
						// update SUBJECT pdf update status flag
						foreach ($subjectArray as $listSubject) {
							$this->request->data['Subject']['Subject'][] = $listSubject;
							// User SUBJECT pdf save
							$this->SubjectPdf->query("UPDATE subject_pdfs SET upload_status=1 WHERE user_id=$id AND subject_id=$listSubject");
							$getDetailData = $this->SubjectDetail->find('first', ['conditions' => ['SubjectDetail.user_id' => $id, 'SubjectDetail.subject_id' => $listSubject]]);
							if(!isset($getDetailData) || empty($getDetailData)){
								$subDetailSave['SubjectDetail'] = ['user_id' => $id, 'subject_id' => $listSubject, 'user_level' => 'Beginner', 'user_experience' => '1'];
								$this->SubjectDetail->save($subDetailSave);
								$this->SubjectDetail->id = null;
							}
						}
					}

					if (isset($subjectArray) && !empty($subjectArray) && count($subjectArray) > 0) {
						// delete extra SUBJECT pdf files
						$extraSubjects = $this->SubjectPdf->find('all', array('conditions' => array("SubjectPdf.user_id " => $id, "NOT" => array("SubjectPdf.subject_id" => $subjectArray))));

						if (isset($extraSubjects) && !empty($extraSubjects)) {
							foreach ($extraSubjects as $listSubject) {
								if (file_exists(SUBJECT_PDF_PATH . $this->Session->read("Auth.User.id") . DS . $listSubject['SubjectPdf']['pdf_name'])) {
									unlink(SUBJECT_PDF_PATH . $this->Session->read("Auth.User.id") . DS . $listSubject['SubjectPdf']['pdf_name']);
								}
								$this->SubjectPdf->delete(array('SubjectPdf.id' => $listSubject['SubjectPdf']['id']));
							}
						}
					}

				} else {

					// delete all files if SUBJECT array is empty
					$folder = SUBJECT_PDF_PATH . $this->Session->read("Auth.User.id");
					$files = glob($folder . '/*');
					foreach ($files as $file) {
						if (is_file($file)) {
							unlink($file);
						}
					}
					$this->SubjectPdf->deleteAll(array('SubjectPdf.user_id' => $this->Session->read("Auth.User.id")));
					$this->request->data['Subject']['Subject'][] = array();
				}

				// User Subject pdf delete which have upload_status 0
				$getSubjects = $this->SubjectPdf->find('all', array('conditions' => array('SubjectPdf.user_id' => $id, 'SubjectPdf.upload_status' => 0)));
				if (isset($getSubjects) && !empty($getSubjects)) {
					foreach ($getSubjects as $listSubject) {
						if (file_exists(SUBJECT_PDF_PATH . $this->Session->read("Auth.User.id") . DS . $listSubject['SubjectPdf']['pdf_name'])) {
							unlink(SUBJECT_PDF_PATH . $this->Session->read("Auth.User.id") . DS . $listSubject['SubjectPdf']['pdf_name']);
						}
						$this->SubjectPdf->delete(array('SubjectPdf.id' => $listSubject['SubjectPdf']['id']));
					}
				}
				/* SUBJECT ADD/EDIT SECTION ENDS */


				/* DOMAIN ADD/EDIT SECTION STARTS */
				$this->loadModel('Domain');
				$this->loadModel('DomainDetail');
				$this->loadModel('DomainPdf');
				if (isset($this->request->data['domain_pdf_ids']) && !empty($this->request->data['domain_pdf_ids'])) {
					$DomainArray = explode(",", $this->request->data['domain_pdf_ids']);

					if (isset($DomainArray) && !empty($DomainArray) && count($DomainArray) > 0) {
						// update DOMAIN pdf update status flag
						foreach ($DomainArray as $listDomain) {
							$this->request->data['Domain']['Domain'][] = $listDomain;
							// User DOMAIN pdf save
							$this->DomainPdf->query("UPDATE domain_pdfs SET upload_status=1 WHERE user_id=$id AND domain_id=$listDomain");
							$getDetailData = $this->DomainDetail->find('first', ['conditions' => ['DomainDetail.user_id' => $id, 'DomainDetail.domain_id' => $listDomain]]);
							if(!isset($getDetailData) || empty($getDetailData)){
								$domainDetailSave['DomainDetail'] = ['user_id' => $id, 'domain_id' => $listDomain, 'user_level' => 'Beginner', 'user_experience' => '1'];
								$this->DomainDetail->save($domainDetailSave);
								$this->DomainDetail->id = null;
							}
						}
					}

					if (isset($DomainArray) && !empty($DomainArray) && count($DomainArray) > 0) {
						// delete extra DOMAIN pdf files
						$extraDomain = $this->DomainPdf->find('all', array('conditions' => array("DomainPdf.user_id " => $id, "NOT" => array("DomainPdf.domain_id" => $DomainArray))));

						if (isset($extraDomain) && !empty($extraDomain)) {
							foreach ($extraDomain as $listDomain) {
								if (file_exists(DOMAIN_PDF_PATH . $this->Session->read("Auth.User.id") . DS . $listDomain['DomainPdf']['pdf_name'])) {
									unlink(DOMAIN_PDF_PATH . $this->Session->read("Auth.User.id") . DS . $listDomain['DomainPdf']['pdf_name']);
								}
								$this->DomainPdf->delete(array('DomainPdf.id' => $listDomain['DomainPdf']['id']));
							}
						}
					}

				} else {

					// delete all files if DOMAIN array is empty
					$folder = DOMAIN_PDF_PATH . $this->Session->read("Auth.User.id");
					$files = glob($folder . '/*');
					foreach ($files as $file) {
						if (is_file($file)) {
							unlink($file);
						}
					}
					$this->DomainPdf->deleteAll(array('DomainPdf.user_id' => $this->Session->read("Auth.User.id")));
					$this->request->data['Domain']['Domain'][] = array();
				}

				// User DOMAIN pdf delete which have upload_status 0
				$getDomains = $this->DomainPdf->find('all', array('conditions' => array('DomainPdf.user_id' => $id, 'DomainPdf.upload_status' => 0)));
				if (isset($getDomains) && !empty($getDomains)) {
					foreach ($getDomains as $listDomain) {
						if (file_exists(DOMAIN_PDF_PATH . $this->Session->read("Auth.User.id") . DS . $listDomain['DomainPdf']['pdf_name'])) {
							unlink(DOMAIN_PDF_PATH . $this->Session->read("Auth.User.id") . DS . $listDomain['DomainPdf']['pdf_name']);
						}
						$this->DomainPdf->delete(array('DomainPdf.id' => $listDomain['DomainPdf']['id']));
					}
				}
				/* DOMAIN ADD/EDIT SECTION ENDS */

				/*===============================================================================*/
				$UserDottedLine = (isset($this->request->data['UserDottedLine']) && !empty($this->request->data['UserDottedLine'])) ? $this->request->data['UserDottedLine'] : [];
				$UserDottedLineUsers = (isset($this->request->data['UserDottedLine']['dotted_user_id']) && !empty($this->request->data['UserDottedLine']['dotted_user_id'])) ? $this->request->data['UserDottedLine']['dotted_user_id'] : [];
				unset($this->request->data['UserDottedLine']);
				unset($this->request->data['dotted_user_id']);
				// pr($this->request->data,1);
				if ($this->User->saveAssociated($this->request->data, array('deep' => true))) {

					$users = $this->User->find('first', array('conditions' => array('User.id' => $id)));
					$userDetails = $this->UserDetail->find('first', array('conditions' => array('UserDetail.id' => $detid)));
					$users['User']['UserDetail'] = $userDetails['UserDetail'];

					if (isset($this->request->data['User']['password']) && !empty($this->request->data['User']['password'])) {
						$this->changePasswordEmail($userDetails, $users['User']['email']);
					}

					if (isset($UserDottedLine['dotted_user_id']) && !empty($UserDottedLine['dotted_user_id'])) {
						// Delete previous entries
						// pr($UserDottedLine, 1);
						$this->User->query("DELETE FROM user_dotted_lines WHERE user_id = $id");
						$dotted_user_id = $UserDottedLine['dotted_user_id'];
						$qry = "INSERT INTO `user_dotted_lines` (`user_id`, `dotted_user_id`) VALUES ";
						$qry_arr = [];
	    				foreach ($dotted_user_id as $key => $value) {
	    					$qry_arr[] = "('$id', '$value')";
	    				}
	    				$qry .= implode(' ,', $qry_arr);
	    				$this->User->query($qry);
					}
					if(!isset($UserDottedLineUsers) || empty($UserDottedLineUsers)) {
						$this->User->query("DELETE FROM user_dotted_lines WHERE user_id = $id");
					}

					$redirect = (isset($this->request->data['UserRefer']['refer']) && !empty($this->request->data['UserRefer']['refer'])) ? $this->request->data['UserRefer']['refer'] : '';

					if ( CHAT_VERSION == 'old' ) {

						$HttpSocket = new HttpSocket([
							'ssl_verify_host' => false,
							'ssl_verify_peer_name' => false,
							'ssl_verify_peer' => false,
						]);
						$results = $HttpSocket->get(CHATURL . '/user/' . $this->Auth->user('id') . '/update');

					} else {
						// UPDATE USER DATA TO MONGO
						$this->Users->addUser($id, true);
					}

					if (isset($redirect) && !empty($redirect)) {
						$this->redirect($redirect);
					} else {
						// uncomment after all done
						if($this->Session->read("Auth.User.role_id") == 3){
							$this->redirect(array('action' => 'manage_users', 'controller' => 'organisations'));
						}
						$this->redirect(array('action' => 'lists', 'controller' => 'projects'));
					}
				}
			}
			else{
				$this->User->validationErrors['email'] = "Email is required.";
			}

		} else {

			unset($_SESSION['data']);

			// $this->request->data = $this->User->read(null, $id);
			$this->User->unbindModel(array('hasMany' => array('ProjectPermission', 'WorkspacePermission', 'ElementPermission', 'UserProject', 'UserSetting', 'UserPassword'), 'hasAndBelongsToMany' => ['Skill', 'Subject', 'Domain'], 'hasOne' => ['OrganisationUser']));
			$this->request->data = $this->User->findById($id);
			if (isset($this->request->data['User']['password'])) {
				unset($this->request->data['User']['password']);
			}

			$selectedDotted = [];
			$dottedUsers = $this->User->query("SELECT dotted_user_id FROM user_dotted_lines WHERE user_id = $id");
			if(isset($dottedUsers) && !empty($dottedUsers)){
				$selectedDotted = Set::extract($dottedUsers, '/user_dotted_lines/dotted_user_id');
			}
			$this->request->data['UserDottedLine']['dotted_user_id'] = $selectedDotted;


			// User Skill pdf delete which have upload_status 0
			$getskills = $this->SkillPdf->find('all', array('conditions' => array('SkillPdf.user_id' => $id, 'SkillPdf.upload_status' => 0)));
			if (isset($getskills) && !empty($getskills)) {
				foreach ($getskills as $listskill) {
					if (file_exists(SKILL_PDF_PATH . $this->Session->read("Auth.User.id") . DS . $listskill['SkillPdf']['pdf_name'])) {
						unlink(SKILL_PDF_PATH . $this->Session->read("Auth.User.id") . DS . $listskill['SkillPdf']['pdf_name']);
					}
					$this->SkillPdf->delete(array('SkillPdf.id' => $listskill['SkillPdf']['id']));
				}
			}

			$this->loadModel('SubjectPdf');
			$this->loadModel('DomainPdf');

			// User Subject pdf delete which have upload_status 0
			$getSubjects = $this->SubjectPdf->find('all', array('conditions' => array('SubjectPdf.user_id' => $id, 'SubjectPdf.upload_status' => 0)));
			if (isset($getSubjects) && !empty($getSubjects)) {
				foreach ($getSubjects as $listSubject) {
					if (file_exists(SUBJECT_PDF_PATH . $this->Session->read("Auth.User.id") . DS . $listSubject['SubjectPdf']['pdf_name'])) {
						unlink(SUBJECT_PDF_PATH . $this->Session->read("Auth.User.id") . DS . $listSubject['SubjectPdf']['pdf_name']);
					}
					$this->SubjectPdf->delete(array('SubjectPdf.id' => $listSubject['SubjectPdf']['id']));
				}
			}


			// User DOMAIN pdf delete which have upload_status 0
			$getDomains = $this->DomainPdf->find('all', array('conditions' => array('DomainPdf.user_id' => $id, 'DomainPdf.upload_status' => 0)));
			if (isset($getDomains) && !empty($getDomains)) {
				foreach ($getDomains as $listDomain) {
					if (file_exists(DOMAIN_PDF_PATH . $this->Session->read("Auth.User.id") . DS . $listDomain['DomainPdf']['pdf_name'])) {
						unlink(DOMAIN_PDF_PATH . $this->Session->read("Auth.User.id") . DS . $listDomain['DomainPdf']['pdf_name']);
					}
					$this->DomainPdf->delete(array('DomainPdf.id' => $listDomain['DomainPdf']['id']));
				}
			}

		}
	}

	public function is_dir_empty($dir) {
	  if (!is_readable($dir)) return null;
	  $handle = opendir($dir);
	  while (false !== ($entry = readdir($handle))) {
		if ($entry !== '.' && $entry !== '..') { // <-- better use strict comparison here
		  closedir($handle); // <-- always clean up! Close the directory stream
		  return false;
		}
	  }
	  closedir($handle); // <-- always clean up! Close the directory stream
	  return true;
	}

	//== User Change Password Email =========================
	public function changePasswordEmail($useData = null, $userEmail = null) {

		if (isset($useData) && isset($userEmail) && !empty($userEmail)) {
			$name = $useData['UserDetail']['first_name'] . ' ' . $useData['UserDetail']['last_name'];

			$email = new CakeEmail();
			$email->config('Smtp');
			//$email->config('Smtp');
			// $email->from(array(ADMIN_EMAIL => SITENAME));
			$email->from(array(ADMIN_FROM_EMAIL => SITENAME));
			$email->to($userEmail);
			$email->subject(SITENAME . ': Password change');
			$email->template('password_changed');
			$email->emailFormat('html');
			$email->viewVars(array('name' => $name));
			return $email->send();
		}
	}

	public function orgaccountedit($id = null) {
		$this->layout = 'inner';
		$id = $this->Session->read('Auth.User.id');
		$detid = $this->Session->read('Auth.User.UserDetail.id');
		// pr($this->Session->read('Auth')); die;

		$this->set('title_for_layout', __('Edit Profile', true));
		$this->User->id = $id;
		$skills = array();
		$skillsAll = $this->Skill->query("SELECT * FROM skill");

		$this->loadModel('UserPassword');

		//pr($skills); die;

		if (!$this->User->exists()) {
			$this->Session->setFlash(__('Invalid User.'), 'error');
			$this->redirect(array('action' => 'index'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {

			//pr($this->request->data); die;

			if ($this->User->saveAssociated($this->request->data, array('deep' => true))) {

				$users = $this->User->find('first', array('conditions' => array('User.id' => $id)));
				$userDetails = $this->UserDetail->find('first', array('conditions' => array('UserDetail.id' => $detid)));
				$users['User']['UserDetail'] = $userDetails['UserDetail'];

				$this->Session->setFlash(__('Your profile details has been updated successfully.'), 'success');

				$redirect = (isset($this->request->data['UserRefer']['refer']) && !empty($this->request->data['UserRefer']['refer'])) ? $this->request->data['UserRefer']['refer'] : '';

				if ($this->live_setting == true) {
					//$HttpSocket = new HttpSocket();

					$HttpSocket = new HttpSocket([
						'ssl_verify_host' => false,
						'ssl_verify_peer_name' => false,
						'ssl_verify_peer' => false,
					]);

					$results = $HttpSocket->get(CHATURL . '/user/' . $this->Auth->user('id') . '/update');

				}

				if (isset($redirect) && !empty($redirect)) {
					$this->redirect($redirect);
				} /* else {
					$this->redirect(array('action' => 'lists', 'controller' => 'projects'));
				} */
			}
		} else {
			unset($_SESSION['data']);
			$this->request->data = $this->User->read(null, $id);
			if (isset($this->request->data['User']['password'])) {
				unset($this->request->data['User']['password']);
			}
		}
	}

	public function changepassword($id = null) {
		$this->layout = 'inner';
		$id = $this->Session->read('Auth.User.id');
		$detid = $this->Session->read('Auth.User.UserDetail.id');

		$this->set('title_for_layout', __('Change Password', true));
		$this->User->id = $id;
		$this->request->data['User']['id'] = $id;

		if (  !dbExists('User', $id) ) {
			$this->redirect(array('controller' => 'users', 'action' => 'logout',$id));
		}

		if ($this->request->is('post') || $this->request->is('put')) {

			unset($this->request->data['userid']);
			unset($this->request->data['email']);
			unset($this->request->data['password']);
			unset($this->request->data['organisation_id']);
			unset($this->request->data['project_id']);
			unset($this->request->data['msg']);
			//============= Check Old Password ===================================

			if ($this->User->find('count', array('conditions' => array('User.password' => AuthComponent::password(trim($this->request->data['User']['current_password'])), 'User.id' => $id)))) {

				$userEmail = $this->Session->read('Auth.User.email');
				//Updated 21th Nov 2016
				$userDomainName = explode("@", $userEmail);
				//$userDomain = explode(".", $userDomainName[1]);
				$userDomain = $userDomainName[1];

				$checkOrganisationDomain = $this->checkOrgDomain($userDomain);

				if (isset($checkOrganisationDomain) && !empty($checkOrganisationDomain)) {
					// e('if', 1);
					if (isset($_SESSION['data']) && !empty($_SESSION['data'])) {
						unset($_SESSION['data']);
					}

					$_SESSION['data'] = $this->request->data;

					$this->request->data['UserDetail']['org_id'] = $this->checkOrgDomainLogin($userDomain);

					$org_id = (isset($this->request->data['UserDetail']['org_id']) && !empty($this->request->data['UserDetail']['org_id'])) ? $this->request->data['UserDetail']['org_id'] : 1;

					$orgPasswordPolicy = $this->OrgPassPolicy->find('first', array('conditions' => array('OrgPassPolicy.org_id' => $org_id)));

					//================================================================================
					if (isset($orgPasswordPolicy['OrgPassPolicy']['pass_repeat'])) {
						$previousPassword = $this->checkUserPrePassword($id, $orgPasswordPolicy['OrgPassPolicy']['pass_repeat']);
					}

					if (empty(trim($this->request->data['User']['password']))) {

						$this->User->validationErrors['password'] = "New Password is required.";

					} else if (empty(trim($this->request->data['User']['cpassword']))) {

						$this->User->validationErrors['cpassword'] = "Confirm Password is required.";

					} else {

						if (isset($orgPasswordPolicy['OrgPassPolicy']['min_lenght']) && strlen($this->request->data['User']['password']) < $orgPasswordPolicy['OrgPassPolicy']['min_lenght']) {

							if (isset($_SESSION['data']) && !empty($_SESSION['data'])) {

								unset($_SESSION['data']);

							}

							$this->User->validationErrors['password'] = "Password should be at least " . $orgPasswordPolicy['OrgPassPolicy']['min_lenght'] . " characters";

						}

						if (!isset($orgPasswordPolicy['OrgPassPolicy']['min_lenght']) && strlen($this->request->data['User']['password']) < 4) {

							$this->User->validationErrors['password'] = "Password should be at least 4 characters";

						}

						if (isset($orgPasswordPolicy['OrgPassPolicy']['numeric_char']) && $orgPasswordPolicy['OrgPassPolicy']['numeric_char'] == 1) {

							if (isset($_SESSION['data']) && !empty($_SESSION['data'])) {

								unset($_SESSION['data']);
								//pr($_SESSION['data']);
							}

							if (!preg_match('/[0-9]/', $this->request->data['User']['password'])) {

								$this->User->validationErrors['password'] = "Password should have minimum one numeric character";
							}

						}

						if (isset($orgPasswordPolicy['OrgPassPolicy']['alph_char']) && $orgPasswordPolicy['OrgPassPolicy']['alph_char'] == 1) {

							if (isset($_SESSION['data']) && !empty($_SESSION['data'])) {

								unset($_SESSION['data']);
							}

							//	pr($this->request->data);die;
							if (!preg_match('/[a-zA-Z]/', $this->request->data['User']['password'])) {

								$this->User->validationErrors['password'] = "Password should have minimum one alpha character";

							}

						}
						$newPassword = AuthComponent::password(trim($this->request->data['User']['password']));

						if (isset($orgPasswordPolicy['OrgPassPolicy']['pass_repeat'])) {
							if (in_array($newPassword, $previousPassword)) {

								// $this->User->validationErrors['password'] = "Password should not be from your last " . $orgPasswordPolicy['OrgPassPolicy']['pass_repeat'] . " passwords";
								$this->User->validationErrors['password'] = "New Password cannot be this previous password";

							}
						}

						if (isset($orgPasswordPolicy['OrgPassPolicy']['special_char']) && $orgPasswordPolicy['OrgPassPolicy']['special_char'] == 1) {

							if (!preg_match('/\W/', $this->request->data['User']['password'])) {

								$this->User->validationErrors['password'] = 'Password should have minimum one special character.';

							}
						}

						if (isset($orgPasswordPolicy['OrgPassPolicy']['special_char']) && $orgPasswordPolicy['OrgPassPolicy']['caps_char'] == 1) {

							if (!preg_match('/[A-Z]/', $this->request->data['User']['password'])) {

								$this->User->validationErrors['password'] = 'Password should have minimum one capital character.';

							}
						}
					}
					//================================================================================

					if ($this->User->validates(array('fieldList' => array('password', 'cpassword')))) {
						if(UA){
							$this->UserDetail->id = $detid;
							$this->UserDetail->saveField('org_password', $this->data['User']['password']);
						}

						$pans = AuthComponent::password($this->data['User']['password']);
						$this->request->data['UserPassword']['password'] = $pans;

						unset($this->request->data['UserDetail']);
						$this->request->data['User']['change_password_sts'] = 0;
						if ($this->User->saveAssociated($this->request->data, array('deep' => true))) {

							$this->request->data['UserPassword']['user_id'] = $id;

							$this->UserPassword->save($this->request->data);

							$users = $this->User->find('first', array('conditions' => array('User.id' => $id)));
							$userDetails = $this->UserDetail->find('first', array('conditions' => array('UserDetail.id' => $detid)));

							$this->changePasswordEmail($userDetails, $users['User']['email']);

							//$this->Session->setFlash(__('Your password has been updated successfully.'), 'success');
							unset($_SESSION['data']);
							$this->redirect(array('action' => 'lists', 'controller' => 'projects'));
						}

					}

				} else {



					if ($this->User->validates(array('fieldList' => array('password', 'cpassword')))) {
						if(UA){
							$this->UserDetail->id = $detid;
							$this->UserDetail->saveField('org_password', $this->data['User']['password']);
						}

						$pans = AuthComponent::password($this->data['User']['password']);
						$this->request->data['UserPassword']['password'] = $pans;
						unset($this->request->data['UserDetail']);
						if ($this->User->saveAssociated($this->request->data, array('deep' => true))) {

							$this->request->data['UserPassword']['user_id'] = $id;

							$this->UserPassword->save($this->request->data);

							$users = $this->User->find('first', array('conditions' => array('User.id' => $id)));
							$userDetails = $this->UserDetail->find('first', array('conditions' => array('UserDetail.id' => $detid)));

							$this->changePasswordEmail($userDetails, $users['User']['email']);

							//$this->Session->setFlash(__('Your password has been updated successfully.'), 'success');
							unset($_SESSION['data']);
							$this->redirect(array('action' => 'lists', 'controller' => 'projects'));
						}
					}
				}

			} else {
				//e('out', 1);
				unset($this->request->data['userid']);
				unset($this->request->data['email']);
				unset($this->request->data['password']);
				unset($this->request->data['organisation_id']);
				unset($this->request->data['project_id']);
				unset($this->request->data['msg']);
				//pr($this->request->data); die;
				unset($this->request->data['UserDetail']);
				$this->User->saveAssociated($this->request->data);
				//pr($this->User->validationErrors);die;
				//	die('not right');
			}

			//====================================================================

		} else {

			//$this->request->data = $this->User->read(null, $id);
			if (isset($this->request->data['User']['password'])) {
				unset($this->request->data['User']['password']);
			}

		}
	}

	public function remove_profile_pic() {
		//data[UserDetail][profile_pic] data[UserDetail][document_pic]

		if ($this->request->is('ajax')) {
			$this->layout = false;
			$this->autoRender = false;

			$id = (isset($this->data['uid']) && !empty($this->data['uid'])) ? $this->data['uid'] : $this->Session->read('Auth.User.id');
			$userDetail = $this->objView->loadHelper('ViewModel')->get_user($id, null, 1);

			$this->User->id = $id;
			$this->UserDetail->id = $userDetail['UserDetail']['id'];

			$detid = $userDetail['UserDetail']['id'];
			if ( $this->data['id'] == 'UserDetailProfilePic') {
				$this->request->data['UserDetail']['profile_pic'] = "";
			} else if (  $this->data['id'] == 'UserDetailDocumentPic') {
				$this->request->data['UserDetail']['document_pic'] = '';
			}
			// $userDetail = $this->objView->loadHelper('ViewModel')->get_user($id, null, 1);

			if ($this->UserDetail->save($this->request->data)) {
				if (  $this->data['id'] == 'UserDetailProfilePic') {

					//$profile = $this->Session->read('Auth.User.UserDetail.profile_pic');
					$profile =  $userDetail['UserDetail']['profile_pic'];
					$profiles = SITEURL . USER_PIC_PATH . $profile;
					if (!empty($profile) && file_exists(USER_PIC_PATH . $profile)) {
						$profiles = WWW_ROOT . USER_PIC_PATH . $profile;
						unlink($profiles);

					}
					$this->UserDetail->query("UPDATE user_details SET profile_pic = NULL WHERE id=".$id);
				} else if ( $this->data['id'] == 'UserDetailDocumentPic') {
					$docimg =  $userDetail['UserDetail']['document_pic'];
					//$docimg = $this->Session->read('Auth.User.UserDetail.document_pic');
					$docimgs = SITEURL . USER_PIC_PATH . $docimg;
					if (!empty($docimg) && file_exists(USER_PIC_PATH . $docimg)) {
						$docimgs = WWW_ROOT . USER_PIC_PATH . $docimg;
						unlink($docimgs);

					}
				}

				if (CHAT_VERSION == 'old') {
					$HttpSocket = new HttpSocket([
						'ssl_verify_host' => false,
						'ssl_verify_peer_name' => false,
						'ssl_verify_peer' => false,
					]);
					$results = $HttpSocket->get(CHATURL . '/user/' . $id . '/update');
				}
				else{
					$sql = "SELECT ud.profile_pic as thumb FROM users as u LEFT JOIN user_details as ud ON u.id=ud.user_id WHERE u.id =" . $id;

					$user_detail = $this->User->query($sql);

					// UPDATE USER DATA TO MONGO
					$this->Users->updateUserImage($id, $user_detail, true);
				}

				$users = $this->User->find('first', array('conditions' => array('User.id' => $id)));
				$userDetails = $this->UserDetail->find('first', array('conditions' => array('UserDetail.id' => $detid)));
				$users['User']['UserDetail'] = $userDetails['UserDetail'];

				//$this->Session->write('Auth', $users);
				// $this->Session->setFlash(__('The Profile has been updated successfully.'), 'success');
				die('success');
			}
		}
	}

	public function profile($uid = null) {
		$detid = $this->Session->read('Auth.User.UserDetail.id');
		App::import("Model", "User");
		$this->User = new User();

		$this->User->id = $id = (isset($uid) && !empty($uid)) ? $uid : $this->Session->read('Auth.User.id');
		// pr($uid, 1);
		if (!$this->User->exists()) {
			$this->Session->setFlash(__('Invalid User Profile.'), 'error');
			die('error');
		}

		if ($this->request->is('post') || $this->request->is('put')) {
			$post = $this->request->data;
			// pr($post, 1);

			if(isset($this->request->data['UserDetail']['urlImage']) && !empty($this->request->data['UserDetail']['urlImage'])){

				if (isset($this->request->data['UserDetail'])) {
					$files = $this->UserDetail->find('first', array('conditions' => array( 'UserDetail.user_id' => $this->request->data['User']['id']), 'fields' => array('UserDetail.profile_pic')));

					@unlink(WWW_ROOT .USER_PIC_PATH . DS . $files['UserDetail']['profile_pic']);
				}

				$img = $this->request->data['UserDetail']['urlImage'];
				$img = str_replace('data:image/png;base64,', '', $img);
				$img = str_replace('data:image/jpeg;base64,', '', $img);
				$img = str_replace(' ', '+', $img);
				$data = base64_decode($img);

				$file = USER_PIC_PATH.time().".png";
				$fnam = time().".png";
				$success = file_put_contents($file, $data);

				$this->request->data['UserDetail']['profile_pic'] = $fnam;
			}

			if ($this->UserDetail->save($this->request->data)) {

				if (CHAT_VERSION == 'old') {
					$HttpSocket = new HttpSocket([
						'ssl_verify_host' => false,
						'ssl_verify_peer_name' => false,
						'ssl_verify_peer' => false,
					]);
					$results = $HttpSocket->get(CHATURL . '/user/' . $this->Auth->user('id') . '/update');
				}
				else{
					$sql = "SELECT ud.profile_pic as thumb FROM users as u LEFT JOIN user_details as ud ON u.id=ud.user_id WHERE u.id =" . $id;

					$user_detail = $this->User->query($sql);

					// UPDATE USER DATA TO MONGO
					$this->Users->updateUserImage($id, $user_detail, true);
				}

				$users = $this->User->find('first', array('conditions' => array('User.id' => $id)));
				$userDetails = $this->UserDetail->find('first', array('conditions' => array('UserDetail.id' => $detid)));
				if (isset($userDetails['UserDetail']) && !empty($userDetails['UserDetail'])) {
					$users['User']['UserDetail'] = $userDetails['UserDetail'];
				}
				die('success');
			}
		} else {
			$this->request->data = $this->User->read(null, $id);
			if (isset($this->request->data['User']['password'])) {
				unset($this->request->data['User']['password']);
			}
		}
		$this->set('uid', $uid);
	}

	public function save_profile_image() {

		if ($this->request->isAjax()) {
			$response = ['success' => false];
			$this->layout = false;
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				// pr($post, 1);
				$id = (isset($post['uid']) && !empty($post['uid'])) ? $post['uid'] : $this->Session->read('Auth.User.id');
				$save_data = [];

				if(isset($post['image_type']) && !empty($post['image_type'])){
					if($post['image_type'] == 'upload'){
						$img = $_FILES['profile_image'];
						$folder_url = WWW_ROOT . 'uploads/user_images/';
						$orgFileName = $img['name'];
						$exists_file = $folder_url . DS . $orgFileName;
						if (!empty($orgFileName)) {

							$tempFile = $img['tmp_name'];

							$unique_file_name = $this->unique_file_name($folder_url, $orgFileName);
							$targetFile = $folder_url . DS . $unique_file_name;
							$save_data['UserDetail']['profile_pic'] = $unique_file_name;
							if (move_uploaded_file($tempFile, $targetFile)) {

							}
						}
					}
					else if($post['image_type'] == 'blob'){
						$img = $post['profile_image'];
						$img = str_replace('data:image/png;base64,', '', $img);
						$img = str_replace('data:image/jpeg;base64,', '', $img);
						$img = str_replace(' ', '+', $img);
						$data = base64_decode($img);

						$file = USER_PIC_PATH.time().".png";
						$fnam = time().".png";
						$success = file_put_contents($file, $data);
						$save_data['UserDetail']['profile_pic'] = $fnam;
					}
				}

				$users = $this->UserDetail->find('first', array('conditions' => array('UserDetail.user_id' => $id), 'fields' => ['id', 'profile_pic']));
				// pr($users, 1);
				if((isset($save_data) && !empty($save_data)) && (isset($users) && !empty($users))){

					$profile =  $users['UserDetail']['profile_pic'];
					$profiles = SITEURL . USER_PIC_PATH . $profile;
					if (!empty($profile) && file_exists(USER_PIC_PATH . $profile)) {
						$profiles = WWW_ROOT . USER_PIC_PATH . $profile;
						unlink($profiles);
					}


					$this->UserDetail->id = $users['UserDetail']['id'];
					if ($this->UserDetail->save($save_data)) {

						$sql = "SELECT ud.profile_pic as thumb FROM users as u LEFT JOIN user_details as ud ON u.id=ud.user_id WHERE u.id =" . $id;
						$user_detail = $this->User->query($sql);
						// pr($user_detail,1);
						// UPDATE USER DATA TO MONGO
						$this->Users->updateUserImage($id, $user_detail, true);
						$response['success'] = true;
					}
				}

			}
			echo json_encode($response);
			exit;
		}
	}

	public function user_profile_image() {

		if ($this->request->isAjax()) {
			$response = ['success' => false, 'content' => ''];
			$this->layout = false;
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				$save_data = [];
				// pr($post, 1);

				$id =(isset($post['uid']) && !empty($post['uid'])) ? $post['uid'] : $this->Session->read('Auth.User.id');
				$sql = "SELECT ud.profile_pic as thumb FROM users as u LEFT JOIN user_details as ud ON u.id=ud.user_id WHERE u.id =" . $id;
				$user_detail = $this->User->query($sql);

				$profile_pic = (isset($user_detail[0]['ud']['thumb']) && !empty($user_detail[0]['ud']['thumb'])) ? SITEURL . 'uploads/user_images/' . $user_detail[0]['ud']['thumb'] : SITEURL . 'img/image_placeholders/logo_placeholder.gif';
				$response['content'] = $profile_pic;
				$response['success'] = true;

			}
			echo json_encode($response);
			exit;
		}
	}
	public function unique_file_name($path, $filename) {

		if ($pos = strrpos($filename, '.')) {
			$name = substr($filename, 0, $pos);
			$ext = substr($filename, $pos);
		} else {
			$name = $filename;
		}

		$newpath = $path . '/' . $filename;
		$newname = $filename;
		$counter = 0;
		while (file_exists($newpath)) {
			$newname = $name.'_'.$counter.$ext;
			$newpath = $path.'/'.$newname;
			$counter++;
		}
		return $newname;
	}
	public function admin_get_state_city() {
		$this->autoRender = false;
		$this->loadModel('State');
		$loadType = $_POST['loadType'];
		$loadId = $_POST['loadId'];
		if ($loadType == "state") {
			$sql = $this->State->find('all', array('conditions' => array('country_iso_code' => $loadId), 'fields' => array('id', 'name'), 'order' => 'name ASC'));
			if ( isset($sql) && !empty($sql) && count($sql) > 0) {
				$HTML = "";
				foreach ($sql as $key => $val) {
					$HTML .= "<option value='" . $val['State']['id'] . "'>" . $val['State']['name'] . "</option>";
				}
				echo $HTML;
			}
		} /* else {
				$sql = $this->City->find('all', array('conditions' => array('state_code' => $loadId), 'fields' => array('id', 'name'), 'order' => 'name ASC'));
				  if (count($sql) > 0) {
					$HTML = "";
				  foreach ($sql as $key => $val) {
					$HTML.="<option value='" . $val['City']['id'] . "'>" . $val['City']['name'] . "</option>";
				  }
					echo $HTML;
				  }
	          }
*/
	}

	public function checkSession() {
		//echo $session_id = $this->Session->id();die;
		/* if($this->Auth->user('id')){
	          echo $userID =  $this->Auth->user('id');	exit;
	          }else{
	          echo 0;exit;
*/
		//$this->loadModel('CakeSession');
		$session = $this->CakeSession->find('all', array('conditions' => array('id' => '294ojqb0c6hprojdvl4tcqd3d5')));
		//$session = $this->CakeSession->query("SELECT data from cake_sessions where id='294ojqb0c6hprojdvl4tcqd3d5'");
		//pr($session);die;
	}

	public function createThumbnail($user_id = null, $thumbWidth = 30) {
		$this->layout = false;
		$this->autoRender = false;
		$userDetail = $this->objView->loadHelper('ViewModel')->get_user($user_id, null, 1);
		$filename = SITEURL . 'images/placeholders/user/user_1.png';

		if (isset($userDetail) && !empty($userDetail)) {
			$profile_pic = $userDetail['UserDetail']['profile_pic'];
			if (!empty($profile_pic) && file_exists(USER_PIC_PATH . $profile_pic)) {
				$filename = SITEURL . USER_PIC_PATH . $profile_pic;
			}
		}
		$details = getimagesize($filename);
		$info = explode('.', $filename);

		$content = file_get_contents($filename);
		$srcImg = imagecreatefromstring($content);
		$thumbHeight = $details[1] * ($thumbWidth / $details[0]);
		$thumbImg = imagecreatetruecolor($thumbWidth, $thumbHeight);
		imagecopyresampled($thumbImg, $srcImg, 0, 0, 0, 0, $thumbWidth, $thumbHeight, $details[0], $details[1]);

		if ($details['mime'] == 'image/jpg' || $details['mime'] == 'image/jpeg') {
			imagejpeg($thumbImg, null, 100);
		} else if ($details['mime'] == 'image/png') {
			imagepng($thumbImg);
		} else if ($details['mime'] == 'image/gif') {
			imagegif($thumbImg);
		}

		imagedestroy($srcImg);
		header("Content-Type: " . $details['mime']);

		echo $thumbImg;
	}

	/*     * ********************* Admin Panel Functions End ************************* */



	public function authback() {

	}

	public function checkDomain($domain_name) {
		$domain = $this->OrganisationUser->find('first', array('conditions' => array('domain_name' => $domain_name)));

		if (isset($domain) && !empty($domain['OrganisationUser']) && count($domain['OrganisationUser']) > 0) {
			return $domain['OrganisationUser']['user_id'];
		}
	}

	public function checkOrgDomain($domain_name = null, $posted_org = null) {

		$conditions = array('ManageDomain.domain_name' => $domain_name, 'ManageDomain.create_account' => 1);

		if(isset($posted_org) && !empty($posted_org)) {
			$this->loadModel('OrganizationEmailDomain');
			$oed = $this->OrganizationEmailDomain->query("SELECT email_domain_id FROM organization_email_domains WHERE organization_id = $posted_org");
			if(isset($posted_org) && !empty($posted_org)) {
				$eod_id = Set::extract($oed, '{n}.organization_email_domains.email_domain_id');
				$conditions['ManageDomain.id'] = $eod_id;
			}
		}
		// pr($conditions, 1);
		$domain = $this->ManageDomain->find('all', array('conditions' => $conditions));

		if (isset($domain) && !empty($domain) ) {

			return 1;

		} else {

			return 0;
		}
	}

	public function validate_email() {

		$this->layout = 'ajax';
		$this->autoRender = false;

		$post = $this->request->data;
		$posted_org = (isset($post['orgid']) && !empty($post['orgid'])) ? $post['orgid'] : null;
		// pr($this->request->data, 1);
		if(isset($post['User']['email']) && !empty($post['User']['email'])){
			$email = $post['User']['email'];
			$userDomainName = explode("@", $email);
			$userDomain = $userDomainName[1];

			$uid = (isset($post['uid']) && !empty($post['uid'])) ? $post['uid'] : false;

			$this->User->recursive = -1;
			$checkEmail = $this->User->find('first', array('conditions' => array('User.email' => $email)));

			if (ORG_SETUP == true) {
				$checkOrganisationDomain = $this->checkOrgDomain($userDomain, $posted_org);
				// pr($checkEmail, 1);

				if (!isset($checkOrganisationDomain) || empty($checkOrganisationDomain) ) {
					echo "false";

				} else {

					if (isset($checkEmail) && !empty($checkEmail['User']['email']) && $this->Session->read('Auth.User.id') == $checkEmail['User']['id']) {
						echo "true";
					} else {

						if (isset($checkEmail) && !empty($checkEmail['User']['email']) && $checkEmail['User']['id'] == $uid) {
							echo "true";
						}
						else if (isset($checkEmail) && !empty($checkEmail['User']['email'])) {
							echo "false";
						} else {
							echo "true";
						}
					}
				}
			} else {

				if (isset($checkEmail) && !empty($checkEmail['User']['email']) && $this->Session->read('Auth.User.id') == $checkEmail['User']['id']) {

					echo "true";

				} else {
					if (isset($checkEmail) && !empty($checkEmail['User']['email']) && $checkEmail['User']['id'] == $uid) {
							echo "true";
						}
					else  if (isset($checkEmail) && !empty($checkEmail['User']['email'])) {
						echo "false";
					} else {
						echo "true";
					}

				}

			}
		}



	}

	public function checkOrgDomainLogin($domain_name = null) {

		$domain = $this->ManageDomain->find('first', array('conditions' => array('domain_name' => $domain_name, 'create_account' => 1)));

		if (isset($domain) && (!empty($domain['ManageDomain']) && count($domain['ManageDomain']) > 0)) {

			return $domain['ManageDomain']['org_id'];

		} else {

			return 0;
		}
	}

	public function admin_checkOrgdomain() {

		if ($this->request->isAjax()) {

			$this->autoRender = false;
			$this->layout = 'ajax';
			$response = ['success' => false, 'content' => null];

			$this->loadModel("OrganisationUser");

			$domainNameorg = trim(strtolower($this->request->data['domainName']));

			if (isset($domainNameorg) && !empty($domainNameorg)) {

				$checkSpecialcharAndWspc = 0;
				//if (preg_match("/^[a-zA-Z](\-?\.?[a-zA-Z0-9]+)+[a-zA-Z]$/", $domainNameorg)) {
				if (preg_match("/^[a-zA-Z](\-?[a-zA-Z0-9]+)+[a-zA-Z]$/", $domainNameorg)) {
					$checkSpecialcharAndWspc = 0;
				} else {
					$checkSpecialcharAndWspc = 1;
				}

				//echo $checkSpecialcharAndWspc;

				$cdmain = $this->OrganisationUser->find('count', array('conditions' => array('OrganisationUser.domain_name' => $domainNameorg)));

				$xmlapi = new XmlApi('127.0.0.1', CPANELUSR, CPANELPASS);
				$xmlapi->set_port(2083);
				$xmlapi->set_output('json');
				$xmlapi->set_hash("username", CPANELUSR);
				$xmlapi->password_auth(CPANELUSR, CPANELPASS);
				$xmlapi->set_debug(1);

				$subdomainsList = $xmlapi->api2_query(CPANELUSR, 'SubDomain', 'listsubdomains');

				$result = json_decode($subdomainsList);

				$liveSubdomain = 0;
				foreach ($result->cpanelresult->data as $domainList) {
					if ($domainNameorg == $domainList->subdomain) {
						$liveSubdomain = 1;
					}
				}

				if ((isset($cdmain) && $cdmain > 0) || (isset($liveSubdomain) && $liveSubdomain > 0)) {

					$response = ['success' => false, 'content' => "https://$domainNameorg" . WEBDOMAIN . " domain has already been taken"];

				} else if ($checkSpecialcharAndWspc > 0) {

					$response = ['success' => false, 'content' => "https://$domainNameorg" . WEBDOMAIN . " domain is not valid, please choose another domain."];

				} else {

					$response = ['success' => true, 'content' => "https://$domainNameorg" . WEBDOMAIN . " domain is available"];

				}

				echo json_encode($response);
				exit;

			} else {
				$response = ['success' => false, 'content' => "Please fill out this field"];
				echo json_encode($response);
				exit;
			}
		}

	}

	public function checkUserPrePassword($userid = null, $limit = null) {

		$this->loadModel('UserPassword');

		$previousPassword = $this->UserPassword->find('list', array('conditions' => array('UserPassword.user_id' => $userid), 'limit' => $limit, 'order' => 'id DESC', 'fields' => array('UserPassword.password')));
		//pr($previousPassword); die;
		return array_values($previousPassword);

	}

	public function projects($project_id = null) {

		$this->layout = 'resources';

		if( $_SERVER['HTTP_HOST'] != LOCALIP )  {

			if( $this->Session->read('Auth.User.role_id') == 3 ){
				$this->redirect(['controller' => 'organisations','action' => 'dashboard']);
			}
			if( $this->Session->read('Auth.User.role_id') == 1 ){
				$this->redirect(['controller' => 'dashboard','action' => 'index']);
			}
		}

		$this->loadModel('Project');
		$this->set('title_for_layout', __('Assets', true));
		$this->set('page_heading', __('Assets', true));
		$user_id = $this->Auth->user('id');
		$this->set('user_id', $user_id);
		$this->set('session_id', $this->Session->id());
		$project_id = $project_type = '';

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

		if (empty($project_type) && empty($project_id)) {

			if (isset($this->params['pass'][0]) && !empty($this->params['pass'][0])) {
				$project_id = $this->params['pass'][0];
			$project_type = $this->requestAction('/projects/CheckProjectType/' . $project_id . '/' . $this->Session->read('Auth.User.id'));
			}

		}

		$this->Project->id = $project_id;
		$this->set(compact("project_type", "project_id"));

		if ($project_id == '' || !$this->Project->exists() && $project_type == 'm_project') {
		//	$this->Session->setFlash(__('Invalid Project Id.'));
			//$this->redirect(SITEURL . 'projects/lists');
		}
		if ($project_id == '' || !$this->Project->exists() && $project_type == 'r_project') {
			//$this->Session->setFlash(__('Invalid Project Id.'));
			//$this->redirect(SITEURL . 'projects/share_lists');
		}
		if ($project_id == '' || !$this->Project->exists() && $project_type == 'g_project') {
			//$this->Session->setFlash(__('Invalid Project Id.'));
			//$this->redirect(SITEURL . 'groups/shared_projects');
		}


		/* // Find All current user's projects
		$myprojectlist = $this->__myproject_selectbox($user_id);
		// Find All current user's received projects
		$myreceivedprojectlist = $this->__receivedproject_selectbox($user_id, 1);
		// Find All current user's group projects
		$mygroupprojectlist = $this->__groupproject_selectbox($user_id, 1); */

		$myprojectlist  = array();
		$myreceivedprojectlist  = array();
		$mygroupprojectlist  = array();
		$allProjects  = array();
		$allProjects = $this->Common->getAssestsProjects($this->Session->read('Auth.User.id'),1);

		$this->set(compact("myprojectlist", "myreceivedprojectlist", "mygroupprojectlist","allProjects"));


		if (isset($this->params->query['types']) && !empty($this->params->query['types'])) {
			$typesArr = explode(",", $this->params->query['types']);
			foreach ($typesArr as $key => $val_selectbox) {
				$this->set($val_selectbox, $val_selectbox);
			}
			$this->set('typesArr', $typesArr);
		}

		$statusArr = array();
		if (isset($this->params->query['status']) && !empty($this->params->query['status'])) {
			$statusArr = explode(",", $this->params->query['status']);
			foreach ($statusArr as $key => $val_selectbox) {
				//$this->set($val_status_selectbox, $val_selectbox);
			}
			// $this->set('statusArr', $statusArr);
		}
		$this->set('statusArr', $statusArr);

		$project_where['UserProject.user_id'] = $user_id;
		$project_order = [];
		if (isset($project_id) && !empty($project_id)) {
			$project_where['Project.id'] = $project_id;
		} else {
			$project_order = array('UserProject.modified DESC');
		}

		$conditionsN = null;
		$conditionsN['ProjectPermission.user_id'] = $user_id;
		$conditionsN['ProjectPermission.user_project_id'] = $this->Common->get_up_id($project_id, $user_id);

		$this->loadModel('ProjectPermission');
		$projects_shared = $this->ProjectPermission->find('first', array(
			'conditions' => $conditionsN,
			'fields' => array('ProjectPermission.user_project_id'),
			'order' => 'ProjectPermission.created DESC',
			'recursive' => -1,
		));

		$grp_id = $this->Group->GroupIDbyUserID($project_id, $user_id);
		if (isset($grp_id) && !empty($grp_id)) {
			$group_permission = $this->Group->group_permission_details($project_id, $grp_id);

			if (isset($group_permission['ProjectPermission']['project_level']) && $group_permission['ProjectPermission']['project_level'] == 1) {
				$project_level = $group_permission['ProjectPermission']['project_level'];
				$this->set('project_level', $project_level);
			}
			$this->set('gpid', $grp_id);
		}

		$this->Project->recursive = -1;
		//$this->UserProject->recursive = 1;
		//$projects = $this->UserProject->find('all', array('conditions' => $project_where, 'order' => $project_order));
		//$project_where['Project.id'] = 0;
		//ProjectPermission
		/* $this->UserProject->unbindModel(
				['hasMany' => ['ProjectPermission']]
			); */

		if (empty($projects) && !empty($projects_shared)) {

			$project_where = NULL;
			$project_where['Project.id'] = $project_id;
			$projects = $this->UserProject->find('first', array(
			'conditions' => $project_where, 'order' => $project_order),

			);
		}

		//,'fields'=> array('Project.id','Project.title','Project.start_date','Project.end_date')

		if (empty($projects) && !empty($group_permission)) {

			$project_where = NULL;
			$project_where['Project.id'] = $project_id;
			$projects = $this->UserProject->find('first', array('conditions' => $project_where, 'order' => $project_order) );
		}

		$projectdetail = array();
		if( isset($project_id) && !empty($project_id) ){

		$pfields = 'Project.id,Project.title,Project.start_date,Project.end_date,Project.color_code';
		$projects = $this->objView->loadHelper('ViewModel')->projectsDetails($project_id,$pfields);

		//pr($projects);

		}


		$paginator = array(
			// 'fields' => array(
			// 'Template.id', 'Template.title', 'Template.description', 'Template.layout_preview'
			// ),
			'conditions' => array(
				'UserProject.status' => 1,
				$project_where,
			),
			'limit' => 8,
			"order" => "UserProject.id ASC",
		);

		$this->paginate = $paginator;
		$this->set('projects', $this->paginate('UserProject'));
		//$this->set('projects', $projects);

		$project_title = '';
		$prdata = null;
		$this->loadModel('ProjectWorkspace');

		// $cat_crumb = get_category_list($project_id);

		$crumb = [
			'last' => ['Assets'],
		];

		if (isset($project_id) && !empty($project_id)) {
			$prdata = $this->Project->find('first', ['recursive' => 1, 'conditions' => ['Project.id' => $project_id, 'Project.studio_status !=' => 1]]);

			$project_title = (isset($prdata) && !empty($prdata)) ? _strip_tags($prdata['Project']['title']) : '';
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
			$crumb = array_merge(  $crumb);
		}

		// if (isset($cat_crumb) && !empty($cat_crumb)) {
		// 	$crumb = array_merge($cat_crumb, $crumb);
		// }

		$this->set('crumb', $crumb);

		$this->set("project_id", $project_id);
	}

	public function projectDeleteEmail($project_name = null, $project_id = null) {

		$projectname = '';
		$projectname = $project_name;

		$view = new View();
		$commonHelper = $view->loadHelper('Common');

		$ownerdetails = $commonHelper->ProjectOwner($project_id, $this->Session->read('Auth.User.id'));

		$deletedUser = $this->User->findById($this->Session->read('Auth.User.id'));
		$projectDeletedUser = $deletedUser['UserDetail']['first_name'] . ' ' . $deletedUser['UserDetail']['last_name'];

		$data2 = array();
		$data1[] = $ownerdetails['UserProject']['user_id'];
		$data2 = participants_group_owner($project_id);
		$data3 = participants_owners($project_id, $ownerdetails['UserProject']['user_id']);

		$all_owner = array();
		if (isset($data2) && !empty($data2) && count($data2) > 0) {
			$all_owner = array_unique(array_merge($data1, $data3, $data2));
		} else {
			$all_owner = array_unique(array_merge($data1, $data3));
		}

		$userlist = '';

		$all_owner = array_filter($all_owner);

		//pr($all_owner); die;
		$projectAction = SITEURL . 'projects/lists';
		foreach ($all_owner as $key => $valData) {

			if (isset($valData) && !empty($valData)) {

				$this->User->unbindModel(
					['hasOne' => ['OrganisationUser']]
				);
				$this->User->unbindModel(
					['hasAndBelongsToMany' => 'Skill']
				);
				$this->User->unbindModel(
					['hasMany' => ['ProjectPermission', 'WorkspacePermission', 'ElementPermission', 'UserProject', 'UserSetting', 'UserPassword']]
				);

				$usersDetails = $this->User->find('first', array('conditions' => array('User.id' => $valData)));

				$owner_name = $usersDetails['UserDetail']['first_name'] . ' ' . $usersDetails['UserDetail']['last_name'];

				$dasAnnotate = $this->EmailNotification->find('first', ['conditions' => ['notification_type' => 'project', 'personlization' => 'project_deleted', 'user_id' => $valData]]);

				if ((!isset($dasAnnotate['EmailNotification']['email']) || $dasAnnotate['EmailNotification']['email'] == 1) && (!isset($usersDetails['User']['email_notification']) || $usersDetails['User']['email_notification'] == 1)) {

					$email = new CakeEmail();
					$email->config('Smtp');
					// $email->from(array(ADMIN_EMAIL => SITENAME));
					$email->from(array(ADMIN_FROM_EMAIL => SITENAME));
					$email->to($usersDetails['User']['email']);
					$email->subject(SITENAME . ': Project deleted');
					$email->template('project_delete_email');
					$email->emailFormat('html');
					$email->viewVars(array('project_name' => $projectname, 'owner_name' => $owner_name, 'deletedby' => $projectDeletedUser,'open_page'=>$projectAction));
					$email->send();

				}

			}

		}
		//die;

	}

	function checkExpiredOrg($orgUser_id = null) {
		$this->autoRender = false;
		$this->layout = false;

		$result = $this->OrgSetting->find('first', array('conditions' => array('OrgSetting.user_id' => $orgUser_id)));

		$currentDate = strtotime(date('Y-m-d'));
		if (strtotime($result['OrgSetting']['start_date']) <= $currentDate && strtotime($result['OrgSetting']['end_date']) >= $currentDate) {
			return true;
		} else {
			return false;
		}

	}

	public function project_people($project_id = null) {

		if ($this->request->is('get')) {

			$this->layout = 'ajax';

			$view = new View();
			$common = $view->loadHelper('Common');

			$data = null;

			if (isset($project_id) && !empty($project_id)) {

				$owner = $common->ProjectOwner($project_id, $this->Session->read('Auth.User.id'));

				$data['participants'] = participants($project_id, $owner['UserProject']['user_id']);

				$data['participants_owners'] = participants_owners($project_id, $owner['UserProject']['user_id']);

				$data['participantsGpOwner'] = participants_group_owner($project_id);

				$data['participantsGpSharer'] = participants_group_sharer($project_id);

			}

			$this->set('data', $data);

			if (isset($owner) && !empty($owner)) {
				$this->set('owner', $owner['UserProject']['user_id']);
			}

			$this->set('project_id', $project_id);
		}

	}

	public function project_assign_people($project_id = null, $type = null) {

		if ($this->request->is('get')) {

			$this->layout = 'ajax';

			$view = new View();
			$common = $view->loadHelper('Common');

			$data = null;

			if (isset($project_id) && !empty($project_id)) {
				$type = $this->params['named']['user_type'];

				if (!isset($type) || empty($type)) {
					$conditions = array('conditions' => array('ElementAssignment.project_id' => $project_id, 'ElementAssignment.reaction !=' => 3, 'ElementAssignment.created_by' => $this->Auth->user('id')), 'fields' => array('ElementAssignment.assigned_to'));

				} else {
					$conditions = array('conditions' => array('ElementAssignment.project_id' => $project_id, 'ElementAssignment.reaction !=' => 3), 'fields' => array('ElementAssignment.assigned_to'));

				}
				$owner = $common->ProjectOwner($project_id, $this->Session->read('Auth.User.id'));

				$this->ElementAssignment->unbindModel(
					array('belongsTo' => array('Element', 'Sender', 'Receiver'))
				);

				$data = $this->ElementAssignment->find('list', $conditions);
			}
			$result = array_unique($data);
			$this->set('data', $result);

			if (isset($owner) && !empty($owner)) {
				$this->set('owner', $owner['UserProject']['user_id']);
			}

			$this->set('project_id', $project_id);
		}

	}

	public function validateEmail($email = null) {

		$userDomainName = explode("@", $email);
		$userDomain = $userDomainName[1];

		$checkOrganisationDomain = $this->checkOrgDomain($userDomain);

		if (!isset($checkOrganisationDomain) || empty($checkOrganisationDomain) || $checkOrganisationDomain <= 0) {
			return false;
		} else {
			return true;
		}
	}



	// Password Policy
	public function list_program_policy() {

		$this->layout = 'ajax';
		$response = '';

		$whatINeed = explode(WEBDOMAIN, $_SERVER['HTTP_HOST']);
		$whatINeed = $whatINeed[0];

		if (($this->request->is('post') || $this->request->is('put'))) {

			if (isset($this->request->data['listpolicy']) && $this->request->data['listpolicy'] == 'resetpssword') {

				//$org_id = $this->Session->read('Auth.User.UserDetail.org_id');
				$org_id = 1;
				$data = $this->OrgPassPolicy->find('first', array('conditions' => array('OrgPassPolicy.org_id' => $org_id), 'fields' => array('OrgPassPolicy.min_lenght', 'OrgPassPolicy.numeric_char', 'OrgPassPolicy.alph_char', 'OrgPassPolicy.special_char', 'OrgPassPolicy.caps_char')));

				if (isset($data) && !empty($data)) {
					$response = (isset($data) && !empty($data)) ? $data : array();
					$this->set('policydata', $response);
				}

			} else {

				//$org_id = $this->Session->read('Auth.User.UserDetail.org_id');

				$org_id = 1;
				//$data = $this->OrgPassPolicy->find('first', array('conditions' => array('OrgPassPolicy.org_id' => $org_id), 'fields' => array('OrgPassPolicy.min_lenght', 'OrgPassPolicy.numeric_char', 'OrgPassPolicy.alph_char', 'OrgPassPolicy.special_char', 'OrgPassPolicy.caps_char')));

				$data = $this->OrgPassPolicy->find('first', array('conditions' => array('OrgPassPolicy.id' => $org_id), 'fields' => array('OrgPassPolicy.min_lenght', 'OrgPassPolicy.numeric_char', 'OrgPassPolicy.alph_char', 'OrgPassPolicy.special_char', 'OrgPassPolicy.caps_char')));

				if (isset($data) && !empty($data)) {
					$response = (isset($data) && !empty($data)) ? $data : array();
					$this->set('policydata', $response);
				}

			}
		}

	}

	// Upload Skill pdf =====================================================

	public function skillpdfupload() {
		$this->autoRender = false;
		if ($this->request->isAjax()) {
			$user_id = $this->Auth->user('id');
			//SkillPdf
			$this->layout = 'ajax';
			$response = [
				'success' => false,
				'msg' => '',
				'content' => null,
				'skillcount' => 0,
			];

			//echo $this->request->data['skill_id'];
			//pr($this->params);

			if (isset($this->params['form']['pdf_file']) && !empty(($this->params['form']['pdf_file']))) {
				$post = $this->request->data;
				$user_id = (isset($post['user_id']) && !empty($post['user_id'])) ? $post['user_id'] : $this->Auth->user('id');
				$this->request->data['pdf_name'] = strip_tags($this->request->data['pdf_name']);
				$this->request->data['pdf_name'] = str_replace("'", "", $this->request->data['pdf_name']);
				$this->request->data['pdf_name'] = str_replace('"', "", $this->request->data['pdf_name']);

				$this->request->data['SkillPdf']['pdf_name'] = $newname = (isset($this->request->data['pdf_name']) && !empty($this->request->data['pdf_name'])) ? str_replace(".", "", pathinfo($this->request->data['pdf_name'], PATHINFO_FILENAME)) . ".pdf" : $this->params['form']['pdf_file']['name'];

				$this->request->data['SkillPdf']['tooltip_name'] = (isset($this->request->data['pdf_name']) && !empty($this->request->data['pdf_name'])) ? $this->request->data['pdf_name'] : basename($this->params['form']['pdf_file']['name'], ".pdf");

				$this->request->data['SkillPdf']['user_id'] = $user_id;
				$this->request->data['SkillPdf']['skill_id'] = $this->request->data['skill_id'];

				if (file_exists(SKILL_PDF_PATH . $user_id . DS . $newname)) {

					$filepath = SKILL_PDF_PATH . $user_id;
					$fileoldname = $newname;
					$this->request->data['SkillPdf']['pdf_name'] = $newname = $this->file_newname($filepath, $fileoldname);

				}

				$allowed = array('pdf');
				$filename = $this->params['form']['pdf_file']['name'];
				$tmpname = $this->params['form']['pdf_file']['tmp_name'];
				$ext = pathinfo($filename, PATHINFO_EXTENSION);
				$filsize = filesize($tmpname);

				if (in_array($ext, $allowed) && $filsize <= 5242880) {

					if (!file_exists(SKILL_PDF_PATH . $user_id)) {
						mkdir(SKILL_PDF_PATH . $user_id, 0777, true);
					}
					if (move_uploaded_file($tmpname, SKILL_PDF_PATH . $user_id . '/' . $newname)) {
						$this->SkillPdf->save($this->request->data);

						$getSkill = $this->SkillPdf->find('all', array('conditions' => array('SkillPdf.user_id' => $user_id, 'SkillPdf.skill_id' => $this->request->data['skill_id']), 'order' => 'SkillPdf.pdf_name ASC'));

						$content = [];
						if (isset($getSkill) && !empty($getSkill)) {
							$skillCnt = 0;
							foreach($getSkill as $listskill){

								if( file_exists(SKILL_PDF_PATH . $listskill['SkillPdf']['user_id'] . DS . $listskill['SkillPdf']['pdf_name']) ){
									$content[$skillCnt] = ['pdfname' => $listskill['SkillPdf']['pdf_name'], 'pdf_id' => $listskill['SkillPdf']['id']];
									if( isset($listskill['SkillPdf']['tooltip_name']) && !empty($listskill['SkillPdf']['tooltip_name']) ){
										$content[$skillCnt]['tooltip_name'] = $listskill['SkillPdf']['tooltip_name'];
									} else {
										$pdftooltip = explode(".pdf",$listskill['SkillPdf']['pdf_name']);
										$content[$skillCnt]['tooltip_name'] = $pdftooltip[0];
									}
									$content[$skillCnt]['user_id'] = $listskill['SkillPdf']['user_id'];
									$skillCnt++;
								}
							}

						}

						$response = [
							'success' => true,
							'msg' => 'Pdf file uploaded successfully',
							'content' => $content,
							'skillcount' => $skillCnt,

						];

					}

				} else {
					$response = [
						'success' => false,
						'msg' => 'File type or file type is not valid',
						'content' => '',
						'skillcount' => 0,
					];
				}

			}
			echo (json_encode($response));
			exit;

		}

	}

	public function get_skill_pdf() {
		if ($this->request->isAjax()) {
			$user_id = $this->Auth->user('id');
			$response = [
				'success' => false,
				'content' => ['pdfname'=>'','pdf_id'=>'','tooltip_name'=>'','user_id'=>'','skillcount'=>'','SkillDetail'=>''],
			];
			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;
				// pr($post, 1);
				$user_id = (isset($post['user_id']) && !empty($post['user_id'])) ? $post['user_id'] : $this->Auth->user('id');
				$response['success'] = true;
				// $user_id = $this->Auth->user('id');
				if( !empty($post['skill_id']) ){
					$getSkill = $this->SkillPdf->find('all', array('conditions' => array('SkillPdf.user_id' => $user_id, 'SkillPdf.skill_id' => $post['skill_id'] ), 'order' => 'SkillPdf.pdf_name ASC'));

					$userSkillDetails = $this->SkillDetail->find('first',array('conditions'=>array('SkillDetail.skill_id'=>$post['skill_id'],'SkillDetail.user_id'=>$user_id)));

					//pr($userSkillDetails,1);

					$content = [];
					if (isset($getSkill) && !empty($getSkill)) {
						$skillCnt = 0;
						foreach($getSkill as $listskill){

							if( file_exists(SKILL_PDF_PATH . $listskill['SkillPdf']['user_id'] . DS . $listskill['SkillPdf']['pdf_name']) ){
								$content[$skillCnt] = ['pdfname' => $listskill['SkillPdf']['pdf_name'], 'pdf_id' => $listskill['SkillPdf']['id']];
								if( isset($listskill['SkillPdf']['tooltip_name']) && !empty($listskill['SkillPdf']['tooltip_name']) ){
									$content[$skillCnt]['tooltip_name'] = $listskill['SkillPdf']['tooltip_name'];
								} else {
									$pdftooltip = explode(".pdf",$listskill['SkillPdf']['pdf_name']);
									$content[$skillCnt]['tooltip_name'] = $pdftooltip[0];
								}
								$content[$skillCnt]['user_id'] = $listskill['SkillPdf']['user_id'];
								$skillCnt++;
							}

						}
						$response['content']['skillcount'] = $skillCnt;
					}


					if( isset($userSkillDetails) && !empty($userSkillDetails['SkillDetail']) ){

						$content['details']['user_level'] = $userSkillDetails['SkillDetail']['user_level'];
						$content['details']['user_experience'] = $userSkillDetails['SkillDetail']['user_experience'];
						$content['details']['skill_id'] = $userSkillDetails['SkillDetail']['skill_id'];
					} else {
						$content['details']['user_level'] = null;
						$content['details']['user_experience'] = null;
					}

				}

				$response['content'] = $content;
			}
		}
		echo json_encode($response);
		exit();
	}

	public function skillpdfdelete() {
		if ($this->request->isAjax()) {
			$response = [
				'success' => false,
				'content' => [],
			];
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;

				$getSkill = $this->SkillPdf->find('first', array('conditions' => array('SkillPdf.id' => $post['id'])));

				if (isset($getSkill) && !empty($getSkill)) {
					$this->SkillPdf->delete(array('SkillPdf.id' => $post['id']));
					$response['success'] = true;
					if (file_exists(SKILL_PDF_PATH . $this->Session->read("Auth.User.id") . DS . $getSkill['SkillPdf']['pdf_name'])) {
						unlink(SKILL_PDF_PATH . $this->Session->read("Auth.User.id") . DS . $getSkill['SkillPdf']['pdf_name']);
					}
				}

			}
		}
		echo json_encode($response);
		exit();
	}

	function file_newname($path, $filename) {
		if ($pos = strrpos($filename, '.')) {
			$name = substr($filename, 0, $pos);
			$ext = substr($filename, $pos);
		} else {
			$name = $filename;
		}

		$newpath = $path . '/' . $filename;
		$newname = $filename;
		$counter = 0;
		while (file_exists($newpath)) {
			$newname = $name . '_' . $counter . $ext;
			$newpath = $path . '/' . $newname;
			$counter++;
		}

		return $newname;
	}

	public function get_skills() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			// pr($this->request->query['term'] , 1);
			$response = [
				'success' => false,
				'msg' => null,
				'content' => null,
			];
			$skills = [];
			if ($this->request->isJson()) {
				// if (isset($this->request->query['term']) && !empty($this->request->query['term'])) {
					$term = $this->request->query['term'];
					$seperator = '^';
					$search_str = Sanitize::escape(like($term, $seperator ));
					//$query = "SELECT id, title FROM skills WHERE status = 1 AND (`title` like '% $term%' OR  `title` like  '%$term %'  OR  `title` like  '% $term %' OR `title` like '$term%')  order by title asc";
					$query = "SELECT id, title FROM skills WHERE status = 1 AND (`title` like '%$search_str%' ESCAPE '$seperator' ) order by title asc";

					$skills = $this->Skill->query($query);
					if (isset($skills) && !empty($skills)) {
						$skills = Set::combine($skills, '{n}.skills.id', '{n}.skills.title');
					}
					if (isset($skills) && !empty($skills)) {
						$response['success'] = true;
						$response['content'] = $skills;
					} else {
						$response['success'] = false;
						$response['content'] = null;
						$response['msg'] = 'No matching Skills found';
					}

				// }
			}
			echo json_encode($response);
			exit;
		}

	}

	/*========== User Interest Start =================================*/

	public function save_interest() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$response = [
				'success' => false,
				'msg' => null,
			];

			if (isset($this->request->data) && empty($this->request->data['interest-id']) && !empty($this->request->data['interest-title'])) {

				$this->request->data['UserInterest']['user_id'] = $this->request->data['edited_id'];
				$this->request->data['UserInterest']['title'] = $this->request->data['interest-title'];//strip_tags($this->request->data['interest-title']);
				$this->UserInterest->save($this->request->data['UserInterest']);
				$this->UserInterest->getLastInsertID();

				$response = [
					'success' => true,
					'msg' => 'User interest saved',
				];

			} else {

				if (!empty($this->request->data['interest-id'])) {

					$this->request->data['UserInterest']['user_id'] = $this->request->data['edited_id'];
					$this->request->data['UserInterest']['id'] = $this->request->data['interest-id'];
					$this->request->data['UserInterest']['title'] = $this->request->data['interest-title'];//strip_tags($this->request->data['interest-title']);
					$this->UserInterest->save($this->request->data['UserInterest']);

					$response = [
						'success' => true,
						'msg' => 'User interest updated',
					];

				}

			}

			echo json_encode($response);
			exit;
		}
	}

	public function user_interest_list($user_id = null) {

		if ($this->request->isAjax()) {

			$this->layout = false;
			$user_id = (!isset($user_id) || empty($user_id)) ? $this->Auth->user('id') : $user_id;
			$this->UserInterest->recursive = -1;
			$interestlists = $this->UserInterest->find('all',
				array('conditions' => array('UserInterest.user_id' => $user_id), 'order' => 'UserInterest.title ASC')
			);
			$view = new View($this, false);
			$view->viewPath = 'Users';
			$view->set("interestlists", $interestlists);
			$html = $view->render('interestlists');

			echo json_encode($html);
			exit();
		}

	}

	public function delete_user_interest() {

		if ($this->request->isAjax()) {

			$this->layout = false;
			$response = [
				'success' => false,
				'content' => null,
			];

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				if (isset($post['id']) && !empty($post['id'])) {

					if ($this->UserInterest->delete($post['id'])) {
						$response['success'] = true;
					}

				}
			}

			echo json_encode($response);
			exit();
		}

	}

	public function check_interest_count($user_id = null) {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$response = [
				'success' => true,
			];

			$user_id = (!isset($user_id) || empty($user_id)) ? $this->Auth->user('id') : $user_id;
			$this->UserInterest->recursive = -1;
			$interestlists = $this->UserInterest->find('count',
				array('conditions' => array('UserInterest.user_id' => $user_id), 'order' => 'UserInterest.id DESC')
			);

			// if ($interestlists >= 20) {
			// 	$response['success'] = false;
			// }
			echo json_encode($response);
			exit();
		}

	}
	/*========== User Interest End =================================*/

	public function element_delete_data($data = null) {

		if (!empty($data)) {

			//$this->DeleteData->save($data);

		}

	}

	public function wsp_delete_data($data = null) {

		if (!empty($data)) {

			//$this->DeleteData->save($data);

		}

	}

	public function project_delete_data($data = null) {

		if (!empty($data)) {

			//$this->DeleteData->save($data);

		}

	}
	/* USER ACCOUNT LOCK */
	public function locked() {
		$this->layout = 'lock';
		$this->set('title_for_layout', __('Account Locked', true));
		$this->set('page_heading', __('Account Locked', true));
		$this->setJsVar('referer', $this->referer());

	}

	public function lock_my_account() {
		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$response = [
				'success' => false,
			];

			$user_id = $this->Auth->user('id');
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				$response['success'] = true;
				$this->Session->write('account_locked', true);
			}
		}
		echo json_encode($response);
		exit();
	}

	public function unlock_account() {
		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$response = [
				'success' => false,
			];

			$user_id = $this->Auth->user('id');
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				if($this->validate_unlock($post)){
					$response['success'] = true;
					$this->Session->delete('account_locked');
				}
			}
		}
		echo json_encode($response);
		exit();
	}


	function validate_unlock($post) {
		$userId = $this->Auth->user('id');

		$userInput = $post['pass'];
		$hashedUserInput = Security::hash($userInput, null, true);

		$passData = $this->User->find('count', array(
			'conditions' => array(
				'password' => $hashedUserInput,
				'id' => $userId,
			),
			'recursive' => -1,
		));
		// pr($userId, 1);
		if (isset($passData) && !empty($passData)) {
			return true;
		}
		return false;
	}
	/* USER ACCOUNT LOCK END */

	// save user skill details ===================================================
	public function save_user_skill_detail(){

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$response = [
				'success' => false,
				'content' => null,
			];

			if( isset($this->request->data['skill_id']) && !empty($this->request->data['skill_id']) ){
				$this->loadModel('UserSkill');
				$post = $this->request->data;
				$user_id = (isset($post['user_id']) && !empty($post['user_id'])) ? $post['user_id'] : $this->Auth->user('id');
				// pr($post, 1);
				$user_skill_count = $this->UserSkill->find('count',
                                   [
	                                   	'conditions' =>
		                                   	[
		                               			'UserSkill.user_id' => $user_id,
		                               			'UserSkill.skill_id' => $this->request->data['skill_id']
											]
									]
								);
				if(!isset($user_skill_count) || empty($user_skill_count)){
					$skilldata['UserSkill'] = ['user_id' => $user_id, 'skill_id' => $this->request->data['skill_id']];
					$this->UserSkill->save($skilldata);
				}

				$this->request->data['SkillDetail']['user_level'] = $this->request->data['user_level'];
				$this->request->data['SkillDetail']['user_experience'] = $this->request->data['user_experience'];
				$this->request->data['SkillDetail']['skill_id'] = $this->request->data['skill_id'];
				$this->request->data['SkillDetail']['user_id'] = $user_id;

				$checkSkillDetails = $this->SkillDetail->find('all',
						array('conditions'=>array('SkillDetail.skill_id'=>$this->request->data['skill_id'],'SkillDetail.user_id'=>$user_id))
				);

				if( isset($checkSkillDetails) && count($checkSkillDetails) > 0 ){

					$skillDetailIds = Set::extract($checkSkillDetails, '/SkillDetail/id');
					if( $this->SkillDetail->delete($skillDetailIds) ){
						$this->SkillDetail->save($this->request->data);
						$response['success'] = true;
						$response['content'] = $this->request->data;
					}

				} else {
					$this->SkillDetail->save($this->request->data);
					$response['success'] = true;
					$response['content'] = $this->request->data;
				}
			}

		echo json_encode($response);
		exit();
		}

	}

	public function delete_user_skill_detail(){

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$response = [
				'success' => false,
				'content' => null,
			];

			$user_id = $this->Auth->user('id');

			if( !empty($this->request->data['skill_id']) && !empty($this->request->data['skill_id']) ){
				$post = $this->request->data;
				$user_id = (isset($post['user_id']) && !empty($post['user_id'])) ? $post['user_id'] : $this->Auth->user('id');

				$this->request->data['SkillDetail']['skill_id'] = $this->request->data['skill_id'];
				$this->request->data['SkillDetail']['user_id'] = $user_id;

				$checkSkillDetails = $this->SkillDetail->find('all',
						array('conditions'=>array('SkillDetail.skill_id'=>$this->request->data['skill_id'],'SkillDetail.user_id'=>$user_id))
				);
				if( isset($checkSkillDetails) && count($checkSkillDetails) > 0 ){

					$skillDetailIds = Set::extract($checkSkillDetails, '/SkillDetail/id');
					if( $this->SkillDetail->delete($skillDetailIds) ){
						$this->SkillDetail->save($this->request->data);
						$response['success'] = true;
						$response['content'] = $this->request->data;
					}
				}
			}

		echo json_encode($response);
		exit();
		}

	}

	public function delete_user_skill(){

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$response = [
				'success' => false,
				'content' => null,
			];

			$user_id = $this->Auth->user('id');

			if( !empty($this->request->data['skill_id']) && !empty($this->request->data['skill_id']) ){
				$post = $this->request->data;
				$user_id = (isset($post['user_id']) && !empty($post['user_id'])) ? $post['user_id'] : $this->Auth->user('id');

				$this->request->data['SkillDetail']['skill_id'] = $this->request->data['skill_id'];
				$this->request->data['SkillDetail']['user_id'] = $user_id;

				$checkSkillDetails = $this->SkillDetail->find('all',
						array('conditions'=>array('SkillDetail.skill_id'=>$this->request->data['skill_id'],'SkillDetail.user_id'=>$user_id))
				);
				if( isset($checkSkillDetails) && count($checkSkillDetails) > 0 ){

					$skillDetailIds = Set::extract($checkSkillDetails, '/SkillDetail/id');
					$this->SkillDetail->delete($skillDetailIds);
				}

				$this->loadModel('UserSkill');
				$countSkillDetail = $this->UserSkill->find('all',
						array('conditions'=> ['UserSkill.skill_id'=>$this->request->data['skill_id'], 'UserSkill.user_id'=>$user_id], 'recursive'=>-1, 'fields'=> ['id']));
				if(isset($countSkillDetail) && !empty($countSkillDetail)){
					$user_skill_ids = Set::extract($countSkillDetail, '/UserSkill/id');
					$this->UserSkill->delete($user_skill_ids);
				}
				$response['success'] = true;
				$response['content'] = $this->request->data;
			}

		echo json_encode($response);
		exit();
		}

	}


	// Generate token
	public function getToken($length){
		$token = "";
		$codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
		$codeAlphabet.= "abcdefghijklmnopqrstuvwxyz";
		$codeAlphabet.= "0123456789";
		$max = strlen($codeAlphabet); // edited

		for ($i=0; $i < $length; $i++) {
			$token .= $codeAlphabet[random_int(0, $max-1)];
		}

		return $token;
	}


	public function check_session_cookie() {

		if ($this->request->isAjax()) {

			$this->layout = false;

			$response = [
				'success' => false
			];

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				$userData = $this->User->find('first', ['conditions' => ['User.session_token' => $post['session_cookie'], 'User.id' => $this->Auth->user('id')], 'recursive' => -1, 'fields' => ['session_token']]);
				if(isset($userData) && !empty($userData)) {
					$response['success'] = true;
				}
			}
		}

		echo json_encode($response);
		exit();

	}

	public function activate_account($act_key = null) {
		$cid = $this->Session->read('Auth.User.id');
		if(isset($cid) && !empty($cid)){
			$this->Auth->logout();
		}

		if(!isset($act_key) || empty($act_key)){
			// die('activation key invalid');
			$this->Session->setFlash('Invalid user.', 'error');
			$this->redirect(SITEURL.'users/login');
		}

		$endc = safeDecrypt($act_key);

		$userData = $this->User->find('first', array('conditions' => array('User.email' => $endc), 'recursive' => -1));

		if(!isset($userData) || empty($userData)){
			// die('Record not found of this activation key');
			$this->Session->setFlash('The Username no longer exists.', 'error');
			$this->redirect(SITEURL.'users/login');
		}

		if(!empty($userData['User']['is_activated'])){
			// die('Account already activated');
			$this->Session->setFlash('Your account is already active.', 'error');
			$this->redirect(SITEURL.'users/login');
		}

		$today = date('Y-m-d H:i:s');
		$activation_time = date('Y-m-d H:i:s', strtotime($userData['User']['activation_time']));
		$hour_diff = round((strtotime($today) - strtotime($activation_time))/3600, 1);
		if($hour_diff > 48){
			// die('Email is older than 48 hrs');
			$this->Session->setFlash('Your account activation link has expired.', 'error');
			$this->redirect(SITEURL.'users/login');
		}

		$save_user_detail['User'] = [
				'status' => 1,
				'is_activated' => 1,
				'activation_time' => '',
			];
		$this->User->id = $userData['User']['id'];
		if($this->User->save($save_user_detail)){
			$this->CommonEmail->user_confirmation(['user_id' => $userData['User']['id']]);
			// INSERT DEFAULT ENTRY FOR REWARD OPT SETTING FOR THIS USER
			$this->loadModel('RewardOptedSetting');
			$rewData = [
					'user_id' => $userData['User']['id'],
					'reward_opt_status' => 1,
					'reward_table_opt_status' => 1,
					'created' => date('Y-m-d H:i:s'),
					'modified' => date('Y-m-d H:i:s')
				];
				$this->RewardOptedSetting->save($rewData);
			// GET PRIVATE FROM USER LOCATION TYPE
			$this->loadModel('UserLocationType');
			$ult = $this->UserLocationType->query("SELECT id FROM user_location_types WHERE location LIKE '%Private%' LIMIT 1");
			if(isset($ult) && !empty($ult)){
				$this->loadModel('UserLocation');
				$user_location_id = $ult[0]['user_location_types']['id'];
				$inData = [
						'user_id' => $userData['User']['id'],
						'user_location_type_id' => $user_location_id,
						'start_datetime' => date('Y-m-d H:i:s')
					];
				if($this->UserLocation->save($inData)){ }
			}

			$this->Session->setFlash('Your account has been activated.', 'success');
			$this->redirect(SITEURL.'users/login');
		}
	}




}
