<?php
App::uses('AppModel', 'Model');

class ElementCost extends AppModel {

	var $name = 'ElementCost';

	var $hasMany = array(
		'UserElementCost' => ['dependent' => true],
	);

	var $belongsTo = array(
		'Element' => ['dependent' => true],
	);

	var $validate = array(
		'estimated_cost' => array(
			'required' => array(
				'rule' => array(EMPTY_MSG),
				'message' => 'Estimated cost is required.',
			),
		),
		'spend_cost' => array(
			'required' => array(
				'rule' => array(EMPTY_MSG),
				'message' => 'Spend cost is required.',
			),
		),
	);
}