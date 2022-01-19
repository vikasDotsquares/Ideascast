<?php
App::uses('AppModel', 'Model');
/**
 * Country Model
 *
 */
class ProjectWorkspace extends AppModel {

    var $name = 'ProjectWorkspace';

    //var $belongsTo = array ('Project', 'Workspace');

	var $belongsTo =  [
			'Project' => [
				'dependent' => true
			],
			'Workspace'  => [
				'dependent' => true
			]
		] ;

	var $hasMany = [
            
            'WorkspacePermission' => [
                'className'     => 'WorkspacePermission',
                'dependent'=> true
            ],
		];
}