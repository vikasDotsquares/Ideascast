<?php

App::uses('AppModel', 'Model');

class AppUser extends AppModel {
    
    public $useTable = "api_users";
    
    public $validate = array(		
		'api_email' => array(
            'required' => array(
                'rule' => array(EMPTY_MSG),
                'message' => 'Email is required'
            ),
            'email' => array(
                'rule' => array('email'),
                'message' => 'Valid email is required',
            ),
            'unique' => array(
                'rule' => array('isUnique', 'email'),
                'message' => 'This email has already been taken',
            //'on' => 'create'
            )
        ),		
        'api_username' => array(
            'required' => array(
                'rule' => array(EMPTY_MSG),
                'message' => 'API Username is required'
            ),
            
            'unique' => array(
                'rule' => array('isUnique', 'api_username'),
                'message' => 'This username has already been taken',
            //'on' => 'create'
            ),
			'valid_username' => array(
				'rule' => array('valid_username'),
				'message' => 'Only alphabets(A-Z,a-z) and numbers(0-9) can be used for Username. Min 6 and Max 10 characters.'
			)
        ),
    );
	
	function valid_username() {
		if (isset($this->data[$this->alias]['api_username']) && !empty($this->data[$this->alias]['api_username'])) {   $username = $this->data[$this->alias]['api_username'];
			$userlenght = strlen($username);
			if(preg_match('/^[a-zA-Z][A-Za-z0-9]{5,10}$/', $username) && $userlenght < 11) {
				return true;
			}			
			return false;
		}
	}
}
