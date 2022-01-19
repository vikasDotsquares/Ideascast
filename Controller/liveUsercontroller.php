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
	public $uses = array('User', 'Skill', 'UserDetail', 'UserPlan', 'UserInstitution', 'Plan', 'UserTransctionDetail', 'UserProject', 'Coupon', 'CakeSession', 'Element', 'Workspace', 'Area', 'Project', 'ProjectGroupUser', 'UserProject', 'ProjectWorkspace', 'ProjectPermission', 'ElementPermission', 'ManageDomain', 'OrganisationUser', 'OrgPassPolicy', 'UserPassword', 'PasswordLockout', 'EmailNotification', 'OrgSetting', 'ElementAssignment');

	/**
	 * check login for admin and frontend user
	 * allow and deny user
	 */
	public $components = array('Email', 'Common', 'Image', 'CommonEmail', 'Auth', 'Group');

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
		$this->Auth->allow('index', 'register', 'confirm', 'logout', 'activate', 'activation', 'forgetpwd', 'reset', 'admin_login', 'admin_forgotpassword', 'admin_logout', 'admin_forgot_password', 'admin_check_old_password', 'admin_get_state_city', 'registration', 'checkSession', 'validate_email');

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

		$plans = $this->UserPlan->find('first', array('conditions' => array('UserPlan.user_id' => $id, 'UserPlan.plan_id' => 4, ' 	UserPlan.start_date !=' => ''), 'fields' => array('UserPlan.is_active')));

		return isset($plans) ? $plans : array();
	}

	public function getUPlan($id) {
		$plans = $this->UserPlan->find('all', array('conditions' => array('UserPlan.user_id' => $id, 'UserPlan.plan_id !=' => '', 'UserPlan.start_date !=' => ''), 'fields' => array('UserPlan.id', 'UserPlan.start_date', 'UserPlan.end_date', 'UserPlan.user_id', 'UserPlan.plan_id'), 'Order' => 'UserPlan.id'));
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

		$cat_crumb = get_category_list($project_id);
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

		if (isset($cat_crumb) && !empty($cat_crumb)) {
			$crumb = array_merge($cat_crumb, $crumb);
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
			$workspaces = $this->ProjectWorkspace->query("select Workspace.id as id,Workspace.title as title,Workspace.color_code as color_code from project_workspaces as ProjectWorkspace left join workspaces as Workspace on ProjectWorkspace.workspace_id=Workspace.id  where ProjectWorkspace.project_id = '$project_id' and Workspace.studio_status != 1 order by ProjectWorkspace.sort_order");
		} elseif (isset($project_type) && !empty($project_type) && ($project_type == 'r_project' || $project_type == 'g_project')) {

			if (isset($pr_permission['ProjectPermission']['project_level']) && $pr_permission['ProjectPermission']['project_level'] > 0) {
				$workspaces = $this->ProjectWorkspace->query("select Workspace.id as id,Workspace.title as title,Workspace.color_code as color_code from project_workspaces as ProjectWorkspace left join workspaces as Workspace on ProjectWorkspace.workspace_id=Workspace.id  where ProjectWorkspace.project_id = '" . $project_id . "'  and Workspace.studio_status != 1 order by ProjectWorkspace.sort_order");
			} else {
				$workspaces = $this->ProjectWorkspace->query("select Workspace.id as id,Workspace.title as title,Workspace.color_code as color_code from project_workspaces as ProjectWorkspace left join workspaces as Workspace on ProjectWorkspace.workspace_id=Workspace.id  where ProjectWorkspace.project_id = '" . $project_id . "' AND ProjectWorkspace.id in(" . $ims . ")  and Workspace.studio_status != 1 order by ProjectWorkspace.sort_order");
			}
		}
		// pr($workspaces);
		$myWorkspaceslistByproject = array();
		if (isset($workspaces) && !empty($workspaces)) {
			foreach ($workspaces as $valWP) {
				$myWorkspaceslistByproject[$valWP['Workspace']['id']] = strip_tags(str_replace("&nbsp;", " ", $valWP['Workspace']['title']));
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

		$project = $this->Project->find("first", array("conditions" => array("Project.id" => $project_id), "fields" => array()));

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
			$totaldays = floor(abs(strtotime($maxdate) - strtotime($mindate)) / (60 * 60 * 24)) + 1;
		} else {
			$totaldays = floor(abs(strtotime($maxdate) - strtotime($mindate)) / (60 * 60 * 24));
		}
		if ($curr_date <= $maxdate && $mindate <= $curr_date) {
			if (!empty($mindate) && !empty($maxdate)) {
				$remainingdays = floor(abs(strtotime($maxdate) - strtotime($curr_date)) / (60 * 60 * 24)) + 1;
			} else {
				$remainingdays = floor(abs(strtotime($maxdate) - strtotime($curr_date)) / (60 * 60 * 24));
			}
			$leftdays = floor(abs(strtotime($curr_date) - strtotime($mindate)) / (60 * 60 * 24));
		} else {
			$remainingdays = 0;
			if (!empty($mindate) && !empty($maxdate) && $mindate <= $curr_date) {
				$leftdays = floor(abs(strtotime($maxdate) - strtotime($mindate)) / (60 * 60 * 24)) + 1;
			} else if (!empty($mindate) && !empty($maxdate) && $mindate >= $curr_date) {
				$leftdays = 0;
			} else {
				$leftdays = floor(abs(strtotime($maxdate) - strtotime($mindate)) / (60 * 60 * 24));
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

		//echo 'project_id : '.$project_id, 'user_id : '.$user_id, 'project_type : '.$project_type, 'wrk_id : '.$workspace_id, 'status : '.$status, 'type : '.$type;
		//pr($this->params['pass']);

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

		$project = $this->Project->find("first", array("conditions" => array("Project.id" => $project_id), "fields" => array()));

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

			if (!empty($uId) && $gId < 1) {
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
						$status_conditions = 'Element.area_id= "' . $areaId . '" AND Element.end_date < "' . $cur_date . '"';
					} else if ($status == 'CMP') {
						$status_conditions = 'Element.area_id= "' . $areaId . '" AND Element.sign_off="1"';
					}

					$elementByAreaid = $this->Element->query("select Element.* from elements as Element where $status_conditions and Element.studio_status !=1 order by start_date asc");

					if (!empty($criticalEleStatus) && $criticalEleStatus == 1) {
						$elementByAreaid = $this->Element->query("select Element.*,ElementDependency.* from elements as Element INNER JOIN element_dependencies as ElementDependency ON Element.id=ElementDependency.element_id  where $status_conditions and Element.studio_status !=1 and ElementDependency.is_critical =1 order by start_date asc");
					}

					if (!empty($uId) && !empty($gId)) {
						$elementByAreaid = array();
						$elementByAreaid = $this->Element->query("select Element.*,ElementPermission.* from elements as Element INNER JOIN element_permissions as ElementPermission ON Element.id=ElementPermission.element_id  where $status_conditions and Element.studio_status !=1 and ElementPermission.project_group_id =$gId order by start_date asc");

						$elementByAreaid2 = $this->Element->query("select Element.*,ElementPermission.* from elements as Element INNER JOIN element_permissions as ElementPermission ON Element.id=ElementPermission.element_id  where $status_conditions and Element.studio_status !=1 and ElementPermission.user_id =$uId order by start_date asc");

						if (isset($elementByAreaid2) && !empty($elementByAreaid2)) {
							$elementByAreaid = array_merge($elementByAreaid2, $elementByAreaid);
						}

					}
					if (!empty($uId) && $gId < 1) {
						$elementByAreaid = $this->Element->query("select Element.*,ElementPermission.* from elements as Element INNER JOIN element_permissions as ElementPermission ON Element.id=ElementPermission.element_id  where $status_conditions and Element.studio_status !=1 and ElementPermission.user_id =$uId order by start_date asc");
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

				if (!empty($uId) && $gId < 1) {
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

						if (!empty($uId) && !empty($gId)) {
							$elementByAreaid = array();
							$elementByAreaid = $this->Element->query("select Element.*,ElementPermission.* from elements as Element INNER JOIN element_permissions as ElementPermission ON Element.id=ElementPermission.element_id  where $status_conditions and Element.studio_status !=1 and ElementPermission.project_group_id =$gId order by start_date asc");

							$elementByAreaid2 = $this->Element->query("select Element.*,ElementPermission.* from elements as Element INNER JOIN element_permissions as ElementPermission ON Element.id=ElementPermission.element_id  where $status_conditions and Element.studio_status !=1 and ElementPermission.user_id =$uId order by start_date asc");

							if (isset($elementByAreaid2) && !empty($elementByAreaid2)) {
								$elementByAreaid = array_merge($elementByAreaid2, $elementByAreaid);
							}

						}
						if (!empty($uId) && $gId < 1) {
							$elementByAreaid = $this->Element->query("select Element.*,ElementPermission.* from elements as Element INNER JOIN element_permissions as ElementPermission ON Element.id=ElementPermission.element_id  where $status_conditions and Element.studio_status !=1 and ElementPermission.user_id =$uId order by start_date asc");
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
							$elementByAreaid = $this->Element->query("select Element.*,ElementDependency.* from elements as Element INNER JOIN element_dependencies as ElementDependency ON Element.id=ElementDependency.element_id  where   $status_conditions and Element.studio_status !=1 and ElementDependency.is_critical =1 order by start_date asc");
						}

						if (!empty($uId) && !empty($gId)) {
							$elementByAreaid = array();

							$elementByAreaid = $this->Element->query("select Element.*,ElementPermission.* from elements as Element INNER JOIN element_permissions as ElementPermission ON Element.id=ElementPermission.element_id  where $status_conditions and Element.studio_status !=1 and ElementPermission.project_group_id =$gId order by start_date asc");

							$elementByAreaid2 = $this->Element->query("select Element.*,ElementPermission.* from elements as Element INNER JOIN element_permissions as ElementPermission ON Element.id=ElementPermission.element_id  where $status_conditions and Element.studio_status !=1 and ElementPermission.user_id =$uId order by start_date asc");

							if (isset($elementByAreaid2) && !empty($elementByAreaid2)) {
								$elementByAreaid = array_merge($elementByAreaid2, $elementByAreaid);
							}

						}
						if (!empty($uId) && $gId < 1) {
							$elementByAreaid = $this->Element->query("select Element.*,ElementPermission.* from elements as Element INNER JOIN element_permissions as ElementPermission ON Element.id=ElementPermission.element_id  where $status_conditions and Element.studio_status !=1 and ElementPermission.user_id =$uId order by start_date asc");
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

	public function event_gantt() {
		$this->set('title_for_layout', __('Planner', true));
		$this->layout = "calander";
		$user_id = $this->Auth->user('id');
		$project_id = '';

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
		$project = $this->Project->find("first", array("conditions" => array("Project.id" => $project_id), "fields" => array("Project.title")));
		$project_name = strip_tags(str_replace("&nbsp;", " ", $project['Project']['title']));
		$workspace_id = isset($this->params->named['workspace']) && !empty($this->params->named['workspace']) ? $this->params->named['workspace'] : '';
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
		// Find All workspaces by project id
		$elementsArr = $this->element_by_project($project_id, $user_id, $project_type, $type = 'gantt');

		//pr($elementsArr);
		$pd = $this->Project->find('first', array('conditions' => array('Project.id' => $project_id)));
		$this->set("projects", $pd);

		// $cat_crumb = get_category_list($project_id);
		$crumb = ['last' => ['Gantt']];

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

		$this->set(compact(
			"project_id", "elementsArr", "project_name", "workspace_id", "project_type", "myprojectlist", "myreceivedprojectlist", "myWorkspaceslistByproject", "mygroupprojectlist"
		)
		);
		// pr($mygroupprojectlist, 1);
		$this->render("gantt/event_gantt");
	}

	public function get_workspaces_by_project() {
		$this->autoRender = false;
		$this->layout = false;

		$user_id = $this->Auth->user('id');
		$project_id = $workspace_id = '';
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
		$project = $this->Project->find("first", array("conditions" => array("Project.id" => $project_id), "fields" => array("Project.title")));
		$projects = $this->Project->find("first", array("recursive" => 0, "conditions" => array("Project.id" => $project_id), "fields" => array()));

		$project_name = strip_tags(str_replace("&nbsp;", " ", $project['Project']['title']));
		$status = isset($this->data['status']) && !empty($this->data['status']) ? $this->data['status'] : '';

		$criticalStatus = isset($this->data['criticalStatus']) && !empty($this->data['criticalStatus']) && $this->data['criticalStatus'] == 1 ? $this->data['criticalStatus'] : '';

		$uId = (isset($this->data['user_id']) && !empty($this->data['user_id'])) ? $this->data['user_id'] : 0;
		$gId = (isset($this->data['group_id']) && !empty($this->data['group_id'])) ? $this->data['group_id'] : 0;

		if ($this->RequestHandler->isAjax()) {
			// Find All workspaces by project id
			$elementsArr = $this->element_by_ws_and_status($project_id, $user_id, $project_type, $workspace_id, $status, $type = 'gantt', $criticalStatus, $uId, $gId);

			$this->set(compact("project_id", "elementsArr", "project_name", "workspace_id", "project_type", "myprojectlist", "myreceivedprojectlist", "myWorkspaceslistByproject", "projects"));
			$this->render('gantt/get_workspaces_by_project');
			$this->layout = false;
			// exit;
		}
	}

	public function get_element_by_workspaces() {
		$this->autoRender = false;
		$this->layout = false;

		$user_id = $this->Auth->user('id');
		$project_id = $workspace_id = '';
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
		$project = $this->Project->find("first", array("conditions" => array("Project.id" => $project_id), "fields" => array("Project.title")));
		$projects = $this->Project->find("first", array("recursive" => 0, "conditions" => array("Project.id" => $project_id), "fields" => array()));
		$project_name = strip_tags(str_replace("&nbsp;", " ", $project['Project']['title']));
		if ($this->RequestHandler->isAjax()) {
			// Find All workspaces by project id

			$uId = (isset($this->data['user_id']) && !empty($this->data['user_id'])) ? $this->data['user_id'] : 0;
			$gId = (isset($this->data['group_id']) && !empty($this->data['group_id'])) ? $this->data['group_id'] : 0;

			// Find All workspaces by project id
			$elementsArr = $this->element_by_ws_and_status($project_id, $user_id, $project_type, $workspace_id, $status, $type = 'gantt', $criticalStatus, $uId, $gId);

			$this->set(compact("project_id", "elementsArr", "project_name", "workspace_id", "workspace_id", "project_type", "myprojectlist", "myreceivedprojectlist", "myWorkspaceslistByproject", "projects"));
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

		$cat_crumb = get_category_list($project_id);

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

		if (isset($cat_crumb) && !empty($cat_crumb)) {
			$crumb = array_merge($cat_crumb, $crumb);
		}

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

		if (check_license() == false) {

			//$this->Session->setFlash(" This domain limit is excced, Please contact to your administrator.", 'error');
			$this->Session->setFlash("All OpusView licences are allocated to users, Please contact to your administrator.", 'error');
			$_SESSION['data'] = $this->request->data;
			$this->redirect(array('controller' => 'users', 'action' => 'login', 'admin' => false));

		}

		$this->set('questionArray', $this->questionArray);

		$this->loadModel('UserPassword');

		if ($this->request->is('post') || $this->request->is('put')) {
			$this->request->data['UserDetail']['question'] = $this->questionArray[$this->request->data['UserDetail']['question']];
			$this->request->data['User']['activation_key'] = $activatiinHash = $this->User->getActivationHash();
			$this->request->data['User']['role_id'] = SITE_USER;

			$this->User->create();

			$this->UserDetail->validator()->remove('org_name');

			if (isset($this->request->data['UserDetail']['membership_code']) && !empty($this->request->data['UserDetail']['membership_code'])) {
				$memcode = $this->request->data['UserDetail']['membership_code'];

				$userData = $this->UserInstitution->find('first', array('fields' => array('UserInstitution.*'), 'conditions' => array('UserInstitution.membership_code' => $memcode, 'UserInstitution.end >=' => date('Y-m-d 12:00:00'), 'UserInstitution.start <=' => date('Y-m-d 12:00:00'))));

				if (isset($userData) && !empty($userData)) {
					$userDetails = $this->UserDetail->find('first', array('fields' => array('UserDetail.*'), 'conditions' => array('UserDetail.user_id' => $userData['UserInstitution']['user_id'])));

					$userStatus = $this->User->find('first', array('fields' => array('User.status'), 'conditions' => array('User.id' => $userData['UserInstitution']['user_id'])));
				}
			}

			if ((isset($userData) && !empty($userData)) && (!empty($userStatus['User']['status']))) {
				//pr($userStatus);
				$associated_user_id = $userData['UserInstitution']['user_id'];
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
				$userDomainName = explode("@", $this->request->data['User']['email']);
				//$userDomain = explode(".",$userDomainName[1]);
				$userDomain = $userDomainName[1];

				//$checkOrganisationDomain = $this->checkOrgDomain($userDomain[0]);
				// ========== Updated on 26th July 2017 =======================
				$checkOrganisationDomain = $this->checkOrgDomain($userDomain);

				//echo $checkOrganisationDomain;

				if (!isset($checkOrganisationDomain) || empty($checkOrganisationDomain) || $checkOrganisationDomain <= 0) {

					$this->Session->setFlash("Supplied email domain does not allowed, please try again with valid email domain.", 'error');
					$_SESSION['data'] = $this->request->data;
					$this->redirect(array('controller' => 'users', 'action' => 'register'));

				}

				//die;

				if (isset($checkOrganisationDomain) && !empty($checkOrganisationDomain) && $checkOrganisationDomain > 0) {

					//if( $this->checkExpiredOrg($checkOrganisationDomain) ){

					/* if( !isset($checkOrganisationDomain) || empty($checkOrganisationDomain) ){

							$this->Session->setFlash("Supplied email domain does not allowed, please try again with valid email domain.",'error');
							$_SESSION['data'] = $this->request->data;
							$this->redirect(array('controller' => 'users', 'action' => 'register'));

						} */

					if (isset($checkOrganisationDomain) && !empty($checkOrganisationDomain)) {

						$_SESSION['data'] = $this->request->data;

						//

						$org_detail = $this->ManageDomain->findById($checkOrganisationDomain);

						//pr($org_detail); die;

						$this->request->data['UserDetail']['org_id'] = $org_detail['ManageDomain']['org_id'];

						$this->request->data['User']['managedomain_id'] = $checkOrganisationDomain;

						$orgPasswordPolicy = $this->OrgPassPolicy->find('first', array('conditions' => array('OrgPassPolicy.org_id' => $this->request->data['UserDetail']['org_id'])));

						if (isset($orgPasswordPolicy['OrgPassPolicy']) && !empty($orgPasswordPolicy['OrgPassPolicy'])) {

							if (strlen($this->request->data['User']['password']) < $orgPasswordPolicy['OrgPassPolicy']['min_lenght']) {

								$this->request->data['error']['password'] = "Password should be at least " . $orgPasswordPolicy['OrgPassPolicy']['min_lenght'] . " char";
								$_SESSION['data'] = $this->request->data;
								$this->redirect(array('controller' => 'users', 'action' => 'register'));

							} else if ($orgPasswordPolicy['OrgPassPolicy']['numeric_char'] == 1) {

								if (!preg_match('/[0-9]/', $this->request->data['User']['password'])) {

									$this->request->data['error']['password'] = "Password should have minimum one numeric char";
									$_SESSION['data'] = $this->request->data;
									$this->redirect(array('controller' => 'users', 'action' => 'register'));

								}

							} else if ($orgPasswordPolicy['OrgPassPolicy']['alph_char'] == 1) {

								if (!preg_match('/[a-zA-Z]/', $this->request->data['User']['password'])) {

									$this->request->data['error']['password'] = "Password should have minimum one alpha char";
									$_SESSION['data'] = $this->request->data;
									$this->redirect(array('action' => 'register', $this->request->data));

								}
							}

						}

					}

					/*}  else {
						echo "Orgnasation has been expired.";
						die;
					*/

				}

				//pr($this->request->data); die;

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
						$mongo = new MongoClient(MONGO_CONNECT);
						$this->mongoDB = $mongo->selectDB(MONGO_DATABASE);
						$mongo_collection = new MongoCollection($this->mongoDB, 'users');
					}

					$sql = "SELECT u.id,u.email,u.password,ud.first_name as firstname,ud.last_name as lastname, ud.profile_pic as thumb,ud.department, ud.address,ud.job_title, ud.job_role, ud.bio, c.countryName as country_name, c.countryCode as country_code, timezones.name as timezone_name, timezones.timezone as timezone_offset FROM users as u LEFT JOIN user_details as ud ON u.id=ud.user_id LEFT JOIN countries as c ON c.countryCode=ud.country_id LEFT JOIN timezones ON u.id=timezones.user_id WHERE u.id =" . $userId;

					$user_result = $this->User->query($sql);
					$this->loadModel('Timezone');
					//$loggedInTimzone = $this->Timezone->find('first', array('conditions'=>array('Timezone.user_id'=>63 )));

					if ($this->live_setting == true) {

						$datetime = new MongoDate(strtotime(date('Y-m-d h:i:s')));

						// INSERT USER DATA TO MONGO
						$ret = $mongo_collection->save(['id' => intval($userId, 10),

							'email' => strip_tags($user_result[0]['u']['email']),
							'password' => strip_tags($user_result[0]['u']['password']),
							'firstname' => strip_tags($user_result[0]['ud']['firstname']),
							'lastname' => strip_tags($user_result[0]['ud']['lastname']),
							'thumb' => strip_tags($user_result[0]['ud']['thumb']),
							'department' => strip_tags($user_result[0]['ud']['department']),
							'address' => strip_tags($user_result[0]['ud']['address']),
							'jobTitle' => strip_tags($user_result[0]['ud']['job_title']),
							'jobRole' => strip_tags($user_result[0]['ud']['job_role']),
							'bio' => strip_tags($user_result[0]['ud']['bio']),
							'contacts' => array(),
							'datetime' => $datetime,
							'session' => array(
								'datetime' => $datetime,
								'organisationId' => intval(1),
								'projectId' => intval(1),
								'projects' => array(),
							),
							'visibility' => 'online',
							'status' => 'offline',
							'contacts' => array(),
							'timezone' => array(
								'name' => 'Asia/Calcutta',
								'offset' => '-330',
							),
							'country' => array(
								'name' => strip_tags($user_result[0]['c']['country_name']),
								'code' => strip_tags($user_result[0]['c']['country_code']),
							),
						]);

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
					$datns = array_keys($this->questionArray, $this->request->data['UserDetail']['question']);
					$this->request->data['UserDetail']['question'] = $datns['0'];
					// pr($this->request->data['UserDetail']['question']); die;
					$this->request->data = $this->request->data;
				}
			}
			//  $this->Session->setFlash(__('The user could not be saved. Please, try again.'));
		}
	}

	public function __sendEmailConfirm($useData, $lastInsertID, $activatiinHash) {
		//$user = $this->User->find('first',array('conditions'=>array('User.id' => $user_id),'fields'=>array('User.id','User.email'),'recursive'=>0));

		if (!count($useData)) {
			debug(__METHOD__ . " failed to retrieve User data for user.id: {$user_id}");
			return false;
		}
		$activate_url = SITEURL . 'users/activate/' . $lastInsertID . '/' . $activatiinHash;
		$name = $useData['UserDetail']['first_name'] . ' ' . $useData['UserDetail']['last_name'];

		$email = new CakeEmail();
		$email->config('Smtp');
		//$email->config('Smtp');
		$email->from(array(ADMIN_EMAIL => SITENAME));
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

		$this->set('title_for_layout', __('Login', true));

		if ($this->Session->read('Auth.User.id') > 0 && $this->Session->read('Auth.User.role_id') == 2) {
			return $this->redirect(SITEURL . 'dashboards/project_center');
		} else if ($this->Session->read('Auth.User.id') > 0 && $this->Session->read('Auth.User.role_id') == 3) {
			//return $this->redirect(SITEURL . 'organisations/manage_users');
			return $this->redirect(SITEURL . 'organisations/dashboard');
		} else if ($this->Session->read('Auth.User.role_id') == 1) {
			return $this->redirect(SITEURL . 'templates/create_workspace/0/');
		}

		if ($this->request->is('post')) {

			$whatINeed = explode('.ideascast.com', $_SERVER['HTTP_HOST']);
			$whatINeed = $whatINeed[0];

			//echo $whatINeed; die;

			$userEmail = $this->request->data['User']['email'];
			if ($whatINeed == 'prod') {

				$userlDetail13 = $this->User->find('first', array('conditions' => array('User.email' => $userEmail, 'User.role_id !=' => 3)));
				if (count($userlDetail13) == 0) {

					$this->Session->setFlash(__('Sign in details are incorrect please retry.', 'error'));
					$_SESSION['LoginDetails'] = $this->request->data;
					return $this->redirect(array('controller' => 'Users', 'action' => 'login', 'admin' => false));
				}

			} else {

				$userlDetail13 = $this->User->find('first', array('conditions' => array('User.email' => $userEmail)));

			}

			//pr($userlDetail13);die;
			// Organisation can not login into main site, he can login only his domain

			if (isset($userlDetail13['OrganisationUser']) && !empty($userlDetail13['OrganisationUser']['domain_name'])) {

				$orgDomain = $this->OrgSetting->find('first', array('conditions' => array('OrgSetting.user_id' => $userlDetail13['User']['id'], 'OrgSetting.subdomain' => $whatINeed)));

				if (isset($orgDomain) && empty($orgDomain) && $userlDetail13['User']['role_id'] == 3) {
					$this->Session->setFlash(__('Sign in details are incorrect please retry.', 'error'));
					$_SESSION['LoginDetails'] = $this->request->data;
					return $this->redirect(array('controller' => 'Users', 'action' => 'login', 'admin' => false));
				}

			}

			if (!empty($userlDetail13['OrganisationUser']['creator_id']) && !empty($userlDetail13['OrganisationUser']['domain_name'])) {

				$this->Session->setFlash(__('Sign in details are incorrect please retry.', 'error'));
				$_SESSION['LoginDetails'] = $this->request->data;
				return $this->redirect(array('controller' => 'Users', 'action' => 'login', 'admin' => false));

			}

			if (isset($userlDetail13['UserDetail']['org_id']) && !empty($userlDetail13['UserDetail']['org_id'])) {

				$orgPasswordPolicy = $this->OrgPassPolicy->find('first', array('conditions' => array('OrgPassPolicy.org_id' => $userlDetail13['UserDetail']['org_id'])));

				$checkLoginAttempt = $this->PasswordLockout->find('first', array('conditions' => array('PasswordLockout.user_id' => $userlDetail13['User']['id'])));

				$currentTime = date('Y-m-d h:i:s');

				if (isset($checkLoginAttempt['PasswordLockout']['updated'])) {

					$existingtime = $checkLoginAttempt['PasswordLockout']['updated'];

					$datetime1 = strtotime($existingtime);
					$datetime2 = strtotime($currentTime);
					$interval = abs($datetime2 - $datetime1);
					$minutes = round($interval / 60);

					if ((isset($orgPasswordPolicy) && !empty($orgPasswordPolicy['OrgPassPolicy']['lockout_period'])) && (isset($checkLoginAttempt) && !empty($checkLoginAttempt['PasswordLockout']))) {
						if ($checkLoginAttempt['PasswordLockout']['attempt_status'] == 0 && $checkLoginAttempt['PasswordLockout']['login_attempt'] == $orgPasswordPolicy['OrgPassPolicy']['temp_lockout'] && $minutes < $orgPasswordPolicy['OrgPassPolicy']['lockout_period']) {

							$this->Session->setFlash(__('Your account has locked for security reson, so please try after some time.', 'error'));
							$_SESSION['LoginDetails'] = $this->request->data;
							return $this->redirect(array('controller' => 'Users', 'action' => 'login', 'admin' => false));
						}
					}
				}
			}

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
					// CONNECT WITH MONGO DB
					$mongo = new MongoClient(MONGO_CONNECT);
					$this->mongoDB = $mongo->selectDB(MONGO_DATABASE);

					// mongoDB update/insert
					$mongo_collection = new MongoCollection($this->mongoDB, 'users');
					$ret = $mongo_collection->update(
						[
							'id' => intval($this->Session->read('Auth.User.id'), 10),
						],
						[
							'$set' =>
							[
								'is_mobile' => $is_mobile,
							],
						]
					);

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

				if ($this->live_setting == true) {
					$HttpSocket = new HttpSocket();
					//$results = $HttpSocket->get('http://jeera.ideascast.com:90/user/'.$this->Auth->user('id').'/login');
				}

				unset($_SESSION['LoginDetails']);

				$results = $this->User->find('first', array(
					'conditions' => array('User.email' => $this->Auth->user('email')),
					'fields' => array('User.status', 'User.modified'),
				));

				$this->Session->write('Auth.User.mm_time', time());

				if (empty($results['User']['status']) || ($results['User']['status'] == 0)) {
					// User has not confirmed account
					//$this->Session->setFlash('Your account has not been activated.');
					$this->Session->setFlash('Your account is not currently activated');
					return $this->redirect($this->Auth->logout());
					$this->redirect(array('action' => 'login'));
				} else {

					if (isset($results) && !empty($results)) {

						// =====================================================
						//			LOGIN ATTEMPT SUCCESSFULL
						// =====================================================

						$checkLoginAttempt = $this->PasswordLockout->find('first', array('conditions' => array('PasswordLockout.user_id' => $results['User']['id'])));
						if (isset($checkLoginAttempt) && count($checkLoginAttempt) > 0) {

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
										$this->Session->setFlash('Your password has been expired, please reset again.');
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
					$landing_controller = 'dashboards';
					$landing_action = 'project_center';
					$userStartPageData = $this->User->find('first', ['conditions' => ['User.id' => $this->Auth->user('id')], 'recursive' => -1]);
					$page_setting_toggle = (isset($userStartPageData) && !empty($userStartPageData)) ? $userStartPageData['User']['page_setting_toggle'] : 0;
					$landing_url = (isset($userStartPageData) && !empty($userStartPageData)) ? $userStartPageData['User']['landing_url'] : null;

					$this->loadModel('ProjectStat');

					$data = $this->ProjectStat->updateAll(array('ProjectStat.status' => 0), array('ProjectStat.user_id' => $this->Auth->user('id')));

					if (isset($page_setting_toggle) && !empty($page_setting_toggle)) {
						if (isset($landing_url) && !empty($landing_url)) {
							$landing_url = explode('/', $landing_url);
							$landing_controller = $landing_url[0];
							$landing_action = $landing_url[1];

							return $this->redirect(array('controller' => $landing_controller, 'action' => $landing_action, 'admin' => false));

						} else {
							return $this->redirect(array('controller' => 'dashboards', 'action' => 'project_center', 'admin' => false));
						}
					} else {
						return $this->redirect($this->Auth->redirectUrl());

					}

					//}
				}

				return $this->redirect($this->Auth->redirectUrl());
			}
			$_SESSION['LoginDetails'] = array();
			$userEmail = $this->request->data['User']['email'];
			$userDomainName = explode("@", $userEmail);
			$userDomain = explode(".", $userDomainName[1]);
			$checkOrganisationDomain = $this->checkOrgDomain($userDomain[0]);
			//pr($checkOrganisationDomain); die;

			if ($checkOrganisationDomain > 0) {

				$orgPasswordPolicy = $this->OrgPassPolicy->find('first', array('conditions' => array('OrgPassPolicy.org_id' => $checkOrganisationDomain)));

				$userlDetail = $this->User->find('first', array('conditions' => array('User.email' => $userEmail)));

				if (isset($userlDetail) && !empty($userlDetail)) {

					$checkLoginAttempt = $this->PasswordLockout->find('first', array('conditions' => array('PasswordLockout.user_id' => $userlDetail['User']['id'])));

				} else {
					$checkLoginAttempt = array();
				}

				//pr($orgPasswordPolicy); die;

				if (isset($checkLoginAttempt) && count($checkLoginAttempt) > 0) {

					if ($orgPasswordPolicy['OrgPassPolicy']['temp_lockout'] > $checkLoginAttempt['PasswordLockout']['login_attempt']) {

						$this->PasswordLockout->id = $checkLoginAttempt['PasswordLockout']['id'];
						$this->PasswordLockout->saveField('attempt_status', (int) 0);
						$this->PasswordLockout->saveField('login_attempt', (int) $this->PasswordLockout->field('login_attempt') + 1);
						$this->PasswordLockout->saveField('attempt_count', (int) $this->PasswordLockout->field('attempt_count') + 1);

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
					}

				}

			}

			$this->Session->setFlash(__('Sign in details are incorrect please retry', 'error'));
		}

		//$this->Session->setFlash(__('Unauthorised access!', 'error'));
		//return $this->redirect(SITEURL);
	}

	public function logout() {

		/*if( $this->live_setting == true ) {
			$HttpSocket = new HttpSocket();
			$results = $HttpSocket->get(CHATURL.'/user/'.$this->Auth->user('id').'/logout');
		}*/

		$past = time() - 3600;
		if (isset($_COOKIE) && !empty($_COOKIE)) {
			foreach ($_COOKIE as $key => $value) {
				setcookie($key, $value, $past, '/');
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

		$this->Session->setFlash(__('Logout Successful.'), 'success');
		return $this->redirect($this->Auth->logout());
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

			$this->UserPlan->recursive = 2;

			$this->UserPlan->bindModel(array('belongsTo' => array('Plan', 'PlanType' => array('className' => 'PlanType', 'foreignKey' => 'plan_type'))));

			$userplan = $this->UserPlan->find('all', array('conditions' => array('UserPlan.user_id' => $this->User->id, 'UserPlan.plan_id !=' => 0, 'UserPlan.is_active' => 1)));

			$this->set('userplan', $userplan);
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
		if (!empty($this->data)) {
			if (empty($this->data['User']['email'])) {
				$this->Session->setFlash('Please Provide Your Email Adress that You used to Register with Us');
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

							//============Email================//
							/* SMTP Options */
							/* $this->Email->smtpOptions = array(
								                              'port'=>'25',
								                              'timeout'=>'30',
								                              'host' => 'mail.example.com',
								                              'username'=>'accounts+example.com',
								                              'password'=>'your password'
								                              );
								                              $this->Email->template = 'resetpw';
								                              $this->Email->from    = 'Your Email <accounts@example.com>';
								                              $this->Email->to      = $fu['User']['name'].'<'.$fu['User']['email'].'>';
								                              $this->Email->subject = 'Reset Your Example.com Password';
								                              $this->Email->sendAs = 'both';

								                              $this->Email->delivery = 'smtp';
								                              $this->set('ms', $ms);
								                              $this->Email->send();
							*/

							//$this->set('ms', $ms);
							//$this->set('name', $fu['UserDetail']['first_name']);
							$email = new CakeEmail();
							$email->config('Smtp');
							$email->from(array(ADMIN_EMAIL => SITENAME));
							$email->to($fu['User']['email']);
							$email->subject(SITENAME . ': Password Change');
							$email->template('resetpw');
							$email->emailFormat('html');
							$email->viewVars(array('ms' => $ms, 'name' => $fu['UserDetail']['first_name']));
							$email->send();

							$this->Session->setFlash('Check Your Email To Reset your password', 'success');
							return $this->redirect(array('action' => 'forgetpwd'));
							//============EndEmail=============//
						} else {
							$this->Session->setFlash("Error Generating Reset link");
							// return $this->redirect(array('action' => 'forgetpwd'));
						}
					} else {
						$this->Session->setFlash('This Account is not Active yet.Check Your mail to activate it');
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

						$this->Session->setFlash('Incorrect details,please try again.');
						// return $this->redirect(array('action' => 'forgetpwd'));
					}
				} else {
					$this->Session->setFlash('Incorrect details,please try again.');
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
		if (!empty($token)) {
			$u = $this->User->findBytokenhash($token);
			if (isset($u) && !empty($u)) {
				$this->User->id = $u['User']['id'];

				if (!empty($this->data)) {
					$this->User->data = $this->data;
					$this->User->data['User']['email'] = $u['User']['email'];
					$this->User->data['User']['id'] = $u['User']['id'];
					$new_hash = sha1($u['User']['email'] . rand(0, 100)); //created token
					$this->User->data['User']['tokenhash'] = $new_hash;

					if ($this->User->validates(array('fieldList' => array('password', 'cpassword')))) {

						if ($this->User->save($this->User->data, false)) {
							$this->Session->setFlash('Password Has been Updated', 'success');
							//$this->redirect('/');
							$this->redirect(array('controller' => 'users', 'action' => 'login'));
						}
					} else {
						$this->set('errors', $this->User->invalidFields());
					}
				}
			} else {
				$this->Session->setFlash('Token Corrupted, Please Retry. The reset link work only for once.');
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

		$this->UserPlan->bindModel(array('belongsTo' => array('Plan', 'PlanType' => array('className' => 'PlanType', 'foreignKey' => 'plan_type'))));

		$userplan = $this->UserPlan->find('all', array('conditions' => array('UserPlan.user_id' => $this->User->id, 'UserPlan.plan_id !=' => 0, 'UserPlan.is_active' => 1)));

		$this->set('userplan', $userplan);
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

		if (isset($this->data['id']) && !empty($this->data['id'])) {
			$this->loadModel('Project');

			$id = $this->data['id'];

			$this->Project->id = $id;
			$user_id = $this->Auth->user('id');
			$this->set('user_id', $user_id);

			if (!$this->Project->exists()) {
				throw new NotFoundException(__('Invalid project'), 'error');
			}

			if ($this->Project->exists()) {
				if ($this->live_setting == true) {
					// CONNECT WITH MONGO DB
					$mongo = new MongoClient(MONGO_CONNECT);
					$this->mongoDB = $mongo->selectDB(MONGO_DATABASE);

					// mongoDB update/insert
					$mongo_collection = new MongoCollection($this->mongoDB, 'projects');
					$mongo_collection->remove(array('id' => (int) $id));
				}

				// ============== Start == Email which is send when project will be deleted ===============
				$projectdetail = $this->Project->findById($id);
				$project_name = '';
				if (isset($projectdetail['Project']['title']) && !empty($projectdetail['Project']['title'])) {
					$project_name = ucfirst(strip_tags($projectdetail['Project']['title']));
				}
				$this->projectDeleteEmail($project_name, $id);
				// ============== End == Email which is send when project will be deleted =================

				$this->loadModel('UserProject');
				$this->loadModel('ProjectWorkspace');

				$this->loadModel('Area');
				$this->loadModel('Workspace');
				$this->loadModel('Element');
				$this->loadModel('ElementLink');
				$this->loadModel('ElementDocument');
				$this->loadModel('Feedback');
				$this->loadModel('ElementMindmap');
				$this->loadModel('ElementNote');
				$this->loadModel('ElementDecision');

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
				$this->loadModel('ProjectSketchInterest');
				$this->loadModel('ProjectSketchParticipant');
				$this->loadModel('ProjectSketch');
				$this->loadModel('WorkspaceActivity');
				$this->loadModel('Activity');
				$this->loadModel('ProjectActivity');
				$this->loadModel('ProjectBoard');
				$this->loadModel('ProjectSkill');
				$this->loadModel('ProjectPropagate');
				$this->loadModel('WorkspacePropagate');
				$this->loadModel('ElementPropagate');
				$this->loadModel('Vote');
				$this->loadModel('WikiPageComment');

				$this->loadModel('ProjectProgram');

				$userasspro = $this->UserProject->find('all', array('fields' => array('UserProject.*'), 'conditions' => array('UserProject.user_id' => $user_id, 'UserProject.project_id' => $id, 'UserProject.owner_user' => 1)));

				if (isset($userasspro) && !empty($userasspro)) {

					$this->ElementPermission->deleteAll(array('ElementPermission.project_id' => $id), false);

					$this->Vote->deleteAll(array('Vote.project_id' => $id));

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

					$this->ProjectSketchInterest->deleteAll(array('ProjectSketchInterest.project_id' => $id));

					$this->ProjectSketchParticipant->deleteAll(array('ProjectSketchParticipant.project_id' => $id));

					$this->ProjectSketch->deleteAll(array('ProjectSketch.project_id' => $id));

					//$this->ProjectSkill->deleteAll(array('ProjectSkill.project_id' => $id));

					$this->ProjectActivity->deleteAll(array('ProjectActivity.project_id' => $id));

					$this->WorkspaceActivity->deleteAll(array('WorkspaceActivity.project_id' => $id));

					$this->Activity->deleteAll(array('Activity.project_id' => $id));

					$this->ProjectBoard->deleteAll(array('ProjectBoard.project_id' => $id));

					$usp = null;

					$this->ProjectProgram->deleteAll(array('ProjectProgram.project_id' => $id));

					$this->Project->delete($id);

					foreach ($userasspro as $usp) {

						$this->UserProject->delete($usp['UserProject']['id']);

						if (isset($usp) && !empty($usp)) {

							$ProjectWorkspace = $this->ProjectWorkspace->find('all', array('fields' => array('ProjectWorkspace.*'), 'conditions' => array('ProjectWorkspace.project_id' => $usp['UserProject']['project_id'])));

							foreach ($ProjectWorkspace as $wsp) {

								$this->ProjectWorkspace->delete($wsp['ProjectWorkspace']['id']);

								$this->Workspace->deleteAll(array('Workspace.id' => $wsp['ProjectWorkspace']['workspace_id']));

								$area = $this->Area->find('all', array('fields' => array('Area.*'), 'conditions' => array('Area.workspace_id' => $wsp['ProjectWorkspace']['workspace_id'], 'Area.studio_status !=' => 1)));

								foreach ($area as $ar) {

									$Element = $this->Element->find('all', array('fields' => array('Element.*'), 'conditions' => array('Element.area_id' => $ar['Area']['id'])));

									foreach ($Element as $elm) {

										$this->ElementLink->deleteAll(array('ElementLink.element_id' => $elm['Element']['id']));

										$this->ElementDecision->deleteAll(array('ElementDecision.element_id' => $elm['Element']['id']));

										$this->ElementDocument->deleteAll(array('ElementDocument.element_id' => $elm['Element']['id']));

										$this->Feedback->deleteAll(array('Feedback.element_id' => $elm['Element']['id']));

										$this->ElementNote->deleteAll(array('ElementNote.element_id' => $elm['Element']['id']));

										$this->ElementMindmap->deleteAll(array('ElementMindmap.element_id' => $elm['Element']['id'], 'ElementMindmap.user_id' => $user_id));
									}

									$this->Element->deleteAll(array('Element.area_id' => $ar['Area']['id']));
								}

								$this->Area->deleteAll(array('Area.workspace_id' => $wsp['ProjectWorkspace']['workspace_id']));

							}

							$this->ProjectWorkspace->deleteAll(array('ProjectWorkspace.project_id' => $id));
						}
					}

					die('success');
				} else {
					$this->Session->setFlash(__('Project could not deleted.'), 'error');
					die('error');
				}

			} else {
				$this->Session->setFlash(__('Project could not deleted.'), 'error');
			}
		}
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
				$this->UserPlan->deleteAll(array('UserPlan.user_id' => $id));

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

				$this->UserTransctionDetail->deleteAll(array('UserTransctionDetail.user_id' => $id));

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
				$this->UserPlan->deleteAll(array('UserPlan.user_id' => $id));
				$this->UserTransctionDetail->deleteAll(array('UserTransctionDetail.user_id' => $id));
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

				$this->UserPlan->deleteAll(array('UserPlan.user_id' => $id));
				$this->UserTransctionDetail->deleteAll(array('UserTransctionDetail.user_id' => $id));
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

		if ($_SERVER['SERVER_NAME'] != "prod.ideascast.com" && $_SERVER['SERVER_NAME'] != "192.168.4.29") {
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
		$this->Session->setFlash(__('Logout Successfully.'), 'success');
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
		$id = $this->Session->read('Auth.Admin.User.id');
		$detid = $this->Session->read('Auth.Admin.User.UserDetail.id');
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
				$userDetails = $this->UserDetail->find('first', array('conditions' => array('UserDetail.id' => $detid)));
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

	public function myaccountedit($id = null) {
		$this->layout = 'inner';
		$id = $this->Session->read('Auth.User.id');
		$detid = $this->Session->read('Auth.User.UserDetail.id');
		// pr($this->Session->read('Auth')); die;

		$this->set('title_for_layout', __('Edit Profile', true));
		$this->User->id = $id;
		$skills = array();
		$skillsAll = $this->Skill->query("SELECT * FROM skills");
		//pr($skillsAll); die;
		/* if (isset($skillsAll) && !empty($skillsAll)) {
			foreach ($skillsAll as $skil) {
				$skills[$skil['skill']['id']] = $skil['skill']['title'];
			}
		} */
		if (isset($skillsAll) && !empty($skillsAll)) {
			foreach ($skillsAll as $skil) {

				$skills[$skil['skills']['id']] = $skil['skills']['title'];
			}

		}

		$this->loadModel('UserPassword');
		//$skills = $this->Skill->find("list", array("conditions" => array("Skill.status" => 1), 'order' => 'Skill.title ASC'));

		if (isset($skills) && !empty($skills)) {
			$this->set("skills", $skills);
		}

		//pr($skills); die;

		if (!$this->User->exists()) {
			$this->Session->setFlash(__('Invalid User.'), 'error');
			$this->redirect(array('action' => 'index'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {

			// Updated 21th Nov 2016
			$userDomainName = explode("@", $this->request->data['User']['email']);
			$userDomain = explode(".", $userDomainName[1]);

			$checkOrganisationDomain = $this->checkOrgDomain($userDomain[0]);

			if (isset($checkOrganisationDomain) && !empty($checkOrganisationDomain)) {

				$_SESSION['data'] = $this->request->data;
				$this->request->data['UserDetail']['org_id'] = $this->checkOrgDomain($userDomain[0]);

				$orgPasswordPolicy = $this->OrgPassPolicy->find('first', array('conditions' => array('OrgPassPolicy.org_id' => $this->request->data['UserDetail']['org_id'])));

				//pr($orgPasswordPolicy); die;
				if (isset($orgPasswordPolicy['OrgPassPolicy']) && !empty($orgPasswordPolicy['OrgPassPolicy'])) {
					if (strlen($this->request->data['User']['password']) < $orgPasswordPolicy['OrgPassPolicy']['min_lenght']) {

						$this->request->data['error']['password'] = "Password should be at least " . $orgPasswordPolicy['OrgPassPolicy']['min_lenght'] . " char";

						$_SESSION['data'] = $this->request->data;
						$this->redirect(array('controller' => 'users', 'action' => 'myaccountedit', $id));

					} else if ($orgPasswordPolicy['OrgPassPolicy']['numeric_char'] == 1) {

						if (!preg_match('/[0-9]/', $this->request->data['User']['password'])) {

							$this->request->data['error']['password'] = "Password should have minimum one numeric char";

							$_SESSION['data'] = $this->request->data;
							$this->redirect(array('controller' => 'users', 'action' => 'myaccountedit', $id));

						}

					} else if ($orgPasswordPolicy['OrgPassPolicy']['alph_char'] == 1) {

						if (!preg_match('/[a-zA-Z]/', $this->request->data['User']['password'])) {

							$this->request->data['error']['password'] = "Password should have minimum one alpha char";

							$_SESSION['data'] = $this->request->data;
							$this->redirect(array('controller' => 'users', 'action' => 'myaccountedit', $id));

						}
					}
				}

				$pans = AuthComponent::password($this->data['User']['password']);
				$this->request->data['UserPassword']['password'] = $pans;
			}

			//pr($this->request->data); die;

			if ($this->User->saveAssociated($this->request->data, array('deep' => true))) {

				$users = $this->User->find('first', array('conditions' => array('User.id' => $id)));
				$userDetails = $this->UserDetail->find('first', array('conditions' => array('UserDetail.id' => $detid)));
				$users['User']['UserDetail'] = $userDetails['UserDetail'];

				if (isset($this->request->data['User']['password']) && !empty($this->request->data['User']['password'])) {
					$this->changePasswordEmail($userDetails, $users['User']['email']);
				}

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
				} else {
					$this->redirect(array('action' => 'lists', 'controller' => 'projects'));
				}
			}
		} else {
			unset($_SESSION['data']);
			$this->request->data = $this->User->read(null, $id);
			if (isset($this->request->data['User']['password'])) {
				unset($this->request->data['User']['password']);
			}
		}
	}

	//== Change Password Email =========================
	public function changePasswordEmail($useData = null, $userEmail = null) {

		if (isset($useData) && isset($userEmail) && !empty($userEmail)) {
			$name = $useData['UserDetail']['first_name'] . ' ' . $useData['UserDetail']['last_name'];

			$email = new CakeEmail();
			$email->config('Smtp');
			$email->from(array(ADMIN_EMAIL => SITENAME));
			$email->to($userEmail);
			$email->subject(SITENAME . ': Password Change');
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

					if (isset($_SESSION['data']) && !empty($_SESSION['data'])) {
						unset($_SESSION['data']);
					}

					$_SESSION['data'] = $this->request->data;
					$this->request->data['UserDetail']['org_id'] = $this->checkOrgDomain($userDomain);

					$orgPasswordPolicy = $this->OrgPassPolicy->find('first', array('conditions' => array('OrgPassPolicy.org_id' => $this->request->data['UserDetail']['org_id'])));

					//pr($orgPasswordPolicy); die;
					//================================================================================
					if (isset($orgPasswordPolicy['OrgPassPolicy']['pass_repeat'])) {
						$previousPassword = $this->checkUserPrePassword($id, $orgPasswordPolicy['OrgPassPolicy']['pass_repeat']);
					}
					$newPassword = AuthComponent::password(trim($this->request->data['User']['password']));

					if (isset($orgPasswordPolicy['OrgPassPolicy']['min_lenght']) && strlen($this->request->data['User']['password']) < $orgPasswordPolicy['OrgPassPolicy']['min_lenght']) {

						if (isset($_SESSION['data']) && !empty($_SESSION['data'])) {

							unset($_SESSION['data']);

						}

						$this->request->data['error']['password'] = "Password should be at least " . $orgPasswordPolicy['OrgPassPolicy']['min_lenght'] . " char";

						$_SESSION['data'] = $this->request->data;
						$this->redirect(array('controller' => 'users', 'action' => 'changepassword'));

					} else if (isset($orgPasswordPolicy['OrgPassPolicy']['numeric_char']) && $orgPasswordPolicy['OrgPassPolicy']['numeric_char'] == 1) {

						if (isset($_SESSION['data']) && !empty($_SESSION['data'])) {

							unset($_SESSION['data']);
							//pr($_SESSION['data']);
						}

						if (!preg_match('/[0-9]/', $this->request->data['User']['password'])) {

							$this->request->data['error']['password'] = "Password should have minimum one numeric char";

							$_SESSION['data'] = $this->request->data;
							$this->redirect(array('controller' => 'users', 'action' => 'changepassword', $id));

						}

					} else if (isset($orgPasswordPolicy['OrgPassPolicy']['alph_char']) && $orgPasswordPolicy['OrgPassPolicy']['alph_char'] == 1) {

						if (isset($_SESSION['data']) && !empty($_SESSION['data'])) {

							unset($_SESSION['data']);
						}

						//	pr($this->request->data);die;
						if (!preg_match('/[a-zA-Z]/', $this->request->data['User']['password'])) {

							$this->request->data['error']['password'] = "Password should have minimum one alpha char";

							$_SESSION['data'] = $this->request->data;
							$this->redirect(array('controller' => 'users', 'action' => 'changepassword', $id));

						}

					}
					if (isset($orgPasswordPolicy['OrgPassPolicy']['pass_repeat'])) {
						if (in_array($newPassword, $previousPassword)) {

							$this->request->data['error']['password'] = "Password should not be from your last " . $orgPasswordPolicy['OrgPassPolicy']['pass_repeat'] . " passwords";

							$_SESSION['data'] = $this->request->data;
							$this->redirect(array('controller' => 'users', 'action' => 'changepassword', $id));

						}
					}
					//====================================================================================

					$pans = AuthComponent::password($this->data['User']['password']);
					$this->request->data['UserPassword']['password'] = $pans;

					if ($this->User->saveAssociated($this->request->data, array('deep' => true))) {

						//== Change password ===============================
						$users = $this->User->find('first', array('conditions' => array('User.id' => $id)));
						$userDetails = $this->UserDetail->find('first', array('conditions' => array('UserDetail.id' => $detid)));
						$this->changePasswordEmail($userDetails, $users['User']['email']);
						//==================================================

						$this->Session->setFlash(__('Your password has been updated successfully.'), 'success');
						unset($_SESSION['data']);
						$this->redirect(array('action' => 'lists', 'controller' => 'projects'));
					}
				}

			} else {

				unset($this->request->data['userid']);
				unset($this->request->data['email']);
				unset($this->request->data['password']);
				unset($this->request->data['organisation_id']);
				unset($this->request->data['project_id']);
				unset($this->request->data['msg']);
				//pr($this->request->data); die;
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
			$this->User->id = $this->Session->read('Auth.User.id');
			$this->UserDetail->id = $this->Session->read('Auth.User.UserDetail.id');
			$this->User->id = $this->Session->read('Auth.User.id');
			$id = $this->Session->read('Auth.User.id');
			$detid = $this->Session->read('Auth.User.UserDetail.id');
			if (isset($this->data['value']) && !empty($this->data['value']) && $this->data['id'] == 'UserDetailProfilePic') {
				$this->request->data['UserDetail']['profile_pic'] = '';
			} else if (isset($this->data['value']) && !empty($this->data['value']) && $this->data['id'] == 'UserDetailDocumentPic') {
				$this->request->data['UserDetail']['document_pic'] = '';
			}
			//pr($this->request->data); die;
			if ($this->UserDetail->save($this->request->data)) {
				if (isset($this->data['value']) && !empty($this->data['value']) && $this->data['id'] == 'UserDetailProfilePic') {
					$profile = $this->Session->read('Auth.User.UserDetail.profile_pic');
					$profiles = SITEURL . USER_PIC_PATH . $profile;
					if (!empty($profile) && file_exists(USER_PIC_PATH . $profile)) {
						$profiles = WWW_ROOT . USER_PIC_PATH . $profile;
						unlink($profiles);
					}
				} else if (isset($this->data['value']) && !empty($this->data['value']) && $this->data['id'] == 'UserDetailDocumentPic') {
					$docimg = $this->Session->read('Auth.User.UserDetail.document_pic');
					$docimgs = SITEURL . USER_PIC_PATH . $docimg;
					if (!empty($docimg) && file_exists(USER_PIC_PATH . $docimg)) {
						$docimgs = WWW_ROOT . USER_PIC_PATH . $docimg;
						unlink($docimgs);
					}
				}

				if ($this->live_setting == true) {
					//$HttpSocket = new HttpSocket();

					$HttpSocket = new HttpSocket([
						'ssl_verify_host' => false,
						'ssl_verify_peer_name' => false,
						'ssl_verify_peer' => false,
					]);

					$results = $HttpSocket->get(CHATURL . '/user/' . $this->Auth->user('id') . '/update');

				}

				$users = $this->User->find('first', array('conditions' => array('User.id' => $id)));
				$userDetails = $this->UserDetail->find('first', array('conditions' => array('UserDetail.id' => $detid)));
				$users['User']['UserDetail'] = $userDetails['UserDetail'];

				$this->Session->write('Auth', $users);
				$this->Session->setFlash(__('The Profile has been updated successfully.'), 'success');
				die('success');
			}
		}
	}

	public function profile() {
		$id = $this->Session->read('Auth.User.id');
		$detid = $this->Session->read('Auth.User.UserDetail.id');
		//pr($this->Session->read('Auth.User'));
		//	$this->layout= false;
		App::import("Model", "User");
		$this->User = new User();

		$this->User->id = $this->Session->read('Auth.User.id');

		if (!$this->User->exists()) {
			$this->Session->setFlash(__('Invalid User Profile.'), 'error');
			die('error');
		}
		//	print_r($this->request->data );// die;

		if ($this->request->is('post') || $this->request->is('put')) {

			// pr($this->request->data); die;

			if ($this->UserDetail->save($this->request->data)) {

				if ($this->live_setting == true) {
					//$HttpSocket = new HttpSocket();

					$HttpSocket = new HttpSocket([
						'ssl_verify_host' => false,
						'ssl_verify_peer_name' => false,
						'ssl_verify_peer' => false,
					]);
					$results = $HttpSocket->get(CHATURL . '/user/' . $this->Auth->user('id') . '/update');
				}

				$users = $this->User->find('first', array('conditions' => array('User.id' => $id)));
				$userDetails = $this->UserDetail->find('first', array('conditions' => array('UserDetail.id' => $detid)));
				if (isset($userDetails['UserDetail']) && !empty($userDetails['UserDetail'])) {
					$users['User']['UserDetail'] = $userDetails['UserDetail'];
				}

				$this->Session->write('Auth', $users);

				//$this->Session->setFlash(__('The Profile has been updated successfully.'), 'success');
				die('success');
			}
			//pr($this->UserDetail->validationErrors);die;
		} else {
			$this->request->data = $this->User->read(null, $id);
			if (isset($this->request->data['User']['password'])) {
				unset($this->request->data['User']['password']);
			}
		}
	}

	public function admin_get_state_city() {
		$this->autoRender = false;
		$this->loadModel('State');
		$loadType = $_POST['loadType'];
		$loadId = $_POST['loadId'];
		if ($loadType == "state") {
			$sql = $this->State->find('all', array('conditions' => array('country_iso_code' => $loadId), 'fields' => array('id', 'name'), 'order' => 'name ASC'));
			if (count($sql) > 0) {
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

	public function test() {

	}

	public function authback() {

	}

	public function checkDomain($domain_name) {
		$domain = $this->OrganisationUser->find('first', array('conditions' => array('domain_name' => $domain_name)));

		if (isset($domain) && count($domain['OrganisationUser']) > 0) {
			return $domain['OrganisationUser']['user_id'];
		}
	}

	public function checkOrgDomain($domain_name = null) {

		$domain = $this->ManageDomain->find('first', array('conditions' => array('domain_name' => $domain_name, 'create_account' => 1)));

		if (isset($domain) && (!empty($domain['ManageDomain']) && count($domain['ManageDomain']) > 0)) {

			//return $domain['ManageDomain']['org_id'];
			return $domain['ManageDomain']['id'];

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

					$response = ['success' => false, 'content' => "https://$domainNameorg.ideascast.com domain has already been taken"];

				} else if ($checkSpecialcharAndWspc > 0) {

					$response = ['success' => false, 'content' => "https://$domainNameorg.ideascast.com domain is not valid, please choose another domain."];

				} else {

					$response = ['success' => true, 'content' => "https://$domainNameorg.ideascast.com domain is available"];

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

		return array_values($previousPassword);

	}

	public function projects($project_id = null) {

		$this->layout = 'resources';

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

		$cat_crumb = get_category_list($project_id);

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

		if (isset($cat_crumb) && !empty($cat_crumb)) {
			$crumb = array_merge($cat_crumb, $crumb);
		}

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
		if (isset($data2) && count($data2) > 0) {
			$all_owner = array_unique(array_merge($data1, $data3, $data2));
		} else {
			$all_owner = array_unique(array_merge($data1, $data3));
		}

		$userlist = '';

		$all_owner = array_filter($all_owner);

		//pr($all_owner); die;

		foreach ($all_owner as $key => $valData) {

			if (isset($valData) && !empty($valData)) {

				$this->User->unbindModel(
					['hasOne' => ['UserInstitution', 'OrganisationUser']]
				);
				$this->User->unbindModel(
					['hasAndBelongsToMany' => 'Skill']
				);
				$this->User->unbindModel(
					['hasMany' => ['ProjectPermission', 'WorkspacePermission', 'ElementPermission', 'UserProject', 'UserPlan', 'UserTransctionDetail', 'UserSetting', 'UserPassword']]
				);

				$usersDetails = $this->User->find('first', array('conditions' => array('User.id' => $valData)));

				$owner_name = $usersDetails['UserDetail']['first_name'] . ' ' . $usersDetails['UserDetail']['last_name'];

				$dasAnnotate = $this->EmailNotification->find('first', ['conditions' => ['notification_type' => 'project', 'personlization' => 'project_deleted', 'user_id' => $valData]]);

				if ((!isset($dasAnnotate['EmailNotification']['email']) || $dasAnnotate['EmailNotification']['email'] == 1) && (!isset($usersDetails['User']['email_notification']) || $usersDetails['User']['email_notification'] == 1)) {

					$email = new CakeEmail();
					$email->config('Smtp');
					$email->from(array(ADMIN_EMAIL => SITENAME));
					$email->to($usersDetails['User']['email']);
					$email->subject(SITENAME . ': Project Deleted');
					$email->template('project_delete_email');
					$email->emailFormat('html');
					$email->viewVars(array('project_name' => $projectname, 'owner_name' => $owner_name, 'deletedby' => $projectDeletedUser));
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

	public function validate_email() {

		$this->layout = 'ajax';
		$this->autoRender = false;
		$email = $this->request->data['email'];
		$userDomainName = explode("@", $email);
		$userDomain = $userDomainName[1];

		$checkOrganisationDomain = $this->checkOrgDomain($userDomain);

		if (!isset($checkOrganisationDomain) || empty($checkOrganisationDomain) || $checkOrganisationDomain <= 0) {
			echo "false";
		} else {

			$this->User->recursive = -1;
			$checkEmail = $this->User->find('first', array('conditions' => array('User.email' => $email)));

			if (isset($checkEmail) && !empty($checkEmail['User']['email'])) {
				echo "false";
			} else {
				echo "true";
			}

		}
	}

}
