<?php
App::uses('AppModel', 'Model');

class BlogDocument extends AppModel {
	
	public $belongsTo = array(
		'Blog' => array(
			'className' => 'Blog'
		)
    );

     
	
	public $validate = array(
		/* 'title' => array(
            'required' => array(
                'rule' => array('notEmpty'),
                'message' => 'Title is required'
            ), 
			'length' => array(
				'rule' => array('maxLength', 100),
				'message' => 'Title must be no larger than 100 characters.'
			)
        ), */
		'document_name' => array(
            'required' => array(
                'rule' => array(EMPTY_MSG),
                'message' => 'Document is required'
            ) 
        )
	);
	
}