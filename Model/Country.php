<?php
App::uses('AppModel', 'Model');
/**
 * Country Model
 *
 */
class Country extends AppModel {
	
    public $validate = array(
        'country' => array(
            'required' => array(
                'rule' => array(EMPTY_MSG),
                'message' => 'Country is required'
            ),
        ),
					'vat' => array(
						'numeric' => array(
							'rule' => 'numeric',
							'allowEmpty' => true,
							'message' => 'Please enter numeric value'
						),      
			   'range' => array(
							'rule' => array('range', -0.1, 100.01),
							'required' => true,
							'message' => 'value must be between 0 to 100',
						),           
					),
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
    
    public function beforeSave($options = array()) {
        
        if (!isset($this->data[$this->alias]["status"])) {
            $this->data[$this->alias]["status"] = 0;
        }

        return true;
    }
	
	
	
	
}