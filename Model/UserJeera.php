<?php

App::uses('AppModel', 'Model');

/**
 * User Model
 *
 */
  
class UserJeera extends AppModel {
	
	var $alias =  'User' ;
	var $name = 'UserJeera';
     
	var $useDbConfig = 'jeera';
	public $useTable = 'users';
   
	 public $hasOne = array(
        'UserDetail' => array(
            'className' => 'UserDetailJeera',
            'dependent' => true
        ),
        'UserInstitution' => array(
            'className' => 'UserInstitution',
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
        'UserPlan' => array(
            'className' => 'UserPlan',
        //  'dependent'=> true
        ),
        'UserTransctionDetail' => array(
            'className' => 'UserTransctionDetail',
            'conditions' => array('UserTransctionDetail.user_id' => 'id'),
        //  'dependent'=> true
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
	
	
		
		 
  

}
