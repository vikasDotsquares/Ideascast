<?php
App::uses('Controller/Component', 'Auth', 'Session', 'RequestHandler');
App::uses('Sanitize', 'Utility');
App::uses('CakeEmail', 'Network/Email');
//App::import('Vendor', 'Classes/MPDF56/mpdf');
App::import('Vendor', 'MPDF56/PhpWord');

// App::import('Lib', 'Communications');

class MissionsController extends AppController {

	public $name = 'Missions';

	public $uses = ['User', 'UserDetail', 'ProjectPermission', 'ElementPermission', 'UserSetting', 'Category', 'Aligned', 'UserProject', 'Project', 'Workspace', 'Area', 'ProjectWorkspace', 'Element', 'ElementLink', 'ProjectGroup', 'ProjectGroupUser', 'WorkspaceComment', 'WorkspaceCommentLike', 'MissionUser', 'MissionSetting', 'TemplateDetail', 'Feedback', 'Vote', 'Activity'];

	public $objView = null;

	public $user_id = null;

	public $components = array('Common', 'Search');

	public $helpers = array('Html', 'Form', 'Session', 'Time', 'Text', 'Common', 'ViewModel', 'Search');

	public function beforeFilter() {

		parent::beforeFilter();

		$this->user_id = $this->Auth->user('id');

		$view = new View();
		$this->objView = $view;


		if( $_SERVER['HTTP_HOST'] != LOCALIP )  {

			if( $this->Session->read('Auth.User.role_id') == 3 ){
				$this->redirect(['controller' => 'organisations','action' => 'dashboard']);
			}
			if( $this->Session->read('Auth.User.role_id') == 1 ){
				$this->redirect(['controller' => 'dashboard','action' => 'index']);
			}
		}

	}

	public function index() {

		$this->layout = 'inner';
		$this->set('title_for_layout', __('Mission Room', true));
		$this->set('page_heading', __('Mission Room', true));
		$this->set('page_subheading', __('Work together to drive Project results', true));

		$project_id = $workspace_id = null;

		// Get project id from URL parameters
		if (isset($this->params['named']) && !empty($this->params['named'])) {
			$project_id = (isset($this->params['named']['project']) && !empty($this->params['named']['project'])) ? $this->params['named']['project'] : null;
			$workspace_id = (isset($this->params['named']['workspace']) && !empty($this->params['named']['workspace'])) ? $this->params['named']['workspace'] : null;
		}

		// Get Project detail based on project id
		$project_detail = null;
		if (isset($project_id) && !empty($project_id)) {

			$viewVars['project_id'] = $project_id;
			$project_detail = $this->Project->find('first', ['conditions' => ['Project.id' => $project_id]]);

			if (isset($project_detail) && !empty($project_detail)) {

				$viewVars['projects'] = $project_detail;

				$this->set('project_detail',$project_detail);

				$title = htmlentities($project_detail['Project']['title']);
				$this->set('page_heading', $title);

				// create category breadcrumb
				// $extra_crumb = get_category_list($project_id);

				// add project to breadcrumb
				$extra_crumb[$title] = [
					'data' => [
						'url' => '/projects/index/' . $project_id,
						'class' => 'tipText',
						'title' => $title,
						'data-original-title' => ucwords($title),
					],
				];
			}
		}
		// last in breadcrumb
		$crumb = [
			'last' => [
				'data' => [
					'title' => 'Mission Room',
					'data-original-title' => 'Mission Room',
				],
			],
		];
		//$viewVars['crumb'] = $crumb;
		if (isset($extra_crumb) && !empty($extra_crumb)) {
			$viewVars['crumb'] = array_merge($extra_crumb, $crumb);
		}

		// Get all users

		$project_users = $this->MissionUser->find('all', ['conditions' => ['MissionUser.project_id' => $project_id], 'recursive' => -1, 'fields' => ['MissionUser.user_id']]);
		if (isset($project_users) && !empty($project_users)) {
			$project_users = Set::extract($project_users, '/MissionUser/user_id');
		}

		$this->User->Behaviors->load('Containable');
		$users = $this->User->find('all', ['conditions' => ['User.status' => 1, 'User.role_id !=' => 1, 'User.id' => $project_users], 'contain' => 'UserDetail']);
		if (isset($users) && !empty($users)) {
			foreach ($users as $key => $val) {
				$viewVars['users_list'][$val['User']['id']] = $val['UserDetail']['first_name'] . ' ' . $val['UserDetail']['last_name'];
			}
		}

		$user_settings = $this->mission_settings();

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
			$group_permission = $this->objView->loadHelper('Group')->group_permission_details($project_id, $projects_group_shared_user['ProjectGroupUser']['project_group_id']);

			$pll_level = $group_permission['ProjectPermission']['project_level'];

			$this->set('gpid', $projects_group_shared_user['ProjectGroupUser']['project_group_id']);

			if (isset($pll_level) && $pll_level == 1) {
				$this->set('project_level', 1);
			}
		}

		$p_permission = $this->objView->loadHelper('Common')->project_permission_details($project_id, $this->Session->read('Auth.User.id'));
		$user_project = $this->objView->loadHelper('Common')->userproject($project_id, $this->Session->read('Auth.User.id'));
		$gp_exists = $this->objView->loadHelper('Group')->GroupIDbyUserID($project_id, $this->Session->read('Auth.User.id'));

		if (isset($gp_exists) && !empty($gp_exists)) {
			$p_permission = $this->objView->loadHelper('Group')->group_permission_details($project_id, $gp_exists);
		}

		if ((isset($user_project) && !empty($user_project)) || (isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level'] == 1)) {

		} else {
			$this->Session->setFlash(__("You don't have owner permissions to access Mission Room."), 'error');
			$this->redirect($this->referer());
		}


		$this->set($viewVars);
		$this->setJsVar('project_id', $project_id);
		$this->setJsVar('workspace_id', $workspace_id);

	}

	public function workspace_template() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$html = '';
			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;

				// Get workspace template with workspace id
				$viewModal = $this->objView->loadHelper('ViewModel');
				$templateData['templateRows'] = $viewModal->getAreaTemplate($post['workspace_id']);

				/******************************************************/
				$project_id = workspace_pid($post['workspace_id']);
				$workspace_id = $post['workspace_id'];
				$workspace_data = getByDbId('Workspace', $workspace_id);
				$templateData['workspace'] = $workspace_data;
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
				// pr($templateData, 1);

				/*$view = new View($this, false);
					$view->viewPath = 'Projects/partials';
					$view->layout = false;
					$view->set('data', $templateData);
					$view->set('project_id', $project_id);
					$view->set('workspace_id', $post['workspace_id']);
				*/

				$view = new View($this, false);
				$view->viewPath = 'Missions/partials';
				$view->layout = false;
				$view->set('data', $templateData);
				$view->set('project_id', $project_id);
				$view->set('workspace_id', $post['workspace_id']);
				$html = $view->render('workspace_template');

			}

			echo json_encode($html);
			exit();
		}

	}

	public function workspace_zones() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$response = [
				'success' => true,
				'msg' => null,
				'content' => null,
			];

			if ($this->request->is('post') || $this->request->is('put')) {

				$zones = null;
				$post = $this->request->data;

				if (isset($post['workspace_id']) && !empty($post['workspace_id'])) {
					$workspace_id = $post['workspace_id'];

					$zones = get_workspace_areas($workspace_id);
					$areas = null;
					foreach ($zones as $key => $title) {
						$areas[] = ['key' => $key, 'title' => $title];
					}

					if (isset($zones) && !empty($zones)) {
						$response['content'] = $areas;
						$response['success'] = true;
					}
				}
			}
			echo json_encode($response);
			exit;
		}

	}


	public function get_area_elements($element_id = null) {
		if ($this->request->isAjax()) {

			$response = [
				'success' => false,
				'msg' => '',
				'content' => null,
			];

			$viewData = null;

			$this->layout = 'ajax';
			// $this->autoRender = false;

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				$viewData['project_id'] = $post['project_id'];
				$viewData['workspace_id'] = $post['workspace_id'];
				$viewData['area_id'] = $post['area_id'];
				$viewData['area_detail'] = getByDbId('Area', $post['area_id']);
				// pr($viewData, 1);
			}
			$this->set($viewData);
			$this->render('/Missions/partials/get_area_elements');

			// echo json_encode($html);
			// exit();

		}
	}

	public function get_area_elements_tasks($element_id = null) {
		if ($this->request->isAjax()) {

			$response = [
				'success' => false,
				'msg' => '',
				'content' => null,
			];

			$viewData = null;

			$this->layout = 'ajax';
			// $this->autoRender = false;

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				$viewData['project_id'] = $post['project_id'];
				$viewData['workspace_id'] = $post['workspace_id'];
				$viewData['area_id'] = $post['area_id'];
				$viewData['area_detail'] = getByDbId('Area', $post['area_id']);
				$area_element = $this->objView->loadHelper('ViewModel')->getAreaTask($post['workspace_id'], $post['area_id']);

				$viewData['all_elements'] = $area_element;

			}

			//$user_project, $p_permission, $fstatus ];

			$this->set('all_elements',$viewData['all_elements']);
			$this->set('project_id',$viewData['project_id']);
			$this->set('workspace_id',$viewData['workspace_id']);
			$this->set('area_id',$viewData['area_id']);
			$this->set('area_detail',$viewData['area_detail']);

			$this->set('p_permission','');
			$this->set('user_project','');
			$this->set('fstatus','');

			$this->render('/Missions/partials/area_task_elements');

			// echo json_encode($html);
			// exit();

		}
	}



	public function area_elements() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$response = [
				'success' => true,
				'msg' => null,
				'content' => null,
			];

			if ($this->request->is('post') || $this->request->is('put')) {

				$elements = null;
				$post = $this->request->data;
				//pr($post, 1);

				if (isset($post['zones']) && !empty($post['zones'])) {
					$area_id = $post['zones'];

					$elements = $el_users = $this->objView->loadHelper('ViewModel')->area_elements($area_id);
					$elements = Set::combine($elements, '{n}.Element.id', '{n}.Element.title');

					if (isset($elements) && !empty($elements)) {
						$response['content'] = $elements;
						$response['success'] = true;
					}
				}
			}
			echo json_encode($response);
			exit;
		}

	}

	public function area_people() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$response = [
				'success' => true,
				'msg' => null,
				'content' => null,
			];

			if ($this->request->is('post') || $this->request->is('put')) {

				$elements = null;
				$post = $this->request->data;

				if (isset($post['zones']) && !empty($post['zones'])) {
					$area_id = $post['zones'];

					$project_id = getParentId($this->Area, $area_id);

					$elements = $this->objView->loadHelper('ViewModel')->area_elements($area_id);

					$user_all = null;
					if (isset($elements) && !empty($elements)) {
						$elements = Set::extract($elements, '/Element/id');
						// pr($elements, 1);
						$users = element_users($elements, $project_id);
						if (isset($users) && !empty($users)) {
							foreach ($users as $key => $val) {
								$this->User->Behaviors->load('Containable');
								$userDetail = $this->User->find('first', ['conditions' => ['User.id' => $val], 'contain' => 'UserDetail']);
								$user_all[$userDetail['User']['id']] = $userDetail['UserDetail']['first_name'] . ' ' . $userDetail['UserDetail']['last_name'];
							}
						}

						$response['content'] = $user_all;
						$response['success'] = true;
					}
				}
			}
			echo json_encode($response);
			exit;
		}

	}

	public function workspace_elements() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$response = [
				'success' => true,
				'msg' => null,
				'content' => null,
			];

			if ($this->request->is('post') || $this->request->is('put')) {

				$elements = null;
				$post = $this->request->data;

				if (isset($post['workspace_id']) && !empty($post['workspace_id'])) {
					$workspace_id = $post['workspace_id'];

					$elements = workspace_elements($workspace_id);
					$elements = Set::combine($elements, '{n}.Element.id', '{n}.Element.title');

					if (isset($elements) && !empty($elements)) {
						$response['content'] = $elements;
						$response['success'] = true;
					}
				}
			}
			echo json_encode($response);
			exit;
		}

	}

	public function workspace_people() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$response = [
				'success' => true,
				'msg' => null,
				'content' => null,
			];

			if ($this->request->is('post') || $this->request->is('put')) {

				$elements = null;
				$post = $this->request->data;

				if (isset($post['workspace_id']) && !empty($post['workspace_id'])) {
					$workspace_id = $post['workspace_id'];

					$elements = workspace_elements($workspace_id);
					$elements = Set::extract($elements, '/Element/id');
					$project_id = workspace_pid($workspace_id);
					$el_users = $this->objView->loadHelper('Common')->element_sharers($elements, $project_id, 1);

					$users = wsp_users(workspace_pwid(workspace_pid($workspace_id), $workspace_id), workspace_pid($workspace_id));

					if (isset($el_users) && !empty($el_users)) {
						$users = array_merge($users, $el_users);
					}

					$users = array_unique($users);

					$user_all = null;
					if (isset($users) && !empty($users)) {
						foreach ($users as $key => $val) {
							$this->User->Behaviors->load('Containable');
							$userDetail = $this->User->find('first', ['conditions' => ['User.id' => $val], 'contain' => 'UserDetail']);
							if (isset($userDetail) && !empty($userDetail)) {
								$user_all[$userDetail['User']['id']] = $userDetail['UserDetail']['first_name'] . ' ' . $userDetail['UserDetail']['last_name'];
							}
						}
					}

					if (isset($user_all) && !empty($user_all)) {
						$response['content'] = $user_all;
						$response['success'] = true;
					}
				}
			}
			echo json_encode($response);
			exit;
		}

	}

	public function get_workspace_comments($workspace_id = null) {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$data = null;

			if (isset($workspace_id) && !empty($workspace_id)) {

				$data = $this->WorkspaceComment->find('all', ['conditions' => ['WorkspaceComment.workspace_id' => $workspace_id], 'order' => 'WorkspaceComment.modified DESC']);

			}

			$view = new View($this, false);
			$view->viewPath = 'Missions/partials'; // Directory inside view directory to search for .ctp files
			$view->layout = false; // if you want to disable layout
			$view->set('data', $data);
			// pr($data, 1);
			$html = $view->render('get_workspace_comments');

			echo json_encode($html);
			exit;
		}
	}

	public function people_comments() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$data = null;
			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;

				$condition = null;
				$condition['WorkspaceComment.workspace_id'] = $post['workspace_id'];

				if (isset($post['users']) && !empty($post['users'])) {
					$condition['WorkspaceComment.user_id'] = $post['users'];
				}
				$data = $this->WorkspaceComment->find('all', ['conditions' => $condition, 'order' => ['WorkspaceComment.modified DESC']]);

			}

			$view = new View($this, false);
			$view->viewPath = 'Missions/partials'; // Directory inside view directory to search for .ctp files
			$view->layout = false; // if you want to disable layout
			$view->set('data', $data);
			// pr($data, 1);
			$html = $view->render('get_workspace_comments');

			echo json_encode($html);
			exit;
		}
	}

	public function workspace_comment_people($workspace_id = null) {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$response = [
				'success' => false,
				'content' => null,
			];

			if (isset($workspace_id) && !empty($workspace_id)) {
				$users = $user_all = null;
				$ws_comments = $this->WorkspaceComment->find('all', ['conditions' => ['WorkspaceComment.workspace_id' => $workspace_id], 'recursive' => -1, 'fields' => ['WorkspaceComment.user_id'], 'group' => ['WorkspaceComment.user_id']]);
				if (isset($ws_comments) && !empty($ws_comments)) {
					$users = Set::extract($ws_comments, '/WorkspaceComment/user_id');
				}

				if (isset($users) && !empty($users)) {
					foreach ($users as $key => $val) {
						$this->User->Behaviors->load('Containable');
						$userDetail = $this->User->find('first', ['conditions' => ['User.id' => $val], 'contain' => 'UserDetail']);
						$user_all[$userDetail['User']['id']] = $userDetail['UserDetail']['first_name'] . ' ' . $userDetail['UserDetail']['last_name'];
					}
					$response['content'] = $user_all;
				}
				$response['success'] = true;

			}

			echo json_encode($response);
			exit;

		}
	}

	public function workspace_comments($workspace_id = null, $comment_id = null) {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$response = null;
			$response['workspace_id'] = $workspace_id;

			if (isset($comment_id) && !empty($comment_id)) {

				$response['comment_id'] = $comment_id;

				$this->request->data = $this->WorkspaceComment->read(null, $comment_id);

			}

			$this->set('response', $response);

			$this->render('/Missions/partials/workspace_comments');
		}
	}

	public function manage_comment() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$response = [
				'success' => false,
				'msg' => null,
				'content' => null,
			];

			if ($this->request->is('post') || $this->request->is('put')) {

				$elements = null;
				$post = $this->request->data;

				$post['WorkspaceComment']['comments'] = trim(strip_tags($post['WorkspaceComment']['comments']));

				if (isset($post['WorkspaceComment']['workspace_id']) && !empty($post['WorkspaceComment']['workspace_id']) && !empty($post['WorkspaceComment']['comments']) ) {

					if ($this->WorkspaceComment->save($post)) {
						$response['content'] = $this->WorkspaceComment->getLastInsertId();
						$response['success'] = true;
					}
				}
			}
			echo json_encode($response);
			exit;
		}

	}

	public function like_comment($comment_id = null) {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$data = $response = null;
			$response = ['content' => '', 'success' => false];

			if (isset($comment_id) && !empty($comment_id)) {
				// check that the current user not posted like for this comment previously
				$data = $this->WorkspaceCommentLike->find('count', [
					'conditions' => [
						'WorkspaceCommentLike.user_id' => $this->user_id,
						'WorkspaceCommentLike.workspace_comment_id' => $comment_id,
					],
					'recursive' => -1,
				]);
				$response['success'] = true;
				// if the current user not posted like for this comment previously, only then enter data in database
				if (isset($data) && empty($data)) {
					$in_data['WorkspaceCommentLike']['user_id'] = $this->user_id;
					$in_data['WorkspaceCommentLike']['workspace_comment_id'] = $comment_id;
					// pr($this->WorkspaceCommentLike->save($in_data), 1);
					if ($this->WorkspaceCommentLike->save($in_data)) {
						$response['content'] = $this->WorkspaceCommentLike->getLastInsertId();
					}
				}
			}

			echo json_encode($response);
			exit();
		}
	}

	public function delete_comment($comment_id = null) {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$data = $response = null;
			$response = ['content' => '', 'success' => false];

			if (isset($comment_id) && !empty($comment_id)) {

				if ($this->WorkspaceComment->delete($comment_id, true)) {
					$response['success'] = true;
				}

			}

			echo json_encode($response);
			exit();
		}
	}

	public function mission_users($project_id = null) {

		$this->layout = 'inner';
		$this->set('title_for_layout', __('Mission Room Sharing', true));
		$this->set('page_heading', __('Mission Room Sharing', true));
		$this->set('page_subheading', __('Mission Room Sharing', true));

		if ($this->request->is('post') || $this->request->is('put')) {

			$post = $this->request->data;

			if (isset($post['MissionUser']['user_id']) && !empty($post['MissionUser']['user_id'])) {

				$previously = $this->MissionUser->find('all', ['conditions' => ['MissionUser.project_id' => $project_id], 'recursive' => -1, 'fields' => ['MissionUser.id']]);
				if (isset($previously) && !empty($previously)) {
					$ids = Set::extract($previously, '/MissionUser/id');

					$this->MissionUser->delete($ids);
				}

				$all_data = null;
				foreach ($post['MissionUser']['user_id'] as $key => $val) {

					$data[]['MissionUser'] = ['project_id' => $project_id, 'user_id' => $val];
				}
				$all_data = $data;

				if ($this->MissionUser->saveAll($all_data)) {
					$this->redirect('index/project:' . $project_id);
				}
			}
		}

		$all_users = [];
		$owners = $this->ProjectPermission->find('all', ['conditions' => ['ProjectPermission.user_project_id' => project_upid($project_id), 'ProjectPermission.project_level' => 1, 'ProjectPermission.user_id IS NOT NULL']]);
		if (isset($owners) && !empty($owners)) {
			$owners = Set::extract($owners, '/ProjectPermission/user_id');
			$all_users = array_merge($all_users, $owners);
		}

		$groups = $this->ProjectPermission->find('all', ['conditions' => ['ProjectPermission.user_project_id' => project_upid($project_id), 'ProjectPermission.project_level' => 1, 'ProjectPermission.project_group_id IS NOT NULL']]);
		if (isset($groups) && !empty($groups)) {
			$groups = Set::extract($groups, '/ProjectPermission/project_group_id');
			$groupHelper = $this->objView->loadHelper('Group');
			$group_users = $groupHelper->group_users($groups, true);
			if (isset($group_users) && !empty($group_users)) {
				$all_users = array_merge($all_users, $group_users);
			}
		}

		$project_users = $this->MissionUser->find('all', ['conditions' => ['MissionUser.project_id' => $project_id], 'recursive' => -1, 'fields' => ['MissionUser.user_id']]);
		if (isset($project_users) && !empty($project_users)) {
			$project_users = Set::extract($project_users, '/MissionUser/user_id');
		}
		$this->set('project_users', $project_users);

		$this->set('project_id', $project_id);
		$this->set('all_users', $all_users);
	}

	public function filter_data($workspace_id = null) {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$data = $response = null;
			$response = ['content' => '', 'success' => false];

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;

				if (isset($workspace_id) && !empty($workspace_id)) {

					$data['workspace_id'] = $workspace_id;

					if (isset($post['zones']) && !empty($post['zones'])) {
						$data['area_id'] = $post['zones'];
					}

					if (isset($post['tasks']) && !empty($post['tasks'])) {
						$data['element_id'] = $post['tasks'];
					}

					if (isset($post['people']) && !empty($post['people'])) {
						$data['people'] = $post['people'];
					}

				}
			}

			$view = new View($this, false);
			$view->viewPath = 'Missions/partials'; // Directory inside view directory to search for .ctp files
			$view->layout = false; // if you want to disable layout
			$view->set('data', $data);
			// pr($data, 1);
			$html = $view->render('filter_data');

			echo json_encode($html);
			exit;
		}
	}

	public function workspace_bucket($workspace_id = null) {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$data = $response = null;
			$data['workspace_id'] = $workspace_id;
			$response = ['content' => '', 'success' => false];

			$view = new View($this, false);
			$view->viewPath = 'Missions/partials'; // Directory inside view directory to search for .ctp files
			$view->layout = false; // if you want to disable layout
			$view->set('data', $data);
			// pr($data, 1);
			$html = $view->render('filter_data');

			echo json_encode($html);
			exit();
		}
	}

	public function bucket_sorting() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$response = [
				'success' => true,
				'msg' => '',
				'content' => null,
			];

			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;
				$user_settings = $this->mission_settings();

				if (isset($post) && !empty($post)) {

					foreach ($post['sort_orders'] as $key => $val) {
						$this->MissionSetting->updateAll(
							array('MissionSetting.sort_order' => $val['order']), //fields to update
							array('MissionSetting.slug' => $val['slug']) //condition
						);
					}
					$response['success'] = true;
				}
			}
			echo json_encode($response);
			exit;
		}
	}

	public function theme_setting() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$response = [
				'success' => true,
				'msg' => '',
				'content' => null,
			];

			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;
				$user_settings = $this->MissionSetting->find('first', [
					'conditions' => ['MissionSetting.user_id' => $this->user_id, 'MissionSetting.slug' => 'bg_theme'],
					'recursive' => -1,
				]);

				if (!isset($user_settings) || empty($user_settings)) {
					$newTheme = [
						'MissionSetting' => [
							'user_id' => $this->user_id,
							'slug' => 'bg_theme',
							'title' => 'Background Color',
							'sort_order' => 0,
							'bg_theme' => $post['bg_theme'],
							'bg_color' => $post['bg_color'],
						],
					];
					if ($this->MissionSetting->save($newTheme)) {
						$response['success'] = true;
					}
				} else {
					$this->MissionSetting->id = $user_settings['MissionSetting']['id'];
					$setting = [
						'MissionSetting' => [
							'bg_theme' => $post['bg_theme'],
							'bg_color' => $post['bg_color'],
						],
					];
					if ($this->MissionSetting->save($setting)) {
						$response['success'] = true;
					}
					// $this->MissionSetting->updateAll(
					// array( 'MissionSetting.bg_theme' => "'".$post['bg_theme']."'" ),   //fields to update
					// array( 'MissionSetting.slug' => 'bg_theme', 'MissionSetting.user_id' => $this->user_id )  //condition
					// );
				}
			}
			echo json_encode($response);
			exit;
		}
	}

	public function area_element_manage($area_id = null) {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$response = [
				'success' => true,
				'msg' => '',
				'content' => null,
			];

			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;

				if (isset($post['elements']) && !empty($post['elements'])) {
					foreach ($post['elements'] as $key => $val) {
						$this->Element->id = null;
						$this->Element->id = $val;
						if ($this->Element->saveField('sort_order', ($key + 1))) {

						}
					}
				}

				// Get workspace template with workspace id
				$groupHelper = $this->objView->loadHelper('Group');
				$commonHelper = $this->objView->loadHelper('Common');

				$area_detail = getByDbId('Area', $area_id);
				$workspace_id = $area_detail['Area']['workspace_id'];
				$project_id = workspace_pid($workspace_id);

				$view = new View($this, false);
				$view->viewPath = 'Missions/partials'; // Directory inside view directory to search for .ctp files
				$view->layout = false; // if you want to disable layout
				$view->set('area_detail', $area_detail);

				$view->set('area_id', $area_id);

				$html = $view->render('area_elements');

			}
			echo json_encode($html);
			exit;
		}
	}

	public function mission_settings() {

		// USER SETTING FOR BUCKET SORT ORDERS
		// IT WILL DISPLAY RESOURCE BUCKETS ACCORDING TO THE SORT ORDER THAT USER SET THEM
		$user_conditions = ['MissionSetting.user_id' => $this->user_id];
		// if logged user has no settings for project center than insert new data
		if (!$this->MissionSetting->hasAny($user_conditions)) {
			// Get default settings data
			$newSettings = $this->MissionSetting->bucketSettings($this->user_id);
			// Insert into database
			$this->MissionSetting->saveAll($newSettings);
		}

		$user_settings = $this->MissionSetting->find('all', [
			'conditions' => $user_conditions,
			'recursive' => -1,
		]);

		if (isset($user_settings) && !empty($user_settings)) {
			$user_settings = Set::combine($user_settings, '{n}.MissionSetting.slug', '{n}.MissionSetting');
		}

		return $user_settings;

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
				$this->request->data['Element']['title'] = strip_tags($this->request->data['Element']['title']);
				$this->request->data['Element']['description'] = nl2br($this->request->data['Element']['description']);

				$this->request->data['Element']['start_date'] = (isset($this->request->data['Element']['start_date']) && !empty($this->request->data['Element']['start_date'])) ? date('d-m-Y', strtotime($this->request->data['Element']['start_date'])) : "";
				$this->request->data['Element']['end_date'] = (isset($this->request->data['Element']['end_date']) && !empty($this->request->data['Element']['end_date'])) ? date('d-m-Y', strtotime($this->request->data['Element']['end_date'])) : "";

			}

			$this->set('response', $response);

			$this->render('/Missions/partials/create_element');
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

				// pr($this->request->data, 1);
				$this->Element->set($this->request->data);

				if ($this->Element->validates()) {
					$this->request->data['Element']['updated_user_id'] = $this->user_id;
					$this->request->data['Element']['created_by'] = $this->user_id;
					$this->Element->create();

					if (isset($this->request->data['Element']['id']) && !empty($this->request->data['Element']['id'])) {
						$this->Element->id = $this->request->data['Element']['id'];
					}

					$max_order = task_max_sort_order($area_id);
					$this->request->data['Element']['sort_order'] = $max_order;

					if ($this->Element->save($this->request->data)) {
						$insertId = 0;
						$response['success'] = true;
						if (isset($this->request->data['Element']['id']) && !empty($this->request->data['Element']['id'])) {
							$insertId = $this->request->data['Element']['id'];
						} else {
							$insertId = $this->Element->getLastInsertId();

							$wsp_id = area_workspace($this->request->data['Element']['area_id']);
							$project_id = workspace_pid($wsp_id);

							$this->loadModel('ElementPermission');

							$arr['ElementPermission']['user_id'] = $this->user_id;
							$arr['ElementPermission']['element_id'] = $insertId;
							$arr['ElementPermission']['project_id'] = $project_id;
							$arr['ElementPermission']['workspace_id'] = $wsp_id;
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
				// pr($this->validateErrors($this->Element), 1);
				echo json_encode($response);
				exit;
			}
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

				$this->request->data = $this->Workspace->read(null, $workspace_id);
				$this->request->data['Workspace']['title'] = strip_tags($this->request->data['Workspace']['title']);

				$this->request->data['Workspace']['start_date'] = (isset($this->request->data['Workspace']['start_date']) && !empty($this->request->data['Workspace']['start_date'])) ? date('d-m-Y', strtotime($this->request->data['Workspace']['start_date'])) : "";
				$this->request->data['Workspace']['end_date'] = (isset($this->request->data['Workspace']['end_date']) && !empty($this->request->data['Workspace']['end_date'])) ? date('d-m-Y', strtotime($this->request->data['Workspace']['end_date'])) : "";

				$this->setJsVar('wdata', $this->request->data['Workspace']);

				// pr($this->request->data , 1);
			}

			$this->set('response', $response);
			$this->set('project_id', $project_id);

			$this->render('/Missions/partials/create_workspace');
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

				$this->request->data['Workspace']['updated_user_id'] = $this->user_id;
				$this->request->data['Workspace']['created_by'] = $this->user_id;


				$start = $this->request->data['Workspace']['start_date'];
				$end = $this->request->data['Workspace']['end_date'];
				$check = $this->Common->check_date_validation_ws($start, $end, $project_id, null);

				$this->Workspace->set($this->request->data);

				if ($this->Workspace->validates()) {

					$this->loadModel('ProjectWorkspace');
					$this->Workspace->create();

					if (empty($check) || $check == null) {

						$this->request->data['Workspace']['start_date'] = date('Y-m-d', strtotime($this->request->data['Workspace']['start_date']));
						$this->request->data['Workspace']['end_date'] = date('Y-m-d', strtotime($this->request->data['Workspace']['end_date']));

						$this->request->data['Workspace']['title'] = strip_tags(substr($this->request->data['Workspace']['title'], 0, 50));
						//$this->request->data['Workspace']['description'] = strip_tags(substr($this->request->data['Workspace']['description'], 0, 250));

						$this->request->data['Workspace']['studio_status'] = 0;

						if ($this->Workspace->save($this->request->data)) {

							$this->Common->projectModified($project_id, $this->user_id);

							$insertId = 0;
							$response['success'] = true;
							if (isset($this->request->data['Workspace']['id']) && !empty($this->request->data['Workspace']['id'])) {
								$insertId = $this->request->data['Workspace']['id'];
							} else {
								$insertId = $this->Workspace->getLastInsertId();

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

							$response['content'] = ['id' => $insertId];
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
	 * Open Popup Modal Boxes method
	 *
	 * @return void
	 */
	public function get_templates($project_id = null, $workspace_id = null) {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			if (isset($workspace_id) && !empty($workspace_id)) {
				$response['workspace_id'] = $workspace_id;
				$response['project_id'] = $project_id;
			}

			$this->set('data', $response);

			$this->render('/Missions/partials/get_templates');
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

								$tdData = $this->TemplateDetail->find('all', [
									'conditions' => [
										'TemplateDetail.template_id' => $template_id,
									],
									'recursive' => -1,
									'order' => ['TemplateDetail.id ASC'],
								]);

								foreach ($tdData as $index => $detail) {
									$tdetail = $detail['TemplateDetail'];

									$adata = null;
									$adata['Area']['studio_status'] = 0;
									$adata['Area']['title'] = 'Area ' . ($index + 1);
									$adata['Area']['description'] = 'Area ' . ($index + 1);
									$adata['Area']['tooltip_text'] = 'Area ' . ($index + 1);
									$adata['Area']['status'] = 1;
									$adata['Area']['sort_order'] = $index + 1;
									$adata['Area']['template_detail_id'] = $tdetail['id'];
									$adata['Area']['workspace_id'] = $workspace_id;

									$this->Area->create();
									if ($this->Area->save($adata)) {

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
		     * @name  		shared_users
		     * @access		public
		     * @package  	App/Controller/StudiosController
	*/
	public function shared_users($type = 'feedback', $id = null) {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$detail = null;

			if ((isset($type) && !empty($type)) && (isset($id) && !empty($id))) {

				if (isset($id) && !empty($id)) {

					if ($type == 'feedback') {

						$this->loadModel('Feedback');
						$this->loadModel('FeedbackUser');
						$this->Feedback->bindModel(array('hasMany' => array('FeedbackUser')));
						$this->Feedback->bindModel(array('hasMany' => array('FeedbackResult')));

						$this->Feedback->recursive = 3;
						$this->FeedbackUser->bindModel(array('belongsTo' => array('User')));
						$detail = $this->Feedback->find('first', array('conditions' => array('Feedback.id' => $id), 'order' => array('Feedback.id' => 'desc')));

					} else if ($type == 'vote') {

						$this->loadModel('Vote');
						$this->loadModel('VoteUser');
						$this->Vote->bindModel(array('hasMany' => array('VoteResult')));
						$this->VoteUser->bindModel(array('belongsTo' => array('User')));

						$this->Vote->recursive = 3;
						$detail = $this->Vote->find('first', array('conditions' => array('Vote.id' => $id), 'order' => array('Vote.id' => 'desc')));

					}

				}
			}

			$this->set('viewData', $detail);
			$this->set('type', $type);

			$this->render('/Missions/partials/shared_users');
		}
	}

	/*
		     * @name  		add_asset
		     * @access		public
		     * @package  	App/Controller/StudiosController
	*/
	public function add_asset($type = null, $eid = null) {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$detail = null;

			if ((isset($type) && !empty($type)) && (isset($eid) && !empty($eid))) {

			}

			$this->set('type', $type);
			$this->set('eid', $eid);

			$this->render('/Missions/partials/add_asset');
		}
	}

	/*
		     * @name  		embedded_link
		     * @access		public
		     * @package  	App/Controller/StudiosController
	*/
	public function embedded_link($id = null) {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$data = null;

			if (isset($id) && !empty($id)) {
				$data = $this->ElementLink->findById($id);
			}

			$this->set('data', $data);

			$this->render('/Missions/partials/embedded_link');
		}
	}

	/*
		     * @name  		select_element
		     * @access		public
		     * @package  	App/Controller/StudiosController
	*/
	public function select_element($wid = null, $hash = null) {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$data = null;

			if (isset($wid) && !empty($wid)) {
				$data = workspace_elements($wid);
			}


			$this->set('data', $data);
			$this->set('hash', $hash);

			$this->render('/Missions/partials/select_element');
		}
	}

	/*
		     * @name  		activities
		     * @access		public
		     * @package  	App/Controller/StudiosController
	*/
	public function activities() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$data = $projects = null;
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;

				if ((isset($post['project_id']) && !empty($post['project_id'])) && (isset($post['workspace_id']) && !empty($post['workspace_id']))) {
					$project_id = $post['project_id'];
					$workspace_id = $post['workspace_id'];

					$data = $this->Activity->find('all', ['conditions' => ['Activity.element_type' => ['feedback', 'votes', 'element_documents', 'element_links', 'element_notes', 'element_decisions', 'element_mindmaps', 'element_tasks'], 'Activity.project_id' => $project_id, 'Activity.workspace_id' => $workspace_id], 'fields' => ['element_type', 'relation_id', 'updated_user_id', 'message', 'updated'], 'order' => ['updated DESC'], "group" => ["DATE_FORMAT(Activity.updated,'%Y-%m-%d %h:%i')", 'Activity.element_type']]);

				}
			}

			$view = new View($this, false);
			$view->viewPath = 'Missions/partials'; // Directory inside view directory to search for .ctp files
			$view->layout = false; // if you want to disable layout
			$view->set('data', $data);
			$view->set('projects', $projects);
			// pr($data, 1);
			$html = $view->render('activities');

			echo json_encode($html);
			exit;

		}
	}

	/*
		     * @name  		delete_workspace
		     * @access		public
		     * @package  	App/Controller/StudiosController
	*/
	public function delete_workspace() {

		if ($this->request->isAjax()) {

			$response = ['success' => false];
			$this->layout = 'ajax';

			$data = $projects = null;
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;

				if ((isset($post['project_id']) && !empty($post['project_id'])) && (isset($post['workspace_id']) && !empty($post['workspace_id']))) {
					$project_id = $post['project_id'];
					$workspace_id = $post['workspace_id'];

					$pwid = $this->ProjectWorkspace->find('first', ['conditions' => ['ProjectWorkspace.project_id' => $project_id, 'ProjectWorkspace.workspace_id' => $workspace_id], 'fields' => ['ProjectWorkspace.id']]);

					if (isset($pwid) && !empty($pwid)) {
						$pwid = Set::extract($pwid, '/ProjectWorkspace/id');
					}

					if ($this->ProjectWorkspace->delete(['ProjectWorkspace.id' => $pwid[0]], false)) {
						if ($this->Workspace->delete(['Workspace.id' => $workspace_id], false)) {
							$response['success'] = true;
						}
					}
				}
			}

			echo json_encode($response);
			exit;

		}
	}

}
