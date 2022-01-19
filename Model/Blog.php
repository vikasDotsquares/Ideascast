<?php
App::uses('AppModel', 'Model');

class Blog extends AppModel {
	
	/* public $belongsTo = array(
		'Project' => array(
			'className' => 'Project'
		)
    ); */

    public $hasMany = array(
		'BlogComment' => array(
			'className' => 'BlogComment'
		),
		'BlogDocument' => array(
			'className' => 'BlogDocument'
		)
    );
	
	public $validate = array(
		'title' => array(
            'required' => array(
                'rule' => array(EMPTY_MSG),
                'message' => 'Blog title is required'
            ), 
			'length' => array(
				'rule' => array('maxLength', 100),
				'message' => 'Title must be no larger than 100 characters.'
			)
        ),
		'description' => array(
            'required' => array(
                'rule' => array(EMPTY_MSG),
                'message' => 'Blog description is required'
            ) 
        ),
	);
	
}