<?php

App::uses('AppModel', 'Model');

/**
 * ElementRelation Model
 *
 */
class ElementRelation extends AppModel {
	
    public $belongsTo = array(
        'AreaRelation' => array(
            'className' => 'AreaRelation',
            'foreignKey' => 'area_relation_id',
            'dependent' => true
        )
    ); 
    public $hasMany = array(
        'ElementRelationDocument' => array(
            'className' => 'ElementRelationDocument',
            'foreignKey' => 'element_relation_id',
            'dependent' => true
        )
    ); 
	
}
