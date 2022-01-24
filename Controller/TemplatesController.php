<?php

/**
 * Tasks controller.
 *
 * This file will render views from views/Tasks/
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Task
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
App::uses('Controller/Component', 'Auth', 'Session', 'RequestHandler', 'Router');
App::uses('CakeEmail', 'Network/Email');
App::uses('HttpSocket', 'Network/Http');
/**
 * Tasks Controller
 *
 * @property User $User
 * @property PaginatorComponent $Paginator
 */
class TemplatesController extends AppController {

	public $name = 'Templates';
	public $uses = array('UserProject', 'Project', 'Workspace', 'ProjectWorkspace', 'Area', 'Element', 'Template', 'TemplateDetail', 'ProjectGroup', 'ProjectGroupUser', 'TemplateCategory', 'TemplateRelation', 'AreaRelation', 'TemplateLike', 'ElementRelation', 'ElementRelationDocument', 'TemplateReview', 'ThirdParty', 'ElementDocument', 'TemplateMove');
	public $pagination = null;
	public $user_id = null;

	/**
	 * check login for admin and frontend user
	 * allow and deny user
	 */
	public $components = array('RequestHandler', 'Group', 'Common', 'Paginator');

	/**
	 * Helpers
	 *
	 * @var array
	 */
	public $helpers = array('Html', 'Form', 'Session', 'Time', 'Text', 'Common', 'Js' => array('Prototype'), 'Template');

	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow('get_workspaces');
		$this->pagination['limit'] = 16;
		$this->user_id = $this->Auth->user('id');

		$this->pagination['show_summary'] = false;
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
	}

	/**
	 * Displays templates for the workspace
	 *
	 * @param $project_id
	 * @return void
	 */
	public function index($project = null, $workspace = null, $area = null) {

		$this->redirect(Controller::referer());

		$this->setJsVar('project_id', $project);
		$this->setJsVar('workspace_id', $workspace);

		// pr($this->_jsVars);
		if (!$this->Auth->loggedIn()) {
			return $this->redirect(array('controller' => 'pages', 'action' => 'display', 'home', 'admin' => false));
		}

		if (!$project) {
			return $this->redirect(array('controller' => 'pages', 'action' => 'display', 'home', 'admin' => false));
		}

		$this->layout = 'inner';
		$data['title_for_layout'] = __('Knowledge Library', true);
		$data['page_heading'] = __('Knowledge Library', true);

		// GET PROJECT DETAIL OF THE LOGGED-IN USER
		$user_id = $this->Auth->user('id');
		$this->Project->recursive = 4;

		$projects = $this->Project->find('first', array('conditions' => array('Project.user_id' => $user_id), 'order' => array('Project.created DESC')));

		$this->Workspace->recursive = -1;
		$data['workspace'] = $this->Workspace->find('first', array(
			'fields' => array('id', 'title'),
			'conditions' => array('Workspace.project_id' => $project),
		));

		/* * *************************** */
		$this->Template->recursive = 2;
		$data['templateData'] = $this->Template->find('first', [
			'conditions' => array('Template.id' => 3),
		]);

		$templateParents = $this->TemplateDetail->find('all', [
			'fields' => array('TemplateDetail.parent_id'),
			'conditions' => array('TemplateDetail.template_id' => 3),
			'group' => array('TemplateDetail.parent_id'),
		]);

		foreach ($templateParents as $k => $v) {
			$data['templateParents'][] = $v['TemplateDetail']['parent_id'];
		}
		// pr($data['templateParents']);
		// pr($data['templateData']);
		// die;
		/*         * *************************** */
		$area_where['Area.status'] = 1;
		if (isset($workspace) && !empty($workspace)) {
			$area_where['Area.workspace_id'] = $workspace;
		}
		$data['area'] = $this->Area->find('list', array(
			'fields' => array('id', 'title'),
			'conditions' => $area_where,
		)
		);

		$data['projects'] = $projects;
		$data['templates'] = $projects;
		$this->set('data', $data);
		$this->set('project_id', $project);
	}

	/**
	 * Displays templates for the workspace
	 *
	 * @param $project_id
	 * @return void
	 */
	public function create_workspace_tab($project_id = null) {

		$this->layout = 'inner';
		$data['title_for_layout'] = __('Create Workspace', true);

		if (!$this->Auth->loggedIn()) {
			return $this->redirect(array('controller' => 'pages', 'action' => 'display', 'home', 'admin' => false));
		}

		if (empty($project_id)) {
			$this->redirect(Controller::referer());
		}

		$user_id = $this->Auth->user('id');

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
			// pr($projects_group_shared_user, 1);
			// echo $project_id." sdsd ".$projects_group_shared_user['ProjectGroupUser']['project_group_id']; die;
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
		$conditionsN['ProjectPermission.user_project_id'] = project_upid($project_id);
		$this->loadModel('ProjectPermission');
		$projects_shared = $this->ProjectPermission->find('first', array(
			'conditions' => $conditionsN,
			'fields' => array('ProjectPermission.user_project_id'),
			'order' => 'ProjectPermission.created DESC',
			'recursive' => -1,
		));
		/* -----------sharing code----------- */

		$pshare = $project_detail = null;
		if (isset($projects_shared) && !empty($projects_shared)) {
			$pshare = Set::extract($projects_shared, 'ProjectPermission.user_project_id');
			$project_detail = $this->UserProject->find('first', array('recursive' => 1, 'conditions' => ['UserProject.id' => $pshare]));
		} else {
			$project_detail = $this->UserProject->find('first', array('recursive' => 1, 'conditions' => ['UserProject.project_id' => $project_id]));
		}
		$data['page_heading'] = __('Create Workspace', true);

		$this->set('project_detail', $project_detail);

		$this->set('project_id', $project_id);

		// CREATE WORKSPACE BY AJAX REQUEST
		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$response = [
				'success' => false,
				'status' => 400,
				'content' => null,
				'date_error' => null,
			];

			if ($this->request->is('post') || $this->request->is('put')) {
				$this->request->data['Workspace']['updated_user_id'] = $this->Session->read("Auth.User.id");
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
						if ($this->ProjectWorkspace->saveAssociated($this->request->data)) {

							$this->loadModel('WorkspacePermission');
							$insertIdN = $this->ProjectWorkspace->getLastInsertId();

							$arr['WorkspacePermission']['user_id'] = $user_id;
							$arr['WorkspacePermission']['user_project_id'] = project_upid($project_id);
							$arr['WorkspacePermission']['project_workspace_id'] = $insertIdN;
							$arr['WorkspacePermission']['permit_read'] = 1;
							$arr['WorkspacePermission']['permit_add'] = 1;
							$arr['WorkspacePermission']['permit_edit'] = 1;
							$arr['WorkspacePermission']['permit_delete'] = 1;
							$arr['WorkspacePermission']['permit_copy'] = 1;
							$arr['WorkspacePermission']['permit_move'] = 1;
							$arr['WorkspacePermission']['is_editable'] = 1;

							$this->WorkspacePermission->save($arr);

							// $this->Common->projectModified($project_id, $user_id);

							$url = false;
							$insertId = 0;
							$response['success'] = true;
							if (isset($this->request->data['Workspace']['id']) && !empty($this->request->data['Workspace']['id'])) {
								$url = true;
								$insertId = $this->request->data['Workspace']['id'];
							} else {
								$this->Workspace->id = $insertId = $this->Workspace->getLastInsertId();

								// Create area table entries with the selected template_detail data
								if (isset($insertId) && !empty($insertId)) {
									$workspace = $this->Workspace->find('first', [
										'conditions' => [
											'Workspace.id' => $insertId,
										],
										'recursive' => 2,
									]);
									if (!is_null($workspace)) {
										$templateDetail = Set::extract($workspace, '/Template/TemplateDetail/id');
										$areas = null;

										foreach ($templateDetail as $id => $detail) {

											$areas[$id]['Area'] = ['title' => "Area - " . ($id + 1), 'description' => "Area - " . ($id + 1), 'tooltip_text' => "Area - " . ($id + 1), 'is_standby' => '0', 'status' => '1', 'template_detail_id' => $detail, 'workspace_id' => $insertId];
										}

										if (!empty($areas)) {
											$this->Area->saveAll($areas);
										}
									}
								}

								$this->request->data['ProjectWorkspace']['workspace_id'] = $insertId;
								$this->request->data['ProjectWorkspace']['workspace_id'] = $insertId;
								$this->Workspace->ProjectWorkspace->save($this->request->data);

							}

							$response['content'] = ['id' => $insertId, 'url' => $url];
						}
					} else {
						if (!empty($check) && $check != null) {
							$response['date_error'] = $check;
							$response['content'] = $this->validateErrors($this->Workspace);
						}
					}

				} else {

					$response['content'] = $this->validateErrors($this->Workspace);
				}
			}

			echo json_encode($response);
			exit();
		}
		// END AJAX REQUEST
		// GET PRE-DETINED TEMPLATES FROM DATABASE
		$this->Template->recursive = -1;
		$data['templates'] = $this->Template->find('all', [
			'fields' => array('Template.id', 'Template.title', 'Template.description', 'Template.layout_preview'),
			'conditions' => array('Template.status' => 1),
		]);

		$paginator = array(
			'fields' => array(
				'Template.id', 'Template.title', 'Template.description', 'Template.layout_preview',
			),
			'conditions' => array(
				'Template.status' => 1,
			),
			'limit' => $this->pagination['limit'],
			"order" => "Template.id ASC",
		);

		$this->paginate = $paginator;

		$this->set('templates', $this->paginate('Template'));
		$this->pagination['show_summary'] = true;
		$this->set('JeeraPaging', $this->pagination);

		$this->set('data', $data);

		$project_title = _strip_tags($project_detail['Project']['title']);

		$cat_crumb = get_category_list($project_id);

		$crumb = [
			'Summary' => [
				'data' => [
					'url' => '/projects/index/' . $project_id,
					'class' => 'tipText',
					'title' => $project_title,
					'data-original-title' => $project_title,
				],
			],
			'last' => ['Knowledge Library'],
		];

		if (isset($cat_crumb) && !empty($cat_crumb)) {
			$crumb = array_merge($cat_crumb, $crumb);
		}
		$this->set('crumb', $crumb);
		$this->set('project_title', $project_title);
	}

	/**
	 * Displays templates for the workspace
	 *
	 * @param $project_id
	 * @return void
	 */
	public function create_workspace($project_id = null, $template_category_id = null) {

		$this->layout = 'inner';
		$title_for_layout = __('Knowledge Library', true);

		if( $_SERVER['HTTP_HOST'] != LOCALIP )  {

			if( $this->Session->read('Auth.User.role_id') == 3 ){
				$this->redirect(['controller' => 'organisations','action' => 'dashboard']);
			}
			/* if( $this->Session->read('Auth.User.role_id') == 1 ){
				$this->redirect(['controller' => 'dashboard','action' => 'index']);
			} */
		}


		if (!$this->Auth->loggedIn()) {
			return $this->redirect(array('controller' => 'pages', 'action' => 'display', 'home', 'admin' => false));
		}

		// if ( !isset($project_id) || empty($project_id))
		// $this->redirect(Controller::referer());

		$user_id = $this->Auth->user('id');

		// if user have not permiss of this project the he will redirect to project list page == add dated 5th Feb 2019  ==

		/*=============== Group Permission 5th Feb 2019 ================================== */
		if (isset($project_id) && !empty($project_id)) {

			$view = new View();
			$viewModal = $view->loadHelper('ViewModel');
			$PermitType = $viewModal->projectPermitType($project_id, $this->Session->read('Auth.User.id'));

			if (!isset($PermitType) || empty($PermitType)) {

				return $this->redirect(array('controller' => 'projects', 'action' => 'lists', 'admin' => false));

			}
		}
		// ===============================================================================

		/////////////////////////////////////////////////////
		if (isset($project_id) && !empty($project_id)) {
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
				// pr($projects_group_shared_user, 1);
				// echo $project_id." sdsd ".$projects_group_shared_user['ProjectGroupUser']['project_group_id']; die;
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
			$conditionsN['ProjectPermission.user_project_id'] = project_upid($project_id);
			$this->loadModel('ProjectPermission');
			$projects_shared = $this->ProjectPermission->find('first', array(
				'conditions' => $conditionsN,
				'fields' => array('ProjectPermission.user_project_id'),
				'order' => 'ProjectPermission.created DESC',
				'recursive' => -1,
			));
			/* -----------sharing code----------- */

			$pshare = $project_detail = null;
			if (isset($projects_shared) && !empty($projects_shared)) {
				$pshare = Set::extract($projects_shared, 'ProjectPermission.user_project_id');
				$project_detail = $this->UserProject->find('first', array('recursive' => 1, 'conditions' => ['UserProject.id' => $pshare]));
			} else {
				$project_detail = $this->UserProject->find('first', array('recursive' => 1, 'conditions' => ['UserProject.project_id' => $project_id]));
			}

			$this->set('project_detail', $project_detail);

		}
		$this->set('project_id', $project_id);
		$this->setJsVar('project_id', $project_id);
		$this->setJsVar('template_category_id', $template_category_id);
		/////////////////////////////////////////////////////////////

		// CREATE WORKSPACE BY AJAX REQUEST
		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$response = [
				'success' => false,
				'status' => 400,
				'content' => null,
				'date_error' => null,
			];

			if ($this->request->is('post') || $this->request->is('put')) {

				$this->request->data['Workspace']['updated_user_id'] = $this->Session->read("Auth.User.id");

				$this->request->data['Workspace']['created_by'] = $this->Session->read("Auth.User.id");

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

						$this->request->data['Workspace']['title'] = $this->request->data['Workspace']['title'] ;
						//$this->request->data['Workspace']['description'] = substr($this->request->data['Workspace']['description'], 0, 250);



						if ($this->ProjectWorkspace->saveAssociated($this->request->data)) {

							$this->loadModel('WorkspacePermission');
							$insertIdN = $this->ProjectWorkspace->getLastInsertId();
							$workspace_id = $this->Workspace->getLastInsertId();


							$current_user_id = $this->Session->read("Auth.User.id");
							$this->loadModel('UserPermission');
							$all_users = $this->UserPermission->find('all',array('conditions'=>array('UserPermission.project_id'=>$project_id,'UserPermission.user_id !='=>$current_user_id,'UserPermission.role'=>array('Creator','Group Owner','Owner'),'UserPermission.workspace_id IS NULL'),'fields'=>array( 'user_id')));

							if(isset($all_users) && !empty($all_users)) {
								$project_all_users = Set::extract($all_users, '/UserPermission/user_id');

								/************** socket messages **************/
								if (SOCKET_MESSAGES) {

									$prj_users = $project_all_users;

									$s_open_users = null;
									if (isset($prj_users) && !empty($prj_users)) {
										foreach ($prj_users as $key => $value) {
											if (web_notify_setting($value, 'workspace', 'workspace_sharing')) {
												$s_open_users[] = $value;
											}
										}
									}
									$userDetail = get_user_data($current_user_id);
									$content = [
										'socket' => [
											'notification' => [
												'type' => 'workspace_sharing',
												'created_id' => $this->user_id,
												'project_id' => $project_id,
												'refer_id' => $workspace_id,
												'creator_name' => $userDetail['UserDetail']['full_name'],
												'subject' => 'Workspace sharing',
												'heading' => 'Permission: Owner',
												'sub_heading' => 'Project: ' . strip_tags(getFieldDetail('Project', $project_id, 'title')).'<br />Workspace: ' . strip_tags(getFieldDetail('Workspace', $workspace_id, 'title')),
												'date_time' => timezoneTimeConvertor($this->user_id, date('Y-m-d g:iA'), $format = 'd M Y g:iA') . user_country($this->user_id),
											],
										],
									];
									if (is_array($s_open_users)) {
										$content['socket']['received_users'] = array_values($s_open_users);
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
								foreach ($project_all_users as $key => $value) {
									$this->Common->workspace_share_email($value, $project_id, $workspace_id);
								}

							}
							/************** socket messages **************/

							$this->Common->create_workspace_activity($project_id, $workspace_id);

							$arr['WorkspacePermission']['user_id'] = $user_id;
							$arr['WorkspacePermission']['user_project_id'] = project_upid($project_id);
							$arr['WorkspacePermission']['project_workspace_id'] = $insertIdN;
							$arr['WorkspacePermission']['permit_read'] = 1;
							$arr['WorkspacePermission']['permit_add'] = 1;
							$arr['WorkspacePermission']['permit_edit'] = 1;
							$arr['WorkspacePermission']['permit_delete'] = 1;
							$arr['WorkspacePermission']['permit_copy'] = 1;
							$arr['WorkspacePermission']['permit_move'] = 1;
							$arr['WorkspacePermission']['is_editable'] = 1;

							$this->WorkspacePermission->save($arr);

							// $this->Common->projectModified($project_id, $user_id);

							$url = false;
							$insertId = 0;
							$response['success'] = true;
							if (isset($this->request->data['Workspace']['id']) && !empty($this->request->data['Workspace']['id'])) {
								$url = true;
								$insertId = $this->request->data['Workspace']['id'];
							} else {
								$this->Workspace->id = $insertId = $this->Workspace->getLastInsertId();

								// Create area table entries with the selected template_detail data
								if (isset($insertId) && !empty($insertId)) {
									$workspace = $this->Workspace->find('first', [
										'conditions' => [
											'Workspace.id' => $insertId,
										],
										'recursive' => 2,
									]);
									if (!is_null($workspace)) {
										$templateDetail = Set::extract($workspace, '/Template/TemplateDetail/id');
										$areas = null;

										foreach ($templateDetail as $id => $detail) {

											$areas[$id]['Area'] = ['title' => "Area - " . ($id + 1), 'description' => "Area - " . ($id + 1), 'tooltip_text' => "Area - " . ($id + 1), 'is_standby' => '0', 'status' => '1', 'template_detail_id' => $detail, 'workspace_id' => $insertId];
										}

										if (!empty($areas)) {
											$this->Area->saveAll($areas);
										}
									}
								}

								$this->request->data['ProjectWorkspace']['workspace_id'] = $insertId;
								$this->request->data['ProjectWorkspace']['workspace_id'] = $insertId;
								$this->Workspace->ProjectWorkspace->save($this->request->data);
							}

							$response['content'] = ['id' => $insertId, 'url' => $url];
						}
					} else {
						if (!empty($check) && $check != null) {
							$response['date_error'] = $check;
							$response['content'] = $this->validateErrors($this->Workspace);
						}
					}

				} else {
					// pr($this->request->data);
					if(!isset($this->request->data['Workspace']['title']) || empty($this->request->data['Workspace']['title'])){
						$this->Element->validationErrors['Workspace']['title'] = "Workspace title is required";
					}
					$response['content'] = $this->validateErrors($this->Workspace);
					// pr($this->validationErrors, 1);
				}
			}

			echo json_encode($response);
			exit();
		}
		// END AJAX REQUEST

		$crumb = null;

		////////////////////////////////////////////////////////////////////
		if (isset($project_id) && !empty($project_id)) {
			// GET PRE-DETINED TEMPLATES FROM DATABASE
			$this->Template->recursive = -1;
			$data['templates'] = $this->Template->find('all', [
				'fields' => array('Template.id', 'Template.title', 'Template.description', 'Template.layout_preview'),
				'conditions' => array('Template.status' => 1), "order" => "Template.title ASC",
			]);

			// 'TemplateRelation.rating between ? and ?' => array( ($starRating-1), $starRating)

			$paginator = array(
				'fields' => array(
					'Template.id', 'Template.title', 'Template.description', 'Template.layout_preview',
				),
				'conditions' => array(
					'Template.status' => 1,
				),
				'limit' => $this->pagination['limit'],
				"order" => "Template.id ASC",
			);

			$this->paginate = $paginator;

			$this->set('templates', $this->paginate('Template'));
			$this->pagination['show_summary'] = true;
			$this->set('JeeraPaging', $this->pagination);

			$project_title = htmlentities($project_detail['Project']['title'],ENT_QUOTES);

			// $cat_crumb = get_category_list($project_id);


			if ((isset($project_id) && !empty($project_id) && $project_id > 0  )  && ( !isset($this->request->params['pass'][1]))) {

				$updatedcumb = 'Add Workspace';

			}else{
			  $updatedcumb = 'Knowledge Library';
			}

			$crumb = [
				'Summary' => [
					'data' => [
						'url' => '/projects/index/' . $project_id,
						'class' => 'tipText',
						'title' => $project_title,
						'data-original-title' => $project_title,
					],
				],
				'last' => [$updatedcumb],
			];

			/*if (isset($cat_crumb) && !empty($cat_crumb)) {
				$crumb = array_merge($cat_crumb, $crumb);
			}*/

			$this->set('project_title', $project_title);
		}

		/////////////////////////////////////////////////////////////////////////

		$user_templates = $template_categories = null;

		if (isset($template_category_id) && !empty($template_category_id)) {

			$this->set('template_category_id', $template_category_id);
			$this->setJsVar('template_category_id', $template_category_id);

			/* paging for mindmap */
			$paginator = null;
			$this->pagination['limit'] = 1;

			$trPageCount = $this->TemplateRelation->find('count', [
				'conditions' => array(
					'TemplateRelation.status' => 1,
					'TemplateRelation.template_category_id' => $template_category_id,

				),
			]);

			/* GET USER CREATED TEMPLATES */
			$user_templates = $this->TemplateRelation->find('all', [
				'conditions' => [
					'TemplateRelation.status' => 1,
					'TemplateRelation.type' => 1,
					'TemplateRelation.template_category_id' => $template_category_id,

				],
				"order" => ["TemplateRelation.title ASC"],
			]);

			$paginator['TemplateRelation'] = array(
				'limit' => 1,
				'conditions' => array(
					'TemplateRelation.status' => 1,
					'TemplateRelation.template_category_id' => $template_category_id,

				),
			);

			$this->pagination['count'] = $trPageCount;
			$this->set('trPageCount', $trPageCount);
			/* End paging for mindmap */
			$this->pagination['show_summary'] = true;
			$this->paginate = $paginator;
			// $this->set('user_templates', $this->paginate('TemplateRelation'));
			$jeeraPaging['options'] = array(
				'url' => array_merge(
					array(
						'controller' => $this->request->params['controller'],
						'action' => 'get_more_user_template',
					),
					// $this->request->params['pass'],
					$this->request->params['named']
				),
			);
			$this->set('JeeraPaging', $jeeraPaging);

			$cat_data = getByDbId('TemplateCategory', $template_category_id, ['title']);
			// pr($cat_data, 1);

			if ((isset($project_id) && !empty($project_id) && $project_id > 0  )  && ( isset($this->request->params['pass'][1]) && !empty($this->request->params['pass'][1]))) {
			$crub_texts = $project_title;
			} else {
			$crub_texts = 'Knowledge Library';
			}

			$crumb = [
				'Templates' => [
					'data' => [
						'url' => '/templates/create_workspace/' . $project_id,
						'class' => 'tipText',
						'title' =>  $crub_texts,
						'data-original-title' => $crub_texts,
					],
				],
				'last' => [$cat_data['TemplateCategory']['title']],
			];
		} else {
			/* GET TEMPLATES CATEGORIES */
			$template_categories = $this->TemplateCategory->find('all', [
				'conditions' => [
					'TemplateCategory.status' => 1,
				],
				'order' => '(CASE WHEN title="Other" THEN 1 ELSE 0 END), title ASC',
				'recursive' => -1,
			]);

			if (!isset($project_id) || empty($project_id)) {
				$crumb = [
					'last' => ['Knowledge Library'],
				];
			}
		}

		$thirdpartyUser = $this->ThirdParty->find('all', array('conditions' => array('ThirdParty.status' => 1), 'order' => 'ThirdParty.username ASC'));

		//pr($template_categories); die;
		//$data['page_heading'] = __('Add Template Workspace', true);

		if( isset($this->request->params['pass'][1]) && $this->request->params['pass'][1] > 0){
			$title_for_layout = __('Add Knowledge Template', true);
			$data['page_heading'] = __('Add Knowledge Template', true);

		}else if ((isset($project_id) && !empty($project_id) && $project_id > 0  )  && ( !isset($this->request->params['pass'][1]))) {
			$title_for_layout = __('Add Workspace', true);
			$data['page_heading'] = __('Add Workspace', true);
		} else {
			$data['page_heading'] = __('Knowledge Library', true);
		}
		$this->set('title_for_layout', $title_for_layout);
		$this->set('data', $data);
		$this->set('thirdpartyuser', $thirdpartyUser);
		$this->set('crumb', $crumb);
		$this->set('user_templates', $user_templates);
		$this->set('template_categories', $template_categories);

	}

	public function admin_create_workspace($project = null) {

		$this->layout = 'admin_inner';
		$data['title_for_layout'] = __('Create Workspace', true);

		if (!$this->Auth->loggedIn()) {
			return $this->redirect(array('controller' => 'pages', 'action' => 'display', 'home', 'admin' => false));
		}

		if (empty($project)) {
			$this->redirect(Controller::referer());
		}

		$user_id = $this->Auth->user('id');

		$project_detail = $this->UserProject->find('first', array('recursive' => 1, 'conditions' => ['UserProject.project_id' => $project, 'UserProject.user_id' => $user_id]));

		$data['page_heading'] = __('Create Workspace', true);

		$this->set('project_detail', $project_detail);
		$this->set('project_id', $project);

		// CREATE WORKSPACE BY AJAX REQUEST
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			$response = [
				'success' => false,
				'status' => 400,
				'content' => null,
			];

			if ($this->request->is('post') || $this->request->is('put')) {

				$this->Workspace->set($this->request->data);
				if ($this->Workspace->validates()) {
					$this->loadModel('ProjectWorkspace');
					$this->Workspace->create();

					if ($this->ProjectWorkspace->saveAssociated($this->request->data)) {

						$url = false;
						$insertId = 0;
						$response['success'] = true;
						if (isset($this->request->data['Workspace']['id']) && !empty($this->request->data['Workspace']['id'])) {

							$url = true;
							$insertId = $this->request->data['Workspace']['id'];
						} else {
							$this->Workspace->id = $insertId = $this->Workspace->getLastInsertId();

							$this->request->data['ProjectWorkspace']['workspace_id'] = $insertId;
							$this->Workspace->ProjectWorkspace->save($this->request->data);
						}

						$response['content'] = ['id' => $insertId, 'url' => $url];
					}
				} else {
					$response['content'] = $this->validateErrors($this->Workspace);
				}
			}

			echo json_encode($response);
			exit();
		}
		// END AJAX REQUEST
		// GET PRE-DETINED TEMPLATES FROM DATABASE
		$this->Template->recursive = -1;
		$data['templates'] = $this->Template->find('all', [
			'fields' => array('Template.id', 'Template.title', 'Template.description', 'Template.layout_preview'),
			'conditions' => array('Template.status' => 1),
		]);

		$this->set('data', $data);
	}

	/**
	 * Open Popup Modal Boxes method
	 *
	 * @return void
	 */
	public function popups($form, $project_id, $template_id = 0, $workspace_id = null) {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$response = array('project_id' => $project_id, 'template_id' => $template_id);

			if (isset($workspace_id) && !empty($workspace_id)) {
				$response['workspace_id'] = $workspace_id;

				$this->request->data = $this->Workspace->read(null, $workspace_id);

				// pr($this->request->data, 1);
			}

			$this->set('response', $response);
			$this->set('project_id', $project_id);

			$this->set('form_name', $form);
			$this->render('/Templates/partials/popup_forms');
		}
	}

	public function get_more() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$model = 'Template';

			if ($this->request->is('post') || $this->request->is('put')) {
				// pr($this->request->data, 1);
				$paginator = array(
					'conditions' => array(
						$model . '.status' => 1,
					),
					'limit' => $this->pagination['limit'],
					"order" => $model . ".id ASC",
				);

				$this->paginate = $paginator;
				$this->set('templates', $this->paginate($model));
				$this->set('project_id', $this->request->data);

				$this->pagination['show_summary'] = true;
				$this->pagination['model'] = $model;
				$this->set('JeeraPaging', $this->pagination);

				$this->render('/Templates/partials/list_more');
			}
		}
	}

	public function get_more_user_template() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$model = 'TemplateRelation';

			if ($this->request->is('post') || $this->request->is('put')) {
				// pr($this->request->data, 1);
				$paginator = array(
					'conditions' => array(
						$model . '.status' => 1,
					),
					'limit' => $this->pagination['limit'],
					"order" => $model . ".id ASC",
				);

				$this->paginate = $paginator;
				$this->set('user_templates', $this->paginate($model));
				$this->set('project_id', $this->request->data);
				//pr($user_templates, 1);
				$this->pagination['show_summary'] = true;
				$this->pagination['model'] = $model;
				$this->set('JeeraPaging', $this->pagination);

			}
		}
		$this->render('/Templates/partials/get_more_user_template');
	}

	public function admin_popups($form, $project_id, $template_id, $workspace_id = null) {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$response = array('project_id' => $project_id, 'template_id' => $template_id);

			if (isset($workspace_id) && !empty($workspace_id)) {
				$response['workspace_id'] = $workspace_id;

				$this->request->data = $this->Workspace->read(null, $workspace_id);

				// pr($this->request->data, 1);
			}

			$this->set('response', $response);

			$this->set('form_name', $form);
			$this->render('/Templates/partials/popup_forms');
		}
	}

	/**
	 * Displays templates for the workspace
	 *
	 * @param $project_id
	 * @return void
	 */
	public function get_workspace() {

		$type_ajax = false;
		if ($this->request->isAjax()) {
			$type_ajax = true;
		}

		if ($type_ajax) {
			$this->layout = 'ajax';
			$this->autoRender = false;

			Configure::write('debug', 0);
			$response = [
				'success' => false,
				'status' => 400,
				'content' => null,
			];
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;

				// GET ALL DISTINCT ROW NUMBERS
				$template_groups = $this->TemplateDetail->find('all', array('fields' => 'DISTINCT row_no'));

				// EXTRACT ALL ROW NUMBERS
				$grouped_ids = Set::extract($template_groups, '/TemplateDetail/row_no');

				// NOW GET GROUPED ROWS DATA INTO DIFFERENT ARRAY KEYS
				$template_detail = $this->TemplateDetail->find('all', array(
					'fields' => ['TemplateDetail.id', 'TemplateDetail.row_no', 'TemplateDetail.col_no', 'TemplateDetail.size_w', 'TemplateDetail.size_h'],
					'conditions' => array(
						'TemplateDetail.row_no' => $grouped_ids,
						'TemplateDetail.size_w >=' => 1,
						'TemplateDetail.size_h >=' => 1,
						'TemplateDetail.template_id' => $post['template_id'],
					),
				)
				);

				$templateRows = null;
				foreach ($template_detail as $k => $v) {

					$row = $v['TemplateDetail'];
					if ($row['size_w'] > 0 && $row['size_h'] > 0) {
						$row_no = $row['row_no'];
						$templateRows[$row_no][] = $row;
					}
				}

				$data['templateRows'] = $templateRows;

				if (!is_null($templateRows)) {
					$response['success'] = true;
					$response['status'] = 200;
					$response['content'] = $templateRows;
				}
				//
				$data['response'] = $response;

				$this->set('data', $data);

				$this->render('/Templates/get_workspace/');
			}
			$this->sendJson($response);

			// return new CakeResponse(array('body'=> json_encode(  $response ), true));
		}
	}

	/**
	 * Displays templates for the workspace
	 *
	 * @param $project_id
	 * @return void
	 */
	function template_select() {

		$this->layout = 'ajax';
		$this->autoRender = false;

		// Router::parseExtensions('json');

		if ($this->request->isAjax()) {
			// Configure::write('debug', 0);
			$response = [
				'success' => false,
				'msg' => '',
				'content' => null,
			];
			if ($this->request->is('post') || $this->request->is('put')) {
				// GET PRE-DETINED TEMPLATES FROM DATABASE
				$this->Template->recursive = -1;
				$templates = $this->Template->find('all', [
					'fields' => array('Template.id', 'Template.title', 'Template.description', 'Template.layout_preview'),
					'conditions' => array('Template.status' => 1),
				]);

				$content = null;
				if (!is_null($templates)) {
					$templates = Set::extract('/Template/.', $templates);
					$layout_dir = '/img/layouts/';
					foreach ($templates as $k => $v) {
						$imagePath = WWW_ROOT . $layout_dir . $v['layout_preview'];
						$imageUrl = '';
						if (file_exists($imagePath)) {
							$imageUrl = $this->webroot . $layout_dir . $v['layout_preview'];
						} else {
							$imageUrl = $this->webroot . $layout_dir . 'image_not_available.jpg';
						}
						$content[$v['id']] = [
							'text' => $v['title'],
							'value' => $v['id'],
							'selected' => false,
							'description' => $v['description'],
							'imageSrc' => $imageUrl,
						];
					}
					$response['content'] = $content;
					$this->set(compact('response')); // Pass $data to the view
					// $this->sendJson($response);
				}
			}
			$this->render('/Templates/template_select/');
		}
	}

	/**
	 * Displays templates for the workspace
	 *
	 * @param $project_id
	 * @return void
	 */
	function filter_templates() {

		$this->layout = 'ajax';
		$this->autoRender = false;
		$model = 'TemplateRelation';
		// $this->pagination['limit'] = 12;
		//echo $templateCount."pawan"; die;

		if ($this->request->isAjax()) {

			$response = [
				'success' => false,
				'msg' => '',
				'content' => null,
			];

			$view = new View($this, false);
			$view->viewPath = 'Templates/partials'; // Directory inside view directory to search for .ctp files
			$view->layout = false; // if you want to disable layout
			$templateCount = array();
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;

				$thirdpartyUser = $this->ThirdParty->find('all', array('conditions' => array('ThirdParty.status' => 1), 'order' => 'ThirdParty.username ASC'));

				/* $user_templates = $this->TemplateRelation->find('all', [
					'conditions' => [
						'TemplateRelation.status' => 1,
						'TemplateRelation.type' => $post['type'],
						'TemplateRelation.template_category_id' => $post['category_id']
					],
					"order" => ["TemplateRelation.title ASC"]
				]); */
				if ($post['type'] == 3) {
					//$this->pagination['limit'] = 9;
				} else {
					//$this->pagination['limit'] = 12;
				}

				$paginator = array(
					'conditions' => array(
						$model . '.status' => 1,
						$model . '.type' => $post['type'],
						$model . '.template_category_id' => $post['category_id'],
					),
					//'limit' => $this->pagination['limit'],
					"order" => $model . ".title ASC",
				);

				$templateCount = $this->TemplateRelation->find('count', [
					'conditions' => array(
						$model . '.status' => 1,
						$model . '.type' => $post['type'],
						$model . '.template_category_id' => $post['category_id'],
					),
				]);

				$user_templates = $this->TemplateRelation->find('all', [
					'conditions' => array(
						$model . '.status' => 1,
						$model . '.type' => $post['type'],
						$model . '.template_category_id' => $post['category_id'],
					),
					//'limit' => $this->pagination['limit'],
					"order" => $model . ".title ASC",
				]);

				$this->paginate = $paginator;
				$this->pagination['show_summary'] = true;
				$this->pagination['model'] = $model;
				$jeeraPaging1 = [];

				$jeeraPaging1['options'] = array(
					'url' => array_merge(
						array(
							'controller' => $this->request->params['controller'],
							'action' => 'filter_templates',
						),
						$this->request->params['named']
					),
					'type' => $post['type'],
					'template_category_id' => $post['category_id'],
					'columnwidht' => $post['columnwidht'],
					'templatecounter' => $templateCount,
				);

				$view->set('user_templates', $user_templates);
				// $view->set('user_templates', $this->paginate($model));
				$view->set('thirdpartyuser', $thirdpartyUser);
				$view->set('template_category_id', $post['category_id']);
				$view->set('project_id', $post['project_id']);
				$view->set('columnwidht', $post['columnwidht']);
				$view->set('templateCount', $templateCount);
				$view->set('pageLimit', $this->pagination['limit']);
				$view->set('template_paging', $jeeraPaging1);
			}

			$html = $view->render('filter_templates');
			// $this->render('/Templates/partials/filter_templates');

			echo json_encode($html);
			exit;
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
				// $post = $this->request->data;

				/*
					                  if( isset($post['date_constraints']) && $post['date_constraints'] > 0 ) {

					                  // $this->Element->validator()->add(
					                  // 'start_date',
					                  // 'required',
					                  // [
					                  // 'rule' => array('notEmpty'),
					                  // 'message' => 'Start date is required.'
					                  // ])
					                  // ->add(
					                  // 'end_date',
					                  // 'required',
					                  // [
					                  // 'rule' => array('notEmpty'),
					                  // 'message' => 'End date is required.'
					                  // ]);
					                  $this->Element->validate['start_date']['required'] = array(
					                  'rule' => array('notEmpty'),
					                  'message' => 'test'
					                  );

				*/

				$this->Element->set($this->request->data);
				if ($this->Element->validates()) {
					// pr($this->request->data);die;
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

	function filter_user_templates() {

		$this->layout = 'ajax';
		$this->autoRender = false;

		//pr($this->request->data); die;

		if ($this->request->isAjax()) {

			$response = [
				'success' => false,
				'msg' => '',
				'content' => null,
				'template_count' => null,
			];

			$view = new View($this, false);
			$view->viewPath = 'Templates/partials'; // Directory inside view directory to search for .ctp files
			$view->layout = false; // if you want to disable layout

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;

				if (isset($post['thirdparty_id']) && $post['thirdparty_id'] > 0) {

					$user_templates = $this->TemplateRelation->find('all', [
						'conditions' => [
							'TemplateRelation.status' => 1,
							'TemplateRelation.type' => 3,
							'TemplateRelation.thirdparty_id' => $post['thirdparty_id'],
							'template_category_id' => $post['template_category_id'],
						], "order" => ["TemplateRelation.title ASC"],
					]);

					$user_templates_count = $this->TemplateRelation->find('count', [
						'conditions' => [
							'TemplateRelation.status' => 1,
							'TemplateRelation.type' => 3,
							'TemplateRelation.thirdparty_id' => $post['thirdparty_id'],
							'template_category_id' => $post['template_category_id'],
						], "order" => ["TemplateRelation.title ASC"],
					]);

				} else {
					$user_templates = $this->TemplateRelation->find('all', [
						'conditions' => [
							'TemplateRelation.status' => 1,
							'TemplateRelation.type' => 3,
							'template_category_id' => $post['template_category_id'],
						], "order" => ["TemplateRelation.title ASC"],
					]);

					$user_templates_count = $this->TemplateRelation->find('count', [
						'conditions' => [
							'TemplateRelation.status' => 1,
							'TemplateRelation.type' => 3,
							'template_category_id' => $post['template_category_id'],
						], "order" => ["TemplateRelation.title ASC"],
					]);

				}
				//pr($user_templates); die;
				$view->set('user_templates', $user_templates);
				$view->set('project_id', $post['project_id']);
				//$view->set('user_templates_count', $user_templates_count);
				$view->set('columnwidht', $post['columnwidth']);

				$response = ['template_count' => $user_templates_count];
			}

			$html = $view->render('filter_user_templates');

			echo $html;
			exit;
		}
	}
	/*
		     * @name  		get_selected_templates
		     * @access		public
		     * @package  	App/Controller/TemplatesController
	*/
	public function get_selected_templates($total_area = 0) {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$response = [
				'success' => false,
				'msg' => '',
				'content' => null,
			];

			$data = null;
			$this->set('data', $total_area);

			$this->render('/Templates/partials/get_selected_templates');
		}
	}

	/*
		     * @name  		template_form
		     * @access		public
		     * @package  	App/Controller/TemplatesController
	*/
	public function template_form($total_area = 0) {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$response = [
				'success' => true,
				'content' => null,
			];

			$template_id = null;

			if ($this->request->is('post') || $this->request->is('put')) {
				if (isset($this->request->data['template_id']) && !empty($this->request->data['template_id'])) {
					$template_id = $this->request->data['template_id'];
				}
			}

			$template_categories = $this->TemplateCategory->find('list', [
				'conditions' => [
					'TemplateCategory.status' => 1,
				],
				'fields' => ['TemplateCategory.id', 'TemplateCategory.title'],
				'order' => ['TemplateCategory.title'],
				'recursive' => -1,
			]);

			$this->set('template_categories', $template_categories);
			$this->set('template_id', $template_id);
			$this->set('total_area', $total_area);

			$this->render('/Templates/partials/template_form');
		}
	}

	/*
		     * @name  		save_template
		     * @access		public
		     * @package  	App/Controller/TemplatesController
	*/
	public function save_template() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$response = [
				'success' => false,
				'content' => null,
				'type' => null,
			];

			//pr($this->request->data); die;

			if ($this->request->is('post') || $this->request->is('put')) {

				$this->TemplateRelation->set($this->request->data);

				$titlelenght = strlen($this->request->data['TemplateRelation']['title']);
				if ($titlelenght > 50) {
					$this->request->data['TemplateRelation']['title'] = substr($this->request->data['TemplateRelation']['title'], 0, 50);
					// $this->TemplateRelation->validationErrors['title'] = 'Title must be no larger than 50 characters long.';

				}
				// pr($this->request->data['TemplateRelation'], 1);

				if ($this->TemplateRelation->validates()) {

					//for user type (Set or Not)
					if (isset($this->request->data['TemplateRelation']['user_type']) && !empty($this->request->data['TemplateRelation']['user_type'])) {
						$this->request->data['TemplateRelation']['type'] = $this->request->data['TemplateRelation']['user_type'];
						unset($this->request->data['TemplateRelation']['user_type']);
					}

					if ((!isset($this->request->data['TemplateRelation']['type']) || empty($this->request->data['TemplateRelation']['type'])) && $this->Session->read('Auth.User.role_id') == 1) {
						$this->request->data['TemplateRelation']['type'] = 2;
					}
					// if Relation id( Third Party User ) is not set or empty
					if (!isset($this->request->data['TemplateRelation']['thirdparty_id']) || empty($this->request->data['TemplateRelation']['thirdparty_id'])) {
						$this->request->data['TemplateRelation']['thirdparty_id'] = 0;
						$this->request->data['TemplateRelation']['type'] = 2;
					}

					if ($this->Session->read('Auth.User.role_id') == 2) {
						$this->request->data['TemplateRelation']['type'] = 1;
					}

					/* if(isset($this->request->data['TemplateRelation']['type']) && $this->request->data['TemplateRelation']['type'] == 3 && empty($this->request->data['TemplateRelation']['thirdparty_id'])  ){

						$this->set('validationErrorsArray', $this->TemplateRelation->thirdparty_id());
						$response['content'] = $this->validateErrors($this->TemplateRelation);
					} */

					//pr($this->request->data['TemplateRelation']); die;

					// SAVE TEMPLATE RELATION DATA
					if ($this->TemplateRelation->save($this->request->data['TemplateRelation'])) {

						$newInsertId = $this->TemplateRelation->getLastInsertId();

						if ($this->Session->read('Auth.User.role_id') == 1) {

							if( $_SERVER['HTTP_HOST'] != '192.168.7.20' && $_SERVER['HTTP_HOST'] != '192.168.8.29' ){

								App::import("Model", "OrgSettingJeera");
								$OrgSettingJeera = new OrgSettingJeera();
								$whatINeed = explode(WEBDOMAIN, $_SERVER['HTTP_HOST']);
								$whatINeed = $whatINeed[0];
								$result = $OrgSettingJeera->find('first', array('conditions' => array('OrgSettingJeera.domain_name' => $whatINeed)));
								if (isset($result) && !empty($result)) {
									$organisationid = $result['OrgSettingJeera']['id'];
									// CONNECT TO PROD MAIN SERVER
									$con = mysqli_connect(root_host, root_dbuser, root_dbpass, root_dbname);
									if (mysqli_connect_errno()) {
										echo "Failed to connect to MySQL: " . mysqli_connect_error();
									}

									/*=====  save to Template Move table data ==== */
									$whatINeed = 1;
									$organisationid = 1;
									$folder_id = $this->request->data['TemplateRelation']['template_category_id'];
									$tmp_type = 2;
									$template_id = $newInsertId;
									$domain_name = $whatINeed;
									$organisation_id = $organisationid;

									$tmpinsert = mysqli_query($con, "INSERT INTO `template_moves` SET
														folder_id = " . $folder_id . ",
														template_id = " . $template_id . ",
														tmp_type = " . $tmp_type . ",
														domain_name = '" . $domain_name . "',
														organisation_id = " . $organisation_id . ",
														created = '" . date('Y-m-d h:i:s') . "',
														modified = '" . date('Y-m-d h:i:s') . "'
												");

									/*=============================================*/
								}
							}
						}

						if (isset($newInsertId) && !empty($newInsertId)) {

							$response['success'] = true;
							$response['type'] = $this->request->data['TemplateRelation']['type'];

							$area_data = $this->request->data['area'];
							// GET TEMPLATE DETAIL IDS
							$templateDetail = $this->TemplateDetail->find('all', ['conditions' => ['TemplateDetail.template_id' => $this->request->data['TemplateRelation']['template_id']]]);
							$templateDetail = Set::extract($templateDetail, '/TemplateDetail/id');

							if (isset($area_data) && !empty($area_data)) {
								// GET ALL POSTED AREA DATA AND LOOP THROUGH
								$areas = null;
								foreach ($area_data as $k => $v) {
									$newInsertAreaId = null;
									//
									$templateDetailId = $templateDetail[$k];
									$area_title = (isset($v['title']) && !empty($v['title'])) ? $v['title'] : "Area - " . ($k + 1);
									$area_desc = (isset($v['purpose']) && !empty($v['purpose'])) ? $v['purpose'] : "Area - " . ($k + 1);

									$areas['AreaRelation'] = ['title' => $area_title, 'description' => $area_desc, 'tooltip_text' => $area_title, 'is_standby' => '0', 'status' => '1', 'template_detail_id' => $templateDetailId, 'template_relation_id' => $newInsertId];
									// SAVE AREA RELATION DATA
									if ($this->AreaRelation->save($areas)) {

										$newInsertAreaId = $this->AreaRelation->getLastInsertId();
										$this->AreaRelation->id = null;
										//
										if (isset($newInsertAreaId) && !empty($newInsertAreaId)) {
											$eldata = null;
											if (isset($v['element']) && !empty($v['element'])) {
												foreach ($v['element'] as $ek => $ev) {
													// pr($ev, 1);
													if (isset($ev['element_title']) && !empty($ev['element_title'])) {
														$eldata[$ek]['ElementRelation'] = [
															'title' => $ev['element_title'],
															'description' => $ev['task_description'],
															'comments' => $ev['element_title'],
															'color_code' => $ev['task_color'],
															'status' => '1',
															'sort_order' => ($ek + 1),
															'area_relation_id' => $newInsertAreaId,
														];
														if (isset($ev['file_name']) && !empty($ev['file_name'])) {
															foreach ($ev['file_name'] as $fk => $fv) {
																$pathinfo = mime_content_type(TEMPLATE_DOCUMENTS . $fv);
																$eldata[$ek]['ElementRelation']['ElementRelationDocument'][$fk] = [
																	'creater_id' => $this->Session->read("Auth.User.id"),
																	'title' => $fv,
																	'file_name' => $fv,
																	'file_size' => formatSizeUnits(filesize(TEMPLATE_DOCUMENTS . $fv)),
																	'file_type' => $pathinfo,
																	'status' => 1,
																];
															}
														}
													}

												}
												// pr($eldata, 1);
												if (isset($eldata) && !empty($eldata)) {
													$this->ElementRelation->saveAll($eldata, ['deep' => true]);

												}
											}
										}

									}
								}

							}
							$this->Session->setFlash('Knowledge Template has been added successfully.', 'success');
						}
					}

				} else {
					$response['content'] = $this->TemplateRelation->validationErrors;
					//pr($this->validateErrors($this->TemplateRelation));
				}
			}
			echo json_encode($response);
			exit;
		}
	}

	public function element_document_delete() {
		$this->layout = 'ajax';
		$this->autoRender = false;
		if ($this->request->isAjax()) {
			$response = [
				'success' => false,
				'msg' => '',
				'content' => null,
			];
			// pr($this->request->data, 1);
			if (isset($this->request->data['id']) && !empty($this->request->data['id']) && $this->request->data['id'] != 'null') {
				$id = $this->request->data['id'];
				$fielname = $this->request->data['file_name'];
				$conditions = array("ElementRelationDocument.file_name" => $fielname);
				if (isset($id) && !empty($id)) {
					$conditions['AND'][] = array("ElementRelationDocument.id" => $id);
				}
				$old = $this->ElementRelationDocument->find("first", array("conditions" => $conditions));
				$this->request->data['ElementRelationDocument']['id'] = $old['ElementRelationDocument']['id'];
				$filePath = TEMPLATE_DOCUMENTS . $old['ElementRelationDocument']['file_name'];
				if ($this->ElementRelationDocument->delete($this->request->data['ElementRelationDocument']['id'])) {
					if (file_exists($filePath)) {
						unlink($filePath);
					}
					$response = [
						'success' => true,
						'msg' => 'Attachment has been removed successfully.',
						'content' => null,
					];
					echo json_encode($response);
					exit();
				} else {
					echo json_encode($response);
					exit();
				}
			} else {

				$filePath = TEMPLATE_DOCUMENTS . $this->request->data['file_name'];
				if (file_exists($filePath)) {
					unlink($filePath);
				}
				$response = [
					'success' => true,
					'msg' => 'Attachment has been removed successfully.',
					'content' => null,
				];
				echo json_encode($response);
				exit();
			}
		}
	}

	/*
		     * @name  		save_template_updates
		     * @access		public
		     * @package  	App/Controller/TemplatesController
	*/
	public function save_template_updates() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$response = [
				'success' => false,
				'content' => null,
				'type' => '',
			];

			if ($this->request->is('post') || $this->request->is('put')) {
				$this->TemplateRelation->set($this->request->data);

				$titlelenght = strlen($this->request->data['TemplateRelation']['title']);

				if ($titlelenght > 50) {

					$this->TemplateRelation->validationErrors['title'] = "Title must be no larger than 50 characters long.";

				}
				//pr($this->request->data);die;

				if ($this->TemplateRelation->validates($this->request->data)) {

					if (isset($this->request->data['TemplateRelation']['user_type']) && !empty($this->request->data['TemplateRelation']['user_type'])) {
						$this->request->data['TemplateRelation']['type'] = $this->request->data['TemplateRelation']['user_type'];
					} else {
						$this->request->data['TemplateRelation']['type'] = 1;
					}

					//pr($this->request->data);die;

					// SAVE TEMPLATE RELATION DATA
					if ($this->TemplateRelation->save($this->request->data['TemplateRelation'])) {

						if ($this->params['pass']['0'] == 'update') {

							$this->Session->setFlash('Knowledge Template has updated Successfully.', 'success');
						}

						$newInsertId = $this->request->data['TemplateRelation']['id'];

						if (isset($newInsertId) && !empty($newInsertId)) {

							$response['success'] = true;
							$response['type'] = $this->request->data['TemplateRelation']['type'];

							$area_data = $this->request->data['area'];
							// GET TEMPLATE DETAIL IDS
							$templateDetail = $this->TemplateDetail->find('all', ['conditions' => ['TemplateDetail.template_id' => 4]]);
							$templateDetail = Set::extract($templateDetail, '/TemplateDetail/id');
							//pr($area_data, 1);
							// pr($templateDetail );

							if (isset($area_data) && !empty($area_data)) {
								// GET ALL POSTED AREA DATA AND LOOP THROUGH
								$areas = null;
								foreach ($area_data as $k => $v) {
									$newInsertAreaId = null;
									//
									$area_title = (isset($v['title']) && !empty($v['title'])) ? $v['title'] : "Area - " . ($k + 1);
									$area_desc = (isset($v['purpose']) && !empty($v['purpose'])) ? $v['purpose'] : "Area - " . ($k + 1);

									$areas['AreaRelation'] = ['id' => $v['id'], 'title' => $area_title, 'description' => $area_desc, 'tooltip_text' => $area_title];
									// SAVE AREA RELATION DATA
									if ($this->AreaRelation->save($areas)) {
										// pr($this->request->data, 1);
										$newInsertAreaId = $v['id'];
										// $this->AreaRelation->id = null;

										if (isset($newInsertAreaId) && !empty($newInsertAreaId)) {
											$eldata = null;

											if (isset($v['element']) && !empty($v['element'])) {
												$i = 1;
												// pr($v['element'], 1);
												foreach ($v['element'] as $ek => $ev) {

													if (isset($ev['id']) && !empty($ev['id'])) {
														if (isset($ev['element_title']) && !empty($ev['element_title'])) {

															$elupdate['ElementRelation'] = [
																'id' => $ev['id'],
																'title' => $ev['element_title'],
																'description' => $ev['task_description'],
																'comments' => $ev['element_title'],
																'sort_order' => ($i + 1),
																'color_code' => $ev['task_color'],
															];
															$el_doc_data = null;
															if (isset($ev['file_name']) && !empty($ev['file_name'])) {
																foreach ($ev['file_name'] as $fk => $fv) {
																	$pathinfo = mime_content_type(TEMPLATE_DOCUMENTS . $fv);
																	$el_doc_data[$fk]['ElementRelationDocument'] = [
																		'element_relation_id' => $ev['id'],
																		'creater_id' => $this->Session->read("Auth.User.id"),
																		'title' => $fv,
																		'file_name' => $fv,
																		'file_size' => formatSizeUnits(filesize(TEMPLATE_DOCUMENTS . $fv)),
																		'file_type' => $pathinfo,
																		'status' => 1,
																	];
																}
																// pr($el_doc_data, 1);
																if (isset($el_doc_data) && !empty($el_doc_data)) {
																	$this->ElementRelationDocument->saveAll($el_doc_data);
																}
															}
															// pr($elupdate, 1);
															$this->ElementRelation->save($elupdate);
														}
													} else {
														if (isset($ev['element_title']) && !empty($ev['element_title'])) {

															$counter = relational_elements($newInsertAreaId);
															$eldata[$ek]['ElementRelation'] = [
																'title' => $ev['element_title'],
																'description' => $ev['task_description'],
																'comments' => $ev['element_title'],
																'color_code' => $ev['task_color'],
																'status' => '1',
																'sort_order' => ($i + 1),
																'area_relation_id' => $newInsertAreaId,
															];

															if (isset($ev['file_name']) && !empty($ev['file_name'])) {
																// pr($ev['file_name'], 1);
																foreach ($ev['file_name'] as $fk => $fv) {
																	$pathinfo = pathinfo(TEMPLATE_DOCUMENTS . $fv);
																	$eldata[$ek]['ElementRelation']['ElementRelationDocument'][$fk] = [
																		'creater_id' => $this->Session->read("Auth.User.id"),
																		'title' => $fv,
																		'file_name' => $fv,
																		'file_size' => formatSizeUnits(filesize(TEMPLATE_DOCUMENTS . $fv)),
																		'file_type' => $pathinfo['extension'],
																		'status' => 1,
																	];
																}
															}

														}
													}
													$i++;

												}
											}
											// pr($eldata, 1);
											if (isset($eldata) && !empty($eldata)) {
												$this->ElementRelation->saveAll($eldata, ['deep' => true]);

											}
										}

									}
								}

							}
							//
						}
					}
				} else {

					$response['content'] = $this->TemplateRelation->validationErrors;
					//pr($this->validateErrors($this->TemplateRelation));

				}
			}
			//	pr($response); die;
			echo json_encode($response);
			exit;
		}
	}

	/*
		     * @name  		create_templates
		     * @access		public
		     * @package  	App/Controller/TemplatesController
	*/
	public function create_templates() {

		$this->layout = 'inner';
		$view_data['title_for_layout'] = __('Add Knowledge Template', true);
		$view_data['page_heading'] = __('Add Knowledge Template', true);
		$view_data['page_subheading'] = __('Create a Knowledge Template for Projects', true);

		$this->set($view_data);

		$listdomainusers = $this->Common->userDetail($this->Session->read('Auth.User.id'));

		if($listdomainusers['UserDetail']['create_template'] != 1){

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

		$crumb = [
			'last' => ['Add Knowledge Template'],
		];

		$this->set('crumb', $crumb);
	}

	/*
		     * @name  		update_template
		     * @access		public
		     * @package  	App/Controller/TemplatesController
	*/
	public function update_template($template_id = null, $template_category_id = null, $project_id = null) {

		$this->layout = 'inner';

		$view_data['title_for_layout'] = __('Edit Knowledge Template', true);
		$view_data['page_heading'] = __('Edit Knowledge Template', true);
		$view_data['page_subheading'] = __('Update a Knowledge Template', true);

		$crumb = null;

		if (isset($template_id) && !empty($template_id)) {
			$templateData = $this->TemplateRelation->find('first', [
				'conditions' => [
					'TemplateRelation.user_id' => $this->user_id,
					'TemplateRelation.id' => $template_id,
				],
				'order' => ['TemplateRelation.created ASC'],
			]);
			if (isset($templateData) && !empty($templateData)) {
				$templateData['TemplateRelation']['title'] = html_entity_decode(substr($templateData['TemplateRelation']['title'], 0, 50), ENT_QUOTES);
				$templateData['TemplateRelation']['description'] = html_entity_decode(substr($templateData['TemplateRelation']['description'], 0, 560), ENT_QUOTES);
				$templateData['TemplateRelation']['key_result_target'] = html_entity_decode(substr($templateData['TemplateRelation']['key_result_target'], 0, 250), ENT_QUOTES);

				// pr($templateData, 1);

				$this->request->data = $templateData;
				$view_data['templateData'] = $templateData;
				$cat_data = getByDbId('TemplateCategory', $template_category_id, ['title']);
				$crumb['Templates'] = [
					'data' => [
						'url' => '/templates/create_workspace/' . $project_id . '/' . $template_category_id,
						'class' => 'tipText',
						'title' => 'Select Knowledge Templates',
						'data-original-title' => 'Knowledge Templates',
					],
				];
			} else {
				// $this->Session->setFlash(__('Unauthorized access.'));

				$this->redirect($this->referer());
			}
		}

		$template_categories = $this->TemplateCategory->find('list', [
			'conditions' => [
				'TemplateCategory.status' => 1,
			],
			'fields' => ['TemplateCategory.id', 'TemplateCategory.title'],
			'order' => ['TemplateCategory.title'],
			'recursive' => -1,
		]);

		$crumb['last'] = ['Edit Knowledge Template'];
		// pr($crumb, 1);

		$this->set('template_categories', $template_categories);
		$view_data['template_id'] = $template_id;
		$view_data['template_category_id'] = $template_category_id;
		$view_data['template_original_id'] = $templateData['Template']['id'];
		$view_data['project_id'] = $project_id;
		$view_data['crumb'] = $crumb;
		$this->set($view_data);

		$this->setJsVar('template_category_id', $template_category_id);
		$this->setJsVar('project_id', $project_id);

	}

	public function like_comment($template_id = null) {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$data = $response = null;
			$response = ['content' => '', 'success' => false];

			if (isset($template_id) && !empty($template_id)) {
				// check that the current user not posted like for this comment previously
				$data = $this->TemplateLike->find('count', [
					'conditions' => [
						'TemplateLike.user_id' => $this->user_id,
						'TemplateLike.template_relation_id' => $template_id,
					],
					'recursive' => -1,
				]);
				$response['success'] = true;
				// if the current user not posted like for this comment previously, only then enter data in database
				if (isset($data) && empty($data)) {
					$in_data['TemplateLike']['user_id'] = $this->user_id;
					$in_data['TemplateLike']['template_relation_id'] = $template_id;
					// pr($this->TemplateLike->save($in_data), 1);
					if ($this->TemplateLike->save($in_data)) {
						$response['content'] = $this->TemplateLike->getLastInsertId();
					}
				}
			}

			echo json_encode($response);
			exit();
		}
	}

	public function trash_template() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$data = $response = null;
			$response = ['content' => '', 'success' => false];
			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;
				// pr($post, 1);

				if (isset($post['template_id']) && !empty($post['template_id'])) {
					$element_relations = template_elements($post['template_id'], false);
					if (isset($element_relations) && !empty($element_relations)) {
						$element_relation_ids = Set::extract($element_relations, '/ElementRelation/id');
						$this->ElementRelation->deleteAll(['ElementRelation.id' => $element_relation_ids]);
					}
					if ($this->TemplateRelation->delete($post['template_id'], true)) {
						$response['success'] = true;
					}
				}
			}

			echo json_encode($response);
			exit();
		}
	}

	public function trash_element() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$data = $response = null;
			$response = ['content' => '', 'success' => false];
			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;

				if (isset($post['element_id']) && !empty($post['element_id'])) {

					if ($this->ElementRelation->delete($post['element_id'])) {
						$response['success'] = true;
					}

				}
			}

			echo json_encode($response);
			exit();
		}
	}

	public function set_rating($template_id = null) {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$data = $response = null;

			$response = ['content' => '', 'success' => false];

			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;

				$data['TemplateReview'] = [
					'template_relation_id' => $template_id,
					'user_id' => $this->user_id,
					'comments' => $post['comments'],
					'rating' => $post['rating'],
					'used_unused' => $post['used_unused'],
				];
				if (isset($template_id) && !empty($template_id)) {
					$data['TemplateReview']['id'] = $post['id'];
				}

				if (isset($template_id) && !empty($template_id)) {

					if ($this->TemplateReview->save($data)) {
						$response['success'] = true;
					}

				}
			}

			echo json_encode($response);
			exit();
		}
	}

	public function template_categories() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$data = $response = null;
			$response = ['content' => '', 'success' => false];

			$view = new View($this, false);
			$view->viewPath = 'Templates/partials'; // Directory inside view directory to search for .ctp files
			$view->layout = false; // if you want to disable layout

			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;
				// pr($post, 1);
				/* GET TEMPLATES CATEGORIES */
				$template_categories = $this->TemplateCategory->find('all', [
					'conditions' => [
						'TemplateCategory.status' => 1,
					],
					'recursive' => -1,
				]);

				$view->set('template_categories', $template_categories);
				$view->set('project_id', $post['project_id']);

			}

			// pr($data, 1);
			$html = $view->render('template_categories');

			echo json_encode($html);
			exit;
			// $this->render('/Templates/partials/template_categories');

		}
	}

	public function view_elements($template_id = null) {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$data = $response = null;
			$response = ['content' => '', 'success' => false];

			$this->set('template_id', $template_id);

			$this->render(DS . 'Templates' . DS . 'partials' . DS . 'view_elements');

		}
	}

	public function add_review($template_relation_id = null) {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$data = $this->TemplateReview->find('all', [
				'conditions' => ['TemplateReview.template_relation_id' => $template_relation_id],
				'order' => ['TemplateReview.created DESC'],
			]);

			$dataCreator = $this->TemplateRelation->find('first', [
				'conditions' => ['TemplateRelation.id' => $template_relation_id],

			]);

			$this->set('template_relation_id', $template_relation_id);
			$this->set('data', $data);
			$this->set('dataCreator', $dataCreator);

			$this->render(DS . 'Templates' . DS . 'partials' . DS . 'add_review');
		}
	}

	public function save_review() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$response = [
				'success' => false,
				'msg' => '',
				'content' => null,
			];

			if ($this->request->is('post') || $this->request->is('put')) {
				//pr($this->request->data, 1);
				$this->TemplateReview->set($this->request->data);
				if ($this->TemplateReview->validates()) {

					$post = $this->request->data['TemplateReview'];

					$post['used_unused'] = (isset($post['used_unused']) && !empty($post['used_unused'])) ? 1 : 0;

					$response['content'] = $post['template_relation_id'];

					if (isset($post['id']) && !empty($post['id'])) {
						$this->TemplateReview->id = $post['id'];
					}

					if ($this->TemplateReview->save($post)) {

						$item_id = $post['template_relation_id'];
						$review_count = template_reviews($item_id, 1);
						$sum_template_reviews = sum_template_reviews($item_id);
						$average = 0;
						if ((isset($sum_template_reviews[0][0]['total']) && !empty($sum_template_reviews[0][0]['total'])) && (isset($review_count) && !empty($review_count))) {

							$average = $sum_template_reviews[0][0]['total'] / $review_count;
							$average = round($average, 2);

							$this->TemplateRelation->id = $item_id;
							$this->TemplateRelation->rating = $average;

							$this->TemplateRelation->save($this->TemplateRelation);

						}

						$response['success'] = true;
					}
				} else {
					$response['content'] = $this->TemplateReview->validationErrors;
					// pr($this->TemplateReview->validationErrors,1);
				}
			}
			echo json_encode($response);
			exit;

		}
	}

	public function select_user_template() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$response = [
				'success' => false,
				'content' => null,
			];

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;

				$template_id = $post['template_id'];
				$project_id = $post['project_id'];

				$templateData = $this->TemplateRelation->find('first', ['conditions' => ['TemplateRelation.id' => $template_id]]);

				if (isset($templateData) && !empty($templateData)) {
					$templateDetail = $templateData['TemplateRelation'];
					if (!isset($templateDetail['key_result_target']) || empty($templateDetail['key_result_target'])) {
						//$templateDetail['key_result_target'] = 'This is your Key Result Target';
					}

					$workspaceData['Workspace'] = [
						'template_relation_id' => $templateDetail['id'],
						'title' => $templateDetail['title'],
						'description' => $templateDetail['description'],
						'template_id' => $templateDetail['template_id'],
						'color_code' => $templateDetail['color_code'],
						'updated_user_id' => $this->user_id,
						'created_by' => $this->user_id,
						'status' => 1,
						'studio_status' => 0,
						'sign_off' => 0,
					];

					// SAVE WORKSPACE
					if ($this->Workspace->save($workspaceData, false)) {

						$workspace_id = $this->Workspace->getLastInsertId();
						$response['success'] = true;
						$response['content'] = $workspace_id;

						$data1 = array();
						$data2 = array();
						$current_user_id = $this->Auth->user('id');

						$this->loadModel('UserPermission');
						$all_users = $this->UserPermission->find('all',array('conditions'=>array('UserPermission.project_id'=>$project_id,'UserPermission.user_id !='=>$current_user_id,'UserPermission.role'=>array('Creator','Group Owner','Owner'),'UserPermission.workspace_id IS NULL'),'fields'=>array( 'user_id')));


						if(isset($all_users) && !empty($all_users)) {
							$project_all_users = Set::extract($all_users, '/UserPermission/user_id');

							/************** socket messages **************/
							if (SOCKET_MESSAGES) {

								$prj_users = $project_all_users;

								$s_open_users = null;
								if (isset($prj_users) && !empty($prj_users)) {
									foreach ($prj_users as $key => $value) {
										if (web_notify_setting($value, 'workspace', 'workspace_sharing')) {
											$s_open_users[] = $value;
										}
									}
								}
								$userDetail = get_user_data($current_user_id);
								$content = [
									'socket' => [
										'notification' => [
											'type' => 'workspace_sharing',
											'created_id' => $this->user_id,
											'project_id' => $project_id,
											'refer_id' => $workspace_id,
											'creator_name' => $userDetail['UserDetail']['full_name'],
											'subject' => 'Workspace sharing',
											'heading' => 'Permission: Owner',
											'sub_heading' => 'Project: ' . strip_tags(getFieldDetail('Project', $project_id, 'title')).'<br />Workspace: ' . strip_tags(getFieldDetail('Workspace', $workspace_id, 'title')),
											'date_time' => timezoneTimeConvertor($this->user_id, date('Y-m-d g:iA'), $format = 'd M Y g:iA') . user_country($this->user_id),
										],
									],
								];
								if (is_array($s_open_users)) {
									$content['socket']['received_users'] = array_values($s_open_users);
								}
								$response['content'] = $content;
							}
							foreach ($project_all_users as $key => $value) {
								$this->Common->workspace_share_email($value, $project_id, $workspace_id);
							}

						}
						/************** socket messages **************/


						// GET SORT ORDER OF PREVIOUS WORKSPACES OF THE SELECTED PROJECT
						$maximum = $this->ProjectWorkspace->query('SELECT MAX(sort_order) as maximum FROM project_workspaces WHERE project_id = "' . $project_id . '"');
						$max_sort = (isset($maximum[0][0]['maximum']) && !empty($maximum[0][0]['maximum'])) ? ($maximum[0][0]['maximum'] + 1) : 0;

						// AND ASSIGN NEXT ORDER TO NEWLY CREATED WORKSPACE IN PROJECT-WORKSPACE TABLE
						$pwdata['ProjectWorkspace'] = ['project_id' => $project_id, 'workspace_id' => $workspace_id, 'sort_order' => $max_sort, 'leftbar_status' => 1];
						$this->ProjectWorkspace->save($pwdata);
						$project_workspace_id = $this->ProjectWorkspace->getLastInsertId();

						// INSERT ENTRY IN WORKSPACE-PERMISSION TABLE WITH FULL PERMISSIONS
						$this->loadModel('WorkspacePermission');

						$wsp_permit['WorkspacePermission']['user_id'] = $this->user_id;
						$wsp_permit['WorkspacePermission']['user_project_id'] = project_upid($project_id);
						$wsp_permit['WorkspacePermission']['project_workspace_id'] = $project_workspace_id;
						$wsp_permit['WorkspacePermission']['permit_read'] = 1;
						$wsp_permit['WorkspacePermission']['permit_add'] = 1;
						$wsp_permit['WorkspacePermission']['permit_edit'] = 1;
						$wsp_permit['WorkspacePermission']['permit_delete'] = 1;
						$wsp_permit['WorkspacePermission']['permit_copy'] = 1;
						$wsp_permit['WorkspacePermission']['permit_move'] = 1;
						$wsp_permit['WorkspacePermission']['is_editable'] = 1;

						$this->WorkspacePermission->save($wsp_permit);

						// $this->Common->projectModified($project_id, $this->user_id);

						// GET ALL AREA FROM TEMPLATE AND ASSIGN THEM WITH THE WORKSPACE
						if (isset($templateData['AreaRelation']) && !empty($templateData['AreaRelation'])) {
							foreach ($templateData['AreaRelation'] as $akey => $aval) {
								$areaDetail = $aval;
								$areaData = null;
								$areaData['Area'] = [
									'id' => null,
									'title' => $areaDetail['title'],
									'description' => $areaDetail['description'],
									'tooltip_text' => $areaDetail['description'],
									'workspace_id' => $workspace_id,
									'template_detail_id' => $areaDetail['template_detail_id'],
									'status' => 1,
									'studio_status' => 0,
								];
								// SAVE AREA
								$this->Area->id = null;
								if ($this->Area->save($areaData)) {

									$area_id = $this->Area->getLastInsertId();

									$elementData = relational_elements($areaDetail['id'], false);
									if (isset($elementData) && !empty($elementData)) {

										foreach ($elementData as $ekey => $eval) {
											$elementDetail = $eval['ElementRelation'];
											$elData['Element'] = [
												'area_id' => $area_id,
												'updated_user_id' => $this->user_id,
												'created_by' => $this->user_id,
												'title' => $elementDetail['title'],
												'description' => $elementDetail['description'],
												'comments' => '',
												'color_code' => $elementDetail['color_code'],
												'date_constraints' => 0,
												'status' => 1,
												'studio_status' => 0,
												'sort_order' => ($ekey + 1),
											];

											$this->Element->id = null;
											if ($this->Element->save($elData, false)) {

												$element_id = $this->Element->getLastInsertId();

												// INSERT ENTRY IN ELEMENT-DOCUMENT TABLE FROM ELEMENT-RELATION-DOCUMENT TABLE RELATED TO THIS ELEMENT
												$elementDocData = $eval['ElementRelationDocument'];
												$this->loadModel('ElementRelationDocument');
												foreach ($elementDocData as $keyDoc => $valDoc) {
													$valDocData = $valDoc;
													// pr($valDoc, 1);
													$this->ElementDocument->id = null;
													$el_doc['ElementDocument']['element_id'] = $element_id;
													$el_doc['ElementDocument']['creater_id'] = $this->user_id;
													$el_doc['ElementDocument']['updated_user_id'] = $this->user_id;
													$el_doc['ElementDocument']['title'] = $valDocData['title'];
													$el_doc['ElementDocument']['file_name'] = $valDocData['file_name'];
													$el_doc['ElementDocument']['file_size'] = $valDocData['file_size'];
													$el_doc['ElementDocument']['file_type'] = $valDocData['file_type'];
													$el_doc['ElementDocument']['status'] = $valDocData['status'];
													$el_doc['ElementDocument']['is_search'] = 0;
													if ($this->ElementDocument->save($el_doc)) {
														$tmplFile = TEMPLATE_DOCUMENTS . $valDocData['file_name'];
														$eleDocFile = ELEMENT_DOCUMENT_PATH . $element_id . '/' . $valDocData['file_name'];
														if (!file_exists(ELEMENT_DOCUMENT_PATH . $element_id)) {
															mkdir(ELEMENT_DOCUMENT_PATH . $element_id, 0777, true);
														}
														copy($tmplFile, $eleDocFile);
													}
												}

												// INSERT ENTRY IN ELEMENT-PERMISSION TABLE WITH FULL PERMISSIONS
												$this->loadModel('ElementPermission');
												$el_permit['ElementPermission']['user_id'] = $this->user_id; //$this->Session->read('Auth.User.id');//
												$el_permit['ElementPermission']['element_id'] = $element_id;
												$el_permit['ElementPermission']['project_id'] = $project_id;
												$el_permit['ElementPermission']['workspace_id'] = $workspace_id;
												$el_permit['ElementPermission']['permit_read'] = 1;
												$el_permit['ElementPermission']['permit_add'] = 1;
												$el_permit['ElementPermission']['permit_edit'] = 1;
												$el_permit['ElementPermission']['permit_delete'] = 1;
												$el_permit['ElementPermission']['permit_copy'] = 1;
												$el_permit['ElementPermission']['permit_move'] = 1;
												$el_permit['ElementPermission']['is_editable'] = 1;
												$this->ElementPermission->save($el_permit);
												$elementPermissionId = $this->ElementPermission->getLastInsertId();

												$this->ElementPermission->id = null;

												// $this->update_project_modify($element_id, $project_id);
											}
										}

									}

								}
							}
						}

					}
				}

			}
			echo json_encode($response);
			exit;

		}
	}

	public function get_reviews($template_relation_id = null) {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$response = [
				'success' => true,
				'msg' => '',
				'content' => null,
			];

			$data = $this->TemplateReview->find('all', [
				'conditions' => ['TemplateReview.template_relation_id' => $template_relation_id],
				'order' => ['TemplateReview.created DESC'],
			]);

			$view = new View($this, false);
			$view->viewPath = 'Templates/partials'; // Directory inside view directory to search for .ctp files
			$view->layout = false; // if you want to disable layout
			$view->set('template_relation_id', $template_relation_id);
			$view->set('data', $data);

			$html = $view->render('get_reviews');
			// $this->render(DS . 'Projects' . DS . 'partials' . DS . 'objectives' . DS . 'get_annotations');

			echo json_encode($html);
			exit();
		}
	}

	public function get_reviews_count($template_relation_id = null) {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$response = [
				'success' => true,
				'msg' => '',
				'content' => null,
			];

			$data = $this->TemplateReview->find('count', [
				'conditions' => ['TemplateReview.template_relation_id' => $template_relation_id],
				'order' => ['TemplateReview.created DESC'],
			]);

			echo json_encode($data);
			exit();

		}
	}

	public function review_stars($template_relation_id = null) {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$response = [
				'success' => true,
				'msg' => '',
				'content' => null,
			];

			$view = new View($this, false);
			$view->viewPath = 'Templates/partials'; // Directory inside view directory to search for .ctp files
			$view->layout = false; // if you want to disable layout
			$view->set('template_id', $template_relation_id);

			$html = $view->render('review_stars');

			echo json_encode($html);
			exit();

		}
	}

	public function delete_review($id = null, $template_id = null) {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$response = [
				'success' => true,
				'msg' => '',
				'content' => null,
			];

			if ($this->TemplateReview->delete($id)) {

				$item_id = $template_id;
				$review_count = template_reviews($item_id, 1);
				$sum_template_reviews = sum_template_reviews($item_id);
				$average = 0;
				if ((isset($sum_template_reviews[0][0]['total']) && !empty($sum_template_reviews[0][0]['total'])) && (isset($review_count) && !empty($review_count))) {

					$average = $sum_template_reviews[0][0]['total'] / $review_count;
					$average = round($average, 2);

					$this->TemplateRelation->id = $item_id;
					$this->TemplateRelation->rating = $average;

					$this->TemplateRelation->save($this->TemplateRelation);

				}

				echo json_encode($id);
			}

			exit();

		}
	}

	public function update_project_modify($element_id = null, $project_id = null) {
		if (!isset($element_id) || empty($element_id)) {
			return true;
		}

		if (!isset($project_id) || empty($project_id)) {
			$project_id = $this->Element->getProject($element_id);
		}

		if (!empty($project_id)) {
			// $this->Common->projectModified($project_id, $this->user_id);
		}
		return true;
	}

	public function show_admin_profile($user_id) {
		$this->loadModel('Timezone');
		$this->User->id = $user_id;

		$response = [
			'success' => false,
			'content' => null,
			'msg' => '',
		];

		if (!$this->User->exists()) {
			$response['msg'] = 'Invalid User Profile.';
		}

		$userTimezone = $this->Timezone->find('first', ['conditions' => ['Timezone.user_id' => $user_id]]);
		//echo $user_id;
		$user_details = $this->ThirdParty->find('first', ['conditions' => ['ThirdParty.id' => $user_id]]);
		//pr($user_details, 1);
		$this->set('user_timezone', $userTimezone);
		$this->set('user_details', $user_details);
		$this->set('referer', $this->referer());

	}

	public function search_category() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$data = $response = null;
			$response = ['content' => '', 'success' => false];
			$keyword = null;
			$starRating = array();
			$flag = false;

			if (isset($this->request->data) && !empty($this->request->data['keyword'])) {
				$keyword = $this->request->data['keyword'];
				$flag = true;
			}

			if (isset($this->request->data) && !empty($this->request->data['starRating'])) {
				$starRating = explode(',', $this->request->data['starRating']);
				$flag = true;

			}

			$actual = '';

			if ($flag == false) {
				$actual = 'utemp_cat_list_actual';
			}
			$this->set('actual', $actual);

			$this->TemplateRelation->unbindModel(array('hasMany' => array('AreaRelation', 'TemplateLike', 'TemplateReview')), false);
			$this->TemplateRelation->unbindModel(array('belongsTo' => array('Template', 'User')), false);

			if (isset($keyword) && !empty($keyword)) {

				/* 				$template_categories = $this->TemplateRelation->find('all', [
					'conditions' => [
					'TemplateRelation.status' => '1',
					'AND' => array(
					'TemplateRelation.title Like "%'.$keyword.'%"'
					)
					],
					'group'=>'TemplateRelation.template_category_id'
				*/

				$ser = '^';

				$keyword = Sanitize::escape(like($keyword, $ser ));

				//die;


				$template_categories = $this->TemplateRelation->query('SELECT count(TemplateRelation.id) as total, `TemplateRelation`.`id`, `TemplateRelation`.`type`, `TemplateRelation`.`user_id`, `TemplateRelation`.`thirdparty_id`, `TemplateRelation`.`template_id`, `TemplateRelation`.`template_category_id`, `TemplateRelation`.`rating`, `TemplateRelation`.`status`, `TemplateRelation`.`title`, `TemplateRelation`.`description`, `TemplateRelation`.`key_result_target`, `TemplateRelation`.`color_code`, `TemplateRelation`.`created`, `TemplateRelation`.`modified`, `TemplateCategory`.`id`, `TemplateCategory`.`title`, `TemplateCategory`.`status`,  `TemplateCategory`.`cat_icon`, `TemplateCategory`.`created`, `TemplateCategory`.`modified` FROM `template_relations` AS `TemplateRelation` LEFT JOIN `template_categories` AS `TemplateCategory` ON (`TemplateRelation`.`template_category_id` = `TemplateCategory`.`id`)  WHERE `TemplateRelation`.`status` = 1 AND  `TemplateRelation`.`title` like "%' . $keyword . '%" ESCAPE "'.$ser.'" GROUP BY `TemplateRelation`.`template_category_id`');

			} else if (isset($starRating) && !empty($starRating)) {

				$ratingConditions = 'AND ( ';

				foreach ($starRating as $ratingValue) {
					//  = array(
					// ('TemplateRelation'.'rating' >= '0.5' AND 'TemplateRelation'.'rating' <= '1.4')
					// );

					if ($ratingValue == '1') {
						$ratingConditions .= '(TemplateRelation.rating >= 0.5 AND TemplateRelation.rating  <= 1.4)';
					}

					if ($ratingValue == '2') {
						if (isset($ratingConditions) && !empty($ratingConditions) && $ratingConditions != 'AND ( ') {
							$ratingConditions .= ' OR ';
						}
						$ratingConditions .= '(TemplateRelation.rating >= 1.5 AND TemplateRelation.rating  <= 2.4)';

					}

					if ($ratingValue == '3') {
						if (isset($ratingConditions) && !empty($ratingConditions) && $ratingConditions != 'AND ( ') {
							$ratingConditions .= ' OR ';
						}
						$ratingConditions .= ' (TemplateRelation.rating >= 2.5 AND TemplateRelation.rating  <= 3.4)';
					}

					if ($ratingValue == '4') {
						if (isset($ratingConditions) && !empty($ratingConditions) && $ratingConditions != 'AND ( ') {
							$ratingConditions .= ' OR ';
						}
						$ratingConditions .= ' (TemplateRelation.rating >= 3.5 AND TemplateRelation.rating  <= 4.4)';
					}

					if ($ratingValue == '5') {
						if (isset($ratingConditions) && !empty($ratingConditions) && $ratingConditions != 'AND ( ') {
							$ratingConditions .= ' OR ';
						}
						$ratingConditions .= ' (TemplateRelation.rating >= 4.5 AND TemplateRelation.rating  <= 5)';
					}
				}

				$ratingConditions .= ' ) ';
				//$ratingConditions = array_merge($ratingConditions,$ratingConditions1,$ratingConditions2,$ratingConditions3,$ratingConditions4,$ratingConditions5);

				$template_categories = $this->TemplateRelation->query('SELECT `TemplateRelation`.`id`, `TemplateRelation`.`type`, `TemplateRelation`.`user_id`, `TemplateRelation`.`thirdparty_id`, `TemplateRelation`.`template_id`, `TemplateRelation`.`template_category_id`, `TemplateRelation`.`rating`, `TemplateRelation`.`status`, `TemplateRelation`.`title`, `TemplateRelation`.`description`, `TemplateRelation`.`key_result_target`, `TemplateRelation`.`color_code`, `TemplateRelation`.`created`, `TemplateRelation`.`modified`, `TemplateCategory`.`id`, `TemplateCategory`.`title`, `TemplateCategory`.`status`, `TemplateCategory`.`cat_icon`, `TemplateCategory`.`created`, `TemplateCategory`.`modified` FROM `template_relations` AS `TemplateRelation` LEFT JOIN `template_categories` AS `TemplateCategory` ON (`TemplateRelation`.`template_category_id` = `TemplateCategory`.`id`)  WHERE `TemplateRelation`.`status` = 1 AND `TemplateRelation`.`rating` IS NOT NULL AND `TemplateRelation`.`rating` != 0 ' . $ratingConditions . ' GROUP BY `TemplateRelation`.`template_category_id` ');

				$template_categories_count = $this->TemplateRelation->query('SELECT count(TemplateRelation.id) as total, `TemplateRelation`.`id`, `TemplateRelation`.`type`, `TemplateRelation`.`user_id`, `TemplateRelation`.`thirdparty_id`, `TemplateRelation`.`template_id`, `TemplateRelation`.`template_category_id`, `TemplateRelation`.`rating`, `TemplateRelation`.`status`, `TemplateRelation`.`title`, `TemplateRelation`.`description`, `TemplateRelation`.`key_result_target`, `TemplateRelation`.`color_code`, `TemplateRelation`.`created`, `TemplateRelation`.`modified`, `TemplateCategory`.`id`, `TemplateCategory`.`title`, `TemplateCategory`.`status`, `TemplateCategory`.`cat_icon`, `TemplateCategory`.`created`, `TemplateCategory`.`modified` FROM `template_relations` AS `TemplateRelation` LEFT JOIN `template_categories` AS `TemplateCategory` ON (`TemplateRelation`.`template_category_id` = `TemplateCategory`.`id`)  WHERE `TemplateRelation`.`status` = 1 AND `TemplateRelation`.`rating` IS NOT NULL AND `TemplateRelation`.`rating` != 0 ' . $ratingConditions . ' group by TemplateRelation.template_category_id');

			} else {

				$template_categories = $this->TemplateCategory->find('all', [
					'conditions' => [
						'TemplateCategory.status' => 1,
					],
					'order' => '(CASE WHEN title="Other" THEN 1 ELSE 0 END), title ASC',
					'recursive' => -1,
				]);
			}

			$this->set('template_categories', $template_categories);
			if (isset($template_categories_count) && !empty($template_categories_count)) {
				$this->set('template_categories_count', $template_categories_count);
			}
			if (isset($this->request->data['project_id']) && !empty($this->request->data['project_id'])) {
				$this->set('projects_id', $this->request->data['project_id']);
			}
			$this->set('reviewrating', $starRating);
			$this->render(DS . 'Templates' . DS . 'partials' . DS . 'search_category');

			//pr($template_categories); die;

		}
	}

	public function jeera_dashboard() {
		$this->layout = 'inner';
		$actionname = $this->request->params['action'];

		$crumb = [
			'Templates' => [
				'data' => [
					'url' => '/templates/create_workspace/0',
					'class' => 'tipText',
					'title' => 'Select Templates',
					'data-original-title' => 'Templates',
				],
			],
			'last' => ['OpusView Dashboard'],
		];

		$refer_url = '';
		$refer_url = Router::url($this->referer(), true);
		$this->set('refer_url', $refer_url);
		/*

			if( isset($refer_url) ){

				$crumb = [
					'Templates' => [
						'data' => [
							'url' => $refer_url ,
							'class' => 'tipText',
							'title' => 'Select Templates',
							'data-original-title' => 'Templates',
						]
					],
					'last' => [ 'Jeera Dashboard' ]
				];

		*/

		if ($this->Session->read('Auth.User.role_id') != 1) {
			$this->redirect(SITEURL . 'dashboards/project_center');
		}

		$title_for_layout = __('OpusView Dashboard', true);
		$this->set('title_for_layout', $title_for_layout);

		$data['page_heading'] = __('OpusView Dashboard', true);
		$data['page_subheading'] = __('OpusView Dashboard', true);
		$this->set('data', $data);

		$per_page_show = 10;
		$orConditions = array();
		$in = 0;
		$finalConditions = array('TemplateRelation.type' => 2, 'TemplateRelation.status' => 1);

		if (isset($this->data['TemplateRelation']['template_relation_id'])) {
			$category_id = trim($this->data['TemplateRelation']['template_relation_id']);
		} else {
			$category_id = $this->Session->read('TemplateRelation.template_relation_id');
		}

		if (isset($category_id)) {
			$this->Session->write('TemplateRelation.template_relation_id', $category_id);
			$category_ids = explode(" ", $category_id);
			if (isset($category_ids[0]) && !empty($category_ids[0]) && count($category_ids) < 2) {
				$category_id = $category_ids[0];
				$in = 1;
				$orConditions = array('OR' => array(
					'TemplateRelation.template_category_id' => $category_id,
				));
			}
		}

		if (!empty($orConditions)) {
			$finalConditions = array_merge($finalConditions, $orConditions);
		}

		$this->TemplateRelation->unbindModel(array('hasMany' => array('AreaRelation', 'TemplateLike', 'TemplateReview')), false);
		$this->TemplateRelation->unbindModel(array('belongsTo' => array('User')), false);

		//$templateData = $this->TemplateRelation->find('all', array('conditions'=>$finalConditions,'order'=>array('TemplateRelation.id DESC')) );
		//$this->set('templateData', $templateData);

		$count = $this->TemplateRelation->find('count', array('conditions' => $finalConditions));
		$this->set('count', $count);

		$this->Session->write('template.per_page_show', $per_page_show);
		$this->TemplateRelation->recursive = 1;
		$this->paginate = array('conditions' => $finalConditions, "limit" => $per_page_show, "order" => array('TemplateRelation.id DESC'));

		$this->set('crumb', $crumb);
		$this->set('templateData', $this->paginate('TemplateRelation'));
		$this->set('in', $in);

	}

	public function delete_template($template_id = null) {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$data = $response = null;
			$this->layout = false;

			if (isset($template_id) && !empty($template_id)) {

				$this->TemplateRelation->id = $template_id;

				if ($this->TemplateRelation->delete()) {
					die('success');

				} else {
					die('error');
				}

			}

		}
	}

	public function jeera_dashboard_resetfilter() {
		$this->Session->write('TemplateRelation.template_relation_id', '');
		$this->redirect(array('action' => 'jeera_dashboard'));
	}

	public function thirdparty_dashboard() {
		$this->layout = 'inner';

		$actionname = $this->request->params['action'];

		$crumb = [
			'Templates' => [
				'data' => [
					'url' => '/templates/create_workspace/0',
					'class' => 'tipText',
					'title' => 'Select Templates',
					'data-original-title' => 'Templates',
				],
			],
			'last' => ['Third Party Dashboard'],
		];

		$refer_url = '';
		$refer_url = Router::url($this->referer(), true);
		$this->set('refer_url', $refer_url);
		/*
			if(isset($refer_url)){
				$crumb = [
				'Templates' => [
					'data' => [
						'url' => $refer_url ,
						'class' => 'tipText',
						'title' => 'Select Templates',
						'data-original-title' => 'Templates',
						]
					],
					'last' => ['Third Party Dashboard']
				];
		*/

		if ($this->Session->read('Auth.User.role_id') != 1) {
			$this->redirect(SITEURL . 'dashboards/project_center');
		}

		$title_for_layout = __('Third Party Dashboard', true);
		$this->set('title_for_layout', $title_for_layout);

		$data['page_heading'] = __('Third Party Dashboard', true);
		$data['page_subheading'] = __('Third Party Dashboard', true);
		$this->set('data', $data);

		$per_page_show = 10;
		$orConditions = array();
		$orSecConditions = array();
		$in = 0;
		$finalConditions = array('TemplateRelation.type' => 3, 'TemplateRelation.status' => 1);

		//pr($this->data); die;

		if (isset($this->data['ThirdTemplateRelation']['thirdparty_id'])) {
			$thirdparty_id = trim($this->data['ThirdTemplateRelation']['thirdparty_id']);
		} else {
			$thirdparty_id = $this->Session->read('ThirdTemplateRelation.thirdparty_id');
		}

		if (isset($thirdparty_id)) {
			$this->Session->write('ThirdTemplateRelation.thirdparty_id', $thirdparty_id);
			$thirdparty_ids = explode(" ", $thirdparty_id);
			if (isset($thirdparty_ids[0]) && !empty($thirdparty_ids[0]) && count($thirdparty_ids) < 2) {
				$thirdparty_id = $thirdparty_ids[0];
				$in = 1;
				$orSecConditions = array('TemplateRelation.thirdparty_id' => $thirdparty_id);
			}
		}

		if (isset($this->data['ThirdTemplateRelation']['template_relation_id'])) {
			$category_id = trim($this->data['ThirdTemplateRelation']['template_relation_id']);
		} else {
			$category_id = $this->Session->read('ThirdTemplateRelation.template_relation_id');
		}

		if (isset($category_id)) {
			$this->Session->write('ThirdTemplateRelation.template_relation_id', $category_id);
			$category_ids = explode(" ", $category_id);
			if (isset($category_ids[0]) && !empty($category_ids[0]) && count($category_ids) < 2) {
				$category_id = $category_ids[0];
				$in = 1;
				$orConditions = array('TemplateRelation.template_category_id' => $category_id);
			}
		}

		if (!empty($orConditions) || !empty($orSecConditions)) {
			$finalConditions = array_merge($finalConditions, $orSecConditions, $orConditions);
		}

		//pr($finalConditions); die;

		$this->TemplateRelation->unbindModel(array('hasMany' => array('AreaRelation', 'TemplateLike', 'TemplateReview')), false);
		$this->TemplateRelation->unbindModel(array('belongsTo' => array('User')), false);

		$count = $this->TemplateRelation->find('count', array('conditions' => $finalConditions));
		$this->set('count', $count);

		$this->Session->write('thirdtemplate.per_page_show', $per_page_show);
		$this->TemplateRelation->recursive = 1;
		$this->paginate = array('conditions' => $finalConditions, "limit" => $per_page_show, "order" => array('TemplateRelation.id DESC'));

		$this->set('crumb', $crumb);
		$this->set('templateData', $this->paginate('TemplateRelation'));
		$this->set('in', $in);
	}

	public function delete_thirdparty_template($template_id = null) {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$data = $response = null;
			$this->layout = false;

			if (isset($template_id) && !empty($template_id)) {

				$this->TemplateRelation->id = $template_id;

				if ($this->TemplateRelation->delete()) {
					die('success');

				} else {
					die('error');
				}

			}

		}
	}

	public function thirdparty_dashboard_resetfilter() {
		$this->Session->write('ThirdTemplateRelation.template_relation_id', '');
		$this->Session->write('ThirdTemplateRelation.thirdparty_id', '');
		$this->redirect(array('action' => 'thirdparty_dashboard'));
	}

	public function search_template() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$data = $response = null;
			$response = ['content' => '', 'success' => false, 'template_count' => 0];
			$keyword = null;
			$starRating = array();
			$template_type = null;
			$this->pagination['limit'] = 1;
			$columnWidth = 0;

			$ratesArr = [];

			//$this->pagination['show_summary'] = true;
			//$this->pagination['model'] = 'TemplateRelation';
			$templateratingPaging = [];

			if (isset($this->request->data) && !empty($this->request->data['starRating'])) {
				$starRating = explode(',', $this->request->data['starRating']);
			}

			if (isset($this->request->data) && !empty($this->request->data['template_category_id'])) {
				$template_category_id = $this->request->data['template_category_id'];
			}

			if (isset($this->request->data) && !empty($this->request->data['searchdatatype'])) {
				$template_type = ' AND TemplateRelation.type =' . $this->request->data['searchdatatype'];
			}

			if (isset($this->request->data) && !empty($this->request->data['columnWidth'])) {
				$columnWidth = $this->request->data['columnWidth'];
			}
			if (isset($this->request->data) && !empty($this->request->data['columnwidth'])) {
				$columnWidth = $this->request->data['columnwidth'];
			}

			if (isset($this->request->data) && !empty($this->request->data['keyword'])) {
				$keyword = $this->request->data['keyword'];
			}

			$project_id = $this->request->data['project_id'];

			$ratingConditions = null;
			if (isset($starRating) && !empty($starRating)) {

				$ratingConditions = 'AND ( ';

				foreach ($starRating as $ratingValue) {
					//  = array(
					// ('TemplateRelation'.'rating' >= '0.5' AND 'TemplateRelation'.'rating' <= '1.4')
					// );

					if ($ratingValue == '1') {
						$ratingConditions .= '(TemplateRelation.rating >= 0.5 AND TemplateRelation.rating  <= 1.4)';
					}

					if ($ratingValue == '2') {
						if (isset($ratingConditions) && !empty($ratingConditions) && $ratingConditions != 'AND ( ') {
							$ratingConditions .= ' OR ';
						}
						$ratingConditions .= '(TemplateRelation.rating >= 1.5 AND TemplateRelation.rating  <= 2.4)';
					}

					if ($ratingValue == '3') {
						if (isset($ratingConditions) && !empty($ratingConditions) && $ratingConditions != 'AND ( ') {
							$ratingConditions .= ' OR ';
						}
						$ratingConditions .= ' (TemplateRelation.rating >= 2.5 AND TemplateRelation.rating  <= 3.4)';
					}

					if ($ratingValue == '4') {
						if (isset($ratingConditions) && !empty($ratingConditions) && $ratingConditions != 'AND ( ') {
							$ratingConditions .= ' OR ';
						}
						$ratingConditions .= ' (TemplateRelation.rating >= 3.5 AND TemplateRelation.rating  <= 4.4)';
					}

					if ($ratingValue == '5') {
						if (isset($ratingConditions) && !empty($ratingConditions) && $ratingConditions != 'AND ( ') {
							$ratingConditions .= ' OR ';
						}
						$ratingConditions .= ' (TemplateRelation.rating >= 4.5 AND TemplateRelation.rating  <= 5)';
					}
				}

				$ratingConditions .= ' ) ';
				//$ratingConditions = array_merge($ratingConditions,$ratingConditions1,$ratingConditions2,$ratingConditions3,$ratingConditions4,$ratingConditions5);

			}
			$user_templates = null;

			//pr($this->request->data); die;

			if (isset($ratingConditions) && !empty($ratingConditions)) {
				// $user_templates = $this->TemplateRelation->query('SELECT   `TemplateRelation`.`id`, `TemplateRelation`.`type`, 	`TemplateRelation`.`user_id`, `TemplateRelation`.`thirdparty_id`, `TemplateRelation`.`template_id`, `TemplateRelation`.`template_category_id`, `TemplateRelation`.`rating`, `TemplateRelation`.`status`, `TemplateRelation`.`title`, `TemplateRelation`.`description`, `TemplateRelation`.`key_result_target`, `TemplateRelation`.`color_code`, `TemplateRelation`.`created`, `TemplateRelation`.`modified`, `TemplateCategory`.`id`, `TemplateCategory`.`title`, `TemplateCategory`.`status`, `TemplateCategory`.`created`, `TemplateCategory`.`modified` FROM `template_relations` AS `TemplateRelation` LEFT JOIN `template_categories` AS `TemplateCategory` ON (`TemplateRelation`.`template_category_id` = `TemplateCategory`.`id`)  WHERE `TemplateRelation`.`status` = 1 AND TemplateRelation.template_category_id = '.$template_category_id. '  order by TemplateRelation.title ASC');

				$user_templates = $this->TemplateRelation->query('SELECT   `TemplateRelation`.`id`, `TemplateRelation`.`type`,`TemplateRelation`.`wsp_imported`, 	`TemplateRelation`.`user_id`, `TemplateRelation`.`thirdparty_id`, `TemplateRelation`.`template_id`, `TemplateRelation`.`template_category_id`, `TemplateRelation`.`rating`, `TemplateRelation`.`status`, `TemplateRelation`.`title`, `TemplateRelation`.`description`, `TemplateRelation`.`key_result_target`, `TemplateRelation`.`color_code`, `TemplateRelation`.`created`, `TemplateRelation`.`modified`, `TemplateCategory`.`id`, `TemplateCategory`.`title`, `TemplateCategory`.`status`, `TemplateCategory`.`created`, `TemplateCategory`.`modified` FROM `template_relations` AS `TemplateRelation` LEFT JOIN `template_categories` AS `TemplateCategory` ON (`TemplateRelation`.`template_category_id` = `TemplateCategory`.`id`)  WHERE `TemplateRelation`.`status` = 1 AND TemplateRelation.template_category_id = ' . $template_category_id . ' ' . $template_type . ' AND `TemplateRelation`.`rating` IS NOT NULL AND `TemplateRelation`.`rating` != 0 ' . $ratingConditions . '  order by TemplateRelation.title ASC');

			} else if (isset($keyword) && !empty($keyword)) {

				$ser = '^';

				$keyword = Sanitize::escape(like($keyword, $ser ));

				$trPageCount = $this->TemplateRelation->query('SELECT `TemplateRelation`.`id`, `TemplateRelation`.`wsp_imported`,`TemplateRelation`.`type`, `TemplateRelation`.`user_id`, `TemplateRelation`.`thirdparty_id`, `TemplateRelation`.`template_id`, `TemplateRelation`.`template_category_id`, `TemplateRelation`.`rating`, `TemplateRelation`.`status`, `TemplateRelation`.`title`, `TemplateRelation`.`description`, `TemplateRelation`.`key_result_target`, `TemplateRelation`.`color_code`, `TemplateRelation`.`created`, `TemplateRelation`.`modified`, `TemplateCategory`.`id`, `TemplateCategory`.`title`, `TemplateCategory`.`status`, `TemplateCategory`.`created`, `TemplateCategory`.`modified` FROM `template_relations` AS `TemplateRelation` LEFT JOIN `template_categories` AS `TemplateCategory` ON (`TemplateRelation`.`template_category_id` = `TemplateCategory`.`id`)  WHERE `TemplateRelation`.`status` = 1 AND TemplateRelation.template_category_id = ' . $template_category_id . ' AND  `TemplateRelation`.`title` like "%' . $keyword . '%" escape "'.$ser.'" ');


				$user_templates = $this->TemplateRelation->query('SELECT   `TemplateRelation`.`id`, `TemplateRelation`.`wsp_imported`,`TemplateRelation`.`type`, `TemplateRelation`.`user_id`, `TemplateRelation`.`thirdparty_id`, `TemplateRelation`.`template_id`, `TemplateRelation`.`template_category_id`, `TemplateRelation`.`rating`, `TemplateRelation`.`status`, `TemplateRelation`.`title`, `TemplateRelation`.`description`, `TemplateRelation`.`key_result_target`, `TemplateRelation`.`color_code`, `TemplateRelation`.`created`, `TemplateRelation`.`modified`, `TemplateCategory`.`id`, `TemplateCategory`.`title`, `TemplateCategory`.`status`, `TemplateCategory`.`created`, `TemplateCategory`.`modified` FROM `template_relations` AS `TemplateRelation` LEFT JOIN `template_categories` AS `TemplateCategory` ON (`TemplateRelation`.`template_category_id` = `TemplateCategory`.`id`)  WHERE `TemplateRelation`.`status` = 1 AND TemplateRelation.template_category_id = ' . $template_category_id . '	 AND  `TemplateRelation`.`title` like "%' . $keyword . '%"  escape "'.$ser.'"  order by TemplateRelation.title ASC  ');

			} else {

				/* paging for mindmap */
				$paginator = null;

				$this->request->data['searchdatatype'] = (isset($this->request->data['searchdatatype']) && !empty($this->request->data['searchdatatype'])) ? ($this->request->data['searchdatatype']) : 1;

				$trPageCount = $this->TemplateRelation->find('count', [
					'conditions' => array(
						'TemplateRelation.status' => 1,
						'TemplateRelation.template_category_id' => $template_category_id,
						'TemplateRelation.type' => $this->request->data['searchdatatype'],
					),
				]);

				/* GET USER CREATED TEMPLATES */
				$user_templates = $this->TemplateRelation->find('all', [
					'conditions' => [
						'TemplateRelation.status' => 1,
						'TemplateRelation.type' => 1,
						'TemplateRelation.template_category_id' => $template_category_id,
						'TemplateRelation.type' => $this->request->data['searchdatatype'],

					],

					"order" => ["TemplateRelation.title ASC"],
				]);

			}

		}

		// pr($paginatorCond); die;

		// $this->paginate = $paginatorCond;
		$this->set('user_templates', $user_templates);
		$this->set('project_id', $project_id);
		$this->set('columnWidth', $columnWidth);
		/* $this->set('template_paging', $templateratingPaging);
				$this->set('pageLimit', $this->pagination["limit"]);

				if(isset($trPageCount) && !empty($trPageCount) ){
					$this->set('trPageCount', count($trPageCount));
				}
				$response = ['template_count'=> count($trPageCount) ]; */

		//$this->set('responsew', $response);
		$this->render(DS . 'Templates' . DS . 'partials' . DS . 'search_template');

	}

	public function get_pagination() {
		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;

				$paging_links = (!empty($post)) ? jsPaginations($post) : "";
				if (!empty($paging_links)) {
					$response['success'] = TRUE; // Some might be empty
					// $response['js_pconfig'] = $paging_links["js_pconfig"];
					$response['output'] = $paging_links["output"];
				} else {
					$response['success'] = FALSE; // Some might be empty
				}
			}

			echo json_encode($response);
			exit;
		}
	}

	public function comment_uploads($do_list_id = null) {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$response = null;

			$response = [
				'success' => false,
				'content' => null,
			];

			if (isset($this->data['area']) && !empty($this->data['area'])) {

				$savearray = array();

				$response['success'] = true;
				foreach ($this->data['area'] as $k => $value) {
					$output_dir = TEMPLATE_DOCUMENTS;

					if (isset($value['element']) && !empty($value['element'])) {

						if (isset($value['element'][$do_list_id]['element_file']) && !empty($value['element'][$do_list_id]['element_file'])) {
							foreach ($value['element'][$do_list_id]['element_file'] as $ele => $val) {
								if (isset($val['name']) && !empty($val['name'])) {

									$new_value1 = str_replace('"','',$val['name']);
									$new_value = str_replace("'",'',$new_value1);

									$ext = pathinfo($val['name']);
									// $fileName = $this->unique_file_name($output_dir, $val['name']);
									$fileName = $this->unique_file_name($output_dir, $new_value);

									if (move_uploaded_file($val["tmp_name"], $output_dir . $fileName)) {
										$response['content'][] = $fileName;
									} else if (copy($val["tmp_name"], $output_dir . $fileName)) {
										$response['content'][] = $fileName;
									}
								}
							}
						}
					}
				}
			}
		}
		echo json_encode($response);
		exit;
	}

	public function download_template_doc($id = null) {
		if (isset($id) && !empty($id)) {
			$this->loadModel('ElementRelationDocument');
			// Retrieve the file ready for download
			$data = $this->ElementRelationDocument->findById($id);
			if (empty($data)) {
				throw new NotFoundException();
			}

			// Send file as response
			$upload_path = TEMPLATE_DOCUMENTS . DS . $data['ElementRelationDocument']['file_name'];

			$this->response->file($upload_path, array(
				'download' => true,
				'name' => $data['ElementRelationDocument']['file_name'],
			));
			return $this->response;
		}
	}

	public function unique_file_name($path, $filename) {
		// e($path.' '.$filename);
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
			$newname = $name . '_' . $counter . $ext;
			$newpath = $path . '/' . $newname;
			$counter++;
		}

		return $newname;
	}

/* **********************
 * COPY/MOVE TEMPLATES
 ************************/

	public function manage_templates($template_cat_id = null) {
		$response = ['success' => false];
		if ($this->request->isAjax()) {
			$this->layout = false;

			$viewVars = null;
			$viewVars['template_cat_id'] = $template_cat_id;

			$templates = $this->TemplateRelation->find('all', [
				'conditions' => [
					'TemplateRelation.template_category_id' => $template_cat_id,
					'TemplateRelation.type' => 1,
				],
				'recursive' => -1,
			]);
			$viewVars['templates'] = $templates;

			$category_detail = $this->TemplateCategory->find('first', [
				'conditions' => [
					'TemplateCategory.id' => $template_cat_id,
				],
				'fields' => ['id', 'title'],
				'recursive' => -1,
			]);
			$viewVars['category_detail'] = $category_detail;

			$template_categories = $this->TemplateCategory->find('list', [
				'conditions' => [
					'TemplateCategory.status' => 1,
					// 'TemplateCategory.id !=' => $template_cat_id,
				],
				'recursive' => -1,
				'order' => ['title ASC'],
			]);
			$viewVars['template_categories'] = $template_categories;

			$this->set($viewVars);
			$this->render('/Templates/partials/manage_templates');
		}
	}

	public function copy_move_templates($template_cat_id = null) {
		$response = [
			'success' => false,
			'content' => null,
		];
		if ($this->request->isAjax()) {
			$this->layout = false;

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				// pr(ELEMENT_DOCUMENT_PATH, 1);

				// SELECTED TEMPLATES
				$temp_relation = (isset($post['Template']['template_list']) && !empty($post['Template']['template_list'])) ? $post['Template']['template_list'] : null;
				// SELECTED DESTINATION
				$temp_cat = (isset($post['Template']['template_categories']) && !empty($post['Template']['template_categories'])) ? $post['Template']['template_categories'] : null;

				// 1 = move, 2 = copy
				$copy_move = (isset($post['Template']['option_copy_move']) && !empty($post['Template']['option_copy_move'])) ? $post['Template']['option_copy_move'] : null;

				// copy/move reviews, likes and docs or not
				$option_review = (isset($post['Template']['option_review']) && !empty($post['Template']['option_review'])) ? $post['Template']['option_review'] : null;
				$option_likes = (isset($post['Template']['option_likes']) && !empty($post['Template']['option_likes'])) ? $post['Template']['option_likes'] : null;
				$option_docs = (isset($post['Template']['option_dots']) && !empty($post['Template']['option_dots'])) ? $post['Template']['option_dots'] : null;

				// IF TEMPLATE AND DESTINATION FOLDER BOTH ARE EXISTS IN POST
				if (!empty($temp_relation) && !empty($temp_cat)) {
					foreach ($temp_relation as $key => $value) {
						$template_data = $this->TemplateRelation->find('first', [
							'conditions' => [
								'TemplateRelation.id' => $value,
							],
							'recursive' => -1,
						]);
						if ($copy_move == 1) {
							// move
							$copy_data['TemplateRelation'] = ['id' => $value, 'template_category_id' => $temp_cat];
							if ($this->TemplateRelation->save($copy_data)) {
								$response['success'] = true;
								if (!empty($option_review)) {
									$this->TemplateReview->deleteAll(['TemplateReview.template_relation_id' => $value]);
								}
								if (!empty($option_likes)) {
									$this->TemplateLike->deleteAll(['TemplateLike.template_relation_id' => $value]);
								}
								if (!empty($option_docs)) {
									$areas = $this->AreaRelation->find('all', [
										'conditions' => [
											'AreaRelation.template_relation_id' => $value,
										],
										'recursive' => -1,
									]);
									if (isset($areas) && !empty($areas)) {
										// loop through all areas
										foreach ($areas as $akey => $avalue) {
											$area_relation_id = $avalue['AreaRelation']['id'];
											$elements = $this->ElementRelation->find('all', [
												'conditions' => [
													'ElementRelation.area_relation_id' => $area_relation_id,
												],
												'recursive' => -1,
											]);
											if (isset($elements) && !empty($elements)) {
												// loop through elements and get all documents
												foreach ($elements as $ekey => $evalue) {
													$element_relation_id = $evalue['ElementRelation']['id'];
													$documents = $this->ElementRelationDocument->find('all', [
														'conditions' => [
															'ElementRelationDocument.element_relation_id' => $element_relation_id,
														],
														'recursive' => -1,
													]);
													if (isset($documents) && !empty($documents)) {
														// loop through all documents
														foreach ($documents as $dkey => $dvalue) {
															$doc_data = $dvalue['ElementRelationDocument'];
															$file_name = $doc_data['file_name'];
															// remove from database
															$this->ElementRelationDocument->deleteAll(['ElementRelationDocument.id' => $doc_data['id']]);
															// remove from physical storage
															$upload_path = WWW_ROOT . 'uploads/template_element_document' . DS . $file_name;
															if (file_exists($upload_path)) {
																unlink($upload_path);
															}
														}
													}
												}
											}
										}
									}
								}
							}
						} else if ($copy_move == 2) {
							// copy
							$row = $template_data;
							$this->TemplateRelation->id = null;
							unset($row['TemplateRelation']['id']);
							$title = $this->duplicate_check($row['TemplateRelation']['title'], $temp_cat);
							$row['TemplateRelation']['title'] = $title;
							// $row['TemplateRelation']['title'] = $row['TemplateRelation']['title'] . '#';
							$row['TemplateRelation']['template_category_id'] = $temp_cat;
							$row['TemplateRelation']['user_id'] = $this->user_id;

							if ($this->TemplateRelation->save($row)) {
								$response['success'] = true;
								$newInsertId = $this->TemplateRelation->getLastInsertId();

								// get all areas and save with associated relation id
								$areas = $this->AreaRelation->find('all', [
									'conditions' => [
										'AreaRelation.template_relation_id' => $value,
									],
									'recursive' => -1,
								]);

								if (isset($areas) && !empty($areas)) {

									// loop through all areas
									foreach ($areas as $akey => $avalue) {
										$adata = null;
										$area_data = $adata = $avalue['AreaRelation'];
										// pr($adata);
										$area_relation_id = $area_data['id'];
										$adata['template_relation_id'] = $newInsertId;
										unset($adata['id']);
										$this->AreaRelation->id = null;
										// pr($adata, 1);
										// save new area relation data
										// pr($this->AreaRelation->save($adata), 1);
										if ($this->AreaRelation->save($adata)) {
											$newAreaRelId = $this->AreaRelation->getLastInsertId();
											// get all elements of each area relation id
											$elements = $this->ElementRelation->find('all', [
												'conditions' => [
													'ElementRelation.area_relation_id' => $area_relation_id,
												],
												'recursive' => -1,
											]);
											if (isset($elements) && !empty($elements)) {
												$edata = null;
												// save all elements with new area relation id
												foreach ($elements as $ekey => $evalue) {
													$edata = $evalue['ElementRelation'];
													unset($edata['id']);
													$edata['area_relation_id'] = $newAreaRelId;
													$this->ElementRelation->id = null;
													if ($this->ElementRelation->save($edata)) {
														// if user want to copy documents also
														if (empty($option_docs)) {
															// Get newly created element relation id
															$newElementRelId = $this->ElementRelation->getLastInsertId();
															// source element relation id
															$element_relation_id = $evalue['ElementRelation']['id'];
															$documents = $this->ElementRelationDocument->find('all', [
																'conditions' => [
																	'ElementRelationDocument.element_relation_id' => $element_relation_id,
																],
																'recursive' => -1,
															]);
															if (isset($documents) && !empty($documents)) {
																// loop through all documents
																foreach ($documents as $dkey => $dvalue) {
																	$doc_data = null;
																	$doc_data = $dvalue['ElementRelationDocument'];
																	unset($doc_data['id']);
																	$file_name = $doc_data['file_name'];

																	// Get new name, change if already exists
																	$path = WWW_ROOT . 'uploads/template_element_document';
																	$new_file_name = $this->file_newname($path, $file_name);

																	$old_file_path = WWW_ROOT . 'uploads/template_element_document' . DS . $file_name;
																	$new_file_path = WWW_ROOT . 'uploads/template_element_document' . DS . $new_file_name;

																	$doc_data['file_name'] = $new_file_name;
																	$doc_data['element_relation_id'] = $newElementRelId;
																	// copy document
																	if (copy($old_file_path, $new_file_path)) {
																		// save with new name
																		$this->ElementRelationDocument->id = null;
																		$this->ElementRelationDocument->save($doc_data);
																	}
																}
															}
														}
													}
												}
											}
										} // end area saving
									} // end area relation data iteration

								}

								if (empty($option_review)) {
									// get all reviews and save with associated relation id
									$reviews = $this->TemplateReview->find('all', [
										'conditions' => [
											'TemplateReview.template_relation_id' => $value,
										],
										'recursive' => -1,
									]);
									if (isset($reviews) && !empty($reviews)) {
										$rdata = null;
										foreach ($reviews as $rkey => $rvalue) {
											$rdata['TemplateReview'][$rkey] = [
												'template_relation_id' => $newInsertId,
												'user_id' => $this->user_id,
												'comments' => $rvalue['TemplateReview']['comments'],
												'rating' => $rvalue['TemplateReview']['rating'],
												'used_unused' => $rvalue['TemplateReview']['used_unused'],
											];
										}
										$this->TemplateReview->saveAll($rdata['TemplateReview']);
									}
								}
								if (empty($option_likes)) {
									// get all likes and save with associated relation id
									$likes = $this->TemplateLike->find('all', [
										'conditions' => [
											'TemplateLike.template_relation_id' => $value,
										],
										'recursive' => -1,
									]);
									if (isset($likes) && !empty($likes)) {
										$ldata = null;
										foreach ($likes as $rkey => $lvalue) {
											$ldata['TemplateLike'][$rkey] = [
												'template_relation_id' => $newInsertId,
												'user_id' => $this->user_id,
												'like_unlike' => $lvalue['TemplateLike']['like_unlike'],
											];
										}
										$this->TemplateLike->saveAll($ldata['TemplateLike']);
									}
								}
							}
						}
					}
				}
			}
			echo json_encode($response);
			exit;
		}
	}

	function duplicate_check($name = '', $template_cat_id = null) {

		$title = $name;
		$i = 0;
		do {
			//Check in the database here
			$exists = $this->TemplateRelation->find('count', [
				'conditions' => [
					'TemplateRelation.title' => $name,
					'TemplateRelation.template_category_id' => $template_cat_id,
				],
				'recursive' => -1,
			]);
			// if exists in database assign a new name
			if ($exists) {
				$i++;
				$name = htmlentities($title, ENT_QUOTES) . '#' . $i;
			}
		} while ($exists);
		return $name;
	}

	function file_newname($path, $filename) {
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
			$newname = $name . '_' . $counter . $ext;
			$newpath = $path . '/' . $newname;
			$counter++;
		}

		return $newname;
	}

/* **********************
 * SELECT MULTIPLE TEMPLATES TO PROJECT
 ************************/
	public function multi_templates() {
		$response = ['success' => false];
		if ($this->request->isAjax()) {
			$this->layout = false;
			$project_id = $template_cat_id = $type = null;
			$viewVars = null;

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				// pr($post, 1);
				$project_id = $viewVars['project_id'] = (isset($post['project_id']) && !empty($post['project_id'])) ? $post['project_id'] : null;
				$template_cat_id = $viewVars['template_cat_id'] = (isset($post['template_cat_id']) && !empty($post['template_cat_id'])) ? $post['template_cat_id'] : null;
				$type = $viewVars['type'] = (isset($post['type']) && !empty($post['type'])) ? $post['type'] : null;

				$templates = $this->TemplateRelation->find('all', [
					'conditions' => [
						'TemplateRelation.template_category_id' => $template_cat_id,
						'TemplateRelation.type' => $type,
					],
					'recursive' => -1,
				]);
				$viewVars['templates'] = $templates;

				$project_detail = $this->Project->find('first', [
					'conditions' => [
						'Project.id' => $project_id,
					],
					'fields' => ['id', 'title'],
					'recursive' => -1,
				]);
				$viewVars['project_detail'] = $project_detail;

				$category_detail = $this->TemplateCategory->find('first', [
					'conditions' => [
						'TemplateCategory.id' => $template_cat_id,
					],
					'fields' => ['id', 'title'],
					'recursive' => -1,
				]);
				$viewVars['category_detail'] = $category_detail;

				$template_categories = $this->TemplateCategory->find('list', [
					'conditions' => [
						'TemplateCategory.status' => 1,
					],
					'recursive' => -1,
					'order' => ['title ASC'],
				]);
				$viewVars['project_id'] = $project_id;
				$viewVars['template_categories'] = $template_categories;
			}

			$this->set($viewVars);
			$this->render('/Templates/partials/multi_templates');
		}
	}

	public function select_multi_template() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$response = [
				'success' => false,
				'msg' => null,
				'content' => null,
			];
			$pwcounter = 0;
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				// pr($post, 1);

				$template_list = $post['Template']['template_list'];
				$project_id = $post['Template']['project_id'];
				foreach ($template_list as $tkey => $tval) {
					$template_id = $tval;
					$templateData = $this->TemplateRelation->find('first', ['conditions' => ['TemplateRelation.id' => $template_id]]);

					if (isset($templateData) && !empty($templateData)) {
						$templateDetail = $templateData['TemplateRelation'];
						if (!isset($templateDetail['key_result_target']) || empty($templateDetail['key_result_target'])) {
							//$templateDetail['key_result_target'] = 'This is your Key Result Target';
						}

						$workspaceData['Workspace'] = [
							'template_relation_id' => $templateDetail['id'],
							'title' => $templateDetail['title'],
							'description' => $templateDetail['description'],
							'template_id' => $templateDetail['template_id'],
							'color_code' => $templateDetail['color_code'],
							'updated_user_id' => $this->user_id,
							'created_by' => $this->user_id,
							'status' => 1,
							'studio_status' => 0,
							'sign_off' => 0,
						];

						$this->Workspace->id = null;
						// SAVE WORKSPACE
						if ($this->Workspace->save($workspaceData, false)) {

							$workspace_id = $this->Workspace->getLastInsertId();
							$response['success'] = true;
							$response['content'] = $workspace_id;

							// GET SORT ORDER OF PREVIOUS WORKSPACES FROM THE SELECTED PROJECT
							if (empty($pwcounter)) {
								$maximum = $this->ProjectWorkspace->query('SELECT MAX(sort_order) as maximum FROM project_workspaces WHERE project_id = "' . $project_id . '"');
								$max_sort = (isset($maximum[0][0]['maximum'])) ? ($maximum[0][0]['maximum'] + 1) : 1;
								$pwcounter = $max_sort;
							} else {
								$pwcounter++;
							}

							$response['msg'][] = $pwcounter;
							// AND ASSIGN NEXT ORDER TO NEWLY CREATED WORKSPACE IN PROJECT-WORKSPACE TABLE
							$this->ProjectWorkspace->id = null;
							$pwdata['ProjectWorkspace'] = ['project_id' => $project_id, 'workspace_id' => $workspace_id, 'sort_order' => $pwcounter, 'leftbar_status' => 1];
							$this->ProjectWorkspace->save($pwdata);
							$project_workspace_id = $this->ProjectWorkspace->getLastInsertId();

							// INSERT ENTRY IN WORKSPACE-PERMISSION TABLE WITH FULL PERMISSIONS
							$this->loadModel('WorkspacePermission');
							$this->WorkspacePermission->id = null;

							$wsp_permit['WorkspacePermission']['user_id'] = $this->user_id;
							$wsp_permit['WorkspacePermission']['user_project_id'] = project_upid($project_id);
							$wsp_permit['WorkspacePermission']['project_workspace_id'] = $project_workspace_id;
							$wsp_permit['WorkspacePermission']['permit_read'] = 1;
							$wsp_permit['WorkspacePermission']['permit_add'] = 1;
							$wsp_permit['WorkspacePermission']['permit_edit'] = 1;
							$wsp_permit['WorkspacePermission']['permit_delete'] = 1;
							$wsp_permit['WorkspacePermission']['permit_copy'] = 1;
							$wsp_permit['WorkspacePermission']['permit_move'] = 1;
							$wsp_permit['WorkspacePermission']['is_editable'] = 1;

							$this->WorkspacePermission->save($wsp_permit);

							// $this->Common->projectModified($project_id, $this->user_id);

							// GET ALL AREA FROM TEMPLATE AND ASSIGN THEM WITH THE WORKSPACE
							if (isset($templateData['AreaRelation']) && !empty($templateData['AreaRelation'])) {
								foreach ($templateData['AreaRelation'] as $akey => $aval) {
									$areaDetail = $aval;

									$areaData['Area'] = [
										'title' => $areaDetail['title'],
										'description' => $areaDetail['description'],
										'tooltip_text' => $areaDetail['description'],
										'workspace_id' => $workspace_id,
										'template_detail_id' => $areaDetail['template_detail_id'],
										'status' => 1,
										'studio_status' => 0,
									];
									// SAVE AREA
									$this->Area->id = null;
									if ($this->Area->save($areaData)) {

										$area_id = $this->Area->getLastInsertId();

										$elementData = relational_elements($areaDetail['id'], false);
										if (isset($elementData) && !empty($elementData)) {

											foreach ($elementData as $ekey => $eval) {
												$elementDetail = $eval['ElementRelation'];
												$elData['Element'] = [
													'area_id' => $area_id,
													'updated_user_id' => $this->user_id,
													'created_by' => $this->user_id,
													'title' => $elementDetail['title'],
													'description' => $elementDetail['description'],
													'comments' => '',
													'color_code' => $elementDetail['color_code'],
													'date_constraints' => 0,
													'status' => 1,
													'studio_status' => 0,
													'sort_order' => ($ekey + 1),
												];

												$this->Element->id = null;
												if ($this->Element->save($elData, false)) {

													$element_id = $this->Element->getLastInsertId();

													// INSERT ENTRY IN ELEMENT-DOCUMENT TABLE FROM ELEMENT-RELATION-DOCUMENT TABLE RELATED TO THIS ELEMENT
													$elementDocData = $eval['ElementRelationDocument'];
													$this->loadModel('ElementRelationDocument');
													foreach ($elementDocData as $keyDoc => $valDoc) {
														$valDocData = $valDoc;
														// pr($valDoc, 1);
														$this->ElementDocument->id = null;
														$el_doc['ElementDocument']['element_id'] = $element_id;
														$el_doc['ElementDocument']['creater_id'] = $this->user_id;
														$el_doc['ElementDocument']['updated_user_id'] = $this->user_id;
														$el_doc['ElementDocument']['title'] = $valDocData['title'];
														$el_doc['ElementDocument']['file_name'] = $valDocData['file_name'];
														$el_doc['ElementDocument']['file_size'] = $valDocData['file_size'];
														$el_doc['ElementDocument']['file_type'] = $valDocData['file_type'];
														$el_doc['ElementDocument']['status'] = $valDocData['status'];
														$el_doc['ElementDocument']['is_search'] = 0;
														if ($this->ElementDocument->save($el_doc)) {
															$tmplFile = TEMPLATE_DOCUMENTS . $valDocData['file_name'];
															$eleDocFile = ELEMENT_DOCUMENT_PATH . $element_id . '/' . $valDocData['file_name'];
															if (!file_exists(ELEMENT_DOCUMENT_PATH . $element_id)) {
																mkdir(ELEMENT_DOCUMENT_PATH . $element_id, 0777, true);
															}
															copy($tmplFile, $eleDocFile);
														}
													}

													// INSERT ENTRY IN ELEMENT-PERMISSION TABLE WITH FULL PERMISSIONS
													$this->loadModel('ElementPermission');
													$el_permit['ElementPermission']['user_id'] = $this->user_id; //$this->Session->read('Auth.User.id');//
													$el_permit['ElementPermission']['element_id'] = $element_id;
													$el_permit['ElementPermission']['project_id'] = $project_id;
													$el_permit['ElementPermission']['workspace_id'] = $workspace_id;
													$el_permit['ElementPermission']['permit_read'] = 1;
													$el_permit['ElementPermission']['permit_add'] = 1;
													$el_permit['ElementPermission']['permit_edit'] = 1;
													$el_permit['ElementPermission']['permit_delete'] = 1;
													$el_permit['ElementPermission']['permit_copy'] = 1;
													$el_permit['ElementPermission']['permit_move'] = 1;
													$el_permit['ElementPermission']['is_editable'] = 1;
													$this->ElementPermission->save($el_permit);
													$this->ElementPermission->id = null;
													// pr($this->ElementPermission->save($el_permit), 1);

													// $this->update_project_modify($element_id, $project_id);
												}
											}

										}

									}
								}
							}

						}
					}
				}

			}
			echo json_encode($response);
			exit;

		}
	}

	public function convertwstemplate() {

		/* =========================Owner level project lists============== */

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$response = [
				'success' => false,
				'msg' => '',
				'projects' => null,
			];

			$m_projects = array();
			$r_projects = array();
			$g_projects = array();
			$all_users = [];

			$this->Project->unbindModel(['hasMany' => ['ProjectPermission']]);
			$projects = $this->Project->UserProject->find('list', array('conditions' => ['UserProject.user_id' => $this->user_id, 'UserProject.owner_user' => 1], 'recursive' => 1, 'fields' => ['Project.id', 'Project.title']));

			$m_projects = (isset($projects) && !empty($projects)) ? $projects : array();
			$all_users = $all_users + $m_projects;

			$rec_projects = get_rec_projects($this->user_id, 1);
			$r_projects = (isset($rec_projects) && !empty($rec_projects)) ? $rec_projects : array();
			$all_users = $all_users + $r_projects;

			$grp_projects = group_rec_projects($this->user_id, 1);
			$g_projects = (isset($grp_projects) && !empty($grp_projects)) ? $grp_projects : array();
			$all_users = $all_users + $g_projects;

			/* GET TEMPLATES CATEGORIES */
			$template_categories = $this->TemplateCategory->find('all', [
				'conditions' => [
					'TemplateCategory.status' => 1,
				],
				'order' => '(CASE WHEN title="Other" THEN 1 ELSE 0 END), title ASC',
				'recursive' => -1,
			]);

			$this->set('ownerprojects', array_filter($all_users));
			$this->set('template_categories', $template_categories);

			$this->render(DS . 'Templates' . DS . 'convertwstemplate');

		}
		/* =================================================================== */

	}

	public function get_workspaces() {

		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			$this->autoRender = false;

			if ($this->request->is('post') || $this->request->is('put')) {

				// pr($this->request->data); die;

				$post = $this->request->data;
				$project_id = $post['project_id'];

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
				}

				if (isset($pr_permission) && !empty($pr_permission)) {
					$ws_permission = $this->Common->work_permission_details($project_id, $user_id);
				}

				if ((!empty($us_permission)) || (isset($pr_permission['ProjectPermission']['project_level']) && $pr_permission['ProjectPermission']['project_level'] == 1) || (((isset($ws_permission) && !empty($ws_permission))))) {

					if (!empty($us_permission) || (isset($pr_permission['ProjectPermission']['project_level']) && $pr_permission['ProjectPermission']['project_level'] == 1)) {

						$projectWorkspaces = $this->Project->ProjectWorkspace->find('all', ['conditions' => ['Workspace.id !=' => '', 'ProjectWorkspace.project_id' => $project_id, 'ProjectWorkspace.leftbar_status' => 1], 'fields' => ['ProjectWorkspace.project_id', 'Workspace.id', 'Workspace.title', 'ProjectWorkspace.sort_order'], 'order' => ['ProjectWorkspace.project_id ASC', 'ProjectWorkspace.sort_order ASC']]);

					} else if (((isset($ws_permission) && !empty($ws_permission)))) {

						$projectWorkspaces = $this->Project->ProjectWorkspace->find('all', ['conditions' => ['ProjectWorkspace.project_id' => $project_id, 'ProjectWorkspace.leftbar_status' => 1, 'Workspace.id !=' => '', 'ProjectWorkspace.id' => $ws_permission], 'fields' => ['ProjectWorkspace.project_id', 'Workspace.id', 'Workspace.title', 'ProjectWorkspace.sort_order'], 'order' => ['ProjectWorkspace.project_id ASC', 'ProjectWorkspace.sort_order ASC']]);
					}
				}
				//pr($projectWorkspaces,1);
				if (isset($projectWorkspaces) && !empty($projectWorkspaces)) {
					$list = [];
					foreach ($projectWorkspaces as $key => $v) {
						$wtitle = '';

						$sortOrder = $v['ProjectWorkspace']['sort_order'];
						$wtitle = strip_tags($v['Workspace']['title']);
						$list[] = array('title' => $wtitle, 'id' => $v['Workspace']['id']);
					}
					$returnHtml = "<option value='0'>Select Workspace</option>";
					if (isset($list) && !empty($list)) {
						//$returnHtml = "<option value='0'>Select Workspace</option>";
						foreach ($list as $wplists) {
							$returnHtml .= "<option value=" . $wplists['id'] . ">" . $wplists['title'] . "</option>";
						}
					}
					return $returnHtml;
				} else {
					$returnHtml = "<option value='0'>Select Workspace</option>";
					return $returnHtml;
				}
			}
		}
	}

	public function workspace_template() {

		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			$this->autoRender = false;

			$response = [
				'success' => false,
				'content' => null,
				'type' => '',
			];

			if ($this->request->is('post') || $this->request->is('put')) {

				$user_id = $this->Auth->user('id');
				$post = $this->request->data;
				//pr($post); die;
				$project_id = $post['project_id'];
				$workspace_id = $post['workspace_id'];
				$template_category_ids = explode(",", $post['destination_id']);

				$wpdata = $this->Workspace->find('first', array(
					'conditions' => array('Workspace.id' => $workspace_id),
				));

				$wpsArea = $this->Area->query("SELECT * FROM `areas` WHERE workspace_id = $workspace_id  and studio_status !=1 ORDER BY CASE `is_standby` WHEN '0' THEN `sort_order` WHEN '1' THEN id ELSE 1 END ASC");

				// pr($wpsArea,1);

				if (isset($template_category_ids) && !empty($template_category_ids)) {

					foreach ($template_category_ids as $key => $template_category_id) {

						/* data inserted into Template Relations table */
						$this->request->data['TemplateRelation']['id'] = '';
						$this->request->data['TemplateRelation']['type'] = 1;
						$this->request->data['TemplateRelation']['template_id'] = $wpdata['Workspace']['template_id'];
						$this->request->data['TemplateRelation']['thirdparty_id'] = 0;
						$this->request->data['TemplateRelation']['user_id'] = $user_id;
						$this->request->data['TemplateRelation']['template_category_id'] = $template_category_id;
						$this->request->data['TemplateRelation']['title'] = strip_tags(str_replace("<br />", " ", str_replace("<br>", " ", $post['template_title'])));
						$this->request->data['TemplateRelation']['color_code'] = $wpdata['Workspace']['color_code'];
						$this->request->data['TemplateRelation']['description'] = strip_tags(str_replace("<br />", " ", str_replace("<br>", " ", $post['template_description'])));
						//$this->request->data['TemplateRelation']['key_result_target'] = 'This is your Key Result Target';
						$this->request->data['TemplateRelation']['key_result_target'] = $post['template_description'];
						$this->request->data['TemplateRelation']['is_search'] = 1;
						$this->request->data['TemplateRelation']['status'] = 1;
						if (isset($post['wsp_imported']) && $post['wsp_imported'] == 'on') {
							$this->request->data['TemplateRelation']['wsp_imported'] = 1;
						}

						/* Template save process start */
						if ($this->TemplateRelation->save($this->request->data)) {

							$TemplateRelationID = $this->TemplateRelation->getLastInsertId();

							/* Get Template relation id from area table and insert other details through request loop into area realtion table. */
							if (isset($wpsArea) && !empty($wpsArea)) {

								/* data inserted into Area Relation table  */
								// foreach ($wpdata['Area'] as $wsareas) {
								foreach ($wpsArea as $wspareall) {

									$wsareas = $wspareall['areas'];

									$this->request->data['AreaRelation']['id'] = '';
									$this->request->data['AreaRelation']['template_relation_id'] = $TemplateRelationID;
									$this->request->data['AreaRelation']['template_detail_id'] = $wsareas['template_detail_id'];
									$this->request->data['AreaRelation']['title'] = strip_tags(str_replace("<br />", " ", str_replace("<br>", " ", $wsareas['title'])));
									$this->request->data['AreaRelation']['description'] = strip_tags(str_replace("<br />", " ", str_replace("<br>", " ", $wsareas['tooltip_text'])));
									$this->request->data['AreaRelation']['tooltip_text'] = strip_tags(str_replace("<br />", " ", str_replace("<br>", " ", $wsareas['tooltip_text'])));
									$this->request->data['AreaRelation']['is_standby'] = $wsareas['is_standby'];
									$this->request->data['AreaRelation']['status'] = $wsareas['status'];
									$this->request->data['AreaRelation']['sort_order'] = $wsareas['sort_order'];
									$this->request->data['AreaRelation']['studio_status'] = $wsareas['sort_order'];
									$this->request->data['AreaRelation']['is_search'] = $wsareas['studio_status'];

									if ($this->AreaRelation->save($this->request->data)) {

										$AreaRelationID = $this->AreaRelation->getLastInsertId();

										$areaElement = area_element($wsareas['id']);

										if (isset($areaElement) && !empty($areaElement)) {

											// data inserted into Element Relation table
											foreach ($areaElement as $elementlist) {

												$element_id = $elementlist['Element']['id'];
												$this->request->data['ElementRelation']['id'] = '';
												$this->request->data['ElementRelation']['area_relation_id'] = $AreaRelationID;
												$this->request->data['ElementRelation']['updated_user_id'] = $user_id;
												$this->request->data['ElementRelation']['title'] = strip_tags(str_replace("<br />", " ", str_replace("<br>", " ", $elementlist['Element']['title'])));
												$this->request->data['ElementRelation']['description'] = strip_tags(str_replace("<br />", " ", str_replace("<br>", " ", $elementlist['Element']['description'])));
												$this->request->data['ElementRelation']['comments'] = strip_tags(str_replace("<br />", " ", str_replace("<br>", " ", $elementlist['Element']['comments'])));
												$this->request->data['ElementRelation']['date_constraints'] = $elementlist['Element']['date_constraints'];
												$this->request->data['ElementRelation']['start_date'] = '';
												$this->request->data['ElementRelation']['end_date'] = '';
												$this->request->data['ElementRelation']['sign_off'] = 0;
												$this->request->data['ElementRelation']['color_code'] = $elementlist['Element']['color_code'];
												$this->request->data['ElementRelation']['sort_order'] = $elementlist['Element']['sort_order'];
												$this->request->data['ElementRelation']['status'] = $elementlist['Element']['status'];
												$this->request->data['ElementRelation']['studio_status'] = $elementlist['Element']['studio_status'];
												$this->request->data['ElementRelation']['is_search'] = $elementlist['Element']['is_search'];

												/* Element relation save start */
												if ($this->ElementRelation->save($this->request->data)) {

													$elementRelationID = $this->ElementRelation->getLastInsertId();

													if (isset($post['include_documents']) && $post['include_documents'] == 'on') {
														$element_documents = elements_files($element_id);
														if (isset($element_documents) && !empty($element_documents)) {
															//insert into Element Relation documents
															foreach ($element_documents as $elementlists) {
																$time = time() . rand();
																// pr($element_documents);

																$this->request->data['ElementRelationDocument']['id'] = '';
																$this->request->data['ElementRelationDocument']['element_relation_id'] = $elementRelationID;
																$this->request->data['ElementRelationDocument']['creater_id'] = $user_id;
																$this->request->data['ElementRelationDocument']['updated_user_id'] = $user_id;
																$this->request->data['ElementRelationDocument']['title'] = strip_tags(str_replace("<br />", " ", str_replace("<br>", " ", $elementlists['ElementDocument']['title'])));

																$this->request->data['ElementRelationDocument']['file_size'] = $elementlists['ElementDocument']['file_size'];
																$this->request->data['ElementRelationDocument']['file_type'] = $elementlists['ElementDocument']['file_type'];
																$this->request->data['ElementRelationDocument']['status'] = $elementlists['ElementDocument']['status'];
																$this->request->data['ElementRelationDocument']['is_search'] = $elementlists['ElementDocument']['is_search'];

																$elmentdocumentfile = ELEMENT_DOCUMENT_PATH . $element_id . "/" . $elementlists['ElementDocument']['file_name'];
																$templateDocumentfile = TEMPLATE_DOCUMENTS . $time . $elementlists['ElementDocument']['file_name'];
																$file_newname = '';
																if (file_exists($elmentdocumentfile)) {
																	$file_newname = file_newname(TEMPLATE_DOCUMENTS, $elementlists['ElementDocument']['file_name']);
																	$file_newname_dir = TEMPLATE_DOCUMENTS . $file_newname;
																	if (!copy($elmentdocumentfile, $file_newname_dir)) {
																		echo "failed to copy $file...";die;
																	}
																}
																$this->request->data['ElementRelationDocument']['file_name'] = $file_newname;
																$this->ElementRelationDocument->save($this->request->data);
																$elementRelationDocumentID = $this->ElementRelationDocument->getLastInsertId();

															}
														}
													}

												}

												/* Element relation save end */

											}

										} //End Element Relation if condition
										$response['success'] = true;

									} else {

										/* Template Relation delete if not saved areas */
										$this->TemplateRelation->delete($TemplateRelationID);
										$this->Session->setFlash('Knowledge Template has not been added.', 'error');
										$response['success'] = false;
										$this->redirect();

									}

								} // End AreaRelation Loop

							} //End AreaRelation if condition

							$catCnt = ( isset($template_category_ids) && !empty($template_category_ids) ) ? count($template_category_ids) : 0;
							$this->Session->setFlash('Knowledge Template has been added successfully.', 'success');
							$response['success'] = true;

							/* if( $catCnt > 1 ){

									return $this->redirect(array('controller' => 'templates', 'action' => 'create_workspace', '0'));

								} else {

									return $this->redirect(array('controller' => 'templates', 'action' => 'create_workspace', '0/'.$template_category_ids[0]));

							*/

						} else {

							//$this->Session->setFlash('Template has not been added.', 'error');
							$response['content'] = $this->TemplateRelation->validationErrors;
							$response['success'] = false;

						}
						/* Template save process end */
					}
				}

				return json_encode($response);

			}
		}
	}

	public function delete_an_item($template_id = null) {

		if ($this->request->isAjax()) {

			$this->layout = false;
			$viewData = null;
			$viewData['template_id'] = $template_id;

			$this->set($viewData);
			$this->render('/Templates/partials/delete_an_item');

		}
	}

	public function get_workspace_detail($wsp_id = null){

		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			$this->autoRender = false;

			if ($this->request->is('post') || $this->request->is('put')) {

				// pr($this->request->data); die;

				$post = $this->request->data;
				$workspace_id = $post['workspace_id'];

				if (!$workspace_id) {
					return null;
				}

				$wsp = $this->Workspace->find('first', ['conditions' => ['Workspace.id' => $workspace_id,  ], 'fields' => ['Workspace.id', 'Workspace.title', 'Workspace.description' ]]);

				if(isset($wsp) && !empty($wsp['Workspace']['description'])){
					return $wsp['Workspace']['description'];
				}


			}

		}
	}

}