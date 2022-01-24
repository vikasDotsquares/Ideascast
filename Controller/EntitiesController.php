<?php

App::uses('Controller/Component', 'Auth', 'Session', 'RequestHandler');
App::uses('CakeEmail', 'Network/Email');
App::uses('Folder', 'Utility');
App::uses('File', 'Utility');
App::uses('HttpSocket', 'Network/Http');

use Cake\Network\Request;

class EntitiesController extends AppController {

	public $name = 'Entities';
	public $uses = array(
		'UserDetail', 'Category', 'Workspace', 'Area', 'Element', 'Project', 'ElementLink', 'ElementDocument', 'ElementNote', 'ElementMindmap', 'ElementFeedback', 'ElementFeedbackDetail', 'Feedback', 'ElementDecision', 'ElementDecisionDetail', 'Decision', 'ProjectGroup', 'ProjectGroupUser', 'UserProject', 'ProjectWorkspace', 'ProjectPermission', 'ElementPermission', 'WorkspacePermission', 'EmailNotification', 'ElementAssignment', 'DeleteData', 'ElementType', 'ProjectElementType', 'ElementCostHistory', 'ElementDependancyRelationship', 'ElementDependency', 'ElementCost', 'ShareElement','UserPermission','SignoffTask' );
	public $helpers = array(
		'Html', 'Form', 'Session', 'Time', 'Text', 'Common', 'ViewModel', 'Group', 'Wiki','Permission');
	public $user_id = null;
	public $pagination = null;
	public $_token = null;
	public $objView = null;
	public $activity_offset = 50;
	public $team_offset = 50;

	public $components = array(
		'Common', 'Group', 'Users'
		// 'Security'
	);

	public function beforeFilter() {
		parent::beforeFilter();
		// $this->Security->blackHoleCallback = 'error';
		$this->set('controller', 'entities');

		$view = new View();
		$this->objView = $view;

		if ($this->request->is('ajax')) {
			$this->response->disableCache();
		}

		$this->Auth->allow('elementScheduleOverdueEmailCron');

		$this->user_id = $this->Auth->user('id');

		// Pagination
		$this->pagination['limit'] = 4;
		$this->pagination['show_summary'] = true;
		$this->pagination['options'] = array(
			'url' => array_merge(array(
				'controller' => $this->request->params['controller'],
				'action' => 'get_more',
			), $this->request->params['pass'], $this->request->params['named']),
		);

		$this->set('JeeraPaging', $this->pagination);
		// Pagination
		// session token
		$this->_token = str_replace("0.", "", microtime());
		$this->_token = str_replace(" ", "", $this->_token);

		// echo Security::hash('random string').'_'.$this->_token;
		// session token
		// $this->Security->csrfExpires = '+1 hour';
		if ($this->request->isAjax()) {
			// $this->_validateToken($this->request, $this->params);
		}

		$this->_doc_file_ext();


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

	public function task_list() {
		$this->layout = 'inner';
		$viewVars['page_heading'] = 'Task Lists';
		$this->set('title_for_layout', __('Task Lists', true));
		$this->set('page_heading', __('Task Lists', true));
		$viewVars['page_subheading'] = 'View Tasks in Workspaces';

		$total_my_projects = get_my_projects($this->Session->read('Auth.User.id'), true);
		$total_recieved = $this->requestAction(array('controller' => 'projects', 'action' => 'total_recieved'));
		$total_grp_rec = $this->requestAction(array('controller' => 'groups', 'action' => 'shared_Totprojects'));

		if (empty($total_my_projects) && empty($total_recieved) && empty($total_grp_rec)) {
			$this->Session->setFlash('There is no project in your account.', 'error');
			$this->redirect(array('controller' => 'dashboards', 'action' => 'project_center'));
		}

		$proj = $wsp = $stus = 0;

		$project_id = $workspace = $status = 0;

		if (isset($this->params['named']) && !empty($this->params['named'])) {
			$params = $this->params['named'];
			$viewVars['project_id'] = $proj = isset($params['project']) && !empty($params['project']) ? $params['project'] : null;
			$viewVars['workspace_id'] = $wsp = isset($params['workspace']) && !empty($params['workspace']) ? $params['workspace'] : null;
			$viewVars['status'] = $stus = isset($params['status']) && !empty($params['status']) ? $params['status'] : null;
		}

		$projects = [];
		$mprojects = get_my_projects($this->user_id);
		$rprojects = get_rec_projects($this->user_id);
		$gprojects = group_rec_projects($this->user_id);

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
			$projects = array_map("strip_tags", $projects);
			$projects = array_map("trim", $projects);
			$projects = array_map(function ($v) {
				return html_entity_decode($v, ENT_COMPAT, "UTF-8");
			}, $projects);
			natcasesort($projects);
		}
		$this->set("list_projects", $projects);

		$this->setJsVar('js_project', $proj);
		$this->setJsVar('js_workspace', $wsp);
		$this->setJsVar('js_status', $stus);

		$viewVars['crumb'] = [
			'last' => [
				'data' => [
					'title' => 'Task Lists',
					'data-original-title' => 'Task Lists',
				],
			],
		];

		$this->set($viewVars);
	}

	public function task_list_projects() {

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

				/* if (isset($post['type']) && $post['type'] == 1) {
						$response['content'] = get_my_projects($this->user_id);
					} else if (isset($post['type']) && $post['type'] == 2) {
						$projects = get_rec_projects($this->user_id);
						$response['content'] = $projects;

					} else if (isset($post['type']) && $post['type'] == 3) {
						$response['content'] = group_rec_projects($this->user_id);
				*/

				$my_projects = array();
				$rec_projects = array();
				$grp_projects = array();
				if (!empty(get_my_projects($this->user_id))) {
					$my_projects = get_my_projects($this->user_id);
				}
				if (!empty(get_rec_projects($this->user_id))) {
					$rec_projects = get_rec_projects($this->user_id);
				}
				if (!empty(group_rec_projects($this->user_id))) {
					$grp_projects = group_rec_projects($this->user_id);
				}
				$all_projects = array_merge($my_projects, $rec_projects, $grp_projects);
				$response['content'] = $all_projects;

				// pr($response['content']);
				// pr($post, 1);
			}
			echo json_encode($response);
			exit();
		}
	}

	public function task_list_workspaces() {

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
				$result = null;
				if (isset($post['project_id']) && !empty($post['project_id'])) {
					$workspaces = get_project_workspace($post['project_id']);
					$response['content'] = ($workspaces) ? Set::combine($workspaces, '{n}.Workspace.id', '{n}.Workspace.title') : null;
				}

			}
			// $this->set('response', $response);

			// $this->render('/Entities/partials/task_list_workspaces');
			echo json_encode($response);
			exit();
		}
	}

	public function task_list_areas() {

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
				$result = null;
				if (isset($post['workspace_id']) && !empty($post['workspace_id'])) {
					$response['success'] = true;
					$response['content'] = get_workspace_areas($post['workspace_id']);
				}

				// pr($response['content'], 1);

			}
			echo json_encode($response);
			exit();
		}
	}

	public function task_list_el_date($workspace_id = null, $area_id = null, $element_id = null) {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			if (isset($workspace_id) && !empty($workspace_id)) {

				$response['workspace_id'] = $workspace_id;
				$response['area_id'] = $area_id;

				// Get all workspace elements
				$response['all_elements'] = $this->objView->loadHelper('ViewModel')->area_elements($area_id, false, $element_id);

			}
			$this->set('response', $response);

			$this->render('/Entities/partials/task_list_el_date');
		}
	}

	public function task_list_save_elements($area_id = null) {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$response = [
				'success' => false,
				'status' => 400,
				'content' => null,
			];

			if ($this->request->is('post') || $this->request->is('put')) {

				$workspace_id = area_workspace_id($area_id, 0);

				$project_id = workspace_pid($workspace_id);

				$project_detail = $this->objView->loadHelper('ViewModel')->getProjectDetail($project_id, -1);

				//project
				$date = $this->objView->loadHelper('Common')->getDateStartOrEnd($project_id);
				$mindate = isset($date['start_date']) && !empty($date['start_date']) ? date("d-m-Y", strtotime($date['start_date'])) : '';
				$maxdate = isset($date['end_date']) && !empty($date['end_date']) ? date("d-m-Y", strtotime($date['end_date'])) : '';

				//workspace
				$date_workspace = $this->objView->loadHelper('Common')->getDateStartOrEnd_elm($workspace_id);

				$mindate_workspace = isset($date_workspace['start_date']) && !empty($date_workspace['start_date']) ? date("d-m-Y", strtotime($date_workspace['start_date'])) : '';
				$maxdate_workspace = isset($date_workspace['end_date']) && !empty($date_workspace['end_date']) ? date("d-m-Y", strtotime($date_workspace['end_date'])) : '';

				$allErrors = null;
				$allData = null;
				$post = $this->request->data;

				foreach ($post as $k => $row) {
					$allData['Element'][$k]['id'] = $row['id'];

					$allData['Element'][$k]['start_date'] = (isset($row['start_date']) && !empty($row['start_date'])) ? date('Y-m-d', strtotime($row['start_date'])) : null;

					$allData['Element'][$k]['end_date'] = (isset($row['end_date']) && !empty($row['end_date'])) ? date('Y-m-d', strtotime($row['end_date'])) : null;

					if ((isset($allData['Element'][$k]['start_date']) && !empty($allData['Element'][$k]['start_date'])) && (isset($allData['Element'][$k]['end_date']) && !empty($allData['Element'][$k]['end_date']))) {
						$allData['Element'][$k]['date_constraints'] = 1;
					} else {
						$allData['Element'][$k]['date_constraints'] = 0;
					}

					if ((isset($allData['Element'][$k]['start_date']) && !empty($allData['Element'][$k]['start_date'])) && (isset($allData['Element'][$k]['end_date']) && !empty($allData['Element'][$k]['end_date'])) && ($allData['Element'][$k]['end_date'] < $allData['Element'][$k]['start_date'])) {
						$allErrors[$row['id']]['start_end_date'] = true;
					}

					if (isset($mindate_workspace) && (!empty($allData['Element'][$k]['start_date']) && date('Y-m-d', strtotime($allData['Element'][$k]['start_date'])) < date('Y-m-d', strtotime($mindate_workspace)))) {
						// Start date error
						$allErrors[$row['id']]['start_date'] = true;

					}

					if (isset($maxdate_workspace) && (!empty($allData['Element'][$k]['end_date']) && date('Y-m-d', strtotime($allData['Element'][$k]['end_date'])) > date('Y-m-d', strtotime($maxdate_workspace)))) {
						// end date error
						$allErrors[$row['id']]['end_date'] = true;
					}
				}

				if (isset($allData) && !empty($allData)) {

					if (isset($allData) && empty($allErrors)) {

						foreach ($post as $k => $row) {

							//===== Start for Email Notification ======================
							$element_id = $row['id'];
							$latestDate = $this->Element->findById($element_id);

							$startDate = strtotime($latestDate['Element']['start_date']);
							$endDate = strtotime($latestDate['Element']['end_date']);

							$willSend = false;
							if (($startDate < strtotime($row['start_date']) || $startDate > strtotime($row['start_date'])) && ($endDate < strtotime($row['end_date']) || $endDate > strtotime($row['end_date']))) {
								$willSend = true;

							} else if ($startDate < strtotime($row['start_date']) || $startDate > strtotime($row['start_date'])) {
								$willSend = true;

							} else if ($endDate < strtotime($row['end_date']) || $endDate > strtotime($row['end_date'])) {
								$willSend = true;
							} else {

							}

							if ($willSend == true) {

								$elementDetail = $this->Element->findById($element_id);
								$elementName = '';
								if (isset($elementDetail['Element']['title']) && !empty($elementDetail['Element']['title'])) {
									$elementName = $elementDetail['Element']['title'];
								}
								$project_id = element_project($element_id);
								$all_owner = element_users(array($element_id), $project_id);

								$this->elementScheduleChangeEmail($elementName, $all_owner, $element_id);
								/************** socket messages **************/
								if (SOCKET_MESSAGES) {
									$current_user_id = $this->user_id;
									$e_users = $all_owner;
									if (isset($e_users) && !empty($e_users)) {
										if (($key = array_search($current_user_id, $e_users)) !== false) {
											unset($e_users[$key]);
										}
									}
									$open_users = null;
									if (isset($e_users) && !empty($e_users)) {
										foreach ($e_users as $key1 => $value1) {
											if (web_notify_setting($value1, 'element', 'element_schedule_change')) {
												$open_users[] = $value1;
											}
										}
									}
									$userDetail = get_user_data($current_user_id);
									$content = [
										'notification' => [
											'type' => 'task_schedule_change',
											'created_id' => $current_user_id,
											'project_id' => $project_id,
											'refer_id' => $element_id,
											'creator_name' => $userDetail['UserDetail']['full_name'],
											'subject' => 'Task schedule change',
											'heading' => 'Task: ' . strip_tags(getFieldDetail('Element', $element_id, 'title')),
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
							//======== End Email Notification ==============
						}

						if ($this->Element->saveAll($allData['Element'])) {

							$response['success'] = true;
							$this->autorender = FALSE;
						}

					} else {
						$response['content'] = $allErrors;
						$response['success'] = false;
					}
				}

				echo json_encode($response);
				exit;
			}
		}

	}

	public function task_list_ws_date($project_id = null, $workspace_id = null) {

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

			$this->render('/Entities/partials/task_list_ws_date');
		}
	}

	/**
	 * Save newly created workspace
	 *
	 * @param $project_id
	 * @return JSON array
	 */
	public function task_list_save_workspace($project_id = null) {

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
				$start = $this->request->data['Workspace']['start_date'];
				$end = $this->request->data['Workspace']['end_date'];
				$check = $this->Common->check_date_validation_ws($start, $end, $project_id, $this->request->data['Workspace']['id']);

				$this->Workspace->set($this->request->data);

				if ($this->Workspace->validates()) {

					$this->loadModel('ProjectWorkspace');
					$this->Workspace->create();

					if (empty($check) || $check == null) {

						$this->request->data['Workspace']['start_date'] = date('Y-m-d', strtotime($this->request->data['Workspace']['start_date']));
						$this->request->data['Workspace']['end_date'] = date('Y-m-d', strtotime($this->request->data['Workspace']['end_date']));

						if (isset($this->request->data['Workspace']['id']) && !empty($this->request->data['Workspace']['id'])) {
							$this->Workspace->id = $this->request->data['Workspace']['id'];
						}

						// pr($this->request->data, 1);
						$latestDate = $this->Workspace->find('first', ['conditions' => ['Workspace.id' => $this->request->data['Workspace']['id']]]);

						if ($this->Workspace->save($this->request->data)) {

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
								$this->Common->workspaceScheduleChangeEmail($project_id, $this->request->data['Workspace']['id']);
							}

							// ============== End Email Notification =====================

							$response['success'] = true;
							$this->Common->projectModified($project_id, $this->user_id);
							$this->autorender = FALSE;
						}
					} else {
						if (!empty($check) && $check != null) {
							$response['date_error'] = $check;
						}

					}
				} else {
					$response['content'] = $this->validateErrors($this->Workspace);
				}

				echo json_encode($response);
				exit;
			}
		}

	}

	public function filtered_list() {
		$html = '';
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

			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;
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

				if (isset($post['project_id']) && !empty($post['project_id'])) {
					$params['project_id'] = $post['project_id'];
					$params['wsp_selected'] = true;
					$pw_condition['ProjectWorkspace.project_id'] = $post['project_id'];

					if (isset($post['ws_id']) && !empty($post['ws_id'])) {
						$pw_condition['ProjectWorkspace.workspace_id'] = $post['ws_id'];
						$params['wsp_selected'] = false;
					}
					$pw_condition['Workspace.studio_status !='] = 1;

					$workspaces = $elements = $ws_ids = $area_ids = null;

					$this->ProjectWorkspace->Behaviors->load('Containable');

					$workspaces = $this->ProjectWorkspace->find('all', ['conditions' => $pw_condition, 'contain' => 'Workspace', 'order' => ['ProjectWorkspace.sort_order']]);

					if (isset($workspaces) && !empty($workspaces)) {

						foreach ($workspaces as $key => $value) {

							$workspace = $value['Workspace'];

							$ws_ids[] = $workspace['id'];

							$areas = $viewModel->workspace_areas($workspace['id'], false, true, true);

							if (isset($areas) && !empty($areas)) {
								if (is_array($area_ids)) {
									$area_ids = array_merge($area_ids, array_values($areas));
								} else {
									$area_ids = array_values($areas);
								}

							}

						}
					}

					if (isset($ws_ids) && !empty($ws_ids)) {
						$params['ws_ids'] = $ws_ids;
					}

					if (isset($post['area_id']) && !empty($post['area_id'])) {
						$area_ids = $post['area_id'];
					}
					$params['area_ids'] = $area_ids;

					if ((isset($post['sort_by']) && !empty($post['sort_by']))) {
						$params['sort_by'] = $post['sort_by'];
					}

					if ((isset($post['task_status']) && !empty($post['task_status']))) {
						$params['task_status'] = $post['task_status'];
					}

				}
				$this->set('params', $params);

				$view = new View($this, false);
				$view->viewPath = 'Entities/partials'; // Directory inside view directory to search for .ctp files
				$view->layout = false; // if you want to disable layout
				$view->set('data', $data);

				$html = $view->render('task_list');

			}
			echo json_encode($html);
			exit();
		}
	}

	public function element_people($element_id = null) {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$response = [
				'success' => false,
				'msg' => '',
				'content' => null,
			];

			$view = new View($this, false);
			$view->viewPath = 'Entities/element_files'; // Directory inside view directory to search for .ctp files
			$view->layout = false; // if you want to disable layout

			if (isset($element_id) && !empty($element_id)) {
				$view->set('element_id', $element_id);
			}

			$html = $view->render('element_people');

			echo $html;
			exit();
		}
	}

	public function quick_share_permissions($project_id = null, $user_id = null, $element_id = null) {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$viewData = null;

			$viewData['project_id'] = $project_id;
			$viewData['user_id'] = $user_id;
			$viewData['element_id'] = $element_id;

			$this->set($viewData);

			$this->render('/Entities/element_files/quick_share_permissions');

		}
	}

	public function apply_user_filter($project_id, $element_id, $type, $selected, $match_all) {
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
				$taskUsers = $viewModel->taskUsers($element_id);
				if(!empty($taskUsers)) {
					$user_ids = Set::extract('{n}/user_details/user_id', $taskUsers);
					$nonTaskUsersIdsImp = implode(',', $user_ids);
					$concatQuery = " AND user_skills.user_id NOT IN ($nonTaskUsersIdsImp) ";
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

	public function getQuickShareUserListOld($project_id = null, $element_id = null) {
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			$this->autoRender = false;

			$viewData['project_id'] = $project_id;
			$viewData['element_id'] = $element_id;
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
						$selectedUsers = $this->apply_user_filter($project_id, $element_id, $type, $selected, $match_all);

						$perm_users = array_intersect($perm_users, $selectedUsers);

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
				$viewData['element_id'] = $element_id;
				$viewData['perm_users'] = $perm_users;
				$viewData['not_shared_user'] = array_keys($not_shared);
			}
			$this->set('viewData', $viewData);
			$this->render('/Entities/element_files/quick_share_user_list', 'ajax');
		}
	}

	public function getQuickShareUserList($project_id = null, $element_id = null) {
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			$this->autoRender = false;

			$viewData['project_id'] = $project_id;
			$viewData['element_id'] = $element_id;
			$viewData['perm_users'] = array();
			$viewData['not_shared_user'] = array();
			$items_per_page = $this->quick_share_paging;

			if ($this->request->isAjax()) {
				$type = $this->request->data['type'];

				$selected = (isset($this->request->data['selected']) && !empty($this->request->data['selected'])) ? trim($this->request->data['selected']) : '';
				$match_all = (isset($this->request->data['is_match_all']) && trim($this->request->data['is_match_all']) != '') ? $this->request->data['is_match_all'] : false;
				$offset = (isset($this->request->data['page']) && !empty(trim($this->request->data['page']))) ? trim($this->request->data['page']) : 0;

				$perm_users_sliced = array();

				$perm_users_count = get_selected_users_workspace_with_paging($this->user_id, $project_id, $element_id, 'task', false, $type, $selected, $match_all, $items_per_page, $offset, 1);

				$perm_users = get_selected_users_workspace_with_paging($this->user_id, $project_id, $element_id, 'task', false, $type, $selected, $match_all, $items_per_page, $offset, 0);

				if(!empty($perm_users)) {
					//$perm_users_sliced = array_slice($perm_users, $offset, $this->quick_share_paging, true);
				}

				$viewData['project_id'] = $project_id;
				$viewData['element_id'] = $element_id;
				$viewData['perm_users'] = $perm_users;
				$viewData['tot_perm_users'] = (isset($perm_users_count[0]) && isset($perm_users_count[0][0]) && isset($perm_users_count[0][0]['u_cnt'])) ? $perm_users_count[0][0]['u_cnt'] : 0;

				$viewData['type'] = $type;
			}
			$this->set('viewData', $viewData);
			$this->render('/Entities/element_files/quick_share_user_list', 'ajax');
		}
	}

	public function quick_share($project_id = null, $element_id = null) {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$viewData = null;

			$perm_users = get_selected_users_workspace($this->user_id, $project_id, $element_id, false, 'task');
			$perm_users_sliced = array_slice($perm_users, 0, $this->quick_share_paging, true);

			/*$not_shared = get_selected_users($this->user_id, $project_id);
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
			$viewData['element_id'] = $element_id;
			$viewData['perm_users'] = $perm_users_sliced;
			$viewData['perm_users_all'] = array_keys($perm_users);

			$this->set($viewData);

			$this->render('/Entities/element_files/quick_share');

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
				$element_workspace = element_workspace($post['Share']['element_id']);
				$userProjectId = project_upid($project_id);
				$userOwnerId = project_owner($project_id);

				$is_element_permit = $this->objView->loadHelper('Permission')->is_element_permit($post['Share']['user_id'], $post['Share']['element_id']);

				if(!isset($is_element_permit) || empty($is_element_permit)){
					$task_data = [
						'project_id' => $project_id,
						'workspace_id' => $element_workspace,
						'element_id' => $post['Share']['element_id'],
						'element_type' => 'element_tasks',
						'user_id' => $this->user_id,
						'updated_user_id' => $this->user_id,
						'message' => 'Task shared',
						'updated' => date("Y-m-d H:i:s"),
					];
					$this->loadModel('Activity');
					$this->Activity->id = null;
					$this->Activity->save($task_data);
				}

				if (isset($post['ElementPermission']['id']) && !empty($post['ElementPermission']['id'])) {
					// update only element permissions table

					if ((!isset($post['ElementPermission']['permit_read']) || empty($post['ElementPermission']['permit_read'])) &&
						(!isset($post['ElementPermission']['permit_edit']) || !empty($post['ElementPermission']['permit_edit'])) &&
						(!isset($post['ElementPermission']['permit_delete']) || !empty($post['ElementPermission']['permit_delete'])) &&
						(!isset($post['ElementPermission']['permit_copy']) || !empty($post['ElementPermission']['permit_copy'])) &&
						(!isset($post['ElementPermission']['permit_move']) || !empty($post['ElementPermission']['permit_move']))) {
						if ($this->ElementPermission->delete($post['ElementPermission']['id'])) {
							if( PROCEDURE_MODE == 1 ) {
								$delCond = array('ShareElement.element_permission_id'=>$post['ElementPermission']['id']);
								$this->ShareElement->deleteAll($delCond);
							}
							$response['success'] = true;
						}
					} else {
						$element_permit = [
							'id' => $post['ElementPermission']['id'],
							'user_id' => $post['Share']['user_id'],
							'element_id' => $post['Share']['element_id'],
							'workspace_id' => $element_workspace,
							'project_id' => $project_id,
							'permit_read' => (isset($post['ElementPermission']['permit_read']) && !empty($post['ElementPermission']['permit_read'])) ? 1 : 0,
							'permit_edit' => (isset($post['ElementPermission']['permit_edit']) && !empty($post['ElementPermission']['permit_edit'])) ? 1 : 0,
							'permit_delete' => (isset($post['ElementPermission']['permit_delete']) && !empty($post['ElementPermission']['permit_delete'])) ? 1 : 0,
							'permit_copy' => (isset($post['ElementPermission']['permit_copy']) && !empty($post['ElementPermission']['permit_copy'])) ? 1 : 0,
							'permit_move' => (isset($post['ElementPermission']['permit_move']) && !empty($post['ElementPermission']['permit_move'])) ? 1 : 0,
						];

						// pr($element_permit, 1);
						if ($this->ElementPermission->save($element_permit)) {
							$response['success'] = true;

							/************** socket messages **************/
							if (SOCKET_MESSAGES) {
								$heading = 'Sharer';
								$p_permission = $this->Common->project_permission_details($project_id, $post['Share']['user_id']);
								if (isset($p_permission) && !empty($p_permission)) {
									if ($p_permission['ProjectPermission']['project_level'] > 0) {
										$heading = 'Owner';
									}
								}

								$send_notification = false;
								if (web_notify_setting($post['Share']['user_id'], 'element', 'task_sharing')) {
									$send_notification = true;
								}
								$userDetail = get_user_data($this->user_id);

								$content = [
									'socket' => [
										'notification' => [
											'type' => 'task_sharing',
											'created_id' => $this->user_id,
											'project_id' => $project_id,
											'refer_id' => $post['Share']['element_id'],
											'creator_name' => $userDetail['UserDetail']['full_name'],
											'subject' => 'Task sharing updated',
											'heading' => 'Permission: ' . $heading,
											'sub_heading' => 'Project: ' . strip_tags(getFieldDetail('Project', $project_id, 'title')).'<br />Workspace: ' . strip_tags(getFieldDetail('Workspace', $element_workspace, 'title')).'<br />Task: ' . strip_tags(getFieldDetail('Element', $post['Share']['element_id'], 'title')),
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
							$this->Common->task_share_email($post['Share']['user_id'], $project_id, $element_workspace, $post['Share']['element_id'], true);
						}
					}
				} else {
					// create new entries for project, workspace and element permissions tables
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
							// Add user project connection to mongo db
							$this->Users->userConnections($post['Share']['user_id'], $project_id);
						}
					}

					$wsp_permissions = $this->Common->wsp_permission_details(workspace_pwid($project_id, $element_workspace), $project_id, $post['Share']['user_id']);
					if (!isset($wsp_permissions) || empty($wsp_permissions)) {
						$workspace_permit = [
							'user_id' => $post['Share']['user_id'],
							'user_project_id' => $userProjectId,
							'project_workspace_id' => workspace_pwid($project_id, $element_workspace),
							'permit_read' => 1,
							'permit_add' => 0,
							'permit_edit' => 0,
							'permit_delete' => 0,
						];
						if ($this->WorkspacePermission->save($workspace_permit)) {
							$response['success'] = true;
						}
					}

					$element_permit = [
						'user_id' => $post['Share']['user_id'],
						'element_id' => $post['Share']['element_id'],
						'workspace_id' => $element_workspace,
						'project_id' => $project_id,
						'permit_read' => (isset($post['ElementPermission']['permit_read']) && !empty($post['ElementPermission']['permit_read'])) ? 1 : 0,
						'permit_edit' => (isset($post['ElementPermission']['permit_edit']) && !empty($post['ElementPermission']['permit_edit'])) ? 1 : 0,
						'permit_delete' => (isset($post['ElementPermission']['permit_delete']) && !empty($post['ElementPermission']['permit_delete'])) ? 1 : 0,
						'permit_copy' => (isset($post['ElementPermission']['permit_copy']) && !empty($post['ElementPermission']['permit_copy'])) ? 1 : 0,
						'permit_move' => (isset($post['ElementPermission']['permit_move']) && !empty($post['ElementPermission']['permit_move'])) ? 1 : 0,
					];
					if ($this->ElementPermission->save($element_permit)) {
						$eshare_insert_id = $this->ElementPermission->inserted_ids;
						$response['success'] = true;

						// create new entries for project, workspace and element permissions tables
						if( PROCEDURE_MODE == 1 ) {
							/*  Start Stored Procedure ================================== */

							 if( !empty($eshare_insert_id) ){

								foreach($eshare_insert_id as $epids){

									$elePermission = $this->ElementPermission->findById($epids);

									$epProject_id = $elePermission['ElementPermission']['project_id'];
									$epWorkspace_id = $elePermission['ElementPermission']['workspace_id'];
									$epElement_id = $elePermission['ElementPermission']['element_id'];
									$epUser_id = $elePermission['ElementPermission']['user_id'];
									$eleArea_id = element_area($elePermission['ElementPermission']['element_id']);

									if( $this->objView->loadHelper('ViewModel')->projectPermitType($epProject_id, $epUser_id) ){
										$user_level = 1;
									} else {
										$user_level = 0;
									}

								}
							}
							/* End Stored Procedure =========================== */
						}


						if (isset($p_permission) && !empty($p_permission)) {
							/************* socket messages *************/
							if (SOCKET_MESSAGES) {
								$heading = 'Sharer';
								$p_permission = $this->Common->project_permission_details($project_id, $post['Share']['user_id']);
								if ($p_permission['ProjectPermission']['project_level'] > 0) {
									$heading = 'Owner';
								}

								$send_notification = false;
								if (web_notify_setting($post['Share']['user_id'], 'element', 'task_sharing')) {
									$send_notification = true;
								}
								$userDetail = get_user_data($this->user_id);

								$content = [
									'socket' => [
										'notification' => [
											'type' => 'task_sharing',
											'created_id' => $this->user_id,
											'project_id' => $project_id,
											'refer_id' => $post['Share']['element_id'],
											'creator_name' => $userDetail['UserDetail']['full_name'],
											'subject' => 'Task sharing',
											'heading' => 'Permission: Sharer',
											'sub_heading' => 'Project: ' . strip_tags(getFieldDetail('Project', $project_id, 'title')).'<br />Workspace: ' . strip_tags(getFieldDetail('Workspace', $element_workspace, 'title')).'<br />Task: ' . strip_tags(getFieldDetail('Element', $post['Share']['element_id'], 'title')),
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
							/************* socket messages **************/
							$this->Common->task_share_email($post['Share']['user_id'], $project_id, $element_workspace, $post['Share']['element_id']);
						} else {
							$this->Common->task_share_email($post['Share']['user_id'], $project_id, $element_workspace, $post['Share']['element_id']);
							$this->Common->getProjectAllUser($project_id, $post['Share']['user_id']);
							/************* socket messages *************/
							if (SOCKET_MESSAGES) {
								$heading = 'Sharer';
								$p_permission = $this->Common->project_permission_details($project_id, $post['Share']['user_id']);
								if ($p_permission['ProjectPermission']['project_level'] > 0) {
									$heading = 'Owner';
								}

								$send_notification = false;
								if (web_notify_setting($post['Share']['user_id'], 'element', 'task_sharing')) {
									$send_notification = true;
								}
								$userDetail = get_user_data($this->user_id);
								$content = [
									'socket' => [
										'notification' => [
											'type' => 'task_sharing',
											'created_id' => $this->user_id,
											'project_id' => $project_id,
											'refer_id' => $post['Share']['element_id'],
											'creator_name' => $userDetail['UserDetail']['full_name'],
											'subject' => 'Task sharing',
											'heading' => 'Permission: Sharer',
											'sub_heading' => 'Project: ' . strip_tags(getFieldDetail('Project', $project_id, 'title')).'<br />Workspace: ' . strip_tags(getFieldDetail('Workspace', $element_workspace, 'title')).'<br />Task: ' . strip_tags(getFieldDetail('Element', $post['Share']['element_id'], 'title')),
											'date_time' => timezoneTimeConvertor($this->user_id, date('Y-m-d g:iA'), $format = 'd M Y g:iA') . user_country($this->user_id),
										]
									]
								];

								if ($send_notification) {
									$content['socket']['received_users'] = [$post['Share']['user_id']];
								}

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
							/************* socket messages **************/
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
						}
					}

				}
			}
			echo json_encode($response);
			exit;
		}
	}

	public function update_category() {
		$this->layout = 'inner';

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
				$this->Category->set($this->request->data['Category']);
				if ($this->Category->validates()) {

					$id = null;
					if (isset($post['Category']['id']) && !empty($post['Category']['id'])) {
						$id = $post['Category']['id'];

						if ($this->Category->save($this->request->data['Category'])) {

							$content = $this->Category->find('first', [
								'conditions' => [
									'Category.id' => $id,
								],
							]);
							// // pr($content, 1);
							$response['msg'] = "Category has been saved successfully.";
							$response['success'] = true;
							$response['content'] = $content;
						} else {
							$response['msg'] = "Category could not be saved.";
						}
					}
				} else {
					$errors = $this->validateErrors($this->Category);
					$response['content'] = $errors;
				}
			}
			echo json_encode($response);
			exit();
		}

		$categories = $this->Category->find('threaded', array(
			'fields' => array(
				'id',
				'title',
				'parent_id',
			),
		));
		// $this->set(compact('categories'));
		// // pr($categories, 1);
		$this->set('categories', $categories);
	}

	public function update_date($id = null) {
		$this->layout = null;

		$data = $this->Element->find('first', ['conditions' => ['Element.id' => $id], 'recursive' => -1, 'fields' => ['id', 'modified']]);
		//$this->set('data', $data);
		//$d = date('d M, Y g:iA', strtotime($data['Element']['modified']));
		$d = $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A', strtotime($data['Element']['modified'])), $format = 'd M, Y g:iA');
		echo json_encode($d);
		exit;
		//$this->render(null, null, APP . 'views' . DS . 'Entities' . DS . 'update_date.ctp');
	}

	public function get_more() {
		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;
				$model = 'Element' . ucwords($post['model']);
				$dbModel = '';

				if (isset($this->params['named']) && !empty($this->params['named'])) {
					if (isset($this->params['named']['model']) && !empty($this->params['named']['model'])) {
						$dbModel = $this->params['named']['model'];
					}
				}

				if (isset($post['limit']) && !empty($post['limit'])) {
					$this->pagination['limit'] = $post['limit'];
				}
				$element_id = 0;
				if (isset($this->params['pass']) && !empty($this->params['pass'])) {
					if (isset($this->params['pass'][0]) && !empty($this->params['pass'][0])) {
						$element_id = $this->params['pass'][0];
					}
				}
				if (!is_null($element_id)) {
					$paginator = array(
						'conditions' => array(
							$dbModel . '.status' => 1,
							$dbModel . '.element_id' => $element_id,
						),
						'recursive' => -1,
						'limit' => $this->pagination['limit'],
						"order" => $dbModel . ".created DESC",
					);

					$this->paginate = $paginator;
					$this->set('rows', $this->paginate($dbModel));
					$this->set('model', $model);

					$this->pagination['show_summary'] = true;
					$this->pagination['model'] = $dbModel;
					$this->set('JeeraPaging', $this->pagination);
				}
				$this->render('/Entities/partials/list_more_' . strtolower($post['model']));
			}
		}
		return;
	}

	/*
		     * @name popover
		     * @todo Open Popup Modal Box for update area title
		     * @access public
		     * @package App/Controller/ProjectsController
		     * @return void
	*/

	public function auth_check() {
		$this->layout = 'ajax';
		// $this->autoRender = false;

		if ($this->request->isAjax()) {

			$response = [
				'success' => false,
				'msg' => '',
				'content' => null,
			];

			$row = null;

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;

				$hashedUserInput = AuthComponent::password($post['User']['password']);
				$user_check_res = $this->User->find('count', array(
					'conditions' => array(
						'User.password' => $hashedUserInput,
						'User.id' => $this->user_id,
					),
				));

				if ($user_check_res > 0) {
					$response['success'] = true;
					$response['content'] = $user_check_res;
				} else {
					$response['content'] = $user_check_res;
					$response['msg'] = "You are not authorized to access this.";
				}
			}
			echo json_encode($response);
			exit();
		}
	}

	/*
		     * @name popover
		     * @todo Open Popup Modal Box for update area title
		     * @access public
		     * @package App/Controller/ProjectsController
		     * @return void
	*/

	public function auth_delete() {
		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$response = $row = null;

			$this->render(DS . 'Elements' . DS . 'popover_auth');
		}
	}

	/*
		     * @name index
		     * @access public
		     * @package App/Controller/EntitiesController
	*/

	public function index($element_id = null) {
		$this->layout = 'inner';

		$this->set('title_for_layout', __('Elements', true));
		$this->set('page_heading', __('Elements', true));

		$user_id = $this->Auth->user('id');

		$this->Element->recursive = -1;
		$elements = $this->Element->find('all', array(
			'conditions' => [
				'Element.area_id' => 1,
			],
			'order' => 'Element.id ASC',
		));

		$this->set('elements', $elements);
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

							$updated_title = $this->request->data['Area']['title'];

							// $response['success'] = true;
							$response['msg'] = "Success";
							$response['content'] = [
								'title' => $updated_title,
							];
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

	function create_element() {
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
				// pr($this->request->data, 1);
				if(!isset($this->request->data['Element']['date_constraints']) || empty($this->request->data['Element']['date_constraints'])){
					$this->request->data['Element']['date_constraints'] = 0;
				}
				$user_id = $this->Auth->user('id');

				$this->Element->set($this->request->data);

				if ($this->Element->validates()) {

					if(isset($this->request->data['Element']['start_date']) && !empty($this->request->data['Element']['start_date'])){
						$this->request->data['Element']['start_date'] = date('Y-m-d h:i:s', strtotime($this->request->data['Element']['start_date']));
					}
					if(isset($this->request->data['Element']['end_date']) && !empty($this->request->data['Element']['end_date'])){
						$this->request->data['Element']['end_date'] = date('Y-m-d h:i:s', strtotime($this->request->data['Element']['end_date']));
					}

					$area_id = $this->request->data['Element']['area_id'];

					$count = $neighbors = $this->Element->find("count", [
						'conditions' => [
							'Element.area_id' => $area_id,
						],'recursive'=>-1
					]);

					$dataEE = $this->Element->find('first', array('conditions' => array('Element.area_id' => $area_id), 'order' => array('Element.sort_order' => 'DESC'), 'fields' => 'Element.sort_order', 'recursive' => -1));

					if (isset($dataEE) && !empty($dataEE)) {
						if (isset($dataEE['Element']['sort_order']) && $dataEE['Element']['sort_order'] > 0) {
							$this->request->data['Element']['sort_order'] = $dataEE['Element']['sort_order'] + 1;
						} else {
							$this->request->data['Element']['sort_order'] = (!is_null($count) && $count > 0) ? ($count + 1) : 1;
						}
					} else {
						$this->request->data['Element']['sort_order'] = (!is_null($count) && $count > 0) ? ($count + 1) : 1;
					}

					$this->loadModel('Area');
					$aar = $this->Area->find('first', array(
						'conditions' => array(
							'Area.id' => $area_id,
						),
						'recursive' => -1,
					));

					$wsp_id = $aar['Area']['workspace_id'];

					$this->loadModel('ProjectWorkspace');

					$pr_ids = $this->ProjectWorkspace->find('first', array(
						'conditions' => array(
							'ProjectWorkspace.workspace_id' => $wsp_id,
						),
					));

					$pr_id = $pr_ids['ProjectWorkspace']['project_id'];
					$this->request->data['Element']['updated_user_id'] = $this->Auth->user('id');
					$this->request->data['Element']['created_by'] = $this->Auth->user('id');

					$this->Element->unbindModel(array('hasOne' => ['ElementDecision']));

					if ($this->Element->save($this->request->data['Element'])) {

						$insert_id = $this->Element->getLastInsertId();
						if (!empty($this->request->data['ElementType']['type_id'])) {
							$this->request->data['ElementType']['element_id'] = $insert_id;
							$this->ElementType->save($this->request->data['ElementType']);
						}


						$this->loadModel('ElementPermission');
						$arr['ElementPermission']['user_id'] = $user_id;
						$arr['ElementPermission']['element_id'] = $insert_id;
						$arr['ElementPermission']['project_id'] = $pr_id;
						$arr['ElementPermission']['workspace_id'] = $wsp_id;
						$arr['ElementPermission']['permit_read'] = 1;
						$arr['ElementPermission']['permit_add'] = 1;
						$arr['ElementPermission']['permit_edit'] = 1;
						$arr['ElementPermission']['permit_delete'] = 1;
						$arr['ElementPermission']['permit_copy'] = 1;
						$arr['ElementPermission']['permit_move'] = 1;
						$arr['ElementPermission']['is_editable'] = 1;

						// if current user is group sharer than insert group id with element permission
						$grp_qry = "SELECT up.role, up.group_id FROM `user_permissions` up WHERE up.project_id = '$pr_id' AND up.user_id = '$user_id' GROUP BY up.user_id";
						$grp_data = $this->Element->query($grp_qry);
						if(isset($grp_data[0]['up']['role']) && !empty($grp_data[0]['up']['role']) && $grp_data[0]['up']['role'] == 'Group Sharer'){
							$arr['ElementPermission']['project_group_id'] = $grp_data[0]['up']['group_id'];
						}

						$this->ElementPermission->save($arr);
						$elementPermissionInsert_id = $this->ElementPermission->getLastInsertId();

						$response['success'] = true;
						$response['msg'] = "Success";
						$post = $this->request->data['Element'];
						$elements_details = $edata = null;

						$response['msg'] = "Success";

					} else {
						$response['msg'] = "Error!!!";
					}
				} else {
					if(!isset($this->request->data['Element']['area_id']) || empty($this->request->data['Element']['area_id'])){
						$this->Element->validationErrors['area_id'] = "This is required";
					}
					if(!isset($this->request->data['ElementType']['type_id']) || empty($this->request->data['ElementType']['type_id'])){
						$this->Element->validationErrors['ElementType']['type_id'] = "This is required";
					}
					if(!isset($this->request->data['Element']['date_constraints']) || empty($this->request->data['Element']['date_constraints'])){
						$this->Element->validationErrors['start_date'] = "";
						$this->Element->validationErrors['end_date'] = "";
					}
					$response['content'] = $this->validateErrors($this->Element);
				}
			}

			echo json_encode($response);
			exit();
		}
	}

	public function update_js_config($project_id = null, $workspace_id = null, $user_id = null) {

		$user_project = $this->objView->loadHelper('Common')->userproject($project_id, $user_id);
		$p_permission = $this->objView->loadHelper('Common')->project_permission_details($project_id, $user_id);

		$project_level = 0;

		// Get group id
		$grp_id = $this->objView->loadHelper('Group')->GroupIDbyUserID($project_id, $user_id);
		// Get Elements permissions
		$e_permission = $this->objView->loadHelper('Common')->element_permission_details($workspace_id, $project_id, $user_id);
		// Group permissions
		$group_permission = $this->objView->loadHelper('Group')->group_permission_details($project_id, $grp_id);
		// Project level according to the group permissions
		if (isset($group_permission['ProjectPermission']['project_level']) && $group_permission['ProjectPermission']['project_level'] == 1) {
			$project_level = $group_permission['ProjectPermission']['project_level'];
		}

		if ((isset($grp_id) && !empty($grp_id))) {

			if (isset($e_permission) && !empty($e_permission)) {
				$e_permissions = $this->objView->loadHelper('Group')->group_element_permission_details($workspace_id, $project_id, $grp_id);
				$e_permission = array_merge($e_permission, $e_permissions);
			} else {
				$e_permission = $this->objView->loadHelper('Group')->group_element_permission_details($workspace_id, $project_id, $grp_id);
			}
		}
		$areas = get_workspace_areas($workspace_id, false);
		$areaElements = null;
		if (isset($areas) && !empty($areas)) {
			// pr($areas, 1);
			foreach ($areas as $k => $v) {

				$elements_details_temp = null;
				if ((isset($e_permission) && !empty($e_permission))) {
					$all_elements = $this->objView->loadHelper('ViewModel')->area_elements_permissions($v['Area']['id'], false, $e_permission);
				}

				if (((isset($user_project) && !empty($user_project)) || (isset($project_level) && $project_level == 1) || (isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level'] == 1))) {
					$all_elements = $this->objView->loadHelper('ViewModel')->area_elements($v['Area']['id']);
				}

				if (isset($all_elements) && !empty($all_elements)) {

					foreach ($all_elements as $element_index => $e_data) {

						$element = $e_data['Element'];

						$element_decisions = $element_feedbacks = [];
						if (isset($element['studio_status']) && empty($element['studio_status'])) {
							$element_decisions = _element_decisions($element['id'], 'decision');
							$element_feedbacks = _element_decisions($element['id'], 'feedback');
							$element_statuses = _element_statuses($element['id']);

							$self_status['self_status'] = element_status($element['id']);

							$element_assets = element_assets($element['id'], true);
							$arraySearch = arraySearch($all_elements, 'id', $element['id']);

							if (isset($arraySearch) && !empty($arraySearch)) {
								$elements_details_temp[] = array_merge($arraySearch[0], $element_assets, $element_decisions, $element_feedbacks, $element_statuses, $self_status);
							}
						}
					}

					$areaElements[$v['Area']['id']]['el'] = $elements_details_temp;
				}
			}
		}
		return $areaElements;
	}

	/**
	 * Update background color code of an element in workspace->areas page
	 *
	 * @param
	 *        	$element_id
	 *
	 * @return void
	 */
	public function remove_element($id = null) {
		if ($this->request->isAjax()) {
			// die('sdfsfdf');
			$response = [
				'success' => false,
				'msg' => '',
				'content' => null,
			];

			$this->layout = 'ajax';
			$this->autoRender = false;
			$this->loadModel('ElementType');
			$this->loadModel('CurrentTask');

			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;

				//pr($post, 1);

				$id = (isset($post['Element']['id']) && !empty($post['Element']['id'])) ? $post['Element']['id'] : $id;

				$this->Element->id = $id;

				// Get Project Id with Element id; Update Project modified date
				// $this->update_project_modify($id);

				/* ============= Element Owners =============================== */
				$participants_owners = array();
				$participantsGpOwner = array();
				$elementDetail = $this->Element->findById($id);
				$elementName = '';
				if (isset($elementDetail['Element']['title']) && !empty($elementDetail['Element']['title'])) {
					$elementName = $elementDetail['Element']['title'];
				}

				$project_id = element_project($id);
				$all_owner = element_users(array($id), $project_id);




				/* ============================================================= */

				if (!$this->Element->exists()) {
					throw new NotFoundException(__('Invalid Element'), 'error');
				}
				$element = $eledeleted_data = $this->Element->findById($id);
				$this->CurrentTask->deleteAll(array('CurrentTask.task_id' => $id));

				//DELETE ALL THE ENTRIES FROM PLAN EFFORT TABLE
				$this->loadModel('PlanEffort');
				$pe_del = array('element_id'=>$id);
				$this->PlanEffort->deleteAll($pe_del);

				if ($this->Element->delete()) {

					$eleType = array('ElementType.project_id'=>$project_id, 'ElementType.element_id'=>$id);
					$this->ElementType->deleteAll($eleType);
					// Delete all reminders associated with the element...
					$this->loadModel('Reminder');
					$this->Reminder->deleteAll(['Reminder.element_id' => $id]);

					// its user for activity only
					$project_id = $this->Element->getProject($id);

					$area = $this->Area->findById($element['Element']['area_id']);
					$user_id = $this->Session->read("Auth.User.id");
					$workspace_id = $area['Area']['workspace_id'];

					$workspace = $this->ProjectWorkspace->find("first", array("conditions" => array("ProjectWorkspace.workspace_id" => $workspace_id)));
					$project_id = $workspace['ProjectWorkspace']['project_id'];

					// $this->Common->create_workspace_activity($project_id, $workspace_id);
					// end code here

					$response['success'] = true;
					$response['msg'] = 'Element has been deleted successfully.';
					$response['content'] = $this->update_element_detail($workspace_id);

					/*========== Strat Element Delete Email =========================== */

					$all_owner_tot = ( isset($all_owner) && !empty($all_owner) ) ? count($all_owner) : 0;

					if ( $all_owner_tot > 0 && !empty($elementName) && !empty($project_id)) {

						/************** socket messages **************/
						if (SOCKET_MESSAGES) {
							$current_user_id = $this->user_id;
							$ele_users = $all_owner;
							if (isset($ele_users) && !empty($ele_users)) {
								if (($key = array_search($current_user_id, $ele_users)) !== false) {
									unset($ele_users[$key]);
								}
							}
							$del_users = null;
							if (isset($ele_users) && !empty($ele_users)) {
								foreach ($ele_users as $key1 => $value1) {
									if (web_notify_setting($value1, 'element', 'element_deleted')) {
										$del_users[] = $value1;
									}
								}
							}
							$userDetail = get_user_data($current_user_id);
							$content = [
								'notification' => [
									'type' => 'task_deleted',
									'created_id' => $current_user_id,
									'project_id' => $project_id,
									'refer_id' => $workspace_id,
									'creator_name' => $userDetail['UserDetail']['full_name'],
									'subject' => 'Task deleted',
									'heading' => 'Task: ' . strip_tags($element['Element']['title']),
									'sub_heading' => 'Project: ' . strip_tags(getFieldDetail('Project', $project_id, 'title')),
									'date_time' => timezoneTimeConvertor($this->user_id, date('Y-m-d g:iA'), $format = 'd M Y g:iA') . user_country($this->user_id),
								],
								// 'received_users' => array_values($ele_users),
							];
							if (is_array($del_users)) {
								$content['received_users'] = array_values($del_users);
							}
							$response['content']['socket'] = $content;
						}
						/************** socket messages **************/
						$this->elementDeleteEmail($elementName, $project_id, $all_owner,$workspace_id);
					}

					/*========== Strat Element Delete Email =========================== */

				} else {
					$response['msg'] = 'Task could not deleted successfully.';
				}
				// $this->Element->_query(1);
			}

			echo json_encode($response);
			exit();
		}
	}

	function update_element($element_id = null, $mm_id = null) {

		$this->layout = 'inner';
		$this->Element->id = $element_id;


		//check permission
		if( check_ele_permission($element_id)  ){
			$this->redirect(array('controller' => 'projects', 'action' => 'lists'));
		}

		if (!$this->Element->exists()) {
			$this->redirect($this->referer());
		}

		$permit_data = null;
		if (isset($this->user_id) && !empty($this->user_id)) {
			$owner_id = $this->user_id;
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

			$pp_data = $this->ProjectGroup->find('all', [
				'joins' => [
					[
						'table' => 'project_group_users',
						'alias' => 'ProjectGroupUser',
						'type' => 'INNER',
						'conditions' => ['ProjectGroupUser.project_group_id = ProjectGroup.id'],
					],
				],
				'conditions' => [
					'ProjectGroup.group_owner_id' => $owner_id,
					'ProjectGroup.is_deleted' => 0,
					'ProjectGroupUser.approved' => 1,
				],
				'order' => ['ProjectGroup.created DESC'],
				'recursive' => 1,
			]);

			$pp_data_count = ( isset($pp_data) && !empty($pp_data) ) ? count($pp_data) : 0;

			if (!empty($pp_data_count)) {
				$permit_data['pp_data'] = $pp_data;
				$permit_data['pp_data_count'] = $pp_data_count;
			}
		}

		if (isset($project_id) && !empty($project_id)) {
			$this->setJsVar('project_id', $project_id);
		}

		$element_workspace = element_workspace($element_id);
		$project_id = element_project($element_id);

		$task_data = [
							'project_id' => $project_id,
							'workspace_id' => $element_workspace,
							'element_id' => $element_id,
							'element_type' => 'elements',
							'user_id' => $this->user_id,
							'updated_user_id' => $this->user_id,
							'message' => 'Task viewed',
							'updated' => date("Y-m-d H:i:s"),
						];
						$this->loadModel('Activity');
						$this->Activity->id = null;
						$this->Activity->save($task_data);




		$this->setJsVar('element_id', $element_id);
		$this->setJsVar('element_tasks', 'element_tasks');

		$this->set('permit_data', $permit_data);
		$this->set('pp_data_count', $pp_data_count);

		$this->set('title_for_layout', __('Update Task', true));
		$this->set('page_heading', 'Update Task');
		$this->set('page_subheading', 'View the Task and Assets');

		$user_id = $this->Auth->user('id');
		$this->set('user_id', $user_id);
		$this->set('session_id', $this->Session->id());

		// Get current project id for side-bar workspaces list
		// $_sidebarProjectId = _sidebarWsList($this->Element, $element_id);
		$this->set("_sidebarProjectId", $project_id);

		$this->Element->unbindAll();
		$this->Element->bindModel(array('belongsTo' => array('Area')));
		$result = $this->Element->find('first', [
			'conditions' => [
				'Element.id' => $element_id,
			]
		]);
		$arwsid = $result['Area']['workspace_id'];

		//unbind model used for remove extra model 24 Jan 2020======
		$this->ProjectWorkspace->unbindModel(array('hasMany' => ['WorkspacePermission']));
		$prj = $this->Workspace->ProjectWorkspace->find('all', [
			'conditions' => [
				'ProjectWorkspace.workspace_id' => $arwsid,
			],
			'fields'=>['Project.id,Project.start_date,Project.end_date,Workspace.sign_off,Workspace.start_date,Workspace.end_date']
		]);
		$this->set("prj", $prj);
		//===========================================================

		$dateprec = STATUS_NOT_SPACIFIED;

		if (isset($result['Element']['date_constraints']) && !empty($result['Element']['date_constraints']) && $result['Element']['date_constraints'] > 0) {

			if (((isset($result['Element']['start_date']) && !empty($result['Element']['start_date'])) && date('Y-m-d', strtotime($result['Element']['start_date'])) > date('Y-m-d')) && $result['Element']['sign_off'] != 1) {

				$dateprec = STATUS_NOT_STARTED;
			} else if (((isset($result['Element']['end_date']) && !empty($result['Element']['end_date'])) && date('Y-m-d', strtotime($result['Element']['end_date'])) < date('Y-m-d')) && $result['Element']['sign_off'] != 1) {

				$dateprec = STATUS_OVERDUE;
			} else if (isset($result['Element']['sign_off']) && !empty($result['Element']['sign_off']) && $result['Element']['sign_off'] > 0) {

				$dateprec = STATUS_COMPLETED;
			} else if ((((isset($result['Element']['end_date']) && !empty($result['Element']['end_date'])) && (isset($result['Element']['start_date']) && !empty($result['Element']['start_date']))) && (date('Y-m-d', strtotime($result['Element']['start_date'])) <= date('Y-m-d')) && date('Y-m-d', strtotime($result['Element']['end_date'])) >= date('Y-m-d')) && $result['Element']['sign_off'] != 1) {

				$dateprec = STATUS_PROGRESS;
			}
		}
		// echo $dateprec;
		// die;
		$this->set('date_status', $dateprec);

		// $project_id = $_sidebarProjectId;

		/* -----------Group code----------- at below lot-of model were including with UserProject */

		$query = "SELECT user_permissions.* FROM `user_permissions`
				INNER JOIN projects on
					projects.id = user_permissions.project_id
				WHERE
					user_permissions.project_id = $project_id AND user_permissions.user_id = $this->user_id and workspace_id is null order by role ASC ";


		$project_level = $this->UserPermission->query($query);
		if( isset($project_level) && !empty($project_level) ){
			$this->set('gpid', $project_level[0]['user_permissions']['group_id']);
			if (isset($project_level[0]['user_permissions']['permit_edit']) && $project_level[0]['user_permissions']['permit_edit'] == 1) {
				$this->set('project_level', 1);
			}
		}

		if ($this->request->is('post') || $this->request->is('put')) {

			if (!$this->request->is('ajax')) {

				// Check Feedback and Vote Min and Max date
				//$this->Common->feedbackVotesDate($element_id);

				$this->Element->set($this->request->data['Element']);

				$esd = isset($this->request->data['Element']['start_date']) && !empty($this->request->data['Element']['start_date']) ? date('Y-m-d', strtotime($this->request->data['Element']['start_date'])) : '';
				$eed = isset($this->request->data['Element']['end_date']) && !empty($this->request->data['Element']['end_date']) ? date('Y-m-d', strtotime($this->request->data['Element']['end_date'])) : '';


				//****** check Feedback and Vote date, compare with element date *****
				$feedbackVote = $this->Common->feedbackVotesDate($this->request->data['Element']['id']);

				$dateFlag = false;
				if ( ( (isset($esd) && !empty($esd)) && (isset($feedbackVote['minfeedstd']) && !empty($feedbackVote['minfeedstd'])) && ($esd > date('Y-m-d', strtotime($feedbackVote['minfeedstd'] )))) || ((isset($eed) && !empty($eed)) && (isset($feedbackVote['maxfeedend']) && !empty($feedbackVote['maxfeedend'])) && ($eed < date('Y-m-d', strtotime($feedbackVote['maxfeedend']))  )))    {

					$dateFlag = true;

				}

				if ( ( ( isset($esd) && !empty($esd)) && (isset($feedbackVote['minvotestd']) && !empty($feedbackVote['minvotestd'])) && ($esd > date('Y-m-d', strtotime($feedbackVote['minvotestd'] )))) || ((isset($eed) && !empty($eed)) && (isset($feedbackVote['maxvoteend']) && !empty($feedbackVote['maxvoteend']))  && ($eed < date('Y-m-d', strtotime($feedbackVote['maxvoteend'])))  )   ) {

					$dateFlag = true;
				}


				if( $dateFlag ){

					$this->Session->setFlash(__('You cannot make this change to the Task date schedule because it is outside the Feedback or Votes dates it contains.'), 'error');
					$this->Element->validationErrors['end_date'][0] = 'You cannot make this change to the Task date schedule because it is outside the Feedback or Votes dates it contains.';
					$this->redirect(array('controller' => 'entities', 'action' => 'update_element', $element_id));
				}

				/**********************************************************************/

				if ( (empty($prj[0]['Workspace']['end_date']) || empty($prj[0]['Workspace']['start_date'])) && (isset($this->request->data['Element']['date_constraints']) && $this->request->data['Element']['date_constraints'] > 0) ) {
					$this->Session->setFlash(__('Cannot set Task schedule before the Workspace schedule is set.'), 'error');
					$this->redirect(array('controller' => 'entities', 'action' => 'update_element', $element_id));
				}

				//if (isset($prj[0]['Workspace']) && !empty($esd) && $esd < date('Y-m-d h:i:s', strtotime($prj[0]['Workspace']['start_date']))) {
				if (isset($prj[0]['Workspace']) && !empty($esd) && $esd < date('Y-m-d', strtotime($prj[0]['Workspace']['start_date']))) {
					$this->Session->setFlash(__('Start date should be on or after the Workspace start date.'), 'error');
					$this->Element->validationErrors['start_date'][0] = 'Start date should be on or after the Workspace start date.';
					$this->redirect(array('controller' => 'entities', 'action' => 'update_element', $element_id));
				}
				//if (isset($prj[0]['Workspace']) && !empty($eed) && $eed > date('Y-m-d h:i:s', strtotime($prj[0]['Workspace']['end_date']))) {
				if (isset($prj[0]['Workspace']) && !empty($eed) && $eed > date('Y-m-d', strtotime($prj[0]['Workspace']['end_date']))) {
					$this->Session->setFlash(__('End date should be less than to workspace end date.'), 'error');
					$this->Element->validationErrors['end_date'][0] = 'End date should be less than to workspace end date.';
					$this->redirect(array('controller' => 'entities', 'action' => 'update_element', $element_id));
				}


				if ($this->Element->validates()) {

					if (isset($this->request->data['Element']['date_constraints']) && $this->request->data['Element']['date_constraints'] > 0) {
						$this->request->data['Element']['start_date'] = (isset($this->request->data['Element']['start_date']) && !empty($this->request->data['Element']['start_date'])) ? date('Y-m-d h:i:s', strtotime($this->request->data['Element']['start_date'])) : $this->request->data['Element']['start_date'];

						$this->request->data['Element']['end_date'] = (isset($this->request->data['Element']['end_date']) && !empty($this->request->data['Element']['end_date'])) ? date('Y-m-d h:i:s', strtotime($this->request->data['Element']['end_date'])) : $this->request->data['Element']['end_date'];
					} else {
						$this->request->data['Element']['start_date'] = $this->request->data['Element']['end_date'] = null;
					}
					//pr($this->request->data, 1);
					$this->request->data['Element']['updated_user_id'] = $this->Auth->user('id');

					$latestDate = $this->Element->findById($element_id);

					if ($this->Element->save($this->request->data)) {

						//Save Project Element Type
						if (!empty($this->request->data['ElementType']['type_id'])) {
							$this->ElementType->save($this->request->data);
						} /*  else {
							$this->ElementType->save($this->request->data);
						} */

						$this->Element->unbindModel(array('hasOne' => ['ElementDecision']));

						$this->Element->updateAll(array("Element.create_activity" => 0), array("Element.id" => $element_id));

						// Get Project Id with Element id; Update Project modified date
						// $this->update_project_modify($element_id);

						//$this->update_task_project_activity($element_id);
						// $this->update_task_up_activity($element_id);

						//============= Start Email Notification ==============================

						$startDate = strtotime($latestDate['Element']['start_date']);
						$endDate = strtotime($latestDate['Element']['end_date']);

						$willSend = false;
						if (($startDate < strtotime($this->request->data['Element']['start_date']) || $startDate > strtotime($this->request->data['Element']['start_date'])) && ($endDate < strtotime($this->request->data['Element']['end_date']) || $endDate > strtotime($this->request->data['Element']['end_date']))) {
							$willSend = true;

						} else if ($startDate < strtotime($this->request->data['Element']['start_date']) || $startDate > strtotime($this->request->data['Element']['start_date'])) {
							$willSend = true;

						} else if ($endDate < strtotime($this->request->data['Element']['end_date']) || $endDate > strtotime($this->request->data['Element']['end_date'])) {
							$willSend = true;

						} else {

						}
						if ($willSend == true) {

							$elementDetail = $this->Element->findById($element_id);
							$elementName = '';
							if (isset($elementDetail['Element']['title']) && !empty($elementDetail['Element']['title'])) {
								$elementName = $elementDetail['Element']['title'];
							}
							$project_id = element_project($element_id);
							$all_owner = element_users(array($element_id), $project_id);

							$this->elementScheduleChangeEmail($elementName, $all_owner, $element_id);

							/************** socket messages **************/
							if (SOCKET_MESSAGES) {
								$current_user_id = $this->user_id;
								$e_users = $all_owner;
								if (isset($e_users) && !empty($e_users)) {
									if (($key = array_search($current_user_id, $e_users)) !== false) {
										unset($e_users[$key]);
									}
								}
								$open_users = null;
								if (isset($e_users) && !empty($e_users)) {
									foreach ($e_users as $key1 => $value1) {
										if (web_notify_setting($value1, 'element', 'element_schedule_change')) {
											$open_users[] = $value1;
										}
									}
								}
								$userDetail = get_user_data($current_user_id);
								$content = [
									'notification' => [
										'type' => 'task_schedule_change',
										'created_id' => $current_user_id,
										'project_id' => $project_id,
										'refer_id' => $element_id,
										'creator_name' => $userDetail['UserDetail']['full_name'],
										'subject' => 'Task schedule change',
										'heading' => 'Task: ' . htmlentities(getFieldDetail('Element', $element_id, 'title'), ENT_QUOTES),
										'sub_heading' => 'Project: ' . htmlentities(getFieldDetail('Project', $project_id, 'title'), ENT_QUOTES),
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
						//============= End Email Notification ====================================

						/* $this->redirect([
							'controller' => 'projects',
							'action' => 'manage_elements',
							$project_id,
							$result['Area']['workspace_id'],
						]); */

						$this->redirect([
							'controller' => 'entities',
							'action' => 'update_element',
							$element_id
						]);

					}
				} else {
					//pr($this->Element->validationErrors ); die;
					//$v = $this->validateErrors ( $this->Element );
					$errors = $this->Element->validationErrors;
					$this->set('errors', $errors);
					//// pr($v ,1);
				}
			}
		} else {

			$this->request->data = $this->Element->read(null, $element_id);

			// $this->setJsVar('element_data', $this->request->data);
			// // pr($this->request->data, 1);
		}
		$join['joins'] = array(
			array(
				'table' => 'decisions',
				'alias' => 'Decision',
				'type' => 'LEFT',
				'conditions' => array(
					'Decision.id = ElementDecisionDetail.decision_id',
				),
			),
		);

		// Get all decisions
		$this->Element->unbindModel(array(
			'hasMany' => array(
				'Links',
				'Documents',
				'Notes',
				'Mindmaps',
			),
			'belongsTo' => [
				'Area',
			],
		));
		$element_decisions = $this->ElementDecision->find('first', [
			'conditions' => [
				'ElementDecision.element_id' => $element_id,
			],
			'recursive' => 2,
		]);
		$this->set(compact('element_decisions'));

		$decisions = $this->Decision->find('all', [
			'fields' => [
				'id',
				'title',
				'tip_text',
				'status',
				'created',
			],
			'conditions' => [
				'status' => 1,
			],
			'recursive' => -1,
		]);
		$this->set(compact('decisions'));
		$content = [];
		if (!empty($element_decisions)) {
			if (isset($element_decisions['Element']) && !empty($element_decisions['Element'])) {
				$content['e_data'] = $element_decisions['Element'];
			}
			if (isset($element_decisions['ElementDecision']) && !empty($element_decisions['ElementDecision'])) {
				$content['ed_data'] = $element_decisions['ElementDecision'];
			}
			if (isset($element_decisions['ElementDecisionDetail']) && !empty($element_decisions['ElementDecisionDetail'])) {
				$content['edd_data'] = $element_decisions['ElementDecisionDetail'];
			}
		}

		$this->setJsVar('element_decision', $content);

		// Get all feedbacks
		$this->Element->unbindModel(array(
			'hasMany' => array(
				'Links',
				'Documents',
				'Notes',
				'Mindmaps',
			),
			'belongsTo' => [
				'Area',
			],
		));
		$element_feedbacks = $this->ElementFeedback->find('first', [
			'conditions' => [
				'ElementFeedback.element_id' => $element_id,
			],
			'recursive' => 2,
		]);
		$this->set(compact('element_feedbacks'));

		$feedbacks = $this->Feedback->find('all', [
			'fields' => [
				'id',
				'title',
				'tip_text',
				'status',
				'created',
			],
			'conditions' => [
				'status' => 1,
			],
			'recursive' => -1,
		]);
		$this->set(compact('feedbacks'));
		$content = [];
		if (!empty($element_feedbacks)) {
			if (isset($element_feedbacks['Element']) && !empty($element_feedbacks['Element'])) {
				$content['e_data'] = $element_feedbacks['Element'];
			}
			if (isset($element_feedbacks['ElementFeedback']) && !empty($element_feedbacks['ElementFeedback'])) {
				$content['ef_data'] = $element_feedbacks['ElementFeedback'];
			}
			if (isset($element_feedbacks['ElementFeedbackDetail']) && !empty($element_feedbacks['ElementFeedbackDetail'])) {
				$content['efd_data'] = $element_feedbacks['ElementFeedbackDetail'];
			}
		}

		$this->setJsVar('element_feedbacks', $content);

		$this->loadModel('Project');
		$projectDetail = $this->Project->find('first', [
			'fields' => [
				'Project.id',
				'Project.created',
			],
			'conditions' => [
				'Project.id' => $project_id,
			],
		]);

		$signoff_comment = $this->SignoffTask->find('count', array('conditions'=>array('SignoffTask.element_id'=>$element_id) ));


		$this->loadModel('UserPermission');
		$current_user_id = $this->user_id;
		$ElementUserPermission = $this->UserPermission->query("select role from user_permissions where user_id = $current_user_id and element_id = $element_id"  );
		$element_role = $ElementUserPermission[0]['user_permissions']['role'];

		$this->set('projectDetail', $projectDetail);
		$this->set('signoff_comment', $signoff_comment);

		$this->set('project_id', $project_id);
		$this->set('workspace_id', $result['Area']['workspace_id']);
		$this->set('element_id', $element_id);

		$this->setJsVar('currentProjectId', $project_id);
		$this->setJsVar('currentWorkspaceId', $result['Area']['workspace_id']);
		$this->setJsVar('currentElementId', $element_id);
		$this->setJsVar('currentUserRole', $element_role);

		// Get all decisions of the current element and assign to global JS variable
		$element_decision_data = $this->ElementDecision->find('all', [
			'conditions' => [
				'ElementDecision.element_id' => $element_id,
			],
			'recursive' => 1,
		]);
		$this->set('element_decision_data', $element_decision_data);

		$project_workspace = null;
		$this->loadModel('ProjectWorkspace');
		if (isset($project_id) && !empty($project_id)) {
			$project_workspace = $this->ProjectWorkspace->find('first', [
				'conditions' => [
					'ProjectWorkspace.project_id' => $project_id,
					'ProjectWorkspace.workspace_id' => $result['Area']['workspace_id'],
				],
				'recursive' => 1,
			]);
		}

		$ws_title = ($project_workspace['Workspace']['title']);
		
		//$ws_title = htmlentities($project_workspace['Workspace']['title'], ENT_QUOTES, "UTF-8");
		
		//$project_title = htmlentities($project_workspace['Project']['title'], ENT_QUOTES, "UTF-8");
		
		$project_title = ($project_workspace['Project']['title']);

		// Get title of element
		$crumb_element = null;
		if (isset($element_id) && !empty($element_id)) {
			$crumb_ele = $this->Element->find('first', [
				'conditions' => [
					'Element.id' => $element_id,
				],
				'recursive' => -1,
			]);
			if (!empty($crumb_ele)) {
				$crumb_element = $crumb_ele['Element'];
			}
			//find Project Type
			$elementType = $this->ElementType->find('first', array('conditions' => array('ElementType.project_id' => $project_id, 'ElementType.element_id' => $element_id)));

			if (!empty($elementType)) {
				$this->set('selElementType', $elementType['ElementType']['type_id']);
				$this->set('updateElementType', $elementType['ElementType']['id']);
			} else {
				$elementtype = $this->ProjectElementType->find('first', array('conditions' => array('ProjectElementType.project_id' => $project_id, 'ProjectElementType.title' => 'General')));
				// pr($elementtype);
				$peType = (isset($elementtype['ProjectElementType']['id']) && !empty($elementtype['ProjectElementType']['id'])) ? $elementtype['ProjectElementType']['id'] : null;
				$this->set('selElementType', $peType);
				$this->set('updateElementType', null);

			}

		}

		// Get category detail of parent Project
		// if category detail found, merge it with other breadcrumb data
		// $cat_crumb = get_category_list($project_id);

		$crumb = [
			// 'Project' => '/projects/lists/',
			'Summary' => [
				'data' => [
					'url' => '/projects/index/' . $project_id,
					'class' => 'tipText',
					'title' =>   $project_title ,
					'data-original-title' =>  $project_title,
				],
			],
			'Workspace' => [
				'data' => [
					'url' => '/projects/manage_elements/' . $project_id . '/' . $result['Area']['workspace_id'] ,
					'class' => 'tipText',
					'title' =>   $ws_title ,
					'data-original-title' =>  $ws_title ,
				],
			],
			'Area' => [
				'data' => [
					'url' => '/projects/manage_elements/' . $project_id . '/' . $result['Area']['workspace_id'] ,
					'class' => 'tipText',
					'title' => ( $result['Area']['title'] ),
					'data-original-title' =>( $result['Area']['title']),
				],
			],
			'last' => [
				'data' => [
					// 'class' => 'tipText',
					'title' =>  htmlentities($crumb_element['title'], ENT_QUOTES, "UTF-8")  ,
					'data-original-title' => htmlentities($crumb_element['title'], ENT_QUOTES, "UTF-8")  ,
				],
			],
		];

		/*if (isset($cat_crumb) && !empty($cat_crumb) && is_array($cat_crumb)) {
			$crumb = array_merge($cat_crumb, $crumb);
		}*/

		$this->set('crumb', $crumb);

		$this->pagination['limit'] = 3;
		$linkPageCount = $this->ElementLink->find('count', [
			'conditions' => array(
				'ElementLink.status' => 1,
				'ElementLink.element_id' => $element_id,
			),
		]);
		$paginator['ElementLink'] = array(
			'limit' => 3,
			'conditions' => array(
				'ElementLink.status' => 1,
				'ElementLink.element_id' => $element_id,
			),
		);
		$this->pagination['count'] = $linkPageCount;
		$this->paginate = $paginator;
		$this->set('linkPage', $this->paginate('ElementLink'));
		$this->set('linkPageCount', $linkPageCount);
		// $this->set('JeeraPaging', ['ElementLink' => $this->pagination]);

		$paginator = null;
		$this->pagination['limit'] = 1;
		$notePageCount = $this->ElementNote->find('count', [
			'conditions' => array(
				'ElementNote.status' => 1,
				'ElementNote.element_id' => $element_id,
			),
		]);
		$paginator['ElementNote'] = array(
			'limit' => 1,
			'conditions' => array(
				'ElementNote.status' => 1,
				'ElementNote.element_id' => $element_id,
			),
		);
		$this->pagination['count'] = 1;
		$this->paginate = $paginator;
		$this->set('notePage', $this->paginate('ElementNote'));
		$this->set('notePageCount', $notePageCount);

		/* paging for mindmap */
		$paginator = null;
		$this->pagination['limit'] = 5;
		$mindmapPageCount = $this->ElementMindmap->find('count', [
			'conditions' => array(
				'ElementMindmap.element_id' => $element_id,
			),
		]);
		$mindmapData = $this->ElementMindmap->find('all', [
			'conditions' => array(
				'ElementMindmap.element_id' => $element_id,
			),
		]);

		$paginator['ElementMindmap'] = array(
			'limit' => 5,
			'conditions' => array(
				'ElementMindmap.element_id' => $element_id,
			),
		);
		$this->pagination['count'] = $mindmapPageCount;
		$this->paginate = $paginator;
		$this->set('mMPage', $mindmapData);
		$this->set('mindmapPageCount', $mindmapPageCount);
		/* End paging for mindmap */
		$this->pagination['show_summary'] = true;
		// $this->set('JeeraPaging', $this->pagination );
		// End Pagination

		$this->set('_token', $this->_token);
		$this->loadModel('Vote');
		$this->loadModel('VoteUser');
		$this->Vote->recursive = 3;
		$this->Vote->bindModel(array('hasMany' => array('VoteResult')));
		$this->VoteUser->bindModel(array('belongsTo' => array('User')));
		// $votedata = $this->Vote->find('all',array('conditions'=>array('Vote.element_id'=>$element_id, 'VoteQuestion.id !='=>''), 'order'=>array('Vote.id'=>'desc')));
		// // pr($votedata);die;
		$this->set('votes', $this->Vote->find('all', array('conditions' => array('Vote.element_id' => $element_id, 'VoteQuestion.id !=' => ''), 'order' => array('Vote.id' => 'desc'))));

		$this->loadModel('VoteType');
		$this->set('voteTypes', $this->VoteType->find('list', array('conditions' => array('VoteType.status' => '1'), 'fields' => array('VoteType.id', 'VoteType.title'))));

		$this->loadModel('Feedback');
		$this->loadModel('FeedbackUser');
		$this->Feedback->recursive = 3;
		//$this->Feedback->bindModel(array('hasMany'=>array('FeedbackResult')));
		$this->Feedback->bindModel(array('hasMany' => array('FeedbackUser')));
		$this->FeedbackUser->bindModel(array('belongsTo' => array('User')));
		// $votedata = $this->Feedback->find('all',array('conditions'=>array('Feedback.element_id'=>$element_id), 'order'=>array('Feedback.id'=>'desc')));
		//// pr($votedata);die;
		$this->set('feedbacks', $this->Feedback->find('all', array('conditions' => array('Feedback.element_id' => $element_id), 'order' => array('Feedback.id' => 'desc'))));

		$eldata = getByDbId('Element', $element_id);
		$this->set('page_heading', __($eldata['Element']['title'], true));
		$this->set('eldata', $eldata);

		$project_type = CheckProjectType($project_id, $this->user_id);
		$this->set('project_type', $project_type);


		// $team_data = $this->objView->loadHelper('Permission')->team_task_efforts_listing(null, $element_id, 0, $this->team_offset, null);
		// $this->set('team_data', $team_data);

		$task_detail = $this->Element->query("SELECT id, title, date_constraints, sign_off, sign_off_date, start_date, end_date FROM elements WHERE id = $element_id");
		$task_detail = $task_detail[0]['elements'];
		$this->set('task_detail', $task_detail);

	}

	public function docElmAdd($element_id = null) {
		if ($this->request->isAjax()) {

			$response = [
				'success' => false,
				'msg' => '',
				'content' => null,
			];

			$this->layout = 'ajax';
			$this->autoRender = false;

			if ($this->request->is('post') || $this->request->is('put')) {

				$count = $this->ElementLink->find('count', array(
					'conditions' => array(
						'ElementLink.id' => $this->request->data['id'],
						'ElementLink.element_id' => $this->request->data['element_id'],
					),
				));

				if (isset($count) && $count == 0) {

					$this->request->data['Links']['element_id'] = $this->request->data['element_id'];
					$this->request->data['Links']['title'] = $this->request->data['link_tit'];
					$this->request->data['Links']['references'] = $this->request->data['link_href'];

					if ($this->ElementLink->save($this->request->data['Links'])) {

						$id = $this->ElementLink->getLastInsertId();

						// Get Project Id with Element id; Update Project modified date
						// $this->update_project_modify($this->request->data['element_id']);

						$response['success'] = true;
						$response['msg'] = "Success";
						$response['content'] = $id;
						// // pr($response, 1);
					} else {
						$response['msg'] = "Error";
					}
				} else {
					$response['content'] = $this->validateErrors($this->ElementLink);
				}
			}

			echo json_encode($response);
			exit();
		}
	}

	public function docmmAdd($element_id = null) {
		if ($this->request->isAjax()) {

			$response = [
				'success' => false,
				'msg' => '',
				'content' => null,
			];

			$this->layout = 'ajax';
			$this->autoRender = false;

			if ($this->request->is('post') || $this->request->is('put')) {

				$count = $this->ElementMindmap->find('count', array(
					'conditions' => array(
						'ElementMindmap.id' => $this->request->data['id'],
						'ElementMindmap.element_id' => $this->request->data['element_id'],
					),
				));

				if (isset($count) && $count == 0) {

					$data = $this->ElementMindmap->find('first', array(
						'conditions' => array(
							'ElementMindmap.id' => $this->request->data['id'],
						),
					));
					$data['ElementMindmap']['element_id'] = $this->request->data['element_id'];
					unset($data['ElementMindmap']['id']);

					if ($this->ElementMindmap->save($data)) {

						$id = $this->ElementMindmap->getLastInsertId();

						// Get Project Id with Element id; Update Project modified date
						// $this->update_project_modify($this->request->data['element_id']);

						$response['success'] = true;
						$response['msg'] = "Success";
						$response['content'] = $id;
						// // pr($response, 1);
					} else {
						$response['msg'] = "Error";
					}
				} else {
					$response['content'] = $this->validateErrors($this->ElementMindmap);
				}
			}

			echo json_encode($response);
			exit();
		}
	}

	public function docFileAdd($element_id = null) {
		if ($this->request->isAjax()) {

			$response = [
				'success' => false,
				'msg' => '',
				'content' => null,
			];

			$this->layout = 'ajax';
			$this->autoRender = false;

			if ($this->request->is('post') || $this->request->is('put')) {

				$count = $this->ElementDocument->find('count', array(
					'conditions' => array(
						'ElementDocument.id' => $this->request->data['id'],
						'ElementDocument.element_id' => $this->request->data['element_id'],
					),
				));

				if (isset($count) && $count == 0) {

					$this->loadModel('ElementDocument');

					$old_elm_id = $this->request->data['old_elm_id'];

					$data = $this->ElementDocument->find('first', array(
						'conditions' => array(
							'ElementDocument.id' => $this->request->data['id'],
						),
					));

					// $data['ElementDocument']['file_name'] = $data['ElementDocument']['file_name'].'_'.rand();

					$data['ElementDocument']['element_id'] = $this->request->data['element_id'];
					unset($data['ElementDocument']['id']);

					$fold = $data['ElementDocument']['file_name'];

					$dimg = explode('.', $data['ElementDocument']['file_name']);

					$fname = $dimg['0'] . '_' . rand() . '.' . $dimg['1'];

					$data['ElementDocument']['file_name'] = $fname;

					if ($this->ElementDocument->save($data)) {

						// Get Project Id with Element id; Update Project modified date
						// $this->update_project_modify($this->request->data['element_id']);

						$file = DOC_ROOT . '/uploads/element_documents/' . $old_elm_id . '/' . $fold;

						$newfile = DOC_ROOT . '/uploads/element_documents/' . $this->request->data['element_id'] . '/' . $fname;

						$newfilehref = SITEURL . 'entities/update_element/' . $this->request->data['element_id'] . '/#documents';

						copy($file, $newfile);

						$id = $this->ElementDocument->getLastInsertId();

						$response['success'] = true;
						$response['msg'] = "Success";
						$response['content'] = $id;
						$response['rel'] = trim($this->request->data['element_id']);
						$response['href'] = $newfilehref;
						// // pr($response, 1);
					} else {
						$response['msg'] = "Error";
					}
				} else {
					$response['content'] = $this->validateErrors($this->ElementLink);
				}
			}

			echo json_encode($response);
			exit();
		}
	}

	/**
	 * Add a link to the element
	 *
	 * @param
	 *        	$element_id
	 *
	 * @return void
	 */
	public function play_media($id = null) {

		$this->layout = 'ajax';
		// $this->autoRender = false;
		$response = [
			'success' => false,
			'msg' => '',
			'content' => [],
		];

		// if ($this->request->isAjax ()) {
		// $this->autoRender = false;
		// if ($this->request->is ( 'post' ) || $this->request->is ( 'put' )) {
		$data = $this->ElementLink->find('first', ['conditions' => ['ElementLink.id' => $id, 'ElementLink.link_type' => 2],
			'fields' => ['ElementLink.embed_code'],
		]);
		// // pr($data, 1);
		$response['success'] = true;
		$response['msg'] = '';
		$response['content'] = $data['ElementLink'];
		// }
		$this->set('data', $data);
		// echo json_encode($response);
		// exit;
		$this->render('/Entities/partials/play_media');
		// }
	}

	public function update_task_up_activity($element_id = null) {
		//$project_id = $this->Element->getProject($element_id);
		$element = $this->Element->findById($element_id);
		$area = $this->Area->findById($element['Element']['area_id']);
		$user_id = $this->Session->read("Auth.User.id");
		$workspace_id = $area['Area']['workspace_id'];

		$workspace = $this->ProjectWorkspace->find("first", array("conditions" => array("ProjectWorkspace.workspace_id" => $workspace_id),'recursive'=>-1));
		$project_id = $workspace['ProjectWorkspace']['project_id'];

		$date = date("Y-m-d H:i:s");

		/*
			$this->loadModel("Activity");
			$data = [
				'project_id' => $project_id,
				'workspace_id' => $workspace_id,
				'element_id' => $element_id,
				'element_type' => 'element_tasks',
				'relation_id' => $element_id,
				'user_id' => $user_id,
				'updated_user_id' => $user_id,
				'user_status' => '0',
				'message' => 'Task updated',
				'updated' => $date,
			];

			$this->Activity->save($data);
		*/

		/*$work_data = [
			'project_id' => $project_id,
			'workspace_id' => $workspace_id,
			'updated_user_id' => $user_id,
			'message' => 'Workspace updated',
			'updated' => $date,
		];
		$this->loadModel("WorkspaceActivity");
		$this->WorkspaceActivity->save($work_data);
		$project_data = [
			'project_id' => $project_id,
			'updated_user_id' => $user_id,
			'message' => 'Project updated',
			'updated' => $date,
		];

		$this->loadModel("ProjectActivity");
		$this->ProjectActivity->save($project_data);*/

	}

	public function update_task_activity($element_id = null) {
		$project_id = $this->Element->getProject($element_id);

		$element = $this->Element->findById($element_id);
		$area = $this->Area->findById($element['Element']['area_id']);
		$user_id = $this->Session->read("Auth.User.id");
		$workspace_id = $area['Area']['workspace_id'];

		$workspace = $this->ProjectWorkspace->find("first", array("conditions" => array("ProjectWorkspace.workspace_id" => $workspace_id)));
		$project_id = $workspace['ProjectWorkspace']['project_id'];

		$date = date("Y-m-d H:i:s");

		$this->loadModel("Activity");
		$data = [
			'project_id' => $project_id,
			'workspace_id' => $workspace_id,
			'element_id' => $element_id,
			'element_type' => 'element_tasks',
			'relation_id' => $element_id,
			'user_id' => $user_id,
			'updated_user_id' => $user_id,
			'user_status' => '0',
			'message' => 'Task updated',
			'updated' => $date,
		];

		$this->Activity->save($data);

		$work_data = [
			'project_id' => $project_id,
			'workspace_id' => $workspace_id,
			'updated_user_id' => $user_id,
			'message' => 'Workspace updated',
			'updated' => $date,
		];
		$this->loadModel("WorkspaceActivity");
		$this->WorkspaceActivity->save($work_data);
		$project_data = [
			'project_id' => $project_id,
			'updated_user_id' => $user_id,
			'message' => 'Project updated',
			'updated' => $date,
		];

		$this->loadModel("ProjectActivity");
		$this->ProjectActivity->save($project_data);

	}
	public function update_task_project_activity($element_id = null) {
		$project_id = $this->Element->getProject($element_id);
		$element = $this->Element->findById($element_id);
		$area = $this->Area->findById($element['Element']['area_id']);
		$user_id = $this->Session->read("Auth.User.id");
		$workspace_id = $area['Area']['workspace_id'];
		$date = date("Y-m-d h:i:s");

		$project_data = [
			'project_id' => $project_id,
			'updated_user_id' => $user_id,
			'message' => 'Project updated',
			'updated' => $date,
		];

		$this->loadModel("ProjectActivity");
		$this->ProjectActivity->save($project_data);

	}

	public function update_task_workspace_activity($element_id = null) {
		$project_id = $this->Element->getProject($element_id);
		$element = $this->Element->findById($element_id);
		$area = $this->Area->findById($element['Element']['area_id']);
		$user_id = $this->Session->read("Auth.User.id");
		$workspace_id = $area['Area']['workspace_id'];
		$date = date("Y-m-d h:i:s");

		$project_data = [
			'project_id' => $project_id,
			'updated_user_id' => $user_id,
			'message' => 'Project updated',
			'updated' => $date,
		];

		$this->loadModel("ProjectActivity");
		$this->ProjectActivity->save($project_data);

	}

	/**
	 * Add a link to the element
	 *
	 * @param
	 *        	$element_id
	 *
	 * @return void
	 */
	public function add_links($element_id = null) {

		$view = new View();
		$wiki = $view->loadHelper('Wiki');

		if ($this->request->isAjax()) {

			$response = [
				'success' => false,
				'msg' => '',
				'content' => null,
			];

			$this->layout = 'ajax';
			$this->autoRender = false;

			// // pr($this->request->data); die;
			if ($this->request->is('post') || $this->request->is('put')) {

				$this->ElementLink->set($this->request->data['Links']);

				$linkType = 0;

				if (isset($this->request->data['Links']['link_type']) && !empty($this->request->data['Links']['link_type'])) {
					$linkType = $this->request->data['Links']['link_type'];
				}

				$this->ElementLink->setValidationRules($linkType);

				if (isset($this->request->data['Links']['references']) && $this->request->data['Links']['references'] == 1) {
					$linkData = $this->request->data['Links']['references'];

					if (!empty($linkData)) {
						$chkLinks = explode("//", $linkData);

						if (isset($chkLinks['1']) && !empty($chkLinks['1'])) {
							$this->request->data['Links']['references'] = $this->request->data['Links']['references'];
						} else {
							$this->request->data['Links']['references'] = "http://" . $this->request->data['Links']['references'];
						}
					}
				}

				// $validator = $this->ElementLink->validate;
				// // pr($validator , 1);

				if ($this->ElementLink->validates()) {

					if (empty($this->request->data['Links']['id'])) {
						$this->request->data['Links']['creater_id'] = $this->Auth->user('id');
					}

					$this->request->data['Links']['updated_user_id'] = $this->Auth->user('id');

					if ($this->ElementLink->save($this->request->data['Links'])) {

						if (isset($this->request->data['Links']['id']) && !empty($this->request->data['Links']['id'])) {
							$linkid = $this->request->data['Links']['id'];
						} else {
							$linkid = $this->ElementLink->getLastInsertId();
						}
						$this->ElementLink->updateAll(
							array("ElementLink.create_activity" => 0),
							array("ElementLink.id" => $linkid)
						);
						// Update elemnt task activity.
						// $this->update_task_activity($element_id);

						// Get Project Id with Element id; Update Project modified date
						// $this->update_project_modify($element_id);

						$this->request->data['Element']['id'] = $element_id;
						$this->request->data['Element']['updated_user_id'] = $this->Auth->user('id');
						#$this->Element->save($this->request->data['Element']);

						$id = 0;
						$allow = 0;

						if (isset($this->request->data['Links']['id']) && empty($this->request->data['Links']['id'])) {
							$id = $this->ElementLink->getLastInsertId();
						} else {
							$id = $this->request->data['Links']['id'];
							$allow = 1;
						}
						$element_link = null;
						if (isset($id) && !empty($id)) {
							$element_link = $this->ElementLink->find('first', [
								'conditions' => [
									'ElementLink.id' => $id,
								],
							]);
						}

						if (!is_null($element_link)) {

							$element_link['ElementLink']['title'] = html_entity_decode($element_link['ElementLink']['title']);
							//$element_link ['ElementLink'] ['created'] = date('d M, Y g:iA', strtotime($element_link ['ElementLink'] ['created']));
							$element_link['ElementLink']['created'] = $this->objView->loadHelper('Wiki')->_displayDate(date('Y-m-d h:i:s A', strtotime($element_link['ElementLink']['created'])), $format = 'd M, Y g:iA');

							//$element_link ['ElementLink'] ['modified'] = date('d M, Y g:iA', strtotime($element_link ['ElementLink'] ['modified']));
							$element_link['ElementLink']['modified'] = $this->objView->loadHelper('Wiki')->_displayDate(date('Y-m-d h:i:s A', strtotime($element_link['ElementLink']['modified'])), $format = 'd M, Y g:iA');
						}

						//// pr($element_link ['ElementLink'],1);
						$creator_id = $element_link['ElementLink']['creater_id'];

						$element_link['ElementLink']['creator'] = $this->Common->elementLink_creator($id, $this->request->data['Links']['project_id'], $creator_id);

						$response['success'] = true;
						$response['msg'] = "Success";
						$response['allow'] = $allow;
						$response['content'] = $element_link;
						// // pr($response, 1);
					} else {
						$response['msg'] = "Error!!!";
					}
				} else {
					$response['content'] = $this->validateErrors($this->ElementLink);
				}
			}

			echo json_encode($response);
			exit();
		}
	}

	/**
	 * Update background color code of an element in workspace->areas page
	 *
	 * @param
	 *        	$element_id
	 *
	 * @return void
	 */
	public function remove_link($element_id = null) {
		if ($this->request->is('ajax')) {

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

				if (isset($post['link_id']) && !empty($post['link_id'])) {
					$id = $post['link_id'];
					$this->ElementLink->id = $id;
					if (!$this->ElementLink->exists()) {
						throw new NotFoundException(__('Invalid detail'), 'error');
					}

					$dids = $this->ElementLink->findById($id);

					if ($this->ElementLink->delete()) {
						$this->request->data['Element']['id'] = $dids['ElementLink']['element_id'];
						$this->request->data['Element']['updated_user_id'] = $this->Auth->user('id');

						$this->Element->id = $dids['ElementLink']['element_id'];

						$defaultElementid = $dids['ElementLink']['element_id'];

						$task_data = [
						'project_id' => element_project($defaultElementid),
						'workspace_id' => element_workspace($defaultElementid),
						'element_id' => $defaultElementid,
						'element_type' => 'element_links',
						'user_id' => $this->user_id,
						'relation_id' => $dids['ElementLink']['id'],
						'updated_user_id' => $this->user_id,
						'message' => 'Link deleted',
						'updated' => date("Y-m-d H:i:s"),
						];
						$this->loadModel('Activity');
						$this->Activity->id = null;
						$this->Activity->save($task_data);

						#$this->Element->save($this->request->data['Element']);

						// Get Project Id with Element id; Update Project modified date
						// $this->update_project_modify($dids['ElementLink']['element_id']);
						#$this->update_task_activity($dids['ElementLink']['element_id']);
						$response['success'] = true;
						$response['msg'] = 'Element link has been deleted successfully.';
						$response['content'] = [];
					} else {
						$response['msg'] = 'Element could not deleted successfully.';
					}

					// $this->Element->_query(1);
				}
			}

			echo json_encode($response);
			exit();
		}
	}

	/**
	 * Add a notes to the element
	 *
	 * @param
	 *        	$element_id
	 *
	 * @return void
	 */
	public function add_note($element_id = null) {
		if ($this->request->isAjax()) {

			$response = [
				'success' => false,
				'action' => null,
				'content' => null,
			];

			$this->layout = 'ajax';
			$this->autoRender = false;

			if ($this->request->is('post') || $this->request->is('put')) {
				// // pr($this->request->data, 1);
				$id = null;
				$action = '';
				if (empty($this->request->data['Notes']['id'])) {
					$this->request->data['Notes']['creater_id'] = $this->Auth->user('id');
				}

				$this->request->data['Notes']['updated_user_id'] = $this->Auth->user('id');

				$this->ElementNote->set($this->request->data['Notes']);

				if ($this->ElementNote->validates()) {
					$this->request->data['Notes']['create_activity'] = 1;
					if ($this->ElementNote->save($this->request->data['Notes'])) {

						if (isset($this->request->data['Notes']['id']) && !empty($this->request->data['Notes']['id'])) {
							$id = $this->request->data['Notes']['id'];
							$action = 'update';
						} else {
							$id = $this->ElementNote->getLastInsertId();
							$action = 'create';
						}
						$this->ElementNote->updateAll(
							array("ElementNote.create_activity" => 0),
							array("ElementNote.id" => $id)
						);

						$element_id = $this->request->data['Notes']['element_id'];

						$this->request->data['Element']['id'] = $element_id;
						$this->request->data['Element']['updated_user_id'] = $this->Auth->user('id');

						// $this->Element->save($this->request->data['Element']);

						// Get Project Id with Element id; Update Project modified date
						// $this->update_project_modify($element_id);
						// Update elemnt task activity.
						// $this->update_task_activity($element_id);

						$element_note = null;
						if (isset($id) && !empty($id)) {
							$element_note = $this->ElementNote->find('first', [
								'conditions' => [
									'ElementNote.id' => $id,
								],
							]);

							if (!is_null($element_note)) {
								// $element_note['ElementNote']['created'] = dateFormat($element_note['ElementNote']['created']);
								$element_note['ElementNote']['created'] = $this->objView->loadHelper('Wiki')->_displayDate(date('Y-m-d h:i:s A', strtotime($element_note['ElementNote']['created'])), $format = 'd M, Y g:iA');

								// $element_note['ElementNote']['modified'] = dateFormat($element_note['ElementNote']['modified']);

								$element_note['ElementNote']['modified'] = $this->objView->loadHelper('Wiki')->_displayDate(date('Y-m-d h:i:s A', strtotime($element_note['ElementNote']['modified'])), $format = 'd M, Y g:iA');

								$element_note['ElementNote']['title'] =  htmlentities($element_note['ElementNote']['title'], ENT_QUOTES, "UTF-8") ; 
							}
						}

						$creator_id = $element_note['ElementNote']['creater_id'];

						$element_note['ElementNote']['creator'] = $this->Common->elementNote_creator($id, $this->request->data['Notes']['project_id'], $creator_id);

						//// pr($element_note,1);

						$response['success'] = true;
						$response['action'] = $action;
						$response['content'] = $element_note;
					}
				} else {
					$response['content'] = $this->validateErrors($this->ElementNote);
				}
			}

			echo json_encode($response);
			exit();
		}
	}

	/**
	 * Update background color code of an element in workspace->areas page
	 *
	 * @param
	 *        	$element_id
	 *
	 * @return void
	 */
	public function remove_note($element_id = null) {
		if ($this->request->is('ajax')) {

			$response = [
				'success' => false,
				'msg' => '',
				'content' => null,
			];

			$this->layout = 'ajax';
			$this->autoRender = false;

			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;
				// // pr($this->request->data, 1 );

				if (isset($post['note_id']) && !empty($post['note_id'])) {
					$id = $post['note_id'];

					$this->ElementNote->id = $id;

					if (!$this->ElementNote->exists()) {
						throw new NotFoundException(__('Invalid detail'), 'error');
					}
					$dids = $this->ElementNote->findById($id);

					if ($this->ElementNote->delete()) {
						$this->request->data['Element']['id'] = $dids['ElementNote']['element_id'];
						$this->request->data['Element']['updated_user_id'] = $this->Auth->user('id');

						$this->Element->id = $dids['ElementNote']['element_id'];

						$defaultElementid = $dids['ElementNote']['element_id'];

						$task_data = [
						'project_id' => element_project($defaultElementid),
						'workspace_id' => element_workspace($defaultElementid),
						'element_id' => $defaultElementid,
						'element_type' => 'element_notes',
						'user_id' => $this->user_id,
						'relation_id' => $dids['ElementNote']['id'],
						'updated_user_id' => $this->user_id,
						'message' => 'Note deleted',
						'updated' => date("Y-m-d H:i:s"),
						];
						$this->loadModel('Activity');
						$this->Activity->id = null;
						$this->Activity->save($task_data);

						// $this->Element->save($this->request->data['Element']);

						// Get Project Id with Element id; Update Project modified date
						// $this->update_project_modify($dids['ElementNote']['element_id']);
						// $this->update_task_activity($dids['ElementNote']['element_id']);
						$response['success'] = true;
						$response['msg'] = 'Element note has been deleted successfully.';
						$response['content'] = [];
					} else {
						$response['msg'] = 'Element could not deleted successfully.';
					}
					// $this->Element->_query(1);
				}
			}

			echo json_encode($response);
			exit();
		}
	}

	/**
	 * Add a link to the element
	 *
	 * @param
	 *        	$element_id
	 *
	 * @return void
	 */
	public function upload_editor_image($element_id = null, $type = 'notes') {
		if ($this->request->isAjax()) {

			$response = [
				'success' => false,
				'msg' => '',
				'content' => null,
			];

			$this->layout = 'ajax';
			$this->autoRender = false;

			if ($this->request->is('post') || $this->request->is('put')) {
				// // pr($this->params['pass'], 1);
				$sizeLimit = 5 * 1024 * 1024;
				$folder_url = WWW_ROOT . ELEMENT_NOTES_TEMP_PATH;
				$return_path = SITEURL . ELEMENT_NOTES_TEMP_PATH;
				$result = $fileNewName = $upload_object = $upload_detail = null;

				if (isset($this->params['pass']) && !empty($this->params['pass'])) {
					$param = $this->params['pass'];
					if (isset($param[1]) && $param[1] == 'feedback') {
						$folder_url = WWW_ROOT . ELEMENT_FEEDBACK_IMAGE_PATH;
						$return_path = SITEURL . ELEMENT_FEEDBACK_IMAGE_PATH;
					}
				}

				if (!empty($_FILES)) {

					$upload_object = $_FILES["image1"];

					$folder_url .= DS . $element_id;
					// echo $folder_url;
					// // pr($upload_object , 1);
					if (!file_exists($folder_url)) {
						mkdir($folder_url, 0777, true);
					}

					$nameArr = explode(".", $upload_object['name']);
					$fileName = (!empty($nameArr) && isset($nameArr[0])) ? $nameArr[0] : '';
					$fileExt = (!empty($nameArr) && isset($nameArr[1])) ? $nameArr[1] : '';
					// $fileNewName = $upload_object['name'][$i];// md5(microtime())
					$first = null;
					$first = explode(' ', microtime());
					$sec = explode('.', $first[0]);
					$fileNewName = rand(0, $sec[1]) . '-' . rand(0, $first[1]) . '.' . $fileExt;

					if (!is_writable($folder_url)) {
						$result = array(
							'error' => "Server error. Upload directory isn't writable. Please ask server admin to change permissions.",
						);
					}
					if (!empty($fileNewName)) {

						$tempFile = $upload_object['tmp_name'];

						$targetFile = $folder_url . DS . $fileNewName;
						$fileSize = filesize($tempFile);

						if ($fileSize > $sizeLimit) {
							$result = array(
								'error' => 'File is too large. Please ask server admin to increase the file upload limit.',
							);
						}
						if (empty($result)) {
							move_uploaded_file($tempFile, $targetFile);
						}

						$upload_detail['name'] = $_FILES["image1"]['name'];
						$upload_detail['path'] = $return_path . '/' . $element_id . '/' . $fileNewName;
					}

					$response['success'] = true;
					$response['msg'] = "Success";
					$response['content'] = $upload_detail;
				}
				// // pr($upload_detail, 1);
			}

			echo json_encode($upload_detail);
			exit();
		}
	}

	public function add_document($element_id = null) {
		if ($this->request->isAjax()) {

			$response = [
				'success' => false,
				'msg' => '',
				'content' => null,
			];

			$this->layout = 'ajax';
			$this->autoRender = false;

			if ($this->request->is('post') || $this->request->is('put')) {
				// // pr($this->request->data, 1);
				$check_file = 1;

				if (empty($this->request->data['Documents']['id'])) {
					$this->request->data['Documents']['creater_id'] = $this->Auth->user('id');
				}

				$this->request->data['Documents']['updated_user_id'] = $this->Auth->user('id');

				if (isset($this->request->data['Documents']['id']) && empty($this->request->data['Documents']['id'])) {
					if (isset($this->request->data['extension_valid']) && empty($this->request->data['extension_valid'])) {

						$check_file = ($this->request->data['extension_valid'] > 1) ? 1 : 2;
					}
				} else {
					if (isset($this->request->data['extension_valid']) && empty($this->request->data['extension_valid'])) {

						$check_file = ($this->request->data['extension_valid'] > 1) ? 1 : 2;
					}
				}

				$sizeLimit = 10 * 1024 * 1024; // 5MB
				$folder_url = WWW_ROOT . ELEMENT_DOCUMENT_PATH;
				$result = $fileNewName = $upload_object = $upload_detail = null;

				if ($check_file == true) {

					$upload_object = (isset($_FILES["file_name"])) ? $_FILES["file_name"] : null;

					$folder_url .= DS . $element_id;

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
								$last_row = $this->ElementDocument->find('first', array(
									'recursive' => '-1',
									'fields' => [
										'id',
									],
									'order' => 'ElementDocument.id DESC',
								));
							}
							$fileNewName = $orgFileName;
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
							$response['msg'] = "File size limit exceeded,Please upload a file upto 10MB.";
						}
					}
				}

				if (isset($upload_detail) && !empty($upload_detail)) {
					$this->request->data['Documents']['file_name'] = $upload_detail['name'];
					$this->request->data['Documents']['file_size'] = $upload_detail['size'];
					$this->request->data['Documents']['file_type'] = $upload_detail['type'];
				} else {
					unset($this->request->data['Documents']['file_name']);
					unset($this->request->data['Documents']['file_size']);
					unset($this->request->data['Documents']['file_type']);
				}

				if (isset($check_file) && !empty($check_file) && $check_file == 1) {

					$this->ElementDocument->set($this->request->data['Documents']);

					if ($this->ElementDocument->validates()) {

						if ($this->ElementDocument->save($this->request->data['Documents'])) {

							$id = 0;
							if (isset($this->request->data['Documents']['id']) && empty($this->request->data['Documents']['id'])) {

								$id = $this->ElementDocument->getLastInsertId();
							} else {
								$id = $this->request->data['Documents']['id'];
							}
							$this->ElementDocument->updateAll(
								array("ElementDocument.create_activity" => 0),
								array("ElementDocument.id" => $id)
							);

							$this->request->data['Element']['id'] = $element_id;
							$this->request->data['Element']['updated_user_id'] = $this->Auth->user('id');
							// $this->Element->save($this->request->data['Element']);

							$element_doc = null;
							if (isset($id) && !empty($id)) {
								$element_doc = $this->ElementDocument->find('first', [
									'conditions' => [
										'ElementDocument.id' => $id,
									],
								]);
							}

							// Get Project Id with Element id; Update Project modified date
							// $this->update_project_modify($element_id);
							// Update elemnt task activity.
							// $this->update_task_activity($element_id);

							$creator_id = $element_doc['ElementDocument']['creater_id'];
							
							

							$element_doc['ElementDocument']['creator'] = $this->Common->elementDoc_creator($id, $this->request->data['Documents']['project_id'], $creator_id);
							
							$element_doc['ElementDocument']['title'] =   htmlentities($this->request->data['Documents']['title'], ENT_QUOTES, "UTF-8") ;  

							$element_doc['ElementDocument']['created'] = $element_doc['ElementDocument']['created'] = $this->objView->loadHelper('Wiki')->_displayDate(date('Y-m-d h:i:s A', strtotime($element_doc['ElementDocument']['created'])), $format = 'd M, Y g:iA');

							$element_doc['ElementDocument']['modified'] = $element_doc['ElementDocument']['modified'] = $this->objView->loadHelper('Wiki')->_displayDate(date('Y-m-d h:i:s A', strtotime($element_doc['ElementDocument']['modified'])), $format = 'd M, Y g:iA');

							$response['success'] = true;
							$response['msg'] = "Success";
							$response['content'] = $element_doc;
						} else {
							$response['msg'] = "Error!!!";
						}
					} else {
						$response['content'] = $this->validateErrors($this->ElementDocument);
					}
				}
			}

			echo json_encode($response);
			exit();
		}
	}

	public function add_note_document($element_id = null) {
		if ($this->request->isAjax()) {

			$response = [
				'success' => false,
				'msg' => '',
				'content' => null,
			];

			$this->layout = 'ajax';
			$this->autoRender = false;

			if ($this->request->is('post') || $this->request->is('put')) {

				$check_file = 1;

				if (isset($this->request->data['Documents']['id']) && empty($this->request->data['Documents']['id'])) {
					if (isset($this->request->data['extension_valid']) && empty($this->request->data['extension_valid'])) {

						$check_file = ($this->request->data['extension_valid'] > 1) ? 1 : 2;
					}
				} else {
					if (isset($this->request->data['extension_valid']) && empty($this->request->data['extension_valid'])) {

						$check_file = ($this->request->data['extension_valid'] > 1) ? 1 : 2;
					}
				}

				$sizeLimit = 10 * 1024 * 1024; // 5MB
				$folder_url = WWW_ROOT . ELEMENT_DOCUMENT_PATH;
				$result = $fileNewName = $upload_object = $upload_detail = null;

				if ($check_file == true) {

					$upload_object = (isset($_FILES["file_name"])) ? $_FILES["file_name"] : null;

					$folder_url .= DS . $element_id;

					if ($upload_object) {
						if (!file_exists($folder_url)) {
							mkdir($folder_url, 0777, true);
						}
						// for($i = 0, $n = count ( $upload_object ['name'] ); $i < $n; $i ++) { }

						$sizeMB = 0;
						$sizeStr = "";
						$sizeKB = $upload_object['size'] / 1024;
						// // pr($upload_object);
						// // pr(($sizeKB/(1024)), 1);
						if (($sizeKB) > 1024) {
							$sizeMB = $sizeKB / 1024;
							$sizeStr = number_format($sizeMB, 2) . " MB";
						} else {
							$sizeStr = number_format($sizeKB, 2) . " KB";
						}

						$nameArr = explode(".", $upload_object['name']);

						$fileName = (!empty($nameArr) && isset($nameArr[0])) ? $nameArr[0] : '';
						$fileExt = (!empty($nameArr) && isset($nameArr)) ? $nameArr[1] : '';
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
								$last_row = $this->ElementDocument->find('first', array(
									'recursive' => '-1',
									'fields' => [
										'id',
									],
									'order' => 'ElementDocument.id DESC',
								));
								if (isset($last_row) && !empty($last_row)) {
									$fileNewName = $fileName;
									$fileNewName .= '-' . ($last_row['ElementDocument']['id'] + 1);
									$fileNewName .= '.' . $fileExt;
								}
							} else {
								$fileNewName .= $orgFileName;
							}

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
							$response['msg'] = "File size limit exceeded,Please upload a file upto 10MB.";
						}
					}
				}

				if (isset($upload_detail) && !empty($upload_detail)) {
					$this->request->data['Documents']['file_name'] = $upload_detail['name'];
					$this->request->data['Documents']['file_size'] = $upload_detail['size'];
					$this->request->data['Documents']['file_type'] = $upload_detail['type'];
				} else {
					unset($this->request->data['Documents']['file_name']);
					unset($this->request->data['Documents']['file_size']);
					unset($this->request->data['Documents']['file_type']);
				}

				if (isset($check_file) && !empty($check_file) && $check_file == 1) {

					$this->ElementDocument->set($this->request->data['Documents']);

					if ($this->ElementDocument->validates()) {

						if ($this->ElementDocument->save($this->request->data['Documents'])) {

							// Get Project Id with Element id; Update Project modified date
							// $this->update_project_modify($element_id);

							$id = 0;
							if (isset($this->request->data['Documents']['id']) && empty($this->request->data['Documents']['id'])) {

								$id = $this->ElementDocument->getLastInsertId();
							} else {
								$id = $this->request->data['Documents']['id'];
							}
							$element_doc = null;
							if (isset($id) && !empty($id)) {
								$element_doc = $this->ElementDocument->find('first', [
									'conditions' => [
										'ElementDocument.id' => $id,
									],
								]);
							}

							$response['success'] = true;
							$response['msg'] = "Success";
							$response['content'] = $element_doc;
						} else {
							$response['msg'] = "Error!!!";
						}
					} else {
						$response['content'] = $this->validateErrors($this->ElementDocument);
					}
				}
			}

			echo json_encode($response);
			exit();
		}
	}

	/**
	 * Update background color code of an element in workspace->areas page
	 *
	 * @param
	 *        	$element_id
	 *
	 * @return void
	 */
	public function remove_decision($element_id = null) {
		if ($this->request->is('ajax')) {

			$response = [
				'success' => false,
				'msg' => '',
				'content' => null,
			];

			$this->layout = 'ajax';
			$this->autoRender = false;

			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;
				// // pr($post, 1);

				if (isset($post['decision_id']) && !empty($post['decision_id'])) {

					$id = $post['decision_id'];

					$this->ElementDecision->id = $id;

					$dids = $this->ElementDecision->findById($id);

					/*
						                     * if (!$this->ElementDecision->exists()) {
						                     * throw new NotFoundException(__('Invalid detail'), 'error');
						                     * }
					*/
					// $d = $this->ElementDecision->ElementDecisionDetail->find('count', ['conditions' => ['ElementDecision.element_id' => $element_id]]);
					// echo $this->ElementDecisionDetail->_query();
					// // pr($d, 1);

					if ($this->ElementDecision->deleteAll([
						'ElementDecision.id' => $id,
					], true)) {

						$this->request->data['Element']['id'] = $element_id;
						$this->request->data['Element']['updated_user_id'] = $this->Auth->user('id');
						// $this->Element->save($this->request->data['Element']);
						// Get Project Id with Element id; Update Project modified date
						// $this->update_project_modify($element_id);
						// $this->update_task_activity($element_id);

						$defaultElementid = $dids['ElementDecision']['element_id'];

						$task_data = [
						'project_id' => element_project($defaultElementid),
						'workspace_id' => element_workspace($defaultElementid),
						'element_id' => $defaultElementid,
						'element_type' => 'element_decisions',
						'user_id' => $this->user_id,
						'relation_id' => $dids['ElementDecision']['id'],
						'updated_user_id' => $this->user_id,
						'message' => 'Decision deleted',
						'updated' => date("Y-m-d H:i:s"),
						];
						$this->loadModel('Activity');
						$this->Activity->id = null;
						$this->Activity->save($task_data);


						$response['success'] = true;
						$response['msg'] = 'Element decision has been deleted successfully.';
						$response['content'] = [];
					} else {
						$response['msg'] = 'Element decision could not deleted successfully.';
					}
				}
			}

			echo json_encode($response);
			exit();
		}
	}

	/**
	 * Add decisions to the element
	 *
	 * @param
	 *        	$element_id
	 *
	 * @return void
	 */
	public function add_decision($element_id = null) {
		if ($this->request->isAjax()) {

			$response = [
				'success' => false,
				'action' => null,
				'content' => null,
			];

			$this->layout = 'ajax';
			$this->autoRender = false;

			if ($this->request->is('post') || $this->request->is('put')) {
				// // pr($this->request->data, 1);
				$id = $decision_id = $element_id = null;
				$dbError = false;

				$postedED = $this->request->data['ElementDecision'];

				// Get action value:
				// 1. create_decision: If no decision is exists in database for the element
				// required: ElementDecision.element_id, ElementDecision.title
				// 2. create_decision_detail: If no decision is selected for the element
				// required: ElementDecision.element_id, ElementDecisionDetail.decision_id, ElementDecision.title, ElementDecisionDetail.description
				// 3. update_decision_detail: update a specific decision detail
				// required: ElementDecision.element_id, ElementDecisionDetail.id, ElementDecisionDetail.decision_id, ElementDecision.title, ElementDecisionDetail.description

				$action = $this->request->data['action'];

				$this->ElementDecision->set($this->request->data['ElementDecision']);
				if ($this->ElementDecision->validates()) {

					// check on update for the combination of element_id and decision_id
					// if exists, return error

					if (isset($postedED['element_id']) && !empty($postedED['element_id'])) {
						$element_id = $postedED['element_id'];
					}
					if (isset($postedED['id']) && !empty($postedED['id'])) {
						$id = $postedED['id'];
					} else {
						$this->request->data['ElementDecision']['creater_id'] = $this->Auth->user('id');
					}

					if (!empty($action)) {

						// Get all data of the supplied element id
						$this->Element->unbindModel(array(
							'hasMany' => array(
								'Links',
								'Documents',
								'Notes',
								'Mindmaps',
							),
							'belongsTo' => [
								'Area',
							],
						));

						if ($action == 'create_decision') {
							$this->request->data['ElementDecision']['updated_user_id'] = $this->Auth->user('id');

							if ($this->ElementDecision->save($this->request->data['ElementDecision'])) {
								$id = $this->ElementDecision->getLastInsertId();

								$this->ElementDecision->updateAll(
									array("ElementDecision.create_activity" => 0),
									array("ElementDecision.id" => $id)
								);

								if (!empty($id)) {

									$element_decisions = $this->ElementDecision->find('first', [
										'conditions' => [
											'ElementDecision.element_id' => $element_id,
										],
										'recursive' => 1,
									]);
								}

								// Get Project Id with Element id; Update Project modified date
								// $this->update_project_modify($element_id);
								// Update elemnt task activity.
								// $this->update_task_activity($element_id);

								$this->request->data['Element']['id'] = $element_id;
								$this->request->data['Element']['updated_user_id'] = $this->Auth->user('id');
								
								// $this->Element->save($this->request->data['Element']);

								// // pr($element_decisions, 1);
								$content = null;
								if (!empty($element_decisions)) {
									if (isset($element_decisions['Element']) && !empty($element_decisions['Element'])) {
										$tempData = $element_decisions['ElementDecision'];
										$tempData['created'] = _displayDate($tempData['created']);
										$tempData['modified'] = _displayDate($tempData['modified']);
										$tempData['creator'] = $this->Common->userFullname($tempData['creater_id']);
										
										$tempData['title'] =  htmlentities($tempData['title'], ENT_QUOTES, "UTF-8") ;
										$element_decisions['Element']['ElementDecision'] = $tempData;

										$content['e_data'] = $element_decisions['Element'];
									}
									if (isset($element_decisions['ElementDecision']) && !empty($element_decisions['ElementDecision'])) {
										$tempData = $element_decisions['ElementDecision'];
										$tempData['created'] = _displayDate($tempData['created']);
										$tempData['creater'] = $tempData['creater_id'];
										$tempData['creator'] = $this->Common->userFullname($tempData['creater_id']);
										$tempData['modified'] = _displayDate($tempData['modified']);
										//$tempData['title'] =  htmlentities($tempData['title'], ENT_QUOTES, "UTF-8") ;

										$content['ed_data'] = $tempData;
									}
								}
								// // pr($content, 1);
								$response['success'] = true;
								$response['content'] = $content;
							} else {
								$response['success'] = false;
							}
						}
					}
				} else {
					$response['content'] = $this->validateErrors($this->ElementDecision);
				}
			}

			echo json_encode($response);
			exit();
		}
	}

	/**
	 * Add decisions to the element
	 *
	 * @param
	 *        	$element_id
	 *
	 * @return void
	 */
	public function add_decision_detail($element_id = null) {
		if ($this->request->isAjax()) {

			$response = [
				'success' => false,
				'action' => null,
				'content' => null,
			];

			$this->layout = 'ajax';
			$this->autoRender = false;

			if ($this->request->is('post') || $this->request->is('put')) {
				$this->request->data['ElementDecision']['updated_user_id'] = $this->Auth->user('id');
				$this->request->data['ElementDecisionDetail']['updated_user_id'] = $this->Auth->user('id');
				// // pr($this->request->data, 1);
				$id = $decision_id = $element_id = $element_decision_id = null;
				$dbError = false;

				$postedED = $this->request->data['ElementDecision'];
				$postedEDD = $this->request->data['ElementDecisionDetail'];
				/*
					                 * // Get action value:
					                 * // 1. create_decision: If no decision is exists in database for the element
					                 * // required: ElementDecision.element_id, ElementDecision.title
					                 *
					                 * // 2. create_decision_detail: If no decision is selected for the element
					                 * // required: ElementDecision.element_id, ElementDecisionDetail.decision_id, ElementDecision.title, ElementDecisionDetail.description
					                 *
					                 * // 3. update_decision_detail: update a specific decision detail
					                 * // required: ElementDecision.element_id, ElementDecisionDetail.id, ElementDecisionDetail.decision_id, ElementDecision.title, ElementDecisionDetail.description
				*/
				$action = $this->request->data['action'];

				$this->ElementDecisionDetail->set($this->request->data['ElementDecisionDetail']);
				if ($this->ElementDecisionDetail->validates()) {
					// // pr($this->request->data, 1);
					// check on update for the combination of element_id and decision_id
					// if exists, return error

					if (isset($postedED['id']) && !empty($postedED['id'])) {
						$postedEDD['element_decision_id'] = $postedED['id'];
					}
					if (isset($postedED['element_id']) && !empty($postedED['element_id'])) {
						$element_id = $postedED['element_id'];
					}
					if (isset($postedEDD['id']) && !empty($postedEDD['id'])) {
						$id = $postedEDD['id'];
					}
					if (isset($postedEDD['decision_id']) && !empty($postedEDD['decision_id'])) {
						$decision_id = $postedEDD['decision_id'];
					}

					if (!empty($action)) {

						// Get all data of the supplied element id
						$this->Element->unbindModel(array(
							'hasMany' => array(
								'Links',
								'Documents',
								'Notes',
								'Mindmaps',
							),
							'belongsTo' => [
								'Area',
							],
						));

						// UPDATE

						$this->request->data['ElementDecisionDetail']['create_activity'] = true;
						if ($this->ElementDecisionDetail->save($this->request->data['ElementDecisionDetail'])) {

							$response['success'] = true;

							if (isset($this->request->data['ElementDecisionDetail']['id']) && !empty($this->request->data['ElementDecisionDetail']['id'])) {
								$id = $this->request->data['ElementDecisionDetail']['id'];
							} else {
								$id = $this->ElementDecisionDetail->getLastInsertId();
							}

							$this->ElementDecisionDetail->updateAll(
								array("ElementDecisionDetail.create_activity" => 0),
								array("ElementDecisionDetail.id" => $id)
							);
							// Update elemnt task activity.
							// $this->update_task_activity($element_id);

							$this->request->data['Element']['id'] = $element_id;
							$this->request->data['Element']['updated_user_id'] = $this->Auth->user('id');
							// $this->Element->save($this->request->data['Element']);

						}
						// end update

						if ((isset($postedED['id']) && !empty($postedED['id'])) && isset($element_id) && !empty($element_id)) {
							// Update element decision main table title
							$this->request->data['ElementDecision']['updated_user_id'] = $this->Auth->user('id');
							$postElementDec = $this->request->data['ElementDecision'];
							$postElementDec['modified'] = date('Y-m-d H:i:s');

							if ($this->ElementDecision->save($postElementDec)) {
								$response['success'] = true;

							}
						}

						$element_decisions = $this->ElementDecision->find('first', [
							'conditions' => [
								'ElementDecision.element_id' => $element_id,
							],
							'recursive' => 2,
						]);

						// Get Project Id with Element id; Update Project modified date
						// $this->update_project_modify($element_id);

						$content = null;
						if (!empty($element_decisions)) {
							if (isset($element_decisions['Element']) && !empty($element_decisions['Element'])) {

								$tempData = $element_decisions['Element']['ElementDecision'];
								//$tempData['created'] = _displayDate($tempData['created']);
								//$tempData['modified'] = _displayDate($tempData['modified']);
								$tempData['created'] = $this->objView->loadHelper('Wiki')->_displayDate(date('Y-m-d h:i:s A', strtotime($tempData['created'])), $format = 'd M, Y g:iA');
								$tempData['modified'] = $this->objView->loadHelper('Wiki')->_displayDate(date('Y-m-d h:i:s A', strtotime($tempData['modified'])), $format = 'd M, Y g:iA');
								
								$tempData['title'] =  htmlentities($tempData['title'], ENT_QUOTES, "UTF-8") ;
								//$tempData['comment'] =  htmlentities($tempData['comment'], ENT_QUOTES, "UTF-8") ;

								$element_decisions['Element']['ElementDecision'] = $tempData;

								$content['e_data'] = $element_decisions['Element'];
							}
							if (isset($element_decisions['ElementDecision']) && !empty($element_decisions['ElementDecision'])) {
								$tempData = $element_decisions['ElementDecision'];
								//$tempData['created'] = _displayDate($tempData['created']);
								//$tempData['modified'] = _displayDate($tempData['modified']);
								$tempData['title'] =  htmlentities($tempData['title'], ENT_QUOTES, "UTF-8") ;
								//$tempData['comment'] =  htmlentities($tempData['comment'], ENT_QUOTES, "UTF-8") ;
								$tempData['created'] = $this->objView->loadHelper('Wiki')->_displayDate(date('Y-m-d h:i:s A', strtotime($tempData['created'])), $format = 'd M, Y g:i A');
								$tempData['modified'] = $this->objView->loadHelper('Wiki')->_displayDate(date('Y-m-d h:i:s A', strtotime($tempData['modified'])), $format = 'd M, Y g:i A');

								$content['ed_data'] = $tempData;
							}
							if (isset($element_decisions['ElementDecisionDetail']) && !empty($element_decisions['ElementDecisionDetail'])) {
								$content['edd_data'] = $element_decisions['ElementDecisionDetail'];
							}
						}
						$current_row = null;
						if (isset($id) && !empty($id)) {
							$current_row = $this->ElementDecisionDetail->find('first', [
								'conditions' => [
									'ElementDecisionDetail.id' => $id,
								],
								'recursive' => -1,
							]);
							//pr($current_row);die;
						}
						$response['success'] = true;
						$response['current'] = $current_row;
						$response['content'] = $content;
					}
				} else {
					$response['content'] = $this->validateErrors($this->ElementDecision);
				}
			}

			echo json_encode($response);
			exit();
		}
	}

	/**
	 * Add feedback to the element
	 *
	 * @param
	 *        	$element_id
	 *
	 * @return void
	 */
	public function create_feedback($url_element_id = null) {
		if ($this->request->isAjax()) {

			$response = [
				'success' => false,
				'action' => null,
				'content' => null,
			];

			$this->layout = 'ajax';
			$this->autoRender = false;

			if ($this->request->is('post') || $this->request->is('put')) {
				// // pr($this->request->data, 1);
				$id = $feedback_id = $element_id = null;
				$dbError = false;

				$postedEF = $this->request->data['ElementFeedback'];

				// Get action value:
				// 1. create_decision: If no decision is exists in database for the element
				// required: ElementFeedback.element_id, ElementFeedback.title
				// 2. create_decision_detail: If no decision is selected for the element
				// required: ElementFeedback.element_id, ElementDecisionDetail.decision_id, ElementFeedback.title, ElementDecisionDetail.description
				// 3. update_decision_detail: update a specific decision detail
				// required: ElementFeedback.element_id, ElementDecisionDetail.id, ElementDecisionDetail.decision_id, ElementFeedback.title, ElementDecisionDetail.description

				$action = $this->request->data['action'];

				$this->ElementFeedback->set($this->request->data['ElementFeedback']);
				if ($this->ElementFeedback->validates()) {

					// check on update for the combination of element_id and feedback_id
					// if exists, return error

					if (isset($postedEF['element_id']) && !empty($postedEF['element_id'])) {
						$element_id = $postedEF['element_id'];

						// Get Project Id with Element id; Update Project modified date
						// $this->update_project_modify($element_id);
					}
					if (isset($postedEF['id']) && !empty($postedEF['id'])) {
						$id = $postedEF['id'];
					}

					if (!empty($action)) {

						// Get all data of the supplied element id
						$this->Element->unbindModel(array(
							'hasMany' => array(
								'Links',
								'Documents',
								'Notes',
								'Mindmaps',
							),
							'belongsTo' => [
								'Area',
							],
						));

						if ($action == 'create_feedback') {

							if ($this->ElementFeedback->save($this->request->data['ElementFeedback'])) {
								$id = $this->ElementFeedback->getLastInsertId();
								if (!empty($id)) {

									$element_feedbacks = $this->ElementFeedback->find('first', [
										'conditions' => [
											'ElementFeedback.element_id' => $element_id,
										],
										'recursive' => 2,
									]);
								}
								//
								$content = null;
								if (!empty($element_feedbacks)) {
									if (isset($element_feedbacks['Element']) && !empty($element_feedbacks['Element'])) {
										$content['e_data'] = $element_feedbacks['Element'];
									}
									if (isset($element_feedbacks['ElementFeedback']) && !empty($element_feedbacks['ElementFeedback'])) {
										$content['ef_data'] = $element_feedbacks['ElementFeedback'];
									}
								}
								// // pr($content, 1);
								$response['success'] = true;
								$response['content'] = $content;
							} else {
								$response['success'] = false;
							}
						}
					}
				} else {
					$response['content'] = $this->validateErrors($this->ElementFeedback);
				}
			}

			echo json_encode($response);
			exit();
		}
	}

	/**
	 * Add feedback detail to the element
	 *
	 * @param
	 *        	$element_id
	 *
	 * @return void
	 */
	public function create_feedback_detail($elementId = null) {
		if ($this->request->is('ajax')) {

			$response = [
				'success' => false,
				'action' => null,
				'content' => null,
			];

			$this->layout = 'ajax';
			$this->autoRender = false;

			if ($this->request->is('post') || $this->request->is('put')) {
				// // pr($this->request->data, 1);
				$id = $feedback_id = $element_id = $element_feedback_id = null;
				$dbError = false;

				$postedEF = $this->request->data['ElementFeedback'];
				$postedEFD = $this->request->data['ElementFeedbackDetail'];
				/*
					                 * // Get action value:
					                 * // 1. create_decision: If no decision is exists in database for the element
					                 * // required: ElementFeedback.element_id, ElementFeedback.title
					                 *
					                 * // 2. create_decision_detail: If no decision is selected for the element
					                 * // required: ElementFeedback.element_id, ElementFeedbackDetail.feedback_id, ElementFeedback.title, ElementFeedbackDetail.description
					                 *
					                 * // 3. update_decision_detail: update a specific decision detail
					                 * // required: ElementFeedback.element_id, ElementFeedbackDetail.id, ElementFeedbackDetail.feedback_id, ElementFeedback.title, ElementFeedbackDetail.description
				*/
				$action = $this->request->data['action'];

				$this->ElementFeedbackDetail->set($this->request->data['ElementFeedbackDetail']);
				if ($this->ElementFeedbackDetail->validates()) {

					// check on update for the combination of element_id and feedback_id
					// if exists, return error

					if (isset($postedEF['id']) && !empty($postedEF['id'])) {
						$postedEFD['element_feedback_id'] = $postedEF['id'];
					}
					if (isset($postedEF['element_id']) && !empty($postedEF['element_id'])) {
						$element_id = $postedEF['element_id'];
					}
					if (isset($postedEFD['id']) && !empty($postedEFD['id'])) {
						$id = $postedEFD['id'];
					}
					if (isset($postedEFD['feedback_id']) && !empty($postedEFD['feedback_id'])) {
						$feedback_id = $postedEFD['feedback_id'];
					}

					if (!empty($action)) {

						// Get Project Id with Element id; Update Project modified date
						// $this->update_project_modify($element_id);

						// Get all data of the supplied element id
						$this->Element->unbindModel(array(
							'hasMany' => array(
								'Links',
								'Documents',
								'Notes',
								'Mindmaps',
							),
							'belongsTo' => [
								'Area',
							],
						));

						if (isset($element_id) && !empty($element_id)) {

							// Update element decision main table title
							$postTitle = $this->request->data['ElementFeedback']['title'];
							$sql = "UPDATE element_feedbacks set title = '" . $postTitle . "' WHERE element_id = '" . $element_id . "'";
							$updateSuccess = $this->ElementFeedback->query($sql);
						}

						if ($action == 'create_feedback_detail') {

							if (isset($feedback_id) && !empty($feedback_id)) {

								if ($this->ElementFeedbackDetail->save($postedEFD)) {
									$id = $this->ElementFeedbackDetail->getLastInsertId();
								}
							}
						} else if ($action == 'update_feedback_detail' && !empty($id)) {

							// ERROR/UPDATE
							$postTitle = $this->request->data['ElementFeedback']['title'];
							$sql = "UPDATE element_feedbacks set title = '" . $postTitle . "' WHERE element_id = '" . $element_id . "'";
							$updateSuccess = $this->ElementFeedback->query($sql);

							if ($updateSuccess) {

								if ($this->ElementFeedbackDetail->save($this->request->data['ElementFeedbackDetail'])) {
									$response['success'] = true;
								}
								// echo $this->ElementFeedback->_query();
								// // pr($updateSuccess);
							} else {
								$dbError = true;
								// pr("ERROR");
							}
						} // end update

						$element_feedbacks = $this->ElementFeedback->find('first', [
							'conditions' => [
								'ElementFeedback.element_id' => $element_id,
							],
							'recursive' => 2,
						]);
						$content = null;
						if (!empty($element_feedbacks)) {

							if (isset($element_feedbacks['Element']['ElementDecision']) && !empty($element_feedbacks['Element']['ElementDecision'])) {
								unset($element_feedbacks['Element']['ElementDecision']);
							}

							if (isset($element_feedbacks['Element']) && !empty($element_feedbacks['Element'])) {
								$content['e_data'] = $element_feedbacks['Element'];
							}
							if (isset($element_feedbacks['ElementFeedback']) && !empty($element_feedbacks['ElementFeedback'])) {
								$content['ef_data'] = $element_feedbacks['ElementFeedback'];
							}
							if (isset($element_feedbacks['ElementFeedbackDetail']) && !empty($element_feedbacks['ElementFeedbackDetail'])) {
								$content['efd_data'] = $element_feedbacks['ElementFeedbackDetail'];
							}
						}
						// // pr($element_feedbacks, 1);
						$current_row = null;
						if (isset($id) && !empty($id)) {
							$current_row = $this->ElementFeedbackDetail->find('first', [
								'conditions' => [
									'ElementFeedbackDetail.id' => $id,
								],
								'recursive' => -1,
							]);
						}
						$response['success'] = true;
						$response['current'] = $current_row;
						$response['content'] = $content;
					}
				} else {
					$response['content'] = $this->validateErrors($this->ElementFeedbackDetail);
				}
			}

			echo json_encode($response);
			exit();
		}
	}

	/**
	 * Update background color code of an element in workspace->areas page
	 *
	 * @param
	 *        	$element_id
	 *
	 * @return void
	 */
	public function remove_doc($element_id = null) {
		if ($this->request->is('ajax')) {

			$response = [
				'success' => false,
				'msg' => '',
				'content' => null,
			];

			$this->layout = 'ajax';
			$this->autoRender = false;

			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;
				// // pr($post, 1);

				if (isset($post['doc_id']) && !empty($post['doc_id'])) {
					$id = $post['doc_id'];
					$this->ElementDocument->id = $element_id;
					$dids = $this->ElementDocument->findById($element_id);
					if (!$this->ElementDocument->exists()) {
						throw new NotFoundException(__('Invalid detail'), 'error');
					}
					if ($this->ElementDocument->delete()) {
						$this->request->data['Element']['id'] = $dids['ElementDocument']['element_id'];
						$this->request->data['Element']['updated_user_id'] = $this->Auth->user('id');

						$this->Element->id = $dids['ElementDocument']['element_id'];

						$defaultElementid = $dids['ElementDocument']['element_id'];

						$task_data = [
						'project_id' => element_project($defaultElementid),
						'workspace_id' => element_workspace($defaultElementid),
						'element_id' => $defaultElementid,
						'element_type' => 'element_documents',
						'user_id' => $this->user_id,
						'relation_id' => $dids['ElementDocument']['id'],
						'updated_user_id' => $this->user_id,
						'message' => 'Document deleted',
						'updated' => date("Y-m-d H:i:s"),
						];
						$this->loadModel('Activity');
						$this->Activity->id = null;
						$this->Activity->save($task_data);

						// $this->Element->save($this->request->data['Element']);

						// Get Project Id with Element id; Update Project modified date
						// $this->update_project_modify($dids['ElementDocument']['element_id']);
						// $this->update_task_activity($dids['ElementDocument']['element_id']);
						$response['success'] = true;
						$response['msg'] = 'Element document data has been deleted successfully.';
						$response['content'] = [];
					} else {
						$response['msg'] = 'Element could not deleted successfully.';
					}

					$response['success'] = true;
					// $this->Element->_query(1);
				}
			}

			echo json_encode($response);
			exit();
		}
	}

	/**
	 * Update background color code of an element in workspace->areas page
	 *
	 * @param
	 *        	$element_id
	 *
	 * @return void
	 */
	public function download_asset1($id = null) {
		if (isset($id) && !empty($id)) {
			// Retrieve the file ready for download
			$data = $this->ElementDocument->findById($id);
			if (empty($data)) {
				throw new NotFoundException();
			}

			// Send file as response
			$upload_path = ELEMENT_DOCUMENT_PATH . DS . $data['ElementDocument']['element_id'] . DS . $data['ElementDocument']['file_name'];

			$this->response->file($upload_path, array(
				'download' => true,
				'name' => $data['ElementDocument']['file_name'],
			));
			return $this->response;
		}
	}

	/**
	 * Update background color code of an element in workspace->areas page
	 *
	 * @param
	 *        	$element_id
	 *
	 * @return void
	 */
	public function download_asset($id = null) {
		// if ($this->request->is('ajax')) {
		if (isset($id) && !empty($id)) {
			// Retrieve the file ready for download
			$data = $this->ElementDocument->findById($id);
			if (empty($data)) {
				throw new NotFoundException();
			}

			$response = [
				'success' => false,
				'content' => null,
			];

			if (isset($data) && !empty($data)) {
				// Send file as response
				$response['content'] = ELEMENT_DOCUMENT_PATH . DS . $data['ElementDocument']['element_id'] . DS . $data['ElementDocument']['file_name'];
				$response['success'] = true;
			}
			$this->autoRender = false;

			return $this->response->file($response['content'], array('download' => true));
			// echo json_encode($response);
			// die;
			// return $this->response;
		}
		// }
	}

	/**
	 * Update sign off status of element
	 *
	 * @param
	 *        	none
	 *
	 * @return void
	 */
	public function element_signoff() {
		if ($this->request->is('ajax')) {

			$response = [
				'success' => false,
				'msg' => '',
				'content' => null,
			];

			$this->layout = 'ajax';
			$this->autoRender = false;

			if ($this->request->is('post') || $this->request->is('put')) {
				// echo $element_id;
				$this->request->data['Element']['create_activity'] = 1;
				$this->request->data['Element']['updated_user_id'] = $this->Session->read('Auth.User.id');
				$post = $this->request->data['Element'];


				if (isset($post['id']) && !empty($post['id'])) {
					$id = $post['id'];

					$this->Element->id = $id;
					$element_id = $id;
					if (!$this->Element->exists()) {
						throw new NotFoundException(__('Invalid detail'), 'error');
					}
					// SIGNOFF DATE
					if(isset($this->request->data['Element']['sign_off']) && empty($this->request->data['Element']['sign_off']) && $this->request->data['Element']['sign_off'] == 0){
						$post['sign_off_date'] = '';
					}

					if ($this->Element->save($post)) {

						/* exits sign-off entry delete ****************/
						$del = array('element_id'=>$element_id);
						$signoffdata = $this->SignoffTask->find('first',array('conditions'=>$del ));
						if( isset($signoffdata) ){
							if( !empty(!empty($signoffdata['SignoffTask']['task_evidence'])) ){
								$folder_url = WWW_ROOT . ELEMENT_SIGNOFF_PATH;
								unlink($folder_url.'/'.$signoffdata['SignoffTask']['task_evidence']);
							}
							$this->SignoffTask->deleteAll($del);
						}
						/********************************/

						$this->Element->unbindModel(array('hasOne' => ['ElementDecision']));
						$this->Element->updateAll(array("Element.create_activity" => 0), array("Element.id" => $id));
						// Get Project Id with Element id; Update Project modified date
						// $this->update_project_modify($id);
						// $this->update_task_up_activity($id);

						$emai_type = 'signed-off';
						if ($post['sign_off'] == 0) {
							$emai_type = 're-opened';
						}
						//$post['sign_off'] == 0; reopen
						//$post['sign_off'] == 1; sign-off

						$response['success'] = true;
						$response['msg'] = 'You have been signed off successfully.';
						$response['content'] = [];

						/* ============= Strat Element Signoff Email TO Owners =============================== */

						$participants_owners = array();
						$participantsGpOwner = array();
						$elementDetail = $this->Element->findById($element_id);
						$elementName = '';
						if (isset($elementDetail['Element']['title']) && !empty($elementDetail['Element']['title'])) {
							$elementName = $elementDetail['Element']['title'];
						}
						$project_id = element_project($element_id);

						$all_owner = element_users(array($element_id), $project_id);
						$all_owner_tot = ( isset($all_owner) && !empty($all_owner) ) ? count($all_owner) : 0;
						if (isset($all_owner) && !empty($all_owner)) {
							if (($key = array_search($this->user_id, $all_owner)) !== false) {
								unset($all_owner[$key]);
							}
						}
							// pr($all_owner, 1);
						if ( ($all_owner_tot) > 0 && !empty($elementName) && !empty($project_id)) {
							/************** socket messages **************/
							if (SOCKET_MESSAGES) {
								$current_user_id = $this->user_id;
								$ele_users = $all_owner;
								if (isset($ele_users) && !empty($ele_users)) {
									if (($key = array_search($current_user_id, $ele_users)) !== false) {
										unset($ele_users[$key]);
									}
								}
								$s_open_users = $r_open_users = null;
								if (isset($ele_users) && !empty($ele_users)) {
									foreach ($ele_users as $key => $value) {
										if (web_notify_setting($value, 'element', 'element_sign_off')) {
											$s_open_users[] = $value;
										}
										if (web_notify_setting($value, 'element', 'element_reopened')) {
											$r_open_users[] = $value;
										}
									}
								}
								$userDetail = get_user_data($current_user_id);
								$heading = (isset($post['sign_off']) && $post['sign_off'] == 1) ? 'Task sign-off' : 'Task re-opened';
								$content = [
									'notification' => [
										'type' => 'task_signoff',
										'created_id' => $current_user_id,
										'project_id' => $project_id,
										'refer_id' => $id,
										'creator_name' => $userDetail['UserDetail']['full_name'],
										'subject' => $heading,
										'heading' => 'Task: ' . strip_tags($elementDetail['Element']['title']),
										'sub_heading' => 'Project: ' . strip_tags(getFieldDetail('Project', $project_id, 'title')),
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
							$this->elementSignOffEmail($elementName, $project_id, $all_owner, $emai_type,$element_id);
						}

						/*========== End Element Signoff Email TO Owners =========================== */

					} else {
						$response['msg'] = 'Signing off could not be completed. Please try again later.';
					}
					// $this->Element->_query(1);
				}
			}

			echo json_encode($response);
			exit();
		}
	}

	/**
	 * Update background color code of an element in workspace->areas page
	 *
	 * @param
	 *        	$element_id
	 *
	 * @return void
	 */
	public function decision_signoff($element_id = null) {
		if ($this->request->is('ajax')) {

			$response = [
				'success' => false,
				'msg' => '',
				'content' => null,
			];

			$this->layout = 'ajax';
			$this->autoRender = false;

			if ($this->request->is('post') || $this->request->is('put')) {
				// echo $element_id;
				$this->request->data['ElementDecision']['create_activity'] = 1;
				$post = $this->request->data['ElementDecision'];
				// // pr($post, 1);

				if (isset($post['id']) && !empty($post['id'])) {
					$id = $post['id'];

					$this->ElementDecision->id = $id;
					if (!$this->ElementDecision->exists()) {
						throw new NotFoundException(__('Invalid detail'), 'error');
					}
					if ($this->ElementDecision->save($post)) {
						$this->ElementDecision->updateAll(
							array("ElementDecision.create_activity" => 0),
							array("ElementDecision.id" => $id)
						);
						// $this->update_task_activity($element_id);
						// Get Project Id with Element id; Update Project modified date
						// $this->update_project_modify($element_id);

						$response['success'] = true;
						$response['msg'] = 'You have been signed off successfully.';
						$response['content'] = [];
					} else {
						$response['msg'] = 'Signing off could not be completed. Please try again later.';
					}
					// $this->Element->_query(1);
				}
			}

			echo json_encode($response);
			exit();
		}
	}

	/**
	 * Update background color code of an element in workspace->areas page
	 *
	 * @param
	 *        	$element_id
	 *
	 * @return void
	 */
	public function feedback_signoff($element_id = null) {
		if ($this->request->is('ajax')) {

			$response = [
				'success' => false,
				'msg' => '',
				'content' => null,
			];

			$this->layout = 'ajax';
			$this->autoRender = false;

			if ($this->request->is('post') || $this->request->is('put')) {
				// echo $element_id;
				$this->request->data['Feedback']['create_activity'] = 1;
				$post = $this->request->data['Feedback'];
				// // pr($post, 1);

				if (isset($post['id']) && !empty($post['id'])) {
					$id = $post['id'];
					$post['sign_off'] = (isset($this->request->data['reopen']) && !empty($this->request->data['reopen']) && $this->request->data['reopen'] == 'reopen') ? 0 : 1;
					$this->Feedback->id = $id;
					if (!$this->Feedback->exists()) {
						throw new NotFoundException(__('This Feedback request has been removed'), 'error');
					}
					//// pr($post);die;
					if ($this->Feedback->save($post)) {
						$this->Feedback->updateAll(
							array("Feedback.create_activity" => 0),
							array("Feedback.id" => $id)
						);
						// $this->update_task_activity($element_id);
						// Get Project Id with Element id; Update Project modified date
						// $this->update_project_modify($element_id);

						$response['success'] = true;
						$response['msg'] = 'You have been signed off successfully.';
						$response['content'] = [];
					} else {
						$response['msg'] = 'Signing off could not be completed. Please try again later.';
					}
					// $this->Element->_query(1);
				}
			}

			echo json_encode($response);
			exit();
		}
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
				// $this->update_project_modify($target);
				// $this->update_project_modify($origin);

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

			/*
				             * $files = $dir->find('.*');
				             * foreach ($files as $file) {
				             * $file = new File( $dir->pwd() . DS . $file);
				             * $contents = $file->read();
				             * // pr($file->name);
				             * // $file->write('I am overwriting the contents of this file');
				             * // $file->append('I am adding to the bottom of this file.');
				             * // $file->delete(); // I am deleting this file
				             * $file->close(); // Be sure to close the file when you're done
				             * }
			*/
		}
		return $done;
	}

	function restrict_copy_paste($target_wsp_id = null, $area_id = null, $element_id = null){
		$return = [
			'success' => false,
			'message' => 'Error in operation',
		];
		if(isset($target_wsp_id) && !empty($target_wsp_id)){
			$target_wsp_data = $this->Workspace->find('first', [
				'conditions' => [
					'Workspace.id' => $target_wsp_id,
				],
				'recursive' => -1,
				'fields' => ['Workspace.sign_off', 'Workspace.start_date', 'Workspace.end_date'],
			]);


			if(isset($target_wsp_data) && !empty($target_wsp_data)){
				$target_wsp_data = $target_wsp_data['Workspace'];
				if($target_wsp_data['sign_off'] != 1){


					if( isset($target_wsp_data['start_date']) && !empty($target_wsp_data['start_date']) && isset($target_wsp_data['end_date']) && !empty($target_wsp_data['end_date'])  ){


						 if((isset($target_wsp_data['end_date']) && $target_wsp_data['end_date'] >= date('Y-m-d')) && (isset($target_wsp_data['start_date']) && $target_wsp_data['start_date'] <= date('Y-m-d 00:00:00'))) {
							$return['success'] = true;

						}

						 if((isset($target_wsp_data['start_date']) && $target_wsp_data['start_date'] > date('Y-m-d 00:00:00')) ) {
							if( FUTURE_DATE == 'on' ){
								$return['success'] = true;
							} else {
								$return['success'] = false;
								$return['message'] = "Target workspace schedule has not reached the start date.";
							}
						}

						if((isset($target_wsp_data['end_date']) && $target_wsp_data['end_date'] < date('Y-m-d')) ) {
							$return['message'] = "Cannot move Task because the Workspace end date has passed.";
						}

						if( isset($element_id) && !empty($element_id) ){
							// Get Element Start and End Date data
							$checkDate = $this->sendingElementDetail($element_id);
							if( isset($checkDate) && !empty($checkDate['Element']) ){

								$eleStartDate = date('Y-m-d 00:00:00',strtotime($checkDate['Element']['start_date']));
								$eleEndDate = date('Y-m-d 00:00:00',strtotime($checkDate['Element']['end_date']));

								//check with workspace start and end date
								if((isset($target_wsp_data['end_date']) && $target_wsp_data['end_date'] < $eleEndDate) || (isset($target_wsp_data['start_date']) && $target_wsp_data['start_date'] > $eleStartDate )) {
									$return['success'] = false;
									$return['message'] = "Target workspace data conditions are not matched with processed area task data.";
								}

							}

						}
					} else {
						$return['success'] = false;
						$return['message'] = "The Workspace has no date schedule.";
					}

				}
				else if( $target_wsp_data['sign_off']== 1) {
					$return['message'] = "Target workspace has Signed-off.";
				}
				if(!isset($target_wsp_data['start_date'])) {
	                $return['message'] = "The Workspace has no date schedule.";
				}
			}

		}

		return $return;
	}
	/**
	 * Update background color code of an element in workspace->areas page
	 *
	 * @param
	 *        	$element_id
	 *
	 * @return void
	 */
	public function cut_copy_paste($element_id = null) {
		if ($this->request->isAjax()) {

			$response = [
				'success' => false,
				'msg' => '',
				'content' => null,
				'error' => '',
			];
			$success = false;
			$wsp_date_passed_msg = 'Target workspace date has been passed.';

			$this->layout = 'ajax';
			$this->autoRender = false;

			if ($this->request->is('post') || $this->request->is('put')) {
				// pr($this->request->data, 1);
				$postCurrentAreaID = null;

				$postWorkspaceId = $this->request->data['workspace_id'];
				$postSortArea = $this->request->data['sort_area'];
				$postElements = $this->request->data['Element'];
				$postElementAction = $this->request->data['element_action'];
				$postprojectid = (isset($this->request->data['project_id']) && !empty($this->request->data['project_id'])) ? $this->request->data['project_id'] : null;
				$user_id = $this->Auth->user('id');

				if (isset($postElements['area_id']) && !empty($postElements['area_id']) && $postElements['area_id'] > 0) {

					// check action, cut = Only update area_id, copy = create new element with reference of area_id
					if (isset($postElementAction) && !empty($postElementAction)) {

						// CREATE A NEW ELEMENT
						if ($postElementAction == 'copy' || $postElementAction == 'copy_to') {

							$allData = [
								'Element' => null,
								'Links' => null,
								'Documents' => null,
								'Notes' => null,
							];

							// GET SELECTED ELEMENT ROW
							$row = $this->Element->find('first', [
								'conditions' => [
									'Element.id' => $element_id,
								],
								'recursive' => 1,
							]);

							$target_wsp_id = area_workspace_id($postElements['area_id']);
							$target_operation = $this->restrict_copy_paste($target_wsp_id,$postElements['area_id'],$element_id);

							if(!empty($target_operation['success'])) {
								unset($row['Area']);
								$row['Element']['area_id'] = $postElements['area_id'];


								$max_order = task_max_sort_order($postElements['area_id']);
								$row['Element']['sort_order'] = $max_order;
								unset($row['Element']['id']);

								/* if( FUTURE_DATE == 'on' ){
									$taget_wsp_detail = $this->Workspace->findById($target_wsp_id);
									$row['Element']['start_date']= $taget_wsp_detail['Workspace']['start_date'];
									$row['Element']['end_date']= $taget_wsp_detail['Workspace']['end_date'];
								} */
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
									$nPermit = arraySearch($row['Permissions'], 'user_id', $user_id, false);
								}

								if (isset($nPermit) && !empty($nPermit)) {

								}else{
								$allData['Element']['Element']['created_by'] = $this->Auth->user('id');
								}

								$Common = $this->objView->loadHelper('Common');
								$Group = $this->objView->loadHelper('Group');
								$project_level = 0;

								$p_permission = $Common->project_permission_details(element_project($element_id), $this->Session->read('Auth.User.id'));

								$user_project = $Common->userproject(element_project($element_id), $this->Session->read('Auth.User.id'));

								$grp_id = $Group->GroupIDbyUserID(element_project($element_id), $this->Session->read('Auth.User.id'));

								if (isset($grp_id) && !empty($grp_id)) {

									$group_permission = $Group->group_permission_details(element_project($element_id), $grp_id);
									if (isset($group_permission['ProjectPermission']['project_level']) && $group_permission['ProjectPermission']['project_level'] == 1) {
										$project_level = $group_permission['ProjectPermission']['project_level'];
									}

								}

								if ((isset($user_project) && !empty($user_project)) || (isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level'] == 1) || (isset($project_level) && $project_level == 1)) {

								}

								$area_id = $row['Element']['area_id'];
								// NOW SAVE IT TO DATABASE
								// if( $this->Element->save($newRow) ) {
								if ($this->Element->saveAll($allData, array(
									'deep' => true,
								))) {

									$insert_id = $this->Element->getLastInsertId();
									//added by pawan
									$eleWorkspaceId = element_workspace($insert_id);

									$this->loadModel('ElementPermission');
									if (isset($nPermit) && !empty($nPermit)) {
										$arr['ElementPermission']['user_id'] = $user_id;
										$arr['ElementPermission']['element_id'] = $insert_id;
										$arr['ElementPermission']['project_id'] = $postprojectid;
										$arr['ElementPermission']['workspace_id'] = $eleWorkspaceId;
										$arr['ElementPermission']['permit_read'] = (!empty($nPermit)) ? $nPermit[0]['permit_read'] : 0;
										$arr['ElementPermission']['permit_add'] = (!empty($nPermit)) ? $nPermit[0]['permit_add'] : 0;
										$arr['ElementPermission']['permit_edit'] = (!empty($nPermit)) ? $nPermit[0]['permit_edit'] : 0;
										$arr['ElementPermission']['permit_delete'] = (!empty($nPermit)) ? $nPermit[0]['permit_delete'] : 0;
										$arr['ElementPermission']['permit_copy'] = (!empty($nPermit)) ? $nPermit[0]['permit_copy'] : 0;
										$arr['ElementPermission']['permit_move'] = (!empty($nPermit)) ? $nPermit[0]['permit_move'] : 0;
										$arr['ElementPermission']['is_editable'] = (!empty($nPermit)) ? $nPermit[0]['is_editable'] : 0;
									} else {
										$arr['ElementPermission']['user_id'] = $user_id;
										$arr['ElementPermission']['element_id'] = $insert_id;
										$arr['ElementPermission']['project_id'] = $postprojectid;
										$arr['ElementPermission']['workspace_id'] = $eleWorkspaceId;
										$arr['ElementPermission']['permit_read'] = 1;
										$arr['ElementPermission']['permit_add'] = 1;
										$arr['ElementPermission']['permit_edit'] = 1;
										$arr['ElementPermission']['permit_delete'] = 1;
										$arr['ElementPermission']['permit_copy'] = 1;
										$arr['ElementPermission']['permit_move'] = 1;
										$arr['ElementPermission']['is_editable'] = 1;
									}
									$this->ElementPermission->save($arr);

									$elementPermissionInsert_id = $this->ElementPermission->getLastInsertId();

									// Get Project Id with Element id; Update Project modified date
									// $this->update_project_modify($element_id);

									// Now copy all the documents of the selected element to the target element
									$task_response = $this->cut_copy_paste_docs($element_id, $insert_id, $postElementAction);

									$success = true;
									$response['success'] = true;
									$response['msg'] = $insert_id;
								}
							}
							else{
								$success = false;
								$response['success'] = false;
								$response['error'] = $target_operation['message'];
							}
						} else if ($postElementAction == 'cut' || $postElementAction == 'drag_drop' || $postElementAction == 'move_to') {

							$target_wsp_id = area_workspace_id($postElements['area_id']);
							$target_operation = $this->restrict_copy_paste($target_wsp_id,$postElements['area_id']);

							if(!empty($target_operation['success'])) {
								// UPDATE AREA ID, IF CUT AND DRAG-DROP PERFORMED
								$this->Element->id = $element_id;

								$old_ele_pid = element_project($element_id);
								$old_ele_wspid = element_workspace($element_id);

								// Get total elements in target area
								$max_order = task_max_sort_order($postElements['area_id']);

								if ($this->Element->save(['area_id' => $postElements['area_id'],'created_by' => $this->user_id, 'sort_order' => ($max_order)])) {
									if ($postElementAction == 'move_to') {

										if ($this->ElementPermission->deleteAll(['ElementPermission.element_id'=> $element_id])) {
											$ele_pid = element_project($element_id);
											$ele_wspid = element_workspace($element_id);
											$eparr['ElementPermission']['user_id'] = $this->user_id;
											$eparr['ElementPermission']['element_id'] = $element_id;
											$eparr['ElementPermission']['project_id'] = element_project($element_id);
											$eparr['ElementPermission']['workspace_id'] = element_workspace($element_id);
											$eparr['ElementPermission']['permit_read'] = 1;
											$eparr['ElementPermission']['permit_add'] = 1;
											$eparr['ElementPermission']['permit_edit'] = 1;
											$eparr['ElementPermission']['permit_delete'] = 1;
											$eparr['ElementPermission']['permit_copy'] = 1;
											$eparr['ElementPermission']['permit_move'] = 1;
											$eparr['ElementPermission']['is_editable'] = 1;
											$this->ElementPermission->save($eparr);

											$elementPermissionInsert_id = $this->ElementPermission->getLastInsertId();


										}
									}

									// Get Project Id with Element id; Update Project modified date
									// $this->update_project_modify($element_id);

									$success = true;
								}
							}
							else{
								$success = false;
								$response['success'] = false;
								$response['error'] = $target_operation['message'];
							}

						} else {
							$success = false; // ERROR!!!
							$response['msg'] = 'No action provided.';
						}

						// AFTER ALL DONE
						if ($success == true) {

							// sort elements of passed areas
							if (isset($postSortArea) && !empty($postSortArea)) {
								// $key = array_search('0', $array); // $key = 2;
								// $area_ids = $postSortArea;
								// $this->reset_orders($area_ids);
							}

							$elements_details = null;

							// GET ALL AREAS AND ELEMENTS OF THE CURRENT WORKSPACE
							if (isset($postWorkspaceId) && !empty($postWorkspaceId)) {

								$workspace = $this->Area->find('all', [
									'conditions' => [
										'Area.workspace_id' => $postWorkspaceId,
									],
									'fields' => [
										'Area.id',
									],
									'recursive' => -1,
								]);
								$areaID = Set::extract($workspace, '/Area/id');

								$e_permission = $this->Common->element_permission_details($postWorkspaceId, $postprojectid, $user_id);

								$p_permission = $this->Common->project_permission_details($postprojectid, $user_id);

								/* -----------Group code----------- */
								$projectsg = $this->UserProject->find('first', ['recursive' => 2, 'conditions' => ['UserProject.project_id' => $postprojectid]]);
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
									$group_permission = $this->Group->group_permission_details($postprojectid, $projects_group_shared_user['ProjectGroupUser']['project_group_id']);

									$pll_level = $group_permission['ProjectPermission']['project_level'];

									$this->set('gpid', $projects_group_shared_user['ProjectGroupUser']['project_group_id']);

									$e_permission = $this->Group->group_element_permission_details($postWorkspaceId, $postprojectid, $projects_group_shared_user['ProjectGroupUser']['project_group_id']);

									$p_permission = $this->Group->group_permission_details($postprojectid, $projects_group_shared_user['ProjectGroupUser']['project_group_id']);

									if (isset($pll_level) && $pll_level == 1) {
										$this->set('project_level', 1);
									}
								}
								/* -----------Group code----------- */

								//group_element_permission_details

								$user_project = $this->Common->userproject($postprojectid, $user_id);

								foreach ($areaID as $k => $area_id) {

									if (isset($e_permission) && !empty($e_permission)) {

										$cond = [
											'Element.area_id' => $area_id,
											'Element.id' => $e_permission,
										];
									} else if (((isset($user_project) && !empty($user_project)) || (isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level'] == 1))) {

										$cond = [
											'Element.area_id' => $area_id,
										];
									}
									$elements_details_1 = $elements = null;
									$elements = $this->Element->find('all', [
										'conditions' => $cond,
										'fields' => [
											'Element.*',
										],
										'recursive' => -1,
										'order' => [
											'sort_order ASC',
										],
									]);

								}
							}
							$elements_details = $this->update_element_detail($this->request->data['workspace_id']);
							$response['success'] = true;
							//pr($elements_details, 1);

							$response['content'] = (isset($elements_details) && !empty($elements_details)) ? $elements_details : null;
						} else {
							$response['msg'] = 'Anything wrong in process.';
						}
					}
				}
			}

			echo json_encode($response);
			exit();
		}
	}

	/**
	 * Update background color code of an element in workspace->areas page
	 *
	 * @param
	 *        	$element_id
	 *
	 * @return void
	 */
	public function update_color($element_id = null) {
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

				$this->Element->id = $element_id;
				if ($this->Element->saveField('color_code', $post['color_code'])) {

					// Get Project Id with Element id; Update Project modified date
					// $this->update_project_modify($element_id);
					$response['content'] = $this->getWorkspaceData($element_id);
					$response['success'] = true;
					$response['msg'] = "Success";
				} else {
					$response['msg'] = "Error!!!";
				}
			}

			echo json_encode($response);
			exit();
		}
	}

	function getWorkspaceData($element_id = null) {
		$user_id = $this->Session->read('Auth.User.id');
		$project_id = element_project($element_id);
		$workspace_id = element_workspace($element_id);

		$wsp_permission = $this->objView->loadHelper('Common')->wsp_permission_details($this->objView->loadHelper('ViewModel')->workspace_pwid($workspace_id), $project_id, $user_id);

		$p_permission = $this->objView->loadHelper('Common')->project_permission_details($project_id, $user_id);

		$user_project = $this->objView->loadHelper('Common')->userproject($project_id, $user_id);

		$grp_id = $this->objView->loadHelper('Group')->GroupIDbyUserID($project_id, $user_id);

		$e_permission = $this->objView->loadHelper('Common')->element_permission_details($workspace_id, $project_id, $user_id);
		$project_level = 0;
		if ((isset($grp_id) && !empty($grp_id))) {
			$group_permission = $this->objView->loadHelper('Group')->group_permission_details($project_id, $grp_id);
			if (isset($group_permission['ProjectPermission']['project_level']) && $group_permission['ProjectPermission']['project_level'] == 1) {
				$project_level = $group_permission['ProjectPermission']['project_level'];
			}

			if (isset($e_permission) && !empty($e_permission)) {
				$e_permissions = $this->objView->loadHelper('Group')->group_element_permission_details($workspace_id, $project_id, $grp_id);
				$e_permission = array_merge($e_permission, $e_permissions);
			} else {
				$e_permission = $this->objView->loadHelper('Group')->group_element_permission_details($workspace_id, $project_id, $grp_id);
			}
		}

		$areaElements = null;
		$areas = get_workspace_areas($workspace_id, false);
		// pr($areas, 1);
		if (isset($areas) && !empty($areas)) {
			foreach ($areas as $k => $v) {

				$elements_details_temp = null;
				if ((isset($e_permission) && !empty($e_permission))) {
					$all_elements = $this->objView->loadHelper('ViewModel')->area_elements_permissions($v['Area']['id'], false, $e_permission);
				}

				if (((isset($user_project) && !empty($user_project)) || (isset($project_level) && $project_level == 1) || (isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level'] == 1))) {
					$all_elements = $this->objView->loadHelper('ViewModel')->area_elements($v['Area']['id']);
				}

				if (isset($all_elements) && !empty($all_elements)) {

					foreach ($all_elements as $element_index => $e_data) {

						$element = $e_data['Element'];

						$element_decisions = $element_feedbacks = [];
						if (isset($element['studio_status']) && empty($element['studio_status'])) {
							$element_decisions = _element_decisions($element['id'], 'decision');
							$element_feedbacks = _element_decisions($element['id'], 'feedback');
							$element_statuses = _element_statuses($element['id']);

							$self_status['self_status'] = element_status($element['id']);

							$element_assets = element_assets($element['id'], true);
							$arraySearch = arraySearch($all_elements, 'id', $element['id']);

							if (isset($arraySearch) && !empty($arraySearch)) {
								$elements_details_temp[] = array_merge($arraySearch[0], $element_assets, $element_decisions, $element_feedbacks, $element_statuses, $self_status);
							}
						}
					}

					$areaElements[$v['Area']['id']]['el'] = $elements_details_temp;
				}
			}
		}
		return $areaElements;
	}

	public function get_workspace_template() {

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
				$statuses = [];
				if(isset($post['status']) && !empty($post['status'])) {
					$statuses = $post['status'];
				}
				$viewData['statuses'] = $statuses;

				$project_id = $viewData['project_id'] = $post['project_id'];
				$workspace_id = $viewData['workspace_id'] = $post['workspace_id'];

				$workspace = getByDbId('Workspace', $workspace_id);
				$viewData['data']['workspace'] = $workspace;
				$this->loadModel('TemplateDetail');
				$template_groups = $this->TemplateDetail->find('all', array(
					'fields' => 'DISTINCT row_no, id',
					'conditions' => array(
						'TemplateDetail.template_id' => $workspace['Workspace']['template_id'],
					),
				));

				$template_detail_id = Set::extract($template_groups, '/TemplateDetail/id');

				$area_template_data = $this->Workspace->Area->find('all', [
					'fields' => [
						'Area.id as area_id', 'Area.workspace_id', 'Area.title', 'Area.description as desc', 'Area.tooltip_text', 'Area.status',
						'TemplateDetail.id as temp_detail_id', 'TemplateDetail.row_no', 'TemplateDetail.col_no', 'TemplateDetail.size_w', 'TemplateDetail.size_h', 'TemplateDetail.elements_counter', 'TemplateDetail.template_id',
					],
					'conditions' => [
						'Area.workspace_id' => $workspace_id,
						'Area.template_detail_id' => $template_detail_id,
					],
					'recursive' => 2,
					'order' => ['TemplateDetail.id ASC', 'TemplateDetail.row_no ASC'],
				]);

				$templateRows = null;

				foreach ($area_template_data as $row_id => $row_templates) {
					$area_detail = $row_templates['Area'];
					$temp_detail = $row_templates['TemplateDetail'];

					/*$row_templates['Elements'] = $this->Element->find('all', array(
							'conditions' => array(
								'Element.area_id' => $row_templates['Area']['area_id'],
								$finalConditions,
							),
							'order' => ['Element.sort_order ASC'],
						)
					*/

					$elements = null;
					// $elements = $row_templates['Elements'];

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

				$viewData['data']['templateRows'] = $templateRows;

				$viewData['areas'] = $this->Area->find('all',
					[
						'conditions' =>
						[
							'Area.workspace_id' => $workspace_id,
						],
						'fields' => ['Area.id'],
						'recursive' => -1,
					]);
			}

			$this->set($viewData);
			$this->render('/Projects/partials/workspace_layout');
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
			$this->render('/Projects/partials/area_elements');

			// echo json_encode($html);
			// exit();

		}
	}

	/**
	 * Update background sort order of an element in workspace->areas page
	 *
	 * @param
	 *        	$element_id
	 *
	 * @return void
	 */
	public function update_sort_order($element_id = null) {
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
				$area_id = $post['Element']['area_id'];
				$this->Element->id = null;
				$error_data = null;

				if (isset($post) && !empty($post)) {

					if (isset($post['Element']['sort_order']) && !empty($post['Element']['sort_order'])) {
						$id = $post['Element']['id'];
						$area_id = $post['Element']['area_id'];
						$sort_order = $post['Element']['sort_order'];
						$direction = $post['direction'];
						$previous_element = $next_element = null;
						// find previous element
						if ($direction == 'up') {
							$neighbours = $this->Element->find('first', array(
								'conditions' => ['Element.sort_order <' => $sort_order, 'Element.area_id' => $area_id],
								'order' => 'Element.sort_order DESC',
								'fields' => ['id', 'sort_order'],
								'recursive' => -1,
								'limit' => 1,
							));
							// pr($neighbours, 1);

							$neighbour_sortorder = null;
							if (isset($neighbours) && !empty($neighbours)) {
								$neighbour_sortorder = $neighbours['Element']['sort_order'];
								// e($neighbour_sortorder);
								// get current element's data.
								$current_el = getByDbId('Element', $id);
								$current_sort_order = $current_el['Element']['sort_order'];
								// e($current_sort_order, 1);

								$this->Element->id = null;
								$this->Element->id = $neighbours['Element']['id'];
								if ($this->Element->saveField('sort_order', $current_sort_order)) {
									// $this->update_project_modify($id);
								}
								// update current
								$this->Element->id = $id;
								$this->Element->saveField('sort_order', $neighbour_sortorder);
							}
						}

						// find next element
						if ($direction == 'down') {
							$neighbours = $this->Element->find('first', array(
								'conditions' => ['Element.sort_order >' => $sort_order, 'Element.area_id' => $area_id],
								'order' => 'Element.sort_order ASC',
								'fields' => ['id', 'sort_order'],
								'recursive' => -1,
								'limit' => 1,
							));
							// pr($neighbours, 1);

							$neighbour_sortorder = null;
							if (isset($neighbours) && !empty($neighbours)) {
								$neighbour_sortorder = $neighbours['Element']['sort_order'];
								// e($neighbour_sortorder);
								// get current element's data.
								$current_el = getByDbId('Element', $id);
								$current_sort_order = $current_el['Element']['sort_order'];
								// e($current_sort_order, 1);

								$this->Element->id = null;
								$this->Element->id = $neighbours['Element']['id'];
								if ($this->Element->saveField('sort_order', $current_sort_order)) {
									// $this->update_project_modify($id);
								}
								// update current
								$this->Element->id = $id;
								$this->Element->saveField('sort_order', $neighbour_sortorder);
							}
						}

						// save current
						/*$this->Element->id = null;
						$this->Element->id = $id;
						$this_data['sort_order'] = $sort_order;
						if ($this->Element->saveField('sort_order', $this_data['sort_order'])) {

							// Get Project Id with Element id; Update Project modified date
							$this->update_project_modify($id);
						} else {
							$error_data['current'] = true;
						}*/

						$eerror_count = (isset($error_data) && !empty($error_data)) ? count($error_data) : 0;

						if ($eerror_count <= 0) {

							$elements_details = $edata = null;
							$area_id = $post['Element']['area_id'];

							$this->loadModel("Area");
							$elements_data = $this->Area->find('first', [
								'conditions' => [
									'Area.id' => $area_id,
								],
								'recursive' => 1,
							]);

							$workspace_id = Set::extract($elements_data, '/Area/workspace_id');
							$elements_details = $this->update_element_detail($workspace_id);

							$response['success'] = true;
							$response['msg'] = "Success";
							$response['content']['elements_details'] = (isset($elements_details) && !empty($elements_details)) ? $elements_details : [];
							// // pr($elements_details, 1);
						}
						// // pr($error_data, 1);
					} else {
						// ERROR;
						die("ERROR");
					}
				}
			}

			echo json_encode($response);
			exit();
		}
	}

	public function update_sort_order_old($element_id = null, $direction = 'up') {
		if ($this->request->isAjax()) {

			$response = [
				'success' => false,
				'msg' => '',
				'content' => null,
			];

			$this->layout = 'ajax';
			$this->autoRender = false;

			if ($this->request->is('post') || $this->request->is('put')) {
				// $this->reset_orders();die;
				$post = $this->request->data;
				$errors = null;

				if (isset($direction) && !empty($direction)) {
					// start increase
					if ($direction == 'up') {

						$total_elements = $this->Element->find('count', [
							'conditions' => [
								'Element.area_id' => $post['Element']['area_id'],
							],
						]);

						$current = $this->Element->findById($element_id, [
							'fields' => 'sort_order',
						]);

						if (!is_null($current) && $total_elements > 0) {

							$current_sort_order = $current['Element']['sort_order'];

							// sort order can't be greater than total elements in passed area
							if ($current_sort_order < $total_elements) {

								$next = $this->Element->find('first', [
									'conditions' => [
										'sort_order >' => $current_sort_order,
										'area_id' => $post['Element']['area_id'],
									],
									'fields' => [
										'id',
										'sort_order',
									],
								]);
								$next_sort_order = (isset($next['Element']['sort_order'])) ? $next['Element']['sort_order'] : 0;

								// update current
								$this->Element->id = $element_id;
								$this->Element->saveField('sort_order', $next_sort_order);

								// update next
								$this->Element->id = null;
								$this->Element->id = (isset($next['Element']['id'])) ? $next['Element']['id'] : 0;
								$this->Element->saveField('sort_order', $current_sort_order);
							}
						} // end is_null( $current .....
					}
					// end increase
					// start decrease
					if ($direction == 'down') {
						$current = $this->Element->findById($element_id, [
							'fields' => 'sort_order',
						]);

						if (!is_null($current)) {
							// Check current element db-data
							$current_sort_order = $current['Element']['sort_order'];

							if ($current_sort_order > 1) {
								// sort order can't be less than 1
								$prev = $this->Element->find('first', [
									'conditions' => [
										'sort_order <' => $current_sort_order,
										'area_id' => $post['Element']['area_id'],
									],
									'fields' => [
										'id',
										'sort_order',
									],
								]);
								$prev_sort_order = $prev['Element']['sort_order'];

								// update current
								$this->Element->id = $element_id;
								$this->Element->saveField('sort_order', $prev_sort_order);

								// update previous
								$this->Element->id = null;
								$this->Element->id = $prev['Element']['id'];
								$this->Element->saveField('sort_order', $current_sort_order);
							}
						}
					}
					// end decrease

					$elements_details = $edata = null;
					$area_id = $post['Element']['area_id'];

					$this->loadModel("Area");
					$elements_data = $this->Area->find('first', [
						'conditions' => [
							'Area.id' => $area_id,
						],
						'recursive' => 1,
					]);

					if (isset($elements_data) && !empty($elements_data)) {
						/*
							                         * foreach( $elements_data['Elements'] as $key => $val ) {
							                         * $edata[] = $val;
							                         * }
							                         * $elements_details = $edata;
						*/
					}
					$workspace_id = Set::extract($elements_data, '/Area/workspace_id');
					$elements_details = $this->update_element_detail($workspace_id);

					$response['success'] = true;
					$response['msg'] = "Success";
					$response['content']['elements_details'] = (isset($elements_details) && !empty($elements_details)) ? $elements_details : [];
				} else {
					$response['msg'] = "Error!!!";
				}
			}

			echo json_encode($response);
			exit();
		}
	}

	public function update_element_detail($workspace_id = null) {

		return array();

		if (empty($workspace_id)) {
			return null;
		}

		$workspace = $this->Area->find('all', [
			'conditions' => [
				'Area.workspace_id' => $workspace_id,
			],
			'fields' => [
				'Area.id',
			],
			'recursive' => -1,
		]);
		$areaID = Set::extract($workspace, '/Area/id');

		$elements_details = null;

		$view = new View();
		$viewmodel = $view->loadHelper('ViewModel');
		$common = $view->loadHelper('Common');
		$group = $view->loadHelper('Group');

		$p_permission = $common->project_permission_details(workspace_pid($workspace_id), $this->user_id);

		$user_project = $common->userproject(workspace_pid($workspace_id), $this->user_id);

		$e_permission = $common->element_permission_details($workspace_id, workspace_pid($workspace_id), $this->user_id);

		/* -----------Group code----------- */
		$projectsg = $this->UserProject->find('first', ['recursive' => 2, 'conditions' => ['UserProject.project_id' => workspace_pid($workspace_id)]]);
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
			$group_permission = $this->Group->group_permission_details(workspace_pid($workspace_id), $projects_group_shared_user['ProjectGroupUser']['project_group_id']);

			$p_permission = $group->group_permission_details(workspace_pid($workspace_id), $projects_group_shared_user['ProjectGroupUser']['project_group_id']);

			$e_permission = $group->group_element_permission_details($workspace_id, workspace_pid($workspace_id), $projects_group_shared_user['ProjectGroupUser']['project_group_id']);

			$pll_level = $group_permission['ProjectPermission']['project_level'];

			$this->set('gpid', $projects_group_shared_user['ProjectGroupUser']['project_group_id']);

			if (isset($pll_level) && $pll_level == 1) {
				$this->set('project_level', 1);
			}
		}
		/* -----------Group code----------- */

		foreach ($areaID as $k => $area_id) {
			$elements_details_1 = null;

			if (((isset($user_project) && !empty($user_project)) || (isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level'] == 1))) {
				$elements = $this->Element->find('all', [
					'conditions' => [
						'Element.area_id' => $area_id,
					],
					'fields' => [
						'Element.*',
					],
					'recursive' => -1,
					'order' => [
						'sort_order ASC',
					],
				]);
			} else {
				$elements = $this->Element->find('all', [
					'conditions' => [
						'Element.area_id' => $area_id,
						'Element.id' => $e_permission,
					],
					'fields' => [
						'Element.*',
					],
					'recursive' => -1,
					'order' => [
						'sort_order ASC',
					],
				]);
			}
			$elements = $viewmodel->element_permission_data($elements, $area_id);

			if (!is_null($elements)) {

				$elements_details_1 = _element_detail(null, $elements);

				if (!is_null($elements_details_1)) {
					$elements_details[$area_id]['el'] = $elements_details_1;
				}

				/*
					                 * foreach( $elements as $element_index => $element_detail ) {
					                 * $element_decisions = _element_decisions($element_detail['Element']['id'], 'decision');
					                 * $element_feedbacks = _element_decisions($element_detail['Element']['id'], 'feedback');
					                 * $element_statuses = _element_statuses($element_detail['Element']['id']);
					                 * $element_assets = element_assets($element_detail['Element']['id'], true);
					                 *
					                 * $arraySearch = arraySearch($elements, 'id', $element_detail['Element']['id']);
					                 *
					                 * if( isset($arraySearch) && !empty($arraySearch)) {
					                 * $elements_details_1[] = array_merge($arraySearch[0], $element_assets, $element_feedbacks, $element_decisions, $element_statuses);
					                 * }
					                 * }
					                 * if( !is_null($elements_details_1) )
					                 * $elements_details[$area_id]['el'] = $elements_details_1;
				*/
			}
		}
		//pr($elements_details, 1);
		return $elements_details;
	}

	public function area_elements_detail($area_id = null) {

		if (empty($area_id)) {
			return null;
		}

		$elements_details = null;

		$elements_details_1 = null;
		$elements = $this->Element->find('all', [
			'conditions' => [
				'Element.area_id' => $area_id,
			],
			'fields' => [
				'Element.*',
			],
			'recursive' => -1,
			'order' => [
				'sort_order ASC',
			],
		]);

		if (!is_null($elements)) {

			$elements_details_1 = _element_detail(null, $elements);

			if (!is_null($elements_details_1)) {
				$elements_details[$area_id]['el'] = $elements_details_1;
			}
		}

		return $elements_details;
	}

	/**
	 * Update status of an element in workspace->areas page
	 *
	 * @param
	 *        	$element_id
	 *
	 * @return void
	 */
	public function update_status($element_id = null) {
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

				$this->Element->id = $element_id;
				if ($this->Element->saveField('status', $post['status'])) {

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

	public function get_popup($area_id = null, $workspace_id = null) {
		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			if(isset($area_id) && !empty($area_id)){
				$query = "SELECT a.workspace_id FROM areas a WHERE a.id = $area_id";
				$data = $this->Element->query($query);
				$workspace_id = $data[0]['a']['workspace_id'];
			}

			$query = "SELECT a.id, a.title FROM areas a WHERE a.workspace_id = $workspace_id";
			$data = $this->Element->query($query);
			$area_list = Set::combine($data, '{n}.a.id', '{n}.a.title');

			// element_project($element_id);
			// element_workspace($defaultElementid);
			$project_id = workspace_pid($workspace_id);

			$query = "SELECT Project.start_date, Project.end_date FROM projects Project WHERE Project.id = $project_id";
			$pdata = $this->Element->query($query);

			$this->set('area_list', $area_list);
			$this->set('area_id', $area_id);
			$this->set('project_id', $project_id);
			$this->set('workspace_id', $workspace_id);
			$this->set('pdata', $pdata);

			$this->render('/Entities/partials/get_popup');
		}
	}

	public function reset_orders($areas = null) {
		$area_ids = null;

		if (isset($areas) && !empty($areas)) {
			$area_ids = $areas;
		} else {
			$all = $this->Area->query("SELECT id
								FROM `areas`
								WHERE
									workspace_id IN
									(SELECT workspace_id
									FROM `project_workspaces`
									WHERE
									project_id IN
										(SELECT project_id
										FROM `user_projects`
										WHERE
										user_id = '" . $this->user_id . "'
										)
									)
						");

			// // pr( $all, 1);
			$area_ids = $q = $elements = null;
			foreach ($all as $k => $v) {
				$area_ids[] = $v['areas']['id'];
			}

			// $area_ids = Set::extract ( $all, 'Area/id' );
		}
		$temp_data = [];
		if (isset($area_ids) && !empty($area_ids)) {
			// // pr( $area_ids, 1);
			//
			foreach ($area_ids as $k => $v) {
				// e($v);
				$elements = null;
				$elements = $this->Element->find('all', [
					'conditions' => [
						'Element.area_id' => $v,
					],
					'fields' => [
						'Element.id',
					],
					'order' => [
						'sort_order ASC',
					],
				]);
				$inc = 1;
				foreach ($elements as $k1 => $v1) {
					$eid = $v1['Element']['id'];

					$temp_data[$v][$eid] = $inc;
					$this->Element->id = $v1['Element']['id'];
					if ($this->Element->saveField('sort_order', $inc)) {
						// $q[$v] = $this->Element->_query();
					}
					$inc = $inc + 1;
				}

				// $area_ids[$v['Element']['area_id']][$v['Element']['id']] = $v['Element']['id'];
			}
			// return;
		}
		// // pr($temp_data );
	}

	/**
	 * Add a notes to the element
	 *
	 * @param
	 *        	$element_id
	 *
	 * @return void
	 */
	public function add_mindmap($element_id = null, $mm_id = null) {
		$user_id = $this->Auth->user('id');
		if ($this->request->isAjax()) {
			$response = [
				'success' => false,
				'action' => null,
				'content' => null,
			];

			$this->layout = 'ajax';
			$this->autoRender = false;

			if ($this->request->is('post') || $this->request->is('put')) {
				// // pr($this->request->data, 1);die;
				$id = null;
				$action = '';
				$this->ElementMindmap->set($this->request->data['ElementMindmap']);
				if (empty($this->request->data['ElementMindmap']['id'])) {
					$this->request->data['ElementMindmap']['creater_id'] = $this->Auth->user('id');
				}

				$this->request->data['ElementMindmap']['updated_user_id'] = $this->Auth->user('id');

				if ($this->ElementMindmap->validates()) {
					// // pr($this->request->data, 1);
					$this->request->data['ElementMindmap']['user_id'] = $user_id;

					if ($this->ElementMindmap->save($this->request->data['ElementMindmap'])) {

						if (isset($this->request->data['ElementMindmap']['id']) && !empty($this->request->data['ElementMindmap']['id'])) {
							$id = $this->request->data['ElementMindmap']['id'];
							$this->ElementMindmap->updateAll(
								array("ElementMindmap.create_activity" => 0),
								array("ElementMindmap.id" => $id)
							);
							$action = 'update';
						} else {
							$id = $this->ElementMindmap->getLastInsertId();
							$this->ElementMindmap->updateAll(
								array("ElementMindmap.create_activity" => 0),
								array("ElementMindmap.id" => $id)
							);
							$this->ElementMindmap->query("update element_mindmaps SET parent_id=$id WHERE id=$id");

							$action = 'create';
						}

						// Get Project Id with Element id; Update Project modified date
						// $this->update_project_modify($element_id);
						// Update elemnt task activity.
						// $this->update_task_activity($element_id);

						$this->request->data['Element']['id'] = $element_id;
						$this->request->data['Element']['updated_user_id'] = $this->Auth->user('id');
						// $this->Element->save($this->request->data['Element']);

						$element_mindmap = null;
						if (isset($id) && !empty($id)) {
							$element_mindmap = $this->ElementMindmap->find('first', [
								'conditions' => [
									'ElementMindmap.id' => $id,
								],
							]);

							if (!is_null($element_mindmap)) {
								//$element_mindmap['ElementMindmap']['created'] = _displayDate(/$element_mindmap['ElementMindmap']['created']);
								//$element_mindmap['ElementMindmap']['modified'] = _displayDate(//$element_mindmap['ElementMindmap']['modified']);

								$element_mindmap['ElementMindmap']['created'] = $this->objView->loadHelper('Wiki')->_displayDate(date('Y-m-d h:i:s A', strtotime($element_mindmap['ElementMindmap']['created'])), $format = 'd M, Y g:iA');

								// $element_note['ElementNote']['modified'] = dateFormat($element_note['ElementNote']['modified']);

								$element_mindmap['ElementMindmap']['modified'] = $this->objView->loadHelper('Wiki')->_displayDate(date('Y-m-d h:i:s A', strtotime($element_mindmap['ElementMindmap']['modified'])), $format = 'd M, Y g:iA');


							}
						}

						$creator_id = $element_mindmap['ElementMindmap']['creater_id'];

						$element_mindmap['ElementMindmap']['creator'] = $this->Common->elementMM_creator($id, $this->request->data['ElementMindmap']['project_id'], $creator_id);

						$response['success'] = true;
						$response['action'] = $action;
						$response['content'] = $element_mindmap;
						$response['session_id'] = $this->Session->id();
						$response['mmtime'] = $this->Session->read('Auth.User.mm_time');
					}
				} else {
					$response['content'] = $this->validateErrors($this->ElementMindmap);
				}
			}

			echo json_encode($response);
			exit();
		}
	}

	/**
	 *
	 * @param
	 *        	$mindmap_id
	 *
	 * @return void
	 */
	public function remove_mindmap($element_id = null, $mind_map_id = null) {
		if ($this->request->is('ajax')) {

			$response = [
				'success' => false,
				'msg' => '',
				'content' => null,
			];

			$this->layout = 'ajax';
			$this->autoRender = false;

			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;
				// // pr($post, 1);die;

				if (isset($post['mind_map_id']) && !empty($post['mind_map_id'])) {
					$id = $post['mind_map_id'];
					$this->ElementMindmap->id = $id;
					if (!$this->ElementMindmap->exists()) {
						throw new NotFoundException(__('Invalid detail'), 'error');
					}

					$dids = $this->ElementMindmap->findById($id);


					if ($this->ElementMindmap->delete()) {
						$this->request->data['Element']['id'] = $element_id;
						$this->request->data['Element']['updated_user_id'] = $this->Auth->user('id');

						$defaultElementid = $dids['ElementMindmap']['element_id'];

						$task_data = [
						'project_id' => element_project($defaultElementid),
						'workspace_id' => element_workspace($defaultElementid),
						'element_id' => $defaultElementid,
						'element_type' => 'element_mindmaps',
						'user_id' => $this->user_id,
						'relation_id' => $dids['ElementMindmap']['id'],
						'updated_user_id' => $this->user_id,
						'message' => 'Mind Map deleted',
						'updated' => date("Y-m-d H:i:s"),
						];
						$this->loadModel('Activity');
						$this->Activity->id = null;
						$this->Activity->save($task_data);
						// $this->Element->save($this->request->data['Element']);
						// Get Project Id with Element id; Update Project modified date
						// $this->update_project_modify($element_id);
						// $this->update_task_activity($element_id);
						$response['success'] = true;
						$response['msg'] = 'Mindmap has been deleted successfully.';
						$response['content'] = [];
					} else {
						$response['msg'] = 'Mindmap could not deleted successfully.';
					}
					// $this->Element->_query(1);
				}
			}

			echo json_encode($response);
			exit();
		}
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

	/*     * ******* Voting  ************* */

	public function add_vote($element_id = null) {
		if ($this->request->isAjax()) {

			$response = [
				'success' => false,
				'action' => null,
				'content' => null,
			];

			$view = new View();
			$ViewModel = $view->loadHelper('ViewModel');

			$this->layout = 'ajax';
			$this->autoRender = false;
			$this->loadModel('Vote');
			$this->loadModel('VoteQuestion');
			$this->loadModel('VoteQuestionOption');
			$this->loadModel('VoteUser');
			if ($this->request->is('post') || $this->request->is('put')) {
				//	 // pr($this->request->data, 1);
				$id = null;
				$action = '';
				if (isset($this->request->data['Vote']) && !empty($this->request->data['Vote'])) {
					$this->Vote->set($this->request->data['Vote']);

					$project_id = $this->request->data['Vote']['project_id'];
					$workspace_id = $this->request->data['Vote']['workspace_id'];
					$element_id = $this->request->data['Vote']['element_id'];
					$dates = $this->get_s_e_date($project_id, $workspace_id, $element_id);

					if (isset($dates['mindate']) && !empty($this->request->data['Vote']['start_date']) && strtotime($this->request->data['Vote']['start_date']) < strtotime($dates['mindate'])) {
						$this->Vote->validationErrors['start_date'][0] = 'Invalid date selection.';
					}
					if (isset($dates['maxdate']) && !empty($this->request->data['Vote']['end_date']) && strtotime($this->request->data['Vote']['end_date'] > $dates['maxdate'])) {
						$this->Vote->validationErrors['end_date'][0] = 'Invalid date selection.';
					}

					if(isset($this->request->data['Vote']['end_date']) && !empty($this->request->data['Vote']['end_date']) && $this->request->data['Vote']['end_date'] < $this->request->data['Vote']['start_date']){

							$this->request->data['Vote']['start_date'] = $this->request->data['Vote']['end_date'];
						}

					if ($this->Vote->validates()) {
						$this->request->data['Vote']['user_id'] = $this->Auth->User('id');
						if (isset($this->request->data['Vote']['start_date'])) {
							$this->request->data['Vote']['start_date'] = date('Y-m-d HH:ii:ss', strtotime($this->request->data['Vote']['start_date']));
						}

						if (isset($this->request->data['Vote']['end_date'])) {
							$this->request->data['Vote']['end_date'] = date('Y-m-d HH:ii:ss', strtotime($this->request->data['Vote']['end_date']));
						}

						$this->request->data['Vote']['user_id'] = $this->Auth->User('id');
						$this->request->data['Vote']['updated_user_id'] = $this->Auth->user('id');

						$this->request->data['Vote']['create_activity'] = true;
						if ($this->Vote->save($this->request->data['Vote'])) {
							if (isset($this->request->data['Vote']['id']) && !empty($this->request->data['Vote']['id'])) {
								$id = $this->request->data['Vote']['id'];
								$action = 'update';
							} else {
								$id = $this->Vote->getLastInsertId();
								$action = 'create';
							}

							$this->Vote->updateAll(
								array("Vote.create_activity" => 0),
								array("Vote.id" => $id)
							);

							// Get Project Id with Element id; Update Project modified date
							// $this->update_project_modify($element_id);
							// Update elemnt task activity.
							// $this->update_task_activity($element_id);

							$this->request->data['Element']['id'] = $element_id;
							$this->request->data['Element']['updated_user_id'] = $this->Auth->user('id');
							// $this->Element->save($this->request->data['Element']);

							$element_note = null;
							if (isset($id) && !empty($id)) {
								$element_note = $this->Vote->find('first', [
									'conditions' => [
										'Vote.id' => $id,
									],
								]);

								if (!is_null($element_note)) {
									// $element_note['ElementNote']['created'] = dateFormat($element_note['ElementNote']['created']);
									$element_note['Vote']['created'] = date('d M, Y g:iA', strtotime($element_note['Vote']['created']));

									// $element_note['ElementNote']['modified'] = dateFormat($element_note['ElementNote']['modified']);

									$element_note['Vote']['modified'] = date('d M, Y g:iA', strtotime($element_note['Vote']['modified']));
								}
							}

							$response['success'] = true;
							$response['step'] = 1;
							$response['action'] = $action;
							$response['content'] = $element_note;
						}
					} else {
						$response['content'] = $this->Vote->validationErrors;
					}
				} else if (isset($this->request->data['VoteQuestion']) && !empty($this->request->data['VoteQuestion'])) {

					$this->VoteQuestion->set($this->request->data['VoteQuestion']);
					if (isset($this->request->data['VoteQuestionOption'])) {
						$this->VoteQuestionOption->set($this->request->data['VoteQuestionOption']);
					}

					if ($this->VoteQuestion->validates() && $this->VoteQuestionOption->validates()) {

						$vote_type_id = $this->request->data['VoteQuestion']['vote_type_id'];

						$vote_id = $this->request->data['VoteQuestion']['vote_id'];
						if (isset($vote_id) && !empty($vote_id)) {
							$this->VoteQuestion->deleteAll(array('VoteQuestion.vote_id' => $vote_id));
						}

						if ($this->VoteQuestion->save($this->request->data['VoteQuestion'])) {

							if (empty($this->request->data['VoteQuestion']['id'])) {
								$QuserId = $this->VoteQuestion->getLastInsertID();
								$this->request->data['VoteQuestion']['id'] = $QuserId;
							}
							if (isset($this->request->data['VoteQuestion']['distributed_count']) && !empty($this->request->data['VoteQuestion']['distributed_count'])) {
								$this->VoteQuestion->updateAll(array('VoteQuestion.distributed_count' => $this->request->data['VoteQuestion']['distributed_count']), array('VoteQuestion.id' => $this->request->data['VoteQuestion']['id']));
							}

							// Get Project Id with Element id; Update Project modified date
							// $this->update_project_modify($element_id);

							if (isset($this->request->data['VoteQuestion']['id']) && !empty($this->request->data['VoteQuestion']['id'])) {
								$id = $this->request->data['VoteQuestion']['id'];
								$action = 'update';
							} else {
								$id = $this->VoteQuestion->getLastInsertId();
								$action = 'create';
							}
							$element_note = null;
							$element_note = $this->VoteQuestion->find('first', [
								'conditions' => [
									'VoteQuestion.id' => $id,
								],
								'fields' => [
									'VoteQuestion.vote_id',
								],
							]);

							if (isset($this->request->data['VoteQuestionOption']['option'][$vote_type_id]) && !empty($this->request->data['VoteQuestionOption']['option'][$vote_type_id])) {
								if (isset($this->request->data['VoteQuestion']['id']) && !empty($this->request->data['VoteQuestion']['id'])) {
									$question_id = $this->request->data['VoteQuestion']['id'];
									$this->VoteQuestionOption->deleteAll(array('VoteQuestionOption.vote_question_id' => $question_id));
								}
								foreach ($this->request->data['VoteQuestionOption']['option'][$vote_type_id] as $val) {
									$this->VoteQuestionOption->create();
									$dataArr['VoteQuestionOption']['vote_question_id'] = $id;
									$dataArr['VoteQuestionOption']['option'] = $val;
									$this->VoteQuestionOption->save($dataArr, array('validate' => false));
								}
							}

							$response['step'] = 2;
							$response['success'] = true;
							$response['action'] = $action;
							$response['content'] = $element_note;
						}
					} else {
						$vqoErrors = $this->validateErrors($this->VoteQuestionOption);
						$vqErrors = $this->validateErrors($this->VoteQuestion);
						if (isset($vqErrors) && isset($vqoErrors) && !empty($vqErrors) && !empty($vqoErrors)) {
							$response['content'] = array_merge($vqErrors, $vqoErrors);
						} else if (isset($vqErrors) && !empty($vqErrors)) {
							$response['content'] = $vqErrors;
						} else if (isset($vqoErrors) && !empty($vqoErrors)) {
							$response['content'] = $vqoErrors;
						}
					}
				} else if (isset($this->request->data['VoteUser']) && !empty($this->request->data['VoteUser'])) {

					$this->VoteUser->set($this->request->data['VoteUser']);

					if (isset($this->request->data['Vote'])) {
						$project_id = $this->request->data['Vote']['project_id'];
						$workspace_id = $this->request->data['Vote']['workspace_id'];
						$element_id = $this->request->data['Vote']['element_id'];

						$dates = $this->get_s_e_date($project_id, $workspace_id, $element_id);
						$this->Feedback->validates();
						if (isset($dates['mindate']) && !empty($this->request->data['Vote']['start_date']) && strtotime($this->request->data['Vote']['start_date']) < strtotime($dates['mindate'])) {
							$this->Vote->validationErrors['start_date'][0] = 'Invalid date selection.';
						}
						if (isset($dates['maxdate']) && !empty($this->request->data['Vote']['end_date']) && strtotime($this->request->data['Vote']['end_date']) > strtotime($dates['maxdate'])) {
							$this->Vote->validationErrors['end_date'][0] = 'Invalid date selection.';
						}

					}

					if ($this->VoteUser->validates()) {
						$vote_id = $this->request->data['VoteUser']['vote_id'];
						if (isset($vote_id) && !empty($vote_id)) {
							$this->VoteUser->deleteAll(array('VoteUser.vote_id' => $vote_id));
						}
						$users = isset($this->request->data['VoteUser']['list']) ? $this->request->data['VoteUser']['list'] : array();
						$voteuserData['VoteUser']['vote_id'] = $vote_id;
						//// pr($this->request->data);die;
						$this->loadModel('Vote');
						$vote_id = $this->request->data['VoteUser']['vote_id'];
						$voteDetails = $this->Vote->find('first', array('conditions' => array('Vote.id' => $vote_id)));
						$voteTitle = $voteDetails['Vote']['title'];
						$vote_type_id = $voteDetails['VoteQuestion']['vote_type_id'];
						$vote_question_id = $voteDetails['VoteQuestion']['id'];
						// Get total options
						$this->loadModel('VoteQuestionOption');
						$vote_question_optioncount = $this->VoteQuestionOption->find('count', array('conditions' => array('VoteQuestionOption.vote_question_id' => $vote_question_id)));
						//echo $this->Auth->User('id');die;

						$ele_detail = $ViewModel->getElementDetails($element_id);

						$projectDetail = getByDbId('Project', $ele_detail[0]['user_permissions']['project_id'], 'title');
						$workspaceDetail = getByDbId('Workspace', $ele_detail[0]['user_permissions']['workspace_id'], 'title');
						$elementDetail = getByDbId('Element', $element_id, 'title');

						$projectName = ( isset($projectDetail) && !empty($projectDetail['Project']['title']) )? $projectDetail['Project']['title'] : '';
						$workspaceName = ( isset($workspaceDetail) && !empty($workspaceDetail['Workspace']['title']) )? $workspaceDetail['Workspace']['title'] : '';
						$elementName = ( isset($elementDetail) && !empty($elementDetail['Element']['title']) )? $elementDetail['Element']['title'] : '';

						$requestAction = SITEURL.'entities/voting/'.$voteDetails['Vote']['id'];

						$this->loadModel('User');

						if (isset($users) && !empty($users)) {
							if (($key = array_search($this->Session->read('Auth.User.id'), $users)) !== false) {
								//unset($users[$key]);
							}
						}
						foreach ($users as $userId) {
							if (!is_numeric($userId)) {
								continue;
							}

							$this->VoteUser->create();
							$voteuserData['VoteUser']['user_id'] = $userId;
							if ($this->VoteUser->save($voteuserData)) {
								$name = $this->Session->read('Auth.User.UserDetail.first_name') . ' ' . $this->Session->read('Auth.User.UserDetail.last_name');
								$usersDetails = $this->User->find('first', array('conditions' => array('User.id' => $userId)));
								$name = $this->Session->read('Auth.User.UserDetail.first_name') . ' ' . $this->Session->read('Auth.User.UserDetail.last_name');
								// Send Emails to user
								$loggedInemail = $this->Session->read('Auth.User.email');
								$emailAddress = $usersDetails['User']['email'];
								$userId = $usersDetails['User']['id'];

								$notifiInterest = $this->EmailNotification->find('first', ['conditions' => ['notification_type' => 'element', 'personlization' => 'vote_invitation_request', 'user_id' => $userId]]);

								if ((!isset($notifiInterest['EmailNotification']['email']) || $notifiInterest['EmailNotification']['email'] == 1) && (!isset($usersDetails['User']['email_notification']) || $usersDetails['User']['email_notification'] == 1)) {

									$email = new CakeEmail();
									$email->config('Smtp');
									// $email->from(array($loggedInemail => $name));

									//$email->from(array(ADMIN_FROM_EMAIL => $name));
									$email->from(array(ADMIN_FROM_EMAIL => SITENAME));
									$email->to($emailAddress);
									//$email->subject('Vote Invitation');
									$email->subject(SITENAME . ": Vote invitation request");
									$email->template('invitation_email');
									$email->emailFormat('html');
									$email->viewVars(array('Custname' => $usersDetails['UserDetail']['first_name'] . ' ' . $usersDetails['UserDetail']['last_name'], 'voteTitle' => $voteTitle, 'vote_question_optioncount' => $vote_question_optioncount, 'vote_type_id' => $vote_type_id, 'name' => $name,'projectName' => $projectName,'workspaceName' => $workspaceName,'elementName' => $elementName,'open_page' => $requestAction));
									if ($this->Common->check_email_permission($userId, 'vote_invitation_request', 'element') == true) {
										$email->send();
									}

								}

							}
						}

						/************** socket messages **************/
						if (SOCKET_MESSAGES) {
							$current_user_id = $this->user_id;
							$ele_users = $users;
							if (isset($ele_users) && !empty($ele_users)) {
								if (($key = array_search($current_user_id, $ele_users)) !== false) {
									unset($ele_users[$key]);
								}
							}
							$v_open_users = null;
							if (isset($ele_users) && !empty($ele_users)) {
								foreach ($ele_users as $key => $value) {
									if (web_notify_setting($value, 'element', 'vote_invitation_request')) {
										$v_open_users[] = $value;
									}
								}
							}
							$userDetail = get_user_data($current_user_id);
							$heading = 'Vote invitation';
							$content = [
								'notification' => [
									'type' => 'vote_invitation',
									'created_id' => $current_user_id,
									'project_id' => element_project($element_id),
									'refer_id' => $vote_id,
									'creator_name' => $userDetail['UserDetail']['full_name'],
									'subject' => $heading,
									'heading' => 'Vote: ' . $voteTitle,
									'sub_heading' => 'Project: ' . strip_tags(getFieldDetail('Project', element_project($element_id), 'title')),
									'date_time' => timezoneTimeConvertor($this->user_id, date('Y-m-d g:iA'), $format = 'd M Y g:iA') . user_country($this->user_id),
								],
								//'received_users' => array_values($ele_users),
							];
							if (is_array($v_open_users)) {
								$content['received_users'] = array_values($v_open_users);
							}
							$response['socket_content']['socket'] = $content;
						}
						/************** socket messages **************/


						$element_note = '';
						$response['step'] = 3;
						$response['success'] = true;
						$response['action'] = $action;
						$response['content'] = $element_note;
					} else {
						$response['content'] = $this->validateErrors($this->VoteUser);
					}
				}
			}

			echo json_encode($response);
			exit();
		}
	}

	public function update_vote($vote_id = null) {
		//echo $vote_id; die;
		if ($this->request->isAjax()) {
			//   // pr($this->request->data);die;
			$response = [
				'success' => false,
				'action' => null,
				'content' => null,
			];

			$this->layout = 'ajax';
			$this->autoRender = false;
			$this->loadModel('Vote');
			$this->loadModel('VoteQuestion');
			$this->loadModel('VoteQuestionOption');
			$this->loadModel('VoteUser');
			if ($this->request->is('post') || $this->request->is('put')) {
				//	 // pr($this->request->data, 1);
				$id = null;
				$action = '';
				// // pr($this->request->data);die;

				if (isset($this->request->data['Vote']) && !empty($this->request->data['Vote'])) {
					$this->Vote->set($this->request->data['Vote']);
					if ($this->Vote->validates()) {
						$this->request->data['Vote']['user_id'] = $this->Auth->User('id');

						if (isset($this->request->data['Vote']['start_date'])) {
							$this->request->data['Vote']['start_date'] = date('Y-m-d HH:ii:ss', strtotime($this->request->data['Vote']['start_date']));
						}

						if (isset($this->request->data['Vote']['end_date'])) {
							$this->request->data['Vote']['end_date'] = date('Y-m-d HH:ii:ss', strtotime($this->request->data['Vote']['end_date']));
						}

						if(isset($this->request->data['Vote']['end_date']) && !empty($this->request->data['Vote']['end_date']) && $this->request->data['Vote']['end_date'] < $this->request->data['Vote']['start_date']){

							$this->request->data['Vote']['start_date'] = $this->request->data['Vote']['end_date'];
						}


						$this->request->data['Vote']['user_id'] = $this->Auth->User('id');
						$this->request->data['Vote']['updated_user_id'] = $this->Auth->user('id');
						$this->request->data['Vote']['create_activity'] = true;
						if ($this->Vote->save($this->request->data['Vote'])) {

							if (isset($this->request->data['Vote']['id']) && !empty($this->request->data['Vote']['id'])) {
								$id = $this->request->data['Vote']['id'];
								$action = 'update';
							} else {
								$id = $this->Vote->getLastInsertId();
								$action = 'create';
							}

							$this->Vote->updateAll(
								array("Vote.create_activity" => 0),
								array("Vote.id" => $id)
							);
							// Update elemnt task activity.
							// $this->update_task_activity($this->request->data['Vote']['element_id']);

							// Get Project Id with Element id; Update Project modified date

							$this->request->data['Element']['id'] = $this->request->data['Vote']['element_id'];
							$this->request->data['Element']['updated_user_id'] = $this->Auth->user('id');
							// $this->Element->save($this->request->data['Element']);

							$element_note = null;
							if (isset($id) && !empty($id)) {
								$element_note = $this->Vote->find('first', [
									'conditions' => [
										'Vote.id' => $id,
									],
								]);

								if (!is_null($element_note)) {
									// $element_note['ElementNote']['created'] = dateFormat($element_note['ElementNote']['created']);
									$element_note['Vote']['created'] = date('d M, Y g:iA', strtotime($element_note['Vote']['created']));

									// $element_note['ElementNote']['modified'] = dateFormat($element_note['ElementNote']['modified']);

									$element_note['Vote']['modified'] = date('d M, Y g:iA', strtotime($element_note['Vote']['modified']));
								}
							}

							$response['success'] = true;
						}
					} else {
						$response['content'] = $this->validateErrors($this->Vote);
					}
				} else if (isset($this->request->data['VoteUser']) && !empty($this->request->data['VoteUser'])) {

					//// pr($this->request->data);die;
					$this->request->data['VoteUser']['updated_user_id'] = $this->Auth->user('id');
					$this->VoteUser->set($this->request->data['VoteUser']);

					if ($this->VoteUser->validates()) {
						$vote_id = $this->request->data['VoteUser']['vote_id'];
						if (isset($vote_id) && !empty($vote_id)) {
							$this->VoteUser->deleteAll(array('VoteUser.vote_id' => $vote_id));
						}
						$users = @explode(',', $this->request->data['VoteUser']['list']);
						$voteuserData['VoteUser']['vote_id'] = $vote_id;
						//// pr($users);die;
						foreach ($users as $userId) {
							$this->VoteUser->create();
							$voteuserData['VoteUser']['user_id'] = $userId;
							$this->VoteUser->save($voteuserData);
						}
						$response['success'] = true;
					} else {
						$response['content'] = $this->validateErrors($this->VoteUser);
					}
				}
			}

			echo json_encode($response);
			exit();
		}
	}

	function users_listing($vote_id = '') {

		$this->layout = false;
		$this->loadModel('User');
		$orConditions = array();
		$andConditions = array();
		if (array_key_exists('q', $this->params->query) && $this->params->query['q'] != '') {
			$orConditions = array('OR' => array(
				'UserDetail.first_name LIKE' => '%' . $this->params->query['q'] . '%',
				'UserDetail.last_name LIKE' => '%' . $this->params->query['q'] . '%',
			));
		}

		if (array_key_exists('vote_id', $this->params->query) && $this->params->query['vote_id'] != '') {
			$vote_id = $this->params->query['vote_id'];
		}

		//// pr($this->params->query);
		$user_id = $this->Auth->User('id');
		$userids = array();
		//$userids[$user_id] = $user_id;
		if (isset($vote_id) && !empty($vote_id)) {
			$this->loadModel('VoteUser');
			$user_ids = $this->VoteUser->find('list', array('conditions' => array('VoteUser.vote_id' => $vote_id), 'fields' => array('VoteUser.user_id', 'VoteUser.user_id')));
		}
		if (isset($user_ids) && !empty($user_ids)) {
			$userids = array_merge($user_ids, $userids);
		}
		//// pr($user_ids);
		$andConditions = array_merge(array('User.status' => 1, 'User.role_id' => 2, 'User.id !=' => $userids), $orConditions);
		//// pr($user_ids);
		$users = $this->User->find('all', array('conditions' => $andConditions, 'fields' => array('User.id', 'UserDetail.first_name', 'UserDetail.last_name'), 'order' => array('UserDetail.first_name' => 'ASC')));
		$arr1 = array();
		if (isset($users) && !empty($users)) {
			$i = 0;
			foreach ($users as $user) {
				$arr1[$i]['id'] = $user['User']['id'];
				$arr1[$i]['name1'] = " ";
				$arr1[$i]['itemid'] = $user['UserDetail']['first_name'] . ' ' . $user['UserDetail']['last_name'];

				if (isset($user['UserDetail']['first_name']) && !empty($user['UserDetail']['first_name'])) {
					$arr1[$i]['name'] = $user['UserDetail']['first_name'] . ' ' . $user['UserDetail']['last_name'] . " <span class='text-red'>(&nbsp;<i class='fa fa-user'></i>&nbsp;<span>)";
				} else {
					$arr1[$i]['name'] = $user['User']['email'] . " <span class='text-red'>(&nbsp;<i class='fa fa-user'></i>&nbsp;<span>)";
				}

				$i++;
			}
		}

		$arr1_tot = ( isset($arr1) && !empty($arr1) ) ? count($arr1) : 0;
		echo json_encode(array('total' => $arr1_tot, 'rows' => $arr1));
		die;
	}

	function vote_users_listing($vote_id = '') {

		$this->layout = false;
		$this->loadModel('User');
		$orConditions = array();
		$andConditions = array();
		$or = "";
		if (array_key_exists('q', $this->params->query) && $this->params->query['q'] != '') {
			$orConditions = array('OR' => array(
				'UserDetail.first_name LIKE' => '%' . $this->params->query['q'] . '%',
				'UserDetail.last_name LIKE' => '%' . $this->params->query['q'] . '%',
			));

			$or = "(ud.first_name LIKE '%$q%' OR ud.last_name LIKE '%$q%')";
		}

		if (array_key_exists('vote_id', $this->params->query) && $this->params->query['vote_id'] != '') {
			$vote_id = $this->params->query['vote_id'];
		}

		$user_id = $this->Auth->User('id');

		$userids = array();
		//$userids[$user_id] = $user_id;
		if (isset($vote_id) && !empty($vote_id)) {
			$this->loadModel('VoteUser');
			$user_ids = $this->VoteUser->find('list', array('conditions' => array('VoteUser.vote_id' => $vote_id), 'fields' => array('VoteUser.user_id', 'VoteUser.user_id')));
		}

		if (isset($user_ids) && !empty($user_ids)) {
			$userids = array_merge($user_ids, $userids);
		}

		$groups = $this->ProjectGroup->find('all', [
			'conditions' => [
				'ProjectGroup.group_owner_id' => $user_id,
				'ProjectGroup.is_deleted' => 0,
				'UserProject.id !=' => '',
			],
			'order' => ['ProjectGroup.created DESC'],
			//'recursive' => 1
		]);

		$group_data = $group_users = null;

		if (isset($groups) && !empty($groups)) {

			foreach ($groups as $key => $val) {

				$gdata = $val['ProjectGroup'];
				$gudata = $val['ProjectGroupUser'];
				$group_data[$gdata['id']][] = ['title' => htmlspecialchars(htmlspecialchars($gdata['title']))];

				foreach ($gudata as $key1 => $val1) {

					if ($val1['approved'] == 1) {

						if (is_array($userids) && !in_array($val1['user_id'], $userids)) {

							$group_users[] = $val1['user_id'];
							$userData = get_user_data($val1['user_id']);
							if( isset($userData) && isset($userData['UserDetail']['user_id']) && !empty($userData['UserDetail']['user_id']) ){
								$group_data[$gdata['id']]['users'][] = [
									'id' => $userData['UserDetail']['user_id'],
									'name' => $userData['UserDetail']['first_name'] . " " . $userData['UserDetail']['last_name'],
								];
							}
						}
					}
				}
			}
		}

		$users_arr = array();
		$userids = (isset($group_users) && !empty($group_users)) ? array_merge($group_users, $userids) : $userids;
		$allUsers = $this->objView->loadHelper('User')->grp_users_data($userids, $or);
		if(isset($allUsers) && !empty($allUsers)) {
			foreach ($allUsers as $key => $value) {
				// if($value['ud']['user_id'] != $this->Session->read('Auth.User.id')){
					$users_arr[] = ['id' => $value['ud']['user_id'], 'name' => $value[0]['full_name']];
				// }
			}
		}

		$arr1_tot = ( isset($users_arr) && !empty($users_arr) ) ? count($users_arr) : 0;

		echo json_encode(array('total' => $arr1_tot, 'success' => true, 'users' => $users_arr, 'group_data' => $group_data));
		die;

	}

	function feedback_users_listing_new($feedback_id = '') {

		$this->layout = false;

		$fb_user_ids = null;
		if (array_key_exists('feedback_id', $this->params->query) && $this->params->query['feedback_id'] != '') {
			$feedback_id = $this->params->query['feedback_id'];
			$this->loadModel('FeedbackUser');
			$fb_user_ids = $this->FeedbackUser->find('list', array('conditions' => array('FeedbackUser.feedback_id' => $feedback_id), 'fields' => array('FeedbackUser.user_id', 'FeedbackUser.user_id')));
			// pr($fb_user_ids);
		}

		// Indivisual users
		$users_arr = [];
		$project_id = $this->request->data['project'];
		$project_users = $this->objView->loadHelper('Permission')->project_feedback_users($project_id, $fb_user_ids);
		if(isset($project_users) && !empty($project_users)){
			foreach ($project_users as $key => $value) {
				if($value['user_details']['user_id'] != $this->Session->read('Auth.User.id')){
					$users_arr[] = ['id' => $value['user_details']['user_id'], 'name' => $value[0]['fullname']];
				}
			}
		}

		// group users
		$group_data = $group_users = [];
		$group_users = $this->objView->loadHelper('Permission')->grp_feedback_users($this->Session->read('Auth.User.id'), $project_id, $fb_user_ids);
		if(isset($group_users) && !empty($group_users)){
			foreach ($group_users as $key => $value) {
				if(!isset($group_data[$value['pg']['id']])) {
					// pr($value['pg']['title']);
					$group_data[$value['pg']['id']][] = ['title' => htmlspecialchars(htmlspecialchars($value['pg']['title']))];
					// $group_data[$value['pg']['id']][] = ['title' => htmlspecialchars($value['pg']['title'],ENT_QUOTES, "UTF-8")];

					//
				}
				$group_data[$value['pg']['id']]['users'][] = ['id' => $value['ud']['user_id'], 'name' => $value[0]['full_name']];
			}
		}
		// pr($group_data);
		$total_users = ( isset($users_arr) && !empty($users_arr) ) ? count($users_arr) : 0;
		echo json_encode(array( 'success' => true, 'total' => $total_users,'users' => $users_arr, 'group_data' => $group_data));
		exit;

		/*
			pr($group_users );
			pr($group_data, 1);

			$orConditions = array();
			$andConditions = array();
			$or = "";
			if (array_key_exists('q', $this->params->query) && $this->params->query['q'] != '') {
				$orConditions = array('OR' => array(
					'UserDetail.first_name LIKE' => '%' . $this->params->query['q'] . '%',
					'UserDetail.last_name LIKE' => '%' . $this->params->query['q'] . '%',
				));

				$q = $this->params->query['q'];
				$or = "(ud.first_name LIKE '%$q%' OR ud.last_name LIKE '%$q%')";
			}

			if (array_key_exists('feedback_id', $this->params->query) && $this->params->query['feedback_id'] != '') {
				$feedback_id = $this->params->query['feedback_id'];
			}

			$user_id = $this->Auth->User('id');
			$userids = array();
			if (isset($feedback_id) && !empty($feedback_id)) {
				$this->loadModel('FeedbackUser');
				$user_ids = $this->FeedbackUser->find('list', array('conditions' => array('FeedbackUser.feedback_id' => $feedback_id), 'fields' => array('FeedbackUser.user_id', 'FeedbackUser.user_id')));
			}
			$group_cond = null;

			if (isset($user_ids) && !empty($user_ids)) {
				$userids = array_merge($user_ids, $userids);
				$group_cond['NOT'] = ['ProjectGroupUser.user_id' => $userids];
			}

			$group_data = $group_users = [];
			$grp_users = $this->objView->loadHelper('User')->grp_users($user_id);
			if(isset($grp_users) && !empty($grp_users)){
				foreach ($grp_users as $key => $value) {
					if(!isset($group_data[$value['pg']['id']])) {
						$group_data[$value['pg']['id']][] = ['title' => $value['pg']['title']];
					}
					$group_users[] = $value['ud']['user_id'];
					$group_data[$value['pg']['id']]['users'][] = ['id' => $value['ud']['user_id'], 'name' => $value[0]['full_name']];
				}
			}

			$users_arr = array();
			$userids = (isset($group_users) && !empty($group_users)) ? array_merge($group_users, $userids) : $userids;

			$allUsers = $this->objView->loadHelper('User')->grp_users_data($userids, $or);
			if(isset($allUsers) && !empty($allUsers)) {
				foreach ($allUsers as $key => $value) {
					if($value['ud']['user_id'] != $this->Session->read('Auth.User.id')){
						$users_arr[] = ['id' => $value['ud']['user_id'], 'name' => $value[0]['full_name']];
					}
				}
			}
			$arr1_tot = ( isset($users_arr) && !empty($users_arr) ) ? count($users_arr) : 0;
			echo json_encode(array('total' => $arr1_tot, 'success' => true, 'users' => $users_arr, 'group_data' => $group_data));
			exit;
		*/
	}

	public function update_users() {
		//// pr($this->request->data);
		$response['success'] = false;
		$response['content'] = '';

		if (isset($this->request->data['VoteUser']['list']) && !empty($this->request->data['VoteUser']['list'])) {
			$this->loadModel('VoteUser');

			$view = new View();
			$ViewModel = $view->loadHelper('ViewModel');

			$this->VoteUser->set($this->request->data['VoteUser']);
			$vote_id = $this->request->data['VoteUser']['vote_id'];
			//$users = @explode(',', $this->request->data ['VoteUser']['list']);
			$users = $this->request->data['VoteUser']['list'];
			$voteuserData['VoteUser']['vote_id'] = $vote_id;

			$this->loadModel('User');
			$this->loadModel('Vote');
			$vote_id = $this->request->data['VoteUser']['vote_id'];
			$voteDetails = $this->Vote->find('first', array('conditions' => array('Vote.id' => $vote_id)));
			$voteTitle = $voteDetails['Vote']['title'];
			$vote_type_id = $voteDetails['VoteQuestion']['vote_type_id'];
			$vote_question_id = $voteDetails['VoteQuestion']['id'];
			// Get total options
			$this->loadModel('VoteQuestionOption');
			$vote_question_optioncount = $this->VoteQuestionOption->find('count', array('conditions' => array('VoteQuestionOption.vote_question_id' => $vote_question_id)));

			$element_id = $this->request->data['VoteUser']['element_id'];
			$ele_detail = $ViewModel->getElementDetails($element_id);
			$projectDetail = getByDbId('Project', $ele_detail[0]['user_permissions']['project_id'], 'title');
			$workspaceDetail = getByDbId('Workspace', $ele_detail[0]['user_permissions']['workspace_id'], 'title');
			$elementDetail = getByDbId('Element', $element_id, 'title');

			$projectName = ( isset($projectDetail) && !empty($projectDetail['Project']['title']) )? $projectDetail['Project']['title'] : '';
			$workspaceName = ( isset($workspaceDetail) && !empty($workspaceDetail['Workspace']['title']) )? $workspaceDetail['Workspace']['title'] : '';
			$elementName = ( isset($elementDetail) && !empty($elementDetail['Element']['title']) )? $elementDetail['Element']['title'] : '';
			$requestAction = SITEURL.'entities/voting/'.$voteDetails['Vote']['id'];


			if (isset($users) && !empty($users)) {
				if (($key = array_search($this->Session->read('Auth.User.id'), $users)) !== false) {
					//unset($users[$key]);
				}
			}

			foreach ($users as $userId) {
				$voteuserData['VoteUser']['user_id'] = $userId;
				if (!$this->VoteUser->find('count', array('conditions' => array('VoteUser.user_id' => $userId, 'VoteUser.vote_id' => $vote_id)))) {
					$voteuserData['VoteUser']['id'] = '';
					$voteuserData['VoteUser']['updated_user_id'] = $this->Auth->user('id');
					if ($this->VoteUser->save($voteuserData, false)) {

						$usersDetails = $this->User->find('first', array('conditions' => array('User.id' => $userId)));
						$name = $this->Session->read('Auth.User.UserDetail.first_name') . ' ' . $this->Session->read('Auth.User.UserDetail.last_name');
						$fname = $usersDetails['UserDetail']['full_name'];
						$loggedInemail = $this->Session->read('Auth.User.email');
						$emailAddress = $usersDetails['User']['email'];
						$email = new CakeEmail();
						$email->config('Smtp');
						// $email->from(array($loggedInemail => $name));
						//$email->from(array(ADMIN_FROM_EMAIL => $name));
						$email->from(array(ADMIN_FROM_EMAIL => SITENAME));
						$email->to($emailAddress);
						$email->subject('Vote Invitation');
						$email->template('invitation_email');
						$email->emailFormat('html');
						$email->viewVars(array('Custname' => $fname, 'voteTitle' => $voteTitle, 'vote_question_optioncount' => $vote_question_optioncount, 'vote_type_id' => $vote_type_id, 'name' => $name,'projectName' => $projectName,'workspaceName' => $workspaceName,'elementName' => $elementName,'open_page' => $requestAction ));
						$email->send();
					}
				}
			}

			/************** socket messages **************/
			if (SOCKET_MESSAGES) {
				if (isset($users) && !empty($users)) {
					$vusers = null;
					foreach ($users as $userId) {
						if (!$this->VoteUser->find('count', array('conditions' => array('VoteUser.user_id' => $userId, 'VoteUser.vote_id' => $vote_id)))) {
							$vusers[] = $userId;
						}
					}

					$project_id = $voteDetails['Vote']['project_id'];
					$current_user_id = $this->user_id;
					$voteusers = $users;
					if (isset($voteusers) && !empty($voteusers)) {
						if (($key = array_search($current_user_id, $voteusers)) !== false) {
							unset($voteusers[$key]);
						}
					}
					$open_users = null;
					if (isset($voteusers) && !empty($voteusers)) {
						foreach ($voteusers as $key1 => $value1) {
							if (web_notify_setting($value1, 'element', 'vote_invitation_request')) {
								$open_users[] = $value1;
							}
						}
					}


					$userDetail = get_user_data($current_user_id);
					$content = [
						'notification' => [
							'type' => 'vote_invitation',
							'created_id' => $current_user_id,
							'project_id' => $project_id,
							'creator_name' => $userDetail['UserDetail']['full_name'],
							'subject' =>  'Vote invitation',
							'refer_id' => $vote_id,
							'heading' => 'Vote: ' . strip_tags(getFieldDetail('Vote', $vote_id, 'title')),
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
			}
			/************** socket messages **************/

			$response['success'] = true;
			$response['content'] = $this->VoteUser->find('count', array('conditions' => array('VoteUser.vote_id' => $vote_id)));
		}
		echo json_encode($response);
		exit();
	}

	function vote_request($vote_id = null) {
		$this->set('title_for_layout', __('Vote Request', true));
		$this->layout = 'inner';
		$this->loadModel('Vote');
		$this->loadModel('VoteUser');
		$this->loadModel('VoteResult');
		$this->VoteUser->recursive = 2;
		$andConditions = array();
		$user_id = $this->Auth->User('id');
		$andConditions = array('VoteUser.user_id' => $user_id);
		if (isset($this->params->query['status']) && !empty($this->params->query['status'])) {
			$status = $this->params->query['status'];
			if ($status == 'P') {
				$andConditions = array_merge($andConditions, array('VoteUser.vote_status' => 0, 'Vote.start_date <=' => date('Y-m-d 00:00:00'), 'Vote.end_date >=' => date('Y-m-d 00:00:00')));
			} else if ($status == 'C') {
				$andConditions = array_merge($andConditions, array('VoteUser.vote_status' => 1));
			} else if ($status == 'R') {
				$andConditions = array_merge($andConditions, array('VoteUser.vote_status' => 2));
			} else if ($status == 'E') {
				$andConditions = array_merge($andConditions, array('Vote.end_date <' => date('Y-m-d 00:00:00'), 'VoteUser.vote_status' => 0));
			} else if ($status == 'N') {
				$andConditions = array_merge($andConditions, array('Vote.start_date >' => date('Y-m-d 00:00:00'), 'VoteUser.vote_status' => 0));
			}
		}

		if (isset($this->params->query['search']) && !empty($this->params->query['search'])) {
			$search = $this->params->query['search'];
			$andConditions = array_merge($andConditions, array('Vote.title LIKE' => '%' . $search . '%'));
		}

		$this->VoteUser->bindModel(array('belongsTo' => array('Vote')));
		$this->Vote->bindModel(array('hasOne' => array('VoteResult' => array('className' => 'VoteResult', 'conditions' => array('VoteResult.user_id' => $user_id)))));


		if(isset($vote_id) && !empty($vote_id)){
		$this->Vote->id = $vote_id;

		if (!$this->Vote->exists()) {
			$this->Session->setFlash(__('This Vote request
has been removed.'), 'error');
			$this->redirect(array('action' => 'vote_request/'));
		}

		}

		//$votedata = $this->VoteUser->find('all',array('conditions'=>array('VoteUser.user_id'=>$user_id)));
		//// pr($votedata);die;

		$this->set('votes', $this->VoteUser->find('all', array('conditions' => $andConditions, 'order' => array('VoteUser.id' => 'DESC'))));

		$crumb = ['last' => ['Vote Requests']];

		$this->set('crumb', $crumb);
	}

	function voting($vote_id) {
		$this->set('title_for_layout', __('Vote Request Detail', true));
		$this->layout = 'inner';
		$this->loadModel('Vote');
		$this->loadModel('VoteUser');
		$this->loadModel('VoteResult');
		$this->Vote->id = $vote_id;
		$conditions = array(
			'VoteUser.user_id' => $this->Session->read('Auth.User.id'),
			'VoteUser.vote_id' => $vote_id,
		);

		if (!$this->Vote->exists() || !$this->VoteUser->hasAny($conditions)) {
			$this->Session->setFlash(__('This Vote request
has been removed.'), 'error');
			$this->redirect(array('action' => 'vote_request'));
		}
		$user_id = $this->Auth->User('id');
		$this->Vote->recursive = 2;
		$this->Vote->bindModel(array('belongsTo' => array('Project' => array('className' => 'Project'), 'Owner' => array('className' => 'User', 'foreignKey' => 'user_id'))));
		$this->Vote->bindModel(array('hasOne' => array('VoteResult' => array('className' => 'VoteResult', 'conditions' => array('VoteResult.user_id' => $user_id)))));
		$this->VoteResult->bindModel(array('belongsTo' => array('VoteQuestionOption')));

		$votes = $this->Vote->find('first', array('conditions' => array('Vote.id' => $vote_id)));

		$this->set('votes', $votes);

		$crumb = ['last' => [$votes['Project']['title']]];

		if (isset($vote_id) && !empty($vote_id)) {

			$crumb1 = [
				'Project' => [
					'data' => [
						'url' => '/entities/vote_request',
						'class' => 'tipText',
						'title' => "Voting Requests",
						'data-original-title' => "Voting Requests",
					],
				],
			];
			$crumb = array_merge($crumb1, $crumb);
		}

		$this->set('crumb', $crumb);
	}

	function re_voting($vote_id) {
		$this->set('title_for_layout', __('Revoting', true));
		$this->layout = 'inner';
		$this->loadModel('Vote');
		$this->loadModel('VoteUser');
		$this->loadModel('VoteResult');
		$this->Vote->id = $vote_id;

		$conditions = array(
			'VoteUser.user_id' => $this->Session->read('Auth.User.id'),
			'VoteUser.vote_id' => $vote_id,
		);

		if (!$this->Vote->exists() || !$this->VoteUser->hasAny($conditions)) {
			$this->Session->setFlash(__('This Vote request
has been removed.'), 'error');
			$this->redirect(array('action' => 'vote_request'));
		}
		$user_id = $this->Auth->User('id');
		$this->Vote->recursive = 2;
		$this->Vote->bindModel(array('belongsTo' => array('Project' => array('className' => 'Project'), 'Owner' => array('className' => 'User', 'foreignKey' => 'user_id'))));
		$this->Vote->bindModel(array('hasOne' => array('VoteResult' => array('className' => 'VoteResult', 'conditions' => array('VoteResult.user_id' => $user_id)))));
		$this->VoteResult->bindModel(array('belongsTo' => array('VoteQuestionOption')));

		$votes = $this->Vote->find('first', array('conditions' => array('Vote.id' => $vote_id)));

		$this->set('votes', $votes);

		$crumb = ['last' => [$votes['Project']['title']]];

		if (isset($vote_id) && !empty($vote_id)) {

			$crumb1 = [
				'Project' => [
					'data' => [
						'url' => '/entities/vote_request',
						'class' => 'tipText',
						'title' => "Voing Requests",
						'data-original-title' => "Voing Requests",
					],
				],
			];
			$crumb = array_merge($crumb1, $crumb);
		}

		$this->set('crumb', $crumb);
	}

	function pending_vote_request() {
		$this->loadModel('Vote');
		$this->loadModel('VoteUser');
		$user_id = $this->Auth->User('id');
		$this->VoteUser->bindModel(array('belongsTo' => array('Vote')));
		return $this->VoteUser->find('count', array('conditions' => array('VoteUser.vote_status' => 0, 'Vote.is_completed' => 0, 'VoteUser.user_id' => $user_id, 'Vote.end_date >=' => date('Y-m-d 00:00:00'))));
	}

	public function vote_save() {
		if (isset($this->data) && !empty($this->data)) {
			$this->loadModel('Vote');
			// vote
			if (isset($this->request->data['VoteResult']['vote_type_id']) && !empty($this->request->data['VoteResult']['vote_type_id']) && ($this->request->data['VoteResult']['vote_type_id'] == '5' || $this->request->data['VoteResult']['vote_type_id'] == '6')) {
				$this->request->data['VoteResult']['user_id'] = $this->Auth->User('id');
				$this->loadModel('VoteResult');
				$this->request->data['VoteResult']['vote_change_datetime'] = time();
				if (isset($this->data['VoteResult']['vote_change_freq']) && !empty($this->data['VoteResult']['vote_change_freq'])) {
					$vote_change_freq = $this->data['VoteResult']['vote_change_freq'];
					$this->request->data['VoteResult']['vote_change_datetime'] = strtotime("+$vote_change_freq hours", time());
				}

				$Votes = $this->Vote->find('first', array('conditions' => array('Vote.id' => $this->request->data['VoteResult']['vote_id'])));
				$elid = $Votes['Vote']['element_id'];
				$project_id = $Votes['Vote']['project_id'];
				$workspace_id = element_workspace($elid);
				$task_data = [
					'project_id' => $project_id,
					'workspace_id' => $workspace_id,
					'element_id' => $elid,
					'relation_id' => $this->request->data['VoteResult']['vote_id'],
					'element_type' => 'votes',
					'user_id' => $this->user_id,
					'updated_user_id' => $this->user_id,
					'message' => 'Vote received',
					'updated' => date("Y-m-d H:i:s"),
				];
				$this->loadModel('Activity');
				$this->Activity->id = null;
				$this->Activity->save($task_data);

				$save = 0;
				if (isset($this->request->data['VoteResult']['vote_question_option_id']) && !empty($this->request->data['VoteResult']['vote_question_option_id'])) {
					$this->loadModel('VoteResult');
					foreach ($this->request->data['VoteResult']['vote_question_option_id'] as $id => $range) {
						$voteResults = $this->VoteResult->find('first', array('conditions' => array('VoteResult.vote_id' => $this->request->data['VoteResult']['vote_id'], 'VoteResult.user_id' => $this->Session->read('Auth.User.id'), 'VoteResult.vote_type_id' => $this->request->data['VoteResult']['vote_type_id'], 'VoteResult.vote_question_id' => $this->request->data['VoteResult']['vote_question_id'], 'VoteResult.vote_question_option_id' => $id)));
						if (isset($voteResults) && !empty($voteResults)) {
							$this->request->data['VoteResult']['id'] = $voteResults['VoteResult']['id'];
						}
						$this->request->data['VoteResult']['vote_question_option_id'] = $id;
						$this->request->data['VoteResult']['vote_range'] = $range;
						//// pr($voteResults);
						//// pr($this->request->data);
						if ($this->VoteResult->save($this->data)) {
							$save = 1;
							$this->loadModel('VoteUser');

							$vote_id = $this->request->data['VoteResult']['vote_id'];
							$user_id = $this->Auth->User('id');
							$voteUser = $this->VoteUser->find('first', array('conditions' => array('VoteUser.vote_id' => $vote_id, 'VoteUser.user_id' => $user_id)));
							if (isset($voteUser) && !empty($voteUser)) {
								$ArrVoteData['VoteUser']['id'] = $voteUser['VoteUser']['id'];
								$ArrVoteData['VoteUser']['vote_id'] = $vote_id;
								$ArrVoteData['VoteUser']['user_id'] = $user_id;
								$ArrVoteData['VoteUser']['vote_status'] = 1;
								$this->VoteUser->save($ArrVoteData, false);
							}
						}
					} //die;
				}

				if (!empty($save)) {
					$this->Session->setFlash(__('Your vote has been submitted successfully.'), 'success');
					$this->redirect(array('action' => 'vote_save'));
				}
			}
			// Re-Vote
			else {


				$Votes = $this->Vote->find('first', array('conditions' => array('Vote.id' => $this->request->data['VoteResult']['vote_id'])));
				$elid = $Votes['Vote']['element_id'];
				$project_id = $Votes['Vote']['project_id'];
				$workspace_id = element_workspace($elid);
				$task_data = [
					'project_id' => $project_id,
					'workspace_id' => $workspace_id,
					'element_id' => $elid,
					'relation_id' => $this->request->data['VoteResult']['vote_id'],
					'element_type' => 'votes',
					'user_id' => $this->user_id,
					'updated_user_id' => $this->user_id,
					'message' => 'Vote received',
					'updated' => date("Y-m-d H:i:s"),
				];
				$this->loadModel('Activity');
				$this->Activity->id = null;
				$this->Activity->save($task_data);


				$this->request->data['VoteResult']['user_id'] = $this->Auth->User('id');
				$this->loadModel('VoteResult');
				$this->request->data['VoteResult']['vote_change_datetime'] = time();
				if (isset($this->data['VoteResult']['vote_change_freq']) && !empty($this->data['VoteResult']['vote_change_freq'])) {
					$vote_change_freq = $this->data['VoteResult']['vote_change_freq'];
					$this->request->data['VoteResult']['vote_change_datetime'] = strtotime("+$vote_change_freq hours", time());
				}
				//// pr($this->data);die;
				if ($this->VoteResult->save($this->data)) {
					$this->loadModel('VoteUser');
					$vote_id = $this->request->data['VoteResult']['vote_id'];
					$user_id = $this->Auth->User('id');
					$voteUser = $this->VoteUser->find('first', array('conditions' => array('VoteUser.vote_id' => $vote_id, 'VoteUser.user_id' => $user_id)));
					if (isset($voteUser) && !empty($voteUser)) {
						$ArrVoteData['VoteUser']['id'] = $voteUser['VoteUser']['id'];
						$ArrVoteData['VoteUser']['vote_id'] = $vote_id;
						$ArrVoteData['VoteUser']['user_id'] = $user_id;
						$ArrVoteData['VoteUser']['vote_status'] = 1;

						$this->VoteUser->save($ArrVoteData, false);
						//// pr($ArrVoteData); die;
					}
					$this->Session->setFlash(__('Your vote has been submitted successfully.'), 'success');
					$this->redirect(array('action' => 'vote_save'));
				}
			}
		}
		$this->redirect(array('action' => 'vote_request'));
	}

	public function remove_vote() {
		if (isset($this->data['id']) && !empty($this->data['id'])) {
			$vote_id = $this->data['id'];
			$this->loadModel('Vote');

			$dids = $this->Vote->findById($vote_id);

			if (isset($vote_id) && !empty($vote_id)) {
				// Get No Response votes
				$usersEmails = $this->Common->getUsersListToSendReminder($vote_id);

				if (isset($usersEmails) && !empty($usersEmails)) {
					$this->loadModel('Vote');
					$voteDetails = $this->Vote->find('first', array('conditions' => array('Vote.id' => $vote_id)));

					$view = new View();
					$ViewModel = $view->loadHelper('ViewModel');

					$project_id = $voteDetails['Vote']['project_id'];
					$element_id = $voteDetails['Vote']['element_id'];

					$ele_detail = $ViewModel->getElementDetails($element_id);
					$projectDetail = getByDbId('Project', $project_id, 'title');
					$workspaceDetail = getByDbId('Workspace', $ele_detail[0]['user_permissions']['workspace_id'], 'title');
					$elementDetail = getByDbId('Element', $element_id, 'title');

					$projectName = ( isset($projectDetail) && !empty($projectDetail['Project']['title']) )? $projectDetail['Project']['title'] : '';
					$workspaceName = ( isset($workspaceDetail) && !empty($workspaceDetail['Workspace']['title']) )? $workspaceDetail['Workspace']['title'] : '';
					$elementName = ( isset($elementDetail) && !empty($elementDetail['Element']['title']) )? $elementDetail['Element']['title'] : '';

					$requestAction = SITEURL.'entities/update_element/'.$element_id.'#votes';


					$voteTitle = $voteDetails['Vote']['title'];
					foreach ($usersEmails as $usersDetails) {
						$name = $this->Session->read('Auth.User.UserDetail.first_name') . ' ' . $this->Session->read('Auth.User.UserDetail.last_name');

						$notifiInterest = $this->EmailNotification->find('first', ['conditions' => ['notification_type' => 'element', 'personlization' => 'vote_removed', 'user_id' => $usersDetails['User']['id']]]);

						if ((!isset($notifiInterest['EmailNotification']['email']) || $notifiInterest['EmailNotification']['email'] == 1) && (!isset($usersDetails['User']['email_notification']) || $usersDetails['User']['email_notification'] == 1)) {

							// Send Emails to user
							$loggedInemail = $this->Session->read('Auth.User.email');
							$emailAddress = $usersDetails['User']['email'];
							$userId = $usersDetails['User']['id'];
							$email = new CakeEmail();
							$email->config('Smtp');
							// $email->from(array($loggedInemail => $name));
							//$email->from(array(ADMIN_FROM_EMAIL => $name));
							$email->from(array(ADMIN_FROM_EMAIL => SITENAME));
							$email->to($emailAddress);
							//$email->subject('Vote Removed');
							$email->subject(SITENAME . ": Vote removed");
							$email->template('reminder_vote_remove');
							$email->emailFormat('html');
							$email->viewVars(array('Custname' => $usersDetails['UserDetail']['first_name'] . ' ' . $usersDetails['UserDetail']['last_name'], 'voteTitle' => $voteTitle, 'name' => $name,'projectName' => $projectName,'workspaceName' => $workspaceName,'elementName' => $elementName,'open_page' => $requestAction));

							if ($this->Common->check_email_permission($userId, 'vote_removed', 'element') == true) {
								$email->send();
							}
						}
					}

					/************** socket messages **************/
					if (SOCKET_MESSAGES) {
						$project_id = element_project($element_id);
						$current_user_id = $this->user_id;
						$voteusers = Set::extract($usersEmails, '/User/id');
						$r_users = $voteusers;
						if (isset($r_users) && !empty($r_users)) {
							if (($key = array_search($current_user_id, $r_users)) !== false) {
								unset($r_users[$key]);
							}
						}
						$open_users = null;
						if (isset($r_users) && !empty($r_users)) {
							foreach ($r_users as $key1 => $value1) {
								if (web_notify_setting($value1, 'element', 'vote_removed')) {
									$open_users[] = $value1;
								}
							}
						}
						$userDetail = get_user_data($current_user_id);
						$content = [
							'notification' => [
								'type' => 'vote_removed',
								'created_id' => $current_user_id,
								'project_id' => $project_id,
								'refer_id' => $element_id,
								'creator_name' => $userDetail['UserDetail']['full_name'],
								'subject' => 'Vote removed',
								'heading' => 'Vote: ' . strip_tags(getFieldDetail('Vote', $vote_id, 'title')),
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
			}

			if ($this->Vote->delete(array('Vote.id' => $vote_id))) {

				$element_id = $this->data['element_id'];
				$this->request->data['Element']['id'] = $element_id;
				$this->request->data['Element']['updated_user_id'] = $this->Auth->user('id');

				$defaultElementid = $element_id;

						$task_data = [
						'project_id' => element_project($defaultElementid),
						'workspace_id' => element_workspace($defaultElementid),
						'element_id' => $defaultElementid,
						'element_type' => 'votes',
						'user_id' => $this->user_id,
						'relation_id' => $dids['Vote']['id'],
						'updated_user_id' => $this->user_id,
						'message' => 'Vote deleted',
						'updated' => date("Y-m-d H:i:s"),
						];
						$this->loadModel('Activity');
						$this->Activity->id = null;
						$this->Activity->save($task_data);

				// $this->Element->save($this->request->data['Element']);

				// Get Project Id with Element id; Update Project modified date
				// $this->update_project_modify($element_id);
				// $this->update_task_activity($element_id);

				$this->Vote->delete(array('Vote.id' => $vote_id));
				die('success');
			}
		}
		die('error');
	}

	public function cancel_vote() {
		if ($this->request->is('ajax')) {
			if ($this->request->is('post') || $this->request->is('put')) {
				// pr($this->data, 1);
				if (isset($this->data['id']) && !empty($this->data['id'])) {
					$vote_id = $this->data['id'];

					if (isset($vote_id) && !empty($vote_id)) {
						$this->loadModel('Vote');
						$voteDetails = $this->Vote->find('first', array('conditions' => array('Vote.id' => $vote_id)));

						$project_id = $voteDetails['Vote']['project_id'];
						$element_id = $voteDetails['Vote']['element_id'];
						if ($this->Vote->delete(array('Vote.id' => $vote_id))) {
							$this->loadModel('Activity');
							if($this->Activity->deleteAll(array('Activity.relation_id' => $vote_id, 'Activity.element_id' => $element_id, 'Activity.project_id' => $project_id))){
								die('success');
							}
						}
					}

				}
			}
		}
		die('error');
	}

	public function decline_vote($vote_id = '') {
		// if(isset($this->data['vote_id']) && !empty($this->data['vote_id'])){
		//$vote_id = $this->data['vote_id'];
		$this->loadModel('VoteResult');
		$this->loadModel('VoteUser');
		$this->loadModel('VoteQuestion');
		$this->loadModel('Vote');
		$Votes = $this->Vote->find('first', array('conditions' => array('Vote.id' => $vote_id)));
		$VoteQuestions = $this->VoteQuestion->find('first', array('conditions' => array('VoteQuestion.vote_id' => $vote_id)));
		$ArrData['VoteResult']['vote_id'] = $vote_id;
		$ArrData['VoteResult']['user_id'] = $this->Auth->User('id');
		$ArrData['VoteResult']['vote_type_id'] = $VoteQuestions['VoteQuestion']['vote_type_id'];
		$ArrData['VoteResult']['vote_question_id'] = $VoteQuestions['VoteQuestion']['id'];
		$ArrData['VoteResult']['vote_question_option_id'] = 'D';
		$ArrData['VoteResult']['vote_range'] = '';
		// Delete Old entries
		$this->VoteResult->deleteAll(array('VoteResult.vote_id' => $vote_id, 'VoteResult.user_id' => $this->Auth->User('id')));

		if (isset($Votes['Vote']['vote_change_freq']) && !empty($Votes['Vote']['vote_change_freq'])) {
			$vote_change_freq = $Votes['Vote']['vote_change_freq'];
			$ArrData['VoteResult']['vote_change_datetime'] = strtotime("+$vote_change_freq hours", time());
		}

		if ($this->VoteResult->save($ArrData)) {
			$voteUser = $this->VoteUser->find('first', array('conditions' => array('VoteUser.vote_id' => $vote_id, 'VoteUser.user_id' => $this->Auth->User('id'))));
			if (isset($voteUser) && !empty($voteUser)) {
				$ArrVoteData['VoteUser']['id'] = $voteUser['VoteUser']['id'];
				$ArrVoteData['VoteUser']['vote_id'] = $vote_id;
				$ArrVoteData['VoteUser']['user_id'] = $this->Auth->User('id');
				$ArrVoteData['VoteUser']['vote_status'] = 2; // rejected
				$this->VoteUser->save($ArrVoteData);

				$elid = $Votes['Vote']['element_id'];
				$project_id = $Votes['Vote']['project_id'];
				$workspace_id = element_workspace($elid);
				$task_data = [
					'project_id' => $project_id,
					'workspace_id' => $workspace_id,
					'element_id' => $elid,
					'relation_id' => $vote_id,
					'element_type' => 'votes',
					'user_id' => $this->user_id,
					'updated_user_id' => $this->user_id,
					'message' => 'Vote declined',
					'updated' => date("Y-m-d H:i:s"),
				];
				$this->loadModel('Activity');
				$this->Activity->id = null;
				$this->Activity->save($task_data);
			}
			// die('success');
		}
		$this->redirect(array('controller' => 'entities', 'action' => 'vote_request'));
		//  }
		//  die('error');
	}

	public function view_question($vote_id = '') {
		if (isset($vote_id) && !empty($vote_id)) {
			$this->loadModel('Vote');
			$this->loadModel('VoteResult');
			$this->Vote->recursive = 2;
			$this->Vote->bindModel(array('hasMany' => array('VoteResult')));
			$vote_details = $this->Vote->find('first', array('conditions' => array('Vote.id' => $vote_id)));
			$this->set('vote_details', $vote_details);

			//// pr($vote_details);
		}
	}

	public function view_result($vote_id = '') {
		if (isset($vote_id) && !empty($vote_id)) {
			$this->loadModel('Vote');
			$this->loadModel('VoteResult');
			$this->Vote->recursive = 2;
			$this->Vote->bindModel(array('hasMany' => array('VoteResult')));
			$vote_details = $this->Vote->find('first', array('conditions' => array('Vote.id' => $vote_id)));
			$this->set('vote_details', $vote_details);

			//// pr($vote_details);
		}
	}

	public function reminder_vote_user($vote_id = '') {
		if (isset($vote_id) && !empty($vote_id)) {
			// Get No Response votes
			$usersEmails = $this->Common->getUsersListToSendReminder($vote_id);

			//pr($this->request->data); die;

			if (isset($usersEmails) && !empty($usersEmails)) {

				$view = new View();
				$ViewModel = $view->loadHelper('ViewModel');

				$this->loadModel('Vote');
				$voteDetails = $this->Vote->find('first', array('conditions' => array('Vote.id' => $vote_id)));

				$project_id = $voteDetails['Vote']['project_id'];
				$element_id = $voteDetails['Vote']['element_id'];

				$ele_detail = $ViewModel->getElementDetails($element_id);
				$projectDetail = getByDbId('Project', $project_id, 'title');
				$workspaceDetail = getByDbId('Workspace', $ele_detail[0]['user_permissions']['workspace_id'], 'title');
				$elementDetail = getByDbId('Element', $element_id, 'title');

				$projectName = ( isset($projectDetail) && !empty($projectDetail['Project']['title']) )? $projectDetail['Project']['title'] : '';
				$workspaceName = ( isset($workspaceDetail) && !empty($workspaceDetail['Workspace']['title']) )? $workspaceDetail['Workspace']['title'] : '';
				$elementName = ( isset($elementDetail) && !empty($elementDetail['Element']['title']) )? $elementDetail['Element']['title'] : '';

				$requestAction = SITEURL.'entities/voting/'.$voteDetails['Vote']['id'];

				$voteTitle = $voteDetails['Vote']['title'];
				foreach ($usersEmails as $usersDetails) {
					$name = $this->Session->read('Auth.User.UserDetail.first_name') . ' ' . $this->Session->read('Auth.User.UserDetail.last_name');

					$notifiInterest = $this->EmailNotification->find('first', ['conditions' => ['notification_type' => 'element', 'personlization' => 'vote_reminder', 'user_id' => $usersDetails['User']['id']]]);

					if ((!isset($notifiInterest['EmailNotification']['email']) || $notifiInterest['EmailNotification']['email'] == 1) && (!isset($usersDetails['User']['email_notification']) || $usersDetails['User']['email_notification'] == 1)) {

						// Send Emails to user
						$loggedInemail = $this->Session->read('Auth.User.email');
						$emailAddress = $usersDetails['User']['email'];
						$userId = $usersDetails['User']['id'];
						$email = new CakeEmail();
						$email->config('Smtp');
						// $email->from(array($loggedInemail => $name));
						//$email->from(array(ADMIN_FROM_EMAIL => $name));
						$email->from(array(ADMIN_FROM_EMAIL => SITENAME));
						$email->to($emailAddress);
						//$email->subject('Vote Invitation Reminder');
						$email->subject(SITENAME . ": Vote reminder");
						$email->template('reminder_email');
						$email->emailFormat('html');
						$email->viewVars(array('Custname' => $usersDetails['UserDetail']['first_name'] . ' ' . $usersDetails['UserDetail']['last_name'], 'voteTitle' => $voteTitle, 'name' => $name,'projectName' => $projectName,'workspaceName' => $workspaceName,'elementName' => $elementName,'open_page' => $requestAction));
						if ($this->Common->check_email_permission($userId, 'vote_reminder', 'element') == true) {
							$email->send();
						}

					}

				}

				/************** socket messages **************/
				if (SOCKET_MESSAGES) {
					$project_id =  $voteDetails['Vote']['project_id'] ;
					$current_user_id = $this->user_id;
					$voteusers = Set::extract($usersEmails, '/User/id');
					$r_users = $voteusers;
					if (isset($r_users) && !empty($r_users)) {
						if (($key = array_search($current_user_id, $r_users)) !== false) {
							unset($r_users[$key]);
						}
					}
					$open_users = null;
					if (isset($r_users) && !empty($r_users)) {
						foreach ($r_users as $key1 => $value1) {
							if (web_notify_setting($value1, 'element', 'vote_reminder')) {
								$open_users[] = $value1;
							}
						}
					}
					$userDetail = get_user_data($current_user_id);
					$content = [
						'notification' => [
							'type' => 'vote_reminder',
							'created_id' => $current_user_id,
							'project_id' => $project_id,
							'refer_id' => $voteDetails['Vote']['id'],
							'creator_name' => $userDetail['UserDetail']['full_name'],
							'subject' => 'Vote reminder',
							'heading' => 'Vote: ' . strip_tags(getFieldDetail('Vote', $vote_id, 'title')),
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

				die('success');
			}
		}
		die('error');
	}

	public function vote_signoff($element_id = null) {
		if ($this->request->is('ajax')) {
			$this->loadModel('Vote');
			$response = [
				'success' => false,
				'msg' => '',
				'content' => null,
			];

			$this->layout = 'ajax';
			$this->autoRender = false;

			if ($this->request->is('post') || $this->request->is('put')) {

				$this->request->data['Vote']['create_activity'] = 1;
				$post = $this->request->data['Vote'];

				if (isset($post['id']) && !empty($post['id'])) {
					$id = $post['id'];
					$post['is_completed'] = (isset($this->request->data['reopen']) && !empty($this->request->data['reopen']) && $this->request->data['reopen'] == 'reopen') ? 0 : 1;
					// pr($post, 1);
					$this->Vote->id = $id;
					if (!$this->Vote->exists()) {
						throw new NotFoundException(__('This Vote request has been removed.'), 'error');
					}
					//// pr($post);die;
					if ($this->Vote->save($post)) {
						$this->Vote->updateAll(
							array("Vote.create_activity" => 0),
							array("Vote.id" => $id)
						);
						// $this->update_task_activity($element_id);
						// Get Project Id with Element id; Update Project modified date
						// $this->update_project_modify($element_id);

						$response['success'] = true;
						$response['msg'] = 'You have been signed off successfully.';
						$response['content'] = [];
					} else {
						$response['msg'] = 'Signing off could not be completed. Please try again later.';
					}
					// $this->Element->_query(1);
				}
			}

			echo json_encode($response);
			exit();
		}
	}

	/*     * *********** Votes ************* */

	/*     * *********** Feedback ************* */

	function pending_feedback_request() {

		$this->loadModel('Feedback');
		$this->loadModel('FeedbackUser');
		$user_id = $this->Auth->User('id');
		$this->FeedbackUser->bindModel(array('belongsTo' => array('Feedback')));
		return $this->FeedbackUser->find('count', array('conditions' => array('FeedbackUser.feedback_status' => 0, 'Feedback.sign_off' => 0, 'FeedbackUser.user_id' => $user_id, 'Feedback.end_date >=' => date('Y-m-d 00:00:00'))));
	}

	function feedback_request() {
		$this->set('title_for_layout', __('Feedback Request', true));
		$this->layout = 'inner';
		$this->loadModel('Feedback');
		$this->loadModel('FeedbackUser');
		//$this->loadModel('VoteResult');
		$this->FeedbackUser->recursive = 2;
		$andConditions = array();
		$user_id = $this->Auth->User('id');
		$andConditions = array('FeedbackUser.user_id' => $user_id,'Feedback.id !='=>'');
		if (isset($this->params->query['status']) && !empty($this->params->query['status'])) {
			$status = $this->params->query['status'];
			if ($status == 'P') {
				$andConditions = array_merge($andConditions, array('FeedbackUser.feedback_status' => 0, 'Feedback.start_date <=' => date('Y-m-d 00:00:00'), 'Feedback.end_date >=' => date('Y-m-d 00:00:00')));
			} else if ($status == 'C') {
				$andConditions = array_merge($andConditions, array('FeedbackUser.feedback_status' => 1));
			} else if ($status == 'R') {
				$andConditions = array_merge($andConditions, array('FeedbackUser.feedback_status' => 2));
			} else if ($status == 'E') {
				$andConditions = array_merge($andConditions, array('Feedback.end_date <' => date('Y-m-d 00:00:00'), 'FeedbackUser.feedback_status' => 0));
			} else if ($status == 'N') {
				$andConditions = array_merge($andConditions, array('Feedback.start_date >' => date('Y-m-d 00:00:00'), 'FeedbackUser.feedback_status' => 0));
			}
		}

		if (isset($this->params->query['search']) && !empty($this->params->query['search'])) {
			$search = $this->params->query['search'];
			$andConditions = array_merge($andConditions, array('Feedback.title LIKE' => '%' . $search . '%'));
		}

		$this->FeedbackUser->recursive = 2;
		$this->FeedbackUser->bindModel(array('belongsTo' => array('Feedback')));
		$this->Feedback->bindModel(array('hasOne' => array('FeedbackResult' => array('className' => 'FeedbackResult', 'conditions' => array('FeedbackResult.user_id' => $user_id)))));

		//$votedata = $this->FeedbackUser->find('all',array('conditions'=>array('FeedbackUser.user_id'=>$user_id)));
		// // pr($votedata);die;

		$this->set('feedbacks', $this->FeedbackUser->find('all', array('conditions' => $andConditions, 'order' => array('FeedbackUser.id' => 'DESC'))));

		$crumb = ['last' => ['Feedback Requests']];

		$this->set('crumb', $crumb);
	}
	public function get_s_e_date($project_id, $workspace_id, $element_id) {

		$elm = $this->Element->find("first", array("recursive" => -1, "fields" => array("Element.start_date", "Element.end_date"), "conditions" => array("Element.id" => $element_id)));
		$wor = $this->Workspace->find("first", array("recursive" => -1, "fields" => array("Workspace.start_date", "Workspace.end_date"), "conditions" => array("Workspace.id" => $workspace_id)));
		$pro = $this->Project->find("first", array("recursive" => -1, "fields" => array("Project.start_date", "Project.end_date"), "conditions" => array("Project.id" => $project_id)));

		$mindate_elm = isset($elm['Element']['start_date']) && !empty($elm['Element']['start_date']) ? date("d-m-Y", strtotime($elm['Element']['start_date'])) : '';
		$maxdate_elm = isset($elm['Element']['end_date']) && !empty($elm['Element']['end_date']) ? date("d-m-Y", strtotime($elm['Element']['end_date'])) : '';
		$mindate_workspace = isset($wor['Workspace']['start_date']) && !empty($wor['Workspace']['start_date']) ? date("d-m-Y", strtotime($wor['Workspace']['start_date'])) : '';
		//$mindate_workspace = ($mindate_workspace < $cur_date) ? $cur_date : $mindate_workspace;
		$maxdate_workspace = isset($wor['Workspace']['end_date']) && !empty($wor['Workspace']['end_date']) ? date("d-m-Y", strtotime($wor['Workspace']['end_date'])) : '';
		$mindate_project = isset($pro['Project']['start_date']) && !empty($pro['Project']['start_date']) ? date("d-m-Y", strtotime($pro['Project']['start_date'])) : '';
		$maxdate_project = isset($pro['Project']['end_date']) && !empty($pro['Project']['end_date']) ? date("d-m-Y", strtotime($pro['Project']['end_date'])) : '';
		$cur_date = date("d-m-Y");
		if (isset($mindate_elm) && empty($mindate_elm)) {
			if (isset($mindate_workspace) && !empty($mindate_workspace)) {
				//$mindate_elm = ($mindate_workspace < $cur_date) ? $cur_date : $mindate_workspace;
				$mindate_elm = $mindate_workspace;
			} else if (isset($mindate_workspace) && empty($mindate_workspace)) {
				//$mindate_elm = ($mindate_project < $cur_date) ? $cur_date : $mindate_project;
				$mindate_elm = $mindate_project;
			} else {
				$mindate_elm = '';
			}
		} else if (isset($mindate_elm) && !empty($mindate_elm)) {
			//$mindate_elm = ($mindate_workspace < $cur_date) ? $cur_date : $mindate_workspace;
		}
		if (isset($maxdate_elm) && empty($maxdate_elm)) {
			if (isset($maxdate_workspace) && !empty($maxdate_workspace)) {
				$maxdate_elm = $maxdate_workspace;
			} else if (isset($maxdate_workspace) && empty($maxdate_workspace)) {
				$maxdate_elm = $maxdate_project;
			} else {
				$maxdate_elm = '';
			}
		}
		return array("mindate" => $mindate_elm, "maxdate" => $maxdate_elm);

	}
	public function add_feedback($element_id = null) {
		if ($this->request->isAjax()) {

			$response = [
				'success' => false,
				'action' => null,
				'content' => null,
			];

			$this->layout = 'ajax';
			$this->autoRender = false;
			$this->loadModel('Feedback');
			$this->loadModel('FeedbackUser');
			if ($this->request->is('post') || $this->request->is('put')) {
				//	 // pr($this->request->data, 1);
				$id = null;
				$action = '';
				if (isset($this->request->data['Feedback']) && !empty($this->request->data['Feedback'])) {
					$this->Feedback->set($this->request->data['Feedback']);

					$project_id = $this->request->data['Feedback']['project_id'];
					$workspace_id = $this->request->data['Feedback']['workspace_id'];
					$element_id = $this->request->data['Feedback']['element_id'];
					$dates = $this->get_s_e_date($project_id, $workspace_id, $element_id);

					if (isset($dates['mindate']) && !empty($this->request->data['Feedback']['start_date']) && strtotime($this->request->data['Feedback']['start_date']) < strtotime($dates['mindate'])) {
						$this->Feedback->validationErrors['start_date'][0] = 'Invalid date selection.';
					}
					if (isset($dates['maxdate']) && !empty($this->request->data['Feedback']['end_date']) && strtotime($this->request->data['Feedback']['end_date']) > strtotime($dates['maxdate'])) {
						$this->Feedback->validationErrors['end_date'][0] = 'Invalid date selection.';
					}

					if(isset($this->request->data['Feedback']['end_date']) && !empty($this->request->data['Feedback']['end_date']) && $this->request->data['Feedback']['end_date'] < $this->request->data['Feedback']['start_date']){

							$this->request->data['Feedback']['start_date'] = $this->request->data['Feedback']['end_date'];
						}

					//print_r($this->Feedback->validationErrors);die;

					if ($this->Feedback->validates()) {
						$this->request->data['Feedback']['user_id'] = $this->Auth->User('id');

						if (isset($this->request->data['Feedback']['start_date'])) {
							$this->request->data['Feedback']['start_date'] = date('Y-m-d HH:ii:ss', strtotime($this->request->data['Feedback']['start_date']));
						}

						if (isset($this->request->data['Feedback']['end_date'])) {
							$this->request->data['Feedback']['end_date'] = date('Y-m-d HH:ii:ss', strtotime($this->request->data['Feedback']['end_date']));
						}

						$this->request->data['Feedback']['user_id'] = $this->Auth->User('id');
						$this->request->data['Feedback']['updated_user_id'] = $this->Auth->user('id');

						$this->request->data['Feedback']['create_activity'] = true;

						if ($this->Feedback->save($this->request->data['Feedback'])) {

							if (isset($this->request->data['Feedback']['id']) && !empty($this->request->data['Feedback']['id'])) {
								$id = $this->request->data['Feedback']['id'];
								$action = 'update';
							} else {
								$id = $this->Feedback->getLastInsertId();
								$action = 'create';
							}

							$this->Feedback->updateAll(
								array("Feedback.create_activity" => 0),
								array("Feedback.id" => $id)
							);

							// Get Project Id with Element id; Update Project modified date
							// $this->update_project_modify($element_id);
							// Update elemnt task activity.
							// $this->update_task_activity($element_id);

							$this->request->data['Element']['id'] = $element_id;
							$this->request->data['Element']['updated_user_id'] = $this->Auth->user('id');
							// $this->Element->save($this->request->data['Element']);

							$element_note = null;
							if (isset($id) && !empty($id)) {
								$element_note = $this->Feedback->find('first', [
									'conditions' => [
										'Feedback.id' => $id,
									],
								]);

								if (!is_null($element_note)) {
									$element_note['Feedback']['created'] = $this->objView->loadHelper('Wiki')->_displayDate(date('Y-m-d h:i:s A', strtotime($element_note['Feedback']['modified'])), $format = 'd M, Y g:iA');

									$element_note['Feedback']['modified'] = $this->objView->loadHelper('Wiki')->_displayDate(date('Y-m-d h:i:s A', strtotime($element_note['Feedback']['modified'])), $format = 'd M, Y g:iA');
								}
							}

							$response['success'] = true;
							$response['step'] = 1;
							$response['action'] = $action;
							$response['content'] = $element_note;
						}
					} else {
						//print_r($this->Feedback->validationErrors);//die;
						//print_r($this->validateErrors($this->Feedback));
						$response['content'] = $this->Feedback->validationErrors;
					}
				} else if (isset($this->request->data['FeedbackUser']) && !empty($this->request->data['FeedbackUser'])) {

					$this->FeedbackUser->set($this->request->data['FeedbackUser']);
					if ($this->FeedbackUser->validates()) {
						$feedback_id = $this->request->data['FeedbackUser']['feedback_id'];
						if (isset($feedback_id) && !empty($feedback_id)) {
							$this->FeedbackUser->deleteAll(array('FeedbackUser.feedback_id' => $feedback_id));
						}
						$users = $this->request->data['FeedbackUser']['list'];
						if (isset($users) && !empty($users)) {
							if (($key = array_search($this->Session->read('Auth.User.id'), $users)) !== false) {
								unset($users[$key]);
							}
						}
						$voteuserData['FeedbackUser']['feedback_id'] = $feedback_id;

						//// pr($this->request->data);die;
						$this->loadModel('Feedback');
						$feedback_id = $this->request->data['FeedbackUser']['feedback_id'];
						$voteDetails = $this->Feedback->find('first', array('conditions' => array('Feedback.id' => $feedback_id)));
						$feedbackTitle = $voteDetails['Feedback']['title'];

						$view = new View();
						$ViewModel = $view->loadHelper('ViewModel');

						$project_id = $voteDetails['Feedback']['project_id'];
						$element_id = $voteDetails['Feedback']['element_id'];

						$ele_detail = $ViewModel->getElementDetails($element_id);
						$projectDetail = getByDbId('Project', $project_id, 'title');
						$workspaceDetail = getByDbId('Workspace', $ele_detail[0]['user_permissions']['workspace_id'], 'title');
						$elementDetail = getByDbId('Element', $element_id, 'title');

						$projectName = ( isset($projectDetail) && !empty($projectDetail['Project']['title']) )? $projectDetail['Project']['title'] : '';
						$workspaceName = ( isset($workspaceDetail) && !empty($workspaceDetail['Workspace']['title']) )? $workspaceDetail['Workspace']['title'] : '';
						$elementName = ( isset($elementDetail) && !empty($elementDetail['Element']['title']) )? $elementDetail['Element']['title'] : '';
						$requestAction = SITEURL.'entities/feedbacks/'.$voteDetails['Feedback']['id'];


						$this->loadModel('User');
						foreach ($users as $userId) {
							if (!is_numeric($userId)) {
								continue;
							}

							$this->FeedbackUser->create();
							$voteuserData['FeedbackUser']['user_id'] = $userId;

							$voteuserData['FeedbackUser']['updated_user_id'] = $this->Auth->user('id');
							if ($this->FeedbackUser->save($voteuserData)) {
								$name = $this->Session->read('Auth.User.UserDetail.first_name') . ' ' . $this->Session->read('Auth.User.UserDetail.last_name');
								$usersDetails = $this->User->find('first', array('conditions' => array('User.id' => $userId)));
								$name = $this->Session->read('Auth.User.UserDetail.first_name') . ' ' . $this->Session->read('Auth.User.UserDetail.last_name');
								// Send Emails to user
								$loggedInemail = $this->Session->read('Auth.User.email');
								$emailAddress = $usersDetails['User']['email'];
								$userId = $usersDetails['User']['id'];
								$email = new CakeEmail();
								$email->config('Smtp');
								// $email->from(array($loggedInemail => $name));
								//$email->from(array(ADMIN_FROM_EMAIL => $name));
								$email->from(array(ADMIN_FROM_EMAIL => SITENAME));
								$email->to($emailAddress);
								$email->subject(SITENAME . ": Feedback invitation request");
								$email->template('feedback_invitation_email');
								$email->emailFormat('html');
								$email->viewVars(array('Custname' => $usersDetails['UserDetail']['first_name'] . ' ' . $usersDetails['UserDetail']['last_name'], 'name' => $name, 'feedbackTitle' => $feedbackTitle,'projectName' => $projectName,'workspaceName' => $workspaceName,'elementName' => $elementName,'open_page' => $requestAction));
								if ($this->Common->check_email_permission($userId, 'feedback_invitation_request', 'element') == true) {

									$email->send();
								}
							}
						}

						/************** socket messages **************/
						if (SOCKET_MESSAGES) {
							$current_user_id = $this->user_id;
							$ele_users = $users;
							if (isset($ele_users) && !empty($ele_users)) {
								if (($key = array_search($current_user_id, $ele_users)) !== false) {
									unset($ele_users[$key]);
								}
							}
							$f_open_users = null;
							if (isset($ele_users) && !empty($ele_users)) {
								foreach ($ele_users as $key => $value) {
									if (web_notify_setting($value, 'element', 'feedback_invitation_request')) {
										$f_open_users[] = $value;
									}
								}
							}
							$element_id  = $this->request->params['pass'][0];
							$userDetail = get_user_data($current_user_id);
							$heading = 'Feedback Invitation';
							$content = [
								'notification' => [
									'type' => 'feedback_invitation',
									'created_id' => $current_user_id,
									'project_id' => element_project($element_id),
									'refer_id' => $feedback_id,
									'creator_name' => $userDetail['UserDetail']['full_name'],
									'subject' => $heading,
									'heading' => 'Feedback: ' . $feedbackTitle,
									'sub_heading' => 'Project: ' . strip_tags(getFieldDetail('Project', element_project($element_id), 'title')),
									'date_time' => timezoneTimeConvertor($this->user_id, date('Y-m-d g:iA'), $format = 'd M Y g:iA') . user_country($this->user_id),
								],
								//'received_users' => array_values($ele_users),
							];
							if (is_array($f_open_users)) {
								$content['received_users'] = array_values($f_open_users);
							}
							$response['socket_content']['socket'] = $content;
						}
						/************** socket messages **************/


						$element_note = '';
						$response['step'] = 2;
						$response['success'] = true;
						$response['action'] = $action;
						$response['content'] = $element_note;
					} else {
						$response['content'] = $this->validateErrors($this->FeedbackUser);
					}
				}
			}

			echo json_encode($response);
			exit();
		}
	}

	/*
		     * @name  		get_users_rating
		     * @access		public
		     * @package  	App/Controller/GroupsController
	*/

	public function get_users_rating() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$response = [
				'success' => false,
				'msg' => null,
				'content' => null,
			];

			$rating = [];

			if ($this->request->is('post') || $this->request->is('put')) {
				$this->loadModel('FeedbackRating');
				$post = $this->request->data;

				$query = "SELECT `given_to_id`, order_sum as sum_rate, order_count as total_rows FROM
							(
								SELECT `given_to_id`, sum(rate) order_sum, count(rate) order_count
								FROM feedback_ratings
								GROUP BY `given_to_id`
							) temp
						GROUP BY `given_to_id` ";

				$rating = $this->FeedbackRating->query($query);

				$rating_data = null;
				if (isset($rating) && !empty($rating)) {

					foreach ($rating as $data) {
						$userid = $data['temp']['given_to_id'];
						$rate = $data['temp']['sum_rate'];
						$total_rows = $data['temp']['total_rows'];
						$totalRating = $rate / $total_rows;
						$rating_data[$userid] = round($totalRating, 1);
					}
				}

				$response['success'] = true;

				$response['content'] = (isset($rating_data) && !empty($rating_data)) ? $rating_data : 0;
			}
			echo json_encode($response);
			exit;
		}
	}

	/*
		     * @name  		get_user_rating
		     * @access		public
		     * @package  	App/Controller/GroupsController
	*/

	public function get_user_rating() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$response = [
				'success' => false,
				'msg' => null,
				'content' => null,
			];
			$rating = [];
			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;
				// pr($post);
				$rating = $this->Common->feedbackRateAverage($post['user_id']);

				$response['success'] = true;

				$response['content'] = (isset($rating) && !empty($rating)) ? round($rating, 1) : 0;
			}
			echo json_encode($response);
			exit;
		}
	}

	function feedback_users_listing($feedback_id = '') {

		$this->layout = false;
		$this->loadModel('User');
		$orConditions = array();
		$andConditions = array();
		if (array_key_exists('q', $this->params->query) && $this->params->query['q'] != '') {
			$orConditions = array('OR' => array(
				'UserDetail.first_name LIKE' => '%' . $this->params->query['q'] . '%',
				'UserDetail.last_name LIKE' => '%' . $this->params->query['q'] . '%',
			));
		}

		if (array_key_exists('feedback_id', $this->params->query) && $this->params->query['feedback_id'] != '') {
			$feedback_id = $this->params->query['feedback_id'];
		}

		//// pr($this->params->query);
		$user_id = $this->Auth->User('id');
		$userids = array();
		//$userids[$user_id] = $user_id;
		if (isset($feedback_id) && !empty($feedback_id)) {
			$this->loadModel('FeedbackUser');
			$user_ids = $this->FeedbackUser->find('list', array('conditions' => array('FeedbackUser.feedback_id' => $feedback_id), 'fields' => array('FeedbackUser.user_id', 'FeedbackUser.user_id')));
		}
		if (isset($user_ids) && !empty($user_ids)) {
			$userids = array_merge($user_ids, $userids);
		}
		//// pr($user_ids);
		$andConditions = array_merge(array('User.status' => 1, 'User.role_id' => 2, 'User.id !=' => $userids), $orConditions);
		//// pr($user_ids);
		$users = $this->User->find('all', array('conditions' => $andConditions, 'fields' => array('User.id', 'UserDetail.first_name', 'UserDetail.last_name'), 'order' => array('UserDetail.first_name' => 'ASC')));
		$arr1 = array();
		//// pr($users);
		//// pr($andConditions,1);
		if (isset($users) && !empty($users)) {
			$i = 0;
			foreach ($users as $user) {
				$vv = $this->Common->feedbackRateAverage($user['User']['id']);
				if (empty($vv)) {
					$vv = 0;
				}
				$arr1[$i]['id'] = $user['User']['id'];
				$arr1[$i]['name1'] = " ";
				$arr1[$i]['itemid'] = $user['UserDetail']['first_name'] . ' ' . $user['UserDetail']['last_name'];
				$arr1[$i]['name'] = $user['UserDetail']['first_name'] . ' ' . $user['UserDetail']['last_name'] . " <span class='text-red'>(Rated : " . round($vv, 2) . ",&nbsp; <i class='fa fa-user'></i>&nbsp;)<span>";
				$i++;
			}
		}
		$arr1_tot = ( isset($arr1) && !empty($arr1) ) ? count($arr1) : 0;
		echo json_encode(array('total' => $arr1_tot, 'rows' => $arr1));
		die;
	}

	function group_users_listing($group_id = '', $feedback_id = '') {

		$this->layout = false;
		$this->loadModel('User');
		$orConditions = array();
		$andConditions = array();
		if (array_key_exists('q', $this->params->query) && $this->params->query['q'] != '') {
			$orConditions = array('OR' => array(
				'UserDetail.first_name LIKE' => '%' . $this->params->query['q'] . '%',
				'UserDetail.last_name LIKE' => '%' . $this->params->query['q'] . '%',
			));
		}

		if (array_key_exists('feedback_id', $this->params->query) && $this->params->query['feedback_id'] != '') {
			$feedback_id = $this->params->query['feedback_id'];
		}

		//// pr($this->params->query);
		$user_id = $this->Auth->User('id');
		$userids = $useridss = array();
		//$userids[$user_id] = $user_id;

		if (isset($feedback_id) && !empty($feedback_id)) {
			$this->loadModel('FeedbackUser');
			$useridss = $this->FeedbackUser->find('list', array('conditions' => array('FeedbackUser.feedback_id' => $feedback_id), 'fields' => array('FeedbackUser.user_id', 'FeedbackUser.user_id')));
		}

		if (isset($group_id) && !empty($group_id)) {
			$this->loadModel('ProjectGroupUser');
			$user_ids = $this->ProjectGroupUser->find('list', array('conditions' => array('ProjectGroupUser.project_group_id' => $group_id, 'ProjectGroupUser.approved' => 1), 'fields' => array('ProjectGroupUser.user_id', 'ProjectGroupUser.user_id')));
		}

		if (isset($user_ids) && !empty($user_ids)) {
			$userids = array_merge($user_ids, $userids);
		}

		//// pr($user_ids);
		$andConditions = array_merge(array('User.status' => 1, 'User.role_id' => 2, 'User.id' => $userids, 'User.id !=' => $useridss), $orConditions);
		//// pr($user_ids);
		$users = $this->User->find('all', array('conditions' => $andConditions, 'fields' => array('User.id', 'UserDetail.first_name', 'UserDetail.last_name'), 'order' => array('UserDetail.first_name' => 'ASC')));
		$arr1 = array();
		//// pr($users);

		if (isset($users) && !empty($users)) {
			$i = 0;
			foreach ($users as $user) {
				$vv = $this->Common->feedbackRateAverage($user['User']['id']);
				if (empty($vv)) {
					$vv = 0;
				}
				$arr1[$i]['id'] = $user['User']['id'];
				$arr1[$i]['name1'] = " ";
				$arr1[$i]['itemid'] = $user['UserDetail']['first_name'] . ' ' . $user['UserDetail']['last_name'];
				$arr1[$i]['name'] = $user['UserDetail']['first_name'] . ' ' . $user['UserDetail']['last_name'] . " <span class='text-red'>(Rated : " . round($vv, 2) . ",&nbsp; <i class='fa fa-user'></i>&nbsp;)<span>";
				$i++;
			}
		}
		$arr1_tot = ( isset($arr1) && !empty($arr1) ) ? count($arr1) : 0;
		echo json_encode(array('total' => $arr1_tot, 'rows' => $arr1));
		die;
	}

	public function view_feedback($feedback_id = '') {
		if (isset($feedback_id) && !empty($feedback_id)) {
			$this->loadModel('Feedback');
			$this->loadModel('FeedbackResult');
			$this->Feedback->recursive = 3;
			$this->Feedback->bindModel(array('hasMany' => array('FeedbackResult' => array('className' => 'FeedbackResult', 'order' => array('FeedbackResult.id' => 'DESC')), 'FeedbackAttachment' => array('className' => 'FeedbackAttachment', 'conditions' => array('FeedbackAttachment.status' => 0)))));
			$this->FeedbackResult->bindModel(array('belongsTo' => array('User')));
			$this->FeedbackResult->bindModel(array('hasMany' => array('FeedbackAttachment' => array('className' => 'FeedbackAttachment', 'conditions' => array('FeedbackAttachment.status' => 1)))));
			$feedback_details = $this->Feedback->find('first', array('conditions' => array('Feedback.id' => $feedback_id)));
			$this->set('feedback_details', $feedback_details);
			//  pr($feedback_details,1);
		}
	}

	public function feedback_update_users() {
		//// pr($this->request->data);
		$response['success'] = false;
		$response['content'] = '';
		//	pr($this->request->data['FeedbackUser']['list']);die;
		if (isset($this->request->data['FeedbackUser']['list']) && !empty($this->request->data['FeedbackUser']['list'])) {
			$this->loadModel('FeedbackUser');
			$this->FeedbackUser->set($this->request->data['FeedbackUser']);
			$feedback_id = $this->request->data['FeedbackUser']['feedback_id'];
			//$users = @explode(',', $this->request->data ['FeedbackUser']['list']);
			$users = $this->request->data['FeedbackUser']['list'];

			$voteuserData['FeedbackUser']['feedback_id'] = $feedback_id;
			//pr($users);die;
			$this->loadModel('User');
			$this->loadModel('Feedback');
			$voteDetails = $this->Feedback->find('first', array('conditions' => array('Feedback.id' => $feedback_id)));
			$voteTitle = $voteDetails['Feedback']['title'];
			// Get total options Feedback

			$view = new View();
			$ViewModel = $view->loadHelper('ViewModel');

			$project_id = $voteDetails['Feedback']['project_id'];
			$element_id = $voteDetails['Feedback']['element_id'];

			$ele_detail = $ViewModel->getElementDetails($element_id);
			$projectDetail = getByDbId('Project', $project_id, 'title');
			$workspaceDetail = getByDbId('Workspace', $ele_detail[0]['user_permissions']['workspace_id'], 'title');
			$elementDetail = getByDbId('Element', $element_id, 'title');

			$projectName = ( isset($projectDetail) && !empty($projectDetail['Project']['title']) )? $projectDetail['Project']['title'] : '';
			$workspaceName = ( isset($workspaceDetail) && !empty($workspaceDetail['Workspace']['title']) )? $workspaceDetail['Workspace']['title'] : '';
			$elementName = ( isset($elementDetail) && !empty($elementDetail['Element']['title']) )? $elementDetail['Element']['title'] : '';
			$requestAction = SITEURL.'entities/feedback_request';


			$data = array();
			$Arrdata = array();
			if (isset($users) && !empty($users)) {
				if (($key = array_search($this->Session->read('Auth.User.id'), $users)) !== false) {
					unset($users[$key]);
				}
			}
			foreach ($users as $userId) {
				$voteuserData['FeedbackUser']['user_id'] = $userId;
				if (!$this->FeedbackUser->find('count', array('conditions' => array('FeedbackUser.user_id' => $userId, 'FeedbackUser.feedback_id' => $feedback_id)))) {
					$voteuserData['FeedbackUser']['id'] = '';
					$voteuserData['FeedbackUser']['updated_user_id'] = $this->Auth->user('id');

					if ($this->FeedbackUser->save($voteuserData, false)) {

						$usersDetails = $this->User->find('first', array('conditions' => array('User.id' => $userId)));
						$name = $this->Session->read('Auth.User.UserDetail.first_name') . ' ' . $this->Session->read('Auth.User.UserDetail.last_name');
						$fname = $usersDetails['UserDetail']['full_name'];
						$loggedInemail = $this->Session->read('Auth.User.email');

						$notifiInterest = $this->EmailNotification->find('first', ['conditions' => ['notification_type' => 'element', 'personlization' => 'feedback_invitation_request', 'user_id' => $userId]]);

						if ((!isset($notifiInterest['EmailNotification']['email']) || $notifiInterest['EmailNotification']['email'] == 1) && (!isset($usersDetails['User']['email_notification']) || $usersDetails['User']['email_notification'] == 1)) {

							$emailAddress = $usersDetails['User']['email'];
							$email = new CakeEmail();
							$email->config('Smtp');
							// $email->from(array($loggedInemail => $name));
							$email->from(array(ADMIN_FROM_EMAIL => SITENAME));
							//$email->from(array(ADMIN_FROM_EMAIL => $name));
							$email->to($emailAddress);
							$email->subject(SITENAME . ": Feedback invitation request");
							$email->template('feedback_invitation_email');
							$email->emailFormat('html');
							$email->viewVars(array('Custname' => $fname, 'feedbackTitle' => $voteTitle, 'name' => $name,'projectName' => $projectName,'workspaceName' => $workspaceName,'elementName' => $elementName,'open_page' => $requestAction));
							$email->send();

						}

						$data['id'] = $usersDetails['UserDetail']['user_id'];
						$data['name'] = $fname;
						$Arrdata[] = $data;

					}
				}
			}

			/************** socket messages **************/
			if (SOCKET_MESSAGES) {
				$project_id = element_project($voteDetails['Feedback']['element_id']);
				$current_user_id = $this->user_id;
				$r_users = $users;
				if (isset($r_users) && !empty($r_users)) {
					if (($key = array_search($current_user_id, $r_users)) !== false) {
						unset($r_users[$key]);
					}
				}
				$open_users = null;
				if (isset($r_users) && !empty($r_users)) {
					foreach ($r_users as $key1 => $value1) {
						if (web_notify_setting($value1, 'element', 'feedback_invitation_request')) {
							$open_users[] = $value1;
						}
					}
				}
				$userDetail = get_user_data($current_user_id);
				$content = [
					'notification' => [
						'type' => 'feedback_invitation',
						'created_id' => $current_user_id,
						'project_id' => $project_id,
						// 'refer_id' => null,
						'creator_name' => $userDetail['UserDetail']['full_name'],
						'subject' => 'Feedback invitation',
						'heading' => 'Feedback: ' . strip_tags(getFieldDetail('Feedback', $feedback_id, 'title')),
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
			$response['success'] = true;
			$response['date'] = date('d M,Y');
			$response['content'] = $this->FeedbackUser->find('count', array('conditions' => array('FeedbackUser.feedback_id' => $feedback_id)));
			$response['users'] = $Arrdata;
		}
		echo json_encode($response);
		exit();
	}

	public function reminder_feedback_user($feedback_id = '') {
		if (isset($feedback_id) && !empty($feedback_id)) {
			// Get No Response votes
			$usersEmails = $this->Common->getFeedbacksUsersListToSendReminder($feedback_id);
			//pr($usersEmails, 1);
			if (isset($usersEmails) && !empty($usersEmails)) {
				$this->loadModel('Feedback');
				$feedbackDetails = $this->Feedback->find('first', array('conditions' => array('Feedback.id' => $feedback_id)));

				$view = new View();
				$ViewModel = $view->loadHelper('ViewModel');

				$project_id = $feedbackDetails['Feedback']['project_id'];
				$element_id = $feedbackDetails['Feedback']['element_id'];

				$ele_detail = $ViewModel->getElementDetails($element_id);
				$projectDetail = getByDbId('Project', $project_id, 'title');
				$workspaceDetail = getByDbId('Workspace', $ele_detail[0]['user_permissions']['workspace_id'], 'title');
				$elementDetail = getByDbId('Element', $element_id, 'title');

				$projectName = ( isset($projectDetail) && !empty($projectDetail['Project']['title']) )? $projectDetail['Project']['title'] : '';
				$workspaceName = ( isset($workspaceDetail) && !empty($workspaceDetail['Workspace']['title']) )? $workspaceDetail['Workspace']['title'] : '';
				$elementName = ( isset($elementDetail) && !empty($elementDetail['Element']['title']) )? $elementDetail['Element']['title'] : '';
				$requestAction = SITEURL.'entities/feedbacks/'.$feedbackDetails['Feedback']['id'];

				$feedbackTitle = $feedbackDetails['Feedback']['title'];
				foreach ($usersEmails as $usersDetails) {
					$name = $this->Session->read('Auth.User.UserDetail.first_name') . ' ' . $this->Session->read('Auth.User.UserDetail.last_name');

					$notifiInterest = $this->EmailNotification->find('first', ['conditions' => ['notification_type' => 'element', 'personlization' => 'feedback_reminder', 'user_id' => $usersDetails['User']['id']]]);

					if ((!isset($notifiInterest['EmailNotification']['email']) || $notifiInterest['EmailNotification']['email'] == 1) && (!isset($usersDetails['User']['email_notification']) || $usersDetails['User']['email_notification'] == 1)) {

						// Send Emails to user
						$loggedInemail = $this->Session->read('Auth.User.email');
						$emailAddress = $usersDetails['User']['email'];
						$userId = $usersDetails['User']['id'];
						$email = new CakeEmail();
						$email->config('Smtp');
						// $email->from(array($loggedInemail => $name));
						//$email->from(array(ADMIN_FROM_EMAIL => $name));
						$email->from(array(ADMIN_FROM_EMAIL => SITENAME));
						$email->to($emailAddress);
						//$email->subject('Feedback Invitation Reminder');
						$email->subject(SITENAME . ": Feedback reminder");
						$email->template('feedback_reminder_email');
						$email->emailFormat('html');
						$email->viewVars(array('Custname' => $usersDetails['UserDetail']['first_name'] . ' ' . $usersDetails['UserDetail']['last_name'], 'feedbackTitle' => $feedbackTitle, 'name' => $name,'projectName' => $projectName,'workspaceName' => $workspaceName,'elementName' => $elementName,'open_page' => $requestAction));
						if ($this->Common->check_email_permission($userId, 'feedback_reminder', 'element') == true) {
							$email->send();
						}

					}

				}

				/************** socket messages **************/
				if (SOCKET_MESSAGES) {
					$project_id = element_project($feedbackDetails['Feedback']['element_id']);
					$current_user_id = $this->user_id;
					$fdusers = Set::extract($usersEmails, '/User/id');
					$r_users = $fdusers;
					if (isset($r_users) && !empty($r_users)) {
						if (($key = array_search($current_user_id, $r_users)) !== false) {
							unset($r_users[$key]);
						}
					}
					$open_users = null;
					if (isset($r_users) && !empty($r_users)) {
						foreach ($r_users as $key1 => $value1) {
							if (web_notify_setting($value1, 'element', 'feedback_reminder')) {
								$open_users[] = $value1;
							}
						}
					}
					$userDetail = get_user_data($current_user_id);
					$content = [
						'notification' => [
							'type' => 'feedback_reminder',
							'created_id' => $current_user_id,
							'project_id' => $project_id,
							'refer_id' => $feedback_id,
							'creator_name' => $userDetail['UserDetail']['full_name'],
							'subject' => 'Feedback reminder',
							'heading' => 'Feedback: ' . strip_tags(getFieldDetail('Feedback', $feedback_id, 'title')),
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
				die('success');
			}
		}
		die('error');
	}

	public function update_feedback($feedback_id = null) {
		//echo $vote_id; die;
		if ($this->request->isAjax()) {
			//   // pr($this->request->data);die;
			$response = [
				'success' => false,
				'action' => null,
				'content' => null,
			];

			$this->layout = 'ajax';
			$this->autoRender = false;
			$this->loadModel('Feedback');
			$this->loadModel('FeedbackUser');
			if ($this->request->is('post') || $this->request->is('put')) {
				//	 // pr($this->request->data, 1);
				$id = null;
				$action = '';
				// // pr($this->request->data);die;

				if (isset($this->request->data['Feedback']) && !empty($this->request->data['Feedback'])) {
					$this->Feedback->set($this->request->data['Feedback']);
					if ($this->Feedback->validates()) {
						$this->request->data['Feedback']['user_id'] = $this->Auth->User('id');
						if (isset($this->request->data['Feedback']['start_date'])) {
							$this->request->data['Feedback']['start_date'] = date('Y-m-d HH:ii:ss', strtotime($this->request->data['Feedback']['start_date']));
						}

						if (isset($this->request->data['Feedback']['end_date'])) {
							$this->request->data['Feedback']['end_date'] = date('Y-m-d HH:ii:ss', strtotime($this->request->data['Feedback']['end_date']));
						}

						if(isset($this->request->data['Feedback']['end_date']) && !empty($this->request->data['Feedback']['end_date']) && $this->request->data['Feedback']['end_date'] < $this->request->data['Feedback']['start_date']){

							$this->request->data['Feedback']['start_date'] = $this->request->data['Feedback']['end_date'];
						}

						$this->request->data['Feedback']['user_id'] = $this->Auth->User('id');
						$this->request->data['Feedback']['updated_user_id'] = $this->Auth->User('id');

						$this->request->data['Feedback']['create_activity'] = true;
						if ($this->Feedback->save($this->request->data['Feedback'])) {

							if (isset($this->request->data['Feedback']['id']) && !empty($this->request->data['Feedback']['id'])) {
								$id = $this->request->data['Feedback']['id'];
								$action = 'update';
							} else {
								$id = $this->Feedback->getLastInsertId();
								$action = 'create';
							}

							$this->Feedback->updateAll(
								array("Feedback.create_activity" => 0),
								array("Feedback.id" => $id)
							);

							// Update elemnt task activity.
							// $this->update_task_activity($this->request->data['Feedback']['element_id']);

							// Get Project Id with Element id; Update Project modified date

							$this->request->data['Element']['id'] = $this->request->data['Feedback']['element_id'];
							$this->request->data['Element']['updated_user_id'] = $this->Auth->user('id');
							// $this->Element->save($this->request->data['Element']);

							$element_note = null;
							if (isset($id) && !empty($id)) {
								$element_note = $this->Feedback->find('first', [
									'conditions' => [
										'Feedback.id' => $id,
									],
								]);

								if (!is_null($element_note)) {
									// $element_note['ElementNote']['created'] = dateFormat($element_note['ElementNote']['created']);
									$element_note['Feedback']['created'] = date('d M, Y g:iA', strtotime($element_note['Feedback']['created']));

									// $element_note['ElementNote']['modified'] = dateFormat($element_note['ElementNote']['modified']);

									$element_note['Feedback']['modified'] = date('d M, Y g:iA', strtotime($element_note['Feedback']['modified']));
								}
							}

							$response['success'] = true;
						}
					} else {
						$response['content'] = $this->validateErrors($this->Feedback);
					}
				} else if (isset($this->request->data['FeedbackUser']) && !empty($this->request->data['FeedbackUser'])) {

					//// pr($this->request->data);die;
					$this->FeedbackUser->set($this->request->data['FeedbackUser']);

					if ($this->FeedbackUser->validates()) {
						$feedback_id = $this->request->data['FeedbackUser']['vote_id'];
						if (isset($vote_id) && !empty($vote_id)) {
							$this->FeedbackUser->deleteAll(array('FeedbackUser.vote_id' => $vote_id));
						}
						$users = @explode(',', $this->request->data['FeedbackUser']['list']);
						$voteuserData['FeedbackUser']['vote_id'] = $vote_id;
						//// pr($users);die;
						foreach ($users as $userId) {
							$this->FeedbackUser->create();
							$voteuserData['FeedbackUser']['user_id'] = $userId;
							$this->FeedbackUser->save($voteuserData);
						}
						$response['success'] = true;
					} else {
						$response['content'] = $this->validateErrors($this->VoteUser);
					}
				}
			}

			echo json_encode($response);
			exit();
		}
	}

	/**
	 * Update background color code of an element in workspace->areas page
	 *
	 * @param
	 *        	$element_id
	 *
	 * @return void
	 */
	public function cancel_feedback() {
		if ($this->request->is('ajax')) {

			$response = [
				'success' => false,
				'msg' => '',
				// 'content' => null
			];

			$this->layout = 'ajax';
			$this->autoRender = false;

			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;
				// // pr($post, 1);

				if (isset($post['feedback_id']) && !empty($post['feedback_id'])) {

					$id = $post['feedback_id'];
					$element_id = $post['element_id'];
					$this->loadModel('FeedbackUser');
					$checkFeedbackUser = $this->FeedbackUser->find('count', array(
						'conditions' => array('FeedbackUser.feedback_id' => $id)
					));
					if($checkFeedbackUser == 0) {
						$this->Feedback->id = $id;

						if ($this->Feedback->deleteAll([
							'Feedback.id' => $id,
						], true)) {
							$this->loadModel('Activity');
							if($this->Activity->deleteAll(array('Activity.relation_id' => $id, 'Activity.element_id' => $element_id ))){

							}
							$this->loadModel('FeedbackAttachment');
							$getFeedbackAttachment = $this->FeedbackAttachment->find('all', array(
								'conditions' => array('FeedbackAttachment.feedback_id' => $id)
							));
							if(!empty($getFeedbackAttachment)) {
								foreach($getFeedbackAttachment as $k => $attachment) {pr($attachment);
									$upload_path = WWW_ROOT . ELEMENT_DOCUMENT_PATH . $element_id . DS . 'feedbacks' . DS . $id ;
									$filepath = $upload_path . DS . $attachment['FeedbackAttachment']['file_name'];
									if (file_exists($filepath)) {
										unlink($filepath);
									}
								}
								@rmdir($upload_path);
								$this->FeedbackAttachment->deleteAll([
									'FeedbackAttachment.feedback_id' => $id,
								], true);
							}
							$this->request->data['Element']['id'] = $element_id;
							$this->request->data['Element']['updated_user_id'] = $this->Auth->user('id');
							// $this->Element->save($this->request->data['Element']);

							// Get Project Id with Element id; Update Project modified date
							// $this->update_project_modify($element_id);
							// $this->update_task_activity($element_id);
							$response['success'] = true;
							$response['msg'] = 'Element feedback has been deleted successfully.';
							// $response ['content'] = [];
						} else {
							$response['msg'] = 'Element feedback could not deleted successfully.';
						}
					}
				}
			}

			echo json_encode($response);
			exit();
		}
	}

	/**
	 * Update background color code of an element in workspace->areas page
	 *
	 * @param
	 *        	$element_id
	 *
	 * @return void
	 */
	public function remove_feedback() {
		if ($this->request->is('ajax')) {

			$response = [
				'success' => false,
				'msg' => '',
				// 'content' => null
			];

			$this->layout = 'ajax';
			$this->autoRender = false;

			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;
				// // pr($post, 1);

				if (isset($post['feedback_id']) && !empty($post['feedback_id'])) {

					$id = $post['feedback_id'];
					$element_id = $post['element_id'];

					$this->Feedback->id = $id;

					$dids = $this->Feedback->findById($id);

					if ($this->Feedback->deleteAll([
						'Feedback.id' => $id,
					], true)) {
						$this->request->data['Element']['id'] = $element_id;
						$this->request->data['Element']['updated_user_id'] = $this->Auth->user('id');


						$defaultElementid = $element_id;

						$task_data = [
						'project_id' => element_project($defaultElementid),
						'workspace_id' => element_workspace($defaultElementid),
						'element_id' => $defaultElementid,
						'element_type' => 'feedback',
						'user_id' => $this->user_id,
						'relation_id' => $dids['Feedback']['id'],
						'updated_user_id' => $this->user_id,
						'message' => 'Feedback deleted',
						'updated' => date("Y-m-d H:i:s"),
						];
						$this->loadModel('Activity');
						$this->Activity->id = null;
						$this->Activity->save($task_data);


						// $this->Element->save($this->request->data['Element']);

						// Get Project Id with Element id; Update Project modified date
						// $this->update_project_modify($element_id);
						// $this->update_task_activity($element_id);
						$response['success'] = true;
						$response['msg'] = 'Element feedback has been deleted successfully.';
						// $response ['content'] = [];
					} else {
						$response['msg'] = 'Element feedback could not deleted successfully.';
					}
				}
			}

			echo json_encode($response);
			exit();
		}
	}

	function feedbacks($feedback_id) {
		$this->set('title_for_layout', __('Feedback Request Detail', true));
		$this->layout = 'inner';
		$this->loadModel('Feedback');
		$this->loadModel('FeedbackUser');
		$this->loadModel('FeedbackResult');
		$this->Feedback->id = $feedback_id;
		$conditions = array(
			'FeedbackUser.user_id' => $this->Session->read('Auth.User.id'),
			'FeedbackUser.feedback_id' => $feedback_id,
		);

		if (!$this->Feedback->exists() || !$this->FeedbackUser->hasAny($conditions)) {
			$this->Session->setFlash(__('This Feedback request
has been removed.'), 'error');
			$this->redirect(array('action' => 'feedback_request'));
		}
		$user_id = $this->Auth->User('id');
		$this->Feedback->recursive = 2;

		$this->Feedback->bindModel([
			'belongsTo' => [
				'Project' => [
					'className' => 'Project',
					'fields' => ['Project.id', 'Project.title']
				],
				'Owner' => [
					'className' => 'User',
					'foreignKey' => 'user_id',
					'fields' => ['Owner.id']
				]
			]
		]);
		$this->Feedback->Owner->bindModel([
			'hasOne' => [
				'UserDetail' => [
					'className' => 'UserDetail',
					'fields' => ['UserDetail.id', 'UserDetail.user_id', 'UserDetail.first_name', 'UserDetail.last_name']
				],
			]
		]);

		$this->Feedback->Project->unbindModel([
			'belongsTo' => ['Category'],
			'hasMany' => [
				'ProjectWorkspace',
				'UserProject',
				'ProjectSkill',
				'ElementPermission',
				'ProjectElementType'
			]
		]);

		$this->Feedback->Owner->unbindModel([
			'hasOne' => ['UserInstitution', 'OrganisationUser'],
			'hasMany' => [
				'ProjectPermission',
				'WorkspacePermission',
				'ElementPermission',
				'UserProject',
				'UserPlan',
				'UserTransctionDetail',
				'UserSetting',
				'UserPassword',
			],
			'hasAndBelongsToMany' => ['Skill'],
		]);

		$this->Feedback->bindModel(array('hasMany' => array('FeedbackResult' => array('className' => 'FeedbackResult', 'conditions' => array('FeedbackResult.user_id' => $user_id), 'order' => array('FeedbackResult.id' => 'DESC')), 'FeedbackAttachment' => array('className' => 'FeedbackAttachment', 'conditions' => array('FeedbackAttachment.status' => 0)))));

		//$this->FeedbackResult->bindModel(array('hasMany'=>array('FeedbackAttachment'=>array('className'=>'FeedbackAttachment', 'foreignKey'   => 'feedback_id','associatedKey'   => 'feedback_id','conditions'=>array('FeedbackAttachment.user_id'=>$user_id, 'FeedbackAttachment.status'=>1)))));
		$this->FeedbackResult->bindModel(array('hasMany' => array('FeedbackAttachment' => array('className' => 'FeedbackAttachment', 'conditions' => array('FeedbackAttachment.user_id' => $user_id, 'FeedbackAttachment.status' => 1)))));

		$feedbacks = $this->Feedback->find('first', array('conditions' => array('Feedback.id' => $feedback_id)));

		$this->set('feedbacks', $feedbacks);
		//pr($feedbacks);die;

		$crumb = ['last' => [$feedbacks['Project']['title']]];

		if (isset($feedback_id) && !empty($feedback_id)) {

			$crumb1 = [
				'Project' => [
					'data' => [
						'url' => '/entities/feedback_request',
						'class' => 'tipText',
						'title' => "Feedback Requests",
						'data-original-title' => "Feedback Requests",
					],
				],
			];
			$crumb = array_merge($crumb1, $crumb);
		}

		$this->set('crumb', $crumb);
	}

	public function feedback_save() {
		if (isset($this->data) && !empty($this->data)) {

			$this->request->data['FeedbackResult']['user_id'] = $this->Auth->User('id');
			$this->request->data['FeedbackResult']['feedback'] = trim($this->request->data['FeedbackResult']['feedback']);
			$this->loadModel('FeedbackResult');
			$this->request->data['FeedbackResult']['feedback_change_datetime'] = time();
			if (isset($this->data['Feedback']['feedback_change_freq']) && !empty($this->data['Feedback']['feedback_change_freq'])) {
				$feedback_change_freq = $this->data['Feedback']['feedback_change_freq'];
				$this->request->data['FeedbackResult']['vote_change_datetime'] = strtotime("+$feedback_change_freq hours", time());
			}

			// pr($this->data);die;
			if ($this->FeedbackResult->save($this->data)) {
				$feedback_result_id = $this->FeedbackResult->getLastInsertId();
				$unique_key = $this->data['FeedbackAttachment']['feedback_result_id'];
				$this->loadModel('FeedbackAttachment');
				$this->FeedbackAttachment->updateAll(array('FeedbackAttachment.feedback_result_id' => $feedback_result_id), array('FeedbackAttachment.feedback_result_id' => $unique_key));
				$user_id = $this->Auth->User('id');
				$this->loadModel('Feedback');
				$this->loadModel('FeedbackUser');
				$feedback_id = $this->request->data['FeedbackResult']['feedback_id'];
				// delete Declined feedback
				$this->FeedbackResult->deleteAll(array('FeedbackResult.feedback_id' => $feedback_id, 'FeedbackResult.user_id' => $user_id, 'FeedbackResult.is_decline' => 1));

				$FeedbackUser = $this->FeedbackUser->find('first', array('conditions' => array('FeedbackUser.feedback_id' => $feedback_id, 'FeedbackUser.user_id' => $user_id)));
				if (isset($FeedbackUser) && !empty($FeedbackUser)) {
					$ArrFeedbackData['FeedbackUser']['id'] = $FeedbackUser['FeedbackUser']['id'];
					$ArrFeedbackData['FeedbackUser']['feedback_id'] = $feedback_id;
					$ArrFeedbackData['FeedbackUser']['user_id'] = $user_id;
					$ArrFeedbackData['FeedbackUser']['feedback_status'] = 1;

					$this->FeedbackUser->save($ArrFeedbackData, false);
					//// pr($ArrFeedbackData); die;
				}

				$feedback = $this->data['FeedbackResult']['feedback'];
				// Feedback Email Send to feedback Creater
				$feedback_by = $this->Session->read('Auth.User.UserDetail.first_name') . ' ' . $this->Session->read('Auth.User.UserDetail.last_name');
				// Send Emails to user
				$loggedInemail = $this->Session->read('Auth.User.email');
				$feedbackDetails = $this->Feedback->find('first', array('conditions' => array('Feedback.id' => $feedback_id)));

				$view = new View();
				$ViewModel = $view->loadHelper('ViewModel');

				$project_id = $feedbackDetails['Feedback']['project_id'];
				$element_id = $feedbackDetails['Feedback']['element_id'];
				$ele_detail = $ViewModel->getElementDetailswsp($element_id);

				$elid = $feedbackDetails['Feedback']['element_id'];
				$workspace_id = $ele_detail[0]['user_permissions']['workspace_id'];
				$task_data = [
					'project_id' => $project_id,
					'workspace_id' => $workspace_id,
					'element_id' => $elid,
					'relation_id' => $feedback_id,
					'element_type' => 'feedback',
					'user_id' => $this->user_id,
					'updated_user_id' => $this->user_id,
					'message' => 'Feedback received',
					'updated' => date("Y-m-d H:i:s"),
				];
				$this->loadModel('Activity');
				$this->Activity->id = null;
				$this->Activity->save($task_data);

				$projectDetail = getByDbId('Project', $project_id, 'title');
				$workspaceDetail = getByDbId('Workspace', $ele_detail[0]['user_permissions']['workspace_id'], 'title');
				$elementDetail = getByDbId('Element', $element_id, 'title');

				$projectName = ( isset($projectDetail) && !empty($projectDetail['Project']['title']) )? $projectDetail['Project']['title'] : '';
				$workspaceName = ( isset($workspaceDetail) && !empty($workspaceDetail['Workspace']['title']) )? $workspaceDetail['Workspace']['title'] : '';
				$elementName = ( isset($elementDetail) && !empty($elementDetail['Element']['title']) )? $elementDetail['Element']['title'] : '';
				$requestAction = SITEURL.'entities/update_element/'.$element_id.'#feedbacks';


				$feedbackTitle = $feedbackDetails['Feedback']['title'];
				$owner_id = $feedbackDetails['Feedback']['user_id'];
				$usersDetails = $this->User->find('first', array('conditions' => array('User.id' => $owner_id)));
				$feedback_byEmail = $this->Session->read('Auth.User.email');
				$emailAddress = $usersDetails['User']['email'];
				$userId = $usersDetails['User']['id'];
				$email = new CakeEmail();
				$email->config('Smtp');
				// $email->from(array($loggedInemail => $feedback_by));
				$email->from(array(ADMIN_FROM_EMAIL => $feedback_by));
				$email->to($emailAddress);
				//$email->subject("Feedback by $feedback_by on $feedbackTitle");
				$email->subject(SITENAME . ": Feedback received");
				$email->template('feedback_done_email');
				$email->emailFormat('html');
				$email->viewVars(array('Custname' => $usersDetails['UserDetail']['first_name'] . ' ' . $usersDetails['UserDetail']['last_name'], 'feedbackTitle' => $feedbackTitle, 'feedback' => $feedback, 'feedback_by' => $feedback_by,'projectName' => $projectName,'workspaceName' => $workspaceName,'elementName' => $elementName,'open_page' => $requestAction));
				if ($this->Common->check_email_permission($userId, 'feedback_received', 'element') == true) {
					$email->send();
				}

				/************** socket messages **************/
				if (SOCKET_MESSAGES) {
					$project_id = element_project($feedbackDetails['Feedback']['element_id']);
					$current_user_id = $this->user_id;
					$send_notification = false;
					if (web_notify_setting($owner_id, 'element', 'feedback_received')) {
						$send_notification = true;
					}
					$userDetail = get_user_data($current_user_id);
					$content = [
						'notification' => [
							'type' => 'feedback_received',
							'created_id' => $current_user_id,
							'project_id' => $project_id,
							'refer_id' => $element_id,
							'creator_name' => $userDetail['UserDetail']['full_name'],
							'subject' => 'Feedback received',
							'heading' => 'Feedback: ' . strip_tags(getFieldDetail('Feedback', $feedback_id, 'title')),
							'sub_heading' => 'Project: ' . strip_tags(getFieldDetail('Project', $project_id, 'title')),
							'date_time' => timezoneTimeConvertor($this->user_id, date('Y-m-d g:iA'), $format = 'd M Y g:iA') . user_country($this->user_id),
						],
					];
					if ($send_notification) {
						$content['received_users'] = [$owner_id];
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

				$this->Session->setFlash(__('Your feedback has been submitted successfully.'), 'success');
				//$this->redirect(array('action'=>'feedback_save'));
			}
		}
		$this->redirect(array('action' => 'feedback_request'));
	}

	public function decline_feedback($feedback_id = '') {
		$this->loadModel('FeedbackResult');
		$this->loadModel('FeedbackUser');
		$this->loadModel('Feedback');
		$feedbacks = $this->Feedback->find('first', array('conditions' => array('Feedback.id' => $feedback_id)));
		$ArrData['FeedbackResult']['feedback_id'] = $feedback_id;
		$ArrData['FeedbackResult']['user_id'] = $this->Auth->User('id');
		$ArrData['FeedbackResult']['is_decline'] = 1;

		if (isset($feedbacks['Feedback']['feedback_change_freq']) && !empty($feedbacks['Feedback']['feedback_change_freq'])) {
			$feedback_change_freq = $feedbacks['Feedback']['feedback_change_freq'];
			$ArrData['FeedbackResult']['feedback_change_datetime'] = strtotime("+$feedback_change_freq hours", time());
		}

		if ($this->FeedbackResult->save($ArrData)) {
			$FeedbackUser = $this->FeedbackUser->find('first', array('conditions' => array('FeedbackUser.feedback_id' => $feedback_id, 'FeedbackUser.user_id' => $this->Auth->User('id'))));
			if (isset($FeedbackUser) && !empty($FeedbackUser)) {
				$ArrVoteData['FeedbackUser']['id'] = $FeedbackUser['FeedbackUser']['id'];
				$ArrVoteData['FeedbackUser']['feedback_id'] = $feedback_id;
				$ArrVoteData['FeedbackUser']['user_id'] = $this->Auth->User('id');
				$ArrVoteData['FeedbackUser']['feedback_status'] = 2; // rejected
				$this->FeedbackUser->save($ArrVoteData);

				$elid = $feedbacks['Feedback']['element_id'];
				$project_id = element_project($elid);
				$workspace_id = element_workspace($elid);
				$task_data = [
						'project_id' => $project_id,
						'workspace_id' => $workspace_id,
						'element_id' => $elid,
						'relation_id' => $feedback_id,
						'element_type' => 'feedback',
						'user_id' => $this->user_id,
						'updated_user_id' => $this->user_id,
						'message' => 'Feedback declined',
						'updated' => date("Y-m-d H:i:s"),
					];
					$this->loadModel('Activity');
					$this->Activity->id = null;
					$this->Activity->save($task_data);
			}
		}
		$this->redirect(array('controller' => 'entities', 'action' => 'feedback_request'));
	}

	public function add_feedback_doc($element_id = null) {
		if ($this->request->isAjax()) {

			$response = [
				'success' => false,
				'msg' => '',
				'content' => null,
			];
			$this->loadModel('FeedbackAttachment');
			$this->layout = 'ajax';
			$this->autoRender = false;

			if ($this->request->is('post') || $this->request->is('put')) {
				// // pr($this->request->data, 1);
				$check_file = 1;

				$this->request->data['FeedbackAttachment']['user_id'] = $this->Auth->user('id');
				$feedback_id = '';
				if (isset($this->request->data['FeedbackAttachment']['feedback_id']) && empty($this->request->data['FeedbackAttachment']['feedback_id'])) {
					if (isset($this->request->data['extension_valid']) && empty($this->request->data['extension_valid'])) {

						$check_file = ($this->request->data['extension_valid'] > 1) ? 1 : 2;
					}
				} else {
					$feedback_id = $this->request->data['FeedbackAttachment']['feedback_id'];
					if (isset($this->request->data['extension_valid']) && empty($this->request->data['extension_valid'])) {
						$check_file = ($this->request->data['extension_valid'] > 1) ? 1 : 2;
					}
				}

				$sizeLimit = 10 * 1024 * 1024; // 5MB
				$folder_url = WWW_ROOT . ELEMENT_DOCUMENT_PATH;
				$result = $fileNewName = $upload_object = $upload_detail = null;

				if ($check_file == true) {

					$upload_object = (isset($_FILES["file_name"])) ? $_FILES["file_name"] : null;

					$folder_url .= DS . $element_id;

					if ($upload_object) {
						if (!file_exists($folder_url)) {
							mkdir($folder_url, 0777, true);
						}
						if (isset($feedback_id) && !empty($feedback_id)) {
							$folder_url .= DS . 'feedbacks' . DS . $feedback_id;
							if (!file_exists($folder_url)) {
								mkdir($folder_url, 0777, true);
							}
						}
						// for($i = 0, $n = count ( $upload_object ['name'] ); $i < $n; $i ++) { }

						$sizeMB = 0;
						$sizeStr = "";
						$sizeKB = $upload_object['size'] / 1024;
						// // pr($upload_object);
						// // pr(($sizeKB/(1024)), 1);
						if (($sizeKB) > 1024) {
							$sizeMB = $sizeKB / 1024;
							$sizeStr = number_format($sizeMB, 2) . " MB";
						} else {
							$sizeStr = number_format($sizeKB, 2) . " KB";
						}

						//$nameArr = explode(".", $upload_object ['name']);

						$fileExt = substr($upload_object['name'], strripos($upload_object['name'], '.') + 1);
						$fileName = substr($upload_object['name'], 0, strripos($upload_object['name'], '.'));

						//$fileName = (!empty($nameArr) && isset($nameArr [0])) ? $nameArr [0] : '';
						//$fileExt = (!empty($nameArr) && isset($nameArr)) ? $nameArr [1] : '';

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
								$last_row = $this->FeedbackAttachment->find('first', array(
									'recursive' => '-1',
									'fields' => [
										'id',
									],
									'order' => 'FeedbackAttachment.id DESC',
								));
								if (isset($last_row) && !empty($last_row)) {
									$fileNewName = $fileName;
									$fileNewName .= '-' . ($last_row['FeedbackAttachment']['id'] + 1);
									$fileNewName .= '.' . $fileExt;
								}
							} else {
								$fileNewName .= $orgFileName;
							}

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
							$response['msg'] = "File size limit exceeded,Please upload a file upto 10MB.";
						}
					}
				}

				if (isset($upload_detail) && !empty($upload_detail)) {
					$this->request->data['FeedbackAttachment']['file_name'] = $upload_detail['name'];
					$this->request->data['FeedbackAttachment']['file_size'] = $upload_detail['size'];
					$this->request->data['FeedbackAttachment']['file_type'] = $upload_detail['type'];
				} else {
					unset($this->request->data['FeedbackAttachment']['file_name']);
					unset($this->request->data['FeedbackAttachment']['file_size']);
					unset($this->request->data['FeedbackAttachment']['file_type']);
				}

				if (isset($check_file) && !empty($check_file) && $check_file == 1) {

					$this->FeedbackAttachment->set($this->request->data['FeedbackAttachment']);

					if ($this->FeedbackAttachment->validates()) {
						//// pr($this->request->data,1);
						if ($this->FeedbackAttachment->save($this->request->data['FeedbackAttachment'])) {
							$id = $this->FeedbackAttachment->getLastInsertId();
							// Get Project Id with Element id; Update Project modified date
							// $this->update_project_modify($element_id);
							$element_doc = null;
							if (isset($id) && !empty($id)) {
								$element_doc = $this->FeedbackAttachment->find('first', [
									'conditions' => [
										'FeedbackAttachment.id' => $id,
									],
								]);
							}

							$response['success'] = true;
							$response['msg'] = "Success";
							$response['content'] = $element_doc;
						} else {
							$response['msg'] = "Error!!!";
						}
					} else {
						$response['content'] = $this->validateErrors($this->FeedbackAttachment);
					}
				}
			}

			echo json_encode($response);
			exit();
		}
	}

	public function download_feedback_doc($id = null) {
		if (isset($id) && !empty($id)) {
			$this->loadModel('FeedbackAttachment');
			// Retrieve the file ready for download
			$data = $this->FeedbackAttachment->findById($id);
			if (empty($data)) {
				throw new NotFoundException();
			}

			// Send file as response
			$upload_path = ELEMENT_DOCUMENT_PATH . DS . $data['FeedbackAttachment']['element_id'] . DS . 'feedbacks' . DS . $data['FeedbackAttachment']['feedback_id'] . DS . $data['FeedbackAttachment']['file_name'];

			$this->response->file($upload_path, array(
				'download' => true,
				'name' => $data['FeedbackAttachment']['file_name'],
			));
			return $this->response;
		}
	}

	public function move_doc_to_document() {

		if (isset($this->data) && !empty($this->data)) {

			$attachment_id = $this->data['attachment_id'];
			$doc_content = $this->data['doc_content'];

			$this->loadModel('FeedbackAttachment');
			$this->loadModel('ElementDocument');

			$FeedbackAttachments = $this->FeedbackAttachment->find('first', array('conditions' => array('FeedbackAttachment.id' => $attachment_id)));

			if (isset($FeedbackAttachments) && !empty($FeedbackAttachments)) {

				$element_id = $FeedbackAttachments['FeedbackAttachment']['element_id'];

				$dataArr['ElementDocument']['element_id'] = $element_id;
				$dataArr['ElementDocument']['creater_id'] = $this->Session->read('Auth.User.id');
				$dataArr['ElementDocument']['title'] = $doc_content;

				//pr($dataArr['ElementDocument']['title']);

				$dimg = explode('.', $FeedbackAttachments['FeedbackAttachment']['file_name']);
				//$fname = $dimg['0'] . '_' . rand() . '.' . $dimg['1'];
				$fname = $dimg['0'] . '.' . $dimg['1'];
				$dataArr['ElementDocument']['file_name'] = $fname;
				$dataArr['ElementDocument']['file_size'] = $FeedbackAttachments['FeedbackAttachment']['file_size'];
				$dataArr['ElementDocument']['file_type'] = $FeedbackAttachments['FeedbackAttachment']['file_type'];
				$dataArr['ElementDocument']['feedback_attachments_id'] = $attachment_id;
				$dataArr['ElementDocument']['status'] = 1;

				$folder_url = WWW_ROOT . ELEMENT_DOCUMENT_PATH;
				$folder_url .= $element_id;

				$file = $folder_url . DS . 'feedbacks' . DS . $FeedbackAttachments['FeedbackAttachment']['feedback_id'] . DS . $FeedbackAttachments['FeedbackAttachment']['file_name'];

				$newfile = "";

				$nFile = $folder_url . DS . $FeedbackAttachments['FeedbackAttachment']['file_name'];

				if (!file_exists($folder_url)) {
					mkdir($folder_url, 0777, true);
				}

				// if file not exists only then create new name
				if (file_exists($nFile)) {
					$newfile = $folder_url . DS . $fname;
				} else {
					$newfile = $nFile;
				}

				if (!$this->ElementDocument->find('count', array('conditions' => array('ElementDocument.feedback_attachments_id' => $attachment_id)))) {

					if (copy($file, $newfile)) {

						if ($this->ElementDocument->save($dataArr)) {

							$e_id = $this->ElementDocument->getLastInsertId();

							$element_doc = $this->ElementDocument->find('first', array('conditions' => array('ElementDocument.id' => $e_id)));

							$creator_id = $this->Session->read('Auth.User.id');

							$element_doc['ElementDocument']['creator'] = $this->Common->elementDoc_creator($e_id, $FeedbackAttachments['FeedbackAttachment']['project_id'], $creator_id);

							$response['success'] = true;
							$response['msg'] = "Success";
							$response['content'] = $element_doc;

							echo json_encode($response);
							die;
						}
					}
				} else {
					$ElementDocumentData = $this->ElementDocument->find('first', array('conditions' => array('ElementDocument.feedback_attachments_id' => $attachment_id)));
					$title = $ElementDocumentData['ElementDocument']['title'];
					$response['success'] = false;
					$response['msg'] = "This document is already saved in documents with <b>$title</b> title.";
					$response['content'] = '';
					echo json_encode($response);
					die;
				}
			}
		}
		$response['success'] = false;
		$response['msg'] = "There is some technical problem to add document. Please try again.";
		$response['content'] = '';
		echo json_encode($response);
		die;
	}

	function rate_feedback() {
		$response['success'] = 0;
		$response['update'] = 0;
		if (isset($this->data) && !empty($this->data)) {
			// pr($this->data );
			$dataArr['FeedbackRating']['feedback_result_id'] = $this->data['fbr_id'];
			$dataArr['FeedbackRating']['feedback_id'] = $this->data['feedback_id'];
			$dataArr['FeedbackRating']['given_by_id'] = $this->data['creater_id'];
			$dataArr['FeedbackRating']['given_to_id'] = $this->data['user_id'];
			$dataArr['FeedbackRating']['rate'] = $this->data['rating'];
			$dataArr['FeedbackRating']['comment'] = $this->data['comment'];
			$this->loadModel('FeedbackRating');
			$is_exists = $this->FeedbackRating->find('first', array('conditions' => array('FeedbackRating.feedback_id' => $this->data['feedback_id'], 'FeedbackRating.feedback_result_id' => $this->data['fbr_id'], 'FeedbackRating.given_by_id' => $this->data['creater_id'], 'FeedbackRating.given_to_id' => $this->data['user_id'])));
			//pr($is_exists); die;
			if (!empty($is_exists)) {
				$dataArr['FeedbackRating']['id'] = $is_exists['FeedbackRating']['id'];
				$response['update'] = 1;
			}
			if ($this->FeedbackRating->save($dataArr)) {
				$response['success'] = 1;
			}
		}
		echo json_encode($response);
		die;
	}

	public function rate_userdetails($user_id = '') {
		$this->loadModel('User');
		$userDetails = $this->User->findById($user_id);
		$this->request->data = $userDetails;
		$this->set('user_details', $userDetails);
	}

	public function participants_feedbackuser($feedback_id = '') {
		if (isset($feedback_id) && !empty($feedback_id)) {
			$this->loadModel('Feedback');
			$this->loadModel('FeedbackUser');
			$this->Feedback->recursive = 3;
			$this->Feedback->bindModel(array('hasMany' => array('FeedbackUser')));
			$this->Feedback->bindModel(array('hasMany' => array('FeedbackResult')));
			$this->FeedbackUser->bindModel(array('belongsTo' => array('User')));
			$detail = $this->Feedback->find('first', array('conditions' => array('Feedback.id' => $feedback_id), 'order' => array('Feedback.id' => 'desc')));
			//pr($detail);die;
			$this->set('detail', $detail);
		}
	}

	/*     * *********** Feedback ************* */

	public function participants_voteuser($vote_id = '') {
		if (isset($vote_id) && !empty($vote_id)) {
			$this->loadModel('Vote');
			//$data = $this->Vote->findById($vote_id);// pr($data);
			/* $this->set('data', $data['Vote']);
	              $this->set('users', $data['VoteUser']);
		*/
			$this->loadModel('Vote');
			$this->loadModel('VoteUser');
			$this->Vote->recursive = 3;
			$this->Vote->bindModel(array('hasMany' => array('VoteResult')));
			$this->VoteUser->bindModel(array('belongsTo' => array('User')));
			$this->set('detail', $this->Vote->find('first', array('conditions' => array('Vote.id' => $vote_id), 'order' => array('Vote.id' => 'desc'))));
		}
	}

	public function get_history() {
		$this->layout = false;
		$this->autoRender = false;
		if ($this->request->is('ajax') || $this->request->is('get')) {
			$id = isset($this->params['pass'][0]) && !empty($this->params['pass'][0]) ? $this->params['pass'][0] : '';
			$type = isset($this->params['pass'][1]) && !empty($this->params['pass'][1]) ? $this->params['pass'][1] : '';

			$this->loadModel("Activity");
			$this->loadModel("UserDetail");
			$this->Activity->bindModel(array(
				"belongsTo" => array(
					"UserDetail" => array(
						"class" => "UserDetail",
						"foreignKey" => false,
						"conditions" => array("Activity.updated_user_id = UserDetail.user_id"),
					),
				),
			));
			//pr($type);

			$history_lists = $this->Activity->find("all", array(
				"conditions" => array(
					"Activity.relation_id" => $id,
					"Activity.element_type" => $type,
				),
				'limit' => 25,
				'order' => "Activity.id DESC",
				"group" => "DATE_FORMAT(Activity.updated,'%Y-%m-%d %h:%i:%s')"));

			//pr($history_lists);
			$type = str_replace("_", " ", $this->params['pass'][1]);
			$type_1 = $this->params['pass'][1];
			$this->set(compact("id", "type", "type_1", "history_lists"));
			if ($type_1 != 'element_tasks') {
				$this->render("activity/update_history");
			} else {
				return $history_lists;
			}
		}

	}
	public function more_history() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$model = 'Activity';

			if ($this->request->is('post') || $this->request->is('put')) {
				$this->pagination['page'] = 10;
				$this->loadModel("Activity");
				$this->loadModel("UserDetail");
				$this->Activity->bindModel(array(
					"belongsTo" => array(
						"UserDetail" => array(
							"class" => "UserDetail",
							"foreignKey" => false,
							"conditions" => array("Activity.updated_user_id = UserDetail.user_id"),
						),
					),
				));

				//
				$history_list = $this->Activity->find('count', [
					'conditions' => [
						"Activity.relation_id" => $this->request->data['element_id'],
						"Activity.element_type" => $this->request->data['element_tasks'],
					],
					'order' => 'Activity.id DESC',
					"group" => "DATE_FORMAT(Activity.updated,'%Y-%m-%d %h:%i:%s')"]);
				$t = ($history_list / 10);

				if (isset($history_list) && !empty($history_list) && ($this->params['named']['page'] <= ceil(($history_list / 10)))) {

					$paginator = array(
						'conditions' => array(
							$model . ".relation_id" => $this->request->data['element_id'],
							$model . ".element_type" => $this->request->data['element_tasks'],
						),
						"joins" => array(
							array(
								'alias' => 'UserDetail',
								'table' => 'user_details',
								'type' => 'INNER',
								'conditions' => 'Activity.updated_user_id = UserDetail.user_id',
							),
						),
						'fields' => ['Activity.*', 'UserDetail.*'],
						'limit' => 10,
						"group" => "DATE_FORMAT(Activity.updated,'%Y-%m-%d %h:%i:%s')",
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
				$this->set('element_id', $this->request->data['element_id']);
				$this->set('element_tasks', $this->request->data['element_tasks']);
				$this->render('/Entities/activity/update_filter_history');
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
			$type = isset($this->params['named']['type']) && !empty($this->params['named']['type']) ? $this->params['named']['type'] : '';
			$type = strtolower($type);
			$filterbyhistory = isset($this->params['named']['filterbyhistory']) && !empty($this->params['named']['filterbyhistory']) ? $this->params['named']['filterbyhistory'] : '';
			$seeactivityhistory = isset($this->params['named']['seeactivityhistory']) && !empty($this->params['named']['seeactivityhistory']) ? $this->params['named']['seeactivityhistory'] : '';

			$this->loadModel("Activity");
			$this->loadModel("UserDetail");
			//pr($this->params['named']);
			$this->Activity->bindModel(array(
				"belongsTo" => array(
					"UserDetail" => array(
						"class" => "UserDetail",
						"foreignKey" => false,
						"conditions" => array("Activity.updated_user_id = UserDetail.user_id"),
					),
				),
			));

			if ($seeactivityhistory == 'all') {
				$conditions = array("Activity.relation_id" => $id, "Activity.element_type" => $type);
			} else if ($seeactivityhistory == 'today') {
				$start = date('Y-m-d');
				$end = date('Y-m-d');
				$conditions = array(
					"Activity.relation_id" => $id,
					"Activity.element_type" => $type,
					'date(Activity.updated) BETWEEN ? AND ?' => array($start, $end),
				);
			} else if ($seeactivityhistory == 'last_7_day') {
				$end = date('Y-m-d');
				$start = date('Y-m-d', strtotime('-7 day'));
				$conditions = array(
					"Activity.relation_id" => $id,
					"Activity.element_type" => $type,
					'date(Activity.updated) BETWEEN ? AND ?' => array($start, $end),
				);
			} else if ($seeactivityhistory == 'this_month') {
				$end = date('Y-m-t');
				$start = date('Y-m-01');
				$conditions = array(
					"Activity.relation_id" => $id,
					"Activity.element_type" => $type,
					'date(Activity.updated) BETWEEN ? AND ?' => array($start, $end),
				);
			}

			$history_lists = $this->Activity->find("all", array(
				"conditions" => $conditions,
				'order' => "Activity.id DESC",
				//"group" => "DATE_FORMAT(Activity.updated,'%Y-%m-%d %h:%i')",
			)
			);
			//pr($history_lists);
			$type = str_replace("_", " ", $type);
			$type_1 = $this->params['named']['type'];

			$this->set(compact("id", "type", "type_1", "filterbyhistory", "seeactivityhistory", "history_lists"));
			$this->render("activity/update_filter_history");
		}
	}

	public function delete_feedback_document() {
		$this->loadModel("FeedbackAttachment");

		if ($this->request->is('ajax')) {

			$response = [
				'success' => false,
				'msg' => '',
			];

			$this->layout = 'ajax';
			$this->autoRender = false;

			if ($this->request->is('post') || $this->request->is('put')) {

				$id = $this->request->data['feedback_attachments_id'];

				if (isset($id) && !empty($id)) {

					$feedbackdetail = $this->FeedbackAttachment->find('first', array('conditions' => array('FeedbackAttachment.id' => $id)));

					$element_id = $feedbackdetail['FeedbackAttachment']['element_id'];
					$feedback_id = $feedbackdetail['FeedbackAttachment']['feedback_id'];
					$feedback_filename = $feedbackdetail['FeedbackAttachment']['file_name'];

					$this->FeedbackAttachment->delete(['FeedbackAttachment.id' => $id], true);

					$upload_path = WWW_ROOT . ELEMENT_DOCUMENT_PATH . $element_id . DS . 'feedbacks' . DS . $feedback_id . DS . $feedback_filename;
					if (file_exists($upload_path)) {
						unlink($upload_path);
					}
					$response['success'] = true;
					$response['msg'] = 'Feedback document has been deleted successfully.';

				} else {
					$response['msg'] = 'Feedback document could not deleted successfully.';
				}

			}

			echo json_encode($response);
			exit();
		}

	}

	public function elementDeleteEmail($elementName = null, $project_id = null, $all_owner = null,$workspace_id = null) {

		$view = new View();
		$commonHelper = $view->loadHelper('Common');
		$ViewModel = $view->loadHelper('ViewModel');

		//$pageAction = SITEURL . 'projects/manage_elements/' . $project_id.'/'.$wsp_id;

		$all_owner = array_unique(array_filter($all_owner));

		$projectName = $this->Project->find('first',array('conditions'=>array('Project.id'=>$project_id),'recursive'=>-1,'fields' => 'Project.title'));
		$workspaceDetail = getByDbId('Workspace', $workspace_id, 'title');
		$pageAction = SITEURL . 'projects/manage_elements/' . $project_id.'/'.$workspace_id;

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
					if (isset($usersDetails) && !empty($usersDetails)) {
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

							// $owner_name = $usersDetails['UserDetail']['first_name'] . ' ' . $usersDetails['UserDetail']['last_name'];
							$owner_name = $fullFName . $fullLName;
						}

						$deletedUser = $this->User->findById($this->Session->read('Auth.User.id'));
						$loggedInUser = $deletedUser['UserDetail']['first_name'] . ' ' . $deletedUser['UserDetail']['last_name'];

						$dasAnnotate = $this->EmailNotification->find('first', ['conditions' => ['notification_type' => 'element', 'personlization' => 'element_deleted', 'user_id' => $valData]]);

						if ((!isset($dasAnnotate['EmailNotification']['email']) || $dasAnnotate['EmailNotification']['email'] == 1) && (!isset($usersDetails['User']['email_notification']) || $usersDetails['User']['email_notification'] == 1)) {

							$email = new CakeEmail();
							$email->config('Smtp');
							// $email->from(array(ADMIN_EMAIL => SITENAME));
							$email->from(array(ADMIN_FROM_EMAIL => SITENAME));
							$email->to($usersDetails['User']['email']);
							$email->subject(SITENAME . ': Task deleted');
							$email->template('element_delete_email');
							$email->emailFormat('html');
							$email->viewVars(array('elementName' => $elementName, 'owner_name' => $owner_name, 'deletedby' => $loggedInUser, 'projectName' => $projectName['Project']['title'], 'workspaceName' => $workspaceDetail['Workspace']['title'],'open_page'=>$pageAction));
							$email->send();
						}
					}
				}
			}
		}
	}

	public function elementSignOffEmail($elementName = null, $project_id = null, $all_owner = null, $emai_type = 'signed-off',$element_id = null) {

		$view = new View();
		$commonHelper = $view->loadHelper('Common');
		$viewModel = $view->loadHelper('ViewModel');

		$elementAction = SITEURL.'entities/update_element/'.$element_id.'#tasks';
		$ele_detail = $viewModel->getElementDetails($element_id);
		$projectDetail = getByDbId('Project', $ele_detail[0]['user_permissions']['project_id'], 'title');
		$workspaceDetail = getByDbId('Workspace', $ele_detail[0]['user_permissions']['workspace_id'], 'title');
		$projectName = ( isset($projectDetail) && !empty($projectDetail['Project']['title']) )? $projectDetail['Project']['title'] : '';
		$workspaceName = ( isset($workspaceDetail) && !empty($workspaceDetail['Workspace']['title']) )? $workspaceDetail['Workspace']['title'] : '';

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

					$email_subject = $emai_type;
					if ($emai_type == 're-opened') {
						$dasAnnotate = $this->EmailNotification->find('first', ['conditions' => ['notification_type' => 'element', 'personlization' => 'element_reopened', 'user_id' => $valData]]);
					} else {
						$dasAnnotate = $this->EmailNotification->find('first', ['conditions' => ['notification_type' => 'element', 'personlization' => 'element_sign_off', 'user_id' => $valData]]);
						$email_subject = 'sign-off';
					}

					if ((!isset($dasAnnotate['EmailNotification']['email']) || $dasAnnotate['EmailNotification']['email'] == 1) && (!isset($usersDetails['User']['email_notification']) || $usersDetails['User']['email_notification'] == 1)) {

						if ($this->Session->read('Auth.User.id') != $valData) {
							$email = new CakeEmail();
							$email->config('Smtp');
							// $email->from(array(ADMIN_EMAIL => SITENAME));
							$email->from(array(ADMIN_FROM_EMAIL => SITENAME));
							$email->to($usersDetails['User']['email']);
							$email->subject(SITENAME . ': Task ' . $email_subject);
							$email->template('element_signoff_email');
							$email->emailFormat('html');
							$email->viewVars(array('projectName' => $projectName,'workspaceName' => $workspaceName,'elementName' => $elementName, 'owner_name' => $owner_name, 'signoffby' => $loggedInUser, 'mail_type' => $emai_type, 'open_page' => $elementAction));
							$email->send();
						}

					}

				}

			}
		}
		//die;
	}

	public function elementScheduleChangeEmail($elementName = null, $all_owner = null, $element_id = null) {

		$view = new View();
		$commonHelper = $view->loadHelper('Common');
		$viewModel = $view->loadHelper('ViewModel');

		$all_owner = array_unique(array_filter($all_owner));

		$elementAction = SITEURL.'entities/update_element/'.$element_id.'#tasks';
		$ele_detail = $viewModel->getElementDetails($element_id);
		$projectDetail = getByDbId('Project', $ele_detail[0]['user_permissions']['project_id'], 'title');
		$workspaceDetail = getByDbId('Workspace', $ele_detail[0]['user_permissions']['workspace_id'], 'title');
		$projectName = ( isset($projectDetail) && !empty($projectDetail['Project']['title']) )? $projectDetail['Project']['title'] : '';
		$workspaceName = ( isset($workspaceDetail) && !empty($workspaceDetail['Workspace']['title']) )? $workspaceDetail['Workspace']['title'] : '';
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

					$dasAnnotate = $this->EmailNotification->find('first', ['conditions' => ['notification_type' => 'element', 'personlization' => 'element_schedule_change', 'user_id' => $valData]]);

					if ((!isset($dasAnnotate['EmailNotification']['email']) || $dasAnnotate['EmailNotification']['email'] == 1) && (!isset($usersDetails['User']['email_notification']) || $usersDetails['User']['email_notification'] == 1)) {

						$email = new CakeEmail();
						$email->config('Smtp');
						// $email->from(array(ADMIN_EMAIL => SITENAME));
						$email->from(array(ADMIN_FROM_EMAIL => SITENAME));
						$email->to($usersDetails['User']['email']);
						$email->subject(SITENAME . ': Task schedule change');
						$email->template('element_schedule_change_email');
						$email->emailFormat('html');
						$email->viewVars(array('projectName' => $projectName,'workspaceName' => $workspaceName,'element_name' => $elementName, 'owner_name' => $owner_name, 'changedBy' => $loggedInUser, 'elementAction' => $elementAction, 'open_page' => $elementAction));
						$email->send();

					}
				}
			}
		}
	}


	public function elementScheduleOverdueEmailCron() {
		$this->layout = false;
		$this->autoRender = false;
		$this->loadModel('UserPermission');
		$view = new View();
		$viewModel = $view->loadHelper('ViewModel');

		$this->Element->unbindModel(
						array('hasMany' => array(
							'Links','Documents','Notes','Mindmaps'
						),
						)
					);
		$this->Element->unbindModel(
						array('belongsTo' => array(
							'Links','Documents','Notes','Mindmaps'
						),
						)
					);

		$sql1 = "SELECT

							Element.title as title,
							Element.id as id,
							Element.end_date as end_date

						FROM user_permissions

							INNER JOIN
								elements as Element
								ON Element.id=user_permissions.element_id and 	(DATE(Element.end_date)<DATE(NOW()))  and Element.sign_off =0
								group by Element.id";


						$elementList = $this->UserPermission->query($sql1);



		if (isset($elementList) && !empty($elementList) && count($elementList) > 0) {

			$currentDate = date('Y-m-d');
			foreach ($elementList as $listall) {

				//================= Overdue Days ====================================
				$daysleft = daysLeft(date('Y-m-d', strtotime($listall['Element']['end_date'])), $currentDate);

				if ($daysleft == 1) {

					$element_id = $listall['Element']['id'];
					$elementAction = SITEURL . 'entities/update_element/' . $element_id.'#tasks';
					$elementName = '';
					if (isset($listall['Element']['title']) && !empty($listall['Element']['title'])) {
						$elementName = $listall['Element']['title'];
					}

					$ele_detail = $viewModel->getElementDetails($element_id);
					$projectDetail = getByDbId('Project', $ele_detail[0]['user_permissions']['project_id'], 'title');
					$workspaceDetail = getByDbId('Workspace', $ele_detail[0]['user_permissions']['workspace_id'], 'title');

					$projectName = ( isset($projectDetail) && !empty($projectDetail['Project']['title']) )? $projectDetail['Project']['title'] : '';
					$workspaceName = ( isset($workspaceDetail) && !empty($workspaceDetail['Workspace']['title']) )? $workspaceDetail['Workspace']['title'] : '';

					$sql = "SELECT

							user_permissions.role,
							user_permissions.user_id,
							user_permissions.element_id,
							user_permissions.project_id,
							users.email as email,
							users.email_notification as email_notification,
							user_details.first_name as firstName,
							user_details.last_name as lastName

						FROM user_permissions

							INNER JOIN
								elements
								ON elements.id=user_permissions.element_id
							INNER JOIN
								users
								ON users.id=user_permissions.user_id
							INNER JOIN
								user_details
								ON user_details.user_id=users.id
							WHERE
								user_permissions.element_id=$element_id";


						$all_owner = $this->UserPermission->query($sql);

					if (isset($all_owner) && !empty($all_owner)) {
						foreach ($all_owner as $valData) {
							$user_permissions = $valData['user_permissions'];
							$project_id = $valData['user_permissions']['project_id'];
							$users = $valData['users'];
							$usersDetails = $valData['user_details'];

							$notifiragstatus = $this->EmailNotification->find('first', ['conditions' => ['notification_type' => 'element', 'personlization' => 'element_schedule_overdue', 'user_id' => $user_permissions['user_id']]]);

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
								// $email->from(array(ADMIN_EMAIL => SITENAME));
								$email->from(array(ADMIN_FROM_EMAIL => SITENAME));
								$email->to($users['email']);
								$email->subject(SITENAME . ': Task schedule overdue');
								$email->template('element_schedule_overdue');
								$email->emailFormat('html');
								$email->viewVars(array('projectName' => $projectName,'workspaceName' => $workspaceName,'elementName' => $elementName, 'owner_name' => $owner_name, 'elementAction' => $elementAction, 'open_page' => $elementAction));
								$email->send();
							}

							/************** socket messages **************/
							if (SOCKET_MESSAGES) {
								$r_users = [];
								$r_users[] = $user_permissions['user_id'];

								$open_users = null;
								if (isset($r_users) && !empty($r_users)) {
									foreach ($r_users as $key1 => $value1) {
										// $user_permissions = $value1['user_permissions'];
										if (web_notify_setting($user_permissions['user_id'], 'element', 'element_schedule_overdue')) {
											$open_users[] = $user_permissions['user_id'];
										}
									}
								}
								$open_users = array_unique($open_users);

								$content = [
									'notification' => [
										'type' => 'task_overdue',
										'created_id' => '',
										'project_id' => $project_id,
										'refer_id' => $element_id,
										'creator_name' => '',
										'subject' => 'Task schedule overdue',
										'heading' => 'Task: ' . $elementName,
										'sub_heading' => 'Project: ' . strip_tags(getFieldDetail('Project', $project_id, 'title')),
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
	}

	public function task_assignment($element_id = null) {
		$response = ['success' => false];
		if ($this->request->isAjax()) {
			$this->layout = false;

			$element_detail = getByDbId('Element', $element_id);
			$this->set('element_detail', $element_detail);

			$element_assigned = element_assigned($element_id);
			$this->set('element_assigned', $element_assigned);

			$this->render('/Entities/partials/task_assignment');
		}
	}

	public function task_assignment_button() {
		$response = ['success' => false];
		if ($this->request->isAjax()) {
			$this->layout = false;

			$element_id = null;
			$html = null;

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				$element_id = $post['element_id'];
			}

			$view = new View($this, false);
			$view->viewPath = 'Entities/partials'; // Directory inside view directory to search for .ctp files
			$view->layout = false; // if you want to disable layout
			$view->set('element_id', $element_id);

			$html = $view->render('task_assignment_button');
			echo json_encode($html);
			exit();
		}
	}

	public function task_assignment_image() {

		if ($this->request->isAjax()) {
			$this->layout = false;
			//$this->autoRender = false;
			$element_id = null;
			$html = null;

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				$element_id = $post['element_id'];
				$current_user_id = $post['current_user_id'];
				//$project_id = $post['project_id'];
			}

			//$view = new View($this, false);
			//$view->viewPath = 'Entities/partials'; // Directory inside view directory to search for .ctp files

			$this->set('element_id', $element_id);
			$this->set('current_user_id', $current_user_id);
			$this->set('project_id', element_project($element_id));

			//$html = $view->render('task_image_assigned');
 			$this->render('/Entities/partials/task_image_assigned');
			//echo json_encode($html);
			//exit();
		}
	}


	public function update_activity($data = null) {

		$workspace_id = element_workspace($data['element_id']);

		$this->loadModel("Activity");
		$detail = [
			'project_id' => $data['project_id'],
			'workspace_id' => $workspace_id,
			'element_id' => $data['element_id'],
			'element_type' => 'element_tasks',
			'relation_id' => $data['element_id'],
			'user_id' => $this->user_id,
			'updated_user_id' => $this->user_id,
			'user_status' => '0',
			'message' => $data['message'],
			'updated' => date('Y-m-d H:i:s'),
		];
		$this->Activity->save($detail);
		// ClassRegistry::init('ProjectActivity')->save($project_data);
	}
	public function save_assignment() {
		$response = ['success' => false, 'content' => null, 'removed' => null, 'newuser' => false, 'removeuser' => false];

		if ($this->request->isAjax()) {
			$this->layout = false;

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				// pr($post, 1);
				$project_id = element_project($post['element_id']);
				$eid = $post['element_id'];
				$saveData = $removev_user = null;
				$remove = false;
				$newAssignee = false;
				if (isset($post['type']) && !empty($post['type'])) {
					$type = $post['type'];
					//pr($post);
					if ($type == 'myself') {
						$saveData = ['id' => $post['id'], 'reaction' => $post['reaction']];

					} else if ($type == 'sendto') {
						$element_assigned = null;
						$saveData = ['assigned_to' => $post['assigned_to'], 'created_by' => $post['created_by'], 'element_id' => $post['element_id'],'reaction' => 0];
						if (isset($post['id']) && !empty($post['id'])) {
							$saveData['id'] = $post['id'];
							$element_assigned = element_assigned($post['element_id']);
							if ($element_assigned['ElementAssignment']['assigned_to'] != $post['assigned_to']) {
								$saveData['reaction'] = '0';
								// assignee changed without remove previous user

								if (isset($post['assigned_to']) && !empty($post['assigned_to'])) {
									$newAssignee = true;
									$removev_user = $element_assigned['ElementAssignment']['assigned_to'];
								}
							}
						}
						else{
							$act = ['project_id' => $project_id, 'element_id' => $post['element_id'], 'message'=> 'Task assigned'];
							$this->update_activity($act);
						}
						if (!isset($post['assigned_to']) || empty($post['assigned_to'])) {
							$remove = true;
							if (isset($post['id']) && !empty($post['id'])) {
								$remove_assign = getByDbId('ElementAssignment', $post['id']);
								$removev_user = $remove_assign['ElementAssignment']['assigned_to'];
							}
						}
						// cross icon is clicked
						/*if ((isset($element_assigned) && !empty($element_assigned)) && (!isset($post['assigned_to']) || empty($post['assigned_to']))) {
							$remove = true;
							$remove_assign = getByDbId('ElementAssignment', $post['id']);
							$removev_user = $remove_assign['ElementAssignment']['assigned_to'];
						}*/
					} else if ($type == 'received') {
						$saveData = ['id' => $post['id'], 'reaction' => $post['reaction']];
					}



					// Get Element Project ID

					$saveData['project_id'] = $project_id;

					if (isset($post['assigned_to']) && !empty($post['assigned_to'])) {
						$act = ['project_id' => $project_id, 'element_id' => $post['element_id'], 'message'=> 'Task assigned'];
						$this->update_activity($act);
					}

					// if a assignee is selected save it
					// otherwise remove this element's data

					//pr($saveData); die;
					if (!$remove) {
						if ($this->ElementAssignment->save($saveData)) {

							if($saveData['reaction']==1){
								$query = "UPDATE element_efforts SET change_hours = '0' WHERE element_id = '$eid'  AND is_active=1";
								$this->Element->query($query);
							}
							$response['success'] = true;
							if ($type == 'sendto') {
								/************** socket messages **************/
								if (SOCKET_MESSAGES) {
									if ($post['assigned_to'] != $this->user_id) {
										$send_notification = false;
										if (web_notify_setting($post['assigned_to'], 'element', 'assignment')) {
											$send_notification = true;
										}
										$userDetail = get_user_data($post['created_by']);
										$content = [
											'socket' => [
												'notification' => [
													'type' => 'assignment',
													'created_id' => $post['created_by'],
													'project_id' => $project_id,
													'refer_id' => $post['element_id'],
													'creator_name' => $userDetail['UserDetail']['full_name'],
													'subject' => 'Task assigned',
													'heading' => 'Task: ' . strip_tags(getFieldDetail('Element', $post['element_id'], 'title')),
													'sub_heading' => 'Project: ' . strip_tags(getFieldDetail('Project', $project_id, 'title')),
													'date_time' => timezoneTimeConvertor($this->user_id, date('Y-m-d g:iA'), $format = 'd M Y g:iA') . user_country($this->user_id),
												],
											],
										];
										if ($send_notification) {
											$content['socket']['received_users'] = [$post['assigned_to']];
										}
										$response['content'] = $content;
									}
								}
								/************** socket messages **************/
								$this->sendEmailNotification($post['assigned_to'], 'assignment', $post['element_id']);
								$response['newuser'] = $post['assigned_to'];
							}
						}
					} else {

						if (isset($saveData['id']) && !empty($saveData['id'])) {
							$previous_data = $this->ElementAssignment->find('first', ['conditions' => ['ElementAssignment.id' => $saveData['id']]]);

							if ($this->ElementAssignment->delete($saveData['id'])) {
								$response['success'] = true;

								/************** socket messages **************/
								if (SOCKET_MESSAGES) {
									if ($previous_data['ElementAssignment']['assigned_to'] != $this->user_id) {
										$send_notification = false;
										if (web_notify_setting($previous_data['ElementAssignment']['assigned_to'], 'element', 'assignment_removed')) {
											$send_notification = true;
										}
										$userDetail = get_user_data($previous_data['ElementAssignment']['created_by']);
										$content = [
											'socket' => [
												'notification' => [
													'type' => 'assignment_removed',
													'created_id' => $previous_data['ElementAssignment']['created_by'],
													'project_id' => $project_id,
													'refer_id' => $previous_data['ElementAssignment']['element_id'],
													'creator_name' => $userDetail['UserDetail']['full_name'],
													'subject' => 'Assignment removed',
													'heading' => 'Task: ' . strip_tags(getFieldDetail('Element', $previous_data['ElementAssignment']['element_id'], 'title')),
													'sub_heading' => 'Project: ' . strip_tags(getFieldDetail('Project', $project_id, 'title')),
													'date_time' => timezoneTimeConvertor($this->user_id, date('Y-m-d g:iA'), $format = 'd M Y g:iA') . user_country($this->user_id),
												],
											],
										];
										if ($send_notification) {
											$content['socket']['received_users'] = [$previous_data['ElementAssignment']['assigned_to']];
										}
										$response['content'] = $content;
									}
								}
								/************** socket messages **************/
								$this->sendEmailNotification($removev_user, 'assignment_removed', $post['element_id']);
								$response['removeuser'] = $removev_user;
							}
						}
					}
					if ($newAssignee) {
						/************** socket messages **************/
						if (SOCKET_MESSAGES) {
							// if ($previous_data['ElementAssignment']['assigned_to'] != $this->user_id) {
							$previous_data = $this->ElementAssignment->find('first', ['conditions' => ['ElementAssignment.id' => $post['id']]]);
							$send_notification = false;
							if (web_notify_setting($removev_user, 'element', 'assignment_removed')) {
								$send_notification = true;
							}
							$userDetail = get_user_data($previous_data['ElementAssignment']['created_by']);
							$content = [
								'notification' => [
									'type' => 'assignment_removed',
									'created_id' => $previous_data['ElementAssignment']['created_by'],
									'project_id' => $project_id,
									'refer_id' => $previous_data['ElementAssignment']['element_id'],
									'creator_name' => $userDetail['UserDetail']['full_name'],
									'subject' => 'Assignment removed',
									'heading' => 'Task: ' . strip_tags(getFieldDetail('Element', $previous_data['ElementAssignment']['element_id'], 'title')),
									'sub_heading' => 'Project: ' . strip_tags(getFieldDetail('Project', $project_id, 'title')),
									'date_time' => timezoneTimeConvertor($this->user_id, date('Y-m-d g:iA'), $format = 'd M Y g:iA') . user_country($this->user_id),
								],
							];
							if ($send_notification) {
								$content['received_users'] = [$removev_user];
							}
							$response['removed'] = $content;
							// }
						}
						// pr($removev_user, 1);
						/************** socket messages **************/
						$this->sendEmailNotification($removev_user, 'assignment_removed', $post['element_id']);
						$response['removeuser'] = $removev_user;
					}
				}
					$view = new View();
					$Permission = $view->loadHelper('Permission');
					$currentUserTasks = $Permission->currentUserTasks();

					$response['myTcount'] = $currentUserTasks;
			}
		}
		echo json_encode($response);
		exit;
	}

	public function element_dependancy_critical_old($element_id = null) {

		$response = ['success' => false];
		if ($this->request->isAjax()) {
			$this->layout = false;
			$this->set('element_id', $element_id);
			$this->render('/Entities/partials/element_dependancy_critical');
		}

	}


	public function element_dependancy_critical($element_id = null) {
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			$response = null;
			$this->autoRender = false;
			if (isset($element_id) && !empty($element_id)) {
				// Get all workspace elements
				$element = $this->Element->findById($element_id);
				$response['workspace_id'] = $element['Area']['workspace_id'];
				$response['area_id'] = $element['Area']['id'];
				// pr($element, 1);
				$response['defaultElementID'] = $element_id;
				$response['all_elements'] = $element;
			}

			$this->set('response', $response);
			$this->set('elementid', $element_id);
			$this->render('/Entities/element_files/task_list_el_date_depend');
		}
	}


	// Assignment Email Notification
	public function sendEmailNotification($user_id = null, $assinmentType = null, $task_id = null) {

		if (!isset($user_id) || empty($user_id)) {
			return true;
		}

		$assignedby = $this->Session->read('Auth.User.UserDetail.first_name') . ' ' . $this->Session->read('Auth.User.UserDetail.last_name');
		$loggedInemail = $this->Session->read('Auth.User.email');

		$usersDetails = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
		$emailAddress = $usersDetails['User']['email'];

		$project_id = element_project($task_id);
		$projectDetail = project_detail($project_id);
		$taskName = $this->Element->find('first', array('conditions' => array('Element.id' => $task_id)));


		$view = new View();
		$ViewModel = $view->loadHelper('ViewModel');
		$ele_detail = $ViewModel->getElementDetails($task_id);
		$workspaceDetail = getByDbId('Workspace', $ele_detail[0]['user_permissions']['workspace_id'], 'title');
		$projectName = ( isset($projectDetail) && !empty($projectDetail['title']) )? $projectDetail['title'] : '';
		$workspaceName = ( isset($workspaceDetail) && !empty($workspaceDetail['Workspace']['title']) )? $workspaceDetail['Workspace']['title'] : '';
		$requestAction = SITEURL.'entities/update_element/'.$task_id.'#tasks';


		$notifiInterest = $this->EmailNotification->find('first', ['conditions' => ['notification_type' => 'element', 'personlization' => $assinmentType, 'user_id' => $user_id]]);
		if ((!isset($notifiInterest['EmailNotification']['email']) || $notifiInterest['EmailNotification']['email'] == 1) && (!isset($usersDetails['User']['email_notification']) || $usersDetails['User']['email_notification'] == 1)) {

			$email = new CakeEmail();
			$email->config('Smtp');
			$email->smtp = true;
			// $email->from(array($loggedInemail => $assignedby));
			// $email->from(array(ADMIN_EMAIL => SITENAME));
			$email->from(array(ADMIN_FROM_EMAIL => SITENAME));
			$email->to($emailAddress);
			// $email->to("rajpurohitganpat@gmail.com");
			if ($assinmentType == 'assignment_removed') {
				$email->subject(SITENAME . ": Task assignment removed");
				$email->template('task_assigned_removed');
			} else {
				$email->subject(SITENAME . ": Task assigned");
				$email->template('task_assigned_email');
			}

			$email->emailFormat('html');
			$email->viewVars(array('Custname' => $usersDetails['UserDetail']['first_name'] . ' ' . $usersDetails['UserDetail']['last_name'], 'taskName' => $taskName['Element']['title'], 'projectName' => $projectDetail['title'], 'assignedby' => $assignedby,'projectName' => $projectName,'workspaceName' => $workspaceName,'elementName' => $taskName['Element']['title'],'open_page' => $requestAction));

			$email->send();
			/*pr($email->send());
			die('emaiil');*/

		}
	}

	public function testemail() {
		$email = new CakeEmail();
		$email->config('Smtp');
		$email->from(array(ADMIN_EMAIL => SITENAME));
		$email->to("jagdish.dots@gmail.com");
		$email->subject(SITENAME . ': Test Email');
		$email->template('element_schedule_overdue');
		$email->emailFormat('html');
		$email->viewVars(array('elementName' => "Test Email Title", 'owner_name' => "Pawan Sharma"));
		$email->send();
	}

	/*================================== Area Risk Popup =============================*/
	public function area_risks($project_id = null, $workspace_id = null, $area_id = null) {

		$this->loadModel('RmElement');
		$this->loadModel('Element');
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

		$data = array();
		if (isset($area_id) && !empty($area_id)) {
			$query = '';

			$query .= 'SELECT element.id ';
			$query .= 'FROM elements as element ';
			$query .= "WHERE element.area_id = " . $area_id . " ";

			if (isset($type) && !empty($type) && $type == 'completed') {
				$query .= "AND element.date_constraints = 1 ";
				$query .= "AND element.sign_off = 1 ";
			}

			$data = $this->Element->query($query);
			$data = Set::extract($data, '/element/id');
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
				$intersectElements = array_intersect($data, $intersectElements);
			}

		}

		$this->set('intersectElements', $intersectElements);
		$this->set('project_id', $project_id);
		$this->set('workspace_id', $workspace_id);
		$this->set('area_id', $area_id);
	}

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

		$risk_elementscnt = '';
		$risk_elements_riskids = array();
		if (isset($risk_elements) && !empty($risk_elements)) {

			$risk_elements_riskids = Set::extract($risk_elements, '/RmElement/rm_detail_id');
			$risk_elements_riskids = array_unique($risk_elements_riskids);
			$risk_elements_riskids = array_intersect($user_risk, $risk_elements_riskids);
		}

		$risk_elementscnt = array_unique($risk_elements_riskids);
		return (isset($risk_elementscnt) && !empty($risk_elementscnt)) ? count($risk_elementscnt) : 0;

	}

	/*========================================================================================*/

	public function delete_an_item($element_id = null, $update_page = false) {

		if ($this->request->isAjax()) {

			$this->layout = false;
			$viewData = null;
			$viewData['element_id'] = $element_id;
			$viewData['update_page'] = $update_page;
			$viewData['project_id'] = element_project($element_id);

			$this->set($viewData);
			$this->render('/Entities/partials/delete_an_item');

		}
	}

	public function get_links($element_id = null) {

		if ($this->request->isAjax()) {

			$this->layout = false;
			$viewData = null;
			$viewData['lists'] = $this->Element->read(null, $element_id);
			$viewData['element_id'] = $element_id;

			$view = new View($this, false);
			$view->viewPath = 'Entities/element_files';
			$view->set($viewData);
			$html = $view->render('links_partial');
			echo json_encode($html);
			exit();
		}
	}

	public function element_delete_data($data = null) {

		if (!empty($data)) {

			$this->DeleteData->save($data);

		}

	}

	/*********************************************************/
	/************************ Task Pin ***********************/
	/*********************************************************/

	public function current_task() {

		$this->loadModel('CurrentTask');

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$response = [
				'success' => false,
				'content' => null,
			];
			$user_id = $this->user_id;

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				if( isset($post['project_id']) && !empty($post['project_id']) && isset($post['task_id']) && !empty($post['task_id']) ) {

					$cntTask = $this->CurrentTask->find('count', array('conditions' => array('CurrentTask.user_id' => $user_id)));

					if ( isset($cntTask) ) {
						if ($post['status'] == 'add') {

							$this->request->data['CurrentTask']['id'] = '';
							$this->request->data['CurrentTask']['user_id'] = $user_id;
							$this->request->data['CurrentTask']['task_id'] = $post['task_id'];
							$this->request->data['CurrentTask']['project_id'] = $post['project_id'];
							$this->request->data['CurrentTask']['created'] = date('Y-m-d h:i:s');
							$this->CurrentTask->save($this->request->data['CurrentTask']);
							$response['success'] = true;
						}
					}

					if ($post['status'] == 'remove') {
						$this->CurrentTask->deleteAll(array('CurrentTask.project_id' => $post['project_id'],'CurrentTask.task_id' => $post['task_id'], 'CurrentTask.user_id' => $user_id));
						$response['success'] = true;
					}

				}
			}

			echo json_encode($response);
			exit();

		}
	}


	/*******************************************************************
	********************* Element Cost and Dependency
	********************************************************************/

	public function element_cost_save($area_id = null) {
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			$response = [
				'success' => false,
				'status' => 400,
				'content' => null,
				'element_id' => null,
			];
			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;

				if (isset($post['ElementCost'])) {

					if ((!isset($post['ElementCost']['estimated_cost']) || empty($post['ElementCost']['estimated_cost'])) && (!isset($post['ElementCost']['spend_cost']) || empty($post['ElementCost']['spend_cost']))) {

						$response['content']['estimated_cost'] = 'Estimated cost is required.';
						$response['content']['spend_cost'] = 'Spend cost is required.';
						$response['success'] = false;

					} else {
						$response['success'] = true;
					}

					if (!isset($post['ElementCost']['estimated_cost']) || empty($post['ElementCost']['estimated_cost'])) {

						$response['content']['estimated_cost'] = 'Estimated cost is required.';
						$response['success'] = false;

					} else {
						$response['success'] = true;
					}

					if (!isset($post['ElementCost']['spend_cost']) || empty($post['ElementCost']['spend_cost'])) {

						$response['content']['spend_cost'] = 'Spend cost is required.';
						$response['success'] = false;

					} else {
						$response['success'] = true;
					}

					if ($response['success'] == true) {

						$projectData = project_detail(element_project($post[0]['id']));
						$projectCurrencyID = 12;
						if (isset($projectData['Project']['currency_id']) && !empty($projectData['Project']['currency_id'])) {
							$projectCurrencyID = $projectData['Project']['currency_id'];
						}

						$post['ElementCost']['element_id'] = $post[0]['id'];
						$post['ElementCost']['updated_by'] = $this->Session->read('Auth.User.id');
						$post['ElementCost']['project_currency_id'] = $projectCurrencyID;
						$this->ElementCost->save($post['ElementCost']);

						$response['content']['message'] = 'Cost are saved';
						$response['success'] = true;

					}
				}

				//$element_id = $allData['Element'][0]['id'];
				//$latestCost = $this->ElementCost->find('first', array('conditions'=>array['ElementCost.element_id'=>$element_id], 'order'=>'ElementCost.id DESC' ) );

				echo json_encode($response);
				exit;
			}
		}
	}

	public function element_dependency_save() {
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			$response = [
				'success' => false,
				'status' => 400,
				'content' => null,
				'element_id' => null,
			];
			$insert_id = '';
            $gsUp = array();
			$gatedRLS = [];
			$uncheckedIDS = array();
			$uncheckedGateIDS = array();
			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;


				if (isset($post['DefaultDependency'])) {
				$defaultElementid = $post['DefaultDependency']['element_id'];

				$dataDep['ElementDependency']['user_id'] = $this->Session->read('Auth.User.id');
				$dataDep['ElementDependency']['element_id'] = $defaultElementid;

				$edata = $this->ElementDependency->find('first', array('conditions' => array('ElementDependency.element_id' => $defaultElementid)));

				if (isset($post['ElementDependency']['id']) && !empty($post['ElementDependency']['id'])) {
				$dataDep['ElementDependency']['id'] = $edata['ElementDependency']['id'];
				}




				$this->ElementDependency->save($dataDep);

				if( isset($edata) && !empty($edata['ElementDependency']['id']) ){

					$relation_id = $edata['ElementDependency']['id'];
				}else{
					$relation_id = $this->ElementDependency->getLastInsertId();
				}


				$task_data = [
						'project_id' => element_project($defaultElementid),
						'workspace_id' => element_workspace($defaultElementid),
						'element_id' => $defaultElementid,
						'element_type' => 'element_dependencies',
						'user_id' => $this->user_id,
						'relation_id' => $relation_id,
						'updated_user_id' => $this->user_id,
						'message' => 'Task dependencies updated',
						'updated' => date("Y-m-d H:i:s"),
					];
				$this->loadModel('Activity');
				$this->Activity->id = null;
				$this->Activity->save($task_data);



				}


				if (!isset($post['ElementDependency']) || empty($post['ElementDependency'])) {

					$post['ElementDependency'][$defaultElementid]['is_critical'] = 'off';

				}

				if (isset($post['ElementDependency'])) {

					if (isset($post['DefaultDependency']['is_chk']) && !empty($post['DefaultDependency']['is_chk'])) {
						$unchecked = $post['DefaultDependency']['is_chk'];
						$uncheckedIDS = explode(',', $unchecked);
					}

					if (isset($uncheckedIDS) && !empty($uncheckedIDS)) {

						foreach ($uncheckedIDS as $idsd) {

							$post['ElementDependency'][$idsd]['is_critical'] = 'off';

						}
					}

					foreach ($post['ElementDependency'] as $key => $dependencyValue) {

						$deletecondP = array('ElementDependency.element_id' => $defaultElementid);

						$element_dependancy_id = array();
						$element_dependancy_id = $this->ElementDependency->find('list', array('conditions' => $deletecondP));

						$dependancyrelationshipdelete = array('ElementDependancyRelationship.element_dependancy_id' => $element_dependancy_id);

						$element_dependancys = $this->ElementDependancyRelationship->find('list', array('conditions' => array('ElementDependancyRelationship.element_dependancy_id' => $element_dependancy_id), 'fields' => array('ElementDependancyRelationship.id', 'ElementDependancyRelationship.element_id')));

						/* Delete previous entries from ElementDependancyRelationship */

						//pr($element_dependancys);

						if (isset($element_dependancy_id) && !empty($element_dependancy_id)) {

							foreach ($element_dependancys as $key => $depn) {
								//pr($key);
								$ownDpend = $this->ElementDependency->find('first', array('conditions' => array('ElementDependency.element_id' => $depn)));

								if (isset($ownDpend) && !empty($ownDpend)) {
									$getID = $ownDpend['ElementDependency']['id'];
									//  echo $key.'<br>';
									$Drelationshipdelete = array('ElementDependancyRelationship.element_dependancy_id' => $getID, 'ElementDependancyRelationship.element_id' => $defaultElementid);

									$Drelationshipgated = array('ElementDependancyRelationship.element_dependancy_id' => $getID, 'ElementDependancyRelationship.element_id' => $defaultElementid,'ElementDependancyRelationship.is_gated' =>1 );
									//  pr($Drelationshipdelete);


									$gatedRLS[] = $this->ElementDependancyRelationship->find('first', array('conditions' => $Drelationshipgated));

									$this->ElementDependancyRelationship->deleteAll($Drelationshipdelete, true);
								}

								$this->ElementDependancyRelationship->delete($key);
							}
							////$this->ElementDependancyRelationship->deleteAll($dependancyrelationshipdelete);

						}

						$gsUp = $gatedRLS  ;

						//$this->ElementDependency->deleteAll($deletecond);
						//===================================================================

						$data['ElementDependency']['user_id'] = $this->Session->read('Auth.User.id');
						if ($key != 'id') {
							$data['ElementDependency']['element_id'] = $key;
						}
						$data['ElementDependency']['is_critical'] = 0;


						if (isset($dependencyValue['is_critical']) && $dependencyValue['is_critical'] == 'on') {
							$data['ElementDependency']['is_critical'] = 1;
						}

						if (isset($this->request->data['ElementDependency']['id']) && !empty($this->request->data['ElementDependency']['id'])) {

							if ($key == $defaultElementid) {

								$this->ElementDependency->id = $this->request->data['ElementDependency']['id'];
								$data['ElementDependency']['id'] = $this->request->data['ElementDependency']['id'];

							} else {

								$this->ElementDependency->id = null;

							}

							$cc = $this->ElementDependency->find('count', array('conditions' => array('ElementDependency.element_id' => $key)));
							$dd = $this->ElementDependency->find('first', array('conditions' => array('ElementDependency.element_id' => $key)));

							if ($this->ElementDependency->id == null && (isset($dd) && !empty($dd))) {
								$this->ElementDependency->id = $dd['ElementDependency']['id'];
								$data['ElementDependency']['id'] = $dd['ElementDependency']['id'];
							}

							//pr($uncheckedIDS); die;

							/* Update Dependency entries */

							if (isset($uncheckedIDS) && !empty($uncheckedIDS)) {
								$this->ElementDependency->updateAll(
									array('ElementDependency.is_critical' => 0, 'ElementDependency.user_id' => $this->Session->read('Auth.User.id')),
									array('ElementDependency.element_id' => $uncheckedIDS)
								);
							}

							if (isset($data['ElementDependency']['element_id']) && $data['ElementDependency']['element_id'] != 0) {

								$data['ElementDependency']['user_id'] = $this->Session->read('Auth.User.id');

								$this->ElementDependency->save($data);
								$insert_id = $this->request->data['ElementDependency']['id'];
							}

						} else {

							/* Save Dependency entries */

							//	 pr($data['ElementDependency']);
							$cc = $this->ElementDependency->find('count', array('conditions' => array('ElementDependency.element_id' => $key)));

							$data['ElementDependency']['user_id'] = $this->Session->read('Auth.User.id');

							if ($data['ElementDependency']['element_id'] != 0 && $cc < 1) {
								$this->ElementDependency->id = null;
								$this->ElementDependency->save($data['ElementDependency']);
							}

						}

						$response['success'] = true;
						$response['content']['message'] = 'Element dependency is saved';
					}

					if (!isset($insert_id) || empty($insert_id)) {

						$edata = $this->ElementDependency->find('first', array('conditions' => array('ElementDependency.element_id' => $defaultElementid)));

						if (isset($edata) && !empty($edata)) {

							$insert_id = $edata['ElementDependency']['id'];
						}
					}

					// ========== ======================================================
					// pr($post['ElementDependancyRelationship'],1);

					/* Save DependancyRelationship entries */

					if (isset($post['ElementDependancyRelationship']) && !empty($post['ElementDependancyRelationship'])) {

						$data['ElementDependancyRelationship']['element_dependancy_id'] = $insert_id;



						foreach ($post['ElementDependancyRelationship'] as $keydeprel => $valuedeprel) {

							$data['ElementDependancyRelationship']['element_id'] = $keydeprel;
							if ($valuedeprel['dependency'] > 0 && $defaultElementid != $data['ElementDependancyRelationship']['element_id']) {

								$data['ElementDependancyRelationship']['dependency'] = $valuedeprel['dependency'];


								if( GATE_ENABLED == true ){
									if (isset($valuedeprel['is_gated']) && $valuedeprel['is_gated'] == 'on' && $valuedeprel['dependency'] >= 1) {
										$data['ElementDependancyRelationship']['is_gated'] = 1;
									} else {
										$data['ElementDependancyRelationship']['is_gated'] = 0;
									}
								}
								$this->ElementDependancyRelationship->saveAssociated($data);
							}
						}
					}


					/***********//***********/

					/*  Check current element's all dependencies relationship and save reverse for itself for them */

					$defaultElementDependencyid = $this->ElementDependency->find('first', array('conditions' => array('ElementDependency.element_id' => $defaultElementid)));

					if (isset($defaultElementDependencyid) && !empty($defaultElementDependencyid['ElementDependency']['id'])) {

						//$defaultElementDependencyid['ElementDependency']['id'];
						$all_DependancyRelationship = $this->ElementDependancyRelationship->find('list', array('conditions' => array('ElementDependancyRelationship.element_dependancy_id' => $defaultElementDependencyid['ElementDependency']['id']), 'fields' => array('ElementDependancyRelationship.id', 'ElementDependancyRelationship.element_id')));




						$data1 = array();

						if (isset($all_DependancyRelationship) && !empty($all_DependancyRelationship)) {

							foreach ($all_DependancyRelationship as $allD) {

								/* Check old dependency base on element id so find first */

								$defaultDependencyid = $this->ElementDependency->find('first', array('conditions' => array('ElementDependency.element_id' => $allD)));


								if (isset($defaultDependencyid) && !empty($defaultDependencyid)) {

									$defaultDependencyRelationid = $this->ElementDependancyRelationship->find('first', array('conditions' => array('ElementDependancyRelationship.element_id' => $allD, 'ElementDependancyRelationship.element_dependancy_id' => $defaultElementDependencyid['ElementDependency']['id'])));

									$selfdependency = $defaultDependencyRelationid['ElementDependancyRelationship']['dependency'];



									$newDep = 0;
									if ($selfdependency == 1) {
										$newDep = 2;
									} else {
										$newDep = 1;
									}

									$dataRU = $this->ElementDependancyRelationship->find('first', array('conditions' => array('ElementDependancyRelationship.element_dependancy_id' => $defaultDependencyid['ElementDependency']['id'], 'ElementDependancyRelationship.element_id' => $defaultElementid)));

									$data1['ElementDependancyRelationship']['id'] = null;

									if (isset($dataRU) && !empty($dataRU)) {
										$data1['ElementDependancyRelationship']['id'] = $dataRU['ElementDependancyRelationship']['id'];
									} else {
										$data1['ElementDependancyRelationship']['id'] = null;
									}




									$data1['ElementDependancyRelationship']['element_dependancy_id'] = $defaultDependencyid['ElementDependency']['id'];

									$data1['ElementDependancyRelationship']['element_id'] = $defaultElementid;

									$data1['ElementDependancyRelationship']['dependency'] = $newDep;
									//$data1['ElementDependancyRelationship']['is_gated'] = 1;


								//	pr($data1);

									$this->ElementDependancyRelationship->saveAssociated($data1);

									/* get the Id from relationship and insert relationship in reverse order */

								} else {

									$dataDp = array();

									$dataDp['ElementDependency']['id'] = null;

									$dataDp['ElementDependency']['element_id'] = $allD;

									$dataDp['ElementDependency']['is_critical'] = 0;
									//$dataDp['ElementDependency']['is_gated'] = 0;

									$dataDp['ElementDependency']['user_id'] = $this->Session->read('Auth.User.id');

									if ($this->ElementDependency->save($dataDp)) {

										$insertId = $this->ElementDependency->getLastInsertId();

										$defaultDependencyRelationid = $this->ElementDependancyRelationship->find('first', array('conditions' => array('ElementDependancyRelationship.element_id' => $allD, 'ElementDependancyRelationship.element_dependancy_id' => $defaultElementDependencyid['ElementDependency']['id'])));




										if (isset($defaultDependencyRelationid) && !empty($defaultDependencyRelationid)) {

											$selfdependency = $defaultDependencyRelationid['ElementDependancyRelationship']['dependency'];
											$newDep = 0;
											if ($selfdependency == 1) {
												$newDep = 2;
											} else {
												$newDep = 1;
											}

											$data1['ElementDependancyRelationship']['id'] = null;

											$data1['ElementDependancyRelationship']['element_dependancy_id'] = $insertId;

											$data1['ElementDependancyRelationship']['element_id'] = $defaultElementid;

											$data1['ElementDependancyRelationship']['dependency'] = $newDep;

											$this->ElementDependancyRelationship->save($data1);

										}

									}

								}

							}
						}

					}

					/*------------Save previous gated-----------------*/

					if(isset($gatedRLS) && !empty($gatedRLS)){
						$gatedRLS = array_filter($gatedRLS);
						foreach($gatedRLS as $gted){

							$this->ElementDependancyRelationship->updateAll(
									array('ElementDependancyRelationship.is_gated' => 1   ),
									array('ElementDependancyRelationship.element_dependancy_id' => $gted['ElementDependancyRelationship']['element_dependancy_id'],'ElementDependancyRelationship.element_id' => $gted['ElementDependancyRelationship']['element_id'] )
								);
						}
					}




					/***********//***********/

				}
 //die;
				echo json_encode($response);
				exit;
			}
		}
	}
	// currently this function is not in use
	public function critical_status_update() {

		if ($this->request->isAjax()) {
			$view = new View($this, false);
			$view->layout = false;
			$this->autoRender = false;

			$response = [
				'success' => false,
				'status' => 400,
				'content' => null,
			];

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;

				$data = array();
				if (isset($post['element_id']) && !empty($post['element_id'])) {

					$data = $this->ElementDependency->find('first', array(
						'conditions' => array('ElementDependency.element_id' => $post['element_id']),
						'fields' => array('ElementDependency.is_critical'),
					));

					if (isset($data) && !empty($data)) {
						$response['content'] = (isset($data['ElementDependency']['is_critical']) && $data['ElementDependency']['is_critical'] == 1) ? 1 : 0;
						$response['success'] = true;
					}
				}

				echo json_encode($response);
				die();
			}
		}

	}

	public function dependancy_status_update() {

		if ($this->request->isAjax()) {
			$view = new View($this, false);
			$view->layout = false;
			$this->autoRender = false;

			$response = [
				'success' => false,
				'status' => 400,
				'content' => null,
			];

			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;
				$data = array();

				if (isset($post['element_id']) && !empty($post['element_id'])) {

					$data = $this->ElementDependency->find('first', array(
						'conditions' => array('ElementDependency.element_id' => $post['element_id']),
						'fields' => array('ElementDependency.id'),
					));

					if (isset($data) && !empty($data) ) {

							$predessor = $this->ElementDependancyRelationship->find('count', array('conditions' => array(
								'ElementDependancyRelationship.element_dependancy_id' => $data['ElementDependency']['id'], 'ElementDependancyRelationship.dependency' => 1,
							),
							));
							$successor = $this->ElementDependancyRelationship->find('count', array('conditions' => array(
								'ElementDependancyRelationship.element_dependancy_id' => $data['ElementDependency']['id'], 'ElementDependancyRelationship.dependency' => 2,
							),
							));

						if ((isset($predessor) && isset($successor)) && $predessor > 0 && $successor > 0) {
							$response['content'] = 'both';
							$response['success'] = true;
						} else if (isset($predessor) && $predessor > 0) {
							$response['content'] = 'predessor';
							$response['success'] = true;
						} else if (isset($successor) && $successor > 0) {
							$response['content'] = 'successor';
							$response['success'] = true;
						} else {
							$response['content'] = 'none';
							$response['success'] = true;
						}
					}
				}

				echo json_encode($response);
				die();
			}
		}

	}

	public function element_list() {

		if ($this->request->isAjax()) {
			$view = new View($this, false);
			$view->layout = false;
			$this->autoRender = false;

			$response = [
				'success' => false,
				'status' => 400,
				'content' => null,
				'element_id' => null,
			];
			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;

				$data = array();
				if (isset($post['element_id']) && !empty($post['element_id'])) {

					$elementlist = $this->ElementDependency->find('first', array(
						'conditions' => array('ElementDependency.element_id' => $post['element_id'],
						)));
					$views = new View();
					$commonHelper = $views->loadHelper('Common');
					$getDependancyStatus = $commonHelper->dependancy_status($post['element_id']);

					if (isset($getDependancyStatus) && $getDependancyStatus == 'predessor') {
						$dts = 1;
					} else if (isset($getDependancyStatus) && $getDependancyStatus == 'successor') {
						$dts = 2;
					} else if (isset($getDependancyStatus) && $getDependancyStatus == 'both') {
						$dts = 3;
					}

					$view->viewPath = 'Dashboards';
					$view->set('elementlist', $elementlist);
					$view->set('dependancytype', $dts);
					$view->set('dependancytypes', $dts);
					$html = $view->render('element_relation_list');

				}

				echo json_encode($html);
				die();
			}
		}

	}

	public function element_cost_history() {

		if ($this->request->isAjax()) {
			$view = new View($this, false);
			$view->layout = false;
			$this->autoRender = false;

			$response = [
				'success' => false,
				'status' => 400,
				'content' => null,
				'element_id' => null,
			];
			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;

				$data = array();
				if (isset($post['element_id']) && !empty($post['element_id'])) {

					$spendcost = $this->ElementCostHistory->find('all', array(
						'conditions' => array('ElementCostHistory.element_id' => $post['element_id'], 'ElementCostHistory.spend_cost <>' => ''),
						'order' => 'ElementCostHistory.id DESC',
					));

					$estimatedcost = $this->ElementCostHistory->find('all', array(
						'conditions' => array('ElementCostHistory.element_id' => $post['element_id'], 'ElementCostHistory.estimated_cost <>' => ''),
						'order' => 'ElementCostHistory.id DESC',
					));
				}

				if ($post['historytype'] == "spend_cost") {
					$view->viewPath = 'Entities';
					$view->set('spendcost', $spendcost);
					$html = $view->render('costs_pend_list');
				} else {
					$view->viewPath = 'Entities';
					$view->set('estimatedcost', $estimatedcost);
					$html = $view->render('cost_list');
				}

				echo json_encode($html);
				die();
			}
		}

	}
	public function task_list_el_date_cost($element_id = null) {
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			$response = null;
			if (isset($element_id) && !empty($element_id)) {
				// Get all workspace elements
				$element = $this->Element->findById($element_id);
				$response['workspace_id'] = $element['Area']['workspace_id'];
				$response['area_id'] = $element['Area']['id'];
				// pr($element, 1);
				$response['defaultElementID'] = $element_id;
				$response['all_elements'] = $element;
			}
			$this->set('response', $response);
			$this->set('elementid', $element_id);
			$this->render('/Entities/element_files/task_list_el_date_cost');
		}
	}

	public function dependancy_status_count(){


		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			$response = null;

			$post = $this->request->data;
			$element_id = 	$post['element_id'];
			if (isset($element_id) && !empty($element_id)) {
				$element_id = $post['element_id'];
				$this->set('element_id', $element_id);
			}
			$this->set('element_id', $element_id);
			$this->render('/Entities/element_files/dependancy');
		}

	}

	public function cost_status_count(){


		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			$response = null;

			$post = $this->request->data;
			$element_id = 	$post['element_id'];
			if (isset($element_id) && !empty($element_id)) {
				$element_id = $post['element_id'];
				$this->set('element_id', $element_id);
			}
			$this->set('element_id', $element_id);
			$this->render('/Entities/element_files/element_cost_count');
		}

	}

	/**********************************************************************************
	****************************** New Function for Task Page *************************
	**********************************************************************************/

	public function get_workspace_task_template() {

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

				$statuses = [];
				if(isset($post['status']) && !empty($post['status'])) {
					$statuses = $post['status'];
				}

				if(isset($post['generalflag']) && !empty($post['generalflag'])) {
					 $viewData['generalflag'] = $post['generalflag'];
				}

				$viewData['statuses'] = $statuses;
				$project_id = $viewData['project_id'] = $post['project_id'];
				$workspace_id = $viewData['workspace_id'] = $post['workspace_id'];

				// Project Task Type
				if( isset($post['task_type']) && $post['task_type'] == 'project_type' && isset($post['project_task_type']) && !empty($post['project_task_type']) ){
					$viewData['project_task_type'] = $post['project_task_type'];
				}
				// assigned_user
				if( isset($post['assigned_user']) && !empty($post['assigned_user']) ){
					$viewData['assigned_user'] = $post['assigned_user'];
				}
				// pr($post, 1);
				/****************************************************/

					$this->Workspace->recursive = -1;
					$workspace = $this->Workspace->find('first', [
						'conditions' => [
							'Workspace.id' => $workspace_id,
							'Workspace.studio_status !=' => 1
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

				/****************************************************/


				//$data = $this->objView->loadHelper('ViewModel')->getAreaTask($workspace_id, null, $data);

				$taskCount = $this->objView->loadHelper('ViewModel')->getTaskCount($workspace_id);
				$wsp_permissions =$this->objView->loadHelper('ViewModel')->getWspPermission($workspace_id);


				$this->set('viewData', $viewData);
				$this->set('project_id', $project_id);
				$this->set('workspace_id', $workspace_id);
				$data['workspace'] = $workspace;
				$this->set('data', $data);
				$this->set('wsp_permissions', $wsp_permissions);



				$this->set('taskCount', $taskCount);

			}

			//$this->set(compact('viewData','data'));
			$this->render('/Projects/partials/workspace_ele_layout');
		}

	}

	//cut copy paste
	public function task_cut_copy_paste($element_id = null) {
		if ($this->request->isAjax()) {

		$this->loadModel('UserPermission');

			$response = [
				'success' => false,
				'msg' => '',
				'content' => null,
				'error' => '',
			];
			$success = false;
			$wsp_date_passed_msg = 'Cannot move Task because the Workspace end date has passed.';

			$this->layout = 'ajax';
			$this->autoRender = false;

			if ($this->request->is('post') || $this->request->is('put')) {
				// pr($this->request->data, 1);
				$postCurrentAreaID = null;

				$postWorkspaceId = $this->request->data['workspace_id'];
				$postSortArea = $this->request->data['sort_area'];
				$postElements = $this->request->data['Element'];
				$postElementAction = $this->request->data['element_action'];
				$postprojectid = (isset($this->request->data['project_id']) && !empty($this->request->data['project_id'])) ? $this->request->data['project_id'] : null;
				$user_id = $this->Auth->user('id');

				if (isset($postElements['area_id']) && !empty($postElements['area_id']) && $postElements['area_id'] > 0) {

					// check action, cut = Only update area_id, copy = create new element with reference of area_id
					if (isset($postElementAction) && !empty($postElementAction)) {

						// CREATE A NEW ELEMENT
						if ($postElementAction == 'copy' || $postElementAction == 'copy_to') {

							$allData = [
								'Element' => null,
								'Links' => null,
								'Documents' => null,
								'Notes' => null,
							];

							// GET SELECTED ELEMENT ROW
							/* $row = $this->Element->find('first', [
								'conditions' => [
									'Element.id' => $element_id,
								],
								'recursive' => 1,
							]); */
							//pr($row); die;

							// get is_editable permissions
							$ele_iseditable = $this->ElementPermission->find('first',

										array('conditions'=>array('ElementPermission.element_id'=>$element_id,'ElementPermission.user_id'=>$user_id),'recursive'=>-1, 'fields'=>'is_editable' )

							);
							// Get Element data
							$ele_query = "SELECT
								Element.*

							FROM user_permissions

							INNER JOIN elements as Element
								ON Element.id = user_permissions.element_id

							WHERE user_permissions.element_id = $element_id and user_id = $user_id ";

							$eleDetail = $this->UserPermission->query($ele_query);


		//first union for Links
		//second union for Documents
		//third union for Notes
		$query = "SELECT

			Links.id as Links,
			0 as Docs,
			0 as Notes,
			user_permissions.permit_read,
			user_permissions.permit_add,
			user_permissions.permit_edit,
			user_permissions.permit_delete,
			user_permissions.permit_copy,
			user_permissions.permit_move,

			Links.element_id as link_element_id,
			Links.title as link_title,
			Links.link_type as link_type,
			Links.references as link_references,
			Links.embed_code as link_embed_code,
			Links.creater_id as link_creater_id,
			Links.updated_user_id as link_updated_user_id,
			Links.status as link_status,
			Links.created as link_created,
			Links.modified as link_modified,
			Links.is_search as link_is_search,
			Links.create_activity as link_create_activity,

			0 as doc_element_id,
			0 as doc_title,
			0 as doc_file_name,
			0 as doc_file_size,
			0 as doc_file_type,
			0 as doc_feedback_attachments_id,
			0 as doc_status,
			0 as doc_created,
			0 as doc_modified,
			0 as doc_is_search,
			0 as doc_create_activity,

			0 as note_element_id,
			0 as note_title,
			0 as note_description,
			0 as note_creater_id,
			0 as note_updated_user_id,
			0 as note_status,
			0 as note_created,
			0 as note_modified,
			0 as note_is_search,
			0 as note_create_activity

		FROM user_permissions
		INNER JOIN element_links as Links
			ON Links.element_id = user_permissions.element_id
		WHERE user_permissions.element_id = $element_id AND user_id = $user_id

		UNION distinct

		SELECT
			0 as Links,
			Documents.id as Docs,
			0 as Notes,
			user_permissions.permit_read,
			user_permissions.permit_add,
			user_permissions.permit_edit,
			user_permissions.permit_delete,
			user_permissions.permit_copy,
			user_permissions.permit_move,

			0 as link_element_id,
			0 as link_title,
			0 as link_type,
			0 as link_references,
			0 as link_embed_code,
			0 as link_creater_id,
			0 as link_updated_user_id,
			0 as link_status,
			0 as link_created,
			0 as link_modified,
			0 as link_is_search,
			0 as link_create_activity,

			Documents.element_id as doc_element_id,
			Documents.title as doc_title,
			Documents.file_name as doc_file_name,
			Documents.file_size as doc_file_size,
			Documents.file_type as doc_file_type,
			Documents.feedback_attachments_id as doc_feedback_attachments_id,
			Documents.status as doc_status,
			Documents.created as doc_created,
			Documents.modified as doc_modified,
			Documents.is_search as doc_is_search,
			Documents.create_activity as doc_create_activity,

			0 as note_element_id,
			0 as note_title,
			0 as note_description,
			0 as note_creater_id,
			0 as note_updated_user_id,
			0 as note_status,
			0 as note_created,
			0 as note_modified,
			0 as note_is_search,
			0 as note_create_activity

		FROM user_permissions
		INNER JOIN element_documents as Documents
			ON Documents.element_id = user_permissions.element_id
		WHERE user_permissions.element_id = $element_id AND user_id = $user_id

		UNION distinct

		SELECT
			0 as Links,
			0 as Docs,
			Notes.id as Notes,
			user_permissions.permit_read,
			user_permissions.permit_add,
			user_permissions.permit_edit,
			user_permissions.permit_delete,
			user_permissions.permit_copy,
			user_permissions.permit_move,

			0 as link_element_id,
			0 as link_title,
			0 as link_type,
			0 as link_references,
			0 as link_embed_code,
			0 as link_creater_id,
			0 as link_updated_user_id,
			0 as link_status,
			0 as link_created,
			0 as link_modified,
			0 as link_is_search,
			0 as link_create_activity,

			0 as doc_element_id,
			0 as doc_title,
			0 as doc_file_name,
			0 as doc_file_size,
			0 as doc_file_type,
			0 as doc_feedback_attachments_id,
			0 as doc_status,
			0 as doc_created,
			0 as doc_modified,
			0 as doc_is_search,
			0 as doc_create_activity,

			Notes.element_id as note_element_id,
			Notes.title as note_title,
			Notes.description as note_description,
			Notes.creater_id as note_creater_id,
			Notes.updated_user_id as note_updated_user_id,
			Notes.status as note_status,
			Notes.created as note_created,
			Notes.modified as note_modified,
			Notes.is_search as note_is_search,
			Notes.create_activity as note_create_activity

		FROM user_permissions
		INNER JOIN element_notes as Notes
			ON Notes.element_id = user_permissions.element_id
		WHERE user_permissions.element_id = $element_id AND user_id = $user_id ";


									$eleData = $this->UserPermission->query($query);


									$row = array();
									$row['Permissions']= array();
									foreach($eleData as $key => $listRows){
										foreach($listRows as $skey => $finalRows){

											$row['Permissions'][$skey]['permit_read'] = $finalRows['permit_read'];
											$row['Permissions'][$skey]['permit_add'] = $finalRows['permit_add'];
											$row['Permissions'][$skey]['permit_edit'] = $finalRows['permit_edit'];
											$row['Permissions'][$skey]['permit_delete'] = $finalRows['permit_delete'];
											$row['Permissions'][$skey]['permit_copy'] = $finalRows['permit_copy'];
											$row['Permissions'][$skey]['permit_move'] = $finalRows['permit_move'];
											$row['Permissions'][$skey]['is_editable'] = $ele_iseditable['ElementPermission']['is_editable'];;

											if( !empty($finalRows['Links']) && $finalRows['Links'] !=0 ){

												//$finalRows['element_id'] = $finalRows['link_element_id'];
												$finalRows['title'] = $finalRows['link_title'];
												$finalRows['type'] = $finalRows['link_type'];
												$finalRows['references'] = $finalRows['link_references'];
												$finalRows['embed_code'] = $finalRows['link_embed_code'];
												$finalRows['creater_id'] = $finalRows['link_creater_id'];
												$finalRows['updated_user_id'] = $finalRows['link_updated_user_id'];
												$finalRows['status'] = $finalRows['link_status'];
												$finalRows['created'] = $finalRows['link_created'];
												$finalRows['modified'] = $finalRows['link_modified'];
												$finalRows['is_search'] = $finalRows['link_is_search'];
												$finalRows['create_activity'] = $finalRows['link_create_activity'];

												unset($finalRows['Docs']);
												unset($finalRows['Notes']);
												unset($finalRows['doc_element_id']);
												unset($finalRows['doc_title']);
												unset($finalRows['doc_file_name']);
												unset($finalRows['doc_file_size']);
												unset($finalRows['doc_file_type']);
												unset($finalRows['doc_feedback_attachments_id']);
												unset($finalRows['doc_status']);
												unset($finalRows['doc_created']);
												unset($finalRows['doc_modified']);
												unset($finalRows['doc_is_search']);
												unset($finalRows['doc_create_activity']);

												unset($finalRows['note_element_id']);
												unset($finalRows['note_title']);
												unset($finalRows['note_description']);
												unset($finalRows['note_creater_id']);
												unset($finalRows['note_updated_user_id']);
												unset($finalRows['note_status']);
												unset($finalRows['note_created']);
												unset($finalRows['note_modified']);
												unset($finalRows['note_is_search']);
												unset($finalRows['note_create_activity']);

												unset($finalRows['Links']);
												unset($finalRows['link_element_id']);

												$row['Links'][]= $finalRows;
											}
											if( !empty($finalRows['Docs']) && $finalRows['Docs'] !=0 ){

												//$finalRows['element_id'] = $finalRows['doc_element_id'];
												$finalRows['title'] = $finalRows['doc_title'];
												$finalRows['file_name'] = $finalRows['doc_file_name'];
												$finalRows['file_size'] = $finalRows['doc_file_size'];
												$finalRows['file_type'] = $finalRows['doc_file_type'];
												$finalRows['feedback_attachments_id'] = $finalRows['doc_feedback_attachments_id'];
												$finalRows['status'] = $finalRows['doc_status'];
												$finalRows['created'] = $finalRows['doc_created'];
												$finalRows['modified'] = $finalRows['doc_modified'];
												$finalRows['is_search'] = $finalRows['doc_is_search'];
												$finalRows['create_activity'] = $finalRows['doc_create_activity'];

												unset($finalRows['Links']);
												unset($finalRows['Notes']);

												unset($finalRows['note_element_id']);
												unset($finalRows['note_title']);
												unset($finalRows['note_description']);
												unset($finalRows['note_creater_id']);
												unset($finalRows['note_updated_user_id']);
												unset($finalRows['note_status']);
												unset($finalRows['note_created']);
												unset($finalRows['note_modified']);
												unset($finalRows['note_is_search']);
												unset($finalRows['note_create_activity']);

												unset($finalRows['Links']);
												unset($finalRows['link_element_id']);
												unset($finalRows['link_title']);
												unset($finalRows['link_type']);
												unset($finalRows['link_references']);
												unset($finalRows['link_embed_code']);
												unset($finalRows['link_creater_id']);
												unset($finalRows['link_updated_user_id']);
												unset($finalRows['link_status']);
												unset($finalRows['link_created']);
												unset($finalRows['link_modified']);
												unset($finalRows['link_is_search']);
												unset($finalRows['link_create_activity']);

												unset($finalRows['Docs']);
												unset($finalRows['doc_element_id']);

												$row['Documents'][]= $finalRows;
											}
											if( !empty($finalRows['Notes']) && $finalRows['Notes'] !=0 ){

												// $finalRows['element_id'] = $finalRows['note_element_id'];
												$finalRows['title'] = $finalRows['note_title'];
												$finalRows['description'] = $finalRows['note_description'];
												$finalRows['creater_id'] = $finalRows['note_creater_id'];
												$finalRows['updated_user_id'] = $finalRows['note_updated_user_id'];
												$finalRows['status'] = $finalRows['note_status'];
												$finalRows['created'] = $finalRows['note_created'];
												$finalRows['modified'] = $finalRows['note_modified'];
												$finalRows['is_search'] = $finalRows['note_is_search'];
												$finalRows['create_activity'] = $finalRows['note_create_activity'];


												unset($finalRows['Docs']);
												unset($finalRows['Links']);

												unset($finalRows['doc_element_id']);
												unset($finalRows['doc_title']);
												unset($finalRows['doc_file_name']);
												unset($finalRows['doc_file_size']);
												unset($finalRows['doc_file_type']);
												unset($finalRows['doc_feedback_attachments_id']);
												unset($finalRows['doc_status']);
												unset($finalRows['doc_created']);
												unset($finalRows['doc_modified']);
												unset($finalRows['doc_is_search']);
												unset($finalRows['doc_create_activity']);

												unset($finalRows['Links']);
												unset($finalRows['link_element_id']);
												unset($finalRows['link_title']);
												unset($finalRows['link_type']);
												unset($finalRows['link_references']);
												unset($finalRows['link_embed_code']);
												unset($finalRows['link_creater_id']);
												unset($finalRows['link_updated_user_id']);
												unset($finalRows['link_status']);
												unset($finalRows['link_created']);
												unset($finalRows['link_modified']);
												unset($finalRows['link_is_search']);
												unset($finalRows['link_create_activity']);

												unset($finalRows['Notes']);
												unset($finalRows['note_element_id']);

												$row['Notes'][]= $finalRows;
											}
										}
									}
									// pr($row);
									/*pr($row['Documents']);
									pr($row['Notes']); */
									//die("000000000045");
									//unset($eleDetail[0]['Element']['id']);


									$target_wsp_id = area_workspace_id($postElements['area_id']);
									$target_operation = $this->restrict_copy_paste($target_wsp_id,$postElements['area_id'],$element_id);

									if(!empty($target_operation['success'])) {

										//unset($row['Area']);
										$row['Element']['area_id'] = $postElements['area_id'];
										$max_order = task_max_sort_order($postElements['area_id']);
										$row['Element']['sort_order'] = $max_order;
										//unset($row['Element']['id']);

										$row['Element']['updated_user_id'] = $eleDetail[0]['Element']['updated_user_id'];
										$row['Element']['title'] = $eleDetail[0]['Element']['title'];
										$row['Element']['description'] = $eleDetail[0]['Element']['description'];
										$row['Element']['comments'] = $eleDetail[0]['Element']['comments'];
										$row['Element']['date_constraints'] = $eleDetail[0]['Element']['date_constraints'];
										$row['Element']['start_date'] = $eleDetail[0]['Element']['start_date'];
										$row['Element']['end_date'] = $eleDetail[0]['Element']['end_date'];
										$row['Element']['sign_off'] = $eleDetail[0]['Element']['sign_off'];
										$row['Element']['color_code'] = $eleDetail[0]['Element']['color_code'];
										$row['Element']['studio_status'] = $eleDetail[0]['Element']['studio_status'];
										$row['Element']['is_search'] = $eleDetail[0]['Element']['is_search'];

										if (isset($nPermit) && !empty($nPermit)) {
										}else{
											$row['Element']['created_by'] = $user_id;;
										}



										/* if( FUTURE_DATE == 'on' ){
											$taget_wsp_detail = $this->Workspace->find('first',['conditions'=>['Workspace.id'=>$target_wsp_id],'recursive'=>-1,'fields'=>['start_date','end_date']]);
											$row['Element']['start_date']= $taget_wsp_detail['Workspace']['start_date'];
											$row['Element']['end_date']= $taget_wsp_detail['Workspace']['end_date'];
										} */

										// copy only element data
										// $allData['Element'] = $row['Element'];
										$allData['Element'] = $row;
										//pr($allData['Element']['Permissions'][0]); die;
										$nPermit = $allData['Element']['Permissions'] = $allData['Element']['Permissions'];
										//pr($allData['Element']['Permissions'][0]); die;

										// find and remove element_id and primary id from all element assets
										// that are - element document, links and notes
										/* if (isset($row['Links']) && !empty($row['Links'])) {
											$nLinks = arraySearch($row['Links'], 'link_element_id', $element_id, true);
											$allData['Links'] = arraySearch($nLinks, 'id', null, true);
										}
										if (isset($row['Documents']) && !empty($row['Documents'])) {
											$nDocuments = arraySearch($row['Documents'], 'element_id', $element_id, true);
											$allData['Documents'] = arraySearch($nDocuments, 'id', null, true);
										}
										if (isset($row['Notes']) && !empty($row['Notes'])) {
											$nNotes = arraySearch($row['Notes'], 'element_id', $element_id, true);
											$allData['Notes'] = arraySearch($nNotes, 'id', null, true);
										} */
										/* $nPermit = null;
										if (isset($row['Permissions']) && !empty($row['Permissions'])) {
											$nPermit = arraySearch($row['Permissions'][0], 'user_id', $user_id, false);
										} */

										//pr($nPermit); die;

										$area_id = $row['Element']['area_id'];
										// NOW SAVE IT TO DATABASE
										// if( $this->Element->save($newRow) ) {
										if ($this->Element->saveAll($allData, array(
											'deep' => true,
										))) {

											$insert_id = $this->Element->getLastInsertId();
											//added by pawan
											$eleWorkspaceId = element_workspace($insert_id);

											$this->loadModel('ElementPermission');
											if (isset($nPermit) && !empty($nPermit)) {
												$arr['ElementPermission']['user_id'] = $user_id;
												$arr['ElementPermission']['element_id'] = $insert_id;
												$arr['ElementPermission']['project_id'] = $postprojectid;
												$arr['ElementPermission']['workspace_id'] = $eleWorkspaceId;
												$arr['ElementPermission']['permit_read'] = (!empty($nPermit)) ? $nPermit[0]['permit_read'] : 0;
												$arr['ElementPermission']['permit_add'] = (!empty($nPermit)) ? $nPermit[0]['permit_add'] : 0;
												$arr['ElementPermission']['permit_edit'] = (!empty($nPermit)) ? $nPermit[0]['permit_edit'] : 0;
												$arr['ElementPermission']['permit_delete'] = (!empty($nPermit)) ? $nPermit[0]['permit_delete'] : 0;
												$arr['ElementPermission']['permit_copy'] = (!empty($nPermit)) ? $nPermit[0]['permit_copy'] : 0;
												$arr['ElementPermission']['permit_move'] = (!empty($nPermit)) ? $nPermit[0]['permit_move'] : 0;
												$arr['ElementPermission']['is_editable'] = (!empty($nPermit)) ? $nPermit[0]['is_editable'] : 0;
											} else {
												$arr['ElementPermission']['user_id'] = $user_id;
												$arr['ElementPermission']['element_id'] = $insert_id;
												$arr['ElementPermission']['project_id'] = $postprojectid;
												$arr['ElementPermission']['workspace_id'] = $eleWorkspaceId;
												$arr['ElementPermission']['permit_read'] = 1;
												$arr['ElementPermission']['permit_add'] = 1;
												$arr['ElementPermission']['permit_edit'] = 1;
												$arr['ElementPermission']['permit_delete'] = 1;
												$arr['ElementPermission']['permit_copy'] = 1;
												$arr['ElementPermission']['permit_move'] = 1;
												$arr['ElementPermission']['is_editable'] = 1;
											}
											$this->ElementPermission->save($arr);

											//$elementPermissionInsert_id = $this->ElementPermission->getLastInsertId();
											// Get Project Id with Element id; Update Project modified date
											//$this->update_project_modify($element_id);

											// Now copy all the documents of the selected element to the target element
											$task_response = $this->cut_copy_paste_docs($element_id, $insert_id, $postElementAction);

											$success = true;
											$response['success'] = true;
											$response['msg'] = $insert_id;
										}
									}
									else{
										$success = false;
										$response['success'] = false;
										$response['error'] = $target_operation['message'];
									}
								} else if ($postElementAction == 'cut' || $postElementAction == 'drag_drop' || $postElementAction == 'move_to') {

									$target_wsp_id = area_workspace_id($postElements['area_id']);
									$target_operation = $this->restrict_copy_paste($target_wsp_id,$postElements['area_id'],$element_id);

									if(!empty($target_operation['success'])) {
										// UPDATE AREA ID, IF CUT AND DRAG-DROP PERFORMED
										$this->Element->id = $element_id;

										$old_ele_pid = element_project($element_id);
										$old_ele_wspid = element_workspace($element_id);

										// Get total elements in target area
										$max_order = task_max_sort_order($postElements['area_id']);

										if ($this->Element->save(['area_id' => $postElements['area_id'],'created_by'=>$this->user_id ,'sort_order' => ($max_order)])) {
											if ($postElementAction == 'move_to') {

												if ($this->ElementPermission->deleteAll(['ElementPermission.element_id'=> $element_id])) {
													$ele_pid = element_project($element_id);
													$ele_wspid = element_workspace($element_id);
													$eparr['ElementPermission']['user_id'] = $this->user_id;
													$eparr['ElementPermission']['element_id'] = $element_id;
													$eparr['ElementPermission']['project_id'] = element_project($element_id);
													$eparr['ElementPermission']['workspace_id'] = element_workspace($element_id);
													$eparr['ElementPermission']['permit_read'] = 1;
													$eparr['ElementPermission']['permit_add'] = 1;
													$eparr['ElementPermission']['permit_edit'] = 1;
													$eparr['ElementPermission']['permit_delete'] = 1;
													$eparr['ElementPermission']['permit_copy'] = 1;
													$eparr['ElementPermission']['permit_move'] = 1;
													$eparr['ElementPermission']['is_editable'] = 1;
													$this->ElementPermission->save($eparr);

													$elementPermissionInsert_id = $this->ElementPermission->getLastInsertId();

												}
											}

											// Get Project Id with Element id; Update Project modified date
										//	$this->update_project_modify($element_id);

											$success = true;
										}
									}
									else{
										$success = false;
										$response['success'] = false;
										$response['error'] = $target_operation['message'];
									}

								} else {
									$success = false; // ERROR!!!
									$response['msg'] = 'No action provided.';
								}

								// AFTER ALL DONE
								if ($success == true) {
									$response['success'] = true;
									$response['content'] =  null;
								} else {
									$response['msg'] = 'Anything wrong in process.';
								}
							}
						}
					}

					echo json_encode($response);
					exit();
				}
	}

	/**
	 * Update background sort order of an element in workspace->areas page
	 *
	 * @param
	 *        	$element_id
	 *
	 * @return void
	 */
	public function update_sort_order_task($element_id = null) {
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
				$area_id = $post['Element']['area_id'];
				$this->Element->id = null;
				$error_data = null;

				if (isset($post) && !empty($post)) {

					if (isset($post['Element']['sort_order']) && !empty($post['Element']['sort_order'])) {
						$id = $post['Element']['id'];
						$area_id = $post['Element']['area_id'];
						$sort_order = $post['Element']['sort_order'];
						$direction = $post['direction'];
						$previous_element = $next_element = null;
						// find previous element
						if ($direction == 'up') {
							$neighbours = $this->Element->find('first', array(
								'conditions' => ['Element.sort_order <' => $sort_order, 'Element.area_id' => $area_id],
								'order' => 'Element.sort_order DESC',
								'fields' => ['id', 'sort_order'],
								'recursive' => -1,
								'limit' => 1,
							));
							// pr($neighbours, 1);

							$neighbour_sortorder = null;
							if (isset($neighbours) && !empty($neighbours)) {
								$neighbour_sortorder = $neighbours['Element']['sort_order'];
								// e($neighbour_sortorder);
								// get current element's data.
								$current_el = getByDbId('Element', $id);
								$current_sort_order = $current_el['Element']['sort_order'];
								// e($current_sort_order, 1);

								$this->Element->id = null;
								$this->Element->id = $neighbours['Element']['id'];
								if ($this->Element->saveField('sort_order', $current_sort_order)) {
									//$this->update_project_modify($id);
								}
								// update current
								$this->Element->id = $id;
								$this->Element->saveField('sort_order', $neighbour_sortorder);
							}
						}

						// find next element
						if ($direction == 'down') {
							$neighbours = $this->Element->find('first', array(
								'conditions' => ['Element.sort_order >' => $sort_order, 'Element.area_id' => $area_id],
								'order' => 'Element.sort_order ASC',
								'fields' => ['id', 'sort_order'],
								'recursive' => -1,
								'limit' => 1,
							));
							// pr($neighbours, 1);

							$neighbour_sortorder = null;
							if (isset($neighbours) && !empty($neighbours)) {
								$neighbour_sortorder = $neighbours['Element']['sort_order'];
								// e($neighbour_sortorder);
								// get current element's data.
								$current_el = getByDbId('Element', $id);
								$current_sort_order = $current_el['Element']['sort_order'];
								// e($current_sort_order, 1);

								$this->Element->id = null;
								$this->Element->id = $neighbours['Element']['id'];
								if ($this->Element->saveField('sort_order', $current_sort_order)) {
									//$this->update_project_modify($id);
								}
								// update current
								$this->Element->id = $id;
								$this->Element->saveField('sort_order', $neighbour_sortorder);
							}
						}
						$eerror_count = (isset($error_data) && !empty($error_data)) ? count($error_data) : 0;

						if ($eerror_count <= 0) {

							$elements_details = $edata = null;
							$area_id = $post['Element']['area_id'];

							$this->loadModel("Area");
							$response['success'] = true;
							$response['msg'] = "Success";
						}
					} else {
						die("ERROR");
					}
				}
			}

			echo json_encode($response);
			exit();
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


				//pr($area_element);

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

			$this->render('/Projects/partials/area_task_elements');

			// echo json_encode($html);
			// exit();

		}
	}

	public function delete_an_task($element_id = null, $update_page = false) {

		if ($this->request->isAjax()) {

			$this->layout = false;
			$viewData = null;
			$viewData['element_id'] = $element_id;
			$viewData['update_page'] = $update_page;
			$viewData['project_id'] = element_project($element_id);

			$task_data = [
				'project_id' => element_project($element_id),
				'workspace_id' => element_workspace($element_id),
				'element_id' => $element_id,
				'element_type' => 'element_tasks',
				'user_id' => $this->user_id,
				'relation_id' => $element_id,
				'updated_user_id' => $this->user_id,
				'message' => 'Task deleted',
				'updated' => date("Y-m-d H:i:s"),
			];
			$this->loadModel('Activity');
			$this->Activity->id = null;
			$this->Activity->save($task_data);

			$this->set($viewData);
			$this->render('/Entities/partials/delete_an_task');

		}
	}

	/**
	 * Update background color code of an element in workspace->areas page
	 *
	 * @param
	 *        	$element_id
	 *
	 * @return void
	 */
	public function remove_task($id = null) {
		if ($this->request->isAjax()) {
			// die('sdfsfdf');
			$response = [
				'success' => false,
				'msg' => '',
				'content' => null,
			];

			$this->layout = 'ajax';
			$this->autoRender = false;
			$this->loadModel('ElementType');
			$this->loadModel('CurrentTask');

			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;

				//pr($post, 1);

				$id = (isset($post['Element']['id']) && !empty($post['Element']['id'])) ? $post['Element']['id'] : $id;

				$this->Element->id = $id;

				// Get Project Id with Element id; Update Project modified date
				// $this->update_project_modify($id);

				/* ============= Element Owners =============================== */
				$participants_owners = array();
				$participantsGpOwner = array();
				$elementDetail = $element = $this->Element->find('first', ['conditions' => ['Element.id' => $id], 'recursive' => -1, 'fields' => ['area_id', 'title']]);
				$elementName = '';
				if (isset($elementDetail['Element']['title']) && !empty($elementDetail['Element']['title'])) {
					$elementName = $elementDetail['Element']['title'];
				}

				$project_id = element_project($id);
				$workspace_id = element_workspace($id);
				// $all_owner = element_users(array($id), $project_id);

				$all_owner = [];
				$all_users = $this->objView->loadHelper('Permission')->taskUsers($id);
				if(isset($all_users) && !empty($all_users)){
					$all_owner = Set::extract($all_users, '{n}.user_details.user_id');
				}

				/* ============================================================= */

				if (!$this->Element->exists()) {
					throw new NotFoundException(__('Invalid Element'), 'error');
				}

				$this->CurrentTask->deleteAll(array('CurrentTask.task_id' => $id));


				//DELETE ALL THE ENTRIES FROM PLAN EFFORT TABLE
				$this->loadModel('PlanEffort');
				$pe_del = array('element_id'=>$id);
				$this->PlanEffort->deleteAll($pe_del);

				if ($this->Element->delete()) {

					/* exits sign-off entry delete ****************/
					$del = array('element_id'=>$id);
					$signoffdata = $this->SignoffTask->find('first',array('conditions'=>$del ));
					if( isset($signoffdata) ){
						if( !empty(!empty($signoffdata['SignoffTask']['task_evidence'])) ){
							$folder_url = WWW_ROOT . ELEMENT_SIGNOFF_PATH;
							unlink($folder_url.'/'.$signoffdata['SignoffTask']['task_evidence']);
						}
						$this->SignoffTask->deleteAll($del);
					}

					$task_ids =  $id;
					$user_id = $this->Session->read("Auth.User.id");
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
					//$this->Element->deleteAll(array('Element.id' => $task_ids));
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
					//$this->loadModel('SignoffTask');
					//$this->loadModel('CurrentTask');
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
					//$this->SignoffTask->deleteAll(array('SignoffTask.element_id' => $task_ids));
 					//$this->CurrentTask->deleteAll(['CurrentTask.task_id' => $task_ids]);
					$this->Reminder->deleteAll(['Reminder.element_id' => $task_ids]);

					}


					$response['success'] = true;
					$response['msg'] = 'Element has been deleted successfully.';

					/*========== Strat Element Delete Email =========================== */

					$all_owner_tot = ( isset($all_owner) && !empty($all_owner) ) ? count($all_owner) : 0;

					if ( $all_owner_tot > 0 && !empty($elementName) && !empty($project_id)) {
						// pr($all_owner, 1);
						/************** socket messages **************/
						if (SOCKET_MESSAGES) {
							$current_user_id = $this->user_id;
							$ele_users = $all_owner;

							if (isset($ele_users) && !empty($ele_users)) {
								if (($key = array_search($current_user_id, $ele_users)) !== false) {
									unset($ele_users[$key]);
								}
							}

							$del_users = null;
							if (isset($ele_users) && !empty($ele_users)) {
								foreach ($ele_users as $key1 => $value1) {
									if (web_notify_setting($value1, 'element', 'element_deleted')) {
										$del_users[] = $value1;
									}
								}
							}
							$userDetail = get_user_data($current_user_id);
							$content = [
								'notification' => [
									'type' => 'task_deleted',
									'created_id' => $current_user_id,
									'project_id' => $project_id,
									'refer_id' => $workspace_id,
									'creator_name' => $userDetail['UserDetail']['full_name'],
									'subject' => 'Task deleted',
									'heading' => 'Task: ' . strip_tags($element['Element']['title']),
									'sub_heading' => 'Project: ' . strip_tags(getFieldDetail('Project', $project_id, 'title')),
									'date_time' => timezoneTimeConvertor($this->user_id, date('Y-m-d g:iA'), $format = 'd M Y g:iA') . user_country($this->user_id),
								],
							];
							if (is_array($del_users)) {
								$content['received_users'] = array_values($del_users);
							}
							$response['content']['socket'] = $content;
						}
						/************** socket messages **************/
						$this->elementDeleteEmail($elementName, $project_id, $all_owner,$workspace_id);
					}

					/*========== Strat Element Delete Email =========================== */

				} else {
					$response['msg'] = 'Task could not deleted successfully.';
				}
				// $this->Element->_query(1);
			}

			echo json_encode($response);
			exit();
		}
	}

	// task start and end date
	public function sendingElementDetail($element_id = null){

		if( isset($element_id) && !empty($element_id) ){

			$eleDate = $this->Element->find('first',array('conditions'=>array('Element.id'=>$element_id),'recursive'=>-1,'fields'=>array('Element.start_date','Element.end_date') ) );
			if( isset($eleDate) && !empty($eleDate) && !empty($eleDate['Element']['start_date']) && !empty($eleDate['Element']['end_date'])  ){
				return $eleDate;
			} else {
				return null;
			}

		}
	}

	// Target Workspace start and end date
	public function targetWspDetail($workspace_id = null){
		if( isset($workspace_id) && !empty($workspace_id) ){

			$wspDate = $this->Workspace->find('first',array('conditions'=>array('Workspace.id'=>$workspace_id),'recursive'=>-1 ) );
			return $wspDate;

		}
	}

	public function signoff_task($element_id = null){

		if( isset($element_id) && !empty($element_id) ){
			$data = $this->objView->loadHelper('Common')->element_dependencies_gated($this->Session->read('Auth.User.id'),$element_id);

			if( isset($data) && !empty($data['element']) ){
				$this->Element->unbindAll();
				$eleData = $this->Element->find('all',array(
						'conditions'=>array('Element.id'=>$data['element']),
						'fields'=>array('Element.id','Element.title','Element.sign_off')
					)
				);
				$this->set('elements',$eleData);
				$this->set('elementid',$element_id);
				$this->render('/Entities/signoff_task');
			}
		}

	}

	public function tasks_signoff($element_id = null){

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			if( isset($element_id) && !empty($element_id) ){

				$this->set('sign_off',1);
				$this->set('element_id',$element_id);
				$this->render('/Entities/element_files/task_signoff_model');

			}
		}

	}


	public function show_signoff($element_id = null){

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			if( isset($element_id) && !empty($element_id) ){

				$this->set('element_id',$element_id);
				$comment =$this->SignoffTask->find('first',array(
					'conditions'=> array('SignoffTask.element_id'=> $element_id )
					)
				);

				$userDetail = get_user_data($comment['SignoffTask']['user_id']);

				if( isset($comment) && !empty($comment) ){
					$this->set('comment',$comment);
					$this->set('userDetail',$userDetail);
				}
				$this->render('/Entities/element_files/show_signoff_model');

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
			$signoff_type = 'element';
			$task_comment = '';
			$element_id = '';
			$current_user_id = $this->Auth->user('id');

			if ($this->request->is('post') || $this->request->is('put')) {
				// pr($this->request->data, 1);

				if( isset($this->request->data['signoff_comment']) && !empty($this->request->data['signoff_comment']) ){
					$task_comment = $this->request->data['signoff_comment'];
				}

				if( isset($this->request->data['element_id']) && !empty($this->request->data['element_id']) ){
					$element_id = $this->request->data['element_id'];
				}


				$evidence_title = '';
				$check_file = true;
				if( isset($_FILES['file']) && !empty($_FILES['file']['name']) ){


						$sizeLimit = 10; // 10MB
						//$sizeLimit = 3 * 1024 ; // 10MB
						$folder_url = WWW_ROOT . ELEMENT_SIGNOFF_PATH;
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

								/*  echo $sizeMB ."==". $sizeLimit;
								 die; */
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
										$last_row = $this->SignoffTask->find('first', array(
											'recursive' => '-1',
											'fields' => [
												'id',
											],
											'order' => 'SignoffTask.id DESC',
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


					if( isset($task_comment) && !empty($task_comment) && isset($task_evidence) && !empty($task_evidence) && isset($element_id) && !empty($element_id) ){

						$save_signoff = true;

					} else if(isset($task_comment) && !empty($task_comment) && !isset($task_evidence) && empty($task_evidence) && isset($element_id) && !empty($element_id)){

						$save_signoff = true;

					} else {

						if( isset($task_comment) && !empty($task_comment) && isset($element_id) && !empty($element_id) ) {
							$save_signoff = true;
						} else {
							$save_signoff = false;
						}
					}
				} else {
					$save_signoff == false;
				}

				if( $save_signoff == true ){

					//DELETE ALL THE ENTRIES FROM PLAN EFFORT TABLE
					$this->loadModel('PlanEffort');
					$del = array('element_id'=>$element_id);
					$this->PlanEffort->deleteAll($del);

					//exits entry delete
					$this->SignoffTask->deleteAll($del);


					$this->request->data['SignoffTask']['id'] = null;
					$this->request->data['SignoffTask']['user_id'] = $current_user_id;
					$this->request->data['SignoffTask']['element_id'] = $element_id;
					$this->request->data['SignoffTask']['signoff_type'] = $signoff_type;
					$this->request->data['SignoffTask']['task_comment'] = $task_comment;
					$this->request->data['SignoffTask']['task_evidence'] = $task_evidence;
					$this->request->data['SignoffTask']['evidence_title'] = $evidence_title;

					//pr($this->request->data);

					if( $this->SignoffTask->save($this->request->data['SignoffTask']) ){

						/*============= sign off task =========================*/

							$this->request->data['Element']['create_activity'] = 1;
							$this->request->data['Element']['updated_user_id'] = $this->Session->read('Auth.User.id');
							$post = $this->request->data['Element'];

							// pr($post, 1);

							if (isset($element_id) && !empty($element_id)) {
								$id = $element_id;

								$this->Element->id = $id;
								$element_id = $id;
								if (!$this->Element->exists()) {
									throw new NotFoundException(__('Invalid detail'), 'error');
								}
								// SIGNOFF DATE
								$post['sign_off_date'] = date('Y-m-d');

								if ($this->Element->save($post)) {

									$this->Element->unbindModel(array('hasOne' => ['ElementDecision']));
									$this->Element->updateAll(array("Element.create_activity" => 0), array("Element.id" => $id));
									// Get Project Id with Element id; Update Project modified date
									// $this->update_project_modify($id);
									// $this->update_task_up_activity($id);

									$emai_type = 'signed-off';
									if ($post['sign_off'] == 0) {
										$emai_type = 're-opened';
									}

									$response['success'] = true;
									$response['msg'] = 'You have been signed off successfully.';
									$response['content'] = [];

									/* ============= Strat Element Signoff Email TO Owners =============================== */

									$participants_owners = array();
									$participantsGpOwner = array();
									$elementDetail = $this->Element->findById($element_id);
									$elementName = '';
									if (isset($elementDetail['Element']['title']) && !empty($elementDetail['Element']['title'])) {
										$elementName = $elementDetail['Element']['title'];
									}
									$project_id = element_project($element_id);

									$all_owner = element_users(array($element_id), $project_id);
									$all_owner_tot = ( isset($all_owner) && !empty($all_owner) ) ? count($all_owner) : 0;
									if (isset($all_owner) && !empty($all_owner)) {
										if (($key = array_search($this->user_id, $all_owner)) !== false) {
											unset($all_owner[$key]);
										}
									}
										// pr($all_owner, 1);
									if ( ($all_owner_tot) > 0 && !empty($elementName) && !empty($project_id)) {
										/************** socket messages **************/
										if (SOCKET_MESSAGES) {
											$current_user_id = $this->user_id;
											$ele_users = $all_owner;
											if (isset($ele_users) && !empty($ele_users)) {
												if (($key = array_search($current_user_id, $ele_users)) !== false) {
													unset($ele_users[$key]);
												}
											}
											$s_open_users = $r_open_users = null;
											if (isset($ele_users) && !empty($ele_users)) {
												foreach ($ele_users as $key => $value) {
													if (web_notify_setting($value, 'element', 'element_sign_off')) {
														$s_open_users[] = $value;
													}
													if (web_notify_setting($value, 'element', 'element_reopened')) {
														$r_open_users[] = $value;
													}
												}
											}
											$userDetail = get_user_data($current_user_id);
											$heading = (isset($post['sign_off']) && $post['sign_off'] == 1) ? 'Task sign-off' : 'Task re-opened';
											$content = [
												'notification' => [
													'type' => 'task_signoff',
													'created_id' => $current_user_id,
													'project_id' => $project_id,
													'refer_id' => $id,
													'creator_name' => $userDetail['UserDetail']['full_name'],
													'subject' => $heading,
													'heading' => 'Task: ' . strip_tags($elementDetail['Element']['title']),
													'sub_heading' => 'Project: ' . strip_tags(getFieldDetail('Project', $project_id, 'title')),
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
										$this->elementSignOffEmail($elementName, $project_id, $all_owner, $emai_type,$element_id);
									}

									/*========== End Element Signoff Email TO Owners =========================== */

								} else {
									$response['msg'] = 'Signing off could not be completed. Please try again later.';
								}
								// $this->Element->_query(1);
							}

						/*********************************************************/


						//$response['success'] = true;
					}

				}
			}
			echo json_encode($response);
			exit();
		}

	}

	public function download_signoff($id = null) {

		if (isset($id) && !empty($id)) {

			$data = $this->SignoffTask->findById($id);
			if (empty($data)) {
				throw new NotFoundException();
			}

			$response = [
				'success' => false,
				'content' => null,
			];

			if (isset($data) && !empty($data)) {
				// Send file as response
				$response['content'] = ELEMENT_SIGNOFF_PATH  . DS . $data['SignoffTask']['task_evidence'];
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

	public function dependancy_gated_status(){

		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			$this->autoRender = false;
			$response = [
				'success' => false,
				'msg' => '',
				'content' => null,
			];

			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;

				$element_id = $post['element_id'];

				$viewData = null;

				$checkcnt = $this->objView->loadHelper('ViewModel')->checkEleDepndancy($element_id);

				if( isset($checkcnt) && $checkcnt == 'none' ){
					$response['content'] = 1;
				}

			}
			echo json_encode($response);
			exit();
		}
	}

	public function check_task_restrict(){

		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			$this->autoRender = false;
			$response = [
				'success' => false,
				'msg' => '',
				'content' => 0
			];



			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;
				$id = $post['element_id'];


				$view = new View();
				$Common = $view->loadHelper('Common');
				$signofftask = '';

				if( isset($id) && !empty($id) ){

				$latestDate = $this->Element->findById($id);



				if($latestDate['Element']['date_constraints'] !=1){

					return;
				}

				if (((isset($latestDate['Element']['start_date']) && !empty($latestDate['Element']['start_date'])) && date('Y-m-d', strtotime($latestDate['Element']['start_date'])) > date('Y-m-d')) && $latestDate['Element']['sign_off'] != 1) {

					return;
				}



					$gated_check ='';

					//$gated_check = $this->objView->loadHelper('ViewModel')->checkEleDepndancy($id);

					$gated_check = $Common->element_dependencies_gated($this->Session->read('Auth.User.id'),$id);

					// pr($gated_check,1);

					$signofftask = $this->Common->singoffelement($id);


					$this->loadModel('SignoffTask');
					$tComment = $this->SignoffTask->find('count', ['conditions' => ['SignoffTask.element_id' => $id]]);

					$this->loadModel('ElementPermission');
					$wsp = $this->ElementPermission->find('first', ['conditions' => ['ElementPermission.element_id' => $id]]);

					$this->loadModel('Workspace');
					$wsp_id = $this->Workspace->find('first', ['conditions' => ['Workspace.id' => $wsp['ElementPermission']['workspace_id']]]);



					if( $signofftask != '1' ){
						if(isset($wsp_id['Workspace']) && $wsp_id['Workspace']['sign_off'] ==1){
							$response['content'] = 5;
						}
						if(isset($tComment) && $tComment > 0){
						$response['content'] = 4;
						}else{
						$response['content'] = 1;
						}
						$response['msg'] = $signofftask;

					} else if( (!isset($gated_check['success']) || empty($gated_check['success']) || $gated_check['success'] != 1) &&  (isset($gated_check['element']) && !empty($gated_check['element']))){
					//} else if( isset($gated_check) && !empty($gated_check) && $gated_check != 'none' ){

						$response['content'] = 2;

					} else {

						$response['content'] = 3;
						$response['success'] = true;

					}

				}

			}





			echo json_encode($response);
			exit();
		}

	}

  	public function element_options_partial() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$response = [
				'success' => false,
				'msg' => '',
				'content' => null,
			];

			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;

				//$project_id = (isset($post['project_id']) && !empty($post['project_id'])) ? $post['project_id'] : null;
				//$workspace_id = (isset($post['workspace_id']) && !empty($post['workspace_id'])) ? $post['workspace_id'] : null;
				$element_id = (isset($post['element_id']) && !empty($post['element_id'])) ? $post['element_id'] : null;
				$result = $this->Element->find('first', [
					'conditions' => [
						'Element.id' => $element_id,
					]
				]);

				//===========================================================

				$dateprec = STATUS_NOT_SPACIFIED;

				if (isset($result['Element']['date_constraints']) && !empty($result['Element']['date_constraints']) && $result['Element']['date_constraints'] > 0) {

					if (((isset($result['Element']['start_date']) && !empty($result['Element']['start_date'])) && date('Y-m-d', strtotime($result['Element']['start_date'])) > date('Y-m-d')) && $result['Element']['sign_off'] != 1) {

						$dateprec = STATUS_NOT_STARTED;
					} else if (((isset($result['Element']['end_date']) && !empty($result['Element']['end_date'])) && date('Y-m-d', strtotime($result['Element']['end_date'])) < date('Y-m-d')) && $result['Element']['sign_off'] != 1) {

						$dateprec = STATUS_OVERDUE;
					} else if (isset($result['Element']['sign_off']) && !empty($result['Element']['sign_off']) && $result['Element']['sign_off'] > 0) {

						$dateprec = STATUS_COMPLETED;
					} else if ((((isset($result['Element']['end_date']) && !empty($result['Element']['end_date'])) && (isset($result['Element']['start_date']) && !empty($result['Element']['start_date']))) && (date('Y-m-d', strtotime($result['Element']['start_date'])) <= date('Y-m-d')) && date('Y-m-d', strtotime($result['Element']['end_date'])) >= date('Y-m-d')) && $result['Element']['sign_off'] != 1) {

						$dateprec = STATUS_PROGRESS;
					}
				}

				$signoff_comment = $this->SignoffTask->find('count', array('conditions'=>array('SignoffTask.element_id'=>$element_id) ));



				$this->set('signoff_comment', $signoff_comment);
		// echo $dateprec;
		// die;
				$this->set('date_status', $dateprec);
				$this->set('element_id', $element_id);
				$this->set('eldata', $result);
				$response = [
					'success' => true,
					'msg' => '',
					'content' => 'done',
				];

			}

		}

		$this->render(DS . 'Entities' . DS . 'element_files' . DS . 'element_options_partial');

	}

	public function el_assets() {

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
				$taskID = (isset($post['taskID']) && !empty($post['taskID'])) ? $post['taskID'] : null;



				$this->set('project_id', $project_id);
				$this->set('workspace_id', $workspace_id);
				$this->set('taskID', $taskID);

			}

		}

		$this->render(DS . 'Entities' . DS . 'element_files' . DS . 'el_assets');
	}


	public function confidence($element_id = null){

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$this->set('element_id', $element_id);
			$signoffdata = $this->Element->find('first',array('fields'=>['Element.id','Element.sign_off'],'conditions'=>['Element.id'=>$element_id],'recursive'=>-1 ));

			$this->set('sign_off', $signoffdata['Element']['sign_off']);

			// GET ALL PREVIOUS CONFIDENCE DATA
			$query = "SELECT
						DISTINCT(el.id),
						el.user_id,
						el.level,
						el.comment,
						el.created,
						CONCAT_WS(' ',ud.first_name , ud.last_name) as full_name,
						ud.profile_pic,
						ud.organization_id,
						ud.user_id
					FROM user_permissions up

					LEFT JOIN element_levels el
					ON up.element_id = el.element_id

					LEFT JOIN user_details ud
						ON ud.user_id = el.user_id

					WHERE
						el.element_id = $element_id AND
					    up.element_id IS NOT NULL

					ORDER BY el.created DESC  ";
			$history = $this->Element->query($query);
			$this->set('history', $history);
			$this->render('/Entities/element_files/confidence');
		}

	}

	public function save_confidence(){

		$response = ['success' => false, 'msg' => ''];
		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;
				// pr($post, 1);
				$element_id = $post['element_id'];
				$this->loadModel('ElementLevel');


				// get previous entries of this task
				$query = "SELECT count(*) AS counter FROM element_levels WHERE element_id = '$element_id' AND level = '".$post['level']."'";
				$levels = $this->Element->query($query);

				if(isset($levels[0][0]['counter']) && !empty($levels[0][0]['counter'])) {
					$response['msg'] = "Level must be set to a new value.";
				}
				else{
					$query = "UPDATE element_levels SET is_active = '0' WHERE element_id = '$element_id'";
					$this->Element->query($query);
					$post['user_id'] = $this->user_id;
					$post['is_active'] = 1;
					if($this->ElementLevel->save($post)){
						// SAVE TASK ACTIVITY
						$task_data = [
							'project_id' => $post['project_id'],
							'workspace_id' => $post['workspace_id'],
							'element_id' => $element_id,
							'element_type' => 'element_levels',
							'relation_id' => $this->ElementLevel->getLastInsertId(),
							'user_id' => $this->user_id,
							'updated_user_id' => $this->user_id,
							'message' => 'Task confidence level set',
							'updated' => date("Y-m-d H:i:s"),
						];
						$this->loadModel('Activity');
						$this->Activity->id = null;
						$this->Activity->save($task_data);

						$response['success'] = true;
					}
				}

			}
			echo json_encode($response);
			exit;
		}

	}

	public function delete_confidence(){

		$response = ['success' => false];
		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				$element_id = $post['element_id'];
				$this->loadModel('ElementLevel');
				$this->ElementLevel->id = $post['id'];
				if($this->ElementLevel->delete()){
					$response['success'] = true;
					// SAVE TASK ACTIVITY
					$task_data = [
						'project_id' => $post['project_id'],
						'workspace_id' => $post['workspace_id'],
						'element_id' => $element_id,
						'element_type' => 'element_levels',
						// 'relation_id' => null,
						'user_id' => $this->user_id,
						'updated_user_id' => $this->user_id,
						'message' => 'Task confidence level deleted',
						'updated' => date("Y-m-d H:i:s"),
					];
					$this->loadModel('Activity');
					$this->Activity->id = null;
					$this->Activity->save($task_data);

					$query = "SELECT id FROM element_levels WHERE element_id = '$element_id' ORDER BY created DESC LIMIT 1";
					$first = $this->Element->query($query);
					if(isset($first) && !empty($first)){
						$last_id = $first[0]['element_levels']['id'];
						$this->ElementLevel->id = $last_id;
						$this->ElementLevel->saveField('is_active', 1);
					}
				}

			}
			echo json_encode($response);
			exit;
		}

	}

	public function update_confidence($element_id = null){

		$response = ['success' => false];
		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$this->set('element_id', $element_id);
			$this->render('/Entities/element_files/update_confidence');
		}

	}

	public function update_task_color() {

		if ($this->request->isAjax()) {

			$response = [
				'success' => false
			];

			$this->layout = 'ajax';
			$this->autoRender = false;

			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;
				// pr($post, 1);

				$this->Element->id = $post['task_id'];
				if ($this->Element->saveField('color_code', $post['color_code'])) {

					$response['success'] = true;
					$task_data = [
						'project_id' => element_project($post['task_id']),
						'workspace_id' => element_workspace($post['task_id']),
						'element_id' => $post['task_id'],
						'element_type' => 'element_tasks',
						'user_id' => $this->user_id,
						'relation_id' => $post['task_id'],
						'updated_user_id' => $this->user_id,
						'message' => 'Task color updated',
						'updated' => date("Y-m-d H:i:s"),
					];
					$this->loadModel('Activity');
					$this->Activity->id = null;
					$this->Activity->save($task_data);
				}
			}

			echo json_encode($response);
			exit();
		}
	}

	protected function find_activity($data = null, $filters = null) {
		$limit_query = ' LIMIT '.$this->activity_offset;
		$page = $data['page'];
		if(isset($data['page']) && !empty($data['page'])){
			$limit_query = " LIMIT $page, " . $this->activity_offset;
		}

		$filter_query = ' 1';
		if(isset($data['search_text']) && !empty($data['search_text'])){
			$seperator = '^';
			$search_str= Sanitize::escape(like($data['search_text'], $seperator ));
			$filter_query = " (ele_title LIKE '%$search_str%' ESCAPE '$seperator') ";
		}

		$order = 'ORDER BY el_status ASC';
		if( isset($data['coloumn']) && !empty($data['coloumn']) && isset($data['order']) && !empty($data['order']) ){
			$order = "ORDER BY ".$data['coloumn']." ".$data['order'];
		}

		$element_id = null;
		if(isset($data['element_id']) && !empty($data['element_id'])){
			$element_id = $data['element_id'];
		}

		return $this->objView->loadHelper('Permission')->wsp_activities_tasks($data['project_id'], $data['workspace_id'], $page, $this->activity_offset, $order, $filter_query, $filters, $element_id );
	}

	public function filter_activity(){

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				// pr($post, 1);
				$page = (isset($post['page']) && !empty($post['page'])) ? $post['page'] : 0;
				$order = (isset($post['order']) && !empty($post['order'])) ? $post['order'] : 'ASC';
				$coloumn = (isset($post['coloumn']) && !empty($post['coloumn'])) ? $post['coloumn'] : 'el_status';
				$search_text = (isset($post['search_text']) && !empty($post['search_text'])) ? $post['search_text'] : '';

				$status = (isset($post['status']) && !empty($post['status'])) ? $post['status'] : '';
				$types = (isset($post['types']) && !empty($post['types'])) ? $post['types'] : '';
				$assign = (isset($post['assign']) && !empty($post['assign'])) ? $post['assign'] : '';
				$filters = ['status' => $status, 'types' => $types, 'assign' => $assign];
				$data = [
						'page' => $page,
						'order' => $order,
						'coloumn' => $coloumn,
						'search_text' => $search_text,
						'project_id' => $post['project_id'],
						'workspace_id' => $post['workspace_id']
					];
				$wsp_activities_tasks = $this->find_activity($data, $filters);
				$this->set('wsp_activities_tasks', $wsp_activities_tasks);
				$this->set('project_id', $post['project_id']);
				$this->set('workspace_id', $post['workspace_id']);

			}
			$this->render('/Projects/sections/wsp_activities');
		}
	}

	public function filter_element_activity(){

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				// pr($post, 1);
				$page = (isset($post['page']) && !empty($post['page'])) ? $post['page'] : 0;
				$order = (isset($post['order']) && !empty($post['order'])) ? $post['order'] : 'ASC';
				$coloumn = (isset($post['coloumn']) && !empty($post['coloumn'])) ? $post['coloumn'] : 'el_status';
				$search_text = (isset($post['search_text']) && !empty($post['search_text'])) ? $post['search_text'] : '';
				$element_id = (isset($post['element_id']) && !empty($post['element_id'])) ? $post['element_id'] : '';

				$status = (isset($post['status']) && !empty($post['status'])) ? $post['status'] : '';
				$types = (isset($post['types']) && !empty($post['types'])) ? $post['types'] : '';
				$assign = (isset($post['assign']) && !empty($post['assign'])) ? $post['assign'] : '';
				$filters = ['status' => $status, 'types' => $types, 'assign' => $assign];
				$data = [
						'page' => $page,
						'order' => $order,
						'coloumn' => $coloumn,
						'search_text' => $search_text,
						'project_id' => $post['project_id'],
						'workspace_id' => $post['workspace_id'],
						'element_id' => $element_id
					];
				$wsp_activities_tasks = $this->find_activity($data, $filters);
				$this->set('wsp_activities_tasks', $wsp_activities_tasks);
				$this->set('project_id', $post['project_id']);
				$this->set('workspace_id', $post['workspace_id']);

			}
			$this->render('/Projects/sections/element_activities');
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
				$page = (isset($post['page']) && !empty($post['page'])) ? $post['page'] : 0;
				$order = (isset($post['order']) && !empty($post['order'])) ? $post['order'] : 'ASC';
				$coloumn = (isset($post['coloumn']) && !empty($post['coloumn'])) ? $post['coloumn'] : 'el_status';
				$search_text = (isset($post['search_text']) && !empty($post['search_text'])) ? $post['search_text'] : '';

				$status = (isset($post['status']) && !empty($post['status'])) ? $post['status'] : '';
				$types = (isset($post['types']) && !empty($post['types'])) ? $post['types'] : '';
				$assign = (isset($post['assign']) && !empty($post['assign'])) ? $post['assign'] : '';
				$filters = ['status' => $status, 'types' => $types, 'assign' => $assign];

				$filter_query = ' 1';
				if(isset($search_text) && !empty($search_text)){
					$seperator = '^';
					$search_str = Sanitize::escape(like($search_text, $seperator ));
					$filter_query = " (ele_title LIKE '%$search_str%' ESCAPE '$seperator') ";
				}
				$data = $this->objView->loadHelper('Permission')->wsp_activities_count($post['project_id'], $post['workspace_id'], $filter_query, $filters );
				$count = count($data);

			}
			echo json_encode($count);
			exit;
		}
	}

	public function duplicate_task($element_id = null, $area_id = null, $workspace_id = null, $project_id = null) {
		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$edata = [];
			if(isset($element_id) && !empty($element_id)){
				$query = "SELECT title, description, comments, color_code, date_constraints, start_date, end_date FROM elements WHERE id = $element_id";
				$edata = $this->Element->query($query);
				$edata = $edata[0]['elements'];
				// pr($edata, 1);
			}

			$query = "SELECT a.id, a.title FROM areas a WHERE a.workspace_id = $workspace_id";
			$data = $this->Element->query($query);
			$area_list = Set::combine($data, '{n}.a.id', '{n}.a.title');

			$elementType = $this->ElementType->find('first', array('conditions' => array('ElementType.project_id' => $project_id, 'ElementType.element_id' => $element_id)));

			if (!empty($elementType)) {
				$this->set('selElementType', $elementType['ElementType']['type_id']);
			} else {
				$elementtype = $this->ProjectElementType->find('first', array('conditions' => array('ProjectElementType.project_id' => $project_id, 'ProjectElementType.title' => 'General')));
				$peType = (isset($elementtype['ProjectElementType']['id']) && !empty($elementtype['ProjectElementType']['id'])) ? $elementtype['ProjectElementType']['id'] : null;
				$this->set('selElementType', $peType);

			}
			$query = "SELECT Project.start_date, Project.end_date FROM projects Project WHERE Project.id = $project_id";
			$pdata = $this->Element->query($query);

			$this->set('edata', $edata);
			$this->set('area_list', $area_list);
			$this->set('area_id', $area_id);
			$this->set('workspace_id', $workspace_id);
			$this->set('project_id', $project_id);
			$this->set('element_id', $element_id);
			$this->set('pdata', $pdata);

			$this->render('/Entities/partials/duplicate_task');
		}
	}

	public function add_duplicate_task() {
		if ($this->request->isAjax()) {

			$this->loadModel('UserPermission');

			$response = [
				'success' => false,
				'msg' => '',
				'content' => null,
				'error' => '',
			];
			$success = false;
			$wsp_date_passed_msg = 'Cannot copy Task because the Workspace end date has passed.';

			$this->layout = 'ajax';
			$this->autoRender = false;

			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data['Element'];
				// pr($post, 1);
				$project_id = $post['project_id'];
				$workspace_id = $post['workspace_id'];
				$area_id = $post['area_id'];
				$element_id = $post['element_id'];
				$element_title = $post['title'];
				$element_description = $post['description'];
				$comments = (isset($post['comments']) && !empty($post['comments'])) ? $post['comments'] : '';
				$date_constraints = (!isset($post['date_constraints']) || empty($post['date_constraints'])) ? 0 : 1;
				$start_date = ((isset($post['date_constraints']) && !empty($post['date_constraints'])) && (isset($post['start_date']) && !empty($post['start_date']))) ? date('Y-m-d h:i:s', strtotime($post['start_date'])) : '';
				$end_date = ((isset($post['date_constraints']) && !empty($post['date_constraints'])) && (isset($post['end_date']) && !empty($post['end_date']))) ? date('Y-m-d h:i:s', strtotime($post['end_date'])) : '';
				$user_id = $this->Auth->user('id');

				if ( isset($area_id) && !empty($area_id) ) {

					// CREATE A NEW ELEMENT
					$allData = [
						'Element' => null,
						'Links' => null,
						'Documents' => null,
						'Notes' => null,
					];

					// get is_editable permissions
					$ele_iseditable = $this->ElementPermission->find('first', array('conditions'=>array('ElementPermission.element_id'=>$element_id,'ElementPermission.user_id'=>$user_id),'recursive'=>-1, 'fields'=>'is_editable' )
					);
					// Get Element data
					$ele_query = "SELECT Element.*

								FROM user_permissions

								INNER JOIN elements as Element
									ON Element.id = user_permissions.element_id

								WHERE user_permissions.element_id = $element_id AND user_id = $user_id ";

					$eleDetail = $this->UserPermission->query($ele_query);


					//first union for Links
					//second union for Documents
					//third union for Notes
					$query = "SELECT

						Links.id as Links,
						0 as Docs,
						0 as Notes,
						user_permissions.permit_read,
						user_permissions.permit_add,
						user_permissions.permit_edit,
						user_permissions.permit_delete,
						user_permissions.permit_copy,
						user_permissions.permit_move,

						Links.element_id as link_element_id,
						Links.title as link_title,
						Links.link_type as link_type,
						Links.references as link_references,
						Links.embed_code as link_embed_code,
						Links.creater_id as link_creater_id,
						Links.updated_user_id as link_updated_user_id,
						Links.status as link_status,
						Links.created as link_created,
						Links.modified as link_modified,
						Links.is_search as link_is_search,
						Links.create_activity as link_create_activity,

						0 as doc_element_id,
						0 as doc_title,
						0 as doc_file_name,
						0 as doc_file_size,
						0 as doc_file_type,
						0 as doc_feedback_attachments_id,
						0 as doc_status,
						0 as doc_created,
						0 as doc_modified,
						0 as doc_is_search,
						0 as doc_create_activity,

						0 as note_element_id,
						0 as note_title,
						0 as note_description,
						0 as note_creater_id,
						0 as note_updated_user_id,
						0 as note_status,
						0 as note_created,
						0 as note_modified,
						0 as note_is_search,
						0 as note_create_activity

					FROM user_permissions
					INNER JOIN element_links as Links
						ON Links.element_id = user_permissions.element_id
					WHERE user_permissions.element_id = $element_id AND user_id = $user_id

					UNION distinct

					SELECT
						0 as Links,
						Documents.id as Docs,
						0 as Notes,
						user_permissions.permit_read,
						user_permissions.permit_add,
						user_permissions.permit_edit,
						user_permissions.permit_delete,
						user_permissions.permit_copy,
						user_permissions.permit_move,

						0 as link_element_id,
						0 as link_title,
						0 as link_type,
						0 as link_references,
						0 as link_embed_code,
						0 as link_creater_id,
						0 as link_updated_user_id,
						0 as link_status,
						0 as link_created,
						0 as link_modified,
						0 as link_is_search,
						0 as link_create_activity,

						Documents.element_id as doc_element_id,
						Documents.title as doc_title,
						Documents.file_name as doc_file_name,
						Documents.file_size as doc_file_size,
						Documents.file_type as doc_file_type,
						Documents.feedback_attachments_id as doc_feedback_attachments_id,
						Documents.status as doc_status,
						Documents.created as doc_created,
						Documents.modified as doc_modified,
						Documents.is_search as doc_is_search,
						Documents.create_activity as doc_create_activity,

						0 as note_element_id,
						0 as note_title,
						0 as note_description,
						0 as note_creater_id,
						0 as note_updated_user_id,
						0 as note_status,
						0 as note_created,
						0 as note_modified,
						0 as note_is_search,
						0 as note_create_activity

					FROM user_permissions
					INNER JOIN element_documents as Documents
						ON Documents.element_id = user_permissions.element_id
					WHERE user_permissions.element_id = $element_id AND user_id = $user_id

					UNION distinct

					SELECT
						0 as Links,
						0 as Docs,
						Notes.id as Notes,
						user_permissions.permit_read,
						user_permissions.permit_add,
						user_permissions.permit_edit,
						user_permissions.permit_delete,
						user_permissions.permit_copy,
						user_permissions.permit_move,

						0 as link_element_id,
						0 as link_title,
						0 as link_type,
						0 as link_references,
						0 as link_embed_code,
						0 as link_creater_id,
						0 as link_updated_user_id,
						0 as link_status,
						0 as link_created,
						0 as link_modified,
						0 as link_is_search,
						0 as link_create_activity,

						0 as doc_element_id,
						0 as doc_title,
						0 as doc_file_name,
						0 as doc_file_size,
						0 as doc_file_type,
						0 as doc_feedback_attachments_id,
						0 as doc_status,
						0 as doc_created,
						0 as doc_modified,
						0 as doc_is_search,
						0 as doc_create_activity,

						Notes.element_id as note_element_id,
						Notes.title as note_title,
						Notes.description as note_description,
						Notes.creater_id as note_creater_id,
						Notes.updated_user_id as note_updated_user_id,
						Notes.status as note_status,
						Notes.created as note_created,
						Notes.modified as note_modified,
						Notes.is_search as note_is_search,
						Notes.create_activity as note_create_activity

					FROM user_permissions
					INNER JOIN element_notes as Notes
						ON Notes.element_id = user_permissions.element_id
					WHERE user_permissions.element_id = $element_id AND user_id = $user_id ";


					$eleData = $this->UserPermission->query($query);


					$row = array();
					$row['Permissions']= array();
					foreach($eleData as $key => $listRows){
						foreach($listRows as $skey => $finalRows){

							$row['Permissions'][$skey]['permit_read'] = $finalRows['permit_read'];
							$row['Permissions'][$skey]['permit_add'] = $finalRows['permit_add'];
							$row['Permissions'][$skey]['permit_edit'] = $finalRows['permit_edit'];
							$row['Permissions'][$skey]['permit_delete'] = $finalRows['permit_delete'];
							$row['Permissions'][$skey]['permit_copy'] = $finalRows['permit_copy'];
							$row['Permissions'][$skey]['permit_move'] = $finalRows['permit_move'];
							$row['Permissions'][$skey]['is_editable'] = (isset($ele_iseditable['ElementPermission']['is_editable']) && !empty($ele_iseditable['ElementPermission']['is_editable'])) ? $ele_iseditable['ElementPermission']['is_editable'] : 0;

							if( !empty($finalRows['Links']) && $finalRows['Links'] !=0 ){

								//$finalRows['element_id'] = $finalRows['link_element_id'];
								$finalRows['title'] = $finalRows['link_title'];
								$finalRows['type'] = $finalRows['link_type'];
								$finalRows['references'] = $finalRows['link_references'];
								$finalRows['embed_code'] = $finalRows['link_embed_code'];
								$finalRows['creater_id'] = $finalRows['link_creater_id'];
								$finalRows['updated_user_id'] = $finalRows['link_updated_user_id'];
								$finalRows['status'] = $finalRows['link_status'];
								$finalRows['created'] = $finalRows['link_created'];
								$finalRows['modified'] = $finalRows['link_modified'];
								$finalRows['is_search'] = $finalRows['link_is_search'];
								$finalRows['create_activity'] = $finalRows['link_create_activity'];

								unset($finalRows['Docs']);
								unset($finalRows['Notes']);
								unset($finalRows['doc_element_id']);
								unset($finalRows['doc_title']);
								unset($finalRows['doc_file_name']);
								unset($finalRows['doc_file_size']);
								unset($finalRows['doc_file_type']);
								unset($finalRows['doc_feedback_attachments_id']);
								unset($finalRows['doc_status']);
								unset($finalRows['doc_created']);
								unset($finalRows['doc_modified']);
								unset($finalRows['doc_is_search']);
								unset($finalRows['doc_create_activity']);

								unset($finalRows['note_element_id']);
								unset($finalRows['note_title']);
								unset($finalRows['note_description']);
								unset($finalRows['note_creater_id']);
								unset($finalRows['note_updated_user_id']);
								unset($finalRows['note_status']);
								unset($finalRows['note_created']);
								unset($finalRows['note_modified']);
								unset($finalRows['note_is_search']);
								unset($finalRows['note_create_activity']);

								unset($finalRows['Links']);
								unset($finalRows['link_element_id']);

								$row['Links'][]= $finalRows;
							}
							if( !empty($finalRows['Docs']) && $finalRows['Docs'] !=0 ){

								//$finalRows['element_id'] = $finalRows['doc_element_id'];
								$finalRows['title'] = $finalRows['doc_title'];
								$finalRows['file_name'] = $finalRows['doc_file_name'];
								$finalRows['file_size'] = $finalRows['doc_file_size'];
								$finalRows['file_type'] = $finalRows['doc_file_type'];
								$finalRows['feedback_attachments_id'] = $finalRows['doc_feedback_attachments_id'];
								$finalRows['status'] = $finalRows['doc_status'];
								$finalRows['created'] = $finalRows['doc_created'];
								$finalRows['modified'] = $finalRows['doc_modified'];
								$finalRows['is_search'] = $finalRows['doc_is_search'];
								$finalRows['create_activity'] = $finalRows['doc_create_activity'];

								unset($finalRows['Links']);
								unset($finalRows['Notes']);

								unset($finalRows['note_element_id']);
								unset($finalRows['note_title']);
								unset($finalRows['note_description']);
								unset($finalRows['note_creater_id']);
								unset($finalRows['note_updated_user_id']);
								unset($finalRows['note_status']);
								unset($finalRows['note_created']);
								unset($finalRows['note_modified']);
								unset($finalRows['note_is_search']);
								unset($finalRows['note_create_activity']);

								unset($finalRows['Links']);
								unset($finalRows['link_element_id']);
								unset($finalRows['link_title']);
								unset($finalRows['link_type']);
								unset($finalRows['link_references']);
								unset($finalRows['link_embed_code']);
								unset($finalRows['link_creater_id']);
								unset($finalRows['link_updated_user_id']);
								unset($finalRows['link_status']);
								unset($finalRows['link_created']);
								unset($finalRows['link_modified']);
								unset($finalRows['link_is_search']);
								unset($finalRows['link_create_activity']);

								unset($finalRows['Docs']);
								unset($finalRows['doc_element_id']);

								$row['Documents'][]= $finalRows;
							}
							if( !empty($finalRows['Notes']) && $finalRows['Notes'] !=0 ){

								// $finalRows['element_id'] = $finalRows['note_element_id'];
								$finalRows['title'] = $finalRows['note_title'];
								$finalRows['description'] = $finalRows['note_description'];
								$finalRows['creater_id'] = $finalRows['note_creater_id'];
								$finalRows['updated_user_id'] = $finalRows['note_updated_user_id'];
								$finalRows['status'] = $finalRows['note_status'];
								$finalRows['created'] = $finalRows['note_created'];
								$finalRows['modified'] = $finalRows['note_modified'];
								$finalRows['is_search'] = $finalRows['note_is_search'];
								$finalRows['create_activity'] = $finalRows['note_create_activity'];


								unset($finalRows['Docs']);
								unset($finalRows['Links']);

								unset($finalRows['doc_element_id']);
								unset($finalRows['doc_title']);
								unset($finalRows['doc_file_name']);
								unset($finalRows['doc_file_size']);
								unset($finalRows['doc_file_type']);
								unset($finalRows['doc_feedback_attachments_id']);
								unset($finalRows['doc_status']);
								unset($finalRows['doc_created']);
								unset($finalRows['doc_modified']);
								unset($finalRows['doc_is_search']);
								unset($finalRows['doc_create_activity']);

								unset($finalRows['Links']);
								unset($finalRows['link_element_id']);
								unset($finalRows['link_title']);
								unset($finalRows['link_type']);
								unset($finalRows['link_references']);
								unset($finalRows['link_embed_code']);
								unset($finalRows['link_creater_id']);
								unset($finalRows['link_updated_user_id']);
								unset($finalRows['link_status']);
								unset($finalRows['link_created']);
								unset($finalRows['link_modified']);
								unset($finalRows['link_is_search']);
								unset($finalRows['link_create_activity']);

								unset($finalRows['Notes']);
								unset($finalRows['note_element_id']);

								$row['Notes'][]= $finalRows;
							}
						}
					}

					$target_operation = $this->restrict_copy_paste($workspace_id, $area_id, $element_id);

					if(!empty($target_operation['success'])) {

						$row['Element']['area_id'] = $area_id;
						$max_order = task_max_sort_order($area_id);
						$row['Element']['sort_order'] = $max_order;

						$row['Element']['updated_user_id'] = $eleDetail[0]['Element']['updated_user_id'];
						$row['Element']['title'] = $element_title;
						$row['Element']['description'] = $element_description;
						$row['Element']['comments'] = $comments;
						$row['Element']['date_constraints'] = $date_constraints;
						$row['Element']['start_date'] = $start_date;
						$row['Element']['end_date'] = $end_date;
						$row['Element']['sign_off'] = $eleDetail[0]['Element']['sign_off'];
						$row['Element']['color_code'] = (isset($eleDetail[0]['Element']['color_code']) && !empty($eleDetail[0]['Element']['color_code'])) ? $eleDetail[0]['Element']['color_code'] : 'panel-color-gray';
						$row['Element']['studio_status'] = $eleDetail[0]['Element']['studio_status'];
						$row['Element']['is_search'] = $eleDetail[0]['Element']['is_search'];

						if (isset($nPermit) && !empty($nPermit)) {
						}else{
							$row['Element']['created_by'] = $user_id;
						}
						$allData['Element'] = $row;
						$nPermit = $allData['Element']['Permissions'] = $allData['Element']['Permissions'];

						// NOW SAVE IT TO DATABASE
						if ($this->Element->saveAll($allData, array(
							'deep' => true,
						))) {

							$insert_id = $this->Element->getLastInsertId();
							if (!empty($this->request->data['ElementType']['type_id'])) {
								$this->request->data['ElementType']['element_id'] = $insert_id;
								$this->request->data['ElementType']['project_id'] = $project_id;
								$this->ElementType->save($this->request->data['ElementType']);
							}

							$task_data = [
								'project_id' => $project_id,
								'workspace_id' => $workspace_id,
								'element_id' => $insert_id,
								'element_type' => 'element_tasks',
								'user_id' => $this->user_id,
								'relation_id' => $insert_id,
								'updated_user_id' => $this->user_id,
								'message' => 'Task duplicated',
								'updated' => date("Y-m-d H:i:s"),
							];
							$this->loadModel('Activity');
							$this->Activity->id = null;
							$this->Activity->save($task_data);

							$task_data = [
								'project_id' => $project_id,
								'workspace_id' => $workspace_id,
								'element_id' => $insert_id,
								'element_type' => 'element_tasks',
								'user_id' => $this->user_id,
								'relation_id' => $insert_id,
								'updated_user_id' => $this->user_id,
								'message' => 'Task created',
								'updated' => date("Y-m-d H:i:s"),
							];
							$this->loadModel('Activity');
							$this->Activity->id = null;
							$this->Activity->save($task_data);

							$this->loadModel('ElementPermission');
							$arr['ElementPermission']['user_id'] = $user_id;
							$arr['ElementPermission']['element_id'] = $insert_id;
							$arr['ElementPermission']['project_id'] = $project_id;
							$arr['ElementPermission']['workspace_id'] = $workspace_id;
							$arr['ElementPermission']['permit_read'] = 1;
							$arr['ElementPermission']['permit_add'] = 1;
							$arr['ElementPermission']['permit_edit'] = 1;
							$arr['ElementPermission']['permit_delete'] = 1;
							$arr['ElementPermission']['permit_copy'] = 1;
							$arr['ElementPermission']['permit_move'] = 1;
							$arr['ElementPermission']['is_editable'] = 1;
							$this->ElementPermission->save($arr);

							// Now copy all the documents of the selected element to the target element
							$task_response = $this->cut_copy_paste_docs($element_id, $insert_id, 'copy');

							$success = true;
							$response['success'] = true;
							$response['msg'] = $insert_id;
						}
					}
					else{
						$success = false;
						$response['success'] = false;
						$response['error'] = $target_operation['message'];
					}

				// AFTER ALL DONE
				if ($success == true) {
					$response['success'] = true;
					$response['content'] =  null;
				} else {
					$response['msg'] = 'Anything wrong in process.';
				}

				}
			}

			echo json_encode($response);
			exit();
		}
	}


	public function update_task($element_id = null) {
		if ($this->request->isAjax()) {

			$project_id = element_project($element_id);
			$workspace_id = element_workspace($element_id);
			$area_id = element_area($element_id);

			$this->layout = 'ajax';
			$edata = [];
			if(isset($element_id) && !empty($element_id)){
				$query = "SELECT title, description, comments, color_code, date_constraints, start_date, end_date FROM elements WHERE id = $element_id";
				$edata = $this->Element->query($query);
				$edata = $edata[0]['elements'];
				// pr($edata, 1);
			}

			$query = "SELECT a.id, a.title FROM areas a WHERE a.workspace_id = $workspace_id";
			$data = $this->Element->query($query);
			$area_list = Set::combine($data, '{n}.a.id', '{n}.a.title');

			$elementType = $this->ElementType->find('first', array('conditions' => array('ElementType.project_id' => $project_id, 'ElementType.element_id' => $element_id)));

			if (!empty($elementType)) {
				$this->set('selElementType', $elementType['ElementType']['type_id']);
			} else {
				$elementtype = $this->ProjectElementType->find('first', array('conditions' => array('ProjectElementType.project_id' => $project_id, 'ProjectElementType.title' => 'General')));
				$peType = (isset($elementtype['ProjectElementType']['id']) && !empty($elementtype['ProjectElementType']['id'])) ? $elementtype['ProjectElementType']['id'] : null;
				$this->set('selElementType', $peType);

			}
			$query = "SELECT Project.start_date, Project.end_date FROM projects Project WHERE Project.id = $project_id";
			$pdata = $this->Element->query($query);

			$this->set('edata', $edata);
			$this->set('area_list', $area_list);
			$this->set('area_id', $area_id);
			$this->set('workspace_id', $workspace_id);
			$this->set('project_id', $project_id);
			$this->set('element_id', $element_id);
			$this->set('pdata', $pdata);

			$this->render('/Entities/partials/update_task');
		}
	}

	public 	function save_task_detail() {
		$this->layout = 'ajax';
		$this->autoRender = false;

		if ($this->request->isAjax()) {
			// Configure::write('debug', 0);
			$response = [
				'success' => false
			];
			if ($this->request->is('post') || $this->request->is('put')) {
				// pr($this->request->data, 1);
				$post = $this->request->data['Element'];

				$project_id = $post['project_id'];
				$workspace_id = $post['workspace_id'];
				$area_id = $post['area_id'];
				$element_id = $post['element_id'];
				$element_title = $post['title'];
				$element_description = $post['description'];
				$comments = (isset($post['comments']) && !empty($post['comments'])) ? $post['comments'] : '';
				$date_constraints = (!isset($post['date_constraints']) || empty($post['date_constraints'])) ? 0 : 1;
				$start_date = ((isset($post['date_constraints']) && !empty($post['date_constraints'])) && (isset($post['start_date']) && !empty($post['start_date']))) ? date('Y-m-d h:i:s', strtotime($post['start_date'])) : '';
				$end_date = ((isset($post['date_constraints']) && !empty($post['date_constraints'])) && (isset($post['end_date']) && !empty($post['end_date']))) ? date('Y-m-d h:i:s', strtotime($post['end_date'])) : '';
				$user_id = $this->Auth->user('id');

				$data = [
						'area_id' => $area_id,
						'title' => $element_title,
						'description' => $element_description,
						'comments' => $comments,
						'date_constraints' => $date_constraints,
						'start_date' => $start_date,
						'end_date' => $end_date,
						'updated_user_id' => $user_id,
					];
				$this->Element->id = $element_id;
				if ($this->Element->save($data)) {

					if(empty($date_constraints)){
						//DELETE ALL THE ENTRIES FROM PLAN EFFORT TABLE
						$this->loadModel('PlanEffort');
						$del = array('element_id'=>$element_id);
						$this->PlanEffort->deleteAll($del);
					}

					if (!empty($this->request->data['ElementType']['type_id'])) {
						$this->ElementType->deleteAll(['ElementType.element_id' => $element_id], false);
						$this->request->data['ElementType']['element_id'] = $element_id;
						$this->request->data['ElementType']['project_id'] = $project_id;
						$this->ElementType->save($this->request->data['ElementType']);
					}

					$response['success'] = true;

				}

			}

			echo json_encode($response);
			exit();
		}
	}

	public function update_task_viewed() {
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';

			$response = ['success' => false];
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				$task_data = [
					'project_id' => element_project($post['task_id']),
					'workspace_id' => element_workspace($post['task_id']),
					'element_id' => $post['task_id'],
					'element_type' => 'element_tasks',
					'user_id' => $this->user_id,
					'relation_id' => $post['task_id'],
					'updated_user_id' => $this->user_id,
					'message' => 'Task viewed',
					'updated' => date("Y-m-d H:i:s"),
				];
				$this->loadModel('Activity');
				$this->Activity->id = null;
				// $this->Activity->save($task_data);
				$response['success'] = true;
			}
			echo json_encode($response);
			exit;
		}
	}

	public function team(){
		$this->layout = 'ajax';
		$this->autoRender = false;
		$this->render('/Entities/team/team');
	}

	public function set_efforts($element_id = null, $user_id = null){

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			// GET PREVIOUS HISTORY OF THIS TASK IF ANY
			$hcount = $this->Element->query("SELECT count(*) AS history FROM element_efforts WHERE element_id = $element_id AND user_id = $user_id AND is_active = 1");
			$hcount = $hcount[0][0]['history'];
			// GET PREVIOUS ACTIVE HISTORY OF THIS TASK AND USER IF ANY
			$ef_data = $this->Element->query("SELECT * FROM element_efforts WHERE element_id = $element_id AND user_id = $user_id AND is_active = 1");

			$el_data = $this->Element->query("SELECT id, sign_off FROM elements WHERE id = $element_id");

			$this->set('element_id', $element_id);
			$this->set('user_id', $user_id);
			$this->set('hcount', $hcount);
			$this->set('ef_data', $ef_data);
			$this->set('el_signoff', $el_data[0]['elements']['sign_off']);

			$this->render('/Entities/team/set_efforts');
		}

	}

	public function effort_history(){

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				$task_id = (isset($post['task_id']) && !empty($post['task_id'])) ? $post['task_id'] : null;
				$user_id = (isset($post['user_id']) && !empty($post['user_id'])) ? $post['user_id'] : null;
				$el_data = $this->Element->query("SELECT id, sign_off FROM elements WHERE id = $task_id");

				$this->set('el_signoff', $el_data[0]['elements']['sign_off']);
				$this->set('task_id', $task_id);
				$this->set('user_id', $user_id);
			}

			$this->render('/Entities/team/effort_history');
		}

	}

	public function save_effort(){

		$response = ['success' => false, 'msg' => ''];

		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				$task_id = (isset($post['task_id']) && !empty($post['task_id'])) ? $post['task_id'] : null;
				$user_id = (isset($post['user_id']) && !empty($post['user_id'])) ? $post['user_id'] : null;
				$completed_hours = (isset($post['completed_hours']) && !empty($post['completed_hours'])) ? $post['completed_hours'] : 0;
				$remaining_hours = (isset($post['remaining_hours']) && !empty($post['remaining_hours'])) ? $post['remaining_hours'] : 0;
				$comment = (isset($post['comment']) && !empty($post['comment'])) ? $post['comment'] : '';

				$query = "SELECT count(*) AS chours FROM element_efforts WHERE element_id = $task_id AND user_id = $user_id AND completed_hours = $completed_hours AND remaining_hours = $remaining_hours AND is_active = 1";
				$chours = $this->Element->query($query);
				$chours = $chours[0][0]['chours'];

				// $query = "SELECT count(*) AS rhours FROM element_efforts WHERE element_id = $task_id AND user_id = $user_id AND remaining_hours = $remaining_hours";
				// $rhours = $this->Element->query($query);
				// $rhours = $rhours[0][0]['rhours'];

				$error = false;
				if( (isset($chours) && !empty($chours)) ){
					$response['msg'] = 'At least one of Completed or Remaining hours must be set to a new value';
					$error = true;
				}
				$query = "SELECT * FROM element_efforts WHERE element_id = $task_id AND user_id = $user_id and is_active =1";
				$change_hours = 0;
				$exists = $this->Element->query($query);
				if( isset($exists) && !empty($exists) ){
					$exist = $exists[0]['element_efforts']['id'];
					$exist_completed_hours = $exists[0]['element_efforts']['completed_hours'];
					$exist_remaining_hours = $exists[0]['element_efforts']['remaining_hours'];

					if( isset($exist)  ){
						$change_hours = ($completed_hours + $remaining_hours ) - ($exist_completed_hours + $exist_remaining_hours );
					}

				}

				$project_id = element_project($task_id);
				$workspace_id = element_workspace($task_id);

				$data = [
						'project_id' => $project_id,
						'workspace_id' => $workspace_id,
						'element_id' => $task_id,
						'user_id' => $user_id,
						'comment' => $comment,
						'completed_hours' => $completed_hours,
						'remaining_hours' => $remaining_hours,
						'is_active' => 1,
						'change_hours' => $change_hours
					];

				if(!$error){
					$query = "UPDATE element_efforts SET is_active = '0' WHERE element_id = '$task_id' AND user_id = '$user_id'";
					$this->Element->query($query);

					$this->loadModel('ElementEffort');
					if($this->ElementEffort->save($data)) {
						$response['success'] = true;
						$response['msg'] = '';

						$task_data = [
							'project_id' => $project_id,
							'workspace_id' => $workspace_id,
							'element_id' => $task_id,
							'element_type' => 'element_efforts',
							'user_id' => $this->user_id,
							'updated_user_id' => $this->user_id,
							'message' => 'Task effort set',
							'updated' => date("Y-m-d H:i:s"),
						];
						$this->loadModel('Activity');
						$this->Activity->id = null;
						$this->Activity->save($task_data);

					}
				}
			}

		}
		echo json_encode($response);
		exit();

	}

	public function remove_effort(){

		$response = ['success' => false, 'msg' => ''];

		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				$id = (isset($post['id']) && !empty($post['id'])) ? $post['id'] : null;

				if(!empty($id)) {
					// GET DETAIL OF ROW BEING DELETED
					$query = "SELECT * FROM element_efforts WHERE id = $id";
					$detail = $this->Element->query($query);
					$detail = $detail[0]['element_efforts'];
					$task_id = $detail['element_id'];
					$user_id = $detail['user_id'];
					$is_active = $detail['is_active'];

					// DELETE THE SELECTED ROW
					$query = "DELETE FROM element_efforts WHERE id = $id";
					$detail = $this->Element->query($query);
					// IF DELETED ROW IS ACTIVE ONE
					if(!empty($is_active)){
						// SELECT NEXT ROW FOR THIS USER AND TASK
						$query = "SELECT * FROM element_efforts WHERE element_id = $task_id AND user_id = $user_id ORDER BY id DESC LIMIT 1";
						$next_row = $this->Element->query($query);
						// IF THERE IS ANY OTHER ROW
						if(isset($next_row) && !empty($next_row)){
							$next_row = $next_row[0]['element_efforts'];
							$next_row_id = $next_row['id'];
							// DEACTIVE ALL ROWS
							$query = "UPDATE element_efforts SET is_active = '0' WHERE element_id = '$task_id' AND user_id = '$user_id'";
							$this->Element->query($query);
							// UPDATE NEXT ROW TO ACTIVE
							$query = "UPDATE element_efforts SET is_active = '1' WHERE id = '$next_row_id'";
							$this->Element->query($query);
						}
					}
					$response['success'] = true;
				}
			}

		}
		echo json_encode($response);
		exit();

	}

	protected function find_data($user_id = null, $task_id = null, $page = 0, $sorting = [], $filters = []) {
		$limit_query = ' LIMIT '.$this->team_offset;
		if(isset($page) && !empty($page)){
			$limit_query = " LIMIT $page, " . $this->team_offset;
		}

		$order = $col = $by = '';
		if( isset($sorting) && !empty($sorting) ){

			if( isset($sorting['coloumn']) && !empty($sorting['coloumn']) && isset($sorting['order']) && !empty($sorting['order']) ){
				$order = "ORDER BY ".$sorting['coloumn']." ".$sorting['order'];
				$col = $sorting['coloumn'];
				$by = $sorting['order'];
			}

		}

		return $this->objView->loadHelper('Permission')->team_task_efforts_listing($user_id, $task_id, $page, $this->team_offset, $order, $col, $by );
	}

	public function filter_team() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				// pr($post, 1);
				$user_id = (isset($post['user_id']) && !empty($post['user_id'])) ? $post['user_id'] : null;
				$task_id = (isset($post['task_id']) && !empty($post['task_id'])) ? $post['task_id'] : 0;
				$project_id = (isset($post['project_id']) && !empty($post['project_id'])) ? $post['project_id'] : 0;
				$page = (isset($post['page']) && !empty($post['page'])) ? $post['page'] : 0;
				$order = (isset($post['order']) && !empty($post['order'])) ? $post['order'] : '';
				$coloumn = (isset($post['coloumn']) && !empty($post['coloumn'])) ? $post['coloumn'] : '';

				$team_data = $this->find_data($user_id, $task_id, $page, ['coloumn' => $coloumn, 'order' => $order] );
				$this->set('project_id', $project_id);
				$this->set('element_id', $task_id);
				$this->set('team_data', $team_data);

				if( isset($user_id) && !empty($user_id) ){
					$this->render('/Entities/team/team_user');
				}
				else{
					$this->render('/Entities/team/team');
				}
			}
		}

	}


	public function task_progress_bar() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				// pr($post, 1);
				$task_id = (isset($post['task_id']) && !empty($post['task_id'])) ? $post['task_id'] : 0;
				$project_id = element_project($task_id);
				$workspace_id = element_workspace($task_id);
				$task_detail = $this->Element->query("SELECT id, title, date_constraints, sign_off, sign_off_date, start_date, end_date FROM elements WHERE id = $task_id");
				$task_detail = $task_detail[0]['elements'];
				// pr($task_detail, 1);

				$this->set('project_id', $project_id);
				$this->set('workspace_id', $workspace_id);
				$this->set('element_id', $task_id);
				$this->set('task_detail', $task_detail);

			}
			$this->render('/Entities/element_files/task_progress_bar');
		}

	}


}