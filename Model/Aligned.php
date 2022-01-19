<?php
App::uses('AppModel', 'Model');

class Aligned extends AppModel {
	
    var $name = 'Aligned';
	 
	var $hasMany = array(
			'Project' => array(
				'className' => 'Project' 
			),					
		);
		 
}

