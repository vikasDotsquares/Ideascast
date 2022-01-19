<?php

App::uses('AppModel', 'Model');

/**
 * ProjectElementType Model
 *
 */
class ProjectElementType extends AppModel {	 
	
	public $belongsTo = array(
        'Project' => array(
            'className' => 'Project',
            'foreignKey' => 'project_id'
        ) 
         
    );
	
    /**
     * Validation rules
     *
     * @var array
     */

	public $validate = array(
        'title' => array(
            'required' => array(
                'rule' => array(EMPTY_MSG),
                'message' => 'Task type is required'
            )
        )
	);	


 

	public function beforeSave($options = array()) {

		$user_id = CakeSession::read("Auth.User.id");

		if (isset($this->data[$this->alias]["id"]) && !empty($this->data[$this->alias]["id"])) {

			// Save UserProject each time when a project is updated
			// This will help to set last updated project on top while getting summary page
			$user_project = new UserProject();
			$project_id = $this->data[$this->alias]["id"];
 
		}
		foreach ($this->data[$this->alias] as $k => $v) {
			//$c = preg_replace('@<script[^>]*?.*?</script>@siu', '', $v);
			//$c = preg_replace('@<script>.*?@siu', '', $c);
			if(is_string($v)){
			$c = htmlspecialchars( $v);
			$this->data[$this->alias][$k] = $c;
			}
			
		}

		//$this->data = Sanitize::clean($this->data, array('encode' => true));
		//pr($this->data); die;
		return true;
	}	
	 

}
