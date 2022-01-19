<?php

App::uses('AppModel', 'Model');

/**
 * SkillLink Model
 *
 */
class SkillLink extends AppModel { 
	
	var $name = 'SkillLink';
	
	public $validate = array(		 
		'link_name' => array(
			'required' => array(
				'rule' => array(EMPTY_MSG),
				'message' => 'Skill link title is required.',
			)
			 
		),					 
		'web_link' => array(
			'required' => array(
				'rule' => EMPTY_MSG,
				'message' => 'Skill Web link is required.'
			)
		)
		
	); 
	
	/* ,
	array(
		'rule' => 'isUnique',
		'message' => 'Skill link title has already been taken.',
		'required' => true,
	) */
}
