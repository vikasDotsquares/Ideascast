<?php
App::uses('AppModel', 'Model');

class UserInterest extends AppModel {
	
	public $belongsTo = array(
		'User' => array(
			'className' => 'User'
		)
    );
	
}