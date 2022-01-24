<?php

App::uses('AppModel', 'Model');

/**
 * ProjectComment Model
 *
 */
class ProjectNote extends AppModel {
	
    public $belongsTo = array(
        'Project' => array(
            'className' => 'Project',
            'foreignKey' => 'project_id',
            'dependent' => true
        ),
        'User' => array(
            'className' => 'User',
            'foreignKey' => 'user_id',
            'dependent' => true
        )
    );
	
	
    public $validate = array(
        'note' => array(
            'required' => array(
                'rule' => array(EMPTY_MSG),
                'message' => 'Note is required'
            ),
            /* 'maxLength' => array(
                'rule' => array('maxLength', 250),
                'message' => 'Comment must be no larger than 250 characters long.'
            ) */
        ),
        'project_id' => array(
            'required' => array(
                'rule' => array(EMPTY_MSG),
                'message' => 'Project is required'
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
