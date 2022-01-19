<?php
App::uses('AppModel', 'Model');

class ElementLink extends AppModel {
	
    var $name = 'ElementLink';
	
    var $belongsTo = array (
			'Element'  => [ 'dependent' => true ]
		);
		
	var $validate = null;
	
	function __construct($id = false, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);

		$this->setValidationRules();
	}

	function setValidationRules($condition = null) {
		$validations = array(
				'element_id' => array(
					'required' => array(
						'rule' => array(EMPTY_MSG),
						'message' => 'Link with an element is required.'
					) 
				), 
				'title' => array(
					'required' => array(
						'rule' => array(EMPTY_MSG),
						'message' => 'Title is required.'
					) 
				)
			); 
		 
		if ($condition == 1) {
			
			$validations = array_merge($validations, array(  
						'references' => array(
							
							'required' => array(
								'rule' => array(EMPTY_MSG),
								'message' => 'Link is required.'
							),
							'url' => array(
								'rule' => 'url',
								'message' => 'Not a valid URL.',
								// 'allowEmpty' => true
							),
						),
					)
				); 
		}
		/* else if ($condition == 2) {
			$validations = array_merge($validations, array(
					'media_link' => array( 
						'required' => array(
							'rule' => array('notEmpty'),
							'message' => 'Media file URL is required.'
						), 
						'url' => array(
							'rule' => 'url',
							'message' => 'Not a valid URL.'
						)
					),
				)
			); 
			
		} */
		else if ($condition == 2) {
			$validations = array_merge($validations, array(
					'embed_code' => array( 
						'required' => array(
							'rule' => array(EMPTY_MSG),
							'message' => 'Embeded code is required.'
						) 
					),
				)
			); 
			
		}
		$this->validate = $validations;
	}
	 
	
	
}