<?php
App::uses('AppModel', 'Model');

class Decision extends AppModel {
	
    var $name = 'Decision';
	 
	var $hasMany = array (
			'ElementDecisionDetails' => [
				'className' => 'ElementDecisionDetails',
				'foreignKey' => 'decision_id',
				'dependent' => true
			] 
		);
	var $hasAndBelongsToMany = array(
			'Element' => array(
					'className' => 'Element',
					'joinTable' => 'element_decisions',
					'foreignKey' => 'decision_id',
					'associationForeignKey' => 'element_id',
					//'unique' => true,
		)
	);
	
}