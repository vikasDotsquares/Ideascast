<?php

App::uses('AppModel', 'Model');

/**
 * ProjectRag Model
 *
 */
class ProjectRag extends AppModel {
	
    public $belongsTo = array(
        'Project' => array(
            'className' => 'Project',
            'foreignKey' => 'project_id',
            'dependent' => true
        ),
        'User' => array(
            'className' => 'User',
            'foreignKey' => 'user_id',
            'dependent' => true
        )
    );


}
