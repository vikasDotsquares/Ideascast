<?php

App::uses('AppModel', 'Model');

/**
 * AreaRelation Model
 *
 */
class AreaRelation extends AppModel {
	
    public $belongsTo = array(
        'TemplateRelation' => array(
            'className' => 'TemplateRelation',
            'foreignKey' => 'template_relation_id',
            'dependent' => true
        )
    );
	
	
}
