<?php

App::uses( 'AppModel', 'Model' );
class Vote extends AppModel {

	var $name = 'Vote';
	var $hasOne = array(
		'VoteQuestion' => [ 'dependent' => true ]
	);
	
	var $hasMany = array(
		'VoteUser' => [ 'dependent' => true ],
		'VoteResults' => [
				'className' => 'VoteResults',
				'foreignKey' => 'vote_id',
				//'dependent' => true
			] 		
	);	
 
	
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
