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
class ApiRisksController extends AppController {

	var $name = 'ApiRisks';

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
		$this->Auth->allow('get_risk_details', 'create');
		$view = new View();
		$this->objView = $view;
		$this->live_setting = LIVE_SETTING;
		$response = $this->api_check_user();
		$this->renderApiResponse($response);
	}

	public function get_risk_details() {
		$response = array();
		$data = array();
		$risks = array();
		$project_id = (!empty($this->request->data['project_id']) ? $this->request->data['project_id'] : (!empty($this->request->query['project_id']) ? $this->request->query['project_id'] : ''));
		$user_id = (!empty($this->request->data['user_id']) ? $this->request->data['user_id'] : (!empty($this->request->query['user_id']) ? $this->request->query['user_id'] : ''));
		$rIds 			=   ( !empty($this->request->data['risk_id']) ? explode(",",$this->request->data['risk_id'])  : ( !empty($this->request->query['risk_id']) ? explode(",",$this->request->query['risk_id']) : '' ) );
		$staticRisksProbs = array(0 => 'None', '1' => 'Rare', 2 => 'Unlikely', 3 => 'Possible', 4 => 'Likely', 5 => 'Almost Certain');
		$staticRisksImpact = array(0 => 'None', '1' => 'Negligible', 2 => 'Minor', 3 => 'Moderate', 4 => 'Major', 5 => 'Critical');
		$message = "Risk not found";
		
		
		if (empty($project_id)) 
		{
			$statusCode = 950;
			$message = "Risk\'s Project Id is missing.";
		} 
		else 
		{
			
			$statusCode = 200;
			$this->loadModel('RmDetail');
			$this->loadModel('RmElement');
			$this->loadModel('RmUser');
			$this->loadModel('UserDetail');
			$this->loadModel('Project');
			$this->loadModel('UserProject');
			$this->loadModel('UserDetail');
			$this->loadModel('User');
			$this->loadModel('Workspace');
			$this->loadModel('RmExposeResponse');
			$this->Project->bindModel(array('belongsTo' => array('Currency' => array('className' => 'Currency'), 'Category' => array('className' => 'Category'), 'Aligned' => array('className' => 'Aligned'))));
			$global_project =    $this->Project->findById($project_id);
			$risks['Project'] = $global_project ['Project'];
			if( !empty($user_id))
			{
				$conditions = array('RmDetail.user_id' => $user_id,'RmDetail.project_id' => $project_id) ;
				
			}
			else 
			{
				$conditions = array('RmDetail.project_id' => $project_id) ;
			}
			
			if( !empty($rIds)) 
			{
				if(count($rIds)==1)
				{
					$conditions["RmDetail.id "] = $rIds[0] ;	
				}
				else
				{
					$conditions["RmDetail.id IN"] = $rIds ;
				}
				
			}
			$RmDetail = $this->RmDetail->find('all', array('conditions' => $conditions ,'recursive' => -1));
			$userdata = array();
			if( !empty($user_id))
			{
				$userdata = $this->RmUser->find('all', array('conditions' => array('RmUser.user_id' => $user_id),'group' => 'RmUser.rm_detail_id'));
			}
			else
			{
				$userdata = $this->RmUser->find('all', array('group' => 'RmUser.rm_detail_id'));
			}
			
			if (isset($userdata) && !empty($userdata)) 
			{
				$rk_ids = Set::extract($userdata, '/RmUser/rm_detail_id');
				
				$userRmDetail = $this->RmDetail->find('all', [
					'conditions' => [
						'RmDetail.id' => $rk_ids,
						'RmDetail.project_id' => $project_id,
					]
				]);
				$RmDetail = array_merge($RmDetail, $userRmDetail);
			}
			$riskData = $RmDetail;
			
			
			foreach ($riskData as $key => $risk) 
			{
				$exposures = $this->RmExposeResponse->find('all', array('conditions' => array('RmExposeResponse.rm_detail_id' => $risk['RmDetail']['id']), 'fields' => array('RmExposeResponse.impact', 'RmExposeResponse.percentage', 'RmExposeResponse.mitigation_user_id', 'RmExposeResponse.mitigation', 'RmExposeResponse.contingency_user_id', 'RmExposeResponse.contingency', 'RmExposeResponse.residual', 'RmExposeResponse.residual_user_id')));
				if (!empty($exposures)) 
				{
					$exposureData = calculate_exposer($exposures[0]['RmExposeResponse']['impact'], $exposures[0]['RmExposeResponse']['percentage']);
					$exposure = $exposureData['text'];

				}
				$risks[$key]['RmDetail'] = array(
					'id' => $risk['RmDetail']['id'],'user_id' => $risk['RmDetail']['user_id'],
					'title' => $risk['RmDetail']['title'],
					'description' => $risk['RmDetail']['description'],
					'possible_occurrence' => date('Y-m-d', strtotime($risk['RmDetail']['possible_occurrence'])),
					'created' => date('Y-m-d', strtotime($risk['RmDetail']['created'])),
					'modified' => date('Y-m-d', strtotime($risk['RmDetail']['modified'])),
					'status' => ($risk['RmDetail']['status'] == 1 ? "Open" : ($risk['RmDetail']['status'] == 2 ? "Review" : ($risk['RmDetail']['status'] == 3 ? "SignOff" : "Overdue"))),

				);
				if (!empty($exposure)) 
				{
					$risks[$key]['RmDetail']['exposure'] = $exposure;
					if (!empty($exposures[0]['RmExposeResponse']['impact'])) 
					{
						$risks[$key]['RmDetail']['impact'] = $staticRisksImpact[$exposures[0]['RmExposeResponse']['impact']];
					}

					if (!empty($exposures[0]['RmExposeResponse']['percentage'])) 
					{
						$risks[$key]['RmDetail']['prob'] = $staticRisksImpact[$exposures[0]['RmExposeResponse']['percentage']];
					}

				}

				if (!empty($exposures) && !empty($exposures[0]['RmExposeResponse']['mitigation_user_id']) && !empty($exposures[0]['RmExposeResponse']['mitigation'])) 
				{
					$risks[$key]['RmDetail']['mitigation'] = $exposures[0]['RmExposeResponse']['mitigation'];
					$risks[$key]['RmDetail']['mitigation_user'] = array();

					$mitigationUserData = $this->User->find('all', array('conditions' => array('User.id' => $exposures[0]['RmExposeResponse']['mitigation_user_id']), 'fields' => array('User.id,User.email,UserDetail.first_name,UserDetail.last_name')));
					$risks[$key]['RmDetail']['mitigation_user'] = array('id' => $mitigationUserData[0]['User']['id'], 'email' => $mitigationUserData[0]['User']['email'], 'first_name' => $mitigationUserData[0]['UserDetail']['first_name'], 'last_name' => $mitigationUserData[0]['UserDetail']['last_name']);
				}
				if (!empty($exposures) && !empty($exposures[0]['RmExposeResponse']['contingency_user_id']) && !empty($exposures[0]['RmExposeResponse']['contingency'])) 
				{
					$risks[$key]['RmDetail']['contingency'] = $exposures[0]['RmExposeResponse']['contingency'];
					$risks[$key]['RmDetail']['contingency_user'] = array();

					$contingencyUserData = $this->User->find('all', array('conditions' => array('User.id' => $exposures[0]['RmExposeResponse']['contingency_user_id']), 'fields' => array('User.id,User.email,UserDetail.first_name,UserDetail.last_name')));
					$risks[$key]['RmDetail']['contingency_user'] = array('id' => $contingencyUserData[0]['User']['id'], 'email' => $contingencyUserData[0]['User']['email'], 'first_name' => $contingencyUserData[0]['UserDetail']['first_name'], 'last_name' => $contingencyUserData[0]['UserDetail']['last_name']);
				}
				if (!empty($exposures) && !empty($exposures[0]['RmExposeResponse']['residual_user_id']) && !empty($exposures[0]['RmExposeResponse']['residual'])) 
				{
					$risks[$key]['RmDetail']['residual'] = $exposures[0]['RmExposeResponse']['residual'];
					$risks[$key]['RmDetail']['residual_user'] = array();

					$residualUserData = $this->User->find('all', array('conditions' => array('User.id' => $exposures[0]['RmExposeResponse']['residual_user_id']), 'fields' => array('User.id,User.email,UserDetail.first_name,UserDetail.last_name')));
					$risks[$key]['RmDetail']['residual_user'] = array('id' => $residualUserData[0]['User']['id'], 'email' => $residualUserData[0]['User']['email'], 'first_name' => $residualUserData[0]['UserDetail']['first_name'], 'last_name' => $residualUserData[0]['UserDetail']['last_name']);
				}
				$joins = array(
					array(
						'table' => 'user_details',
						'alias' => 'UserDetail',
						'type' => 'INNER',
						'conditions' => array(
							'UserDetail.user_id = RmUser.user_id',
						),
						array(
							'table' => 'users',
							'alias' => 'User',
							'type' => 'INNER',
							'conditions' => array(
								'User.id = RmUser.user_id',
							),
						),
					));
				$rmUserData = $this->RmUser->find('all', array('conditions' => array('RmUser.rm_detail_id' => $risk['RmDetail']['id']), 'joins' => $joins, 'fields' => array('User.id,User.email,UserDetail.first_name,UserDetail.last_name')));
				if (!empty($rmUserData)) 
				{
					$risks[$key]['AssignedUsers'] = array();
					foreach ($rmUserData as $userKey => $userData) 
					{
						$risks[$key]['AssignedUsers'][$userKey] = array('id' => $userData['User']['id'], 'email' => $userData['User']['email'], 'first_name' => $userData['UserDetail']['first_name'], 'last_name' => $userData['UserDetail']['last_name']);
					}
				}
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
	 
	  
				$userId = $risk['RmDetail']['user_id'];
				$user = $this->UserDetail->find('first',array('conditions' => array('UserDetail.user_id' => $userId), 'joins' => $joins, 'fields' => array('User.email','UserDetail.first_name', 'UserDetail.last_name')));
				
				
				$user['UserDetail']['email'] = $user['User']['email'];
				$risks[$key]['RiskLeader'] = $user['UserDetail'];

				$project_id = $risk['RmDetail']['project_id'];

				$elements = $this->RmElement->find('all', array('conditions' => array('RmElement.rm_detail_id' => $risk['RmDetail']['id'], 'RmElement.project_id' => $project_id)));
				if (!empty($elements)) 
				{
					$message = 'Element Found.';
					$risks[$key]['Element'] = array();
					foreach ($elements as $elementKey => $element) 
					{
						$element_id = $element['RmElement']['element_id'];
						$risks[$key]['Element'][$elementKey] = array(
							'title' => $element['Element']['title'],
							'description' => $element['Element']['description'],
							'comments' => $element['Element']['comments'],
							'current_status' => ($element['Element']['sign_off'] == 1 ? "Sign Off" : ($element['Element']['sign_off'] !== 1 ? get_element_activities($element_id) : "Open")),
							'status' => elementStatus($element['Element']['start_date'], $element['Element']['end_date'], $element_id, $element['Element']['sign_off']),
							'created' => date('Y-m-d', strtotime($element['Element']['created'])),
							'modified' => date('Y-m-d', strtotime($element['Element']['modified'])),
							'start_date' => (!empty($element['Element']['start_date']) ? date('Y-m-d', strtotime($element['Element']['start_date'])) : 'N/A'),
							'end_date' => (!empty($element['Element']['end_date']) ? date('Y-m-d', strtotime($element['Element']['end_date'])) : 'N/A'),
							'date_constraints' => ($element['Element']['date_constraints'] == 1 ? 'true' : 'false'));
					}
				

					/*$this->Project->bindModel(array('belongsTo' => array('Currency' => array('className' => 'Currency'), 'Category' => array('className' => 'Category'), 'Aligned' => array('className' => 'Aligned'))));
					$project = $this->Project->findById($project_id);
					$user = $this->User->find('all', array('fields' => array('User.id', 'User.email', 'UserDetail.first_name', 'UserDetail.last_name', 'UserDetail.contact', 'UserDetail.profile_pic'), 'conditions' => array('User.id' => $project['UserProject'][0]['user_id'])));
					//$this->User->unbindModel(array('belongsTo'=>array('UserInstitution','OrganisationUser','ProjectPermission','WorkspacePermission','ElementPermission','UserProject','UserPlan','UserSetting','Skill')));
					$project['User'] = $user[0]['User'];
					$project['Workspaces'] = array();
					$workSpIds = array();
					foreach ($project['ProjectWorkspace'] as $projWorkspace) {
						$workSpIds[] = $projWorkspace['workspace_id'];
					}
					$this->Workspace->unbindModel(array('hasMany' => array('ProjectWorkspace', 'Area', 'ElementPermission')));
					$workspaces = $this->Workspace->find('all', array('fields' => array('Workspace.id', 'Workspace.title', 'Workspace.description'), 'conditions' => array('Workspace.id' => $workSpIds)));
					$project['Workspaces'] = array();
	
					foreach ($workspaces as $worksp) 
					{
						$project['Workspaces'][] = $worksp['Workspace'];
					}
					$project['Creator'] = array(
						'first_name' => $user[0]['UserDetail']['first_name'],
						'last_name' => $user[0]['UserDetail']['last_name'],
						'email' => $user[0]['User']['email'],
						'contact' => $user[0]['UserDetail']['contact'],
						'profile_pic' => $user[0]['UserDetail']['profile_pic']);
	
					$sharers = participants($project_id, $user[0]['User']['id']);
					//$sharers = array(1,25);
					if (!empty($sharers)) {
						$sharersData = $this->User->find('all', array('fields' => array('UserDetail.first_name', 'UserDetail.last_name', 'UserDetail.contact', 'UserDetail.profile_pic', 'User.email'), 'conditions' => array('User.id' => array_unique($sharers))));
	
						foreach ($sharersData as $sharerData) {
							$project['Sharers'][] = array(
								'first_name' => $sharerData['UserDetail']['first_name'],
								'last_name' => $sharerData['UserDetail']['last_name'],
								'email' => $sharerData['User']['email'],
								'contact' => $sharerData['UserDetail']['contact'],
								'profile_pic' => $sharerData['UserDetail']['profile_pic']);
						}
					}
	
					$owners = participants_owners($project_id, $user[0]['User']['id']);
	
					if (!empty($owners)) {
						$ownersData = $this->User->find('all', array('fields' => array('UserDetail.first_name', 'UserDetail.last_name', 'UserDetail.contact', 'UserDetail.profile_pic', 'User.email'), 'conditions' => array('User.id' => array_unique($owners))));
	
						foreach ($ownersData as $ownerData) {
							$project['Owners'][] = array(
								'first_name' => $ownerData['UserDetail']['first_name'],
								'last_name' => $ownerData['UserDetail']['last_name'],
								'email' => $ownerData['User']['email'],
								'contact' => $ownerData['UserDetail']['contact'],
								'profile_pic' => $ownerData['UserDetail']['profile_pic']);
						}
					}
	
					$groupOwners = participants_group_owner($project_id);
					if (!empty($groupOwners)) {
						$groupOwnersData = $this->User->find('all', array('fields' => array('UserDetail.first_name', 'UserDetail.last_name', 'UserDetail.contact', 'UserDetail.profile_pic', 'User.email'), 'conditions' => array('User.id' => array_unique($groupOwners))));
						foreach ($groupOwnersData as $groupOwnerData) {
							$project['GroupOwners'][] = array(
								'first_name' => $groupOwnerData['UserDetail']['first_name'],
								'last_name' => $groupOwnerData['UserDetail']['last_name'],
								'email' => $groupOwnerData['User']['email'],
								'contact' => $groupOwnerData['UserDetail']['contact'],
								'profile_pic' => $groupOwnerData['UserDetail']['profile_pic']);
						}
					}
					$groupSharers = participants_group_sharer($project_id);
					if (!empty($groupSharers)) {
						$groupSharersData = $this->User->find('all', array('fields' => array('UserDetail.first_name', 'UserDetail.last_name', 'UserDetail.contact', 'UserDetail.profile_pic', 'User.email'), 'conditions' => array('User.id' => array_unique($groupSharers))));
						foreach ($groupSharersData as $groupSharerData) {
							$project['GroupSharers'][] = array(
								'first_name' => $groupSharerData['UserDetail']['first_name'],
								'last_name' => $groupSharerData['UserDetail']['last_name'],
								'email' => $groupSharerData['User']['email'],
								'contact' => $groupSharerData['UserDetail']['contact'],
								'profile_pic' => $groupSharerData['UserDetail']['profile_pic']);
						}
					}
					$project['Project']['rag_current_status'] = ($project['Project']['rag_current_status'] == 1 ? "red" : ($project['Project']['rag_current_status'] == 2 ? "Amber" : "Green"));
					$project['Project']['current_status'] = ($project['Project']['sign_off'] == 1 ? "Sign Off" : ($project['Project']['sign_off'] !== 1 ? get_project_activities($project_id) : "Open"));
					$project['Project']['status'] = projectStatus($project['Project']['start_date'], $project['Project']['end_date'], $project_id, $project['Project']['sign_off']);
					$project['Project']['created'] = date('Y-m-d', $project['Project']['created']);
					$project['Project']['modified'] = date('Y-m-d', $project['Project']['modified']);
	
					$project['Project']['start_date'] = date('Y-m-d', strtotime($project['Project']['start_date']));
					$project['Project']['end_date'] = date('Y-m-d', strtotime($project['Project']['end_date']));
					if (!empty($project['ProjectSkill'])) {
						foreach ($project['ProjectSkill'] as $projectSkill) {
							$project['Skills'][] = $this->Skill->find('all', array('conditions' => array('Skill.id' => $projectSkill['skill_id']), 'fields' => array('Skill.title')));
						}
					}
					unset($project['ProjectSkill']);
					$keysArray = array(
						'Project' => array('id', 'title', 'objective', 'description', 'rag_current_status', 'created', 'modified', 'start_date', 'end_date', 'current_status', 'color_code', 'status'),
						'Category' => array('id', 'title'),
						'Currency' => array('id', 'name'),
						'Aligned' => array('id', 'title'),
					);
	
					$project = $this->filterFields($keysArray, $project);
					unset($risks[$key]['Project']);
					$risks[$key]['Project'] = $project['Project'];
					$risks[$key]['Project']['Category'] = $project['Category'];
					$risks[$key]['Project']['Currency'] = $project['Currency'];
					$risks[$key]['Project']['Aligned'] = $project['Aligned'];
					if (!empty($project['Workspaces'])) {
						$risks[$key]['Project']['Workspaces'] = $project['Workspaces'];
					}
	
					if (!empty($project['Creator'])) {
						$risks[$key]['Project']['Creator'] = $project['Creator'];
					}
	
					if (!empty($project['Sharers'])) {
						$risks[$key]['Project']['Sharers'] = $project['Sharers'];
					}
	
					if (!empty($project['GroupSharers'])) {
						$risks[$key]['Project']['GroupSharers'] = $project['GroupSharers'];
					}
	
					if (!empty($project['Owners'])) {
						$risks[$key]['Project']['Owners'] = $project['Owners'];
					}*/
				}
				else
				{
					$message = "Risk found";
				}
			}
			

		}
		$this->set([
			'message' => $message,
			'statusCode' => $statusCode,
			'data' => $risks,
			'_serialize' => ['statusCode', 'message', 'data'],
		]);
	}
	private function project_risk_types($project_id) {
		$this->loadModel('RmProjectRiskType');
		$this->loadModel('RmRiskType');
		$project_risks = $this->RmProjectRiskType->find('all', [
			'conditions' => [
				'RmProjectRiskType.project_id' => $project_id,
				'RmProjectRiskType.user_id IS NULL',
			],
		]);
		if (!isset($project_risks) || empty($project_risks)) {
			$RmRiskType = $this->RmRiskType->find('all', [
				'conditions' => [
					'RmRiskType.status' => 1,
				],
			]);

			if (isset($RmRiskType) && !empty($RmRiskType)) {
				$riskTypes = null;
				foreach ($RmRiskType as $key => $value) {
					$riskTypes[]['RmProjectRiskType'] = [
						'title' => $value['RmRiskType']['title'],
						'project_id' => $project_id,
					];
				}
				$this->RmProjectRiskType->saveAll($riskTypes);
			}

		}

		$project_risks = $this->RmProjectRiskType->find('all', [
			'conditions' => [
				'RmProjectRiskType.project_id' => $project_id,
			],
			'recursive' => -1,
		]);

		$data = array();
		if (!empty($project_risks)) {
			foreach ($project_risks as $project_risk) {
				$data[] = $project_risk['RmProjectRiskType']['id'];
			}
		}
		return $data;
	}
	private function get_risk_users($risk_id, $user_id) {

		$risk_users = $this->RmUser->find('all', [
			'conditions' => [
				'RmUser.rm_detail_id' => $risk_id,
				'RmUser.user_id !=' => $user_id,
			],
			'recursive' => -1,
		]);

		return (isset($risk_users) && !empty($risk_users)) ? $risk_users : false;
	}
	private function get_project_users($project_id, $user_id = null) {

		$project_users = [];
		$user_id = (isset($user_id) && !empty($user_id)) ? $user_id : $this->user_id;
		$view = new View();
		$objView = $view;
		$owner = $objView->loadHelper('Common')->ProjectOwner($project_id, $user_id);

		if (!empty($owner)) {

			$owner = $owner['User']['id'];

			$participantsGpOwner = participants_group_owner($project_id);

			$participantsGpSharer = participants_group_sharer($project_id);

			$participants_owners = participants_owners($project_id, $owner);

			$participants = participants($project_id, $owner);

			$participants = isset($participants) ? array_filter($participants) : $participants;
			// $participants = (is_array($participants)) ? $participants : array($participants);

			$participants_owners = isset($participants_owners) ? array_filter($participants_owners) : $participants_owners;
			// $participants_owners = (is_array($participants_owners)) ? $participants_owners : array($participants_owners);


			$participantsGpOwner = isset($participantsGpOwner) ? array_filter($participantsGpOwner) : $participantsGpOwner;
			// $participantsGpOwner = (is_array($participantsGpOwner)) ? $participantsGpOwner : array($participantsGpOwner);

			$participantsGpSharer = isset($participantsGpSharer) ? array_filter($participantsGpSharer) : $participantsGpSharer;
			// $participantsGpSharer = (is_array($participantsGpSharer)) ? $participantsGpSharer : array($participantsGpSharer);

			if (is_array($participants)) {
				$project_users = array_merge($project_users, $participants);
			}
			if (is_array($participants_owners)) {
				$project_users = array_merge($project_users, $participants_owners);
			}
			if (is_array($participantsGpOwner)) {
				$project_users = array_merge($project_users, $participantsGpOwner);
			}
			if (is_array($participantsGpSharer)) {
				$project_users = array_merge($project_users, $participantsGpSharer);
			}

		}

		return $project_users;
	}
	private function get_project_elements($project_id, $user_id = null) {

		$e_permission = null;
		$project_level = 0;

		$p_permission = $this->objView->loadHelper('Common')->project_permission_details($project_id, $user_id);
		$user_project = $this->objView->loadHelper('Common')->userproject($project_id, $user_id);

		$group_id = $this->objView->loadHelper('Group')->GroupIDbyUserID($project_id, $user_id);

		$project_workspace = get_project_workspace($project_id, true);

		if (isset($project_workspace) && !empty($project_workspace)) {

			$project_workspaces = array_keys($project_workspace);

			foreach ($project_workspaces as $key => $workspace_id) {

				$wsp_permission = $this->objView->loadHelper('Common')->wsp_permission_details($this->objView->loadHelper('ViewModel')->workspace_pwid($workspace_id), $project_id, $user_id);

				if (isset($group_id) && !empty($group_id)) {

					$group_permission = $this->objView->loadHelper('Group')->group_permission_details($project_id, $group_id);

					if (isset($group_permission['ProjectPermission']['project_level']) && $group_permission['ProjectPermission']['project_level'] == 1) {
						$project_level = $group_permission['ProjectPermission']['project_level'];
					}
					$wsp_permission = $this->objView->loadHelper('Group')->group_wsp_permission_details($this->objView->loadHelper('ViewModel')->workspace_pwid($workspace_id), $project_id, $group_id);
				}

				if ((isset($user_project) && !empty($user_project)) || (isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level'] == 1) || (isset($project_level) && $project_level == 1)) {
					$e_permission = $this->objView->loadHelper('ViewModel')->project_elements($project_id);
					if (isset($e_permission) && !empty($e_permission)) {
						$e_permission = Set::extract($e_permission, '/element/id');
					}

				} else if (isset($wsp_permission) && !empty($wsp_permission)) {
					$e_permission = $this->objView->loadHelper('Common')->element_permission_details($workspace_id, $project_id, $user_id);

					if ((isset($group_id) && !empty($group_id))) {

						if (isset($e_permission) && !empty($e_permission)) {
							$ge_permissions = $this->objView->loadHelper('Group')->group_element_permission_details($workspace_id, $project_id, $group_id);
							$e_permission = array_merge($e_permission, $ge_permissions);
						} else {
							$e_permission = $this->objView->loadHelper('Group')->group_element_permission_details($workspace_id, $project_id, $group_id);
						}
					}
				}
			}
		}
		// pr($e_permission, 1);
		return $e_permission;
	}
	private function notificationEmail($user_id = null, $risk_id = null, $project_id = null, $email_type = 'risk_assignment') {
		// return true;
		if (isset($risk_id) && !empty($risk_id) && isset($user_id) && !empty($user_id) && isset($project_id) && !empty($project_id)) {
			$this->loadModel('User');
			$this->loadModel('UserDetail');
			$this->loadModel('Project');
			$this->loadModel('RmDetail');
			$this->loadModel('EmailNotification');

			//========== Logged in user===========
			$loginuse = $this->User->findById($user_id);
			$loggedInUser = $loginuse['UserDetail']['first_name'] . ' ' . $loginuse['UserDetail']['last_name'];

			//========== user which will get email ===========
			$usersDetails = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
			$send_to = $usersDetails['UserDetail']['first_name'] . ' ' . $usersDetails['UserDetail']['last_name'];

			//========== Project Detail ===========
			$projectDetails = $this->Project->find('first', array('conditions' => array('Project.id' => $project_id)));

			//========== Riest Detail ===========
			$riskDetail = $this->RmDetail->find('first', array('conditions' => array('RmDetail.id' => $risk_id)));

			$checkNotification = $this->EmailNotification->find('first', ['conditions' => ['EmailNotification.notification_type' => 'riskcenter', 'EmailNotification.personlization' => $email_type, 'EmailNotification.user_id' => $user_id]]);

			if ((!isset($checkNotification['EmailNotification']['email']) || $checkNotification['EmailNotification']['email'] == 1) && (!isset($usersDetails['User']['email_notification']) || $usersDetails['User']['email_notification'] == 1)) {

				$email = new CakeEmail();
				$email->config('Smtp');
				$email->from(array(ADMIN_FROM_EMAIL => SITENAME));
				$email->to($usersDetails['User']['email']);
				$email->emailFormat('html');

				if ($email_type == 'risk_assignment') {

					$email->subject(SITENAME . ': Risk assignment');
					$email->template('risk_assigned_email');
					$email->viewVars(array('risk_name' => $riskDetail['RmDetail']['title'], 'custname' => $send_to, 'changedBy' => $loggedInUser, 'projectName' => $projectDetails['Project']['title']));
					$email->send();

				}

				if ($email_type == 'risk_signedoff') {

					$email->subject(SITENAME . ': Risk signed-off');
					$email->template('risk_signedoff_email');
					$email->viewVars(array('risk_name' => $riskDetail['RmDetail']['title'], 'custname' => $send_to, 'changedBy' => $loggedInUser, 'projectName' => $projectDetails['Project']['title']));
					$email->send();
				}

				if ($email_type == 'risk_overdue') {

					$email->subject(SITENAME . ': Risk overdue');
					$email->template('risk_overdue_email');
					$email->viewVars(array('risk_name' => $riskDetail['RmDetail']['title'], 'custname' => $send_to, 'changedBy' => $loggedInUser, 'projectName' => $projectDetails['Project']['title']));
					$email->send();

				}

			}
			return true;
		}
	}
	public function create() {
		$response = array();
		$this->loadModel('RmProjectRiskType');
		$risk_id = 0;
		$data = array();
		$project_id = (!empty($this->request->data['project_id']) ? $this->request->data['project_id'] : (!empty($this->request->query['project_id']) ? $this->request->query['project_id'] : ''));
		$title = (!empty($this->request->data['title']) ? $this->request->data['title'] : (!empty($this->request->query['title']) ? $this->request->query['title'] : ''));
		$poso = (!empty($this->request->data['poso']) ? $this->request->data['poso'] : (!empty($this->request->query['poso']) ? $this->request->query['poso'] : ''));
		$desc = (!empty($this->request->data['desc']) ? $this->request->data['desc'] : (!empty($this->request->query['desc']) ? $this->request->query['desc'] : ''));
		$creator = (!empty($this->request->data['creator']) ? $this->request->data['creator'] : (!empty($this->request->query['creator']) ? $this->request->query['creator'] : ''));
		$risk_type_id = (!empty($this->request->data['risk_type_id']) ? $this->request->data['risk_type_id'] : (!empty($this->request->query['risk_type_id']) ? $this->request->query['risk_type_id'] : ''));
		$element_id = (!empty($this->request->data['element_id']) ? $this->request->data['element_id'] : (!empty($this->request->query['element_id']) ? $this->request->query['element_id'] : ''));
		
		$errors = array();
		if (empty($project_id)) {
			array_push($errors, "Risk's project id is missing");
		}
		if (empty($title)) {
			array_push($errors, "Risk's Title missing");
		}
		if (empty($poso)) {
			array_push($errors, "Risk's Possible Occurrence date is missing");
		} else if (!empty($poso)) {
			if ($poso <= date('Y-m-d')) {
				array_push($errors, "Risk's Possible Occurrence date must be bigger than to current date");
			}
		}
		if (empty($desc)) {
			array_push($errors, "Risk's description is missing");
		}
		if (empty($creator)) {
			array_push($errors, "Risk's creator email is missing");
		} else {
			$this->loadModel('User');
			$user = $this->User->findByEmail($creator);
			if (empty($user)) {
				array_push($errors, "No user exists with " . $creator . " email address");
			} else {
				$user_id = $user['User']['id'];
				$project_users = $this->get_project_users($project_id, $user_id);
				if (!empty($project_users)) {
					if (!in_array($user_id, $project_users)) {
						array_push($errors, $creator . " is not associated with this project");
					}
				} else {
					array_push($errors, $creator . " is not associated with this project");
				}
			}
		}

		if (empty($risk_type_id)) {
			array_push($errors, "Risk's Type id is missing");
		} else if (!empty($project_id) && !empty($risk_type_id)) {
			$project_risk_types = $this->project_risk_types($project_id);

			if (!in_array($risk_type_id, $project_risk_types)) {
				array_push($errors, " ( Risk Type Id : " . $risk_type_id . " not associated with this project ( Project Id : " . $project_id . " )  ");
			}
		} else {
			array_push($errors, " ( Risk's Type Id : " . $risk_type_id . " not associated with this project ( Project Id : " . $project_id . " )  ");
		}

		if (!empty($element_id) && !empty($project_id) && !empty($user_id)) {
			$project_elements = $this->get_project_elements($project_id, $user_id);
			if (empty($project_elements)) {
				array_push($errors, " No Element Found");
			} else if (!in_array($element_id, $project_elements)) {
				array_push($errors, " ( Element Id : " . $element_id . " not associated with this project ( Project Id : " . $project_id . " )  ");
				$project_elements = array();
			}
		}

		if (empty($errors)) {
			$riskData['user_id'] = $user_id;
			$riskData['created_by'] = $user_id;
			$riskData['project_id'] = $project_id;
			$riskData['title'] = $title;
			$riskData['rm_project_risk_type_id'] = $risk_type_id;
			$riskData['possible_occurrence'] = $poso;
			$riskData['description'] = $desc;
			$riskData['status'] = 1;
			$this->loadModel('RmDetail');

			if ($this->RmDetail->save($riskData)) {
				$risk_id = $this->RmDetail->getLastInsertId();
				
				if ($project_users) {
					/************** socket messages **************/
					if (SOCKET_MESSAGES) {
						$current_user_id = $user_id;
						$r_users = $project_users;
						$r_open_users = null;
						if (isset($r_users) && !empty($r_users)) {
							foreach ($r_users as $key => $value) {
								if (web_notify_setting($value, 'riskcenter', 'risk_assignment')) {
									$r_open_users[] = $value;
								}
							}
						}
						$userDetail = get_user_data($current_user_id);
						$content = [
							'notification' => [
								'type' => 'risk',
								'created_id' => $current_user_id,
								'project_id' => $riskData['project_id'],
								'creator_name' => $userDetail['UserDetail']['full_name'],
								'subject' => 'Risk assignment',
								'heading' => 'Risk: ' . strip_tags($riskData['title']),
								'sub_heading' => 'Project: ' . strip_tags(getFieldDetail('Project', $riskData['project_id'], 'title')),
								'date_time' => timezoneTimeConvertor($this->user_id, date('Y-m-d g:iA'), $format = 'Y-m-d g:iA') . user_country($current_user_id),
							],
							//'received_users' => array_values($ele_users),
						];
						if (is_array($r_open_users)) {
							$content['received_users'] = array_values($r_open_users);
						}
					}
					/************** socket messages **************/
					foreach ($project_users as $key => $value) {
						$riskUsers[]['RmUser'] = [
							'user_id' => $value,
							'rm_detail_id' => $risk_id,
						];
						//Risk assignment
						$this->notificationEmail($value, $risk_id, $riskData['project_id'], 'risk_assignment');

					}
					$this->loadModel('RmUser');
					$this->RmUser->saveAll($riskUsers);
				}
				if (!empty($project_elements)) {
					foreach ($project_elements as $key => $value) {
						$riskElements[]['RmElement'] = [
							'element_id' => $value,
							'rm_detail_id' => $risk_id,
							'project_id' => $project_id,
						];
					}
					$this->loadModel('RmElement');
					$this->RmElement->saveAll($riskElements);
				}
			}
		}
		$keysArray = array(
						'User' => array('id','email'),
						'Project' => array('id','title','description','objective','color_code','start_date','end_date')
						
				);	
				

		if( $risk_id > 0 )
		{
			$newData = $this->RmDetail->find('first', array('conditions'=>array('RmDetail.id' => $risk_id)));
			$newData = $this->filterFields($keysArray,$newData);
		}
		
				
		
		
		
		$this->set([
			'message' => (!empty($errors) ? $errors : 'Risks Created successfully'),
			'statusCode' => (!empty($errors) ? 950 : 200),
			'data' => ($risk_id > 0 ? $newData : array()),
			'_serialize' => ['statusCode', 'message', 'data'],
		]);
	}
}