<?php
App::uses('AppModel', 'Model');

class ElementPropagate extends AppModel {

    var $name = 'ElementPropagate';

    var $belongsTo = array (
			'Element'  	=> [ 'dependent' => true ],
			'Workspace',
			'Project', 
			'ShareBy' => array(
				'className' => 'User',
				'foreignKey' => 'share_by_id'
			), 
			'ShareFor' => array(
				'className' => 'User',
				'foreignKey' => 'share_for_id'
			),
		);

	var $validate = array(
		'share_by_id' => array(
			'required' => array(
				'rule' => array(EMPTY_MSG),
				'message' => 'Owner user is required.'
			)
		),
		'share_for_id' => array(
			'required' => array(
				'rule' => array(EMPTY_MSG),
				'message' => 'Share user is required.'
			)
		),
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
	); 
}