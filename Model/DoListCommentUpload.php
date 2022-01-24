<?php

App::uses('AppModel', 'Model');

/**
 * DoListCommentUpload Model
 *
 */
class DoListCommentUpload extends AppModel {
	
    public $validate = array(
        'file_name' => array(
            'required' => array(
                'rule' => array(EMPTY_MSG),
                'message' => 'File is required'
            )
        ),
        'do_list_comment_id' => array(
            'required' => array(
                'rule' => array(EMPTY_MSG),
                'message' => 'Title is required'
            )
        )
        
    );
	

}
