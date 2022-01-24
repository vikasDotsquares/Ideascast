<?php
App::uses('AppModel', 'Model');
class Plan extends AppModel {     
     public $validate = array(
        'title' => array(
            'required' => array(
                'rule' => array(EMPTY_MSG),
                'message' => 'Title is required'
            ),
           
        ),
        'description' => array(
            'notempty' => array(
                'rule' => array(EMPTY_MSG),
                'message' => 'Please enter description',
            ),           
        ),

		
		'plantype_monthly' => array(
            'notempty' => array(
               'rule' => 'numeric',
                'required' => true,
                'message' => 'Please enter amount only',
            ),           
        ),
		
		'plantype_yearly' => array(
            'notempty' => array(
               'rule' => 'numeric',
                'required' => true,
                'message' => 'Please enter amount only',
            ),           
        ),
		
		'plantype_once' => array(
            'notempty' => array(
                'rule' => 'numeric',
                'required' => true,
                'message' => 'Please enter amount only',
            ),           
        ),
       
		
    );

 }
