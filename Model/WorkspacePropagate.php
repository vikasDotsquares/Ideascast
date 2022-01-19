<?php
App::uses('AppModel', 'Model');

class WorkspacePropagate extends AppModel {

    var $name = 'WorkspacePropagate';
	
    var $belongsTo = array ( 
			'UserProject',
			'ProjectWorkspace',
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
		),
		'project_workspace_id' => array(
			'required' => array(
				'rule' => array(EMPTY_MSG),
				'message' => 'Reference with a workspace is required.'
			)
		)
	); 
	
	
}