<?php

/**
 * Organisation controller.
 *
 * This file will render views from views/Organisation/
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
App::uses('Controller/Component', 'Auth', 'Session', 'RequestHandler');
App::uses('CakeEmail', 'Network/Email');
App::uses('SimplePasswordHasher', 'Controller/Component/Auth');
App::uses('Sanitize', 'Utility');
App::import('Lib', 'XmlApi');
/**
 * Organisations Controller
 *
 * @property User $User
 * @property PaginatorComponent $Paginator
 */
class OrganisationsController extends AppController {

	var $name = 'Organisations';

	public $uses = array('User', 'UserDetail', 'OrganisationUser', 'ManageDomain', 'OrgPassPolicy', 'OrgSetting', 'Domain', 'Setting', 'Template', 'TemplateDetail', 'TemplateRelation', 'TemplateLike', 'TemplateReview', 'AreaRelation', 'ElementRelation', 'ElementRelationDocument', 'TemplateCategory', 'UserPassword', 'TemplateMove','Department');

	//$this->Auth->allow('update_color');

	public $objView;
	private $protectedDatabases = array(
		'information_schema',
	);
	/**
	 * check login for admin and frontend user
	 * allow and deny user
	 */
	public $components = array('Email', 'Common', 'Image', 'CommonEmail', 'Auth', 'Group','Users');

	public $live_setting;

	public $mongoDB = null;

	public $user_offset;
	/**
	 * Helpers
	 *
	 * @var array
	 */
	public $helpers = array('Html', 'Form', 'Session', 'Time', 'Text', 'Common', 'Group', 'Wiki');

	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow('index', 'admin_restore_database', 'testcron', 'testemail', 'projectScheduleOverdueCron', 'workspaceScheduleOverdueCron', 'elementScheduleOverdueCron', 'todoScheduleOverdueCron', 'update_todays_reminder_setting_cron', 'getTemplate', 'riskScheduleOverdueCron', 'skts_cron', 'used_space_notification_cron', 'domain_total_size', 'chatSize','create_subdomain_file','domain_document_size','getDirectorySize','create_subdomain_temp_file','opuscast_name','opuscast_version','watchScheduleCron');

		$view = new View();
		$this->objView = $view;
		$this->live_setting = LIVE_SETTING;

		$this->user_offset = 50;

	}

	/* *********** Front End Panel Common Functions Start  *********** */

	public function index() {
		if (!$this->Auth->loggedIn()) {
			return $this->redirect(array('controller' => 'pages', 'action' => 'display', 'home', 'admin' => false));
		}
		return $this->redirect(array('controller' => 'pages', 'action' => 'display', 'home', 'admin' => false));
	}

	/* ********* Admin Panel Functions Start  **** */

	/**
	 * Displays a view
	 *
	 * @param mixed What page to display
	 * @return void
	 */
	public function admin_index() {

		$orConditions = array();
		//$andConditions = array('NOT' => array('User.role_id' => array(1)));
		$andConditions = array('User.role_id' => 3);
		$finalConditions = array();

		$in = 0;
		$per_page_show = $this->Session->read('user.per_page_show');
		if (empty($per_page_show)) {
			$per_page_show = ADMIN_PAGING;
		}

		$this->User->unbindModel(array('hasOne' => array('OrganisationUser')), false);

		if (isset($this->data['User']['keyword'])) {
			$keyword = trim($this->data['User']['keyword']);
		} else {
			$keyword = $this->Session->read('user.keyword');
		}

		if (isset($this->data['UserDetail']['country_id']) && !empty($this->data['UserDetail']['country_id'])) {
			$county = trim($this->data['UserDetail']['country_id']);
			$this->Session->write('user.country', $county);
			$in = 1;
			$andConditions1 = array(
				'UserDetail.country_id LIKE' => '%' . $county . '%',
			);

			$andConditions = array_merge($andConditions, $andConditions1);
		}
		//pr($keyword); die;

		if (isset($keyword)) {
			$this->Session->write('user.keyword', $keyword);
			$keywords = explode(" ", $keyword);
			if (isset($keywords[0]) && !empty($keywords[0]) && count($keywords) < 2) {
				$keyword = $keywords[0];
				$in = 1;
				$orConditions = array('OR' => array(
					'User.email LIKE' => '%' . $keyword . '%',
					'UserDetail.org_name LIKE' => '%' . $keyword . '%',
					'UserDetail.first_name LIKE' => '%' . $keyword . '%',
					'UserDetail.last_name LIKE' => '%' . $keyword . '%',
					//'UserDetail.country_id LIKE' => '%' . $county . '%'
				));
			} else if (!empty($keywords) && count($keywords) > 1) {
				$first_name = $keywords[0];
				$last_name = $keywords[1];
				$in = 1;
				$andConditions = array('AND' => array(
					'UserDetail.first_name LIKE' => '%' . $first_name . '%',
					'UserDetail.last_name LIKE' => '%' . $last_name . '%',
					//'UserDetail.country_id LIKE' => '%' . $county . '%'
				));
			}
		}

		if (isset($this->data['User']['status'])) {
			$status = $this->data['User']['status'];
		} else {
			$status = $this->Session->read('user.status');
		}

		if (isset($status)) {
			$this->Session->write('user.status', $status);
			if ($status != '') {
				$in = 1;
				$andConditions = array_merge($andConditions, array('User.status' => $status));
			}
		}

		if (isset($this->data['User']['per_page_show']) && !empty($this->data['User']['per_page_show'])) {
			$per_page_show = $this->data['User']['per_page_show'];
		}

		if (!empty($orConditions)) {
			$finalConditions = array_merge($finalConditions, $orConditions);
		}

		if (!empty($andConditions)) {
			$finalConditions = array_merge($finalConditions, array('AND' => $andConditions));
		}

		$count = $this->User->find('count', array('conditions' => $finalConditions));
		$this->paginate = array('conditions' => $finalConditions, "limit" => $per_page_show, "order" => "User.created DESC");

		$this->set('count', $count);
		$this->set('title_for_layout', __('All Organizations', true));
		$this->Session->write('user.per_page_show', $per_page_show);
		$this->User->recursive = 0;
		$this->set('users', $this->paginate('User'));
		$this->set('in', $in);

	}

	function admin_resetfilter() {
		$this->Session->write('user.keyword', '');
		$this->Session->write('OrganisationUser.domain_name', '');
		$this->Session->write('user.status', '');
		$this->redirect(array('action' => 'index'));
	}

	function admin_list_resetfilter($org_id = null) {
		$this->Session->write('orgsetting.keyword', '');
		$this->Session->write('orgsetting.status', '');
		$this->redirect(array('action' => 'list_domain', $org_id));
	}

	public function admin_add() {
		$this->set('title_for_layout', __('Add Organization', true));

		if ($this->request->is('post') || $this->request->is('put')) {

			if ($this->User->validates()) {

				$this->request->data['UserDetail']['org_password'] = $this->request->data['User']['password'];

				//$this->request->data['UserDetail']['org_password'] = AES_ENCRYPT($this->request->data['UserDetail']['org_password'],'secret');

				if ($this->User->saveAssociated($this->request->data)) {

					$userId = $this->User->getLastInsertID();

					$sqlN = "update user_details set user_details.org_password = AES_ENCRYPT(org_password, 'secret')  WHERE user_details.user_id =" . $userId;

					$this->UserDetail->query($sqlN);

					$this->Session->setFlash(__('The Organization details have been saved successfully.'), 'success');
					$this->redirect(array('action' => 'index'));

				}
			}
		}
	}

	public function admin_add_domain($id = null) {

		$this->set('title_for_layout', __('Add Domain', true));

		if (isset($id)) {
			$organisationname = $this->UserDetail->find('first', array('conditions' => array('UserDetail.user_id' => $id)));
			$this->set('organisationname', $organisationname['UserDetail']['org_name']);
			$this->set('id', $id);
		}
		/* if ( empty($id) ) {
				$this->redirect(array('action' => 'index'));
			}  */

		$ideaVersion = $this->Setting->find('first', array('conditions' => array('Setting.id' => 1)));
		$ideasCastVersion = '';
		$this->set('ideasCastVersion', '1.0');
		if (isset($ideaVersion['Setting']['idesversion']) && !empty($ideaVersion['Setting']['idesversion'])) {
			$this->set('ideasCastVersion', $ideaVersion['Setting']['idesversion']);
			$ideasCastVersion = $ideaVersion['Setting']['idesversion'];
		}

		if ($this->request->is('post') || $this->request->is('put')) {

			//if ( !preg_match("/^[a-zA-Z](\-?[a-zA-Z0-9]+)+[a-zA-Z]$/", $this->request->data['OrgSetting']['subdomain']) ) {
			if (!preg_match("/^[a-zA-Z0-9\-]*$/", $this->request->data['OrgSetting']['subdomain'])) {

				$this->request->data;
				$this->Session->setFlash(__('Provided domain is not a valid domain.'), 'error');
				return false;

			}
			//pr($this->OrgSetting->validator());die;
			$this->OrgSetting->set($this->request->data);

			if ($this->OrgSetting->validates($this->request->data)) {

				//if( 2 < 3 && $this->OrgSetting->validates( ) ) {

				if (isset($this->request->data['OrgSetting']['subdomain']) && !empty($this->request->data['OrgSetting']['subdomain'])) {
					$this->request->data['OrganisationUser']['domain_name'] = strtolower($this->request->data['OrgSetting']['subdomain']);
				}

				/* =========== Start SubDomain Configuration ========================== */

				$dbpassword = $this->Common->generatestrongpassword();
				$returnChechVal = $this->Common->checksubdomain(strtolower($this->request->data['OrganisationUser']['domain_name']), $dbpassword);

				/* =========== End SubDomain Configuration ======================== */

				//$returnChechVal = 'success';

				if ($returnChechVal == 'success') {
					$lastUserID = '';
					$lastorg_id = '';

					$dbpassword = $this->Common->generatestrongpassword();
					// It should be opened when it is going to deploy on server
					$returnVal = $this->Common->addsubdomain(strtolower($this->request->data['OrganisationUser']['domain_name']), $dbpassword);

					$lastUserID = $this->request->data['OrgSetting']['user_id'];

					$this->request->data['OrganisationUser']['id'] = '';
					$this->request->data['OrganisationUser']['user_id'] = $lastUserID;

					//$this->request->data['OrganisationUser']['creator_id'] = $this->request->data['OrganisationUser']['creator_id'];
					$this->request->data['OrganisationUser']['creator_id'] = $this->Session->read('Auth.User.id');

					$this->request->data['OrganisationUser']['domain_name'] = strtolower($this->request->data['OrganisationUser']['domain_name']);

					if ($this->OrganisationUser->save($this->request->data)) {
						$lastorg_id = $this->OrganisationUser->getLastInsertId();
						$this->request->data['OrgSetting']['org_id'] = $lastorg_id;
					}

					//if( !empty(DOMAINPREFIX) ){
					$domainPrefix = DOMAINPREFIX;
					//} else {
					//$domainPrefix = DOMAIN_ALISE;
					//}

					/* $dbname = $dbuser = $domainPrefix.$this->request->data['OrganisationUser']['domain_name'];
									$dbuser = $this->request->data['OrganisationUser']['domain_name']; */

					$ndbname = preg_replace('/[.,]/', '', strtolower($this->request->data['OrganisationUser']['domain_name']));
					$latestdb = str_replace('-', '', $ndbname);
					$dbname = $domainPrefix . $latestdb;
					$dbuser = $latestdb;
					if (strlen($latestdb) > 16) {
						$dbuser = substr($latestdb, 0, 16);
					}

					$getLastPort = $this->OrgSetting->find('first', array('order' => array('OrgSetting.id DESC'), 'fields' => array('MAX(OrgSetting.mongo_port) as mongo_port'), 'limit' => 1));
					if (isset($getLastPort) && count($getLastPort) > 0) {
						$domainLastPort = $getLastPort['0']['mongo_port'];
					} else {
						$domainLastPort = DOMAIN_LAST_PORT;
					}

					$getLastPortMM = $this->OrgSetting->find('first', array('order' => array('OrgSetting.id DESC'), 'fields' => array('MAX(OrgSetting.mm_port) as mm_port'), 'limit' => 1));

					if (isset($getLastPortMM) && !empty($getLastPortMM) && count($getLastPortMM) > 0) {
						$mmportvalueLast = $getLastPortMM['0']['mm_port'];
					} else {
						$mmportvalueLast = MMPORT_VALUE_LAST;
					}

					if ($mmportvalueLast < MMPORT_VALUE_LAST) {

						$mmportvalueLast = MMPORT_VALUE_LAST;
					}

					if ($domainLastPort < DOMAIN_LAST_PORT) {

						$domainLastPort = DOMAIN_LAST_PORT;
					}

					$this->request->data['OrgSetting']['user_id'] = $lastUserID;
					$this->request->data['OrgSetting']['jeera_version'] = $ideasCastVersion;
					//$this->request->data['OrgSetting']['creator_by'] = '';
					$this->request->data['OrgSetting']['subdomain'] = strtolower($this->request->data['OrganisationUser']['domain_name']);
					$this->request->data['OrgSetting']['dbname'] = $dbname;
					$this->request->data['OrgSetting']['dbuser'] = $dbuser;
					$this->request->data['OrgSetting']['dbpass'] = $dbpassword;

					$start_date = explode('/', $this->request->data['OrgSetting']['start_date']);
					$end_date = explode('/', $this->request->data['OrgSetting']['end_date']);

					$this->request->data['OrgSetting']['start_date'] = date('Y-m-d',strtotime($start_date[2].'-'.$start_date[1].'-'.$start_date[0]));

					$this->request->data['OrgSetting']['end_date'] = date('Y-m-d',strtotime($end_date[2].'-'.$end_date[1].'-'.$end_date[0]));

					$chatportvalue = $domainLastPort + 1;
					$this->request->data['OrgSetting']['mongo_port'] = $chatportvalue;

					$mmportvalue = $mmportvalueLast + 1;
					$this->request->data['OrgSetting']['mm_port'] = $mmportvalue;
					//=====================================================

					$domainName = strtolower($this->request->data['OrganisationUser']['domain_name']);

					if( $_SERVER['SERVER_ADDR'] ==  OPUSVIEW || $_SERVER['SERVER_ADDR'] ==  OPUSVIEW_DEV ||  $_SERVER['SERVER_ADDR']  == OPUSVIEW_CLOUD )
					{
						//$dbuser = root_dbuser;
						//$dbpassword = root_dbpass;
					}

					if( CHAT_CLOUD == 'yes' ){

						$configPath = $_SERVER['DOCUMENT_ROOT'] . '/cloudChatMM/chat/' . $domainName . 'chat.js';
						$my_file = $_SERVER['DOCUMENT_ROOT'] . '/chatMM/chat_service/peeyush.js';

						fopen($configPath, 'a') or die('Cannot open file:  ' . $configPath);

						if (file_exists($configPath)) {
							$siteurl = 'https://'.$domainName.WEBDOMAIN;
							$configData = file_get_contents($my_file);
							$newtxt = "global.__dbname = '" . $dbname . "';\n";
							$newtxt .= "global.__dbUser = '" . $dbuser . "';\n";
							$newtxt .= "global.__dbPass = '". $dbpassword . "';\n";
							$newtxt .= "global.__dbMongo = '" . $domainName . "';\n";
							$newtxt .= "process.env.PORT = ".$chatportvalue.";\n";
							$newtxt .= "global.__user_images_dir = '".$siteurl."/app/webroot/uploads/user_images/';\n";

							file_put_contents($configPath, $newtxt . "\n" . $configData);

						}

					} else {

						$configPath = $_SERVER['DOCUMENT_ROOT'] . '/chatMM/chat/' . $domainName . 'chat.js';
						$my_file = $_SERVER['DOCUMENT_ROOT'] . '/configappchat.js';

						fopen($configPath, 'a') or die('Cannot open file:  ' . $configPath);

						if (file_exists($configPath)) {

							$configData = file_get_contents($my_file);
							$newtxt =  "global.__dbname = '" . $dbname . "';\n";
							$newtxt .= "global.__dbUser = '" . $dbuser . "';\n";
							$newtxt .= "global.__dbPass = '" . $dbpassword . "';\n";
							$newtxt .= "global.__dbMongo = '" . $domainName . "';\n";
							$newtxt .= "var port = " . $chatportvalue . ";\n";

							file_put_contents($configPath, $newtxt . "\n" . $configData);

						}
					}

					// =============== file create for MM ==================

						if( CHAT_CLOUD == 'yes' ){
							$configPathm = $_SERVER['DOCUMENT_ROOT'] . '/cloudChatMM/mm/' . $domainName . 'mm.js';
						}
						else{
							$configPathm = $_SERVER['DOCUMENT_ROOT'] . '/chatMM/mm/' . $domainName . 'mm.js';
						}
						$my_filem = $_SERVER['DOCUMENT_ROOT'] . '/configappmm.js';

						fopen($configPathm, 'a') or die('Cannot open file:  ' . $configPathm);

						if (file_exists($configPathm)) {

							$configDatam = file_get_contents($my_filem);
							$newtxtm = "global.__dbname = '" . $dbname . "';\n";
							$newtxtm .= "global.__dbUser = '" . $dbuser . "';\n";
							$newtxtm .= "global.__dbPass = '" . $dbpassword . "';\n";
							$newtxtm .= "global.__subdomain = '" . $domainName . "';\n";
							$newtxtm .= "var port = " . $mmportvalue . ";\n";

							file_put_contents($configPathm, $newtxtm . "\n" . $configDatam);

						}

					//======================================================

					if ($this->OrgSetting->saveAssociated($this->request->data)) {
						$insert_id = $this->OrgSetting->getLastInsertId();
						// ======== after add domain email will go to Organisation =====
						$this->_sendOrgEmail($lastUserID, $domainName);
						// =============================================================
						// Will update domain DB Config file  ==============
							$this->create_subdomain_file($insert_id);
							$this->create_subdomain_temp_file($insert_id);
						//==================================================

						$this->Session->setFlash(__('The Domain details have been saved successfully.'), 'success');

						$this->redirect(array('action' => 'list_domain', $lastUserID));
					}

				} else {

					$this->request->data;
					$this->Session->setFlash(__('This domain has already been taken.'), 'error');
				}
			} else {

				$errors = $this->OrgSetting->validationErrors;
				//pr($errors); die;

			}

		}
	}

	/**
	 * admin_view method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function admin_view($id = null) {
		$this->set('title_for_layout', __('View User', true));
		$this->User->id = $id;

		if (!$this->User->exists()) {
			throw new NotFoundException(__('Invalid User'));
		}
		$this->User->recursive = 2;

		//$this->UserPlan->recursive = 2;
		//$this->UserPlan->bindModel(array('belongsTo' => array('Plan', 'PlanType' => array('className' => 'PlanType', 'foreignKey' => 'plan_type'))));

		$this->OrganisationUser->bindModel(array('belongsTo' => array('User')));

		//$userplan = $this->UserPlan->find('all', array('conditions' => array('UserPlan.user_id' => $this->User->id, 'UserPlan.plan_id !=' => 0, 'UserPlan.is_active' => 1)));
		//$this->set('userplan', $userplan);

		$this->request->data = $this->User->read(null, $this->User->id);

		if (isset($this->request->data['User']['password'])) {
			unset($this->request->data['User']['password']);
		}
	}

	public function admin_orgdetails($id = null, $domain_id = null) {
		$this->set('title_for_layout', __('Organization Detail', true));
		$this->layout = false;
		$this->User->id = $id;
		//$this->loadModel('UserPlan');

		if (!$this->User->exists()) {
			throw new NotFoundException(__('Invalid User'));
		}

		//$sizeresult = $this->domain_total_size($id,$domain_id);

		//$orgdetails = $this->OrgSetting->find('first', array('conditions'=>array('OrgSetting.user_id'=> $id)) );
		$orgdetails = $this->OrgSetting->find('first', array('conditions' => array('OrgSetting.id' => $domain_id)));

		$orgFulldetails = $this->Common->userDetail($id);


		if( $_SERVER['SERVER_ADDR'] ==  OPUSVIEW || $_SERVER['SERVER_ADDR'] ==  OPUSVIEW_DEV ||  $_SERVER['SERVER_ADDR']  ==OPUSVIEW_CLOUD )
		{
			$mysqlUserName = root_dbuser;
			$mysqlPassword = root_dbpass;
		} else {
			$mysqlUserName = $orgdetails['OrgSetting']['dbuser'];
			$mysqlPassword = $orgdetails['OrgSetting']['dbpass'];
		}

		$con = mysqli_connect(root_host, $mysqlUserName, $mysqlPassword, $orgdetails['OrgSetting']['dbname']);

		if (mysqli_connect_errno()) {
			echo "Failed to connect to MySQL: " . mysqli_connect_error();
		}

		$userDetails = mysqli_query($con, "SELECT * FROM users WHERE role_id = 2 ");
		$apiusers = mysqli_query($con, "SELECT * FROM api_users ");

		$currentDatabasesizeGB = 0.00;
		$currentDatabasesizeMB = 0.00;
		$lincentotals = 0;
		$apilincentotals = 0;
		if ($con && !empty($userDetails)) {

			if (mysqli_num_rows($userDetails) > 0) {
				$lincentotals = mysqli_num_rows($userDetails);
			}

			if (mysqli_num_rows($apiusers) > 0) {
				$apilincentotals = mysqli_num_rows($apiusers);
			}

			//$dbselect = mysqli_select_db($orgdetails['OrgSetting']['dbname'], $con);
			$result1 = mysqli_query($con, "SHOW TABLE STATUS");
			$dbsize = 0;
			while ($row = mysqli_fetch_array($result1)) {
				$dbsize += $row["Data_length"] + $row["Index_length"];
			}

			// below 2 lines will be remove
			//$currentDatabasesizeGB = number_format(($dbsize / 1024 / 1024 / 1024), 3); // Database size in GB
			//$currentDatabasesizeMB = number_format(($dbsize / 1024 / 1024), 2); // Database size in MB

			// file size ======================================================

			$totalsize = 0;
			$announcements = mysqli_query($con, "SELECT * FROM `announcements`");
			//$announcementscount = mysqli_num_rows($announcements);

			$blogdocuments = mysqli_query($con, "SELECT document_name FROM blog_documents");
			$todolistcomment = mysqli_query($con, "SELECT file_name FROM do_list_comment_uploads ");
			$todolistuploads = mysqli_query($con, "SELECT file_name FROM do_list_uploads ");
			$elementdocuments = mysqli_query($con, "SELECT element_id,file_name FROM element_documents ");
			$elementrelationdocuments = mysqli_query($con, "SELECT file_name FROM element_relation_documents order by file_name asc ");
			$feedbackattachments = mysqli_query($con, "SELECT file_name FROM feedback_attachments ");
			//$todocommentuploads = mysqli_query($con, "SELECT document FROM to_do_comment_uploads ");
			//$todosubcommentuploads = mysqli_query($con, "SELECT document FROM to_do_sub_comment_uploads ");
			//$todosubuploads = mysqli_query($con, "SELECT document FROM to_do_sub_uploads ");
			//$todouploads = mysqli_query($con, "SELECT document FROM to_do_uploads ");
			$userdetails = mysqli_query($con, "SELECT profile_pic, document_pic, menu_pic FROM user_details ");
			$wikipagecommentdocuments = mysqli_query($con, "SELECT document_name FROM wiki_page_comment_documents ");

			$elementrelationdocument_size = 0;
			if (mysqli_num_rows($elementrelationdocuments) > 0) {
				while ($result = mysqli_fetch_array($elementrelationdocuments)) {

					if (file_exists("uploads/template_element_document/" . $result['file_name'])) {
						$elementrelationdocument_size += filesize("uploads/template_element_document/" . $result['file_name']);
					}
				}
			}

			$announcements_size = 0;
			if (!empty($announcements)) {
				while ($result = mysqli_fetch_array($announcements)) {

					if (file_exists("uploads/announcement/" . $result['announce_file'])) {
						$announcements_size += filesize("uploads/announcement/" . $result['announce_file']);
					}
				}
			}

			$blogdocuments_size = 0;
			if (mysqli_num_rows($blogdocuments) > 0) {
				while ($result = mysqli_fetch_array($blogdocuments)) {

					if (file_exists("uploads/blogdocuments/" . $result['document_name'])) {
						$blogdocuments_size += filesize("uploads/blogdocuments/" . $result['document_name']);
					}
				}
			}

			$todolistcomment_size = 0;
			if (mysqli_num_rows($todolistcomment) > 0) {
				while ($result = mysqli_fetch_array($todolistcomment)) {

					if (file_exists("uploads/dolist_comments/" . $result['file_name'])) {
						$todolistcomment_size += filesize("uploads/dolist_comments/" . $result['file_name']);
					}
				}
			}

			$todolistuploads_size = 0;
			if (mysqli_num_rows($todolistuploads) > 0) {
				while ($result = mysqli_fetch_array($todolistuploads)) {

					if (file_exists("uploads/dolist_uploads/" . $result['file_name'])) {
						$todolistuploads_size += filesize("uploads/dolist_uploads/" . $result['file_name']);
					}
				}
			}

			$elementdocuments_size = 0;
			if (mysqli_num_rows($elementdocuments) > 0) {
				while ($result = mysqli_fetch_array($elementdocuments)) {

					if (file_exists("uploads/element_documents/" . $result['element_id'] . "/" . $result['file_name'])) {
						$elementdocuments_size += filesize("uploads/element_documents/" . $result['element_id'] . "/" . $result['file_name']);
					}
				}
			}

			$feedbackattachments_size = 0;
			if (mysqli_num_rows($feedbackattachments) > 0) {
				while ($result = mysqli_fetch_array($feedbackattachments)) {

					if (file_exists("uploads/element_feedback_images/" . $result['file_name'])) {
						$feedbackattachments_size += filesize("uploads/element_feedback_images/" . $result['file_name']);
					}
				}
			}

			$todocommentuploads_size = 0;
			/* if (mysqli_num_rows($todocommentuploads) > 0) {
				while ($result = mysqli_fetch_array($todocommentuploads)) {

					if (file_exists("uploads/dolist_comments/" . $result['document'])) {
						$todocommentuploads_size += filesize("uploads/dolist_comments/" . $result['document']);
					}
				}
			} */

			$todosubcommentuploads_size = 0;
			/* if (mysqli_num_rows($todosubcommentuploads) > 0) {
				while ($result = mysqli_fetch_array($todosubcommentuploads)) {

					if (file_exists("uploads/dolist_comments/" . $result['document'])) {
						$todosubcommentuploads_size += filesize("uploads/dolist_comments/" . $result['document']);
					}
				}
			} */

			$todosubuploads_size = 0;
			/* if (mysqli_num_rows($todosubuploads) > 0) {
				while ($result = mysqli_fetch_array($todosubuploads)) {

					if (file_exists("uploads/dolist_uploads/" . $result['document'])) {
						$todosubuploads_size += filesize("uploads/dolist_uploads/" . $result['document']);
					}
				}
			} */

			$todouploads_size = 0;
			/* if (mysqli_num_rows($todouploads) > 0) {
				while ($result = mysqli_fetch_array($todouploads)) {

					if (file_exists("uploads/dolist_uploads/" . $result['document'])) {
						$todouploads_size += filesize("uploads/dolist_uploads/" . $result['document']);
					}
				}
			} */

			$userprofilepic_size = 0;
			$userdocumentpic_size = 0;
			$usermenupic_size = 0;
			//profile_pic, document_pic, menu_pic
			if (mysqli_num_rows($userdetails) > 0) {
				while ($result = mysqli_fetch_array($userdetails)) {

					if (file_exists("uploads/user_images/" . $result['profile_pic'])) {
						$userprofilepic_size += filesize("uploads/user_images/" . $result['profile_pic']);
					}
					if (file_exists("uploads/user_images/" . $result['document_pic'])) {
						$userdocumentpic_size += filesize("uploads/user_images/" . $result['document_pic']);
					}
					if (file_exists("uploads/user_images/" . $result['menu_pic'])) {
						$usermenupic_size += filesize("uploads/user_images/" . $result['menu_pic']);
					}
				}
			}

			$wikipagecommentdocuments_size = 0;
			if (mysqli_num_rows($wikipagecommentdocuments) > 0) {
				while ($result = mysqli_fetch_array($wikipagecommentdocuments)) {

					if (file_exists("uploads/wiki_page_document/" . $result['document'])) {
						$wikipagecommentdocuments_size += filesize("uploads/wiki_page_document/" . $result['document']);
					}
				}
			}

			$kulsize = $elementrelationdocument_size + $announcements_size + $blogdocuments_size + $todolistcomment_size + $elementdocuments_size + $todolistuploads_size + $feedbackattachments_size + $todocommentuploads_size + $todosubcommentuploads_size + $todosubuploads_size + $todouploads_size + $userprofilepic_size + $userdocumentpic_size + $usermenupic_size + $wikipagecommentdocuments_size;

			$chatsizetotal = $this->chatSize($orgdetails['OrgSetting']['subdomain']);

			$chatsizeTotalGB = ($chatsizetotal / 1024);

			$currentDatabasesizeGB = number_format((($kulsize + $dbsize) / 1024 / 1024 / 1024), 3) + ($chatsizeTotalGB); // Database size in GB
			$currentDatabasesizeMB = number_format((($kulsize + $dbsize) / 1024 / 1024), 2) + ($chatsizetotal); // Database size in MB

			//=================================================================

		}
		mysqli_close($con);

		$this->set('consumedbsizegb', round($currentDatabasesizeGB, 3));
		$this->set('consumedbsizemb', $currentDatabasesizeMB);

		$this->set('userfullname', $orgFulldetails);
		$this->set('licencestotal', $lincentotals);
		$this->set('apilincentotals', $apilincentotals);
		$this->set('orgsettings', $orgdetails);

	}

	public function orgownerdetail($id = null) {

		$this->set('title_for_layout', __('Organization Detail', true));
		App::import("Model", "OrgUserSettingJeera");
		$OrgUserSettingJeera = new OrgUserSettingJeera();

		App::import("Model", "User");
		$_user_detail = new User();
		App::import("Model", "AppUser");
		$_api_user = new AppUser();

		$dn = $_user_detail->find('count', array('conditions' => array('User.id' => $id)));

		if ($dn < 1) {
			throw new NotFoundException(__('Invalid User'));
		}


		if (($this->Session->read('Auth.User.role_id') != 3)  && LOCALIP != $_SERVER['SERVER_ADDR'] ) {

			$this->redirect(array('controller' => 'projects', 'action' => 'lists'));
		}

		$dbsize = 0;
		$currentDatabasesizeGB = 0.00;
		$currentDatabasesizeMB = 0.00;
		$lincentotals = 0;
		$apilincentotals = 0;

		$orgdetails = $this->UserDetail->find('first', array('conditions' => array('UserDetail.user_id' => $this->Session->read('Auth.User.id'))));
		$orgFulldetails = $this->Common->userDetail($id);
		$licencestotal = $_user_detail->find('count', array('conditions' => array('User.role_id' => 2), 'recursive' => -1));

		$apilicencestotal = $_api_user->find('count');

		$whatINeed = explode(WEBDOMAIN, $_SERVER['HTTP_HOST']);
		$whatINeed = $whatINeed[0];
		// data from main jeera database org_setting table

		$organisationDetails = $OrgUserSettingJeera->find('first', array('conditions' => array('subdomain' => $whatINeed)));

		$con = mysqli_connect(root_host, dbuser, dbpass, dbname);

		if (mysqli_connect_errno()) {
			echo "Failed to connect to MySQL: " . mysqli_connect_error();
		}

		/* $this->conn = mysql_pconnect('localhost', dbuser, dbpass);
		$dbselect = mysql_select_db(dbname); */
		$result1 = mysqli_query($con, "SHOW TABLE STATUS");

		while ($row = mysqli_fetch_array($result1)) {
			$dbsize += $row["Data_length"] + $row["Index_length"];
		}

		//$currentDatabasesizeGB = number_format(($dbsize / 1024 / 1024 / 1024), 3); // Database size in GB
		//$currentDatabasesizeMB = number_format(($dbsize / 1024 / 1024), 2); // Database size in MB

		//if( $_SERVER['SERVER_NAME'] == 'dotsquares.ideascast.com' ) {
		// file size ======================================================

		$totalsize = 0;
		$announcements = mysqli_query($con, "SELECT * FROM `announcements`");
		//$announcementscount = mysqli_num_rows($announcements);

		$blogdocuments = mysqli_query($con, "SELECT document_name FROM blog_documents");
		$todolistcomment = mysqli_query($con, "SELECT file_name FROM do_list_comment_uploads ");
		$todolistuploads = mysqli_query($con, "SELECT file_name FROM do_list_uploads ");
		$elementdocuments = mysqli_query($con, "SELECT element_id,file_name FROM element_documents ");
		$elementrelationdocuments = mysqli_query($con, "SELECT file_name FROM element_relation_documents order by file_name asc ");
		$feedbackattachments = mysqli_query($con, "SELECT file_name FROM feedback_attachments ");
		//$todocommentuploads = mysqli_query($con, "SELECT document FROM to_do_comment_uploads ");
		//$todosubcommentuploads = mysqli_query($con, "SELECT document FROM to_do_sub_comment_uploads ");
		//$todosubuploads = mysqli_query($con, "SELECT document FROM to_do_sub_uploads ");
		//$todouploads = mysqli_query($con, "SELECT document FROM to_do_uploads ");
		$userdetails = mysqli_query($con, "SELECT profile_pic, document_pic, menu_pic FROM user_details ");
		$wikipagecommentdocuments = mysqli_query($con, "SELECT document_name FROM wiki_page_comment_documents ");

		$elementrelationdocument_size = 0;
		if (mysqli_num_rows($elementrelationdocuments) > 0) {
			while ($result = mysqli_fetch_array($elementrelationdocuments)) {

				if (file_exists("uploads/template_element_document/" . $result['file_name'])) {
					$elementrelationdocument_size += filesize("uploads/template_element_document/" . $result['file_name']);
				}
			}
		}

		$announcements_size = 0;
		if (!empty($announcements)) {
			while ($result = mysqli_fetch_array($announcements)) {

				if (file_exists("uploads/announcement/" . $result['announce_file'])) {
					$announcements_size += filesize("uploads/announcement/" . $result['announce_file']);
				}
			}
		}

		$blogdocuments_size = 0;
		if (mysqli_num_rows($blogdocuments) > 0) {
			while ($result = mysqli_fetch_array($blogdocuments)) {

				if (file_exists("uploads/blogdocuments/" . $result['document_name'])) {
					$blogdocuments_size += filesize("uploads/blogdocuments/" . $result['document_name']);
				}
			}
		}

		$todolistcomment_size = 0;
		if (mysqli_num_rows($todolistcomment) > 0) {
			while ($result = mysqli_fetch_array($todolistcomment)) {

				if (file_exists("uploads/dolist_comments/" . $result['file_name'])) {
					$todolistcomment_size += filesize("uploads/dolist_comments/" . $result['file_name']);
				}
			}
		}

		$todolistuploads_size = 0;
		if (mysqli_num_rows($todolistuploads) > 0) {
			while ($result = mysqli_fetch_array($todolistuploads)) {

				if (file_exists("uploads/dolist_uploads/" . $result['file_name'])) {
					$todolistuploads_size += filesize("uploads/dolist_uploads/" . $result['file_name']);
				}
			}
		}

		$elementdocuments_size = 0;
		if (mysqli_num_rows($elementdocuments) > 0) {
			while ($result = mysqli_fetch_array($elementdocuments)) {

				if (file_exists("uploads/element_documents/" . $result['element_id'] . "/" . $result['file_name'])) {
					$elementdocuments_size += filesize("uploads/element_documents/" . $result['element_id'] . "/" . $result['file_name']);
				}
			}
		}

		$feedbackattachments_size = 0;
		if (mysqli_num_rows($feedbackattachments) > 0) {
			while ($result = mysqli_fetch_array($feedbackattachments)) {

				if (file_exists("uploads/element_feedback_images/" . $result['file_name'])) {
					$feedbackattachments_size += filesize("uploads/element_feedback_images/" . $result['file_name']);
				}
			}
		}

		$todocommentuploads_size = 0;
		/* if (mysqli_num_rows($todocommentuploads) > 0) {
			while ($result = mysqli_fetch_array($todocommentuploads)) {

				if (file_exists("uploads/dolist_comments/" . $result['document'])) {
					$todocommentuploads_size += filesize("uploads/dolist_comments/" . $result['document']);
				}
			}
		} */

		$todosubcommentuploads_size = 0;
		/* if (mysqli_num_rows($todosubcommentuploads) > 0) {
			while ($result = mysqli_fetch_array($todosubcommentuploads)) {

				if (file_exists("uploads/dolist_comments/" . $result['document'])) {
					$todosubcommentuploads_size += filesize("uploads/dolist_comments/" . $result['document']);
				}
			}
		} */

		$todosubuploads_size = 0;
		/* if (mysqli_num_rows($todosubuploads) > 0) {
			while ($result = mysqli_fetch_array($todosubuploads)) {

				if (file_exists("uploads/dolist_uploads/" . $result['document'])) {
					$todosubuploads_size += filesize("uploads/dolist_uploads/" . $result['document']);
				}
			}
		} */

		$todouploads_size = 0;
		/* if (mysqli_num_rows($todouploads) > 0) {
			while ($result = mysqli_fetch_array($todouploads)) {

				if (file_exists("uploads/dolist_uploads/" . $result['document'])) {
					$todouploads_size += filesize("uploads/dolist_uploads/" . $result['document']);
				}
			}
		} */

		$userprofilepic_size = 0;
		$userdocumentpic_size = 0;
		$usermenupic_size = 0;
		//profile_pic, document_pic, menu_pic
		if (mysqli_num_rows($userdetails) > 0) {
			while ($result = mysqli_fetch_array($userdetails)) {

				if (file_exists("uploads/user_images/" . $result['profile_pic'])) {
					$userprofilepic_size += filesize("uploads/user_images/" . $result['profile_pic']);
				}
				if (file_exists("uploads/user_images/" . $result['document_pic'])) {
					$userdocumentpic_size += filesize("uploads/user_images/" . $result['document_pic']);
				}
				if (file_exists("uploads/user_images/" . $result['menu_pic'])) {
					$usermenupic_size += filesize("uploads/user_images/" . $result['menu_pic']);
				}
			}
		}

		$wikipagecommentdocuments_size = 0;
		if (mysqli_num_rows($wikipagecommentdocuments) > 0) {
			while ($result = mysqli_fetch_array($wikipagecommentdocuments)) {
				if(isset($result['document'])){
				if (file_exists("uploads/wiki_page_document/" . $result['document'])) {
					$wikipagecommentdocuments_size += filesize("uploads/wiki_page_document/" . $result['document']);
				}
				}
			}
		}

		$kulsize = $elementrelationdocument_size + $announcements_size + $blogdocuments_size + $todolistcomment_size + $elementdocuments_size + $todolistuploads_size + $feedbackattachments_size + $todocommentuploads_size + $todosubcommentuploads_size + $todosubuploads_size + $todouploads_size + $userprofilepic_size + $userdocumentpic_size + $usermenupic_size + $wikipagecommentdocuments_size;

		$chatsizetotal = $this->chatSize($whatINeed);
		$chatsizeTotalGB = ($chatsizetotal / 1024);

		$currentDatabasesizeGB = number_format((($kulsize + $dbsize) / 1024 / 1024 / 1024), 3) + (round($chatsizeTotalGB, 3)); // Database size in GB
		$currentDatabasesizeMB = number_format((($kulsize + $dbsize) / 1024 / 1024), 2) + ($chatsizetotal); // Database size in MB

		//=================================================================

		//}

		$this->set('consumedbsizegb', $currentDatabasesizeGB);
		$this->set('consumedbsizemb', $currentDatabasesizeMB);
		//mysql_close();

		$this->set('userfullname', $orgFulldetails);
		$this->set('organisationDetails', $organisationDetails);
		$this->set('orgsettings', $orgdetails);

		if (isset($licencestotal) && $licencestotal > 0) {
			$this->set('licencestotal', $licencestotal);
		} else {
			$this->set('licencestotal', 0);
		}

		if (isset($apilicencestotal) && $apilicencestotal > 0) {
			$this->set('apilicencestotal', $apilicencestotal);
		} else {
			$this->set('apilicencestotal', 0);
		}

	}

	public function admin_edit($id = null) {
		$this->set('title_for_layout', __('Edit Organization', true));
		$this->User->id = $id;
		if (!$this->User->exists()) {
			$this->Session->setFlash(__('Invalid User.'), 'error');
			$this->redirect(array('action' => 'index'));
		}

		//pr($this->request->data);die;

		if ($this->request->is('post') || $this->request->is('put')) {

			if ($this->User->saveAssociated($this->request->data)) {

				$this->Session->setFlash(__('The Organization details have been updated successfully.'), 'success');
				$this->redirect(array('action' => 'index'));

			}

		} else {
			//$this->User->recursive = 2;

			//$this->User->bindModel(array('hasOne' => array('OrgSetting')));
			$this->request->data = $this->User->read(null, $id);

			if (isset($this->request->data['User']['password'])) {
				unset($this->request->data['User']['password']);
			}

		}

		//==========================================================================

		/* $orgdetails = $this->OrgSetting->find('first', array('conditions'=>array('OrgSetting.user_id'=> $id)) );

			$this->conn = mysql_pconnect('localhost', $orgdetails['OrgSetting']['dbuser'], $orgdetails['OrgSetting']['dbpass']);
			mysql_select_db($orgdetails['OrgSetting']['dbname']);
			$userDetails = mysql_query("SELECT * FROM users WHERE role_id = 2 ");
			$useslincense = 0;
			if( @mysql_num_rows($userDetails) > 0 ){
				$useslincense = mysql_num_rows($userDetails);
			}

			if( isset($orgdetails) && count($orgdetails) > 0 ){

				if( isset($this->request->data['OrgSetting']) && ($this->request->data['OrgSetting']['allowed_space'] < $orgdetails['OrgSetting']['allowed_space'] && $useslincense < $orgdetails['OrgSetting']['license']) ){

					$this->User->bindModel(array('hasOne' => array('OrgSetting')));
					$this->request->data = $this->User->read(null, $id);

					if (isset($this->request->data['User']['password'])) {
						unset($this->request->data['User']['password']);
					}

					$this->Session->setFlash(__('Licence and database should not be lesser than existing values'), 'error');
					$this->redirect(array('action' => 'edit/'.$id));

				} else if( isset($this->request->data['OrgSetting']) && $this->request->data['OrgSetting']['allowed_space'] < $orgdetails['OrgSetting']['allowed_space'] &&  $useslincense < $orgdetails['OrgSetting']['license'] ){

					$this->User->bindModel(array('hasOne' => array('OrgSetting')));
					$this->request->data = $this->User->read(null, $id);

					if (isset($this->request->data['User']['password'])) {
						unset($this->request->data['User']['password']);
					}

					$this->Session->setFlash(__('Database limit should not be lesser than existing usage'), 'error');
					$this->redirect(array('action' => 'edit/'.$id));

				} else if( isset($this->request->data['OrgSetting']) && $useslincense < $orgdetails['OrgSetting']['license'] ){

					$this->User->bindModel(array('hasOne' => array('OrgSetting')));
					$this->request->data = $this->User->read(null, $id);

					if (isset($this->request->data['User']['password'])) {
						unset($this->request->data['User']['password']);
					}

					$this->Session->setFlash(__('Licence should not be lesser than existing number of users'), 'error');
					$this->redirect(array('action' => 'edit/'.$id));

				} else { */

		//==========================================================================
		//pr($this->request->data); die;
		/* if ($this->User->saveAssociated($this->request->data)) {

								$this->request->data['OrgSetting']['start_date'] = date('Y-m-d', strtotime($this->request->data['OrgSetting']['start_date']));
								$this->request->data['OrgSetting']['end_date'] = date('Y-m-d', strtotime($this->request->data['OrgSetting']['end_date']));

								$this->OrgSetting->save($this->request->data);

							$this->Session->setFlash(__('The Organization details have been updated successfully.'), 'success');
							$this->redirect(array('action' => 'index'));
						}*/

		/* }
			}	*/

		/* } else { 	//$this->User->recursive = 2;

						$this->User->bindModel(array('hasOne' => array('OrgSetting')));
			            $this->request->data = $this->User->read(null, $id);

			            if (isset($this->request->data['User']['password'])) {
			                unset($this->request->data['User']['password']);
			            }

		*/
		//pr($this->request->data);die;
	}

	public function admin_deleteAuth($id = null) {

		if (isset($this->data['id']) && !empty($this->data['id'])) {
			$id = $this->data['id'];
			$this->User->id = $id;
			if (!$this->User->exists()) {
				throw new NotFoundException(__('Invalid Organizations'), 'error');
			}

			//$resutl = $this->OrganisationUser->find('all', array('conditions'=>['OrganisationUser.user_id'=>$id]));

			$resutl = $this->OrgSetting->find('all', array('conditions' => ['OrgSetting.user_id' => $id]));

			//if( isset( $resutl ) && count($resutl) > 0 ){
			if (isset($resutl)) {
				$msg = false;
				foreach ($resutl as $domainlist) {

					//$domain = $domainlist['OrgSetting']['subdomain'];
					$domain = $domainlist['OrgSetting']['subdomain'];
					$domainName = $domainlist['OrgSetting']['subdomain'];
					$databasename = $domainlist['OrgSetting']['dbname'];
					$dbusername = $domainlist['OrgSetting']['dbuser'];
					$dbpassword = $domainlist['OrgSetting']['dbpass'];

					$this->domain_document_unlinks($domainName,$databasename,$dbusername,$dbpassword);

					$msg = $this->Common->deletesubdomain($domain, $databasename, $dbusername);


					if( file_exists( $_SERVER['DOCUMENT_ROOT'].'/app/domain_dbs/'.$domainName.'.php' ) ){
						unlink($_SERVER['DOCUMENT_ROOT'].'/app/domain_dbs/'.$domainName.'.php');
					}
					/* if( file_exists( $_SERVER['DOCUMENT_ROOT'] . '/chatMM/chat/' . $domainName . 'chat.js' ) ){
						unlink($_SERVER['DOCUMENT_ROOT'] . '/chatMM/chat/' . $domainName . 'chat.js');
					}
					if( file_exists( $_SERVER['DOCUMENT_ROOT'] . '/chatMM/mm/' . $domainName . 'mm.js' ) ){
						unlink($_SERVER['DOCUMENT_ROOT'] . '/chatMM/mm/' . $domainName . 'mm.js');
					} */
					if( file_exists( $_SERVER['DOCUMENT_ROOT'] . '/cloudChatMM/chat/' . $domainName . 'chat.js' ) ){
						unlink($_SERVER['DOCUMENT_ROOT'] . '/cloudChatMM/chat/' . $domainName . 'chat.js');
					}
					if( file_exists( $_SERVER['DOCUMENT_ROOT'] . '/cloudChatMM/mm/' . $domainName . 'mm.js' ) ){
						unlink($_SERVER['DOCUMENT_ROOT'] . '/cloudChatMM/mm/' . $domainName . 'mm.js');
					}

					if ($this->live_setting == true && $msg == true) {

						if( PHP_VERSIONS == 5 ){
							$mongo = new MongoClient(MONGO_CONNECT);
							$db = $mongo->$domain;
							$response = $db->drop();
						} else {
							// domain database delete from mongo
							$mongo = new MongoDB\Driver\Manager(MONGO_CONNECT);
							$listcollections = new MongoDB\Driver\Command(["listCollections" => 1]);
							$result = $mongo->executeCommand($domain, $listcollections);
							//if( $result->toArray() !== null ){
								$collections = $result->toArray();
								foreach ($collections as $collection) {
									if(isset($collection) && !empty(($collection)) ){
										$mongo->executeCommand($domain, new \MongoDB\Driver\Command(["drop" => $collection->name]));
									}
								}
							//}
						}

					}

				}
				$resutl_tot = ( isset($resutl) && !empty($resutl) ) ? count($resutl) : 0;
				if ($resutl_tot == 0 && $msg == false) {
					$msg = true;
				}

				if ($msg == true) {

					if ($this->User->delete()) {

						$this->UserDetail->deleteAll(array('UserDetail.user_id' => $id));
						$this->OrgSetting->deleteAll(array('OrgSetting.user_id' => $id));
						$this->OrganisationUser->deleteAll(array('OrganisationUser.user_id' => $id));
						$this->ManageDomain->deleteAll(array('ManageDomain.org_id' => $id));

						$this->Session->setFlash(__('Organization has been deleted successfully.'), 'success');
						die('success');

					} else {
						$this->Session->setFlash(__('Organization could not deleted successfully.'), 'error');
					}
				}

			} else {
				$this->Session->setFlash(__('Organization could not deleted successfully.'), 'error');
			}

		}
		die('error');
	}

	public function admin_deleteAuth_domain($id = null, $user_id = null, $org_id = null) {

		if (isset($this->data['id']) && !empty($this->data['id'])) {
			$id = $this->data['id'];
			$org_id = $this->data['org_id'];
			$user_id = $this->data['user_id'];

			$this->OrganisationUser->id = $id;
			if (!$this->OrganisationUser->exists()) {
				throw new NotFoundException(__('Invalid Organizations'), 'error');
			}

			//pr($this->data); die;

			/* $resutl = $this->OrganisationUser->find('first', array('conditions'=>['OrganisationUser.id'=>$id]));
			$domain = $resutl['OrganisationUser']['domain_name']; */

			$resutOrg = $this->OrgSetting->find('first', array('conditions' => ['OrgSetting.id' => $org_id]));
			$domain = $resutOrg['OrgSetting']['subdomain'];
			$domainName = $resutOrg['OrgSetting']['subdomain'];
			$databasename = $resutOrg['OrgSetting']['dbname'];
			$dbusername = $resutOrg['OrgSetting']['dbuser'];
			$dbpassword = $resutOrg['OrgSetting']['dbpass'];

			$this->domain_document_unlinks($domainName,$databasename,$dbusername,$dbpassword);

			$msg = $this->Common->deletesubdomainlive($domain, $databasename, $dbusername);

			if( file_exists( $_SERVER['DOCUMENT_ROOT'].'/app/domain_dbs/'.$domainName.'.php' ) ){
				unlink($_SERVER['DOCUMENT_ROOT'].'/app/domain_dbs/'.$domainName.'.php');
			}
/* 			if( file_exists( $_SERVER['DOCUMENT_ROOT'] . '/chatMM/chat/' . $domainName . 'chat.js' ) ){
				unlink($_SERVER['DOCUMENT_ROOT'] . '/chatMM/chat/' . $domainName . 'chat.js');
			}
			if( file_exists( $_SERVER['DOCUMENT_ROOT'] . '/chatMM/mm/' . $domainName . 'mm.js' ) ){
				unlink($_SERVER['DOCUMENT_ROOT'] . '/chatMM/mm/' . $domainName . 'mm.js');
			} */
			if( file_exists( $_SERVER['DOCUMENT_ROOT'] . '/cloudChatMM/chat/' . $domainName . 'chat.js' ) ){
				unlink($_SERVER['DOCUMENT_ROOT'] . '/cloudChatMM/chat/' . $domainName . 'chat.js');
			}
			if( file_exists( $_SERVER['DOCUMENT_ROOT'] . '/cloudChatMM/mm/' . $domainName . 'mm.js' ) ){
				unlink($_SERVER['DOCUMENT_ROOT'] . '/cloudChatMM/mm/' . $domainName . 'mm.js');
			}


			if ($msg == true) {
				if ($this->OrganisationUser->delete()) {

					if ($this->live_setting == true) {
						if( PHP_VERSIONS == 5 ){
							$mongo = new MongoClient(MONGO_CONNECT);
							$db = $mongo->$domain;
							$response = $db->drop();
						} else {
							// domain database delete from mongo
							$mongo = new MongoDB\Driver\Manager(MONGO_CONNECT);
							$listcollections = new MongoDB\Driver\Command(["listCollections" => 1]);
							$result = $mongo->executeCommand($domain, $listcollections);
							//if( $result->toArray() !== null ){
								$collections = $result->toArray();
								foreach ($collections as $collection) {
									if(isset($collection) && !empty(($collection)) ){
										$mongo->executeCommand($domain, new \MongoDB\Driver\Command(["drop" => $collection->name]));
									}
								}
							//}
						}
					}

					$this->OrgSetting->deleteAll(array('OrgSetting.id' => $org_id));
					$this->OrganisationUser->deleteAll(array('OrganisationUser.id' => $id));
					$this->ManageDomain->deleteAll(array('ManageDomain.org_id' => $id));

					$this->Session->setFlash(__('Organization has been deleted successfully.'), 'success');
					die('success');

				} else {
					$this->Session->setFlash(__('Organization could not deleted successfully.'), 'error');
				}
			}
		}
		die('error');
	}

	public function admin_delete_org_user() {

		if (isset($this->data['id']) && !empty($this->data['id'])) {
			$id = $this->data['id'];

			$this->OrganisationUser->id = $id;
			if (!$this->OrganisationUser->exists()) {
				throw new NotFoundException(__('Invalid Organization User'), 'error');
			}

			if ($this->OrganisationUser->delete()) {
				$this->Session->setFlash(__('Organization User has been deleted successfully.'), 'success');
				die('success');
			} else {
				$this->Session->setFlash(__('Organization User could not deleted successfully.'), 'error');
			}
		}
		die('error');

	}

	public function admin_domain_settings() {

		$orConditions = array();
		$andConditions = array('User.role_id' => array(3));
		$finalConditions = array();

		$in = 0;
		$per_page_show = $this->Session->read('user.per_page_show');
		if (empty($per_page_show)) {
			$per_page_show = ADMIN_PAGING;
		}

		if (isset($this->data['User']['keyword'])) {
			$keyword = trim($this->data['User']['keyword']);
		} else {
			$keyword = $this->Session->read('user.keyword');
		}

		if (isset($keyword)) {
			$this->Session->write('user.keyword', $keyword);
			$keywords = explode(" ", $keyword);
			if (isset($keywords[0]) && !empty($keywords[0]) && count($keywords) < 2) {
				$keyword = $keywords[0];
				$in = 1;
				$orConditions = array('OR' => array(
					'User.email LIKE' => '%' . $keyword . '%',
					'UserDetail.first_name LIKE' => '%' . $keyword . '%',
					'UserDetail.last_name LIKE' => '%' . $keyword . '%',
				));
			} else if (!empty($keywords) && count($keywords) > 1) {
				$first_name = $keywords[0];
				$last_name = $keywords[1];
				$in = 1;
				$andConditions = array('AND' => array(
					'UserDetail.first_name LIKE' => '%' . $first_name . '%',
					'UserDetail.last_name LIKE' => '%' . $last_name . '%',
				));
			}
		}

		if (isset($this->data['User']['status'])) {
			$status = $this->data['User']['status'];
		} else {
			$status = $this->Session->read('user.status');
		}

		if (isset($status)) {
			$this->Session->write('user.status', $status);
			if ($status != '') {
				$in = 1;
				$andConditions = array_merge($andConditions, array('User.status' => $status));
			}
		}

		if (isset($this->data['UserDetail']['country_id']) && !empty($this->data['UserDetail']['country_id'])) {
			$county = trim($this->data['UserDetail']['country_id']);
			$this->Session->write('user.country', $county);
			$in = 1;
			$andConditions1 = array(
				'UserDetail.country_id LIKE' => '%' . $county . '%',
			);
			$andConditions = array_merge($andConditions, $andConditions1);
		}

		if (isset($this->data['User']['per_page_show']) && !empty($this->data['User']['per_page_show'])) {
			$per_page_show = $this->data['User']['per_page_show'];
		}

		if (!empty($orConditions)) {
			$finalConditions = array_merge($finalConditions, $orConditions);
		}
		if (!empty($andConditions)) {
			$finalConditions = array_merge($finalConditions, array('AND' => $andConditions));
		}

		//pr($finalConditions); die;
		$count = $this->User->find('count', array('conditions' => $finalConditions));
		$this->set('count', $count);

		$this->set('title_for_layout', __('Organizations | Domain Settings', true));
		$this->Session->write('user.per_page_show', $per_page_show);
		$this->User->recursive = 0;
		$this->paginate = array('conditions' => $finalConditions, "limit" => $per_page_show, "order" => "User.created DESC");
		$this->set('users', $this->paginate('User'));
		$this->set('in', $in);
	}

	public function domain_settings() {

		$this->loadModel("ManageDomain");

		$this->layout = 'inner';
		$this->set('title_for_layout', __('Manage Email Domains', true));

		$viewData['page_heading'] = 'Manage Email Domains';
		$viewData['page_subheading'] = 'Create and manage user access';

		$in = 0;
		$per_page_show = $this->Session->read('user.per_page_show');
		if (empty($per_page_show)) {
			$per_page_show = ADMIN_PAGING;
			// $per_page_show = 1;
		}

		if ($this->Session->read('Auth.User.role_id') == 3) {
			$connect_id = $this->Session->read('Auth.User.id');
		} else if ($this->Session->read('Auth.User.UserDetail.administrator') == 1 && $this->Session->read('Auth.User.UserDetail.org_id') != 0) {
			$connect_id = $this->Session->read('Auth.User.UserDetail.org_id');
		}

		if (($this->Session->read('Auth.User.role_id') != 3) && LOCALIP != $_SERVER['SERVER_ADDR'] ) {

			$this->redirect(array('controller' => 'projects', 'action' => 'lists'));
		}

		// $listdomain = $this->ManageDomain->find('all', array('conditions'=>array('ManageDomain.org_id'=>$connect_id),'order' => 'ManageDomain.domain_name ASC'));
		//$this->set(compact('listdomain','viewData'));

		$this->paginate = array('conditions' => array('ManageDomain.org_id' => $connect_id), 'order' => 'ManageDomain.created DESC', "limit" => $per_page_show);

		$count = $this->ManageDomain->find('count', array('conditions' => array('ManageDomain.org_id' => $connect_id)));

		if (isset($count) && $count > 0) {
			$pageCount = intval(ceil($count / $per_page_show));
			if (isset($this->request->params['named']['page']) && $this->request->params['named']['page'] > $pageCount) {
				return $this->redirect(array('controller' => 'organisations', 'action' => 'domain_settings', 'admin' => false));
			}
		}

		$viewData['crumb'] = [
			'last' => [
				'data' => [
					'title' => "Manage Email Domains",
					'data-original-title' => "Manage Email Domains",
				],
			],
		];
		$this->set('crumb',$viewData['crumb']);

		$this->set('count', $count);
		$this->set('listdomain', $this->paginate('ManageDomain'));
		$this->set('in', $in);
		$this->set('viewData', $viewData);

	}

	public function manage_domain($id = null) {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			//
			$response = ['success' => false, 'content' => null];

			$domaintype = $this->Domain->find('list', array('conditions' => array('Domain.status' => 1),'group'=>'Domain.title', 'order' => 'Domain.title ASC'));
			$this->set(compact('domaintype'));

			if (($this->request->is('post') || $this->request->is('put'))) {
				// pr($this->request->data, 1);
				// $this->autoRender = false;
				$frmactionType = $this->request->data['ManageDomain']['actionType'];
				//echo $frmactionType; die;

				unset($this->request->data['ManageDomain']['actionType']);

				if ($this->Session->read('Auth.User.role_id') == 3) {
					$this->request->data['ManageDomain']['org_id'] = $this->Session->read('Auth.User.id');
				} else {
					$this->request->data['ManageDomain']['org_id'] = $this->Session->read('Auth.User.UserDetail.org_id');
				}

				if (isset($this->request->data['ManageDomain']['id']) && !empty($this->request->data['ManageDomain']['id'])) {
					//$this->request->data['ManageDomain']['user_id']= $this->Session->read('Auth.User.id');
				} else {
					$this->request->data['ManageDomain']['user_id'] = $this->Session->read('Auth.User.id');
				}

				$this->request->data['ManageDomain']['updated_by'] = $this->Session->read('Auth.User.id');

				$this->ManageDomain->set($this->request->data);

				//$this->request->data['ManageDomain']['create_account'] = 1;
				$this->request->data['ManageDomain']['status'] = 1;

				if (!empty($this->request->data['ManageDomain']['id'])) {

					if ($this->request->data['ManageDomain']['domain_name'] == '') {

						$response['success'] = 'false';
						$response['content'] = array('domain' => 'Domain name is required');
						echo json_encode($response);
						exit();

					} else {

						if (isset($this->request->data['ManageDomain']['domain_id']) && !empty($this->request->data['ManageDomain']['domain_id'])) {

							$domaintypename = $this->Domain->find('first', array('conditions' => array('Domain.id' => $this->request->data['ManageDomain']['domain_id'])));

							$this->request->data['ManageDomain']['domain_name'] = strtolower($this->request->data['ManageDomain']['domain_name'] . '.' . $domaintypename['Domain']['title']);

							if ($this->ManageDomain->save($this->request->data)) {

								$this->Session->setFlash(__('The email domain has been updated successfully.'), 'success');
								$response['success'] = true;
								echo json_encode($response);
								exit();
							} else {

								$response['success'] = false;
								$response['content'] = array('domain' => 'This domain has already been taken');
								echo json_encode($response);
								exit();

							}

						} else {
							$response['success'] = false;
							$response['content'] = array('type' => 'Please select your domain type');
							echo json_encode($response);
							exit();
						}
					}

				} else {

					if (!isset($this->request->data['ManageDomain']['domain_name']) || empty($this->request->data['ManageDomain']['domain_name'])) {

						$response['success'] = false;
						$response['content'] = array('domain' => 'Domain name is required');

					} else if (!isset($this->request->data['ManageDomain']['domain_id']) || empty($this->request->data['ManageDomain']['domain_id'])) {

						$response['success'] = false;
						$response['content']['type'] = 'Please select your domain type';

					} else {

						$domaintypename = $this->Domain->find('first', array('conditions' => array('Domain.id' => $this->request->data['ManageDomain']['domain_id'])));

						$this->request->data['ManageDomain']['domain_name'] = strtolower($this->request->data['ManageDomain']['domain_name'] . '.' . $domaintypename['Domain']['title']);

						$chkdomain = $this->ManageDomain->find('count', array('conditions' => array('ManageDomain.domain_name' => $this->request->data['ManageDomain']['domain_name'])));

						if ($chkdomain == 0) {
							if (!empty($this->request->data)) {

								if ($frmactionType != 'editAction') {
									// echo "add action";
									$this->ManageDomain->save($this->request->data);
									$lastid = $this->ManageDomain->getLastInsertId();
									$this->Session->setFlash(__('The email domain has been added successfully.'), 'success');
									$response['success'] = true;

								} else {
									// echo "edit action";
									$this->ManageDomain->save($this->request->data);
									$this->Session->setFlash(__('The email domain has been updated successfully.'), 'success');
									$response['success'] = true;

								}
							}

						} else {

							$this->ManageDomain->validationErrors['domain_name'] = "This domain has already been taken";
							$response['success'] = false;
							//$response['content'] = 'This domain has already been taken';
							$response['content'] = array('domain' => 'This domain has already been taken');
						}
					}
					echo json_encode($response);
					exit();
				}

			} else {
				$this->request->data = $this->ManageDomain->read(null, $id);
			}
		}

	}

	function domain_delete() {

		if ($this->request->is('ajax')) {

			// $this->autoRender = false;
			$this->loadModel('ManageDomain');

			if (isset($this->request->data['domain_id'])) {

				$condition = array('ManageDomain.id' => $this->request->data['domain_id']);
				$domainName = $this->ManageDomain->find('all',array('conditions'=>array('ManageDomain.id'=>$this->request->data['domain_id'])));

				if( isset($domainName) && !empty($domainName) ){

					foreach($domainName as $listEmailDomain){

						$response = $this->checkEmailDomainUser($listEmailDomain['ManageDomain']['domain_name']);

						if( $response == false ){

							if ($this->ManageDomain->delete($listEmailDomain['ManageDomain']['id'])) {

								$conditionUser = array('User.managedomain_id' => $listEmailDomain['ManageDomain']['id']);
								$userid = $this->User->find('list', array(
									'conditions' => $conditionUser,
									'fields' => array('id'),
									)
								);

								// =========== Start Delete Chat Users ===================
								if ($this->live_setting == true) {

									if (PHP_VERSIONS == 5) {
										$mongo = new MongoClient(MONGO_CONNECT);
										$this->mongoDB = $mongo->selectDB(MONGO_DATABASE);
										$mongo_collection = new MongoCollection($this->mongoDB, 'users');
										$mongo_collection->remove(array('_id' => $userid), true);
									} else {
										$mongo = new MongoDB\Driver\Manager(MONGO_CONNECT);
										$bulk = new MongoDB\Driver\BulkWrite;
										//$qry = array("id" => $id);
										$ret = $bulk->delete(array('id' => (int) $userid));
										$mongo->executeBulkWrite(MONGO_DATABASE.'.users', $bulk);
									}

								}
								// =========== End Delete Chat Users ===================

								//$orgUser->delete($userid);

								$this->Session->setFlash(__('Email domain has been deleted successfully.'), 'success');
								die('success');

							} else {
								$this->Session->setFlash(__('Email domain could not deleted successfully.'), 'error');
								die('error');
							}

						} else {

							$this->Session->setFlash(__('Domain couldn\'t be delete.'), 'error');
							die('error');

						}
					}
					die;
				}

			}

		}
	}

	function domain_updatestatus() {

		if ($this->request->is('ajax')) {
			$this->autoRender = false;
			$this->loadModel('ManageDomain');

			$this->request->data['ManageDomain'] = $this->request->data;
			$this->request->data['ManageDomain']['updated_by'] = $this->Session->read('Auth.User.id');
			if ($this->ManageDomain->save($this->request->data, false)) {
				$this->ManageDomain->updateAll(array('ManageDomain.status' => $this->request->data['ManageDomain']['status']), array('ManageDomain.id' => $this->request->data['ManageDomain']['id']));
				$this->Session->setFlash(__('Domain status has been changed successfully.'), 'success');
				die('success');
			} else {
				$this->Session->setFlash(__('Domain status could not updated successfully.'), 'error');
			}
		}

	}

	function domain_createaccount() {

		if ($this->request->is('ajax')) {
			$this->autoRender = false;
			$this->loadModel('ManageDomain');

			if (!isset($this->request->data['id']) || empty($this->request->data['id'])) {
				$this->Session->setFlash(__('Domain create account could not updated successfully.'), 'error');
				die('error');
			} else {

				$this->request->data['ManageDomain']['id'] = $this->request->data['id'];
				$this->request->data['ManageDomain']['create_account'] = $this->request->data['status'];

				$this->request->data['ManageDomain']['updated_by'] = $this->Session->read('Auth.User.id');

				if ($this->ManageDomain->save($this->request->data, false)) {

					//$this->Session->setFlash(__('Domain create account has been changed successfully.'), 'success');
					$this->Session->setFlash(__('Domain status has been changed successfully.'), 'success');
					die('success');

				} else {
					//$this->Session->setFlash(__('Domain create account could not updated successfully.'), 'error');
					$this->Session->setFlash(__('Domain status could not updated successfully.'), 'error');
				}
			}
		}

	}

	function manage_users($domain_id = null) {

		$this->layout = 'inner';
		$this->set('title_for_layout', __('Manage Users', true));
		$viewData['page_heading'] = 'Manage Users';
		$viewData['page_subheading'] = 'Create and manage users';


		if (($this->Session->read('Auth.User.role_id') == 2 && $this->Session->read('Auth.User.UserDetail.administrator') != 1)  && LOCALIP != $_SERVER['SERVER_ADDR'] ) {

						$this->redirect(array('controller' => 'projects', 'action' => 'lists'));
		}

		/*
			$orConditions = array();
	        $andConditions = array();
	        $finalConditions = array();

			$in = $count = 0;
			$per_page_show = 50;//$this->Session->read('user.per_page_show');
			if (empty($per_page_show)) {
				//$per_page_show = ADMIN_PAGING;
				$per_page_show = 50;
			}


			 $this->paginate = array(  "limit" => $per_page_show);

			if (isset($this->data['User']['keyword'])) {
					$keyword = trim($this->data['User']['keyword']);
			} else {
					$keyword = $this->Session->read('User.keyword');

			}
			$ascorder = 'asc';
			$pages = '';
			if( isset($this->params['named']['page']) && !empty($this->params['named']['page']) ){
				$pages = $this->params['named']['page'];
			} else if ( isset($this->params['named']['sortorder']) && !empty($this->params['named']['sortorder']) ){
				$ascorder = $this->params['named']['sortorder'];
			} else {
				$pages = '';
				$ascorder = 'asc';
			}

			if (isset($keyword) && !empty($keyword)) {
				$parms =  (isset($this->params['pass']['0']) && !empty($this->params['pass']['0'])) ? $this->params['pass']['0'] : '';
			$this->redirect(['action' => 'manage_users',$parms,'page'=>$pages,'sortorder'=>$ascorder,'search' => $keyword]);
			}



			if (isset($this->params['named']['search']) && !empty($this->params['named']['search'])) {
				$keyword = trim($this->params['named']['search']);
			} else {
				$keyword = $this->Session->read('Skill.keyword');
			}

			if (isset($keyword)) {
				$this->Session->write('user.keyword', $keyword);
				$keywords = explode(" ", $keyword);


				if (isset($keywords[0]) && !empty($keywords[0]) && count($keywords) < 2) {
					$keyword = $keywords[0];
					$in = 1;
					$orConditions = array('OR' => array(
						'User.email LIKE' => '%' . $keyword . '%',
						'UserDetail.first_name LIKE' => '%' . $keyword . '%',
						'UserDetail.last_name LIKE' => '%' . $keyword . '%',
					));

				} else if (!empty($keywords) && count($keywords) > 1) {
					$first_name = $keywords[0];
					$last_name = $keywords[1];
					$in = 1;
					$andConditions = array('AND' => array(
						'UserDetail.first_name LIKE' => '%' . $first_name . '%',
						'UserDetail.last_name LIKE' => '%' . $last_name . '%',
					));

				}
			}





			$admin_true = (isset($this->request->params['named']['flag']) && !empty($this->request->params['named']['flag'])) ? $this->request->params['named']['flag'] : 0;


			if (isset($admin_true) && !empty($admin_true)) {
				$andConditions = array('UserDetail.administrator ' => 1);
				if (isset($keywords[0]) && !empty($keywords[0]) && count($keywords) < 2) {
					$keyword = $keywords[0];
					$in = 1;
					$andConditions =  array(
						'UserDetail.administrator ' => 1
					);

				} else if (!empty($keywords) && count($keywords) > 1) {
					$first_name = $keywords[0];
					$last_name = $keywords[1];
					$in = 1;
					$andConditions = array(
						'UserDetail.first_name LIKE' => '%' . $first_name . '%',
						'UserDetail.last_name LIKE' => '%' . $last_name . '%',
						'User.managedomain_id ' => $domain_id
					);

				}


			}


			if (isset($domain_id) && !empty($domain_id)) {
				$andConditions = array('User.managedomain_id ' => $domain_id);
				if (isset($keywords[0]) && !empty($keywords[0]) && count($keywords) < 2) {
					$keyword = $keywords[0];
					$in = 1;
					$andConditions =  array(
						'User.managedomain_id ' => $domain_id
					);

				} else if (!empty($keywords) && count($keywords) > 1) {
					$first_name = $keywords[0];
					$last_name = $keywords[1];
					$in = 1;
					$andConditions = array(
						'UserDetail.first_name LIKE' => '%' . $first_name . '%',
						'UserDetail.last_name LIKE' => '%' . $last_name . '%',
						'User.managedomain_id ' => $domain_id
					);

				}


			}



			$connect_id = '';
			$passs = '';
			$listDomainUsers = '';
			if ($this->Session->read('Auth.User.role_id') == 3 ||  LOCALIP == $_SERVER['SERVER_ADDR']) {
				$connect_id = $this->Session->read('Auth.User.id');
				$passs = '';

				$this->User->recursive = 1;
					$this->User->unbindModel(
						['hasOne' => ['OrganisationUser']]
					);
					$this->User->unbindModel(
						['hasMany' => ['ProjectPermission', 'WorkspacePermission', 'ElementPermission', 'UserProject', 'UserSetting', 'UserPassword']]
					);

				if (!empty($orConditions)) {
					$finalConditions = array_merge($finalConditions, $orConditions);
				}

				if (!empty($andConditions)) {
					$andConditions = array_merge($finalConditions, array('AND' => $andConditions));
				}

				if (empty($andConditions)) {
					$andConditions =  $finalConditions ;
				}
				$this->set('in', $in);

				$this->User->recursive = 1;
					$this->User->unbindModel(
						['hasOne' => ['OrganisationUser']]
					);
					$this->User->unbindModel(
						['hasMany' => ['ProjectPermission', 'WorkspacePermission', 'ElementPermission', 'UserProject', 'UserSetting', 'UserPassword']]
					);

				if(LOCALIP == $_SERVER['SERVER_ADDR']){

				$this->paginate = array('conditions' => array(  'User.role_id' => 2, $passs, $andConditions), "group" => "User.id", 'fields' => array('User.*', 'UserDetail.*', 'UserDetail.org_id', 'UserDetail.first_name', 'UserDetail.last_name'), "order" => "User.created DESC", "limit" => $per_page_show);

				}else{

				$this->paginate = array('conditions' => array('UserDetail.org_id' => $connect_id, 'User.role_id' => 2, $passs, $andConditions), "group" => "User.id", 'fields' => array('User.*', 'UserDetail.*', 'UserDetail.org_id', 'UserDetail.first_name', 'UserDetail.last_name'), "order" => "User.created DESC", "limit" => $per_page_show);

				}

				$count = $this->User->find('count', array('conditions' => array('UserDetail.org_id' => $connect_id, 'User.role_id' => 2, $passs, $andConditions), "group" => "User.id", 'fields' => array('User.*', 'UserDetail.*', 'UserDetail.org_id', 'UserDetail.first_name', 'UserDetail.last_name')));
			}
			else if ($this->Session->read('Auth.User.UserDetail.administrator') == 1 && $this->Session->read('Auth.User.UserDetail.org_id') != 0) {

				$connect_id = $this->Session->read('Auth.User.UserDetail.org_id');
				if (!empty($orConditions)) {
					$finalConditions = array_merge($finalConditions, $orConditions);
				}

				if (!empty($andConditions)) {
					$andConditions = array_merge($finalConditions, array('AND' => $andConditions));
				}

				if (empty($andConditions)) {
					$andConditions =  $finalConditions ;
				}
				$this->User->recursive = 1;
					$this->User->unbindModel(
						['hasOne' => ['OrganisationUser']]
					);
					$this->User->unbindModel(
						['hasMany' => ['ProjectPermission', 'WorkspacePermission', 'ElementPermission', 'UserProject', 'UserSetting', 'UserPassword']]
					);




				$this->paginate = array('conditions' => array('UserDetail.org_id' => $connect_id, 'User.role_id' => 2, $passs, $andConditions), "group" => "User.id", 'fields' => array('User.*', 'UserDetail.*', 'UserDetail.org_id', 'UserDetail.first_name', 'UserDetail.last_name'), "order" => "User.created DESC", "limit" => $per_page_show);

				$count = $this->User->find('count', array('conditions' => array('UserDetail.org_id' => $connect_id, 'User.role_id' => 2, $passs, $andConditions), "group" => "User.id", 'fields' => array('User.*', 'UserDetail.*', 'UserDetail.org_id', 'UserDetail.first_name', 'UserDetail.last_name')));
			}

			if (isset($count) && $count > 0) {
				$pageCount = intval(ceil($count / $per_page_show));
				if (isset($this->request->params['named']['page']) && $this->request->params['named']['page'] > $pageCount) {
					return $this->redirect(array('controller' => 'organisations', 'action' => 'manage_users', 'admin' => false));
				}
			}
			$this->set('count', $count);
			$this->set('keyword', $keyword);
			$this->set('listDomainUsers', $this->paginate('User'));
			$this->set('in', $in);

		*/

		$userParams = ['limit' => $this->user_offset];
		$lists = $this->get_users($userParams);
		$this->set('lists', $lists);
		$this->setJsVar('user_offset', $this->user_offset);



		$viewData['crumb'] = [
			'last' => [
				'data' => [
					'title' => "Manage Users",
					'data-original-title' => "Manage Users",
				],
			],
		];

		// pr($this->paginate, 1);
		$this->set('crumb', $viewData['crumb']);


		$this->set('viewData', $viewData);
	}

	function get_users($query_params = null){

		$order_by = "ORDER BY ud.first_name ASC, ud.last_name ASC";
		if( (isset($query_params['order']) && !empty($query_params['order'])) && (isset($query_params['coloumn']) && !empty($query_params['coloumn'])) ){
			$order = $query_params['order'];
			$coloumn = $query_params['coloumn'];
			$order_by = "ORDER BY $coloumn $order";
			if($query_params['coloumn'] == 'first_name'){
				$order_by = "ORDER BY first_name $order, last_name $order";
			}
			if($query_params['coloumn'] == 'last_name'){
				$order_by = "ORDER BY last_name $order, first_name $order";
			}
		}

		$where_str = "";
		$where = [];
		if( isset($query_params['search']) && !empty($query_params['search']) ){
			$where[] = $query_params['search'];
		}
		if( isset($query_params['admin_search']) && !empty($query_params['admin_search']) ){
			$where[] = "ud.administrator = 1";
		}
		if( isset($query_params['edomain_search']) && !empty($query_params['edomain_search']) ){
			$val = $query_params['edomain_search'];
			$where[] = "u.managedomain_id = $val";
		}

		if(isset($where) && !empty($where)){
			$where_str = " AND " . implode(" AND ", $where);
		}

		$page = (isset($query_params['page']) && !empty($query_params['page'])) ? $query_params['page'] : 0;
		$limit_str = '';
		if(isset($query_params['limit']) && !empty($query_params['limit'])){
			$limit = $query_params['limit'];
			$limit_str = "LIMIT $page, $limit";
		}


		$userSQL = "SELECT
					u.id, u.email, ud.first_name, ud.last_name, u.role_id, u.activation_key, u.status, u.is_activated, u.activation_time, u.created, u.managedomain_id,
				    ud.id AS ud_id, CONCAT_WS(' ',ud.first_name , ud.last_name) AS full_name, ud.profile_pic, ud.org_id, ud.administrator, ud.create_project, ud.create_template, ud.resourcer, ud.organization_id, ud.job_title, ud.analytics
				FROM users u
				INNER JOIN user_details ud on ud.user_id = u.id

				WHERE u.email IS NOT NULL AND u.role_id = 2
					$where_str

				GROUP BY u.id

				$order_by
				$limit_str

			";
			// pr($userSQL, 1);
		return $this->User->query($userSQL);
	}


	/* USER LIST */
	public function user_list(){

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				// pr($post);
				$query_params = [];
				$query_params['limit'] = $this->user_offset;
				$query_params['page'] = (isset($post['page']) && !empty($post['page'])) ? $post['page'] : 0;
				$query_params['order'] = (isset($post['order']) && !empty($post['order'])) ? $post['order'] : 'ASC';
				$query_params['coloumn'] = (isset($post['coloumn']) && !empty($post['coloumn'])) ? $post['coloumn'] : 'first_name';

				if (isset($post['admin_search']) && !empty($post['admin_search'])) {
					$query_params['admin_search'] = 1;
				}
				if (isset($post['edomain_search']) && !empty($post['edomain_search'])) {
					$query_params['edomain_search'] = $post['edomain_search'];
				}

				if (isset($post['search']) && !empty($post['search'])) {
					$seperator = '^';
					$search_str = Sanitize::escape(like($post['search'], $seperator ));
					$query_params['search'] = " (first_name LIKE '%$search_str' ESCAPE '$seperator' OR last_name LIKE '%$search_str' ESCAPE '$seperator' OR email LIKE '%$search_str' ESCAPE '$seperator' OR CONCAT(first_name, ' ', last_name) LIKE '%$search_str%' ESCAPE '$seperator') ";
					// $query_params['search'] = " (first_name LIKE '%$search_str' ESCAPE '$seperator' OR last_name LIKE '%$search_str' ESCAPE '$seperator') ";
				}
				// pr($query_params, 1);

				$lists = $this->get_users($query_params);

				$this->set('lists', $lists);

			}
			$this->render('/Organisations/partial/user_lists');
		}
	}

	public function user_count() {
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';

			$data = [];
			$count = 0;
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;

				$query_params = [];
				$query_params['order'] = (isset($post['order']) && !empty($post['order'])) ? $post['order'] : 'ASC';
				$query_params['coloumn'] = (isset($post['coloumn']) && !empty($post['coloumn'])) ? $post['coloumn'] : 'first_name';

				if (isset($post['admin_search']) && !empty($post['admin_search'])) {
					$query_params['admin_search'] = 1;
				}
				if (isset($post['edomain_search']) && !empty($post['edomain_search'])) {
					$query_params['edomain_search'] = $post['edomain_search'];
				}

				if (isset($post['search']) && !empty($post['search'])) {
					$seperator = '^';
					$search_str = Sanitize::escape(like($post['search'], $seperator ));
					$query_params['search'] = " (first_name LIKE '%$search_str' ESCAPE '$seperator' OR last_name LIKE '%$search_str' ESCAPE '$seperator' OR CONCAT(first_name, ' ', last_name) LIKE '%$search_str%' ESCAPE '$seperator') ";
					// $query_params['search'] = " (first_name LIKE '%$search_str%' ESCAPE '$seperator' OR last_name LIKE '%$search_str%' ESCAPE '$seperator') ";
				}

				$data = $this->get_users($query_params);
				$count = count($data);

			}
			echo json_encode($count);
			exit;
		}
	}

	function manage_users1($domain_id = null) {

		$this->layout = 'inner';
		$this->set('title_for_layout', __('Manage Users', true));
		$viewData['page_heading'] = 'Manage Users';
		$viewData['page_subheading'] = 'Create and manage users';

		$orConditions = array();
        $andConditions = array();
        $finalConditions = array();

		$in = $count = 0;
		$per_page_show = 50;//$this->Session->read('user.per_page_show');
		if (empty($per_page_show)) {
			//$per_page_show = ADMIN_PAGING;
			$per_page_show = 50;
		}


		 $this->paginate = array(  "limit" => $per_page_show);
		//$keyword = (isset($this->request->data['User']['keyword']) && !empty($this->request->data['User']['keyword'])) ? $this->request->data['User']['keyword'] : '';

		if (isset($this->data['User']['keyword'])) {
				$keyword = trim($this->data['User']['keyword']);
		} else {
				$keyword = $this->Session->read('User.keyword');

		}
		$ascorder = 'asc';
		$pages = '';
		if( isset($this->params['named']['page']) && !empty($this->params['named']['page']) ){
			$pages = $this->params['named']['page'];
		} else if ( isset($this->params['named']['sortorder']) && !empty($this->params['named']['sortorder']) ){
			$ascorder = $this->params['named']['sortorder'];
		} else {
			$pages = '';
			$ascorder = 'asc';
		}

		if (isset($keyword) && !empty($keyword)) {
			$parms =  (isset($this->params['pass']['0']) && !empty($this->params['pass']['0'])) ? $this->params['pass']['0'] : '';
		$this->redirect(['action' => 'manage_users',$parms,'page'=>$pages,'sortorder'=>$ascorder,'search' => $keyword]);
		}



		if (isset($this->params['named']['search']) && !empty($this->params['named']['search'])) {
			$keyword = trim($this->params['named']['search']);
		} else {
			$keyword = $this->Session->read('Skill.keyword');
		}

		if (isset($keyword)) {
			$this->Session->write('user.keyword', $keyword);
			$keywords = explode(" ", $keyword);


			if (isset($keywords[0]) && !empty($keywords[0]) && count($keywords) < 2) {
				$keyword = $keywords[0];
				$in = 1;
				$orConditions = array('OR' => array(
					'User.email LIKE' => '%' . $keyword . '%',
					'UserDetail.first_name LIKE' => '%' . $keyword . '%',
					'UserDetail.last_name LIKE' => '%' . $keyword . '%',
					//'UserDetail.country_id LIKE' => '%' . $county . '%'
				));

			} else if (!empty($keywords) && count($keywords) > 1) {
				$first_name = $keywords[0];
				$last_name = $keywords[1];
				$in = 1;
				$andConditions = array('AND' => array(
					'UserDetail.first_name LIKE' => '%' . $first_name . '%',
					'UserDetail.last_name LIKE' => '%' . $last_name . '%',
					//'UserDetail.country_id LIKE' => '%' . $county . '%'
				));

			}
		}



		if (($this->Session->read('Auth.User.role_id') == 2 && $this->Session->read('Auth.User.UserDetail.administrator') != 1)  && LOCALIP != $_SERVER['SERVER_ADDR'] ) {

						$this->redirect(array('controller' => 'projects', 'action' => 'lists'));
		}

		$admin_true = (isset($this->request->params['named']['flag']) && !empty($this->request->params['named']['flag'])) ? $this->request->params['named']['flag'] : 0;


		if (isset($admin_true) && !empty($admin_true)) {
			$andConditions = array('UserDetail.administrator ' => 1);
			if (isset($keywords[0]) && !empty($keywords[0]) && count($keywords) < 2) {
				$keyword = $keywords[0];
				$in = 1;
				$andConditions =  array(
					//'User.email LIKE' => '%' . $keyword . '%',
					//'UserDetail.first_name LIKE' => '%' . $keyword . '%',
					//'UserDetail.last_name LIKE' => '%' . $keyword . '%',
					'UserDetail.administrator ' => 1
					//'UserDetail.country_id LIKE' => '%' . $county . '%'
				);

			} else if (!empty($keywords) && count($keywords) > 1) {
				$first_name = $keywords[0];
				$last_name = $keywords[1];
				$in = 1;
				$andConditions = array(
					'UserDetail.first_name LIKE' => '%' . $first_name . '%',
					'UserDetail.last_name LIKE' => '%' . $last_name . '%',
					'User.managedomain_id ' => $domain_id
					//'UserDetail.country_id LIKE' => '%' . $county . '%'
				);

			}


		}


		if (isset($domain_id) && !empty($domain_id)) {
			$andConditions = array('User.managedomain_id ' => $domain_id);
			if (isset($keywords[0]) && !empty($keywords[0]) && count($keywords) < 2) {
				$keyword = $keywords[0];
				$in = 1;
				$andConditions =  array(
					//'User.email LIKE' => '%' . $keyword . '%',
					//'UserDetail.first_name LIKE' => '%' . $keyword . '%',
					//'UserDetail.last_name LIKE' => '%' . $keyword . '%',
					'User.managedomain_id ' => $domain_id
					//'UserDetail.country_id LIKE' => '%' . $county . '%'
				);

			} else if (!empty($keywords) && count($keywords) > 1) {
				$first_name = $keywords[0];
				$last_name = $keywords[1];
				$in = 1;
				$andConditions = array(
					'UserDetail.first_name LIKE' => '%' . $first_name . '%',
					'UserDetail.last_name LIKE' => '%' . $last_name . '%',
					'User.managedomain_id ' => $domain_id
					//'UserDetail.country_id LIKE' => '%' . $county . '%'
				);

			}


		}



		$connect_id = '';
		$passs = '';
		$listDomainUsers = '';
		if ($this->Session->read('Auth.User.role_id') == 3 ||  LOCALIP == $_SERVER['SERVER_ADDR']) {
			$connect_id = $this->Session->read('Auth.User.id');
			$passs = '';

			$this->User->recursive = 1;
				$this->User->unbindModel(
					['hasOne' => ['OrganisationUser']]
				);
				/* $this->User->unbindModel(
					['hasAndBelongsToMany' => 'Skill']
				); */
				$this->User->unbindModel(
					['hasMany' => ['ProjectPermission', 'WorkspacePermission', 'ElementPermission', 'UserProject', 'UserSetting', 'UserPassword']]
				);

			if (!empty($orConditions)) {
				$finalConditions = array_merge($finalConditions, $orConditions);
			}

			if (!empty($andConditions)) {
				$andConditions = array_merge($finalConditions, array('AND' => $andConditions));
			}

			if (empty($andConditions)) {
				$andConditions =  $finalConditions ;
			}




			$this->set('in', $in);

			/* $listDomainUsers = $this->User->find('all', array('conditions'=>array('UserDetail.org_id'=>$connect_id,'User.role_id'=>2,$passs),"group"=>"User.id",'fields' => array('User.*', 'UserDetail.*', 'UserDetail.org_id','UserDetail.first_name','UserDetail.last_name'),   "order" => "User.created DESC")); */

			//pr($passs);

			$this->User->recursive = 1;
				$this->User->unbindModel(
					['hasOne' => ['OrganisationUser']]
				);
				/* $this->User->unbindModel(
					['hasAndBelongsToMany' => 'Skill']
				); */
				$this->User->unbindModel(
					['hasMany' => ['ProjectPermission', 'WorkspacePermission', 'ElementPermission', 'UserProject', 'UserSetting', 'UserPassword']]
				);

			if(LOCALIP == $_SERVER['SERVER_ADDR']){

			$this->paginate = array('conditions' => array(  'User.role_id' => 2, $passs, $andConditions), "group" => "User.id", 'fields' => array('User.*', 'UserDetail.*', 'UserDetail.org_id', 'UserDetail.first_name', 'UserDetail.last_name'), "order" => "User.created DESC", "limit" => $per_page_show);

			}else{

			$this->paginate = array('conditions' => array('UserDetail.org_id' => $connect_id, 'User.role_id' => 2, $passs, $andConditions), "group" => "User.id", 'fields' => array('User.*', 'UserDetail.*', 'UserDetail.org_id', 'UserDetail.first_name', 'UserDetail.last_name'), "order" => "User.created DESC", "limit" => $per_page_show);

			}

			$count = $this->User->find('count', array('conditions' => array('UserDetail.org_id' => $connect_id, 'User.role_id' => 2, $passs, $andConditions), "group" => "User.id", 'fields' => array('User.*', 'UserDetail.*', 'UserDetail.org_id', 'UserDetail.first_name', 'UserDetail.last_name')));
		}
		else if ($this->Session->read('Auth.User.UserDetail.administrator') == 1 && $this->Session->read('Auth.User.UserDetail.org_id') != 0) {

			$connect_id = $this->Session->read('Auth.User.UserDetail.org_id');
			if (!empty($orConditions)) {
				$finalConditions = array_merge($finalConditions, $orConditions);
			}

			if (!empty($andConditions)) {
				$andConditions = array_merge($finalConditions, array('AND' => $andConditions));
			}

			if (empty($andConditions)) {
				$andConditions =  $finalConditions ;
			}
			//$passs = array("User.id !=".$this->Session->read('Auth.User.id') );

			//$passs = array("User.id !=".$this->Session->read('Auth.User.id'), "User.parent_id" => $this->Session->read('Auth.User.id') );



			$this->User->recursive = 1;
				$this->User->unbindModel(
					['hasOne' => ['OrganisationUser']]
				);
				/* $this->User->unbindModel(
					['hasAndBelongsToMany' => 'Skill']
				); */
				$this->User->unbindModel(
					['hasMany' => ['ProjectPermission', 'WorkspacePermission', 'ElementPermission', 'UserProject', 'UserSetting', 'UserPassword']]
				);




			$this->paginate = array('conditions' => array('UserDetail.org_id' => $connect_id, 'User.role_id' => 2, $passs, $andConditions), "group" => "User.id", 'fields' => array('User.*', 'UserDetail.*', 'UserDetail.org_id', 'UserDetail.first_name', 'UserDetail.last_name'), "order" => "User.created DESC", "limit" => $per_page_show);

			$count = $this->User->find('count', array('conditions' => array('UserDetail.org_id' => $connect_id, 'User.role_id' => 2, $passs, $andConditions), "group" => "User.id", 'fields' => array('User.*', 'UserDetail.*', 'UserDetail.org_id', 'UserDetail.first_name', 'UserDetail.last_name')));
		}

		if (isset($count) && $count > 0) {
			$pageCount = intval(ceil($count / $per_page_show));
			if (isset($this->request->params['named']['page']) && $this->request->params['named']['page'] > $pageCount) {
				return $this->redirect(array('controller' => 'organisations', 'action' => 'manage_users', 'admin' => false));
			}
		}



		$viewData['crumb'] = [
			'last' => [
				'data' => [
					'title' => "Manage Users",
					'data-original-title' => "Manage Users",
				],
			],
		];

		// pr($this->paginate, 1);
		$this->set('crumb',$viewData['crumb']);
		$this->set('count', $count);
		$this->set('keyword', $keyword);


		$this->set('listDomainUsers', $this->paginate('User'));
		$this->set('in', $in);
		$this->set('viewData', $viewData);

		/* $this->User->find('all', array('conditions'=>array('UserDetail.org_id'=>$connect_id,'User.role_id'=>2,$passs),"group"=>"User.id",'fields' => array('User.*', 'UserDetail.*', 'UserDetail.org_id','UserDetail.first_name','UserDetail.last_name'),   "order" => "User.created DESC","limit" => $per_page_show));
		*/
		//$this->set(compact('listDomainUsers','viewData'));
	}

	function manageuser_resetfilter() {
		$this->Session->write('user.keyword', '');
		$this->Session->write('user.status', '');
		$this->Session->write('user.country', '');
		$this->redirect(array('action' => 'index'));
	}

	public function user_add($refer_to = null) {

		$this->layout = 'inner';
		$this->set('title_for_layout', __('Add User', true));
		$viewData['page_heading'] = 'Manage Domain Users';
		$viewData['page_subheading'] = 'Domain Users';
		$viewData['refer_to'] = $refer_to;

		$id = $this->Session->read('Auth.User.id');
		$this->loadModel('Timezone');


		if (($this->Session->read('Auth.User.role_id') == 2 && $this->Session->read('Auth.User.UserDetail.administrator') != 1)  && LOCALIP != $_SERVER['SERVER_ADDR'] ) {

				$this->redirect(array('controller' => 'projects', 'action' => 'lists'));
		}
		if( $this->Session->read('Auth.User.role_id') == 1 ){
				$this->redirect(['controller' => 'dashboard','action' => 'index']);
		}

		// pr($refer_to, 1);

		// ORGANIZATIONS LIST
		$organizations = array();
		$check_admin_settings = check_admin_settings();

		if( $check_admin_settings == 2){
			$current_org = $this->objView->loadHelper('Permission')->current_org();
			$current_org = $current_org['organization_id'];
			if(!empty($current_org)){
					$organizationsAll = $this->User->query("SELECT id, name FROM organizations WHERE id = '$current_org' ORDER BY name ASC");
					if (isset($organizationsAll) && !empty($organizationsAll)) {
						foreach ($organizationsAll as $org) {
							$organizations[$org['organizations']['id']] = html_entity_decode( ($org['organizations']['name'] ));
						}
					}
			}
		}
		else{
			$organizationsAll = $this->User->query("SELECT id, name FROM organizations ORDER BY name ASC");
			// pr($organizationsAll,1);
			if (isset($organizationsAll) && !empty($organizationsAll)) {
				foreach ($organizationsAll as $org) {
					$organizations[$org['organizations']['id']] = html_entity_decode( ($org['organizations']['name'] ));
				}
			}
		}
		$viewData['organizations'] = $organizations;


		// REPORTS TO USERS LIST
		$all_report_users = array();
		$ulist = $this->User->query("SELECT ud.user_id, ud.first_name, ud.last_name FROM users u INNER JOIN user_details ud ON ud.user_id = u.id WHERE u.role_id = 2 AND u.status = 1 AND u.is_activated = 1 ORDER BY ud.first_name, ud.last_name ASC");
		// pr($ulist,1);
		if (isset($ulist) && !empty($ulist)) {
			foreach ($ulist as $u) {
				$all_report_users[$u['ud']['user_id']] = $u['ud']['first_name'] . ' ' . $u['ud']['last_name'];
			}
		}
		$viewData['all_report_users'] = $all_report_users;

		// DOTTED LINE USERS LIST
		$all_users = array();
		$ulist = $this->User->query("SELECT ud.user_id, ud.first_name, ud.last_name FROM users u INNER JOIN user_details ud ON ud.user_id = u.id WHERE u.role_id = 2 AND u.status = 1 AND u.is_activated = 1 ORDER BY ud.first_name, ud.last_name ASC");
		// pr($ulist,1);
		if (isset($ulist) && !empty($ulist)) {
			foreach ($ulist as $u) {
				$all_users[$u['ud']['user_id']] = $u['ud']['first_name'] . ' ' . $u['ud']['last_name'];
			}
		}
		$viewData['all_users'] = $all_users;

		if ($this->request->is('post') || $this->request->is('put')) {

			$_SESSION['data'] = $this->request->data;
			// pr($this->request->data, 1);

			// CONNECT WITH MONGO DB
			if ($this->live_setting == true) {

				if (PHP_VERSIONS == 5) {
					$mongo = new MongoClient(MONGO_CONNECT);
					$this->mongoDB = $mongo->selectDB(MONGO_DATABASE);
					$mongo_collection = new MongoCollection($this->mongoDB, 'users');
				} else {

					$mongo = new MongoDB\Driver\Manager(MONGO_CONNECT);
					$mongo_collection = new MongoDB\Driver\BulkWrite;
				}

			}

			if (check_license()) {

				if (isset($this->request->data['User']['email']) && !empty($this->request->data['User']['email'])) {
					$post = $this->request->data;

					// pr($this->validationErrors );
					$userEmail = $this->request->data['User']['email'];
					$getEmail = explode("@", $userEmail);
					if (isset($getEmail[1]) && !empty($getEmail[1])) {
						//$getDomain = explode(".",$getEmail[1]);
						$getDomain = strtolower($getEmail[1]);
					}

					$pmin_length = 5;
					$policy_checks = [];
					$policy_checks[] = 'lower_case';
					//echo $getDomain; die;
					//$checkorgDomain = $this->checkOrgdomainforUser($getDomain[0], $this->Session->read('Auth.User.id'));

					$organisations_id = $this->Session->read('Auth.User.id');
					$as_og = $this->Session->read('Auth.User.UserDetail.org_id');

					if (isset($as_og) && $as_og > 0) {
						$organisations_id = $this->Session->read('Auth.User.UserDetail.org_id');
					}

					$this->request->data['UserDetail']['org_id'] = $organisations_id;

					if (isset($getDomain) && !empty($getDomain)) {
						$posted_org = (isset($post['UserDetail']['organization_id']) && !empty($post['UserDetail']['organization_id'])) ? $post['UserDetail']['organization_id'] : null;
						$checkorgDomain = $this->checkEmaildomainforUser($getDomain, $organisations_id, $posted_org);

						//================= Pawan Check password policy =================
						//if( $_SERVER['SERVER_NAME'] == 'dotsquares.ideascast.com' ) {

						$this->request->data['UserDetail']['org_id'] = $organisations_id;
						$this->request->data['User']['managedomain_id'] = $checkorgDomain;
						$orgPasswordPolicy = $this->OrgPassPolicy->find('first', array('conditions' => array('OrgPassPolicy.org_id' => $this->request->data['UserDetail']['org_id'])));

						if (isset($orgPasswordPolicy['OrgPassPolicy']) && !empty($orgPasswordPolicy['OrgPassPolicy'])) {

							if (!empty($orgPasswordPolicy['OrgPassPolicy']['min_lenght'])) {

								$pmin_length = $orgPasswordPolicy['OrgPassPolicy']['min_lenght'] + 1;

							}
							if ($orgPasswordPolicy['OrgPassPolicy']['numeric_char'] == 1) {

								$policy_checks[] = 'numbers';

							}if (isset($orgPasswordPolicy['OrgPassPolicy']['alph_char']) && !empty($orgPasswordPolicy['OrgPassPolicy']['alph_char'])) {

								$policy_checks[] = 'alphbate';
							}
							if (isset($orgPasswordPolicy['OrgPassPolicy']['special_char']) && !empty($orgPasswordPolicy['OrgPassPolicy']['special_char'])) {

								$policy_checks[] = 'special_symbols';
							}

							if (isset($orgPasswordPolicy['OrgPassPolicy']['caps_char']) && !empty($orgPasswordPolicy['OrgPassPolicy']['caps_char'])) {

								$policy_checks[] = 'upper_case';
							}

						}

					}

					$policy_checks = implode(',', $policy_checks);

					$policy_password = policy_password($pmin_length, $policy_checks);

					if($_SERVER["SERVER_NAME"] == "192.168.7.20"){

						$checkorgDomain = true;
					}

					if (isset($checkorgDomain) && $checkorgDomain == false) {
						$this->Session->setFlash(__('Supplied email domain does not allowed, please try again with valid email domain.'), 'error');
					} else {

						$userPassword = '';
						$this->UserDetail->validator()->remove('org_name');
						$this->UserDetail->validator()->remove('department_id');

						$this->request->data['UserDetail']['added_by'] = $this->Session->read('Auth.User.id');
						$this->request->data['UserDetail']['updated_by'] = $this->Session->read('Auth.User.id');

						//add department id when user adding
						// $departmentData = $this->getDepartment();
						//$this->request->data['UserDetail']['department'] = $departmentData['Department']['name'];
						// $this->request->data['UserDetail']['department_id'] = $departmentData['Department']['id'];

						$this->request->data['User']['managedomain_id'] = $checkorgDomain;

						$userPassword = $policy_password;

						$this->request->data['User']['parent_id'] = $this->Session->read('Auth.User.id');
						$this->request->data['User']['email'] = strtolower($this->request->data['User']['email']);
						$this->request->data['User']['role_id'] = '2';

						/* activation */
						if(UA) {
							// $this->request->data['User']['status'] = 0;
							$this->request->data['User']['activation_time'] = date('Y-m-d h:i:s');
						}
						else{
							$this->request->data['User']['status'] = 1;
						}
						/* activation */

						$pans = AuthComponent::password($userPassword);
						$this->request->data['User']['password'] = $userPassword;
						$this->request->data['UserPassword']['password'] = $pans;
						$this->request->data['UserDetail']['org_password'] = $userPassword;

						/* $UserDottedLine = $this->request->data['UserDottedLine'];
						unset($this->request->data['UserDottedLine']);
						unset($this->request->data['dotted_user_id']); */

						if ($this->User->saveAssociated($this->request->data)) {
							$userId = $this->User->getLastInsertID();

							if (isset($UserDottedLine['dotted_user_id']) && !empty($UserDottedLine['dotted_user_id'])) {
								// Delete previous entries
								$this->User->query("DELETE FROM user_dotted_lines WHERE user_id = $userId");
								$dotted_user_id = $UserDottedLine['dotted_user_id'];
								$qry = "INSERT INTO `user_dotted_lines` (`user_id`, `dotted_user_id`) VALUES ";
								$qry_arr = [];
			    				foreach ($dotted_user_id as $key => $value) {
			    					$qry_arr[] = "($userId, $value)";
			    				}
			    				$qry .= implode(' ,', $qry_arr);
			    				$this->User->query($qry);
							}

							$this->request->data['UserPassword']['user_id'] = $userId;
							$this->UserPassword->save($this->request->data);

							$sqlN = "update user_details set user_details.org_password = AES_ENCRYPT(org_password, 'secret')  WHERE user_details.user_id =" . $userId;

							$this->UserDetail->query($sqlN);

							$loggedInTimzone = $this->Timezone->find('first', array('conditions' => array('Timezone.user_id' => $this->Session->read('Auth.User.id'))));

							if ($this->live_setting == true) {
								$this->Users->addUser($userId);
							}

							unset($_SESSION['data']);

							$parentid = $this->User->find('first', array('conditions' => array('User.id' => $userId)));

							$getorgname = $this->User->find('first', array('conditions' => array('User.id' => $parentid['User']['parent_id'])));

							$clientAdminEmail = $getorgname['User']['email'];

							// email to user
							if(UA){
								$this->CommonEmail->user_activation(['user_id' => $userId]);
							}
							else{
								$this->__sendEmailConfirm($this->request->data, $userPassword, $getorgname['UserDetail']['org_name'],'',$clientAdminEmail);
							}

							if(isset($refer_to) && !empty($refer_to)) {
								if($refer_to == 'people'){
									$this->redirect(array('controller' => 'resources', 'action' => 'people'));
								}
							}
							$this->Session->setFlash(__('User Profile has been added successfully.'), 'success');
							$this->redirect(array('action' => 'manage_users'));

						}
					}
				}
				else{
					$this->User->validationErrors['User']['email'] = 'Email is required';
				}
			} else {
				$this->Session->setFlash(__('You are not allowed to add user under the organization because your licencing limit has been exceeded.'), 'error');
			}

		}

		$this->set($viewData);


	}

	public function user_edit($id = null) {
		$this->layout = 'inner';
		$this->set('title_for_layout', __('Edit User', true));

		$viewData['page_heading'] = 'Manage Domain Users';
		$viewData['page_subheading'] = 'Domain Users';
		$this->loadModel('Timezone');
		if (isset($this->request->data['User']['id'])) {
			$id = $this->request->data['User']['id'];
		}
		$this->User->id = $id;


		$organizations = array();
		$organizationsAll = $this->User->query("SELECT id, name FROM organizations ORDER BY name ASC");
		// pr($organizationsAll,1);
		if (isset($organizationsAll) && !empty($organizationsAll)) {
			foreach ($organizationsAll as $org) {
				$organizations[$org['organizations']['id']] = html_entity_decode( ($org['organizations']['name'] ));
			}
		}
		$this->set("organizations", $organizations);

		$all_users = array();
		$ulist = $this->User->query("SELECT ud.user_id, ud.first_name, ud.last_name FROM users u INNER JOIN user_details ud ON ud.user_id = u.id WHERE u.role_id = 2 AND u.status = 1 AND u.is_activated = 1 AND u.id <> $id ORDER BY ud.first_name, ud.last_name ASC");
		// pr($ulist,1);
		if (isset($ulist) && !empty($ulist)) {
			foreach ($ulist as $u) {
				$all_users[$u['ud']['user_id']] = $u['ud']['first_name'] . ' ' . $u['ud']['last_name'];
			}
		}
		$this->set("all_users", $all_users);

		// pr($this->User);exit;
		if (!$this->User->exists()) {
			$this->Session->setFlash(__('Invalid User.'), 'error');
			$this->redirect(array('action' => 'manage_users'));
		}

		if (($this->Session->read('Auth.User.role_id') == 2 && $this->Session->read('Auth.User.UserDetail.administrator') != 1)  && LOCALIP != $_SERVER['SERVER_ADDR'] ) {

						$this->redirect(array('controller' => 'projects', 'action' => 'lists'));
		}
		if( $this->Session->read('Auth.User.role_id') == 1 ){
				$this->redirect(['controller' => 'dashboard','action' => 'index']);
		}

		//pr($this->request->data); die;

		if ($this->request->is('post') || $this->request->is('put')) {

			$post = $this->request->data;
			// pr($post, 1);

			$pagerefereurl = ( isset($this->request->data['User']['pagerefer']) && !empty($this->request->data['User']['pagerefer']) )? $this->request->data['User']['pagerefer'] : '' ;

			// CONNECT WITH MONGO DB
			if ($this->live_setting == true) {
				if (PHP_VERSIONS == 5) {
					$mongo = new MongoClient(MONGO_CONNECT);
					$this->mongoDB = $mongo->selectDB(MONGO_DATABASE);
					$mongo_collection = new MongoCollection($this->mongoDB, 'users');
				} else {
					$mongo = new MongoDB\Driver\Manager(MONGO_CONNECT);
					$mongo_collection = new MongoDB\Driver\BulkWrite;
				}
			}

			//==================================================================

			if (isset($this->request->data['User']['email']) && !empty($this->request->data['User']['email'])) {

				$userEmail = $this->request->data['User']['email'];
				$getEmail = explode("@", $userEmail);
				if (isset($getEmail[1]) && !empty($getEmail[1])) {
					//$getDomain = explode(".",$getEmail[1]);
					$getDomain = strtolower($getEmail[1]);
				}

				$organisations_id = $this->Session->read('Auth.User.id');
				$as_og = $this->Session->read('Auth.User.UserDetail.org_id');

				if (isset($as_og) && $as_og > 0) {
					$organisations_id = $this->Session->read('Auth.User.UserDetail.org_id');
				}

				$this->request->data['UserDetail']['org_id'] = $organisations_id;

				if (isset($getDomain) && !empty($getDomain)) {
					$posted_org = (isset($post['UserDetail']['organization_id']) && !empty($post['UserDetail']['organization_id'])) ? $post['UserDetail']['organization_id'] : null;
					$checkorgDomain = $this->checkEmaildomainforUser($getDomain, $organisations_id, $posted_org);

					//================= Pawan Check password policy =================
					//if( $_SERVER['SERVER_NAME'] == 'dotsquares.ideascast.com' ) {

					$this->request->data['UserDetail']['org_id'] = $organisations_id;
					$this->request->data['User']['managedomain_id'] = $checkorgDomain;
					$orgPasswordPolicy = $this->OrgPassPolicy->find('first', array('conditions' => array('OrgPassPolicy.org_id' => $this->request->data['UserDetail']['org_id'])));

					$this->request->data['User']['password'] = trim($this->request->data['User']['password']);



					if((isset($this->request->data['User']['password']) && !empty($this->request->data['User']['password'])) && (!isset($this->request->data['User']['cpassword']) || empty($this->request->data['User']['cpassword']))){

						 unset($this->request->data['User']['password']);
					}


					if (isset($this->request->data['User']['password']) && !empty($this->request->data['User']['password'])) {

						if (!isset($orgPasswordPolicy['OrgPassPolicy']) || empty($orgPasswordPolicy['OrgPassPolicy'])) {

							if (strlen($this->request->data['User']['password']) > 0 && strlen($this->request->data['User']['password']) < 4) {

								$this->Session->setFlash(__('Password should be at least 4 characters'), 'error');
								$this->redirect(array('controller' => 'organisations', 'action' => 'user_edit', $id));

							}
						}

					}

					if (isset($orgPasswordPolicy['OrgPassPolicy']) && !empty($orgPasswordPolicy['OrgPassPolicy']) && isset($this->request->data['User']['password']) && !empty($this->request->data['User']['password'])) {

						if (isset($orgPasswordPolicy['OrgPassPolicy']['pass_repeat'])) {
							$previousPassword = $this->checkUserPrePassword($id, $orgPasswordPolicy['OrgPassPolicy']['pass_repeat']);
						}

						if (isset($orgPasswordPolicy['OrgPassPolicy']['min_lenght']) && strlen($this->request->data['User']['password']) < $orgPasswordPolicy['OrgPassPolicy']['min_lenght']) {

							$this->Session->setFlash(__('Password should be at least ' . $orgPasswordPolicy['OrgPassPolicy']['min_lenght'] . ' characters'), 'error');
							$this->redirect(array('controller' => 'organisations', 'action' => 'user_edit', $id));

						}

						if (isset($orgPasswordPolicy['OrgPassPolicy']['numeric_char']) && $orgPasswordPolicy['OrgPassPolicy']['numeric_char'] == 1) {

							if (!preg_match('/[0-9]/', $this->request->data['User']['password'])) {

								$this->Session->setFlash(__('Password should have minimum one numeric character.'), 'error');
								$this->redirect(array('controller' => 'organisations', 'action' => 'user_edit', $id));

							}

						}

						if (isset($orgPasswordPolicy['OrgPassPolicy']['alph_char']) && $orgPasswordPolicy['OrgPassPolicy']['alph_char'] == 1) {

							if (!preg_match('/[a-zA-Z]/', $this->request->data['User']['password'])) {

								$this->Session->setFlash(__('Password should have minimum one alpha character.'), 'error');
								$this->redirect(array('action' => 'organisations', 'action' => 'user_edit', $id));

							}
						}
						if (isset($orgPasswordPolicy['OrgPassPolicy']['special_char']) && $orgPasswordPolicy['OrgPassPolicy']['special_char'] == 1) {

							if (!preg_match('/\W/', $this->request->data['User']['password'])) {

								$this->Session->setFlash(__('Password should have minimum one special character.'), 'error');
								$_SESSION['data'] = $this->request->data;
								$this->redirect(array('action' => 'organisations', 'action' => 'user_edit', $id));

							}
						}

						if (isset($orgPasswordPolicy['OrgPassPolicy']['caps_char']) && $orgPasswordPolicy['OrgPassPolicy']['caps_char'] == 1) {

							if (!preg_match('/[A-Z]+/', $this->request->data['User']['password'])) {

								$this->Session->setFlash(__('Password should have minimum one capital character.'), 'error');
								$_SESSION['data'] = $this->request->data;
								$this->redirect(array('action' => 'organisations', 'action' => 'user_edit', $id));

							}
						}

						$newPassword = AuthComponent::password(trim($this->request->data['User']['password']));
						if (isset($orgPasswordPolicy['OrgPassPolicy']['pass_repeat'])) {
							if (in_array($newPassword, $previousPassword)) {

								//$this->User->validationErrors['password'] = "Password should not be from your last " . $orgPasswordPolicy['OrgPassPolicy']['pass_repeat'] . " passwords";

								$this->Session->setFlash(__("Password should not be from your last " . $orgPasswordPolicy['OrgPassPolicy']['pass_repeat'] . " passwords"), 'error');
								$this->redirect(array('action' => 'organisations', 'action' => 'user_edit', $id));

							}
						}

					}

					//}

				}

				if($_SERVER["SERVER_NAME"] =="192.168.7.20"){

						$checkorgDomain = true;
				}


				if (isset($checkorgDomain) && $checkorgDomain == false) {

					$this->Session->setFlash(__('Supplied email domain does not allowed, please try again with valid email domain.'), 'error');

					//$this->redirect(array('action' =>'user_add'));

				} else {

					$userPassword = '';
					$this->UserDetail->validator()->remove('org_name');

					$this->request->data['UserDetail']['added_by'] = $this->Session->read('Auth.User.id');
					$this->request->data['UserDetail']['updated_by'] = $this->Session->read('Auth.User.id');
					$this->request->data['User']['managedomain_id'] = $checkorgDomain;

					if (isset($this->data['User']['password']) && !empty($this->data['User']['password'])) {
						$userPassword = $this->request->data['User']['password'];
					}

					//$this->request->data['User']['status'] = 1;
					$this->request->data['User']['parent_id'] = $this->Session->read('Auth.User.id');
					$this->request->data['User']['email'] = strtolower($this->request->data['User']['email']);

					if (isset($this->data['User']['password']) && !empty($this->data['User']['password'])) {
						$this->request->data['UserDetail']['org_password'] = $userPassword;
						$pans = AuthComponent::password($this->data['User']['password']);
						$this->request->data['UserPassword']['password'] = $pans;
					}

					$this->User->set($this->request->data);
					if ($this->User->validates()) {

						$userId = $this->request->data['User']['id'];
						$userOldData = $this->User->find('first', ['conditions' => ['User.id' => $userId], 'fields' => ['User.email'], 'recursive' => -1]);
						// IF EMAIL UPDATED
						if($userOldData['User']['email'] != $this->request->data['User']['email']) {
							$this->request->data['User']['status'] = 0;
							$this->request->data['User']['is_activated'] = 0;
							$this->request->data['User']['activation_time'] = date('Y-m-d h:i:s');
							$this->request->data['User']['email_notification'] = 0;
							$this->request->data['User']['web_notification'] = 0;
						}
						else{
							if(!isset($this->request->data['User']['status'])){
								$this->request->data['User']['status'] = 0;
								$this->request->data['User']['email_notification'] = 0;
								$this->request->data['User']['web_notification'] = 0;
							}else{
								$this->request->data['User']['status'] = 1;
								$this->request->data['User']['email_notification'] = 1;
								$this->request->data['User']['web_notification'] = 1;

							}
						}


						if ($this->User->saveAssociated($this->request->data)) {

							$this->request->data['UserPassword']['user_id'] = $userId;


							if (isset($post['UserDottedLine']['dotted_user_id']) && !empty($post['UserDottedLine']['dotted_user_id'])) {
								// Delete previous entries
								$this->User->query("DELETE FROM user_dotted_lines WHERE user_id = $userId");
								$dotted_user_id = $post['UserDottedLine']['dotted_user_id'];
								$qry = "INSERT INTO `user_dotted_lines` (`user_id`, `dotted_user_id`) VALUES ";
								$qry_arr = [];
			    				foreach ($dotted_user_id as $key => $value) {
			    					$qry_arr[] = "('$userId', '$value')";
			    				}
			    				$qry .= implode(' ,', $qry_arr);
			    				$this->User->query($qry);
							}


							if (isset($this->data['User']['password']) && !empty($this->data['User']['password'])) {

							$sqlN = "update user_details set user_details.org_password = AES_ENCRYPT(org_password, 'secret')  WHERE user_details.user_id =" . $userId;

							$this->UserDetail->query($sqlN);
							}

							$this->UserPassword->save($this->request->data);

							/*$sql = "SELECT u.id,u.email,u.password,ud.first_name as firstname,ud.last_name as lastname, ud.profile_pic as thumb,ud.department_id, ud.job_title, ud.job_role, ud.bio, ud.org_name, ud.contact, timezones.name as timezone_name, timezones.timezone as timezone_offset, dept.name as dept_name FROM users as u LEFT JOIN user_details as ud ON u.id=ud.user_id  LEFT JOIN timezones ON u.id=timezones.user_id LEFT JOIN departments dept ON dept.id = ud.department_id WHERE u.id=" . $userId;
							$user_result = $this->User->query($sql);*/

							$loggedInTimzone = $this->Timezone->find('first', array('conditions' => array('Timezone.user_id' => $this->Session->read('Auth.User.id'))));

							if ($this->live_setting == true) {
								$this->Users->addUser($userId, true);
							}

							unset($_SESSION['data']);

							if($userOldData['User']['email'] != $this->request->data['User']['email']) {
								$email_data = ['user_id' => $userId];
								/*if (isset($this->data['User']['password']) && !empty($this->data['User']['password'])) {
									$email_data['password'] = $userPassword;
								}*/
								$this->CommonEmail->user_activation($email_data);
							}

							$this->Session->setFlash(__('User has been updated successfully.'), 'success');
							if( !empty($pagerefereurl) ){
								$this->redirect($pagerefereurl);
							} else {
								$this->redirect(array('action' => 'manage_users'));
							}

						} else {
							$this->request->data = $this->User->read(null, $id);
							if (isset($this->request->data['User']['password'])) {
								unset($this->request->data['User']['password']);
							}
						}

					} //pawan

				}
			}


		} else {
			$this->request->data = $this->User->read(null, $id);
			//$this->request->data = $this->User->findById($id);
			if (isset($this->request->data['User']['password'])) {
				unset($this->request->data['User']['password']);
			}

			$selectedDotted = [];
			$dottedUsers = $this->User->query("SELECT dotted_user_id FROM user_dotted_lines WHERE user_id = $id");
			if(isset($dottedUsers) && !empty($dottedUsers)){
				$selectedDotted = Set::extract($dottedUsers, '/user_dotted_lines/dotted_user_id');
			}
			$this->request->data['UserDottedLine']['dotted_user_id'] = $selectedDotted;
		}
	}

	public function checkdomain() {

		if ($this->request->isAjax()) {

			$this->autoRender = false;
			$this->layout = 'ajax';
			$response = ['success' => false, 'content' => null];

			$this->loadModel("OrganisationUser");

			if (isset($this->request->data['domainName']) && !empty($this->request->data['domainName'])) {

				$cdmain = 0;
				$cdmain = $this->OrganisationUser->find('count', array('conditions' => array('OrganisationUser.domain_name' => $this->request->data['domainName'], 'OrganisationUser.user_id != ' => $this->request->data['userid'])));

				/* $xmlapi = new XmlApi('127.0.0.1',CPANELUSR,CPANELPASS);
					$xmlapi->set_port( 2083 );
					$xmlapi->set_output('json');
					$xmlapi->set_hash("username", CPANELUSR);
					$xmlapi->password_auth(CPANELUSR,CPANELPASS);
					$xmlapi->set_debug(1);

					$subdomainsList = $xmlapi->api2_query(CPANELUSR, 'SubDomain', 'listsubdomains');

					$result = json_decode($subdomainsList);

					foreach( $result->cpanelresult->data as $domainList){
						if( $this->request->data['domainName'] == $domainList->subdomain ){
							$cdmain = 1;
						}
				*/

				if ($cdmain > 0) {
					$response = ['success' => false, 'content' => "This domain has already been taken"];
				} else {
					$response = ['success' => true, 'content' => "This domain now available"];
				}
				echo json_encode($response);
				exit;
			} else {
				$response = ['success' => false, 'content' => "Please fill out this filed"];
				echo json_encode($response);
				exit;
			}
		}

	}

	public function checkOrgdomainforUser($domainName = null, $org_id = null) {

		//$this->autoRender = false;
		$this->loadModel("OrganisationUser");
		$response = true;
		if (isset($domainName) && !empty($domainName)) {

			$cdmain = $this->OrganisationUser->find('count', array('conditions' => array('OrganisationUser.domain_name' => $domainName, 'OrganisationUser.user_id' => $org_id)));

			if ($cdmain <= 0) {
				$response = false;
			}

		} else {
			$response = false;
		}
		return $response;
		exit;

	}

	public function email_validation() {

		if ($this->request->isAjax()) {
            $this->layout = false;
            $response = ['success' => false, 'content' => []];
            $data = [];
            if ($this->request->is('post') || $this->request->is('put')) {
            	$post = $this->request->data;
            	$userEmail = $post['email'];
				$getEmail = explode("@", $userEmail);
				$domainName = '';
				if (isset($getEmail[1]) && !empty($getEmail[1])) {
					$domainName = strtolower($getEmail[1]);
				}

				$posted_org = $post['org_id'];

				$this->loadModel("ManageDomain");

				if (isset($domainName) && !empty($domainName)) {

					$this->ManageDomain->unbindModel(
						array('belongsTo' => array('Domain'))
					);

					$conditions = array('ManageDomain.domain_name' => $domainName, 'ManageDomain.create_account' => 1);
					if(isset($posted_org) && !empty($posted_org)) {
						$this->loadModel('OrganizationEmailDomain');
						$oed = $this->OrganizationEmailDomain->query("SELECT email_domain_id FROM organization_email_domains WHERE organization_id = $posted_org");
						if(isset($posted_org) && !empty($posted_org)) {
							$eod_id = Set::extract($oed, '{n}.organization_email_domains.email_domain_id');
							$conditions['ManageDomain.id'] = $eod_id;
						}
					}

					$cdmain = $this->ManageDomain->find('first', array('conditions' => $conditions));

					if (isset($cdmain['ManageDomain'])) {
						$response['success'] = true;
					}
				}
			}
			echo json_encode($response);
            exit;
		}

	}

	public function checkEmaildomainforUser($domainName = null, $org_id = null, $posted_org = null) {

		// $this->autoRender = false;
		$this->loadModel("ManageDomain");
		$response = true;

		if (isset($domainName) && !empty($domainName)) {

			$this->ManageDomain->unbindModel(
				array('belongsTo' => array('Domain'))
			);

			$conditions = array('ManageDomain.domain_name' => $domainName, 'ManageDomain.create_account' => 1);
			if(isset($posted_org) && !empty($posted_org)) {
				$this->loadModel('OrganizationEmailDomain');
				$oed = $this->OrganizationEmailDomain->query("SELECT email_domain_id FROM organization_email_domains WHERE organization_id = $posted_org");
				if(isset($posted_org) && !empty($posted_org)) {
					$eod_id = Set::extract($oed, '{n}.organization_email_domains.email_domain_id');
					$conditions['ManageDomain.id'] = $eod_id;
				}
			}

			$cdmain = $this->ManageDomain->find('first', array('conditions' => $conditions));

			if (isset($cdmain['ManageDomain'])) {
				$response = $cdmain['ManageDomain']['id'];
			} else {
				$response = false;
			}

		} else {
			$response = false;
		}
		return $response;

	}

	function organisation_user_reset() {
		if ($this->request->is('ajax')) {

			$this->autoRender = false;
			$this->loadModel('User');

			App::import("Model", "User");
			$this->User = new User();
			$secrets = NULL;

			if (isset($this->request->data['user_id']) && !empty($this->request->data['user_id'])) {

			$ud = $this->User->findById(array('User.id' => $this->request->data['user_id']));

			$this->UserDetail->id = $ud['UserDetail']['id'];
			$this->UserDetail->saveField('membership_code', $secrets);

			if(isset($ud['User']['role_id']) && $ud['User']['role_id']==3){

				$this->UserDetail->updateAll(array('membership_code' => NULL),  array('UserDetail.membership_code !=' => ''));

				$this->UserDetail->updateAll(array('backup_code' => NULL),  array('UserDetail.backup_code !=' => ''));

				unset($_SESSION['secrets']);
			}

			}

		}
	}

	function backup_code($flag= null){

		if(isset($flag) && $flag > 1){

		$userData =  $this->UserDetail->find('first', ['conditions' => ['UserDetail.user_id' => $this->Auth->user('id')], 'recursive' => -1, 'fields' => ['backup_code' ]]);

		$this->set('backup_code', $userData['UserDetail']['backup_code']);

		}else{

		$userData =  $this->UserDetail->find('first', ['conditions' => ['UserDetail.user_id' => $this->Auth->user('id')], 'recursive' => -1, 'fields' => [ 'id','backup_code','membership_code' ]]);



		if(isset($userData['UserDetail']['backup_code']) && !empty($userData['UserDetail']['backup_code'])){

			$newCode = AuthComponent::password($userData['UserDetail']['backup_code']);
		}else{
			$newCode = AuthComponent::password($userData['UserDetail']['membership_code']);
			$newCode = AuthComponent::password($newCode);

		}

		$this->UserDetail->id = $userData['UserDetail']['id'];
		$this->UserDetail->saveField('backup_code', $newCode);

		$this->set('backup_code', $newCode);

		}

		$this->set('flag', $flag);

	}

	function authentication($flag= null){
		$response = ['success' => false, 'content' => ''];
		if ($this->request->is('post') || $this->request->is('put')) {


			if(isset($this->request->data['code']) && !empty($this->request->data['code'])){


		$userData = $this->User->find('first', ['conditions' => ['User.id' => $this->Auth->user('id')], 'recursive' => -1, 'fields' => ['page_setting_toggle', 'landing_url', 'landing_id']]);
		$userData = (isset($userData) && !empty($userData)) ? $userData['User'] : null;
		$this->set('userData', $userData);

		App::import('Vendor', 'googleAuth/GoogleAuthenticator');

		$ga = new GoogleAuthenticator();

			if( !isset($_SESSION['secrets'] ) || empty($_SESSION['secrets'] )){

				$_SESSION['secrets'] = $this->request->data['secret'];


			}


			if( isset($_SESSION['secrets'] ) && !empty($_SESSION['secrets'] )){
				$secrets = $_SESSION['secrets'];
			}

			$membership_code = $this->Session->read('Auth.User.UserDetail.membership_code');

			if(!isset($flag) || $flag !=1){

			if(isset($membership_code) && !empty($membership_code)){

				$secrets = $membership_code;
			}
			}

			//pr($secrets); die;



			$code = trim($this->request->data['code']);

			$code = str_replace(' ','',$code);
			//pr(trim($code)); die;

			$user_id = $this->Session->read('Auth.User.UserDetail.id');



			//membership_code

			$checkResult = $ga->verifyCode($secrets, $code,2);    // 2 = 2*30sec clock tolerance

			if ($checkResult)
			{
				 $this->UserDetail->id = $user_id;
				 $this->UserDetail->saveField('membership_code', $secrets);
				 $this->UserDetail->saveField('backup_code', AuthComponent::password($secrets));
				 $_SESSION['check_secrets'] = 1;
				 $response['success'] = true;
				 $response['content'] = '';
				 $response['error'] = '';
				 echo json_encode($response);
				 exit();

			}else{
				$response['success'] = false;
				$response['content'] = '';
				$response['error'] = 'Invalid two-factor code';

				echo json_encode($response);
				exit();

			}




			}else{
				$response['success'] = false;
				$response['content'] = '';
				$response['error'] = 'Code is required';

				echo json_encode($response);
				exit();
			}

		}else{
			//$response = ['success' => false, 'content' => '','error'=>'Code is required'];
			//echo json_encode($response);
			//exit();
		}


	}

	function organisation_user_delete() {

		if ($this->request->is('ajax')) {

			$this->autoRender = false;
			$this->loadModel('User');

			App::import("Model", "User");
			$this->User = new User();


			if (isset($this->request->data['user_id']) && !empty($this->request->data['user_id'])) {

				$id = $this->request->data['user_id'];

				$this->User->id = $id;

				if (!$this->User->exists()) {
					throw new NotFoundException(__('Invalid User'), 'error');
				}

				if ($this->User->delete()) {
					//if ($id) {

					$this->loadModel('UserProject');
					$this->loadModel('ProjectWorkspace');
					$this->loadModel('Project');
					$this->loadModel('Area');
					$this->loadModel('Workspace');
					$this->loadModel('Element');
					$this->loadModel('ElementLink');
					$this->loadModel('ElementDocument');
					$this->loadModel('ElementFeedback');
					$this->loadModel('ElementMindmap');
					$this->loadModel('ElementNote');
					$this->loadModel('ElementDecision');
					$this->loadModel('UserSkill');
					$this->loadModel('SkillDetail');
					$this->loadModel('UserLocation');

					$this->loadModel('ProgramProject');
					$this->loadModel('Program');
					$this->loadModel('ProgramUser');


					$program_ids = $this->Program->find('list', ['conditions' => ['Program.created_by' => $id ],'fields' => array('Program.id')]);

					if(isset($program_ids) && !empty($program_ids)){
					$this->ProgramProject->deleteAll(array('ProgramProject.program_id' => $program_ids));
					$this->Program->deleteAll(array('Program.id' => $program_ids));
					$this->ProgramUser->deleteAll(array('ProgramUser.program_id' => $program_ids));
					}
					$this->ProgramUser->deleteAll(array('ProgramUser.user_id' => $id));


					if ($this->live_setting == true) {

						$whatINeed = explode(WEBDOMAIN, $_SERVER['HTTP_HOST']);
						$whatINeed = $whatINeed[0];

						if (PHP_VERSIONS == 5) {
							$mongo = new MongoClient(MONGO_CONNECT);
							$db = $mongo->$whatINeed;
							$collection = $db->users;
							$qry = array("id" => $id);
							$ret = $collection->remove(array('id' => (int) $id));
						} else {
							$mongo = new MongoDB\Driver\Manager(MONGO_CONNECT);
							$bulk = new MongoDB\Driver\BulkWrite;
							$qry = array("id" => $id);
							$ret = $bulk->delete(array('id' => (int) $id));
							$mongo->executeBulkWrite(MONGO_DATABASE.'.users', $bulk);
						}


					}

					$this->UserDetail->deleteAll(array('UserDetail.user_id' => $id));

					$this->SkillDetail->deleteAll(array('SkillDetail.user_id' => $id));

					$this->UserSkill->deleteAll(array('UserSkill.user_id' => $id));

					$this->UserLocation->deleteAll(array('UserLocation.user_id' => $id));

					$userasspro = $this->UserProject->find('all', array('fields' => array('UserProject.*'), 'conditions' => array('UserProject.user_id' => $id)));
					if( isset($userasspro) && !empty($userasspro) ){
						foreach ($userasspro as $usp) {

							$this->UserProject->delete($usp['UserProject']['id']);

							$this->Project->delete($usp['UserProject']['project_id']);

							$ProjectWorkspace = $this->ProjectWorkspace->find('all', array('fields' => array('ProjectWorkspace.*'), 'conditions' => array('ProjectWorkspace.project_id' => $usp['UserProject']['project_id'])));

							if( isset($ProjectWorkspace) && !empty($ProjectWorkspace) ){

								foreach ($ProjectWorkspace as $wsp) {

									$this->ProjectWorkspace->delete($wsp['ProjectWorkspace']['id']);

									$this->Workspace->deleteAll(array('Workspace.id' => $wsp['ProjectWorkspace']['workspace_id']));

									$area = $this->Area->find('all', array('fields' => array('Area.*'), 'conditions' => array('Area.workspace_id' => $wsp['ProjectWorkspace']['workspace_id'] )));

									if( isset($area) && !empty($area) ){
										foreach ($area as $ar) {

											$Element = $this->Element->find('all', array('fields' => array('Element.*'), 'conditions' => array('Element.area_id' => $ar['Area']['id'])));

											foreach ($Element as $elm) {

												 $this->Element->deleteAll(array('Element.id' => $elm['Element']['id']));

											}

											$this->Area->deleteAll(array('Area.id' => $ar['Area']['id']));
										}
									}

									$ele_query = "DELETE FROM `elements` WHERE area_id NOT IN (SELECT id FROM areas)";
									$elements = $this->Element->query($ele_query);

									$this->Area->deleteAll(array('Area.workspace_id' => $wsp['ProjectWorkspace']['workspace_id']));

								}
							}
						}
					}

					$this->UserProject->deleteAll(array('UserProject.user_id' => $id, 'UserProject.owner_user' => 1));

					// $this->Session->setFlash(__('User has been deleted successfully.'), 'success');
					die('success');
				} else {
					// $this->Session->setFlash(__('User could not deleted successfully.'), 'error');
				}
			}
			die('error');



		}
	}



	function delete_allusers() {

		if ($this->request->is('ajax')) {

			$this->autoRender = false;
			$this->loadModel('User');

			App::import("Model", "User");
			$this->User = new User();

			$deleteresponse = false;

			$this->loadModel('UserProject');
			$this->loadModel('ProjectWorkspace');
			$this->loadModel('Project');
			$this->loadModel('Area');
			$this->loadModel('Workspace');
			$this->loadModel('Element');
			$this->loadModel('ElementLink');
			$this->loadModel('ElementDocument');
			$this->loadModel('ElementFeedback');
			$this->loadModel('ElementMindmap');
			$this->loadModel('ElementNote');
			$this->loadModel('ElementDecision');
			$this->loadModel('UserSkill');
			$this->loadModel('SkillDetail');
			$this->loadModel('UserLocation');



			if (isset($this->request->data['user_id']) && !empty($this->request->data['user_id'])) {

				foreach($this->request->data['user_id'] as $user_val){
					$id = $user_id = $user_val;
					$this->User->id = $id;

					if ($this->User->delete()) {

						if ($this->live_setting == true) {

							$whatINeed = explode(WEBDOMAIN, $_SERVER['HTTP_HOST']);
							$whatINeed = $whatINeed[0];

							if (PHP_VERSIONS == 5) {

								$mongo = new MongoClient(MONGO_CONNECT);
								$db = $mongo->$whatINeed;
								$collection = $db->users;
								$qry = array("id" => $id);
								$ret = $collection->remove(array('id' => (int) $user_id));

							} else {

								$mongo = new MongoDB\Driver\Manager(MONGO_CONNECT);
								$bulk = new MongoDB\Driver\BulkWrite;
								$bulk->delete(array('id' => (int) $user_id));
								$result = $mongo->executeBulkWrite(MONGO_DATABASE.'.users', $bulk);

							}
						}

							$this->UserSkill->deleteAll(array('UserSkill.user_id' => $id));
							$this->SkillDetail->deleteAll(array('SkillDetail.user_id' => $id));
							$this->UserDetail->deleteAll(array('UserDetail.user_id' => $id));
							$this->UserLocation->deleteAll(array('UserLocation.user_id' => $id));

							//$userasspro = $this->UserProject->find('all', array('fields'=>array('UserProject.*'),'conditions' => array('UserProject.user_id' => $id, 'UserProject.owner_user'=>1)));

							$userasspro = $this->UserProject->find('all', array('fields' => array('UserProject.*'), 'conditions' => array('UserProject.user_id' => $id)));
							if( isset($userasspro) && !empty($userasspro) ){
								foreach ($userasspro as $usp) {

									$this->UserProject->delete($usp['UserProject']['id']);

									$this->Project->delete($usp['UserProject']['project_id']);

									$ProjectWorkspace = $this->ProjectWorkspace->find('all', array('fields' => array('ProjectWorkspace.*'), 'conditions' => array('ProjectWorkspace.project_id' => $usp['UserProject']['project_id'])));

									if( isset($ProjectWorkspace) && !empty($ProjectWorkspace) ){

										foreach ($ProjectWorkspace as $wsp) {

											$this->ProjectWorkspace->delete($wsp['ProjectWorkspace']['id']);

											$this->Workspace->deleteAll(array('Workspace.id' => $wsp['ProjectWorkspace']['workspace_id']));

											$area = $this->Area->find('all', array('fields' => array('Area.*'), 'conditions' => array('Area.workspace_id' => $wsp['ProjectWorkspace']['workspace_id'], 'Area.studio_status !=' => 1)));
											if( isset($area) && !empty($area) ){
												foreach ($area as $ar) {
													// pr($ar);

													$Element = $this->Element->find('all', array('fields' => array('Element.*'), 'conditions' => array('Element.area_id' => $ar['Area']['id'])));

													foreach ($Element as $elm) {

														//pr($elm);

														$this->ElementLink->deleteAll(array('ElementLink.element_id' => $elm['Element']['id']));

														$this->ElementDecision->deleteAll(array('ElementDecision.element_id' => $elm['Element']['id']));

														$this->ElementDocument->deleteAll(array('ElementDocument.element_id' => $elm['Element']['id']));

														$this->ElementFeedback->deleteAll(array('ElementFeedback.element_id' => $elm['Element']['id']));

														$this->ElementNote->deleteAll(array('ElementNote.element_id' => $elm['Element']['id']));

														$this->ElementMindmap->deleteAll(array('ElementMindmap.element_id' => $elm['Element']['id'], 'ElementMindmap.user_id' => $user_id));
													}

													$this->Element->deleteAll(array('Element.area_id' => $ar['Area']['id']));
												}
											}

											$ele_query = "DELETE FROM `elements` WHERE area_id NOT IN (SELECT id FROM areas)";
											$elements = $this->Element->query($ele_query);

											$this->Area->deleteAll(array('Area.workspace_id' => $wsp['ProjectWorkspace']['workspace_id']));
										}
									}
								}
							}

						$this->UserProject->deleteAll(array('UserProject.user_id' => $id, 'UserProject.owner_user' => 1));
						$deleteresponse = true;
					}

				}

				if( $deleteresponse == true ){
					// $this->Session->setFlash(__('User has been deleted successfully.'), 'success');
					echo json_encode($deleteresponse);
					exit();
				} else {
					// $this->Session->setFlash(__('User could not deleted successfully.'), 'error');
					echo json_encode($deleteresponse);
					exit();
				}

			}

		}
	}



	function org_updatestatus() {

		if ($this->request->is('ajax')) {
			$this->autoRender = false;
			App::import("Model", "User");
			$this->User = new User();
			$this->User->id = $this->request->data['id'];
			$this->request->data['User']['id'] = $this->request->data['id'];
			$this->request->data['User']['status'] = $this->request->data['status'];

			if($this->request->data['status'] == 0) {
				$this->request->data['User']['email_notification'] = 0;
				$this->request->data['User']['web_notification'] = 0;
			}
			else {
				$this->request->data['User']['email_notification'] = 1;
				$this->request->data['User']['web_notification'] = 1;
			}
			unset($this->request->data['id']);
			unset($this->request->data['status']);

			if ($this->User->save($this->request->data, false)) {
				// $this->Session->setFlash(__('User status has been updated successfully.'), 'success');
				die('success');
			} else {
				// $this->Session->setFlash(__('User status could not updated successfully.'), 'error');
			}
		}

	}

	function org_admin_status() {

		if ($this->request->is('ajax')) {
			$this->autoRender = false;
			$this->loadModel('UserDetail');
			$this->loadModel('User');

			//$this->request->data;

			if ($this->UserDetail->save($this->request->data, false)) {
				//$this->User->updateAll( array('UserDetail.administrator' => $this->request->data['UserDetail']['administrator']),array('User.id' => $this->request->data['UserDetail']['user_id']));
				// $this->Session->setFlash(__('User administrator status has been updated successfully.'), 'success');
				die('success');
			} else {
				// $this->Session->setFlash(__('User administrator status could not updated success fully.'), 'error');
			}
		}

	}

	function org_project_status() {

		if ($this->request->is('ajax')) {
			$this->autoRender = false;
			$this->loadModel('UserDetail');
			$this->loadModel('User');

			if ($this->UserDetail->save($this->request->data, false)) {
				// $this->Session->setFlash(__('User projects status has been updated successfully.'), 'success');
				die('success');
			} else {
				// $this->Session->setFlash(__('User projects status could not updated success fully.'), 'error');
			}
		}

	}

	public function admin_orgusers($id = null) {

		$orConditions = array();
		$andConditions = array();
		$finalConditions = array();

		$in = 0;
		$per_page_show = $this->Session->read('user.per_page_show');
		if (empty($per_page_show)) {
			$per_page_show = ADMIN_PAGING;
		}

		if (isset($this->data['User']['keyword'])) {
			$keyword = trim($this->data['User']['keyword']);
		} else {
			$keyword = $this->Session->read('user.keyword');
		}

		if (isset($this->data['User']['emails'])) {
			$email = trim($this->data['User']['emails']);
		} else {
			$email = $this->Session->read('user.email');
		}

		if (isset($keyword)) {
			$this->Session->write('user.keyword', $keyword);
			$keywords = explode(" ", $keyword);
			if (isset($keywords[0]) && !empty($keywords[0]) && count($keywords) < 2) {
				$keyword = $keywords[0];
				$in = 1;
				$orConditions = array('OR' => array(
					'User.email LIKE' => '%' . $keyword . '%',
					'UserDetail.first_name LIKE' => '%' . $keyword . '%',
					'UserDetail.last_name LIKE' => '%' . $keyword . '%',
				));
			} else if (!empty($keywords) && count($keywords) > 1) {
				$first_name = $keywords[0];
				$last_name = $keywords[1];
				$in = 1;
				$andConditions = array('AND' => array(
					'UserDetail.first_name LIKE' => '%' . $first_name . '%',
					'UserDetail.last_name LIKE' => '%' . $last_name . '%',
				));
			}
		}

		if (isset($email) && !empty($email)) {
			$this->Session->write('user.email', $email);
			$in = 1;
			$andConditions = array('AND' => array(
				'User.email LIKE' => '%' . $email . '%',
			));

		}

		if (!empty($id)) {
			$andConditions2 = array(
				'UserDetail.org_id' => $id,
			);
			$andConditions = array_merge($andConditions, $andConditions2);
		}

		if (isset($this->data['User']['status'])) {
			$status = $this->data['User']['status'];
		} else {
			$status = $this->Session->read('user.status');
		}

		if (isset($status)) {
			$this->Session->write('user.status', $status);
			if ($status != '') {
				$in = 1;
				$andConditions = array_merge($andConditions, array('User.status' => $status));
			}
		}

		if (isset($this->data['UserDetail']['country_id']) && !empty($this->data['UserDetail']['country_id'])) {
			$county = trim($this->data['UserDetail']['country_id']);
			$this->Session->write('user.country', $county);
			$in = 1;
			$andConditions1 = array(
				'UserDetail.country_id LIKE' => '%' . $county . '%',
			);

			$andConditions = array_merge($andConditions, $andConditions1);
		}

		if (isset($this->data['User']['per_page_show']) && !empty($this->data['User']['per_page_show'])) {
			$per_page_show = $this->data['User']['per_page_show'];
		}

		if (!empty($orConditions)) {
			$finalConditions = array_merge($finalConditions, $orConditions);
		}
		if (!empty($andConditions)) {
			$finalConditions = array_merge($finalConditions, array('AND' => $andConditions));
		}

		//pr($finalConditions); die;

		$this->set('title_for_layout', __('View Organization User', true));
		$this->Session->write('user.per_page_show', $per_page_show);

		//	pr( $this->User); die;

		$this->paginate = array('conditions' => $finalConditions, "limit" => $per_page_show, "order" => "User.created DESC");

		$count = $this->User->find('count', array('conditions' => $finalConditions));

		$this->set('count', $count);
		$this->User->recursive = 3;
		$this->set('users', $this->paginate('User'));
		$this->set('in', $in);
	}

	public function admin_org_user_delete($id = null) {

		if (isset($this->data['id']) && !empty($this->data['id'])) {

			//if (isset($id)) {

			$id = $this->data['id'];

			$this->User->id = $id;

			if (!$this->User->exists()) {
				throw new NotFoundException(__('Invalid User'), 'error');
			}

			if ($this->User->delete()) {

				//if ($id) {

				$this->loadModel('UserProject');
				$this->loadModel('ProjectWorkspace');
				$this->loadModel('Project');
				$this->loadModel('Area');
				$this->loadModel('Workspace');
				$this->loadModel('Element');
				$this->loadModel('ElementLink');
				$this->loadModel('ElementDocument');
				$this->loadModel('ElementFeedback');
				$this->loadModel('ElementMindmap');
				$this->loadModel('ElementNote');
				$this->loadModel('ElementDecision');
				$this->loadModel('UserSkill');
				$this->loadModel('SkillDetail');

				$this->UserDetail->deleteAll(array('UserDetail.user_id' => $id));
				$this->SkillDetail->deleteAll(array('SkillDetail.user_id' => $id));

				$this->UserSkill->deleteAll(array('UserSkill.user_id' => $id));
				//$this->UserPlan->deleteAll(array('UserPlan.user_id' => $id));

				//$userasspro = $this->UserProject->find('all', array('fields'=>array('UserProject.*'),'conditions' => array('UserProject.user_id' => $id, 'UserProject.owner_user'=>1)));

				$userasspro = $this->UserProject->find('all', array('fields' => array('UserProject.*'), 'conditions' => array('UserProject.user_id' => $id)));

				foreach ($userasspro as $usp) {

					$this->UserProject->delete($usp['UserProject']['id']);

					$this->Project->delete($usp['UserProject']['project_id']);

					$ProjectWorkspace = $this->ProjectWorkspace->find('all', array('fields' => array('ProjectWorkspace.*'), 'conditions' => array('ProjectWorkspace.project_id' => $usp['UserProject']['project_id'])));

					foreach ($ProjectWorkspace as $wsp) {

						$this->ProjectWorkspace->delete($wsp['ProjectWorkspace']['id']);

						$this->Workspace->deleteAll(array('Workspace.id' => $wsp['ProjectWorkspace']['workspace_id']));

						$area = $this->Area->find('all', array('fields' => array('Area.*'), 'conditions' => array('Area.workspace_id' => $wsp['ProjectWorkspace']['workspace_id'], 'Area.studio_status !=' => 1)));

						foreach ($area as $ar) {
							// pr($ar);

							$Element = $this->Element->find('all', array('fields' => array('Element.*'), 'conditions' => array('Element.area_id' => $ar['Area']['id'])));

							foreach ($Element as $elm) {

								//pr($elm);

								$this->ElementLink->deleteAll(array('ElementLink.element_id' => $elm['Element']['id']));

								$this->ElementDecision->deleteAll(array('ElementDecision.element_id' => $elm['Element']['id']));

								$this->ElementDocument->deleteAll(array('ElementDocument.element_id' => $elm['Element']['id']));

								$this->ElementFeedback->deleteAll(array('ElementFeedback.element_id' => $elm['Element']['id']));

								$this->ElementNote->deleteAll(array('ElementNote.element_id' => $elm['Element']['id']));

								$this->ElementMindmap->deleteAll(array('ElementMindmap.element_id' => $elm['Element']['id'], 'ElementMindmap.user_id' => $id));
							}

							$this->Element->deleteAll(array('Element.area_id' => $ar['Area']['id']));
						}

						$this->Area->deleteAll(array('Area.workspace_id' => $wsp['ProjectWorkspace']['workspace_id']));
					}
				}

				$this->UserProject->deleteAll(array('UserProject.user_id' => $id, 'UserProject.owner_user' => 1));

				//$this->UserTransctionDetail->deleteAll(array('UserTransctionDetail.user_id' => $id));

				$this->Session->setFlash(__('User has been deleted successfully.'), 'success');
				die('success');
			} else {
				$this->Session->setFlash(__('User could not deleted successfully.'), 'error');
			}
		}
		die('error');
	}

	/**
	 * admin_edit method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function admin_org_user_edit($id = null, $insid = null) {
		$this->set('title_for_layout', __('Edit Organization User', true));
		$this->User->id = $id;

		if (!$this->User->exists()) {
			$this->Session->setFlash(__('Invalid User.'), 'error');
			$this->redirect(array('action' => 'index'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			//pr($this->request->data);die;
			$organisationsid = $this->request->data['User']['org_id'];
			if (isset($this->request->data['User']['status']) && $this->request->data['User']['status'] == "on") {
				$this->request->data['User']['status'] = "1";
			} else {
				$this->request->data['User']['status'] = "0";
			}

			if ($this->User->saveAssociated($this->request->data)) {
				$this->Session->setFlash(__('The user has been updated successfully.'), 'success');
				$this->redirect(array('action' => 'orgusers', $organisationsid));

			} else {

				$datas = $this->User->findById($id);
				$this->request->data['UserDetail']['profile_pic'] = $datas['UserDetail']['profile_pic'];
			}
		} else {
			$this->request->data = $this->User->read(null, $id);
			//pr($this->request->data);die;
			if (isset($this->request->data['User']['password'])) {
				unset($this->request->data['User']['password']);
			}
		}
	}

	function admin_org_user_resetfilter($id = null) {
		$this->Session->write('user.keyword', '');
		$this->Session->write('user.email', '');
		$this->redirect(array('action' => 'orgusers', $id));
	}

	/**
	 * update_status method
	 *
	 * @return void
	 */
	public function admin_org_user_updatestatus() {
		if ($this->request->is('ajax')) {
			$this->autoRender = false;
			$this->loadModel('User');
			$this->request->data['User'] = $this->request->data;

			if ($this->User->save($this->request->data)) {
				$this->Session->setFlash(__('User status has been updated successfully.'), 'success');
				die('success');
			} else {
				$this->Session->setFlash(__('User status could not updated successfully.'), 'error');
			}
		}
		die('error');
	}

	public function password_policy() {

		$this->layout = 'inner';
		$this->set('title_for_layout', __('Security', true));
		$viewData['page_heading'] = 'Security';
		$viewData['page_subheading'] = 'View and edit organization security settings';

		$this->set('viewData', $viewData);

		if ($this->Session->read('Auth.User.role_id') == 3) {
			$id = $this->Session->read('Auth.User.id');
			$this->request->data['OrgPassPolicy']['org_id'] = $this->Session->read('Auth.User.id');
		}else{

			if(LOCALIP != $_SERVER['SERVER_ADDR']){
				$this->redirect(array('controller' => 'projects', 'action' => 'lists'));
			}
		}

		/* else if ($this->Session->read('Auth.User.UserDetail.administrator') == 1 && $this->Session->read('Auth.User.UserDetail.org_id') != 0) {
			$this->redirect(array('controller' => 'projects', 'action' => 'lists'));
			$id = $this->Session->read('Auth.User.UserDetail.org_id');
			$this->request->data['OrgPassPolicy']['org_id'] = $id;
		} */

		//if( $_SERVER['SERVER_NAME'] == 'dotsquares.ideascast.com' ||  $_SERVER['SERVER_NAME'] == '192.168.8.29' ) {

		if ($this->request->is('post') || $this->request->is('put')) {
			if (isset($this->request->data['OrgPassPolicy']['force_change_pass']) && $this->request->data['OrgPassPolicy']['force_change_pass'] == 'on') {
				$this->request->data['OrgPassPolicy']['force_change_pass'] = 1;
			} else {
				$this->request->data['OrgPassPolicy']['force_change_pass'] = 0;
			}

			if (isset($this->request->data['OrgPassPolicy']['numeric_char']) && $this->request->data['OrgPassPolicy']['numeric_char'] == 'on') {
				$this->request->data['OrgPassPolicy']['numeric_char'] = 1;
			} else {
				$this->request->data['OrgPassPolicy']['numeric_char'] = 0;
			}
			if (isset($this->request->data['OrgPassPolicy']['alph_char']) && $this->request->data['OrgPassPolicy']['alph_char'] == 'on') {
				$this->request->data['OrgPassPolicy']['alph_char'] = 1;
			} else {
				$this->request->data['OrgPassPolicy']['alph_char'] = 0;
			}

			if (isset($this->request->data['OrgPassPolicy']['special_char']) && $this->request->data['OrgPassPolicy']['special_char'] == 'on') {
				$this->request->data['OrgPassPolicy']['special_char'] = 1;
			} else {
				$this->request->data['OrgPassPolicy']['special_char'] = 0;
			}

			if (isset($this->request->data['OrgPassPolicy']['caps_char']) && $this->request->data['OrgPassPolicy']['caps_char'] == 'on') {
				$this->request->data['OrgPassPolicy']['caps_char'] = 1;
			} else {
				$this->request->data['OrgPassPolicy']['caps_char'] = 0;
			}

			$this->request->data['OrgPassPolicy']['updated_by'] = $this->Session->read('Auth.User.id');

			// pr($this->request->data['OrgPassPolicy']); die;

			if ($this->OrgPassPolicy->save($this->request->data)) {

				if (isset($this->request->data['OrgPassPolicy']['force_change_pass'])) {

					$this->User->unbindModel(
						array('hasOne' => array('UserDetail', 'UserInstitution', 'OrganisationUser'),
							'hasMany' => array('ProjectPermission', 'WorkspacePermission', 'ElementPermission', 'UserProject', 'UserSetting', 'UserPassword'),
							'hasAndBelongsToMany' => array('Skill'),
						), true);

					$this->User->updateAll(array('change_password_sts' => $this->request->data['OrgPassPolicy']['force_change_pass']), array('1' => '1'));

				}
				$this->Session->setFlash(__('Password policy has been updated successfully.'), 'success');
				$this->request->data = $this->OrgPassPolicy->find('first', array('conditions' => array('OrgPassPolicy.org_id' => $id)));
				$this->redirect(array('action' => 'password_policy'));

			}

		} else {

			if (isset($id) && !empty($id)) {
				$this->request->data = $this->OrgPassPolicy->find('first', array('conditions' => array('OrgPassPolicy.org_id' => $id)));
			}

		}
		//}


		$viewData['crumb'] = [
			'last' => [
				'data' => [
					'title' => "Security",
					'data-original-title' => "Security",
				],
			],
		];
		$this->set('crumb',$viewData['crumb']);

		//$this->set('crumb',$crumb);

	}

	function admin_import_database() {

		if ($this->request->isAjax()) {
			$this->autoRender = false;
			$this->layout = 'ajax';
			$response = ['success' => false, 'content' => ''];

			//pr($this->request->data); die;

			if (($this->request->is('post') || $this->request->is('put'))) {

				if( !empty($this->request->data['orguser']) ){

					$domainDetail = $this->OrgSetting->findById($this->request->data['orguser']);

					$dbuser = $domainDetail['OrgSetting']['dbuser'];
					$dbpass = $domainDetail['OrgSetting']['dbpass'];
					$domain_id = $domainDetail['OrgSetting']['id'];

					$message = $this->import_database(DOMAINPREFIX . $this->request->data['dname'],$dbuser,$dbpass,$domain_id);

					/*
						import_procedure_drop_index
						drop_create_index
						create_foreign_keys
					*/

					//pr($message); die;
					if ($message) {

						$this->request->data['OrgSetting']['id'] = $this->request->data['orguser'];
						$this->request->data['OrgSetting']['db_setup'] = 1;
						$this->OrgSetting->save($this->request->data);
						$this->create_subdomain_file($domain_id);
						$this->create_subdomain_temp_file($domain_id);

						$importProcedure = true;
						$dropCreateIndex = true;
						$createForegionKey = true;

						//procedure
						$procedure = $this->import_procedure_drop_index(DOMAINPREFIX . $this->request->data['dname'],$dbuser,$dbpass,$domain_id);

						if($procedure){

							//drop create index
							$dropcreateindex = $this->drop_create_index(DOMAINPREFIX . $this->request->data['dname'],$dbuser,$dbpass,$domain_id);

							if($dropcreateindex){

								//foreign functionality is stopped due to DELETE functionality by pawan
								//create foregion key
								/* $createforegin = $this->create_foreign_keys(DOMAINPREFIX . $this->request->data['dname'],$dbuser,$dbpass,$domain_id);
								if(!$createforegin){
									$createForegionKey = false;
								} */
							}

							if(!$dropcreateindex){
								$dropCreateIndex = false;
							}

						}

						if(!$procedure){
							$importProcedure = false;
						}

						if( !$importProcedure || $importProcedure == false ){

							$this->Session->setFlash(__("Database has been setup successfully for this Domain but Procedure given error"), 'error');
							$response['success'] = false;

						} else if( !$dropCreateIndex || $dropCreateIndex == false ){

							$this->Session->setFlash(__("Database has been setup successfully for this Domain but drop and create index given error"), 'error');
							$response['success'] = false;

						} else if( !$createForegionKey || $createForegionKey == false ){

							$this->Session->setFlash(__("Database has been setup successfully for this Domain but create foregion key given error"), 'error');
							$response['success'] = false;

						} else {
							$this->Session->setFlash(__("Database has been setup successfully for this Domain."), 'success');
							$response['success'] = true;
						}


					} else {
						$this->Session->setFlash(__('Database has not been imported successfully, please try again.'), 'error');
						$response['success'] = false;
					}

					echo json_encode($response);
					exit();
				}

			}

		}
	}

	function admin_drop_tables_old($org_dbid = null) {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$this->autoRender = false;
			$response = ['success' => false, 'content' => ''];

			if (($this->request->is('post') || $this->request->is('put'))) {

				if (!empty($this->request->data['dbid'])) {
					$org_dbid = $this->request->data['dbid'];
				}

				$this->loadModel('OrgSettings');
				$orgdbdetails = $this->OrgSettings->find('first', array('conditions' => array('OrgSettings.org_id' => $org_dbid)));

				$dydatabase = [];

				$this->conn = mysql_pconnect(root_host, $orgdbdetails['OrgSettings']['dbuser'], $orgdbdetails['OrgSettings']['dbpass']);
				$db_list = mysql_list_dbs($this->conn);
				while ($row = mysql_fetch_object($db_list)) {
					if (!in_array($row->Database, $this->protectedDatabases)) {
						if ($row->Database == $orgdbdetails['OrgSettings']['dbname']) {
							$dydatabase[] = $row->Database;
						}
					}
				}
				$sql = "SHOW TABLES FROM $dydatabase[0]";
				$result = mysql_query($sql);
				$table = array();
				$dsql = '';
				while ($row = @mysql_fetch_row($result)) {
					$table[] = $row[0];

				}
				$table_tot = ( isset($table) && !empty($table) ) ? count($table) : 0;
				if ( $table_tot > 0) {

					$html = '';
					$query = '';
					$error = false;
					foreach ($table as $viewTable) {

						$dsql = "DROP TABLE IF EXISTS $viewTable";
						mysql_select_db($dydatabase[0]);
						$query = mysql_query($dsql);
						if ($query) {

							$dsqlv = "DROP VIEW IF EXISTS $viewTable";
							mysql_select_db($dydatabase[0]);
							$queryv = mysql_query($dsqlv);

							$html .= $viewTable . " is deleted<br>";
							$error = true;
						}
					}

					if ($error) {

						if (isset($this->request->data['orguid']) && $this->request->data['orguid'] > 0) {

							if ($this->live_setting == true) {

								$mongodb = $orgdbdetails['OrgSettings']['subdomain'];

								if( PHP_VERSIONS == 5 ){
									$mongo = new MongoClient(MONGO_CONNECT);
									$db = $mongo->$domain;
									$response = $db->drop();
								} else {
									// domain database delete from mongo
									$mongo = new MongoDB\Driver\Manager(MONGO_CONNECT);
									$listcollections = new MongoDB\Driver\Command(["listCollections" => 1]);
									$result = $mongo->executeCommand($mongodb, $listcollections);

									//if( $result->toArray() !== null ){
										$collections = $result->toArray();
										foreach ($collections as $collection) {
											if(isset($collection) && !empty(($collection)) ){
												$mongo->executeCommand($mongodb, new \MongoDB\Driver\Command(["drop" => $collection->name]));
											}
										}
									//}
								}
							}



							$this->request->data['OrgSetting']['id'] = $this->request->data['orguid'];
							$this->request->data['OrgSetting']['db_setup'] = 0;
							$this->request->data['OrgSetting']['apisdk_status'] = 0;
							$this->OrgSetting->save($this->request->data);

						}
						//$dydatabase[0]
						$this->Session->setFlash(__("Database tables have been deleted successfully. Now you can setup database again."), 'success');
						$response['success'] = true;

					} else {
						$this->Session->setFlash(__("Database tables did not delete, please try again."), 'error');
						$response['success'] = false;
					}

				} else {
					$this->Session->setFlash(__("Database tables does not exists."), 'error');
					$response['success'] = false;
				}

			}
			echo json_encode($response);
			exit();
		}
	}


	function admin_drop_tables($org_dbid = null) {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$this->autoRender = false;
			$response = ['success' => false, 'content' => ''];

			if (($this->request->is('post') || $this->request->is('put'))) {

				if (!empty($this->request->data['dbid'])) {
					$org_dbid = $this->request->data['dbid'];
				}

				$this->loadModel('OrgSettings');
				$orgdbdetails = $this->OrgSettings->find('first', array('conditions' => array('OrgSettings.org_id' => $org_dbid)));

				$dydatabase = [];

				//$this->conn = mysqli_connect('localhost', $orgdbdetails['OrgSettings']['dbuser'], $orgdbdetails['OrgSettings']['dbpass']);


				if( $_SERVER['SERVER_ADDR'] ==  OPUSVIEW || $_SERVER['SERVER_ADDR'] ==  OPUSVIEW_DEV ||  $_SERVER['SERVER_ADDR']  ==OPUSVIEW_CLOUD )
				{
					$mysqlUserName = root_dbuser;
					$mysqlPassword = root_dbpass;
				} else {
					$mysqlUserName = $orgdbdetails['OrgSettings']['dbuser'];
					$mysqlPassword = $orgdbdetails['OrgSettings']['dbpass'];
				}

				$this->conn = mysqli_connect(WEBDOMAIN_HOST, $mysqlUserName, $mysqlPassword, $orgdbdetails['OrgSettings']['dbname']);

				if (mysqli_connect_errno())
				  {
					echo "Failed to connect to MySQL: " . mysqli_connect_error();
				  }

				//$db_list = mysql_list_dbs($this->conn);
				/* $db_list = mysqli_select_db($this->conn);
				pr($db_list); die;
				while ($row = mysqli_fetch_object($db_list)) {
					if (!in_array($row->Database, $this->protectedDatabases)) {
						if ($row->Database == $orgdbdetails['OrgSettings']['dbname']) {
							$dydatabase[] = $row->Database;
						}
					}
				} */

				$dydatabase[0] = $orgdbdetails['OrgSettings']['dbname'];
				$sql = "SHOW TABLES FROM $dydatabase[0]";
				$result = mysqli_query($this->conn,$sql);
				$table = array();
				$dsql = '';
				while ($row = @mysqli_fetch_row($result)) {
					$table[] = $row[0];

				}
				$table_tot = ( isset($table) && !empty($table) ) ? count($table) : 0;
				if ($table_tot > 0) {

					$html = '';
					$query = '';
					$error = false;
					foreach ($table as $viewTable) {

						//$dsql = "DROP TABLE IF EXISTS $viewTable";
						//mysqli_select_db($this->conn,$dydatabase[0]);

						$dsql_1 = "SET foreign_key_checks = 0";
						$dsql_2 = "DROP TABLE IF EXISTS $viewTable";
						$dsql_3 = "SET foreign_key_checks = 1;";

						$query1 = mysqli_query($this->conn,$dsql_1);
						$query = mysqli_query($this->conn,$dsql_2);
						if ($query && $query1) {

							$dsqlv = "DROP VIEW IF EXISTS $viewTable";
							//mysqli_select_db($this->conn,$dydatabase[0]);
							$queryv = mysqli_query($this->conn,$dsqlv);
							$query = mysqli_query($this->conn,$dsql_3);

							$html .= $viewTable . " is deleted<br>";
							$error = true;
						}
					}

					if ($error) {

						if (isset($this->request->data['orguid']) && $this->request->data['orguid'] > 0) {

							if ($this->live_setting == true) {

								$mongodb = $orgdbdetails['OrgSettings']['subdomain'];
								$domain = $orgdbdetails['OrgSettings']['subdomain'];

								if( PHP_VERSIONS == 5 ){
									$mongo = new MongoClient(MONGO_CONNECT);
									$db = $mongo->$domain;
									$response = $db->drop();
								} else {
									// domain database delete from mongo
									$mongo = new MongoDB\Driver\Manager(MONGO_CONNECT);
									$listcollections = new MongoDB\Driver\Command(["listCollections" => 1]);
									$result = $mongo->executeCommand($mongodb, $listcollections);

									//if( $result->toArray() !== null ){
										$collections = $result->toArray();
										foreach ($collections as $collection) {
											if(isset($collection) && !empty(($collection)) ){
												$mongo->executeCommand($mongodb, new \MongoDB\Driver\Command(["drop" => $collection->name]));
											}
										}
									//}
								}
							}



							$this->request->data['OrgSetting']['id'] = $this->request->data['orguid'];
							$this->request->data['OrgSetting']['db_setup'] = 0;
							$this->request->data['OrgSetting']['apisdk_status'] = 0;
							$this->OrgSetting->save($this->request->data);

						}
						//$dydatabase[0]
						$this->Session->setFlash(__("Database tables have been deleted successfully. Now you can setup database again."), 'success');
						$response['success'] = true;

					} else {
						$this->Session->setFlash(__("Database tables did not delete, please try again."), 'error');
						$response['success'] = false;
					}

				} else {
					$this->Session->setFlash(__("Database tables does not exists."), 'error');
					$response['success'] = false;
				}

			}
			echo json_encode($response);
			exit();
		}
	}


	public function __sendEmailConfirm($useData = null, $userPassword = null, $siteadmin = null, $loginurl = null,$clientAdminEmail = null) {

		if( empty($useData) ) {
			debug(__METHOD__ . " failed to retrieve User data for user");
			return false;
		}

		$fullname = $useData['UserDetail']['first_name'] . ' ' . $useData['UserDetail']['last_name'];
		$userEmail = $useData['User']['email'];

		$email = new CakeEmail();
		$email->config('Smtp');
		$email->from(array(ADMIN_FROM_EMAIL => SITENAME));
		$email->to($useData['User']['email']);
		$email->subject(SITENAME . ': Login details');
		$email->template('user_login_details');
		$email->emailFormat('html');
		$email->viewVars(array('fullname' => $fullname, 'userPassword' => $userPassword, 'siteadmin' => $siteadmin, 'userEmail' => $userEmail, 'siteurl' => $_SERVER['HTTP_HOST'],'clientAdminEmail'=>$clientAdminEmail));
		return $email->send();

	}

	public function _sendOrgEmail($userid = null, $domainName = null) {

		if ( !isset($userid) && empty($userid) ) {
			debug(__METHOD__ . " failed to retrieve User data for user");
			return false;
		}

		$useData = $this->User->find('first', array('conditions' => array('User.id' => $userid)));
		$useDetails = $this->UserDetail->find('first', array('conditions' => array('UserDetail.user_id' => $userid)));


		$sqlN = "select AES_DECRYPT(org_password, 'secret') as org_password from user_details WHERE user_details.user_id =".$userid;

		$dd = $this->UserDetail->query($sqlN);
		//pr($dd);


		$orgfullname = $useDetails['UserDetail']['first_name'] . ' ' . $useDetails['UserDetail']['last_name'];
		$orgName = $useDetails['UserDetail']['org_name'];
		$orgPassword = $dd[0][0]['org_password'];
		$orgEmail = $useData['User']['email'];
		$siteurl = 'https://' . $domainName . WEBDOMAIN;

		$email = new CakeEmail();
		$email->config('Smtp');
		$email->from(array(ADMIN_FROM_EMAIL => SITENAME));
		$email->to($orgEmail);
		//$email->subject(SITENAME . ': Organization Login Details');
		$email->subject(SITENAME . ': System administrator account');
		$email->template('org_login_details');
		$email->emailFormat('html');

		$email->viewVars(array('orgfullname' => $orgfullname, 'orgName' => $orgName, 'orgPassword' => $orgPassword, 'orgEmail' => $orgEmail, 'siteurl' => $siteurl));

		return $email->send();
	}

	public function admin_list_domain($user_id = null) {

		$in = 0;
		$per_page_show = $this->Session->read('orgsetting.per_page_show');
		if (empty($per_page_show)) {
			$per_page_show = ADMIN_PAGING;
		}

		if (isset($user_id)) {

			$orConditions = array();
			$andConditions = array();
			$finalConditions = array();

			$in = 0;
			$per_page_show = $this->Session->read('orgsetting.per_page_show');
			if (empty($per_page_show)) {
				$per_page_show = ADMIN_PAGING;
			}

			/*============== Updated by Pawan =============================*/
			//if( $_SERVER['REMOTE_ADDR'] == '111.93.41.194' ) {

			$org_domain = $this->OrgSetting->find('all', array('conditions' => array('OrgSetting.user_id' => $user_id)));

			if (isset($org_domain) && !empty($org_domain)) {
				foreach ($org_domain as $listdomain) {

					$domianName = $listdomain['OrgSetting']['subdomain'];
					$organisationuser = $this->OrganisationUser->find('first', array('conditions' => array('OrganisationUser.domain_name' => $domianName)));
					if (empty($listdomain['OrgSetting']['org_id']) || $listdomain['OrgSetting']['org_id'] == NULL) {
						$sql = "UPDATE org_settings set org_id = " . $organisationuser['OrganisationUser']['id'] . " WHERE id = " . $listdomain['OrgSetting']['id'] . " ";
						$data = $this->OrgSetting->query($sql);

					}
				}
			}

			//}
			//===============================================================

			//pr($this->data['OrgSetting']);

			if (isset($this->data['OrgSetting']['keyword'])) {
				$keyword = trim($this->data['OrgSetting']['keyword']);
			} else {
				$keyword = $this->Session->read('orgsetting.keyword');
			}

			if (isset($keyword)) {
				$this->Session->write('orgsetting.keyword', $keyword);

				if (isset($keyword) && !empty($keyword)) {
					$keyword = $keyword;
					$in = 1;
					$orConditions = array('OR' => array(
						'OrgSetting.subdomain LIKE' => '%' . $keyword . '%',
					));
				}
			}

			if (isset($this->data['OrgSetting']['status'])) {
				$status = $this->data['OrgSetting']['status'];
			} else {
				$status = $this->Session->read('orgsetting.status');
			}

			if (isset($status)) {
				$this->Session->write('orgsetting.status', $status);
				if ($status != '') {
					$in = 1;
					$andConditions = array_merge($andConditions, array('OrgSetting.status' => $status));
				}
			}

			if (isset($this->data['OrgSetting']['per_page_show']) && !empty($this->data['OrgSetting']['per_page_show'])) {
				$per_page_show = $this->data['OrgSetting']['per_page_show'];
			}

			if (!empty($orConditions)) {
				$finalConditions = array_merge($finalConditions, $orConditions);
			}

			if (!empty($andConditions)) {
				$finalConditions = array_merge($finalConditions, array('AND' => $andConditions));
			}

			$finalUserConditions = array('OrgSetting.user_id' => $user_id);
			$finalConditions = array_merge($finalConditions, $finalUserConditions);

			//pr($finalConditions);die;
			$count = $this->OrgSetting->find('count', array('conditions' => $finalConditions));
			$organisationname = $this->UserDetail->find('first', array('conditions' => array('UserDetail.user_id' => $user_id)));

			$this->paginate = array('conditions' => $finalConditions, "limit" => $per_page_show, "order" => "OrgSetting.prmry_sts desc");


			$this->set('count', $count);
			$this->set('organisationname', $organisationname['UserDetail']['org_name']);
			$this->set('title_for_layout', __('All Domains', true));
			$this->Session->write('OrgSetting.per_page_show', $per_page_show);
			$this->OrgSetting->recursive = 2;
			if(isset($this->request->params['named']['page']) && !empty($this->request->params['named']['page'])){
				$curentpage = $this->request->params['named']['page'];

				$total_pages = $count  / $per_page_show ;
				$total_pages = ceil($total_pages);
				if($total_pages < $curentpage){

					 $this->redirect(array('action' => 'list_domain', $user_id));
				}

			}
			$this->set('listdomain', $this->paginate('OrgSetting'));
			$this->set('in', $in);

		}

	}

	public function admin_domain_statusupdate() {

		if ($this->request->is('ajax')) {
			$this->autoRender = false;
			$this->loadModel('OrgSetting');

			//pr($this->request->data); die;

			$this->request->data['OrgSetting'] = $this->request->data;
			if ($this->OrgSetting->save($this->request->data, false)) {
				// Will update domain DB Config file
				$this->create_subdomain_file($this->request->data['id']);
				$this->create_subdomain_temp_file($this->request->data['id']);
				$this->Session->setFlash(__('Domain status has been updated successfully.'), 'success');
				die('success');
			} else {
				$this->Session->setFlash(__('Domain status could not updated successfully.'), 'error');
			}
		}
		die('error');
	}

	public function admin_domain_primaryupdate() {

		if ($this->request->is('ajax')) {
			$this->autoRender = false;
			$this->loadModel('OrgSetting');

			$this->request->data['OrgSetting'] = $this->request->data;

			//	pr($this->request->data); die;

			if ($this->OrgSetting->save($this->request->data, false)) {

				$data = $this->OrgSetting->updateAll(array('OrgSetting.prmry_sts' => 0), array('OrgSetting.id !=' => $this->request->data['OrgSetting']['id'], 'OrgSetting.user_id' => $this->request->data['OrgSetting']['org_id']));

				$this->Session->setFlash(__('Primary domain status has been updated successfully.'), 'success');
				die('success');
			} else {
				$this->Session->setFlash(__('Primary domain status could not updated successfully.'), 'error');
			}
		}
		die('error');
	}

	/**
	 * admin_domain_edit method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function admin_edit_domain($id = null, $user_id = null) {
		$this->set('title_for_layout', __('Edit Domain', true));
		$this->OrgSetting->id = $id;

		$ideaVersion = $this->Setting->find('first', array('conditions' => array('Setting.id' => 1)));
		$ideasCastVersion = '';
		$this->set('ideasCastVersion', '1.0');
		if (isset($ideaVersion['Setting']['idesversion']) && !empty($ideaVersion['Setting']['idesversion'])) {
			$this->set('ideasCastVersion', $ideaVersion['Setting']['idesversion']);
			$ideasCastVersion = $ideaVersion['Setting']['idesversion'];
		}

		if (!empty($user_id)) {

			$organisationname = $this->UserDetail->find('first', array('conditions' => array('UserDetail.user_id' => $user_id)));
			$this->set('organisationname', $organisationname['UserDetail']['org_name']);

		} else {

			$user_id = $this->request->data['OrgSetting']['user_id'];
			$organisationname = $this->UserDetail->find('first', array('conditions' => array('UserDetail.user_id' => $user_id)));
			$this->set('organisationname', $organisationname['UserDetail']['org_name']);

		}

		if (!$this->OrgSetting->exists()) {
			$this->Session->setFlash(__('Invalid Domain.'), 'error');
			$this->redirect(array('action' => 'list_domain'));
		}

		if ($this->request->is('post') || $this->request->is('put')) {

			//==========================================================================

			//pr($this->request->data['OrgSetting']['start_date']);
			$sdDate = $edDate = null;
			if (isset($this->request->data['OrgSetting']['start_date']) && isset($this->request->data['OrgSetting']['end_date'])) {
				$sdDate = strtotime($this->request->data['OrgSetting']['start_date']);
				$edDate = strtotime($this->request->data['OrgSetting']['end_date']);

				//if( $sdDate > $edDate )

			}

			$start_date = explode('/', $this->request->data['OrgSetting']['start_date']);
			$end_date = explode('/', $this->request->data['OrgSetting']['end_date']);

			$this->request->data['OrgSetting']['start_date'] = $start_date[2] . '-' . $start_date[1] . '-' . $start_date[0];
			$this->request->data['OrgSetting']['end_date'] = $end_date[2] . '-' . $end_date[1] . '-' . $end_date[0];

			$orgdetails = $this->OrgSetting->find('first', array('conditions' => array('OrgSetting.id' => $id)));


			if( $_SERVER['SERVER_ADDR'] ==  OPUSVIEW || $_SERVER['SERVER_ADDR'] ==  OPUSVIEW_DEV ||  $_SERVER['SERVER_ADDR']  ==OPUSVIEW_CLOUD )
			{
				$mysqlUserName = root_dbuser;
				$mysqlPassword = root_dbpass;
			} else {
				$mysqlUserName = $orgdetails['OrgSetting']['dbuser'];
				$mysqlPassword = $orgdetails['OrgSetting']['dbpass'];
			}

			$this->conn = mysqli_connect(root_host, $mysqlUserName, $mysqlPassword, $orgdetails['OrgSetting']['dbname']);
			if (mysqli_connect_errno()) {
				echo "Failed to connect to MySQL: " . mysqli_connect_error();
			}
			//=============== Get Database Size =====================
			//$dbselect = mysql_select_db($orgdetails['OrgSetting']['dbname'], $this->conn);
			$result1 = mysqli_query($this->conn, "SHOW TABLE STATUS");
			$dbsize = 0;
			while ($row = mysqli_fetch_array($result1)) {
				$dbsize += $row["Data_length"] + $row["Index_length"];
			}
			$currentDatabasesize = number_format(($dbsize / 1024 / 1024 / 1024), 3); // Database size in GB
			//=======================================================

			$userDetails = mysqli_query($this->conn,"SELECT * FROM users WHERE role_id = 2 ");
			$useslincense = 0;
			if (@mysqli_num_rows($userDetails) > 0) {
				$useslincense = mysqli_num_rows($userDetails);
			}

			if (isset($orgdetails) && !empty($orgdetails) && count($orgdetails) > 0) {

				if (isset($this->request->data['OrgSetting']) && ($this->request->data['OrgSetting']['allowed_space'] < $currentDatabasesize && $this->request->data['OrgSetting']['license'] < $useslincense)) {

					$this->request->data = $this->OrgSetting->read(null, $id);
					$this->Session->setFlash(__('Licence and database should not be lesser than existing values'), 'error');
					$this->redirect(array('action' => 'edit_domain/', $id, $user_id));

				} else if (isset($this->request->data['OrgSetting']) && $this->request->data['OrgSetting']['allowed_space'] < $currentDatabasesize) {

					$this->request->data = $this->OrgSetting->read(null, $id);

					$this->Session->setFlash(__('Database limit should not be lesser than existing usage'), 'error');
					$this->redirect(array('action' => 'edit_domain/', $id, $user_id));

				} else if (isset($this->request->data['OrgSetting']) && $this->request->data['OrgSetting']['license'] < $useslincense) {

					$this->request->data = $this->OrgSetting->read(null, $id);

					$this->Session->setFlash(__('Licence should not be lesser than existing number of users'), 'error');
					$this->redirect(array('action' => 'edit_domain/', $id, $user_id));

				} else {

					//==========================================================================
					$subdomain = '';
					if (isset($this->request->data['OrgSetting']['subdomain']) && !empty($this->request->data['OrgSetting']['subdomain'])) {
						$subdomain = $this->request->data['OrgSetting']['subdomain'];
						unset($this->request->data['OrgSetting']['subdomain']);
					}

					$this->request->data['OrgSetting']['jeera_version'] = $ideasCastVersion;

					if ($this->OrgSetting->saveAssociated($this->request->data)) {
						// Will update domain DB Config file
						$this->create_subdomain_file($this->request->data['OrgSetting']['id']);
						$this->create_subdomain_temp_file($this->request->data['OrgSetting']['id']);
						$this->Session->setFlash(__('The domain has been updated successfully.'), 'success');
						$this->redirect(array('action' => 'list_domain', $this->request->data['OrgSetting']['user_id']));

					} else {
						$datas = $this->OrgSetting->findById($id);
					}
				}
			}

		} else {
			$this->request->data = $this->OrgSetting->read(null, $id);
		}

	}

	public function dashboard() {
		$this->layout = 'inner';
		$this->set('title_for_layout', __('Organization Dashboard', true));
		$viewData['page_heading'] = 'Organization Dashboard';
		$viewData['page_subheading'] = '';

		$domainlists = 0;
		$whatINeed = explode(WEBDOMAIN, $_SERVER['HTTP_HOST']);
		$whatINeed = $whatINeed[0];

		App::import("Model", "OrgSettingJeera");
		$OrgSettingJeera = new OrgSettingJeera();

		App::import("Model", "OrgUserSettingJeera");
		$OrgUserSettingJeera = new OrgUserSettingJeera();

		$result = $OrgSettingJeera->find('first', array('conditions' => array('OrgSettingJeera.domain_name' => $whatINeed)));

		$domainlists = $OrgUserSettingJeera->find('count', array('conditions' => array('OrgSetting.user_id' => $result['OrgSettingJeera']['user_id'], 'OrgSetting.subdomain !=' => $whatINeed)));


		/* if (($this->Session->read('Auth.User.role_id') == 2 && $this->Session->read('Auth.User.UserDetail.administrator') != 1)  && LOCALIP != $_SERVER['SERVER_ADDR'] ) {

						$this->redirect(array('controller' => 'projects', 'action' => 'lists'));
		} */

		if (($this->Session->read('Auth.User.role_id') == 2)  && LOCALIP != $_SERVER['SERVER_ADDR'] ) {

			$this->redirect(array('controller' => 'projects', 'action' => 'lists'));
		}
		if( $this->Session->read('Auth.User.role_id') == 1 ){
				$this->redirect(['controller' => 'dashboard','action' => 'index']);
		}

		$profile = $this->Session->read('Auth.User');
		$this->set('profile', $profile);
		$this->set('domainCount', $domainlists);

	}

	public function get_color_code($slug = null) {
		$this->layout = false;
		$this->autoRender = false;
		$this->loadModel("AdminSetting");
		$old = $this->AdminSetting->findBySlug($slug);
		return isset($old['AdminSetting']['color_code']) && !empty($old['AdminSetting']['color_code']) ? $old['AdminSetting']['color_code'] : 'bg-default';
	}

	public function update_color() {
		$response['success'] = false;
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			// $this->autoRender = false;
			$this->loadModel("AdminSetting");
			$slug = $color_code = null;
			if ($this->request->is('post') || $this->request->is('put')) {
				$slug = $this->request->data['slug'];
				$color_code = $this->request->data['color_code'];
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
					if ($this->AdminSetting->save($this->request->data)) {
						$response['success'] = true;
					}
				}
			}
		}
		echo json_encode($response);
		exit;
	}

	function domain_list() {

		$this->layout = 'inner';
		/* $this->set('title_for_layout', __('Associated  Domains', true));
			$viewData['page_heading'] = 'Associated  Domains';
		*/

		$this->set('title_for_layout', __('Linked Domains', true));
		$viewData['page_heading'] = 'Linked Domains';
		$viewData['page_subheading'] = 'Client Linked Domains';



		$whatINeed = explode(WEBDOMAIN, $_SERVER['HTTP_HOST']);
		$whatINeed = $whatINeed[0];

		App::import("Model", "OrgSettingJeera");
		$OrgSettingJeera = new OrgSettingJeera();

		App::import("Model", "OrgUserSettingJeera");
		$OrgUserSettingJeera = new OrgUserSettingJeera();

		$result = $OrgSettingJeera->find('first', array('conditions' => array('OrgSettingJeera.domain_name' => $whatINeed)));

		$domainlists = $OrgUserSettingJeera->find('all', array('conditions' => array('OrgSetting.user_id' => $result['OrgSettingJeera']['user_id'], 'OrgSetting.subdomain !=' => $whatINeed)));

		//if (($this->Session->read('Auth.User.role_id') == 2 && $this->Session->read('Auth.User.UserDetail.administrator') != 1)  && LOCALIP != $_SERVER['SERVER_ADDR'] ) {
		if (($this->Session->read('Auth.User.role_id') != 3)  && LOCALIP != $_SERVER['SERVER_ADDR'] ) {

			$this->redirect(array('controller' => 'projects', 'action' => 'lists'));
		}

		//$domainlists = $OrgUserSettingJeera->find('all', array('conditions'=>array('OrgSetting.user_id' => $result['OrgSettingJeera']['user_id'] )) );

		$viewData['crumb'] = [
			'last' => [
				'data' => [
					'title' => "Linked Domains",
					'data-original-title' => "Linked Domains",
				],
			],
		];
		$this->set('crumb',$viewData['crumb']);
		$this->set('viewData',$viewData);
		$this->set('listDomain', $domainlists);
		$this->set('whatINeed', $whatINeed);

	}

	public function client_email_domain($subdomain = null) {

		if ($subdomain == null) {
			//$this->Session->setFlash(__('Domain is not accessible.'), 'error');
			$this->redirect(array('action' => 'domain_list'));
			die('success');
		}

		if (($this->Session->read('Auth.User.role_id') == 2)  && LOCALIP != $_SERVER['SERVER_ADDR'] ) {

			$this->redirect(array('controller' => 'projects', 'action' => 'lists'));
		}
		if( $this->Session->read('Auth.User.role_id') == 1 ){
				$this->redirect(['controller' => 'dashboard','action' => 'index']);
		}

		$this->layout = 'inner';
		$this->set('title_for_layout', __('Linked Domain Emails', true));

		$viewData['page_heading'] = 'Linked Domain Emails';
		$viewData['page_subheading'] = 'View linked domain emails';

		App::import("Model", "OrgSettingJeera");
		$OrgSettingJeera = new OrgSettingJeera();

		App::import("Model", "OrgUserSettingJeera");
		$OrgUserSettingJeera = new OrgUserSettingJeera();

		if (isset($subdomain) && !empty($subdomain)) {
			$listdomain = array();

			$whatINeed = explode(WEBDOMAIN, $_SERVER['HTTP_HOST']);
			$whatINeed = $whatINeed[0];

			$result = $OrgSettingJeera->find('first', array('conditions' => array('OrgSettingJeera.domain_name' => $whatINeed)));

			$domainlists = $OrgUserSettingJeera->find('first', array('conditions' => array('OrgSetting.user_id' => $result['OrgSettingJeera']['user_id'], 'OrgSetting.subdomain =' => $subdomain)));

			if (isset($domainlists) && !empty($domainlists)) {

				//$this->conn = mysql_pconnect(root_host, $domainlists['OrgSetting']['dbuser'], $domainlists['OrgSetting']['dbpass']);
				//mysqli_select_db($domainlists['OrgSetting']['dbname']);

				$con = mysqli_connect(root_host, $domainlists['OrgSetting']['dbuser'], $domainlists['OrgSetting']['dbpass'], $domainlists['OrgSetting']['dbname']);

				if (mysqli_connect_errno()) {
					echo "Failed to connect to MySQL: " . mysqli_connect_error();
				}

				$email_domains = mysqli_query($con,"SELECT * FROM manage_domains ");

				if (@mysqli_num_rows($email_domains) > 0) {
					while ($resultdomain = mysqli_fetch_array($email_domains)) {
						$listdomain[] = $resultdomain;
					}
				}
				$this->set(compact('listdomain', 'subdomain', 'viewData'));

			} else {
				$this->Session->setFlash(__('https://' . $subdomain . WEBDOMAIN.' domain is not accessible.'), 'error');
				$this->redirect(array('action' => 'domain_list'));
				die('success');
			}

		}

	}

	public function client_manage_users_old($subdomain = null, $emaildomain = null) {

		if ($subdomain == null) {
			//$this->Session->setFlash(__('Domain is not accessible.'), 'error');
			$this->redirect(array('action' => 'domain_list'));
			die('success');
		}

		$this->layout = 'inner';
		$this->set('title_for_layout', __('Linked Domain Users', true));

		$viewData['page_heading'] = 'Linked Domain Users';
		$viewData['page_subheading'] = 'View linked domain users';

		App::import("Model", "OrgSettingJeera");
		$OrgSettingJeera = new OrgSettingJeera();

		App::import("Model", "OrgUserSettingJeera");
		$OrgUserSettingJeera = new OrgUserSettingJeera();

		$andConditions = '';

		if (isset($subdomain) && !empty($subdomain)) {
			$listDomainUsers = '';
			$listEmailDomains = array();

			$whatINeed = explode(WEBDOMAIN, $_SERVER['HTTP_HOST']);
			$whatINeed = $whatINeed[0];

			$result = $OrgSettingJeera->find('first', array('conditions' => array('OrgSettingJeera.domain_name' => $whatINeed)));

			$domainlists = $OrgUserSettingJeera->find('first', array('conditions' => array('OrgSetting.user_id' => $result['OrgSettingJeera']['user_id'], 'OrgSetting.subdomain =' => $subdomain)));

			if (isset($emaildomain) && !empty($emaildomain)) {
				$andConditions = ' AND users.managedomain_id =' . $emaildomain;
			}

			if (isset($domainlists) && !empty($domainlists)) {
				$this->conn = mysql_pconnect(root_host, $domainlists['OrgSetting']['dbuser'], $domainlists['OrgSetting']['dbpass']);
				mysql_select_db($domainlists['OrgSetting']['dbname']);

				$edomains = mysql_query("SELECT id, domain_name FROM manage_domains");

				if (@mysql_num_rows($edomains) > 0) {
					while ($resultemail = mysql_fetch_array($edomains)) {
						$listEmailDomains[$resultemail['id']] = $resultemail['domain_name'];
					}
				}

				$users = mysql_query("SELECT users.*,user_details.first_name,user_details.last_name,user_details.administrator FROM users INNER JOIN user_details ON users.id = user_details.user_id where role_id = 2 " . $andConditions);

				if (@mysql_num_rows($users) > 0) {
					while ($resultusers = mysql_fetch_array($users)) {
						$listDomainUsers[] = $resultusers;
					}
				}
				$this->set(compact('listDomainUsers', 'subdomain', 'viewData', 'listEmailDomains'));
			} else {
				$this->Session->setFlash(__('https://' . $subdomain . WEBDOMAIN.' domain is not accessible.'), 'error');
				$this->redirect(array('action' => 'domain_list'));
				die('success');
			}
		}

	}

	public function checkEmailDomainUser($domainName = null) {

		$response = false;
		if (isset($domainName) && !empty($domainName)) {
			$allusers = $this->User->find('all', array('conditions' => array('User.role_id !=' => 3), 'fields' => array('id', 'email')));

			if (isset($allusers) && !empty($allusers) && count($allusers) > 0) {
				$domainCount = 0;
				foreach ($allusers as $val) {

					$getDomain = explode("@", $val['User']['email']);
					if ($getDomain[1] == $domainName) {
						$domainCount++;
					}

				}
				if ($domainCount > 0) {
					$response = true;
				}
			}

		} else {
			$response = false;
		}
		return $response;
	}

	public function testemail() {

		$link = mysqli_connect(root_host, root_dbuser, root_dbpass,root_dbname);
		if (mysqli_connect_errno()) {
			echo "Failed to connect to MySQL: " . mysqli_connect_error();
		}
		$sql = mysqli_query($link,"select subdomain from org_settings");
		while ($result = mysqli_fetch_array($sql)) {
			//$command = "wget -O /dev/null https://prod.ideascast.com/organisations/testcron >/dev/null 2>&1";
			$command = "wget -O /dev/null https://" . $result['subdomain'] . WEBDOMAIN."/organisations/testcron/" . $result['subdomain'];
			exec("wget -O /dev/null https://" . $result['subdomain'] . WEBDOMAIN."/organisations/testcron/" . $result['subdomain']);
		}
		mysqli_close($link);

	}

	public function import_database($database_name = null,$db_user = null, $db_pass = null,$domain_id = null) {
		//ENTER THE RELEVANT INFO BELOW

		if( $_SERVER['SERVER_ADDR'] ==  OPUSVIEW || $_SERVER['SERVER_ADDR'] ==  OPUSVIEW_DEV ||  $_SERVER['SERVER_ADDR']  ==OPUSVIEW_CLOUD )
		{
			$mysqlUserName = root_dbuser;
			$mysqlPassword = root_dbpass;
		} else {
			$mysqlUserName = $db_user;
			$mysqlPassword = $db_pass;
		}

		$mysqlDatabaseName = $database_name;
		$mysqlHostName = root_host;
		$mysqlImportFilename = WWW_ROOT . 'copydb/dummy_database.sql';
		$viewImport = WWW_ROOT . 'copydb/user_permission_view.sql';

		//DO NOT EDIT BELOW THIS LINE
		//Export the database and output the status to the page
		$command = 'mysql -h ' . $mysqlHostName . ' -u ' . $mysqlUserName . ' -p"' . $mysqlPassword . '" --default-character-set=utf8 ' . $mysqlDatabaseName . ' < ' . $mysqlImportFilename;

		$command1 = 'mysql -h ' . $mysqlHostName . ' -u ' . $mysqlUserName . ' -p"' . $mysqlPassword . '" --default-character-set=utf8 ' . $mysqlDatabaseName . ' < ' . $viewImport;

		exec($command, $output, $worked);
		exec($command1, $output, $worked);

		switch ($worked) {
		case 0:
			return true;
			break;
		case 1:
			return false;
			break;
		}
	}
	//this function will call after import_database function
	public function import_procedure_drop_index($database_name = null,$db_user = null, $db_pass = null,$domain_id = null) {
		//ENTER THE RELEVANT INFO BELOW

		if( $_SERVER['SERVER_ADDR'] ==  OPUSVIEW || $_SERVER['SERVER_ADDR'] ==  OPUSVIEW_DEV ||  $_SERVER['SERVER_ADDR']  ==OPUSVIEW_CLOUD )
		{
			$mysqlUserName = root_dbuser;
			$mysqlPassword = root_dbpass;
		} else {
			$mysqlUserName = $db_user;
			$mysqlPassword = $db_pass;
		}

		$mysqlDatabaseName = $database_name;
		$mysqlHostName = root_host;
		$mysqlImportFilename = WWW_ROOT . 'copydb/procedure_drop_index_1.sql';

		//DO NOT EDIT BELOW THIS LINE
		//Export the database and output the status to the page
		$command = 'mysql -h ' . $mysqlHostName . ' -u ' . $mysqlUserName . ' -p"' . $mysqlPassword . '" --default-character-set=utf8 ' . $mysqlDatabaseName . ' < ' . $mysqlImportFilename;

		exec($command, $output, $worked);

		switch ($worked) {
		case 0:
			return true;
			break;
		case 1:
			return false;
			break;
		}
	}
	//this function will call after import_procedure_drop_index function
	public function drop_create_index($database_name = null,$db_user = null, $db_pass = null,$domain_id = null) {
		//ENTER THE RELEVANT INFO BELOW

		if( $_SERVER['SERVER_ADDR'] ==  OPUSVIEW || $_SERVER['SERVER_ADDR'] ==  OPUSVIEW_DEV ||  $_SERVER['SERVER_ADDR']  ==OPUSVIEW_CLOUD )
		{
			$mysqlUserName = root_dbuser;
			$mysqlPassword = root_dbpass;
		} else {
			$mysqlUserName = $db_user;
			$mysqlPassword = $db_pass;
		}

		$mysqlDatabaseName = $database_name;
		$mysqlHostName = root_host;
		$mysqlImportFilename = WWW_ROOT . 'copydb/drop_and_create_indexe_2.sql';

		//DO NOT EDIT BELOW THIS LINE
		//Export the database and output the status to the page
		$command = 'mysql -h ' . $mysqlHostName . ' -u ' . $mysqlUserName . ' -p"' . $mysqlPassword . '" --default-character-set=utf8 ' . $mysqlDatabaseName . ' < ' . $mysqlImportFilename;

		exec($command, $output, $worked);

		switch ($worked) {
		case 0:
			return true;
			break;
		case 1:
			return false;
			break;
		}
	}

	//this function will call after drop_create_index function
	public function create_foreign_keys($database_name = null,$db_user = null, $db_pass = null,$domain_id = null) {
		//ENTER THE RELEVANT INFO BELOW

		if( $_SERVER['SERVER_ADDR'] ==  OPUSVIEW || $_SERVER['SERVER_ADDR'] ==  OPUSVIEW_DEV ||  $_SERVER['SERVER_ADDR']  ==OPUSVIEW_CLOUD )
		{
			$mysqlUserName = root_dbuser;
			$mysqlPassword = root_dbpass;
		} else {
			$mysqlUserName = $db_user;
			$mysqlPassword = $db_pass;
		}

		$mysqlDatabaseName = $database_name;
		$mysqlHostName = root_host;
		$mysqlImportFilename = WWW_ROOT . 'copydb/create_foreign_keys_3.sql';

		//DO NOT EDIT BELOW THIS LINE
		//Export the database and output the status to the page
		$command = 'mysql -h ' . $mysqlHostName . ' -u ' . $mysqlUserName . ' -p"' . $mysqlPassword . '" --default-character-set=utf8 ' . $mysqlDatabaseName . ' < ' . $mysqlImportFilename;

		exec($command, $output, $worked);

		switch ($worked) {
		case 0:
			return true;
			break;
		case 1:
			return false;
			break;
		}
	}

	//====================== Start Cron function =======================================

	function testcron($pd = null) {

		$email = new CakeEmail();
		$email->config('Smtp');
		$email->from(array(ADMIN_FROM_EMAIL => SITENAME));
		$email->to('testcron@gmail.com');
		$email->subject(SITENAME . ': Test Cron Email ' . $pd);

		return $email->send('Test Cron email message from idea admin via = ' . $pd);
		die;
	}

	public function projectScheduleOverdueCron() {

		$link = mysqli_connect(root_host, cron_dbuser, cron_dbpass,root_dbname);
		if (mysqli_connect_errno()) {
			echo "Failed to connect to MySQL: " . mysqli_connect_error();
		}
		$sql = mysqli_query($link,"select subdomain from org_settings");
		while ($result = mysqli_fetch_array($sql)) {

			//exec("wget -O /dev/null https://".$result['subdomain'].".ideascast.com/projects/projectScheduleOverdueEmailCron");

			$ch = curl_init();

			// set URL and other appropriate options
			curl_setopt($ch, CURLOPT_URL, "https://" . $result['subdomain'] . WEBDOMAIN."/projects/projectScheduleOverdueEmailCron");
			curl_setopt($ch, CURLOPT_HEADER, 0);

			// grab URL and pass it to the browser
			curl_exec($ch);

			// close cURL resource, and free up system resources
			curl_close($ch);
		}

		mysqli_close($link);

		//5	0	*	*	*	wget -O /dev/null https://jeera.ideascast.com/projects/projectScheduleOverdueEmailCron

	}

	public function workspaceScheduleOverdueCron() {

		$link = mysqli_connect(root_host, cron_dbuser, cron_dbpass,root_dbname);
		if (mysqli_connect_errno()) {
			echo "Failed to connect to MySQL: " . mysqli_connect_error();
		}
		$sql = mysqli_query($link,"select subdomain from org_settings");
		while ($result = mysqli_fetch_array($sql)) {

			//exec("wget -O /dev/null https://".$result['subdomain'].".ideascast.com/workspaces/workspaceScheduleOverdueEmailCron");

			$ch = curl_init();

			// set URL and other appropriate options
			curl_setopt($ch, CURLOPT_URL, "https://" . $result['subdomain'] .WEBDOMAIN."/workspaces/workspaceScheduleOverdueEmailCron");
			curl_setopt($ch, CURLOPT_HEADER, 0);

			// grab URL and pass it to the browser
			curl_exec($ch);

			// close cURL resource, and free up system resources
			curl_close($ch);
		}

		mysqli_close($link);
		//20	0	*	*	*	wget -O /dev/null https://jeera.ideascast.com/workspaces/workspaceScheduleOverdueEmailCron

	}

	public function elementScheduleOverdueCron() {

		$link = mysqli_connect(root_host, cron_dbuser, cron_dbpass,root_dbname);
		if (mysqli_connect_errno()) {
			echo "Failed to connect to MySQL: " . mysqli_connect_error();
		}
		$sql = mysqli_query($link,"select subdomain from org_settings");
		while ($result = mysqli_fetch_array($sql)) {

			//exec("https://".$result['subdomain'].".ideascast.com/entities/elementScheduleOverdueEmailCron");
			$ch = curl_init();

			// set URL and other appropriate options
			curl_setopt($ch, CURLOPT_URL, "https://" . $result['subdomain'] . WEBDOMAIN."/entities/elementScheduleOverdueEmailCron");
			curl_setopt($ch, CURLOPT_HEADER, 0);

			// grab URL and pass it to the browser
			curl_exec($ch);

			// close cURL resource, and free up system resources
			curl_close($ch);
		}

		mysqli_close($link);

		//30	0	*	*	*	wget -O /dev/null https://jeera.ideascast.com/entities/elementScheduleOverdueEmailCron

	}

	public function todoScheduleOverdueCron() {

		$link = mysqli_connect(root_host, cron_dbuser, cron_dbpass,root_dbname);
		if (mysqli_connect_errno()) {
			echo "Failed to connect to MySQL: " . mysqli_connect_error();
		}
		$sql = mysqli_query($link,"select subdomain from org_settings");
		while ($result = mysqli_fetch_array($sql)) {

			//exec("wget -O /dev/null https://".$result['subdomain'].".ideascast.com/todos/todoScheduleOverdueEmailCron");
			$ch = curl_init();

			// set URL and other appropriate options
			curl_setopt($ch, CURLOPT_URL, "https://" . $result['subdomain'] . WEBDOMAIN."/todos/todoScheduleOverdueEmailCron");
			curl_setopt($ch, CURLOPT_HEADER, 0);

			// grab URL and pass it to the browser
			curl_exec($ch);

			// close cURL resource, and free up system resources
			curl_close($ch);

		}

		mysqli_close($link);

		//0	1	*	*	*	wget -O /dev/null https://jeera.ideascast.com/todos/todoScheduleOverdueEmailCron

	}

	public function update_todays_reminder_setting_cron() {

		$link = mysqli_connect(root_host, cron_dbuser, cron_dbpass,root_dbname);
		if (mysqli_connect_errno()) {
			echo "Failed to connect to MySQL: " . mysqli_connect_error();
		}
		$sql = mysqli_query($link,"select subdomain from org_settings");
		while ($result = mysqli_fetch_array($sql)) {

			//exec("wget -O /dev/null https://".$result['subdomain'].".ideascast.com/dashboards/update_todays_reminder_setting");

			$ch = curl_init();

			// set URL and other appropriate options
			curl_setopt($ch, CURLOPT_URL, "https://" . $result['subdomain'] . WEBDOMAIN."/dashboards/update_todays_reminder_setting");
			curl_setopt($ch, CURLOPT_HEADER, 0);

			// grab URL and pass it to the browser
			curl_exec($ch);

			// close cURL resource, and free up system resources
			curl_close($ch);
		}

		mysqli_close($link);
		//0	0	*	*	*	wget -O /dev/null https://jeera.ideascast.com/dashboards/update_todays_reminder_setting

	}

	public function skts_cron() {

		$link = mysqli_connect(root_host, root_dbuser, root_dbpass,root_dbname);
		if (mysqli_connect_errno()) {
			echo "Failed to connect to MySQL: " . mysqli_connect_error();
		}
		$sql = mysqli_query($link,"select subdomain from org_settings");
		while ($result = mysqli_fetch_array($sql)) {

			//exec("wget -O /dev/null https://".$result['subdomain'].".ideascast.com/skts/crons");
			$ch = curl_init();

			// set URL and other appropriate options
			curl_setopt($ch, CURLOPT_URL, "https://" . $result['subdomain'] . WEBDOMAIN."/skts/crons");
			curl_setopt($ch, CURLOPT_HEADER, 0);

			// grab URL and pass it to the browser
			curl_exec($ch);

			// close cURL resource, and free up system resources
			curl_close($ch);
		}

		mysqli_close($link);
		//0	0	*	*	*	wget -O /dev/null https://jeera.ideascast.com/dashboards/update_todays_reminder_setting

	}

	//Risk email Notification
	public function riskScheduleOverdueCron() {

		$link = mysqli_connect(root_host, cron_dbuser, cron_dbpass,root_dbname);
		if (mysqli_connect_errno()) {
			echo "Failed to connect to MySQL: " . mysqli_connect_error();
		}
		$sql = mysqli_query($link,"select subdomain from org_settings");
		while ($result = mysqli_fetch_array($sql)) {

			$ch = curl_init();

			// set URL and other appropriate options
			curl_setopt($ch, CURLOPT_URL, "https://" . $result['subdomain'] . WEBDOMAIN."/risks/emailNotificationOverdueCron");
			curl_setopt($ch, CURLOPT_HEADER, 0);

			// grab URL and pass it to the browser
			curl_exec($ch);

			// close cURL resource, and free up system resources
			curl_close($ch);
		}

		mysqli_close($link);

		//5	0	*	*	*	wget -O /dev/null https://jeera.ideascast.com/projects/projectScheduleOverdueEmailCron

	}

	//====================== End Cron function =======================================

	public function manage_templates() {

		if ($this->Session->read('Auth.User.role_id') != 3) {
			$this->redirect(array('controller' => 'dashboards', 'action' => 'project_center'));
			die();
		}

		$this->layout = 'inner';
		$this->set('title_for_layout', __('Manage Templates', true));
		$viewData['page_heading'] = 'Manage Templates';
		$viewData['page_subheading'] = 'Copy Templates to Linked Domains';

		App::import("Model", "OrgUserSettingJeera");
		$OrgUserSettingJeera = new OrgUserSettingJeera();

		App::import("Model", "OrgSettingJeera");
		$OrgSettingJeera = new OrgSettingJeera();

		//============= Get Internal Template =======================================
		$this->TemplateRelation->unbindModel(
			array('belongsTo' => array('Template', 'User', 'TemplateCategory'))
		);
		$this->TemplateRelation->unbindModel(
			array('hasMany' => array('AreaRelation', 'TemplateLike', 'TemplateReview', 'Workspace'))
		);
		$internaltemplate = $this->TemplateRelation->find('list',
			array('conditions' => array('TemplateRelation.type' => 1),
				'fields' => array('id', 'title'),
				'order' => 'TemplateRelation.title ASC',
			));

		//============ Get all subdomain ============================================
		$whatINeed = explode(WEBDOMAIN, $_SERVER['HTTP_HOST']);
		$whatINeed = $whatINeed[0];
		$result = $OrgSettingJeera->find('first', array('conditions' => array('OrgSettingJeera.domain_name' => $whatINeed)));
		$alldomains = $OrgUserSettingJeera->find('list',
			array('conditions' => array('OrgSetting.user_id' => $result['OrgSettingJeera']['user_id'], 'subdomain !=' => $whatINeed),
				'fields' => array('id', 'subdomain'),
				'order' => 'OrgSetting.subdomain ASC',
			)
		);

		$templateCategories = $this->TemplateCategory->find('list',
			array('order' => 'TemplateCategory.title ASC')
		);

		$crumb = [
			'Summary' => [
				'data' => [
					'url' => '/organisations/manage_templates',
					'class' => 'tipText',
					'title' => 'Import Template',
					'data-original-title' => 'Template',
				],
			], /* ,
			'last' => [
				'data' => [
					'title' => 'List Domains',
					'data-original-title' => 'List Domains',
				],
			], */
		];

		$this->set(compact('internaltemplate', 'alldomains', 'viewData', 'templateCategories', 'crumb'));
	}

	public function getDomainUsers($domain_id = null) {

		$response['success'] = false;
		if ($this->request->isAjax()) {
			$view = new View($this, false);
			$view->layout = false;

			$this->autoRender = false;
			// Create new view to return to ajax request

			if ($this->request->is('post') || $this->request->is('put')) {

				$domain_id = $this->request->data['domain_id'];

				App::import("Model", "OrgUserSettingJeera");
				$OrgUserSettingJeera = new OrgUserSettingJeera();

				$domains = $OrgUserSettingJeera->findById($domain_id);

				$link = mysql_connect(WEBDOMAIN_HOST, $domains['OrgSetting']['dbuser'], $domains['OrgSetting']['dbpass']);
				if ($link) {
					$dbselect = mysql_select_db($domains['OrgSetting']['dbname'], $link);
				}
				$sql = mysql_query("select users.id,user_details.first_name,user_details.last_name from users,user_details where users.role_id = 2 AND users.id = user_details.user_id order by user_details.first_name ASC ");
				$userlists = array();
				while ($result = mysql_fetch_assoc($sql)) {
					$userlists[] = $result;
				}

				//pr($userlists);die;

				$view->viewPath = 'Organisations';
				$view->set('userlists', $userlists);
				$html = $view->render('userslist');

				echo json_encode($html);
				die();

				mysql_close($link);

			}
		}
	}

	public function getTemplate($folder_id = null) {

		$response['success'] = false;
		if ($this->request->isAjax()) {
			$view = new View($this, false);
			$view->layout = false;

			$this->autoRender = false;
			// Create new view to return to ajax request

			if ($this->request->is('post') || $this->request->is('put')) {

				$folder_id = $this->request->data['folder_id'];

				//============= Get Internal Template =======================================
				$this->TemplateRelation->unbindModel(
					array('belongsTo' => array('Template', 'User', 'TemplateCategory'))
				);
				$this->TemplateRelation->unbindModel(
					array('hasMany' => array('AreaRelation', 'TemplateLike', 'TemplateReview', 'Workspace'))
				);
				$internaltemplate = $this->TemplateRelation->find('list',
					array('conditions' => array('TemplateRelation.type' => 1, 'TemplateRelation.template_category_id' => $folder_id),
						'fields' => array('id', 'title'),
					));

				/* $view->viewPath = 'Organisations';
					$view->set('templatelist', $internaltemplate);
					$html = $view->render('templatelist');
				*/
				echo json_encode($internaltemplate);
				die();

				mysql_close($link);

			}
		}
	}

	public function templateMoveto($template_id = null, $domain_id = null, $user_id = null) {

		App::import("Model", "OrgUserSettingJeera");
		$OrgUserSettingJeera = new OrgUserSettingJeera();

		$response['template_id'] = '';
		$response['subdomain_id'] = '';
		$response['domainUserid'] = '';
		$response['category_id'] = '';
		$reviews_staus = 1;
		$likes_status = 1;
		$document_status = 1;
		$response['success'] = false;

		if ($this->request->isAjax()) {
			$this->autoRender = false;

			$template_relation_id = null;
			$domain_id = null;
			$user_id = null;

			if ($this->request->is('post') || $this->request->is('put')) {

				if (isset($this->request->data['template_id']) && !empty($this->request->data['template_id'])) {
					$template_relation_id = $this->request->data['template_id'];
				} else {
					$response['template_id'] = "Please select a template.";
				}

				if (isset($this->request->data['subdomain_id']) && !empty($this->request->data['subdomain_id'])) {
					$domain_id = $this->request->data['subdomain_id'];
				} else {
					$response['subdomain_id'] = "Please select a domain.";
				}

				if (isset($this->request->data['domainUserid']) && !empty($this->request->data['domainUserid'])) {
					$user_id = $this->request->data['domainUserid'];
				} else {
					$response['domainUserid'] = "Please select a domain user.";
				}

				if (isset($this->request->data['category_id']) && !empty($this->request->data['category_id'])) {
					$category_id = $this->request->data['category_id'];
				} else {
					$response['category_id'] = "Please select a category.";
				}

				if (isset($this->request->data['copywith']) && !empty($this->request->data['copywith']) && count($this->request->data['copywith']) > 0) {

					foreach ($this->request->data['copywith'] as $val) {

						if (isset($val['reviews']) && $val['reviews'] == 'reviews') {
							$reviews_staus = 0;
						}
						if (isset($val['likes']) && $val['likes'] == 'likes') {
							$likes_status = 0;
						}
						if (isset($val['documents']) && $val['documents'] == 'documents') {
							$document_status = 0;
						}
					}
				}

				$imageFieldName = null;
				if (!empty($template_relation_id) && !empty($domain_id) && !empty($user_id) && !empty($category_id) && count($template_relation_id) > 0) {

					$check_file = true;

					if (isset($this->request->data['upload_image']) && !empty($this->request->data['upload_image'])) {

						$sizeLimit = 10 * 1024 * 1024; //10MB
						$folder_url = WWW_ROOT . TEMPLATES_MOVE_IMAGE;
						$result = $fileNewName = $upload_object = $upload_detail = null;

						$upload_object = (isset($_FILES["file_image"])) ? $_FILES["file_image"] : null;

						$folder_url .= DS . $user_id;

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

								$orgFileName = $upload_object['name'];
								$exists_file = $folder_url . DS . $orgFileName;
								$fileNewName .= time() . $orgFileName;

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
										$imageFieldName = $fileNewName;
									}

									$upload_detail['name'] = $fileNewName;
									$upload_detail['type'] = $upload_object['type'];
									$upload_detail['size'] = $sizeStr;
								}
							} else {

								$check_file = false;
								$response['template_img_msg'] = "File size limit exceeded,Please upload a file upto 10MB.";
								$response['success'] = false;
							}

						}
					}

					// Get Domain details from main Organisation
					$domainDetails = $OrgUserSettingJeera->find('first',
						array('conditions' => array('OrgSetting.id' => $domain_id),
						)
					);

					// Connect to subdomain
					$link = mysql_connect(WEBDOMAIN_HOST, $domainDetails['OrgSetting']['dbuser'], $domainDetails['OrgSetting']['dbpass']);
					if ($link) {
						$dbselect = mysql_select_db($domainDetails['OrgSetting']['dbname'], $link);
					}

					// Start copy template to anoter subdomain
					foreach ($template_relation_id as $templateid_list) {
						$templateRelationInsertId = '';
						$templateRelationtest = $this->TemplateRelation->find('first', [
							'conditions' => [
								'TemplateRelation.id' => $templateid_list,
							],
							'recursive' => -1,
						]);
						/* pr($templateRelationtest);
								die; */
						// Get Template Relations
						$templateRelation = $this->TemplateRelation->find('first', [
							'conditions' => [
								'TemplateRelation.id' => $templateid_list,
							],
							'recursive' => -1,
						]);
						// Get Area with Template Relations
						$areaRelation = $this->AreaRelation->find('all', [
							'conditions' => [
								'AreaRelation.template_relation_id' => $templateid_list,
							],
							'recursive' => -1,

						]);

						// Get Template Like from Template Relations
						$templateLikes = $this->TemplateLike->find('all', [
							'conditions' => [
								'TemplateLike.template_relation_id' => $templateid_list,
							],
						]);

						// Get Template Like from Template Relations
						$templateReviews = $this->TemplateReview->find('all', [
							'conditions' => [
								'TemplateReview.template_relation_id' => $templateid_list,
							],
						]);

						//pr($areaRelation);
						//die;
						//template_category_id = 	'".$templateRelation['TemplateRelation']['template_category_id']."',
						//============ INSERT DATA INTO TEMPLATE RELATIONS TABLE =================================

						$temp_title = preg_replace('/(<br>)+$/', '', $templateRelation['TemplateRelation']['title']);

						$temp_description = preg_replace('/(<br>)+$/', '', $templateRelation['TemplateRelation']['description']);

						$atitle = htmlentities($temp_title, ENT_QUOTES);
						$adescription = htmlentities($temp_description, ENT_QUOTES);

						$insertTemplateRelation = "INSERT INTO template_relations SET

															type = 	'" . $templateRelation['TemplateRelation']['type'] . "',
															user_id = 	'" . $user_id . "',
															template_id = 	'" . $templateRelation['TemplateRelation']['template_id'] . "',
															thirdparty_id = 	'" . $templateRelation['TemplateRelation']['thirdparty_id'] . "',
															template_category_id = 	'" . $category_id . "',
															rating = 	'" . $templateRelation['TemplateRelation']['rating'] . "',
															status = 	'" . $templateRelation['TemplateRelation']['status'] . "',
															title = 	'" . $atitle . "',
															description = 	'" . $adescription . "',
															key_result_target = 	'" . $templateRelation['TemplateRelation']['key_result_target'] . "',
															color_code = 	'" . $templateRelation['TemplateRelation']['color_code'] . "',
															is_search = '" . $templateRelation['TemplateRelation']['is_search'] . "',
															template_image = '" . $imageFieldName . "',
															created = '" . date('Y-m-d h:i:s') . "',
															modified = 	'" . date('Y-m-d h:i:s') . "'
														";
						//echo "<br>";
						//echo $insertTemplateRelation;
						$insert = mysql_query($insertTemplateRelation);
						$templateRelationInsertId = mysql_insert_id();
						//echo $templateRelationInsertId."<br>";
						//========= INSERT DATA INTO AREA RELATIONS TABLE =============================================
						if (isset($areaRelation) && !empty($areaRelation)) {
							foreach ($areaRelation as $templateAreaRelations) {

								$adata = null;
								$area_data = $adata = $templateAreaRelations['AreaRelation'];
								// pr($adata);
								$area_relation_id = $area_data['id'];
								$adata['template_relation_id'] = $templateRelationInsertId;
								unset($adata['id']);

								$insertAreaRelation = "INSERT INTO area_relations SET
													`template_relation_id` = " . $templateRelationInsertId . ",
													`template_detail_id` = " . $templateAreaRelations['AreaRelation']['template_detail_id'] . ",
													`title` = '" . $templateAreaRelations['AreaRelation']['title'] . "',
													`description` = '" . $templateAreaRelations['AreaRelation']['description'] . "',
													`tooltip_text` = '" . $templateAreaRelations['AreaRelation']['tooltip_text'] . "',
													`is_standby` = '" . $templateAreaRelations['AreaRelation']['is_standby'] . "',
													`status` = '" . $templateAreaRelations['AreaRelation']['status'] . "',
													`sort_order` = '" . $templateAreaRelations['AreaRelation']['sort_order'] . "',
													`studio_status` = '" . $templateAreaRelations['AreaRelation']['studio_status'] . "',
													`is_search` = '" . $templateAreaRelations['AreaRelation']['is_search'] . "'

												";
								//echo $insertAreaRelation."<br>";
								mysql_query($insertAreaRelation);
								$newAreaRelId = mysql_insert_id();

								$elements = $this->ElementRelation->find('all', [
									'conditions' => [
										'ElementRelation.area_relation_id' => $area_relation_id,
									],
									'recursive' => -1,
								]);
								//================== Element Start =======================

								if (isset($elements) && !empty($elements)) {
									$edata = null;
									// save all elements with new area relation id
									foreach ($elements as $ekey => $evalue) {
										$edata = $evalue['ElementRelation'];
										//unset($edata['id']);
										$edata['area_relation_id'] = $newAreaRelId;

										// insert Element Relation table

										$elementRelations = "INSERT INTO element_relations SET
																					area_relation_id = '" . $newAreaRelId . "',
																					updated_user_id = '" . $user_id . "',
																					title = '" . $edata['title'] . "',
																					description = '" . $edata['title'] . "',
																					comments = '" . $edata['comments'] . "',
																					date_constraints = '" . $edata['date_constraints'] . "',
																					start_date = '" . $edata['start_date'] . "',
																					end_date = '" . $edata['end_date'] . "',
																					sign_off = '" . $edata['sign_off'] . "',
																					color_code = '" . $edata['color_code'] . "',
																					sort_order = '" . $edata['sort_order'] . "',
																					status = '" . $edata['status'] . "'
																				";

										mysql_query($elementRelations);
										$newElementRelationsId = mysql_insert_id();

										// Copy Documents
										if (isset($document_status) && $document_status == 1) {
											// source element relation id
											//$element_relation_id = $evalue['ElementRelation']['id'];
											$documents = $this->ElementRelationDocument->find('all', [
												'conditions' => [
													'ElementRelationDocument.element_relation_id' => $evalue['ElementRelation']['id'],
												],
												'recursive' => -1,
											]);

											if (isset($documents) && !empty($documents)) {
												// loop through all documents
												$newid = '';
												foreach ($documents as $dkey => $dvalue) {
													$doc_data = null;
													$doc_data = $dvalue['ElementRelationDocument'];

													//unset($doc_data['id']);
													$file_name = $doc_data['file_name'];

													// Get new name, change if already exists
													$path = WWW_ROOT . 'uploads/template_element_document';
													$new_file_name = $this->file_newname($path, $file_name);

													$old_file_path = WWW_ROOT . 'uploads/template_element_document' . DS . $file_name;
													$new_file_path = WWW_ROOT . 'uploads/template_element_document' . DS . $new_file_name;

													$doc_data['file_name'] = $new_file_name;
													$doc_data['element_relation_id'] = $newElementRelationsId;

													// copy document
													if (copy($old_file_path, $new_file_path)) {
														// save with new name

														$documentInsert = "INSERT INTO element_relation_documents SET
																							element_relation_id = '" . $newElementRelationsId . "',
																							creater_id = '" . $user_id . "',
																							updated_user_id = '" . $user_id . "',
																							title = '" . $doc_data['title'] . "',
																							file_name = '" . $doc_data['file_name'] . "',
																							file_size = '" . $doc_data['file_size'] . "',
																							file_type = '" . $doc_data['file_type'] . "',
																							status = '" . $doc_data['status'] . "',
																							is_search = '" . $doc_data['is_search'] . "' ";
														//echo $documentInsert;
														mysql_query($documentInsert);
														$newElementRelId = mysql_insert_id();
														//$this->ElementRelationDocument->save($doc_data);
														if ($newElementRelId > 0) {
															$response['success'] = true;
															$this->Session->setFlash(__('Template successfully import at subdomain <a target="_blank" href="https://' . $domainDetails['OrgSetting']['subdomain'] . WEBDOMAIN.'">https://' . $domainDetails['OrgSetting']['subdomain'] . '</a>'), 'success');
														}

													}
												}
												//echo $newid;
											}
										} // document end

										// Copy Template Likes =================
										if (isset($likes_status) && $likes_status == 1) {
											if (isset($templateLikes) && !empty($templateLikes)) {

												foreach ($templateLikes as $listlikes) {

													$insertLikes = "INSERT INTO template_likes SET
																			template_relation_id = " . $templateRelationInsertId . ",
																			user_id = " . $user_id . ",
																			like_unlike = " . $listlikes['TemplateLike']['like_unlike'] . ",
																			created = '" . date('Y-m-d h:i:s') . "',
																			modified = 	'" . date('Y-m-d h:i:s') . "',
																			is_search =  '" . $listlikes['TemplateLike']['is_search'] . "' ";
													mysql_query($insertLikes);
												}

											}
										}
										// Copy Templates Reviews
										if (isset($reviews_staus) && $reviews_staus == 1) {
											if (isset($templateReviews) && !empty($templateReviews)) {
												foreach ($templateReviews as $listreviews) {

													$insertReviews = "INSERT INTO template_reviews SET
																			template_relation_id = " . $templateRelationInsertId . ",
																			user_id = " . $user_id . ",
																			comments = '" . $listreviews['TemplateReview']['comments'] . "',
																			rating = '" . $listreviews['TemplateReview']['rating'] . "',
																			used_unused = '" . $listreviews['TemplateReview']['used_unused'] . "',
																			created = '" . date('Y-m-d h:i:s') . "',
																			modified = 	'" . date('Y-m-d h:i:s') . "',
																			is_search =  '" . $listreviews['TemplateReview']['is_search'] . "'";
													mysql_query($insertReviews);
												}
											}
										}

									}
								}
								//======== Element End ============================================
								$response['success'] = true;
								$this->Session->setFlash(__('Template successfully import at subdomain <a target="_blank" href="https://' . $domainDetails['OrgSetting']['subdomain'] . WEBDOMAIN.'">https://' . $domainDetails['OrgSetting']['subdomain'] . '</a>'), 'success');
							}
						}
						//=============================================================================
					}

				}

				mysql_close($link);
			}
			echo json_encode($response);
			exit;
		}
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

	public function used_space_notification_cron() {

		//$this->loadModel('UserPlan');

		$orgdetails = $this->OrgSetting->find('all');

		$orgFulldetails = $this->Common->userDetail($id);

		if (isset($orgdetails) && !empty($orgdetails)) {
			foreach ($orgdetails as $listdomain) {

				$this->conn = mysql_pconnect('localhost', $orgdetails['OrgSetting']['dbuser'], $orgdetails['OrgSetting']['dbpass']);
				mysql_select_db($orgdetails['OrgSetting']['dbname']);
				$userDetails = mysql_query("SELECT * FROM users WHERE role_id = 2 ");

				$lincentotals = 0;
				if (@mysql_num_rows($userDetails) > 0) {
					$lincentotals = mysql_num_rows($userDetails);
				}

				if ($this->conn) {

					$dbselect = mysql_select_db($orgdetails['OrgSetting']['dbname'], $this->conn);
					$result1 = mysql_query("SHOW TABLE STATUS");
					$dbsize = 0;
					while ($row = mysql_fetch_array($result1)) {
						$dbsize += $row["Data_length"] + $row["Index_length"];
					}

					$currentDatabasesizeGB = number_format(($dbsize / 1024 / 1024 / 1024), 3); // Database size in GB
					$currentDatabasesizeMB = number_format(($dbsize / 1024 / 1024), 2); // Database size in MB
					mysql_close($this->conn);
					$this->set('consumedbsizegb', $currentDatabasesizeGB);
					$this->set('consumedbsizemb', $currentDatabasesizeMB);

				}

			}
		}

		// $this->set('userfullname', $orgFulldetails);
		// $this->set('licencestotal', $lincentotals);
		// $this->set('orgsettings', $orgdetails);

	}

	public function insufficient_space() {

		$link = mysqli_connect(root_host, cron_dbuser, cron_dbpass,root_dbname);
		if (mysqli_connect_errno()) {
			echo "Failed to connect to MySQL: " . mysqli_connect_error();
		}
		$sql = mysqli_query($link,"select subdomain from org_settings");
		while ($result = mysqli_fetch_array($sql)) {

			$ch = curl_init();

			// set URL and other appropriate options
			curl_setopt($ch, CURLOPT_URL, "https://" . $result['subdomain'] .WEBDOMAIN. "/organisations/domain_total_size/" . $result['subdomain']);
			curl_setopt($ch, CURLOPT_HEADER, 0);

			// grab URL and pass it to the browser
			curl_exec($ch);

			// close cURL resource, and free up system resources
			curl_close($ch);
		}

		mysqli_close($link);
		//0	0	*	*	*	wget -O /dev/null https://jeera.ideascast.com/dashboards/update_todays_reminder_setting

	}

	public function domain_total_size($domain_name = null) {

		$this->autoRender = false;

		App::import("Model", "OrgUserSettingJeera");
		$OrgUserSettingJeera = new OrgUserSettingJeera();

		$response['currentDatabasesizeGB'] = 0.00;
		$response['currentDatabasesizeMB'] = 0.00;
		$response['lincentotals'] = 0;

		// data from main jeera database org_setting table
		$orgdetails = $OrgUserSettingJeera->find('first', array('conditions' => array('subdomain' => $domain_name)));

		if (isset($orgdetails['OrgSetting']) && !empty($orgdetails['OrgSetting']['id']) && !empty($orgdetails['OrgSetting']['subdomain'])) {

			$userdetails = $this->User->find('first', array('conditions' => array('User.id' => $orgdetails['OrgSetting']['user_id'])));
			$org_name = $userdetails['UserDetail']['org_name'];
			$client_name = $userdetails['UserDetail']['full_name'];

			if( $_SERVER['SERVER_ADDR'] ==  OPUSVIEW || $_SERVER['SERVER_ADDR'] ==  OPUSVIEW_DEV ||  $_SERVER['SERVER_ADDR']  ==OPUSVIEW_CLOUD )
			{
				$mysqlUserName = root_dbuser;
				$mysqlPassword = root_dbpass;
			} else {
				$mysqlUserName = $orgdetails['OrgSetting']['dbuser'];
				$mysqlPassword = $orgdetails['OrgSetting']['dbpass'];
			}

			$con = mysqli_connect(root_host, $mysqlUserName, $mysqlPassword, $orgdetails['OrgSetting']['dbname']);

			if (mysqli_connect_errno()) {
				echo "Failed to connect to MySQL: " . mysqli_connect_error();
			}

			if ($con && isset($userdetails) && !empty($userdetails)) {

				$result1 = mysqli_query($con, "SHOW TABLE STATUS");
				$dbsize = 0;
				while ($row = mysqli_fetch_array($result1)) {
					$dbsize += $row["Data_length"] + $row["Index_length"];
				}

				//$currentDatabasesizeGB = number_format(($dbsize / 1024 / 1024 / 1024), 3); // Database size in GB
				//$currentDatabasesizeMB = number_format(($dbsize / 1024 / 1024), 2); // Database size in MB

				// file size ======================================================

				$totalsize = 0;
				$announcements = mysqli_query($con, "SELECT * FROM `announcements`");
				//$announcementscount = mysqli_num_rows($announcements);

				$blogdocuments = mysqli_query($con, "SELECT document_name FROM blog_documents");
				$todolistcomment = mysqli_query($con, "SELECT file_name FROM do_list_comment_uploads ");
				$todolistuploads = mysqli_query($con, "SELECT file_name FROM do_list_uploads ");
				$elementdocuments = mysqli_query($con, "SELECT element_id,file_name FROM element_documents ");
				$elementrelationdocuments = mysqli_query($con, "SELECT file_name FROM element_relation_documents order by file_name asc ");
				$feedbackattachments = mysqli_query($con, "SELECT file_name FROM feedback_attachments ");
				$todocommentuploads = mysqli_query($con, "SELECT document FROM to_do_comment_uploads ");
				$todosubcommentuploads = mysqli_query($con, "SELECT document FROM to_do_sub_comment_uploads ");
				$todosubuploads = mysqli_query($con, "SELECT document FROM to_do_sub_uploads ");
				$todouploads = mysqli_query($con, "SELECT document FROM to_do_uploads ");
				$userdetailss = mysqli_query($con, "SELECT profile_pic, document_pic, menu_pic FROM user_details ");
				$wikipagecommentdocuments = mysqli_query($con, "SELECT document_name FROM wiki_page_comment_documents ");

				$elementrelationdocument_size = 0;
				if (mysqli_num_rows($elementrelationdocuments) > 0) {
					while ($result = mysqli_fetch_array($elementrelationdocuments)) {

						if (file_exists("uploads/template_element_document/" . $result['file_name'])) {
							$elementrelationdocument_size += filesize("uploads/template_element_document/" . $result['file_name']);
						}
					}
				}

				$announcements_size = 0;
				if (!empty($announcements)) {
					while ($result = mysqli_fetch_array($announcements)) {

						if (file_exists("uploads/announcement/" . $result['announce_file'])) {
							$announcements_size += filesize("uploads/announcement/" . $result['announce_file']);
						}
					}
				}

				$blogdocuments_size = 0;
				if (mysqli_num_rows($blogdocuments) > 0) {
					while ($result = mysqli_fetch_array($blogdocuments)) {

						if (file_exists("uploads/blogdocuments/" . $result['document_name'])) {
							$blogdocuments_size += filesize("uploads/blogdocuments/" . $result['document_name']);
						}
					}
				}

				$todolistcomment_size = 0;
				if (mysqli_num_rows($todolistcomment) > 0) {
					while ($result = mysqli_fetch_array($todolistcomment)) {

						if (file_exists("uploads/dolist_comments/" . $result['file_name'])) {
							$todolistcomment_size += filesize("uploads/dolist_comments/" . $result['file_name']);
						}
					}
				}

				$todolistuploads_size = 0;
				if (mysqli_num_rows($todolistuploads) > 0) {
					while ($result = mysqli_fetch_array($todolistuploads)) {

						if (file_exists("uploads/dolist_uploads/" . $result['file_name'])) {
							$todolistuploads_size += filesize("uploads/dolist_uploads/" . $result['file_name']);
						}
					}
				}

				$elementdocuments_size = 0;
				if (mysqli_num_rows($elementdocuments) > 0) {
					while ($result = mysqli_fetch_array($elementdocuments)) {

						if (file_exists("uploads/element_documents/" . $result['element_id'] . "/" . $result['file_name'])) {
							$elementdocuments_size += filesize("uploads/element_documents/" . $result['element_id'] . "/" . $result['file_name']);
						}
					}
				}

				$feedbackattachments_size = 0;
				if (mysqli_num_rows($feedbackattachments) > 0) {
					while ($result = mysqli_fetch_array($feedbackattachments)) {

						if (file_exists("uploads/element_feedback_images/" . $result['file_name'])) {
							$feedbackattachments_size += filesize("uploads/element_feedback_images/" . $result['file_name']);
						}
					}
				}

				$todocommentuploads_size = 0;
				if (mysqli_num_rows($todocommentuploads) > 0) {
					while ($result = mysqli_fetch_array($todocommentuploads)) {

						if (file_exists("uploads/dolist_comments/" . $result['document'])) {
							$todocommentuploads_size += filesize("uploads/dolist_comments/" . $result['document']);
						}
					}
				}

				$todosubcommentuploads_size = 0;
				if (mysqli_num_rows($todosubcommentuploads) > 0) {
					while ($result = mysqli_fetch_array($todosubcommentuploads)) {

						if (file_exists("uploads/dolist_comments/" . $result['document'])) {
							$todosubcommentuploads_size += filesize("uploads/dolist_comments/" . $result['document']);
						}
					}
				}

				$todosubuploads_size = 0;
				if (mysqli_num_rows($todosubuploads) > 0) {
					while ($result = mysqli_fetch_array($todosubuploads)) {

						if (file_exists("uploads/dolist_uploads/" . $result['document'])) {
							$todosubuploads_size += filesize("uploads/dolist_uploads/" . $result['document']);
						}
					}
				}

				$todouploads_size = 0;
				if (mysqli_num_rows($todouploads) > 0) {
					while ($result = mysqli_fetch_array($todouploads)) {

						if (file_exists("uploads/dolist_uploads/" . $result['document'])) {
							$todouploads_size += filesize("uploads/dolist_uploads/" . $result['document']);
						}
					}
				}

				$userprofilepic_size = 0;
				$userdocumentpic_size = 0;
				$usermenupic_size = 0;
				//profile_pic, document_pic, menu_pic userdetails
				if (mysqli_num_rows($userdetailss) > 0) {
					while ($result = mysqli_fetch_array($userdetailss)) {

						if (file_exists("uploads/user_images/" . $result['profile_pic'])) {
							$userprofilepic_size += filesize("uploads/user_images/" . $result['profile_pic']);
						}
						if (file_exists("uploads/user_images/" . $result['document_pic'])) {
							$userdocumentpic_size += filesize("uploads/user_images/" . $result['document_pic']);
						}
						if (file_exists("uploads/user_images/" . $result['menu_pic'])) {
							$usermenupic_size += filesize("uploads/user_images/" . $result['menu_pic']);
						}
					}
				}

				$wikipagecommentdocuments_size = 0;
				if (mysqli_num_rows($wikipagecommentdocuments) > 0) {
					while ($result = mysqli_fetch_array($wikipagecommentdocuments)) {

						if (file_exists("uploads/wiki_page_document/" . $result['document'])) {
							$wikipagecommentdocuments_size += filesize("uploads/wiki_page_document/" . $result['document']);
						}
					}
				}

				$kulsize = $elementrelationdocument_size + $announcements_size + $blogdocuments_size + $todolistcomment_size + $elementdocuments_size + $todolistuploads_size + $feedbackattachments_size + $todocommentuploads_size + $todosubcommentuploads_size + $todosubuploads_size + $todouploads_size + $userprofilepic_size + $userdocumentpic_size + $usermenupic_size + $wikipagecommentdocuments_size;

				$chatsizetotal = $this->chatSize($domain_name);

				$currentDatabasesizeGB = number_format((($kulsize + $dbsize) / 1024 / 1024 / 1024), 3); // Database size in GB
				$currentDatabasesizeMB = number_format((($kulsize + $dbsize) / 1024 / 1024), 2) + ($chatsizetotal); // mysql Database size with chat moongodb size in MB
				$allowedspace = ($orgdetails['OrgSetting']['allowed_space'] * 1024);

				$totalpercentage = number_format((($currentDatabasesizeMB / $allowedspace) * 100), 2);
				// $totalpercentage = 80.21;
				if ($totalpercentage >= 80) {
					$pawan = 'vikas.gautam@dotsquares.com';
					// Email to Admin
					$email = new CakeEmail();
					$email->config('Smtp');
					$email->from(array(ADMIN_FROM_EMAIL => SITENAME));
					// $email->to($pawan);
					$email->to(ADMIN_EMAIL);
					$email->subject(SITENAME . ': OpusView Data Storage');
					$email->template('data_storage_admin');
					$email->emailFormat('html');
					$email->viewVars(array('org_name' => $org_name));
					$email->send();

					// Email to Client
					$email = new CakeEmail();
					$email->config('Smtp');
					$email->from(array(ADMIN_FROM_EMAIL => SITENAME));
					// $email->to($pawan);
					$email->to($userdetails['User']['email']);
					$email->subject(SITENAME . ': OpusView Data Storage');
					$email->template('data_storage_clilent');
					$email->emailFormat('html');
					$email->viewVars(array('client_name' => $client_name));
					$email->send();

				} else {
					echo "You have sufficient space, consumed space " . $totalpercentage;
				}
				//=================================================================
				mysqli_close($con);
			}
		}
	}

	public function chatSize($domain = null) {
		//$this->autoRender = false;

		if (PHP_VERSIONS == 5) {

			$mongo = new MongoClient(MONGO_CONNECT);
			$this->mongoDB = $mongo->selectDB($domain);
			$mongo_users = new MongoCollection($this->mongoDB, 'users');
			$mongo_attachments = new MongoCollection($this->mongoDB, 'attachments');
			$ret = $mongo_users->find();

			$pipeline = [
			'$lookup' => [
				'from' => 'attachments',
				'localField' => '_id',
				'foreignField' => 'userId',
				'as' => 'user_attachment',
				],
			];
			$userData = null;

			$imagefolder = array('jpg', 'jpeg', 'png', 'gif');
			$docsfolder = array("txt", "doc", "docx", "xls", "xlsx", "ppt", "pptx", "pdf", "rar", "zip", "rtf");

			$chatAttachementSize = 0;
			foreach ($ret as $obj) {
				foreach ($obj['_id'] as $key => $userId) {

					$att = $mongo_attachments->find(['userId' => $userId, 'visibility' => 'visible']);

					if (isset($att) && !empty($att)) {
						foreach ($att as $key => $value) {
							// get attachments size from chat images folder
							if (in_array($value['extention'], $imagefolder)) {
								if (file_exists(DOC_CHAT_URL."screenshot/" . $value['name'])) {
									$chatAttachementSize += filesize(DOC_CHAT_URL."screenshot/" . $value['name']);
								}
								if (file_exists(DOC_CHAT_URL."images/" . $value['name'])) {
									$chatAttachementSize += filesize(DOC_CHAT_URL."images/" . $value['name']);
								}
							}

							// get attachments size from chat images folder
							if (in_array($value['extention'], $docsfolder)) {
								if (file_exists(DOC_CHAT_URL."document/" . $value['name'])) {
								 $chatAttachementSize += filesize(DOC_CHAT_URL."document/" . $value['name']);
								}
							}
						}
					}
				}
			}

			if ($chatAttachementSize > 0) {
				return round($chatAttachementSize / 1024 / 1024, 2);
			} else {
				return 0;
			}

		} else {

			$chatAttachementSize = 0;
			// MONGO_DATABASE;
			$mongo = new MongoDB\Driver\Manager(MONGO_CONNECT);
			$filter = ['_id' => ' > 0 '];
			$options = [];
			$query = new \MongoDB\Driver\Query([]);
			$ret   = $mongo->executeQuery($domain.'.users', $query)->toArray();

			$re = array();
			foreach ($ret as $obj) {
				$obj = (array)$obj;
				foreach ($obj['_id'] as $userId) {
					$re[] = $userId;
				}
			}

			$filter = ['userId' => ['$in' => $re]];
			$options  = [];

			$queryattc = new \MongoDB\Driver\Query( $filter,$options );
			$att = $mongo->executeQuery($domain.'.attachments', $queryattc)->toArray();

			if (isset($att) && !empty($att)) {

				$imagefolder = array('jpg', 'jpeg', 'png', 'gif');
				$docsfolder = array("txt", "doc", "docx", "xls", "xlsx", "ppt", "pptx", "pdf", "rar", "zip", "rtf");

					foreach ($att as $value) {
					$value = (array) $value ;

					// get attachments size from chat images folder
					if (in_array($value['extention'], $imagefolder)) {
						if (file_exists(DOC_CHAT_URL."screenshot/" . $value['name'])) {
							$chatAttachementSize += filesize(DOC_CHAT_URL."screenshot/" . $value['name']);
						}

						if (file_exists(DOC_CHAT_URL."images/" . $value['name'])) {
							$chatAttachementSize += filesize(DOC_CHAT_URL."images/" . $value['name']);
						}
					}

					// get attachments size from chat images folder
					if (in_array($value['extention'], $docsfolder)) {
						if (file_exists(DOC_CHAT_URL."document/" . $value['name'])) {
						 $chatAttachementSize += filesize(DOC_CHAT_URL."document/" . $value['name']);
						}
					}
				 }
			}
			if ($chatAttachementSize > 0) {
				return round($chatAttachementSize / 1024 / 1024, 2);
			} else {
				return 0;
			}
		}

	}

	public function checkUserPrePassword($userid = null, $limit = null) {

		$this->loadModel('UserPassword');

		$previousPassword = $this->UserPassword->find('list', array('conditions' => array('UserPassword.user_id' => $userid), 'limit' => $limit, 'order' => 'id DESC', 'fields' => array('UserPassword.password')));
		//pr($previousPassword); die;
		return array_values($previousPassword);

	}

	// Enable or Disable Api Sdk status for domain
	public function admin_domain_sdkstatus() {

		if ($this->request->is('ajax')) {

			$this->autoRender = false;
			$this->loadModel('OrgSetting');

			$this->request->data['OrgSetting'] = $this->request->data;

			if ($this->OrgSetting->save($this->request->data, false)) {

				//$data = $this->OrgSetting->updateAll(array('OrgSetting.prmry_sts' => 0), array('OrgSetting.id !=' => $this->request->data['OrgSetting']['id'], 'OrgSetting.user_id' => $this->request->data['OrgSetting']['org_id']));

				$this->Session->setFlash(__('Api Sdk status has been updated successfully.'), 'success');
				die('success');
			} else {
				$this->Session->setFlash(__('Api Sdk status could not updated successfully.'), 'error');
			}

		}
		die('error');
	}

	// controller did not update only below function is added
	public function admin_list_database() {

		$in = 0;
		$org_domain = $this->OrgSetting->find('all');
		$this->set('title_for_layout', __('Database Query', true));
		$viewData['page_heading'] = 'Database Query';
		$viewData['page_subheading'] = 'Query';
		$response = '';
		if( isset($this->request->data['response']) && !empty($this->request->data['response']) ){
			$response = $this->request->data['response'];
		}

		//var_dump($response);

		$this->set('response', $response);
		$this->set('viewData', $viewData);
		$this->set('listdomain', $org_domain);

	}

	public function admin_query_run() {
		$this->autoRender = false;

		if ($this->request->is('post') || $this->request->is('put')) {

			if( isset($this->request->data['dbquery']) && !empty($this->request->data['dbquery']) ){

				$querystr = $this->request->data['dbquery'];
				$multiquery = array();
				if( $this->request->data['dbmultiquery'] == 'on' ){
					$multiquery1 = explode(';',$querystr);
					$multiquery = array_map('trim',$multiquery1);
					//print_r($trimmed_array);
				} else {
					$multiquery[] = $this->request->data['dbquery'];
				}

				$org_domain = $this->OrgSetting->find('all');
				$response = true;
				if( !empty($org_domain) ){

					if( !empty($multiquery) ){
						$i = 0;

						$keys = array_keys($multiquery);
						$last = end($keys);
						if( empty($multiquery[$last])  ){
							array_pop($multiquery);
						}

						foreach($multiquery as $key => $querystring){

							if( !empty($querystring) ){

								$query = trim($querystring);
								foreach($org_domain as $listdbs){

									if( $_SERVER['SERVER_ADDR'] ==  OPUSVIEW || $_SERVER['SERVER_ADDR'] ==  OPUSVIEW_DEV ||  $_SERVER['SERVER_ADDR']  ==OPUSVIEW_CLOUD )
									{
										$mysqlUserName = root_dbuser;
										$mysqlPassword = root_dbpass;
									} else {
										$mysqlUserName = $listdbs['OrgSetting']['dbuser'];
										$mysqlPassword = $listdbs['OrgSetting']['dbpass'];
									}

									$dbcon = mysqli_connect(WEBDOMAIN_HOST, $mysqlUserName, $mysqlPassword, $listdbs['OrgSetting']['dbname']);

									mysqli_query($dbcon,"ALTER DATABASE ".$listdbs['OrgSetting']['dbname']." CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

									if( mysqli_connect_errno() ){

										echo "Failed to connect to MySQL: " . mysqli_connect_error();
										echo "Error in ".$listdbs['OrgSetting']['dbname']." database";
										$response = false;
										$this->Session->setFlash(__("Database '".$listdbs['OrgSetting']['dbname']."' query has some problem."),'error');
										return $this->redirect(array('controller' => 'organisations', 'action' => 'list_database', 'admin' => true));

									} else {

										//$response = true;
										//$sql = mysqli_query($dbcon, $query) or die (mysqli_error($dbcon));
										if(mysqli_query($dbcon, $query)){
											$response = true;
										} else {
											continue;
										}


									}
								}
							}
						$i++;
						}
					}

					$this->set("response",$response);
					$this->Session->setFlash(__('The query has been executed successfully.'),'success');
					return $this->redirect(array('controller' => 'organisations', 'action' => 'list_database', 'admin' => true));

				}
			} else {

				//file import for database
				if( !empty($_FILES['dbsqlfile']['tmp_name']) && !empty($_FILES['dbsqlfile']['name'])  ){

					$folderpath = WWW_ROOT . 'sqlfile/';
					$filetmpname = $_FILES['dbsqlfile']['tmp_name'];
					$orginalfilename = $_FILES['dbsqlfile']['name'];
					$orginalfilenameNew = 'copydbsql.sql';

					unlink($folderpath.$orginalfilenameNew);

					if( move_uploaded_file($filetmpname,$folderpath.$orginalfilenameNew) ){

						$org_domain = $this->OrgSetting->find('all');

						if( !empty($org_domain) ){

							$mysqlImportFilename = WWW_ROOT . 'sqlfile/'.$orginalfilenameNew;

							foreach($org_domain as $listdbs){

								$mysqlDatabaseName = $listdbs['OrgSetting']['dbname'];
								$mysqlUserName = 'ideascast';
								$mysqlPassword = '9ARxbfMXu(o2';
								$mysqlHostName = root_host;
								// $mysqlHostName = 'localhost';

								//Export the database and output the status to the page
								$command = 'mysql -h ' . $mysqlHostName . ' -u ' . $mysqlUserName . ' -p"' . $mysqlPassword . '" --default-character-set=utf8 ' . $mysqlDatabaseName . ' < ' . $mysqlImportFilename;

								exec($command, $output, $worked);
								//echo $command;
								/* switch ($worked) {
								case 0:
									return true;
									break;
								case 1:
									echo $mysqlDatabaseName;
									break;
								} */
								//echo $mysqlDatabaseName."<br />";

							}

							$this->Session->setFlash(__('The query has been executed successfully.'),'success');
							return $this->redirect(array('controller' => 'organisations', 'action' => 'list_database', 'admin' => true));

						}
					}
				}

			}

		}

	}


	public function client_manage_users($subdomain = null, $emaildomain = null) {

		if ($subdomain == null) {
			$this->redirect(array('action' => 'domain_list'));
			die('success');
		}

		if (($this->Session->read('Auth.User.role_id') == 2)  && LOCALIP != $_SERVER['SERVER_ADDR'] ) {

			$this->redirect(array('controller' => 'projects', 'action' => 'lists'));
		}
		if( $this->Session->read('Auth.User.role_id') == 1 ){
				$this->redirect(['controller' => 'dashboard','action' => 'index']);
		}

		$this->layout = 'inner';
		$this->set('title_for_layout', __('Linked Domain Users', true));

		$viewData['page_heading'] = 'Linked Domain Users';
		$viewData['page_subheading'] = 'View linked domain users';

		App::import("Model", "OrgSettingJeera");
		$OrgSettingJeera = new OrgSettingJeera();

		App::import("Model", "OrgUserSettingJeera");
		$OrgUserSettingJeera = new OrgUserSettingJeera();

		$sortorder = 'desc';
		if( isset($this->request->params['named']['sort']) && !empty($this->request->params['named']['sort']) ){
			$sortorder = $this->request->params['named']['sort'];
		}

			$andConditions = '';
			$orConditions = '';
			if (isset($subdomain) && !empty($subdomain)) {
				$listDomainUsers = array();
				$listEmailDomains = array();

				$whatINeed = explode(WEBDOMAIN, $_SERVER['HTTP_HOST']);
				$whatINeed = $whatINeed[0];

				$result = $OrgSettingJeera->find('first', array('conditions' => array('OrgSettingJeera.domain_name' => $whatINeed)));

				$domainlists = $OrgUserSettingJeera->find('first', array('conditions' => array('OrgSetting.user_id' => $result['OrgSettingJeera']['user_id'], 'OrgSetting.subdomain =' => $subdomain)));

				if (isset($emaildomain) && !empty($emaildomain)) {
					$andConditions = ' AND users.managedomain_id =' . $emaildomain;
				}

				if (isset($domainlists) && !empty($domainlists)) {
					//$this->conn = mysql_pconnect('localhost', $domainlists['OrgSetting']['dbuser'], $domainlists['OrgSetting']['dbpass']);
					//mysql_select_db($domainlists['OrgSetting']['dbname']);

					$con = mysqli_connect(root_host, $domainlists['OrgSetting']['dbuser'], $domainlists['OrgSetting']['dbpass'], $domainlists['OrgSetting']['dbname']);

					if (mysqli_connect_errno()) {
						echo "Failed to connect to MySQL: " . mysqli_connect_error();
					}

					$edomains = mysqli_query($con,"SELECT id, domain_name FROM manage_domains");

					if (@mysqli_num_rows($edomains) > 0) {
						while ($resultemail = mysqli_fetch_array($edomains)) {
							$listEmailDomains[$resultemail['id']] = $resultemail['domain_name'];
						}
					}

					$in = 0;
					$per_page_show = $this->Session->read('user.per_page_show');
					if (empty($per_page_show)) {
						$per_page_show = 200;
					}


					if (isset($this->data['User']['keyword'])) {
						$keyword = trim($this->data['User']['keyword']);
					} else if (isset($this->params['named']['search']) && !empty($this->params['named']['search']))
					{
						$keyword = trim($this->params['named']['search']);
					} else {
						$keyword = $this->Session->read('User.keyword');
					}


				if (isset($keyword)) {
					$this->Session->write('User.keyword', $keyword);
					$keywords = explode(" ", $keyword);

					if (isset($keywords[0]) && !empty($keywords[0]) && count($keywords) < 2) {
						$keyword = $keywords[0];
						$in = 1;
						$orConditions = " AND ( user_details.first_name LIKE '". $keyword . "%'";
						$orConditions .= " OR user_details.last_name LIKE '".  $keyword . "%')";


					} else if (!empty($keywords) && count($keywords) > 1) {
						$first_name = $keywords[0];
						$last_name = $keywords[1];
						$in = 1;
						$orConditions = " AND ( user_details.first_name LIKE '". $keyword . "%'";
						$orConditions .= " OR user_details.last_name LIKE '".  $keyword . "%')";

					}
				}


				$users = mysqli_query($con,"SELECT users.*,user_details.first_name,user_details.last_name,user_details.administrator FROM users INNER JOIN user_details ON users.id = user_details.user_id where role_id = 2 " . $andConditions.$orConditions. " order by user_details.first_name ".$sortorder." limit ".$per_page_show );


				if (@mysqli_num_rows($users) > 0) {
					while ($resultusers = mysqli_fetch_array($users)) {
						$listDomainUsers[] = $resultusers;
					}
				}

				$this->set('in', $in);
				$this->set(compact('listDomainUsers', 'subdomain', 'viewData', 'listEmailDomains','keyword'));

			} else {
				$this->Session->setFlash(__('https://' . $subdomain . WEBDOMAIN.' domain is not accessible.'), 'error');
				$this->redirect(array('action' => 'domain_list'));
				die('success');
			}
		}
	}

	function client_admin_resetfilter($domain) {
		$this->Session->write('User.keyword', '');
		$this->redirect(array('action' => 'client_manage_users',$domain));
	}


	// 17 April 2019 This function Will run by Cron:- When Super Admin Create New Template, Cron hit each domain and will inserted template
	public function admin_templateMoveByCron() {
		$this->autoRender = false;
		$this->layout = false;

		App::import("Model", "OrgUserSettingJeera");
		$OrgUserSettingJeera = new OrgUserSettingJeera();

		$imageFieldName = null;
		$response = true;

		$templateData = $this->TemplateMove->find('all', array('conditions'=>array('TemplateMove.deployed_tmp'=>0), 'order'=>'TemplateMove.id ASC', 'limit'=> 5 ) );

		if( isset($templateData) && !empty($templateData) ) {

			// Get Domain details from main Organisation
			$listDomains = $OrgUserSettingJeera->find('all',
					array('conditions' => array('OrgSetting.db_setup'=>1),
				)
			);

			if( isset($listDomains) && !empty($listDomains) ){
				foreach( $listDomains as $domainDetails ){
					// Connect to subdomain

					if( $_SERVER['SERVER_ADDR'] ==  OPUSVIEW || $_SERVER['SERVER_ADDR'] ==  OPUSVIEW_DEV ||  $_SERVER['SERVER_ADDR']  ==OPUSVIEW_CLOUD )
					{
						$mysqlUserName = root_dbuser;
						$mysqlPassword = root_dbpass;
					} else {
						$mysqlUserName = $domainDetails['OrgSetting']['dbuser'];
						$mysqlPassword = $domainDetails['OrgSetting']['dbpass'];
					}

					$con = mysqli_connect(WEBDOMAIN_HOST, $mysqlUserName, $mysqlPassword, $domainDetails['OrgSetting']['dbname']);

					//get template id and category id from TemplateMove table
					if( isset($templateData) && !empty($templateData) ){
						foreach( $templateData as $templateids ){

							$templateMoveData['TemplateMove']['id']=$templateids['TemplateMove']['id'];
							$templateMoveData['TemplateMove']['deployed_tmp'] = 1;

							$this->TemplateMove->save($templateMoveData);

							$templateid_list = $templateids['TemplateMove']['template_id'];
							$category_id = $templateids['TemplateMove']['folder_id'];
							$likes_status = 1;
							$reviews_staus = 1;
							$document_status = 1;
							$user_id = 1;

							//========================================================================
							$templateRelationInsertId = '';
							// Get Template Relations
							$templateRelation = $this->TemplateRelation->find('first', [
								'conditions' => [
									'TemplateRelation.id' => $templateid_list,
								],
								'recursive' => -1,
							]);

							// Get Area with Template Relations
							$areaRelation = $this->AreaRelation->find('all', [
								'conditions' => [
									'AreaRelation.template_relation_id' => $templateid_list,
								],
								'recursive' => -1,

							]);

							//===== INSERT DATA INTO TEMPLATE RELATIONS TABLE =================

							$temp_title = preg_replace('/(<br>)+$/', '', $templateRelation['TemplateRelation']['title']);

							$temp_description = preg_replace('/(<br>)+$/', '', $templateRelation['TemplateRelation']['description']);

							$atitle = htmlentities($temp_title, ENT_QUOTES);
							$adescription = htmlentities($temp_description, ENT_QUOTES);

							$insertTemplateRelation = "INSERT INTO template_relations SET

																type = 	'" . $templateRelation['TemplateRelation']['type'] . "',
																user_id = 	'" . $user_id . "',
																template_id = 	'" . $templateRelation['TemplateRelation']['template_id'] . "',
																thirdparty_id = 	'" . $templateRelation['TemplateRelation']['thirdparty_id'] . "',
																template_category_id = 	'" . $category_id . "',
																rating = 	'" . $templateRelation['TemplateRelation']['rating'] . "',
																status = 	'" . $templateRelation['TemplateRelation']['status'] . "',
																title = 	'" . $atitle . "',
																description = 	'" . $adescription . "',
																key_result_target = 	'" . $templateRelation['TemplateRelation']['key_result_target'] . "',
																color_code = 	'" . $templateRelation['TemplateRelation']['color_code'] . "',
																is_search = '" . $templateRelation['TemplateRelation']['is_search'] . "',
																template_image = '" . $imageFieldName . "',
																created = '" . date('Y-m-d h:i:s') . "',
																modified = 	'" . date('Y-m-d h:i:s') . "'
															";

							$insert = mysqli_query($con,$insertTemplateRelation);
							$templateRelationInsertId = mysqli_insert_id($con);

							//======== INSERT DATA INTO AREA RELATIONS TABLE ==================
							if (isset($areaRelation) && !empty($areaRelation)) {
								foreach ($areaRelation as $templateAreaRelations) {

									$adata = null;
									$area_data = $adata = $templateAreaRelations['AreaRelation'];$area_relation_id = $area_data['id'];
									$adata['template_relation_id'] = $templateRelationInsertId;
									unset($adata['id']);

									$insertAreaRelation = "INSERT INTO area_relations SET
														`template_relation_id` = " . $templateRelationInsertId . ",
														`template_detail_id` = " . $templateAreaRelations['AreaRelation']['template_detail_id'] . ",
														`title` = '" . $templateAreaRelations['AreaRelation']['title'] . "',
														`description` = '" . $templateAreaRelations['AreaRelation']['description'] . "',
														`tooltip_text` = '" . $templateAreaRelations['AreaRelation']['tooltip_text'] . "',
														`is_standby` = '" . $templateAreaRelations['AreaRelation']['is_standby'] . "',
														`status` = '" . $templateAreaRelations['AreaRelation']['status'] . "',
														`sort_order` = '" . $templateAreaRelations['AreaRelation']['sort_order'] . "',
														`studio_status` = '" . $templateAreaRelations['AreaRelation']['studio_status'] . "',
														`is_search` = '" . $templateAreaRelations['AreaRelation']['is_search'] . "'

													";
									mysqli_query($con,$insertAreaRelation);
									$newAreaRelId = mysqli_insert_id($con);

									$elements = $this->ElementRelation->find('all', [
										'conditions' => [
											'ElementRelation.area_relation_id' => $area_relation_id,
										],
										'recursive' => -1,
									]);
									//================== Element Start =======================

									if (isset($elements) && !empty($elements)) {
										$edata = null;
										// save all elements with new area relation id
										foreach ($elements as $ekey => $evalue) {
											$edata = $evalue['ElementRelation'];
											//unset($edata['id']);
											$edata['area_relation_id'] = $newAreaRelId;

											// insert Element Relation table
											$elementRelations = "INSERT INTO element_relations SET
													area_relation_id = '" . $newAreaRelId . "',
													updated_user_id = '" . $user_id . "',
													title = '" . $edata['title'] . "',
													description = '" . $edata['title'] . "',
													comments = '" . $edata['comments'] . "',
													date_constraints = '" . $edata['date_constraints'] . "',
													start_date = '" . $edata['start_date'] . "',
													end_date = '" . $edata['end_date'] . "',
													sign_off = '" . $edata['sign_off'] . "',
													color_code = '" . $edata['color_code'] . "',
													sort_order = '" . $edata['sort_order'] . "',
													status = '" . $edata['status'] . "' ";

											mysqli_query($con,$elementRelations);
											$newElementRelationsId = mysqli_insert_id($con);

											// Copy Documents
											if (isset($document_status) && $document_status == 1) {

												$documents = $this->ElementRelationDocument->find('all', [
													'conditions' => [
														'ElementRelationDocument.element_relation_id' => $evalue['ElementRelation']['id'],
													],
													'recursive' => -1,
												]);

												if (isset($documents) && !empty($documents)) {
													// loop through all documents
													$newid = '';
													foreach ($documents as $dkey => $dvalue) {
														$doc_data = null;
														$doc_data = $dvalue['ElementRelationDocument'];

														//unset($doc_data['id']);
														$file_name = $doc_data['file_name'];

														// Get new name, change if already exists
														$path = WWW_ROOT . 'uploads/template_element_document';
														$new_file_name = $this->file_newname($path, $file_name);

														$old_file_path = WWW_ROOT . 'uploads/template_element_document' . DS . $file_name;
														$new_file_path = WWW_ROOT . 'uploads/template_element_document' . DS . $new_file_name;

														$doc_data['file_name'] = $new_file_name;
														$doc_data['element_relation_id'] = $newElementRelationsId;

														// copy document
														if (copy($old_file_path, $new_file_path)) {
															// save with new name

									$documentInsert = "INSERT INTO element_relation_documents SET
									element_relation_id = '" . $newElementRelationsId . "',
									creater_id = '" . $user_id . "',
									updated_user_id = '" . $user_id . "',
									title = '" . $doc_data['title'] . "',
									file_name = '" . $doc_data['file_name'] . "',
									file_size = '" . $doc_data['file_size'] . "',
									file_type = '" . $doc_data['file_type'] . "',
									status = '" . $doc_data['status'] . "',
									is_search = '" . $doc_data['is_search'] . "' ";


															mysqli_query($con,$documentInsert);
															$newElementRelId = mysqli_insert_id($con);

														}
													}
												}
											}
										}
									} //======== Element End =======================
								}
							}//=====================================================
						}


						mysqli_close($con);
					}
				} echo "Template successfully inserted";
			}
		}
	}
	//***************** Start Chat General Setting ***********************
	public function general_settings() {

		$this->loadModel("Setting");

		$this->layout = 'inner';
		$this->set('title_for_layout', __('Manage General Settings', true));

		$viewData['page_heading'] = 'General';
		$viewData['page_subheading'] = 'Change the overall behavior of the system';

		//if (($this->Session->read('Auth.User.role_id') == 2 && $this->Session->read('Auth.User.UserDetail.administrator') != 1)  && LOCALIP != $_SERVER['SERVER_ADDR'] ) {
		if (($this->Session->read('Auth.User.role_id') != 3)  && LOCALIP != $_SERVER['SERVER_ADDR'] ) {

			$this->redirect(array('controller' => 'projects', 'action' => 'lists'));
		}

		$viewData['settings'] = false;
		$data = $this->Setting->find('first', array('conditions' => array('Setting.id' => 1), 'fields' => 'chat_capability'));
		if(isset($data) && !empty($data)) {
			$viewData['chat_capability'] = (isset($data['Setting']['chat_capability']) && !empty($data['Setting']['chat_capability'])) ? true : false;
		}

		$this->set($viewData);
	}
	//

	public function update_chat_capability() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$response = [
				'success' => false,
				'content' => [],
			];

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;

				$chat_capability = (isset($post['chat_capability']) && !empty($post['chat_capability'])) ? 1 : 0;

				$this->Setting->id = 1;
				if ($this->Setting->saveField('chat_capability', $chat_capability)) {
					$response['success'] = true;
				}
			}

			echo json_encode($response);
			exit();

		}
	}

	//***************** End Chat General Setting ***********************

	/***********************************************************************
	******************* File Write for SubDomain Detail ********************
	************************************************************************/
	//for multiple domain
	public function create_subdomain_file($subdomain_id = null ){
		$this->autoRender = false;
		$this->layout = false;

		App::import("Model", "OrgUserSettingJeera");
		$OrgUserSettingJeera = new OrgUserSettingJeera();

		if( isset($subdomain_id) && !empty($subdomain_id)  ){
			// Get Domain details from main Organisation
			$listDomains = $OrgUserSettingJeera->find('all',array('conditions'=>array('OrgSetting.id'=>$subdomain_id) ) );
		} else {
			// Get Domain details from main Organisation
			$listDomains = $OrgUserSettingJeera->find('all');
		}

		if(!is_dir($_SERVER['DOCUMENT_ROOT'].'/app/domain_dbs')) {
		  mkdir($_SERVER['DOCUMENT_ROOT'].'/app/domain_dbs');
		}
		if( isset($listDomains) && !empty($listDomains) ){
			foreach( $listDomains as $domainDetails ){

				if( file_exists($_SERVER['DOCUMENT_ROOT'].'/app/domain_dbs/' . $domainDetails['OrgSetting']['subdomain'] . '.php') ){
					unlink($_SERVER['DOCUMENT_ROOT'].'/app/domain_dbs/' . $domainDetails['OrgSetting']['subdomain'] . '.php');
				}

				$domain_name = $domainDetails['OrgSetting']['subdomain'];
				$domain_userid = $domainDetails['OrgSetting']['user_id'];
				$domain_dbname = $domainDetails['OrgSetting']['dbname'];
				$domain_dbuser = $domainDetails['OrgSetting']['dbuser'];
				$domain_dbpass = $domainDetails['OrgSetting']['dbpass'];
				$domain_db_setup = $domainDetails['OrgSetting']['db_setup'];
				$domain_status = $domainDetails['OrgSetting']['status'];
				$domain_start_date = $domainDetails['OrgSetting']['start_date'];
				$domain_end_date = $domainDetails['OrgSetting']['end_date'];
				$domain_allowed_space = $domainDetails['OrgSetting']['allowed_space'];
				$domain_license = $domainDetails['OrgSetting']['license'];
				$domain_apilicense = $domainDetails['OrgSetting']['apilicense'];
				$domain_jeera_version = $domainDetails['OrgSetting']['jeera_version'];
				$domain_mm_port = $domainDetails['OrgSetting']['mm_port'];
				$domain_mongo_port = $domainDetails['OrgSetting']['mongo_port'];
				$domain_prmry_sts = $domainDetails['OrgSetting']['prmry_sts'];
				$domain_apisdk_status = $domainDetails['OrgSetting']['apisdk_status'];

				$configPath = $_SERVER['DOCUMENT_ROOT'].'/app/domain_dbs/' . $domain_name . '.php';
				fopen($configPath, 'w') or die('Cannot open file:  ' . $configPath);

				if (file_exists($configPath)) {

					$documentChatSize = $this->domain_document_size($domain_name,$domain_dbname,root_dbuser,root_dbpass);

					$register_size = explode('=',$documentChatSize);

					$siteurl = 'https://'.$domain_name.WEBDOMAIN;
					$configData = file_get_contents($configPath);
					$newtxt = "<?php ". PHP_EOL;
					$newtxt .= '$domain_url = "'.$siteurl.'";'. PHP_EOL;
					$newtxt .= '$domain_name = "'.$domain_name.'";'. PHP_EOL;
					$newtxt .= '$domain_userid = "'.$domain_userid.'";'. PHP_EOL;
					$newtxt .= '$domain_dbname = "'.$domain_dbname.'";'. PHP_EOL;
					$newtxt .= '$domain_dbuser = "'.$domain_dbuser.'";'. PHP_EOL;
					$newtxt .= '$'."domain_dbpass = '".$domain_dbpass."';". PHP_EOL;
					$newtxt .= '$domain_db_setup = "'.$domain_db_setup.'";'. PHP_EOL;
					$newtxt .= '$domain_status = "'.$domain_status.'";'. PHP_EOL;
					$newtxt .= '$domain_start_date = "'.$domain_start_date.'";'. PHP_EOL;
					$newtxt .= '$domain_end_date = "'.$domain_end_date.'";'. PHP_EOL;
					$newtxt .= '$domain_apilicense = "'.$domain_apilicense.'";'. PHP_EOL;
					$newtxt .= '$domain_jeeraversion = "'.$domain_jeera_version.'";'. PHP_EOL;
					$newtxt .= '$domain_mmport = "'.$domain_mm_port.'";'. PHP_EOL;
					$newtxt .= '$domain_mongo_port = "'.$domain_mongo_port.'";'. PHP_EOL;

					$newtxt .= '$domain_user_license = "'.$domain_license.'";'. PHP_EOL;
					$newtxt .= '$registered_user_license = "'.$register_size[1].'";'. PHP_EOL;

					$newtxt .= '$domain_allowedspace = "'.$domain_allowed_space.'GB";'. PHP_EOL;
					//DB Size, Document and Chat size
					$newtxt .= '$domain_consumed_space = "'.$register_size[0].'MB";'. PHP_EOL;

					//$newtxt .= '$DOMAIN_PRYSTS = "'.$domain_prmry_sts.'";'. PHP_EOL;
					//$newtxt .= '$DOMAIN_SDKSTATUS = "'.$domain_apisdk_status.'";'. PHP_EOL;

					$newtxt .= "?>";

					file_put_contents($configPath, $newtxt.$configData);

				}

			}
		}
	}

	public function domain_document_size($domain_name,$dbname,$dbuser,$dbpass){
		$this->autoRender = false;
		$this->layout = false;
			$con = mysqli_connect(root_host, $dbuser, $dbpass, $dbname);
			if (mysqli_connect_errno()) {
				echo "Failed to connect to MySQL: " . mysqli_connect_error();
			}
			if ($con) {
				//============= Registered User Count ==========================================================
				$registerUserCountSqlQuery = "SELECT COUNT(*) as total FROM users WHERE role_id = 2";
				$registerUserCount = 0;
				if( $result = mysqli_query($con,$registerUserCountSqlQuery) ){
					$registerUserCountSql = mysqli_fetch_assoc($result);
					$registerUserCount =  ( isset($registerUserCountSql['total']) && !empty($registerUserCountSql['total']) ) ? $registerUserCountSql['total'] : 0;
				}
				$currentDatabasesize = 0.00;
				$dbsize = 0;
				$totalsize = 0;

				$result1 = mysqli_query($con, "SHOW TABLE STATUS");
				while ($row = mysqli_fetch_array($result1)) {
					$dbsize += $row["Data_length"] + $row["Index_length"];
				}

				// file size ======================================================


				$elementrelationdocuments_result = "SELECT file_name FROM element_relation_documents order by file_name asc ";
				$elementrelationdocument_size = 0;
				if( $elementrelationdocuments = mysqli_query($con,$elementrelationdocuments_result) ){
					if (mysqli_num_rows($elementrelationdocuments) > 0) {
						while ($result = mysqli_fetch_array($elementrelationdocuments)) {
							if (file_exists(DOC_ROOT."uploads/template_element_document/" . $result['file_name'])) {
								$elementrelationdocument_size += filesize(DOC_ROOT."uploads/template_element_document/" . $result['file_name']);
							}
						}
					}
				}

				$announcements_size = 0;
				$announcements_result = "SELECT * FROM `announcements`";
				if ( $announcements = mysqli_query($con, $announcements_result) )  {
					while ($result = mysqli_fetch_array($announcements)) {
						if (file_exists(DOC_ROOT."uploads/announcement/" . $result['announce_file'])) {
							$announcements_size += filesize(DOC_ROOT."uploads/announcement/" . $result['announce_file']);
						}
					}
				}

				$blogdocuments_size = 0;
				$blogdocuments_result = "SELECT document_name FROM blog_documents";
				if( $blogdocuments = mysqli_query($con,$blogdocuments_result) ){
					if (mysqli_num_rows($blogdocuments) > 0) {
						while ($result = mysqli_fetch_array($blogdocuments)) {

							if (file_exists(DOC_ROOT."uploads/blogdocuments/" . $result['document_name'])) {
								$blogdocuments_size += filesize(DOC_ROOT."uploads/blogdocuments/" . $result['document_name']);
							}
						}
					}
				}

				$todolistcomment_size = 0;
				$todolistcomment_result = "SELECT file_name FROM do_list_comment_uploads ";
				if( $todolistcomment = mysqli_query($con,$todolistcomment_result) ){
					if (mysqli_num_rows($todolistcomment) > 0) {
						while ($result = mysqli_fetch_array($todolistcomment)) {

							if (file_exists(DOC_ROOT."uploads/dolist_comments/" . $result['file_name'])) {
								$todolistcomment_size += filesize(DOC_ROOT."uploads/dolist_comments/" . $result['file_name']);
							}
						}
					}
				}

				$todolistuploads_size = 0;
				$todolistuploads_result = "SELECT file_name FROM do_list_uploads ";
				if( $todolistuploads = mysqli_query($con, $todolistuploads_result) ){
					if (mysqli_num_rows($todolistuploads) > 0) {
						while ($result = mysqli_fetch_array($todolistuploads)) {

							if (file_exists(DOC_ROOT."uploads/dolist_uploads/" . $result['file_name'])) {
								$todolistuploads_size += filesize(DOC_ROOT."uploads/dolist_uploads/" . $result['file_name']);
							}
						}
					}
				}

				$elementdocuments_size = 0;
				$elementdocuments_result = "SELECT element_id,file_name FROM element_documents ";
				if( $elementdocuments = mysqli_query($con, $elementdocuments_result) ){
					if (mysqli_num_rows($elementdocuments) > 0) {
						while ($result = mysqli_fetch_array($elementdocuments)) {

							if (file_exists(DOC_ROOT."uploads/element_documents/" . $result['element_id'] . "/" . $result['file_name'])) {
								$elementdocuments_size += filesize(DOC_ROOT."uploads/element_documents/" . $result['element_id'] . "/" . $result['file_name']);
							}
						}
					}
				}

				$feedbackattachments_size = 0;
				$feedbackattachments_result = "SELECT file_name FROM feedback_attachments ";
				if( $feedbackattachments = mysqli_query($con, $feedbackattachments_result) ){
					if (mysqli_num_rows($feedbackattachments) > 0) {
						while ($result = mysqli_fetch_array($feedbackattachments)) {

							if (file_exists(DOC_ROOT."uploads/element_feedback_images/" . $result['file_name'])) {
								$feedbackattachments_size += filesize(DOC_ROOT."uploads/element_feedback_images/" . $result['file_name']);
							}
						}
					}
				}

				$todocommentuploads_size = 0;
				$todocommentuploads_result = "SELECT document FROM to_do_comment_uploads ";
				if( $todocommentuploads = mysqli_query($con, $todocommentuploads_result) ){
					if (mysqli_num_rows($todocommentuploads) > 0) {
						while ($result = mysqli_fetch_array($todocommentuploads)) {

							if (file_exists(DOC_ROOT."uploads/dolist_comments/" . $result['document'])) {
								$todocommentuploads_size += filesize(DOC_ROOT."uploads/dolist_comments/" . $result['document']);
							}
						}
					}
				}


				$todosubcommentuploads_size = 0;
				$todosubcommentuploads_result = "SELECT document FROM to_do_sub_comment_uploads ";
				if( $todosubcommentuploads = mysqli_query($con, $todosubcommentuploads_result) ){
					if (mysqli_num_rows($todosubcommentuploads) > 0) {
						while ($result = mysqli_fetch_array($todosubcommentuploads)) {

							if (file_exists(DOC_ROOT."uploads/dolist_comments/" . $result['document'])) {
								$todosubcommentuploads_size += filesize(DOC_ROOT."uploads/dolist_comments/" . $result['document']);
							}
						}
					}
				}

				$todosubuploads_size = 0;
				$todosubuploads_result = "SELECT document FROM to_do_sub_uploads ";
				if($todosubuploads = mysqli_query($con, $todosubuploads_result) ){
					if (mysqli_num_rows($todosubuploads) > 0) {
						while ($result = mysqli_fetch_array($todosubuploads)) {

							if (file_exists(DOC_ROOT."uploads/dolist_uploads/" . $result['document'])) {
								$todosubuploads_size += filesize(DOC_ROOT."uploads/dolist_uploads/" . $result['document']);
							}
						}
					}
				}

				$todouploads_size = 0;
				$todouploads_result = "SELECT document FROM to_do_uploads ";
				if($todouploads = mysqli_query($con, $todouploads_result) ){
					if (mysqli_num_rows($todouploads) > 0) {
						while ($result = mysqli_fetch_array($todouploads)) {

							if (file_exists(DOC_ROOT."uploads/dolist_uploads/" . $result['document'])) {
								$todouploads_size += filesize(DOC_ROOT."uploads/dolist_uploads/" . $result['document']);
							}
						}
					}
				}

				$userprofilepic_size = 0;
				$userdocumentpic_size = 0;
				$usermenupic_size = 0;
				//profile_pic, document_pic, menu_pic
				$userdetails_result = "SELECT profile_pic, document_pic, menu_pic FROM user_details ";
				if( $userdetails = mysqli_query($con, $userdetails_result) ){
					if (mysqli_num_rows($userdetails) > 0) {
						while ($result = mysqli_fetch_array($userdetails)) {

							if (file_exists(DOC_ROOT."uploads/user_images/" . $result['profile_pic'])) {
								$userprofilepic_size += filesize(DOC_ROOT."uploads/user_images/" . $result['profile_pic']);
							}
							if (file_exists(DOC_ROOT."uploads/user_images/" . $result['document_pic'])) {
								$userdocumentpic_size += filesize(DOC_ROOT."uploads/user_images/" . $result['document_pic']);
							}
							if (file_exists(DOC_ROOT."uploads/user_images/" . $result['menu_pic'])) {
								$usermenupic_size += filesize(DOC_ROOT."uploads/user_images/" . $result['menu_pic']);
							}
						}
					}
				}

				$wikipagecommentdocuments_size = 0;
				$wikipagecommentdocuments_result = "SELECT document_name FROM wiki_page_comment_documents ";
				if( $wikipagecommentdocuments = mysqli_query($con, $wikipagecommentdocuments_result)  ){
					if (mysqli_num_rows($wikipagecommentdocuments) > 0) {
						while ($result = mysqli_fetch_array($wikipagecommentdocuments)) {

							if (isset($result['document'] ) && file_exists(DOC_ROOT."uploads/wiki_page_document/" . $result['document'])) {
								$wikipagecommentdocuments_size += filesize(DOC_ROOT."uploads/wiki_page_document/" . $result['document']);
							}
						}
					}
				}

				$kulsize = $elementrelationdocument_size + $announcements_size + $blogdocuments_size + $todolistcomment_size + $elementdocuments_size + $todolistuploads_size + $feedbackattachments_size + $todocommentuploads_size + $todosubcommentuploads_size + $todosubuploads_size + $todouploads_size + $userprofilepic_size + $userdocumentpic_size + $usermenupic_size + $wikipagecommentdocuments_size;

				$chatdocumentsizeMB = $this->chatSize($domain_name);
				$currentDatabasesizeMB = number_format((($kulsize + $dbsize) / 1024 / 1024), 2); // Database size in MB
				$currentDatabasesize = number_format((($kulsize + $dbsize) / 1024 / 1024 / 1024), 3); // Database size in GB
				//=================================================================

			}
			$totalSize = 0;
			$totalSize = $currentDatabasesizeMB + $chatdocumentsizeMB;
			return $totalSize.'='.$registerUserCount;
			mysqli_close($con);
			//==============================================================================

	}


	public function domain_document_unlinks($domain_name,$dbname,$dbuser,$dbpass){
		$this->autoRender = false;
		$this->layout = false;
			$con = mysqli_connect(root_host, $dbuser, $dbpass, $dbname);
			if (mysqli_connect_errno()) {
				echo "Failed to connect to MySQL: " . mysqli_connect_error();
			}
			if ($con) {

				$elementrelationdocuments_result = "SELECT file_name FROM element_relation_documents order by file_name asc ";
				if( $elementrelationdocuments = mysqli_query($con,$elementrelationdocuments_result) ){
					if (mysqli_num_rows($elementrelationdocuments) > 0) {
						while ($result = mysqli_fetch_array($elementrelationdocuments)) {
							if (file_exists(DOC_ROOT."uploads/template_element_document/" . $result['file_name'])) {
								unlink(DOC_ROOT."uploads/template_element_document/" . $result['file_name']);
							}
						}
					}
				}

				$announcements_result = "SELECT * FROM `announcements`";
				if ( $announcements = mysqli_query($con, $announcements_result) )  {
					while ($result = mysqli_fetch_array($announcements)) {
						if (file_exists(DOC_ROOT."uploads/announcement/" . $result['announce_file'])) {
							unlink(DOC_ROOT."uploads/announcement/" . $result['announce_file']);
						}
					}
				}

				$blogdocuments_result = "SELECT document_name FROM blog_documents";
				if( $blogdocuments = mysqli_query($con,$blogdocuments_result) ){
					if (mysqli_num_rows($blogdocuments) > 0) {
						while ($result = mysqli_fetch_array($blogdocuments)) {
							if (file_exists(DOC_ROOT."uploads/blogdocuments/" . $result['document_name'])) {
								unlink(DOC_ROOT."uploads/blogdocuments/" . $result['document_name']);
							}
						}
					}
				}

				$todolistcomment_result = "SELECT file_name FROM do_list_comment_uploads ";
				if( $todolistcomment = mysqli_query($con,$todolistcomment_result) ){
					if (mysqli_num_rows($todolistcomment) > 0) {
						while ($result = mysqli_fetch_array($todolistcomment)) {
							if (file_exists(DOC_ROOT."uploads/dolist_comments/" . $result['file_name'])) {
								unlink(DOC_ROOT."uploads/dolist_comments/" . $result['file_name']);
							}
						}
					}
				}

				$todolistuploads_result = "SELECT file_name FROM do_list_uploads ";
				if( $todolistuploads = mysqli_query($con, $todolistuploads_result) ){
					if (mysqli_num_rows($todolistuploads) > 0) {
						while ($result = mysqli_fetch_array($todolistuploads)) {
							if (file_exists(DOC_ROOT."uploads/dolist_uploads/" . $result['file_name'])) {
								unlink(DOC_ROOT."uploads/dolist_uploads/" . $result['file_name']);
							}
						}
					}
				}

				$elementdocuments_result = "SELECT element_id,file_name FROM element_documents ";
				if( $elementdocuments = mysqli_query($con, $elementdocuments_result) ){
					if (mysqli_num_rows($elementdocuments) > 0) {
						while ($result = mysqli_fetch_array($elementdocuments)) {
							if (file_exists(DOC_ROOT."uploads/element_documents/" . $result['element_id'] . "/" . $result['file_name'])) {
								unlink(DOC_ROOT."uploads/element_documents/" . $result['element_id'] . "/" . $result['file_name']);
							}
						}
					}
				}

				$feedbackattachments_result = "SELECT file_name FROM feedback_attachments ";
				if( $feedbackattachments = mysqli_query($con, $feedbackattachments_result) ){
					if (mysqli_num_rows($feedbackattachments) > 0) {
						while ($result = mysqli_fetch_array($feedbackattachments)) {
							if (file_exists(DOC_ROOT."uploads/element_feedback_images/" . $result['file_name'])) {
								unlink(DOC_ROOT."uploads/element_feedback_images/" . $result['file_name']);
							}
						}
					}
				}

				$todocommentuploads_result = "SELECT document FROM to_do_comment_uploads ";
				if( $todocommentuploads = mysqli_query($con, $todocommentuploads_result) ){
					if (mysqli_num_rows($todocommentuploads) > 0) {
						while ($result = mysqli_fetch_array($todocommentuploads)) {
							if (file_exists(DOC_ROOT."uploads/dolist_comments/" . $result['document'])) {
								unlink(DOC_ROOT."uploads/dolist_comments/" . $result['document']);
							}
						}
					}
				}

				$todosubcommentuploads_result = "SELECT document FROM to_do_sub_comment_uploads ";
				if( $todosubcommentuploads = mysqli_query($con, $todosubcommentuploads_result) ){
					if (mysqli_num_rows($todosubcommentuploads) > 0) {
						while ($result = mysqli_fetch_array($todosubcommentuploads)) {
							if (file_exists(DOC_ROOT."uploads/dolist_comments/" . $result['document'])) {
								unlink(DOC_ROOT."uploads/dolist_comments/" . $result['document']);
							}
						}
					}
				}

				$todosubuploads_result = "SELECT document FROM to_do_sub_uploads ";
				if($todosubuploads = mysqli_query($con, $todosubuploads_result) ){
					if (mysqli_num_rows($todosubuploads) > 0) {
						while ($result = mysqli_fetch_array($todosubuploads)) {
							if (file_exists(DOC_ROOT."uploads/dolist_uploads/" . $result['document'])) {
								unlink(DOC_ROOT."uploads/dolist_uploads/" . $result['document']);
							}
						}
					}
				}

				$todouploads_result = "SELECT document FROM to_do_uploads ";
				if($todouploads = mysqli_query($con, $todouploads_result) ){
					if (mysqli_num_rows($todouploads) > 0) {
						while ($result = mysqli_fetch_array($todouploads)) {
							if (file_exists(DOC_ROOT."uploads/dolist_uploads/" . $result['document'])) {
								unlink(DOC_ROOT."uploads/dolist_uploads/" . $result['document']);
							}
						}
					}
				}

				$userdetails_result = "SELECT profile_pic, document_pic, menu_pic FROM user_details ";
				if( $userdetails = mysqli_query($con, $userdetails_result) ){
					if (mysqli_num_rows($userdetails) > 0) {
						while ($result = mysqli_fetch_array($userdetails)) {
							if (file_exists(DOC_ROOT."uploads/user_images/" . $result['profile_pic'])) {
								unlink(DOC_ROOT."uploads/user_images/" . $result['profile_pic']);
							}
							if (file_exists(DOC_ROOT."uploads/user_images/" . $result['document_pic'])) {
								unlink(DOC_ROOT."uploads/user_images/" . $result['document_pic']);
							}
							if (file_exists(DOC_ROOT."uploads/user_images/" . $result['menu_pic'])) {
								unlink(DOC_ROOT."uploads/user_images/" . $result['menu_pic']);
							}
						}
					}
				}

				$wikipagecommentdocuments_result = "SELECT document_name FROM wiki_page_comment_documents ";
				if( $wikipagecommentdocuments = mysqli_query($con, $wikipagecommentdocuments_result)  ){
					if (mysqli_num_rows($wikipagecommentdocuments) > 0) {
						while ($result = mysqli_fetch_array($wikipagecommentdocuments)) {
							if (file_exists(DOC_ROOT."uploads/wiki_page_document/" . $result['document'])) {
								unlink(DOC_ROOT."uploads/wiki_page_document/" . $result['document']);
							}
						}
					}
				}
			}
			//==============================================================================

	}

	//create temp file for subdomain
	public function create_subdomain_temp_file($subdomain_id = null){
		$this->autoRender = false;
		$this->layout = false;

		App::import("Model", "OrgUserSettingJeera");
		$OrgUserSettingJeera = new OrgUserSettingJeera();

		if( isset($subdomain_id) && !empty($subdomain_id)  ){
			// Get Domain details from main Organisation
			$listDomains = $OrgUserSettingJeera->find('all',array('conditions'=>array('OrgSetting.id'=>$subdomain_id) ) );
		} else {
			// Get Domain details from main Organisation
			$listDomains = $OrgUserSettingJeera->find('all');
		}

		if(!is_dir($_SERVER['DOCUMENT_ROOT'].'/app/domain_dbs/temp')) {
		  mkdir($_SERVER['DOCUMENT_ROOT'].'/app/domain_dbs/temp');
		}
		if( isset($listDomains) && !empty($listDomains) ){
			foreach( $listDomains as $domainDetails ){



				if( file_exists($_SERVER['DOCUMENT_ROOT'].'/app/domain_dbs/temp/' . $domainDetails['OrgSetting']['subdomain'] . '_tmp.php') ){
					unlink($_SERVER['DOCUMENT_ROOT'].'/app/domain_dbs/temp/' . $domainDetails['OrgSetting']['subdomain'] . '_tmp.php');
				}


				$domain_name = $domainDetails['OrgSetting']['subdomain'];
				$domain_userid = $domainDetails['OrgSetting']['user_id'];
				$domain_dbname = $domainDetails['OrgSetting']['dbname'];
				$domain_dbuser = $domainDetails['OrgSetting']['dbuser'];
				$domain_dbpass = $domainDetails['OrgSetting']['dbpass'];
				$domain_db_setup = $domainDetails['OrgSetting']['db_setup'];
				$domain_status = $domainDetails['OrgSetting']['status'];
				$domain_start_date = $domainDetails['OrgSetting']['start_date'];
				$domain_end_date = $domainDetails['OrgSetting']['end_date'];
				$domain_allowed_space = $domainDetails['OrgSetting']['allowed_space'];
				$domain_license = $domainDetails['OrgSetting']['license'];
				$domain_apilicense = $domainDetails['OrgSetting']['apilicense'];
				$domain_jeera_version = $domainDetails['OrgSetting']['jeera_version'];
				$domain_mm_port = $domainDetails['OrgSetting']['mm_port'];
				$domain_mongo_port = $domainDetails['OrgSetting']['mongo_port'];
				$domain_prmry_sts = $domainDetails['OrgSetting']['prmry_sts'];
				$domain_apisdk_status = $domainDetails['OrgSetting']['apisdk_status'];

				$configPath = $_SERVER['DOCUMENT_ROOT'].'/app/domain_dbs/temp/' . $domain_name . '_tmp.php';
				fopen($configPath, 'w') or die('Cannot open file:  ' . $configPath);

				if (file_exists($configPath)) {

					$documentChatSize = $this->domain_document_size($domain_name,$domain_dbname,root_dbuser,root_dbpass);

					$register_size = explode('=',$documentChatSize);

					$siteurl = 'https://'.$domain_name.WEBDOMAIN;
					$configData = file_get_contents($configPath);
					$newtxt = "<?php ". PHP_EOL;
					$newtxt .= '$domain_url = "'.$siteurl.'";'. PHP_EOL;
					$newtxt .= '$domain_name = "'.$domain_name.'";'. PHP_EOL;
					$newtxt .= '$domain_userid = "'.$domain_userid.'";'. PHP_EOL;
					$newtxt .= '$domain_dbname = "'.$domain_dbname.'";'. PHP_EOL;
					$newtxt .= '$domain_dbuser = "'.$domain_dbuser.'";'. PHP_EOL;
					$newtxt .= '$'."domain_dbpass = '".$domain_dbpass."';". PHP_EOL;
					$newtxt .= '$domain_db_setup = "'.$domain_db_setup.'";'. PHP_EOL;
					$newtxt .= '$domain_status = "'.$domain_status.'";'. PHP_EOL;
					$newtxt .= '$domain_start_date = "'.$domain_start_date.'";'. PHP_EOL;
					$newtxt .= '$domain_end_date = "'.$domain_end_date.'";'. PHP_EOL;
					$newtxt .= '$domain_apilicense = "'.$domain_apilicense.'";'. PHP_EOL;
					$newtxt .= '$domain_jeeraversion = "'.$domain_jeera_version.'";'. PHP_EOL;
					$newtxt .= '$domain_mmport = "'.$domain_mm_port.'";'. PHP_EOL;
					$newtxt .= '$domain_mongo_port = "'.$domain_mongo_port.'";'. PHP_EOL;

					$newtxt .= '$domain_user_license = "'.$domain_license.'";'. PHP_EOL;
					$newtxt .= '$registered_user_license = "'.$register_size[1].'";'. PHP_EOL;

					$newtxt .= '$domain_allowedspace = "'.$domain_allowed_space.'GB";'. PHP_EOL;
					//DB Size, Document and Chat size
					$newtxt .= '$domain_consumed_space = "'.$register_size[0].'MB";'. PHP_EOL;

					//$newtxt .= '$DOMAIN_PRYSTS = "'.$domain_prmry_sts.'";'. PHP_EOL;
					//$newtxt .= '$DOMAIN_SDKSTATUS = "'.$domain_apisdk_status.'";'. PHP_EOL;

					$newtxt .= "?>";

					file_put_contents($configPath, $newtxt.$configData);

				}

			}
		}
	}

	public function resend_activation_email(){

		if ($this->request->isAjax()) {

			$this->layout = false;
			$response = ['success' => false];

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				$user_id = $post['user'];

				$this->User->id = $user_id;
				$this->User->saveField('activation_time', date('Y-m-d h:i:s'));
				$this->CommonEmail->user_activation(['user_id' => $user_id]);
				$response['success'] = true;
				//
				// $this->Session->setFlash('Activation email sent.', 'success');

			}

			echo json_encode($response);
			exit();

		}
	}

	public function cron_delete_skills($subdomain_id = null ){
		$this->autoRender = false;
		$this->layout = false;
		$sql = "DELETE skill_details FROM skill_details
		LEFT JOIN user_skills
		ON skill_details.user_id = user_skills.user_id AND skill_details.skill_id = user_skills.skill_id
		WHERE user_skills.user_id IS NULL";
		$this->User->query($sql);
	}

	public function skillDeleteCron() {

		$link = mysqli_connect(root_host, root_dbuser, root_dbpass,root_dbname);
		if (mysqli_connect_errno()) {
			echo "Failed to connect to MySQL: " . mysqli_connect_error();
		}
		$sql = mysqli_query($link,"select subdomain from org_settings");
		while ($result = mysqli_fetch_array($sql)) {

			$ch = curl_init();

			// set URL and other appropriate options
			curl_setopt($ch, CURLOPT_URL, "https://" . $result['subdomain'] . WEBDOMAIN."/organisations/cron_delete_skills");
			curl_setopt($ch, CURLOPT_HEADER, 0);

			// grab URL and pass it to the browser
			curl_exec($ch);

			// close cURL resource, and free up system resources
			curl_close($ch);
		}

		mysql_close($link);

		//5	0	*	*	*	wget -O /dev/null https://jeera.ideascast.com/projects/projectScheduleOverdueEmailCron

	}

	public function appearance() {

		$this->layout = 'inner';
		$this->set('title_for_layout', __('Appearance', true));
		$viewData['page_heading'] = 'Appearance';
		$viewData['page_subheading'] = 'Change the look of your system';

		$this->set('viewData', $viewData);


		$id = $this->Session->read('Auth.User.id');
		$detid = $this->Session->read('Auth.User.UserDetail.id');


		/* if (($this->Session->read('Auth.User.role_id') == 2 && $this->Session->read('Auth.User.UserDetail.administrator') != 1)  && LOCALIP != $_SERVER['SERVER_ADDR'] ) {

				$this->redirect(array('controller' => 'projects', 'action' => 'lists'));
		} */

		if (($this->Session->read('Auth.User.role_id') == 2)  && LOCALIP != $_SERVER['SERVER_ADDR'] ) {

			$this->redirect(array('controller' => 'projects', 'action' => 'lists'));
		}
		if( $this->Session->read('Auth.User.role_id') == 1 ){
				$this->redirect(['controller' => 'dashboard','action' => 'index']);
		}


		//pr($this->Session->read('Auth.User'));
		//	$this->layout= false;
		App::import("Model", "User");
		$this->User = new User();

		$this->User->id = $this->Session->read('Auth.User.id');

		if (!$this->User->exists()) {
			$this->Session->setFlash(__('Invalid User.'), 'error');
			die('error');
		}

		if ($this->request->is('post') || $this->request->is('put')) {

			if ($this->UserDetail->save($this->request->data)) {


				//$this->Session->write('Auth', $users);

				 $this->Session->setFlash(__('Saved successfully.'), 'success');

			}
			//pr($this->UserDetail->validationErrors);die;
		} else {
			$this->request->data = $this->User->read(null, $id);

		}

		$viewData['crumb'] = [
			'last' => [
				'data' => [
					'title' => "Appearance",
					'data-original-title' => "Appearance",
				],
			],
		];
		$this->set('crumb',$viewData['crumb']);



	}


	public function remove_profile_pic() {
		//data[UserDetail][profile_pic] data[UserDetail][document_pic]

		if ($this->request->is('ajax')) {
			$this->layout = false;
			$this->autoRender = false;
			$this->User->id = $this->Session->read('Auth.User.id');
			$this->UserDetail->id = $this->Session->read('Auth.User.UserDetail.id');
			$this->User->id = $this->Session->read('Auth.User.id');
			$id = $this->Session->read('Auth.User.id');
			$detid = $this->Session->read('Auth.User.UserDetail.id');
			if (  $this->data['id'] == 'UserDetailMenuPic') {
				$this->request->data['UserDetail']['menu_pic'] = '';
			}
			//pr($this->request->data); die;
			$userDetail = $this->objView->loadHelper('ViewModel')->get_user($id, null, 1);

			if ($this->UserDetail->save($this->request->data)) {
				 if ( $this->data['id'] == 'UserDetailMenuPic') {
					$docimg =  $userDetail['UserDetail']['menu_pic'];
					//$docimg = $this->Session->read('Auth.User.UserDetail.document_pic');
					$docimgs = SITEURL . USER_PIC_PATH . $docimg;
					if (!empty($docimg) && file_exists(USER_PIC_PATH . $docimg)) {
						$docimgs = WWW_ROOT . USER_PIC_PATH . $docimg;
						unlink($docimgs);

					}
				}




				//$this->Session->write('Auth', $users);
				//$this->Session->setFlash(__('The Profile has been updated successfully.'), 'success');
				die('success');
			}
		}
	}

	public function getDepartment(){

		$data = $this->Department->find('first', ['conditions'=> ['Department.name'=>'Other'], 'fields'=>['Department.id', 'Department.name'] ]  );

		if( isset($data) && !empty($data) ){
			return $data;
		}

	}

	public function getDepartmentbyid($id = null){

		if( isset($id) && !empty($id) ){
			$query = "select name from departments where id = $id";
			$dname =  ClassRegistry::init('Department')->query($query);
			if( isset( $dname ) && !empty($dname[0]['departments']['name']) ){
				return $dname[0]['departments']['name'];
			}
		}
	}


	public function opuscast_name(){

		$this->autoRender = false;

		return json_encode(get_theme_data());
	}

	public function opuscast_version(){

		$this->autoRender = false;

		return DOMAIN_JEERA_VERSION;
	}

	// ADMIN LISTING //
	public function listings() {

		$this->layout = 'inner';
		$this->set('title_for_layout', __('Administration: Lists', true));
		$viewData['page_heading'] = 'Lists';
		$viewData['page_subheading'] = 'Customize application pick lists';

		$viewData['crumb'] = [
			'last' => [
				'data' => [
					'title' => "Lists",
					'data-original-title' => "Lists",
				],
			],
		];

		$viewData['program_ists'] = $this->objView->loadHelper('Scratch')->program_type_list();
		$viewData['lists'] = $this->objView->loadHelper('Scratch')->project_type_list();
		$viewData['cost_type_list'] = $this->objView->loadHelper('Scratch')->cost_type_list();
		$viewData['currency_list'] = $this->objView->loadHelper('Scratch')->currency_list();
		$viewData['task_type_list'] = $this->objView->loadHelper('Scratch')->task_type_list();
		$viewData['org_lists'] = $this->objView->loadHelper('Scratch')->org_type_list();
		$viewData['loc_lists'] = $this->objView->loadHelper('Scratch')->loc_type_list();
		$viewData['story_lists'] = $this->objView->loadHelper('Scratch')->story_type_list();
		$viewData['risk_type_list'] = $this->objView->loadHelper('Scratch')->risk_type_list();
		$this->set($viewData);

	}

	public function program_type_save() {
		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$response = [
				'success' => false,
				'msg' => ''
			];
			if ($this->request->is('post') || $this->request->is('put')) {
				$this->loadModel('ProgramType');
				$post = $this->request->data;
				// pr($post, 1);
				$type_id = (isset($post['id']) && !empty($post['id'])) ? $post['id'] : null;
				$type_title = (isset($post['title']) && !empty($post['title'])) ?  Sanitize::escape($post['title']) : null;

				if(isset($post['title']) && !empty($post['title'])) {
					if(isset($post['id']) && !empty($post['id'])) {
						// update
						$query = "update program_types types set types.type = '$type_title' WHERE types.id = $type_id";
						$this->ProgramType->query($query);
						$response['success'] = true;
					}
					else{
						// insert
						$query = "INSERT INTO program_types set type = '$type_title'";
						$this->ProgramType->query($query);
						$response['success'] = true;
					}
				}
				else{
					$response['msg'] = 'Title is required';
				}

			}
			echo json_encode($response);
			exit;
		}
	}

	public function program_type_list() {
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				// pr($post, 1);
				$program_ists = $this->objView->loadHelper('Scratch')->program_type_list(null, false);
				$this->set('program_ists', $program_ists);
			}
			$this->render('/Organisations/partial/program_type_list');
		}
	}

	public function program_type_reassign($id = null) {
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			if ($this->request->is('post') || $this->request->is('put')) {
				$response = ['success' => false];
				$post = $this->request->data;
				// pr($post, 1);
				$this->loadModel('ProgramType');
				$this->loadModel('Program');
				$this->Program->updateAll(array('type_id' => $post['update_id']), array('type_id' => $post['delete_id']));
				echo json_encode(['success' => true]);
				exit();
			}
			$this->set('id', $id);
			$this->render('/Organisations/partial/program_type_reassign');
		}
	}

	public function program_type_delete() {
		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$response = [
				'success' => false
			];
			if ($this->request->is('post') || $this->request->is('put')) {
				$this->loadModel('ProgramType');
				$post = $this->request->data;
				// pr($post, 1);
				$type_id = (isset($post['id']) && !empty($post['id'])) ? $post['id'] : null;

				if(isset($post['id']) && !empty($post['id'])) {
					// delete
					$query = "DELETE FROM program_types WHERE id = $type_id";
					$this->ProgramType->query($query);
					$response['success'] = true;
				}

			}
			echo json_encode($response);
			exit;
		}
	}



	public function cost_type_save() {
		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$response = [
				'success' => false,
				'msg' => ''
			];
			if ($this->request->is('post') || $this->request->is('put')) {
				$this->loadModel('CostType');
				$post = $this->request->data;
				// pr($post, 1);
				$type_id = (isset($post['id']) && !empty($post['id'])) ? $post['id'] : null;
				$type_title = (isset($post['title']) && !empty($post['title'])) ?  Sanitize::escape($post['title']) : null;

				if(isset($post['title']) && !empty($post['title'])) {
					if(isset($post['id']) && !empty($post['id'])) {
						// update
						$query = "update cost_types types set types.type = '$type_title' WHERE types.id = $type_id";
						$this->CostType->query($query);
						$response['success'] = true;
					}
					else{
						// insert
						$query = "INSERT INTO cost_types set type = '$type_title'";
						$this->CostType->query($query);
						$response['success'] = true;
					}
				}
				else{
					$response['msg'] = 'Title is required';
				}

			}
			echo json_encode($response);
			exit;
		}
	}

	public function cost_type_list() {
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				// pr($post, 1);
				$cost_type_list = $this->objView->loadHelper('Scratch')->cost_type_list(null, false);
				$this->set('cost_type_list', $cost_type_list);
			}
			$this->render('/Organisations/partial/cost_type_list');
		}
	}

	public function cost_type_reassign($id = null) {
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			if ($this->request->is('post') || $this->request->is('put')) {
				$response = ['success' => false];
				$post = $this->request->data;
				// pr($post, 1);
				$this->loadModel('CostType');
				$this->loadModel('ElementCost');
				$this->ElementCost->updateAll(array('cost_type_id' => $post['update_id']), array('cost_type_id' => $post['delete_id']));
				echo json_encode(['success' => true]);
				exit();
			}
			$this->set('id', $id);
			$this->render('/Organisations/partial/cost_type_reassign');
		}
	}

	public function cost_type_delete() {
		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$response = [
				'success' => false
			];
			if ($this->request->is('post') || $this->request->is('put')) {
				$this->loadModel('CostType');
				$post = $this->request->data;
				// pr($post, 1);
				$type_id = (isset($post['id']) && !empty($post['id'])) ? $post['id'] : null;

				if(isset($post['id']) && !empty($post['id'])) {
					// delete
					$query = "DELETE FROM cost_types WHERE id = $type_id";
					$this->CostType->query($query);
					$response['success'] = true;
				}

			}
			echo json_encode($response);
			exit;
		}
	}


	public function project_type_list() {
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				// pr($post, 1);
				$params = [];
				$params['order'] = (isset($post['order']) && !empty($post['order'])) ? $post['order'] : null;
				$params['field'] = (isset($post['field']) && !empty($post['field'])) ? $post['field'] : null;
				$lists = $this->objView->loadHelper('Scratch')->project_type_list(null, false, $params);
				$this->set('lists', $lists);
			}
			$this->render('/Organisations/partial/project_type_list');
		}
	}

	public function project_type_sort() {
		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$response = [
				'success' => false
			];
			if ($this->request->is('post') || $this->request->is('put')) {
				$this->loadModel('Aligned');
				$post = $this->request->data;

				$type_ids = $post['type_id'];
				foreach ($type_ids as $index => $type_id) {
					$order = $index + 1;
					if ($type_id != '') {
						$query = "update aligneds align set align.sort_order = $order WHERE align.id = $type_id";
						$this->Aligned->query($query);
					}
				}
				$response['success'] = true;
			}
			echo json_encode($response);
			exit;
		}
	}

	public function project_type_save() {
		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$response = [
				'success' => false,
				'msg' => ''
			];
			if ($this->request->is('post') || $this->request->is('put')) {
				$this->loadModel('Aligned');
				$post = $this->request->data;
				$type_id = (isset($post['id']) && !empty($post['id'])) ? $post['id'] : null;
				$type_title = (isset($post['title']) && !empty($post['title'])) ?  Sanitize::escape($post['title']) : null;

				if(isset($post['title']) && !empty($post['title'])) {
					if(isset($post['id']) && !empty($post['id'])) {
						// update
						$query = "update aligneds align set align.title = '$type_title' WHERE align.id = $type_id";
						$this->Aligned->query($query);
						$response['success'] = true;
					}
					else{
						// insert
						$date = date('Y-m-d');
						$query = "select MAX(sort_order) AS max_sort from aligneds";
						$max = $this->Aligned->query($query);
						$max = (!empty($max[0][0]['max_sort'])) ? $max[0][0]['max_sort'] + 1 : 1;
						$query = "INSERT INTO aligneds set title = '$type_title', created = '$date', modified = '$date', sort_order = $max";
						$this->Aligned->query($query);
						$response['success'] = true;
					}
				}
				else{
					$response['msg'] = 'Title is required';
				}

			}
			echo json_encode($response);
			exit;
		}
	}

	public function project_type_delete() {
		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$response = [
				'success' => false
			];
			if ($this->request->is('post') || $this->request->is('put')) {
				$this->loadModel('Aligned');
				$post = $this->request->data;
				$type_id = (isset($post['id']) && !empty($post['id'])) ? $post['id'] : null;

				if(isset($post['id']) && !empty($post['id'])) {
					// delete
					$query = "DELETE FROM aligneds WHERE id = $type_id";
					$this->Aligned->query($query);
					$response['success'] = true;
				}

			}
			echo json_encode($response);
			exit;
		}
	}

	public function project_type_reassign($id = null) {
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			if ($this->request->is('post') || $this->request->is('put')) {
				$response = ['success' => false];
				$post = $this->request->data;
				// pr($post, 1);
				$this->loadModel('Aligned');
				$this->loadModel('Project');
				$this->Project->updateAll(array('aligned_id' => $post['update_id']), array('aligned_id' => $post['delete_id']));
				/*if ($this->Aligned->delete(['Aligned.id' => $post['delete_id']], false)) {
					echo json_encode(['success' => true]);
					exit();
				}*/
				echo json_encode(['success' => true]);
				exit();
			}
			$this->set('id', $id);
			$this->render('/Organisations/partial/project_type_reassign');
		}
	}

	public function currency_list() {
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				// pr($post, 1);
				$params = [];
				$lists = $this->objView->loadHelper('Scratch')->currency_list(null, false, $params);
				$this->set('currency_list', $lists);
			}
			$this->render('/Organisations/partial/currency_list');
		}
	}

	public function currency_save() {
		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$response = [
				'success' => false,
				'msg' => ''
			];
			if ($this->request->is('post') || $this->request->is('put')) {
				$this->loadModel('Currency');
				$post = $this->request->data;
				$id = (isset($post['id']) && !empty($post['id'])) ? $post['id'] : null;
				$title = (isset($post['title']) && !empty($post['title'])) ? Sanitize::escape($post['title']) : null;
				$code = (isset($post['code']) && !empty($post['code'])) ? Sanitize::escape($post['code']) : null;
				$status = (isset($post['status']) && !empty($post['status'])) ? $post['status'] : 0;

				if( (isset($post['title']) && !empty($post['title'])) && (isset($post['code']) && !empty($post['code'])) ) {
					if(isset($post['id']) && !empty($post['id'])) {
						// update
						$query = "update currencies curr set curr.name = '$title', curr.sign = '$code', curr.status = '$status' WHERE curr.id = $id";
						$this->Currency->query($query);
						$response['success'] = true;
					}
					else{
						// insert
						$query = "INSERT INTO currencies set name = '$title', sign = '$code', status = '$status'";
						$this->Currency->query($query);
						$response['success'] = true;
					}
				}
				else{
					$response['msg'] = 'Name and Code are required';
				}

			}
			echo json_encode($response);
			exit;
		}
	}

	public function currency_delete() {
		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$response = [
				'success' => false
			];
			if ($this->request->is('post') || $this->request->is('put')) {
				$this->loadModel('Currency');
				$post = $this->request->data;
				$type_id = (isset($post['id']) && !empty($post['id'])) ? $post['id'] : null;

				if(isset($post['id']) && !empty($post['id'])) {
					// delete
					$query = "DELETE FROM currencies WHERE id = $type_id";
					$this->Currency->query($query);
					$response['success'] = true;
				}

			}
			echo json_encode($response);
			exit;
		}
	}

	public function currency_reassign($id = null) {
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			if ($this->request->is('post') || $this->request->is('put')) {
				$response = ['success' => false];
				$post = $this->request->data;
				// pr($post, 1);
				$this->loadModel('Project');
				$this->Project->updateAll(array('currency_id' => $post['update_id']), array('currency_id' => $post['delete_id']));
				echo json_encode(['success' => true]);
				exit();
			}
			$this->set('id', $id);
			$this->render('/Organisations/partial/currency_reassign');
		}
	}

	public function task_type_list() {
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				// pr($post, 1);
				$params = [];
				$lists = $this->objView->loadHelper('Scratch')->task_type_list();
				$this->set('task_type_list', $lists);
			}
			$this->render('/Organisations/partial/task_type_list');
		}
	}

	public function task_type_save() {
		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$response = [
				'success' => false,
				'msg' => ''
			];
			if ($this->request->is('post') || $this->request->is('put')) {
				$this->loadModel('ProjectType');
				$post = $this->request->data;

				$id = (isset($post['id']) && !empty($post['id'])) ? $post['id'] : null;
				$title = (isset($post['title']) && !empty($post['title'])) ? Sanitize::escape($post['title']) : null;

				if( isset($post['title']) && !empty($post['title']) ) {
					$date = date('Y-m-d');
					if(isset($post['id']) && !empty($post['id'])) {
						// update
						$query = "update project_types pt set pt.title = '$title', modified = '$date' WHERE pt.id = $id";
						$this->ProjectType->query($query);
						$response['success'] = true;
					}
					else{
						// insert
						$query = "INSERT INTO project_types set title = '$title', created = '$date', modified = '$date'";
						$this->ProjectType->query($query);
						$response['success'] = true;
					}
				}
				else{
					$response['msg'] = 'Title is required';
				}

			}
			echo json_encode($response);
			exit;
		}
	}

	public function task_type_delete() {
		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$response = [
				'success' => false
			];
			if ($this->request->is('post') || $this->request->is('put')) {
				$this->loadModel('ProjectType');
				$post = $this->request->data;
				$type_id = (isset($post['id']) && !empty($post['id'])) ? $post['id'] : null;

				if(isset($post['id']) && !empty($post['id'])) {
					// delete
					$query = "DELETE FROM project_types WHERE id = $type_id";
					$this->ProjectType->query($query);
					$response['success'] = true;
				}

			}
			echo json_encode($response);
			exit;
		}
	}


	public function org_type_list() {
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				// pr($post, 1);
				$params = [];
				$lists = $this->objView->loadHelper('Scratch')->org_type_list(null, false);
				$this->set('org_lists', $lists);
			}
			$this->render('/Organisations/partial/org_type_list');
		}
	}

	public function org_type_save() {
		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$response = [
				'success' => false,
				'msg' => ''
			];
			if ($this->request->is('post') || $this->request->is('put')) {
				$this->loadModel('Currency');
				$post = $this->request->data;
				$id = (isset($post['id']) && !empty($post['id'])) ? $post['id'] : null;
				$title = (isset($post['title']) && !empty($post['title'])) ? Sanitize::escape($post['title']) : null;

				if( isset($post['title']) && !empty($post['title']) ) {
					if(isset($post['id']) && !empty($post['id'])) {
						// update
						$query = "update organization_types ot set ot.type = '$title' WHERE ot.id = $id";
						$this->Currency->query($query);
						$response['success'] = true;
					}
					else{
						// insert
						$query = "INSERT INTO organization_types set type = '$title'";
						$this->Currency->query($query);
						$response['success'] = true;
					}
				}
				else{
					$response['msg'] = 'Type title is required';
				}

			}
			echo json_encode($response);
			exit;
		}
	}

	public function org_type_delete() {
		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$response = [
				'success' => false
			];
			if ($this->request->is('post') || $this->request->is('put')) {
				$this->loadModel('Currency');
				$post = $this->request->data;
				$type_id = (isset($post['id']) && !empty($post['id'])) ? $post['id'] : null;

				if(isset($post['id']) && !empty($post['id'])) {
					// delete
					$query = "DELETE FROM organization_types WHERE id = $type_id";
					$this->Currency->query($query);
					$response['success'] = true;
				}

			}
			echo json_encode($response);
			exit;
		}
	}

	public function org_reassign($id = null) {
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			if ($this->request->is('post') || $this->request->is('put')) {
				$response = ['success' => false];
				$post = $this->request->data;
				// pr($post, 1);
				$this->loadModel('Organization');
				$this->Organization->updateAll(array('type_id' => $post['update_id']), array('type_id' => $post['delete_id']));
				echo json_encode(['success' => true]);
				exit();
			}
			$this->set('id', $id);
			$this->render('/Organisations/partial/org_reassign');
		}
	}


	public function loc_type_list() {
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				// pr($post, 1);
				$params = [];
				$lists = $this->objView->loadHelper('Scratch')->loc_type_list(null, false);
				$this->set('loc_lists', $lists);
			}
			$this->render('/Organisations/partial/loc_type_list');
		}
	}

	public function loc_type_save() {
		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$response = [
				'success' => false,
				'msg' => ''
			];
			if ($this->request->is('post') || $this->request->is('put')) {
				$this->loadModel('Currency');
				$post = $this->request->data;
				$id = (isset($post['id']) && !empty($post['id'])) ? $post['id'] : null;
				$title = (isset($post['title']) && !empty($post['title'])) ? Sanitize::escape($post['title']) : null;

				if( isset($post['title']) && !empty($post['title']) ) {
					if(isset($post['id']) && !empty($post['id'])) {
						// update
						$query = "update location_types ot set ot.type = '$title' WHERE ot.id = $id";
						$this->Currency->query($query);
						$response['success'] = true;
					}
					else{
						// insert
						$query = "INSERT INTO location_types set type = '$title'";
						$this->Currency->query($query);
						$response['success'] = true;
					}
				}
				else{
					$response['msg'] = 'Type title is required';
				}

			}
			echo json_encode($response);
			exit;
		}
	}

	public function loc_type_delete() {
		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$response = [
				'success' => false
			];
			if ($this->request->is('post') || $this->request->is('put')) {
				$this->loadModel('Currency');
				$post = $this->request->data;
				$type_id = (isset($post['id']) && !empty($post['id'])) ? $post['id'] : null;

				if(isset($post['id']) && !empty($post['id'])) {
					// delete
					$query = "DELETE FROM location_types WHERE id = $type_id";
					$this->Currency->query($query);
					$response['success'] = true;
				}

			}
			echo json_encode($response);
			exit;
		}
	}

	public function loc_reassign($id = null) {
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			if ($this->request->is('post') || $this->request->is('put')) {
				$response = ['success' => false];
				$post = $this->request->data;
				// pr($post, 1);
				$this->loadModel('Location');
				$this->Location->updateAll(array('type_id' => $post['update_id']), array('type_id' => $post['delete_id']));
				echo json_encode(['success' => true]);
				exit();
			}
			$this->set('id', $id);
			$this->render('/Organisations/partial/loc_reassign');
		}
	}


	public function story_type_list() {
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				// pr($post, 1);
				$params = [];
				$lists = $this->objView->loadHelper('Scratch')->story_type_list(null, false);
				$this->set('story_lists', $lists);
			}
			$this->render('/Organisations/partial/story_type_list');
		}
	}

	public function story_type_save() {
		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$response = [
				'success' => false,
				'msg' => ''
			];
			if ($this->request->is('post') || $this->request->is('put')) {
				$this->loadModel('Currency');
				$post = $this->request->data;
				$id = (isset($post['id']) && !empty($post['id'])) ? $post['id'] : null;
				$title = (isset($post['title']) && !empty($post['title'])) ? Sanitize::escape($post['title']) : null;

				if( isset($post['title']) && !empty($post['title']) ) {
					if(isset($post['id']) && !empty($post['id'])) {
						// update
						$query = "update story_types ot set ot.type = '$title' WHERE ot.id = $id";
						$this->Currency->query($query);
						$response['success'] = true;
					}
					else{
						// insert
						$query = "INSERT INTO story_types set type = '$title'";
						$this->Currency->query($query);
						$response['success'] = true;
					}
				}
				else{
					$response['msg'] = 'Type title is required';
				}

			}
			echo json_encode($response);
			exit;
		}
	}

	public function story_type_delete() {
		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$response = [
				'success' => false
			];
			if ($this->request->is('post') || $this->request->is('put')) {
				$this->loadModel('Currency');
				$post = $this->request->data;
				$type_id = (isset($post['id']) && !empty($post['id'])) ? $post['id'] : null;

				if(isset($post['id']) && !empty($post['id'])) {
					// delete
					$query = "DELETE FROM story_types WHERE id = $type_id";
					$this->Currency->query($query);
					$response['success'] = true;
				}

			}
			echo json_encode($response);
			exit;
		}
	}

	public function story_reassign($id = null) {
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			if ($this->request->is('post') || $this->request->is('put')) {
				$response = ['success' => false];
				$post = $this->request->data;
				// pr($post, 1);
				$this->loadModel('Story');
				$this->Story->updateAll(array('type_id' => $post['update_id']), array('type_id' => $post['delete_id']));
				echo json_encode(['success' => true]);
				exit();
			}
			$this->set('id', $id);
			$this->render('/Organisations/partial/story_reassign');
		}
	}


	public function risk_type_list() {
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				// pr($post, 1);
				$params = [];
				$lists = $this->objView->loadHelper('Scratch')->risk_type_list();
				$this->set('risk_type_list', $lists);
			}
			$this->render('/Organisations/partial/risk_type_list');
		}
	}

	public function risk_type_save() {
		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$response = [
				'success' => false,
				'msg' => ''
			];
			if ($this->request->is('post') || $this->request->is('put')) {
				$this->loadModel('RmRiskType');
				$post = $this->request->data;

				$id = (isset($post['id']) && !empty($post['id'])) ? $post['id'] : null;
				$title = (isset($post['title']) && !empty($post['title'])) ? Sanitize::escape($post['title']) : null;

				if( isset($post['title']) && !empty($post['title']) ) {
					$date = date('Y-m-d');
					if(isset($post['id']) && !empty($post['id'])) {
						// update
						$query = "update rm_risk_types pt set pt.title = '$title', modified = '$date' WHERE pt.id = $id";
						$this->RmRiskType->query($query);
						$response['success'] = true;
					}
					else{
						// insert
						$query = "INSERT INTO rm_risk_types set title = '$title', created = '$date', modified = '$date'";
						$this->RmRiskType->query($query);
						$response['success'] = true;
					}
				}
				else{
					$response['msg'] = 'Title is required';
				}

			}
			echo json_encode($response);
			exit;
		}
	}

	public function risk_type_delete() {
		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$response = [
				'success' => false
			];
			if ($this->request->is('post') || $this->request->is('put')) {
				$this->loadModel('RmRiskType');
				$post = $this->request->data;
				$type_id = (isset($post['id']) && !empty($post['id'])) ? $post['id'] : null;

				if(isset($post['id']) && !empty($post['id'])) {
					// delete
					$query = "DELETE FROM rm_risk_types WHERE id = $type_id";
					$this->RmRiskType->query($query);
					$response['success'] = true;
				}

			}
			echo json_encode($response);
			exit;
		}
	}

	// ADMIN USER SETTINGS
	public function user_settings() {

		$this->layout = 'inner';
		$this->set('title_for_layout', __('Administration: User Settings', true));
		$viewData['page_heading'] = 'User Settings';
		$viewData['page_subheading'] = 'View and update global User administration settings';

		$viewData['crumb'] = [
			'last' => [
				'data' => [
					'title' => "User Settings",
					'data-original-title' => "User Settings",
				],
			],
		];

		if (($this->Session->read('Auth.User.role_id') == 2)  && LOCALIP != $_SERVER['SERVER_ADDR'] ) {

			$this->redirect(array('controller' => 'projects', 'action' => 'lists'));
		}

		$this->loadModel('ProfileSetting');
		$this->loadModel('ProfileField');

		$data = $this->ProfileSetting->find('first');
		$ProfileField = $this->ProfileField->find('all');

		$viewData['ProfileSetting'] = $data;
		$viewData['ProfileField'] = $ProfileField;

		if ($this->request->is('post') || $this->request->is('put')) {
		 // pr($this->request->data); //die;
			if(isset($this->request->data['ProfileSetting']) && !empty($this->request->data['ProfileSetting'])){

			    if (isset($this->request->data['ProfileSetting']['allow_admin_from_org']) && $this->request->data['ProfileSetting']['allow_admin_from_org'] == 'on') {
					$this->request->data['ProfileSetting']['allow_admin_from_org'] = 1;
				}else{
					$this->request->data['ProfileSetting']['allow_admin_from_org'] = 0;
				}

				if(!isset($this->request->data['ProfileSetting']['allow_admin_from_org'])){
					$this->request->data['ProfileSetting']['allow_admin_from_org'] = 0;
				}

				if (isset($this->request->data['ProfileSetting']['own_profile']) && $this->request->data['ProfileSetting']['own_profile'] == 'on') {
					$this->request->data['ProfileSetting']['own_profile'] = 1;
				}else{
					$this->request->data['ProfileSetting']['own_profile'] = 0;
				}

				if(isset($data) && !empty($data)){
					$this->request->data['ProfileSetting']['id'] = $data['ProfileSetting']['id'];
				}

				$this->ProfileSetting->saveAll($this->request->data);
			}else{
				$this->request->data['ProfileSetting']['allow_admin_from_org'] = 0;
				$this->request->data['ProfileSetting']['own_profile'] = 1;
				if(isset($data) && !empty($data)){
					 $this->request->data['ProfileSetting']['id'] = $data['ProfileSetting']['id'];
				}
				$this->ProfileSetting->saveAll($this->request->data);
			}


			if(isset($this->request->data['ProfileField']) && !empty($this->request->data['ProfileField'])){

				/*
				foreach ($this->request->data['ProfileField'] as $key => $listval) {
					if (isset($listval['web']) && $listval['web'] == 'on') {
								$notificationweb = 1;
							} else {
								$notificationweb = 0;
							}
				} */

				//$this->ProfileSetting->saveAll($this->request->data);

			}

		}

		$this->set($viewData);
	}

	public function save_user_settings() {
		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$response = [
				'success' => false
			];

			$this->loadModel('ProfileSetting');
			$this->loadModel('ProfileField');

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				$data = ['id' => 1, 'allow_admin_from_org' => 0, 'own_profile' => 0];
				// if( (isset($post['admin']) && !empty($post['admin'])) || (isset($post['user']) && !empty($post['user'])) ){
					if(isset($post['admin']) && !empty($post['admin'])) {
						$data['allow_admin_from_org'] = 1;
					}
					if(isset($post['user']) && !empty($post['user'])) {
						$data['own_profile'] = 1;
						if(isset($post['fields']) && !empty($post['fields'])) {
							$this->ProfileField->query("UPDATE profile_fields SET status = '0'");
							foreach ($post['fields'] as $key => $value) {
								$query = "UPDATE profile_fields SET status = '$value' WHERE slug = '$key'";
								$this->ProfileField->query($query);
							}
						}
					}
					else{
						if(!isset($post['fields']) || empty($post['fields'])) {
							$this->ProfileField->query("UPDATE profile_fields SET status = '0'");
						}
					}

					$this->ProfileSetting->save($data);
					$response['success'] = true;
				// }

			}
			echo json_encode($response);
			exit;
		}
	}

	public function user_add_new() {

		$this->layout = 'inner';
		$this->set('title_for_layout', __('Add User', true));
		$viewData['page_heading'] = 'Manage Domain Users';
		$viewData['page_subheading'] = 'Domain Users';

		$id = $this->Session->read('Auth.User.id');
		$this->loadModel('Timezone');

		// ORGANIZATIONS LIST
		$organizations = array();
		$check_admin_settings = check_admin_settings();

		if($check_admin_settings == 1 || $check_admin_settings == 2){
			$current_org = $this->objView->loadHelper('Permission')->current_org();
			$current_org = $current_org['organization_id'];
			if(!empty($current_org)){
					$organizationsAll = $this->User->query("SELECT id, name FROM organizations WHERE id = '$current_org' ORDER BY name ASC");
					if (isset($organizationsAll) && !empty($organizationsAll)) {
						foreach ($organizationsAll as $org) {
							$organizations[$org['organizations']['id']] = html_entity_decode( ($org['organizations']['name'] ));
						}
					}
			}
		}
		else{
			$organizationsAll = $this->User->query("SELECT id, name FROM organizations ORDER BY name ASC");
			// pr($organizationsAll,1);
			if (isset($organizationsAll) && !empty($organizationsAll)) {
				foreach ($organizationsAll as $org) {
					$organizations[$org['organizations']['id']] = html_entity_decode( ($org['organizations']['name'] ));
				}
			}
		}
		$viewData['organizations'] = $organizations;


		// REPORTS TO USERS LIST
		$all_report_users = array();
		$ulist = $this->User->query("SELECT ud.user_id, ud.first_name, ud.last_name FROM users u INNER JOIN user_details ud ON ud.user_id = u.id WHERE u.role_id = 2 AND u.status = 1 AND u.is_activated = 1 ORDER BY ud.first_name, ud.last_name ASC");
		// pr($ulist,1);
		if (isset($ulist) && !empty($ulist)) {
			foreach ($ulist as $u) {
				$all_report_users[$u['ud']['user_id']] = $u['ud']['first_name'] . ' ' . $u['ud']['last_name'];
			}
		}
		$viewData['all_report_users'] = $all_report_users;

		// DOTTED LINE USERS LIST
		$all_users = array();
		$ulist = $this->User->query("SELECT ud.user_id, ud.first_name, ud.last_name FROM users u INNER JOIN user_details ud ON ud.user_id = u.id WHERE u.role_id = 2 AND u.status = 1 AND u.is_activated = 1 ORDER BY ud.first_name, ud.last_name ASC");
		// pr($ulist,1);
		if (isset($ulist) && !empty($ulist)) {
			foreach ($ulist as $u) {
				$all_users[$u['ud']['user_id']] = $u['ud']['first_name'] . ' ' . $u['ud']['last_name'];
			}
		}
		$viewData['all_users'] = $all_users;

		if ($this->request->is('post') || $this->request->is('put')) {

			$_SESSION['data'] = $this->request->data;
			// pr($this->request->data, 1);

			// CONNECT WITH MONGO DB
			if ($this->live_setting == true) {

				if (PHP_VERSIONS == 5) {
					$mongo = new MongoClient(MONGO_CONNECT);
					$this->mongoDB = $mongo->selectDB(MONGO_DATABASE);
					$mongo_collection = new MongoCollection($this->mongoDB, 'users');
				} else {

					$mongo = new MongoDB\Driver\Manager(MONGO_CONNECT);
					$mongo_collection = new MongoDB\Driver\BulkWrite;
				}

			}

			if (check_license()) {

				if (isset($this->request->data['User']['email']) && !empty($this->request->data['User']['email'])) {
					$post = $this->request->data;
					// pr($this->validationErrors );
					$userEmail = $this->request->data['User']['email'];
					$getEmail = explode("@", $userEmail);
					if (isset($getEmail[1]) && !empty($getEmail[1])) {
						//$getDomain = explode(".",$getEmail[1]);
						$getDomain = strtolower($getEmail[1]);
					}

					$pmin_length = 5;
					$policy_checks = [];
					$policy_checks[] = 'lower_case';
					//echo $getDomain; die;
					//$checkorgDomain = $this->checkOrgdomainforUser($getDomain[0], $this->Session->read('Auth.User.id'));

					$organisations_id = $this->Session->read('Auth.User.id');
					$as_og = $this->Session->read('Auth.User.UserDetail.org_id');

					if (isset($as_og) && $as_og > 0) {
						$organisations_id = $this->Session->read('Auth.User.UserDetail.org_id');
					}

					$this->request->data['UserDetail']['org_id'] = $organisations_id;

					if (isset($getDomain) && !empty($getDomain)) {
						$posted_org = (isset($post['UserDetail']['organization_id']) && !empty($post['UserDetail']['organization_id'])) ? $post['UserDetail']['organization_id'] : null;
						$checkorgDomain = $this->checkEmaildomainforUser($getDomain, $organisations_id, $posted_org);

						//================= Pawan Check password policy =================
						//if( $_SERVER['SERVER_NAME'] == 'dotsquares.ideascast.com' ) {

						$this->request->data['UserDetail']['org_id'] = $organisations_id;
						$this->request->data['User']['managedomain_id'] = $checkorgDomain;
						$orgPasswordPolicy = $this->OrgPassPolicy->find('first', array('conditions' => array('OrgPassPolicy.org_id' => $this->request->data['UserDetail']['org_id'])));

						if (isset($orgPasswordPolicy['OrgPassPolicy']) && !empty($orgPasswordPolicy['OrgPassPolicy'])) {

							if (!empty($orgPasswordPolicy['OrgPassPolicy']['min_lenght'])) {

								$pmin_length = $orgPasswordPolicy['OrgPassPolicy']['min_lenght'] + 1;

							}
							if ($orgPasswordPolicy['OrgPassPolicy']['numeric_char'] == 1) {

								$policy_checks[] = 'numbers';

							}if (isset($orgPasswordPolicy['OrgPassPolicy']['alph_char']) && !empty($orgPasswordPolicy['OrgPassPolicy']['alph_char'])) {

								$policy_checks[] = 'alphbate';
							}
							if (isset($orgPasswordPolicy['OrgPassPolicy']['special_char']) && !empty($orgPasswordPolicy['OrgPassPolicy']['special_char'])) {

								$policy_checks[] = 'special_symbols';
							}

							if (isset($orgPasswordPolicy['OrgPassPolicy']['caps_char']) && !empty($orgPasswordPolicy['OrgPassPolicy']['caps_char'])) {

								$policy_checks[] = 'upper_case';
							}

						}

					}

					$policy_checks = implode(',', $policy_checks);

					$policy_password = policy_password($pmin_length, $policy_checks);

					if($_SERVER["SERVER_NAME"] == "192.168.7.20"){

						$checkorgDomain = true;
					}

					if (isset($checkorgDomain) && $checkorgDomain == false) {
						$this->Session->setFlash(__('Supplied email domain does not allowed, please try again with valid email domain.'), 'error');
					} else {

						$userPassword = '';
						$this->UserDetail->validator()->remove('org_name');
						$this->UserDetail->validator()->remove('department_id');

						$this->request->data['UserDetail']['added_by'] = $this->Session->read('Auth.User.id');
						$this->request->data['UserDetail']['updated_by'] = $this->Session->read('Auth.User.id');

						//add department id when user adding
						// $departmentData = $this->getDepartment();
						//$this->request->data['UserDetail']['department'] = $departmentData['Department']['name'];
						// $this->request->data['UserDetail']['department_id'] = $departmentData['Department']['id'];

						$this->request->data['User']['managedomain_id'] = $checkorgDomain;

						$userPassword = $policy_password;

						$this->request->data['User']['parent_id'] = $this->Session->read('Auth.User.id');
						$this->request->data['User']['email'] = strtolower($this->request->data['User']['email']);

						/* activation */
						if(UA) {
							// $this->request->data['User']['status'] = 0;
							$this->request->data['User']['activation_time'] = date('Y-m-d h:i:s');
						}
						else{
							$this->request->data['User']['status'] = 1;
						}
						/* activation */

						$pans = AuthComponent::password($userPassword);
						$this->request->data['User']['password'] = $userPassword;
						$this->request->data['UserPassword']['password'] = $pans;
						$this->request->data['UserDetail']['org_password'] = $userPassword;

						/* $UserDottedLine = $this->request->data['UserDottedLine'];
						unset($this->request->data['UserDottedLine']);
						unset($this->request->data['dotted_user_id']); */

						// pr($this->request->data, 1);
						if ($this->User->saveAssociated($this->request->data)) {
							$userId = $this->User->getLastInsertID();

							if (isset($UserDottedLine['dotted_user_id']) && !empty($UserDottedLine['dotted_user_id'])) {
								// Delete previous entries
								$this->User->query("DELETE FROM user_dotted_lines WHERE user_id = $userId");
								$dotted_user_id = $UserDottedLine['dotted_user_id'];
								$qry = "INSERT INTO `user_dotted_lines` (`user_id`, `dotted_user_id`) VALUES ";
								$qry_arr = [];
			    				foreach ($dotted_user_id as $key => $value) {
			    					$qry_arr[] = "($userId, $value)";
			    				}
			    				$qry .= implode(' ,', $qry_arr);
			    				$this->User->query($qry);
							}

							$this->request->data['UserPassword']['user_id'] = $userId;
							$this->UserPassword->save($this->request->data);

							$sqlN = "update user_details set user_details.org_password = AES_ENCRYPT(org_password, 'secret')  WHERE user_details.user_id =" . $userId;

							$this->UserDetail->query($sqlN);

							$loggedInTimzone = $this->Timezone->find('first', array('conditions' => array('Timezone.user_id' => $this->Session->read('Auth.User.id'))));

							if ($this->live_setting == true) {
								$this->Users->addUser($userId);
							}

							unset($_SESSION['data']);

							$parentid = $this->User->find('first', array('conditions' => array('User.id' => $userId)));

							$getorgname = $this->User->find('first', array('conditions' => array('User.id' => $parentid['User']['parent_id'])));

							$clientAdminEmail = $getorgname['User']['email'];

							// email to user
							if(UA){
								$this->CommonEmail->user_activation(['user_id' => $userId]);
							}
							else{
								$this->__sendEmailConfirm($this->request->data, $userPassword, $getorgname['UserDetail']['org_name'],'',$clientAdminEmail);
							}

							$this->Session->setFlash(__('User has been saved successfully.'), 'success');
							$this->redirect(array('action' => 'manage_users'));

						}
					}
				}
			} else {

				$this->Session->setFlash(__('You are not allowed to add user under the organization because your licencing limit has been exceeded.'), 'error');
				//$this->redirect(array('action' =>'user_add'));

			}

		}

		$this->set($viewData);

	}
	
	public function watchScheduleCron() {

		$link = mysqli_connect(root_host, cron_dbuser, cron_dbpass,root_dbname);
		if (mysqli_connect_errno()) {
			echo "Failed to connect to MySQL: " . mysqli_connect_error();
		}
		$sql = mysqli_query($link,"select subdomain from org_settings");
		while ($result = mysqli_fetch_array($sql)) {

			//exec("wget -O /dev/null https://".$result['subdomain'].".ideascast.com/projects/projectScheduleOverdueEmailCron");

			$ch = curl_init();

			// set URL and other appropriate options
			curl_setopt($ch, CURLOPT_URL, "https://" . $result['subdomain'] . WEBDOMAIN."/competencies/watch_diff_cron");
			curl_setopt($ch, CURLOPT_HEADER, 0);

			// grab URL and pass it to the browser
			curl_exec($ch);

			// close cURL resource, and free up system resources
			curl_close($ch);
		}

		mysqli_close($link);

		//5	0	*	*	*	wget -O /dev/null https://jeera.ideascast.com/projects/projectScheduleOverdueEmailCron

	}
	
}