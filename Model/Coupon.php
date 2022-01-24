<?php
App::uses('AppModel', 'Model');
class Coupon extends AppModel {  

/*	public $hasMany = array(
            
			'UserTransctionDetail'=>array(
                'className'     => 'UserTransctionDetail', 
                'conditions' => array('UserTransctionDetail.coupon_id' => 'id'),				
              //  'dependent'=> true 
            )
    ); */

   
     public $validate = array(
        'name' => array(
            'required' => array(
                'rule' => array(EMPTY_MSG),
                'message' => 'Name is required'
            ),
			'unique' => array(
                'rule' => array('isUnique', 'name'),
                'message' => 'Already been taken',
            //'on' => 'create'
            )
			),
			'percentage' => array(
						'numeric' => array(
							'rule' => 'numeric',
							'allowEmpty' => true,
							'message' => 'Please enter numeric value'
						),      
			   'range' => array(
							'rule' => array('range', 1, 100.01),
							'required' => true,
							'message' => 'value must be between 1 to 100',
						),           
					),	
			
		/*'useable' => array(
            'notempty' => array(
                'rule' => 'numeric',
                'required' => true,
				
                'message' => 'Please enter numeric value',
            ),           
        ), */
		
		'flat' => array(
			'numeric' => array(
				'rule' => 'numeric',
				'allowEmpty' => true,
				'message' => 'Please enter numeric value'
			),         
        ),
		
		'start_time' => array(
            'required' => array(
                'rule' => array(EMPTY_MSG),
                'message' => 'This is required'
            ),
         
        ),
		'end_time' => array(
            'required' => array(
                'rule' => array(EMPTY_MSG),
                'message' => 'This is required'
            ),
         
        ),
		
		
		'on_amount' => array(
			'numeric' => array(
				'rule' => 'numeric',
				'allowEmpty' => true,
				'message' => 'Please enter numeric value'
			),         
        ),
		
		
          
        
    );
 }
