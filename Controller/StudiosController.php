<?php
App::uses('Controller/Component', 'Auth', 'Session', 'RequestHandler');
App::uses('Sanitize', 'Utility');
App::uses('CakeEmail', 'Network/Email');

App::uses('Folder', 'Utility');
App::uses('File', 'Utility');

// App::import('Lib', 'Communications');

class StudiosController extends AppController {

	public $name = 'Studios';
	public $uses = ['UserProject', 'Project', 'Workspace', 'Area', 'ProjectWorkspace', 'Element', 'ProjectGroup', 'ProjectGroupUser', 'TemplateDetail', 'UserDetail','ElementPermission','ShareElement'];
	public $user_id = null;
	public $objView = null;
	public $pagination = null;
	public $mongoDB = null;
	public $live_setting;
	public $components = array('Mpdf', 'Common', 'Group','Projects');

	/**
	 * check login for admin and frontend user
	 * allow and deny user
	 */
	//public $components = array('Email');
	// $this->loadModel('Project');
	/**
	 * Helpers
	 *
	 * @var array
	 */
	public $helpers = array('Html', 'Form', 'Session', 'Time', 'Text', 'Common', 'ViewModel');

	public function beforeFilter() {
		parent::beforeFilter();
		$this->set('controller', 'studio');

		$this->user_id = $this->Auth->user('id');

		$view = new View();
		$this->objView = $view;

		$this->setJsVar('user_id', $this->user_id);

		$this->live_setting = LIVE_SETTING;

		if( $_SERVER['HTTP_HOST'] != LOCALIP )  {

			if( $this->Session->read('Auth.User.role_id') == 3 ){
				$this->redirect(['controller' => 'organisations','action' => 'dashboard']);
			}
			if( $this->Session->read('Auth.User.role_id') == 1 ){
				$this->redirect(['controller' => 'dashboard','action' => 'index']);
			}
		}


	}

	/*
		     * @name  		index
		     * @access		public
		     * @package  	App/Controller/StudiosController
	*/
	public function index($project_id = null) {

		$this->layout = 'inner';

		$common = $this->objView->loadHelper('Common');

		$this->set('title_for_layout', __('Design Board', true));

		$viewData['page_heading'] = "Design Board";

		$viewData['page_subheading'] = "Create and visualize Project flows";
		$viewData['project_id'] = null;

		if (isset($this->params['named']) && !empty($this->params['named'])) {
			$params = $this->params['named'];
			$viewData['project_id'] = isset($params['project']) && !empty($params['project']) ? $params['project'] : null;
			/* $viewVars['workspace_id'] = isset($params['workspace']) && !empty($params['workspace']) ? $params['workspace'] : null;
				$viewVars['status'] = isset($params['status']) && !empty($params['status']) ? $params['status'] : null; */
			//pr($params );
		}

		$this->Project->unbindModel(['hasMany' => ['ProjectPermission']]);

		$projects = [];
		$mprojects = $this->Project->UserProject->find('list', array('conditions' => ['UserProject.user_id' => $this->user_id, 'UserProject.owner_user' => 1, 'Project.sign_off' => 0], 'recursive' => 1, 'fields' => ['Project.id', 'Project.title']));

		/*=====================================================================*/
		$rprojects = get_rec_projects($this->user_id, 1);
		$gprojects = group_rec_projects($this->user_id, 1);

		if (isset($mprojects) && !empty($mprojects)) {
			$projects = $projects + $mprojects;
		}
		if (isset($rprojects) && !empty($rprojects)) {
			$projects = $projects + $rprojects;
		}
		if (isset($gprojects) && !empty($gprojects)) {
			$projects = $projects + $gprojects;
		}

		if (isset($projects) && !empty($projects)) {
			$projects = array_map("htmlentities", $projects);
			$projects = array_map("trim", $projects);
			$projects = array_map(function ($v) {
				return html_entity_decode($v, ENT_COMPAT, "UTF-8");
			}, $projects);
			natcasesort($projects);
		}
		$viewData['my_projects'] = $projects;
		/*=========================================================================*/

		if (isset($project_id) && !empty($project_id)) {
			$project_detail = $common->get_project($project_id);
			$viewData['project_detail'] = (isset($project_detail) && !empty($project_detail)) ? $project_detail['Project'] : null;
			$viewData['project_id'] = $project_id;

		}

		//========== Updated 25th Oct ==================
		$userProjectStatus = $this->UserDetail->find('first', ['conditions' => ['UserDetail.user_id' => $this->Session->read('Auth.User.id')]]);
		$viewData['create_project_status'] = 0;
		if (isset($userProjectStatus) && !empty($userProjectStatus['UserDetail']['create_project']) && $userProjectStatus['UserDetail']['create_project'] == 1) {
			$viewData['create_project_status'] = $userProjectStatus['UserDetail']['create_project'];
		}

		//==============================================

		$viewData['crumb'] = [
			'last' => [
				'data' => [
					'class' => 'tipText',
					'title' => 'Design Board',
					'data-original-title' => 'Design Board',
				],
			],
		];

		$this->set($viewData);
	}

	/*
		     * @name  		list_projects
		     * @access		public
		     * @package  	App/Controller/StudiosController
	*/
	public function list_projects() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$response = [
				'success' => false,
				'msg' => '',
				'content' => null,
			];

			$row = null;

			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;

				if (isset($post['type']) && $post['type'] == 1) {
					$this->Project->unbindModel(['hasMany' => ['ProjectPermission']]);

					$projects = $this->Project->UserProject->find('list', array('conditions' => ['UserProject.user_id' => $this->user_id, 'UserProject.owner_user' => 1], 'recursive' => 1, 'fields' => ['Project.id', 'Project.title']));
					$response['success'] = true;
					$response['content'] = (isset($projects) && !empty($projects)) ? $projects : null;
				} else if (isset($post['type']) && $post['type'] == 2) {
					$rec_projects = get_rec_projects($this->user_id, 1);
					$response['success'] = true;
					$response['content'] = (isset($rec_projects) && !empty($rec_projects)) ? $rec_projects : null;
					// pr($rec_projects);

				} else if (isset($post['type']) && $post['type'] == 3) {
					$grp_projects = group_rec_projects($this->user_id, 1);
					$response['success'] = true;
					$response['content'] = (isset($grp_projects) && !empty($grp_projects)) ? $grp_projects : null;
				}

			}
			echo json_encode($response);
			exit();
		}
	}

	/*
		     * @name  		get_project_info
		     * @access		public
		     * @package  	App/Controller/StudiosController
	*/
	public function get_project_info() {

		if ($this->request->isAjax()) {

			$common = $this->objView->loadHelper('Common');

			$this->layout = 'ajax';
			$response = [
				'success' => false,
				'msg' => '',
				'content' => null,
			];
			$is_owner = false;

			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;

				if (isset($post['project_id']) && !empty($post['project_id'])) {
					$data = $common->get_project($post['project_id']);
					$project_owner = is_project_owner($post['project_id'], $this->user_id);
					$is_owner = (isset($project_owner) && !empty($project_owner)) ? true : false;
				}

				$view = new View($this, false);
				$view->viewPath = 'Studios/partials'; // Directory inside view directory to search for .ctp files
				$view->layout = false; // if you want to disable layout
				$view->set('data', $data);
				$view->set('is_owner', $is_owner);

				$html = $view->render('project_box');

			}
			echo json_encode($html);
			exit();
		}
	}

	/*
		     * @name  		get_project_workspaces
		     * @access		public
		     * @package  	App/Controller/StudiosController
	*/
	public function get_project_workspaces() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$response = [
				'success' => false,
				'msg' => '',
				'content' => null,
			];

			$workspaces = $data = null;

			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;

				if (isset($post['project_id']) && !empty($post['project_id'])) {

					$this->ProjectWorkspace->Behaviors->load('Containable');

					$pw_data = $this->ProjectWorkspace->find('all', ['conditions' => ['ProjectWorkspace.project_id' => $post['project_id']], 'contain' => 'Workspace', 'order' => ['ProjectWorkspace.sort_order ASC']]);

					if (!empty($pw_data)) {

						$workspaces = $pw_data;

					}

					$data = ($workspaces) ? $workspaces : null;
				}

				$view = new View($this, false);
				$view->viewPath = 'Studios/partials'; // Directory inside view directory to search for .ctp files
				$view->layout = false; // if you want to disable layout
				$view->set('data', $data);

				$html = $view->render('workspace_box');
			}

			echo json_encode($html);
			exit();
		}
	}

	/*
		     * @name  		get_workspace_areas
		     * @access		public
		     * @package  	App/Controller/StudiosController
	*/
	public function get_workspace_areas() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$response = [
				'success' => false,
				'msg' => '',
				'content' => null,
			];

			$data = null;

			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;
				$result = null;
				if (isset($post['workspace_id']) && !empty($post['workspace_id'])) {


		/******************************************************/
		$workspace_id = $post['workspace_id'];
		$wsp_areas = $this->Area->find('all', [
			'conditions' => [
				'Area.workspace_id' => $workspace_id,
			],
			'recursive' => -1,
			'fields' => ['Area.id'],
		]);
		if (isset($wsp_areas) && !empty($wsp_areas)) {
			foreach ($wsp_areas as $keys => $values) {

				$area_id = $values['Area']['id'];
				$find_cond = [
					'Element.area_id' => $area_id,
					'OR' => [
						'Element.sort_order IS NULL',
						'Element.sort_order <=' => 0,
					],
				];
				if ($this->Element->hasAny($find_cond)) {
					$getMax = $this->Element->find('all', [
						'conditions' => ['Element.area_id' => $area_id],
						'fields' => array('MAX(Element.sort_order) AS max_sort'),
						'recursive' => -1,
					]);
					$increment = 1;
					if (isset($getMax) && !empty($getMax)) {
						$increment = $getMax[0][0]['max_sort'] + 1;
					}
					// pr($getMax, 1);
					$allEls = $this->Element->find('all', [
						'conditions' => $find_cond,
						'fields' => 'Element.id',
						'recursive' => -1,
					]);

					if (isset($allEls) && !empty($allEls)) {
						foreach ($allEls as $key => $value) {
							$this->Element->id = $value['Element']['id'];
							$this->Element->saveField('sort_order', $increment++);
						}
					}
				}
			}
		}
		/******************************************************/







					$data = $this->Area->find('all', ['conditions' => ['Area.workspace_id' => $post['workspace_id']], 'recursive' => -1, 'order' => ['Area.sort_order ASC']]);
				}

				$view = new View($this, false);
				$view->viewPath = 'Studios/partials'; // Directory inside view directory to search for .ctp files
				$view->layout = false; // if you want to disable layout
				$view->set('data', $data);

				$html = $view->render('zone_box');

			}
			echo json_encode($html);
			exit();
		}
	}

	/*
		     * @name  		get_area_elements
		     * @access		public
		     * @package  	App/Controller/StudiosController
	*/
	public function get_area_elements() {

		if ($this->request->isAjax()) {


			$this->layout = 'ajax';
			$response = [
				'success' => false,
				'msg' => '',
				'content' => null,
			];

			$data = null;

			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;
				$data = null;
				$wsp_sign_off = 0;
				$prjid = 0;
				$user_id = $this->Session->read('Auth.User.id');
				if (isset($post['area_id']) && !empty($post['area_id'])) {
					// pr($post);
					$area = $post['area_id'];
					if(isset($post['wspid']) && !empty($post['wspid'])){
						$wsp_id = $post['wspid'];
					}
					else{
						$wsp_id = area_workspace_id($post['area_id']);
					}


					if(isset($post['pid']) && !empty($post['pid'])){
						$prjid = $post['pid'];
					}
					else{
						$prjid = workspace_pid($wsp_id);
					}
					/*$data = $this->Element->find('all', [
						'recursive' => -1,
						'conditions' => ['Element.area_id' => $post['area_id']],
						'order' => ['Element.sort_order ASC'],
					]);
					*/

					$query = "SELECT IF(w.sign_off IS NULL, 0, w.sign_off) AS signoff
							FROM workspaces w
							WHERE w.id = $wsp_id";
					$wsp_details = $this->Element->query($query);
					// pr($wsp_details,1);
					$wsp_sign_off = (isset($wsp_details[0][0]['signoff'])) ? $wsp_details[0][0]['signoff'] : 0;
					$query = "SELECT
									Element.id,
									Element.title,
									Element.description,
									Element.comments,
									Element.studio_status,
									Element.start_date,
									Element.end_date,
									Element.color_code,
									Element.sign_off,
									Element.area_id,
									(CASE
										WHEN (Element.date_constraints=0) THEN 'not_spacified'
										WHEN (DATE(NOW()) < DATE(Element.start_date) and Element.sign_off!=1 and Element.date_constraints=1) THEN 'not_started'
										WHEN (DATE(NOW()) BETWEEN DATE(Element.start_date) AND DATE(Element.end_date) and Element.sign_off!=1 and Element.date_constraints=1 ) THEN 'progress'
										WHEN (DATE(Element.end_date) < DATE(NOW()) and Element.sign_off!=1 and Element.date_constraints=1) THEN 'overdue'
										WHEN (Element.sign_off=1) THEN 'completed'
										ELSE 'not_spacified'
									END) AS task_status
							FROM elements Element
							WHERE Element.area_id = $area";
					$data = $this->Element->query($query);
					// pr(count($data), 1);
				}
				$view = new View($this, false);
				$view->viewPath = 'Studios/partials'; // Directory inside view directory to search for .ctp files
				$view->layout = false; // if you want to disable layout
				$view->set('data', $data);
				$view->set('wsp_sign_off', $wsp_sign_off);
				$view->set('prjid', $prjid);

				$html = $view->render('element_box');

			}
			echo json_encode($html);
			exit();
		}
	}

		public function workspace_date_check() {

			$this->autoRender = false;
			$this->layout = false;
			$response = [
				'success' => false,
				'msg' => '',
				'content' => null,
			];

			$data = null;

			$message = [
				'message' => false,
				'msg_status' => 0
				];

			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;



					//if (isset($post['area_id']) && !empty($post['area_id'])) {


					$wsp_id = area_workspace_id($post['area_id']);
					$wsp_details = getByDbId('Workspace',$wsp_id, array('start_date','end_date','sign_off'));

					if($wsp_details['Workspace']['sign_off'] !=1){

						if( FUTURE_DATE == 'off' ){
							if((isset($wsp_details['Workspace']['start_date']) && $wsp_details['Workspace']['start_date'] > date('Y-m-d 00:00:00')) ){

								$message['message'] ="Workspace schedule has not reached the start date.";
							 }

							if((isset($wsp_details['Workspace']['end_date']) && $wsp_details['Workspace']['end_date'] < date('Y-m-d')) ){

								$message['message'] ="You cannot add an Task because Workspace end date has passed.";
							}
						}

					} else if(isset($wsp_details['Workspace']['start_date']) && $wsp_details['Workspace']['sign_off'] == 1){
						$message['message'] ="Workspace Is Signed Off";
						$message['msg_status'] = 1;
					}

					/* } else if(isset($wsp_details['Workspace']['start_date']) ){
						$message['message'] ="You cannot add an Task because Workspace has Signoff.";
					} */


					if(!isset($wsp_details['Workspace']['start_date'])){
						$message['message'] ="Please add a schedule to this workspace first.";
					}

					//}



					return json_encode($message) ;


			}



			exit();

		}


		public function project_date_check() {

			$this->autoRender = false;
			$this->layout = false;
			$response = [
				'success' => false,
				'msg' => '',
				'content' => null,
			];

			$data = null;

			$message['message'] = null;

			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;



					//if (isset($post['area_id']) && !empty($post['area_id'])) {


						$project_id =  $post['project_id'];
						$wsp_details = getByDbId('Project',$project_id, array('start_date','end_date','sign_off'));


					if($wsp_details['Project']['sign_off'] !=1){

						if( FUTURE_DATE == 'off' ){
							if((isset($wsp_details['Project']['start_date']) && $wsp_details['Project']['start_date'] > date('Y-m-d 00:00:00')) ){

								$message['message'] ="Project schedule has not reached the start date.";
							}

							if((isset($wsp_details['Project']['end_date']) && $wsp_details['Project']['end_date'] < date('Y-m-d')) ){

								$message['message'] ="You cannot add an Workspace because Project end date has passed.";
							}
						}

					}
					 else if(isset($wsp_details['Project']['start_date'])){
						$message['message'] ="You cannot add an Workspace because Project has Signoff.";
					 }


					if(!isset($wsp_details['Project']['start_date'])){
						$message['message'] ="Please add a schedule to this Project first.";
					}

					//}



					return json_encode($message) ;


			}



			exit();

		}



		public function workspace_date_check_by_wsp_id() {

			$this->autoRender = false;
			$this->layout = false;
			$response = [
				'success' => false,
				'msg' => '',
				'content' => null,
			];

			$data = null;

			$message['message'] = null;

			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;



					//if (isset($post['area_id']) && !empty($post['area_id'])) {


						$wsp_id = $post['workspace_id'];
						$wsp_details = getByDbId('Workspace',$wsp_id, array('start_date','end_date','sign_off'));


					if($wsp_details['Workspace']['sign_off'] !=1){

							if((isset($wsp_details['Workspace']['start_date']) && $wsp_details['Workspace']['start_date'] > date('Y-m-d 00:00:00')) ){

							$message['message'] ="Workspace schedule has not reached the start date.";
						 }

						if((isset($wsp_details['Workspace']['end_date']) && $wsp_details['Workspace']['end_date'] < date('Y-m-d')) ){

							$message['message'] ="You cannot add an Task because Workspace end date has passed.";
						}

					}
					 else if(isset($wsp_details['Workspace']['start_date'])){
						$message['message'] ="You cannot add an Task because Workspace has Signoff.";
					 }


					if(!isset($wsp_details['Workspace']['start_date'])){
						$message['message'] ="Please add a schedule to this workspace first.";
					}

					//}



					return json_encode($message) ;


			}



			exit();

		}


		public function workspace_area_date_check_by_wsp_id() {

			$this->autoRender = false;
			$this->layout = false;
			$response = [
				'success' => false,
				'msg' => '',
				'content' => null,
			];

			$data = null;

			$message = ['message' => null, 'msg_status' => 0];

			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;



					//if (isset($post['area_id']) && !empty($post['area_id'])) {


						$wsp_id = $post['workspace_id'];
						$wsp_details = getByDbId('Workspace',$wsp_id, array('start_date','end_date','sign_off'));


					if($wsp_details['Workspace']['sign_off'] !=1){

						if( FUTURE_DATE == 'off' ){
							if((isset($wsp_details['Workspace']['start_date']) && $wsp_details['Workspace']['start_date'] > date('Y-m-d 00:00:00')) ){

								$message['message'] ="Workspace schedule has not reached the start date.";
							 }

							if((isset($wsp_details['Workspace']['end_date']) && $wsp_details['Workspace']['end_date'] < date('Y-m-d')) ){

								$message['message'] ="You cannot add an Area because Workspace end date has passed.";
							}
						}

					} else if(isset($wsp_details['Workspace']['start_date']) && $wsp_details['Workspace']['sign_off'] == 1){
						$message['message'] ="Workspace Is Signed Off";
						$message['msg_status'] = 1;
					}


					/* else if(isset($wsp_details['Workspace']['start_date'])){
						$message['message'] ="You cannot add an Area because Workspace has Signoff.";
					 } */


					if(!isset($wsp_details['Workspace']['start_date'])){
						$message['message'] ="Please add a schedule to this workspace first.";
					}

					//}



					return json_encode($message) ;


			}



			exit();

		}



	/*
		     * @name  		count_workspace_areas
		     * @access		public
		     * @package  	App/Controller/StudiosController
	*/
	public function count_workspace_areas() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$response = [
				'success' => false,
				'msg' => '',
				'content' => null,
			];
			// pr($this->params->query['workspace_id'], 1);
			$data = null;

			$post = $this->request->data;
			$result = null;
			if (isset($this->params->query['workspace_id']) && !empty($this->params->query['workspace_id'])) {
				$data = $this->Area->find('all', ['conditions' => ['Area.workspace_id' => $this->params->query['workspace_id']], 'recursive' => -1, 'order' => ['Area.sort_order ASC']]);
			}
			// echo count($data);
			$data_tot = ( isset($data) && !empty($data) ) ? count($data) : 0;
			echo json_encode($data_tot);
			exit();
		}
	}

	/**
	 * Open Popup Modal Boxes method
	 *
	 * @return void
	 */
	public function create_project_modal($project_id = null) {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$response = array('project_id' => $project_id);

			if (isset($project_id) && !empty($project_id)) {
				$response['project_id'] = $project_id;

				$this->request->data = $this->Project->read(null, $project_id);
				$this->request->data['Project']['title'] = html_entity_decode($this->request->data['Project']['title']);
				$this->request->data['Project']['studio_status'] = (isset($this->request->data['Project']['studio_status'])) ? $this->request->data['Project']['studio_status'] : 1;

				$this->request->data['Project']['start_date'] = (isset($this->request->data['Project']['start_date']) && !empty($this->request->data['Project']['start_date'])) ? date('d-m-Y', strtotime($this->request->data['Project']['start_date'])) : "";
				$this->request->data['Project']['end_date'] = (isset($this->request->data['Project']['end_date']) && !empty($this->request->data['Project']['end_date'])) ? date('d-m-Y', strtotime($this->request->data['Project']['end_date'])) : "";
				// pr($this->request->data, 1);
			}

			$this->loadModel('Category');
			$categories_list = $this->Category->find('threaded', array('recursive' => -1));
			$categories = tree_list($categories_list, 'Category', 'id', 'title');
			$this->set(compact('categories'));

			$this->loadModel('Aligned');
			$aligneds = $this->Aligned->find("list", ['order' => ['Aligned.title ASC']]);
			$this->set(compact('aligneds'));

			$this->set('response', $response);

			$this->render('/Studios/partials/create_project');
		}
	}

	/**
	 * Open Popup Modal Boxes method
	 *
	 * @return void
	 */
	public function create_workspace($project_id = null, $workspace_id = null) {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$response = array('project_id' => $project_id);

			if (isset($workspace_id) && !empty($workspace_id)) {
				$response['workspace_id'] = $workspace_id;

				$this->request->data = $this->Workspace->find('first', [
					'conditions' => [
						'id' => $workspace_id
					],
					'recursive' => -1,
					'fields' => [
						'id', 'title', 'description', 'outcome', 'start_date', 'end_date', 'template_id', 'color_code'
					]
				]);
				$this->request->data['Workspace']['title'] = html_entity_decode($this->request->data['Workspace']['title']);

				$this->request->data['Workspace']['start_date'] = (isset($this->request->data['Workspace']['start_date']) && !empty($this->request->data['Workspace']['start_date'])) ? date('d-m-Y', strtotime($this->request->data['Workspace']['start_date'])) : "";
				$this->request->data['Workspace']['end_date'] = (isset($this->request->data['Workspace']['end_date']) && !empty($this->request->data['Workspace']['end_date'])) ? date('d-m-Y', strtotime($this->request->data['Workspace']['end_date'])) : "";

				$this->setJsVar('wdata', $this->request->data['Workspace']);

				// pr($this->request->data , 1);
			}

			$this->set('response', $response);
			$this->set('project_id', $project_id);

			$this->render('/Studios/partials/create_workspace');
		}
	}

	/**
	 * Open Popup Modal Boxes method
	 *
	 * @return void
	 */
	public function create_zone($workspace_id = null, $area_id = null) {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$response = array('workspace_id' => $workspace_id);

			if (isset($area_id) && !empty($area_id)) {
				$response['area_id'] = $area_id;

				$this->request->data = $this->Area->read(null, $area_id);
				$this->request->data['Area']['title'] = html_entity_decode($this->request->data['Area']['title']);
			}

			$this->set('response', $response);

			$this->render('/Studios/partials/create_zone');
		}
	}

	/**
	 * Open Popup Modal Boxes method
	 *
	 * @return void
	 */
	public function create_element($area_id = null, $element_id = null) {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$response = array('area_id' => $area_id);

			if (isset($element_id) && !empty($element_id)) {
				$response['element_id'] = $element_id;

				$this->request->data = $this->Element->read(null, $element_id);
				$this->request->data['Element']['title'] = html_entity_decode($this->request->data['Element']['title']);
				$this->request->data['Element']['description'] = $this->request->data['Element']['description'];

				$this->request->data['Element']['start_date'] = (isset($this->request->data['Element']['start_date']) && !empty($this->request->data['Element']['start_date'])) ? date('d-m-Y', strtotime($this->request->data['Element']['start_date'])) : "";
				$this->request->data['Element']['end_date'] = (isset($this->request->data['Element']['end_date']) && !empty($this->request->data['Element']['end_date'])) ? date('d-m-Y', strtotime($this->request->data['Element']['end_date'])) : "";

			}

			$this->set('response', $response);

			$this->render('/Studios/partials/create_element');
		}
	}

	/**
	 * Save newly created workspace
	 *
	 * @param $project_id
	 * @return JSON array
	 */
	public function save_workspace($project_id = null) {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$response = [
				'success' => false,
				'status' => 400,
				'content' => null,
				'date_error' => null,
			];

			if ($this->request->is('post') || $this->request->is('put')) {
				// pr($this->request->data, 1);

				$this->request->data['Workspace']['updated_user_id'] = $this->user_id;
				$this->request->data['Workspace']['created_by'] = $this->user_id;


				$start = $this->request->data['Workspace']['start_date'];
				$end = $this->request->data['Workspace']['end_date'];
				$wspid = ( isset($this->request->data['Workspace']['id']) && !empty($this->request->data['Workspace']['id']) ) ? $this->request->data['Workspace']['id'] : null ;
				$check = $this->Common->check_date_validation_ws($start, $end, $project_id, $wspid);

				$this->Workspace->set($this->request->data);

				if ($this->Workspace->validates()) {

					$this->loadModel('ProjectWorkspace');
					$this->Workspace->create();

					if (empty($check) || $check == null) {

						$this->request->data['Workspace']['start_date'] = date('Y-m-d', strtotime($this->request->data['Workspace']['start_date']));
						$this->request->data['Workspace']['end_date'] = date('Y-m-d', strtotime($this->request->data['Workspace']['end_date']));

						if (isset($this->request->data['Workspace']['id']) && !empty($this->request->data['Workspace']['id'])) {
							$this->Workspace->id = $this->request->data['Workspace']['id'];
						} else {
							$this->request->data['Workspace']['studio_status'] = 1;
						}

						$this->request->data['Workspace']['title'] = html_entity_decode($this->request->data['Workspace']['title'], ENT_QUOTES);

						if ($this->Workspace->save($this->request->data)) {

							$this->Common->projectModified($project_id, $this->user_id);

							$insertId = 0;
							$response['success'] = true;
							$insert = false;
							if (isset($this->request->data['Workspace']['id']) && !empty($this->request->data['Workspace']['id'])) {
								$insertId = $this->request->data['Workspace']['id'];
							} else {
								$insertId = $this->Workspace->getLastInsertId();
								$insert = true;

								$max_sort = $this->ProjectWorkspace->find('first', [
									'conditions' => [
										'ProjectWorkspace.project_id' => $project_id,
									],
									'fields' => ['MAX(ProjectWorkspace.sort_order) AS sort_order'],
								]);

								$pwdata['ProjectWorkspace']['project_id'] = $project_id;
								$pwdata['ProjectWorkspace']['workspace_id'] = $insertId;

								if (isset($max_sort) && !empty($max_sort)) {
									$next_sort = (isset($max_sort[0]['sort_order'])) ? $max_sort[0]['sort_order'] + 1 : 1;
									$pwdata['ProjectWorkspace']['sort_order'] = $next_sort;
								}

								if ($this->ProjectWorkspace->save($pwdata)) {
									$pwid = $this->ProjectWorkspace->getLastInsertId();

									$this->loadModel('WorkspacePermission');

									$arr['WorkspacePermission']['user_id'] = $this->user_id;
									$arr['WorkspacePermission']['user_project_id'] = project_upid($project_id);
									$arr['WorkspacePermission']['project_workspace_id'] = $pwid;
									$arr['WorkspacePermission']['permit_read'] = 1;
									$arr['WorkspacePermission']['permit_add'] = 1;
									$arr['WorkspacePermission']['permit_edit'] = 1;
									$arr['WorkspacePermission']['permit_delete'] = 1;
									$arr['WorkspacePermission']['permit_copy'] = 1;
									$arr['WorkspacePermission']['permit_move'] = 1;
									$arr['WorkspacePermission']['is_editable'] = 1;

									$this->WorkspacePermission->save($arr);
								}

							}

							$response['content'] = ['id' => $insertId, 'insert' => $insert];
							$this->autorender = FALSE;
						}
					} else {
						if (!empty($check) && $check != null) {
							$response['date_error'] = $check;
						}
						// $this->set('error', $this->Contact->validationErrors[$this->request['data']['field']][0]);
					}
				} else {
					$response['content'] = $this->validateErrors($this->Workspace);
				}

				echo json_encode($response);
				exit;
			}
		}

	}

	/**
	 * Save newly created area
	 *
	 * @param $workspace_id
	 * @return JSON array
	 */
	public function save_zone($workspace_id = null) {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$response = [
				'success' => false,
				'status' => 400,
				'content' => null,
			];

			if ($this->request->is('post') || $this->request->is('put')) {

				$this->Area->set($this->request->data);

				if ($this->Area->validates()) {

					$this->Area->create();
					$this->request->data['Area']['tooltip_text'] = $this->request->data['Area']['tooltip_text'];

					if (isset($this->request->data['Area']['id']) && !empty($this->request->data['Area']['id'])) {
						$this->Area->id = $this->request->data['Area']['id'];
					} else {
						$this->request->data['Area']['studio_status'] = 1;
						$this->request->data['Area']['template_detail_id'] = 0;
					}

					if ($this->Area->save($this->request->data)) {
						$insertId = 0;
						$response['success'] = true;
						if (isset($this->request->data['Area']['id']) && !empty($this->request->data['Area']['id'])) {
							$insertId = $this->request->data['Area']['id'];
						} else {
							$insertId = $this->Area->getLastInsertId();
						}

						$response['content'] = ['id' => $insertId];
						$this->autorender = FALSE;
					}

				} else {
					$response['content'] = $this->validateErrors($this->Area);
				}

				echo json_encode($response);
				exit;
			}
		}

	}

	/**
	 * Save newly created project
	 *
	 * @param $workspace_id
	 * @return JSON array
	 */
	public function create_project() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$this->autorender = FALSE;
			$response = [
				'success' => false,
				'content' => null,
				'msg' => '',
			];

			if ($this->request->is('post') || $this->request->is('put')) {
				// pr($this->request->data, 1);

				if ($this->live_setting) {

					if (PHP_VERSIONS == 5) {
						$mongo = new MongoClient(MONGO_CONNECT);
						$this->mongoDB = $mongo->selectDB(MONGO_DATABASE);
						$mongo_collection = new MongoCollection($this->mongoDB, 'projects');
					} else {
						$mongo = new MongoDB\Driver\Manager(MONGO_CONNECT);
						$bulk = new MongoDB\Driver\BulkWrite;
					}


				}

				$this->Project->set($this->request->data);

				if ($this->Project->validates()) {

					$this->Project->create();
					$insertId = 0;
					$insert = false;

					$this->request->data['Project']['updated_user_id'] = $this->user_id;
					$this->request->data['Project']['task_type'] = 'create';

					$this->request->data['Project']['start_date'] = date('Y-m-d', strtotime($this->request->data['Project']['start_date']));
					$this->request->data['Project']['end_date'] = date('Y-m-d', strtotime($this->request->data['Project']['end_date']));




					if (isset($this->request->data['Project']['id']) && !empty($this->request->data['Project']['id'])) {
						$checkWspDate = true;
						/*============= Added by Pawan ==========================================*/

						$dsfg = $this->Group->ProjectDateValidEnd($this->request->data['Project']['id']);
						if (isset($dsfg) && !empty($dsfg)) {

							if ((isset($this->request->data['Project']['start_date']) && !empty($this->request->data['Project']['start_date'])) && (isset($this->request->data['Project']['end_date']) && !empty($this->request->data['Project']['end_date']))) {

								if (date('Y-m-d', strtotime($this->request->data['Project']['start_date'])) > date('Y-m-d', strtotime($dsfg['start_date']))) {

									$response['success'] = false;
									// $errorMsg = 'Project Start date should not be greater than Workspace Start date.';
									$errorMsg = 'Project start date is after a workspace start date.';
									$response['content'] = ['insert' => false,'start_date'=>$errorMsg];
									$checkWspDate = false;

								}

								if (date('Y-m-d', strtotime($this->request->data['Project']['end_date'])) < date('Y-m-d', strtotime($dsfg['end_date']))) {

									$response['success'] = false;
									$errorMsg = 'Project End date should not be less than Workspace End date.';
									$response['content'] = ['insert' => false,'end_date'=>$errorMsg];
									$checkWspDate = false;

								}


							}
						}

					/*************************************************************************/
						if($checkWspDate){

							$this->request->data['Project']['task_type'] = 'update';
							if ($this->Project->save($this->request->data)) {
								$insertId = $this->request->data['Project']['id'];
							}
							if ($this->live_setting == true) {
								//$ret = $mongo_collection->update(['id' => intval($insertId, 10)], ['$set' => ['title' => strip_tags($this->request->data['Project']['title'])]]);

								$this->Projects->manageProjectUpdate($insertId, $this->request->data['Project']['title']);

							}

							$response['success'] = true;
							$response['content'] = ['id' => $insertId, 'insert' => false];
						}

					} else {

						$this->request->data['UserProject']['user_id'] = $this->user_id;
						$this->request->data['UserProject']['owner_user'] = 1;
						$this->request->data['Project']['studio_status'] = 1;
						unset($this->request->data['Project']['currency_id']);

						if ($this->UserProject->saveAssociated($this->request->data)) {
							$insertId = $this->Project->getLastInsertId();

							// INSERT IN MONGO
							if ($this->live_setting == true) {

								//$ret = $mongo_collection->save(['id' => intval($insertId, 10), 'title' => strip_tags($this->request->data['Project']['title'])]);

								$this->Projects->manageProjectInsert($insertId, $this->request->data['Project']['title']);


							}

							$insert = true;
							$response['success'] = true;
							$response['content'] = ['id' => $insertId, 'insert' => $insert];
						}
					}

					/* $response['success'] = true;
					$response['content'] = ['id' => $insertId, 'insert' => $insert]; */

				} else {
					$response['content'] = $this->validateErrors($this->Project);
				}
			}

			echo json_encode($response);
			exit;
		}

	}

	/**
	 * Save newly created element
	 *
	 * @param $area_id
	 * @return JSON array
	 */
	public function save_element($area_id = null) {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$response = [
				'success' => false,
				'status' => 400,
				'content' => null,
			];

			if ($this->request->is('post') || $this->request->is('put')) {

				$this->request->data['Element']['start_date'] = (isset($this->request->data['Element']['start_date']) && !empty($this->request->data['Element']['start_date'])) ? date('Y-m-d', strtotime($this->request->data['Element']['start_date'])) : null;
				$this->request->data['Element']['end_date'] = (isset($this->request->data['Element']['end_date']) && !empty($this->request->data['Element']['end_date'])) ? date('Y-m-d', strtotime($this->request->data['Element']['end_date'])) : null;

				if ((isset($this->request->data['Element']['start_date']) && !empty($this->request->data['Element']['start_date'])) && (isset($this->request->data['Element']['end_date']) && !empty($this->request->data['Element']['end_date']))) {
					$this->request->data['Element']['date_constraints'] = 1;
				} else {
					$this->request->data['Element']['date_constraints'] = 0;
				}

				//Code for both start and end date should be there or both should be empty, if only one date is there, it should give error
				if ((isset($this->request->data['Element']['start_date']) && $this->request->data['Element']['start_date'] != '') || (isset($this->request->data['Element']['end_date']) && $this->request->data['Element']['end_date'] != '')) {
					$this->request->data['Element']['check_date'] = 1;
				} else {
					$this->request->data['Element']['check_date'] = 0;
				}
				$this->Element->validate['start_date'] = array (
					'required' => array (
						'rule' => 'validateDates',
						'message' => 'Start date is required'
					)
				);
				$this->Element->validate['end_date'] = array (
					'required' => array (
						'rule' => 'validateDates',
						'message' => 'End date is required'
					)
				);

				// pr($this->request->data, 1);
				$this->Element->set($this->request->data);

				if ($this->Element->validates()) {
					$this->request->data['Element']['updated_user_id'] = $this->user_id;
					$this->request->data['Element']['created_by'] = $this->user_id;
					$this->Element->create();

					if (isset($this->request->data['Element']['id']) && !empty($this->request->data['Element']['id'])) {
						$this->Element->id = $this->request->data['Element']['id'];
					} else {
						$this->request->data['Element']['studio_status'] = 1;
					}

					$max_order = task_max_sort_order($area_id);
					$this->request->data['Element']['sort_order'] = $max_order ;

					if ($this->Element->save($this->request->data)) {
						$insertId = 0;
						$response['success'] = true;
						if (isset($this->request->data['Element']['id']) && !empty($this->request->data['Element']['id'])) {
							$insertId = $this->request->data['Element']['id'];
						} else {
							$insertId = $this->Element->getLastInsertId();

							$workspace_ids = $this->Area->find('first', ['conditions' => ['Area.id' => $this->request->data['Element']['area_id']], 'recursive' => -1, 'fields' => ['Area.workspace_id']]);
							$wsp_id = (isset($workspace_ids) && !empty($workspace_ids)) ? Set::extract($workspace_ids, '/Area/workspace_id') : 0;

							$workspace_ids = $this->Area->find('first', ['conditions' => ['Area.id' => $this->request->data['Element']['area_id']], 'recursive' => -1, 'fields' => ['Area.workspace_id']]);
							$wsp_id = (isset($workspace_ids) && !empty($workspace_ids)) ? Set::extract($workspace_ids, '/Area/workspace_id') : 0;

							$project_id = workspace_pid($wsp_id[0]);

							$this->loadModel('ElementPermission');

							$arr['ElementPermission']['user_id'] = $this->user_id;
							$arr['ElementPermission']['element_id'] = $insertId;
							$arr['ElementPermission']['project_id'] = $project_id;
							$arr['ElementPermission']['workspace_id'] = $wsp_id[0];
							$arr['ElementPermission']['permit_read'] = 1;
							$arr['ElementPermission']['permit_add'] = 1;
							$arr['ElementPermission']['permit_edit'] = 1;
							$arr['ElementPermission']['permit_delete'] = 1;
							$arr['ElementPermission']['permit_copy'] = 1;
							$arr['ElementPermission']['permit_move'] = 1;
							$arr['ElementPermission']['is_editable'] = 1;
							$this->ElementPermission->save($arr);
						}

						$response['content'] = ['id' => $insertId];
						$this->autorender = FALSE;
					}

				} else {
					$response['content'] = $this->validateErrors($this->Element);
				}

				echo json_encode($response);
				exit;
			}
		}

	}

	/*
		     * @name  		get_selected_templates
		     * @access		public
		     * @package  	App/Controller/StudiosController
	*/
	public function get_selected_templates($total_area = 0, $workspace = null) {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$response = [
				'success' => false,
				'msg' => '',
				'content' => null,
			];

			if (isset($workspace) && !empty($workspace)) {
				$this->set('workspace_id', $workspace);
			}

			$data = null;
			$this->set('data', $total_area);

			$this->render('/Studios/partials/get_selected_templates');
		}
	}

	/*
		     * @name  		workspace_sorting
		     * @access		public
		     * @package  	App/Controller/StudiosController
	*/
	public function workspace_sorting() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$response = [
				'success' => false,
				'msg' => '',
				'content' => null,
			];

			$data = null;

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				// pr($post, 1);

				$project_id = $post['project_id'];
				$workspace_ids = $post['workspace_id'];

				foreach ($workspace_ids as $index => $workspace_id) {
					$pwid = workspace_pwid($project_id, $workspace_id);

					if (isset($pwid) && !empty($pwid)) {
						$this->ProjectWorkspace->id = $pwid;
						$this->ProjectWorkspace->set(array('sort_order' => $index + 1, 'project_id' => $project_id));
						$this->ProjectWorkspace->save();
					}
				}

			}
			exit();
			// $this->render('/Studios/partials/get_selected_templates');
		}
	}

	/*
		     * @name  		area_sorting
		     * @access		public
		     * @package  	App/Controller/StudiosController
	*/
	public function area_sorting() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$response = [
				'success' => false,
				'msg' => '',
				'content' => null,
			];

			$data = null;

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				// pr($post, 1);

				$workspace_id = $post['workspace_id'];
				$area_ids = $post['area_id'];

				foreach ($area_ids as $index => $id) {

					if (isset($id) && !empty($id)) {
						$this->Area->id = $id;
						$this->Area->set(array('sort_order' => $index + 1, 'is_standby' => 1, 'workspace_id' => $workspace_id, 'studio_status' => 1));
						$this->Area->save();
					}
				}

			}
			exit();
		}
	}

	/*
		     * @name  		element_sorting
		     * @access		public
		     * @package  	App/Controller/StudiosController
	*/
	public function element_sorting() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$response = [
				'success' => false,
				'msg' => '',
				'content' => null,
			];

			$data = null;

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				// pr($post, 1);

				$area_id = $post['area_id'];
				$element_ids = $post['element_id'];

				foreach ($element_ids as $index => $id) {

					if (isset($id) && !empty($id)) {
						$this->Element->id = $id;
						$this->Element->set(array('sort_order' => $index + 1, 'area_id' => $area_id));
						$this->Element->save();
					}
				}

			}
			exit();
			// $this->render('/Studios/partials/get_selected_templates');
		}
	}

	/*
		     * @name  		setting_templates
		     * @access		public
		     * @package  	App/Controller/StudiosController
	*/
	public function setting_templates() {

		if ($this->request->isAjax()) {

			$viewModal = $this->objView->loadHelper('ViewModel');

			$this->layout = 'ajax';
			$response = [
				'success' => false,
				'msg' => '',
				'content' => null,
			];

			$data = null;

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;

				$project_id = $post['project_id'];
				$workspace_id = $post['workspace_id'];
				$template_id = $post['template_id'];

				if (isset($workspace_id) && !empty($workspace_id)) {

					$this->Project->id = $project_id;
					$this->Project->set(array('studio_status' => 0));

					if ($this->Project->save()) {
						$this->Workspace->id = $workspace_id;
						$this->Workspace->set(array('studio_status' => 0, 'template_id' => $template_id));

						if ($this->Workspace->save()) {
							$wsData = $this->Workspace->find('first', [
								'conditions' => [
									'Workspace.id' => $workspace_id,
								],
								'recursive' => -1,
							]);

							if (isset($wsData) && !empty($wsData)) {
								$areaData = $this->Area->find('all', [
									'conditions' => [
										'Area.workspace_id' => $workspace_id,
									],
									'recursive' => -1,
									'order' => ['Area.sort_order ASC'],
								]);

								$tdData = $this->TemplateDetail->find('all', [
									'conditions' => [
										'TemplateDetail.template_id' => $template_id,
									],
									'recursive' => -1,
									'order' => ['TemplateDetail.id ASC'],
								]);

								foreach ($areaData as $index => $detail) {
									$area = $detail['Area'];

									if (isset($tdData[$index]['TemplateDetail']) && !empty($tdData[$index]['TemplateDetail'])) {
										$td = $tdData[$index]['TemplateDetail'];
										$this->Area->id = $area['id'];
										$this->Area->set(array('studio_status' => 0, 'is_standby' => 0, 'template_detail_id' => $td['id'], 'workspace_id' => $workspace_id));
										$this->Area->save();
									}
								}

								// get all area of the workspace
								$area_ids = $this->Area->find('all', ['conditions' => ['Area.workspace_id' => $workspace_id], 'recursive' => -1, 'order' => ['Area.sort_order ASC']]);
								$area_ids = Set::extract($area_ids, '/Area/id');

								// get all elements of all the areas
								$el_ids = $this->Element->find('all', [
									'recursive' => -1,
									'conditions' => ['Element.area_id' => $area_ids],
									'order' => ['Element.sort_order ASC'],
								]);
								$el_ids = Set::extract($el_ids, '/Element/id');

								if (isset($el_ids) && !empty($el_ids)) {
									foreach ($el_ids as $index => $ids) {
										$this->Element->id = $ids;
										$this->Element->save(array('studio_status' => 0));
									}
								}

							}
						}
					}
				}
			}
			exit();
		}
	}

	/*
		     * @name  		delete_project
		     * @todo  		Remove a project
		     * @access		public
		     * @package  	App/Controller/StudiosController
	*/

	public function delete_project() {

		$this->autoRender = false;

		$response = ['success' => false];
		$this->layout = 'ajax';

		// CONNECT WITH MONGO DB

		if ($this->request->is('ajax')) {
			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;
				$project_id = $post['project_id'];

				if (!empty($project_id)) {

					if ($this->live_setting == true) {

						$this->Projects->removeCollectionData($project_id);

						/* if (PHP_VERSIONS == 5) {

							$mongo = new MongoClient(MONGO_CONNECT);
							$this->mongoDB = $mongo->selectDB(MONGO_DATABASE);
							$mongo_collection = new MongoCollection($this->mongoDB, 'projects');
							$mongo_collection->remove(array('id' => (int) $project_id));

						} else {

							$mongo = new MongoDB\Driver\Manager(MONGO_CONNECT);
							$bulk = new MongoDB\Driver\BulkWrite;
							$qry = array("id" => $project_id);
							$ret = $bulk->delete(array('id' => (int) $project_id));
							$mongo->executeBulkWrite(MONGO_DATABASE.'.projects', $bulk);

						} */

					}

					$updata = $this->UserProject->find('first', ['conditions' => ['UserProject.project_id' => $project_id], 'fields' => ['UserProject.id']]);
					// pr($updata, 1);

					App::import('Controller', 'Projects');
					$ProjectsController = new ProjectsController;
					if ($this->Project->delete(['Project.id' => $project_id], false)) {
						if ($ProjectsController->project_associations_delete($project_id, $this->Session->read('Auth.User.id'))) {
							// die('project_associations_delete success');
						}
						$upids = (isset($updata['UserProject']['id']) && !empty($updata['UserProject']['id'])) ? $updata['UserProject']['id'] : 0;
						if (isset($upids) && !empty($upids)) {
							$this->UserProject->delete(['UserProject.id' => $upids], false);
						}

						$this->ProjectWorkspace->unbindModel(['hasMany' => ['WorkspacePermission']]);
						$pwdata = $this->ProjectWorkspace->find('all', ['conditions' => ['ProjectWorkspace.project_id' => $project_id], 'fields' => ['ProjectWorkspace.id', 'ProjectWorkspace.workspace_id']]);

						if (isset($pwdata) && !empty($pwdata)) {
							$pwids = Set::extract($pwdata, '/ProjectWorkspace/id');
							$workspace_id = Set::extract($pwdata, '/ProjectWorkspace/workspace_id');
							foreach ($pwids as $k => $v) {
								$this->ProjectWorkspace->delete(['ProjectWorkspace.id' => $v], false);
							}
							foreach ($workspace_id as $k => $v) {
								$this->Workspace->delete(['Workspace.id' => $v], false);
							}
						}

						if (isset($workspace_id) && !empty($workspace_id)) {

							$areaData = $this->Area->find('all', [
								'conditions' => [
									'Area.workspace_id' => $workspace_id,
								],
								'recursive' => -1,
								'fields' => ['Area.id'],
							]);
							if (isset($areaData) && !empty($areaData)) {
								$areaData = Set::extract($areaData, '/Area/id');

								$elData = $this->Element->find('all', [
									'conditions' => [
										'Element.area_id' => $areaData,
									],
									'recursive' => -1,
									'fields' => ['Element.id'],
								]);

								foreach ($areaData as $ak => $av) {
									$this->Area->delete(['Area.id' => $av], false);
								}

								if (isset($elData) && !empty($elData)) {
									$elData = Set::extract($elData, '/Element/id');
									foreach ($elData as $ek => $ev) {
										$this->Element->delete(['Element.id' => $ev], false);
									}
								}
							}
							$response['success'] = true;
						}
					}
				}
			}
		}

		echo json_encode($response);
		exit();
	}

	/*
     * @name  		delete_workspace
     * @todo  		Remove a workspace
     * @access		public
     * @package  	App/Controller/StudiosController
	 */

	public function delete_workspace() {

		$this->autoRender = false;

		$response = ['success' => false];
		$this->layout = 'ajax';

		if ($this->request->is('ajax')) {
			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;
				$project_id = $post['project_id'];
				$workspace_id = $post['workspace_id'];

				if (!empty($project_id) && !empty($workspace_id)) {

					$this->loadModel('UserPermission');
					$task_ids = $this->UserPermission->find('list', array('fields' => array('UserPermission.element_id','UserPermission.element_id'), 'conditions' => array('UserPermission.workspace_id' => $workspace_id,'UserPermission.role' => 'Creator')));
					
					$area_ids = $this->UserPermission->find('list', array('fields' => array('UserPermission.area_id','UserPermission.area_id'), 'conditions' => array('UserPermission.workspace_id' => $workspace_id,'UserPermission.role' => 'Creator')));		

					$task_ids = array_filter($task_ids);
					$area_ids = array_filter($area_ids);
 
					$this->ProjectWorkspace->delete(['ProjectWorkspace.project_id' => $project_id]);

					if ($this->Workspace->delete(['Workspace.id' => $workspace_id], false)) {
						
					 
					$this->loadModel('ElementPermission');
					$this->ElementPermission->deleteAll(array('ElementPermission.workspace_id' => $workspace_id), false);
					$this->loadModel('Vote');
					$this->Vote->deleteAll(array('Vote.element_id' => $task_ids));				 
					
					
					$up_id =  project_upid($project_id);
					$this->loadModel('WorkspacePermission');
					$this->WorkspacePermission->deleteAll(array('WorkspacePermission.user_project_id' => $up_id ));					 
					$this->loadModel('WorkspacePropagate');
					$this->WorkspacePropagate->deleteAll(array('WorkspacePropagate.user_project_id' => $up_id ));
					$this->loadModel('ElementPropagate');
					$this->ElementPropagate->deleteAll(array('ElementPropagate.project_id' => $project_id));
 
					$this->loadModel('WorkspaceActivity');
					$this->WorkspaceActivity->deleteAll(array('WorkspaceActivity.project_id' => $project_id));
 

					$usp = null;
					$this->loadModel('Feedback'); 
					$this->Feedback->deleteAll(array('Feedback.element_id' => $task_ids));
 
					
					if(isset($workspace_id ) && !empty($workspace_id )){
					
					 
					$this->loadModel('WorkspaceComment'); 					
					$wsp_comment_ids = $this->WorkspaceComment->find('list', array('fields' => array('WorkspaceComment.id','WorkspaceComment.workspace_id'), 'conditions' => array('WorkspaceComment.workspace_id' => $workspace_id )));
					$this->loadModel('WorkspaceCommentLike'); 					
					if(isset($wsp_comment_ids) && !empty($wsp_comment_ids)){
					$this->WorkspaceCommentLike->deleteAll(array('WorkspaceCommentLike.workspace_comment_id' => $wsp_comment_ids));
					}
					
					$this->WorkspaceComment->deleteAll(array('WorkspaceComment.workspace_id' => $workspace_id));
					
					$this->loadModel('WorkspaceCostComment');
					$this->WorkspaceCostComment->deleteAll(array('WorkspaceCostComment.workspace_id' => $workspace_id));
					$this->loadModel('SignoffWorkspace');					
					$this->SignoffWorkspace->deleteAll(array('SignoffWorkspace.workspace_id' => $workspace_id));
					$this->loadModel('CurrentWorkspace');					
					$this->CurrentWorkspace->deleteAll(array('CurrentWorkspace.workspace_id' => $workspace_id));
					
					}
					
					if(isset($task_ids ) && !empty($task_ids )){
					$this->loadModel('ElementLink');					
					$this->ElementLink->deleteAll(array('ElementLink.element_id' => $task_ids));
					$this->loadModel('ElementDecision');
					$this->ElementDecision->deleteAll(array('ElementDecision.element_id' => $task_ids));
					$this->loadModel('ElementDocument');
					$this->ElementDocument->deleteAll(array('ElementDocument.element_id' => $task_ids));

					/* $this->Feedback->deleteAll(array('Feedback.element_id' => $elm['Element']['id'])); */
					$this->loadModel('ElementNote');
					$this->ElementNote->deleteAll(array('ElementNote.element_id' => $task_ids));
					$this->loadModel('ElementMindmap');
					$this->ElementMindmap->deleteAll(array('ElementMindmap.element_id' => $task_ids ));
					$this->loadModel('Vote');
					$this->Vote->deleteAll(array('Vote.element_id' => $task_ids));	
					$this->loadModel('ElementLevel');					
					$this->ElementLevel->deleteAll(array('ElementLevel.element_id' => $task_ids));		
					$this->loadModel('ElementEffort');						
					$this->ElementEffort->deleteAll(array('ElementEffort.element_id' => $task_ids));					
					$this->loadModel('Element');						
					$this->Element->deleteAll(array('Element.id' => $task_ids));
					$this->loadModel('UserElementCost');
					$this->loadModel('RmElement');	
					$this->loadModel('ElementType');	
					$this->loadModel('ElementDependency');	
					$this->loadModel('ElementDependancyRelationship');	
					$this->loadModel('ElementCostHistory');	
					$this->loadModel('UserElementCost');	
					$this->loadModel('ElementCostComment');	
					$this->loadModel('ElementCost');	
					$this->loadModel('ElementAssignment');	
					$this->loadModel('Reminder');						
					$this->loadModel('Area');						
					$this->loadModel('SignoffTask');						
					$this->loadModel('CurrentTask');						
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
					$this->SignoffTask->deleteAll(array('SignoffTask.element_id' => $task_ids));
 					$this->CurrentTask->deleteAll(['CurrentTask.task_id' => $task_ids]);
					$this->Reminder->deleteAll(['Reminder.element_id' => $task_ids]);
					
					}
					
					if(isset($area_ids ) && !empty($area_ids )){
					$this->Area->deleteAll(array('Area.id' => $area_ids));
					}
						$response['success'] = true;
					}
				 
			}
		}
		}

		echo json_encode($response);
		exit();
	}

	/*
		     * @name  		delete_area
		     * @todo  		Remove an area
		     * @access		public
		     * @package  	App/Controller/StudiosController
	*/
	public function delete_area() {

		$this->autoRender = false;

		$response = ['success' => false];
		$this->layout = 'ajax';

		if ($this->request->is('ajax')) {
			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;
				$workspace_id = $post['workspace_id'];
				$area_id = $post['area_id'];
				// pr($post, 1);
				if (!empty($workspace_id) && !empty($area_id)) {

					$this->Workspace->id = $workspace_id;
					$this->Workspace->saveField('studio_status', 1);
					$this->Workspace->saveField('template_id', 0);
					
					$areaData = $this->Area->find('all', [
						'conditions' => [
							'Area.workspace_id' => $workspace_id,
						],
						'recursive' => -1,
						'fields' => ['Area.id'],
					]);
						
					if (isset($areaData) && !empty($areaData)) {
						$areaData = Set::extract($areaData, '/Area/id');
						$this->Area->updateAll(
							array('Area.template_detail_id' => 0, 'Area.studio_status' => 1),
							array('Area.id' => $areaData)
						);
					}
					
					$elData = $this->Element->find('all', [
						'conditions' => [
							'Element.area_id' => $area_id,
						],
						'recursive' => -1,
						'fields' => ['Element.id'],
					]);

					if ($this->Area->delete(['Area.id' => $area_id], false)) {

						if (isset($elData) && !empty($elData)) {
							$elData = Set::extract($elData, '/Element/id');
							foreach ($elData as $ek => $ev) {
								$this->Element->delete(['Element.id' => $ev], false);
							}
						}
					}
					$response['success'] = true;
					
				}
			}
		}

		echo json_encode($response);
		exit();
	}

	/*
		     * @name  		delete_element
		     * @todo  		Remove an area
		     * @access		public
		     * @package  	App/Controller/StudiosController
	*/

	public function delete_element() {

		$this->autoRender = false;

		$response = ['success' => false];
		$this->layout = 'ajax';

		if ($this->request->is('ajax')) {
			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;
				$element_id = $post['element_id'];
				$task_ids = $post['element_id'];

				// pr($post, 1);
				if (!empty($task_ids)) {

/* 					if ($this->Element->delete(['Element.id' => $element_id], false)) {
						$response['success'] = true;
					} */
					
					if(isset($task_ids ) && !empty($task_ids )){
					$this->loadModel('ElementLink');					
					$this->ElementLink->deleteAll(array('ElementLink.element_id' => $task_ids));
					$this->loadModel('ElementDecision');
					$this->ElementDecision->deleteAll(array('ElementDecision.element_id' => $task_ids));
					$this->loadModel('ElementDocument');
					$this->ElementDocument->deleteAll(array('ElementDocument.element_id' => $task_ids));

					/* $this->Feedback->deleteAll(array('Feedback.element_id' => $elm['Element']['id'])); */
					$this->loadModel('ElementNote');
					$this->ElementNote->deleteAll(array('ElementNote.element_id' => $task_ids));
					$this->loadModel('ElementMindmap');
					$this->ElementMindmap->deleteAll(array('ElementMindmap.element_id' => $task_ids ));
					$this->loadModel('Vote');
					$this->Vote->deleteAll(array('Vote.element_id' => $task_ids));	
					$this->loadModel('ElementLevel');					
					$this->ElementLevel->deleteAll(array('ElementLevel.element_id' => $task_ids));		
					$this->loadModel('ElementEffort');						
					$this->ElementEffort->deleteAll(array('ElementEffort.element_id' => $task_ids));					
					$this->loadModel('Element');						
					$this->Element->deleteAll(array('Element.id' => $task_ids));
					$this->loadModel('UserElementCost');
					$this->loadModel('RmElement');	
					$this->loadModel('ElementType');	
					$this->loadModel('ElementDependency');	
					$this->loadModel('ElementDependancyRelationship');	
					$this->loadModel('ElementCostHistory');	
					$this->loadModel('UserElementCost');	
					$this->loadModel('ElementCostComment');	
					$this->loadModel('ElementCost');	
					$this->loadModel('ElementAssignment');	
					$this->loadModel('Reminder');						
					$this->loadModel('Area');						
					$this->loadModel('SignoffTask');						
					$this->loadModel('CurrentTask');						
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
 					$this->SignoffTask->deleteAll(array('SignoffTask.element_id' => $task_ids));
					$this->Reminder->deleteAll(['Reminder.element_id' => $task_ids]);
					$this->CurrentTask->deleteAll(['CurrentTask.task_id' => $task_ids]);					
					$response['success'] = true;
					}
					
					
					

				}
			}
		}

		echo json_encode($response);
		exit();
	}

	/*
		     * @name  		update_color_code
		     * @todo  		Update color code of project, workspace and elements
		     * @access		public
		     * @package  	App/Controller/StudiosController
	*/

	public function update_color_code() {

		$this->autoRender = false;

		$response = ['success' => false];
		$this->layout = 'ajax';

		if ($this->request->is('ajax')) {

			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;
				$id = $post['id'];
				$type = $post['type'];
				$color_code = $post['color_code'];

				// pr($post, 1);
				if (!empty($id)) {

					if (!empty($color_code)) {

						if (!empty($type)) {

							if ($type == 'project') {

								$this->Project->id = $id;
								$this->Project->set(array('color_code' => $color_code));
								$this->Project->save();

							}
							if ($type == 'workspace') {

								$this->Workspace->id = $id;
								$this->Workspace->set(array('color_code' => $color_code));
								$this->Workspace->save();

							}
							if ($type == 'element') {

								$this->Element->id = $id;
								$this->Element->set(array('color_code' => $color_code));
								$this->Element->save();

							}
							$response['success'] = true;
						}
					}
				}
			}
		}

		echo json_encode($response);
		exit();
	}

	/*
		     * @name  		sorted_elements
		     * @access		public
		     * @package  	App/Controller/StudiosController
	*/
	public function sorted_elements() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$response = [
				'success' => false,
				'msg' => '',
				'content' => null,
			];

			$data = null;

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;

				$type = $post['type'];

				$area_id = $post['area_id'];

				if (isset($area_id) && !empty($area_id)) {

					// get all elements of all the area
					$query_select = $query_conditions = null;
					$query_select[] = 'Element.*';

					$query = '';

					$query_conditions[] = "Element.area_id = '" . $area_id . "' ";
					$query_conditions[] = "Element.date_constraints = 1 ";
					$query_conditions[] = "Element.sign_off != 1 ";

					if (isset($type) && $type == 'start') {
						// $query_select[] = "TIMESTAMPDIFF( DAY, '".date("Y-m-d h:i:s")."', Element.start_date) AS totalDays ";
						// $query_conditions[] = "date(Element.start_date) >= '".date('Y-m-d')."' ";
						// $order = " ORDER BY totalDays ASC ";
						$order = " ORDER BY date(Element.start_date) ASC ";
					} elseif (isset($type) && $type == 'end') {
						// $query_select[] = "TIMESTAMPDIFF( DAY, '".date("Y-m-d h:i:s")."', Element.end_date) AS totalDays ";
						// $query_conditions[] = "date(Element.end_date) >= '".date('Y-m-d')."' ";
						// $order = " ORDER BY totalDays ASC ";
						$order = " ORDER BY date(Element.end_date) ASC ";
					} else {
						$order = '';
					}

					$select = 'SELECT ' . implode(', ', $query_select);
					$query .= $select;
					$query .= ' FROM elements as Element ';
					$cond = 'WHERE ' . implode(' AND ', $query_conditions);
					$query .= $cond;

					$query .= $order;
					// e($query, 1);
					$se_elements = $this->Element->query($query);

					$el_ids = null;
					if (isset($se_elements) && !empty($se_elements)) {
						$el_ids = Set::extract($se_elements, '/Element/id');
						$data = $se_elements;
					}

					$other_conditions = null;
					$other_conditions['Element.area_id'] = $area_id;
					if (isset($el_ids) && !empty($el_ids)) {
						$other_conditions['not'] = ['Element.id' => $el_ids];
					}

					$other_elements = $this->Element->find('all', [
						'recursive' => -1,
						'conditions' => $other_conditions,
						'order' => ['Element.sort_order ASC'],
					]);

					if (isset($other_elements) && !empty($other_elements)) {
						$data = array_merge($se_elements, $other_elements);
					}

					if (isset($data) && !empty($data)) {
						$element_ids = Set::extract($data, '/Element/id');
					}

					if (isset($element_ids) && !empty($element_ids)) {
						foreach ($element_ids as $index => $id) {

							if (isset($id) && !empty($id)) {

								$this->Element->id = $id;
								$this->Element->set(array('sort_order' => $index + 1));
								$this->Element->save();
							}
						}
					}
					//
				}
				$view = new View($this, false);
				$view->viewPath = 'Studios/partials'; // Directory inside view directory to search for .ctp files
				$view->layout = false; // if you want to disable layout
				$view->set('data', $data);

				$html = $view->render('element_box');

			}
			echo json_encode($html);
			exit();
			// $this->render('/Studios/partials/get_selected_templates');
		}
	}

	/*
		     * @name  		sorted_workspaces
		     * @access		public
		     * @package  	App/Controller/StudiosController
	*/
	public function sorted_workspaces() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$response = [
				'success' => false,
				'msg' => '',
				'content' => null,
			];

			$data = null;

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;

				$type = $post['type'];

				$project_id = $post['project_id'];

				if (isset($project_id) && !empty($project_id)) {

					$query_select = $query_conditions = null;
					$query_select[] = 'Workspace.*';

					$query = '';

					$query_conditions[] = "Workspace.sign_off != 1 ";
					$query_conditions[] = "Workspace.id IN (SELECT workspace_id FROM `project_workspaces` WHERE project_id = '" . $project_id . "') ";
					$query_conditions[] = "Workspace.start_date IS NOT NULL ";
					$query_conditions[] = "Workspace.end_date IS NOT NULL ";

					if (isset($type) && $type == 'start') {
						// $query_select[] = "TIMESTAMPDIFF( DAY, '".date("Y-m-d")."', date(Workspace.start_date)) AS totalDays ";
						// $query_conditions[] = "date(Workspace.start_date) >= '".date('Y-m-d')."' ";
						// $order = " ORDER BY totalDays ASC ";
						$order = " ORDER BY date(Workspace.start_date) ASC ";
					} elseif (isset($type) && $type == 'end') {
						// $query_select[] = "TIMESTAMPDIFF( DAY, '".date("Y-m-d")."', date(Workspace.end_date)) AS totalDays ";
						// $query_conditions[] = "date(Workspace.end_date) >= '".date('Y-m-d')."' ";
						$order = " ORDER BY date(Workspace.end_date) ASC ";
					} else {
						$order = '';
					}

					$select = 'SELECT ' . implode(', ', $query_select);
					$query .= $select;
					$query .= ' FROM workspaces as Workspace ';
					$cond = 'WHERE ' . implode(' AND ', $query_conditions);
					$query .= $cond;

					$query .= $order;

					$se_elements = $this->Workspace->query($query);

					$el_ids = null;
					if (isset($se_elements) && !empty($se_elements)) {
						$el_ids = Set::extract($se_elements, '/Workspace/id');
						$data = $se_elements;
					}

					$other_conditions = null;
					$other_conditions['ProjectWorkspace.project_id'] = $project_id;
					if (isset($el_ids) && !empty($el_ids)) {
						$other_conditions['not'] = ['Workspace.id' => $el_ids];
					}

					$this->ProjectWorkspace->Behaviors->load('Containable');

					$other_elements = $this->ProjectWorkspace->find('all', ['conditions' => $other_conditions, 'contain' => 'Workspace', 'order' => ['ProjectWorkspace.sort_order ASC']]);
					// pr($other_elements, 1);

					if (isset($other_elements) && !empty($other_elements)) {
						$data = array_merge($se_elements, $other_elements);
					}

					if (isset($data) && !empty($data)) {
						$ws_ids = Set::extract($data, '/Workspace/id');
					}

					foreach ($ws_ids as $index => $id) {

						if (isset($id) && !empty($id)) {

							$pw = $this->ProjectWorkspace->find('first', ['conditions' => ['ProjectWorkspace.project_id' => $project_id, 'ProjectWorkspace.workspace_id' => $id], 'fields' => ['ProjectWorkspace.id'], 'recursive' => -1]);
							$pw_id = Set::extract($pw, '/ProjectWorkspace/id');

							$this->ProjectWorkspace->id = $pw_id;
							$this->ProjectWorkspace->set(array('sort_order' => $index + 1));
							$this->ProjectWorkspace->save();
							// pr($id );
						}
					}
				}
				// die;
				$view = new View($this, false);
				$view->viewPath = 'Studios/partials'; // Directory inside view directory to search for .ctp files
				$view->layout = false; // if you want to disable layout
				$view->set('data', $data);

				$html = $view->render('workspace_box');

			}
			echo json_encode($html);
			exit();
			// $this->render('/Studios/partials/get_selected_templates');
		}
	}

	/*
		     * @name  		move_element
		     * @todo  		Remove an area
		     * @access		public
		     * @package  	App/Controller/StudiosController
	*/
	public function move_element() {

		$this->autoRender = false;

		$response = ['success' => false];
		$this->layout = 'ajax';

		if ($this->request->is('ajax')) {
			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;
				$element_id = $post['element_id'];
				$area_id = $post['area_id'];
				$max_order = task_max_sort_order($area_id);


				if (dbIdExists('Element',$element_id) ) {

				if (!empty($element_id) && !empty($area_id)) {
					$this->Element->id = $element_id;
					$data['Element']['updated_user_id'] = $this->user_id;
					$data['Element']['area_id'] = $area_id;
					$data['Element']['sort_order'] =  $max_order;
					if ($this->Element->save($data)) {

						// added by pawan element value update or save in store procudure =============================================================
						$postprojectid = element_project($element_id);
						$ele_wspid = element_workspace($element_id);

						$result = $this->ElementPermission->find('first',array('conditions'=>array(
								'user_id' => $this->user_id,
								'element_id' => $element_id,
								'project_id' => $postprojectid
								)
							  )
							);


						if( !empty($result['ElementPermission']['id']) ){
							//Stored Procedure
							$updateQuery = "Update element_permissions SET workspace_id = $ele_wspid WHERE user_id = $this->user_id AND element_id = $element_id AND project_id = $postprojectid ";
							$this->ElementPermission->query($updateQuery);
							//$this->save_element_share($this->user_id,$element_id,$area_id);

						} else {

							$row = $this->Element->find('first', [
								'conditions' => [
									'Element.id' => $element_id,
								],
								'recursive' => 1,
							]);

							$nPermit = null;
							if (isset($row['Permissions']) && !empty($row['Permissions'])) {
								$nPermit = arraySearch($row['Permissions'], 'user_id', $this->user_id, false);
							}

							$postWorkspaceId = area_workspace($area_id);
							$postprojectid = element_project($element_id);

							$arr['ElementPermission']['user_id'] = $this->user_id;
							$arr['ElementPermission']['element_id'] = $element_id;
							$arr['ElementPermission']['project_id'] = $postprojectid;
							$arr['ElementPermission']['workspace_id'] = $postWorkspaceId;
							$arr['ElementPermission']['permit_read'] = (!empty($nPermit)) ? $nPermit[0]['permit_read'] : 0;
							$arr['ElementPermission']['permit_add'] = (!empty($nPermit)) ? $nPermit[0]['permit_add'] : 0;
							$arr['ElementPermission']['permit_edit'] = (!empty($nPermit)) ? $nPermit[0]['permit_edit'] : 0;
							$arr['ElementPermission']['permit_delete'] = (!empty($nPermit)) ? $nPermit[0]['permit_delete'] : 0;
							$arr['ElementPermission']['permit_copy'] = (!empty($nPermit)) ? $nPermit[0]['permit_copy'] : 0;
							$arr['ElementPermission']['permit_move'] = (!empty($nPermit)) ? $nPermit[0]['permit_move'] : 0;
							$this->ElementPermission->save($arr);

							$elementPermissionInsert_id = $this->ElementPermission->getLastInsertId();
							//$this->save_element_share($this->user_id,$element_id,$area_id,$elementPermissionInsert_id);

						}
						//==============================================


						$response['success'] = true;
					}
				}

				}else{

					$response['success'] = false;
				}
			}
		}

		echo json_encode($response);
		exit();
	}

	/*
		     * @name  		copy_element
		     * @todo  		Copy an element into an area with its all resources(documents, links, decisions etc.)
		     * @access		public
		     * @package  	App/Controller/StudiosController
	*/
	public function copy_element() {

		$this->autoRender = false;

		$response = ['success' => false];
		$this->layout = 'ajax';

		if ($this->request->is('ajax')) {
			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;
				$element_id = $post['element_id'];
				$area_id = $post['area_id'];
				// pr($post, 1);
				if (!empty($element_id) && !empty($area_id)) {

					$row = $this->Element->find('first', [
						'conditions' => [
							'Element.id' => $element_id,
						],
						'recursive' => 1,
					]);

					if(isset($row ) && !empty($row )){

					unset($row['Area']);

					$max_order = task_max_sort_order($area_id);
					$row['Element']['sort_order'] = $max_order;

					$row['Element']['area_id'] = $area_id;
					unset($row['Element']['id']);

					if( FUTURE_DATE == 'on' ){
						$target_wsp_id = area_workspace_id($area_id);
						$taget_wsp_detail = $this->Workspace->findById($target_wsp_id);
						$row['Element']['start_date']= $taget_wsp_detail['Workspace']['start_date'];
						$row['Element']['end_date']= $taget_wsp_detail['Workspace']['end_date'];
					}

					// copy only element data
					$allData['Element'] = $row['Element'];

					// find and remove element_id and primary id from all element assets
					// that are - element document, links and notes
					if (isset($row['Links']) && !empty($row['Links'])) {
						$nLinks = arraySearch($row['Links'], 'element_id', $element_id, true);
						$allData['Links'] = arraySearch($nLinks, 'id', null, true);
					}
					if (isset($row['Documents']) && !empty($row['Documents'])) {
						$nDocuments = arraySearch($row['Documents'], 'element_id', $element_id, true);
						$allData['Documents'] = arraySearch($nDocuments, 'id', null, true);
					}
					if (isset($row['Notes']) && !empty($row['Notes'])) {
						$nNotes = arraySearch($row['Notes'], 'element_id', $element_id, true);
						$allData['Notes'] = arraySearch($nNotes, 'id', null, true);
					}
					$nPermit = null;
					if (isset($row['Permissions']) && !empty($row['Permissions'])) {
						$nPermit = arraySearch($row['Permissions'], 'user_id', $this->user_id, false);
						// // pr($nPermit, 1);
						//$allData ['Permissions'] = arraySearch($nPermit, 'id', null, true);
					}

					// NOW SAVE IT TO DATABASE
					// if( $this->Element->save($newRow) ) {
					if ($this->Element->saveAll($allData, array('deep' => true))) {

						$insert_id = $this->Element->getLastInsertId();
						$postWorkspaceId = area_workspace($area_id);
						$postprojectid = element_project($element_id);

						$this->loadModel('ElementPermission');
						$arr['ElementPermission']['user_id'] = $this->user_id;
						$arr['ElementPermission']['element_id'] = $insert_id;
						$arr['ElementPermission']['project_id'] = $postprojectid;
						$arr['ElementPermission']['workspace_id'] = $postWorkspaceId;
						$arr['ElementPermission']['permit_read'] = (!empty($nPermit)) ? $nPermit[0]['permit_read'] : 0;
						$arr['ElementPermission']['permit_add'] = (!empty($nPermit)) ? $nPermit[0]['permit_add'] : 0;
						$arr['ElementPermission']['permit_edit'] = (!empty($nPermit)) ? $nPermit[0]['permit_edit'] : 0;
						$arr['ElementPermission']['permit_delete'] = (!empty($nPermit)) ? $nPermit[0]['permit_delete'] : 0;
						$arr['ElementPermission']['permit_copy'] = (!empty($nPermit)) ? $nPermit[0]['permit_copy'] : 0;
						$arr['ElementPermission']['permit_move'] = (!empty($nPermit)) ? $nPermit[0]['permit_move'] : 0;
						$this->ElementPermission->save($arr);

						// call stored procedure ================================
						$elementPermissionInsert_id = $this->ElementPermission->getLastInsertId();
						//$this->element_copy_past($this->user_id,$insert_id,$area_id,$postWorkspaceId,$postprojectid,$elementPermissionInsert_id);
						//=======================================================
						// Get Project Id with Element id; Update Project modified date
						$this->update_project_modify($element_id);
						$this->update_project_modify($insert_id);

						// Now copy all the documents of the selected element to the target element
						$task_response = $this->cut_copy_paste_docs($element_id, $insert_id, $post['action']);

						$response['success'] = true;
					}
				}

					}else{

						$response['success'] = false;
					}
			}
		}

		echo json_encode($response);
		exit();
	}

	public function cut_copy_paste_docs($origin = null, $target = null, $action = 'copy') {
		$done = false;

		if ((isset($origin) && !empty($origin)) && (isset($target) && !empty($target))) {

			$upload_path = WWW_ROOT . ELEMENT_DOCUMENT_PATH; // Upload Dir

			$folder_url = $upload_path . DS . $origin; // Copy/Move from
			$copy_url = $upload_path . DS . $target; // Copy/Move to
			// $dir = new Folder(APP_DIR . DS . "webroot" . DS . "img");
			$dir = new Folder($folder_url);

			if (isset($action) && !empty($action)) {

				// Get Project Id with Element id; Update Project modified date
				$this->update_project_modify($target);
				$this->update_project_modify($origin);

				$action_data = array(
					'to' => $copy_url, // copy/move to
					'from' => $folder_url, // will cause a cd() to occur
					'mode' => 0777,
					'scheme' => Folder::SKIP, // Skip directories/files that already exist.
					'recursive' => true,
				) // set false to disable recursive copy
				;

				if ($action == 'copy_to') {
					$done = $dir->copy($action_data);
				} else if ($action == 'move_to') {
					$done = $dir->move($action_data);
				}
			}
		}
		return $done;
	}

	public function update_project_modify($element_id = null) {
		if (!isset($element_id) || empty($element_id)) {
			return true;
		}

		$project_id = $this->Element->getProject($element_id);

		if (!empty($project_id)) {
			$this->Common->projectModified($project_id, $this->user_id);
			// e('here');
		}
		return true;
	}

	/*
		     * @name  		move_area
		     * @todo  		Remove an area
		     * @access		public
		     * @package  	App/Controller/StudiosController
			 * @updateddate	27 December 2019
			 * @update by	Pawan
	*/
	public function move_area() {

		$this->autoRender = false;

		$response = ['success' => false];
		$this->layout = 'ajax';

		if ($this->request->is('ajax')) {
			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;
				$workspace_id = $post['workspace_id'];
				$area_id = $post['area_id'];
				// pr($post, 1);

				if (dbIdExists('Area',$area_id) ) {

					// target wsp start and end detail
					$target_wsp_data = $this->targetWspDetail($workspace_id);
					// get old workspace task min date and max date
					$target_wsp_data = $target_wsp_data['Workspace'];

					if( isset($target_wsp_data) && !empty($target_wsp_data) ){
						$eleStartDate = null;
						$eleEndDate = null;
						$checkEleDate = $this->sendingElementDetail($area_id);
						if( isset($checkEleDate['start_date']) && !empty($checkEleDate['start_date']) && isset($checkEleDate['end_date']) && !empty($checkEleDate['end_date']) ){
							$eleStartDate = date('Y-m-d 00:00:00',strtotime($checkEleDate['start_date']));
							$eleEndDate = date('Y-m-d 00:00:00',strtotime($checkEleDate['end_date']));
						}

						if($target_wsp_data['sign_off'] != 1){

							 if( ( isset( $eleStartDate ) && !empty($eleStartDate ) ) && ( isset( $eleEndDate ) && !empty($eleEndDate ) )   ){

								//check with workspace start and end date
								if( (isset($target_wsp_data['end_date']) && $target_wsp_data['end_date'] < $eleEndDate) || (isset($target_wsp_data['start_date']) && $target_wsp_data['start_date'] > $eleStartDate ) ) {

									$response['message'] = "Target workspace data conditions are not matched with processed task data.";
									$response['success'] = false;

								} else {
									$response['success'] = true;
								}

							 } else if((isset($target_wsp_data['end_date']) && $target_wsp_data['end_date'] >= date('Y-m-d')) && (isset($target_wsp_data['start_date']) && $target_wsp_data['start_date'] <= date('Y-m-d 00:00:00'))) {

								$response['success'] = true;

							} else if((isset($target_wsp_data['start_date']) && $target_wsp_data['start_date'] > date('Y-m-d 00:00:00')) ) {
								if( FUTURE_DATE == 'off' ){
									$response['message'] = "Target workspace schedule has not reached the start date.";
									$response['success'] = false;
								} else {
									$response['success'] = true;
								}


							} else if((isset($target_wsp_data['end_date']) && $target_wsp_data['end_date'] < date('Y-m-d')) ) {

								$response['message'] = "Cannot move Task because the Workspace end date has passed.";
								$response['success'] = false;

							} else if(!isset($target_wsp_data['start_date'])) {
								$response['message'] = "Please add a schedule to target workspace first.";
								$response['success'] = false;

							} else {
								$response['success'] = true;
							}

							// above conditions get response true then area will be moved
							if( isset($response['success']) && $response['success'] == true ){

								// Enable studio status of the old workspace
								$this->Workspace->id = $post['old_workspace_id'];
								$wdata['Workspace']['studio_status'] = 1;
								$wdata['Workspace']['template_id'] = 0;
								if ($this->Workspace->save($wdata)) {
									$this->Workspace->id = null;
								}

								if (!empty($workspace_id) && !empty($area_id)) {
									// Enable studio status of the current workspace
									$this->Workspace->id = $workspace_id;
									$wdata['Workspace']['studio_status'] = 1;
									$wdata['Workspace']['template_id'] = 0;
									if ($this->Workspace->save($wdata)) {

										// Move current area to the selected workspace
										$this->Area->id = $area_id;
										$adata['Area']['workspace_id'] = $workspace_id;
										$adata['Area']['studio_status'] = 1;
										if ($this->Area->save($adata)) {}

										// Get all area of the current workspace
										$areaData = $this->Area->find('all', [
											'conditions' => [
												'Area.workspace_id' => $workspace_id,
											],
											'recursive' => -1,
											'fields' => ['Area.id'],
										]);

										// update all areas, and enable studio status of all, including the current
										if (isset($areaData) && !empty($areaData)) {
											$areaData = Set::extract($areaData, '/Area/id');
											$this->Area->updateAll(
												array('Area.template_detail_id' => 0, 'Area.studio_status' => 1),
												array('Area.id' => $areaData)
											);
										}

										$response['success'] = true;
									}
								}
							}

						} else if( $target_wsp_data['sign_off']== 1) {
							$response['message'] = "Target workspace has Signed-off.";
							$response['success'] = false;
						}
					}
				}
			}
		}
		echo json_encode($response);
		exit();
	}

	/*
		     * @name  		copy_area
		     * @todo  		Remove an area
		     * @access		public
		     * @package  	App/Controller/StudiosController
	*/
	public function copy_area() {

		$this->autoRender = false;

		$response = ['success' => false, 'content' => null];
		$this->layout = 'ajax';

		if ($this->request->is('ajax')) {
			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;
				$workspace_id = $post['workspace_id'];
				$area_id = $post['area_id'];
				// pr($post, 1);



				if (dbIdExists('Area',$area_id) ) {

				if (!empty($workspace_id) && !empty($area_id)) {
					// Enable studio status of the current workspace
					$this->Workspace->id = $workspace_id;
					$wdata['Workspace']['studio_status'] = 1;
					$wdata['Workspace']['template_id'] = 0;
					if ($this->Workspace->save($wdata)) {

						// Get all area of the current workspace
						$areaData = $this->Area->find('first', [
							'conditions' => [
								'Area.id' => $area_id,
							],
							'recursive' => -1,
						]);

						$areaData['Area']['workspace_id'] = $workspace_id;
						$areaData['Area']['template_detail_id'] = 0;
						$areaData['Area']['studio_status'] = 1;
						$areaData['Area']['is_standby'] = 0;

						unset($areaData['Area']['id']);
					//	$areaData['Area']['id'] = null;


						// update all areas, and enable studio status of all, including the current
						if ($this->Area->save($areaData,false)) {

							$new_area_id = $this->Area->getLastInsertId();

							// Get all area of the current workspace
							$all_area = $this->Area->find('all', [
								'conditions' => [
									'Area.workspace_id' => $workspace_id,
								],
								'recursive' => -1,
								'fields' => ['Area.id'],
							]);

							// update all areas, and enable studio status of all, including the current
							if (isset($all_area) && !empty($all_area)) {
								$all_area = Set::extract($all_area, '/Area/id');
								$this->Area->save(
									array('Area.template_detail_id' => 0, 'Area.studio_status' => 1),
									array('Area.id' => $all_area)
								);
							}

							// get all elements of all the areas
							$el_ids = $this->Element->find('all', [
								'recursive' => -1,
								'conditions' => ['Element.area_id' => $area_id],
							]);
							$el_ids = Set::extract($el_ids, '/Element/id');

							if (isset($el_ids) && !empty($el_ids)) {
								/* $this->Element->updateAll(
									array('Element.studio_status' => 1),
									array('Element.id' => $el_ids ),
									array('Element.area_id' => $new_area_id )
								); */

								foreach ($el_ids as $index => $ids) {
									$new_ids = $this->copy_element_data($ids, $new_area_id, 'copy');
									$response['content'][$new_ids] = 'success';
									/*$this->Element->updateAll(
										array('Element.studio_status' => 1, 'Element.id' => $new_ids),
										array('Element.area_id' => $new_area_id)
									);*/

								}
							}

						}

						$response['success'] = true;
					}
				}

				}else{

					$response['success'] = false;
				}
			}
		}

		echo json_encode($response);
		exit();
	}

	public function wsp_studio_status() {

		$this->autoRender = false;

		$response = ['success' => false, 'content' => null];
		$this->layout = 'ajax';

		if ($this->request->is('ajax')) {
			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;
				$type_id = $post['id'];
				$type = $post['type'];
				$wspCount = $areaCount = 0;

				$wspCount = $this->Workspace->find('count', ['conditions' => ['Workspace.id' => $type_id, 'Workspace.studio_status' => 1]]);
				$areaCounter = $this->Area->find('count', ['conditions' => ['Area.workspace_id' => $type_id]]);
				if (!empty($wspCount) && $areaCounter) {
					$response['success'] = true;
				} else {
					$areaCount = $this->Area->find('count', ['conditions' => ['Area.workspace_id' => $type_id, 'Area.studio_status' => 1]]);
					if (!empty($areaCount)) {
						$response['success'] = true;
					} /*else {
						$areaId = $this->Area->find('all', ['conditions' => ['Area.workspace_id' => $type_id], 'fields' => ['Area.id']]);
						if (isset($areaId) && !empty($areaId)) {
							$area_id = Set::extract($areaId, '/Area/id');
							$elCount = $this->Element->find('count', ['conditions' => ['Element.area_id' => $area_id, 'Element.studio_status' => 1]]);
							if (!empty($elCount)) {
								$response['success'] = true;
							}
						}
					}*/
				}
			}
		}

		echo json_encode($response);
		exit();
	}

	public function area_studio_status() {

		$this->autoRender = false;

		$response = ['success' => false, 'content' => null];
		$this->layout = 'ajax';

		if ($this->request->is('ajax')) {
			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;
				$type_id = $post['id'];
				$type = $post['type'];
				$wspCount = $areaCount = 0;
				$wspid = area_workspace_id($type_id);

				// pr($wspid);
				$areaCount = $this->Area->find('count', ['conditions' => ['Area.workspace_id' => $wspid, 'Area.studio_status' => 1]]);
				// pr($areaCount);
				if (!empty($areaCount)) {
					$response['success'] = true;
				} else {
					$elCount = $this->Element->find('count', ['conditions' => ['Element.area_id' => $type_id, 'Element.studio_status' => 1]]);
					if (!empty($elCount)) {
						$response['success'] = true;
					}
				}

			}
		}

		echo json_encode($response);
		exit();
	}

	public function copy_element_data($element_id = null, $area_id = null, $action = null) {

		if (!empty($element_id) && !empty($area_id)) {

			$row = $this->Element->find('first', [
				'conditions' => [
					'Element.id' => $element_id,
				],
				'recursive' => 1,
			]);

			unset($row['Area']);
			$row['Element']['area_id'] = $area_id;
			unset($row['Element']['id']);

			// copy only element data
			$allData['Element'] = $row['Element'];

			// find and remove element_id and primary id from all element assets
			// that are - element document, links and notes
			if (isset($row['Links']) && !empty($row['Links'])) {
				$nLinks = arraySearch($row['Links'], 'element_id', $element_id, true);
				$allData['Links'] = arraySearch($nLinks, 'id', null, true);
			}
			if (isset($row['Documents']) && !empty($row['Documents'])) {
				$nDocuments = arraySearch($row['Documents'], 'element_id', $element_id, true);
				$allData['Documents'] = arraySearch($nDocuments, 'id', null, true);
			}
			if (isset($row['Notes']) && !empty($row['Notes'])) {
				$nNotes = arraySearch($row['Notes'], 'element_id', $element_id, true);
				$allData['Notes'] = arraySearch($nNotes, 'id', null, true);
			}
			$nPermit = null;
			if (isset($row['Permissions']) && !empty($row['Permissions'])) {
				$nPermit = arraySearch($row['Permissions'], 'user_id', $this->user_id, false);
				// // pr($nPermit, 1);
				//$allData ['Permissions'] = arraySearch($nPermit, 'id', null, true);
			}

			// NOW SAVE IT TO DATABASE
			// if( $this->Element->save($newRow) ) {
			$allData['Element']['studio_status'] = 1;
			if ($this->Element->saveAll($allData, array('deep' => true))) {

				$insert_id = $this->Element->getLastInsertId();
				$postWorkspaceId = area_workspace($area_id);
				$postprojectid = element_project($element_id);

				$this->loadModel('ElementPermission');
				$arr['ElementPermission']['user_id'] = $this->user_id;
				$arr['ElementPermission']['element_id'] = $insert_id;
				$arr['ElementPermission']['project_id'] = $postprojectid;
				$arr['ElementPermission']['workspace_id'] = $postWorkspaceId;
				$arr['ElementPermission']['permit_read'] = (!empty($nPermit)) ? $nPermit[0]['permit_read'] : 0;
				$arr['ElementPermission']['permit_add'] = (!empty($nPermit)) ? $nPermit[0]['permit_add'] : 0;
				$arr['ElementPermission']['permit_edit'] = (!empty($nPermit)) ? $nPermit[0]['permit_edit'] : 0;
				$arr['ElementPermission']['permit_delete'] = (!empty($nPermit)) ? $nPermit[0]['permit_delete'] : 0;
				$arr['ElementPermission']['permit_copy'] = (!empty($nPermit)) ? $nPermit[0]['permit_copy'] : 0;
				$arr['ElementPermission']['permit_move'] = (!empty($nPermit)) ? $nPermit[0]['permit_move'] : 0;
				$this->ElementPermission->save($arr);

				// Get Project Id with Element id; Update Project modified date
				$this->update_project_modify($element_id);
				$this->update_project_modify($insert_id);

				// Now copy all the documents of the selected element to the target element
				$task_response = $this->cut_copy_paste_docs($element_id, $insert_id, $action);

				return $insert_id;
			}
		}
		return false;
	}

	public function conversation() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$response = ['success' => false, 'content' => null];

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				if (isset($post['msg']) && !empty($post['msg'])) {
					$content = '';
					$filename = WWW_ROOT . 'conversation.txt';
					if (file_exists($filename)) {
						//write
						$content = '<p>' . $post['msg'] . ' <b>' . date('d M, Y h:i:s') . '</b></p>';
						$file = new File($filename, true);
						$file->write($content);
					} else {
						//read
						$file = new File($filename);
						$content = $file->read(true, 'r');
					}

					$response['success'] = true;
					// $response['content']['msg'] = $post['msg'];
					$response['content']['msg'] = $content;
				}
			}
			echo json_encode($response);
			exit();

		}
	}

	/*========================================================================================*/

	public function trash_a_project($project_id = null) {

		if ($this->request->isAjax()) {

			$this->layout = false;
			$viewData = null;
			$viewData['project_id'] = $project_id;

			$this->set($viewData);
			$this->render('/Studios/partials/trash_a_project');

		}
	}

	public function trash_a_workspace($workspace_id = null) {

		if ($this->request->isAjax()) {

			$this->layout = false;
			$viewData = null;
			$viewData['workspace_id'] = $workspace_id;
			$viewData['project_id'] = $project_id = workspace_pid($workspace_id);
			$viewData['pw_id'] = workspace_pwid($project_id, $workspace_id);

			$this->set($viewData);
			$this->render('/Studios/partials/trash_a_workspace');

		}
	}

	public function trash_an_area($area_id = null) {

		if ($this->request->isAjax()) {

			$this->layout = false;
			$viewData = null;
			$viewData['area_id'] = $area_id;
			$viewData['workspace_id'] = $workspace_id = area_workspace_id($area_id);
			$viewData['project_id'] = workspace_pid($workspace_id);

			$this->set($viewData);
			$this->render('/Studios/partials/trash_an_area');

		}
	}

	public function trash_a_task($element_id = null) {

		if ($this->request->isAjax()) {

			$this->layout = false;
			$viewData = null;
			$viewData['element_id'] = $element_id;
			$viewData['project_id'] = element_project($element_id);

			$this->set($viewData);
			$this->render('/Studios/partials/trash_a_task');

		}
	}


	// Old Workspace all task min start date and max end date
	public function sendingElementDetail($area_id = null){
		if( isset($area_id) && !empty($area_id) ){

			$startDate = "SELECT
					min(elements.start_date) as ele_start_date
			FROM `elements`
			where
				  elements.date_constraints = 1 and
				elements.area_id = $area_id ";

			$endDate = "SELECT max(elements.end_date) as ele_end_date
			FROM `elements`
			where
				  elements.date_constraints = 1 and
				elements.area_id = $area_id ";
			$wspStartDate =  $this->Element->query($startDate);
			$wspEndDate =  $this->Element->query($endDate);

			$dates = array();
			$dates['start_date'] = $wspStartDate[0][0]['ele_start_date'];
			$dates['end_date'] = $wspEndDate[0][0]['ele_end_date'];
			return $dates;
		}
	}

	// Target Workspace start and end date
	public function targetWspDetail($workspace_id = null){
		if( isset($workspace_id) && !empty($workspace_id) ){
			$wspDate = $this->Workspace->find('first',array('conditions'=>array('Workspace.id'=>$workspace_id),'recursive'=>-1,'fields'=>array('Workspace.start_date','Workspace.end_date','Workspace.sign_off') ) );
			return $wspDate;

		}
	}

}
