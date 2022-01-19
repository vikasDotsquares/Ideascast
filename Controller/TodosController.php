<?php
App::uses('Controller/Component', 'Auth', 'Session', 'RequestHandler');
App::uses('CakeEmail', 'Network/Email');
App::uses('Sanitize', 'Utility');
App::uses('CakeText', 'Utility');
App::uses('HttpSocket', 'Network/Http');

class TodosController extends AppController {

	public $name = 'Todos';
	public $objView = null;
	public $components = array('Mpdf', 'Common', 'Group', "CommonEmail");
	public $uses = array(
		"UserDetail",
		"UserProject",
		"Project",
		'Workspace',
		'Area',
		"User",
		"Aligned",
		"Element",
		"DoList",
		"DoListUser",
		"DoListComment",
		"DoListUpload",
		"DoListCommentUpload",
		"DoListCommentLike",
		"EmailNotification",
	);
	public $helpers = array('Html', 'Form', 'Session', 'Time', 'Text', 'Common', 'Todocomon');
	public $user_id = null;

	public function beforeFilter() {

		parent::beforeFilter();
		if (isset($this->request->data)) {
			//$this->request->data = Sanitize::clean($this->request->data, array("remove_html" => true));
		}

		$this->Auth->allow('todoScheduleOverdueEmailCron');

		$this->user_id = $this->Auth->user('id');

		$this->set('controller', 'Todos');

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

		if (isset($this->params['named']['project']) && !empty($this->params['named']['project']) ) {
			if(!dbExists('Project', $this->params['named']['project'])){
				$this->redirect(array('controller' => 'todos', 'action' => 'index'));
			}
		}

		$this->set('title_for_layout', __('To-dos', true));
		$this->set('page_heading', __('To-dos', true));
		$this->set('page_subheading', __('View your To-do lists', true));
		$this->layout = 'inner';

		$user_id = $this->Auth->user('id');
		$view_vars['prj_id'] = $view_vars['day'] = null;
		$view_vars['sdate'] = $view_vars['edate'] = null;

		if (isset($this->params['named']) && !empty($this->params['named'])) {
			$view_vars['prj_id'] = (isset($this->params['named']['project']) && !empty($this->params['named']['project'])) ? $this->params['named']['project'] : null;
			$view_vars['day'] = (isset($this->params['named']['day']) && !empty($this->params['named']['day'])) ? $this->params['named']['day'] : null;
			$view_vars['no_state'] = (isset($this->params['named']['no_state']) && !empty($this->params['named']['no_state'])) ? $this->params['named']['no_state'] : null;

			$view_vars['dolist_id'] = (isset($this->params['named']['dolist_id']) && !empty($this->params['named']['dolist_id'])) ? $this->params['named']['dolist_id'] : null;

			$view_vars['sdate'] = (isset($this->params['named']['sdate']) && !empty($this->params['named']['sdate'])) ? $this->params['named']['sdate'] : null;

			$view_vars['edate'] = (isset($this->params['named']['edate']) && !empty($this->params['named']['edate'])) ? $this->params['named']['edate'] : null;

		}
		$view_vars['current_user'] = $this->user_id;

		$projects = [];
		$mprojects = get_my_projects($this->user_id);
		$rprojects = get_rec_projects($this->user_id, 1, 1);
		$gprojects = group_rec_projects($this->user_id, 2);
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
			/*$projects = array_map("strip_tags", $projects);
			$projects = array_map("trim", $projects);*/
			$projects = array_map(function ($v) {
				return html_entity_decode($v, ENT_COMPAT, "UTF-8");
			}, $projects);
			natcasesort($projects);
		}
		$this->set('projects', $projects);

		$this->setJsVar('view_vars', $this->params['named']);
		$crumb = ['last' => ['data' => ['title' => 'To-dos', 'data-original-title' => 'To-dos']]];

		$this->set('crumb', $crumb);
		//$this->set(compact("sdate", "edate"));
		$this->set($view_vars);
	}

	public function get_users($project_id = null) {
		$this->autoRender = false;
		//$this->User->virtualFields = array('name' => 'CONCAT(UserDetail.first_name," ", UserDetail.last_name)');
		$users = [];
		$this->User->recursive = -1;
		// e($project_id, 1);
		if (empty($project_id)) {

			$usersAll = $this->User->find("all", array(
				"conditions" => array(
					"NOT" => array("User.id" => $this->Session->read("Auth.User.id")),
					"User.role_id" => 2,
					"User.status" => 1,
				),
				"fields" => array("User.id"),
			)
			);
			//	pr($usersAll,1);
			//$this->User->Behaviors->load('Containable');
			if (isset($usersAll) && !empty($usersAll)) {
				foreach ($usersAll as $key => $val) {
					$this->User->Behaviors->load('Containable');
					$userDetail = $this->User->find('first', ['conditions' => ['User.id' => $val['User']['id']], 'contain' => 'UserDetail']);
					if (isset($userDetail) && !empty($userDetail)) {
						$users[] = array('id' => $val['User']['id'], 'name' => (isset($userDetail['UserDetail']['first_name']) && !empty($userDetail['UserDetail']['first_name'])) ? $userDetail['UserDetail']['first_name'] . ' ' . $userDetail['UserDetail']['last_name'] : $userDetail['User']['email']);

					}
				}
			}

			// pr($users, 1);
		} else {


			$view = new View();
			$ViewModel = $view->loadHelper('ViewModel');

			$users = $ViewModel->todo_People_all($project_id,$this->Session->read("Auth.User.id"));

			if(!isset($users) || empty($users)){
				$users = $ViewModel->todo_project_date($project_id,$this->Session->read("Auth.User.id"));
			}

			$users = Hash::extract($users, '{n}.{n}');


		}

		return json_encode($users);
	}

	public function check_email_permission($user_id = null, $personlization = null) {

		$notifiragstatus = ClassRegistry::init('EmailNotification')->find('first', ['conditions' => ['notification_type' => 'to_dos', 'personlization' => $personlization, 'user_id' => $user_id]]);

		$usersDetails = ClassRegistry::init('User')->find('first', array('conditions' => array('User.id' => $user_id)));

		if ((!isset($notifiragstatus['EmailNotification']['email']) || $notifiragstatus['EmailNotification']['email'] == 1) && (!isset($usersDetails['User']['email_notification']) || $usersDetails['User']['email_notification'] == 1)) {
			return true;
		} else {
			return false;
		}

	}

	public function manage($todo_id = null) {

		$this->set('title_for_layout', __('Create To-do', true));
		$this->set('page_heading', __('Create To-do', true));
		$this->set('page_subheading', __('Create a To-do', true));
		$crumb = ['last' => ['data' => ['title' => 'To-do lists', 'data-original-title' => 'To-do Lists']]];

		$this->user_id = $this->Auth->user('id');
		$sub = '';
		$msg = 'saved';
		$count = 0;
		if (isset($todo_id) && !empty($todo_id)) {
			$count = $this->DoList->find('count', array('conditions' => array('DoList.id' => $todo_id, 'DoList.user_id' => $this->user_id)));

			if ($count < 1) {
				$this->Session->setFlash('Invalid access!', 'error');
				$this->redirect(array("action" => "index"));
			}
		}

		if (isset($todo_id) && !empty($todo_id)) {
			$old = $this->DoList->findById($todo_id);
			if (isset($old['Parent']['id']) && !empty($old['Parent']['id'])) {
				$sub = 'Sub ';
			}
			$this->set('title_for_layout', __('Update ' . $sub . 'To-do', true));
			$this->set('page_heading', __('Update ' . $sub . 'To-do', true));
			$this->set('page_subheading', __('Update ' . $sub . 'To-do', true));
		}

		$this->layout = 'inner';

		if ($this->request->is('post') || $this->request->is('put')) {
			//pr($this->request->data, 1);
			$this->DoList->set($this->request->data['DoList']);
			$todo_users = null;

			$todo_title = $this->request->data['DoList']['title'];

			$this->request->data['DoList']['user_id'] = $this->user_id;
			$this->request->data['DoList']['created_by'] = $this->user_id;

			$this->request->data['DoList']['is_search'] = isset($this->request->data['DoList']['project_id']) && !empty($this->request->data['DoList']['project_id']) ? 0 : 1;

			//pr($this->request->data['DoList'],1);

			if ($this->DoList->validates()) {

				if (isset($this->request->data['DoList']['dateby']) && !empty($this->request->data['DoList']['dateby'])) {
					$dat = explode('-', $this->request->data['DoList']['dateby']);

					if (isset($dat[0]) && !empty($dat[0])) {
						/* $startArray = explode('/', $dat[0]);
							$start_date = date(trim($startArray[1]) . '/' . trim($startArray[0]) . '/' . trim($startArray[2]));
						*/
						$this->request->data['DoList']['start_date'] = date("Y-m-d", strtotime($dat[0]));
					}
					if (isset($dat[1]) && !empty($dat[1])) {
						/*$endArray = explode('/', $dat[1]);
							$end_date = date(trim($endArray[1]) . '/' . trim($endArray[0]) . '/' . trim($endArray[2]));
						*/
						$this->request->data['DoList']['end_date'] = date("Y-m-d", strtotime($dat[1]));
					} else {
						/*$startArray = explode('/', $dat[0]);
							$start_date = date(trim($startArray[1]) . '/' . trim($startArray[0]) . '/' . trim($startArray[2]));
						*/
						$this->request->data['DoList']['end_date'] = date("Y-m-d", strtotime($dat[0]));
					}
				}

				if (isset($this->request->data['DoList']['dateby']) && !empty($this->request->data['DoList']['dateby'])) {
					unset($this->request->data['DoList']['dateby']);
				}

				//======= Check Start and End date of TODO ===============================
				$subParentTodoList = $this->DoList->find('first', array('conditions' => array('DoList.id' => $this->request->data['DoList']['id']), 'recursive' => -1));

				if (isset($this->request->data['DoList']['id']) && !empty($this->request->data['DoList']['id']) && (isset($subParentTodoList) && $subParentTodoList['DoList']['parent_id'] <= 0)) {

					if (isset($this->request->data['DoList']['start_date']) && !empty($this->request->data['DoList']['start_date'])) {
						$subtodolist = $this->DoList->find('count', array('conditions' => array('DoList.parent_id' => $this->request->data['DoList']['id'], 'DoList.start_date < ' => $this->request->data['DoList']['start_date']), 'recursive' => -1));

						if ($subtodolist > 0) {
							$this->Session->setFlash(__('To-do Start date should not be greater than Sub To-do\'s Start date.'), 'error');
							$this->redirect(array('controller' => 'todos', 'action' => 'manage', $this->request->data['DoList']['id']));
						}
					}

					if (isset($this->request->data['DoList']['end_date']) && !empty($this->request->data['DoList']['end_date'])) {
						$subtoDoListEnd = $this->DoList->find('count', array('conditions' => array('DoList.parent_id' => $this->request->data['DoList']['id'], 'DoList.end_date > ' => $this->request->data['DoList']['end_date']), 'recursive' => -1));

						if ($subtoDoListEnd > 0) {
							$this->Session->setFlash(__('To-do End date should not be less than Sub To-do\'s End date.'), 'error');
							$this->redirect(array('controller' => 'todos', 'action' => 'manage', $this->request->data['DoList']['id']));
						}
					}

				} else {
					//======== Check sub todo date ==========================
					$subTodo = $this->DoList->find('first', array('conditions' => array('DoList.id' => $this->request->data['DoList']['id']), 'recursive' => -1, 'fields' => 'DoList.parent_id'));

					if (isset($subTodo) && !empty($subTodo['DoList']['parent_id'])) {

						$mainTodo = $this->DoList->find('first', array('conditions' => array('DoList.id' => $subTodo['DoList']['parent_id']), 'recursive' => -1));
						$mainTodoStartDate = $mainTodo['DoList']['start_date'];
						$mainTodoEndDate = $mainTodo['DoList']['end_date'];

						if (!empty($this->request->data['DoList']['start_date']) && $this->request->data['DoList']['start_date'] < $mainTodoStartDate) {
							$this->Session->setFlash(__('Sub To-do Start date should not be less than To-do\'s Start date.'), 'error');
							$this->redirect(array('controller' => 'todos', 'action' => 'manage', $this->request->data['DoList']['id']));
						}

						if (!empty($this->request->data['DoList']['end_date']) && $this->request->data['DoList']['end_date'] > $mainTodoEndDate) {
							$this->Session->setFlash(__('Sub To-do End date should not be greater than To-do\'s End date.'), 'error');
							$this->redirect(array('controller' => 'todos', 'action' => 'manage', $this->request->data['DoList']['id']));
						}
					}
					//=============================================================

					if (!empty($subtodolist) && $subtodolist > 0) {
						$this->Session->setFlash(__('To-do Start date should not be greater than Sub To-do\'s Start date.'), 'error');
						$this->redirect(array('controller' => 'todos', 'action' => 'manage', $this->request->data['DoList']['id']));
					}

					if (isset($this->request->data['DoList']['end_date']) && !empty($this->request->data['DoList']['end_date'])) {
						$subtoDoListEnd = $this->DoList->find('count', array('conditions' => array('DoList.parent_id' => $this->request->data['DoList']['id'], 'DoList.end_date > ' => $this->request->data['DoList']['end_date']), 'recursive' => -1));

						if ($subtoDoListEnd > 0) {
							$this->Session->setFlash(__('To-do End date should not be less than Sub To-do\'s End date.'), 'error');
							$this->redirect(array('controller' => 'todos', 'action' => 'manage', $this->request->data['DoList']['id']));
						}
					}

				}

				$post = $this->request->data;
				unset($this->request->data['DoListUser']);

				if (isset($post['DoListUser']['user_id']) && !empty($post['DoListUser']['user_id'])) {
					$ids = $this->DoListUser->find('list', array('conditions' => array('DoListUser.do_list_id' => $post['DoList']['id']), 'fields' => array('DoListUser.id', 'DoListUser.user_id')));
					$users = array();
					foreach ($post['DoListUser']['user_id'] as $key => $val) {
						if (empty($ids) || !in_array($val, $ids)) {
							$users[] = ['user_id' => $val, 'owner_id' => $this->user_id];
							$todo_users[] = $val;
						}
					}

					$this->request->data['DoListUser'] = $users;
				}

				$files = null;
				unset($this->request->data['DoListUpload']);
				/* if (isset($post['DoListUpload']['file_name'])) {
					$getfiles = arrayUnique($post['DoListUpload']['file_name']);
					foreach ($post['DoListUpload']['file_name'] as $key => $val) {
						if (isset($val) && !empty($val)) {
							$files[] = ['file_name' => $val, 'file_name_original' => $val];
						}
					}
					$this->request->data['DoListUpload'] = $files;
				} */

				if (isset($post['upload_files']) && !empty($post['upload_files'])) {
					$uploadFiles = explode(',',$post['upload_files']);
					$getfiles = arrayUnique($uploadFiles);
					foreach ($uploadFiles as $key => $val) {

						if (isset($val) && !empty($val)) {
							$files[] = ['file_name' => $val, 'file_name_original' => $val];
						}

					}
					$this->request->data['DoListUpload'] = $files;
				}

				/* pr($this->request->data['DoListUpload']);
				die; */

				$prjid = null;



				$todo_message = "";
				if (isset($this->request->data['DoList']['id']) && !empty($this->request->data['DoList']['id'])) {
					if(isset($sub) && !empty($sub)){
					$todo_message = "Sub To-do updated";
					}else{
					$todo_message = "To-do updated";
					}

				}else{
					$todo_message = "To-do created";
				}



				if ($this->DoList->saveAll($this->request->data)) {

					if (isset($this->request->data['DoList']['project_id']) && !empty($this->request->data['DoList']['project_id'])) {


					$project_id = $this->request->data['DoList']['project_id'];

					$task_data = [
						'project_id' => $project_id,
						'element_type' => 'do_lists',
						'updated_user_id' => $this->user_id,
						'message' => $todo_message,
						'updated' => date("Y-m-d H:i:s"),
					];
					$this->loadModel('ProjectActivity');
					$this->ProjectActivity->id = null;
					$this->ProjectActivity->save($task_data);

					}

					$projectName = '';
					if (isset($this->request->data['DoList']['project_id']) && !empty($this->request->data['DoList']['project_id'])) {

						$this->Common->projectModified($this->request->data['DoList']['project_id'], $this->user_id);
						//pr($this->request->data);die;

						$projectName = getFieldDetail('Project', $this->request->data['DoList']['project_id'], 'title');
					}

					if (isset($this->request->data['DoList']['id']) && !empty($this->request->data['DoList']['id'])) {
						$getlastid = $this->request->data['DoList']['id'];

						$pageAction = SITEURL.'todos/tododetails/'.$getlastid;

						if (isset($todo_users) && !empty($todo_users)) {
							$todo_users = array_unique($todo_users);
							//== I am here....
							$senderData = $this->User->findById($this->user_id);

							foreach ($todo_users as $user_id) {
								$user_data = $this->User->findById($user_id);

								//========== Strat Email Notifications ========================
								$dasAnnotate = $this->EmailNotification->find('first', ['conditions' => ['notification_type' => 'to_dos', 'personlization' => 'todo_request', 'user_id' => $this->Session->read('Auth.User.id')]]);

								if (!isset($dasAnnotate['EmailNotification']['email']) || $dasAnnotate['EmailNotification']['email'] == 1) {
									$sendMail = $this->CommonEmail->sendEmailToDoUser($user_data, $todo_title, $senderData, "To-do",$pageAction,$projectName); //pr($user_data); die;
								}
								// ======= End Email Notifications ======================

							}

							if (isset($this->request->data['DoList']['project_id']) && !empty($this->request->data['DoList']['project_id'])) {
								$prjid = $this->request->data['DoList']['project_id'];
							}

						}

					} else {

						$getlastid = $this->DoList->getLastInsertID();

						$pageAction = SITEURL.'todos/tododetails/'.$getlastid;

						if (isset($this->request->data['DoList']['project_id']) && !empty($this->request->data['DoList']['project_id'])) {
							$prjid = $this->request->data['DoList']['project_id'];
						}

						if (isset($todo_users) && !empty($todo_users)) {
							$todo_users = array_unique($todo_users);
							$senderData = $this->User->findById($this->user_id);

							foreach ($todo_users as $user_id) {
								$user_data = $this->User->findById($user_id);

								//========== Strat Email Notifications ========================
								$dasAnnotate = $this->EmailNotification->find('first', ['conditions' => ['notification_type' => 'to_dos', 'personlization' => 'todo_request', 'user_id' => $user_id]]);

								if ((!isset($dasAnnotate['EmailNotification']['email']) || $dasAnnotate['EmailNotification']['email'] == 1) && (!isset($user_data['User']['email_notification']) || $user_data['User']['email_notification'] == 1)) {

									$sendMail = $this->CommonEmail->sendEmailToDoUser($user_data, $todo_title, $senderData, "To-do",$pageAction,$projectName);
								}
								// ======= End Email Notifications ======================

							}
						}
					}
					$this->todo_request_notification($getlastid, $todo_users, $prjid);
					$msg = 'saved';
					if (isset($todo_id) && !empty($todo_id)) {
						$msg = "updated";
					}
					//pr($this->request->data,1);
					$this->Session->setFlash('The To-do has been ' . $msg . ' successfully', 'success');
					if (isset($this->request->data['DoList']['project_id']) && $this->request->data['DoList']['project_id'] > 0) {
						$project_id = $this->request->data['DoList']['project_id'];
						$this->redirect(array("action" => "index", "project" => $project_id, "dolist_id" => $getlastid));
					} else {
						$this->redirect(array("action" => "index", "dolist_id" => $getlastid));
					}
				} else {
					$this->Session->setFlash(__('The To-do could not be ' . $msg . '. Please, try again.'), 'error');
				}

			} else {

				// $this->Session->setFlash(__('The To-do could not be ' . $msg . '. Please, try again.'), 'error');

			}
		}

		if (isset($todo_id) && !empty($todo_id)) {
			$this->request->data = $this->DoList->read(null, $todo_id);
			// pr($this->request->data, 1);
		}

		App::import('Controller', 'Users');
		$Users = new UsersController;
		$projects = null;
		$myprojectlist = $myreceivedprojectlist = $mygroupprojectlist = array();
		// Find All current user's projects
		$myprojectlist = $Users->__myproject_selectbox($this->user_id);
		// Find All current user's received projects
		$myreceivedprojectlist = $Users->__receivedproject_selectbox($this->user_id);
		// Find All current user's group projects
		$mygroupprojectlist = $Users->__groupproject_selectbox($this->user_id);

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
		// pr($projects, 1);

		//$projects = get_my_projects($this->user_id);
		$this->set('projects', $projects);

		$this->set('sub', $sub);
		$this->set('crumb', $crumb);

		$project = null;
		if (isset($this->params['named']['project']) && !empty($this->params['named']['project'])) {
			$project = $this->params['named']['project'];
		}
		$this->set('project', $project);
	}

	function todo_request_notification($todo_id = null, $todo_users = null, $project_id = null) {

		/************** socket messages **************/
		if (SOCKET_MESSAGES) {
			$current_user_id = $this->Session->read('Auth.User.id');
			if (isset($todo_users) && !empty($todo_users)) {
				if (($key = array_search($current_user_id, $todo_users)) !== false) {
					unset($todo_users[$key]);
				}
			}
			$open_users = null;
			if (isset($todo_users) && !empty($todo_users)) {
				foreach ($todo_users as $key1 => $value1) {
					if (web_notify_setting($value1, 'to_dos', 'todo_request')) {
						$open_users[] = $value1;
					}
				}
			}
			// pr($open_users, 1);
			$sub_heading = 'N/A';
			$prj = '';
			if (isset($project_id) && !empty($project_id)) {
				$sub_heading = strip_tags(getFieldDetail('Project', $project_id, 'title'));
				$prj = $project_id;
			}
			$userDetail = get_user_data($current_user_id);
			$ts_parent_id = getFieldDetail('DoList', $todo_id, 'parent_id');
			$ts_text = '';
			if (!empty($ts_parent_id)) {
				$ts_text = 'Sub ';
			}
			$content = [
				'notification' => [
					'type' => 'todo_request',
					'project_id' => $prj,
					'refer_id' => $todo_id,
					'created_id' => $current_user_id,
					'creator_name' => $userDetail['UserDetail']['full_name'],
					'subject' => $ts_text . 'To-do request',
					'heading' => $ts_text . 'To-do: ' . htmlentities(getFieldDetail('DoList', $todo_id, 'title'), ENT_QUOTES, "UTF-8"),
					'sub_heading' => 'Project: ' . $sub_heading,
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

	public function save_subtodo() {

		if ($this->request->isAjax()) {
			if ($this->request->is('post') || $this->request->is('put')) {

				$this->DoList->set($this->request->data['DoList']);
				$todo_users = null;
				$todo_title = $this->request->data['DoList']['title'];
				$this->request->data['DoList']['user_id'] = $this->user_id;
				$this->request->data['DoList']['created_by'] = $this->user_id;
				$response['flag'] = null;

				if ($this->DoList->validates()) {

					if (isset($this->request->data['DoList']['dateby']) && !empty($this->request->data['DoList']['dateby'])) {
						$dat = explode('-', $this->request->data['DoList']['dateby']);

						if (isset($dat[0]) && !empty($dat[0])) {
							/* $startArray = explode('/', $dat[0]);
								$start_date = date(trim($startArray[1]) . '/' . trim($startArray[0]) . '/' . trim($startArray[2]));
							*/
							$this->request->data['DoList']['start_date'] = date("Y-m-d", strtotime($dat[0]));
						}
						if (isset($dat[1]) && !empty($dat[1])) {
							/* $endArray = explode('/', $dat[1]);
								$end_date = date(trim($endArray[1]) . '/' . trim($endArray[0]) . '/' . trim($endArray[2]));
							*/
							$this->request->data['DoList']['end_date'] = date("Y-m-d", strtotime($dat[1]));
						} else {
							/* $startArray = explode('/', $dat[0]);
								$start_date = date(trim($startArray[1]) . '/' . trim($startArray[0]) . '/' . trim($startArray[2]));
							*/
							$this->request->data['DoList']['end_date'] = date("Y-m-d", strtotime($dat[0]));
						}
					}

					//================= Check Sub ToDo date with Parent To-Do ======
					if (!empty($this->request->data['DoList']['id'])) {
						$todo_parent_id = getFieldDetail('DoList', $this->request->data['DoList']['id'], 'parent_id');
					} else {
						$todo_parent_id = $this->request->data['DoList']['parent_id'];
					}

					if (!empty($todo_parent_id) && !empty($this->request->data['DoList']['start_date'])) {

						$parentDetail = getByDbId('DoList', $todo_parent_id, ['start_date', 'end_date']);
						$todoStratDate = $parentDetail['DoList']['start_date'];
						$todoEndDate = $parentDetail['DoList']['end_date'];

						if (isset($todoStratDate) && !empty($todoStratDate)) {
							if ($this->request->data['DoList']['start_date'] < $todoStratDate) {
								$response['success'] = false;
								$response['msg'] = 'Sub To-do Start date should not be less than To-do\'s Start date.';
								$response['flag'] = 'reload';
								$response['content']['startEndDateErr'] = 'Sub To-do Start date should not be less than To-do\'s Start date.';
								echo json_encode($response);
								exit();
							}
						}
						if (isset($todoEndDate) && !empty($todoEndDate)) {
							if ($this->request->data['DoList']['end_date'] > $todoEndDate) {

								$response['success'] = false;
								$response['msg'] = 'Sub To-do End date should not be greater than To-do\'s End date.';
								$response['flag'] = 'reload';
								$response['content']['startEndDateErr'] = 'Sub To-do End date should not be greater than To-do\'s End date.';

								echo json_encode($response);
								exit();
							}
						}
					}

					//=========================================================

					if (isset($this->request->data['DoList']['dateby']) && !empty($this->request->data['DoList']['dateby'])) {
						unset($this->request->data['DoList']['dateby']);
					}

					$post = $this->request->data;

					unset($this->request->data['DoListUser']);

					if (isset($post['DoListUser']['user_id']) && !empty($post['DoListUser']['user_id'])) {

						if (isset($post['DoList']['id']) && !empty($post['DoList']['id'])) {
							$group = $this->objView->loadHelper('Group');
							$SubuserID = $group->dolist_users($post['DoList']['id'], false);

							$ids = Set::extract($SubuserID, '/DoListUser/id');
							if (isset($ids) && !empty($ids)) {
								$this->DoListUser->delete($ids);
							}
						}

						$post['DoListUser']['user_id'] = array_filter($post['DoListUser']['user_id']);
						foreach ($post['DoListUser']['user_id'] as $key => $val) {
							$users[] = ['user_id' => $val, 'owner_id' => $this->user_id];
							$todo_users[] = $val;
						}

						$this->request->data['DoListUser'] = $users;
					}

					$files = null;
					unset($this->request->data['DoListUpload']);
					if (isset($post['DoListUpload']['file_name'])) {
						$getfiles = arrayUnique($post['DoListUpload']['file_name']);
						foreach ($post['DoListUpload']['file_name'] as $key => $val) {
							$files[] = ['file_name' => $val, 'file_name_original' => $val];
						}
						$this->request->data['DoListUpload'] = $files;
					}

					$senderData = $this->User->findById($this->user_id);

					$todo_message = "";
					if (isset($this->request->data['DoList']['id']) && !empty($this->request->data['DoList']['id'])) {

						$todo_message = "Sub To-do updated";
					} else {
						$todo_message = "Sub To-do created";

					}


					if ($this->DoList->saveAll($this->request->data)) {

						if (isset($this->request->data['DoList']['id']) && !empty($this->request->data['DoList']['id'])) {
							$getlastid = $this->request->data['DoList']['id'];
							//$todo_message = "Sub To-do updated";
						} else {
							//$todo_message = "Sub To-do created";
							$getlastid = $this->DoList->getLastInsertID();
							$pageAction = SITEURL.'todos/tododetails/'.$getlastid;
							if (isset($todo_users) && !empty($todo_users)) {
								$todo_users = array_unique($todo_users);
								foreach ($todo_users as $user_id) {
									$user_data = $this->User->findById($user_id);
									$sendMail = $this->CommonEmail->sendEmailToDoUser($user_data, $todo_title, $senderData, "Sub To-do",$pageAction);
								}

								$response['flag'] = 'reload';
							}
						}

						$ts_project_id = getFieldDetail('DoList', $getlastid, 'project_id');
						//pr($getlastid);
						//pr($ts_project_id ,1);

						if (isset($ts_project_id) && !empty($ts_project_id)) {


						$project_id = $ts_project_id;

						$task_data = [
							'project_id' => $project_id,
							'element_type' => 'do_lists',
							'updated_user_id' => $this->user_id,
							'message' => $todo_message,
							'updated' => date("Y-m-d H:i:s"),
						];
						$this->loadModel('ProjectActivity');
						$this->ProjectActivity->id = null;
						$this->ProjectActivity->save($task_data);



						}


						$this->todo_request_notification($getlastid, $todo_users, $ts_project_id);

						//$this->Session->setFlash('The To-do has been saved successfully', 'success');
						$response['success'] = true;
						$response['msg'] = 'Sub To-do has been saved.';
						$response['content'] = $this->request->data;
						$response['todo_id'] = $getlastid;
						$response['project_id'] = $ts_project_id;
					} else {
						$response['success'] = false;
						$response['msg'] = 'Please try again later.';
						//$this->Session->setFlash(__('The To-do could not be saved. Please, try again.'), 'error');
					}
					echo json_encode($response);
					exit();
				} else {
					$response['msg'] = 'Please try again later.';
					//pr($this->validateErrors($this->DoList));
					$response['content'] = $this->validateErrors($this->DoList);
					echo json_encode($response);
					exit();
				}
			}
		}
	}

	public function comments($do_list_id = null) {

		if ($this->request->isAjax()) {

			// $viewModel = $this->objView->loadHelper( 'ViewModel' );

			$this->layout = 'ajax';

			$data = null;

			$view = new View($this, false);
			$view->viewPath = 'Todos/partials'; // Directory inside view directory to search for .ctp files
			$view->layout = false; // if you want to disable layout

			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;

				if (isset($post['do_list_id']) && !empty($post['do_list_id'])) {
					$do_list_id = $post['do_list_id'];

					$conditions = null;
					$conditions['DoListComment.do_list_id'] = $do_list_id;
					if (isset($post['users']) && !empty($post['users'])) {
						$conditions['DoListComment.user_id'] = $post['users'];
					}

					$data = $this->DoListComment->find('all', [
						'conditions' => [$conditions],
						'recursive' => -1,
						"order" => array("DoListComment.modified DESC"),
					]);
					$view->set('do_list_id', $do_list_id);
				}

				$view->set('data', $data);

				$html = $view->render('comment_box');
			}
			echo json_encode($html);
			exit();
		}
	}

	public function get_comments_count() {

		if ($this->request->isAjax()) {
			$this->layout = 'ajax';

			$response = ['success' => true, 'content' => null];

			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;

				if (isset($post['do_list_id']) && !empty($post['do_list_id'])) {
					$do_list_id = $post['do_list_id'];

					$todo_comments = get_todo_comments($do_list_id, true);
					$response['success'] = true;
					$response['content'] = ($todo_comments) ? $todo_comments : 0;
				}

			}
			echo json_encode($response);
			exit();
		}
	}

	public function todo_users() {
		$this->layout = 'ajax';
		$do_list_id = $data = null;
		if ($this->request->isAjax()) {
			if (isset($this->request->data['do_list_id']) && !empty($this->request->data['do_list_id'])) {

				$do_list_id = $this->request->data['do_list_id'];
				$data = $this->DoList->find('first', [
					'conditions' => [
						'DoList.id' => $do_list_id,
					],
					'recursive' => -1,
				]);
			}

		}
		$this->set('data', $data);
		$this->set('do_list_id', $do_list_id);
	}

	public function people_comments() {

		if ($this->request->isAjax()) {

			// $viewModel = $this->objView->loadHelper( 'ViewModel' );

			$this->layout = 'ajax';

			$data = null;

			$view = new View($this, false);
			$view->viewPath = 'Todos/partials'; // Directory inside view directory to search for .ctp files
			$view->layout = false; // if you want to disable layout

			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;

				if (isset($post['do_list_id']) && !empty($post['do_list_id'])) {
					$do_list_id = $post['do_list_id'];

					$conditions = null;
					$conditions['DoListComment.do_list_id'] = $do_list_id;
					if (isset($post['users']) && !empty($post['users'])) {
						$conditions['DoListComment.user_id'] = $post['users'];
					}
					// pr($conditions, 1);
					$data = $this->DoListComment->find('all', [
						'conditions' => $conditions,
						'recursive' => -1,
						"order" => array("DoListComment.modified DESC"),
					]);
					$view->set('do_list_id', $do_list_id);
				}

				$view->set('data', $data);

				$html = $view->render('people_comment');
			}
			echo json_encode($html);
			exit();
		}
	}

	public function buttons_panel() {

		if ($this->request->isAjax()) {

			// $viewModel = $this->objView->loadHelper( 'ViewModel' );

			$this->layout = 'ajax';

			$data = null;

			$view = new View($this, false);
			$view->viewPath = 'Todos/partials'; // Directory inside view directory to search for .ctp files
			$view->layout = false; // if you want to disable layout

			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;

				if (isset($post['do_list_id']) && !empty($post['do_list_id'])) {
					$do_list_id = $post['do_list_id'];
					$data = $this->DoList->find('first', [
						'conditions' => [
							'DoList.id' => $do_list_id,
						],
						'recursive' => -1,
					]);
					$view->set('do_list_id', $do_list_id);
				}

				$view->set('data', $data);

				$html = $view->render('buttons_panel');
			}
			echo json_encode($html);
			exit();
		}
	}

	public function buttons_panel_sidebar() {

		if ($this->request->isAjax()) {

			// $viewModel = $this->objView->loadHelper( 'ViewModel' );

			$this->layout = 'ajax';

			$data = null;

			$view = new View($this, false);
			$view->viewPath = 'Todos/partials'; // Directory inside view directory to search for .ctp files
			$view->layout = false; // if you want to disable layout

			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;

				if (isset($post['do_list_id']) && !empty($post['do_list_id'])) {
					$do_list_id = $post['do_list_id'];
					$data = $this->DoList->find('first', [
						'conditions' => [
							'DoList.id' => $do_list_id,
						],
						'recursive' => -1,
					]);
					$view->set('do_list_id', $do_list_id);
				}

				$view->set('data', $data);

				$html = $view->render('buttons_panel_sidebar');
			}
			echo json_encode($html);
			exit();
		}
	}

	public function like_comment($comment_id = null) {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$data = $response = null;
			$response = ['content' => null, 'success' => false];

			if ( $this->DoListComment->exists($comment_id) ) {

				if (isset($comment_id) && !empty($comment_id)) {
					// check that the current user not posted like for this comment previously
					$data = $this->DoListCommentLike->find('count', [
						'conditions' => [
							'DoListCommentLike.user_id' => $this->user_id,
							'DoListCommentLike.do_list_comment_id' => $comment_id,
						],
						'recursive' => -1,
					]);

					// if the current user not posted like for this comment previously, only then enter data in database
					if (isset($data) && empty($data)) {
						$in_data['DoListCommentLike']['user_id'] = $this->user_id;
						$in_data['DoListCommentLike']['do_list_comment_id'] = $comment_id;
						// pr($this->DoListCommentLike->save($in_data), 1);
						if ($this->DoListCommentLike->save($in_data)) {
							$response['success'] = true;
						}
					}
				}
			}

			echo json_encode($response);
			exit();
		}
	}

	public function delete_dolist() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$data = null;
			$data['success'] = false;
			$data['content'] = '';
			$data['socket_content'] = null;
			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;
				// pr($post, 1);
				if (isset($post['do_list_id']) && !empty($post['do_list_id'])) {
					$do_list_id = $post['do_list_id'];

					$todo_details = $this->DoList->findById($do_list_id);

					$list_data = $this->DoList->find('first', [
						'conditions' => [
							'DoList.parent_id' => $do_list_id,
						],
					]);

					$todotype = 'To-do';
					if (isset($todo_details['DoList']['title']) && !empty($todo_details['DoList']['title'])) {
						if ($todo_details['DoList']['parent_id'] > 0) {
							$todotype = 'Sub to-do';
						}
					}

					$DoListData = $this->DoList->find('first', [
						'conditions' => [
							'DoList.id' => $do_list_id,
						],
						'recursive' => -1,
					]);
					$group = $this->objView->loadHelper('Group');
					$all_owner = $group->do_list_users($do_list_id,1);

					if (isset($list_data) && !empty($list_data)) {
						$data['content'] = 'Please delete all sub To-do first.';
					} else {
						$this->todoDeleteEmail($todo_details['DoList']['title'], $do_list_id, $todotype);


					if (isset($DoListData['DoList']['project_id']) && !empty($DoListData['DoList']['project_id'])) {

					$project_id = $DoListData['DoList']['project_id'];


					if($DoListData['DoList']['parent_id'] >0){
						$todo_message = "Sub To-do deleted";
					}else{
						$todo_message = "To-do deleted";
					}


					$task_data = [
						'project_id' => $project_id,
						'element_type' => 'do_lists',
						'updated_user_id' => $this->user_id,
						'message' => $todo_message,
						'updated' => date("Y-m-d H:i:s"),
					];
					$this->loadModel('ProjectActivity');
					$this->ProjectActivity->id = null;
					$this->ProjectActivity->save($task_data);



					}

					$this->DoList->delete($do_list_id);

						$data['success'] = true;
						/************** socket messages **************/
						if (SOCKET_MESSAGES) {
							$current_user_id = $this->Auth->user('id');

							if (isset($all_owner) && !empty($all_owner)) {
								if (isset($DoListData['DoList']['user_id'])) {
									$loggedinuserss[] = $DoListData['DoList']['user_id'];
									$all_owner = array_merge($all_owner, $loggedinuserss);
								}
							}
							$all_owner = array_unique(array_filter($all_owner));
							if (isset($all_owner) && !empty($all_owner)) {
								if (($key = array_search($current_user_id, $all_owner)) !== false) {
									unset($all_owner[$key]);
								}
							}

							$s_open_users = $r_open_users = null;
							if (isset($all_owner) && !empty($all_owner)) {
								foreach ($all_owner as $key => $value) {
									if (web_notify_setting($value, 'to_dos', 'todo_delete')) {
										$s_open_users[] = $value;
									}
								}
							}
							$userDetail = get_user_data($current_user_id);
							$sub_heading = (isset($DoListData['DoList']['project_id']) && !empty($DoListData['DoList']['project_id'])) ? 'Project: ' . strip_tags(getFieldDetail('Project', $DoListData['DoList']['project_id'], 'title')) : 'Project: ' . 'N/A';
							$ts_parent_id = $todo_details['DoList']['parent_id'];
							$ts_text = '';
							if (!empty($ts_parent_id)) {
								$ts_text = 'Sub ';
							}
							$content = [
								'notification' => [
									'type' => 'todo_delete',
									'created_id' => $current_user_id,
									'project_id' => $DoListData['DoList']['project_id'],
									'creator_name' => $userDetail['UserDetail']['full_name'],
									'subject' => $ts_text . 'To-do deleted',
									'heading' => $ts_text . 'To-do: ' . $todo_details['DoList']['title'],
									'sub_heading' => $sub_heading,
									'date_time' => timezoneTimeConvertor($this->user_id, date('Y-m-d g:iA'), $format = 'd M Y g:iA') . user_country($this->user_id),
								],
							];

							if (is_array($s_open_users)) {
								$content['received_users'] = array_values($s_open_users);
							}
							$data['socket_content']['socket'] = $content;
						}
						/************** socket messages **************/

					}
				}
			}
			echo json_encode($data);
			exit();
		}
	}

	public function delete_comment($comment_id = null) {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$data = null;
			$data['success'] = false;
			$data['content'] = '';
			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;
				$todoComm = $this->DoListComment->findById($comment_id);
				$todo = $this->DoList->findById($todoComm['DoListComment']['do_list_id']);


				if (isset($todo['DoList']['project_id']) && $todo['DoList']['project_id'] > 0){

					$subtext = '';
					if(isset($todo['DoList']['parent_id']) && $todo['DoList']['parent_id'] > 0){
						$subtext = 'Sub ';
					}


					$todo_message = $subtext."To-do comment deleted";

				}

				if (isset($comment_id) && !empty($comment_id)) {

					if ($this->DoListComment->delete($comment_id)) {

						if (isset($todo['DoList']['project_id']) && $todo['DoList']['project_id'] > 0){
							$task_data = [
								'project_id' => $todo['DoList']['project_id'],
								'element_type' => 'do_list_comments',
								'updated_user_id' => $this->user_id,
								'message' => $todo_message,
								'updated' => date("Y-m-d H:i:s"),
							];
							$this->loadModel('ProjectActivity');
							$this->ProjectActivity->id = null;
							$this->ProjectActivity->save($task_data);
						}

						$data['success'] = true;
					}
				}
			}
			echo json_encode($data);
			exit();
		}
	}

	public function sign_off_dolist() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$response = ['success' => false, 'content' => null];
			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;
				// pr($post, 1);
				if (isset($post['do_list_id']) && !empty($post['do_list_id'])) {
					$do_list_id = $post['do_list_id'];

					$this->DoList->id = $do_list_id;
					$this->DoList->set(array('sign_off' => 1));
					$this->DoList->save();

					//$old = $this->DoList->findById($id);
					$ts_parent_id = getFieldDetail('DoList', $do_list_id, 'parent_id');
					$child = $this->DoList->find('all', ['conditions' => ['DoList.parent_id' => $do_list_id]]);
					$child = Set::extract($child, '/DoList/id');
					if (isset($child) && !empty($child)) {
						if ($this->DoList->updateAll(array('DoList.sign_off' => 1), array('DoList.id' => $child))) {
							$type = null;
							if (isset($old['DoList']['parent_id']) && $old['DoList']['parent_id'] > 0) {
								$type = ' Sub ';
							}

							$this->Session->setFlash('You have successfully sign off ' . $type . ' To-do.', 'success');
						}
					}

					//===== Start Email Notifications =========================================================

					$todo_details = $this->DoList->findById($do_list_id);
					// pr($todo_details, 1);
					if (isset($todo_details['DoList']['title']) && !empty($todo_details['DoList']['title'])) {

						if (isset($todo_details['DoList']['parent_id']) && $todo_details['DoList']['parent_id'] > 0) {
							$this->todoSignOffEmail($todo_details['DoList']['title'], $do_list_id, 'Sub To-do');
						} else {
							$this->todoSignOffEmail($todo_details['DoList']['title'], $do_list_id, 'To-do');
						}

						/************** socket messages **************/
						if (SOCKET_MESSAGES) {

							$current_user_id = $this->Auth->user('id');
							$group = $this->objView->loadHelper('Group');
							$all_owner = $group->do_list_users($do_list_id,1);

							$DoListData = $this->DoList->find('first', [
								'conditions' => [
									'DoList.id' => $do_list_id,
								],
								'recursive' => -1,
							]);

							if (isset($all_owner) && !empty($all_owner)) {
								if (isset($DoListData['DoList']['user_id'])) {
									$loggedinuserss[] = $DoListData['DoList']['user_id'];
									$all_owner = array_merge($all_owner, $loggedinuserss);
								}
							}
							$all_owner = array_unique(array_filter($all_owner));
							if (isset($all_owner) && !empty($all_owner)) {
								if (($key = array_search($current_user_id, $all_owner)) !== false) {
									unset($all_owner[$key]);
								}
							}

							$s_open_users = $r_open_users = null;
							if (isset($all_owner) && !empty($all_owner)) {
								foreach ($all_owner as $key => $value) {
									if (web_notify_setting($value, 'to_dos', 'todo_signoff')) {
										$s_open_users[] = $value;
									}
								}
							}
							$userDetail = get_user_data($current_user_id);
							$sub_heading = (isset($DoListData['DoList']['project_id']) && !empty($DoListData['DoList']['project_id'])) ? 'Project: ' . strip_tags(getFieldDetail('Project', $DoListData['DoList']['project_id'], 'title')) : 'Project: ' . 'N/A';
							$ts_parent_id = $todo_details['DoList']['parent_id'];
							$ts_text = '';
							if (!empty($ts_parent_id)) {
								$ts_text = 'Sub ';
							}

							if (isset($DoListData['DoList']['project_id']) && $DoListData['DoList']['project_id'] > 0){

									$todo_message = $ts_text."To-do signed off";
									$task_data = [
									'project_id' => $DoListData['DoList']['project_id'],
									'element_type' => 'do_list',
									'updated_user_id' => $this->user_id,
									'message' => $todo_message,
									'updated' => date("Y-m-d H:i:s"),
									];
									$this->loadModel('ProjectActivity');
									$this->ProjectActivity->id = null;
									$this->ProjectActivity->save($task_data);
							}
							$content = [
								'notification' => [
									'type' => 'todo_signoff',
									'created_id' => $current_user_id,
									'project_id' => $DoListData['DoList']['project_id'],
									'refer_id' => $DoListData['DoList']['id'],
									'creator_name' => $userDetail['UserDetail']['full_name'],
									'subject' => $ts_text . 'To-do sign-off',
									'heading' => $ts_text . 'To-do: ' . $todo_details['DoList']['title'],
									'sub_heading' => $sub_heading,
									'date_time' => timezoneTimeConvertor($this->user_id, date('Y-m-d g:iA'), $format = 'd M Y g:iA') . user_country($this->user_id),
								],
								//'received_users' => array_values($ele_users),
							];
							if (is_array($s_open_users)) {
								$content['received_users'] = array_values($s_open_users);
							}
							$response['content']['socket'] = $content;
						}
						/************** socket messages **************/

					}

					$response['success'] = true;
				}
			}
			// pr($response, 1);
			echo json_encode($response);
			exit();
		}
	}

	public function archive_dolist() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$data = null;
			$data['success'] = false;
			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;
				// pr($post, 1);
				if (isset($post['do_list_id']) && !empty($post['do_list_id'])) {
					$do_list_id = $post['do_list_id'];

					$this->DoList->id = $do_list_id;
					$this->DoList->set(array('is_archive' => 1));
					$this->DoList->save();

					$child = $this->DoList->find('all', ['conditions' => ['DoList.parent_id' => $do_list_id]]);
					$child = Set::extract($child, '/DoList/id');
					if ($this->DoList->updateAll(array('DoList.is_archive' => 1), array('DoList.id' => $child))) {

					}

					$data['success'] = true;
				}
			}
			echo json_encode($data);
			exit();
		}
	}

	public function page_body() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$data = null;

			$view = new View($this, false);
			$view->viewPath = 'Todos/partials'; // Directory inside view directory to search for .ctp files
			$view->layout = false; // if you want to disable layout

			$view_vars['prj_id'] = $view_vars['day'] = null;
			$view_vars['sdate'] = $view_vars['edate'] = null;
			if (isset($this->params['named']) && !empty($this->params['named'])) {
				$view_vars['prj_id'] = (isset($this->params['named']['project']) && !empty($this->params['named']['project'])) ? $this->params['named']['project'] : null;
				$view_vars['day'] = (isset($this->params['named']['day']) && !empty($this->params['named']['day'])) ? $this->params['named']['day'] : null;
				$view_vars['no_state'] = (isset($this->params['named']['no_state']) && !empty($this->params['named']['no_state'])) ? $this->params['named']['no_state'] : null;

				$view_vars['dolist_id'] = (isset($this->params['named']['dolist_id']) && !empty($this->params['named']['dolist_id'])) ? $this->params['named']['dolist_id'] : null;
				$view_vars['sdate'] = (isset($this->params['named']['sdate']) && !empty($this->params['named']['sdate'])) ? $this->params['named']['sdate'] : null;
				$view_vars['edate'] = (isset($this->params['named']['edate']) && !empty($this->params['named']['edate'])) ? $this->params['named']['edate'] : null;
			}
			$view_vars['current_user'] = $this->user_id;
			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;

				$project_id = null;
				if (isset($post['project_id']) && !empty($post['project_id'])) {
					// $project_id = $post['project_id'];
				}

				$this->setJsVar('view_vars', $this->params['named']);
				$view->set($view_vars);
				$view->set('data', $data);

				$html = $view->render('page_body');
			}
			echo json_encode($html);
			exit();
		}
	}

	public function manage_comment($do_list_id = null, $comment_id = null) {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$response = null;
			$response['do_list_id'] = $do_list_id;

			if (isset($comment_id) && !empty($comment_id)) {
				$response['comment_id'] = $comment_id;

				$this->request->data = $this->DoListComment->read(null, $comment_id);

				$response['comment_id'] = $comment_id;

				// pr($this->request->data );
			}

			$this->set('response', $response);

			$this->render('/Todos/partials/manage_comment');
		}
	}

	public function save_comment() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$response = [
				'success' => false,
				'content' => null,
			];

			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;
				$uploads = null;

				$todo = $this->DoList->findById($post['DoListComment']['do_list_id']);
				if (isset($todo['DoList']['project_id']) && $todo['DoList']['project_id'] != 0) {
					$post['DoListComment']['is_search'] = 1;
				}
				if (isset($post['DoListCommentUpload']['file_name']) && !empty($post['DoListCommentUpload']['file_name'])) {
					foreach ($post['DoListCommentUpload']['file_name'] as $key => $val) {
						if (isset($todo['DoList']['project_id']) && $todo['DoList']['project_id'] != 0) {
							$uploads[] = ['is_search' => 1, 'file_name' => $val, 'file_name_original' => $val];
						} else {
							$uploads[] = ['file_name' => $val, 'file_name_original' => $val];
						}

					}
					unset($post['DoListCommentUpload']);
					$post['DoListCommentUpload'] = $uploads;
				}

				//$this->DoListComment->validates();

				//$this->request->data['DoListComment']['comments'] = trim(strip_tags($this->request->data['DoListComment']['comments']));


				if (isset($todo['DoList']['project_id']) && $todo['DoList']['project_id'] > 0){

					$subtext = '';
					if(isset($todo['DoList']['parent_id']) && $todo['DoList']['parent_id'] > 0){
						$subtext = 'Sub ';
					}

					if(isset($post['DoListComment']['id']) && !empty($post['DoListComment']['id'])){
						$todo_message = $subtext."To-do comment updated";
					}else{
						$todo_message = $subtext."To-do comment created";
					}
				}

				if ( !empty($this->request->data['DoListComment']['comments']) ) {
					if ($this->DoListComment->saveAll($post)) {
						if (isset($todo['DoList']['project_id']) && $todo['DoList']['project_id'] != 0) {
							$this->Common->projectModified($todo['DoList']['project_id'], $this->user_id);

							$task_data = [
								'project_id' => $todo['DoList']['project_id'],
								'element_type' => 'do_list_comments',
								'updated_user_id' => $this->user_id,
								'message' => $todo_message,
								'updated' => date("Y-m-d H:i:s"),
							];
							$this->loadModel('ProjectActivity');
							$this->ProjectActivity->id = null;
							$this->ProjectActivity->save($task_data);


						}
						$response['success'] = true;
						$response['content'] = $this->DoListComment->getLastInsertId();
					}
				} else {
					$response = [
						'success' => false,
						'msg' => 'The comment could not be saved. Please, try again.',
						'content' => "A comment is required.",
					];
					echo json_encode($response);
					exit();
				}
			}
			echo json_encode($response);
			exit;
		}
	}

	public function dolist_uploads($do_list_id = null) {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$response = null;
			$response['do_list_id'] = $do_list_id;
			$response = [
				'success' => false,
				'content' => null,
			];
			// pr($this->request->data, 1);
			if (isset($this->data['DoListUpload']['file_name']) && !empty($this->data['DoListUpload']['file_name'])) {
				// $getfiles = $this->data['DoListUpload']['file_name'];
				$getfiles = arrayUnique($this->request->data['DoListUpload']['file_name']);
				//pr($getfiles, 1);

				if (isset($getfiles) && !empty($getfiles)) {
					$response['success'] = true;
					foreach ($getfiles as $k => $value) {
						$output_dir = DO_LIST_UPLOAD;

						$new_value1 = str_replace('"','',$value['name']);
						$new_value = str_replace("'",'',$new_value1);
						//$new_value = str_replace('^','',$new_value2);

						// $ext = pathinfo($value['name']);
						// $fileName = $this->unique_file_name($output_dir, $value['name']);
						$ext = pathinfo($new_value);
						$fileName = $this->unique_file_name($output_dir, $new_value);

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

	public function comment_uploads($do_list_id = null) {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$response = null;
			$response['do_list_id'] = $do_list_id;
			$response = [
				'success' => false,
				'content' => null,
			];

			if (isset($this->data['DoListCommentUpload']['file_name']) && !empty($this->data['DoListCommentUpload']['file_name'])) {
				$getfiles = $this->data['DoListCommentUpload']['file_name'];
				$savearray = array();
				if (isset($getfiles) && !empty($getfiles)) {
					$response['success'] = true;
					foreach ($getfiles as $k => $value) {
						$output_dir = DO_LIST_COMMENT;
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

	public function requests($type = 'main') {

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
		$this->DoList->unbindModel(array("hasMany" => array("DoListUser")));
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
			'order' => 'DoListUser.approved ASC',
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

	public function getTDCount($type = null) {
		$user_id = $this->Session->read("Auth.User.id");
		$conditions = array();
		$this->DoList->recursive = 1;

		$this->DoList->unbindModel(array("hasMany" => array("DoListUser")));
		$this->DoList->bindModel(array("hasOne" => array("DoListUser")));

		$countTodo = $this->DoList->find('count', [
			'conditions' => [
				'DoListUser.user_id' => $user_id,
				'DoListUser.owner_id !=' => $user_id,
				'DoListUser.approved <=' => 0,
				'OR' => [
					'DoList.end_date >=' => date('Y-m-d'),
					'DoList.end_date IS NULL',
				],
			],
		]);

		$count = $countTodo;

		return (isset($count) && !empty($count)) ? $count : 0;
	}

	public function getSubTodoRequestCount() {
		$user_id = $this->Session->read("Auth.User.id");
		$conditions = array();
		$this->DoList->recursive = 1;

		$this->DoList->unbindModel(array("hasMany" => array("DoListUser")));
		$this->DoList->bindModel(array("hasOne" => array("DoListUser")));

		$countTodo = $this->DoList->find('count', [
			'conditions' => [
				'DoListUser.user_id' => $user_id,
				'DoListUser.owner_id !=' => $user_id,
				'DoList.parent_id !=' => 0,
				'DoListUser.approved <=' => 0,
			],
		]);
		$count = $countTodo;

		return (isset($count) && !empty($count)) ? $count : 0;
	}

	public function getMainTodoRequestCount() {
		$user_id = $this->Session->read("Auth.User.id");
		$conditions = array();
		$this->DoList->recursive = 1;

		$this->DoList->unbindModel(array("hasMany" => array("DoListUser")));
		$this->DoList->bindModel(array("hasOne" => array("DoListUser")));

		$countTodo = $this->DoList->find('count', [
			'conditions' => [
				'DoListUser.user_id' => $user_id,
				'DoListUser.owner_id !=' => $user_id,
				'DoList.parent_id' => 0,
				'DoListUser.approved <=' => 0,
			],
		]);
		$count = $countTodo;

		return (isset($count) && !empty($count)) ? $count : 0;
	}

	public function getMyTodoCount() {
		$user_id = $this->Session->read("Auth.User.id");
		$conditions = array();
		$this->DoList->recursive = 1;
		$project_id = $day = $start_date = $end_date = null;

		if (isset($this->params['named']['project']) && !empty($this->params['named']['project'])) {
			$project_id = $this->params['named']['project'];
		}
		if (isset($this->params['named']['day']) && !empty($this->params['named']['day'])) {
			$day = $this->params['named']['day'];
		}
		if (isset($this->params['named']['sdate']) && !empty($this->params['named']['sdate'])) {
			$start_date = $this->params['named']['sdate'];
			$end_date = $this->params['named']['edate'];
			$conditions['AND'][] = array('DoList.start_date >=' => $start_date);
			$conditions['AND'][] = array('DoList.end_date <=' => $end_date);
		}

		$conditions['And'][] = ['DoList.user_id' => $user_id];
		$conditions['And'][] = ['DoList.is_archive' => 0];

		if (isset($project_id) && !empty($project_id)) {
			$conditions['AND'][] = array('DoList.project_id' => $project_id);
		} else {
			$conditions['AND'][] = array('DoList.project_id IS NULL');
		}

		if (isset($day) && !empty($day)) {
			if ($day == 'active') {
				$conditions['AND'][] = array('DoList.sign_off' => 0);
			} else if ($day == 'today') {
				$conditions['AND'][] = array('DoList.start_date <=' => date('Y-m-d'));
				$conditions['AND'][] = array('DoList.end_date >=' => date('Y-m-d'));
				$conditions['AND'][] = array('DoList.sign_off !=' => 1);
			} else if ($day == 'tomorrow') {
				$conditions['AND'][] = array('DoList.start_date' => date('Y-m-d', strtotime("+1 day")));
				$conditions['AND'][] = array('DoList.sign_off !=' => 1);
			} else if ($day == 'upcoming') {
				$conditions['AND'][] = array('DoList.start_date >=' => date('Y-m-d', strtotime("+2 day")));
				$conditions['AND'][] = array('DoList.sign_off !=' => 1);
			} else if ($day == 'notset') {
				$conditions['AND'][] = array('DoList.start_date IS NULL');
				$conditions['AND'][] = array('DoList.end_date IS NULL');
				$conditions['AND'][] = array('DoList.sign_off !=' => 1);
			} else if ($day == 'completed') {
				$conditions['AND'][] = array('DoList.sign_off' => 1);
			} else if ($day == 'overdue') {
				$conditions['AND'][] = array('DoList.end_date <' => date('Y-m-d'));
				$conditions['AND'][] = array('DoList.sign_off !=' => 1);
			}
		}

		$countTodo = $this->DoList->find('count', [
			'conditions' => $conditions,
		]);
		$count = $countTodo;

		return (isset($count) && !empty($count)) ? $count : 0;
	}

	public function getRecTodoCount() {
		$user_id = $this->Session->read("Auth.User.id");
		$conditions = array();
		$this->DoList->recursive = 1;
		$project_id = $day = $start_date = $end_date = null;
		if (isset($this->params['named']['project']) && !empty($this->params['named']['project'])) {
			$project_id = $this->params['named']['project'];
		}
		if (isset($this->params['named']['day']) && !empty($this->params['named']['day'])) {
			$day = $this->params['named']['day'];
		}
		if (isset($this->params['named']['sdate']) && !empty($this->params['named']['sdate'])) {
			$start_date = $this->params['named']['sdate'];
			$end_date = $this->params['named']['edate'];
			$conditions['AND'][] = array('DoList.start_date >=' => $start_date);
			$conditions['AND'][] = array('DoList.end_date <=' => $end_date);
		}
		$this->DoList->unbindModel(array("hasMany" => array("DoListUser")));
		$this->DoList->bindModel(array("hasOne" => array("DoListUser")));
		$conditions['And'][] = [
			'DoListUser.user_id' => $user_id,
			'DoListUser.owner_id !=' => $user_id,
			'DoListUser.approved' => 1,
		];
		if (isset($project_id) && !empty($project_id)) {
			$conditions['AND'][] = array('DoList.project_id' => $project_id);
		} else {
			$conditions['AND'][] = array('DoList.project_id IS NULL');
		}

		if (isset($day) && !empty($day)) {
			if ($day == 'active') {
				$conditions['AND'][] = array('DoList.sign_off' => 0);
			} else if ($day == 'today') {
				$conditions['AND'][] = array('DoList.start_date <=' => date('Y-m-d'));
				$conditions['AND'][] = array('DoList.end_date >=' => date('Y-m-d'));
				$conditions['AND'][] = array('DoList.sign_off !=' => 1);
			} else if ($day == 'tomorrow') {
				$conditions['AND'][] = array('DoList.start_date' => date('Y-m-d', strtotime("+1 day")));
				$conditions['AND'][] = array('DoList.sign_off !=' => 1);
			} else if ($day == 'upcoming') {
				$conditions['AND'][] = array('DoList.start_date >=' => date('Y-m-d', strtotime("+2 day")));
				$conditions['AND'][] = array('DoList.sign_off !=' => 1);
			} else if ($day == 'notset') {
				$conditions['AND'][] = array('DoList.start_date IS NULL');
				$conditions['AND'][] = array('DoList.end_date IS NULL');
				$conditions['AND'][] = array('DoList.sign_off !=' => 1);
			} else if ($day == 'completed') {
				$conditions['AND'][] = array('DoList.sign_off' => 1);
			} else if ($day == 'overdue') {
				$conditions['AND'][] = array('DoList.end_date <' => date('Y-m-d'));
				$conditions['AND'][] = array('DoList.sign_off !=' => 1);
			}
		}
		$conditions['And'][] = ['DoList.is_archive' => 0];
		$countTodo = $this->DoList->find('count', [
			'conditions' => $conditions,
		]);
		$count = $countTodo;

		return (isset($count) && !empty($count)) ? $count : 0;
	}

	public function getIsArchiveCount() {
		$user_id = $this->Session->read("Auth.User.id");
		$conditions = array();
		$this->DoList->recursive = 1;
		$project_id = $day = $start_date = $end_date = null;
		if (isset($this->params['named']['project']) && !empty($this->params['named']['project'])) {
			$project_id = $this->params['named']['project'];
		}
		if (isset($this->params['named']['day']) && !empty($this->params['named']['day'])) {
			$day = $this->params['named']['day'];
		}
		if (isset($this->params['named']['sdate']) && !empty($this->params['named']['sdate'])) {
			$start_date = $this->params['named']['sdate'];
			$end_date = $this->params['named']['edate'];
			$conditions['AND'][] = array('DoList.start_date >=' => $start_date);
			$conditions['AND'][] = array('DoList.end_date <=' => $end_date);
		}
		$this->DoList->unbindModel(array("hasMany" => array("DoListUser")));
		$this->DoList->bindModel(array("hasOne" => array("DoListUser")));

		if (isset($project_id) && !empty($project_id)) {
			$conditions['AND'][] = array('DoList.project_id' => $project_id);
		} else {
			$conditions['AND'][] = array('DoList.project_id IS NULL');
		}

		if (isset($day) && !empty($day)) {
			if ($day == 'active') {
				$conditions['AND'][] = array('DoList.sign_off' => 0);
			} else if ($day == 'today') {
				$conditions['AND'][] = array('DoList.start_date <=' => date('Y-m-d'));
				$conditions['AND'][] = array('DoList.end_date >=' => date('Y-m-d'));
				$conditions['AND'][] = array('DoList.sign_off !=' => 1);
			} else if ($day == 'tomorrow') {
				$conditions['AND'][] = array('DoList.start_date' => date('Y-m-d', strtotime("+1 day")));
				$conditions['AND'][] = array('DoList.sign_off !=' => 1);
			} else if ($day == 'upcoming') {
				$conditions['AND'][] = array('DoList.start_date >=' => date('Y-m-d', strtotime("+2 day")));
				$conditions['AND'][] = array('DoList.sign_off !=' => 1);
			} else if ($day == 'notset') {
				$conditions['AND'][] = array('DoList.start_date IS NULL');
				$conditions['AND'][] = array('DoList.end_date IS NULL');
				$conditions['AND'][] = array('DoList.sign_off !=' => 1);
			} else if ($day == 'completed') {
				$conditions['AND'][] = array('DoList.sign_off' => 1);
			} else if ($day == 'overdue') {
				$conditions['AND'][] = array('DoList.end_date <' => date('Y-m-d'));
				$conditions['AND'][] = array('DoList.sign_off !=' => 1);
			}
		}
		$count = 0;

		$conditions['And'][] = ['DoList.is_archive' => 1];
		$conditions['And'][] = ['DoList.user_id' => $user_id];
		$countTodo = $this->DoList->find('count', [
			'conditions' => $conditions,
		]);

		//  $count = $countTodo;

		/* received */
		$rec_conditions = array();
		$project_id = $day = $start_date = $end_date = null;
		if (isset($this->params['named']['project']) && !empty($this->params['named']['project'])) {
			$project_id = $this->params['named']['project'];
		}
		if (isset($this->params['named']['day']) && !empty($this->params['named']['day'])) {
			$day = $this->params['named']['day'];
		}
		if (isset($this->params['named']['sdate']) && !empty($this->params['named']['sdate'])) {
			$start_date = $this->params['named']['sdate'];
			$end_date = $this->params['named']['edate'];
			$rec_conditions['AND'][] = array('DoList.start_date >=' => $start_date);
			$rec_conditions['AND'][] = array('DoList.end_date <=' => $end_date);
		}
		$this->DoList->unbindModel(array("hasMany" => array("DoListUser")));
		$this->DoList->bindModel(array("hasOne" => array("DoListUser")));

		if (isset($project_id) && !empty($project_id)) {
			$rec_conditions['AND'][] = array('DoList.project_id' => $project_id);
		} else {
			$rec_conditions['AND'][] = array('DoList.project_id IS NULL');
		}

		if (isset($day) && !empty($day)) {
			if ($day == 'active') {
				$rec_conditions['AND'][] = array('DoList.sign_off' => 0);
			} else if ($day == 'today') {
				$rec_conditions['AND'][] = array('DoList.start_date <=' => date('Y-m-d'));
				$rec_conditions['AND'][] = array('DoList.end_date >=' => date('Y-m-d'));
				$rec_conditions['AND'][] = array('DoList.sign_off !=' => 1);
			} else if ($day == 'tomorrow') {
				$rec_conditions['AND'][] = array('DoList.start_date' => date('Y-m-d', strtotime("+1 day")));
				$rec_conditions['AND'][] = array('DoList.sign_off !=' => 1);
			} else if ($day == 'upcoming') {
				$rec_conditions['AND'][] = array('DoList.start_date >=' => date('Y-m-d', strtotime("+2 day")));
				$rec_conditions['AND'][] = array('DoList.sign_off !=' => 1);
			} else if ($day == 'notset') {
				$rec_conditions['AND'][] = array('DoList.start_date IS NULL');
				$rec_conditions['AND'][] = array('DoList.end_date IS NULL');
				$rec_conditions['AND'][] = array('DoList.sign_off !=' => 1);
			} else if ($day == 'completed') {
				$rec_conditions['AND'][] = array('DoList.sign_off' => 1);
			} else if ($day == 'overdue') {
				$rec_conditions['AND'][] = array('DoList.end_date <' => date('Y-m-d'));
				$rec_conditions['AND'][] = array('DoList.sign_off !=' => 1);
			}
		}
		$rec_do_lists = 0;
		$user_data = $this->DoListUser->find('all', [
			'conditions' => [
				'DoListUser.user_id' => $user_id,
				'DoListUser.owner_id !=' => $user_id,
				'DoListUser.approved' => 1,
			],
			'fields' => ['DoListUser.do_list_id'],
		]);

		if (isset($user_data) && !empty($user_data)) {
			$user_data = Set::extract($user_data, '/DoListUser/do_list_id');
			$rec_conditions['AND'][] = [
				'DoList.id' => $user_data,
				'DoList.is_archive' => 1,
			];

			$rec_do_lists = $this->DoList->find("count", array(
				"conditions" => $rec_conditions,
				"recursive" => -1,
			)
			);
		}

		$count = $rec_do_lists + $countTodo;

		return (isset($count) && !empty($count)) ? $count : 0;
	}

	public function getArchiveTodoCount() {
		$user_id = $this->Session->read("Auth.User.id");
		$rec_do_lists = 0;
		$user_data = $this->DoListUser->find('all', [
			'conditions' => [
				'DoListUser.user_id' => $user_id,
				'DoListUser.owner_id !=' => $user_id,
				'DoListUser.approved' => 1,
			],
			'fields' => ['DoListUser.do_list_id'],
		]);

		if (isset($user_data) && !empty($user_data)) {
			$user_data = Set::extract($user_data, '/DoListUser/do_list_id');
			$rec_conditions['AND'][] = [
				'DoList.id' => $user_data,
				'DoList.is_archive' => 1,
			];

			$rec_do_lists = $this->DoList->find("count", array(
				"conditions" => $rec_conditions,
				"recursive" => -1,
			)
			);
		}

		$count = $this->DoList->find("count", array("conditions" => array("DoList.user_id" => $user_id, "DoList.is_archive" => 1)));
		return $count + $rec_do_lists;
	}

	public function AcceptToDoRequest($id = null, $request = null) {
		$this->layout = 'ajax';
		$user_id = $this->Auth->user('id');
		$this->autoRender = false;
		$response = [
			'success' => false,
			'msg' => '',
			'content' => null,
		];
		if ($this->request->is("post")) {
			$msg = null;
			if (isset($id) && !empty($request)) {
				$msg = $request == '1' ? 'accepted' : 'declined';
				$old = $this->DoList->findById($id);
				$type = '';
				if (isset($old['DoList']['parent_id']) && $old['DoList']['parent_id'] > 0) {
					$type = ' Sub ';
				}
				$this->request->data['DoListUser']['id'] = $id;
				$this->request->data['DoListUser']['approved'] = $request;
				$this->DoListUser->save($this->request->data);
				$this->Session->setFlash('You have successfully ' . $msg . $type . ' To-do request', 'success');
				$response = [
					'success' => true,
					'msg' => 'You have successfully ' . $msg . $type . ' To-do request.',
					'content' => null,
				];
			}
			echo json_encode($response);
			exit();
		}
	}

	public function tododetails($todo_id = null) {

		if (isset($todo_id) && !empty($todo_id) ) {
			if(!dbExists('DoList', $todo_id)){
				$this->redirect(array('controller' => 'todos', 'action' => 'index'));
			}
		}

		$project_name = 'No Project';
		$this->layout = 'inner';
		$user_id = $this->Auth->user('id');
		$data = $type = null;

		$this->DoList->id = $todo_id;
		if (!$this->DoList->exists()) {
			$this->Session->setFlash(__('Invalid To-do.'), 'error');
			$this->redirect(array('action' => 'requests'));
		}
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
			$this->set('title_for_layout', __($type . ' To-do Details', true));
			$this->set('page_subheading', __('View ' . $type . ' To-do details', true));
			$this->set('page_heading', __($project_name, true));

			$crumb = ['last' => ['data' => ['title' => $type . ' To-do request', 'data-original-title' => $type . ' To-do Request']]];


		} else {
			$this->Session->setFlash(__('Invalid todo.'), 'error');
			$this->redirect(array('action' => 'requests'));
		}

		$this->set('type', $type);
		$this->set(compact("tododata", "todouserdata"));

		$this->set('crumb', $crumb);
	}

	public function get_status($id = null, $my = null) {
		$this->DoList = ClassRegistry::init('DoList');
		$user_id = $this->Session->read("Auth.User.id");
		$returndata = "N/A";
		$this->DoList->recursive = 2;
		$this->DoList->unbindModel(array("hasMany" => array("DoListUser")));
		$this->DoList->bindModel(array("hasOne" => array("DoListUser")));
		if (isset($my) && $my == true) {
			$data = $this->DoList->find("first", array("conditions" => array('DoList.id' => $id)));

			if (isset($data['DoList']['sign_off']) && $data['DoList']['sign_off'] == 1) {
				$returndata = 'Completed';
			} else if (isset($data['DoList']['end_date']) && $data['DoList']['sign_off'] == 0 && !empty($data['DoList']['end_date']) && strtotime($data['DoList']['end_date']) < strtotime(date("Y-m-d"))) {
				$returndata = 'Overdue';
			} else if (isset($data['DoList']['start_date']) && !empty($data['DoList']['start_date']) && strtotime($data['DoList']['start_date']) <= strtotime(date("Y-m-d")) && strtotime($data['DoList']['end_date']) >= strtotime(date("Y-m-d"))) {
				$returndata = 'Progressing';
			} else if (isset($data['DoList']['start_date']) && !empty($data['DoList']['end_date']) && !empty($data['DoList']['start_date']) && strtotime($data['DoList']['start_date']) > strtotime(date("Y-m-d")) && strtotime($data['DoList']['end_date']) >= strtotime(date("Y-m-d"))) {
				$returndata = 'Not Started';
			}

		} else {
			$data = $this->DoList->find("first", array("conditions" => array('DoListUser.user_id' => $user_id, 'DoList.id' => $id)));
			if (isset($data['DoList']['sign_off']) && $data['DoList']['sign_off'] == 1) {
				$returndata = 'Completed';
			} else if (isset($data['DoList']['end_date']) && $data['DoList']['sign_off'] == 0 && !empty($data['DoList']['end_date']) && strtotime($data['DoList']['end_date']) < strtotime(date("Y-m-d")) && $data['DoListUser']['approved'] != 2) {
				$returndata = 'Overdue';
			} else if (isset($data['DoList']['start_date']) && !empty($data['DoList']['start_date']) && strtotime($data['DoList']['start_date']) <= strtotime(date("Y-m-d")) && strtotime($data['DoList']['end_date']) >= strtotime(date("Y-m-d")) && $data['DoListUser']['approved'] == 1) {
				$returndata = 'Progressing';
			} else if (isset($data['DoList']['start_date']) && !empty($data['DoList']['start_date']) && strtotime($data['DoList']['start_date']) <= strtotime(date("Y-m-d")) && strtotime($data['DoList']['end_date']) >= strtotime(date("Y-m-d")) && $data['DoListUser']['approved'] == 0) {
				$returndata = 'Open';
			} else if (isset($data['DoList']['start_date']) && $data['DoListUser']['approved'] != 2 && !empty($data['DoList']['end_date']) && !empty($data['DoList']['start_date']) && strtotime($data['DoList']['start_date']) > strtotime(date("Y-m-d")) && strtotime($data['DoList']['end_date']) >= strtotime(date("Y-m-d"))) {
				$returndata = 'Not Started';
			} else if (isset($data['DoList']['start_date']) && $data['DoListUser']['approved'] == 2 && !empty($data['DoList']['end_date']) && !empty($data['DoList']['start_date']) && strtotime($data['DoList']['start_date']) > strtotime(date("Y-m-d")) && strtotime($data['DoList']['end_date']) >= strtotime(date("Y-m-d"))) {
				$returndata = 'Declined';
			} else if (isset($data['DoListUser']['approved']) && isset($data['DoListUser']['approved']) && $data['DoListUser']['approved'] == 2) {
				$returndata = 'Declined';
			} else if (empty($data['DoList']['start_date']) && empty($data['DoList']['end_date']) && isset($data['DoListUser']['approved']) && $data['DoListUser']['approved'] != 2) {
				$returndata = 'Not Set';
			}
		}

		return $returndata;
	}

	public function getAllUserOnThisTodo($todoid = null) {
		$this->layout = false;
		$this->DoList->recursive = 1;
		$users = $this->DoList->find("first", array("conditions" => array("DoList.id" => $todoid)));
		// pr($users);
		$this->set(compact("users"));
		$this->render('/Todos/get_all_user_on_this_todo');
	}

	public function comment_document_delete() {
		$this->layout = 'ajax';
		$this->autoRender = false;
		if ($this->request->isAjax()) {
			$response = [
				'success' => false,
				'msg' => 'Sorry accor some error.',
				'content' => null,
			];
			if (isset($this->request->data['id']) && !empty($this->request->data['id']) && $this->request->data['id'] != 'null') {
				$id = $this->request->data['id'];
				$fielname = $this->request->data['file_name'];
				$conditions = array("DoListCommentUpload.file_name" => $fielname);
				if (isset($id) && !empty($id)) {
					$conditions['AND'][] = array("DoListCommentUpload.id" => $id);
				}
				$old = $this->DoListCommentUpload->find("first", array("conditions" => $conditions));
				$this->request->data['DoListCommentUpload']['id'] = $old['DoListCommentUpload']['id'];
				$filePath = TODOCOMMENT . $old['DoListCommentUpload']['file_name'];
				if ($this->DoListCommentUpload->delete($this->request->data['DoListCommentUpload']['id'])) {
					if (file_exists($filePath)) {
						unlink($filePath);
					}
					$response = [
						'success' => true,
						'msg' => 'The to-do comment attachment has been removed successfully.',
						'content' => null,
					];
					echo json_encode($response);
					exit();
				} else {
					echo json_encode($response);
					exit();
				}
			} else {

				$filePath = TODOCOMMENT . $this->request->data['file_name'];
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

	public function delete_doc() {
		$this->layout = 'ajax';
		$this->autoRender = false;
		if ($this->request->isAjax()) {
			$response = [
				'success' => false,
				'msg' => 'Sorry accor some error.',
				'content' => null,
			];
			if (isset($this->request->data['id']) && !empty($this->request->data['id']) && $this->request->data['id'] != 'null') {
				$id = $this->request->data['id'];
				if (isset($id) && !empty($id)) {
					$conditions['AND'][] = array("DoListUpload.id" => $id);
				}
				$old = $this->DoListUpload->find("first", array("conditions" => $conditions));
				$this->request->data['DoListUpload']['id'] = $old['DoListUpload']['id'];
				$filePath = TODO . $old['DoListUpload']['file_name'];
				if ($this->DoListUpload->delete($this->request->data['DoListUpload']['id'])) {
					if (file_exists($filePath)) {
						unlink($filePath);
					}
					$response = [
						'success' => true,
						'msg' => 'The to-do attachment has been removed successfully.',
						'content' => null,
					];
					echo json_encode($response);
					exit();
				} else {
					echo json_encode($response);
					exit();
				}
			} else {

				$filePath = TODO . $this->request->data['file_name'];
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

	public function todoDeleteEmail($todoname = null, $todo_id = null, $todotype = 'To-do') {

		$view = new View();
		$commonHelper = $view->loadHelper('Common');
		$all_owner = array();
		$loggedinuserss = array();
		$group = $this->objView->loadHelper('Group');
		$all_owner = $group->do_list_users($todo_id,1);

		$data = $this->DoList->find('first', [
			'conditions' => [
				'DoList.id' => $todo_id,
			],
			'recursive' => -1,
		]);

		$project_id = '';
		if (isset($all_owner) && !empty($all_owner)) {
			if (isset($data['DoList']['user_id'])) {
				$loggedinuserss[] = $data['DoList']['user_id'];
				$project_id = ( isset($data['DoList']['project_id']) && !empty($data['DoList']['project_id']) )? $data['DoList']['project_id'] : '';
				$all_owner = array_merge($all_owner, $loggedinuserss);
			}
		}

		$todoAction = SITEURL . 'todos/index/dolist_id:'.$todo_id;
		if( !empty($project_id) ){
			$todoAction = SITEURL . 'todos/index/project:'.$project_id.'/dolist_id:'.$todo_id;
		}


		$all_owner = array_unique(array_filter($all_owner));

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

					$usersDetails = $this->User->findById($valData);

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

					$deletedUser = $this->User->findById($this->Session->read("Auth.User.id"));
					$loggedInUser = $deletedUser['UserDetail']['first_name'] . ' ' . $deletedUser['UserDetail']['last_name'];

					$email = new CakeEmail();
					$email->config('Smtp');
					$email->from(array(ADMIN_FROM_EMAIL => SITENAME));
					$email->to($usersDetails['User']['email']);
					if ($todotype == 'To-do') {
						$emailSubject = 'To-do';
					} else {
						$emailSubject = 'Sub To-do';
					}
					$email->subject(SITENAME . ': ' . $emailSubject . ' deleted');
					$email->template('todo_delete_email');
					$email->emailFormat('html');
					$email->viewVars(array('todoname' => $todoname, 'owner_name' => $owner_name, 'deletedby' => $loggedInUser, 'todotype' => $todotype, 'open_page' => $todoAction));

					if ($this->check_email_permission($valData, 'todo_delete') == true) {
						$email->send();
					}
				}
			}
		}

	}

	public function todoSignOffEmail($todoname = null, $todo_id = null, $todotype = 'To-do') {

		$view = new View();
		$commonHelper = $view->loadHelper('Common');
		$all_owner = array();
		$loggedinuserss = array();
		$group = $this->objView->loadHelper('Group');
		$all_owner = $group->do_list_users($todo_id,1);

		$data = $this->DoList->find('first', [
			'conditions' => [
				'DoList.id' => $todo_id,
			],
			'recursive' => -1,
		]);
		$project_id = '';
		if (isset($all_owner) && !empty($all_owner)) {
			if (isset($data['DoList']['user_id'])) {
				$loggedinuserss[] = $data['DoList']['user_id'];
				$project_id = $data['DoList']['project_id'];
				$all_owner = array_merge($all_owner, $loggedinuserss);
			}
		}
		$all_owner = array_unique(array_filter($all_owner));

		$pageAction = SITEURL . 'todos/index/dolist_id:'.$todo_id;
		if( !empty($project_id) ){
			$pageAction = SITEURL . 'todos/index/project:'.$project_id.'/dolist_id:'.$todo_id;
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

					$usersDetails = $this->User->findById($valData);
					// $owner_name = $usersDetails['UserDetail']['first_name'] . ' ' . $usersDetails['UserDetail']['last_name'];

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

					$deletedUser = $this->User->findById($this->Session->read("Auth.User.id"));
					$loggedInUser = $deletedUser['UserDetail']['first_name'] . ' ' . $deletedUser['UserDetail']['last_name'];

					$email = new CakeEmail();
					$email->config('Smtp');
					$email->from(array(ADMIN_FROM_EMAIL => SITENAME));
					$email->to($usersDetails['User']['email']);
					$email->subject(SITENAME . ': ' . $todotype . ' sign-off');
					$email->template('todo_signoff_email');
					$email->emailFormat('html');
					$email->viewVars(array('todoname' => $todoname, 'owner_name' => $owner_name, 'signedoffby' => $loggedInUser, 'todotype' => $todotype,'open_page'=>$pageAction));

					if ($this->check_email_permission($valData, 'todo_signoff') == true) {
						$email->send();
						return true;
					}
					// pr(ADMIN_EMAIL);
				}
			}
		}
		// pr($all_owner, 1);
		return true;

	}

	public function todoScheduleOverdueEmailCron() {
		$this->layout = false;
		$this->autoRender = false;

		$view = new View();
		$commonHelper = $view->loadHelper('Common');
		$groupHelper = $view->loadHelper('Group');

		$todoList = $this->DoList->find('all', ['conditions' => ['DoList.sign_off !=' => 1], 'recursive' => -1]);

		if (isset($todoList) && !empty($todoList) && count($todoList) > 0) {

			$all_owner = [];

			$currentDate = date('Y-m-d');
			foreach ($todoList as $listall) {

				//================= Overdue Days ====================================
				$daysleft = daysLeft($listall['DoList']['end_date'], $currentDate);
				if ($daysleft == 1) {

					$project_id = ( isset($listall['DoList']['project_id']) && !empty($listall['DoList']['project_id']) )? $listall['DoList']['project_id'] : '';
					$dolist_id = $listall['DoList']['id'];
					$todoname = $listall['DoList']['title'];

					$todoAction = SITEURL . 'todos/index/dolist_id:'.$dolist_id;
					$projectName = '';
					if( !empty($project_id) ){
						$todoAction = SITEURL . 'todos/index/project:'.$project_id.'/dolist_id:'.$dolist_id;
						$projectName = getFieldDetail('Project', $project_id, 'title');
					}

					$all_owner = array_merge(array($listall['DoList']['user_id']), $groupHelper->do_list_users($listall['DoList']['id']));

					if (isset($all_owner) && !empty($all_owner)) {
						if (($key = array_search($this->Session->read('Auth.User.id'), $all_owner)) !== false) {
							unset($all_owner[$key]);
						}
					}
					if (isset($all_owner) && !empty($all_owner)) {
						foreach ($all_owner as $valData) {

							//echo $valData."<br>";

							$notifiragstatus = $this->EmailNotification->find('first', ['conditions' => ['notification_type' => 'to_dos', 'personlization' => 'todo_overdue', 'user_id' => $valData]]);

							$usersDetails = $this->User->find('first', array('conditions' => array('User.id' => $valData)));

							if ((!isset($notifiragstatus['EmailNotification']['email']) || $notifiragstatus['EmailNotification']['email'] == 1) && (!isset($usersDetails['User']['email_notification']) || $usersDetails['User']['email_notification'] == 1)) {

								// $owner_name = $usersDetails['UserDetail']['first_name'] . ' ' . $usersDetails['UserDetail']['last_name'];

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

								$email = new CakeEmail();
								$email->config('Smtp');
								$email->from(array(ADMIN_FROM_EMAIL => SITENAME));
								$email->to($usersDetails['User']['email']);
								$email->subject(SITENAME . ': To-do overdue');
								$email->template('todo_schedule_overdue');
								$email->emailFormat('html');
								$email->viewVars(array('todoname' => $todoname, 'owner_name' => $owner_name, 'todoAction' => $todoAction, 'open_page' => $todoAction,'projectName'=>$projectName));
								$email->send();

							}
						}
					}

					if (isset($all_owner) && !empty($all_owner)) {

						/************** socket messages **************/
						if (SOCKET_MESSAGES) {
							$r_users = $all_owner;
							$open_users = null;
							if (isset($r_users) && !empty($r_users)) {
								foreach ($r_users as $key1 => $value1) {
									if (web_notify_setting($value1, 'to_dos', 'todo_overdue')) {
										$open_users[] = $value1;
									}
								}
							}

							$sub_heading = 'N/A';
							if (isset($listall['DoList']['project_id']) && !empty($listall['DoList']['project_id'])) {
								$sub_heading = strip_tags(getFieldDetail('Project', $listall['DoList']['project_id'], 'title'));
							}
							$ts_parent_id = $listall['DoList']['parent_id'];
							$ts_text = '';
							if (!empty($ts_parent_id)) {
								$ts_text = 'Sub ';
							}
							$content = [
								'notification' => [
									'type' => 'todo_overdue',
									'created_id' => '',
									'project_id' => $listall['DoList']['project_id'],
									'refer_id' => $listall['DoList']['id'],
									'creator_name' => '',
									'subject' => $ts_text . 'To-do overdue',
									'heading' => $ts_text . 'To-do: ' . htmlentities(getFieldDetail('DoList', $listall['DoList']['id'], 'title'), ENT_QUOTES, "UTF-8"),
									'sub_heading' => 'Project: ' . $sub_heading,
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

				}

			}

		}

	}

	public function getdouploadlist() {
		$view = new View();
		$ViewModel = $view->loadHelper('ViewModel');

		if ($this->request->isAjax()) {
			if ($this->request->is('post') || $this->request->is('put')) {

				if (isset($this->request->data['do_list_id']) && !empty($this->request->data['do_list_id'])) {
					$uploadlists = array();
					$uploadlists = $ViewModel->todoUploadlist($this->request->data['do_list_id']);
					if (isset($uploadlists) && !empty($uploadlists)) {
						echo json_encode($uploadlists);
						exit;
					}
				}
			}
		}
	}

}
