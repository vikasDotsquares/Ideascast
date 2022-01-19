<?php
App::uses('AppModel', 'Model');
App::uses('Sanitize', 'Utility');

class Element extends AppModel {

	var $name = 'Element';
  	var $belongsTo = array(
		'Areas' => ['className' => 'Area',
			'foreignKey' => 'area_id',
			'dependent' => true],
		'Area' => [
			'dependent' => true],
	);
	var $hasMany = array(
		'Links' => [
			'className' => 'ElementLink',
			'foreignKey' => 'element_id',
			'dependent' => true,
		],
		'Documents' => [
			'className' => 'ElementDocument',
			'foreignKey' => 'element_id',
			'dependent' => true,
		],
		'Notes' => [
			'className' => 'ElementNote',
			'foreignKey' => 'element_id',
			'dependent' => true,
		],
		'Mindmaps' => [
			'className' => 'ElementMindmap',
			'foreignKey' => 'element_id',
			'dependent' => false,
		],

		'Permissions' => [
			'className' => 'ElementPermission',
			'foreignKey' => 'element_id',
			'dependent' => false,
		],
	);
	// var $hasAndBelongsToMany = array(
	// 'Decision' => array(
	// 'className' => 'Decision',
	// 'joinTable' => 'element_decisions',
	// 'foreignKey' => 'element_id',
	// 'associationForeignKey' => 'decision_id'
	// )
	// );

	var $hasOne = [
		'ElementDecision' => [
			'className' => 'ElementDecision',
			'dependent' => true,
		],
		'ElementFeedback' => [
			'className' => 'ElementFeedback',
			'dependent' => true,
		],
	];
	var $validate = array(
		'title' => array(
			'required' => array(
				'rule' => array(EMPTY_MSG),
				'message' => 'Title is required.',
			),
			'minlength' => array(
				'rule' => array('minlength', 5),
				'message' => 'Minimum length is 5 chars.',
			),
			/* 			'maxlength' => array(
				  'rule' => array('maxlength', 100),
				  'message' => 'Maximum length is 100 chars.',
			*/
		),
		'description' => array(
			'required' => array(
				'rule' => array(EMPTY_MSG),
				'message' => 'Description is required.',
			),
			/* 			'maxlength' => array(
				  'rule' => array('maxlength', 500),
				  'message' => 'Maximum length is 500 chars.',
			*/
		),
		'comments' => array(
			/* 			'maxlength' => array(
				  'rule' => array('maxlength', 2000),
				  'message' => 'Maximum length is 2000 chars.',
			*/
		),
		'area_id' => array(
			'required' => array(
				'rule' => array(EMPTY_MSG),
				'message' => 'Area is required.',
			),
		),
		'date_constraints' => array(
			'rule' => array('boolean'),
		),
		'start_date' => array(
			'required' => array(
				'rule' => array('isDateConstraintSet'),
				'message' => 'Start date is required.',
			),
		),
		'end_date' => array(
			'required' => array(
				'rule' => array('isDateConstraintSet'),
				'message' => 'End date is required.',
			),
		),
	);

	public function isOwnedBy($post, $user) {
		// return $this->field('id', array('id' => $post, 'user_id' => $user)) !== false;
		return true;
	}

	function validateDates($field) {
		$passed = true;
		if(array_key_exists('start_date', $field)) {
			if ($this->data[$this->alias]['check_date'] > 0 and (!isset($this->data[$this->alias]['start_date']) or empty($this->data[$this->alias]['start_date']))) {
				$passed = false;
			} else {
				$passed = true;
			}
		}
		if(array_key_exists('end_date', $field)) {
			if ($this->data[$this->alias]['check_date'] > 0 and (!isset($this->data[$this->alias]['end_date']) or empty($this->data[$this->alias]['end_date']))) {
				$passed = false;
			} else {
				$passed = true;
			}
		}
		return $passed;
	}

	function isDateConstraintSet($field) {

		$passed = true;

		switch (true) {

		case array_key_exists('start_date', $field):
			if ($this->data[$this->alias]['date_constraints'] > 0 and (!isset($this->data[$this->alias]['start_date']) or empty($this->data[$this->alias]['start_date']))) {
				$passed = false;
			} else {
				$passed = true;
			}

			break;

		case array_key_exists('end_date', $field):
			if ($this->data[$this->alias]['date_constraints'] > 0 and (!isset($this->data[$this->alias]['end_date']) or empty($this->data[$this->alias]['end_date']))) {
				$passed = false;
			} else {
				$passed = true;
			}

			break;
		}

		return $passed;
	}

	/* public function beforeSave($options = array()) {

		  if (isset($this->data[$this->alias]["sort_order"]) && !empty($this->data[$this->alias]["sort_order"]) ) {
		  $this->data[$this->alias]['sort_order'] = $this->setMaxOrderBeforeSave();
		  }
		  else{
		  $this->data[$this->alias]['sort_order'] = $this->setMaxOrderBeforeSave();
		  }

		  return true; //this is required, otherwise validation will always fail
	*/

	public function setMaxOrderBeforeSave() {
		$detail = null;
		$max_sort = 0;
		if (isset($this->data[$this->alias]["area_id"]) && !empty($this->data[$this->alias]["area_id"])) {
			$detail = $this->find('first', array(
				'conditions' => array($this->alias . '.area_id' => $this->data[$this->alias]['area_id']),
				'fields' => array('MAX(' . $this->alias . '.sort_order) AS max_sort'),
			)
			);
			if (isset($detail) && !empty($detail)) {
				if (isset($detail[0]['max_sort'])) {
					$max_sort = (int) ($detail[0]['max_sort'] + 1);
				}
			}
		}

		return ($max_sort > 0) ? $max_sort : 0;
	}

	public function getStandby() {
		$detail = null;
		$max_sort = 0;
		if (isset($this->data[$this->alias]["area_id"]) && !empty($this->data[$this->alias]["area_id"])) {
			$detail = $this->find('first', array(
				'conditions' => array($this->alias . '.area_id' => $this->data[$this->alias]['area_id']),
				'fields' => array('MAX(' . $this->alias . '.sort_order) AS max_sort'),
			)
			);
			if (isset($detail) && !empty($detail)) {
				if (isset($detail[0]['max_sort'])) {
					$max_sort = (int) ($detail[0]['max_sort'] + 1);
				}
			}
		}

		return ($max_sort > 0) ? $max_sort : 0;
	}

	/*
		 * Get Project id of passed element id
		 * param#1: $id - Element.id
		 * param#2: $id_fields - if false; return whole array of project
	*/
	public function getProject($id, $id_fields = true) {

		if (!isset($id) || empty($id)) {
		//	return false;
		}

		ClassRegistry::init('Project');
		ClassRegistry::init('ProjectWorkspace');
		ClassRegistry::init('Area');
		ClassRegistry::init('Workspace');
		$project = new Project();
		$ws = new ProjectWorkspace();
		$area = new Area();
		$wsp = new Workspace();

		$project_data = $project_detail = null;

		$model = $this; // Element Class

		if ((isset($model->belongsTo) && !empty($model->belongsTo)) && (isset($model->belongsTo['Areas']) && !empty($model->belongsTo['Areas']))) {

			$this->id = $id;
			$this->saveField('modified', date('Y-m-d H:i:s'));

			// Get area of element
			$area_id = $model->find('first', [
				'conditions' => ['Element.id' => $id], 'fields' => ['Element.area_id'], 'recursive' => -1,
			]);

			if (isset($area_id) && !empty($area_id)) {
				// Get workspace of the area
				$ws_id = $area->find('first', [
					'conditions' => ['Area.id' => $area_id['Element']['area_id']],
					'fields' => ['Area.workspace_id'], 'recursive' => -1,
				]);

				//$dataW = array();
				$dataW['Workspace']['id'] = $ws_id['Area']['workspace_id'];
				$dataW['Workspace']['modified'] = date('Y-m-d H:i:s');

				ClassRegistry::init('Workspace')->save($dataW);
				//pr($ws_id);

				if (isset($ws_id) && !empty($ws_id)) {
					// Get project id of that workspace
					$prj_id = $ws->find('first', [
						'conditions' => ['ProjectWorkspace.workspace_id' => $ws_id['Area']['workspace_id']],
						'fields' => ['ProjectWorkspace.project_id'], 'recursive' => -1,
					]);

					if (!empty($prj_id) && is_array($prj_id)) {

						$project_id = $prj_id['ProjectWorkspace']['project_id'];
						// Get project detail
						$project_data = $project->find('first', ['conditions' => ['Project.id' => $project_id], 'recursive' => -1]);

						if (isset($project_data) && !empty($project_data)) {
							if (isset($id_fields) && !empty($id_fields)) {
								$project_detail = $project_data['Project']['id'];
							} else {
								$project_detail = $project_data['Project'];
							}
						}
					}
				}
			}
		}

		return (!empty($project_detail)) ? $project_detail : false;
	}

	/*public function beforeSave($created = false, $options = array()) {

		if (!isset($this->data)) {
			$this->data = Sanitize::clean($this->data, array('encode' => true));
		}

		return true;
	}*/


	public function afterFind($results, $primary = false) {
		App::uses('CakeTime', 'Utility');

			foreach ($results as $key => $val) {

				if (isset($val[$this->alias]['title'])) {
					$results[$key][$this->alias]['title'] = html_entity_decode(html_entity_decode($val[$this->alias]['title'] ,ENT_QUOTES, "UTF-8"));
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

	public function beforeSave($created = false, $options = array()) {

		if (!isset($this->data)) {
			/*$this->data = Sanitize::clean($this->data, array('encode' => true));*/
			foreach ($this->data[$this->alias] as $k => $v) {
				//$c = preg_replace('@<script[^>]*?.*?</script>@siu', '', $v);
				//$c = preg_replace('@<script>.*?@siu', '', $c);
				$c = htmlspecialchars( $v);
				$this->data[$this->alias][$k] = $c;
			}
		}

		return true;
	}

}
