<?php

App::uses('AppModel', 'Model');

/**
 * User Model
 *
 */
class Subject extends AppModel {

    /**
     * Validation rules
     *
     * @var array
     */
    public $displayField = 'title';

    public function getSubjects( $term, $excludeSkills = '' ) {
        if ( !isset($term) || empty($term) ){
			return false;
		}

		if($excludeSkills != '') {
			$subjects = $this->find('list', [
				'conditions' => [
					'Subject.title LIKE' => "%$term%",
					"NOT" => array( "Subject.id" => explode(',', $excludeSkills) )
				],
				'fields' => ['Subject.id', 'Subject.title'],
				'recursive' => -1
			]);
		} else {
			$subjects = $this->find('list', [
				'conditions' => [
					'Subject.title LIKE' => "%$term%"
				],
				'fields' => ['Subject.id', 'Subject.title'],
				'recursive' => -1
			]);
		}
		return $subjects;
    }


}
