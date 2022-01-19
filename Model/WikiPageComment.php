<?php

App::uses('AppModel', 'Model');

class WikiPageComment extends AppModel {
    
    public $useTable = "wiki_page_comments";
    
    public $validate = array(
        'description' => array(
            'required' => array(
                'rule' => array(EMPTY_MSG),
                'message' => 'Wiki description is required'
            ),
            'length' => array(
                'rule' => array('maxLength', 1000),
                'message' => 'Description must be no larger than 1000 characters.'
            )
        ),
    );
    public $hasOne = array(
    'WikiPage' => array(
        'className'     => 'WikiPage',
        'foreignKey'    => false,
        'conditions' => array('WikiPageComment.wiki_page_id = WikiPage.id')  
    ));
    public $belongsTo = array(
        'Project' => array(
            'className' => 'Project'
        )
    );
     public $hasMany = array(
        'WikiPageCommentDocument' => array(
            'className' => 'WikiPageCommentDocument',
            'foreignKey' => 'wiki_page_comment_id',
            'dependent' => true
        ),
    );

}
