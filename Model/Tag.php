<?php

App::uses('AppModel', 'Model');
App::uses('Controller/Component', 'Auth');
App::uses('Sanitize', 'Utility');

/**
 * Project Model
 *
 */
class Tag extends AppModel {
	var $name = 'Tag';
	
	public $validate = array(
	);
}
