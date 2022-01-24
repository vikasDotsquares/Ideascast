<?php
App::uses('AppModel', 'Model');

class Program extends AppModel {

 

	public $validate = array(
		'program_name' => array(
			'required' => array(
				'rule' => array(EMPTY_MSG),
				'message' => 'Program name is required',
			),
			'length' => array(
				'rule' => array('maxLength', 350),
				'message' => 'Program name must be no larger than 350 characters.',
			),
		),
	);
}