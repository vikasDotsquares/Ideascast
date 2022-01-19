<?php

App::uses('Controller/Component', 'Auth', 'Session', 'RequestHandler');
App::uses('Sanitize', 'Utility');
App::uses('CakeEmail', 'Network/Email');

App::import('Lib', 'SharingTree');

class GroupsController extends AppController {

	public $name = 'Groups';
	public $uses = ['User', 'UserDetail', 'UserProject', 'Project', 'Workspace', 'Area', 'ProjectWorkspace', 'Element', 'ElementPermission', 'WorkspacePermission', 'ProjectPermission', 'ElementPropagate', 'WorkspacePropagate', 'ProjectPropagate', 'ProjectGroup', 'ProjectGroupUser', 'ProjectGroupHistory', 'Skill', 'EmailNotification','ShareElement'];
	public $user_id = null;
	public $pagination = null;
	public $components = array('Common', 'Users');
	public $objView = null;

	/**
	 * Helpers
	 *
	 * @var array
	 */
	public $helpers = array('Html', 'Form', 'Session', 'Text', 'ViewModel', 'Group');

	public function beforeFilter() {
		parent::beforeFilter();

		$this->user_id = $this->Auth->user('id');

		$view = new View();
		$this->objView = $view;


		if( $_SERVER['HTTP_HOST'] != LOCALIP )  {

			if( $this->Session->read('Auth.User.role_id') == 3 ){
				$this->redirect(['controller' => 'organisations','action' => 'dashboard']);
			}
			if( $this->Session->read('Auth.User.role_id') == 1 ){
				$this->redirect(['controller' => 'dashboard','action' => 'index']);
			}
		}



	}

	/*
		 * @name  	index
		 * @access	public
		 * @package  App/Controller/GroupsController
	*/

	public function index() {

		$this->layout = 'inner';

		$viewData = null;

		$viewData['title_for_layout'] = 'Create Group';
		$viewData['page_heading'] = 'Create Group';

		/* Select Projects */
		/* $projects = $this->Project->find('all', array(
			'joins' => array(
				array(
					'table' => 'user_projects',
					'alias' => 'UserProject',
					'type' => 'INNER',
					'conditions' => array(
						'UserProject.project_id = Project.id',
					),
				),
			),
			'conditions' => ['UserProject.owner_user' => 1, 'UserProject.user_id' => $this->user_id, 'Project.studio_status' => 0],
			'fields' => array('Project.id', 'Project.title'),
			'order' => 'UserProject.created ASC',
			'group' => ['UserProject.project_id'],

			'recursive' => -1,
		));

		$projects_list = (isset($projects) && !empty($projects)) ? Set::combine($projects, '{n}.Project.id', '{n}.Project.title') : null;
		$this->set('projects_list', $projects_list); */

		/* multiselect group */
		/* Select group */
		$groupList = $this->ProjectGroup->find('all', array(
			'conditions' => ['ProjectGroup.group_owner_id' => $this->user_id, 'ProjectGroup.is_deleted' => 0],
			'fields' => array('ProjectGroup.id', 'ProjectGroup.title'),
			'order' => 'ProjectGroup.created DESC',
			'recursive' => 1,
		));
		// pr($groupList, 1);
		$this->set('groupList', $groupList);
		/* multiselect group */

		$crumb = [
			'My Groups' => [
				'data' => [
					'url' => '/shares/my_groups',
					'class' => 'tipText',
					'title' => "My Groups",
					'data-original-title' => "My Groups",
				],
			],
			'last' => [
				'data' => [
					'title' => 'Create Group',
					'data-original-title' => 'Create Group',
				],
			],
		];

		$projects_list = [];
		$mprojects = get_my_projects($this->user_id);
		$rprojects = get_rec_projects($this->user_id);

		if (isset($mprojects) && !empty($mprojects)) {
			$projects_list = $projects_list + $mprojects;
		}
		if (isset($rprojects) && !empty($rprojects)) {
			$projects_list = $projects_list + $rprojects;
		}

		if (isset($projects_list) && !empty($projects_list)) {
			$projects_list = array_map("strip_tags", $projects_list);
			$projects_list = array_map("trim", $projects_list);
			$projects_list = array_map(function ($v) {
				return html_entity_decode($v, ENT_COMPAT, "UTF-8");
			}, $projects_list);
			natcasesort($projects_list);
		}
		$this->set('projects_list', $projects_list);

		$this->loadModel('Tag');
		$tags = $this->Tag->find('list', ['conditions' => ['user_id' => $this->user_id],'fields' => array('Tag.tag'), 'order' => ['tag ASC'], 'group' => array('tag')]);

		$tagResp = [];
		if (isset($tags) && !empty($tags)) {
			$k = 0;
			foreach ($tags as $key => $value) {
				$tagResp[$k]['label'] = $value;
				$tagResp[$k]['value'] = $value;
				$k++;
			}
		}
		$this->set('tags', $tagResp);


		if (isset($cat_crumb) && !empty($cat_crumb)) {
			$crumb = array_merge($cat_crumb, $crumb);
		}
		$viewData['crumb'] = (isset($crumb) && !empty($crumb)) ? $crumb : null;

		$this->set($viewData);

	}

	/*
		 * @name  		create_permissions
		 * @access		public
		 * @package  	App/Controller/GroupsController
	*/

	public function create_permissions($project_id = null, $group_id = null) {

		$this->layout = 'inner';

		$viewData = null;

		if (empty($project_id) && empty($group_id)) {
			$viewData['title_for_layout'] = 'Group Permissions';
			$viewData['page_heading'] = 'Group Permissions';
		} else {
			$viewData['title_for_layout'] = 'Group Sharing - Step 2: Permissions Selection';
			$viewData['page_heading'] = 'Group Sharing: Permissions Selection';
			$viewData['project_id'] = (isset($project_id) && !empty($project_id)) ? $project_id : null;
			$viewData['group_id'] = (isset($group_id) && !empty($group_id)) ? $group_id : null;
		}

		if ($this->request->is('post') || $this->request->is('put')) {

			$post = $this->request->data;

			$response = true;

			if ((isset($post['Share']['group_id']) && !empty($post['Share']['group_id'])) && (isset($post['Share']['project_id']) && !empty($post['Share']['project_id']))) {

				$shareGroup = $post['Share']['group_id'];

				$projectId = $post['Share']['project_id'];

				$sharingLevel = (isset($post['Share']['project_level'])) ? $post['Share']['project_level'] : 0;

				$propogationEnable = (isset($post['Share']['share_permission']) && $sharingLevel == 0) ? $post['Share']['share_permission'] : 0;

				# GET USER PROJECT ID OF SUPPLIED PROJECT
				$userProjectId = project_upid($projectId);

				$userOwnerId = project_owner($projectId);

				// e('propogationEnable = '.$propogationEnable.', shareGroup = '.$shareGroup.', sharingLevel = '.$sharingLevel.', projectId = '.$projectId);

				// GET ALL DIFFERENT TYPE OF PERMISSIONS DATA FROM POST
				$prjPermit = (isset($post['ProjectPermission'])) ? $post['ProjectPermission'][$projectId] : null;

				### if owner permission given
				if ($sharingLevel > 0) {

					$owner_level_permit = [
						'project_group_id' => $shareGroup,
						'share_by_id' => $this->user_id,
						'owner_id' => $userOwnerId,
						'user_project_id' => $userProjectId,
						'share_permission' => 1,
						'project_level' => 1,
						'parent_id' => 0,
						'permit_read' => 1,
						'permit_edit' => 1,
						'permit_delete' => 1,
					];

					if ($this->ProjectPermission->save($owner_level_permit)) {
						$allInsertedId['project'] = $this->ProjectPermission->getLastInsertId();
					} else {
						$response = false;
					}

				} else {

					$wsPermit = (isset($post['WorkspacePermission'])) ? $post['WorkspacePermission'] : null;

					$elPermit = (isset($post['ElementPermission'])) ? $post['ElementPermission'] : null;

					$allInsertedId = ['project' => null, 'workspace' => null, 'element' => null];

					### Add Project Permissions
					if (!empty($prjPermit)) {

						$project_permissions = [
							'project_group_id' => $shareGroup,
							'share_by_id' => $this->user_id,
							'owner_id' => $userOwnerId,
							'user_project_id' => $userProjectId,
							'parent_id' => 0,
							'share_permission' => $propogationEnable,
							'project_level' => 0,
						];

						foreach ($prjPermit as $pkey => $val) {
							$project_permissions[$pkey] = (!empty($val) && $val > 0) ? 1 : 0;
						}

						if (!empty($project_permissions)) {

							$this->ProjectPermission->id = (isset($projectPermissionId) && !empty($projectPermissionId)) ? $projectPermissionId : null;

							if ($this->ProjectPermission->save($project_permissions)) {
								$allInsertedId['project'] = $this->ProjectPermission->getLastInsertId();
							} else {
								$response = false;
							}

						}
					}

					### Add Workspace Permissions
					if (!empty($wsPermit)) {

						$workspace_permissions = null;

						foreach ($wsPermit as $wsid => $val) {

							$wsPermitions = null;

							$wsPermitions = [
								'project_group_id' => $shareGroup,
								'user_project_id' => $userProjectId,
							];

							# GET project_workspace_id FROM project_id and workspace_id VALUE
							$project_workspace_id = workspace_pwid($projectId, $wsid);

							if (isset($project_workspace_id) && !empty($project_workspace_id)) {

								$wsPermitions['project_workspace_id'] = $project_workspace_id;

								if (!empty($val) && is_array($val)) {
									# CREATE MULTI-ARRAY TO INSERT MULTIPLE DATA AT ONCE
									foreach ($val as $wkey => $wval) {
										$wsPermitions[$wkey] = (!empty($wval) && $wval > 0) ? 1 : 0;
									}
								}

								$workspace_permissions[]['WorkspacePermission'] = $wsPermitions;
							}
						}

						if (!empty($workspace_permissions)) {
							if ($this->WorkspacePermission->saveAll($workspace_permissions)) {
								$allInsertedId['workspace'] = $this->WorkspacePermission->inserted_ids;
							} else {
								$response = false;
							}
						}
					}

					### Add Element Permissions
					if (!empty($elPermit)) {

						$element_permissions = null;
						// INSERT NEXT ROW ONLY IF THE PARENT IS CREATED SUCCESSFULLY

						foreach ($elPermit as $elid => $val) {

							$elPermitions = null;

							$elPermitions = [
								'project_group_id' => $shareGroup,
								'project_id' => $project_id,
							];

							if (!empty($val) && is_array($val)) {

								foreach ($val as $ekey => $eval) {
									# GET project_id and workspace_id VALUES WITH EXTRACTING VALUE OF EACH
									$elDetail = explode('_', $eval);

									if (!empty($elDetail) && is_array($elDetail)) {

										list($wsID, $aID, $eID) = $elDetail;

										if (!empty($wsID) && !empty($aID) && !empty($eID)) {
											// ADD READ PERMISSION OF THIS ELEMENT
											$ws_project_id = workspace_pid($wsID);
											$ws_read['user_project_id'] = project_upid($ws_project_id);
											$ws_read['project_workspace_id'] = workspace_pwid($ws_project_id, $wsID);
											$ws_read['project_group_id'] = $shareGroup;

											if (!$this->WorkspacePermission->hasAny($ws_read)) {
												$ws_read['permit_read'] = 1;
												if ($this->WorkspacePermission->saveAll($ws_read)) {
													$ws_insert_id = $this->WorkspacePermission->inserted_ids;
												}
											}
											$elPermitions['workspace_id'] = $wsID;
											$elPermitions['element_id'] = $eID;
											$elPermitions[$ekey] = (!empty($eval) && $eval > 0) ? 1 : 0;
										}
									}
								}
							}

							$element_permissions[]['ElementPermission'] = $elPermitions;

						}

						if (!empty($element_permissions)) {
							if ($this->ElementPermission->saveAll($element_permissions)) {
								$allInsertedId['element'] = $this->ElementPermission->inserted_ids;
							} else {
								$response = false;
							}
						}
					}
				}
			}

			if ($response) {
				$this->redirect(array('controller' => 'shares', 'action' => 'my_groups'));
			}

		}

		/* Select Projects */
		$projects = $this->Project->find('all', array(
			'joins' => array(
				array(
					'table' => 'user_projects',
					'alias' => 'UserProject',
					'type' => 'INNER',
					'conditions' => array(
						'UserProject.project_id = Project.id',
					),
				),
			),
			'conditions' => ['UserProject.owner_user' => 1, 'UserProject.user_id' => $this->user_id],
			'fields' => array('Project.id', 'Project.title'),
			'order' => 'UserProject.created ASC',
			'group' => ['UserProject.project_id'],

			'recursive' => -1,
		));

		$projects_list = (isset($projects) && !empty($projects)) ? Set::combine($projects, '{n}.Project.id', '{n}.Project.title') : null;
		$this->set('projects_list', $projects_list);

		$crumb = [
			'My Groups' => [
				'data' => [
					'url' => '/shares/my_groups',
					'class' => 'tipText',
					'title' => "My Groups",
					'data-original-title' => "My Groups",
				],
			],
			'last' => [
				'data' => [
					'title' => 'Group Permissions',
					'data-original-title' => 'Group Permissions',
				],
			],
		];
		if (isset($cat_crumb) && !empty($cat_crumb)) {
			$crumb = array_merge($cat_crumb, $crumb);
		}
		$viewData['crumb'] = (isset($crumb) && !empty($crumb)) ? $crumb : null;

		$this->set($viewData);

		### if permissions already exists, redirect to update permissions page
		if (isset($project_id) && !empty($project_id) && isset($group_id) && !empty($group_id)) {
			if (group_has_permissions($project_id, $group_id)) {
				$this->redirect(array('controller' => 'groups', 'action' => 'update_permissions', $project_id, $group_id));
			}
		}
	}

	/*
		 * @name  		update_permissions
		 * @access		public
		 * @package  	App/Controller/GroupsController
	*/

	public function update_permissions($project_id = null, $group_id = null, $projectPermissionId = null) {

		$this->layout = 'inner';

		$viewData = null;

		if (empty($project_id) && empty($group_id)) {
			$viewData['title_for_layout'] = 'Update Group Sharing';
			$viewData['page_heading'] = 'Update Group Sharing';
		} else {
			$viewData['title_for_layout'] = 'Update Group Sharing';
			$viewData['page_heading'] = 'Update Group Sharing';
			$viewData['project_id'] = (isset($project_id) && !empty($project_id)) ? $project_id : null;
			$viewData['group_id'] = (isset($group_id) && !empty($group_id)) ? $group_id : null;
		}

		if ($this->request->is('post') || $this->request->is('put')) {

			$post = $this->request->data;

			$response = true;

			if ((isset($post['Share']['group_id']) && !empty($post['Share']['group_id'])) && (isset($post['Share']['project_id']) && !empty($post['Share']['project_id']))) {

				$shareGroup = $post['Share']['group_id'];

				$projectId = $post['Share']['project_id'];

				$sharingLevel = (isset($post['Share']['project_level'])) ? $post['Share']['project_level'] : 0;

				$propogationEnable = (isset($post['Share']['share_permission']) && $sharingLevel == 0) ? $post['Share']['share_permission'] : 0;

				# GET USER PROJECT ID OF SUPPLIED PROJECT
				$userProjectId = project_upid($projectId);

				$userOwnerId = project_owner($projectId);

				// e('propogationEnable = '.$propogationEnable.', shareGroup = '.$shareGroup.', sharingLevel = '.$sharingLevel.', projectId = '.$projectId);

				$projectPermissionId = (isset($projectPermissionId) && !empty($projectPermissionId)) ? $projectPermissionId : null;

				// -------- Get workspace permissions of this user with selected project
				$pw_data = get_project_workspace($project_id, true);

				### Start Workspace Permission Delete
				$currentWPermit = null;
				if (isset($pw_data) && !empty($pw_data)) {
					$currentWPermit = $this->WorkspacePermission->find('all', [
						'conditions' => [
							'WorkspacePermission.user_project_id' => $userProjectId,
							'WorkspacePermission.project_group_id' => $post['Share']['group_id'],
							'WorkspacePermission.project_workspace_id' => array_values($pw_data),
						],
						'fields' => ['WorkspacePermission.id'],
						'recursive' => -1,
					]);
					// e($this->WorkspacePermission->_query());
					// EXTRACTING WORKSPACE PERMISSIONS TABLE IDs
					$currentWPermit = Set::extract($currentWPermit, '/WorkspacePermission/id');
				}
				$responses = null;
				if (isset($currentWPermit) && !empty($currentWPermit)) {

					if ($this->WorkspacePermission->deleteAll(['WorkspacePermission.id' => $currentWPermit])) {
						$responses['w'] = $currentWPermit;
					}
				}

				### End Workspace Permission Delete

				### Start Element Permission Delete
				// -------- Get element permissions of this user with selected project

				$currentEPermit = null;
				if (isset($pw_data) && !empty($pw_data)) {
					$currentEPermit = $this->ElementPermission->find('all', [
						'conditions' => [
							'ElementPermission.project_id' => $project_id,
							'ElementPermission.project_group_id' => $post['Share']['group_id'],
							'ElementPermission.workspace_id' => array_keys($pw_data),
						],
						'fields' => ['ElementPermission.id'],
						'recursive' => -1,
					]);
					// EXTRACTING ELEMENT PERMISSIONS TABLE IDs
					$currentEPermit = Set::extract($currentEPermit, '/ElementPermission/id');
				}




				if (isset($currentEPermit) && !empty($currentEPermit)) {

					/*  
					if ($this->ElementPermission->deleteAll(['ElementPermission.project_group_id' => $post['Share']['group_id']])) {
						$responses['e'] = $currentEPermit;
					} */

				}


				//Old Data will be deleted from ElementPermission and ShareElement table when shaing will be updated
				if( isset($post['Share']['group_id']) && !empty($post['Share']['group_id']) ){

					$elePermissionIds = $this->ElementPermission->find('list',['conditions'=>['ElementPermission.project_group_id'=>$post['Share']['group_id']],'fields' => ['id'] ] );

					if(isset($elePermissionIds) && !empty($elePermissionIds)){
						foreach($elePermissionIds as $pLists){
							$delCond = array('ShareElement.element_permission_id'=>$pLists);
							$this->ShareElement->deleteAll($delCond);
						}
					}
					$this->ElementPermission->deleteAll(['ElementPermission.project_group_id' => $post['Share']['group_id']]);
				}


				// pr($responses, 1);
				### End Element Permission Delete

				### if owner permission given
				if ($sharingLevel > 0) {

					$owner_level_permit = [

						'project_group_id' => $shareGroup,
						'share_by_id' => $this->user_id,
						'owner_id' => $userOwnerId,
						'user_project_id' => $userProjectId,
						'share_permission' => 1,
						'project_level' => 1,
						'parent_id' => 0,
						'permit_read' => 1,
						'permit_edit' => 1,
						'permit_delete' => 1,
					];

					if (!empty($projectPermissionId)) {
						$owner_level_permit['id'] = $projectPermissionId;
					}
					// pr($owner_level_permit, 1);
					if ($this->ProjectPermission->save($owner_level_permit)) {
						$allInsertedId['project'] = $this->ProjectPermission->getLastInsertId();
					} else {
						$response = false;
					}

				} else {
					// GET ALL DIFFERENT TYPE OF PERMISSIONS DATA FROM POST
					$prjPermit = (isset($post['ProjectPermission'])) ? $post['ProjectPermission'][$projectId] : null;

					$wsPermit = (isset($post['WorkspacePermission'])) ? $post['WorkspacePermission'] : null;

					$elPermit = (isset($post['ElementPermission'])) ? $post['ElementPermission'] : null;

					$allInsertedId = ['project' => null, 'workspace' => null, 'element' => null];


					### Add Project Permissions
					if (!empty($prjPermit)) {

						$project_permissions = [
							'project_group_id' => $shareGroup,
							'share_by_id' => $this->user_id,
							'owner_id' => $userOwnerId,
							'user_project_id' => $userProjectId,
							'parent_id' => 0,
							'share_permission' => $propogationEnable,
							'project_level' => 0,
						];
						foreach ($prjPermit as $pkey => $val) {
							$project_permissions[$pkey] = (!empty($val) && $val > 0) ? 1 : 0;
						}

						if (!empty($project_permissions)) {

							$this->ProjectPermission->id = (isset($projectPermissionId) && !empty($projectPermissionId)) ? $projectPermissionId : null;

							if ($this->ProjectPermission->save($project_permissions)) {
								$allInsertedId['project'] = $this->ProjectPermission->getLastInsertId();
							} else {
								$response = false;
							}

						}
					}

					### Add Workspace Permissions
					if (!empty($wsPermit)) {

						$workspace_permissions = null;

						foreach ($wsPermit as $wsid => $val) {

							$wsPermitions = null;

							$wsPermitions = [
								'project_group_id' => $shareGroup,
								'user_project_id' => $userProjectId,
							];

							# GET project_workspace_id FROM project_id and workspace_id VALUE
							$project_workspace_id = workspace_pwid($projectId, $wsid);

							if (isset($project_workspace_id) && !empty($project_workspace_id)) {

								$wsPermitions['project_workspace_id'] = $project_workspace_id;

								if (!empty($val) && is_array($val)) {
									# CREATE MULTI-ARRAY TO INSERT MULTIPLE DATA AT ONCE
									foreach ($val as $wkey => $wval) {
										$wsPermitions[$wkey] = (!empty($wval) && $wval > 0) ? 1 : 0;
									}
								}

								$workspace_permissions[]['WorkspacePermission'] = $wsPermitions;
							}
						}

						if (!empty($workspace_permissions)) {
							if ($this->WorkspacePermission->saveAll($workspace_permissions)) {
								$allInsertedId['workspace'] = $this->WorkspacePermission->inserted_ids;
							} else {
								$response = false;
							}
							//pr($allInsertedId['workspace']);
						}
					}
					### Add Element Permissions
					if (!empty($elPermit)) {

						$element_permissions = null;
						// $element_permissions = [];
						// INSERT NEXT ROW ONLY IF THE PARENT IS CREATED SUCCESSFULLY


						foreach ($elPermit as $elid => $val) {
							$elPermitions = null;

							$elPermitions = [
								'project_group_id' => $shareGroup,
								'project_id' => $project_id,
							];

							if (!empty($val) && is_array($val)) {

								foreach ($val as $ekey => $eval) {
									# GET project_id and workspace_id VALUES WITH EXTRACTING VALUE OF EACH
									$elDetail = explode('_', $eval);

									if (!empty($elDetail) && is_array($elDetail)) {

										list($wsID, $aID, $eID) = $elDetail;

										if (!empty($wsID) && !empty($aID) && !empty($eID)) {
											// ADD READ PERMISSION OF THIS ELEMENT
											$ws_project_id = workspace_pid($wsID);
											$ws_read['user_project_id'] = project_upid($ws_project_id);
											$ws_read['project_workspace_id'] = workspace_pwid($ws_project_id, $wsID);
											$ws_read['project_group_id'] = $shareGroup;

											if (!$this->WorkspacePermission->hasAny($ws_read)) {
												$ws_read['permit_read'] = 1;
												if ($this->WorkspacePermission->saveAll($ws_read)) {
													$ws_insert_id = $this->WorkspacePermission->inserted_ids;
												}
											}
											$elPermitions['workspace_id'] = $wsID;
											$elPermitions['element_id'] = $eID;
											$elPermitions[$ekey] = (!empty($eval) && $eval > 0) ? 1 : 0;
										}
									}
								}
							}
							$element_permissions[]['ElementPermission'] = $elPermitions;
						}


						if (!empty($element_permissions)) {
							if ($this->ElementPermission->saveAll($element_permissions )) {

									// $eshare_insert_id = $this->ElementPermission->inserted_ids;
									//start procedure ====================================
									if( PROCEDURE_MODE == 1 ) {

										$groupUser = array();
										$eshare_insert_id = array();
										$groupUser = $this->objView->loadHelper('Group')->group_users($shareGroup, true);
										if( isset($groupUser) && !empty($groupUser) ){


											$eshare_insert_id = $this->ElementPermission->find('all',array(
													'conditions'=>array(
															'ElementPermission.project_group_id'=>$shareGroup
														),'recursive'=>-1
												)
											);

										}

										 if( isset($groupUser) && !empty($groupUser) ){
											foreach($groupUser as $user_list){

												if( !empty($eshare_insert_id) ){

													foreach($eshare_insert_id as $elePermission){

														$epids = $elePermission['ElementPermission']['id'];
														$epProject_id = $elePermission['ElementPermission']['project_id'];
														$epWorkspace_id = $elePermission['ElementPermission']['workspace_id'];
														$epElement_id = $elePermission['ElementPermission']['element_id'];
														$epUser_id = $user_list;
														$eleArea_id = element_area($elePermission['ElementPermission']['element_id']);

														if( $this->objView->loadHelper('ViewModel')->projectPermitType($epProject_id, $epUser_id) ){
															$user_level = 1;
														} else {
															$user_level = 0;
														}

														//$this->ElementPermission->query("CALL share_element_insert($epUser_id,$epProject_id,$epWorkspace_id,$eleArea_id,$epElement_id,$user_level,$epids)");

													}

												}
											}
										}
									}
									//end procedure ====================================


							} else {
								$response = false;
							}
						}

					}
				}
			}
			if ($response) {
				$this->redirect(array('controller' => 'groups', 'action' => 'update_permissions', $project_id, $group_id));
			}

		}

		/* Select Projects */
		$projects = $this->Project->find('all', array(
			'joins' => array(
				array(
					'table' => 'user_projects',
					'alias' => 'UserProject',
					'type' => 'INNER',
					'conditions' => array(
						'UserProject.project_id = Project.id',
					),
				),
			),
			'conditions' => ['UserProject.owner_user' => 1, 'UserProject.user_id' => $this->user_id],
			'fields' => array('Project.id', 'Project.title'),
			'order' => 'UserProject.created ASC',
			'group' => ['UserProject.project_id'],

			'recursive' => -1,
		));

		$projects_list = (isset($projects) && !empty($projects)) ? Set::combine($projects, '{n}.Project.id', '{n}.Project.title') : null;
		$this->set('projects_list', $projects_list);

		$crumb = [
			'My Groups' => [
				'data' => [
					'url' => '/shares/my_groups',
					'class' => 'tipText',
					'title' => "My Groups",
					'data-original-title' => "My Groups",
				],
			],
			'last' => [
				'data' => [
					'title' => 'Update Group Permissions',
					'data-original-title' => 'Update Group Sharing',
				],
			],
		];
		if (isset($cat_crumb) && !empty($cat_crumb)) {
			$crumb = array_merge($cat_crumb, $crumb);
		}
		$viewData['crumb'] = (isset($crumb) && !empty($crumb)) ? $crumb : null;

		$this->set($viewData);

	}

	/*
		 * @name  	shared_projects
		 * @access	public
		 * @package  App/Controller/GroupsController
	*/

	public function shared_Totprojects() {

		$this->layout = 'inner';

		$viewData = null;

		$viewData['title_for_layout'] = 'Group Received Projects';
		$viewData['page_heading'] = 'Group Received Projects';
		$viewData['page_subheading'] = 'Your group projects';
		$count = 0;

		/* Select Projects */
		$projects = $this->Project->find('all', array(
			'joins' => array(
				array(
					'table' => 'user_projects',
					'alias' => 'UserProject',
					'type' => 'INNER',
					'conditions' => array(
						'UserProject.project_id = Project.id',
					),
				),
			),
			'conditions' => ['UserProject.owner_user' => 1, 'UserProject.user_id' => $this->user_id],
			'fields' => array('Project.id', 'Project.title'),
			'order' => 'UserProject.created ASC',
			'group' => ['UserProject.project_id'],

			'recursive' => -1,
		));

		$count = $this->ProjectGroupUser->find('count', ['conditions' => ['ProjectGroupUser.user_id' => $this->user_id, 'ProjectGroupUser.approved' => 1, 'ProjectGroup.id !=' => '', 'UserProject.id !=' => ''], 'recursive' => 1]);

		return $count;
	}

	public function shared_projects() {

		$this->layout = 'inner';

		$viewData = null;

		$viewData['title_for_layout'] = 'Group Received Projects';
		$viewData['page_heading'] = 'Group Received Projects';
		$viewData['page_subheading'] = 'Your group projects';

		/* Select Projects */
		$projects = $this->Project->find('all', array(
			'joins' => array(
				array(
					'table' => 'user_projects',
					'alias' => 'UserProject',
					'type' => 'INNER',
					'conditions' => array(
						'UserProject.project_id = Project.id',
					),
				),
			),
			'conditions' => ['UserProject.owner_user' => 1, 'UserProject.user_id' => $this->user_id],
			'fields' => array('Project.id', 'Project.title'),
			'order' => 'UserProject.created ASC',
			'group' => ['UserProject.project_id'],

			'recursive' => -1,
		));

		$viewData['list'] = $this->ProjectGroupUser->find('all', ['conditions' => ['ProjectGroupUser.user_id' => $this->user_id, 'ProjectGroupUser.approved' => 1, 'ProjectGroup.id !=' => ''], 'recursive' => 1]);

		$projects_list = (isset($projects) && !empty($projects)) ? Set::combine($projects, '{n}.Project.id', '{n}.Project.title') : null;
		$this->set('projects_list', $projects_list);
		// pr($viewData['list'], 1);
		$crumb = [
			'last' => [
				'data' => [
					'title' => 'Group Received Projects',
					'data-original-title' => 'Group Received Projects',
				],
			],
		];
		if (isset($cat_crumb) && !empty($cat_crumb)) {
			$crumb = array_merge($cat_crumb, $crumb);
		}
		$viewData['crumb'] = (isset($crumb) && !empty($crumb)) ? $crumb : null;

		$this->set($viewData);

	}

	/*
		 * @name  		pending_group_request
		 * @access		public
		 * @package  	App/Controller/GroupsController
	*/

	function pending_group_request() {

		return $this->ProjectGroupUser->find('count', array('conditions' => array('ProjectGroupUser.approved' => 0, 'ProjectGroupUser.user_id' => $this->user_id)));
	}

	/*
		 * @name  		get_project
		 * @access		public
		 * @package  	App/Controller/GroupsController
	*/

	public function get_projects() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$response = [
				'success' => false,
				'msg' => null,
				'content' => null,
			];

			if ($this->request->is('post') || $this->request->is('put')) {

				$project_type = $this->data['project_type'];

				if (isset($project_type) && !empty($project_type)) {

					$project_list = null;

					if ($project_type == 1) {
						$project_list = get_my_projects($this->user_id);
					} else if ($project_type == 2) {
						$project_list = get_rec_projects($this->user_id);
					}

					if (isset($project_list) && !empty($project_list)) {
						$response['content'] = (!empty($project_list)) ? $project_list : null;

						$response['success'] = true;

					} else {
						$response['msg'] = 'There are no projects found.';
					}
				}
			}
			echo json_encode($response);
			exit;
		}

	}

	/*
		 * @name  		get_users
		 * @access		public
		 * @package  	App/Controller/GroupsController
	*/

	public function get_users($project_id = null) {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$response = [
				'success' => false,
				'msg' => null,
				'content' => null,
			];

			if ($this->request->is('post') || $this->request->is('put')) {

				$project_id = $this->data['project_id'];

				if (isset($project_id) && !empty($project_id)) {

					$perm_users = get_selected_users($this->user_id, $project_id);

					if (isset($perm_users) && !empty($perm_users)) {

						// $groupUsers = $this->ProjectGroupUser->find('list', ['conditions' => [ 'ProjectGroupUser.user_project_id' => project_upid($project_id)], 'fields' => ['ProjectGroupUser.id', 'ProjectGroupUser.user_id' ]]);

						// $result = null;
						// if( (isset($perm_users) && !empty($perm_users))) {
						// $result = $perm_users;
						// if (isset($groupUsers) && !empty($groupUsers)) {
						// foreach($perm_users as $k => $v ) {
						// if( in_array($k, $groupUsers) ) {
						// unset($result[$k]);
						// }
						// }
						// }
						// }

						// pr($perm_users);
						// pr($groupUsers, 1);
						// pr($groupUsers, 1);

						$response['content'] = (isset($perm_users) && !empty($perm_users)) ? $perm_users : null;

						$response['success'] = true;

					} else {
						$response['msg'] = 'No users available for group.';
					}
				}
			}
			echo json_encode($response);
			exit;
		}

	}

	/*
		 * @name  		get_users_with_skills
		 * @access		public
		 * @package  	App/Controller/GroupsController
	*/

	public function get_users_with_skills($project_id = null) {
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';

			$response = [
				'success' => false,
				'msg' => null,
				'content' => null,
			];

			if ($this->request->is('post') || $this->request->is('put')) {
				$project_id = $this->data['project_id'];
				if (isset($project_id) && !empty($project_id)) {
					// $perm_users = project_outer_users($this->user_id, $project_id);
					$perm_users = get_selected_users($this->user_id, $project_id);
					if (isset($perm_users) && !empty($perm_users)) {
						$perms_users_ids = array_keys($perm_users);

						$view = new View();
						$viewModal = $view->loadHelper('Common');

						$skillSet = $viewModal->get_skill_of_users($perms_users_ids);
						$skills = [];
						if(!empty($skillSet)) {
							foreach ($skillSet as $key => $value) {
								$skills[$key]['label'] = $value['skills']['title'];
								$skills[$key]['value'] = $value['user_skills']['skill_id'];
							}
						}

						$subjectSet = $viewModal->get_subject_of_users($perms_users_ids);
						$subjects = [];
						if(!empty($subjectSet)) {
							foreach ($subjectSet as $key => $value) {
								$subjects[$key]['label'] = $value['subjects']['title'];
								$subjects[$key]['value'] = $value['user_subjects']['subject_id'];
							}
						}

						$domainSet = $viewModal->get_domain_of_users($perms_users_ids);
						$domains = [];
						if(!empty($domainSet)) {
							foreach ($domainSet as $key => $value) {
								$domains[$key]['label'] = $value['knowledge_domains']['title'];
								$domains[$key]['value'] = $value['user_domains']['domain_id'];
							}
						}

						$response['content']['user_list'] = (isset($perm_users) && !empty($perm_users)) ? $perm_users : null;
						$response['content']['skill_list'] = (isset($skills) && !empty($skills)) ? $skills : null;
						$response['content']['subject_list'] = (isset($subjects) && !empty($subjects)) ? $subjects : null;
						$response['content']['domain_list'] = (isset($domains) && !empty($domains)) ? $domains : null;
						$response['success'] = true;
					} else {
						$response['msg'] = 'No users available for group.';
					}
				}
			}
			echo json_encode($response);
			exit;
		}
	}

	/*
		 * @name  		apply_user_filter
		 * @access		public
		 * @package  	App/Controller/GroupsController
	*/

	public function apply_user_filter() {
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';

			$response = [
				'success' => false,
				'msg' => null,
				'content' => null,
			];
			if ($this->request->isJson()) {
				$type = $this->request->data['type'];
				$project_id = $this->request->data['project_id'];

				$availableUsers = (isset($this->request->data['available_users']) && !empty($this->request->data['available_users'])) ? trim($this->request->data['available_users']) : '';
				$usersArr = array_map('trim', explode(',', $availableUsers));
				$selUsers = implode(',',$usersArr);
				//$concatQuery = " AND tagged_user_id IN ($selUsers) ";

				$loggedInUserId = $this->Session->read("Auth.User.id");
				$selected = (isset($this->request->data['selected']) && !empty($this->request->data['selected'])) ? trim($this->request->data['selected']) : '';
				$match_all = (isset($this->request->data['is_match_all']) && trim($this->request->data['is_match_all']) != '') ? $this->request->data['is_match_all'] : false;

				if($selected != '') {
					if ($type == 'tag') {
						$this->loadModel('Tag');

						$termsArr = array_map('trim', explode(',', $selected));
						$selectedTags = implode("','",$termsArr);
						$tagCnt = count($termsArr);

						$matchAllCond = "";
						if($match_all) {
							$matchAllCond = " HAVING COUNT(DISTINCT tag) = $tagCnt ";
						}

						$findQuery = "SELECT
									tagged_user_id, user_details.first_name, user_details.last_name
								FROM tags
								LEFT JOIN users ON tags.tagged_user_id = users.id
								LEFT JOIN user_details ON users.id = user_details.user_id
								WHERE tags.user_id = $this->user_id AND tagged_user_id IN(
									SELECT tagged_user_id
									FROM `tags`
									WHERE 1 AND tag IN ('$selectedTags') AND user_id = $this->user_id AND tagged_user_id IN ($selUsers)
									GROUP BY tagged_user_id
									$matchAllCond
								)
								GROUP BY tagged_user_id ORDER BY user_details.first_name ASC";

						$tagUsers = $this->Tag->query($findQuery);
						$result = array();
						if (isset($tagUsers) && !empty($tagUsers)) {
							foreach($tagUsers as $k => $v) {
								$result[$k]['label'] = $v['user_details']['first_name'] . ' ' . $v['user_details']['last_name'];
								$result[$k]['value'] = $v['tags']['tagged_user_id'];
							}
						}

						$response['success'] = true;
						$response['content'] = $result;
					} elseif ($type == 'skill') {
						$this->loadModel('UserSkill');

						$project_id = $this->request->data['project_id'];

						$non_proj_users = array();
						$concatQuery = "";
						/*if(1 == 2 && $project_id > 0) {
							$view = new View();
							$viewModel = $view->loadHelper('Permission');
							$projUsers = $viewModel->users_on_project($project_id);
							if(!empty($projUsers)) {
								$user_ids = Set::extract('{n}/user_permissions/user_id', $projUsers);
								$nonProjUsersIdsImp = implode(',', $user_ids);
								$concatQuery = " AND user_skills.user_id NOT IN ($nonProjUsersIdsImp) ";
							}
						}*/

						$termsArr = array_map('trim', explode(',', $selected));
						$selectedSkills = implode("','",$termsArr);
						$skillCnt = count($termsArr);

						$matchAllCond = "";
						if($match_all) {
							$matchAllCond = " HAVING COUNT(DISTINCT skill_id) = $skillCnt ";
						}

						$findQuery = "SELECT
									user_skills.user_id, user_details.first_name, user_details.last_name
								FROM user_skills
								LEFT JOIN users ON user_skills.user_id = users.id
								LEFT JOIN user_details ON users.id = user_details.user_id
								WHERE user_skills.user_id IN(
									SELECT user_skills.user_id
									FROM `user_skills`
									WHERE 1 AND skill_id IN ('$selectedSkills') AND user_skills.user_id IN ($selUsers) $concatQuery
									GROUP BY user_skills.user_id
									$matchAllCond
								)
								GROUP BY user_skills.user_id ORDER BY user_details.first_name ASC";

						$skillUsers = $this->UserSkill->query($findQuery);
						$result = array();
						if (isset($skillUsers) && !empty($skillUsers)) {
							foreach($skillUsers as $k => $v) {
								$result[$k]['label'] = $v['user_details']['first_name'] . ' ' . $v['user_details']['last_name'];
								$result[$k]['value'] = $v['user_skills']['user_id'];
							}
						}

						$response['success'] = true;
						$response['content'] = $result;
					} elseif ($type == 'subject') {
						$this->loadModel('UserSubject');
						$project_id = $this->request->data['project_id'];

						$concatQuery = "";

						$termsArr = array_map('trim', explode(',', $selected));
						$selectedSubjects = implode("','",$termsArr);
						$subejctCnt = count($termsArr);

						$matchAllCond = "";
						if($match_all) {
							$matchAllCond = " HAVING COUNT(DISTINCT subject_id) = $subejctCnt ";
						}

						$findQuery = "SELECT
									user_subjects.user_id, user_details.first_name, user_details.last_name
								FROM user_subjects
								LEFT JOIN users ON user_subjects.user_id = users.id
								LEFT JOIN user_details ON users.id = user_details.user_id
								WHERE user_subjects.user_id IN(
									SELECT user_subjects.user_id
									FROM `user_subjects`
									WHERE 1 AND subject_id IN ('$selectedSubjects') AND user_subjects.user_id IN ($selUsers) $concatQuery
									GROUP BY user_subjects.user_id
									$matchAllCond
								)
								GROUP BY user_subjects.user_id ORDER BY user_details.first_name ASC";

						$subjectUsers = $this->UserSubject->query($findQuery);
						$result = array();
						if (isset($subjectUsers) && !empty($subjectUsers)) {
							foreach($subjectUsers as $k => $v) {
								$result[$k]['label'] = $v['user_details']['first_name'] . ' ' . $v['user_details']['last_name'];
								$result[$k]['value'] = $v['user_subjects']['user_id'];
							}
						}

						$response['success'] = true;
						$response['content'] = $result;
					} elseif ($type == 'domain') {
						$this->loadModel('UserDomain');
						$project_id = $this->request->data['project_id'];

						$concatQuery = "";

						$termsArr = array_map('trim', explode(',', $selected));
						$selectedDomains = implode("','",$termsArr);
						$domainCnt = count($termsArr);

						$matchAllCond = "";
						if($match_all) {
							$matchAllCond = " HAVING COUNT(DISTINCT domain_id) = $domainCnt ";
						}

						$findQuery = "SELECT
									user_domains.user_id, user_details.first_name, user_details.last_name
								FROM user_domains
								LEFT JOIN users ON user_domains.user_id = users.id
								LEFT JOIN user_details ON users.id = user_details.user_id
								WHERE user_domains.user_id IN(
									SELECT user_domains.user_id
									FROM `user_domains`
									WHERE 1 AND domain_id IN ('$selectedDomains') AND user_domains.user_id IN ($selUsers) $concatQuery
									GROUP BY user_domains.user_id
									$matchAllCond
								)
								GROUP BY user_domains.user_id ORDER BY user_details.first_name ASC";

						$domainUsers = $this->UserDomain->query($findQuery);
						$result = array();
						if (isset($domainUsers) && !empty($domainUsers)) {
							foreach($domainUsers as $k => $v) {
								$result[$k]['label'] = $v['user_details']['first_name'] . ' ' . $v['user_details']['last_name'];
								$result[$k]['value'] = $v['user_domains']['user_id'];
							}
						}

						$response['success'] = true;
						$response['content'] = $result;
					}
				} else {
					$perm_users = get_selected_users($this->user_id, $project_id);

					$result = array();
					if (isset($perm_users) && !empty($perm_users)) {
						$key = 0;
						foreach($perm_users as $k => $v) {
							$result[$key]['label'] = $v;
							$result[$key]['value'] = $k;
							$key++;
						}
					}
					$response['success'] = true;
					$response['content'] = $result;
				}
			}
			echo json_encode($response);
			exit;
		}
	}

	/*
		 * @name  		get_users
		 * @access		public
		 * @package  	App/Controller/GroupsController
	*/

	public function get_users_by_skills($project_id = null) {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$response = [
				'success' => false,
				'msg' => null,
				'content' => null,
			];

			if ($this->request->is('post') || $this->request->is('put')) {

				//  pr($this->data, 1);

				$project_id = $this->data['project_id'];
				$userIds = $this->data['userIds'];
				$skills = $this->data['skills'];

				$cond = [];
				$finalCond = ' ';
				if(isset($skills) && !empty($skills)){

					foreach($skills as $skill){
						$cond[] = 'UserSkill.skill_id ='.$skill ;
					}

					$finalCond = implode(" and ",$cond);
				}

				if (isset($project_id) && !empty($project_id)) {

					$perm_users = null;

					if (isset($skills) && !empty($skills)) {
						$this->User->unbindAll();
						// $this->User->bindModel(['hasMany' => ['UserSkill'], 'hasOne' => ['UserDetail']]);
						$user_skill_join = ['table' => 'user_skills', 'alias' => 'UserSkill', 'type' => 'INNER', 'conditions' => ['UserSkill.user_id = User.id']];
						$user_detail_join = ['table' => 'user_details', 'alias' => 'UserDetails', 'type' => 'INNER', 'conditions' => ['UserDetails.user_id = User.id']];
						$user_list = $this->User->find('all', [
							'joins' => [
								$user_skill_join,
								$user_detail_join,
							],
							'conditions' => [
								'UserSkill.user_id' => explode(',', $userIds),
								'UserSkill.skill_id' => $skills,
								//$finalCond
							],
							'fields' => ['UserDetails.user_id', 'UserSkill.id', 'CONCAT(UserDetails.first_name, " ", UserDetails.last_name) AS user_name'],
							'group' => array('UserSkill.user_id HAVING COUNT(UserSkill.id) = '.count($skills)),
						]);

						if (isset($user_list) && !empty($user_list)) {
							$perm_users = Set::combine($user_list, '{n}.UserDetails.user_id', '{n}.0.user_name');
						}
						//pr($perm_users);


						$perm_users = $this->Common->get_users_by_skills($userIds, $skills);

						/*$s = "";$t = "";
						foreach($skills as $k => $v) {
							$s .= " , SUM(CASE WHEN skill_id = ".$v." THEN 1 ELSE 0 END) AS T".$k." ";
							$t .= " 'T".$k."'+";
						}
						$t = rtrim($t, '+');


						$sql = "SELECT `UserDetails`.`user_id`, COUNT(".$t.") as tot, `UserSkill`.`user_id`, `UserSkill`.`id`, CONCAT(UserDetails.first_name, \" \", UserDetails.last_name) AS user_name ".$s."

							FROM `ideascast`.`users` AS `User` INNER JOIN `ideascast`.`user_skills` AS `UserSkill` ON (`UserSkill`.`user_id` = `User`.`id`) INNER JOIN `ideascast`.`user_details` AS `UserDetails` ON (`UserDetails`.`user_id` = `User`.`id`)  WHERE `UserSkill`.`user_id` IN (".$userIds.") AND `UserSkill`.`skill_id` IN (".implode(',', $skills).")  GROUP BY `UserSkill`.`user_id` HAVING tot = ".count($skills);
						$a = $this->User->query($sql);
						$final = array();
						foreach($a as $k => $f) {
							if($f[0]['tot'] == count($skills) ) {
								$final[$f['UserDetails']['user_id']] = $f[0]['user_name'];
							}
						}
						$perm_users = $final;*/
					}

					if (isset($perm_users) && !empty($perm_users)) {

						$response['content'] = (isset($perm_users) && !empty($perm_users)) ? $perm_users : null;

						$response['success'] = true;

					} else {
						//$response['msg'] = 'There are no user for selected skills.';
						$response['msg'] = 'No users';
					}
				}
			}
			echo json_encode($response);
			exit;
		}

	}


	public function update_group_title() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$response = [
				'success' => false,
			];

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				if((isset($post['id']) && !empty($post['id'])) && (isset($post['title']) && !empty($post['title']))) {
					$this->ProjectGroup->id = $post['id'];
					if($this->ProjectGroup->saveField('title', $post['title'])) {
						$response['success'] = true;
					}
				}
			}
			echo json_encode($response);
			exit;
		}
	}
	/*
		 * @name  		add_users_to_group
		 * @access		public
		 * @package  	App/Controller/GroupsController
	*/

	public function add_users_to_group($project_id = null, $group_id = null) {

		$this->layout = 'ajax';

		$perm_users = null;
		$msg = '';

		if ($this->request->params['isAjax']) {

			if (isset($group_id) && !empty($group_id)) {

				$perm_users = get_selected_users($this->user_id, $project_id);

				if (!isset($perm_users) || empty($perm_users)) {
					$msg = 'There are no user for this project.';
				}
				$groupDetail = null;
				$groupData = $this->ProjectGroup->find('first', ['conditions' => ['ProjectGroup.id' => $group_id], 'recursive' => -1]);

				if (isset($groupData) && !empty($groupData)) {
					$groupDetail = $groupData['ProjectGroup'];
				}
				$this->set('groupData', $groupDetail);
			}

			$this->set('group_id', $group_id);
			$this->set('project_id', $project_id);
			$this->setJsVar('project_id', $project_id);
			$this->set('perm_users', $perm_users);
			$this->render('/Groups/partials/add_users_to_group');
		}

	}

	/*
		 * @name  		attach_users_with_group
		 * @access		public
		 * @package  	App/Controller/GroupsController
	*/

	public function attach_users_with_group() {

		$perm_users = null;
		$msg = '';
		//
		if ($this->request->is('post') || $this->request->is('put')) {

			$post = $this->request->data;
			$project_id = $post['project_id'];
			$groupUsers = array();
			if (isset($post['ProjectGroupUsers']) && !empty($post['ProjectGroupUsers'])) {
				$data = null;
				foreach ($post['ProjectGroupUsers'] as $key => $val) {
					$data[$key]['ProjectGroupUser'] = [
						'project_group_id' => $post['group_id'],
						'user_project_id' => project_upid($post['project_id']),
						'user_id' => $val,
						'approved' => 0,
					];
					$groupUsers[] = $val;
					if (isset($post['to_url']) && !empty($post['to_url'])) {
						$data[$key]['ProjectGroupUser']['request_by'] = $this->user_id;
					}
				}

				$inserted_ids = null;
				if (isset($data) && !empty($data)) {

					if ($this->ProjectGroupUser->saveAll($data)) {
						$inserted_ids = $this->ProjectGroupUser->inserted_ids;
						// pr($inserted_ids, 1);
						// Add user project connection to mongo db
						// $this->Users->userConnections($groupUsers, $post['project_id']);

						// ============= START GROUP REQUEST EMAIL ========================
						$groupid = $data[0]['ProjectGroupUser']['project_group_id'];

						$projectdetail = $this->Project->findById($project_id);
						$projectname = '';
						if (isset($projectdetail['Project']['title']) && !empty($projectdetail['Project']['title'])) {
							$projectname = ucfirst(htmlentities($projectdetail['Project']['title'], ENT_QUOTES));
						}

						if (isset($groupid) && !empty($groupid)) {
							$pGroupdetail = $this->ProjectGroup->findById($groupid);
							$groupName = '';
							if (isset($pGroupdetail['ProjectGroup']['title']) && !empty($pGroupdetail['ProjectGroup']['title'])) {
								$groupName = ucfirst(htmlentities($pGroupdetail['ProjectGroup']['title'], ENT_QUOTES));
							}
						}

						if (isset($groupUsers) && !empty($groupUsers) && count($groupUsers) > 0) {
							foreach ($groupUsers as $useridVal) {

								$dasAnnotate = $this->EmailNotification->find('first', ['conditions' => ['notification_type' => 'group', 'personlization' => 'group_request', 'user_id' => $useridVal]]);

								if (!isset($dasAnnotate['EmailNotification']['email']) || $dasAnnotate['EmailNotification']['email'] == 1) {

									$this->groupRequestEmail($projectname, $project_id, $useridVal, $groupName);

								}

							}

							/************** socket messages **************/
							if (SOCKET_MESSAGES) {

								$current_user_id = $this->user_id;
								$grp_users = $groupUsers;
								$req_users = null;
								if (isset($grp_users) && !empty($grp_users)) {
									foreach ($grp_users as $key1 => $value1) {
										if (web_notify_setting($value1, 'group', 'group_request')) {
											$req_users[] = $value1;
										}
									}
								}

								$userDetail = get_user_data($this->user_id);
								$content = [
									'notification' => [
										'type' => 'group_request',
										'created_id' => $this->user_id,
										'project_id' => $project_id,
										'creator_name' => $userDetail['UserDetail']['full_name'],
										'subject' => 'Group request',
										'heading' => 'Group: ' . htmlentities($groupName, ENT_QUOTES),
										'sub_heading' => 'Project: ' . htmlentities($projectname, ENT_QUOTES),
										'date_time' => timezoneTimeConvertor($this->user_id, date('Y-m-d g:iA'), $format = 'd M Y g:iA') . user_country($this->user_id),
									],
								];
								if (is_array($req_users)) {
									$content['received_users'] = array_values($req_users);
								}

								$request = array(
									'header' => array(
										'Content-Type' => 'application/json',
									),
								);
								$content = json_encode($content);
								$HttpSocket = new HttpSocket([
									'ssl_verify_host' => false,
									'ssl_verify_peer_name' => false,
									'ssl_verify_peer' => false,
								]);

								$results = $HttpSocket->post(CHATURL . '/serveremit', $content, $request);
							}
							/************** socket messages **************/
						}

						//die("1111");
						// ============= END GROUP REQUEST EMAIL ==========================

					}
				}
			}

			if (isset($post['to_url']) && !empty($post['to_url'])) {
				$this->redirect(array('controller' => 'groups', 'action' => 'shared_projects'));
			} else {
				$this->redirect(array('controller' => 'shares', 'action' => 'my_groups', $post['group_id']));
			}
		}

	}

	/*
		 * @name  		attach_users_with_group
		 * @access		public
		 * @package  	App/Controller/GroupsController
	*/

	public function delete_group($project_id = null, $group_id = null) {

		$perm_users = null;
		$msg = '';

		$post = $this->request->data;
		
 
		$this->loadModel('ProgramProject');
		$this->loadModel('Program');		

		if (isset($project_id) && !empty($project_id) && isset($group_id) && !empty($group_id)) {

			$pgu_id = $this->ProjectGroupUser->find('list', ['conditions' => ['ProjectGroupUser.project_group_id' => $group_id]]);
			
			$pgus_id = $this->ProjectGroupUser->find('list', ['conditions' => ['ProjectGroupUser.project_group_id' => $group_id],'fields' => array('ProjectGroupUser.user_id')]);
			
			 
			$program_ids = $this->Program->find('list', ['conditions' => ['Program.created_by' => $pgus_id ],'fields' => array('Program.id')]);
			 
			if(isset($program_ids) && !empty($program_ids)){
			
				$ids = $this->ProgramProject->find('list', ['conditions' => ['ProgramProject.project_id' => $project_id , 'ProgramProject.program_id' => $program_ids  ],'fields' => array('ProgramProject.id')]);
			
			if(isset($ids) && !empty($ids)){
			 
				 $this->ProgramProject->deleteAll(array('ProgramProject.id' => $ids));
			
			}
			
			}
		 
			

			$ppu_id = $this->ProjectPermission->find('list', ['conditions' => ['ProjectPermission.project_group_id' => $group_id]]);

			$wpu_id = $this->WorkspacePermission->find('list', ['conditions' => ['WorkspacePermission.project_group_id' => $group_id]]);

			$epu_id = $this->ElementPermission->find('list', ['conditions' => ['ElementPermission.project_group_id' => $group_id]]);

			$this->ProjectGroup->id = $group_id;
			$gdata['ProjectGroup'] = ['is_deleted' => 1];

			if ($this->ProjectGroup->save($gdata)) {}

			$this->ProjectPermission->id = $ppu_id;
			$pdata['ProjectPermission'] = ['is_deleted' => 1];

			if ($this->ProjectPermission->save($pdata)) {}

			if ($this->ProjectGroupUser->delete($pgu_id)) {}
			if (isset($wpu_id) && !empty($wpu_id)) {
				if ($this->WorkspacePermission->delete($wpu_id)) {}
			}

			if (isset($epu_id) && !empty($epu_id)) {
				if ($this->ElementPermission->delete($epu_id)) {}
			}

			// pr($wpu_id );
			// pr($epu_id, 1);
			echo json_encode(['success' => true]);
			exit();

		}

		$this->redirect(array('controller' => 'shares', 'action' => 'my_groups'));

	}

	/*
		 * @name  	create_group
		 * @access	public
		 * @package  App/Controller/GroupsController
	*/

	public function create_group() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$response = [
				'success' => false,
				'msg' => null,
				'content' => null,
				'socket_content' => null,
			];

			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data['Group'];
				 //pr($post);

				//$users = (isset($post['users']) && !empty($post['users'])) ? $post['users'] : null;
				$users = (isset($this->request->data['userSelectedIds']) && !empty($this->request->data['userSelectedIds'])) ? explode(',', $this->request->data['userSelectedIds']) : null;
				$user_project_id = (isset($post['project_id']) && !empty($post['project_id'])) ? project_upid($post['project_id']) : null;
				$title = (isset($post['title']) && !empty($post['title'])) ? trim($post['title']) : null;

				if ((isset($users) && !empty($users)) && (isset($user_project_id) && !empty($user_project_id)) && (isset($title) && !empty($title))) {

					$groupData['title'] = $title;
					$groupData['group_owner_id'] = $this->user_id;
					$groupData['user_project_id'] = $user_project_id;
					$groupData['share_permission'] = 2;

					if ($this->ProjectGroup->save($groupData)) {

						$pg_insert_id = $this->ProjectGroup->getLastInsertId();
						$content['project_group'] = $pg_insert_id;

						/*********** Save Default Permissions for newly created Group ****************/
						$project_id = $post['project_id'];
						$projectOwnerId = project_owner($post['project_id']);
						$default_permit = [
							'project_group_id' => $pg_insert_id,
							'share_by_id' => $this->user_id,
							'owner_id' => $projectOwnerId,
							'user_project_id' => $user_project_id,
							'share_permission' => 0,
							'project_level' => 0,
							'parent_id' => 0,
							'permit_read' => 1,
							'permit_edit' => 0,
							'permit_delete' => 0,
						];

						if ($this->ProjectPermission->save($default_permit)) {
							$default_permit_id = $this->ProjectPermission->getLastInsertId();
						}

						/***************************/

						if (isset($pg_insert_id) && !empty($pg_insert_id) && isset($users) && !empty($users)) {
							$i = 0;
							$grpUsers = null;
							$groupUsersforEmail = array();
							foreach ($users as $key => $usr) {

								$grpUsers[$i]['ProjectGroupUser']['id'] = null;
								$grpUsers[$i]['ProjectGroupUser']['user_id'] = $usr;
								$grpUsers[$i]['ProjectGroupUser']['project_group_id'] = $pg_insert_id;
								$grpUsers[$i]['ProjectGroupUser']['user_project_id'] = $user_project_id;
								$groupUsersforEmail[] = $usr;
								$i++;
							}

							if (isset($grpUsers) && !empty($grpUsers)) {

								if ($this->ProjectGroupUser->saveAll($grpUsers)) {


									// Add group users to user-project-connection into mongo db
									// $this->Users->userConnections($post['users'], $post['project_id']);

									$content['group_users'] = $this->ProjectGroupUser->inserted_ids;
									$response['success'] = true;

									// ============= START GROUP REQUEST EMAIL ========================
									$groupid = $pg_insert_id;

									$projectdetail = $this->Project->findById($project_id);
									$projectname = '';
									if (isset($projectdetail['Project']['title']) && !empty($projectdetail['Project']['title'])) {
										$projectname = ucfirst(strip_tags($projectdetail['Project']['title']));
									}

									if (isset($groupid) && !empty($groupid)) {
										$pGroupdetail = $this->ProjectGroup->findById($groupid);
										$groupName = '';
										if (isset($pGroupdetail['ProjectGroup']['title']) && !empty($pGroupdetail['ProjectGroup']['title'])) {
											$groupName = ucfirst(strip_tags($pGroupdetail['ProjectGroup']['title']));
										}
									}

									if (isset($groupUsersforEmail) && !empty($groupUsersforEmail) && count($groupUsersforEmail) > 0) {
										foreach ($groupUsersforEmail as $useridVal) {

											$usersDetails = $this->User->find('first', array('conditions' => array('User.id' => $useridVal)));

											$dasAnnotate = $this->EmailNotification->find('first', ['conditions' => ['notification_type' => 'group', 'personlization' => 'group_request', 'user_id' => $useridVal]]);

											if ((!isset($dasAnnotate['EmailNotification']['email']) || $dasAnnotate['EmailNotification']['email'] == 1) && (!isset($usersDetails['User']['email_notification']) || $usersDetails['User']['email_notification'] == 1)) {

												$this->groupRequestEmail($projectname, $project_id, $useridVal, $groupName);
											}
										}

										/************** socket messages **************/
										if (SOCKET_MESSAGES) {
											$current_user_id = $this->user_id;
											$grp_users = $groupUsersforEmail;
											$req_users = null;
											if (isset($grp_users) && !empty($grp_users)) {
												foreach ($grp_users as $key1 => $value1) {
													if (web_notify_setting($value1, 'group', 'group_request')) {
														$req_users[] = $value1;
													}
												}
											}

											$userDetail = get_user_data($this->user_id);
											$content = [
												'notification' => [
													'type' => 'group_request',
													'created_id' => $this->user_id,
													'creator_name' => $userDetail['UserDetail']['full_name'],
													'subject' => 'Group request',
													'heading' => 'Group: ' . strip_tags(getFieldDetail('ProjectGroup', $pg_insert_id, 'title')),
													'sub_heading' => 'Project: ' . strip_tags(getFieldDetail('Project', $project_id, 'title')),
													'date_time' => timezoneTimeConvertor($this->user_id, date('Y-m-d g:iA'), $format = 'd M Y g:iA') . user_country($this->user_id),
												],
											];
											if (is_array($req_users)) {
												$content['received_users'] = array_values($req_users);
											}

											$response['socket_content'] = $content;
										}
										/************** socket messages **************/
									}

									//die("1111");
									// ============= END GROUP REQUEST EMAIL ==========================

								}
							}

							$perm_users = get_selected_users($this->user_id, $post['project_id']);

							$groupUsers = $this->ProjectGroupUser->find('list', ['conditions' => ['ProjectGroupUser.user_project_id' => $user_project_id], 'fields' => ['ProjectGroupUser.user_id', 'ProjectGroupUser.user_id']]);

							$result = null;
							if (isset($perm_users) && !empty($perm_users) && isset($groupUsers) && !empty($groupUsers)) {
								$result = $perm_users;
								foreach ($perm_users as $k => $v) {
									if (in_array($k, $groupUsers)) {
										// e('asdf');
										unset($result[$k]);
									}
								}
							}
							// pr($groupUsers );
							// pr($result, 1);
							// $response['content'] = $result;
							$response['content'] = ['project_id' => $post['project_id'], 'group_id' => $pg_insert_id];
						}
					} else {
						$response['msg'] = 'There are no user for this project.';

					}
				} else {
					if (empty($title)) {
						$response['content']['title'] = 'Project Group Title is required';
					}
					if (empty($users)) {
						$response['content']['users'] = 'At least one Group Member is required';
					}
				}
			}
			echo json_encode($response);
			exit;
		}

	}

	public function group_users($gid, $accepted = false, $pid = null) {

		$conditions['ProjectGroupUser.project_group_id'] = $gid;
		if ($accepted) {
			$conditions['ProjectGroupUser.approved'] = 1;
		}

		$datas = $this->ProjectGroupUser->find('all', array('conditions' => $conditions));

		if (isset($datas) && !empty($datas)) {

			foreach ($datas as $dat) {
				$data[] = $dat['ProjectGroupUser']['user_id'];
			}
		}

		$data = isset($data) ? $data : array();
		$this->set('data', $data);
		$this->set('gid', $gid);
		$this->set('pid', $pid);
	}

	/*
		 * @name  		get_skills
		 * @access		public
		 * @package  	App/Controller/GroupsController
	*/

	public function get_skills() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			// pr($this->request->query['term'] , 1);
			$response = [
				'success' => false,
				'msg' => null,
				'content' => null,
			];
			$skills = [];
			if ($this->request->isJson()) {
				if (isset($this->request->query['term']) && !empty($this->request->query['term'])) {
					$excludeSkills = (isset($this->request->query['selectedSkills']) && !empty($this->request->query['selectedSkills'])) ? $this->request->query['selectedSkills'] : '';
					$skills = $this->Skill->getSkills($this->request->query['term'], $excludeSkills);
					if (isset($skills) && !empty($skills)) {
						$response['success'] = true;
						$response['content'] = $skills;
					}
				}
			}
			echo json_encode($response);
			exit;
		}

	}

	public function get_group_users($group_id = null, $project_id = null) {

		$this->layout = 'ajax';

		if ($this->request->isAjax()) {

			$html = null;

			$group_users = group_users($group_id);

			$this->set('data', $group_users);
			$this->set('gid', $group_id);
			$this->set('pid', $project_id);

			$this->render('/Groups/partials/get_group_users');

		}
	}

	public function group_project($group_id = null, $project_id = null, $user_id = null) {

		$this->layout = 'ajax';

		if ($this->request->isAjax()) {

			$data = array();
			$html = null;

			if ($this->request->is('post') || $this->request->is('put')) {

				$conditions = null;
				$conditions['UserProject.status'] = 1;
				$conditions['UserProject.project_id !='] = '';

				$conditions['UserProject.project_id'] = $project_id;

				$projects = $this->UserProject->find('all', array(
					'joins' => array(
						array(
							'table' => 'projects',
							'alias' => 'Projects',
							'type' => 'INNER',
							'conditions' => array(
								'UserProject.project_id = Projects.id',
							),
						),
					),
					'conditions' => $conditions,
					'fields' => array('UserProject.*', 'Projects.*'),
					'order' => 'UserProject.modified DESC',
					'group' => ['UserProject.project_id'],
					'recursive' => 1,
				));

				$view = new View($this, false);
				$view->viewPath = 'Groups/partials'; // Directory inside view directory to search for .ctp files
				$view->layout = false; // if you want to disable layout
				$view->set('projects', $projects);
				$view->set('group_id', $group_id);
				$view->set('project_id', $project_id);
				$view->set('project_id', $project_id);
				$view->set('share', $user_id);

				$html = $view->render('group_project');
			}

			echo json_encode($html);
			exit();
		}
	}

	public function groupRequestEmail($project_name = null, $project_id = null, $user_id = null, $groupName = null) {

		$projectname = '';
		$projectname = $project_name;

		$view = new View();
		$commonHelper = $view->loadHelper('Common');

		$deletedUser = $this->User->findById($this->Session->read('Auth.User.id'));
		$loggedInUser = $deletedUser['UserDetail']['first_name'] . ' ' . $deletedUser['UserDetail']['last_name'];

		$usersDetails = $this->User->findById($user_id);
		$projectAction = SITEURL . 'shares/group_requests';

		if (!isset($usersDetails['User']['email_notification']) || $usersDetails['User']['email_notification'] == 1) {

			$owner_name = $usersDetails['UserDetail']['first_name'] . ' ' . $usersDetails['UserDetail']['last_name'];
			$email = new CakeEmail();
			$email->config('Smtp');
			// $email->from(array(ADMIN_EMAIL => SITENAME));
			$email->from(array(ADMIN_FROM_EMAIL => SITENAME));
			$email->to($usersDetails['User']['email']);
			$email->subject(SITENAME . ': Group request');
			$email->template('group_request_email');
			$email->emailFormat('html');
			$email->viewVars(array('project_name' => $projectname, 'owner_name' => $owner_name, 'sentby' => $loggedInUser, 'groupName' => $groupName,'open_page'=>$projectAction));
			$email->send();

		}

	}

	public function project_people($gid, $pid, $flag, $accepted = true) {

		if ($this->request->is('get')) {

			$this->layout = 'ajax';

			$conditions['ProjectGroupUser.project_group_id'] = $gid;
			if ($accepted) {
				$conditions['ProjectGroupUser.approved'] = 1;
			}

			$datas = $this->ProjectGroupUser->find('all', array('conditions' => $conditions));

			if (isset($datas) && !empty($datas)) {

				foreach ($datas as $dat) {
					$data[] = $dat['ProjectGroupUser']['user_id'];
				}
			}

			if (isset($data)) {
				$this->set('data', $data);
			}

			$this->set('project_id', $pid);
			$this->set('flag', $flag);

			return isset($data) ? $data : array();
		}

	}

}
