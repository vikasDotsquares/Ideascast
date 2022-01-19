<?php
App::uses('AppModel', 'Model');

class ProjectPropagate extends AppModel {

    var $name = 'ProjectPropagate';

    var $belongsTo = array ( 
			'UserProject',
			'Parent' => array(
				'className' => 'ProjectPropagate',
				'foreignKey' => 'parent_id'
			),
			// Owner User
			'ShareBy' => array(
				'className' => 'User',
				'foreignKey' => 'share_by_id'
			), 
			// Share with User
			'ShareFor' => array(
				'className' => 'User',
				'foreignKey' => 'share_for_id'
			),
		);
		
	public $hasMany = array(
		'Children' => array(
			'className' => 'ProjectPropagate',
			'foreignKey' => 'parent_id'
		)
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
		'user_project_id' => array(
			'required' => array(
				'rule' => array(EMPTY_MSG),
				'message' => 'Reference with a project is required.'
			)
		)
	); 
}