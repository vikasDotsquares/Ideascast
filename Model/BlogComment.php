<?php

App::uses('AppModel', 'Model');

class BlogComment extends AppModel {

    public $belongsTo = array(
        'Blog' => array(
            'className' => 'Blog'
        )
    );
     public $hasMany = array(
        'BlogDocument' => array(
            'className' => 'BlogDocument',
            'foreignKey' => 'blog_comment_id',
            'dependent' => true
        ),
    );
    public $validate = array(
        'description' => array(
            'required' => array(
                'rule' => array(EMPTY_MSG),
                'message' => 'Description is required'
            ),
            'length' => array(
                'rule' => array('maxLength', 1000),
                'message' => 'Description must be no larger than 1000 characters.'
            )
        ),
    );

}
