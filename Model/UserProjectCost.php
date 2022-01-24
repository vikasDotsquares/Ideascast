<?php
App::uses('AppModel', 'Model');

class UserProjectCost extends AppModel {

	var $name = 'UserProjectCost';

	var $belongsTo = array(
		'User' => ['dependent' => true],
		'Project' => ['dependent' => true],
	);

}