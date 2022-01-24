<?php

App::uses('AppModel', 'Model');

/**
 * ElementCostComment Model
 *
 */
class ElementCostComment extends AppModel {
	
	public $validate = array(
        'comments' => array(
            'required' => array(
                'rule' => array(EMPTY_MSG),
                'message' => 'Comment is required'
            )
        )
    );
}
