<?php

App::uses('AppModel', 'Model');

/**
 * WorkspaceComment Model
 *
 */
class WorkspaceComment extends AppModel {
	
    public $belongsTo = array(
        'Workspace' => array(
            'className' => 'Workspace',
            'foreignKey' => 'workspace_id',
            'dependent' => true
        ),
        'User' => array(
            'className' => 'User',
            'foreignKey' => 'user_id',
            'dependent' => true
        )
    );
	
    public $hasMany = array( 
		'WorkspaceCommentLike' => array(
			'className' => 'WorkspaceCommentLike',
			'foreignKey' => 'workspace_comment_id',
			'dependent' => true
		)
    );
	
	
    public $validate = array(
        'comments' => array(
            'required' => array(
                'rule' => array(EMPTY_MSG),
                'message' => 'Comment is required'
            ),
            'maxLength' => array(
                'rule' => array('maxLength', 1502),
                'message' => 'Comment must be no larger than 1000 characters long.'
            )
        ),
        'workspace_id' => array(
            'required' => array(
                'rule' => array(EMPTY_MSG),
                'message' => 'Workspace is required'
            )
        ),
        'user_id' => array(
            'required' => array(
                'rule' => array(EMPTY_MSG),
                'message' => 'User is required'
            )
        ),
        
    );
	

}
