<?php
App::uses('AppModel', 'Model');

class ProjectPermission extends AppModel {

    var $name = 'ProjectPermission';

    var $belongsTo = array (
			'User',
			'UserProject',
			'Parent' => array(
				'className' => 'ProjectPermission',
				'foreignKey' => 'parent_id'
			),
		);
		
	public $hasMany = array(
		'Children' => array(
			'className' => 'ProjectPermission',
			'foreignKey' => 'parent_id'
		)
    );	
		

	var $validate = array( 
		'user_project_id' => array(
			'required' => array(
				'rule' => array(EMPTY_MSG),
				'message' => 'Reference with a project is required.'
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