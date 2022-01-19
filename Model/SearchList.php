<?php
App::uses('AppModel', 'Model');
/**
 * SearchList Model
 *
 */
class SearchList extends AppModel {
		
    var $name = 'SearchList';
	
	var $hasMany =
		[
			'SearchListUser' => [
				'dependent' => true
			],  
		] ; 
					

    public $validate = array(
		'title' => array(
			'required' => array(
				'rule' => array(EMPTY_MSG),
				'message' => 'List title is required'
			), 
		), 
    );
	
/* 
    public function afterFind($results, $primary = false) {
        App::uses('CakeTime', 'Utility');
        foreach ($results as $key => $val) {
            if (isset($val[$this->alias]['created'])) {
                $results[$key][$this->alias]['created'] = CakeTime::format(ADMIN_DATE_FORMAT, $val[$this->alias]['created'], null, TIME_ZONE);
            }
            if (isset($val[$this->alias]['modified'])) {
                $results[$key][$this->alias]['modified'] = CakeTime::format(ADMIN_DATE_FORMAT, $val[$this->alias]['modified'], null, TIME_ZONE);
            }
        }
        return $results;
    } */

   /*   public function beforeSave($options = array()) {

        if (!isset($this->data[$this->alias]["status"])) {
            $this->data[$this->alias]["status"] = 0;
        }

        return true;
    } */
}