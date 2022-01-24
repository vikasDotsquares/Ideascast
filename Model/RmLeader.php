<?php
App::uses('AppModel', 'Model');

class RmLeader extends AppModel {

	var $name = 'RmLeader';

	var $belongsTo = array(
		'User' => ['dependent' => true],
		'RmDetail' => ['dependent' => true],
	);

}