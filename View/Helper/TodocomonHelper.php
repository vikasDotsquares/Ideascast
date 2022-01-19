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
App::uses('Helper', 'View', 'Auth', 'Session');
App::uses('Sanitize', 'Utility');
App::uses('CakeText', 'Utility');

App::import("Model", "DoList");
App::import("Model", "DoListUser");

/**
 * Application helper
 *
 * Add your application-wide methods in the class below, your helpers
 * will inherit them.
 *
 * @package       app.View.Helper
 */
class TodocomonHelper extends Helper {

	protected $_do_list;
	protected $_do_list_user;

	public function __construct(View $View, $settings = array()) {

		parent::__construct($View, $settings);

		$this->_do_list = new DoList();
		$this->_do_list_user = new DoListUser();
	}

	var $helpers = array('Html', 'Session', 'Text', "Common");

	public function getAllByProject($project_id = null) {
		$user_id = $this->Session->read("Auth.User.id");
		$this->ToDo = ClassRegistry::init('ToDo');
		$this->ToDoSub = ClassRegistry::init('ToDoSub');
		$countByProject = 0;

		$this->ToDo->unbindModel(array("hasMany" => array("ToDoUser")));
		$this->ToDo->bindModel(array("hasOne" => array("ToDoUser")));

		$countTodo = $this->ToDo->find("count", array(
			"contain" => array("ToDoUser", "ToDoUpload"),
			"group" => "ToDo.id",
			'conditions' => array(
				'ToDo.project_id' => $project_id,
				"AND" => array(
					"OR" => array('ToDoUser.approved' => 1, 'ToDoUser.owner_id' => $user_id),
				),
				"OR" => array(
					'ToDoUser.owner_id' => $user_id,
					'ToDoUser.user_id' => $user_id,
				),
			),
		)
		);

		$this->ToDoSub->unbindModel(array("hasMany" => array("ToDoSubUser")));
		$this->ToDoSub->bindModel(array("hasOne" => array("ToDoSubUser")));

		$countTodosub = $this->ToDoSub->find("count", array(
			"contain" => array("ToDoSubUser", "ToDoSubUpload"),
			"group" => "ToDoSub.id",
			'conditions' => array(
				'ToDoSub.project_id' => $project_id,
				"AND" => array(
					"OR" => array('ToDoSubUser.approved' => 1, 'ToDoSubUser.owner_id' => $user_id),
				),
				"OR" => array(
					'ToDoSubUser.owner_id' => $user_id,
					'ToDoSubUser.user_id' => $user_id,
				),
			),
		)
		);

		//pr($countTodo);
		//pr($countTodosub);
		$countByProject = $countTodo + $countTodosub;
		return $countByProject;
	}

	public function getAllUnspecified() {

		$user_id = $this->Session->read("Auth.User.id");
		$this->ToDo = ClassRegistry::init('ToDo');
		$this->ToDoSub = ClassRegistry::init('ToDoSub');
		$countUnspecified = 0;
		$conditions = $subconditions = array();
		$type = isset($this->params['named']['type']) ? $this->params['named']['type'] : null;
		if ($type == 'm') {
			$conditions['AND'][] = array('ToDo.user_id' => $user_id);
			$subconditions['AND'][] = array('ToDoSub.user_id' => $user_id);
		} else if ($type == 'r') {
			$conditions['AND'][] = array('ToDoUser.approved' => 1, 'ToDoUser.user_id' => $user_id);
			$subconditions['AND'][] = array('ToDoSubUser.approved' => 1, 'ToDoSubUser.user_id' => $user_id);
		}
		$conditions['AND'][] = array('ToDo.project_id IS NULL');
		$subconditions['AND'][] = array('ToDoSub.project_id IS NULL');
		$this->ToDo->unbindModel(array("hasMany" => array("ToDoUser")));
		$this->ToDo->bindModel(array("hasOne" => array("ToDoUser")));
		$countTodo = $this->ToDo->find("count", array(
			"contain" => array("ToDoUser", "ToDoUpload"),
			"group" => "ToDo.id",
			'conditions' => $conditions,
		)
		);

		$this->ToDoSub->unbindModel(array("hasMany" => array("ToDoSubUser")));
		$this->ToDoSub->bindModel(array("hasOne" => array("ToDoSubUser")));
		$countTodosub = $this->ToDoSub->find("count", array(
			"contain" => array("ToDoSubUser", "ToDoSubUpload"),
			"group" => "ToDoSub.id",
			'conditions' => $subconditions,
		)
		);

		$countUnspecified = $countTodo + $countTodosub;
		return $countUnspecified;
	}

	public function get_todo_count_today($project_id = null, $type = null, $day = null) {
		$user_id = $this->Session->read("Auth.User.id");
		$this->ToDo = ClassRegistry::init('ToDo');
		$this->ToDoSub = ClassRegistry::init('ToDoSub');
		$this->ToDo->recursive = 1;

		$countToday = 0;
		$conditions = $subconditions = array();

		$conditions['AND'][] = array('ToDo.start_date <=' => date("Y-m-d"), 'ToDo.end_date >=' => date("Y-m-d"), 'ToDo.sign_off !=' => 1);
		$subconditions['AND'][] = array('ToDoSub.start_date <=' => date("Y-m-d"), 'ToDoSub.end_date >=' => date("Y-m-d"), 'ToDoSub.sign_off !=' => 1);
		if ($type == 'm') {
			$conditions['AND'][] = array('ToDo.user_id' => $user_id);
			$subconditions['AND'][] = array('ToDoSub.user_id' => $user_id);
		} else if ($type == 'r') {
			$conditions['AND'][] = array('ToDoUser.approved' => 1, 'ToDoUser.user_id' => $user_id);
			$subconditions['AND'][] = array('ToDoSubUser.approved' => 1, 'ToDoSubUser.user_id' => $user_id);
		}
		if (isset($project_id) && !empty($project_id)) {
			$conditions['AND'][] = array('ToDo.project_id' => $project_id);
			$subconditions['AND'][] = array('ToDoSub.project_id' => $project_id);
		} else {
			$conditions['AND'][] = array('ToDo.project_id IS NULL');
			$subconditions['AND'][] = array('ToDoSub.project_id IS NULL');
		}

		$this->ToDo->unbindModel(array("hasMany" => array("ToDoUser")));
		$this->ToDo->bindModel(array("hasOne" => array("ToDoUser")));
		$countTodo = $this->ToDo->find("count", array(
			"contain" => array("ToDoUser", "ToDoUpload"),
			"group" => "ToDo.id",
			'conditions' => $conditions,
		)
		);

		$this->ToDoSub->unbindModel(array("hasMany" => array("ToDoSubUser")));
		$this->ToDoSub->bindModel(array("hasOne" => array("ToDoSubUser")));
		$countTodosub = $this->ToDoSub->find("count", array(
			"contain" => array("ToDoSubUser", "ToDoSubUpload"),
			"group" => "ToDoSub.id",
			'conditions' => $subconditions,
		)
		);

		$countToday = $countTodo + $countTodosub;
		return $countToday;
	}

	public function get_todo_count_tomorrow($project_id = null, $type = null, $day = null) {
		$user_id = $this->Session->read("Auth.User.id");
		$this->ToDo = ClassRegistry::init('ToDo');
		$this->ToDoSub = ClassRegistry::init('ToDoSub');
		$this->ToDo->recursive = 1;
		$countTomorrow = 0;
		$tomorrow = date("Y-m-d", strtotime("+1 day"));

		$conditions = $subconditions = array();

		$conditions['AND'][] = array('ToDo.start_date' => $tomorrow, 'ToDo.sign_off !=' => 1);
		$subconditions['AND'][] = array('ToDoSub.start_date' => $tomorrow, 'ToDoSub.sign_off !=' => 1);
		if ($type == 'm') {
			$conditions['AND'][] = array('ToDo.user_id' => $user_id);
			$subconditions['AND'][] = array('ToDoSub.user_id' => $user_id);
		} else if ($type == 'r') {
			$conditions['AND'][] = array('ToDoUser.approved' => 1, 'ToDoUser.user_id' => $user_id);
			$subconditions['AND'][] = array('ToDoSubUser.approved' => 1, 'ToDoSubUser.user_id' => $user_id);
		}
		if (isset($project_id) && !empty($project_id)) {
			$conditions['AND'][] = array('ToDo.project_id' => $project_id);
			$subconditions['AND'][] = array('ToDoSub.project_id' => $project_id);
		} else {
			$conditions['AND'][] = array('ToDo.project_id IS NULL');
			$subconditions['AND'][] = array('ToDoSub.project_id IS NULL');
		}

		$this->ToDo->unbindModel(array("hasMany" => array("ToDoUser")));
		$this->ToDo->bindModel(array("hasOne" => array("ToDoUser")));
		$countTomorrowTodo = $this->ToDo->find("count", array(
			"contain" => array("ToDoUser", "ToDoUpload"),
			"group" => "ToDo.id",
			'conditions' => $conditions,
		)
		);

		$this->ToDoSub->unbindModel(array("hasMany" => array("ToDoSubUser")));
		$this->ToDoSub->bindModel(array("hasOne" => array("ToDoSubUser")));
		$countTomorrowTodosub = $this->ToDoSub->find("count", array(
			"contain" => array("ToDoSubUser", "ToDoSubUpload"),
			"group" => "ToDoSub.id",
			'conditions' => $subconditions,
		)
		);

		$countTomorrow = $countTomorrowTodo + $countTomorrowTodosub;
		return $countTomorrow;
	}

	public function get_todo_count_upcoming($project_id = null, $type = null, $day = null) {
		$user_id = $this->Session->read("Auth.User.id");
		$this->ToDo = ClassRegistry::init('ToDo');
		$this->ToDoSub = ClassRegistry::init('ToDoSub');
		$this->ToDo->recursive = 1;
		$countUpcoming = 0;
		$upcoming = date("Y-m-d", strtotime("+1 day"));

		$conditions = $subconditions = array();

		$conditions['AND'][] = array('ToDo.start_date > ' => $upcoming, 'ToDo.sign_off !=' => 1);
		$subconditions['AND'][] = array('ToDoSub.start_date > ' => $upcoming, 'ToDoSub.sign_off !=' => 1);
		if ($type == 'm') {
			$conditions['AND'][] = array('ToDo.user_id' => $user_id);
			$subconditions['AND'][] = array('ToDoSub.user_id' => $user_id);
		} else if ($type == 'r') {
			$conditions['AND'][] = array('ToDoUser.approved' => 1, 'ToDoUser.user_id' => $user_id);
			$subconditions['AND'][] = array('ToDoSubUser.approved' => 1, 'ToDoSubUser.user_id' => $user_id);
		}
		if (isset($project_id) && !empty($project_id)) {
			$conditions['AND'][] = array('ToDo.project_id' => $project_id);
			$subconditions['AND'][] = array('ToDoSub.project_id' => $project_id);
		} else {
			$conditions['AND'][] = array('ToDo.project_id IS NULL');
			$subconditions['AND'][] = array('ToDoSub.project_id IS NULL');
		}

		$this->ToDo->unbindModel(array("hasMany" => array("ToDoUser")));
		$this->ToDo->bindModel(array("hasOne" => array("ToDoUser")));
		$countUpcomingTodo = $this->ToDo->find("count", array(
			"contain" => array("ToDoUser", "ToDoUpload"),
			"group" => "ToDo.id",
			'conditions' => $conditions,
		)
		);

		$this->ToDoSub->unbindModel(array("hasMany" => array("ToDoSubUser")));
		$this->ToDoSub->bindModel(array("hasOne" => array("ToDoSubUser")));
		$countUpcomingTodosub = $this->ToDoSub->find("count", array(
			"contain" => array("ToDoSubUser", "ToDoSubUpload"),
			"group" => "ToDoSub.id",
			'conditions' => $subconditions,
		)
		);

		$countUpcoming = $countUpcomingTodo + $countUpcomingTodosub;
		return $countUpcoming;
	}

	public function get_todo_count_overdue($project_id = null, $type = null, $day = null) {

		$user_id = $this->Session->read("Auth.User.id");
		$this->ToDo = ClassRegistry::init('ToDo');
		$this->ToDoSub = ClassRegistry::init('ToDoSub');
		$this->ToDo->recursive = 1;
		$countOverdue = 0;

		$conditions = $subconditions = array();

		$conditions['AND'][] = array('ToDo.end_date < ' => date("Y-m-d"), 'ToDo.sign_off !=' => 1);
		$subconditions['AND'][] = array('ToDoSub.end_date < ' => date("Y-m-d"), 'ToDoSub.sign_off !=' => 1);
		if ($type == 'm') {
			$conditions['AND'][] = array('ToDo.user_id' => $user_id);
			$subconditions['AND'][] = array('ToDoSub.user_id' => $user_id);
		} else if ($type == 'r') {
			$conditions['AND'][] = array('ToDoUser.approved' => 1, 'ToDoUser.user_id' => $user_id);
			$subconditions['AND'][] = array('ToDoSubUser.approved' => 1, 'ToDoSubUser.user_id' => $user_id);
		}
		if (isset($project_id) && !empty($project_id)) {
			$conditions['AND'][] = array('ToDo.project_id' => $project_id);
			$subconditions['AND'][] = array('ToDoSub.project_id' => $project_id);
		} else {
			$conditions['AND'][] = array('ToDo.project_id IS NULL');
			$subconditions['AND'][] = array('ToDoSub.project_id IS NULL');
		}

		$this->ToDo->unbindModel(array("hasMany" => array("ToDoUser")));
		$this->ToDo->bindModel(array("hasOne" => array("ToDoUser")));
		$countOverdueTodo = $this->ToDo->find("count", array(
			"contain" => array("ToDoUser", "ToDoUpload"),
			"group" => "ToDo.id",
			'conditions' => $conditions,
		)
		);

		$this->ToDoSub->unbindModel(array("hasMany" => array("ToDoSubUser")));
		$this->ToDoSub->bindModel(array("hasOne" => array("ToDoSubUser")));
		$countOverdueTodosub = $this->ToDoSub->find("count", array(
			"contain" => array("ToDoSubUser", "ToDoSubUpload"),
			"group" => "ToDoSub.id",
			'conditions' => $subconditions,
		)
		);

		$countOverdue = $countOverdueTodo + $countOverdueTodosub;

		return $countOverdue;
	}

	public function get_todo_count_notset($project_id = null, $type = null, $day = null) {

		$user_id = $this->Session->read("Auth.User.id");
		$this->ToDo = ClassRegistry::init('ToDo');
		$this->ToDoSub = ClassRegistry::init('ToDoSub');
		$this->ToDo->recursive = 1;
		$countNotSet = 0;

		$conditions = $subconditions = array();

		$conditions['AND'][] = array('ToDo.start_date IS NULL', 'ToDo.sign_off !=' => 1);
		$subconditions['AND'][] = array('ToDoSub.start_date IS NULL', 'ToDoSub.sign_off !=' => 1);
		if ($type == 'm') {
			$conditions['AND'][] = array('ToDo.user_id' => $user_id);
			$subconditions['AND'][] = array('ToDoSub.user_id' => $user_id);
		} else if ($type == 'r') {
			$conditions['AND'][] = array('ToDoUser.approved' => 1, 'ToDoUser.user_id' => $user_id);
			$subconditions['AND'][] = array('ToDoSubUser.approved' => 1, 'ToDoSubUser.user_id' => $user_id);
		}
		if (isset($project_id) && !empty($project_id)) {
			$conditions['AND'][] = array('ToDo.project_id' => $project_id);
			$subconditions['AND'][] = array('ToDoSub.project_id' => $project_id);
		} else {
			$conditions['AND'][] = array('ToDo.project_id IS NULL');
			$subconditions['AND'][] = array('ToDoSub.project_id IS NULL');
		}

		$this->ToDo->unbindModel(array("hasMany" => array("ToDoUser")));
		$this->ToDo->bindModel(array("hasOne" => array("ToDoUser")));
		$countNotSetTodo = $this->ToDo->find("count", array(
			"contain" => array("ToDoUser", "ToDoUpload"),
			"group" => "ToDo.id",
			'conditions' => $conditions,
		)
		);

		$this->ToDoSub->unbindModel(array("hasMany" => array("ToDoSubUser")));
		$this->ToDoSub->bindModel(array("hasOne" => array("ToDoSubUser")));
		$countNotSetTodosub = $this->ToDoSub->find("count", array(
			"contain" => array("ToDoSubUser", "ToDoSubUpload"),
			"group" => "ToDoSub.id",
			'conditions' => $subconditions,
		)
		);

		$countNotSet = $countNotSetTodo + $countNotSetTodosub;
		return $countNotSet;
	}

	public function get_todo_count_completed($project_id = null, $type = null, $day = null) {

		$user_id = $this->Session->read("Auth.User.id");
		$this->ToDo = ClassRegistry::init('ToDo');
		$this->ToDoSub = ClassRegistry::init('ToDoSub');
		$this->ToDo->recursive = 1;
		$countCompleted = 0;

		$conditions = $subconditions = array();

		$conditions['AND'][] = array('ToDo.sign_off' => 1);
		$subconditions['AND'][] = array('ToDoSub.sign_off' => 1);
		if ($type == 'm') {
			$conditions['AND'][] = array('ToDo.user_id' => $user_id);
			$subconditions['AND'][] = array('ToDoSub.user_id' => $user_id);
		} else if ($type == 'r') {
			$conditions['AND'][] = array('ToDoUser.approved' => 1, 'ToDoUser.user_id' => $user_id);
			$subconditions['AND'][] = array('ToDoSubUser.approved' => 1, 'ToDoSubUser.user_id' => $user_id);
		}
		if (isset($project_id) && !empty($project_id)) {
			$conditions['AND'][] = array('ToDo.project_id' => $project_id);
			$subconditions['AND'][] = array('ToDoSub.project_id' => $project_id);
		} else {
			$conditions['AND'][] = array('ToDo.project_id IS NULL');
			$subconditions['AND'][] = array('ToDoSub.project_id IS NULL');
		}

		$this->ToDo->unbindModel(array("hasMany" => array("ToDoUser")));
		$this->ToDo->bindModel(array("hasOne" => array("ToDoUser")));
		$countCompletedTodo = $this->ToDo->find("count", array(
			"contain" => array("ToDoUser", "ToDoUpload"),
			"group" => "ToDo.id",
			'conditions' => $conditions,
		)
		);

		$this->ToDoSub->unbindModel(array("hasMany" => array("ToDoSubUser")));
		$this->ToDoSub->bindModel(array("hasOne" => array("ToDoSubUser")));
		$countCompletedTodosub = $this->ToDoSub->find("count", array(
			"contain" => array("ToDoSubUser", "ToDoSubUpload"),
			"group" => "ToDoSub.id",
			'conditions' => $subconditions,
		)
		);

		$countCompleted = $countCompletedTodo + $countCompletedTodosub;
		return $countCompleted;
	}

	public function get_status($id = null, $status = null) {
		$status = isset($status) && !empty($status) && $status == 'main' ? "DoList" : "DoList";
		$this->$status = ClassRegistry::init($status);
		$user_id = $this->Session->read("Auth.User.id");
		$returndata = "N/A";
		$this->$status->recursive = 2;
		$this->$status->unbindModel(array("hasMany" => array($status . "User")));
		$this->$status->bindModel(array("hasOne" => array($status . "User")));
		$data = $this->$status->find("first", array("conditions" => array($status . 'User.user_id' => $user_id, $status . '.id' => $id)));
		//pr($data);
		if (isset($data[$status]['sign_off']) && $data[$status]['sign_off'] == 1) {
			$returndata = 'Completed';
		} else if (isset($data[$status]['end_date']) && $data[$status]['sign_off'] == 0 && !empty($data[$status]['end_date']) && strtotime($data[$status]['end_date']) < strtotime(date("Y-m-d")) && $data[$status . 'User']['approved'] != 2) {
			$returndata = 'Overdue';
		} else if (isset($data[$status]['start_date']) && !empty($data[$status]['start_date']) && strtotime($data[$status]['start_date']) <= strtotime(date("Y-m-d")) && strtotime($data[$status]['end_date']) >= strtotime(date("Y-m-d")) && $data[$status . 'User']['approved'] == 1) {
			$returndata = 'Progressing';
		} else if (isset($data[$status]['start_date']) && !empty($data[$status]['start_date']) && strtotime($data[$status]['start_date']) <= strtotime(date("Y-m-d")) && strtotime($data[$status]['end_date']) >= strtotime(date("Y-m-d")) && $data[$status . 'User']['approved'] == 0) {
			$returndata = 'Open';
		} else if (isset($data[$status]['start_date']) && !empty($data[$status]['end_date']) && !empty($data[$status]['start_date']) && strtotime($data[$status]['start_date']) > strtotime(date("Y-m-d")) && strtotime($data[$status]['end_date']) >= strtotime(date("Y-m-d"))) {
			$returndata = 'Not Started';
		} else if (isset($data[$status . 'User']['approved']) && $data[$status . 'User']['approved'] == 2) {
			$returndata = 'Declined';
		} else if (empty($data[$status]['start_date']) && empty($data[$status]['end_date'])) {
			$returndata = 'Not Started';
		}
		return $returndata;
	}

	public function get_todo_lists($user_id = null, $requested = false, $project_id = null, $day = null) {

		$user_id = (isset($user_id) && !empty($user_id)) ? $user_id : $this->Session->read("Auth.User.id");
		$ToDo = ClassRegistry::init('ToDo');
		$ToDoUser = ClassRegistry::init('ToDoUser');
		$ToDoSub = ClassRegistry::init('ToDoSub');
		$ToDo->recursive = 2;

		$ToDo->unbindModel(array("hasMany" => array("ToDoUser")));
		$ToDo->bindModel(array("hasOne" => array("ToDoUser")));
		$ToDo->bindModel(array("belongsTo" => array("Project")));

		$ToDoSub->unbindModel(array("hasMany" => array("ToDoSubUser")));
		$ToDoSub->bindModel(array("hasOne" => array(
			"ToDoSubUser" => array(
				"group" => "ToDoSub.id",
				'conditions' => array(
					"AND" => array(
						"OR" => array('ToDoSubUser.approved' => 1, 'ToDoSubUser.owner_id' => $user_id),
					),
					"OR" => array(
						'ToDoSubUser.owner_id' => $user_id,
						'ToDoSubUser.user_id' => $user_id,
					),
				),
			),
		),
		)
		);
		$conditions = $cond = $contain = null;
		if (isset($project_id) && !empty($project_id)) {
			$conditions['AND'][] = array('ToDo.project_id' => $project_id);
		} else {
			$conditions['AND'][] = array('ToDo.project_id IS NULL');
		}

		if (isset($day) && !empty($day)) {
			if ($day == 1) {
				$conditions['AND'][] = array('ToDo.start_date <=' => date('Y-m-d'));
				$conditions['AND'][] = array('ToDo.end_date >=' => date('Y-m-d'));
				$conditions['AND'][] = array('ToDo.sign_off !=' => 1);
			} else if ($day == 2) {
				$conditions['AND'][] = array('ToDo.start_date' => date('Y-m-d', strtotime("+1 day")));
				$conditions['AND'][] = array('ToDo.sign_off !=' => 1);
			} else if ($day == 3) {
				$conditions['AND'][] = array('ToDo.start_date >=' => date('Y-m-d', strtotime("+2 day")));
				$conditions['AND'][] = array('ToDo.sign_off !=' => 1);
			} else if ($day == 4) {
				$conditions['AND'][] = array('ToDo.end_date <' => date('Y-m-d'));
				$conditions['AND'][] = array('ToDo.sign_off !=' => 1);
			} else if ($day == 5) {
				$conditions['AND'][] = array('ToDo.start_date IS NULL');
				$conditions['AND'][] = array('ToDo.end_date IS NULL');
				$conditions['AND'][] = array('ToDo.sign_off !=' => 1);
			} else if ($day == 6) {
				$conditions['AND'][] = array('ToDo.sign_off' => 1);
			}
		}

		if (isset($requested) && !empty($requested) && $requested == true) {

			$contain = array("Project", "ToDoUser", "ToDoSub");
			$data = $ToDoUser->find("all", array(
				"conditions" => [
					'ToDoUser.user_id' => $user_id,
					'ToDoUser.owner_id !=' => $user_id,
					'ToDoUser.approved' => 1,
				],
				'fields' => 'ToDoUser.to_do_id',
			)
			);
			$to_do_id = Set::extract($data, '/ToDoUser/to_do_id');
			$conditions['AND'][] = ['ToDo.id' => $to_do_id];
			$data = $ToDo->find("all", array(
				"group" => "ToDo.id",
				'contain' => ["ToDoUser", "ToDoSub"],
				"conditions" => $conditions,
			)
			);
			// e($ToDo->_query());
		} else {
			if (isset($project_id) && !empty($project_id)) {
				$conditions['AND'][] = array('ToDo.project_id' => $project_id, 'ToDo.user_id' => $user_id);
			} else {
				$conditions['AND'][] = [
					'ToDo.user_id' => $user_id,
					'ToDo.project_id IS NULL',
				];
			}

			$data = $ToDo->find("all", array(
				"group" => "ToDo.id",
				'contain' => [
					"ToDoUser",
					"ToDoUpload",
					"ToDoComment",
					"ToDoSub" => [
						"ToDoSubUser",
						"ToDoSubUpload",
						"ToDoSubComment",
					],
				],
				"conditions" => $conditions,
			)
			);
			pr($conditions);
			// e($ToDo->_query());
		}

		//pr($data);
		return (isset($data) && !empty($data)) ? $data : false;
	}

	public function get_subtodo_lists($user_id = null, $requested = false, $project_id = null, $day = null) {

		$user_id = $this->Session->read("Auth.User.id");
		$ToDo = ClassRegistry::init('ToDo');
		$ToDoSub = ClassRegistry::init('ToDoSub');
		$ToDo->recursive = 1;

		$ToDoSub->unbindModel(array("hasMany" => array("ToDoSubUser")));
		$ToDoSub->bindModel(array("hasOne" => array("ToDoSubUser")));
		$ToDoSub->bindModel(array("belongsTo" => array("Project")));

		$conditions = $cond = $contain = null;
		if (isset($project_id) && !empty($project_id)) {
			$conditions['AND'][] = array('ToDoSub.project_id' => $project_id);
		} else {
			$conditions['AND'][] = array('ToDoSub.project_id IS NULL');
		}

		if (isset($day) && !empty($day)) {
			if ($day == 1) {
				$conditions['AND'][] = array('ToDoSub.start_date <=' => date('Y-m-d'));
				$conditions['AND'][] = array('ToDoSub.end_date >=' => date('Y-m-d'));
				$conditions['AND'][] = array('ToDoSub.sign_off !=' => 1);
			} else if ($day == 2) {
				$conditions['AND'][] = array('ToDoSub.start_date' => date('Y-m-d', strtotime("+1 day")));
				$conditions['AND'][] = array('ToDoSub.sign_off !=' => 1);
			} else if ($day == 3) {
				$conditions['AND'][] = array('ToDoSub.start_date >=' => date('Y-m-d', strtotime("+2 day")));
				$conditions['AND'][] = array('ToDoSub.sign_off !=' => 1);
			} else if ($day == 4) {
				$conditions['AND'][] = array('ToDoSub.end_date <' => date('Y-m-d'));
				$conditions['AND'][] = array('ToDoSub.sign_off !=' => 1);
			} else if ($day == 5) {
				$conditions['AND'][] = array('ToDoSub.start_date IS NULL');
				$conditions['AND'][] = array('ToDoSub.end_date IS NULL');
				$conditions['AND'][] = array('ToDoSub.sign_off !=' => 1);
			} else if ($day == 6) {
				$conditions['AND'][] = array('ToDoSub.sign_off' => 1);
			}
		}

		if (isset($requested) && !empty($requested) && $requested == true) {
			$conditions['AND'][] = [
				array(
					'ToDoSubUser.approved' => 1,
					'ToDoSubUser.owner_id !=' => $user_id,
					'ToDoSubUser.user_id' => $user_id,
				),
			];

			$contain = array("Project", "ToDoSubUser", "ToDoSubComment", "ToDoSubUpload");
			$data = $ToDoSub->find("all", array(
				"group" => "ToDoSub.id",
				"contain" => $contain,
				"group" => "ToDoSub.id",
				"conditions" => $conditions,
			)
			);
			//	pr($conditions);
		} else {
			if (isset($project_id) && !empty($project_id)) {
				$conditions['AND'][] = array('ToDoSub.project_id' => $project_id, 'ToDoSub.user_id' => $user_id);
			} else {
				$conditions['AND'][] = [
					'ToDoSub.user_id' => $user_id,
					'ToDoSub.project_id IS NULL',
				];
			}
			$data = $ToDoSub->find("all", array(
				"group" => "ToDoSub.id",
				'contain' => ["ToDoSubUser"],
				"conditions" => $conditions,
			)
			);
		}

		//e($ToDoSub->_query());
		return (isset($data) && !empty($data)) ? $data : false;
	}

	public function do_lists($project_id = null, $day = null) {

		$user_id = $this->Session->read("Auth.User.id");

		$this->_do_list->bindModel(array("belongsTo" => array("Project")));

		$conditions = $cond = $contain = null;

		if (isset($project_id) && !empty($project_id)) {
			$conditions['AND'][] = array('DoList.project_id' => $project_id);
		}

		if (isset($day) && !empty($day)) {
			if ($day == 1) {
				$conditions['AND'][] = array('DoList.start_date <=' => date('Y-m-d'));
				$conditions['AND'][] = array('DoList.end_date >=' => date('Y-m-d'));
				$conditions['AND'][] = array('DoList.sign_off !=' => 1);
			} else if ($day == 2) {
				$conditions['AND'][] = array('DoList.start_date' => date('Y-m-d', strtotime("+1 day")));
				$conditions['AND'][] = array('DoList.sign_off !=' => 1);
			} else if ($day == 3) {
				$conditions['AND'][] = array('DoList.start_date >=' => date('Y-m-d', strtotime("+2 day")));
				$conditions['AND'][] = array('DoList.sign_off !=' => 1);
			} else if ($day == 4) {
				$conditions['AND'][] = array('DoList.end_date <' => date('Y-m-d'));
				$conditions['AND'][] = array('DoList.sign_off !=' => 1);
			} else if ($day == 5) {
				$conditions['AND'][] = array('DoList.start_date IS NULL');
				$conditions['AND'][] = array('DoList.end_date IS NULL');
				$conditions['AND'][] = array('DoList.sign_off !=' => 1);
			} else if ($day == 6) {
				$conditions['AND'][] = array('DoList.sign_off' => 1);
			}
		}

		$conditions['AND'][] = [
			'DoList.user_id' => $user_id,
		];

		$data = $this->_do_list->find("all", array(
			"conditions" => $conditions,
		)
		);

		return (isset($data) && !empty($data)) ? $data : false;
	}

	public function rec_do_lists($project_id = null, $day = null) {

		$user_id = $this->Session->read("Auth.User.id");

		$this->_do_list->bindModel(array("belongsTo" => array("Project")));

		$conditions = $cond = $contain = null;

		if (isset($project_id) && !empty($project_id)) {
			$conditions['AND'][] = array('DoList.project_id' => $project_id);
		}

		if (isset($day) && !empty($day)) {
			if ($day == 1) {
				$conditions['AND'][] = array('DoList.start_date <=' => date('Y-m-d'));
				$conditions['AND'][] = array('DoList.end_date >=' => date('Y-m-d'));
				$conditions['AND'][] = array('DoList.sign_off !=' => 1);
			} else if ($day == 2) {
				$conditions['AND'][] = array('DoList.start_date' => date('Y-m-d', strtotime("+1 day")));
				$conditions['AND'][] = array('DoList.sign_off !=' => 1);
			} else if ($day == 3) {
				$conditions['AND'][] = array('DoList.start_date >=' => date('Y-m-d', strtotime("+2 day")));
				$conditions['AND'][] = array('DoList.sign_off !=' => 1);
			} else if ($day == 4) {
				$conditions['AND'][] = array('DoList.end_date <' => date('Y-m-d'));
				$conditions['AND'][] = array('DoList.sign_off !=' => 1);
			} else if ($day == 5) {
				$conditions['AND'][] = array('DoList.start_date IS NULL');
				$conditions['AND'][] = array('DoList.end_date IS NULL');
				$conditions['AND'][] = array('DoList.sign_off !=' => 1);
			} else if ($day == 6) {
				$conditions['AND'][] = array('DoList.sign_off' => 1);
			}
		}

		$conditions['AND'][] = [
			'DoList.user_id' => $user_id,
		];

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
		}
		pr($user_data, 1);

		$data = $this->_do_list->find("all", array(
			"conditions" => $conditions,
		)
		);

		return (isset($data) && !empty($data)) ? $data : false;
	}

	public function get_do_list_detail($do_list_id = null) {
		if (!isset($do_list_id) || empty($do_list_id)) {
			return null;
		}

		return $this->_do_list->findById($do_list_id);

	}

}
