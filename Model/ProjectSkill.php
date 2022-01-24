<?php

App::uses('AppModel', 'Model');

/**
 * ProjectSkill Model
 *
 */
class ProjectSkill extends AppModel {

	public $belongsTo = array(
		'Project' => array(
			'className' => 'Project',
			'foreignKey' => 'project_id',
			'dependent' => true,
		),
		'Skill' => array(
			'className' => 'Skill',
			'foreignKey' => 'skill_id',
			'dependent' => true,
		),
	);

}
