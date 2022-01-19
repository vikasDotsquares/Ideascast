<?php
App::uses('AppModel', 'Model');
/**
 * Country Model
 *
 */
class UserTransctionDetail extends AppModel {
	    public $belongsTo = array(

			'User'=>array(
                'className'     => 'User',  
                'foreignKey' => 'user_id',	
            ),
			'Coupon'=>array(
                'className'     => 'Coupon',  
                'foreignKey' => 'coupon_id',	
            ),
			/* 'UserDetail'=>array(
                'className'     => 'UserDetail',  
                'foreignKey' => false,	
			    'conditions' => array('UserTransctionDetail.user_id' => 'UserDetail.user_id'),		
            ), */
			/* 'UserPlan'=>array(
                'className'     => 'UserPlan',  
                'foreignKey' => 'user_id',	
            ), */
			
    );
	
	
	
}
	