<?php
App::uses('AppModel', 'Model');

class RmExposeResponse extends AppModel {

	var $name = 'RmExposeResponse';

	var $belongsTo = array(
		'RmDetail' => ['dependent' => true],
	);

}