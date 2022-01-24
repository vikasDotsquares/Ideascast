<?php
App::uses('AppModel', 'Model');

class RmElement extends AppModel {

	var $name = 'RmElement';

	var $belongsTo = array(
		'Element' => ['dependent' => true],
		'Project' => ['dependent' => true],
		'RmDetail' => ['dependent' => true],
	);

}