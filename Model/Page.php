<?php
App::uses('AppModel', 'Model');
/**
 * Page Model
 *
 */
class Page extends AppModel {
	
  
    public function afterFind($results, $primary = false) {
        App::uses('CakeTime', 'Utility');
        foreach ($results as $key => $val) {
            if (isset($val[$this->alias]['created'])) {
                $results[$key][$this->alias]['created'] = CakeTime::format(ADMIN_DATE_FORMAT, $val[$this->alias]['created'], null, TIME_ZONE);
            }
        }
        return $results;
    }
	
	
     
     public $validate = array(
        'name' => array(
            'required' => array(
                'rule' => array(EMPTY_MSG),
                'message' => 'Name is required'
            ),
			),
            'content' => array(
			 'required' => array(
                'rule' => array(EMPTY_MSG),
                'message' => 'Content is required',
			),	
            ),
          
        
    );
     
    /**
     * Overrides parent before save for slug generation
     * Also handles ordering of the page
		*
     * @return boolean Always true
     */
	public function beforeSave( $options = array() ) {
		
		if (!empty($this->data[$this->alias]['title']) && empty($this->data[$this->alias]['slug'])) {
				
			if (!empty($this->data[$this->alias][$this->primaryKey])) {
					
				$this->data[$this->alias]['slug'] = $this->generateSlug($this->data[$this->alias]['title'], $this->data['Page'][$this->primaryKey]);
				
			} 
			else {
					
				$this->data[$this->alias]['slug'] = $this->generateSlug($this->data[$this->alias]['title']);
				
			}
		}
		
		
        if (!isset($this->data[$this->alias]["status"])) {
            $this->data[$this->alias]["status"] = 1;
        }
		
		
		return true;
	}
	
}