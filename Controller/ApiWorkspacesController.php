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
class ApiWorkspacesController extends AppController {

	var $name = 'ApiWorkspaces';
	
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
		$this->Auth->allow('get_workspace_details');
		$view = new View();
		$this->objView = $view;		
		$this->live_setting = LIVE_SETTING;		
		$response = $this->api_check_user();
		$this->renderApiResponse($response);		
	}
	public function get_workspace_details() 
	{ 
		$response = array();
		$data = array();
		
		$workspace_id 			=   ( !empty($this->request->data['workspace_id']) ? $this->request->data['workspace_id']  : ( !empty($this->request->query['workspace_id']) ? $this->request->query['workspace_id'] : '' ) );
		
		if( empty($workspace_id))
		{
			$statusCode = 950;
			$message = 'WorkSpace Id is missing.';				
		}
		else
		{
			$statusCode = 200 ;
			$message = 'WorkSpace Found.';
			$this->loadModel('ProjectWorkspace');
			$this->loadModel('Project');$this->loadModel('Template');
		 
			//$this->ProjectWorkspace->unbindModel(['hasMany' => 'ElementPermission']);


			

			$workspaces = $this->ProjectWorkspace->findByWorkspaceId($workspace_id);
			
			if( !empty($workspaces))
			{
				$template_id = $workspaces['Workspace']['template_id'];
				$template = $this->Template->findById($template_id);
				if( !empty($template['Template']))
				{
					$workspaces['WorkspaceTemplate'] = $template['Template'] ;
				}
	
				$project_id = $workspaces['Project']['id'];

				$this->Project->bindModel(array('belongsTo'=>array('Currency'=>array('className' => 'Currency'),'Category'=>array('className' => 'Category'),'Aligned'=>array('className' => 'Aligned'))));	



				$project = $this->Project->findById($project_id);

				if( !empty($project['Project']))
				{
					$workspaces['WorkspaceProject'] = $project['Project'] ;
					$workspaces['WorkspaceProject']['rag_current_status'] = ( $workspaces['WorkspaceProject']['rag_current_status'] == 1 ? "red" : ($workspaces['WorkspaceProject']['rag_current_status']== 2 ? "Amber" : "Green"));
					$workspaces['WorkspaceProject']['current_status'] = ( $workspaces['WorkspaceProject']['sign_off'] == 1 ? "Sign Off" : ( $workspaces['WorkspaceProject']['sign_off'] !== 1 ? get_project_activities($project_id) : "Open"));
					$workspaces['WorkspaceProject']['created'] = date('Y-m-d',$workspaces['WorkspaceProject']['created']);
					$workspaces['WorkspaceProject']['modified'] = date('Y-m-d',$workspaces['WorkspaceProject']['modified']);
		
					$workspaces['WorkspaceProject']['start_date'] = date('Y-m-d',strtotime($workspaces['WorkspaceProject']['start_date']));
					$workspaces['WorkspaceProject']['end_date'] = date('Y-m-d',strtotime($workspaces['WorkspaceProject']['end_date']));
				}
				

				

				$workspaces['Workspace']['start_date'] = date('Y-m-d',strtotime($workspaces['Workspace']['start_date']));
				$workspaces['Workspace']['end_date'] = date('Y-m-d',strtotime($workspaces['Workspace']['end_date']));

					
				$workspaces['Workspace']['status'] = workspace_status($workspaces['Workspace']['id']);

				if( !empty($workspaces))
				{
					$this->loadModel('Area');
		
					$this->Area->unbindModel(array('hasMany'=>array('Elements'),'belongsTo'=>array('Workspace')));
					
					$areas = $this->Area->findByWorkspaceId($workspace_id);
					if( !empty($areas))
					{
						$workspaces['Area'] = $areas['Area'];
					}
					if( !empty($areas['TemplateDetail']))
					{
						$workspaces['TemplateDetail'] = $areas['TemplateDetail'];
					}
					$workspaces['Workspace']['current_status'] = ( $workspaces['Workspace']['sign_off'] == 1 ? "Sign Off" : ( $workspaces['Workspace']['sign_off'] !== 1 ? get_workspace_activities($workspace_id) : "Open"));
			
					$keysArray = array(
						'ProjectWorkspace' => array('id','project_id','workspace_id'),
						'Project' => array('id','title','objective','description'),
						'Workspace' => array('id','title','description','created','modified','status','current_status','color_code','start_date','end_date'),
						'Area' => array('id','title','description','template_detail_id','tooltip_text'),
						'TemplateDetail' => array('id','template_id'),
						'WorkspaceProject' =>array('id','title','objective','description','rag_current_status','created','modified','start_date','end_date','sign_off','color_code'),
						'WorkspaceTemplate'=>array('id','title','description')
						
					);	
					$workspaces = $this->filterFields($keysArray,$workspaces);
					$workspaces['Workspace']['created'] = date('Y-m-d',strtotime($workspaces['Workspace']['created']));
					$workspaces['Workspace']['modified'] = date('Y-m-d',strtotime($workspaces['Workspace']['modified']));
					unset($workspaces['ProjectWorkspace']);
					unset($workspaces['Project']);
					unset($workspaces['WorkspacePermission']);
					unset($workspaces['TemplateDetail']);
				}
				else
				{
					$statusCode = 950;
					$message = 'No Workspaces exists with this  #'.$workspace_id.' id .';
				}
			}
			else
			{
				$statusCode = 950;
				$message = 'No Workspaces exists with this  #'.$workspace_id.' id .';
			}			
		}
		$this->set([
	            'message' => $message,
				'statusCode' => $statusCode,
				'data' => (!empty($workspaces)?$workspaces:array()),
	            '_serialize' => ['statusCode', 'message', 'data']
	        ]);
	}
}