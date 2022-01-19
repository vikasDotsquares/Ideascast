<?php
App::uses('AppModel', 'Model');

class ElementMindmap extends AppModel {
	
    var $name = 'ElementMindmap';
	
    var $belongsTo = array (
			'Element' 
		);
	
	var $hasMany = array (
			'MindmapHistory' => [
				'className' => 'MindmapHistory',
				'foreignKey' => 'element_mindmap_id',
				'dependent' => true
			],
		);
		
	var $validate = array(
		'element_id' => array(
			'required' => array(
				'rule' => array(EMPTY_MSG),
				'message' => 'Reference with an element is required.'
			) 
		), 
		'title' => array(
			'required' => array(
				'rule' => array(EMPTY_MSG),
				'message' => 'Title is required.'
			), 
			
		), 
		'description' => array( 
			'required' => array(
				'rule' => array(EMPTY_MSG),
				'message' => 'Description is required.'
			) 
		),
	); 
}