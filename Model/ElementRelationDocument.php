<?php

App::uses('AppModel', 'Model');

/**
 * ElementRelationDocument Model
 *
 */
class ElementRelationDocument extends AppModel {
	
    public $belongsTo = array(
        'ElementRelation' => array(
            'className' => 'ElementRelation',
            'foreignKey' => 'element_relation_id'
        )
    );
	
	
}
