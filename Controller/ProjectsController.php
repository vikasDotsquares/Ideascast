<?php
App::uses('Controller/Component', 'Auth', 'Session', 'RequestHandler');

App::uses('Sanitize', 'Utility');
App::uses('CakeEmail', 'Network/Email');
App::import('Vendor', 'MPDF56/PhpWord');
App::uses('HttpSocket', 'Network/Http');

class ProjectsController extends AppController {

	public $name = 'Projects';
	public $uses = ['User', 'UserDetail', 'ProjectPermission', 'UserSetting', 'Category', 'Aligned', 'UserProject', 'Project', 'Workspace', 'Area', 'ProjectWorkspace', 'Element', 'ProjectGroup', 'ProjectGroupUser', 'Skill', "ProjectSkill", "ProjectComment", "ProjectRag", "EmailNotification",   "Program", "DeleteData", "ProjectElementType", "ProjectElementTypeTemp","SignoffProject",'Subject','ProjectSubject','KnowledgeDomain','ProjectDomain','ProjectRagComment' ];
	public $user_id = null;
	public $pagination = null;
	public $mongoDB = null;
	public $live_setting;
	public $components = array('Mpdf', 'Common', 'Group', 'Projects', 'Users');
	public $objView = null;
	public $program_offset = 50;
	public $listing_offset = 50;
	public $activity_offset = 50;
	public $wsp_team_offset = 50;
	public $project_team_offset = 50;
	public $project_risk_offset = 50;

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
	public $helpers = array('Html', 'Form', 'Session', 'Time', 'Text', 'Common', 'ViewModel', 'Mpdf', 'Image', 'Permission');

	public function beforeFilter() {
		parent::beforeFilter();
		if (isset($this->request->data)) {
			// $this->request->data = Sanitize::clean($this->data, array('encode' => false));


		}
		$view = new View();
		$this->objView = $view;

		$this->Auth->allow('project_people_all', 'chat_all_projects', 'chat_projects', 'projectScheduleOverdueEmailCron', 'domain_connect', 'risk_project_elements', 'get_project_elements');

		$this->set('controller', 'projects');

		$this->pagination['limit'] = 4;
		$this->pagination['summary_model'] = 'Projects';
		$this->pagination['options'] = array(
			'url' => array_merge(
				array(
					'controller' => $this->request->params['controller'],
					'action' => 'get_more',
				), $this->request->params['pass'], $this->request->params['named']
			),
		);

		$this->set('JeeraPaging', $this->pagination);
		$this->user_id = $this->Auth->user('id');

		$this->live_setting = LIVE_SETTING;

		if( $_SERVER['HTTP_HOST'] != LOCALIP )  {

			if( $this->Session->read('Auth.User.role_id') == 3 ){
				$this->redirect(['controller' => 'organisations','action' => 'dashboard']);
			}
			if( $this->Session->read('Auth.User.role_id') == 1 ){
				$this->redirect(['controller' => 'dashboard','action' => 'index']);
			}
		}
		$this->quick_share_paging = 50;
		$this->setJsVar('quick_share_paging', $this->quick_share_paging);
	}

	public function page_sample($workspace_id = null) {

		$this->layout = 'inner';
		$workspace_id = (isset($workspace_id)) ? $workspace_id : 29;
		$this->set('title_for_layout', __('Page Sample', true));

		$view = new View();
		$viewModal = $view->loadHelper('ViewModel');

		$templateData = $viewModal->getAreaTemplate($workspace_id);

		$data['templateRows'] = $templateData;

		$this->set('data', $data);
	}

	public function crop_image() {

		$this->layout = 'inner';
	}

	public function workspace_templates($workspace_id = null) {

		$this->layout = 'inner';

		$this->set('title_for_layout', __('Workspace Template', true));

		$view = new View();
		$viewModal = $view->loadHelper('ViewModel');

		$templateData = $viewModal->getAreaTemplate($workspace_id);

		$data['templateRows'] = $templateData;

		$this->set('data', $data);
	}

	public function sample_multiuploader() {

		$this->layout = 'inner';

		$this->set('title_for_layout', __('Page Sample', true));

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			if ($this->request->is('post') || $this->request->is('put')) {

				pr($_FILES);
				pr($this->params['form'], 1);

			}

		}
		$this->loadModel('Skill');
		$skills = $this->Skill->find('list', ['fields' => ['id', 'title']]);
		$this->set('skills', $skills);
		$this->setJsVar('skills', $skills);
	}
	public function get_token_data() {

		$this->layout = 'inner';

		$this->set('title_for_layout', __('Page Sample', true));
		$skills = null;
		if ($this->request->isAjax()) {
			// pr($this->request->query['q'], 1);
			$this->layout = 'ajax';

			$this->loadModel('Skill');
			$skills = $this->Skill->find('all', ['conditions' => ['title like' => '%' . $this->request->query['q'] . '%'], 'fields' => ['id', 'title']]);
			if (isset($skills) && !empty($skills)) {
				foreach ($skills as $key => $value) {
					$sk[] = ['id' => $value['Skill']['id'], 'name' => $value['Skill']['title']];
				}
			}
			// pr($sk, 1);

		}
		echo json_encode($sk);
		exit;

	}

	public function template_samples() {

		$this->layout = 'inner';

		$this->set('title_for_layout', __('Template Sample', true));
	}

	public function index($project_id = null, $type = null) {
		$this->layout = 'inner';

		$this->set('title_for_layout', __('Project Summary', true));

		$user_id = $this->user_id;

		unset($_SESSION['data']);

		// REDIRECT TO PROJECT LIST PAGE IF NO PROJECT SELECTED OR NO PERMISSION OF THIS PROJECT
		if (!isset($project_id) || empty($project_id) || !dbExists('Project', $project_id) || check_project_permission($project_id)) {

			$this->redirect(array('controller' => 'projects', 'action' => 'lists'));

		}


		// SAVE TO ACTIVITY
		$task_data = [
			'project_id' => $project_id,
			//'element_type' => 'do_lists',
			'updated_user_id' => $this->user_id,
			'message' => 'Project viewed',
			'updated' => date("Y-m-d H:i:s"),
		];
		$this->loadModel('ProjectActivity');
		$this->ProjectActivity->id = null;
		$this->ProjectActivity->save($task_data);

		// GET PROJECT DETAIL
		$project_detail = $this->objView->loadHelper('Permission')->project_data($project_id)[0];

		$crumb = [
			'last' => [
				'data' => [
					'title' => htmlentities($project_detail['projects']['title'], ENT_QUOTES, "UTF-8"),
					'data-original-title' => htmlentities($project_detail['projects']['title'], ENT_QUOTES, "UTF-8"),
				],
			],
		];

		// PROJECT TILES
		$this->loadModel('ProjectTile');
		$p_tiles = [
				[ 'title' => 'tile_confidence', 'status' => 1 ],
				[ 'title' => 'tile_burndown', 'status' => 1 ],
				[ 'title' => 'tile_3', 'status' => 0 ],
				[ 'title' => 'tile_4', 'status' => 0 ],
				[ 'title' => 'tile_5', 'status' => 0 ],
				[ 'title' => 'tile_6', 'status' => 0 ],
				[ 'title' => 'tile_7', 'status' => 0 ],
				[ 'title' => 'tile_8', 'status' => 0 ],
			];
		$tiles_count = $this->ProjectTile->find('count', ['conditions' => ['project_id' => $project_id]]);
		if(!isset($tiles_count) || empty($tiles_count)){
			// INSERT ALL TILES FOR THIS PROJECT
			$tile_data = [];
			foreach ($p_tiles as $key => $value) {
				$tile_data[] = ['ProjectTile' =>
						[
							'project_id' => $project_id,
							'user_id' => $this->user_id,
							'filename' => $value['title'],
							'sort_order' => ($key + 1),
							'status' => $value['status']
						]
					];
			}
			$this->ProjectTile->saveAll($tile_data);
		}
		$project_tiles = $this->ProjectTile->find('all', ['conditions' => ['project_id' => $project_id], 'order' => ['sort_order ASC']]);
		$this->set('project_tiles', $project_tiles);

		$current_tab = (isset($this->params['named']['tab']) && !empty($this->params['named']['tab']) ) ? $this->params['named']['tab'] : null;

		$project_role = $project_detail['user_permissions']['role'];
		$project_type = $project_detail[0]['project_type'];
		if($project_role != 'Creator') {
			$crumb_key = ($project_type == 'g_project') ? 'Group Received Projects' : 'Received Projects';
			$extra_crumb = [
				$crumb_key => [
					'data' => [
						'url' => '/projects/lists/',
						'class' => 'tipText',
						'title' => $crumb_key,
						'data-original-title' => $crumb_key,
					],
				],
			];
		}

		$this->loadModel('ProjectOppOrg');
		$opportunity_cnt = $this->ProjectOppOrg->find('count', array(
									'conditions' => array('ProjectOppOrg.project_id' => $project_id)
								)
							);

		$summary_limit = 50;
		$this->set('opportunity_cnt', $opportunity_cnt);
		$this->set('project_detail', $project_detail['projects']);
		$this->set('project_permission', $project_role);
		$this->set('crumb', $crumb);
		$this->set('project_id', $project_id);
		$this->set('limit', $summary_limit);
		$this->set('current_page', 0);
		$this->setJsVar('project_permission', $project_role);
		$this->setJsVar('project_id', $project_id);
		$this->setJsVar('wsp_limit', $summary_limit);
		$this->setJsVar('type', $type);
		$this->set('current_tab', $current_tab);

		$filters = ['limit' => $this->project_team_offset, 'project_id' => $project_id];
		// $project_teams = $this->objView->loadHelper('Scratch')->project_teams($filters);
		// $this->set('project_teams', $project_teams);
		$this->setJsVar('project_team_offset', $this->project_team_offset);

		$query_params = ['project_id' => $project_id, 'limit' => $this->project_risk_offset];
		// $project_risks = $this->objView->loadHelper('Scratch')->project_risks($query_params);
		// $this->set('project_risks', $project_risks);
		$this->setJsVar('project_risk_offset', $this->project_risk_offset);

	}

	public function project_breakdown() {
		if ($this->request->isAjax()) {

			$this->layout = false;
			$viewData = null;
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
			}

			$this->set($viewData);
			$this->render('/Projects/sections/project_breakdown');

		}
	}


	public function project_breakdown_json() {
		// if ($this->request->isAjax()) {
		// die('adfsdf');

			$this->layout = false;
			$view = new View($this, false);
			$view->viewPath = 'Projects/sections';
			$data = null;
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				// pr($post, 1);
				$project_breakdown = $this->objView->loadHelper('Permission')->project_breakdown($this->user_id, $post['project_id']);
				$data = $project_breakdown[0][0]['projectRoots'];
				$view->set('data', $data);
			}

			$html = $view->render('project_breakdown_json');
			echo json_encode($html);
			exit();

		// }
	}


	public function summary_data() {
		if ($this->request->isAjax()) {

			$this->layout = false;
			$viewData = null;
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				// pr($post, 1);
				$viewData = $post;
			}

			$this->set($viewData);
			$this->render('/Projects/sections/summary_data');

		}
	}

	public function project_summary_count() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$response = [
				'success' => false,
				'content' => null,
			];
			$user_id = $this->user_id;

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;

				$project_workspace_count = $this->objView->loadHelper('Permission')->project_summary_count($post['project_id']);
				$project_workspace_count = (issemp($project_workspace_count)) ? $project_workspace_count[0][0]['counter'] : 0;
				$response['success'] = true;
				$response['content'] = $project_workspace_count;

			}

			echo json_encode($response);
			exit();

		}
	}

	public function summary_sort() {
		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$response = [
				'success' => false
			];
			if ($this->request->is('post') || $this->request->is('put')) {
				$this->loadModel('ProjectWorkspace');
				$post = $this->request->data;
				$project_id = $post['project_id'];

				$workspace_ids = $this->request->data['workspace_id'];
				foreach ($workspace_ids as $index => $workspace_id) {
					$order = $index + 1;
					if ($workspace_id != '') {
						$query = "update project_workspaces pw set pw.sort_order = $order WHERE pw.workspace_id = $workspace_id AND pw.project_id = $project_id";
						$this->ProjectWorkspace->query($query);
					}
				}
				$act_data = [
					'project_id' => $project_id,
					'updated_user_id' => $this->user_id,
					'message' => 'Project Workspace order updated'
				];
				prj_activities($act_data);
				$response['success'] = true;
			}
			echo json_encode($response);
			exit;
		}
	}

	public function summary_wsp_color() {

		if ($this->request->isAjax()) {

			$response = [
				'success' => false
			];

			$this->layout = 'ajax';
			$this->autoRender = false;

			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;

				$this->Workspace->id = $post['workspace_id'];
				$project_id = $post['project_id'];
				if ($this->Workspace->saveField('color_code', $post['color_code'])) {

					$response['success'] = true;
					$act_data = [
							'project_id' => $project_id,
							'workspace_id' => $post['workspace_id'],
							'updated_user_id' => $this->user_id,
							'message' => 'Workspace color updated'
						];
					wsp_activities($act_data);
				}
			}

			echo json_encode($response);
			exit();
		}
	}

	public function project_pic($project_id = null) {

		$this->layout = 'ajax';

		if ($this->request->isAjax()) {

			$data = array();

			if ($this->request->is('post') || $this->request->is('put')) {
				$folder_url = WWW_ROOT . PROJECT_IMAGE_PATH;
				$upload_object = $_FILES["image_file"];

			}

			if (isset($project_id) && !empty($project_id)) {

				$data = $this->Project->find('first', ['conditions' => ['Project.id' => $project_id], 'recursive' => -1]);
			}

			$this->set('project_id', $project_id);
			$this->set('data', $data);

			$this->render('/Projects/sections/project_pic');
		}

	}

	public function summary($project_id = null, $create_project = 0) {

		$this->layout = 'inner';

		$this->set('title_for_layout', __('Project Summary', true));

		$user_id = $this->Auth->user('id');

		unset($_SESSION['data']);


		if (!isset($project_id) || empty($project_id) || !dbExists('Project', $project_id)) {

			$this->redirect(array('controller' => 'projects', 'action' => 'lists'));

		}

		// if (!dbExists('Project', $project_id)) {
		if (empty($project_id) ) {
			$this->redirect(array('controller' => 'projects', 'action' => 'lists'));
		}

		//check permission
		if( check_project_permission($project_id)  ){
			$this->redirect(array('controller' => 'projects', 'action' => 'lists'));
		}


		$task_data = [
			'project_id' => $project_id,
			//'element_type' => 'do_lists',
			'updated_user_id' => $this->user_id,
			'message' => 'Project viewed',
			'updated' => date("Y-m-d H:i:s"),
		];
		$this->loadModel('ProjectActivity');
		$this->ProjectActivity->id = null;
		$this->ProjectActivity->save($task_data);

		/* -----------Group code----------- */
		$this->UserProject->unbindModel(['hasMany' => 'ProjectPermission']);
		$this->UserProject->unbindModel(['belongsTo' => ['User']]);
		$projectsg = $this->UserProject->find('first', ['recursive' => 1, 'conditions' => ['UserProject.project_id' => $project_id], 'fields' => ['UserProject.id']]);

		$pgupid = $projectsg['UserProject']['id'];
		$conditionsG = null;
		$conditionsG['ProjectGroupUser.user_id'] = $this->user_id;
		$conditionsG['ProjectGroupUser.user_project_id'] = $pgupid;
		$conditionsG['ProjectGroupUser.approved'] = 1;
		$projects_group_shared_user = $this->ProjectGroupUser->find('first', array(
			'conditions' => $conditionsG,
			'fields' => array('ProjectGroupUser.project_group_id'),
			'recursive' => -1,
		));

		if (isset($projects_group_shared_user) && !empty($projects_group_shared_user)) {
			$group_permission = $this->Group->group_permission_details($project_id, $projects_group_shared_user['ProjectGroupUser']['project_group_id']);

			$pll_level = $group_permission['ProjectPermission']['project_level'];

			$this->set('gpid', $projects_group_shared_user['ProjectGroupUser']['project_group_id']);

			if (isset($pll_level) && $pll_level == 1) {
				$this->set('project_level', 1);
			}
		}
		/* -----------Group code----------- */
		//pr($group_permission,1);

		/* -----------sharing code----------- */
		$conditionsN = null;
		$conditionsN['ProjectPermission.user_id'] = $this->user_id;
		$this->loadModel('ProjectPermission');
		/* $projects_shared = $this->ProjectPermission->find('all', array(
			'conditions' => $conditionsN,
			'fields' => array('ProjectPermission.user_project_id'),
			'order' => 'ProjectPermission.created DESC',
			'recursive' => -1,
		)); */
		/* -----------sharing code----------- */

		// CHECK THAT HTTP-REFERER IS manage_project
		// TO SHOW DIFFERENT MESSAGE ON LISTING
		$create_referer = false;
		if ($this->referer() == Router::url(array('action' => 'manage_project'), true)) {
			$create_referer = true;
		} else {
			$create_referer = false;
		}
		$this->set('create_referer', $create_referer);
		$projects = null;

		if (isset($project_id) && !empty($project_id)) {
			$this->UserProject->unbindModel(['hasMany' => 'ProjectPermission']);
			$projects = $this->UserProject->find('first', ['recursive' => 1, 'conditions' => ['UserProject.user_id' => $this->user_id, 'UserProject.project_id' => $project_id]]);

			// pr($user_projects, 1);

			$this->set('projects', $projects);
			// echo $this->UserProject->_query();die;
			$this->set('project_id', $project_id);
		}

		$projects = $this->UserProject->find('first', ['recursive' => 1, 'conditions' => ['Project.id' => $project_id]]);

		$this->set(compact('projects'));

		$extra_crumb = null;

		if (isset($projects) && !empty($projects) && $projects['UserProject']['user_id'] != $this->user_id) {
			$crumb_key = (isset($projects_group_shared_user) && !empty($projects_group_shared_user)) ? 'Group Received Projects' : 'Received Projects';
			$extra_crumb = [
				$crumb_key => [
					'data' => [
						'url' => '/projects/lists/',
						'class' => 'tipText',
						'title' => $crumb_key,
						'data-original-title' => $crumb_key,
					],
				],
			];
		} else {
			//$extra_crumb = get_category_list($project_id);
		}

		// Get logged in users projects
		$this->set('projects', $projects);

		$project_title = htmlentities($projects['Project']['title'], ENT_QUOTES);

		$crumb = [
			'last' => [
				'data' => [
					'title' => $project_title,
					'data-original-title' => $project_title,
				],
			],
		];
		if (isset($extra_crumb) && !empty($extra_crumb)) {
			$crumb = array_merge($extra_crumb, $crumb);
		}
		// pr($crumb, 1);
		$this->set('crumb', $crumb);

		$this->set('p_id', $project_id);

		// $project_type = CheckProjectType($project_id, $this->user_id);
		// $this->set('project_type', $project_type);

		$this->setJsVar('project_id', $project_id);

		$this->set('perPageWspLimit', 50);
		$this->setJsVar('wsp_limit', 50);
	}


	public function load_more_wsp() {
		if ($this->request->isAjax()) {

			$this->layout = false;
			$viewData = null;
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				$viewData = $post;
			}

			$this->set($viewData);
			$this->render('/Projects/partials/load_more_wsp');

		}
	}


	public function get_wsp_count() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$response = [
				'success' => false,
				'content' => null,
			];
			$user_id = $this->user_id;

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;

				$project_workspace_count = $this->objView->loadHelper('ViewModel')->project_workspace_count( $post['project_id'] );

				$response['success'] = true;
				$response['content'] = $project_workspace_count;

			}

			echo json_encode($response);
			exit();

		}
	}


	public function task_results($project_id = null) {

		$this->layout = 'inner';
		$this->set('title_for_layout', __('Results', true));

		$view = new View();
		$viewModel = $view->loadHelper('ViewModel');

		$row = $data = null;
		// post status
		// 1 = Not specified
		// 2 = Not started
		// 3 = progressing
		// 4 = completed
		// 5 = overdue
		// sort by
		// 1 = Ending Soonest
		// 2 = Ending Last

		$result = $params = null;

		$conditions = $pw_condition = [];
		$order = '';

		if (isset($project_id) && !empty($project_id)) {
			$params['project_id'] = $project_id;
			$pw_condition['ProjectWorkspace.project_id'] = $project_id;
			$pw_condition['Workspace.studio_status !='] = 1;

			$workspaces = $elements = $ws_ids = $area_ids = null;

			$this->ProjectWorkspace->Behaviors->load('Containable');

			$workspaces = $this->ProjectWorkspace->find('all', ['conditions' => $pw_condition, 'order' => ['ProjectWorkspace.sort_order ASC'], 'contain' => 'Workspace']);

			if (isset($workspaces) && !empty($workspaces)) {

				$workspace = $workspaces;

			}

			if (isset($workspace) && !empty($workspace)) {
				$params['workspaces'] = $workspace;
			}
			// pr($params, 1);
		}
		$this->set('params', $params);

		/***********************************************************************************/
		$this->set('project_id', $project_id);

		$this->Project->unbindModel(['hasMany' => 'ProjectWorkspace']);
		$project_detail = null;
		$project_detail = $this->Project->UserProject->find('first', array(
			'recursive' => 2,
			'conditions' => [
				'UserProject.project_id' => $project_id,
			],
		));
		$this->set('project_detail', $project_detail);

		$this->Project->unBindModel(array('hasMany' => array('ProjectWorkspace', 'UserProject')));
		// $this->Project->unbindModelAll();
		$results = $this->Project->find('first', [
			'conditions' => [
				'Project.id' => $project_id,
			],
			'fields' => [
				'Project.id',
				'Project.category_id',
				'Project.title',
				'Category.id AS cat_id',
				'Category.title',
				'Category.parent_id',
			],
		]);
		// pr($results, 1);
		$category_bread = null;

		$parent_titles = '';

		$cat_title = '';

		if (isset($results) && !empty($results)) {

			$category_id = $results['Category']['cat_id'];

			if (isset($results['Category']) && !empty($results['Category'])) {
				$category_bread = ['id' => $results['Category']['cat_id'], 'parent_id' => $results['Category']['parent_id'], 'keys' => true, 'reverse' => true];

				$cat_title = $results['Category']['title'];
			}
		}
		// pr($category_bread, 1);
		$this->set('category_bread', $category_bread);

		$crumb = [];

		$crumb = [
			'Summary' => [
				'data' => [
					'url' => '/projects/index/' . $project_id,
					'class' => 'tipText',
					'title' => $project_detail['Project']['title'],
					'data-original-title' => $project_detail['Project']['title'],
				],
			],
			'Project Report' => [
				'data' => [
					'url' => '/projects/reports/' . $project_id,
					'class' => 'tipText',
					'title' => 'Project Report',
					'data-original-title' => 'Project Report',
				],
			],
			'last' => ['Results'],
		];

		/*if (!empty($category_id)) {
			$parent_titles = category_breadcrumb($category_bread, true);
			$cat_crumb['Category'] = [
				'data' => [
					'url' => '/categories/index/' . $category_id,
					'class' => 'tipText',
					'title' => $parent_titles,
					'data-original-title' => $cat_title,
				],
			];

			$crumb = array_merge($cat_crumb, $crumb);
		}*/

		$this->set('crumb', $crumb);

		$this->set('page_heading', $project_detail['Project']['title']);
		$this->set('page_subheading', 'Information about your Project');
	}
	/*
		     * @name  		get_more
		     * @access		public
		     * @package  	App/Controller/ProjectsController
	*/

	public function project_people($project_id = null) {

		if ($this->request->is('get')) {

			$this->layout = 'ajax';

			$view = new View();
			$common = $view->loadHelper('Common');

			$this->set('project_id', $project_id);
		}

	}

	public function chat_all_projects($uid = null) {

		$this->autoLayout = false;
		$this->render(false);

		if (isset($uid) && !empty($uid)) {
			$userId = $uid;
		} else {
			$userId = $this->Session->read("Auth.User.id");
		}

		$project_lists_chat = $this->Common->get_user_project_list_chat($userId);
		$project_lists_chat = array_filter($project_lists_chat);

		$dim = [];
		if (isset($project_lists_chat) && !empty($project_lists_chat)) {
			foreach ($project_lists_chat as $dat => $val) {

				$dim[] = array('id' => $dat, 'title' => htmlentities($val));

			}
		}

		$response = [
			'success' => true,
			'content' => $dim,
		];

		$this->response->body(json_encode($response));
		$this->response->statusCode(200);
		$this->response->type('application/json');

		return $this->response;

		die;

	}

	public function domain_connect() {

		$whatINeed = explode('.', $_SERVER['HTTP_HOST']);
		$whatINeed = $whatINeed[0];
		$db = dbname;
		$db_user = dbuser;
		$db_pass = dbpass;

		$this->autoLayout = false;
		$this->render(false);

		$dim = [];
		if (isset($db) && !empty($db)) {

			$dim = array('db' => $db, 'db_user' => $db_user, 'dbpass' => $db_pass);

		}

		$response = [
			'success' => true,
			'content' => $dim,
		];

		$this->response->body(json_encode($response));
		$this->response->statusCode(200);
		$this->response->type('application/json');

		return $this->response;

		die;

	}

	public function chat_projects() {

		$this->autoLayout = false;
		$this->render(false);

		$project_lists_chat = $this->Project->find('list', ['conditions' => ['Project.studio_status !=' => 1, 'Project.title IS NOT NULL'], 'fields' => ['Project.id', 'Project.title']]);
		$dim = null;
		if (isset($project_lists_chat) && !empty($project_lists_chat)) {
			foreach ($project_lists_chat as $dat => $val) {
				$dim[] = array('id' => $dat, 'title' =>  ($val));
			}
		}
		$response = [
			'success' => true,
			'content' => $dim,
		];
		$this->response->body(json_encode($response));
		$this->response->statusCode(200);
		$this->response->type('application/json');

		return $this->response;

		die;

	}


	public function project_people_all($project_id = null) {
		$json = [];
		// if ($this->request->isAjax()) {

		$this->autoLayout = false;
		$this->render(false);

		$view = new View();
		$common = $view->loadHelper('Common');

		$viewModal = $view->loadHelper('ViewModel');

		$id = [];
		$data = null;

		if (isset($project_id) && !empty($project_id)) {

			$owner = $common->ProjectOwner($project_id, $this->Session->read('Auth.User.id'));

			$owners = $owner['UserProject']['user_id'];

			$data['participants'] = participants($project_id, $owner['UserProject']['user_id']);

			$data['participants_owners'] = participants_owners($project_id, $owner['UserProject']['user_id']);

			$data['participantsGpOwner'] = participants_group_owner($project_id);

			$data['participantsGpSharer'] = participants_group_sharer($project_id);

		}

		if (isset($data) && !empty($data)) {

			if (isset($owners) && !empty($owners)) {
				$userDetail = $viewModal->get_user($owners, null, 1);
				// pr($userDetail, 1);

				if (isset($userDetail) && !empty($userDetail)) {
					$user_name = $userDetail['UserDetail']['first_name'] . ' ' . $userDetail['UserDetail']['last_name'];

					$id[] = $userDetail['User']['id'];

				}}
			$owners = isset($owners) ? $owners : 0;

			if (isset($data['participants_owners']) && !empty($data['participants_owners'])) {
				foreach ($data['participants_owners'] as $key => $val) {

					if ($owner != $val) {
						$unbind = ['hasMany' => ['ProjectPermission', 'WorkspacePermission', 'ElementPermission', 'UserProject', 'UserSetting'], 'hasOne' => ['UserInstitution']];
						$userDetail = $viewModal->get_user($val, $unbind, 1);
						// pr($userDetail, 1);

						if (isset($userDetail) && !empty($userDetail)) {
							$user_name = $userDetail['UserDetail']['first_name'] . ' ' . $userDetail['UserDetail']['last_name'];

							$id[] = $userDetail['User']['id'];

						}}
				}
			}
			if (isset($data['participantsGpOwner']) && !empty($data['participantsGpOwner'])) {
				foreach ($data['participantsGpOwner'] as $key => $val) {

					$unbind = ['hasMany' => ['ProjectPermission', 'WorkspacePermission', 'ElementPermission', 'UserProject', 'UserSetting'], 'hasOne' => ['UserInstitution']];
					$userDetail = $viewModal->get_user($val, $unbind, 1);
					// pr($userDetail, 1);

					if (isset($userDetail) && !empty($userDetail)) {
						$user_name = $userDetail['UserDetail']['first_name'] . ' ' . $userDetail['UserDetail']['last_name'];

						$profile_pic = $userDetail['UserDetail']['profile_pic'];

						$id[] = $userDetail['User']['id'];
					}

				}
			}
			if (isset($data['participants']) && !empty($data['participants'])) {
				foreach ($data['participants'] as $key => $val) {

					$unbind = ['hasMany' => ['ProjectPermission', 'WorkspacePermission', 'ElementPermission', 'UserProject', 'UserSetting'], 'hasOne' => ['UserInstitution']];
					$userDetail = $viewModal->get_user($val, $unbind, 1);
					// pr($userDetail, 1);

					if (isset($userDetail) && !empty($userDetail)) {
						$user_name = $userDetail['UserDetail']['first_name'] . ' ' . $userDetail['UserDetail']['last_name'];

						$profile_pic = $userDetail['UserDetail']['profile_pic'];

						$id[] = $userDetail['User']['id'];

					}

				}
			}
			if (isset($data['participantsGpSharer']) && !empty($data['participantsGpSharer'])) {
				foreach ($data['participantsGpSharer'] as $key => $val) {

					$unbind = ['hasMany' => ['ProjectPermission', 'WorkspacePermission', 'ElementPermission', 'UserProject', 'UserSetting'], 'hasOne' => ['UserInstitution']];
					$userDetail = $viewModal->get_user($val, $unbind, 1);
					// pr($userDetail, 1);

					if (isset($userDetail) && !empty($userDetail)) {
						$user_name = $userDetail['UserDetail']['first_name'] . ' ' . $userDetail['UserDetail']['last_name'];

						$id[] = $userDetail['User']['id'];
					}

				}
			}

		}

		/* $request = array(
				'header' => array('Content-Type' => 'application/json',
				),
			);
			$this->response->type('json');  */

		$json = array_values(array_unique($id));

		/*

			$json =  json_encode($json );

			$this->response->header(array(
				'WWW-Authenticate: Negotiate',
				'Content-type: application/json'
			));

			$this->response->body($json);
			$this->response->statusCode(200);
			$this->response->type('application/json');

			return $this->response;
			//echo $json; */

		// }
		$response = [
			'success' => true,
			'content' => $json,
		];

		$this->response->body(json_encode($response));
		$this->response->statusCode(200);
		$this->response->type('application/json');

		return $this->response;
	}

	public function wsp_people($projectwsp_id = null, $project_id = null) {

		if ($this->request->is('get')) {

			$this->layout = 'ajax';

			$view = new View();
			$group = $view->loadHelper('Group');
			$common = $view->loadHelper('Common');

			$data = null;

			if (isset($projectwsp_id) && !empty($projectwsp_id)) {

				$owner = $common->ProjectOwner($project_id, $this->Session->read('Auth.User.id'));

				$data['participants'] = wsp_participants($project_id, $projectwsp_id, $owner['UserProject']['user_id']);

				$data['participants_owners'] = participants_owners($project_id, $owner['UserProject']['user_id'], 1);
				// pr($data['participants_owners'], 1);
				$data['participantsGpOwner'] = participants_group_owner($project_id);

				$data['participantsGpSharer'] = wsp_grps_sharer($project_id, $projectwsp_id);
			}

			$this->set('data', $data);

			if (isset($owner) && !empty($owner)) {
				$this->set('owner', $owner['UserProject']['user_id']);
			}

			$this->set('projectwsp_id', $projectwsp_id);
			$this->set('project_id', $project_id);
		}

	}

	/*
		     * @name  		get_workspaces
		     * @access		public
		     * @package  	App/Controller/ProjectsController
	*/

	public function get_workspaces($project_id) {

		if (!$project_id) {
			return null;
		}

		$user_id = $this->Auth->user('id');

		$us_permission = $this->Common->userproject($project_id, $user_id);
		$pr_permission = $this->Common->project_permission_details($project_id, $user_id);
		//$ws_permission = $this->Common->work_permission_details($project_id, $user_id);

		$grp_id = $this->Group->GroupIDbyUserID($project_id, $user_id);
		if (isset($grp_id) && !empty($grp_id)) {

			if (isset($group_permission['ProjectPermission']['project_level']) && $group_permission['ProjectPermission']['project_level'] == 1) {
				$project_level = $group_permission['ProjectPermission']['project_level'];
				$this->set('project_level', $project_level);
			}
			$this->set('gpid', $grp_id);
		}

		if (isset($grp_id) && !empty($grp_id)) {

			$pr_permission = $this->Group->group_permission_details($project_id, $grp_id);
			$ws_permission = $this->Group->group_work_permission_details($project_id, $grp_id);
			//pr($wwsid); die;
		}

		if (isset($pr_permission) && !empty($pr_permission)) {
			$ws_permission = $this->Common->work_permission_details($project_id, $user_id);
		}

		//pr($us_permission);
		//pr($pr_permission ,1);
		// pr($ws_permission);

		if ((!empty($us_permission)) || (isset($pr_permission['ProjectPermission']['project_level']) && $pr_permission['ProjectPermission']['project_level'] == 1) || (((isset($ws_permission) && !empty($ws_permission))))) {

			if (!empty($us_permission) || (isset($pr_permission['ProjectPermission']['project_level']) && $pr_permission['ProjectPermission']['project_level'] == 1)) {

				$projectWorkspaces = $this->Project->ProjectWorkspace->find('all', ['conditions' => ['Workspace.id !=' => '', 'ProjectWorkspace.project_id' => $project_id, 'ProjectWorkspace.leftbar_status' => 1], 'fields' => ['ProjectWorkspace.project_id', 'Workspace.id', 'Workspace.title', 'ProjectWorkspace.sort_order'], 'order' => ['ProjectWorkspace.project_id ASC', 'ProjectWorkspace.sort_order ASC']]);
			} else if (((isset($ws_permission) && !empty($ws_permission)))) {

				$projectWorkspaces = $this->Project->ProjectWorkspace->find('all', ['conditions' => ['ProjectWorkspace.project_id' => $project_id, 'ProjectWorkspace.leftbar_status' => 1, 'Workspace.id !=' => '', 'ProjectWorkspace.id' => $ws_permission], 'fields' => ['ProjectWorkspace.project_id', 'Workspace.id', 'Workspace.title', 'ProjectWorkspace.sort_order'], 'order' => ['ProjectWorkspace.project_id ASC', 'ProjectWorkspace.sort_order ASC']]);
			}
		}

		if (isset($projectWorkspaces) && !empty($projectWorkspaces)) {
			$list = [];
			foreach ($projectWorkspaces as $key => $v) {
				$wtitle = '';

				$sortOrder = $v['ProjectWorkspace']['sort_order'];
				$wtitle = $v['Workspace']['title'];
				$list[$sortOrder] = array('title' => $wtitle, 'id' => $v['Workspace']['id']);
			}
			return $list;
		} else {
			return null;
		}

	}

	/*
		     * @name  		get_more
		     * @access		public
		     * @package  	App/Controller/ProjectsController
	*/

	public function get_more() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$model = 'Project';

			if ($this->request->is('post') || $this->request->is('put')) {
				// pr($this->request->data, 1);
				$paginator = array(
					'conditions' => array(
						$model . '.status' => 1,
					),
					'limit' => $this->pagination['limit'],
					"order" => $model . ".created DESC",
				);

				$this->paginate = $paginator;
				$this->set('templates', $this->paginate($model));
				$this->set('project_id', $this->request->data);

				$this->pagination['show_summary'] = true;
				$this->pagination['model'] = $model;
				$this->set('JeeraPaging', $this->pagination);

				$this->render('/Project/partials/list_more');
			}
		}
	}

	/*
		     * @name  		get_more
		     * @access		public
		     * @package  	App/Controller/ProjectsController
	*/

	public function partial_reports($project_id = null) {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$model = 'ProjectWorkspace';

			if ($this->request->is('post') || $this->request->is('put')) {

				// pr($this->request->data, 1);
				$this->pagination['limit'] = 6;
				$this->pagination['summary_model'] = 'Workspaces';
				$this->pagination['options'] = array(
					'url' => array_merge(
						array(
							'controller' => $this->request->params['controller'],
							'action' => 'partial_reports',
						), $this->request->params['pass'], $this->request->params['named']
					),
				);
				// $this->$model->Behaviors->load('Containable');
				$this->Project->unbindModel(['belongsTo' => ['Workspace', 'Project']]);
				$paginator = array(
					'conditions' => [
						'ProjectWorkspace.project_id' => $this->request->data['project_id'],
						'Workspace.id IS NOT NULL',
						'Workspace.studio_status <>' => 1,
					],
					// 'conditions' => array(
					// $model . '.project_id' => $this->request->data['project_id']

					// ),
					'joins' => array(
						array(
							'alias' => 'Workspaces',
							'table' => 'workspaces',
							'type' => 'INNER',
							'conditions' => 'Workspaces.id = ProjectWorkspace.workspace_id',
						),
					),
					'joins' => array(
						array(
							'alias' => 'Projects',
							'table' => 'projects',
							'type' => 'INNER',
							'conditions' => 'Projects.id = ProjectWorkspace.project_id',
						),
					),
					// 'contain' => ['Workspace', 'Project'],
					'limit' => $this->request->data['limit'],
					"order" => [$model . '.sort_order' => 'ASC'],
					// "order" => [$model . ".sort_order ASC", $model . ".project_id ASC", $model . ".workspace_id ASC"]
				);
//
				$this->paginate = $paginator;
				$this->set('project_workspaces', $this->paginate($model));
				$this->set('project_id', $this->request->data['project_id']);

				$this->pagination['show_summary'] = true;
				$this->pagination['model'] = $model;
				$this->set('JeeraPaging', $this->pagination);

				$this->Project->unbindModel(['hasMany' => 'ProjectWorkspace']);
				$project_detail = $this->Project->UserProject->find('first', array(
					'recursive' => 2,
					'conditions' => [
						'UserProject.user_id' => $this->user_id,
						'UserProject.project_id' => $project_id,
					],
				));
				$this->set(compact('project_detail'));

				// pr($this->pagination, 1);
				$this->render('/Projects/partials/partial_reports');
			}
		}
	}

	public function project_detail($project_id = null) {
		if ($this->request->isAjax()) {

			$this->layout = false;
			$viewData = null;
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				$viewData = $post;
			}

			$this->set($viewData);
			$this->render('/Projects/partials/project_detail');

		}
	}



	public function total_recieved($category_id = null) {
		$conditions = null;
		$conditions['ProjectPermission.user_id'] = $this->user_id;
		$conditions['ProjectPermission.user_project_id !='] = ''; // ['not' => array('User.site_url' => null)];
		$conditions['ProjectPermission.owner_id !='] = '';
		$conditions['ProjectPermission.share_by_id !='] = '';
		$conditions['UserProject.id !='] = '';

		$this->loadModel('ProjectPermission');

		$projects = $this->ProjectPermission->find('count', array(
			'conditions' => $conditions,
			'fields' => array('ProjectPermission.*'),
			'recursive' => 1,
		));

		// e($this->ProjectPermission->_query());
		return $projects;

	}

	public function share_lists($category_id = null) {

		$this->layout = 'inner';

		$this->set('title_for_layout', __('Received Projects', true));
		$this->set('page_heading', __('Received Projects', true));

		$conditions = null;
		$conditions['ProjectPermission.user_id'] = $this->user_id;
		$conditions['ProjectPermission.user_project_id !='] = ''; // ['not' => array('User.site_url' => null)];
		$conditions['ProjectPermission.owner_id !='] = '';
		$conditions['ProjectPermission.share_by_id !='] = '';

		$this->loadModel('ProjectPermission');

		$projects = $this->ProjectPermission->find('all', array(
			'conditions' => $conditions,
			'fields' => array('ProjectPermission.*'),
			'order' => 'ProjectPermission.created DESC',
			//'group' => ['ProjectPermission.owner_id' ],
			'group' => ['ProjectPermission.share_by_id'],
			'recursive' => -1,
		));

		// echo $this->ProjectPermission->_query();
		// pr($projects);
		// die;
		$this->set('projects', $projects);

		$this->set('crumb', ['last' => ['Received Projects']]);
	}

	public function share_projects($category_id = null, $suser_id = null) {

		$this->layout = 'inner';

		$this->set('title_for_layout', __('Received Projects', true));
		$this->set('page_heading', __('The project name', true));

		$conditions = null;
		//$conditions['UserProject.user_id'] = $this->user_id;
		$conditions['UserProject.status'] = 1;
		$conditions['UserProject.project_id !='] = ''; // ['not' => array('User.site_url' => null)];

		$conditionsN = null;
		$conditionsN['ProjectPermission.user_id'] = $this->user_id;
		$conditionsN['ProjectPermission.share_by_id'] = $suser_id;

		if (isset($category_id) && !empty($category_id)) {
			//	$conditions['Project.category_id'] = $category_id;
		}

		$this->loadModel('ProjectPermission');
		$projects_shared = $this->ProjectPermission->find('all', array(
			'conditions' => $conditionsN,
			'fields' => array('ProjectPermission.user_project_id'),
			'order' => 'ProjectPermission.created DESC',
			'recursive' => -1,
		));

		foreach ($projects_shared as $sshare) {
			$idms[] = $sshare['ProjectPermission']['user_project_id'];
		}

		if (isset($idms) && !empty($idms)) {
			$conditions['UserProject.id'] = $idms;
		}

		$projects = $this->UserProject->find('all', array(
			'joins' => array(
				array(
					'table' => 'projects',
					'alias' => 'Projects',
					'type' => 'INNER',
					'conditions' => array(
						'UserProject.project_id = Projects.id',
					),
				),
			),
			'conditions' => $conditions,
			'fields' => array('UserProject.*', 'Projects.*'),
			'order' => 'UserProject.modified DESC',
			'group' => ['UserProject.project_id'],
			'recursive' => 1,
		));
		// e($this->UserProject->_query());

		$this->set('projects', $projects);

		$this->set('crumb', ['Received Projects' => [
			'data' => [
				'url' => '/projects/share_lists/',
				'class' => 'tipText',
				'title' => "Received Projects",
				'data-original-title' => "Received Projects",
			],
		], 'last' => ['Project']]);

		$categories_list = $this->Category->find('threaded', array('recursive' => -1));
		$categories = tree_list($categories_list, 'Category', 'id', 'title');
		$this->set(compact('categories'));
	}

	public function shared_projects() {

		$this->layout = 'ajax';

		if ($this->request->isAjax()) {

			$data = array();
			$html = null;

			if ($this->request->is('post') || $this->request->is('put')) {
				/**************************************************/
				$user_id = $this->request->data['user'];
				$suser_id = $this->request->data['share'];
				$conditions = null;
				//$conditions['UserProject.user_id'] = $this->user_id;
				$conditions['UserProject.status'] = 1;
				$conditions['UserProject.project_id !='] = ''; // ['not' => array('User.site_url' => null)];

				$conditionsN = null;
				$conditionsN['ProjectPermission.user_id'] = $this->user_id;
				$conditionsN['ProjectPermission.share_by_id'] = $suser_id;

				if (isset($category_id) && !empty($category_id)) {
					//	$conditions['Project.category_id'] = $category_id;
				}

				$this->loadModel('ProjectPermission');
				$projects_shared = $this->ProjectPermission->find('all', array(
					'conditions' => $conditionsN,
					'fields' => array('ProjectPermission.user_project_id'),
					'order' => 'ProjectPermission.created DESC',
					'recursive' => -1,
				));

				foreach ($projects_shared as $sshare) {
					$idms[] = $sshare['ProjectPermission']['user_project_id'];
				}

				if (isset($idms) && !empty($idms)) {
					$conditions['UserProject.id'] = $idms;
				}

				$projects = $this->UserProject->find('all', array(
					'joins' => array(
						array(
							'table' => 'projects',
							'alias' => 'Projects',
							'type' => 'INNER',
							'conditions' => array(
								'UserProject.project_id = Projects.id',
							),
						),
					),
					'conditions' => $conditions,
					'fields' => array('UserProject.*', 'Projects.*'),
					'order' => 'UserProject.modified DESC',
					'group' => ['UserProject.project_id'],
					'recursive' => 1,
				));
				// e($this->UserProject->_query());

				/***********************************************************/

				$view = new View($this, false);
				$view->viewPath = 'Projects'; // Directory inside view directory to search for .ctp files
				$view->layout = false; // if you want to disable layout
				$view->set('projects', $projects);
				$view->set('user', $user_id);
				$view->set('share', $suser_id);

				$html = $view->render('shared_projects');
			}

			echo json_encode($html);
			exit();
		}
	}

	/*
		     * @name  		popups
		     * @todo  		Open Popup Modal Boxes method
		     * @access		public
		     * @package  	App/Controller/ProjectsController
		     * @return  	void
	*/

	public function popups($form) {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$response = array();
			if (isset($form) && ($form == 'open_project' || $form == 'project_reports')) {
				$projects = $this->Project->UserProject->find('all', array(
					'fields' => ['Project.id', 'Project.title'],
					'recursive' => 1,
					'conditions' => [
						'UserProject.user_id' => $this->user_id,
					],
				));
				$combined = Set::combine($projects, '/Project/id', '/Project/title');
				$combined = Sanitize::stripTags($combined, 'b', 'br', 'strong', 'u', 'em');
				$response['projects'] = $combined;
			}
			if (isset($form) && $form == 'confirm_delete') {
				$response['message'] = "Are you sure you want to send this item to trash?";
			}
			$this->set('response', $response);

			$this->set('form_name', $form);

			$this->render(DS . 'Projects' . DS . 'partials' . DS . 'popup_forms');
		}
	}

	/*
		     * @name  		popover
		     * @todo  		Open Popup Modal Box for update area title
		     * @access		public
		     * @package  	App/Controller/ProjectsController
		     * @return  	void
	*/

	public function popover($area_id = null) {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$response = $row = null;

			// if ($this->request->is('post') || $this->request->is('put')) {
			$row = $this->Area->find('first', array(
				'fields' => ['id', 'title'],
				'recursive' => -1,
				'conditions' => [
					'id' => $area_id,
				],
			));
			// }
			// pr($row, 1);
			$this->set('row', $row['Area']);

			$this->render(DS . 'Projects' . DS . 'partials' . DS . 'popover');
		}
	}

	/**
	 * Open Popup Modal Boxes method
	 *
	 * @return void
	 */
	public function popup_filter($term = '') {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$response = array();

			$user_id = $this->Auth->user('id');
			$term = $this->request->query('term');

			if (isset($term) && !empty($term)) {
				$this->Project->recursive = -2;
				$all = $this->Project->find('all', array(
					'fields' => [
						'id', 'title',
					],
					'conditions' => [
						'Project.user_id' => $user_id,
						'Project.title LIKE' => trim($term) . '%',
					],
				));
				// echo $this->getLastQuery();
				$projNames = null;
				if (!is_null($all)) {
					foreach ($all as $id => $p) {
						$projData = $p['Project'];
						$projNames[] = ['label' => $projData['title'], 'value' => $projData['id']];
					}
					$response = $projNames;
				}
			} else {
				echo "empty";
			}
			$this->set(compact('response'));
			$this->set('_serialize', 'response');
		}
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
				if (isset($this->request->query['term']) && !empty($this->request->query['term'])) {
					$term = $this->request->query['term'];
					$excludeSkills = (isset($this->request->query['selectedSkills']) && !empty($this->request->query['selectedSkills'])) ? $this->request->query['selectedSkills'] : '';
					if($excludeSkills != '') {
						$query = "SELECT id, title FROM skills WHERE (`title` like '% $term%' OR  `title` like  '%$term %'  OR  `title` like  '% $term %' OR `title` like '$term%' ) AND id NOT IN (".$excludeSkills.") order by title asc";
					} else {
						$query = "SELECT id, title FROM skills WHERE `title` like '% $term%' OR  `title` like  '%$term %'  OR  `title` like  '% $term %' OR `title` like '$term%' order by title asc";
					}
					$skills = $this->Skill->query($query);
					if (isset($skills) && !empty($skills)) {
						$skills = Set::combine($skills, '{n}.skills.id', '{n}.skills.title');
					}
					if (isset($skills) && !empty($skills)) {
						$response['success'] = true;
						$response['content'] = $skills;
					}

				}
			}
			echo json_encode($response);
			exit;
		}

	}

	public function get_skill_object() {

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
				if (isset($this->request->query['term']) && !empty($this->request->query['term'])) {
					$term = $this->request->query['term'];
					$excludeSkills = (isset($this->request->query['selectedSkills']) && !empty($this->request->query['selectedSkills'])) ? $this->request->query['selectedSkills'] : '';
					if($excludeSkills != '') {
						$query = "SELECT id, title FROM skills WHERE (`title` like '% $term%' OR  `title` like  '%$term %'  OR  `title` like  '% $term %' OR `title` like '$term%' ) AND id NOT IN (".$excludeSkills.") order by title asc";
					} else {
						$query = "SELECT id, title FROM skills WHERE `title` like '$term%' OR  `title` like  '$term %'  OR  `title` like  '$term %' OR `title` like '$term%' order by title asc";
					}
					$skills = $this->Skill->query($query);
					if (isset($skills) && !empty($skills)) {
						$skills = Set::combine($skills, '{n}.skills.id', '{n}.skills.title');
					}
					if (isset($skills) && !empty($skills)) {
						$response['success'] = true;
						$skill = [];
						foreach ($skills as $key => $value) {
							$skill[] = ['id' => $key, 'text' => $value];
						}
						$response['content'] = $skill;
					}

				}
			}
			echo json_encode($response);
			exit;
		}

	}

	public function get_project_skills() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$response = [
				'success' => false,
				'msg' => null,
				'content' => null,
			];
			$skills = $project_skills = [];
			$project_id = $this->request->data['project_id'];
			$skills = $this->ProjectSkill->find('all', array('conditions' => array('ProjectSkill.project_id' => $project_id)));
			// pr($skills, 1);
			if (isset($skills) && !empty($skills)) {
				foreach ($skills as $key => $value) {
					$project_skills[] = ['label' => $value['Skill']['title'], 'key' => $value['ProjectSkill']['skill_id']];
				}
			}

			echo json_encode($project_skills);
			exit;
		}

	}

	public function get_skills_data() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$response = [
				'success' => false,
				'msg' => null,
				'content' => null,
			];
			$return_skills = [];
			$skill_selected = $this->request->data['skills'];
			// pr($skill_selected, 1);
			if (isset($skill_selected) && !empty($skill_selected)) {
				$skills = $this->Skill->find('all', array('conditions' => array('Skill.id' => $skill_selected)));
				foreach ($skills as $key => $value) {
					$return_skills[] = ['label' => $value['Skill']['title'], 'key' => $value['Skill']['id']];
				}
			}

			echo json_encode($return_skills);
			exit;
		}

	}




	public function get_domains() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			// pr($this->request->query['term'] , 1);
			$response = [
				'success' => false,
				'msg' => null,
				'content' => null,
			];
			$domains = [];
			if ($this->request->isJson()) {
				if (isset($this->request->query['term']) && !empty($this->request->query['term'])) {
					$term = $this->request->query['term'];
					$excludeDomains = (isset($this->request->query['selectedDomains']) && !empty($this->request->query['selectedDomains'])) ? $this->request->query['selectedDomains'] : '';
					if($excludeDomains != '') {
						$query = "SELECT id, title FROM knowledge_domains WHERE (`title` like '% $term%' OR  `title` like  '%$term %'  OR  `title` like  '% $term %' OR `title` like '$term%' ) AND id NOT IN (".$excludeDomains.") order by title asc";
					} else {
						$query = "SELECT id, title FROM knowledge_domains WHERE `title` like '% $term%' OR  `title` like  '%$term %'  OR  `title` like  '% $term %' OR `title` like '$term%' order by title asc";
					}
					$domains = $this->KnowledgeDomain->query($query);
					if (isset($domains) && !empty($domains)) {
						$domains = Set::combine($domains, '{n}.knowledge_domains.id', '{n}.knowledge_domains.title');
					}
					if (isset($domains) && !empty($domains)) {
						$response['success'] = true;
						$response['content'] = $domains;
					}

				}
			}
			echo json_encode($response);
			exit;
		}

	}

	public function get_domain_object() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			// pr($this->request->query['term'] , 1);
			$response = [
				'success' => false,
				'msg' => null,
				'content' => null,
			];
			$domains = [];
			if ($this->request->isJson()) {
				if (isset($this->request->query['term']) && !empty($this->request->query['term'])) {
					$term = $this->request->query['term'];
					$excludeDomains = (isset($this->request->query['selectedDomains']) && !empty($this->request->query['selectedDomains'])) ? $this->request->query['selectedDomains'] : '';
					if($excludeDomains != '') {
						$query = "SELECT id, title FROM knowledge_domains WHERE (`title` like '% $term%' OR  `title` like  '%$term %'  OR  `title` like  '% $term %' OR `title` like '$term%' ) AND id NOT IN (".$excludeDomains.") order by title asc";
					} else {
						$query = "SELECT id, title FROM knowledge_domains WHERE `title` like '$term%' OR  `title` like  '$term %'  OR  `title` like  '$term %' OR `title` like '$term%' order by title asc";
					}
					$domains = $this->KnowledgeDomain->query($query);
					if (isset($domains) && !empty($domains)) {
						$domains = Set::combine($domains, '{n}.knowledge_domains.id', '{n}.knowledge_domains.title');
					}
					if (isset($domains) && !empty($domains)) {
						$response['success'] = true;
						$domain = [];
						foreach ($domains as $key => $value) {
							$domain[] = ['id' => $key, 'text' => $value];
						}
						$response['content'] = $domain;
					}

				}
			}
			echo json_encode($response);
			exit;
		}

	}

	public function get_project_domains() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$response = [
				'success' => false,
				'msg' => null,
				'content' => null,
			];
			$domains = $project_domains = [];
			$project_id = $this->request->data['project_id'];
			$domains = $this->ProjectDomain->find('all', array('conditions' => array('ProjectDomain.project_id' => $project_id)));
			// pr($skills, 1);
			if (isset($domains) && !empty($domains)) {
				foreach ($domains as $key => $value) {
					$project_domains[] = ['label' => $value['KnowledgeDomain']['title'], 'key' => $value['ProjectDomain']['domain_id']];
				}
			}

			echo json_encode($project_domains);
			exit;
		}

	}

	public function get_domains_data() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$response = [
				'success' => false,
				'msg' => null,
				'content' => null,
			];
			$return_domains = [];
			$domain_selected = $this->request->data['domains'];
			// pr($skill_selected, 1);
			if (isset($domain_selected) && !empty($domain_selected)) {
				$domains = $this->KnowledgeDomain->find('all', array('conditions' => array('KnowledgeDomain.id' => $domain_selected)));
				foreach ($domains as $key => $value) {
					$return_domains[] = ['label' => $value['KnowledgeDomain']['title'], 'key' => $value['KnowledgeDomain']['id']];
				}
			}

			echo json_encode($return_domains);
			exit;
		}

	}



	public function get_subjects() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			// pr($this->request->query['term'] , 1);
			$response = [
				'success' => false,
				'msg' => null,
				'content' => null,
			];
			$subjects = [];
			if ($this->request->isJson()) {
				if (isset($this->request->query['term']) && !empty($this->request->query['term'])) {
					$term = $this->request->query['term'];
					$excludeSubjects = (isset($this->request->query['selectedSubjects']) && !empty($this->request->query['selectedSubjects'])) ? $this->request->query['selectedSubjects'] : '';
					if($excludeSubjects != '') {
						$query = "SELECT id, title FROM subjects WHERE (`title` like '% $term%' OR  `title` like  '%$term %'  OR  `title` like  '% $term %' OR `title` like '$term%' ) AND id NOT IN (".$excludeSubjects.") order by title asc";
					} else {
						$query = "SELECT id, title FROM subjects WHERE `title` like '% $term%' OR  `title` like  '%$term %'  OR  `title` like  '% $term %' OR `title` like '$term%' order by title asc";
					}
					$subjects = $this->Subject->query($query);
					if (isset($subjects) && !empty($subjects)) {
						$subjects = Set::combine($subjects, '{n}.subjects.id', '{n}.subjects.title');
					}
					if (isset($subjects) && !empty($subjects)) {
						$response['success'] = true;
						$response['content'] = $subjects;
					}

				}
			}
			echo json_encode($response);
			exit;
		}

	}

	public function get_subject_object() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			// pr($this->request->query['term'] , 1);
			$response = [
				'success' => false,
				'msg' => null,
				'content' => null,
			];
			$subjects = [];
			if ($this->request->isJson()) {
				if (isset($this->request->query['term']) && !empty($this->request->query['term'])) {
					$term = $this->request->query['term'];
					$excludeSubjects = (isset($this->request->query['selectedSubjects']) && !empty($this->request->query['selectedSubjects'])) ? $this->request->query['selectedSubjects'] : '';
					if($excludeSubjects != '') {
						$query = "SELECT id, title FROM subjects WHERE (`title` like '% $term%' OR  `title` like  '%$term %'  OR  `title` like  '% $term %' OR `title` like '$term%' ) AND id NOT IN (".$excludeSubjects.") order by title asc";
					} else {
						$query = "SELECT id, title FROM subjects WHERE `title` like '$term%' OR  `title` like  '$term %'  OR  `title` like  '$term %' OR `title` like '$term%' order by title asc";
					}
					$subjects = $this->Subject->query($query);
					if (isset($subjects) && !empty($subjects)) {
						$subjects = Set::combine($subjects, '{n}.subjects.id', '{n}.subjects.title');
					}
					if (isset($subjects) && !empty($subjects)) {
						$response['success'] = true;
						$domain = [];
						foreach ($subjects as $key => $value) {
							$domain[] = ['id' => $key, 'text' => $value];
						}
						$response['content'] = $domain;
					}

				}
			}
			echo json_encode($response);
			exit;
		}

	}

	public function get_project_subjects() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$response = [
				'success' => false,
				'msg' => null,
				'content' => null,
			];
			$subjects = $project_subjects = [];
			$project_id = $this->request->data['project_id'];
			$subjects = $this->ProjectSubject->find('all', array('conditions' => array('ProjectSubject.project_id' => $project_id)));
			// pr($skills, 1);
			if (isset($subjects) && !empty($subjects)) {
				foreach ($subjects as $key => $value) {
					$project_subjects[] = ['label' => $value['Subject']['title'], 'key' => $value['ProjectSubject']['subject_id']];
				}
			}

			echo json_encode($project_subjects);
			exit;
		}

	}

	public function get_subjects_data() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$response = [
				'success' => false,
				'msg' => null,
				'content' => null,
			];
			$return_subjects = [];
			$subject_selected = $this->request->data['subjects'];
			// pr($skill_selected, 1);
			if (isset($subject_selected) && !empty($subject_selected)) {
				$subjects = $this->Subject->find('all', array('conditions' => array('Subject.id' => $subject_selected)));
				foreach ($subjects as $key => $value) {
					$return_subjects[] = ['label' => $value['Subject']['title'], 'key' => $value['Subject']['id']];
				}
			}

			echo json_encode($return_subjects);
			exit;
		}

	}


	function project_rag_notification($project_id = null, $rag_type = null) {

		$prj_rag = getFieldDetail('Project', $project_id, 'rag_status');
		$ragstatus = 'To ';

		$view = new View();
		$commonHelper = $view->loadHelper('Common');
		$ragCLS = $commonHelper->getRAG($project_id);
		if (isset($ragCLS['rag_color']) && $ragCLS['rag_color'] == 'bg-yellow') {
			$ragstatus = 'Amber';
		} else if (isset($ragCLS['rag_color']) && $ragCLS['rag_color'] == 'bg-red') {
			$ragstatus = 'Red';
		} else {
			$ragstatus = 'Green';
		}
		$heading = $ragstatus . ' (' . $rag_type . ')';

		/************** socket messages **************/
		if (SOCKET_MESSAGES) {
			$current_user_id = $this->Session->read('Auth.User.id');
			App::import('Controller', 'Risks');
			$Risks = new RisksController;
			$project_all_users = $Risks->get_project_users($project_id, $current_user_id);
			if (isset($project_all_users) && !empty($project_all_users)) {
				if (($key = array_search($current_user_id, $project_all_users)) !== false) {
					unset($project_all_users[$key]);
				}
			}
			$open_users = null;
			if (isset($project_all_users) && !empty($project_all_users)) {
				foreach ($project_all_users as $key1 => $value1) {
					if ($this->objView->loadHelper('ViewModel')->projectPermitType($project_id, $value1)) {
						if (web_notify_setting($value1, 'project', 'project_rag')) {
							$open_users[] = $value1;
						}
					}
				}
			}
			$userDetail = get_user_data($current_user_id);
			$content = [
				'notification' => [
					'type' => 'rag_update',
					'created_id' => $current_user_id,
					'project_id' => $project_id,
					'creator_name' => $userDetail['UserDetail']['full_name'],
					'subject' => 'RAG update',
					'heading' => 'Status: ' . $heading,
					'sub_heading' => 'Project: ' . getFieldDetail('Project', $project_id, 'title'),
					'date_time' => timezoneTimeConvertor($this->user_id, date('Y-m-d g:iA'), $format = 'd M Y g:iA') . user_country($this->user_id),
				],
			];
			if (is_array($open_users)) {
				$content['received_users'] = array_values($open_users);
			}

			$request = array(
				'header' => array(
					'Content-Type' => 'application/json',
				),
			);
			$content = json_encode($content);
			$HttpSocket = new HttpSocket([
				'ssl_verify_host' => false,
				'ssl_verify_peer_name' => false,
				'ssl_verify_peer' => false,
			]);

			$results = $HttpSocket->post(CHATURL . '/serveremit', $content, $request);
		}
		/************** socket messages **************/
	}

	public function project_activities($project_id = '') {
		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$html = '';
			if ($this->request->is('post') || $this->request->is('put')) {

				$view = new View($this, false);
				$view->viewPath = 'Projects/activity';
				$view->layout = false;
				$view->set('project_id', $project_id);

				$html = $view->render('task_activity');

			}

			echo json_encode($html);
			exit();
		}
	}

	/*
		     * @name  		reports
		     * @todo  		Get project full detail with reports
		     * @access		public
		     * @package  	App/Controller/ProjectsController
	*/

	public function reports($project_id = '') {

		$this->layout = 'inner';
		$this->set('title_for_layout', __('Project Report', true));
		$this->set('page_heading', __('Project Report', true));

		$category_id = $pwcount = 0;
		$project_workspaces1 = null;

		if (isset($project_id) && !empty($project_id)) {
			$ppr = $this->UserProject->find('first', array('conditions' => array('UserProject.project_id' => $project_id), 'recursive' => 1));

			$this->loadModel('ProjectPermission');

			$ppm = $this->ProjectPermission->find('first', array('conditions' => array('ProjectPermission.user_id' => $this->user_id, 'ProjectPermission.user_project_id' => $ppr['UserProject']['id']), 'recursive' => 1));

			$grp_id = $this->Group->GroupIDbyUserID($project_id, $this->user_id);

			if (isset($grp_id) && !empty($grp_id)) {

				$group_permission = $this->Group->group_permission_details($project_id, $grp_id);

				if (isset($group_permission['ProjectPermission']['project_level']) && $group_permission['ProjectPermission']['project_level'] == 1) {
					$project_level = $group_permission['ProjectPermission']['project_level'];
					if (isset($project_level) && $project_level == 1) {
						$this->set('project_level', 1);
					}
				}

				//
			}

			// if the current logged in user is not authorized to view this project
			if ((!$this->Project->UserProject->hasAny(['UserProject.user_id' => $this->user_id, 'UserProject.project_id' => $project_id])) && ((!isset($ppm)) && empty($ppm))) {
				$this->redirect(array('controller' => 'projects', 'action' => 'lists'));
			}
			$uusde = $this->Common->userproject($project_id, $this->user_id);

			if ((isset($uusde) && !empty($uusde)) || (isset($ppm) && (isset($ppm['ProjectPermission']) && $ppm['ProjectPermission']['project_level'] == 1)) || (isset($project_level) && $project_level == 1)) {

				$project_workspaces1 = $this->Project->ProjectWorkspace->find('all', array(
					'recursive' => 1,
					'conditions' => [
						'ProjectWorkspace.project_id' => $project_id,
						'Workspace.id IS NOT NULL',
						'Workspace.studio_status <>' => 1,
						//'Workspaces.id' => $wsids
					],
					'joins' => array(array(
						'table' => 'workspaces',
						'alias' => 'Workspaces',
						'type' => 'left',
						'conditions' => array(
							'Workspaces.id' => 'ProjectWorkspace.workspace_id',
						),
					)),
					'order' => ['ProjectWorkspace.sort_order ASC'],
				));

				$this->pagination['limit'] = 6;
				$this->pagination['summary_model'] = 'Workspaces';
				$this->pagination['options'] = array(
					'url' => array_merge(
						array(
							'controller' => $this->request->params['controller'],
							'action' => 'partial_reports',
						), $this->request->params['pass'], $this->request->params['named']
					),
				);
				$paginator = array(
					'recursive' => 1,
					'conditions' => [
						'ProjectWorkspace.project_id' => $project_id,
						'Workspace.id IS NOT NULL',
						'Workspace.studio_status <>' => 1,
					],
					'joins' => array(array(
						'table' => 'workspaces',
						'alias' => 'Workspaces',
						'type' => 'left',
						'conditions' => array(
							'Workspaces.id' => 'ProjectWorkspace.workspace_id',
						),
					)),
					'limit' => $this->pagination['limit'],
					"order" => ['ProjectWorkspace.sort_order' => 'ASC'],
				);
				$this->paginate = $paginator;
			} else if ((isset($ppm) && !empty($ppm)) || (isset($grp_id) && !empty($grp_id))) {

				$wsids = $this->Common->work_permission_details($project_id, $this->user_id);

				if ((isset($grp_id) && !empty($grp_id))) {
					$wsids = $this->Group->group_work_permission_details($project_id, $grp_id);
				}
				//pr($wsids,1);

				$project_workspaces1 = $this->Project->ProjectWorkspace->find('all', array(
					'recursive' => 1,
					'conditions' => [
						'ProjectWorkspace.project_id' => $project_id,
						'Workspace.id IS NOT NULL', 'ProjectWorkspace.id' => $wsids, 'Workspace.studio_status <>' => 1,
					],
					'joins' => array(array(
						'table' => 'workspaces',
						'alias' => 'Workspaces',
						'type' => 'left',
						'conditions' => array(
							'Workspaces.id' => 'ProjectWorkspace.workspace_id',
						),
					)),
					'order' => ['ProjectWorkspace.sort_order ASC'],
				));

				$this->pagination['limit'] = 6;
				$this->pagination['summary_model'] = 'Workspaces';
				$this->pagination['options'] = array(
					'url' => array_merge(
						array(
							'controller' => $this->request->params['controller'],
							'action' => 'partial_reports',
						), $this->request->params['pass'], $this->request->params['named']
					),
				);

				$paginator = array(
					'recursive' => 1,
					'conditions' => [
						'ProjectWorkspace.project_id' => $project_id,
						'Workspace.id IS NOT NULL',
						'Workspace.studio_status <>' => 1,
						'ProjectWorkspace.id' => $wsids,
					],
					'joins' => array(array(
						'table' => 'workspaces',
						'alias' => 'Workspaces',
						'type' => 'left',
						'conditions' => array(
							'Workspaces.id' => 'ProjectWorkspace.workspace_id',
						),
					)),
					'limit' => $this->pagination['limit'],
					"order" => ['ProjectWorkspace.sort_order' => 'ASC'],
				);
				$this->paginate = $paginator;
			}

			$this->set('project_workspaces', $this->paginate('ProjectWorkspace'));
			$this->set('project_workspaces_all', $project_workspaces1);

			$this->pagination['show_summary'] = true;
			$this->set('JeeraPaging', $this->pagination);

			$this->Project->unbindModel(['hasMany' => 'ProjectWorkspace']);
			$project_detail = null;
			$project_detail = $this->Project->UserProject->find('first', array(
				'recursive' => 2,
				'conditions' => [
					//'UserProject.user_id' => $this->user_id,
					'UserProject.project_id' => $project_id,
				],
			));

			$this->set(compact('project_detail'));
			// echo $this->ProjectWorkspace->_query();
			// $results = $this->Project->findById($project_id);

			$this->Project->unBindModel(array('hasMany' => array('ProjectWorkspace', 'UserProject')));
			// $this->Project->unbindModelAll();
			$results = $this->Project->find('first', [
				'conditions' => [
					'Project.id' => $project_id,
				],
				'fields' => [
					'Project.id',
					'Project.category_id',
					'Project.title',
					'Category.id AS cat_id',
					'Category.title',
					'Category.parent_id',
				],
			]);
			// pr($results, 1);
			$category_bread = null;

			$parent_titles = '';

			$cat_title = '';

			if (isset($results) && !empty($results)) {

				$category_id = $results['Category']['cat_id'];

				if (isset($results['Category']) && !empty($results['Category'])) {
					$category_bread = ['id' => $results['Category']['cat_id'], 'parent_id' => $results['Category']['parent_id'], 'keys' => true, 'reverse' => true];

					$cat_title = $results['Category']['title'];
				}
			}
			// pr($category_bread, 1);
			$this->set('category_bread', $category_bread);
		}
		$this->set('project_id', $project_id);

		if (isset($project_workspaces1) && !empty($project_workspaces1)) {
			$pwcount = count($project_workspaces1);
		}
		$this->set('pwcount', $pwcount);

		$crumb = [];

		$crumb = [
			//'Project' => '/projects/lists/',
			'Summary' => [
				'data' => [
					'url' => '/projects/index/' . $project_id,
					'class' => 'tipText',
					'title' => $project_detail['Project']['title'],
					'data-original-title' => htmlentities($project_detail['Project']['title']),
				],
			],
			'last' => ['Project Report']];

		/*if (!empty($category_id)) {
			$parent_titles = category_breadcrumb($category_bread, true);
			$cat_crumb['Category'] = [
				'data' => [
					'url' => '/categories/index/' . $category_id,
					'class' => 'tipText',
					'title' => $parent_titles,
					'data-original-title' => $cat_title,
				],
			];

			$crumb = array_merge($cat_crumb, $crumb);
			// e('in');
			// pr($cat_crumb );
			// pr($crumb );
		}*/
		// pr($crumb, 1);
		$this->set('crumb', $crumb);
	}

	/*
		     * @name  		getTipText
		     * @todo  		Get tooltip text of selected item
		     * @access		public
		     * @package  	App/Controller/ProjectsController
	*/

	public function getTipText($project_id = null) {

		if ($this->request->isAjax()) {
			$this->loadModel('TipText');
			$response = [
				'success' => false,
				'msg' => '',
				'content' => null,
			];

			$this->layout = 'ajax';
			$this->autoRender = false;

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				// if new words comes with request, save them all
				if (isset($post['newWords']) && !empty($post['newWords'])) {

					$newData = null;
					foreach ($post['newWords'] as $k => $v) {
						$place_holder = strtolower($v);
						$place_holder = Inflector::slug($place_holder, '-');
						$ex = explode('-', $v);
						if (!empty($ex)) {
							$values = implode(' ', array_map('ucfirst', explode('-', $v)));
						}

						$newData['TipText'][$k]['place_holder'] = $place_holder;
						$newData['TipText'][$k]['values'] = $values;
						$newData['TipText'][$k]['status'] = 1;
					}
					if (!empty($newData)) {
						$this->TipText->saveAll($newData['TipText']);
					}
				}

				$this->loadModel("TipText");
				$response['content'] = $this->TipText->find('list', [
					'conditions' => ['TipText.status' => 1],
					'fields' => ['TipText.place_holder', 'TipText.values'],
					'order' => ['TipText.id DESC'],
				]);
				// pr($response['content'], 1);
			}

			echo json_encode($response);
			exit();
		}
	}

	/*
		     * @name  		update_color
		     * @todo  		Update background color code of a project box anywhere
		     * @access		public
		     * @package  	App/Controller/ProjectsController
	*/

	public function update_color($project_id = null) {

		if ($this->request->isAjax()) {

			$response = [
				'success' => false,
				'msg' => '',
				'content' => null,
			];

			$this->layout = 'ajax';
			$this->autoRender = false;

			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;

				$this->Project->id = $project_id;
				if ($this->Project->saveField('color_code', $post['color_code'])) {

					$response['success'] = true;
					$response['msg'] = "Success";
					$response['content'] = [];
				} else {
					$response['msg'] = "Error!!!";
				}
			}

			echo json_encode($response);
			exit();
		}
	}

	/*
		     * @name  		workspace_layout_redesign
		     * @todo  		Base method for get/set/view elements of an area
		     * @access		public
		     * @package  	App/Controller/ProjectsController
	*/
	// old manage_elements function
	public function tasks($project_id = null, $workspace_id = null) {

		if (is_null($project_id) || is_null($workspace_id)) {
			$this->redirect(Controller::referer());
		}

		$this->loadModel("Template");

		$this->layout = 'inner';

		$this->set('title_for_layout', __('Manage Tasks', true));

		if ($this->request->is('ajax')) {

			$response = [
				'success' => false,
				'msg' => '',
				'content' => null,
			];

			$this->layout = 'ajax';
			$this->autoRender = false;

			if ($this->request->is('post') || $this->request->is('put')) {
				$data = $this->request->data;
				$data['Project']['user_id'] = $this->user_id;

				$this->Project->set($data);
				if ($this->Project->validates()) {
					if ($this->Project->save($this->request->data)) {
						$insertId = $this->Project->getLastInsertId();
						// $detail = $this->Project->findById($insertId);
						$response['success'] = true;
						$response['msg'] = "Success";
						$response['content'] = array('id' => $insertId);
					} else {
						$response['msg'] = "Error!!!";
					}
				} else {
					$response['content'] = $this->validateErrors($this->Project);
				}
			}

			echo json_encode($response);
			exit();
		}

		/******************************************************/
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

		$this->Workspace->recursive = -1;
		$workspace = $this->Workspace->find('first', [
			'conditions' => [
				'Workspace.id' => $workspace_id,
			],
		]);

		// pr($workspace, 1);

		$this->loadModel('TemplateDetail');
		$template_groups = $this->TemplateDetail->find('all', array(
			'fields' => 'DISTINCT row_no, id',
			'recursive' => 1,
			'conditions' => array(
				'TemplateDetail.template_id' => $workspace['Workspace']['template_id'],
			),
		));

		$grouped_ids = Set::extract($template_groups, '/TemplateDetail/id');

		$this->Area->unbindModel(
			array('hasMany' => array(
				'Elements',
			),
			)
		);

		/* -----------Group code----------- */
		$projectsg = $this->UserProject->find('first', ['recursive' => -1, 'conditions' => ['UserProject.project_id' => $project_id], 'fields' => ['UserProject.id']]);
		$pgupid = $projectsg['UserProject']['id'];
		$conditionsG = null;
		$conditionsG['ProjectGroupUser.user_id'] = $this->user_id;
		$conditionsG['ProjectGroupUser.user_project_id'] = $pgupid;
		$conditionsG['ProjectGroupUser.approved'] = 1;
		$projects_group_shared_user = $this->ProjectGroupUser->find('first', array(
			'conditions' => $conditionsG,
			'fields' => array('ProjectGroupUser.project_group_id'),
			'recursive' => -1,
		));
		if (isset($projects_group_shared_user) && !empty($projects_group_shared_user)) {
			//echo $project_id." sdsd ".$projects_group_shared_user['ProjectGroupUser']['project_group_id']; die;
			$group_permission = $this->Group->group_permission_details($project_id, $projects_group_shared_user['ProjectGroupUser']['project_group_id']);

			$pll_level = $group_permission['ProjectPermission']['project_level'];

			$this->set('gpid', $projects_group_shared_user['ProjectGroupUser']['project_group_id']);

			if (isset($pll_level) && $pll_level == 1) {
				$this->set('project_level', 1);
			}
		}
		/* -----------Group code----------- */

		$area_template_data = $this->Workspace->Area->find('all', [
			'fields' => [
				'Area.id as area_id', 'Area.workspace_id', 'Area.title', 'Area.description as desc', 'Area.tooltip_text', 'Area.status',
				'TemplateDetail.id as temp_detail_id', 'TemplateDetail.row_no', 'TemplateDetail.col_no', 'TemplateDetail.size_w', 'TemplateDetail.size_h', 'TemplateDetail.elements_counter', 'TemplateDetail.template_id',
			],
			'conditions' => [
				'Area.workspace_id' => $workspace_id,
				'Area.template_detail_id' => $grouped_ids,
			],
			'recursive' => 1,
			'order' => ['TemplateDetail.id ASC', 'TemplateDetail.row_no ASC'],
		]);

		//pr($area_template_data,1);

		$templateRows = $andConditions = null;

		//pr($this->data);
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

		if (isset($this->data['Element']['start_date'])) {
			$start = trim($this->data['Element']['start_date']);
		} else {
			$start = $this->Session->read('element.start_date');
		}

		if (isset($this->data['Element']['end_date'])) {
			$end = trim($this->data['Element']['end_date']);
		} else {
			$end = $this->Session->read('element.end_date');
		}

		$andConditions = array();

		if (isset($keyword)) {
			$this->Session->write('user.keyword', $keyword);
			$keywords = explode(" ", $keyword);

			if (!empty($keyword)) {
				$in = 1;
				$andConditions = array('OR' => array(
					'Element.description LIKE' => '%' . $keyword . '%',
					'Element.title LIKE' => '%' . $keyword . '%',
				));
			}
		}

		if ((isset($start) && isset($end)) && (!empty($start) && !empty($end))) {

			if (empty($andConditions)) {
				$andConditions = array(
					'Element.start_date >=' => date('Y-m-d H:i:s', strtotime($start . " 00:00:00")),
					'Element.end_date <=' => date('Y-m-d H:i:s', strtotime($end . " 23:59:59")),
					//'UserDetail.country_id LIKE' => '%' . $county . '%'
				);
			} else {

				$andConditions1 = array(
					'Element.start_date >=' => date('Y-m-d H:i:s', strtotime($start . " 00:00:00")),
					'Element.end_date <=' => date('Y-m-d H:i:s', strtotime($end . " 23:59:59")),
					//'UserDetail.country_id LIKE' => '%' . $county . '%'
				);
				$andConditions = array_merge($andConditions1, $andConditions);
			}
		}

		$finalConditions = array();

		if (isset($this->data['User']['status'])) {
			$status = $this->data['User']['status'];
		} else {
			$status = $this->Session->read('user.status');
		}

		if (isset($status)) {
			$this->Session->write('User.status', $status);
			if ($status != '') {

				if ($status == 0) {

					$andConditions = array_merge($andConditions, array('Element.date_constraints' => 0));
				} else if ($status == 1) {

					$andConditions = array_merge($andConditions, array(
						'Element.start_date <=' => date('Y-m-d H:i:s', strtotime(date('Y-m-d') . " 00:00:00")),
						'Element.end_date >=' => date('Y-m-d H:i:s', strtotime(date('Y-m-d') . " 23:59:59")),
						'Element.date_constraints >' => 0,
					));
				} else if ($status == 2) {

					$andConditions = array_merge($andConditions, array(
						'Element.start_date >' => date('Y-m-d H:i:s', strtotime(date('Y-m-d') . " 00:00:00")),
						'Element.date_constraints >' => 0,
					));
				} else if ($status == 3) {

					$andConditions = array_merge($andConditions, array(
						'Element.sign_off >' => 0,
					));
				} else if ($status == 4) {

					$andConditions = array_merge($andConditions, array(
						'Element.end_date <' => date('Y-m-d H:i:s', strtotime(date('Y-m-d') . " 23:59:59")),
						'Element.date_constraints >' => 0,
						'Element.sign_off ' => 0,
					));
				}

				$in = 1;
			}
		}

		if (isset($this->data['Element']['per_page_show']) && !empty($this->data['Element']['per_page_show'])) {
			$per_page_show = $this->data['Element']['per_page_show'];
		}

		if (!empty($andConditions) && !empty($finalConditions)) {
			$finalConditions = array_merge($finalConditions, array('AND' => $andConditions));
			$in = 1;
		} else if (!empty($andConditions)) {
			$finalConditions = $andConditions;
			$in = 1;
		}
		foreach ($area_template_data as $row_id => $row_templates) {
			$area_detail = $row_templates['Area'];
			$temp_detail = $row_templates['TemplateDetail'];

			$row_templates['Elements'] = $this->Element->find('all', array(
				'conditions' => array(
					'Element.area_id' => $row_templates['Area']['area_id'],
					$finalConditions,
				),
				'recursive' => -1,
				'order' => ['Element.sort_order ASC'],
			)
			);

			$elements = $row_templates['Elements'];

			if ($temp_detail['size_w'] > 0 && $temp_detail['size_h'] > 0) {
				$row_no = $temp_detail['row_no'];
				$area_templates = array_merge($temp_detail, $area_detail);
				if (isset($elements) && !empty($elements)) {
					$area_templates['elements'] = $elements;
				}

				//pr($area_templates['elements'],1);
				// pr($area_templates);
				$templateRows[$row_no][] = $area_templates;
			}
		}

		$data['templateRows'] = $templateRows;
		// pr($templateRows,1);

		$this->setJsVar('workspace_id', $workspace_id);
		$this->setJsVar('project_id', $project_id);

		$this->setJsVar('template_id', $workspace['Workspace']['template_id']);
		$this->set('template_id', $workspace['Workspace']['template_id']);

		$this->set('project_id', $project_id);
		$this->set('workspace_id', $workspace_id);

		$data['workspace'] = $workspace;
		$data['page_heading'] = 'Tasks';
		$data['page_subheading'] = 'View Tasks in this Workspace';

		$this->set('data', $data);
		$this->set('in', $in);

		// Get project detail
		$projects = $cat_crumb = null;
		if (isset($project_id) && !empty($project_id)) {
			$projects = $this->Project->find('first', ['conditions' => ['Project.id' => $project_id], 'recursive' => -1]);

			// Get category detail of parent Project
			// if category detail found, merge it with other breadcrumb data
			// $cat_crumb = get_category_list($project_id);
		}
		$project_title = $projects['Project']['title'];

		$crumb = [
			'Summary' => [
				'data' => [
					'url' => '/projects/index/' . $project_id,
					'class' => 'tipText',
					'title' => $project_title,
					'data-original-title' => $project_title,
				],
			],
			'last' => [
				'data' => [
					'title' => $workspace['Workspace']['title'],
					'data-original-title' => $workspace['Workspace']['title'],
				],
			],
		];

		/*if (isset($cat_crumb) && !empty($cat_crumb) && is_array($cat_crumb)) {

			$crumb = array_merge($cat_crumb, $crumb);
		}*/
		$this->set('crumb', $crumb);

		$areas = $this->Area->find('all', ['conditions' => [
			'Area.workspace_id' => $workspace_id,
		],
			'fields' => ['Area.id'],
			'recursive' => -1,
		]);
		if ($this->get_project_elements_risks_count($project_id, $workspace_id) > 0) {
			$riskElementCnt = $this->get_project_elements_risks_count($project_id, $workspace_id);
		} else {
			$riskElementCnt = 0;
		}
		$this->set('riskelementcount', $riskElementCnt);
		$this->set('areas', $areas);
	}

	/*
		     * @name  		manage_elements
		     * @todo  		Base method for get/set/view elements of an area
		     * @access		public
		     * @package  	App/Controller/ProjectsController
	*/

	public function manage_elements_old($project_id = null, $workspace_id = null) {

		if (is_null($project_id) || is_null($workspace_id)) {
			$this->redirect(Controller::referer());
		}

		$this->loadModel("Template");

		$this->layout = 'inner';

		$this->set('title_for_layout', __('Manage Elements', true));

		if ($this->request->is('ajax')) {

			$response = [
				'success' => false,
				'msg' => '',
				'content' => null,
			];

			$this->layout = 'ajax';
			$this->autoRender = false;

			if ($this->request->is('post') || $this->request->is('put')) {
				$data = $this->request->data;
				$data['Project']['user_id'] = $this->user_id;

				$this->Project->set($data);
				if ($this->Project->validates()) {
					if ($this->Project->save($this->request->data)) {
						$insertId = $this->Project->getLastInsertId();
						// $detail = $this->Project->findById($insertId);
						$response['success'] = true;
						$response['msg'] = "Success";
						$response['content'] = array('id' => $insertId);
					} else {
						$response['msg'] = "Error!!!";
					}
				} else {
					$response['content'] = $this->validateErrors($this->Project);
				}
			}

			echo json_encode($response);
			exit();
		}

		$this->Workspace->recursive = 2;
		$workspace = $this->Workspace->find('first', [
			'conditions' => [
				'Workspace.id' => $workspace_id,
			],
		]);

		$this->loadModel('TemplateDetail');
		$template_groups = $this->TemplateDetail->find('all', array(
			'fields' => 'DISTINCT row_no, id',
			'conditions' => array(
				'TemplateDetail.template_id' => $workspace['Workspace']['template_id'],
			),
		));

		$grouped_ids = Set::extract($template_groups, '/TemplateDetail/id');

		$this->Area->unbindModel(
			array('hasMany' => array(
				'Elements',
			),
			)
		);

		/* -----------Group code----------- */
		$projectsg = $this->UserProject->find('first', ['recursive' => 2, 'conditions' => ['UserProject.project_id' => $project_id]]);
		$pgupid = $projectsg['UserProject']['id'];
		$conditionsG = null;
		$conditionsG['ProjectGroupUser.user_id'] = $this->user_id;
		$conditionsG['ProjectGroupUser.user_project_id'] = $pgupid;
		$conditionsG['ProjectGroupUser.approved'] = 1;
		$projects_group_shared_user = $this->ProjectGroupUser->find('first', array(
			'conditions' => $conditionsG,
			'fields' => array('ProjectGroupUser.project_group_id'),
			'recursive' => -1,
		));
		if (isset($projects_group_shared_user) && !empty($projects_group_shared_user)) {
			//echo $project_id." sdsd ".$projects_group_shared_user['ProjectGroupUser']['project_group_id']; die;
			$group_permission = $this->Group->group_permission_details($project_id, $projects_group_shared_user['ProjectGroupUser']['project_group_id']);

			$pll_level = $group_permission['ProjectPermission']['project_level'];

			$this->set('gpid', $projects_group_shared_user['ProjectGroupUser']['project_group_id']);

			if (isset($pll_level) && $pll_level == 1) {
				$this->set('project_level', 1);
			}
		}
		/* -----------Group code----------- */

		$area_template_data = $this->Workspace->Area->find('all', [
			'fields' => [
				'Area.id as area_id', 'Area.workspace_id', 'Area.title', 'Area.description as desc', 'Area.tooltip_text', 'Area.status',
				'TemplateDetail.id as temp_detail_id', 'TemplateDetail.row_no', 'TemplateDetail.col_no', 'TemplateDetail.size_w', 'TemplateDetail.size_h', 'TemplateDetail.elements_counter', 'TemplateDetail.template_id',
			],
			'conditions' => [
				'Area.workspace_id' => $workspace_id,
				'Area.template_detail_id' => $grouped_ids,
			],
			'recursive' => 2,
			'order' => ['TemplateDetail.id ASC', 'TemplateDetail.row_no ASC'],
		]);

		$templateRows = $andConditions = null;

		//pr($this->data);
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

		if (isset($this->data['Element']['start_date'])) {
			$start = trim($this->data['Element']['start_date']);
		} else {
			$start = $this->Session->read('element.start_date');
		}

		if (isset($this->data['Element']['end_date'])) {
			$end = trim($this->data['Element']['end_date']);
		} else {
			$end = $this->Session->read('element.end_date');
		}

		$andConditions = array();

		if (isset($keyword)) {
			$this->Session->write('user.keyword', $keyword);
			$keywords = explode(" ", $keyword);

			if (!empty($keyword)) {
				$in = 1;
				$andConditions = array('OR' => array(
					'Element.description LIKE' => '%' . $keyword . '%',
					'Element.title LIKE' => '%' . $keyword . '%',
				));
			}
		}

		if ((isset($start) && isset($end)) && (!empty($start) && !empty($end))) {

			if (empty($andConditions)) {
				$andConditions = array(
					'Element.start_date >=' => date('Y-m-d H:i:s', strtotime($start . " 00:00:00")),
					'Element.end_date <=' => date('Y-m-d H:i:s', strtotime($end . " 23:59:59")),
					//'UserDetail.country_id LIKE' => '%' . $county . '%'
				);
			} else {

				$andConditions1 = array(
					'Element.start_date >=' => date('Y-m-d H:i:s', strtotime($start . " 00:00:00")),
					'Element.end_date <=' => date('Y-m-d H:i:s', strtotime($end . " 23:59:59")),
					//'UserDetail.country_id LIKE' => '%' . $county . '%'
				);
				$andConditions = array_merge($andConditions1, $andConditions);
			}
		}

		$finalConditions = array();

		if (isset($this->data['User']['status'])) {
			$status = $this->data['User']['status'];
		} else {
			$status = $this->Session->read('user.status');
		}

		if (isset($status)) {
			$this->Session->write('User.status', $status);
			if ($status != '') {

				if ($status == 0) {

					$andConditions = array_merge($andConditions, array('Element.date_constraints' => 0));
				} else if ($status == 1) {

					$andConditions = array_merge($andConditions, array(
						'Element.start_date <=' => date('Y-m-d H:i:s', strtotime(date('Y-m-d') . " 00:00:00")),
						'Element.end_date >=' => date('Y-m-d H:i:s', strtotime(date('Y-m-d') . " 23:59:59")),
						'Element.date_constraints >' => 0,
					));
				} else if ($status == 2) {

					$andConditions = array_merge($andConditions, array(
						'Element.start_date >' => date('Y-m-d H:i:s', strtotime(date('Y-m-d') . " 00:00:00")),
						'Element.date_constraints >' => 0,
					));
				} else if ($status == 3) {

					$andConditions = array_merge($andConditions, array(
						'Element.sign_off >' => 0,
					));
				} else if ($status == 4) {

					$andConditions = array_merge($andConditions, array(
						'Element.end_date <' => date('Y-m-d H:i:s', strtotime(date('Y-m-d') . " 23:59:59")),
						'Element.date_constraints >' => 0,
						'Element.sign_off ' => 0,
					));
				}

				$in = 1;
			}
		}

		if (isset($this->data['Element']['per_page_show']) && !empty($this->data['Element']['per_page_show'])) {
			$per_page_show = $this->data['Element']['per_page_show'];
		}

		if (!empty($andConditions) && !empty($finalConditions)) {
			$finalConditions = array_merge($finalConditions, array('AND' => $andConditions));
			$in = 1;
		} else if (!empty($andConditions)) {
			$finalConditions = $andConditions;
			$in = 1;
		}
		foreach ($area_template_data as $row_id => $row_templates) {
			$area_detail = $row_templates['Area'];
			$temp_detail = $row_templates['TemplateDetail'];

			$row_templates['Elements'] = $this->Element->find('all', array(
				'conditions' => array(
					'Element.area_id' => $row_templates['Area']['area_id'],
					$finalConditions,
				),
				'order' => ['Element.sort_order ASC'],
			)
			);

			$elements = $row_templates['Elements'];

			if ($temp_detail['size_w'] > 0 && $temp_detail['size_h'] > 0) {
				$row_no = $temp_detail['row_no'];
				$area_templates = array_merge($temp_detail, $area_detail);
				if (isset($elements) && !empty($elements)) {
					$area_templates['elements'] = $elements;
				}

				//pr($area_templates['elements'],1);
				// pr($area_templates);
				$templateRows[$row_no][] = $area_templates;
			}
		}

		$data['templateRows'] = $templateRows;
		// pr($templateRows,1);

		$this->setJsVar('workspace_id', $workspace_id);
		$this->setJsVar('project_id', $project_id);

		$this->setJsVar('template_id', $workspace['Workspace']['template_id']);
		$this->set('template_id', $workspace['Workspace']['template_id']);

		$this->set('project_id', $project_id);
		$this->set('workspace_id', $workspace_id);

		$data['workspace'] = $workspace;
		$data['page_heading'] = 'Tasks';
		$data['page_subheading'] = 'View Tasks in this Workspace';

		$this->set('data', $data);
		$this->set('in', $in);

		// Get project detail
		$projects = $cat_crumb = null;
		if (isset($project_id) && !empty($project_id)) {
			$projects = $this->Project->find('first', ['conditions' => ['Project.id' => $project_id], 'recursive' => -1]);

			// Get category detail of parent Project
			// if category detail found, merge it with other breadcrumb data
			$cat_crumb = get_category_list($project_id);
		}
		$project_title = $projects['Project']['title'];

		$crumb = [
			'Summary' => [
				'data' => [
					'url' => '/projects/index/' . $project_id,
					'class' => 'tipText',
					'title' => $project_title,
					'data-original-title' => $project_title,
				],
			],
			'last' => [
				'data' => [
					'title' => $workspace['Workspace']['title'],
					'data-original-title' => $workspace['Workspace']['title'],
				],
			],
		];

		if (isset($cat_crumb) && !empty($cat_crumb) && is_array($cat_crumb)) {

			$crumb = array_merge($cat_crumb, $crumb);
		}
		$this->set('crumb', $crumb);

		$areas = $this->Area->find('all', ['conditions' => [
			'Area.workspace_id' => $workspace_id,
		],
			'fields' => ['Area.id'],
			'recursive' => -1,
		]);

		$this->set('areas', $areas);
	}

	/*
		     * @name  		configureWorkspaces
		     * @todo  		Configuration settings to show/hide workspaces of a project
		     * @access		public
		     * @package  	App/Controller/ProjectsController
	*/

	public function configureWorkspaces() {

		if ($this->request->is('post') || $this->request->is('put')) {

			// pr($this->request->data, 1);
			$project_id = $this->request->data['Project']['id'];

			//if ($this->ProjectWorkspace->updateAll(array('ProjectWorkspace.sort_order' => 0, 'ProjectWorkspace.leftbar_status' => 0), array('ProjectWorkspace.project_id' => $project_id))) {

			$post = null;
			foreach ($this->request->data['ProjectWorkspace'] as $sort_order => $value) {

				$leftbar_status = (isset($value['workspace_id']) && !empty($value['workspace_id'])) ? 1 : 0;

				$post[] = array(
					'ProjectWorkspace' => array(
						//'sort_order' => $sort_order,
						'id' => $value["id"],
						'leftbar_status' => $leftbar_status,
						'modified' => date('Y-m-d h:i:s'),
					),
				);
			}
			$this->ProjectWorkspace->saveMany($post);
			//}
			die('success');
		}
	}

	/*
		     * @name  		configureWorkspaces
		     * @todo  		Configuration settings to show/hide workspaces of a project
		     * @access		public
		     * @package  	App/Controller/ProjectsController
	*/

	public function project_description($project_id = null) {
		$this->layout = 'ajax';
		if ($this->request->is('get')) {

			$data = $this->Project->find('first', ['conditions' => ['Project.id' => $project_id], 'recursive' => -1]);
			// pr($data, 1);
			$this->set('data', $data);

			// $this->render( '/Project/partials/project_description' );
		}
	}

	/*
		     * @name  		trashWorkspace
		     * @todo  		Remove a workspace
		     * @access		public
		     * @package  	App/Controller/ProjectsController
	*/

	public function trashWorkspace($project_id = 0, $workspace_id = 0) {

		$this->autoRender = false;

		$response = ['success' => false, 'content' => null];
		$this->layout = 'ajax';

		if ($this->request->is('ajax')) {
			if ($this->request->is('post') || $this->request->is('put')) {

				$action = $this->request->data['action'];

				if ($action == 'delete' && !empty($project_id) && !empty($workspace_id)) {
					$response['success'] = true;
					// $this->ProjectWorkspace->Behaviors->load('Containable');
					$id = $this->ProjectWorkspace->find('first', ['conditions' => ['ProjectWorkspace.project_id' => $project_id, 'ProjectWorkspace.workspace_id' => $workspace_id], 'fields' => ['ProjectWorkspace.id']]);

/* 					$workspace_elements = workspace_elements($workspace_id);
					if (isset($workspace_elements) && !empty($workspace_elements)) {
						$workspace_elements = Set::extract($workspace_elements, '/Element/id');
					}

					$elementArea = get_workspace_areasid($workspace_id); */

					//pr($workspace_elements, 1);

					/* = Get workspace owner ================================================================== */

					$participants = $participants_owners = $participantsGpOwner = $participantsGpSharer = [];

					$projectwsp_id = isset($id['ProjectWorkspace']['id']) ? $id['ProjectWorkspace']['id'] : 0;

					$owner = $this->Common->ProjectOwner($project_id, $this->Session->read('Auth.User.id'));

					$owner_id = isset($owner) ? $owner['UserProject']['user_id'] : 0;

					$participants = wsp_participants($project_id, $projectwsp_id, $owner['UserProject']['user_id']);

					$participants_owners = array_filter(participants_owners($project_id, $owner['UserProject']['user_id']));

					$i = 0;
					foreach ($participants_owners as $nom) {

						if ($owner_id != $nom && $nom != '') {
							$i++;
						}
					}

					/* ================ Email notification involved users ============================= */
					$data = array();
					$data1 = array();
					$data2 = array();
					$data3 = array();
					$data4 = array();
					$data5 = array();

					$view = new View();
					$commonHelper = $view->loadHelper('Common');

					$projectwsp_id = workspace_pwid($project_id, $workspace_id);

					$data = $commonHelper->ProjectOwner($project_id, $this->Session->read('Auth.User.id'));

					$data1[] = $data['UserProject']['user_id'];
					$data2 = wsp_participants($project_id, $projectwsp_id, $data['UserProject']['user_id']);
					$data3 = participants_owners($project_id, $data['UserProject']['user_id'], 1);
					$data4 = participants_group_owner($project_id);
					$data5 = wsp_grps_sharer($project_id, $projectwsp_id);

					$data1 = (isset($data1) && !empty($data1)) ? $data1 : array();
					$data2 = (isset($data2) && !empty($data2)) ? $data2 : array();
					$data3 = (isset($data3) && !empty($data3)) ? $data3 : array();
					$data4 = (isset($data4) && !empty($data4)) ? $data4 : array();
					$data5 = (isset($data5) && !empty($data5)) ? $data5 : array();

					$all_owner = array();

					$data1 = array_filter($data1);
					$data2 = array_filter($data2);
					$data3 = array_filter($data3);
					$data4 = array_filter($data4);
					$data5 = array_filter($data5);

					$all_owner = array_merge($data1, $data2, $data3, $data4, $data5);
					$all_owner = array_unique($all_owner);

					//pr($all_owner); die;

					/* ================ Email notification involved users ============================= */

					$workspaceDetails = $this->Workspace->findById($workspace_id);
					$projectDetails = $this->Project->findById($project_id);

					/* = ====================================================================================== */

					// $this->Common->update_project_activity($project_id);
					$user_id = $this->Auth->user('id');


					$this->loadModel('UserPermission');
					$task_ids = $this->UserPermission->find('list', array('fields' => array('UserPermission.element_id','UserPermission.element_id'), 'conditions' => array('UserPermission.workspace_id' => $workspace_id,'UserPermission.role' => 'Creator')));

					$area_ids = $this->UserPermission->find('list', array('fields' => array('UserPermission.area_id','UserPermission.area_id'), 'conditions' => array('UserPermission.workspace_id' => $workspace_id,'UserPermission.role' => 'Creator')));

					$task_ids = array_filter($task_ids);
					$area_ids = array_filter($area_ids);

/* 					pr($task_ids);
					pr($area_ids);
					die; */
					$this->ProjectWorkspace->delete(['ProjectWorkspace.id' => $projectwsp_id]);

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
					$this->Reminder->deleteAll(['Reminder.element_id' => $task_ids]);
					$this->CurrentTask->deleteAll(['CurrentTask.task_id' => $task_ids]);


					}

					if(isset($area_ids ) && !empty($area_ids )){
					$this->Area->deleteAll(array('Area.id' => $area_ids));
					}



						$this->Common->projectModified($project_id, $user_id);
						$response['success'] = true;

						// Delete all reminders associated with the elements of this workspace..
						/* if (isset($workspace_elements) && !empty($workspace_elements)) {
							$this->loadModel('Reminder');
							$this->loadModel('Area');
							$this->loadModel('ElementPermission');
							$this->loadModel('Element');

							$this->Reminder->deleteAll(['Reminder.element_id' => $workspace_elements]);

							//============================================================================
							//Delete all workspace element area
							if (isset($elementArea) && !empty($elementArea)) {
								foreach ($elementArea as $area_id) {
									$this->Area->delete($area_id);
								}
							}
							//Delete all element permissin
							$this->ElementPermission->delete(['ElementPermission.workspace_id' => $workspace_id]);
							foreach ($workspace_elements as $elment_id) {

								$eledeleted_data = $this->Element->findById($elment_id);

								$this->Element->delete($elment_id);
								// ================ save element delete data into DELETE DATA table =========

								// ===========================================================================

							}
							//============================================================================

						} */

						// ============== Start Workspace Delete Email ============================
						$workspacename = '';
						if (isset($workspaceDetails['Workspace']['title']) && !empty($workspaceDetails['Workspace']['title'])) {
							$workspacename = $workspaceDetails['Workspace']['title'];
							$projectName = $projectDetails['Project']['title'];
							$this->workspaceDeleteEmail($workspacename, $project_id, $all_owner, $projectName);

							/************** socket messages **************/
							if (SOCKET_MESSAGES) {
								$current_user_id = $this->Auth->user('id');
								$wsp_users = $all_owner;
								if (isset($wsp_users) && !empty($wsp_users)) {
									if (($key = array_search($current_user_id, $wsp_users)) !== false) {
										unset($wsp_users[$key]);
									}
								}
								$s_open_users = null;
								if (isset($wsp_users) && !empty($wsp_users)) {
									foreach ($wsp_users as $key => $value) {
										if (web_notify_setting($value, 'workspace', 'workspace_deleted')) {
											$s_open_users[] = $value;
										}
									}
								}
								$userDetail = get_user_data($current_user_id);
								$content = [
									'notification' => [
										'type' => 'workspace_deleted',
										'created_id' => $current_user_id,
										'project_id' => $project_id,
										'creator_name' => $userDetail['UserDetail']['full_name'],
										'subject' => 'Workspace deleted',
										'heading' => 'Workspace: ' . $workspacename,
										'sub_heading' => 'Project: ' . getFieldDetail('Project', $project_id, 'title'),
										'date_time' => timezoneTimeConvertor($this->user_id, date('Y-m-d g:iA'), $format = 'd M Y g:iA') . user_country($this->user_id),
									],
								];
								if (is_array($s_open_users)) {
									$content['received_users'] = array_values($s_open_users);
								}
								$response['content']['socket'] = $content;
							}
							/************** socket messages **************/
						}
						// ================ End Workspace Delete Email ============================

					}
				}
			}
		}

		echo json_encode($response);
		exit();
	}

	/*
		     * @name  		trashWorkspace
		     * @todo  		Remove a workspace
		     * @access		public
		     * @package  	App/Controller/ProjectsController
	*/

	public function delete_multiple_workspaces() {

		$this->autoRender = false;

		$response = ['success' => false];
		$this->layout = 'ajax';

		if ($this->request->is('ajax')) {

			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;

				$project_id = $post['pid'];
				$workspace_ids = $post['wid'];

				if ((isset($project_id) && !empty($project_id)) && (isset($workspace_ids) && !empty($workspace_ids))) {

					$response['success'] = true;

					foreach ($workspace_ids as $wskey => $workspace_id) {

						$id = $this->ProjectWorkspace->find('first', ['conditions' => ['ProjectWorkspace.project_id' => $project_id, 'ProjectWorkspace.workspace_id' => $workspace_id], 'fields' => ['ProjectWorkspace.id']]);

						$workspace_elements = workspace_elements($workspace_id);
						if (isset($workspace_elements) && !empty($workspace_elements)) {
							$workspace_elements = Set::extract($workspace_elements, '/Element/id');
						}

						$elementArea = get_workspace_areasid($workspace_id);

						//pr($workspace_elements, 1);

						/* = Get workspace owner ================================================================== */

						$participants = $participants_owners = $participantsGpOwner = $participantsGpSharer = [];

						$projectwsp_id = isset($id['ProjectWorkspace']['id']) ? $id['ProjectWorkspace']['id'] : 0;

						$owner = $this->Common->ProjectOwner($project_id, $this->Session->read('Auth.User.id'));

						$owner_id = isset($owner) ? $owner['UserProject']['user_id'] : 0;

						$participants = wsp_participants($project_id, $projectwsp_id, $owner['UserProject']['user_id']);

						$participants_owners = array_filter(participants_owners($project_id, $owner['UserProject']['user_id']));

						$i = 0;
						foreach ($participants_owners as $nom) {

							if ($owner_id != $nom && $nom != '') {
								$i++;
							}
						}

						/* ================ Email notification involved users ============================= */
						$data = array();
						$data1 = array();
						$data2 = array();
						$data3 = array();
						$data4 = array();
						$data5 = array();

						$view = new View();
						$commonHelper = $view->loadHelper('Common');

						$projectwsp_id = workspace_pwid($project_id, $workspace_id);

						$data = $commonHelper->ProjectOwner($project_id, $this->Session->read('Auth.User.id'));

						$data1[] = $data['UserProject']['user_id'];
						$data2 = wsp_participants($project_id, $projectwsp_id, $data['UserProject']['user_id']);
						$data3 = participants_owners($project_id, $data['UserProject']['user_id'], 1);
						$data4 = participants_group_owner($project_id);
						$data5 = wsp_grps_sharer($project_id, $projectwsp_id);

						$data1 = (isset($data1) && !empty($data1)) ? $data1 : array();
						$data2 = (isset($data2) && !empty($data2)) ? $data2 : array();
						$data3 = (isset($data3) && !empty($data3)) ? $data3 : array();
						$data4 = (isset($data4) && !empty($data4)) ? $data4 : array();
						$data5 = (isset($data5) && !empty($data5)) ? $data5 : array();

						$all_owner = array();

						$data1 = array_filter($data1);
						$data2 = array_filter($data2);
						$data3 = array_filter($data3);
						$data4 = array_filter($data4);
						$data5 = array_filter($data5);

						$all_owner = array_merge($data1, $data2, $data3, $data4, $data5);
						$all_owner = array_unique($all_owner);

						//pr($all_owner); die;

						/* ================ Email notification involved users ============================= */

						$workspaceDetails = $this->Workspace->findById($workspace_id);
						$projectDetails = $this->Project->findById($project_id);

						/* = ====================================================================================== */

						$this->ProjectWorkspace->delete(['ProjectWorkspace.id' => $id['ProjectWorkspace']['id']]);
						$this->Common->update_project_activity($project_id);
						$user_id = $this->Auth->user('id');
						if ($this->Workspace->delete(['Workspace.id' => $workspace_id], false)) {
							$this->Common->projectModified($project_id, $user_id);
							$response['success'] = true;

							// Delete all reminders associated with the elements of this workspace..
							if (isset($workspace_elements) && !empty($workspace_elements)) {
								$this->loadModel('Reminder');
								$this->loadModel('Area');
								$this->loadModel('ElementPermission');
								$this->loadModel('Element');

								$this->Reminder->deleteAll(['Reminder.element_id' => $workspace_elements]);

								//============================================================================
								//Delete all workspace element area
								if (isset($elementArea) && !empty($elementArea)) {
									foreach ($elementArea as $area_id) {
										$this->Area->delete($area_id);
									}
								}
								//Delete all element permissin
								$this->ElementPermission->delete(['ElementPermission.workspace_id' => $workspace_id]);
								foreach ($workspace_elements as $elment_id) {
									$this->Element->delete($elment_id);
								}
								//============================================================================

							}

							// ============== Start Workspace Delete Email ============================
							$workspacename = '';
							if (isset($workspaceDetails['Workspace']['title']) && !empty($workspaceDetails['Workspace']['title'])) {
								$workspacename = $workspaceDetails['Workspace']['title'];
								$projectName = $projectDetails['Project']['title'];
								$this->workspaceDeleteEmail($workspacename, $project_id, $all_owner, $projectName);
							}
							// ================ End Workspace Delete Email ============================

						}
					}
				}
			}
		}

		echo json_encode($response);
		exit();
	}

	/*
		     * @name  		sortOrderWorkspaces
		     * @todo  		Set sort order of workspaces of a project
		     * @access		public
		     * @package  	App/Controller/ProjectsController
	*/

	public function sortOrderWorkspaces() {
		if ($this->request->is('post') || $this->request->is('put')) {
			$this->loadModel('ProjectWorkspace');

			// Sort data by calling AJAX in \\192.168.4.32\htdocs\ideascomposer\app\webroot\js\pages\dashboard.js

			$workspace_ids = $this->request->data['ids'];
			foreach ($workspace_ids as $index => $workspace_id) {
				// $workspace_id =  explode('-', $workspace_id);

				if ($workspace_id != '') {
					$this->ProjectWorkspace->id = $workspace_id;
					$this->ProjectWorkspace->set(array('sort_order' => $index + 1, 'project_id' => $this->request->data['id']));
					// echo $this->ProjectWorkspace->_query();
					$this->ProjectWorkspace->save();
				}
			}

			$project_id = $this->request->data['id'];
			$workspaces_list = $this->get_workspaces($project_id);
			//pr($workspaces_list);
			echo json_encode($workspaces_list);
			//pr($this->request->data,1);
			exit;
		}
	}

	/*
		     * @name  		workspaceConfigPopup
		     * @todo  		Show a popup to Set sort order of workspaces of a project
		     * @access		public
		     * @package  	App/Controller/ProjectsController
	*/

	public function workspaceConfigPopup($project_id = null) {
		$this->layout = 'ajax';

		if (empty($this->user_id)) {
			return;
		}

		$user_project = $this->Project->UserProject->find('first', [
			'recursive' => 1,
			'conditions' => [
				// 'UserProject.user_id' => $this->user_id,
				'UserProject.project_id' => $project_id,
			],
			'fields' => [
				'UserProject.id', 'UserProject.project_id', 'Project.id', 'Project.title',
			],
		]);
		$this->set("user_project", $user_project);
		// pr($user_project, 1);
		$project_workspaces = $this->Workspace->ProjectWorkspace->find('all', [
			'recursive' => 1,
			'conditions' => [
				'ProjectWorkspace.project_id' => $project_id,
				'ProjectWorkspace.workspace_id !=' => '',
				'Workspace.id !=' => '',
				'Workspace.studio_status !=' => 1,
			],
			'fields' => [
				'ProjectWorkspace.id', 'ProjectWorkspace.leftbar_status', 'Workspace.id', 'Workspace.title',
			],
		]);
		//
		//pr($project_workspaces); die;

		$this->set(compact("project_workspaces"));

		/*
			          $detail = $this->Project->ProjectWorkspace->find('first', array(
			          'recursive' => -1,
			          'conditions' => array('ProjectWorkspace.project_id' => $project_id),
			          'fields' => array('MAX(ProjectWorkspace.sort_order) AS max_sort' ) ) );

			          $max_sort = 1;
			          if( isset($detail) && !empty($detail) ) {
			          if( isset($detail[0]['max_sort']) ) {
			          $max_sort = $detail[0]['max_sort'];
			          }
			          }

			          $projects = $this->Project->UserProject->find('first', array('recursive' => 2, 'conditions' => array('UserProject.project_id' => $project_id, 'UserProject.user_id' => $this->user_id), 'order' => array('UserProject.created DESC')));

			          $selectedWorkspaceIds = array();
			          if( isset($projects) && !empty($projects) ) {
			          foreach ($projects['Project']['ProjectWorkspace'] as $WorkspaceArray) {
			          $selectedWorkspaceIds[] = $WorkspaceArray['workspace_id'];
			          }
			          }
			          // echo $this->Project->_query();
			          // pr($projects, 1);

			          $this->set('selectedWorkspaceIds', $selectedWorkspaceIds);
			          $this->set('projects_title', $projects['Project']['title']);
			          $this->set('projectId', $projects['Project']['id']);
			          $this->set('max_sort', $max_sort);
		*/
	}

	public function exportwsp($project_id = null) {
		$this->layout = 'ajax';

		if (empty($this->user_id)) {
			return;
		}

		$user_project = $this->Project->UserProject->find('first', [
			'recursive' => 1,
			'conditions' => [
				// 'UserProject.user_id' => $this->user_id,
				'UserProject.project_id' => $project_id,
			],
			'fields' => [
				'UserProject.id', 'UserProject.project_id', 'Project.id', 'Project.title',
			],
		]);
		$this->set("user_project", $user_project);
		// pr($user_project, 1);
		$project_workspaces = $this->Workspace->ProjectWorkspace->find('all', [
			'recursive' => 1,
			'conditions' => [
				'ProjectWorkspace.project_id' => $project_id,
				'ProjectWorkspace.workspace_id !=' => '',
			],
			'fields' => [
				'ProjectWorkspace.id', 'ProjectWorkspace.leftbar_status', 'Workspace.id', 'Workspace.title',
			],
		]);
		//
		//pr($project_workspaces); die;

		$this->set(compact("project_workspaces"));
	}

	public function shares($project_id = null) {

		$this->layout = 'inner';

		$this->set('page_heading', 'Project Sharing');

		$project_detail = $this->Project->UserProject->find('first', ['conditions' => ['UserProject.project_id' => $project_id, 'UserProject.user_id' => $this->user_id], 'recursive' => 2]);
		$this->set('project_detail', $project_detail);

		$users = $this->UserDetail->find('all', ['fields' => ['UserDetail.user_id', 'UserDetail.first_name', 'UserDetail.last_name'], 'recursive' => -1]);
		$users_list = [];
		if (isset($users) && !empty($users)) {
			foreach ($users as $k => $v) {
				$userdata = $v['UserDetail'];
				$users_list[$userdata['user_id']] = $userdata['first_name'] . ' ' . $userdata['last_name'];
			}
		}
		$this->set(compact('users_list'));

		if (isset($project_detail) && !empty($project_detail)) {
			$cat_crumb = get_category_list($project_detail['Project']['id']);

			$project_title = $project_detail['Project']['title'];

			$crumb = [
				'Summary' => [
					'data' => [
						'url' => '/projects/index/' . $project_id,
						'class' => 'tipText',
						'title' => $project_detail['Project']['title'],
						'data-original-title' => $project_detail['Project']['title'],
					],
				],
				'last' => [
					'data' => [
						'title' => 'Sharing',
						'data-original-title' => 'Sharing',
					],
				],
			];
			if (isset($cat_crumb) && !empty($cat_crumb)) {
				$crumb = array_merge($cat_crumb, $crumb);
			}

			$this->set('crumb', $crumb);
		}
	}

	public function group($project_id = null) {
		$this->layout = 'inner';
		$user_id = $this->Auth->user('id');

		$this->set('page_heading', 'Project Group');

		$project_detail = $this->Project->UserProject->find('all', ['conditions' => ['UserProject.owner_user' => 1, 'UserProject.user_id' => $this->user_id], 'fields' => ['Project.id', 'Project.title'], 'recursive' => 1]);

		$project_list = [];
		if (isset($project_detail) && !empty($project_detail)) {

			foreach ($project_detail as $p) {
				$project_detail = $p['Project'];
				$project_list[$project_detail['id']]['id'] = $project_detail['id'];
				$project_list[$project_detail['id']]['label'] = $project_detail['title'];
			}
			if (isset($project_list) && !empty($project_list)) {
				$p_list = [];
				foreach ($project_list as $k => $v) {
					$p_list[$v['id']] = $v['label'];
				}
			}
		}

		$this->set(compact('p_list'));

		$users = $this->UserDetail->find('all', ['fields' => ['UserDetail.user_id', 'UserDetail.first_name', 'UserDetail.last_name'], 'recursive' => -1]);
		$users_list = [];
		if (isset($users) && !empty($users)) {

			foreach ($users as $v) {
				$userdata = $v['UserDetail'];
				$users_list[$userdata['user_id']]['id'] = $userdata['user_id'];
				$users_list[$userdata['user_id']]['label'] = $userdata['first_name'] . ' ' . $userdata['last_name'];
			}
		}
		$this->set(compact('users_list'));

		$this->loadModel('GroupUser');
		$this->loadModel('ProjectGroup');

		$this->request->data['ProjectGroup']['group_owner_id'] = $user_id;

		if ($this->request->is('post') || $this->request->is('put')) {

			$uids = array_keys($this->request->data['GroupUsers']['user_id']);

			$this->ProjectGroup->set($this->request->data);
			if ($this->ProjectGroup->validates()) {
				$this->request->data['UserProject']['user_id'] = $this->user_id;

				if ($this->ProjectGroup->save($this->request->data)) {

					$project_group_id = $this->ProjectGroup->getLastInsertId();
					$this->request->data['GroupUser']['project_group_id'] = $project_group_id;

					foreach ($uids as $uid) {
						$this->request->data['GroupUser']['id'] = '';
						$this->request->data['GroupUser']['user_id'] = $uid;
						$this->GroupUser->save($this->request->data);
					}

					$this->redirect(array('controller' => 'projects', 'action' => 'group', $project_group_id));
				}
			} else {
				$v = $this->validateErrors($this->ProjectGroup);
				// pr($v);die;
			}
		}
	}

	/* ======================= ADMIN FUNCTIONS ========================= */

	/**
	 * Admin add project method
	 *
	 * @return void
	 */
	public function admin_manage_project($project_id = null) {

		$data = null;

		$this->layout = 'admin_inner';

		if (isset($project_id) && !empty($project_id)) {

			$this->set('title_for_layout', __('Update Project', true));
			$this->set('text_val', __('Update', true));

			$this->set('project_id', $project_id);
		} else {
			$this->set('title_for_layout', __('Create Project', true));
			$this->set('text_val', __('Create', true));
		}

		if ($this->request->is('post') || $this->request->is('put')) {

			$this->Project->set($this->request->data);
			if ($this->Project->validates()) {
				$this->request->data['UserProject']['user_id'] = $this->user_id;

				// pr($this->request->data, 1);
				if ($this->UserProject->saveAssociated($this->request->data)) {

					if (!isset($project_id) && empty($project_id)) {
						$project_id = $this->Project->getLastInsertId();
					}

					$this->redirect(array('controller' => 'projects', 'action' => 'index', $project_id, 1));
				}
			} else {
				$v = $this->validateErrors($this->Project);
				// pr($v);die;
			}
		} else {
			if (isset($project_id) && !empty($project_id)) {
				$this->request->data = $this->UserProject->find("first", ['conditions' => ['UserProject.project_id' => $project_id, 'UserProject.user_id' => $this->user_id]]);
			}

		}
	}

	public function admin_workspaceConfigPopup($project_id = null) {
		$this->layout = 'ajax';
		// $projects = $this->Project->UserProject->find('first', array('conditions' => array('UserProject.user_id' => $this->user_id), 'order' => array('UserProject.created DESC'), 'recursive' => 2));

		$this->loadModel('ProjectWorkspace');
		$detail = $this->Project->ProjectWorkspace->find('first', array(
			'recursive' => -1,
			'conditions' => array('ProjectWorkspace.project_id' => $project_id),
			'fields' => array('MAX(ProjectWorkspace.sort_order) AS max_sort')));

		$max_sort = 1;
		if (isset($detail) && !empty($detail)) {
			if (isset($detail[0]['max_sort'])) {
				$max_sort = $detail[0]['max_sort'];
			}
		}

		$projects = $this->Project->UserProject->find('first', array('recursive' => 2, 'conditions' => array('UserProject.project_id' => $project_id, 'UserProject.user_id' => $this->user_id), 'order' => array('UserProject.created DESC')));

		$selectedWorkspaceIds = array();
		if (isset($projects) && !empty($projects)) {
			foreach ($projects['Project']['ProjectWorkspace'] as $WorkspaceArray) {
				$selectedWorkspaceIds[] = $WorkspaceArray['workspace_id'];
			}
		}
		// echo $this->Project->_query();
		// pr($projects, 1);

		$this->set('selectedWorkspaceIds', $selectedWorkspaceIds);
		$this->set('projects_title', $projects['Project']['title']);
		$this->set('projectId', $projects['Project']['id']);
		$this->set('max_sort', $max_sort);
	}

	public function admin_manage_elements($project_id = null, $workspace_id = null) {

		if (is_null($project_id) || is_null($workspace_id)) {
			$this->redirect(Controller::referer());
		}

		$this->loadModel("Template");

		$this->layout = 'admin_inner';

		$this->set('title_for_layout', __('Manage Elements', true));

		$data['page_heading'] = 'Elements';

		if ($this->request->isAjax()) {

			$response = [
				'success' => false,
				'msg' => '',
				'content' => null,
			];

			$this->layout = 'ajax';
			$this->autoRender = false;

			if ($this->request->is('post') || $this->request->is('put')) {
				$data = $this->request->data;
				$data['Project']['user_id'] = $this->user_id;

				$this->Project->set($data);
				if ($this->Project->validates()) {
					if ($this->Project->save($this->request->data)) {
						$insertId = $this->Project->getLastInsertId();
						// $detail = $this->Project->findById($insertId);
						$response['success'] = true;
						$response['msg'] = "Success";
						$response['content'] = array('id' => $insertId);
					} else {
						$response['msg'] = "Error!!!";
					}
				} else {
					$response['content'] = $this->validateErrors($this->Project);
				}
			}

			echo json_encode($response);
			exit();
		}

		$this->Workspace->recursive = 2;
		$workspace = $this->Workspace->find('first', [
			'conditions' => [
				'Workspace.id' => $workspace_id,
			],
		]);

		$a = $this->Workspace->Area->find('all', [
			'conditions' => [
				'Workspace.id' => $workspace_id,
				// "not" => array ( 'Area.is_standby' => 1)
				'Area.is_standby <=' => 0,
			],
			'recursive' => 1,
			'fields' => ['Workspace.id', 'Area.*'],
		]);

		// pr($a, 1);
		$td_ids = null;
		$count_td_ids = $count_areas = 0;

		if (isset($workspace) && !empty($workspace)) {
			$td_ids = Set::extract($workspace, '/Template/TemplateDetail/id');
			$count_td_ids = ( isset($td_ids) && !empty($td_ids) ) ? count($td_ids) : 0;

			$count_areas = ( isset($workspace['Area']) && !empty($workspace['Area']) ) ? count($workspace['Area']) : 0;
		}

		// If THIS IS THE FIRST TIME AFTER CREATING A WORKSPACE
		// IF THERE ARE NO ANY AREA IS THERE; CREATE THEM
		if ($count_td_ids != $count_areas) {

			// BEFORE CREATING, DELETE ALL AREAS OF CURRENT WORKSPACE
			if (!$this->Workspace->Area->hasAny(array('Area.workspace_id' => $workspace_id))) {

				$template_detail = $workspace['Template']['TemplateDetail'];

				if (!empty($count_td_ids)) {
					$areas = null;
					foreach ($td_ids as $key => $td) {

						$areas[$key]['Area'] = ['title' => 'Area', 'description' => 'Area', 'tooltip_text' => 'Area', 'is_standby' => '0', 'status' => '1', 'template_detail_id' => $td, 'workspace_id' => $workspace_id];
					}

					if (!empty($areas)) {
						$this->Area->saveAll($areas);
					}
				}
			}
			//
		}

		$this->loadModel('TemplateDetail');
		$template_groups = $this->TemplateDetail->find('all', array(
			'fields' => 'DISTINCT row_no, id',
			'conditions' => array(
				'TemplateDetail.template_id' => $workspace['Workspace']['template_id'],
			),
		));

		$grouped_ids = Set::extract($template_groups, '/TemplateDetail/id');

		$area_template_data = $this->Workspace->Area->find('all', [
			'fields' => ['Area.id as area_id', 'Area.workspace_id', 'Area.title', 'Area.description as desc', 'Area.tooltip_text', 'Area.is_standby', 'Area.status',
				'TemplateDetail.id as temp_detail_id', 'TemplateDetail.row_no', 'TemplateDetail.col_no', 'TemplateDetail.size_w', 'TemplateDetail.size_h', 'TemplateDetail.template_id'],
			'conditions' => [
				'Area.workspace_id' => $workspace_id,
				'Area.template_detail_id' => $grouped_ids,
			],
			'recursive' => 1,
			'order' => ['TemplateDetail.id ASC', 'TemplateDetail.row_no ASC'],
		]);

		$templateRows = null;
		foreach ($area_template_data as $row_id => $row_templates) {
			$area_detail = $row_templates['Area'];
			$temp_detail = $row_templates['TemplateDetail'];
			$elements = $row_templates['Elements'];

			if ($temp_detail['size_w'] > 0 && $temp_detail['size_h'] > 0) {
				$row_no = $temp_detail['row_no'];
				$area_templates = array_merge($temp_detail, $area_detail);
				if (isset($elements) && !empty($elements)) {
					$area_templates['elements'] = $elements;
				}
				// pr($area_templates);
				$templateRows[$row_no][] = $area_templates;
			}
		}

		// pr($templateRows, 1);
		$data['templateRows'] = $templateRows;

		$this->setJsVar('tip_text_remote', Router::Url(array('controller' => 'projects', 'action' => 'getTipText', 'admin' => FALSE), TRUE));
		$this->setJsVar('get_element_remote', Router::Url(array('controller' => 'entities', 'action' => 'get_element_clone', 'admin' => FALSE), TRUE));

		$this->setJsVar('workspace_data', $workspace);
		$this->setJsVar('workspace_id', $workspace_id);

		$this->set('project_id', $project_id);
		$this->set('workspace_id', $workspace_id);

		$data['workspace'] = $workspace;

		$this->set('data', $data);
	}

	public function admin_list($project_id = null) {

		//echo 	$this->Auth->user('id'); die;
		$this->layout = 'inner';
		$this->set('title_for_layout', __('Projects', true));
		$this->set('page_heading', __('Projects', true));
		$user_id = $this->Auth->user('id');

		$project_where['UserProject.user_id'] = $user_id;
		$project_order = [];
		if (isset($project_id) && !empty($project_id)) {
			$project_where['Project.id'] = $project_id;
		} else {
			$project_order = array('UserProject.modified DESC');
		}

		$this->Project->recursive = 1;
		$projects = $this->UserProject->find('all', array('conditions' => $project_where, 'order' => $project_order));

		$this->set('projects', $projects);
		$this->set(compact('projects'));

		$this->set('projects', $projects);
	}

	public function admin_index($project_id = null, $create_project = 0) {

		$this->layout = 'admin_inner';

		$this->set('title_for_layout', __('Project Summary', true));

		$user_id = $this->Auth->user('id');

		// CHECK THAT HTTP-REFERER IS manage_project
		// TO SHOW DIFFERENT MESSAGE ON LISTING
		$create_referer = false;
		if ($this->referer() == Router::url(array('action' => 'manage_project'), true)) {
			$create_referer = true;
		} else {
			$create_referer = false;
		}
		$this->set('create_referer', $create_referer);

		if (isset($project_id) && !empty($project_id)) {

			$projects = $this->UserProject->find('first', ['recursive' => 5, 'conditions' => ['UserProject.user_id' => $this->user_id, 'UserProject.project_id' => $project_id]]);
			//
			// echo $this->UserProject->_query();die;
			$this->set('project_id', $project_id);
		} else {
			$projects = $this->UserProject->find('first', ['recursive' => 5, 'conditions' => ['UserProject.user_id' => $this->user_id], 'order' => 'UserProject.modified DESC']);
			if (!is_null($projects)) {
				$projects_arr = Set::extract($projects, '/UserProject/project_id');
				$project_id = (isset($projects_arr[0]) && !empty($projects_arr[0])) ? $projects_arr[0] : null;
				$this->set("project_id", $project_id);
			}
		}
		// pr($projects);
		// Get logged in users projects
		$this->set(compact('projects'));

		$this->set('projects', $projects);
	}

	/**
	 * Displays a view
	 *
	 * @param mixed What page to display
	 * @return void
	 */
	public function admin_indexs() {

		$orConditions = array();
		$andConditions = array();
		$finalConditions = array();

		$in = 0;
		$per_page_show = $this->Session->read('project.per_page_show');
		if (empty($per_page_show)) {
			$per_page_show = ADMIN_PAGING;
		}

		if (isset($this->data['Project']['keyword'])) {
			$keyword = $this->data['Project']['keyword'];
		} else {
			$keyword = $this->Session->read('project.keyword');
		}

		if (isset($keyword)) {
			$this->Session->write('project.keyword', $keyword);
			if (!empty($keyword)) {
				$in = 1;
				$orConditions = array('OR' => array('Project.project_name LIKE' => '%' . $keyword . '%'));
			}
		}

		if (isset($this->data['Project']['projects_status_id'])) {
			$projects_status_id = $this->data['Project']['projects_status_id'];
		} else {
			$projects_status_id = $this->Session->read('project.projects_status_id');
		}

		if (isset($projects_status_id)) {
			$this->Session->write('project.projects_status_id', $projects_status_id);
			if ($projects_status_id != '') {
				$in = 1;
				$andConditions = array_merge($andConditions, array('Project.projects_status_id' => $projects_status_id));
			}
		}

		if (isset($this->data['Project']['per_page_show']) && !empty($this->data['Project']['per_page_show'])) {
			$per_page_show = $this->data['Project']['per_page_show'];
		}

		if (!empty($orConditions)) {
			$finalConditions = array_merge($finalConditions, $orConditions);
		}
		if (!empty($andConditions)) {
			$finalConditions = array_merge($finalConditions, array('AND' => $andConditions));
		}

		$this->set('title_for_layout', __('All Projects', true));
		$this->Session->write('project.per_page_show', $per_page_show);
		//$this->Project->recursive = 0;
		$this->paginate = array('conditions' => $finalConditions, "limit" => $per_page_show, "order" => "Project.created DESC");
		$this->set('projects', $this->paginate('Project'));
		$this->set('in', $in);
	}

	/**
	 * admin_source_view method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function admin_view($id = null) {
		$this->set('title_for_layout', __('View Project', true));
		$this->Project->id = $id;
		if (!$this->Project->exists()) {
			throw new NotFoundException(__('Invalid Project'));
		}

		$this->request->data = $this->Project->read(null, $id);
	}

	/**
	 * admin_source_add method
	 *
	 * @return void
	 */
	public function admin_add() {
		$this->set('title_for_layout', __('Add Project', true));
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->Project->save($this->request->data)) {
				$this->Session->setFlash(__('The Project has been saved successfully.'), 'success');
				die('success');
			}
		}
	}

	/**
	 * admin_source_edit method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function admin_edit($id = null) {

		$this->set('title_for_layout', __('Edit Project', true));
		$this->Project->id = $id;
		//check country exist
		if (!$this->Project->exists()) {
			$this->Session->setFlash(__('Invalid Project.'), 'error');
			$this->redirect(array('action' => 'index'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {

			if ($this->Project->save($this->request->data)) {
				$this->Session->setFlash(__('The Project has been updated successfully.'), 'success');
				die('success');
			}
		} else {
			$this->request->data = $this->Project->read(null, $id);
		}
	}

	/**
	 * admin_source_delete method
	 *
	 * @throws MethodNotAllowedException
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function admin_delete($id = null) {
		if (isset($this->data['id']) && !empty($this->data['id'])) {
			$id = $this->data['id'];
			$this->Project->id = $id;
			if (!$this->Project->exists()) {
				throw new NotFoundException(__('Invalid Project'), 'error');
			}

			if ($this->Project->delete()) {
				$this->Session->setFlash(__('Project has been deleted successfully.'), 'success');
				die('success');
			} else {
				$this->Session->setFlash(__('Project could not deleted successfully.'), 'error');
			}
		}
		die('error');
	}

	/**
	 * admin_source_updatestatus method
	 *
	 * @common for all location attributes like country . city , states etc.
	 * @return
	 */
	public function admin_updatestatus() {
		if ($this->request->is('ajax')) {
			$this->autoRender = false;
			$this->loadModel('Project');
			$this->request->data['Project'] = $this->request->data;

			if ($this->Project->save($this->request->data)) {
				$this->Session->setFlash(__('Project status has been updated successfully.'), 'success');
				die('success');
			} else {
				$this->Session->setFlash(__('Project status could not updated successfully.'), 'error');
			}
		}
		die('error');
	}

	public function savepdf($project_id = null, $fnm = null) {

		$this->loadModel('Project');
		$this->set('title_for_layout', __('Resources', true));
		$this->set('page_heading', __('Resources', true));
		$user_id = $this->Auth->user('id');

		if (isset($this->request->data['Project']['id'])) {
			$project_id = $this->request->data['Project']['id'];
		}

		$project_where['UserProject.user_id'] = $user_id;
		$project_order = [];
		if (isset($project_id) && !empty($project_id)) {
			$project_where['Project.id'] = $project_id;
		} else {
			$project_order = array('UserProject.modified DESC');
		}

		$this->Project->recursive = 1;
		$projects = $this->UserProject->find('all', array('conditions' => $project_where, 'order' => $project_order));
		//pr($projects); die;
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

		$this->set('crumb', [/* 'Project' => '/projects/lists', */'last' => ['Resources']]);
		$this->set("project_id", $project_id);

		$this->paginate = $paginator;
		$this->set('projects', $this->paginate('UserProject'));

		if ($this->request->is('post') || $this->request->is('put')) {

			// pr($this->request->data, 1);
			$project_id = $this->request->data['Project']['id'];

			if (isset($this->request->data['ProjectWorkspace']) && !empty($this->request->data['ProjectWorkspace'])) {
				$pwsp = $this->request->data['ProjectWorkspace'];
				foreach ($pwsp as $wp) {
					if (isset($wp['workspace_id']) && !empty($wp['workspace_id'])) {
						$wps_id[] = $wp['workspace_id'];
					}
				}
			}
			$this->set('wps_id', $wps_id);
		}

		//	pr($this->paginate('UserProject')); die;

		$this->layout = 'pdf';
		$this->Mpdf->init();

		// $this->Mpdf=new mPDF('utf-8','A4-L','','' , 0 , 0 , 0 , 0 , 0 , 0);
		// $this->Mpdf->SetDisplayMode('fullpage');
		//	$this->Mpdf->list_indent_first_level = 0;
		//$this->Mpdf->setFilename(DOC_ROOT."/pdfs/".$fnm.'.pdf');
		// $this->Mpdf->setOutput('F');

		$this->Mpdf->setFilename($fnm . '.pdf');
		$this->Mpdf->setOutput('D');

		//return;
	}

	public function savedoc($project_id = null, $fnm = null) {

		$this->loadModel('Project');
		$this->set('title_for_layout', __('Resources', true));
		$this->set('page_heading', __('Resources', true));
		$user_id = $this->Auth->user('id');

		if (isset($this->request->data['Project']['id'])) {
			$project_id = $this->request->data['Project']['id'];
		}

		$project_where['UserProject.user_id'] = $user_id;
		$project_order = [];
		if (isset($project_id) && !empty($project_id)) {
			$project_where['Project.id'] = $project_id;
		} else {
			$project_order = array('UserProject.modified DESC');
		}

		$this->Project->recursive = 1;
		$projects = $this->UserProject->find('all', array('conditions' => $project_where, 'order' => $project_order));
		//pr($projects); die;
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

		$this->set('crumb', [/* 'Project' => '/projects/lists', */'last' => ['Resources']]);
		$this->set("project_id", $project_id);

		$this->paginate = $paginator;
		$this->set('projects', $this->paginate('UserProject'));

		if ($this->request->is('post') || $this->request->is('put')) {

			// pr($this->request->data, 1);
			$project_id = $this->request->data['Project']['id'];

			if (isset($this->request->data['ProjectWorkspace']) && !empty($this->request->data['ProjectWorkspace'])) {
				$pwsp = $this->request->data['ProjectWorkspace'];
				foreach ($pwsp as $wp) {
					if (isset($wp['workspace_id']) && !empty($wp['workspace_id'])) {
						$wps_id[] = $wp['workspace_id'];
					}
				}
			}
			$this->set('wps_id', $wps_id);
		}

		//	pr($this->paginate('UserProject')); die;

		$this->layout = 'doc';
	}

	public function saveppt($project_id = null, $fnm = null) {

		$this->loadModel('Project');
		$this->set('title_for_layout', __('Resources', true));
		$this->set('page_heading', __('Resources', true));
		$user_id = $this->Auth->user('id');

		if (isset($this->request->data['Project']['id'])) {
			$project_id = $this->request->data['Project']['id'];
		}

		$project_where['UserProject.user_id'] = $user_id;
		$project_order = [];
		if (isset($project_id) && !empty($project_id)) {
			$project_where['Project.id'] = $project_id;
		} else {
			$project_order = array('UserProject.modified DESC');
		}

		$this->Project->recursive = 1;
		$projects = $this->UserProject->find('all', array('conditions' => $project_where, 'order' => $project_order));
		//pr($projects); die;
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

		$this->set('crumb', [/* 'Project' => '/projects/lists', */'last' => ['Resources']]);
		$this->set("project_id", $project_id);

		$this->paginate = $paginator;
		$this->set('projects', $this->paginate('UserProject'));

		if ($this->request->is('post') || $this->request->is('put')) {

			// pr($this->request->data, 1);
			$project_id = $this->request->data['Project']['id'];

			if (isset($this->request->data['ProjectWorkspace']) && !empty($this->request->data['ProjectWorkspace'])) {
				$pwsp = $this->request->data['ProjectWorkspace'];
				foreach ($pwsp as $wp) {
					if (isset($wp['workspace_id']) && !empty($wp['workspace_id'])) {
						$wps_id[] = $wp['workspace_id'];
					}
				}
			}
			$this->set('wps_id', $wps_id);
		}

		//	pr($this->paginate('UserProject')); die;

		$this->layout = 'doc';
	}

	public function element_resetfilter($eid, $pid) {
		$this->Session->write('project.keyword', '');
		$this->Session->write('user.keyword', '');
		$this->Session->write('user.status', '');
		$this->Session->write('element.start', '');
		$this->Session->write('element.end', '');
		$this->Session->write('project.description', '');
		$this->Session->write('project.title', '');
		$this->Session->write('element.description', '');
		$this->Session->write('element.title', '');
		$this->redirect(array('action' => 'manage_elements', $eid, $pid, 'admin' => false));
	}

	public function communication() {
		pr($this->request->data, 1);
	}

	public function comm() {
		$this->layout = 'inner';
	}

	public function CheckProjectType($pid, $uid) {
		$project_id = $pid;
		$user_id = CakeSession::read("Auth.User.id");
		$this->loadModel("UserPermission");

		$project_type = $this->UserPermission->query("SELECT user_permissions.role FROM
			`user_permissions`
			INNER JOIN projects
			ON user_permissions.project_id = projects.id
			Where user_permissions.user_id = $uid and
			user_permissions.project_id = $pid and user_permissions.workspace_id IS NULL
			order by projects.title "
			);

		if( isset($project_type) && isset($project_type[0]['user_permissions']) && !empty($project_type[0]['user_permissions']['role'])  ){

			if($project_type[0]['user_permissions']['role'] == 'Sharer' || $project_type[0]['user_permissions']['role'] == 'Owner' ){
				return 'r_project';
			} else if( $project_type[0]['user_permissions']['role'] == 'Group Owner' || $project_type[0]['user_permissions']['role'] == 'Group Sharer' ){
				return 'g_project';
			} else {
				return 'm_project';
			}
		} else {
			return false;
		}
	}

	public function CheckProjectType_old($pid, $uid) {
		$this->autoRender = FALSE;
		$project_id = $pid;
		//$user_id = $this->Auth->user('id');

		$data = $this->UserProject->find('first', array('conditions' => array('UserProject.user_id' => $uid, 'UserProject.project_id' => $pid, 'UserProject.owner_user' => 1)));

		if (isset($data) & !empty($data)) {

			return isset($data) ? "m_project" : "false";

		}
		// echo ClassRegistry::init('UserProject')->_query();

		/* -----------Group code----------- */
		$projectsg = $this->UserProject->find('first', ['recursive' => 2, 'conditions' => ['UserProject.project_id' => $project_id]]);
		$conditionsG = $projects_group_shared_user = null;
		if (isset($projectsg) && !empty($projectsg)) {
			$pgupid = $projectsg['UserProject']['id'];
			$conditionsG['ProjectGroupUser.user_id'] = $this->user_id;
			$conditionsG['ProjectGroupUser.user_project_id'] = $pgupid;
			$conditionsG['ProjectGroupUser.approved'] = 1;
			$projects_group_shared_user = $this->ProjectGroupUser->find('first', array(
				'conditions' => $conditionsG,
				'fields' => array('ProjectGroupUser.project_group_id'),
				'recursive' => -1,
			));
		}

		if (isset($projects_group_shared_user) && !empty($projects_group_shared_user)) {
			//echo $project_id." sdsd ".$projects_group_shared_user['ProjectGroupUser']['project_group_id']; die;
			$group_permission = $this->Group->group_permission_details($project_id, $projects_group_shared_user['ProjectGroupUser']['project_group_id']);

			$pll_level = $group_permission['ProjectPermission']['project_level'];

			$this->set('gpid', $projects_group_shared_user['ProjectGroupUser']['project_group_id']);

			if (isset($pll_level) && $pll_level == 1) {
				$this->set('project_level', 1);
			}

			return isset($projects_group_shared_user) ? "g_project" : "false";
		}
		/* -----------Group code----------- */
		//pr($group_permission,1);

		/* -----------sharing code----------- */
		$conditionsN = null;
		$conditionsN['ProjectPermission.user_id'] = $this->user_id;
		$this->loadModel('ProjectPermission');
		$projects_shared = $this->ProjectPermission->find('all', array(
			'conditions' => $conditionsN,
			'fields' => array('ProjectPermission.user_project_id'),
			'order' => 'ProjectPermission.created DESC',
			'recursive' => -1,
		));
		/* -----------sharing code----------- */

		if ((isset($projects_shared) && !empty($projects_shared)) && empty($projects)) {

			$projectss = $this->UserProject->find('first', ['recursive' => 2, 'conditions' => ['UserProject.project_id' => $project_id]]);

			if (isset($projectss) && !empty($projectss)) {
				$projects = $projectss;
				$counssst = $this->ProjectPermission->find('count', array(
					'conditions' => array('ProjectPermission.user_project_id' => $projectss['UserProject']['id']),
					'fields' => array('ProjectPermission.user_project_id'),
					'order' => 'ProjectPermission.created DESC',
					'recursive' => -1,
				));

				if (isset($counssst) && $counssst > 0) {
					$projects = $this->UserProject->find('first', ['recursive' => 2, 'conditions' => ['UserProject.id' => $projectss['UserProject']['id']]]);

					return isset($projects) ? "r_project" : "false";
				}
			}
		}
	}

/******************** Objectives ***************************/

	/*
		     * @name  	objectives
		     * @access	public
		     * @package  App/Controller/ProjectsController
	*/

	public function objectives($project_id = null, $ragstatus = null, $budgets = null) {

		$this->layout = 'inner';

		if (isset($project_id) && !empty($project_id) ) {
			if(!dbExists('Project', $project_id)){
				$this->redirect(array('controller' => 'projects', 'action' => 'objectives'));
			}
		}

		$this->set('page_heading', __('Status Center', true));
		$this->set('title_for_layout', __('Status Center', true));
		$this->set('page_subheading', __('View key information about your Projects', true));

		// App::import('Controller', 'Users');
		// $Users = new UsersController;
		$projects = null;
		$myprojectlist = $myreceivedprojectlist = $mygroupprojectlist = array();
		// Find All current user's projects
		// $myprojectlist = $Users->__myproject_selectbox($this->user_id);
		// Find All current user's received projects
		// $myreceivedprojectlist = $Users->__receivedproject_selectbox($this->user_id, 1);
		// Find All current user's group projects
		// $mygroupprojectlist = $Users->__groupproject_selectbox($this->user_id, 1);

		$myprojectlist = myprojects($this->user_id);
		$mygroupprojectlist = groupprojects($this->user_id, 1);
		$myreceivedprojectlist = receivedprojects($this->user_id, 1);


		if (is_array($myprojectlist)) {
			$projects1 = $myprojectlist;
		}

		if (is_array($mygroupprojectlist)) {
			$projects1 = array_replace($mygroupprojectlist, $projects1);
		} else {
			$projects1 = $projects1;
		}

		if (is_array($myreceivedprojectlist) && is_array($projects1)) {
			$projects1 = array_replace($myreceivedprojectlist, $projects1);
		} else {
			$projects1 = $projects1;
		}

		$projects = array_map("strip_tags", $projects1);
		$projects = array_map("trim", $projects);
		natcasesort($projects);

		$this->set('projects', $projects);

		$aligned = $this->Aligned->find("list", ['order' => ['Aligned.title ASC']]);
		$this->set('aligned', $aligned);


		$this->set('project_id', $project_id);
		$this->setJsVar('objectives', ['project_id' => $project_id, 'ragstatus' => $ragstatus, 'budgets' => $budgets]);
		$this->set('ragstatus', $ragstatus);
		$this->set('budgets', $budgets);


		$crumb = [
			//'Project' => '/projects/lists/',
			'last' => [
				'data' => [
					'title' => "Status Center",
					'data-original-title' => "Status Center",
				],
			],
		];
		$this->set('crumb', $crumb);

		/*$projectCategoryId = array();
		if (isset($projects) && !empty($projects)) {
			foreach ($projects as $pkey => $pvalue) {

				$projectlist = $this->Project->find("first", array('conditions' => array('Project.id' => $pkey)));
				if (isset($projectlist) && $projectlist['Project']['category_id'] > 0) {
					$projectCategoryId[] = $projectlist['Project']['category_id'];
				}

			}

			$userCategories = array();
			$sqlCategory = $this->Category->query("SELECT id FROM categories WHERE user_id = '" . $this->user_id . "' ");

			if (isset($sqlCategory) && !empty($sqlCategory) && count($sqlCategory) > 0) {

				foreach ($sqlCategory as $catValue) {
					$userCategories[] = $catValue['categories']['id'];
				}
			}
		}
		$allUserCategory = array();
		if (isset($userCategories) && !empty($userCategories) && count($userCategories) > 0 && isset($projectCategoryId) && !empty($projectCategoryId) && count($projectCategoryId) > 0) {
			$allUserCategory = array_unique(array_merge($userCategories, $projectCategoryId));
		}

		$categoryw_id = null;
		if (isset($project_id) && !empty($project_id)) {

			$project_ids = $this->Project->find("first", array('conditions' => array('Project.id' => $project_id)));
			//echo $project_ids['Project']['category_id'];die;
			if (isset($project_ids) && $project_ids['Project']['category_id'] > 0) {
				$categoryw_id = $project_ids['Project']['category_id'];
			}
		}

		$this->set('category_id', $categoryw_id);*/

		//$projects = get_my_projects($this->user_id);

			/* 		if(isset($allUserCategory) && !empty($allUserCategory)){

			$this->loadModel('CategoryAll');
			$categories_list = $this->CategoryAll->find('all', array('conditions'=> array('Category.id'=>$allUserCategory)));

			$categories = tree_list($categories_list, 'Category', 'id', 'title');

			$categories_temp = null;
			if (isset($categories) && !empty($categories)) {
			$categories_temp = $categories;
			foreach ($categories as $k => $v) {
			$n = category_projects($k, true);
			if ($n) {
			$categories_temp[$k] = $v . ' (' . $n . ')';
			}
			}
			}
			//$categories = $categories_temp;
			//pr($categories); die;
			$this->set(compact('categories'));

			} else { */

		/*$categories_list = $this->Category->find('threaded', array('recursive' => -1));
		$categories = tree_list($categories_list, 'Category', 'id', 'title');
		$categories_temp = null;
		if (isset($categories) && !empty($categories)) {
			$categories_temp = $categories;
			foreach ($categories as $k => $v) {
				$n = category_projects($k, true);
				if ($n) {
					$categories_temp[$k] = $v . ' (' . $n . ')';
				}
			}
		}
		$categories = $categories_temp;
		$this->set(compact('categories'));*/

		//}

	}

	public function filtered_data() {

		$view = new View();
		$viewModal = $view->loadHelper('ViewModel');

		if ($this->request->isAjax()) {
			$this->layout = 'ajax';

			$response = [
				'success' => false,
				'msg' => '',
				'content' => null,
			];
			App::import('Controller', 'Users');
			$Users = new UsersController;

			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;
				// pr($post );

				$conditions = [];
				$summary_view = (isset($post['summary_view']) && !empty($post['summary_view'])) ? $post['summary_view'] : 0;
				$this->set('summary_view', $summary_view);



				$project_ids = null;
				$program_id = null;
				$conditions = $projectN = [];

				$rag_status = $allign = 0;
				if (isset($post['rag_status']) && !empty($post['rag_status'])) {
					// $conditions['Project.rag_current_status'] = $post['rag_status'];
					$rag_status = $post['rag_status'];
				}
				if ( isset($post['aligned_id']) && !empty($post['aligned_id'])) {
					$allign = $post['aligned_id'];
				}
				if (isset($post['program_id']) && !empty($post['program_id'])) {
					$program_id = $post['program_id'];
				}
				if (isset($post['project_id']) && !empty($post['project_id']) && $post['project_id'] != 'none') {
					$project_id = $post['project_id'];

					$projectIDs = [$post['project_id']];
					$project_ids = $this->objView->loadHelper('Permission')->all_my_projects(1, $rag_status, $allign, $projectIDs);
				}

				if ((isset($post['project_id']) && !empty($post['project_id']) && $post['project_id'] != 'none') && (( isset($post['aligned_id']) && !empty($post['aligned_id'])) || (isset($post['rag_status']) && !empty($post['rag_status']))) ) {
					$project_ids = [];
					if(isset($post['rag_status']) && !empty($post['rag_status'])){
						$rag_status = $post['rag_status'];
					}
					if(isset($post['aligned_id']) && !empty($post['aligned_id'])){
						$allign = $post['aligned_id'];
					}
					$projectIDs = [$post['project_id']];
					$project_ids = $this->objView->loadHelper('Permission')->all_my_projects(1, $rag_status, $allign, $projectIDs);
				}

				if ((!isset($post['project_id']) || empty($post['project_id']) || $post['project_id'] == 'none')) {
					$project_ids = $this->objView->loadHelper('Permission')->all_my_projects(1, $rag_status, $allign);
				}


				$this->set('program_id', $program_id);

				$this->set('projects', $project_ids);
				// pr($project_ids, 1);


				/*
					$rag_status = 0;
					if (isset($post['rag_status']) && !empty($post['rag_status'])) {
						$conditions['Project.rag_current_status'] = $post['rag_status'];
						$rag_status = $post['rag_status'];
					}
					$program_id = null;
					//========= Updated 12 Jan 2018 ===============
					if (isset($post['program_id']) && !empty($post['program_id'])) {

						$program_id = $post['program_id'];
					}

					$project_id = 0;
					if (isset($post['project_id']) && !empty($post['project_id']) && $post['project_id'] != 'none') {
						$conditions['Project.id'] = $post['project_id'];
						$project_id = $post['project_id'];
					}

					$this->UserProject->unbindModel(['hasMany' => ['ProjectPermission']]);

					$viewData['projects'] = null;
					$project_ids = null;
					if (!isset($post['project_id']) || empty($post['project_id']) || $post['project_id'] == 'none') {
						$myprojectlist = myprojects($this->user_id);
						$mygroupprojectlist = groupprojects($this->user_id, 1);
						$myreceivedprojectlist = receivedprojects($this->user_id, 1);

						if (is_array($myprojectlist)) {
							$projects1 = $myprojectlist;
						}

						if (is_array($mygroupprojectlist)) {
							$projects1 = array_replace($mygroupprojectlist, $projects1);
						} else {
							$projects1 = $projects1;
						}

						if (is_array($myreceivedprojectlist) && is_array($projects1)) {
							$projects1 = array_replace($myreceivedprojectlist, $projects1);
						} else {
							$projects1 = $projects1;
						}

						if (isset($program_id) && !empty($program_id)) {
							$this->loadModel('ProjectProgram');
							$projgram_project_ids = $this->ProjectProgram->find('list', array('conditions' => array('ProjectProgram.program_id' => $program_id), 'fields' => array('ProjectProgram.id', 'ProjectProgram.project_id')));

							if ((isset($projgram_project_ids) && !empty($projgram_project_ids)) && !empty($projects1)) {
								$projgram_project_ids = array_map("strip_tags", $projgram_project_ids);
								$projgram_project_ids = array_map("trim", $projgram_project_ids);
								natcasesort($projgram_project_ids);
								$projectss = array_intersect($projgram_project_ids, array_keys($projects1));
							}
							$projectN = [];

							if (isset($projectss) && !empty($projectss)) {
								foreach ($projectss as $pros) {
									$projectN[$pros] = $projects1[$pros];
								}
								$projects1 = $projectN;
							}

						}

						if (isset($program_id) && empty($projectN)) {
							$projects1 = null;
						}

						if (!empty($projects1)) {
							$conditions['Project.id'] = array_keys($projects1);
						}

						if (!empty($projects1) && isset($post['aligned_id']) && !empty($post['aligned_id'])) {
							$conditions['Project.aligned_id'] = $post['aligned_id'];
						}
					} else if (isset($post['aligned_id']) && !empty($post['aligned_id'])) {
						$conditions['Project.aligned_id'] = $post['aligned_id'];
					}

					if ((isset($conditions) && !empty($conditions)) || !empty($projects1)) {
						$conditions['Project.studio_status !='] = 1;
						$allProjects = $this->Project->find('all', array('conditions' => $conditions, 'recursive' => 1, 'fields' => ['Project.*'], 'order' => ['Project.title'], 'recursive'=> -1));

						$viewData['projects'] = $allProjects;
					}

					//  pr($viewData, 1);
					$viewData['post'] = $post;
					$viewData['projt_id'] = $project_id;

					$this->set($viewData);
				*/
			}

		}

		$this->render(DS . 'Projects' . DS . 'partials' . DS . 'objectives' . DS . 'filtered_data');
	}

	public function objective_creator_sharers() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$response = [
				'success' => false,
				'msg' => '',
				'content' => null,
			];

			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;

				if (isset($post['project_id']) && !empty($post['project_id'])) {

				}
				if (isset($post['aligned_id']) && !empty($post['aligned_id'])) {

				}

				// $task_data = $this->objView->loadHelper('ViewModel')->get_project_elements($post['project_id'], false, false, true);

				// $this->set('elements', $task_data);
				$this->set('project_id', $post['project_id']);

			}

		}

		$this->render(DS . 'Projects' . DS . 'partials' . DS . 'objectives' . DS . 'objective_creator_sharers');
	}

	public function objective_task_detail() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$response = [
				'success' => false,
				'msg' => '',
				'content' => null,
			];

			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;

				if (isset($post['project_id']) && !empty($post['project_id'])) {

				}
				if (isset($post['aligned_id']) && !empty($post['aligned_id'])) {

				}

				$task_data = $this->objView->loadHelper('ViewModel')->get_project_elements($post['project_id'], false, false, true);
				$this->set('elements', $task_data);
				$this->set('project_id', $post['project_id']);

			}

		}

		$this->render(DS . 'Projects' . DS . 'partials' . DS . 'objectives' . DS . 'objective_task_detail');
	}

	public function objective_workspaces() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$response = [
				'success' => false,
				'msg' => '',
				'content' => null,
			];

			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;

				$workspaces = $this->objView->loadHelper('Permission')->wspAndTasks($post['project_id']);
				// $workspaces = $this->objView->loadHelper('Permission')->wsp_of_project($post['project_id']);

				$this->set('workspaces', $workspaces);
				$this->set('project_id', $post['project_id']);

			}

		}
		$this->render(DS . 'Projects' . DS . 'partials' . DS . 'objectives' . DS . 'objective_workspaces');
	}

	public function objective_task_chart() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$view = new View();
			$viewModal = $view->loadHelper('ViewModel');

			$response = [
				'success' => false,
				'msg' => '',
				'content' => null,
			];
			$not_spacified = $not_started = $progressing = $completed = $overdue = 0;
			$elements = null;

			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;

				$project_id = (isset($post['project_id']) && !empty($post['project_id'])) ? $post['project_id'] : null;
				$workspace_id = (isset($post['workspace_id']) && !empty($post['workspace_id'])) ? $post['workspace_id'] : null;


				if(isset($workspace_id) && !empty($workspace_id)){
					$elements = $this->objView->loadHelper('Permission')->wspTaskCounters($project_id , $workspace_id );
				}else{
					$elements = $this->objView->loadHelper('Permission')->projectTaskCounters($project_id);
				}


						$not_spacified = (isset($elements[0][0]['NON']) && !empty($elements[0][0]['NON'])) ? intval($elements[0][0]['NON']) : 0;
						$not_started = (isset($elements[0][0]['PND']) && !empty($elements[0][0]['PND'])) ? intval($elements[0][0]['PND']) : 0;
						$progressing = (isset($elements[0][0]['PRG']) && !empty($elements[0][0]['PRG'])) ? intval($elements[0][0]['PRG']) : 0;
						$completed = (isset($elements[0][0]['CMP']) && !empty($elements[0][0]['CMP'])) ? intval($elements[0][0]['CMP']) : 0;
						$overdue = (isset($elements[0][0]['OVD']) && !empty($elements[0][0]['OVD'])) ? intval($elements[0][0]['OVD']) : 0;

						$elements_tots =  $not_spacified + $not_started + $progressing + $completed + $overdue;



				$response['content'] = [
					'total_elements' => $elements_tots,
					'counters' => [
 						$not_spacified,
						$not_started,
						$progressing,
						$completed,
						$overdue,

					],
					'counter_data' => [
						number_format((($not_spacified / $elements_tots) * 100), 2),
						number_format((($not_started / $elements_tots) * 100), 2),
						number_format((($progressing / $elements_tots) * 100), 2),
						number_format((($completed / $elements_tots) * 100), 2),
						number_format((($overdue / $elements_tots) * 100), 2),
					],

				];
				$response['success'] = true;
				echo json_encode($response);
				exit;
			}


		}

	}

	public function objective_task_sort() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$view = new View();
			$viewModel = $view->loadHelper('ViewModel');

			$response = [
				'success' => false,
				'msg' => '',
				'content' => null,
			];

			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;

				$conditions = $pw_condition = [];
				$order = '';

				if (isset($post['project_id']) && !empty($post['project_id'])) {
					$this->set('project_id', $post['project_id']);
					$pw_condition['ProjectWorkspace.project_id'] = $post['project_id'];
				}
				if (isset($post['keyResult']) && !empty($post['keyResult'])) {
					$pw_condition['ProjectWorkspace.workspace_id'] = $post['keyResult'];
				}

				$workspaces = $elements = $area_ids = null;
				$this->ProjectWorkspace->Behaviors->load('Containable');

				$workspaces = $this->ProjectWorkspace->find('all', ['conditions' => $pw_condition, 'contain' => 'Workspace']);

				if (isset($workspaces) && !empty($workspaces)) {

					foreach ($workspaces as $key => $value) {

						$workspace = $value['Workspace'];

						$areas = $viewModel->workspace_areas($workspace['id'], false, true);
						if (isset($areas) && !empty($areas)) {
							if (is_array($area_ids)) {
								$area_ids = array_merge($area_ids, array_values($areas));
							} else {
								$area_ids = array_values($areas);
							}

						}

					}
				}

				$query = '';
				$diff = '';
				$from = '';

				$select = 'SELECT element.*';
				$diff = ', TIMESTAMPDIFF( DAY, element.`start_date`, element.`end_date`) AS totalDays ';

				$from = 'FROM elements as element ';

				if (isset($area_ids) && !empty($area_ids)) {

					$query .= "WHERE element.area_id IN (" . implode(',', $area_ids) . ") AND element.studio_status <> '1'";

					// Element date range --------------------------
					if ((isset($post['dateRange']) && !empty($post['dateRange'])) && empty($post['taskStatus'])) {

						$dateRange = $post['dateRange'];

						// soonest end
						if ($dateRange == 1) {
							$diff = ', TIMESTAMPDIFF( DAY, now(), element.`end_date`) AS totalDays ';
							$query .= "AND date(element.end_date) >= '" . date('Y-m-d') . "' ";
							$query .= "AND element.sign_off != '1' ";

							$order = 'ORDER BY totalDays ASC';
						}
						// latest end
						if ($dateRange == 2) {
							$query .= "AND date(element.modified) <= '" . date('Y-m-d') . "' ";
							$query .= "AND element.sign_off = '1' ";

							$order = 'ORDER BY totalDays ASC';
						}
						// schedule longest
						if ($dateRange == 3) {

							$query .= "AND element.sign_off != '1' ";
							$query .= "AND element.date_constraints = 1 ";

							$order = 'ORDER BY totalDays DESC';
						}
						// schedule shortest
						if ($dateRange == 4) {
							$query .= "AND element.sign_off != '1' ";
							$query .= "AND element.date_constraints = 1 ";

							$order = 'ORDER BY totalDays ASC';
						}
					}
					// Element Status --------------------------
					if ((isset($post['taskStatus']) && !empty($post['taskStatus'])) && empty($post['dateRange'])) {

						$taskStatus = $post['taskStatus'];

						if ($taskStatus == 0 || $taskStatus == 1) {
							// not spacified
							$query .= "AND ( element.date_constraints IS NULL OR element.date_constraints = 0 ) ";
							$query .= "AND element.sign_off != 1 ";
						} else if ($taskStatus == 2) {
							// not started
							$diff = ', TIMESTAMPDIFF( DAY, now(), element.`start_date`) AS totalDays ';
							$query .= "AND date(element.start_date) > '" . date('Y-m-d') . "' ";
							$query .= "AND element.date_constraints = 1 ";
							$query .= "AND element.sign_off != 1 ";
							$order = 'ORDER BY totalDays ASC';
						} else if ($taskStatus == 3) {
							// progressing
							$query .= "AND date(element.start_date) <= '" . date('Y-m-d') . "' ";
							$query .= "AND date(element.end_date) >= '" . date('Y-m-d') . "' ";
							$query .= "AND element.date_constraints = 1 ";
							$query .= "AND element.sign_off != 1 ";
						} else if ($taskStatus == 4) {
							// completed
							$query .= "AND element.sign_off = 1 ";
						} else if ($taskStatus == 5) {
							// overdue
							$diff = ', TIMESTAMPDIFF( DAY, element.`end_date`, now()) AS totalDays ';
							$query .= "AND date(element.end_date) < '" . date('Y-m-d') . "' ";
							$query .= "AND element.sign_off != 1 ";
							$query .= "AND element.date_constraints = 1 ";
							$order = 'ORDER BY totalDays DESC';
						}
					}

					if ((isset($post['dateRange']) && !empty($post['dateRange'])) && (isset($post['taskStatus']) && !empty($post['taskStatus']))) {
						$taskStatus = $post['taskStatus'];
						$dateRange = $post['dateRange'];
						if (($taskStatus == 2 || $taskStatus == 3) && ($dateRange == 1)) {

							// Soonest End or Progressing / Not Started
							$diff = ', TIMESTAMPDIFF( DAY, now(), element.`end_date`) AS totalDays ';
							$query .= "AND date(element.end_date) >= '" . date('Y-m-d') . "' ";
							$query .= "AND element.sign_off != '1' ";

							$order = 'ORDER BY totalDays ASC';

							$query .= "AND element.date_constraints = 1 ";
							if ($taskStatus == 3) {
								// Only if progressing
								$query .= "AND element.start_date  <= '" . date('Y-m-d') . "'  ";
							} else {
								$query .= "AND element.start_date  > '" . date('Y-m-d') . "'  ";
							}

							$query .= "AND element.sign_off != 1 ";
						}
						//else if( ($taskStatus == 4 || $taskStatus == 5) && ($dateRange == 2)) {
						else if (($taskStatus == 4) && ($dateRange == 2)) {
							// Latest End or Overdue / Completed

							$query .= "AND date(element.modified) <= '" . date('Y-m-d') . "' ";
							$query .= "AND element.sign_off = '1' ";

							$order = 'ORDER BY totalDays ASC';

						} else if (($taskStatus != 1) && ($dateRange == 3 || $dateRange == 4)) {
							// Scheduled Largest / Shortest or status should not be not specified

							if ($taskStatus == 2) {
								$query .= "AND element.start_date  > '" . date('Y-m-d') . "'  ";
								$query .= "AND element.sign_off != '1' ";
							}
							if ($taskStatus == 3) {
								$query .= "AND element.start_date  <= '" . date('Y-m-d') . "' ";
								$query .= "AND element.end_date  >= '" . date('Y-m-d') . "' ";
								$query .= "AND element.sign_off != '1' ";
							}
							if ($taskStatus == 4) {
								$query .= "AND element.sign_off = '1' ";
							}
							if ($taskStatus == 5) {
								$query .= "AND element.end_date  < '" . date('Y-m-d') . "' ";
								$query .= "AND element.sign_off != '1' ";
							}

							$query .= "AND element.date_constraints = 1 ";

							if ($dateRange == 3) {
								$order = 'ORDER BY totalDays DESC';
							} else {
								$order = 'ORDER BY totalDays ASC';
							}
						}
					}
				}

				// e( $select.$diff.$from.$query . '' . $order , 1);
				$data = $this->Element->query($select . $diff . $from . $query . '' . $order);

				$this->set('data', $data);

				$view = new View($this, false);
				$view->viewPath = 'Projects/partials/objectives'; // Directory inside view directory to search for .ctp files
				$view->layout = false; // if you want to disable layout
				$view->set('data', $data); // set your variables for view here
				$html = $view->render('objective_task_sort');
				echo json_encode($html);
				exit;
			}

		}
		// $this->render(DS . 'Projects' . DS . 'partials' . DS . 'objectives' . DS . 'objective_task_sort');
	}

	public function objective_decision_chart() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$view = new View();
			$viewModel = $view->loadHelper('ViewModel');

			$response = [
				'success' => true,
				'msg' => '',
				'content' => null,
			];

			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;
				$workspaces = null;
				if (isset($post['workspace_id']) && !empty($post['workspace_id'])) {
					$workspace_id = $post['workspace_id'];
					$workspaces = $viewModel->getWorkspaceDetail($workspace_id);
					$workspaces['Workspace'] = $workspaces;
				} else if (isset($post['project_id']) && !empty($post['project_id'])) {
					$project_id = $post['project_id'];
					$workspaces = get_project_workspace($project_id);
				}

				$dec_data = $chart_data = null;
				if (isset($workspaces) && !empty($workspaces)) {
					$dp = $dc = 0;
					foreach ($workspaces as $k => $v) {
						$dec_data = _workspace_decision_feedbacks($v['Workspace']['id']);
						$dp += (isset($dec_data['progress'])) ? $dec_data['progress'] : 0;
						$dc += (isset($dec_data['complete'])) ? $dec_data['complete'] : 0;
					}

					$response['content']['chart_filled'] = true;
					if (empty($dp) && empty($dc)) {
						$response['content']['chart_filled'] = false;
					}
					$response['content']['chart_data'] = [
						[
							'label' => 'Live',
							'value' => $dp,
						],
						[
							'label' => 'Completed',
							'value' => $dc,
						],
					];
				}

				echo json_encode($response);
				exit();
				// pr($post, 1);

			}

		}
		// $this->render(DS . 'Projects' . DS . 'partials' . DS . 'objectives' . DS . 'objective_decision_chart');
	}

	public function objective_feedback_chart() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$view = new View();
			$viewModel = $view->loadHelper('ViewModel');

			$response = [
				'success' => true,
				'msg' => '',
				'content' => null,
			];

			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;
				$workspaces = null;
				if (isset($post['workspace_id']) && !empty($post['workspace_id'])) {
					$workspaces_id = $post['workspace_id'];
				} else if (isset($post['project_id']) && !empty($post['project_id'])) {
					$project_id = $post['project_id'];
					$workspaces = get_project_workspace($project_id);
					$workspaces_id = Set::extract($workspaces, '/Workspace/id');
				}

				$dec_data = $chart_data = null;

				$dp = $dc = 0;
				if (isset($workspaces_id) && !empty($workspaces_id)) {

					$fedp_data = _workspace_feedbacks($workspaces_id, false);
					$fedc_data = _workspace_feedbacks($workspaces_id, true);

					$response['content']['chart_filled'] = true;
					if (empty($fedp_data) && empty($fedc_data)) {
						$response['content']['chart_filled'] = false;
					}
					$response['content']['chart_data'] = [
						[
							'label' => 'Live',
							'value' => $fedp_data,
						],
						[
							'label' => 'Completed',
							'value' => $fedc_data,
						],
					];
				}

				echo json_encode($response);
				exit();
				// pr($post, 1);

			}

		}
		// $this->render(DS . 'Projects' . DS . 'partials' . DS . 'objectives' . DS . 'objective_decision_chart');
	}

	public function objective_vote_chart() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$view = new View();
			$viewModel = $view->loadHelper('ViewModel');

			$response = [
				'success' => true,
				'msg' => '',
				'content' => null,
			];

			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;
				$workspaces = null;
				if (isset($post['workspace_id']) && !empty($post['workspace_id'])) {
					$workspaces_id = $post['workspace_id'];
				} else if (isset($post['project_id']) && !empty($post['project_id'])) {
					$project_id = $post['project_id'];
					$workspaces = get_project_workspace($project_id);
					$workspaces_id = Set::extract($workspaces, '/Workspace/id');
				}

				$chart_data = null;

				if (isset($workspaces_id) && !empty($workspaces_id)) {
					$dp = $dc = 0;

					$vedp_data = _workspace_votes($workspaces_id, false);
					$vedc_data = _workspace_votes($workspaces_id, true);

					$response['content']['chart_filled'] = true;
					if (empty($vedp_data) && empty($vedc_data)) {
						$response['content']['chart_filled'] = false;
					}
					$response['content']['chart_data'] = [
						[
							'label' => 'Live',
							'value' => $vedp_data,
						],
						[
							'label' => 'Completed',
							'value' => $vedc_data,
						],
					];
				}

				echo json_encode($response);
				exit();
				// pr($post, 1);

			}

		}
		// $this->render(DS . 'Projects' . DS . 'partials' . DS . 'objectives' . DS . 'objective_decision_chart');
	}

	public function objective_dfv() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$view = new View();
			$viewModel = $view->loadHelper('ViewModel');

			$response = [
				'success' => true,
				'msg' => '',
				'content' => null,
			];

			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;

				$project_id = null;
				if (isset($post['project_id']) && !empty($post['project_id'])) {
					$project_id = $post['project_id'];
				}

				// pr($post, 1);
				$view = new View($this, false);
				$view->viewPath = 'Projects/partials/objectives'; // Directory inside view directory to search for .ctp files
				$view->layout = false; // if you want to disable layout
				$view->set('project_id', $project_id);
				// $view->set ('data', $data); // set your variables for view here
				$html = $view->render('objective_dfv');
				echo json_encode($html);
				exit;
				//

			}

		}
		// $this->render(DS . 'Projects' . DS . 'partials' . DS . 'objectives' . DS . 'objective_decision_chart');
	}

	public function objective_dfv_decisions() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$view = new View();
			$viewModel = $view->loadHelper('ViewModel');

			$response = [
				'success' => true,
				'msg' => '',
				'content' => null,
			];

			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;

				$project_id = null;

				// pr($post, 1);
				$view = new View($this, false);
				$view->viewPath = 'Projects/partials/objectives'; // Directory inside view directory to search for .ctp files
				$view->layout = false; // if you want to disable layout

				if (isset($post['workspace_id']) && !empty($post['workspace_id'])) {
					$view->set('workspace_id', $post['workspace_id']);
				} else {
					if (isset($post['project_id']) && !empty($post['project_id'])) {
						$view->set('project_id', $post['project_id']);
					}
				}

				$html = $view->render('objective_dfv_decisions');
				echo json_encode($html);
				exit;

			}

		}
		// $this->render(DS . 'Projects' . DS . 'partials' . DS . 'objectives' . DS . 'objective_decision_chart');
	}

	public function objective_dfv_feedbacks() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$view = new View();
			$viewModel = $view->loadHelper('ViewModel');

			$response = [
				'success' => true,
				'msg' => '',
				'content' => null,
			];

			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;

				$project_id = null;

				// pr($post, 1);
				$view = new View($this, false);
				$view->viewPath = 'Projects/partials/objectives'; // Directory inside view directory to search for .ctp files
				$view->layout = false; // if you want to disable layout

				if (isset($post['workspace_id']) && !empty($post['workspace_id'])) {
					$view->set('workspace_id', $post['workspace_id']);
				} else {
					if (isset($post['project_id']) && !empty($post['project_id'])) {
						$view->set('project_id', $post['project_id']);
					}
				}

				$html = $view->render('objective_dfv_feedbacks');
				echo json_encode($html);
				exit;

			}

		}
		// $this->render(DS . 'Projects' . DS . 'partials' . DS . 'objectives' . DS . 'objective_decision_chart');
	}

	public function objective_dfv_votes() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$view = new View();
			$viewModel = $view->loadHelper('ViewModel');

			$response = [
				'success' => true,
				'msg' => '',
				'content' => null,
			];

			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;

				$project_id = null;

				// pr($post, 1);
				$view = new View($this, false);
				$view->viewPath = 'Projects/partials/objectives'; // Directory inside view directory to search for .ctp files
				$view->layout = false; // if you want to disable layout

				if (isset($post['workspace_id']) && !empty($post['workspace_id'])) {
					$view->set('workspace_id', $post['workspace_id']);
				} else {
					if (isset($post['project_id']) && !empty($post['project_id'])) {
						$view->set('project_id', $post['project_id']);
					}
				}

				$html = $view->render('objective_dfv_votes');
				echo json_encode($html);
				exit;

			}

		}
		// $this->render(DS . 'Projects' . DS . 'partials' . DS . 'objectives' . DS . 'objective_decision_chart');
	}

	public function dfv_decisions_list() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$view = new View();
			$viewModel = $view->loadHelper('ViewModel');

			$response = [
				'success' => true,
				'msg' => '',
				'content' => null,
			];

			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;

				$project_id = null;

				// pr($post, 1);
				$view = new View($this, false);
				$view->viewPath = 'Projects/partials/objectives'; // Directory inside view directory to search for .ctp files
				$view->layout = false; // if you want to disable layout

				if (isset($post['workspace_id']) && !empty($post['workspace_id'])) {
					$view->set('workspace_id', $post['workspace_id']);
				} else {
					if (isset($post['project_id']) && !empty($post['project_id'])) {
						$view->set('project_id', $post['project_id']);
					}
				}
				$view->set('type', $post['type']);

				$html = $view->render('objective_dfv_decisions');
				echo json_encode($html);
				exit;

			}

		}
	}

	public function dfv_feedbacks_list() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$view = new View();
			$viewModel = $view->loadHelper('ViewModel');

			$response = [
				'success' => true,
				'msg' => '',
				'content' => null,
			];

			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;

				$project_id = null;

				// pr($post, 1);
				$view = new View($this, false);
				$view->viewPath = 'Projects/partials/objectives'; // Directory inside view directory to search for .ctp files
				$view->layout = false; // if you want to disable layout

				if (isset($post['workspace_id']) && !empty($post['workspace_id'])) {
					$view->set('workspace_id', $post['workspace_id']);
				} else {
					if (isset($post['project_id']) && !empty($post['project_id'])) {
						$view->set('project_id', $post['project_id']);
					}
				}
				$view->set('type', $post['type']);

				$html = $view->render('objective_dfv_feedbacks');
				echo json_encode($html);
				exit;

			}

		}
	}

	public function dfv_votes_list() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$view = new View();
			$viewModel = $view->loadHelper('ViewModel');

			$response = [
				'success' => true,
				'msg' => '',
				'content' => null,
			];

			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;

				$project_id = null;

				// pr($post, 1);
				$view = new View($this, false);
				$view->viewPath = 'Projects/partials/objectives'; // Directory inside view directory to search for .ctp files
				$view->layout = false; // if you want to disable layout

				if (isset($post['workspace_id']) && !empty($post['workspace_id'])) {
					$view->set('workspace_id', $post['workspace_id']);
				} else {
					if (isset($post['project_id']) && !empty($post['project_id'])) {
						$view->set('project_id', $post['project_id']);
					}
				}
				$view->set('type', $post['type']);

				$html = $view->render('objective_dfv_votes');
				echo json_encode($html);
				exit;

			}

		}
	}

	public function objectives_filters() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			// $view = new View();
			// $viewModel = $view->loadHelper('ViewModel');

			$response = [
				'success' => true,
				'msg' => '',
				'content' => null,
			];

			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;

				$project_id = null;

				// pr($post, 1);
				$view = new View($this, false);
				$view->viewPath = 'Projects/partials/objectives'; // Directory inside view directory to search for .ctp files
				$view->layout = false; // if you want to disable layout

				if (isset($post['project_id']) && !empty($post['project_id'])) {
					$view->set('project_id', $post['project_id']);
				}
				if (isset($post['aligned_id']) && !empty($post['aligned_id'])) {
					$view->set('aligned_id', $post['aligned_id']);
				}
				if (isset($post['rag_status']) && !empty($post['rag_status'])) {
					$view->set('rag_status', $post['rag_status']);
				}
				if (isset($post['program_id']) && !empty($post['program_id'])) {
					$view->set('program_id', $post['program_id']);
				}

				$html = $view->render('objectives_filters');
				echo json_encode($html);
				exit;

			}

		}
	}

	public function project_cards() {
		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$response = [
				'success' => true,
				'msg' => '',
				'content' => null,
			];
			// App::import('Controller', 'Users');
			// $Users = new UsersController;
			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;

				$view = new View($this, false);
				$view->viewPath = 'Projects/partials/objectives'; // Directory inside view directory to search for .ctp files
				$view->layout = false; // if you want to disable layout

				$project_ids = null;
				$program_id = null;
				$conditions = $projectN = [];

				$rag_status = $allign = 0;
				if (isset($post['rag_status']) && !empty($post['rag_status'])) {
					// $conditions['Project.rag_current_status'] = $post['rag_status'];
					$rag_status = $post['rag_status'];
				}
				if ( isset($post['aligned_id']) && !empty($post['aligned_id'])) {
					$allign = $post['aligned_id'];
				}
				if (isset($post['program_id']) && !empty($post['program_id'])) {
					$program_id = $post['program_id'];
				}
				if (isset($post['project_id']) && !empty($post['project_id']) && $post['project_id'] != 'none') {
					$project_id = $post['project_id'];

					$projectIDs = [$post['project_id']];
					$project_ids = $this->objView->loadHelper('Permission')->all_my_projects(1, $rag_status, $allign, $projectIDs);
				}

				if ((isset($post['project_id']) && !empty($post['project_id']) && $post['project_id'] != 'none') && (( isset($post['aligned_id']) && !empty($post['aligned_id'])) || (isset($post['rag_status']) && !empty($post['rag_status']))) ) {
					$project_ids = [];
					if(isset($post['rag_status']) && !empty($post['rag_status'])){
						$rag_status = $post['rag_status'];
					}
					if(isset($post['aligned_id']) && !empty($post['aligned_id'])){
						$allign = $post['aligned_id'];
					}
					$projectIDs = [$post['project_id']];
					$project_ids = $this->objView->loadHelper('Permission')->all_my_projects(1, $rag_status, $allign, $projectIDs);
				}

				if ((!isset($post['project_id']) || empty($post['project_id']) || $post['project_id'] == 'none') && (!isset($program_id) || empty($program_id))) {
					$project_ids = $this->objView->loadHelper('Permission')->all_my_projects(1, $rag_status, $allign);
				}

				// Program selected
				if (isset($program_id) && !empty($program_id)) {
					$this->loadModel('ProjectProgram');
					$projgram_project_ids = $this->ProjectProgram->find('list', array('conditions' => array('ProjectProgram.program_id' => $program_id), 'fields' => array('ProjectProgram.id', 'ProjectProgram.project_id'), 'recursive' => -1));
					// pr($projgram_project_ids);

					if ((isset($projgram_project_ids) && !empty($projgram_project_ids)) ) {
						$projectIDs = array_values($projgram_project_ids);
						// if($_SERVER['REMOTE_ADDR'] == '111.93.41.194'){
						//
						// }
						// pr($projectIDs, 1);

						$project_ids = $this->objView->loadHelper('Permission')->all_my_projects(1, $rag_status, $allign, $projectIDs);
					}
				}
				$view->set('program_id', $program_id);

				$view->set('projects', $project_ids);
				$html = $view->render('project_cards');
				echo json_encode($html);
				exit;

			}

		}
	}

	public function ending_elements() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$view = new View();
			$viewModel = $view->loadHelper('ViewModel');

			$response = [
				'success' => true,
				'msg' => '',
				'content' => null,
			];

			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;

				$project_id = $dateStr = null;

				$data = null;

				if (isset($post['project_id']) && !empty($post['project_id'])) {
					$project_id = $post['project_id'];
				}

				if (isset($post['dateStr']) && !empty($post['dateStr'])) {
					$dateStr = $post['dateStr'];
				}

				$data = $viewModel->ending_elements($project_id, $dateStr);

				$view = new View($this, false);
				$view->viewPath = 'Projects/partials'; // Directory inside view directory to search for .ctp files
				$view->layout = false; // if you want to disable layout

				$view->set('project_id', $post['project_id']);

				$view->set('data', $data);

				$html = $view->render('ending_elements');

				echo json_encode($html);
				exit;

			}

		}
	}

	public function project_signoff($workspace_id = null) {
		// die('asdfsfdsfdf');
		if ($this->request->isAjax()) {
			$response = [
				'success' => false,
				'msg' => '',
				'content' => null,
			];
			$this->layout = 'ajax';
			$this->autoRender = false;

			$this->request->data['Project']['updated_user_id'] = $this->Session->read("Auth.User.id");
			if (isset($this->request->data['Project']['sign_off']) && $this->request->data['Project']['sign_off'] == 1) {
				$this->request->data['Project']['task_type'] = 'reopen';
				$projectTaskStauts = 'sign_off';

			} else if (isset($this->request->data['Project']['sign_off']) && $this->request->data['Project']['sign_off'] == 0) {
				$this->request->data['Project']['task_type'] = 'sign_off';
				$projectTaskStauts = 'reopen';
			}



			$this->request->data['Project']['create_activity'] = 1;
			$post = $this->request->data['Project'];
			// pr($post, 1);
			if (isset($post['id']) && !empty($post['id'])) {
				$id = $post['id'];
				$project_id = $post['id'];
				$this->Project->id = $id;

				if (!$this->Project->exists()) {
					throw new NotFoundException(__('Invalid detail'), 'error');
				}
				if(isset($this->request->data['Project']['sign_off']) && empty($this->request->data['Project']['sign_off']) && $this->request->data['Project']['sign_off'] == 0){
					// SIGNOFF DATE
					$post['sign_off_date'] = '';
				}
				// pr($post);die;

				if ($this->Project->save($post)) {

					$this->Project->updateAll(
						array("Project.create_activity" => 0),
						array("Project.id" => $id)
					);

					/* exits sign-off entry delete ****************/
						$del = array('project_id'=>$project_id);
						$signoffdata = $this->SignoffProject->find('first',array('conditions'=>$del ));
						if( isset($signoffdata) ){
							if( !empty(!empty($signoffdata['SignoffProject']['task_evidence'])) ){
								$folder_url = WWW_ROOT . PROJECT_SIGNOFF_PATH;
								unlink($folder_url.'/'.$signoffdata['SignoffProject']['task_evidence']);
							}
							$this->SignoffProject->deleteAll($del);
						}
						/********************************/


					//$this->Element->save($post)
					// Get Project Id with Element id; Update Project modified date
					//$this->update_project_modify($id);

					//============ Start Email Notification setting ================

					// ============== Start == annotation email when added by user ===============
					$projectdetail = $this->Project->findById($project_id);
					$project_name = '';
					if (isset($projectdetail['Project']['title']) && !empty($projectdetail['Project']['title'])) {
						$project_name = ucfirst(strip_tags($projectdetail['Project']['title']));
					}

					$this->projectSignoffEmail($project_name, $projectdetail['Project']['id'], $projectTaskStauts);

					// ============== End == annotation email when added by user ===============

					//============ End Email Notification setting =================

					/************** socket messages **************/
					if (SOCKET_MESSAGES) {
						$current_user_id = $this->Auth->user('id');
						App::import('Controller', 'Risks');
						$Risks = new RisksController;
						$project_all_users = $Risks->get_project_users($id, $current_user_id);
						if (isset($project_all_users) && !empty($project_all_users)) {
							if (($key = array_search($current_user_id, $project_all_users)) !== false) {
								unset($project_all_users[$key]);
							}
						}

						$s_open_users = $r_open_users = null;
						if (isset($project_all_users) && !empty($project_all_users)) {
							foreach ($project_all_users as $key => $value) {
								if (web_notify_setting($value, 'project', 'project_complete')) {
									$s_open_users[] = $value;
								}
								if (web_notify_setting($value, 'project', 'project_reopen')) {
									$r_open_users[] = $value;
								}
							}
						}
						$userDetail = get_user_data($current_user_id);
						$heading = (isset($post['sign_off']) && $post['sign_off'] == 1) ? 'Project complete' : 'Project re-opened';
						$content = [
							'notification' => [
								'type' => 'project_complete',
								'created_id' => $current_user_id,
								'project_id' => $projectdetail['Project']['id'],
								'creator_name' => $userDetail['UserDetail']['full_name'],
								'subject' => $heading,
								'heading' => 'Project: ' . strip_tags($project_name),
								'sub_heading' => '',
								'date_time' => timezoneTimeConvertor($this->user_id, date('Y-m-d g:iA'), $format = 'd M Y g:iA') . user_country($this->user_id),
							],
							//'received_users' => array_values($ele_users),
						];
						if (isset($post['sign_off']) && $post['sign_off'] == 1) {
							if (is_array($s_open_users)) {
								$content['received_users'] = array_values($s_open_users);
							}
						} else {
							if (is_array($r_open_users)) {
								$content['received_users'] = array_values($r_open_users);
							}
						}
						$response['content']['socket'] = $content;
					}
					/************** socket messages **************/

					$response['success'] = true;
					$response['msg'] = 'You have been signed off successfully.';
					// $response['content'] = [];
				} else {
					$response['msg'] = 'Signing off could not be completed. Please try again later.';
				}
				// $this->Element->_query(1);
			}
		}
		echo json_encode($response);
		exit();
	}

	public function get_progressing_workspace($project_id = null) {
		$projet_workspaces = $this->ProjectWorkspace->find("list", array("fields" => array("ProjectWorkspace.workspace_id"), "recursive" => -1, "conditions" => array("ProjectWorkspace.project_id" => $project_id)));
		$all_ws_by_projectid = $this->Workspace->find("count", array(
			"recursive" => -1,
			"conditions" => array(
				"Workspace.id" => $projet_workspaces,
				"Workspace.sign_off" => 0,
			),
		)
		);
		return $all_ws_by_projectid;
	}

	public function get_history() {
		$this->layout = false;
		$this->autoRender = false;
		if ($this->request->is('ajax') || $this->request->is('get')) {
			$id = isset($this->params['pass'][0]) && !empty($this->params['pass'][0]) ? $this->params['pass'][0] : '';

			$this->loadModel("ProjectActivity");
			$this->loadModel("UserDetail");
			$this->ProjectActivity->bindModel(array(
				"belongsTo" => array(
					"UserDetail" => array(
						"class" => "UserDetail",
						"foreignKey" => false,
						"conditions" => array("ProjectActivity.updated_user_id = UserDetail.user_id"),
					),
				),
			));
			$history_lists = $this->ProjectActivity->find("all", array("conditions" => array("ProjectActivity.project_id" => $id), 'order' => "ProjectActivity.id DESC", 'limit' => 15));
			//pr($history_lists,1);
			$this->set(compact("id", "history_lists"));
			return $history_lists;

			//$this->render("activity/update_history");

		}
	}
	public function more_history() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$model = 'ProjectActivity';

			if ($this->request->is('post') || $this->request->is('put')) {
				$this->pagination['page'] = 10;
				$this->loadModel("ProjectActivity");
				$this->loadModel("UserDetail");
				$this->ProjectActivity->bindModel(array(
					"belongsTo" => array(
						"UserDetail" => array(
							"class" => "UserDetail",
							"foreignKey" => false,
							"conditions" => array("ProjectActivity.updated_user_id = UserDetail.user_id"),
						),
					),
				));

				//
				$history_list = $this->ProjectActivity->find('count', ['conditions' => ["ProjectActivity.project_id" => $this->request->data['project_id']], 'order' => 'ProjectActivity.id DESC']);
				$t = ($history_list / 10);

				if (isset($history_list) && !empty($history_list) && ($this->params['named']['page'] <= ceil(($history_list / 10)))) {

					$paginator = array(
						'conditions' => array(
							$model . ".project_id" => $this->request->data['project_id'],
						),
						"joins" => array(
							array(
								'alias' => 'UserDetail',
								'table' => 'user_details',
								'type' => 'INNER',
								'conditions' => 'ProjectActivity.updated_user_id = UserDetail.user_id',
							),
						),
						'fields' => ['ProjectActivity.*', 'UserDetail.*'],
						'limit' => 10,
						"order" => $model . ".id DESC",
					);

					$this->paginate = $paginator;
					$this->set('history_lists', $this->paginate($model));

					$this->pagination['show_summary'] = true;
					$this->pagination['model'] = $model;
					$this->set('JeeraPaging', $this->pagination);
				} else {
					$history_lists = null;
					$this->set('history_lists', $history_lists);
				}
				// $this->set('history_lists', $history_lists);
				$this->set('project_id', $this->request->data['project_id']);
				$this->render('/Projects/activity/update_filter_history');
			}
		}
		return true;
	}

	public function delete_history($id = null) {
		$this->layout = false;
		$this->autoRender = false;
		$this->loadModel("Activity");
		if ($this->request->is('ajax')) {
			if ($this->Activity->delete($id)) {
				return json_encode(array("success" => true));
			} else {
				return json_encode(array("success" => false));
			}
		}
	}

	public function get_filter_history() {
		$this->layout = false;
		$this->autoRender = false;
		if ($this->request->is('ajax')) {

			$id = isset($this->params['named']['id']) && !empty($this->params['named']['id']) ? $this->params['named']['id'] : '';
			$seeactivityhistory = isset($this->params['named']['seeactivityhistory']) && !empty($this->params['named']['seeactivityhistory']) ? $this->params['named']['seeactivityhistory'] : '';

			$this->loadModel("ProjectActivity");
			$this->loadModel("UserDetail");
			//pr($this->params['named']);
			$this->ProjectActivity->bindModel(array(
				"belongsTo" => array(
					"UserDetail" => array(
						"class" => "UserDetail",
						"foreignKey" => false,
						"conditions" => array("ProjectActivity.updated_user_id = UserDetail.user_id"),
					),
				),
			));

			if ($seeactivityhistory == 'all') {
				$conditions = array("ProjectActivity.project_id" => $id);
			} else if ($seeactivityhistory == 'today') {
				$start = date('Y-m-d');
				$end = date('Y-m-d');
				$conditions = array(
					"ProjectActivity.project_id" => $id,
					'date(ProjectActivity.updated) BETWEEN ? AND ?' => array($start, $end),
				);
			} else if ($seeactivityhistory == 'last_7_day') {
				$end = date('Y-m-d');
				$start = date('Y-m-d', strtotime('-7 day'));
				$conditions = array(
					"ProjectActivity.project_id" => $id,
					'date(ProjectActivity.updated) BETWEEN ? AND ?' => array($start, $end),
				);
			} else if ($seeactivityhistory == 'this_month') {
				$end = date('Y-m-t');
				$start = date('Y-m-01');
				$conditions = array(
					"ProjectActivity.project_id" => $id,
					'date(ProjectActivity.updated) BETWEEN ? AND ?' => array($start, $end),
				);
			}

			$history_lists = $this->ProjectActivity->find("all", array(
				"conditions" => $conditions,
				'order' => "ProjectActivity.id DESC",
				"group" => array("ProjectActivity.updated"),
			)
			);
			//pr($history_lists);
			$this->set(compact("id", "seeactivityhistory", "history_lists"));
			$this->render("activity/update_filter_history");
		}
	}

	public function project_image_upload($project_id = null, $center = 1) {

		$this->layout = 'ajax';

		if ($this->request->isAjax()) {

			$data = array();

			if ($this->request->is('post') || $this->request->is('put')) {
				$folder_url = WWW_ROOT . PROJECT_IMAGE_PATH;
				$upload_object = $_FILES["image_file"];

			}

			if (isset($project_id) && !empty($project_id)) {

				$data = $this->Project->find('first', ['conditions' => ['Project.id' => $project_id], 'recursive' => -1]);
			}

			$this->set('project_id', $project_id);
			$this->set('center', $center);
			$this->set('data', $data);

			$this->render('/Projects/partials/project_image_upload');
		}

	}

	public function image_upload($project_id = null) {

		$this->layout = 'ajax';

		if ($this->request->isAjax()) {

			$data = array();
			$response = ['success' => false, 'content' => null, 'msg' => ''];

			if ($this->request->is('post') || $this->request->is('put')) {

				$folder_url = WWW_ROOT . PROJECT_IMAGE_PATH;
				// $upload_object = $_FILES["image_file"];
				if (isset($this->request->data['Project']['image_file']) && !empty($this->request->data['Project']['image_file'])) {
					$image = $this->request->data['Project']['image_file'];
					$image_detail = getimagesize($image['tmp_name']);

					$mt = microtime();
					$mt = explode(' ', $mt);

					$ex = explode('.', $image['name']);
					$nme = $ex[0];
					$ext = end($ex);
					$imagename = $nme . '_' . $project_id . '-' . $mt[1] . "." . $ext;
					$imagename = preg_replace('/\s+/', '_', $imagename);
					$imagename = str_replace(' ', '_', $imagename);

					list($width, $height) = getimagesize($image['tmp_name']);
					if (($width >= 750) && ($height >= 150)) {
						$resized = true; //$this->resizes($image, $folder_url);
						// die;
						if (copy($image['tmp_name'], $folder_url . $imagename)) {
							// if ($this->delete_image($project_id)) {}

							$this->Project->id = $project_id;
							if ($this->Project->saveField('image_file', $imagename)) {

								// Update Project modified date
								$this->Common->projectModified($project_id, $this->user_id);
								// Update Project image change date-time
								$this->Common->projectImageModified($project_id, $this->user_id);

								$response['success'] = true;
								$response['msg'] = "Success";
								$response['content'] = [];
							} else {
								$response['msg'] = "Error!!!";
							}
						}
					} else {
						$response['msg'] = "Image should be at least 750x150.";
					}
				}
			}

			echo json_encode($response);
			exit();
		}
	}

	public function remove_project_image($project_id = null) {

		$this->layout = 'ajax';

		if ($this->request->isAjax()) {

			$data = array();
			$response = ['success' => false];

			if ($this->request->is('post') || $this->request->is('put')) {

				if (isset($project_id) && !empty($project_id)) {
					if ($this->delete_image($project_id)) {
						$response['success'] = true;
					}
				}

			}

			echo json_encode($response);
			exit();
		}

	}

	public function delete_image($project_id) {
		if (isset($project_id) && !empty($project_id)) {

			$data = $this->Project->find('first', ['conditions' => ['Project.id' => $project_id], 'recursive' => -1]);
			$project_image = $data['Project']['image_file'];

			if (!empty($project_image) && file_exists(PROJECT_IMAGE_PATH . $project_image)) {
				$ex = explode('.', $project_image);
				$nme = $ex[0];
				$ext = end($ex);
				$thumb = $nme . '_780x150' . '.' . $ext;
				if (!empty($project_image) && file_exists(PROJECT_IMAGE_PATH . $thumb)) {
					unlink(PROJECT_IMAGE_PATH . $thumb);
				}
				unlink(PROJECT_IMAGE_PATH . $project_image);
			}
			$this->Project->id = $project_id;
			if ($this->Project->saveField('image_file', '')) {

				// Update Project modified date
				$this->Common->projectModified($project_id, $this->user_id);
			}
		}
		return true;
	}

	public function get_project_image($project_id = null) {

		$this->layout = 'ajax';

		if ($this->request->isAjax()) {

			$data = array();
			$html = null;

			if ($this->request->is('post') || $this->request->is('put')) {

				$data = $this->Project->find('first', ['conditions' => ['Project.id' => $project_id], 'recursive' => -1]);

				$view = new View($this, false);
				$view->viewPath = 'Projects/partials'; // Directory inside view directory to search for .ctp files
				$view->layout = false; // if you want to disable layout
				$view->set('data', $data);

				$html = $view->render('project_image');
			}

			echo json_encode($html);
			exit();
		}

	}

	public function project_center_image($project_id = null) {

		$this->layout = 'ajax';

		if ($this->request->isAjax()) {

			$data = array();
			$html = null;

			if ($this->request->is('post') || $this->request->is('put')) {

				$data = $this->Project->find('first', ['conditions' => ['Project.id' => $project_id], 'recursive' => -1]);

				$view = new View($this, false);
				$view->viewPath = 'Dashboards/partials'; // Directory inside view directory to search for .ctp files
				$view->layout = false; // if you want to disable layout
				$view->set('data', $data);

				$html = $view->render('project_center_image');
			}

			echo json_encode($html);
			exit();
		}

	}

	public function project_header_image($project_id = null) {

		$this->layout = 'ajax';

		if ($this->request->isAjax()) {

			$data = array();
			$html = null;

			if ($this->request->is('post') || $this->request->is('put')) {

				// $data = $this->Project->find('first', ['conditions' => ['Project.id' => $project_id], 'recursive' => -1]);

				$view = new View($this, false);
				$view->viewPath = 'Projects/partials'; // Directory inside view directory to search for .ctp files
				$view->layout = false; // if you want to disable layout
				$view->set('p_id', $project_id);

				$html = $view->render('project_header_image');
			}

			echo json_encode($html);
			exit();
		}

	}


	public function add_annotate($project_id = null) {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$data = $this->ProjectComment->find('all', [
				'conditions' => ['ProjectComment.project_id' => $project_id],
				'order' => ['ProjectComment.modified DESC'],
			]);

			$this->set('project_id', $project_id);
			$this->set('data', $data);

			$this->render(DS . 'Projects' . DS . 'partials' . DS . 'objectives' . DS . 'add_annotate');
		}
	}

	public function save_annotate($project_type = null) {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$response = [
				'success' => false,
				'msg' => '',
				'content' => null,
			];

			if ($this->request->is('post') || $this->request->is('put')) {



				$this->request->data['ProjectComment']['comments'] = trim(strip_tags($this->request->data['ProjectComment']['comments']));

				$this->ProjectComment->set($this->request->data);
				if ($this->ProjectComment->validates()) {

					$post = $this->request->data['ProjectComment'];

					$response['content'] = [$post['project_id']];

					$message = 'Project annotation created';
					if (isset($post['id']) && !empty($post['id'])) {
						$this->ProjectRagComment->id = $post['id'];
						$message = 'Project annotation updated';
					}



					if ($this->ProjectComment->save($post)) {


						$task_data = [
							'project_id' => $post['project_id'],
							//'element_type' => 'do_lists',
							'updated_user_id' => $this->user_id,
							'message' => $message,
							'updated' => date("Y-m-d H:i:s"),
						];
						$this->loadModel('ProjectActivity');
						$this->ProjectActivity->id = null;
						$this->ProjectActivity->save($task_data);

						/************** socket messages **************/
						if (SOCKET_MESSAGES) {
							if (!isset($post['id']) || empty($post['id'])) {
								$current_user_id = $this->user_id;
								$project_users = $this->get_project_users($post['project_id'], $current_user_id);
								if (isset($project_users) && !empty($project_users)) {
									if (($key = array_search($current_user_id, $project_users)) !== false) {
										unset($project_users[$key]);
									}
								}

								$userDetail = get_user_data($this->user_id);
								$content = [
									'notification' => [
										'type' => 'annotation_add',
										'created_id' => $this->user_id,
										'project_id' => $post['project_id'],
										'creator_name' => $userDetail['UserDetail']['full_name'],
										'subject' => 'Annotation added',
										'heading' => 'Project: ' . strip_tags(getFieldDetail('Project', $post['project_id'], 'title')),
										'sub_heading' => 'Annotation: ' . $post['comments'],
										'date_time' => timezoneTimeConvertor($this->user_id, date('Y-m-d g:iA'), $format = 'd M Y g:iA') . user_country($this->user_id),
									],
									'received_users' => array_values($project_users),
								];
								$response['content']['socket'] = $content;
							}
						}
						/************** socket messages **************/

						// ============== Start == annotation email when added by user ===============
						$projectdetail = $this->Project->findById($post['project_id']);
						$project_name = '';
						if (isset($projectdetail['Project']['title']) && !empty($projectdetail['Project']['title'])) {
							$project_name = ucfirst(strip_tags($projectdetail['Project']['title']));
						}
						$this->projectAnnotationEmail($project_name, $projectdetail['Project']['id'],$project_type);
						// ============== End == annotation email when added by user ===============

						$response['success'] = true;
					}
				} else {
					$response['content'] = $this->ProjectComment->validationErrors;
					// pr($this->ProjectComment->validationErrors,1);
				}
			}
			echo json_encode($response);
			exit;

		}
	}

	public function get_annotations($project_id = null) {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$response = [
				'success' => true,
				'msg' => '',
				'content' => null,
			];

			$data = $this->ProjectComment->find('all', [
				'conditions' => ['ProjectComment.project_id' => $project_id],
				'order' => ['ProjectComment.modified DESC'],
			]);

			$view = new View($this, false);
			$view->viewPath = 'Projects/partials/objectives'; // Directory inside view directory to search for .ctp files
			$view->layout = false; // if you want to disable layout
			$view->set('project_id', $project_id);
			$view->set('data', $data);

			$html = $view->render('get_annotations');
			// $this->render(DS . 'Projects' . DS . 'partials' . DS . 'objectives' . DS . 'get_annotations');

			echo json_encode($html);
			exit();
		}
	}

	public function get_annotation_count($project_id = null) {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$response = [
				'success' => true,
				'msg' => '',
				'content' => null,
			];

			$data = $this->ProjectComment->find('count', [
				'conditions' => ['ProjectComment.project_id' => $project_id],
				'order' => ['ProjectComment.created DESC'],
			]);

			echo json_encode($data);
			exit();

		}
	}

	public function delete_annotate($id = null,$project_id = null) {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$response = [
				'success' => true,
				'msg' => '',
				'content' => null,
			];

			$task_data = [
							'project_id' => $project_id,
							//'element_type' => 'do_lists',
							'updated_user_id' => $this->user_id,
							'message' => 'Project annotation deleted',
							'updated' => date("Y-m-d H:i:s"),
						];
			$this->loadModel('ProjectActivity');
			$this->ProjectActivity->id = null;
			$this->ProjectActivity->save($task_data);

			if ($this->ProjectComment->delete($id)) {

				echo json_encode($id);
			}

			exit();

		}
	}

	public function add_note($project_id = null) {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$this->loadModel('ProjectNote');
			$data = $this->ProjectNote->find('all', [
				'conditions' => ['ProjectNote.project_id' => $project_id],
				'order' => ['ProjectNote.modified DESC'],
			]);

			$this->set('project_id', $project_id);
			$this->set('data', $data);

			$this->render(DS . 'Projects' . DS . 'partials' . DS . 'objectives' . DS . 'add_note');
		}
	}

	public function save_note($project_type = null) {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$response = [
				'success' => false,
				'msg' => '',
				'content' => null,
			];

			if ($this->request->is('post') || $this->request->is('put')) {

				$this->loadModel('ProjectNote');

				$this->request->data['ProjectNote']['note'] = trim(($this->request->data['ProjectNote']['note']));

				$this->ProjectNote->set($this->request->data);
				if ($this->ProjectNote->validates()) {

					$post = $this->request->data['ProjectNote'];

					$response['content'] = [$post['project_id']];

					$message = 'Project note created';
					if (isset($post['id']) && !empty($post['id'])) {
						$this->ProjectNote->id = $post['id'];
						$message = 'Project note updated';
					}



					if ($this->ProjectNote->save($post)) {


						$task_data = [
							'project_id' => $post['project_id'],
							//'element_type' => 'do_lists',
							'updated_user_id' => $this->user_id,
							'message' => $message,
							'updated' => date("Y-m-d H:i:s"),
						];
						$this->loadModel('ProjectActivity');
						$this->ProjectActivity->id = null;
						$this->ProjectActivity->save($task_data);

						/************** socket messages **************/
/* 						if (SOCKET_MESSAGES) {
							if (!isset($post['id']) || empty($post['id'])) {
								$current_user_id = $this->user_id;
								$project_users = $this->get_project_users($post['project_id'], $current_user_id);
								if (isset($project_users) && !empty($project_users)) {
									if (($key = array_search($current_user_id, $project_users)) !== false) {
										unset($project_users[$key]);
									}
								}

								$userDetail = get_user_data($this->user_id);
								$content = [
									'notification' => [
										'type' => 'note_add',
										'created_id' => $this->user_id,
										'project_id' => $post['project_id'],
										'creator_name' => $userDetail['UserDetail']['full_name'],
										'subject' => 'Note added',
										'heading' => 'Project: ' . strip_tags(getFieldDetail('Project', $post['project_id'], 'title')),
										'sub_heading' => 'Note: ' . $post['comments'],
										'date_time' => timezoneTimeConvertor($this->user_id, date('Y-m-d g:iA'), $format = 'd M Y g:iA') . user_country($this->user_id),
									],
									'received_users' => array_values($project_users),
								];
								$response['content']['socket'] = $content;
							}
						} */
						/************** socket messages **************/

						// ============== Start == annotation email when added by user ===============
						$projectdetail = $this->Project->findById($post['project_id']);
						$project_name = '';
						if (isset($projectdetail['Project']['title']) && !empty($projectdetail['Project']['title'])) {
							$project_name = ucfirst(strip_tags($projectdetail['Project']['title']));
						}
						//$this->projectNoteEmail($project_name, $projectdetail['Project']['id'],$project_type);
						// ============== End == annotation email when added by user ===============

						$response['success'] = true;
					}
				} else {
					$response['content'] = $this->ProjectNote->validationErrors;
					// pr($this->ProjectComment->validationErrors,1);
				}
			}
			echo json_encode($response);
			exit;

		}
	}

	public function get_notes($project_id = null) {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$response = [
				'success' => true,
				'msg' => '',
				'content' => null,
			];

			$this->loadModel('ProjectNote');

			$data = $this->ProjectNote->find('all', [
				'conditions' => ['ProjectNote.project_id' => $project_id],
				'order' => ['ProjectNote.modified DESC'],
			]);

			$view = new View($this, false);
			$view->viewPath = 'Projects/partials/objectives'; // Directory inside view directory to search for .ctp files
			$view->layout = false; // if you want to disable layout
			$view->set('project_id', $project_id);
			$view->set('data', $data);

			$html = $view->render('get_notes');
			// $this->render(DS . 'Projects' . DS . 'partials' . DS . 'objectives' . DS . 'get_annotations');

			echo json_encode($html);
			exit();
		}
	}

	public function get_note_count($project_id = null) {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$response = [
				'success' => true,
				'msg' => '',
				'content' => null,
			];

			$this->loadModel('ProjectNote');

			$data = $this->ProjectNote->find('count', [
				'conditions' => ['ProjectNote.project_id' => $project_id],
				'order' => ['ProjectNote.created DESC'],
			]);

			echo json_encode($data);
			exit();

		}
	}

	public function delete_note($id = null,$project_id = null) {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$response = [
				'success' => true,
				'msg' => '',
				'content' => null,
			];

			$task_data = [
							'project_id' => $project_id,
							//'element_type' => 'do_lists',
							'updated_user_id' => $this->user_id,
							'message' => 'Project note deleted',
							'updated' => date("Y-m-d H:i:s"),
						];
			$this->loadModel('ProjectActivity');
			$this->ProjectActivity->id = null;
			$this->ProjectActivity->save($task_data);

			$this->loadModel('ProjectNote');

			if ($this->ProjectNote->delete($id)) {

				echo json_encode($id);
			}

			exit();

		}
	}


	public function el_rag() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$response = [
				'success' => false,
				'msg' => '',
				'content' => null,
			];

			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;

				$project_id = (isset($post['project_id']) && !empty($post['project_id'])) ? $post['project_id'] : null;

				$this->set('project_id', $project_id);

			}

		}

		$this->render(DS . 'Projects' . DS . 'partials' . DS . 'el_rag');
	}


	public function apply_user_filter($project_id, $type, $selected, $match_all, $perm_users = array()) {
		$result = array();
		$selectedUsersSQL = '';
		if(!empty($perm_users)) {
			$selectedUsersSQL = implode(',',$perm_users);
		}
		if($type == 'tag') {
			$this->loadModel('Tag');

			if($selected != '') {
				$termsArr = array_map('trim', explode(',', $selected));
				$selectedTags = implode("','",$termsArr);
				$tagCnt = count($termsArr);
				$matchAllCond = "";
				if($match_all) {
					$matchAllCond = " HAVING COUNT(DISTINCT tag) = $tagCnt ";
				}
				$findQuery = "SELECT
									tagged_user_id, user_details.first_name, user_details.last_name
								FROM tags
								LEFT JOIN users ON tags.tagged_user_id = users.id
								LEFT JOIN user_details ON users.id = user_details.user_id
								WHERE tags.user_id = $this->user_id AND tagged_user_id IN(
									SELECT tagged_user_id
									FROM `tags`
									WHERE 1 AND tag IN ('$selectedTags') AND user_id = $this->user_id AND tagged_user_id IN ($selectedUsersSQL)
									GROUP BY tagged_user_id
									$matchAllCond
								)
								GROUP BY tagged_user_id ORDER BY user_details.first_name ASC";

				$tagUsers = $this->Tag->query($findQuery);

				if (isset($tagUsers) && !empty($tagUsers)) {
					foreach($tagUsers as $k => $v) {
						$result[$v['tags']['tagged_user_id']] = $v['user_details']['first_name'] . ' '. $v['user_details']['last_name'];
					}
				}
			}
		} else if($type == 'skill') {
			$this->loadModel('UserSkill');

			if($selected != '') {
				$termsArr = array_map('trim', explode(',', $selected));
				$selectedSkills = implode("','",$termsArr);
				$skillCnt = count($termsArr);
				$matchAllCond = "";
				if($match_all) {
					$matchAllCond = " HAVING COUNT(DISTINCT skill_id) = $skillCnt ";
				}
				echo $findQuery = "SELECT
									user_skills.user_id, user_details.first_name, user_details.last_name
								FROM user_skills
								LEFT JOIN users ON user_skills.user_id = users.id
								LEFT JOIN user_details ON users.id = user_details.user_id
								WHERE user_skills.user_id IN(
									SELECT user_skills.user_id
									FROM `user_skills`
									WHERE 1 AND skill_id IN ('$selectedSkills') AND user_skills.user_id IN ($selectedUsersSQL)
									GROUP BY user_skills.user_id
									$matchAllCond
								)
								GROUP BY user_skills.user_id ORDER BY user_details.first_name ASC";

				$skillUsers = $this->UserSkill->query($findQuery);

				if (isset($skillUsers) && !empty($skillUsers)) {
					foreach($skillUsers as $k => $v) {
						$result[$v['user_skills']['user_id']] = $v['user_details']['first_name'] . ' '. $v['user_details']['last_name'];
					}
				}
			}
		}
		return $result;
	}

	public function getQuickShareUserList($project_id = null) {
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			$this->autoRender = false;

			$viewData['project_id'] = $project_id;
			$viewData['perm_users'] = array();
			$items_per_page = $this->quick_share_paging;

			if ($this->request->isAjax()) {
				$type = $this->request->data['type'];
				//$default_users = (isset($this->request->data['perm_users_all']) && !empty($this->request->data['perm_users_all'])) ? $this->request->data['perm_users_all'] : array();
				$selected = (isset($this->request->data['selected']) && !empty($this->request->data['selected'])) ? trim($this->request->data['selected']) : '';
				$match_all = (isset($this->request->data['is_match_all']) && trim($this->request->data['is_match_all']) != '') ? $this->request->data['is_match_all'] : false;
				$offset = (isset($this->request->data['page']) && !empty(trim($this->request->data['page']))) ? trim($this->request->data['page']) : 0;

				//$perm_users = get_selected_users($this->user_id, $project_id );
				//$perm_users_id = $default_users;
				/*if(!empty($perm_users)) {
					$perm_users_id = array_keys($perm_users);
				}*/

				$perm_users_sliced = array();

				if( 1 == 2 && $selected != '') {
					if($type == 'tag' || $type == 'skill') {
						$selectedUsers = $this->apply_user_filter($project_id, $type, $selected, $match_all, $perm_users_id);
						$perm_users = $selectedUsers;
					} else if($type == 'text') {
						$new_perm_users = [];
						if(!empty($perm_users)) {
							foreach($perm_users as $k => $u) {
								if (stripos($u, $selected) !== false) {
									$new_perm_users[$k] = $u;
								}
							}
							$perm_users = $new_perm_users;
						}
					}
				} else {
					$perm_users = get_selected_users_with_paging($this->user_id, $project_id, false, $type, $selected, $match_all, $items_per_page, $offset, 1);
					if(!empty($perm_users)) {
						$perm_users_sliced = array_slice($perm_users, $offset, $this->quick_share_paging, true);
					}
				}

				$viewData['project_id'] = $project_id;
				$viewData['perm_users'] = $perm_users_sliced;
				$viewData['tot_perm_users'] = count($perm_users);
				$viewData['type'] = $type;
			}
			$this->set('viewData', $viewData);
			$this->render('/Projects/partials/quick_share_user_list', 'ajax');
		}
	}

	public function quick_share($project_id = null) {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$perm_users = get_selected_users($this->user_id, $project_id);
			//$perm_users_sliced = get_selected_users_with_paging($this->user_id, $project_id, false, $this->quick_share_paging);
			$perm_users_sliced = array_slice($perm_users, 0, $this->quick_share_paging, true);

			$perm_users_ids = [];
			if(!empty($perm_users)) {
				$perm_users_ids = array_keys($perm_users);
			}
			$viewData = null;

			/*Code for Show all tags and skills on pop up*/
			$this->loadModel('Tag');
			$this->loadModel('Skill');
			$this->loadModel('subject');
			$this->loadModel('Domain');
			$loggedInUserId = $this->user_id;
			$tags = $this->Tag->find('list', ['conditions' => ['user_id' => $loggedInUserId],'fields' => array('Tag.tag'), 'order' => ['tag ASC'], 'group' => array('tag')]);

			$sk = [];
			if (isset($tags) && !empty($tags)) {
				$k = 0;
				foreach ($tags as $key => $value) {
					$sk[$k]['label'] = $value;
					$sk[$k]['value'] = $value;
					$k++;
				}
			}
			$viewData['tags'] = $sk;
			$view = new View();
			$commonHelper = $view->loadHelper('Common');

			$skillsData = $commonHelper->get_skill_of_users($perm_users_ids);
			$skills = [];
			if (isset($skillsData) && !empty($skillsData)) {
				$k = 0;
				foreach ($skillsData as $key => $value) {
					$skills[$k]['label'] = $value['skills']['title'];
					$skills[$k]['value'] = $value['user_skills']['skill_id'];
					$k++;
				}
			}
			$viewData['skills'] = $skills;

			$subjectsData = $commonHelper->get_subject_of_users($perm_users_ids);
			$subjects = [];
			if (isset($subjectsData) && !empty($subjectsData)) {
				$k = 0;
				foreach ($subjectsData as $key => $value) {
					$subjects[$k]['label'] = $value['subjects']['title'];
					$subjects[$k]['value'] = $value['user_subjects']['subject_id'];
					$k++;
				}
			}
			$viewData['subjects'] = $subjects;

			$domainsData = $commonHelper->get_domain_of_users($perm_users_ids);
			$domains = [];
			if (isset($domainsData) && !empty($domainsData)) {
				$k = 0;
				foreach ($domainsData as $key => $value) {
					$domains[$k]['label'] = $value['knowledge_domains']['title'];
					$domains[$k]['value'] = $value['user_domains']['domain_id'];
					$k++;
				}
			}
			$viewData['domains'] = $domains;

			$viewData['perm_users'] = $perm_users_sliced;
			$viewData['perm_users_all'] = array_keys($perm_users);
			$viewData['project_id'] = $project_id;

			$this->set($viewData);
			$this->render('/Projects/partials/quick_share');

		}
	}

	public function save_quick_share($project_id = null) {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$response = [
				'success' => false,
				'content' => null,
				'socket_newuser' => null,
				'socket_sharing' => null,
			];

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				// pr($post, 1);

				$userProjectId = project_upid($project_id);
				$userOwnerId = project_owner($project_id);

				$parent_id = 0;
				if ($userOwnerId != $this->user_id) {
					$parent = $this->ProjectPermission->find('first', [
						'conditions' => ['ProjectPermission.user_project_id' => $userProjectId, 'ProjectPermission.owner_id' => $userOwnerId, 'ProjectPermission.user_id' => $this->user_id],

					]);
					$parent_id = (isset($parent) && !empty($parent)) ? $parent['ProjectPermission']['id'] : 0;

				}

				$same_level_permit = [
					'user_id' => $post['Share']['user_id'],
					'share_by_id' => $this->user_id,
					'owner_id' => $userOwnerId,
					'user_project_id' => $userProjectId,
					'parent_id' => $parent_id,
					'share_permission' => 1,
					'project_level' => 1,
					'permit_read' => 1,
					'permit_add' => 1,
					'permit_edit' => 1,
					'permit_delete' => 1,
				];
				if ($this->ProjectPermission->save($same_level_permit)) {
					$response['success'] = true;

					// insert into project activity
					$project_data = [
						'project_id' => $project_id,
						'updated_user_id' => $this->user_id,
						'message' => 'Project shared',
						'updated' => date("Y-m-d H:i:s"),
					];
					$this->loadModel('ProjectActivity');
					$this->ProjectActivity->save($project_data);

					$this->Users->userConnections($post['Share']['user_id'], $project_id);

					$response['content']['insert_id'] = $this->ProjectPermission->getLastInsertId();
					$this->Common->share_project_email($post['Share']['user_id'], $project_id);
					$this->Common->getProjectAllUser($project_id, $post['Share']['user_id']);

					/************** socket messages (new project member) **************/
					if (SOCKET_MESSAGES) {
						$current_user_id = $this->user_id;
						App::import('Controller', 'Risks');
						$Risks = new RisksController;
						$project_all_users = $Risks->get_project_users($project_id, $current_user_id);
						if (isset($project_all_users) && !empty($project_all_users)) {
							if (($key = array_search($current_user_id, $project_all_users)) !== false) {
								unset($project_all_users[$key]);
							}
						}
						if (isset($project_all_users) && !empty($project_all_users)) {
							if (($key = array_search($post['Share']['user_id'], $project_all_users)) !== false) {
								unset($project_all_users[$key]);
							}
						}
						$open_users = null;
						if (isset($project_all_users) && !empty($project_all_users)) {
							foreach ($project_all_users as $key1 => $value1) {
								if (web_notify_setting($value1, 'project', 'project_new_member')) {
									$open_users[] = $value1;
								}
							}
						}
						$heading = 'Sharer';
						$p_permission = $this->Common->project_permission_details($project_id, $post['Share']['user_id']);
						if (isset($p_permission) && !empty($p_permission)) {
							if ($p_permission['ProjectPermission']['project_level'] > 0) {
								$heading = 'Owner';
							}
						}
						$userDetail = get_user_data($current_user_id);
						$sharedUserDetail = get_user_data($post['Share']['user_id']);
						$content = [
							'socket' => [
								'notification' => [
									'type' => 'new_project_member',
									'created_id' => $current_user_id,
									'project_id' => $project_id,
									'creator_name' => $userDetail['UserDetail']['full_name'],
									'subject' => 'New project member',
									'heading' => 'Member: ' . $sharedUserDetail['UserDetail']['full_name'] . '<br />Permission: ' . $heading,
									'sub_heading' => 'Project: ' . strip_tags(getFieldDetail('Project', $project_id, 'title')),
									'date_time' => timezoneTimeConvertor($this->user_id, date('Y-m-d g:iA'), $format = 'd M Y g:iA') . user_country($this->user_id),
								],
								// 'received_users' => [$post['Share']['user_id']],
							],
						];
						if (is_array($open_users)) {
							$content['socket']['received_users'] = array_values($open_users);
						}

						$response['socket_newuser'] = $content;
					}
					/************** socket messages (new project member) **************/

					/************** socket messages (project sharing) **************/
					if (SOCKET_MESSAGES) {
						$current_user_id = $this->user_id;
						$heading = 'Sharer';
						$p_permission = $this->Common->project_permission_details($project_id, $post['Share']['user_id']);
						if (isset($p_permission) && !empty($p_permission)) {
							if ($p_permission['ProjectPermission']['project_level'] > 0) {
								$heading = 'Owner';
							}
						}
						$send_notification = false;
						if (web_notify_setting($post['Share']['user_id'], 'project', 'project_sharing')) {
							$send_notification = true;
						}
						$userDetail = get_user_data($current_user_id);
						$sharedUserDetail = get_user_data($post['Share']['user_id']);
						$content = [
							'socket' => [
								'notification' => [
									'type' => 'project_sharing',
									'created_id' => $current_user_id,
									'project_id' => $project_id,
									'creator_name' => $userDetail['UserDetail']['full_name'],
									'subject' => 'Project sharing',
									'heading' => 'Permission: ' . $heading,
									'sub_heading' => 'Project: ' . strip_tags(getFieldDetail('Project', $project_id, 'title')),
									'date_time' => timezoneTimeConvertor($this->user_id, date('Y-m-d g:iA'), $format = 'd M Y g:iA') . user_country($this->user_id),
								],
							],
						];
						if ($send_notification) {
							$content['socket']['received_users'] = [$post['Share']['user_id']];
						}

						$response['socket_sharing'] = $content;
					}
					/************** socket messages (project sharing) **************/

				}

			}
			echo json_encode($response);
			exit;
		}
	}

	public function get_activities() {

		$this->layout = 'ajax';

		if ($this->request->isAjax()) {

			$data = array();
			$html = null;

			if ($this->request->is('post') || $this->request->is('put')) {

				$view = new View($this, false);
				$view->viewPath = 'Projects/activity'; // Directory inside view directory to search for .ctp files
				$view->layout = false; // if you want to disable layout

				$view->set('project_id', $this->request->data['project_id']);

				$html = $view->render('task_activity');
			}

			echo json_encode($html);
			exit();
		}
	}

	public function sendRagStatusUpdateEmail($ragStatus_new = null, $project_name = null, $project_id = null, $ragstatustype = null) {

		$ragstatus = '';
		$projectname = '';
		$projectname = $project_name;

		$view = new View();
		$commonHelper = $view->loadHelper('Common');
		$ragCLS = $commonHelper->getRAG($project_id);
		if (isset($ragCLS['rag_color']) && $ragCLS['rag_color'] == 'bg-yellow') {
			$ragstatus = 'Amber';
		} else if (isset($ragCLS['rag_color']) && $ragCLS['rag_color'] == 'bg-red') {
			$ragstatus = 'Red';
		} else {
			$ragstatus = 'Green';
		}

		$ownerdetails = $commonHelper->ProjectOwner($project_id, $this->Session->read('Auth.User.id'));
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
		if (isset($all_owner) && !empty($all_owner)) {
			if (($key = array_search($this->Session->read('Auth.User.id'), $all_owner)) !== false) {
				unset($all_owner[$key]);
			}
		}

		$pageAction = SITEURL . 'projects/objectives/' . $project_id;
		if (isset($all_owner) && !empty($all_owner)) {
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
					$owner_name = 'N/A';
					if (!isset($usersDetails['UserDetail']['first_name']) && empty($usersDetails['UserDetail']['last_name'])) {
						$owner_name = 'N/A';
					} else {
						$fullFName = '';
						$fullLName = '';
						if (!empty($usersDetails['UserDetail']['first_name'])) {
							$fullFName = $usersDetails['UserDetail']['first_name'] . ' ';
						}
						if (!empty($usersDetails['UserDetail']['last_name'])) {
							$fullLName = $usersDetails['UserDetail']['last_name'];
						}
						$owner_name = $fullFName . $fullLName;
					}
					//$owner_name = $usersDetails['UserDetail']['first_name'] . ' ' . $usersDetails['UserDetail']['last_name'];
					//manual or rule-based
					$email = new CakeEmail();
					$email->config('Smtp');
					$dasAnnotate = $this->EmailNotification->find('first', ['conditions' => ['notification_type' => 'dashboard', 'personlization' => 'project_rag', 'user_id' => $valData]]);

					if ((!isset($dasAnnotate['EmailNotification']['email']) || $dasAnnotate['EmailNotification']['email'] == 1) && (!isset($usersDetails['User']['email_notification']) || $usersDetails['User']['email_notification'] == 1)) {

						$email->from(array(ADMIN_FROM_EMAIL => SITENAME));
						$email->to($usersDetails['User']['email']);
						$email->subject(SITENAME . ': RAG update');
						$email->template('rag_status_update_email');
						$email->emailFormat('html');
						$email->viewVars(array('ragstatus' => $ragstatus, 'project_name' => $projectname, 'owner_name' => $owner_name, 'ragstatustype' => $ragstatustype,'open_page'=>$pageAction));
						$email->send();
					}

				}

			}
		}
	}
	
	
	public function testEmail(){
		$email = new CakeEmail();
			$email->config('Smtp');
			$email->from(array(ADMIN_FROM_EMAIL => SITENAME));
			//$email->to('pawansharma13@gmail.com');
			//$email->to('abhishek.maheshwari@dotsquares.com');
			$email->subject(SITENAME . ': RAG update');
			$email->template('video');
			$email->emailFormat('html');
			$email->viewVars();
			$email->send();
		exit;
	}

	public function projectAnnotationEmail($project_name = null, $project_id = null,$project_type = null) {

		$projectname = '';
		$projectname = $project_name;

		$view = new View();
		$commonHelper = $view->loadHelper('Common');

		$ownerdetails = $commonHelper->ProjectOwner($project_id, $this->Session->read('Auth.User.id'));

		$deletedUser = $this->User->findById($this->Session->read('Auth.User.id'));
		$loggedInUser = $deletedUser['UserDetail']['first_name'] . ' ' . $deletedUser['UserDetail']['last_name'];

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
		if(isset($project_type) && !empty($project_type)) {
			$projectAction = SITEURL . 'projects/index/'.$project_id.'/annotate';
		}else{
			$projectAction = SITEURL . 'projects/objectives';
		}

			$projectAction = SITEURL . 'projects/index/'.$project_id.'/annotate';

		if (isset($all_owner) && !empty($all_owner)) {
			if (($key = array_search($this->Session->read('Auth.User.id'), $all_owner)) !== false) {
				unset($all_owner[$key]);
			}
		}
		if (isset($all_owner) && !empty($all_owner)) {
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
					$owner_name = 'N/A';
					if (!isset($usersDetails['UserDetail']['first_name']) && empty($usersDetails['UserDetail']['last_name'])) {
						$owner_name = 'N/A';
					} else {
						$fullFName = '';
						$fullLName = '';
						if (!empty($usersDetails['UserDetail']['first_name'])) {
							$fullFName = $usersDetails['UserDetail']['first_name'] . ' ';
						}
						if (!empty($usersDetails['UserDetail']['last_name'])) {
							$fullLName = $usersDetails['UserDetail']['last_name'];
						}
						$owner_name = $fullFName . $fullLName;
					}
					//$owner_name = $usersDetails['UserDetail']['first_name'] . ' ' . $usersDetails['UserDetail']['last_name'];

					$dasAnnotate = $this->EmailNotification->find('first', ['conditions' => ['notification_type' => 'dashboard', 'personlization' => 'annotation_add', 'user_id' => $valData]]);

					if ((!isset($dasAnnotate['EmailNotification']['email']) || $dasAnnotate['EmailNotification']['email'] == 1) && (!isset($usersDetails['User']['email_notification']) || $usersDetails['User']['email_notification'] == 1)) {

						$email = new CakeEmail();
						$email->config('Smtp');
						$email->from(array(ADMIN_FROM_EMAIL => SITENAME));
						$email->to($usersDetails['User']['email']);
						$email->subject(SITENAME . ': Annotation added');
						$email->template('project_annotation_email');
						$email->emailFormat('html');
						$email->viewVars(array('project_name' => $projectname, 'owner_name' => $owner_name, 'addedby' => $loggedInUser,'open_page'=>$projectAction,'project_type'=>$project_type));
						$email->send();

					}

				}
			}
		}
	}


	public function projectNoteEmail($project_name = null, $project_id = null,$project_type = null) {

		$projectname = '';
		$projectname = $project_name;

		$view = new View();
		$commonHelper = $view->loadHelper('Common');

		$ownerdetails = $commonHelper->ProjectOwner($project_id, $this->Session->read('Auth.User.id'));

		$deletedUser = $this->User->findById($this->Session->read('Auth.User.id'));
		$loggedInUser = $deletedUser['UserDetail']['first_name'] . ' ' . $deletedUser['UserDetail']['last_name'];

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
		$projectAction = SITEURL . 'projects/objectives';
		if (isset($all_owner) && !empty($all_owner)) {
			if (($key = array_search($this->Session->read('Auth.User.id'), $all_owner)) !== false) {
				unset($all_owner[$key]);
			}
		}
		if (isset($all_owner) && !empty($all_owner)) {
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
					$owner_name = 'N/A';
					if (!isset($usersDetails['UserDetail']['first_name']) && empty($usersDetails['UserDetail']['last_name'])) {
						$owner_name = 'N/A';
					} else {
						$fullFName = '';
						$fullLName = '';
						if (!empty($usersDetails['UserDetail']['first_name'])) {
							$fullFName = $usersDetails['UserDetail']['first_name'] . ' ';
						}
						if (!empty($usersDetails['UserDetail']['last_name'])) {
							$fullLName = $usersDetails['UserDetail']['last_name'];
						}
						$owner_name = $fullFName . $fullLName;
					}
					//$owner_name = $usersDetails['UserDetail']['first_name'] . ' ' . $usersDetails['UserDetail']['last_name'];

					$dasAnnotate = $this->EmailNotification->find('first', ['conditions' => ['notification_type' => 'dashboard', 'personlization' => 'note_add', 'user_id' => $valData]]);

					if ((!isset($dasAnnotate['EmailNotification']['email']) || $dasAnnotate['EmailNotification']['email'] == 1) && (!isset($usersDetails['User']['email_notification']) || $usersDetails['User']['email_notification'] == 1)) {

						$email = new CakeEmail();
						$email->config('Smtp');
						$email->from(array(ADMIN_FROM_EMAIL => SITENAME));
						$email->to($usersDetails['User']['email']);
						$email->subject(SITENAME . ': Note added');
						$email->template('project_note_email');
						$email->emailFormat('html');
						$email->viewVars(array('project_name' => $projectname, 'owner_name' => $owner_name, 'addedby' => $loggedInUser,'open_page'=>$projectAction,'project_type'=>$project_type));
						$email->send();

					}

				}
			}
		}
	}

	public function workspaceDeleteEmail($workspaceName = null, $project_id = null, $all_owner = null, $projectName = null) {

		$view = new View();
		$commonHelper = $view->loadHelper('Common');
		$all_owner = array_unique(array_filter($all_owner));
		$projectAction = SITEURL . 'projects/index/' . $project_id;
		if (isset($all_owner) && !empty($all_owner)) {
			if (($key = array_search($this->Session->read('Auth.User.id'), $all_owner)) !== false) {
				unset($all_owner[$key]);
			}
		}
		if (isset($all_owner) && !empty($all_owner)) {

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
					$owner_name = 'N/A';
					if (!isset($usersDetails['UserDetail']['first_name']) && empty($usersDetails['UserDetail']['last_name'])) {
						$owner_name = 'N/A';
					} else {
						$fullFName = '';
						$fullLName = '';
						if (!empty($usersDetails['UserDetail']['first_name'])) {
							$fullFName = $usersDetails['UserDetail']['first_name'] . ' ';
						}
						if (!empty($usersDetails['UserDetail']['last_name'])) {
							$fullLName = $usersDetails['UserDetail']['last_name'];
						}
						$owner_name = $fullFName . $fullLName;
					}
					// $owner_name = $usersDetails['UserDetail']['first_name'] . ' ' . $usersDetails['UserDetail']['last_name'];

					$deletedUser = $this->User->findById($this->Session->read('Auth.User.id'));
					$loggedInUser = $deletedUser['UserDetail']['first_name'] . ' ' . $deletedUser['UserDetail']['last_name'];

					$dasAnnotate = $this->EmailNotification->find('first', ['conditions' => ['notification_type' => 'project', 'personlization' => 'workspace_deleted', 'user_id' => $valData]]);

					if ((!isset($dasAnnotate['EmailNotification']['email']) || $dasAnnotate['EmailNotification']['email'] == 1) && (!isset($usersDetails['User']['email_notification']) || $usersDetails['User']['email_notification'] == 1)) {

						$email = new CakeEmail();
						$email->config('Smtp');
						$email->from(array(ADMIN_FROM_EMAIL => SITENAME));
						$email->to($usersDetails['User']['email']);
						$email->subject(SITENAME . ': Workspace deleted');
						$email->template('workspace_delete_email');
						$email->emailFormat('html');
						$email->viewVars(array('workspaceName' => $workspaceName, 'owner_name' => $owner_name, 'deletedby' => $loggedInUser, 'projectName' => $projectName,'open_page'=>$projectAction));
						$email->send();

					}

				}
			}

		}
		//die;

	}

	public function projectSignoffEmail($project_name = null, $project_id = null, $projectStatus = null) {

		$projectname = '';
		$projectname = $project_name;

		App::import("Model", "User");
		$this->User = new User();

		$view = new View();
		$commonHelper = $view->loadHelper('Common');

		$ownerdetails = $commonHelper->ProjectOwner($project_id, $this->Session->read('Auth.User.id'));

		$deletedUser = $this->User->findById($this->Session->read('Auth.User.id'));
		$loggedInUser = $deletedUser['UserDetail']['first_name'] . ' ' . $deletedUser['UserDetail']['last_name'];

		/* $data2 = array();
			$data1[] = $ownerdetails['UserProject']['user_id'];
			$data2 = participants_group_owner($project_id);
		*/

		$data = array();
		$data1 = array();
		$data2 = array();
		$data3 = array();
		$data4 = array();
		$data5 = array();

		$data5[] = $ownerdetails['UserProject']['user_id'];
		$data4 = participants_group_owner($project_id);
		$data3 = participants_owners($project_id, $ownerdetails['UserProject']['user_id']);
		$data2 = participants($project_id, $ownerdetails['UserProject']['user_id']);
		$data1 = participants_group_sharer($project_id);

		$data1 = (isset($data1) && !empty($data1)) ? $data1 : array();
		$data2 = (isset($data2) && !empty($data2)) ? $data2 : array();
		$data3 = (isset($data3) && !empty($data3)) ? $data3 : array();
		$data4 = (isset($data4) && !empty($data4)) ? $data4 : array();
		$data5 = (isset($data5) && !empty($data5)) ? $data5 : array();

		$all_owner = array();

		$data1 = array_filter($data1);
		$data2 = array_filter($data2);
		$data3 = array_filter($data3);
		$data4 = array_filter($data4);
		$data5 = array_filter($data5);

		$all_owner = array_merge($data1, $data2, $data3, $data4, $data5);
		$all_owner = array_unique($all_owner);

		$all_owner = array();
		if (isset($data2) && count($data2) > 0) {
			$all_owner = array_unique(array_merge($data1, $data3, $data2));
		} else {
			$all_owner = array_unique(array_merge($data1, $data3));
		}

		$userlist = '';

		$all_owner = array_filter($all_owner);

		if ($projectStatus == 'reopen') {
			$emailTemplate = 'project_reopen_email';
			$subject = 'Project re-opened';
			$projectAction = SITEURL . 'projects/index/' . $project_id;
		} else {
			$emailTemplate = 'project_signoff_email';
			$subject = 'Project complete';
			$projectAction = SITEURL . 'projects/index/' . $project_id;
		}
		if (isset($all_owner) && !empty($all_owner)) {
			if (($key = array_search($this->Session->read('Auth.User.id'), $all_owner)) !== false) {
				unset($all_owner[$key]);
			}
		}
		if (isset($all_owner) && !empty($all_owner)) {
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
						$owner_name = 'N/A';
					if (!isset($usersDetails['UserDetail']['first_name']) && empty($usersDetails['UserDetail']['last_name'])) {
						$owner_name = 'N/A';
					} else {
						$fullFName = '';
						$fullLName = '';
						if (!empty($usersDetails['UserDetail']['first_name'])) {
							$fullFName = $usersDetails['UserDetail']['first_name'] . ' ';
						}
						if (!empty($usersDetails['UserDetail']['last_name'])) {
							$fullLName = $usersDetails['UserDetail']['last_name'];
						}
						$owner_name = $fullFName . $fullLName;
					}
					// $owner_name = $usersDetails['UserDetail']['first_name'] . ' ' . $usersDetails['UserDetail']['last_name'];

					if ($projectStatus == 'reopen') {

						$dasAnnotate = $this->EmailNotification->find('first', ['conditions' => ['notification_type' => 'project', 'personlization' => 'project_reopen', 'user_id' => $valData]]);

					} else {

						$dasAnnotate = $this->EmailNotification->find('first', ['conditions' => ['notification_type' => 'project', 'personlization' => 'project_complete', 'user_id' => $valData]]);

					}

					if ((!isset($dasAnnotate['EmailNotification']['email']) || $dasAnnotate['EmailNotification']['email'] == 1) && (!isset($usersDetails['User']['email_notification']) || $usersDetails['User']['email_notification'] == 1)) {

						$email = new CakeEmail();
						$email->config('Smtp');
						$email->from(array(ADMIN_FROM_EMAIL => SITENAME));
						$email->to($usersDetails['User']['email']);
						$email->subject(SITENAME . ': ' . $subject);
						$email->template($emailTemplate);
						$email->emailFormat('html');
						$email->viewVars(array('project_name' => $projectname, 'owner_name' => $owner_name, 'signedoffby' => $loggedInUser,'open_page'=>$projectAction));
						$email->send();

					}

				}

			}
		}
	}



	public function projectScheduleOverdueEmailCron() {
		$this->layout = false;
		$this->autoRender = false;
		$this->loadModel('UserPermission');

		$view = new View();
		$commonHelper = $view->loadHelper('Common');

		$projectList = $this->Project->find('all', ['conditions' => ['Project.sign_off !=' => 1],'recursive'=>-1,'fields'=>array('Project.id','Project.title','Project.end_date')]);

		if (isset($projectList) && !empty($projectList) && count($projectList) > 0) {

			$currentDate = date('Y-m-d');
			foreach ($projectList as $listall) {
				//pr($listall);
				//================= Overdue Days ====================================
				$daysleft = daysLeft(date('Y-m-d', strtotime($listall['Project']['end_date'])), $currentDate);
				if ($daysleft == 1) {

					$project_id = $listall['Project']['id'];
					$projectname = strip_tags($listall['Project']['title']);
					$projectAction = SITEURL . 'projects/index/' . $project_id;

					$sql = "SELECT role,user_permissions.user_id,project_id,users.email as email,users.email_notification as email_notification, user_details.first_name as firstName,user_details.last_name as lastName FROM user_permissions

							INNER JOIN
								projects
								ON projects.id=user_permissions.project_id
							INNER JOIN
								users
								ON users.id=user_permissions.user_id
							INNER JOIN
								user_details
								ON user_details.user_id=users.id
							WHERE
								user_permissions.project_id=$project_id	AND
								user_permissions.workspace_id IS NULL ";

						$all_owner = $this->UserPermission->query($sql);

						if (isset($all_owner) && !empty($all_owner)) {

							foreach ($all_owner as $valData) {
								$user_permissions = $valData['user_permissions'];
								$users = $valData['users'];
								$usersDetails = $valData['user_details'];

								$notifiragstatus = $this->EmailNotification->find('first', ['conditions' => ['notification_type' => 'project', 'personlization' => 'project_schedule_overdue', 'user_id' => $user_permissions['user_id']]] );

								if ((!isset($notifiragstatus['EmailNotification']['email']) || $notifiragstatus['EmailNotification']['email'] == 1) && (!isset($users['email_notification']) || $users['email_notification'] == 1)) {

									$owner_name = 'N/A';
									if (!isset($usersDetails['firstName']) && empty($usersDetails['lastName'])) {
										$owner_name = 'N/A';
									} else {
										$fullFName = '';
										$fullLName = '';
										if (!empty($usersDetails['firstName'])) {
											$fullFName = $usersDetails['firstName'] . ' ';
										}
										if (!empty($usersDetails['lastName'])) {
											$fullLName = $usersDetails['lastName'];
										}
										$owner_name = $fullFName . $fullLName;
									}

									$email = new CakeEmail();
									$email->config('Smtp');
									$email->from(array(ADMIN_FROM_EMAIL => SITENAME));
									$email->to($users['email']);
									$email->subject(SITENAME . ': Project schedule overdue');
									$email->template('project_schedule_overdue');
									$email->emailFormat('html');
									$email->viewVars(array('project_name' => $projectname, 'owner_name' => $owner_name, 'projectAction' => $projectAction, 'open_page' => $projectAction));
									$email->send();
								}
							}
						/************** socket messages **************/
							if (SOCKET_MESSAGES) {
								$r_users = $all_owner;
								$open_users = null;
								if (isset($r_users) && !empty($r_users)) {
									foreach ($r_users as $key1 => $value1) {
										$user_permissions = $value1['user_permissions'];
										if (web_notify_setting($user_permissions['user_id'], 'project', 'project_schedule_overdue')) {
											$open_users[] = $user_permissions['user_id'];
										}
									}
								}
								$open_users = array_unique($open_users);
								$content = [
									'notification' => [
										'type' => 'project_schedule_overdue',
										'created_id' => '',
										'project_id' => $project_id,
										'creator_name' => '',
										'subject' => 'Project schedule overdue',
										'heading' => 'Overdue: ' . date('d M Y', strtotime($listall['Project']['end_date'])),
										'sub_heading' => 'Project: ' . $projectname,
										'date_time' => '',
									],
								];
								if (is_array($open_users)) {
									$content['received_users'] = array_values($open_users);
								}
								$request = array(
									'header' => array(
										'Content-Type' => 'application/json',
									),
								);
								$content = json_encode($content);
								$HttpSocket = new HttpSocket([
									'ssl_verify_host' => false,
									'ssl_verify_peer_name' => false,
									'ssl_verify_peer' => false,
								]);
								$results = $HttpSocket->post(CHATURL . '/serveremit', $content, $request);
							}
						/************** socket messages **************/
					}
				}

			}

		}

	}

	public function getProjectList($program_id = null) {
		$this->layout = false;
		$this->autoRender = false;

		App::import('Controller', 'Users');
		$Users = new UsersController;
		$projects = null;
		$myprojectlist = $myreceivedprojectlist = $mygroupprojectlist = array();
		//pr($this->request->data); die;
		if ($this->request->is('post') || $this->request->is('put')) {

			$user_id = null;
			$aligned_id = null;
			$program_id = null;

			$program_id = $this->request->data['program_id'];

			// Find All current user's projects
			$myprojectlist = $Users->__myproject_selectbox($this->user_id, $aligned_id);
			// Find All current user's received projects
			$myreceivedprojectlist = $Users->__receivedproject_selectbox($this->user_id, 1, $aligned_id);
			// Find All current user's group projects
			$mygroupprojectlist = $Users->__groupproject_selectbox($this->user_id, 1, $aligned_id);

			if (is_array($myprojectlist)) {
				$projects1 = $myprojectlist;
			}

			if (is_array($mygroupprojectlist)) {
				$projects1 = array_replace($mygroupprojectlist, $projects1);
			} else {
				$projects1 = $projects1;
			}

			if (is_array($myreceivedprojectlist) && is_array($projects1)) {
				$projects1 = array_replace($myreceivedprojectlist, $projects1);
			} else {
				$projects1 = $projects1;
			}

			$projects = array_map("strip_tags", $projects1);
			$projects = array_map("trim", $projects);
			natcasesort($projects);

			$this->loadModel('ProjectProgram');
			$projgram_project_ids = $this->ProjectProgram->find('list', array('conditions' => array('ProjectProgram.program_id' => $program_id), 'fields' => array('ProjectProgram.id', 'ProjectProgram.project_id')));

			if ((isset($projgram_project_ids) && !empty($projgram_project_ids)) && !empty($projects)) {
				$projgram_project_ids = array_map("strip_tags", $projgram_project_ids);
				$projgram_project_ids = array_map("trim", $projgram_project_ids);
				natcasesort($projgram_project_ids);
				$projectss = array_intersect($projgram_project_ids, array_keys($projects));
			}
			$projectN = [];
			if (isset($projectss) && !empty($projectss)) {
				foreach ($projectss as $pros) {
					$projectN[$pros] = $projects[$pros];
				}
				$projects = $projectN;
			}

			$view = new View($this, false);
			$view->viewPath = 'Projects/partials';
			$view->set("projects", $projects);
			$view->set("program_id", $program_id);
			$html = $view->render('projectlists');
			echo json_encode($html);
			exit;

		}

	}

	/*=== Gantt Chart =========================================================*/
	public function ganttchart($project_id = null) {
		$this->layout = 'inner';
		$this->loadModel('Project');
		$this->set('title_for_layout', __('Show Gantt Chart', true));
		$this->set('page_heading', __('Show Gantt Chart', true));
		$this->set('page_subheading', __('Show Gantt Chart', true));
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

		App::import('Controller', 'Users');
		$this->User = new UsersController;

		// Find All current user's projects
		$myprojectlist = $this->User->__myproject_selectbox($user_id);
		// Find All current user's received projects
		$myreceivedprojectlist = $this->User->__receivedproject_selectbox($user_id, 1);
		// Find All current user's group projects
		$mygroupprojectlist = $this->User->__groupproject_selectbox($user_id, 1);

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
	/*=========================================================================*/

/* ***********
 * Program
 *************/

	public function create_program() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			App::import('Controller', 'Users');
			$Users = new UsersController;
			$projects = null;
			$myprojectlist = $myreceivedprojectlist = $mygroupprojectlist = array();
			// Find All current user's projects
			$myprojectlist = $Users->__myproject_selectbox($this->user_id);
			// Find All current user's received projects
			$myreceivedprojectlist = $Users->__receivedproject_selectbox($this->user_id, 1);
			// Find All current user's group projects
			$mygroupprojectlist = $Users->__groupproject_selectbox($this->user_id, 1);

			if (is_array($myprojectlist)) {
				$projects1 = $myprojectlist;
			}

			if (is_array($mygroupprojectlist)) {
				$projects1 = array_replace($mygroupprojectlist, $projects1);
			} else {
				$projects1 = $projects1;
			}

			if (is_array($myreceivedprojectlist) && is_array($projects1)) {
				$projects1 = array_replace($myreceivedprojectlist, $projects1);
			} else {
				$projects1 = $projects1;
			}

			$projects = array_map("strip_tags", $projects1);
			$projects = array_map("trim", $projects);
			natcasesort($projects);

			$this->set('projects', $projects);
			$this->render(DS . 'Projects' . DS . 'partials' . DS . 'create_program');

		}

	}

	public function update_program() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			App::import('Controller', 'Users');
			$Users = new UsersController;
			$projects = null;
			$myprojectlist = $myreceivedprojectlist = $mygroupprojectlist = array();
			// Find All current user's projects
			$myprojectlist = $Users->__myproject_selectbox($this->user_id);
			// Find All current user's received projects
			$myreceivedprojectlist = $Users->__receivedproject_selectbox($this->user_id, 1);
			// Find All current user's group projects
			$mygroupprojectlist = $Users->__groupproject_selectbox($this->user_id, 1);

			if (is_array($myprojectlist)) {
				$projects1 = $myprojectlist;
			}

			if (is_array($mygroupprojectlist)) {
				$projects1 = array_replace($mygroupprojectlist, $projects1);
			} else {
				$projects1 = $projects1;
			}

			if (is_array($myreceivedprojectlist) && is_array($projects1)) {
				$projects1 = array_replace($myreceivedprojectlist, $projects1);
			} else {
				$projects1 = $projects1;
			}

			$projects = array_map("strip_tags", $projects1);
			$projects = array_map("trim", $projects);
			natcasesort($projects);

			$this->set('allprojects', $projects);

			$this->render(DS . 'Projects' . DS . 'partials' . DS . 'update_program');

		}

	}

	public function save_program() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$response = [
				'success' => false,
				'msg' => '',
				'content' => null,
				'program_name' => '',
				'project_id' => '',
			];

			// $this->request->data['Program']['program_name'] = '';
			// $this->request->data['ProjectProgram']['project_id'] = '';

			if ($this->request->is('post') || $this->request->is('put')) {
				$this->request->data['program_name'] = htmlentities($this->request->data['program_name'], ENT_QUOTES);
				$this->Program->set($this->request->data);

				$this->Program->validates();
				$this->ProjectProgram->validates();

				if (!empty($this->Program->validationErrors) && empty($this->request->data['project_id'])) {

					$response['content'] = $this->Program->validationErrors;
					$response['content']['project_id'] = 'Project is required';
					$response['success'] = false;

				} else if (!empty($this->Program->validationErrors)) {

					$response['content'] = $this->Program->validationErrors;
					$response['success'] = false;

				} else if (empty($this->request->data['project_id'])) {

					$response['content']['project_id'] = 'Project is required';
					$response['success'] = false;

				} else {

					if (isset($this->request->data['program_name']) && !empty($this->request->data['program_name']) && isset($this->request->data['project_id'])) {

						$this->request->data['Program']['program_name'] = $this->request->data['program_name'];
						$this->request->data['ProjectProgram']['project_id'] = $this->request->data['project_id'];

					}

					if ($this->Program->validates() && $this->ProjectProgram->validates()) {

						$this->request->data['Program']['user_id'] = $this->Auth->user('id');
						$projectids = $this->request->data['ProjectProgram']['project_id'];

						if (isset($projectids) && !empty($projectids)) {

							if ($this->Program->save($this->request->data)) {

								$program_id = $this->Program->getLastInsertID();

								//unset($this->request->data['Program']);
								if ($this->request->data['ProjectProgram']) {

									$projectids = $this->request->data['ProjectProgram']['project_id'];
									if (isset($projectids) && !empty($projectids)) {
										foreach ($projectids as $pprojectid) {

											$this->request->data['ProjectProgram']['id'] = null;
											$this->request->data['ProjectProgram']['program_id'] = $program_id;

											$this->request->data['ProjectProgram']['project_id'] = $pprojectid;

											$this->ProjectProgram->save($this->request->data);

										}
										$response['success'] = true;

									}
								}

							}

						} /* else {

							//$this->ProjectProgram->save($this->request->data);
							$response['success'] = false;
							$response['content'] = 'error';
							//$response['program_name'] = $this->Program->validationErrors;
							//$response['project_id'] = $this->ProjectProgram->validationErrors;
							} */
					}
				}

				/*  else {
					//$this->ProjectProgram->save($this->request->data);
					$response['success'] = false;
					$response['content'] = 'error';
					$response['program_name'] = $this->Program->validationErrors;
					$response['project_id'] = $this->ProjectProgram->validationErrors;
				} */
			}
			//pr($response); die;
			echo json_encode($response);
			exit;

		}
	}

	public function update_save_program() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$response = [
				'success' => false,
				'msg' => '',
				'content' => null,
				'program_name' => '',
				'project_id' => '',
			];

			if ($this->request->is('post') || $this->request->is('put')) {

				// $this->request->data['Program']['program_name'] = '';
				// $this->request->data['ProjectProgram']['project_id'] = '';

				$this->Program->validates();
				$this->ProjectProgram->validates();

				if ((!empty($this->Program->validationErrors) && empty($this->request->data['program_name']))) {

					$response['content'] = $this->Program->validationErrors;

					$response['program_name'] = 'Program name is required';
					$response['success'] = false;

				}  else {

					$this->request->data['program_name'] = trim($this->request->data['program_name'] );

					if( isset($this->request->data['program_name']) && !empty($this->request->data['program_name']) ) {

						$this->request->data['Program']['id'] = $this->request->data['program_id'];
						$this->request->data['Program']['program_name'] = $this->request->data['program_name'];

						$this->ProjectProgram->deleteAll(array('ProjectProgram.program_id' => $this->request->data['Program']['id']));

						if (isset($this->request->data['project_id']) && !empty($this->request->data['project_id'])) {

							$this->request->data['ProjectProgram']['project_id'] = $this->request->data['project_id'];
						} else {

							$this->request->data['ProjectProgram']['project_id'] = null;
						}

						if ($this->Program->save($this->request->data)) {

							$response = [
								'success' => true,
								'msg' => 'success',
								'content' => 'success',
								'program_name' => $this->request->data['Program']['program_name'],
								'project_id' => $this->request->data['ProjectProgram']['project_id'],
							];

							if (isset($this->request->data['Program']['id']) && !empty($this->request->data['Program']['id'])) {
								$program_id = $this->request->data['Program']['id'];

							} else {

								$program_id = $this->Program->getLastInsertID();
							}

							//unset($this->request->data['Program']);
							if (isset($this->request->data['ProjectProgram']) && !empty($this->request->data['ProjectProgram'])) {

								if (isset($this->request->data['ProjectProgram']['project_id']) && !empty($this->request->data['ProjectProgram']['project_id'])) {
									$projectids = $this->request->data['ProjectProgram']['project_id'];
								} else {

									$projectids = array();
								}

								if (isset($projectids) && !empty($projectids)) {
									foreach ($projectids as $pprojectid) {

										$this->request->data['ProjectProgram']['id'] = null;
										$this->request->data['ProjectProgram']['program_id'] = $program_id;

										$this->request->data['ProjectProgram']['project_id'] = $pprojectid;

										$this->ProjectProgram->save($this->request->data);

									}
									$response['success'] = true;

								}
							}

						}else{

							$response['content'] = $this->Program->validationErrors;
							$response['program_name'] = 'Program name is required';
							$response['success'] = false;
						}

					}else{

						$response['content'] = $this->Program->validationErrors;
						$response['program_name'] = 'Program name is required';
						$response['success'] = false;
					}

				}

			}
			//pr($response); die;
			echo json_encode($response);
			exit;

		}
	}

	public function update_project_list() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$response = [
				'success' => false,
				'msg' => '',
				'content' => null,
			];
			$html = '';

			if ($this->request->is('post') || $this->request->is('put')) {

				$this->request->data['Program']['program_id'] = '';

				if (isset($this->request->data['program_id']) && !empty($this->request->data['program_id'])) {

					$selectedProjectProgram = $this->ProjectProgram->find('list',
						array(
							'conditions' => array(
								'ProjectProgram.program_id' => $this->request->data['program_id']),
							'fields' => array('ProjectProgram.id', 'ProjectProgram.project_id'),
						)
					);

					$newProjectProgram = array_values(array_filter($selectedProjectProgram));

					/*=========== Start Project List ==============================*/
					App::import('Controller', 'Users');
					$Users = new UsersController;
					$projects = null;
					$myprojectlist = $myreceivedprojectlist = $mygroupprojectlist = array();
					// Find All current user's projects
					$myprojectlist = $Users->__myproject_selectbox($this->user_id);
					// Find All current user's received projects
					$myreceivedprojectlist = $Users->__receivedproject_selectbox($this->user_id, 1);
					// Find All current user's group projects
					$mygroupprojectlist = $Users->__groupproject_selectbox($this->user_id, 1);

					if (is_array($myprojectlist)) {
						$projects1 = $myprojectlist;
					}

					if (is_array($mygroupprojectlist)) {
						$projects1 = array_replace($mygroupprojectlist, $projects1);
					} else {
						$projects1 = $projects1;
					}

					if (is_array($myreceivedprojectlist) && is_array($projects1)) {
						$projects1 = array_replace($myreceivedprojectlist, $projects1);
					} else {
						$projects1 = $projects1;
					}

					$projects = array_map("strip_tags", $projects1);
					$projects = array_map("trim", $projects);
					natcasesort($projects);

					$this->set('allprojects', $projects);
					$this->set('selectedProjectProgram', $newProjectProgram);

					/*=========== End Project List ================================*/
					$view = new View($this, false);
					$view->viewPath = 'Projects/partials'; // Directory inside view directory to search for .ctp files
					$view->layout = false; // if you want to disable layout
					$view->set('allprojects', $projects);
					$view->set('selectedProjectProgram', $newProjectProgram);

					$html = $view->render('update_project_list');
					// $this->render(DS . 'Projects' . DS . 'partials' . DS . 'update_project_list');

				}
			}
			echo (json_encode($html));
			exit;
		}

	}

	public function delete_program() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$response = [
				'success' => false,
				'msg' => '',
				'content' => null,
			];

			if ($this->request->is('post') || $this->request->is('put')) {

				$this->request->data['Program']['program_id'] = '';

				if (isset($this->request->data['program_id']) && !empty($this->request->data['program_id'])) {

					$this->Program->delete($this->request->data['program_id']);
					$response['success'] = true;
					$response['content'] = "Program Deleted";

				}
			}

			echo json_encode($response);
			exit;

		}
	}
	//program_lists

	public function program_list_update() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$response = [
				'success' => true,
				'msg' => '',
				'content' => null,
			];
			//$response['content'] = $this->objView->loadHelper('Common')->program_lists();

			$programlist = $this->Program->find('list', array('conditions' => array('Program.user_id' => $this->Session->read('Auth.User.id')), 'fields' => array('Program.id', 'Program.program_name'), 'order' => 'Program.program_name ASC'));

			$view = new View($this, false);
			$view->viewPath = 'Projects/partials'; //Directory inside view directory to search for .ctp files
			$view->layout = false; // if you want to disable layout
			$view->set('programlist', $programlist);
			$html = $view->render('program_list');

			echo (json_encode($html));
			exit;

		}
	}

	public function project_associations_delete($project_id = null, $user_id = null) {
		if (!isset($project_id) || empty($project_id)) {
			return false;
		}

		$id = $project_id;

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
		// pr($userasspro, 1);
		if (isset($userasspro) && !empty($userasspro)) {
			// die('here 1');
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

			// $this->Project->delete($id);

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

			return true;
		}
		return false;
	}

	function get_project_users($project_id, $user_id = null) {

		$user_id = (isset($user_id) && !empty($user_id)) ? $user_id : $this->user_id;

		$owner = $this->objView->loadHelper('Common')->ProjectOwner($project_id, $user_id);

		$participants = participants($project_id, $owner['UserProject']['user_id']);

		$participants_owners = participants_owners($project_id, $owner['UserProject']['user_id']);

		$participantsGpOwner = participants_group_owner($project_id);

		$participantsGpSharer = participants_group_sharer($project_id);

		$participants = isset($participants) ? array_filter($participants) : $participants;
		// $participants = (is_array($participants)) ? $participants : array($participants);

		$participants_owners = isset($participants_owners) ? array_filter($participants_owners) : $participants_owners;
		// $participants_owners = (is_array($participants_owners)) ? $participants_owners : array($participants_owners);

		$participantsGpOwner = isset($participantsGpOwner) ? array_filter($participantsGpOwner) : $participantsGpOwner;
		// $participantsGpOwner = (is_array($participantsGpOwner)) ? $participantsGpOwner : array($participantsGpOwner);

		$participantsGpSharer = isset($participantsGpSharer) ? array_filter($participantsGpSharer) : $participantsGpSharer;
		// $participantsGpSharer = (is_array($participantsGpSharer)) ? $participantsGpSharer : array($participantsGpSharer);

		$project_users = [];
		if (is_array($participants)) {
			$project_users = array_merge($project_users, $participants);
		}
		if (is_array($participants_owners)) {
			$project_users = array_merge($project_users, $participants_owners);
		}
		if (is_array($participantsGpOwner)) {
			$project_users = array_merge($project_users, $participantsGpOwner);
		}
		if (is_array($participantsGpSharer)) {
			$project_users = array_merge($project_users, $participantsGpSharer);
		}

		$project_users = array_unique($project_users);

		return $project_users;
	}

	/*===================== Project Element Risks count ============================================*/
	public function get_project_elements_risks_count($project_id = null, $workspace_id = null) {
		$this->loadModel('RmElement');
		$e_permission = null;
		$project_level = 0;

		$user_id = $this->Session->read('Auth.User.id');
		$p_permission = $this->objView->loadHelper('Common')->project_permission_details($project_id, $user_id);
		$user_project = $this->objView->loadHelper('Common')->userproject($project_id, $user_id);
		$group_id = $this->objView->loadHelper('Group')->GroupIDbyUserID($project_id, $user_id);
		$project_workspace = get_project_workspace($project_id, true);

		if (isset($workspace_id) && !empty($workspace_id)) {

			$wsp_permission = $this->objView->loadHelper('Common')->wsp_permission_details($this->objView->loadHelper('ViewModel')->workspace_pwid($workspace_id), $project_id, $user_id);

			if (isset($group_id) && !empty($group_id)) {

				$group_permission = $this->objView->loadHelper('Group')->group_permission_details($project_id, $group_id);

				if (isset($group_permission['ProjectPermission']['project_level']) && $group_permission['ProjectPermission']['project_level'] == 1) {
					$project_level = $group_permission['ProjectPermission']['project_level'];
				}
				$wsp_permission = $this->objView->loadHelper('Group')->group_wsp_permission_details($this->objView->loadHelper('ViewModel')->workspace_pwid($workspace_id), $project_id, $group_id);
			}

			if ((isset($user_project) && !empty($user_project)) || (isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level'] == 1) || (isset($project_level) && $project_level == 1)) {
				$e_permission = $this->objView->loadHelper('ViewModel')->project_workspace_elements($project_id, $workspace_id);
				if (isset($e_permission) && !empty($e_permission)) {
					$e_permission = Set::extract($e_permission, '/element/id');
				}

			} else if (isset($wsp_permission) && !empty($wsp_permission)) {
				$e_permission = $this->objView->loadHelper('Common')->element_permission_details($workspace_id, $project_id, $user_id);

				if ((isset($group_id) && !empty($group_id))) {

					if (isset($e_permission) && !empty($e_permission)) {
						$ge_permissions = $this->objView->loadHelper('Group')->group_element_permission_details($workspace_id, $project_id, $group_id);
						$e_permission = array_merge($e_permission, $ge_permissions);
					} else {
						$e_permission = $this->objView->loadHelper('Group')->group_element_permission_details($workspace_id, $project_id, $group_id);
					}
				}
			}
		}

		$user_id = $this->Session->read('Auth.User.id');
		$user_risk = user_risks($user_id);
		$risk_elements = $this->RmElement->find('all', [
			'conditions' => [
				'RmElement.project_id' => $project_id,
				'RmElement.element_id' => $e_permission,
			],
			'fields' => 'RmElement.rm_detail_id',
			'recursive' => 1,
		]);

		$risk_elementscnt = 0;
		$risk_elements_riskids = array();
		if (isset($risk_elements) && !empty($risk_elements)) {

			$risk_elements_riskids = Set::extract($risk_elements, '/RmElement/rm_detail_id');
			if (isset($risk_elements_riskids) && !empty($risk_elements_riskids)) {
				$risk_elements_riskids = array_unique($risk_elements_riskids);
				if ($user_risk) {
					$risk_elements_riskids = array_intersect($user_risk, $risk_elements_riskids);
				}
				$risk_elementscnt = array_unique($risk_elements_riskids);
			}
		}

		return (isset($risk_elementscnt) && !empty($risk_elementscnt)) ? count($risk_elementscnt) : 0;

	}

	public function risk_project_elements($project_id = null) {
		$this->autoRender = false;
		$this->loadModel('RmElement');

		$risk_elements = $this->RmElement->find('all', [
			'conditions' => [
				'RmElement.project_id' => $project_id,
			],
			'fields' => 'element_id',
			'recursive' => -1,
		]);
		$risk_elements = Set::extract($risk_elements, '/RmElement/element_id');
		$risk_elements = array_unique($risk_elements);
		// $result=array_intersect($a1,$a2);
		pr($risk_elements);
		// return (isset($risk_elements) && !empty($risk_elements)) ? $risk_elements : false;

	}

	public function wsp_risks($project_id, $workspace_id = null) {

		$this->loadModel('RmElement');
		$e_permission = array();
		$project_level = 0;

		$user_id = $this->Session->read('Auth.User.id');
		$p_permission = $this->objView->loadHelper('Common')->project_permission_details($project_id, $user_id);
		$user_project = $this->objView->loadHelper('Common')->userproject($project_id, $user_id);
		$group_id = $this->objView->loadHelper('Group')->GroupIDbyUserID($project_id, $user_id);
		$project_workspace = get_project_workspace($project_id, true);

		if (isset($workspace_id) && !empty($workspace_id)) {

			$wsp_permission = $this->objView->loadHelper('Common')->wsp_permission_details($this->objView->loadHelper('ViewModel')->workspace_pwid($workspace_id), $project_id, $user_id);

			if (isset($group_id) && !empty($group_id)) {

				$group_permission = $this->objView->loadHelper('Group')->group_permission_details($project_id, $group_id);

				if (isset($group_permission['ProjectPermission']['project_level']) && $group_permission['ProjectPermission']['project_level'] == 1) {
					$project_level = $group_permission['ProjectPermission']['project_level'];
				}
				$wsp_permission = $this->objView->loadHelper('Group')->group_wsp_permission_details($this->objView->loadHelper('ViewModel')->workspace_pwid($workspace_id), $project_id, $group_id);
			}

			if ((isset($user_project) && !empty($user_project)) || (isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level'] == 1) || (isset($project_level) && $project_level == 1)) {

				$e_permission = $this->objView->loadHelper('ViewModel')->project_workspace_elements($project_id, $workspace_id);
				if (isset($e_permission) && !empty($e_permission)) {
					$e_permission = Set::extract($e_permission, '/element/id');
				}

			} else if (isset($wsp_permission) && !empty($wsp_permission)) {
				$e_permission = $this->objView->loadHelper('Common')->element_permission_details($workspace_id, $project_id, $user_id);

				if ((isset($group_id) && !empty($group_id))) {

					if (isset($e_permission) && !empty($e_permission)) {
						$ge_permissions = $this->objView->loadHelper('Group')->group_element_permission_details($workspace_id, $project_id, $group_id);
						$e_permission = array_merge($e_permission, $ge_permissions);
					} else {
						$e_permission = $this->objView->loadHelper('Group')->group_element_permission_details($workspace_id, $project_id, $group_id);
					}
				}
			}
		}

		$risk_elements = array();
		$risk_elements = $this->RmElement->find('all', [
			'conditions' => [
				'RmElement.project_id' => $project_id,
			],
			'fields' => 'element_id',
			'recursive' => -1,
		]);

		$intersectElements = array();
		if (isset($risk_elements) && !empty($risk_elements)) {

			$risk_elements = Set::extract($risk_elements, '/RmElement/element_id');
			$risk_elements = array_unique($risk_elements);

			if (!empty($risk_elements) && !empty($risk_elements)) {
				$intersectElements = array_intersect($e_permission, $risk_elements);
			}

		}

		$this->set('intersectElements', $intersectElements);
		$this->set('project_id', $project_id);
		$this->set('workspace_id', $workspace_id);
	}
	/*========================================================================================*/

	public function delete_an_item($project_id = null) {

		if ($this->request->isAjax()) {

			$this->layout = false;
			$viewData = null;
			$viewData['id'] = $project_id;

			$this->set($viewData);
			$this->render('/Projects/partials/delete_an_item');

		}
	}

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

	public function manage_project_load($project_id = null, $workspace_id = null) {
		$data = null;

		$this->layout = false;

		if (is_null($project_id) || is_null($workspace_id)) {
			$this->redirect(Controller::referer());
		}

		$this->loadModel("Template");

		// $this->layout = 'inner';

		$this->set('title_for_layout', __('Manage Elements', true));

		$this->Workspace->recursive = -1;
		$workspace = $this->Workspace->find('first', [
			'conditions' => [
				'Workspace.id' => $workspace_id,
			],
		]);

		//pr($workspace,1);

		$this->loadModel('TemplateDetail');
		$template_groups = $this->TemplateDetail->find('all', array(
			'fields' => 'DISTINCT row_no, id',
			'recursive' => 1,
			'conditions' => array(
				'TemplateDetail.template_id' => $workspace['Workspace']['template_id'],
			),
		));

		$grouped_ids = Set::extract($template_groups, '/TemplateDetail/id');

		$this->Area->unbindModel(
			array('hasMany' => array(
				'Elements',
			),
			)
		);

		/* -----------Group code----------- */
		$projectsg = $this->UserProject->find('first', ['recursive' => -1, 'conditions' => ['UserProject.project_id' => $project_id], 'fields' => ['UserProject.id']]);
		$pgupid = $projectsg['UserProject']['id'];
		$conditionsG = null;
		$conditionsG['ProjectGroupUser.user_id'] = $this->user_id;
		$conditionsG['ProjectGroupUser.user_project_id'] = $pgupid;
		$conditionsG['ProjectGroupUser.approved'] = 1;
		$projects_group_shared_user = $this->ProjectGroupUser->find('first', array(
			'conditions' => $conditionsG,
			'fields' => array('ProjectGroupUser.project_group_id'),
			'recursive' => -1,
		));
		if (isset($projects_group_shared_user) && !empty($projects_group_shared_user)) {
			//echo $project_id." sdsd ".$projects_group_shared_user['ProjectGroupUser']['project_group_id']; die;
			$group_permission = $this->Group->group_permission_details($project_id, $projects_group_shared_user['ProjectGroupUser']['project_group_id']);

			$pll_level = $group_permission['ProjectPermission']['project_level'];

			$this->set('gpid', $projects_group_shared_user['ProjectGroupUser']['project_group_id']);

			if (isset($pll_level) && $pll_level == 1) {
				$this->set('project_level', 1);
			}
		}
		/* -----------Group code----------- */

		$area_template_data = $this->Workspace->Area->find('all', [
			'fields' => [
				'Area.id as area_id', 'Area.workspace_id', 'Area.title', 'Area.description as desc', 'Area.tooltip_text', 'Area.status',
				'TemplateDetail.id as temp_detail_id', 'TemplateDetail.row_no', 'TemplateDetail.col_no', 'TemplateDetail.size_w', 'TemplateDetail.size_h', 'TemplateDetail.elements_counter', 'TemplateDetail.template_id',
			],
			'conditions' => [
				'Area.workspace_id' => $workspace_id,
				'Area.template_detail_id' => $grouped_ids,
			],
			'recursive' => 1,
			'order' => ['TemplateDetail.id ASC', 'TemplateDetail.row_no ASC'],
		]);

		//pr($area_template_data,1);

		$templateRows = $andConditions = null;

		//pr($this->data);
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

		if (isset($this->data['Element']['start_date'])) {
			$start = trim($this->data['Element']['start_date']);
		} else {
			$start = $this->Session->read('element.start_date');
		}

		if (isset($this->data['Element']['end_date'])) {
			$end = trim($this->data['Element']['end_date']);
		} else {
			$end = $this->Session->read('element.end_date');
		}

		$andConditions = array();

		if (isset($keyword)) {
			$this->Session->write('user.keyword', $keyword);
			$keywords = explode(" ", $keyword);

			if (!empty($keyword)) {
				$in = 1;
				$andConditions = array('OR' => array(
					'Element.description LIKE' => '%' . $keyword . '%',
					'Element.title LIKE' => '%' . $keyword . '%',
				));
			}
		}

		if ((isset($start) && isset($end)) && (!empty($start) && !empty($end))) {

			if (empty($andConditions)) {
				$andConditions = array(
					'Element.start_date >=' => date('Y-m-d H:i:s', strtotime($start . " 00:00:00")),
					'Element.end_date <=' => date('Y-m-d H:i:s', strtotime($end . " 23:59:59")),
					//'UserDetail.country_id LIKE' => '%' . $county . '%'
				);
			} else {

				$andConditions1 = array(
					'Element.start_date >=' => date('Y-m-d H:i:s', strtotime($start . " 00:00:00")),
					'Element.end_date <=' => date('Y-m-d H:i:s', strtotime($end . " 23:59:59")),
					//'UserDetail.country_id LIKE' => '%' . $county . '%'
				);
				$andConditions = array_merge($andConditions1, $andConditions);
			}
		}

		$finalConditions = array();

		if (isset($this->data['User']['status'])) {
			$status = $this->data['User']['status'];
		} else {
			$status = $this->Session->read('user.status');
		}

		if (isset($status)) {
			$this->Session->write('User.status', $status);
			if ($status != '') {

				if ($status == 0) {

					$andConditions = array_merge($andConditions, array('Element.date_constraints' => 0));
				} else if ($status == 1) {

					$andConditions = array_merge($andConditions, array(
						'Element.start_date <=' => date('Y-m-d H:i:s', strtotime(date('Y-m-d') . " 00:00:00")),
						'Element.end_date >=' => date('Y-m-d H:i:s', strtotime(date('Y-m-d') . " 23:59:59")),
						'Element.date_constraints >' => 0,
					));
				} else if ($status == 2) {

					$andConditions = array_merge($andConditions, array(
						'Element.start_date >' => date('Y-m-d H:i:s', strtotime(date('Y-m-d') . " 00:00:00")),
						'Element.date_constraints >' => 0,
					));
				} else if ($status == 3) {

					$andConditions = array_merge($andConditions, array(
						'Element.sign_off >' => 0,
					));
				} else if ($status == 4) {

					$andConditions = array_merge($andConditions, array(
						'Element.end_date <' => date('Y-m-d H:i:s', strtotime(date('Y-m-d') . " 23:59:59")),
						'Element.date_constraints >' => 0,
						'Element.sign_off ' => 0,
					));
				}

				$in = 1;
			}
		}

		if (isset($this->data['Element']['per_page_show']) && !empty($this->data['Element']['per_page_show'])) {
			$per_page_show = $this->data['Element']['per_page_show'];
		}

		if (!empty($andConditions) && !empty($finalConditions)) {
			$finalConditions = array_merge($finalConditions, array('AND' => $andConditions));
			$in = 1;
		} else if (!empty($andConditions)) {
			$finalConditions = $andConditions;
			$in = 1;
		}
		foreach ($area_template_data as $row_id => $row_templates) {
			$area_detail = $row_templates['Area'];
			$temp_detail = $row_templates['TemplateDetail'];

			$row_templates['Elements'] = $this->Element->find('all', array(
				'conditions' => array(
					'Element.area_id' => $row_templates['Area']['area_id'],
					$finalConditions,
				),
				'recursive' => -1,
				'order' => ['Element.sort_order ASC'],
			)
			);

			$elements = $row_templates['Elements'];

			if ($temp_detail['size_w'] > 0 && $temp_detail['size_h'] > 0) {
				$row_no = $temp_detail['row_no'];
				$area_templates = array_merge($temp_detail, $area_detail);
				if (isset($elements) && !empty($elements)) {
					$area_templates['elements'] = $elements;
				}

				//pr($area_templates['elements'],1);
				// pr($area_templates);
				$templateRows[$row_no][] = $area_templates;
			}
		}

		$data['templateRows'] = $templateRows;
		// pr($templateRows,1);

		$this->setJsVar('workspace_id', $workspace_id);
		$this->setJsVar('project_id', $project_id);

		$this->setJsVar('template_id', $workspace['Workspace']['template_id']);
		$this->set('template_id', $workspace['Workspace']['template_id']);

		$this->set('project_id', $project_id);
		$this->set('workspace_id', $workspace_id);

		$data['workspace'] = $workspace;
		$data['page_heading'] = 'Elements';
		$data['page_subheading'] = 'View Elements in this Workspace';

		$this->set('data', $data);
		$this->set('in', $in);

		// Get project detail
		$projects = $cat_crumb = null;
		if (isset($project_id) && !empty($project_id)) {
			$projects = $this->Project->find('first', ['conditions' => ['Project.id' => $project_id], 'recursive' => -1]);

			// Get category detail of parent Project
			// if category detail found, merge it with other breadcrumb data
			$cat_crumb = get_category_list($project_id);
		}
		$project_title = _strip_tags($projects['Project']['title']);

		$crumb = [
			'Summary' => [
				'data' => [
					'url' => '/projects/index/' . $project_id,
					'class' => 'tipText',
					'title' => $project_title,
					'data-original-title' => $project_title,
				],
			],
			'last' => [
				'data' => [
					'title' => $workspace['Workspace']['title'],
					'data-original-title' => $workspace['Workspace']['title'],
				],
			],
		];

		if (isset($cat_crumb) && !empty($cat_crumb) && is_array($cat_crumb)) {

			$crumb = array_merge($cat_crumb, $crumb);
		}
		$this->set('crumb', $crumb);

		$areas = $this->Area->find('all', ['conditions' => [
			'Area.workspace_id' => $workspace_id,
		],
			'fields' => ['Area.id'],
			'recursive' => -1,
		]);
		if ($this->get_project_elements_risks_count($project_id, $workspace_id) > 0) {
			$riskElementCnt = $this->get_project_elements_risks_count($project_id, $workspace_id);
		} else {
			$riskElementCnt = 0;
		}
		$this->set('riskelementcount', $riskElementCnt);
		$this->set('areas', $areas);

		$this->render('/Projects/manage_elements');

	}

	public function show_area_elements($area_id = null, $workspace_id = null, $project_id = null) {
		if ($this->request->isAjax()) {

			$this->layout = false;

			$area_detail = getByDbId('Area', $area_id);
			$this->set('area_id', $area_id);
			$this->set('workspace_id', $workspace_id);
			$this->set('project_id', $project_id);
			$this->set('area_detail', $area_detail);

			$this->render('/Projects/partials/area_elements');

		}
	}

	/*=============================================================================*/
	public function all_project_types() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$response = [
				'success' => false,
				'content' => [],
			];
			$user_id = $this->user_id;

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;

				if (isset($post['project_id']) && !empty($post['project_id'])) {
					$project_id = $post['project_id'];
				} else {
					$project_temp_id = 'ideascast_' . $this->Session->read('Auth.User.id');
				}

				if (isset($project_id) && !empty($project_id)) {

					$project_types = $this->project_types_custom($project_id);
					$project_types_selected = $this->project_types_selected($project_id);
					$response['content'] = Set::combine($project_types, '{n}.ProjectElementType.id', '{n}.ProjectElementType.title');

					$response['content'] = array_map(function ($v) {
												return trim(htmlentities($v));
											}, $response['content']);


					$response['selectedIDs'] = (isset($project_types_selected) && !empty($project_types_selected)) ? implode(',', $project_types_selected) : 0;
					$selTypes = custom_type_used($project_id);
					if (isset($selTypes) && !empty($selTypes)) {
						$response['used_ids'] = (isset($selTypes) && !empty($selTypes)) ? implode(',', $selTypes) : 0;
					} else {
						$response['used_ids'] = 0;
					}

				} else {

					$project_types = $this->project_types_temp_custom($project_temp_id);
					$response['content'] = Set::combine($project_types, '{n}.ProjectElementTypeTemp.id', '{n}.ProjectElementTypeTemp.title');

					$response['content'] = array_map(function ($v) {
												return trim(htmlentities($v));
											}, $response['content']);

					$response['selectedIDs'] = '';

				}
				$response['success'] = true;
			}

			echo json_encode($response);
			exit();

		}
	}

	public function custom_project_type() {

		if ($this->request->isAjax()) {

			$this->layout = false;
			$user_id = $this->user_id;

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				$type_count = 0;
				$projects_types = null;
				$project_id = null;

				$view = new View($this, false);
				$view->viewPath = 'Projects/partials';

				if (isset($post) && !empty($post['project_id'])) {

					$project_id = $post['project_id'];
					if (isset($project_id) && !empty($project_id)) {
						$projects_types = $this->project_types_custom($project_id);

						$projects_types = (isset($projects_types) && !empty($projects_types)) ? Set::combine($projects_types, '{n}.ProjectElementType.id', '{n}.ProjectElementType.title') : $projects_types;

						$projectElementTypeSelected = $this->ProjectElementType->find('list', array(
							'conditions' => array('ProjectElementType.project_id' => $project_id, 'ProjectElementType.type_status' => 1),
							'fields' => 'ProjectElementType.id',
						)
						);

						if (isset($projectElementTypeSelected) && !empty($projectElementTypeSelected)) {
							$type_count = count($projectElementTypeSelected);
						}

					}
					$view->set('type_count', $type_count);
					$view->set("project_id", $project_id);
					$view->set("projects_types", $projects_types);
					$html = $view->render('custom_project_type');

				} else {
					$project_temp_id = 'ideascast_' . $this->Session->read('Auth.User.id');
					$projects_types = $this->project_types_temp_custom($project_temp_id);
					$projects_types = (isset($projects_types) && !empty($projects_types)) ? Set::combine($projects_types, '{n}.ProjectElementTypeTemp.id', '{n}.ProjectElementTypeTemp.title') : $projects_types;
					$view->set("project_id", '');
					$view->set("projects_types", $projects_types);
					$html = $view->render('custom_project_type_temp');
				}

			}

			echo json_encode($html);
			exit();

		}
	}

	function project_types($project_id) {

		$projectTypes = $this->ProjectElementType->find('all', [
			'conditions' => [
				'ProjectElementType.project_id' => $project_id,
			],
			'order' => 'ProjectElementType.title',
		]);
		//order by case when id in (5,15,25) then -1 else id end,id
		return (isset($projectTypes) && !empty($projectTypes)) ? $projectTypes : false;
	}

	function project_types_custom($project_id) {

		/* $projectTypes = $this->ProjectElementType->find('all', [
			'conditions' => [
				'ProjectElementType.project_id' => $project_id,
			],
			'order'=>'ProjectElementType.title'
		]); */

		$query = "SELECT * from project_element_types as ProjectElementType Where project_id = '" . $project_id . "' order by case when title = 'Generals' then -1 else title end,title ";

		$projectTypes = $this->ProjectElementType->query($query);

		return (isset($projectTypes) && !empty($projectTypes)) ? $projectTypes : false;

		//  order by case when title = 'General' then -1 else title end,title

	}

	function project_types_temp_custom($project_temp_id) {

		/* $projectTypes = $this->ProjectElementTypeTemp->find('all', [
				'conditions' => [
					'ProjectElementTypeTemp.project_id' => $project_temp_id,
					'ProjectElementTypeTemp.user_id' => $this->Session->read('Auth.User.id'),
				],
			]);

		*/

		$query = "SELECT * from project_element_type_temps as ProjectElementTypeTemp Where project_id = '" . $project_temp_id . "' order by case when title = 'Generals' then -1 else title end,title ";

		$projectTypes = $this->ProjectElementTypeTemp->query($query);
		return (isset($projectTypes) && !empty($projectTypes)) ? $projectTypes : false;

	}

	function project_types_selected($project_id) {

		$project_types_selected = $this->ProjectElementType->find('list', [
			'conditions' => [
				'ProjectElementType.project_id' => $project_id,
				'ProjectElementType.type_status' => 1,
			],
			'fields' => array('ProjectElementType.id'),
		]);

		return (isset($project_types_selected) && !empty($project_types_selected)) ? $project_types_selected : 0;
	}
 
	public function save_project_type() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$response = [
				'success' => false,
				'content' => null,
			];
			$user_id = $this->user_id;

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;

				if ($post['tabletype'] == 'default') {
					if (isset($post['project_type_id']) && !empty($post['project_type_id'])) {
						$title = Sanitize::escape($post['title']);
						$checktitle = $this->ProjectElementType->find('count', array('conditions' => array('ProjectElementType.title' => trim($title), 'ProjectElementType.project_id' => $post['pid'], 'ProjectElementType.user_id' => $user_id, 'ProjectElementType.id !=' => $post['project_type_id'])));

						if ($checktitle <= 0) {

							$id = $post['project_type_id'];

							$this->ProjectElementType->query("update project_element_types set title = '$title', is_custom = 1 WHERE id = $id");
							//if ($this->ProjectElementType->saveField("title", $post['title'])) {
								$response['success'] = true;
							//}

						} else {
							$response['success'] = false;
							$response['content'] = "Cannot create, already exists";

						}
					}
				} else {
					$project_temp_id = 'ideascast_' . $this->Session->read('Auth.User.id');
					if (isset($post['project_type_id']) && !empty($post['project_type_id'])) {
						$title = Sanitize::escape($post['title']);

						$checktitle = $this->ProjectElementTypeTemp->find('count', array('conditions' => array('ProjectElementTypeTemp.title' => trim($title), 'ProjectElementTypeTemp.project_id' => $project_temp_id, 'ProjectElementTypeTemp.user_id' => $user_id, 'ProjectElementTypeTemp.id !=' => $post['project_type_id'])));

						if ($checktitle <= 0) {
							$id = $post['project_type_id'];
							$this->ProjectElementType->query("update project_element_type_temps set title = '$title', is_custom = 1 WHERE id = $id");
							//$this->ProjectElementTypeTemp->id = $post['project_type_id'];
							//if ($this->ProjectElementTypeTemp->saveField("title", $post['title'])) {
								$response['success'] = true;
							//}
						} else {
							$response['success'] = false;
							$response['content'] = "Cannot create, already exists";
						}

					}

				}

			}

			echo json_encode($response);
			exit();

		}
	}

	public function trash_project_type() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$response = [
				'success' => false,
				'content' => null,
			];
			$user_id = $this->user_id;

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;

				if (isset($post['project_type_id']) && !empty($post['project_type_id'])) {

					$taskcount = $this->ProjectElementType->find('count', array('conditions' => array('ProjectElementType.project_id' => $post['project_id'])));

					$usedType = custom_type_involved($post['project_type_id'], $post['project_id']);

					if (empty($usedType)) {

						if ($taskcount > 1) {
							if ($this->ProjectElementType->delete($post['project_type_id'])) {
								$response['success'] = true;
							}
						} else {
							$response['success'] = false;
						}

					} else {
						$response['success'] = true;
					}

					/* if( !empty($post['project_id']) ){
						if( custom_type_involved($post['project_type_id'],$post['project_id']) <= 0 ){
							if ($this->ProjectElementType->delete($post['project_type_id'])) {
								$response['success'] = true;
							}
						} else {
							$response = [
								'success' => false,
								'content' => 'Task Type used in project',
							];
						}
					} */
				}
			}

			echo json_encode($response);
			exit();

		}
	}

	public function trash_project_type_temp() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$response = [
				'success' => false,
				'content' => null,
			];
			$user_id = $this->user_id;

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				if (isset($post['project_type_id']) && !empty($post['project_type_id'])) {

					$project_temp_id = 'ideascast_' . $this->Session->read('Auth.User.id');
					$taskcount = $this->ProjectElementTypeTemp->find('count', array('conditions' => array('ProjectElementTypeTemp.project_id' => $project_temp_id)));

					if ($taskcount > 1) {

						if ($this->ProjectElementTypeTemp->delete($post['project_type_id'])) {
							$response['success'] = true;
						}

					} else {

						$response['success'] = false;

					}

				}
			}

			echo json_encode($response);
			exit();

		}
	}

	public function create_project_type() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$response = [
				'success' => false,
				'content' => null,
			];
			$user_id = $this->user_id;

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;

				if (isset($post['title']) && !empty($post['title']) && !empty($post['project_id'])) {

					$checktitle = $this->ProjectElementType->find('count', array('conditions' => array('ProjectElementType.title' => trim($post['title']), 'ProjectElementType.project_id' => $post['project_id'], 'ProjectElementType.user_id' => $user_id)));

					if ($checktitle <= 0) {

						$data = [
							'title' => $post['title'],
							'project_id' => $post['project_id'],
							'user_id' => $user_id,
							'is_custom' => 1
						];
						if ($this->ProjectElementType->save($data)) {
							$response['success'] = true;
						}

					} else {
						$response['success'] = false;
						$response['content'] = "Cannot create, already exists";
					}

				} else {
					$project_temp_id = 'ideascast_' . $this->Session->read('Auth.User.id');

					$checktitle = $this->ProjectElementTypeTemp->find('count', array('conditions' => array('ProjectElementTypeTemp.title' => trim($post['title']), 'ProjectElementTypeTemp.project_id' => $project_temp_id, 'ProjectElementTypeTemp.user_id' => $user_id)));

					if ($checktitle <= 0) {
						$data = [
							'title' => $post['title'],
							'project_id' => $project_temp_id,
							'user_id' => $user_id,
							'is_custom' => 1
						];
						if ($this->ProjectElementTypeTemp->save($data)) {
							$response['success'] = true;
						}
					} else {
						$response['success'] = false;
						$response['content'] = "Cannot create, already exists";
					}
				}

			}

			echo json_encode($response);
			exit();

		}
	}

	public function project_element_type() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$response = [
				'success' => false,
				'content' => null,
			];
			$user_id = $this->user_id;

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;

				// $projectEleType = $this->ViewModel->project_element_type($project_id);

				if (isset($post['typeid']) && !empty($post['typeid']) && !empty($post['project_id'])) {

					/* $checktitle = $this->ProjectElementType->find('list',
						array('conditions' => array('ProjectElementType.project_id' => $post['project_id'],'ProjectElementType.id !=' => $post['typeid'], 'ProjectElementType.user_id' => $user_id),'recursive'=> -1)); */

					$checktitle = $this->ProjectElementType->find('list', ['conditions' => [
						'ProjectElementType.project_id' => $post['project_id'],
						'ProjectElementType.type_status' => 1,
						'ProjectElementType.id !=' => $post['typeid'],
					], 'recursive' => -1, 'order' => 'ProjectElementType.title ASC',
					]);

					if (isset($checktitle) && !empty($checktitle)) {

						$response['content'] = $checktitle;
						$response['success'] = true;

					} else {
						$response['success'] = false;
						$response['content'] = "Cannot create, already exists";
					}

				}

			}

			echo json_encode($response);
			exit();

		}
	}

	public function current_project() {

		$this->loadModel('CurrentProject');

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$response = [
				'success' => false,
				'content' => null,
			];
			$user_id = $this->user_id;

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				if (isset($post['project_id']) && !empty($post['project_id'])) {

					//$cntProject = $this->CurrentProject->find('count', array('conditions' => array('CurrentProject.user_id' => $user_id)));

					//if (isset($cntProject) && $cntProject < 5) {
						if ($post['status'] == 'add') {

							$this->request->data['CurrentProject']['id'] = '';
							$this->request->data['CurrentProject']['user_id'] = $user_id;
							$this->request->data['CurrentProject']['project_id'] = $post['project_id'];
							$this->request->data['CurrentProject']['created'] = date('Y-m-d h:i:s');
							$this->CurrentProject->save($this->request->data['CurrentProject']);
							$response['success'] = true;
						}
					//}

					if ($post['status'] == 'remove') {
						$this->CurrentProject->deleteAll(array('CurrentProject.project_id' => $post['project_id'], 'CurrentProject.user_id' => $user_id));
						$response['success'] = true;
					}

				}
			}

			echo json_encode($response);
			exit();

		}
	}

	public function switch_element_type() {

		$this->loadModel('ElementType');

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$response = [
				'success' => false,
				'content' => null,
			];
			$user_id = $this->user_id;

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				if (isset($post['project_id']) && !empty($post['project_id'])) {

					$exitstype = $this->ElementType->find('all',
						array('conditions' => array('ElementType.project_id' => $post['project_id'], 'ElementType.type_id' => $post['exitstype']),
						)
					);

					if (isset($exitstype) && !empty($exitstype)) {

						foreach ($exitstype as $eletypeval) {

							$this->request->data['ElementType']['id'] = $eletypeval['ElementType']['id'];
							$this->request->data['ElementType']['type_id'] = $post['typeid'];
							$this->ElementType->save($this->request->data['ElementType']);
							$response['success'] = true;
						}
						$response['success'] = true;
					} else {
						$response['success'] = false;
						$response['content'] = 'Task Type is not exists';
					}
				}
			}

			echo json_encode($response);
			exit();

		}
	}

	/************************************************************************
	*************** Start Get People from User Permission View **************
	*************************************************************************/
	// tasks function
	public function manage_elements($project_id = null, $workspace_id = null) {



		$this->layout = 'inner';

		$this->set('title_for_layout', __('Manage Tasks', true));

		if (isset($project_id) && !empty($project_id) ) {
			if(!dbExists('Project', $project_id)){
				$this->redirect(array('controller' => 'projects', 'action' => 'lists'));
			}
			else{
				if (isset($workspace_id) && !empty($workspace_id) ) {
					if(!dbExists('Workspace', $workspace_id)){
						$this->redirect(array('controller' => 'projects', 'action' => 'index', $project_id));
					}
				}
			}
		}
		if (is_null($project_id) || is_null($workspace_id)) {
			$this->redirect(Controller::referer());
		}

		$this->loadModel("Template");
		$this->loadModel('ProjectActivity');
		$this->loadModel('WorkspaceActivity');
		$this->loadModel('TemplateDetail');

		$this->Workspace->recursive = -1;
		$workspace = $this->Workspace->find('first', [
			'conditions' => [
				'Workspace.id' => $workspace_id
			],
		]);

		if( isset($workspace['Workspace']['studio_status']) && !empty($workspace['Workspace']['studio_status']) ){
			$this->Session->setFlash(__('Workspace state has been changed in to draft, please generate it through the Design Center.'), 'error');
			$this->redirect(array('controller'=>'projects','action' => 'index',$project_id));
		}

		$task_data = [
			'project_id' => $project_id,
			'workspace_id' => $workspace_id,
			//'element_type' => 'do_lists',
			'updated_user_id' => $this->user_id,
			'message' => 'Workspace viewed',
			'updated' => date("Y-m-d H:i:s"),
		];

		$this->ProjectActivity->id = null;
		$this->WorkspaceActivity->save($task_data);

		$template_groups = $this->TemplateDetail->find('all', array(
			'fields' => 'DISTINCT row_no, id',
			'recursive' => 1,
			'conditions' => array(
				'TemplateDetail.template_id' => $workspace['Workspace']['template_id'],
			),
		));

		$grouped_ids = Set::extract($template_groups, '/TemplateDetail/id');
		$this->Area->unbindModel(
			array('hasMany' => array(
				'Elements',
			),
			)
		);

		$area_template_data = $this->Workspace->Area->find('all', [
			'fields' => [
				'Area.id as area_id', 'Area.workspace_id', 'Area.title', 'Area.description as desc', 'Area.tooltip_text', 'Area.status',
				'TemplateDetail.id as temp_detail_id', 'TemplateDetail.row_no', 'TemplateDetail.col_no', 'TemplateDetail.size_w', 'TemplateDetail.size_h', 'TemplateDetail.elements_counter', 'TemplateDetail.template_id',
			],
			'conditions' => [
				'Area.workspace_id' => $workspace_id,
				'Area.template_detail_id' => $grouped_ids,
			],
			'recursive' => 1,
			'order' => ['TemplateDetail.id ASC', 'TemplateDetail.row_no ASC'],
		]);

		$templateRows = $andConditions = null;

		$in = 0;
		foreach ($area_template_data as $row_id => $row_templates) {
			$area_detail = $row_templates['Area'];
			$temp_detail = $row_templates['TemplateDetail'];

			if ($temp_detail['size_w'] > 0 && $temp_detail['size_h'] > 0) {
				$row_no = $temp_detail['row_no'];
				$area_templates = array_merge($temp_detail, $area_detail);
				if (isset($elements) && !empty($elements)) {
					$area_templates['elements'] = $elements;
				}
				$templateRows[$row_no][] = $area_templates;
			}
		}

		$data['templateRows'] = $templateRows;

		$this->setJsVar('workspace_id', $workspace_id);
		$this->setJsVar('project_id', $project_id);

		$this->setJsVar('template_id', $workspace['Workspace']['template_id']);
		$this->set('template_id', $workspace['Workspace']['template_id']);

		$this->set('project_id', $project_id);
		$this->set('workspace_id', $workspace_id);

		$data['workspace'] = $workspace;
		$data['page_heading'] = 'Tasks';
		$data['page_subheading'] = 'View Tasks in this Workspace';

		$this->set('workspace_detail', $workspace);
		$this->set('data', $data);
		$this->set('in', $in);

		// Get project detail
		$projects = $cat_crumb = null;
		if (isset($project_id) && !empty($project_id)) {
			$projects = $this->Project->find('first', ['conditions' => ['Project.id' => $project_id],'fields'=>['Project.title'], 'recursive' => -1]);
		}

		$project_title = $projects['Project']['title'];

		$crumb = [
			'Summary' => [
				'data' => [
					'url' => '/projects/index/' . $project_id,
					'class' => 'tipText',
					'title' => $project_title,
					'data-original-title' => $project_title,
				],
			],
			'last' => [
				'data' => [
					'title' => htmlentities($workspace['Workspace']['title'], ENT_QUOTES, "UTF-8"),
					'data-original-title' => htmlentities($workspace['Workspace']['title'], ENT_QUOTES, "UTF-8"),
				],
			],
		];


		$this->set('crumb', $crumb);

		/*$get_project_task_risks_count = $this->get_project_task_risks_count($project_id, $workspace_id);
		if ( $get_project_task_risks_count > 0) {
			$riskElementCnt = $get_project_task_risks_count;
		} else {
			$riskElementCnt = 0;
		}*/
		// $this->set('riskelementcount', $riskElementCnt);


		$project_type = $this->CheckProjectType($project_id, $this->user_id);
		$this->set('project_type', $project_type);

		// $wsp_activities_tasks = $this->objView->loadHelper('Permission')->wsp_activities_tasks($project_id, $workspace_id, 0, $this->activity_offset, 'ORDER BY el_status ASC', ' 1', null, null);
		// $this->set('wsp_activities_tasks', $wsp_activities_tasks);
		$this->set('limit', $this->activity_offset);
		$this->setJsVar('activity_offset', $this->activity_offset);

		// workspace team
		// $team_param['workspace_id'] = $workspace_id;
		// $team_param['project_id'] = $project_id;
		// $team_param['limit'] = $this->wsp_team_offset;
		// $teams['wsp_teams'] = $this->objView->loadHelper('Scratch')->wsp_teams($team_param);
		// $this->set($teams);
		$this->set('wsp_team_offset', $this->wsp_team_offset);
		$this->setJsVar('wsp_team_offset', $this->wsp_team_offset);
	}

	public function wsp_teams(){

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				// pr($post, 1);

				$teams['workspace_id'] = $post['workspace_id'];
				$teams['project_id'] = $post['project_id'];
				$teams['limit'] = $this->wsp_team_offset;
				$teams['wsp_teams'] = $this->objView->loadHelper('Scratch')->wsp_teams($teams);
				$this->set($teams);

			}
			$this->render('/Projects/sections/wsp_team');
		}
	}

	public function wsp_activities(){

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				// pr($post, 1);

				$workspace_id = $post['workspace_id'];
				$project_id = $post['project_id'];

				$wsp_activities_tasks = $this->objView->loadHelper('Permission')->wsp_activities_tasks($project_id, $workspace_id, 0, $this->activity_offset, 'ORDER BY el_status ASC', ' 1', null, null);
				$this->set('wsp_activities_tasks', $wsp_activities_tasks);
				$this->set('workspace_id', $workspace_id);
				$this->set('project_id', $project_id);

			}
			$this->render('/Projects/sections/wsp_activities');
		}
	}



	public function wsp_task_people($workspace_id = null, $project_id = null) {

		if ($this->request->is('get')) {

			$this->layout = 'ajax';

			$view = new View();
			$ViewModel = $view->loadHelper('ViewModel');

			$data = null;
			if (isset($workspace_id) && !empty($workspace_id)) {

				$data = $ViewModel->workspacePeople($workspace_id);

			}
			if( isset($data) && !empty($data) ){
				$this->set('data', $data);
			} else {
				$this->set('data', $data);
			}
			$this->set('workspace_id', $workspace_id);
			$this->set('project_id', $project_id);
		}

	}

	public function get_project_task_risks_count($project_id = null, $workspace_id = null) {
		$this->loadModel('RmElement');
		$this->loadModel('UserPermission');
		$e_permission = null;
		$project_level = 0;

		$user_id = $this->Session->read('Auth.User.id');
		/* $p_permission = $this->objView->loadHelper('Common')->project_permission_details($project_id, $user_id);
		$user_project = $this->objView->loadHelper('Common')->userproject($project_id, $user_id);
		$group_id = $this->objView->loadHelper('Group')->GroupIDbyUserID($project_id, $user_id);
		$project_workspace = get_project_workspace($project_id, true); */

		if (isset($workspace_id) && !empty($workspace_id)) {

			$e_permission_list = $this->UserPermission->find('all',
					array(
						'conditions'=>array('UserPermission.project_id'=>$project_id,'UserPermission.workspace_id'=>$workspace_id,'UserPermission.user_id'=>$user_id,'UserPermission.element_id !='=> '' ),
						'fields'=>'UserPermission.element_id'
						)
				);
			$e_permission = Set::extract($e_permission_list, '/UserPermission/element_id');
		}

		$user_id = $this->Session->read('Auth.User.id');
		$user_risk = user_risks($user_id);
		$risk_elements = $this->RmElement->find('all', [
			'conditions' => [
				'RmElement.project_id' => $project_id,
				'RmElement.element_id' => $e_permission,
			],
			'fields' => 'RmElement.rm_detail_id',
			'recursive' => 1,
		]);

		$risk_elementscnt = 0;
		$risk_elements_riskids = array();
		if (isset($risk_elements) && !empty($risk_elements)) {

			$risk_elements_riskids = Set::extract($risk_elements, '/RmElement/rm_detail_id');
			if (isset($risk_elements_riskids) && !empty($risk_elements_riskids)) {
				$risk_elements_riskids = array_unique($risk_elements_riskids);
				if ($user_risk) {
					$risk_elements_riskids = array_intersect($user_risk, $risk_elements_riskids);
				}
				$risk_elementscnt = array_unique($risk_elements_riskids);
			}
		}

		return (isset($risk_elementscnt) && !empty($risk_elementscnt)) ? count($risk_elementscnt) : 0;

	}

	public function wsp_task_risks($project_id, $workspace_id = null) {

		$this->loadModel('RmElement');
		$this->loadModel('UserPermission');
		$e_permission = array();
		$project_level = 0;

		$user_id = $this->Session->read('Auth.User.id');
		if (isset($workspace_id) && !empty($workspace_id)) {

			$e_permission_list = $this->UserPermission->find('all',
					array(
						'conditions'=>array('UserPermission.project_id'=>$project_id,'UserPermission.workspace_id'=>$workspace_id,'UserPermission.user_id'=>$user_id,'UserPermission.element_id !='=> '' ),
						'fields'=>'UserPermission.element_id'
						)
				);
			$e_permission = Set::extract($e_permission_list, '/UserPermission/element_id');

		}

		$risk_elements = array();
		$risk_elements = $this->RmElement->find('all', [
			'conditions' => [
				'RmElement.project_id' => $project_id,
			],
			'fields' => 'element_id',
			'recursive' => -1,
		]);

		$intersectElements = array();
		if (isset($risk_elements) && !empty($risk_elements)) {

			$risk_elements = Set::extract($risk_elements, '/RmElement/element_id');
			$risk_elements = array_unique($risk_elements);

			if (!empty($risk_elements) && !empty($risk_elements)) {
				$intersectElements = array_intersect($e_permission, $risk_elements);
			}

		}

		$this->set('intersectElements', $intersectElements);
		$this->set('project_id', $project_id);
		$this->set('workspace_id', $workspace_id);
	}


	/************************************************************************
	*************** Start Get People from User Permission View **************
	*************************************************************************/

	public function sample() {

		$this->layout ='gantt';

	}


	public function tasks_signoff($project_id = null){

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			if( isset($project_id) && !empty($project_id) ){

				$this->set('sign_off',1);
				$this->set('project_id',$project_id);
				$this->render('/Projects/partials/task_signoff_model');

			}
		}
	}


	public function show_signoff($project_id = null){

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			if( isset($project_id) && !empty($project_id) ){

				$this->set('project_id',$project_id);
				$comment =$this->SignoffProject->find('first',array(
					'conditions'=> array('SignoffProject.project_id'=> $project_id )
					)
				);

				$userDetail = get_user_data($comment['SignoffProject']['user_id']);

				if( isset($comment) && !empty($comment) ){
					$this->set('comment',$comment);
					$this->set('userDetail',$userDetail);
				}
				$this->render('/Projects/partials/show_signoff_model');

			}
		}
	}

	public function save_signoff_comment(){

		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			$this->autoRender = false;
			$response = [
				'success' => false,
				'msg' => '',
				'content' => null,
			];

			$task_evidence = '';
			$signoff_type = 'project';
			$task_comment = '';
			$project_id = '';
			$current_user_id = $this->Auth->user('id');

			if ($this->request->is('post') || $this->request->is('put')) {

				if( isset($this->request->data['signoff_comment']) && !empty($this->request->data['signoff_comment']) ){
					$task_comment = $this->request->data['signoff_comment'];
				}

				if( isset($this->request->data['project_id']) && !empty($this->request->data['project_id']) ){
					$project_id = $this->request->data['project_id'];
				}

				$evidence_title = '';
				$check_file = true;
				if( isset($_FILES['file']) && !empty($_FILES['file']['name']) ){


					$sizeLimit = 10; // 10MB
					$folder_url = WWW_ROOT . PROJECT_SIGNOFF_PATH;
					$result = $fileNewName = $upload_object = $upload_detail = null;
					if ($check_file == true) {
						$upload_object = (isset($_FILES['file'])) ? $_FILES['file'] : null;
						//$folder_url .= DS . $element_id;
						if ($upload_object) {
							if (!file_exists($folder_url)) {
								mkdir($folder_url, 0777, true);
							}

							$sizeMB = 0;
							$sizeStr = "";
							$sizeKB = $upload_object['size'] / 1024;
							if (($sizeKB) > 1024) {
								$sizeMB = $sizeKB / 1024;
								$sizeStr = number_format($sizeMB, 2) . " MB";
							} else {
								$sizeStr = number_format($sizeKB, 2) . " KB";
							}
							$fileExt = substr($upload_object['name'], strripos($upload_object['name'], '.') + 1);
							$fileName = substr($upload_object['name'], 0, strripos($upload_object['name'], '.'));

							if ($sizeMB <= $sizeLimit) {

								if (!is_writable($folder_url)) {
									$result = array(
										'error' => "Server error. Upload directory isn't writable. Please ask server admin to change permissions.",
									);
								}

								// if file exists, change file name with the saved entry of the record id
								$orgFileName = $upload_object['name'];
								$exists_file = $folder_url . DS . $orgFileName;

								if (file_exists($exists_file)) {
									$last_row = $this->SignoffProject->find('first', array(
										'recursive' => '-1',
										'fields' => [
											'id',
										],
										'order' => 'SignoffProject.id DESC',
									));
								}

								$evidence_title = $orgFileName;
								$orgFileName_new = $this->unique_file_name($folder_url,$orgFileName);

								$task_evidence = $fileNewName = $orgFileName_new;
								if (!empty($fileNewName)) {

									$tempFile = $upload_object['tmp_name'];

									$targetFile = $folder_url . DS . $fileNewName;
									$fileSize = true; // filesize($tempFile);

									if (!$fileSize) {
										$result = array(
											'error' => 'File is too large. Please ask server admin to increase the file upload limit.',
										);
									}
									if (empty($result)) {
										move_uploaded_file($tempFile, $targetFile);
									}

									$upload_detail['name'] = $fileNewName;
									$upload_detail['type'] = $upload_object['type'];
									$upload_detail['size'] = $sizeStr;
								}
							} else {

								$check_file = false;
								$response['msg'] = "File size limit exceeded, Please upload a file upto 10MB.";
							}
						}
					}

				//$task_evidence = $_FILES['file']['name'];
				}

				$save_signoff = false;
				if( empty($response['msg']) ){


					if( isset($task_comment) && !empty($task_comment) && isset($task_evidence) && !empty($task_evidence) && isset($project_id) && !empty($project_id) ){

						$save_signoff = true;

					} else if(isset($task_comment) && !empty($task_comment) && !isset($task_evidence) && empty($task_evidence) && isset($project_id) && !empty($project_id)){

						$save_signoff = true;

					} else {

						if( isset($task_comment) && !empty($task_comment) && isset($project_id) && !empty($project_id) ) {
							$save_signoff = true;
						} else {
							$save_signoff = false;
						}
					}
				} else {
					$save_signoff == false;
				}

				if( $save_signoff == true ){

					//exits entry delete
					$del = array('project_id'=>$project_id);
					$this->SignoffProject->deleteAll($del);


					$this->request->data['SignoffProject']['id'] = null;
					$this->request->data['SignoffProject']['user_id'] = $current_user_id;
					$this->request->data['SignoffProject']['project_id'] = $project_id;
					$this->request->data['SignoffProject']['signoff_type'] = $signoff_type;
					$this->request->data['SignoffProject']['task_comment'] = $task_comment;
					$this->request->data['SignoffProject']['task_evidence'] = $task_evidence;
					$this->request->data['SignoffProject']['evidence_title'] = $evidence_title;

					if( $this->SignoffProject->save($this->request->data['SignoffProject']) ){

						$this->request->data['Project']['updated_user_id'] = $this->Session->read("Auth.User.id");

						//pr($this->request->data['Project']);

						if (isset($this->request->data['Project']['sign_off']) && $this->request->data['Project']['sign_off'] == 1) {
							$this->request->data['Project']['task_type'] = 'reopen';
							$projectTaskStauts = 'sign_off';

						} else if (isset($this->request->data['Project']['sign_off']) && $this->request->data['Project']['sign_off'] == 0) {
							$this->request->data['Project']['task_type'] = 'sign_off';
							$projectTaskStauts = 'reopen';
						}
						$this->request->data['Project']['create_activity'] = 1;
						$post = $this->request->data['Project'];
						// pr($post, 1);
						if (isset($post['id']) && !empty($post['id'])) {
							$id = $post['id'];
							$project_id = $post['id'];
							$this->Project->id = $id;

							if (!$this->Project->exists()) {
								throw new NotFoundException(__('Invalid detail'), 'error');
							}
							// SIGNOFF DATE
							$post['sign_off_date'] = date('Y-m-d');

							if ($this->Project->save($post)) {

								$this->Project->updateAll(
									array("Project.create_activity" => 0),
									array("Project.id" => $id)
								);

								$this->loadModel('RmDetail');
								$this->RmDetail->updateAll(
									array("RmDetail.status" => 3, "RmDetail.modified" => '"'.date("Y-m-d H:i:s").'"'),
									array("RmDetail.project_id" => $id)
								);


								//$this->Element->save($post)
								// Get Project Id with Element id; Update Project modified date
								//$this->update_project_modify($id);

								//======== Start Email Notification setting ===========

								// ====== Start == annotation email when added by user =====
								$projectdetail = $this->Project->findById($project_id);
								$project_name = '';
								if (isset($projectdetail['Project']['title']) && !empty($projectdetail['Project']['title'])) {
									$project_name = ucfirst(strip_tags($projectdetail['Project']['title']));
								}

								$this->projectSignoffEmail($project_name, $projectdetail['Project']['id'], $projectTaskStauts);

								// =====End == annotation email when added by user ==========

								//============ End Email Notification setting ==============


								if (SOCKET_MESSAGES) {
									$current_user_id = $this->Auth->user('id');
									App::import('Controller', 'Risks');
									$Risks = new RisksController;
									$project_all_users = $Risks->get_project_users($id, $current_user_id);
									if (isset($project_all_users) && !empty($project_all_users)) {
										if (($key = array_search($current_user_id, $project_all_users)) !== false) {
											unset($project_all_users[$key]);
										}
									}

									$s_open_users = $r_open_users = null;
									if (isset($project_all_users) && !empty($project_all_users)) {
										foreach ($project_all_users as $key => $value) {
											if (web_notify_setting($value, 'project', 'project_complete')) {
												$s_open_users[] = $value;
											}
											if (web_notify_setting($value, 'project', 'project_reopen')) {
												$r_open_users[] = $value;
											}
										}
									}
									$userDetail = get_user_data($current_user_id);
									$heading = (isset($post['sign_off']) && $post['sign_off'] == 1) ? 'Project complete' : 'Project re-opened';
									$content = [
										'notification' => [
											'type' => 'project_complete',
											'created_id' => $current_user_id,
											'project_id' => $projectdetail['Project']['id'],
											'creator_name' => $userDetail['UserDetail']['full_name'],
											'subject' => $heading,
											'heading' => 'Project: ' . strip_tags($project_name),
											'sub_heading' => '',
											'date_time' => timezoneTimeConvertor($this->user_id, date('Y-m-d g:iA'), $format = 'd M Y g:iA') . user_country($this->user_id),
										],
										//'received_users' => array_values($ele_users),
									];
									if (isset($post['sign_off']) && $post['sign_off'] == 1) {
										if (is_array($s_open_users)) {
											$content['received_users'] = array_values($s_open_users);
										}
									} else {
										if (is_array($r_open_users)) {
											$content['received_users'] = array_values($r_open_users);
										}
									}
									$response['content']['socket'] = $content;
								}


								$response['success'] = true;
								$response['msg'] = 'You have been signed off successfully.';
								// $response['content'] = [];
							} else {
								$response['msg'] = 'Signing off could not be completed. Please try again later.';
							}
						}
					}
				}
			}
			echo json_encode($response);
			exit();
		}
	}

	public function download_signoff($id = null) {

		if (isset($id) && !empty($id)) {

			$data = $this->SignoffProject->findById($id);
			if (empty($data)) {
				throw new NotFoundException();
			}

			$response = [
				'success' => false,
				'content' => null,
			];

			if (isset($data) && !empty($data)) {
				// Send file as response
				$response['content'] = PROJECT_SIGNOFF_PATH  . DS . $data['SignoffProject']['task_evidence'];
				$response['success'] = true;
			}
			$this->autoRender = false;

			return $this->response->file($response['content'], array('download' => true));

		}

	}


	public function unique_file_name($path, $filename) {
		//echo $path.' '.$filename;
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
			$newname = $name . '_(' . $counter . ')' . $ext;
			$newpath = $path . '/' . $newname;
			$counter++;
		}

		return $newname;
	}



	/********************************************************************/

	public function project_progress_bar() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$response = [
				'success' => false,
				'msg' => '',
				'content' => null,
			];

			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;

				$project_id = (isset($post['project_id']) && !empty($post['project_id'])) ? $post['project_id'] : null;

				$projects = $this->Project->find('first', ['recursive' => -1, 'conditions' => ['Project.id' => $project_id]]);

				$project_type = $this->CheckProjectType($project_id, $this->user_id);
				$this->set('project_type', $project_type);

				$this->set('project_id', $project_id);
				$this->set('projects', $projects);

			}

		}

		$this->render(DS . 'Projects' . DS . 'partials' . DS . 'project_progress_bar');
	}


	public function wsp_progress_bar() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$response = [
				'success' => false,
				'msg' => '',
				'content' => null,
			];

			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;

				$project_id = (isset($post['project_id']) && !empty($post['project_id'])) ? $post['project_id'] : null;
				$workspace_id = (isset($post['workspace_id']) && !empty($post['workspace_id'])) ? $post['workspace_id'] : null;

				$project_type = $this->CheckProjectType($project_id, $this->user_id);
				$this->set('project_type', $project_type);

				$this->set('project_id', $project_id);
				$this->set('workspace_id', $workspace_id);

			}

		}

		$this->render(DS . 'Projects' . DS . 'partials' . DS . 'wsp_progress_bar');
	}

	public function lists() {
		$this->layout = 'inner';

		$this->set('title_for_layout', __('My Work', true));
		$this->set('page_heading', 'My Work');
		$this->set('page_subheading', 'View your programs, project work and related items');

		$user_id = $this->user_id;

		$crumb = [
			'last' => [
				'data' => [
					'title' => 'My Work',
					'data-original-title' => 'My Work',
				],
			],
		];

		$query_params = ['limit' => $this->program_offset];
		// $programs_list = $this->objView->loadHelper('Scratch')->programs_list($query_params);

		// $projects = $this->objView->loadHelper('Permission')->project_listing(0, $this->listing_offset );

		// $this->set('programs_list', $programs_list);
		// $this->set('projects', $projects);
		$this->set('crumb', $crumb);
		$this->set('limit', $this->listing_offset);
		$this->setJsVar('listing_offset', $this->listing_offset);
		$this->setJsVar('program_offset', $this->program_offset);

		$sel_tab = '';
		if(isset($this->params['named']) && !empty($this->params['named'])){
			if(isset($this->params['named']['tab']) && !empty($this->params['named']['tab'])){
				$sel_tab = $this->params['named']['tab'];
			}
		}
		$this->set('sel_tab', $sel_tab);
		$this->setJsVar('sel_tab', $sel_tab);
	}

	protected function find_data($filter = null, $page = 0, $sorting = [], $filters = []) {
		$limit_query = ' LIMIT '.$this->listing_offset;
		if(isset($page) && !empty($page)){
			$limit_query = " LIMIT $page, " . $this->listing_offset;
		}

		$filter_query = ' 1';
		if(isset($filter) && !empty($filter)){
			$seperator = '^';
			$search_str= Sanitize::escape(like($filter, $seperator ));
			$filter_query = " (ptitle LIKE '%$search_str%' ESCAPE '$seperator') ";
		}

		$order = 'ORDER BY prj_status, ptitle ASC';
		if( isset($sorting) && !empty($sorting) ){

			if( isset($sorting['coloumn']) && !empty($sorting['coloumn']) && isset($sorting['order']) && !empty($sorting['order']) ){
				$order = "ORDER BY ".$sorting['coloumn']." ".$sorting['order'];
			}

		}

		return $this->objView->loadHelper('Permission')->project_listing($page, $this->listing_offset, $order, $filter_query, $filters);
	}

	public function filter_list() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				// pr($post, 1);
				$page = (isset($post['page']) && !empty($post['page'])) ? $post['page'] : 0;
				$order = (isset($post['order']) && !empty($post['order'])) ? $post['order'] : 'ASC';
				$coloumn = (isset($post['coloumn']) && !empty($post['coloumn'])) ? $post['coloumn'] : 'prj_status';
				$search_text = (isset($post['search_text']) && !empty($post['search_text'])) ? $post['search_text'] : '';

				$programs = (isset($post['programs']) && !empty($post['programs'])) ? $post['programs'] : '';
				$projects = (isset($post['projects']) && !empty($post['projects'])) ? $post['projects'] : '';
				$status = (isset($post['status']) && !empty($post['status'])) ? $post['status'] : '';
				$rag = (isset($post['rag']) && !empty($post['rag'])) ? $post['rag'] : '';
				$roles = (isset($post['roles']) && !empty($post['roles'])) ? $post['roles'] : '';
				$task_types = (isset($post['task_types']) && !empty($post['task_types'])) ? $post['task_types'] : '';
				$members = (isset($post['members']) && !empty($post['members'])) ? $post['members'] : '';
				$filters = ['programs' => $programs, 'projects' => $projects, 'status' => $status, 'rag' => $rag, 'roles' => $roles, 'task_types' => $task_types, 'members' => $members];
				// $projects = $this->find_data();
				$projects = $this->find_data($search_text, $page, ['coloumn' => $coloumn, 'order' => $order], $filters);
				$this->set('projects', $projects);

			}
			$this->render('/Projects/sections/listing_rows');
		}

	}

	public function listing_row() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				$project_id = (isset($post['project_id']) && !empty($post['project_id'])) ? $post['project_id'] : null;

				$projects = $this->objView->loadHelper('Permission')->listing_row($project_id);
				$this->set('projects', $projects);

			}
			$this->render('/Projects/sections/listing_row');
		}

	}

	public function view_project($project_id = null, $tab = NULL) {
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			$user_id = $this->user_id;

			$viewData['project_id'] = $project_id;
			$viewData['tab'] = $tab;
			$this->set($viewData);
			$this->render('/Projects/sections/view_project');
		}
	}

	public function filter_projects(){

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$user_id = $this->user_id;
			$viewData['projects'] = $this->Project->query("SELECT
						p.id, p.title
					FROM user_permissions up
					INNER JOIN projects p ON p.id = up.project_id
					WHERE up.user_id = $user_id AND up.workspace_id IS NULL
					ORDER BY p.title ASC ");

			$viewData['programs'] = $this->Project->query("SELECT
			      	prog.id, prog.name
			  	FROM
			        programs prog
			    LEFT JOIN program_users pu ON
			        pu.program_id = prog.id
			    WHERE
			    	prog.created_by = $user_id
			    	OR pu.user_id = $user_id

		    	GROUP BY prog.id
		    	ORDER BY prog.name ASC ");

			$this->set($viewData);
			$this->render('/Projects/sections/filter_projects');
		}

	}

	public function update_project_color() {

		if ($this->request->isAjax()) {

			$response = [
				'success' => false
			];

			$this->layout = 'ajax';
			$this->autoRender = false;

			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;

				$this->Project->id = $post['project_id'];
				if ($this->Project->saveField('color_code', $post['color_code'])) {

					$response['success'] = true;
				}
			}

			echo json_encode($response);
			exit();
		}
	}

	public function tab_paging_count() {
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';

			$data = [];
			$count = 0;
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				// pr($post, 1);
				$type = $post['type'];

				$order = (isset($post['order']) && !empty($post['order'])) ? $post['order'] : 'ASC';
				$coloumn = (isset($post['coloumn']) && !empty($post['coloumn'])) ? $post['coloumn'] : 'prj_status';
				$search_text = (isset($post['search_text']) && !empty($post['search_text'])) ? $post['search_text'] : '';

				$programs = (isset($post['programs']) && !empty($post['programs'])) ? $post['programs'] : '';
				$projects = (isset($post['projects']) && !empty($post['projects'])) ? $post['projects'] : '';
				$status = (isset($post['status']) && !empty($post['status'])) ? $post['status'] : '';
				$rag = (isset($post['rag']) && !empty($post['rag'])) ? $post['rag'] : '';
				$roles = (isset($post['roles']) && !empty($post['roles'])) ? $post['roles'] : '';
				$task_types = (isset($post['task_types']) && !empty($post['task_types'])) ? $post['task_types'] : '';
				$members = (isset($post['members']) && !empty($post['members'])) ? $post['members'] : '';
				$filters = ['programs' => $programs, 'projects' => $projects, 'status' => $status, 'rag' => $rag, 'roles' => $roles, 'task_types' => $task_types, 'members' => $members];

				$filter_query = '';
				if(isset($search_text) && !empty($search_text)){
					$seperator = '^';
					$search_str = Sanitize::escape(like($search_text, $seperator ));
					$filter_query = " AND (ptitle LIKE '%$search_str%' ESCAPE '$seperator') ";
				}

				$data = $this->objView->loadHelper('Permission')->list_count($filter_query, $filters);
				$count = count($data);

			}
			echo json_encode($count);
			exit;
		}
	}

	public function project_documents($project_id = null) {
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			$this->set('project_id', $project_id);
			$this->render('/Projects/sections/project_documents');
		}
	}

	public function upload_project_doc() {

		if ($this->request->isAjax()) {

            $this->layout = 'ajax';
			$this->autoRender = false;
			$response = [
				'success' => false,
				'content' => null,
			];

			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;
				$folder_url = WWW_ROOT . 'uploads/project_documents/';
				$upload_object = null;

				if ( isset($this->request->data['ProjectDocument']['filename']) && !empty($this->request->data['ProjectDocument']['filename']) ) {
					$upload_object = $this->request->data['ProjectDocument']['filename'];
				}

				if ($upload_object) {
					if (!file_exists($folder_url)) {
						mkdir($folder_url, 0777, true);
					}

					// if file exists, change file name with the saved entry of the record id
					$orgFileName = $upload_object['name'];
					$exists_file = $folder_url . DS . $orgFileName;

					if (!empty($orgFileName)) {

						$tempFile = $upload_object['tmp_name'];

						$unique_file_name = $this->unique_file_name($folder_url, $orgFileName);
						$targetFile = $folder_url . DS . $unique_file_name;

						if (move_uploaded_file($tempFile, $targetFile)) {
							$max = $this->Project->query('SELECT MAX(sort_order) sort_order FROM project_documents WHERE project_id = "'.$post['ProjectDocument']['project_id'].'"');
							$max_order = ( !empty($max[0][0]['sort_order']) ) ? ($max[0][0]['sort_order'] + 1) : 1;
							$data = [
								'project_id' => $post['ProjectDocument']['project_id'],
								'user_id' => $this->user_id,
								'modified_user_id' => $this->user_id,
								'title' => $post['title'],
								'summary' => $post['summary'],
								'filename' => $unique_file_name,
								'is_sharers' => $post['is_sharers'],
								'sort_order' => $max_order,
							];
							$this->loadModel('ProjectDocument');
							if($this->ProjectDocument->save($data)){
								$response['success'] = true;
								$response['content'] = $unique_file_name;
							}
							// SAVE TO ACTIVITY
							$act_data = [
								'project_id' => $post['ProjectDocument']['project_id'],
								'updated_user_id' => $this->user_id,
								'message' => 'Project document created',
								'updated' => date("Y-m-d H:i:s"),
							];
							$this->loadModel('ProjectActivity');
							$this->ProjectActivity->id = null;
							$this->ProjectActivity->save($act_data);
						}
					}

				}
				echo json_encode($response);
				exit();
			}
		}
	}

	public function project_document_list($project_id = null) {
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			$projectPermitType = $this->objView->loadHelper('ViewModel')->projectPermitType($project_id, $this->user_id);
			$sign_off = $this->Project->query("SELECT sign_off FROM projects WHERE id = $project_id");
			// pr($sign_off, 1);
			$list = $this->Project->query("SELECT * FROM project_documents WHERE project_id = $project_id ORDER BY sort_order ASC");
			$this->set('projectPermitType', $projectPermitType);
			$this->set('list', $list);
			$this->set('sign_off', $sign_off);
			$this->set('project_id', $project_id);
			$this->render('/Projects/sections/project_document_list');
		}
	}

	public function project_doc_sharing() {

		if ($this->request->isAjax()) {

            $this->layout = 'ajax';
			$this->autoRender = false;
			$response = [
				'success' => false,
				'content' => null,
			];

			if ($this->request->is('post') || $this->request->is('put')) {
				$this->loadModel('ProjectDocument');
				$post = $this->request->data;
				$this->ProjectDocument->id = $post['id'];
				$this->ProjectDocument->saveField('is_sharers', $post['is_sharers']);
				$this->ProjectDocument->saveField('modified_user_id', $this->user_id);
				$response['success'] = true;
				// SAVE TO ACTIVITY
				$act_data = [
					'project_id' => $post['project_id'],
					'updated_user_id' => $this->user_id,
					'message' => 'Project document updated',
					'updated' => date("Y-m-d H:i:s"),
				];
				$this->loadModel('ProjectActivity');
				$this->ProjectActivity->id = null;
				$this->ProjectActivity->save($act_data);
			}
			echo json_encode($response);
			exit();
		}
	}

	public function project_doc_sorting() {

		if ($this->request->isAjax()) {

            $this->layout = 'ajax';
			$this->autoRender = false;
			$response = [
				'success' => false,
				'content' => null,
			];

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				$this->loadModel('ProjectDocument');
				$this->ProjectDocument->id = $post['current_id'];
				$this->ProjectDocument->saveField('sort_order', $post['next_order']);
				$this->ProjectDocument->saveField('modified_user_id', $this->user_id);
				$this->ProjectDocument->id = $post['next_id'];
				$this->ProjectDocument->saveField('sort_order', $post['current_order']);
				$this->ProjectDocument->saveField('modified_user_id', $this->user_id);
				$response['success'] = true;
				// SAVE TO ACTIVITY
				$act_data = [
					'project_id' => $post['project_id'],
					'updated_user_id' => $this->user_id,
					'message' => 'Project document updated',
					'updated' => date("Y-m-d H:i:s"),
				];
				$this->loadModel('ProjectActivity');
				$this->ProjectActivity->id = null;
				$this->ProjectActivity->save($act_data);
			}
			echo json_encode($response);
			exit();
		}
	}

	public function project_doc_remove() {

		if ($this->request->isAjax()) {

            $this->layout = 'ajax';
			$this->autoRender = false;
			$response = [
				'success' => false,
				'content' => null,
			];

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				$this->loadModel('ProjectDocument');
				$this->ProjectDocument->delete(['ProjectDocument.id' => $post['id']], false);
				$response['success'] = true;
				// SAVE TO ACTIVITY
				$act_data = [
					'project_id' => $post['project_id'],
					'updated_user_id' => $this->user_id,
					'message' => 'Project document deleted',
					'updated' => date("Y-m-d H:i:s"),
				];
				$this->loadModel('ProjectActivity');
				$this->ProjectActivity->id = null;
				$this->ProjectActivity->save($act_data);
			}
			echo json_encode($response);
			exit();
		}
	}

	public function project_summary($project_id = null) {
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			$this->set('project_id', $project_id);
			$this->render('/Projects/sections/project_summary');
		}
	}

	public function summary_docs($project_id = null) {
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';

			$this->set('project_id', $project_id);
			$this->render('/Projects/sections/summary_docs');
		}
	}

	public function summary_notes($project_id = null) {
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			// $list = $this->Project->query("SELECT * FROM project_documents WHERE project_id = $project_id ORDER BY sort_order ASC");
			// pr($list, 1);
			// $this->set('list', $list);
			$this->set('project_id', $project_id);
			$this->render('/Projects/sections/summary_notes');
		}
	}

	public function summary_links($project_id = null) {
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			$this->set('project_id', $project_id);
			$this->render('/Projects/sections/summary_links');
		}
	}

	public function summary_competency($project_id = null) {
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			$this->set('project_id', $project_id);
			$this->render('/Projects/sections/summary_competency');
		}
	}

	public function tiles_sort() {

		if ($this->request->isAjax()) {

            $this->layout = 'ajax';
			$this->autoRender = false;
			$response = [
				'success' => false,
				'content' => null,
			];

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				$this->loadModel('ProjectTile');
				if(isset($post['tiles_id']) && !empty($post['tiles_id'])) {
					foreach ($post['tiles_id'] as $key => $value) {
						$this->ProjectTile->id = $value;
						$this->ProjectTile->saveField('sort_order', ($key + 1));
					}
				}
				$response['success'] = true;
			}
			echo json_encode($response);
			exit();
		}
	}

	public function download_project_doc($id = null) {

		if (isset($id) && !empty($id)) {
 			$path = 'uploads/project_documents/';

			$this->loadModel('ProjectDocument');

			$data = $this->ProjectDocument->findById($id);
			if (empty($data)) {
				throw new NotFoundException();
			}

			$response = [
				'success' => false,
				'content' => null,
			];

			if( isset($data) && !empty($data) ) {
				// Send file as response
				$response['content'] = $path . $data['ProjectDocument']['filename'];
				$response['success'] = true;
			}
			$this->autoRender = false;
			return $this->response->file($response['content'], array('download' => true));
		}

	}
	public function project_links($project_id = null) {
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			$this->set('project_id', $project_id);
			$this->render('/Projects/sections/project_links');
		}
	}

	public function project_link_list($project_id = null) {
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			$projectPermitType = $this->objView->loadHelper('ViewModel')->projectPermitType($project_id, $this->user_id);
			$sign_off = $this->Project->query("SELECT sign_off FROM projects WHERE id = $project_id");
			// pr($sign_off, 1);
			$list = $this->Project->query("SELECT * FROM project_links WHERE project_id = $project_id ORDER BY sort_order ASC");
			$this->set('projectPermitType', $projectPermitType);
			$this->set('list', $list);
			$this->set('sign_off', $sign_off);
			$this->set('project_id', $project_id);
			$this->render('/Projects/sections/project_link_list');
		}
	}

	public function add_project_link() {

		if ($this->request->isAjax()) {

            $this->layout = 'ajax';
			$this->autoRender = false;
			$response = [
				'success' => false,
				'content' => null,
			];

			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;

				$max = $this->Project->query('SELECT MAX(sort_order) sort_order FROM project_links WHERE project_id = "'.$post['project_id'].'"');
				$max_order = ( !empty($max[0][0]['sort_order']) ) ? ($max[0][0]['sort_order'] + 1) : 1;
				$data = [
					'project_id' => $post['project_id'],
					'user_id' => $this->user_id,
					'modified_user_id' => $this->user_id,
					'link' => $post['url'],
					'title' => $post['title'],
					'summary' => $post['summary'],
					'is_sharers' => $post['is_sharers'],
					'is_open_new_tab' => $post['open_in_tab'],
					'sort_order' => $max_order,
				];
				$this->loadModel('ProjectLink');
				if($this->ProjectLink->save($data)){
					$response['success'] = true;
					// SAVE TO ACTIVITY
					$act_data = [
						'project_id' => $post['project_id'],
						'updated_user_id' => $this->user_id,
						'message' => 'Project link created',
						'updated' => date("Y-m-d H:i:s"),
					];
					$this->loadModel('ProjectActivity');
					$this->ProjectActivity->id = null;
					$this->ProjectActivity->save($act_data);
				}
				echo json_encode($response);
				exit();
			}
		}
	}

	public function project_link_sharing() {

		if ($this->request->isAjax()) {

            $this->layout = 'ajax';
			$this->autoRender = false;
			$response = [
				'success' => false,
				'content' => null,
			];

			if ($this->request->is('post') || $this->request->is('put')) {
				$this->loadModel('ProjectLink');
				$post = $this->request->data;
				$this->ProjectLink->id = $post['id'];
				$this->ProjectLink->saveField('is_sharers', $post['is_sharers']);
				$this->ProjectLink->saveField('modified_user_id', $this->user_id);
				$response['success'] = true;
				// SAVE TO ACTIVITY
				$act_data = [
					'project_id' => $post['project_id'],
					'updated_user_id' => $this->user_id,
					'message' => 'Project link updated',
					'updated' => date("Y-m-d H:i:s"),
				];
				$this->loadModel('ProjectActivity');
				$this->ProjectActivity->id = null;
				$this->ProjectActivity->save($act_data);
			}
			echo json_encode($response);
			exit();
		}
	}

	public function project_link_sorting() {

		if ($this->request->isAjax()) {

            $this->layout = 'ajax';
			$this->autoRender = false;
			$response = [
				'success' => false,
				'content' => null,
			];

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				$this->loadModel('ProjectLink');
				$this->ProjectLink->id = $post['current_id'];
				$this->ProjectLink->saveField('sort_order', $post['next_order']);
				$this->ProjectLink->saveField('modified_user_id', $this->user_id);
				$this->ProjectLink->id = $post['next_id'];
				$this->ProjectLink->saveField('sort_order', $post['current_order']);
				$this->ProjectLink->saveField('modified_user_id', $this->user_id);
				$response['success'] = true;
				// SAVE TO ACTIVITY
				$act_data = [
					'project_id' => $post['project_id'],
					'updated_user_id' => $this->user_id,
					'message' => 'Project link updated',
					'updated' => date("Y-m-d H:i:s"),
				];
				$this->loadModel('ProjectActivity');
				$this->ProjectActivity->id = null;
				$this->ProjectActivity->save($act_data);
			}
			echo json_encode($response);
			exit();
		}
	}

	public function project_link_remove() {

		if ($this->request->isAjax()) {

            $this->layout = 'ajax';
			$this->autoRender = false;
			$response = [
				'success' => false,
				'content' => null,
			];

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				$this->loadModel('ProjectLink');
				$this->ProjectLink->delete(['ProjectLink.id' => $post['id']], false);
				$response['success'] = true;
				// SAVE TO ACTIVITY
				$act_data = [
					'project_id' => $post['project_id'],
					'updated_user_id' => $this->user_id,
					'message' => 'Project link deleted',
					'updated' => date("Y-m-d H:i:s"),
				];
				$this->loadModel('ProjectActivity');
				$this->ProjectActivity->id = null;
				$this->ProjectActivity->save($act_data);
			}
			echo json_encode($response);
			exit();
		}
	}

	public function project_competencies($project_id = null) {
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			$this->set('project_id', $project_id);
			$this->render('/Projects/sections/project_competencies');
		}
	}

	public function save_project_competencies($project_id = null) {
		$response = ['success' => false];
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				// pr($post, 1);
				$this->ProjectSkill->deleteAll(array('ProjectSkill.project_id' => $project_id), false);

				if(isset($post['skills']) && !empty($post['skills'])){
					$allData = $post['skills'];
					$qry = "INSERT INTO `project_skills` (`project_id`, `skill_id`) VALUES ";
					$qry_arr = [];
    				foreach ($allData as $key => $value) {
    					$qry_arr[] = "('$project_id', '$value')";
    				}
    				$qry .= implode(' ,', $qry_arr);
    				$this->ProjectSkill->query($qry);
				}

				$this->ProjectSubject->deleteAll(array('ProjectSubject.project_id' => $project_id), false);

				if(isset($post['subjects']) && !empty($post['subjects'])){
					$allData = $post['subjects'];
					$qry = "INSERT INTO `project_subjects` (`project_id`, `subject_id`) VALUES ";
					$qry_arr = [];
    				foreach ($allData as $key => $value) {
    					$qry_arr[] = "('$project_id', '$value')";
    				}
    				$qry .= implode(' ,', $qry_arr);
    				$this->ProjectSkill->query($qry);
				}

				$this->ProjectDomain->deleteAll(array('ProjectDomain.project_id' => $project_id), false);

				if(isset($post['domains']) && !empty($post['domains'])){
					$allData = $post['domains'];
					$qry = "INSERT INTO `project_domains` (`project_id`, `domain_id`) VALUES ";
					$qry_arr = [];
    				foreach ($allData as $key => $value) {
    					$qry_arr[] = "('$project_id', '$value')";
    				}
    				$qry .= implode(' ,', $qry_arr);
    				$this->ProjectSkill->query($qry);
				}
				$response['success'] = true;
			}
		}
		echo json_encode($response);
		exit();
	}

	public function tree() {
		$response = ['success' => false];
		if ($this->request->isAjax()) {

			$this->layout = false;
			$html = '';
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				// pr($post, 1);
				$project_id = (isset($post['project']) && !empty($post['project'])) ? $post['project'] : null;
				$view = new View($this, false);
				$view->viewPath = 'Projects/cost';
				$view->set("project_id", $project_id);
				$html = $view->render('tree');
			}
			echo json_encode($html);
			exit;
		}
	}

	public function get_reminder_count() {
		if ($this->request->isAjax()) {

			$this->layout = false;
			$reminder_elements = element_reminder($this->Session->read('Auth.User.id'));
            $rcount = (isset($reminder_elements) && !empty($reminder_elements)) ? count($reminder_elements) : 0;
			echo json_encode($rcount);
			exit;
		}
	}

	public function project_opportunity($project_id = null) {
		$this->layout = 'ajax';

		if ($this->request->isAjax()) {

			$ssd_data = [];
			if (isset($project_id) && !empty($project_id)) {

				$qry = "SELECT

					( SELECT JSON_ARRAYAGG(JSON_OBJECT( 'id', organizations.id, 'title', organizations.name)) AS JSON FROM organizations ) as all_org,

					( SELECT JSON_ARRAYAGG(JSON_OBJECT( 'id', project_opp_orgs.organization_id)) AS JSON FROM project_opp_orgs WHERE project_opp_orgs.project_id = $project_id ) as selected_org

					 ";
				$ssd_data = $this->Project->query($qry);

			} else {
				$qry = "SELECT

						( SELECT JSON_ARRAYAGG(JSON_OBJECT( 'id', organizations.id, 'title', organizations.name)) AS JSON FROM organizations ) as all_org

						 ";
				$ssd_data = $this->Project->query($qry);
			}

			$this->set('ssd_data', $ssd_data);
			$this->set('project_id', $project_id);

			$this->render('/Projects/sections/project_opportunity');
		}
	}

	public function save_org_opportunity(){

		$response = ['success' => false, 'msg'=>null, 'opp_count'=>0];
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				$this->loadModel('ProjectOppOrg');

				if( isset($post['org_id']) && !empty($post['org_id']) &&  isset($post['project_id']) && !empty($post['project_id'])  ){


					$org_data = array();
					foreach( $post['org_id'] as $org_val ){
						$org_data[] = array('ProjectOppOrg'=>['organization_id'=> $org_val, 'project_id'=> $post['project_id']] );
					}

					$this->ProjectOppOrg->deleteAll(array('ProjectOppOrg.project_id' => $post['project_id']), false);
					if( $this->ProjectOppOrg->saveAll($org_data) ){

						// SAVE TO ACTIVITY
						$task_data = [
							'project_id' => $post['project_id'],
							'updated_user_id' => $this->user_id,
							'message' => 'Project opportunity posted',
							'updated' => date("Y-m-d H:i:s"),
						];
						$this->loadModel('ProjectActivity');
						$this->ProjectActivity->id = null;
						$this->ProjectActivity->save($task_data);

						$opportunity_cnt = $this->ProjectOppOrg->find('count', array(
							'conditions' => array('ProjectOppOrg.project_id' => $post['project_id'])
							)
						);
						if( isset( $opportunity_cnt ) && !empty($opportunity_cnt) ){
							$response['opp_count'] = $opportunity_cnt;
						}

						$response['success'] = true;
						$response['msg'] = 'Project opportunity posted.';
					}

				}
			}
			echo json_encode($response);
			exit();
		}
	}

	public function remove_project_opportunity(){

		$response = ['success' => false, 'msg'=>null, 'opp_count'=>0];
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				$this->loadModel('ProjectOppOrg');

				// if(  isset($post['project_id']) && !empty($post['project_id']) && isset($post['org_id']) && !empty($post['org_id'])  ){
				if(  isset($post['project_id']) && !empty($post['project_id']) ){

					/*
					$org_ids = $post['org_id'];
					$existingOrg = $this->ProjectOppOrg->find('count', array(
						'conditions' =>
							array('ProjectOppOrg.project_id' => $post['project_id'])
						)
					);
					$orgid_list = array();
					if( isset($existingOrg) && !empty($existingOrg)  ){
						foreach($existingOrg as $listval){
							$orgid_list[]=$listval;
						}
					} */
					// if( $this->ProjectOppOrg->deleteAll(array('ProjectOppOrg.project_id' => $post['project_id'], 'ProjectOppOrg.id'=> $orgid_list ), false) )


					if( $this->ProjectOppOrg->deleteAll(array('ProjectOppOrg.project_id' => $post['project_id'] ), false) )
					{

						// SAVE TO ACTIVITY
						$task_data = [
							'project_id' => $post['project_id'],
							'updated_user_id' => $this->user_id,
							'message' => 'Project opportunity unposted',
							'updated' => date("Y-m-d H:i:s"),
						];
						$this->loadModel('ProjectActivity');
						$this->ProjectActivity->id = null;
						$this->ProjectActivity->save($task_data);

						$opportunity_cnt = $this->ProjectOppOrg->find('count', array(
							'conditions' => array('ProjectOppOrg.project_id' => $post['project_id'])
							)
						);
						if( isset( $opportunity_cnt ) && !empty($opportunity_cnt) ){
							$response['opp_count'] = $opportunity_cnt;
						}

						$response['success'] = true;
						$response['msg'] = 'Project opportunity unposted.';
					}

				}
			}
			echo json_encode($response);
			exit();
		}
	}

	public function performance_cost() {

		if ($this->request->isAjax()) {
			$this->layout = false;
			$view = new View($this, false);
			$view->viewPath = 'Projects/sections';
			$data = null;
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				// pr($post, 1);
				$performance_cost = $this->objView->loadHelper('Scratch')->performance_cost($this->user_id, $post['project_id']);
				$data = $performance_cost[0][0]['perform_data'];
				$view->set('data', $data);

			}
			$html = $view->render('project_breakdown_json');
			echo json_encode($html);
			exit();
		}
	}

	public function performance_data() {

		if ($this->request->isAjax()) {
			$this->layout = false;
			$view = new View($this, false);
			$view->viewPath = 'Projects/sections';
			$data = null;
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				$project_id = $post['project_id'];
				$type = $post['type'];
				if($type == 'cost'){
					$performance_cost = $this->objView->loadHelper('Scratch')->performance_cost($this->user_id, $project_id);
					$data = $performance_cost[0][0]['perform_data'];
				}
				else if($type == 'efforts'){
					$performance_efforts = $this->objView->loadHelper('Scratch')->performance_efforts($this->user_id, $project_id);
					$data = $performance_efforts[0][0]['perform_data'];
				}
				else if($type == 'confidence'){
					$performance_confidence = $this->objView->loadHelper('Scratch')->performance_confidence($this->user_id, $project_id);
					$data = $performance_confidence[0][0]['perform_data'];
				}
				else if($type == 'status'){
					$performance_status = $this->objView->loadHelper('Scratch')->performance_status($this->user_id, $project_id);
					$data = $performance_status[0][0]['perform_data'];
				}
				else if($type == 'activity'){
					$performance_activity = $this->objView->loadHelper('Scratch')->performance_activity($this->user_id, $project_id);
					$data = $performance_activity[0][0]['perform_data'];
				}

				$view->set('data', $data);

			}
			$html = $view->render('project_breakdown_json');
			echo json_encode($html);
			exit();
		}
	}

	public function manage_project($project_id = null) {


		// REDIRECT TO PROJECT LIST PAGE IF NO PROJECT SELECTED OR NO PERMISSION OF THIS PROJECT
		if (isset($project_id) && !empty($project_id) ) {
			if(!dbExists('Project', $project_id) || check_project_permission($project_id)){
				$this->redirect(array('controller' => 'projects', 'action' => 'lists'));
			}
		}
		$data = null;

		$this->layout = 'inner';

		$this->loadModel('ProjectType');

		$project_temp_id = 'ideascast_' . $this->Auth->user('id');

		$today = date('Y-m-d h:i:s');
		if (isset($project_id) && !empty($project_id)) {

			$user_id = $this->Auth->user('id');
			$pCondition['UserProject.project_id'] = $project_id;

			#----------- check sharing permissions -----------
			if (has_permissions($project_id)) {
				$conditions['UserProject.user_id'] = $user_id;
			}
			#----------- check sharing permissions -----------

			$this->set('user_id', $user_id);

			$this->setJsVar('project_id', $project_id);


			$this->set('title_for_layout', __('Update Project', true));
			$this->set('text_val', __('Save', true));
			$this->set('project_id', $project_id);
			$userproject = $this->UserProject->find('first', array('conditions' => $pCondition));

			$projectElementType = $this->ProjectElementType->find('list', array('conditions' => array('ProjectElementType.project_id' => $project_id), 'order' => 'title'));
			$projectElementTypeSelected = array();
			$projectElementTypeSelected = $this->ProjectElementType->find('list', array(
					'conditions' => array('ProjectElementType.project_id' => $project_id, 'ProjectElementType.type_status' => 1),
					'fields' => 'ProjectElementType.id',
				)
			);

			if (isset($projectElementType) && !empty($projectElementType)) {

				/*$prt_data = $this->Project->query("SELECT title FROM project_element_types prt WHERE project_id = '$project_id' AND is_custom <> 1");
				if(isset($prt_data) && !empty($prt_data)){
					$prt_data = Set::extract($prt_data, '{n}.prt.title');
				}
				$pt_data = $this->Project->query("SELECT title FROM project_types pt");
				if(isset($pt_data) && !empty($pt_data)){
					$pt_data = Set::extract($pt_data, '{n}.pt.title');
				}
				if( (isset($prt_data) && !empty($prt_data)) && (isset($pt_data) && !empty($pt_data)) ){
					$delete_from = array_diff($prt_data, $pt_data);
					$add_to = array_diff($pt_data, $prt_data);
					if(isset($delete_from) && !empty($delete_from)) {
						$this->Project->query("DELETE FROM project_element_types WHERE title IN('" . implode("', '", $delete_from) . "')");
					}
					if(isset($add_to) && !empty($add_to)) {
						$qry = "INSERT INTO project_element_types (title, project_id, user_id, type_status, created, modified) VALUES ";
						$qry_arr = [];
						foreach ($add_to as $list) {
							$qry_arr[] = "('$list', '$project_id', '$user_id', '0', '$today', '$today')";
						}
						$qry .= implode(' ,', $qry_arr);
        				$this->Project->query($qry);
					}
				}*/
				$projectElementType = $this->ProjectElementType->find('list', array('conditions' => array('ProjectElementType.project_id' => $project_id), 'order' => 'title'));

				$this->set('projectType', $projectElementType);
				$this->set('projectElementTypeSelected', $projectElementTypeSelected);
				$projectElementTypeSelected_tot = ( isset($projectElementTypeSelected) && !empty($projectElementTypeSelected) ) ? count($projectElementTypeSelected) : 0;
				$this->setJsVar('project_task_types', $projectElementTypeSelected_tot );
			} else {
				$projecttypelist = $this->ProjectType->find('list');
				if (isset($projecttypelist) && !empty($projecttypelist)) {
					foreach ($projecttypelist as $ptypelist) {
						$this->request->data['ProjectElementType']['id'] = '';
						$this->request->data['ProjectElementType']['title'] = $ptypelist;
						$this->request->data['ProjectElementType']['project_id'] = $project_id;
						$this->request->data['ProjectElementType']['user_id'] = $user_id;
						$this->ProjectElementType->save($this->request->data['ProjectElementType']);
					}
				}
				$projectElementType = $this->ProjectElementType->find('list', array('conditions' => array('ProjectElementType.project_id' => $project_id), 'order' => 'title'));
				$this->set('projectType', $projectElementType);
				$this->set('projectElementTypeSelected', $projectElementTypeSelected);

			}

		} else {

			$listdomainusers = $this->Common->userDetail($this->Session->read('Auth.User.id'));

			if($listdomainusers['UserDetail']['create_project'] != 1){

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
				}else {

					return $this->redirect(array('controller' => 'projects', 'action' => 'lists', 'admin' => false));

				}

			}

			$user_id = $this->Auth->user('id');

			$projectElementType = $this->ProjectElementTypeTemp->find('count', array('conditions' => array('ProjectElementTypeTemp.project_id' => $project_temp_id, 'ProjectElementTypeTemp.user_id' => $user_id), 'order' => 'title'));

			if (empty($projectElementType) && $projectElementType <= 0) {

				$projecttypelist = $this->ProjectType->find('list');
				if (isset($projecttypelist) && !empty($projecttypelist)) {
					foreach ($projecttypelist as $ptypelist) {
						$this->request->data['ProjectElementTypeTemp']['id'] = '';
						$this->request->data['ProjectElementTypeTemp']['title'] = $ptypelist;
						$this->request->data['ProjectElementTypeTemp']['project_id'] = $project_temp_id;
						$this->request->data['ProjectElementTypeTemp']['user_id'] = $user_id;
						$this->ProjectElementTypeTemp->save($this->request->data['ProjectElementTypeTemp']);
					}
				}

			} else {
				$prt_data = $this->Project->query("SELECT title FROM project_element_type_temps prt WHERE project_id = '$project_temp_id' AND user_id = '$user_id' AND is_custom <> 1");
				if(isset($prt_data) && !empty($prt_data)){
					$prt_data = Set::extract($prt_data, '{n}.prt.title');
				}
				$pt_data = $this->Project->query("SELECT title FROM project_types pt");
				if(isset($pt_data) && !empty($pt_data)){
					$pt_data = Set::extract($pt_data, '{n}.pt.title');
				}
				if( (isset($prt_data) && !empty($prt_data)) && (isset($pt_data) && !empty($pt_data)) ){
					$delete_from = array_diff($prt_data, $pt_data);
					$add_to = array_diff($pt_data, $prt_data);
					if(isset($delete_from) && !empty($delete_from)) {
						$this->Project->query("DELETE FROM project_element_type_temps WHERE title IN('" . implode("', '", $delete_from) . "')");
					}
					if(isset($add_to) && !empty($add_to)) {
						$qry = "INSERT INTO project_element_type_temps (title, project_id, user_id, created, modified) VALUES ";
						$qry_arr = [];
						foreach ($add_to as $list) {
							$qry_arr[] = "('$list', '$project_temp_id', '$user_id', '$today', '$today')";
						}
						$qry .= implode(' ,', $qry_arr);
        				$this->Project->query($qry);
					}
				}

				$projectElementType = $this->ProjectElementTypeTemp->find('list', array('conditions' => array('ProjectElementTypeTemp.project_id' => $project_temp_id, 'ProjectElementTypeTemp.user_id' => $user_id), 'order' => 'title'));
				if (isset($this->request->data['ProjectElementType']) && !empty($this->request->data['ProjectElementType']['id'])) {
					$this->request->data['ProjectElementType'] = $this->request->data['ProjectElementType']['id'];
					$this->set('projectElementTypeSelected', $this->request->data['ProjectElementType']);
				} else {
					$this->set('projectElementTypeSelected', array());
				}
			}

			$projectElementTypelist = $this->ProjectElementTypeTemp->find('all', array('conditions' => array('ProjectElementTypeTemp.project_id' => $project_temp_id, 'ProjectElementTypeTemp.user_id' => $user_id), 'order' => 'title'));

			// isko uncomment mat karna
			//unset($_SESSION['data']);

			//pr($projectElementTypelist,1);
			$this->set('projectType', $projectElementTypelist);
			$this->set('title_for_layout', __('New Project', true));
			$this->set('text_val', __('Save', true));
			$this->set('page_heading', __('New Project', true));

		}




		if ($this->request->is('post') || $this->request->is('put')) {
			// pr($this->request->data, 1);

			if (isset($this->request->data['Project']['title']) && !empty($this->request->data['Project']['title'])) {
				$content = $this->request->data['Project']['title'];
				$string = htmlentities($content, null, 'utf-8');

				$content = html_entity_decode($content);
				$this->request->data['Project']['title'] = substr($content, 0, 100);
			}

			if (isset($this->request->data['Project']['objective']) && !empty($this->request->data['Project']['objective'])) {
				$content = $this->request->data['Project']['objective'];

				$content = html_entity_decode($content);

				$this->request->data['Project']['objective'] = substr($content, 0, 500);
			}

			if (isset($this->request->data['Project']['description']) && !empty($this->request->data['Project']['description'])) {

				$content = $this->request->data['Project']['description'];

				$content = html_entity_decode($content);
				$this->request->data['Project']['description'] = substr($content, 0, 500);

			}

			$selTypes = custom_type_used($project_id);

			if ((!isset($this->request->data['ProjectElementType']) || empty($this->request->data['ProjectElementType']))) {
				$this->request->data['error']['eletasktype'] = 'At least one Task Type is required';
				$this->ProjectElementType->validationErrors['id'] = 'At least one Task Type is required';
			} else {
				$this->request->data['error']['eletasktype'] = '';
			}

			if (isset($selTypes) && !empty($selTypes)) {

				$this->request->data['error']['eletasktype'] = '';
			}

			$this->Project->set($this->request->data);

			if ($this->Project->validates()) {
				// pr($this->Project->validationErrors, 1);

				if (isset($this->request->data['error']['eletasktype']) && !empty($this->request->data['error']['eletasktype'])) {

					$_SESSION['data'] = $this->request->data;

					$this->redirect(array('controller' => 'projects', 'action' => 'manage_project', $project_id));
				}

				if (!isset($this->request->data['UserProject']['id']) || empty($this->request->data['UserProject']['id'])) {
					$this->request->data['UserProject']['user_id'] = $this->user_id;
					$this->request->data['Project']['created_by'] = $this->user_id;
				}

				$this->request->data['Project']['updated_user_id'] = $this->Session->read("Auth.User.id");
				if ((isset($this->request->data['UserProject']['id']) && empty($this->request->data['UserProject']['id'])) && (isset($this->request->data['Project']['id']) && empty($this->request->data['Project']['id']))) {
					$this->request->data['UserProject']['owner_user'] = 1;
				}

				if (isset($this->request->data['Project']['start_date']) && isset($this->request->data['Project']['end_date'])) {
					$this->request->data['Project']['start_date'] = (isset($this->request->data['Project']['start_date']) && !empty($this->request->data['Project']['start_date'])) ? date('Y-m-d h:i:s', strtotime($this->request->data['Project']['start_date'])) : $this->request->data['Project']['start_date'];

					$this->request->data['Project']['end_date'] = (isset($this->request->data['Project']['end_date']) && !empty($this->request->data['Project']['end_date'])) ? date('Y-m-d h:i:s', strtotime($this->request->data['Project']['end_date'])) : $this->request->data['Project']['end_date'];
				} else {
					$this->request->data['Project']['start_date'] = $this->request->data['Project']['end_date'] = null;
				}
				$this->request->data['Project']['task_type'] = 'update';

				$this->request->data['Project']['is_search'] = 1;

				if ((isset($this->request->data['Project']['id']) && !empty($this->request->data['Project']['id']))) {
					if (!isset($this->request->data['Project']['currency_id']) || empty($this->request->data['Project']['currency_id'])) {
						$this->request->data['Project']['currency_id'] = 0;
						$this->request->data['UserProject']['is_currency'] = 0;
					} else {
						$this->request->data['UserProject']['is_currency'] = 1;
					}

					$latestDate = $this->Project->find('first', ['conditions' => ['Project.id' => $project_id]]);

					$startDate = strtotime($latestDate['Project']['start_date']);
					$endDate = strtotime($latestDate['Project']['end_date']);

					$willSend = false;
					if (($startDate < strtotime($this->request->data['Project']['start_date']) || $startDate > strtotime($this->request->data['Project']['start_date'])) && ($endDate < strtotime($this->request->data['Project']['end_date']) || $endDate > strtotime($this->request->data['Project']['end_date']))) {
						$willSend = true;

					} else if ($startDate < strtotime($this->request->data['Project']['start_date']) || $startDate > strtotime($this->request->data['Project']['start_date'])) {
						$willSend = true;

					} else if ($endDate < strtotime($this->request->data['Project']['end_date']) || $endDate > strtotime($this->request->data['Project']['end_date'])) {
						$willSend = true;

					} else {

					}

					$dsfg = $this->Group->ProjectDateValidEnd($project_id);

					if (isset($dsfg) && !empty($dsfg)) {
						if ((isset($this->request->data['Project']['start_date']) && !empty($this->request->data['Project']['start_date'])) && (isset($this->request->data['Project']['end_date']) && !empty($this->request->data['Project']['end_date']))) {

							if (date('Y-m-d', strtotime($this->request->data['Project']['start_date'])) > date('Y-m-d', strtotime($dsfg['start_date']))) {

								$this->Session->setFlash(__('Project start date is after a workspace start date.'), 'error');
								$this->redirect(array('controller' => 'projects', 'action' => 'manage_project', $project_id));
							}

							if (date('Y-m-d', strtotime($this->request->data['Project']['end_date'])) < date('Y-m-d', strtotime($dsfg['end_date']))) {

								$this->Session->setFlash(__('Project End date should not be less than Workspace End date.'), 'error');
								$this->redirect(array('controller' => 'projects', 'action' => 'manage_project', $project_id));

							}
						}
					}

					if ($willSend == true) {
						$this->Common->projectScheduleChangeEmail($project_id);

						/************** socket messages **************/
						if (SOCKET_MESSAGES) {
							$current_user_id = $this->Session->read('Auth.User.id');
							App::import('Controller', 'Risks');
							$Risks = new RisksController;
							$project_all_users = $Risks->get_project_users($project_id, $current_user_id);
							if (isset($project_all_users) && !empty($project_all_users)) {
								if (($key = array_search($current_user_id, $project_all_users)) !== false) {
									unset($project_all_users[$key]);
								}
							}
							$open_users = null;
							if (isset($project_all_users) && !empty($project_all_users)) {
								foreach ($project_all_users as $key1 => $value1) {
									if (web_notify_setting($value1, 'project', 'project_schedule_change')) {
										$open_users[] = $value1;
									}
								}
							}
							$userDetail = get_user_data($current_user_id);
							$content = [
								'notification' => [
									'type' => 'project_schedule_change',
									'created_id' => $current_user_id,
									'project_id' => $project_id,
									'creator_name' => $userDetail['UserDetail']['full_name'],
									'subject' => 'Project schedule change',
									'heading' => 'Schedule: ' . date('d M Y', strtotime($this->request->data['Project']['start_date'])) . ' to ' . date('d M Y', strtotime($this->request->data['Project']['end_date'])),
									'sub_heading' => 'Project: ' . strip_tags(getFieldDetail('Project', $project_id, 'title')),
									'date_time' => timezoneTimeConvertor($this->user_id, date('Y-m-d g:iA'), $format = 'd M Y g:iA') . user_country($this->user_id),
								],
							];
							if (is_array($open_users)) {
								$content['received_users'] = array_values($open_users);
							}

							$request = array(
								'header' => array(
									'Content-Type' => 'application/json',
								),
							);
							$content = json_encode($content);
							$HttpSocket = new HttpSocket([
								'ssl_verify_host' => false,
								'ssl_verify_peer_name' => false,
								'ssl_verify_peer' => false,
							]);
							$results = $HttpSocket->post(CHATURL . '/serveremit', $content, $request);
						}
						/************** socket messages **************/

					}

					$this->request->data['Project']['color_code'] = (isset($this->request->data['Project']['color_code']) && !empty($this->request->data['Project']['color_code'])) ? $this->request->data['Project']['color_code'] : "panel-success";
					if ($this->UserProject->saveAssociated($this->request->data)) {

						// $my_programs = my_programs($this->user_id);
						$my_programs = $this->Program->query("SELECT
						      	prog.id
						  	FROM
						        programs prog
						    WHERE
                            	prog.created_by = $user_id "
		    			);
		    			if(isset($my_programs) && !empty($my_programs)){
							$my_programs = Set::extract($my_programs, '{n}.prog.id');
							// $my_programs = implode(",", $my_programs);
						}
						// pr($my_programs);
						// associate project with programs if selected
						$this->loadModel('ProgramProject');
						if((isset($this->request->data['program_id']) && !empty($this->request->data['program_id'])) && (isset($project_id) && !empty($project_id))) {
							$selected_programs = $this->request->data['program_id'];
							if($this->ProgramProject->deleteAll(['ProgramProject.program_id' => $my_programs, 'ProgramProject.project_id' => $project_id ])){
								foreach ($selected_programs as $key => $pg_id) {
									$pgdata = [];
									$pgdata['ProgramProject']['id'] = null;
									$pgdata['ProgramProject']['program_id'] = $pg_id;
									$pgdata['ProgramProject']['project_id'] = $project_id;

									$this->ProgramProject->save($pgdata);
								}
							}
						}
						else if( isset($project_id) && !empty($project_id) ) {
							$this->ProgramProject->deleteAll(['ProgramProject.program_id' => $my_programs, 'ProgramProject.project_id' => $project_id]);
						}

						unset($_SESSION['data']);

						// mongoDB update/insert
						if ($this->live_setting == true) {


							$this->Projects->manageProjectUpdate($this->request->data['Project']['id'], $this->request->data['Project']['title']);

						}

						$selTypes = custom_type_used($project_id);

						if (isset($this->request->data['ProjectElementType']['id']) && !empty($this->request->data['ProjectElementType']['id']) && !empty($project_id)) {

							$unselectedpet = $this->ProjectElementType->find('list', array('conditions' => array('ProjectElementType.id !=' => $this->request->data['ProjectElementType']['id'], 'ProjectElementType.project_id' => $project_id)));

							foreach ($this->request->data['ProjectElementType']['id'] as $selectedPtype) {
								$this->request->data['ProjectElementType']['id'] = $selectedPtype;
								$this->request->data['ProjectElementType']['type_status'] = 1;
								$this->ProjectElementType->save($this->request->data['ProjectElementType']);
							}

							if (isset($unselectedpet) && !empty($unselectedpet)) {
								//pr($unselectedpet);
								foreach ($unselectedpet as $upetkey => $unselectedPtype) {
									$this->request->data['ProjectElementType']['id'] = $upetkey;
									$this->request->data['ProjectElementType']['type_status'] = 0;
									$this->ProjectElementType->save($this->request->data['ProjectElementType']);
								}
							}

							if (isset($selTypes) && !empty($selTypes)) {
								$this->ProjectElementType->updateAll(array('ProjectElementType.type_status' => 1), array('ProjectElementType.id' => $selTypes));
							}

						} else if (isset($selTypes) && !empty($selTypes)) {

							$this->ProjectElementType->updateAll(array('ProjectElementType.type_status' => 0), array('ProjectElementType.project_id' => $project_id));
							$this->ProjectElementType->updateAll(array('ProjectElementType.type_status' => 1), array('ProjectElementType.id' => $selTypes));

						}

						if (!isset($project_id) && empty($project_id)) {
							$project_id = $this->Project->getLastInsertId();
							$this->Common->update_project_activity($project_id, true);
						} else {
							$this->Common->update_project_activity($project_id);
						}


						if ((isset($this->request->data['Project']['rag_status']) && $this->request->data['Project']['rag_status'] == 3)) {

							// if row for this project id is exists... then update the row, otherwise insert new row
							if ($this->ProjectRag->hasAny(['ProjectRag.project_id' => $this->request->data['Project']['id']])) {
								$ragResult = $this->ProjectRag->find('first', [
									'conditions' => [
										'ProjectRag.project_id' => $this->request->data['Project']['id'],
									],
								]
								);
								if (!isset($this->request->data['ProjectRag']['red_value'])) {
									$this->request->data['ProjectRag']['red_value'] = ' ';
								}
								if (!isset($this->request->data['ProjectRag']['amber_value'])) {
									$this->request->data['ProjectRag']['amber_value'] = ' ';
								}
								if (isset($ragResult['ProjectRag']['id'])) {
									$ragData['ProjectRag'] = [
										'id' => $ragResult['ProjectRag']['id'],
										'project_id' => $project_id,
										'user_id' => $this->user_id,
										'amber_value' => $this->request->data['ProjectRag']['amber_value'],
										'red_value' => $this->request->data['ProjectRag']['red_value'],
									];
									$this->ProjectRag->save($ragData);
								} else {
									$ragData['ProjectRag'] = [
										'project_id' => $project_id,
										'user_id' => $this->user_id,
										'amber_value' => $ambervalue,
										'red_value' => $redvalue,
									];
									$this->ProjectRag->save($ragData);
								}

								$ragStatus_old = '';
								$ragStatus_new = 'bg-green';
								$project_name = (isset($this->request->data['Project']['title']) && !empty($this->request->data['Project']['title'])) ? $this->request->data['Project']['title'] : '';
								if (isset($this->request->data['email_rag']) && !empty($this->request->data['email_rag'])) {
									$ragStatus_old = $this->request->data['email_rag'];
								}

								if (isset($this->request->data['rag_status']) && $this->request->data['rag_status'] == 1) {
									$ragStatus_new = 'bg-amber';
								} else if (isset($this->request->data['rag_status']) && $this->request->data['rag_status'] == 2) {
									$ragStatus_new = 'bg-red';
								} else {
									$ragStatus_new = 'bg-green';
								}

								$view = new View();
								$commonHelper = $view->loadHelper('Common');
								$ragCLS = $commonHelper->getRAG($project_id);

							} else {

								$redvalue = (isset($this->request->data['ProjectRag']['red_value']) && !empty($this->request->data['ProjectRag']['red_value'])) ? $this->request->data['ProjectRag']['red_value'] : '';

								$ambervalue = (isset($this->request->data['ProjectRag']['amber_value']) && !empty($this->request->data['ProjectRag']['amber_value'])) ? $this->request->data['ProjectRag']['amber_value'] : '';

								if (!empty($redvalue) || !empty($ambervalue)) {
									$ragData['ProjectRag'] = [
										'project_id' => $project_id,
										'user_id' => $this->user_id,
										'amber_value' => $ambervalue,
										'red_value' => $redvalue,
									];
									$this->ProjectRag->save($ragData);

								}

							}

							$ragStatus_old = '';
							$ragStatus_new = 'bg-green';
							$project_name = (isset($this->request->data['Project']['title']) && !empty($this->request->data['Project']['title'])) ? $this->request->data['Project']['title'] : '';
							if (isset($this->request->data['email_rag']) && !empty($this->request->data['email_rag'])) {
								$ragStatus_old = $this->request->data['email_rag'];
							}

							if (isset($this->request->data['rag_status']) && $this->request->data['rag_status'] == 1) {
								$ragStatus_new = 'bg-amber';
							} else if (isset($this->request->data['rag_status']) && $this->request->data['rag_status'] == 2) {
								$ragStatus_new = 'bg-red';
							} else {
								$ragStatus_new = 'bg-green';
							}

							$view = new View();
							$commonHelper = $view->loadHelper('Common');
							$ragCLS = $commonHelper->getRAG($project_id);

							if (isset($this->request->data['Project']['rag_status']) && $this->request->data['Project']['rag_status'] != $this->request->data['email_rag_type'] && ($this->request->data['Project']['rag_status'] == 1 || $this->request->data['Project']['rag_status'] == 2)) {

								$ragStatustype = 'Manual Based';
								$this->sendRagStatusUpdateEmail($ragStatus_new, $project_name, $project_id, $ragStatustype);
								$this->project_rag_notification($project_id, $ragStatustype);

							} else if (($this->request->data['Project']['rag_status'] == 3) && $this->request->data['email_rag'] != $ragCLS['rag_color']) {

								if ($this->ProjectRag->hasAny(['ProjectRag.project_id' => $this->request->data['Project']['id']])) {

									$ragResult = $this->ProjectRag->find('first', [
										'conditions' => [
											'ProjectRag.project_id' => $this->request->data['Project']['id'],
										],
									]
									);

									$ragStatustype = (($ragResult['ProjectRag']['amber_value'] < 1) && ($ragResult['ProjectRag']['red_value'] < 1)) ? 'Manual Based' : 'Rule Based';

									//$ragStatustype = 'Rule Based';

								} else {
									$ragStatustype = 'Manual Based';
								}
								$this->sendRagStatusUpdateEmail($ragStatus_new, $project_name, $project_id, $ragStatustype);
								$this->project_rag_notification($project_id, $ragStatustype);

							}

							// ===========================================================================================

						} // if any entry is exists for the project and rag status value is other than "green". Then delete that row.
						else if ($this->ProjectRag->hasAny(['ProjectRag.project_id' => $this->request->data['Project']['id']])) {

							$ragResult = $this->ProjectRag->find('first', [
								'conditions' => [
									'ProjectRag.project_id' => $this->request->data['Project']['id'],
								],
							]
							);

							$this->ProjectRag->delete(['ProjectRag.id' => $ragResult['ProjectRag']['id']]);

							// ====================== RAG status mail goes from this side ====================

							//pr($this->request->data);die;
							$ragStatus_old = '';
							$ragStatus_new = 'bg-green';
							$project_name = (isset($this->request->data['Project']['title']) && !empty($this->request->data['Project']['title'])) ? $this->request->data['Project']['title'] : '';
							if (isset($this->request->data['email_rag']) && !empty($this->request->data['email_rag'])) {
								$ragStatus_old = $this->request->data['email_rag'];
							}

							if (isset($this->request->data['Project']['rag_status']) && $this->request->data['Project']['rag_status'] == 1) {
								$ragStatus_new = 'bg-amber';
							} else if (isset($this->request->data['Project']['rag_status']) && $this->request->data['Project']['rag_status'] == 2) {
								$ragStatus_new = 'bg-red';
							} else {
								$ragStatus_new = 'bg-green';
							}

							if (isset($this->request->data['Project']['rag_status']) && $this->request->data['Project']['rag_status'] != $this->request->data['email_rag_type'] && ($this->request->data['Project']['rag_status'] == 1 || $this->request->data['Project']['rag_status'] == 2)) {

								$ragStatustype = 'Manual Based';
								$this->sendRagStatusUpdateEmail($ragStatus_new, $project_name, $project_id, $ragStatustype);

								$this->project_rag_notification($project_id, $ragStatustype);
							}
						} else {

							$ragStatus_old = '';
							$ragStatus_new = 'bg-green';
							$project_name = (isset($this->request->data['Project']['title']) && !empty($this->request->data['Project']['title'])) ? $this->request->data['Project']['title'] : '';
							if (isset($this->request->data['email_rag']) && !empty($this->request->data['email_rag'])) {
								$ragStatus_old = $this->request->data['email_rag'];
							}

							if (isset($this->request->data['rag_status']) && $this->request->data['rag_status'] == 1) {
								$ragStatus_new = 'bg-amber';
							} else if (isset($this->request->data['rag_status']) && $this->request->data['rag_status'] == 2) {
								$ragStatus_new = 'bg-red';
							} else {
								$ragStatus_new = 'bg-green';
							}

							if (isset($this->request->data['Project']['rag_status']) && $this->request->data['Project']['rag_status'] != $this->request->data['email_rag_type'] && ($this->request->data['Project']['rag_status'] == 1 || $this->request->data['Project']['rag_status'] == 2)) {

								$ragStatustype = 'Manual Based';
								$this->sendRagStatusUpdateEmail($ragStatus_new, $project_name, $project_id, $ragStatustype);
								$this->project_rag_notification($project_id, $ragStatustype);

							}

						}
						if (isset($project_id) && !empty($project_id)) {
							$view = new View();
							$commonHelper = $view->loadHelper('Common');
							$ragCLS = $commonHelper->getRAG($project_id, true);

							$rag_current_status = $ragCLS['rag_color'];

							$this->Project->updateAll(array('Project.rag_current_status' => $rag_current_status), array('Project.id' => $project_id));
						}

						$this->Common->projectModified($this->request->data['Project']['id'], $this->user_id);

						$this->redirect(array('controller' => 'projects', 'action' => 'index', $project_id));
					}

				} else {

					$this->request->data['Project']['color_code'] = (isset($this->request->data['Project']['color_code']) && !empty($this->request->data['Project']['color_code'])) ? $this->request->data['Project']['color_code'] : "panel-success";

					$this->request->data['Project']['create_activity'] = 1;

					if (isset($this->request->data['ProjectElementType']['id']) && !empty($this->request->data['ProjectElementType']['id'])) {
						$newIDs = $this->request->data['ProjectElementType']['id'];
					} else {
						if (isset($this->request->data['ProjectElementType']) && !empty($this->request->data['ProjectElementType'])) {
							$newIDs = $this->request->data['ProjectElementType'];
						}
					}

					if ($this->UserProject->saveAssociated($this->request->data)) {

						unset($_SESSION['data']);

						$project_temp_id = 'ideascast_' . $this->Auth->user('id');

						if (!isset($project_id) && empty($project_id)) {
							$project_id = $this->Project->getLastInsertId();
						}
						// associate project with programs if selected
						if((isset($this->request->data['program_id']) && !empty($this->request->data['program_id'])) && (isset($project_id) && !empty($project_id))) {
							$selected_programs = $this->request->data['program_id'];
							foreach ($selected_programs as $key => $pg_id) {
								$pgdata = [];
								$pgdata['ProgramProject']['id'] = null;
								$pgdata['ProgramProject']['program_id'] = $pg_id;
								$pgdata['ProgramProject']['project_id'] = $project_id;
								$this->loadModel('ProgramProject');
								$this->ProgramProject->save($pgdata);
							}
						}

						$projectTypelist = $this->ProjectElementTypeTemp->find('all', array(
							'conditions' => array('ProjectElementTypeTemp.user_id' => $this->user_id, 'ProjectElementTypeTemp.project_id' => $project_temp_id),
						)
						);

						if (isset($projectTypelist) && !empty($projectTypelist)) {
							foreach ($projectTypelist as $key => $prtlists) {
								$this->request->data['ProjectElementType']['type_status'] = 0;
								if (!empty($newIDs)) {
									if (in_array($prtlists['ProjectElementTypeTemp']['id'], $newIDs)) {
										$this->request->data['ProjectElementType']['type_status'] = 1;
									}
								} /*else {
										$this->request->data['ProjectElementType']['type_status'] = 0;
									}*/

								$this->request->data['ProjectElementType']['id'] = '';
								$this->request->data['ProjectElementType']['title'] = $prtlists['ProjectElementTypeTemp']['title'];
								$this->request->data['ProjectElementType']['project_id'] = $project_id;

								$this->request->data['ProjectElementType']['user_id'] = $this->user_id;

								$this->request->data['ProjectElementType']['created'] = date('Y-m-d h:i:s');
								$this->request->data['ProjectElementType']['modified'] = date('Y-m-d h:i:s');

								$this->ProjectElementType->save($this->request->data['ProjectElementType']);

							}

							//=============================================

							$userTempId = "ideascast_" . $this->Auth->user('id');
							/*========== Delete temp data from Project Element table */
							$project_temp_id = 'ideascast_' . $this->Auth->user('id');
							$delConditions = array('ProjectElementTypeTemp.project_id' => $project_temp_id, 'ProjectElementTypeTemp.user_id' => $this->Auth->user('id'));
							$this->ProjectElementTypeTemp->deleteAll($delConditions);
							/*========== ============================================*/

						}


						$user_project_id = $this->UserProject->getLastInsertId();
						$this->Project->updateAll(
							array("Project.create_activity" => 0),
							array("Project.id" => $project_id)
						);
						$this->Common->update_project_activity($project_id, true);
						if ((isset($this->request->data['Project']['rag_status']) && $this->request->data['Project']['rag_status'] == 3)) {

							// if row for this project id is exists... then update the row, otherwise insert new row
							if ($this->ProjectRag->hasAny(['ProjectRag.project_id' => $project_id])) {
								$ragResult = $this->ProjectRag->find('first', [
									'conditions' => [
										'ProjectRag.project_id' => $project_id,
									],
								]
								);

								$redvalue = (isset($this->request->data['ProjectRag']['red_value']) && !empty($this->request->data['ProjectRag']['red_value'])) ? $this->request->data['ProjectRag']['red_value'] : '';

								$ambervalue = (isset($this->request->data['ProjectRag']['amber_value']) && !empty($this->request->data['ProjectRag']['amber_value'])) ? $this->request->data['ProjectRag']['amber_value'] : '';

								if (!empty($redvalue) || !empty($ambervalue)) {
									$ragData['ProjectRag'] = [
										'project_id' => $project_id,
										'user_id' => $this->user_id,
										'amber_value' => $ambervalue,
										'red_value' => $redvalue,
									];
									$this->ProjectRag->save($ragData);

								}

							} else {
								$redvalue = (isset($this->request->data['ProjectRag']['red_value']) && !empty($this->request->data['ProjectRag']['red_value'])) ? $this->request->data['ProjectRag']['red_value'] : '';

								$ambervalue = (isset($this->request->data['ProjectRag']['amber_value']) && !empty($this->request->data['ProjectRag']['amber_value'])) ? $this->request->data['ProjectRag']['amber_value'] : '';

								if (!empty($redvalue) || !empty($ambervalue)) {
									$ragData['ProjectRag'] = [
										'project_id' => $project_id,
										'user_id' => $this->user_id,
										'amber_value' => $ambervalue,
										'red_value' => $redvalue,
									];
									$this->ProjectRag->save($ragData);

								}
							}
						}

						// INSERT IN MONGO
						if ($this->live_setting == true) {
							/*pr($this->request->data, 1);
							if(isset($project_id) && !empty($project_id)){
								$this->Projects->manageProjectUpdate($project_id, $this->request->data['Project']['title']);
							}
							else{
							}*/
								$this->Projects->manageProjectInsert($project_id, $this->request->data['Project']['title']);

						}

						if (isset($project_id) && !empty($project_id)) {
							$view = new View();
							$commonHelper = $view->loadHelper('Common');
							$ragCLS = $commonHelper->getRAG($project_id, true);

							$rag_current_status = $ragCLS['rag_color'];

							$this->Project->updateAll(array('Project.rag_current_status' => $rag_current_status), array('Project.id' => $project_id));
						}

						$this->Common->projectModified($this->request->data['Project']['id'], $this->user_id);

						$this->redirect(array('controller' => 'projects', 'action' => 'index', $project_id));
					}
				}
			} else {
				//yanha se kuch nahi ho raha hai
				unset($_SESSION['data']);
			}

		} else {

			$rag_rules = 1;

			if (isset($project_id) && !empty($project_id)) {
				$specificallyThisOne = $this->UserProject->find('first', array(
					'conditions' => array('UserProject.project_id' => $project_id),
				));
			} else {
				$specificallyThisOne = null;
			}

			if ($this->ProjectRag->hasAny(['ProjectRag.project_id' => $project_id])) {

				$ragResult = $this->ProjectRag->find('first', [
					'conditions' => [
						'ProjectRag.project_id' => $project_id,
					],
				]
				);
				$specificallyThisOne['ProjectRag'] = $ragResult['ProjectRag'];
				$rag_rules = (($ragResult['ProjectRag']['amber_value'] < 1) && ($ragResult['ProjectRag']['red_value'] < 1)) ? 1 : 2;

				//$rag_rules = 2;
			}

			$this->setJsVar('rag_rules', $rag_rules);

			$projectStartDate = getFieldDetail('Project',$project_id,'start_date');

			$this->request->data = $specificallyThisOne;
			if (isset($this->request->data['Project']['start_date']) && !empty($this->request->data['Project']['start_date']) && isset($this->request->data['Project']['end_date']) && !empty($this->request->data['Project']['end_date']) ) {
				$this->request->data['Project']['start_date'] = date("d-m-Y", strtotime($this->request->data['Project']['start_date']));
				$this->request->data['Project']['end_date'] = date("d-m-Y", strtotime($this->request->data['Project']['end_date']));

			}
		}


		$dateprec = STATUS_NOT_SPACIFIED;


		/* -----------Group code----------- */
		if (isset($project_id)) {
			$projectsg = $this->UserProject->find('first', ['recursive' => -1, 'conditions' => ['UserProject.project_id' => $project_id]]);


			$pgupid = $projectsg['UserProject']['id'];
			$conditionsG = null;
			$conditionsG['ProjectGroupUser.user_id'] = $this->user_id;
			$conditionsG['ProjectGroupUser.user_project_id'] = $pgupid;
			$conditionsG['ProjectGroupUser.approved'] = 1;
			$projects_group_shared_user = $this->ProjectGroupUser->find('first', array(
				'conditions' => $conditionsG,
				'fields' => array('ProjectGroupUser.project_group_id'),
				'recursive' => -1,
			));

			if (isset($projects_group_shared_user) && !empty($projects_group_shared_user)) {
				//echo $project_id." sdsd ".$projects_group_shared_user['ProjectGroupUser']['project_group_id']; die;
				$group_permission = $this->Group->group_permission_details($project_id, $projects_group_shared_user['ProjectGroupUser']['project_group_id']);

				$pll_level = $group_permission['ProjectPermission']['project_level'];

				$this->set('gpid', $projects_group_shared_user['ProjectGroupUser']['project_group_id']);

				if (isset($pll_level) && $pll_level == 1) {
					$this->set('project_level', 1);
				}
			}
		}

			if (isset($project_id) && !empty($project_id)) {
				$specificallyThisOne = $this->UserProject->find('first', array(
					'conditions' => array('UserProject.project_id' => $project_id),
				));
			} else {
				$specificallyThisOne = null;
			}


		$aligneds = $this->Aligned->find("list", ['order' => ['Aligned.id ASC']]);
		$this->set(compact('aligneds'));

		$projNames = 'Project';
		$projectData = $this->Project->find("first", ['conditions' => ['Project.id' => $project_id],'recursive'=>-1]);
		if (isset($projectData['Project'])) {
			$projNames = $projectData['Project']['title'];
		}

		$my_programs = [];
		$programs = $this->Program->query("SELECT
						      	prog.id, prog.name
						  	FROM
						        programs prog
						    WHERE
                            	prog.created_by = $user_id "
		    			);
		if(isset($programs) && !empty($programs)){
			$my_programs = Set::combine($programs, '{n}.prog.id', '{n}.prog.name');
			$my_programs = array_map(function ($v) {
				return htmlentities($v, ENT_QUOTES, "UTF-8");
			}, $my_programs);
		}

		$this->set('my_programs', $my_programs);
		$my_program_id = [];
		if(isset($my_programs) && !empty($my_programs)) {
			$my_program_id = array_keys($my_programs);
		}

		$project_programs = [];
		if((isset($project_id) && !empty($project_id)) && (isset($my_program_id) && !empty($my_program_id))) {
			$project_programs = project_programs($project_id, $my_program_id);
		}

		$this->set('project_programs', $project_programs);


		$crumb_title = (isset($project_id) && !empty($project_id)) ? 'Project Details' : 'New Project';

		/*$crumb = [
			'last' => [
				'data' => [
					'class' => 'tipText',
					'title' => $crumb_title,
					'data-original-title' => $crumb_title,
				],
			],
		];*/

		$this->set('crumb', []);
	}

	// WORKSPACE TEAM
	public function wsp_team_count() {
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';

			$data = [];
			$count = 0;
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				// pr($post, 1);

				$order = (isset($post['order']) && !empty($post['order'])) ? $post['order'] : 'ASC';
				$coloumn = (isset($post['coloumn']) && !empty($post['coloumn'])) ? $post['coloumn'] : 'first_name';
				$project_id = (isset($post['project_id']) && !empty($post['project_id'])) ? $post['project_id'] : null;
				$workspace_id = (isset($post['workspace_id']) && !empty($post['workspace_id'])) ? $post['workspace_id'] : null;

				$filters = ['order' => $order, 'coloumn' => $coloumn, 'project_id' => $project_id, 'workspace_id' => $workspace_id];

				$data = $this->objView->loadHelper('Scratch')->wsp_teams($filters);
				$count = count($data);

			}
			echo json_encode($count);
			exit;
		}
	}

	public function filter_wsp_team(){

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				// pr($post);
				$limit = $this->wsp_team_offset;
				$page = (isset($post['page']) && !empty($post['page'])) ? $post['page'] : 0;
				$order = (isset($post['order']) && !empty($post['order'])) ? $post['order'] : 'ASC';
				$coloumn = (isset($post['coloumn']) && !empty($post['coloumn'])) ? $post['coloumn'] : 'first_name';
				$project_id = (isset($post['project_id']) && !empty($post['project_id'])) ? $post['project_id'] : null;
				$workspace_id = (isset($post['workspace_id']) && !empty($post['workspace_id'])) ? $post['workspace_id'] : null;

				$filters = ['limit' => $limit, 'page' => $page, 'order' => $order, 'coloumn' => $coloumn, 'project_id' => $project_id, 'workspace_id' => $workspace_id];

				$wsp_teams = $this->objView->loadHelper('Scratch')->wsp_teams($filters);
				$this->set('wsp_teams', $wsp_teams);
				$this->set('project_id', $post['project_id']);
				$this->set('workspace_id', $post['workspace_id']);

			}
			$this->render('/Projects/sections/wsp_team');
		}
	}

	// WORKSPACE TEAM
	public function project_team_count() {
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';

			$data = [];
			$count = 0;
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				// pr($post, 1);

				$order = (isset($post['order']) && !empty($post['order'])) ? $post['order'] : 'ASC';
				$coloumn = (isset($post['coloumn']) && !empty($post['coloumn'])) ? $post['coloumn'] : 'first_name';
				$project_id = (isset($post['project_id']) && !empty($post['project_id'])) ? $post['project_id'] : null;

				$filters = ['order' => $order, 'coloumn' => $coloumn, 'project_id' => $project_id];

				$data = $this->objView->loadHelper('Scratch')->project_teams($filters);
				$count = count($data);

			}
			echo json_encode($count);
			exit;
		}
	}

	public function get_project_summary(){

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				// pr($post);

				$this->set('project_id', $post['project_id']);
				$this->set('current_page', 0);
				$this->set('limit', 50);

			}
			$this->render('/Projects/sections/summary_data');
		}
	}

	public function filter_project_team(){

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				// pr($post);
				$limit = $this->project_team_offset;
				$page = (isset($post['page']) && !empty($post['page'])) ? $post['page'] : 0;
				$order = (isset($post['order']) && !empty($post['order'])) ? $post['order'] : 'ASC';
				$coloumn = (isset($post['coloumn']) && !empty($post['coloumn'])) ? $post['coloumn'] : 'first_name';
				$project_id = (isset($post['project_id']) && !empty($post['project_id'])) ? $post['project_id'] : null;

				$filters = ['limit' => $limit, 'page' => $page, 'order' => $order, 'coloumn' => $coloumn, 'project_id' => $project_id];

				$project_teams = $this->objView->loadHelper('Scratch')->project_teams($filters);
				$this->set('project_teams', $project_teams);
				$this->set('project_id', $post['project_id']);

			}
			$this->render('/Projects/sections/project_teams');
		}
	}

	public function project_risks(){

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				// pr($post);
				$query_params = [];
				$query_params['limit'] = $this->project_risk_offset;
				$query_params['page'] = (isset($post['page']) && !empty($post['page'])) ? $post['page'] : 0;
				$query_params['order'] = (isset($post['order']) && !empty($post['order'])) ? $post['order'] : 'ASC';
				$query_params['coloumn'] = (isset($post['coloumn']) && !empty($post['coloumn'])) ? $post['coloumn'] : 'rdate';
				$query_params['project_id'] = (isset($post['project_id']) && !empty($post['project_id'])) ? $post['project_id'] : null;

				if (isset($post['param_exposure']) && !empty($post['param_exposure'])) {
					$query_params['exposure'] = $post['param_exposure'];
				}
				if (isset($post['type_id']) && !empty($post['type_id'])) {
					$query_params['type_id'] = $post['type_id'];
				}
				if (isset($post['status']) && !empty($post['status'])) {
					$query_params['status'] = $post['status'];
				}
				if (isset($post['impact']) && !empty($post['impact'])) {
					$query_params['impact'] = $post['impact'];
				}
				if (isset($post['probability']) && !empty($post['probability'])) {
					$query_params['probability'] = $post['probability'];
				}
				if (isset($post['exposure']) && !empty($post['exposure'])) {
					$query_params['exposure'] = $post['exposure'];
				}

				$project_risks = $this->objView->loadHelper('Scratch')->project_risks($query_params);
				$this->set('project_risks', $project_risks);
				$this->set('project_id', $post['project_id']);

			}
			$this->render('/Projects/sections/project_risks');
		}
	}

	public function risk_row() {

		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				if (isset($post['risk_id']) && !empty($post['risk_id'])) {
					$query_params['risk_id'] = $post['risk_id'];
				}
				$viewData['project_id'] = $query_params['project_id'] = $post['project_id'];
				// pr($query_params, 1);
				$viewData['project_risks'] = $this->objView->loadHelper('Scratch')->project_risks($query_params);
				$this->set($viewData);

			}
			$this->render('/Projects/sections/risk_row');

		}
	}


	public function project_risk_count() {
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';

			$data = [];
			$count = 0;
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;

				$query_params = [];
				$query_params['order'] = (isset($post['order']) && !empty($post['order'])) ? $post['order'] : 'ASC';
				$query_params['coloumn'] = (isset($post['coloumn']) && !empty($post['coloumn'])) ? $post['coloumn'] : 'rdate';
				$query_params['project_id'] = (isset($post['project_id']) && !empty($post['project_id'])) ? $post['project_id'] : null;

				if (isset($post['param_exposure']) && !empty($post['param_exposure'])) {
					$query_params['exposure'] = $post['param_exposure'];
				}
				if (isset($post['type_id']) && !empty($post['type_id'])) {
					$query_params['type_id'] = $post['type_id'];
				}
				if (isset($post['status']) && !empty($post['status'])) {
					$query_params['status'] = $post['status'];
				}
				if (isset($post['impact']) && !empty($post['impact'])) {
					$query_params['impact'] = $post['impact'];
				}
				if (isset($post['probability']) && !empty($post['probability'])) {
					$query_params['probability'] = $post['probability'];
				}
				if (isset($post['exposure']) && !empty($post['exposure'])) {
					$query_params['exposure'] = $post['exposure'];
				}

				$project_risks = $this->objView->loadHelper('Scratch')->project_risks($query_params);
				$count = count($project_risks);

			}
			echo json_encode($count);
			exit;
		}
	}

	public function update_risk_map(){

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				// pr($post);
				$this->set('project_id', $post['project_id']);

			}
			$this->render('/Projects/sections/risks_map');
		}
	}


	/* PROGRAM */
	public function programs_list(){

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				// pr($post);
				$query_params = [];
				$query_params['limit'] = $this->program_offset;
				$query_params['page'] = (isset($post['page']) && !empty($post['page'])) ? $post['page'] : 0;
				$query_params['order'] = (isset($post['order']) && !empty($post['order'])) ? $post['order'] : 'ASC';
				$query_params['coloumn'] = (isset($post['coloumn']) && !empty($post['coloumn'])) ? $post['coloumn'] : 'status';

				if (isset($post['roles']) && !empty($post['roles'])) {
					$query_params['roles'] = $post['roles'];
				}
				if (isset($post['status']) && !empty($post['status'])) {
					$query_params['status'] = $post['status'];
				}
				if (isset($post['types']) && !empty($post['types'])) {
					$query_params['types'] = $post['types'];
				}
				if (isset($post['search']) && !empty($post['search'])) {
					$seperator = '^';
					$search_str = Sanitize::escape(like($post['search'], $seperator ));
					$query_params['search'] = " (name LIKE '%$search_str%' ESCAPE '$seperator') ";
				}
				// pr($query_params, 1);

				$programs_list = $this->objView->loadHelper('Scratch')->programs_list($query_params);

				$this->set('programs_list', $programs_list);

			}
			$this->render('/Projects/programs/programs_rows');
		}
	}

	public function add_program() {
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			$user_id = $this->user_id;
			$response = ['success' => false, 'content' => []];
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				$this->loadModel('Program');
				$this->loadModel('ProgramProjects');
				$this->loadModel('ProgramUser');
				// pr($post, 1);
				if((isset($post['name']) && !empty($post['name'])) && (isset($post['type_id']) && !empty($post['type_id']))){
					$pdata = [
						'name' => $post['name'],
						'type_id' => $post['type_id'],
						'description' => $post['description'],
						'outcome' => $post['outcome'],
						'color_code' => 'darkgray',
						'created_by' => $this->user_id,
						'created_on' => date('Y-m-d'),
					];
					if($this->Program->save($pdata)){
						$response['success'] = true;
						$program_id = $this->Program->getLastInsertId();
						if(isset($post['projects']) && !empty($post['projects'])){
							$projects = $post['projects'];
        					$qry = "INSERT INTO `program_projects` (`program_id`, `project_id`) VALUES ";
        					$qry_arr = [];
            				foreach ($projects as $key => $value) {
            					$qry_arr[] = "('$program_id', '$value')";
            				}
            				$qry .= implode(' ,', $qry_arr);
            				$this->ProgramProjects->query($qry);
						}
						$all_stakeholders = [];
						if(isset($post['stakeholders']) && !empty($post['stakeholders'])){
							$stakeholders = $post['stakeholders'];
        					$qry = "INSERT INTO `program_users` (`program_id`, `user_id`) VALUES ";
        					$qry_arr = [];
            				foreach ($stakeholders as $key => $value) {
            					$qry_arr[] = "('$program_id', '$value')";
            					$all_stakeholders[] = $value;
            				}
            				$qry .= implode(' ,', $qry_arr);
            				$this->ProgramUser->query($qry);
						}

						// socket messages
						if (SOCKET_MESSAGES && !empty($all_stakeholders)) {
							$current_user_id = $this->Auth->user('id');
							$wsp_users = $all_stakeholders;
							$s_open_users = null;
							if (isset($wsp_users) && !empty($wsp_users)) {
								foreach ($wsp_users as $key => $value) {
									if (web_notify_setting($value, 'program', 'email_program_stackholder')) {
										$s_open_users[] = $value;
									}
								}
							}
							$userDetail = get_user_data($current_user_id);
							$content = [
								'notification' => [
									'type' => 'program',
									'created_id' => $current_user_id,
									'creator_name' => $userDetail['UserDetail']['full_name'],
									'subject' => 'Program stakeholder',
									'heading' => 'Program: ' . $post['name'],
									'date_time' => timezoneTimeConvertor($this->user_id, date('Y-m-d g:iA'), $format = 'd M Y g:i A') . user_country($this->user_id),
								],
							];
							if (is_array($s_open_users)) {
								$content['received_users'] = array_values($s_open_users);
							}
							$response['content']['socket'] = $content;
						}

						// SEND EMAIL
						if(isset($all_stakeholders) && !empty($all_stakeholders)){
							foreach ($all_stakeholders as $key => $value) {
								$this->Common->program_stakeholder_added($program_id, $value);
							}
						}
					}
				}
				echo json_encode($response);
				exit;
			}
			$viewData['users'] = $this->UserDetail->query("SELECT
						up.user_id,
						CONCAT_WS(' ',up.first_name , up.last_name) AS username
					FROM users
					INNER JOIN user_details up ON up.user_id = users.id
					WHERE users.status = 1 AND users.is_activated = 1 AND users.is_deleted = 0 AND users.role_id = 2 AND users.id NOT IN($user_id)
					ORDER BY up.first_name ASC, up.last_name ASC");
			if(isset($viewData['users']) && !empty($viewData['users'])){
				$viewData['users'] = Set::combine($viewData['users'], '{n}.up.user_id', '{n}.0.username');
			}

			$viewData['projects'] = $this->UserDetail->query("SELECT
						p.id, p.title
					FROM user_permissions up
					INNER JOIN projects p ON p.id = up.project_id
					WHERE up.user_id = $user_id AND up.role IN('Owner', 'Group Owner', 'Creator') AND up.workspace_id IS NULL
					ORDER BY p.title ASC");
			if(isset($viewData['projects']) && !empty($viewData['projects'])){
				$viewData['projects'] = Set::combine($viewData['projects'], '{n}.p.id', '{n}.p.title');
			}

			$viewData['types'] = $this->UserDetail->query("SELECT
						pt.id, pt.type
					FROM program_types pt
					ORDER BY pt.type ASC");
			// pr($viewData['types'],1);
			if(isset($viewData['types']) && !empty($viewData['types'])){
				$viewData['types'] = Set::combine($viewData['types'], '{n}.pt.id', '{n}.pt.type');
			}
			$this->set($viewData);
			$this->render('/Projects/programs/add_program');
		}
	}

	public function edit_program($program_id = null) {
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			$user_id = $this->user_id;
			$response = ['success' => false];
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				$this->loadModel('Program');
				$this->loadModel('ProgramProjects');
				$this->loadModel('ProgramUser');
				// pr($post, 1);
				if((isset($post['id']) && !empty($post['id'])) && (isset($post['name']) && !empty($post['name'])) && (isset($post['type_id']) && !empty($post['type_id']))){
					$program_id = $post['id'];
					$pdata = [
						'name' => $post['name'],
						'type_id' => $post['type_id'],
						'description' => $post['description'],
						'outcome' => $post['outcome']
					];
					$this->Program->id = $post['id'];

					if($this->Program->save($pdata)){
						$response['success'] = true;

						if(isset($post['projects']) && !empty($post['projects'])){
							$this->Program->query("DELETE FROM program_projects WHERE program_id = $program_id");
							$projects = $post['projects'];
        					$qry = "INSERT INTO `program_projects` (`program_id`, `project_id`) VALUES ";
        					$qry_arr = [];
            				foreach ($projects as $key => $value) {
            					$qry_arr[] = "('$program_id', '$value')";
            				}
            				$qry .= implode(' ,', $qry_arr);
            				$this->ProgramProjects->query($qry);
						}
						else{
							$this->Program->query("DELETE FROM program_projects WHERE program_id = $program_id");
						}
						$added_stakeholders = [];
						if(isset($post['stakeholders']) && !empty($post['stakeholders'])){
							// GET ALL ADDED USERS
							$preUsers = $this->Program->query("SELECT user_id FROM program_users WHERE program_id = $program_id");
							if(isset($preUsers) && !empty($preUsers)){
								$preUsers = Set::extract($preUsers, '{n}.program_users.user_id');
							}
							// DIFFERENCE OF ADDED AND POSTED USERS
							$added_stakeholders = array_diff($post['stakeholders'], $preUsers);

							$this->Program->query("DELETE FROM program_users WHERE program_id = $program_id");
							$stakeholders = $post['stakeholders'];
        					$qry = "INSERT INTO `program_users` (`program_id`, `user_id`) VALUES ";
        					$qry_arr = [];
            				foreach ($stakeholders as $key => $value) {
            					$qry_arr[] = "('$program_id', '$value')";
            				}
            				$qry .= implode(' ,', $qry_arr);
            				$this->ProgramUser->query($qry);
						}
						else{
							$this->Program->query("DELETE FROM program_users WHERE program_id = $program_id");
						}

						// socket messages
						if (SOCKET_MESSAGES && !empty($added_stakeholders)) {
							$current_user_id = $this->Auth->user('id');
							$socket_users = $added_stakeholders;
							$s_open_users = null;
							if (isset($socket_users) && !empty($socket_users)) {
								foreach ($socket_users as $key => $value) {
									if (web_notify_setting($value, 'program', 'email_program_stackholder')) {
										$s_open_users[] = $value;
									}
								}
							}
							$userDetail = get_user_data($current_user_id);
							$content = [
								'notification' => [
									'type' => 'program',
									'created_id' => $current_user_id,
									'creator_name' => $userDetail['UserDetail']['full_name'],
									'subject' => 'Program stakeholder',
									'heading' => 'Program: ' . $post['name'],
									'date_time' => timezoneTimeConvertor($this->user_id, date('Y-m-d g:iA'), $format = 'd M Y g:i A') . user_country($this->user_id),
								],
							];
							if (is_array($s_open_users)) {
								$content['received_users'] = array_values($s_open_users);
							}
							$response['content']['socket'] = $content;
						}

						// SEND EMAIL
						if(isset($added_stakeholders) && !empty($added_stakeholders)){
							foreach ($added_stakeholders as $key => $value) {
								$this->Common->program_stakeholder_added($program_id, $value);
							}
						}
					}
				}
				echo json_encode($response);
				exit;
			}
			$viewData['users'] = $this->UserDetail->query("SELECT
						up.user_id,
						CONCAT_WS(' ',up.first_name , up.last_name) AS username
					FROM users
					INNER JOIN user_details up ON up.user_id = users.id
					WHERE users.status = 1 AND users.is_activated = 1 AND users.is_deleted = 0 AND users.role_id = 2 AND users.id NOT IN($user_id)
					ORDER BY up.first_name ASC, up.last_name ASC");
			if(isset($viewData['users']) && !empty($viewData['users'])){
				$viewData['users'] = Set::combine($viewData['users'], '{n}.up.user_id', '{n}.0.username');
			}

			$viewData['projects'] = $this->UserDetail->query("SELECT
						p.id, p.title
					FROM user_permissions up
					INNER JOIN projects p ON p.id = up.project_id
					WHERE up.user_id = $user_id AND up.role IN('Owner', 'Group Owner', 'Creator') AND up.workspace_id IS NULL
					ORDER BY p.title ASC");
			if(isset($viewData['projects']) && !empty($viewData['projects'])){
				$viewData['projects'] = Set::combine($viewData['projects'], '{n}.p.id', '{n}.p.title');
			}

			$viewData['types'] = $this->UserDetail->query("SELECT
						pt.id, pt.type
					FROM program_types pt
					ORDER BY pt.type ASC");
			// pr($viewData['types'],1);
			if(isset($viewData['types']) && !empty($viewData['types'])){
				$viewData['types'] = Set::combine($viewData['types'], '{n}.pt.id', '{n}.pt.type');
			}
			$viewData['progs'] = $this->UserDetail->query("SELECT
							progs.id, progs.name, progs.description, progs.outcome, progs.type_id,
							prjs.all_projects, prjs.cp, pusr.all_users, pusr.cu
						FROM (
						  	SELECT
						      	prog.id, prog.name, prog.description, prog.outcome, prog.type_id
						  	FROM
						        programs prog
						    LEFT JOIN program_users pu ON
						        pu.program_id = prog.id
						    WHERE
                            	prog.id = $program_id
                            GROUP BY prog.id
						) AS progs
						LEFT JOIN (
						   	SELECT
						   		pp.program_id,
						   		GROUP_CONCAT(pp.project_id) AS all_projects,
                            	COUNT(DISTINCT(pp.project_id)) AS cp
							FROM program_projects pp
							GROUP BY pp.program_id
						)
						AS prjs ON prjs.program_id = progs.id
						LEFT JOIN (
						   	SELECT
						   		pp.program_id,
						   		GROUP_CONCAT(pp.user_id) AS all_users,
                            	COUNT(DISTINCT(pp.user_id)) AS cu
							FROM program_users pp
							GROUP BY pp.program_id
						)
						AS pusr ON pusr.program_id = progs.id
					");
			$this->set($viewData);
			$this->render('/Projects/programs/edit_program');
		}
	}

	public function view_program($program_id = null, $tab = "stakeholder-tab") {
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			$user_id = $this->user_id;

			$viewData['progs'] = $this->UserDetail->query("SELECT
							progs.id, progs.name, progs.description, progs.outcome, progs.type, progs.created_on, progs.creator, progs.organization_id, progs.job_title, progs.profile_pic, progs.created_by,
							pusr.stakeholders,
							pdets.stdate, pdets.endate,
							teams.team
						FROM (
						  	SELECT
						      	prog.id, prog.name, prog.description, prog.outcome, pt.type, prog.created_on,
								CONCAT_WS(' ',ud.first_name , ud.last_name) AS creator, prog.created_by,
								ud.organization_id, ud.job_title, ud.profile_pic
						  	FROM
						        programs prog
						    LEFT JOIN program_users pu ON
						        pu.program_id = prog.id
						    LEFT JOIN user_details ud ON
						        ud.user_id = prog.created_by
						    LEFT JOIN program_types pt ON
						        pt.id = prog.type_id
						    WHERE
                            	prog.id = $program_id
                            GROUP BY prog.id
						) AS progs

						LEFT JOIN (
						   	SELECT
						   		pp.program_id,
						   		JSON_ARRAYAGG(JSON_OBJECT( 'id', ud.user_id, 'title', CONCAT_WS(' ',ud.first_name , ud.last_name), 'org_id', ud.organization_id, 'job_title', ud.job_title, 'profile_pic', ud.profile_pic  )) AS stakeholders
							FROM program_users pp
							LEFT JOIN user_details ud ON ud.user_id = pp.user_id
							GROUP BY pp.program_id
						) AS pusr ON pusr.program_id = progs.id

						LEFT JOIN (
						   	SELECT
						   		pp.program_id AS id,
						   		MIN(DATE(prj.start_date)) AS stdate,
						        MAX(DATE(prj.end_date)) AS endate
							FROM program_projects pp
							LEFT JOIN projects prj ON
								pp.project_id = prj.id
							GROUP BY pp.program_id
						) AS pdets ON progs.id = pdets.id

						LEFT JOIN (
						   	SELECT
						    	pp.program_id AS id,
						    	JSON_ARRAYAGG(JSON_OBJECT( 'id', ud.user_id, 'title', CONCAT_WS(' ',ud.first_name , ud.last_name), 'org_id', ud.organization_id, 'job_title', ud.job_title, 'profile_pic', ud.profile_pic  )) AS team
						    FROM
						    	program_projects pp
						    LEFT JOIN user_permissions up ON pp.project_id = up.project_id AND up.workspace_id IS NULL
						    LEFT JOIN user_details ud ON ud.user_id = up.user_id
						    GROUP BY pp.program_id
						) AS teams ON progs.id = teams.id
					");
			$viewData['tab'] = $tab;
			$this->set($viewData);
			$this->render('/Projects/programs/view_program');
		}
	}

	public function delete_programs($program_id = null) {
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';

			$response = ['success' => false];
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				// pr($post, 1);
				$data['id'] = $program_id = $post['program_id'];
				if($this->Program->delete($data, false)){
					$response['success'] = true;
					$this->Program->query("DELETE FROM program_projects WHERE program_id = $program_id");
					$this->Program->query("DELETE FROM program_users WHERE program_id = $program_id");
				}

				echo json_encode($response);
				exit;
			}
		}
		$this->set('program_id', $program_id);
		$this->render('/Projects/programs/delete_program');
	}

	public function remove_stakeholder($program_id = null) {
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			$user_id = $this->user_id;

			$response = ['success' => false];
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				// pr($post, 1);
				$program_id = $post['program_id'];
				$this->Program->query("DELETE FROM program_users WHERE program_id = $program_id AND user_id = $user_id");
				$response['success'] = true;

				// socket messages
				$programDetails = $this->Program->query("
                                SELECT CONCAT_WS(' ',ud.first_name , ud.last_name) AS creator, ud.user_id, p.name
                                FROM programs p
                                LEFT JOIN user_details ud ON ud.user_id = p.created_by
                                WHERE p.id = $program_id
		                    ");
				$creator_id = $programDetails[0]['ud']['user_id'];
				$program_name = $programDetails[0]['p']['name'];

				if (SOCKET_MESSAGES) {
					$current_user_id = $this->Auth->user('id');
					$wsp_users = [$creator_id];
					$s_open_users = null;
					if (isset($wsp_users) && !empty($wsp_users)) {
						foreach ($wsp_users as $key => $value) {
							if (web_notify_setting($value, 'program', 'stackholder_removal')) {
								$s_open_users[] = $value;
							}
						}
					}
					$userDetail = get_user_data($current_user_id);
					$content = [
						'notification' => [
							'type' => 'program',
							'created_id' => $current_user_id,
							'creator_name' => $userDetail['UserDetail']['full_name'],
							'subject' => 'Stakeholder removal',
							'heading' => 'Program: ' . $program_name,
							'date_time' => timezoneTimeConvertor($this->user_id, date('Y-m-d g:iA'), $format = 'd M Y g:i A') . user_country($this->user_id),
						],
					];
					if (is_array($s_open_users)) {
						$content['received_users'] = array_values($s_open_users);
					}
					$response['content']['socket'] = $content;
				}
				// socket messages

				// SEND EMAIL
				if(isset($current_user_id) && !empty($current_user_id)){
					$this->Common->program_stakeholder_removal($program_id, $current_user_id);
				}
				echo json_encode($response);
				exit;
			}
		}
		$this->set('program_id', $program_id);
		$this->render('/Projects/programs/remove_stakeholder');
	}

	public function programs_count() {
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';

			$data = [];
			$count = 0;
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;

				$query_params = [];
				$query_params['order'] = (isset($post['order']) && !empty($post['order'])) ? $post['order'] : 'ASC';
				$query_params['coloumn'] = (isset($post['coloumn']) && !empty($post['coloumn'])) ? $post['coloumn'] : 'status';

				if (isset($post['roles']) && !empty($post['roles'])) {
					$query_params['roles'] = $post['roles'];
				}
				if (isset($post['status']) && !empty($post['status'])) {
					$query_params['status'] = $post['status'];
				}
				if (isset($post['types']) && !empty($post['types'])) {
					$query_params['types'] = $post['types'];
				}
				if (isset($post['search']) && !empty($post['search'])) {
					$seperator = '^';
					$search_str = Sanitize::escape(like($post['search'], $seperator ));
					$query_params['search'] = " (name LIKE '%$search_str%' ESCAPE '$seperator') ";
				}

				$data = $this->objView->loadHelper('Scratch')->programs_list($query_params);
				$count = count($data);

			}
			echo json_encode($count);
			exit;
		}
	}

	public function program_counter() {
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			$total_programs = get_programs_count($this->user_id);
			echo json_encode($total_programs);
			exit;
		}
	}

	public function program_color() {

		if ($this->request->isAjax()) {

			$response = [
				'success' => false
			];

			$this->layout = 'ajax';
			$this->autoRender = false;

			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;

				$this->Program->id = $post['program_id'];
				if ($this->Program->saveField('color_code', $post['color_code'])) {

					$response['success'] = true;
				} else {
					$response['msg'] = "Error!!!";
				}
			}

			echo json_encode($response);
			exit();
		}
	}

	public function filter_programs(){

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$user_id = $this->user_id;

			$this->render('/Projects/programs/filter_programs');
		}
	}
}
