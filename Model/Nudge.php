<?php
App::uses('AppModel', 'Model');

class Nudge extends AppModel {

    public $hasMany = array(
		'NudgeUser' => array(
			'className' => 'NudgeUser'
		)
    );



}