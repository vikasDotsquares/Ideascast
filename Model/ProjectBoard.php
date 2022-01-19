<?php
App::uses('AppModel', 'Model');
/**
 * UserProject Model
 *
 */
class ProjectBoard extends AppModel {
	
    var $name = 'ProjectBoard';	
	
	public $belongsTo = array(
        'Project' => array(
            'className' => 'Project',
            //'dependent' => true
        ),
         
    );
}