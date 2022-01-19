<?php
App::uses('AppModel', 'Model');
/**
 * ProjectGroups Model
 *
 */
class ProjectGroup extends AppModel {
	
	var $hasMany = array(
			'ProjectGroupUser' => [
				'className' => 'ProjectGroupUser',
				'foreignKey' => 'project_group_id',
				'dependent' => true
			],
		);

	var $hasOne = array(
			'ProjectPermission' => [
				'className' => 'ProjectPermission',
				'foreignKey' => 'project_group_id',
				//'dependent' => true
			],
			'UserProject' => [
				'className' => 'UserProject',
				'foreignKey' => false,				 
				'conditions' => array('ProjectGroup.user_project_id = UserProject.id')
				//'dependent' => true
			],
		);		
		
    public $validate = array(
        'title' => array(
            'required' => array(
                'rule' => array(EMPTY_MSG),
                'message' => 'Project group title is required'
            ),
        ),
        'group_owner_id' => array(
            'required' => array(
                'rule' => array(EMPTY_MSG),
                'message' => 'Project owner is required'
            ),
        ),
    );
 
    
}