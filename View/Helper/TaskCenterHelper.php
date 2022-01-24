<?php

/**
 * Application level View Helper
 *
 * This file is application-wide helper file. You can put all
 * application-wide helper-related methods here.
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
 * @package       app.View.Helper
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
App::uses('Helper', 'View');

App::import("Model", "UserProject");
App::import("Model", "ProjectWorkspace");
App::import("Model", "Project");
App::import("Model", "Workspace");
App::import("Model", "Availability");
App::import("Model", "Area");
App::import("Model", "Element");
App::import("Model", "ElementDecision");

App::import("Model", "ProjectPermission");
App::import("Model", "WorkspacePermission");
App::import("Model", "ElementPermission");

/**
 * Application helper
 *
 * Add your application-wide methods in the class below, your helpers
 * will inherit them.
 *
 * @package app.View.Helper
 */
class TaskCenterHelper extends AppHelper {
	var $helpers = array(
		'Html',
		'Session',
		'Thumbnail',
		'Common',
		"Group",
	);

	protected $_user_projects;
	protected $_project_workspaces;
	protected $_projects;
	protected $_workspaces;
	protected $_availabilities;
	protected $_areas;
	protected $_elements;
	protected $_element_decisions;

	protected $_pr_permit;
	protected $_ws_permit;
	protected $_el_permit;

	public function __construct(View $View, $settings = array()) {

		parent::__construct($View, $settings);

		$this->_user_projects = new UserProject();
		$this->_project_workspaces = new ProjectWorkspace();
		$this->_projects = new Project();
		$this->_workspaces = new Workspace();
		$this->_availabilities = new Availability();
		$this->_areas = new Area();
		$this->_elements = new Element();
		$this->_element_decisions = new ElementDecision();

		$this->_pr_permit = new ProjectPermission();
		$this->_ws_permit = new WorkspacePermission();
		$this->_el_permit = new ElementPermission();

	}

	public function _displayDate_new($date = null, $format = 'd M, Y g:i A') {
		// date must be pass in formate of 'Y-m-d h:i:s A'....
		$timezone = ClassRegistry::init('Timezone')->findByUserId($this->Session->read("Auth.User.id"));
		
		if((!isset($timezone['Timezone']['name']) || empty($timezone['Timezone']['name'])) || ($timezone['Timezone']['name'] ==  'Etc/Unknown')){
			$timezone['Timezone']['name'] = 'Europe/London';
		}
		
		$target_time_zone = new DateTimeZone($timezone['Timezone']['name']);
		$kolkata_date_time = new DateTime('now', $target_time_zone);
		$time = $kolkata_date_time->format('P');
		$exp = explode(':', $time);
		$minutes = (substr($exp[0], 1) * 60) + $exp[1];
		$sign = substr($exp[0], 0, 1);
		$addsignandminutes = $sign . $minutes;

		$rfc_1123_date = date('Y-m-d h:i:s A', strtotime($date));
		$sertimestamp = strtotime($rfc_1123_date . " $addsignandminutes minute");
		$date = date($format, $sertimestamp);

		date_default_timezone_set('UTC');

		if (empty($date)) {
			return;
		}

		return $date;
	}

	public function userByProject($project_ids = null, $fieldfrom = null) {
		$all_total = $all_total_project = $all_userby_type = [];
		$owner_arr = $participants_arr = $participantsGpOwner_arr = $participantsGpSharer_arr = $participants_owners_arr = [];
		if (!is_array($project_ids)) {
			$project_ids = [$project_ids];
		}

		$allOwnerData = $allSharerData = [];
		foreach ($project_ids as $project_id) {
			$owner = $this->Common->ProjectOwner($project_id, $this->Session->read('Auth.User.id'), $fieldfrom);
			$participants = participants($project_id, isset($owner['UserProject']['user_id']) ? $owner['UserProject']['user_id'] : null);
			$participants_owners = participants_owners($project_id, isset($owner['UserProject']['user_id']) ? $owner['UserProject']['user_id'] : null);

			$participantsGpOwner = participants_group_owner($project_id);
			$participantsGpSharer = participants_group_sharer($project_id);

			$participants = isset($participants) ? array_filter($participants) : $participants;
			$participants_owners = isset($participants_owners) ? array_filter($participants_owners) : $participants_owners;
			$participantsGpOwner = isset($participantsGpOwner) ? array_filter($participantsGpOwner) : $participantsGpOwner;
			$participantsGpSharer = isset($participantsGpSharer) ? array_filter($participantsGpSharer) : $participantsGpSharer;

			$owneruser = isset($owner['User']['id']) && !empty($owner['User']['id']) ? $owner['User']['id'] : 'N/A';
			$owner_arr[] = $owneruser;
			$all_total[] = $owneruser;
			$allSharerData = array();
			$all_total_project[$project_id][] = $owneruser;
			$allOwnerData[] = $owneruser;
			$all_userby_type[$project_id]['owner'][] = $owneruser;
			if (isset($participants) && !empty($participants)) {
				foreach ($participants as $v) {
					$participants_arr[] = $v;
					$all_total[] = $v;
					$all_total_project[$project_id][] = $v;
					$allSharerData[] = $v;
					$all_userby_type[$project_id]['participants'][] = $v;
				}
			}
			if (isset($participants_owners) && !empty($participants_owners)) {
				foreach ($participants_owners as $v) {
					$participants_owners_arr[] = $v;
					$all_total[] = $v;
					$all_total_project[$project_id][] = $v;
					$allOwnerData[] = $v;

					$all_userby_type[$project_id]['participants_owners'][] = $v;
				}
			}
			if (isset($participantsGpOwner) && !empty($participantsGpOwner)) {
				foreach ($participantsGpOwner as $v) {
					$participantsGpOwner_arr[] = $v;
					$all_total[] = $v;
					$all_total_project[$project_id][] = $v;
					$allOwnerData[] = $v;
					$all_userby_type[$project_id]['participantsGpOwner'][] = $v;
				}
			}
			if (isset($participantsGpSharer) && !empty($participantsGpSharer)) {
				foreach ($participantsGpSharer as $v) {
					$participantsGpSharer_arr[] = $v;
					$all_total[] = $v;
					$all_total_project[$project_id][] = $v;
					$allSharerData[] = $v;

					$all_userby_type[$project_id]['participantsGpSharer'][] = $v;
				}
			}

		}

		if (!empty($fieldfrom) && $fieldfrom == 'taskcenter') {
			$users = ["all_project_user" => $all_total, "project_by_user" => $all_total_project];
			return $users;
		} else {
			$users = ["all_userby_type" => $all_userby_type, "all_project_user" => $all_total, "project_by_user" => $all_total_project, 'allOwner' => $allOwnerData, 'allSharer' => $allSharerData];
			return $users;
		}

	}

	// Get all elements of a user
	// $userID = <string>
	// $project_id = <array>
	public function userElementsDemo($userID = null, $project_id = null) {

		if (isset($userID) && !empty($userID)) {
			$view = new View();
			$view_model = $view->loadHelper('ViewModel');
			$common = $view->loadHelper('Common');
			$group = $view->loadHelper('Group');

			$all = [];

			if (isset($project_id) && !empty($project_id)) {
				//
				foreach ($project_id as $key => $value) {

					$pid = $value;
					$p_permission = $this->project_pp($pid, $userID);
					$owner_user = $this->owner_user($pid, $userID);

					$gp_exists = $group->GroupIDbyUserID($pid, $userID);
					if (isset($gp_exists) && !empty($gp_exists)) {
						$p_permission = $this->group_pp($pid, $gp_exists);
					}

					$e_permission = [];

					if (($owner_user) || ($p_permission)) {
						$all_elements = $view_model->projectElementsTaskCenter($pid);
						if (isset($all_elements) && !empty($all_elements)) {
							$e_permission = Set::extract($all_elements, '{n}/element/id');
						}

					} else {
						$e_permission = $this->element_pp($pid, $userID);

						if ((isset($gp_exists) && !empty($gp_exists))) {

							if (isset($e_permission) && !empty($e_permission)) {
								$e_permissions = $group->group_element_permission_data($pid, $gp_exists);
								$e_permission = array_merge($e_permission, $e_permissions);
							} else {
								$e_permission = $group->group_element_permission_data($pid, $gp_exists);
							}
						}
					}
					$all = array_merge($all, $e_permission);

				}
			}

			return array_unique($all);
		}
	}

	public function owner_user($pid, $uid) {
		$data = ClassRegistry::init('UserProject')->find('first', array('conditions' => array('UserProject.user_id' => $uid, 'UserProject.project_id' => $pid, 'UserProject.owner_user' => 1)));

		return (isset($data) && !empty($data)) ? true : false;
	}

	public function project_pp($pid, $uid) {

		$data = $this->_pr_permit->find('first', array('conditions' => array('ProjectPermission.user_id' => $uid, 'ProjectPermission.user_project_id' => project_upid($pid)), 'recursive' => -1, 'fields' => ['ProjectPermission.project_level']));

		$flag = false;
		if (isset($data) && !empty($data)) {
			$flag = (isset($data['ProjectPermission']['project_level']) && !empty($data['ProjectPermission']['project_level'])) ? true : false;
		}

		return $flag;
	}

	public function group_pp($pid, $gid) {

		$data = $this->_pr_permit->find('first', array('conditions' => array('ProjectPermission.project_group_id' => $gid, 'ProjectPermission.user_project_id' => project_upid($pid)), 'recursive' => -1, 'fields' => ['ProjectPermission.project_level']));

		$flag = false;
		if (isset($data) && !empty($data)) {
			$flag = (isset($data['ProjectPermission']['project_level']) && !empty($data['ProjectPermission']['project_level'])) ? true : false;
		}

		return $flag;
	}

	public function element_pp($pid, $uid) {

		$datas = $this->_el_permit->find('all', array('conditions' => array('ElementPermission.user_id' => $uid, 'ElementPermission.project_id' => $pid, 'ElementPermission.user_id IS NOT NULL', 'Project.id !=' => '', 'Element.id !=' => '', 'Workspace.id !=' => '')));

		if (isset($datas) && !empty($datas)) {

			foreach ($datas as $dat) {
				$data[] = $dat['ElementPermission']['element_id'];
			}
		}

		return isset($data) ? $data : array();
	}

	// Get all elements of a user
	// $userID = <string>
	// $project_id = <array>
	public function userElements($userID = null, $project_id = null) {

		if (isset($userID) && !empty($userID)) {

			$view = new View();
			$view_model = $view->loadHelper('ViewModel');
			$common = $view->loadHelper('Common');
			$group = $view->loadHelper('Group');

			$all = [];

			if (isset($project_id) && !empty($project_id)) {
				//
				foreach ($project_id as $key => $value) {

					$pid = $value;
					$p_permission = $common->project_permission_details($pid, $userID);
					$user_project = $common->userproject($pid, $userID);
					$gp_exists = $group->GroupIDbyUserID($pid, $userID);
					if (isset($gp_exists) && !empty($gp_exists)) {
						$p_permission = $group->group_permission_details($pid, $gp_exists);
					}

					$e_permission = [];

					if (((isset($user_project)) && (!empty($user_project))) || (isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level'] == 1)) {
						$all_elements = $view_model->projectElementsTaskCenter($pid);

						if (isset($all_elements) && !empty($all_elements)) {
							$e_permission = Set::extract($all_elements, '{n}/element/id');
						}

					} else {
						$e_permission = $common->element_permission_data($pid, $userID);

						if ((isset($gp_exists) && !empty($gp_exists))) {

							if (isset($e_permission) && !empty($e_permission)) {
								$e_permissions = $group->group_element_permission_data($pid, $gp_exists);
								$e_permission = array_merge($e_permission, $e_permissions);
							} else {
								$e_permission = $group->group_element_permission_data($pid, $gp_exists);
							}
						}
					}
					$all = array_merge($all, $e_permission);

				}
			}
			return array_unique($all);
		}
	}

	// Get all elements of supplied users
	// $userID = <array>
	// $project_id = <array>
	public function usersElements($userID = null, $project_id = null) {

		if (isset($userID) && !empty($userID)) {
			// mpr($userID, $project_id);

			App::import("Model", "ProjectPermission");
			$pp = new ProjectPermission();

			App::import("Model", "ElementPermission");
			$ep = new ElementPermission();

			$view = new View();
			$view_model = $view->loadHelper('ViewModel');
			$common = $view->loadHelper('Common');
			$group = $view->loadHelper('Group');

			$all = [];

			if ((isset($userID) && !empty($userID)) && (isset($project_id) && !empty($project_id))) {

				foreach ($userID as $ukey => $uvalue) {

					foreach ($project_id as $key => $value) {

						$pid = $value;
						$p_permission = $common->project_permission_details($pid, $uvalue);

						$user_project = $common->userproject($pid, $uvalue);
						$gp_exists = $group->GroupIDbyUserID($pid, $uvalue);
						if (isset($gp_exists) && !empty($gp_exists)) {
							$p_permission = $group->group_permission_details($pid, $gp_exists);
						}

						$e_permission = [];

						if (((isset($user_project)) && (!empty($user_project))) || (isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level'] == 1)) {
							$all_elements = $view_model->projectElementsTaskCenter($pid);
							if (isset($all_elements) && !empty($all_elements)) {
								$e_permission = Set::extract($all_elements, '{n}/element/id');
							}
						} else {

							$e_permission = $common->element_permission_data($pid, $uvalue);

							if ((isset($gp_exists) && !empty($gp_exists))) {

								if (isset($e_permission) && !empty($e_permission)) {
									$e_permissions = $group->group_element_permission_data($pid, $gp_exists);
									$e_permission = array_merge($e_permission, $e_permissions);
								} else {
									$e_permission = $group->group_element_permission_data($pid, $gp_exists);
								}
							}
						}

						$all = array_merge($all, $e_permission);

						//
					} // END PROJECTS

				} // END USER
			}
			return array_unique($all);
		}
	}

	public function usersWorkspaces($userID = null, $project_id = null) {

		if (isset($userID) && !empty($userID)) {
			// mpr($userID, $project_id);

			App::import("Model", "ProjectPermission");
			$pp = new ProjectPermission();

			App::import("Model", "ElementPermission");
			$ep = new ElementPermission();

			$view = new View();
			$view_model = $view->loadHelper('ViewModel');
			$common = $view->loadHelper('Common');
			$group = $view->loadHelper('Group');

			$all = [];

			if ((isset($userID) && !empty($userID)) && (isset($project_id) && !empty($project_id))) {

				foreach ($userID as $ukey => $uvalue) {

					foreach ($project_id as $key => $value) {

						$pid = $value;
						$p_permission = $common->project_permission_details($pid, $uvalue);

						$user_project = $common->userproject($pid, $uvalue);
						$gp_exists = $group->GroupIDbyUserID($pid, $uvalue);
						if (isset($gp_exists) && !empty($gp_exists)) {
							$p_permission = $group->group_permission_details($pid, $gp_exists);
						}

						$wsp_permission = [];

						if (((isset($user_project)) && (!empty($user_project))) || (isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level'] == 1)) {
							$all_wsp = $view_model->get_project_workspace($pid);
							if (isset($all_wsp) && !empty($all_wsp)) {
								$wsp_permission = Set::extract($all_wsp, '{n}/Workspace/id');
							}
						} else {

							$wsp_permission = $common->work_permission_details($pid, $uvalue);

							if ((isset($gp_exists) && !empty($gp_exists))) {

								if (isset($wsp_permission) && !empty($wsp_permission)) {
									$e_permissions = $group->group_work_permission_details($pid, $gp_exists);
									$wsp_permission = array_merge($wsp_permission, $e_permissions);
								} else {
									$wsp_permission = $group->group_work_permission_details($pid, $gp_exists);
								}
							}

							$wpids = null;
							if (isset($wsp_permission) && !empty($wsp_permission)) {
								// foreach ($wsp_permission as $wkey => $wvalue) {
								$wpids = pwid_workspace($wsp_permission, $pid);
								// }
								// pr($wpids);
								$wsp_permission = $wpids;
							}

						}

						$all = array_merge($all, $wsp_permission);

						//
					} // END PROJECTS

				} // END USER
			}

			return array_unique($all);
		}
	}

	public function element_permit_type($pid = null, $user_id = null, $eid = null) {
		$data = false;

		$view = new View();
		$view_model = $view->loadHelper('ViewModel');
		$common = $view->loadHelper('Common');
		$group = $view->loadHelper('Group');

		App::import("Model", "ProjectPermission");
		$pp = new ProjectPermission();

		$p_permission = $common->project_permission_details($pid, $user_id);

		$user_project = $common->userproject($pid, $user_id);
		$gp_exists = $group->GroupIDbyUserID($pid, $user_id);
		if (isset($gp_exists) && !empty($gp_exists)) {
			$p_permission = $group->group_permission_details($pid, $gp_exists);
		}

		$e_permission = [];

		if (((isset($user_project)) && (!empty($user_project))) || (isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level'] == 1)) {
			$data = true;
		} else {
			$e_permission = $common->element_share_permission($eid, $pid, $user_id);
			if (isset($gp_exists) && !empty($gp_exists)) {
				$e_permission = $group->group_element_share_permission($eid, $pid, $gp_exists);
			}
			if (isset($e_permission) && !empty($e_permission)) {
				$edata = $e_permission['ElementPermission'];
				if ((isset($edata['is_editable']) && !empty($edata['is_editable']))) {
					$data = true;
				}
			}
		}

		return $data;
	}

	public function total_signoff($user_id, $project_id) {

		$eles = $this->userElements($user_id, $project_id);
		if (isset($eles) && !empty($eles)) {
			$ele = ClassRegistry::init('Element')->find('count', array('conditions' => array('Element.id' => $eles, 'Element.sign_off' => 1)));
			return (isset($ele) && !empty($ele)) ? $ele : 0;
		} else {
			return 0;
		}
	}

	public function workspace_permit_type($pid = null, $user_id = null, $wid = null) {
		$data = false;

		$view = new View();
		$view_model = $view->loadHelper('ViewModel');
		$common = $view->loadHelper('Common');
		$group = $view->loadHelper('Group');

		App::import("Model", "ProjectPermission");
		$pp = new ProjectPermission();

		$p_permission = $common->project_permission_details($pid, $user_id);

		$user_project = $common->userproject($pid, $user_id);
		$gp_exists = $group->GroupIDbyUserID($pid, $user_id);
		if (isset($gp_exists) && !empty($gp_exists)) {
			$p_permission = $group->group_permission_details($pid, $gp_exists);
		}

		$wsp_permission = [];

		if (((isset($user_project)) && (!empty($user_project))) || (isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level'] == 1)) {
			$data = true;
		} else {
			$wsp_permission = $common->workspace_permit(workspace_pwid($pid, $wid), $pid, $user_id);
			if (isset($gp_exists) && !empty($gp_exists)) {
				$wsp_permission = $group->group_workspace_permit(workspace_pwid($pid, $wid), $pid, $gp_exists);
			}
			if (isset($wsp_permission) && !empty($wsp_permission)) {

				$wsp_data = $wsp_permission['WorkspacePermission'];
				if ((isset($wsp_data['is_editable']) && !empty($wsp_data['is_editable']))) {
					$data = true;
				}
			}
		}

		return $data;
	}

	public function project_permit_type($pid = null, $user_id = null) {
		$data = false;

		$view = new View();
		$view_model = $view->loadHelper('ViewModel');
		$common = $view->loadHelper('Common');
		$group = $view->loadHelper('Group');

		App::import("Model", "ProjectPermission");
		$pp = new ProjectPermission();

		$p_permission = $common->project_permission_details($pid, $user_id);
		$project_permission = [];

		$user_project = $common->userproject($pid, $user_id);
		$gp_exists = $group->GroupIDbyUserID($pid, $user_id);
		if (isset($gp_exists) && !empty($gp_exists)) {
			$p_permission = $group->group_permission_details($pid, $gp_exists);
		}

		if (((isset($user_project)) && (!empty($user_project))) || (isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level'] == 1)) {
			$data = true;
		}

		return $data;
	}

	public function user_exists($users = null) {
		App::import("Model", "User");
		$u = new User();
		App::import("Model", "UserDetail");
		$ud = new UserDetail();

		$data = null;
		if (isset($users) && !empty($users)) {
			foreach ($users as $key => $value) {
				if ($ud->hasAny(['user_id' => $value])) {
					$data[$key] = $value;
				}
			}
		}
		return $data;
	}

	// current user projects
	function user_projects($current_user_id = null) {

		$current_user_id = (isset($current_user_id) && !empty($current_user_id)) ? $current_user_id : $this->Session->read('Auth.User.id');

		$all_projects = $filter_projects = null;
		$myprojectlist = $myreceivedprojectlist = $mygroupprojectlist = array();

		// Find All current user's projects
		$myprojectlist = myprojects($current_user_id);
		$filter_projects['my'] = (isset($myprojectlist) && !empty($myprojectlist)) ? array_filter(array_keys($myprojectlist)) : [];

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
			$filter_projects['others'] = array_filter(array_unique($filter_projects['others']));
		}

		return $filter_projects;
	}

	public function userElementsPaging($userID = null, $pid = null, $current_page = 0, $limit = 3) {

		if (isset($userID) && !empty($userID)) {
			$view = new View();
			$view_model = $view->loadHelper('ViewModel');
			$common = $view->loadHelper('Common');
			$group = $view->loadHelper('Group');

			$all = [];

			if (isset($pid) && !empty($pid)) {

				$p_permission = $this->project_pp($pid, $userID);
				$owner_user = $this->owner_user($pid, $userID);

				$gp_exists = $group->GroupIDbyUserID($pid, $userID);
				if (isset($gp_exists) && !empty($gp_exists)) {
					$p_permission = $this->group_pp($pid, $gp_exists);
				}

				$e_permission = [];

				if (($owner_user) || ($p_permission)) {
					$all_elements = $this->projectElementsPaging($pid, $current_page, $limit);
					if (isset($all_elements) && !empty($all_elements)) {
						$e_permission = Set::extract($all_elements, '{n}/element/id');
					}

				} else {
					$e_permission = $this->permission_element_paging($pid, $userID, $current_page, $limit);

					if ((isset($gp_exists) && !empty($gp_exists))) {

						if (isset($e_permission) && !empty($e_permission)) {
							$e_permissions = $this->group_element_permission_paging($pid, $gp_exists, $current_page, $limit);
							$e_permission = array_merge($e_permission, $e_permissions);
						} else {
							$e_permission = $this->group_element_permission_paging($pid, $gp_exists, $current_page, $limit);
						}
					}
				}
				$all = array_merge($all, $e_permission);

			}

			return array_unique($all);
		}
	}

	public function projectElementsPaging($project_id = null, $current_page = 0, $limit = 3) {

		$data = null;
		$view = new View();
		$view_model = $view->loadHelper('ViewModel');

		if (!isset($project_id) || empty($project_id)) {
			return $data;
		}

		$workspaces = $elements = $area_ids = null;

		$workspaces = $view_model->get_project_workspace_task_center($project_id);

		if (isset($workspaces) && !empty($workspaces)) {

			foreach ($workspaces as $key => $value) {

				$workspace = $value['Workspace'];

				$areas = $view_model->workspace_areas($workspace['id'], false, true);

				if (isset($areas) && !empty($areas)) {
					if (is_array($area_ids)) {
						$area_ids = array_merge($area_ids, array_values($areas));
					} else {
						$area_ids = array_values($areas);
					}

				}

			}
		}

		if (isset($area_ids) && !empty($area_ids)) {

			$query = '';

			$query .= 'SELECT element.id ';
			$query .= 'FROM elements as element ';
			$query .= "WHERE element.area_id IN (" . implode(',', $area_ids) . ") ";
			$query .= "AND element.studio_status != 1 ";
			$query .= "LIMIT $current_page, $limit";

			$data = $this->_elements->query($query);
		}

		return $data;
	}

	public function permission_element_paging($pid, $uid, $current_page = 0, $limit = 3) {

		$query = '';

		$query .= 'SELECT ElementPermission.element_id ';
		$query .= 'FROM element_permissions as ElementPermission ';
		$query .= "WHERE ElementPermission.project_id = '$pid' ";
		$query .= "AND ElementPermission.user_id = '$uid' ";
		$query .= "AND ElementPermission.user_id IS NOT NULL ";
		$query .= "LIMIT $current_page, $limit";

		$datas = $this->_el_permit->query($query);

		/*$datas = $this->_el_permit->find('all', array('conditions' => array('ElementPermission.user_id' => $uid, 'ElementPermission.project_id' => $pid, 'ElementPermission.user_id IS NOT NULL', 'Project.id !=' => '', 'Element.id !=' => '', 'Workspace.id !=' => '')));*/

		if (isset($datas) && !empty($datas)) {

			foreach ($datas as $dat) {
				$data[] = $dat['ElementPermission']['element_id'];
			}
		}

		return isset($data) ? $data : array();
	}

	public function group_element_permission_paging($pid, $gid) {

		$query = '';

		$query .= 'SELECT ElementPermission.element_id ';
		$query .= 'FROM element_permissions as ElementPermission ';
		$query .= "WHERE ElementPermission.project_id = '$pid' ";
		$query .= "AND ElementPermission.project_group_id = '$gid' ";
		$query .= "LIMIT $current_page, $limit";

		$datas = $this->_el_permit->query($query);

		// $datas = ClassRegistry::init('ElementPermission')->find('all', array('conditions' => array('ElementPermission.project_group_id' => $gid, 'ElementPermission.project_id' => $pid, 'Element.id !=' => '')));

		if (isset($datas) && !empty($datas)) {

			foreach ($datas as $dat) {
				$data[] = $dat['ElementPermission']['element_id'];
			}
		}

		return isset($data) ? $data : array();
	}

}
