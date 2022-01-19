<?php
App::uses('AppModel', 'Model');
class Currency extends AppModel {     
     public $validate = array(
	     'country' => array(
            'required' => array(
                'rule' => array(EMPTY_MSG),
                'message' => 'Name is required'
            ),
	    ),
        'name' => array(
            'required' => array(
                'rule' => array(EMPTY_MSG),
                'message' => 'Name is required'
            ),
			),
            'sign' => array(
			 'required' => array(
                'rule' => array(EMPTY_MSG),
                'message' => 'Sign is required',
			),	
            ),
			'value' => array(
			 'required' => array(
                'rule' => array(EMPTY_MSG),
                'message' => 'Value is required',
			),	
            ),
			'symbol' => array(
			 'required' => array(
                'rule' => array(EMPTY_MSG),
                'message' => 'Value is required',
			),	
            ),
          
        
    );
 }
