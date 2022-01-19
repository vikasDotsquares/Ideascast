<?php

App::uses('AppModel', 'Model');
App::import("Model", "UserProject");
App::uses('Controller/Component', 'Auth');
App::uses('Sanitize', 'Utility');

/**
 * Project Model
 *
 */
class Project extends AppModel {

	var $name = 'Project';
	var $hasMany = ['ProjectWorkspace', 'UserProject', 'ProjectSkill', 'ElementPermission','ProjectElementType'];
	var $belongsTo = array(
			'Category',
	);

	/*
		      var $belongsTo = array(
		      'Category' => array(
		      'className' => 'Category',
		      // 'foreignKey' => 'category_id',
		      'fields' => array('Category.id as cat_id', 'Category.title as cat_title')
	*/
	public $validate = array(
		/*'category_id' => array(
			'required' => array(
				'rule' => array(EMPTY_MSG),
				'message' => 'Category is required',
			),
		), */
		'aligned_id' => array(
			'required' => array(
				'rule' => array(EMPTY_MSG),
				'message' => 'Type is required',
			),
		),
		'currency_id' => array(
			'required' => array(
				'rule' => array(EMPTY_MSG),
				'message' => 'Currency is required',
			),
		),
		'title' => array(
			'required' => array(
				'rule' => array(EMPTY_MSG),
				'message' => 'Title is required',
			),
			/* 'length' => array(
				'rule' => array('maxLength', 50),
				'message' => 'Title must be no larger than 50 characters long.',
			), */
		),
		'objective' => array(
			'required' => array(
				'rule' => array(EMPTY_MSG),
				'message' => 'Outcome is required',
			),
			/* 'length' => array(
				          'rule' => array('maxLength', 250),
				          'message' => 'Objective must be no larger than 250 characters long.'
			*/
		),
		'start_date' => array(
			'required' => array(
				'rule' => array(EMPTY_MSG),
				'message' => 'Start Date is required',
			),
		),
		'end_date' => array(
			'required' => array(
				'rule' => array(EMPTY_MSG),
				'message' => 'End Date is required',
			),
		),
		'description' => array(
			'required' => array(
				'rule' => array(EMPTY_MSG),
				'message' => 'Description is required',
			),
			/* 'length' => array(
				          'rule' => array('maxLength', 500),
				          'message' => 'Description must be no larger than 500 characters long.'
			*/
		),
	);

	/*

		      public function beforeFind($queryData) {
		      parent::beforeFind($queryData);

		      //
		      $queryData['joins'] = array(
		      array(
		      'table' => 'categories',
		      'alias' => 'Categorys',
		      'type' => 'INNER',
		      'conditions' => 'Categorys.id = Product.category_id'
		      )
		      );
		      $queryData = array_merge(['limit' => -1], $queryData['joins']);
		      // $queryData = array_merge(['fields' => ['Categorys.id as cid']], $queryData);
		      // $queryData = array_merge($queryData, ['Product.category_id' = 'Category']);
		      // pr($queryData, 1);
		      return $queryData;
		      }
	*/

	public function afterFind($results, $primary = false) {
		App::uses('CakeTime', 'Utility');

			foreach ($results as $key => $val) {

				if (isset($val[$this->alias]['title'])) {
					//$results[$key][$this->alias]['title'] = html_entity_decode(html_entity_decode($val[$this->alias]['title'] ,ENT_QUOTES, "UTF-8"));
				}
				if (isset($val[$this->alias]['objective'])) {
					$results[$key][$this->alias]['objective'] = html_entity_decode(html_entity_decode($val[$this->alias]['objective'], ENT_QUOTES, "UTF-8"));
				}
				if (isset($val[$this->alias]['description'])) {
					$results[$key][$this->alias]['description'] = html_entity_decode(html_entity_decode($val[$this->alias]['description'], ENT_QUOTES, "UTF-8"));
				}

			}

			foreach ($results as $key => $val) {
				if (isset($val[$this->alias]['created_at'])) {
					$results[$key][$this->alias]['created_at'] = CakeTime::format(ADMIN_DATE_FORMAT, $val[$this->alias]['created_at'], null, TIME_ZONE);
				}
			}

			return $results;

		/* } else {

			foreach ($results as $key => $val) {
				if (isset($val[$this->alias]['created_at'])) {
					$results[$key][$this->alias]['created_at'] = CakeTime::format(ADMIN_DATE_FORMAT, $val[$this->alias]['created_at'], null, TIME_ZONE);
				}
			}
			return $results;
		} */
	}

	public function beforeSave($options = array()) {

		$user_id = CakeSession::read("Auth.User.id");

		if (isset($this->data[$this->alias]["id"]) && !empty($this->data[$this->alias]["id"])) {

			// Save UserProject each time when a project is updated
			// This will help to set last updated project on top while getting summary page
			$user_project = new UserProject();
			$project_id = $this->data[$this->alias]["id"];

/* 			$user_projects = $user_project->find("all", [
				'conditions' => [
					'UserProject.project_id' => $project_id,
					'UserProject.user_id' => $user_id,
				],
				'fields' => [
					'UserProject.id',
					'UserProject.project_id',
				],
			]);

			if (isset($user_projects) && !empty($user_projects)) {
				$data = null;
				$user_project_ids = Set::extract($user_projects, '/UserProject/id');
				foreach ($user_project_ids as $key => $val) {
					$data[] = array('UserProject' => array('id' => $val, 'modified' => date('Y-m-d h:i:s')));
				}
				if ($user_project->saveMany($data, array('deep' => true))) {

				}
			} */
		}
		foreach ($this->data[$this->alias] as $k => $v) {
			//$c = preg_replace('@<script[^>]*?.*?</script>@siu', '', $v);
			//$c = preg_replace('@<script>.*?@siu', '', $c);
			if(is_string($v)){
			//$c = htmlspecialchars( $v);
			//$this->data[$this->alias][$k] = $c;
			}

		}

		//$this->data = Sanitize::clean($this->data, array('encode' => true));
		//pr($this->data); die;
		return true;
	}

	public function afterSave($created = false, $options = array()) {

		if (!isset($this->data[$this->alias]["status"])) {
			$this->data[$this->alias]["status"] = 0;
		}

		return true;
	}

}
