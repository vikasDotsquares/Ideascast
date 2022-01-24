<?php
/**
 * Dashboard controller.
 *
 * This file will render views from views/Dashboards/
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
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
App::uses('AppController', 'Controller');
App::uses('CakeEmail', 'Network/Email');
/**
 * Dashboards controller
 *
 * Override this controller by placing a copy in controllers directory of an application
 *
 * @package       app.Controller
 * @link http://book.cakephp.org/2.0/en/controllers/dashboards-controller.html
 */
class DashboardsController extends AppController {
/**
 * Controller name
 *
 * @var string
 */
	public $name = 'Dashboards';
/**
 * Default helper
 *
 * @var array
 */
	public $helpers = array('Html', 'Session', 'Common', 'TaskCenter', "Permission");
/**
 * This controller does not use a model
 *
 * @var array
 */
	public $uses = array('UserSetting', 'Workspace', 'Project', 'ProjectDateHistory', 'Aligned', 'AdminSetting', 'Element', 'Reminder', 'ReminderSetting', 'EmailNotification', 'User', 'UserDetail', 'ElementCost', 'ElementDependency', 'ElementDependancyRelationship', 'ElementCostHistory','SignoffTask');
	public $user_id = null;
	public $objView = null;
	public function beforeFilter() {
		parent::beforeFilter();
		$this->user_id = $this->Auth->user('id');
		$this->Auth->allow('update_color', 'update_todays_reminder_setting');
		$view = new View();
		$this->objView = $view;

		if( $_SERVER['HTTP_HOST'] != LOCALIP )  {

			if( $this->Session->read('Auth.User.role_id') == 3 ){
				$this->redirect(['controller' => 'organisations','action' => 'dashboard']);
			}
			if( $this->Session->read('Auth.User.role_id') == 1 ){
				//$this->redirect(['controller' => 'dashboard','action' => 'index']);
				// $this->redirect(SITEURL . 'dashboard');
			}
		}


	}
/**
 * Displays a view
 *
 * @param mixed What page to display
 * @return void
 */
	public function admin_index() {
		$this->layout = 'admin_inner';
		$profile = $this->Session->read('Auth.Admin.User');
		$title_for_layout = 'Admin Dashboard';
		$this->set(compact('title_for_layout'));
	}

	public function admin_update_color() {
		$response['success'] = false;
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			// $this->autoRender = false;
			$slug = $color_code = null;
			if ($this->request->is('post') || $this->request->is('put')) {
				//pr($this->request->data);
				$slug = $this->request->data['slug'];
				$color_code = $this->request->data['color_code'];
				$title = $this->request->data['title'];

				$old = $this->AdminSetting->find('first', ['conditions' => ['AdminSetting.slug' => $slug]]);
				if (isset($old) && !empty($old)) {
					$this->request->data['AdminSetting']['id'] = $old['AdminSetting']['id'];
					$this->request->data['AdminSetting']['color_code'] = $color_code;
					if ($this->AdminSetting->save($this->request->data)) {
						$response['success'] = true;
					}
				} else {

					$this->request->data['AdminSetting']['color_code'] = $color_code;
					$this->request->data['AdminSetting']['slug'] = $slug;
					$this->request->data['AdminSetting']['title'] = $title;
					if ($this->AdminSetting->save($this->request->data)) {

						$lastid = $this->AdminSetting->getLastInsertId();
						$this->request->data['AdminSetting']['sort_order'] = $lastid;
						$this->AdminSetting->save($this->request->data);

						$response['success'] = true;
					}
				}
			}
		}
		echo json_encode($response);
		exit;
	}
	public function admin_get_color_code($slug = null) {
		$this->layout = false;
		$this->autoRender = false;
		$this->loadModel("AdminSetting");
		$old = $this->AdminSetting->findBySlug($slug);
		return isset($old['AdminSetting']['color_code']) && !empty($old['AdminSetting']['color_code']) ? $old['AdminSetting']['color_code'] : 'bg-default';
	}
	/**************** Project Center *******************/
	public function project_center($align_id = null) {
		$this->layout = 'inner';
		$this->set('title_for_layout', __('Project Center', true));
		$this->set('page_heading', __('Project Center', true));
		$this->set('page_subheading', __('View Project activities and access work sections', true));
		$data = $user_settings = null;
		// $user_conditions = ['UserSetting.user_id' => $this->user_id];
		if ($this->Session->read('Auth.User.role_id') == 1) {
			return $this->redirect(SITEURL . 'templates/create_workspace/0/');
		}
		// if logged user has no settings for project center than insert new data
		/*if (!$this->UserSetting->hasAny($user_conditions)) {
			// Get default settings data
			$newSettings = $this->UserSetting->setSettings($this->user_id);
			// Insert into database
			$this->UserSetting->saveAll($newSettings);
		}*/
		/*$user_settings = $this->UserSetting->find('all', [
			'conditions' => $user_conditions,
			'recursive' => -1,
		]);
		if (isset($user_settings) && !empty($user_settings)) {
			$user_settings = Set::combine($user_settings, '{n}.UserSetting.slug', '{n}.UserSetting');
		}*/

		// $aligneds = $this->Aligned->find("list", ['order' => ['Aligned.title ASC']]);
		// $this->set(compact('aligneds'));
		// $data['user_settings'] = $user_settings;
		// $data['align_id'] = $align_id;
		$data['crumb'] = [
			'last' => [
				'data' => [
					'title' => "Project Center",
					'data-original-title' => "Project Center",
				],
			],
		];

		//========== Updated 25th Oct ==================
		/*$userProjectStatus = $this->UserDetail->find('first', ['conditions' => ['UserDetail.user_id' => $this->Session->read('Auth.User.id')]]);
		$data['create_project_status'] = 0;
		if (isset($userProjectStatus) && !empty($userProjectStatus['UserDetail']['create_project']) && $userProjectStatus['UserDetail']['create_project'] == 1) {
			$data['create_project_status'] = $userProjectStatus['UserDetail']['create_project'];
		}*/

		//==============================================

		$this->set($data);

	}
	public function collapse_expand() {
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			$response = [
				'success' => true,
				'msg' => '',
				'content' => null,
			];
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				$user_settings = $this->UserSetting->find('first', [
					'conditions' => [
						'UserSetting.user_id' => $this->user_id,
						'UserSetting.slug' => $post['slug'],
					],
					'recursive' => -1,
				]);
				if (isset($user_settings) && !empty($user_settings)) {
					$this->UserSetting->id = $user_settings['UserSetting']['id'];
					if ($this->UserSetting->saveField('is_closed', $post['is_closed'])) {
						$response['success'] = true;
					}
				}
				$user_settings = $this->UserSetting->find('first', [
					'conditions' => [
						'UserSetting.user_id' => $this->user_id,
						'UserSetting.slug' => $post['slug'],
					],
					'recursive' => -1,
				]);
			}
			echo json_encode($response);
			exit;
		}
	}

	public function color_codes() {
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			$response = [
				'success' => true,
				'msg' => '',
				'content' => null,
			];
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				$user_settings = $this->UserSetting->find('first', [
					'conditions' => [
						'UserSetting.user_id' => $this->user_id,
						'UserSetting.slug' => $post['slug'],
					],
					'recursive' => -1,
				]);
				if (isset($user_settings) && !empty($user_settings)) {
					$this->UserSetting->id = $user_settings['UserSetting']['id'];
					if ($this->UserSetting->saveField('color_code', $post['color_code'])) {
						$response['success'] = true;
					}
				}
			}
			echo json_encode($response);
			exit;
		}
	}
	public function project_detail() {
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			$response = [
				'success' => true,
				'msg' => '',
				'content' => null,
			];
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				$project = $this->Project->find('first', array(
					'joins' => array(
						array(
							'table' => 'user_projects',
							'alias' => 'UserProject',
							'type' => 'INNER',
							'conditions' => array(
								'UserProject.project_id = Project.id',
							),
						),
					),
					'conditions' => ['Project.id' => $post['id']],
					'fields' => ['UserProject.*', 'Project.*'],
					'order' => 'UserProject.modified DESC',
					'group' => ['UserProject.project_id'],
					'recursive' => -1,
				));
				$view = new View($this, false);
				$view->viewPath = 'Dashboards/partials'; // Directory inside view directory to search for .ctp files
				$view->layout = false; // if you want to disable layout
				$view->set('slug', $post['slug']);
				$user_settings = $this->UserSetting->find('first', [
					'conditions' => [
						'UserSetting.user_id' => $this->user_id,
						'UserSetting.slug' => $post['slug'],
					],
					'recursive' => -1,
				]);
				$view->set('user_settings', $user_settings);
				if (isset($post['permit_id']) && !empty($post['permit_id'])) {
					$view->set('permit_id', $post['permit_id']);
				}
				if (isset($project) && !empty($project)) {
					$view->set('projects', $project);
				}
				$html = $view->render('center_project_detail');
				echo json_encode($html);
				exit;
			}
		}
	}
	public function partial_project_detail() {
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			$response = [
				'success' => true,
				'msg' => '',
				'content' => null,
			];
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				$project = $this->Project->find('first', array(
					'joins' => array(
						array(
							'table' => 'user_projects',
							'alias' => 'UserProject',
							'type' => 'INNER',
							'conditions' => array(
								'UserProject.project_id = Project.id',
							),
						),
					),
					'conditions' => ['Project.id' => $post['id']],
					'fields' => ['UserProject.*', 'Project.*'],
					'order' => 'UserProject.modified DESC',
					'group' => ['UserProject.project_id'],
					'recursive' => -1,
				));
				$view = new View($this, false);
				$view->viewPath = 'Dashboards/partials'; // Directory inside view directory to search for .ctp files
				$view->layout = false; // if you want to disable layout
				$view->set('slug', $post['slug']);
				$user_settings = $this->UserSetting->find('first', [
					'conditions' => [
						'UserSetting.user_id' => $this->user_id,
						'UserSetting.slug' => $post['slug'],
					],
					'recursive' => -1,
				]);
				$view->set('user_settings', $user_settings);
				if (isset($post['permit_id']) && !empty($post['permit_id'])) {
					$view->set('permit_id', $post['permit_id']);
				}
				if (isset($project) && !empty($project)) {
					$view->set('projects', $project);
				}
				$html = $view->render('partial_project_detail');
				echo json_encode($html);
				exit;
			}
		}
	}
	public function partial_comments_detail() {
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			$response = [
				'success' => true,
				'msg' => '',
				'content' => null,
			];
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				$project = $this->Project->find('first', array(
					'joins' => array(
						array(
							'table' => 'user_projects',
							'alias' => 'UserProject',
							'type' => 'INNER',
							'conditions' => array(
								'UserProject.project_id = Project.id',
							),
						),
					),
					'conditions' => ['Project.id' => $post['id']],
					'fields' => ['UserProject.*', 'Project.*'],
					'order' => 'UserProject.modified DESC',
					'group' => ['UserProject.project_id'],
					'recursive' => -1,
				));
				$view = new View($this, false);
				$view->viewPath = 'Dashboards/partials'; // Directory inside view directory to search for .ctp files
				$view->layout = false; // if you want to disable layout
				if (isset($post['slug']) && !empty($post['slug'])) {
					$view->set('slug', $post['slug']);
				}
				if (isset($post['user_id']) && !empty($post['user_id'])) {
					$view->set('userId', $post['user_id']);
				}
				if (isset($post['permit_id']) && !empty($post['permit_id'])) {
					//$view->set('permit_id', $post['permit_id']);
				}
				if (isset($project) && !empty($project)) {
					$view->set('projects', $project);
				}
				$html = $view->render('partial_comments_detail');
				echo json_encode($html);
				exit;
			}
		}
	}
	/* UPDATES: 31 MARCH, 2017 */
	public function partial_project_tasks() {
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			$response = [
				'success' => true,
				'msg' => '',
				'content' => null,
			];
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				// pr($post, 1);
				$view = new View($this, false);
				$view->viewPath = 'Dashboards/partials'; // Directory inside view directory to search for .ctp files
				$view->layout = false; // if you want to disable layout
				if (isset($post['slug']) && !empty($post['slug'])) {
					$view->set('slug', $post['slug']);
				}
				if (isset($post['user_id']) && !empty($post['user_id'])) {
					$view->set('userId', $post['user_id']);
				}
				if (isset($post['permit_id']) && !empty($post['permit_id'])) {
					//$view->set('permit_id', $post['permit_id']);
				}
				if (isset($post['id']) && !empty($post['id'])) {
					$view->set('project', $post['id']);
				}
				$html = $view->render('project_tasks');

				echo json_encode($html);
				exit;
			}
		}
	}

	public function partial_project_blogs() {
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			$response = [
				'success' => true,
				'msg' => '',
				'content' => null,
			];
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				$view = new View($this, false);
				$view->viewPath = 'Dashboards/partials'; // Directory inside view directory to search for .ctp files
				$view->layout = false; // if you want to disable layout
				if (isset($post['slug']) && !empty($post['slug'])) {
					$view->set('slug', $post['slug']);
				}
				if (isset($post['user_id']) && !empty($post['user_id'])) {
					$view->set('userId', $post['user_id']);
				}
				if (isset($post['permit_id']) && !empty($post['permit_id'])) {
					//$view->set('permit_id', $post['permit_id']);
				}
				if (isset($post['id']) && !empty($post['id'])) {
					$view->set('project', $post['id']);
				}
				$html = $view->render('project_blogs');
				echo json_encode($html);
				exit;
			}
		}
	}
	public function partial_project_comments() {
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			$response = [
				'success' => true,
				'msg' => '',
				'content' => null,
			];

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				$view = new View($this, false);
				$view->viewPath = 'Dashboards/partials'; // Directory inside view directory to search for .ctp files
				$view->layout = false; // if you want to disable layout
				if (isset($post['slug']) && !empty($post['slug'])) {
					$view->set('slug', $post['slug']);
				}
				if (isset($post['user_id']) && !empty($post['user_id'])) {
					$view->set('userId', $post['user_id']);
				}
				if (isset($post['permit_id']) && !empty($post['permit_id'])) {
					//$view->set('permit_id', $post['permit_id']);
				}
				if (isset($post['id']) && !empty($post['id'])) {
					$view->set('project', $post['id']);
				}
				$html = $view->render('project_comments');

				echo json_encode($html);
				exit;
			}
		}
	}

	public function partial_project_assets() {
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			$response = [
				'success' => true,
				'msg' => '',
				'content' => null,
			];
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				$view = new View($this, false);
				$view->viewPath = 'Dashboards/partials'; // Directory inside view directory to search for .ctp files
				$view->layout = false; // if you want to disable layout
				if (isset($post['slug']) && !empty($post['slug'])) {
					$view->set('slug', $post['slug']);
				}
				if (isset($post['user_id']) && !empty($post['user_id'])) {
					$view->set('userId', $post['user_id']);
				}
				if (isset($post['permit_id']) && !empty($post['permit_id'])) {
					//$view->set('permit_id', $post['permit_id']);
				}
				if (isset($post['id']) && !empty($post['id'])) {
					$view->set('project', $post['id']);
				}
				$html = $view->render('project_assets');
				echo json_encode($html);
				exit;
			}
		}
	}

	public function get_project_comments($project_id = null) {
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			$response = [
				'success' => true,
				'msg' => '',
				'content' => null,
			];
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				$project_id = $post['project_id'];
				$slug = $post['slug'];
				$permission = (isset($post['permission']) && !empty($post['permission'])) ? $post['permission'] : 0;
				$view = new View($this, false);
				$view->viewPath = 'Dashboards/partials'; // Directory inside view directory to search for .ctp files
				$view->layout = false; // if you want to disable layout
				$view->set('project', $project_id);
				$view->set('permit_owner', $permission);
				$view->set('slug', $slug);
				$html = $view->render('project_comments');
				echo json_encode($html);
				exit;
			}
		}
	}

	public function get_task_count($project_id = null) {
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			$response = [
				'success' => true,
				'msg' => '',
				'content' => null,
			];

			$user_id = null;
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				if(isset($post['user_id']) && !empty($post['user_id'])) {
					$user_id = $post['user_id'];
				}
			}

			$task_total = $this->objView->loadHelper('ViewModel')->getUserTasksProjectCount($project_id, $user_id);

			echo json_encode($task_total);
			exit;
		}
	}


	public function get_paging_tasks() {
		$this->layout = false;
		$view = new View($this, false);
		$view->viewPath = 'Dashboards/partials';
		if ($this->request->is('post') || $this->request->is('put')) {
			$post = $this->request->data;
			$project_id = $post['project'];
			$page = $post['page'];
			$user_id = null;
			if(isset($post['user_id']) && !empty($post['user_id'])) {
				$user_id = $post['user_id'];
			}
			$tasks = $this->objView->loadHelper('ViewModel')->getUserTasksProjectPaging($project_id, $page, $user_id);
			$view->set('all_tasks', $tasks);
			$view->set('project', $project_id);
		}

		$html = $view->render('get_paging_tasks');

		echo json_encode($html);
		exit();
	}

	public function compound_data() {
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			$response = [
				'success' => true,
				'msg' => '',
				'content' => null,
			];
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				// pr($post, 1);
				$view = new View($this, false);
				$view->viewPath = 'Dashboards/partials'; // Directory inside view directory to search for .ctp files
				$view->layout = false; // if you want to disable layout
				$view->set('slug', $post['slug']);
				$view->set('align', $post['align_id']);
				$html = $view->render('compound_data');
				echo json_encode($html);
				exit;
			}
		}
	}

	public function project_comment_tab() {
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			$response = [
				'success' => true,
				'msg' => '',
				'content' => null,
			];
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				// pr($post, 1);
				$view = new View($this, false);
				$view->viewPath = 'Dashboards/partials'; // Directory inside view directory to search for .ctp files
				$view->layout = false; // if you want to disable layout
				if (isset($post['slug']) && !empty($post['slug'])) {
					$view->set('slug', $post['slug']);
				}
				if (isset($post['user_id']) && !empty($post['user_id'])) {
					$view->set('userId', $post['user_id']);
				}
				if (isset($post['permit_id']) && !empty($post['permit_id'])) {
					//$view->set('permit_id', $post['permit_id']);
				}
				if (isset($post['id']) && !empty($post['id'])) {
					$view->set('project', $post['id']);
				}
				if (isset($post['section']) && !empty($post['section'])) {
					$view->set('section', $post['section']);
				}
				$html = $view->render('project_comment_tab');
				echo json_encode($html);
				exit;
			}
		}
	}

	public function project_resource_tab() {
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			$response = [
				'success' => true,
				'msg' => '',
				'content' => null,
			];
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				// pr($post, 1);
				$view = new View($this, false);
				$view->viewPath = 'Dashboards/partials'; // Directory inside view directory to search for .ctp files
				$view->layout = false; // if you want to disable layout
				if (isset($post['slug']) && !empty($post['slug'])) {
					$view->set('slug', $post['slug']);
				}
				if (isset($post['user_id']) && !empty($post['user_id'])) {
					$view->set('userId', $post['user_id']);
				}
				if (isset($post['permit_id']) && !empty($post['permit_id'])) {
					//$view->set('permit_id', $post['permit_id']);
				}
				if (isset($post['id']) && !empty($post['id'])) {
					$view->set('project', $post['id']);
				}
				if (isset($post['section']) && !empty($post['section'])) {
					$view->set('section', $post['section']);
				}
				$html = $view->render('project_resource_tab');
				echo json_encode($html);
				exit;
			}
		}
	}

/* ***********
 * TASK CENTER
 *************/
	public function task_center($project_id = null) {
		$this->layout = 'inner';
		$this->set('title_for_layout', __('Task Center', true));
		$this->set('page_heading', __('Task Center', true));
		$this->set('page_subheading', __('View Tasks in your Projects', true));
		$viewData = null;

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

		$sorted_project = [];
		if (isset($this->params['named']['program']) && !empty($this->params['named']['program'])) {
			$program_projects = program_projects($this->params['named']['program']);
			if (isset($program_projects) && !empty($program_projects)) {
				$program_projects = Set::extract($program_projects, '/ProjectProgram/project_id');
				foreach ($projects as $key => $value) {
					if (in_array($key, $program_projects)) {
						$sorted_project[$key] = $value;
					}
				}
			}
		}
		// pr($program_projects, 1);
		if (isset($sorted_project) && !empty($sorted_project)) {
			$projects = $sorted_project;
		}
		// pr($projects, 1);

		$viewData['projects'] = $projects;

		$viewData['named_params'] = (isset($this->params['named']['status']) && !empty($this->params['named']['status'])) ? $this->params['named']['status'] : 0;
		// pr($this->params['named'], 1);
		$this->setJsVar('named_params', $viewData['named_params']);

		$viewData['project_id'] = (isset($project_id) && !empty($project_id)) ? $project_id : false;
		$this->setJsVar('project_id', $viewData['project_id']);

		$viewData['crumb'] = [
			'last' => [
				'data' => [
					'title' => "Task Center",
					'data-original-title' => "Task Center",
				],
			],
		];
		$this->set($viewData);

		$current_user_id = $this->Session->read('Auth.User.id');
		$this->setJsVar('current_user_id', $current_user_id);
	}
	public function filtered_data() {
		if ($this->request->isAjax()) {
			$this->layout = false;
			if ($this->request->is('post') || $this->request->is('put')) {
				App::import('Controller', 'Users');
				$Users = new UsersController;
				$projects = null;
				$post = $this->request->data;
				$owner = 1;
				if (isset($post['user_ids']) && !empty($post['user_ids'])) {
					if (count($post['user_ids']) == 1) {
						if ($this->user_id == $post['user_ids'][0]) {
							$owner = null;
						}
					}
				}
				if (isset($post['project_ids']) && !empty($post['project_ids'])) {
					$projects = array_flip($post['project_ids']);
				}
				$filter_users = (isset($post['user_ids'])) ? $post['user_ids'] : null;
				$named_params = (isset($post['named_params'])) ? $post['named_params'] : null;
				$filter_projects = $projects;
				$view = new View($this, false);
				$view->viewPath = 'Dashboards/task_center';
				$view->set(compact("filter_users", "filter_projects", "named_params"));
				$html = $view->render('filtered_data');
				echo json_encode($html);
				exit;
			}
		}
	}
	public function filter_users() {
		if ($this->request->isAjax()) {
			$this->layout = false;
			if ($this->request->is('post') || $this->request->is('put')) {
				$filter_users = (isset($this->request->data['user_ids'])) ? $this->request->data['user_ids'] : null;
				$owner = (isset($this->request->data['clear'])) ? 1 : 0;
				$view = new View($this, false);
				$view->viewPath = 'Dashboards/task_center'; // Directory inside view directory to
				$view->set("filter_users", $filter_users);
				$view->set("owner", $owner);
				$html = $view->render('filter_users');
				echo json_encode($html);
				exit;
			}
		}
	}
	public function filter_projects() {
		if ($this->request->isAjax()) {
			$this->layout = false;
			if ($this->request->is('post') || $this->request->is('put')) {
				App::import('Controller', 'Users');
				$Users = new UsersController;
				$projects = null;
				$post = $this->request->data;
				// pr($post);
				$owner = 1;
				if (isset($post['user_ids']) && !empty($post['user_ids'])) {
					if (count($post['user_ids']) == 1) {
						if ($this->user_id == $post['user_ids'][0]) {
							$owner = null;
						}
					}
				}
				// pr($owner, 1 );
				$myprojectlist = $myreceivedprojectlist = $mygroupprojectlist = array();
				// Find All current user's projects
				$myprojectlist = $Users->__myproject_selectbox($this->user_id);
				// Find All current user's received projects
				$myreceivedprojectlist = $Users->__receivedproject_selectbox($this->user_id, $owner);
				// Find All current user's group projects
				$mygroupprojectlist = $Users->__groupproject_selectbox($this->user_id, $owner);
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
				$filter_users = (isset($this->request->data['user_ids'])) ? $this->request->data['user_ids'] : null;
				$named_params = (isset($post['named_params'])) ? $post['named_params'] : null;
				$allprojects = $projects;
				$view = new View($this, false);
				$view->viewPath = 'Dashboards/task_center'; // Directory inside view directory to
				$view->set(compact("filter_users", "allprojects", "named_params"));
				$html = $view->render('filter_projects');
				echo json_encode($html);
				exit;
			}
		}
	}
	public function task_count($first = null) {
		if ($this->request->isAjax()) {
			$this->layout = false;
			if ($this->request->is('post') || $this->request->is('put')) {
				App::import('Controller', 'Users');
				$Users = new UsersController;
				$projects = null;
				$post = $this->request->data;
				$yes = 1;
				$filter_users = null;
				if (isset($post['ids']) && !empty($post['ids'])) {
					$filter_users = $post['ids'];
				} else if (isset($post['user_ids']) && !empty($post['user_ids'])) {
					$filter_users = $post['user_ids'];
				}

				$owner = 1;
				if (isset($post['ids']) && !empty($post['ids'])) {
					if (count($post['ids']) == 1) {
						if ($this->user_id == $post['ids'][0]) {
							$owner = null;
						}
					}
				} else if (isset($post['clear']) && !empty($post['clear'])) {
					$owner = null;
				}
				$myprojectlist = $myreceivedprojectlist = $mygroupprojectlist = array();
				// Find All current user's projects
				$myprojectlist = $Users->__myproject_selectbox($this->user_id);
				// Find All current user's received projects
				$myreceivedprojectlist = $Users->__receivedproject_selectbox($this->user_id, $owner);
				// Find All current user's group projects
				$mygroupprojectlist = $Users->__groupproject_selectbox($this->user_id, $owner);

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
				$allprojects = array_keys($projects);
				$newProjects = null;
				if (isset($post['project_ids']) && !empty($post['project_ids'])) {
					$allprojects = $post['project_ids'];
				}
				$named_params = (isset($post['named_params'])) ? $post['named_params'] : null;

				$view = new View($this, false);
				$view->viewPath = 'Dashboards/task_center'; // Directory inside view directory to
				$view->set(compact("filter_users", "allprojects", "owner", "named_params"));
				$html = $view->render('task_count');
				echo json_encode($html);
				exit;
			}
		}
	}
	public function filter_task_by_project($first = null) {
		$html = '';
		if ($this->request->isAjax()) {
			$this->layout = false;
			if ($this->request->is('post') || $this->request->is('put')) {
				$filter_users = isset($this->request->data['user_ids']) ? $this->request->data['user_ids'] : null;
				$filter_projects = $this->request->data['project_ids'];
				$view = new View($this, false);
				$view->viewPath = 'Dashboards/task_center'; // Directory inside view directory to
				$view->set(compact("filter_users", "filter_projects", "first"));
				$html = $view->render('filter_task_by_project');
			}
			echo json_encode($html);
			exit;
		}
	}
	public function filter_wsp_by_project() {
		if ($this->request->isAjax()) {
			$this->layout = false;
			if ($this->request->is('post') || $this->request->is('put')) {
				$filter_users = isset($this->request->data['user_ids']) ? $this->request->data['user_ids'] : null;
				$filter_projects = $this->request->data['project_ids'];
				$view = new View($this, false);
				$view->viewPath = 'Dashboards/task_center'; // Directory inside view directory to
				$view->set(compact("filter_users", "filter_projects"));
				$html = $view->render('filter_wsp_by_project');
				echo json_encode($html);
				exit;
			}
		}
	}
	public function filter_selected_project() {
		if ($this->request->isAjax()) {
			$this->layout = false;
			if ($this->request->is('post') || $this->request->is('put')) {
				$filter_users = isset($this->request->data['user_ids']) ? $this->request->data['user_ids'] : null;
				$filter_projects = $this->request->data['project_ids'];
				$view = new View($this, false);
				$view->viewPath = 'Dashboards/task_center'; // Directory inside view directory to
				$view->set(compact("filter_users", "filter_projects"));
				$html = $view->render('filter_selected_project');
				echo json_encode($html);
				exit;
			}
		}
	}
	public function filter_people_by_project() {
		if ($this->request->isAjax()) {
			$this->layout = false;
			if ($this->request->is('post') || $this->request->is('put')) {
				$selected_projects = $this->request->data['project_ids'];
				$view = new View($this, false);
				$view->viewPath = 'Dashboards/task_center'; // Directory inside view directory to
				$view->set("selected_projects", $selected_projects);
				$html = $view->render('filter_people_by_project');
				echo json_encode($html);
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
			$this->render('/Dashboards/task_center/task_list_ws_date');
		}
	}
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

							// ============== Start Email Notification =================================

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

							// ============== End Email Notification =================================

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
	public function task_list_el_date($element_id = null) {
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

				$signoff_details = array();
				$element_signoff = 0;
				if( isset($element) && !empty($element) && $element['Element']['sign_off'] == 1 ){
					$element_signoff = 1;
					$signoff_details = $this->SignoffTask->find('first', array('conditions'=>array('element_id'=>$element_id)));
					if( isset($signoff_detail) && !empty($signoff_detail) ){
						return $signoff_details;
					}
				}

			}

			$this->set('signoff_details', $signoff_details);
			$this->set('signoff_status', $element_signoff);
			$this->set('response', $response);
			$this->set('elementid', $element_id);
			$this->render('/Dashboards/task_center/task_list_el_date');
		}
	}

	public function task_list_project_date($project_id = null) {
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			$response = null;
			if (isset($project_id) && !empty($project_id)) {
				// Get all workspace elements
				$project = $this->Project->findById($project_id);
				$this->request->data = $project;
				$response['project'] = $project;
				$response['project_id'] = $project_id;
				$this->loadModel("ProjectDateHistory");
				$history = $this->ProjectDateHistory->find("all", array("order" => "id DESC", "conditions" => ["project_id" => $project_id]));
				$this->set("history", $history);
			}
			$this->set('response', $response);
			$this->render('/Dashboards/task_center/task_list_project_date');
		}
	}
	public function project_annotate($project_id = null) {
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			$response = null;
			if (isset($project_id) && !empty($project_id)) {
				$this->loadModel("ProjectDateHistory");
				$history = $this->ProjectDateHistory->find("all", array("order" => "id DESC", "conditions" => ["project_id" => $project_id]));
				$this->set("history", $history);
			}
			$this->set('response', $response);
			$this->render('/Dashboards/task_center/project_annotate');
		}
	}
	public function task_list_project_save($project_id = null) {
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			$response = [
				'success' => false,
				'status' => 400,
				'project_id' => $project_id,
				'content' => null,
				'date_error' => null,
			];
			$wsp_max_date = null;
			if (isset($project_id) && !empty($project_id)) {
				$project = $this->Project->findById($project_id);
				$workspaces = get_project_workspace($project_id, true);
				if (isset($workspaces) && !empty($workspaces)) {
					$wsp_max_date = get_workspace_max_enddate(array_keys($workspaces));
				}
				$enddate = date("Y-m-d", strtotime($project['Project']['end_date']));
				$newdate = date("Y-m-d", strtotime($this->data['Project']['end_date']));
				if ($newdate == $enddate) {
					$response['success'] = false;
					$response['date_error'] = 'Date already set.';
				} else if ($newdate == null) {
					$response['success'] = false;
					$response['date_error'] = 'Please select end date.';
				} else if ($wsp_max_date > $newdate) {
					$response['success'] = false;
					$response['date_error'] = 'Please select project end date grater than workspace end date.';
				} else {
					$this->Project->id = $project_id;

					$latestDate = $this->Project->find('first', ['conditions' => ['Project.id' => $project_id]]);

					if ($this->Project->saveField('end_date', $newdate)) {
						$this->loadModel("ProjectDateHistory");
						$d['project_id'] = $project_id;
						$d['user_id'] = $this->user_id;
						$d['end_date'] = $enddate;
						$d['comments'] = $this->request->data['Project']['comments'];
						$this->ProjectDateHistory->save($d);
						$response['success'] = true;
						$history = $this->ProjectDateHistory->find("all", array("order" => "id DESC", "conditions" => ["project_id" => $project_id]));
						$this->set("history", $history);

						// ============== Start Email Notification ===============

						$startDate = strtotime($latestDate['Project']['start_date']);
						$endDate = strtotime($latestDate['Project']['end_date']);

						$willSend = false;
						/* if (($startDate < strtotime($this->request->data['Project']['start_date']) || $startDate > strtotime($this->request->data['Project']['start_date'])) && ($endDate < strtotime($this->request->data['Project']['end_date']) || $endDate > strtotime($this->request->data['Project']['end_date']))) {
								$willSend = true;

							} else if ($startDate < strtotime($this->request->data['Project']['start_date']) || $startDate > strtotime($this->request->data['Project']['start_date'])) {
								$willSend = true;

						*/if ($endDate < strtotime($this->request->data['Project']['end_date']) || $endDate > strtotime($this->request->data['Project']['end_date'])) {
							$willSend = true;

						}

						if ($willSend == true) {
							$this->Common->projectScheduleChangeEmail($project_id);
						}

						// ============== End Email Notification ===============

					}
				}
			}
			$this->set('response', $response);
			echo json_encode($response);
			exit;
		}
	}
	public function task_list_save_elements($area_id = null) {
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			$response = [
				'success' => false,
				'status' => 400,
				'content' => null,
				'element_id' => null,
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

				foreach ($post['element_date'] as $k => $row) {
					$response['element_id'] = $row['id'];
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
				//die;
				//pr($allData); die;

				//===== Start for Email Notification ======================
				$element_id = $allData['Element'][0]['id'];
				$latestDate = $this->Element->findById($element_id);
				//===== End for Email Notification ========================

				if (isset($allData) && !empty($allData)) {
					if (isset($allData) && empty($allErrors)) {
						if ($this->Element->saveAll($allData['Element'])) {

							//===== Start Email Notification ==============

							$startDate = strtotime($latestDate['Element']['start_date']);
							$endDate = strtotime($latestDate['Element']['end_date']);

							$willSend = false;
							if (($startDate < strtotime($allData['Element'][0]['start_date']) || $startDate > strtotime($allData['Element'][0]['start_date'])) && ($endDate < strtotime($allData['Element'][0]['end_date']) || $endDate > strtotime($allData['Element'][0]['end_date']))) {
								$willSend = true;

							} else if ($startDate < strtotime($allData['Element'][0]['start_date']) || $startDate > strtotime($allData['Element'][0]['start_date'])) {
								$willSend = true;

							} else if ($endDate < strtotime($allData['Element'][0]['end_date']) || $endDate > strtotime($allData['Element'][0]['end_date'])) {
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

							}
							//======== End Email Notification ======================

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

			$uncheckedIDS = array();
			$gsUp = array();
			$gatedRLS = [];
			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;

			if (isset($post['DefaultDependency']) ) {

				$defaultElementid = $post['DefaultDependency']['element_id'];

				$dataDep['ElementDependency']['user_id'] = $this->Session->read('Auth.User.id');
				$dataDep['ElementDependency']['element_id'] = $defaultElementid;

				$edata = $this->ElementDependency->find('first', array('conditions' => array('ElementDependency.element_id' => $defaultElementid)));

				$dataDep['ElementDependency']['id'] = null;
				if( isset($edata) && !empty($edata['ElementDependency']['id']) ){
					$dataDep['ElementDependency']['id'] = $edata['ElementDependency']['id'];
					$relation_id = $edata['ElementDependency']['id'];
				}else{
					$relation_id = $this->ElementDependency->getLastInsertId();
				}

				$this->ElementDependency->save($dataDep);


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

					// pr($post ); die;
					//$data['ElementDependency']['user_id'] = $this->Session->read('Auth.User.id');

					foreach ($post['ElementDependency'] as $key => $dependencyValue) {

						// pr($key);
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
									//  pr($Drelationshipdelete);


									$Drelationshipgated = array('ElementDependancyRelationship.element_dependancy_id' => $getID, 'ElementDependancyRelationship.element_id' => $defaultElementid,'ElementDependancyRelationship.is_gated' =>1 );
									//  pr($Drelationshipdelete);


									$gatedRLS[] = $this->ElementDependancyRelationship->find('first', array('conditions' => $Drelationshipgated));

									$this->ElementDependancyRelationship->deleteAll($Drelationshipdelete, true);
								}

								$this->ElementDependancyRelationship->delete($key);
							}
							////$this->ElementDependancyRelationship->deleteAll($dependancyrelationshipdelete);

						}

						//die;

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
					//pr($post['ElementDependancyRelationship']);

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
						//pr($all_DependancyRelationship); die;

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

									$this->ElementDependancyRelationship->saveAssociated($data1);

									/* get the Id from relationship and insert relationship in reverse order */

								} else {

									$dataDp = array();

									$dataDp['ElementDependency']['id'] = null;

									$dataDp['ElementDependency']['element_id'] = $allD;

									$dataDp['ElementDependency']['is_critical'] = 0;


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




				}

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
					$view->viewPath = 'Dashboards';
					$view->set('spendcost', $spendcost);
					$html = $view->render('costspendlist');
				} else {
					$view->viewPath = 'Dashboards';
					$view->set('estimatedcost', $estimatedcost);
					$html = $view->render('costlist');
				}

				echo json_encode($html);
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

						$query = "SELECT elements.id, elements.title,
						( select JSON_ARRAYAGG(JSON_OBJECT('element_id',element_dependancy_relationships.element_id,'ele_title',(select ele.title from elements ele where ele.id = element_dependancy_relationships.element_id),'dependency',element_dependancy_relationships.dependency)) as jsonobj from element_dependancy_relationships where element_dependancy_id = element_dependencies.id) as ele_all_dependancy,

						(SELECT count(dependency) FROM element_dependancy_relationships where element_dependancy_id = element_dependencies.id and dependency = 1) as dependency_predessor,

						(SELECT count(dependency) FROM element_dependancy_relationships where element_dependancy_id = element_dependencies.id and dependency = 2) as dependency_successor

						FROM elements
						LEFT JOIN element_dependencies
							ON element_dependencies.element_id = elements.id
						LEFT JOIN element_dependancy_relationships edr
							ON edr.element_dependancy_id = element_dependencies.id
						WHERE
							elements.id = ".$post['element_id']."
						GROUP BY elements.id ";

						$resutls =  $this->Element->query($query);

					$view->viewPath = 'Dashboards';
					$view->set('dependancy_value', $resutls);
					$html = $view->render('element_relation_list');

				}

				echo json_encode($html);
				die();
			}
		}

	}

	public function element_list_gantt() {

		//if ($this->request->isAjax()) {
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

				//pr($elementlist);
				$views = new View();
				$commonHelper = $views->loadHelper('Common');
				$getDependancyStatus = $commonHelper->dependancy_status($post['element_id']);

				$predessorCount = $commonHelper->ele_dependency_count($post['element_id'], 1);

				$successorCount = $commonHelper->ele_dependency_count($post['element_id'], 2);

				$dts = '';
				if (isset($getDependancyStatus) && $getDependancyStatus == 'predessor') {
					$dts = 1;
				} else if (isset($getDependancyStatus) && $getDependancyStatus == 'successor') {
					$dts = 2;
				} else if (isset($getDependancyStatus) && $getDependancyStatus == 'both') {
					$dts = 3;
				}

				$view->viewPath = 'Dashboards';
				$view->set('elementlist', $elementlist);
				$view->set('predessorCount', $predessorCount);
				$view->set('successorCount', $successorCount);
				$view->set('dependancytype', $dts);
				$view->set('dependancytypes', $dts);
				$html = $view->render('element_relation_gantt');

			}

			echo json_encode($html);
			die();
		}
		//}

	}

	public function task_centers($project_id = null) {

		$this->layout = 'inner';
		$this->set('title_for_layout', __('Task Center', true));
		$this->set('page_heading', __('Task Center', true));
		$this->set('page_subheading', __('View Tasks in your Projects', true));
		$viewData = null;
		$projects = null;

		$views = new View();
		$ViewModel = $views->loadHelper('ViewModel');
		$Permission = $views->loadHelper('Permission');

		$projects = $ViewModel->userTotalProjects($this->user_id);

		$sorted_project = array();
		$program_projects = array();
		$project_program = array();
		if (isset($this->params['named']['program']) && !empty($this->params['named']['program'])) {
			if( isset($this->params['named']['status']) && !empty($this->params['named']['status'])) {

				$program_projects = $this->objView->loadHelper('Permission')->program_project($this->params['named']['program'],$this->params['named']['status']);

			} else {

				$program_projects = $this->objView->loadHelper('Permission')->program_project($this->params['named']['program']);

			}
		}

		/* else if(isset($this->params['named']['status']) && !empty($this->params['named']['status'])){
			$projects = $ViewModel->userTotalProjects($this->user_id,$this->params['named']['status']);
		} else {
			$projects = $ViewModel->userTotalProjects($this->user_id);
		}*/


		if( isset($program_projects) && !empty($program_projects) ){
			foreach($program_projects as $projectlist){
				$project_program[]=	$projectlist['a']['project_id'];
			}
		}

		$viewData['named_params'] = (isset($this->params['named']['status']) && !empty($this->params['named']['status'])) ? $this->params['named']['status'] : 0;

		$viewData['assigned'] = (isset($this->params['named']['assigned']) && !empty($this->params['named']['assigned'])) ? $this->params['named']['assigned'] : 0;

		$viewData['projects'] = $projects;
		$viewData['project_program'] = $project_program;

		$this->setJsVar('named_params', $viewData['named_params']);

		$viewData['project_id'] = (isset($project_id) && !empty($project_id)) ? $project_id : false;
		$this->setJsVar('project_id', $viewData['project_id']);
		$this->setJsVar('project_program', $viewData['project_program']);
		$this->setJsVar('assigned', $viewData['assigned']);


		$viewData['crumb'] = [
			'last' => [
				'data' => [
					'title' => "Task Center",
					'data-original-title' => "Task Center",
				],
			],
		];
		$this->set($viewData);
		$current_user_id = $this->Session->read('Auth.User.id');
		$this->setJsVar('current_user_id', $current_user_id);

	}

	public function filtered_datas() {
		if ($this->request->isAjax()) {
			$html = '';
			$this->autoRender = false;
			$this->layout = false;

			$views = new View();
			$ViewModel = $views->loadHelper('ViewModel');

			if ($this->request->is('post') || $this->request->is('put')) {
				$projects = null;
				$post = $this->request->data;

				//pr($post,1);

				$owner = 1;
				$user_ids = '';
				$assigned_user_ids = '';
				if (isset($post['user_ids']) && !empty($post['user_ids'])) {
					if (count($post['user_ids']) == 1) {
						if ($this->user_id == $post['user_ids'][0]) {
							$owner = null;
						}
					}
					$user_ids = $post['user_ids'][0];
				}

				if (isset($post['assigned_user_ids']) && !empty($post['assigned_user_ids'])) {
					$assigned_user_ids = $post['assigned_user_ids'][0];
				}

				if (isset($post['assigned']) && !empty($post['assigned'])) {
					$assigned_user_ids = $post['assigned'];
				}

				$assign_sorting = '';
				if (isset($post['assign_sorting']) && !empty($post['assign_sorting'])) {
					$assign_sorting = $post['assign_sorting'];
				}

				$element_sorting = '';
				if (isset($post['element_sorting']) && !empty($post['element_sorting'])) {
					$element_sorting = $post['element_sorting'];
				}

				$wsp_sorting = '';
				if (isset($post['wsp_sorting']) && !empty($post['wsp_sorting'])) {
					$wsp_sorting = $post['wsp_sorting'];
				}

				$selected_dates = null;
				if (isset($post['selectedDates']) && !empty(trim($post['selectedDates']))) {
					$selected_dates = trim($post['selectedDates']);
				}

				$assigned_reaction = '';
				if (isset($post['assigned_status']) && !empty($post['assigned_status'])) {

					if( $post['assigned_status'] == 'assigned0' ){
						$assigned_reaction = 5;
					} else if( $post['assigned_status'] == 'assigned1' ){
						$assigned_reaction = 1;
					} else if( $post['assigned_status'] == 'assigned2' ){
						$assigned_reaction = 2;
					} else if( $post['assigned_status'] == 'assigned3' ){
						$assigned_reaction = 3;
					} else if( $post['assigned_status'] == 'assigned4' ){
						$assigned_reaction = 4;
					}

				}

				//pr($allprojects);
				//$filter_projects = $projects;
				$filter_projects = array();
				if (isset($post['project_ids']) && !empty($post['project_ids'])) {
					//$projects = array_flip($post['project_ids']);
					$projects = $post['project_ids'];
					$pids = implode(",",$post['project_ids']);
				 	$filter_projects = $ViewModel->projectDetails($pids, ['title', 'id', 'start_date', 'end_date', 'color_code']);
				}

				//echo $post['named_params']."named_parems";

				$namedparam = array();
				 if( isset($post['named_params']) && !empty($post['named_params']) ){
					if( $post['named_params'] == 1 ){
						$namedparam[] = 'OVD';
					}
					if( $post['named_params'] == 4 ){
						$namedparam[] = 'NON';
					}
					if( $post['named_params'] == 5 ){
						$namedparam[] = 'CMP';
					}
					if( $post['named_params'] == 7 ){
						$namedparam[] = 'PRG';
					}
					if( $post['named_params'] == 6 ){
						$namedparam[] = 'PND';
					}

					if( $post['named_params'] == 8 ){
						$namedparam[] = 'NON';
						$namedparam[] = 'PND';
						$namedparam[] = 'PRG';
						$namedparam[] = 'OVD';
					}
				}

				/* if(isset($post['named_param']) && !empty($post['named_param'])){
					$namedparam = array();
					unset($post['named_params']);
					unset($namedparam);
					$this->setJsVar('named_params', 'reset');
				    $this->setJsVar('assigned', 'reset');
				    

				} */

				$filter_task_staus = array();


				if( isset($namedparam) && !empty($namedparam) && (!isset($filter_task_staus) || empty($filter_task_staus)) ){
					$filter_task_staus = $namedparam;
				}

				if (isset($post['task_status']) && !empty($post['task_status'])) {
					$filter_task_staus = $post['task_status'];
				}


				$element_title = array();
				if (isset($post['element_title']) && !empty($post['element_title'])) {
					$element_title = $post['element_title'];
				}

				$dateStartSorttype = '';
				if (isset($post['dateStartSort_type']) && !empty($post['dateStartSort_type'])) {
					if( $post['dateStartSort_type'] == 'element' ){
						$dateStartSorttype = "elements";
					}
					if( $post['dateStartSort_type'] == 'workspace' ){
						$dateStartSorttype = "workspaces";
					}
				}

				$dateEndSorttype = '';
				if (isset($post['dateEndSort_type']) && !empty($post['dateEndSort_type'])) {
					if( $post['dateEndSort_type'] == 'element' ){
						$dateEndSorttype = "elements";
					}
					if( $post['dateEndSort_type'] == 'workspace' ){
						$dateEndSorttype = "workspaces";
					}
				}

				$filter_users = (isset($post['user_ids'])) ? $post['user_ids'] : null;
				//$filter_projects = $projects;


				//pr($filter_projects);

				$element_task_type = '';

				$view = new View($this, false);
				$view->viewPath = 'Dashboards/task_center';
				$view->set(compact("filter_users", "filter_projects","assigned_user_ids","assigned_reaction","filter_task_staus","assign_sorting","element_sorting","wsp_sorting","selected_dates","element_title","dateEndSorttype","dateStartSorttype","element_task_type"));



				$html = $view->render('filtered_datas');
				echo json_encode($html);
				exit;
			}
		}
	}

	public function get_paging_taskcenter() {
		$this->layout = false;
		$view = new View($this, false);
		$view->viewPath = 'Dashboards/partials';
		$ViewModel = $view->loadHelper('ViewModel');

		if ($this->request->is('post') || $this->request->is('put')) {
			$post = $this->request->data;
			$project_id = $post['project'];
			$page = $post['page'];
			$user_id = null;
			if(isset($post['user_id']) && !empty($post['user_id'])) {
				$user_id = $post['user_id'];
			}
				$assigned_user_ids = '';
				if (isset($post['assigned_userid']) && !empty($post['assigned_userid'])) {
					$assigned_user_ids = $post['assigned_userid'][0];
				}

				$filter_task_staus = array();
				if (isset($post['task_status']) && !empty($post['task_status'])) {
					$filter_task_staus = $post['task_status'];
				}

				$assigned_reaction = '';
				if (isset($post['assigned_status']) && !empty($post['assigned_status'])) {

					if( $post['assigned_status'] == 'assigned0' ){
						$assigned_reaction = 5;
					} else if( $post['assigned_status'] == 'assigned1' ){
						$assigned_reaction = 1;
					} else if( $post['assigned_status'] == 'assigned2' ){
						$assigned_reaction = 2;
					} else if( $post['assigned_status'] == 'assigned3' ){
						$assigned_reaction = 3;
					} else if( $post['assigned_status'] == 'assigned4' ){
						$assigned_reaction = 4;
					}

				}

			$dateStartSorttype = '';
			if (isset($post['dateStartSort_type']) && !empty($post['dateStartSort_type'])) {
				if( $post['dateStartSort_type'] == 'element' ){
					$dateStartSorttype = "elements";
				}
				if( $post['dateStartSort_type'] == 'workspace' ){
					$dateStartSorttype = "workspaces";
				}
			}

			$dateEndSorttype = '';
			if (isset($post['dateEndSort_type']) && !empty($post['dateEndSort_type'])) {
				if( $post['dateEndSort_type'] == 'element' ){
					$dateEndSorttype = "elements";
				}
				if( $post['dateEndSort_type'] == 'workspace' ){
					$dateEndSorttype = "workspaces";
				}
			}

			$assign_sorting = '';
			if (isset($post['assign_sorting']) && !empty($post['assign_sorting'])) {
				$assign_sorting = $post['assign_sorting'];
			}

			$element_sorting = '';
			if (isset($post['element_sorting']) && !empty($post['element_sorting'])) {
				$element_sorting = $post['element_sorting'];
			}

			$wsp_sorting = '';
			if (isset($post['wsp_sorting']) && !empty($post['wsp_sorting'])) {
				$wsp_sorting = $post['wsp_sorting'];
			}
			//pr($post);
			$selected_dates = null;
			if (isset($post['selectedDates']) && !empty(trim($post['selectedDates']))) {
				$selected_dates = trim($post['selectedDates']);
			}

			$element_title = array();
			if (isset($post['element_title']) && !empty($post['element_title'])) {
				$element_title = $post['element_title'];
			}

			$element_task_type = array();
			if (isset($post['eletasktype']) && !empty($post['eletasktype'])) {
				$element_task_type = implode(",",$post['eletasktype']);
			}


			$filter_projects = $ViewModel->projectDetails($project_id, ['title', 'id', 'start_date', 'end_date', 'color_code']);
			if(isset($post['next_page']) && !empty($post['next_page'])) {
				$next_page = $post['next_page'];
			}
			else{
				$next_page = $page + 1;
			}

			$view->set('prjid', $project_id);
			$view->set('userid', $user_id);
			$view->set('page', $page);
			$view->set('next_page', $next_page);
			$view->set('assigned_reaction', $assigned_reaction);
			$view->set('assigned_user_ids', $assigned_user_ids);
			$view->set('filter_task_staus', $filter_task_staus);
			$view->set('filter_projects', $filter_projects);
			$view->set('dateEndSorttype', $dateEndSorttype);
			$view->set('dateStartSorttype', $dateStartSorttype);

			$view->set('assign_sorting', $assign_sorting);
			$view->set('element_sorting', $element_sorting);
			$view->set('wsp_sorting', $wsp_sorting);
			$view->set('selected_dates', $selected_dates);
			$view->set('element_title', $element_title);
			$view->set('element_task_type', $element_task_type);

		}

		$html = $view->render('get_paging_taskcenter');

		echo json_encode($html);
		exit();
	}


	public function filter_projectss() {
		if ($this->request->isAjax()) {
			$this->layout = false;
			if ($this->request->is('post') || $this->request->is('put')) {

				$views = new View();
				$ViewModel = $views->loadHelper('ViewModel');
				$projects = null;
				$post = $this->request->data;

				$owner = 1;
				$user_ids = '';
				if (isset($post['user_ids']) && !empty($post['user_ids'])) {
					if (count($post['user_ids']) == 1) {
						if ($this->user_id == $post['user_ids'][0]) {
							$owner = null;
						}
					}
					$user_ids = $post['user_ids'][0];
				}

				$projects = $ViewModel->userTotalProjects($user_ids);
				$filter_users = (isset($this->request->data['user_ids'])) ? $this->request->data['user_ids'] : null;
				$named_params = (isset($post['named_params'])) ? $post['named_params'] : null;
				$allprojects = $projects;
				$view = new View($this, false);
				$view->viewPath = 'Dashboards/task_center'; // Directory inside view directory to
				$view->set(compact("filter_users", "allprojects", "named_params"));
				$html = $view->render('filter_projectss');
				echo json_encode($html);
				exit;
			}
		}
	}

	public function task_counts($first = null) {
		if ($this->request->isAjax()) {
			$this->layout = false;
			if ($this->request->is('post') || $this->request->is('put')) {

				$projects = null;
				$views = new View();
				$ViewModel = $views->loadHelper('ViewModel');

				$post = $this->request->data;

				$yes = 1;
				$filter_users = null;
				if (isset($post['ids']) && !empty($post['ids'])) {
					$filter_users = $post['ids'];
				} else if (isset($post['user_ids']) && !empty($post['user_ids'])) {
					$filter_users = $post['user_ids'];
				}

				$user_ids ='';
				if( isset($filter_users) && !empty($filter_users) ){
					$dataUsers = [];
					foreach($filter_users as $users){
						$dataUsers[]=$users;
					}
					$user_ids = implode(",",$dataUsers);
				} else {
					$user_ids = $this->Session->read('Auth.User.id');
				}


				$project_ids = null;
				if (isset($post['project_ids']) && !empty($post['project_ids'])) {
					$project_ids = $post['project_ids'];
				}


				//$allprojects = $ViewModel->userTotalProjects($user_ids);

				$named_params = (isset($post['named_params'])) ? $post['named_params'] : null;



				$owner = 0;
				$view = new View($this, false);
				$view->viewPath = 'Dashboards/task_center'; // Directory inside view directory to
				$view->set(compact("filter_users", "owner", "named_params","project_ids"));
				$html = $view->render('task_counts');
				echo json_encode($html);
				exit;
			}
		}
	}

	public function filtered_ones() {
		if ($this->request->isAjax()) {
			$this->layout = false;
			if ($this->request->is('post') || $this->request->is('put')) {

				$view = new View($this, false);
				$view->viewPath = 'Dashboards/task_center';
				$view->set("filter_users", $this->request->data['user_ids']);
				$view->set("prjid", $this->request->data['project_id']);
				$html = $view->render('filtered_ones');
				echo json_encode($html);
				exit;
			}
		}
	}

	public function filter_wsp_by_projects() {
		if ($this->request->isAjax()) {
			$this->layout = false;
			if ($this->request->is('post') || $this->request->is('put')) {
				$filter_users = isset($this->request->data['user_ids']) ? $this->request->data['user_ids'] : null;
				$filter_projects = $this->request->data['project_ids'];
				$view = new View($this, false);
				$view->viewPath = 'Dashboards/task_center'; // Directory inside view directory to
				$view->set(compact("filter_users", "filter_projects"));
				$html = $view->render('filter_wsp_by_projects');
				echo json_encode($html);
				exit;
			}
		}
	}

	public function filter_task_by_projects($first = null) {
		$html = '';
		if ($this->request->isAjax()) {
			$this->layout = false;
			if ($this->request->is('post') || $this->request->is('put')) {
				$filter_users = isset($this->request->data['user_ids']) ? $this->request->data['user_ids'] : null;
				$filter_projects = $this->request->data['project_ids'];
				$view = new View($this, false);
				$view->viewPath = 'Dashboards/task_center'; // Directory inside view directory to
				$view->set(compact("filter_users", "filter_projects", "first"));
				$html = $view->render('filter_task_by_projects');
			}
			echo json_encode($html);
			exit;
		}
	}

	public function filter_userss() {
		if ($this->request->isAjax()) {
			$this->layout = false;
			if ($this->request->is('post') || $this->request->is('put')) {
				$filter_users = (isset($this->request->data['user_ids'])) ? $this->request->data['user_ids'] : null;
				$owner = (isset($this->request->data['clear'])) ? 1 : 0;
				$view = new View($this, false);
				$view->viewPath = 'Dashboards/task_center'; // Directory inside view directory to
				$view->set("filter_users", $filter_users);
				$view->set("owner", $owner);
				$html = $view->render('filter_userss');
				echo json_encode($html);
				exit;
			}
		}
	}

	/*==========================================================*/


	public function filtered_data_row() {
		if ($this->request->isAjax()) {
			$this->layout = false;
			if ($this->request->is('post') || $this->request->is('put')) {

				$view = new View($this, false);
				$view->viewPath = 'Dashboards/task_center';
				$view->set("filter_users", $this->request->data['filter_users']);
				$view->set("prjid", $this->request->data['project_id']);
				$html = $view->render('filtered_data_row');
				echo json_encode($html);
				exit;
			}
		}
	}
	public function filtered_one() {
		if ($this->request->isAjax()) {
			$this->layout = false;
			if ($this->request->is('post') || $this->request->is('put')) {

				$view = new View($this, false);
				$view->viewPath = 'Dashboards/task_center';
				$view->set("filter_users", $this->request->data['user_ids']);
				$view->set("prjid", $this->request->data['project_id']);
				$html = $view->render('filtered_one');
				echo json_encode($html);
				exit;
			}
		}
	}

/* ***********
 * TASK REMINDER
 *************/

	public function task_reminder($filter = null) {
		$this->layout = 'inner';
		$this->set('title_for_layout', __('Reminders', true));
		$this->set('page_heading', __('Reminders', true));
		$this->set('page_subheading', __('View your Active Reminders', true));
		$viewData = null;

		//$viewData['reminder_elements'] = $this->reminder_elements();
		$params =  array('field'=> 'reminders.reminder_date', 'direction'=>'asc');
		$viewData['params'] = $params;
		$viewData['crumb'] = [
			'last' => [
				'data' => [
					'title' => "Reminders",
					'data-original-title' => "Reminders",
				],
			],
		];
		$viewData['project_id'] = (isset($this->params['named']['project']) && !empty($this->params['named']['project'])) ? $this->params['named']['project'] : 0;
		$viewData['reminder_filter'] = (isset($filter) && !empty($filter)) ? $filter : null;

		$userData = $this->User->find('first', ['conditions' => ['User.id' => $this->user_id], 'recursive' => -1, 'fields' => 'reminder_notification']);
		$viewData['reminder_pop_up'] = false;
		if(!empty($userData['User']['reminder_notification'])) {
			$viewData['reminder_pop_up'] = false;
		}

		$this->set($viewData);
		$this->setJsVar('reminder_filter', $filter);
		$this->setJsVar('project_id', $viewData['project_id']);
	}

	public function trash_task_reminder() {
		$response = ['success' => false];
		if ($this->request->isAjax()) {
			$this->layout = false;
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;

				if (isset($post['id']) && !empty($post['id'])) {

					$condition = array('ReminderSetting.reminder_id' => [$post['id']]);

					if ($this->ReminderSetting->deleteAll($condition, false)) {

						if ($this->Reminder->delete($post['id'])) {
							$response['success'] = true;
						}

					}
				}
			}
		}
		echo json_encode($response);
		exit;
	}

	public function reminder_popup_setting() {
		$response = ['success' => false];
		if ($this->request->isAjax()) {
			$this->layout = false;
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				// pr($post, 1);
				if (isset($post['user_id']) && !empty($post['user_id'])) {

					$detail['User']['id'] = $post['user_id'];
					$detail['User']['reminder_notification'] = $post['setting'];

					if ($this->User->save($detail)) {
						$response['success'] = true;
					}
				}
			}
		}
		echo json_encode($response);
		exit;
	}

	public function delete_reminder() {
		$response = ['success' => false];
		if ($this->request->isAjax()) {
			$this->layout = false;
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				// pr($post, 1);
				if (isset($post['id']) && !empty($post['id'])) {
					$reminder_setting = $this->ReminderSetting->find('first', [
						'conditions' => [
							'ReminderSetting.reminder_id' => $post['id'], 'ReminderSetting.user_id' => $this->user_id,
						],
					]);
					$detail['ReminderSetting']['reminder_id'] = $post['id'];
					$detail['ReminderSetting']['user_id'] = $this->user_id;
					$detail['ReminderSetting']['is_deleted'] = 1;
					if (isset($reminder_setting) && !empty($reminder_setting)) {
						$detail['ReminderSetting']['id'] = $reminder_setting['ReminderSetting']['id'];
					}

					if ($this->ReminderSetting->save($detail)) {
						$response['success'] = true;
					}
				}
			}
		}
		echo json_encode($response);
		exit;
	}

	public function delete_all_reminders() {
		$response = ['success' => false];
		if ($this->request->isAjax()) {
			$this->layout = false;
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				$reminder_elements = $this->reminder_elements();

				if (isset($reminder_elements) && !empty($reminder_elements)) {

					$detail = null;
					foreach ($reminder_elements as $key => $value) {
						$detail[$key]['ReminderSetting']['reminder_id'] = $value['id'];
						$detail[$key]['ReminderSetting']['user_id'] = $this->user_id;
						$detail[$key]['ReminderSetting']['is_deleted'] = 1;
					}
					// pr($detail, 1);
					if (isset($detail) && !empty($detail)) {
						if ($this->ReminderSetting->saveAll($detail)) {
							$response['success'] = true;
						}
					}
				}
			}
		}
		echo json_encode($response);
		exit;
	}

	public function element_reminder($element_id = null, $reminder_id = null) {
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			$html = "";

			if ($this->request->is('post') || $this->request->is('put')) {
				$this->layout = false;
				$post = $this->request->data;

				$response = ['success' => false, 'content' => null];

				$rem_date = $post['Reminder']['reminder_date'];
				$post['Reminder']['reminder_date'] = date('Y-m-d H:i:s', strtotime($post['Reminder']['reminder_date']));

				$element_participants = $el_users = null;
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
				}

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

			$this->render('/Dashboards/task_center/element_reminder');
		}
	}

	public function get_user_reminder() {
		$response = ['success' => false];
		if ($this->request->isAjax()) {
			$this->layout = false;
			$reminder_elements = $this->reminder_elements();

			$view = new View($this, false);
			$view->viewPath = 'Dashboards/partials';
			$view->set("reminder_elements", $reminder_elements);
			$html = $view->render('reminders');
			echo json_encode($html);
			exit;
		}
	}

	public function get_reminder_count() {
		$response = ['success' => false, 'content' => 0];
		if ($this->request->isAjax()) {
			$this->layout = false;
			$reminder_elements = $this->reminder_elements();
			$total_reminders = 0;
			if (isset($reminder_elements) && !empty($reminder_elements)) {
				$total_reminders = count($reminder_elements);
			}
			$response['success'] = true;
			$response['content'] = $total_reminders;
			echo json_encode($response);
			exit();
		}
	}
	public function show_reminder() {
		$response = ['success' => false];
		if ($this->request->isAjax()) {
			$this->layout = false;
			$reminder_elements = $this->reminder_elements();
			$today_reminder_elements = $this->_todays_reminder_elements;
			$overdue_reminder_elements = $this->_overdue_reminder_elements;
			$upcoming_reminder_elements = $this->_upcoming_reminder_elements;

			$this->set('reminder_elements', $reminder_elements);
			$this->set('today_reminder_elements', $today_reminder_elements);
			$this->set('overdue_reminder_elements', $overdue_reminder_elements);
			$this->set('upcoming_reminder_elements', $upcoming_reminder_elements);

			$this->render('/Dashboards/partials/show_reminder');
		}
	}
	public function show_todays_reminder() {
		$response = ['success' => false];
		if ($this->request->isAjax()) {
			$this->layout = false;
			$reminder_elements = $this->_todays_reminder_elements;

			$detail['User']['id'] = $this->user_id;
			$detail['User']['today_reminder'] = 0;
			if ($this->User->save($detail)) {}
			$this->set('reminder_elements', $reminder_elements);
			$this->render('/Dashboards/partials/show_todays_reminder');
		}
	}
	public function update_todays_reminder_setting() {
		$this->layout = false;
		$this->autoRender = false;
		App::import('Model', 'User');
		$user = new User();
		$detail['User']['today_reminder'] = 1;

		$user->updateAll(
			array('User.today_reminder' => 1), //fields to update
			array('User.id !=' => '') //condition
		);

	}

/* ***********
 * JANI
 *************/

	public function open_jani() {
		$response = ['success' => false];
		// if ($this->request->isAjax()) {
			$this->layout = false;

			$viewVars = null;
			$this->set($viewVars);
			$this->render('/Dashboards/partials/jani/open_jani');
		// }
	}

	public function jani_partial() {
		$response = ['success' => false];
		if ($this->request->isAjax()) {
			$this->layout = false;

			$viewVars = null;


			$this->set($viewVars);
			$this->render('/Dashboards/partials/jani/jani_partial');
		}
	}

	public function elementScheduleChangeEmail($elementName = null, $all_owner = null, $element_id = null) {

		$view = new View();
		$commonHelper = $view->loadHelper('Common');

		$all_owner = array_unique(array_filter($all_owner));

		$elementAction = SITEURL.'entities/update_element/'.$element_id.'#tasks';
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
						$email->from(array(ADMIN_FROM_EMAIL => SITENAME));
						$email->to($usersDetails['User']['email']);
						$email->subject(SITENAME . ': Task schedule change');
						$email->template('element_schedule_change_email');
						$email->emailFormat('html');
						$email->viewVars(array('element_name' => $elementName, 'owner_name' => $owner_name, 'changedBy' => $loggedInUser, 'elementAction' => $elementAction));
						$email->send();

					}
				}
			}
		}
	}

/* ***************
 * PROGRAM CENTER
 *****************/
	public function add_program() {

		$this->loadModel('Program');

		$this->set('page_heading', __('Create Program', true));
		$this->set('title_for_layout', __('Create Program', true));
		$this->set('page_subheading', __('Create Programs', true));

		$crumb = [
			'last' => [
				'data' => [
					'title' => "Create Program",
					'data-original-title' => "Create Program",
				],
			],
		];
		$this->set('crumb', $crumb);

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

				if(!empty($this->request->data['Program']) && !empty($this->request->data['Program']['program_name'])) {
					$programArr = [];
					foreach($this->request->data['Program']['program_name'] as $k => $prog_name) {

						$programArr[$k]['Program']['program_name'] = htmlentities($prog_name, ENT_QUOTES);

						//$programArr[$k]['Program']['program_name'] = $this->request->data['Program']['program_name'][$k];

						$programArr[$k]['Program']['user_id'] = $this->Auth->user('id');
						if(trim($prog_name) != '' ) {
							//$this->Program->create();
							//$this->request->data['Program']['program_name'] = $prog_name;
							//$this->request->data['Program']['user_id'] = $this->user_id;
							//$this->Program->save($this->request->data);
						}
					}
					$this->request->data = $programArr;
					//$this->Program->set($this->request->data);
				}

				if (!empty($this->Program->validationErrors)) {

					$response['content'] = $this->Program->validationErrors;
					$response['success'] = false;

				} else if ($this->Program->validates()) {
					if ($this->Program->saveMany($this->request->data)) {
						$response['success'] = true;
					}
				}
			}
			//pr($response); die;
			echo json_encode($response);
			exit;

		} else {
			$this->layout = 'inner';
		}
	}
	public function program_center($project_id = null) {
		$this->redirect(['controller' => 'projects','action' => 'lists']);
		$this->loadModel('Program');
		$this->loadModel('ProjectProgram');
		$this->layout = 'inner';

		$this->set('page_heading', __('My Programs', true));
		$this->set('title_for_layout', __('My Programs', true));
		$this->set('page_subheading', __('View and manage your personal Programs', true));

		$projects = [];
		$programs = $this->Program->find('all', ['conditions' => [
			'Program.user_id' => $this->user_id,
		],
			'fields' => [
				'Program.id',
				'Program.program_name',
			],
			'order' => 'Program.program_name'
		]);

		$program_data = [];
		$conditions['Program.user_id'] = $this->user_id;
		$programs1 = $this->ProjectProgram->find('all', [
			'conditions' => $conditions,
			'fields' => [
				'Program.*',
			],
			'group' => ['Program.id'],
		]);

		if (isset($programs) && !empty($programs)) {
			$projects = Set::extract($programs, '/ProjectProgram/project_id');
			$projects = getByDbIds('Project', $projects, ['id', 'title']);
			$projects = Set::combine($projects, '{n}.Project.id', '{n}.Project.title');
			//$programs = Set::extract($programs, '/Program');
		}

		$program_data_without_proj = [];
		$this->set('program_data', $programs);
		$this->set('programs', $programs);
		$this->set('projects', $projects);
		$this->set('program_data_without_proj', $program_data_without_proj);
		$this->setJsVar('projects', $projects);
		$this->setJsVar('project_id', $project_id);
		$this->set('project_id', $project_id);

		$crumb = [
			'last' => [
				'data' => [
					'title' => "My Programs",
					'data-original-title' => "My Programs",
				],
			],
		];
		$this->set('crumb', $crumb);

	}

	public function project_cards() {
		$response = ['success' => false];
		$this->loadModel('Program');
		$this->loadModel('ProjectProgram');
		if ($this->request->isAjax()) {
			$this->layout = false;

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;

				$search_term = null;
				if (isset($post['s']) && !empty($post['s'])) {
					$search_term = $post['s'];
				}

				$program_id = $post['program_id'];
				$render_file = 'program_partial';
				$program_data = [];
				$ragVal = (isset($post['rag']) && !empty($post['rag'])) ? $post['rag'] : null;
				$conditions['ProjectProgram.program_id'] = $program_id;
				if (isset($post['projects']) && !empty($post['projects'])) {
					$conditions['ProjectProgram.project_id'] = $post['projects'];
				}

				$program_projects = $this->ProjectProgram->find('all', [
					'conditions' => $conditions,
					'recursive' => -1,
				]);
				if (isset($program_projects) && !empty($program_projects)) {
					foreach ($program_projects as $prkey => $prvalue) {
						$prjid = $prvalue['ProjectProgram']['project_id'];
						$rag_status = $this->objView->loadHelper('Common')->getRAG($prjid, true)['rag_color'];
						if (isset($ragVal) && !empty($ragVal)) {
							if ($rag_status == $ragVal) {
								$program_data[$program_id]['Projects'][] = $prvalue['ProjectProgram'];
							}
						} else {
							$program_data[$program_id]['Projects'][] = $prvalue['ProjectProgram'];
						}
					}
				}

				$temp_program_data = null;
				if ((isset($program_data) && !empty($program_data)) && (isset($search_term) && !empty($search_term))) {
					foreach ($program_data as $program_id => $data) {
						$prjdata = $data['Projects'];
						// $temp_program_data[$program_id] = $data;
						$temp_program_data[$program_id]['Projects'] = null;
						if (isset($prjdata) && !empty($prjdata)) {
							foreach ($prjdata as $key => $pdata) {
								$project_id = $pdata['project_id'];
								$project_title = strip_tags(getFieldDetail('Project', $project_id, 'title'));
								if (stripos($project_title, $search_term) !== false) {
									$temp_program_data[$program_id]['Projects'][] = $pdata;
								}
							}
						}
					}
					$program_data = $temp_program_data;
				}
				// pr($program_data);
				$this->set('program_data', $program_data);
				$this->set('post_rag', $ragVal);
				$this->set('program_id', $program_id);
			}
		}
		$this->render('/Dashboards/partials/program_center/project_cards');
	}

	public function filter_program_center() {
		$response = ['success' => false];
		$this->loadModel('Program');
		$this->loadModel('ProjectProgram');
		if ($this->request->isAjax()) {
			$this->layout = false;

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;

				$search_term = null;
				if (isset($post['s']) && !empty($post['s'])) {
					$search_term = $post['s'];
				}

				$render_file = 'program_partial';
				$program_data = [];
				$ragVal = (isset($post['rag']) && !empty($post['rag'])) ? $post['rag'] : null;
				$conditions['Program.user_id'] = $this->user_id;
				if (isset($post['projects']) && !empty($post['projects'])) {
					$conditions['ProjectProgram.project_id'] = $post['projects'];
				}
				$programs = $this->ProjectProgram->find('all', [
					'conditions' => $conditions,
					'fields' => [
						//'Program.*',
					],
					'order' => ['Program.program_name'],
					'group' => ['Program.id'],
				]);
				$selectedIds = [];

				if (isset($programs) && !empty($programs)) {
					foreach ($programs as $pgkey => $pgvalue) {
						$program_projects = program_projects($pgvalue['Program']['id']);
						$program_data[$pgkey]['Program'] = $pgvalue['Program'];
						// pr($pgvalue['Program']);
						if (isset($program_projects) && !empty($program_projects)) {
							foreach ($program_projects as $prkey => $prvalue) {
								if (isset($post['projects']) && !empty($post['projects'])) {
									if (in_array($prvalue['ProjectProgram']['project_id'], $post['projects'])) {
										$prjid = $prvalue['ProjectProgram']['project_id'];
										$rag_status = $this->objView->loadHelper('Common')->getRAG($prjid, true)['rag_color'];
										// e($rag_status);
										if (isset($ragVal) && !empty($ragVal)) {
											if ($rag_status == $ragVal) {
												$program_data[$pgkey]['ProjectProgram'][] = $prvalue['ProjectProgram'];
											}
										} else {
											$program_data[$pgkey]['ProjectProgram'][] = $prvalue['ProjectProgram'];
										}
									}
								} else {
									$prjid = $prvalue['ProjectProgram']['project_id'];
									$rag_status = $this->objView->loadHelper('Common')->getRAG($prjid, true)['rag_color'];
									if (isset($ragVal) && !empty($ragVal)) {
										if ($rag_status == $ragVal) {
											$program_data[$pgkey]['ProjectProgram'][] = $prvalue['ProjectProgram'];
										}
									} else {
										$program_data[$pgkey]['ProjectProgram'][] = $prvalue['ProjectProgram'];
									}
								}
							}
						}
					}
				}

				$temp_program_data = null;
				if ((isset($program_data) && !empty($program_data)) && (isset($search_term) && !empty($search_term))) {
					foreach ($program_data as $program_id => $data) {
						$prjdata = $data['Projects'];
						if (isset($prjdata) && !empty($prjdata)) {
							foreach ($prjdata as $key => $pdata) {
								$project_id = $pdata['project_id'];
								$project_title = strip_tags(getFieldDetail('Project', $project_id, 'title'));
								if (stripos($project_title, $search_term) !== false) {
									$temp_program_data[$program_id] = $data;
								}
							}
						}
					}
					$program_data = $temp_program_data;
				}
				if (isset($program_data) && !empty($program_data)) {
					$selectedIds = Set::classicExtract($program_data, '{n}.Program.id');
				}
				//////////////////////
				if(!empty($selectedIds)) {
					$programs = $this->Program->find('all', ['conditions' => [
						'Program.user_id' => $this->user_id,
						'NOT' => array(
									"Program.id" => $selectedIds
								)
					],
						'fields' => [
							'Program.id',
							'Program.program_name',
						],
					]);
				} else {
					$programs = $this->Program->find('all', ['conditions' => [
						'Program.user_id' => $this->user_id,
						],
						'fields' => [
							'Program.id',
							'Program.program_name',
						],
					]);
				}

				$program_data_without_proj = [];
				if (isset($programs) && !empty($programs)) {
					$programs = Set::extract($programs, '/Program');
					foreach($programs as $k => $prog) {
						if(!array_key_exists($prog['Program']['id'], $program_data)) {
							$program_data_without_proj[] = $prog;
						}
					}
				}
				$program_data_without_proj = array();



				if($ragVal != null || isset($post['projects'])) {
					if(!empty($program_data)) {
						foreach ($program_data as $k => $prg) {
							if(!(isset($prg['ProjectProgram']) && !empty($prg['ProjectProgram']))) {
								unset($program_data[$k]);
							}
						}
					}

					$prog_cnt = ( isset($program_data) && !empty($program_data) ) ? count($program_data) : 0;
				} else {
					$program_data = array_merge($program_data,$programs);
					usort($program_data, array($this, 'date_compare'));
					$prog_cnt = ( isset($programs) && !empty($programs) ) ? count($programs) : 0;
				}
				//pr($program_data);
				//pr($programs);die;
				$render_file = 'filter_program_center';
				$this->set('program_data', $program_data);
				$this->set('programs', $programs);
				$this->set('prog_cnt', $prog_cnt);
				$this->set('program_data_without_proj', $program_data_without_proj);
				$this->set('post_rag', $ragVal);
			}
		}
		$this->render('/Dashboards/partials/program_center/' . $render_file);
	}
	function date_compare($a, $b) {
		return strcasecmp($a['Program']["program_name"], $b['Program']["program_name"]);
	}
	public function program_search() {
		$response = ['success' => false];
		$this->loadModel('Program');
		$this->loadModel('ProjectProgram');
		$render_file = 'program_search';
		if ($this->request->isAjax()) {
			$this->layout = false;

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;

				$search_term = null;
				if (isset($post['s']) && !empty($post['s'])) {
					$search_term = $post['s'];
				}

				$program_data = [];
				$ragVal = (isset($post['rag']) && !empty($post['rag'])) ? $post['rag'] : null;
				$conditions['Program.user_id'] = $this->user_id;
				if (isset($post['projects']) && !empty($post['projects'])) {
					$conditions['ProjectProgram.project_id'] = $post['projects'];
				}
				$programs = $this->ProjectProgram->find('all', [
					'conditions' => $conditions,
					'fields' => [
						'Program.*',
					],
					'order' => ['Program.program_name'],
					'group' => ['Program.id'],
				]);

				if (isset($programs) && !empty($programs)) {
					foreach ($programs as $pgkey => $pgvalue) {
						$program_projects = program_projects($pgvalue['Program']['id']);//pr($program_projects);
						$program_data[$pgkey]['Program'] = $pgvalue['Program'];
						// pr($pgvalue['Program']);
						if (isset($program_projects) && !empty($program_projects)) {
							foreach ($program_projects as $prkey => $prvalue) {
								if (isset($post['projects']) && !empty($post['projects'])) {
									if (in_array($prvalue['ProjectProgram']['project_id'], $post['projects'])) {
										$prjid = $prvalue['ProjectProgram']['project_id'];
										$rag_status = $this->objView->loadHelper('Common')->getRAG($prjid, true)['rag_color'];
										// e($rag_status);
										if (isset($ragVal) && !empty($ragVal)) {
											if ($rag_status == $ragVal) {
												$program_data[$pgkey]['ProjectProgram'][] = $prvalue['ProjectProgram'];
											}
										} else {
											$program_data[$pgkey]['ProjectProgram'][] = $prvalue['ProjectProgram'];
										}
									}
								} else {
									$prjid = $prvalue['ProjectProgram']['project_id'];
									$rag_status = $this->objView->loadHelper('Common')->getRAG($prjid, true)['rag_color'];
									if (isset($ragVal) && !empty($ragVal)) {
										if ($rag_status == $ragVal) {
											$program_data[$pgkey]['ProjectProgram'][] = $prvalue['ProjectProgram'];
										}
									} else {
										$program_data[$pgkey]['ProjectProgram'][] = $prvalue['ProjectProgram'];
									}
								}
							}
						}
					}
				}

				$temp_program_data = null;
				if ((isset($program_data) && !empty($program_data)) && (isset($search_term) && !empty($search_term))) {
					foreach ($program_data as $program_id => $data) {
						if(isset($data['ProjectProgram']) && !empty($data['ProjectProgram'])) {
							$prjdata = $data['ProjectProgram'];
							$temp_program_data[$program_id] = $data;
							$temp_program_data[$program_id]['ProjectProgram'] = null;
							if (isset($prjdata) && !empty($prjdata)) {
								foreach ($prjdata as $key => $pdata) {
									$project_id = $pdata['project_id'];
									$project_title = strip_tags(getFieldDetail('Project', $project_id, 'title'));
									if (stripos($project_title, $search_term) !== false) {
										$temp_program_data[$program_id]['ProjectProgram'][] = $pdata;
									}
								}
							}
						}
					}
					$program_data = $temp_program_data;
				}

				$program_count = 0;
				$selectedIds = array();
				if ( isset($program_data) && !empty($program_data) ) {
					$selectedIds = Set::classicExtract($program_data, '{n}.Program.id');
					foreach ($program_data as $program_id => $data) {
						if(isset($data['ProjectProgram']) && !empty($data['ProjectProgram'])) {
							$program_count++;
						}
					}
				}
				if($ragVal != null || isset($post['projects']) || !is_null($search_term)) {
					if(!empty($program_data)) {
						foreach ($program_data as $k => $prg) {
							if(!(isset($prg['ProjectProgram']) && !empty($prg['ProjectProgram']))) {
								unset($program_data[$k]);
							}
						}
					}

					$prog_cnt = ( isset($program_data) && !empty($program_data) ) ? count($program_data) : 0;
				} else {
					$newconditions['Program.user_id'] = $this->user_id;
					if ( isset($program_data) && !empty($program_data) && count($program_data) > 0) {
						$allprograms = $this->Program->find('all', ['conditions' => [
							'Program.user_id' => $this->user_id,
							'NOT' => array(
										"Program.id" => $selectedIds
									)
						],
							'fields' => [
								'Program.id',
								'Program.program_name',
							],
						]);
					}else {
						$allprograms = $this->Program->find('all', [
							'conditions' => [
								'Program.user_id' => $this->user_id,
							],
							'fields' => [
								'Program.id',
								'Program.program_name',
							],
							'order' => 'Program.program_name'
						]);
					};
					$program_data = array_merge($program_data,$allprograms);
					usort($program_data, array($this, 'date_compare'));
					$prog_cnt = ( isset($programs) && !empty($programs) ) ? count($programs) : 0;
				}
				$this->set('program_data', $program_data);
				$this->set('post_rag', $ragVal);
				$this->set('program_count', $program_count);
			}
		}
		$this->render('/Dashboards/partials/program_center/' . $render_file);
	}

	public function program_center_costs() {
		$response = ['success' => false, 'content' => null];
		$this->loadModel('Program');
		$this->loadModel('ProjectProgram');
		if ($this->request->isAjax()) {
			$this->layout = false;

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;

				$this->set('prg_prj_id', $post['projects']);
				$response['success'] = true;
			}
		}
		$this->render('/Dashboards/partials/program_center/program_center_costs');
	}

	public function program_center_charts() {
		$response = ['success' => false, 'content' => null];
		$this->loadModel('Program');
		$this->loadModel('ProjectProgram');
		if ($this->request->isAjax()) {
			$this->layout = false;

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				// pr($post['projects'], 1);

				$this->set('selected_project_id', $post['selected_project_id']);
				$this->set('prg_prj_id', $post['projects']);
				$this->set('program_id', $post['program_id']);
				$response['success'] = true;
			}
		}
		$this->render('/Dashboards/partials/program_center/program_center_charts');
	}

	public function all_risk_charts() {
		$response = ['success' => false, 'content' => null];
		$this->loadModel('Program');
		$this->loadModel('ProjectProgram');
		if ($this->request->isAjax()) {
			$this->layout = false;

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				// pr($post, 1);

				$this->set('selected_project_id', $post['selected_project_id']);
				$this->set('prg_prj_id', json_decode($post['projects']));
				$this->set('program_id', $post['program_id']);
				$response['success'] = true;
			}
		}
		$this->render('/Dashboards/partials/program_center/all_risk_charts');
	}

	public function my_risk_charts() {
		$response = ['success' => false, 'content' => null];
		$this->loadModel('Program');
		$this->loadModel('ProjectProgram');
		if ($this->request->isAjax()) {
			$this->layout = false;

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				// pr($post, 1);

				$this->set('selected_project_id', $post['selected_project_id']);
				$this->set('prg_prj_id', json_decode($post['projects']));
				$this->set('program_id', $post['program_id']);
				$response['success'] = true;
			}
		}
		$this->render('/Dashboards/partials/program_center/my_risk_charts');
	}

	public function get_program_projects() {
		$response = ['success' => false, 'content' => null];
		$this->loadModel('Program');
		$this->loadModel('ProjectProgram');
		if ($this->request->isAjax()) {
			$this->layout = false;

			if ($this->request->is('post') || $this->request->is('put')) {
				$projects = [];
				$programs = $this->Program->find('all', ['conditions' => [
					'Program.user_id' => $this->user_id,
				],
					'fields' => [
						'Program.id',
						'Program.program_name',
					],
				]);
				if (isset($programs) && !empty($programs)) {
					$projects = Set::extract($programs, '/ProjectProgram/project_id');
					$projects = getByDbIds('Project', $projects, ['id', 'title']);
					$projects = Set::combine($projects, '{n}.Project.id', '{n}.Project.title');
				}
				$this->set('projects', $projects);
				$this->setJsVar('projects', $projects);
				$response['success'] = true;
				$response['content'] = $projects;
			}
		}
		echo json_encode($response);
		exit();
	}

	public function get_program_count() {
		$response = ['success' => false, 'content' => null];
		$this->loadModel('Program');
		if ($this->request->isAjax()) {
			$this->layout = false;

			if ($this->request->is('post') || $this->request->is('put')) {
				$programCnt = $this->Program->find('count', [
					'conditions' => [
						'Program.user_id' => $this->user_id,
					]
				]);

				$response['success'] = true;
				$response['content'] = (isset($programCnt) && !empty($programCnt)) ? $programCnt : 0;
			}
		}
		echo json_encode($response);
		exit();
	}

/* ***********
 * Work Manager
 *************/
	public function work_manager($project_id = null) {
		$this->layout = 'inner';
		$this->set('title_for_layout', __('Work Manager', true));
		$this->set('page_heading', __('Work Manager', true));
		$this->set('page_subheading', __('Remove users', true));
		$viewData = null;

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

		$viewData['projects'] = $projects;

		$viewData['named_params'] = (isset($this->params['named']['status']) && !empty($this->params['named']['status'])) ? $this->params['named']['status'] : 0;
		$this->setJsVar('named_params', $viewData['named_params']);

		$viewData['project_id'] = (isset($project_id) && !empty($project_id)) ? $project_id : false;
		$this->setJsVar('project_id', $viewData['project_id']);

		$viewData['crumb'] = [
			'last' => [
				'data' => [
					'title' => "Work Manager",
					'data-original-title' => "Work Management",
				],
			],
		];

		$userProjects = $this->user_projects();
		$this->set('userprojects', $userProjects);

		$this->set($viewData);
		$current_user_id = $this->Session->read('Auth.User.id');
		$this->setJsVar('current_user_id', $current_user_id);
	}

	public function filtered_data_wm() {
		if ($this->request->isAjax()) {
			$this->layout = false;
			if ($this->request->is('post') || $this->request->is('put')) {
				App::import('Controller', 'Users');
				$Users = new UsersController;
				$projects = null;

				$post = $this->request->data;
				$owner = 1;
				if (isset($post['user_ids']) && !empty($post['user_ids'])) {
					if (count($post['user_ids']) == 1) {
						if ($this->user_id == $post['user_ids'][0]) {
							$owner = null;
						}
					}
				}
				if (isset($post['project_ids']) && !empty($post['project_ids'])) {
					$projects = array_flip($post['project_ids']);
				}
				$filter_users = (isset($post['user_ids'])) ? $post['user_ids'] : null;
				$named_params = (isset($post['named_params'])) ? $post['named_params'] : null;

				pr($filter_users);die;

				$filter_projects = $projects;
				$view = new View($this, false);
				$view->viewPath = 'Dashboards/work_manager';
				$view->set(compact("filter_users", "filter_projects", "named_params"));
				$html = $view->render('filtered_data_wm');
				echo json_encode($html);
				exit;
			}
		}
	}
	public function filter_users_wm() {
		if ($this->request->isAjax()) {
			$this->layout = false;
			if ($this->request->is('post') || $this->request->is('put')) {
				$filter_users = (isset($this->request->data['user_ids'])) ? $this->request->data['user_ids'] : null;
				$owner = (isset($this->request->data['clear'])) ? 1 : 0;
				$view = new View($this, false);
				$view->viewPath = 'Dashboards/work_manager'; // Directory inside view directory to
				$view->set("filter_users", $filter_users);
				$view->set("owner", $owner);
				$html = $view->render('filter_users');
				echo json_encode($html);
				exit;
			}
		}
	}
	public function filter_projects_wm() {
		if ($this->request->isAjax()) {
			$this->layout = false;
			if ($this->request->is('post') || $this->request->is('put')) {
				App::import('Controller', 'Users');
				$Users = new UsersController;
				$projects = null;
				$post = $this->request->data;
				// pr($post);
				$owner = 1;
				if (isset($post['user_ids']) && !empty($post['user_ids'])) {
					if (count($post['user_ids']) == 1) {
						if ($this->user_id == $post['user_ids'][0]) {
							$owner = null;
						}
					}
				}
				// pr($owner, 1 );
				$myprojectlist = $myreceivedprojectlist = $mygroupprojectlist = array();
				// Find All current user's projects
				$myprojectlist = $Users->__myproject_selectbox($this->user_id);
				// Find All current user's received projects
				$myreceivedprojectlist = $Users->__receivedproject_selectbox($this->user_id, $owner);
				// Find All current user's group projects
				$mygroupprojectlist = $Users->__groupproject_selectbox($this->user_id, $owner);
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
				$filter_users = (isset($this->request->data['user_ids'])) ? $this->request->data['user_ids'] : null;
				$named_params = (isset($post['named_params'])) ? $post['named_params'] : null;
				$allprojects = $projects;
				$view = new View($this, false);
				$view->viewPath = 'Dashboards/work_manager'; // Directory inside view directory to
				$view->set(compact("filter_users", "allprojects", "named_params"));
				$html = $view->render('filter_projects');
				echo json_encode($html);
				exit;
			}
		}
	}

	public function filteredprojects_wm() {
		if ($this->request->isAjax()) {
			$this->layout = false;
			if ($this->request->is('post') || $this->request->is('put')) {
				App::import('Controller', 'Users');
				$Users = new UsersController;
				$projects = null;
				$post = $this->request->data;
				//pr($post);
				$owner = 1;
				// pr($owner, 1 );
				$myprojectlist = $myreceivedprojectlist = $mygroupprojectlist = array();
				$projects1 = array();

				if (isset($post['ids']) && !empty($post['ids'])) {
					if (count($post['ids']) > 0) {

						foreach ($post['ids'] as $userids) {

							$this->user_id = $userids;
							$myprojectlist = array();
							$myreceivedprojectlist = array();
							$mygroupprojectlist = array();

							// Find All current user's projects
							$myprojectlist = $Users->__myproject_selectbox($this->user_id);
							// Find All current user's received projects
							$myreceivedprojectlist = $Users->__receivedproject_selectbox($this->user_id, $owner);
							// Find All current user's group projects
							$mygroupprojectlist = $Users->__groupproject_selectbox($this->user_id, $owner);

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
						}
						//}
					}
				} else {

					$myprojectlist = array();
					$myreceivedprojectlist = array();
					$mygroupprojectlist = array();

					// Find All current user's projects
					$myprojectlist = $Users->__myproject_selectbox($this->user_id);
					// Find All current user's received projects
					$myreceivedprojectlist = $Users->__receivedproject_selectbox($this->user_id, $owner);
					// Find All current user's group projects
					$mygroupprojectlist = $Users->__groupproject_selectbox($this->user_id, $owner);

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

				}

				$projects = array_map("strip_tags", $projects1);
				$projects = array_map("trim", $projects);
				natcasesort($projects);

				$filter_users = (isset($this->request->data['user_ids'])) ? $this->request->data['user_ids'] : null;
				$named_params = (isset($post['named_params'])) ? $post['named_params'] : null;
				$allprojects = $projects;
				$view = new View($this, false);
				$view->viewPath = 'Dashboards/work_manager'; // Directory inside view directory to
				$view->set(compact("filter_users", "allprojects", "named_params"));
				$html = $view->render('filteredprojects_wm');
				echo json_encode($html);
				exit;
			}
		}
	}

	public function task_count_wm($first = null) {
		if ($this->request->isAjax()) {
			$this->layout = false;
			if ($this->request->is('post') || $this->request->is('put')) {
				App::import('Controller', 'Users');
				$Users = new UsersController;
				$projects = null;
				$post = $this->request->data;
				$yes = 1;
				$filter_users = null;
				if (isset($post['ids']) && !empty($post['ids'])) {
					$filter_users = $post['ids'];
				} else if (isset($post['user_ids']) && !empty($post['user_ids'])) {
					$filter_users = $post['user_ids'];
				}
				$owner = 1;
				if (isset($post['ids']) && !empty($post['ids'])) {
					if (count($post['ids']) == 1) {
						if ($this->user_id == $post['ids'][0]) {
							$owner = null;
						}
					}
				} else if (isset($post['clear']) && !empty($post['clear'])) {
					$owner = null;
				}
				$myprojectlist = $myreceivedprojectlist = $mygroupprojectlist = array();
				// Find All current user's projects
				$myprojectlist = $Users->__myproject_selectbox($this->user_id);
				// Find All current user's received projects
				$myreceivedprojectlist = $Users->__receivedproject_selectbox($this->user_id, $owner);
				// Find All current user's group projects
				$mygroupprojectlist = $Users->__groupproject_selectbox($this->user_id, $owner);
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
				$allprojects = array_keys($projects);
				$newProjects = null;
				if (isset($post['project_ids']) && !empty($post['project_ids'])) {
					$allprojects = $post['project_ids'];
				}
				$named_params = (isset($post['named_params'])) ? $post['named_params'] : null;

				$view = new View($this, false);
				$view->viewPath = 'Dashboards/work_manager'; // Directory inside view directory to
				$view->set(compact("filter_users", "allprojects", "owner", "named_params"));
				$html = $view->render('task_count_wm');
				echo json_encode($html);
				exit;
			}
		}
	}

	public function filtered_user_remove() {
		if ($this->request->isAjax()) {
			$this->layout = false;
			if ($this->request->is('post') || $this->request->is('put')) {
				App::import('Controller', 'Users');
				$Users = new UsersController;
				$projects = null;

				$post = $this->request->data;

				$owner = 1;
				if (isset($post['user_ids']) && !empty($post['user_ids'])) {
					if (count($post['user_ids']) == 1) {
						if ($this->user_id == $post['user_ids'][0]) {
							$owner = null;
						}
					}
				}
				if (isset($post['project_ids']) && !empty($post['project_ids'])) {
					$projects = array_flip($post['project_ids']);
				}
				$filter_users = (isset($post['user_ids'])) ? $post['user_ids'] : null;
				$named_params = (isset($post['named_params'])) ? $post['named_params'] : null;

				//pr($filter_users);die;

				$filter_projects = $projects;
				$view = new View($this, false);
				$view->viewPath = 'Dashboards/work_manager';
				$view->set(compact("filter_users", "filter_projects", "named_params"));
				$html = $view->render('filtered_user_remove');
				echo json_encode($html);
				exit;
			}
		}
	}

	public function user_projects() {

		//My Projects
		$this->Project->unbindModel(['hasMany' => ['ProjectPermission']]);
		$projects = $this->Project->UserProject->find('list', array('conditions' => ['UserProject.user_id' => $this->user_id, 'UserProject.owner_user' => 1], 'recursive' => 1, 'fields' => ['Project.id', 'Project.title']));

		$allproject[] = (isset($projects) && !empty($projects)) ? $projects : null;

		//Received Project
		$rec_projects = get_rec_projects($this->user_id, 1);
		$allproject[] = (isset($rec_projects) && !empty($rec_projects)) ? $rec_projects : null;

		//Group Received Projects
		$grp_projects = group_rec_projects($this->user_id, 1);

		$allproject[] = (isset($grp_projects) && !empty($grp_projects)) ? $grp_projects : null;

		$userprojects = array();
		if (isset($allproject) && !empty($allproject) && count($allproject) > 0) {
			foreach ($allproject as $projectid) {
				if (isset($projectid) && !empty($projectid) && count($projectid) > 0) {
					foreach ($projectid as $key => $pid) {
						if (isset($pid) && !empty($pid)) {
							$userprojects[] = $key;
						}
					}
				}
			}
		}
		return $userprojects;
	}

	/*============ New function for Task Center Page ============== */
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
			$this->render('/Dashboards/task_center/task_list_el_date_cost');
		}
	}
	public function task_list_el_date_depend($element_id = null) {
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
			$this->render('/Dashboards/task_center/task_list_el_date_depend');
		}
	}

	public function getAssignUsers(){
		if ($this->request->isAjax()) {
			$this->layout = false;
			$response = null;

			if ($this->request->is('post') || $this->request->is('put')) {
				if( isset($this->request->data['project_ids']) && !empty($this->request->data['project_ids']) && isset($this->request->data['user_id']) && !empty($this->request->data['user_id']) ){
					$projects = implode(",",$this->request->data['project_ids']);
					$user_id = implode(",",$this->request->data['user_id']);
					$assign_lists = $this->objView->loadHelper('Permission')->elementAssignedUser($projects,$user_id);
					$assigned_user = '';
					if(isset( $_POST['assigned'] ) && !empty($_POST['assigned'])){
						$assigned_user = $_POST['assigned'];
					}

					// $this->set('assign_lists', $assign_lists);
					// $this->render('/Dashboards/task_center/assign_users_lists');
					$view = new View($this, false);
					$view->viewPath = 'Dashboards/task_center';
					$view->set('assign_lists', $assign_lists);
					$view->set('assigned_user', $assigned_user);
					$html = $view->render('assign_users_lists');
					echo json_encode($html);
					exit();
				} else {
					$html = '';
					echo json_encode($html);
					exit();
				}
			}
		}
	}

	public function tasklists() {
		$this->layout = false;
		$view = new View($this, false);
		$view->viewPath = 'Dashboards/partials';
		$ViewModel = $view->loadHelper('ViewModel');

		if ($this->request->is('post') || $this->request->is('put')) {

			$post = $this->request->data;
			$project_id = $post['project'];
			$user_id = null;
			$filter_users = null;

			if(isset($post['user_id']) && !empty($post['user_id'])) {
				$filter_users = $post['user_id'];
			}
			if(isset($post['user_id']) && !empty($post['user_id'])) {
				$user_id = $post['user_id'];
			}
			$assigned_user_ids = '';
			if (isset($post['assigned_userid']) && !empty($post['assigned_userid'])) {
				$assigned_user_ids = $post['assigned_userid'][0];
			}

			$filter_task_staus = array();
			if (isset($post['task_status']) && !empty($post['task_status'])) {
				$filter_task_staus = $post['task_status'];
			}

			$assigned_reaction = '';
			if (isset($post['assigned_status']) && !empty($post['assigned_status'])) {

				if( $post['assigned_status'] == 'assigned0' ){
					$assigned_reaction = 5;
				} else if( $post['assigned_status'] == 'assigned1' ){
					$assigned_reaction = 1;
				} else if( $post['assigned_status'] == 'assigned2' ){
					$assigned_reaction = 2;
				} else if( $post['assigned_status'] == 'assigned3' ){
					$assigned_reaction = 3;
				} else if( $post['assigned_status'] == 'assigned4' ){
					$assigned_reaction = 4;
				}

			}

			$dateStartSorttype = '';
			if (isset($post['dateStartSort_type']) && !empty($post['dateStartSort_type'])) {
				if( $post['dateStartSort_type'] == 'element' ){
					$dateStartSorttype = "elements";
				}
				if( $post['dateStartSort_type'] == 'workspace' ){
					$dateStartSorttype = "workspaces";
				}
			}

			$dateEndSorttype = '';
			if (isset($post['dateEndSort_type']) && !empty($post['dateEndSort_type'])) {
				if( $post['dateEndSort_type'] == 'element' ){
					$dateEndSorttype = "elements";
				}
				if( $post['dateEndSort_type'] == 'workspace' ){
					$dateEndSorttype = "workspaces";
				}
			}

			$assign_sorting = '';
			if (isset($post['assign_sorting']) && !empty($post['assign_sorting'])) {
				$assign_sorting = $post['assign_sorting'];
			}

			$element_sorting = '';
			if (isset($post['element_sorting']) && !empty($post['element_sorting'])) {
				$element_sorting = $post['element_sorting'];
			}

			$wsp_sorting = '';
			if (isset($post['wsp_sorting']) && !empty($post['wsp_sorting'])) {
				$wsp_sorting = $post['wsp_sorting'];
			}
			//pr($post);
			$selected_dates = null;
			if (isset($post['selectedDates']) && !empty(trim($post['selectedDates']))) {
				$selected_dates = trim($post['selectedDates']);
			}

			$element_title = array();
			if (isset($post['element_title']) && !empty($post['element_title'])) {
				$element_title = $post['element_title'];
			}

			$element_task_type = array();
			if (isset($post['eletasktype']) && !empty($post['eletasktype'])) {

				$element_task_type = implode(",",$post['eletasktype']);

			}

			$filter_projects = $ViewModel->projectDetails($project_id, ['title', 'id', 'start_date', 'end_date', 'color_code']);


			$view->set('prjid', $project_id);
			$view->set('userid', $user_id);
			$view->set('assigned_reaction', $assigned_reaction);
			$view->set('assigned_user_ids', $assigned_user_ids);
			$view->set('filter_task_staus', $filter_task_staus);
			$view->set('filter_projects', $filter_projects);
			$view->set('dateEndSorttype', $dateEndSorttype);
			$view->set('dateStartSorttype', $dateStartSorttype);
			$view->set('assign_sorting', $assign_sorting);
			$view->set('element_sorting', $element_sorting);
			$view->set('wsp_sorting', $wsp_sorting);
			$view->set('selected_dates', $selected_dates);
			$view->set('element_title', $element_title);
			$view->set('element_task_type', $element_task_type);
		}

		$html = $view->render('get_tasktype_data');
		echo json_encode($html);
		exit();
	}

	function projecttasktype(){

		if ($this->request->isAjax()) {
			$this->layout = false;
			$response = null;

			if ($this->request->is('post') || $this->request->is('put')) {
				if( isset($this->request->data['project_ids']) && !empty($this->request->data['project_ids']) && isset($this->request->data['user_id']) && !empty($this->request->data['user_id']) ){

					$projects = implode(",",$this->request->data['project_ids']);
					$user_id = $this->request->data['user_id'];
					$tasktype_lists = $this->objView->loadHelper('Permission')->projectTaskType($projects,$user_id);

					$view = new View($this, false);
					$view->viewPath = 'Dashboards/task_center';
					$view->set('tasktype_lists', $tasktype_lists);
					$html = $view->render('projecttasktype');
					echo json_encode($html);
					exit();
				}
			}
		}

	}


	/*==============================================================*/

	function task_reminder_sort(){

		if ($this->request->isAjax()) {
			$this->layout = false;
			$response = null;

			if ($this->request->is('post') || $this->request->is('put')) {

					$parms = array();
					$field =  'reminders.reminder_date';
					$direction =  'asc';
					if( isset($this->request->data['field']) && !empty($this->request->data['field']) ){
						$field = 'reminders.'.$this->request->data['field'];
					}
					if( isset($this->request->data['direction']) && !empty($this->request->data['direction']) ){
						$direction = $this->request->data['direction'];
					}
					$params = array('field'=>$field, 'direction'=>$direction);

					$view = new View($this, false);
					$view->viewPath = 'Dashboards/partials';
					$view->set('params', $params);
					$html = $view->render('reminders');
					echo json_encode($html);
					exit();

			}
		}

	}


}