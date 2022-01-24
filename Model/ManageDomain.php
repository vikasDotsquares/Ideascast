<?php

App::uses('AppModel', 'Model');

/**
 * User Model
 *
 */
class ManageDomain extends AppModel {

    /**
     * Validation rules
     *
     * @var array
    */	
	public $belongsTo = array(
        'Domain' => array(
            'className' => 'Domain'
        )
    );
	
    public $validate = array(      
		'domain_name' => array(
            'required' => array(
                'rule' => array(EMPTY_MSG),
                'message' => 'Domain name is required'
            ),
            'unique' => array(
                'rule' => array('isUnique', 'domain_name'),
                'message' => 'This domain has already been taken'
            )
        ),
		'domain_id' => array(
            'required' => array(
                'rule' => array(EMPTY_MSG),
                'message' => 'Select domain type is required'
            )
        )	
    );

}
