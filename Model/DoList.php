<?php

App::uses('AppModel', 'Model');

/**
 * DoList Model
 *
 */
class DoList extends AppModel { 
	
    public $hasMany = array(
        'DoListUser' => array(
            'className' => 'DoListUser',
            'foreignKey' => 'do_list_id',
            'dependent' => true
        ),
        'DoListUpload' => array(
            'className' => 'DoListUpload',
            'foreignKey' => 'do_list_id',
            'dependent' => true
        ),
        'DoListComment' => array(
            'className' => 'DoListComment',
            'foreignKey' => 'do_list_id',
            'dependent' => true
        ),
		'Children'=>array(
			'className'=>'DoList',
			'foreignKey'=>'parent_id'
		)
    );
	
	public $belongsTo = array(
		'Parent'=>array(
			'className'=>'DoList',
			'foreignKey'=>'parent_id'
		)
	);
	
/* 	 public $hasOne = array(
        'Project' => array(
            'className' => 'Project',
			'foreignKey'=>false,
			'conditions'=>array('Project.id=DoList.project_id') 
          
        ),
	); */
    
    public $validate = array(
        'title' => array(
            'required' => array(
                'rule' => array(EMPTY_MSG),
                'message' => 'To-do is required'
            ), 
            'maxLength' => array(
                'rule' => array('maxLength', 100),
                'message' => 'To-do must be no larger than 100 characters long.'
            )
        ),
        
    );
    public function beforeSave($options = array()) {
        
        return true;
    }

   
}
