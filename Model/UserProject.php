<?php
App::uses('AppModel', 'Model');
/**
 * UserProject Model
 *
 */
class UserProject extends AppModel {

	var $name = 'UserProject';

	var $belongsTo = array('Project', 'User');

	var $hasMany = [
		'ProjectPermission' => [
			'className' => 'ProjectPermission',
			'dependent' => true,
		],

	];

	public function beforeFind($queryData = null) {
		parent::beforeFind($queryData);

		$group = [];

		if (isset($queryData['group']) && !empty($queryData['group'])) {
			$group = $queryData['group'];
		}
		if (isset($group) && is_array($group)) {
			$queryData['group'] = array_merge($group,
				[
					'UserProject.project_id',
				]
			);
		}

		return $queryData;

	}

}