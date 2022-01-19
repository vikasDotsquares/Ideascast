<?php
App::uses('AppModel', 'Model');
/**
 * Country Model
 *
 */
class LocationsByDivision extends AppModel {
	
    public $validate = array(
        'division_type' => array(
            'required' => array(
                'rule' => array(EMPTY_MSG),
                'message' => 'Division is required'
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