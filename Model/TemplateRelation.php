<?php

App::uses('AppModel', 'Model');

/**
 * TemplateRelation Model
 *
 */
class TemplateRelation extends AppModel {

	public $hasMany = array(
		'AreaRelation' => array(
			'className' => 'AreaRelation',
			'foreignKey' => 'template_relation_id',
			'dependent' => true,
		),
		'TemplateLike' => array(
			'className' => 'TemplateLike',
			'foreignKey' => 'template_relation_id',
			'dependent' => true,
		),
		'TemplateReview' => array(
			'className' => 'TemplateReview',
			'foreignKey' => 'template_relation_id',
			'dependent' => true,
		),
		'Workspace' => array(
			'className' => 'Workspace',
			'foreignKey' => 'template_relation_id',
			//'dependent' => true,
		),
	);

	public $belongsTo = array(
		'Template' => array(
			'className' => 'Template',
			'foreignKey' => 'template_id',
		),
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
		),
		'TemplateCategory' => array(
			'className' => 'TemplateCategory',
			'foreignKey' => 'template_category_id',
		),
	);

	public $validate = array(
		'template_category_id' => array(
			'required' => array(
				'rule' => array(EMPTY_MSG),
				'message' => 'Category is required',
			),
		),
		'title' => array(
			'required' => array(
				'rule' => array(EMPTY_MSG),
				'message' => 'Title is required',
			),
			/*'maxLength' => array(
				'rule' => array('maxLength', 100),
				'message' => 'Title must be no larger than 50 characters long.'
			)*/
		),

		'description' => array(
			'required' => array(
				'rule' => array(EMPTY_MSG),
				'message' => 'Description is required',
			),
			'maxLength' => array(
				'rule' => array('maxLength', 600),
				'message' => 'Description must be no larger than 500 characters long.',
			),
		),
		'thirdparty_id' => array(
			'checkThirdparty' => array(
				'rule' => 'checkThirdparty',
				'required' => false,
				'message' => 'Please select third party user.',
			),
		),

	);

	function checkThirdparty() {
		$data = $this->data;

		//pr($data);

		if (!empty($data['TemplateRelation']['user_type']) && $data['TemplateRelation']['user_type'] == 3) {
			//pr($data);
			if (empty($data['TemplateRelation']['thirdparty_id'])) {
				//pr($data);
				return false;
			} else {
				return true;
			}

		} else {
			return true;
		}

	}
	/*
		function paginate($conditions, $limit=1, $fields=null, $order=null, $page=null, $recursive=null, $extra=null)
		{
			//pr($conditions,1);
			return $this->query('SELECT `TemplateRelation`.`id`, `TemplateRelation`.`type`, `TemplateRelation`.`user_id`, `TemplateRelation`.`thirdparty_id`, `TemplateRelation`.`template_id`, `TemplateRelation`.`template_category_id`, `TemplateRelation`.`rating`, `TemplateRelation`.`status`, `TemplateRelation`.`title`, `TemplateRelation`.`description`, `TemplateRelation`.`key_result_target`, `TemplateRelation`.`color_code`, `TemplateRelation`.`created`, `TemplateRelation`.`modified`, `TemplateCategory`.`id`, `TemplateCategory`.`title`, `TemplateCategory`.`status`, `TemplateCategory`.`created`, `TemplateCategory`.`modified` FROM `template_relations` AS `TemplateRelation` LEFT JOIN `template_categories` AS `TemplateCategory` ON (`TemplateRelation`.`template_category_id` = `TemplateCategory`.`id`)  WHERE '.$conditions . $limit);
		}

		function paginateCount($conditions=null, $recursive=null, $extra=null)
		{
			return $this->query('SELECT COUNT(*) FROM `template_relations` AS `TemplateRelation` LEFT JOIN `template_categories` AS `TemplateCategory` ON (`TemplateRelation`.`template_category_id` = `TemplateCategory`.`id`)  WHERE '.$conditions);
	*/

}
