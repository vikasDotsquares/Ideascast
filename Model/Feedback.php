<?php
App::uses('AppModel', 'Model');

class Feedback extends AppModel {
	
    var $name = 'Feedback';
	
	var $hasMany = array (
			'FeedbackResults' => [
				'className' => 'FeedbackResults',
				'foreignKey' => 'feedback_id',
				//'dependent' => true
			] 
		);
	 
	/* var $hasMany = array (
			'ElementFeedbackDetails' => [
				'className' => 'ElementFeedbackDetails',
				'foreignKey' => 'feedback_id',
				'dependent' => true
			] 
		);
	var $hasAndBelongsToMany = array(
			'Element' => array(
					'className' => 'Element',
					'joinTable' => 'element_feedback',
					'foreignKey' => 'feedback_id',
					'associationForeignKey' => 'element_id',
					//'unique' => true,
		)
	); */
	
	
	var $validate = array(
		'title' => array(
			'required' => array(
				'rule' => array(EMPTY_MSG),
				'message' => 'Title is required.'
			),
			// array('rule' => 'uniqueCombi')
		),
		'reason' => array(
			'required' => array(
				'rule' => array(EMPTY_MSG),
				'message' => 'Reason is required.'
			),
			// array('rule' => 'uniqueCombi')
		),
		'feedback_for' => array(
			'required' => array(
				'rule' => array(EMPTY_MSG),
				'message' => 'Feedback for is required.'
			),
			// array('rule' => 'uniqueCombi')
		),
		'start_date' => array(
			'required' => array(
				'rule' => array(EMPTY_MSG),
				'message' => 'Start date is required.'
			),
			// array('rule' => 'uniqueCombi')
		),
		'end_date' => array(
			'required' => array(
				'rule' => array(EMPTY_MSG),
				'message' => 'End date is required.'
			),
			// array('rule' => 'uniqueCombi')
		),
		
		);
		
		
}