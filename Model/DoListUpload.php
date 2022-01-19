<?php

App::uses('AppModel', 'Model');

/**
 * DoListUpload Model
 *
 */
class DoListUpload extends AppModel {
	
    public $belongsTo = array(
        'DoList' => array(
            'className' => 'DoList',
            'foreignKey' => 'do_list_id',
            'dependent' => true
        )
    );
	
    public $validate = array(
        'do_list_id' => array(
            'required' => array(
                'rule' => array(EMPTY_MSG),
                'message' => 'File must assigned with a To-do'
            )
        ) 
    );
	

}
