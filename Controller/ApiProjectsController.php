<?php

/**
 * Users controller.
 *
 * This file will render views from views/Users/
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
App::uses('Controller/Component', 'Auth', 'Session', 'RequestHandler');
App::uses('CakeEmail', 'Network/Email');
App::uses('SimplePasswordHasher', 'Controller/Component/Auth');
App::import('Vendor', 'google-api');
App::uses('HttpSocket', 'Network/Http');
App::import('Vendor', 'firebase/src/JWT');
App::import('Lib', 'XmlApi');
/**
 * Users Controller
 *
 * @property User $User
 * @property PaginatorComponent $Paginator
 */
class ApiProjectsController extends AppController {

	var $name = 'ApiProjects';
	
	public $components = array('Email', 'Common', 'Image', 'CommonEmail', 'Auth', 'Group');

	public $live_setting;

	public $mongoDB = null;
	/**
	 * Helpers
	 *
	 * @var array
	 */
	public $helpers = array('Html', 'Form', 'Session', 'Time', 'Text', 'Common', 'Group', 'Wiki', 'User', 'ViewModel');

	public function beforeFilter() {
		
		parent::beforeFilter();
		$this->Auth->allow('get_project_details');
		$view = new View();
		$this->objView = $view;		
		$this->live_setting = LIVE_SETTING;		
		$response = $this->api_check_user();
		
		$this->renderApiResponse($response);		
	}
	public function get_project_details() 
	{ 
		$response = array();
		$data = array();
		
		$project_id 			=   ( !empty($this->request->data['project_id']) ? $this->request->data['project_id']  : ( !empty($this->request->query['project_id']) ? $this->request->query['project_id'] : '' ) );
		
		if( empty($project_id))
		{
			$statusCode = 950;
			$message = 'Project Id is missing.';				
		}
		else
		{
			
			$this->loadModel('Project');
			$this->loadModel('UserProject');
			$this->loadModel('UserDetail');
			$this->loadModel('User');
			$this->loadModel('Workspace');
			$this->loadModel('ProjectSkill');
			$this->loadModel('Skill');
			$this->Project->bindModel(array('belongsTo'=>array('Currency'=>array('className' => 'Currency'),'Category'=>array('className' => 'Category'),'Aligned'=>array('className' => 'Aligned'))));			
			$project = $this->Project->findById($project_id);
			if( empty($project)) 
			{
				$statusCode = 950;
				$message = 'No project exists with this  #'.$project_id.' id .';
			}
			else
			{
				$user = $this->User->find('all',array('fields'=>array('User.id','User.email','UserDetail.first_name','UserDetail.last_name','UserDetail.contact','UserDetail.profile_pic'),'conditions'=>array('User.id'=>$project['UserProject'][0]['user_id'])));
				$this->User->unbindModel(array('belongsTo'=>array('UserInstitution','OrganisationUser','ProjectPermission','WorkspacePermission','ElementPermission','UserProject','UserPlan','UserSetting','Skill')));
				
				$project['User'] = $user[0]['User'];
				$project['Workspaces'] = array();
				
				$workSpIds = array();
				foreach( $project['ProjectWorkspace'] as $projWorkspace)
				{
					$workSpIds[] = $projWorkspace['workspace_id'];
				}
				$this->Workspace->unbindModel(array('hasMany'=>array('ProjectWorkspace','Area','ElementPermission')));
				$workspaces = $this->Workspace->find('all',array('fields'=>array('Workspace.id','Workspace.title','Workspace.description'),'conditions'=>array('Workspace.id'=>$workSpIds)));
				$project['Workspaces'] = array();

				foreach($workspaces as $worksp)
				{
					$project['Workspaces'][] = $worksp['Workspace'];
				}
				
				$project['Creator'] = array(
					'first_name'=>$user[0]['UserDetail']['first_name'],
					'last_name'=>$user[0]['UserDetail']['last_name'],
					'email'=>$user[0]['User']['email'],
					'contact'=>$user[0]['UserDetail']['contact'],
					'profile_pic'=>$user[0]['UserDetail']['profile_pic'])  ;
	
				$sharers = participants($project_id, $user[0]['User']['id']);
				//$sharers = array(1,25);
				if( !empty($sharers))
				{
					$sharersData = $this->User->find('all', array('fields'=>array('UserDetail.first_name','UserDetail.last_name','UserDetail.contact','UserDetail.profile_pic','User.email'),'conditions'=>array('User.id'=>array_unique($sharers))));			
					
					foreach($sharersData as $sharerData)
					{
						$project['Sharers'][] =  array(
														'first_name'=>$sharerData['UserDetail']['first_name'],
														'last_name'=>$sharerData['UserDetail']['last_name'],
														'email'=>$sharerData['User']['email'],
														'contact'=>$sharerData['UserDetail']['contact'],
														'profile_pic'=>$sharerData['UserDetail']['profile_pic'])  ;
					}
				}
				
				$owners = participants_owners($project_id, $user[0]['User']['id']);
				
				if( !empty($owners))
				{
					$ownersData = $this->User->find('all', array('fields'=>array('UserDetail.first_name','UserDetail.last_name','UserDetail.contact','UserDetail.profile_pic','User.email'),'conditions'=>array('User.id'=>array_unique($owners))));			
					
					foreach($ownersData as $ownerData)
					{
						$project['Owners'][] = array(
							'first_name'=>$ownerData['UserDetail']['first_name'],
							'last_name'=>$ownerData['UserDetail']['last_name'],
							'email'=>$ownerData['User']['email'],
							'contact'=>$ownerData['UserDetail']['contact'],
							'profile_pic'=>$ownerData['UserDetail']['profile_pic']) ;
					}
				}
				
				
				$groupOwners = participants_group_owner($project_id);
				if( !empty($groupOwners))
				{
					$groupOwnersData = $this->User->find('all', array('fields'=>array('UserDetail.first_name','UserDetail.last_name','UserDetail.contact','UserDetail.profile_pic','User.email'),'conditions'=>array('User.id'=>array_unique($groupOwners))));			
					foreach($groupOwnersData as $groupOwnerData)
					{
						$project['GroupOwners'][] = array(
							'first_name'=>$groupOwnerData['UserDetail']['first_name'],
							'last_name'=>$groupOwnerData['UserDetail']['last_name'],
							'email'=>$groupOwnerData['User']['email'],
							'contact'=>$groupOwnerData['UserDetail']['contact'],
							'profile_pic'=>$groupOwnerData['UserDetail']['profile_pic']) ;
					}
				}
				$groupSharers = participants_group_sharer($project_id);
				if( !empty($groupSharers))
				{
					$groupSharersData = $this->User->find('all', array('fields'=>array('UserDetail.first_name','UserDetail.last_name','UserDetail.contact','UserDetail.profile_pic','User.email'),'conditions'=>array('User.id'=>array_unique($groupSharers))));	
					foreach($groupSharersData as $groupSharerData)
					{
						$project['GroupSharers'][] = array(
							'first_name'=>$groupSharerData['UserDetail']['first_name'],
							'last_name'=>$groupSharerData['UserDetail']['last_name'],
							'email'=>$groupSharerData['User']['email'],
							'contact'=>$groupSharerData['UserDetail']['contact'],
							'profile_pic'=>$groupSharerData['UserDetail']['profile_pic']) ;
					}
				}
	
				
				$project['Project']['rag_current_status'] = ( $project['Project']['rag_current_status'] == 1 ? "red" : ($project['Project']['rag_current_status']== 2 ? "Amber" : "Green"));
				$project['Project']['current_status'] = ( $project['Project']['sign_off'] == 1 ? "Sign Off" : ( $project['Project']['sign_off'] !== 1 ? get_project_activities($project_id) : "Open"));
				$project['Project']['status'] = projectStatus($project['Project']['start_date'], $project['Project']['end_date'] , $project_id,$project['Project']['sign_off']);
				$project['Project']['created'] = date('Y-m-d',$project['Project']['created']);
				$project['Project']['modified'] = date('Y-m-d',$project['Project']['modified']);
	
				$project['Project']['start_date'] = date('Y-m-d',strtotime($project['Project']['start_date']));
				$project['Project']['end_date'] = date('Y-m-d',strtotime($project['Project']['end_date']));
				if( !empty($project['ProjectSkill']) )
				{
					foreach($project['ProjectSkill'] as $projectSkill)
					{					
						$project['Skills'][] = $this->Skill->find('all',array('conditions'=>array('Skill.id'=>$projectSkill['skill_id']),'fields'=>array('Skill.title')));
					}
				}
				unset($project['ProjectSkill']);
				$keysArray = array(
						'Project' => array('id','title','objective','description','rag_current_status','created','modified','start_date','end_date','current_status','color_code','status'),
						'Category' => array('id','title'),
						'Currency' => array('id','name'),
						'Aligned'  => array('id','title')
				);	
				



				$project = $this->filterFields($keysArray,$project);
				unset($project['UserProject']);
				unset($project['ElementPermission']);
				unset($project['ProjectWorkspace']);
				unset($project['WorkspacePermission']);
				if( !empty($project) )
				{
					$statusCode = 200;
					$message = 'Project '.$project_id.' \'s data.';
					$data = $project ;
				}
				else
				{
					$statusCode = 950;
					$message = 'No project exists with this  #'.$project_id.' id .';
				}
			}			
			
		}
		$this->set([
	            'message' => $message,
				'statusCode' => $statusCode,
				'data' => $data,
	            '_serialize' => ['statusCode', 'message', 'data']
	        ]);
	}
	
	
	
	
}
