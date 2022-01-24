<?php

App::uses('Controller/Component', 'Auth', 'Session', 'RequestHandler');
App::uses('CakeEmail', 'Network/Email', 'Http/Client');
App::uses('HttpSocket', 'Network/Http');

class WorkspacesController extends AppController {

	public $name = 'Workspaces';
	public $components = array('Mpdf', 'Common', 'Group', 'Users');
	public $uses = array('Workspace', 'Area', 'Project', "Element", "ProjectPermission", "WorkspacePermission", "EmailNotification","SignoffWorkspace");
	public $helpers = array('Html', 'Form', 'Session', 'Time', 'Text', 'Common');
	public $user_id = null;
	public $pagination = null;
	public $objView = null;

	public function beforeFilter() {

		parent::beforeFilter();

		$view = new View();
		$this->objView = $view;

		$this->user_id = $this->Auth->user('id');

		$this->Auth->allow('workspaceScheduleOverdueEmailCron');

		$this->set('controller', 'workspaces');
		$this->pagination['limit'] = 4;
		$this->pagination['summary_model'] = 'Workspace';
		$this->pagination['options'] = array(
			'url' => array_merge(
				array(
					'controller' => $this->request->params['controller'],
					'action' => 'get_more',
				),
				$this->request->params['pass'],
				$this->request->params['named']
			),
		);

		$this->set('JeeraPaging', $this->pagination);


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

	public function admin_configureWorkspaces() {

		$this->loadModel('ProjectWorkspace');

		$projectId = $this->request->params['project_id'];

		$workspacesList = null;

		$res = $this->ProjectWorkspace->find('all', array(
			'joins' => array(
				array(
					'table' => 'projects',
					'alias' => 'ProjectsJ',
					'type' => 'INNER',
					'conditions' => ['ProjectsJ.id' => $projectId],
				),
			),
			'conditions' => ['ProjectWorkspace.project_id' => $projectId],
			'fields' => ['Workspace.id', 'Workspace.title', 'ProjectWorkspace.id as pwid', 'ProjectWorkspace.leftbar_status'],
			'recursive' => 1,
			'order' => 'Project.created DESC',
		)
		);

		if (isset($res) && !empty($res)) {
			$workspacesList = $res;
		}

		return $workspacesList;
	}

	public function configureWorkspaces() {

		$this->loadModel('ProjectWorkspace');

		$projectId = $this->request->params['project_id'];

		$workspacesList = null;

		$res = $this->ProjectWorkspace->find('all', array(
			'joins' => array(
				array(
					'table' => 'projects',
					'alias' => 'ProjectsJ',
					'type' => 'INNER',
					'conditions' => ['ProjectsJ.id' => $projectId],
				),
			),
			'conditions' => ['ProjectWorkspace.project_id' => $projectId],
			'fields' => ['Workspace.id', 'Workspace.title', 'ProjectWorkspace.id as pwid', 'ProjectWorkspace.leftbar_status'],
			'recursive' => 1,
			'order' => 'Project.created DESC',
		)
		);

		if (isset($res) && !empty($res)) {
			$workspacesList = $res;
		}

		return $workspacesList;
	}

	public function admin_workspaceListForProject() {
		$projectId = $this->request->params['project_id'];
		$this->loadModel('ProjectWorkspace');
		$this->ProjectWorkspace->bindModel(array('belongsTo' => array('Workspace' => array(
			'className' => 'Workspace',
			'foreignKey' => 'workspace_id',
		))));
		$workspacesList = $this->ProjectWorkspace->find('all', array('conditions' => array('ProjectWorkspace.project_id' => $projectId)));

		$selectedWorkspaces = array();
		foreach ($workspacesList as $key => $val) {
			$selectedWorkspaces[$val['Workspace']['id']] = $val['Workspace']['title'];
		}
		//pr($workspacesList);
		//$workspacesList = $this->Workspace->find('list',array('fields'=>array('Workspace.id', 'Workspace.title')));
		return $selectedWorkspaces;
	}

	public function workspaceListForProject() {
		$projectId = $this->request->params['project_id'];
		$this->loadModel('ProjectWorkspace');
		$this->ProjectWorkspace->bindModel(array('belongsTo' => array('Workspace' => array(
			'className' => 'Workspace',
			'foreignKey' => 'workspace_id',
		))));
		$workspacesList = $this->ProjectWorkspace->find('all', array('conditions' => array('ProjectWorkspace.project_id' => $projectId)));

		$selectedWorkspaces = array();
		foreach ($workspacesList as $key => $val) {
			$selectedWorkspaces[$val['Workspace']['id']] = $val['Workspace']['title'];
		}
		//pr($workspacesList);
		//$workspacesList = $this->Workspace->find('list',array('fields'=>array('Workspace.id', 'Workspace.title')));
		return $selectedWorkspaces;
	}

	public function update_workspace($project_id = null, $id = null) {

		if (!isset($project_id) || !isset($id)) {
			$this->redirect(array('controller' => 'projects', 'action' => 'index'));
		}
		$this->set("project_id", $project_id);
		$user_id = $this->Auth->user('id');

		$this->layout = 'inner';

		$data = null;

		$this->set('title_for_layout', __('Update Workspace', true));

		$data['project_id'] = $project_id;

		$data['id'] = $id;


		$signoff_comment = $this->SignoffWorkspace->find('count', array('conditions'=>array('SignoffWorkspace.workspace_id'=>$id) ));
		$this->set('signoff_comment', $signoff_comment);

		if ($this->request->is('post') || $this->request->is('put')) {

			$this->request->data['Workspace']['task_type'] = 'update';
			$this->request->data['Workspace']['updated_user_id'] = $this->Session->read("Auth.User.id");

			$post = $this->request->data;
			$response = null;
			if (isset($id) && !empty($id)) {

				$this->Workspace->set($post);

				if ($this->Workspace->validates()) {

					if (date('Y-m-d', strtotime($this->request->data['Workspace']['end_date'])) < date('Y-m-d', strtotime($this->request->data['Workspace']['start_date']))) {

						$this->Session->setFlash(__('Workspace End date should not be less than Workspace Start date.'), 'error');
						$this->redirect(SITEURL . 'workspaces/update_workspace/' . $project_id . '/' . $id);

					}

					// $this->request->data['Workspace']['description'] = $this->request->data['Workspace']['description'];
					$start = $this->request->data['Workspace']['start_date'];
					$end = $this->request->data['Workspace']['end_date'];
					$check = $this->Common->check_date_validation_ws($start, $end, $project_id, $id);

					if (!empty($check) && $check != null) {
						$this->Session->setFlash(__($check), 'error');
						$this->redirect(SITEURL . 'workspaces/update_workspace/' . $project_id . '/' . $id);
					}

					$this->request->data['Workspace']['start_date'] = date("Y-m-d H:i:s", strtotime($start));
					$this->request->data['Workspace']['end_date'] = date("Y-m-d H:i:s", strtotime($end));

					$this->request->data['Workspace']['color_code'] = (isset($this->request->data['Workspace']['color_code']) && !empty($this->request->data['Workspace']['color_code'])) ? $this->request->data['Workspace']['color_code'] : "bg-success";

					$this->request->data['Workspace']['create_activity'] = 1;

					$latestDate = $this->Workspace->find('first', ['conditions' => ['Workspace.id' => $id]]);

					$this->request->data['Workspace']['title'] = substr($this->request->data['Workspace']['title'], 0, 50);
					//$this->request->data['Workspace']['description'] = substr($this->request->data['Workspace']['description'], 0, 250);

					if ($this->Workspace->save($this->request->data['Workspace'])) {

						$this->Workspace->updateAll(
							array("Workspace.create_activity" => 0),
							array("Workspace.id" => $id)
						);
						// $this->Common->update_project_activity($project_id);

						// $this->Common->projectModified($project_id, $user_id);

						// ======== Start Email Notification ==============

						$startDate = strtotime($latestDate['Workspace']['start_date']);
						$endDate = strtotime($latestDate['Workspace']['end_date']);

						$willSend = false;

						if (($startDate < strtotime($this->request->data['Workspace']['start_date']) || $startDate > strtotime($this->request->data['Workspace']['start_date'])) && ($endDate < strtotime($this->request->data['Workspace']['end_date']) || $endDate > strtotime($this->request->data['Workspace']['end_date']))) {
							$willSend = true;

						} else if ($startDate < strtotime($this->request->data['Workspace']['start_date']) || $startDate > strtotime($this->request->data['Workspace']['start_date'])) {
							$willSend = true;

						} else if ($endDate < strtotime($this->request->data['Workspace']['end_date']) || $endDate > strtotime($this->request->data['Workspace']['end_date'])) {
							$willSend = true;

						} else {

						}

						if ($willSend == true) {
							$this->Common->workspaceScheduleChangeEmail($project_id, $id);
							/************** socket messages **************/
							if (SOCKET_MESSAGES) {
								$WSPUsers = $this->getWSPUsers($project_id, $id);
								$current_user_id = $this->user_id;
								if (isset($WSPUsers) && !empty($WSPUsers)) {
									if (($key = array_search($current_user_id, $WSPUsers)) !== false) {
										unset($WSPUsers[$key]);
									}
								}
								$s_open_users = null;
								if (isset($WSPUsers) && !empty($WSPUsers)) {
									foreach ($WSPUsers as $key1 => $value1) {
										if (web_notify_setting($value1, 'workspace', 'workspace_schedule_change')) {
											$s_open_users[] = $value1;
										}
									}
								}
								$userDetail = get_user_data($this->user_id);
								$content = [
									'socket' => [
										'notification' => [
											'type' => 'workspace_schedule_change',
											'created_id' => $this->user_id,
											'project_id' => $project_id,
											'refer_id' => $id,
											'creator_name' => $userDetail['UserDetail']['full_name'],
											'subject' => 'Workspace schedule change',
											'heading' => 'Workspace: ' .getFieldDetail('Workspace', $id, 'title'),
											'sub_heading' => 'Project: ' .getFieldDetail('Project', $project_id, 'title'),
											'date_time' => timezoneTimeConvertor($this->user_id, date('Y-m-d g:iA'), $format = 'd M Y g:iA') . user_country($this->user_id),
										],
									],
								];
								if (is_array($s_open_users)) {
									$content['socket']['received_users'] = array_values($s_open_users);
								}

								$content = json_encode($content['socket']);

								$request = array(
									'header' => array(
										'Content-Type' => 'application/json',
									),
								);

								$HttpSocket = new HttpSocket([
									'ssl_verify_host' => false,
									'ssl_verify_peer_name' => false,
									'ssl_verify_peer' => false,
								]);
								$results = $HttpSocket->post(CHATURL . '/serveremit', $content, $request);

							}
							/************** socket messages **************/
						}

						// ============== End Email Notification =================================

						$this->redirect(array('controller' => 'projects', 'action' => 'manage_elements', $project_id, $id));
					} else {
						$response['msg'] = "Error!!!";
					}

				} else {
					$errors = $this->Workspace->validationErrors;
					$this->set('errors', $errors);
					// pr($errors, 1);
				}
			} else {
				$response['msg'] = "Error!!!";
			}

			$data['response'] = $response;
		} else {
			$this->request->data = $this->Workspace->read(null, $id);
			$this->request->data['Workspace']['title'] = substr($this->request->data['Workspace']['title'], 0, 50);
			//$this->request->data['Workspace']['description'] = substr($this->request->data['Workspace']['description'], 0, 250);
		}

		$dateprec = STATUS_NOT_SPACIFIED;

		/* -----------Group code----------- */
		if (isset($project_id)) {
			$this->loadModel('UserProject');
			$this->loadModel('ProjectGroupUser');
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
		}
		/* -----------Group code----------- */

		if ((isset($this->request->data['Workspace']['start_date']) && !empty($this->request->data['Workspace']['start_date'])) && (isset($this->request->data['Workspace']['end_date']) && !empty($this->request->data['Workspace']['end_date']))) {

			if ((isset($this->request->data['Workspace']['start_date']) && !empty($this->request->data['Workspace']['start_date'])) && date('Y-m-d', strtotime($this->request->data['Workspace']['start_date'])) > date('Y-m-d')) {

				$dateprec = STATUS_NOT_STARTED;
			} else if ((isset($this->request->data['Workspace']['end_date']) && !empty($this->request->data['Workspace']['end_date'])) && date('Y-m-d', strtotime($this->request->data['Workspace']['end_date'])) < date('Y-m-d')) {

				$dateprec = STATUS_OVERDUE;
			} else if (isset($this->request->data['Workspace']['sign_off']) && !empty($this->request->data['Workspace']['sign_off']) && $this->request->data['Workspace']['sign_off'] > 0) {

				$dateprec = STATUS_COMPLETED;
			} else if (((isset($this->request->data['Workspace']['end_date']) && !empty($this->request->data['Workspace']['end_date'])) && (isset($this->request->data['Workspace']['start_date']) && !empty($this->request->data['Workspace']['start_date']))) && (date('Y-m-d', strtotime($this->request->data['Workspace']['start_date'])) <= date('Y-m-d')) && date('Y-m-d', strtotime($this->request->data['Workspace']['end_date'])) >= date('Y-m-d')) {

				$dateprec = STATUS_PROGRESS;
			}
		}







		//echo $dateprec;

		//die;
		$this->set('date_status', $dateprec);

		$dataW = $this->Workspace->read(null, $id);
		$data['workspace'] = (!empty($dataW)) ? $dataW['Workspace'] : null;
		$data['page_heading'] = $dataW['Workspace']['title'];

		$this->set('data', $data);

		$pdata = $this->Project->find('first', ['conditions' => ['Project.id' => $project_id], 'fields' => ['Project.id', 'Project.sign_off', 'Project.title'], 'recursive' => '-1']);

		// Get category detail of parent Project
		// if category detail found, merge it with other breadcrumb data
		// $cat_crumb = get_category_list($project_id);

		$crumb = [
			//'Project' => '/projects/lists',
			'Summary' => [
				'data' => [
					'url' => '/projects/index/' . $project_id,
					'class' => 'tipText',
					'title' => $pdata['Project']['title'],
					'data-original-title' => $pdata['Project']['title'],
				],
			],
			'last' => ['Workspace Details'],
		];

		/*if (isset($cat_crumb) && !empty($cat_crumb) && is_array($cat_crumb)) {

			$crumb = array_merge($cat_crumb, $crumb);
		}*/




		$this->setJsVar('project_id', $project_id);
		$this->setJsVar('workspace_id', $id);
		$this->set('projectdata', $pdata);
		$this->set('crumb', $crumb);
	}

	public function update_area($area_id = null) {

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

				if (isset($area_id) && !empty($area_id)) {

					$this->Workspace->Area->set($data);

					if ($this->Workspace->Area->validates()) {

						if ($this->Workspace->Area->save($this->request->data)) {

							$newValues = $this->Workspace->Area->find('first', ['conditions' => ['Area.id' => $data['Area']['id']]]);
							// pr($newValues, 1);
							$updated_title = $newValues['Area']['title'];
							/* $updated_title = ( strlen($updated_title) > 15 ) ?
								                              substr($updated_title, 0, 18).'...' :
							*/

							$response['success'] = true;
							$response['msg'] = "Success";
							$response['content'] = ['title' => $updated_title];
						} else {
							$response['msg'] = "Error!!!";
						}
					} else {
						$response['content'] = $this->validateErrors($this->Workspace->Area);
					}
				} else {
					$response['msg'] = "Error!!!";
				}

			}

			echo json_encode($response);
			exit();
		}
	}

	function add_element() {

		$this->layout = 'ajax';
		$this->autoRender = false;

		if ($this->request->isAjax()) {
			// Configure::write('debug', 0);
			$response = [
				'success' => false,
				'msg' => '',
				'content' => null,
			];
			if ($this->request->is('post') || $this->request->is('put')) {

				$this->Element->set($this->request->data);
				if ($this->Element->validates()) {
					//pr($this->request->data);
					//die;
					if ($this->Element->save($this->request->data)) {
						$response['success'] = true;
						$response['msg'] = "Success";
					} else {
						$response['msg'] = "Error!!!";
					}
				} else {
					$response['content'] = $this->validateErrors($this->Element);
				}
			}

			echo json_encode($response);
			exit();
		}
	}

	/**
	 * Update background color code of a project in list page
	 *
	 * @param  $project_id
	 *
	 * @return void
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

				$this->Workspace->id = $project_id;
				if ($this->Workspace->saveField('color_code', $post['color_code'])) {

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

	public function admin_update_color($project_id = null) {

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

				$this->Workspace->id = $project_id;
				if ($this->Workspace->saveField('color_code', $post['color_code'])) {

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

	public function show_detail($id = null) {
		$eid = encr($id);
		$did = decr($id);

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$response = $workspace_detail = array();
			if (isset($did) && !empty($did)) {
				$workspace_detail = $this->Workspace->find('first', ['conditions' => ['Workspace.id' => $did], 'recursive' => -1]);
			}
			$this->set('workspace_detail', $workspace_detail);

			$this->render('/Workspaces/partials/show_detail');
		}
	}

	/**
	 * Update background color code of an element in workspace->areas page
	 *
	 * @param  $element_id
	 *
	 * @return void
	 */
	public function update_area_tip_text($area_id = null) {

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
				// pr($post, 1);
				$this->Area->id = $area_id;

				$post['Area']['tooltip_text'] = htmlentities($post['Area']['tooltip_text']);

				if ($this->Area->saveField('tooltip_text', $post['Area']['tooltip_text'])) {

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
	public function update_task_project_activity($workspace_id = null) {
		/*$this->loadModel("ProjectWorkspace");
		$workspace = $this->ProjectWorkspace->find("first", array("conditions" => array("ProjectWorkspace.workspace_id" => $workspace_id)));
		$user_id = $this->Session->read("Auth.User.id");
		$project_id = $workspace['ProjectWorkspace']['project_id'];
		$date = date("Y-m-d h:i:s");

		$project_data = [
			'project_id' => $project_id,
			'updated_user_id' => $user_id,
			'message' => 'Project updated',
			'updated' => $date,
		];

		$this->loadModel("ProjectActivity");
		$this->ProjectActivity->save($project_data);*/

	}
	public function workspace_signoff($workspace_id = null) {
		if ($this->request->isAjax()) {
			// pr($this->request->data);die('hi');
			$response = [
				'success' => false,
				'msg' => '',
				'content' => null,
			];
			$this->layout = 'ajax';
			$this->autoRender = false;

			$this->request->data['Workspace']['task_type'] = 'update';
			$this->request->data['Workspace']['updated_user_id'] = $this->Session->read("Auth.User.id");
			if (isset($this->request->data['Workspace']['sign_off']) && $this->request->data['Workspace']['sign_off'] == 1) {
				$this->request->data['Workspace']['task_type'] = 'reopen';
				$workspaceStatus = 'sign_off';
			} else if (isset($this->request->data['Workspace']['sign_off']) && $this->request->data['Workspace']['sign_off'] == 0) {
				$this->request->data['Workspace']['task_type'] = 'sign_off';
				$workspaceStatus = 'reopen';
			}


			$this->request->data['Workspace']['create_activity'] = 1;
			$post = $this->request->data['Workspace'];
			//pr($post, 1);
			if (isset($post['id']) && !empty($post['id'])) {
				$id = $post['id'];
				$workspace_id = $post['id'];
				$this->Workspace->id = $id;

				if (!$this->Workspace->exists()) {
					throw new NotFoundException(__('Invalid detail'), 'error');
				}
				// SIGNOFF DATE
				if (isset($this->request->data['Workspace']['sign_off']) && $this->request->data['Workspace']['sign_off'] == 0) {
					$post['sign_off_date'] = '';
				}

				$project_id = workspace_pid($id);
				//echo $project_id; die;

				if ($this->Workspace->save($post)) {
					$this->Workspace->updateAll(
						array("Workspace.create_activity" => 0),
						array("Workspace.id" => $id)
					);
					// $this->update_task_project_activity($id);


					/*********exits sign-off entry delete **********/
						$del = array('workspace_id'=>$workspace_id);
						$signoffdata = $this->SignoffWorkspace->find('first',array('conditions'=>$del ));
						if( isset($signoffdata) ){
							if( !empty(!empty($signoffdata['SignoffWorkspace']['task_evidence'])) ){
								$folder_url = WWW_ROOT . WORKSPACE_SIGNOFF_PATH;
								unlink($folder_url.'/'.$signoffdata['SignoffWorkspace']['task_evidence']);
							}
							$this->SignoffWorkspace->deleteAll($del);
						}
					/*********************************************/


					//$this->Element->save($post)
					// Get Project Id with Element id; Update Project modified date
					//$this->update_project_modify($id);
					/************** socket messages **************/
					if (SOCKET_MESSAGES) {
						$WSPUsers = $this->getWSPUsers($project_id, $id);
						$current_user_id = $this->user_id;
						if (isset($WSPUsers) && !empty($WSPUsers)) {
							if (($key = array_search($current_user_id, $WSPUsers)) !== false) {
								unset($WSPUsers[$key]);
							}
						}
						$s_open_users = $r_open_users = null;
						if (isset($WSPUsers) && !empty($WSPUsers)) {
							foreach ($WSPUsers as $key1 => $value1) {
								if (web_notify_setting($value1, 'workspace', 'workspace_sign_off')) {
									$s_open_users[] = $value1;
								}
								if (web_notify_setting($value1, 'workspace', 'workspace_reopened')) {
									$r_open_users[] = $value1;
								}
							}
						}
						$userDetail = get_user_data($this->user_id);
						$heading = (isset($this->request->data['Workspace']['sign_off']) && $this->request->data['Workspace']['sign_off'] == 1) ? 'Workspace sign-off' : 'Workspace re-opened';
						$content = [
							'socket' => [
								'notification' => [
									'type' => 'workspace_sign_off',
									'created_id' => $this->user_id,
									'project_id' => $project_id,
									'refer_id' => $id,
									'creator_name' => $userDetail['UserDetail']['full_name'],
									'subject' => $heading,
									'heading' => 'Workspace: ' . strip_tags(getFieldDetail('Workspace', $id, 'title')),
									'sub_heading' => 'Project: ' . strip_tags(getFieldDetail('Project', $project_id, 'title')),
									'date_time' => timezoneTimeConvertor($this->user_id, date('Y-m-d g:iA'), $format = 'd M Y g:iA') . user_country($this->user_id),
								],
							],
						];
						if (isset($this->request->data['Workspace']['sign_off']) && $this->request->data['Workspace']['sign_off'] == 1) {
							if (is_array($s_open_users)) {
								$content['socket']['received_users'] = array_values($s_open_users);
							}
						} else {
							if (is_array($r_open_users)) {
								$content['socket']['received_users'] = array_values($r_open_users);
							}
						}
						$response['content'] = $content;
					}
					/************** socket messages **************/
					$this->workspaceSignOffEmail($project_id, $id, $workspaceStatus);

					$response['success'] = true;
					$response['msg'] = 'You have been signed off successfully.';
				} else {
					$response['msg'] = 'Signing off could not be completed. Please try again later.';
				}
				// $this->Element->_query(1);
			}


		}
		echo json_encode($response);
		exit();
	}

	public function get_progressing_workspace_element($workspace_id = null) {
		$areas = $this->Area->find("list", array("fields" => array("Area.id"), "recursive" => -1, "conditions" => array("Area.workspace_id" => $workspace_id)));
		$all_el_by_areaid = $this->Element->find("count", array(
			"recursive" => -1,
			"conditions" => array(
				"Element.area_id" => $areas,
				"Element.sign_off" => 0,
				"Element.date_constraints" => 1,
			),
		)
		);
		return $all_el_by_areaid;
	}

	public function get_history() {
		$this->layout = false;
		$this->autoRender = false;
		if ($this->request->is('ajax') || $this->request->is('get')) {
			$id = isset($this->params['pass'][0]) && !empty($this->params['pass'][0]) ? $this->params['pass'][0] : '';

			$this->loadModel("WorkspaceActivity");
			$this->loadModel("UserDetail");
			$this->WorkspaceActivity->bindModel(array(
				"belongsTo" => array(
					"UserDetail" => array(
						"class" => "UserDetail",
						"foreignKey" => false,
						"conditions" => array("WorkspaceActivity.updated_user_id = UserDetail.user_id"),
					),
				),
			));
			$history_lists = $this->WorkspaceActivity->find("all", array("conditions" => array("WorkspaceActivity.workspace_id" => $id), 'limit' => 15, 'order' => "WorkspaceActivity.id DESC"));
			$this->set(compact("id", "history_lists"));
			return $history_lists;

			//$this->render("activity/update_history");

		}
	}
	public function more_history() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$model = 'WorkspaceActivity';

			if ($this->request->is('post') || $this->request->is('put')) {
				$this->pagination['page'] = 10;
				$this->loadModel("WorkspaceActivity");
				$this->loadModel("UserDetail");
				$this->WorkspaceActivity->bindModel(array(
					"belongsTo" => array(
						"UserDetail" => array(
							"class" => "UserDetail",
							"foreignKey" => false,
							"conditions" => array("WorkspaceActivity.updated_user_id = UserDetail.user_id"),
						),
					),
				));

				//
				$history_list = $this->WorkspaceActivity->find('count', ['conditions' => ["WorkspaceActivity.workspace_id" => $this->request->data['workspace_id']], 'order' => 'WorkspaceActivity.id DESC']);
				$t = ($history_list / 10);

				if (isset($history_list) && !empty($history_list) && ($this->params['named']['page'] <= ceil(($history_list / 10)))) {

					$paginator = array(
						'conditions' => array(
							$model . ".workspace_id" => $this->request->data['workspace_id'],
						),
						"joins" => array(
							array(
								'alias' => 'UserDetail',
								'table' => 'user_details',
								'type' => 'INNER',
								'conditions' => 'WorkspaceActivity.updated_user_id = UserDetail.user_id',
							),
						),
						'fields' => ['WorkspaceActivity.*', 'UserDetail.*'],
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
				$this->set('workspace_id', $this->request->data['workspace_id']);
				$this->render('/Workspaces/activity/update_filter_history');
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

			$this->loadModel("WorkspaceActivity");
			$this->loadModel("UserDetail");
			//pr($this->params['named']);
			$this->WorkspaceActivity->bindModel(array(
				"belongsTo" => array(
					"UserDetail" => array(
						"class" => "UserDetail",
						"foreignKey" => false,
						"conditions" => array("WorkspaceActivity.updated_user_id = UserDetail.user_id"),
					),
				),
			));

			if ($seeactivityhistory == 'all') {
				$conditions = array("WorkspaceActivity.workspace_id" => $id);
			} else if ($seeactivityhistory == 'today') {
				$start = date('Y-m-d');
				$end = date('Y-m-d');
				$conditions = array(
					"WorkspaceActivity.workspace_id" => $id,
					'date(WorkspaceActivity.updated) BETWEEN ? AND ?' => array($start, $end),
				);

			} else if ($seeactivityhistory == 'last_7_day') {
				$end = date('Y-m-d');
				$start = date('Y-m-d', strtotime('-7 day'));
				$conditions = array(
					"WorkspaceActivity.workspace_id" => $id,
					'date(WorkspaceActivity.updated) BETWEEN ? AND ?' => array($start, $end),
				);
			} else if ($seeactivityhistory == 'this_month') {
				$end = date('Y-m-t');
				$start = date('Y-m-01');
				$conditions = array(
					"WorkspaceActivity.workspace_id" => $id,
					'date(WorkspaceActivity.updated) BETWEEN ? AND ?' => array($start, $end),
				);
			}

			$history_lists = $this->WorkspaceActivity->find("all", array(
				"conditions" => $conditions,
				'order' => "WorkspaceActivity.id DESC",
				"group" => "DATE_FORMAT(WorkspaceActivity.updated,'%Y-%m-%d %h:%i')",

			)
			);

			$this->set(compact("id", "seeactivityhistory", "history_lists"));
			$this->render("activity/update_filter_history");
		}
	}

	public function apply_user_filter($project_id, $workspace_id, $type, $selected, $match_all) {
		$result = array();
		if($type == 'tag') {
			$this->loadModel('Tag');

			if($selected != '') {
				$termsArr = array_map('trim', explode(',', $selected));
				$selectedTags = implode("','",$termsArr);
				$tagCnt = count($termsArr);
				if($match_all) {
					$findQuery = "SELECT
									tagged_user_id, user_details.first_name, user_details.last_name
								FROM tags
								LEFT JOIN users ON tags.tagged_user_id = users.id
								LEFT JOIN user_details ON users.id = user_details.user_id
								WHERE tags.user_id = $this->user_id AND tagged_user_id IN(
									SELECT tagged_user_id
									FROM `tags`
									WHERE 1 AND tag IN ('$selectedTags') AND user_id = $this->user_id
									GROUP BY tagged_user_id
									HAVING COUNT(DISTINCT tag) = $tagCnt
								)
								GROUP BY tagged_user_id ORDER BY user_details.first_name ASC";
				} else {
					$findQuery = "SELECT
									tagged_user_id, user_details.first_name, user_details.last_name
								FROM tags
								LEFT JOIN users ON tags.tagged_user_id = users.id
								LEFT JOIN user_details ON users.id = user_details.user_id
								WHERE tags.user_id = $this->user_id AND tagged_user_id IN(
									SELECT tagged_user_id
									FROM `tags`
									WHERE 1 AND tag IN ('$selectedTags') AND user_id = $this->user_id
									GROUP BY tagged_user_id
								)
								GROUP BY tagged_user_id
								ORDER BY user_details.first_name ASC";
				}
				$tagUsers = $this->Tag->query($findQuery);

				if (isset($tagUsers) && !empty($tagUsers)) {
					foreach($tagUsers as $k => $v) {
						$result[] = $v['tags']['tagged_user_id'];
					}
				}
			}
		} else if($type == 'skill') {
			$this->loadModel('UserSkill');

			$non_proj_users = array();
			$concatQuery = "";
			if($project_id > 0) {
				$view = new View();
				$viewModel = $view->loadHelper('Permission');
				$wsUsers = $viewModel->workspaceUsers($project_id, $workspace_id);
				if(!empty($wsUsers)) {
					$user_ids = Set::extract('{n}/user_details/user_id', $wsUsers);
					$nonWsUsersIdsImp = implode(',', $user_ids);
					$concatQuery = " AND user_skills.user_id NOT IN ($nonWsUsersIdsImp) ";
				}
			}

			if($selected != '') {
				$termsArr = array_map('trim', explode(',', $selected));
				$selectedSkills = implode("','",$termsArr);
				$skillCnt = count($termsArr);

				if($match_all) {
					$findQuery = "SELECT
									user_skills.user_id, user_details.first_name, user_details.last_name
								FROM user_skills
								LEFT JOIN users ON user_skills.user_id = users.id
								LEFT JOIN user_details ON users.id = user_details.user_id
								WHERE user_skills.user_id IN(
									SELECT user_skills.user_id
									FROM `user_skills`
									WHERE 1 AND skill_id IN ('$selectedSkills') $concatQuery
									GROUP BY user_skills.user_id
									HAVING COUNT(DISTINCT skill_id) = $skillCnt
								)
								GROUP BY user_skills.user_id ORDER BY user_details.first_name ASC";
				} else {
					$findQuery = "SELECT
									user_skills.user_id, user_details.first_name, user_details.last_name
								FROM user_skills
								LEFT JOIN users ON user_skills.user_id = users.id
								LEFT JOIN user_details ON users.id = user_details.user_id
								WHERE user_skills.user_id IN(
									SELECT user_skills.user_id
									FROM `user_skills`
									WHERE 1 AND skill_id IN ('$selectedSkills') $concatQuery
									GROUP BY user_skills.user_id
								)
								GROUP BY user_skills.user_id ORDER BY user_details.first_name ASC";
				}

				$skillUsers = $this->UserSkill->query($findQuery);

				if (isset($skillUsers) && !empty($skillUsers)) {
					foreach($skillUsers as $k => $v) {
						$result[] = $v['user_skills']['user_id'];
					}
				}
			}
		}
		return $result;
	}

	public function getQuickShareUserListOld($project_id = null, $workspace_id = null) {
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			$this->autoRender = false;

			$viewData['project_id'] = $project_id;
			$viewData['workspace_id'] = $workspace_id;
			$viewData['perm_users'] = array();
			$viewData['not_shared_user'] = array();

			if ($this->request->isAjax()) {
				$not_shared = get_selected_users($this->user_id, $project_id);
				// e(project_upid($project_id));

				$current_user_id = $this->Session->read('Auth.User.id');
				$conditions = ['ProjectPermission.project_level !=' => 1, 'ProjectPermission.user_id IS NOT NULL', 'ProjectPermission.user_project_id' => project_upid($project_id)];
				$p_permission = $this->ProjectPermission->find('all', ['conditions' => $conditions, 'fields' => ['ProjectPermission.project_level', 'ProjectPermission.user_id'], 'recursive' => -1]);

				$perm_users = [];

				if (isset($p_permission) && !empty($p_permission)) {
					$perm_users = Set::extract($p_permission, '/ProjectPermission/user_id');
				}

				if (isset($not_shared) && !empty($not_shared)) {
					$perm_users = array_merge($perm_users, array_keys($not_shared));
				}

				$type = $this->request->data['type'];
				$selected = (isset($this->request->data['selected']) && !empty($this->request->data['selected'])) ? trim($this->request->data['selected']) : '';
				$match_all = (isset($this->request->data['is_match_all']) && trim($this->request->data['is_match_all']) != '') ? $this->request->data['is_match_all'] : false;

				$conditions = [
						'User.id' => $perm_users,
						'User.role_id' => 2,
						'UserDetail.first_name IS NOT NULL',
						'UserDetail.last_name IS NOT NULL',
					];

				if( $selected != '') {
					if($type == 'tag' || $type == 'skill') {
						$selectedUsers = $this->apply_user_filter($project_id, $workspace_id, $type, $selected, $match_all);
						//if(!empty($selectedUsers)) {
							$perm_users = array_intersect($perm_users, $selectedUsers);
						//}
						$conditions = [
							'User.id' => $perm_users,
							'User.role_id' => 2,
							'UserDetail.first_name IS NOT NULL',
							'UserDetail.last_name IS NOT NULL',
						];
					} else if($type == 'text') {
						$conditions = [
							'User.id' => $perm_users,
							'User.role_id' => 2,
							'UserDetail.first_name IS NOT NULL',
							'UserDetail.last_name IS NOT NULL',
							'OR' => [
								'UserDetail.first_name LIKE' => '%'.$selected.'%',
								'UserDetail.last_name LIKE' => '%'.$selected.'%',
							]
						];
					}
				}
				$perm_users = $this->User->find('all', [
					'conditions' => $conditions,
					'fields' => [
						'UserDetail.user_id', 'UserDetail.first_name', 'UserDetail.last_name', 'UserDetail.profile_pic', 'UserDetail.job_title',
					],
					'joins' => [
						[
							'table' => 'user_details',
							'alias' => 'UserDetail',
							'type' => 'INNER',
							'conditions' => ['User.id = UserDetail.user_id'],
						],
					],
					'recursive' => -1,
					'order' => ['UserDetail.first_name ASC', 'UserDetail.last_name ASC'],
				]);

				$viewData['project_id'] = $project_id;
				$viewData['workspace_id'] = $workspace_id;
				$viewData['perm_users'] = $perm_users;
				$viewData['not_shared_user'] = array_keys($not_shared);
			}
			$this->set('viewData', $viewData);
			$this->render('/Workspaces/partials/quick_share_user_list', 'ajax');
		}
	}

	public function checkWSPUser() {
		if ($this->request->isAjax()) {
			$response = [
				'success' => false,
				'msg' => '',
				'content' => null,
			];

			$this->layout = 'ajax';
			$this->autoRender = false;

			$wsUsers = array();
			if ($this->request->isAjax()) {
				$u_id = (isset($this->request->data['u_id']) && !empty($this->request->data['u_id'])) ? trim($this->request->data['u_id']) : 0;
				$wsp_id = (isset($this->request->data['wsp_id']) && !empty($this->request->data['wsp_id'])) ? trim($this->request->data['wsp_id']) : 0;

				if($u_id > 0 && $wsp_id > 0) {
					$view = new View();
					$viewModel = $view->loadHelper('Permission');
					$wsUsers = $viewModel->workspaceUserPermissions($u_id, $wsp_id);

					if(!empty($wsUsers)) {
						$response = [
							'success' => true,
							'msg' => 'success',
							'content' => $wsUsers[0]['user_permissions'],
						];
					}
				}
			}
			echo json_encode($response);exit();
		}
	}

	public function getQuickShareUserList($project_id = null, $workspace_id = null) {
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			$this->autoRender = false;

			$viewData['project_id'] = $project_id;
			$viewData['workspace_id'] = $workspace_id;
			$viewData['perm_users'] = array();
			$viewData['not_shared_user'] = array();
			$items_per_page = $this->quick_share_paging;

			if ($this->request->isAjax()) {
				$type = $this->request->data['type'];

				$selected = (isset($this->request->data['selected']) && !empty($this->request->data['selected'])) ? trim($this->request->data['selected']) : '';
				$match_all = (isset($this->request->data['is_match_all']) && trim($this->request->data['is_match_all']) != '') ? $this->request->data['is_match_all'] : false;
				$offset = (isset($this->request->data['page']) && !empty(trim($this->request->data['page']))) ? trim($this->request->data['page']) : 0;

				$perm_users_sliced = array();

				$perm_users_count = get_selected_users_workspace_with_paging($this->user_id, $project_id, $workspace_id, 'wsp', false, $type, $selected, $match_all, $items_per_page, $offset, 1);

				$perm_users = get_selected_users_workspace_with_paging($this->user_id, $project_id, $workspace_id, 'wsp', false, $type, $selected, $match_all, $items_per_page, $offset, 0);

				if(!empty($perm_users)) {
					//$perm_users_sliced = array_slice($perm_users, $offset, $this->quick_share_paging, true);
				}

				$viewData['project_id'] = $project_id;
				$viewData['workspace_id'] = $workspace_id;
				$viewData['perm_users'] = $perm_users;
				$viewData['tot_perm_users'] = (isset($perm_users_count[0]) && isset($perm_users_count[0][0]) && isset($perm_users_count[0][0]['u_cnt'])) ? $perm_users_count[0][0]['u_cnt'] : 0;

				$viewData['type'] = $type;
			}
			$this->set('viewData', $viewData);
			$this->render('/Workspaces/partials/quick_share_user_list', 'ajax');
		}
	}

	public function quick_share($project_id = null, $workspace_id = null) {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$viewData = null;

			//$not_shared = get_selected_users($this->user_id, $project_id);

			$perm_users = get_selected_users_workspace($this->user_id, $project_id, $workspace_id, false, 'wsp');
			$perm_users_sliced = array_slice($perm_users, 0, $this->quick_share_paging, true);

			// e(project_upid($project_id));
			/*$current_user_id = $this->Session->read('Auth.User.id');
			$conditions = ['ProjectPermission.project_level !=' => 1, 'ProjectPermission.user_id IS NOT NULL', 'ProjectPermission.user_project_id' => project_upid($project_id)];
			$p_permission = $this->ProjectPermission->find('all', ['conditions' => $conditions, 'fields' => ['ProjectPermission.project_level', 'ProjectPermission.user_id'], 'recursive' => -1]);

			$perm_users = [];

			if (isset($p_permission) && !empty($p_permission)) {
				$perm_users = Set::extract($p_permission, '/ProjectPermission/user_id');
			}

			if (isset($not_shared) && !empty($not_shared)) {
				$perm_users = array_merge($perm_users, array_keys($not_shared));
			}
			$perm_users = $this->User->find('all', [
				'conditions' => [
					'User.id' => $perm_users,
					'User.role_id' => 2,
					'UserDetail.first_name IS NOT NULL',
					'UserDetail.last_name IS NOT NULL',
				],
				'fields' => [
					'UserDetail.user_id', 'UserDetail.first_name', 'UserDetail.last_name', 'UserDetail.profile_pic', 'UserDetail.job_title',
				],
				'joins' => [
					[
						'table' => 'user_details',
						'alias' => 'UserDetail',
						'type' => 'INNER',
						'conditions' => ['User.id = UserDetail.user_id'],
					],
				],
				'recursive' => -1,
				'order' => ['UserDetail.first_name ASC', 'UserDetail.last_name ASC'],
			]);*/
			$perm_users_ids = [];
			if(!empty($perm_users)) {
				$perm_users_ids = Set::extract($perm_users, '{n}.UserDetail.user_id');
			}
			//pr($perm_users);die;

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
			$viewData['tags'] = $sk;
			$view = new View();
			$commonHelper = $view->loadHelper('Common');
			$skillsData = $commonHelper->get_skill_of_users($perm_users_ids);
			//$skillsData = $this->Skill->find('list', ['conditions' => ['status' => 1],'fields' => array('Skill.id', 'Skill.title'), 'order' => ['title ASC']]);

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

			$viewData['project_id'] = $project_id;
			$viewData['workspace_id'] = $workspace_id;
			$viewData['perm_users'] = $perm_users_sliced;
			$viewData['perm_users_all'] = array_keys($perm_users);

			$this->set($viewData);

			$this->render('/Workspaces/partials/quick_share');
		}

	}
	public function select_permissions() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$viewData = null;

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				// pr($post, 1);

				$viewData['type'] = $post['type'];
			}

			$this->set($viewData);

			$this->render('/Workspaces/partials/select_permissions');

		}
	}

	public function quick_share_permissions($project_id = null, $user_id = null, $workspace_id = null) {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$viewData = null;

			$viewData['project_id'] = $project_id;
			$viewData['user_id'] = $user_id;
			$viewData['workspace_id'] = $workspace_id;

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;

				$viewData['type'] = $post['type'];
			}

			$this->set($viewData);

			$this->render('/Workspaces/partials/quick_share_permissions');

		}
	}

	public function save_quick_share($project_id = null) {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$response = [
				'success' => false,
				'content' => null,
				'socket_newuser' => null,
			];
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;

				$userProjectId = project_upid($project_id);
				$userOwnerId = project_owner($project_id);
				$mail_sent = false;
				// pr($post['Share'] , 1);
				if (isset($post['Share']['wid']) && !empty($post['Share']['wid'])) {

					$multi_wsp = $post['Share']['wid'];


					foreach ($multi_wsp as $key => $val) {


						$wsp_permissions = $this->Common->wsp_permission_details(workspace_pwid($project_id, $val), $project_id, $post['Share']['user_id']);
						// if wsp permission already exists.
						if (isset($wsp_permissions) && !empty($wsp_permissions)) {
							$wsp_permit_id = Set::classicExtract($wsp_permissions, '0.WorkspacePermission.id');

							$workspace_permit = [
								'id' => $wsp_permit_id,
								'user_id' => $post['Share']['user_id'],
								'user_project_id' => $userProjectId,
								'project_workspace_id' => workspace_pwid($project_id, $val),
								'permit_read' => (isset($post['WorkspacePermission']['permit_read']) && !empty($post['WorkspacePermission']['permit_read'])) ? 1 : 0,
								'permit_edit' => (isset($post['WorkspacePermission']['permit_edit']) && !empty($post['WorkspacePermission']['permit_edit'])) ? 1 : 0,
								'permit_delete' => (isset($post['WorkspacePermission']['permit_delete']) && !empty($post['WorkspacePermission']['permit_delete'])) ? 1 : 0,
								'permit_add' => (isset($post['WorkspacePermission']['permit_add']) && !empty($post['WorkspacePermission']['permit_add'])) ? 1 : 0,
							];

							if ($this->WorkspacePermission->save($workspace_permit)) {
								$response['success'] = true;


								$work_data = [
									'project_id' => $project_id,
									'workspace_id' => $val,
									'updated_user_id' => $this->user_id,
									'message' => 'Workspace shared',
									'updated' => date("Y-m-d H:i:s"),
								];
								$this->loadModel('WorkspaceActivity');
								$this->WorkspaceActivity->id = null;
								$this->WorkspaceActivity->save($work_data);


									/************** socket messages **************/
									if (SOCKET_MESSAGES) {
										// e('first -- '.$val);
										$heading = 'Sharer';
										$p_permission = $this->Common->project_permission_details($project_id, $post['Share']['user_id']);
										if (isset($p_permission) && !empty($p_permission)) {
											if ($p_permission['ProjectPermission']['project_level'] > 0) {
												$heading = 'Owner';
											}
										}
										$send_notification = false;
										if (web_notify_setting($post['Share']['user_id'], 'workspace', 'workspace_sharing')) {
											$send_notification = true;
										}
										$userDetail = get_user_data($this->user_id);
										$content = null;
										$content = [
											'socket' => [
												'notification' => [
													'type' => 'workspace_sharing',
													'created_id' => $this->user_id,
													'project_id' => $project_id,
													'refer_id' => $val,
													'creator_name' => $userDetail['UserDetail']['full_name'],
													'subject' => 'Workspace sharing updated',
													'heading' => 'Permission: ' . $heading,
													'sub_heading' => 'Project: ' . strip_tags(getFieldDetail('Project', $project_id, 'title')).'<br />Workspace: ' . strip_tags(getFieldDetail('Workspace', $val, 'title')),
													'date_time' => timezoneTimeConvertor($this->user_id, date('Y-m-d g:iA'), $format = 'd M Y g:iA') . user_country($this->user_id),
												],
											],
										];
										if ($send_notification) {
											$content['socket']['received_users'] = [$post['Share']['user_id']];
										}

										// $response['content'] = $content;
										$request = array(
											'header' => array(
												'Content-Type' => 'application/json',
											),
										);
										// pr($content);
										$content = json_encode($content['socket']);
										$HttpSocket = new HttpSocket([
											'ssl_verify_host' => false,
											'ssl_verify_peer_name' => false,
											'ssl_verify_peer' => false,
										]);
										$results = $HttpSocket->post(CHATURL . '/serveremit', $content, $request);
										// pr($results);

									}
									/************** socket messages **************/
								// if ($mail_sent == false) {
									$this->Common->workspace_share_email($post['Share']['user_id'], $project_id, $val);
									// $mail_sent = true;
								// }
							}
						} else {
							$p_permission = $this->Common->project_permission_details($project_id, $post['Share']['user_id']);

							$parent_id = 0;

							if ($userOwnerId != $this->user_id) {
								$parent = $this->ProjectPermission->find('first', [
									'conditions' => ['ProjectPermission.user_project_id' => $userProjectId, 'ProjectPermission.owner_id' => $userOwnerId, 'ProjectPermission.user_id' => $this->user_id],

								]);
								$parent_id = (isset($parent) && !empty($parent)) ? $parent['ProjectPermission']['id'] : 0;

							}

							if (!isset($p_permission) || empty($p_permission)) {
								$project_permit = [
									'user_id' => $post['Share']['user_id'],
									'share_by_id' => $this->user_id,
									'owner_id' => $userOwnerId,
									'user_project_id' => $userProjectId,
									'parent_id' => $parent_id,
									'share_permission' => 0,
									'project_level' => 0,
									'permit_read' => 1,
									'permit_add' => 0,
									'permit_edit' => 0,
									'permit_delete' => 0,
								];

								if ($this->ProjectPermission->save($project_permit)) {
									$response['success'] = true;

									// insert into project activity
									$project_data = [
										'project_id' => $project_id,
										'updated_user_id' => $this->user_id,
										'message' => 'Project shared',
										'updated' => date("Y-m-d H:i:s"),
									];
									$this->loadModel('ProjectActivity');
									$this->ProjectActivity->id = null;
									$this->ProjectActivity->save($project_data);

									// Add user project connection to mongo db
									$this->Users->userConnections($post['Share']['user_id'], $project_id);
								}
							}

							$workspace_permit = [
								'id' => '',
								'user_id' => $post['Share']['user_id'],
								'user_project_id' => $userProjectId,
								'project_workspace_id' => workspace_pwid($project_id, $val),
								'permit_read' => (isset($post['WorkspacePermission']['permit_read']) && !empty($post['WorkspacePermission']['permit_read'])) ? 1 : 0,
								'permit_edit' => (isset($post['WorkspacePermission']['permit_edit']) && !empty($post['WorkspacePermission']['permit_edit'])) ? 1 : 0,
								'permit_delete' => (isset($post['WorkspacePermission']['permit_delete']) && !empty($post['WorkspacePermission']['permit_delete'])) ? 1 : 0,
								'permit_add' => (isset($post['WorkspacePermission']['permit_add']) && !empty($post['WorkspacePermission']['permit_add'])) ? 1 : 0,
							];

							$work_data = [
								'project_id' => $project_id,
								'workspace_id' => $val,
								'updated_user_id' => $this->user_id,
								'message' => 'Workspace shared',
								'updated' => date("Y-m-d H:i:s"),
							];
							$this->loadModel('WorkspaceActivity');
							$this->WorkspaceActivity->id = null;
							$this->WorkspaceActivity->save($work_data);

							if ($this->WorkspacePermission->save($workspace_permit)) {
								$response['success'] = true;


								if (isset($p_permission) && !empty($p_permission)) {

										/************** socket messages **************/
										if (SOCKET_MESSAGES) {
											// e('sec -- '.$val);
											$heading = 'Sharer';
											if ($p_permission['ProjectPermission']['project_level'] > 0) {
												$heading = 'Owner';
											}

											$send_notification = false;
											if (web_notify_setting($post['Share']['user_id'], 'workspace', 'workspace_sharing')) {
												$send_notification = true;
											}
											$userDetail = get_user_data($this->user_id);
											$content = null;
											$content = [
												'socket' => [
													'notification' => [
														'type' => 'workspace_sharing',
														'created_id' => $this->user_id,
														'project_id' => $project_id,
														'workspace_id' => $val,
														'creator_name' => $userDetail['UserDetail']['full_name'],
														'subject' => 'Workspace sharing',
														'heading' => 'Permission: ' . $heading,
														'sub_heading' => 'Project: ' . strip_tags(getFieldDetail('Project', $project_id, 'title')).'<br />Workspace: ' . strip_tags(getFieldDetail('Workspace', $val, 'title')),
														'date_time' => timezoneTimeConvertor($this->user_id, date('Y-m-d g:iA'), $format = 'd M Y g:iA') . user_country($this->user_id),
													],
												],
											];

											if ($send_notification) {
												$content['socket']['received_users'] = [$post['Share']['user_id']];
											}

											// $response['content'] = $content;
											$request = array(
												'header' => array(
													'Content-Type' => 'application/json',
												),
											);
											// pr($content);
											$content = json_encode($content['socket']);
											$HttpSocket = new HttpSocket([
												'ssl_verify_host' => false,
												'ssl_verify_peer_name' => false,
												'ssl_verify_peer' => false,
											]);
											$results = $HttpSocket->post(CHATURL . '/serveremit', $content, $request);
										}
										/************** socket messages **************/
										// if ($mail_sent == false) {
										$this->Common->workspace_share_email($post['Share']['user_id'], $project_id, $val);
										// $mail_sent = true;
										// }
								} else {

										$this->Common->workspace_share_email($post['Share']['user_id'], $project_id, $val);
										$this->Common->getProjectAllUser($project_id, $post['Share']['user_id']);
										// $mail_sent = true;
										$p_permission = $this->Common->project_permission_details($project_id, $post['Share']['user_id']);
										/************** socket messages **************/
										if (SOCKET_MESSAGES) {
											// e('third -- '.$val);
											$heading = 'Sharer';
											if ($p_permission['ProjectPermission']['project_level'] > 0) {
												$heading = 'Owner';
											}

											$send_notification = false;
											if (web_notify_setting($post['Share']['user_id'], 'workspace', 'workspace_sharing')) {
												$send_notification = true;
											}
											$userDetail = get_user_data($this->user_id);
											$content = null;
											$content = [
												'socket' => [
													'notification' => [
														'type' => 'workspace_sharing',
														'created_id' => $this->user_id,
														'project_id' => $project_id,
														'workspace_id' => $val,
														'creator_name' => $userDetail['UserDetail']['full_name'],
														'subject' => 'Workspace sharing',
														'heading' => 'Permission: ' . $heading,
														'sub_heading' => 'Project: ' . strip_tags(getFieldDetail('Project', $project_id, 'title')).'<br />Workspace: ' . strip_tags(getFieldDetail('Workspace', $val, 'title')),
														'date_time' => timezoneTimeConvertor($this->user_id, date('Y-m-d g:iA'), $format = 'd M Y g:iA') . user_country($this->user_id),
													],
													// 'received_users' => [$post['Share']['user_id']],
												],
											];

											if ($send_notification) {
												$content['socket']['received_users'] = [$post['Share']['user_id']];
											}

											// $response['content'] = $content;
											$request = array(
												'header' => array(
													'Content-Type' => 'application/json',
												),
											);
											// pr($content);
											$content = json_encode($content['socket']);
											$HttpSocket = new HttpSocket([
												'ssl_verify_host' => false,
												'ssl_verify_peer_name' => false,
												'ssl_verify_peer' => false,
											]);
											$results = $HttpSocket->post(CHATURL . '/serveremit', $content, $request);
										}
										/************** socket messages **************/
										/************** socket messages (new project member) **************/
									if ($mail_sent == false) {
										$mail_sent = true;
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
									}
								}
							}
						}
					}
				}

			}
			// die('end');
			echo json_encode($response);
			exit;
		}
	}



	private function getWSPUsers($project_id = null, $workspace_id = null) {

		$data = array();
		$data2 = array();
		$data3 = array();
		$data4 = array();
		$data5 = array();

		$projectwsp_id = workspace_pwid($project_id, $workspace_id);
		$view = new View();
		$commonHelper = $view->loadHelper('Common');
		$data = $commonHelper->ProjectOwner($project_id, $this->Session->read('Auth.User.id'));

		$data2 = wsp_participants($project_id, $projectwsp_id, $data['UserProject']['user_id']);
		$data3 = participants_owners($project_id, $data['UserProject']['user_id'], 1);
		$data4 = participants_group_owner($project_id);
		$data5 = wsp_grps_sharer($project_id, $projectwsp_id);

		$data2 = (isset($data2) && !empty($data2)) ? $data2 : array();
		$data3 = (isset($data3) && !empty($data3)) ? $data3 : array();
		$data4 = (isset($data4) && !empty($data4)) ? $data4 : array();
		$data5 = (isset($data5) && !empty($data5)) ? $data5 : array();

		$all_owner = array();

		$data2 = array_filter($data2);
		$data3 = array_filter($data3);
		$data4 = array_filter($data4);
		$data5 = array_filter($data5);

		$all_owner = array_merge($data2, $data3, $data4, $data5);

		$all_owner = array_unique($all_owner);

		return $all_owner;

	}


	public function workspaceScheduleOverdueEmailCron() {
		$this->layout = false;
		$this->autoRender = false;
		$this->loadModel('UserPermission');

		$view = new View();
		$commonHelper = $view->loadHelper('Common');

		$this->Workspace->unbindModel(
			array('hasMany' => array('Area', 'ElementPermission'))
		);

		$this->loadModel('ProjectWorkspace');
		$this->ProjectWorkspace->unbindModel(
			array('hasMany' => array(  'WorkspacePermission'))
		);

		$workspaceList = $this->ProjectWorkspace->find('all', ['conditions' => ['Workspace.sign_off !=' => 1 ,'ProjectWorkspace.project_id !='=>''],'fields'=>['Workspace.id','Workspace.title','Workspace.end_date','ProjectWorkspace.project_id']]);

		if (isset($workspaceList) && !empty($workspaceList) && count($workspaceList) > 0) {
			$emailStatus = false;
			$currentDate = date('Y-m-d');
			foreach ($workspaceList as $listall) {

				$workspace = $listall['Workspace'];
				$projectworkspace = $listall['ProjectWorkspace'];

				//================= Overdue Days ====================================

				$daysleft = daysLeft(date('Y-m-d', strtotime($listall['Workspace']['end_date'])), $currentDate);

				if (isset($projectworkspace['project_id']) && !empty($projectworkspace['project_id'])) {

					$project_id = $projectworkspace['project_id'];
					$projecttitle = $this->Project->find('first', array('conditions' => array('Project.id' => $project_id)));
					$projectname = $projecttitle['Project']['title'];
					if ($daysleft == 1) {

						$workspace_name = $workspace['title'];
						$workspace_id = $workspace['id'];
						$workspaceAction = SITEURL . 'projects/manage_elements/' . $project_id . '/' . $workspace_id;

						$sql = "SELECT role,user_permissions.user_id,project_id,workspace_id,users.email as email,users.email_notification as email_notification, user_details.first_name as firstName,user_details.last_name as lastName FROM user_permissions

							INNER JOIN
								workspaces
								ON workspaces.id=user_permissions.workspace_id
							INNER JOIN
								users
								ON users.id=user_permissions.user_id
							INNER JOIN
								user_details
								ON user_details.user_id=users.id
							WHERE
								user_permissions.workspace_id=$workspace_id	AND
								user_permissions.area_id IS NULL ";

						$all_owner = $this->UserPermission->query($sql);

						if (isset($all_owner) && !empty($all_owner)) {

							foreach ($all_owner as $valData) {
								$user_permissions = $valData['user_permissions'];
								$users = $valData['users'];
								$usersDetails = $valData['user_details'];

								$notifiragstatus = $this->EmailNotification->find('first', ['conditions' => ['notification_type' => 'workspace', 'personlization' => 'workspace_schedule_overdue', 'user_id' => $user_permissions['user_id']]]);

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
									$email->subject(SITENAME . ': Workspace schedule overdue');
									$email->template('workspace_schedule_overdue');
									$email->emailFormat('html');
									$email->viewVars(array('workspace_name' => $workspace_name,'projectName' => $projectname, 'owner_name' => $owner_name, 'workspaceAction' => $workspaceAction, 'open_page' => $workspaceAction));
									$email->send();
									$emailStatus = true;
								}
							}
								/************** socket messages **************/
									if (SOCKET_MESSAGES) {
										$r_users = $all_owner;
										$open_users = null;
										if (isset($r_users) && !empty($r_users)) {
											foreach ($r_users as $key1 => $value1) {
												$user_permissions = $value1['user_permissions'];
												if (web_notify_setting($user_permissions['user_id'], 'workspace', 'workspace_schedule_overdue')) {
													$open_users[] = $user_permissions['user_id'];
												}
											}
										}
										$open_users = array_unique($open_users);
										$content = [
											'notification' => [
												'type' => 'workspace_schedule_overdue',
												'created_id' => '',
												'project_id' => $project_id,
												'refer_id' => $workspace_id,
												'creator_name' => '',
												'subject' => 'Workspace schedule overdue',
												'heading' => 'Workspace: ' . $workspace_name,
												'sub_heading' => 'Project: ' . strip_tags(getFieldDetail('Project', $project_id, 'title')),
												//'date_time' => timezoneTimeConvertor($this->user_id, date('Y-m-d g:iA'), $format = 'd M Y g:iA') . user_country($this->user_id),
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
			/* if ($emailStatus) {
				echo "Emails had sent successfully.";
			} */
		}

	}


	public function workspaceSignOffEmail($project_id = null, $workspace_id = null, $workspaceStatus = null) {

		$this->layout = false;
		$this->autoRender = false;

		$view = new View();
		$commonHelper = $view->loadHelper('Common');
		$ownerdetails = $commonHelper->ProjectOwner($project_id, $this->Session->read('Auth.User.id'));

		$data = array();
		$data1 = array();
		$data2 = array();
		$data3 = array();
		$data4 = array();
		$data5 = array();

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

		$loggedInUser = $this->Session->read('Auth.User.id');
		$sharedUser = ClassRegistry::init('User')->find('first', array('conditions' => array('User.id' => $loggedInUser)));
		$changedBy = $sharedUser['UserDetail']['first_name'] . ' ' . $sharedUser['UserDetail']['last_name'];

		$projecttitle = ClassRegistry::init('Project')->find('first', array('conditions' => array('Project.id' => $project_id)));
		$workspacetitle = $this->Workspace->find('first', array('conditions' => array('Workspace.id' => $workspace_id)));
		$projectname = $projecttitle['Project']['title'];
		$workspace_name = $workspacetitle['Workspace']['title'];

		$workspaceAction = SITEURL . 'projects/manage_elements/' . $project_id . '/' . $workspace_id;

		if ($workspaceStatus == 'sign_off') {
			$mailSubject = 'Workspace sign-off';
			$mailTemplate = 'workspace_sign_off';
		}
		if ($workspaceStatus == 'reopen') {
			$mailSubject = 'Workspace re-opened';
			$mailTemplate = 'workspace_reopen';
		}
		if (isset($all_owner) && !empty($all_owner)) {
			if (($key = array_search($this->Session->read('Auth.User.id'), $all_owner)) !== false) {
				unset($all_owner[$key]);
			}
		}
		if (isset($all_owner) && !empty($all_owner)) {
			foreach ($all_owner as $valData) {

				if ($workspaceStatus == 'reopen') {
					$notifiragstatus = ClassRegistry::init('EmailNotification')->find('first', ['conditions' => ['notification_type' => 'workspace', 'personlization' => 'workspace_reopened', 'user_id' => $valData]]);

				} else {

					$notifiragstatus = ClassRegistry::init('EmailNotification')->find('first', ['conditions' => ['notification_type' => 'workspace', 'personlization' => 'workspace_sign_off', 'user_id' => $valData]]);

				}
				//$notifiragstatus = ClassRegistry::init('EmailNotification')->find('first', ['conditions' => ['notification_type' => 'workspace', 'personlization' => 'workspace_schedule_change', 'user_id' => $valData]]);

				//$usersDetails = ClassRegistry::init('User')->find('first', array('conditions' => array('User.id' => $valData)));

				App::import("Model", "User");
				$this->User = new User();

				$usersDetails = $this->User->find('first', array('conditions' => array('User.id' => $valData)));

				if ((!isset($notifiragstatus['EmailNotification']['email']) || $notifiragstatus['EmailNotification']['email'] == 1) && (!isset($usersDetails['User']['email_notification']) || $usersDetails['User']['email_notification'] == 1)) {

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

					$email = new CakeEmail();
					$email->config('Smtp');
					$email->from(array(ADMIN_FROM_EMAIL => SITENAME));
					$email->to($usersDetails['User']['email']);
					$email->subject(SITENAME . ': ' . $mailSubject);
					$email->template($mailTemplate);
					$email->emailFormat('html');
					$email->viewVars(array('workspace_name' => $workspace_name,'projectName' => $projectname, 'owner_name' => $owner_name, 'signedoffby' => $changedBy, 'workspaceAction' => $workspaceAction, 'open_page' => $workspaceAction));
					$email->send();

				}

			}
		}

	}
	/*========================================================================================*/

	public function delete_an_item($project_id = null, $workspace_id = null, $pw_id = null) {

		if ($this->request->isAjax()) {

			$this->layout = false;
			$viewData = null;
			$viewData['project_id'] = $project_id;
			$viewData['workspace_id'] = $workspace_id;
			$viewData['pw_id'] = $pw_id;

			$this->set($viewData);
			$this->render('/Workspaces/partials/delete_an_item');

		}
	}




	/*=================== Projec Sign off ============================= */


	 public function tasks_signoff($workspace_id = null){

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			if( isset($workspace_id) && !empty($workspace_id) ){

				$this->set('sign_off',1);
				$this->set('workspace_id',$workspace_id);
				$this->render('/Workspaces/partials/task_signoff_model');

			}
		}

	}


	public function show_signoff($workspace_id = null){

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			if( isset($workspace_id) && !empty($workspace_id) ){

				$this->set('workspace_id',$workspace_id);
				$comment =$this->SignoffWorkspace->find('first',array(
					'conditions'=> array('SignoffWorkspace.workspace_id'=> $workspace_id )
					)
				);

				$userDetail = get_user_data($comment['SignoffWorkspace']['user_id']);

				if( isset($comment) && !empty($comment) ){
					$this->set('comment',$comment);
					$this->set('userDetail',$userDetail);
				}
				$this->render('/Workspaces/partials/show_signoff_model');

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
			$signoff_type = 'workspace';
			$task_comment = '';
			$workspace_id = '';
			$current_user_id = $this->Auth->user('id');

			if ($this->request->is('post') || $this->request->is('put')) {
				// pr($this->request->data, 1);

				if( isset($this->request->data['signoff_comment']) && !empty($this->request->data['signoff_comment']) ){
					$task_comment = $this->request->data['signoff_comment'];
				}

				if( isset($this->request->data['workspace_id']) && !empty($this->request->data['workspace_id']) ){
					$workspace_id = $this->request->data['workspace_id'];
				}


				$evidence_title = '';
				$check_file = true;
				if( isset($_FILES['file']) && !empty($_FILES['file']['name']) ){


						$sizeLimit = 10; // 10MB
						$folder_url = WWW_ROOT . WORKSPACE_SIGNOFF_PATH;
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
										$last_row = $this->SignoffWorkspace->find('first', array(
											'recursive' => '-1',
											'fields' => [
												'id',
											],
											'order' => 'SignoffWorkspace.id DESC',
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


					if( isset($task_comment) && !empty($task_comment) && isset($task_evidence) && !empty($task_evidence) && isset($workspace_id) && !empty($workspace_id) ){

						$save_signoff = true;

					} else if(isset($task_comment) && !empty($task_comment) && !isset($task_evidence) && empty($task_evidence) && isset($workspace_id) && !empty($workspace_id)){

						$save_signoff = true;

					} else {

						if( isset($task_comment) && !empty($task_comment) && isset($workspace_id) && !empty($workspace_id) ) {
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
					$del = array('workspace_id'=>$workspace_id);
					$this->SignoffWorkspace->deleteAll($del);


					$this->request->data['SignoffWorkspace']['id'] = null;
					$this->request->data['SignoffWorkspace']['user_id'] = $current_user_id;
					$this->request->data['SignoffWorkspace']['workspace_id'] = $workspace_id;
					$this->request->data['SignoffWorkspace']['signoff_type'] = $signoff_type;
					$this->request->data['SignoffWorkspace']['task_comment'] = $task_comment;
					$this->request->data['SignoffWorkspace']['task_evidence'] = $task_evidence;
					$this->request->data['SignoffWorkspace']['evidence_title'] = $evidence_title;

					if( $this->SignoffWorkspace->save($this->request->data['SignoffWorkspace']) ){

						$this->request->data['Workspace']['task_type'] = 'update';
						$this->request->data['Workspace']['updated_user_id'] = $this->Session->read("Auth.User.id");
						if (isset($this->request->data['Workspace']['sign_off']) && $this->request->data['Workspace']['sign_off'] == 1) {
							$this->request->data['Workspace']['task_type'] = 'reopen';
							$workspaceStatus = 'sign_off';
						} else if (isset($this->request->data['Workspace']['sign_off']) && $this->request->data['Workspace']['sign_off'] == 0) {
							$this->request->data['Workspace']['task_type'] = 'sign_off';
							$workspaceStatus = 'reopen';
						}
						$this->request->data['Workspace']['create_activity'] = 1;
						$post = $this->request->data['Workspace'];
						//pr($post, 1);
						if (isset($post['id']) && !empty($post['id'])) {
							$id = $post['id'];
							$workspace_id = $post['id'];
							$this->Workspace->id = $id;

							if (!$this->Workspace->exists()) {
								throw new NotFoundException(__('Invalid detail'), 'error');
							}
							// SIGNOFF DATE
							$post['sign_off_date'] = date('Y-m-d');

							$project_id = workspace_pid($id);
							//echo $project_id; die;

							if ($this->Workspace->save($post)) {
								$this->Workspace->updateAll(
									array("Workspace.create_activity" => 0),
									array("Workspace.id" => $id)
								);
								// $this->update_task_project_activity($id);

								//$this->Element->save($post)
								// Get Project Id with Element id; Update Project modified date
								//$this->update_project_modify($id);
								/************** socket messages **************/
								if (SOCKET_MESSAGES) {
									$WSPUsers = $this->getWSPUsers($project_id, $id);
									$current_user_id = $this->user_id;
									if (isset($WSPUsers) && !empty($WSPUsers)) {
										if (($key = array_search($current_user_id, $WSPUsers)) !== false) {
											unset($WSPUsers[$key]);
										}
									}
									$s_open_users = $r_open_users = null;
									if (isset($WSPUsers) && !empty($WSPUsers)) {
										foreach ($WSPUsers as $key1 => $value1) {
											if (web_notify_setting($value1, 'workspace', 'workspace_sign_off')) {
												$s_open_users[] = $value1;
											}
											if (web_notify_setting($value1, 'workspace', 'workspace_reopened')) {
												$r_open_users[] = $value1;
											}
										}
									}
									$userDetail = get_user_data($this->user_id);
									$heading = (isset($this->request->data['Workspace']['sign_off']) && $this->request->data['Workspace']['sign_off'] == 1) ? 'Workspace sign-off' : 'Workspace re-opened';
									$content = [
										'socket' => [
											'notification' => [
												'type' => 'workspace_sign_off',
												'created_id' => $this->user_id,
												'project_id' => $project_id,
												'refer_id' => $id,
												'creator_name' => $userDetail['UserDetail']['full_name'],
												'subject' => $heading,
												'heading' => 'Workspace: ' . strip_tags(getFieldDetail('Workspace', $id, 'title')),
												'sub_heading' => 'Project: ' . strip_tags(getFieldDetail('Project', $project_id, 'title')),
												'date_time' => timezoneTimeConvertor($this->user_id, date('Y-m-d g:iA'), $format = 'd M Y g:iA') . user_country($this->user_id),
											],
										],
									];
									if (isset($this->request->data['Workspace']['sign_off']) && $this->request->data['Workspace']['sign_off'] == 1) {
										if (is_array($s_open_users)) {
											$content['socket']['received_users'] = array_values($s_open_users);
										}
									} else {
										if (is_array($r_open_users)) {
											$content['socket']['received_users'] = array_values($r_open_users);
										}
									}
									$response['content'] = $content;
								}
								/************** socket messages **************/
								$this->workspaceSignOffEmail($project_id, $id, $workspaceStatus);

								$response['success'] = true;
								$response['msg'] = 'You have been signed off successfully.';
							} else {
								$response['msg'] = 'Signing off could not be completed. Please try again later.';
							}
							// $this->Element->_query(1);
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

			$data = $this->SignoffWorkspace->findById($id);
			if (empty($data)) {
				throw new NotFoundException();
			}

			$response = [
				'success' => false,
				'content' => null,
			];

			if (isset($data) && !empty($data)) {
				// Send file as response
				$response['content'] = WORKSPACE_SIGNOFF_PATH  . DS . $data['SignoffWorkspace']['task_evidence'];
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


	public function current_workspace() {

		$this->loadModel('CurrentWorkspace');

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$response = [
				'success' => false,
				'content' => null,
			];
			$user_id = $this->user_id;

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				if( isset($post['project_id']) && !empty($post['project_id']) && isset($post['workspace_id']) && !empty($post['workspace_id']) ) {

					$cntTask = $this->CurrentWorkspace->find('count', array('conditions' => array('CurrentWorkspace.user_id' => $user_id)));

					if ( isset($cntTask) ) {
						if ($post['status'] == 'add') {

							$this->request->data['CurrentWorkspace']['id'] = '';
							$this->request->data['CurrentWorkspace']['user_id'] = $user_id;
							$this->request->data['CurrentWorkspace']['workspace_id'] = $post['workspace_id'];
							$this->request->data['CurrentWorkspace']['project_id'] = $post['project_id'];
							$this->request->data['CurrentWorkspace']['created'] = date('Y-m-d h:i:s');
							$this->CurrentWorkspace->save($this->request->data['CurrentWorkspace']);
							$response['success'] = true;
						}
					}

					if ($post['status'] == 'remove') {
						$this->CurrentWorkspace->deleteAll(array('CurrentWorkspace.project_id' => $post['project_id'],'CurrentWorkspace.workspace_id' => $post['workspace_id'], 'CurrentWorkspace.user_id' => $user_id));
						$response['success'] = true;
					}

				}
			}

			echo json_encode($response);
			exit();

		}
	}


	 public function filter_tasks($project_id = null, $workspace_id = null){

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$this->set('project_id', $project_id);
			$this->set('workspace_id', $workspace_id);
			$this->render('/Workspaces/partials/filter_tasks');
		}

	}

	public function get_workspace_options() {

		if ($this->request->isAjax()) {

			$this->layout = false;
			$viewData = null;

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				$viewData['project_id'] = $post['project_id'];
				$viewData['workspace_id'] = $post['workspace_id'];
			}

			$this->set($viewData);
			$this->render('/Workspaces/partials/wsp_options');

		}
	}

	public function element_reminder($element_id = null) {
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			$html = "";



			if ($this->request->is('post') || $this->request->is('put')) {
				$this->layout = false;
				$post = $this->request->data;

				$response = ['success' => false, 'content' => null];

				$rem_date = $post['Reminder']['reminder_date'];
				$post['Reminder']['reminder_date'] = date('Y-m-d H:i:s', strtotime($post['Reminder']['reminder_date']));

				/*$element_participants = $el_users = null;
				if (!isset($post['Reminder']['id']) || empty($post['Reminder']['id'])) {
					$element_participants = $this->objView->loadHelper('ViewModel')->element_participants($post['Reminder']['element_id']);
					if (isset($element_participants['participantsOwners']) && !empty($element_participants['participantsOwners'])) {
						foreach ($element_participants['participantsOwners'] as $key => $value) {
							if ($this->user_id != $value) {
								$el_users[] = $value;
							}
						}
					}
					if (isset($element_participants['participantsGpOwner']) && !empty($element_participants['participantsGpOwner'])) {
						foreach ($element_participants['participantsGpOwner'] as $key => $value) {
							if ($this->user_id != $value) {
								$el_users[] = $value;
							}
						}
					}
					if (isset($element_participants['sharers']) && !empty($element_participants['sharers'])) {
						foreach ($element_participants['sharers'] as $key => $value) {
							if ($this->user_id != $value) {
								$el_users[] = $value;
							}
						}
					}
				}*/
				$el_users = [];
				$all_users = $this->objView->loadHelper('Permission')->taskUsers($element_id);
				if(isset($all_users) && !empty($all_users)){
					$el_users = Set::extract($all_users, '{n}.user_details.user_id');
				}
				// pr($el_users, 1);

				if ($this->Reminder->save($post)) {

					$elementtitle = $this->Element->find('first', array('conditions'=>array('Element.id'=>$element_id), 'fields'=>array('Element.title') ) );

					/************** socket messages **************/
					if (SOCKET_MESSAGES) {
						$current_user_id = $this->user_id;
						$project_id = element_project($element_id);
						$elmnt_users = $el_users;

						$remind_users = null;
						if (isset($elmnt_users) && !empty($elmnt_users)) {
							$elmnt_users = array_unique($elmnt_users);
							// $elmnt_users[] = $current_user_id;
							foreach ($elmnt_users as $key1 => $value1) {
								if (web_notify_setting($value1, 'element', 'element_reminders')) {
									$remind_users[] = $value1;
								}
							}
						}
						// pr($elmnt_users, 1);

						$userDetail = get_user_data($this->user_id);
						$content = [
							'socket' => [
								'notification' => [
									'type' => 'reminder',
									'created_id' => $this->user_id,
									'project_id' => $project_id,
									'refer_id' => $element_id,
									'creator_name' => $userDetail['UserDetail']['full_name'],
									'subject' => 'Reminder',
									'heading' => 'Task: ' . strip_tags(getFieldDetail('Element', $element_id, 'title')),
									'sub_heading' => 'Project: ' . strip_tags(getFieldDetail('Project', $project_id, 'title')),
									'date_time' => timezoneTimeConvertor($this->user_id, date('Y-m-d g:iA'), $format = 'd M Y g:iA') . user_country($this->user_id),
								],
							],
						];
						if (web_notify_setting($current_user_id, 'element', 'element_reminders')) {
							$content['socket']['me'] = true;
							$remind_users[] = $current_user_id;
						}
						if (is_array($remind_users)) {
							$content['socket']['received_users'] = array_values($remind_users);
						}

						$response['content'] = $content;
					}
					/************** socket messages **************/
					// send email
					if (isset($el_users) && !empty($el_users)) {

						if (isset($el_users) && !empty($el_users)) {
							if (($key = array_search($this->Session->read('Auth.User.id'), $el_users)) !== false) {
								unset($el_users[$key]);
							}
						}

						// send mail to the user who creating the reminder

						// get all user detail
						$usersDetails = $this->User->find('first', ['conditions' => ['User.id' => $this->user_id]]);

						// get notification setting
						$notify_status = $this->EmailNotification->find('first', ['conditions' => ['notification_type' => 'project', 'personlization' => 'element_reminders', 'user_id' => $this->user_id]]);

						// Element workspace detail ==================================
						$workspace_id = element_workspace($element_id);
						$workspace_name = getFieldDetail('Workspace', $workspace_id, 'title');
						//=============================================================

						// get element project data
						$element_project_id = element_project($post['Reminder']['element_id']);
						$project_data = getByDbId('Project', $element_project_id, ['title']);
						$project_name = $project_data['Project']['title'];
						$by_user_name = $usersDetails['UserDetail']['first_name'] . ' ' . $usersDetails['UserDetail']['last_name'];

						$pageAction = SITEURL . 'entities/update_element/'.$element_id.'#tasks';

						if ((!isset($notify_status['EmailNotification']['email']) || $notify_status['EmailNotification']['email'] == 1) && (!isset($usersDetails['User']['email_notification']) || $usersDetails['User']['email_notification'] == 1)) {
							$email = new CakeEmail();
							$email->config('Smtp');
							$email->from(array(ADMIN_FROM_EMAIL => MAIL_SITENAME));
							$email->to($usersDetails['User']['email']);
							$email->subject(SITENAME . ': Reminder');
							$email->template('reminder_email_owner');
							$email->emailFormat('html');
							$email->viewVars(array('project_name' => $project_name, 'user_name' => $by_user_name,'elementName'=>$elementtitle,'open_page'=>$pageAction,'workspace_name'=>$workspace_name));
							$email->send();
						}

						// send mail to other users
						foreach ($el_users as $key => $value) {

							// get all user detail
							$usersDetails = $this->User->find('first', ['conditions' => ['User.id' => $value]]);
							if (isset($usersDetails) && !empty($usersDetails)) {

								// get notification setting
								$notify_status = $this->EmailNotification->find('first', ['conditions' => ['notification_type' => 'project', 'personlization' => 'element_reminders', 'user_id' => $value]]);

								// get element project data
								$element_project_id = element_project($post['Reminder']['element_id']);
								//$project_data = getByDbId('Project', $element_project_id, ['title']);
								//$project_name = $project_data['Project']['title'];

								if ((!isset($notify_status['EmailNotification']['email']) || $notify_status['EmailNotification']['email'] == 1) && (!isset($usersDetails['User']['email_notification']) || $usersDetails['User']['email_notification'] == 1)) {
									$to_user_name = $usersDetails['UserDetail']['first_name'] . ' ' . $usersDetails['UserDetail']['last_name'];

									$email = new CakeEmail();
									$email->config('Smtp');
									$email->from(array(ADMIN_FROM_EMAIL => MAIL_SITENAME));
									$email->to($usersDetails['User']['email']);
									$email->subject(SITENAME . ': Reminder');
									$email->template('reminder_email_others');
									$email->emailFormat('html');
									$email->viewVars(array('project_name' => $project_name, 'user_name' => $to_user_name, 'by_user_name' => $by_user_name,'elementName'=>$elementtitle,'open_page'=>$pageAction,'workspace_name'=>$workspace_name));
									$email->send();
								}
							}
						}
					}
					$response['success'] = true;

				}
				echo json_encode($response);
				exit();
			}
			$element_detail = getByDbId('Element', $element_id, ['id', 'title', 'start_date', 'end_date', 'date_constraints']);
			$get_element_reminder = get_element_reminder($element_id);
			$this->request->data = $get_element_reminder;
			$this->set('element', $element_detail);

			$this->render('/Workspaces/partials/element_reminder');
		}
	}

	public function edit_workspace($project_id = null, $workspace_id = null) {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

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
					// pr(strlen($this->request->data['Workspace']['outcome']) );
					// pr($this->request->data['Workspace']['outcome'], 1);

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

						// $this->request->data['Workspace']['title'] = html_entity_decode($this->request->data['Workspace']['title'], ENT_QUOTES);

						if ($this->Workspace->save($this->request->data)) {

							$this->Common->projectModified($project_id, $this->user_id);

							$response['success'] = true;
							$insertId = $this->request->data['Workspace']['id'];
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

			if (isset($workspace_id) && !empty($workspace_id)) {

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
			}

			$this->set('project_id', $project_id);
			$this->set('workspace_id', $workspace_id);

			$this->render('/Workspaces/partials/edit_workspace');
		}
	}

//
}
