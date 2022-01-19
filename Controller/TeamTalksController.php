<?php

App::uses('Controller/Component', 'Auth', 'Session', 'RequestHandler');
App::uses('Sanitize', 'Utility');
App::uses('CakeEmail', 'Network/Email');
//App::import('Vendor', 'Classes/MPDF56/mpdf');
App::import('Vendor', 'MPDF56/PhpWord');

// App::import('Lib', 'Communications');

class TeamTalksController extends AppController {

	public $name = 'TeamTalks';
	public $uses = [
		'User',
		'UserDetail',
		'ProjectPermission',
		'UserSetting',
		'Category',
		'Aligned',
		'UserProject',
		'Project',
		'Workspace',
		'Area',
		'ProjectWorkspace',
		'Element',
		'ProjectGroup',
		'ProjectGroupUser',
		'Wiki',
		'Blog',
		'BlogComment',
		'BlogDocument',
		'BlogView',
		'EmailNotification',
	];
	public $user_id = null;
	public $pagination = null;
	public $components = array('Mpdf', 'Common', 'Group');

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
	public $helpers = array('Html', 'Form', 'Session', 'Time', 'Text', 'Common', 'ViewModel', 'Mpdf', 'TeamTalk', 'Js' => array('Jquery'));

	public function beforeFilter() {
		parent::beforeFilter();
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

		// $this->Auth->allow('index');
		// $s = $this->Auth->allowedActions;
		// pr($this->Auth->allowedActions);
		// pr($this->Auth->allowedControllers, 1);
		
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
		     * @name  		get_more
		     * @access		public
		     * @package  	App/Controller/ProjectsController
	*/

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

	/*
		     * @name  		lists
		     * @access		public
		     * @package  	App/Controller/ProjectsController
	*/

	public function total_recieved($category_id = null) {
		$conditions = null;
		$conditions['ProjectPermission.user_id'] = $this->user_id;
		$conditions['ProjectPermission.user_project_id !='] = ''; // ['not' => array('User.site_url' => null)];
		$conditions['ProjectPermission.owner_id !='] = '';
		$conditions['ProjectPermission.share_by_id !='] = '';

		$this->loadModel('ProjectPermission');

		$projects = $this->ProjectPermission->find('count', array(
			'conditions' => $conditions,
			'fields' => array('ProjectPermission.*'),
			'recursive' => -1,
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
		//  pr($projects);
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

	// function for Blog Page
	public function index($project_id = null) {
		//pr($this->params);die;
		if (isset($this->params['named']['project']) && !empty($this->params['named']['project']) ) {
			if(!dbExists('Project', $this->params['named']['project'])){
				$this->redirect(array('controller' => 'team_talks', 'action' => 'index'));
			}
		}
		$viewVars = $data = null;
		$this->layout = 'inner';
		$data['title_for_layout'] = __('My Blogs', true);
		$data['page_heading'] = __('My Blogs', true);
		$data['page_subheading'] = __('View Blogs shared by you and other team members', true);

		$this->set($data);

		$crumb = ['last' => ['data' => ['title' => 'My Blogs', 'data-original-title' => 'My Blogs']]];

		$this->set('crumb', $crumb);

		$projects = [];
		$mprojects = get_my_projects($this->user_id, null, 1);
		$rprojects = get_rec_projects($this->user_id, 1, 1, 1);
		$gprojects = group_rec_projects($this->user_id, null, 1);

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

		$this->set('projectlists', $projects);

		$project_id = $workspace = $status = null;

		if (isset($this->params['named']) && !empty($this->params['named'])) {
			$params = $this->params['named'];
			$viewVars['project_id'] = isset($params['project']) && !empty($params['project']) ? $params['project'] : null;
		}

		$this->set($viewVars);
	}

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

				/*
					if (isset($post['type']) && $post['type'] == 1) {
						$response['content'] = get_my_projects($this->user_id, null, 1);
					} else if (isset($post['type']) && $post['type'] == 2) {
						$response['content'] = get_rec_projects($this->user_id, 1, 1, 1);
					} else if (isset($post['type']) && $post['type'] == 3) {
						$response['content'] = group_rec_projects($this->user_id, null, 1);
				*/

				$projects = [];
				$mprojects = get_my_projects($this->user_id, null, 1);
				$rprojects = get_rec_projects($this->user_id, 1, 1, 1);
				$gprojects = group_rec_projects($this->user_id, null, 1);

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
					$projects = array_map("html_entity_decode", $projects);
					$projects = array_map("trim", $projects);
					$projects = array_map(function ($v) {
						return html_entity_decode($v, ENT_COMPAT, "UTF-8");
					}, $projects);
					natcasesort($projects);
				}
				$response['content'] = $projects;

				if (empty($response['content'])) {
					$response['content'] = null;
				}
				// pr($response['content']);
				// pr($post, 1);
			}
			echo json_encode($response);
			exit();
		}
	}

	public function projects_list() {
		$html = '';
		if ($this->request->isAjax()) {

			$view = new View();
			$viewModel = $view->loadHelper('ViewModel');

			$this->loadModel('Project');

			$this->layout = 'ajax';
			$response = [
				'success' => false,
				'msg' => '',
				'content' => null,
			];

			$row = $data = null;
			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;
				$result = $params = null;

				$conditions = $pw_condition = [];
				$order = '';

				if (isset($this->request->data['project_id']) && !empty($this->request->data['project_id'])) {

					$data = $this->Project->find("first", array('conditions' => array('Project.id' => $this->request->data['project_id']), 'fields' => array('Project.id', 'Project.title', 'Project.start_date', 'Project.end_date', 'Project.color_code'), 'recursive' => -1));
				}

				$this->set('data', $data);
				$view->layout = false; //if you want to disable layout
				$html = $this->render('partials/task_list');
			}
			return $html;
			exit();
		}
	}

	// Create/Save blog
	public function create_blogs($project_id = null) {

		if ($this->request->isAjax()) {
			$this->layout = 'ajax';

			$this->loadModel('UserProject');

			if (isset($this->request->data['Blog']['project_id'])) {
				$project_id = $this->request->data['Blog']['project_id'];
				$this->set('project_id', $project_id);
			}

			$this->Blog->set($this->request->data);
			$response = null;
			$response['success'] = false;
			$response['content'] = null;

			if ($this->Blog->validates()) {
				if (!empty($this->request->data)) {

					if ($this->Blog->save($this->request->data)) {

						$this->Common->projectModified($project_id, $this->Session->read('Auth.User.id'));
						//========================== Send Email to Project all Participants ==============
						$owner = $this->Common->ProjectOwner($project_id, $this->Session->read('Auth.User.id'));

						$participants = participants($project_id, $owner['UserProject']['user_id']);
						$participants_owners = participants_owners($project_id, $owner['UserProject']['user_id']);
						$participantsGpOwner = participants_group_owner($project_id);
						$participantsGpSharer = participants_group_sharer($project_id);

						$participants = isset($participants) ? array_filter($participants) : $participants = array();
						$participants_owners = isset($participants_owners) ? array_filter($participants_owners) : $participants_owners = array();
						$participantsGpOwner = isset($participantsGpOwner) ? array_filter($participantsGpOwner) : $participantsGpOwner = array();
						$participantsGpSharer = isset($participantsGpSharer) ? array_filter($participantsGpSharer) : $participantsGpSharer = array();

						$allUsers = array_merge($participants, $participants_owners, $participantsGpOwner, $participantsGpSharer);
						//pr($allUsers, 1);
						$lastblogid = $this->Blog->getLastInsertId();
						$user_id = $this->Session->read('Auth.User.id');
						if (isset($allUsers) && !empty($allUsers) && count($allUsers) > 0) {
							foreach ($allUsers as $listUsers) {

								$notifiragstatus = $this->EmailNotification->find('first', ['conditions' => ['notification_type' => 'team_talk', 'personlization' => 'blog_created', 'user_id' => $listUsers]]);

								$usersDetails = $this->User->find('first', array('conditions' => array('User.id' => $listUsers)));

								if ((!isset($notifiragstatus['EmailNotification']['email']) || $notifiragstatus['EmailNotification']['email'] == 1) && (!isset($usersDetails['User']['email_notification']) || $usersDetails['User']['email_notification'] == 1)) {

									$this->Common->project_blog_email($listUsers, $project_id, $lastblogid);

								}
							}

							/************** socket messages **************/
							if (SOCKET_MESSAGES) {
								$current_user_id = $this->Session->read('Auth.User.id');
								$blog_users = $allUsers;
								if (isset($blog_users) && !empty($blog_users)) {
									if (($key = array_search($current_user_id, $blog_users)) !== false) {
										unset($blog_users[$key]);
									}
								}
								$open_users = null;
								if (isset($blog_users) && !empty($blog_users)) {
									foreach ($blog_users as $key1 => $value1) {
										if (web_notify_setting($value1, 'team_talk', 'blog_created')) {
											$open_users[] = $value1;
										}
									}
								}
								$userDetail = get_user_data($current_user_id);
								$content = [
									'notification' => [
										'type' => 'blog_created',
										'created_id' => $current_user_id,
										'project_id' => $project_id,
										'refer_id' => $lastblogid,
										'creator_name' => $userDetail['UserDetail']['full_name'],
										'subject' => 'Blog created',
										'heading' => 'Blog: ' . strip_tags(getFieldDetail('Blog', $lastblogid, 'title')),
										'sub_heading' => 'Project: ' . strip_tags(getFieldDetail('Project', $project_id, 'title')),
										'date_time' => timezoneTimeConvertor($this->user_id, date('Y-m-d g:iA'), $format = 'd M Y g:iA') . user_country($this->user_id),
									],
								];
								if (is_array($open_users)) {
									$content['received_users'] = array_values($open_users);
								}
								$response['content']['socket'] = $content;
							}
							/************** socket messages **************/
						}
						//================================================================================

						$this->Session->setFlash('Blog created successfully.', 'success');
						$response['success'] = true;
					}
				}
			} else {
				$response['content'] = $this->validateErrors($this->Blog);
			}

			$this->render('/TeamTalks/create_blog');
			echo json_encode($response);
			exit;
		}
	}

	public function create_blog($project_id = null, $user_id = null) {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$response = null;

			$this->set('project_id', $project_id);

			$this->render('/TeamTalks/create_blog');
		}
	}

	public function edit_blog($blog_id = null) {

		// if ($this->request->isAjax()) {

		$this->layout = 'ajax';
		$response = null;

		if ($this->request->is('post') || $this->request->is('put')) {
			unset($this->request->data['Blog']['descriptions']);

			if (isset($this->request->data['Blog']['project_id'])) {
				$project_id = $this->request->data['Blog']['project_id'];
				$this->set('project_id', $project_id);
			}

			if (!empty($this->request->data)) {
				if ($this->Blog->save($this->request->data)) {

					//========================== Send Email to Project all Participants ==============
					$owner = $this->Common->ProjectOwner($project_id, $this->Session->read('Auth.User.id'));

					$participants = participants($project_id, $owner['UserProject']['user_id']);
					$participants_owners = participants_owners($project_id, $owner['UserProject']['user_id']);
					$participantsGpOwner = participants_group_owner($project_id);
					$participantsGpSharer = participants_group_sharer($project_id);

					$participants = isset($participants) ? array_filter($participants) : $participants = array();
					$participants_owners = isset($participants_owners) ? array_filter($participants_owners) : $participants_owners = array();
					$participantsGpOwner = isset($participantsGpOwner) ? array_filter($participantsGpOwner) : $participantsGpOwner = array();
					$participantsGpSharer = isset($participantsGpSharer) ? array_filter($participantsGpSharer) : $participantsGpSharer = array();

					$allUsers = array_merge($participants, $participants_owners, $participantsGpOwner, $participantsGpSharer);

					$lastblogid = $this->request->data['Blog']['id'];
					$user_id = $this->Session->read('Auth.User.id');

					if (isset($allUsers) && count($allUsers) > 0) {
						foreach ($allUsers as $listUsers) {

							$notifiragstatus = $this->EmailNotification->find('first', ['conditions' => ['notification_type' => 'team_talk', 'personlization' => 'blog_updated', 'user_id' => $listUsers]]);

							$usersDetails = $this->User->find('first', array('conditions' => array('User.id' => $listUsers)));

							if ((!isset($notifiragstatus['EmailNotification']['email']) || $notifiragstatus['EmailNotification']['email'] == 1) && (!isset($usersDetails['User']['email_notification']) || $usersDetails['User']['email_notification'] == 1)) {

								$this->Common->project_update_blog_email($listUsers, $project_id, $lastblogid);

							}

						}

						/************** socket messages **************/
						if (SOCKET_MESSAGES) {
							$current_user_id = $this->Session->read('Auth.User.id');
							$blog_users = $allUsers;
							if (isset($blog_users) && !empty($blog_users)) {
								if (($key = array_search($current_user_id, $blog_users)) !== false) {
									unset($blog_users[$key]);
								}
							}
							$open_users = null;
							if (isset($blog_users) && !empty($blog_users)) {
								foreach ($blog_users as $key1 => $value1) {
									if (web_notify_setting($value1, 'team_talk', 'blog_updated')) {
										$open_users[] = $value1;
									}
								}
							}
							$userDetail = get_user_data($current_user_id);
							$content = [
								'notification' => [
									'type' => 'blog_updated',
									'created_id' => $current_user_id,
									'project_id' => $project_id,
									'refer_id' => $lastblogid,
									'creator_name' => $userDetail['UserDetail']['full_name'],
									'subject' => 'Blog updated',
									'heading' => 'Blog: ' . strip_tags(getFieldDetail('Blog', $lastblogid, 'title')),
									'sub_heading' => 'Project: ' . strip_tags(getFieldDetail('Project', $project_id, 'title')),
									'date_time' => timezoneTimeConvertor($this->user_id, date('Y-m-d g:iA'), $format = 'd M Y g:iA') . user_country($this->user_id),
								],
							];
							if (is_array($open_users)) {
								$content['received_users'] = array_values($open_users);
							}
							$response['content']['socket'] = $content;
						}
						/************** socket messages **************/
					}

					//================================================================================

					$this->Session->setFlash('Blog updated successfully.', 'success');
					$response['success'] = true;
					echo json_encode($response);
					exit;
				} else {
					$response['content'] = $this->validateErrors($this->Blog);

					echo json_encode($response);
					exit;
				}
			}
		} else {
			$this->request->data = $this->Blog->read(null, $blog_id);
			$response['error'] = false;
		}

		$this->set('blog_id', $blog_id);

		$this->render('/TeamTalks/partials/edit_blog');

		//   }
	}

	public function user_comment_list($blog_id = null, $project_id = null) {
		$this->layout = 'ajax';
		$blog_comments = $this->BlogComment->find("all", array("conditions" => array("BlogComment.blog_id" => $blog_id), 'order' => 'BlogComment.updated DESC'));
		$this->set("blog_comments", $blog_comments);
		$this->render('partials/user_comment_list');
	}

	public function blog_documents_list($blog_id = null, $project_id = null) {

		//pr($this->request->data, 1);

		$this->loadModel('BlogDocument');
		$this->layout = 'ajax';
		$blog_documents = $this->BlogDocument->find("all", array("conditions" => array("BlogDocument.blog_id" => $this->request->data['blog_id'], "BlogDocument.blog_comment_id" => 0), 'order' => 'BlogDocument.updated DESC'));
		//pr($blog_documents, 1);
		$this->set("blog_documents", $blog_documents);
		$this->render('partials/blog_documents_list');

	}

	public function blog_comment_document_delete() {
		#create by pukhraj -04-05-2016
		$this->layout = 'ajax';
		$this->autoRender = false;
		$this->loadModel('BlogDocument');
		if ($this->request->isAjax()) {
			$response = [
				'success' => false,
				'msg' => 'Sorry accor some error.',
				'content' => null,
			];
			if (isset($this->request->data['id']) && !empty($this->request->data['id']) && $this->request->data['id'] != 'null') {
				$id = $this->request->data['id'];
				$fielname = $this->request->data['file_name'];
				$conditions = array("BlogDocument.document_name" => $fielname);
				if (isset($id) && !empty($id)) {
					$conditions['AND'][] = array("BlogDocument.id" => $id);
				}
				$old = $this->BlogDocument->find("first", array("conditions" => $conditions));
				$this->request->data['BlogDocument']['id'] = $old['BlogDocument']['id'];
				$filePath = DO_LIST_BLOG_DOCUMENTS . $old['BlogDocument']['document_name'];
				if ($this->BlogDocument->delete($this->request->data['BlogDocument']['id'])) {
					if (file_exists($filePath)) {
						unlink($filePath);
					}
					$response = [
						'success' => true,
						'msg' => 'The comment attachment has been removed successfully.',
						'content' => null,
					];
					echo json_encode($response);
					exit();
				} else {
					echo json_encode($response);
					exit();
				}
			} else {

				$filePath = DO_LIST_BLOG_DOCUMENTS . $this->request->data['file_name'];
				if (file_exists($filePath)) {
					unlink($filePath);
				}
				$response = [
					'success' => true,
					'msg' => 'The comment attachment has been removed successfully.',
					'content' => null,
				];
				echo json_encode($response);
				exit();
			}
		}
	}

	public function delete_blog_document() {
		#create by pukhraj -04-05-2016
		$this->layout = 'ajax';
		$this->autoRender = false;
		$this->loadModel('BlogDocument');
		if ($this->request->isAjax()) {
			$response = [
				'success' => false,
				'msg' => 'Sorry accor some error.',
				'content' => null,
			];

			//pr($this->request->data, 1);

			if (isset($this->request->data['id']) && !empty($this->request->data['id']) && $this->request->data['id'] != 'null') {
				$id = $this->request->data['id'];
				//$fielname = $this->request->data['file_name'];
				//$conditions = array("BlogDocument.document_name" => $fielname);
				if (isset($id) && !empty($id)) {
					$conditions['AND'][] = array("BlogDocument.id" => $id);
				}
				$old = $this->BlogDocument->find("first", array("conditions" => $conditions));
				$this->request->data['BlogDocument']['id'] = $old['BlogDocument']['id'];
				$filePath = DO_LIST_BLOG_DOCUMENTS . $old['BlogDocument']['document_name'];
				if ($this->BlogDocument->delete($this->request->data['BlogDocument']['id'])) {
					if (file_exists($filePath)) {
						unlink($filePath);
					}
					$response = [
						'success' => true,
						'msg' => 'The blog attachment has been removed successfully.',
						'content' => null,
					];
					echo json_encode($response);
					exit();
				} else {
					echo json_encode($response);
					exit();
				}
			} else {

				$filePath = DO_LIST_BLOG_DOCUMENTS . $this->request->data['file_name'];
				if (file_exists($filePath)) {
					unlink($filePath);
				}
				$response = [
					'success' => true,
					'msg' => 'The blog attachment has been removed successfully.',
					'content' => null,
				];
				echo json_encode($response);
				exit();
			}
		}
	}

	public function add_blog_comments($blog_id = null, $project_id = null) {

		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			if ($this->request->is('post') || $this->request->is('put')) {
				$this->autoRender = false;
				$response = ['success' => false, 'msg' => "Invalid", 'content' => null];
				$this->BlogComment->set($this->data);

				if ($this->BlogComment->validates()) {

					if (isset($this->request->data['BlogDocument']['file_name']) && !empty($this->request->data['BlogDocument']['file_name'])) {
						$files = $this->request->data['BlogDocument']['file_name'];
						unset($this->request->data['BlogDocument']['file_name']);
						foreach ($files as $file_key => $file) {
							$this->request->data['BlogDocument'][$file_key]["document_name"] = $file;
							$this->request->data['BlogDocument'][$file_key]["user_id"] = $this->request->data['BlogComment']['user_id'];

							$this->request->data['BlogDocument'][$file_key]["blog_id"] = $this->request->data['BlogComment']['blog_id'];
						}
					}

					if ($this->BlogComment->saveAll($this->request->data)) {

						$this->Common->projectModified($this->request->data['BlogComment']['project_id'], $this->request->data['BlogComment']['user_id']);
						$response['success'] = true;
						$response['msg'] = 'The comment has been saved successfully.';
						$response['content'] = $this->request->data;
						echo json_encode($response);
						exit();

					}
				} else {
					$response['success'] = false;
					$response['msg'] = 'The comment could not be saved. Please, try again.';
					$response['content'] = $this->validateErrors($this->BlogComment);
					echo json_encode($response);
					exit();
				}
			} else {
				$data['blog_id'] = (isset($this->request->params['named']['blog_id']) && !empty($this->request->params['named']['blog_id'])) ? $this->request->params['named']['blog_id'] : "";
				$data['project_id'] = (isset($this->request->params['named']['project_id']) && !empty($this->request->params['named']['project_id'])) ? $this->request->params['named']['project_id'] : "";

				$this->set('data', $data);
				$this->render('partials/add_blog_comments');
			}
		}
	}

	public function add_comment_blog($blog_id = null, $project_id = null) {

		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			if ($this->request->is('post') || $this->request->is('put')) {
				$this->autoRender = false;
				$response = ['success' => false, 'msg' => "Invalid", 'content' => null];
				$this->BlogComment->set($this->data);
				if ($this->BlogComment->validates()) {
					if (isset($this->request->data['BlogDocument']['file_name']) && !empty($this->request->data['BlogDocument']['file_name'])) {
						$files = $this->request->data['BlogDocument']['file_name'];
						unset($this->request->data['BlogDocument']['file_name']);
						foreach ($files as $file_key => $file) {
							$this->request->data['BlogDocument'][$file_key]["document_name"] = $file;
							$this->request->data['BlogDocument'][$file_key]["user_id"] = $this->request->data['BlogComment']['user_id'];
							$this->request->data['BlogDocument'][$file_key]["blog_id"] = $this->request->data['BlogComment']['blog_id'];
						}
					}
					//pr($this->request->data); exit;
					if ($this->BlogComment->saveAll($this->request->data)) {

						$this->Common->projectModified($this->request->data['BlogComment']["project_id"], $this->request->data['BlogComment']["user_id"]);

						$response['success'] = true;
						$response['msg'] = 'The comment has been saved successfully.';
						$response['content'] = $this->request->data;

						if (isset($this->request->data['BlogComment']['refer_id']) && !empty($this->request->data['BlogComment']['refer_id'])) {
							$response['refer_id'] = $this->request->data['BlogComment']['refer_id'];
							$this->set('refer_id', $this->request->data['BlogComment']['refer_id']);
						} else {
							$response['refer_id'] = null;
						}

						echo json_encode($response);
						exit();
					}
				} else {
					$response['success'] = false;
					$response['msg'] = 'The comment could not be saved. Please, try again.';
					$response['content'] = $this->validateErrors($this->BlogComment);
					echo json_encode($response);
					exit();
				}
			} else {
				$data['blog_id'] = (isset($this->request->params['named']['blog_id']) && !empty($this->request->params['named']['blog_id'])) ? $this->request->params['named']['blog_id'] : "";
				$data['project_id'] = (isset($this->request->params['named']['project_id']) && !empty($this->request->params['named']['project_id'])) ? $this->request->params['named']['project_id'] : "";

				$this->set('data', $data);

				$this->render('partials/add_comment_blog');
			}
		}
	}

	public function edit_blog_comments($blog_id = null, $project_id = null) {

		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			if ($this->request->is('post') || $this->request->is('put')) {
				$this->autoRender = false;
				$response = ['success' => false, 'msg' => "Invalid", 'content' => null];
				$this->BlogComment->set($this->data);
				if ($this->BlogComment->validates()) {
					if (isset($this->request->data['BlogDocument']['file_name']) && !empty($this->request->data['BlogDocument']['file_name'])) {
						$files = $this->request->data['BlogDocument']['file_name'];

						unset($this->request->data['BlogDocument']['file_name']);
						foreach ($files as $file_key => $file) {

							$this->request->data['BlogDocument'][$file_key]["document_name"] = $file;
							$this->request->data['BlogDocument'][$file_key]["user_id"] = $this->request->data['BlogComment']['user_id'];
							$this->request->data['BlogDocument'][$file_key]["blog_id"] = $this->request->data['BlogComment']['blog_id'];
						}

					}

					/* if(isset($this->params['com_class']) && !empty($this->params['com_class'])){
							pr($this->params['com_class']);

						}
					*/
					//pr($this->request->data, 1);

					if ($this->BlogComment->saveAll($this->request->data)) {
						$response['success'] = true;
						$response['msg'] = 'The comment has been saved successfully.';
						$response['content'] = $this->request->data;
						echo json_encode($response);
						exit();
					}
				} else {
					$response['success'] = false;
					$response['msg'] = 'The comment could not be saved. Please, try again.';
					$response['content'] = $this->validateErrors($this->BlogComment);

					echo json_encode($response);
					exit();
				}
			} else {

				$blog_id = (isset($this->request->params['named']['blog_id']) && !empty($this->request->params['named']['blog_id'])) ? $this->request->params['named']['blog_id'] : "";
				$comment_id = (isset($this->request->params['named']['comment_id']) && !empty($this->request->params['named']['comment_id'])) ? $this->request->params['named']['comment_id'] : "";

				$this->request->data = $this->BlogComment->read(null, $comment_id);
				$this->render('partials/edit_blog_comments');
			}
		}
	}

	public function edit_blog_comments_list($blog_id = null, $comment_id = null) {

		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			if ($this->request->is('post') || $this->request->is('put')) {
				$this->autoRender = false;
				$response = ['success' => false, 'msg' => "Invalid", 'content' => null];
				$this->BlogComment->set($this->data);
				if ($this->BlogComment->validates()) {
					if (isset($this->request->data['BlogDocument']['file_name']) && !empty($this->request->data['BlogDocument']['file_name'])) {
						$files = $this->request->data['BlogDocument']['file_name'];

						unset($this->request->data['BlogDocument']['file_name']);
						foreach ($files as $file_key => $file) {

							$this->request->data['BlogDocument'][$file_key]["document_name"] = $file;
							$this->request->data['BlogDocument'][$file_key]["user_id"] = $this->request->data['BlogComment']['user_id'];
							$this->request->data['BlogDocument'][$file_key]["blog_id"] = $this->request->data['BlogComment']['blog_id'];
						}

					}

					/* if(isset($this->params['com_class']) && !empty($this->params['com_class'])){
							pr($this->params['com_class']);

						}
					*/
					//pr($this->request->data, 1);

					if ($this->BlogComment->saveAll($this->request->data)) {

						$data = $this->BlogComment->find('first', array("conditions" => array("BlogComment.id" => $this->request->data['BlogComment']['id'])));

						$this->set('data', $data);
						$response['content'] = $this->render('partials/update_comment_list');

						$response['success'] = true;
						$response['msg'] = 'The comment has been saved successfully.';

						return $response;

					}
				} else {
					$response['success'] = false;
					$response['msg'] = 'The comment could not be saved. Please, try again.';
					$response['content'] = $this->validateErrors($this->BlogComment);

					echo json_encode($response);
					exit();
				}
			} else {

				$blog_id = (isset($this->request->params['named']['blog_id']) && !empty($this->request->params['named']['blog_id'])) ? $this->request->params['named']['blog_id'] : "";
				$comment_id = (isset($this->request->params['named']['comment_id']) && !empty($this->request->params['named']['comment_id'])) ? $this->request->params['named']['comment_id'] : "";

				$this->request->data = $this->BlogComment->read(null, $comment_id);
				$this->render('partials/edit_blog_comments_list');
			}
		}
	}

	public function addedit_comment_blog($blog_id = null, $comment_id = null) {

		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			$response = array();
			if ($this->request->is('post') || $this->request->is('put')) {
				$this->autoRender = false;
				$response = ['success' => false, 'msg' => "Invalid", 'content' => null];
				$this->BlogComment->set($this->data);
				// pr($this->request->data);die;
				unset($this->request->data['Blog']);

				if ($this->BlogComment->validates()) {
					if (isset($this->request->data['BlogDocument']['file_name']) && !empty($this->request->data['BlogDocument']['file_name'])) {
						$files = $this->request->data['BlogDocument']['file_name'];

						unset($this->request->data['BlogDocument']['file_name']);
						foreach ($files as $file_key => $file) {

							$this->request->data['BlogDocument'][$file_key]["document_name"] = $file;
							$this->request->data['BlogDocument'][$file_key]["user_id"] = $this->request->data['BlogComment']['user_id'];
							$this->request->data['BlogDocument'][$file_key]["blog_id"] = $this->request->data['BlogComment']['blog_id'];
						}

					}

					if ($this->BlogComment->saveAll($this->request->data)) {

						$data = $this->BlogComment->find('first', array("conditions" => array("BlogComment.id" => $this->request->data['BlogComment']['id'])));

						//$this->set('data', $data);
						//$response ['content'] = $this->render('partials/update_comment_list');

						$response['success'] = true;
						$response['msg'] = 'The comment has been saved successfully.';

						if (isset($this->request->data['BlogComment']['refer_id']) && !empty($this->request->data['BlogComment']['refer_id'])) {
							$response['refer_id'] = $this->request->data['BlogComment']['refer_id'];
						} else {
							$response['refer_id'] = null;
						}

						echo json_encode($response);

					}
				} else {
					$response['success'] = false;
					$response['msg'] = 'The comment could not be saved. Please, try again.';
					$response['content'] = $this->validateErrors($this->BlogComment);

					echo json_encode($response);
					exit();
				}
			} else {

				$blog_id = (isset($this->request->params['named']['blog_id']) && !empty($this->request->params['named']['blog_id'])) ? $this->request->params['named']['blog_id'] : "";
				$comment_id = (isset($this->request->params['named']['comment_id']) && !empty($this->request->params['named']['comment_id'])) ? $this->request->params['named']['comment_id'] : "";

				$this->request->data = $this->BlogComment->read(null, $comment_id);
				$this->render('partials/addedit_comment_blog');
			}
		}
	}

	public function blog_comments($blog_id = null, $project_id = null) {

		if ($this->request->isAjax()) {
			$this->layout = 'ajax';

			$this->loadModel('BlogView');

			$response = null;
			$data['blog_id'] = $blog_id = $this->request->data['blog_id'];
			$data['project_id'] = $project_id = $this->request->data['project_id'];

			$response = [
				'success' => false,
				'content' => null,
			];

			// Below Code for Blog Views
			$this->Blog->unBindModel(array('hasMany' => array('BlogComment', 'BlogDocument')));
			$blogdata = $this->Blog->find("first", array("conditions" => array("Blog.id" => $blog_id)));

			if (isset($blogdata) && $blogdata['Blog']['user_id'] != $this->Session->read("Auth.User.id")) {

				$bviews = $this->BlogView->find('all', ['conditions' => ['BlogView.blog_id' => $blog_id, 'BlogView.user_id' => $this->Session->read("Auth.User.id")]]);

				if (isset($bviews) && !empty($bviews) && count($bviews) > 0) {

					$this->request->data['BlogView']['bview'] = $bviews[0]['BlogView']['bview'] + 1;
					$this->request->data['BlogView']['id'] = $bviews[0]['BlogView']['id'];
					$this->BlogView->save($this->request->data['BlogView']);

				} else {

					$this->request->data['BlogView']['user_id'] = $this->Session->read("Auth.User.id");
					$this->request->data['BlogView']['blog_id'] = $blog_id;
					$this->request->data['BlogView']['project_id'] = $project_id;
					$this->request->data['BlogView']['bview'] = 1;
					$this->BlogView->save($this->request->data['BlogView']);
				}

			}

			$this->set('data', $data);
			$this->render('partials/blog_comments');

		}
	}

	public function blog_admin($blog_id = null, $project_id = null) {

		if ($this->request->isAjax()) {
			$this->layout = 'ajax';

			$this->loadModel('BlogDocument');
			$this->loadModel('BlogComment');

			$response = null;
			$blog_list = null;
			$doc_list = null;

			$user_id = isset($this->request->data['user_id']) ? $this->request->data['user_id'] : null;
			$project_id = isset($this->request->data['project_id']) ? $this->request->data['project_id'] : null;

			$blog_id = (isset($this->request->data['blog_id']) && !empty($this->request->data['blog_id'])) ? $this->request->data['blog_id'] : null;
			$refer_id = (isset($this->request->data['refer_id']) && !empty($this->request->data['refer_id'])) ? $this->request->data['refer_id'] : null;
			$blog_type = (isset($this->request->data['blog_type']) && !empty($this->request->data['blog_type'])) ? $this->request->data['blog_type'] : null;
			$blog_userid = (isset($this->request->data['blog_userid']) && !empty($this->request->data['blog_userid'])) ? $this->request->data['blog_userid'] : null;

			$this->set('refer_id', $refer_id);
			$this->set('blog_id', $blog_id);
			$this->set('project_id', $project_id);
			$this->set('blog_type', $blog_type);

			if (isset($blog_type) && $blog_type == 'show_all_comment') {

				if ((isset($blog_id) && !empty($blog_id)) || (isset($refer_id) && !empty($refer_id))) {

					$blog_list = $this->BlogComment->find("all", array("conditions" => array("BlogComment.blog_id" => $blog_id), 'order' => 'BlogComment.updated DESC'));

					$blogids = $this->Blog->find("all", array("conditions" => array("Blog.project_id" => $project_id), 'order' => 'Blog.id DESC'));
					if (!empty($blogids)) {

						$blog_ids = Set::extract('/Blog/id', $blogids);
						$this->BlogComment->unBindModel(array('belongsTo' => array('Blog')));
						$doc_list = $this->BlogDocument->find("all", array("conditions" => array("BlogDocument.blog_id" => $blog_ids), 'order' => 'BlogDocument.updated DESC'));
					}

					$allbloglist = $this->Blog->find('all', ['conditions' => ['Blog.project_id' => $project_id], 'order' => 'Blog.id DESC']);

				}

			} else if (isset($blog_type) && $blog_type == 'show_all_document') {

				if ((isset($blog_id) && !empty($blog_id)) || (isset($refer_id) && !empty($refer_id))) {

					$doc_list = $this->BlogDocument->find("all", array("conditions" => array("BlogDocument.blog_id" => $blog_id), 'order' => 'BlogDocument.id DESC'));

					$blogdata = $this->Blog->find("all", array("conditions" => array("Blog.project_id" => $project_id), 'order' => 'Blog.id DESC'));
					if (!empty($blogdata)) {
						$blog_ids = Set::extract('/Blog/id', $blogdata);
						$blog_list = $this->BlogComment->find("all", array("conditions" => array("BlogComment.blog_id" => $blog_ids), 'order' => 'BlogComment.updated DESC'));
					}

					$allbloglist = $this->Blog->find('all', ['conditions' => ['Blog.project_id' => $project_id], 'order' => 'Blog.id DESC']);

				}

			} else if (isset($blog_type) && $blog_type == 'show_comment') {

				if ((isset($blog_id) && !empty($blog_id)) || (isset($refer_id) && !empty($refer_id))) {

					$blog_list = $this->BlogComment->find("all", array("conditions" => array("BlogComment.blog_id" => $blog_id, 'BlogComment.user_id' => $blog_userid), 'order' => 'BlogComment.updated DESC'));

					$doc_list = $this->BlogDocument->find("all", array("conditions" => array("BlogDocument.project_id" => $project_id, 'BlogDocument.user_id' => $blog_userid), 'order' => 'BlogDocument.id DESC'));

					$allbloglist = $this->Blog->find('all', ['conditions' => ['Blog.project_id' => $project_id], 'order' => 'Blog.id DESC']);

				}

			} else if (isset($blog_type) && $blog_type == 'show_document') {

				if ((isset($blog_id) && !empty($blog_id)) || (isset($refer_id) && !empty($refer_id))) {

					/* $doc_list = $this->BlogDocument->find("all",array("conditions"=>array("BlogDocument.project_id"=>$project_id, "BlogDocument.blog_id"=>$blog_id, 'BlogDocument.user_id'=> $blog_userid, "BlogDocument.blog_comment_id"=>0), 'order'=>'BlogDocument.id DESC')); */

					$doc_list = $this->BlogDocument->find("all", array("conditions" => array("BlogDocument.blog_id" => $blog_id, 'BlogDocument.user_id' => $blog_userid), 'order' => 'BlogDocument.id DESC'));

					$blogdata = $this->Blog->find("all", array("conditions" => array("Blog.project_id" => $project_id), 'order' => 'Blog.id DESC'));

					if (!empty($blogdata)) {
						$blog_ids = Set::extract('/Blog/id', $blogdata);
						$blog_list = $this->BlogComment->find("all", array("conditions" => array("BlogComment.blog_id" => $blog_ids, 'BlogComment.user_id' => $blog_userid), 'order' => 'BlogComment.updated DESC'));
					}

					$allbloglist = $this->Blog->find('all', ['conditions' => ['Blog.project_id' => $project_id], 'order' => 'Blog.id DESC']);

				}

			} else if (isset($blog_type) && $blog_type == 'show_admin_blog') {

				if ((isset($blog_id) && !empty($blog_id)) || (isset($refer_id) && !empty($refer_id))) {

					$blogdata = $this->Blog->find("all", array("conditions" => array("Blog.project_id" => $project_id), 'order' => 'Blog.id DESC'));
					if (!empty($blogdata)) {
						$blog_ids = Set::extract('/Blog/id', $blogdata);
						$blog_list = $this->BlogComment->find("all", array("conditions" => array("BlogComment.blog_id" => $blog_ids), 'order' => 'BlogComment.updated DESC'));
					}

					$blogids = $this->Blog->find("all", array("conditions" => array("Blog.project_id" => $project_id), 'order' => 'Blog.id DESC'));
					if (!empty($blogids)) {

						$blog_ids = Set::extract('/Blog/id', $blogids);
						$this->BlogComment->unBindModel(array('belongsTo' => array('Blog')));
						$doc_list = $this->BlogDocument->find("all", array("conditions" => array("BlogDocument.blog_id" => $blog_ids), 'order' => 'BlogDocument.updated DESC'));
					}

					$allbloglist = $this->Blog->find('all', ['conditions' => ['Blog.project_id' => $project_id, 'Blog.user_id' => $blog_userid], 'order' => 'Blog.id DESC']);

				}

			} else {

				$blogdata = $this->Blog->find("all", array("conditions" => array("Blog.project_id" => $project_id), 'order' => 'Blog.id DESC'));
				if (!empty($blogdata)) {
					$blog_ids = Set::extract('/Blog/id', $blogdata);
					$blog_list = $this->BlogComment->find("all", array("conditions" => array("BlogComment.blog_id" => $blog_ids), 'order' => 'BlogComment.updated DESC'));
				}

				//$doc_list = $this->BlogDocument->find("all",array("conditions"=>array("OR"=>array("BlogDocument.project_id"=>$project_id, "BlogDocument.blog_comment_id >"=>0)  ), 'order'=>'BlogDocument.id DESC'));

				$blogids = $this->Blog->find("all", array("conditions" => array("Blog.project_id" => $project_id), 'order' => 'Blog.id DESC'));
				if (!empty($blogids)) {

					$blog_ids = Set::extract('/Blog/id', $blogids);
					$this->BlogComment->unBindModel(array('belongsTo' => array('Blog')));
					$doc_list = $this->BlogDocument->find("all", array("conditions" => array("BlogDocument.blog_id" => $blog_ids), 'order' => 'BlogDocument.id DESC'));
				}

				$allbloglist = $this->Blog->find('all', ['conditions' => ['Blog.project_id' => $project_id], 'order' => 'Blog.id DESC']);

			}

			//pr($doc_list, 1);
			$this->set('allbloglist', $allbloglist);
			$this->set('com_list', $blog_list);
			$this->set('doc_list', $doc_list);
			$this->render('blog_admin/blog_admin');
			/* $response = ['success' => false,'content' => null,'blog_id'=>$blog_id];
				return json_encode($response);
			*/

		}
	}

	public function blog_dashboard() {

		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			$this->autoRender = false;
			$this->loadModel('Blog');

			//pr($this->request->data);

			if (!empty($this->request->data)) {
				$project_id = isset($this->request->data['project_id']) ? $this->request->data['project_id'] : null;
				$user_id = isset($this->request->data['user_id']) ? $this->request->data['user_id'] : null;

				if (isset($this->request->data['listby']) && $this->request->data['listby'] == 'blog_user_list') {

					$this->Blog->unBindModel(array('hasMany' => array('BlogDocument', 'BlogComment')));
					$data = $this->Blog->find('all', ['conditions' => ['Blog.project_id' => $project_id, 'Blog.user_id' => $user_id], 'order' => 'Blog.id DESC']);

					$this->set('bloglistby', $this->request->data['listby']);
					$this->set('bloglistuser', $user_id);

				} else if (isset($this->request->data['filterby']) && !empty($this->request->data['filterby'])) {

					$conditions = array();

					$created = $this->request->data['filterby'];

					if ($created == 'today') {
						$start = date('Y-m-d');
						$end = date('Y-m-d');
						$fconditions = array(
							'date(Blog.created) BETWEEN ? AND ?' => array($start, $end),
						);
					} else if ($created == 'last_7_day') {
						$end = date('Y-m-d');
						$start = date('Y-m-d', strtotime('-7 day'));
						$fconditions = array(
							'date(Blog.created) BETWEEN ? AND ?' => array($start, $end),
						);
					} else if ($created == 'last_30_day') {
						$end = date('Y-m-d');
						$start = date('Y-m-d', strtotime('-30 day'));
						$fconditions = array(
							'date(Blog.created) BETWEEN ? AND ?' => array($start, $end),
						);
					} else {
						$fconditions = array();
					}

					$data['Project']['id'] = $this->request->data['project_id'];

					$defaultcondtion = array('Blog.project_id' => $project_id);
					$fullConditions = array_merge($defaultcondtion, $fconditions);

					$this->Blog->unBindModel(array('hasMany' => array('BlogDocument', 'BlogComment')));
					$data = $this->Blog->find('all', ['conditions' => array($fullConditions), 'order' => 'Blog.id DESC']);

					//pr($data);

				} else {

					$data['Project']['id'] = $this->request->data['project_id'];

					$this->Blog->unBindModel(array('hasMany' => array('BlogDocument', 'BlogComment')));
					$data = $this->Blog->find('all', ['conditions' => ['Blog.project_id' => $project_id], 'order' => 'Blog.id DESC']);

				}

			}

			$this->set('bloglist', $data);
			$this->set('project_id', $project_id);
			$this->render('blog_dashboard/blog_dashboard');
		}

	}

	public function blog_list_user() {

		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			$this->autoRender = false;
			$this->loadModel('Blog');

			if (!empty($this->request->data)) {
				$project_id = isset($this->request->data['project_id']) ? $this->request->data['project_id'] : null;
				//$user_id = isset($this->request->data['user_id'])? $this->request->data['user_id'] : null;

				$this->Blog->unBindModel(array('hasMany' => array('BlogDocument', 'BlogComment')));
				$data = $this->Blog->find('all',
					array('conditions' => array('Blog.project_id' => $project_id),
						'group' => 'Blog.user_id', 'order' => 'Blog.id DESC',
					)
				);

			}

			$this->set('blogusers', $data);
			$this->render('blog_dashboard/blog_list_user');
		}

	}

	public function comments_like($project_id = null, $comment_id = null, $user_id = null) {

		if ($this->request->isAjax()) {
			$this->layout = 'ajax';

			$this->loadModel('CommentLike');

			$this->set('project_id', $project_id);

			$response = null;
			$response['success'] = false;
			$response['content'] = null;

			if (!empty($this->request->data)) {

				$this->request->data['CommentLike']['project_id'] = $this->request->data['project_id'];
				$this->request->data['CommentLike']['user_id'] = $this->request->data['user_id'];
				$this->request->data['CommentLike']['comment_id'] = $this->request->data['comment_id'];
				$this->request->data['CommentLike']['like_unlike'] = 1;

				if ($this->CommentLike->save($this->request->data['CommentLike'])) {
					$countLike = $this->CommentLike->find('count', array('conditions' => array('CommentLike.project_id' => $this->request->data['project_id'], 'CommentLike.comment_id' => $this->request->data['comment_id']),
					)
					);
					$response['success'] = true;
					$response['content'] = $countLike;
				}
			}

			echo json_encode($response);
			exit;
		}
	}

	public function blog_comment_delete($comment_id = null) {

		if ($this->request->isAjax()) {
			$this->layout = 'ajax';

			$this->loadModel('BlogComment');
			$this->loadModel('BlogDocument');

			$response = null;
			$response['success'] = false;
			$response['content'] = null;

			//pr($this->request->data, 1);

			if (!empty($this->request->data)) {

				$this->request->data['BlogComment']['id'] = $this->request->data['comment_id'];

				if ($this->BlogComment->delete($this->request->data['BlogComment'])) {

					$this->BlogDocument->deleteAll(array("BlogDocument.blog_comment_id" => $this->request->data['BlogComment']['id']));

					$response['success'] = true;
				}
			}

			echo json_encode($response);
			exit;
		}
	}

	public function blog_delete($comment_id = null) {

		if ($this->request->isAjax()) {
			$this->layout = 'ajax';

			$this->loadModel('Blog');

			$response = null;
			$response['success'] = false;
			$response['content'] = null;

			if (!empty($this->request->data)) {

				$this->request->data['Blog']['id'] = $this->request->data['blog_id'];
				$project_id = $this->request->data['project_id'];

				$blog_name = strip_tags(getFieldDetail('Blog', $this->request->data['Blog']['id'], 'title'));
				$projectname = strip_tags(getFieldDetail('Project', $project_id, 'title'));
				$blogDeletedUser = get_user_data($this->Session->read('Auth.User.id'));

				if ($this->Blog->delete($this->request->data['Blog'])) {

					//========================== Send Email to Project all Participants ==============
					$owner = $this->Common->ProjectOwner($project_id, $this->Session->read('Auth.User.id'));

					$participants = participants($project_id, $owner['UserProject']['user_id']);
					$participants_owners = participants_owners($project_id, $owner['UserProject']['user_id']);
					$participantsGpOwner = participants_group_owner($project_id);
					$participantsGpSharer = participants_group_sharer($project_id);

					$participants = isset($participants) ? array_filter($participants) : $participants = array();
					$participants_owners = isset($participants_owners) ? array_filter($participants_owners) : $participants_owners = array();
					$participantsGpOwner = isset($participantsGpOwner) ? array_filter($participantsGpOwner) : $participantsGpOwner = array();
					$participantsGpSharer = isset($participantsGpSharer) ? array_filter($participantsGpSharer) : $participantsGpSharer = array();

					$allUsers = array_merge($participants, $participants_owners, $participantsGpOwner, $participantsGpSharer);

					$lastblogid = $this->request->data['Blog']['id'];
					$user_id = $this->Session->read('Auth.User.id');

					$pageAction = SITEURL . 'team_talks/index/project:' . $project_id;
					if (isset($allUsers) && !empty($allUsers)) {
						if (($key = array_search($this->Session->read('Auth.User.id'), $allUsers)) !== false) {
							unset($allUsers[$key]);
						}
					}
					if (isset($allUsers) &&  !empty($allUsers) && count($allUsers) > 0) {
						foreach ($allUsers as $listUsers) {

							$notiDelStatus = $this->EmailNotification->find('first', ['conditions' => ['notification_type' => 'team_talk', 'personlization' => 'blog_deleted', 'user_id' => $listUsers]]);

							$userDetailData = getByDbId('UserDetail', $listUsers, ['full_name']);
							$usersDetails = getByDbId('User', $listUsers, ['email', 'email_notification']);

							if ((!isset($notiDelStatus['EmailNotification']['email']) || $notiDelStatus['EmailNotification']['email'] == 1) && (!isset($usersDetails['User']['email_notification']) || $usersDetails['User']['email_notification'] == 1)) {

								$email = new CakeEmail();
								$email->config('Smtp');
								$email->from(array(ADMIN_FROM_EMAIL => SITENAME));
								$email->to($usersDetails['User']['email']);
								$email->subject(SITENAME . ': Blog deleted');
								$email->template('blog_delete_email');
								$email->emailFormat('html');
								$email->viewVars(array('blog_name' => $blog_name, 'project_name' => $projectname, 'owner_name' => $userDetailData['UserDetail']['full_name'], 'deletedby' => $blogDeletedUser['UserDetail']['full_name'],'open_page'=>$pageAction));
								$email->send();

							}

						}
						/************** socket messages **************/
						if (SOCKET_MESSAGES) {
							$current_user_id = $this->Session->read('Auth.User.id');
							$blog_users = $allUsers;
							if (isset($blog_users) && !empty($blog_users)) {
								if (($key = array_search($current_user_id, $blog_users)) !== false) {
									unset($blog_users[$key]);
								}
							}
							$open_users = null;
							if (isset($blog_users) && !empty($blog_users)) {
								foreach ($blog_users as $key1 => $value1) {
									if (web_notify_setting($value1, 'team_talk', 'blog_deleted')) {
										$open_users[] = $value1;
									}
								}
							}
							$userDetail = get_user_data($current_user_id);
							$content = [
								'notification' => [
									'type' => 'blog_deleted',
									'created_id' => $current_user_id,
									'project_id' => $project_id,
									'creator_name' => $userDetail['UserDetail']['full_name'],
									'subject' => 'Blog deleted',
									'heading' => 'Blog: ' . $blog_name,
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
					//===================================================================

					$response['success'] = true;
				}
			}

			echo json_encode($response);
			exit;
		}
	}

	public function document_delete($document_id = null) {

		if ($this->request->isAjax()) {
			$this->layout = 'ajax';

			$this->loadModel('BlogDocument');

			$response = null;
			$response['success'] = false;
			$response['content'] = null;

			//pr($this->request->data, 1);

			if (!empty($this->request->data)) {

				$this->request->data['BlogDocument']['id'] = $this->request->data['document_id'];

				if ($this->BlogDocument->delete($this->request->data['BlogDocument'])) {

					$response['success'] = true;
				}
			}

			echo json_encode($response);
			exit;
		}
	}

	public function project_blog_list() {

		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			$this->autoRender = false;
			$this->loadModel('Blog');

			//pr($this->request->data['project_id'], 1);

			if (!empty($this->request->data)) {

				$project_id = $this->request->data['project_id'];
				$this->set('project_id', $this->request->data['project_id']);
				$data['Project']['id'] = $this->request->data['project_id'];

				$this->Blog->unBindModel(array('hasMany' => array('BlogDocument')));

				$data = $this->Blog->find('all', ['conditions' => ['Blog.project_id' => $project_id], 'order' => 'Blog.updated DESC']);
			}

			$this->set('bloglist', $data);
			$this->set('data', $data);
			$this->render('partials/blog_comment/get_blog_by_user');
		}
	}

	public function document_blog_list() {

		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			$this->autoRender = false;
			$this->loadModel('Blog');
			$this->loadModel('BlogDocument');

			//pr($this->request->data['project_id'], 1);

			if (!empty($this->request->data)) {

				$project_id = $this->request->data['project_id'];
				$this->set('project_id', $this->request->data['project_id']);
				$data['Project']['id'] = $this->request->data['project_id'];

				$this->Blog->unBindModel(array('hasMany' => array('BlogDocument')));

				/* $this->Blog->bindModel(
					array(
						'hasMany' => array(
							'BlogDocument' => array(
								'conditions' => array('BlogDocument.blog_comment_id' => 0, 'BlogDocument.blog_id >'=> 0, 'BlogDocument.project_id >'=> 0 )
							)
						)
					)
				); */

				$this->Blog->bindModel(
					array(
						'hasMany' => array(
							'BlogDocument' => array(
								'conditions' => array('BlogDocument.blog_id >' => 0),
							),
						),
					)
				);

				$data = $this->Blog->find('all', ['conditions' => ['Blog.project_id' => $project_id], 'order' => 'Blog.updated DESC']);

			}

			$this->set('bloglist', $data);
			$this->set('data', $data);
			$this->render('partials/document_blog_list');
		}
	}

	public function blog_list() {

		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			$this->autoRender = false;
			$this->loadModel('Blog');

			if (!empty($this->request->data)) {

				$project_id = $this->request->data['project_id'];
				$user_id = $this->request->data['user_id'];
				$data['Project']['id'] = $this->request->data['project_id'];
				$data = $this->Blog->find('all', ['conditions' => ['Blog.project_id' => $project_id], 'recursive' => -1, 'order' => 'Blog.id DESC']);
			}

			$this->set('bloglist', $data);
			$this->set('data', $data);
			$this->render('partials/blog_list');
		}
	}

	public function blog_admin_list() {

		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			$this->autoRender = false;
			$this->loadModel('Blog');

			//pr($this->request->data, 1);

			if (!empty($this->request->data)) {

				$project_id = $this->request->data['project_id'];
				$user_id = $this->Session->read("Auth.User.id");

				$this->Blog->bindModel(
					array(
						'hasMany' => array(
							'BlogDocument' => array(
								'conditions' => array('BlogDocument.blog_comment_id' => 0, 'BlogDocument.blog_id >' => 0, 'BlogDocument.project_id >' => 0),
							),
						),
					)
				);

				$data = $this->Blog->find('all', ['conditions' => ['Blog.project_id' => $project_id], 'order' => 'Blog.updated DESC']);

				//pr($data);
			}

			$this->set('bloglist', $data);
			$this->set('data', $data);
			$this->set('project_id', $project_id);
			$this->render('blog_admin/blog_list');
		}
	}

	public function blog_comments_list($project_id = null) {

		if ($this->request->isAjax()) {
			$this->layout = 'ajax';

			$this->loadModel('BlogComment');

			$blog_id = (isset($this->request->data['blog_id']) && !empty($this->request->data['blog_id'])) ? $this->request->data['blog_id'] : null;
			$refer_id = (isset($this->request->data['refer_id']) && !empty($this->request->data['refer_id'])) ? $this->request->data['refer_id'] : null;
			$project_id = (isset($this->request->data['project_id']) && !empty($this->request->data['project_id'])) ? $this->request->data['project_id'] : null;

			$this->set('project_id', $project_id);

			if ((isset($blog_id) && !empty($blog_id)) || (isset($refer_id) && !empty($refer_id))) {

				$data = $this->BlogComment->find("all", array("conditions" => array("BlogComment.blog_id" => $blog_id), 'order' => 'BlogComment.updated DESC'));

				$relevanceData = $this->BlogComment->find("all", array("conditions" => array("BlogComment.blog_id" => $blog_id), 'recursive' => -1, 'order' => ['BlogComment.rating' => 'DESC', 'BlogComment.id' => 'DESC']));

			} else {

				$data = $this->Blog->find("all", array("conditions" => array("Blog.project_id" => $project_id), 'recursive' => -1, 'order' => 'Blog.updated DESC'));
				if (!empty($data)) {
					$blog_ids = Set::extract('/Blog/id', $data);
					$data = $this->BlogComment->find("all", array("conditions" => array("BlogComment.blog_id" => $blog_ids), 'order' => 'BlogComment.updated DESC'));
				}

				$relevanceData = $this->Blog->find("all", array("conditions" => array("Blog.project_id" => $project_id), 'recursive' => -1, 'order' => 'Blog.id DESC'));
				if (!empty($relevanceData)) {
					$blog_ids = Set::extract('/Blog/id', $relevanceData);
					$relevanceData = $this->BlogComment->find("all", array("conditions" => array("BlogComment.blog_id" => $blog_ids), 'recursive' => -1, 'order' => ['BlogComment.rating' => 'DESC', 'BlogComment.id' => 'DESC']));
				}

			}

			$this->set('blog_list', $data);
			$this->set('relevanceData', $relevanceData);
			$this->render('partials/blog_comment/blog_comment');

		}

	}

	public function get_blog_comment_by_user($project_id = null) {

		if ($this->request->isAjax()) {
			$this->layout = 'ajax';

			$this->loadModel('BlogComment');

			$project_id = (isset($this->request->data['project_id']) && !empty($this->request->data['project_id'])) ? $this->request->data['project_id'] : null;

			$user_id = (isset($this->request->data['user_id']) && !empty($this->request->data['user_id'])) ? $this->request->data['user_id'] : null;

			$blog_id = (isset($this->request->data['blog_id']) && !empty($this->request->data['blog_id'])) ? $this->request->data['blog_id'] : null;

			//pr($this->request->data, 1);

			$this->set('project_id', $project_id);

			if ((isset($blog_id) && !empty($blog_id))) {

				$data = $this->BlogComment->find("all", array("conditions" => array("BlogComment.blog_id" => $blog_id, "BlogComment.user_id" => $user_id), 'order' => 'BlogComment.id DESC'));

			} else {

				$data = $this->Blog->find("all", array("conditions" => array("Blog.project_id" => $project_id), 'order' => 'Blog.updated DESC'));
				//pr($data, 1);
				if (!empty($data)) {

					$blog_ids = Set::extract('/Blog/id', $data);

					$this->BlogComment->unBindModel(array('belongsTo' => array('Blog')));

					$data = $this->BlogComment->find("all", array("conditions" => array("BlogComment.blog_id" => $blog_ids, "BlogComment.user_id" => $user_id), 'order' => 'BlogComment.updated DESC'));
				}
			}

			$this->set('blog_list', $data);
			$this->render('partials/blog_comment/get_blog_comment_by_user');

		}

	}

	public function document_list($project_id = null, $user_id = null) {

		if ($this->request->isAjax()) {
			$this->layout = 'ajax';

			//pr($this->request->data,1);

			$project_id = isset($this->request->data['project_id']) ? $this->request->data['project_id'] : null;
			$user_id = isset($this->request->data['user_id']) ? $this->request->data['user_id'] : null;

			$this->loadModel('BlogDocument');

			$blogids = $this->Blog->find("all", array("conditions" => array("Blog.project_id" => $project_id), 'recursive' => -1, 'order' => 'Blog.id DESC'));
			$data = array();
			if (!empty($blogids)) {

				$blog_ids = Set::extract('/Blog/id', $blogids);
				$this->BlogComment->unBindModel(array('belongsTo' => array('Blog')));
				$data = $this->BlogDocument->find("all", array("conditions" => array("BlogDocument.blog_id" => $blog_ids), 'order' => 'BlogDocument.updated DESC'));

				//$data = $this->BlogDocument->find("all", array("conditions" => array("BlogDocument.blog_id" => $blog_ids), 'fields'=>array('BlogDocument.id','BlogDocument.document_name','BlogDocument.user_id','BlogDocument.created','BlogDocument.blog_comment_id','BlogDocument.blog_id','Blog.id','Blog.title','Blog.project_id'), 'order' => 'BlogDocument.updated DESC'));
			}

			//pr($data);

			$this->set('doc_list', $data);
			$this->render('partials/document_list');

		}

	}

	public function project_document_count($project_id = null) {

		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			$this->autoRender = false;

			$data = 0;
			if (isset($this->request->data['project_id'])) {

				$project_id = $this->request->data['project_id'];
				$this->loadModel('BlogDocument');
				$data = $this->BlogDocument->find("count", array("conditions" => array("BlogDocument.project_id" => $project_id)));

			}
			return $data;
			exit;
		}

	}

	/* Project Wiki Functions */
	public function wiki($project_id = null) {

		$viewVars = $data = null;
		$this->layout = 'inner';
		$data['title_for_layout'] = __('Team Talk', true);
		$data['page_heading'] = __('Team Talk', true);
		$data['page_subheading'] = __('Discuss Project work with the team', true);

		$this->set($data);

		$crumb = ['last' => ['data' => ['title' => 'Team Talk', 'data-original-title' => 'Team Talk']]];

		$this->set('crumb', $crumb);

		$project_id = $workspace = $status = null;

		if (isset($this->params['named']) && !empty($this->params['named'])) {
			$params = $this->params['named'];
			$viewVars['project_id'] = isset($params['project']) && !empty($params['project']) ? $params['project'] : null;
		}
		$this->set($viewVars);
	}

	public function projects_wiki_list() {
		$html = '';
		if ($this->request->isAjax()) {

			$view = new View();
			$viewModel = $view->loadHelper('ViewModel');

			$this->loadModel('Project');

			$this->layout = 'ajax';
			$response = [
				'success' => false,
				'msg' => '',
				'content' => null,
			];

			$row = $data = null;
			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;
				$result = $params = null;

				$conditions = $pw_condition = [];
				$order = '';

				if (isset($this->request->data['project_id']) && !empty($this->request->data['project_id'])) {

					$data = $this->Project->find("first", array('conditions' => array('Project.id' => $this->request->data['project_id'])));
				}

				$this->set('data', $data);
				$view->layout = false; //if you want to disable layout
				$html = $this->render('partials/task_list_wiki');
			}
			return $html;
			exit();
		}
	}

	public function create_wiki($project_id = null) {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$response = null;

			$this->set('project_id', $project_id);

			$this->render('/TeamTalks/create_wiki');
		}
	}

	public function wiki_list_projects() {

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

				// $post['type']: [ 1 => 'my_projects', 2 => 'received_projects', 3 => 'group_received_projects'];

				if (isset($post['type']) && $post['type'] == 1) {
					$response['content'] = get_my_projects($this->user_id);
				} else if (isset($post['type']) && $post['type'] == 2) {
					$response['content'] = get_rec_projects($this->user_id, 1, 1);
				} else if (isset($post['type']) && $post['type'] == 3) {
					$response['content'] = group_rec_projects($this->user_id, 2);
				}
				// pr($response['content']);
				// pr($post, 1);
			}
			echo json_encode($response);
			exit();
		}
	}

	public function save_blog_like($project_id = null, $blog_id = null, $user_id = null) {

		if ($this->request->isAjax()) {
			$this->layout = 'ajax';

			$this->loadModel('BlogLike');

			$this->set('project_id', $project_id);

			//$this->Wiki->set($this->request->data);

			$response = null;
			$response['success'] = false;
			$response['content'] = null;

			if (!empty($this->request->data)) {

				$this->request->data['BlogLike']['project_id'] = $this->request->data['project_id'];
				$this->request->data['BlogLike']['user_id'] = $this->request->data['user_id'];
				$this->request->data['BlogLike']['blog_id'] = $this->request->data['blog_id'];
				$this->request->data['BlogLike']['like_unlike'] = 1;

				if ($this->BlogLike->save($this->request->data['BlogLike'])) {

					$this->Common->projectModified($this->request->data['BlogLike']["project_id"], $this->request->data['BlogLike']["user_id"]);

					$countLike = $this->BlogLike->find('count', array('conditions' => array('BlogLike.project_id' => $this->request->data['project_id'], 'BlogLike.blog_id' => $this->request->data['blog_id']),
					)
					);
					$response['success'] = true;
					$response['content'] = $countLike;
				}
			}

			echo json_encode($response);
			exit;
		}
	}

	public function list_userblogs($project_id = null, $user_id = null) {

		if ($this->request->isAjax()) {
			$this->layout = 'ajax';

			$response = null;
			$response['success'] = false;
			$response['content'] = null;
			$view = new View();
			$TeamTalk = $view->loadHelper('TeamTalk');
			$common = $view->loadHelper('Common');

			if (!empty($this->request->data)) {

				$project_id = $data['Project']['id'] = $this->request->data['project_id'];
				$user_id = $this->request->data['user_id'];

				$listall = $this->Blog->find('all', array('conditions' => array('Blog.project_id' => $project_id, 'Blog.user_id' => $user_id),
					'order' => 'Blog.id DESC',
				)
				);

				$this->set('project_id', $project_id);
				$this->set('user_id', $user_id);
				$this->set('bloglist', $listall);

				$response['success'] = true;
				$this->autorender = false;
				$this->render('/TeamTalks/partials/userblog_list', 'ajax');
				return;
			}
		}
	}

	public function blog_document_uploads() {

		if ($this->request->isAjax()) {
			$this->layout = 'ajax';

			$response = null;

			$data['blog_id'] = $this->request->params['named']['blog_id'];
			$data['project_id'] = $this->request->params['named']['project_id'];

			$response = [
				'success' => false,
				'content' => null,
			];

			$this->set('data', $data);
			$this->render('partials/blog_documents');
		}
	}

	public function public_blog_documents() {

		if ($this->request->isAjax()) {
			$this->layout = 'ajax';

			$response = null;
			$data['project_id'] = $this->request->params['named']['project_id'];
			$data['blog_id'] = $this->request->params['named']['blog_id'];

			$response = [
				'success' => false,
				'content' => null,
			];

			$this->set('data', $data);
			$this->render('partials/public_blog_documents');
		}
	}

	public function edit_blog_document() {

		if ($this->request->isAjax()) {
			$this->layout = 'ajax';

			$this->loadModel('BlogDocument');

			$document_id = $this->request->params['named']['id'];
			$project_id = $this->request->params['named']['project_id'];
			$this->request->data['BlogDocument']['project_id'] = $project_id;
			$this->request->data = $this->BlogDocument->find('first', array('conditions' => array('BlogDocument.id' => $document_id)));

			$this->render('partials/edit_blog_documents');
		}
	}

	public function blog_uploads($blog_id = null) {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$this->loadModel('BlogDocument');

			//$response = null;
			//$response['blog_id'] = $blog_id;
			$response = [
				'success' => false,
				'content' => null,
			];

			if (isset($this->data['BlogDocument']['document_name']) && !empty($this->data['BlogDocument']['document_name'])) {

				$getfiles = $this->data['BlogDocument']['document_name'];
				$savearray = array();
				if (isset($getfiles) && !empty($getfiles)) {
					$response['success'] = true;
					foreach ($getfiles as $k => $value) {
						$output_dir = DO_LIST_BLOG_DOCUMENTS;
						if (isset($value['name']) && !empty($value['name'])) {
							$ext = pathinfo($value['name']);
							$fileName = $this->unique_file_name($output_dir, $value['name']);
							if (move_uploaded_file($value["tmp_name"], $output_dir . $fileName)) {
								$response['content'][] = $fileName;
							} else if (copy($value["tmp_name"], $output_dir . $fileName)) {
								$response['content'][] = $fileName;
							}
						}
					}
				}
			}
			echo json_encode($response);
			exit;
		}

	}

	// For save blog document and title
	public function save_blog_uploads() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$response = [
				'success' => false,
				'content' => null,
			];
			$this->loadModel('BlogDocument');
			//pr($this->request->data['BlogDocument']['file_name']); exit;
			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;
				$uploads = null;

				if (isset($post['BlogDocument']['file_name']) && !empty($post['BlogDocument']['file_name'])) {
					foreach ($post['BlogDocument']['file_name'] as $key => $val) {
						$uploads[][$key] = $val;
					}
					unset($post['BlogDocument']);
					$post['BlogDocument'] = $uploads;
					$this->request->data['BlogDocument']['document_name'] = $uploads;
				}

				$this->BlogDocument->set($this->request->data);
				if (isset($this->request->data['BlogDocument']['project_id'])) {
					$response['project_id'] = $this->request->data['BlogDocument']['project_id'];
				}

				if (isset($this->request->data['BlogDocument']['blog_id'])) {
					$response['blog_id'] = $this->request->data['BlogDocument']['blog_id'];
				}

				//$this->DoListComment->validates();
				unset($this->request->data['BlogDocument']['file_name']);

				if ($this->request->data['BlogDocument']) {
					if (array_key_exists('document_name', $this->request->data['BlogDocument'])) {
						if (isset($this->request->data['BlogDocument']) && !empty($this->request->data['BlogDocument'])) {
							$i = 0;
							if (isset($this->request->data['BlogDocument']['document_name']) && !empty($this->request->data['BlogDocument']['document_name'])) {
								foreach ($this->request->data['BlogDocument']['document_name'] as $doc) {

									$this->request->data['BlogDocument']['id'] = '';
									$this->request->data['BlogDocument']['document_name'] = $doc[$i];
									$this->BlogDocument->save($this->request->data);
									$i++;

								}
								$response['success'] = true;
								echo json_encode($response);
								die;
							}
						}
					} else {
						$response['success'] = false;
						$response['content'] = $this->validateErrors($this->BlogDocument);
						echo json_encode($response);
						die;
					}
				}

				if ($this->BlogDocument->saveAll($this->request->data['BlogDocument'])) {

					//$this->Common->projectModified($this->request->data['BlogDocument']['project_id'], $user_id);

					$response['success'] = true;
					$response['content'] = $this->BlogDocument->getLastInsertId();
				} else {
					$response['success'] = false;
					$response['content'] = $this->validateErrors($this->BlogDocument);
				}
			} else {
				$response['success'] = false;
				$response['content'] = $this->validateErrors($this->BlogDocument);
			}
		}
		echo json_encode($response);
		exit;
	}

	// For save blog document and title
	public function save_public_uploads() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$this->render = false;
			$response = [
				'success' => false,
				'content' => null,
			];
			$this->loadModel('BlogDocument');

			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;
				$uploads = null;

				if (isset($post['BlogDocument']['file_name']) && !empty($post['BlogDocument']['file_name'])) {
					foreach ($post['BlogDocument']['file_name'] as $key => $val) {
						$uploads[][$key] = $val;
					}
					unset($post['BlogDocument']);
					$post['BlogDocument'] = $uploads;
					$this->request->data['BlogDocument']['document_name'] = $uploads;
				} else {
					$response['success'] = false;
					$response['content'] = "Document is required.";
					echo json_encode($response);
					exit;
				}

				$this->BlogDocument->set($this->request->data);
				if (isset($this->request->data['BlogDocument']['project_id'])) {
					$response['project_id'] = $this->request->data['BlogDocument']['project_id'];
				}

				//$this->DoListComment->validates();
				unset($this->request->data['BlogDocument']['file_name']);

				if ($this->request->data['BlogDocument']) {

					if (isset($this->request->data['BlogDocument']) && !empty($this->request->data['BlogDocument'])) {
						$i = 0;
						if (isset($this->request->data['BlogDocument']['document_name'])) {
							foreach ($this->request->data['BlogDocument']['document_name'] as $doc) {
								$this->request->data['BlogDocument']['id'] = '';
								$this->request->data['BlogDocument']['document_name'] = $doc[$i];
								$this->BlogDocument->save($this->request->data);
								$i++;
							}
						} else {
							$response['success'] = false;
						}

					}
				}
				$response['success'] = true;
				$response['content'] = $this->request->data['BlogDocument']['blog_id'];
				echo json_encode($response);
				die;

				if ($this->BlogDocument->saveAll($this->request->data['BlogDocument'])) {
					$response['success'] = true;
					$response['content'] = $this->BlogDocument->getLastInsertId();
				} else {
					$response['success'] = false;
					$response['content'] = $this->validateErrors($this->BlogDocument);
				}
			} else {
				$response['success'] = false;
				$response['content'] = $this->validateErrors($this->BlogDocument);
			}

		}
		echo json_encode($response);
		exit;
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

	public function comments_doc_uploads($blog_id = null) {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$response = null;
			$response['blog_id'] = $blog_id;
			$response = [
				'success' => false,
				'content' => null,
			];

			if (isset($this->data['BlogDocument']['document_name']) && !empty($this->data['BlogDocument']['document_name'])) {

				$getfiles = $this->data['BlogDocument']['document_name'];
				$savearray = array();
				if (isset($getfiles) && !empty($getfiles)) {
					$response['success'] = true;
					foreach ($getfiles as $k => $value) {
						$output_dir = DO_LIST_BLOG_DOCUMENTS;
						if (isset($value['name']) && !empty($value['name'])) {
							$ext = pathinfo($value['name']);
							$fileName = $this->unique_file_name($output_dir, $value['name']);
							if (move_uploaded_file($value["tmp_name"], $output_dir . $fileName)) {
								$response['content'][] = $fileName;
							} else if (copy($value["tmp_name"], $output_dir . $fileName)) {
								$response['content'][] = $fileName;
							}
						}
					}
				}
			}
		}
		echo json_encode($response);
		exit;
	}

	public function blogviewspeople($project_id = null, $blog_id = null) {

		$this->render = false;

		$project_id = $this->request->params['named']['project_id'];
		$blog_id = $this->request->params['named']['blog_id'];

		if (!empty($project_id) && !empty($blog_id)) {

			$data = $this->BlogView->find("all", array("fields" => "BlogView.user_id", "conditions" => array("BlogView.project_id" => $project_id, "BlogView.blog_id" => $blog_id)));

		}

		$this->set("viewUsers", $data);
		$this->render('blog_dashboard/blogviewspeople');

	}

	public function commentpeople($project_id = null, $blog_id = null) {

		$this->render = false;

		$project_id = $this->request->params['named']['project_id'];
		$blog_id = $this->request->params['named']['blog_id'];

		if (!empty($blog_id)) {

			$data = $this->BlogComment->find("all", array("conditions" => array("BlogComment.blog_id" => $blog_id), 'group' => "BlogComment.user_id", 'order' => 'BlogComment.id DESC'));

		}
		$this->set("viewUsers", $data);
		$this->render('blog_dashboard/commentpeople');
	}

	public function blogdocumentpeople($blog_id = null) {

		$this->render = false;

		$blog_id = $this->request->params['named']['blog_id'];

		$data = $this->BlogDocument->find("all", array("conditions" => array(

			"BlogDocument.blog_id" => $blog_id,
		), "group" => "BlogDocument.user_id",
		)
		);

		$this->set("viewUsers", $data);
		$this->render('blog_dashboard/blogdocumentpeople');

	}

	public function set_rating($rate = null, $id = null) {

		$this->layout = 'ajax';
		//echo $rate."==".;exit;
		$this->render = false;
		if (isset($this->request->data['id'])) {

			$this->request->data['BlogComment']['id'] = $this->request->data['id'];
			$this->request->data['BlogComment']['rating'] = $this->request->data['value'];
			$this->request->data['BlogComment']['rating_by'] = $this->Session->read('Auth.User.id');

			$this->BlogComment->save($this->request->data);
		}
		exit;

	}

}
