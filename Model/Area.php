<?php
App::uses('AppModel', 'Model');

class Area extends AppModel {
	
    var $name = 'Area';
	
	/* var $hasMany = array(
			'Elements' => array(
				'className' => 'Elements', 
				'conditions' => ['Elements.status <' => 3],
				'order' => 'Elements.sort_order ASC' 
			),
			'StandbyElement' 
		); */
	
	var $hasMany = array(
			'Elements' => array(
				'className' => 'Elements', 
				'order' => 'Elements.sort_order ASC',
				// 'dependent' => true
			),					
		);
		
	
		
	var $belongsTo =  [
			'TemplateDetail', 
			'Workspace'  => [
				'dependent' => true
			]
		] ; 
	// var $belongsTo = array ('Workspace',  );
	 
	public $validate = array(
			'title' => array(
				'required' => array(
					'rule' => array(EMPTY_MSG),
					'message' => 'Title is required'
				), 
				'length' => array(
					'rule' => array('maxLength', 100),
					'message' => 'Title must be no larger than 100 characters long.'
				)
			),
			'description' => array(
				'required' => array(
					'rule' => array(EMPTY_MSG),
					'message' => 'Description is required'
				), 
				'length' => array(
					'rule' => array('maxLength', 250),
					'message' => 'Description must be no larger than 250 characters long.'
				)
			
			),
		
		);
	 
}

