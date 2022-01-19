<?php
App::uses('AppModel', 'Model');

class RmUser extends AppModel {

	var $name = 'RmUser';

	var $belongsTo = array(
		'User' => ['dependent' => true],
		'RmDetail' => ['dependent' => true],
	);

}