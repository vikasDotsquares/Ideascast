<?php
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
App::uses('HttpSocket', 'Network/Http');

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
	public $objView = null;

	public $components = array(
		'Session',
		'Common',
		'Auth',
		'RequestHandler',
		'Cookie',
		'Users',
	);

	var $uses = array('User', 'UserNotification', 'OrgPassPolicy');

	var $helpers = array('Html', 'Session', 'Form', 'Text', 'Time', 'Wiki', 'TaskCenter', 'Common', 'Permission', 'User');



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

		/* if  !dbExists('User', $this->Auth->user('id'))) {

			$this->redirect(array('controller' => 'users', 'action' => 'logout'));

		} */



		if ($this->Auth->loggedIn()) {

			// ************ Its set auto logout time
			//setcookie("LOGOUT-TIME-" . $this->Auth->user('id'), time() + 301800, time() + 301810, '/');

			$userDetails = $this->Common->userDetail($this->Auth->user('id'));

			//	pr($userDetails); die;
			if (isset($userDetails['UserDetail']['org_id']) && !empty($userDetails['UserDetail']['org_id'])) {

				$userOrgId = $userDetails['UserDetail']['org_id'];
				$orgPasswordPolicy = $this->OrgPassPolicy->find('first', array('conditions' => array('OrgPassPolicy.org_id' => $userOrgId)));

				if (isset($orgPasswordPolicy['OrgPassPolicy']['session_timeout']) && !empty($orgPasswordPolicy['OrgPassPolicy']['session_timeout'])) {
					$sessionTimeOut = $orgPasswordPolicy['OrgPassPolicy']['session_timeout'];
				}

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

			//$scope = array('User.status' => 1, 'User.role_id' => SITE_ADMIN);

			//$scope = array('User.status' => 1, 'User.role_id' => array(1, 3));
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


			//$this->Auth->loginRedirect = array('controller' => 'dashboards', 'action' => 'project_center', 'admin' => false);

			// set auth after login redirect =============================
				$userStartPageData = $this->User->find('first', ['conditions' => ['User.id' => $this->Auth->user('id')], 'recursive' => -1]);
				$page_setting_toggle = (isset($userStartPageData) && !empty($userStartPageData)) ? $userStartPageData['User']['page_setting_toggle'] : 0;
				$landing_url = (isset($userStartPageData) && !empty($userStartPageData)) ? $userStartPageData['User']['landing_url'] : null;

				if (isset($page_setting_toggle) && !empty($page_setting_toggle)) {
					if (isset($landing_url) && !empty($landing_url)) {
						$landing_url = explode('/', $landing_url);
						$landing_controller = $landing_url[0];
						$landing_action = $landing_url[1];

						$this->Auth->loginRedirect = array('controller' => $landing_controller, 'action' => $landing_action, 'admin' => false);

					} else {


						$this->Auth->loginRedirect = array('controller' => 'projects', 'action' => 'lists', 'admin' => false);

					}
				} else {

					$this->Auth->loginRedirect = array('controller' => 'projects', 'action' => 'lists', 'admin' => false);

				}
			//============================================================


			//$this->Auth->logoutRedirect	=   array('controller'=>'users', 'action'=>'login', 'admin' => false);
			$this->Auth->logoutRedirect = array('controller' => 'users', 'action' => 'login', 'admin' => false);

			//$scope = array('User.status' => array(0,1), 'User.role_id' => '2');
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
		 $project_lists_chat = array_filter($project_lists_chat);
		$this->set("project_lists", $project_lists_chat);


		$socket_userid = $this->Auth->user('id');
		$this->setJsVar('socket_name', $socket_userid);

		// set user's reminder popup setting to view and javascript of the current user
		$reminder_pop_up = $this->reminder_pop_up($this->Session->read('Auth.User.id'));
		$this->set("reminder_pop_up", false);
		$this->setJsVar('reminder_pop_up', false);


		// $isLoggedin = $this->Users->user_is_loggedin($this->Session->read('Auth.User.id'));
		// $this->set('isLoggedin', $isLoggedin);
		$this->setJsVar('stoken', "");
		if( $this->Session->check('stoken') ){


			$randomSessionStr = $this->Session->read('stoken');
			$this->setJsVar('stoken', $randomSessionStr);
		}


	}

	public function afterFilter() {
		// echo "I am an afterFilter";
	}

	public function beforeRender() {
		// Set the jsVars array which holds the variables to be used in js
		$this->_jsVars['page_load'] = true;
		$this->_jsVars['start_time'] = number_format(microtime(true) * 1000, 0, '.', '');
		$this->_jsVars['logs'] = true;
		$this->_jsVars['base_url'] = $this->webroot;
		$this->_jsVars['web_root'] = WWW_ROOT;
		$this->_jsVars['dir_sep'] = DS;
		//New Task Center Gloable Variable
		$this->_jsVars['task_centers'] = SITEURL.'dashboards/task_centers/';

		$hostname = $_SERVER['HTTP_HOST'];
		$whatINeed = explode('/', $_SERVER['REQUEST_URI']);
		$whatINeed = $whatINeed[1];

		if ($whatINeed == "ideascast") {
			$this->_jsVars['subdomain_base_url'] = PROTOCOL . $hostname . '/' . $whatINeed . '/';
		} else {
			$this->_jsVars['subdomain_base_url'] = 'https://' . $hostname . '/';
		}
		$this->_jsVars['live_setting'] = true;

		$this->_jsVars['document_domain'] = $_SERVER['SERVER_NAME'];

		// Search Pagination Settings
		$this->_jsVars['search_limit'] = 15;
		$this->_jsVars['search_adjacents'] = 1;

		$this->_jsVars['CHATURL'] = CHATURL;
		$this->_jsVars['SOCKETURL'] = SOCKETURL;
		$this->_jsVars['chat_enabled'] = CHAT_ENABLED;
		$this->_jsVars['socket_messages'] = SOCKET_MESSAGES;
		$this->_jsVars['MMURL'] = MMURL;
		$this->_jsVars['CHAT_CLOUD'] = CHAT_CLOUD;

		$user = $this->Auth->user();
		$dataUser = ['id' => $user['id'], 'email' => $user['email'], 'role_id' => $user['role_id']];

		$this->_jsVars['USER'] = $dataUser;
		//$this->_jsVars['USER']  = $this->Auth->user();
		$this->_jsVars['current_time'] = date('Y-m-d h:i:s');
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
	 * @param array $data collection of name and value pair
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

		/* $variables = (isset($data) && !empty($data) ) ? $data : null;

			$this->Cookie->name = 'collapse';
			$this->Cookie->time = 3600;  // or '1 hour'
			//$this->Cookie->path = '/bakers/preferences/';
			//$this->Cookie->domain = 'example.com';
			$this->Cookie->secure = false;  // i.e. only sent if using secure HTTPS
			$this->Cookie->key = 'qSI232qs*&sXOw!adre@34SAv!@*(XSL#$%)asGb$@11~_+!@#HKis~#^';
			$this->Cookie->httpOnly = false;
		*/
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

	public function todays_reminders($current_user_id = null, $reminder_elements = null) {

		$todays_reminder_list = [];
		if (isset($reminder_elements) && !empty($reminder_elements)) {
			foreach ($reminder_elements as $key => $value) {
				// e(date('Y-m-d h', strtotime($value['reminder_date'])));
				// e(date('Y-m-d h'));
				if (date('Y-m-d', strtotime($value['reminder_date'])) == date('Y-m-d')) {
					// if (date('Y-m-d h', strtotime($value['reminder_date'])) >= date('Y-m-d h') && date('Y-m-d h', strtotime($value['reminder_date'])) <= date('Y-m-d 23')) {
					$todays_reminder_list[] = $value;
				}
			}
		}

		return $todays_reminder_list;
	}
	//end todays_reminders()

	public function overdue_reminders($current_user_id = null, $reminder_elements = null) {

		$overdue_reminder_list = [];
		if (isset($reminder_elements) && !empty($reminder_elements)) {
			foreach ($reminder_elements as $key => $value) {
				if (date('Y-m-d', strtotime($value['reminder_date'])) < date('Y-m-d')) {
					$overdue_reminder_list[] = $value;
				}
			}
		}

		return $overdue_reminder_list;
	}
	//end overdue_reminders()

	public function upcoming_reminders($current_user_id = null, $reminder_elements = null) {

		$upcoming_reminder_list = [];
		if (isset($reminder_elements) && !empty($reminder_elements)) {
			foreach ($reminder_elements as $key => $value) {
				if (date('Y-m-d', strtotime($value['reminder_date'])) > date('Y-m-d')) {
					$upcoming_reminder_list[] = $value;
				}
			}
		}

		return $upcoming_reminder_list;
	}
	//end overdue_reminders()


	public function reminder_elements($current_user_id = null) {
		$current_user_id = (isset($current_user_id) && !empty($current_user_id)) ? $current_user_id : $this->Session->read('Auth.User.id');

		$reminder_elements = element_reminder($current_user_id);

		return $reminder_elements;
	}
	//end writeCookie()

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

	public function delete_user_notifications($user_id = null, $type = null) {
		$save_data = null;
		if (isset($user_id) && !empty($user_id)) {
			$conditions['receiver_id'] = $user_id;

			if (isset($type) && !empty($type)) {
				$conditions['type'] = $type;
			} else {
				// $conditions['OR'] = ['type !=' => 'reward', 'type IS NULL'];
				$conditions['OR'] = array( "NOT" => array(
									"type" => array("reward", "nudge") )
								,'type IS NULL' );
			}
			// pr($conditions, 1);
			if ($this->UserNotification->deleteAll($conditions)) {
				return true;
			}
		}
		return true;
	}

	protected function object2array($object) {
		if (is_object($object)) {
			foreach ($object as $key => $value) {
				$array[$key] = $value;
			}
		} else {
			$array = $object;
		}
		return $array;
	}

	protected function getInterface() {
		if (strpos($_SERVER['HTTP_USER_AGENT'], 'Mozilla') !== false) {
			$interface = 'WEB';
		} else {
			$interface = 'INTERNAL';
		}

		return $interface;
	}

	protected function api_check_user() {
		$response = array();
		$results = array();
		if (!empty($this->request->action)) {
			$api_key = (!empty($this->request->header('x-cake-key')) ? $this->request->header('x-cake-key') : (!empty($this->request->query['api_key']) ? $this->request->query['api_key'] : ''));
			$api_username = (!empty($this->request->header('x-cake-username')) ? $this->request->header('x-cake-username') : (!empty($this->request->query['api_username']) ? $this->request->query['api_username'] : ''));
			$api_email = (!empty($this->request->header('x-cake-email')) ? $this->request->header('x-cake-email') : (!empty($this->request->query['api_email']) ? $this->request->query['api_email'] : ''));
			$interface = $this->getInterface();

			if ($api_key != '' && $api_username != '' && $api_email != '') {
				$this->loadModel('ApiUser');
				$user = $this->ApiUser->find('all', array('conditions' => array('ApiUser.api_key' => $api_key, 'ApiUser.api_username' => $api_username, 'ApiUser.api_email' => $api_email)));
				if (!$user) {
					$statusCode = 950;
					$message = 'API credentials are incorrect.';
					$response = array('message' => $message, 'statusCode' => $statusCode, 'data' => array());
				} else {

					if ($user[0]['ApiUser']['status'] != 1) {
						$statusCode = 950;
						$message = 'Your API credentials are still not activated or deactivated by administrator. Please contact to administrator';
						$response = array('message' => $message, 'statusCode' => $statusCode, 'data' => array());
					} else {
						$returnReponse = true;
						if ($interface == 'WEB') {

							if ($user[0]['ApiUser']['web_execution_permission'] < 1) {
								$returnReponse = false;
							} else {
								$returnReponse = true;
							}

						} else {
							$returnReponse = true;
						}

						if ($returnReponse) {
							App::import('Vendor', 'firebase/src/JWT');
							$tokenId = base64_encode(mcrypt_create_iv(4));
							$issuedAt = time();
							$notBefore = $issuedAt + 10; //Adding 10 seconds
							$expire = $notBefore + 7200; // Adding 60 seconds
							$serverName = 'https://jeera.ideascast.com';
							$data = [
								'iat' => $issuedAt, // Issued at: time when the token was generated
								'jti' => $tokenId, // Json Token Id: an unique identifier for the token
								'iss' => $serverName, // Issuer

								'exp' => $expire, // Expire
								'data' => array('id' => $user[0]['ApiUser']['id'], 'api_key' => $user[0]['ApiUser']['api_key']),
							];
							$secretKey = $api_key;
							$jwt = JWT::encode($data, $secretKey, 'HS256');
							$unencodedArray = ['jwt' => $jwt];
							$statusCode = 200;
							$message = 'API User authenticate successfully.';
							$results = array('message' => $message, 'statusCode' => $statusCode, 'data' => json_encode($unencodedArray));
							$newJwt = json_decode($results['data']);
							try
							{
								$DecodedDataArray = JWT::decode($newJwt->jwt, $api_key, array('HS256'));
								$DecodedArray = $this->object2array($DecodedDataArray->data);

								$data['id'] = $DecodedArray['id'];
								$data['api_key'] = $DecodedArray['api_key'];

								$apiUserExists = $this->ApiUser->find('count', array('conditions' => array('ApiUser.id' => $data['id'], 'ApiUser.api_key' => $data['api_key'])));
								if ($apiUserExists > 0) {
									unset($data['data']);
									$response = array('message' => $message, 'statusCode' => $statusCode, 'data' => $data);
								} else {
									$response = array('message' => $message = 'Your JWT authentication failed', 'statusCode' => 950, 'data' => array());
								}

							} catch (Exception $e) {
								$response = array('message' => $message = 'Your API credentials failed', 'statusCode' => 950, 'data' => array());
							}
						} else {
							$statusCode = 950;
							$message = 'Your API credentials are not permitted for web api. Please contact to administrator';
							$response = array('message' => $message, 'statusCode' => $statusCode, 'data' => array());
						}
					}

				}
			} else {
				$statusCode = 950;
				$message = 'Please provide all API credentials details.';
				$response = array('message' => $message, 'statusCode' => $statusCode, 'data' => array());
			}

		} else {
			$statusCode = 950;
			$message = 'Please provide all API credentials details.';
			$response = array('message' => $message, 'statusCode' => $statusCode, 'data' => array());
		}
		return $response;
	}

	protected function renderApiResponse($response) {
		if ($response['statusCode'] != 200) {
			$this->set([
				'message' => $response['message'],
				'statusCode' => $response['statusCode'],
				'data' => $response['data'],
				'_serialize' => ['statusCode', 'message', 'data'],
			]);
			$ext = pathinfo($this->request->url, PATHINFO_EXTENSION);
			$this->response->type('application/' . $ext);
			if ($ext == 'json') {
				$this->response->body(json_encode($response));
			} else {

				$serialize = $this->viewVars['_serialize'];
				$rootNode = isset($this->viewVars['_rootNode']) ? $this->viewVars['_rootNode'] : 'response';
				if (is_array($serialize)) {
					$data = array($rootNode => array());
					foreach ($serialize as $alias => $key) {
						if (is_numeric($alias)) {
							$alias = $key;
						}
						$data[$rootNode][$alias] = $this->viewVars[$key];
					}
				} else {
					$data = isset($this->viewVars[$serialize]) ? $this->viewVars[$serialize] : null;
					if (is_array($data) && Hash::numeric(array_keys($data))) {
						$data = array($rootNode => array($serialize => $data));
					}
				}
				$options['pretty'] = true;
				$xml = Xml::fromArray($data, $options)->asXML();
				echo $this->response->body($xml);
			}
			$this->response->send();
			exit;
		}
	}

	protected function generateToken($term, $action, $controller) {
		App::import('Vendor', 'firebase/src/JWT');
		$tokenId = base64_encode(mcrypt_create_iv(4));
		$issuedAt = time();
		$notBefore = $issuedAt + 10; //Adding 10 seconds
		$expire = $notBefore + 7200; // Adding 60 seconds
		$serverName = 'https://jeera.ideascast.com';
		$data = [
			'iat' => $issuedAt, // Issued at: time when the token was generated
			'jti' => $tokenId, // Json Token Id: an unique identifier for the token
			'iss' => $serverName, // Issuer

			'exp' => $expire, // Expire
			'data' => array('term' => $term, 'action' => $action, 'controller' => $controller),
		];
		$secretKey = $term . ':' . $action . ':' . $controller;
		$jwt = JWT::encode($data, $secretKey, 'HS256');
		$unencodedArray = ['jwt' => $jwt];
		return $jwt;
		/*$statusCode = 200;
			$message = 'API User authenticate successfully.';
			$results = array('data' => $jwt);
		*/

	}
	protected function filterFields($keysArray, $response) {
		foreach ($keysArray as $keyModel => $keyArray) {
			if (!empty($response[$keyModel])) {
				$modelKeys = array_keys($response[$keyModel]);
				foreach ($modelKeys as $modelKey) {
					if (!in_array($modelKey, $keyArray)) {
						unset($response[$keyModel][$modelKey]);
					}
				}
			}
		}
		return $response;
	}
	protected function http_response($url) {

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, TRUE);
		curl_setopt($ch, CURLOPT_NOBODY, TRUE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		$head = curl_exec($ch);
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		return $httpCode;
	}

	protected function getRemoteFilesize($file_url, $formatSize = true) {
		$head = array_change_key_case(get_headers($file_url, 1));
		// content-length of download (in bytes), read from Content-Length: field

		$clen = isset($head['content-length']) ? $head['content-length'] : 0;

		// cannot retrieve file size, return "-1"
		if (!$clen) {
			return -1;
		}

		if (!$formatSize) {
			return $clen;
			// return size in bytes
		}

		$size = $clen;
		switch ($clen) {
		case $clen < 1024:
			$size = $clen . ' B';
			break;
		case $clen < 1048576:
			$size = round($clen / 1024, 2) . ' KB';
			break;
		case $clen < 1073741824:
			$size = round($clen / 1048576, 2) . ' MB';
			break;
		case $clen < 1099511627776:
			$size = round($clen / 1073741824, 2) . ' GB';
			break;
		}

		return $size;
		// return formatted size
	}

	protected function _sidebar_status($user_id = null) {
		$this->loadModel('User');
		$data = $this->User->find('first', array('conditions' => array('User.id' => $user_id), 'fields' => array("User.body_collapse")));
		$sidebar_status = (isset($data['User']["body_collapse"])) ? $data['User']["body_collapse"] : 0;
	}
}
