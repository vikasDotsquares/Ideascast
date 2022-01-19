<?php

App::uses('AppModel', 'Model');

/**
 * TemplateCategory Model
 *
 */
class TemplateCategory extends AppModel {
	public $hasMany = array(
			'TemplateRelation' => array(
				'className' => 'TemplateRelation',
				'foreignKey' => 'template_category_id',
				'dependent' => true
			)
		);
}
