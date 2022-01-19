<?php
App::uses('AppModel', 'Model');

class ElementDependency extends AppModel {
	
    var $name = 'ElementDependency';
	
    public $belongsTo = array ( 
			'Element' => [ 'dependent' => true ]
		);	

	public $hasMany = array(
		'ElementDependancyRelationship' => array(
		'className' => 'ElementDependancyRelationship',
		'dependent' => true,
		'foreignKey'=> 'element_dependancy_id' 
	) );
}