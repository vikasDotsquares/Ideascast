<?php
App::uses('Controller/Component', 'Auth', 'Session', 'RequestHandler');
App::uses('Sanitize', 'Utility');
App::uses('CakeEmail', 'Network/Email');

class RequestsController extends AppController {

	public $name = 'Requests';

	public $uses = [
		'User',
		'UserDetail',
		'Project',
		'Element',
		'UserProject',
		'ProjectWorkspace',
		'Workspace',
		'DoList',
		'DoListUser',
	];

	public $objView = null;
	public $user_id = null;
	public $components = array(
		'Common',
	);
	public $helpers = array(
		'Html',
		'Form',
		'Session',
		'Time',
		'Text',
		'Common',
		'ViewModel',
	);

	public function beforeFilter() {
		parent::beforeFilter();

		$this->user_id = $this->Auth->user('id');

		$view = new View();
		$this->objView = $view;

	}

	public function index() {


	}


	public function group() {

		$this->layout = 'inner';

		$this->set('page_heading', 'Group Requests');

		$this->set('title_for_layout', 'Group Requests');
		// Check this user for previous data exists or not
		$permit_data = null;
		$this->loadModel('ProjectGroupUser');
		if (isset($this->user_id) && !empty($this->user_id)) {

			$pp_data = $this->ProjectGroupUser->find('all', [
				'conditions' => [
					'ProjectGroupUser.user_id' => $this->user_id,
					'ProjectGroupUser.approved' => [0, 1, 2],
				],
				'recursive' => 1,
			]);

			$pp_data_count = ( isset($pp_data) && !empty($pp_data) ) ? count($pp_data) : 0;

		}

		if (isset($project_id) && !empty($project_id)) {
			$this->setJsVar('project_id', $project_id);
		}

		$this->set('permit_data', $pp_data);

		$crumb = [
			'last' => [
				'data' => [
					'title' => 'Group Requests',
					'data-original-title' => 'Group Requests',
				],
			],
		];
		$this->set('crumb', $crumb);

	}

	public function todo($type = 'main') {
		$this->layout = 'inner';
		$user_id = $this->Session->read("Auth.User.id");
		$conditions = $condition_users = array();

		if (isset($type) && $type == 'main') {
			$this->set('title_for_layout', __('To-do Requests', true));
			$this->set('page_heading', __('To-do Requests', true));
			$this->set('page_subheading', __('View To-do Requests', true));
			$conditions['AND'][] = array('DoList.parent_id' => 0);
		} else if (isset($type) && $type == 'sub') {
			$this->set('title_for_layout', __('Sub To-do Requests', true));
			$this->set('page_heading', __('Sub To-do Requests', true));
			$this->set('page_subheading', __('View Sub To-do Requests', true));
			$conditions['AND'][] = array('DoList.parent_id !=' => 0);
		}

		if (isset($this->params->query['status'])) {
			$status = $this->params->query['status'];
			$this->set('status', $status);
			$v = trim($this->params->query['status']);
			if (!empty($v)) {
				if ($status == 'PND') {
					$conditions['AND'][] = array('DoList.start_date > ' => date("Y-m-d"));
				} else if ($status == 'PRG') {
					$conditions['AND'][] = array('DoList.sign_off' => 0);
					$conditions['AND'][] = array('DoListUser.approved' => 1);
					$conditions['AND'][] = array('DoList.start_date <= ' => date("Y-m-d"));
					$conditions['AND'][] = array('DoList.end_date >= ' => date("Y-m-d"));
				} else if ($status == 'OVD') {
					$conditions['AND'][] = array('DoList.end_date < ' => date("Y-m-d"));
					$conditions['AND'][] = array('DoList.sign_off' => 0);
				} else if ($status == 'CMP') {
					$conditions['AND'][] = array('DoList.sign_off' => 1);
				} else if ($status == 'RJCT') {
					$condition_users['AND'][] = array('DoListUser.approved' => 2);
					$conditions['AND'][] = array('DoListUser.approved' => 2);
				} else if ($status == 'OPN') {
					$condition_users['AND'][] = array('DoListUser.approved' => 0);
					$conditions['AND'][] = array('DoListUser.approved' => 0);
					$conditions['AND'][] = array('DoList.sign_off' => 0);
					$conditions['OR'][] = array('DoList.start_date IS NOT NULL', 'DoList.start_date <=' => date("Y-m-d"));
				}
			}
		}
		if (isset($this->params->query['keywords'])) {
			$keywords = $this->params->query['keywords'];
			$this->set('keywords', $keywords);
			$v = trim($this->params->query['keywords']);
			if (!empty($v)) {
				$conditions['OR'][] = array('DoList.title LIKE' => '%' . trim($keywords) . '%');
			}
		}

		$conditions['AND'][] = ['DoListUser.user_id' => $user_id];
		$condition_users['AND'][] = ['DoListUser.user_id' => $user_id];
		$this->DoList->unbindModel(array("hasMany" => array("DoListUser", "DoListUpload", "DoListComment", "Children")));
		$this->DoList->bindModel(array("hasOne" => array("DoListUser" => array(
			'conditions' => $condition_users,
		),
		),
		)
		);
		//pr($conditions);
		//pr($condition_users,1);
		$this->paginate = [
			'recursive' => 2,
			'order' => 'DoListUser.modified DESC',
			'group' => 'DoList.id',
			'conditions' => $conditions,
			'limit' => 1000,
		];
		$pageing = $this->paginate($this->DoList);
		$this->set('data', $pageing);

		if (isset($type) && $type == 'main') {
			$crumb = [
				'Summary' => [
					'data' => [
						'url' => '/todos/index',
						'class' => 'tipText',
						'title' => 'To-do',
						'data-original-title' => 'To-do',
					],
				],
				'last' => [
					'data' => [
						'title' => 'To-do Request lists',
						'data-original-title' => 'To-do Requests',
					],
				],
			];
		} else if (isset($type) && $type == 'sub') {

			$crumb = [
				'Summary' => [
					'data' => [
						'url' => '/todos/index',
						'class' => 'tipText',
						'title' => 'To-do',
						'data-original-title' => 'To-do',
					],
				],
				'last' => [
					'data' => [
						'title' => 'Sub To-do Requests',
						'data-original-title' => 'Sub To-do Requests',
					],
				],
			];
		}

		$this->set(compact("type"));
		$this->set('crumb', $crumb);
	}


	public function wiki() {
		$this->layout = 'inner';
		$user_id = $this->user_id;
		$this->set('title_for_layout', __('Wiki Requests', true));
		$this->set('page_heading', __('Wiki Requests', true));
		$this->set('page_subheading', __('View Wiki Requests', true));
		$this->loadModel('WikiUser');
		$this->WikiUser->bindModel(array(
			'hasOne' => array(
				'Wiki' => array(
					'className' => 'Wiki',
					'foreignKey' => false,
					"conditions" => array("Wiki.id = WikiUser.wiki_id"),
				),
			),
		));
		$conditions = array("WikiUser.owner_id" => $user_id);

		if (isset($this->params->query['status'])) {
			$status = $this->params->query['status'];
			$this->set('status', $status);
			$v = trim($this->params->query['status']);
			if (!empty($v)) {
				if ($status == 'accept') {
					$conditions['AND'][] = array('WikiUser.approved' => 1);
				} else if ($status == 'decline') {
					$conditions['AND'][] = array('WikiUser.approved' => 2);
				} else if ($status == 'pending') {
					$conditions['AND'][] = array('WikiUser.approved' => 0);
				}
			}
		}
		if (isset($this->params->query['keywords'])) {
			$keywords = $this->params->query['keywords'];
			$this->set('keywords', $keywords);
			$v = trim($this->params->query['keywords']);
			if (!empty($v)) {
				$conditions['OR'][] = array('Wiki.title LIKE' => '%' . trim($keywords) . '%');
			}
		}

		$this->paginate = [
			'order' => 'WikiUser.approved DESC',
			'conditions' => $conditions,
			'limit' => 10000,
		];
		$data = $this->paginate($this->WikiUser);

		$this->set("data", $data);
		$crumb = [
			'Summary' => [
				'data' => [
					'url' => '/wikies/index',
					'class' => 'tipText',
					'title' => 'Wiki',
					'data-original-title' => 'Wiki',
				],
			],
			'last' => [
				'data' => [
					'title' => 'Wiki Request lists',
					'data-original-title' => 'Wiki Requests',
				],
			],
		];
		$this->set('crumb', $crumb);
	}

	/****************** XHR **************************/

	public function todo_detail() {

		$this->layout = false;
		$project_name = 'Unspecified';
		$user_id = $this->Auth->user('id');
		$data = $type = null;

		if ($this->request->isAjax()) {
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;

				$this->DoList->id = $todo_id = $post['id'];

				$this->DoList->unbindModel(array("hasMany" => array("DoListUser")));
				$this->DoList->bindModel(array("hasOne" => array("DoListUser" => array("conditions" => array("DoListUser.user_id" => $user_id)))));

				$todouserdata = $this->DoListUser->find("all", array("conditions" => array("DoListUser.do_list_id" => $todo_id)));
				$tododata = $this->DoList->find("first", array("conditions" => array("DoList.id" => $todo_id)));
				if (isset($todo_id) && !empty($todo_id) && $tododata['DoListUser']['id'] != '') {
					if ($tododata['DoList']['project_id'] != '') {
						$this->loadModel("Project");
						$project = $this->Project->findById($tododata['DoList']['project_id']);
						$project_name = strip_tags($project['Project']['title']);
					}
					if (isset($tododata['DoList']['parent_id']) && $tododata['DoList']['parent_id'] != 0) {
						$type = 'Sub';
					}

				}
			}

			$viewData['tododata'] = $tododata;
			$viewData['todouserdata'] = $todouserdata;
			$viewData['project_name'] = $project_name;


			$view = new View($this, false);
			$view->viewPath = 'Requests/partials';
			$view->set($viewData);
			$html = $view->render('todo_detail');

			echo json_encode($html);
			exit();

		}
	}

}
