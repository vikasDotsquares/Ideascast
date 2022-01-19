<?php
App::uses('AppModel', 'Model');

class ElementFeedbackDetail extends AppModel {
	
    var $name = 'ElementFeedbackDetail';
	
    var $belongsTo = array ( 
			'ElementFeedback'  => [ 'dependent' => true ],
			'Feedback' => [ 
				'className' => 'Feedback',
				'foreignKey' => 'feedback_id' 
			],
		);
	   
}