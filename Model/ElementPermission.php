<?php
App::uses('AppModel', 'Model');

class ElementPermission extends AppModel {

    var $name = 'ElementPermission';

    var $belongsTo = array (
			'Element'  	=> [ 'dependent' => true ],
			'Workspace',
			'Project',
			'User'
		);

	var $validate = array(
		'element_id' => array(
			'required' => array(
				'rule' => array(EMPTY_MSG),
				'message' => 'Reference with an element is required.'
			)
		),
		'project_id' => array(
			'required' => array(
				'rule' => array(EMPTY_MSG),
				'message' => 'Reference with a project is required.'
			)
		),
		'workspace_id' => array(
			'required' => array(
				'rule' => array(EMPTY_MSG),
				'message' => 'Reference with a workspace is required.'
			)
		),
		'user_id' => array(
			'required' => array(
				'rule' => array(EMPTY_MSG),
				'message' => 'Reference with a user is required.'
			)
		)
	); 
}