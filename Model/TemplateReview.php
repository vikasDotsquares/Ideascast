<?php

App::uses('AppModel', 'Model');

/**
 * TemplateReview Model
 *
 */
class TemplateReview extends AppModel {
	public $belongsTo = array(
		'User'=>array(
			'className'=>'User',
			'foreignKey'=>'user_id'
		)
	);
	
	
    public $validate = array(
		'comments' => array(
			'required' => array(
				'rule' => array(EMPTY_MSG),
				'message' => 'Comment is required'
			),
			'maxLength' => array(
					'rule' => array('maxLength', 250),
					'message' => 'Comment must be no larger than 250 characters long.'
				)
			),
			'template_relation_id' => array(
				'required' => array(
					'rule' => array(EMPTY_MSG),
					'message' => 'Template is required'
				)
			),
			'user_id' => array(
				'required' => array(
					'rule' => array(EMPTY_MSG),
					'message' => 'User is required'
				)
			),
	
		);
}
