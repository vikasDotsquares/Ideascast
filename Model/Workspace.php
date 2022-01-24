<?php
App::uses('AppModel', 'Model');
App::uses('Sanitize', 'Utility');
/**
 * Workspace Model
 *
 */
class Workspace extends AppModel {

	var $name = 'Workspace';
	var $hasMany = [
		'ProjectWorkspace' => [
			'dependent' => true,
		],
		'Area' => [
			'dependent' => true,
		],
		'ElementPermission',
	];

	var $belongsTo = array('Template');

	public $validate = array(
		'title' => array(
			'required' => array(
				'rule' => array(EMPTY_MSG),
				'message' => 'Title is required',
			),
		),
		 'description' => array(
			'required' => array(
				'rule' => array(EMPTY_MSG),
				'message' => 'Description is required',
			),

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
	);

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
	}
	public function beforeSave($created = false, $options = array()) {

		if (!isset($this->data)) {
			foreach ($this->data[$this->alias] as $k => $v) {
			}
		}

		return true;
	}

}