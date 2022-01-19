<?php
App::uses('AppModel', 'Model');

class ElementDecision extends AppModel {
	
    var $name = 'ElementDecision';
	
    var $belongsTo = array ( 
			'Element' => [ 'dependent' => true ]
		);
	
    var $hasMany = array (
			'ElementDecisionDetail' => [ 'dependent' => true ]
		);
		
	
	/* 
	public $hasAndBelongsToMany = array(
			'Decision' => array(
				'className' => 'Decision',
				'joinTable' => 'elements',
				'foreignKey' => 'element_id',
				'associationForeignKey' => 'id',
			),
			'Element' => array(
				'className' => 'Element',
				'joinTable' => 'decisions',
				'foreignKey' => 'decision_id',
				'associationForeignKey' => 'id',
			)
			
		);
	 */
    function uniqueCombi() {
        $combi = array(
				"{$this->alias}.element_id" => $this->data[$this->alias]['element_id'],
				"{$this->alias}.decision_id"  => $this->data[$this->alias]['decision_id']
			);
        return $this->isUnique($combi, false);
    }
		
	var $validate = array(
		'element_id' => array(
			'required' => array(
				'rule' => array(EMPTY_MSG),
				'message' => 'Reference with an element is required.'
			),
			// array('rule' => 'uniqueCombi')
		),
		'decision_id' => array(
			'required' => array(
				'rule' => array(EMPTY_MSG),
				'message' => 'Reference with a decision is required.'
			),
			// array('rule' => 'uniqueCombi')
		), 
		'title' => array(
			'required' => array(
				'rule' => array(EMPTY_MSG),
				'message' => 'Title field is required.'
			) 
		) 
	); 
}