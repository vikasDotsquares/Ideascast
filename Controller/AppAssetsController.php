<?php

/**

 * AppAssets controller.

 *

 * This file will render views from views/AppAssets/

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

class AppAssetsController extends AppController {

	var $name = 'AppAssets';

	public $uses = ['User', 'UserDetail', 'ProjectPermission', 'UserSetting', 'Category', 'Aligned', 'UserProject', 'Project', 'Workspace', 'Area', 'ProjectWorkspace', 'Element', 'ProjectGroup', 'ProjectGroupUser', 'Skill', "ProjectSkill", "ProjectComment", "ProjectRag", "EmailNotification", "ProjectProgram", "Program","RewardCharity","UserPermission"];

	public $components = array('Email', 'Common', 'Image', 'CommonEmail', 'Auth', 'Group');

	public $live_setting;

	public $mongoDB = null;

	/**

	 * Helpers

	 *

	 * @var array

	 */

	public $helpers = array('Html', 'Form', 'Session', 'Time', 'Text', 'Common', 'Group', 'Wiki', 'User', 'ViewModel','Permission');

	public function beforeFilter() {

		parent::beforeFilter();

		//$this->Auth->allow('index', 'admin_add','admin_generate_keygen');
		$this->Auth->allow();
		$view = new View();
		$this->objView = $view;
		$this->live_setting = LIVE_SETTING;

		if( $_SERVER['HTTP_HOST'] != LOCALIP )  {

			if( $this->Session->read('Auth.User.role_id') == 2 ){
				$this->redirect(['controller' => 'projects','action' => 'lists']);
			}
			if( $this->Session->read('Auth.User.role_id') == 1 ){
				$this->redirect(['controller' => 'dashboard','action' => 'index']);
			}
		}

	}

	public function index() {

		$this->layout = 'inner';
		$this->set('title_for_layout', __('Assets', true));

		/* $myprojects = myprojects($this->Session->read('Auth.User.id'));
		$groupproject = groupprojects($this->Session->read('Auth.User.id'));
		$receivedproject = receivedprojects($this->Session->read('Auth.User.id'));
		$myprojects = (isset($myprojects) && !empty($myprojects)) ? $myprojects : [];
		$groupproject = (isset($groupproject) && !empty($groupproject)) ? $groupproject : [];
		$receivedproject = (isset($receivedproject) && !empty($receivedproject)) ? $receivedproject : [];
		$allProjects = $myprojects + $groupproject + $receivedproject; */


		$this->UserProject->unbindModel(array('hasMany' => array('ProjectPermission')), true);
		$this->UserProject->unbindModel(array('belongsTo' => array('User')), true);
		$allProjects = $this->UserProject->find('all', array('conditions'=>array('UserProject.project_id !=' => '','Project.id !=' => ''),'recursive'=>1, 'order'=>'title ASC'  ) );
		$this->set('allProjects', $allProjects);

	}

	public function projectsapi() {

		if ($this->request->isAjax()) {
			$this->layout = 'ajax';

			if ($this->request->is('post') || $this->request->is('put')) {

				if (isset($this->request->data['project_id']) && !empty($this->request->data['project_id'])) {
					$project_id = $this->request->data['project_id'];
					$data = $this->Project->find('first', array('conditions' => array('Project.id' => $project_id)));
					if (isset($data) && !empty($data)) {
						$this->set('projectdata', $data);
						$this->render('/AppAssets/partials/projectsapi');
					}
				}
			}

		}

	}

	public function elementoptions() {

		if ($this->request->isAjax()) {
			$this->layout = 'ajax';

			if ($this->request->is('post') || $this->request->is('put')) {

				if (isset($this->request->data['project_id']) && !empty($this->request->data['project_id']) && isset($this->request->data['workspace_id']) && !empty($this->request->data['workspace_id'])) {

					$project_id = $this->request->data['project_id'];
					$workspace_id = $this->request->data['workspace_id'];
					$areaconditions = '';
					if (isset($this->request->data['area_id']) && !empty($this->request->data['area_id'])) {
						$area_id = $this->request->data['area_id'];
						$areaconditions = array('Area.id' => $area_id);
					}

					$areas = $this->Area->find('all', ['conditions' => [
						'Area.workspace_id' => $workspace_id, $areaconditions,
					],
						'fields' => ['Area.id'],
						'recursive' => -1,
					]);

					// pr($areas);die;
					if (isset($areas) && !empty($areas)) {
						$this->set('areadata', $areas);
						$this->set('project_id', $project_id);
						$this->set('workspace_id', $workspace_id);
						$this->render('/AppAssets/partials/elementoptions');
					}
				}
			}

		}

	}

	public function areaoptions() {

		if ($this->request->isAjax()) {
			$this->layout = 'ajax';

			if ($this->request->is('post') || $this->request->is('put')) {

				if (isset($this->request->data['project_id']) && !empty($this->request->data['project_id']) && isset($this->request->data['workspace_id']) && !empty($this->request->data['workspace_id'])) {

					$project_id = $this->request->data['project_id'];
					$workspace_id = $this->request->data['workspace_id'];

					$areas = $this->Area->find('all', ['conditions' => [
						'Area.workspace_id' => $workspace_id,
					],
						'fields' => ['Area.id', 'Area.title'],
						'recursive' => -1,
					]);

					if (isset($areas) && !empty($areas)) {
						$this->set('areadata', $areas);
						$this->set('project_id', $project_id);
						$this->set('workspace_id', $workspace_id);
						$this->render('/AppAssets/partials/areaoptions');
					}
				}
			}

		}

	}

	public function userdata() {

		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			$this->autoRender = false;

			if ($this->request->is('post') || $this->request->is('put')) {

				if (isset($this->request->data['project_id']) && !empty($this->request->data['project_id']) && isset($this->request->data['user_id']) && !empty($this->request->data['user_id'])) {

					$project_id = $this->request->data['project_id'];
					$user_id = $this->request->data['user_id'];

					$data = $this->User->find('first', ['conditions' => [
						'User.id' => $user_id,
					],
						'fields' => ['User.email'],
						'recursive' => -1,
					]);

					if (isset($data) && !empty($data)) {

						if (isset($data['User']['email']) && !empty($data['User']['email'])) {
							echo $data['User']['email'];
						}

					}
				}
			}

		}
	}

	public function getRisktypebyuser() {

		$view = new View();
		$viewModal = $view->loadHelper('ViewModel');

		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			$this->autoRender = false;

			if ($this->request->is('post') || $this->request->is('put')) {

				if (isset($this->request->data['project_id']) && !empty($this->request->data['project_id']) && isset($this->request->data['user_id']) && !empty($this->request->data['user_id'])) {

					$project_id = $this->request->data['project_id'];
					$user_id = $this->request->data['user_id'];

					$risktype = $viewModal->project_risktypes($this->request->data['project_id'], $this->request->data['user_id']);

					if (isset($risktype) && !empty($risktype)) {
						$this->set('risktype', $risktype);
						/* $this->set('project_id', $project_id);
						$this->set('user_id', $user_id); */
						$this->render('/AppAssets/partials/risktypeoptions');
					}
				}
			}
		}
	}

	public function getWorkspace(){
		$view = new View();
		$viewModal = $view->loadHelper('ViewModel');

		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			$this->autoRender = false;

			if ($this->request->is('post') || $this->request->is('put')) {

				if (isset($this->request->data['project_id']) && !empty($this->request->data['project_id']) ) {

					$project_id = $this->request->data['project_id'];
					$projectdata = $viewModal->getProjectWorkspaces($this->request->data['project_id']);

					if (isset($projectdata) && !empty($projectdata)) {
						$this->set('projectdata', $projectdata);
						$this->render('/AppAssets/partials/wspoptions');
					}
				}
			}
		}
	}

	public function getProjectPeople(){
		$view = new View();
		$viewModal = $view->loadHelper('ViewModel');

		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			$this->autoRender = false;

			if ($this->request->is('post') || $this->request->is('put')) {

				if (isset($this->request->data['project_id']) && !empty($this->request->data['project_id']) ) {

					$project_id = $this->request->data['project_id'];
					$projectpeople = $viewModal->projectusers($this->request->data['project_id']);

					if (isset($projectpeople) && !empty($projectpeople)) {
						$this->set('projectpeople', $projectpeople);
						$this->render('/AppAssets/partials/projectpeople');
					}
				}
			}
		}
	}

	public function getProjectElement(){

		$view = new View();
		$viewModal = $view->loadHelper('ViewModel');

		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			$this->autoRender = false;

			if ($this->request->is('post') || $this->request->is('put')) {

				if( isset($this->request->data['project_id']) && !empty($this->request->data['project_id']) && isset($this->request->data['user_id']) && !empty($this->request->data['user_id']) ) {

					$project_id = $this->request->data['project_id'];
					$user_id = $this->request->data['user_id'];
					$projectelement = $viewModal->getProjectElementApi($this->request->data['project_id'],$user_id);
					//$user_id = $viewModal->getProjectElementApi($this->request->data['user_id']);

					if (isset($projectelement) && !empty($projectelement)) {
						$this->set('projectelement', $projectelement);
						$this->set('user_id', $user_id);
						$this->render('/AppAssets/partials/projectelement');
					}
				}
			}
		}
	}
	// Get Project Todo
	public function getProjectTodo(){

		$view = new View();
		$viewModal = $view->loadHelper('ViewModel');

		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			$this->autoRender = false;

			if ($this->request->is('post') || $this->request->is('put')) {

				if (isset($this->request->data['project_id']) && !empty($this->request->data['project_id']) && isset($this->request->data['user_id']) && !empty($this->request->data['user_id']) ) {

					$project_id = $this->request->data['project_id'];
					$user_id = $this->request->data['user_id'];

					$projecttodo = $viewModal->get_project_todo($project_id, $user_id);

					if (isset($projecttodo) && !empty($projecttodo)) {
						$this->set('projecttodos', $projecttodo);
						$this->render('/AppAssets/partials/projecttodo');
					}
				}
			}
		}
	}
	// Get Project Risk
	public function getProjectRisk()
	{
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			$this->autoRender = false;

			if ($this->request->is('post') || $this->request->is('put')) {

				if ( isset($this->request->data['project_id']) && !empty($this->request->data['project_id']) && isset($this->request->data['user_id']) && !empty($this->request->data['user_id']) ) {

					$project_id = $this->request->data['project_id'];
					$user_id = $this->request->data['user_id'];

					$RmDetail = getUserRisksByProject($user_id, [$project_id]);

					if (isset($RmDetail) && !empty($RmDetail)) {
						$this->set('RmDetail', $RmDetail);
						$this->render('/AppAssets/partials/projectrisk');
					}
				}
			}
		}

	}


	public function getRewardProjectPeople(){
		$view = new View();
		$viewModal = $view->loadHelper('ViewModel');

		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			$this->autoRender = false;

			if ($this->request->is('post') || $this->request->is('put')) {

				if (isset($this->request->data['project_id']) && !empty($this->request->data['project_id']) ) {

					$project_id = $this->request->data['project_id'];
					$projectpeople = $viewModal->projectusers($this->request->data['project_id']);

					$projectsPeoples = array();
					if (isset($projectpeople) && !empty($projectpeople)) {

						foreach( $projectpeople as $rewardpeoplelists ){
							if( user_opt_status($rewardpeoplelists) ){
								$projectsPeoples[] =  $rewardpeoplelists;
							}
						}
						$this->set('projectpeople', $projectsPeoples);
						$this->render('/AppAssets/partials/rewardprojectpeople');
					}
				}
			}
		}
	}

	public function getProjectRewardCharity(){

		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			$this->autoRender = false;

			if ($this->request->is('post') || $this->request->is('put')) {

				if (isset($this->request->data['project_id']) && !empty($this->request->data['project_id']) ) {

					$project_id = $this->request->data['project_id'];
					$projectchirty = $this->RewardCharity->find('first', array('conditions'=>array('RewardCharity.project_id' => $project_id )) );

					if (isset($projectchirty) && !empty($projectchirty)) {
						$this->set('projectcharity', $projectchirty);
						$this->render('/AppAssets/partials/rewardcharity');
					}
				}
			}
		}
	}

	public function checkProjectRewardStatus(){

		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			$this->autoRender = false;

			if ($this->request->is('post') || $this->request->is('put')) {

				if (isset($this->request->data['project_id']) && !empty($this->request->data['project_id']) ) {

					$project_id = $this->request->data['project_id'];
					$rewardstatus = $this->UserProject->find('first', array('conditions'=>array('UserProject.project_id' => $project_id )) );

					//pr($rewardstatus['UserProject']);

					if (isset($rewardstatus['UserProject']['is_rewards']) && !empty($rewardstatus['UserProject']['is_rewards']) && $rewardstatus['UserProject']['is_rewards'] == 1) {
						return 'continued';
					} else {
						return 'exits';
					}
				}
			}
		}

	}

	public function getProjectElementbyid(){

		$view = new View();
		$viewModal = $view->loadHelper('ViewModel');
		$this->loadModel('Element');

		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			$this->autoRender = false;

			if ($this->request->is('post') || $this->request->is('put')) {

				if( isset($this->request->data['project_id']) && !empty($this->request->data['project_id']) && isset($this->request->data['element_id']) && !empty($this->request->data['element_id']) ) {

					$project_id = $this->request->data['project_id'];
					$element_id = $this->request->data['element_id'];
					$element_type = $this->request->data['element_type'];

					$element = $this->Element->findById($element_id);

					if( $element_type == 'links' ){

						if (isset($element['Links']) && !empty($element['Links'])) {
							$optiontext = 'Links';
							$this->set('projectelement', $element['Links']);
							$this->set('element_type', $optiontext);
							$this->render('/AppAssets/partials/element_type');
						}

					} else if( $element_type == 'docs' ){

						if (isset($element['Documents']) && !empty($element['Documents'])) {
							$optiontext = 'Documents';
							$this->set('projectelement', $element['Documents']);
							$this->set('element_type', $optiontext);
							$this->render('/AppAssets/partials/element_type');
						}

					} else {

						if (isset($element['Notes']) && !empty($element['Notes'])) {
							$optiontext = 'Notes';
							$this->set('projectelement', $element['Notes']);
							$this->set('element_type', $optiontext);
							$this->render('/AppAssets/partials/element_type');
						}
					}

				}
			}
		}
	}

	function get_sub_todos() {

		$view = new View();
		$viewModal = $view->loadHelper('ViewModel');
		$this->loadModel('Element');
		$this->loadModel('DoList');
		$this->loadModel('DoListUser');

		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			$this->autoRender = false;

			if ($this->request->is('post') || $this->request->is('put')) {

				if( isset($this->request->data['project_id']) && !empty($this->request->data['project_id']) && isset($this->request->data['user_id']) && !empty($this->request->data['user_id']) && isset($this->request->data['todo_id']) && !empty($this->request->data['todo_id']) ) {

					$project_id = $this->request->data['project_id'];
					$user_id = $this->request->data['user_id'];
					$todo_id = $this->request->data['todo_id'];

					$list = [];
					$data = $this->DoList->find('all',
						[
							'joins' => [
								[
									'table' => 'do_list_users',
									'alias' => 'DoListUser',
									'type' => 'INNER',
									'conditions' => ['DoList.id = DoListUser.do_list_id'],
								],
							],
							'conditions' => [
								'DoList.project_id' => $project_id, 'DoList.parent_id ' => $todo_id, 'DoList.sign_off' => 0,
								'OR' => [
									'DoList.user_id' => $user_id,
									'DoListUser.owner_id' => $user_id,
									'DoListUser.user_id' => $user_id,
								],
							],
						]);
						//'DoListUser.approved' => 1,

						$finalarray = array();
						if (isset($data) && !empty($data)) {
							$list = Set::extract($data, '/DoList/id');
							$list = array_unique($list);
							$finalarray = $this->DoList->find('all', array('conditions'=>array('DoList.id'=>$list) ) );
							$this->set('finalarray', $finalarray);
							$this->render('/AppAssets/partials/subtodolist');

						}


				}
			}
		}
	}
	
	
	
	
	
	/***** Below function is only for testing please do not delete **********/
	function get_gantt_data($project_id = 122, $workspace_id = null, $ele_status = 'PRG',$is_critical = null, $filter_by_user = null ){		
	
		$this->layout = 'inner';	 
		
		//echo "=project id =".$project_id ."=wsp id=". $workspace_id ."=ele stats=". $ele_status; 
		
		$user_id = $this->Session->read('Auth.User.id');
		if(isset($user) && !empty($user)){
			$user_id = $user;
		} 
		
		$query = "SELECT
			user_permissions.role,
			projects.id, projects.title, projects.budget, projects.start_date, projects.end_date, projects.sign_off, projects.sign_off_date,
			workspaces.id, workspaces.title, workspaces.start_date, workspaces.end_date, workspaces.sign_off, workspaces.sign_off_date, workspaces.color_code,
			currencies.sign,

			GROUP_CONCAT(elements.id) as all_tasks,
			count(elements.id) as total_tasks,

			(CASE
				WHEN (DATE(NOW())<DATE(workspaces.start_date) and workspaces.sign_off!=1) THEN 'WPND'
				WHEN (DATE(NOW()) BETWEEN DATE(workspaces.start_date) AND DATE(workspaces.end_date) and workspaces.sign_off!=1) THEN 'WPRG'
				WHEN (DATE(workspaces.end_date)<DATE(NOW()) and workspaces.sign_off!=1) THEN 'WOVD'
				WHEN (workspaces.sign_off=1) THEN 'WCMP'
				ELSE 'WNON'
			END) AS wsp_status,

			sum(elements.date_constraints=0) AS NON,
			sum(DATE(NOW())<DATE(elements.start_date) and elements.sign_off!=1 and elements.date_constraints=1) AS PND,
			sum(DATE(NOW()) BETWEEN DATE(elements.start_date) AND DATE(elements.end_date) and elements.sign_off!=1   and elements.date_constraints=1  ) AS PRG,
			sum(DATE(elements.end_date)<DATE(NOW()) and elements.sign_off!=1 and elements.date_constraints=1) AS OVD,
			sum(elements.sign_off=1) AS CMP,

			(select sum(element_costs.spend_cost) from element_costs where element_costs.element_id in (select user_permissions.element_id from user_permissions where user_permissions.element_id is not null  and user_permissions.workspace_id = workspaces.id  and user_permissions.user_id = $user_id AND user_permissions.project_id = $project_id )) as spend_total,

			(select sum(element_costs.estimated_cost) from element_costs where element_costs.element_id in (select user_permissions.element_id from user_permissions where user_permissions.element_id is not null  and user_permissions.workspace_id = workspaces.id  and user_permissions.user_id = $user_id AND user_permissions.project_id = $project_id )) as estimate_total,

			(CASE
				WHEN (user_permissions.role='Owner' ) THEN 'r_project'
				WHEN (user_permissions.role='Sharer' ) THEN 'r_project'
				WHEN (user_permissions.role='Group Owner' ) THEN 'g_project'
				WHEN (user_permissions.role='Group Sharer' ) THEN 'g_project'
				ELSE 'm_project'
			END) AS prj_type,


			(select SUM( ((res.impact = 2 AND res.percentage = 5) OR (res.impact = 3 AND (res.percentage = 5 OR res.percentage= 4)) OR (res.impact = 4 AND res.percentage = 4) OR (res.impact = 5 AND res.percentage = 3) ) AND rm_details.id = res.rm_detail_id) AS high_risk from rm_details
			LEFT JOIN rm_expose_responses res
			ON rm_details.id = res.rm_detail_id

			where rm_details.id in ( select rm_elements.rm_detail_id from rm_elements where  rm_elements.element_id in (select up.element_id from user_permissions up where up.workspace_id = user_permissions.workspace_id and  up.element_id is not null))

			) high_risk,

			(select SUM( ((res.impact = 4 AND res.percentage = 5) OR (res.impact = 5 AND (res.percentage = 5 OR res.percentage= 4)))  AND rm_details.id = res.rm_detail_id) AS high_risk from rm_details
			LEFT JOIN rm_expose_responses res
			ON rm_details.id = res.rm_detail_id

			where rm_details.id in ( select rm_elements.rm_detail_id from rm_elements where  rm_elements.element_id in (select up.element_id from user_permissions up where up.workspace_id = user_permissions.workspace_id and  up.element_id is not null))

			) severe_risk


		FROM
			user_permissions
		INNER JOIN projects
			ON projects.id=user_permissions.project_id
		INNER JOIN elements
			ON elements.id=user_permissions.element_id and elements.studio_status = 0
		INNER JOIN workspaces ON user_permissions.workspace_id = workspaces.id and workspaces.studio_status = 0
		LEFT JOIN currencies
			ON currencies.id = projects.currency_id
		
		WHERE
			user_permissions.user_id = $user_id AND user_permissions.project_id = $project_id 
		GROUP BY user_permissions.workspace_id "; 
		
		$resultData = ClassRegistry::init('UserPermission')->query($query);
		
		$this->set( compact("resultData", "ele_status", "is_critical", "filter_by_user" ) );		
		$this->layout = false;
		
		
	}
	

	
	
	
}
