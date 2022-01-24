<?php
App::uses('Controller/Component', 'Auth', 'Session', 'RequestHandler');
App::uses('CakeEmail', 'Network/Email');
App::uses('Sanitize', 'Utility');

class SettingsController extends AppController {

	var $name = 'Settings';
	public $uses = array('Setting', 'UserSetting', 'UserTransctionDetail', 'User', 'UserDetail', 'Currency', 'Coupon', 'UserInstitution', 'EmailNotification', 'Availability');

	/**
	 * check login for admin and frontend user
	 * allow and deny user
	 */
	public $components = array('Email', 'common', 'Image', 'CommonEmail', 'Auth', 'Settings','Users');
	public $mongoDB = null;
	public $live_setting;
	public $objView = null;
	public $user_id = null;
	/**
	 * Helpers
	 *
	 * @var array
	 */
	public $helpers = array('Html', 'Form', 'Session', 'Time', 'Text', 'Common', 'TaskCenter');

	public function beforeFilter() {
		parent::beforeFilter();
		$this->user_id = $this->Auth->user('id');
		$this->Auth->allow('index', 'sett', 'confirm', 'logout', 'activate', 'activation', 'plansummary', 'paymentsuccess', 'paymentcancel', 'thanks', 'coupons', 'user_theme');
		$this->live_setting = LIVE_SETTING;
		$view = new View();
		$this->objView = $view;
	}

	public function themes() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$response = [
				'success' => false,
				'msg' => null,
				'content' => null,
			];

			App::import("Model", "User");
			$this->User = new User();

			/*if ($this->request->is('get')) {
				$userData = $this->User->find('first', ['conditions' => ['User.id' => $this->Auth->user('id')], 'recursive' => -1, 'fields' => 'my_theme,secondary_theme']);
				$user_theme = (isset($userData) && !empty($userData)) ? $userData['User']['my_theme'] : 'theme_default';
				$secondary_user_theme = (isset($userData) && !empty($userData)) ? $userData['User']['secondary_theme'] : 'theme_seaweed_green';
				$this->set('user_theme', $user_theme);
				$this->set('secondary_user_theme', $secondary_user_theme);
			}*/
			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->data;
				if (isset($post['selected_theme']) && !empty($post['selected_theme'])) {

					$this->User->id = $this->user_id;

					$dateArray = array( 'my_theme'=>$post['selected_theme'], 'id'=>$this->user_id);
					// $dateArray = array('secondary_theme'=>$post['secondary_theme'],'my_theme'=>$post['selected_theme'],'id'=>$this->user_id);

					if ($this->User->save($dateArray)) {
						$response['success'] = true;
						$response['msg'] = 'Theme has been updated successfully.';
					}

					/* if( $post['secondary_theme'] == 'secondary' ){
						if ($this->User->saveField('secondary_theme', $post['selected_theme'])) {
							$response['success'] = true;
							$response['msg'] = 'Theme has been updated successfully.';
						}
					} else {
						if ($this->User->saveField('my_theme', $post['selected_theme'])) {
							$response['success'] = true;
							$response['msg'] = 'Theme has been updated successfully.';
						}
					} */

				}
				echo json_encode($response);
				exit();
			}
		}

	}

	public function start_page() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$response = [
				'success' => false,
				'msg' => null,
				'content' => null,
			];

			if ($this->request->is('get')) {
				$userData = $this->User->find('first', ['conditions' => ['User.id' => $this->Auth->user('id')], 'recursive' => -1, 'fields' => ['page_setting_toggle', 'landing_url', 'landing_id']]);
				$userData = (isset($userData) && !empty($userData)) ? $userData['User'] : null;
				$this->set('userData', $userData);

			}
			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->data;

				$page_setting_toggle = (isset($post['page_setting_toggle']) && !empty($post['page_setting_toggle'])) ? $post['page_setting_toggle'] : 0;
				if (isset($page_setting_toggle) && !empty($page_setting_toggle)) {
					$this->User->id = $this->user_id;
					$data['User'] = [
						'page_setting_toggle' => 1,
						'landing_url' => $post['landing_url'],
						'project_id' => 0,
					];

					if ($this->User->save($data)) {
						$response['success'] = true;
						$response['msg'] = 'Start page has been set successfully.';
						$response['url'] = $post['landing_url'];
					}
				} else {
					$this->User->id = $this->user_id;
					$data['User'] = [
						'page_setting_toggle' => 0,
						'landing_url' => null,
						'project_id' => 0,
					];
					if ($this->User->save($data)) {
						$response['success'] = true;
						$response['msg'] = 'Start page has been set successfully.';
						$response['url'] = 'dashboards/project_center';
					}
				}
				echo json_encode($response);
				exit();
			}
		}

	}

	public function notifications() {

		$this->layout = 'inner';
		$this->set('title_for_layout', __('Notifications', true));
		$this->set('page_heading', __('Notifications', true));
		$this->set('page_subheading', __('Set email notifications', true));

	}

	public function notification() {

		$this->layout = 'inner';
		$this->set('title_for_layout', __('Settings', true));
		$this->set('page_heading', __('Settings', true));
		$this->set('page_subheading', __('View options and customize your experience', true));

		if( $_SERVER['HTTP_HOST'] != LOCALIP )  {

			if( $this->Session->read('Auth.User.role_id') == 3 ){
				$this->redirect(['controller' => 'organisations','action' => 'dashboard']);
			}
			if( $this->Session->read('Auth.User.role_id') == 1 ){
				$this->redirect(['controller' => 'dashboard','action' => 'index']);
			}
		}
		// =========== Program Section
		$program_data = $this->EmailNotification->find('all', ['conditions' => ['EmailNotification.user_id' => $this->Session->read("Auth.User.id"), 'notification_type' => 'program']]);
		$this->set('program_data', $program_data);

		// =========== Project Section
		$project_data = $this->EmailNotification->find('all', ['conditions' => ['EmailNotification.user_id' => $this->Session->read("Auth.User.id"), 'notification_type' => 'project']]);
		$this->set('project_data', $project_data);

		// =========== Dashboard Section
		$dashboard_data = $this->EmailNotification->find('all', ['conditions' => ['EmailNotification.user_id' => $this->Session->read("Auth.User.id"), 'notification_type' => 'dashboard']]);
		$this->set('dashboard_data', $dashboard_data);

		// =========== Group Section
		$group_data = $this->EmailNotification->find('all', ['conditions' => ['EmailNotification.user_id' => $this->Session->read("Auth.User.id"), 'notification_type' => 'group']]);
		$this->set('group_data', $group_data);

		// =========== Workspace Section
		$workspace_data = $this->EmailNotification->find('all', ['conditions' => ['EmailNotification.user_id' => $this->Session->read("Auth.User.id"), 'notification_type' => 'workspace']]);
		$this->set('workspace_data', $workspace_data);

		// =========== Element Section
		$element_data = $this->EmailNotification->find('all', ['conditions' => ['EmailNotification.user_id' => $this->Session->read("Auth.User.id"), 'notification_type' => 'element']]);
		$this->set('element_data', $element_data);

		// =========== TeamTalk Section
		$teamtalk_data = $this->EmailNotification->find('all', ['conditions' => ['EmailNotification.user_id' => $this->Session->read("Auth.User.id"), 'notification_type' => 'team_talk']]);
		$this->set('teamtalk_data', $teamtalk_data);

		// =========== ToDo Section
		$todo_data = $this->EmailNotification->find('all', ['conditions' => ['EmailNotification.user_id' => $this->Session->read("Auth.User.id"), 'notification_type' => 'to_dos']]);
		$this->set('todo_data', $todo_data);

		// =========== Sketch Section
		$sketch_data = $this->EmailNotification->find('all', ['conditions' => ['EmailNotification.user_id' => $this->Session->read("Auth.User.id"), 'notification_type' => 'sketches']]);
		$this->set('sketch_data', $sketch_data);

		// =========== Assignment Section
		$assignment = $this->EmailNotification->find('all', ['conditions' => ['EmailNotification.user_id' => $this->Session->read("Auth.User.id"), 'notification_type' => 'assignment']]);
		$this->set('assignment', $assignment);

		$assignment_removed = $this->EmailNotification->find('all', ['conditions' => ['EmailNotification.user_id' => $this->Session->read("Auth.User.id"), 'notification_type' => 'assignment_removed']]);
		$this->set('assignment_removed', $assignment_removed);

		// =========== RiskCenter Section
		$riskcenter = $this->EmailNotification->find('all', ['conditions' => ['EmailNotification.user_id' => $this->Session->read("Auth.User.id"), 'notification_type' => 'riskcenter']]);
		$this->set('riskcenter', $riskcenter);

		$crumb = [
			'last' => ['Settings'],
		];
		$this->set('crumb', $crumb);

		$userData = $this->User->find('first', ['conditions' => ['User.id' => $this->Auth->user('id')], 'recursive' => -1, 'fields' => ['page_setting_toggle', 'landing_url', 'landing_id']]);
		$userData = (isset($userData) && !empty($userData)) ? $userData['User'] : null;
		$this->set('userData', $userData);

		$userData1 = $this->User->find('first', ['conditions' => ['User.id' => $this->Auth->user('id')], 'recursive' => -1, 'fields' => 'my_theme,secondary_theme']);
		$user_theme = (isset($userData1) && !empty($userData1)) ? $userData1['User']['my_theme'] : 'theme_default';
		// $secondary_user_theme = (isset($userData1) && !empty($userData1)) ? $userData1['User']['secondary_theme'] : 'theme_seaweed_green';
		$this->set('user_theme', $user_theme);
		// $this->set('secondary_user_theme', $secondary_user_theme);

		$current_tab = '';
		if(isset($this->params['named']) && !empty($this->params['named'])){
			if(isset($this->params['named']['tab']) && !empty($this->params['named']['tab'])){
				$current_tab = $this->params['named']['tab'];
			}
		}
		$this->set('current_tab', $current_tab);
	}

	public function getAllProjects($user_id = null) {

		$projects = null;

		if (isset($user_id) && !empty($user_id)) {

			App::import('Controller', 'Users');
			$Users = new UsersController;
			$projects = null;
			$myprojectlist = $myreceivedprojectlist = $mygroupprojectlist = array();
			// Find All current user's projects
			$myprojectlist = $Users->__myproject_selectbox($user_id);
			// Find All current user's received projects
			$myreceivedprojectlist = $Users->__receivedproject_selectbox($user_id, 1);
			// Find All current user's group projects
			$mygroupprojectlist = $Users->__groupproject_selectbox($user_id, 1);

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

		}
		return (isset($projects) && !empty($projects)) ? $projects : false;
	}

	/*********************** Admin Panel Common Functions Start **************************/

	public function admin_index() {

		$orConditions = array();
		$andConditions = array();
		$finalConditions = array();

		$in = 0;
		$per_page_show = $this->Session->read('setting.per_page_show');
		if (empty($per_page_show)) {
			$per_page_show = ADMIN_PAGING;
		}

		if (isset($this->data['Setting']['keyword'])) {
			$keyword = trim($this->data['Setting']['keyword']);
		} else {
			$keyword = $this->Session->read('setting.keyword');
		}

		if (isset($keyword)) {
			$this->Session->write('Setting.keyword', $keyword);
			$keywords = explode(" ", $keyword);
			if (isset($keywords[0]) && !empty($keywords[0]) && count($keywords) < 2) {
				$keyword = $keywords[0];
				$in = 1;
				$orConditions = array('OR' => array(
					'Setting.title LIKE' => '%' . $keyword . '%',
					'Setting.description LIKE' => '%' . $keyword . '%',
					'Setting.settingtype_monthly LIKE' => '%' . $keyword . '%',
					'Setting.settingtype_yearly LIKE' => '%' . $keyword . '%',
					'Setting.id LIKE' => '%' . $keyword . '%',
				));
			} else if (!empty($keywords) && count($keywords) > 1) {
				$name = $keywords[0];
				$sign = $keywords[1];
				$in = 1;
				$andConditions = array('AND' => array(
					'Setting.title LIKE' => '%' . $name . '%',
					'Setting.description LIKE' => '%' . $sign . '%',
					'Setting.settingtype_monthly LIKE' => '%' . $keyword . '%',
					'Setting.settingtype_yearly LIKE' => '%' . $keyword . '%',
				));
			}
		}

		if (isset($this->data['Setting']['status'])) {
			$status = $this->data['Setting']['status'];
		} else {
			$status = $this->Session->read('setting.status');
		}

		if (isset($status)) {
			$this->Session->write('setting.status', $status);
			if ($status != '') {
				$in = 1;
				$andConditions = array_merge($andConditions, array('Setting.status' => $status));
			}
		}

		if (isset($this->data['Setting']['per_page_show']) && !empty($this->data['Setting']['per_page_show'])) {
			$per_page_show = $this->data['Setting']['per_page_show'];
		}

		if (!empty($orConditions)) {
			$finalConditions = array_merge($finalConditions, $orConditions);
		}
		if (!empty($andConditions)) {
			$finalConditions = array_merge($finalConditions, array('AND' => $andConditions));
		}

		$count = $this->Setting->find('count', array('conditions' => $finalConditions));
		$this->set('count', $count);

		$this->set('title_for_layout', __('All Settings', true));
		$this->Session->write('setting.per_page_show', $per_page_show);
		$this->Setting->recursive = 0;
		$this->paginate = array('conditions' => $finalConditions, "limit" => $per_page_show, "order" => "Setting.id DESC");
		$this->set('settings', $this->paginate('Setting'));
		$this->set('in', $in);
	}

	function admin_setting_resetfilter() {
		$this->Session->write('setting.keyword', '');
		$this->Session->write('setting.status', '');
		$this->redirect(array('action' => 'index'));
	}

	public function admin_setting_updatestatus() {
		if ($this->request->is('ajax')) {
			$this->autoRender = false;
			$this->loadModel('Setting');
			$this->request->data['Setting'] = $this->request->data;

			if ($this->Setting->save($this->request->data, false)) {
				$this->Session->setFlash(__('Setting status has been updated successfully.'), 'success');
				die('success');
			} else {
				$this->Session->setFlash(__('Setting status could not updated successfully.'), 'error');
			}
		}
		die('error');
	}

	/**
	 * admin_source_delete method
	 *
	 * @throws MethodNotAllowedException
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function admin_delete($id = null) {
		if (isset($this->data['id']) && !empty($this->data['id'])) {
			$id = $this->data['id'];
			$this->Setting->id = $id;
			if (!$this->Setting->exists()) {
				throw new NotFoundException(__('Invalid Setting'), 'error');
			}

			if ($this->Setting->delete()) {
				$this->Session->setFlash(__('Setting has been deleted successfully.'), 'success');
				die('success');
			} else {
				$this->Session->setFlash(__('Setting could not deleted successfully.'), 'error');
			}
		}
		die('error');
	}

	/**
	 * admin_add method
	 *
	 * @return void
	 */
	public function admin_add() {
		$this->set('title_for_layout', __('Add Setting', true));
		if ($this->request->is('post') || $this->request->is('put')) {
			//pr($this->request->data); die;

			if (isset($this->request->data['Setting']['status']) && $this->request->data['Setting']['status'] == "on") {
				$this->request->data['Setting']['status'] = "1";
			} else {
				$this->request->data['Setting']['status'] = "0";
			}

			if ($this->Setting->save($this->request->data, true)) {
				$this->Session->setFlash(__('Setting has been saved successfully.'), 'success');
				$this->redirect(array('action' => 'index'));
			}
		}
	}
	/**
	 * admin_edit method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function admin_edit($id = null) {
		$this->set('title_for_layout', __('Edit Setting', true));
		$this->Setting->id = $id;
		if (!$this->Setting->exists()) {
			$this->Session->setFlash(__('Invalid Setting.'), 'error');
			$this->redirect(array('action' => 'index'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			//  pr($this->request->data);die;

			if (isset($this->request->data['Setting']['status']) && $this->request->data['Setting']['status'] == "on") {
				$this->request->data['Setting']['status'] = "1";
			} else {
				$this->request->data['Setting']['status'] = "0";
			}
			if ($this->Setting->save($this->request->data)) {
				$this->Session->setFlash(__('The Setting has been updated successfully.'), 'success');
				$this->redirect(array('controller' => 'dashboards', 'action' => 'index'));
			}
		} else {
			$this->request->data = $this->Setting->read(null, $id);

			//pr($data); die;

		}
	}

	public function updatebody($id = null, $uid) {
		$this->request->data['User']['id'] = $uid;

		$this->loadModel('User');
		if (isset($id) && !empty($id)) {
			$this->request->data['User']['body_collapse'] = '1';
		} else {
			$this->request->data['User']['body_collapse'] = '0';
		}

		//pr($this->request->data['Setting']); die;
		$this->User->Save($this->request->data, false);
		die;
	}

	public function updatewsp($id = null, $uid) {
		$this->request->data['User']['id'] = $uid;
		$this->loadModel('User');
		if ($id == 1) {
			$this->request->data['User']['wsp_collapse'] = '1';
		} else {
			$this->request->data['User']['wsp_collapse'] = '0';
		}

		// pr($this->request->data ); die;
		$this->User->Save($this->request->data, false);
		die;
	}

	public function updatevalue($id = null) {
		$this->loadModel('User');
		$data = $this->User->find('first', array('conditions' => array('User.id' => $id), 'fields' => array("User.body_collapse")));
		return $data['User']["body_collapse"];
	}

	public function updatevalueWsp($id = null) {
		$this->loadModel('User');
		$data = $this->User->find('first', array('conditions' => array('User.id' => $id), 'fields' => array("User.wsp_collapse")));
		return $data['User']["wsp_collapse"];
	}

	public function updatevalueShr($id = null) {
		$this->loadModel('User');
		$data = $this->User->find('first', array('conditions' => array('User.id' => $id), 'fields' => array("User.shr_collapse")));
		return $data['User']["shr_collapse"];
	}

	public function updatevalueRequest($id = null) {
		$this->loadModel('User');
		$data = $this->User->find('first', array('conditions' => array('User.id' => $id), 'fields' => array("User.request_collapse")));
		return $data['User']["request_collapse"];
	}

	public function update_request($id = null, $uid) {
		$this->request->data['User']['id'] = $uid;

		$this->loadModel('User');
		if ($id == 1) {
			$this->request->data['User']['request_collapse'] = '1';
		} else {
			$this->request->data['User']['request_collapse'] = '0';
		}

		// pr($this->request->data ); die;
		$this->User->Save($this->request->data, false);
		die;
	}

	public function update_shr($id = null, $uid) {
		$this->request->data['User']['id'] = $uid;

		$this->loadModel('User');
		if ($id == 1) {
			$this->request->data['User']['shr_collapse'] = '1';
		} else {
			$this->request->data['User']['shr_collapse'] = '0';
		}

		// pr($this->request->data ); die;
		$this->User->Save($this->request->data, false);
		die;
	}

	public function sett($id) {

		$data = $this->Setting->find('first', array('conditions' => array('Setting.id' => 1), 'fields' => array("Setting.$id")));
		return $data['Setting']["$id"];
	}

	public function session_timeout() {

		if ($this->request->isAjax()) {

			$response = null;

			$this->layout = 'ajax';

			if ($this->request->is('post') || $this->request->is('put')) {
				pr($this->Auth->user());
			}
		}
	}

	public function timezone($id = null) {

		$this->loadModel('Timezone');
		$ifExistZone = $this->Timezone->findByUserId($this->Session->read("Auth.User.id"));

/* 		$timeZones = DateTimeZone::listIdentifiers(DateTimeZone::PER_COUNTRY, 'US');
		foreach ( $timeZones as $key => $zoneName )
		{
			$tz = new DateTimeZone($zoneName);
			$loc = $tz->getLocation();
			print($zoneName . " = " . $loc['comments'] . "<br>");
		} */

		//$tz = new DateTimeZone("Europe/Prague");

		//print($tz );

		if (isset($ifExistZone) && !empty($ifExistZone)) {

			if((!isset($ifExistZone['Timezone']['name']) || empty($ifExistZone['Timezone']['name'])) || ($ifExistZone['Timezone']['name'] ==  'Etc/Unknown')){
				$ifExistZone['Timezone']['name'] = 'Europe/London';
			}

			$target_time_zone = new DateTimeZone($ifExistZone['Timezone']['name']);
			$kolkata_date_time = new DateTime('now', $target_time_zone);
			$time = $kolkata_date_time->format('P');

			$this->request->data['Timezone']['id'] = $ifExistZone['Timezone']['id'];
			$this->request->data['Timezone']['name'] = $this->request->data['Cname'];
			$setTimezone = 'UTC ' . $time;
			$this->request->data['Timezone']['timezone'] = $setTimezone;

			$this->Timezone->Save($this->request->data, false);
			$this->Users->addUser($this->Session->read("Auth.User.id"), true);

		} else {


			if((!isset($this->request->data['Cname']) || empty($this->request->data['Cname'])) || ($this->request->data['Cname'] ==  'Etc/Unknown')){
				$this->request->data['Cname'] = 'Europe/London';
			}

			$target_time_zone = new DateTimeZone($this->request->data['Cname']);
			$kolkata_date_time = new DateTime('now', $target_time_zone);
			$time = $kolkata_date_time->format('P');

			$offset = $target_time_zone->getOffset($kolkata_date_time);
			//echo $setTimezone = 'UTC '.$time;

			$this->request->data['Timezone']['user_id'] = $this->Session->read("Auth.User.id");
			$this->request->data['Timezone']['name'] = $this->request->data['Cname'];
			$this->request->data['Timezone']['timezone'] = $target_time_zone;
			$this->Settings->updateTimezoneUser($this->request->data['Cname'], $offset);
			//$this->Timezone->Save($this->request->data, false);

			$this->Users->addUser($this->Session->read("Auth.User.id"), true);
		}

		$ifExistZone = $this->Timezone->findByUserId($this->Session->read("Auth.User.id"));
		if ($this->live_setting == true) {

			if((!isset($this->request->data['Cname']) || empty($this->request->data['Cname'])) || ($this->request->data['Cname'] ==  'Etc/Unknown')){
				$this->request->data['Cname'] = 'Europe/London';
			}

			$dateTimeZone = new DateTimeZone($this->request->data['Cname']);
			$dateTime = new DateTime("now", $dateTimeZone);
			$offset = $dateTimeZone->getOffset($dateTime);

			$ip = $_SERVER['REMOTE_ADDR'];

			if ($_SERVER['SERVER_NAME'] != LOCALIP) {
				$details = json_decode(file_get_contents("http://ipinfo.io/{$ip}"));

				if (isset($details) & !empty($details)) {
					if (isset($details->country) && !empty($details->country)) {
						$co = $details->country;
					} else {
						$co = 'IN';
					}
					$this->request->data['UserDetail']['id'] = $this->Session->read("Auth.User.UserDetail.id");
					$this->request->data['UserDetail']['current_country_id'] = $co;
					$this->UserDetail->save($this->request->data, false);
				}

			}

			// CONNECT WITH MONGO DB
			$this->Settings->updateTimezoneUser($this->request->data['Cname'], $this->request->data['offset']);

			$this->Users->addUser($this->Session->read("Auth.User.id"), true);

			/* $mongo = new MongoClient(MONGO_CONNECT);
				$this->mongoDB = $mongo->selectDB(MONGO_DATABASE);
				$mongo_collection = new MongoCollection($this->mongoDB, 'users');

				$ret = $mongo_collection->update(
					[
						'id' => intval($this->Session->read("Auth.User.id")),
					],
					[
						'$set' =>
						[
							'timezone' => [
								'name' => $this->request->data['Cname'],
								'offset' => $this->request->data['offset'],
							],
						],
					]
			*/
			//pr($ret);
		}

		die;
	}

	/*********************** Admin Panel Common Functions Start ************************* */

	public function updateChatStats($pid = null, $uid = null, $status = 0) {

		$this->loadModel('ProjectStat');

		$data = $this->ProjectStat->find('first', array('conditions' => array('ProjectStat.user_id' => $uid, 'ProjectStat.project_id' => $pid)));

		if (isset($data) && !empty($data)) {
			$this->request->data = $data;
		} else {
			$this->request->data['ProjectStat']['user_id'] = $uid;
			$this->request->data['ProjectStat']['project_id'] = $pid;
		}

		if ($status == 1) {
			$this->request->data['ProjectStat']['status'] = '1';
		} else {
			$this->request->data['ProjectStat']['status'] = '0';
		}

		//pr($this->request->data ); die;
		$this->ProjectStat->Save($this->request->data, false);
		die;
	}

	public function updateChatStatsCheck($uid = null, $pid = null) {

		$this->loadModel('ProjectStat');

		$data = $this->ProjectStat->find('first', array('conditions' => array('ProjectStat.user_id' => $uid, 'ProjectStat.project_id' => $pid)));

		if (isset($data) && !empty($data)) {
			$stat = $data['ProjectStat']['status'];
			return (isset($stat) && !empty($stat)) ? $stat : 0;
		} else {
			return 0;
		}

		die;

	}

	public function email_notification() {
		//$this->loadModel('EmailNotification');

		if ($this->request->is('post') || $this->request->is('put')) {

			$email_type = (isset($this->request->data['email_type']) && !empty($this->request->data['email_type'])) ? $this->request->data['email_type'] : '';

			$conditions = array(
				'EmailNotification.user_id' => $this->Session->read("Auth.User.id"),
				// 'EmailNotification.notification_type' => $email_type
			);

			if ($this->EmailNotification->hasAny($conditions)) {

				if ((isset($this->request->data['notify_toggle']) && isset($this->request->data['notify_toggle_web'])) && ($this->request->data['notify_toggle'] == 1 && $this->request->data['notify_toggle_web'] == 1)) {

					$this->EmailNotification->deleteAll($conditions);

				} else if (!isset($this->request->data['notify_toggle']) && $this->request->data['notify_toggle_web'] == 1) {

					$this->EmailNotification->updateAll(
						array('EmailNotification.web' => 0),
						array('EmailNotification.user_id' => $this->Session->read("Auth.User.id"))
					);

				} else if ($this->request->data['notify_toggle'] == 1 && !isset($this->request->data['notify_toggle_web'])) {

					$this->EmailNotification->updateAll(array('EmailNotification.email' => 0), array('EmailNotification.user_id' => $this->Session->read("Auth.User.id")));

				}
			}

			$notificationData = array();
			$notificationemail = null;
			$notificationmobile = null;
			$notificationweb = null;

			foreach ($this->request->data['EmailNotification'] as $key => $listval) {

				foreach ($listval as $keyval => $emalVal) {

					if (isset($emalVal['email']) && $emalVal['email'] == 'on') {
						$notificationemail = 1;
					} else {
						$notificationemail = 0;
					}
					if (isset($emalVal['mob']) && $emalVal['mob'] == 'on') {
						$notificationmobile = 1;
					} else {
						$notificationmobile = 0;
					}
					if (isset($emalVal['web']) && $emalVal['web'] == 'on') {
						$notificationweb = 1;
					} else {
						$notificationweb = 0;
					}

					if (isset($emalVal['id']) && !empty($emalVal['id'])) {
						$id = $emalVal['id'];
					} else {
						$id = 0;
					}

					if ((isset($this->request->data['notify_toggle']) && isset($this->request->data['notify_toggle_web'])) && ($this->request->data['notify_toggle'] == 1 && $this->request->data['notify_toggle_web'] == 1)) {

						$notificationData[] = array(
							'user_id' => $this->Session->read("Auth.User.id"),
							'notification_type' => $key,
							'personlization' => $keyval,
							'mobile' => $notificationmobile,
							'web' => $notificationweb,
							'email' => $notificationemail,
							'id' => $id,
						);

					} else if (!isset($this->request->data['notify_toggle']) && (isset($this->request->data['notify_toggle_web']) && $this->request->data['notify_toggle_web'] == 1)) {

						$notificationData[] = array(
							'user_id' => $this->Session->read("Auth.User.id"),
							'notification_type' => $key,
							'personlization' => $keyval,
							'web' => $notificationweb,
							'id' => $id,
						);
					} else if ((isset($this->request->data['notify_toggle']) && $this->request->data['notify_toggle'] == 1) && !isset($this->request->data['notify_toggle_web'])) {

						$notificationData[] = array(
							'user_id' => $this->Session->read("Auth.User.id"),
							'notification_type' => $key,
							'personlization' => $keyval,
							'email' => $notificationemail,
							'id' => $id,
						);
					}
				}
			}

			//pr($notificationData); die;

			$this->EmailNotification->saveAll($notificationData);
			$this->redirect('notification/tab:tnotify');
			die;
		}

	}

	public function notification_setting() {

		if ($this->request->isAjax()) {
			$this->layout = 'ajax';

			$response = [
				'success' => false,
				'msg' => null,
				'content' => null,
				'notType' => null,
			];

			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->data;

				if (isset($post['email_notification']) && !empty($post['email_notification'])) {
					$this->User->id = $this->user_id;

					$email_notification = 1;
					if ($post['email_notification'] == 'Off') {
						$email_notification = 0;
					}

					$this->User->id = $this->Session->read("Auth.User.id");

					if ($this->User->saveField('email_notification', $email_notification)) {
						$this->Session->write("Auth.User.email_notification", $email_notification);

						if ($email_notification == 0) {

							$conditions = array(
								'EmailNotification.user_id' => $this->Session->read("Auth.User.id"),
								'EmailNotification.email' => 0,
							);

							//	$this->EmailNotification->updateAll(array('EmailNotification.email' => 0),array('EmailNotification.user_id' => $this->Session->read("Auth.User.id")));

						}

						$response['notType'] = $email_notification;
						$response['success'] = true;
						$response['msg'] = 'Notification has been updated successfully.';
					}
				}
				echo json_encode($response);
				exit();
			}
		}

	}

	public function notification_setting_web() {

		if ($this->request->isAjax()) {
			$this->layout = 'ajax';

			$response = [
				'success' => false,
				'msg' => null,
				'content' => null,
				'notType' => null,
			];

			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->data;

				if (isset($post['web_notification']) && !empty($post['web_notification'])) {
					$this->User->id = $this->user_id;

					$web_notification = 1;
					if ($post['web_notification'] == 'Off') {
						$web_notification = 0;
					}

					$this->User->id = $this->Session->read("Auth.User.id");

					if ($this->User->saveField('web_notification', $web_notification)) {
						$this->Session->write("Auth.User.web_notification", $web_notification);

						if ($web_notification == 0) {

							$conditions = array(
								'EmailNotification.user_id' => $this->Session->read("Auth.User.id"),
								'EmailNotification.web' => 0,
							);

						}

						$response['notType'] = $web_notification;
						$response['success'] = true;
						$response['msg'] = 'Web Notification has been updated successfully.';
					}
				}
				echo json_encode($response);
				exit();
			}
		}
	}

	public function availability() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

		}

	}

	public function create_availability() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$response = [
				'success' => false,
				'msg' => null,
			];

			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->data;

				$post['user_id'] = $this->Session->read("Auth.User.id");

				if (!empty($post['avail_start_date']) && !empty($post['avail_end_date'])) {
					//WHERE new_start < existing_end AND new_end   > existing_start

					if (isset($post['full_day']) && !empty($post['full_day'])) {
						// $daysdiff = round((strtotime($post['avail_end_date']) - strtotime($post['avail_start_date'])) / 86400, 1);
						$daysdiff = strtotime($post['avail_end_date']) - strtotime($post['avail_start_date']);
						$post['avail_start_date'] = date('Y-m-d', strtotime($post['avail_start_date'])) . ' 00:00:00';
						$post['avail_end_date'] = date('Y-m-d', strtotime($post['avail_end_date'])) . ' 00:00:00';

						if ($daysdiff < 0) {

							$response['success'] = false;
							$response['content'] = 'From date-time should be less than to date-time.';

						} else {
							$this->Availability->save($post);

							$response['success'] = true;
							$response['msg'] = 'Not availability has been added successfully.';
						}
					} else {
						$s = explode(' ', $post['avail_start_date']);
						$e = explode(' ', $post['avail_end_date']);
						if ($s[1] == '12:00' && $s[2] == 'AM') {
							$post['avail_start_date'] = $s[0] . ' 00:00:00';
						}
						if ($e[1] == '12:00' && $e[2] == 'AM') {
							$post['avail_end_date'] = $e[0] . ' 00:00:00';
						}
						$hourdiff = round((strtotime($post['avail_end_date']) - strtotime($post['avail_start_date'])) / 3600, 1);

						if ($hourdiff < 0) {

							$response['success'] = false;
							$response['content'] = 'From date-time should be less than to date-time.';

						} else {
							//$post['avail_start_date'] = date('Y-m-d H:i:s', strtotime($post['avail_start_date']));
							//$post['avail_end_date'] = date('Y-m-d H:i:s', strtotime($post['avail_end_date']));
							$this->Availability->save($post);

							$response['success'] = true;
							$response['msg'] = 'Not availability has been added successfully.';
						}
					}

				}

				echo json_encode($response);
				exit();
			}
		}

	}

	public function update_availability() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$response = [
				'success' => false,
				'content' => null,
			];

			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->data;

				if (!empty($post['avail_start_date']) && !empty($post['avail_end_date'])) {
					if (isset($post['full_day']) && !empty($post['full_day'])) {
						// $daysdiff = round((strtotime($post['avail_end_date']) - strtotime($post['avail_start_date'])) / 86400, 1);
						$daysdiff = strtotime($post['avail_end_date']) - strtotime($post['avail_start_date']);
						$post['avail_start_date'] = date('Y-m-d', strtotime($post['avail_start_date'])) . ' 00:00:00';
						$post['avail_end_date'] = date('Y-m-d', strtotime($post['avail_end_date'])) . ' 00:00:00';

						if ($daysdiff < 0) {

							$response['success'] = false;
							$response['content'] = 'From date-time should be less than to date-time.';

						} else {
							$this->Availability->save($post);

							$response['success'] = true;
							$response['msg'] = 'Not availability has been added successfully.';
						}
					} else {
						$s = explode(' ', $post['avail_start_date']);
						$e = explode(' ', $post['avail_end_date']);
						if ($s[1] == '12:00' && $s[2] == 'AM') {
							$post['avail_start_date'] = $s[0] . ' 00:00:00';
						}
						if ($e[1] == '12:00' && $e[2] == 'AM') {
							$post['avail_end_date'] = $e[0] . ' 00:00:00';
						}

						$hourdiff = round((strtotime($post['avail_end_date']) - strtotime($post['avail_start_date'])) / 3600, 1);
						if ($hourdiff < 0) {

							$response['success'] = false;
							$response['content'] = 'From date-time should be less than to date-time.';

						} else {
							$this->Availability->save($post);

							$response['success'] = true;
							$response['msg'] = 'Not availability has been added successfully.';
						}
					}

				}

				echo json_encode($response);
				exit();
			}
		}

	}

	public function current_availability() {

		if ($this->request->isAjax()) {

			$view = new View();
			$ViewModel = $view->loadHelper('ViewModel');

			$this->layout = 'ajax';
			$response = [
				'success' => true,
				'content' => null,
			];
			$currentavail = $this->objView->loadHelper('ViewModel')->currentAvaiability($this->Session->read('Auth.User.id'));
			$this->set('avail_data', $currentavail);
			$this->set('type', 'current');
			$this->set('past', false);
			return $this->render('/Settings/partials/available_data');
		}

	}

	public function future_availability() {

		if ($this->request->isAjax()) {

			$view = new View();
			$ViewModel = $view->loadHelper('ViewModel');

			$this->layout = 'ajax';
			$response = [
				'success' => true,
				'content' => null,
			];
			$upcoming = $this->objView->loadHelper('ViewModel')->upcomingAvaiability($this->Session->read('Auth.User.id'));
			$this->set('avail_data', $upcoming);
			$this->set('type', 'upcoming');
			$this->set('past', false);
			return $this->render('/Settings/partials/available_data');
		}

	}

	public function past_availability() {

		if ($this->request->isAjax()) {

			$view = new View();
			$ViewModel = $view->loadHelper('ViewModel');

			$this->layout = 'ajax';
			$response = [
				'success' => true,
				'content' => null,
			];
			$past = $this->objView->loadHelper('ViewModel')->pastAvaiability($this->Session->read('Auth.User.id'));
			$this->set('avail_data', $past);
			$this->set('type', 'past');
			$this->set('past', true);
			return $this->render('/Settings/partials/available_data');
		}

	}

	public function create_avail_form() {

		if ($this->request->isAjax()) {

			$view = new View();
			$ViewModel = $view->loadHelper('ViewModel');

			$this->layout = 'ajax';
			$response = [
				'success' => true,
				'content' => null,
			];

			return $this->render('/Settings/partials/create_avail_form');
		}

	}

	public function remove_availability() {

		if ($this->request->isAjax()) {
			$this->layout = 'ajax';

			$response = [
				'success' => false,
				'content' => null,
			];

			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;
				// pr($post, 1);
				$this->Availability->id = $post['id'];
				if ($this->Availability->exists()) {
					$this->Availability->delete($post['id']);
					$response['success'] = true;
				}
			}
			echo json_encode($response);
			exit();
		}
	}

	public function get_unavailable() {

		if ($this->request->isAjax()) {
			$this->layout = 'ajax';

			$response = [
				'success' => false,
				'content' => null,
			];
			$all_unavailable = [];

			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;
				$user_unavail = $this->Availability->find('all', ['conditions' => ['Availability.user_id' => $this->user_id]]);
				if (isset($user_unavail) && !empty($user_unavail)) {
					$response['success'] = true;
					foreach ($user_unavail as $key => $value) {
						$data = $value['Availability'];
						// pr($data);
						$stEx = explode(' ', $data['avail_start_date']);
						$enEx = explode(' ', $data['avail_end_date']);
						$start_date = date('Y-m-d', strtotime($stEx[0]));
						$end_date = date('Y-m-d', strtotime($enEx[0]));
						$end = new DateTime($end_date);
						$end = $end->modify('+1 day');
						$daterange = new DatePeriod(new DateTime($start_date), new DateInterval('P1D'), $end);
						foreach ($daterange as $date) {
							$all_unavailable[] = $date->format("Y-n-j");
						}
					}
					$response['content'] = $all_unavailable;
				}
				//
			}
			echo json_encode($response);
			exit();
		}
	}

	function isRangeClashing($StartDate1, $EndDate1, $StartDate2, $EndDate2) {
		return ($StartDate1 <= $EndDate2) && ($StartDate2 <= $EndDate1);
	}

	public function projects_elements_total() {
		if ($this->request->isAjax()) {

			$this->layout = false;
			$response = ['success' => false, 'content' => null];

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;

				$current_user_id = $this->user_id;

				$all_user_projects = [];
				$mprojects = get_my_projects($current_user_id);
				$rprojects = get_rec_projects($current_user_id, 1, 1);
				$gprojects = group_rec_projects($current_user_id, 2);
				if (isset($mprojects) && !empty($mprojects)) {
					$all_user_projects = $all_user_projects + $mprojects;
				}
				if (isset($rprojects) && !empty($rprojects)) {
					$all_user_projects = $all_user_projects + $rprojects;
				}
				if (isset($gprojects) && !empty($gprojects)) {
					$all_user_projects = $all_user_projects + $gprojects;
				}

				$all_user_projects_total = ( isset($all_user_projects) && !empty($all_user_projects) ) ? count($all_user_projects) : 0;

				$userElements = $this->objView->loadHelper('TaskCenter')->userElementsDemo($current_user_id, array_keys($all_user_projects));
				$total_elements = 0;
				if (isset($userElements) && !empty($userElements)) {
					foreach ($userElements as $ekey => $evalue) {
						$total_elements++;
					}
					$response['success'] = true;
				}
				$response['content'] = ['total_elements' => $total_elements, 'all_user_projects' => $all_user_projects_total];
			}

			echo json_encode($response);
			exit();

		}
	}

	public function user_profile($user_id = 1, $project_id = null) {
		$this->loadModel('Timezone');
		if ($this->request->isAjax()) {

			$this->layout = false;
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

			$user_details = $this->User->find('first', ['conditions' => ['User.id' => $user_id]]);

			$org = '';
			if (isset($user_details['User']['parent_id']) && !empty($user_details['User']['parent_id'])) {
				$org_name = $this->User->find('first', ['conditions' => ['User.id' => $user_details['User']['parent_id']]]);
				if (isset($org_name['UserDetail']) && !empty($org_name['UserDetail'])) {
					$org = $org_name['UserDetail']['org_name'];
				}

			}

			$this->set('org_name', $org);
			$this->set('user_timezone', $userTimezone);
			$this->set('user_details', $user_details);
			$this->set('referer', $this->referer());
			$this->set('project_id', $project_id);


			$conditions = null;
			$conditions['UserProject.user_id'] = $this->user_id;
			$conditions['UserProject.status'] = 1;
			$conditions['Project.studio_status'] = 0;
			$conditions['UserProject.project_id !='] = '';
			// $conditions['Project.image_file !='] = '';
			$this->loadModel('Project');
			$projects = $this->Project->find('all', array(
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
				'conditions' => $conditions,
				'fields' => array('Project.id', 'Project.title', 'Project.image_file'),
				'order' => 'UserProject.modified DESC',
				'group' => ['UserProject.project_id'],
				'recursive' => -1,
				// 'limit' => rand(1,15)
			));
			$this->set('projects', $projects);

			$this->render('/Settings/partials/user_profile');
		}
		else{
			$this->redirect(array('controller' => 'users', 'action' => 'login'));
		}
	}

	/*
	 * TASK REMINDERS
	 */
	public function show_reminder() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$this->layout = false;

			$response = [
				'success' => true,
				'content' => null,
			];
			$reminder_elements = $this->reminder_elements();
			$today_reminder_elements = $this->todays_reminders($this->user_id, $reminder_elements);
			$overdue_reminder_elements = $this->overdue_reminders($this->user_id, $reminder_elements);
			$upcoming_reminder_elements = $this->upcoming_reminders($this->user_id, $reminder_elements);

			$this->set('reminder_elements', $reminder_elements);
			$this->set('today_reminder_elements', $today_reminder_elements);
			$this->set('overdue_reminder_elements', $overdue_reminder_elements);
			$this->set('upcoming_reminder_elements', $upcoming_reminder_elements);

			return $this->render('/Settings/partials/show_reminder');
		}

	}

	public function reminder_setting() {

		if ($this->request->isAjax()) {

			$this->layout = false;

			$response = [
				'success' => false,
				'content' => null,
			];
			$userData = $this->User->find('first', ['conditions' => ['User.id' => $this->user_id], 'recursive' => -1, 'fields' => 'reminder_notification']);
			if(!empty($userData['User']['reminder_notification'])) {
				$response['success'] = true;
			}
			$reminder_elements = $this->reminder_elements();
			$todays_reminders = $this->todays_reminders($this->user_id, $reminder_elements);
			$upcoming_reminders = $this->overdue_reminders($this->user_id, $reminder_elements);
			$overdue_reminders = $this->upcoming_reminders($this->user_id, $reminder_elements);

			if ((!isset($todays_reminders) || empty($todays_reminders)) && (!isset($upcoming_reminders) || empty($upcoming_reminders)) && (!isset($overdue_reminders) || empty($overdue_reminders))) {
				$response['success'] = false;
			}
		}

		echo json_encode($response);
		exit();

	}


	/*
	 * SAVE USER SIDEBAR MENU COLLAPSE/EXPAND SETTING
	 */
	public function sidebar_menu_status() {

		if ($this->request->isAjax()) {

			$this->layout = false;

			$response = [
				'success' => false,
			];

			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;
				// field names:
				// wsp_collapse = social menu
				// shr_collapse = delivery menu
				// request_collapse = assets menu

				$field_name = $post['field_name'];
				$value = $post['value'];

				$this->User->id = $this->user_id;
				if($this->User->saveField($field_name, $value)){
					$response['success'] = true;
				}

			}

		}

		echo json_encode($response);
		exit();

	}

	public function user_theme($user_id = null) {

		$this->autoLayout = false;
		$this->render(false);

		$user_theme = user_theme($user_id);
		$response = [
			'success' => true,
			'content' => $user_theme,
		];
		$this->response->body(json_encode($response));
		$this->response->statusCode(200);
		$this->response->type('application/json');

		return $this->response;

		die;

	}


	/*
	 * SAVE USER LOCATION
	 */
	public function update_user_location() {
		$this->loadModel('UserLocation');
		if ($this->request->isAjax()) {

			$this->layout = false;

			$response = [
				'success' => false,
			];

			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;
				$location_id = $post['id'];
				$pre_loc_id = null;
				// get last entry of the current user
				$current_location = $this->UserLocation->find('all', ['conditions' => ['UserLocation.user_id' => $this->user_id], 'limit' => 1, 'order' => 'UserLocation.id DESC']);
				if(isset($current_location) && !empty($current_location)){
					$this->UserLocation->id = $current_location[0]['UserLocation']['id'];
					if($this->UserLocation->saveField('end_datetime', date('Y-m-d H:i:s'))){}
					$this->UserLocation->id = null;
					$pre_loc_id = $current_location[0]['UserLocation']['user_location_type_id'];
				}
				if(empty($pre_loc_id) || $pre_loc_id != $location_id){
					$inData = [
							'user_id' => $this->user_id,
							'user_location_type_id' => $location_id,
							'start_datetime' => date('Y-m-d H:i:s')
						];
						// pr(date('Y-m-d H:i:s'));
					if($this->UserLocation->save($inData)){
						$response['success'] = true;
					}
				}

			}

		}

		echo json_encode($response);
		exit();

	}
	/*
	 * GET USER AVAILABILITY
	 */
	public function get_availability_status() {
		$this->loadModel('UserLocation');
		if ($this->request->isAjax()) {

			$this->layout = false;

			$response = [
				'success' => false,
				'content' => false,
			];

			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;
				$user_id = $post['user_id'];
				$user_unavailability = $this->objView->loadHelper('User')->user_unavailability($user_id);
				$response['content'] = $user_unavailability;
				$response['success'] = true;

			}

		}

		echo json_encode($response);
		exit();

	}

	public function userPrompt() {

		if ($this->request->isAjax()) {
		$user_id = CakeSession::read("Auth.User.id");
		$id = $this->request->data['is_prompt'];

		$this->request->data['User']['id'] = $user_id;

		$this->loadModel('User');
		if (isset($id) && !empty($id)) {
			$this->request->data['User']['is_prompt'] = '1';
		} else {
			$this->request->data['User']['is_prompt'] = '0';
		}

		//pr($this->request->data['Setting']); die;
		$this->User->id = $user_id;
		$this->User->Save($this->request->data, false);

		die;
		}
	}


	public function get_availability($user_id = null) {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$response = [
				'success' => true,
				'msg' => '',
				'content' => null,
			];

			$this->loadModel('UserAvailability');

			$data = $this->UserAvailability->find('all', [
				'conditions' => ['UserAvailability.user_id' => $this->user_id ],
				'order' => ['UserAvailability.effective DESC'],
			]);

			$view = new View($this, false);
			$view->viewPath = 'Settings/partials';
			$view->set('user_id', $this->user_id );
			$view->set('data', $data);
			$html = $view->render('get_availability');

			echo $html;
			exit();
		}
	}

	public function availability_dates() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$response = [
				'success' => false,
				'content' => null,
			];

			$this->loadModel('UserAvailability');
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				$user_id = $this->user_id;
				$conditions = ['UserAvailability.user_id' => $this->user_id];
				if(isset($post['id']) && !empty($post['id'])){
					$conditions['NOT'] = ["UserAvailability.id" => $post['id']];
				}
				$data = $this->UserAvailability->find('all', [
					'conditions' => $conditions,
					'fields' => ['UserAvailability.effective'],
				]);
				if(isset($data) && !empty($data)){
					$response['success'] = true;
					$data = Set::extract($data, '{n}.UserAvailability.effective');
					$output = array_map(function($val) {
						return date('d-m-Y', strtotime($val));
					}, $data);
					$response['content'] = $output;
				}
			}

			echo json_encode($response);
			exit();
		}
	}

	public function user_hr_day() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$response = [
				'success' => false,
				'content' => null,
			];

			$this->loadModel('UserAvailability');
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				$user_id = $this->user_id;
				$response['success'] = true;
				$response['content'] = $this->objView->loadHelper('Scratch')->work_availability();
			}

			echo json_encode($response);
			exit();
		}
	}

	public function delete_data($id = null) {
		if ($this->request->isAjax()) {

			$this->layout = false;

			$this->loadModel('UserAvailability');

			$id = $this->data['id'];
			$this->UserAvailability->id = $id;
			if (!$this->UserAvailability->exists()) {
				throw new NotFoundException(__('Invalid Setting'), 'error');
			}

			if ($this->UserAvailability->delete()) {
			$response = [
				'success' => true,
				'msg' => '',
				'content' => null,
			];
			echo json_encode( $response);
			}
			die;
		}

	}

	public function find_availability($id = null) {

		if ($this->request->isAjax()) {

			$this->layout = false;

			$this->loadModel('UserAvailability');

			$data = $this->UserAvailability->find('first', [
				'conditions' => ['UserAvailability.user_id' => $this->user_id ,'UserAvailability.id' => $id],
				'order' => ['UserAvailability.effective DESC'],
			]);

			$data['UserAvailability']['effective'] = date('d M, Y', strtotime($data['UserAvailability']['effective']));

			echo json_encode( $data['UserAvailability']);
			die;
		}
	}

	public function save_availability($id = null) {
			if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$response = [
				'success' => false,
				'msg' => '',
				'content' => null,
			];

			if ($this->request->is('post') || $this->request->is('put')) {

				$this->loadModel('UserAvailability');
				$this->UserAvailability->set($this->request->data);
				if ($this->UserAvailability->validates()) {
					$post = $this->request->data;
					$post['effective'] = date('Y-m-d', strtotime($post['effective']));
					$response['content'] = [$post['user_id']];
					// pr($post,1);

					$count = $this->UserAvailability->find('count',['conditions' => ['UserAvailability.effective'=>$post['effective'], 'UserAvailability.user_id' => $post['user_id']]]);

					if(isset($post['id']) && !empty($post['id'])){
						$count = 0;
					}

					if(!isset($count) || $count < 1) {
						if ($this->UserAvailability->save($post)) {
							$response['success'] = true;
						}
					}else{
						$response['content'] = [$post['user_id']];
						$response['msg'] = ['Select a new Effective From date'];

					}

					echo json_encode($response);
				}
			}
			}die;
		}

}
