<?php

App::uses('AppModel', 'Model');

/**
 * User Model
 *
 */
class OrgPassPolicy extends AppModel {

    /**
     * Validation rules
     *
     * @var array
     */
    
	public $validate = array(
        'min_lenght' => array(
            'required' => array(
                'rule' => array('maxLength', 100),
                'message' => 'Minimum Length is required'
            )           
        ),
		'change_pass_time' => array(
            'required' => array(
                'rule' => array('maxLength', 100),				
                'message' => 'Change Password is required'
            )           
        ),
		'pass_repeat' => array(
            'required' => array(
                'rule' => array('maxLength', 50),
                'message' => 'No Repeat is required'
            )           
        ),
		'temp_lockout' => array(
            'required' => array(
                'rule' => array('maxLength', 180),
                'message' => 'Temporary Lockout is required'
            )           
        ),
		'lockout_period' => array(
            'required' => array(
                'rule' => array('maxLength', 180),
                'message' => 'Lockout Period is required'
            )           
        ),
		/* 'session_timeout' => array(
            'required' => array(
                'rule' => array(EMPTY_MSG),
                'message' => 'Session Timeout is required'
            )           
        ) */
    );

    

}
