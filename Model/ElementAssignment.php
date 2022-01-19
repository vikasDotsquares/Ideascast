<?php
App::uses('AppModel', 'Model');

class ElementAssignment extends AppModel {

	var $name = 'ElementAssignment';

	var $belongsTo = array(
		'Element' => ['dependent' => true],
		'Sender' => [
			'dependent' => true,
			'foreignKey' => 'created_by',
			'className' => 'User',
		],
		'Receiver' => [
			'dependent' => true,
			'foreignKey' => 'assigned_to',
			'className' => 'User',
		],
	);

}