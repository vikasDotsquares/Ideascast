<?php
App::uses('AppModel', 'Model');

class RmProjectRiskType extends AppModel {

	var $name = 'RmProjectRiskType';

	var $belongsTo = array(
		'User' => ['dependent' => true],
		'Project' => ['dependent' => true],
	);

}