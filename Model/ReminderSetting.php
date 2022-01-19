<?php

App::uses('AppModel', 'Model');

/**
 * Reminder Model
 *
 */
class ReminderSetting extends AppModel {

	public $belongsTo = array(
		'Reminder' => array(
			'className' => 'Reminder',
			'foreignKey' => 'reminder_id',
			'dependent' => true,
		),
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
			'dependent' => true,
		),
	);

}
