<?php
App::uses('Controller/Component', 'Auth', 'Session', 'RequestHandler');
App::uses('Sanitize', 'Utility');
App::uses('CakeEmail', 'Network/Email');

class RisksController extends AppController {

	public $name = 'Risks';

	public $uses = [
		'User',
		'UserDetail',
		'Project',
		'Element',
		'RmRiskType',
		'RmProjectRiskType',
		'RmDetail',
		'RmUser',
		'RmLeader',
		'RmElement',
		'RmExposeResponse',
		'EmailNotification',
	];
	public $objView = null;
	public $user_id = null;
	public $paging_limit = null;
	public $risk_offset = 50;
	public $components = array(
		'Common',
	);
	public $helpers = array(
		'Html',
		'Form',
		'Session',
		'Time',
		'Text',
		'Common',
		'ViewModel',
	);

	public function beforeFilter() {
		parent::beforeFilter();

		$this->paging_limit = 10;

		$this->user_id = $this->Auth->user('id');
		$this->Auth->allow('emailNotificationOverdueCron', 'get_response');

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

	public function risk_center($project_id = null, $element_id = null, $my = null) {


		if (isset($project_id) && !empty($project_id) ) {
			if(!dbExists('Project', $project_id)){
				$this->redirect(array('controller' => 'risks', 'action' => 'index'));
			}
		}
		$this->layout = 'inner';
		$this->set('title_for_layout', __('Risk Center', true));
		$this->set('page_heading', __('Risk Center', true));
		$this->set('page_subheading', __('View Risks in your Projects', true));

		$viewData = [];
		$project_ids = [];
		$user_risks = user_owner_risks($this->user_id);
		// $user_risks = user_risks($this->user_id);

		$RmDetail = $this->RmDetail->find('all', [
			'conditions' => [
				'RmDetail.id' => $user_risks,
			],
			'recursive' => -1,
		]);

		if (isset($RmDetail) && !empty($RmDetail)) {
			$project_ids = Set::extract($RmDetail, '/RmDetail/project_id');
		}
		///////////////////////////////////////
		/*if (isset($project_id) && !empty($project_id)) {
			$projectRisks = [];
			foreach ($RmDetail as $key => $value) {
				if ($value['RmDetail']['project_id'] == $project_id) {
					$projectRisks[] = $value;
				}
			}
			$RmDetail = $projectRisks;
		}*/
		///////////////////////////////////////

		if (isset($element_id) && !empty($element_id)) {
			if (isset($RmDetail) && !empty($RmDetail)) {
				$rmid = Set::extract($RmDetail, '/RmDetail/id');
				$element_data = $this->RmElement->find('all', [
					'conditions' => [
						'RmElement.rm_detail_id' => $rmid,
						'RmElement.element_id' => $element_id,
					],
					'recursive' => -1,
				]);
				$elementRisks = [];
				if (isset($element_data) && !empty($element_data)) {
					$riskid = Set::extract($element_data, '/RmElement/rm_detail_id');
					foreach ($RmDetail as $key => $value) {
						if (in_array($value['RmDetail']['id'], $riskid)) {
							$elementRisks[] = $value;
						}
					}
					$RmDetail = $elementRisks;
				}
			}
		}

		if (isset($my) && !empty($my)) {
			$RmIDS = user_risks($this->user_id);
			$RmDetail = $this->RmDetail->find('all', [
				'conditions' => [
					'RmDetail.id' => $RmIDS,
				],
				'recursive' => -1,
			]);
		}
		$this->set('my', $my);

		// coming from gantt page
		$param_risk_id = null;
		if (isset($this->params['named']['wsp']) && !empty($this->params['named']['wsp'])) {
			if (isset($RmDetail) && !empty($RmDetail)) {
				$param_wsp_id = $this->params['named']['wsp'];
				$elementids = workspace_elements($param_wsp_id);
				if( isset($elementids) && !empty($elementids) ){
					$element_ids = Set::extract($elementids, '/Element/id');
					$rmid = Set::extract($RmDetail, '/RmDetail/id');

					$element_data = $this->RmElement->find('all', [
						'conditions' => [
							'RmElement.rm_detail_id' => $rmid,
							'RmElement.element_id' => $element_ids,
						],
						'recursive' => -1,
					]);
				// pr($element_data, 1);
					$elementRisks = [];
					if (isset($element_data) && !empty($element_data)) {
						$riskid = Set::extract($element_data, '/RmElement/rm_detail_id');
						foreach ($RmDetail as $key => $value) {
							if (in_array($value['RmDetail']['id'], $riskid)) {
								$elementRisks[] = $value;
							}
						}
						$RmDetail = $elementRisks;
					}
				}
			}
		}


		// coming from wsp page
		$param_risk_id = null;
		if (isset($this->params['named']['risk']) && !empty($this->params['named']['risk'])) {
			$param_risk_id = $this->params['named']['risk'];
			$RmDetail = $this->RmDetail->find('all', [
				'conditions' => [
					'RmDetail.id' => $param_risk_id,
				],
				'recursive' => -1,
			]);
		}

		if (isset($RmDetail) && !empty($RmDetail)) {
			$rm_ids = Set::extract($RmDetail, '/RmDetail/id');

			$this->set_overdue($rm_ids);
			$RmDetail = $this->RmDetail->find('all', [
				'conditions' => [
					'RmDetail.id' => $rm_ids,
				],
				'recursive' => -1,
			]);
			if (isset($RmDetail) && !empty($RmDetail)) {
				// $project_ids = Set::extract($RmDetail, '/RmDetail/project_id');
			}
		}

		$viewData['project_ids'] = (isset($project_ids) && !empty($project_ids)) ? array_unique($project_ids) : [];
		$projects = $this->Project->find('list', [
			'conditions' => [
				'id' => $project_ids,
			],
			'fields' => ['id', 'title'],
		]);

		$sorted_project = [];
		$program_param = false;
		if (isset($this->params['named']['program']) && !empty($this->params['named']['program'])) {
			$program_param = true;
		}

		$program_id = null;
		if (isset($this->params['named']['program']) && !empty($this->params['named']['program'])) {
			$program_id = $this->params['named']['program'];
			$program_projects = program_projects($this->params['named']['program']);
			if (isset($program_projects) && !empty($program_projects)) {
				$program_projects = Set::extract($program_projects, '/ProjectProgram/project_id');

				foreach ($projects as $key => $value) {
					if (in_array($key, $program_projects)) {
						$sorted_project[$key] = $value;
					}
				}
			}
		}
		// pr($program_projects, 1);
		$param_exposure = null;
		if (isset($sorted_project) && !empty($sorted_project)) {
			$projects = $sorted_project;

			$RmDetail = getUserRisksByProgram($this->user_id, array_keys($sorted_project));
			if (isset($this->params['named']['exposure']) && !empty($this->params['named']['exposure'])) {
				$param_exposure = $this->params['named']['exposure'];
				$RmDetail_temp = [];
				if(isset($RmDetail) && !empty($RmDetail)) {
					foreach ($RmDetail as $key => $value) {
						$exposer_data = risk_exposer($value['RmDetail']['id']);
				        $exposer = null;
				        if(isset($exposer_data) && !empty($exposer_data)) {
				            $exposer = calculate_exposer($exposer_data["RmExposeResponse"]["impact"], $exposer_data["RmExposeResponse"]["percentage"]);
				            if($param_exposure == strtolower($exposer["text"])) {
				            	$RmDetail_temp[] = $value;
				            }
				        }
					}
					$RmDetail = $RmDetail_temp;
				}
			}
		}

		if (isset($this->params['named']['exposure']) && !empty($this->params['named']['exposure'])) {
			$param_exposure = $this->params['named']['exposure'];
			$RmDetail_temp = [];
			if(isset($RmDetail) && !empty($RmDetail)) {
				foreach ($RmDetail as $key => $value) {
					$exposer_data = risk_exposer($value['RmDetail']['id']);
			        $exposer = null;
			        if(isset($exposer_data) && !empty($exposer_data)) {
			            $exposer = calculate_exposer($exposer_data["RmExposeResponse"]["impact"], $exposer_data["RmExposeResponse"]["percentage"]);
			            if($param_exposure == strtolower($exposer["text"])) {
			            	$RmDetail_temp[] = $value;
			            }
			        }
				}
				$RmDetail = $RmDetail_temp;
			}
		}
		$exposure_all = false;
		if ((isset($this->params['named']['exposure']) && !empty($this->params['named']['exposure'])) && (!isset($project_id) || empty($project_id))) {
			$exposure_all = true;
		}
		$viewData['exposure_all'] = $exposure_all;

		$viewData['projects'] = array_map(function ($v) {
			return trim(htmlentities($v));
		}, $projects);

		// sort all the risks by created date before send to view
		usort($RmDetail, function ($a, $b) {
			return $a['RmDetail']['created'] < $b['RmDetail']['created'];
		});

		if (isset($RmDetail) && !empty($RmDetail)) {
			$RmDetail = array_unique($RmDetail, SORT_REGULAR);
		}
		// pr($RmDetail, 1);
		$viewData['RmDetail'] = $RmDetail;
		$crumb = [
			'last' => [
				'data' => [
					'title' => 'Risk Center',
					'data-original-title' => 'Risk Center',
				],
			],
		];
		$this->set($viewData);
		$this->set('crumb', $crumb);

		$noRiskMessage = false;
		if ((isset($project_ids) && !empty($project_ids)) && (isset($project_id) && !empty($project_id))) {
			if (!in_array($project_id, $project_ids)) {
				$noRiskMessage = true;
			}
		}

		$this->set('program_param', $program_param);
		$this->setJsVar('noRiskMessage', $noRiskMessage);
		$this->set('project_id', $project_id);
		$this->setJsVar('project_id', $project_id);
		$this->set('param_risk_id', $param_risk_id);
		$this->setJsVar('param_risk_id', $param_risk_id);
		$passed_params = $this->params['named'];

		$this->setJsVar('passed_params', $passed_params);

		$risk_ids = Set::extract($RmDetail, '/RmDetail/id');

		$RmDetails = $this->RmDetail->find('all', [
			'conditions' => [
				'RmDetail.id' => $risk_ids
			],
			'recursive' => -1,
			'order' => ['RmDetail.status ASC'],
			'limit' => $this->paging_limit,
		]);
		$RmDetailCount = $this->RmDetail->find('count', [
			'conditions' => [
				'RmDetail.id' => $risk_ids
			],
			'recursive' => -1,
			'order' => ['RmDetail.status DESC']
		]);
		// pr($RmDetails, 1);
		$viewDatas['paging_limit'] = $this->paging_limit;
		$viewDatas['paging_current'] = 0;
		$viewDatas['paging_total'] = $RmDetailCount;
		$viewDatas['RmDetail'] = $RmDetails;
		$this->set( $viewDatas );
		$this->setJsVar('my_risk', $my);
		$this->setJsVar('risk_element_id', $element_id);
		if( isset($this->params['named']['wsp']) && !empty($this->params['named']['wsp']) ){
			$param_wsp_id = $this->params['named']['wsp'];
			$this->setJsVar('risk_wsp_id', $param_wsp_id);
		} else {
			$this->setJsVar('risk_wsp_id', null);
		}

		$this->setJsVar('risk_program_id', $program_id);
		$this->setJsVar('risk_program_exposure', $param_exposure);

		$this->setJsVar('all_project', '');
		if(isset($this->params['named']['project']) && !empty($this->params['named']['project'])){
			$this->setJsVar('all_project', $this->params['named']['project']);
		}
	}

	public function manage_risk($risk_id = null, $passed_project = null) {
		if (isset($risk_id) && !empty($risk_id) ) {
			if(!dbExists('RmDetail', $risk_id)){
				$this->redirect(array('controller' => 'risks', 'action' => 'index'));
			}
		}
		$this->layout = 'inner';

		if(isset($risk_id) && !empty($risk_id)){
			$this->set('title_for_layout', __('Edit  Risk', true));
			$this->set('page_heading', __('Edit  Risk', true));
			$risk_permit = risk_permit($risk_id);
			$risk_creator_id = $risk_permit[0]['rd']['creator_id'];
			$leaders = (!empty($risk_permit[0]['rleader']['leaders'])) ? json_decode($risk_permit[0]['rleader']['leaders'], true) : [];
			$leaders = (!empty($leaders))  ? Set::extract($leaders, '{n}.id') : [];
			if( $risk_creator_id != $this->user_id && $risk_permit[0][0]['rd_status'] != 'Completed' && !in_array($this->user_id, $leaders)){
				$this->redirect(array('controller' => 'risks', 'action' => 'index'));
			}
		}else{
			$this->set('title_for_layout', __('Add Risk', true));
			$this->set('page_heading', __('Add Risk', true));
		}

		$this->set('page_subheading', __('Create a Project Risk', true));
		if (isset($risk_id) && !empty($risk_id)) {
			$risk_detail = risk_detail($risk_id);
			$user = $this->objView->loadHelper('ViewModel')->get_user_data($risk_detail['RmDetail']['user_id']);
			if ($user) {
				$creator = $user['UserDetail']['first_name'] . ' ' . $user['UserDetail']['last_name'];
			}
			$this->set('page_subheading', __('Risk Creator: ' . $creator, true));
		}

		$viewData = [];
		$RmDetail = null;
		$project_id = null;
		if (isset($risk_id) && !empty($risk_id)) {
			// Get project id from riskDetail table
			// Get this project's risk types
			$RmDetail = $this->RmDetail->find('first', [
				'conditions' => [
					'RmDetail.id' => $risk_id,
				],
			]);

			$selected_risk_leader = [];
			$selected_risk_leader = risk_leader($RmDetail['RmDetail']['id']);
			if (isset($selected_risk_leader) && !empty($selected_risk_leader)) {
				$selected_risk_leader[] = $this->user_id;
			} else {
				$selected_risk_leader[] = $this->user_id;
			}

			if (!in_array($this->user_id, $selected_risk_leader)) {
				$this->Session->setFlash(__('You are not authorized to update this risk.'), 'error');
				$this->redirect(array('controller' => 'risks', 'action' => 'index'));
			}
			$viewData['project_id'] = $project_id = $RmDetail['RmDetail']['project_id'];

			if (isset($RmDetail) && !empty($RmDetail)) {
				$project_risk_types = $this->project_risk_types($project_id);
				$viewData['project_risk_types'] = Set::combine($project_risk_types, '{n}.RmProjectRiskType.id', '{n}.RmProjectRiskType.title');

				$user_risk_types = $this->user_risk_types($project_id, $this->user_id);
				$viewData['user_risk_types'] = (isset($user_risk_types) && !empty($user_risk_types)) ? Set::combine($user_risk_types, '{n}.RmProjectRiskType.id', '{n}.RmProjectRiskType.title') : $user_risk_types;

				// Get all users of this risk
				$risk_users = $this->get_risk_users($risk_id, $this->user_id);

				// Get all elements associated with this risk
				$risk_elements = $this->get_risk_elements($risk_id);
				$this->setJsVar('project_risk_id', $RmDetail['RmDetail']['rm_project_risk_type_id']);

				$this->setJsVar('risk_id', $RmDetail['RmDetail']['id']);
			}
		} else {
			// Get default risk types
			$RmRiskType = $this->RmRiskType->find('all', [
				'conditions' => [
					'RmRiskType.status' => 1,
				],
			]);
			$viewData['project_risk_types'] = Set::combine($RmRiskType, '{n}.RmRiskType.id', '{n}.RmRiskType.title');
		}

		$cumb = 'Add Risk';

		if (isset($risk_id) && !empty($risk_id)) {
			$cumb = 'Edit Risk';
		}

		$crumb = [
			'last' => [$cumb],
		];

		$element_param = $from_summary = null;
		if (isset($this->params['named']) && !empty($this->params['named'])) {
			if (isset($this->params['named']['project'])) {
				$from_summary = $passed_project = $this->params['named']['project'];
			}
			if (isset($this->params['named']['elements'])) {
				$element_param = $this->params['named']['elements'];
			}
		}


		$this->set($viewData);
		$this->set('RmDetail', $RmDetail);
		$this->setJsVars(['project_id' => $project_id, 'user_id' => $this->user_id, 'passed_project' => $passed_project, 'element_param' => $element_param, 'from_summary' => $from_summary]);
		$this->set('passed_project', $passed_project);
		$this->set('element_param', $element_param);
		$this->set('crumb', $crumb);
		$this->set('from_summary', $from_summary);
	}

	public function risk_map($project_id = null) {
		$this->layout = 'inner';
		$this->set('title_for_layout', __('Risk Map', true));
		$this->set('page_heading', __('Risk Center', true));
		$this->set('page_subheading', __('View the Risk Register as a Heat Map', true));

		$viewData = [];
		$project_ids = [];

		$RmIds = user_owner_risks($this->user_id);
		$RmDetail = $this->RmDetail->find('all', [
			'conditions' => [
				'RmDetail.id' => $RmIds,
			],
			'recursive' => -1,
		]);

		$project_ids = Set::extract($RmDetail, '/RmDetail/project_id');

		$viewData['projects'] = [];
		$user_projects = $this->objView->loadHelper('Scratch')->user_projects($this->user_id);
		if (isset($user_projects) && !empty($user_projects)) {
			$projects = Set::combine($user_projects, '{n}.p.id', '{n}.p.title');
			$viewData['projects'] = array_map(function ($v) {
				return htmlentities($v, ENT_QUOTES, "UTF-8");
			}, $projects);
		}

		/*$viewData['project_ids'] = (isset($project_ids) && !empty($project_ids)) ? array_unique($project_ids) : [];
		$projects = $this->Project->find('list', [
			'conditions' => [
				'id' => $project_ids,
			],
			'fields' => ['id', 'title'],
		]);
		$viewData['projects'] = array_map(function ($v) {
			return htmlentities(strip_tags($v));
		}, $projects);*/

		$viewData['RmDetail'] = $RmDetail;
		$viewData['project_id'] = $project_id;

		$crumb = [
			'last' => ['Risk Center'],
		];
		$this->set($viewData);
		$this->set('crumb', $crumb);
		$this->setJsVar('project_id', $project_id);
		$this->setJsVar('all_project', '');
		if(isset($this->params['named']['project']) && !empty($this->params['named']['project'])){
			// pr($this->params['named']['project'], 1);
			$this->setJsVar('all_project', $this->params['named']['project']);
			if($this->params['named']['project'] == 'my'){
				$this->set('param', 'my');
			}
			if($this->params['named']['project'] == 'all'){
				$this->set('param', 'all');
			}

		}
		// pr($project_id, 1);
	}

	public function sample() {
		$this->layout = 'inner';
		$this->set('title_for_layout', __('Sample Code', true));
		$this->set('page_heading', __('Sample Code', true));
		$this->set('page_subheading', __('Sample Code', true));

		$crumb = [
			'last' => ['Sample Code'],
		];
		$this->set('crumb', $crumb);

		$mongo = new MongoClient(MONGO_CONNECT);
		$this->mongoDB = $mongo->selectDB(MONGO_DATABASE);
		$mongo_users = new MongoCollection($this->mongoDB, 'users');
		$mongo_attachments = new MongoCollection($this->mongoDB, 'attachments');
		$ret = $mongo_users->find();
		$pipeline = [
			'$lookup' => [
				'from' => 'attachments',
				'localField' => '_id',
				'foreignField' => 'userId',
				'as' => 'user_attachment',
			],
		];
		// pr($mongo_users->aggregate($pipeline), 1);
		// db.users.aggregate([{$lookup:{from: "attachments",localField: "_id",foreignField: "userId",as: "attachments"}}]);
		$userData = null;
		foreach ($ret as $obj) {
			// pr($obj['_id']->$id);
			foreach ($obj['_id'] as $key => $userId) {
				$att = $mongo_attachments->find(['userId' => $userId]);
				// pr($userId);
				if (isset($att) && !empty($att)) {
					foreach ($att as $key => $value) {
						$userData[$userId][] = ['file' => $value['title'] . '.' . $value['extention'], 'type' => $value['attachmentType']];
					}
				}
			}
		}
		pr($userData, 1);
	}

//****************** XHR Functions *********************//

	public function risk_list() {

		if ($this->request->isAjax()) {

			$this->layout = false;
			$user_id = $this->user_id;

			$viewData = [];
			$project_ids = [];

			$RmDetail = $this->RmDetail->find('all', [
				'conditions' => [
					'RmDetail.user_id' => $this->user_id,
				],
				'recursive' => -1,
			]);

			$userdata = $this->RmUser->find('all', [
				'conditions' => [
					'RmUser.user_id' => $this->user_id,
				],
				'group' => 'RmUser.rm_detail_id',
				'recursive' => -1,
			]);
			if (isset($userdata) && !empty($userdata)) {
				$rk_ids = Set::extract($userdata, '/RmUser/rm_detail_id');
				$userRmDetail = $this->RmDetail->find('all', [
					'conditions' => [
						'RmDetail.id' => $rk_ids,
					],
					'recursive' => -1,
				]);

				$RmDetail = array_merge($RmDetail, $userRmDetail);
			}

			$viewData['RmDetail'] = $RmDetail;

			$view = new View($this, false);
			$view->viewPath = 'Risks/partials';
			$view->set($viewData);
			$html = $view->render('risk_list');

			echo json_encode($html);
			exit();

		}
	}

	public function project_risk_list() {

		if ($this->request->isAjax()) {

			$this->layout = false;
			$user_id = $this->user_id;

			$viewData = [];
			$project_ids = [];
			$param_exposure = [];

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				// pr($post, 1);
				$viewData['passed_project_id'] = (isset($post['passed_project_id']) && !empty($post['passed_project_id'])) ? $post['passed_project_id'] : null;
				$project_id = (isset($post['passed_project_id']) && !empty($post['passed_project_id'])) ? $post['passed_project_id'] : null;
				$param_exposure = (isset($post['exposure']) && !empty($post['exposure'])) ? $post['exposure'] : null;
				$param_element_id = (isset($post['risk_element_id']) && !empty($post['risk_element_id'])) ? $post['risk_element_id'] : null;
				$param_wsp_id = (isset($post['risk_wsp_id']) && !empty($post['risk_wsp_id'])) ? $post['risk_wsp_id'] : null;
				$param_risk_id = (isset($post['param_risk_id']) && !empty($post['param_risk_id'])) ? $post['param_risk_id'] : null;
			}

			$user_risks = user_owner_risks($this->user_id);
			// $user_risks = user_risks($this->user_id);

			$rm_conditions['RmDetail.id'] = $user_risks;
			if (isset($project_id) && !empty($project_id) && $project_id != 'all') {
				$rm_conditions['RmDetail.project_id'] = $project_id;
			}
			$RmDetail = $this->RmDetail->find('all', [
				'conditions' => $rm_conditions,
				'recursive' => -1,
				'order' => ['CASE
					WHEN RmDetail.status=3 THEN 4
					WHEN RmDetail.status=1 THEN 1
					WHEN RmDetail.status=2 THEN 2
					WHEN RmDetail.status=4 THEN 3
				END asc']
			]);

			if (isset($param_exposure) && !empty($param_exposure)) {
				$RmDetail_temp = [];
				if(isset($RmDetail) && !empty($RmDetail)) {
					foreach ($RmDetail as $key => $value) {
						$exposer_data = risk_exposer($value['RmDetail']['id']);
				        $exposer = null;
				        if(isset($exposer_data) && !empty($exposer_data)) {
				            $exposer = calculate_exposer($exposer_data["RmExposeResponse"]["impact"], $exposer_data["RmExposeResponse"]["percentage"]);
				            if($param_exposure == strtolower($exposer["text"])) {
				            	$RmDetail_temp[] = $value;
				            }
				        }
					}
					$RmDetail = $RmDetail_temp;
				}
			}
			if (isset($param_element_id) && !empty($param_element_id)) {
				if (isset($RmDetail) && !empty($RmDetail)) {
					$rmid = Set::extract($RmDetail, '/RmDetail/id');
					$element_data = $this->RmElement->find('all', [
						'conditions' => [
							'RmElement.rm_detail_id' => $rmid,
							'RmElement.element_id' => $param_element_id,
						],
						'recursive' => -1,
					]);
					$elementRisks = [];
					if (isset($element_data) && !empty($element_data)) {
						$riskid = Set::extract($element_data, '/RmElement/rm_detail_id');
						foreach ($RmDetail as $key => $value) {
							if (in_array($value['RmDetail']['id'], $riskid)) {
								$elementRisks[] = $value;
							}
						}
					}
					$RmDetail = $elementRisks;
				}
			}

			// coming from gantt page
			if (isset($param_wsp_id) && !empty($param_wsp_id)) {
				if (isset($RmDetail) && !empty($RmDetail)) {
					$param_wsp_id = $param_wsp_id;
					$elementids = workspace_elements($param_wsp_id);
					$elementids = Set::extract($elementids, '/Element/id');
					$rmid = Set::extract($RmDetail, '/RmDetail/id');
					$element_data = $this->RmElement->find('all', [
						'conditions' => [
							'RmElement.rm_detail_id' => $rmid,
							'RmElement.element_id' => $elementids,
						],
						'recursive' => -1,
					]);
					$elementRisks = [];
					if (isset($element_data) && !empty($element_data)) {
						$riskid = Set::extract($element_data, '/RmElement/rm_detail_id');
						foreach ($RmDetail as $key => $value) {
							if (in_array($value['RmDetail']['id'], $riskid)) {
								$elementRisks[] = $value;
							}
						}
					}
					$RmDetail = $elementRisks;
				}
			}

			// coming from wsp page
			if (isset($param_risk_id) && !empty($param_risk_id)) {
				$RmDetail = $this->RmDetail->find('all', [
					'conditions' => [
						'RmDetail.id' => $param_risk_id,
					],
					'recursive' => -1,
				]);
			}


			$risk_ids = Set::extract($RmDetail, '/RmDetail/id');

			$RmDetails = $this->RmDetail->find('all', [
				'conditions' => [
					'RmDetail.id' => $risk_ids
				],
				'recursive' => -1,
				'order' => ['CASE
					WHEN RmDetail.status=3 THEN 4
					WHEN RmDetail.status=1 THEN 1
					WHEN RmDetail.status=2 THEN 2
					WHEN RmDetail.status=4 THEN 3
				END asc'],
				'limit' => $this->paging_limit,
				'offset' => 0,
			]);
			$RmDetailCount = $this->RmDetail->find('count', [
				'conditions' => [
					'RmDetail.id' => $risk_ids
				],
				'recursive' => -1,
				'order' => ['RmDetail.id DESC']
			]);

			$viewData['RmDetail'] = $RmDetails;
			$viewData['paging_limit'] = $this->paging_limit;
			$viewData['paging_current'] = 0;
			$viewData['paging_total'] = $RmDetailCount;

			$view = new View($this, false);
			$view->viewPath = 'Risks/partials';
			$view->set($viewData);
			$html = $view->render('risk_list');

			echo json_encode($html);
			exit();

		}
	}

	public function all_owner_risk() {

		if ($this->request->isAjax()) {

			$this->layout = false;
			$user_id = $this->user_id;

			$viewData = [];
			$user_risks = user_owner_risks($this->user_id);
			// $user_risks = user_risks($this->user_id);

			$RmDetail = $this->RmDetail->find('all', [
				'conditions' => [
					'RmDetail.id' => $user_risks,
				],
				'recursive' => -1,
				'order' => ['CASE
					WHEN RmDetail.status=3 THEN 4
					WHEN RmDetail.status=1 THEN 1
					WHEN RmDetail.status=2 THEN 2
					WHEN RmDetail.status=4 THEN 3
				END asc'],
				'limit' => $this->paging_limit,
				'offset' => 0,
			]);

			$viewData['RmDetail'] = $RmDetail;
			$viewData['paging_limit'] = $this->paging_limit;
			$viewData['paging_current'] = 0;
			$viewData['paging_total'] = ( isset($user_risks) && !empty($user_risks) ) ? count(array_unique($user_risks)) : 0;

			$view = new View($this, false);
			$view->viewPath = 'Risks/partials';
			$view->set($viewData);
			$html = $view->render('all_owner_risk');

			echo json_encode($html);
			exit();

		}
	}

	public function risk_pagination() {

		if ($this->request->isAjax()) {

			$this->layout = false;
			$user_id = $this->user_id;

			$viewData = [];
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				// pr($post );
				$RmIDS = null;
				$user_owner_risks = user_owner_risks($this->user_id);

				$rm_conditions['RmDetail.id'] = $user_owner_risks;
				if (isset($post['project_id']) && !empty($post['project_id']) && $post['project_id'] != 'all') {
					$rm_conditions['RmDetail.project_id'] = $post['project_id'];
				}

				$RmDetail = $this->RmDetail->find('all', [
					'conditions' => $rm_conditions,
					'recursive' => -1,
									'order' => ['CASE
						WHEN RmDetail.status=3 THEN 4
						WHEN RmDetail.status=1 THEN 1
						WHEN RmDetail.status=2 THEN 2
						WHEN RmDetail.status=4 THEN 3
					END asc'],
				]);

				if (isset($post['my']) && !empty($post['my'])) {
					$RmIDS = user_risks($this->user_id);
					$RmDetail = $this->RmDetail->find('all', [
						'conditions' => ['RmDetail.id' => $RmIDS],
						'recursive' => -1,
						'order' => ['CASE
							WHEN RmDetail.status=3 THEN 4
							WHEN RmDetail.status=1 THEN 1
							WHEN RmDetail.status=2 THEN 2
							WHEN RmDetail.status=4 THEN 3
						END asc'],
					]);
				}

				// comming from update element page
				if (isset($post['element_id']) && !empty($post['element_id'])) {
					$RmDetail = $this->User->find('all', [
						'conditions' => [
							'RmElement.element_id' => $post['element_id'],
							'RmElement.rm_detail_id' => $user_owner_risks,
						],
						'fields' => [
							'RmDetail.*'
						],
						'joins' => [
							[
								'table' => 'rm_elements',
								'alias' => 'RmElement',
								'type' => 'INNER',
								'conditions' => ['RmDetail.id = RmElement.rm_detail_id'],
							],
						],
						'recursive' => -1,
						'order' => ['CASE
							WHEN RmDetail.status=3 THEN 4
							WHEN RmDetail.status=1 THEN 1
							WHEN RmDetail.status=2 THEN 2
							WHEN RmDetail.status=4 THEN 3
						END asc'],
					]);

				}

				// from program center page
				if (isset($post['program_id']) && !empty($post['program_id'])) {
					$sorted_project = [];
					$program_projects = program_projects($post['program_id']);
					if (isset($program_projects) && !empty($program_projects)) {
						$program_projects = Set::extract($program_projects, '/ProjectProgram/project_id');

						$RmDetail = getUserRisksByProgram($this->user_id, $program_projects);
						// pr($RmDetail, 1);
						if (isset($post['exposure']) && !empty($post['exposure'])) {
							$param_exposure = $post['exposure'];
							$RmDetail_temp = [];
							if(isset($RmDetail) && !empty($RmDetail)) {
								foreach ($RmDetail as $key => $value) {
									$exposer_data = risk_exposer($value['RmDetail']['id']);
							        $exposer = null;
							        if(isset($exposer_data) && !empty($exposer_data)) {
							            $exposer = calculate_exposer($exposer_data["RmExposeResponse"]["impact"], $exposer_data["RmExposeResponse"]["percentage"]);
							            if($param_exposure == strtolower($exposer["text"])) {
							            	$RmDetail_temp[] = $value;
							            }
							        }
								}
								$RmDetail = $RmDetail_temp;
							}
						}
					}
				}

				// from risk map page
				if (isset($post['exposure']) && !empty($post['exposure'])) {
					$param_exposure = $post['exposure'];
					$RmDetail_temp = [];
					if(isset($RmDetail) && !empty($RmDetail)) {
						foreach ($RmDetail as $key => $value) {
							$exposer_data = risk_exposer($value['RmDetail']['id']);
					        $exposer = null;
					        if(isset($exposer_data) && !empty($exposer_data)) {
					            $exposer = calculate_exposer($exposer_data["RmExposeResponse"]["impact"], $exposer_data["RmExposeResponse"]["percentage"]);
					            if($param_exposure == strtolower($exposer["text"])) {
					            	$RmDetail_temp[] = $value;
					            }
					        }
						}
						$RmDetail = $RmDetail_temp;
					}
				}

				$risk_ids = Set::extract($RmDetail, '/RmDetail/id');
				$RmDetails = $this->RmDetail->find('all', [
					'conditions' => [
						'RmDetail.id' => $risk_ids
					],
					'recursive' => -1,
					'order' => ['CASE
						WHEN RmDetail.status=3 THEN 4
						WHEN RmDetail.status=1 THEN 1
						WHEN RmDetail.status=2 THEN 2
						WHEN RmDetail.status=4 THEN 3
					END asc'],
					'limit' => $this->paging_limit,
					'offset' => $post['current_offset'],
				]);


				$viewData['RmDetail'] = $RmDetails;
			}


			$view = new View($this, false);
			$view->viewPath = 'Risks/partials';
			$view->set($viewData);
			$html = $view->render('risk_pagination');

			echo json_encode($html);
			exit();

		}
	}

	public function risk_list_one() {

		if ($this->request->isAjax()) {

			$this->layout = false;
			$user_id = $this->user_id;

			$viewData = [];
			$project_ids = [];

			$RmDetail = null;
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				// pr($post, 1);
				$risk_id = $post['id'];
				$key = $post['key'];

				$RmDetail = $this->RmDetail->find('first', [
					'conditions' => [
						'RmDetail.id' => $risk_id,
					],
					'recursive' => -1,
				]);
			}

			$viewData['RmDetail'] = $RmDetail;
			$viewData['key'] = $key;

			$view = new View($this, false);
			$view->viewPath = 'Risks/partials';
			$view->set($viewData);
			$html = $view->render('risk_list_one');

			echo json_encode($html);
			exit();

		}
	}

	public function risk_map_data() {

		if ($this->request->isAjax()) {

			$this->layout = false;
			$user_id = $this->user_id;

			$viewData = [];
			$project_id = null;

			$RmDetail = null;
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				$project_id = (isset($post['project_id']) && !empty($post['project_id'])) ? $post['project_id'] : null;
			}

			$viewData['project_id'] = $project_id;

			$view = new View($this, false);
			$view->viewPath = 'Risks/partials';
			$view->set($viewData);
			$html = $view->render('risk_map_data');

			echo json_encode($html);
			exit();

		}
	}

	public function save_risk() {

 		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$response = [
				'success' => false,
				'content' => null,
			];
			$user_id = $this->user_id;

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;

				$project_users = (isset($post['project_users']) && !empty($post['project_users'])) ? $post['project_users'] : false;
				$risk_leaders = (isset($post['risk_leaders']) && !empty($post['risk_leaders'])) ? $post['risk_leaders'] : false;
				$project_elements = (isset($post['project_elements']) && !empty($post['project_elements'])) ? $post['project_elements'] : false;

				$riskData['user_id'] = $user_id;
				$riskData['created_by'] = $user_id;
				$riskData['project_id'] = $post['project_id'];
				$riskData['title'] = substr($post['title'], 0, 50);
				$riskData['rm_project_risk_type_id'] = $post['project_risk_types'];
				$riskData['possible_occurrence'] = (isset($post['future_date']) && !empty($post['future_date'])) ? date('Y-m-d', strtotime($post['future_date'])) : '';
				$riskData['description'] = (isset($post['risk_description']) && !empty($post['risk_description'])) ? substr($post['risk_description'], 0, 750) : '';

				if(isset($post['id']) && !empty($post['id'])){
					$risk_message = 'Risk updated';
				}else{
					$risk_message = 'Risk created';
				}


				if ($this->RmDetail->save($riskData)) {

					$task_data = [
						'project_id' => $post['project_id'],
						'element_type' => 'rm_details',
						'updated_user_id' => $this->user_id,
						'message' => $risk_message,
						'updated' => date("Y-m-d H:i:s"),
					];

					$this->loadModel('ProjectActivity');
					$this->ProjectActivity->id = null;
					$this->ProjectActivity->save($task_data);

					$projectDetails = $this->Project->find('first', array('conditions' => array('Project.id' => $post['project_id'])));
					$risk_id = $this->RmDetail->getLastInsertId();

					$projectName = '';
					if( isset($projectDetails) && !empty($projectDetails) ){
						$projectName = $projectDetails['Project']['title'];
					}

					// pr($project_users);
					if ($project_users) {
						/************** socket messages **************/
						if (SOCKET_MESSAGES) {
							$current_user_id = $this->user_id;
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
									'type' => 'risk_assignment',
									'created_id' => $current_user_id,
									'project_id' => $riskData['project_id'],
									'refer_id' => $risk_id,
									'creator_name' => $userDetail['UserDetail']['full_name'],
									'subject' => 'Risk assignment',
									'heading' => 'Risk: ' . htmlentities($riskData['title'],ENT_QUOTES, "UTF-8"),
									'sub_heading' => 'Project: ' . strip_tags(getFieldDetail('Project', $riskData['project_id'], 'title')),
									'date_time' => timezoneTimeConvertor($this->user_id, date('Y-m-d g:iA'), $format = 'd M Y g:iA') . user_country($this->user_id),
								],
								//'received_users' => array_values($ele_users),
							];
							if (is_array($r_open_users)) {
								$content['received_users'] = array_values($r_open_users);
							}
							$response['content']['socket'] = $content;

						}
						/************** socket messages **************/
						foreach ($project_users as $key => $value) {
							$riskUsers[]['RmUser'] = [
								'user_id' => $value,
								'rm_detail_id' => $risk_id,
							];
							//Risk assignment
							$this->notificationEmail($value, $risk_id, $riskData['project_id'], 'risk_assignment',$projectName,$riskData['title']);

						}
						$this->RmUser->saveAll($riskUsers);
					}
					if ($risk_leaders) {
						foreach ($risk_leaders as $key => $value) {
							$riskLeaders[]['RmLeader'] = [
								'user_id' => $value,
								'rm_detail_id' => $risk_id,
							];
						}
						$this->RmLeader->saveAll($riskLeaders);
					}
					if ($project_elements) {
						foreach ($project_elements as $key => $value) {
							//$riskElements[]['RmElement'] = [
							$this->RmElement->id = null;
							$riskElements['RmElement'] = [
								'element_id' => $value,
								'rm_detail_id' => $risk_id,
								'project_id' => $post['project_id'],
							];
							$this->RmElement->save($riskElements);

							$relation_id = $this->RmElement->getLastInsertId();
							//pr($relation_id );
							$element_workspace = element_workspace($value);

							$element_id = $value;


							$task_data = [
							'project_id' => $post['project_id'],
							'workspace_id' => $element_workspace,
							'element_id' => $element_id,
							'element_type' => 'risk_elements',
							'relation_id' => $relation_id,
							'user_id' => $this->user_id,
							'updated_user_id' => $this->user_id,
							'message' => 'Task Risk created',
							'updated' => date("Y-m-d H:i:s"),
							];
							$this->loadModel('Activity');
							$this->Activity->id = null;
							$this->Activity->save($task_data);
						}
						//$this->RmElement->saveAll($riskElements);
					}
					$response['success'] = true;
				}
			}

			// die;
			echo json_encode($response);
			exit();

		}
	}

	public function update_risk() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$response = [
				'success' => false,
				'content' => null,
			];
			$user_id = $this->user_id;

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				// pr(($post), 1);

				$project_users = (isset($post['project_users']) && !empty($post['project_users'])) ? $post['project_users'] : false;
				$risk_leaders = (isset($post['risk_leaders']) && !empty($post['risk_leaders'])) ? $post['risk_leaders'] : false;
				$project_elements = (isset($post['project_elements']) && !empty($post['project_elements'])) ? $post['project_elements'] : [];

				$riskData['id'] = $risk_id = $post['id'];
				$risk_detail = risk_detail($risk_id);
				if ($risk_detail) {
					if ($this->user_id != $risk_detail['RmDetail']['user_id']) {
						$project_users[] = $this->user_id;
						$risk_leaders[] = $this->user_id;
					}
				}
				// $riskData['user_id'] = $user_id;
				$riskData['project_id'] = $post['project_id'];
				$riskData['title'] = $post['title'];
				$riskData['rm_project_risk_type_id'] = $post['project_risk_types'];
				$riskData['possible_occurrence'] = (isset($post['future_date']) && !empty($post['future_date'])) ? date('Y-m-d', strtotime($post['future_date'])) : '';
				$riskData['description'] = (isset($post['risk_description']) && !empty($post['risk_description'])) ? $post['risk_description'] : '';

				if ($this->RmDetail->save($riskData)) {

					$risk_message = 'Risk updated';
					$task_data = [
						'project_id' => $post['project_id'],
						'element_type' => 'rm_details',
						'updated_user_id' => $this->user_id,
						'message' => $risk_message,
						'updated' => date("Y-m-d H:i:s"),
					];

					$this->loadModel('ProjectActivity');
					$this->ProjectActivity->id = null;
					$this->ProjectActivity->save($task_data);

					$prev_users = $this->RmUser->find('all', ['conditions' => ['RmUser.rm_detail_id' => $risk_id]]);
					if ($this->RmUser->hasAny(['RmUser.rm_detail_id' => $risk_id])) {
						$this->RmUser->deleteAll(['RmUser.rm_detail_id' => $risk_id], false);
					}

					if ($project_users) {
						foreach ($project_users as $key => $value) {
							$riskUsers[]['RmUser'] = [
								'user_id' => $value,
								'rm_detail_id' => $risk_id,
							];
						}
						$this->RmUser->saveAll($riskUsers);

						$projectTitle = strip_tags(getFieldDetail('Project', $riskData['project_id'], 'title'));
						$riskTitle = htmlentities($riskData['title'],ENT_QUOTES, "UTF-8");

						if (isset($prev_users) && !empty($prev_users)) {
							$prev_users = Set::extract($prev_users, '/RmUser/user_id');
						}
						else{
							$prev_users = [];
						}
							$diff_users = array_diff($project_users, $prev_users);
							// pr($diff_users, 1);
							if (isset($diff_users) && !empty($diff_users)) {
								foreach ($diff_users as $key => $userid) {
									$this->notificationEmail($userid, $risk_id, $riskData['project_id'], 'risk_assignment', $projectTitle, $riskTitle);
								}
								/************** socket messages **************/
								if (SOCKET_MESSAGES) {
									$current_user_id = $this->user_id;
									$r_users = $diff_users;
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
											'type' => 'risk_assignment',
											'created_id' => $current_user_id,
											'project_id' => $riskData['project_id'],
											'refer_id' => $risk_id,
											'creator_name' => $userDetail['UserDetail']['full_name'],
											'subject' => 'Risk assignment',
											'heading' => 'Risk: ' . htmlentities($riskData['title'],ENT_QUOTES, "UTF-8"),
											'sub_heading' => 'Project: ' . strip_tags(getFieldDetail('Project', $riskData['project_id'], 'title')),
											'date_time' => timezoneTimeConvertor($this->user_id, date('Y-m-d g:iA'), $format = 'd M Y g:iA') . user_country($this->user_id),
										],
										//'received_users' => array_values($ele_users),
									];
									if (is_array($r_open_users)) {
										$content['received_users'] = array_values($r_open_users);
									}
									$response['content']['socket'] = $content;

								}
								/************** socket messages **************/
							}


					}

					if ($this->RmLeader->hasAny(['RmLeader.rm_detail_id' => $risk_id])) {
						$this->RmLeader->deleteAll(['RmLeader.rm_detail_id' => $risk_id], false);
					}

					if ($risk_leaders) {
						foreach ($risk_leaders as $key => $value) {
							$riskLeaders[]['RmLeader'] = [
								'user_id' => $value,
								'rm_detail_id' => $risk_id,
							];
						}
						$this->RmLeader->saveAll($riskLeaders);
					}

					$prev_elms = $this->RmElement->find('list', ['conditions' => ['RmElement.rm_detail_id' => $risk_id],'fields'=>array('id','element_id')]);

					$prev_elms = (isset($prev_elms) && !empty($prev_elms)) ? $prev_elms : [];

					$oldEl = array_diff($prev_elms,$project_elements);
					$newEL = array_diff($project_elements,$prev_elms);
					$existing = array_intersect($project_elements,$prev_elms);

					/*
					pr($prev_elms);
					pr($project_elements);
					pr($oldEl);
					pr($newEL); */


					if (isset($oldEl) && !empty($oldEl)) {
						foreach ($oldEl as $key => $value) {

							$relation_id = $key;
							//pr($relation_id );
							$element_workspace = element_workspace($value);

							$element_id = $value;

							$task_data = [
							'project_id' => $post['project_id'],
							'workspace_id' => $element_workspace,
							'element_id' => $element_id,
							'element_type' => 'risk_elements',
							'relation_id' => $relation_id,
							'user_id' => $this->user_id,
							'updated_user_id' => $this->user_id,
							'message' => 'Task Risk removed',
							'updated' => date("Y-m-d H:i:s"),
							];
							$this->loadModel('Activity');
							$this->Activity->id = null;
							$this->Activity->save($task_data);

						$this->RmElement->deleteAll(['RmElement.rm_detail_id' => $risk_id,'RmElement.element_id'=> $value], false);
						}
					}



					//if ($this->RmElement->hasAny(['RmElement.rm_detail_id' => $risk_id])) {
						//$this->RmElement->deleteAll(['RmElement.rm_detail_id' => $risk_id], false);
					//}

					if (isset($newEL) && !empty($newEL)) {
						foreach ($newEL as $key => $value) {
							$this->RmElement->id = null;
							$riskElements['RmElement'] = [
								'element_id' => $value,
								'rm_detail_id' => $risk_id,
								'project_id' => $post['project_id'],
							];
							$this->RmElement->save($riskElements);
							$relation_id = $this->RmElement->getLastInsertId();
							//pr($relation_id );
							$element_workspace = element_workspace($value);

							$element_id = $value;


							$task_data = [
							'project_id' => $post['project_id'],
							'workspace_id' => $element_workspace,
							'element_id' => $element_id,
							'element_type' => 'risk_elements',
							'relation_id' => $relation_id,
							'user_id' => $this->user_id,
							'updated_user_id' => $this->user_id,
							'message' => 'Task Risk created',
							'updated' => date("Y-m-d H:i:s"),
							];
							$this->loadModel('Activity');
							$this->Activity->id = null;
							$this->Activity->save($task_data);
						}

					}


					if (isset($existing) && !empty($existing)) {
						foreach ($existing as $key ) {


							//pr($relation_id );
							$values = $this->RmElement->find('first', ['conditions' => ['RmElement.element_id' => $key,'RmElement.rm_detail_id'=>$risk_id],'fields'=>array('id')]);

							$relation_id = $values['RmElement']['id'];
							//pr($relation_id );
							$element_workspace = element_workspace($key);

							$element_id = $key;

							$task_data = [
							'project_id' => $post['project_id'],
							'workspace_id' => $element_workspace,
							'element_id' => $element_id,
							'element_type' => 'risk_elements',
							'relation_id' => $relation_id,
							'user_id' => $this->user_id,
							'updated_user_id' => $this->user_id,
							'message' => 'Task Risk updated',
							'updated' => date("Y-m-d H:i:s"),
							];
							$this->loadModel('Activity');
							$this->Activity->id = null;
							$this->Activity->save($task_data);


						}
					}




					$response['success'] = true;
				}
			}

			echo json_encode($response);
			exit();

		}
	}

	public function risk_status() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$response = [
				'success' => false,
				'content' => null,
				'data' => null,
			];
			$user_id = $this->user_id;

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				$risk_id = (isset($post['id']) && !empty($post['id'])) ? $post['id'] : null;

				if (isset($risk_id) && !empty($risk_id)) {
					$this->RmDetail->id = $risk_id;
					$projectDetail = $this->RmDetail->find('first', array('conditions' => array('RmDetail.id' => $risk_id)));
					// pr($projectDetail, 1);
					if ($this->RmDetail->saveField("status", $post['status'])) {
						$riskDetail = $this->RmDetail->find('first', array('conditions' => array('RmDetail.id' => $risk_id), 'recursive' => -1));

						$response['data'] = _displayDate(date('Y-m-d h:i:s A',strtotime($riskDetail['RmDetail']['modified'])), $format = 'd M Y, g:iA');
						//Risk signed-off
						if ($post['status'] == 3) {
							$allRiskUser = risk_users($risk_id);
							if (isset($allRiskUser) && !empty($allRiskUser)) {
								foreach ($allRiskUser as $key => $value) {
									$this->notificationEmail($value, $risk_id, $projectDetail['RmDetail']['project_id'], 'risk_signedoff',strip_tags(getFieldDetail('Project', $projectDetail['RmDetail']['project_id'], 'title')),htmlentities($projectDetail['RmDetail']['title'],ENT_QUOTES, "UTF-8"));
								}
							}

							/************** socket messages **************/
							if (SOCKET_MESSAGES) {
								$current_user_id = $this->user_id;
								$r_users = $allRiskUser;
								if (isset($r_users) && !empty($r_users)) {
									if (($key = array_search($projectDetail['RmDetail']['user_id'], $r_users)) == false) {
										$r_users[] = $projectDetail['RmDetail']['user_id'];
									}
									if (($key = array_search($current_user_id, $r_users)) !== false) {
										unset($r_users[$key]);
									}
								}
								$r_open_users = null;
								if (isset($r_users) && !empty($r_users)) {
									foreach ($r_users as $key => $value) {
										if (web_notify_setting($value, 'riskcenter', 'risk_signedoff')) {
											$r_open_users[] = $value;
										}
									}
								}
								$userDetail = get_user_data($current_user_id);


								$prev_elms = $this->RmElement->find('list', ['conditions' => ['RmElement.rm_detail_id' => $risk_id],'fields'=>array('id','element_id')]);

								$prev_elms = (isset($prev_elms) && !empty($prev_elms)) ? $prev_elms : [];


								if (isset($prev_elms) && !empty($prev_elms)) {
									foreach ($prev_elms as $key => $value) {

										$relation_id = $key;
										//pr($relation_id );
										$element_workspace = element_workspace($value);

										$element_id = $value;

										$task_data = [
										'project_id' => $projectDetail['RmDetail']['project_id'],
										'workspace_id' => $element_workspace,
										'element_id' => $element_id,
										'element_type' => 'risk_elements',
										'relation_id' => $relation_id,
										'user_id' => $this->user_id,
										'updated_user_id' => $this->user_id,
										'message' => 'Task Risk signed off',
										'updated' => date("Y-m-d H:i:s"),
										];
										$this->loadModel('Activity');
										$this->Activity->id = null;
										$this->Activity->save($task_data);


									}
								}


								$task_data1 = [
									'project_id' => $projectDetail['RmDetail']['project_id'],
									'element_type' => 'rm_details',
									'updated_user_id' => $this->user_id,
									'message' => 'Risk signed off',
									'updated' => date("Y-m-d H:i:s"),
								];

								$this->loadModel('ProjectActivity');
								$this->ProjectActivity->id = null;
								$this->ProjectActivity->save($task_data1);


								$content = [
									'notification' => [
										'type' => 'risk_signedoff',
										'created_id' => $current_user_id,
										'project_id' => $projectDetail['RmDetail']['project_id'],
										'refer_id' => $risk_id,
										'creator_name' => $userDetail['UserDetail']['full_name'],
										'subject' => 'Risk sign-off',
										'heading' => 'Risk: ' . htmlentities($projectDetail['RmDetail']['title'],ENT_QUOTES, "UTF-8"),
										'sub_heading' => 'Project: ' . strip_tags(getFieldDetail('Project', $projectDetail['RmDetail']['project_id'], 'title')),
										'date_time' => timezoneTimeConvertor($this->user_id, date('Y-m-d g:iA'), $format = 'd M Y g:iA') . user_country($this->user_id),
									],
								];
								if (is_array($r_open_users)) {
									$content['received_users'] = array_values($r_open_users);
								}
								$response['content']['socket'] = $content;
							}
							/************** socket messages **************/
						}
						$response['success'] = true;
					}
				}
			}

			echo json_encode($response);
			exit();

		}
	}

	public function save_exposer() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$response = [
				'success' => false,
				'content' => null,
			];
			$user_id = $this->user_id;

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				// pr($post, 1);
				$data = null;

				if (isset($post['risk_id']) && !empty($post['risk_id'])) {
					$data['rm_detail_id'] = $post['risk_id'];
				}
				if (isset($post['impacts'])) {
					$data['impact'] = $post['impacts'];
				}
				if (isset($post['percentages'])) {
					$data['percentage'] = $post['percentages'];
				}

				$exposer_data = $this->RmExposeResponse->find('first', [
					'conditions' => [
						'RmExposeResponse.rm_detail_id' => $data['rm_detail_id'],
					],
				]);
				if (isset($exposer_data) && !empty($exposer_data)) {
					$data['id'] = $exposer_data['RmExposeResponse']['id'];
				}

				if ($this->RmExposeResponse->save($data)) {
					$response['success'] = true;
					$exposer_data = $this->RmExposeResponse->find('first', [
						'conditions' => [
							'RmExposeResponse.rm_detail_id' => $data['rm_detail_id'],
						],
					]);
				}

			}

			echo json_encode($response);
			exit();

		}
	}

	public function save_response() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$response = [
				'success' => false,
				'content' => null,
			];
			$user_id = $this->user_id;

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				// pr($post, 1);
				$data = null;
				$fields = ['mitigation_user_id', 'mitigation_updated'];
				$mitigation = $contingency = $residual = false;

				if (isset($post['risk_id']) && !empty($post['risk_id'])) {
					$data['rm_detail_id'] = $post['risk_id'];
				}
				if ((isset($post['save_mitigation']) && !empty($post['save_mitigation'])) && (isset($post['mitigation']) && !empty($post['mitigation']))) {
					$data['mitigation'] = substr($post['mitigation'], 0, 1000);
					$data['mitigation_user_id'] = $user_id;
					$data['mitigation_updated'] = date('Y-m-d H:i:s');
					$mitigation = true;
					$fields = ['mitigation_user_id', 'mitigation_updated'];
				}
				if ((isset($post['save_contingency']) && !empty($post['save_contingency'])) && (isset($post['contingency']) && !empty($post['contingency']))) {
					$data['contingency'] = substr($post['contingency'], 0, 1000);
					$data['contingency_user_id'] = $user_id;
					$data['contingency_updated'] = date('Y-m-d H:i:s');
					$contingency = true;
					$fields = ['contingency_user_id', 'contingency_updated'];
				}
				if ((isset($post['save_residual']) && !empty($post['save_residual'])) && (isset($post['residual']) && !empty($post['residual']))) {
					$data['residual'] = substr($post['residual'], 0, 1000);
					$data['residual_user_id'] = $user_id;
					$data['residual_updated'] = date('Y-m-d H:i:s');
					$residual = true;
					$fields = ['residual_user_id', 'residual_updated'];
				}

				$exposer_data = $this->RmExposeResponse->find('first', [
					'conditions' => [
						'RmExposeResponse.rm_detail_id' => $data['rm_detail_id'],
					],
				]);
				if (isset($exposer_data) && !empty($exposer_data)) {
					$data['id'] = $exposer_data['RmExposeResponse']['id'];
				}

				if ($this->RmExposeResponse->save($data)) {
					$response['success'] = true;
					$exposer_data = $this->RmExposeResponse->find('first', [
						'conditions' => [
							'RmExposeResponse.rm_detail_id' => $data['rm_detail_id'],
						],
					]);
					if (isset($exposer_data) && !empty($exposer_data)) {
						$userDetail = get_user_data($user_id);
						$user_name = $userDetail['UserDetail']['first_name'] . ' ' . $userDetail['UserDetail']['last_name'];
						if ($mitigation) {
							$response['content'] = ['username' => $user_name, 'updated' => _displayDate($exposer_data['RmExposeResponse']['mitigation_updated'])];
						} else if ($contingency) {
							$response['content'] = ['username' => $user_name, 'updated' => _displayDate($exposer_data['RmExposeResponse']['contingency_updated'])];
						} else if ($residual) {
							$response['content'] = ['username' => $user_name, 'updated' => _displayDate($exposer_data['RmExposeResponse']['residual_updated'])];
						}

					}
				}

			}

			echo json_encode($response);
			exit();

		}
	}

	public function remove_response() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$response = [
				'success' => false,
				'content' => null,
			];
			$user_id = $this->user_id;

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				// pr($post, 1);
				$data = null;
				$mitigation = $contingency = $residual = false;

				if (isset($post['risk_id']) && !empty($post['risk_id'])) {
					$data['rm_detail_id'] = $post['risk_id'];
				}
				if (isset($post['remove_mitigation']) && !empty($post['remove_mitigation'])) {
					$data['mitigation'] = null;
					// $data['mitigation_user_id'] = null;
					// $data['mitigation_updated'] = null;
					$mitigation = true;
				}
				if (isset($post['remove_contingency']) && !empty($post['remove_contingency'])) {
					$data['contingency'] = null;
					// $data['contingency_user_id'] = null;
					// $data['contingency_updated'] = null;
					$contingency = true;
				}
				if (isset($post['remove_residual']) && !empty($post['remove_residual'])) {
					$data['residual'] = null;
					// $data['residual_user_id'] = null;
					// $data['residual_updated'] = null;
					$residual = true;
				}

				$exposer_data = $this->RmExposeResponse->find('first', [
					'conditions' => [
						'RmExposeResponse.rm_detail_id' => $data['rm_detail_id'],
					],
				]);
				if (isset($exposer_data) && !empty($exposer_data)) {
					$data['id'] = $exposer_data['RmExposeResponse']['id'];
				}

				if ($this->RmExposeResponse->save($data)) {
					$response['success'] = true;
				}

			}

			echo json_encode($response);
			exit();

		}
	}

	public function risk_projects() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$response = [
				'success' => false,
				'content' => [],
			];
			$user_id = $this->user_id;

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				// pr($post, 1);

				$project_ids = [];

				$RmDetail = $this->RmDetail->find('all', [
					'conditions' => [
						'RmDetail.user_id' => $this->user_id,
					],
					'recursive' => -1,
				]);

				if (isset($RmDetail) && !empty($RmDetail)) {
					$project_ids = Set::extract($RmDetail, '/RmDetail/project_id');
				}

				$userdata = $this->RmUser->find('all', [
					'conditions' => [
						'RmUser.user_id' => $this->user_id,
					],
					'group' => 'RmUser.rm_detail_id',
					'recursive' => -1,
				]);
				if (isset($userdata) && !empty($userdata)) {
					$rk_ids = Set::extract($userdata, '/RmUser/rm_detail_id');
					$userRmDetail = $this->RmDetail->find('all', [
						'conditions' => [
							'RmDetail.id' => $rk_ids,
						],
						'recursive' => -1,
					]);
					$user_project_ids = Set::extract($userRmDetail, '/RmDetail/project_id');
					$project_ids = array_merge($project_ids, $user_project_ids);
					$RmDetail = array_merge($RmDetail, $userRmDetail);
				}
				// pr($RmDetail, 1);

				$project_ids = (isset($project_ids) && !empty($project_ids)) ? array_unique($project_ids) : [];
				$projects = $this->Project->find('list', [
					'conditions' => [
						'id' => $project_ids,
					],
					'fields' => ['id', 'title'],
				]);
				$response['content'] = array_map(function ($v) {
					return trim(strip_tags($v));
				}, $projects);

				$response['success'] = true;
			}

			echo json_encode($response);
			exit();

		}
	}

	public function risk_types() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$response = [
				'success' => false,
				'content' => [],
			];

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;

				$library_risk_types = $this->library_risk_types();
				$response['content'] = $library_risk_types;

				$response['success'] = true;
			}

			echo json_encode($response);
			exit();

		}
	}

	public function default_risk_types() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$response = [
				'success' => false,
				'content' => [],
			];
			$html = "";
			$user_id = $this->user_id;

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;

				$project_id = $post['project_id'];
				if (isset($project_id) && !empty($project_id)) {
					$response['content'] = $this->types_by_project($project_id);
				}
				$response['success'] = true;
				// $view = new View($this, false);
				// $view->viewPath = 'Risks/partials';
				// $view->set($response);
				// $view->set("select",1);
				// $html = $view->render('default_risk_types');
			}

			echo json_encode($response);
			exit();

		}
	}

	public function all_risk_types() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$response = [
				'success' => false,
				'content' => [],
			];
			$user_id = $this->user_id;

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;

				$project_id = $post['project_id'];
				$today = date('Y-m-d');
				if (isset($project_id) && !empty($project_id)) {
					$project_risk_types = $this->project_risk_types($project_id);
					/***********************************************/
					$prt_data = $this->Project->query("SELECT title FROM rm_project_risk_types prt WHERE project_id = '$project_id' AND is_custom <> 1");
					if(isset($prt_data) && !empty($prt_data)){
						$prt_data = Set::extract($prt_data, '{n}.prt.title');
					}
					$pt_data = $this->Project->query("SELECT title FROM rm_risk_types pt");
					if(isset($pt_data) && !empty($pt_data)){
						$pt_data = Set::extract($pt_data, '{n}.pt.title');
					}
					if( (isset($prt_data) && !empty($prt_data)) && (isset($pt_data) && !empty($pt_data)) ){
						$delete_from = array_diff($prt_data, $pt_data);
						$add_to = array_diff($pt_data, $prt_data);
						if(isset($delete_from) && !empty($delete_from)) {
							$del_vals = [];
							foreach ($delete_from as $key => $value) {
								$del_vals[] = Sanitize::escape($value);
							}
							$this->Project->query("DELETE FROM rm_project_risk_types WHERE title IN('" . implode("', '", $del_vals) . "')");
						}
						if(isset($add_to) && !empty($add_to)) {
							$qry = "INSERT INTO rm_project_risk_types (title, project_id, user_id, created, modified) VALUES ";
							$qry_arr = [];
							foreach ($add_to as $list) {
								$qry_arr[] = "('$list', '$project_id', '$user_id', '$today', '$today')";
							}
							$qry .= implode(' ,', $qry_arr);
	        				$this->Project->query($qry);
						}
					}
					/***********************************************/
					$project_risk_types = $this->project_risk_types($project_id);
					;
					$response['content'] = Set::combine($project_risk_types, '{n}.RmProjectRiskType.id', '{n}.RmProjectRiskType.title');
					$response['content'] = htmlentity($response['content']);
				}
				$response['success'] = true;
			}

			echo json_encode($response);
			exit();

		}
	}

	public function custom_risk_type() {

		if ($this->request->isAjax()) {

			$this->layout = false;
			$user_id = $this->user_id;

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;

				$project_id = $post['project_id'];
				$user_risk_types = null;
				if (isset($project_id) && !empty($project_id)) {
					$user_risk_types = $this->project_user_risk_types($project_id);
					$user_risk_types = (isset($user_risk_types) && !empty($user_risk_types)) ? Set::combine($user_risk_types, '{n}.RmProjectRiskType.id', '{n}.RmProjectRiskType.title') : $user_risk_types;
				}

				$view = new View($this, false);
				$view->viewPath = 'Risks/partials';
				$view->set("project_id", $project_id);
				$view->set("user_risk_types", $user_risk_types);
				$html = $view->render('custom_risk_type');

			}

			echo json_encode($html);
			exit();

		}
	}

	public function project_users() {

		if ($this->request->isAjax()) {

			$this->layout = false;
			$user_id = $this->user_id;

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				// pr($post, 1);

				$project_id = $post['project_id'];
				$project_users = (isset($project_id) && !empty($project_id)) ? $this->get_project_users($project_id, $this->user_id) : [];

				$view = new View($this, false);
				$view->viewPath = 'Risks/partials';
				$view->set("project_id", $project_id);
				$view->set("project_users", $project_users);
				$view->set("risk_id", $post['risk_id']);
				$html = $view->render('project_users');

			}

			echo json_encode($html);
			exit();

		}
	}

	public function risk_leaders() {

		if ($this->request->isAjax()) {

			$this->layout = false;
			$user_id = $this->user_id;

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				// pr($post, 1);

				$project_id = $post['project_id'];
				$risk_leaders = (isset($post['users']) && !empty($post['users'])) ? $post['users'] : [];
				$risk_id = (isset($post['risk_id']) && !empty($post['risk_id'])) ? $post['risk_id'] : null;

				$view = new View($this, false);
				$view->viewPath = 'Risks/partials';
				$view->set("project_id", $project_id);
				$view->set("risk_leaders", $risk_leaders);
				$view->set("risk_id", $risk_id);
				$html = $view->render('risk_leaders');

			}

			echo json_encode($html);
			exit();

		}
	}

	public function get_risk_leaders() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$response = [
				'success' => false,
				'content' => null,
			];
			$user_id = $this->user_id;

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				$risk_id = (isset($post['id']) && !empty($post['id'])) ? $post['id'] : null;

				if (isset($risk_id) && !empty($risk_id)) {

					$risk_leader = risk_leader($risk_id);

					$response['success'] = true;
					$response['content'] = $risk_leader;
				}
			}

			echo json_encode($response);
			exit();

		}
	}

	public function project_elements() {

		if ($this->request->isAjax()) {

			$this->layout = false;
			$user_id = $this->user_id;

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;

				$project_id = $post['project_id'];
				// $project_elements1 = $this->get_project_elements_old($project_id, $this->user_id);
				$project_elements = [];
				if(isset($project_id) && !empty($project_id)){
					$project_elements = $this->get_project_elements($project_id, $this->user_id);
				}

				$view = new View($this, false);
				$view->viewPath = 'Risks/partials';
				$view->set("project_id", $project_id);
				$view->set("project_elements", $project_elements);
				$view->set("element_param", $post['element_param']);
				$view->set("risk_id", $post['risk_id']);
				$html = $view->render('project_elements');

			}

			echo json_encode($html);
			exit();

		}
	}

	public function save_risk_type() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$response = [
				'success' => false,
				'content' => null,
			];
			$user_id = $this->user_id;

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				// pr($post, 1);

				if (isset($post['risk_type_id']) && !empty($post['risk_type_id'])) {
					// $this->RmProjectRiskType->id = $post['risk_type_id'];
					$id = $post['risk_type_id'];
					$title = $post['title'];
					$this->RmProjectRiskType->query("update rm_project_risk_types set title = '$title', is_custom = 1 WHERE id = $id");
					// if ($this->RmProjectRiskType->save(['id' => $post['risk_type_id'], "title", $post['title'], 'is_custom' => 1])) {
						$response['success'] = true;
					// }
				}
			}

			echo json_encode($response);
			exit();

		}
	}

	public function trash_risk_type() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$response = [
				'success' => false,
				'content' => null,
			];
			$user_id = $this->user_id;

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;

				if (isset($post['risk_type_id']) && !empty($post['risk_type_id'])) {
					if ($this->RmProjectRiskType->delete($post['risk_type_id'])) {
						$response['success'] = true;
					}
				}
			}

			echo json_encode($response);
			exit();

		}
	}

	public function create_risk_type() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$response = [
				'success' => false,
				'content' => null,
			];
			$user_id = $this->user_id;

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;

				if (isset($post['title']) && !empty($post['title'])) {
					$data = [
						'title' => (substr($post['title'], 0, 50)),
						'project_id' => $post['project_id'],
						'user_id' => $user_id,
						'is_custom' => 1
					];
					if ($this->RmProjectRiskType->save($data)) {
						$response['success'] = true;
					}
				}
			}

			echo json_encode($response);
			exit();

		}
	}

	public function risk_profile($risk_id = null, $key = null, $type = 'response') {

		if ($this->request->isAjax()) {

			$this->layout = false;
			$user_id = $this->user_id;
			$viewData = null;

			$viewData['type'] = $type;
			$viewData['risk_id'] = $risk_id;
			$viewData['key'] = $key;
			$viewData['RmDetail'] = $this->RmDetail->find('first', [
				'conditions' => [
					'RmDetail.id' => $risk_id,
				],
			]);

			$this->set($viewData);
			$this->render('/Risks/partials/risk_profile');

		}
	}

	public function project_filter_users() {

		if ($this->request->isAjax()) {

			$this->layout = false;
			$user_id = $this->user_id;

			$viewData = [];
			$project_ids = [];

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				$viewData['passed_project_id'] = $passed_project_id = (isset($post['passed_project_id']) && !empty($post['passed_project_id'])) ? $post['passed_project_id'] : null;

				$user_risks = user_owner_risks($this->user_id);

				$RmDetail = $this->RmDetail->find('all', [
					'conditions' => [
						'RmDetail.id' => $user_risks,
					],
					'recursive' => -1,
				]);

				if (isset($passed_project_id) && !empty($passed_project_id)) {
					$projectRisks = [];
					foreach ($RmDetail as $key => $value) {
						if (in_array($value['RmDetail']['project_id'], $passed_project_id)) {
							$projectRisks[] = $value;
						}
					}
					$RmDetail = $projectRisks;
				}

				$project_risk_users = [];
				if (isset($RmDetail) && !empty($RmDetail)) {
					$RmIds = Set::extract($RmDetail, '/RmDetail/id');
					$RmCreators = Set::extract($RmDetail, '/RmDetail/user_id');
					$risk_users = risk_users($RmIds);
					$risk_leader = risk_leader($RmIds);
					if (isset($RmCreators) && !empty($RmCreators)) {
						$project_risk_users = array_merge($project_risk_users, $RmCreators);
					}
					if (isset($risk_users) && !empty($risk_users)) {
						$project_risk_users = array_merge($project_risk_users, $risk_users);
					}
					if (isset($risk_leader) && !empty($risk_leader)) {
						$project_risk_users = array_merge($project_risk_users, $risk_leader);
					}
				}

				if (isset($project_risk_users) && !empty($project_risk_users)) {
					$project_risk_users = array_unique($project_risk_users);
				}
				$viewData['project_risk_users'] = $project_risk_users;
			}

			$view = new View($this, false);
			$view->viewPath = 'Risks/partials';
			$view->set($viewData);
			$html = $view->render('project_filter_users');

			echo json_encode($html);
			exit();

		}
	}

//****************** Private Functions *********************//

	function set_overdue($risk_ids = null) {

		if (!isset($risk_ids) || empty($risk_ids)) {
			return;
		}

		$risks = $this->RmDetail->find('all', [
			'conditions' => [
				'RmDetail.id' => $risk_ids,
			],
		]);

		foreach ($risks as $key => $value) {
			$data = $value['RmDetail'];
			$today = date("Y-m-d");

			if (isset($data['possible_occurrence']) && !empty($data['possible_occurrence'])) {
				$overdue = date('Y-m-d', strtotime($data['possible_occurrence']));
				// e($overdue);
				// e($today);
				if ($overdue < $today) {
					$this->RmDetail->id = $data['id'];
					$this->RmDetail->saveField("status", 4);
					$this->RmDetail->id = null;
				} else if ($overdue > $today && $data['status'] == 4) {
					$this->RmDetail->id = $data['id'];
					$this->RmDetail->saveField("status", 1);
					$this->RmDetail->id = null;
				}
			}
			$this->Project->id = $data['project_id'];
			if (!$this->Project->exists()) {
				$this->RmDetail->delete($data['id']);
			}
			$this->Project->id = null;

		}
		// die;
	}

	/*function library_risk_types() {
		$riskProjects = getRiskProjects($this->user_id);
		// pr($riskProjects, 1);
		$library_risks = $this->RmRiskType->find('all', [
			'conditions' => [
				'RmRiskType.status' => 1,
			],
		]);

		$risks = [];

		if (isset($library_risks) && !empty($library_risks)) {
			$library_risks = Set::combine($library_risks, '{n}.RmRiskType.id', '{n}.RmRiskType.title');
			$risks = array_merge($risks, $library_risks);
		}

		$project_risks = $this->RmProjectRiskType->find('all', [
			'conditions' => [
				'RmProjectRiskType.user_id IS NOT NULL',
				'RmProjectRiskType.project_id' => array_values($riskProjects),
			],
		]);

		if (isset($project_risks) && !empty($project_risks)) {
			$project_risks = Set::combine($project_risks, '{n}.RmProjectRiskType.id', '{n}.RmProjectRiskType.title');
			$risks = array_merge($risks, $project_risks);
		}
		pr($risks, 1);
		return $risks;
	}*/

	function library_risk_types() {
		$userRisks = getUserRisks($this->user_id);

		$project_risks = [];

		if (isset($userRisks) && !empty($userRisks)) {
			$userRiskIds = Set::extract($userRisks, '/RmDetail/id');
			$user_risks = $this->RmDetail->find('all', [
				'conditions' => [
					'RmDetail.id' => $userRiskIds,
				],
				'fields' => ['rm_project_risk_type_id'],
				'group' => ['rm_project_risk_type_id'],
			]);
			$RiskTypeIds = Set::extract($user_risks, '/RmDetail/rm_project_risk_type_id');
			$project_risks = $this->RmProjectRiskType->find('all', [
				'conditions' => [
					'RmProjectRiskType.id' => $RiskTypeIds,
				],
			]);

			if (isset($project_risks) && !empty($project_risks)) {
				$project_risks = Set::combine($project_risks, '{n}.RmProjectRiskType.id', '{n}.RmProjectRiskType.title');
				$response['content'] = htmlentity($project_risks);
			}
			$project_risks = array_unique(htmlentity($project_risks));

		}

		return $project_risks;
	}

	function project_risk_types($project_id) {
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

		return $project_risks;
	}

	function types_by_project($project_id) {

		// $userRisks = getUserRisksByProject($this->user_id, $project_id);
		$userRisks = getOwnerRisksByProject($this->user_id, $project_id);
		// pr($userRisks);
		// pr($userRisks1, 1);

		$project_risks = [];

		if (isset($userRisks) && !empty($userRisks)) {
			$userRiskIds = Set::extract($userRisks, '/RmDetail/id');
			$user_risks = $this->RmDetail->find('all', [
				'conditions' => [
					'RmDetail.id' => $userRiskIds,
				],
				'fields' => ['rm_project_risk_type_id'],
				'group' => ['rm_project_risk_type_id'],
			]);
			$RiskTypeIds = Set::extract($user_risks, '/RmDetail/rm_project_risk_type_id');
			$project_risks = $this->RmProjectRiskType->find('all', [
				'conditions' => [
					'RmProjectRiskType.id' => $RiskTypeIds,
				],
			]);

			if (isset($project_risks) && !empty($project_risks)) {
				$project_risks = Set::combine($project_risks, '{n}.RmProjectRiskType.id', '{n}.RmProjectRiskType.title');
				$project_risks = htmlentity($project_risks);
			}
			$project_risks = array_unique($project_risks);
		}
		return $project_risks;
	}

	function user_risk_types($project_id, $user_id) {

		$user_risks = $this->RmProjectRiskType->find('all', [
			'conditions' => [
				'RmProjectRiskType.project_id' => $project_id,
				'RmProjectRiskType.user_id' => $user_id,
			],
		]);

		return (isset($user_risks) && !empty($user_risks)) ? $user_risks : false;
	}

	function project_user_risk_types($project_id) {

		$user_risks = $this->RmProjectRiskType->find('all', [
			'conditions' => [
				'RmProjectRiskType.project_id' => $project_id,
			],
		]);

		return (isset($user_risks) && !empty($user_risks)) ? $user_risks : false;
	}

	function get_risk_users($risk_id, $user_id) {

		$risk_users = $this->RmUser->find('all', [
			'conditions' => [
				'RmUser.rm_detail_id' => $risk_id,
				'RmUser.user_id !=' => $user_id,
			],
			'recursive' => -1,
		]);

		return (isset($risk_users) && !empty($risk_users)) ? $risk_users : false;
	}

	function get_risk_elements($risk_id) {

		$risk_elements = $this->RmElement->find('all', [
			'conditions' => [
				'RmElement.rm_detail_id' => $risk_id,
			],
			'recursive' => -1,
		]);

		return (isset($risk_elements) && !empty($risk_elements)) ? $risk_elements : false;
	}

	function get_project_users($project_id, $user_id = null, $level = null) {

		$user_id = (isset($user_id) && !empty($user_id)) ? $user_id : $this->user_id;
		$view = new View();
		$objView = $view;

		$project_users = [];
		$project_all_users = $objView->loadHelper('Permission')->project_all_users($project_id);
		if(isset($project_all_users) && !empty($project_all_users)){
			$project_users = Set::extract($project_all_users, '{n}.user_details.user_id');
		}
		
		//natcasesort($project_users);
		//pr($project_users);

		/*pr($project_all_users);
		$owner = $objView->loadHelper('Common')->ProjectOwner($project_id, $user_id);

		$participants = participants($project_id, $owner['UserProject']['user_id']);

		$participants_owners = participants_owners($project_id, $owner['UserProject']['user_id']);

		$participantsGpOwner = participants_group_owner($project_id);

		$participantsGpSharer = participants_group_sharer($project_id);

		$participants = isset($participants) ? array_filter($participants) : $participants;

		$participants_owners = isset($participants_owners) ? array_filter($participants_owners) : $participants_owners;

		$participantsGpOwner = isset($participantsGpOwner) ? array_filter($participantsGpOwner) : $participantsGpOwner;

		$participantsGpSharer = isset($participantsGpSharer) ? array_filter($participantsGpSharer) : $participantsGpSharer;

		$project_users = [];
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
		pr($project_users);*/

		return $project_users;
	}

	function get_project_elements($project_id, $user_id = null) {

		$query = "SELECT Element.id as id, Element.title as title,Element.sign_off   FROM user_permissions
						INNER JOIN
						elements as Element
						ON	Element.id=user_permissions.element_id and Element.sign_off !=1
		WHERE user_id = $user_id and project_id = $project_id and `element_id` IS NOT NULL";

		 $this->loadModel('UserPermission');
		$project_elements = $this->UserPermission->query($query);
		$elements = [];
		if(isset($project_elements) && !empty($project_elements)) {
			foreach($project_elements as $key => $val){
				$elements[] = $val['Element']['id'];

			}
		}

		return $elements;
	}

	function get_project_elements_old($project_id, $user_id = null) {

		$e_permission = null;
		$project_level = 0;
		$user_id = (isset($user_id) && !empty($user_id)) ? $user_id : $this->user_id;

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
		pr($e_permission, 1);
		return $e_permission;
	}

//****************** Email Functions *********************//

	public function notificationEmail($user_id = null, $risk_id = null, $project_id = null, $email_type = 'risk_assignment', $projectName = null, $rmTitle=null) {
		// return true;
		if (isset($risk_id) && !empty($risk_id) && isset($user_id) && !empty($user_id) && isset($project_id) && !empty($project_id)) {

			$pageAction = SITEURL."projects/index/$project_id/tab:risk";
			$loggedInUser = '';
			if( !empty($this->Session->read('Auth.User.id')) ){
				$loginuse = $this->User->findById($this->Session->read('Auth.User.id'));
				$loggedInUser = $loginuse['UserDetail']['first_name'] . ' ' . $loginuse['UserDetail']['last_name'];
			}

			$usersDetails = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
			$send_to = $usersDetails['UserDetail']['first_name'] . ' ' . $usersDetails['UserDetail']['last_name'];

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
					$email->viewVars(array('risk_name' => $rmTitle, 'custname' => $send_to, 'changedBy' => $loggedInUser, 'projectName' => $projectName,'open_page' => $pageAction));
					$email->send();

				}

				if ($email_type == 'risk_signedoff') {

					$email->subject(SITENAME . ': Risk sign-off');
					$email->template('risk_signedoff_email');
					$email->viewVars(array('risk_name' => $rmTitle, 'custname' => $send_to, 'changedBy' => $loggedInUser, 'projectName' => $projectName,'open_page' => $pageAction));
					$email->send();
				}

				if ($email_type == 'risk_overdue') {

					$email->subject(SITENAME . ': Risk overdue');
					$email->template('risk_overdue_email');
					$email->viewVars(array('risk_name' => $rmTitle, 'custname' => $send_to, 'projectName' => $projectName,'open_page' => $pageAction));
					$email->send();

				}

			}
			return true;
		}
	}
	//Risk Overdue set by Cron
	public function emailNotificationOverdueCron() {
		$this->layout = false;
		$this->autoRender = false;
		$risks = $this->RmDetail->find('all', array('recursive' => -1));

		foreach ($risks as $key => $value) {
			$data = $value['RmDetail'];
			// pr($risks, 1);
			$today = date("Y-m-d");
			if (isset($data['possible_occurrence']) && !empty($data['possible_occurrence'])) {
				$overdue = date('Y-m-d', strtotime($data['possible_occurrence']));

				$dd = strtotime($data['possible_occurrence']);
				$current = strtotime(date("Y-m-d"));
				$datediff = $dd - $current;
 				$difference = floor($datediff/(60*60*24));

				if ($difference == -1) {
					$this->RmDetail->id = $data['id'];
					$this->RmDetail->saveField("status", 4);
					$this->RmDetail->id = null;
					//send email
					$riskUsers = $this->RmUser->find('all', array('conditions' => array('RmUser.rm_detail_id' => $data['id']), 'recursive' => -1));

					$projectName = strip_tags(getFieldDetail('Project', $data['project_id'], 'title'));
					$rmTitle = htmlentities(getFieldDetail('RmDetail', $data['id'], 'title'),ENT_QUOTES, "UTF-8");


					if (isset($riskUsers) && !empty($riskUsers)) {
						foreach ($riskUsers as $key => $value) {
							$user_id = $value['RmUser']['user_id'];
							$risk_id = $value['RmUser']['rm_detail_id'];
							//Risk Overdue
							$this->notificationEmail($user_id, $risk_id, $data['project_id'], 'risk_overdue',$projectName,$rmTitle);
						}

						/************** socket messages **************/
						if (SOCKET_MESSAGES) {
							$r_users = $riskUsers;
							$open_users = null;
							if (isset($r_users) && !empty($r_users)) {
								foreach ($r_users as $key1 => $value1) {
									if (web_notify_setting($value1['RmUser']['user_id'], 'riskcenter', 'risk_overdue')) {
										$open_users[] = $value1['RmUser']['user_id'];
									}
								}
							}
							$content = [
								'notification' => [
									'type' => 'risk_overdue',
									'created_id' => '',
									'project_id' => $data['project_id'],
									'refer_id' => $risk_id,
									'creator_name' => '',
									'subject' => 'Risk overdue',
									'heading' => 'Risk: ' . $rmTitle,
									'sub_heading' => 'Project: ' . $projectName
								],
							];
							if (is_array($open_users)) {
								$content['received_users'] = array_values($open_users);
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

				}

			}
		}
		return true;
	}
	/*========================================================================================*/

	public function delete_an_item($risk_id = null, $edit = null, $summary = null) {

		if ($this->request->isAjax()) {

			$this->layout = false;
			$viewData = null;
			$viewData['risk_id'] = $risk_id;
			$viewData['edit'] = (isset($edit) && !empty($edit)) ? true : false;
			$viewData['summary'] = (isset($summary) && !empty($summary)) ? true : false;
			$viewData['project_id'] = risk_project($risk_id);

			$this->set($viewData);
			$this->render('/Risks/partials/delete_an_item');

		}
	}

	//****************** Dummy Functions *********************//
	/*public function get_files() {
		$this->layout = 'inner';
		$this->set('title_for_layout', __('Dummy Page', true));

		$projects = $this->Project->find('all', [
			'fields' => ['image_file'],
		]);

		$files_sizes = 0;
		if (isset($projects) && !empty($projects)) {
			foreach ($projects as $key => $value) {
				$data = $value['Project'];
				$image = $data['image_file'];
				if (!empty($image) && file_exists(PROJECT_IMAGE_PATH . $image)) {
					$filename = PROJECT_IMAGE_PATH . $image;
					$files_sizes += filesize($filename);
				}
			}
		}
		e(humanFileSize($files_sizes));
		pr($files_sizes, 1);
	}



	public function index2($project_id = null, $element_id = null, $my = null) {


		if (isset($project_id) && !empty($project_id) ) {
			if(!dbExists('Project', $project_id)){
				$this->redirect(array('controller' => 'risks', 'action' => 'index'));
			}
		}
		$this->layout = 'inner';
		$this->set('title_for_layout', __('Risk Center', true));
		$this->set('page_heading', __('Risk Center', true));
		$this->set('page_subheading', __('View Risks in your Projects', true));

		$viewData = [];


		$crumb = [
			'last' => [
				'data' => [
					'title' => 'Risk Center',
					'data-original-title' => 'Risk Center',
				],
			],
		];
		$this->set($viewData);
		$this->set('crumb', $crumb);
		$this->set('project_id', $project_id);

		$query_params['project_id'] = (isset($project_id) && !empty($project_id) ) ? [$project_id] : null;
		$query_params['element_id'] = (isset($element_id) && !empty($element_id) ) ? $element_id : null;
		$query_params['my_risks'] = (isset($my) && !empty($my) ) ? $my : null;
		$this->setJsVar('my_risks', $my);

		$user_risk_projects = $this->objView->loadHelper('Permission')->user_risk_projects($this->user_id);
		if (isset($user_risk_projects) && !empty($user_risk_projects)) {
			$projects = Set::combine($user_risk_projects, '{n}.p.id', '{n}.p.ptitle');
		}

		if (isset($this->params['named']['risk']) && !empty($this->params['named']['risk'])) {
			$param_risk_id = $this->params['named']['risk'];
			$query_params['risk_id'] = $param_risk_id;
		}

		$param_wsp_id = null;
		if (isset($this->params['named']['wsp']) && !empty($this->params['named']['wsp'])) {
			$param_wsp_id = $this->params['named']['wsp'];
			$elementids = $this->objView->loadHelper('Permission')->wspTasks($project_id, $param_wsp_id);
			if( isset($elementids) && !empty($elementids) ){
				$element_ids = Set::extract($elementids, '/elements/id');
				$query_params['element_id'] = $element_ids;
			}
		}
		$param_exposure = null;
		if (isset($this->params['named']['exposure']) && !empty($this->params['named']['exposure'])) {
			$param_exposure = ucfirst($this->params['named']['exposure']);
			$query_params['exposure'] = $param_exposure;
		}
		$program_id = null;
		if (isset($this->params['named']['program']) && !empty($this->params['named']['program'])) {
			$program_id = $this->params['named']['program'];
			$program_projects = $this->objView->loadHelper('Permission')->program_projects($program_id);
			if (isset($program_projects) && !empty($program_projects)) {
				$projects = Set::combine($program_projects, '{n}.p.id', '{n}.p.ptitle');
				$query_params['project_id'] = Set::extract($program_projects, '{n}.p.id');
			}
		}

		$list = $this->objView->loadHelper('Permission')->risk_list($this->user_id, $query_params, $this->risk_offset);
		$this->set('list', $list);
		$this->set('projects', $projects);

		$risk_types = $this->objView->loadHelper('Scratch')->risk_types($project_id);
		$this->set('risk_types', $risk_types);

		$this->set('param_wsp_id', $param_wsp_id);
		$this->set('param_exposure', $param_exposure);
		$this->set('param_program_id', $program_id);
		$this->set('risk_offset', $this->risk_offset);

		$this->setJsVar('param_wsp_id', $param_wsp_id);
		$this->setJsVar('param_program_id', $program_id);
		$this->setJsVar('risk_offset', $this->risk_offset);
	}

	public function risk_list2() {

		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				// pr($post, 1);
				$query_params['page'] = (isset($post['page']) && !empty($post['page'])) ? $post['page'] : 0;
				$query_params['order'] = (isset($post['order']) && !empty($post['order'])) ? $post['order'] : 'ASC';
				$query_params['coloumn'] = (isset($post['coloumn']) && !empty($post['coloumn'])) ? $post['coloumn'] : 'rdate';
				// $search_text = (isset($post['search_text']) && !empty($post['search_text'])) ? $post['search_text'] : '';

				if (isset($post['param_wsp_id']) && !empty($post['param_wsp_id'])) {
					$param_wsp_id = $post['param_wsp_id'];
					$elementids = $this->objView->loadHelper('Permission')->wspTasks($project_id, $param_wsp_id);
					if( isset($elementids) && !empty($elementids) ){
						$element_ids = Set::extract($elementids, '/elements/id');
						$query_params['element_id'] = $element_ids;
					}
				}
				if (isset($post['param_exposure']) && !empty($post['param_exposure'])) {
					$param_exposure = ucfirst($post['param_exposure']);
					$query_params['exposure'] = $param_exposure;
				}
				if (isset($post['param_program_id']) && !empty($post['param_program_id'])) {
					$program_id = $post['param_program_id'];
					$program_projects = $this->objView->loadHelper('Permission')->program_projects($program_id);
					if (isset($program_projects) && !empty($program_projects)) {
						$query_params['project_id'] = Set::extract($program_projects, '{n}.p.id');
					}
				}

				if (isset($post['project_id']) && !empty($post['project_id']) && $post['project_id'] != 'all') {
					$query_params['project_id'] = [$post['project_id']];
				}
				if (isset($post['type_id']) && !empty($post['type_id'])) {
					$query_params['type_id'] = $post['type_id'];
				}
				if (isset($post['status']) && !empty($post['status'])) {
					$query_params['status'] = $post['status'];
				}
				if (isset($post['impact']) && !empty($post['impact'])) {
					$query_params['impact'] = $post['impact'];
				}
				if (isset($post['probability']) && !empty($post['probability'])) {
					$query_params['probability'] = $post['probability'];
				}
				if (isset($post['exposure']) && !empty($post['exposure'])) {
					$query_params['exposure'] = $post['exposure'];
				}
				if (isset($post['my_risks']) && !empty($post['my_risks'])) {
					$query_params['my_risks'] = $post['my_risks'];
				}
				if (isset($post['risk_id']) && !empty($post['risk_id'])) {
					$query_params['risk_id'] = $post['risk_id'];
				}
				// pr($query_params, 1);
				// $projects = $this->find_data();
				// $projects = $this->find_data($search_text, $page, ['coloumn' => $coloumn, 'order' => $order], $filters);
				$viewData['list'] = $this->objView->loadHelper('Permission')->risk_list($this->user_id, $query_params, $this->risk_offset);
				$this->set($viewData);

			}
			$this->render('/Risks/partials/risk_list2');

		}
	}


	public function risk_row() {

		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				// pr($post, 1);
				$query_params['page'] = (isset($post['page']) && !empty($post['page'])) ? $post['page'] : 0;
				$query_params['order'] = (isset($post['order']) && !empty($post['order'])) ? $post['order'] : 'ASC';
				$query_params['coloumn'] = (isset($post['coloumn']) && !empty($post['coloumn'])) ? $post['coloumn'] : 'rdate';
				// $search_text = (isset($post['search_text']) && !empty($post['search_text'])) ? $post['search_text'] : '';

				if (isset($post['param_wsp_id']) && !empty($post['param_wsp_id'])) {
					$param_wsp_id = $post['param_wsp_id'];
					$elementids = $this->objView->loadHelper('Permission')->wspTasks($project_id, $param_wsp_id);
					if( isset($elementids) && !empty($elementids) ){
						$element_ids = Set::extract($elementids, '/elements/id');
						$query_params['element_id'] = $element_ids;
					}
				}
				if (isset($post['param_exposure']) && !empty($post['param_exposure'])) {
					$param_exposure = ucfirst($post['param_exposure']);
					$query_params['exposure'] = $param_exposure;
				}
				if (isset($post['param_program_id']) && !empty($post['param_program_id'])) {
					$program_id = $post['param_program_id'];
					$program_projects = $this->objView->loadHelper('Permission')->program_projects($program_id);
					if (isset($program_projects) && !empty($program_projects)) {
						$query_params['project_id'] = Set::extract($program_projects, '{n}.p.id');
					}
				}

				if (isset($post['project_id']) && !empty($post['project_id'])) {
					$query_params['project_id'] = [$post['project_id']];
				}
				if (isset($post['type_id']) && !empty($post['type_id'])) {
					$query_params['type_id'] = $post['type_id'];
				}
				if (isset($post['status']) && !empty($post['status'])) {
					$query_params['status'] = $post['status'];
				}
				if (isset($post['impact']) && !empty($post['impact'])) {
					$query_params['impact'] = $post['impact'];
				}
				if (isset($post['probability']) && !empty($post['probability'])) {
					$query_params['probability'] = $post['probability'];
				}
				if (isset($post['exposure']) && !empty($post['exposure'])) {
					$query_params['exposure'] = $post['exposure'];
				}
				if (isset($post['my_risks']) && !empty($post['my_risks'])) {
					$query_params['my_risks'] = $post['my_risks'];
				}
				if (isset($post['risk_id']) && !empty($post['risk_id'])) {
					$query_params['risk_id'] = $post['risk_id'];
				}
				// pr($query_params, 1);
				// $projects = $this->find_data();
				// $projects = $this->find_data($search_text, $page, ['coloumn' => $coloumn, 'order' => $order], $filters);
				$viewData['list'] = $this->objView->loadHelper('Permission')->risk_list($this->user_id, $query_params, $this->risk_offset);
				$this->set($viewData);

			}
			$this->render('/Risks/partials/risk_row');

		}
	}*/

	public function paging_count() {
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';

			$data = [];
			$count = 0;
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;

				if ((isset($post['project_id']) && !empty($post['project_id'])) || (isset($post['my_risks']) && !empty($post['my_risks']))) {
					if($post['project_id'] == 'all'){
						$query_params['project_id'] = 'all';
					}
					else if($post['project_id'] == 'my'){
						unset($post['project_id']);
						$query_params['my_risks'] = 1;
					}
					else if(isset($post['project_id']) && !empty($post['project_id'])){
						$query_params['project_id'] = [$post['project_id']];
					}


					$query_params['page'] = (isset($post['page']) && !empty($post['page'])) ? $post['page'] : 0;
					$query_params['order'] = (isset($post['order']) && !empty($post['order'])) ? $post['order'] : 'ASC';
					$query_params['coloumn'] = (isset($post['coloumn']) && !empty($post['coloumn'])) ? $post['coloumn'] : 'rdate';
					// $search_text = (isset($post['search_text']) && !empty($post['search_text'])) ? $post['search_text'] : '';

					if (isset($post['param_wsp_id']) && !empty($post['param_wsp_id'])) {
						$param_wsp_id = $post['param_wsp_id'];
						$elementids = $this->objView->loadHelper('Permission')->wspTasks($project_id, $param_wsp_id);
						if( isset($elementids) && !empty($elementids) ){
							$element_ids = Set::extract($elementids, '/elements/id');
							$query_params['element_id'] = $element_ids;
						}
					}
					if (isset($post['param_exposure']) && !empty($post['param_exposure'])) {
						$param_exposure = ucfirst($post['param_exposure']);
						$query_params['exposure'] = $param_exposure;
					}
					if (isset($post['param_program_id']) && !empty($post['param_program_id'])) {
						$program_id = $post['param_program_id'];
						$program_projects = $this->objView->loadHelper('Permission')->program_projects($program_id);
						if (isset($program_projects) && !empty($program_projects)) {
							$query_params['project_id'] = Set::extract($program_projects, '{n}.p.id');
						}
					}


					if (isset($post['type_id']) && !empty($post['type_id'])) {
						$query_params['type_id'] = $post['type_id'];
					}
					if (isset($post['status']) && !empty($post['status'])) {
						$query_params['status'] = $post['status'];
					}
					if (isset($post['impact']) && !empty($post['impact'])) {
						$query_params['impact'] = $post['impact'];
					}
					if (isset($post['probability']) && !empty($post['probability'])) {
						$query_params['probability'] = $post['probability'];
					}
					if (isset($post['exposure']) && !empty($post['exposure'])) {
						$query_params['exposure'] = $post['exposure'];
					}
					if (isset($post['my_risks']) && !empty($post['my_risks'])) {
						$query_params['my_risks'] = $post['my_risks'];
					}

					/*$filter_query = '';
					if(isset($search_text) && !empty($search_text)){
						$seperator = '^';
						$search_str = Sanitize::escape(like($search_text, $seperator ));
						$filter_query = " AND (ptitle LIKE '%$search_str%' ESCAPE '$seperator') ";
					}*/
					// pr($query_params, 1);

					$data = $this->objView->loadHelper('Permission')->risk_list($this->user_id, $query_params);
					$count = count($data);
				}

			}
			echo json_encode($count);
			exit;
		}
	}

	public function type_by_project() {
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';

			$data = [];
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				// pr($post, 1);

				$project_id = (isset($post['project_id']) && !empty($post['project_id'])) ? $post['project_id'] : 0;
				// $data = $this->objView->loadHelper('Permission')->type_by_project($this->user_id, $project_id);
				pr($data);

			}
			echo json_encode($data);
			exit;
		}
	}

	public function risk_detail($risk_id = null) {

		if ($this->request->isAjax()) {

			$this->layout = false;
			$user_id = $this->user_id;
			$viewData = null;

			$viewData['risk_id'] = $risk_id;
			$query_params['risk_id'] = $risk_id;
			$viewData['detail'] = $this->objView->loadHelper('Permission')->risk_detail($this->user_id, $risk_id);

			$this->set($viewData);
			$this->render('/Risks/partials/risk_detail');

		}
	}

	public function index($project_id = null, $element_id = null, $my = null) {


		if (isset($project_id) && !empty($project_id) ) {
			if(!dbExists('Project', $project_id)){
				$this->redirect(array('controller' => 'risks', 'action' => 'index'));
			}
		}
		$this->layout = 'inner';
		$this->set('title_for_layout', __('Risk Center', true));
		$this->set('page_heading', __('Risk Center', true));
		$this->set('page_subheading', __('View Risks in your Projects', true));

		$viewData = $projects = [];

		$crumb = [
			'last' => [
				'data' => [
					'title' => 'Risk Center',
					'data-original-title' => 'Risk Center',
				],
			],
		];
		$this->set($viewData);
		$this->set('crumb', $crumb);
		$this->set('project_id', $project_id);


		$query_params = [];
		$customProject = '';
		$myProject = 0;

		if(isset($project_id) && !empty($project_id) ){
			$query_params['project_id'] = [$project_id];
			$passed_project_id = $project_id;
		}
		if (isset($this->params['named']['project']) && !empty($this->params['named']['project'])) {
			$customProject = $this->params['named']['project'];
			$query_params['project_id'] = $customProject;
			if($this->params['named']['project'] == 'my') {
				$myProject = 1;
				$query_params['my_risks'] = 1;
			}
			$passed_project_id = $customProject;
			$query_params['project_id'] = $customProject;
		}
		if(isset($element_id) && !empty($element_id) ){
			$query_params['element_id'] = [$element_id];
		}
		$this->setJsVar('myProject', $myProject);
		$this->set('customProject', $customProject);

		$user_projects = $this->objView->loadHelper('Scratch')->user_projects($this->user_id);
		if (isset($user_projects) && !empty($user_projects)) {
			$projects = Set::combine($user_projects, '{n}.p.id', '{n}.p.title');
			$projects = array_map(function ($v) {
				return htmlentities($v, ENT_QUOTES, "UTF-8");
			}, $projects);
		}

		// $this->setJsVar('project_id', $passed_project_id);

		/*$user_risk_projects = $this->objView->loadHelper('Permission')->user_risk_projects($this->user_id);
		if (isset($user_risk_projects) && !empty($user_risk_projects)) {
			$projects = Set::combine($user_risk_projects, '{n}.p.id', '{n}.p.ptitle');
		}*/

		if (isset($this->params['named']['risk']) && !empty($this->params['named']['risk'])) {
			$param_risk_id = $this->params['named']['risk'];
			$query_params['risk_id'] = $param_risk_id;
		}

		$param_wsp_id = null;
		if (isset($this->params['named']['wsp']) && !empty($this->params['named']['wsp'])) {
			$param_wsp_id = $this->params['named']['wsp'];
			$elementids = $this->objView->loadHelper('Permission')->wspTasks($project_id, $param_wsp_id);
			if( isset($elementids) && !empty($elementids) ){
				$element_ids = Set::extract($elementids, '/elements/id');
				$query_params['element_id'] = $element_ids;
			}
		}
		$param_exposure = null;
		if (isset($this->params['named']['exposure']) && !empty($this->params['named']['exposure'])) {
			$param_exposure = ($this->params['named']['exposure']);
			$query_params['exposure'] = $param_exposure;
		}
		$program_id = null;
		if (isset($this->params['named']['program']) && !empty($this->params['named']['program'])) {
			$program_id = $this->params['named']['program'];
			$program_projects = $this->objView->loadHelper('Permission')->program_projects($program_id);
			if (isset($program_projects) && !empty($program_projects)) {
				$projects = Set::combine($program_projects, '{n}.p.id', '{n}.p.ptitle');
				$query_params['project_id'] = Set::extract($program_projects, '{n}.p.id');
			}
		}
		// pr($query_params, 1);
		$no_params = false;
		$list = [];
		if(isset($query_params) && !empty($query_params)){
			$list = $this->objView->loadHelper('Permission')->risk_list($this->user_id, $query_params, $this->risk_offset);
		}
		else{
			$no_params = true;
		}
		$this->set('no_params', $no_params);
		$this->set('list', $list);
		$this->set('projects', $projects);

		$risk_types = $this->objView->loadHelper('Scratch')->risk_types($project_id);
		$this->set('risk_types', $risk_types);

		$this->set('param_wsp_id', $param_wsp_id);
		$this->set('param_exposure', $param_exposure);
		$this->set('param_program_id', $program_id);
		$this->set('risk_offset', $this->risk_offset);

		$this->setJsVar('param_wsp_id', $param_wsp_id);
		$this->setJsVar('param_program_id', $program_id);
		$this->setJsVar('risk_offset', $this->risk_offset);
	}

	public function listing() {

		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			$viewData = [];
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;

				if ((isset($post['project_id']) && !empty($post['project_id'])) || (isset($post['my_risks']) && !empty($post['my_risks']))) {
					if($post['project_id'] == 'all'){
						$query_params['project_id'] = 'all';
					}
					else if($post['project_id'] == 'my'){
						unset($post['project_id']);
						$query_params['my_risks'] = 1;
					}
					else if(isset($post['project_id']) && !empty($post['project_id'])){
						$query_params['project_id'] = [$post['project_id']];
					}
					// $query_params['project_id'] = [$post['project_id']];

					$query_params['page'] = (isset($post['page']) && !empty($post['page'])) ? $post['page'] : 0;
					$query_params['order'] = (isset($post['order']) && !empty($post['order'])) ? $post['order'] : 'ASC';
					$query_params['coloumn'] = (isset($post['coloumn']) && !empty($post['coloumn'])) ? $post['coloumn'] : 'rdate';
					// $search_text = (isset($post['search_text']) && !empty($post['search_text'])) ? $post['search_text'] : '';

					if (isset($post['param_wsp_id']) && !empty($post['param_wsp_id'])) {
						$param_wsp_id = $post['param_wsp_id'];
						$elementids = $this->objView->loadHelper('Permission')->wspTasks($project_id, $param_wsp_id);
						if( isset($elementids) && !empty($elementids) ){
							$element_ids = Set::extract($elementids, '/elements/id');
							$query_params['element_id'] = $element_ids;
						}
					}
					if (isset($post['param_exposure']) && !empty($post['param_exposure'])) {
						$param_exposure =  ($post['param_exposure']);
						$query_params['exposure'] = $param_exposure;
					}
					if (isset($post['param_program_id']) && !empty($post['param_program_id'])) {
						$program_id = $post['param_program_id'];
						$program_projects = $this->objView->loadHelper('Permission')->program_projects($program_id);
						if (isset($program_projects) && !empty($program_projects)) {
							$query_params['project_id'] = Set::extract($program_projects, '{n}.p.id');
						}
					}


					if (isset($post['type_id']) && !empty($post['type_id'])) {
						$query_params['type_id'] = $post['type_id'];
					}
					if (isset($post['status']) && !empty($post['status'])) {
						$query_params['status'] = $post['status'];
					}
					if (isset($post['impact']) && !empty($post['impact'])) {
						$query_params['impact'] = $post['impact'];
					}
					if (isset($post['probability']) && !empty($post['probability'])) {
						$query_params['probability'] = $post['probability'];
					}
					if (isset($post['exposure']) && !empty($post['exposure'])) {
						$query_params['exposure'] = $post['exposure'];
					}
					if (isset($post['my_risks']) && !empty($post['my_risks'])) {
						$query_params['my_risks'] = $post['my_risks'];
					}
					if (isset($post['risk_id']) && !empty($post['risk_id'])) {
						$query_params['risk_id'] = $post['risk_id'];
					}
					/*if($last_selected == 'all' && isset($post['my_risks']) && !empty($post['my_risks'])){
						unset($query_params['my_risks']);
					}*/
					// pr($query_params);

					$viewData['list'] = $this->objView->loadHelper('Permission')->risk_list($this->user_id, $query_params, $this->risk_offset);
				}
				$this->set($viewData);

			}
			$this->render('/Risks/section/listing');

		}
	}

	public function listing_row() {

		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				if (isset($post['risk_id']) && !empty($post['risk_id'])) {
					$query_params['risk_id'] = $post['risk_id'];
				}
				// pr($query_params, 1);
				$viewData['list'] = $this->objView->loadHelper('Permission')->risk_list($this->user_id, $query_params, $this->risk_offset);
				$this->set($viewData);

			}
			$this->render('/Risks/section/listing_row');

		}
	}

	public function selected_risk_types() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$response = [
				'success' => false,
				'content' => [],
			];

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				if(isset($post['project_id']) && !empty($post['project_id']) && ($post['project_id'] == 'all' || $post['project_id'] == 'my')){
					$post['project_id'] = null;
				}

				$allTypes = [];
				// if(isset($post['project_id']) && !empty($post['project_id']) && $post['project_id'] != 'all'){
					$risk_types = $this->objView->loadHelper('Scratch')->risk_types($post['project_id']);
					if(isset($risk_types) && !empty($risk_types)) {
						$risk_types = Set::extract($risk_types, '{n}.rpt.title');
						foreach ($risk_types as $key => $value) {
							$allTypes[] = ['key' => $value, 'value' => $value];
						}
					}
				// }
				$response['content'] = $allTypes;

				$response['success'] = true;
			}

			echo json_encode($response);
			exit();

		}
	}


	public function trash_risk() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$response = [
				'success' => false,
				'content' => null,
			];
			$user_id = $this->user_id;

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				$risk_id = (isset($post['id']) && !empty($post['id'])) ? $post['id'] : null;

				if (isset($risk_id) && !empty($risk_id)) {
					$risk_data = $this->objView->loadHelper('Scratch')->risk_details($risk_id);
					if(isset($risk_data) && !empty($risk_data)){
						$risk_details = $risk_data[0]['risk'];
						$assignee = (!empty($risk_data[0]['ruser']['assignee'])) ? json_decode($risk_data[0]['ruser']['assignee'], true) : [];
						// pr($risk_details, 1);
						$risk_tasks = (!empty($risk_data[0]['rtask']['risk_tasks'])) ? json_decode($risk_data[0]['rtask']['risk_tasks'], true) : [];
						if(isset($risk_tasks) && !empty($risk_tasks)){
							$qry = "INSERT INTO `activities` (`project_id`, `workspace_id`, `element_id`, `element_type`, `relation_id`, `user_id`, `updated_user_id`, `message`, `updated`) VALUES ";
        					$qry_arr = [];
            				foreach ($risk_tasks as $key => $value) {
            					$qry_arr[] = "('".$risk_details['project_id']."', '".$value['wsp_id']."', '".$value['id']."', 'risk_elements', '".$risk_details['id']."', '".$user_id."', '".$user_id."', 'Task Risk deleted', '".date("Y-m-d H:i:s")."')";
            				}
            				$qry .= implode(' ,', $qry_arr);
            				$this->RmDetail->query($qry);
						}
						$risk_message = 'Risk deleted';
						$task_data = [
							'project_id' => $risk_details['project_id'],
							'element_type' => 'rm_details',
							'updated_user_id' => $user_id,
							'message' => $risk_message,
							'updated' => date("Y-m-d H:i:s"),
						];
						$this->loadModel('ProjectActivity');
						$this->ProjectActivity->save($task_data);
						$r_users = [];
						$users_on_project = $this->objView->loadHelper('Permission')->projectOwners($risk_details['project_id']);
						if(isset($users_on_project) && !empty($users_on_project)) {
							$users_on_project = Set::extract($users_on_project, '{n}.user_details.user_id');
							$risk_members = Set::extract($assignee, '{n}.id');
							if(isset($risk_members) && !empty($risk_members)){
								$r_users = array_unique(array_merge($risk_members, $users_on_project));
							}
						}

						if (isset($r_users) && !empty($r_users)) {
							if (($key = array_search($risk_details['creator_id'], $r_users)) == false) {
								$r_users[] = $risk_details['creator_id'];
							}
							if (($key = array_search($user_id, $r_users)) !== false) {
								unset($r_users[$key]);
							}
							$r_open_users = null;
							$pageAction = SITEURL."projects/index/".$risk_details['project_id']."/tab:risk";
							if (SOCKET_MESSAGES) {
								if(isset($r_users) && !empty($r_users)) {
									$creator_data = $this->objView->loadHelper('Scratch')->user_notify($risk_details['creator_id']);
									$creator_data = array_merge($creator_data[0]['u'], $creator_data[0][0], $creator_data[0]['en']);
									$assignee[] = $creator_data;
									$riskDeletedUser = $this->Session->read('Auth.User.UserDetail.first_name') . ' ' . $this->Session->read('Auth.User.UserDetail.last_name');
									foreach ($assignee as $key => $value) {
										if(in_array($value['id'], $r_users)) {

											$riskDeleteNoti = $this->EmailNotification->find('first', ['conditions' => ['notification_type' => 'riskcenter', 'personlization' => 'risk_deleted', 'user_id' => $value]]);

											if ((!isset($value['notifiation_email']) || $value['notifiation_email'] == 1) && (!isset($value['email_notification']) || $value['email_notification'] == 1)) {

												$email = new CakeEmail();
												$email->config('Smtp');
												$email->from(array(ADMIN_FROM_EMAIL => SITENAME));
												$email->to($value['email']);
												$email->subject(SITENAME . ': Risk deleted');
												$email->template('risk_delete_email');
												$email->emailFormat('html');
												$email->viewVars(array('risk_name' => $risk_details['title'], 'project_name' => $risk_details['ptitle'], 'owner_name' => $value['title'], 'deletedby' => $riskDeletedUser, 'open_page' => $pageAction));
												$email->send();
											}
										}
									}
								}
								$content = [
									'notification' => [
										'type' => 'risk_delete',
										'created_id' => $this->user_id,
										'project_id' => $risk_details['project_id'],
										'creator_name' => $risk_details['creator_name'],
										'subject' => 'Risk deleted',
										'heading' => 'Risk: ' . htmlentities($risk_details['title'],ENT_QUOTES, "UTF-8"),
										'sub_heading' => 'Project: ' . strip_tags(getFieldDetail('Project', $risk_details['project_id'], 'title')),
										'date_time' => timezoneTimeConvertor($this->user_id, date('Y-m-d g:iA'), $format = 'd M Y g:iA') . user_country($this->user_id),
									],
								];
								foreach ($r_users as $key => $value1) {
									if (web_notify_setting($value1, 'riskcenter', 'risk_deleted')) {
										$r_open_users[] = $value1;
									}
								}
								if (is_array($r_open_users)) {
									$content['received_users'] = array_values($r_open_users);
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
						}
					}
					if ($this->RmDetail->delete($risk_id)) {
						$response['success'] = true;
					}
				}
			}

			echo json_encode($response);
			exit();

		}
	}

	public function update_my_risk() {
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';

			$data = [];
			$user_risks = 0;
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;

				$user_risks = my_risks($this->Session->read("Auth.User.id"), 'my');
				$user_risks = (isset($user_risks) && !empty($user_risks)) ? count($user_risks) : 0;

			}
			echo json_encode($user_risks);
			exit;
		}
	}

}
