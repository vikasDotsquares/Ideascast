<?php
App::import('Lib', 'XmlApi');
/**
 * Component for working common.
 */
class CommonComponent extends Component {

	public $components = array('Session', 'Email', 'Paginator', 'Auth');

	/* function of workspace status us in api */
	public function workspace_status($start_date, $end_date, $sign_off) {

		$status = STATUS_NOT_SPACIFIED;

		if ((isset($start_date) && !empty($start_date)) && (isset($end_date) && !empty($end_date))) {

			if (((isset($start_date) && !empty($start_date)) && date('Y-m-d', strtotime($start_date)) > date('Y-m-d')) && $sign_off != 1) {
				$status = STATUS_NOT_STARTED;
			} else if (((isset($end_date) && !empty($end_date)) && date('Y-m-d', strtotime($end_date)) < date('Y-m-d')) && $sign_off != 1) {
				$status = STATUS_OVERDUE;
			} else if (isset($sign_off) && !empty($sign_off) && $sign_off > 0) {
				$status = STATUS_COMPLETED;
			} else if ((((isset($end_date) && !empty($end_date)) && (isset($start_date) && !empty($start_date))) && (date('Y-m-d', strtotime($start_date)) <= date('Y-m-d')) && date('Y-m-d', strtotime($end_date)) >= date('Y-m-d')) && $sign_off != 1) {
				$status = STATUS_PROGRESS;
			}
		}
		return $status;
	}

	public function countriesMenu() {

		$this->Country = ClassRegistry::init('Country');

		$datas = $this->Country->find('list', array(
			'fields' => array('Country.iso_code', 'Country.name'),
			'limit' => 35,
		));

		return $datas;
	}
	public function create_workspace_activity($project_id = null, $workspace_id = null) {
		$user_id = $this->Session->read("Auth.User.id");
		$date = date("Y-m-d H:i:s");
		$message = 'Workspace created';
		$work_data = [
			'project_id' => $project_id,
			'workspace_id' => $workspace_id,
			'updated_user_id' => $user_id,
			'message' => $message,
			'updated' => $date,
		];
		ClassRegistry::init('WorkspaceActivity')->save($work_data);

		$message = 'Project updated';
		$project_data = [
			'project_id' => $project_id,
			'updated_user_id' => $user_id,
			'message' => $message,
			'updated' => $date,
		];
		// ClassRegistry::init('ProjectActivity')->save($project_data);
	}

	public function update_project_activity($project_id = null, $type = false) {
		$user_id = $this->Session->read("Auth.User.id");
		$date = date("Y-m-d H:i:s");

		if (isset($type) && $type == true) {
			$message = 'Project created';
		} else {
			$message = 'Project updated';
		}

		$project_data = [
			'project_id' => $project_id,
			'updated_user_id' => $user_id,
			'message' => $message,
			'updated' => $date,
		];
		ClassRegistry::init('ProjectActivity')->save($project_data);

	}

	public function countriesList() {

		$this->Country = ClassRegistry::init('Country');

		$datas = $this->Country->find('list', array(
			'fields' => array('Country.iso_code', 'Country.name'),
			'order' => array('Country.name ASC'),
		));

		return $datas;
	}

	public function check_email_permission($user_id = null, $personlization = null, $notification_type = 'sketches' ) {

		$notifiragstatus = ClassRegistry::init('EmailNotification')->find('first', ['conditions' => ['notification_type' => $notification_type, 'personlization' => $personlization, 'user_id' => $user_id]]);

		$usersDetails = ClassRegistry::init('User')->find('first', array('conditions' => array('User.id' => $user_id)));

		if ((!isset($notifiragstatus['EmailNotification']['email']) || $notifiragstatus['EmailNotification']['email'] == 1) && (!isset($usersDetails['User']['email_notification']) || $usersDetails['User']['email_notification'] == 1)) {
			return true;
		} else {
			return false;
		}

	}

	public function citiesListIsoCodeBased($isoCode = null) {

		$this->City = ClassRegistry::init('City');
		ini_set('max_execution_time', 360000);

		$datas = $this->City->find('list', array(
			'fields' => array('City.id', 'City.name'),
			'conditions' => array('City.country_iso_code' => $isoCode),
			'order' => array('City.name ASC'),
		));

		return $datas;
	}

	public function getRoles($id = null) {
		$roles = ClassRegistry::init('User')->find('first', array('conditions' => array('User.id' => $id)));
		return isset($roles['User']['role_id']) ? $roles['User']['role_id'] : '-';
	}

	public function settings($key = null) {

		$this->Settings = ClassRegistry::init('Settings');

		$datas = $this->Settings->findByKey($key);

		return $datas;
	}
	public function get_user_data($user_id = null, $recursive = -1) {

		App::import("Model", "UserDetail");

		$user = new UserDetail();

		if (!$user_id) {
			return;
		}

		$user_detail = null;

		if (isset($user_id) && !empty($user_id)) {
			$user_detail = $user->find('first', ['conditions' => ['UserDetail.user_id' => $user_id], 'recursive' => $recursive]);
		}

		return $user_detail;

	}
	public function getCityByStateCountryCode($StateCode = null, $isoCode = null) {

		$this->City = ClassRegistry::init('City');
		$datas = $this->City->find('list', array(
			'fields' => array('id', 'city_name_ascii'),
			'conditions' => array('City.country_iso_code' => $isoCode, 'City.state_code' => $StateCode),
			'order' => array('City.name ASC'),
		));

		return $datas;
	}

	public function getStatesByIsoCode($isoCode = null) {

		$this->State = ClassRegistry::init('State');
		$datas = $this->State->find('list', array(
			'fields' => array('code', 'name'),
			'conditions' => array('State.country_iso_code' => $isoCode),
			'order' => array('State.name ASC'),
		));

		return $datas;
	}

	public function getState($id = null) {
		$state = ClassRegistry::init('State')->find('first', array('conditions' => array('id' => $id)));
		return isset($state['State']['name']) ? $state['State']['name'] : '-';
	}

	public function hasChild($modelName = '', $id = null) {

		switch ($modelName) {
		case 'Project':
			$numOfTasks = ClassRegistry::init('Task')->find('count', array('conditions' => array('Task.project_id' => $id)));

			//$numOfMilestone = ClassRegistry::init('MileStone')->find('count',array('conditions'=>array('MileStone.project_id'=>$id)) );
			$count = ($numOfTasks);
			break;
		case 'Milestone':
			$count = ClassRegistry::init('Task')->find('count', array('conditions' => array('Task.milestone_id' => $id)));
			break;
		}

		return $count;
	}

	public function currencyConvertor($val) {
		$data = ClassRegistry::init('Currency')->find('first', array('conditions' => array('Currency.status' => 1), 'fields' => array('Currency.value')));

		//pr($data['Currency']['value']); die;
		return $data['Currency']['value'] * $val;
	}

	public function getVat($val) {
		$data = ClassRegistry::init('Country')->find('first', array('conditions' => array('Country.countryCode' => $val), 'fields' => array('Country.vat')));

		return $data['Country']['vat'];
	}

	public function projectModified($project_id, $user_id) {

		$data = array('UserProject.project_id' => $project_id, 'UserProject.owner_user' => 1, 'UserProject.user_id' => $user_id);
		// This will update UserProject with id

		ClassRegistry::init('UserProject')->updateAll(array('UserProject.modified' => "'" . date('Y-m-d H:i:s') . "'"), $data);
	}

	public function sessionData($id) {

		$ams = ClassRegistry::init('CakeSessions');

		$data = $ams->find('first', array('conditions' => array('CakeSessions.id' => $id)));

		return isset($data) ? $data : array();
	}

	// update image updated user
	public function projectImageModified($project_id, $user_id) {

		$data = array('Project.id' => $project_id);

		ClassRegistry::init('Project')->updateAll(['Project.image_updated_on' => "'" . date('Y-m-d H:i:s') . "'", 'Project.image_updated_by' => $user_id], $data);
	}

	public function area_elements_permissions($area_id = null, $keys = false, $arr) {

		if (empty($area_id)) {
			return null;
		}

		$data = null;

		if (!empty($area_id)) {

			$data = ClassRegistry::init('Element')->find('all', [
				'recursive' => -1,
				'conditions' => ['Element.area_id' => $area_id, 'Element.id' => $arr],
				'order' => ['Element.sort_order ASC'],
			]);
			//
		}

		if ($keys && !empty($data)) {
			$elementIds = Set::extract($data, '/Element/id');
			return $elementIds;
		}

		return $data;
	}

	public function get_up_id($pid, $uid) {

		$up_data = ClassRegistry::init('UserProject')->find('first', array('conditions' => array('UserProject.project_id' => $pid)));

		return (isset($up_data['UserProject']['id'])) ? $up_data['UserProject']['id'] : null;
	}

	public function wsp_permission_edit($id, $pid, $uid) {

		$view = new View();
		$common = $view->loadHelper('Common');

		$data = ClassRegistry::init('WorkspacePermission')->find('first', array('conditions' => array('WorkspacePermission.user_id' => $uid, 'WorkspacePermission.project_workspace_id' => $id, 'WorkspacePermission.user_project_id' => $common->get_up_id($pid, $uid))));
		return isset($data['WorkspacePermission']['permit_edit']) ? $data['WorkspacePermission']['permit_edit'] : 0;
	}

	public function wsp_permission_delete($id, $pid, $uid) {

		$view = new View();
		$common = $view->loadHelper('Common');

		$data = ClassRegistry::init('WorkspacePermission')->find('first', array('conditions' => array('WorkspacePermission.project_workspace_id' => $id, 'WorkspacePermission.user_id' => $uid, 'WorkspacePermission.user_project_id' => $common->get_up_id($pid, $uid))));
		return isset($data['WorkspacePermission']['permit_delete']) ? $data['WorkspacePermission']['permit_delete'] : 0;
	}

	public function wsp_permission_details($id, $pid, $uid) {

		$view = new View();
		$common = $view->loadHelper('Common');

		$data = ClassRegistry::init('WorkspacePermission')->find('all', array('conditions' => array('WorkspacePermission.project_workspace_id' => $id, 'WorkspacePermission.user_id' => $uid, 'WorkspacePermission.user_project_id' => $common->get_up_id($pid, $uid))));
		return isset($data) ? $data : array();
	}

	public function project_permission_details($pid, $uid) {

		$view = new View();
		$common = $view->loadHelper('Common');

		$data = ClassRegistry::init('ProjectPermission')->find('first', array('conditions' => array('ProjectPermission.user_id' => $uid, 'ProjectPermission.user_project_id' => $common->get_up_id($pid, $uid))));

		return isset($data) ? $data : array();
	}

	public function userproject($pid, $uid) {
		$data = ClassRegistry::init('UserProject')->find('first', array('conditions' => array('UserProject.user_id' => $uid, 'UserProject.project_id' => $pid, 'UserProject.owner_user' => 1)));
		// echo ClassRegistry::init('UserProject')->_query();
		// pr($data);die;
		return isset($data) ? $data : array();
	}

	public function element_permission_details($wid, $pid, $uid) {

		$datas = ClassRegistry::init('ElementPermission')->find('all', array('conditions' => array('ElementPermission.user_id' => $uid, 'ElementPermission.project_id' => $pid, 'ElementPermission.workspace_id' => $wid)));

		if (isset($datas) && !empty($datas)) {

			foreach ($datas as $dat) {
				$data[] = $dat['ElementPermission']['element_id'];
			}
		}

		return isset($data) ? $data : array();
	}

	public function work_permission_details($pid, $uid) {

		$view = new View();
		$common = $view->loadHelper('Common');

		$datas = ClassRegistry::init('WorkspacePermission')->find('all', array('conditions' => array('WorkspacePermission.user_id' => $uid, 'WorkspacePermission.user_project_id' => $common->get_up_id($pid, $uid))));
		//pr($datas);
		if (isset($datas) && !empty($datas)) {

			foreach ($datas as $dat) {
				$data[] = $dat['WorkspacePermission']['project_workspace_id'];
			}
		}

		return isset($data) ? $data : array();
	}

	public function workspace_pwid($workspace_id = null) {
		$wpwid = null;
		App::import("Model", "ProjectWorkspace");
		$model = new ProjectWorkspace();
		if (!empty($workspace_id) && $model->hasAny(['ProjectWorkspace.workspace_id' => $workspace_id])) {
			$ws = $model->find('first', ['conditions' => ['ProjectWorkspace.workspace_id' => $workspace_id], 'recursive' => -1, 'fields' => ['ProjectWorkspace.id']]);
			$wpwid = (!empty($ws)) ? $ws['ProjectWorkspace']['id'] : null;
		}
		return $wpwid;
	}

	public function share_project_email($user_id, $project_id) {

		$projectDetails = ClassRegistry::init('Project')->find('first', array('conditions' => array('Project.id' => $project_id)));
		//Send Email and redirect to list page
		$loggedInemail = $this->Session->read('Auth.User.email');
		$name = $this->Session->read('Auth.User.UserDetail.full_name');
		$usersDetails = ClassRegistry::init('User')->find('first', array('conditions' => array('User.id' => $user_id)));

		$projectAction = SITEURL . 'projects/index/' . $project_id;

		$notifiragstatus = ClassRegistry::init('EmailNotification')->find('first', ['conditions' => ['notification_type' => 'project', 'personlization' => 'project_sharing', 'user_id' => $user_id]]);

		if ((!isset($notifiragstatus['EmailNotification']['email']) || $notifiragstatus['EmailNotification']['email'] == 1) && (!isset($usersDetails['User']['email_notification']) || $usersDetails['User']['email_notification'] == 1)) {

			$emailAddress = $usersDetails['User']['email'];
			$email = new CakeEmail();
			$email->config('Smtp');
			$email->from(array(ADMIN_FROM_EMAIL => MAIL_SITENAME));
			$email->to($emailAddress);
			// $email->subject( "$name Shared a Project" );
			$email->subject(SITENAME . ": Project sharing");
			$email->template('project_sharing_email');
			$email->emailFormat('html');
			$email->viewVars(array('Custname' => $usersDetails['UserDetail']['first_name'] . ' ' . $usersDetails['UserDetail']['last_name'], 'name' => $name, 'projectDetails' => $projectDetails,'open_page'=>$projectAction));
			$email->send();
			return true;
		}
		return true;

	}


	public function share_update_email($user_id, $project_id) {

		$projectDetails = ClassRegistry::init('Project')->find('first', array('conditions' => array('Project.id' => $project_id)));
		//Send Email and redirect to list page
		$loggedInemail = $this->Session->read('Auth.User.email');
		$name = $this->Session->read('Auth.User.UserDetail.full_name');
		$usersDetails = ClassRegistry::init('User')->find('first', array('conditions' => array('User.id' => $user_id)));

		$pageAction = SITEURL . 'projects/index/' . $project_id;

		$emailAddress = $usersDetails['User']['email'];
		$email = new CakeEmail();
		$email->config('Smtp');
		// $email->from(array($loggedInemail => $name));
		$email->from(array(ADMIN_FROM_EMAIL => MAIL_SITENAME));
		$email->to($emailAddress);
		// $email->subject( "$name Shared a Project" );
		$email->subject(SITENAME . ": Project Sharing New Additions");
		$email->template('share_update_email');
		$email->emailFormat('html');
		$email->viewVars(array('Custname' => $usersDetails['UserDetail']['first_name'] . ' ' . $usersDetails['UserDetail']['last_name'], 'name' => $name, 'projectDetails' => $projectDetails,'open_page'=>$pageAction));
		$email->send();
		return true;
	}

	public function task_share_email($user_id, $project_id, $wsp_id, $task_id, $update = false) {

		$projectDetails = ClassRegistry::init('Project')->find('first', array('conditions' => array('Project.id' => $project_id), 'fields' => ['title']));
		$wspDetails = ClassRegistry::init('Workspace')->find('first', array('conditions' => array('Workspace.id' => $wsp_id), 'fields' => ['title']));
		$taskDetails = ClassRegistry::init('Element')->find('first', array('conditions' => array('Element.id' => $task_id), 'fields' => ['title']));
		//Send Email and redirect to list page
		$loggedInemail = $this->Session->read('Auth.User.email');
		$name = $this->Session->read('Auth.User.UserDetail.full_name');
		$usersDetails = ClassRegistry::init('User')->find('first', array('conditions' => array('User.id' => $user_id)));

		$pageAction = SITEURL . 'entities/update_element/'.$task_id.'#tasks';

		$emailAddress = $usersDetails['User']['email'];
		$email = new CakeEmail();
		$email->config('Smtp');
		$email->from(array(ADMIN_FROM_EMAIL => MAIL_SITENAME));
		$email->to($emailAddress);
		$email->subject(SITENAME . ": Task sharing");
		$email->template('task_share_email');
		$email->emailFormat('html');
		$email->viewVars(array('receiver' => $usersDetails['UserDetail']['first_name'] . ' ' . $usersDetails['UserDetail']['last_name'], 'name' => $name, 'projectDetails' => $projectDetails, 'wspDetails' => $wspDetails, 'taskDetails' => $taskDetails, 'update' => $update,'open_page'=>$pageAction));
		$email->send();
		return true;
	}

	public function workspace_share_email($user_id, $project_id, $wsp_id, $update = false) {

		$projectDetails = ClassRegistry::init('Project')->find('first', array('conditions' => array('Project.id' => $project_id), 'fields' => ['title']));
		$wspDetails = ClassRegistry::init('Workspace')->find('first', array('conditions' => array('Workspace.id' => $wsp_id), 'fields' => ['title']));
		//Send Email and redirect to list page
		$loggedInemail = $this->Session->read('Auth.User.email');
		$name = $this->Session->read('Auth.User.UserDetail.full_name');
		$usersDetails = ClassRegistry::init('User')->find('first', array('conditions' => array('User.id' => $user_id)));

		$pageAction = SITEURL . 'projects/manage_elements/' . $project_id.'/'.$wsp_id;

		$emailAddress = $usersDetails['User']['email'];
		$email = new CakeEmail();
		$email->config('Smtp');
		$email->from(array(ADMIN_FROM_EMAIL => MAIL_SITENAME));
		$email->to($emailAddress);
		$email->subject(SITENAME . ": Workspace sharing");
		$email->template('workspace_share_email');
		$email->emailFormat('html');
		$email->viewVars(array('receiver' => $usersDetails['UserDetail']['first_name'] . ' ' . $usersDetails['UserDetail']['last_name'], 'name' => $name, 'projectDetails' => $projectDetails, 'wspDetails' => $wspDetails, 'update' => $update,'open_page'=>$pageAction));
		$email->send();
		return true;
	}

	public function nudge_email($data = null) {

		//Send Email and redirect to list page
		$loggedInemail = $this->Session->read('Auth.User.email');
		$name = $this->Session->read('Auth.User.UserDetail.full_name');
		$usersDetails = ClassRegistry::init('User')->find('first', array('conditions' => array('User.id' => $data['user_id'])));

		$pageAction = $data['url'];

		$emailAddress = $usersDetails['User']['email'];
		$email = new CakeEmail();
		$email->config('Smtp');
		$email->from(array(ADMIN_FROM_EMAIL => MAIL_SITENAME));
		$email->to($emailAddress);
		$email->subject(SITENAME . ": ".$data['subject']);
		$email->template('nudge_email');
		$email->emailFormat('html');
		$email->viewVars(array('receiver' => $usersDetails['UserDetail']['first_name'] . ' ' . $usersDetails['UserDetail']['last_name'], 'name' => $name, 'message' => $data['message'],'open_page'=>$pageAction, 'hide_button' => $data['hide_button']));
		$email->send();
		return true;
	}


	public function getUsersListToSendReminder($vote_id) {
		$reminder_emailUsers = array();
		if (isset($vote_id) && !empty($vote_id)) {
			// Get the list of invite users of vote
			$inviteUsers = ClassRegistry::init('VoteUser')->find('list', array('conditions' => array('VoteUser.vote_id' => $vote_id), 'fields' => array('VoteUser.user_id', 'VoteUser.user_id'), 'group' => array('VoteUser.user_id')));

			// Get the list of participants users of vote
			$participantsUsers = ClassRegistry::init('VoteResult')->find('list', array('conditions' => array('VoteResult.vote_id' => $vote_id), 'fields' => array('VoteResult.user_id', 'VoteResult.user_id'), 'group' => array('VoteResult.user_id')));

			$reminder_emailUsers = @array_diff($inviteUsers, $participantsUsers);
			$reminder_emailUsers = ClassRegistry::init('User')->find('all', array('conditions' => array('User.id' => $reminder_emailUsers)));
		}
		return $reminder_emailUsers;
	}

	public function getFeedbacksUsersListToSendReminder($feedback_id) {
		$reminder_emailUsers = array();
		if (isset($feedback_id) && !empty($feedback_id)) {
			// Get the list of invite users of vote
			$inviteUsers = ClassRegistry::init('FeedbackUser')->find('list', array('conditions' => array('FeedbackUser.feedback_id' => $feedback_id), 'fields' => array('FeedbackUser.user_id', 'FeedbackUser.user_id'), 'group' => array('FeedbackUser.user_id')));

			// Get the list of participants users of vote
			$participantsUsers = ClassRegistry::init('FeedbackResult')->find('list', array('conditions' => array('FeedbackResult.feedback_id' => $feedback_id), 'fields' => array('FeedbackResult.user_id', 'FeedbackResult.user_id'), 'group' => array('FeedbackResult.user_id')));

			$reminder_emailUsers = @array_diff($inviteUsers, $participantsUsers);
			$reminder_emailUsers = ClassRegistry::init('User')->find('all', array('conditions' => array('User.id' => $reminder_emailUsers)));
		}
		return $reminder_emailUsers;
	}

	public function elementLink_creator($linkid, $pid, $uid) {

		$dataUO = ClassRegistry::init('ElementLink')->find('first', array('conditions' => array('ElementLink.id' => $linkid, 'ElementLink.creater_id >' => 0)));

		//$dataUO['UserProject']['user_id']

		$dataEC = ClassRegistry::init('UserProject')->find('first', array('conditions' => array('UserProject.project_id' => $pid, 'UserProject.owner_user' => 1)));

		// pr($uid); die;

		if (isset($dataUO) && !empty($dataUO)) {
			$id = $dataUO['ElementLink']['creater_id'];
		} else {
			$id = $dataEC['UserProject']['user_id'];
		}

		$dataUD = ClassRegistry::init('UserDetail')->find('first', array('conditions' => array('UserDetail.user_id' => $id), 'fields' => array('first_name', 'last_name')));

		return isset($dataUD) ? $dataUD['UserDetail']['first_name'] . " " . $dataUD['UserDetail']['last_name'] : 'N/A';
	}

	public function elementNote_creator($linkid, $pid, $uid) {

		$dataUO = ClassRegistry::init('ElementNote')->find('first', array('conditions' => array('ElementNote.id' => $linkid, 'ElementNote.creater_id >' => 0)));

		//$dataUO['UserProject']['user_id']

		$dataEC = ClassRegistry::init('UserProject')->find('first', array('conditions' => array('UserProject.project_id' => $pid, 'UserProject.owner_user' => 1)));

		//pr($linkid);

		if (isset($dataUO) && !empty($dataUO)) {
			$id = $dataUO['ElementNote']['creater_id'];
		} else {
			$id = $dataEC['UserProject']['user_id'];
		}

		$dataUD = ClassRegistry::init('UserDetail')->find('first', array('conditions' => array('UserDetail.user_id' => $id), 'fields' => array('first_name', 'last_name')));

		return isset($dataUD) ? $dataUD['UserDetail']['first_name'] . " " . $dataUD['UserDetail']['last_name'] : 'N/A';
	}

	public function elementDoc_creator($linkid, $pid, $uid) {

		$dataUO = ClassRegistry::init('ElementDocument')->find('first', array('conditions' => array('ElementDocument.id' => $linkid, 'ElementDocument.creater_id >' => 0)));

		//$dataUO['UserProject']['user_id']

		$dataEC = ClassRegistry::init('UserProject')->find('first', array('conditions' => array('UserProject.project_id' => $pid, 'UserProject.owner_user' => 1)));

		//pr($linkid);

		if (isset($dataUO) && !empty($dataUO)) {
			$id = $dataUO['ElementDocument']['creater_id'];
		} else {
			$id = $dataEC['UserProject']['user_id'];
		}

		$dataUD = ClassRegistry::init('UserDetail')->find('first', array('conditions' => array('UserDetail.user_id' => $id), 'fields' => array('first_name', 'last_name')));

		return isset($dataUD) ? $dataUD['UserDetail']['first_name'] . " " . $dataUD['UserDetail']['last_name'] : 'N/A';
	}

	public function elementMM_creator($linkid, $pid, $uid) {

		$dataUO = ClassRegistry::init('ElementMindmap')->find('first', array('conditions' => array('ElementMindmap.id' => $linkid, 'ElementMindmap.creater_id >' => 0)));

		//$dataUO['UserProject']['user_id']

		$dataEC = ClassRegistry::init('UserProject')->find('first', array('conditions' => array('UserProject.project_id' => $pid, 'UserProject.owner_user' => 1)));

		//pr($linkid);

		if (isset($dataUO) && !empty($dataUO)) {
			$id = $dataUO['ElementMindmap']['creater_id'];
		} else {
			$id = $dataEC['UserProject']['user_id'];
		}

		$dataUD = ClassRegistry::init('UserDetail')->find('first', array('conditions' => array('UserDetail.user_id' => $id), 'fields' => array('first_name', 'last_name')));

		return isset($dataUD) ? $dataUD['UserDetail']['first_name'] . " " . $dataUD['UserDetail']['last_name'] : 'N/A';
	}

	public function feedbackRateAverage($give_to_id) {
		//echo $fid."<br>".$fr_id."<br>".$give_by_id."<br>".$give_to_id;
		$user_id = $this->Session->read('Auth.User.id');
		$datas = ClassRegistry::init('FeedbackRating')->find('all', array('conditions' => array('FeedbackRating.given_to_id' => $give_to_id)));

		if (isset($datas) && !empty($datas)) {
			$i = 1;
			$rateer = 0;
			$rateTotal = 0;
			foreach ($datas as $dat) {
				$rateer += $dat['FeedbackRating']['rate'];
				$i++;
			}

			$size = sizeof($datas);
			return $rateer / $size;
		}
	}

	public function check_date_validation_ws($start = null, $end = null, $project_id = null, $id = null) {
		$this->Project = ClassRegistry::init('Project');
		$this->Workspace = ClassRegistry::init('Workspace');
		$this->Area = ClassRegistry::init('Area');
		$this->Element = ClassRegistry::init('Element');
		$date_array = null;
		$this->Project->recursive = $this->Workspace->recursive = $this->Area->recursive = $this->Element->recursive = -1;

		if (isset($id) && !empty($id)) {
			$project = $this->Project->find("first", array("fields" => array("Project.start_date", "Project.end_date"), "conditions" => array("Project.id" => $project_id)));
			$workspace = $this->Workspace->find("first", array("fields" => array("Workspace.start_date", "Workspace.end_date"), "conditions" => array("Workspace.id" => $id)));

			$area_ids = $this->Area->find("all", array("fields" => array(), "conditions" => array("Area.workspace_id" => $id)));

			$area_ids = Set::classicExtract($area_ids, '{n}.Area.id');
			$element_array = $this->Element->find("all", array("fields" => array("Element.start_date", "Element.end_date", "Element.area_id"), "conditions" => array("Element.area_id" => $area_ids)));

			$element_date_array = $this->Element->find("all", array("fields" => array("MIN(Element.start_date) AS start_date", "MAX(Element.end_date) AS end_date", "Element.area_id"), "conditions" => array("Element.area_id" => $area_ids)));

			$date_array['Project']['start_date'] = isset($project['Project']['start_date']) && !empty($project['Project']['start_date']) ? $project['Project']['start_date'] : null;
			$date_array['Project']['end_date'] = isset($project['Project']['end_date']) && !empty($project['Project']['end_date']) ? $project['Project']['end_date'] : null;

			$date_array['Workspace']['start_date'] = isset($start) && !empty($start) ? $start : $workspace['Workspace']['end_date'];
			$date_array['Workspace']['end_date'] = isset($end) && !empty($end) ? $end : $workspace['Workspace']['start_date'];

			$date_array['Element']['start_date'] = isset($element_date_array[0][0]['start_date']) && !empty($element_date_array[0][0]['start_date']) ? $element_date_array[0][0]['start_date'] : null;
			$date_array['Element']['end_date'] = isset($element_date_array[0][0]['end_date']) && !empty($element_date_array[0][0]['end_date']) ? $element_date_array[0][0]['end_date'] : null;

			$message = null;

			if (!empty($date_array['Element']['start_date'])) {

				if (date('Y-m-d', strtotime($date_array['Workspace']['start_date'])) < date('Y-m-d', strtotime($date_array['Project']['start_date']))) {
					$message = 'Workspace start date should not be less than project start date.';
				}
				if (date('Y-m-d', strtotime($date_array['Workspace']['end_date'])) > date('Y-m-d', strtotime($date_array['Project']['end_date']))) {
					$message = 'Workspace end date should not be greater than project end date.';
				}

				if (date('Y-m-d', strtotime($date_array['Workspace']['start_date'])) > date('Y-m-d', strtotime($date_array['Element']['start_date']))) {
					// $message = 'Workspace start date should not be greater than task start date.';
					$message = 'Workspace start date is after a task start date.';

				}
				if (date('Y-m-d', strtotime($date_array['Workspace']['end_date'])) < date('Y-m-d', strtotime($date_array['Element']['end_date']))) {
					$message = 'Workspace end date should be greater than task end date.';
				}
			}
		}

		if (isset($id) && empty($id) || $id == null || $id == '') {

			$project = $this->Project->find("first", array("fields" => array("Project.start_date", "Project.end_date"), "conditions" => array("Project.id" => $project_id)));
			$date_array['Project']['start_date'] = isset($project['Project']['start_date']) && !empty($project['Project']['start_date']) ? $project['Project']['start_date'] : null;
			$date_array['Project']['end_date'] = isset($project['Project']['end_date']) && !empty($project['Project']['end_date']) ? $project['Project']['end_date'] : null;

			$date_array['Workspace']['start_date'] = isset($start) && !empty($start) ? $start : null;
			$date_array['Workspace']['end_date'] = isset($end) && !empty($end) ? $end : null;

			$message = null;
			if (date('Y-m-d', strtotime($date_array['Workspace']['start_date'])) < date('Y-m-d', strtotime($date_array['Project']['start_date']))) {
				$message = 'Workspace start date should not be less than project start date.';
			}
			if (date('Y-m-d', strtotime($date_array['Workspace']['end_date'])) > date('Y-m-d', strtotime($date_array['Project']['end_date']))) {
				$message = 'Workspace end date should not be greater than project end date.';
			}
		}

		return $message;
	}

	public function getProjectUser($pid) {
		$data = ClassRegistry::init('UserProject')->find('first', array('conditions' => array('UserProject.project_id' => $pid, 'UserProject.owner_user' => 1)));
		return isset($data) ? $data : array();
	}

	public function userFullname($id) {
		//echo $id."<br>";
		$data = ClassRegistry::init('UserDetail')->find('first', array('conditions' => array('UserDetail.user_id' => $id), 'fields' => array('first_name', 'last_name')));

		//pr($data);

		return (isset($data['UserDetail']) && !empty($data['UserDetail'])) ? $data['UserDetail']['first_name'] . " " . $data['UserDetail']['last_name'] : "N/A";
	}

	public function ProjectOwner($pid, $uid = null) {
		$data = ClassRegistry::init('UserProject')->find('first', array('conditions' => array('UserProject.project_id' => $pid, 'UserProject.owner_user' => 1)));

		return isset($data) ? $data : array();
	}

	public function project_blog_email($user_id, $project_id, $blog_id) {

		$projectDetails = ClassRegistry::init('Project')->find('first', array('conditions' => array('Project.id' => $project_id)));
		$blogDetails = ClassRegistry::init('Blog')->find('first', array('conditions' => array('Blog.id' => $blog_id)));

		$pageAction = SITEURL . 'team_talks/index/project:' . $project_id.'/blog:'.$blog_id;

		//Send Email and redirect to list page
		$loggedInemail = $this->Session->read('Auth.User.email');
		$name = $this->Session->read('Auth.User.UserDetail.full_name');
		$usersDetails = ClassRegistry::init('User')->find('first', array('conditions' => array('User.id' => $user_id)));

		$emailAddress = $usersDetails['User']['email'];
		$email = new CakeEmail();
		$email->config('Smtp');
		//$email->from(array($loggedInemail => $name));
		$email->from(array(ADMIN_FROM_EMAIL => SITENAME));
		$email->to($emailAddress);
		// $email->subject( "$name Shared a Project" );
		$email->subject(SITENAME . ": Blog created");
		$email->template('blog_created_email');
		$email->emailFormat('html');
		$email->viewVars(array('Custname' => $usersDetails['UserDetail']['first_name'] . ' ' . $usersDetails['UserDetail']['last_name'], 'name' => $name, 'projectDetails' => $projectDetails, 'blogDetails' => $blogDetails,'open_page'=>$pageAction));
		$email->send();
		return true;
	}

	public function project_update_blog_email($user_id, $project_id, $blog_id) {

		$projectDetails = ClassRegistry::init('Project')->find('first', array('conditions' => array('Project.id' => $project_id)));
		$blogDetails = ClassRegistry::init('Blog')->find('first', array('conditions' => array('Blog.id' => $blog_id)));

		//Send Email and redirect to list page
		$loggedInemail = $this->Session->read('Auth.User.email');
		$name = $this->Session->read('Auth.User.UserDetail.full_name');
		$usersDetails = ClassRegistry::init('User')->find('first', array('conditions' => array('User.id' => $user_id)));

		$pageAction = SITEURL . 'team_talks/index/project:' . $project_id.'/blog:'.$blog_id;

		$emailAddress = $usersDetails['User']['email'];
		$email = new CakeEmail();
		$email->config('Smtp');
		//$email->from(array($loggedInemail => $name));
		$email->from(array(ADMIN_FROM_EMAIL => SITENAME));
		$email->to($emailAddress);
		// $email->subject( "$name Shared a Project" );
		$email->subject(SITENAME . ": Blog updated");
		$email->template('blog_updated_email');
		$email->emailFormat('html');
		$email->viewVars(array('Custname' => $usersDetails['UserDetail']['first_name'] . ' ' . $usersDetails['UserDetail']['last_name'], 'name' => $name, 'projectDetails' => $projectDetails, 'blogDetails' => $blogDetails,'open_page'=>$pageAction));
		$email->send();
		return true;
	}

	/* --------------------Below function is being used for accessing all owner level assigned project of the current user--------- */

	public function get_user_project_list($user_id = null) {
		App::import('Controller', 'Users');
		$Users = new UsersController;
		$projects = null;
		$myprojectlist = $myreceivedprojectlist = $mygroupprojectlist = array();
		// Find All current user's projects
		$myprojectlist = $Users->__myproject_selectbox($user_id);
		// Find All current user's received projects
		$myreceivedprojectlist = $Users->__receivedproject_selectbox($user_id);
		// Find All current user's group projects
		$mygroupprojectlist = $Users->__groupproject_selectbox($user_id);

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
		return $projects;
	}

	public function get_user_project_list_chat ($user_id = null) {
		$user_id = (isset($user_id) && !empty($user_id)) ? $user_id : $this->Session->read('Auth.User.id');
		if(isset($user_id) && !empty($user_id)){
		$query = "SELECT projects.id,projects.title
					FROM user_permissions
					inner join projects on user_permissions.project_id = projects.id
					WHERE user_permissions.user_id = '$user_id'   AND user_permissions.workspace_id IS NULL";

		$projectCount =  ClassRegistry::init('UserPermission')->query($query);
		$projects = array();
		$projectss = array();
		if(isset($projectCount) && !empty($projectCount)){

			foreach($projectCount as $prg){
				  $projects [$prg['projects']['id']] = $prg['projects']['title'];
			}
		}

		natcasesort($projects);
		}else{
			$projects = array();

		}
		return $projects;
	}

	public function get_user_project_list_chat1($user_id = null) {
		App::import('Controller', 'Users');
		$Users = new UsersController;
		$projects = null;
		$myprojectlist = $myreceivedprojectlist = $mygroupprojectlist = array();
		// Find All current user's projects
		$myprojectlist = $Users->__myproject_selectbox($user_id);
		// Find All current user's received projects
		$myreceivedprojectlist = $Users->__receivedproject_selectbox($user_id);
		// Find All current user's group projects
		$mygroupprojectlist = $Users->__groupproject_selectbox($user_id);

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
		return $projects;
	}

	/* ---------------------------Below function is being used for showing the project >> wsp(n) >> area(n) >> element(n) -> down level links , note etc ---------------------------- */

	public function get_project_chain($project_ids = array()) {
		$project_array = array();

		$this->Project = ClassRegistry::init('Project');
		$this->ProjectWorkspace = ClassRegistry::init('ProjectWorkspace');
		$this->Area = ClassRegistry::init('Area');
		$this->Element = ClassRegistry::init('Element');

		if (isset($project_ids) && !empty($project_ids)) {

			foreach ($project_ids as $project_id) {
				// Get Project details
				$this->Project->recursive = -1;
				$project = $this->Project->findById($project_id);
				if (isset($project['Project']) && !empty($project['Project'])) {

					$project_array[$project_id] = $project['Project'];

					// Get Project Workspaces
					$this->ProjectWorkspace->unbindModel(array('belongsTo' => array('Project')));

					$project_work = $this->ProjectWorkspace->find('all', ['recursive' => 1, 'conditions' => ['ProjectWorkspace.project_id' => $project_id, 'Workspace.id IS NOT NULL',
						'Workspace.studio_status <>' => 1], 'order' => ['ProjectWorkspace.project_id ASC', 'ProjectWorkspace.sort_order ASC'], 'contains' => array('Workspace')]);

					if (isset($project_work) && !empty($project_work)) {
						foreach ($project_work as $work_key => $workspace) {
							$project_array[$project_id]['Workspace'][] = $workspace['Workspace'];

							if (isset($workspace['Workspace']['id']) && !empty($workspace['Workspace']['id'])) {

								// Get workspcae's areas
								$this->Area->recursive = -1;
								$areas = $this->Area->find("all", array("conditions" => array("Area.workspace_id" => $workspace['Workspace']['id'], 'Area.studio_status <>' => 1)));

								if (isset($areas) && !empty($areas)) {
									foreach ($areas as $area_key => $area) {
										$project_array[$project_id]['Workspace'][$work_key]['Area'][] = $area['Area'];

										if (isset($area['Area']) && !empty($area['Area'])) {

											// Get Area's element
											$this->Element->unbindModel(array('belongsTo' => array('Area')));
											$elements = $this->Element->find("all", array("conditions" => array("Element.area_id" => $area['Area']['id'], 'Element.studio_status <>' => 1)));

											if (isset($elements) && !empty($elements)) {
												foreach ($elements as $element_key => $element) {
													$project_array[$project_id]['Workspace'][$work_key]['Area'][$area_key]['Element'][] = $element;
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
		}
		return $project_array;
	}

	public function userOrganisationId($email = null) {

		if (isset($email) && !empty($email)) {

			App::import("Model", "User");
			$User = new User();
			$data = $User->find('first', array('conditions' => array('User.email' => $email), 'recursive' => -1));
			return $data['UserDetail']['org_id'];

		}

	}

	public function userDetail($id) {
		$data = ClassRegistry::init('UserDetail')->find('first', array('conditions' => array('UserDetail.user_id' => $id), 'recursive' => -1));
		return isset($data) ? $data : array();
	}

	public function element_creator($element_id, $pid, $return = null) {

		$data = ClassRegistry::init('ElementPermission')->find('first', array('conditions' => array('ElementPermission.project_id' => $pid, 'ElementPermission.element_id' => $element_id, 'ElementPermission.is_editable' => 1)));

		$dataEC = ClassRegistry::init('UserProject')->find('first', array('conditions' => array('UserProject.project_id' => $pid, 'UserProject.owner_user' => 1)));

		if (isset($data) && !empty($data)) {
			$id = $data['ElementPermission']['user_id'];

			$dataUD = ClassRegistry::init('UserDetail')->find('first', array('conditions' => array('UserDetail.user_id' => $id), 'fields' => array('first_name', 'last_name', 'user_id')));

			$dataUDT['username'] = isset($dataUD) ? $dataUD['UserDetail']['first_name'] . " " . $dataUD['UserDetail']['last_name'] : 'N/A';
			$dataUDT['user_id'] = isset($dataUD) ? $dataUD['UserDetail']['user_id'] : 'N/A';
			//return isset($dataUD) ? $dataUD['UserDetail']['first_name']." " .$dataUD['UserDetail']['last_name'] : 'N/A';

			if (isset($dataUD) && !empty($dataUD)) {
				return $dataUDT;
			} else if (isset($return) && !empty($return)) {
				return null;
			} else {
				return 'N/A';
			}

			// return isset($dataUD) ? $dataUDT : 'N/A';
		} else {
			if (isset($return) && !empty($return)) {
				return null;
			}
			return 'N/A';
		}
	}

	public function elementActiv($id) { }

	public function getProjectAllUser($project_id = null, $share_user_id = null) {

		$view = new View();
		$commonHelper = $view->loadHelper('Common');
		$ownerdetails = $commonHelper->ProjectOwner($project_id, $this->Session->read('Auth.User.id'));

		$data = array();
		$data1 = array();
		$data2 = array();
		$data3 = array();

		$data5[] = $ownerdetails['UserProject']['user_id'];
		$data4 = participants_group_owner($project_id);
		$data3 = participants_owners($project_id, $ownerdetails['UserProject']['user_id']);
		$data2 = participants($project_id, $ownerdetails['UserProject']['user_id']);
		$data1 = participants_group_sharer($project_id);

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
		$onw = array_search($this->Session->read('Auth.User.id'), $all_owner, true);

		if (isset($onw)) {
			unset($all_owner[$onw]);
		}

		$shr = array_search($share_user_id, $all_owner);
		if (isset($shr)) {unset($all_owner[$shr]);}

		$loggedInUser = $this->Session->read('Auth.User.UserDetail.first_name') . ' ' . $this->Session->read('Auth.User.UserDetail.last_name');

		$sharedUser = ClassRegistry::init('User')->find('first', array('conditions' => array('User.id' => $share_user_id)));
		$addedto = $sharedUser['UserDetail']['first_name'] . ' ' . $sharedUser['UserDetail']['last_name'];

		$projecttitle = ClassRegistry::init('Project')->find('first', array('conditions' => array('Project.id' => $project_id)));

		$projectname = $projecttitle['Project']['title'];
		$pageAction = SITEURL . 'projects/index/' . $project_id;

		if (isset($all_owner) && !empty($all_owner)) {
			if (($key = array_search($this->Session->read('Auth.User.id'), $all_owner)) !== false) {
				unset($all_owner[$key]);
			}
		}
		if( isset($all_owner) && !empty($all_owner) ){

			foreach ($all_owner as $valData) {

				$notifiragstatus = ClassRegistry::init('EmailNotification')->find('first', ['conditions' => ['notification_type' => 'project', 'personlization' => 'project_new_member', 'user_id' => $valData]]);

				$usersDetails = ClassRegistry::init('User')->find('first', array('conditions' => array('User.id' => $valData)));

				if( isset($usersDetails) && !empty($usersDetails) ){
					if ((!isset($notifiragstatus['EmailNotification']['email']) || $notifiragstatus['EmailNotification']['email'] == 1) && (!isset($usersDetails['User']['email_notification']) || $usersDetails['User']['email_notification'] == 1)) {
$owner_name = 'N/A';
if( isset($usersDetails['UserDetail']['first_name']) && !empty($usersDetails['UserDetail']['first_name']) && isset($usersDetails['UserDetail']['last_name']) && !empty($usersDetails['UserDetail']['last_name']) ){
	$owner_name = $usersDetails['UserDetail']['first_name']." ".$usersDetails['UserDetail']['last_name'];
} else if( isset($usersDetails['UserDetail']['first_name']) && !empty($usersDetails['UserDetail']['first_name']) && !isset($usersDetails['UserDetail']['last_name']) && empty($usersDetails['UserDetail']['last_name']) ){
	$owner_name = $usersDetails['UserDetail']['first_name'];
} else if( !isset($usersDetails['UserDetail']['first_name']) && empty($usersDetails['UserDetail']['first_name']) && isset($usersDetails['UserDetail']['last_name']) && !empty($usersDetails['UserDetail']['last_name'])) {
	$owner_name = $usersDetails['UserDetail']['last_name'];
} else {
	$owner_name = 'N/A';
}

/* $owner_name = isset($usersDetails['UserDetail']['first_name'])? $usersDetails['UserDetail']['first_name'] : 'N/A' . ' ' . isset($usersDetails['UserDetail']['last_name'])? $usersDetails['UserDetail']['last_name'] : 'N/A'; */

						$email = new CakeEmail();
						$email->config('Smtp');
						$email->from(array(ADMIN_FROM_EMAIL => SITENAME));
						$email->to($usersDetails['User']['email']);
						$email->subject(SITENAME . ": New project member");
						$email->template('project_new_member');
						$email->emailFormat('html');
						$email->viewVars(array('project_name' => $projectname, 'owner_name' => $owner_name, 'addedto' => $addedto, 'addedby' => $loggedInUser,'open_page'=>$pageAction));
						$email->send();

					}
				}
			}
		}

	}

	public function projectScheduleChangeEmail($project_id = null) {

		$view = new View();
		$commonHelper = $view->loadHelper('Common');
		$ownerdetails = $commonHelper->ProjectOwner($project_id, $this->Session->read('Auth.User.id'));

		$data = array();
		$data1 = array();
		$data2 = array();
		$data3 = array();

		$data5[] = $ownerdetails['UserProject']['user_id'];
		$data4 = participants_group_owner($project_id);
		$data3 = participants_owners($project_id, $ownerdetails['UserProject']['user_id']);
		$data2 = participants($project_id, $ownerdetails['UserProject']['user_id']);
		$data1 = participants_group_sharer($project_id);

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
		$onw = array_search($this->Session->read('Auth.User.id'), $all_owner, true);

		if (isset($onw)) {
			unset($all_owner[$onw]);
		}

		$loggedInUser = $this->Session->read('Auth.User.id');
		$sharedUser = ClassRegistry::init('User')->find('first', array('conditions' => array('User.id' => $loggedInUser)));
		$changedBy = $sharedUser['UserDetail']['first_name'] . ' ' . $sharedUser['UserDetail']['last_name'];

		$projecttitle = ClassRegistry::init('Project')->find('first', array('conditions' => array('Project.id' => $project_id)));
		$projectname = $projecttitle['Project']['title'];

		$projectAction = SITEURL . 'projects/index/' . $project_id;
		if (isset($all_owner) && !empty($all_owner)) {
			if (($key = array_search($this->Session->read('Auth.User.id'), $all_owner)) !== false) {
				unset($all_owner[$key]);
			}
		}
		if( isset($all_owner) && !empty($all_owner) ){
			foreach ($all_owner as $valData) {

				$notifiragstatus = ClassRegistry::init('EmailNotification')->find('first', ['conditions' => ['notification_type' => 'project', 'personlization' => 'project_schedule_change', 'user_id' => $valData]]);

				$usersDetails = ClassRegistry::init('User')->find('first', array('conditions' => array('User.id' => $valData)));
				if( isset($usersDetails) && !empty($usersDetails) ){
					if ((!isset($notifiragstatus['EmailNotification']['email']) || $notifiragstatus['EmailNotification']['email'] == 1) && (!isset($usersDetails['User']['email_notification']) || $usersDetails['User']['email_notification'] == 1)) {

						$owner_name = $usersDetails['UserDetail']['first_name'] . ' ' . $usersDetails['UserDetail']['last_name'];

						$email = new CakeEmail();
						$email->config('Smtp');
						$email->from(array(ADMIN_FROM_EMAIL => MAIL_SITENAME));
						$email->to($usersDetails['User']['email']);
						$email->subject(SITENAME . ': Project schedule change');
						$email->template('project_schedule_change');
						$email->emailFormat('html');
						$email->viewVars(array('project_name' => $projectname, 'owner_name' => $owner_name, 'changedBy' => $changedBy,'open_page'=>$projectAction));
						$email->send();

					}
				}
			}
		}

	}

	public function workspaceScheduleChangeEmail($project_id = null, $workspace_id = null) {

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

		//pr($data1);

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

		//pr($all_owner);
		$all_owner = array_unique($all_owner);
		//pr($all_owner); die;

		$loggedInUser = $this->Session->read('Auth.User.id');
		$sharedUser = ClassRegistry::init('User')->find('first', array('conditions' => array('User.id' => $loggedInUser)));
		$changedBy = $sharedUser['UserDetail']['first_name'] . ' ' . $sharedUser['UserDetail']['last_name'];

		$projecttitle = ClassRegistry::init('Project')->find('first', array('conditions' => array('Project.id' => $project_id)));
		$workspacetitle = ClassRegistry::init('Workspace')->find('first', array('conditions' => array('Workspace.id' => $workspace_id)));
		$projectname = $projecttitle['Project']['title'];
		$workspace_name = $workspacetitle['Workspace']['title'];

		$workspaceAction = SITEURL . 'projects/manage_elements/' . $project_id . '/' . $workspace_id;
		if (isset($all_owner) && !empty($all_owner)) {
			if (($key = array_search($this->Session->read('Auth.User.id'), $all_owner)) !== false) {
				unset($all_owner[$key]);
			}
		}
		if( isset($all_owner) && !empty($all_owner) ){
			foreach ($all_owner as $valData) {

				$notifiragstatus = ClassRegistry::init('EmailNotification')->find('first', ['conditions' => ['notification_type' => 'workspace', 'personlization' => 'workspace_schedule_change', 'user_id' => $valData]]);

				$usersDetails = ClassRegistry::init('User')->find('first', array('conditions' => array('User.id' => $valData)));
				if( isset($usersDetails) && !empty($usersDetails) ){
					if ((!isset($notifiragstatus['EmailNotification']['email']) || $notifiragstatus['EmailNotification']['email'] == 1) && (!isset($usersDetails['User']['email_notification']) || $usersDetails['User']['email_notification'] == 1)) {

						$owner_name = $usersDetails['UserDetail']['first_name'] . ' ' . $usersDetails['UserDetail']['last_name'];

						$email = new CakeEmail();
						$email->config('Smtp');
						$email->from(array(ADMIN_FROM_EMAIL => MAIL_SITENAME));
						$email->to($usersDetails['User']['email']);
						$email->subject(SITENAME . ': Workspace schedule change');
						$email->template('workspace_schedule_change');
						$email->emailFormat('html');
						$email->viewVars(array('workspace_name' => $workspace_name, 'owner_name' => $owner_name, 'changedBy' => $changedBy, 'project_name' => $projectname,'workspaceAction' => $workspaceAction,'open_page' => $workspaceAction));
						$email->send();

					}
				}
			}
		}

	}

	/* ============================== Create Subdomain, Database, Username and Password =====================  */
	function checksubdomain($subdomain = null, $password = null) {

		$cPanelUser = CPANELUSR;
		$cPanelPass = CPANELPASS;
		$rootDomain = ROOTDOMAIN;

		if (!empty(DOMAINPREFIX)) {
			$domainPrefix = DOMAINPREFIX;
		} else {
			$domainPrefix = DOMAIN_ALISE;
		}

		$xmlapi = new XmlApi('127.0.0.1', $cPanelUser, $cPanelPass);
		$xmlapi->set_port(2083);
		$xmlapi->set_output('json');
		$xmlapi->set_hash("username", $cPanelUser);
		$xmlapi->password_auth($cPanelUser, $cPanelPass);
		$xmlapi->set_debug(1);

		$args = array(
			'domain' => $subdomain,
			'rootdomain' => ROOTDOMAIN,
			'canoff' => 0,
			'dir' => HOST_DIR,
		);

		$createdb = array(
			'db' => $domainPrefix . $subdomain,
		);

		/* $createUserargs = array(
				'dbuser'   => $domainPrefix.$subdomain,
				'password' => $password,
		); */

		$createUserargs = array(
			'dbuser' => $subdomain,
			'password' => $password,
		);

		/* $argsprivilage = array(
			        'privileges' => 'ALL',
			        'db' => $domainPrefix.$subdomain,
			        'dbuser' => $domainPrefix.$subdomain,
		); */

		$argsprivilage = array(
			'privileges' => 'ALL',
			'db' => $domainPrefix . $subdomain,
			'dbuser' => $subdomain,
		);

		$checkdomainarg = array('regex' => 'rutabaga');

		/*================  check domain already exists or not =========================*/

		$subdomainsList = $xmlapi->api2_query($cPanelUser, 'SubDomain', 'listsubdomains');
		$result = json_decode($subdomainsList);
		$liveSubdomain = 0;



		foreach ($result->cpanelresult->data as $domainList) {
			if ($subdomain == $domainList->subdomain) {
				$liveSubdomain = 1;
			}
		}

		/*================  check database already exists or not =========================*/

		$list_addons = $xmlapi->api2_query($cPanelUser, 'AddonDomain', 'listaddondomains', $checkdomainarg);
		$list_database = $xmlapi->api2_query($cPanelUser, 'MysqlFE', 'listdbs');

		$listsdb = json_decode($list_database);
		$dblistArray = array();
		$liveDatabase = 0;
		foreach ($listsdb->cpanelresult->data as $dbvalue) {
			$dblistArray[] = $dbvalue->db;
		}
		if (in_array($domainPrefix . $subdomain, $dblistArray)) {
			$liveDatabase = 1;
			//$message = "Database '".$domainPrefix.$subdomain."' already exists. choose a different database name.";
		}

		/*=========================================================================*/

		$error = false;
		$message = '';

		if ($liveSubdomain > 0) {

			$error = true;
			//$message = "$domainPrefix.$subdomain domain has already been taken";
			$message = "This domain has already been taken";

		} else if ($liveDatabase > 0) {

			$error = true;
			//$message = "Database $domainPrefix.$subdomain already exists. choose a different database name";
			$message = "This domain database already exists, choose a different database name";

		} else {
			$error = false;
		}

		if ($error) {
			return $message;
		} else {
			return "success";
		}

	}

	function addsubdomain($subdomain = null, $password = null) {

		$cPanelUser = CPANELUSR;
		$cPanelPass = CPANELPASS;
		$rootDomain = ROOTDOMAIN;

		if (!empty(DOMAINPREFIX)) {
			$domainPrefix = DOMAINPREFIX;
		} else {
			$domainPrefix = DOMAIN_ALISE;
		}

		$xmlapi = new XmlApi('127.0.0.1', $cPanelUser, $cPanelPass);
		$xmlapi->set_port(2083);
		$xmlapi->set_output('json');
		$xmlapi->set_hash("username", $cPanelUser);
		$xmlapi->password_auth($cPanelUser, $cPanelPass);
		$xmlapi->set_debug(1);



		$args = array(
			'domain' => $subdomain,
			'rootdomain' => ROOTDOMAIN,
			'canoff' => 0,
			'dir' => HOST_DIR,
		);

		// remove dot and hyphen from DATABASE name
		$ndbname = preg_replace('/[.,]/', '', $subdomain);
		$dbnames = str_replace('-', '', $ndbname);

		$dbuser =  $dbnames;
		//$dbuser = $domainPrefix . $dbnames;
		if (strlen($dbnames) > 16) {
			$dbuser = substr($dbnames, 0, 16);
		}

		$createdb = array(
			'db' => $domainPrefix . $dbnames,
		);
		//========================================
		/* $createUserargs = array(
				'dbuser'   => $domainPrefix.$subdomain,
				'password' => $password,
		); */
		/* $argsprivilage = array(
			        'privileges' => 'ALL',
			        'db' => $domainPrefix.$subdomain,
			        'dbuser' => $domainPrefix.$subdomain,
		); */

		$createUserargs = array(
			'dbuser' => $dbuser,
			'password' => $password,
		);

		$argsprivilage = array(
			'privileges' => 'ALL',
			'db' => $domainPrefix . $dbnames,
			'dbuser' => $dbuser,
		);

		$checkdomainarg = array('regex' => 'rutabaga');

		/*================  check domain already exists or not =========================*/

		$subdomainsList = $xmlapi->api2_query($cPanelUser, 'SubDomain', 'listsubdomains');
		$result = json_decode($subdomainsList);
		$liveSubdomain = 0;
		foreach ($result->cpanelresult->data as $domainList) {
			if ($subdomain == $domainList->subdomain) {
				$liveSubdomain = 1;
			}
		}

		/*================  check database already exists or not =========================*/

		$list_addons = $xmlapi->api2_query($cPanelUser, 'AddonDomain', 'listaddondomains', $checkdomainarg);
		$list_database = $xmlapi->api2_query($cPanelUser, 'MysqlFE', 'listdbs');

		$listsdb = json_decode($list_database);
		$dblistArray = array();
		$liveDatabase = 0;
		foreach ($listsdb->cpanelresult->data as $dbvalue) {
			$dblistArray[] = $dbvalue->db;
		}
		//if( in_array( $domainPrefix.$subdomain, $dblistArray )  ){
		if (in_array($domainPrefix . $dbnames, $dblistArray)) {
			$liveDatabase = 1;
			//$message = "Database '".$domainPrefix.$subdomain."' already exists. choose a different database name.";
		}

		/*=========================================================================*/

		$error = false;
		$message = '';

		if ($liveSubdomain > 0) {

			$error = true;
			$message = "$domainPrefix.$subdomain domain has already been taken";

		} else if ($liveDatabase > 0) {

			$error = true;
			$message = "Database $domainPrefix.$subdomain already exists. choose a different database name";

		} else {



			$subdomainsList1 = $xmlapi->api2_query($cPanelUser, 'SubDomain', 'addsubdomain', $args);

		//	$installssl = $xmlapi->api2_query($user, "SSL", "installssl", array(cabundle => $cabundle, crt => $crt, domain => $domain, key => $key, ip => $ip, user=> $user));

			if($_SERVER['SERVER_ADDR'] == prod_host){

			$subdomainsList2 = $xmlapi->api2_query($cPanelUser, 'MysqlFE', 'createdb', $createdb);

			$subdomainsList3 = $xmlapi->api2_query($cPanelUser, 'MysqlFE', 'createdbuser', $createUserargs);
			$subdomainsList4 = $xmlapi->api2_query($cPanelUser, 'MysqlFE', 'setdbuserprivileges', $argsprivilage);

			$subdomainsList11 = json_decode($subdomainsList1);
			$subdomainsList12 = json_decode($subdomainsList2);
			$subdomainsList13 = json_decode($subdomainsList3);
			$subdomainsList14 = json_decode($subdomainsList4);

			if (!empty($subdomainsList11->cpanelresult->error)) {
				$error = true;
				$message = utf8_decode($subdomainsList11->cpanelresult->error);

			} else if (!empty($subdomainsList12->cpanelresult->error)) {

				$error = true;
				$message = utf8_decode($subdomainsList12->cpanelresult->error);

			} else if (!empty($subdomainsList13->cpanelresult->error)) {

				$error = true;
				$message = utf8_decode($subdomainsList13->cpanelresult->error);

			} else if (!empty($subdomainsList14->cpanelresult->error)) {

				$error = true;
				$message = utf8_decode($subdomainsList14->cpanelresult->error);
			}

			}else{

				$connection =  mysqli_connect(root_host, root_dbuser, root_dbpass);

				// Create database
				 $dbC = $domainPrefix.$dbnames;
				$sql="CREATE DATABASE  $dbC CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";

				if (mysqli_query($connection,$sql))
				{
					$message =  "success db";
				}
				else
				{
					$message =  "Error creating database: " . mysqli_error($connection);
				}


				// Create user permissions


				$sql="CREATE USER '$dbuser'@'".AWS_HOST."' IDENTIFIED WITH mysql_native_password AS '$password';";

				$sql1="GRANT ALL PRIVILEGES ON *.* TO '$dbuser'@'".AWS_HOST."' REQUIRE NONE WITH GRANT OPTION MAX_QUERIES_PER_HOUR 0 MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0;";

				$sql2="GRANT ALL PRIVILEGES ON `$dbC`.* TO '$dbuser'@'".AWS_HOST."';";

				$sql3="GRANT ALL PRIVILEGES ON `$dbC\_%`.* TO '$dbuser'@'".AWS_HOST."';";

				$sql4="ALTER USER '$dbuser'@'".AWS_HOST."' IDENTIFIED BY '$password';";


				if (mysqli_query($connection,$sql)){
					/* comment below line to set only access to single database permission for this user */
					//mysqli_query($connection,$sql1);
					mysqli_query($connection,$sql2);
					mysqli_query($connection,$sql3);
					mysqli_query($connection,$sql4);

					$message =  "success permission";
				}else{
					$message =  "Error";
				}


			}

		}

		if ($error) {
			return $message;
		} else {
			return "success";
		}

	}
	// Domain delete for every thing
	function deletesubdomain($subdomain = null, $databasename = null, $dbusername = null) {


		$cPanelUser = CPANELUSR;
		$cPanelPass = CPANELPASS;
		$rootDomain = ROOTDOMAIN;

		if (!empty(DOMAINPREFIX)) {
			$domainPrefix = DOMAINPREFIX;
		} else {
			$domainPrefix = DOMAIN_ALISE;
		}

		$xmlapi = new XmlApi('127.0.0.1', $cPanelUser, $cPanelPass);
		$xmlapi->set_port(2083);
		$xmlapi->set_output('json');
		$xmlapi->set_hash("username", $cPanelUser);
		$xmlapi->password_auth($cPanelUser, $cPanelPass);
		$xmlapi->set_debug(1);


		$delete_domain = array(
			'domain' => $subdomain . '.' . ROOTDOMAIN,
		);



		$delete_database = array(
			'db' => $databasename,
		);

		$delete_user = array(
			'dbuser' => $dbusername,
		);

		$error = false;
		$message = '';
		//$subdomainsList1 = $xmlapi->api2_query($cPanelUser, 'SubDomain', 'delsubdomain', $delete_domain);

		 if($_SERVER['SERVER_ADDR'] == prod_host){

		 $subdomainsList1 = $xmlapi->api2_query($cPanelUser, 'SubDomain', 'delsubdomain', $delete_domain);

		}else{

		$subdomainsList1 = $xmlapi->api2_query($cPanelUser,
                               "SubDomain", "delsubdomain",
                                array(
                                     'domain' => "$subdomain"."_".ROOTDOMAIN,
                                     'domaindisplay'=>"$subdomain.".ROOTDOMAIN,
                                )
                              );

		}

		if($_SERVER['SERVER_ADDR'] == prod_host){

		$subdomainsList2 = $xmlapi->api2_query($cPanelUser, 'MysqlFE', 'deletedbuser', $delete_user);
		$subdomainsList3 = $xmlapi->api2_query($cPanelUser, 'MysqlFE', 'deletedb', $delete_database);

		/* pr($subdomainsList1);
		pr($subdomainsList2);
		pr($subdomainsList3);  */

		//die;

		$subdomainsList11 = json_decode($subdomainsList1);
		$subdomainsList22 = json_decode($subdomainsList2);
		$subdomainsList33 = json_decode($subdomainsList3);

		if (!empty($subdomainsList11->cpanelresult->error)) {

			$error = true;
			$message = $subdomainsList11->cpanelresult->error;

		} else if (!empty($subdomainsList22->cpanelresult->error)) {

			$error = true;
			$message = $subdomainsList22->cpanelresult->error;

		} else if (!empty($subdomainsList33->cpanelresult->error)) {

			$error = true;
			$message = $subdomainsList33->cpanelresult->error;
		}


		}else{


				$connection =  mysqli_connect(root_host, root_dbuser, root_dbpass);

				// Create database

				$sql="DROP DATABASE ".$databasename;

				if (mysqli_query($connection,$sql))
				{
					$sql5="REVOKE ALL PRIVILEGES, GRANT OPTION FROM '$dbusername'@'".AWS_HOST."'";

					$sql3= "DROP USER '$dbusername'@'".AWS_HOST."'";

					mysqli_query($connection,$sql5) ;
					mysqli_query($connection,$sql3) ;
					$message =  "success drop db";
				}
				else
				{
					$message =  "Error drop database: " . mysqli_error($connection);
				}


		}

		if ($error) {
			return $message;
		} else {
			return true;
		}






	}

	// Seprate domain delete
	function deletesubdomainlive($subdomain = null, $databasename = null, $dbusername = null) {

		$cPanelUser = CPANELUSR;
		$cPanelPass = CPANELPASS;
		$rootDomain = ROOTDOMAIN;

		if (!empty(DOMAINPREFIX)) {
			$domainPrefix = DOMAINPREFIX;
		} else {
			$domainPrefix = DOMAIN_ALISE;
		}

		$xmlapi = new XmlApi('127.0.0.1', $cPanelUser, $cPanelPass);
		$xmlapi->set_port(2083);
		$xmlapi->set_output('json');
		$xmlapi->set_hash("username", $cPanelUser);
		$xmlapi->password_auth($cPanelUser, $cPanelPass);
		$xmlapi->set_debug(1);


		$delete_domain = array(
			'domain' => $subdomain . '.' . ROOTDOMAIN,
		);



		$delete_database = array(
			'db' => $databasename,
		);

		$delete_user = array(
			'dbuser' => $dbusername,
		);

		$error = false;
		$message = '';
		//$subdomainsList1 = $xmlapi->api2_query($cPanelUser, 'SubDomain', 'delsubdomain', $delete_domain);

		 if($_SERVER['SERVER_ADDR'] == prod_host){

		 $subdomainsList1 = $xmlapi->api2_query($cPanelUser, 'SubDomain', 'delsubdomain', $delete_domain);

		}else{

		$subdomainsList1 = $xmlapi->api2_query($cPanelUser,
                               "SubDomain", "delsubdomain",
                                array(
                                     'domain' => "$subdomain"."_".ROOTDOMAIN,
                                     'domaindisplay'=>"$subdomain.".ROOTDOMAIN,
                                )
                              );

		}

		if($_SERVER['SERVER_ADDR'] == prod_host){

		$subdomainsList2 = $xmlapi->api2_query($cPanelUser, 'MysqlFE', 'deletedbuser', $delete_user);
		$subdomainsList3 = $xmlapi->api2_query($cPanelUser, 'MysqlFE', 'deletedb', $delete_database);

		/* pr($subdomainsList1);
		pr($subdomainsList2);
		pr($subdomainsList3);  */

		//die;

		$subdomainsList11 = json_decode($subdomainsList1);
		$subdomainsList22 = json_decode($subdomainsList2);
		$subdomainsList33 = json_decode($subdomainsList3);

		if (!empty($subdomainsList11->cpanelresult->error)) {

			$error = true;
			$message = $subdomainsList11->cpanelresult->error;

		} else if (!empty($subdomainsList22->cpanelresult->error)) {

			$error = true;
			$message = $subdomainsList22->cpanelresult->error;

		} else if (!empty($subdomainsList33->cpanelresult->error)) {

			$error = true;
			$message = $subdomainsList33->cpanelresult->error;
		}


		}else{


				$connection =  mysqli_connect(root_host, root_dbuser, root_dbpass);

				// Create database

				$sql="DROP DATABASE ".$databasename;

				if (mysqli_query($connection,$sql))
				{
					$sql5="REVOKE ALL PRIVILEGES, GRANT OPTION FROM '$dbusername'@'".AWS_HOST."'";

					$sql3= "DROP USER '$dbusername'@'".AWS_HOST."'";

					mysqli_query($connection,$sql5) ;
					mysqli_query($connection,$sql3) ;
					$message =  "success drop db";
				}
				else
				{
					$message =  "Error drop database: " . mysqli_error($connection);
				}


		}

		if ($error) {
			return $message;
		} else {
			return true;
		}


	}

	function generatestrongpassword($length = 9, $add_dashes = false, $available_sets = 'quct') {
		$this->autoRender = false;
		$sets = array();
		if (strpos($available_sets, 'q') !== false) {
			$sets[] = 'abcdefghjkmnpqrstuvwxyz';
		}

		if (strpos($available_sets, 'u') !== false) {
			$sets[] = 'ABCDEFGHJKMNPQRSTUVWXYZ';
		}

		if (strpos($available_sets, 'c') !== false) {
			$sets[] = '23456789';
		}

		if (strpos($available_sets, 't') !== false) {
			$sets[] = '!@#$%&*?';
		}

		$all = '';
		$password = '';
		foreach ($sets as $set) {
			$password .= $set[array_rand(str_split($set))];
			$all .= $set;
		}
		$all = str_split($all);
		for ($i = 0; $i < $length - count($sets); $i++) {
			$password .= $all[array_rand($all)];
		}

		$password = str_shuffle($password);
		if (!$add_dashes) {
			return $password;
		}

		$dash_len = floor(sqrt($length));
		$dash_str = '';
		while (strlen($password) > $dash_len) {
			$dash_str .= substr($password, 0, $dash_len) . '-';
			$password = substr($password, $dash_len);
		}
		$dash_str .= $password;
		return $dash_str;
	}

	/* ============ Create Subdomain, Database, Username and Password ===============  */

	public function get_api_todo_status($id = null, $user_id = null) {

		$status = ClassRegistry::init('DoList');
		$returndata = "N/A";
		$status->recursive = 2;
		$status->unbindModel(array("hasMany" => array("DoListUser")));
		$status->bindModel(array("hasOne" => array("DoListUser")));
		$data = $status->find("first", array("conditions" => array('DoList.id' => $id)));
		// pr($data);
		if (isset($data['DoList']['sign_off']) && $data['DoList']['sign_off'] == 1) {
			$returndata = 'Completed';
		} else if (isset($data['DoList']['end_date']) && $data['DoList']['sign_off'] == 0 && !empty($data['DoList']['end_date']) && strtotime($data['DoList']['end_date']) < strtotime(date("Y-m-d"))) {
			$returndata = 'Overdue';
		} else if (isset($data['DoList']['start_date']) && !empty($data['DoList']['start_date']) && strtotime($data['DoList']['start_date']) <= strtotime(date("Y-m-d")) && strtotime($data['DoList']['end_date']) >= strtotime(date("Y-m-d"))) {
			$returndata = 'Progressing';
		} else if (isset($data['DoList']['start_date']) && !empty($data['DoList']['end_date']) && !empty($data['DoList']['start_date']) && strtotime($data['DoList']['start_date']) > strtotime(date("Y-m-d")) && strtotime($data['DoList']['end_date']) >= strtotime(date("Y-m-d"))) {
			$returndata = 'Not Started';
		} else if (empty($data['DoList']['start_date']) && empty($data['DoList']['end_date'])) {
			$returndata = 'Not Specified';
		}
		return $returndata;
	}

	public function singoffelement($id) {



		$element = ClassRegistry::init('Element');
		$element_decision = ClassRegistry::init('ElementDecision');
		$element_decision_detail = ClassRegistry::init('ElementDecisionDetail');
		$element_vote = ClassRegistry::init('Vote');
		$element_feedback = ClassRegistry::init('Feedback');

		$feedback = '0';
		$descision = '0';
		$vote = '0';
		// First of all check element itself that it is signed off or not
		$element_signoff = $element->find('count', ['conditions' => ['Element.id' => $id, 'Element.sign_off >' => 0]]);
		if (!empty($element_signoff)) {
			return "Already Signed Off";
		}

		$ed_count = $element_decision->find('count', array('conditions' => array('ElementDecision.element_id' => $id, 'ElementDecision.sign_off >' => 0)));
		if (isset($ed_count) && !empty($ed_count)) {
			$descision = 1;
		} else {
			$ed_count = $element_decision->find('count', array('conditions' => array('ElementDecision.element_id' => $id)));
			if (empty($ed_count)) {
				$descision = 1;
			} else {
				$edd_count_comp = $element_decision->ElementDecisionDetail->find('count', ['conditions' => ['ElementDecision.element_id' => $id, 'ElementDecisionDetail.stage_status' => 1]]);

				$edd_count_comp_blank = $element_decision->ElementDecisionDetail->find('count', ['conditions' => ['ElementDecision.element_id' => $id, 'ElementDecisionDetail.stage_status' => 0]]);
				if ((!empty($edd_count_comp) && $edd_count_comp == '6') || (!empty($edd_count_comp_blank) && $edd_count_comp_blank == '6')) {
				//	$descision = 1;
				} else if (empty($edd_count_comp) && empty($edd_count_comp_blank)) {
				//	$descision = 1;
				}
			}
		}



		$signoff_feedback_count = $element_feedback->find('count', array('conditions' => array('Feedback.element_id' => $id, 'Feedback.sign_off' => '0')));

		if (empty($signoff_feedback_count)) {
			$feedback = 1;
		}


		$signoff_vote_count = $element_vote->find('count', array('conditions' => array('Vote.element_id' => $id, 'Vote.is_completed' => '0', 'VoteQuestion.id !=' => '')));


		if (empty($signoff_vote_count)) {
			$vote = 1;
		}




		$RmElement = ClassRegistry::init('RmElement');

		$RmUsers = ClassRegistry::init('RmUser');

		$RmDetails =  ClassRegistry::init('RmDetail');

		$rm_deatils = $RmElement->find('all', array('conditions' => array('RmElement.element_id' => $id)));
		$elementriskcount = 0;
		if (isset($rm_deatils) && !empty($rm_deatils)) {

			foreach( $rm_deatils as $rmelement ){
				$rmdetailid = $rmelement['RmElement']['rm_detail_id'];
				$elementriskcount = $RmDetails->find('count', array('conditions' => array('RmDetail.status !=' => 3, 'RmDetail.id' => $rmdetailid)) );
			}

		}

		if (isset($elementriskcount) && $elementriskcount > 0) {
			$eleRiskCnt = $elementriskcount;
		} else {
			$eleRiskCnt = 0;
		}



		//  echo $feedback.' - feedback<br>'.$vote.'- vote<br>'.$descision;
		$message = '';
		if (!empty($descision) && !empty($vote) && !empty($feedback) && ( empty($eleRiskCnt) || $eleRiskCnt == 0 ) ) {
			return 1;
		} else if (empty($descision) && !empty($vote) && !empty($feedback)) {
			// $message = 'You cannot sign off a task while decision is in process.';
			$message = 'You cannot sign off this Task because a Decision is in progress.';
		} else if (!empty($descision) && empty($vote) && !empty($feedback)) {
			$message = 'You cannot sign off a Task while Vote is in process.';
		} else if (!empty($descision) && !empty($vote) && empty($feedback)) {
			//$message = 'Cannot Sign-off, Feedback in process.';
			//$message = 'You cannot sign off a Task while feedback is in progress.';
			$message = 'You cannot sign off this Task because a Feedback is in progress.';
		} else if (empty($descision) && empty($vote) && !empty($feedback)) {
			//$message = 'You cannot sign off a task while decision and vote are in process.';
			$message = 'You cannot sign off this Task because a Decision and Vote are in progress.';
		} else if (!empty($descision) && empty($vote) && empty($feedback)) {

			//$message = 'You cannot sign off a task while feedback and vote are in process.';
			$message = 'You cannot sign off this Task because a Feedback and Vote are in progress.';

		} else if (empty($descision) && !empty($vote) && empty($feedback)) {
			//$message = 'You cannot sign off a task while decision and feedback are in process.';
			$message = 'You cannot sign off this Task because a Decision and Feedback are in progress.';
		} else if (empty($descision) && empty($vote) && empty($feedback)) {
			// $message = 'You cannot sign off a task while decision and feedback and vote are in process.';
			$message = 'You cannot sign off this Task because a Decision, Feedback and Vote are in progress.';

		} else if( isset($eleRiskCnt) && $eleRiskCnt > 0 ){
			//$message = 'You cannot sign off a task while Risks that are related and not signed off.';
			$message = 'You cannot sign off this Task because there is a related Risk.';
		}



		if (!empty($message)) {
			return $message;
		} else {
			return 1;
		}
	}

	public function get_users_by_skills($userIds = null, $skillIds = null) {
		$response = array();
		if(!is_null($userIds) && !is_null($skillIds)) {
			$this->User = ClassRegistry::init('User');

			$caseCond = $totalParam = "";
			foreach($skillIds as $k => $skill) {
				$caseCond .= " , SUM(CASE WHEN skill_id = ".$skill." THEN 1 ELSE 0 END) AS T".$k." ";
				$totalParam .= " 'T".$k."'+";
			}
			$totalParam = rtrim($totalParam, '+');


			$fetchSQL = "SELECT `UserDetails`.`user_id`, COUNT(".$totalParam.") as totalParam, `UserSkill`.`user_id`, `UserSkill`.`id`, CONCAT(UserDetails.first_name, \" \", UserDetails.last_name) AS user_name ".$caseCond." FROM `users` AS `User` INNER JOIN `user_skills` AS `UserSkill` ON (`UserSkill`.`user_id` = `User`.`id`) INNER JOIN `user_details` AS `UserDetails` ON (`UserDetails`.`user_id` = `User`.`id`)  WHERE `UserSkill`.`user_id` IN (".$userIds.") AND `UserSkill`.`skill_id` IN (".implode(',', $skillIds).")  GROUP BY `UserSkill`.`user_id` HAVING totalParam = ".count($skillIds);

			$fetchUsers = $this->User->query($fetchSQL);

			if(!empty($fetchUsers) && count($fetchUsers) > 0) {
				$response = Set::combine($fetchUsers, '{n}.UserDetails.user_id', '{n}.0.user_name');
			}
		}
		return $response;
	}

	public function getAssestsProjects ($user_id = null, $user_type = null) {
		$user_id = (isset($user_id) && !empty($user_id)) ? $user_id : $this->Session->read('Auth.User.id');
		$user_conditions = " AND user_permissions.role IN ('Sharer','Group Sharer') ";
		if( $user_type == 1 ){
			$user_conditions = " AND user_permissions.role NOT IN ('Sharer','Group Sharer') ";
		}
		if( $user_type == 2 ){
			$user_conditions = " ";
		}

		if(isset($user_id) && !empty($user_id)){
		$query = "SELECT projects.id,projects.title
					FROM user_permissions
					inner join projects on user_permissions.project_id = projects.id
					WHERE user_permissions.user_id = '$user_id'
					$user_conditions
					AND	user_permissions.workspace_id IS NULL";

		$projectCount =  ClassRegistry::init('UserPermission')->query($query);
		$projects = array();
		$projectss = array();
		if(isset($projectCount) && !empty($projectCount)){

			foreach($projectCount as $prg){
				  $projects [$prg['projects']['id']] = html_entity_decode($prg['projects']['title'], ENT_QUOTES, "UTF-8");
			}

		}

		natcasesort($projects);
		}else{
			$projects = array();

		}
		return $projects;
	}

	public function feedbackVotesDate($element_id = null){

		if( isset( $element_id ) && !empty($element_id) ){

			$query = "select MIN(feedback.start_date) as minfeedstd, MAX(feedback.end_date) as maxfeedend, MIN(votes.start_date) as minvotestd, MAX(votes.end_date) as maxvoteend from elements

			LEFT JOIN feedback
				ON elements.id = feedback.element_id

			LEFT JOIN votes
				ON elements.id = votes.element_id

			where elements.id = ".$element_id;

			$data = ClassRegistry::init('Element')->query($query);
			return ( isset($data) && !empty($data[0][0]) ) ? $data[0][0] : array();

		}

	}

	public function program_stakeholder_added($program_id, $user_id = null) {

		$programDetails = ClassRegistry::init('Program')->query("
                                SELECT CONCAT_WS(' ',ud.first_name , ud.last_name) AS creator, p.name
                                FROM programs p
                                LEFT JOIN user_details ud ON ud.user_id = p.created_by
                                WHERE p.id = $program_id
		                    ");
		$program_name = $programDetails[0]['p']['name'];
		$creator_name = $programDetails[0][0]['creator'];

		//Send Email and redirect to list page
		$usersDetails = ClassRegistry::init('User')->find('first', array('conditions' => array('User.id' => $user_id)));

		$projectAction = SITEURL . 'projects/lists';

		$notifiragstatus = ClassRegistry::init('EmailNotification')->find('first', ['conditions' => ['notification_type' => 'program', 'personlization' => 'email_program_stackholder', 'user_id' => $user_id]]);

		if ((!isset($notifiragstatus['EmailNotification']['email']) || $notifiragstatus['EmailNotification']['email'] == 1) && (!isset($usersDetails['User']['email_notification']) || $usersDetails['User']['email_notification'] == 1)) {

			$emailAddress = $usersDetails['User']['email'];
			$email = new CakeEmail();
			$email->config('Smtp');
			$email->from(array(ADMIN_FROM_EMAIL => MAIL_SITENAME));
			$email->to($emailAddress);
			// $email->subject( "$name Shared a Project" );
			$email->subject(SITENAME . ": Program stakeholder");
			$email->template('program_stakeholder');
			$email->emailFormat('html');
			$email->viewVars(array('Custname' => $usersDetails['UserDetail']['first_name'] . ' ' . $usersDetails['UserDetail']['last_name'], 'sender' => $creator_name, 'program_name' => $program_name, 'open_page'=>$projectAction));
			$email->send();
			return true;
		}
		return true;

	}

	public function program_stakeholder_removal($program_id, $user_id = null) {

		$programDetails = ClassRegistry::init('Program')->query("
                                SELECT CONCAT_WS(' ',ud.first_name , ud.last_name) AS creator, p.name, p.created_by
                                FROM programs p
                                LEFT JOIN user_details ud ON ud.user_id = p.created_by
                                WHERE p.id = $program_id
		                    ");
		$program_name = $programDetails[0]['p']['name'];
		$creator_id = $programDetails[0]['p']['created_by'];
		$creator_name = $programDetails[0][0]['creator'];

		//Send Email and redirect to list page
		$creatorDetails = ClassRegistry::init('User')->find('first', array('conditions' => array('User.id' => $creator_id)));
		$usersDetails = ClassRegistry::init('User')->find('first', array('conditions' => array('User.id' => $user_id)));

		$projectAction = SITEURL . 'projects/lists';

		$notifiragstatus = ClassRegistry::init('EmailNotification')->find('first', ['conditions' => ['notification_type' => 'program', 'personlization' => 'stackholder_removal', 'user_id' => $creator_id]]);

		if ((!isset($notifiragstatus['EmailNotification']['email']) || $notifiragstatus['EmailNotification']['email'] == 1) && (!isset($creatorDetails['User']['email_notification']) || $creatorDetails['User']['email_notification'] == 1)) {

			$emailAddress = $creatorDetails['User']['email'];
			$email = new CakeEmail();
			$email->config('Smtp');
			$email->from(array(ADMIN_FROM_EMAIL => MAIL_SITENAME));
			$email->to($emailAddress);
			// $email->subject( "$name Shared a Project" );
			$email->subject(SITENAME . ": Stakeholder removal");
			$email->template('program_stakeholder_removal');
			$email->emailFormat('html');
			$email->viewVars(array('Custname' => $creator_name, 'stakeholder' => $usersDetails['UserDetail']['first_name'] . ' ' . $usersDetails['UserDetail']['last_name'], 'program_name' => $program_name, 'open_page'=>$projectAction));
			$email->send();
			return true;
		}
		return true;

	}

}
