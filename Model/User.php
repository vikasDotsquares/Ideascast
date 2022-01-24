<?php

App::uses('AppModel', 'Model');

/**
 * User Model
 *
 */

class User extends AppModel {

    // public $actsAs = array('Containable');
    // $this->Post->Behaviors->load('Containable');
    // $this->Post->find('all', array('contain' => 'Tag'));

    public $hasOne = array(
        'UserDetail' => array(
            'className' => 'UserDetail',
            'dependent' => true
        ),
		'OrganisationUser' => array(
            'className' => 'OrganisationUser',
			 'dependent' => true
			//'conditions' => array('User.role_id' => 3)
        ),
    );

    public $hasMany = array(
        'ProjectPermission' => array(
            'className' => 'ProjectPermission',
            'dependent' => true
        ),
        'WorkspacePermission' => array(
            'className' => 'WorkspacePermission',
            'dependent' => true
        ),
        'ElementPermission' => array(
            'className' => 'ElementPermission',
            'dependent' => true
        ),
        'UserProject' => array(
            'className' => 'UserProject',
        //'dependent'=> true
        ),
        'UserSetting' => array(
            'className' => 'UserSetting',
            'conditions' => array('UserSetting.user_id' => 'id'),
        //  'dependent'=> true
        ),
        /* 'OrganisationUser' => array(
            'className' => 'OrganisationUser',
            'conditions' => array('OrganisationUser.user_id' => 'id')
        ) */
		'UserPassword' => array(
            'className' => 'UserPassword',
			'dependent' => true
        ),
    );

    public $hasAndBelongsToMany = array(
        'Skill' => array(
            'className' => 'Skill',
            'joinTable' => 'user_skills',
            'foreignKey' => 'user_id',
            'associationForeignKey' => 'skill_id',
            'unique' => 'keepExisting',
            'dependent' => true,
        ),
        'Subject' => array(
            'className' => 'Subject',
            'joinTable' => 'user_subjects',
            'foreignKey' => 'user_id',
            'associationForeignKey' => 'subject_id',
            'unique' => 'keepExisting',
            'dependent' => true,
        ),
        'Domain' => array(
            'className' => 'Domain',
            'joinTable' => 'user_domains',
            'foreignKey' => 'user_id',
            'associationForeignKey' => 'domain_id',
            'unique' => 'keepExisting',
            'dependent' => true,
        )
    );

    public $validate = array(
        'email' => array(
            'required' => array(
                'rule' => array(EMPTY_MSG),
                'message' => 'Email is required'
            ),
            'email' => array(
                'rule' => array('email'),
                'message' => 'Valid email is required',
            ),
            'unique' => array(
                'rule' => array('isUnique', 'email'),
                'message' => 'This email has already been taken',
            //'on' => 'create'
            )
        ),
        'current_password' => array(
            'notempty' => array(
                'rule' => EMPTY_MSG,
                'required' => false,
                'message' => 'Current Password is required',
            ),
            'password_exists' => array(
                'rule' => array('password_exists'),
                'message' => 'Current Password is incorrect'
            )
        ),
        'password' => array(
            /* 'alphaNumeric' => array(
              'rule' => 'alphaNumeric',
              'required' => true,
              'message' => 'Must be at least 8 characters Which includes 1 number character.',
              'on' => 'create'
              ), */
            'required' => array(
                'rule' => array(EMPTY_MSG),
                'message' => 'New Password is required',
                'on' => 'create'
            )
        ),
        'cpassword' => array(
            'required' => array(
                'rule' => array(EMPTY_MSG),
                'message' => 'Confirm Password is required',
                'on' => 'create'
            ),
            'match_passwds' => array(
                'rule' => 'matchPasswds',
                'required' => false,
                'message' => 'New Password and Confirm Password must match',
            ),
        ),
    );

    function password_exists() {
        if (isset($this->data[$this->alias]['current_password']) && !empty($this->data[$this->alias]['current_password'])) {
            $user_id = CakeSession::read("Auth.User.id");
            if ($this->find('count', array('conditions' => array('User.password' => AuthComponent::password($this->data[$this->alias]['current_password']), 'User.id' => $user_id)))) {
                return true;
            }
            return false;
        }
    }

    function beforeValidate($options = array()) {


    }

    function checkEmailInForgotPasswordValidate() {
        $validate1 = array(
            'email_for_forget_password' => array(
                'email' => array(
                    'rule' => array('email'),
                    'message' => 'Please enter valid email address',
                ),
            ),
        );
        $this->validate = $validate1;
        return $this->validates();
    }

    public function beforeSave($options = array()) {

		if (isset($this->data[$this->alias]['password']) && !empty($this->data[$this->alias]['password'])) {
            $this->data[$this->alias]['password'] = AuthComponent::password($this->data[$this->alias]['password']);

        } else {
            unset($this->data[$this->alias]['password']);
        }

		foreach($this->data[$this->alias] as $k => $v) {
           $c = preg_replace('@<script[^>]*?.*?</script>@siu', '', $v);
		   $c = preg_replace('@<script>.*?@siu', '', $c);
           $this->data[$this->alias][$k] = $c;
        }


        return true;
    }

    function matchPasswds() {
        $data = $this->data;
		if(isset($data[$this->alias]['password']) && !empty($data[$this->alias]['password'])){
        return $data[$this->alias]['password'] == $data[$this->alias]['cpassword'];
		}
		return true;
    }

    public function afterFind($results, $primary = false) {
        App::uses('CakeTime', 'Utility');
        foreach ($results as $key => $val) {
            if (isset($val[$this->alias]['created'])) {
                $results[$key][$this->alias]['created'] = CakeTime::format(ADMIN_DATE_FORMAT, $val[$this->alias]['created'], null, TIME_ZONE);
            }
        }
        return $results;
    }

    public function getActivationHash() {
        /* if (!isset($this->id)){
          return false;
          } */
        return substr(Security::hash(Configure::read('Security.salt') . $this->field('created') . rand(10, 100)), 0, 8) . rand(10, 100);
    }

	/**
	 * Returns an array of skills based on a skill id
	 * @param string $skill_id - the id of a skill
	 * @return array of skills
	 */
	public function getUsersBySkill1111($filters = null, $skill_id = null, $user_ids = null, $fields = null) {
		/* //  return false;
		$conditions = null;
		// $conditions = [ 'OR' => ['UserDetails.first_name LIKE' => "%$filters%", 'UserDetails.last_name LIKE' => "%$filters%"]];

		if( isset($user_ids) && !empty($user_ids) ) {
			// $conditions = array_merge($conditions, ['UserSkill.user_id' => $user_ids]);
		}

		if( !isset($fields) || !empty($fields) ) {
			$fields = ['UserDetails.user_id', 'UserDetails.first_name', 'UserDetails.last_name'];
		}

		$this->unbindAll();
		$this->bindModel(['hasMany' => ['UserSkill']]);

		$this->recursive = 1;
		$skill_join = $skill_cond = $user_skill_cond = $user_skill_join = null;

		if( !empty($skill_id)) {
			$user_skill_join = ['table' => 'user_skills', 'alias' => 'UserSkill', 'type' => 'INNER', 'conditions' => ['UserSkill.user_id = User.id']];
			$skill_join = ['table' => 'skills', 'alias' => 'Skills', 'type' => 'LEFT', 'conditions' => ['Skills.id = UserSkill.skill_id']];

			$user_skill_cond['UserSkill.user_id'] = $user_ids;
			$user_skill_cond['UserSkill.skill_id'] = $skill_id;
			$skill_cond = [ 'Skills.title' => "$filters%" ];
		}
		$skills = $this->find('all', array(
			'joins' => array(
				array('table' => 'user_details',
					'alias' => 'UserDetails',
					'type' => 'INNER',
					'conditions' => ['UserDetails.user_id = User.id']
				) ,
				$user_skill_join,
				$skill_join,
			),
			'conditions' =>[
					'OR' => [$user_skill_cond],
					'OR' => [$skill_cond],
					'OR' => [
						'UserDetails.first_name LIKE' => "%$filters%",
						'UserDetails.last_name LIKE' => "%$filters%"
					],
				],
			'fields' => $fields,
			'group' => 'User.id'
		));
		// pr($this->_query());
		// pr($user_skill_cond);
		 */

		$query = '';

		$query .= "SELECT  UserDetails.user_id, UserDetails.first_name, UserDetails.last_name \n";
		$query .= "FROM users U \n";
		$query .= "LEFT JOIN user_details UserDetails ON UserDetails.user_id = U.id \n";
		$query .= "LEFT JOIN user_skills US ON US.user_id = U.id \n";
		$query .= "LEFT JOIN skills S ON S.id = US.skill_id \n";
		$query .= "WHERE \n";
		$query .= "US.user_id IN (".implode(",", $user_ids).") \n";
		$query .= "AND \n";
		$query .= "(UserDetails.first_name LIKE '%$filters%' \n";

		if( !empty($skill_id)) {
			$query .= "OR \n";
			$query .= "US.skill_id IN (".implode(",", $skill_id).") \n";
		}

		$query .= "OR \n";
		$query .= "UserDetails.last_name LIKE '%$filters%') \n";
		// $query .= "OR \n";
		// $query .= "S.title LIKE '$filters%' \n";
		$query .= "GROUP BY US.user_id";
		$udata = $this->query($query);
		// echo $query;
		return $udata;
	}

	public function getUsersBySkill($filters = null, $skill_id = null, $user_ids = null ) {

		$filter_rep =  str_replace(' ', '|', trim($filters));

		$query = '';

		$query1 = '';
		$query1 .= "SELECT UserDetails.user_id, UserDetails.first_name, UserDetails.last_name \n";
		$query1 .= "FROM users U \n";
		$query1 .= "LEFT JOIN user_details UserDetails ON UserDetails.user_id = U.id \n";
		$query1 .= "WHERE \n";
		$query1 .= "UserDetails.user_id IN (".implode(",", $user_ids).") \n";
		$query1 .= "AND \n";
		$query1 .= "(UserDetails.first_name REGEXP '((".$filter_rep.").*)()*' \n";
		$query1 .= "OR \n";
		$query1 .= "UserDetails.last_name REGEXP '((".$filter_rep.").*)()*') \n";
		// $query1 .= "GROUP BY UserDetails.user_id \n";

		$query2 = '';
		$query2 .= "SELECT UserDetails.user_id, UserDetails.first_name, UserDetails.last_name \n";
		$query2 .= "FROM users U \n";
		$query2 .= "LEFT JOIN user_details UserDetails ON UserDetails.user_id = U.id \n";
		$query2 .= "LEFT JOIN user_skills US ON US.user_id = U.id \n";
		$query2 .= "WHERE \n";
		$query2 .= "US.user_id IN (".implode(",", $user_ids).") \n";
		if( !empty($skill_id) ) {
		$query2 .= "AND \n";
		$query2 .= "US.skill_id IN (".implode(",", $skill_id).") \n";
		}
		$query2 .= "GROUP BY UserDetails.user_id";

		if( !empty($skill_id) ) {
			$query .= $query1."UNION \n".$query2;
		}
		else {
			$query .= $query1;
		}
		// echo $query1."UNION \n".$query2;
		// die;
		$udata = $this->query($query);
		// pr($udata, 1);
		return $udata;
	}




}
