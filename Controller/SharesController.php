<?php

App::uses('Controller/Component', 'Auth', 'Session', 'RequestHandler');
App::uses('Sanitize', 'Utility');
App::uses('CakeEmail', 'Network/Email');

App::import('Lib', 'SharingTree');

class SharesController extends AppController {

	public $name = 'Shares';
	public $uses = ['User', 'UserDetail', 'Category', 'UserProject', 'Project', 'Workspace', 'Area', 'ProjectWorkspace', 'Element', 'ElementPermission', 'WorkspacePermission', 'ProjectPermission', 'ElementPropagate', 'WorkspacePropagate', 'ProjectPropagate', 'ProjectGroupUser', 'ProjectGroup', 'ProjectGroupHistory', 'UserSetting', 'Aligned', 'AdminSetting', 'Skill', 'SkillPdf','ShareElement','SkillDetail'];
	public $user_id = null;
	public $pagination = null;
	public $components = array('Common', 'Group', 'Users');

	/**
	 * Helpers
	 *
	 * @var array
	 */
	public $helpers = array('Html', 'Form', 'Session', 'Text', 'ViewModel', 'Common', 'Group', 'Permission');

	public function beforeFilter() {
		parent::beforeFilter();

		$this->user_id = $this->Auth->user('id');


		if( $_SERVER['HTTP_HOST'] != LOCALIP  && ($this->request['action']!='show_profile' && $this->request['action']!='show_org_profile'))  {

			if( $this->Session->read('Auth.User.role_id') == 3 ){
				$this->redirect(['controller' => 'organisations','action' => 'dashboard']);
			}
			if( $this->Session->read('Auth.User.role_id') == 1 ){
				$this->redirect(['controller' => 'dashboard','action' => 'index']);
			}
		}

	}

	public function propagate_sharing1() {

		$this->layout = 'inner';

		$this->set('title_for_layout', __('Page Sample', true));
	}

	/*
		 * @name  	index
		 * @access	public
		 * @package  App/Controller/SharesController
	*/

	public function index($project_id = null, $share_user_id = null, $share_action = 1) {

		$this->layout = 'inner';
		if ($this->request->is('post') || $this->request->is('put')) {

			if (isset($this->request->data['Share']['user_id']) && !empty($this->request->data['Share']['user_id'])) {

				$shareUser = $this->request->data['Share']['user_id'];
			}

			if (isset($this->request->data['Share']['share_action']) && !empty($this->request->data['Share']['share_action'])) {
				$share_action = $this->request->data['Share']['share_action'];
			}

			$this->redirect(array('controller' => 'shares', 'action' => 'index', $project_id, $shareUser, $share_action));

		}

		$this->setJsVar('project_id', $project_id);

		if (empty($share_user_id)) {

			$this->set('title_for_layout', 'Project Sharing - User Selection');
			$this->set('page_heading', 'User Selection');
		} else {
			$this->set('title_for_layout', 'Project Sharing - Set role and permissions');
			$this->set('page_heading', 'Set role and permissions');
		}

		$share_user_id = (isset($share_user_id) && !empty($share_user_id)) ? $share_user_id : 0;
		$this->set('shareUser', $share_user_id);

		// if share option is for add new append 1 with the URL, otherwise append 2
		$share_action = (isset($share_action) && !empty($share_action)) ? $share_action : 1;
		$this->set('share_action', $share_action);

		// -------- Get project detail if the logged in user is associated with this project and have propagation permissions
		$this->ProjectPermission->unbindAll();

		$perm_users = $this->ProjectPermission->find('count', ['conditions' => ['ProjectPermission.user_project_id' => project_upid($project_id), 'ProjectPermission.user_id' => $this->user_id, 'ProjectPermission.share_permission' => 1]]);
		$project_detail = null;

		$ownerUser = 0;
		$propagatePermission = 0;

		if (isset($perm_users) && !empty($perm_users)) {
			$propagatePermission = 1;
		}
		// -------- Project Detail of Passed id
		$project_detail = $this->UserProject->find('first', ['conditions' => ['UserProject.id' => project_upid($project_id)], 'recursive' => 2]);

		// CHECK FOR THE LOGGED IN USER, THAT THIS USER IS OWNER OF THIS PROJECT OR NOT
		if (!empty($project_detail) && (isset($project_detail['UserProject']) && !empty($project_detail['UserProject']))) {
			if ($project_detail['UserProject']['owner_user'] == 1 && $project_detail['UserProject']['user_id'] == $this->user_id) {
				$ownerUser = 1;
			}
		}

		if ($ownerUser == 0 && $propagatePermission == 0) {
			$fmsg_data = ['message' => 'You have not authorized to further propagate.', 'type' => 'error'];
			CakeSession::write('fmessage', $fmsg_data);
			return $this->redirect(array('controller' => 'projects', 'action' => 'lists'));
		}

		$this->set('ownerUser', $ownerUser);
		$this->set('propagatePermission', $propagatePermission);

		$this->set('project_detail', $project_detail);
		$this->set('project_id', $project_id);

		// Get all users that are not associated with this project anymore
		$users_list = get_selected_user_project($this->user_id, $project_id);
		// pr($users_list, 1);
		$this->set(compact('users_list'));

		// Get all groups that the user his owned and are associated with this user
		$grps = $this->ProjectGroup->find('all', [
			'joins' => [
				[
					'table' => 'project_permissions',
					'alias' => 'ProjectPermissions',
					'type' => 'INNER',
					'conditions' => [
						'ProjectPermissions.user_project_id = ProjectGroup.user_project_id',
					],
				],
			],
			'conditions' => [
				'ProjectGroup.group_owner_id' => $this->user_id,
				'ProjectGroup.user_project_id' => project_upid($project_id),
			],
			// 'recursive' => -1,
			'group' => ['ProjectGroup.user_project_id'],
		]
		);
		$grp_users = $this->ProjectGroup->ProjectGroupUser->find('all', [
			'conditions' => [
				'ProjectGroup.user_project_id' => project_upid($project_id),
			],
		]);
		// e($this->ProjectGroup->_query());
		// pr($grp_users, 1);
		$groups_list = get_selected_user_project($this->user_id, $project_id);

		$this->set(compact('groups_list'));

		$pp_data = $wp_data = $ep_data = [];
		$exist_permissions = null;
		$pp_data_count = $wp_data_count = $ep_data_count = 0;

		if (isset($project_detail) && !empty($project_detail)) {

			$userProjectId = $project_detail['UserProject']['id'];
			$projectWorkspaceId = Set::extract($project_detail, '/Project/ProjectWorkspace/id');
			$workspaceId = Set::extract($project_detail, '/Project/ProjectWorkspace/workspace_id');

			// -------- Check this user for previous data exists or not
			if (!empty($share_user_id)) {

				$pp_data = $this->ProjectPermission->find('first', [
					'conditions' => [
						'ProjectPermission.user_id' => $share_user_id,
						'ProjectPermission.user_project_id' => $userProjectId,
					],
					'recursive' => -1,
				]);

				$pp_data_count = ( isset($pp_data) && !empty($pp_data) ) ? count($pp_data) : 0;
				if (!empty($pp_data_count)) {
					$exist_permissions['pp_data'] = $pp_data;
					$exist_permissions['pp_data_count'] = $pp_data_count;
				}

				// -------- Get project_workspace_id of the current project and find those in workspace_permissions for this user
				$wp_data = $this->WorkspacePermission->find('all', [
					'conditions' => [
						'WorkspacePermission.user_id' => $share_user_id,
						'WorkspacePermission.user_project_id' => $userProjectId,
						'WorkspacePermission.project_workspace_id' => $projectWorkspaceId,
					],
					'recursive' => -1,
				]);
				$wp_data_count = ( isset($wp_data) && !empty($wp_data) ) ? count($wp_data) : 0;
				if (!empty($wp_data_count)) {
					$exist_permissions['wp_data'] = $wp_data;
					$exist_permissions['wp_data_count'] = $wp_data_count;
				}

				$view = new View();
				$viewModal = $view->loadHelper('ViewModel');

				// -------- Get all area ids of all workspaces
				$ws_area = $viewModal->workspace_areas($workspaceId, false, true);
				// -------- Get all element ids of those
				$elm = $viewModal->area_elements($ws_area, true);

				// -------- Find all element ids in element_permissions
				$ep_data = $this->ElementPermission->find('all', [
					'conditions' => [
						'ElementPermission.user_id' => $share_user_id,
						'ElementPermission.project_id' => $project_id,
						'ElementPermission.workspace_id' => $workspaceId,
						'ElementPermission.element_id' => $elm,
					],
					'recursive' => -1,
				]);

				$ep_data_count = ( isset($ep_data) && !empty($ep_data) ) ? count($ep_data) : 0;
				if (!empty($ep_data_count)) {
					$exist_permissions['ep_data'] = $ep_data;
					$exist_permissions['ep_data_count'] = $ep_data_count;
				}

			}
			$this->set('exist_permissions', $exist_permissions);

			// -------- CREATE BREADCRUMB
			$extra_crumb = null;
			if ($project_detail['UserProject']['user_id'] != $this->user_id) {

				$extra_crumb = [
					'Received Projects' => [
						'data' => [
							'url' => '/projects/share_lists',
							'class' => 'tipText',
							'title' => 'Received Projects',
							'data-original-title' => 'Received Projects',
						],
					],
				];
			} else {
				//$extra_crumb = get_category_list($project_id);
			}

			$project_title = _strip_tags($project_detail['Project']['title']);

			$crumb = [
				'Summary' => [
					'data' => [
						'url' => '/projects/index/' . $project_id,
						'class' => 'tipText',
						'title' => $project_detail['Project']['title'],
						'data-original-title' => $project_detail['Project']['title'],
					],
				],
				'last' => [
					'data' => [
						'title' => 'Advanced Sharing',
						'data-original-title' => 'Advanced Sharing',
					],
				],
			];
			/*if (isset($extra_crumb) && !empty($extra_crumb)) {
				$crumb = array_merge($extra_crumb, $crumb);
			}*/

			$this->set('crumb', $crumb);
		}

	}

	/*
		 * @name  	manage_sharing: Add Sharing
		 * @access	public
		 * @package  App/Controller/SharesController
	*/
	public function manage_sharing($project_id = null, $share_user_id = null, $share_action = 1) {

		if ($this->request->is('post') || $this->request->is('put')) {
			$post = $this->request->data;


			$this->Common->getProjectAllUser($project_id, $share_user_id);

			$this->create_sharing();
			// $this->create_sharing(['page_name' => 'manage_sharing', 'project_id' => $project_id, 'share_user_id' => $share_user_id, 'share_action' => $share_action]);

		}
	}

	function create_sharing() {

		if ($this->request->is('post') || $this->request->is('put')) {
			$post = $this->request->data;

			$refer_redirect = (isset($this->request->data['ShareRefer']['refer']) && !empty($this->request->data['ShareRefer']['refer'])) ? $this->request->data['ShareRefer']['refer'] : '';

			// pr($post );die;
			if ((isset($post['Share']['user_id']) && !empty($post['Share']['user_id'])) && (isset($post['Share']['project_id']) && !empty($post['Share']['project_id']))) {

				// Add user project connection to mongo db
				$this->Users->userConnections($post['Share']['user_id'], $post['Share']['project_id']);

				$propogationEnable = (isset($post['Share']['share_permission'])) ? $post['Share']['share_permission'] : 0;

				$shareUser = $post['Share']['user_id'];

				$projectId = $post['Share']['project_id'];

				$project_level = (isset($post['Share']['project_level']) && !empty($post['Share']['project_level'])) ? $post['Share']['project_level'] : 0;

				# GET USER-PROJECT ID OF SUPPLIED PROJECT
				$userProjectId = project_upid($projectId);
				# GET CREATOR OF PROJECT
				$userOwnerId = project_owner($projectId);

				# PROJECT LEVEL = 1 THEN JUST SAVE THE PERMISSIONS AS THE CREATOR HAVE.
				if (isset($project_level) && !empty($project_level) && $project_level == 1) {
					if (isset($userProjectId) && !empty($userProjectId)) {

						$user_permission_pid = 0;
						if ($this->user_id != $userOwnerId) {
							$user_permission_1 = $this->ProjectPermission->find('first', ['conditions' => ['ProjectPermission.user_id' => $this->user_id, 'ProjectPermission.user_project_id' => $userProjectId], 'recursive' => -1, 'fields' => ['ProjectPermission.id']]);
							$user_permission_pid = (isset($user_permission_1) && !empty($user_permission_1)) ? $user_permission_1['ProjectPermission']['id'] : 0;
						}

						$same_level_permit = [
							'user_id' => $shareUser,
							'share_by_id' => $this->user_id,
							'owner_id' => $userOwnerId,
							'user_project_id' => $userProjectId,
							'share_permission' => 1,
							'project_level' => 1,
							'parent_id' => $user_permission_pid,
							'permit_read' => 1,
							'permit_add' => 1,
							'permit_edit' => 1,
							'permit_delete' => 1,
						];

						if ($this->ProjectPermission->save($same_level_permit)) {

							$this->loadModel('ProjectBoard');

							$board = $this->ProjectBoard->find('first', array('conditions' => array('ProjectBoard.project_id' => $this->request->data['Share']['project_id'], 'ProjectBoard.sender' => $this->request->data['Share']['user_id'], 'ProjectBoard.receiver' => $this->user_id)));

							if (isset($board) && !empty($board)) {

								$this->request->data['ProjectBoard']['project_status'] = 1;
								$this->request->data['ProjectBoard']['id'] = $board['ProjectBoard']['id'];
								$this->ProjectBoard->save($this->request->data);
							}

							$same_level_insert_id = $this->ProjectPermission->getLastInsertId();
						}
					}

					// Share project email send to user
					$this->Common->share_project_email($shareUser, $projectId);

					//$this->Common->getProjectAllUser($projectId, $shareUser);
					// $this->redirect(array('controller' => 'projects', 'action' => 'lists'));
				} else {
					// GET ALL DIFFERENT TYPE OF PERMISSIONS DATA FROM POST
					$prjPermit = (isset($post['ProjectPermission'])) ? $post['ProjectPermission'] : null;

					$wsPermit = (isset($post['WorkspacePermission'])) ? $post['WorkspacePermission'] : null;

					$elPermit = (isset($post['ElementPermission'])) ? $post['ElementPermission'] : null;

					// GET ALL DIFFERENT TYPE OF PROPAGATE PERMISSIONS DATA FROM POST
					$prjPermitProp = (isset($post['ProjectPermission_prop'])) ? $post['ProjectPermission_prop'] : null;

					$wsPermitProp = (isset($post['WorkspacePermission_prop'])) ? $post['WorkspacePermission_prop'] : null;

					$elPermitProp = (isset($post['ElementPermission_prop'])) ? $post['ElementPermission_prop'] : null;

					$pshare_insert_id = 0;

					// *********************************** INSERT DATA INTO ProjectPermission TABLE
					if (!empty($prjPermit)) {

						# INSERT OWNER DETAIL INTO ProjectPermission AND THIS INSERTED ID WILL SET AS PARENT ID OF NEXT SAVED ROW
						$owner_insert_id = 1;
						if (isset($userProjectId) && !empty($userProjectId)) {
							$project_permissions = null;

							$user_permission_id = 0;
							if ($this->user_id != $userOwnerId) {
								$user_permission_1 = $this->ProjectPermission->find('first', ['conditions' => ['ProjectPermission.user_id' => $this->user_id, 'ProjectPermission.user_project_id' => project_upid($projectId)], 'recursive' => -1, 'fields' => ['ProjectPermission.id']]);
								$user_permission_id = (isset($user_permission_1) && !empty($user_permission_1)) ? $user_permission_1['ProjectPermission']['id'] : 0;
							}

							foreach ($prjPermit as $pid => $val) {
								$project_permissions = [
									'user_id' => $shareUser,
									'share_by_id' => $this->user_id,
									'owner_id' => $userOwnerId,
									'user_project_id' => $userProjectId,
									'parent_id' => $user_permission_id,
									'share_permission' => $propogationEnable,
									'project_level' => 0, # 0=down level, 1=same level
								];
								if (!empty($val) && is_array($val)) {
									$permissions = array_keys($val);

									foreach ($val as $pkey => $pval) {
										$project_permissions[$pkey] = (!empty($pval) && $pval > 0) ? 1 : 0;
									}
								}
							}
							if (isset($project_permissions) && !empty($project_permissions)) {
								if ($this->ProjectPermission->save($project_permissions)) {

									$this->loadModel('ProjectBoard');

									$board = $this->ProjectBoard->find('first', array('conditions' => array('ProjectBoard.project_id' => $this->request->data['Share']['project_id'], 'ProjectBoard.sender' => $this->request->data['Share']['user_id'], 'ProjectBoard.receiver' => $this->user_id)));

									if ((isset($board) && !empty($board)) && $board['ProjectBoard']['project_status'] != 1) {
										// die('mar gaya');
										$this->request->data['ProjectBoard']['project_status'] = 1;
										$this->request->data['ProjectBoard']['id'] = $board['ProjectBoard']['id'];

										$this->ProjectBoard->save($this->request->data);
										$this->Common->share_project_email($shareUser, $projectId);
										$this->Common->getProjectAllUser($projectId, $shareUser);
									}

									$pshare_insert_id = $this->ProjectPermission->getLastInsertId();

									# SAVE PROPAGATION PERMISSIONS IF SELECTED.
									if (isset($propogationEnable) && !empty($propogationEnable)) {
										if (isset($prjPermitProp) && !empty($prjPermitProp)) {
											$project_propagations = null;
											foreach ($prjPermitProp as $pid => $val) {
												$project_propagations = [
													'share_by_id' => $this->user_id,
													'share_for_id' => $shareUser,
													'user_project_id' => $userProjectId,
													'parent_id' => 0,
													'project_level' => 0,
													'share_permission' => $propogationEnable,
												];
												if (!empty($val) && is_array($val)) {
													foreach ($val as $pkey => $pval) {
														$project_propagations[$pkey] = (!empty($pval) && $pval > 0) ? 1 : 0;
													}
												}
											}
											if (isset($project_propagations) && !empty($project_propagations)) {
												if ($this->ProjectPropagate->save($project_propagations)) {

												}
											}
										}
									}
								}

							}
						}
					}

					// *********************************** END ProjectPermission Insertion

					// *********************************** INSERT DATA INTO WorkspacePermission TABLE
					if (!empty($wsPermit)) {

						$workspace_permissions = null;

						foreach ($wsPermit as $wsid => $val) {

							$wsPermitions = null;

							$wsPermitions = [
								'user_id' => $shareUser,
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

						$wshare_insert_id = null;
						if (!empty($workspace_permissions)) {
							if ($this->WorkspacePermission->saveAll($workspace_permissions)) {
								$wshare_insert_id = $this->WorkspacePermission->inserted_ids;
							}
						}

					}
					// *********************************** END WorkspacePermission Insertion

					// *********************************** INSERT DATA INTO ElementPermission TABLE
					if (!empty($elPermit)) {

						$element_permissions = null;
						// INSERT NEXT ROW ONLY IF THE PARENT IS CREATED SUCCESSFULLY

						foreach ($elPermit as $elid => $val) {

							$elPermitions = null;

							$elPermitions = [
								'user_id' => $shareUser,
								'project_id' => $projectId,
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
											$ws_read['user_id'] = $shareUser;

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

						$eshare_insert_id = null;
						if (!empty($element_permissions)) {
							if ($this->ElementPermission->saveAll($element_permissions)) {
								$eshare_insert_id = $this->ElementPermission->inserted_ids;
							}
						}
					}
					// *********************************** END ElementPermission Insertion

					// *********************************** Insert data into WorkspacePropagate
					if (isset($propogationEnable) && !empty($propogationEnable)) {
						if (!empty($wsPermitProp)) {

							$workspace_prop = null;
							// INSERT NEXT ROW ONLY IF THE PARENT IS CREATED SUCCESSFULLY

							foreach ($wsPermitProp as $wsid => $val) {

								$wsPropagation = null;

								$wsPropagation = [
									'share_by_id' => $this->user_id,
									'share_for_id' => $shareUser,
									'user_project_id' => $userProjectId,
								];

								# GET project_workspace_id FROM project_id and workspace_id VALUE
								$project_workspace_id = workspace_pwid($projectId, $wsid);

								if (!empty($project_workspace_id) && !empty($project_workspace_id)) {
									$wsPropagation['project_workspace_id'] = $project_workspace_id;

									if (!empty($val) && is_array($val)) {
										# CREATE MULTI-ARRAY TO INSERT MULTIPLE DATA AT ONCE
										foreach ($val as $wkey => $wval) {
											$wsPropagation[$wkey] = (!empty($wval) && $wval > 0) ? 1 : 0;
										}

									}

									$workspace_prop[]['WorkspacePropagate'] = $wsPropagation;
								}
							}

							$wprop_insert_id = null;
							if (!empty($workspace_prop)) {
								if ($this->WorkspacePropagate->saveAll($workspace_prop)) {
									$wprop_insert_id = $this->WorkspacePropagate->inserted_ids;
								}
							}
							$all_insert_id['wpro'] = $wprop_insert_id;
						}
					} // *********************************** END WorkspacePropagate Insertion

					// *********************************** INSERT DATA INTO ElementPropagate TABLE
					if (isset($propogationEnable) && !empty($propogationEnable)) {
						if (!empty($elPermitProp)) {

							$element_propagations = null;
							// INSERT NEXT ROW ONLY IF THE PARENT IS CREATED SUCCESSFULLY

							foreach ($elPermitProp as $elid => $val) {

								$elPropagate = null;

								$elPropagate = [
									'share_by_id' => $this->user_id,
									'share_for_id' => $shareUser,
									'project_id' => $projectId,
								];

								if (!empty($val) && is_array($val)) {

									foreach ($val as $ekey => $eval) {
										# GET project_id and workspace_id VALUES WITH EXTRACTING VALUE OF EACH
										$elDetail = explode('_', $eval);

										if (!empty($elDetail) && is_array($elDetail)) {

											list($wsID, $aID, $eID) = $elDetail;

											if (!empty($wsID) && !empty($aID) && !empty($eID)) {
												$elPropagate['workspace_id'] = $wsID;
												$elPropagate['element_id'] = $eID;
												$elPropagate[$ekey] = (!empty($eval) && $eval > 0) ? 1 : 0;
											}
										}
									}
								}

								$element_propagations[]['ElementPropagate'] = $elPropagate;

							}

							$eprop_insert_id = null;
							if (!empty($element_propagations)) {
								if ($this->ElementPropagate->saveAll($element_propagations)) {
									$eprop_insert_id = $this->ElementPropagate->inserted_ids;
								}
							}
							$all_insert_id['epro'] = $eprop_insert_id;
						}
					}

					/*if (empty($propogationEnable) && $project_level == 1) {
							$this->redirect(array('controller' => 'projects', 'action' => 'lists'));
						} else {
							$this->redirect(array('controller' => 'shares', 'action' => 'my_sharing'));
					*/
				}

				/************** socket messages (new project member) **************/
				if (SOCKET_MESSAGES) {
					$current_user_id = $this->user_id;
					App::import('Controller', 'Risks');
					$Risks = new RisksController;
					$project_all_users = $Risks->get_project_users($projectId, $current_user_id);
					if (isset($project_all_users) && !empty($project_all_users)) {
						if (($key = array_search($current_user_id, $project_all_users)) !== false) {
							unset($project_all_users[$key]);
						}
						if (($key = array_search($shareUser, $project_all_users)) !== false) {
							unset($project_all_users[$key]);
						}
					}
					$open_users = null;
					if (isset($project_all_users) && !empty($project_all_users)) {
						foreach ($project_all_users as $key1 => $value1) {
							if (web_notify_setting($value1, 'project', 'project_new_member')) {
								$open_users[] = $value1;
							}
						}
					}
					$heading = 'Sharer';
					$p_permission = $this->Common->project_permission_details($projectId, $shareUser);
					if (isset($p_permission) && !empty($p_permission)) {
						if ($p_permission['ProjectPermission']['project_level'] > 0) {
							$heading = 'Owner';
						}
					}
					$userDetail = get_user_data($current_user_id);
					$sharedUserDetail = get_user_data($shareUser);
					$content = [
						'socket' => [
							'notification' => [
								'type' => 'new_project_member',
								'created_id' => $current_user_id,
								'project_id' => $projectId,
								'creator_name' => $userDetail['UserDetail']['full_name'],
								'subject' => 'New project member',
								'heading' => 'Member: ' . $sharedUserDetail['UserDetail']['full_name'] . '<br />Permission: ' . $heading,
								'sub_heading' => 'Project: ' . strip_tags(getFieldDetail('Project', $projectId, 'title')),
								'date_time' => timezoneTimeConvertor($this->user_id, date('Y-m-d g:iA'), $format = 'd M Y g:iA') . user_country($this->user_id),
							],
							// 'received_users' => [$post['Share']['user_id']],
						],
					];
					if (is_array($open_users)) {
						$content['socket']['received_users'] = array_values($open_users);
					}

					$request = array(
						'header' => array(
							'Content-Type' => 'application/json',
						),
					);
					$content = json_encode($content['socket']);
					$HttpSocket = new HttpSocket([
						'ssl_verify_host' => false,
						'ssl_verify_peer_name' => false,
						'ssl_verify_peer' => false,
					]);

					$results = $HttpSocket->post(CHATURL . '/serveremit', $content, $request);
				}

				/************** socket messages (project sharing) **************/
				if (SOCKET_MESSAGES) {
					$current_user_id = $this->user_id;
					$heading = 'Sharer';
					$p_permission = $this->Common->project_permission_details($projectId, $shareUser);
					if (isset($p_permission) && !empty($p_permission)) {
						if ($p_permission['ProjectPermission']['project_level'] > 0) {
							$heading = 'Owner';
						}
					}
					$send_notification = false;
					if (web_notify_setting($shareUser, 'project', 'project_sharing')) {
						$send_notification = true;
					}
					$userDetail = get_user_data($current_user_id);
					$content = [
						'socket' => [
							'notification' => [
								'type' => 'project_sharing',
								'created_id' => $current_user_id,
								'project_id' => $projectId,
								'creator_name' => $userDetail['UserDetail']['full_name'],
								'subject' => 'Project sharing',
								'heading' => 'Permission: ' . $heading,
								'sub_heading' => 'Project: ' . strip_tags(getFieldDetail('Project', $projectId, 'title')),
								'date_time' => timezoneTimeConvertor($this->user_id, date('Y-m-d g:iA'), $format = 'd M Y g:iA') . user_country($this->user_id),
							],
						],
					];
					if ($send_notification) {
						$content['socket']['received_users'] = [$shareUser];
					}

					$request = array(
						'header' => array(
							'Content-Type' => 'application/json',
						),
					);
					$content = json_encode($content['socket']);
					$HttpSocket = new HttpSocket([
						'ssl_verify_host' => false,
						'ssl_verify_peer_name' => false,
						'ssl_verify_peer' => false,
					]);

					$results = $HttpSocket->post(CHATURL . '/serveremit', $content, $request);
				}
				/************** socket messages (project sharing) **************/

				if(isset($refer_redirect) && !empty($refer_redirect)) {
					$this->redirect($refer_redirect);
				}
				else{
					if (isset($project_level) && !empty($project_level) && $project_level == 1) {
						$this->redirect(array('controller' => 'projects', 'action' => 'lists'));
					} else {
						if (empty($propogationEnable) && $project_level == 1) {
							$this->redirect(array('controller' => 'projects', 'action' => 'lists'));
						} else {
							$this->redirect(array('controller' => 'shares', 'action' => 'my_sharing'));
						}
					}
				}
			}
		}
	}

	/*
		 * @name  	show_flash: Show message after ajax post
		 * @access	public
		 * @package  App/Controller/SharesController
	*/
	public function show_flash($type = 'success') {

		if ($this->request->isAjax()) {
			CakeSession::write('fmessage.type', $type);
			$this->render(DS . 'Elements' . DS . 'flash_message');
		}
	}

	/*
		 * @name  	propagate_sharing: Propagate Sharing
		 * @access	public
		 * @package  App/Controller/SharesController
	*/
	public function save_propagate_sharing_old($project_id = null, $share_action = 3) {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$this->autoRender = false;

			$response = [
				'success' => false,
				'msg' => null,
				'type' => null,
			];

			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;
				$sharePost = $post['Share'];

				$project_id = $sharePost['project_id'];
				$share_user_id = $sharePost['user_id'];
				$share_action = $sharePost['share_action'];
				$allow_propagation = $sharePost['propagating']; // 1 = further propagation is allowed

				if (isset($share_user_id) && !empty($share_user_id)) {
					// save permissions
					if ($this->save_sharing(['page_name' => 'propagate_sharing', 'project_id' => $project_id, 'share_user_id' => $share_user_id, 'share_action' => $share_action, 'allow_propagation' => $allow_propagation])) {
						$response['success'] = true;
						$response['type'] = 'success';
						$response['msg'] = 'Permissions saved successfully.';

					} else {
						$response['msg'] = 'Error in saving permissions data.';
						$response['type'] = 'error';
					}

					if (isset($allow_propagation) && !empty($allow_propagation)) {

						// save propagate
						if ($this->save_propagation(['project_id' => $project_id, 'share_user_id' => $share_user_id, 'share_by_id' => $this->user_id, 'share_for_id' => $share_user_id, 'share_action' => $share_action])) {
							$response['success'] = true;
							$response['type'] = 'success';
							$response['msg'] = 'Propagation saved successfully.';

						} else {
							$response['msg'] = 'Error in saving propagation data.';
							$response['type'] = 'error';
						}
					}
				} else {
					$response['type'] = 'info';
					$response['msg'] = 'User not selected.';
				}
				CakeSession::write('fmessage.message', $response['msg']);
			}
			echo json_encode($response);
			exit;
		}
	}

	public function save_propagate_sharing($project_id = null, $share_action = 3) {

		$response = [
			'success' => false,
			'msg' => null,
			'type' => null,
		];

		if ($this->request->is('post') || $this->request->is('put')) {

			$post = $this->request->data;
			$sharePost = $post['Share'];

			$project_id = $sharePost['project_id'];
			$share_user_id = $sharePost['user_id'];
			$share_action = $sharePost['share_action'];

			$allow_propagation = isset($sharePost['propagating']) ? $sharePost['propagating'] : 0; // 1 = further propagation is allowed

			if (isset($share_user_id) && !empty($share_user_id)) {
				// save permissions
				if ($this->save_sharing(['page_name' => 'propagate_sharing', 'project_id' => $project_id, 'share_user_id' => $share_user_id, 'share_action' => $share_action, 'allow_propagation' => $allow_propagation])) {
					$response['success'] = true;
					$response['type'] = 'success';
					$response['msg'] = 'Permissions saved successfully.';
				} else {
					$response['msg'] = 'Error in saving permissions data.';
					$response['type'] = 'error';
				}

				if (isset($allow_propagation) && !empty($allow_propagation)) {

					// save propagate
					if ($this->save_propagation(['project_id' => $project_id, 'share_user_id' => $share_user_id, 'share_by_id' => $this->user_id, 'share_for_id' => $share_user_id, 'share_action' => $share_action])) {
						$response['success'] = true;
						$response['type'] = 'success';
						$response['msg'] = 'Propagation saved successfully.';

					} else {
						$response['msg'] = 'Error in saving propagation data.';
						$response['type'] = 'error';
					}
				}
			} else {
				$response['type'] = 'info';
				$response['msg'] = 'User not selected.';
			}
			CakeSession::write('fmessage.message', $response['msg']);
		}
		return $response;

	}

	/*
		 * @name  	propagate_sharing: Propagate Sharing
		 * @access	public
		 * @package  App/Controller/SharesController
	*/
	public function propagate_sharing($project_id = null, $share_action = 3, $share_by_id = null) {

		$this->layout = 'inner';

		$this->set('title_for_layout', 'Project Propagation');
		$this->set('page_heading', 'Project Propagation');

		if ($this->request->is('post') || $this->request->is('put')) {

			if (isset($this->request->data['Share']['user_id']) && !empty($this->request->data['Share']['user_id'])) {

				$shareUser = $this->request->data['Share']['user_id'];

				$this->Common->share_project_email($shareUser, $project_id);
				$this->Common->getProjectAllUser($project_id, $shareUser);

				// Add user project connection to mongo db
				$this->Users->userConnections($shareUser, $project_id);

				$response = $this->save_propagate_sharing($project_id, 1);


				/************** socket messages **************/
				if (SOCKET_MESSAGES) {
					$p_permission = $this->Common->project_permission_details($project_id, $shareUser);
					$heading = 'Sharer';
					if ($p_permission['ProjectPermission']['project_level'] > 0) {
						$heading = 'Owner';
					}
					$send_notification = false;
					if (web_notify_setting($shareUser, 'project', 'project_sharing')) {
						$send_notification = true;
					}
					$userDetail = get_user_data($this->user_id);
					$content = [
						'notification' => [
							'type' => 'project_sharing',
							'created_id' => $this->user_id,
							'project_id' => $project_id,
							'creator_name' => $userDetail['UserDetail']['full_name'],
							'subject' => 'Project sharing',
							'heading' => 'Permission: ' . $heading,
							'sub_heading' => 'Project: ' . strip_tags(getFieldDetail('Project', $project_id, 'title')),
							'date_time' => timezoneTimeConvertor($this->user_id, date('Y-m-d g:iA'), $format = 'd M Y g:iA') . user_country($this->user_id),
						],
					];
					if ($send_notification) {
						$content['received_users'] = [$shareUser];
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
				if ($response['success']) {
					$this->redirect(array('controller' => 'shares', 'action' => 'my_sharing'));
				}
			} else {
				$this->redirect(array('controller' => 'shares', 'action' => 'propagate_sharing', $project_id));
			}

		}

		$this->setJsVar('project_id', $project_id);

		$project_detail = $this->Project->find('first', ['conditions' => ['Project.id' => $project_id], 'recursive' => -1]);
		$viewData['crumb'] = [
			'Received Projects' => [
				'data' => [
					'url' => '/projects/share_lists',
					'class' => 'tipText',
					'title' => 'Received Projects',
					'data-original-title' => 'Received Projects',
				],
			],
			'Summary' => [
				'data' => [
					'url' => '/projects/index/' . $project_id,
					'class' => 'tipText',
					'title' => $project_detail['Project']['title'],
					'data-original-title' => $project_detail['Project']['title'],
				],
			],
			'last' => [
				'data' => [
					'title' => 'Project Propagation',
					'data-original-title' => 'Project Propagation',
				],
			],
		];

		$viewData['users_list'] = get_selected_users($this->user_id, $project_id);
		$viewData['project_id'] = $project_id;
		$viewData['share_by_id'] = $share_by_id;
		$viewData['user_id'] = $this->user_id;
		$this->set($viewData);

	}

	/*
		 * @name  	propagate_users: Get Users for Propagate Sharing
		 * @access	public
		 * @package  App/Controller/SharesController
	*/
	public function propagate_users($project_id = null) {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$this->autoRender = false;

			$response = [
				'success' => false,
				'action' => null,
				'content' => null,
			];
			$users = get_selected_users($this->user_id, $project_id);
			$userData = null;
			if (isset($users) && !empty($users)) {
				foreach ($users as $key => $val) {
					$userData[] = ['id' => $key, 'label' => $val];
				}
			}
			echo json_encode($userData);
			exit;
			//
			// exit;
		}
	}

	/*
		 * @name  	my_permissions: Get User's given Permissions and Propagations
		 * @access	public
		 * @package  App/Controller/SharesController
	*/
	public function my_permissions($project_id = null) {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$this->autoRender = false;
			$this->set('user_id', $this->user_id);
			$this->set('project_id', $project_id);
			$response = [
				'success' => false,
				'action' => null,
				'content' => null,
			];
			$exist_permissions = null;
			$share_user_id = $this->user_id;
			if (!empty($project_id)) {

				$project_detail = $this->UserProject->find('first', ['conditions' => ['UserProject.id' => project_upid($project_id)], 'recursive' => 2]);
				$userProjectId = $project_detail['UserProject']['id'];
				$projectWorkspaceId = Set::extract($project_detail, '/Project/ProjectWorkspace/id');
				$workspaceId = Set::extract($project_detail, '/Project/ProjectWorkspace/workspace_id');

				$pp_data = $this->ProjectPermission->find('first', [
					'conditions' => [
						'ProjectPermission.user_id' => $share_user_id,
						'ProjectPermission.user_project_id' => project_upid($project_id),
					],
					'recursive' => -1,
				]);

				$pp_data_count = ( isset($pp_data) && !empty($pp_data) ) ? count($pp_data) : 0;
				if (!empty($pp_data_count)) {
					$exist_permissions['pp_data'] = $pp_data;
					$exist_permissions['pp_data_count'] = $pp_data_count;
				}

				// -------- Get project_workspace_id of the current project and find those in workspace_permissions for this user
				$wp_data = $this->WorkspacePermission->find('all', [
					'conditions' => [
						'WorkspacePermission.user_id' => $share_user_id,
						'WorkspacePermission.user_project_id' => project_upid($project_id),
						// 'WorkspacePermission.project_workspace_id' => workspace_pwid
					],
					'recursive' => -1,
				]);
				$wp_data_count = ( isset($wp_data) && !empty($wp_data) ) ? count($wp_data) : 0;
				if (!empty($wp_data_count)) {
					$exist_permissions['wp_data'] = $wp_data;
					$exist_permissions['wp_data_count'] = $wp_data_count;
				}

				$view = new View();
				$viewModal = $view->loadHelper('ViewModel');

				// -------- Get all area ids of all workspaces
				$ws_area = $viewModal->workspace_areas($workspaceId, false, true);
				// -------- Get all element ids of those
				$elm = $viewModal->area_elements($ws_area, true);

				// -------- Find all element ids in element_permissions
				$ep_data = $this->ElementPermission->find('all', [
					'conditions' => [
						'ElementPermission.user_id' => $share_user_id,
						'ElementPermission.project_id' => $project_id,
						'ElementPermission.workspace_id' => $workspaceId,
						'ElementPermission.element_id' => $elm,
					],
					'recursive' => -1,
				]);
				$ep_data_count = ( isset($ep_data) && !empty($ep_data) ) ? count($ep_data) : 0;
				if (!empty($ep_data_count)) {
					$exist_permissions['ep_data'] = $ep_data;
					$exist_permissions['ep_data_count'] = $ep_data_count;
				}

			}
			$this->set('exist_permissions', $exist_permissions);
			$this->render(DS . 'Shares' . DS . 'partials' . DS . 'my_permissions');
			// echo json_encode($userData);
			// exit;
			//
			// exit;
		}
	}

	public function my_element_permissions($project_id = null, $workspaceId = null, $element_id = null, $user_id = null) {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$this->autoRender = false;
			$this->set('user_id', $user_id);
			$this->set('project_id', $project_id);
			$response = [
				'success' => false,
				'action' => null,
				'content' => null,
			];
			$exist_permissions = null;
			$share_user_id = $user_id;
			if (!empty($project_id)) {

				$project_detail = $this->UserProject->find('first', ['conditions' => ['UserProject.id' => project_upid($project_id)], 'recursive' => 2]);
				$userProjectId = $project_detail['UserProject']['id'];
				$projectWorkspaceId = Set::extract($project_detail, '/Project/ProjectWorkspace/id');
				$workspaceId = Set::extract($project_detail, '/Project/ProjectWorkspace/workspace_id');

				$pp_data = $this->ProjectPermission->find('first', [
					'conditions' => [
						'ProjectPermission.user_id' => $share_user_id,
						'ProjectPermission.user_project_id' => project_upid($project_id),
					],
					'recursive' => -1,
				]);

				$pp_data_count = ( isset($pp_data) && !empty($pp_data) ) ? count($pp_data) : 0;
				if (!empty($pp_data_count)) {
					$exist_permissions['pp_data'] = $pp_data;
					$exist_permissions['pp_data_count'] = $pp_data_count;
				}

				// -------- Get project_workspace_id of the current project and find those in workspace_permissions for this user
				$wp_data = $this->WorkspacePermission->find('all', [
					'conditions' => [
						'WorkspacePermission.user_id' => $share_user_id,
						'WorkspacePermission.user_project_id' => project_upid($project_id),
						// 'WorkspacePermission.project_workspace_id' => workspace_pwid
					],
					'recursive' => -1,
				]);
				$wp_data_count = ( isset($wp_data) && !empty($wp_data) ) ? count($wp_data) : 0;
				if (!empty($wp_data_count)) {
					$exist_permissions['wp_data'] = $wp_data;
					$exist_permissions['wp_data_count'] = $wp_data_count;
				}

				$view = new View();
				$viewModal = $view->loadHelper('ViewModel');

				// -------- Get all area ids of all workspaces
				$ws_area = $viewModal->workspace_areas($workspaceId, false, true);
				// -------- Get all element ids of those
				$elm = $viewModal->area_elements($ws_area, true);

				// -------- Find all element ids in element_permissions
				$ep_data = $this->ElementPermission->find('all', [
					'conditions' => [
						'ElementPermission.user_id' => $share_user_id,
						'ElementPermission.project_id' => $project_id,
						'ElementPermission.workspace_id' => $workspaceId,
						'ElementPermission.element_id' => $elm,
					],
					'recursive' => -1,
				]);
				$ep_data_count = ( isset($ep_data) && !empty($ep_data) ) ? count($ep_data) : 0;
				if (!empty($ep_data_count)) {
					$exist_permissions['ep_data'] = $ep_data;
					$exist_permissions['ep_data_count'] = $ep_data_count;
				}

			}
			$this->set('exist_permissions', $exist_permissions);
			$this->render(DS . 'Shares' . DS . 'partials' . DS . 'my_element_permission');
			// echo json_encode($userData);
			// exit;
			//
			// exit;
		}
	}

	public function my_element_permissions_old($project_id = null, $workspaceId = null, $element_id = null, $user_id = null) {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$this->autoRender = false;
			$this->set('user_id', $user_id);
			$this->set('project_id', $project_id);
			$response = [
				'success' => false,
				'action' => null,
				'content' => null,
			];
			$exist_permissions = null;
			$share_user_id = $user_id;
			if (!empty($project_id)) {

				$view = new View();
				$viewModal = $view->loadHelper('ViewModel');

				// -------- Find all element ids in element_permissions
				$ep_data = $this->ElementPermission->find('all', [
					'conditions' => [
						'ElementPermission.user_id' => $share_user_id,
						'ElementPermission.project_id' => $project_id,
						'ElementPermission.workspace_id' => $workspaceId,
						'ElementPermission.element_id' => $element_id,
					],
					'recursive' => -1,
				]);

				$ep_data_count = ( isset($ep_data) && !empty($ep_data) ) ? count($ep_data) : 0;
				if (!empty($ep_data_count)) {
					$exist_permissions['ep_data'] = $ep_data;
					$exist_permissions['ep_data_count'] = $ep_data_count;
				}

			}
			$this->set('exist_permissions', $exist_permissions);
			$this->render(DS . 'Shares' . DS . 'partials' . DS . 'my_element_permission');
			// echo json_encode($userData);
			// exit;
			//
			// exit;
		}
	}

	/*
		 * @name  	update_sharing
		 * @access	public
		 * @package  App/Controller/SharesController
	*/

	public function update_sharing($project_id = null, $share_user_id = null, $share_action = 2, $ppermit_insert_id = 0) {

		$this->layout = 'inner';
		$this->set('page_heading', 'Update Sharing');
		$this->set('title_for_layout', 'Update Sharing');

		// INCASE PROJECT ID, SHARING USER AND SHARING ACTION IS NOT PRESENT IN URL OR ANYTHING WRONG
		if (!isset($project_id) || empty($project_id)) {
			$this->redirect(array('controller' => 'shares', 'action' => 'my_sharing'));
		}
		if (!isset($share_user_id) || empty($share_user_id)) {
			$this->redirect(array('controller' => 'shares', 'action' => 'my_sharing'));
		}
		if (!isset($share_action) || empty($share_action) || $share_action != 2) {
			$this->redirect(array('controller' => 'shares', 'action' => 'my_sharing'));
		}

		$ppermit_update_id = 0;
		if (isset($ppermit_insert_id) && !empty($ppermit_insert_id)) {
			$ppermit_update_id = $ppermit_insert_id;
		}
		$this->set('ppermit_update_id', $ppermit_update_id);

		if ($this->request->is('post') || $this->request->is('put')) {

			$post = $this->request->data;
			// pr($post, 1); // share_permission

			$userPrjId = project_upid($post['Share']['project_id']);

			/*************************************************************************/
			// CHECK PROPOGATION IF EXISTS BEFORE UPDATE
			// NOW PROPOGATION IS UNCHECKED THEN REMOVE ALL PROPOGATION FROM DB
			/*************************************************************************/
			$currentSharePermitCount = $this->ProjectPermission->find('count', [
				'conditions' => [
					'ProjectPermission.user_project_id' => $userPrjId,
					'ProjectPermission.user_id' => $post['Share']['user_id'],
					'ProjectPermission.owner_id' => $this->user_id,
					'ProjectPermission.share_permission' => 1,
				],
				'recursive' => -1,
			]);

			// IF PROPOGATION IS EXISTS AND SHARE PERMISSION IS OFF IN POST DATA THEN REMOVE PREVIOUS PROPOGATION DATA FROM DB
			if (isset($currentSharePermitCount) && !empty($currentSharePermitCount)) {

				if (!isset($this->request->data['Share']['share_permission']) || (isset($this->request->data['Share']['share_permission']) && empty($this->request->data['Share']['share_permission']))) {

					// REMOVE ALL PROPAGATION DATA FROM ALL 3 TABLES [PROJECT, WORKSPACE AND ELEMENT]
					if ($this->remove_propagations($post['Share']['project_id'], $post['Share']['user_id'])) {

						$currentSharePermit = $this->ProjectPermission->find('first', [
							'conditions' => [
								'ProjectPermission.user_project_id' => $userPrjId,
								'ProjectPermission.user_id' => $post['Share']['user_id'],
								'ProjectPermission.owner_id' => $this->user_id,
								'ProjectPermission.share_permission' => 1,
							],
							'fields' => ['ProjectPermission.id'],
							'recursive' => -1,
						]);

						if (isset($currentSharePermit) && !empty($currentSharePermit)) {
							$this->ProjectPermission->id = $currentSharePermit['ProjectPermission']['id'];
							//pr($post['Share'], 1);
							if ($this->ProjectPermission->saveField('share_permission', '0')) {
								$this->Common->share_project_email($post['Share']['user_id'], $post['Share']['project_id']);

								/************** socket messages **************/
								if (SOCKET_MESSAGES) {
									// $p_permission = $this->Common->project_permission_details($project_id, $shareUser);
									$heading = 'Sharer';
									/*if ($p_permission['ProjectPermission']['project_level'] > 0) {
										$heading = 'Owner';
									}*/
									$send_notification = false;
									if (web_notify_setting($post['Share']['user_id'], 'project', 'project_sharing')) {
										$send_notification = true;
									}
									$userDetail = get_user_data($this->user_id);
									$content = [
										'notification' => [
											'type' => 'project_sharing',
											'created_id' => $this->user_id,
											'project_id' => $post['Share']['project_id'],
											'creator_name' => $userDetail['UserDetail']['full_name'],
											'subject' => 'Project sharing',
											'heading' => 'Permission: ' . $heading,
											'sub_heading' => 'Project: ' . strip_tags(getFieldDetail('Project', $post['Share']['project_id'], 'title')),
											'date_time' => timezoneTimeConvertor($this->user_id, date('Y-m-d g:iA'), $format = 'd M Y g:iA') . user_country($this->user_id),
										],
									];
									if ($send_notification) {
										$content['received_users'] = [$post['Share']['user_id']];
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
							//die;
						}
					}
				}
			}

			// DELETE ALL SHARE PERMISSIONS OF THE CURRENT USER BEFORE ADD NEW

			// -------- Get project permissions of this user with selected project
			$currentPPermit = $this->ProjectPermission->find('all', [
				'conditions' => [
					'ProjectPermission.user_project_id' => $userPrjId,
					'ProjectPermission.user_id' => $post['Share']['user_id'],
					'ProjectPermission.owner_id' => $this->user_id,
				],
				'fields' => ['ProjectPermission.id'],
				'recursive' => -1,
			]);

			if (isset($currentPPermit) && empty($currentPPermit)) {
				$currentPPermit = $this->ProjectPermission->find('all', [
					'conditions' => [
						'ProjectPermission.user_project_id' => $userPrjId,
						'ProjectPermission.user_id' => $post['Share']['user_id'],
						'ProjectPermission.share_by_id' => $this->user_id,
					],
					'fields' => ['ProjectPermission.id'],
					'recursive' => -1,
				]);
			}

			// EXTRACTING PROJECT PERMISSIONS TABLE IDs
			$currentPPermit = Set::extract($currentPPermit, '/ProjectPermission/id');

			// -------- Get workspace permissions of this user with selected project
			$pw_data = get_project_workspace($project_id, true);

			if (isset($pw_data) && !empty($pw_data)) {

				$currentWPermit = $this->WorkspacePermission->find('all', [
					'conditions' => [
						'WorkspacePermission.user_project_id' => $userPrjId,
						'WorkspacePermission.user_id' => $post['Share']['user_id'],
						'WorkspacePermission.project_workspace_id' => array_values($pw_data),
					],
					'fields' => ['WorkspacePermission.id'],
					'recursive' => -1,
				]);

				// EXTRACTING WORKSPACE PERMISSIONS TABLE IDs
				$currentWPermit = Set::extract($currentWPermit, '/WorkspacePermission/id');

				// -------- Get element permissions of this user with selected project
				$currentEPermit = $this->ElementPermission->find('all', [
					'conditions' => [
						'ElementPermission.project_id' => $project_id,
						'ElementPermission.user_id' => $post['Share']['user_id'],
						'ElementPermission.workspace_id' => array_keys($pw_data),
						'ElementPermission.is_editable !=' => 1,
					],
					'fields' => ['ElementPermission.id'],
					'recursive' => -1,
				]);

				// EXTRACTING ELEMENT PERMISSIONS TABLE IDs
				$currentEPermit = Set::extract($currentEPermit, '/ElementPermission/id');

			}
			$response = ['pr_del' => false, 'ws_del' => false, 'el_del' => false];
			// DELETE ALL IDs
			$projectPermissionId = null;

			if (!empty($currentPPermit)) {
				$pdetailData['project_level'] = (isset($post['Share']['project_level']) && !empty($post['Share']['project_level'])) ? 1 : 0;
				$pdetailData['share_permission'] = (isset($post['Share']['share_permission']) && !empty($post['Share']['share_permission'])) ? 1 : 0;
				$pdetailData['permit_read'] = (isset($post['ProjectPermission'][$project_id]['permit_read']) && !empty($post['ProjectPermission']['permit_read'])) ? 1 : 0;
				$pdetailData['permit_edit'] = (isset($post['ProjectPermission'][$project_id]['permit_edit']) && !empty($post['ProjectPermission'][$project_id]['permit_edit'])) ? 1 : 0;
				$pdetailData['permit_delete'] = (isset($post['ProjectPermission'][$project_id]['permit_delete']) && !empty($post['ProjectPermission'][$project_id]['permit_delete'])) ? 1 : 0;
				$pdetailData['id'] = $currentPPermit[0];

				if ($this->ProjectPermission->save($pdetailData)) {
					$projectPermissionId = $currentPPermit[0];
				}

			}

			if (!empty($currentWPermit)) {
				if ($this->WorkspacePermission->delete($currentWPermit)) {
					$response['ws_del'] = true;
				}
			}


			if (!empty($currentEPermit)) {

				if( PROCEDURE_MODE == 1 ) {

					$deleteConditions = array('ShareElement.element_permission_id'=>$currentEPermit);
					if ($this->ShareElement->deleteAll($deleteConditions)) {
						$response['el_del'] = true;
					}

				}

				if ($this->ElementPermission->delete($currentEPermit)) {
					$response['el_del'] = true;
				}
			}



			if (isset($post) && !empty($post['Share']['project_level']) && $post['Share']['project_level'] == 1) {
				$this->Common->share_project_email($share_user_id, $project_id);

				/************** socket messages **************/
				if (SOCKET_MESSAGES) {
					// $p_permission = $this->Common->project_permission_details($project_id, $shareUser);
					$heading = 'Owner';
					/*if ($p_permission['ProjectPermission']['project_level'] > 0) {
										$heading = 'Owner';
									}*/
					$send_notification = false;
					if (web_notify_setting($share_user_id, 'project', 'project_sharing')) {
						$send_notification = true;
					}
					$userDetail = get_user_data($this->user_id);
					$content = [
						'notification' => [
							'type' => 'project_sharing',
							'created_id' => $this->user_id,
							'project_id' => $project_id,
							'creator_name' => $userDetail['UserDetail']['full_name'],
							'subject' => 'Project sharing',
							'heading' => 'Permission: ' . $heading,
							'sub_heading' => 'Project: ' . strip_tags(getFieldDetail('Project', $project_id, 'title')),
							'date_time' => timezoneTimeConvertor($this->user_id, date('Y-m-d g:iA'), $format = 'd M Y g:iA') . user_country($this->user_id),
						],
					];
					if ($send_notification) {
						$content['received_users'] = [$share_user_id];
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

			$this->save_sharing(['page_name' => 'update_sharing', 'project_id' => $project_id, 'share_user_id' => $share_user_id, 'share_action' => $share_action, 'projectPermissionId' => $projectPermissionId]);
			
			if( isset($post['ShareRefer']['refer']) && !empty($post['ShareRefer']['refer']) ){
				$this->redirect($post['ShareRefer']['refer']);
				exit;
			}
			

		}
		// End Post
		$share_user_id = (isset($share_user_id) && !empty($share_user_id)) ? $share_user_id : 0;
		$this->set('shareUser', $share_user_id);

		// if share option is for add new append 1 with the URL, otherwise append 2
		$share_action = (isset($share_action) && !empty($share_action)) ? $share_action : 1;
		$this->set('share_action', $share_action);

		$conditions['UserProject.project_id'] = $project_id;
		#----------- check sharing permissions -----------
		if (has_permissions($project_id)) {
			$conditions['UserProject.user_id'] = $this->user_id;
		}
		#----------- check sharing permissions -----------

		// -------- Project Detail of Passed id
		$project_detail = $this->UserProject->find('first', ['conditions' => $conditions, 'recursive' => 2]);
		$this->set('project_detail', $project_detail);

		// -------- Get project permissions of this user with selected project
		$prjectPermissions = $this->ProjectPermission->find('first', ['conditions' => ['ProjectPermission.user_project_id' => project_upid($project_id), 'ProjectPermission.user_id' => $share_user_id], 'recursive' => -1]);

		// 1 = same level, 2 = down level
		$exist_project_level = ((isset($prjectPermissions) && !empty($prjectPermissions)) && (isset($prjectPermissions['ProjectPermission']['project_level']) && !empty($prjectPermissions['ProjectPermission']['project_level']))) ? 1 : 2;

		// 1 = propagation on, 2 = propagation off
		$exist_share_permission = ((isset($prjectPermissions) && !empty($prjectPermissions)) && (isset($prjectPermissions['ProjectPermission']['share_permission']) && !empty($prjectPermissions['ProjectPermission']['share_permission']))) ? 1 : 2;

		// $exist_project_level = 1;
		// $exist_share_permission = 2;

		$this->set('exist_project_level', $exist_project_level);
		$this->set('exist_share_permission', $exist_share_permission);

		$this->setJsVar('project_level', $exist_project_level);
		$this->setJsVar('share_permission', $exist_share_permission);

		$this->set('prjectPermissions', $prjectPermissions);

		$this->set('project_id', $project_id);

		$pp_data = $wp_data = $ep_data = [];
		$exist_permissions = null;
		$pp_data_count = $wp_data_count = $ep_data_count = 0;

		if (isset($project_detail) && !empty($project_detail)) {

			$userProjectId = $project_detail['UserProject']['id'];
			$projectWorkspaceId = Set::extract($project_detail, '/Project/ProjectWorkspace/id');
			$workspaceId = Set::extract($project_detail, '/Project/ProjectWorkspace/workspace_id');

			// -------- Check this user for previous data exists or not
			if (!empty($share_user_id)) {

				$pp_data = $this->ProjectPermission->find('first', [
					'conditions' => [
						'ProjectPermission.user_id' => $share_user_id,
						'ProjectPermission.user_project_id' => $userProjectId,
					],
					'recursive' => -1,
				]);

				$pp_data_count =  ( isset($pp_data) && !empty($pp_data) ) ? count($pp_data) : 0;
				if (!empty($pp_data_count)) {
					$exist_permissions['pp_data'] = $pp_data;
					$exist_permissions['pp_data_count'] = $pp_data_count;
				}

				// -------- Get project_workspace_id of the current project and find those in workspace_permissions for this user
				$wp_data = $this->WorkspacePermission->find('all', [
					'conditions' => [
						'WorkspacePermission.user_id' => $share_user_id,
						'WorkspacePermission.user_project_id' => $userProjectId,
						'WorkspacePermission.project_workspace_id' => $projectWorkspaceId,
					],
					'recursive' => -1,
				]);
				$wp_data_count = ( isset($wp_data) && !empty($wp_data) ) ? count($wp_data) : 0;
				if (!empty($wp_data_count)) {
					$exist_permissions['wp_data'] = $wp_data;
					$exist_permissions['wp_data_count'] = $wp_data_count;
				}

				$view = new View();
				$viewModal = $view->loadHelper('ViewModel');

				// -------- Get all area ids of all workspaces
				$ws_area = $viewModal->workspace_areas($workspaceId, false, true);
				// -------- Get all element ids of those
				$elm = $viewModal->area_elements($ws_area, true);

				// -------- Find all element ids in element_permissions
				$ep_data = $this->ElementPermission->find('all', [
					'conditions' => [
						'ElementPermission.user_id' => $share_user_id,
						'ElementPermission.project_id' => $project_id,
						'ElementPermission.workspace_id' => $workspaceId,
						'ElementPermission.element_id' => $elm,
					],
					'recursive' => -1,
				]);


				$ep_data_count = ( isset($ep_data) && !empty($ep_data) ) ? count($ep_data) : 0;
				if (!empty($ep_data_count)) {
					$exist_permissions['ep_data'] = $ep_data;
					$exist_permissions['ep_data_count'] = $ep_data_count;
				}

			}
			$this->set('exist_permissions', $exist_permissions);
			// pr($exist_permissions, 1);
			// -------- CREATE BREADCRUMB
			// $cat_crumb = get_category_list($project_id);

			$project_title = _strip_tags($project_detail['Project']['title']);

			$crumb = [
				'Summary' => [
					'data' => [
						'url' => '/projects/index/' . $project_id,
						'class' => 'tipText',
						'title' => $project_detail['Project']['title'],
						'data-original-title' => $project_detail['Project']['title'],
					],
				],
				'last' => [
					'data' => [
						'title' => 'Sharing',
						'data-original-title' => 'Sharing',
					],
				],
			];
			/*if (isset($cat_crumb) && !empty($cat_crumb)) {
				$crumb = array_merge($cat_crumb, $crumb);
			}*/

			$this->set('crumb', $crumb);

			/****************************************/
			$update_permissions = $swayam_ki_permissions = false;
			if (!empty($project_id) && !empty($share_user_id)) {

				$dataBB = $this->Common->project_permission_details($project_id, $this->user_id);

				$UP = $this->Common->userproject($project_id, $this->user_id);

				if ((isset($UP) && !empty($UP)) || (isset($dataBB) && $dataBB['ProjectPermission']['project_level'] > 0)) {
					$swayam_ki_permissions = true;
				}

			}

			$this->set('swayam_ki_permissions', $swayam_ki_permissions);
		}

	}

	public function save_sharing($options = null, $post = null) {

		$project_id = $share_user_id = 0;
		$share_action = 1;
		$page_name = '';

		if (isset($options) && !empty($options)) {

			$project_id = (isset($options['project_id']) && !empty($options['project_id'])) ? $options['project_id'] : 0;

			$share_user_id = (isset($options['share_user_id']) && !empty($options['share_user_id'])) ? $options['share_user_id'] : 0;

			$share_action = (isset($options['share_action']) && !empty($options['share_action'])) ? $options['share_action'] : 1;

			$projectPermissionId = (isset($options['projectPermissionId']) && !empty($options['projectPermissionId'])) ? $options['projectPermissionId'] : null;

			$page_name = (isset($options['page_name']) && !empty($options['page_name'])) ? $options['page_name'] : '';

		}


		if ($this->request->is('post') || $this->request->is('put')) {

			$permit_propagate_insert_ids = null;


			$refer_redirect = (isset($this->request->data['ShareRefer']['refer']) && !empty($this->request->data['ShareRefer']['refer'])) ? $this->request->data['ShareRefer']['refer'] : '';

			// SHARE ACTIONS:
			// 1: CREATE SHARING
			// 2: UPDATE SHARING
			// 3: THE USER IS PROPAGATING THE SHARE PERMISSIONS TO FURTHER DOWN LEVEL USER
			if (isset($share_action) && !empty($share_action) && $share_action == 3) {

				// IF SHARING ACTION IS 3 THEN COPY ALL THE SHARE AND PROPOGATE PERMISSIONS AND ADD TO DATABASE WITH THE CURRENT USER ID TO WHOM THIS IS PROPAGATING
				if ((isset($this->request->data['Share']['user_id']) && !empty($this->request->data['Share']['user_id'])) && (isset($this->request->data['Share']['project_id']) && !empty($this->request->data['Share']['project_id']))) {

					// Get this users project permissions
					$proj_permit = $this->ProjectPermission->find('first', ['conditions' => ['ProjectPermission.user_id' => $this->user_id, 'ProjectPermission.user_project_id' => project_upid($project_id), 'ProjectPermission.share_permission' => 1, 'ProjectPermission.project_level' => 0], 'recursive' => -1]);

					// get this user's propagate permissions and set them to next user's sharing A/C
					// Because permissions that the owner permit to this user, only those permissions can be propagate to next user

					$progtin = $this->get_propagations($this->user_id, $project_id);
					// pr($progtin, 1);
					if (isset($progtin['project']) && !empty($progtin['project'])) {

						if (isset($progtin['project']['ProjectPropagate']['permit_read']) && !empty($progtin['project']['ProjectPropagate']['permit_read'])) {

							if (isset($proj_permit) && !empty($proj_permit)) {
								// Next Propagation is Only Allowed if User Set It;

								$proj_permition = $proj_permit['ProjectPermission'];

								// Permissions that owner gives this user to spread to another user
								$proj_prop_permition = null;
								$pp_flag = false;
								if (isset($progtin['project']) && !empty($progtin['project'])) {
									$progtin_project = $progtin['project'];
									if (isset($progtin_project['ProjectPropagate']) && !empty($progtin_project['ProjectPropagate'])) {
										$proj_prop_permition = $progtin_project['ProjectPropagate'];
										$pp_flag = true;
									}
								}

								$permit_propagate_data = [
									'user_id' => $share_user_id,
									'share_by_id' => $this->user_id,
									'owner_id' => $proj_permition['owner_id'],
									'user_project_id' => project_upid($project_id),
									'project_level' => 0,
									'share_permission' => (isset($options['allow_propagation']) && !empty($options['allow_propagation'])) ? $options['allow_propagation'] : 0,
									'parent_id' => $proj_permition['id'],
									// permissions from project propagations of this current user
									'permit_read' => ($pp_flag) ? $proj_prop_permition['permit_read'] : 0,
									'permit_add' => ($pp_flag) ? $proj_prop_permition['permit_add'] : 0,
									'permit_edit' => ($pp_flag) ? $proj_prop_permition['permit_edit'] : 0,
									'permit_delete' => ($pp_flag) ? $proj_prop_permition['permit_delete'] : 0,
								];

								$this->ProjectPermission->id = null;
								if ($this->ProjectPermission->save($permit_propagate_data)) {
									$permit_propagate_insert_ids['p'] = $this->ProjectPermission->inserted_ids;
								}
							}

							// Get workspace permissions of this user
							$wrk_permit = $this->WorkspacePermission->find('all', ['conditions' => ['WorkspacePermission.user_id' => $this->user_id, 'WorkspacePermission.user_project_id' => project_upid($project_id)], 'recursive' => -1]);

							$permit_ws_propagate_insert_id = null;
							$newWrkPermit = $prjWrkId = null;
							if (isset($wrk_permit) && !empty($wrk_permit)) {

								$prjWrkId = Set::extract($wrk_permit, '/WorkspacePermission/project_workspace_id');

								// Permissions that owner gives this user to spread to another user
								$wsp_prop = null;

								if (isset($progtin['workspace']) && !empty($progtin['workspace'])) {
									$i = 0;
									foreach ($progtin['workspace'] as $key => $val) {
										$wsp_prop = $val['WorkspacePropagate'];

										if (isset($wsp_prop['permit_read']) && $wsp_prop['permit_read'] > 0) {
											// Requird Fields
											$newWrkPermit[$i]['WorkspacePermission']['id'] = null;
											$newWrkPermit[$i]['WorkspacePermission']['user_id'] = $share_user_id;
											$newWrkPermit[$i]['WorkspacePermission']['user_project_id'] = $wsp_prop['user_project_id'];
											$newWrkPermit[$i]['WorkspacePermission']['project_workspace_id'] = $wsp_prop['project_workspace_id'];

											// permissions from project propagations of this current user
											$newWrkPermit[$i]['WorkspacePermission']['permit_read'] = $wsp_prop['permit_read'];
											$newWrkPermit[$i]['WorkspacePermission']['permit_add'] = $wsp_prop['permit_add'];
											$newWrkPermit[$i]['WorkspacePermission']['permit_edit'] = $wsp_prop['permit_edit'];
											$newWrkPermit[$i]['WorkspacePermission']['permit_delete'] = $wsp_prop['permit_delete'];

											$i++;
										}
									}

									if (isset($newWrkPermit) && !empty($newWrkPermit)) {
										if ($this->WorkspacePermission->saveAll($newWrkPermit)) {
											$permit_propagate_insert_ids['w'][] = $this->WorkspacePermission->inserted_ids;
										}
									}
								}
							}

							// Get element permissions of this user
							// Check any workspace permissions exists
							if (isset($prjWrkId) && !empty($prjWrkId)) {
								$wsids = pwid_workspace($prjWrkId, $project_id);
								$elm_permit = $this->ElementPermission->find('all', ['conditions' => ['ElementPermission.user_id' => $this->user_id, 'ElementPermission.project_id' => $project_id, 'ElementPermission.workspace_id' => $wsids], 'recursive' => -1]);

								$newElmPerm = null;

								if (isset($elm_permit) && !empty($elm_permit)) {

									if (isset($progtin['element']) && !empty($progtin['element'])) {
										$i = 0;
										foreach ($progtin['element'] as $key => $val) {
											$elm_prop = $val['ElementPropagate'];
											if (isset($elm_prop['permit_read']) && $elm_prop['permit_read'] > 0) {
												// Requird settings
												$newElmPerm[$i]['ElementPermission']['id'] = null;
												$newElmPerm[$i]['ElementPermission']['user_id'] = $share_user_id;
												$newElmPerm[$i]['ElementPermission']['element_id'] = $elm_prop['element_id'];
												$newElmPerm[$i]['ElementPermission']['workspace_id'] = $elm_prop['workspace_id'];
												$newElmPerm[$i]['ElementPermission']['project_id'] = $elm_prop['project_id'];

												// permissions from project propagations of this current user
												$newElmPerm[$i]['ElementPermission']['permit_read'] = $elm_prop['permit_read'];
												$newElmPerm[$i]['ElementPermission']['permit_add'] = $elm_prop['permit_add'];
												$newElmPerm[$i]['ElementPermission']['permit_edit'] = $elm_prop['permit_edit'];
												$newElmPerm[$i]['ElementPermission']['permit_delete'] = $elm_prop['permit_delete'];
												$newElmPerm[$i]['ElementPermission']['permit_copy'] = $elm_prop['permit_copy'];
												$newElmPerm[$i]['ElementPermission']['permit_move'] = $elm_prop['permit_move'];
												$i++;
											}
										}



										if (isset($newElmPerm) && !empty($newElmPerm)) {
											if ($this->ElementPermission->saveAll($newElmPerm)) {
												$permit_propagate_insert_ids['e'][] = $this->ElementPermission->inserted_ids;
											}
										}
									}
								}
							}
						}
					}

					return true;

				}
			} else {

				if ((isset($this->request->data['Share']['user_id']) && !empty($this->request->data['Share']['user_id'])) && (isset($this->request->data['Share']['project_id']) && !empty($this->request->data['Share']['project_id']))) {

					$propogationEnable = (isset($this->request->data['Share']['share_permission'])) ? $this->request->data['Share']['share_permission'] : 0;

					$shareUser = $this->request->data['Share']['user_id'];

					$projectId = $this->request->data['Share']['project_id'];

					$sharingLevel = $this->request->data['Share']['project_level'];

					# GET USER-PROJECT ID OF SUPPLIED PROJECT
					$userProjectId = project_upid($projectId);
					$userOwnerId = project_owner($projectId);

					// FIRST OF ALL CHECK THE LEVEL OF SHARING, IF IT IS UP/SAME LEVEL THEN GIVE FULL PERMISSIONS TO THAT USER, OTHERWISE SET THE PERMISSIONS ACCORDING TO THE POSTED DATA
					// *** SHARING LEVEL 1: FULL PERMISSIONS
					// *** SHARING LEVEL 2: PERMISSIONS ACCORDING POSTED DATA
					if (isset($sharingLevel) && !empty($sharingLevel) && $sharingLevel == 1) {
						if (isset($userProjectId) && !empty($userProjectId)) {

							$user_permission_pid = 0;
							if ($this->user_id != $userOwnerId) {
								$user_permission_1 = $this->ProjectPermission->find('first', ['conditions' => ['ProjectPermission.user_id' => $this->user_id, 'ProjectPermission.user_project_id' => project_upid($project_id)], 'recursive' => -1, 'fields' => ['ProjectPermission.id']]);
								$user_permission_pid = (isset($user_permission_1) && !empty($user_permission_1)) ? $user_permission_1['ProjectPermission']['id'] : 0;
							}

							$same_level_permit = [
								'user_id' => $shareUser,
								'share_by_id' => $this->user_id,
								'owner_id' => $userOwnerId,
								'user_project_id' => $userProjectId,
								'share_permission' => 1,
								'project_level' => 1,
								'parent_id' => $user_permission_pid,
								'permit_read' => 1,
								'permit_add' => 1,
								'permit_edit' => 1,
								'permit_delete' => 1,
							];
							if ($this->ProjectPermission->save($same_level_permit)) {

								$this->loadModel('ProjectBoard');

								$board = $this->ProjectBoard->find('first', array('conditions' => array('ProjectBoard.project_id' => $this->request->data['Share']['project_id'], 'ProjectBoard.sender' => $this->request->data['Share']['user_id'], 'ProjectBoard.receiver' => $this->user_id)));

								if (isset($board) && !empty($board)) {

									$this->request->data['ProjectBoard']['project_status'] = 1;
									$this->request->data['ProjectBoard']['id'] = $board['ProjectBoard']['id'];
									$this->ProjectBoard->save($this->request->data);
								}

								$same_level_insert_id = $this->ProjectPermission->getLastInsertId();
							}
						}
						if ($share_action == 1) {

							// Share project email send to user
							$this->Common->share_project_email($shareUser, $projectId);

							/************** socket messages (new project member) **************/
							if (SOCKET_MESSAGES) {
								$current_user_id = $this->user_id;
								App::import('Controller', 'Risks');
								$Risks = new RisksController;
								$project_all_users = $Risks->get_project_users($projectId, $current_user_id);
								if (isset($project_all_users) && !empty($project_all_users)) {
									if (($key = array_search($current_user_id, $project_all_users)) !== false) {
										unset($project_all_users[$key]);
									}
									if (($key = array_search($shareUser, $project_all_users)) !== false) {
										unset($project_all_users[$key]);
									}
								}
								$open_users = null;
								if (isset($project_all_users) && !empty($project_all_users)) {
									foreach ($project_all_users as $key1 => $value1) {
										if (web_notify_setting($value1, 'project', 'project_new_member')) {
											$open_users[] = $value1;
										}
									}
								}
								$heading = 'Sharer';
								$p_permission = $this->Common->project_permission_details($projectId, $shareUser);
								if (isset($p_permission) && !empty($p_permission)) {
									if ($p_permission['ProjectPermission']['project_level'] > 0) {
										$heading = 'Owner';
									}
								}
								$userDetail = get_user_data($current_user_id);
								$sharedUserDetail = get_user_data($shareUser);
								$content = [
									'socket' => [
										'notification' => [
											'type' => 'new_project_member',
											'created_id' => $current_user_id,
											'project_id' => $projectId,
											'creator_name' => $userDetail['UserDetail']['full_name'],
											'subject' => 'New project member',
											'heading' => 'Member: ' . $sharedUserDetail['UserDetail']['full_name'] . '<br />Permission: ' . $heading,
											'sub_heading' => 'Project: ' . strip_tags(getFieldDetail('Project', $projectId, 'title')),
											'date_time' => timezoneTimeConvertor($this->user_id, date('Y-m-d g:iA'), $format = 'd M Y g:iA') . user_country($this->user_id),
										],
										// 'received_users' => [$post['Share']['user_id']],
									],
								];
								if (is_array($open_users)) {
									$content['socket']['received_users'] = array_values($open_users);
								}

								$request = array(
									'header' => array(
										'Content-Type' => 'application/json',
									),
								);
								$content = json_encode($content['socket']);
								$HttpSocket = new HttpSocket([
									'ssl_verify_host' => false,
									'ssl_verify_peer_name' => false,
									'ssl_verify_peer' => false,
								]);

								$results = $HttpSocket->post(CHATURL . '/serveremit', $content, $request);
							}

							/************** socket messages (new project member) **************/

							/************** socket messages (project sharing) **************/
							if (SOCKET_MESSAGES) {
								$current_user_id = $this->user_id;
								$heading = 'Sharer';
								$p_permission = $this->Common->project_permission_details($projectId, $shareUser);
								if (isset($p_permission) && !empty($p_permission)) {
									if ($p_permission['ProjectPermission']['project_level'] > 0) {
										$heading = 'Owner';
									}
								}
								$send_notification = false;
								if (web_notify_setting($shareUser, 'project', 'project_sharing')) {
									$send_notification = true;
								}
								$userDetail = get_user_data($current_user_id);
								$content = [
									'socket' => [
										'notification' => [
											'type' => 'project_sharing',
											'created_id' => $current_user_id,
											'project_id' => $projectId,
											'creator_name' => $userDetail['UserDetail']['full_name'],
											'subject' => 'Project sharing',
											'heading' => 'Permission: ' . $heading,
											'sub_heading' => 'Project: ' . strip_tags(getFieldDetail('Project', $projectId, 'title')),
											'date_time' => timezoneTimeConvertor($this->user_id, date('Y-m-d g:iA'), $format = 'd M Y g:iA') . user_country($this->user_id),
										],
									],
								];
								if ($send_notification) {
									$content['socket']['received_users'] = [$shareUser];
								}

								$request = array(
									'header' => array(
										'Content-Type' => 'application/json',
									),
								);
								$content = json_encode($content['socket']);
								$HttpSocket = new HttpSocket([
									'ssl_verify_host' => false,
									'ssl_verify_peer_name' => false,
									'ssl_verify_peer' => false,
								]);

								$results = $HttpSocket->post(CHATURL . '/serveremit', $content, $request);
							}
							/************** socket messages (project sharing) **************/

							//$this->Common->getProjectAllUser($projectId, $shareUser);
							if(isset($refer_redirect) && !empty($refer_redirect)){
								$this->redirect($refer_redirect);
							}
							else {
								$this->redirect(array('controller' => 'projects', 'action' => 'lists'));
							}
						} else if ($share_action == 2) {
							if(isset($refer_redirect) && !empty($refer_redirect)){
								$this->redirect($refer_redirect);
							}
							else {
								$this->redirect(array('controller' => 'shares', 'action' => 'my_sharing'));
							}

						}

					} else {

						// GET ALL DIFFERENT TYPE OF PERMISSIONS DATA FROM POST
						$prjPermit = (isset($this->request->data['ProjectPermission'])) ? $this->request->data['ProjectPermission'] : null;

						$wsPermit = (isset($this->request->data['WorkspacePermission'])) ? $this->request->data['WorkspacePermission'] : null;

						$elPermit = (isset($this->request->data['ElementPermission'])) ? $this->request->data['ElementPermission'] : null;

						$pshare_insert_id = 0;

						// *********************************** INSERT DATA INTO ProjectPermission TABLE
						if (!empty($prjPermit)) {

							# INSERT OWNER DETAIL INTO ProjectPermission AND THIS INSERTED ID WILL SET AS PARENT ID OF NEXT SAVED ROW
							$owner_insert_id = 1;
							if (isset($userProjectId) && !empty($userProjectId)) {
								$project_permissions = null;

								$user_permission_id = 0;
								if ($this->user_id != $userOwnerId) {
									$user_permission_1 = $this->ProjectPermission->find('first', ['conditions' => ['ProjectPermission.user_id' => $this->user_id, 'ProjectPermission.user_project_id' => project_upid($project_id)], 'recursive' => -1, 'fields' => ['ProjectPermission.id']]);
									$user_permission_id = (isset($user_permission_1) && !empty($user_permission_1)) ? $user_permission_1['ProjectPermission']['id'] : 0;
								}

								foreach ($prjPermit as $pid => $val) {
									// $permissions = is_array($val) ? array_keys($val) : $val;
									$project_permissions = [
										'user_id' => $shareUser,
										'share_by_id' => $this->user_id,
										'owner_id' => $userOwnerId,
										'user_project_id' => $userProjectId,
										// 'parent_id'	=> $owner_insert_id,
										'parent_id' => $user_permission_id,
										'share_permission'	=> $propogationEnable,
										// 'share_permission' => 0,
										'project_level' => 0, # 0=down level, 1=same level
									];
									if (!empty($val) && is_array($val)) {
										$permissions = array_keys($val);

										foreach ($val as $pkey => $pval) {
											$project_permissions[$pkey] = (!empty($pval) && $pval > 0) ? 1 : 0;
										}
									}
								}

								if (!empty($project_permissions)) {

									$this->ProjectPermission->id = (isset($projectPermissionId) && empty($projectPermissionId)) ? null : $projectPermissionId;

									if ($this->ProjectPermission->save($project_permissions)) {

										$this->loadModel('ProjectBoard');

										$board = $this->ProjectBoard->find('first', array('conditions' => array('ProjectBoard.project_id' => $this->request->data['Share']['project_id'], 'ProjectBoard.sender' => $this->request->data['Share']['user_id'], 'ProjectBoard.receiver' => $this->user_id)));

										if ((isset($board) && !empty($board)) && $board['ProjectBoard']['project_status'] != 1) {
											// die('mar gaya');
											$this->request->data['ProjectBoard']['project_status'] = 1;
											$this->request->data['ProjectBoard']['id'] = $board['ProjectBoard']['id'];

											$this->ProjectBoard->save($this->request->data);
											$this->Common->share_project_email($shareUser, $projectId);
											$this->Common->getProjectAllUser($projectId, $shareUser);
										}

										$pshare_insert_id = (isset($projectPermissionId) && !empty($projectPermissionId)) ? $projectPermissionId : $this->ProjectPermission->getLastInsertId();
									}
								}
							}
						}

						// *********************************** END ProjectPermission Insertion

						// *********************************** INSERT DATA INTO WorkspacePermission TABLE
						if (!empty($wsPermit)) {

							$workspace_permissions = null;

							foreach ($wsPermit as $wsid => $val) {

								$wsPermitions = null;

								$wsPermitions = [
									'user_id' => $shareUser,
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

							$wshare_insert_id = null;
							if (!empty($workspace_permissions)) {
								if ($this->WorkspacePermission->saveAll($workspace_permissions)) {
									$wshare_insert_id = $this->WorkspacePermission->inserted_ids;
								}
							}

						}
						// *********************************** END WorkspacePermission Insertion

						// *********************************** INSERT DATA INTO ElementPermission TABLE
						if (!empty($elPermit)) {

							$element_permissions = null;
							// INSERT NEXT ROW ONLY IF THE PARENT IS CREATED SUCCESSFULLY

							foreach ($elPermit as $elid => $val) {

								$elPermitions = null;

								$elPermitions = [
									'user_id' => $shareUser,
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
												$ws_read['user_id'] = $shareUser;

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

							$eshare_insert_id = null;
							if (!empty($element_permissions)) {
								if ($this->ElementPermission->saveAll($element_permissions)) {
									$eshare_insert_id = $this->ElementPermission->inserted_ids;

								}
							}
						}
						// *********************************** END ElementPermission Insertion
					}
					// Email functionality
					if ($share_action == 1) {
						// Share project email send to user
						$this->Common->share_project_email($shareUser, $projectId);

						/************** socket messages (new project member) **************/
						if (SOCKET_MESSAGES) {
							$current_user_id = $this->user_id;
							App::import('Controller', 'Risks');
							$Risks = new RisksController;
							$project_all_users = $Risks->get_project_users($projectId, $current_user_id);
							if (isset($project_all_users) && !empty($project_all_users)) {
								if (($key = array_search($current_user_id, $project_all_users)) !== false) {
									unset($project_all_users[$key]);
								}
								if (($key = array_search($shareUser, $project_all_users)) !== false) {
									unset($project_all_users[$key]);
								}
							}
							$open_users = null;
							if (isset($project_all_users) && !empty($project_all_users)) {
								foreach ($project_all_users as $key1 => $value1) {
									if (web_notify_setting($value1, 'project', 'project_new_member')) {
										$open_users[] = $value1;
									}
								}
							}
							$heading = 'Sharer';
							$p_permission = $this->Common->project_permission_details($projectId, $shareUser);
							if (isset($p_permission) && !empty($p_permission)) {
								if ($p_permission['ProjectPermission']['project_level'] > 0) {
									$heading = 'Owner';
								}
							}
							$userDetail = get_user_data($current_user_id);
							$sharedUserDetail = get_user_data($shareUser);
							$content = [
								'socket' => [
									'notification' => [
										'type' => 'new_project_member',
										'created_id' => $current_user_id,
										'project_id' => $projectId,
										'creator_name' => $userDetail['UserDetail']['full_name'],
										'subject' => 'New project member',
										'heading' => 'Member: ' . $sharedUserDetail['UserDetail']['full_name'] . '<br />Permission: ' . $heading,
										'sub_heading' => 'Project: ' . strip_tags(getFieldDetail('Project', $projectId, 'title')),
										'date_time' => timezoneTimeConvertor($this->user_id, date('Y-m-d g:iA'), $format = 'd M Y g:iA') . user_country($this->user_id),
									],
									// 'received_users' => [$post['Share']['user_id']],
								],
							];
							if (is_array($open_users)) {
								$content['socket']['received_users'] = array_values($open_users);
							}

							$request = array(
								'header' => array(
									'Content-Type' => 'application/json',
								),
							);
							$content = json_encode($content['socket']);
							$HttpSocket = new HttpSocket([
								'ssl_verify_host' => false,
								'ssl_verify_peer_name' => false,
								'ssl_verify_peer' => false,
							]);

							$results = $HttpSocket->post(CHATURL . '/serveremit', $content, $request);
						}

						/************** socket messages (new project member) **************/

						/************** socket messages (project sharing) **************/
						if (SOCKET_MESSAGES) {
							$current_user_id = $this->user_id;
							$heading = 'Sharer';
							$p_permission = $this->Common->project_permission_details($projectId, $shareUser);
							if (isset($p_permission) && !empty($p_permission)) {
								if ($p_permission['ProjectPermission']['project_level'] > 0) {
									$heading = 'Owner';
								}
							}
							$send_notification = false;
							if (web_notify_setting($shareUser, 'project', 'project_sharing')) {
								$send_notification = true;
							}
							$userDetail = get_user_data($current_user_id);
							$content = [
								'socket' => [
									'notification' => [
										'type' => 'project_sharing',
										'created_id' => $current_user_id,
										'project_id' => $projectId,
										'creator_name' => $userDetail['UserDetail']['full_name'],
										'subject' => 'Project sharing',
										'heading' => 'Permission: ' . $heading,
										'sub_heading' => 'Project: ' . strip_tags(getFieldDetail('Project', $projectId, 'title')),
										'date_time' => timezoneTimeConvertor($this->user_id, date('Y-m-d g:iA'), $format = 'd M Y g:iA') . user_country($this->user_id),
									],
								],
							];
							if ($send_notification) {
								$content['socket']['received_users'] = [$shareUser];
							}

							$request = array(
								'header' => array(
									'Content-Type' => 'application/json',
								),
							);
							$content = json_encode($content['socket']);
							$HttpSocket = new HttpSocket([
								'ssl_verify_host' => false,
								'ssl_verify_peer_name' => false,
								'ssl_verify_peer' => false,
							]);

							$results = $HttpSocket->post(CHATURL . '/serveremit', $content, $request);
						}
						/************** socket messages (project sharing) **************/
						//$this->Common->getProjectAllUser($projectId, $shareUser);
					}


					if(isset($refer_redirect) && !empty($refer_redirect)){
						$this->redirect($refer_redirect);
					}

					// REDIRECT TO THE SPECIFIC PAGE
					if ($propogationEnable > 0) {
						// redirect to propogation page
						if ($share_action == 1) {
							$this->redirect(array('controller' => 'shares', 'action' => 'propagation', $project_id, $share_user_id, $share_action, $pshare_insert_id));
						} else if ($share_action == 2) {

							//$this->redirect(array('controller' => 'shares', 'action' => 'update_propagation', $project_id, $share_user_id, $share_action, $pshare_insert_id));
						}

					} else if (empty($propogationEnable) && $share_action == 1) {
						$this->redirect(array('controller' => 'projects', 'action' => 'lists'));
					} else {
						//$this->redirect(array('controller' => 'shares', 'action' => 'my_sharing'));
					}

				}

			} // END SHARE ACTION
		}
	}

	public function save_propagation($options = null, $post = null) {

		$project_id = $share_user_id = $projectPermissionId = 0;
		$share_action = 1;
		if (isset($options) && !empty($options)) {
			$project_id = (isset($options['project_id']) && !empty($options['project_id'])) ? $options['project_id'] : 0;

			$share_user_id = (isset($options['share_user_id']) && !empty($options['share_user_id'])) ? $options['share_user_id'] : 0;

			$share_action = (isset($options['share_action']) && !empty($options['share_action'])) ? $options['share_action'] : 1;

			$projectPermissionId = (isset($options['projectPermissionId']) && !empty($options['projectPermissionId'])) ? $options['projectPermissionId'] : null;
		}

		# GET USER PROJECT ID OF SUPPLIED PROJECT
		$userProjectId = project_upid($project_id);

		if ($this->request->is('post') || $this->request->is('put')) {

			$progtin = null;

			// SHARE ACTIONS:
			// 1: CREATE SHARING
			// 2: UPDATE SHARING
			// 3: THE USER IS PROPAGATING THE PROPOGATE PERMISSIONS TO ANOTHER USER
			if (isset($share_action) && !empty($share_action) && $share_action == 3) {

				$prop_insert_id = null;

				$share_by_id = (isset($options['share_by_id']) && !empty($options['share_by_id'])) ? $options['share_by_id'] : null;

				$share_for_id = (isset($options['share_for_id']) && !empty($options['share_for_id'])) ? $options['share_for_id'] : null;

				$progtin = $this->get_propagations($share_by_id, $project_id);

				if (isset($progtin) && !empty($progtin)) {
					if (isset($progtin['project']) && !empty($progtin['project'])) {
						$p_progtin = $progtin['project']['ProjectPropagate'];

						$propagate_data = [
							'project_permission_id' => $projectPermissionId,
							'share_by_id' => $share_by_id,
							'share_for_id' => $share_for_id,
							'user_project_id' => project_upid($project_id),
							'project_level' => $p_progtin['project_level'],
							'share_permission' => $p_progtin['share_permission'],
							'parent_id' => $p_progtin['id'],
							'permit_read' => $p_progtin['permit_read'],
							'permit_add' => $p_progtin['permit_add'],
							'permit_edit' => $p_progtin['permit_edit'],
							'permit_delete' => $p_progtin['permit_delete'],
							'permit_copy' => $p_progtin['permit_copy'],
							'permit_move' => $p_progtin['permit_move'],
						];

						$this->ProjectPropagate->id = null;
						if ($this->ProjectPropagate->save($propagate_data)) {
							$prop_insert_id['project'] = $this->ProjectPropagate->inserted_ids;
						}
					}

					// Set workspace propagating
					$newWrkPermit = $prjWrkId = null;
					if (isset($progtin['workspace']) && !empty($progtin['workspace'])) {
						$newWrkPermit = $progtin['workspace'];
						$prjWrkId = Set::extract($progtin['workspace'], '/WorkspacePropagate/project_workspace_id');
						foreach ($progtin['workspace'] as $key => $val) {
							$newWrkPermit[$key]['WorkspacePropagate']['id'] = null;
							$newWrkPermit[$key]['WorkspacePropagate']['share_by_id'] = $share_by_id;
							$newWrkPermit[$key]['WorkspacePropagate']['share_for_id'] = $share_for_id;
						}
						//
						if ($this->WorkspacePropagate->saveAll($newWrkPermit)) {
							$prop_insert_id['workspace'][] = $this->WorkspacePropagate->inserted_ids;
						}
					}

					// Set element propagation
					if (isset($prjWrkId) && !empty($prjWrkId)) {

						$elm_permit = $this->ElementPropagate->find('all', ['conditions' => ['ElementPropagate.share_for_id' => $share_by_id, 'ElementPropagate.project_id' => $project_id, 'ElementPropagate.workspace_id' => pwid_workspace($prjWrkId, $project_id)], 'recursive' => -1]);

						$newElmPerm = null;

						if (isset($elm_permit) && !empty($elm_permit)) {
							$newElmPerm = $elm_permit;
							foreach ($elm_permit as $key => $val) {
								$newElmPerm[$key]['ElementPropagate']['id'] = null;
								$newElmPerm[$key]['ElementPropagate']['share_by_id'] = $share_by_id;
								$newElmPerm[$key]['ElementPropagate']['share_for_id'] = $share_for_id;
							}

							if ($this->ElementPropagate->saveAll($newElmPerm)) {
								$prop_insert_id['element'][] = $this->ElementPropagate->inserted_ids;
							}
						}
					}
				}

				if (!empty($prop_insert_id['project']) || !empty($prop_insert_id['workspace']) || !empty($prop_insert_id['element'])) {
					return true;
				} else {
					return false;
				}

			} else {
				if ((isset($this->request->data['Share']['share_for_id']) && !empty($this->request->data['Share']['share_for_id'])) && (isset($this->request->data['Share']['project_id']) && !empty($this->request->data['Share']['project_id']))) {

					$share_by_id = $this->request->data['Share']['share_by_id']; // owner

					$share_for_id = $this->request->data['Share']['share_for_id']; // share with

					$projectId = $this->request->data['Share']['project_id']; // shared project

					// SET THE PROPOGATE PERMISSIONS ACCORDING TO THE POSTED DATA
					// *** PROJECT LEVEL 0: FULL PERMISSIONS
					$all_insert_id = null;
					$prjProp = (isset($this->request->data['ProjectPropagate'])) ? $this->request->data['ProjectPropagate'] : null;
					$wsProp = (isset($this->request->data['WorkspacePropagate'])) ? $this->request->data['WorkspacePropagate'] : null;
					$elProp = (isset($this->request->data['ElementPropagate'])) ? $this->request->data['ElementPropagate'] : null;

					// *********************************** INSERT DATA INTO ProjectPropagate TABLE
					$project_prop = null;
					if (!empty($prjProp)) {

						# INSERT OWNER DETAIL INTO ProjectPropagate AND THIS INSERTED ID WILL SET AS PARENT ID OF NEXT SAVED ROW
						$owner_insert_id = null;

						$prjPermitExists = false;

						# INSERT NEXT ROW ONLY IF THE PARENT IS CREATED SUCCESSFULLY
						foreach ($prjProp as $pid => $val) {
							$project_prop = [
								'share_by_id' => $share_by_id,
								'share_for_id' => $share_for_id,
								'user_project_id' => $userProjectId,
								'parent_id' => 0,
								'project_level' => 0,
								'share_permission' => 0,
							];
							if (!empty($val) && is_array($val)) {

								if (count(preg_grep('/^permit_*/', array_keys($val))) > 0) {
									$prjPermitExists = true;
								} else {
									$prjPermitExists = false;
								}
								foreach ($val as $pkey => $pval) {
									$project_prop[$pkey] = (!empty($pval) && $pval > 0) ? 1 : 0;
								}
							}
						}

						if (!empty($project_prop) && $prjPermitExists == true) {
							$this->ProjectPropagate->id = null;
							if ($this->ProjectPropagate->save($project_prop)) {
								$pshare_insert_id = $this->ProjectPropagate->getLastInsertId();
								$all_insert_id['ppro'] = [$pshare_insert_id];

								$this->ProjectPermission->id = $projectPermissionId;
								if ($this->ProjectPermission->saveField('share_permission', 1)) {
									// e('done');
								}

							}
						}
					}

					// *********************************** END ProjectPropagate Insertion

					// *********************************** INSERT DATA INTO WorkspacePermission TABLE
					if (!empty($wsProp)) {

						$workspace_prop = null;
						// INSERT NEXT ROW ONLY IF THE PARENT IS CREATED SUCCESSFULLY

						foreach ($wsProp as $wsid => $val) {

							$wsPropagation = null;

							$wsPropagation = [
								'share_by_id' => $share_by_id,
								'share_for_id' => $share_for_id,
								'user_project_id' => $userProjectId,
							];

							# GET project_workspace_id FROM project_id and workspace_id VALUE
							$project_workspace_id = workspace_pwid($projectId, $wsid);

							if (!empty($project_workspace_id) && !empty($project_workspace_id)) {
								$wsPropagation['project_workspace_id'] = $project_workspace_id;

								if (!empty($val) && is_array($val)) {
									# CREATE MULTI-ARRAY TO INSERT MULTIPLE DATA AT ONCE
									foreach ($val as $wkey => $wval) {
										$wsPropagation[$wkey] = (!empty($wval) && $wval > 0) ? 1 : 0;
									}

								}

								$workspace_prop[]['WorkspacePropagate'] = $wsPropagation;
							}
						}

						$wprop_insert_id = null;
						if (!empty($workspace_prop)) {
							if ($this->WorkspacePropagate->saveAll($workspace_prop)) {
								$wprop_insert_id = $this->WorkspacePropagate->inserted_ids;
							}
						}
						$all_insert_id['wpro'] = $wprop_insert_id;

					}

					// *********************************** END WorkspacePropagate Insertion

					// *********************************** INSERT DATA INTO ElementPropagate TABLE
					if (!empty($elProp)) {

						$element_propagations = null;
						// INSERT NEXT ROW ONLY IF THE PARENT IS CREATED SUCCESSFULLY

						foreach ($elProp as $elid => $val) {

							$elPropagate = null;

							$elPropagate = [
								'share_by_id' => $share_by_id,
								'share_for_id' => $share_for_id,
								'project_id' => $project_id,
							];

							if (!empty($val) && is_array($val)) {

								foreach ($val as $ekey => $eval) {
									# GET project_id and workspace_id VALUES WITH EXTRACTING VALUE OF EACH
									$elDetail = explode('_', $eval);

									if (!empty($elDetail) && is_array($elDetail)) {

										list($wsID, $aID, $eID) = $elDetail;

										if (!empty($wsID) && !empty($aID) && !empty($eID)) {
											$elPropagate['workspace_id'] = $wsID;
											$elPropagate['element_id'] = $eID;
											$elPropagate[$ekey] = (!empty($eval) && $eval > 0) ? 1 : 0;
										}
									}
								}
							}

							$element_propagations[]['ElementPropagate'] = $elPropagate;

						}

						$eprop_insert_id = null;
						if (!empty($element_propagations)) {
							if ($this->ElementPropagate->saveAll($element_propagations)) {
								$eprop_insert_id = $this->ElementPropagate->inserted_ids;
							}
						}
						$all_insert_id['epro'] = $eprop_insert_id;
					}

					// *********************************** END ElementPropagate

					// $share_action = 1: Add
					// $share_action = 2: Update
					if ($share_action == '1') {
						// Share project email send to user
						$this->Common->share_project_email($share_user_id, $project_id);
						$this->Common->getProjectAllUser($project_id, $share_user_id);

						/************** socket messages (new project member) **************/
						if (SOCKET_MESSAGES) {
							$current_user_id = $this->user_id;
							App::import('Controller', 'Risks');
							$Risks = new RisksController;
							$project_all_users = $Risks->get_project_users($project_id, $current_user_id);
							if (isset($project_all_users) && !empty($project_all_users)) {
								if (($key = array_search($current_user_id, $project_all_users)) !== false) {
									unset($project_all_users[$key]);
								}
								if (($key = array_search($share_user_id, $project_all_users)) !== false) {
									unset($project_all_users[$key]);
								}
							}
							$open_users = null;
							if (isset($project_all_users) && !empty($project_all_users)) {
								foreach ($project_all_users as $key1 => $value1) {
									if (web_notify_setting($value1, 'project', 'project_new_member')) {
										$open_users[] = $value1;
									}
								}
							}
							$heading = 'Sharer';
							$p_permission = $this->Common->project_permission_details($project_id, $share_user_id);
							if (isset($p_permission) && !empty($p_permission)) {
								if ($p_permission['ProjectPermission']['project_level'] > 0) {
									$heading = 'Owner';
								}
							}
							$userDetail = get_user_data($current_user_id);
							$sharedUserDetail = get_user_data($share_user_id);
							$content = [
								'socket' => [
									'notification' => [
										'type' => 'new_project_member',
										'created_id' => $current_user_id,
										'project_id' => $project_id,
										'creator_name' => $userDetail['UserDetail']['full_name'],
										'subject' => 'New project member',
										'heading' => 'Member: ' . $sharedUserDetail['UserDetail']['full_name'] . '<br />Permission: ' . $heading,
										'sub_heading' => 'Project: ' . strip_tags(getFieldDetail('Project', $project_id, 'title')),
										'date_time' => timezoneTimeConvertor($this->user_id, date('Y-m-d g:iA'), $format = 'd M Y g:iA') . user_country($this->user_id),
									],
									// 'received_users' => [$post['Share']['user_id']],
								],
							];
							if (is_array($open_users)) {
								$content['socket']['received_users'] = array_values($open_users);
							}

							$request = array(
								'header' => array(
									'Content-Type' => 'application/json',
								),
							);
							$content = json_encode($content['socket']);
							$HttpSocket = new HttpSocket([
								'ssl_verify_host' => false,
								'ssl_verify_peer_name' => false,
								'ssl_verify_peer' => false,
							]);

							$results = $HttpSocket->post(CHATURL . '/serveremit', $content, $request);
						}

						/************** socket messages (new project member) **************/

						/************** socket messages (project sharing) **************/
						if (SOCKET_MESSAGES) {
							$current_user_id = $this->user_id;
							$heading = 'Sharer';
							$p_permission = $this->Common->project_permission_details($project_id, $share_user_id);
							if (isset($p_permission) && !empty($p_permission)) {
								if ($p_permission['ProjectPermission']['project_level'] > 0) {
									$heading = 'Owner';
								}
							}
							$send_notification = false;
							if (web_notify_setting($share_user_id, 'project', 'project_sharing')) {
								$send_notification = true;
							}
							$userDetail = get_user_data($current_user_id);
							$content = [
								'socket' => [
									'notification' => [
										'type' => 'project_sharing',
										'created_id' => $current_user_id,
										'project_id' => $project_id,
										'creator_name' => $userDetail['UserDetail']['full_name'],
										'subject' => 'Project sharing',
										'heading' => 'Permission: ' . $heading,
										'sub_heading' => 'Project: ' . strip_tags(getFieldDetail('Project', $project_id, 'title')),
										'date_time' => timezoneTimeConvertor($this->user_id, date('Y-m-d g:iA'), $format = 'd M Y g:iA') . user_country($this->user_id),
									],
								],
							];
							if ($send_notification) {
								$content['socket']['received_users'] = [$share_user_id];
							}

							$request = array(
								'header' => array(
									'Content-Type' => 'application/json',
								),
							);
							$content = json_encode($content['socket']);
							$HttpSocket = new HttpSocket([
								'ssl_verify_host' => false,
								'ssl_verify_peer_name' => false,
								'ssl_verify_peer' => false,
							]);

							$results = $HttpSocket->post(CHATURL . '/serveremit', $content, $request);
						}
						/************** socket messages (project sharing) **************/

					}

					if (!empty($share_action)) {
						$this->redirect(array('controller' => 'shares', 'action' => 'my_sharing', '#' => 'project_view'));
					} else {
						$this->redirect(array('controller' => 'projects', 'action' => 'lists'));
					}

				} // END CHECK SHARING USER AND PROJECT ID
			} // END CHECK SHARING USER AND PROJECT ID
		}

	}

	public function update_propagation($project_id = null, $share_user_id = null, $share_action = 2, $ppermit_insert_id = 0) {

		$this->layout = 'inner';

		$this->set('title_for_layout', 'Project Sharing - Update Propagations');
		$this->set('page_heading', 'Update Propagations');

		// INCASE PROJECT ID, SHARING USER AND SHARING ACTION IS NOT PRESENT IN URL OR ANYTHING WRONG
		if (!isset($project_id) || empty($project_id)) {
			$this->redirect(array('controller' => 'shares', 'action' => 'my_sharing'));
		}
		if (!isset($share_user_id) || empty($share_user_id)) {
			$this->redirect(array('controller' => 'shares', 'action' => 'my_sharing'));
		}
		if (!isset($share_action) || empty($share_action) || $share_action != 2) {
			$this->redirect(array('controller' => 'shares', 'action' => 'my_sharing'));
		}

		$this->set('ppermit_insert_id', $ppermit_insert_id);

		// INCASE PROJECT ID NOT PRESENT IN THE URL
		if (!isset($project_id) || empty($project_id)) {
			$this->redirect(array('controller' => 'shares', 'action' => 'my_sharing'));
		}

		# GET USER PROJECT ID OF SUPPLIED PROJECT
		$userProjectId = project_upid($project_id);
		$this->set('project_id', $project_id);

		$share_user_id = (isset($share_user_id) && !empty($share_user_id)) ? $share_user_id : 0;

		$this->set('shareUser', $share_user_id);

		if ($this->request->is('post') || $this->request->is('put')) {

			/*****************************************/
			$userPrjId = project_upid($this->request->data['Share']['project_id']);
			$post = $this->request->data;

			// DELETE ALL SHARE PERMISSIONS OF THE CURRENT USER BEFORE ADD NEW
			if ($this->remove_propagations($post['Share']['project_id'], $share_user_id)) {

				$this->save_propagation(['project_id' => $project_id, 'share_user_id' => $share_user_id, 'share_action' => $share_action, 'projectPermissionId' => $ppermit_insert_id]);

			}

		} // END CHECK POST

		// if share option is for add new append 1 with the URL, otherwise append 2
		$share_action = (isset($share_action) && !empty($share_action)) ? $share_action : 1;
		$this->set('share_action', $share_action);

		$conditions['UserProject.project_id'] = $project_id;
		#----------- check sharing permissions -----------
		if (has_permissions($project_id)) {
			$conditions['UserProject.user_id'] = $this->user_id;
		}
		#----------- check sharing permissions -----------

		// Project Detail of Passed id
		$project_detail = $this->UserProject->find('first', ['conditions' => $conditions, 'recursive' => 2]);
		$this->set('project_detail', $project_detail);

		// Check this user for permissions data exists or not

		$pp_data = $wp_data = $ep_data = [];
		$exist_permissions = null;
		$pp_data_count = $wp_data_count = $ep_data_count = 0;

		if (isset($userProjectId) && !empty($userProjectId)) {

			$projectWorkspaceId = Set::extract($project_detail, '/Project/ProjectWorkspace/id');
			$workspaceId = Set::extract($project_detail, '/Project/ProjectWorkspace/workspace_id');

			// Check this user for previous data exists or not
			if (!empty($share_user_id)) {

				$pp_data = $this->ProjectPermission->find('first', [
					'conditions' => [
						'ProjectPermission.user_id' => $share_user_id,
						'ProjectPermission.user_project_id' => $userProjectId,
					],
					'recursive' => -1,
				]);

				$pp_data_count = ( isset($pp_data) && !empty($pp_data) ) ? count($pp_data) : 0;
				if (!empty($pp_data_count)) {
					$exist_permissions['pp_data'] = $pp_data;
					$exist_permissions['pp_data_count'] = $pp_data_count;
				}

				// Get project_workspace_id of the current project and find those in workspace_permissions for this user
				$wp_data = $this->WorkspacePermission->find('all', [
					'conditions' => [
						'WorkspacePermission.user_id' => $share_user_id,
						'WorkspacePermission.user_project_id' => $userProjectId,
						'WorkspacePermission.project_workspace_id' => $projectWorkspaceId,
					],
					'recursive' => -1,
				]);

				$wp_data_count = ( isset($wp_data) && !empty($wp_data) ) ? count($wp_data) : 0;
				if (!empty($wp_data_count)) {
					$exist_permissions['wp_data'] = $wp_data;
					$exist_permissions['wp_data_count'] = $wp_data_count;
				}

				$view = new View();
				$viewModal = $view->loadHelper('ViewModel');

				// Get all area ids of all workspaces
				$ws_area = $viewModal->workspace_areas($workspaceId, false, true);
				// Get all element ids of those
				$elm = $viewModal->area_elements($ws_area, true);

				// Find all element ids in element_permissions
				$ep_data = $this->ElementPermission->find('all', [
					'conditions' => [
						'ElementPermission.user_id' => $share_user_id,
						'ElementPermission.project_id' => $project_id,
						'ElementPermission.workspace_id' => $workspaceId,
						'ElementPermission.element_id' => $elm,
					],
					'recursive' => -1,
				]);
				$ep_data_count = ( isset($ep_data) && !empty($ep_data) ) ? count($ep_data) : 0;
				if (!empty($ep_data_count)) {
					$exist_permissions['ep_data'] = $ep_data;
					$exist_permissions['ep_data_count'] = $ep_data_count;
				}

			}
		}
		$this->set('exist_permissions', $exist_permissions);

		if (isset($project_detail) && !empty($project_detail)) {

			// Get Project Workspace and Elements in a Tree
			// $share_data = getProjectWorkspaces($project_id, true);

			// $this->set('share_data', $share_data);

			// $cat_crumb = get_category_list($project_id);

			$project_title = _strip_tags($project_detail['Project']['title']);

			$crumb = [
				'Summary' => [
					'data' => [
						'url' => '/projects/index/' . $project_id,
						'class' => 'tipText',
						'title' => $project_detail['Project']['title'],
						'data-original-title' => $project_detail['Project']['title'],
					],
				],
				'Sharing' => [
					'data' => [
						'url' => '/shares/my_sharing',
						'class' => 'tipText',
						'title' => "My Shared Projects",
						'data-original-title' => "My Shared Projects",
					],
				],
				'last' => [
					'data' => [
						'title' => 'Sharing: Propagation',
						'data-original-title' => 'Sharing: Propagation',
					],
				],
			];
			/*if (isset($cat_crumb) && !empty($cat_crumb)) {
				$crumb = array_merge($cat_crumb, $crumb);
			}*/

			$this->set('crumb', $crumb);
		}

	}

	public function remove_propagations($project_id = null, $share_user_id = null, $share_action = 2) {
		# GET USER PROJECT ID OF SUPPLIED PROJECT
		$userProjectId = project_upid($project_id);

		$share_user_id = (isset($share_user_id) && !empty($share_user_id)) ? $share_user_id : 0;

		/*****************************************/
		$userPrjId = project_upid($project_id);

		// DELETE ALL SHARE PERMISSIONS OF THE CURRENT USER BEFORE ADD NEW
		// -------- Get project permissions of this user with selected project
		$currentPPermit = $this->ProjectPropagate->find('all', [
			'conditions' => [
				'ProjectPropagate.user_project_id' => $userPrjId,
				'ProjectPropagate.share_for_id' => $share_user_id,
				'ProjectPropagate.share_by_id' => $this->user_id,
			],
			'fields' => ['ProjectPropagate.id'],
			'recursive' => -1,
		]);

		// EXTRACTING PROJECT PERMISSIONS TABLE IDs
		$currentPPermit = Set::extract($currentPPermit, '/ProjectPropagate/id');

		// -------- Get workspace permissions of this user with selected project
		$pw_data = get_project_workspace($project_id, true);

		if (isset($pw_data) && !empty($pw_data)) {
			$currentWPermit = $this->WorkspacePropagate->find('all', [
				'conditions' => [
					'WorkspacePropagate.user_project_id' => $userPrjId,
					'WorkspacePropagate.share_for_id' => $share_user_id,
					'WorkspacePropagate.project_workspace_id' => array_values($pw_data),
				],
				'fields' => ['WorkspacePropagate.id'],
				'recursive' => -1,
			]);
			// EXTRACTING WORKSPACE PERMISSIONS TABLE IDs
			$currentWPermit = Set::extract($currentWPermit, '/WorkspacePropagate/id');

			// -------- Get element permissions of this user with selected project
			$currentEPermit = $this->ElementPropagate->find('all', [
				'conditions' => [
					'ElementPropagate.project_id' => $project_id,
					'ElementPropagate.share_for_id' => $share_user_id,
					'ElementPropagate.workspace_id' => array_keys($pw_data),
				],
				'fields' => ['ElementPropagate.id'],
				'recursive' => -1,
			]);
			// EXTRACTING ELEMENT PERMISSIONS TABLE IDs
			$currentEPermit = Set::extract($currentEPermit, '/ElementPropagate/id');

			$response = ['pr_del' => false, 'ws_del' => false, 'el_del' => false];
			// DELETE ALL IDs



			if (!empty($currentPPermit)) {
				if ($this->ProjectPropagate->delete($currentPPermit)) {
					$response['pr_del'] = true;
				}
			}else{
				$response['pr_del'] = true;
			}

			if (!empty($currentWPermit)) {
				if ($this->WorkspacePropagate->delete($currentWPermit)) {
					$response['ws_del'] = true;
				}
			}else{
				$response['ws_del'] = true;
			}

			if (!empty($currentEPermit)) {
				if ($this->ElementPropagate->delete($currentEPermit)) {
					$response['el_del'] = true;
				}
			}else{
				$response['el_del'] = true;
			}

			// CHECK STATUS OF ALL DBDATA DELETED OR NOT
			// IF ANYTHING GONE WRONG; RETURN FALSE
			$returnVal = true;
			if (!empty($response)) {
				foreach ($response as $key => $val) {
					if ($val == false) {
						$returnVal = false;
					}

				}
			}




			// IF RETURN VALUE IS FALSE; THEN CHECK THAT IN DATABASE FOR PROPAGATION IS ACTULLY EXISTS THERE OR NOT; IF NOT AGAIN SET TRUE TO RETURN VALUE
			if (empty($currentPPermit) && empty($currentWPermit) && empty($currentWPermit)) {
				$returnVal = true;
			}

// pr($currentPPermit);
// pr($currentWPermit);
// pr($currentEPermit);
// pr($returnVal);

//  die;


			return $returnVal;

		}
		return false;
	}

	public function delete_shared_propagation($project_id = null, $share_user_id = null) {

		# GET USER PROJECT ID OF SUPPLIED PROJECT
		$userProjectId = project_upid($project_id);

		$share_user_id = (isset($share_user_id) && !empty($share_user_id)) ? $share_user_id : 0;

		/*****************************************/
		$userPrjId = project_upid($project_id);
		$this->layout = 'ajax';

		$this->autoRender = false;

		$response = [
			'success' => false,
			'action' => null,
			'content' => null,
		];
		$response['content'] = ['prp_del' => false, 'wsp_del' => false, 'elp_del' => false, 'pr_del' => false, 'ws_del' => false, 'el_del' => false];
		if ($this->request->isAjax()) {
			if ($this->request->is('post') || $this->request->is('put')) {

				$currentProp = $this->get_permissions($share_user_id, $project_id, true);

				if (isset($currentProp) && !empty($currentProp)) {

					// Add user project connection to mongo db
					$this->Users->userConnections($share_user_id, $project_id, true);

					if (isset($currentProp['project']) && !empty($currentProp['project'])) {
						$ppids = Set::extract($currentProp['project'], '/ProjectPermission/id');
						if (!empty($ppids)) {
							if ($this->ProjectPermission->deleteAll(array('ProjectPermission.id' => $ppids))) {
								$response['content']['prp_del'] = true;
							}

						}
					}
					if (isset($currentProp['workspace']) && !empty($currentProp['workspace'])) {
						$wpids = Set::extract($currentProp['workspace'], '/WorkspacePermission/id');
						if (!empty($wpids)) {
							if ($this->WorkspacePermission->deleteAll(array('WorkspacePermission.id' => $wpids))) {
								$response['content']['wsp_del'] = true;
							}

						}
					}
					if (isset($currentProp['element']) && !empty($currentProp['element'])) {
						$epids = Set::extract($currentProp['element'], '/ElementPermission/id');
						if (!empty($epids)) {
							if ($this->ElementPermission->deleteAll(array('ElementPermission.id' => $epids))) {

								if( PROCEDURE_MODE == 1 ) {
									$delCond = array('ShareElement.element_permission_id'=>$epids);
									$this->ShareElement->deleteAll($delCond);
								}

								$response['content']['elp_del'] = true;
							}

						}
					}
				}

				// DELETE ALL SHARE PERMISSIONS OF THE CURRENT USER BEFORE ADD NEW
				// -------- Get project permissions of this user with selected project
				$currentPPermit = $this->ProjectPropagate->find('all', [
					'conditions' => [
						'ProjectPropagate.user_project_id' => $userPrjId,
						'ProjectPropagate.share_for_id' => $share_user_id,
						'ProjectPropagate.share_by_id' => $this->user_id,
					],
					'fields' => ['ProjectPropagate.id'],
					'recursive' => -1,
				]);

				// EXTRACTING PROJECT PERMISSIONS TABLE IDs
				$currentPPermit = Set::extract($currentPPermit, '/ProjectPropagate/id');
				$currentEPermit = [];

				// -------- Get workspace permissions of this user with selected project
				$pw_data = get_project_workspace($project_id, true);
				if(isset($pw_data) && !empty($pw_data)) {
					$currentWPermit = $this->WorkspacePropagate->find('all', [
						'conditions' => [
							'WorkspacePropagate.user_project_id' => $userPrjId,
							'WorkspacePropagate.share_for_id' => $share_user_id,
							'WorkspacePropagate.project_workspace_id' => array_values($pw_data),
						],
						'fields' => ['WorkspacePropagate.id'],
						'recursive' => -1,
					]);
					// EXTRACTING WORKSPACE PERMISSIONS TABLE IDs

					$currentWPermit = Set::extract($currentWPermit, '/WorkspacePropagate/id');
					// -------- Get element permissions of this user with selected project
					$currentEPermit = $this->ElementPropagate->find('all', [
						'conditions' => [
							'ElementPropagate.project_id' => $project_id,
							'ElementPropagate.share_for_id' => $share_user_id,
							'ElementPropagate.workspace_id' => array_keys($pw_data),
						],
						'fields' => ['ElementPropagate.id'],
						'recursive' => -1,
					]);
					// EXTRACTING ELEMENT PERMISSIONS TABLE IDs
					$currentEPermit = Set::extract($currentEPermit, '/ElementPropagate/id');
				}

				// DELETE ALL IDs

				if (!empty($currentPPermit)) {
					if ($this->ProjectPropagate->deleteAll(array('ProjectPropagate.id' => $currentPPermit))) {
						$response['content']['pr_del'] = true;
					}
				}

				if (!empty($currentWPermit)) {
					if ($this->WorkspacePropagate->deleteAll(['WorkspacePropagate.id' => $currentWPermit])) {
						$response['content']['ws_del'] = true;
					}
				}

				if (!empty($currentEPermit)) {
					if ($this->ElementPropagate->deleteAll(['ElementPropagate.id' => $currentEPermit])) {
						$response['content']['el_del'] = true;
					}
				}

				// CHECK STATUS OF ALL DBDATA DELETED OR NOT
				// IF ANYTHING GONE WRONG; RETURN FALSE
				$returnVal = true;
				if (!empty($response['content'])) {
					foreach ($response['content'] as $key => $val) {
						if ($val == false) {
							$returnVal = false;
						}

					}
				}
				// IF RETURN VALUE IS FALSE; THEN CHECK THAT IN DATABASE FOR PROPAGATION IS ACTULLY EXISTS THERE OR NOT; IF NOT AGAIN SET TRUE TO RETURN VALUE
				if (empty($currentPPermit) && empty($currentWPermit) && empty($currentWPermit)) {
					$returnVal = true;
				}
				$response['success'] = true;
			}
		}

		echo json_encode($response);
		exit;

	}

	/*
		 * Get all propagate permissions of a user with selected project
	*/
	public function get_permissions($user_id = null, $project_id = null, $keys = false) {

		$user_id = (isset($user_id) && !empty($user_id)) ? $user_id : 0;

		/*****************************************/
		$userPrjId = project_upid($project_id);
		$fields = null;
		if ($keys) {
			$fields = ['id'];
		}

		// -------- Get project permissions of this user with selected project
		$currentPermit['project'] = $this->ProjectPermission->find('first', [
			'conditions' => [
				'ProjectPermission.user_project_id' => $userPrjId,
				'ProjectPermission.user_id' => $user_id,
			],
			'fields' => (!empty($fields)) ? $fields : ['*'],
			'recursive' => -1,
		]);

		// -------- Get workspace permissions of this user with selected project
		$pw_data = get_project_workspace($project_id, true);
		if(isset($pw_data) && !empty($pw_data)) {
			$currentPermit['workspace'] = $this->WorkspacePermission->find('all', [
				'conditions' => [
					'WorkspacePermission.user_project_id' => $userPrjId,
					'WorkspacePermission.user_id' => $user_id,
					'WorkspacePermission.project_workspace_id' => array_values($pw_data),
				],
				'fields' => (!empty($fields)) ? $fields : ['*'],
				'recursive' => -1,
			]);

			// -------- Get element permissions of this user with selected project
			$currentPermit['element'] = $this->ElementPermission->find('all', [
				'conditions' => [
					'ElementPermission.project_id' => $project_id,
					'ElementPermission.user_id' => $user_id,
					'ElementPermission.workspace_id' => array_keys($pw_data),
				],
				'fields' => (!empty($fields)) ? $fields : ['*'],
				'recursive' => -1,
			]);
		}

		return $currentPermit;
	}

	/*
		 * Get all propagate permissions of a user with selected project
	*/

	public function get_propagations($user_id = null, $project_id = null) {
		$pw_data = array();
		$share_user_id = (isset($user_id) && !empty($user_id)) ? $user_id : 0;

		/*****************************************/
		$userPrjId = project_upid($project_id);

		// DELETE ALL SHARE PERMISSIONS OF THE CURRENT USER BEFORE ADD NEW
		// -------- Get project permissions of this user with selected project
		$currentPermit['project'] = $this->ProjectPropagate->find('first', [
			'conditions' => [
				'ProjectPropagate.user_project_id' => $userPrjId,
				'ProjectPropagate.share_for_id' => $user_id,
			],
			'recursive' => -1,
		]);

		// -------- Get workspace permissions of this user with selected project
		$pw_data = get_project_workspace($project_id, true);
		if( isset($pw_data) && !empty($pw_data) ){
			$currentPermit['workspace'] = $this->WorkspacePropagate->find('all', [
				'conditions' => [
					'WorkspacePropagate.user_project_id' => $userPrjId,
					'WorkspacePropagate.share_for_id' => $user_id,
					'WorkspacePropagate.project_workspace_id' => array_values($pw_data),
				],
				'recursive' => -1,
			]);

			// -------- Get element permissions of this user with selected project
			$currentPermit['element'] = $this->ElementPropagate->find('all', [
				'conditions' => [
					'ElementPropagate.project_id' => $project_id,
					'ElementPropagate.share_for_id' => $user_id,
					'ElementPropagate.workspace_id' => array_keys($pw_data),
				],
				'recursive' => -1,
			]);
		}

		return $currentPermit;
	}

	public function propagation($project_id = null, $share_user_id = null, $share_action = 1, $ppermit_insert_id = 0) {

		$this->layout = 'inner';

		$this->set('title_for_layout', 'Project Sharing - Step 3: Setting Propagation');
		$this->set('page_heading', 'Step 3: Setting Propagation');

		// INCASE PROJECT ID NOT PRESENT IN THE URL
		if (!isset($project_id) || empty($project_id)) {
			$this->redirect(array('controller' => 'shares', 'action' => 'my_sharing'));
		}

		# GET USER PROJECT ID OF SUPPLIED PROJECT
		$userProjectId = project_upid($project_id);
		$this->set('project_id', $project_id);

		$projectPermissionId = 0;
		$projectPermissionId = (isset($ppermit_insert_id) && !empty($ppermit_insert_id)) ? $ppermit_insert_id : 0;

		if ($this->request->is('post') || $this->request->is('put')) {

			$this->save_propagation(['project_id' => $project_id, 'share_user_id' => $share_user_id, 'share_action' => $share_action, 'projectPermissionId' => $projectPermissionId]);

		} // END CHECK POST
		$this->set('ppermit_insert_id', $projectPermissionId);

		$share_user_id = (isset($share_user_id) && !empty($share_user_id)) ? $share_user_id : 0;
		$this->set('shareUser', $share_user_id);

		// if share option is for add new append 1 with the URL, otherwise append 2
		$share_action = (isset($share_action) && !empty($share_action)) ? $share_action : 1;
		$this->set('share_action', $share_action);

		$conditions['UserProject.project_id'] = $project_id;
		#----------- check sharing permissions -----------
		if (has_permissions($project_id)) {
			$conditions['UserProject.user_id'] = $this->user_id;
		}
		#----------- check sharing permissions -----------

		// Project Detail of Passed id
		$project_detail = $this->UserProject->find('first', ['conditions' => $conditions, 'recursive' => 2]);
		$this->set('project_detail', $project_detail);
		// Check this user for permissions data exists or not
		$pp_data = $wp_data = $ep_data = [];
		$exist_permissions = null;
		$pp_data_count = $wp_data_count = $ep_data_count = 0;

		if (isset($userProjectId) && !empty($userProjectId)) {

			$projectWorkspaceId = Set::extract($project_detail, '/Project/ProjectWorkspace/id');
			$workspaceId = Set::extract($project_detail, '/Project/ProjectWorkspace/workspace_id');

			// Check this user for previous data exists or not
			if (!empty($share_user_id)) {

				$pp_data = $this->ProjectPermission->find('first', [
					'conditions' => [
						'ProjectPermission.user_id' => $share_user_id,
						'ProjectPermission.user_project_id' => $userProjectId,
					],
					'recursive' => -1,
				]);

				$pp_data_count = ( isset($pp_data) && !empty($pp_data) ) ? count($pp_data) : 0;
				if (!empty($pp_data_count)) {
					$exist_permissions['pp_data'] = $pp_data;
					$exist_permissions['pp_data_count'] = $pp_data_count;
				}

				// Get project_workspace_id of the current project and find those in workspace_permissions for this user
				$wp_data = $this->WorkspacePermission->find('all', [
					'conditions' => [
						'WorkspacePermission.user_id' => $share_user_id,
						'WorkspacePermission.user_project_id' => $userProjectId,
						'WorkspacePermission.project_workspace_id' => $projectWorkspaceId,
					],
					'recursive' => -1,
				]);

				$wp_data_count = ( isset($wp_data) && !empty($wp_data) ) ? count($wp_data) : 0;
				if (!empty($wp_data_count)) {
					$exist_permissions['wp_data'] = $wp_data;
					$exist_permissions['wp_data_count'] = $wp_data_count;
				}

				$view = new View();
				$viewModal = $view->loadHelper('ViewModel');

				// Get all area ids of all workspaces
				$ws_area = $viewModal->workspace_areas($workspaceId, false, true);
				// Get all element ids of those
				$elm = $viewModal->area_elements($ws_area, true);

				// Find all element ids in element_permissions
				$ep_data = $this->ElementPermission->find('all', [
					'conditions' => [
						'ElementPermission.user_id' => $share_user_id,
						'ElementPermission.project_id' => $project_id,
						'ElementPermission.workspace_id' => $workspaceId,
						'ElementPermission.element_id' => $elm,
					],
					'recursive' => -1,
				]);
				$ep_data_count = ( isset($ep_data) && !empty($ep_data) ) ? count($ep_data) : 0;
				if (!empty($ep_data_count)) {
					$exist_permissions['ep_data'] = $ep_data;
					$exist_permissions['ep_data_count'] = $ep_data_count;
				}
			}
		}

		$this->set('exist_permissions', $exist_permissions);

		$userPropagatePermissions = $this->ProjectPermission->find('first', [
			'conditions' => [
				'ProjectPermission.user_id' => $this->user_id,
				'ProjectPermission.user_project_id' => project_upid($project_id),
			],
			'recursive' => -1,
		]);
		$this->set('user_propagate_permissions', $userPropagatePermissions);

		if (isset($project_detail) && !empty($project_detail)) {

			// Get Project Workspace and Elements in a Tree
			// $share_data = getProjectWorkspaces($project_id, true);

			// $this->set('share_data', $share_data);

			//$cat_crumb = get_category_list($project_id);

			$project_title = _strip_tags($project_detail['Project']['title']);

			$crumb = [
				'Summary' => [
					'data' => [
						'url' => '/projects/index/' . $project_id,
						'class' => 'tipText',
						'title' => $project_detail['Project']['title'],
						'data-original-title' => $project_detail['Project']['title'],
					],
				],
				'Sharing' => [
					'data' => [
						'url' => '/shares/my_sharing',
						'class' => 'tipText',
						'title' => "My Shared Projects",
						'data-original-title' => "My Shared Projects",
					],
				],
				'last' => [
					'data' => [
						'title' => 'Sharing: Propagation',
						'data-original-title' => 'Sharing: Propagation',
					],
				],
			];
			/*if (isset($cat_crumb) && !empty($cat_crumb)) {
				$crumb = array_merge($cat_crumb, $crumb);
			}*/

			$this->set('crumb', $crumb);
		}

	}
	// add_propagation

	public function total_sharing($project_id = null) {

		$this->layout = 'inner';

		$this->set('page_heading', 'Shared Projects');
		$this->set('title_for_layout', 'Shared Projects');
		// Check this user for previous data exists or not
		$permit_data = null;
		if (isset($this->user_id) && !empty($this->user_id)) {
			$owner_id = $this->user_id;
			$this->User->unbindModel([
				'hasMany' => [
					'UserTransctionDetail',
					'UserInstitution',
					'UserPlan',
				],
				'hasOne' => [
					'UserDetail',
				],
			]);
			$pp_data = $this->ProjectPermission->find('count', [
				'conditions' => [
					'ProjectPermission.owner_id' => $owner_id,
					//'ProjectPermission.share_by_id !=' => $owner_id,
					'OR' => [
						'ProjectPermission.parent_id IS NULL', 'ProjectPermission.parent_id' => 0,
					],
					'ProjectPermission.user_id  IS NOT NULL',
				],
				'order' => ['ProjectPermission.created DESC'],
				'group' => ['ProjectPermission.user_project_id'],
				'recursive' => -1,
			]);
			//pr($pp_data,1);
			return (isset($pp_data) && !empty($pp_data)) ? $pp_data : 0;

		}

	}

	public function my_sharing($project_id = null) {

		$this->layout = 'inner';

		$this->set('page_heading', 'My Sharing');
		// $this->set('page_subheading', 'View Projects you have shared with others');
		$this->set('page_subheading', 'View Projects you created and then shared onwards');
		$this->set('title_for_layout', 'My Sharing');
		// Check this user for previous data exists or not
		$permit_data = null;
		if (isset($this->user_id) && !empty($this->user_id)) {
			$owner_id = $this->user_id;
			$user_id = $this->user_id;

			//== Propagated project count
			$user_permissions = $this->ProjectPermission->find('count', [
				'conditions' => [
					'ProjectPermission.share_by_id' => $user_id,
					'ProjectPermission.owner_id !=' => $user_id,
					'OR' => [
						'ProjectPermission.parent_id IS NOT NULL', 'ProjectPermission.parent_id >' => 0,
					],
				],
				'order' => ['ProjectPermission.created DESC'],
				'group' => ['ProjectPermission.user_project_id'],
				'recursive' => 1,
			]);


			$this->User->unbindModel([
				'hasMany' => [
					'UserTransctionDetail',
					'UserInstitution',
					'UserPlan',
				],
				'hasOne' => [
					'UserDetail',
				],
			]);
			$pp_data = $this->ProjectPermission->find('all', [
				'conditions' => [
					'ProjectPermission.owner_id' => $owner_id,
					//'ProjectPermission.share_by_id !=' => '',
					'UserProject.id IS NOT NULL',
					'OR' => [
						'ProjectPermission.parent_id IS NULL',
						'ProjectPermission.parent_id' => 0,
						'ProjectPermission.user_id  IS NOT NULL',
					],

				],
				'order' => ['ProjectPermission.created DESC'],
				'group' => ['ProjectPermission.user_project_id'],
				'recursive' => 2,
			]);

			$pp_data_count = ( isset($pp_data) && !empty($pp_data) ) ? count($pp_data) : 0;



			if (!empty($pp_data_count)) {
				$permit_data['pp_data'] = $pp_data;
				$permit_data['pp_data_count'] = $pp_data_count;
			}
		}

		if (isset($project_id) && !empty($project_id)) {
			$this->setJsVar('project_id', $project_id);
		}
		$this->set('permit_data', $permit_data);
		$this->set('user_permissions', $user_permissions);

		$crumb = [
			'last' => [
				'data' => [
					'title' => 'My Sharing',
					'data-original-title' => 'My Sharing',
				],
			],
		];
		$this->set('crumb', $crumb);
	}

	public function sharing_map($project_id = null) {

		$this->layout = 'inner';

		$this->set('title_for_layout', 'Sharing Map');
		//$this->set('page_heading', 'View how this Project has been shared');
		$this->set('page_heading', 'View the Project social network');

		$permit_data = null;

		if (isset($project_id) && !empty($project_id)) {

			$structureObj = new SharingTree();

			$da = $structureObj->getRecords($project_id);

			$list_html = $structureObj->generateStructure();

			$this->set('list_html', $list_html);
			// Check the user is owner of this project
			$userProjectData = $this->UserProject->find('count', [
				'conditions' => [
					'UserProject.project_id' => $project_id,
					'UserProject.user_id' => $this->user_id,
					'UserProject.owner_user' => 1,
				],
			]);
			// if this is the owner user
			if (!empty($userProjectData)) {

				$user_project_id = project_upid($project_id);

				$permit_data = $this->ProjectPermission->find('all', [
					'conditions' => [
						'ProjectPermission.user_project_id' => $user_project_id,

						'OR' => [
							'ProjectPermission.parent_id IS NULL', 'ProjectPermission.parent_id' => 0,
						],
					],
					'order' => ['ProjectPermission.created DESC'],
					'recursive' => -1,
				]);
			}
		}

		$view = new View();
		$viewModal = $view->loadHelper('ViewModel');

		$projectDetail = $viewModal->getProjectDetail($project_id);

		$crumb = [
			'Summary' => [
				'data' => [
					'url' => '/projects/index/' . $project_id,
					'class' => 'tipText',
					'title' => $projectDetail['Project']['title'],
					'data-original-title' => htmlentities($projectDetail['Project']['title']),
				],
			],
			'last' => [
				'data' => [
					'title' => 'Sharing Map',
					'data-original-title' => 'Sharing Map',
				],
			],
		];

		$this->set('crumb', $crumb);
		$this->set('user_id', $this->user_id);
		$this->set('project_detail', $projectDetail);
		$this->set('permit_data', $permit_data);
	}

	public function total_propagate() {

		$permit_data = null;

		$user_id = $this->user_id;

		$this->User->unbindModel([
			'hasMany' => [
				'UserTransctionDetail',
				'UserInstitution',
				'UserPlan',
			],
			'hasOne' => [
				'UserDetail',
			],
		]);
		$propagate_data = 0;
		$propagate_data = $this->ProjectPermission->find('count', [
			'conditions' => [
				'ProjectPermission.share_by_id' => $user_id,
				'ProjectPermission.owner_id !=' => $user_id,
				'UserProject.id !=' => '',
				'OR' => [
					'ProjectPermission.parent_id IS NOT NULL', 'ProjectPermission.parent_id !=' => 0,
				],
				'ProjectPermission.user_id  IS NOT NULL',
			],
			'order' => ['ProjectPermission.id DESC'],
			'group' => ['ProjectPermission.user_project_id'],
			'recursive' => 1,
		]);

		return $propagate_data ? $propagate_data : 0;

		//pr($propagate_data,1);

	}

	public function propagated_projects() {

		$this->layout = 'inner';

		/* $this->set('page_heading', 'Propagated Projects');
		$this->set('title_for_layout', 'Propagated Projects'); */
		$this->set('page_heading', 'My Sharing');
		// $this->set('page_subheading', 'View Projects you have shared with others');
		$this->set('page_subheading', 'View Projects you received and then shared onwards');
		$this->set('title_for_layout', 'My Sharing');

		// Check this user for previous data exists or not
		$permit_data = null;

		$user_id = $this->user_id;
		$owner_id = $this->user_id;

			$pp_data = $this->ProjectPermission->find('count', [
				'conditions' => [
					'ProjectPermission.owner_id' => $owner_id,
					//'ProjectPermission.share_by_id !=' => '',
					'UserProject.id IS NOT NULL',
					'OR' => [
						'ProjectPermission.parent_id IS NULL', 'ProjectPermission.parent_id' => 0,
					], 'ProjectPermission.user_id  IS NOT NULL',

				],
				'order' => ['ProjectPermission.created DESC'],
				'group' => ['ProjectPermission.user_project_id'],
				'recursive' => 2,
			]);

			if (isset($pp_data) && !empty($pp_data) && $pp_data > 0) {
				$permit_data = $pp_data;
			} else {
				$permit_data = 0;
			}
		$this->set('permit_data', $permit_data);


		$this->User->unbindModel([
			'hasMany' => [
				'UserTransctionDetail',
				'UserInstitution',
				'UserPlan',
			],
			'hasOne' => [
				'UserDetail',
			],
		]);

		$propagate_data = $this->ProjectPermission->find('all', [
			'conditions' => [
				'ProjectPermission.share_by_id' => $user_id,
				'OR' => [
					'ProjectPermission.parent_id IS NOT NULL', 'ProjectPermission.parent_id !=' => 0,
				],
			],
			'order' => ['ProjectPermission.id DESC'],
			'group' => ['ProjectPermission.user_project_id'],
			'recursive' => -1,
		]);
		//	pr( $propagate_data, 1);
		$this->set('propagate_data', $propagate_data);
		//

		$user_permissions = $this->ProjectPermission->find('all', [
			'conditions' => [
				'ProjectPermission.share_by_id' => $user_id,
				'ProjectPermission.owner_id !=' => $user_id,
				'OR' => [
					'ProjectPermission.parent_id IS NOT NULL', 'ProjectPermission.parent_id >' => 0,
				],
			],
			'order' => ['ProjectPermission.created DESC'],
			'group' => ['ProjectPermission.user_project_id'],
			'recursive' => 1,
		]);
		//pr($user_permissions, 1);
		$this->set('user_permissions', $user_permissions);

		$crumb = [
			'last' => [
				'data' => [
					//'title' => 'Propagated Projects',
					'title' => 'My Sharing',
					'data-original-title' => 'My Sharing',
					//'data-original-title' => 'Propagated Projects',
				],
			],
		];

		$this->set('crumb', $crumb);
	}

	public function get_by_project() {

		$this->layout = 'ajax';
		if ($this->request->isAjax()) {

			$response = [
				'success' => false,
				'action' => null,
				'content' => null,
			];

			$this->autoRender = false;

			if ($this->request->is('post') || $this->request->is('put')) {
				// Get Post Data
				$post = $this->request->data;
				$project_title = null;
				if (isset($post['project_title']) && !empty($post['project_title'])) {
					$project_title = $post['project_title'];
				}

				$projects = $this->Project->query("SELECT id FROM projects WHERE title = '" . $project_title . "'");

				// $userProjectId = project_upid($search_project);
				// Check this user for previous data exists or not
				$permit_data = null;
				if (isset($this->user_id) && !empty($this->user_id)) {
					$owner_id = $this->user_id;
					$this->User->unbindModel([
						'hasMany' => [
							'UserTransctionDetail',
							'UserInstitution',
							'UserPlan',
						],
						'hasOne' => [
							'UserDetail',
						],
					]);
					$pp_data = $this->ProjectPermission->find('all', [
						'conditions' => [
							'ProjectPermission.owner_id' => $owner_id,
							'ProjectPermission.user_project_id' => $userProjectId,
							'OR' => [
								'ProjectPermission.parent_id IS NULL', 'ProjectPermission.parent_id' => 0,
							],
						],
						'order' => ['ProjectPermission.created DESC'],
						'group' => ['ProjectPermission.user_project_id'],
						'recursive' => 2,
					]);
					$pp_data_count = ( isset($pp_data) && !empty($pp_data) ) ? count($pp_data) : 0;
					if (!empty($pp_data_count)) {
						$permit_data['pp_data'] = $pp_data;
						$permit_data['pp_data_count'] = $pp_data_count;
					}
				}
			}
		}

	}

	public function map_permissions($user_id = null, $project_id = null) {

		$this->layout = 'ajax';
		if ($this->request->isAjax()) {

			$response = [
				'success' => false,
				'action' => null,
				'content' => null,
			];

			$this->autoRender = false;

			// $userProjectId = project_upid($search_project);
			// Check this user for previous data exists or not
			$permit_data = null;
			if (isset($user_id) && !empty($user_id)) {

				$this->User->unbindModel([
					'hasMany' => [
						'UserTransctionDetail',
						'UserInstitution',
						'UserPlan',
						// 'ProjectPermission',
						// 'WorkspacePermission',
						// 'ElementPermission',
						'UserProject',
					],
					'hasOne' => [
						'UserInstitution',
					],
				]);
				$user_data = $this->User->ProjectPermission->find('all', [
					'conditions' => [
						'ProjectPermission.user_id' => $user_id,
						'ProjectPermission.user_project_id' => project_upid($project_id),
					],
				]);

			}

			//pr($user_data);
			$this->set('user_id', $user_id);
			$this->set('project_id', $project_id);
			$this->set('user_data', $user_data);
			$this->render(DS . 'Shares' . DS . 'partials' . DS . 'map_permissions');

		}

	}

	public function gmap_permissions($gp_id = null, $project_id = null) {

		$this->layout = 'ajax';
		if ($this->request->isAjax()) {

			$response = [
				'success' => false,
				'action' => null,
				'content' => null,
			];

			$this->autoRender = false;

			// $userProjectId = project_upid($search_project);
			// Check this user for previous data exists or not
			$permit_data = null;
			if (isset($gp_id) && !empty($gp_id)) {

				$this->User->unbindModel([
					'hasMany' => [
						'UserTransctionDetail',
						'UserInstitution',
						'UserPlan',
						// 'ProjectPermission',
						// 'WorkspacePermission',
						// 'ElementPermission',
						'UserProject',
					],
					'hasOne' => [
						'UserInstitution',
					],
				]);
				$user_data = $this->ProjectPermission->find('all', [
					'conditions' => [
						'ProjectPermission.project_group_id' => $gp_id,
						'ProjectPermission.user_project_id' => project_upid($project_id),
					],
				]);
			}

			$gp_data = $this->ProjectGroup->find('first', [
				'conditions' => [
					'ProjectGroup.id' => $gp_id,
				],
			]);

			$gp_dataU = $this->ProjectGroupUser->find('all', [
				'conditions' => [
					'ProjectGroupUser.project_group_id' => $gp_id,
				],
			]);

			$this->set('group_id', $gp_id);
			$this->set('project_id', $project_id);
			$this->set('user_data', $user_data);
			$this->set('gp_data', $gp_data);
			$this->set('gp_dataU', $gp_dataU);
			$this->render(DS . 'Shares' . DS . 'partials' . DS . 'gmap_permissions');

		}

	}

	public function editor_check() {

		$this->layout = 'inner';

		/* if(isset($_FILES) && !empty($_FILES)) {
				$folder_url = WWW_ROOT . ELEMENT_DOCUMENT_PATH;
				$upload_object = (isset($_FILES ["file"])) ? $_FILES ["file"] : null;
				$folder_url .= DS . '86';
				// $extension = pathinfo($fileName, PATHINFO_EXTENSION);

				$tempFile = $upload_object['tmp_name'];
				$targetFile = $folder_url . DS . $upload_object['name'];
				copy($tempFile, $targetFile);
			}
			echo json_encode(['success' => $upload_object]);
		*/
	}

	public function shares1() {
		$this->layout = 'inner';

	}

	/* ======================= ADMIN FUNCTIONS ========================= */

	public function admin_index() {

		$this->layout = 'admin_inner';

		$this->set('title_for_layout', __('Project Summary', true));

		$user_id = $this->Auth->user('id');

		// CHECK THAT HTTP-REFERER IS manage_project
		// TO SHOW DIFFERENT MESSAGE ON LISTING
		$create_referer = false;
		if ($this->referer() == Router::url(array('action' => 'manage_project'), true)) {
			$create_referer = true;
		} else {
			$create_referer = false;
		}
		$this->set('create_referer', $create_referer);

		if (isset($project_id) && !empty($project_id)) {

			$projects = $this->UserProject->find('first', ['recursive' => 5, 'conditions' => ['UserProject.user_id' => $this->user_id, 'UserProject.project_id' => $project_id]]);
			//
			// echo $this->UserProject->_query();die;
			$this->set('project_id', $project_id);
		} else {
			$projects = $this->UserProject->find('first', ['recursive' => 5, 'conditions' => ['UserProject.user_id' => $this->user_id], 'order' => 'UserProject.modified DESC']);
			if (!is_null($projects)) {
				$projects_arr = Set::extract($projects, '/UserProject/project_id');
				$project_id = (isset($projects_arr[0]) && !empty($projects_arr[0])) ? $projects_arr[0] : null;
				$this->set("project_id", $project_id);
			}
		}
		// Get logged in users projects
		$this->set(compact('projects'));

		$this->set('projects', $projects);
	}

	/**
	 * Displays a view
	 *
	 * @param mixed What page to display
	 * @return void
	 */
	public function admin_indexs() {

		$orConditions = array();
		$andConditions = array();
		$finalConditions = array();

		$in = 0;
		$per_page_show = $this->Session->read('project.per_page_show');
		if (empty($per_page_show)) {
			$per_page_show = ADMIN_PAGING;
		}

		if (isset($this->data['Project']['keyword'])) {
			$keyword = $this->data['Project']['keyword'];
		} else {
			$keyword = $this->Session->read('project.keyword');
		}

		if (isset($keyword)) {
			$this->Session->write('project.keyword', $keyword);
			if (!empty($keyword)) {
				$in = 1;
				$orConditions = array('OR' => array('Project.project_name LIKE' => '%' . $keyword . '%'));
			}
		}

		if (isset($this->data['Project']['projects_status_id'])) {
			$projects_status_id = $this->data['Project']['projects_status_id'];
		} else {
			$projects_status_id = $this->Session->read('project.projects_status_id');
		}

		if (isset($projects_status_id)) {
			$this->Session->write('project.projects_status_id', $projects_status_id);
			if ($projects_status_id != '') {
				$in = 1;
				$andConditions = array_merge($andConditions, array('Project.projects_status_id' => $projects_status_id));
			}
		}

		if (isset($this->data['Project']['per_page_show']) && !empty($this->data['Project']['per_page_show'])) {
			$per_page_show = $this->data['Project']['per_page_show'];
		}

		if (!empty($orConditions)) {
			$finalConditions = array_merge($finalConditions, $orConditions);
		}
		if (!empty($andConditions)) {
			$finalConditions = array_merge($finalConditions, array('AND' => $andConditions));
		}

		$this->set('title_for_layout', __('All Projects', true));
		$this->Session->write('project.per_page_show', $per_page_show);
		//$this->Project->recursive = 0;
		$this->paginate = array('conditions' => $finalConditions, "limit" => $per_page_show, "order" => "Project.created DESC");
		$this->set('projects', $this->paginate('Project'));
		$this->set('in', $in);
	}

	/**
	 * admin_source_view method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function admin_view($id = null) {
		$this->set('title_for_layout', __('View Project', true));
		$this->Project->id = $id;
		if (!$this->Project->exists()) {
			throw new NotFoundException(__('Invalid Project'));
		}

		$this->request->data = $this->Project->read(null, $id);
	}

	/**
	 * admin_source_add method
	 *
	 * @return void
	 */
	public function admin_add() {
		$this->set('title_for_layout', __('Add Project', true));
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->Project->save($this->request->data)) {
				$this->Session->setFlash(__('The Project has been saved successfully.'), 'success');
				die('success');
			}
		}
	}

	/**
	 * admin_source_edit method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function admin_edit($id = null) {

		$this->set('title_for_layout', __('Edit Project', true));
		$this->Project->id = $id;
		//check country exist
		if (!$this->Project->exists()) {
			$this->Session->setFlash(__('Invalid Project.'), 'error');
			$this->redirect(array('action' => 'index'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {

			if ($this->Project->save($this->request->data)) {
				$this->Session->setFlash(__('The Project has been updated successfully.'), 'success');
				die('success');
			}
		} else {
			$this->request->data = $this->Project->read(null, $id);
		}
	}

	/**
	 * admin_source_delete method
	 *
	 * @throws MethodNotAllowedException
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function admin_delete($id = null) {
		if (isset($this->data['id']) && !empty($this->data['id'])) {
			$id = $this->data['id'];
			$this->Project->id = $id;
			if (!$this->Project->exists()) {
				throw new NotFoundException(__('Invalid Project'), 'error');
			}

			if ($this->Project->delete()) {
				$this->Session->setFlash(__('Project has been deleted successfully.'), 'success');
				die('success');
			} else {
				$this->Session->setFlash(__('Project could not deleted successfully.'), 'error');
			}
		}
		die('error');
	}

/*************************************Group Functions***********************************************/

	public function my_groups($project_id = null) {

		$this->layout = 'inner';

		$this->set('page_heading', 'My Groups');

		$this->set('title_for_layout', 'My Groups');
		// Check this user for previous data exists or not
		$permit_data = null;
		if (isset($this->user_id) && !empty($this->user_id)) {
			$owner_id = $this->user_id;
			$this->User->unbindModel([
				'hasMany' => [
					'UserTransctionDetail',
					'UserInstitution',
					'UserPlan',
				],
				'hasOne' => [
					'UserDetail',
				],
			]);
			$pp_data = $this->ProjectGroup->find('all', [
				'conditions' => [
					'ProjectGroup.group_owner_id' => $owner_id,
					'ProjectGroup.is_deleted' => 0,
				],
				'order' => ['ProjectGroup.created DESC'],
				'recursive' => 2,
			]);

			//pr($pp_data,1);

			$pp_data_count = ( isset($pp_data) && !empty($pp_data) ) ? count($pp_data) : 0;

			if (!empty($pp_data_count)) {
				$permit_data['pp_data'] = $pp_data;
				$permit_data['pp_data_count'] = $pp_data_count;
			}
		}

		if (isset($project_id) && !empty($project_id)) {
			$this->setJsVar('project_id', $project_id);
		}

		$this->set('permit_data', $permit_data);
		$this->set('pp_data_count', $pp_data_count);

		$crumb = [
			'last' => [
				'data' => [
					'title' => 'My Groups',
					'data-original-title' => 'My Groups',
				],
			],
		];
		$this->set('crumb', $crumb);
	}

	public function trashGroupUser() {

		if ($this->request->isAjax()) {

			$response = [
				'success' => false,
				'msg' => '',
				'content' => null,
			];

			$this->layout = 'ajax';
			//$this->autoRender = false;

			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;
				$this->loadModel('ProjectGroupUser');
				$this->loadModel('ProgramProject');
				$this->loadModel('Program');
				// pr($post['ProjectGroupUser']);
				// Get Project Id with Element id; Update Project modified date
				//$this->update_project_modify($id);

				$ggid = $this->ProjectGroupUser->find('first', ['conditions' => ['ProjectGroupUser.project_group_id' => $post['ProjectGroupUser']['project_group_id'], 'ProjectGroupUser.user_project_id' => $post['ProjectGroupUser']['user_project_id'], 'ProjectGroupUser.user_id' => $post['ProjectGroupUser']['user_id']], 'fields' => 'ProjectGroupUser.id', 'recursive' => 2]);

				$this->ProjectGroupUser->id = $ggid['ProjectGroupUser']['id'];

				if ($this->ProjectGroupUser->delete()) {

					// Delete group user from user-project-connection into mongo db
					$project_primary_id = project_primary_id($post['ProjectGroupUser']['user_project_id']);
					
					
					$program_ids = $this->Program->find('list', ['conditions' => ['Program.created_by' => $post['ProjectGroupUser']['user_id'] ],'fields' => array('Program.id')]);
					
					if(isset($program_ids) && !empty($program_ids)){
					
						$ids = $this->ProgramProject->find('list', ['conditions' => ['ProgramProject.project_id' => $project_primary_id , 'ProgramProject.program_id' => $program_ids  ],'fields' => array('ProgramProject.id')]);
					
					if(isset($ids) && !empty($ids)){
					
						$this->ProgramProject->deleteAll(array('ProgramProject.id' => $ids));
					
					}
					
					}
					
					$this->Users->userConnections($post['ProjectGroupUser']['user_id'], $project_primary_id, true);

					$response['success'] = true;
					$response['msg'] = 'User has been deleted from this group successfully.';
					$response['content'] = [];
				} else {
					$response['msg'] = 'User could not deleted.';

				}
				//$this->ProjectGroupUser->_query(1);
			}

			echo json_encode($response);

		}
		exit();
	}

	public function AcceptGroupRequest() {

		if ($this->request->isAjax()) {

			$response = [
				'success' => false,
				'msg' => '',
				'content' => null,
			];

			$this->layout = 'ajax';
			//$this->autoRender = false;

			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;

				$this->loadModel('ProjectGroupUser');

				$this->ProjectGroupUser->id = $post['ProjectGroupUser']['id'];

				// pr($post );
				if (isset($post['ProjectGroupUser']['approved']) && $post['ProjectGroupUser']['approved'] > 1) {

					if ($this->ProjectGroupUser->save($this->request->data['ProjectGroupUser'])) {
						### save to history
						$dbid = $post['ProjectGroupUser']['id'];
						$req_detail = $this->ProjectGroupUser->find('first', ['conditions' => ['ProjectGroupUser.id' => $dbid], 'recursive' => -1]);

						$response['success'] = true;
						$response['msg'] = 'Request has been rejected successfully.';
						$response['content'] = ['date' => _displayDate($req_detail['ProjectGroupUser']['modified']), 'status' => 2];
					} else {
						$response['msg'] = 'Invalid Selection.';
					}

				}
				else {

					if ($this->ProjectGroupUser->save($this->request->data['ProjectGroupUser'])) {
						$db_id = $this->request->data['ProjectGroupUser']['id'];

						$req_data = $this->ProjectGroupUser->find('first', ['conditions' => ['ProjectGroupUser.id' => $db_id], 'recursive' => 1]);
						$grp_data = $this->ProjectGroup->find('first', ['conditions' => ['ProjectGroup.id' => $req_data['ProjectGroupUser']['project_group_id']], 'fields' => ['ProjectGroup.group_owner_id'], 'recursive' => -1]);

						if($req_data['ProjectGroupUser']['approved'] == 1) {
							// Add user project connection to mongo db
							$project_primary_id = project_primary_id($req_data['ProjectGroupUser']['user_project_id']);
							$this->Users->userConnections($this->user_id, $project_primary_id);
						}

						$response['success'] = true;

						$response['msg'] = 'Request has been approved successfully.';

						$response['content'] = ['date' => _displayDate($req_data['ProjectGroupUser']['modified']), 'status' => 1, 'project_id'=>$project_primary_id, 'group_owner_id' => $grp_data['ProjectGroup']['group_owner_id']];
					} else {
						$response['msg'] = 'Invalid Selection.';
					}

				}
				//$this->ProjectGroupUser->_query(1);
			}

			echo json_encode($response);

		}
		exit();
	}

	public function group_requests() {

		$this->layout = 'inner';

		$this->set('page_heading', 'Group Requests');

		$this->set('title_for_layout', 'Group Requests');
		// Check this user for previous data exists or not
		$permit_data = null;
		if (isset($this->user_id) && !empty($this->user_id)) {

			$this->User->unbindModel([
				'hasMany' => [
					'UserTransctionDetail',
					'UserInstitution',
					'UserPlan',
				],
				'hasOne' => [
					'UserDetail',
				],
			]);

			$pp_data = $this->ProjectGroupUser->find('all', [
				'conditions' => [
					'ProjectGroupUser.user_id' => $this->user_id,
					'ProjectGroupUser.approved' => [0, 1, 2],
				],
				'recursive' => 1,
			]);

			$pp_data_count = ( isset($pp_data) && !empty($pp_data) ) ? count($pp_data) : 0;

		}

		if (isset($project_id) && !empty($project_id)) {
			$this->setJsVar('project_id', $project_id);
		}

		$this->set('permit_data', $pp_data);

		$crumb = [
			'last' => [
				'data' => [
					'title' => 'Group Requests',
					'data-original-title' => 'Group Requests',
				],
			],
		];
		$this->set('crumb', $crumb);
	}


	public function show_profile($user_id, $tab = '') {
		if ($this->request->isAjax()) {
			$this->loadModel('Timezone');
			App::import("Model", "User");
			$this->User = new User();

			$this->User->id = $user_id;

			$response = [
				'success' => false,
				'content' => null,
				'msg' => '',
			];

			if (!$this->User->exists()) {
				$response['msg'] = 'Invalid User Profile.';
			}

			$task_data = [
						'user_id' => $user_id,
						'updated_user_id' => $this->user_id,
						'message' => 'Person viewed',
						'updated' => date("Y-m-d H:i:s"),
					];

			$this->loadModel('UserActivity');
			$this->UserActivity->id = null;
			$this->UserActivity->save($task_data);

			$qry = "SELECT u.*, ud.*, org.name AS org_name, org.image AS org_image, ot.type AS ot_type, dept.name AS dept_name, dept.image AS dept_image, loc.id AS loc_id, loc.name AS loc_name, loc.image AS loc_image, loc.city AS loc_city, lt.type AS lt_type, countries.countryName as country_name,
				( SELECT JSON_ARRAYAGG(JSON_OBJECT( 'full_name', CONCAT_WS(' ',user_details.first_name , user_details.last_name), 'job_title',user_details.job_title, 'user_id', user_details.user_id, 'profile_pic',user_details.profile_pic,'dotted_org', user_details.organization_id )) AS JSON
					FROM `users`
					inner join user_details on user_details.user_id = users.id
					WHERE users.id IN (
							select user_dotted_lines.dotted_user_id
							from user_dotted_lines
							where user_details.user_id = user_dotted_lines.dotted_user_id AND user_dotted_lines.user_id = $user_id
						)
				) as dotted_users,

				CONCAT_WS(' ', udd.first_name , udd.last_name) AS reports_to_user, udd.job_title AS reports_to_job, udd.profile_pic AS reports_to_pic,udd.organization_id as reports_to_org,

				( SELECT JSON_ARRAYAGG(JSON_OBJECT( 'id', st.id, 'name', st.name, 'file', st.image, 'type', story_types.type )) AS JSON FROM stories st LEFT JOIN story_users su ON st.id = su.story_id INNER JOIN story_types on story_types.id = st.type_id WHERE su.user_id = $user_id  ) as selected_stories

					FROM users u
					LEFT JOIN user_details ud ON ud.user_id = u.id
					LEFT JOIN user_details udd on udd.user_id = ud.reports_to_id
					LEFT JOIN organizations org ON org.id = ud.organization_id
					LEFT JOIN locations loc ON loc.id = ud.location_id
					LEFT JOIN departments dept ON dept.id = ud.department_id
					LEFT JOIN organization_types ot ON ot.id = org.type_id
					LEFT JOIN location_types lt ON lt.id = loc.type_id
					LEFT JOIN countries on countries.id = loc.country_id
					WHERE u.id = $user_id
					";
			$show_data = $this->User->query($qry);
			$this->set('show_data', $show_data);
			// $this->User->bindModel(['hasOne' => ['UserDetail'], 'hasMany' => ['UserSkill']]);

			$userTimezone = $this->Timezone->find('first', ['conditions' => ['Timezone.user_id' => $user_id]]);

			$this->loadModel('Tag');
			$cond = ['user_id' => $this->user_id, 'tagged_user_id' => $user_id];
			$tags = $this->Tag->find('all', ['conditions' => $cond,'fields' => array('Tag.id', 'Tag.tag'), 'order' => ['tag ASC'], 'group' => array('tag')]);
			$sk = array();
			if (isset($tags) && !empty($tags)) {
				foreach ($tags as $key => $value) {
					$sk[] = ['id' => $value['Tag']['id'], 'name' => $value['Tag']['tag']];
				}
			}

			$loggedUser = $this->User->query("SELECT organization_id FROM user_details WHERE user_id = '".$this->user_id."'");
			$this->set('loggedUser', $loggedUser[0]['user_details']);

			$this->set('user_timezone', $userTimezone);
			$this->set('referer', $this->referer());
			$this->set('tab', $tab);
			$this->set('user_id', $user_id);
			$this->set('tagsCnt', count($sk));
		}
		else{
			$this->redirect(array('controller' => 'users', 'action' => 'login'));
		}
	}

	public function show_org_profile($user_id, $project_id = null) {
		$this->loadModel('Timezone');
		App::import("Model", "User");
		$this->User = new User();

		$this->User->id = $user_id;

		$response = [
			'success' => false,
			'content' => null,
			'msg' => '',
		];

		if (!$this->User->exists()) {
			$response['msg'] = 'Invalid User Profile.';

		}
		// $this->User->unbindAll();
		// $this->User->bindModel(['hasOne' => ['UserDetail'], 'hasMany' => ['UserSkill']]);

		$userTimezone = $this->Timezone->find('first', ['conditions' => ['Timezone.user_id' => $user_id]]);
		$user_details = $this->User->find('first', ['conditions' => ['User.id' => $user_id]]);

		//pr($user_details, 1);

		$this->set('user_timezone', $userTimezone);
		$this->set('user_details', $user_details);
		$this->set('referer', $this->referer());
		$this->set('project_id', $project_id);

	}

	public function get_user_by_skills() {

		$this->layout = 'ajax';
		if ($this->request->isAjax()) {

			$response = [
				'success' => false,
				'content' => null,
				'msg' => '',
			];

			if (isset($this->request->data['user_ids']) && !empty($this->request->data['user_ids'])) {

				if (isset($this->request->data['filter']) && !empty($this->request->data['filter'])) {

					$this->loadModel('Skill');

					$user_ids = explode(',', $this->request->data['user_ids']);

					$filter_text = $this->request->data['filter'];

					$skills = $this->Skill->find('list', [
						'conditions' => [
							'Skill.title LIKE' => "%$filter_text%",
						],
						'fields' => ['Skill.id', 'Skill.title'],
						'recursive' => -1,
					]);

					// pr($skills, 1);
					$skill_ids = (!empty($skills)) ? array_keys($skills) : null;

					// $skill_filtered = $this->User->getUsersBySkill( $filter_text, $skill_ids, $user_ids);

					$dummy = $this->User->getUsersBySkill($filter_text, $skill_ids, $user_ids);
					$skill_users = null;

					if (isset($skill_ids) && !empty($skill_ids)) {
						$dummy_user_id = Set::extract($dummy, '{n}.{0}.user_id');
						$dummy_fusers = Set::extract($dummy, '{n}.{0}.first_name');
						$dummy_lusers = Set::extract($dummy, '{n}.{0}.last_name');
						if (!empty($dummy_user_id)) {
							foreach ($dummy_user_id as $k => $v) {
								$skill_users[$v[0]] = $dummy_fusers[$k][0] . ' ' . $dummy_lusers[$k][0];
							}
						}
					} else {
						$dummy_user_id = Set::extract($dummy, '{n}.UserDetails.user_id');
						$dummy_fusers = Set::extract($dummy, '{n}.UserDetails.first_name');
						$dummy_lusers = Set::extract($dummy, '{n}.UserDetails.last_name');
						if (!empty($dummy_user_id)) {
							foreach ($dummy_user_id as $k => $v) {
								$skill_users[$v] = $dummy_fusers[$k] . ' ' . $dummy_lusers[$k];
							}
						}

					}

					$response['success'] = true;
					$response['content'] = $skill_users;
				} else {
					$project_id = $this->request->data['project_id'];
					$users_list = get_selected_user_project($this->user_id, $project_id);
					$response['success'] = true;
					$response['content'] = $users_list;
				}
			}

			echo json_encode($response);
			exit();
		}

	}

	public function countTotalElementParts($id, $type) {

		App::import("Model", "Vote");
		$vo = new Vote();
		$this->loadModel($type);

		$datas = $this->$type->find('count', array('conditions' => array($type . '.element_id' => $id)));

		if ($type == 'Feedback') {
			$datas = $this->$type->find('count', array('conditions' => ['element_id' => $id, 'end_date >=' => date('Y-m-d 00:00:00')]));
		}

		if ($type == 'Vote') {
			$datas = $vo->find('count', array('conditions' => ['element_id' => $id, 'end_date >=' => date('Y-m-d 00:00:00'), 'VoteQuestion.id !=' => '']));
		}

		$this->layout = false;
		$this->autorender = false;

		$dd = $datas ? $datas : 0;
		echo $dd;

		die;

	}

/*************************************Group Functions***********************************************/

	public function teams($project_id = null) {

		$this->layout = 'inner';
		$this->set('title_for_layout', 'Team');
		$this->set('page_heading', 'Project Center');
		$this->set('page_subheading', 'View all Projects you are involved in');

		App::import('Controller', 'Users');
		$Users = new UsersController;
		//echo $project_id; exit;
		$align_id = (isset($this->request->params['named']['align']) && !empty($this->request->params['named']['align'])) ? $this->request->params['named']['align'] : null;

		$data = $user_settings = null;
		$user_conditions = ['UserSetting.user_id' => $this->user_id];

		// if logged user has no settings for project center than insert new data
		if (!$this->UserSetting->hasAny($user_conditions)) {
			// Get default settings data
			$newSettings = $this->UserSetting->setSettings($this->user_id);
			// Insert into database
			$this->UserSetting->saveAll($newSettings);
		}

		$user_settings = $this->UserSetting->find('all', [
			'conditions' => $user_conditions,
			'recursive' => -1,
		]);

		if (isset($user_settings) && !empty($user_settings)) {
			$user_settings = Set::combine($user_settings, '{n}.UserSetting.slug', '{n}.UserSetting');
		}

		$aligneds = $this->Aligned->find("list", ['order' => ['Aligned.id ASC']]);
		$this->set(compact('aligneds'));

		$data['user_settings'] = $user_settings;
		$data['align_id'] = $align_id;

		if ($this->request->is('get') || $this->request->is('put')) {

			$myprojectlist = $myreceivedprojectlist = $mygroupprojectlist = array();
			// Find All current user's projects
			$myprojectlist = $Users->__myproject_selectbox($this->user_id, $align_id);
			// Find All current user's received projects
			$myreceivedprojectlist = $Users->__receivedproject_selectbox($this->user_id, 1, $align_id);
			// Find All current user's group projects
			$mygroupprojectlist = $Users->__groupproject_selectbox($this->user_id, 1, $align_id);

			if (is_array($myprojectlist)) {
				$projects1 = $myprojectlist;
			}

			if (is_array($mygroupprojectlist)) {
				$projects1 = array_replace($mygroupprojectlist, $projects1);
			} else {
				$projects1 = $projects1;
			}

			if (is_array($myreceivedprojectlist) && is_array($projects1)) {
				$projects1 = array_replace($myreceivedprojectlist, $projects1);
			} else {
				$projects1 = $projects1;
			}

			$projects = array_map("strip_tags", $projects1);
			$projects = array_map("trim", $projects);
			natcasesort($projects);

		} else {

			$myprojectlist = $myreceivedprojectlist = $mygroupprojectlist = array();
			// Find All current user's projects
			$myprojectlist = $Users->__myproject_selectbox($this->user_id);
			// Find All current user's received projects
			$myreceivedprojectlist = $Users->__receivedproject_selectbox($this->user_id, 1);
			// Find All current user's group projects
			$mygroupprojectlist = $Users->__groupproject_selectbox($this->user_id, 1);

			if (is_array($myprojectlist)) {
				$projects1 = $myprojectlist;
			}

			if (is_array($mygroupprojectlist)) {
				$projects1 = array_replace($mygroupprojectlist, $projects1);
			} else {
				$projects1 = $projects1;
			}

			if (is_array($myreceivedprojectlist) && is_array($projects1)) {
				$projects1 = array_replace($myreceivedprojectlist, $projects1);
			} else {
				$projects1 = $projects1;
			}

			$projects = array_map("strip_tags", $projects1);
			$projects = array_map("trim", $projects);
			natcasesort($projects);
		}

		$crumb = null;
		if (isset($project_id) && !empty($project_id)) {
			$project_data = $this->Project->findById($project_id);
			// pr($project_data, 1);
			if (isset($project_data['Project']) && !empty($project_data['Project'])) {
				$this->set('page_heading', $project_data['Project']['title']);
			}

			$crumb = [
				'Summary' => [
					'data' => [
						'url' => '/projects/index/' . $project_id,
						'class' => 'tipText',
						'title' => $project_data['Project']['title'],
						'data-original-title' => $project_data['Project']['title'],
					],
				],
				'last' => [
					'data' => [
						'title' => 'Project Center',
						'data-original-title' => 'Project Center',
					],
				],
			];
		}

		$this->set('projects', $projects);
		$this->set('align_id', $align_id);
		$this->set('project_id', $project_id);
		$this->set('crumb', $crumb);

	}

	public function user_elements() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$response = [
				'success' => false,
				'msg' => null,
				'content' => null,
			];
			$elements = null;
			$project_id = null;
			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->data;

				if ((isset($post['project_id']) && !empty($post['project_id'])) && (isset($post['user_id']) && !empty($post['user_id']))) {
					$elements = $this->ElementPermission->find('all', [
						'conditions' => [
							'ElementPermission.project_id' => $post['project_id'],
							'ElementPermission.user_id' => $post['user_id'],
							'Element.id !=' => '',
						],
						'fields' => ['ElementPermission.element_id', 'Element.id'],
						'recursive' => 1,
					]);

					if (isset($elements) && !empty($elements)) {
						$elements = Set::extract($elements, '/ElementPermission/element_id');
					}

					$project_id = $post['project_id'];
					$user_id = $post['user_id'];
				}
				// pr($post, 1);
			}

			//pr($elements);
			//exit;

			$view = new View($this, false);
			$view->viewPath = 'Shares/partials/resources'; // Directory inside view directory to search for .ctp files
			$view->layout = false; // if you want to disable layout
			$view->set('project_id', $project_id);
			$view->set('user_id', $user_id);
			$view->set('data', $elements);

			$html = $view->render('user_elements');

			echo json_encode($html);
			exit;

		}

	}

	public function element_users() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$response = [
				'success' => false,
				'msg' => null,
				'content' => null,
			];
			$element_id = $project_id = null;
			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->data;

				if ((isset($post['project_id']) && !empty($post['project_id'])) && (isset($post['element_id']) && !empty($post['element_id']))) {

					$project_id = $post['project_id'];
					$element_id = $post['element_id'];

				}

			}

			$view = new View($this, false);
			$view->viewPath = 'Shares/partials/resources'; // Directory inside view directory to search for .ctp files
			$view->layout = false; // if you want to disable layout
			$view->set('project_id', $project_id);
			$view->set('element_id', $element_id);

			$html = $view->render('element_users');

			echo json_encode($html);
			exit;

		}

	}

	public function team_user_list() {
		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$response = [
				'success' => false,
				'msg' => null,
				'content' => null,
			];
			$project_id = null;
			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->data;

				if (isset($post['project_id']) && !empty($post['project_id'])) {

					$project_id = $post['project_id'];

				}

			}

			$this->set('project_id', $project_id);

			$view = new View($this, false);
			$view->viewPath = 'Shares/partials/resources'; // Directory inside view directory to search for .ctp files
			$view->layout = false; // if you want to disable layout

			$html = $view->render('team_user_list');

			echo json_encode($html);
			exit;

		}

	}

	// Skill will download from edit user page
	public function download_skills($id = null, $user_id = null) {
		$this->autoRender = false;
		if (isset($id) && !empty($id)) {

			// Retrieve the file ready for download
			$data = $this->SkillPdf->find('first', ['conditions' => ['SkillPdf.id' => $id]]);

			if (empty($data)) {
				throw new NotFoundException();
			}

			$response = [
				'success' => false,
				'content' => null,
			];

			if (file_exists(SKILL_PDF_PATH . $user_id . DS . $data['SkillPdf']['pdf_name'])) {
				if (isset($data) && !empty($data)) {
					// Send file as response
					$response['content'] = SKILL_PDF_PATH . $user_id . DS . $data['SkillPdf']['pdf_name'];
					$response['success'] = true;
					return $this->response->file($response['content'], array('download' => true));
				}
			} else {
				return false;
			}
		}
	}

	/*========================================================================================*/

	public function delete_an_item($project_id = null, $group_id = null) {

		if ($this->request->isAjax()) {

			$this->layout = false;
			$viewData = null;
			$viewData['project_id'] = $project_id;
			$viewData['group_id'] = $group_id;

			$this->set($viewData);
			$this->render('/Shares/partials/delete_an_item');

		}
	}

	/*
		 * @name  	show_behaviors: set Users for behaviours
		 * @access	public
		 * @package  App/Controller/SharesController
	*/
	public function show_behaviors() {
		if ($this->request->isAjax()) {
			$this->layout = false;
			$viewData = null;
			$response = [
				'success' => false,
				'content' => null,
			];
			if( isset($_POST['user_id']) && !empty($_POST['user_id']) ){
				$viewData['user_id'] = $_POST['user_id'];
				$this->set('user_id',$viewData);
				$view = new View($this, false);
				$view->viewPath = 'Shares/partials'; // Directory inside view directory to search for .ctp files
				$view->layout = false; // if you want to disable layout
				$response['success'] = true;
				$html = $view->render('show_behaviors');
				echo json_encode($html);
				exit;
			}
		}
	}

}
