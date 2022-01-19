<?php
App::uses('AppModel', 'Model');

class RmDetail extends AppModel {

	var $name = 'RmDetail';

	var $belongsTo = array(
		'User' => ['dependent' => true],
		'Project' => ['dependent' => true],
		'RmProjectRiskType' => ['dependent' => false],
	);

}