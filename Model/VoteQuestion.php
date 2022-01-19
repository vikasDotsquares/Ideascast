<?php

App::uses( 'AppModel', 'Model' );
class VoteQuestion extends AppModel {

	var $name = 'VoteQuestion';
	var $hasMany = array(
		'VoteQuestionOption' => [ 'dependent' => true ]
	);	
	var $belongsTo = array(
		'VoteType' => [ 'dependent' => true ]
	);	
	
	var $validate = array(
            'vote_type_id' => array(
                    'required' => array(
                            'rule' => array(EMPTY_MSG),
                            'message' => 'Voting Method is required.'
                    ),
            ),
            'title' => array(
                    'required' => array(
                            'rule' => array(EMPTY_MSG),
                            'message' => 'Voting For is required.'
                    ),
            ),
			'distributed_count' => array(
                    'required' => array(
                            'rule' => array(EMPTY_MSG),
                            'message' => 'Total option count is required.'
                    ),
            )
	);
		
		
}
