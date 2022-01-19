<?php

App::uses('AppModel', 'Model');

/**
 * ProjectSubject Model
 *
 */
class ProjectSubject extends AppModel {

	public $belongsTo = array(
		'Project' => array(
			'className' => 'Project',
			'foreignKey' => 'project_id',
			'dependent' => true,
		),
		'Subject' => array(
			'className' => 'Subject',
			'foreignKey' => 'subject_id',
			'dependent' => true,
		),
	);

}
