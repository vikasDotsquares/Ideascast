<?php
App::uses('Controller/Component', 'Auth', 'Session', 'RequestHandler');
App::uses('Sanitize', 'Utility');
App::uses('CakeEmail', 'Network/Email');
//App::import('Vendor', 'Classes/MPDF56/mpdf');
App::import('Vendor', 'MPDF56/PhpWord');

// App::import('Lib', 'Communications');

class BoardsController extends AppController {

	public $name = 'Boards';
	public $uses = ['User', 'UserDetail', 'ProjectPermission', 'UserSetting', 'Category', 'Aligned', 'UserProject', 'Project', 'Workspace', 'Area', 'ProjectWorkspace', 'Element', 'ProjectGroup', 'ProjectGroupUser', 'ProjectBoard', 'EmailNotification', 'DeclineReason', 'BoardResponse', 'Nudge', 'NudgeUser'];
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
	public $helpers = array('Html', 'Form', 'Session', 'Time', 'Text', 'Common', 'ViewModel', 'Mpdf', 'Group', 'Permission');

	public $objView = null;
	public $nudge_paging = 20;
	public $opp_offset = 0;

	public function beforeFilter() {
		parent::beforeFilter();
		$this->set('controller', 'projects');

		$view = new View();
		$this->objView = $view;

		$this->opp_offset = 10;

		$this->pagination['limit'] = 5;
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
		$this->set('title_for_layout', __('Opportunities', true));

		$viewData['page_heading'] = 'Opportunities';
		$viewData['page_subheading'] = 'View Projects seeking members from the community';

		$user_id = $this->Auth->user('id');
		$aligneds = $this->Aligned->find("list", ['order' => ['Aligned.title ASC']]);
		$usertype = 0;

		if (!empty($this->request->data)) {

			$sortby = $this->request->data['sort_by'];
			$usertype = $this->request->data['filter_by_user'];
			$aligned_id = trim($this->request->data['Project']['aligned_id']);
			$created = trim($this->request->data['Project']['created']);

			$conditions = array();
			if ($created == 'today') {
				$start = date('Y-m-d');
				$end = date('Y-m-d');
				$conditions = array(
					'date(Project.start_date) BETWEEN ? AND ?' => array($start, $end),
				);
			} else if ($created == 'last_7_day') {
				$end = date('Y-m-d');
				$start = date('Y-m-d', strtotime('-7 day'));
				$conditions = array(
					'date(Project.start_date) BETWEEN ? AND ?' => array($start, $end),
				);
			} else if ($created == 'last_14_day') {
				$end = date('Y-m-d');
				$start = date('Y-m-d', strtotime('-14 day'));
				$conditions = array(
					'date(Project.start_date) BETWEEN ? AND ?' => array($start, $end),
				);
			} else if ($created == 'last_30_day') {
				$end = date('Y-m-d');
				$start = date('Y-m-d', strtotime('-30 day'));
				$conditions = array(
					'date(Project.start_date) BETWEEN ? AND ?' => array($start, $end),
				);
			} else if ($created == 'last_90_day') {
				$end = date('Y-m-d');
				$start = date('Y-m-d', strtotime('-90 day'));
				$conditions = array(
					'date(Project.start_date) BETWEEN ? AND ?' => array($start, $end),
				);
			}

			$sortbydate = array();
			if (!empty($sortby)) {

				$start = date('Y-m-d');
				if ($sortby == 1) {
					$sortbydate = array('date(Project.start_date) <=' => $start);
				}
				if ($sortby == 2) {
					$sortbydate = array('date(Project.start_date) >' => $start);
				}

			}


			$lastConditions = array();
			if (!empty($aligned_id)) {
				$lastConditions = array(array('Project.aligned_id' => $aligned_id));
			}

			$userCondition = array('UserProject.user_id !=' => $user_id);
			if($usertype == 1) {
				//Fetch list of Projects displayed includes Projects that the current User owns
				$userCondition = array('UserProject.user_id' => $user_id, 'UserProject.owner_user'=> 1);
			}

			$viewData['projectsBoard'] = $this->UserProject->find('all', ['recursive' => 1, 'conditions' => ['UserProject.is_board' => 1, $sortbydate, $lastConditions, $conditions, $userCondition, 'Project.id !=' => null], 'order' => array('UserProject.id DESC')]);




		} else {

			/* -----------Projects for showing on/off from on project board page ----------- */
			$viewData['projectsBoard'] = $this->UserProject->find('all', ['recursive' => 1, 'conditions' => ['UserProject.is_board' => 1, 'UserProject.user_id !=' => $user_id, 'Project.id !=' => null], 'order' => array('UserProject.id DESC'), 'limit' => 5]);

		}

		$listBoardProject['boardProject'] = $this->ProjectBoard->find('all', ['recursive' => 2, 'conditions' => ['ProjectBoard.sender' => $user_id, 'ProjectBoard.project_status <' => 1], 'order' => array('ProjectBoard.id DESC')]);

		$crumb = [
			'last' => [
				'data' => [
					'title' => 'Opportunities',
					'data-original-title' => 'Opportunities',
				],
			],
		];
		if (isset($extra_crumb) && !empty($extra_crumb)) {
			$crumb = array_merge($extra_crumb, $crumb);
		}

		$this->set(compact('aligneds', 'viewData', 'crumb', 'listBoardProject','usertype'));

	}

	public function get_filter_project() {

		$this->layout = false;
		$this->autoRender = false;

		if (!empty($this->request->data['Project'])) {

			$viewData['projectsBoard'] = $this->UserProject->find('all', ['recursive' => 1, 'conditions' => ['UserProject.is_board' => 1, 'UserProject.user_id !=' => $user_id, 'Project.aligned_id' => $this->request->data['Project']]]);

			pr($viewData);
			exit;

		}
	}

	public function send_interest() {
		$response = [
			'success' => false,
			'msg' => null,
			'content' => null,
		];
		if (isset($this->request->data['UserDetail']) && !empty($this->request->data['UserDetail'])) {
			// pr($this->request->data, 1);
			$user_id = $this->request->data['UserDetail']['user_id'];
			$project_id = $this->request->data['Project']['id'];
			$message = $this->request->data['send_interest']['message'];

			if (!empty($user_id) && !empty($project_id) && !empty($message)) {

				//$projectUser = $this->Common->getProjectUser($project_id);

				//pr($projectUser); die;

				// If project already exists with same receiver and sender and also have project status 0 or 1
				$checkUserExis = $this->ProjectBoard->find('all', array('conditions' => array('project_id' => $project_id, 'receiver' => $user_id, 'project_status' => array(0, 1), 'sender' => $this->Session->read('Auth.User.id'))));

				if (!empty($checkUserExis) && count($checkUserExis) > 0) {

					$this->Session->setFlash(__('Project Interest request already Sent.'), 'error');
					exit;

				} else {

					$this->ProjectBoard->save(
						array(
							'project_id' => $project_id,
							'receiver' => $user_id,
							'sender' => $this->Session->read('Auth.User.id'),
							'board_msg' => $message,
						)
					);

					$projectDetails = ClassRegistry::init('Project')->find('first', array('conditions' => array('Project.id' => $project_id)));
					//======== Start Send Interest Email Notificatoins ============= Send Email and redirect to list page =====
					$loggedInemail = $this->Session->read('Auth.User.email');
					$name = $this->Session->read('Auth.User.UserDetail.full_name');
					$usersDetails = ClassRegistry::init('User')->find('first', array('conditions' => array('User.id' => $user_id)));
					$pageAction = SITEURL.'boards/project_request#project_view';
					$notifiInterest = $this->EmailNotification->find('first', ['conditions' => ['notification_type' => 'project', 'personlization' => 'project_interest_board', 'user_id' => $user_id]]);

					if ((!isset($notifiInterest['EmailNotification']['email']) || $notifiInterest['EmailNotification']['email'] == 1) && (!isset($usersDetails['User']['email_notification']) || $usersDetails['User']['email_notification'] == 1)) {

						$emailAddress = $usersDetails['User']['email'];
						$email = new CakeEmail();
						$email->config('Smtp');
						//$email->from(array($loggedInemail => $name));
						$email->from(array(ADMIN_FROM_EMAIL => SITENAME));
						$email->to($emailAddress);

						//$email->subject("Project Interest");
						$email->subject(SITENAME . ": Project interest");
						$email->template('project_interest');
						$email->emailFormat('html');
						$email->viewVars(array('Custname' => $usersDetails['UserDetail']['first_name'] . ' ' . $usersDetails['UserDetail']['last_name'], 'name' => $name, 'projectDetails' => $projectDetails, 'DefaultMessage' => $message, 'open_page'=>$pageAction));
						$email->send();

					}

					/************** socket messages **************/
					if (SOCKET_MESSAGES) {
						$current_user_id = $this->user_id;

						$send_notify = false;
						if (web_notify_setting($user_id, 'project', 'project_interest_board')) {
							$send_notify = true;
						}

						$userDetail = get_user_data($current_user_id);
						$content = [
							'notification' => [
								'type' => 'interest',
								'created_id' => $current_user_id,
								'project_id' => $project_id,
								'creator_name' => $userDetail['UserDetail']['full_name'],
								'subject' => 'Project interest',
								'heading' => 'Project: ' . strip_tags(getFieldDetail('Project', $project_id, 'title')),
								'sub_heading' => '',
								'date_time' => timezoneTimeConvertor($this->user_id, date('Y-m-d g:iA'), $format = 'd M Y g:iA') . user_country($this->user_id),
							],
						];
						if ($send_notify) {
							$content['received_users'] = [$user_id];
						}

						$response['content'] = $content;
					}
					/************** socket messages **************/

					//======== End Send Interest Email Notificatoins ===========================================================

					$this->Session->setFlash(__('Your project interest request has been sent.'), 'success');
					// return true;
					$response['success'] = true;

					echo json_encode($response);
					exit;
				}

			}

		}
	}

	public function project_request($project_id = null) {

		$this->layout = 'inner';

		$this->set('page_heading', 'Opportunity Requests');
		$this->set('title_for_layout', 'Opportunity Requests');
		$this->loadModel("UserPermission");
		// Check this user for previous data exists or not
		$permit_data = null;
		if (isset($this->user_id) && !empty($this->user_id)) {
			$owner_id = $this->user_id;

			$this->set('user_login', $owner_id);
			$this->User->unbindModel([
				'hasMany' => [
					'UserTransctionDetail',
					'UserInstitution',
					'UserPlan',
				],
				'hasOne' => [
					'UserDetail',
				],
			]);

			$this->ProjectBoard->bindModel(array(
				'hasOne' => array(
					'User' => array(
						'foreignKey' => false,
						'conditions' => array('User.id = ProjectBoard.sender'),
					),
					'UserDetail' => array(
						'foreignKey' => false,
						'conditions' => array('UserDetail.user_id = ProjectBoard.sender'),
					),

				),
			));

			$sql = " select ProjectBoard.*, Project.*, User.*, UserDetail.* from  user_permissions

					left join projects as Project on

						Project.id = user_permissions.project_id

					inner join project_boards as ProjectBoard on
						ProjectBoard.project_id = user_permissions.project_id

					inner join users as User on
						User.id = user_permissions.user_id
					inner join user_details as UserDetail on
						UserDetail.user_id = User.id


					where user_permissions.role in('Owner','Group Owner','Creator') and  workspace_id is null and user_permissions.user_id = $owner_id group by user_permissions.project_id order by ProjectBoard.id desc ";

			$pp_data = $this->UserPermission->query($sql);



			$this->ProjectBoard->bindModel(array(
				'hasOne' => array(
					'User' => array(
						'foreignKey' => false,
						'conditions' => array('User.id = ProjectBoard.sender'),
					),
					'UserDetail' => array(
						'foreignKey' => false,
						'conditions' => array('UserDetail.user_id = ProjectBoard.sender'),
					),

				),
			));

			$sql = " select ProjectBoard.*, Project.*, User.*, UserDetail.* from  user_permissions

					left join projects as Project on

						Project.id = user_permissions.project_id

					inner join project_boards as ProjectBoard on
						ProjectBoard.project_id = user_permissions.project_id

					inner join users as User on
						User.id = ProjectBoard.sender
					inner join user_details as UserDetail on
						UserDetail.user_id = User.id


					where user_permissions.role in('Owner','Group Owner','Creator') and  workspace_id is null and user_permissions.user_id = $owner_id group by ProjectBoard.sender order by ProjectBoard.id desc ";

			$pp_data_users = $this->UserPermission->query($sql);


			// pr($pp_data,1);

			$pp_data_count = ( isset($pp_data) && !empty($pp_data) ) ? count($pp_data) : 0;

			if (!empty($pp_data_count)) {
				$permit_data['pp_data'] = $pp_data;

			}
		}

		if (isset($project_id) && !empty($project_id)) {
			$this->setJsVar('project_id', $project_id);
		}
		$this->set('permit_data', $permit_data);
		$this->set('pp_data_users', $pp_data_users);

		$crumb = [
			'last' => [
				'data' => [
					'title' => 'Opportunity Requests',
					'data-original-title' => 'Opportunity Requests',
				],
			],
		];
		$this->set('crumb', $crumb);
	}

	public function project_decline($board_id = null) {

		$this->autoRender = false;

		$response = ['success' => false];
		$this->layout = 'ajax';

		if ($this->request->is('ajax')) {
			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;
				$id = $post['id'];
				$this->request->data['ProjectBoard']['id'] = $id;
				$this->request->data['ProjectBoard']['project_status'] = 2;

				if (!empty($id) && !empty($id)) {

					$response['success'] = true;

					if ($this->ProjectBoard->Save($this->request->data)) {

						$response['success'] = true;
					}
				}
			}
		}

		echo json_encode($response);
		exit();

	}

	public function project_accept($board_id = null) {

		$this->autoRender = false;

		$response = ['success' => false];
		$this->layout = 'ajax';

		if ($this->request->is('ajax')) {
			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;
				$id = $post['id'];
				$this->request->data['ProjectBoard']['id'] = $id;
				$this->request->data['ProjectBoard']['project_status'] = 1;

				if (!empty($id) && !empty($id)) {

					$response['success'] = true;

					if ($this->ProjectBoard->Save($this->request->data)) {

						$response['success'] = true;
					}
				}
			}
		}

		echo json_encode($response);
		exit();

	}

	public function decline_interest($board_id = null) {

		$this->layout = false;
		//$this->autoRender = false;

		$response = ['success' => false];
		$this->layout = 'ajax';

		if ($this->request->is('ajax')) {

			if (isset($this->request->params['named']) && !empty($this->request->params['named'])) {

				$data = $this->request->params['named'];
				$this->set('data', $data);
				$this->render('/Boards/partial/decline_interest');

			}
		}

	}


	public function decline_opp_interest($board_id = null) {

		$this->layout = false;
		$response = ['success' => false];
		$this->layout = 'ajax';

		if ($this->request->is('ajax')) {

			if (isset($this->request->params['named']) && !empty($this->request->params['named'])) {

				$data = $this->request->params['named'];
				$this->set('data', $data);
				$this->render('/Boards/partial/decline_opp_interest');

			}

		}

	}



	public function board_decline_save() {

		$this->layout = false;
		$this->autoRender = false;
		$response = ['success' => false];
		$this->layout = 'ajax';

		if ($this->request->is('ajax')) {

			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;

				// $id = $post['id'];
				$this->request->data['ProjectBoard']['id'] = $post['board_id'];
				$this->request->data['ProjectBoard']['project_status'] = 2;
				$this->request->data['ProjectBoard']['updated'] = date('Y-m-d H:i:s');
				$this->request->data['ProjectBoard']['responsed_by'] = $this->user_id;

				$this->request->data['BoardResponse']['project_id'] = $post['project_id'];
				$this->request->data['BoardResponse']['sender_id'] = $post['sender'];
				$this->request->data['BoardResponse']['receiver_id'] = $post['receiver'];
				$this->request->data['BoardResponse']['reason'] = $post['declinereason'];


				if ((isset($this->request->data['BoardResponse']) && !empty($this->request->data['BoardResponse'])) && (isset($this->request->data['ProjectBoard']) && !empty($this->request->data['ProjectBoard']))) {

					$response['success'] = true;

					if ($this->ProjectBoard->Save($this->request->data)) {
						$this->BoardResponse->Save($this->request->data);
						$response['success'] = true;
					}
				}
			}
		}

	}

	public function board_opp_decline_save() {

		$this->layout = false;
		$this->autoRender = false;
		$response = ['success' => false];
		$this->layout = 'ajax';

		if ($this->request->is('ajax')) {

			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;

				// $id = $post['id'];
				$this->request->data['ProjectBoard']['id'] = $post['board_id'];
				$this->request->data['ProjectBoard']['project_status'] = 2;
				$this->request->data['ProjectBoard']['updated'] = date('Y-m-d H:i:s');
				$this->request->data['ProjectBoard']['responsed_by'] = $this->user_id;

				$this->request->data['BoardResponse']['project_id'] = $post['project_id'];
				$this->request->data['BoardResponse']['sender_id'] = $post['sender'];
				$this->request->data['BoardResponse']['receiver_id'] = $post['receiver'];
				$this->request->data['BoardResponse']['reason'] = $post['declinereason'];


				if ((isset($this->request->data['BoardResponse']) && !empty($this->request->data['BoardResponse'])) && (isset($this->request->data['ProjectBoard']) && !empty($this->request->data['ProjectBoard']))) {

					$existing = $this->BoardResponse->find('first',
							['conditions'=>
								[
									'sender_id' => $post['sender'], 'receiver_id'=>$post['receiver'], 'project_id'=> $post['project_id']
								]
							]
					);
					if( isset($existing) && !empty($existing['BoardResponse']['id']) ){
						$this->request->data['BoardResponse']['id'] = $existing['BoardResponse']['id'];
					}

					//pr($this->request->data['BoardResponse']); die;

					$response['success'] = true;

					if ($this->ProjectBoard->Save($this->request->data)) {
						$this->BoardResponse->Save($this->request->data);

						// SAVE TO ACTIVITY
						$opp_data = [
							'project_id' => $post['project_id'],
							'updated_user_id' => $this->user_id,
							'message' => 'Project opportunity request declined',
							'updated' => date("Y-m-d H:i:s"),
						];
						$this->loadModel('ProjectActivity');
						$this->ProjectActivity->id = null;
						$this->ProjectActivity->save($opp_data);

						$response['success'] = true;
						//return $response;
					}
				}
			}

		}

	}

	public function getBoardRequestCount($user_id = null) {

		$projectRequest = $this->ProjectBoard->find("count", array(
			"conditions" => array(
				"ProjectBoard.receiver" => $this->Session->read("Auth.User.id"),
				"ProjectBoard.project_status" => 0,
			),
		));

		return $projectRequest;
	}

	public function __project_workspace_selectbox($project_id = null, $user_id = null, $project_type = null) {

		$us_permission = $this->Common->userproject($project_id, $user_id);
		$pr_permission = $this->Common->project_permission_details($project_id, $user_id);

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

		$myWorkspaceslistByproject = array();
		if (isset($workspaces) && !empty($workspaces)) {
			foreach ($workspaces as $valWP) {
				$myWorkspaceslistByproject[$valWP['Workspace']['id']] = trim(strip_tags(str_replace("&nbsp;", " ", $valWP['Workspace']['title'])));
			}
		}

		return $myWorkspaceslistByproject;
	}

	public function status_board($project_id = null) {

		$this->layout = 'inner';

		// App::import('Controller', 'Users');

		if ($project_id < 1 || empty($project_id)) {
			$this->Session->setFlash(__('Invalid selection.'), 'error');
			$this->redirect('/dashboards/project_center');
			exit;
		}

		$user_id = $this->Auth->user('id');

		$user_project = $this->Common->userproject($project_id, $user_id);
		$p_permission = $this->Common->project_permission_details($project_id, $user_id);

		$project_level = 0;
		$group_permission = array();
		$grp_id = $this->Group->GroupIDbyUserID($project_id, $user_id);
		if (isset($grp_id) && !empty($grp_id)) {

			$group_permission = $this->Group->group_permission_details($project_id, $grp_id);

			if (isset($group_permission['ProjectPermission']['project_level']) && $group_permission['ProjectPermission']['project_level'] == 1) {
				$project_level = $group_permission['ProjectPermission']['project_level'];
				$this->set('project_level', $project_level);
			}
			$this->set('gpid', $grp_id);
		}

		if (((isset($user_project)) && (!empty($user_project))) || (isset($project_level) && $project_level == 1) || (isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level'] == 1)) {
			$flag = true;
		} else {
			$flag = false;
		}

		if ($flag == false) {
			$this->Session->setFlash(__('Invalid selection.'), 'error');
			$this->redirect('/dashboards/project_center');
		}

		$view = new View();
		$viewModel = $view->loadHelper('ViewModel');

		$user_id = $this->user_id;
		$myWorkspaceslistByproject = $this->__project_workspace_selectbox($project_id, $user_id, 'm_project');

		$this->set('wsps', $myWorkspaceslistByproject);

		$aligned = $this->Aligned->find("list", ['order' => ['Aligned.id ASC']]);
		$this->set('aligned', $aligned);

		$aligned = $this->Aligned->find("list", ['order' => ['Aligned.id ASC']]);
		$this->set('aligned', $aligned);

		$this->set('project_id', $project_id);

		$projectDetails = $this->UserProject->find('first', ['recursive' => 1, 'conditions' => ['Project.id' => $project_id]]);

		$this->set('page_heading', __($projectDetails['Project']['title'], true));
		$this->set('title_for_layout', __('Work Board - OpusView', true));
		$this->set('page_subheading', __('View key information about your Projects', true));

		// $extra_crumb = get_category_list($project_id);

		$crumb = [
			'Summary' => [
				'data' => [
					'url' => '/projects/index/' . $project_id,
					'class' => 'tipText',
					'title' => $projectDetails['Project']['title'],
					'data-original-title' => $projectDetails['Project']['title'],
				],
			],
			'last' => ['Work Board'],
		];


		$this->set('crumb', $crumb);
		$this->set('projects', $projectDetails);

		$result = $params = null;
		$conditions = $pw_condition = [];
		$order = '';
		if (isset($project_id) && !empty($project_id)) {

			$params['project_id'] = $project_id;
			$pw_condition['ProjectWorkspace.project_id'] = $project_id;

			if (isset($post['ws_id']) && !empty($post['ws_id'])) {
				$pw_condition['ProjectWorkspace.workspace_id'] = $post['ws_id'];
			}
			$pw_condition['Workspace.studio_status !='] = 1;

			$workspaces = $elements = $ws_ids = $area_ids = null;

			$this->ProjectWorkspace->Behaviors->load('Containable');

			$workspaces = $this->ProjectWorkspace->find('all', ['conditions' => $pw_condition, 'contain' => 'Workspace']);

			if (isset($workspaces) && !empty($workspaces)) {

				foreach ($workspaces as $key => $value) {

					$workspace = $value['Workspace'];

					$ws_ids[] = $workspace['id'];

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
						$query .= "AND element.date_constraints = 1";

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

			$data = $this->Element->query($select . $diff . $from . $query . '' . $order);

			//pr($data); die;

			$this->set('workspace_area', $data);

			/* ======================================================================== */

		}

		$project_type = CheckProjectType($project_id, $this->user_id);
		$this->set('project_type', $project_type);

	}

	public function counters($project_id = null) {
		$html = '';
		// $this->autoRender = false;
		if ($this->request->isAjax()) {
			$this->layout = false;
			$html = '';
			if ($this->request->is('post') || $this->request->is('put')) {
				$project_id = (isset($project_id) && !empty($project_id)) ? $project_id : null;
				$view = new View($this, false);
				$view->viewPath = 'Boards/partial';
				$view->set("project_id", $project_id);
				$html = $view->render('counters');
			}
			echo json_encode($html);
			exit;
		}

	}


	public function filtered_list() {
		$html = '';
		$this->autoRender = false;
		if ($this->request->isAjax()) {

			$view = new View();
			$viewModel = $view->loadHelper('ViewModel');

			$this->loadModel('ProjectWorkspace');
			$this->layout = 'ajax';
			$response = [
				'success' => false,
				'msg' => '',
				'content' => null,
			];

			$row = $data = null;

			/* ======================================================================= */

			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;

				$view = new View();
				$viewModel = $view->loadHelper('ViewModel');

				$result = $params = null;
				$conditions = $pw_condition = [];
				$order = '';

				if (isset($post['project_id']) && !empty($post['project_id'])) {

					$project_id = $post['project_id'];
					$params['project_id'] = $project_id;
					$pw_condition['ProjectWorkspace.project_id'] = $project_id;

					if (isset($post['ws_id']) && !empty($post['ws_id'])) {
						$pw_condition['ProjectWorkspace.workspace_id'] = $post['ws_id'];
					}
					$pw_condition['Workspace.studio_status !='] = 1;

					$workspaces = $elements = $ws_ids = $area_ids = null;

					$this->ProjectWorkspace->Behaviors->load('Containable');

					$workspaces = $this->ProjectWorkspace->find('all', ['conditions' => $pw_condition, 'contain' => 'Workspace']);

					if (isset($workspaces) && !empty($workspaces)) {

						foreach ($workspaces as $key => $value) {

							$workspace = $value['Workspace'];

							$ws_ids[] = $workspace['id'];

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

					$data = $this->Element->query($select . $diff . $from . $query . '' . $order);

					$this->set('workspace_area', $data);
					$this->set('project_id', $project_id);

					$view = new View($this, false);
					$view->viewPath = 'Boards'; // Directory inside view directory to search for .ctp files
					$view->layout = false; // if you want to disable layout
					$html = $view->render('task_list');

					/* ======================================================================== */

				}
				echo json_encode($html);
				exit();
			}
		}
	}

	public function board_element_date($workspace_id = null, $area_id = null, $element_id = null) {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			if (isset($workspace_id) && !empty($workspace_id)) {

				$response['workspace_id'] = $workspace_id;
				$response['area_id'] = $area_id;

				// Get all workspace elements
				$response['all_elements'] = $this->objView->loadHelper('ViewModel')->area_elements($area_id, false, $element_id);

			}
			$this->set('response', $response);

			$this->render('/Boards/partial/board_element_date');
		}
	}

	public function send_nudge() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$program_id = $project_id = $workspace_id = $task_id = $risk_id = $todo_id = null;

			$type = $this->params['named']['type'];
			if(isset($this->params['named']) && !empty($this->params['named'])){
				$params = $this->params['named'];
				// pr($params, 1);

				$program_id = (isset($params['program']) && !empty($params['program'])) ? $params['program'] : null;
				$project_id = (isset($params['project']) && !empty($params['project'])) ? $params['project'] : null;
				$workspace_id = (isset($params['workspace']) && !empty($params['workspace'])) ? $params['workspace'] : null;
				$task_id = (isset($params['task']) && !empty($params['task'])) ? $params['task'] : null;
				$hash = (isset($params['hash']) && !empty($params['hash'])) ? $params['hash'] : null;
				$risk_id = (isset($params['risk']) && !empty($params['risk'])) ? $params['risk'] : null;
				$todo_id = (isset($params['todo']) && !empty($params['todo'])) ? $params['todo'] : null;
				$user_id = (isset($params['user']) && !empty($params['user'])) ? $params['user'] : null;
			}

			$this->set('type', $type);
			$this->set('program_id', $program_id);
			$this->set('project_id', $project_id);
			$this->set('workspace_id', $workspace_id);
			$this->set('task_id', $task_id);
			$this->set('hash', $hash);
			$this->set('risk_id', $risk_id);
			$this->set('todo_id', $todo_id);
			$this->set('user_id', $user_id);

			$this->render('/Boards/partial/send_nudge');
		}
	}

	public function send_nudge_board() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$program_id = $project_id = $workspace_id = $task_id = $risk_id = $todo_id = null;

			$type = $this->params['named']['type'];
			$search_tag_users = (isset($this->params['named']['search_tag_users']) && !empty($this->params['named']['search_tag_users'])) ? $this->params['named']['search_tag_users'] : "";
			if(isset($this->params['named']) && !empty($this->params['named'])){
				$params = $this->params['named'];
				// pr($params, 1);

				$program_id = (isset($params['program']) && !empty($params['program'])) ? $params['program'] : null;
				$project_id = (isset($params['project']) && !empty($params['project'])) ? $params['project'] : null;
				$workspace_id = (isset($params['workspace']) && !empty($params['workspace'])) ? $params['workspace'] : null;
				$task_id = (isset($params['task']) && !empty($params['task'])) ? $params['task'] : null;
				$hash = (isset($params['hash']) && !empty($params['hash'])) ? $params['hash'] : null;
				$risk_id = (isset($params['risk']) && !empty($params['risk'])) ? $params['risk'] : null;
				$todo_id = (isset($params['todo']) && !empty($params['todo'])) ? $params['todo'] : null;
				$selected = (isset($params['selected']) && !empty($params['selected'])) ? $params['selected'] : [];
			}
			/*Code for Show all tags and skills on pop up*/
			$this->loadModel('Tag');
			$this->loadModel('Skill');
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
			$this->set('tags', $sk);
			/*Code for Show all tags and skills on pop up*/
			$this->set('type', $type);
			$this->set('search_tag_users', $search_tag_users);
			$this->set('program_id', $program_id);
			$this->set('project_id', $project_id);
			$this->set('workspace_id', $workspace_id);
			$this->set('task_id', $task_id);
			$this->set('hash', $hash);
			$this->set('risk_id', $risk_id);
			$this->set('todo_id', $todo_id);
			$this->set('selected', $selected);

			$this->render('/Boards/partial/send_nudge_board');
		}
	}

	public function save_nudge() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$response = ['success' => false, 'content' => []];
			$program_id = $project_id = $workspace_id = $task_id = $risk_id = $todo_id = $type = null;

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				// pr($post, 1);
				$mt = microtime();
				$mt = explode(' ', $mt);
				$hash_code = $mt[1];

				// $mt = str_replace(" ","",$mt);
				// $mt = str_replace(".","",$mt);
				$type = $post['type'];
				$nudgeData = [
					'user_id' => $this->user_id,
					'subject' => $post['subject'],
					'message' => $post['message'],
					'page_link' => $post['page_link'],
					'email' => $post['email'],
					'program_id' => $post['program_id'],
					'project_id' => $post['project_id'],
					'workspace_id' => $post['workspace_id'],
					'element_id' => $post['task_id'],
					'rm_detail_id' => $post['risk_id'],
					'do_list_id' => $post['todo_id'],
					'hash_code' => $post['hash'],
					'type' => $post['type'],
				];
				if($this->Nudge->save($nudgeData)){
					$response['success'] = true;
					$nudge_id = $this->Nudge->getLastInsertId();
					if(isset($post['user']) && !empty($post['user'])){
						if($post['type'] == 'profile') {
							$p_user_id = $post['user'];
							unset($post['user']);
							$post['user'][0] = $p_user_id;
						}
						$index = 0;
						$nuData = [];
						foreach ($post['user'] as $key => $value) {
							$nuData[$index++]['NudgeUser'] = [
								'nudge_id' => $nudge_id,
								'sender_id' => $this->user_id,
								'receiver_id' => $value,
								'status' => 0,
								'response' => 0,
								'hash_code' => $hash_code,
							];
						}
						$this->NudgeUser->saveAll($nuData);
						/************** socket messages **************/
						if (SOCKET_MESSAGES) {
							$current_user_id = $this->user_id;
							$all_users = $nudge_users = $post['user'];
							$subheading = '';
							if(isset($post['project_id']) && !empty($post['project_id'])){
								$subheading = 'Project: ' . strip_tags(getFieldDetail('Project', $post['project_id'], 'title'));
							}
							/*else if(isset($post['program_id']) && !empty($post['program_id'])){
								$subheading = 'Program: ' . strip_tags(getFieldDetail('Program', $post['program_id'], 'program_name'));
							}*/

							$userDetail = get_user_data($current_user_id);
							$content = [
								'am_pm' => timezoneTimeConvertor($this->user_id, date('Y-m-d g:iA'), $format = 'A'),
								'notification' => [
									'type' => 'nudge',
									'created_id' => $current_user_id,
									'project_id' => $post['project_id'],
									'refer_id' => null,
									'creator_name' => $userDetail['UserDetail']['full_name'],
									'subject' => htmlent($post['subject']),
									// 'heading' => 'Nudge: ' . $post['subject'],
									'sub_heading' => 'From: '.$userDetail['UserDetail']['full_name'],
									'date_time' => timezoneTimeConvertor($this->user_id, date('Y-m-d g:i'), $format = 'd M Y g:i')
								],
							];
							if (is_array($nudge_users)) {
								$content['received_users'] = $nudge_users;
							}
							$response['content']['socket'] = $content;
						}
						/************** socket messages **************/

						/************** send email **************/
						if((isset($post['user']) && !empty($post['user'])) && (isset($post['email']) && !empty($post['email']))){
							foreach ($post['user'] as $key => $value) {
								$link = SITEURL.'boards/open_nudge/'.$nudge_id.'/'.$value;
								$edata = [
									'user_id' => $value,
									'subject' => $post['subject'],
									'message' => htmlent($post['message']),
									'url' => $link,
									'hide_button' => ($post['page_link']) ? true : false
								];
								$this->Common->nudge_email($edata);
							}
						}
						/************** send email **************/
					}
				}

			}
			echo json_encode($response);
			exit;
		}
	}

	public function nudge_list() {
		$this->layout = 'inner';
		$this->set('title_for_layout', __('Nudges', true));
		$viewData['page_heading'] = 'My Nudges';
		$viewData['page_subheading'] = 'View Nudges you sent and received';

		$this->loadModel('Nudge');
		$this->loadModel('NudgeUser');

		$limit = 'Limit '.$this->nudge_paging;

		$params = "((((`NudgeUser`.`receiver_archive` != 1) AND (`NudgeUser`.`receiver_id` = '".$this->user_id."') AND (`NudgeUser`.`sender_id` != '".$this->user_id."'))) OR (((`NudgeUser`.`receiver_archive` != 1) AND (`NudgeUser`.`receiver_id` = '".$this->user_id."') AND (`NudgeUser`.`sender_archive` != 1) AND (`NudgeUser`.`sender_id` = '".$this->user_id."'))))";

		$query = 'SELECT `NudgeUser`.`id`, `NudgeUser`.`nudge_id`, `NudgeUser`.`sender_id`, `NudgeUser`.`receiver_id`, `NudgeUser`.`status`, `NudgeUser`.`response`, `NudgeUser`.`sender_archive`, `NudgeUser`.`receiver_archive`, `NudgeUser`.`hash_code`, `NudgeUser`.`created`, `NudgeUser`.`modified`, `Nudge`.`id`, `Nudge`.`user_id`, `Nudge`.`subject`, `Nudge`.`message`, `Nudge`.`page_link`, `Nudge`.`email`, `Nudge`.`program_id`, `Nudge`.`project_id`, `Nudge`.`workspace_id`, `Nudge`.`element_id`, `Nudge`.`rm_detail_id`, `Nudge`.`do_list_id`, `Nudge`.`hash_code`, `Nudge`.`type`, `Nudge`.`created`, `Nudge`.`modified` FROM `nudge_users` AS `NudgeUser` LEFT JOIN `nudges` AS `Nudge` ON (`NudgeUser`.`nudge_id` = `Nudge`.`id`) WHERE '.$params.' ORDER BY `NudgeUser`.`created` DESC '.$limit;
		$viewData['nudges'] = $this->NudgeUser->query($query);

		$query = 'SELECT count(*) as total FROM `nudge_users` AS `NudgeUser` LEFT JOIN `nudges` AS `Nudge` ON (`NudgeUser`.`nudge_id` = `Nudge`.`id`) WHERE '.$params.' ORDER BY `NudgeUser`.`created` DESC ';
		$viewData['total_nudges'] = $this->NudgeUser->query($query);
		$viewData['total_nudges'] = $viewData['total_nudges'][0][0]['total'];

		$this->set($viewData);

		$crumb = [
			'last' => [
				'data' => [
					'title' => 'Nudges',
					'data-original-title' => 'Nudges',
				],
			],
		];
		$this->set('crumb', $crumb);
	}

	public function nudge_listing() {
		$this->layout = 'ajax';

		$this->loadModel('Nudge');
		$this->loadModel('NudgeUser');

		$viewData = [];
		if ($this->request->is('post') || $this->request->is('put')) {
			$post = $this->request->data;

			$params = '';

			$conditions = [];

			if(isset($post['search_sel']) && !empty($post['search_sel'])){
				if($post['search_sel'] == 1) { // sent by me
					$params = "NudgeUser.sender_id = '".$this->user_id."' AND NudgeUser.sender_archive != '1'";
				}
				else if($post['search_sel'] == 2) {// sent by others
						$params = "((((`NudgeUser`.`receiver_archive` != 1) AND (`NudgeUser`.`receiver_id` = '".$this->user_id."') AND (`NudgeUser`.`sender_id` != '".$this->user_id."'))) OR (((`NudgeUser`.`receiver_archive` != 1) AND (`NudgeUser`.`receiver_id` = '".$this->user_id."') AND (`NudgeUser`.`sender_archive` != 1) AND (`NudgeUser`.`sender_id` = '".$this->user_id."'))))";

				}
				else if($post['search_sel'] == 3) {// all but only archieved
						$params = "((((`NudgeUser`.`sender_archive` = 1) AND (`NudgeUser`.`sender_id` = '".$this->user_id."'))) OR (((`NudgeUser`.`receiver_archive` = 1) AND (`NudgeUser`.`receiver_id` = '".$this->user_id."'))))";
				}
			}
			else{
					$params = '(( (((`NudgeUser`.`sender_archive` != 1) AND (`NudgeUser`.`sender_id` = '.$this->user_id.'))) and (((`NudgeUser`.`receiver_archive` != 1) AND (`NudgeUser`.`receiver_id` = '.$this->user_id.')))  ) or ( (((`NudgeUser`.`sender_archive` != 1) AND (`NudgeUser`.`sender_id` = '.$this->user_id.') and  (`NudgeUser`.`receiver_id` != '.$this->user_id.')   )) OR (((`NudgeUser`.`receiver_archive` != 1) AND (`NudgeUser`.`receiver_id` = '.$this->user_id.')  AND (`NudgeUser`.`sender_id` != '.$this->user_id.')  )) )) ';
			}

			$ser = '^';
			if(isset($post['search_str']) && !empty($post['search_str'])){

				$search_str= Sanitize::escape(like($post['search_str'], $ser ));

				//die;
				$params .= " AND (Nudge.subject LIKE '(%$search_str%' ESCAPE '$ser' OR Nudge.message LIKE '%$search_str%' ESCAPE '$ser')";
			}

			$limit = 'Limit '.$this->nudge_paging;
			if(isset($post['offset']) && !empty($post['offset'])){
				$limit = 'Limit '.$post['offset'].', '.$this->nudge_paging;
			}

			 $query = 'SELECT `NudgeUser`.`id`, `NudgeUser`.`nudge_id`, `NudgeUser`.`sender_id`, `NudgeUser`.`receiver_id`, `NudgeUser`.`status`, `NudgeUser`.`response`, `NudgeUser`.`sender_archive`, `NudgeUser`.`receiver_archive`, `NudgeUser`.`hash_code`, `NudgeUser`.`created`, `NudgeUser`.`modified`, `Nudge`.`id`, `Nudge`.`user_id`, `Nudge`.`subject`, `Nudge`.`message`, `Nudge`.`page_link`, `Nudge`.`email`, `Nudge`.`program_id`, `Nudge`.`project_id`, `Nudge`.`workspace_id`, `Nudge`.`element_id`, `Nudge`.`rm_detail_id`, `Nudge`.`do_list_id`, `Nudge`.`hash_code`, `Nudge`.`type`, `Nudge`.`created`, `Nudge`.`modified` FROM `nudge_users` AS `NudgeUser` LEFT JOIN `nudges` AS `Nudge` ON (`NudgeUser`.`nudge_id` = `Nudge`.`id`) WHERE '.$params.' ORDER BY `NudgeUser`.`created` DESC '.$limit;

			$viewData['nudges'] = $this->NudgeUser->query($query);
			 // pr($viewData['nudges'], 1);

		}

		$this->set($viewData);
		$this->render('/Boards/partial/nudge_listing');
	}

	public function count_nudge_list() {
		$this->layout = 'ajax';

		$this->loadModel('Nudge');
		$this->loadModel('NudgeUser');

		$response = ['success' => false, 'content' => 0];
		if ($this->request->is('post') || $this->request->is('put')) {
			$post = $this->request->data;

			$params = '';

			$conditions = [];

			if(isset($post['search_sel']) && !empty($post['search_sel'])){
				if($post['search_sel'] == 1) { // sent by me
					$params = "NudgeUser.sender_id = '".$this->user_id."' AND NudgeUser.sender_archive != '1'";
				}
				else if($post['search_sel'] == 2) {// sent by others
						$params = "((((`NudgeUser`.`receiver_archive` != 1) AND (`NudgeUser`.`receiver_id` = '".$this->user_id."') AND (`NudgeUser`.`sender_id` != '".$this->user_id."'))) OR (((`NudgeUser`.`receiver_archive` != 1) AND (`NudgeUser`.`receiver_id` = '".$this->user_id."') AND (`NudgeUser`.`sender_archive` != 1) AND (`NudgeUser`.`sender_id` = '".$this->user_id."'))))";

				}
				else if($post['search_sel'] == 3) {// all but only archieved
						$params = "((((`NudgeUser`.`sender_archive` = 1) AND (`NudgeUser`.`sender_id` = '".$this->user_id."'))) OR (((`NudgeUser`.`receiver_archive` = 1) AND (`NudgeUser`.`receiver_id` = '".$this->user_id."'))))";
				}
			}
			else{
				$params = '(( (((`NudgeUser`.`sender_archive` != 1) AND (`NudgeUser`.`sender_id` = '.$this->user_id.'))) and (((`NudgeUser`.`receiver_archive` != 1) AND (`NudgeUser`.`receiver_id` = '.$this->user_id.')))  ) or ( (((`NudgeUser`.`sender_archive` != 1) AND (`NudgeUser`.`sender_id` = '.$this->user_id.') and  (`NudgeUser`.`receiver_id` != '.$this->user_id.')   )) OR (((`NudgeUser`.`receiver_archive` != 1) AND (`NudgeUser`.`receiver_id` = '.$this->user_id.')  AND (`NudgeUser`.`sender_id` != '.$this->user_id.')  )) )) ';
			}

			if(isset($post['search_str']) && !empty($post['search_str'])){
				 $ser = '^';
				 $search_str= Sanitize::escape(like($post['search_str'], $ser ));

				 $params .= " AND (Nudge.subject LIKE '(%$search_str%' ESCAPE '$ser' OR Nudge.message LIKE '%$search_str%' ESCAPE '$ser')";


			}

			$query = 'SELECT count(*) as total_record FROM `nudge_users` AS `NudgeUser` LEFT JOIN `nudges` AS `Nudge` ON (`NudgeUser`.`nudge_id` = `Nudge`.`id`) WHERE '.$params.' ';


			$nudges = $this->NudgeUser->query($query);
			$response['success'] = true;
			$response['content'] = $nudges[0][0]['total_record'];

		}
		echo json_encode($response);
		exit;
	}

	public function open_nudge($nudge_id = null, $receiver_id = null, $hash_code = null) {
		$this->layout = 'ajax';
		$this->loadModel('Nudge');
		$this->loadModel('NudgeUser');

		$nudge_data = $this->NudgeUser->find('first', ['conditions' => ['NudgeUser.nudge_id' => $nudge_id, 'NudgeUser.receiver_id' => $receiver_id]]);

		$this->NudgeUser->id = $nudge_data['NudgeUser']['id'];
		$this->NudgeUser->saveField('response', 1);
		$this->NudgeUser->saveField('status', 1);
		$link = nudge_link($nudge_data['Nudge']);
		// pr($link);die;

		$this->redirect($link);
	}

	public function change_archive_status($nudge_id = null, $receiver_id = null, $hash_code = null) {
		$this->layout = 'ajax';
		$this->loadModel('Nudge');
		$this->loadModel('NudgeUser');

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$response = ['success' => false, 'content' => []];

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				$nu_id = $post['nudge_user_id'];
				$type = $post['type'];
				$current_status = $post['current_status'];

				$updateTo = 'receiver_archive';
				$value = 0;
				// IF USER IS SENDER
				if($type == 'sent'){
					$updateTo = 'sender_archive';
					if($current_status == 'archive'){
						$value = 1;
					}
				}
				else{
					if($current_status == 'archive'){
						$value = 1;
					}
				}

				$this->NudgeUser->id = $nu_id;
				if($this->NudgeUser->saveField($updateTo, $value)){
					$response['success'] = true;
						$rec = $this->NudgeUser->find('first', ['conditions' => ['NudgeUser.id' => $nu_id]]);
						if($rec['NudgeUser']['sender_id'] == $this->user_id && $rec['NudgeUser']['receiver_id'] == $this->user_id) {
							$this->NudgeUser->id = $nu_id;
							if($current_status == 'archive'){
								$this->NudgeUser->saveField('sender_archive', 1);
							}
							else{
								$this->NudgeUser->saveField('sender_archive', 0);
							}
						}
				}
			}
		}

		echo json_encode($response);
		exit;
	}

	public function gotit_nudge($nudge_id = null, $receiver_id = null, $hash_code = null) {
		$this->layout = 'ajax';
		$this->loadModel('Nudge');
		$this->loadModel('NudgeUser');

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$response = ['success' => false, 'content' => []];

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				$nu_id = $post['nudge_user_id'];

				$this->NudgeUser->id = $nu_id;
				$this->NudgeUser->saveField('response', 1);
				$this->NudgeUser->saveField('status', 1);
				$response['success'] = true;
			}
		}

		echo json_encode($response);
		exit;
	}

	public function update_nudge_row($nudge_id = null, $receiver_id = null, $hash_code = null) {
		$this->layout = 'ajax';
		$this->loadModel('Nudge');
		$this->loadModel('NudgeUser');

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$response = ['success' => false, 'content' => []];

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				$nu_id = $post['nudge_user_id'];

				$viewData['nudges'] = $this->NudgeUser->find('first', [
					'conditions' => [
						'NudgeUser.id' => $nu_id
					]
				]);
				// pr($viewData['nudges'], 1);
				$this->set($viewData);
			}
		}

		$this->render('/Boards/partial/nudge_row');
	}

	public function browse_next_menus() {
		$this->layout = 'ajax';


		if ($this->request->isAjax()) {
			$response = ['success' => false, 'content' => []];

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				// pr($post, 1);

				if(isset($post['type']) && !empty($post['type'])){
					$type = $post['type'];
					$viewData['id'] = $post['id'];
					if($type == 'browse-project'){
						//get all wsp of supplied project
						$viewData['type'] = 'browse-workspace';
					}
					if($type == 'browse-workspace'){
						//get all wsp of supplied project
						$viewData['type'] = 'browse-area';
					}
					if($type == 'browse-area'){
						//get all wsp of supplied project
						$viewData['type'] = 'browse-task';
					}
					$this->set($viewData);
				}
				// $this->set($viewData);
			}
		}

		$this->render('/Elements/front/browse_next_menus');
	}


	/* OPPORTUNITY */
	public function opportunity(){
		$this->layout = 'inner';
		$this->set('title_for_layout', __('Opportunities', true));

		$viewData['page_heading'] = 'Opportunities';
		$viewData['page_subheading'] = 'Find Opportunities seeking new Team Members from the community';

		$trigger_tab = '';
		$click_projectid = '';
		if( isset($this->request->params['pass'][0]) && !empty($this->request->params['pass'][0]) && $this->request->params['pass'][0] == 'request' ){
			$trigger_tab = '#tab_request';
			if( !empty($this->request->params['pass'][1]) ){
				$click_projectid = $this->request->params['pass'][1];
			}
		}


		$viewData['data'] = $this->find_opportunity();

		$viewData['crumb'] = [
			'last' => [
				'data' => [
					'title' => 'Opportunities',
					'data-original-title' => 'Opportunities',
				],
			],
		];
		$this->set($viewData);
		$this->setJsVar('tab', $trigger_tab);
		$this->setJsVar('selected_project', $click_projectid);
		$this->setJsVar('opp_offset', $this->opp_offset);

	}
	public function more_information($pid = null, $tab = null, $request_user = null) {

        if ($this->request->isAjax()) {
            $this->layout = 'ajax';
            $data = [];
            $this->set('data', $data);
			$this->set('pid', $pid);
			$this->set('tab', $tab);
			if( isset($request_user) && !empty($request_user) ){
				$this->set('request_user', $request_user);
			}
			$html = $this->render('partial/more_information');
			return $html;
			exit();

		}
	}

	public function join_req($pid = null) {

        if ($this->request->isAjax()) {
            $this->layout = 'ajax';
            $data = [];
            $data = $this->Project->find('first', ['conditions' => ['Project.id' => $pid], 'recursive' => -1, 'fields' => ['title']]);
            // pr($data);

            if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				$response = [
					'success' => false,
					'content' => null,
				];
				$project_id = $post['project_id'];
				$message = $post['message'];

				$owner = $this->Project->query("SELECT user_id, role FROM user_permissions WHERE role IN ('Creator','Owner','Group Owner') AND project_id = $project_id and workspace_id IS NULL");
				$all_owner = array();
				$cretor_id = '';
				if( isset($owner) && !empty($owner) ){
					foreach($owner as $key => $userLIst){
						if( $userLIst['user_permissions']['role'] == 'Creator' ){
							$cretor_id = $userLIst['user_permissions']['user_id'];
						}
						$all_owner[$key] = $userLIst['user_permissions'];
					}
				}


				//$owner_id = $owner[0]['user_permissions']['user_id'];

				$reqExists = $this->ProjectBoard->find('all', array('conditions' => array('project_id' => $project_id, 'receiver' => $cretor_id, 'project_status' => array(0, 1), 'sender' => $this->user_id )));

				// IF REQUEST IS NOT SENT FOR THIS PROJECT BY THIS USER
				if(!isset($reqExists) || empty($reqExists)) {
					$this->ProjectBoard->save(
						array(
							'project_id' => $project_id,
							'receiver' => $cretor_id,
							'sender' => $this->user_id,
							'board_msg' => $message,
						)
					);

					// SAVE TO ACTIVITY
						$task_data = [
							'project_id' => $project_id,
							'updated_user_id' => $this->user_id,
							'message' => 'Project opportunity request received.',
							'updated' => date("Y-m-d H:i:s"),
						];
						$this->loadModel('ProjectActivity');
						$this->ProjectActivity->id = null;
						$this->ProjectActivity->save($task_data);

					/* owner user loop start  */
			$owner_user = array();
			if( isset($all_owner) && !empty($all_owner) ){
				foreach($all_owner as $listUsers){
					//e($listUsers['user_id']);
					$owner_id = $owner_user[] = $listUsers['user_id'];

					$notifiInterest = $this->EmailNotification->find('first', ['conditions' => ['notification_type' => 'project', 'personlization' => 'project_interest_board', 'user_id' => $owner_id]]);

					$usersDetails = ClassRegistry::init('User')->find('first', array('conditions' => array('User.id' => $owner_id)));

					if ((!isset($notifiInterest['EmailNotification']['email']) || $notifiInterest['EmailNotification']['email'] == 1) && (!isset($usersDetails['User']['email_notification']) || $usersDetails['User']['email_notification'] == 1)) {

						$projectDetails = ClassRegistry::init('Project')->find('first', array('conditions' => array('Project.id' => $project_id)));
						$loggedInemail = $this->Session->read('Auth.User.email');
						$name = $this->Session->read('Auth.User.UserDetail.full_name');

						$pageAction = SITEURL.'boards/opportunity/requests';

						$emailAddress = $usersDetails['User']['email'];
						$email = new CakeEmail();
						$email->config('Smtp');
						//$email->from(array($loggedInemail => $name));
						$email->from(array(ADMIN_FROM_EMAIL => SITENAME));
						$email->to($emailAddress);

						//$email->subject("Project Interest");
						$email->subject(SITENAME . ": Project interest");
						$email->template('project_interest');
						$email->emailFormat('html');
						$email->viewVars(array('Custname' => $usersDetails['UserDetail']['first_name'] . ' ' . $usersDetails['UserDetail']['last_name'], 'name' => $name, 'projectDetails' => $projectDetails, 'DefaultMessage' => $message, 'open_page'=>$pageAction));
						$email->send();

					}
				} // end userlist foreach
			} // end userlist if
			/* owner user loop end  */

					/************** socket messages **************/
					if (SOCKET_MESSAGES) {
						$current_user_id = $this->user_id;

						$send_notify = false;
						if (web_notify_setting($owner_user, 'project', 'project_interest_board')) {
							$send_notify = true;
						}

						$userDetail_soc = get_user_data($this->user_id);
						$content = [
							'notification' => [
								'type' => 'interest',
								'created_id' => $this->user_id,
								'project_id' => $project_id,
								'creator_name' => $userDetail_soc['UserDetail']['full_name'],
								'subject' => 'Project interest',
								'heading' => 'Project: ' . strip_tags(getFieldDetail('Project', $project_id, 'title')),
								'sub_heading' => '',
								'date_time' => timezoneTimeConvertor($this->user_id, date('Y-m-d g:iA'), $format = 'd M Y g:iA') . user_country($this->user_id),
							],
						];
						if ($send_notify) {
							$content['received_users'] = [$owner_user];
						}
						$response['content'] = $content;

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
					$response['success'] = true;
				}
				echo json_encode($response);
				exit;

			}
            $this->set('data', $data);
			$this->set('pid', $pid);
			$html = $this->render('partial/join_req');
			return $html;
			exit();

		}
	}

	public function get_opportunity(){
		if ($this->request->isAjax()) {
            $this->layout = false;

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				if( $post['type'] == 'request' ){

					$title = (isset($this->request->data['title']) && !empty($this->request->data['title'])) ? $this->request->data['title'] : "";
					$data['data'] = $this->find_opportunity_request($title);

					$view = new View($this, false);
					$view->viewPath = 'Boards/partial';
					$view->set($data);
					$html = $view->render('opportunity_request_list');

				} else {
					$title = (isset($this->request->data['title']) && !empty($this->request->data['title'])) ? $this->request->data['title'] : "";
					$data['data'] = $this->find_opportunity($title);

					$view = new View($this, false);
					$view->viewPath = 'Boards/partial';
					$view->set($data);
					$html = $view->render('opportunity_list');
				}

				echo json_encode($html);
				exit();
			}
		}
	}

	public function filter_data(){
		if ($this->request->isAjax()) {
            $this->layout = false;

			// $view = new View($this, false);

            if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				// pr($post, 1);
				$filter = (isset($post['q']) && strlen($post['q']) > 0 ) ? $post['q'] : '';

				$page = (isset($post['page']) && strlen($post['page']) > 0 ) ? $post['page'] : 0;

				$sorting = array();
				$sorting['order'] = 'asc';
				$sorting['coloumn'] = 'title';
				/* if( $post['type'] == 'request' ){
					$sorting['order'] = 'desc';
					$sorting['coloumn'] = 'p.start_date';
				} */
				if( isset($post['order']) && !empty($post['order']) ){
					$sorting['order'] = $post['order'];
				}
				if( isset($post['coloumn']) && !empty($post['coloumn']) ){
					$sorting['coloumn'] = $post['coloumn'];
				}

				//pr($sorting);

				if( $post['type'] == 'request' ){

					$data['data'] = $this->find_opportunity_request($filter, $page, $sorting);
					$this->set($data);
					$this->render('/Boards/partial/opportunity_request_list');

				} else {
					$data['data'] = $this->find_opportunity($filter, $page, $sorting);
					$this->set($data);
					$this->render('/Boards/partial/opportunity_list');
				}


			}


			// echo json_encode($html);
			// exit();
		}
	}

	public function count_opp(){
		if ($this->request->isAjax()) {
            $this->layout = false;

			$count = 0;
            if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				// pr($post, 1);
				$filter = (isset($post['search_text']) && strlen($post['search_text']) > 0 ) ? $post['search_text'] : '';

				$sorting = array();
				$sorting['order'] = 'asc';
				$sorting['coloumn'] = 'title';
				if( isset($post['order']) && !empty($post['order']) ){
					$sorting['order'] = $post['order'];
				}
				if( isset($post['coloumn']) && !empty($post['coloumn']) ){
					$sorting['coloumn'] = $post['coloumn'];
				}
				if( $post['type'] == 'request' ){
					$data = $this->find_opportunity_request($filter, '', $sorting, null, true);
					$count = count($data);
				} else {
					$data = $this->find_opportunity($filter, '', $sorting, null, true);
					$count = count($data);
				}

			}

			echo json_encode($count);
			exit();
		}
	}

	public function get_opportunity_row(){
		if ($this->request->isAjax()) {
            $this->layout = false;

			$view = new View($this, false);

            if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				$project_id = $post['project_id'];
				// pr($post, 1);

				$data['data'] = $this->find_opportunity(null, null, null, $project_id);
				$view->viewPath = 'Boards/partial';
				$view->set($data);
				$html = $view->render('opportunity_row');

			}

			echo json_encode($html);
			exit();
		}
	}

	protected function find_opportunity($filter = null, $page = null, $sorting = array(), $row = null, $count = false) {

		if(!$count){
			$limit_query = ' LIMIT '.$this->opp_offset;
			if(isset($page) && !empty($page)){
				$limit_query = " LIMIT $page, ".$this->opp_offset;
			}
		}
		else{
			$limit_query = '';
		}

		//echo $limit_query;

		$ser = '^';
		$filter_query = '';
		if(isset($filter) && !empty($filter)){

			$search_str= Sanitize::escape(like($filter, $ser ));

			$filter_query = " AND (p.title LIKE '%$search_str%' ESCAPE '$ser' OR p.description LIKE '%$search_str%' ESCAPE '$ser' OR p.objective LIKE '%$search_str%' ESCAPE '$ser') ";

		}

		$order = 'ORDER BY p.start_date DESC';
		if( isset($sorting) && !empty($sorting) ){

			if( isset($sorting['coloumn']) && !empty($sorting['coloumn']) && isset($sorting['order']) && !empty($sorting['order']) ){
				$order = "ORDER BY ".$sorting['coloumn']." ".$sorting['order'];
			}

		}

		$row_query = '';
		if( isset($row) && !empty($row) ){

			$row_query = " AND p.id = $row ";

		}

		$current_user_id = $this->user_id;


		$query = "SELECT
		    	po.project_id,
		    	ud.user_id,
		    	po.organization_id,
		    	p.title,
		    	p.start_date,
		    	p.end_date,
		    	p.color_code,
		    	(
		        	CASE
		         		WHEN(DATE(NOW()) BETWEEN DATE(p.start_date) AND DATE(p.end_date)) THEN 'progressing'
		            	WHEN(DATE(p.end_date) < DATE(NOW())) THEN 'overdue'
		            	WHEN(DATE(p.start_date) > DATE(NOW())) THEN 'not_started'
		            	ELSE 'not_set'
		            END
		        ) AS project_status,
		        wt.total_tasks,
		        wt.total_workspaces,
		        wt.total_owners,
		        wt.total_shares,
		        wt.users,
		        wt.total_people,
		        (
		            SELECT
		                ROUND(COUNT(us.skill_id)/COUNT(ps.skill_id)*100)
		            FROM
		                project_skills ps
		            LEFT JOIN user_skills us ON
		                us.user_id = $current_user_id #change dynamically
		                AND ps.skill_id = us.skill_id
		            WHERE
		                ps.project_id = po.project_id
		        ) AS skill_match_percent,
		        (
		            SELECT
		                ROUND(COUNT(us.subject_id)/COUNT(ps.subject_id)*100)
		            FROM
		                project_subjects ps
		            LEFT JOIN user_subjects us ON
		                us.user_id = $current_user_id #change dynamically
		                AND ps.subject_id = us.subject_id
		            WHERE
		                ps.project_id = po.project_id
		        ) AS subject_match_percent,
		        (
		            SELECT
		                ROUND(COUNT(ud.domain_id)/COUNT(pd.domain_id)*100)
		            FROM
		                project_domains pd
		            LEFT JOIN user_domains ud ON
		                ud.user_id = $current_user_id #change dynamically
		                AND pd.domain_id = ud.domain_id
		            WHERE
		                pd.project_id = po.project_id
		        ) AS domain_match_percent,
		        (
		            SELECT
		                COUNT(up.project_id)
		            FROM
		                user_permissions up
		            INNER JOIN projects p1 ON
		                up.project_id = p1.id
		            WHERE
		                (DATE(p1.start_date) BETWEEN DATE(p.start_date) AND DATE(p.end_date) OR DATE(p1.end_date) BETWEEN DATE(p.start_date) AND DATE(p.end_date))
		                AND up.workspace_id IS NULL AND up.user_id = ud.user_id
		        ) AS match_project_counts,
		            (
		            SELECT
		                COUNT(up.element_id)
		            FROM
		                user_permissions up
		            INNER JOIN elements e ON
		                up.element_id = e.id
		            WHERE
		                (DATE(e.start_date) BETWEEN DATE(p.start_date) AND DATE(p.end_date) OR DATE(e.end_date) BETWEEN DATE(p.start_date) AND DATE(p.end_date))
		                AND up.element_id IS NOT NULL AND up.user_id = ud.user_id
		        ) AS match_tasks_counts,
		        (
		            SELECT
		            	COUNT(a.id)
		            FROM
		            	availabilities a
		            WHERE
		            	a.user_id = ud.user_id
		            	AND (STR_TO_DATE(LEFT(avail_start_date, 10),'%Y-%m-%d') BETWEEN DATE(p.start_date) AND DATE(p.end_date) OR STR_TO_DATE(LEFT(a.avail_end_date, 10),'%Y-%m-%d') BETWEEN DATE(p.start_date) AND DATE(p.end_date))
		        ) AS unavailable_count,
		        (
		            SELECT
		            	COUNT(ub.id)
		            FROM
		            	user_blocks ub
		            WHERE
		            	ub.user_id = ud.user_id
		            	AND (DATE(ub.work_start_date) BETWEEN DATE(p.start_date) AND DATE(p.end_date) OR DATE(ub.work_end_date) BETWEEN DATE(p.start_date) AND DATE(p.end_date))
		        ) AS block_count
		    FROM
		    	project_opp_orgs po
		    INNER JOIN user_details ud ON
		        ud.user_id = $current_user_id #change dynamically
		        AND po.organization_id = ud.organization_id
		    INNER JOIN projects p ON
		    	po.project_id = p.id
		    	AND p.sign_off <> 1
		    LEFT JOIN
		    #get workspaces, tasks and team
		    (
		        SELECT
		            up.project_id,
		            SUM(
		                up.element_id AND up.area_id IS NOT NULL AND up.role = 'Creator'
		                ) AS total_tasks,
		            SUM(
		                up.workspace_id AND up.area_id IS NULL AND up.role = 'Creator'
		                ) AS total_workspaces,
		            SUM(
		                up.user_id AND up.role IN('Creator', 'Owner', 'Group Owner') AND up.workspace_id IS NULL
		                ) AS total_owners,
		            SUM(
		                up.user_id AND up.role IN('Sharer', 'Group Sharer') AND up.workspace_id IS NULL
		                ) AS total_shares,
		            GROUP_CONCAT(DISTINCT(up.user_id)) AS users,
		            SUM(
		                up.user_id AND up.role IN('Sharer','Group Sharer','Creator','Owner','Group Owner') AND up.workspace_id IS NULL
		                ) AS total_people
		        FROM
		            user_permissions up

		        GROUP BY up.project_id

		    ) AS wt ON
		        po.project_id = wt.project_id
				where ud.user_id = $current_user_id
				$filter_query
				$row_query
				$order

				$limit_query
				";

				    //pr($query);
		return $this->Project->query($query);

	}

	public function opportunity_count() {
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';

			$data = [];
			$count = 0;
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				// pr($post, 1);

				$filter = $post['q'];
				$coloumn = $post['coloumn'];
				$order = $post['order'];


				$ser = '^';
				$filter_query = '';
				if(isset($filter) && !empty($filter)){
					$search_str= Sanitize::escape(like($filter, $ser ));

					$filter_query = " AND (projects.title LIKE '%$search_str%' ESCAPE '$ser' OR projects.description LIKE '%$search_str%' ESCAPE '$ser' OR projects.objective LIKE '%$search_str%' ESCAPE '$ser') ";

				}

				$order_qry = 'ORDER BY projects.title ASC';
				if( isset($coloumn) && !empty($coloumn) ){

					if( isset($coloumn) && !empty($coloumn) && isset($order) && !empty($order) ){
						$order_qry = "ORDER BY ".$coloumn." ".$order;
					}

				}
				$current_user_id = $this->user_id;

			$query = "select poo.project_id,ud.user_id,ud.organization_id, projects.title, projects.start_date, projects.end_date , projects.color_code,

					(CASE
						WHEN (projects.sign_off=1) THEN 'completed'
						WHEN (DATE(NOW()) BETWEEN DATE(projects.start_date) AND DATE(projects.end_date)) THEN 'progressing'
						WHEN (DATE(projects.end_date) < DATE(NOW())) THEN 'overdue'
						WHEN (DATE(projects.start_date) > DATE(NOW())) THEN 'not_started'
						ELSE 'not_set'
					END) AS project_status,

					sum(up.element_id and up.area_id is not null and  up.role = 'Creator') AS total_tasks,
					sum(up.workspace_id and up.area_id is null and  up.role = 'Creator')  AS total_workspaces,
					sum(up.user_id and up.role in('Creator','Owner','Group Owner') and up.workspace_id is null)  AS total_owners,
					sum(up.user_id and up.role in('Sharer','Group Sharer') and up.workspace_id is null)  AS total_shares,
					sum(up.user_id and up.role in('Sharer','Group Sharer','Creator','Owner','Group Owner') and up.workspace_id is null)  AS total_people,

					round(( (SELECT COUNT(a.skill_id) skill_match
					FROM project_skills a, user_skills b
					WHERE a.skill_id = b.skill_id AND b.user_id = ud.user_id and a.project_id = poo.project_id) / ( select count(psi.skill_id) from project_skills psi where psi.project_id = poo.project_id ) * 100 )) AS skill_match_percent,


					round(( (SELECT COUNT(pas.domain_id) domain_matchs
					FROM project_domains pas, user_domains pus
					WHERE pas.domain_id = pus.domain_id AND pus.user_id = ud.user_id and pas.project_id = poo.project_id) /  (select count(pds.domain_id) from project_domains pds where pds.project_id = poo.project_id  ) * 100 )) AS domain_match_percent,


					round(( (SELECT COUNT(ass.subject_id) subject_matchs
					FROM project_subjects ass, user_subjects us
					WHERE ass.subject_id = us.subject_id AND us.user_id = ud.user_id and ass.project_id = poo.project_id)/  (select   count(pss.subject_id) from project_subjects pss where pss.project_id = poo.project_id      ) * 100 )) AS subject_match_percent,


					(select count(upcurrent.project_id) from user_permissions upcurrent inner join projects psc on psc.id = upcurrent.project_id
					where (DATE(psc.start_date) BETWEEN DATE(projects.start_date) AND DATE(projects.end_date)   OR   DATE(psc.end_date) BETWEEN DATE(projects.end_date) AND DATE(projects.end_date)) and upcurrent.workspace_id is null and upcurrent.user_id = ud.user_id) as match_project_counts,

					(select count(upcurrent.element_id) from user_permissions upcurrent inner join elements psc on psc.id = upcurrent.element_id
					where (DATE(psc.start_date) BETWEEN DATE(projects.start_date) AND DATE(projects.end_date)   OR   DATE(psc.end_date) BETWEEN DATE(projects.end_date) AND DATE(projects.end_date)) and upcurrent.element_id is not null and upcurrent.user_id = ud.user_id) as match_tasks_counts,


					(SELECT
						# user_id,
						SUM(days) tdays
					FROM
						(
						SELECT
							user_id,
							STR_TO_DATE(LEFT(avail_start_date,10),'%Y-%m-%d') avail_start_date,
							STR_TO_DATE(LEFT(avail_end_date,10),'%Y-%m-%d') avail_end_date,
							CASE WHEN @id != user_id THEN @id := user_id +0 *(@ed := DATE('1970-1-1'))
							END id,
							CASE WHEN STR_TO_DATE(LEFT(avail_start_date,10),'%Y-%m-%d') < @ed THEN CASE WHEN STR_TO_DATE(LEFT(avail_end_date,10),'%Y-%m-%d') > @ed THEN DATEDIFF(STR_TO_DATE(LEFT(avail_end_date,10),'%Y-%m-%d'), @ed) ELSE 0
							END ELSE DATEDIFF(STR_TO_DATE(LEFT(avail_end_date,10),'%Y-%m-%d'), STR_TO_DATE(LEFT(avail_start_date,10),'%Y-%m-%d'))
							END days,
							@ed := CASE WHEN STR_TO_DATE(LEFT(avail_end_date,10),'%Y-%m-%d') > @ed THEN STR_TO_DATE(LEFT(avail_end_date,10),'%Y-%m-%d') ELSE @ed
							END enddt
					FROM
						availabilities, (SELECT @id := 0) const
					ORDER BY
						user_id,
						avail_start_date
					) t
					where t.user_id = ud.user_id and
					(

					t.avail_start_date between DATE(projects.start_date) and DATE(projects.end_date) or t.avail_end_date between DATE(projects.start_date) and DATE(projects.end_date)

					)  GROUP BY
						user_id)  as unvailable_days



					from project_opp_orgs poo
					inner JOIN user_details ud on
					ud.organization_id = poo.organization_id

					INNER join projects on poo.project_id = projects.id
					inner join user_permissions up on up.project_id = poo.project_id

					where poo.project_id not in (select project_id from user_permissions where user_id = $current_user_id and workspace_id is null) and ud.user_id = $current_user_id AND projects.sign_off != 1

					$filter_query

					GROUP by up.project_id
					$order
				";
				$dataAll = $this->Project->query($query);
				if (isset($dataAll) && !empty($dataAll)) {
					$data = $dataAll;
				}
				$count = count($data);
			}

			echo json_encode($count);
			exit;
		}
	}

	public function opportunity_paging_data(){
		if ($this->request->isAjax()) {
            $this->layout = false;

			$view = new View($this, false);

            if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				$page = $post['page'];

				$sorting = array();
				$sorting['order'] = 'asc';
				$sorting['coloumn'] = 'title';
				$q = '';

				if( isset($post['order']) && !empty($post['order']) ){
					$sorting['order'] = $post['order'];
				}
				if( isset($post['coloumn']) && !empty($post['coloumn']) ){
					$sorting['coloumn'] = $post['coloumn'];
				}

				if( isset($post['q']) && !empty($post['q']) ){
					$q = $post['q'];
				}
				$data['data'] = $this->find_opportunity($q, $page, $sorting);
				$view->viewPath = 'Boards/partial';
				$view->set($data);
				$html = $view->render('opportunity_list');

			}

			echo json_encode($html);
			exit();
		}
	}

	/* OPPORTUNITY */

	/* OPPORTUNITY REQUEST */

	public function filter_request_data(){
		if ($this->request->isAjax()) {
            $this->layout = false;

			// $view = new View($this, false);

            if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				// pr($post, 1);
				$filter = (isset($post['q']) && strlen($post['q']) > 0 ) ? $post['q'] : '';

				$page = (isset($post['page']) && strlen($post['page']) > 0 ) ? $post['page'] : 0;

				$sorting = array();
				$sorting['order'] = 'desc';
				$sorting['coloumn'] = 'p.start_date';
				if( isset($post['order']) && !empty($post['order']) ){
					$sorting['order'] = $post['order'];
				}
				if( isset($post['coloumn']) && !empty($post['coloumn']) ){
					$sorting['coloumn'] = $post['coloumn'];
				}


				$data['data'] = $this->find_opportunity_request($filter, $page, $sorting);
				$this->set($data);

			}
			$this->render('/Boards/partial/opportunity_request_list');

		}
	}


	protected function find_opportunity_request($filter = null, $page = null, $sorting = array(), $row = null, $count = false) {

		if(!$count){
			$limit_query = ' LIMIT '.$this->opp_offset;
			if(isset($page) && !empty($page)){
				$limit_query = " LIMIT $page, ".$this->opp_offset;
			}
		}
		else{
			$limit_query = '';
		}

		$ser = '^';
		$filter_query = '';
		if(isset($filter) && !empty($filter)){

			$search_str= Sanitize::escape(like($filter, $ser ));

			$filter_query = " AND (p.title LIKE '%$search_str%' ESCAPE '$ser') ";

		}

		$order = 'ORDER BY p.start_date DESC';
		if( isset($sorting) && !empty($sorting) ){

			if( isset($sorting['coloumn']) && !empty($sorting['coloumn']) && isset($sorting['order']) && !empty($sorting['order']) ){
				$order = "ORDER BY ".$sorting['coloumn']." ".$sorting['order'];
			}

		}

		$row_query = '';
		if( isset($row) && !empty($row) ){

			$row_query = " AND projects.id = $row ";

		}

		$current_user_id = $this->user_id;

		$query = "SELECT
				po.project_id,
				ud.user_id,
				po.organization_id,
				p.title,
				p.start_date,
				p.end_date,
				p.color_code,
				(
					CASE
						WHEN(DATE(NOW()) BETWEEN DATE(p.start_date) AND DATE(p.end_date)) THEN 'progressing'
						WHEN(DATE(p.end_date) < DATE(NOW())) THEN 'overdue'
						WHEN(DATE(p.start_date) > DATE(NOW())) THEN 'not_started'
						ELSE 'not_set'
					END
				) AS project_status,
				wt.total_owners,
				wt.total_shares,
				wt.users,
				wt.total_people,
				/*(
					SELECT
						ROUND(COUNT(us.skill_id)/COUNT(ps.skill_id)*100)
					FROM
						project_skills ps
					LEFT JOIN user_skills us ON
						us.user_id = $current_user_id #change dynamically
						AND ps.skill_id = us.skill_id
					WHERE
						ps.project_id = po.project_id
				) AS skill_match_percent,
				(
					SELECT
						ROUND(COUNT(us.subject_id)/COUNT(ps.subject_id)*100)
					FROM
						project_subjects ps
					LEFT JOIN user_subjects us ON
						us.user_id = $current_user_id #change dynamically
						AND ps.subject_id = us.subject_id
					WHERE
						ps.project_id = po.project_id
				) AS subject_match_percent,
				(
					SELECT
						ROUND(COUNT(ud.domain_id)/COUNT(pd.domain_id)*100)
					FROM
						project_domains pd
					LEFT JOIN user_domains ud ON
						ud.user_id = $current_user_id #change dynamically
						AND pd.domain_id = ud.domain_id
					WHERE
						pd.project_id = po.project_id
				) AS domain_match_percent,
				*/
				(
					SELECT
						round(( (SELECT COUNT(DISTINCT(a.skill_id)) skill_match
						FROM project_skills a, user_skills b
						WHERE a.skill_id = b.skill_id AND a.project_id = up.project_id AND
						b.user_id IN(SELECT user_permissions.user_id FROM user_permissions WHERE user_permissions.project_id = up.project_id AND user_permissions.workspace_id IS NULL )
						) / ( select count(psi.skill_id) from project_skills psi where psi.project_id = up.project_id ) * 100 )) AS skill_matches

						FROM user_permissions up
						INNER JOIN projects
						ON up.project_id = projects.id

						Where projects.id = po.project_id
						GROUP BY projects.id
				) AS skill_match_percent,
				(
					SELECT
						round(( (SELECT COUNT(DISTINCT(a.subject_id)) skill_match
						FROM project_subjects a, user_subjects b
						WHERE a.subject_id = b.subject_id AND a.project_id = up.project_id AND
						b.user_id IN(SELECT user_permissions.user_id FROM user_permissions WHERE user_permissions.project_id = up.project_id AND user_permissions.workspace_id IS NULL )
						) / ( select count(psi.subject_id) from project_subjects psi where psi.project_id = up.project_id ) * 100 )) AS subject_matches

					FROM user_permissions up
					INNER JOIN projects
					ON up.project_id = projects.id

					Where projects.id = po.project_id
					GROUP BY projects.id
				) AS subject_match_percent,
				(
					SELECT
						round(( (SELECT COUNT(DISTINCT(a.domain_id)) skill_match
						FROM project_domains a, user_domains b
						WHERE a.domain_id = b.domain_id AND a.project_id = up.project_id AND
						b.user_id IN(SELECT user_permissions.user_id FROM user_permissions WHERE user_permissions.project_id = up.project_id AND user_permissions.workspace_id IS NULL )
						) / ( select count(psi.domain_id) from project_domains psi where psi.project_id = up.project_id ) * 100 )) AS domain_matches

					FROM user_permissions up
					INNER JOIN projects
					ON up.project_id = projects.id

					Where projects.id = po.project_id
					GROUP BY projects.id
				) AS domain_match_percent,

				(
					SELECT
						COUNT(up.project_id)
					FROM
						user_permissions up
					INNER JOIN projects p1 ON
						up.project_id = p1.id
					WHERE
						(DATE(p1.start_date) BETWEEN DATE(p.start_date) AND DATE(p.end_date) OR DATE(p1.end_date) BETWEEN DATE(p.start_date) AND DATE(p.end_date))
						AND up.workspace_id IS NULL AND up.user_id = ud.user_id
				) AS match_project_counts,

				( select count(id) from project_boards pbs where pbs.project_id = po.project_id and pbs.project_status = 1 ) as accept_request_count,

				( select count(id) from project_boards pbs where pbs.project_id = po.project_id and pbs.project_status = 0 ) as pending_request_count,

				( select count(id) from project_boards pbs where pbs.project_id = po.project_id and pbs.project_status = 2 ) as decline_request_count,


				(SELECT count(*) as counts FROM project_skills  where  project_skills.project_id = po.project_id ) as total_skills,

				(SELECT count(*) as counts FROM project_subjects  where  project_subjects.project_id = po.project_id ) as total_subjects,

				(SELECT count(*) as counts FROM project_domains  where  project_domains.project_id = po.project_id ) as total_domains,

				/*(SELECT COUNT(a.skill_id) FROM project_skills a, user_skills b
				WHERE a.skill_id = b.skill_id AND b.user_id = ud.user_id and a.project_id = po.project_id) AS skill_count,*/

				(
					SELECT (SELECT COUNT(DISTINCT(a.skill_id)) skill_match
					FROM project_skills a, user_skills b
					WHERE a.skill_id = b.skill_id AND a.project_id = up.project_id AND
					b.user_id IN(SELECT user_permissions.user_id FROM user_permissions WHERE user_permissions.project_id = up.project_id AND user_permissions.workspace_id IS NULL )) AS ss

					FROM user_permissions up
					INNER JOIN projects
					ON up.project_id = projects.id

					Where projects.id = po.project_id
					GROUP BY projects.id
				) AS skill_count,

				(
					SELECT (SELECT COUNT(DISTINCT(a.domain_id)) skill_match
					FROM project_domains a, user_domains b
					WHERE a.domain_id = b.domain_id AND a.project_id = up.project_id AND
					b.user_id IN(SELECT user_permissions.user_id FROM user_permissions WHERE user_permissions.project_id = up.project_id AND user_permissions.workspace_id IS NULL )) AS ss

					FROM user_permissions up
					INNER JOIN projects
					ON up.project_id = projects.id

					Where projects.id = po.project_id
					GROUP BY projects.id
				) AS domain_count,

				(
					SELECT (SELECT COUNT(DISTINCT(a.subject_id)) skill_match
					FROM project_subjects a, user_subjects b
					WHERE a.subject_id = b.subject_id AND a.project_id = up.project_id AND
					b.user_id IN(SELECT user_permissions.user_id FROM user_permissions WHERE user_permissions.project_id = up.project_id AND user_permissions.workspace_id IS NULL )) AS ss

					FROM user_permissions up
					INNER JOIN projects
					ON up.project_id = projects.id

					Where projects.id = po.project_id
					GROUP BY projects.id
				) AS subject_count

				/*(SELECT COUNT(pas.domain_id) FROM project_domains pas, user_domains pus
				WHERE pas.domain_id = pus.domain_id AND pus.user_id = ud.user_id and pas.project_id = po.project_id) AS domain_count,

				(SELECT COUNT(ass.subject_id) FROM project_subjects ass, user_subjects us
				WHERE ass.subject_id = us.subject_id AND us.user_id = ud.user_id and ass.project_id = po.project_id) AS subject_count */


			FROM
				project_opp_orgs po
			LEFT JOIN user_permissions upo ON
				upo.project_id = po.project_id and upo.workspace_id is null and upo.user_id = $current_user_id
			INNER JOIN user_details ud ON
				ud.user_id = $current_user_id #change dynamically
				AND po.organization_id = ud.organization_id
			INNER JOIN projects p ON
				po.project_id = p.id
				AND p.sign_off <> 1
			LEFT JOIN
			#get workspaces, tasks and team
			(
				SELECT
					up.project_id,
					SUM(
						up.user_id AND up.role IN('Creator', 'Owner', 'Group Owner') AND up.workspace_id IS NULL
						) AS total_owners,
					SUM(
						up.user_id AND up.role IN('Sharer', 'Group Sharer') AND up.workspace_id IS NULL
						) AS total_shares,
					GROUP_CONCAT(DISTINCT(up.user_id)) AS users,
					SUM(
						up.user_id AND up.role IN('Sharer','Group Sharer','Creator','Owner','Group Owner') AND up.workspace_id IS NULL
						) AS total_people
				FROM
					user_permissions up
				GROUP BY up.project_id

			) AS wt ON
				po.project_id = wt.project_id
				where ud.user_id = $current_user_id and upo.role IN('Creator', 'Owner', 'Group Owner')

				$filter_query
				$row_query

				GROUP BY po.project_id

				$order

				$limit_query

			";

			// pr($query);

				//$limit_query
				//  echo $query;
		return $this->Project->query($query);

	}

	public function request_user_data(){

		if ($this->request->isAjax()) {
            $this->layout = false;
			$view = new View($this, false);

            if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				$project_id = $post['project_id'];
				$sender = null;
				if( isset($post['sender']) && !empty($post['sender']) ){
					$sender = "  AND pb.sender = ".$post['sender']." ";
				}

				$query = "SELECT

				pbs.project_id,
				pbs.sender,
				pbs.start_date,
				pbs.end_date,
				pbs.project_status,
				pbs.board_msg,
				pbs.created,
				pbs.updated,
				pbs.first_name,
				pbs.last_name,
				pbs.profile_pic,
				pbs.job_role,
				pbs.job_title,
				pbs.reasons,
				pbs.receiver,
				pbs.organization_id,
				pbs.receiver_id,
				pbs.pbs_id,
				pbs.responsed_by,
				pbs.reason_id,

				( select concat(rcvd.first_name,' ',rcvd.last_name) approver from user_details rcvd where rcvd.user_id = pbs.responsed_by ) as response_by,

				( select concat(recvd.first_name,' ',recvd.last_name) approver from user_details recvd where recvd.user_id = pbs.responsed_by ) as declined_by,

				round(( (SELECT COUNT(a.skill_id) skill_match
					FROM project_skills a, user_skills b
					WHERE a.skill_id = b.skill_id AND b.user_id = pbs.sender and a.project_id = pbs.project_id) / ( select count(psi.skill_id) from project_skills psi where psi.project_id = pbs.project_id ) * 100 )) AS skill_match_percent,

				round(( (SELECT COUNT(a.subject_id) subject_match
					FROM project_subjects a, user_subjects b
					WHERE a.subject_id = b.subject_id AND b.user_id = pbs.sender and a.project_id = pbs.project_id) / ( select count(psi.subject_id) from project_subjects psi where psi.project_id = pbs.project_id ) * 100 )) AS subject_match_percent,


				round(( (SELECT COUNT(a.domain_id) domain_match
					FROM project_domains a, user_domains b
					WHERE a.domain_id = b.domain_id AND b.user_id = pbs.sender and a.project_id = pbs.project_id) / ( select count(psi.domain_id) from project_domains psi where psi.project_id = pbs.project_id ) * 100 )) AS domain_match_percent,

				(SELECT
						COUNT(up.project_id)
					FROM
						user_permissions up
					INNER JOIN projects p1 ON
						up.project_id = p1.id
					WHERE
						(DATE(p1.start_date) BETWEEN DATE(pbs.start_date) AND DATE(pbs.end_date) OR DATE(p1.end_date) BETWEEN DATE(pbs.start_date) AND DATE(pbs.end_date))
						AND up.workspace_id IS NULL AND up.user_id = pbs.sender
				) AS match_project_counts,
				(
					SELECT
						COUNT(up.element_id)
					FROM
						user_permissions up
					INNER JOIN elements e ON
						up.element_id = e.id
					WHERE
						(DATE(e.start_date) BETWEEN DATE(pbs.start_date) AND DATE(pbs.end_date) OR DATE(e.end_date) BETWEEN DATE(pbs.start_date) AND DATE(pbs.end_date))
						AND up.element_id IS NOT NULL AND up.user_id = pbs.sender
				) AS match_tasks_counts,
				(
					SELECT
						COUNT(a.id)
					FROM
						availabilities a
					WHERE
						a.user_id = pbs.sender
						AND (STR_TO_DATE(LEFT(avail_start_date, 10),'%Y-%m-%d') BETWEEN DATE(pbs.start_date) AND DATE(pbs.end_date) OR STR_TO_DATE(LEFT(a.avail_end_date, 10),'%Y-%m-%d') BETWEEN DATE(pbs.start_date) AND DATE(pbs.end_date))
				) AS unavailable_count,
				(
					SELECT
						COUNT(ub.id)
					FROM
						user_blocks ub
					WHERE
						ub.user_id = pbs.sender
						AND (DATE(ub.work_start_date) BETWEEN DATE(pbs.start_date) AND DATE(pbs.end_date) OR DATE(ub.work_end_date) BETWEEN DATE(pbs.start_date) AND DATE(pbs.end_date))
				) AS block_count


			FROM(
				SELECT

					pb.project_id,
					pb.sender,
					p.start_date,
					p.end_date,
					pb.project_status,
					pb.board_msg,
					pb.created,
					pb.updated,
					ud.first_name,
					ud.last_name,
					ud.profile_pic,
					ud.job_role,
					ud.job_title,
					dr.reasons,
					pb.receiver,
					ud.organization_id,
					br.receiver_id,
					pb.id pbs_id,
					pb.responsed_by,
					dr.id reason_id

				FROM project_boards pb

				LEFT JOIN user_details ud ON
					ud.user_id = pb.sender
				LEFT JOIN projects p ON
					pb.project_id = p.id
				LEFT JOIN board_responses br On
					br.project_id = pb.project_id and br.sender_id = pb.sender
				LEFT JOIN decline_reasons dr On
					br.reason = dr.id

				WHERE pb.project_id = ".$project_id." $sender
				GROUP BY pb.sender
			) AS pbs
			GROUP BY pbs.sender, pbs.project_id

			";
			//pr($query); die;
			$data['data'] =  $this->Project->query($query);

			}
			$project_id = $project_id;
			$view->viewPath = 'Boards/partial';
			$view->set($data);
			$view->set('project_id', $project_id);
			$html = $view->render('request_user_data');

			echo json_encode($html);
			exit();

		}

	}

	public function get_project_data($project = null, $filter = null, $page = null, $sorting = array(), $row = null, $count = false) {

		if ($this->request->isAjax()) {
            $this->layout = false;
			$view = new View($this, false);
            if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;
				$project_id = $post['project_id'];
				$sender = null;
				if( isset($post['sender']) && !empty($post['sender']) ){
					$sender = "  AND pb.sender = ".$post['sender']." ";
				}

				$sorting = array();
				$sorting['order'] = 'desc';
				$sorting['coloumn'] = 'created';
				if( isset($post['order']) && !empty($post['order']) ){
					$sorting['order'] = $post['order'];
				}
				if( isset($post['coloumn']) && !empty($post['coloumn']) ){
					$sorting['coloumn'] = $post['coloumn'];
				}

				if( isset($sorting) && !empty($sorting) ){

					if( isset($sorting['coloumn']) && !empty($sorting['coloumn']) && isset($sorting['order']) && !empty($sorting['order']) ){
						$order = "ORDER BY ".$sorting['coloumn']." ".$sorting['order'];
					}
				}

		 $query = "SELECT

				pbs.project_id,
				pbs.sender,
				pbs.start_date,
				pbs.end_date,
				pbs.project_status,
				pbs.board_msg,
				pbs.created,
				pbs.updated,
				pbs.first_name,
				pbs.last_name,
				pbs.profile_pic,
				pbs.job_role,
				pbs.job_title,
				pbs.reasons,
				pbs.receiver,
				pbs.organization_id,
				pbs.receiver_id,
				pbs.pbs_id,
				pbs.responsed_by,
				pbs.reason_id,

				( select concat(rcvd.first_name,' ',rcvd.last_name) approver from user_details rcvd where rcvd.user_id = pbs.responsed_by ) as response_by,

				( select concat(recvd.first_name,' ',recvd.last_name) approver from user_details recvd where recvd.user_id = pbs.responsed_by ) as declined_by,

				round(( (SELECT COUNT(a.skill_id) skill_match
					FROM project_skills a, user_skills b
					WHERE a.skill_id = b.skill_id AND b.user_id = pbs.sender and a.project_id = pbs.project_id) / ( select count(psi.skill_id) from project_skills psi where psi.project_id = pbs.project_id ) * 100 )) AS skill_match_percent,

				round(( (SELECT COUNT(a.subject_id) subject_match
					FROM project_subjects a, user_subjects b
					WHERE a.subject_id = b.subject_id AND b.user_id = pbs.sender and a.project_id = pbs.project_id) / ( select count(psi.subject_id) from project_subjects psi where psi.project_id = pbs.project_id ) * 100 )) AS subject_match_percent,


				round(( (SELECT COUNT(a.domain_id) domain_match
					FROM project_domains a, user_domains b
					WHERE a.domain_id = b.domain_id AND b.user_id = pbs.sender and a.project_id = pbs.project_id) / ( select count(psi.domain_id) from project_domains psi where psi.project_id = pbs.project_id ) * 100 )) AS domain_match_percent,

				(SELECT
						COUNT(up.project_id)
					FROM
						user_permissions up
					INNER JOIN projects p1 ON
						up.project_id = p1.id
					WHERE
						(DATE(p1.start_date) BETWEEN DATE(pbs.start_date) AND DATE(pbs.end_date) OR DATE(p1.end_date) BETWEEN DATE(pbs.start_date) AND DATE(pbs.end_date))
						AND up.workspace_id IS NULL AND up.user_id = pbs.sender
				) AS match_project_counts,
				(
					SELECT
						COUNT(up.element_id)
					FROM
						user_permissions up
					INNER JOIN elements e ON
						up.element_id = e.id
					WHERE
						(DATE(e.start_date) BETWEEN DATE(pbs.start_date) AND DATE(pbs.end_date) OR DATE(e.end_date) BETWEEN DATE(pbs.start_date) AND DATE(pbs.end_date))
						AND up.element_id IS NOT NULL AND up.user_id = pbs.sender
				) AS match_tasks_counts,
				(
					SELECT
						COUNT(a.id)
					FROM
						availabilities a
					WHERE
						a.user_id = pbs.sender
						AND (STR_TO_DATE(LEFT(avail_start_date, 10),'%Y-%m-%d') BETWEEN DATE(pbs.start_date) AND DATE(pbs.end_date) OR STR_TO_DATE(LEFT(a.avail_end_date, 10),'%Y-%m-%d') BETWEEN DATE(pbs.start_date) AND DATE(pbs.end_date))
				) AS unavailable_count,
				(
					SELECT
						COUNT(ub.id)
					FROM
						user_blocks ub
					WHERE
						ub.user_id = pbs.sender
						AND (DATE(ub.work_start_date) BETWEEN DATE(pbs.start_date) AND DATE(pbs.end_date) OR DATE(ub.work_end_date) BETWEEN DATE(pbs.start_date) AND DATE(pbs.end_date))
				) AS block_count


			FROM(
				SELECT

					pb.project_id,
					pb.sender,
					p.start_date,
					p.end_date,
					pb.project_status,
					pb.board_msg,
					pb.created,
					pb.updated,
					ud.first_name,
					ud.last_name,
					ud.profile_pic,
					ud.job_role,
					ud.job_title,
					dr.reasons,
					pb.receiver,
					ud.organization_id,
					br.receiver_id,
					pb.id pbs_id,
					pb.responsed_by,
					dr.id reason_id

				FROM project_boards pb

				LEFT JOIN user_details ud ON
					ud.user_id = pb.sender
				LEFT JOIN projects p ON
					pb.project_id = p.id
				LEFT JOIN board_responses br On
					br.project_id = pb.project_id and br.sender_id = pb.sender
				LEFT JOIN decline_reasons dr On
					br.reason = dr.id

				WHERE pb.project_id = ".$project_id."
				GROUP BY pb.sender
			) AS pbs
			GROUP BY pbs.sender, pbs.project_id
			$order
			";
			//pr($query);
			$data['data'] =  $this->Project->query($query);

			}
			$project_id = $project_id;
			$view->viewPath = 'Boards/partial';
			$view->set($data);
			$view->set('project_id', $project_id);
			$html = $view->render('get_project_data');

			echo json_encode($html);
			exit();
		}
	}


	public function count_request_project_user($project = null,$filter = null, $page = null, $sorting = array(), $row = null, $count = false) {

		if ($this->request->isAjax()) {
            $this->layout = false;
			$view = new View($this, false);
            if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;
				$project_id = $post['project_id'];
				//$project_id = 249;

				$sorting = array();
				$sorting['order'] = 'desc';
				$sorting['coloumn'] = 'created';
				if( isset($post['order']) && !empty($post['order']) ){
					$sorting['order'] = $post['order'];
				}
				if( isset($post['coloumn']) && !empty($post['coloumn']) ){
					$sorting['coloumn'] = $post['coloumn'];
				}
				if( isset($sorting) && !empty($sorting) ){

					if( isset($sorting['coloumn']) && !empty($sorting['coloumn']) && isset($sorting['order']) && !empty($sorting['order']) ){
						$order = "ORDER BY ".$sorting['coloumn']." ".$sorting['order'];
					}
				}

		 $query = "SELECT

				pbs.project_id,
				pbs.sender,
				pbs.start_date,
				pbs.end_date,
				pbs.project_status,
				pbs.board_msg,
				pbs.created,
				pbs.updated,
				pbs.first_name,
				pbs.last_name,
				pbs.profile_pic,
				pbs.job_role,
				pbs.job_title,
				pbs.reasons,
				pbs.receiver,
				pbs.organization_id,
				pbs.receiver_id,
				pbs.pbs_id,
				pbs.responsed_by,
				pbs.reason_id,

				( select concat(rcvd.first_name,' ',rcvd.last_name) approver from user_details rcvd where rcvd.user_id = pbs.responsed_by ) as response_by,

				( select concat(recvd.first_name,' ',recvd.last_name) approver from user_details recvd where recvd.user_id = pbs.responsed_by ) as declined_by,

				round(( (SELECT COUNT(a.skill_id) skill_match
					FROM project_skills a, user_skills b
					WHERE a.skill_id = b.skill_id AND b.user_id = pbs.sender and a.project_id = pbs.project_id) / ( select count(psi.skill_id) from project_skills psi where psi.project_id = pbs.project_id ) * 100 )) AS skill_match_percent,

				round(( (SELECT COUNT(a.subject_id) subject_match
					FROM project_subjects a, user_subjects b
					WHERE a.subject_id = b.subject_id AND b.user_id = pbs.sender and a.project_id = pbs.project_id) / ( select count(psi.subject_id) from project_subjects psi where psi.project_id = pbs.project_id ) * 100 )) AS subject_match_percent,


				round(( (SELECT COUNT(a.domain_id) domain_match
					FROM project_domains a, user_domains b
					WHERE a.domain_id = b.domain_id AND b.user_id = pbs.sender and a.project_id = pbs.project_id) / ( select count(psi.domain_id) from project_domains psi where psi.project_id = pbs.project_id ) * 100 )) AS domain_match_percent,

				(SELECT
						COUNT(up.project_id)
					FROM
						user_permissions up
					INNER JOIN projects p1 ON
						up.project_id = p1.id
					WHERE
						(DATE(p1.start_date) BETWEEN DATE(pbs.start_date) AND DATE(pbs.end_date) OR DATE(p1.end_date) BETWEEN DATE(pbs.start_date) AND DATE(pbs.end_date))
						AND up.workspace_id IS NULL AND up.user_id = pbs.sender
				) AS match_project_counts,
				(
					SELECT
						COUNT(up.element_id)
					FROM
						user_permissions up
					INNER JOIN elements e ON
						up.element_id = e.id
					WHERE
						(DATE(e.start_date) BETWEEN DATE(pbs.start_date) AND DATE(pbs.end_date) OR DATE(e.end_date) BETWEEN DATE(pbs.start_date) AND DATE(pbs.end_date))
						AND up.element_id IS NOT NULL AND up.user_id = pbs.sender
				) AS match_tasks_counts,
				(
					SELECT
						COUNT(a.id)
					FROM
						availabilities a
					WHERE
						a.user_id = pbs.sender
						AND (STR_TO_DATE(LEFT(avail_start_date, 10),'%Y-%m-%d') BETWEEN DATE(pbs.start_date) AND DATE(pbs.end_date) OR STR_TO_DATE(LEFT(a.avail_end_date, 10),'%Y-%m-%d') BETWEEN DATE(pbs.start_date) AND DATE(pbs.end_date))
				) AS unavailable_count,
				(
					SELECT
						COUNT(ub.id)
					FROM
						user_blocks ub
					WHERE
						ub.user_id = pbs.sender
						AND (DATE(ub.work_start_date) BETWEEN DATE(pbs.start_date) AND DATE(pbs.end_date) OR DATE(ub.work_end_date) BETWEEN DATE(pbs.start_date) AND DATE(pbs.end_date))
				) AS block_count


			FROM(
				SELECT

					pb.project_id,
					pb.sender,
					p.start_date,
					p.end_date,
					pb.project_status,
					pb.board_msg,
					pb.created,
					pb.updated,
					ud.first_name,
					ud.last_name,
					ud.profile_pic,
					ud.job_role,
					ud.job_title,
					dr.reasons,
					pb.receiver,
					ud.organization_id,
					br.receiver_id,
					pb.id pbs_id,
					pb.responsed_by,
					dr.id reason_id

				FROM project_boards pb

				LEFT JOIN user_details ud ON
					ud.user_id = pb.sender
				LEFT JOIN projects p ON
					pb.project_id = p.id
				LEFT JOIN board_responses br On
					br.project_id = pb.project_id and br.sender_id = pb.sender
				LEFT JOIN decline_reasons dr On
					br.reason = dr.id

				WHERE pb.project_id = ".$project_id."
				GROUP BY pb.sender
			) AS pbs
			GROUP BY pbs.sender, pbs.project_id
			$order
			";

			$data =  $this->Project->query($query);

			}

			$total_record = count($data);

			echo json_encode($total_record);
			exit();
		}
	}

	public function update_activity(){
		$response = [
			'success' => false,
			'permission' => null,
		];
		if ($this->request->isAjax()) {
            $this->layout = false;
			$view = new View($this, false);
            if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;
				$project_id = $post['project_id'];
				$board_id = $post['board_id'];
				$sender_id = $post['sender_id'];


				$view = new View();
				$viewModel = $view->loadHelper('Permission');

				if( isset($project_id) && isset($board_id) && !empty($project_id) && !empty($board_id) && isset($sender_id) && !empty($sender_id) ){


					$user_project_id = project_upid($project_id);
					$creator = $viewModel->projectCreator($project_id);

					$current_user_id = $this->user_id;
					$ppdata = array(
									'user_id'=> $sender_id,
									'share_by_id'=> $current_user_id,
									'owner_id'=> $creator[0]['user_details']['user_id'],
									'user_project_id'=> $user_project_id,
									'project_level'=> 0
									);

					if( $this->ProjectPermission->save($ppdata) ){

						$permission_id = $this->ProjectPermission->getLastInsertId();
						$response['permission'] = $permission_id;

						$this->ProjectBoard->save(
							array(
								'id'=>$board_id,
								'project_id' => $project_id,
								'responsed_by' => $this->Session->read('Auth.User.id'),
								'project_status'=>1
							)
						);

						// SAVE TO ACTIVITY
						$opp_data = [
							'project_id' => $project_id,
							'updated_user_id' => $this->user_id,
							'message' => 'Project opportunity request accepted',
							'updated' => date("Y-m-d H:i:s"),
						];
						$this->loadModel('ProjectActivity');
						$this->ProjectActivity->id = null;
						if( $this->ProjectActivity->save($opp_data)){
							$response['success'] = true;
						}

					}
				}
			}
		}
		echo json_encode($response);
		exit();
	}

	/* OPPORTUNITY REQUEST */


}
