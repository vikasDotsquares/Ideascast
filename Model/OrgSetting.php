<?php

App::uses('AppModel', 'Model');

/**
 * OrgSetting Model
 *
 */
class OrgSetting extends AppModel {

    var $name = 'OrgSetting';
	  var $belongsTo = array(
        'User'
    ); 
	var $validate = array(        
		'subdomain' => array(
            'required' => array(
                'rule' => array(EMPTY_MSG),
                'message' => 'Domain name is required'
            ),          
            'unique' => array(
                'rule' => array('isUnique', 'subdomain'),
                'message' => 'This domain has already been taken',            
            )
        ),
		'start_date' => array(
            'checkStartDate' => array(                
                'rule' => array('checkStartDate'),
                'message' => 'Start date is not valid.'
            )            
        ),		
		'allowed_space' => array(
			'required' => array(
                'rule' => array(EMPTY_MSG),
                'message' => 'License is required'
            ),
			'numeric' => array(
						'rule' => 'numeric',
						'allowEmpty' => true,
						'message' => 'Please enter numeric value'
			),           
		),
		'license' => array(
            'required' => array(
                'rule' => array(EMPTY_MSG),
                'message' => 'License is required'
            ),
			'numeric' => array(
						'rule' => 'numeric',
						'allowEmpty' => true,
						'message' => 'Please enter numeric value'
			),	
        ),
		'apilicense' => array(
            'required' => array(
                'rule' => array(EMPTY_MSG),
                'message' => 'API license is required'
            ),
			'numeric' => array(
						'rule' => 'numeric',
						'allowEmpty' => true,
						'message' => 'Please enter numeric value'
			),	
        )
	);

	function checkStartDate(){
		
		if( empty($this->data['OrgSetting']['start_date'] ||  strtotime($this->data['OrgSetting']['start_date']) > strtotime($this->data['OrgSetting']['end_date'] ) ) ){
			return false;
		} else {
			return true;
		}
	}
	
	function checkEndDate(){
		
		$currentDate = strtotime(date('m/d/Y'));
		if( !empty($this->data[$this->alias]['end_date']) || strtotime($this->data[$this->alias]['end_date']) < $currentDate )
		{
			return false;	
		}
		
	}
}
