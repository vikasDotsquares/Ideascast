<?php
App::uses('AppModel', 'Model');

class WorkspacePermission extends AppModel {

    var $name = 'WorkspacePermission';
	
    var $belongsTo = array (
			'User',
			'UserProject',
			'ProjectWorkspace',
		);

	var $validate = array(
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
		),
		'user_id' => array(
			'required' => array(
				'rule' => array(EMPTY_MSG),
				'message' => 'Reference with a user is required.'
			)
		)
	); 
	
	
}