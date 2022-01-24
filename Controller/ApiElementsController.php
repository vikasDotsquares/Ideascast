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
class ApiElementsController extends AppController {

	var $name = 'ApiElements';
	
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
		$this->Auth->allow('get_elements_details','create','update_element');
		$view = new View();
		$this->objView = $view;
		
		

		$this->live_setting = LIVE_SETTING;		
		$response = $this->api_check_user();
		$this->renderApiResponse($response);		
	}

	public function update_element() 
	{
		$errors 				= array();
		$data					= array(); 
		$element_id 			=   ( !empty($this->request->data['element_id']) ? $this->request->data['element_id']  : ( !empty($this->request->query['element_id']) ? $this->request->query['element_id'] : '' ) );
		$title 					=   ( !empty($this->request->data['title']) ? $this->request->data['title']  : ( !empty($this->request->query['title']) ? $this->request->query['title'] : '' ) );
		$description 			=   ( !empty($this->request->data['desc']) ? $this->request->data['desc']  : ( !empty($this->request->query['desc']) ? $this->request->query['desc'] : '' ) );
		$comments 				=   ( !empty($this->request->data['comments']) ? $this->request->data['comments']  : ( !empty($this->request->query['comments']) ? $this->request->query['comments'] : '' ) );
		$start_date 			=   ( !empty($this->request->data['start_date']) ? $this->request->data['start_date']  : ( !empty($this->request->query['start_date']) ? $this->request->query['start_date'] : '' ) );
		$end_date 				=   ( !empty($this->request->data['end_date']) ? $this->request->data['end_date']  : ( !empty($this->request->query['end_date']) ? $this->request->query['end_date'] : '' ) );	
		$message				=   '';
		
		if( empty($element_id))
		{
			$statusCode = 950;
			array_push($errors,'Element Id is missing.');						
		}
		else
		{
			if( $title == '' && $description == '' && $comments == '' && $start_date == '' && $end_date == '' )
			{
				$statusCode = 950;				
				array_push($errors,'You need to specified atleast one field.');
			}
			else
			{
				$this->loadModel('Element');
				$elementExists = $this->Element->exists($element_id);
				if( !$elementExists )
				{
					$statusCode = 950;				
					array_push($errors,'Element id not found.');
	
				}
				else
				{
					$element = $this->Element->findById($element_id);						
					if( !empty($element))
					{
						$this->loadModel('Project');
						$this->loadModel('User');
						$this->loadModel('Workspace');
						$this->loadModel('Area');
						$this->loadModel('UserProject');
						$this->loadModel('ElementPermission');
						$this->loadModel('ProjectWorkspace');
						$this->loadModel('ProjectPermission');					
						
						$workspace_id = element_workspace($element_id);								
						$workspace = $this->Workspace->findById($workspace_id);		

						$project_id = element_project($element_id);
						if( $title != '' )
							$data['Element']['title'] = $title;
						if( $description != '' )
							$data['Element']['description'] = $description;
						if( $comments != '' )
							$data['Element']['comments'] = $comments;
						
						
						if( !empty($data) && count($data) > 0 )
						{
							$this->Element->set($element['Element']);
							$data['Element']['id'] = $element_id ;
						}					
						$date_valid_message = array(0=>true,1=>'');					
						if( $start_date != '' || $end_date != '' )
						{
						
							if( $start_date != '' && empty($end_date) )
							{
								$statusCode = 950;				
							} 
							else if( empty($start_date)  && $end_date != '' )
							{
								$statusCode = 950;				
								array_push($errors,'You need to provide the start date.');
							}	
							if( empty($errors))
							{
								
								
								if ($element['Element']['date_constraints'] < 1) 
								{
									//$data['Element']['start_date']  = null;
									//$data['Element']['end_date'] 	= null;
								}
								else
								{
									
									if( $end_date >= $start_date )
									{		
																							
										$workspace_start_date = $workspace['Workspace']['start_date'];								
										$workspace_end_date = $workspace['Workspace']['end_date'];	
											
										if ( $start_date < $workspace_start_date ) 
										{
											$statusCode = 950;			
											$date_valid_message = array(0=>false,1=>'Start date ('.$start_date.') should be greater than to workspace start date ('.$workspace_start_date.').');
											array_push($errors,$date_valid_message[1]);
										}
										else if ($end_date > $workspace_end_date ) 
										{
											$statusCode = 950;			
											$date_valid_message = array(0=>false,1=>'End date ('.$end_date.') should be greater than to workspace end date ('.$workspace_end_date.').');
											array_push($errors,$date_valid_message[1]);
										}
										else
										{
											$statusCode = 200;			
											$date_valid_message = array(0=>true,1=>'');
											$data['Element']['start_date']  = $start_date;
											$data['Element']['end_date'] 	= $end_date;
											$data['Element']['date_constraints'] = 1;
										}
									}
									else
									{
										$statusCode = 950;
										$date_valid_message = array(0=>false,1=>'End Date should be greater than to Start Date.');
										array_push($errors,$date_valid_message[1]);								
									}
								}
							}
						}
						if(empty($errors))
						{
							
							if ($this->Element->save($data))
							{
								$statusCode = 200;				
								$message = "Element Updated Successfully";
							}
						}
					}				
				}
			}
			$newData = $this->Element->findById($element_id)['Element'];
			if ($element['Element']['date_constraints'] < 1) 
			{
				
				unset($newData['start_date']);unset($newData['end_date']);
			}

			if( !empty($message) && empty($errors)) 
			{
				
				
				
				$this->set([
	            'message' => $message,
				'statusCode' => $statusCode,
				'data' => $newData,
	            '_serialize' => ['statusCode', 'message', 'data']
	        	]);
			}	
			else
			{
				$this->set([
				'message' => $errors,
				'statusCode' => 950,
				'data' => array(),
				'_serialize' => ['statusCode', 'message', 'data']
				]);
			}		
		}
	}
	public function update_js_config($project_id = null, $workspace_id = null, $user_id = null) {

		$user_project = $this->objView->loadHelper('Common')->userproject($project_id, $user_id);
		$p_permission = $this->objView->loadHelper('Common')->project_permission_details($project_id, $user_id);

		$project_level = 0;

		// Get group id
		$grp_id = $this->objView->loadHelper('Group')->GroupIDbyUserID($project_id, $user_id);
		// Get Elements permissions
		$e_permission = $this->objView->loadHelper('Common')->element_permission_details($workspace_id, $project_id, $user_id);
		// Group permissions
		$group_permission = $this->objView->loadHelper('Group')->group_permission_details($project_id, $grp_id);
		// Project level according to the group permissions
		if (isset($group_permission['ProjectPermission']['project_level']) && $group_permission['ProjectPermission']['project_level'] == 1) {
			$project_level = $group_permission['ProjectPermission']['project_level'];
		}

		if ((isset($grp_id) && !empty($grp_id))) {

			if (isset($e_permission) && !empty($e_permission)) {
				$e_permissions = $this->objView->loadHelper('Group')->group_element_permission_details($workspace_id, $project_id, $grp_id);
				$e_permission = array_merge($e_permission, $e_permissions);
			} else {
				$e_permission = $this->objView->loadHelper('Group')->group_element_permission_details($workspace_id, $project_id, $grp_id);
			}
		}
		$areas = get_workspace_areas($workspace_id, false);
		$areaElements = null;
		if (isset($areas) && !empty($areas)) {
			// pr($areas, 1);
			foreach ($areas as $k => $v) {

				$elements_details_temp = null;
				if ((isset($e_permission) && !empty($e_permission))) {
					$all_elements = $this->objView->loadHelper('ViewModel')->area_elements_permissions($v['Area']['id'], false, $e_permission);
				}

				if (((isset($user_project) && !empty($user_project)) || (isset($project_level) && $project_level == 1) || (isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level'] == 1))) {
					$all_elements = $this->objView->loadHelper('ViewModel')->area_elements($v['Area']['id']);
				}

				if (isset($all_elements) && !empty($all_elements)) {

					foreach ($all_elements as $element_index => $e_data) {

						$element = $e_data['Element'];

						$element_decisions = $element_feedbacks = [];
						if (isset($element['studio_status']) && empty($element['studio_status'])) {
							$element_decisions = _element_decisions($element['id'], 'decision');
							$element_feedbacks = _element_decisions($element['id'], 'feedback');
							$element_statuses = _element_statuses($element['id']);

							$self_status['self_status'] = element_status($element['id']);

							$element_assets = element_assets($element['id'], true);
							$arraySearch = arraySearch($all_elements, 'id', $element['id']);

							if (isset($arraySearch) && !empty($arraySearch)) {
								$elements_details_temp[] = array_merge($arraySearch[0], $element_assets, $element_decisions, $element_feedbacks, $element_statuses, $self_status);
							}
						}
					}

					$areaElements[$v['Area']['id']]['el'] = $elements_details_temp;
				}
			}
		}
		return $areaElements;
	}
	public function update_task_up_activity($element_id = null,$user_id=null) {
		//$project_id = $this->Element->getProject($element_id);
		$element = $this->Element->findById($element_id);
		$area = $this->Area->findById($element['Element']['area_id']);
		
		$workspace_id = $area['Area']['workspace_id'];

		$workspace = $this->ProjectWorkspace->find("first", array("conditions" => array("ProjectWorkspace.workspace_id" => $workspace_id)));
		$project_id = $workspace['ProjectWorkspace']['project_id'];

		$date = date("Y-m-d h:i:s");

		/*
			$this->loadModel("Activity");
			$data = [
				'project_id' => $project_id,
				'workspace_id' => $workspace_id,
				'element_id' => $element_id,
				'element_type' => 'element_tasks',
				'relation_id' => $element_id,
				'user_id' => $user_id,
				'updated_user_id' => $user_id,
				'user_status' => '0',
				'message' => 'Task updated',
				'updated' => $date,
			];

			$this->Activity->save($data);
		*/

		$work_data = [
			'project_id' => $project_id,
			'workspace_id' => $workspace_id,
			'updated_user_id' => $user_id,
			'message' => 'Workspace updated',
			'updated' => $date,
		];
		$this->loadModel("WorkspaceActivity");
		$this->WorkspaceActivity->save($work_data);
		$project_data = [
			'project_id' => $project_id,
			'updated_user_id' => $user_id,
			'message' => 'Project updated',
			'updated' => $date,
		];

		$this->loadModel("ProjectActivity");
		$this->ProjectActivity->save($project_data);

	}
	public function update_project_modify($element_id = null,$user_id=null) {
		if (!isset($element_id) || empty($element_id)) {
			return true;
		}

		$project_id = $this->Element->getProject($element_id);

		if (!empty($project_id)) {
			$this->Common->projectModified($project_id, $user_id);
			// e('here');
		}
		return true;
	}
	
	public function getUserData($email) {
		$joins = array(
		 array(
		  'table' => 'users',
		  'alias' => 'User',
		  'type' => 'INNER',
		  'conditions' => array(
		   'User.id = UserDetail.user_id',
		  ),
		 ),
		); 
	 
	   $data = $this->UserDetail->find('first',array('conditions' => array('User.email' =>$email), 'joins' => $joins, 'fields' => array('*')));
	  return $data ;
	  } 
	
	
	public function create_element_assest() 
	{
		Configure::write('debug',2);
		//type will be notes,link,documents
		$errors					=   array();
		$model 					= "";
		$response 				= array();
		$data 					= array();
		$element_id 			=   ( !empty($this->request->data['element_id']) ? $this->request->data['element_id']  : ( !empty($this->request->query['element_id']) ? $this->request->query['element_id'] : '' ) );
		$email 					=   ( !empty($this->request->data['email']) ? $this->request->data['email']  : ( !empty($this->request->query['email']) ? $this->request->query['email'] : '' ) );
		$type					=   ( !empty($this->request->data['type']) ? $this->request->data['type']  : ( !empty($this->request->query['type']) ? $this->request->query['type'] : '' ) );
		$title					=   ( !empty($this->request->data['title']) ? $this->request->data['title']  : ( !empty($this->request->query['title']) ? $this->request->query['title'] : '' ) );
		$description			=   ( !empty($this->request->data['description']) ? $this->request->data['description']  : ( !empty($this->request->query['description']) ? $this->request->query['description'] : '' ) );
		$file_url				=   ( !empty($this->request->data['file_url']) ? $this->request->data['file_url']  : ( !empty($this->request->query['file_url']) ? $this->request->query['file_url'] : '' ) );
		$data = array();
		if( empty($element_id))
		{
			array_push($errors,'Element Id is missing.');				
		}
		if ( empty($type))
		{
			array_push($errors,'Asset Type is missing.');					
		}
		if ( empty($title))
		{
			array_push($errors,'Title is missing.');						
		}
		if ( empty($email))
		{
			array_push($errors,'Creator Email is missing.');						
		}
		else 
		{
			$this->loadModel('User');
			$this->loadModel('UserDetail');
			$user = $this->getUserData($email);
			
			if( !empty($user) )
			{
				$creator_id = $user['User']['id'];
				
			}
			else
			{
				array_push($errors,'No User found');
			}
		}
		
		if($type=='link' && empty($errors) ) 
		{
			$model = "ElementLink";
			$data[$model] = array();
			$link_type					=   ( !empty($this->request->data['link_type']) ? $this->request->data['link_type']  : ( !empty($this->request->query['link_type']) ? $this->request->query['link_type'] : '' ) );
			$link						=   ( !empty($this->request->data['link']) ? $this->request->data['link']  : ( !empty($this->request->query['link']) ? $this->request->query['link'] : '' ) );
			if( empty($link_type))
			{
				array_push($errors,'Link Type missing.');				
			}
			if( empty($link))
			{
				array_push($errors,'Link is missing.');				
			}
			$data[$model]['title'] 				= $title ;
			$data[$model]['element_id'] 		= $element_id ;		
			
			if( !empty($link_type) && !empty($link) )
			{
				$link_type = 2;
				$data[$model]['references'] 	= '' ;
				$data[$model]['embed_code'] 	= "<iframe width='560' height='315' src=".$link."></iframe>" ;
			}
			else
			{
				$link_type = 1;
				$data[$model]['references'] 	= $link ;
				$data[$model]['embed_code'] 	= '' ;
			}
			$data[$model]['creater_id'] 		= $creator_id ;
			$data[$model]['updated_user_id'] 	= $creator_id ;
			$data[$model]['link_type'] = $link_type ;
			
		}
		else if( $type=='notes') 
		{
			if ( empty($description))
			{
				array_push($errors,'Description is missing.');						
			}
			else
			{
				$model = "ElementNotes";
				$data[$model] = array();
				$data[$model]['title'] 				= $title ;
				$data[$model]['element_id'] 		= $element_id ;
				$data[$model]['creater_id'] 		= $creator_id ;
				$data[$model]['updated_user_id'] 	= $creator_id ;
				$data[$model]['description'] 		= $description ;
			}
		}
		else if( $type=='doc') 
		{
			if ( empty($file_url))
			{
				array_push($errors,'File Url is missing.');						
			}
			else
			{
				if(empty($errors))
				{
					$headers = get_headers($file_url);
					$mime_type = '';
					foreach($headers as $header) {
						if( strpos($header,"Content-Type:")!==false) 
						{
							$mime_type = str_replace(array('Content-Type:',' '),array('',''),$header);
						}
					}
					if($mime_type=='') 
					{
						array_push($errors,'File Mime type is not found.');			
					} 
					else 
					{
						$model = "ElementDocument";
						$data[$model] = array();
						$data[$model]['title'] 				= $title ;
						$data[$model]['element_id'] 		= $element_id ;
						$data[$model]['creater_id'] 		= $creator_id ;
						$data[$model]['updated_user_id'] 	= $creator_id ;
						$data[$model]['file_name'] 			= basename($file_url);
						$data[$model]['file_size'] 			= $this->getRemoteFilesize($file_url);
						
						if(!copy($file_url,WWW_ROOT."uploads/element_documents/".$element_id."/".basename($file_url))){
							array_push($errors,'File not exits on provided file url');						
							
						}else{
						
						$data[$model]['file_type'] 			= $mime_type;
						$data[$model]['feedback_attachments_id'] = 0;
						
						}
					}
				}
				else
				{
					array_push($errors,'File not exits on provided file url');
				}
			}
		}
		$data[$model]['status'] = 1 ;
		$data[$model]['is_search'] = 1 ;

		if( empty($errors)) {
		
			
			$this->loadModel($model);
			if($this->$model->save($data))
			{
				$model_id = $this->$model->getLastInsertID();
				$data = $this->$model->findById($model_id );
				$this->set([
						'message' => $model ." created successfully" ,
						'statusCode' => (!empty($errors) ? 950 : 200 ),
						'data' => $data,
						'_serialize' => ['statusCode', 'message', 'data']
				]);
			}
		} 
		else 
		{
			$this->set([
						'message' => (!empty($errors) ? implode("\n ",$errors) : "Error occured !..." ),
						'statusCode' => (!empty($errors) ? 950 : 200 ),
						'data' => array(),
						'_serialize' => ['statusCode', 'message', 'data']
				]);
		
		}
		
	}
	
	
	
	public function create() {
		Configure::write('debug', 2);
		$response = array();
		$data = array();
		
		$project_id 			=   ( !empty($this->request->data['project_id']) ? $this->request->data['project_id']  : ( !empty($this->request->query['project_id']) ? $this->request->query['project_id'] : '' ) );
		$workspace_id			= 	( !empty($this->request->data['workspace_id']) ? $this->request->data['workspace_id']  : ( !empty($this->request->query['workspace_id']) ? $this->request->query['workspace_id'] : '' ) );
		$area_id				= 	( !empty($this->request->data['area_id']) ? $this->request->data['area_id']  : ( !empty($this->request->query['area_id']) ? $this->request->query['area_id'] : '' ) );

		$title = ( !empty($this->request->data['title']) ? $this->request->data['title']  : ( !empty($this->request->query['title']) ? $this->request->query['title'] : '' ) );
		$desc  = ( !empty($this->request->data['desc']) ? $this->request->data['desc']  : ( !empty($this->request->query['desc']) ? $this->request->query['desc'] : '' ) );

		if( empty($project_id))
		{
			$statusCode = 950;
			$message = 'Project Id is missing.';				
		}
		else if ( empty($workspace_id))
		{
			$statusCode = 950;
			$message = 'Project\'s workspace id is missing.';				
		}
		else if ( empty($area_id))
		{
			$statusCode = 950;
			$message = 'workspace\'s area_id is missing.';				
		}
		else if ( empty($title))
		{
			$statusCode = 950;
			$message = 'Title is missing.';				
		}
		else if ( empty($desc))
		{
			$statusCode = 950;
			$message = 'Description is missing.';				
		}
		else
		{
			
			$this->loadModel('Project');
			
			$this->loadModel('User');
			$this->loadModel('Workspace');
			$this->loadModel('Element');
			$this->loadModel('Area');
			$this->loadModel('UserProject');
			$this->loadModel('ElementPermission');
			$this->loadModel('ProjectWorkspace');
			$this->loadModel('ProjectPermission');
			//$this->Project->bindModel(array('belongsTo'=>array('Currency'=>array('className' => 'Currency'),'Category'=>array('className' => 'Category'),'Aligned'=>array('className' => 'Aligned'))));			
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
				$project['Workspace'] = array();
				
				
				unset($project['ProjectSkill']);
				$keysArray = array(
						'Project' => array('id','title','objective','description','rag_current_status','created','modified','start_date','end_date','current_status','color_code','status'),
						'Category' => array('id','title'),
						'Currency' => array('id','name'),
						'Aligned'  => array('id','title')
				);

				$project = $this->filterFields($keysArray,$project);
				
				
				if( !empty($project) )
				{
					$statusCode = 200;
					
					$this->Workspace->unbindModel(array('hasMany'=>array('ProjectWorkspace','Area','ElementPermission')));
					$workspace = $this->Workspace->findById($workspace_id,'id,title,description');

					if( empty($workspace)) 
					{
						$statusCode = 950;
						$message = 'No project exists with that contain workspace id #'.$workspace_id;
					}
					else
					{					
							$project['Workspace'] = $workspace['Workspace'] ;
							$this->Area->unbindModel(array('hasMany'=>array('Element')));						
							$area = $this->Area->find('first',array('fields'=>array('Area.id','Area.title','Area.workspace_id'),'conditions'=>array('Area.id'=>$area_id,'Area.workspace_id'=>$workspace_id)));
							
							if( empty($area) )
							{
								$statusCode = 950;
								$message = 'No workspace exists with that contain area id #'.$area_id;
							}
							/* else if( empty($area['Elements']) )
							{
								$statusCode = 950;
								$message = 'No elements exists with that contain aread id #'.$area_id;
							} */
							else
							{
								/* $elements = $area['Elements']; */
								$count = $neighbors = $this->Element->find("count", [
									'conditions' => [
										'Element.area_id' => $area_id,
									],
								]);
								$dataEE = $this->Element->find('first', array('conditions' => array('Element.area_id' => $area_id), 'order' => array('Element.sort_order' => 'DESC'), 'fields' => 'Element.sort_order'));
								
								if (isset($dataEE)) 
								{
									if (isset($dataEE['Element']['sort_order']) && $dataEE['Element']['sort_order'] > 0) 
									{
										$this->request->data['Element']['sort_order'] = $dataEE['Element']['sort_order'] + 1;
									} 
									else 
									{
										$this->request->data['Element']['sort_order'] = 0;
									}

								} 
								else 
								{
									$this->request->data['Element']['sort_order'] = (!is_null($count) && $count > 0) ? ($count + 1) : 1;
								}
								
								$this->request->data['Element']['updated_user_id'] = $project['UserProject'][0]['user_id'];
								$this->request->data['Element']['area_id'] = $area_id;
								$this->request->data['Element']['title'] = $title;
								$this->request->data['Element']['description'] = $desc;
								$this->request->data['Element']['created_by'] = $project['UserProject'][0]['user_id'];
								
								$this->Element->unbindModel(array('hasOne' => ['ElementDecision']));
								if ($this->Element->save($this->request->data)) 
								{								

									$insert_id = $this->Element->getLastInsertId();

									$this->Element->updateAll(array("Element.create_activity" => 0), array("Element.id" => $insert_id));
									$userProjects = array('UserProject.project_id' => $project_id, 'UserProject.owner_user' => 1, 'UserProject.user_id' => $project['UserProject'][0]['user_id']);
		

									$this->UserProject->updateAll(array('UserProject.modified' => "'" . date('Y-m-d H:i:s') . "'"), $userProjects);	


									//$this->Common->projectModified($project_id, $user_id);
								
									
									$arr['ElementPermission']['user_id'] = $project['UserProject'][0]['user_id'];
									$arr['ElementPermission']['element_id'] = $insert_id;
									$arr['ElementPermission']['project_id'] = $project_id;
									$arr['ElementPermission']['workspace_id'] = $workspace_id;
									$arr['ElementPermission']['permit_read'] = 1;
									$arr['ElementPermission']['permit_add'] = 1;
									$arr['ElementPermission']['permit_edit'] = 1;
									$arr['ElementPermission']['permit_delete'] = 1;
									$arr['ElementPermission']['permit_copy'] = 1;
									$arr['ElementPermission']['permit_move'] = 1;
									$arr['ElementPermission']['is_editable'] = 1;

								$this->ElementPermission->save($arr);
								
								$this->update_project_modify($insert_id,$project['UserProject'][0]['user_id']);
								$this->update_task_up_activity($insert_id,$project['UserProject'][0]['user_id']);
								$response['success'] = true;
								$response['msg'] = "Success";

								$element = $this->Element->find('first', ['conditions' => ['Element.id' => $insert_id,],'recursive' => -1,]);						
								$post = $this->request->data['Element'];
								$newData = $this->Element->find('list', ['conditions' => ['Element.area_id' => $area_id,],'fields' => ['id','sort_order',],'order' => ['sort_order ASC',],]);
								$elements_details = $edata = null;							
								$elements_data = $this->Area->find('first', ['conditions' => ['Area.id' => $area_id,],'recursive' => 1,]);
								// // pr($elements_data, 1);

								if (isset($elements_data) && !empty($elements_data)) 
								{
									foreach ($elements_data['Elements'] as $key => $val) 
									{
										$edata[] = $val;
									}
									$elements_details = $edata;
								}

								
								$area_elements = $this->update_js_config($project_id, $workspace_id, $project['UserProject'][0]['user_id']);							
								$response['content']['area_elements'] = $this->update_js_config($project_id, $workspace_id, $project['UserProject'][0]['user_id']);
								
								$message = 'elements exists with that contain area id #'.$area_id;
								$response['content']['insert_element'] = $element;
								$data = $response ;				
							} 
							else 
							{
								$statusCode = 950;	
								$response['msg'] = "Error!!!";
								$data = $response ;	
							}							
						}					
					}
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
	public function get_elements_details() 
	{ 
		$response = array();
		$data = array();
		$elements = array();
		$element_id 			=   ( !empty($this->request->data['element_id']) ? $this->request->data['element_id']  : ( !empty($this->request->query['element_id']) ? $this->request->query['element_id'] : '' ) );
		
		if( empty($element_id))
		{
			$statusCode = 950;
			$message = 'Element Id is missing.';				
		}
		else
		{
			$statusCode = 200 ;
			$message = 'Element Found.';
			$this->loadModel('Element');
			$this->loadModel('ProjectWorkspace');
			$this->loadModel('Project');
			
			$elements = $this->Element->findById($element_id);
			
			

			$elements['Element']['current_status'] = ( $elements['Element']['sign_off'] == 1 ? "Sign Off" : ( $elements['Element']['sign_off'] !== 1 ? get_element_activities($element_id) : "Open"));
			$elements['Element']['status'] = elementStatus($elements['Element']['start_date'], $elements['Element']['end_date'] , $element_id,$elements['Element']['sign_off']);
			
			if( !empty($elements))
			{
				
				$elements['Element']['created'] = date('Y-m-d',strtotime($elements['Element']['created']));
				$elements['Element']['modified'] = date('Y-m-d',strtotime($elements['Element']['modified']));

				$elements['Element']['start_date'] = date('Y-m-d',strtotime($elements['Element']['start_date']));
				$elements['Element']['end_date'] = date('Y-m-d',strtotime($elements['Element']['end_date']));
				$elements['Element']['date_constraints'] = ($elements['Element']['date_constraints']==1?'true':'false') ;
				$workspace_id = $elements['Area']['workspace_id'] ; 
				$workspaces = $this->ProjectWorkspace->findByWorkspaceId($workspace_id);
				
				if( !empty($workspaces['Workspace']))
				{
					$elements['Workspace'] = $workspaces['Workspace'];
					$project_id = $workspaces['Project']['id'];
				
					$this->Project->bindModel(array('belongsTo'=>array('Currency'=>array('className' => 'Currency'),'Category'=>array('className' => 'Category'),'Aligned'=>array('className' => 'Aligned'))));			
					$project = $this->Project->findById($project_id);
					$elements['Project'] = $project['Project'] ;

					$elements['Project']['rag_current_status'] = ( $elements['Project']['rag_current_status'] == 1 ? "red" : ($elements['Project']['rag_current_status']== 2 ? "Amber" : "Green"));
					$elements['Project']['current_status'] = ( $elements['Project']['sign_off'] == 1 ? "Sign Off" : ( $elements['Project']['sign_off'] !== 1 ? get_project_activities($project_id) : "Open"));
					$elements['Project']['created'] = date('Y-m-d',$elements['Project']['created']);
					$elements['Project']['modified'] = date('Y-m-d',$elements['Project']['modified']);

					$elements['Project']['start_date'] = date('Y-m-d',strtotime($elements['Project']['start_date']));
					$elements['Project']['end_date'] = date('Y-m-d',strtotime($elements['Project']['end_date']));

					$elements['Workspace']['start_date'] = date('Y-m-d',strtotime($workspaces['Workspace']['start_date']));
					$elements['Workspace']['end_date'] = date('Y-m-d',strtotime($workspaces['Workspace']['end_date']));
					$elements['Workspace']['status'] = workspace_status($workspaces['Workspace']['id']);

					


					$workspace_created = date('Y-m-d', strtotime($elements['Workspace']['created']));
					$workspace_modified = date('Y-m-d', strtotime($elements['Workspace']['modified']));
					$elements['Workspace']['created'] = $workspace_created ;
					$elements['Workspace']['modified'] = $workspace_modified ;	
				}


				
				
				
		
				

				

				$element_assigned = element_assigned($element_id);
				$assign_class = '';
            
				if($element_assigned['ElementAssignment']['reaction'] == 1) 
				{
              		$assign_class = '(Task Leader : Accepted)';
             	}
				else if($element_assigned['ElementAssignment']['reaction'] == 2) 
				{
					$assign_class = '(Task Leader : Schedule Not Accepted)';
				}
				else if($element_assigned['ElementAssignment']['reaction'] == 3)
				{
				$assign_class = '(Task Leader : Disengaged)';
				
				}  
				else 
				{
					  if(!empty($element_assigned['ElementAssignment']['assigned_to']))
					  {
               			$assign_class = '(Task Leader : Assigned)';
              		  }
				 } 
				 if( !empty($element_assigned['ElementAssignment'])) 
				 {
					$element_assigned['ElementAssignment']['assign_class'] = $assign_class;
				 }
				 $elements['ElementAssignment'] = $element_assigned['ElementAssignment']; 

				 if( !empty($elements['ElementAssignment']['assigned_to']) && !empty($elements['ElementAssignment']['created_by']))
				 {
					$userIds  = array($elements['ElementAssignment']['assigned_to'],$elements['ElementAssignment']['created_by']);
					
					$user = $this->User->find('all',array('fields'=>array('User.email','UserDetail.first_name','UserDetail.last_name','UserDetail.contact','UserDetail.profile_pic'),'conditions'=>array('User.id'=>$userIds)));
					
					if( !empty($user))
					{
						if(count($user)==1)
						{
							$elements['AssignedTo'] = array('first_name'=>$user[0]['UserDetail']['first_name'],'last_name'=>$user[0]['UserDetail']['last_name'],'email'=>$user[0]['User']['email'],'profile_pic'=>$user[0]['UserDetail']['profile_pic']);
							$elements['AssignedBy'] = array('first_name'=>$user[0]['UserDetail']['first_name'],'last_name'=>$user[0]['UserDetail']['last_name'],'email'=>$user[0]['User']['email'],'profile_pic'=>$user[0]['UserDetail']['profile_pic']);
						}
						else
						{
							$elements['AssignedTo'] = array('first_name'=>$user[0]['UserDetail']['first_name'],'last_name'=>$user[0]['UserDetail']['last_name'],'email'=>$user[0]['User']['email'],'profile_pic'=>$user[0]['UserDetail']['profile_pic']);
							$elements['AssignedBy'] = array('first_name'=>$user[1]['UserDetail']['first_name'],'last_name'=>$user[1]['UserDetail']['last_name'],'email'=>$user[1]['User']['email'],'profile_pic'=>$user[1]['UserDetail']['profile_pic']);
						}
					}
					$elements['ElementAssignment']['created'] = date('Y-m-d',strtotime($elements['ElementAssignment']['created']));
				 	$elements['ElementAssignment']['modified'] = date('Y-m-d',strtotime($elements['ElementAssignment']['modified']));
				}
				 //$elements['ElementAssignedTo'] = array('first_name'=>$user[0]['UserDetail']['first_name'],'last_name'=>$user[0]['UserDetail']['last_name'],'email'=>$user[0]['User']['email'],'profile_pic'=>$user[0]['UserDetail']['profile_pic']);

				 




				$keysArray = array(
					'Element' => array('id','title','description','comments','created','modified','start_date','end_date','current_status','color_code','status','date_constraints'),
					'Area'  => array('id','title','description','tooltip_text'),
					'Project' => array('id','title','objective','description'),
					'Workspace' => array('id','title','description','created','modified','status','current_status','color_code','start_date','end_date'),
					'Project' =>array('id','title','objective','description','rag_current_status','created','modified','start_date','end_date','color_code'),
					
			);	
			
				$elements = $this->filterFields($keysArray,$elements);
				unset($elements['Permissions']);
				unset($elements['Mindmaps']);
				unset($elements['ElementDecision']);
				/*unset($elements['Documents']);
				unset($elements['Notes']);
				unset($elements['Mindmaps']);
				unset($elements['Permissions']);
				unset($elements['Areas']); */
				$array_keys = array_keys($elements);
				foreach($array_keys as $val)
				{
					if(empty($elements[$val]))
					{
						unset($elements[$val]);
					}
					else
					{
						$valKeys = array_keys($elements[$val]);
						$unsetCounter = 0 ;
						$countKeys = ( isset($valKeys) && !empty($valKeys) ) ? count($valKeys) : 0;
						foreach($valKeys as $valKey )
						{
							if( empty($elements[$val][$valKey]))
							{
								$unsetCounter += 1 ;
							}	
						}
						if( $unsetCounter==$countKeys) {
							unset($elements[$val]);
						}

					}
				}
						
				
			}
			else
			{
				$statusCode = 950;
				$message = 'No Element exists with this  #'.$element_id.' id .';
			}			
		}
		$this->set([
	            'message' => $message,
				'statusCode' => $statusCode,
				'data' => $elements,
	            '_serialize' => ['statusCode', 'message', 'data']
	        ]);
	}



public function update_element_assest() 
	{
		
		$errors 				= array();
		$data					= array(); 
		$element_id 			=   ( !empty($this->request->data['element_id']) ? $this->request->data['element_id']  : ( !empty($this->request->query['element_id']) ? $this->request->query['element_id'] : '' ) );
		
		$type					=   ( !empty($this->request->data['type']) ? $this->request->data['type']  : ( !empty($this->request->query['type']) ? $this->request->query['type'] : '' ) );
		$type_id					=   ( !empty($this->request->data['type_id']) ? $this->request->data['type_id']  : ( !empty($this->request->query['type_id']) ? $this->request->query['type_id'] : '' ) );
		$title					=   ( !empty($this->request->data['title']) ? $this->request->data['title']  : ( !empty($this->request->query['title']) ? $this->request->query['title'] : '' ) );
		$desc					=   ( !empty($this->request->data['desc']) ? $this->request->data['desc']  : ( !empty($this->request->query['desc']) ? $this->request->query['desc'] : '' ) );
		$file_url				=   ( !empty($this->request->data['file_url']) ? $this->request->data['file_url']  : ( !empty($this->request->query['file_url']) ? $this->request->query['file_url'] : '' ) );
		
		
		$link_type					=   ( !empty($this->request->data['link_type']) ? $this->request->data['link_type']  : ( !empty($this->request->query['link_type']) ? $this->request->query['link_type'] : '' ) );
		$link						=   ( !empty($this->request->data['link']) ? $this->request->data['link']  : ( !empty($this->request->query['link']) ? $this->request->query['link'] : '' ) );
		$message				=   '';
		$element				=   array();
		if( empty($element_id))
		{
			$statusCode = 950;
			array_push($errors,'Element Id is missing.');						
		}
		if( empty($type))
		{
			$statusCode = 950;
			array_push($errors,'Assest type is missing.');						
		}
		else if( !in_array($type,array('notes','doc','link')))
		{
			$statusCode = 950;
			array_push($errors,'Not a valid Assest type.');	
		}
		
		// if( ($type=='notes'|| $type=='doc') && $title=='')
		// {
		// 	$statusCode = 950;
		// 	array_push($errors,'Note title is missing.');	
		// }
		// if( ($type=='doc') && $title=='')
		// {
		// 	$statusCode = 950;
		// 	array_push($errors,'Document title is missing.');	
		// }
		// if( ($type=='doc') && $file_url=='')
		// {
		// 	$statusCode = 950;
		// 	array_push($errors,'File Url is missing.');	
		// }
		// if($type=='notes' && $desc =='')
		// {
		// 	$statusCode = 950;
		// 	array_push($errors,'Note description is missing.');	
		// }
		if($type=="link")
		{
			$statusCode = 950;
			if( empty($link_type))
			{
				array_push($errors,'Link Type missing.');				
			}
			if( empty($link))
			{
				array_push($errors,'Link is missing.');				
			}
		}
		if( empty($type_id))
		{
			$statusCode = 950;
			array_push($errors,ucfirst($type) . ' id is missing.');						
		}
		if(empty($errors))
		{
			$this->loadModel('Element');
			$elementExists = $this->Element->exists($element_id);
			if( !$elementExists )
			{
				$statusCode = 950;				
				array_push($errors,'Element id not found.');
			}
			else
			{
				$element = $this->Element->findById($element_id);
				
				if( !empty($element))
				{
					if($type=="notes")
					{
						if( $title=='' && $desc == '')
						{
							$statusCode = 950;
							array_push($errors,'You need provide atleast one field for update the note.');
						}
						else
						{
							$model = "ElementNote";
							$this->loadModel('ElementNote');
							$elementNoteExists = $this->ElementNote->exists($type_id);
							if($elementNoteExists)
							{
								$elementNoteData = $this->ElementNote->findByElementId($element_id);
								if(  $elementNoteData['ElementNote']['element_id'] == $element_id)
								{
									$data[$model] = array();
									$data[$model]['id']					= $type_id;
									if($title!='')
									{
										$data[$model]['title'] 				=  $title;
									}
									
									$data[$model]['element_id'] 		= $element_id ;	
									if($desc!='')
									{
										$data[$model]['description'] 				=  $desc;
									}						
									
								}
								else 
								{
									$statusCode = 950;				
									array_push($errors,'This note id (#type_id) not associated with provided Element id.');
								}
							}
							else
							{
								$statusCode = 950;				
								array_push($errors,'Not a valid element note id.');
							}	
						}
						
					}
					else if( $type=='doc') 
					{


						if( $title=='' && $file_url == '')
						{
							$statusCode = 950;
							array_push($errors,'You need provide atleast one field for update the document result.');
						}	
						else
						{
							if(empty($errors))
							{
								$model = 'ElementDocument';
								$this->loadModel($model);
								$elementDocExists = $this->ElementDocument->exists($type_id);
								if($elementDocExists)
								{
									$elementDocData = $this->ElementDocument->findByElementId($element_id);
									if(  $elementDocData['ElementDocument']['element_id'] == $element_id)
									{
										if(!empty($file_url))
										{
											$headers = get_headers($file_url);
											$mime_type = '';
											foreach($headers as $header) 
											{
												if( strpos($header,"Content-Type:")!==false) 
												{
													$mime_type = str_replace(array('Content-Type:',' '),array('',''),$header);
												}
											}
											if($mime_type=='') 
											{
												array_push($errors,'File Mime type is not found.');			
											} 
											if(empty($errors))
											{
												$data[$model] = array();
												$data[$model]['id']					= $type_id;
												if( $title != '')
												{
													$data[$model]['title'] 				= $title ;
												}
												
												$data[$model]['file_name'] 			= basename($file_url);
												$data[$model]['file_size'] 			= $this->getRemoteFilesize($file_url);
												if(!copy($file_url,WWW_ROOT."uploads/element_documents/".$element_id."/".basename($file_url)))
												{
													array_push($errors,'File not exits on provided file url');
												}
												else
												{						
													$data[$model]['file_type'] 			= $mime_type;
													$data[$model]['feedback_attachments_id'] = 0;						
												}
											}	
										}
										else
										{
											$data[$model] = array();
											$data[$model]['id']					= $type_id;
											if( $title != '')
											{
												$data[$model]['title'] 				= $title ;
											}
										}
										
									}
									else 
									{
										$statusCode = 950;				
										array_push($errors,'This document (#type_id) id not associated with provided Element id.');
									}
								}
								else
								{
									$statusCode = 950;				
									array_push($errors,'Not a valid document note id.');
								}
							}
						}
						
					}
					else if($type=='link' && empty($errors) ) 
					{
						$model = "ElementLink";
						$this->loadModel($model);
						$elementLinkExists = $this->ElementLink->exists($type_id);
						if($elementLinkExists)
						{
							$elementLinkData = $this->ElementLink->findByElementId($element_id);
							if(  $elementLinkData['ElementLink']['element_id'] == $element_id)
							{
								$data[$model] = array();					
								$data[$model]['id']					= $type_id;
								$data[$model]['element_id'] 		= $element_id ;
								if($title!='')
								{
									$data[$model]['title'] 				=  $title;
								}
								if($link_type==1)
								{
									$data[$model]['link_type'] 				=  1; //url address
									$data[$model]['references'] 			= $link ;
									$data[$model]['embed_code'] 			= '' ;
								}
								else if($link_type==2)
								{
									$data[$model]['link_type'] 				=  2; //media file link
									$data[$model]['references'] 			= '' ;
									$data[$model]['embed_code'] 			= "<iframe width='560' height='315' src=".$link."></iframe>" ;
								} 
							}
							else 
							{
								$statusCode = 950;				
								array_push($errors,'This link (#type_id) id not associated with provided Element id.');
							}
						}
						else
						{
							$statusCode = 950;				
							array_push($errors,'Not a valid link id.');
						}
						//pr($data);die;
						
					}
				}	
			}
		}
		
		if( empty($errors) && !empty($data) )
		{
			if ($this->$model->save($data))
			{
				$result = array();
				$statusCode = 200;				
				$message = "Element ".ucfirst($model)." Updated Successfully";
				$elementData = $this->Element->findById($element_id);
				

				if($type=='link')
				{

					$result['Element'] = $elementData['Element'];
					foreach($elementData['Links'] as $link)
					{
						if($link['id']== $type_id)
						{
							$result['UpdatedLink'] =  $link;
						}
					}
				}
				else if($type=='notes')
				{

					$result['Element'] = $elementData['Element'];
				
					foreach($elementData['Notes'] as $eNote)
					{
						if($eNote['id']== $type_id)
						{
							$result['UpdatedNote'] =  $eNote;
						}
					}
				}
				else if($type=='doc')
				{

					$result['Element'] = $elementData['Element'];
				
					foreach($elementData['Documents'] as $eDoc)
					{
						if($eDoc['id']== $type_id)
						{
							$result['UpdatedDoc'] =  $eDoc;
						}
					}
				}
				
			}
		}
		$this->set([
				'message' => !empty($errors)?implode(',',$errors): 'Success',
				'statusCode' => 950,
				'data' => !empty($result)?$result:array(),
				'_serialize' => ['statusCode', 'message', 'data']
				]);
	
	}

}