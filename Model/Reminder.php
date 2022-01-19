<?php

App::uses('AppModel', 'Model');

/**
 * Reminder Model
 *
 */
class Reminder extends AppModel {

	public $belongsTo = array(
		'Project' => array(
			'className' => 'Project',
			'foreignKey' => 'project_id',
			'dependent' => true,
		),
		'Element' => array(
			'className' => 'Element',
			'foreignKey' => 'element_id',
			'dependent' => true,
		),
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
			'dependent' => true,
		),
	);

	public $hasMany = array(
		'ReminderSetting' => array(
			'className' => 'ReminderSetting',
			'foreignKey' => 'reminder_id',
			'dependent' => true,
		),
	);

}
