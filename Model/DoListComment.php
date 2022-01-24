<?php

App::uses('AppModel', 'Model');

/**
 * DoListComment Model
 *
 */
class DoListComment extends AppModel {
	
    public $belongsTo = array(
        'DoList' => array(
            'className' => 'DoList',
            'foreignKey' => 'do_list_id',
            'dependent' => true
        ),
        'User' => array(
            'className' => 'User',
            'foreignKey' => 'user_id',
            'dependent' => true
        )
    );
	
    public $hasMany = array(
        'DoListCommentUpload' => array(
            'className' => 'DoListCommentUpload',
            'foreignKey' => 'do_list_comment_id',
            'dependent' => true
        ),
        'DoListCommentLike' => array(
            'className' => 'DoListCommentLike',
            'foreignKey' => 'do_list_comment_id',
            'dependent' => true
        )
    );
	
    public $validate = array(
        'comments' => array(
            'required' => array(
                'rule' => array(EMPTY_MSG),
                'message' => 'Title is required'
            ),
            'maxLength' => array(
                'rule' => array('maxLength', 1000),
                'message' => 'Title must be no larger than 1000 characters long.'
            )
        ),
        'do_list_id' => array(
            'required' => array(
                'rule' => array(EMPTY_MSG),
                'message' => 'Title is required'
            )
        ),
        'user_id' => array(
            'required' => array(
                'rule' => array(EMPTY_MSG),
                'message' => 'Title is required'
            )
        ),
        
    );
	

}
