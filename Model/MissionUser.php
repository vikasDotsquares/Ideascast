<?php

App::uses('AppModel', 'Model');

/**
 * MissionUser Model
 *
 */
class MissionUser extends AppModel { 
	public $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
			'dependent' => true
		)
    );
}
