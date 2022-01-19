<?php
App::uses('AppModel', 'Model');

class RewardHistory extends AppModel {

	var $name = 'RewardHistory';
	public $belongsTo = array(
		'RewardAssignment' => array(
			'className' => 'RewardAssignment',
		),
	);
}