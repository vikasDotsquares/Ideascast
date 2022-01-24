<?php

App::uses('AppModel', 'Model');

class WikiPage extends AppModel {
    
    public $useTable = "wiki_pages";
    
    public $validate = array(
        'title' => array(
            'required' => array(
                'rule' => array(EMPTY_MSG),
                'message' => 'Wiki title is required'
            ),
            'length' => array(
                'rule' => array('maxLength', 100),
                'message' => 'Title must be no larger than 50 characters.'
            )
        ),
        'description' => array(
            'required' => array(
                'rule' => array(EMPTY_MSG),
                'message' => 'Wiki description is required'
            ),
            'length' => array(
                'rule' => array('maxLength', 10000),
                'message' => 'Description must be no larger than 500 characters.'
            )
        ),
    );
    public $belongsTo = array(
        'Project' => array(
            'className' => 'Project'
        )
    );

}
