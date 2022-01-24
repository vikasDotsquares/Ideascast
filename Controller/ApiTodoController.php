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
class ApiTodoController extends AppController {

	var $name = 'ApiTodo';
	
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
		$this->Auth->allow('get_todo_details','create','update');
		$view = new View();
		$this->objView = $view;		
		$this->live_setting = LIVE_SETTING;		
		$response = $this->api_check_user();
		$this->renderApiResponse($response);		
	}
	public function get_todo_details() 
	{ 
		Configure::write('debug',2);
		$response = array();
		$data = array();
		
		$project_id 			=   ( !empty($this->request->data['project_id']) ? $this->request->data['project_id']  : ( !empty($this->request->query['project_id']) ? $this->request->query['project_id'] : '' ) );
		$user_id 			=   ( !empty($this->request->data['user_id']) ? $this->request->data['user_id']  : ( !empty($this->request->query['user_id']) ? $this->request->query['user_id'] : '' ) );
		$todos 			=   ( !empty($this->request->data['todos']) ? explode(",",$this->request->data['todos'])  : ( !empty($this->request->query['todos']) ? explode(",",$this->request->query['todos']) : '' ) );
		
		if( empty($project_id) && empty($user_id) )
		{
			$statusCode = 950;
			$message = 'Either send project id or user id. Currently both are missing';				
		}
		else
		{
			$this->loadModel('Project');
			$this->loadModel('DoList');
			$this->loadModel('UserDetail');
			$this->loadModel('User');
			
			if( $project_id != '' && $user_id != '')
			{
				$conditions = array(
										'DoList.user_id'=>$user_id,
										'DoList.project_id'=>$project_id,
										'DoList.start_date != '=> '',
										'DoList.end_date != '=> '',
										'DoList.sign_off' => 0,
										'DoList.parent_id' => 0
									);
			}
			else if( $project_id != '' && $user_id == '')			
			{
				$conditions = array(
										'DoList.project_id'=>$project_id,										
										'DoList.start_date != '=> '',
										'DoList.end_date != '=> '',
										'DoList.sign_off' => 0,
										'DoList.parent_id' => 0
									);
								
			}
			else if( $project_id == '' && $user_id != '')			
			{
				$conditions = array(
										'DoList.user_id'=>$user_id,
										
										'DoList.start_date != '=> '',
										'DoList.end_date != '=> '',
										'DoList.sign_off' => 0,
										'DoList.parent_id' => 0
									);
				
			}
			
			if( !empty($todos)) 
			{
				if(count($todos)==1)
				{
					$conditions["DoList.id "] = $todos[0] ;	
				}
				else
				{
					$conditions["DoList.id IN"] = $todos ;
				}
				
			}
			
			$dolists = $this->DoList->find('all',array('conditions'=>$conditions));
			
			if( empty($dolists) )
			{
				$statusCode = 950;
				$message = 'No todo list find that match your criteria.';	
			}
			else
			{	
					$data = array();
					$message = 'Record Found.';
					$statusCode = 200;
					$i = 0 ;

					foreach($dolists as $key => $dolist)
					{
						$data[$i] = array();
						if( !empty($dolist['DoList']) )
						{
							$creater_id = $dolist['DoList']['user_id'];
							$creator = $this->User->find('all', array('conditions' => array('User.id' => $creater_id),  'fields' => array('User.id,User.email,UserDetail.first_name,UserDetail.last_name')));
							$data[$i]['ListDetails'] = array(
																'id'=>$dolist['DoList']['id'],
																'title'=>$dolist['DoList']['title'],
																'project_id'=>$dolist['DoList']['project_id'],
																'creator' => array(
																	'id'=>$creator[0]['User']['id'],
																	'email'=>$creator[0]['User']['email'],
																	'first_name'=>$creator[0]['UserDetail']['first_name'],
																	'last_name'=>$creator[0]['UserDetail']['last_name']
																),
																'status'=> $this->Common->get_api_todo_status($dolist['DoList']['id'],$creator[0]['User']['id']),
																'start_date'=>date('Y-m-d',strtotime($dolist['DoList']['start_date'])),
																'end_date'=>date('Y-m-d',strtotime($dolist['DoList']['end_date'])),
																'created'=>date('Y-m-d',strtotime($dolist['DoList']['created'])),
																'modified'=>date('Y-m-d',strtotime($dolist['DoList']['modified']))
															) ;
							if( !empty($dolist['Parent']) && !empty($dolist['Parent']['id']))
							{
								$data[$i]['ListDetails']['parentDetails'] = array(
																					'id'=>$dolist['Parent']['id'],
																					'title'=>$dolist['Parent']['title'],
																					'start_date'=>date('Y-m-d',strtotime($dolist['Parent']['start_date'])),
																					'end_date'=>date('Y-m-d',strtotime($dolist['Parent']['end_date'])),
																					'created'=>date('Y-m-d',strtotime($dolist['Parent']['created'])),
																					'modified'=>date('Y-m-d',strtotime($dolist['Parent']['modified']))
																				);
							}
							if( !empty($dolist['Children']) )
							{

								foreach( $dolist['Children'] as $children)
								{
									$child_creator_user_id = $children['user_id'] ;
									$child_creator = $this->User->find('all', array('conditions' => array('User.id' => $child_creator_user_id),  'fields' => array('User.id,User.email,UserDetail.first_name,UserDetail.last_name')));


									$this->loadModel('DoListUser');	
									$do_list_id = $children['id'];
									$child_assigned_user_ids = $this->DoListUser->find('list',array('conditions'=>array('DoListUser.do_list_id'=>$do_list_id,'DoListUser.approved'=>1),'fields'=>array('DoListUser.id','DoListUser.user_id')));
									if(!empty($child_assigned_user_ids))
									{
										$childAssignedUsersData = $this->User->find('all', array('conditions' => array('User.id IN' => $child_assigned_user_ids),  'fields' => array('User.id,User.email,UserDetail.first_name,UserDetail.last_name')));
										foreach($childAssignedUsersData as $childAssignedUserData)
										{
											$childAssignedUsers[] = array(
																													'id'=>$childAssignedUserData['User']['id'],
																													'email'=>$childAssignedUserData['User']['email'],
																													'first_name'=>$childAssignedUserData['UserDetail']['first_name'],
																													'last_name'=>$childAssignedUserData['UserDetail']['last_name']
																												);
										}
									}
									else
									{
										$childAssignedUsers = array();
									}
									
									$data[$i]['ListDetails']['ChildrenDetails'][] = !empty($childAssignedUsers) ? array(
																							'id'=>$children['id'],
																							'title'=>$children['title'],'parent_id'=>$children['parent_id'],
																							'creator' => array(
																								'id'=>$child_creator[0]['User']['id'],
																								'email'=>$child_creator[0]['User']['email'],
																								'first_name'=>$child_creator[0]['UserDetail']['first_name'],
																								'last_name'=>$child_creator[0]['UserDetail']['last_name']
																							),
																							'assignUser' => $childAssignedUsers,
																							'start_date'=>date('Y-m-d',strtotime($children['start_date'])),
																							'end_date'=>date('Y-m-d',strtotime($children['end_date'])),
																							'created'=>date('Y-m-d',strtotime($children['created'])),
																							'modified'=>date('Y-m-d',strtotime($children['modified']))
																						): array(
																							'id'=>$children['id'],
																							'title'=>$children['title'],'parent_id'=>$children['parent_id'],
																							'creator' => array(
																								'id'=>$child_creator[0]['User']['id'],
																								'email'=>$child_creator[0]['User']['email'],
																								'first_name'=>$child_creator[0]['UserDetail']['first_name'],
																								'last_name'=>$child_creator[0]['UserDetail']['last_name']
																							),
																							
																							'start_date'=>date('Y-m-d',strtotime($children['start_date'])),
																							'end_date'=>date('Y-m-d',strtotime($children['end_date'])),
																							'created'=>date('Y-m-d',strtotime($children['created'])),
																							'modified'=>date('Y-m-d',strtotime($children['modified']))
																						);
									if( !empty($childAssignedUsers))
									{
										
									}


								}
							}
							if( !empty($dolist['DoListUser']) )
							{

								foreach( $dolist['DoListUser'] as $user)
								{
									$assignedUserId = $user['user_id'];
									$assignedOnwerId = $user['owner_id'];
									$assignUser = $this->User->find('all', array('conditions' => array('User.id' => $assignedUserId),  'fields' => array('User.id,User.email,UserDetail.first_name,UserDetail.last_name')));
									
									$data[$i]['ListDetails']['AssignedUsers'][] = array(
																							'id'=>$user['user_id'],
																							'assignUser' => array(
																													'id'=>$assignUser[0]['User']['id'],
																													'email'=>$assignUser[0]['User']['email'],
																													'first_name'=>$assignUser[0]['UserDetail']['first_name'],
																													'last_name'=>$assignUser[0]['UserDetail']['last_name']
																												),
																							
																							
																							'created'=>date('Y-m-d',strtotime($user['created'])),
																							'modified'=>date('Y-m-d',strtotime($user['modified']))
																						);
								}
							}
							if( !empty($dolist['DoListUpload']))
							{
								foreach( $dolist['DoListUpload'] as $upload)
								{
									$data[$i]['ListDetails']['Uploads'][] = array(
																				'id' => $upload['id'] ,
																				'file_name' => $upload['file_name'] ,
																				'path' => Router::url("/",true).'uploads/dolist_uploads/'.$upload['file_name'],
																				'created'=>date('Y-m-d',strtotime($upload['created'])),
																				'modified'=>date('Y-m-d',strtotime($upload['modified']))
									);
								}
							}
							$i ++;
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


	public function create()
	{
		$response = array();
		$data = array();
		$project_id 			=   ( !empty($this->request->data['project_id']) ? $this->request->data['project_id']  : ( !empty($this->request->query['project_id']) ? $this->request->query['project_id'] : '' ) );
		$email 					=   ( !empty($this->request->data['email']) ? $this->request->data['email']  : ( !empty($this->request->query['email']) ? $this->request->query['email'] : '' ) );
		$is_sub_todo 			=   ( !empty($this->request->data['is_sub_todo']) ? $this->request->data['is_sub_todo']  : ( !empty($this->request->query['is_sub_todo']) ? $this->request->query['is_sub_todo'] : 0 ) );

		$start_date 			=   ( !empty($this->request->data['start_date']) ? $this->request->data['start_date']  : ( !empty($this->request->query['start_date']) ? $this->request->query['start_date'] : '' ) );

		$end_date 			=   ( !empty($this->request->data['end_date']) ? $this->request->data['end_date']  : ( !empty($this->request->query['end_date']) ? $this->request->query['end_date'] : '' ) );

		$title 			=   ( !empty($this->request->data['title']) ? $this->request->data['title']  : ( !empty($this->request->query['title']) ? $this->request->query['title'] : '' ) );

		
		$parent_id				=   0 ;
		$errors					=   array();
		$is_search = 1;
		$this->loadModel('User');
		$this->loadModel('DoList');
		$this->loadModel('DoListUpload');
		if( $project_id==0)
		{
			array_push($errors,'Project id is required.');
		}
		else
		{
			if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$start_date)) 
			{
				array_push($errors,'Please enter valid start date Ex. (2012-09-12).');
			} 
			else if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$end_date)) 
			{
				array_push($errors,'Please enter valid end date Ex. (2012-09-12).');
			}
			else
			{
				if( $end_date >= $start_date )
				{
					if( $is_sub_todo > 0)
					{
						$parent_id = ( !empty($this->request->data['parent_id']) ? $this->request->data['parent_id']  : ( !empty($this->request->query['parent_id']) ? $this->request->query['parent_id'] : '' ) );
						if( empty($parent_id) )
						{
							array_push($errors,'Parent id is missing for this subtodo');	
						}
						else
						{
							$parentToDo = $this->DoList->findById($parent_id);
							if( $parentToDo['DoList']['sign_off']==1)
							{
								array_push($errors,'Parent todo has alredy been signed Off so you can not add subtodo.');	
							}
						}
						
					}
			
					if( empty($email) )
					{
						array_push($errors,'User email address is missing');			
					}
					else
					{
						$this->request->data['DoList'] = array();
						$this->request->data['DoList']['title'] = $title ;
						$this->request->data['DoList']['start_date'] = $start_date;
						$this->request->data['DoList']['end_date'] = $end_date;
						
						$user = $this->User->findByEmail($email);
						
						if( !empty($user))
						{
							$user_id = $user['User']['id'];
							$this->request->data['DoList']['user_id'] 	 = $user_id ;			
							$this->request->data['DoList']['created_by'] 	 = $user_id ;			
			
							if( $project_id != '')
							{
								$thisProjectUsers  = $this->objView->loadHelper('ViewModel')->projectusers($project_id);
								if(!in_array($user_id,$thisProjectUsers))
								{
									array_push($errors,'This project is not assigned to provided user id ');	
								}
								else
								{
									$this->request->data['DoList']['project_id'] 	 = $project_id ;
								}
							}
							$this->request->data['DoList']['is_search'] 	 = $is_search ;
							$this->request->data['DoList']['parent_id'] 	 = $parent_id ;
							$uploads				=   ( !empty($this->request->data['uploads']) ? explode(",",$this->request->data['uploads'])  : ( !empty($this->request->query['uploads']) ? explode(",",$this->request->query['uploads']) : '' ) );
							$files 					=   array();
							if( !empty($uploads))
							{
								
								if( is_array($uploads) && count($uploads) >0 )
								{
									$uniqueUploads = array();
									foreach($uploads as $key => $upload)
									{
										$uniqueUploads[$key]['org_file_name'] = $upload ;
										$uniqueUploads[$key]['cust_file_name'] = time().'-'.basename($upload) ;
									}
									foreach($uniqueUploads as $uploadCounter => $uploadFile)
									{
										$files[] = ['file_name' => $uploadFile['cust_file_name'], 'file_name_original' => $uploadFile['cust_file_name'] ];
									}
								}
							}
							if( empty($errors) )
							{
								
								$this->request->data['DoListUpload'] = $files ;
								if ($this->DoList->saveAll($this->request->data)) 
								{
									$todo_id = $this->DoList->getLastInsertID();
									if( !empty($uniqueUploads) )
									{
										foreach($uniqueUploads as $fkey => $file)
										{
											if( !copy($file['org_file_name'],WWW_ROOT.'uploads/dolist_uploads/'.$file['cust_file_name']) )
											{
												array_push($errors,'(File #'.$fkey.') '.$file['org_file_name'].' not uploaded');
											}
										}
									}
									if (isset($project_id) && !empty($project_id)) 
									{
										$this->Common->projectModified($project_id, $user_id);						
									}
									$data = $this->DoList->read(null, $todo_id);
								}
							}
						}
						else
						{
							array_push($errors,"This user doesn't exist.");
						}
						
					}
				}
				else
				{
					array_push($errors,'End Date greater than to Start Date.');
				}
			}
		}
		




		if( !empty( $data) ) 
		{
			$array_keys = array_keys($data);
			foreach($array_keys as $val)
			{
				if(empty($data[$val]))
				{
					unset($data[$val]);
				}
				else
				{
					$valKeys = array_keys($data[$val]);
					$unsetCounter = 0 ;
					$countKeys = ( isset($valKeys) && !empty($valKeys) ) ? count($valKeys) : 0;
					foreach($valKeys as $valKey )
					{
						if( empty($data[$val][$valKey]))
						{
							$unsetCounter += 1 ;
						}	
					}
					if( $unsetCounter==$countKeys) 
					{
						unset($data[$val]);
					}
				}
			}
		}
		$this->set([
			'message' => (!empty($errors) ? $errors : ($parent_id>0?"Sub To Do created successfully":"To Do created successfully") ),
			'statusCode' => (!empty($errors) ? 950 : 200 ),
			'data' => $data,
			'_serialize' => ['statusCode', 'message', 'data']
		]);
	}

	public function update()
	{
		$response 				= array();
		$data 					= array();
		$errors					=   array();
		
		$project_id 			=   ( !empty($this->request->data['project_id']) ? $this->request->data['project_id']  : ( !empty($this->request->query['project_id']) ? $this->request->query['project_id'] : '' ) );;
		$email 					=   ( !empty($this->request->data['email']) ? $this->request->data['email']  : ( !empty($this->request->query['email']) ? $this->request->query['email'] : '' ) );
		$todo_id 				=   ( !empty($this->request->data['id']) ? $this->request->data['id']  : ( !empty($this->request->query['id']) ? $this->request->query['id'] : '' ) );
		$is_sub_todo 			=   0;
		$start_date 			=   ( !empty($this->request->data['start_date']) ? $this->request->data['start_date']  : ( !empty($this->request->query['start_date']) ? $this->request->query['start_date'] : '' ) );
		$end_date 				=   ( !empty($this->request->data['end_date']) ? $this->request->data['end_date']  : ( !empty($this->request->query['end_date']) ? $this->request->query['end_date'] : '' ) );
		$title 					=   ( !empty($this->request->data['title']) ? $this->request->data['title']  : ( !empty($this->request->query['title']) ? $this->request->query['title'] : '' ) );
		$parent_id				=   0 ;
		

		$is_search = 1;



		if( empty($title) && empty($start_date)&& empty($end_date)  )
		{
			array_push($errors,'You need to provided atleast one field for update.');
		}
		else
		{
			if( empty($email) )
			{
				array_push($errors,'Please provide user email address.');
			}
			if( empty($project_id))
			{
				array_push($errors,'Please provide to do project id.');
			}
			if( empty($errors))
			{
				$this->loadModel('User');
				$this->loadModel('DoList');
				$this->loadModel('DoListUpload');
				if( $todo_id==0)
				{
					array_push($errors,'ToDo id is required.');
				}
				else
				{
					$todoExists = $this->DoList->exists($todo_id);				
					if($todoExists)
					{
						$doListData = $this->DoList->findById($todo_id);					
						$this->request->data['DoList'] = array();
						$this->request->data['DoList']['id'] = $todo_id ;
						if(!empty($title)!='')
						{
							$this->request->data['DoList']['title'] = $title ;
						}
						if(!empty($start_date)!='')
						{
							if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$start_date)) 
							{
								array_push($errors,'Please enter valid start date Ex. (2012-09-12).');
							} 
							else
							{						
								$this->request->data['DoList']['start_date'] = $start_date;
							}
						}
						if(!empty($end_date)!='')
						{
							if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$end_date)) 
							{
								array_push($errors,'Please enter valid end date Ex. (2012-09-12).');
							} 
							else
							{						
								$this->request->data['DoList']['end_date'] = $end_date;
							}
						}
						if(!empty($start_date) &&  !empty($end_date) && ($end_date < $start_date) )
						{
							array_push($errors,'End Date greater than to Start Date.');
						}
						
						if($doListData['DoList']['parent_id'] > 0)
						{
							$is_sub_todo = 1;
						}
						if(empty($errors)  )
						{
							if( $is_sub_todo > 0)
							{
								
								$parent_id = $doListData['DoList']['parent_id'];
								$parentToDo = $this->DoList->findById($parent_id);
								$this->request->data['DoList']['sign_off'] = $parentToDo['DoList']['sign_off'];
								if( $parentToDo['DoList']['sign_off']==1)
								{
									array_push($errors,'Parent todo has alredy been signed Off so you can not update subtodo.');	
								}
							}
						}
						if(empty($errors))
						{	
							if(!empty($email))
							{
								$user = $this->User->findByEmail($email);
								
								if( !empty($user))
								{
									$user_id = $user['User']['id'];
									$this->request->data['DoList']['user_id'] 	 = $user_id ;
								}
								else
								{
									array_push($errors,'No user has '.$email.' address.');
								}
							}
							
							if(!empty($project_id)&& !empty($user_id) && empty($errors) )
							{
								$thisProjectUsers  = $this->objView->loadHelper('ViewModel')->projectusers($project_id);
								if(!in_array($user_id,$thisProjectUsers))
								{
									array_push($errors,'This project is not assigned to provided user id ');	
								}
								else
								{
									$this->request->data['DoList']['project_id'] 	 = $project_id ;
								}
							}
							$this->request->data['DoList']['is_search'] 	 = 1 ;
							$this->request->data['DoList']['parent_id'] 	 = $parent_id ;
							if( empty($errors) )
							{
								
	
								if ($this->DoList->save($this->request->data['DoList'])) 
								{
									$uploads				=   ( !empty($this->request->data['uploads']) ? explode(",",$this->request->data['uploads'])  : ( !empty($this->request->query['uploads']) ? explode(",",$this->request->query['uploads']) : '' ) );
									$files 					=   array();
									if( !empty($uploads))
									{
										
										if( is_array($uploads) && count($uploads) >0 )
										{
											$uniqueUploads = array();
											foreach($uploads as $key => $upload)
											{
												$uniqueUploads[$key]['org_file_name'] = $upload ;
												$uniqueUploads[$key]['cust_file_name'] = time().'-'.basename($upload) ;
												
											}
											foreach($uniqueUploads as $uploadCounter => $uploadFile)
											{
												$files[] = ['do_list_id'=> $todo_id , 'file_name' => $uploadFile['cust_file_name'], 'file_name_original' => $uploadFile['cust_file_name'] ];
											}
										}
										$this->request->data['DoListUpload'] = $files ;
									}
									if(!empty($files))
									{
										if( !empty($uniqueUploads) )
										{
											foreach($uniqueUploads as $fkey => $file)
											{
												if( !copy($file['org_file_name'],WWW_ROOT.'uploads/dolist_uploads/'.$file['cust_file_name']))											
												{
													array_push($errors,'(File #'.$fkey.') '.$file['org_file_name'].' not uploaded');
													break;
												}
												else
												{
													continue ;
												}
												
											}										
										}
										if( empty($errors) && !empty($this->request->data['DoListUpload'])) 
										{
											$this->DoListUpload->saveAll($this->request->data['DoListUpload']) ;
										}
									}
									if (!empty($project_id) && !empty($user_id) ) 
									{
										$this->Common->projectModified($project_id, $user_id);						
									}
									$data = $this->DoList->read(null, $todo_id);	
								}
							}
						}
					}
					else
					{
						array_push($errors,'No ToDo exists with id '.$todo_id.' ');
					}
				}
			}
		}
		if( !empty($data) && !empty($errors)) 
		{
			$data = array();
		}
		if( !empty( $data) ) 
		{
			$array_keys = array_keys($data);
			foreach($array_keys as $val)
			{
				if(empty($data[$val]))
				{
					unset($data[$val]);
				}
				else
				{
					$valKeys = array_keys($data[$val]);
					$unsetCounter = 0 ;
					$countKeys = count($valKeys);
					foreach($valKeys as $valKey )
					{
						if( empty($data[$val][$valKey]))
						{
							$unsetCounter += 1 ;
						}	
					}
					if( $unsetCounter==$countKeys) 
					{
						unset($data[$val]);
					}
				}
			}
		}
		$this->set([
			'message' => (!empty($errors) ? $errors : ($parent_id>0?"Sub To Do updated successfully":"To Do updated successfully") ),
			'statusCode' => (!empty($errors) ? 950 : 200 ),
			'data' => $data,
			'_serialize' => ['statusCode', 'message', 'data']
		]);
	}
	
}
