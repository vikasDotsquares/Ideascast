<?php

App::uses('AppModel', 'Model');

/**
 * ProjectCostComment Model
 *
 */
class ProjectCostComment extends AppModel {
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
