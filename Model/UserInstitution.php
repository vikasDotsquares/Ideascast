<?php

App::uses('AppModel', 'Model');

/**
 * User Model
 *
 */
class UserInstitution extends AppModel {

    /**
     * Validation rules
     *
     * @var array
     */ 

	
	
       
    public $validate = array(
        'person' => array(
            'required' => array(
                'rule' => array(EMPTY_MSG),
                'message' => 'This is required'
            ),
         
        ),
		 'start' => array(
            'required' => array(
                'rule' => array(EMPTY_MSG),
                'message' => 'This is required'
            ),
         
        ),
		'end' => array(
            'required' => array(
                'rule' => array(EMPTY_MSG),
                'message' => 'This is required'
            ),
         
        ),
		'membership_code' => array(
            'required' => array(
                'rule' => array(EMPTY_MSG),
                'message' => 'This is required'
            ),
         
        ),
       	'contact' => array(
            'notempty' => array(
               'rule' => 'numeric',
                'required' => true,
                'message' => 'Please enter numeric value',
            ),           
        ),

		  
		
    );

	
}
