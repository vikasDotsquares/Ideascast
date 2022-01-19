<?php
App::uses('AppModel', 'Model');
/**
 * ProjectSketch Model
 *
 */
class ProjectSketch extends AppModel {

    var $name = 'ProjectSketch';
	public $hasMany = array(
		'ProjectSketchParticipant' => array(
			'className' => 'ProjectSketchParticipant',
			'dependent' => false
		),
		
    );
	
	
    public $validate = array(
        'sketch_title' => array(
            'rule' => EMPTY_MSG,
            'message' => 'This field should not be empty.'
        )
    );
    
}
