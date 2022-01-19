<?php

App::uses('AppModel', 'Model');

/**
 * WorkspaceCostComment Model
 *
 */
class WorkspaceCostComment extends AppModel {
	//comments

	public $validate = array(
        'comments' => array(
            'required' => array(
                'rule' => array(EMPTY_MSG),
                'message' => 'Comment is required'
            )
        )
    );
}
