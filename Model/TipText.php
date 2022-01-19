<?php
App::uses('AppModel', 'Model');
/**
 * TipText Model
 *
 */
class TipText extends AppModel {
	
	//public $belongsTo = array( 'ProjectsCategory' , 'ProjectsSource' , 'SubjectExpenseType' );
	
    public $validate = array(
        'place_holder' => array(
            'required' => array(
                'rule' => array(EMPTY_MSG),
                'message' => 'Placeholder key is required'
            ),
			'unique' => array(
				'rule' => array('isUnique', 'place_holder'),
				'message' => 'This place_holder has already been taken',
			)
			//'on' => 'create'
        ),
		'description' => array(
            'required' => array(
                'rule' => array(EMPTY_MSG),
                'message' => 'Description is required'
            ),
        ) 

    );
	
	
    public function afterFind($results, $primary = false) {
        App::uses('CakeTime', 'Utility');
        foreach ($results as $key => $val) {
            if (isset($val[$this->alias]['created'])) {
                $results[$key][$this->alias]['created'] = CakeTime::format(ADMIN_DATE_FORMAT, $val[$this->alias]['created'], null, TIME_ZONE);
            }
        }
        return $results;
    }
	
}