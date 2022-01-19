<?php
App::uses('AppModel', 'Model');
/**
 * Country Model
 *
 */
class ProjectsStatus extends AppModel {
	
    public $validate = array(
        'title' => array(
            'required' => array(
                'rule' => array(EMPTY_MSG),
                'message' => 'Project status title is required'
            ),
        ),
    );

   
}