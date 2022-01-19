<?php

App::uses( 'AppModel', 'Model' );
class VoteUser extends AppModel {

	var $name = 'VoteUser';
	/* var $hasOne = array(
		'VoteQuestion' => [ 'dependent' => true ]
	);	 */
	
	var $validate = array(
		/* 'list' => array(
			'required' => array(
				'rule' => array(EMPTY_MSG),
				'message' => 'Participant users is required.'
			),
		) */
	);
		
		
}
