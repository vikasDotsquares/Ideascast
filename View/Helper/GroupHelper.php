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
App::import("Model", "DoList");
App::import("Model", "DoListUser");
App::import("Model", "DoListComment");
App::import("Model", "DoListUpload");
App::import("Model", "DoListCommentUpload");
App::import("Model", "DoListCommentLike");

/**
 * Application helper
 *
 * Add your application-wide methods in the class below, your helpers
 * will inherit them.
 *
 * @package       app.View.Helper
 */
class GroupHelper extends Helper {

	var $helpers = array('Html', 'Session', 'Thumbnail');
	protected $_do_list;
	protected $_do_list_user;
	protected $_do_comment;
	protected $_do_list_upload;
	protected $_do_comment_upload;
	protected $_do_comment_like;

	public function __construct(View $View, $settings = array()) {

		parent::__construct($View, $settings);

		$this->_do_list = new DoList();
		$this->_do_list_user = new DoListUser();
		$this->_do_comment = new DoListComment();
		$this->_do_list_upload = new DoListUpload();
		$this->_do_comment_upload = new DoListCommentUpload();
		$this->_do_comment_like = new DoListCommentLike();
	}

	public function group_wsp_permission_edit($id, $pid, $gid) {

		$view = new View();
		$common = $view->loadHelper('Common');

		//echo $id."<br>".$common->get_up_id($pid,$uid)."<br>".$uid;
		//echo  $common->get_up_id($pid,$uid)."<br>".$pid ;

		$data = ClassRegistry::init('WorkspacePermission')->find('first', array('conditions' => array('WorkspacePermission.project_group_id' => $gid, 'WorkspacePermission.project_workspace_id' => $id, 'WorkspacePermission.user_project_id' => project_upid($pid))));

		return isset($data['WorkspacePermission']['permit_edit']) ? $data['WorkspacePermission']['permit_edit'] : 0;
	}

	public function group_wsp_permission_delete($id, $pid, $gid) {

		$view = new View();
		$common = $view->loadHelper('Common');

		$data = ClassRegistry::init('WorkspacePermission')->find('first', array('conditions' => array('WorkspacePermission.project_workspace_id' => $id, 'WorkspacePermission.project_group_id' => $gid, 'WorkspacePermission.user_project_id' => project_upid($pid))));
		return isset($data['WorkspacePermission']['permit_delete']) ? $data['WorkspacePermission']['permit_delete'] : 0;
	}

	public function group_wsp_permission_details($id, $pid, $gid) {

		$view = new View();
		$common = $view->loadHelper('Common');

		$data = ClassRegistry::init('WorkspacePermission')->find('all', array('conditions' => array('WorkspacePermission.project_workspace_id' => $id, 'WorkspacePermission.project_group_id' => $gid, 'WorkspacePermission.user_project_id' => project_upid($pid))));

		return isset($data) ? $data : array();
	}

	public function group_workspace_permit($id, $pid, $gid) {

		$view = new View();
		$common = $view->loadHelper('Common');

		$data = ClassRegistry::init('WorkspacePermission')->find('first', array('conditions' => array('WorkspacePermission.project_workspace_id' => $id, 'WorkspacePermission.project_group_id' => $gid, 'WorkspacePermission.user_project_id' => project_upid($pid))));

		return isset($data) ? $data : array();
	}

	public function group_permission_details($pid, $gid) {

		$view = new View();
		$common = $view->loadHelper('Common');

		$data = ClassRegistry::init('ProjectPermission')->find('first', array('conditions' => array('ProjectPermission.project_group_id' => $gid, 'ProjectPermission.user_project_id' => project_upid($pid))));

		return isset($data) ? $data : array();
	}

	public function ProjectGroupDetail($gid = null, $pid = null, $uid = null) {
		$data = null;
		if (isset($gid) && !empty($gid)) {

			$data = ClassRegistry::init('ProjectGroup')->find('first', array('conditions' => array('ProjectGroup.id' => $gid)));
		} else {
			$data = ClassRegistry::init('ProjectGroup')->find('first', array('conditions' => array('ProjectGroup.group_owner_id' => $uid, 'ProjectGroup.user_project_id' => $pid)));
		}
		return isset($data) ? $data : array();
	}

	/* 	public function groupprojectOwner( $gid ,$pid){
		      $data = ClassRegistry::init('ProjectGroup')->find('first', array('conditions'=>array('ProjectGroup.user_project_id'=>$pid )));

		      return isset($data['ProjectGroup']['group_owner_id']) ? $data['ProjectGroup']['group_owner_id']  :  "N/A";
	*/

	public function group_element_share_permission($element_id, $pid, $gid) {

		$data = ClassRegistry::init('ElementPermission')->find('first', array('conditions' => array('ElementPermission.project_group_id' => $gid, 'ElementPermission.project_id' => $pid, 'ElementPermission.element_id' => $element_id)));

		return isset($data) ? $data : array();
	}

	public function group_work_permission_details($pid, $gid) {

		$view = new View();
		$common = $view->loadHelper('Common');
		ClassRegistry::init('WorkspacePermission')->recursive = 2;
		$datas = ClassRegistry::init('WorkspacePermission')->find('all', array('conditions' => array('WorkspacePermission.project_group_id' => $gid, 'WorkspacePermission.user_project_id' => project_upid($pid), 'ProjectWorkspace.id !=' => '')));

		if (isset($datas) && !empty($datas)) {

			foreach ($datas as $dat) {
				$data[] = $dat['WorkspacePermission']['project_workspace_id'];
			}
		}

		return isset($data) ? $data : array();
	}

	public function group_element_permission_details($wid, $pid, $gid) {

		$datas = ClassRegistry::init('ElementPermission')->find('all', array('conditions' => array('ElementPermission.project_group_id' => $gid, 'ElementPermission.project_id' => $pid, 'ElementPermission.workspace_id' => $wid)));

		if (isset($datas) && !empty($datas)) {

			foreach ($datas as $dat) {
				$data[] = $dat['ElementPermission']['element_id'];
			}
		}

		return isset($data) ? $data : array();
	}
	public function group_element_permission_data($pid, $gid) {

		$datas = ClassRegistry::init('ElementPermission')->find('all', array('conditions' => array('ElementPermission.project_group_id' => $gid, 'ElementPermission.project_id' => $pid, 'Element.id !=' => '')));

		if (isset($datas) && !empty($datas)) {

			foreach ($datas as $dat) {
				$data[] = $dat['ElementPermission']['element_id'];
			}
		}

		return isset($data) ? $data : array();
	}

	public function group_users($gid, $accepted = false) {

		$conditions['ProjectGroupUser.project_group_id'] = $gid;
		if ($accepted) {
			$conditions['ProjectGroupUser.approved'] = 1;
		}

		$datas = ClassRegistry::init('ProjectGroupUser')->find('all', array('conditions' => $conditions));

		if (isset($datas) && !empty($datas)) {

			foreach ($datas as $dat) {
				$data[] = $dat['ProjectGroupUser']['user_id'];
			}
		}

		return isset($data) ? $data : array();
	}

	public function my_received_groups($user_id = null, $group_data = true) {

		$conditions['ProjectGroupUser.approved'] = 1;
		if (isset($user_id) && !empty($user_id)) {
			$conditions['ProjectGroupUser.user_id'] = $user_id;
		} else {
			$conditions['ProjectGroupUser.user_id'] = $this->Session->read("Auth.User.id");
		}

		$datas = ClassRegistry::init('ProjectGroupUser')->find('all', array('conditions' => $conditions));

		if ($group_data == true) {

			if (isset($datas) && !empty($datas)) {
				$group_ids = Set::extract($datas, '/ProjectGroupUser/project_group_id');
				$data = ClassRegistry::init('ProjectGroup')->find('all', array('conditions' => ['ProjectGroup.id' => $group_ids], 'recursive' => -1));
			}
		} else {
			if (isset($datas) && !empty($datas)) {

				foreach ($datas as $dat) {
					$data[] = $dat['ProjectGroupUser']['project_group_id'];
				}
			}
		}

		return isset($data) ? $data : array();
	}

	public function GroupIDbyUserID($pid, $uid) {

		ClassRegistry::init('ProjectGroupUser')->unbindAll();
		$datas = ClassRegistry::init('ProjectGroupUser')->find('first', array('conditions' => array('ProjectGroupUser.user_id' => $uid, 'ProjectGroupUser.user_project_id' => project_upid($pid), 'ProjectGroupUser.approved' => 1)));

		if (isset($datas) && !empty($datas)) {

			$data = $datas['ProjectGroupUser']['project_group_id'];
		}

		return isset($data) ? $data : array();
	}

	public function wsp_users($upid = null, $wsid = null) {

		ClassRegistry::init('WorkspacePermission')->unbindModel(['belongsTo' => ['UserProject', 'ProjectWorkspace']]);

		$datas = ClassRegistry::init('WorkspacePermission')->find('all', array('conditions' => array('WorkspacePermission.project_workspace_id' => $wsid, 'WorkspacePermission.user_project_id' => project_upid($upid), 'WorkspacePermission.user_id IS NOT NULL'), 'group' => 'WorkspacePermission.user_id'));

		$data = null;

		if (isset($datas) && !empty($datas)) {
			$data = $datas;
		}
		// e(ClassRegistry::init('WorkspacePermission')->_query());
		return isset($data) ? $data : array();
	}

	public function dolist_detail($dolist_id = null) {

		if (!isset($dolist_id) || empty($dolist_id)) {
			return false;
		}

		$data = $this->_do_list->find("first", array(
			"conditions" => ['DoList.id' => $dolist_id],
			"recursive" => -1,
		)
		);

		return isset($data) ? $data : false;
	}

	public function do_lists($project_id = null, $day = null, $sdate = null, $edate = null, $count = false) {

		$user_id = $this->Session->read("Auth.User.id");

		// $this->_do_list->bindModel(array("belongsTo" => array("Project")));
		$this->_do_list->unbindModel(array("hasMany" => array("Children"), "belongsTo" => array("Parent")));

		$conditions = $cond = $contain = null;

		if (isset($project_id) && !empty($project_id)) {
			$conditions['AND'][] = array('DoList.project_id' => $project_id);
		} else {
			$conditions['AND'][] = array('DoList.project_id IS NULL');
		}
		if (isset($sdate) && isset($edate) && !empty($sdate) && !empty($edate)) {
			$start_date = $this->params['named']['sdate'];
			$end_date = $this->params['named']['edate'];
			$conditions['AND'][] = array('DoList.start_date >=' => $start_date);
			$conditions['AND'][] = array('DoList.end_date <=' => $end_date);
		}

		if (isset($day) && !empty($day)) {
			if ($day == 'all') {
				// $conditions['AND'][] = array('DoList.sign_off' => [0,1]);
			} else if ($day == 'today') {
				$conditions['AND'][] = array('DoList.start_date <=' => date('Y-m-d'));
				$conditions['AND'][] = array('DoList.end_date >=' => date('Y-m-d'));
				$conditions['AND'][] = array('DoList.sign_off !=' => 1);
			} /*else if ($day == 'tomorrow') {
				$conditions['AND'][] = array('DoList.start_date' => date('Y-m-d', strtotime("+1 day")));
				$conditions['AND'][] = array('DoList.sign_off !=' => 1);
			}*/ else if ($day == 'notstarted') {
				$conditions['AND'][] = array('DoList.start_date >' => date('Y-m-d'));
				$conditions['AND'][] = array('DoList.sign_off !=' => 1);
			} else if ($day == 'notset') {
				$conditions['AND'][] = array('DoList.start_date IS NULL');
				$conditions['AND'][] = array('DoList.end_date IS NULL');
				$conditions['AND'][] = array('DoList.sign_off !=' => 1);
			} else if ($day == 'completed') {
				$conditions['AND'][] = array('DoList.sign_off' => 1);
			} else if ($day == 'overdue') {
				$conditions['AND'][] = array('DoList.end_date <' => date('Y-m-d'));
				$conditions['AND'][] = array('DoList.sign_off !=' => 1);
			}
		}

		$conditions['AND'][] = [
			'DoList.user_id' => $user_id,
			'DoList.is_archive' => 0,
		];

		$do_lists = $this->_do_list->find("all", array(
			"conditions" => $conditions,
			"recursive" => -1,
		)
		);

		if($count){
			return (isset($do_lists) && !empty($do_lists)) ? count($do_lists) : 0;
		}

		$dolist = null;
		if (isset($do_lists) && !empty($do_lists)) {
			foreach ($do_lists as $key => $val) {
				$dolist[] = $val['DoList'];
			}
		} else {
			return false;
		}

		$parents = arraySearch($do_lists, 'parent_id', 0, true);
		$children = $indivisual = null;
		$children_ids = $indivisual_sub = $child_ids = null;
		if (isset($parents) && !empty($parents)) {
			foreach ($parents as $key => $val) {
				$child = null;
				$child = arraySearch($do_lists, 'parent_id', $val['id']);
				$children[$val['id']] = $val;
				$child_ids[] = $val['id'];
				if (isset($child) && !empty($child)) {
					$children[$val['id']]['children'] = $child;
					// $children_ids[] = $val;
					foreach ($child as $k => $v) {
						$children_ids[] = $v;
						$child_ids[] = $v['id'];
					}
				}
			}
		}

		if ((isset($children_ids) && !empty($children_ids))) {

			foreach ($dolist as $k => $v) {
				if (!in_array($v['id'], $child_ids)) {
					$indivisual_sub[] = $v;
				}
			}
			// $indivisual_sub = check_diff_multi($dolist, $children_ids);
		} else {
			$indivisual_sub = arraySearch($do_lists, 'parent_id');
		}

		if ((isset($children) && !empty($children)) && (isset($indivisual_sub) && !empty($indivisual_sub))) {
			$data = array_merge($children, $indivisual_sub);
		} else if ((isset($children) && !empty($children)) && (!isset($indivisual_sub) || empty($indivisual_sub))) {
			$data = $children;
		}

		if ((!isset($children) || empty($children)) && (isset($indivisual_sub) && !empty($indivisual_sub))) {
			$data = $indivisual_sub;
		}

		return (isset($data) && !empty($data)) ? $data : false;
	}

	public function do_list_children($do_list_id = null, $day = null, $sdate = null, $edate = null) {

		$user_id = $this->Session->read("Auth.User.id");

		// $this->_do_list->bindModel(array("belongsTo" => array("Project")));
		// $this->_do_list->unbindModel(array("hasMany" => array("Children"), "belongsTo" => array("Parent")));

		$conditions = $cond = $contain = null;
		if (isset($sdate) && isset($edate) && !empty($sdate) && !empty($edate)) {
			$start_date = $this->params['named']['sdate'];
			$end_date = $this->params['named']['edate'];
			$conditions['AND'][] = array('DoList.start_date >=' => $start_date);
			$conditions['AND'][] = array('DoList.end_date <=' => $end_date);
		}
		if (isset($day) && !empty($day)) {
			if ($day == 'active') {
				$conditions['AND'][] = array('DoList.sign_off' => 0);
			} else if ($day == 'today') {
				$conditions['AND'][] = array('DoList.start_date <=' => date('Y-m-d'));
				$conditions['AND'][] = array('DoList.end_date >=' => date('Y-m-d'));
				$conditions['AND'][] = array('DoList.sign_off !=' => 1);
			} else if ($day == 'tomorrow') {
				$conditions['AND'][] = array('DoList.start_date' => date('Y-m-d', strtotime("+1 day")));
				$conditions['AND'][] = array('DoList.sign_off !=' => 1);
			} else if ($day == 'upcoming') {
				$conditions['AND'][] = array('DoList.start_date >=' => date('Y-m-d', strtotime("+2 day")));
				$conditions['AND'][] = array('DoList.sign_off !=' => 1);
			} else if ($day == 'notset') {
				$conditions['AND'][] = array('DoList.start_date IS NULL');
				$conditions['AND'][] = array('DoList.end_date IS NULL');
				$conditions['AND'][] = array('DoList.sign_off !=' => 1);
			} else if ($day == 'completed') {
				$conditions['AND'][] = array('DoList.sign_off' => 1);
			} else if ($day == 'overdue') {
				$conditions['AND'][] = array('DoList.end_date <' => date('Y-m-d'));
				$conditions['AND'][] = array('DoList.sign_off !=' => 1);
			}
		}

		$conditions['AND'][] = [
			'DoList.parent_id' => $do_list_id,
			'DoList.is_archive' => 0,
		];

		$data = $this->_do_list->find("all", array(
			"conditions" => $conditions,
			"recursive" => -1,
		)
		);

		return (isset($data) && !empty($data)) ? $data : false;
	}

	public function rec_do_lists($project_id = null, $day = null, $sdate = null, $edate = null, $count = false) {

		$user_id = $this->Session->read("Auth.User.id");

		$this->_do_list->bindModel(array("belongsTo" => array("Project")));

		$conditions = $cond = $contain = $data = null;

		if (isset($project_id) && !empty($project_id)) {
			$conditions['AND'][] = array('DoList.project_id' => $project_id);
		} else {
			$conditions['AND'][] = array('DoList.project_id IS NULL');
		}
		if (isset($sdate) && isset($edate) && !empty($sdate) && !empty($edate)) {
			$start_date = $this->params['named']['sdate'];
			$end_date = $this->params['named']['edate'];
			$conditions['AND'][] = array('DoList.start_date >=' => $start_date);
			$conditions['AND'][] = array('DoList.end_date <=' => $end_date);
		}
		if (isset($day) && !empty($day)) {
			if ($day == 'all') {
				// $conditions['AND'][] = array('DoList.sign_off' => [0,1]);
			} else if ($day == 'today') {
				$conditions['AND'][] = array('DoList.start_date <=' => date('Y-m-d'));
				$conditions['AND'][] = array('DoList.end_date >=' => date('Y-m-d'));
				$conditions['AND'][] = array('DoList.sign_off !=' => 1);
			} /*else if ($day == 'tomorrow') {
				$conditions['AND'][] = array('DoList.start_date' => date('Y-m-d', strtotime("+1 day")));
				$conditions['AND'][] = array('DoList.sign_off !=' => 1);
			}*/ else if ($day == 'notstarted') {
				$conditions['AND'][] = array('DoList.start_date >' => date('Y-m-d'));
				$conditions['AND'][] = array('DoList.sign_off !=' => 1);
			} else if ($day == 'notset') {
				$conditions['AND'][] = array('DoList.start_date IS NULL');
				$conditions['AND'][] = array('DoList.end_date IS NULL');
				$conditions['AND'][] = array('DoList.sign_off !=' => 1);
			} else if ($day == 'completed') {
				$conditions['AND'][] = array('DoList.sign_off' => 1);
			} else if ($day == 'overdue') {
				$conditions['AND'][] = array('DoList.end_date <' => date('Y-m-d'));
				$conditions['AND'][] = array('DoList.sign_off !=' => 1);
			}
		}

		$user_data = $this->_do_list_user->find('all', [
			'conditions' => [
				'DoListUser.user_id' => $user_id,
				'DoListUser.owner_id !=' => $user_id,
				'DoListUser.approved' => 1,
			],
			'fields' => ['DoListUser.do_list_id'],
		]);

		if (isset($user_data) && !empty($user_data)) {
			$user_data = Set::extract($user_data, '/DoListUser/do_list_id');
			$conditions['AND'][] = [
				'DoList.id' => $user_data,
				'DoList.is_archive' => 0,
			];
		}
		if (isset($user_data) && !empty($user_data)) {
			$rec_do_lists = $this->_do_list->find("all", array(
				"conditions" => $conditions,
				"group" => "DoList.id",
				"recursive" => -1,
			)
			);
		}

		if($count){
			return (isset($rec_do_lists) && !empty($rec_do_lists)) ? count($rec_do_lists) : 0;
		}

		$dolist = null;
		if (isset($rec_do_lists) && !empty($rec_do_lists)) {

			foreach ($rec_do_lists as $key => $val) {
				$dolist[] = $val['DoList'];
			}
		} else {
			return false;
		}

		$parents = arraySearch($rec_do_lists, 'parent_id', 0, true);
		$children = $indivisual = null;
		$children_ids = $indivisual_sub = null;
		if (isset($parents) && !empty($parents)) {
			foreach ($parents as $key => $val) {
				$child = null;
				$child = arraySearch($rec_do_lists, 'parent_id', $val['id']);
				$children[$val['id']] = $val;

				if (isset($child) && !empty($child)) {
					$children[$val['id']]['children'] = $child;
					$children_ids[] = $val;
					foreach ($child as $k => $v) {
						$children_ids[] = $v;
					}
				}
			}
		}
		//
		//
		if ((isset($children_ids) && !empty($children_ids))) {
			$indivisual_sub = check_diff_multi($dolist, $children_ids);
		} else {
			$indivisual_sub = arraySearch($rec_do_lists, 'parent_id');
		}

		if ((isset($children) && !empty($children)) && (isset($indivisual_sub) && !empty($indivisual_sub))) {
			$data = array_merge($children, $indivisual_sub);
			if (isset($data) && !empty($data)) {
				$array = [];
				foreach ($data as $k => $dovalue) {
					if (!in_array($dovalue['id'], $array)) {
						$array[] = $dovalue['id'];
						$data[$k] = $dovalue;
					} else {
						unset($data[$k]);
					}
				}
			}
		} else if ((isset($children) && !empty($children)) && (!isset($indivisual_sub) || empty($indivisual_sub))) {
			$data = $children;
		}

		if ((!isset($children) || empty($children)) && (isset($indivisual_sub) && !empty($indivisual_sub))) {
			$data = $indivisual_sub;
		}

		return (isset($data) && !empty($data)) ? $data : false;
	}

	public function archive_do_lists($project_id = null, $day = null, $sdate = null, $edate = null, $count = false) {

		$user_id = $this->Session->read("Auth.User.id");

		// $this->_do_list->bindModel(array("belongsTo" => array("Project")));
		$this->_do_list->unbindModel(array("hasMany" => array("Children"), "belongsTo" => array("Parent")));

		$conditions = $cond = $contain = null;

		if (isset($project_id) && !empty($project_id)) {
			$conditions['AND'][] = array('DoList.project_id' => $project_id);
		} else {
			$conditions['AND'][] = array('DoList.project_id IS NULL');
		}
		if (isset($sdate) && isset($edate) && !empty($sdate) && !empty($edate)) {
			$start_date = $this->params['named']['sdate'];
			$end_date = $this->params['named']['edate'];
			$conditions['AND'][] = array('DoList.start_date >=' => $start_date);
			$conditions['AND'][] = array('DoList.end_date <=' => $end_date);
		}

		if (isset($day) && !empty($day)) {
			if ($day == 'all') {
				// $conditions['AND'][] = array('DoList.sign_off' => [0,1]);
			} else if ($day == 'today') {
				$conditions['AND'][] = array('DoList.start_date <=' => date('Y-m-d'));
				$conditions['AND'][] = array('DoList.end_date >=' => date('Y-m-d'));
				$conditions['AND'][] = array('DoList.sign_off !=' => 1);
			} /*else if ($day == 'tomorrow') {
				$conditions['AND'][] = array('DoList.start_date' => date('Y-m-d', strtotime("+1 day")));
				$conditions['AND'][] = array('DoList.sign_off !=' => 1);
			}*/ else if ($day == 'notstarted') {
				$conditions['AND'][] = array('DoList.start_date >' => date('Y-m-d'));
				$conditions['AND'][] = array('DoList.sign_off !=' => 1);
			} else if ($day == 'notset') {
				$conditions['AND'][] = array('DoList.start_date IS NULL');
				$conditions['AND'][] = array('DoList.end_date IS NULL');
				$conditions['AND'][] = array('DoList.sign_off !=' => 1);
			} else if ($day == 'completed') {
				$conditions['AND'][] = array('DoList.sign_off' => 1);
			} else if ($day == 'overdue') {
				$conditions['AND'][] = array('DoList.end_date <' => date('Y-m-d'));
				$conditions['AND'][] = array('DoList.sign_off !=' => 1);
			}
		}

		$conditions['AND'][] = [
			'DoList.user_id' => $user_id,
			'DoList.is_archive' => 1,
		];

		$do_lists = $this->_do_list->find("all", array(
			"conditions" => $conditions,
			"recursive" => -1,
		)
		);

		/********************************************************************/
		$rec_conditions = [];
		if (isset($project_id) && !empty($project_id)) {
			$rec_conditions['AND'][] = array('DoList.project_id' => $project_id);
		} else {
			$rec_conditions['AND'][] = array('DoList.project_id IS NULL');
		}
		$rec_conditions['AND'][] = array('DoList.is_archive' => 1);
		// Get all received todos
		$user_data = $this->_do_list_user->find('all', [
			'conditions' => [
				'DoListUser.user_id' => $user_id,
				'DoListUser.owner_id !=' => $user_id,
				'DoListUser.approved' => 1,
			],
			'fields' => ['DoListUser.do_list_id'],
		]);

		if (isset($user_data) && !empty($user_data)) {
			$user_data = Set::extract($user_data, '/DoListUser/do_list_id');
			$rec_conditions['AND'][] = [
				'DoList.id' => $user_data,
			];

			$rec_do_lists = $this->_do_list->find("all", array(
				"conditions" => $rec_conditions,
				"recursive" => -1,
			)
			);
		}
		$alldata = [];
		if (isset($do_lists) && !empty($do_lists)) {
			$alldata = $do_lists;
		}
		// merge received do list if exists.
		if (isset($alldata) && !empty($alldata)) {
			if (isset($rec_do_lists) && !empty($rec_do_lists)) {
				$alldata = array_merge($alldata, $rec_do_lists);
			}
		} else if (isset($rec_do_lists) && !empty($rec_do_lists)) {
			$alldata = $rec_do_lists;
		}

		// Get all do list ids
		if (isset($alldata) && !empty($alldata)) {
			$doListId = Set::extract($alldata, '/DoList/id');

			$do_lists = $this->_do_list->find("all", array(
				"conditions" => ['DoList.id' => $doListId],
				"recursive" => -1,
			)
			);

		}
		/********************************************************************/

		if($count){
			return (isset($do_lists) && !empty($do_lists)) ? count($do_lists) : 0;
		}

		$dolist = null;
		if (isset($do_lists) && !empty($do_lists)) {
			foreach ($do_lists as $key => $val) {
				$dolist[] = $val['DoList'];
			}
		} else {
			return false;
		}

		$parents = arraySearch($do_lists, 'parent_id', 0, true);
		$children = $indivisual = null;
		$children_ids = $indivisual_sub = $child_ids = null;
		if (isset($parents) && !empty($parents)) {
			foreach ($parents as $key => $val) {
				$child = null;
				$child = arraySearch($do_lists, 'parent_id', $val['id']);
				$children[$val['id']] = $val;
				$child_ids[] = $val['id'];
				if (isset($child) && !empty($child)) {
					$children[$val['id']]['children'] = $child;
					// $children_ids[] = $val;
					foreach ($child as $k => $v) {
						$children_ids[] = $v;
						$child_ids[] = $v['id'];
					}
				}
			}
		}

		if ((isset($children_ids) && !empty($children_ids))) {

			foreach ($dolist as $k => $v) {
				if (!in_array($v['id'], $child_ids)) {
					$indivisual_sub[] = $v;
				}
			}
			// $indivisual_sub = check_diff_multi($dolist, $children_ids);
		} else {
			$indivisual_sub = arraySearch($do_lists, 'parent_id');
		}

		if ((isset($children) && !empty($children)) && (isset($indivisual_sub) && !empty($indivisual_sub))) {
			$data = array_merge($children, $indivisual_sub);
		} else if ((isset($children) && !empty($children)) && (!isset($indivisual_sub) || empty($indivisual_sub))) {
			$data = $children;
		}

		if ((!isset($children) || empty($children)) && (isset($indivisual_sub) && !empty($indivisual_sub))) {
			$data = $indivisual_sub;
		}

		return (isset($data) && !empty($data)) ? $data : false;
	}

	public function do_comment_uploads($do_list_comment_id = null) {

		$data = $this->_do_comment_upload->find('all', [
			'conditions' => [
				'DoListCommentUpload.do_list_comment_id' => $do_list_comment_id,
			],
			'recursive' => -1,
		]);

		return (isset($data) && !empty($data)) ? $data : false;
	}

	public function do_comment_likes($do_list_comment_id = null) {

		$data = $this->_do_comment_like->find('count', [
			'conditions' => [
				'DoListCommentLike.do_list_comment_id' => $do_list_comment_id,
			],
		]);

		return (isset($data) && !empty($data)) ? $data : false;
	}

	public function do_list_uploads($do_list_id = null) {

		$data = $this->_do_list_upload->find('all', [
			'conditions' => [
				'DoListUpload.do_list_id' => $do_list_id,
			],
			'recursive' => -1,
		]);

		return (isset($data) && !empty($data)) ? $data : false;
	}

	public function dolist_users($do_list_id = null, $flag = true) {
		$conditions = null;
		if ($flag == true) {
			$conditions = array('DoListUser.do_list_id' => $do_list_id, 'DoListUser.approved' => 1);
		} else {
			$conditions = array('DoListUser.do_list_id' => $do_list_id);
		}

		$data = $this->_do_list_user->find('all', [
			'conditions' => $conditions,
			'recursive' => -1,
		]);

		return (isset($data) && !empty($data)) ? $data : false;
	}

	public function dolist_counters($project_id = null, $day = null, $sdate = null, $edate = null) {

		$user_id = $this->Session->read("Auth.User.id");

		$my_do_list = $rec_do_lists = $alldata = $doListId = $conditions = null;

		$ns_archieve = $active_count = $today_count = $tom_count = $up_count = $ns_count = $over_count = $com_count = null;

		if (isset($project_id) && !empty($project_id)) {
			$conditions['AND'][] = array('DoList.project_id' => $project_id);
		} else {
			$conditions['AND'][] = array('DoList.project_id IS NULL');
		}

		$conditions['AND'][] = array('DoList.user_id' => $user_id);
		// $conditions['AND'][] = array('DoList.is_archive' => 0);

		// Get all my todos
		$my_do_list = $this->_do_list->find('all', [
			'conditions' => $conditions,
			'recursive' => -1,
		]);

		$rec_conditions = null;

		if (isset($project_id) && !empty($project_id)) {
			$rec_conditions['AND'][] = array('DoList.project_id' => $project_id);
		} else {
			$rec_conditions['AND'][] = array('DoList.project_id IS NULL');
		}

		// Get all received todos
		$user_data = $this->_do_list_user->find('all', [
			'conditions' => [
				'DoListUser.user_id' => $user_id,
				'DoListUser.owner_id !=' => $user_id,
				'DoListUser.approved' => 1,
			],
			'fields' => ['DoListUser.do_list_id'],
		]);

		if (isset($user_data) && !empty($user_data)) {
			$user_data = Set::extract($user_data, '/DoListUser/do_list_id');
			$rec_conditions['AND'][] = [
				'DoList.id' => $user_data,
				// 'DoList.is_archive' => 0
			];

			$rec_do_lists = $this->_do_list->find("all", array(
				"conditions" => $rec_conditions,
				"recursive" => -1,
			)
			);
		}

		if (isset($my_do_list) && !empty($my_do_list)) {
			$alldata = $my_do_list;
		}
		// merge received do list if exists.
		if (isset($alldata) && !empty($alldata)) {
			if (isset($rec_do_lists) && !empty($rec_do_lists)) {
				$alldata = array_merge($alldata, $rec_do_lists);
			}
		} else if (isset($rec_do_lists) && !empty($rec_do_lists)) {
			$alldata = $rec_do_lists;
		}

		// Get all do list ids
		if (isset($alldata) && !empty($alldata)) {
			$doListId = Set::extract($alldata, '/DoList/id');
		}
		// pr($doListId);

		$cond = null;
		if (isset($project_id) && !empty($project_id)) {
			$cond['AND'][] = array('DoList.project_id' => $project_id);
		} else {
			$cond['AND'][] = array('DoList.project_id IS NULL');
		}

		$cond['AND'][] = array('DoList.user_id' => $user_id);
		// $cond['AND'][] = array('DoList.is_archive' => 0);

		if (isset($doListId) && !empty($doListId)) {

			// Get Today's Count
			$today_cond['AND'][] = array('DoList.start_date <=' => date('Y-m-d'));
			$today_cond['AND'][] = array('DoList.end_date >=' => date('Y-m-d'));
			$today_cond['AND'][] = array('DoList.sign_off !=' => 1);
			// $today_cond['AND'][] = array('DoList.is_archive' => 0);

			if (isset($doListId) && !empty($doListId)) {
				$today_cond['AND'][] = array('DoList.id' => $doListId);
			}

			if (isset($project_id) && !empty($project_id)) {
				$today_cond['AND'][] = array('DoList.project_id' => $project_id);
			} else {
				$today_cond['AND'][] = array('DoList.project_id IS NULL');
			}

			// $today_cond['AND'][] = array('DoList.user_id' => $user_id);

			$today_count = $this->_do_list->find("count", array(
				"conditions" => $today_cond,
				"recursive" => -1,
			)
			);

			// Get Tomorrow's Count
			$tom_cond['AND'][] = array('DoList.start_date' => date('Y-m-d', strtotime("+1 day")));
			$tom_cond['AND'][] = array('DoList.sign_off !=' => 1);
			// $tom_cond['AND'][] = array('DoList.is_archive' => 0);

			if (isset($doListId) && !empty($doListId)) {
				$tom_cond['AND'][] = array('DoList.id' => $doListId);
			}

			if (isset($project_id) && !empty($project_id)) {
				$tom_cond['AND'][] = array('DoList.project_id' => $project_id);
			} else {
				$tom_cond['AND'][] = array('DoList.project_id IS NULL');
			}

			// $tom_cond['AND'][] = array('DoList.user_id' => $user_id);

			$tom_count = $this->_do_list->find("count", array(
				"conditions" => $tom_cond,
				"recursive" => -1,
			)
			);

			// Get Upcomming's Count
			$up_cond['AND'][] = array('DoList.start_date >=' => date('Y-m-d', strtotime("+2 day")));
			$up_cond['AND'][] = array('DoList.sign_off !=' => 1);
			// $up_cond['AND'][] = array('DoList.is_archive' => 0);

			if (isset($doListId) && !empty($doListId)) {
				$up_cond['AND'][] = array('DoList.id' => $doListId);
			}

			if (isset($project_id) && !empty($project_id)) {
				$up_cond['AND'][] = array('DoList.project_id' => $project_id);
			} else {
				$up_cond['AND'][] = array('DoList.project_id IS NULL');
			}
			// pr($up_cond);
			// $up_cond['AND'][] = array('DoList.user_id' => $user_id);

			$up_count = $this->_do_list->find("count", array(
				"conditions" => $up_cond,
				"recursive" => -1,
			)
			);

			// Get Overdue's Count
			$over_cond['AND'][] = array('DoList.end_date <' => date('Y-m-d'));
			$over_cond['AND'][] = array('DoList.sign_off !=' => 1);
			// $over_cond['AND'][] = array('DoList.is_archive' => 0);

			if (isset($doListId) && !empty($doListId)) {
				$over_cond['AND'][] = array('DoList.id' => $doListId);
			}

			if (isset($project_id) && !empty($project_id)) {
				$over_cond['AND'][] = array('DoList.project_id' => $project_id);
			} else {
				$over_cond['AND'][] = array('DoList.project_id IS NULL');
			}

			// $over_cond['AND'][] = array('DoList.user_id' => $user_id);

			$over_count = $this->_do_list->find("count", array(
				"conditions" => $over_cond,
				"recursive" => -1,
			)
			);

			// Get Not set Count
			$ns_cond['AND'][] = array('DoList.start_date IS NULL');
			$ns_cond['AND'][] = array('DoList.end_date IS NULL');
			$ns_cond['AND'][] = array('DoList.sign_off !=' => 1);
			// $ns_cond['AND'][] = array('DoList.is_archive' => 0);

			if (isset($doListId) && !empty($doListId)) {
				$ns_cond['AND'][] = array('DoList.id' => $doListId);
			}

			if (isset($project_id) && !empty($project_id)) {
				$ns_cond['AND'][] = array('DoList.project_id' => $project_id);
			} else {
				$ns_cond['AND'][] = array('DoList.project_id IS NULL');
			}

			// $ns_cond['AND'][] = array('DoList.user_id' => $user_id);

			$ns_count = $this->_do_list->find("count", array(
				"conditions" => $ns_cond,
				"recursive" => -1,
			)
			);

			$arh_cond = null;
			$ns_archieve = 0;

			if (isset($project_id) && !empty($project_id)) {
				$arh_cond['AND'][] = array('DoList.project_id' => $project_id, 'DoList.is_archive' => 1, 'DoList.user_id' => $user_id);
			} else {
				$arh_cond['AND'][] = array('DoList.project_id IS NULL', 'DoList.is_archive' => 1, 'DoList.user_id' => $user_id);
			}

			$ns_archieve = $this->_do_list->find("count", array(
				"conditions" => $arh_cond,
				"recursive" => -1,
			)
			);

			// Get Completed Count
			$com_cond['AND'][] = array('DoList.sign_off' => 1, 'DoList.is_archive !=' => 1);
			//$com_cond['AND'][] = array('DoList.is_archive' => 0);

			if (isset($doListId) && !empty($doListId)) {
				$com_cond['AND'][] = array('DoList.id' => $doListId);
			}

			if (isset($project_id) && !empty($project_id)) {
				$com_cond['AND'][] = array('DoList.project_id' => $project_id);
			} else {
				$com_cond['AND'][] = array('DoList.project_id IS NULL');
			}

			// $com_cond['AND'][] = array('DoList.user_id' => $user_id);

			$com_count = $this->_do_list->find("count", array(
				"conditions" => $com_cond,
				"recursive" => -1,
			)
			);

		}

		$total = $today_count + $tom_count + $up_count + $ns_count + $over_count;
		$active_count = $today_count + $tom_count + $up_count + $ns_count + $over_count;
		$data = ['active_count' => $active_count, 'today_count' => $today_count, 'tom_count' => $tom_count, 'up_count' => $up_count, 'ns_count' => $ns_count, 'over_count' => $over_count, 'com_count' => $com_count, 'total' => $total, 'archive' => $ns_archieve];

		return (isset($data) && !empty($data)) ? $data : false;
	}

	public function do_list_users($todo_id = null,$user_signoff = 0) {
		$user_id = $this->Session->read("Auth.User.id");



		$allchild = $this->_do_list->find("all", array("fields" => array("DoList.id"), "conditions" => array("DoList.parent_id" => $todo_id )));
		$ids = Set::extract('/DoList/id', $allchild);
		$ids[] = $todo_id;


		$arr_cond = array("DoListUser.do_list_id" => $ids);
		if( isset($user_signoff) && $user_signoff == 1 ){
			$arr_cond = array("DoListUser.do_list_id" => $ids,"DoListUser.approved"=>1);
		}

		$do_list_users = $this->_do_list_user->find('all', [
			"fields" => array("DoListUser.user_id"),
			'conditions' => $arr_cond,
			'recursive' => -1,
			'group' => ['DoListUser.user_id'],
		]);

		$user_ids = Set::extract('/DoListUser/user_id', $do_list_users);
		return $user_ids;
	}

	public function header_dolist() {

		$user_id = $this->Session->read("Auth.User.id");

		$my_do_list = $rec_do_lists = $alldata = $doListId = $conditions = null;

		$active_count = $today_count = $tom_count = $up_count = $ns_count = $over_count = $com_count = null;

		$conditions['AND'][] = array('DoList.project_id IS NOT NULL');
		$conditions['AND'][] = array('DoList.user_id' => $user_id);
		$conditions['AND'][] = array('DoList.sign_off !=' => 1);

		// Get all my todos
		$my_do_list = $this->_do_list->find('all', [
			'conditions' => $conditions,
			'recursive' => -1,
			'group' => ['DoList.project_id'],

		]);

		$rec_conditions = null;

		$rec_conditions['AND'][] = array('DoList.project_id IS NOT NULL');

		// Get all received todos
		$user_data = $this->_do_list_user->find('all', [
			'conditions' => [
				'DoListUser.user_id' => $user_id,
				'DoListUser.owner_id !=' => $user_id,
				'DoListUser.approved' => 1,
			],
			'fields' => ['DoListUser.do_list_id'],
		]);

		if (isset($user_data) && !empty($user_data)) {
			$user_data = Set::extract($user_data, '/DoListUser/do_list_id');
			$rec_conditions['AND'][] = [
				'DoList.id' => $user_data,
			];

			$rec_do_lists = $this->_do_list->find("all", array(
				"conditions" => $rec_conditions,
				"recursive" => -1,
				'group' => ['DoList.project_id'],
			)
			);

		}

		if (isset($my_do_list) && !empty($my_do_list)) {
			$alldata = $my_do_list;
		}
		// merge received do list if exists.
		if (isset($alldata) && !empty($alldata)) {
			if (isset($rec_do_lists) && !empty($rec_do_lists)) {
				$alldata = array_merge($alldata, $rec_do_lists);
			}
		} else if (isset($rec_do_lists) && !empty($rec_do_lists)) {
			$alldata = $rec_do_lists;
		}

		// Get all do list ids
		if (isset($alldata) && !empty($alldata)) {
			$doListId = Set::combine($alldata, '{n}.DoList.id', '{n}.DoList.project_id');
		}
		// pr($doListId);

		return (isset($doListId) && !empty($doListId)) ? $doListId : false;
	}

}
