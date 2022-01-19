<?php

App::uses('AppModel', 'Model');

/**
 * User Model
 *
 */
class KnowledgeDomain extends AppModel {

    /**
     * Validation rules
     *
     * @var array
     */
    public $displayField = 'title';
    /* public $validate = array(
            'Skill' => array(
                'multiple' => array(
                        'rule' => array('multiple', array('min' => 1)),
                        'message' => 'You need to select at least one skill',
                        'required' => true,						
                ),
            ), 
			'title' => array(
                array(
					'rule' => 'isUnique',
					'message' => 'This skill has already been taken.',
					'required' => true,
				),
            )*/	 
			 
			/* 'title' => array(
				'required' => array(
					'rule' => EMPTY_MSG,
					'message' => 'Skill is required'
				),				
				'unique' => array(
					'rule' => array('isUnique', 'title'),
					'message' => 'This skill has already been taken.',					
				)
			),
			
        ); */

    
    public function getSkills( $term, $excludeSkills = '' ) {
        if ( !isset($term) || empty($term) ){
			return false;
		}
		
		if($excludeSkills != '') { 
			$skills = $this->find('list', [
				'conditions' => [
					'KnowledgeDomain.title LIKE' => "%$term%",
					"NOT" => array( "KnowledgeDomain.id" => explode(',', $excludeSkills) )
				],
				'fields' => ['KnowledgeDomain.id', 'KnowledgeDomain.title'],
				'recursive' => -1
			]);
		} else {
			$skills = $this->find('list', [
				'conditions' => [
					'KnowledgeDomain.title LIKE' => "%$term%"
				],
				'fields' => ['KnowledgeDomain.id', 'KnowledgeDomain.title'],
				'recursive' => -1
			]);
		}
		return $skills;
    }
	

}
