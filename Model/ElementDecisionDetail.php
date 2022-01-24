<?php
App::uses('AppModel', 'Model');

class ElementDecisionDetail extends AppModel {
	
    var $name = 'ElementDecisionDetail';
	
    var $belongsTo = array ( 
			'ElementDecision'  => [ 'dependent' => true ],
			'Decision' => [ 
				'className' => 'Decision',
				'foreignKey' => 'decision_id' 
			],
		);
	   
}