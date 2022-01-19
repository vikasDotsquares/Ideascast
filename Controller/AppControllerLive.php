sa<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('Controller', 'Controller');
App::uses('DatabaseSession', 'Model/Datasource/Session');

//echo '1 baar lock khol do Pukhraj ji';die;

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package		app.Controller
 * @link		http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {

	public $_reminder_elements = [];
	public $_todays_reminder_elements = [];
	public $_overdue_reminder_elements = [];
	public $_upcoming_reminder_elements = [];

	public $components = array(
		'Session',
		'Common',
		'Cookie',
		'Auth',
		'RequestHandler',
		'Cookie',
	);

	var $uses = array('User', 'OrgPassPolicy', 'UserNotification');

	var $helpers = array('Html', 'Session', 'Form', 'Text', 'Time', 'Wiki', 'Common');

	/**
	 * Array to hold js variables to be used in layout/view.
	 *
	 * @var   array $_jsVars Array of the js variables to be used in the layout/view
	 */
	var $_jsVars = array();

	var $language, $availableLanguages;

	public $smtp = array(
		'host' => 'mail.dotsquares.com',
		'port' => 587,
		'username' => 'wwwsmtp@dotsquares.com',
		'password' => 'dsmtp909#',
	);

	public function decode_session_data($encoded) {
		$backup = $_SESSION;
		$_SESSION = array();
		session_decode($encoded);
		$ret = $_SESSION;
		$_SESSION = $backup;
		return $ret;
	}

	public function beforeFilter() {

		// SEND REQUEST HEADER EACH TIME ON AN AJAX CALL
		if ($this->RequestHandler->isAjax()) {
			if (!$this->Auth->user()) {
				header('Requires-Auth: 1');
				exit;
			}
		}

		/* Get User theme and set for view */
		$userData = $this->User->find('first', ['conditions' => ['User.id' => $this->Auth->user('id')], 'recursive' => -1, 'fields' => 'my_theme,User.*']);

		$user_theme = (isset($userData) && !empty($userData)) ? $userData['User']['my_theme'] : 'theme_default';
		$this->set('user_theme', $user_theme);

		if ($this->Auth->loggedIn()) {

			// ************ Its set auto logout time
			setcookie("LOGOUT-TIME-" . $this->Auth->user('id'), time() + 301800, time() + 301810, '/');

			$userDetails = $this->Common->userDetail($this->Auth->user('id'));

			if (isset($userDetails['UserDetail']['org_id']) && !empty($userDetails['UserDetail']['org_id'])) {

				$userOrgId = $userDetails['UserDetail']['org_id'];

				$orgPasswordPolicy = $this->OrgPassPolicy->find('first', array('conditions' => array('OrgPassPolicy.org_id' => $userOrgId)));

				if (isset($orgPasswordPolicy['OrgPassPolicy']['session_timeout']) && !empty($orgPasswordPolicy['OrgPassPolicy']['session_timeout'])) {
					$sessionTimeOut = $orgPasswordPolicy['OrgPassPolicy']['session_timeout'];
				}

				//pr($sessionTimeOut);

				if (isset($sessionTimeOut) && !empty($sessionTimeOut)) {

					$session_id = $this->Session->id();

					setcookie('CAKEPHP', $session_id, time() + ($sessionTimeOut * 1800), "/");

					setcookie("LOGOUT-TIME-" . $this->Auth->user('id'), time() + ($sessionTimeOut * 1800), time() + ($sessionTimeOut * 1800), '/');

					$ams = ClassRegistry::init('CakeSessions');
					$data = $ams->find('first', array('conditions' => array('CakeSessions.id' => $session_id)));

					$data['CakeSessions']['expires'] = '101010101010';

					$newArray = $this->decode_session_data($data['CakeSessions']['data']);

					$newArray['Config']['time'] = '101010101010';

					$data['CakeSessions']['data'] = serialize($newArray);

					$data['CakeSessions']['id'] = $session_id;

				} else {

					Configure::write('Session', array(
						'defaults' => 'database', 'timeout' => 600, //10 hour
					));
				}
			}

		}

		if ($this->request->prefix == 'admin') {

			$this->layout = 'admin';

			// Specify which controller/action handles logging in:
			AuthComponent::$sessionKey = 'Auth.Admin.User';

			$this->Auth->loginAction = array('controller' => 'users', 'action' => 'login', 'admin' => true);
			$this->Auth->loginRedirect = array('controller' => 'dashboards', 'action' => 'index', 'admin' => true);
			$this->Auth->logoutRedirect = array('controller' => 'users', 'action' => 'login', 'admin' => true);

			$scope = array('User.status' => 1, 'User.role_id' => array(1));

			$this->Auth->authenticate = array('Form' => array(
				'userModel' => 'User',
				'fields' => array('username' => 'email', 'password' => 'password'),
				'scope' => $scope,
			));
			$this->Auth->allow('admin_login');

		} else {

			// Specify which controller/action handles logging in:
			AuthComponent::$sessionKey = 'Auth.User';
			$this->Auth->loginAction = array('controller' => 'users', 'action' => 'login', 'admin' => false);
			$this->Auth->loginRedirect = array('controller' => 'dashboards', 'action' => 'project_center', 'admin' => false);
			//$this->Auth->logoutRedirect	=   array('controller'=>'users', 'action'=>'login', 'admin' => false);
			$this->Auth->logoutRedirect = array('controller' => 'pages', 'action' => 'display', 'home', 'admin' => false);

			$scope = array('User.status' => array(0, 1));

			$this->Auth->authenticate = array('Form' => array(
				'userModel' => 'User',
				'fields' => array('username' => 'email', 'password' => 'password'),
				'scope' => $scope,
			));
			$this->Auth->allow('login');

		}

		$this->request->addDetector(
			'json',
			[
				'callback' => [$this, 'isJson'],
			]
		);

		$project_lists_chat = $this->Common->get_user_project_list_chat($this->Session->read("Auth.User.id"));

		$this->set("project_lists", $project_lists_chat);

		// set reminder elements to view and javascript of the current user
		$reminder_elements = $this->_reminder_elements = $this->reminder_elements();
		$this->set("reminder_elements", $reminder_elements);
		$this->setJsVar('reminder_elements', $reminder_elements);

		// set user's reminder popup setting to view and javascript of the current user
		$reminder_pop_up = $this->reminder_pop_up($this->Session->read('Auth.User.id'));
		$this->set("reminder_pop_up", $reminder_pop_up);
		$this->setJsVar('reminder_pop_up', $reminder_pop_up);

		// Get current user's setting for the Reminders that are ending today
		$todays_reminder_setting = $this->todays_reminder_setting($this->Session->read('Auth.User.id'));
		$this->set("todays_reminder_setting", $todays_reminder_setting);
		$this->setJsVar('todays_reminder_setting', $todays_reminder_setting);

		// Get current user's setting for the Reminders that are ending today
		$todays_reminders = $this->_todays_reminder_elements = $this->todays_reminders($this->Session->read('Auth.User.id'));
		$this->set("todays_reminders", $todays_reminders);
		$this->setJsVar('todays_reminders', $todays_reminders);

		// Get current user's setting for the Reminders that are ending today
		$overdue_reminders = $this->_overdue_reminder_elements = $this->overdue_reminders($this->Session->read('Auth.User.id'));
		$this->set("overdue_reminders", $overdue_reminders);
		$this->setJsVar('overdue_reminders', $overdue_reminders);

		// Get current user's setting for the Reminders that are ending today
		$upcoming_reminders = $this->_upcoming_reminder_elements = $this->upcoming_reminders($this->Session->read('Auth.User.id'));
		$this->set("upcoming_reminders", $upcoming_reminders);
		$this->setJsVar('upcoming_reminders', $upcoming_reminders);

		$socket_userid = $this->Auth->user('id');
		$this->setJsVar('socket_name', $socket_userid);
	}

	public function beforeRender() {

		// Set the jsVars array which holds the variables to be used in js
		$this->_jsVars['page_load'] = false;
		$this->_jsVars['logs'] = false;
		$this->_jsVars['base_url'] = $this->webroot;

		$hostname = $_SERVER['HTTP_HOST'];
		$whatINeed = explode('/', $_SERVER['REQUEST_URI']);
		$whatINeed = $whatINeed[1];

		if ($whatINeed == "ideascomposer_new") {
			$this->_jsVars['subdomain_base_url'] = 'http://' . $hostname . '/' . $whatINeed . '/';
		} else {
			$this->_jsVars['subdomain_base_url'] = 'https://' . $hostname . '/';
		}

		$this->_jsVars['web_root'] = WWW_ROOT;
		$this->_jsVars['dir_sep'] = DS;

		$this->_jsVars['live_setting'] = false;

		// Search Pagination Settings
		$this->_jsVars['search_limit'] = 15;
		$this->_jsVars['search_adjacents'] = 1;

		$this->_jsVars['CHATURL'] = CHATURL;
		$this->_jsVars['MMURL'] = MMURL;

		$this->_jsVars['USER'] = $this->Auth->user();

		$this->_jsVars['login_url'] = Router::url(array('controller' => 'users', 'action' => 'login', 'admin' => false));
		$this->set('jsVars', $this->_jsVars);

		$go_back = null;
		if ($this->referer()) {
			$go_back = $this->referer();
		}
		$this->set('go_back', $go_back);

		$userdetail = $this->Common->userDetail($this->Auth->user('id'));

		if (isset($userdetail['UserDetail']['org_id']) && $userdetail['UserDetail']['org_id'] > 0) {

			$this->loadModel('OrgPassPolicy');
			$this->loadModel('UserPassword');

			if (isset($orgPasswordPolicy['OrgPassPolicy']['change_pass_time']) && !empty($orgPasswordPolicy['OrgPassPolicy']['change_pass_time'])) {

				$orgPasswordPolicy = $this->UserPassword->find('first', array('conditions' => array('UserPassword.user_id' => $userdetail['UserDetail']['user_id']), 'order' => 'id DESC'));
				if (isset($orgPasswordPolicy['UserPassword']['created']) && !empty($orgPasswordPolicy['UserPassword']['created'])) {

					$userCreatedDate = date('Y-m-d', strtotime($orgPasswordPolicy['UserPassword']['created']));
					$todayDate = date('Y-m-d');
					$consumeDays = daysLeft($userCreatedDate, $todayDate);

				} else {

					$userCreatedDate = date('Y-m-d', strtotime($this->Auth->user('modified')));
					$todayDate = date('Y-m-d');
					$consumeDays = daysLeft($userCreatedDate, $todayDate);

				}

				if ($consumeDays > $orgPasswordPolicy['OrgPassPolicy']['change_pass_time']) {
					if ($this->request->controller != 'users' || $this->request->action != 'myaccountedit') {
						$this->Session->setFlash('Your password has been expired, please reset again.', 'error');
						return $this->redirect(array('controller' => 'users', 'action' => 'myaccountedit', 'admin' => false));
					}
				}

			}
		}
	}

	/**
	 * Check the request is JSON.
	 *
	 * @return Bool
	 */
	public function isJson() {
		return $this->response->type() === 'application/json';
	}

	/**
	 * Validate the request data against the cookie token.
	 *
	 * @param \Cake\Network\Request $request The request to validate against.
	 * @throws \Cake\Network\Exception\ForbiddenException when the CSRF token is invalid or missing.
	 * @return void
	 */
	protected function _validateToken($request, $params) {
		// pr($request->data['_Token']['key'], 1);
		$token = (isset($request->data['_Token']['key'])) ? $request->data['_Token']['key'] : null;
		$header = $request->header('X-CSRF-Token');

		if ($token !== $header) {
			throw new ForbiddenException(__d('cake', 'Invalid CSRF token.'));
		}
	}

	/**
	 * Validate the request data against the cookie token.
	 *
	 * @param \Cake\Network\Request $request The request to validate against.
	 * @throws \Cake\Network\Exception\ForbiddenException when the CSRF token is invalid or missing.
	 * @return void
	 */
	protected function _doc_file_ext($params = null) {
		$allowed_ext = [
			'bmp', 'gif', 'jpg', 'pps', 'png', 'psd', 'pspimage', 'tif', 'csv', 'dat', 'dbf', 'doc', 'docx', 'log', 'mdb', 'msg', 'ppt', 'pptx', 'rtf', 'txt', 'wpd', 'xlr', 'xls', 'xlsx', 'xml', 'html', 'xhtml', 'pdf', 'mp4',
		];
		$this->setJsVar('allowed_ext', $allowed_ext);
	}

	/**
	 * Method to set javascript variables
	 *
	 * This method puts the passed variable in an array. That array is
	 * then converted to json object in layout and can be used
	 * in js files
	 *
	 * @param string $name Name of the variable
	 * @param mixed $value Value of the variable
	 *
	 * @return void
	 */
	public function setJsVar($name, $value) {
		$this->_jsVars[$name] = $value;
	} //end setJsVar()

	/**
	 * Method to set javascript variables
	 *
	 * This method puts the passed variable in an array. That array is
	 * then converted to json object in layout and can be used
	 * in js files
	 *
	 * @param string $name Name of the variable
	 * @param mixed $value Value of the variable
	 *
	 * @return void
	 */
	public function setJsVars($data) {
		if (is_array($data)) {
			foreach ($data as $name => $value) {
				$this->_jsVars[$name] = $value;
			}
		}
	} //end setJsVars()

	// app_controller.php

	/**
	 * Function to send json response. This function is generally used when an ajax request is made
	 *
	 * @param array   $response Data to be sent in json response
	 *
	 * @return void
	 */
	public function sendJson($response) {
		// Make sure no debug info is printed
		//Configure::write('debug', 0); // Turn this to 2 for debugging
		// Set the data for view
		$this->set('response', $response);
		// We will use no layout
		$this->layout = 'ajax';
		// Render the json element
		// $this->render('/Elements/json_action/');
		$this->render(null, null, APP . 'views' . DS . 'Elements' . DS . 'templates' . DS . 'json_action.ctp');

		// $this->render(DS.'elements'.DS.'users'.DS.'modal');
	} //end sendJson()

	/**
	 * Function to send json response. This function is generally used when an ajax request is made
	 *
	 * @param array   $response Data to be sent in json response
	 *
	 * @return void
	 */
	public function createCookie($name = null) {
		// Set default cookie value: 1: collapse
		// Other options: 2: expand
		// Set this cookie for atleast 1 hour
		if (!$this->Cookie->check($name)) {
			$this->Cookie->write($name, '', false, '1 hour');
		}

	} //end createCookie()

	public function readCookie($name = null) {
		if (empty($name)) {
			return null;
		}

		return $this->Cookie->read($name);
	}
	//end readCookie()

	public function writeCookie($name = null, $value = null) {
		if (empty($name)) {
			return null;
		}

		if ($this->Cookie->check($name)) {
			return $this->Cookie->write($name, $value);
		}
	}
	//end writeCookie()

	public function reminder_pop_up($userid = null) {
		if (empty($userid)) {
			return false;
		}
		$userData = $this->User->find('first', ['conditions' => ['User.id' => $userid], 'recursive' => -1, 'fields' => 'reminder_notification']);
		return (!empty($userData['User']['reminder_notification'])) ? true : false;
	}
	//end reminder_pop_up()

	public function todays_reminder_setting($current_user_id = null) {

		$current_user_id = (isset($current_user_id) && !empty($current_user_id)) ? $current_user_id : $this->Session->read('Auth.User.id');

		$userData = $this->User->find('first', ['conditions' => ['User.id' => $current_user_id], 'recursive' => -1, 'fields' => 'today_reminder']);
		return (!empty($userData['User']['today_reminder'])) ? true : false;
	}
	//end todays_reminder_setting()

	public function todays_reminders($current_user_id = null) {

		$todays_reminder_list = [];
		if (isset($this->_reminder_elements) && !empty($this->_reminder_elements)) {

			foreach ($this->_reminder_elements as $key => $value) {
				if (isset($value['reminder_date']) && !empty($value['reminder_date'])) {
					$rmdate = date('Y-m-d', strtotime($value['reminder_date']));
					$tydate = date('Y-m-d');
					if ($rmdate == $tydate) {
						$todays_reminder_list[] = $value;
					}
				}
			}
		}
		return $todays_reminder_list;
	}
	//end todays_reminders()

	public function overdue_reminders($current_user_id = null) {

		$overdue_reminder_list = [];
		if (isset($this->_reminder_elements) && !empty($this->_reminder_elements)) {
			foreach ($this->_reminder_elements as $key => $value) {
				if (isset($value['reminder_date']) && !empty($value['reminder_date'])) {
					$rmdate = date('Y-m-d', strtotime($value['reminder_date']));
					$tydate = date('Y-m-d');
					if ($rmdate < $tydate) {
						$overdue_reminder_list[] = $value;
					}
				}
			}
		}

		return $overdue_reminder_list;
	}
	//end overdue_reminders()

	public function upcoming_reminders($current_user_id = null) {

		$upcoming_reminder_list = [];
		if (isset($this->_reminder_elements) && !empty($this->_reminder_elements)) {
			foreach ($this->_reminder_elements as $key => $value) {
				// e($value['reminder_date']);
				if (isset($value['reminder_date']) && !empty($value['reminder_date'])) {
					$rmdate = date('Y-m-d', strtotime($value['reminder_date']));
					$tydate = date('Y-m-d');
					if ($rmdate > $tydate) {
						$upcoming_reminder_list[] = $value;
					}
				}
			}
		}

		return $upcoming_reminder_list;
	}
	//end upcoming_reminders()

	public function reminder_elements($current_user_id = null) {

		$current_user_id = (isset($current_user_id) && !empty($current_user_id)) ? $current_user_id : $this->Session->read('Auth.User.id');

		$all_projects = $filter_projects = null;
		$myprojectlist = $myreceivedprojectlist = $mygroupprojectlist = array();

		// Find All current user's projects
		$myprojectlist = myprojects($current_user_id);
		$filter_projects['my'] = (isset($myprojectlist) && !empty($myprojectlist)) ? array_keys($myprojectlist) : [];

		$filter_projects['others'] = null;
		// Find All current user's received projects
		$myreceivedprojectlist = receivedprojects($current_user_id);
		$filter_projects['others'] = (isset($myreceivedprojectlist) && !empty($myreceivedprojectlist)) ? array_keys($myreceivedprojectlist) : null;
		// Find All current user's group projects
		$mygroupprojectlist = groupprojects($current_user_id);
		$group = (isset($mygroupprojectlist) && !empty($mygroupprojectlist)) ? array_keys($mygroupprojectlist) : null;

		if (isset($group) && !empty($group)) {
			$filter_projects['others'] = (isset($filter_projects['others']) && !empty($filter_projects['others'])) ? array_merge($filter_projects['others'], $group) : $group;
		}

		if (isset($filter_projects['others']) && !empty($filter_projects['others'])) {
			$filter_projects['others'] = array_unique($filter_projects['others']);
		}

		$reminder_elements = user_element_reminder($filter_projects, $current_user_id);

		return $reminder_elements;
	}
	//end reminder_elements()

	public function update_notification($id = null) {
		$save_data = null;
		if (isset($id) && !empty($id)) {
			$this->UserNotification->id = $id;
			if ($this->UserNotification->saveField('viewed', 1)) {
				return true;
			}
		}
		return true;
	}

	public function delete_notification($id = null) {
		$save_data = null;
		if (isset($id) && !empty($id)) {
			if ($this->UserNotification->delete($id)) {
				return true;
			}
		}
		return true;
	}

	public function delete_user_notifications($user_id = null) {
		$save_data = null;
		if (isset($user_id) && !empty($user_id)) {
			if ($this->UserNotification->deleteAll(['receiver_id' => $user_id])) {
				return true;
			}
		}
		return true;
	}

}
