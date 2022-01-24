<?php
App::uses('Controller/Component', 'Auth', 'Session', 'RequestHandler');
App::uses('Sanitize', 'Utility');
App::uses('CakeEmail', 'Network/Email');

class RewardsController extends AppController {

	public $name = 'Rewards';

	public $uses = [
		'User',
		'UserDetail',
		'Project',
		'Element',
		'UserProject',
		'RewardOptedSetting',
		'RewardAccelerate',
		'RewardAssignment',
		'RewardSetting',
		'RewardAccelerationHistory',
		'RewardHistory',
		'RewardCharity',
		'RewardRedeem',
		'RewardOffer',
		'RewardOfferShop',
		'RewardUserAcceleration',
		'ProjectWorkspace',
		'Workspace',
		'RmDetail',
		'DoList',
		'DoListUser',
	];

	public $objView = null;
	public $user_id = null;
	public $reward_types = null;
	public $myprojects = null;
	public $accepted = false;
	public $notify = true;
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
		$this->accepted = true;
		$this->notify = true;

		$this->set('notify', $this->notify);

		$view = new View();
		$this->objView = $view;

		$this->reward_types = [
			'project' => 'Project',
			'workspace' => 'Workspace',
			'task' => 'Task',
			'risk' => 'Risk',
			'todo' => 'To-do',
			'subtodo' => 'Sub To-do',
			'other' => 'Other',
		];
		$this->set('reward_types', $this->reward_types);
		if(isset($this->user_id) && !empty($this->user_id)){
			// $this->myprojects = $this->getAllProjects($this->user_id);
		}

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
		$this->set('title_for_layout', __('Reward Center', true));
		$this->set('page_heading', __('Reward Center', true));
		$this->set('page_subheading', __('Rewards to recognize work effort in Projects', true));

		$viewData = $allUsersList = [];
		$current_user_id = $this->Session->read('Auth.User.id');

		$allProjects = $this->getAllProjects($current_user_id, 1);
		$projectUsers = $this->objView->loadHelper('TaskCenter')->userByProject(array_keys($allProjects));
		if (isset($projectUsers) && !empty($projectUsers)) {
			$projectUsers = array_unique($projectUsers['all_project_user']);
			$allUsers = $this->objView->loadHelper('TaskCenter')->user_exists($projectUsers);
			if (isset($allUsers) && !empty($allUsers)) {
				if (($key = array_search($current_user_id, $allUsers)) !== false) {
					unset($allUsers[$key]);
				}
			}
			$allUsersList = $this->objView->loadHelper('Common')->usersFullname($allUsers);
			$allUsersList = Set::combine($allUsersList, '/UserDetail/user_id', '/UserDetail/full_name');
		}
		$viewData['allUsersList'] = $allUsersList;

		$myProjects = $this->getAllProjects($this->user_id, 1);
		$viewData['my_projects'] = $myProjects;

		$this->set($viewData);

		$crumb = [
			'last' => [
				'data' => [
					'title' => 'Reward Center',
					'data-original-title' => 'Reward Center',
				],
			],
		];

		$this->set('crumb', $crumb);

	}

//****************** XHR Functions *********************//

	public function top_section() {

		if ($this->request->isAjax()) {

			$this->layout = false;
			$viewData = null;
			$conditions = [];
			$project_ids = null;
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;

				$viewData['user_id'] = $user_id = (isset($post['user_id']) && !empty($post['user_id'])) ? $post['user_id'] : $this->user_id;

				$allProjects = $this->getAllProjects($this->user_id);

				$all_projects = $allProjects;

				$ownerProjects = null;
				if(isset($user_id) && !empty($user_id)){
					$ownerProjects = $this->getAllProjects($user_id);
				}

				$optData = $this->get_user_opt_setting($user_id);
				$viewData['optData'] = $optData;

				$viewData['user_projects'] = $ownerProjects;
				$viewData['my_projects'] = $allProjects;
			}

			$view = new View($this, false);
			$view->viewPath = 'Rewards/partials';
			$view->set($viewData);
			$html = $view->render('top_section');

			echo json_encode($html);
			exit();

		}
	}

	public function user_detail($user_id = null) {

		if ($this->request->isAjax()) {

			$this->layout = false;
			$viewData = null;
			$conditions = [];
			$project_ids = null;

			$viewData['user_id'] = $user_id = (isset($user_id) && !empty($user_id)) ? $user_id : $this->user_id;

			$allProjects = $this->getAllProjects($this->user_id);

			$all_projects = $allProjects;

			$ownerProjects = null;
			if(isset($user_id) && !empty($user_id)){
				$ownerProjects = $this->getAllProjects($user_id);
			}

			$viewData['user_projects'] = $ownerProjects;
			$viewData['my_projects'] = $allProjects;

			$view = new View($this, false);
			$view->viewPath = 'Rewards/partials';
			$view->set($viewData);
			$html = $view->render('user_detail');

			echo json_encode($html);
			exit();

		}
	}

	public function user_projects($user_id) {
		if ($this->request->isAjax()) {

			$this->layout = false;
			$viewData = null;
			$viewData['user_id'] = $user_id;
			$allProjects = $this->getAllProjects($this->user_id);

			$alluserarray = $this->objView->loadHelper('TaskCenter')->userByProject(array_keys($allProjects));

			$all_projects = $projects = $selected_user_projects = [];
			if ($this->user_id != $user_id) {
				$gg = two_user_common_projects($this->user_id, $user_id);
				foreach ($alluserarray['project_by_user'] as $key => $value) {
					foreach ($value as $k => $v) {
						if ($v == $user_id) {
							$selected_user_projects[$key] = $key;
						}
					}
				}
				$projects = (isset($selected_user_projects) && !empty($selected_user_projects)) ? array_unique($selected_user_projects) : null;
				// pr($projects, 1);
			} else {
				$projects = array_keys($allProjects);
			}

			if (isset($projects) && !empty($projects)) {
				$all_project = $this->Project->find('list', ['conditions' => ['Project.id' => $projects]]);
				foreach ($all_project as $key => $value) {
					if (is_project_rewarded($key)) {
						$all_projects[$key] = ucfirst($value);
					}
				}
				asort($all_projects);
			}

			$viewData['allProjects'] = $all_projects;
			$viewData['user_id'] = $user_id;

			$ownerProjects = null;
			if($user_id != $this->user_id) {
				$ownerProjects = $this->getAllProjects($user_id);
			}

			$myProjects = $all_projects;
			$viewData['user_projects'] = $ownerProjects;
			$viewData['my_projects'] = $myProjects;

			$view = new View($this, false);
			$view->viewPath = 'Rewards/partials';
			$view->set($viewData);
			$html = $view->render('user_projects');

			echo json_encode($html);
			exit();

		}
	}

	public function user_earned_points() {
		if ($this->request->isAjax()) {

			$this->layout = false;
			$viewData = null;
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				$viewData['user_id'] = $user_id = $post['user_id'];

				$ownerProjects = [];
				if($this->user_id != $user_id) {
					$ownerProjects = $this->getAllProjects($user_id);
				}
				$myProjects = $this->getAllProjects($this->user_id);
				// $myProjects = $this->getAllProjects($this->user_id);
				$viewData['user_projects'] = $ownerProjects;
				$viewData['my_projects'] = $myProjects;
			}

			$view = new View($this, false);
			$view->viewPath = 'Rewards/partials';
			$view->set($viewData);
			$html = $view->render('user_earned_points');

			echo json_encode($html);
			exit();

		}
	}

	public function user_redeem_points() {
		if ($this->request->isAjax()) {

			$this->layout = false;
			$viewData = null;
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				$viewData['user_id'] = $user_id = $post['user_id'];

				$ownerProjects = [];
				if($this->user_id != $user_id) {
					$ownerProjects = $this->getAllProjects($user_id);
				}
				$myProjects = $this->getAllProjects($this->user_id);
				// $myProjects = $this->getAllProjects($this->user_id);
				$viewData['user_projects'] = $ownerProjects;
				$viewData['my_projects'] = $myProjects;
			}

			$view = new View($this, false);
			$view->viewPath = 'Rewards/partials';
			$view->set($viewData);
			$html = $view->render('user_redeem_points');

			echo json_encode($html);
			exit();

		}
	}

	public function user_remaining_points() {
		if ($this->request->isAjax()) {

			$this->layout = false;
			$viewData = null;
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				$viewData['user_id'] = $user_id = $post['user_id'];

				$ownerProjects = [];
				if($this->user_id != $user_id) {
					$ownerProjects = $this->getAllProjects($user_id);
				}
				$myProjects = $this->getAllProjects($this->user_id);
				// $myProjects = $this->getAllProjects($this->user_id);
				$viewData['user_projects'] = $ownerProjects;
				$viewData['my_projects'] = $myProjects;
			}

			$view = new View($this, false);
			$view->viewPath = 'Rewards/partials';
			$view->set($viewData);
			$html = $view->render('user_remaining_points');

			echo json_encode($html);
			exit();

		}
	}

	public function user_opt_setting() {
		if ($this->request->isAjax()) {

			$this->layout = false;
			$viewData = null;

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				$viewData['user_id'] = (isset($post['user_id']) && !empty($post['user_id'])) ? $post['user_id'] : false;
			}

			$optData = $this->get_user_opt_setting($this->user_id);
			$viewData['optData'] = $optData;

			$view = new View($this, false);
			$view->viewPath = 'Rewards/partials';
			$view->set($viewData);
			$html = $view->render('user_opt_setting');

			echo json_encode($html);
			exit();

		}
	}

	public function update_user_opt_setting() {
		if ($this->request->isAjax()) {

			$this->layout = false;
			$response = ['success' => false];

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;

				$user_id = $this->user_id;
				$optData = $this->get_user_opt_setting($user_id);
				$data = [$post['type'] => $post['status']];
				if (isset($optData) && !empty($optData)) {
					$data['id'] = $optData['id'];
				}
				if ($this->RewardOptedSetting->save($data)) {
					$response['success'] = true;
				}
			}

			echo json_encode($response);
			exit();

		}
	}


	public function reward_projects() {
		if ($this->request->isAjax()) {

			$this->layout = false;
			$response = ['success' => false];
			$user_id = $this->user_id;
			$allProjects = $this->getAllProjects($this->user_id);
			// pr($allProjects);

			$projects = array_keys($allProjects);
			// pr($projects);

			$all_projects = [];
			if (isset($projects) && !empty($projects)) {
				$all_project = $this->Project->find('list', ['conditions' => ['Project.id' => $projects]]);
				foreach ($all_project as $key => $value) {
					if (is_project_rewarded($key)) {
						$all_projects[$key] = ucfirst($value);
					}
				}
				asort($all_projects);
			}

			// pr($all_projects, 1);

			$response['success'] = (isset($all_projects) && !empty($all_projects)) ? true : false;

			echo json_encode($response);
			exit();

		}
	}

	public function user_reward_detail($user_id, $project_id) {
		if ($this->request->isAjax()) {

			$this->layout = false;
			$viewData = null;

			$viewData['user_id'] = $user_id;
			$viewData['project_id'] = $project_id;

			$this->set($viewData);
			$this->render('/Rewards/partials/user_reward_detail');

		}
	}

	public function reward_manager() {
		if ($this->request->isAjax()) {

			$this->layout = false;
			$viewData = null;
			$user_id = $this->user_id;
			$allProjects = $this->getAllProjects($user_id);
			$ownerProjects = $this->getAllProjects($user_id, 1);

			$viewData['all_projects'] = $allProjects;
			$viewData['owner_projects'] = $ownerProjects;
			$viewData['user_id'] = $user_id;

			$this->set($viewData);
			$this->render('/Rewards/partials/reward_manager');

		}
	}

	public function get_redeem_data() {
		if ($this->request->isAjax()) {

			$this->layout = false;
			$response = [
				'success' => false,
				'content' => null,
			];
			$viewData = null;

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				$viewData['user_id'] = $post['user_id'];
				$viewData['project_id'] = $post['project_id'];
			}

			$view = new View($this, false);
			$view->viewPath = 'Rewards/partials';
			$view->set($viewData);
			$html = $view->render('get_redeem_data');

			echo json_encode($html);
			exit();

		}
	}

	public function save_redeem_data() {
		if ($this->request->isAjax()) {

			$this->layout = false;
			$response = [
				'success' => false,
				'content' => null,
			];
			$viewData = null;

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				if ($this->RewardRedeem->save($post)) {
					$response['success'] = true;
				}
			}

			echo json_encode($response);
			exit();

		}
	}

	public function get_allocation_projects() {
		if ($this->request->isAjax()) {

			$this->layout = false;
			$response = [
				'success' => false,
				'content' => null,
			];
			$give_ov_projects = [];

			if ($this->request->is('post') || $this->request->is('put')) {
				$response['success'] = true;
				$post = $this->request->data;
				$user_id = $this->user_id;
				$ownerProjects = $this->getAllProjects($user_id, 1);
				if (isset($ownerProjects) && !empty($ownerProjects)) {
					foreach ($ownerProjects as $key => $value) {
						$project_allocated = 0;
						$project_reward_setting = project_reward_setting($key, 1);
						if ($project_reward_setting) {
							$project_reward_setting = $project_reward_setting['RewardSetting'];
							if (isset($project_reward_setting['remaining_ov']) && !empty($project_reward_setting['remaining_ov'])) {
								$give_ov_projects[$key] = $value;
							}
						}
					}
				}
			}
			$response['content'] = $give_ov_projects;

			echo json_encode($response);
			exit();

		}
	}

	public function get_redeem_projects() {
		if ($this->request->isAjax()) {

			$this->layout = false;
			$response = [
				'success' => false,
				'content' => null,
			];
			$redeem_projects = [];

			if ($this->request->is('post') || $this->request->is('put')) {
				$response['success'] = true;
				$post = $this->request->data;
				$user_id = $this->user_id;
				$allProjects = $this->getAllProjects($user_id);
				if (isset($allProjects) && !empty($allProjects)) {
					foreach ($allProjects as $key => $value) {
						if (user_remaining_redeem_points($user_id, $key)) {
							$redeem_projects[$key] = $value;
						}
					}
				}
			}
			$response['content'] = $redeem_projects;

			echo json_encode($response);
			exit();

		}
	}

	public function get_my_history() {
		if ($this->request->isAjax()) {

			$this->layout = false;
			$response = [
				'success' => false,
				'content' => null,
			];
			$viewData = null;

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				$viewData['user_id'] = $user_id = $this->user_id;
				$viewData['project_id'] = $project_id = (isset($post['project_id']) && !empty($post['project_id'])) ? $post['project_id'] : null;
			}

			$view = new View($this, false);
			$view->viewPath = 'Rewards/partials';
			$view->set($viewData);
			$html = $view->render('get_my_history');

			echo json_encode($html);
			exit();

		}
	}

	public function get_member_history() {
		if ($this->request->isAjax()) {

			$this->layout = false;
			$response = [
				'success' => false,
				'content' => null,
			];
			$viewData = null;

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				$viewData['user_id'] = $user_id = (isset($post['user_id']) && !empty($post['user_id'])) ? $post['user_id'] : null;
				$viewData['project_id'] = $project_id = (isset($post['project_id']) && !empty($post['project_id'])) ? $post['project_id'] : null;
			}

			$view = new View($this, false);
			$view->viewPath = 'Rewards/partials';
			$view->set($viewData);
			$html = $view->render('get_member_history');

			echo json_encode($html);
			exit();

		}
	}

	public function setting_create_allocation() {
		if ($this->request->isAjax()) {

			$this->layout = false;
			$response = [
				'success' => false,
				'content' => null,
			];
			$viewData = null;

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				$viewData['user_id'] = $post['user_id'];
				$viewData['project_id'] = $post['project_id'];
			}

			$view = new View($this, false);
			$view->viewPath = 'Rewards/partials';
			$view->set($viewData);
			$html = $view->render('setting_create_allocation');

			echo json_encode($html);
			exit();

		}
	}

	public function can_accelerate() {
		if ($this->request->isAjax()) {

			$this->layout = false;
			$response = [
				'success' => false,
				'content' => null,
			];
			$viewData = null;

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				$project_id = $post['project_id'];
				$project_accelerate = project_accelerate_setting($project_id, 0, 1);
				if ($project_accelerate) {
					$project_accelerate = $project_accelerate['RewardAccelerate'];
					$today = $this->objView->loadHelper('Wiki')->_displayDate(date('Y-m-d h:i:s A', strtotime(date('Y-m-d'))), $format = 'Y-m-d');
					$accelerate_percent = $project_accelerate['accelerate_percent'];
					$accelerated_rewards = 0;

					if (strtotime($project_accelerate['accelerate_date']) <= strtotime($today)) {
						$project_reward_assignments = project_reward_assignments($post['project_id']);
						if ($project_reward_assignments) {
							foreach ($project_reward_assignments as $key => $value) {
								$pra_data = $value['RewardAssignment'];
								$allocated_rewards = $pra_data['allocated_rewards'];
								$accelerated_value = round(($accelerate_percent * $allocated_rewards) / 100);
								$accelerated_rewards += $accelerated_value;
							}
						}
						$project_reward_setting = project_reward_setting($project_id, 1);
						if ($project_reward_setting) {
							$project_reward_setting = $project_reward_setting['RewardSetting'];
							$remaining_ov = $project_reward_setting['remaining_ov'];
							if ($remaining_ov >= $accelerated_rewards) {
								$response['success'] = true;
								$response['content'] = $remaining_ov - $accelerated_rewards;
							} else {
								$response['content'] = $accelerated_rewards - $remaining_ov;
							}
						}
					}
				} else {
					$response['success'] = true;
					$response['content'] = 'Cannot authorize acceleration. Increase Project Allocation.';
				}
				if (!isset($accelerated_rewards) || empty($accelerated_rewards)) {
					$response['success'] = false;
					$response['content'] = 'rewards';
				}
			}
			echo json_encode($response);
			exit();

		}
	}

	public function setting_get_accelerate() {
		if ($this->request->isAjax()) {

			$this->layout = false;
			$response = [
				'success' => false,
				'content' => null,
			];
			$viewData = null;

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				$viewData['project_id'] = $post['project_id'];
			}
			$view = new View($this, false);
			$view->viewPath = 'Rewards/partials';
			$view->set($viewData);
			$html = $view->render('setting_get_accelerate');

			echo json_encode($html);
			exit();

		}
	}

	public function save_project_accelerate() {
		if ($this->request->isAjax()) {

			$this->layout = false;
			$response = [
				'success' => false,
			];
			$viewData = null;

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;

				$data = [
					'given_by' => $this->user_id,
					'project_id' => $post['project_id'],
					'title' => $post['accelerate_title'],
					'accelerate_percent' => $post['accelerate_percent'],
					'accelerate_date' => (isset($post['accelerate_date']) && !empty($post['accelerate_date'])) ? date('Y-m-d', strtotime($post['accelerate_date'])) : null,
					'reason' => $post['reason'],
					'active' => 1,
					'authorize_status' => 0,
				];
				if (isset($post['id']) && !empty($post['id'])) {
					// $data['id'] = $post['id'];
				}
				$project_accelerate = project_accelerate_setting($post['project_id'], 0, 1);
				if ($project_accelerate) {
					$project_accelerate = $project_accelerate['RewardAccelerate'];
					$data['id'] = $project_accelerate['id'];
				}
				if ($this->RewardAccelerate->save($data)) {
					$response['success'] = true;
				}
			}
			echo json_encode($response);
			exit();

		}
	}

	public function authorize_project_accelerate() {
		if ($this->request->isAjax()) {

			$this->layout = false;
			$response = [
				'success' => false,
				'content' => null,
			];
			$viewData = null;
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				// pr($post, 1);
				$data = [
					'project_id' => $post['project_id'],
					'active' => 0,
					'authorize_status' => 1,
				];
				$project_reward_assignments = null;
				$total_allocated_rewards = 0;
				$project_accelerate = project_accelerate_setting($post['project_id'], 0, 1);
				$acc_created_on = '';
				if ($project_accelerate) {
					$project_accelerate = $project_accelerate['RewardAccelerate'];
					$data['id'] = $project_accelerate['id'];
					$accelerate_percent = $project_accelerate['accelerate_percent'];
					$acc_created_on = date('Y-m-d', strtotime($project_accelerate['created']));

					$pased_data = [
						'project_id' => $post['project_id'],
						'accelerate_id' => $project_accelerate['id'],
						'accelerate_percent' => $accelerate_percent,
					];
					if ($this->update_reward_acceleration($pased_data)) {
						$project_rewarded_users = project_rewarded_users($post['project_id']);

						if (isset($project_rewarded_users) && !empty($project_rewarded_users)) {
							foreach ($project_rewarded_users as $key => $user_id) {
								$current_accelerated_point = current_accelerated_point($user_id, $post['project_id']);
								if ($current_accelerated_point) {
									$total_allocated_rewards += $current_accelerated_point['accelerated_amount'];
								}
							}
						}
					}

					// update accelerate history table.
					$acc_history_data = [
						'given_by' => $this->user_id,
						'project_id' => $post['project_id'],
						'reward_accelerate_id' => $project_accelerate['id'],
						'accelerate_percent' => $accelerate_percent,
						'accelerate_date' => date('Y-m-d'),
						'accelerate_created' => $acc_created_on,
						'authorize_status' => 1,
						'reason' => $project_accelerate['reason'],
					];
					if ($this->RewardAccelerationHistory->save($acc_history_data)) {

					}

				}

				if ($this->RewardAccelerate->save($data)) {
					$response['success'] = true;
					// update project setting table and update project's remaining OV.
					if (isset($total_allocated_rewards) && !empty($total_allocated_rewards)) {
						$project_reward_setting = project_reward_setting($post['project_id'], 1);
						if ($project_reward_setting) {
							$project_reward_setting = $project_reward_setting['RewardSetting'];
							//accelerated_amount
							$project_allocation = $project_reward_setting['remaining_ov'];
							$remaining = $project_allocation - $total_allocated_rewards;
							//pr($remaining, 1);
							$this->RewardSetting->id = $project_reward_setting['id'];
							if ($this->RewardSetting->saveField('remaining_ov', $remaining)) {}
						}
					}

					if ($this->notify) {
						// TO ALL USERS WHO RECEIVED REWARDS FOR THIS PROJECT.
						$project_rewarded_users = project_rewarded_users($post['project_id']);
						if (isset($project_rewarded_users) && !empty($project_rewarded_users)) {
							/************** socket messages **************/
							if (SOCKET_MESSAGES) {
								$current_user_id = $this->user_id;
								$pou_users = $project_rewarded_users;

								$userDetail = get_user_data($current_user_id);

								$content = [
									'notification' => [
										'type' => 'reward',
										'created_id' => $current_user_id,
										'project_id' => $post['project_id'],
										'creator_name' => $userDetail['UserDetail']['full_name'],
										'subject' => 'OV reward acceleration',
										'heading' => 'Project: ' . strip_tags(getFieldDetail('Project', $post['project_id'], 'title')),
										// 'sub_heading' => 'To: ' . $assignedUserDetail['UserDetail']['full_name'],
										'date_time' => timezoneTimeConvertor($this->user_id, date('Y-m-d g:iA'), $format = 'd M Y g:iA') . user_country($this->user_id),
									],
								];

								if (is_array($pou_users)) {
									$content['received_users'] = array_values($pou_users);
								}
								$response['content']['socket'] = $content;

							}
							/************** socket messages **************/
						}
					}
				}
			}

			echo json_encode($response);
			exit();

		}
	}

	public function reject_project_accelerate() {
		if ($this->request->isAjax()) {

			$this->layout = false;
			$response = [
				'success' => false,
			];
			$viewData = null;
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				$data = [
					'project_id' => $post['project_id'],
					'active' => 0,
					'authorize_status' => 0,
				];

				$project_accelerate = project_accelerate_setting($post['project_id'], 0, 1);
				if ($project_accelerate) {
					$project_accelerate = $project_accelerate['RewardAccelerate'];
					$data['id'] = $project_accelerate['id'];

					// update accelerate history table.
					$history_data = [
						'given_by' => $this->user_id,
						'project_id' => $post['project_id'],
						'accelerate_percent' => $project_accelerate['accelerate_percent'],
						'accelerate_date' => date('Y-m-d'),
						'accelerate_created' => date('Y-m-d', strtotime($project_accelerate['created'])),
						'authorize_status' => 0,
						'reason' => $project_accelerate['reason'],
					];
					if ($this->RewardAccelerationHistory->save($history_data)) {

					}
				}
				if ($this->RewardAccelerate->save($data)) {
					$response['success'] = true;
				}
			}

			echo json_encode($response);
			exit();

		}
	}

	public function setting_get_allocation() {
		if ($this->request->isAjax()) {

			$this->layout = false;
			$response = [
				'success' => false,
				'content' => null,
			];
			$viewData = null;

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				$viewData['user_id'] = $post['user_id'];
				$viewData['project_id'] = $post['project_id'];
			}

			$view = new View($this, false);
			$view->viewPath = 'Rewards/partials';
			$view->set($viewData);
			$html = $view->render('setting_get_allocation');

			echo json_encode($html);
			exit();

		}
	}

	public function save_create_allocation() {
		if ($this->request->isAjax()) {

			$this->layout = false;
			$response = [
				'success' => false,
			];
			$viewData = null;

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				// pr($post, 1);
				$data = [
					'given_by' => $post['given_by'],
					'project_id' => $post['project_id'],
					'ov_allocation' => $post['ov_allocation'],
					'ov_exchange' => $post['ov_exchange'],
					'price_value' => $post['price_value'],
					'remaining_ov' => $post['ov_allocation'],
					'active' => 1,
				];

				$project_reward_setting = project_reward_setting($post['project_id'], 1);

				if ($project_reward_setting) {
					$project_reward_setting = $project_reward_setting['RewardSetting'];
					$ov_allocation = $project_reward_setting['ov_allocation'];
					$remaining_ov = $project_reward_setting['remaining_ov'];
					$data['updated_allocation'] = $post['ov_allocation'];
					$data['updation_type'] = $post['updation_type'];
					if (isset($post['updation_type']) && !empty($post['updation_type'])) {
						if ($post['updation_type'] == 'increase') {
							$data['ov_allocation'] = $ov_allocation + $post['ov_allocation'];
							$data['remaining_ov'] = $remaining_ov + $post['ov_allocation'];
						} else if ($post['updation_type'] == 'decrease') {
							$data['ov_allocation'] = $ov_allocation - $post['ov_allocation'];
							$data['remaining_ov'] = $remaining_ov - $post['ov_allocation'];
						}
					}

					$this->RewardSetting->id = $project_reward_setting['id'];

					if ($this->RewardSetting->saveField('active', 0)) {
						$this->RewardSetting->id = null;
					}
				}

				if ($this->RewardSetting->save($data)) {
					$response['success'] = true;
				}
			}
			echo json_encode($response);
			exit();

		}
	}

	public function get_allocated_projects() {
		if ($this->request->isAjax()) {

			$this->layout = false;
			$response = [
				'success' => false,
				'content' => [],
			];
			$viewData = null;

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				$user_id = $post['user_id'];
				$ownerProjects = $this->getAllProjects($user_id, 1);
				$allocated_projects = [];
				if (isset($ownerProjects) && !empty($ownerProjects)) {
					foreach ($ownerProjects as $key => $value) {
						$project_reward_setting = project_reward_setting($key, 1);
						if ($project_reward_setting) {
							$allocated_projects[$key] = $value;
						}
					}
				}
				$response['success'] = true;
				$response['content'] = $allocated_projects;
			}
			echo json_encode($response);
			exit();

		}
	}

	public function get_project_users() {
		if ($this->request->isAjax()) {

			$this->layout = false;
			$response = [
				'success' => false,
				'content' => [],
			];
			$viewData = null;

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				$current_user_id = $this->Session->read('Auth.User.id');
				$project_id = $post['project_id'];
				$me = (isset($post['me']) && !empty($post['me'])) ? $post['me'] : 1;
				$allUsersList = [];

				if (isset($project_id) && !empty($project_id)) {
					$projectUsers = $this->objView->loadHelper('TaskCenter')->userByProject([$project_id]);
					if (isset($projectUsers) && !empty($projectUsers)) {
						$projectUsers = array_unique($projectUsers['all_project_user']);
						$allUsers = $this->objView->loadHelper('TaskCenter')->user_exists($projectUsers);

						// $me = 1 or 2
						if ($me == 1) {
							if (($key = array_search($current_user_id, $allUsers)) !== false) {
								unset($allUsers[$key]);
							}
						}
						$allUsersList = $this->objView->loadHelper('Common')->usersFullname($allUsers);
						$allUsersList = Set::combine($allUsersList, '/UserDetail/user_id', '/UserDetail/full_name');
					}

					if (!isset($allUsersList) || empty($allUsersList)) {
						$currentUser = $this->objView->loadHelper('Common')->usersFullname([$current_user_id]);
						$allUsersList = Set::combine($currentUser, '/UserDetail/user_id', '/UserDetail/full_name');
					}
				}

				$allUsersListing = [];
				if (isset($allUsersList) && !empty($allUsersList)) {
					foreach ($allUsersList as $key => $value) {
						if (user_opt_status($key)) {
							$allUsersListing[$key] = $value;
						}
					}
				}

				$response['success'] = true;
				$response['content'] = $allUsersListing;
			}
			echo json_encode($response);
			exit();

		}
	}

	public function give_ov_users() {
		if ($this->request->isAjax()) {

			$this->layout = false;
			$response = [
				'success' => false,
				'content' => [],
			];
			$viewData = null;

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				$current_user_id = $this->Session->read('Auth.User.id');
				$project_id = $post['project_id'];
				$me = (isset($post['me']) && !empty($post['me'])) ? $post['me'] : 1;
				$allUsersList = [];

				if (isset($project_id) && !empty($project_id)) {
					$projectUsers = $this->objView->loadHelper('TaskCenter')->userByProject([$project_id]);
					if (isset($projectUsers) && !empty($projectUsers)) {
						$projectUsers = array_unique($projectUsers['all_project_user']);
						$allUsers = $this->objView->loadHelper('TaskCenter')->user_exists($projectUsers);

						// $me = 1 or 2
						if ($me == 1) {
							if (($key = array_search($current_user_id, $allUsers)) !== false) {
								unset($allUsers[$key]);
							}
						}
						$allUsersList = $this->objView->loadHelper('Common')->usersFullname($allUsers);
						$allUsersList = Set::combine($allUsersList, '/UserDetail/user_id', '/UserDetail/full_name');
					}

					if (!isset($allUsersList) || empty($allUsersList)) {
						$currentUser = $this->objView->loadHelper('Common')->usersFullname([$current_user_id]);
						$allUsersList = Set::combine($currentUser, '/UserDetail/user_id', '/UserDetail/full_name');
					}
				}

				$owner_count = 0;
				$allUsersListing = [];
				if (isset($allUsersList) && !empty($allUsersList)) {
					foreach ($allUsersList as $key => $value) {
						if (user_opt_status($key) && dbExists('User', $key)) {
							$allUsersListing[$key] = $value;
							$ProjectPermit = $this->objView->loadHelper('ViewModel')->projectPermitType($project_id, $key);
							if ($ProjectPermit) {
								$owner_count++;
							}
						}
					}
				}
				if ($owner_count <= 0) {
					$current_user_name = user_full_name($current_user_id);
					$allUsersListing[$current_user_id] = $current_user_name;
				}

				$response['success'] = true;
				$response['content'] = $allUsersListing;
			}
			echo json_encode($response);
			exit();

		}
	}

	public function get_project_activity() {
		if ($this->request->isAjax()) {

			$this->layout = false;
			$response = [
				'success' => false,
				'content' => [],
			];
			$viewData = $data = null;

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;

				if (isset($post['type']) && !empty($post['type'])) {

					$project_id = $post['project_id'];
					$user_id = $post['user_id'];

					if ($post['type'] == 'workspace') {
						$pwids = $this->get_permit_workspaces($project_id, $user_id);
						if (isset($pwids) && !empty($pwids)) {
							$workspaces = workspace_2_pwid($pwids);
							$data = $this->Workspace->find('list', ['conditions' => ['Workspace.id' => $workspaces]]);
						}
					} else if ($post['type'] == 'task') {
						$elids = $this->get_permit_tasks($project_id, $user_id);
						if (isset($elids) && !empty($elids)) {
							$data = $this->Element->find('list', ['conditions' => ['Element.id' => $elids]]);
						}
					} else if ($post['type'] == 'risk') {
						$risks = user_project_risks($project_id, $user_id);
						if (isset($risks) && !empty($risks)) {
							$data = $this->RmDetail->find('list', ['conditions' => ['RmDetail.id' => $risks]]);
						}
					} else if ($post['type'] == 'todo') {
						$todos = $this->get_permit_todos($project_id, $user_id);
						if (isset($todos) && !empty($todos)) {
							$data = $this->DoList->find('list', ['conditions' => ['DoList.id' => $todos]]);
						}
					} else if ($post['type'] == 'subtodo') {
						$todos = $this->get_permit_sub_todos($project_id, $user_id);
						if (isset($todos) && !empty($todos)) {
							$data = $this->DoList->find('list', ['conditions' => ['DoList.id' => $todos]]);
						}
					}
				}
				$response['success'] = true;
				$response['content'] = $data;
			}
			echo json_encode($response);
			exit();

		}
	}

	public function get_project_ov() {
		if ($this->request->isAjax()) {

			$this->layout = false;
			$response = [
				'success' => false,
				'content' => [],
			];

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				$project_id = $post['project_id'];
				$project_reward = project_reward_setting($project_id, 1);

				$response['success'] = true;
				$response['content'] = (isset($project_reward) && !empty($project_reward)) ? $project_reward['RewardSetting']['remaining_ov'] : '';
			}
			echo json_encode($response);
			exit();

		}
	}

	public function save_give_allocation() {
		if ($this->request->isAjax()) {

			$this->layout = false;
			$response = [
				'success' => false,
			];

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;

				$data = [
					'given_by' => $this->user_id,
					'user_id' => $post['user_id'],
					'project_id' => $post['project_id'],
					'type' => $post['type'],
					'type_relation_id' => $post['activity'],
					'allocated_rewards' => $post['allocated_reward'],
					'reason' => $post['reason'],
				];

				if ($this->RewardAssignment->save($data)) {
					$project_reward = project_reward_setting($post['project_id'], 1);
					if (isset($project_reward) && !empty($project_reward)) {
						$project_reward = $project_reward['RewardSetting'];
						$project_allocation = $project_reward['remaining_ov'];
						$remaining = $project_allocation - $post['allocated_reward'];

						$this->RewardSetting->id = $project_reward['id'];
						if ($this->RewardSetting->saveField('remaining_ov', $remaining)) {

						}
					}
					if ($this->notify) {
						// TO ALL PROJECT OWNERS
						$project_owner_users = project_owner_users($post['project_id']);
						if (isset($project_owner_users) && !empty($project_owner_users)) {
							/************** socket messages **************/
							if (SOCKET_MESSAGES) {
								$current_user_id = $this->user_id;
								$pou_users = $project_owner_users;

								$userDetail = get_user_data($current_user_id);
								$assignedUserDetail = get_user_data($post['user_id']);
								// REMOVE SELF FROM USERS LIST
								if (($key = array_search($current_user_id, $pou_users)) !== false) {
									unset($pou_users[$key]);
								}
								// REMOVE RECEIVED BY USER FROM USERS LIST
								if (($key = array_search($post['user_id'], $pou_users)) !== false) {
									unset($pou_users[$key]);
								}

								$content = [
									'notification' => [
										'type' => 'reward',
										'created_id' => $current_user_id,
										'project_id' => $post['project_id'],
										'creator_name' => $userDetail['UserDetail']['full_name'],
										'subject' => 'OV reward',
										// 'heading' => 'Project: ' . strip_tags(getFieldDetail('Project', $post['project_id'], 'title')),
										'sub_heading' => 'To: ' . $assignedUserDetail['UserDetail']['full_name'],
										'date_time' => timezoneTimeConvertor($this->user_id, date('Y-m-d g:iA'), $format = 'd M Y g:iA') . user_country($this->user_id),
									],
								];
								if (isset($post['type']) && !empty($post['type'])) {
									$data_by_type = ['title' => '', 'type' => ''];
									if ($post['type'] == 'project') {
										$data_by_type = data_by_type($post['type'], null, $post['project_id']);
										$content['notification']['heading'] = 'Project: ' . strip_tags(getFieldDetail('Project', $post['project_id'], 'title'));
									} else {
										$data_by_type = data_by_type($post['type'], $post['activity']);
										$content['notification']['heading'] = $data_by_type['type'] . ': ' . $data_by_type['title'] . '<br />' . 'Project: ' . strip_tags(getFieldDetail('Project', $post['project_id'], 'title'));
									}

								}
								if (is_array($pou_users)) {
									$content['received_users'] = array_values($pou_users);
								}
								$response['content']['socket'] = $content;

							}
							/************** socket messages **************/
						}
						// TO THE USER WHO RECEIVED THE REWARD.
						if (SOCKET_MESSAGES) {
							$current_user_id = $this->user_id;

							$userDetail = get_user_data($current_user_id);
							$assignedUserDetail = get_user_data($post['user_id']);
							$assigned_content = [
								'notification' => [
									'type' => 'reward',
									'created_id' => $current_user_id,
									'project_id' => $post['project_id'],
									'creator_name' => $userDetail['UserDetail']['full_name'],
									'subject' => 'OV reward',
									// 'heading' => 'Project: ' . strip_tags(getFieldDetail('Project', $post['project_id'], 'title')),
									// 'sub_heading' => 'To: ' . $assignedUserDetail['UserDetail']['full_name'],
									'date_time' => timezoneTimeConvertor($this->user_id, date('Y-m-d g:iA'), $format = 'd M Y g:iA') . user_country($this->user_id),
								],
							];
							if (isset($post['type']) && !empty($post['type'])) {
								$data_by_type = ['title' => '', 'type' => ''];
								if ($post['type'] == 'project') {
									$data_by_type = data_by_type($post['type'], null, $post['project_id']);
									$assigned_content['notification']['heading'] = 'Project: ' . strip_tags(getFieldDetail('Project', $post['project_id'], 'title'));
								} else {
									$data_by_type = data_by_type($post['type'], $post['activity']);
									$assigned_content['notification']['heading'] = $data_by_type['type'] . ': ' . $data_by_type['title'] . '<br />' . 'Project: ' . strip_tags(getFieldDetail('Project', $post['project_id'], 'title'));
								}

							}
							$assigned_content['received_users'] = [$post['user_id']];

							$request = array(
								'header' => array(
									'Content-Type' => 'application/json',
								),
							);
							$assigned_content = json_encode($assigned_content);
							$HttpSocket = new HttpSocket([
								'ssl_verify_host' => false,
								'ssl_verify_peer_name' => false,
								'ssl_verify_peer' => false,
							]);
							$results = $HttpSocket->post(CHATURL . '/serveremit', $assigned_content, $request);

						}
					}

					$response['success'] = true;
				}
			}
			echo json_encode($response);
			exit();

		}
	}

	public function get_project_charity() {
		if ($this->request->isAjax()) {

			$this->layout = false;
			$response = [
				'success' => false,
				'ov_setting' => false,
				'content' => [],
			];

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				$project_id = $post['project_id'];
				$project_charity = project_charity($project_id);
				$project_reward_setting = project_reward_setting($project_id, 1);
				if ($project_reward_setting) {
					$response['ov_setting'] = true;
				}

				$response['success'] = true;
				$response['content'] = (isset($project_charity) && !empty($project_charity)) ? $project_charity['RewardCharity'] : '';
			}
			echo json_encode($response);
			exit();

		}
	}

	public function save_project_charity() {
		if ($this->request->isAjax()) {

			$this->layout = false;
			$response = [
				'success' => false,
			];
			$viewData = null;

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;

				$data = [
					'project_id' => $post['project_id'],
					'title' => $post['title'],
				];

				// check exists charity
				$project_charity = project_charity($post['project_id']);
				if ($project_charity) {
					$data['id'] = $project_charity['RewardCharity']['id'];
					$data['updated_by'] = $this->user_id;
				} else {
					$data['given_by'] = $post['given_by'];
					$data['updated_by'] = $post['given_by'];
				}
				if ($this->RewardCharity->save($data)) {
					$response['success'] = true;
				}
			}
			echo json_encode($response);
			exit();

		}
	}

	public function show_project_offers($user_id = null, $project_id = null) {
		if ($this->request->isAjax()) {

			$this->layout = false;
			$viewData = null;

			$viewData['project_id'] = $project_id;
			$viewData['user_id'] = $user_id;

			$this->set($viewData);
			$this->render('/Rewards/partials/show_project_offers');

		}
	}

	public function project_charity_ov($user_id = null, $project_id = null) {
		if ($this->request->isAjax()) {

			$this->layout = false;
			$viewData = null;

			$viewData['project_id'] = $project_id;
			$viewData['user_id'] = $user_id;

			$this->set($viewData);
			$this->render('/Rewards/partials/project_charity_ov');

		}
	}

	public function project_achieved_ov($user_id = null, $project_id = null) {
		if ($this->request->isAjax()) {

			$this->layout = false;
			$viewData = null;

			$viewData['project_id'] = $project_id;
			$viewData['user_id'] = $user_id;

			$this->set($viewData);
			$this->render('/Rewards/partials/project_achieved_ov');

		}
	}

	public function get_project_ov_data() {
		if ($this->request->isAjax()) {

			$this->layout = false;
			$viewData = null;

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				$viewData['user_id'] = $user_id = $post['user_id'];
				$viewData['project_id'] = (isset($post['project_id']) && !empty($post['project_id'])) ? $post['project_id'] : $this->getAllProjects($user_id);
				$viewData['type'] = (isset($post['type']) && !empty($post['type'])) ? $post['type'] : null;
			}

			$view = new View($this, false);
			$view->viewPath = 'Rewards/partials';
			$view->set($viewData);
			$html = $view->render('get_project_ov_data');
			echo json_encode($html);
			exit();
		}
	}

	public function all_project_achieved_ov($user_id = null) {
		if ($this->request->isAjax()) {

			$this->layout = false;
			$viewData = null;

			$viewData['user_id'] = $user_id;
			$viewData['projects'] = $this->getAllProjects($user_id);

			$this->set($viewData);
			$this->render('/Rewards/partials/all_project_achieved_ov');

		}
	}

	public function project_offers() {
		if ($this->request->isAjax()) {

			$this->layout = false;
			$viewData = null;

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				$viewData['project_id'] = (isset($post['project_id']) && !empty($post['project_id'])) ? $post['project_id'] : null;
			}

			$view = new View($this, false);
			$view->viewPath = 'Rewards/partials';
			$view->set($viewData);
			$html = $view->render('project_offers');
			echo json_encode($html);
			exit();
		}
	}

	public function project_offers_detail() {
		if ($this->request->isAjax()) {

			$this->layout = false;
			$response = [
				'success' => false,
				'content' => null,
			];

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				if (isset($post['id']) && !empty($post['id'])) {
					if (is_offer_redeemed($post['id'])) {
						$project_offer_detail = project_offer_detail($post['id']);
						if ($project_offer_detail) {
							$offer_detail = $project_offer_detail['RewardOffer'];
							$updated_by = 'Created by: ';
							$updated_user = user_full_name($offer_detail['creator_id']);
							$updated_by .= $updated_user . ': ';
							$updated_by .= $this->objView->loadHelper('Wiki')->_displayDate(date('Y-m-d h:i:s A', strtotime($offer_detail['modified'])), $format = 'd M Y h:i A');

							$response['content'] = [
								'id' => $offer_detail['id'],
								'title' => $offer_detail['title'],
								'amount' => $offer_detail['amount'],
								'updated' => $updated_by,
							];
							$response['success'] = true;
						}
					}
				}
			}

			echo json_encode($response);
			exit();
		}
	}

	public function project_offers_list() {
		if ($this->request->isAjax()) {

			$this->layout = false;
			$viewData = null;

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				$viewData['project_id'] = (isset($post['project_id']) && !empty($post['project_id'])) ? $post['project_id'] : null;
			}

			$view = new View($this, false);
			$view->viewPath = 'Rewards/partials';
			$view->set($viewData);
			$html = $view->render('project_offers_list');
			echo json_encode($html);
			exit();
		}
	}

	public function save_project_offer() {
		if ($this->request->isAjax()) {

			$this->layout = false;
			$response = [
				'success' => false,
				'content' => ['socket' => []],
			];

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				$save_data = [
					'user_id' => $this->user_id,
					'project_id' => $post['project_id'],
					'title' => $post['title'],
					'amount' => $post['amount'],
				];
				if (isset($post['id']) && !empty($post['id'])) {
					$save_data['id'] = $post['id'];
				} else {
					$save_data['creator_id'] = $this->user_id;
				}

				if ($this->RewardOffer->save($save_data)) {
					$response['success'] = true;
					if ($this->notify) {
						// TO ALL USERS WHO RECEIVED REWARDS FOR THIS PROJECT.
						$project_users = $this->project_all_users($post['project_id']);
						if (isset($project_users) && !empty($project_users)) {
							/************** socket messages **************/
							if (SOCKET_MESSAGES) {
								$current_user_id = $this->user_id;
								$pou_users = $project_users;

								$userDetail = get_user_data($current_user_id);

								$content = [
									'notification' => [
										'type' => 'reward',
										'created_id' => $current_user_id,
										'project_id' => $post['project_id'],
										'creator_name' => $userDetail['UserDetail']['full_name'],
										'subject' => 'New offer',
										'heading' => 'Project: ' . strip_tags(getFieldDetail('Project', $post['project_id'], 'title')),
										'sub_heading' => 'Offer: ' . $post['title'],
										'date_time' => timezoneTimeConvertor($this->user_id, date('Y-m-d g:iA'), $format = 'd M Y g:iA') . user_country($this->user_id),
									],
								];

								if (is_array($pou_users)) {
									$content['received_users'] = $pou_users;
								}
								$response['content']['socket'] = $content;

							}
							/************** socket messages **************/
						}
					}
				}
			}

			echo json_encode($response);
			exit();
		}
	}

	public function delete_project_offer() {
		if ($this->request->isAjax()) {

			$this->layout = false;
			$response = [
				'success' => false,
				'content' => null,
			];

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				if (isset($post['id']) && !empty($post['id'])) {

					if (dbExists('RewardOffer', $post['id'])) {
						if ($this->RewardOffer->delete($post['id'])) {
							$response['success'] = true;
						}
					}
				}
			}

			echo json_encode($response);
			exit();
		}
	}

	public function end_project_offer() {
		if ($this->request->isAjax()) {

			$this->layout = false;
			$response = [
				'success' => false,
				'content' => null,
			];

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				if (isset($post['id']) && !empty($post['id'])) {

					if (dbExists('RewardOffer', $post['id'])) {
						$this->RewardOffer->id = $post['id'];
						if ($this->RewardOffer->saveField('ended', 1)) {
							$response['success'] = true;
						}
					}
				}
			}

			echo json_encode($response);
			exit();
		}
	}

	public function get_project_offers() {
		if ($this->request->isAjax()) {

			$this->layout = false;
			$response = [
				'success' => false,
				'content' => [],
			];

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				$project_id = $post['project_id'];
				$project_offers = project_offers($project_id, true);
				if ($project_offers) {
					$project_offers = Set::combine($project_offers, '{n}.RewardOffer.id', '{n}.RewardOffer.title');
				}
				$response['success'] = true;
				if (isset($post['project_id']) && !empty($post['project_id'])) {
					$response['content'] = (isset($project_offers) && !empty($project_offers)) ? $project_offers : '';
				}
			}
			echo json_encode($response);
			exit();

		}
	}

	public function get_offering_project() {
		if ($this->request->isAjax()) {

			$this->layout = false;
			$response = [
				'success' => false,
				'content' => [],
			];

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;

				$all_projects = $this->getAllProjects($this->user_id);

				$offering_projects = [];
				if (isset($all_projects) && !empty($all_projects)) {
					foreach ($all_projects as $key => $value) {
						$offer_exists = project_offers($key);
						if ($offer_exists) {
							$offering_projects[$key] = $value;
						}
					}
				}
				$response['success'] = true;
				$response['content'] = $offering_projects;
			}
			echo json_encode($response);
			exit();

		}
	}

	public function project_offer_shop() {
		if ($this->request->isAjax()) {

			$this->layout = false;
			$viewData = null;

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				$viewData['project_id'] = (isset($post['project_id']) && !empty($post['project_id'])) ? $post['project_id'] : null;
				$viewData['offer_id'] = (isset($post['offer_id']) && !empty($post['offer_id'])) ? $post['offer_id'] : null;
			}

			$view = new View($this, false);
			$view->viewPath = 'Rewards/partials';
			$view->set($viewData);
			$html = $view->render('project_offer_shop');
			echo json_encode($html);
			exit();
		}
	}

	public function user_project_shopping() {
		if ($this->request->isAjax()) {

			$this->layout = false;
			$viewData = null;

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				$viewData['project_id'] = (isset($post['project_id']) && !empty($post['project_id'])) ? $post['project_id'] : null;
				$viewData['offer_id'] = (isset($post['offer_id']) && !empty($post['offer_id'])) ? $post['offer_id'] : null;
			}

			$view = new View($this, false);
			$view->viewPath = 'Rewards/partials';
			$view->set($viewData);
			$html = $view->render('user_project_shopping');
			echo json_encode($html);
			exit();
		}
	}

	public function offer_shopping() {
		if ($this->request->isAjax()) {

			$this->layout = false;
			$response = [
				'success' => false,
				'content' => null,
			];

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				if ((isset($post['project_id']) && !empty($post['project_id'])) && (isset($post['offer_id']) && !empty($post['offer_id']))) {

					$project_id = $post['project_id'];
					$offer_id = $post['offer_id'];

					$project_offer_detail = project_offer_detail($offer_id);
					if ($project_offer_detail) {

						$project_offer_detail = $project_offer_detail['RewardOffer'];

						$redeem_data = [
							'given_by' => $this->user_id,
							'project_id' => $project_id,
							'redeemed_value' => $project_offer_detail['amount'],
							'reward_offer_id' => $offer_id,
						];

						$project_reward_setting = project_reward_setting($project_id, 1);
						if ($project_reward_setting) {
							$project_reward_setting = $project_reward_setting['RewardSetting'];
							$redeem_data['ov_exchange'] = $project_reward_setting['ov_exchange'];
							$redeem_data['ov_exchange_value'] = $project_reward_setting['price_value'];
							$redeemed_value = ($project_offer_detail['amount'] / $project_reward_setting['price_value']) * $project_reward_setting['ov_exchange'];
							// e($redeemed_value, 1);
							$redeem_data['redeem_amount'] = $redeemed_value;
						}

						$shop_data = [
							'creator_id' => $project_offer_detail['creator_id'],
							'user_id' => $this->user_id,
							'project_id' => $project_id,
							'offer_id' => $offer_id,
							'title' => $project_offer_detail['title'],
							'amount' => $project_offer_detail['amount'],
						];

						if ($this->RewardOfferShop->save($shop_data)) {
							if ($this->RewardRedeem->save($redeem_data)) {
								$response['success'] = true;
							}
							if ($this->notify) {
								if (SOCKET_MESSAGES) {
									$current_user_id = $this->user_id;

									$userDetail = get_user_data($current_user_id);
									$assigned_content = [
										'notification' => [
											'type' => 'reward',
											'created_id' => $current_user_id,
											'project_id' => $project_id,
											'creator_name' => $userDetail['UserDetail']['full_name'],
											'subject' => 'Reward buy',
											'heading' => 'Project: ' . strip_tags(getFieldDetail('Project', $project_id, 'title')),
											'sub_heading' => 'Offer: ' . $project_offer_detail['title'],
											'date_time' => timezoneTimeConvertor($this->user_id, date('Y-m-d g:iA'), $format = 'd M Y g:iA') . user_country($this->user_id),
										],
									];
									$assigned_content['received_users'] = [$project_offer_detail['creator_id']];

									$request = array(
										'header' => array(
											'Content-Type' => 'application/json',
										),
									);
									$assigned_content = json_encode($assigned_content);
									$HttpSocket = new HttpSocket([
										'ssl_verify_host' => false,
										'ssl_verify_peer_name' => false,
										'ssl_verify_peer' => false,
									]);
									$results = $HttpSocket->post(CHATURL . '/serveremit', $assigned_content, $request);
								}
							}
						}
					}
				}
			}

			echo json_encode($response);
			exit();
		}
	}

	public function offer_received_status() {
		if ($this->request->isAjax()) {

			$this->layout = false;
			$response = [
				'success' => false,
			];

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;

				$this->RewardOfferShop->id = $post['offer_id'];
				if ($this->RewardOfferShop->saveField('received', $post['received'])) {
					$response['success'] = true;
				}
			}

			echo json_encode($response);
			exit();
		}
	}

//****************** Private Functions *********************//

	function update_reward_acceleration($data = null) {

		$project_id = $data['project_id'];
		$accelerate_id = $data['accelerate_id'];
		$accelerate_percent = $data['accelerate_percent'];
		$project_rewarded_users = project_rewarded_users($project_id);

		if (isset($project_rewarded_users) && !empty($project_rewarded_users)) {
			foreach ($project_rewarded_users as $key => $user_id) {

				$user_earned_points = project_reward_assignments($project_id, $user_id);

				$total_earned = 0;
				if ($user_earned_points) {
					foreach ($user_earned_points as $rdKey => $rdVal) {
						$total_earned += $rdVal['RewardAssignment']['allocated_rewards'];
					}
				}

				$project_redeemed = project_redeemed_data($project_id, $user_id);
				$total_redeem = 0;
				if ($project_redeemed) {
					foreach ($project_redeemed as $rdKey => $rdVal) {
						$total_redeem += $rdVal['RewardRedeem']['redeem_amount'];
					}
				}

				$project_accelerated_points = project_accelerated_points($project_id, $user_id);

				if ($project_accelerated_points) {
					$total_earned += $project_accelerated_points;
				}

				$total_remaining = $total_earned - $total_redeem;
				// pr($total_remaining);die;
				if (!empty($total_remaining)) {
					$accelerated_amount = round(($accelerate_percent * $total_remaining) / 100);
					$save_data = [
						'user_id' => $user_id,
						'reward_accelerate_id' => $accelerate_id,
						'project_id' => $project_id,
						'remaining_amount' => $total_remaining,
						'accelerate_percent' => $accelerate_percent,
						'accelerated_amount' => $accelerated_amount,
						'status' => 1,
					];
					// Get user's previous acceleration for this project if exists.
					$pre_acc = null;
					$pre_acc = $this->RewardUserAcceleration->find('first', ['conditions' => [
						'user_id' => $user_id,
						'project_id' => $project_id,
						'status' => 1,
					]]);
					if (isset($pre_acc) && !empty($pre_acc)) {
						// $save_data['id'] = $pre_acc['RewardUserAcceleration']['id'];
						$new_total_remaining = $total_remaining;
						$new_accelerated_amount = round(($accelerate_percent * $new_total_remaining) / 100);
						$save_data['remaining_amount'] = $new_total_remaining;
						$save_data['accelerated_amount'] = $new_accelerated_amount;
						// update status of the current accelerated value row.
						$this->RewardUserAcceleration->id = $pre_acc['RewardUserAcceleration']['id'];
						$this->RewardUserAcceleration->saveField('status', 0);
					}

					$this->RewardUserAcceleration->id = null;
					if ($this->RewardUserAcceleration->save($save_data)) {
					}
				}
			}
		}
		return true;
	}

	function ownerShares($project_id, $user_id) {

		$data = array('owner' => null, 'sharer' => null);

		if (isset($project_id) && !empty($project_id)) {

			foreach ($project_id as $pid) {

				$p_permission = $this->objView->loadHelper('Common')->project_permission_details($pid, $user_id);
				$user_project = $this->objView->loadHelper('Common')->userproject($pid, $user_id);

				if (isset($user_project) && !empty($user_project)) {
					$data['owner'][] = 'owner';
				}

				if (isset($p_permission) && !empty($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level'] == 1) {
					$data['owner'][] = 'owner';

				}
				if (isset($p_permission) && !empty($p_permission['ProjectPermission']) && $p_permission['ProjectPermission']['project_level'] != 1) {
					$data['sharer'][] = 'sharer';
				}

				/*  group Work Permission and group permission and level check */
				$grp_id = $this->objView->loadHelper('Group')->GroupIDbyUserID($pid, $user_id);

				if (isset($grp_id) && !empty($grp_id)) {
					$group_permission = $this->objView->loadHelper('Group')->group_permission_details($pid, $grp_id);

					if (isset($group_permission['ProjectPermission']['project_level']) && $group_permission['ProjectPermission']['project_level'] == 1) {
						$data['owner'][] = 'owner';
					} else if (isset($group_permission['ProjectPermission']['project_level']) && $group_permission['ProjectPermission']['project_level'] == 0) {
						$data['sharer'][] = 'sharer';
					}
				}
			}
			return $data;
		}

	}

	function get_user_opt_setting($user_id) {
		$user_id = (isset($user_id) && !empty($user_id)) ? $user_id : $this->user_id;
		$optData = $this->RewardOptedSetting->find('first', [
			'conditions' => [
				'user_id' => $user_id,
			],
		]);
		if (!isset($optData) || empty($optData)) {
			$data = [
				'user_id' => $user_id,
				'reward_opt_status' => 1,
				'reward_table_opt_status' => 1,
			];
			if ($this->RewardOptedSetting->save($data)) {
				$optData = $this->RewardOptedSetting->find('first', [
					'conditions' => [
						'user_id' => $user_id,
					],
				]);
			}
		}
		return (isset($optData) && !empty($optData)) ? $optData['RewardOptedSetting'] : false;
	}

	function getAllProjects($user_id, $owner = null) {
		$allProjects = user_all_projects($user_id, 1);
		uasort($allProjects, function ($a, $b) {return strtolower($a) > strtolower($b);});
		return $allProjects;
	}

	function get_permit_workspaces($project_id, $user_id) {
		$owner = $this->objView->loadHelper('ViewModel')->projectPermitType($project_id, $user_id);
		if ($owner) {
			$wsp_permission = $this->ProjectWorkspace->find('all', ['conditions' => [
				'ProjectWorkspace.project_id' => $project_id,
			],
				'fields' => ['ProjectWorkspace.workspace_id'],
			]);

			if (isset($wsp_permission) && !empty($wsp_permission)) {
				$wsp_permission = Set::extract($wsp_permission, '/ProjectWorkspace/id');
			}
		} else {
			$wsp_permission = $this->objView->loadHelper('Common')->work_permission_details($project_id, $user_id);

			$grp_id = $this->objView->loadHelper('Group')->GroupIDbyUserID($project_id, $user_id);

			if (isset($grp_id) && !empty($grp_id)) {
				$project_level = 0;
				$group_permission = $this->objView->loadHelper('Group')->group_permission_details($project_id, $grp_id);
				if (isset($group_permission['ProjectPermission']['project_level']) && $group_permission['ProjectPermission']['project_level'] == 1) {
					$project_level = $group_permission['ProjectPermission']['project_level'];
				}

				if ($project_level > 0) {
					$wsp_permission = $this->ProjectWorkspace->find('all', ['conditions' => [
						'ProjectWorkspace.project_id' => $project_id,
					],
						'fields' => ['ProjectWorkspace.workspace_id'],
					]);

					if (isset($wsp_permission) && !empty($wsp_permission)) {
						$wsp_permission = Set::extract($wsp_permission, '/ProjectWorkspace/id');
					}
				} else {
					$wsp_permission = $this->objView->loadHelper('Group')->group_work_permission_details($project_id, $grp_id);

				}

			}
			//
		}
		// pr($wsp_permission);
		$workspace_ids = null;

		if (isset($wsp_permission) && !empty($wsp_permission)) {
			foreach ($wsp_permission as $key => $value) {
				$wpid = pwid_workspace($value, $project_id);
				if (dbExists('Workspace', $wpid)) {
					$data = getByDbId('Workspace', $wpid);
					$wspDate = $data['Workspace']['start_date'];
					$today = $this->objView->loadHelper('Wiki')->_displayDate(date('Y-m-d h:i:s A', strtotime(date('Y-m-d'))), $format = 'Y-m-d');
					$wspDate = $this->objView->loadHelper('Wiki')->_displayDate(date('Y-m-d h:i:s A', strtotime($wspDate)), $format = 'Y-m-d');
					if (strtotime($today) >= strtotime($wspDate)) {
						$workspace_ids[] = $value;
					}
				}
			}
		}
		// pr($workspace_ids);
		return $workspace_ids;
	}

	function get_permit_tasks($project_id, $user_id = null) {

		$e_permission = null;
		$project_level = 0;
		$user_id = (isset($user_id) && !empty($user_id)) ? $user_id : $this->user_id;

		$p_permission = $this->objView->loadHelper('Common')->project_permission_details($project_id, $user_id);
		$user_project = $this->objView->loadHelper('Common')->userproject($project_id, $user_id);

		$group_id = $this->objView->loadHelper('Group')->GroupIDbyUserID($project_id, $user_id);

		$project_workspace = get_project_workspace($project_id, true);

		if (isset($project_workspace) && !empty($project_workspace)) {

			$project_workspaces = array_keys($project_workspace);
			$e_permission = [];
			foreach ($project_workspaces as $key => $workspace_id) {

				$wsp_permission = $this->objView->loadHelper('Common')->wsp_permission_details($this->objView->loadHelper('ViewModel')->workspace_pwid($workspace_id), $project_id, $user_id);

				if (isset($group_id) && !empty($group_id)) {

					$group_permission = $this->objView->loadHelper('Group')->group_permission_details($project_id, $group_id);

					if (isset($group_permission['ProjectPermission']['project_level']) && $group_permission['ProjectPermission']['project_level'] == 1) {
						$project_level = $group_permission['ProjectPermission']['project_level'];
					}
					$wsp_permission = $this->objView->loadHelper('Group')->group_wsp_permission_details($this->objView->loadHelper('ViewModel')->workspace_pwid($workspace_id), $project_id, $group_id);
				}

				if ((isset($user_project) && !empty($user_project)) || (isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level'] == 1) || (isset($project_level) && $project_level == 1)) {
					$e_permission = $this->objView->loadHelper('ViewModel')->project_elements($project_id);
					if (isset($e_permission) && !empty($e_permission)) {
						$e_permission = Set::extract($e_permission, '/element/id');
					}

				} else if (isset($wsp_permission) && !empty($wsp_permission)) {
					$e_permission1 = $this->objView->loadHelper('Common')->element_permission_details($workspace_id, $project_id, $user_id);

					if ((isset($group_id) && !empty($group_id))) {

						if (isset($e_permission1) && !empty($e_permission1)) {
							$ge_permissions = $this->objView->loadHelper('Group')->group_element_permission_details($workspace_id, $project_id, $group_id);
							$e_permission = array_merge($e_permission1, $ge_permissions);
						} else {
							$e_permission1 = $this->objView->loadHelper('Group')->group_element_permission_details($workspace_id, $project_id, $group_id);
						}
					}
					$e_permission = array_merge($e_permission, $e_permission1);
				}
			}
		}

		$task_ids = null;
		if (isset($e_permission) && !empty($e_permission)) {
			foreach ($e_permission as $key => $value) {
				if (dbExists('Element', $value)) {
					$data = getByDbId('Element', $value);
					$taskDate = $data['Element']['start_date'];
					$today = $this->objView->loadHelper('Wiki')->_displayDate(date('Y-m-d h:i:s A', strtotime(date('Y-m-d'))), $format = 'Y-m-d');
					$taskDate = $this->objView->loadHelper('Wiki')->_displayDate(date('Y-m-d h:i:s A', strtotime($taskDate)), $format = 'Y-m-d');
					if (strtotime($today) >= strtotime($taskDate)) {
						$task_ids[] = $value;
					}
				}
			}
		}

		return $task_ids;
	}

	function get_permit_todos($project_id, $user_id) {
		$list = [];

		$data = $this->DoList->find('all',
			[
				'joins' => [
					[
						'table' => 'do_list_users',
						'alias' => 'DoListUser',
						'type' => 'INNER',
						'conditions' => ['DoList.id = DoListUser.do_list_id'],
					],
				],
				'conditions' => [
					'DoList.project_id' => $project_id, 'DoList.parent_id' => 0, 'DoList.sign_off' => 0, 'DoListUser.approved' => 1,
					'OR' => [
						'DoList.user_id' => $user_id,
						'DoListUser.owner_id' => $user_id,
						'DoListUser.user_id' => $user_id,
					],
				],
			]);

		if (isset($data) && !empty($data)) {
			$list = Set::extract($data, '/DoList/id');
			$list = array_unique($list);
		}

		return $list;
	}

	function get_permit_sub_todos($project_id, $user_id) {
		$list = [];

		$data = $this->DoList->find('all',
			[
				'joins' => [
					[
						'table' => 'do_list_users',
						'alias' => 'DoListUser',
						'type' => 'INNER',
						'conditions' => ['DoList.id = DoListUser.do_list_id'],
					],
				],
				'conditions' => [
					'DoList.project_id' => $project_id, 'DoList.parent_id !=' => 0, 'DoList.sign_off' => 0, 'DoListUser.approved' => 1,
					'OR' => [
						'DoList.user_id' => $user_id,
						'DoListUser.owner_id' => $user_id,
						'DoListUser.user_id' => $user_id,
					],
				],
			]);

		if (isset($data) && !empty($data)) {
			$list = Set::extract($data, '/DoList/id');
			$list = array_unique($list);
		}
		return $list;
	}

	function project_all_users($project_id) {
		$allUsers = [];

		if (isset($project_id) && !empty($project_id)) {
			$projectUsers = $this->objView->loadHelper('TaskCenter')->userByProject([$project_id]);
			if (isset($projectUsers) && !empty($projectUsers)) {
				$projectUsers = array_unique($projectUsers['all_project_user']);
				$allUsers = $this->objView->loadHelper('TaskCenter')->user_exists($projectUsers);

				if (($key = array_search($this->user_id, $allUsers)) !== false) {
					unset($allUsers[$key]);
				}
			}
		}
		return $allUsers;
	}

}
