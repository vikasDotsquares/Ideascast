<?php

App::uses('AppModel', 'Model');

/**
 * ProjectDomain Model
 *
 */
class ProjectDomain extends AppModel {

	public $belongsTo = array(
		'Project' => array(
			'className' => 'Project',
			'foreignKey' => 'project_id',
			'dependent' => true,
		),
		'KnowledgeDomain' => array(
			'className' => 'KnowledgeDomain',
			'foreignKey' => 'domain_id',
			'dependent' => true,
		),
	);

}
