<?php
App::uses('AppModel', 'Model');

class CategoryAll extends AppModel {

	// var $actsAs = array('Containable');

	var $name = 'Category';

	var $displayField = 'title';
	
	var $useTable = 'categories';
	
	var $alias  = 'Category';

	var $hasMany = array(
					'Project'
				);
	/* public $belongsTo = array(
		'Parent' => array(
			'className' => 'Category',
			'foreignKey' => 'parent_id'
		)
    );

    public $hasMany = array(
		'Children' => array(
			'className' => 'Category',
			'foreignKey' => 'parent_id'
		),
		'Project'
    ); */

    public function beforeFind($queryData = null ) {
		parent::beforeFind($queryData);
		$uid = CakeSession::read("Auth.User.id");

		$conditions = [];
		if( isset($queryData['conditions']) && !empty($queryData['conditions'])) {
			$conditions  = $queryData['conditions'];
		}
		/* if( !isset($conditions['id']) ) {
			$queryData['conditions'] = array_merge( $conditions, [
				[
					'OR' => [
						//'Category.user_id' => $uid,
						//'Category.user_id IS NULL',
						'Category.is_default' => 1
					]
				]
			]);
		} */
		
		//return $queryData;
    }

	/**
	 * checks is the field value is unqiue in the table
	 * note: we are overriding the default cakephp isUnique test as the
		original appears to be broken
	 *
	 * @param string $data Unused ($this->data is used instead)
	 * @param mixed $fields field name (or array of field names) to
		validate
	 * @return boolean true if combination of fields is unique
	 */
	function checkUnique($data, $fields) {
		// if (isset($this->data[$this->title][$this->parent_id])) 
		$id = $parent_data = null;
	
		$uid = CakeSession::read("Auth.User.id");
		
		$conditions = null;
		$where = null;
		$checkId = null;
		if( !is_null($this->data) ) {
			
			if( isset($this->data['Category']['id']) && !empty($this->data['Category']['id'])) {
				$where[] = "Category.id != '".$this->data['Category']['id']."'";
				// $conditions['Category.id !='] = $this->data['Category']['id'];
				$checkId = $this->data['Category']['id'];
			} 
			else{
				$checkId = $this->data['Category']['parent_id'];
			}
		
		
			
			$list = get_duplicate($checkId);
			
			if( !empty($list) && is_array($list)) {
				$where[]  = "( Category.id IN (".implode(',', $list).")" . " OR Category.parent_id IN (".implode(',', $list).") )"; 
			}
			
			$where[] = "Category.title = '".$this->data['Category']['title']."'";
			//$where[] = "Category.user_id = '".$uid."'";
			
			$conditions = implode(' AND ', $where);
			
			$query = "SELECT COUNT(*) AS count FROM categories Category WHERE " . $conditions;
			// pr($query, 1);
			$count_res = $this->query( $query ) ;
			$count = 0;
			$count_res = Set::extract($count_res, '/0/0');
			foreach( $count_res as $k => $val) {
				$count = $val['count'];
			}
			
			if( !empty($count) ) {
				return false;
			}
			
		}
		return true;
		
	}
	
	/** 
	 * checks is the field value is unqiue in the table 
	 * note: we are overriding the default cakephp isUnique test as the original appears to be broken 
	 * 
	 * @param string $data Unused ($this->data is used instead) 
	 * @param mnixed $fields field name (or array of field names) to validate 
	 * @return boolean true if combination of fields is unique 
	 */ 
        function checkUniqueNew($data, $fields) { 
                if (!is_array($fields)) { 
                        $fields = array($fields); 
                } 
                foreach($fields as $key) { 
                        $tmp[$key] = $this->data[$this->name][$key]; 
                } 
                if (isset($this->data[$this->name][$this->primaryKey])) { 
                        $tmp[$this->primaryKey] = "<>".$this->data[$this->name][$this->primaryKey]; 

                } 
                return $this->isUnique($tmp, false); 
        } 
 


	var $validate = array(
			'title' => array(
					'required' => array(
					'rule' => array(EMPTY_MSG),
					'message' => 'Title is required.'
			),
			"unique"=>array(
				"rule"=>array("checkUnique", array("title", "parent_id")),
					"message"=>"Title already exists."
			),
			'minlength' => array(
				'rule' => array('minlength', 1),
				'message' => 'Minimum length is 1 chars.',
			),
			'maxlength' => array(
				'rule' => array('maxlength', 100),
				'message' => 'Maximum length is 100 chars.',
			)
		)

	);



}