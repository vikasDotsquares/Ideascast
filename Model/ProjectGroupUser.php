<?php
App::uses('AppModel', 'Model');
/**
 * GroupUsers Model
 *
 */

class ProjectGroupUser extends AppModel {
	
	var $belongsTo = array(
		'ProjectGroup' => [ 'dependent' => true ],
		'User','UserProject' 
	);
	 
    public $validate = array(
        'project_group_id' => array(
            'required' => array(
                'rule' => array(EMPTY_MSG),
                'message' => 'Project group id is required'
            ),
        ),
        'user_id' => array(
            'required' => array(
                'rule' => array(EMPTY_MSG),
                'message' => 'User id is required'
            ),
        ),
        'user_project_id' => array(
            'required' => array(
                'rule' => array(EMPTY_MSG),
                'message' => 'User project id is required'
            ),
        ),
    );
 
    
}