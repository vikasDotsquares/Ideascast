<?php
App::uses('AppModel', 'Model');

class ElementFeedback extends AppModel {
	
    var $name = 'ElementFeedback';
	
    var $belongsTo = array (
			'Element'  => [ 'dependent' => true ]
		);
	
    var $hasMany = array (
			'ElementFeedbackDetail'  => [ 'dependent' => true ],
		);

	
	var $validate = array(
		'element_id' => array(
			'required' => array(
				'rule' => array(EMPTY_MSG),
				'message' => 'Reference with an element is required.'
			) 
		), 
		'title' => array(
			'required' => array(
				'rule' => array(EMPTY_MSG),
				'message' => 'Title is required.'
			),  
			'maxlength' => array(
				'rule' => array('maxlength', 100),
				'message' => 'Maximum length is 50 chars.',
			)
		), 
		
		'description' => array( 
			'required' => array(
				'rule' => array(EMPTY_MSG),
				'message' => 'Description is required.'
			) 
		),
	); 
}