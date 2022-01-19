<?php
App::uses('Controller/Component', 'Auth', 'Session', 'RequestHandler');
App::uses('Sanitize', 'Utility');
App::uses('CakeEmail', 'Network/Email');

class SocketsController extends AppController {

	public $name = 'Sockets';
	public $uses = ['User', 'UserDetail', 'ProjectPermission', 'UserSetting', 'Category', 'Aligned', 'UserProject', 'Project', 'Workspace', 'Area', 'ProjectWorkspace', 'Element', 'ProjectGroup', 'ProjectGroupUser', 'ProjectBoard', 'EmailNotification'];
	public $user_id = null;
	public $pagination = null;
	public $mongoDB = null;
	public $components = array('Mpdf', 'Common', 'Group', 'Users');
	public $helpers = array('Html', 'Form', 'Session', 'Time', 'Text', 'Common', 'ViewModel', 'Mpdf', 'Group');

	public $objView = null;


	public function beforeFilter() {
		parent::beforeFilter();

		$view = new View();
		$this->objView = $view;
		$this->user_id = $this->Auth->user('id');

		$this->Auth->allow('mongo_user_projects');
	}

	public function index() {

		$this->layout = 'inner';
		$this->set('title_for_layout', __('Socket', true));

		$viewData['page_heading'] = 'Socket';
		$viewData['page_subheading'] = 'Socket';

		$user_id = $this->Auth->user('id');

		$this->set('user_id', $user_id);
		$this->set($viewData);
	}

	public function index2() {

		$this->layout = 'inner';
		$this->set('title_for_layout', __('Socket', true));

		$viewData['page_heading'] = 'Socket';
		$viewData['page_subheading'] = 'Socket';

		$user_id = $this->Auth->user('id');

		$this->set('user_id', $user_id);
		$this->set($viewData);
	}

	public function steps() {
		$response = ['success' => false];
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			$html = '';

			$this->render(DS . 'Sockets' . DS . 'partials' . DS . 'steps');
		}
	}

	// read_notification
	public function read_notification() {
		$response = ['success' => false, 'content' => null];
		if ($this->request->isAjax()) {
			$this->layout = false;

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;

				// call AppController function
				$this->update_notification($post['id']);
				$response['success'] = true;
			}
		}
		echo json_encode($response);
		exit;
	}

	// remove_notification
	public function remove_notification() {
		$response = ['success' => false, 'content' => null];
		if ($this->request->isAjax()) {
			$this->layout = false;

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;

				// call AppController function
				$this->delete_notification($post['id']);
				$response['success'] = true;
			}
		}
		echo json_encode($response);
		exit;
	}

	// remove_all_notifications
	public function remove_all_notifications() {
		$response = ['success' => false, 'content' => null];
		if ($this->request->isAjax()) {
			$this->layout = false;

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;

				// call AppController function
				if (isset($post['type']) && !empty($post['type'])) {
					$this->delete_user_notifications($post['user_id'], $post['type']);
				} else {
					$this->delete_user_notifications($post['user_id']);
				}
				$response['success'] = true;
			}
		}
		echo json_encode($response);
		exit;
	}

	public function show_reward_graphs() {
		if ($this->request->isAjax()) {

			$this->layout = false;
			$viewData = null;
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
			}

			$view = new View($this, false);
			$view->viewPath = 'Elements/front';
			$view->set($viewData);
			$html = $view->render('show_reward_graphs');
			echo json_encode($html);
			exit();
		}
	}

	public function show_bookmarks() {
		if ($this->request->isAjax()) {

			$this->layout = false;
			$viewData = null;
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				// pr($post);
				$viewData['type'] = (isset($post['type']) && !empty($post['type'])) ? $post['type'] : '';
			}

			$view = new View($this, false);
			$view->viewPath = 'Elements/front';
			$view->set($viewData);
			$html = $view->render('show_bookmarks');
			echo json_encode($html);
			exit();
		}
	}


	public function show_browser_menu() {
		if ($this->request->isAjax()) {

			$this->layout = false;
			$viewData = null;
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
			}

			$view = new View($this, false);
			$view->viewPath = 'Elements/front';
			$view->set($viewData);
			$html = $view->render('browse_menus');
			echo json_encode($html);
			exit();
		}
	}

	public function recent_next_menus() {
		$this->layout = 'ajax';

		if ($this->request->isAjax()) {
			$response = ['success' => false, 'content' => []];
			$current_user = $this->Session->read('Auth.User.id');

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				// pr($post, 1);

				if(isset($post['type']) && !empty($post['type'])){
					$type = $post['type'];
					$viewData['type'] = $type;

					if($type == 'recent-project'){
						$query = "SELECT
								    project_id,
								    title
								FROM
									(
									    SELECT
									 		aa.project_id,
									        p.title,
									        aa.updated
									 	FROM
									        (
									        SELECT pa.project_id, pa.updated_user_id, pa.updated
									        FROM
									            project_activities pa
									        WHERE
									            pa.updated_user_id = $current_user #change at runtime
									        UNION ALL
									        SELECT wa.project_id, wa.updated_user_id, wa.updated
									        FROM
									            workspace_activities wa
									        WHERE
									            wa.updated_user_id = $current_user #change at runtime
									        UNION ALL
									        SELECT ta.project_id, ta.updated_user_id, ta.updated
									        FROM
									            activities ta
									        WHERE
									            ta.updated_user_id = $current_user #change at runtime
									        ) AS aa
									    INNER JOIN user_permissions up ON
									        up.workspace_id IS NULL AND
									        aa.updated_user_id = up.user_id AND
									        aa.project_id = up.project_id
									    LEFT JOIN projects p ON
									        aa.project_id = p.id
								    ) AS recent
								GROUP BY recent.project_id, recent.title
								ORDER BY MAX(recent.updated) DESC
								LIMIT 50
							";
						$viewData['data'] = $this->Project->query($query);
					}
					if($type == 'recent-wsp'){
						$query = "SELECT
								    project_id,
								    workspace_id,
								    title
								FROM
									(
									    SELECT
									 		aa.project_id,
									        aa.workspace_id,
									        w.title,
									        aa.updated
									 	FROM
									        (
									        SELECT wa.project_id, wa.workspace_id, wa.updated_user_id, wa.updated
									        FROM
									            workspace_activities wa
									        WHERE
									            wa.updated_user_id = $current_user #change at runtime
									        UNION ALL
									        SELECT ta.project_id, ta.workspace_id, ta.updated_user_id, ta.updated
									        FROM
									            activities ta
									        WHERE
									            ta.updated_user_id = $current_user #change at runtime
									        ) AS aa
									    INNER JOIN user_permissions up ON
									        up.area_id IS NULL AND
									        aa.updated_user_id = up.user_id AND
									        aa.workspace_id = up.workspace_id AND
									        aa.project_id = up.project_id
									    LEFT JOIN workspaces w ON
									        aa.workspace_id = w.id AND
									        w.status = 1
								        #WHERE w.id IS NOT NULL
								    ) AS recent

								GROUP BY recent.project_id, recent.workspace_id, recent.title
								ORDER BY MAX(recent.updated) DESC
								LIMIT 50

							";
						$viewData['data'] = $this->Project->query($query);
					}
					if($type == 'recent-tasks'){
						$query = "SELECT
									project_id,
								    workspace_id,
								    element_id,
								    title
								FROM
									(
									    SELECT
									       	a.project_id,
									    	a.workspace_id,
									 		a.element_id,
									        e.title,
									        a.updated
									 	FROM
											activities a
										INNER JOIN user_permissions up ON
									        a.updated_user_id = up.user_id AND
									        a.element_id = up.element_id
										LEFT JOIN elements e ON
									        a.element_id = e.id
										WHERE
									        a.updated_user_id = $current_user AND #change at runtime
									        e.status = 1 #not deleted
								    ) AS recent
								GROUP BY recent.project_id, recent.workspace_id, recent.element_id, recent.title
								ORDER BY MAX(recent.updated) DESC
								LIMIT 50
								";
						$viewData['data'] = $this->Project->query($query);
					}
					if($type == 'recent-assets'){
						$query = "SELECT
									project_id,
								    workspace_id,
								    element_id,
								    relation_id,
								    element_type,
								    title
								FROM
									(
									    SELECT
									        a.project_id,
										    a.workspace_id,
									 		a.element_id,
									        a.relation_id,
									        a.element_type,
									        (CASE element_type
									    	WHEN 'element_links' THEN element_links.title
											WHEN 'element_notes' THEN element_notes.title
									     	WHEN 'element_documents' THEN element_documents.title
									     	WHEN 'element_mindmaps' THEN element_mindmaps.title
									     	WHEN 'element_decisions' THEN element_decisions.title
									     	WHEN 'feedback' THEN feedback.title #updated
									     	WHEN 'votes' THEN votes.title
									    END) AS title,
									        a.updated
									 	FROM
											activities a
										INNER JOIN user_permissions up ON
									        a.updated_user_id = up.user_id AND
									        a.element_id = up.element_id
										LEFT JOIN elements e ON
									        a.element_id = e.id
									    LEFT JOIN element_links ON a.relation_id = element_links.id
									    LEFT JOIN element_notes ON a.relation_id = element_notes.id
									    LEFT JOIN element_documents ON a.relation_id = element_documents.id
									    LEFT JOIN element_mindmaps ON a.relation_id = element_mindmaps.id
									    LEFT JOIN element_decisions ON a.relation_id = element_decisions.id
									    LEFT JOIN feedback ON a.relation_id = feedback.id #updated
									    LEFT JOIN votes ON a.relation_id = votes.id
										WHERE
									        a.updated_user_id = $current_user AND #change at runtime
									        a.element_type IN ('element_decisions', 'element_documents', 'element_links', 'element_mindmaps', 'element_notes', 'feedback', 'votes') AND
									        e.status = 1 #not deleted
								    ) AS recent
								GROUP BY recent.project_id, recent.workspace_id, recent.element_id, recent.relation_id, recent.element_type, recent.title
								HAVING recent.title IS NOT NULL
								ORDER BY MAX(recent.updated) DESC
								LIMIT 50
							";
						$viewData['data'] = $this->Project->query($query);
					}
					$this->set($viewData);
				}
			}
		}

		$this->render('/Elements/front/recent_next_menus');
	}


	public function validate_password() {
		if ($this->request->isAjax()) {

			$this->layout = false;
			$response = [
				'success' => false,
				'content' => null,
			];
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;

				$userId = $this->user_id; // from the question

				$userInput = $post['pass'];
				$hashedUserInput = Security::hash($userInput, null, true);

				$passData = $this->User->find('count', array(
					'conditions' => array(
						'password' => $hashedUserInput,
						'id' => $userId,
					),
					'recursive' => -1,
				));
				if (isset($passData) && !empty($passData)) {
					$response['success'] = true;
				}
			}
			echo json_encode($response);
			exit();
		}
	}


	public function mongo_operations() {
		if ($this->request->isAjax()) {

			$this->layout = false;
			$response = [
				'success' => false,
				'content' => null,
			];
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				$user_projects = $this->user_projects();
				$response['success'] = true;
				$response['content'] = $user_projects;
			}
			echo json_encode($response);
			exit();
		}
	}

	public function mongo_user_projects() {
		$this->autoLayout = false;
		$this->render(false);

		$user_projects = $this->user_projects();

		$response = [
			'success' => true,
			'content' => $user_projects,
		];

		$this->response->body(json_encode($response));
		$this->response->statusCode(200);
		$this->response->type('application/json');

		return $this->response;

		die;
	}

	/*
	 * user wise projects
	 * @param Array $user_id
	*/
	function user_projects($user_id = null) {

		$data = [];
		if(isset($user_id) && !empty($user_id) && is_array($user_id)) {
			$users = $user_id;
		}
		else {
			$users = $this->User->find('list', ['conditions' => ['User.status' => 1, 'User.role_id' => 2], 'fields' => ['User.id', 'User.email']]);
		}
		if(isset($users) && !empty($users)) {
			foreach ($users as $userId => $email) {
				$projects = $this->Common->get_user_project_list_chat($userId);
				$projects = array_filter($projects);
				if (isset($projects) && !empty($projects)) {
					foreach ($projects as $project_id => $title) {
						$data[] = ['userId' => $userId, 'projectId' => $project_id];
					}
				}
			}
		}

		return $data;
	}

	public function mongoProjectUpdate($project_id, $project_title) {

		if( PHP_VERSIONS == 5 ) {

			$mongo = new MongoClient(MONGO_CONNECT);
			$this->mongoDB = $mongo->selectDB(MONGO_DATABASE);
			$mongo_collection = new MongoCollection($this->mongoDB, 'projects');

			$ret = $mongo_collection->update(['id' => intval($project_id, 10)], ['$set' => ['title' => strip_tags($project_title)]]);

		} else {

			$mongo = new MongoDB\Driver\Manager(MONGO_CONNECT);
			$bulk = new MongoDB\Driver\BulkWrite;

			$bulk->update(['id' => intval($project_id, 10)], ['$set' => ['title' => strip_tags($project_title)]]);

			$mongo->executeBulkWrite(MONGO_DATABASE.'.projects', $bulk);

		}

	}

	public function load_chat() {
		if ($this->request->isAjax()) {

			$this->layout = false;
			$viewData = null;
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				// $viewData['opt_in_status'] = $post['opt_in_status'];
			}

			$view = new View($this, false);
			$view->viewPath = 'Elements/front';
			$html = $view->render('chat_7_partial');
			echo json_encode($html);
			exit();
		}
	}

	public function mongo_user_status() {
		if ($this->request->isAjax()) {

			$this->layout = false;
			$viewData = null;
			$view = new View($this, false);
			$view->viewPath = 'Elements/front';

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				// pr($post);
			}

	        $user_id = $this->Session->read('Auth.User.id');
			$mongo_user_status = $this->Users->user_status($user_id);
			$viewData['mongo_user_status'] = $mongo_user_status;

			$view->set($viewData);
			$html = $view->render('user_status');
			echo json_encode($html);
			exit();
		}
	}

	public function talk_now_menu() {
		if ($this->request->isAjax()) {

			$this->layout = false;
			$viewData = null;
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
			}

			$view = new View($this, false);
			$view->viewPath = 'Elements/front';
			$view->set($viewData);
			$html = $view->render('chat_7_projects');
			echo json_encode($html);
			exit();
		}
	}


}