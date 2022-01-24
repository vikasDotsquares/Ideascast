<?php

App::uses('Controller/Component', 'Auth', 'Session', 'RequestHandler');
App::uses('Sanitize', 'Utility');
App::uses('CakeEmail', 'Network/Email');
// App::import('Vendor', 'Classes/MPDF56/mpdf');
App::import('Vendor', 'MPDF56/PhpWord');
App::import('Vendor', 'PHPExcel');

// App::import('Lib', 'Communications');
class CostsController extends AppController {

	public $name = 'Costs';
	public $uses = [
		'User',
		'UserDetail',
		'ProjectPermission',
		'Category',
		'UserProject',
		'Project',
		'Workspace',
		'Area',
		'ProjectWorkspace',
		'Element',
		'ElementCost',
		'UserElementCost',
		'ElementCostComment',
		'ElementCostHistory',
		'WorkspaceCostComment',
		'ProjectCostComment',
	];
	public $objView = null;
	public $user_id = null;
	public $components = array(
		'Common', 'PhpExcel',
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

	public function index() {
		$this->layout = 'inner';
		$this->set('title_for_layout', __('Cost Center', true));
		$this->set('page_heading', __('Cost Center', true));
		$this->set('page_subheading', __('Plan and manage Project labor costs', true));

		$viewVars = [];

		$project_id = $project_type = '';
		if (isset($this->params->named['m_project']) && !empty($this->params->named['m_project'])) {
			$project_type = 'm_project';
			$project_id = $this->params->named['m_project'];
		} else if (isset($this->params->named['r_project']) && !empty($this->params->named['r_project'])) {
			$project_type = 'r_project';
			$project_id = $this->params->named['r_project'];
		} else if (isset($this->params->named['g_project']) && !empty($this->params->named['g_project'])) {
			$project_type = 'g_project';
			$project_id = $this->params->named['g_project'];
		}

		$viewData['project_id'] = $project_id;

		$this->Project->unbindModel(['hasMany' => ['ProjectPermission']]);

		$projects = [];
		$mprojects = $this->Project->UserProject->find('list', array('conditions' => ['UserProject.user_id' => $this->user_id, 'UserProject.owner_user' => 1], 'recursive' => 1, 'fields' => ['Project.id', 'Project.title']));
		//============================================================
		$rprojects = get_rec_projects($this->user_id, 1);
		$gprojects = group_rec_projects($this->user_id, 1);

		if (isset($mprojects) && !empty($mprojects)) {
			$projects = $projects + $mprojects;
		}
		if (isset($rprojects) && !empty($rprojects)) {
			$projects = $projects + $rprojects;
		}
		if (isset($gprojects) && !empty($gprojects)) {
			$projects = $projects + $gprojects;
		}

		if (isset($projects) && !empty($projects)) {
			/*$projects = array_map("strip_tags", $projects);
			$projects = array_map("trim", $projects);*/
			$projects = array_map(function ($v) {
				return htmlentities($v, ENT_COMPAT, "UTF-8");
			}, $projects);
			natcasesort($projects);
		}
		$viewData['my_projects'] = $projects;
		//==========================================================
		$crumb = [
			'last' => [
				'data' => [
					'title' => 'Cost Center',
					'data-original-title' => 'Cost Center',
				],
			],
		];
		$this->set($viewData);
		$this->set('crumb', $crumb);
	}

	public function tree() {
		$response = ['success' => false];
		if ($this->request->isAjax()) {

			$this->layout = false;
			$html = '';
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				// pr($post, 1);
				$project_id = (isset($post['project']) && !empty($post['project'])) ? $post['project'] : null;
				$view = new View($this, false);
				$view->viewPath = 'Costs/partials';
				$view->set("project_id", $project_id);
				$html = $view->render('tree');
			}
			echo json_encode($html);
			exit;
		}
	}

	public function save_project_budget($project_id = null) {
		$response = ['success' => false];
		if ($this->request->isAjax()) {

			$this->layout = false;
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				$data = ['id' => $project_id, 'budget' => $post['budget']];
				if ($this->Project->save($data)) {


					$task_data = [
						'project_id' => $project_id,
						'updated_user_id' => $this->user_id,
						'message' => 'Budget updated',
						'updated' => date("Y-m-d H:i:s"),
					];
					$this->loadModel('ProjectActivity');
					$this->ProjectActivity->id = null;
					$this->ProjectActivity->save($task_data);


					$response['success'] = true;
				}
			}
			echo json_encode($response);
			exit;
		}
	}


	public function element_users($element_id = null, $cost_type = null) {
		$response = ['success' => false];
		if ($this->request->isAjax()) {

			$this->layout = false;
			// if ($this->request->is('post') || $this->request->is('put')) {
			// 	$post = $this->request->data;
			// 	$element_id = $post['element_id'];
				$element_project = element_project($element_id);

				// $cost_type = 1;
				$user_element_cost = $this->objView->loadHelper('ViewModel')->user_element_cost($element_id, $cost_type);
				$element_cost_users = (isset($user_element_cost) && !empty($user_element_cost)) ? Set::extract($user_element_cost, '/UserElementCost/user_id') : [];

				$element_user_rates = $this->objView->loadHelper('Permission')->element_user_rates($element_project, $element_id, $element_cost_users);
				$this->set("element_user_rates", $element_user_rates);
			// }
		}
		$this->render(DS . 'Costs' . DS . 'partials' . DS . 'element_users');
	}

	public function task_details($element_id = null, $cost_type = 1) {
		$response = ['success' => false];
		if ($this->request->isAjax()) {

			$this->layout = false;
			$html = '';
			if ($this->request->is('post') || $this->request->is('put')) {

				$view = new View($this, false);
				$view->viewPath = 'Costs/partials';
				$view->set("element_id", $element_id);
				$view->set("cost_type", $cost_type);
				$html = $view->render('task_details');
			}
			echo json_encode($html);
			exit;
		}
	}

	public function user_rates($project_id = null) {
		$response = ['success' => false];
		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$html = '';
			$this->set("project_id", $project_id);
			$this->render(DS . 'Costs' . DS . 'partials' . DS . 'user_rates');
		}
	}


	public function clear_user_rates() {
		$response = ['success' => false];
		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;

				if ((isset($post['project_id']) && !empty($post['project_id'])) && (isset($post['user_id']) && !empty($post['user_id']))) {

					$project_id = $post['project_id'];
					$user_id = $post['user_id'];

					$this->loadModel('UserProjectCost');
					$dsql = "DELETE FROM user_project_costs WHERE (user_id, project_id) IN (($user_id, $project_id))";
					$this->UserProjectCost->query($dsql);
					$response['success'] = true;
				}

			}
			echo json_encode($response);
			exit;
		}
	}

	public function user_rate_list($project_id = null) {
		$response = ['success' => false];
		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$html = '';
			$this->set("project_id", $project_id);
			$this->render(DS . 'Costs' . DS . 'partials' . DS . 'user_rate_list');
		}
	}

	public function save_user_rates() {
		$response = ['success' => false];
		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				// pr($post, 1);

				if ((isset($post['project_id']) && !empty($post['project_id'])) && (isset($post['users']) && !empty($post['users']))) {

					$project_id = $post['project_id'];
					$users = $post['users'];
					$day_rate = (isset($post['day_rate']) && !empty($post['day_rate'])) ? number_format($post['day_rate'], 2, '.', '') : '';
					$hour_rate = (isset($post['hour_rate']) && !empty($post['hour_rate'])) ? number_format($post['hour_rate'], 2, '.', '') : '';
					$this->loadModel('UserProjectCost');

					if(!empty($users)){
						$dsql = "DELETE FROM user_project_costs WHERE (user_id, project_id) IN (";
						$dsql_part = [];
						foreach ($users as $key => $value) {
							$dsql_part[] = "($value, $project_id)";
						}
						$dsql .= implode(',', $dsql_part);
						$dsql .= ")";
						$this->UserProjectCost->query($dsql);
						$date = date('Y-m-d h:i:s');

						$isql = "INSERT INTO user_project_costs (user_id, project_id, day_rate, hour_rate, created, modified) VALUES ";
						$isql_part = [];
						foreach ($users as $key => $value) {
							$isql_part[] = "('$value', '$project_id', '$day_rate', '$hour_rate', '$date', '$date')";
						}
						$isql .= implode(',', $isql_part);
						// pr($isql,1);
						$this->UserProjectCost->query($isql);

						$response['success'] = true;
					}
				}

			}
			echo json_encode($response);
			exit;
		}
	}

	public function more_button() {
		$response = ['success' => false];
		if ($this->request->isAjax()) {

			$this->layout = false;
			$html = '';
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				$project_id = (isset($post['project_id']) && !empty($post['project_id'])) ? $post['project_id'] : null;
				$view = new View($this, false);
				$view->viewPath = 'Costs/partials';
				$view->set("project_id", $project_id);
				$html = $view->render('more_button');
			}
			echo json_encode($html);
			exit;
		}
	}

	protected function update_budget($project_id = null){
		$pquery = "SELECT
						    SUM(if( ec.estimated_cost >0, ec.estimated_cost, 0)) AS escost
						FROM
						    element_costs ec
						WHERE
						    ec.element_id IN(
						    SELECT
						        e.id
						    FROM
						        user_permissions up
						    LEFT JOIN elements e ON
						        e.id = up.element_id
						    WHERE
						        up.project_id = $project_id AND up.element_id IS NOT NULL
						)
			        ";
	    $pdata = $this->ElementCost->query($pquery);
	    // pr($pdata);
	    if(isset($pdata) && !empty($pdata)){
	    	$escost = $pdata[0][0]['escost'];
	    	$this->Project->query("UPDATE `projects` SET budget='$escost' WHERE id = $project_id");
	    }
	}

	public function flush_team_cost($element_id = null) {
		$response = ['success' => false];
		if ($this->request->isAjax()) {

			$this->layout = false;
			$html = 'done';
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				if (isset($element_id) && !empty($element_id)) {
					$element_cost = $this->ElementCost->find('first', [
						'conditions' => [
							'element_id' => $element_id,
							'estimate_spend_flag' => $post['est_spend'],
						],
						'recursive' => -1,
					]);
					$element_cost_id = null;
					if (isset($element_cost) && !empty($element_cost)) {

						$element_cost_id = $element_cost['ElementCost']['id'];

						if ($this->ElementCost->hasAny(['ElementCost.id' => $element_cost_id])) {
							$this->ElementCost->deleteAll(['ElementCost.id' => $element_cost_id]);
							if ($this->UserElementCost->hasAny(['UserElementCost.element_cost_id' => $element_cost_id, 'UserElementCost.estimate_spend_flag' => $post['est_spend']])) {
								$this->UserElementCost->deleteAll(['UserElementCost.element_cost_id' => $element_cost_id, 'UserElementCost.estimate_spend_flag' => $post['est_spend']]);
							}
							$response['success'] = true;
							$project_id = element_project($element_id);
							$this->update_budget($project_id);
						}
					}
				}
			}
			echo json_encode($response);
			exit;
		}
	}

	public function save_team_cost($element_id = null) {
		$response = ['success' => false];
		if ($this->request->isAjax()) {

			$this->layout = false;
			$response = [
				'success' => false,
				'content' => null,
			];
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;

				if (isset($element_id) && !empty($element_id)) {
					$element_cost = $this->ElementCost->find('first', [
						'conditions' => [
							'element_id' => $element_id,
							'estimate_spend_flag' => $post['est_spend'],
						],
						'recursive' => -1,
					]);

					$project_id = element_project($element_id);
					$element_workspace = element_workspace($element_id);
					$project_currency = getByDbId('Project', $project_id, ['currency_id']);
					$project_currency_id = $project_currency['Project']['currency_id'];

					$new_el_cost = [
						'element_id' => $element_id,
						'updated_by' => $this->user_id,
						'cost_type_id' => $post['cost_type'],
						'team_member_flag' => $post['team_member'],
						'project_currency_id' => $project_currency_id,
						'estimate_spend_flag' => $post['est_spend'],
					];
					if (isset($element_cost) && !empty($element_cost)) {
						$new_el_cost['id'] = $element_cost['ElementCost']['id'];
					}

					$cost_message = '';
					if ($post['est_spend'] == 1) {
						$new_el_cost['estimated_cost'] = number_format(($post['qty'] * $post['rate']), 2, '.', '');
						$cost_message = 'Task budget updated';
						// unset($element_cost['ElementCost']['spend_cost']);
					} else if ($post['est_spend'] == 2) {
						$cost_message = 'Task actual cost updated';
						$new_el_cost['spend_cost'] = number_format(($post['qty'] * $post['rate']), 2, '.', '');
						// unset($element_cost['ElementCost']['estimated_cost']);
					}
					if ($this->ElementCost->save($new_el_cost)) {
						$element_cost_id = null;
						if (isset($element_cost) && !empty($element_cost)) {
							$element_cost_id = $element_cost['ElementCost']['id'];
							unset($element_cost['ElementCost']['id']);
							unset($element_cost['ElementCost']['created']);
							unset($element_cost['ElementCost']['modified']);
							$this->ElementCostHistory->save($element_cost['ElementCost']);
						} else {
							$element_cost_id = $this->ElementCost->getLastInsertId();
						}
						// pr($element_cost, 1);
						if ($this->UserElementCost->hasAny(['UserElementCost.element_cost_id' => $element_cost_id, 'UserElementCost.estimate_spend_flag' => $post['est_spend']])) {
							$this->UserElementCost->deleteAll(['UserElementCost.element_cost_id' => $element_cost_id, 'UserElementCost.estimate_spend_flag' => $post['est_spend']]);
						}
						$uec_data = [
							'element_cost_id' => $element_cost_id,
							'element_id' => $element_id,
							'user_id' => $this->user_id,
							'estimate_spend_flag' => $post['est_spend'],
							'work_unit' => $post['unit'],
							'work_rate' => $post['rate'],
							'quantity' => $post['qty'],
						];
						$this->UserElementCost->save($uec_data);
						$response['success'] = true;
						$response['content']['id'] = $element_cost_id;


						$task_data = [
							'project_id' => $project_id,
							'workspace_id' => $element_workspace,
							'element_id' => $element_id,
							'element_type' => 'element_costs',
							'user_id' => $this->user_id,
							'updated_user_id' => $this->user_id,
							'message' => $cost_message,
							'updated' => date("Y-m-d H:i:s"),
						];
						$this->loadModel('Activity');
						$this->Activity->id = null;
						$this->Activity->save($task_data);

						$this->update_budget($project_id);
					}
				}
				// pr($post, 1);
			}
			echo json_encode($response);
			exit;
		}
	}

	public function save_member_cost($element_id = null) {
		$response = ['success' => false];
		if ($this->request->isAjax()) {

			$this->layout = false;
			$response = [
				'success' => false,
				'content' => null,
			];
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				// pr($post, 1);
				if (isset($element_id) && !empty($element_id)) {
					$element_cost = $this->ElementCost->find('first', [
						'conditions' => [
							'element_id' => $element_id,
							'estimate_spend_flag' => $post['est_spend'],
						],
						'recursive' => -1,
					]);

					$total_val = 0;
					if(isset($post['combined']) && !empty($post['combined'])){
						foreach ($post['combined'] as $key => $val) {
							$quantity_val = $val['quantity'];
							$rate_val = $val['rate'];
							$total_val += number_format(($rate_val * $quantity_val), 2, '.', '');
						}
					}

					$project_id = element_project($element_id);
					$element_workspace = element_workspace($element_id);
					$project_currency = getByDbId('Project', $project_id, ['currency_id']);
					$project_currency_id = $project_currency['Project']['currency_id'];

					$new_el_cost = [
						'element_id' => $element_id,
						'updated_by' => $this->user_id,
						'team_member_flag' => $post['team_member'],
						'cost_type_id' => $post['cost_type'],
						'project_currency_id' => $project_currency_id,
						'estimate_spend_flag' => $post['est_spend'],
					];
					if (isset($element_cost) && !empty($element_cost)) {
						$new_el_cost['id'] = $element_cost['ElementCost']['id'];
					}

					$cost_message = '';
					if ($post['est_spend'] == 1) {
						$new_el_cost['estimated_cost'] = number_format($total_val, 2, '.', '');
						$cost_message = 'Task budget updated';
					} else if ($post['est_spend'] == 2) {
						$new_el_cost['spend_cost'] = number_format($total_val, 2, '.', '');
						$cost_message = 'Task actual cost updated';
					}

					if ($this->ElementCost->save($new_el_cost)) {
						$element_cost_id = null;
						if (isset($element_cost) && !empty($element_cost)) {
							$element_cost_id = $element_cost['ElementCost']['id'];
							unset($element_cost['ElementCost']['id']);
							$this->ElementCostHistory->save($element_cost['ElementCost']);
						} else {
							$element_cost_id = $this->ElementCost->getLastInsertId();
						}
						if ($this->UserElementCost->hasAny(['UserElementCost.element_cost_id' => $element_cost_id, 'UserElementCost.estimate_spend_flag' => $post['est_spend']])) {
							$this->UserElementCost->deleteAll(['UserElementCost.element_cost_id' => $element_cost_id, 'UserElementCost.estimate_spend_flag' => $post['est_spend']], false);
						}
						if(isset($post['combined']) && !empty($post['combined'])){
							foreach ($post['combined'] as $key => $value) {
								$uec_data = [
									'element_cost_id' => $element_cost_id,
									'element_id' => $element_id,
									'user_id' => $key,
									'estimate_spend_flag' => $post['est_spend'],
									'work_unit' => $value['unit'],
									'work_rate' => $value['rate'],
									'quantity' => $value['quantity'],
								];
								if (isset($user_element_cost) && !empty($user_element_cost)) {
									$uec_data['id'] = $user_element_cost['UserElementCost']['id'];
								}
								$this->UserElementCost->save($uec_data);
								$this->UserElementCost->id = null;
							}
						}
						$response['success'] = true;
						$response['content']['id'] = $element_cost_id;

						$task_data = [
							'project_id' => $project_id,
							'workspace_id' => $element_workspace,
							'element_id' => $element_id,
							'element_type' => 'element_costs',
							'user_id' => $this->user_id,
							'updated_user_id' => $this->user_id,
							'message' => $cost_message,
							'updated' => date("Y-m-d H:i:s"),
						];
						$this->loadModel('Activity');
						$this->Activity->id = null;
						$this->Activity->save($task_data);

						$this->update_budget($project_id);
					}
				}
			}
			echo json_encode($response);
			exit;
		}
	}

	public function get_project_type() {

		$response = ['success' => true, 'content' => null,'showExport'=>false];
		if ($this->request->isAjax()) {
			$this->layout = false;
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				$project_id = $post['project'];
				$user_id = $this->user_id;

				$data = $this->UserProject->find('first', array('conditions' => array('UserProject.user_id' => $user_id, 'UserProject.project_id' => $project_id, 'UserProject.owner_user' => 1)));

				$project_workspace_details = get_project_workspace($project_id );

				// pr($project_workspace_details); die;

				if (isset($project_workspace_details) && !empty($project_workspace_details)) {
					$response['showExport'] = true;
				}

				if (isset($data) & !empty($data)) {
					$response['success'] = true;
					$response['content'] = isset($data) ? "m_project" : "false";
					echo json_encode($response);
					exit;
				}

				/* -----------Group code----------- */
				$this->loadModel('ProjectGroupUser');
				$projectsg = $this->UserProject->find('first', ['recursive' => 2, 'conditions' => ['UserProject.project_id' => $project_id]]);
				if (isset($projectsg) && !empty($projectsg)) {
					$pgupid = $projectsg['UserProject']['id'];
					$conditionsG = null;
					$conditionsG['ProjectGroupUser.user_id'] = $user_id;
					$conditionsG['ProjectGroupUser.user_project_id'] = $pgupid;
					$conditionsG['ProjectGroupUser.approved'] = 1;
					$projects_group_shared_user = $this->ProjectGroupUser->find('first', array(
						'conditions' => $conditionsG,
						'fields' => array('ProjectGroupUser.project_group_id'),
						'recursive' => -1,
					));

					if (isset($projects_group_shared_user) && !empty($projects_group_shared_user)) {
						//echo $project_id." sdsd ".$projects_group_shared_user['ProjectGroupUser']['project_group_id']; die;
						$group_permission = $this->objView->loadHelper('Group')->group_permission_details($project_id, $projects_group_shared_user['ProjectGroupUser']['project_group_id']);
						$response['success'] = true;
						$response['content'] = isset($projects_group_shared_user) ? "g_project" : "false";
						echo json_encode($response);
						exit;
					}
				}
				/* -----------Group code----------- */

				/* -----------sharing code----------- */
				$conditionsN = null;
				$conditionsN['ProjectPermission.user_id'] = $user_id;
				$this->loadModel('ProjectPermission');
				$projects_shared = $this->ProjectPermission->find('all', array(
					'conditions' => $conditionsN,
					'fields' => array('ProjectPermission.user_project_id'),
					'order' => 'ProjectPermission.created DESC',
					'recursive' => -1,
				));
				/* -----------sharing code----------- */

				if ((isset($projects_shared) && !empty($projects_shared)) && empty($projects)) {

					$projectss = $this->UserProject->find('first', ['recursive' => 2, 'conditions' => ['UserProject.project_id' => $project_id]]);

					if (isset($projectss) && !empty($projectss)) {
						$projects = $projectss;
						$counssst = $this->ProjectPermission->find('count', array(
							'conditions' => array('ProjectPermission.user_project_id' => $projectss['UserProject']['id']),
							'fields' => array('ProjectPermission.user_project_id'),
							'order' => 'ProjectPermission.created DESC',
							'recursive' => -1,
						));

						if (isset($counssst) && $counssst > 0) {
							$projects = $this->UserProject->find('first', ['recursive' => 2, 'conditions' => ['UserProject.id' => $projectss['UserProject']['id']]]);
							$response['success'] = true;
							$response['content'] = isset($projects) ? "r_project" : "false";
							echo json_encode($response);
							exit;
						}
					}
				}
			}
		}
		echo json_encode($response);
		exit;

	}
	//======= Annotation Add / Edit / Delete =======================
	public function add_annotate($costType = null, $element_id = null) {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$data = $this->ElementCostComment->find('all', [
				'conditions' => ['ElementCostComment.element_id' => $element_id, 'cost_type' => $costType],
				'order' => ['ElementCostComment.modified DESC'],
			]);

			$elecost = $this->ElementCost->find('first', [
				'conditions' => ['ElementCost.element_id' => $element_id],
			]);

			if ($costType == 2) {
				$tskpboxtitle = 'Task Actual';
			} else {
				$tskpboxtitle = 'Task Budget';
			}

			$this->set('element_id', $element_id);
			$this->set('cost_type', $costType);
			$this->set('tskpboxtitle', $tskpboxtitle);
			$this->set('elementcost', $elecost);
			$this->set('data', $data);

			$this->render(DS . 'Costs' . DS . 'partials' . DS . 'add_annotate');
		}
	}

	public function add_ws_annotate($costType = null, $workspace_id = null) {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$data = $this->WorkspaceCostComment->find('all', [
				'conditions' => ['WorkspaceCostComment.workspace_id' => $workspace_id, 'cost_type' => $costType],
				'order' => ['WorkspaceCostComment.modified DESC'],
			]);

			if ($costType == 2) {
				$wspboxtitle = 'Workspace Actual';
			} else {
				$wspboxtitle = 'Workspace Budget';
			}

			$this->set('workspace_id', $workspace_id);
			$this->set('cost_type', $costType);
			$this->set('wspboxtitle', $wspboxtitle);
			$this->set('data', $data);

			$this->render(DS . 'Costs' . DS . 'partials' . DS . 'add_ws_annotate');
		}
	}

	public function get_wsp_annotations($cost_type = null, $workspace_id = null) {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$response = [
				'success' => true,
				'msg' => '',
				'content' => null,
			];

			$data = $this->WorkspaceCostComment->find('all', [
				'conditions' => ['WorkspaceCostComment.workspace_id' => $workspace_id, 'cost_type' => $cost_type],
				'order' => ['WorkspaceCostComment.modified DESC'],
			]);

			$view = new View($this, false);
			$view->viewPath = 'Costs/partials'; // Directory inside view directory to search for .ctp files
			$view->layout = false; // if you want to disable layout
			$view->set('workspace_id', $workspace_id);
			$view->set('cost_type', $cost_type);
			$view->set('data', $data);

			$html = $view->render('get_wsp_annotations');

			echo json_encode($html);
			exit();
		}
	}

	public function save_wsp_annotate() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$response = [
				'success' => false,
				'msg' => '',
				'content' => null,
			];

			if ($this->request->is('post') || $this->request->is('put')) {

				$this->request->data['WorkspaceCostComment']['comments'] = Sanitize::escape($this->request->data['WorkspaceCostComment']['comments']);

				$this->request->data['WorkspaceCostComment']['comments'] = Sanitize::escape($this->request->data['WorkspaceCostComment']['comments']);

				$this->request->data['WorkspaceCostComment']['comments'] = trim(strip_tags($this->request->data['WorkspaceCostComment']['comments']));

				$this->WorkspaceCostComment->set($this->request->data);
				if ($this->WorkspaceCostComment->validates()) {

					$post = $this->request->data['WorkspaceCostComment'];
					//$this->request->data['WorkspaceCostComment']['comments'] =  utf8_decode($this->request->data['WorkspaceCostComment']['comments']);
					$this->request->data['WorkspaceCostComment']['comments'] = addslashes($this->request->data['WorkspaceCostComment']['comments']);

					//pr($post); die;

					$response['content']['workspace_id'] = $post['workspace_id'];
					$response['content']['cost_type'] = $post['cost_type'];
					// get project currency id ===============================
					$project_id = workspace_pid($post['workspace_id']);
					$currency_id = $this->Project->find('first',
						[
							'conditions' => ['Project.id' => $project_id],
							'fields' => ['Project.currency_id'],

						]
					);
					$post['currency_id'] = isset($currency_id['Project']['currency_id']) ? $currency_id['Project']['currency_id'] : 0;
					//========================================================

					if (isset($post['id']) && !empty($post['id'])) {

						$this->WorkspaceCostComment->id = $post['id'];

						$costvalue = $this->WorkspaceCostComment->find('first', ['conditions' => ['WorkspaceCostComment.id' => $post['id']]]);
						$post['cost'] = $costvalue['WorkspaceCostComment']['cost'];
						$post['currency_id'] = $costvalue['WorkspaceCostComment']['currency_id'];

					}

					if ($this->WorkspaceCostComment->save($post)) {
						$response['success'] = true;
					}
				} else {
					$response['content'] = $this->WorkspaceCostComment->validationErrors;

				}
			}
			echo json_encode($response);
			exit;

		}
	}

	public function delete_wsp_annotate($id = null) {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$response = [
				'success' => true,
				'msg' => '',
				'content' => null,
			];

			if ($this->WorkspaceCostComment->delete($id)) {

				echo json_encode($id);
			}

			exit();

		}
	}

	public function save_annotate() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$response = [
				'success' => false,
				'msg' => '',
				'content' => null,
			];

			if ($this->request->is('post') || $this->request->is('put')) {
				// pr($this->request->data, 1);
				$this->request->data['ElementCostComment']['comments'] = Sanitize::escape($this->request->data['ElementCostComment']['comments']);

				$this->request->data['ElementCostComment']['comments'] = trim(strip_tags($this->request->data['ElementCostComment']['comments']));

				$this->ElementCostComment->set($this->request->data);
				if ($this->ElementCostComment->validates()) {

					$elecost = $this->ElementCost->find('first', [
						'conditions' => ['ElementCost.element_id' => $this->request->data['ElementCostComment']['element_id'], 'ElementCost.estimate_spend_flag' => $this->request->data['ElementCostComment']['cost_type']],
					]);
					// pr($elecost['ElementCost']);

					// get project currency id ===============================
					$project_id = element_project($this->request->data['ElementCostComment']['element_id']);
					$currency_id = $this->Project->find('first',
						[
							'conditions' => ['Project.id' => $project_id],
							'fields' => ['Project.currency_id'],

						]
					);
					//========================================================

					if (isset($this->request->data['ElementCostComment']['element_id']) && $this->request->data['ElementCostComment']['element_id'] > 0) {
						if ($this->request->data['ElementCostComment']['cost_type'] == 1) {
							$this->request->data['ElementCostComment']['cost'] = isset($elecost['ElementCost']['estimated_cost']) ? $elecost['ElementCost']['estimated_cost'] : 0;
						} else {
							$this->request->data['ElementCostComment']['cost'] = isset($elecost['ElementCost']['spend_cost']) ? $elecost['ElementCost']['spend_cost'] : 0;
						}
					}

					// pr($this->request->data);die;

					$post = $this->request->data['ElementCostComment'];
					$post['currency_id'] = isset($currency_id['Project']['currency_id']) ? $currency_id['Project']['currency_id'] : 0;
					$response['content']['element_id'] = $post['element_id'];
					$response['content']['cost_type'] = $post['cost_type'];

					if (isset($post['id']) && !empty($post['id'])) {
						$this->ElementCostComment->id = $post['id'];

						$costvalue = $this->ElementCostComment->find('first', ['conditions' => ['ElementCostComment.id' => $post['id']]]);
						$post['cost'] = $costvalue['ElementCostComment']['cost'];
						//$post['currency_id'] = $costvalue['ElementCostComment']['currency_id'];

					}
					//pr($post);
					if ($this->ElementCostComment->save($post)) {
						$response['success'] = true;
					}
				} else {
					$response['content'] = $this->ElementCostComment->validationErrors;

				}
			}
			echo json_encode($response);
			exit;

		}
	}

	public function get_annotations($cost_type = null, $element_id = null) {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$response = [
				'success' => true,
				'msg' => '',
				'content' => null,
			];

			$data = $this->ElementCostComment->find('all', [
				'conditions' => ['ElementCostComment.element_id' => $element_id, 'cost_type' => $cost_type],
				'order' => ['ElementCostComment.modified DESC'],
			]);

			$elecost = $this->ElementCost->find('first', [
				'conditions' => ['ElementCost.element_id' => $element_id],
			]);
			// pr($elecost, 1);

			$view = new View($this, false);
			$view->viewPath = 'Costs/partials'; // Directory inside view directory to search for .ctp files
			$view->layout = false; // if you want to disable layout
			$view->set('element_id', $element_id);
			$view->set('cost_type', $cost_type);
			$this->set('elementcost', $elecost);
			$view->set('data', $data);

			$html = $view->render('get_annotations');

			echo json_encode($html);
			exit();
		}
	}

	public function get_annotation_count($costType = null, $element_id = null) {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$response = [
				'success' => true,
				'msg' => '',
				'content' => null,
			];

			$data = $this->ElementCostComment->find('count', [
				'conditions' => ['ElementCostComment.element_id' => $element_id, 'cost_type' => $costType],
				'order' => ['ElementCostComment.modified DESC'],
			]);

			echo json_encode($data);
			exit();

		}
	}

	public function get_wsp_annotation_count($costType = null, $workspace_id = null) {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$response = [
				'success' => true,
				'msg' => '',
				'content' => null,
			];

			$data = $this->WorkspaceCostComment->find('count', [
				'conditions' => ['WorkspaceCostComment.workspace_id' => $workspace_id, 'cost_type' => $costType],
				'order' => ['WorkspaceCostComment.modified DESC'],
			]);

			echo json_encode($data);
			exit();

		}
	}

	public function delete_annotate($id = null) {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$response = [
				'success' => true,
				'msg' => '',
				'content' => null,
			];

			if ($this->ElementCostComment->delete($id)) {

				echo json_encode($id);
			}

			exit();

		}
	}

	public function export_xls($project_id = null) {

		$viewVars = $data = null;
		$this->layout = false;

		//$project_id = (isset($this->params['named']['project_id']) && !empty($this->params['named']['project_id'])) ? $this->params['named']['project_id'] : null;
		$viewVars['project_id'] = $project_id;

		if ((empty($project_id) || !is_numeric($project_id) || $project_id == null)) {
			$this->Session->setFlash(__('Invalid Project Id.'));
			//$this->redirect($this->referer());
			$this->redirect(array("controller" => "projects", "action" => "lists"));
		}

		$this->Project->id = $project_id;
		if (!$this->Project->exists()) {
			$this->Session->setFlash(__('Invalid Project.'));
			$this->redirect(array("controller" => "projects", "action" => "lists"));
		}

		//================================================================================

		App::import('Controller', 'Users');
		$Users = new UsersController;
		$projects = null;

		$project = $this->Project->findById($project_id);

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

		$projects = array_map("htmlentities", $projects1);
		$projects = array_map("trim", $projects);
		natcasesort($projects);

		$this->set(compact('projects', 'project_id', 'project'));
		//================================================================================

	}

	public function export_datas1($project_id = null) {

		$this->autoRender = false;
		$this->layout = false;

		$project_id = null;
		$projectName = null;
		$view = new View();
			$this->ViewModel = $view->loadHelper('ViewModel');
			$this->Common = $view->loadHelper('Common');

		if (!isset($this->request->data) && empty($this->request->data['Cost']['project_id'])) {

		} else {

			$project_id = $this->request->data['Cost']['project'];
			$projectCurrencyName = $this->Common->getCurrencySymbolName($project_id);
			$projectName = htmlentities($this->remove($this->request->data['Cost']['title']),ENT_QUOTES);

			/*============== Code start form here.... ================*/
			$this->PhpExcel->createWorksheet()->setDefaultFont('Calibri', 12);
			// define table cells
			$table = array(
				array('label' => __('Type'), 'filter' => false),
				array('label' => __('Name'), 'filter' => false),
				array('label' => __('Start'), 'filter' => false),
				array('label' => __('End'), 'filter' => false),
				array('label' => __('Budget ('.$projectCurrencyName.')'), 'filter' => false),
				array('label' => __('Actual ('.$projectCurrencyName.')'), 'filter' => false),
				array('label' => __('Budget Type'), 'filter' => false),
				array('label' => __('Actual Type'), 'filter' => false),
				array('label' => __('Budget by Member ('.$projectCurrencyName.')'), 'filter' => false),
				array('label' => __('Actual by Member ('.$projectCurrencyName.')'), 'filter' => false),

			);
			$this->PhpExcel->addTableHeader($table, array('name' => 'Cambria', 'bold' => true));
			// load viewModel helper


			if (isset($project_id) && !empty($project_id)) {

				$totalBudget = 0;
				$westimate_sum = $wspend_sum = 0;
				//$project_id = $this->request->params['named']['project_id'];
				//project Details
				$projectDetails = $this->ViewModel->getProjectDetail($project_id);
				$totalBudget = (isset($projectDetails['Project']['budget']) && !empty($projectDetails['Project']['budget'])) ? $projectDetails['Project']['budget'] : 0;

				if (empty($projectName)) {

					/* $projectName = html_entity_decode(strip_tags($projectDetails['Project']['title']));
					$projectName = str_replace("'", "", $projectName);
					$projectName = str_replace('"', "", $projectName);
					$projectName = preg_replace('/[^A-Za-z0-9\-]/', '', $projectName); */

					$projectName = $this->remove(htmlentities($projectDetails['Project']['title'],ENT_QUOTES));
				}

				$project_wsps = get_project_workspace($project_id);
				$workspaces = Set::extract($project_wsps, '/Workspace/id');
				$all_workspace_elements = workspace_elements($workspaces);
				$westimate_sum = $wspend_sum = 0;
				if (isset($all_workspace_elements) && !empty($all_workspace_elements)) {
					$wels = Set::extract($all_workspace_elements, '/Element/id');
					$westimate_sum = $this->ViewModel->wsp_element_cost($wels, 1);
					$wspend_sum = $this->ViewModel->wsp_element_cost($wels, 2);
				}

				$startDatep = isset($projectDetails['Project']['start_date']) ? date('d-M-y', strtotime($projectDetails['Project']['start_date'])) : 'N/A';

				$endDatep = isset($projectDetails['Project']['end_date']) ? date('d-M-y', strtotime($projectDetails['Project']['end_date'])) : 'N/A';

				$projectTitle = htmlentities($projectDetails['Project']['title']);
				$projectTitle = str_replace("'", "", $projectTitle);
				$projectTitle = str_replace('"', "", $projectTitle);
				$projectTitle = $this->remove($projectTitle);
				// $this->PhpExcel->getActiveSheet()->setCellValueExplicit('A1', '1234567890', PHPExcel_Cell_DataType::TYPE_STRING);


				/*========= Project Name font weight BOLD ===============*/
				$styleArray = array(
					'font' => array(
						'bold' => true,
					),
				);
				//$sheet = $this->PhpExcel->getActiveSheet();
				//$sheet->getStyle('A2')->applyFromArray($styleArray);
				/*========================================================*/
				$projectBudget = (isset($projectDetails['Project']['budget']) && !empty($projectDetails['Project']['budget'])) ? number_format($projectDetails['Project']['budget'], 2, '.', '') : '-';
				/*$this->PhpExcel->addTableRow(array(
					'',
					'',
					$startDatep,
					$endDatep,
					number_format($westimate_sum, 2, '.', ''),
					number_format($wspend_sum, 2, '.', ''),
				));*/
				$this->PhpExcel->addTableRow(array(
					"Project",
					$projectTitle,
					// "'".$projectTitle."",
					$startDatep,
					$endDatep,
					number_format($westimate_sum, 2, '.', ''),
					number_format($wspend_sum, 2, '.', ''),
				));

				if (isset($project_wsps) && !empty($project_wsps)) {

					foreach ($project_wsps as $wpvalues) {
						//========= Workspace Row ===================

						$startDatew = isset($wpvalues['Workspace']['start_date']) ? date('d-M-y', strtotime($wpvalues['Workspace']['start_date'])) : 'N/A';
						$endDatew = isset($wpvalues['Workspace']['end_date']) ? date('d-M-y', strtotime($wpvalues['Workspace']['end_date'])) : 'N/A';

						$workspaceTitle = $wpvalues['Workspace']['title'];
						$workspaceTitle = str_replace("'", "", $workspaceTitle);
						$workspaceTitle = str_replace('"', "", $workspaceTitle);
						$workspaceTitle = $this->remove($workspaceTitle);

						$workspace_costing = $view->loadHelper('Scratch')->workspace_costing($wpvalues['Workspace']['id']);

						$this->PhpExcel->addTableRow(array(
							'Workspace',
							"'".$workspaceTitle."",
							$startDatew,
							$endDatew,
							number_format($workspace_costing['budget_cost'], 2, '.', ''),
							number_format($workspace_costing['actual_cost'], 2, '.', ''),
							' ',
							' ',
							'-',
							'-',
						));

						//========== Element Row ================
						$workspace_elements = workspace_elements($wpvalues['Workspace']['id']);
						if (isset($workspace_elements) && !empty($workspace_elements)) {

							foreach ($workspace_elements as $ele_lists) {

								$estimatePeople = array();
								$spendPeople = array();

								$estimatePeoplelist = '-';
								$spendPeoplelist = '-';
								// On Estimated and Spend cost people ==================
								$elementCostPeopleEstimate = elementCostPeople($ele_lists['Element']['id'], 1);
								$elementCostPeopleSpend = elementCostPeople($ele_lists['Element']['id'], 2);

								//============== Estimate People Type ==============
								$estimatedPeopleTyep = '-';
								if (isset($elementCostPeopleEstimate['ElementCost']['team_member_flag']) && !empty($elementCostPeopleEstimate['ElementCost']['team_member_flag']) && $elementCostPeopleEstimate['ElementCost']['team_member_flag'] == 1) {
									$estimatedPeopleTyep = 'Team';
								} else if (isset($elementCostPeopleEstimate['ElementCost']['team_member_flag']) && !empty($elementCostPeopleEstimate['ElementCost']['team_member_flag']) && $elementCostPeopleEstimate['ElementCost']['team_member_flag'] == 2) {

									$estimatedPeopleTyep = 'Member';
									//user_name

									if (isset($elementCostPeopleEstimate['UserElementCost']) && !empty($elementCostPeopleEstimate['UserElementCost'])) {
										foreach ($elementCostPeopleEstimate['UserElementCost'] as $userlist) {
											//UserElementCost
											$userElementTotalCostEst = number_format(($userlist['quantity'] * $userlist['work_rate']), 2, '.', '');
											$estimatePeople[] = get_user_data($userlist['user_id'], array('first_name', 'last_name')) . ' (' . $userElementTotalCostEst . ')';
										}
									}

								}

								//============== Spend People Type ==============
								$spendPeopleTyep = '-';
								if (isset($elementCostPeopleSpend['ElementCost']['team_member_flag']) && !empty($elementCostPeopleSpend['ElementCost']['team_member_flag']) && $elementCostPeopleSpend['ElementCost']['team_member_flag'] == 1) {
									$spendPeopleTyep = 'Team';
								} else if (isset($elementCostPeopleSpend['ElementCost']['team_member_flag']) && !empty($elementCostPeopleSpend['ElementCost']['team_member_flag']) && $elementCostPeopleSpend['ElementCost']['team_member_flag'] == 2) {

									$spendPeopleTyep = 'Member';

									if (isset($elementCostPeopleSpend['UserElementCost']) && !empty($elementCostPeopleSpend['UserElementCost'])) {
										foreach ($elementCostPeopleSpend['UserElementCost'] as $userlists) {

											$userElementTotalCostSpnd = number_format(($userlists['quantity'] * $userlists['work_rate']), 2, '.', '');

											$spendPeople[] = get_user_data($userlists['user_id'], array('first_name', 'last_name')) . ' (' . $userElementTotalCostSpnd . ')';
										}
									}
								}

								//======================================================

								$westimateCost = $this->ViewModel->wsp_element_cost($ele_lists['Element']['id'], 1);
								$wspendCost = $this->ViewModel->wsp_element_cost($ele_lists['Element']['id'], 2);

								$starteDate = isset($ele_lists['Element']['start_date']) ? date('d-M-y', strtotime($ele_lists['Element']['start_date'])) : 'N/A';
								$endeDate = isset($ele_lists['Element']['end_date']) ? date('d-M-y', strtotime($ele_lists['Element']['end_date'])) : 'N/A';

								if (isset($estimatePeople) && !empty($estimatePeople)) {
									$estimatePeoplelist = implode(", ", $estimatePeople);
								} else {
									$estimatePeoplelist = '-';
								}

								if (isset($spendPeople) && !empty($spendPeople)) {
									$spendPeoplelist = implode(", ", $spendPeople);
								} else {
									$spendPeoplelist = '-';
								}

								$elementTitle = $ele_lists['Element']['title'];
								$elementTitle = str_replace("'", "", $elementTitle);
								$elementTitle = str_replace('"', "", $elementTitle);
								$elementTitle = $this->remove($elementTitle);

								$this->PhpExcel->addTableRow(array(
									'Task',
									"'".$elementTitle."",
									$starteDate,
									$endeDate,
									number_format($westimateCost, 2, '.', ''),
									number_format($wspendCost, 2, '.', ''),
									$estimatedPeopleTyep,
									$spendPeopleTyep,
									$estimatePeoplelist,
									$spendPeoplelist,
								));

							} //Element Row Loop

						} //Element Row

					} //  Workspace Row Loop

				} //Workspace Row

				// close table and output

				$this->PhpExcel->addTableFooter()->output($projectName . '.xlsx');
			}
		}
	}

	public function export_datas($project_id = null) {

		$this->autoRender = false;
		$this->layout = false;

		$project_id = null;
		$projectName = null;
		$view = new View();
			$this->ViewModel = $view->loadHelper('ViewModel');
			$this->Common = $view->loadHelper('Common');

		if (!isset($this->request->data) && empty($this->request->data['Cost']['project_id'])) {

		} else {

			$project_id = $this->request->data['Cost']['project'];
			$totalBudget = $westimate_sum = $wspend_sum = 0;

			$projectDetails = $this->ViewModel->getProjectDetail($project_id);
			$totalBudget = (isset($projectDetails['Project']['budget']) && !empty($projectDetails['Project']['budget'])) ? $projectDetails['Project']['budget'] : 0;
			$projectCurrencyName = $this->Common->getCurrencySymbolName($project_id);
			$projectName = htmlentities($this->remove($this->request->data['Cost']['title']),ENT_QUOTES);
			// $dd = get_project_workspaces($project_id);
			$projectName = $this->remove(htmlentities($projectDetails['Project']['title'],ENT_QUOTES));

			$project_wsps = get_project_workspace($project_id);
			$workspaces = Set::extract($project_wsps, '/Workspace/id');
			$all_workspace_elements = workspace_elements($workspaces);
			$westimate_sum = $wspend_sum = 0;
			if (isset($all_workspace_elements) && !empty($all_workspace_elements)) {
				$wels = Set::extract($all_workspace_elements, '/Element/id');
				$westimate_sum = $this->ViewModel->wsp_element_cost($wels, 1);
				$wspend_sum = $this->ViewModel->wsp_element_cost($wels, 2);
			}

			$startDatep = isset($projectDetails['Project']['start_date']) ? date('d-M-y', strtotime($projectDetails['Project']['start_date'])) : 'N/A';
			$endDatep = isset($projectDetails['Project']['end_date']) ? date('d-M-y', strtotime($projectDetails['Project']['end_date'])) : 'N/A';

			$projectTitle = htmlentities($projectDetails['Project']['title']);
			$projectTitle = str_replace("'", "", $projectTitle);
			$projectTitle = str_replace('"', "", $projectTitle);
			$projectTitle = $this->remove($projectTitle);

			// CREATE OBJECT AND A BLANK SHEET
			$objPHPExcel = new PHPExcel();
			$objPHPExcel->setActiveSheetIndex(0);

			$objPHPExcel->getActiveSheet()->SetCellValue('A1', 'Type');
			$objPHPExcel->getActiveSheet()->SetCellValue('B1', 'Name');
			$objPHPExcel->getActiveSheet()->SetCellValue('C1', 'Start');
			$objPHPExcel->getActiveSheet()->SetCellValue('D1', 'End');
			$objPHPExcel->getActiveSheet()->SetCellValue('E1', 'Budget ('.$projectCurrencyName.')');
			$objPHPExcel->getActiveSheet()->SetCellValue('F1', 'Actual ('.$projectCurrencyName.')');
			$objPHPExcel->getActiveSheet()->SetCellValue('G1', 'Budget Type');
			$objPHPExcel->getActiveSheet()->SetCellValue('H1', 'Actual Type');
			$objPHPExcel->getActiveSheet()->SetCellValue('I1', 'Budget by Member ('.$projectCurrencyName.')');
			$objPHPExcel->getActiveSheet()->SetCellValue('J1', 'Actual by Member ('.$projectCurrencyName.')');
			$objPHPExcel->getActiveSheet()->SetCellValue('K1', 'Budget Cost Type');
			$objPHPExcel->getActiveSheet()->SetCellValue('L1', 'Actual Cost Type');

			// SET BOLD TO HEADER
			$objPHPExcel->getActiveSheet()->getStyle("A1:L1")->getFont()->setBold(true);

			// SET AUTO WIDTH TO ALL COLUMNS ACCORDING TO TEXT WIDTH
			foreach(range('A','L') as $columnID) {
			    $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
			}

			$styleArray = array(
			   'font'  => array(
			        'bold'  => false,
			        'color' => array('rgb' => '000000'),
			        'size'  => 12,
			        'name'  => 'Calibri'
			    )
			);
			$objPHPExcel->getDefaultStyle()
			    ->applyFromArray($styleArray);
		    $objPHPExcel->getDefaultStyle()
				    ->getAlignment()
				    ->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);

			// PROJECT ROW
			$objPHPExcel->getActiveSheet()->setCellValueExplicit('A2', 'Project', PHPExcel_Cell_DataType::TYPE_STRING);
			$objPHPExcel->getActiveSheet()->setCellValueExplicit('B2', $projectTitle, PHPExcel_Cell_DataType::TYPE_STRING);
			$objPHPExcel->getActiveSheet()->setCellValueExplicit('C2', $startDatep, PHPExcel_Cell_DataType::TYPE_STRING);
			$objPHPExcel->getActiveSheet()->setCellValueExplicit('D2', $endDatep, PHPExcel_Cell_DataType::TYPE_STRING);
			$objPHPExcel->getActiveSheet()->setCellValueExplicit('E2', number_format($westimate_sum, 2, '.', ''), PHPExcel_Cell_DataType::TYPE_NUMERIC);
			$objPHPExcel->getActiveSheet()->setCellValueExplicit('F2', number_format($wspend_sum, 2, '.', ''), PHPExcel_Cell_DataType::TYPE_NUMERIC);

			// echo $highestRow = $objPHPExcel->getActiveSheet()->getHighestRow().'-----';
			// echo $highestColumn = $objPHPExcel->getActiveSheet()->getHighestColumn();
			$row_counter = 3;
			if (isset($project_wsps) && !empty($project_wsps)) {
					foreach ($project_wsps as $wpvalues) {
						$startDatew = isset($wpvalues['Workspace']['start_date']) ? date('d-M-y', strtotime($wpvalues['Workspace']['start_date'])) : 'N/A';
						$endDatew = isset($wpvalues['Workspace']['end_date']) ? date('d-M-y', strtotime($wpvalues['Workspace']['end_date'])) : 'N/A';

						$workspaceTitle = $wpvalues['Workspace']['title'];
						$workspaceTitle = str_replace("'", "", $workspaceTitle);
						$workspaceTitle = str_replace('"', "", $workspaceTitle);
						$workspaceTitle = $this->remove($workspaceTitle);

						$workspace_costing = $view->loadHelper('Scratch')->workspace_costing($wpvalues['Workspace']['id']);

						$wsp_bcost = $workspace_costing['budget_cost'];
						$wsp_acost = $workspace_costing['actual_cost'];

						// WORKSPACE ROW
						$objPHPExcel->getActiveSheet()->setCellValueExplicit("A$row_counter", 'Workspace', PHPExcel_Cell_DataType::TYPE_STRING);
						$objPHPExcel->getActiveSheet()->setCellValueExplicit("B$row_counter", $workspaceTitle, PHPExcel_Cell_DataType::TYPE_STRING);
						$objPHPExcel->getActiveSheet()->setCellValueExplicit("C$row_counter", $startDatew, PHPExcel_Cell_DataType::TYPE_STRING);
						$objPHPExcel->getActiveSheet()->setCellValueExplicit("D$row_counter", $endDatew, PHPExcel_Cell_DataType::TYPE_STRING);
						$objPHPExcel->getActiveSheet()->setCellValueExplicit("E$row_counter", number_format($wsp_bcost, 2, '.', ''), PHPExcel_Cell_DataType::TYPE_NUMERIC);
						$objPHPExcel->getActiveSheet()->setCellValueExplicit("F$row_counter", number_format($wsp_acost, 2, '.', ''), PHPExcel_Cell_DataType::TYPE_NUMERIC);
						$objPHPExcel->getActiveSheet()->setCellValueExplicit("G$row_counter", ' ', PHPExcel_Cell_DataType::TYPE_STRING);
						$objPHPExcel->getActiveSheet()->setCellValueExplicit("H$row_counter", ' ', PHPExcel_Cell_DataType::TYPE_STRING);
						$objPHPExcel->getActiveSheet()->setCellValueExplicit("I$row_counter", '-', PHPExcel_Cell_DataType::TYPE_STRING);
						$objPHPExcel->getActiveSheet()->setCellValueExplicit("J$row_counter", '-', PHPExcel_Cell_DataType::TYPE_STRING);
						$row_counter++;

						//========== Element Row ================
						$workspace_elements = workspace_elements($wpvalues['Workspace']['id']);
						if (isset($workspace_elements) && !empty($workspace_elements)) {

							foreach ($workspace_elements as $ele_lists) {

								$estimatePeople = array();
								$spendPeople = array();

								$estimatePeoplelist = '-';
								$spendPeoplelist = '-';
								// On Estimated and Spend cost people ==================
								$elementCostPeopleEstimate = elementCostPeople($ele_lists['Element']['id'], 1);
								$elementCostPeopleSpend = elementCostPeople($ele_lists['Element']['id'], 2);


								$costbudgetType = '-';
								if (isset($elementCostPeopleEstimate['CostType']['type']) && !empty($elementCostPeopleEstimate['CostType']['type'])) {
									$costbudgetType = $elementCostPeopleEstimate['CostType']['type'];
								}

								$costactualType = '-';
								if (isset($elementCostPeopleSpend['CostType']['type']) && !empty($elementCostPeopleSpend['CostType']['type'])) {
									$costactualType = $elementCostPeopleSpend['CostType']['type'];

								}


								//============== Estimate People Type ==============
								$estimatedPeopleTyep = '-';
								if (isset($elementCostPeopleEstimate['ElementCost']['team_member_flag']) && !empty($elementCostPeopleEstimate['ElementCost']['team_member_flag']) && $elementCostPeopleEstimate['ElementCost']['team_member_flag'] == 1) {
									$estimatedPeopleTyep = 'Team';
								} else if (isset($elementCostPeopleEstimate['ElementCost']['team_member_flag']) && !empty($elementCostPeopleEstimate['ElementCost']['team_member_flag']) && $elementCostPeopleEstimate['ElementCost']['team_member_flag'] == 2) {

									$estimatedPeopleTyep = 'Member';

									if (isset($elementCostPeopleEstimate['UserElementCost']) && !empty($elementCostPeopleEstimate['UserElementCost'])) {
										foreach ($elementCostPeopleEstimate['UserElementCost'] as $userlist) {
											//UserElementCost
											$userElementTotalCostEst = number_format(($userlist['quantity'] * $userlist['work_rate']), 2, '.', '');
											$estimatePeople[] = get_user_data($userlist['user_id'], array('first_name', 'last_name')) . ' (' . $userElementTotalCostEst . ')';
										}
									}

								}

								//============== Spend People Type ==============
								$spendPeopleTyep = '-';
								if (isset($elementCostPeopleSpend['ElementCost']['team_member_flag']) && !empty($elementCostPeopleSpend['ElementCost']['team_member_flag']) && $elementCostPeopleSpend['ElementCost']['team_member_flag'] == 1) {
									$spendPeopleTyep = 'Team';
								} else if (isset($elementCostPeopleSpend['ElementCost']['team_member_flag']) && !empty($elementCostPeopleSpend['ElementCost']['team_member_flag']) && $elementCostPeopleSpend['ElementCost']['team_member_flag'] == 2) {

									$spendPeopleTyep = 'Member';

									if (isset($elementCostPeopleSpend['UserElementCost']) && !empty($elementCostPeopleSpend['UserElementCost'])) {
										foreach ($elementCostPeopleSpend['UserElementCost'] as $userlists) {

											$userElementTotalCostSpnd = number_format(($userlists['quantity'] * $userlists['work_rate']), 2, '.', '');

											$spendPeople[] = get_user_data($userlists['user_id'], array('first_name', 'last_name')) . ' (' . $userElementTotalCostSpnd . ')';
										}
									}
								}

								//======================================================

								$westimateCost = $this->ViewModel->wsp_element_cost($ele_lists['Element']['id'], 1);
								$wspendCost = $this->ViewModel->wsp_element_cost($ele_lists['Element']['id'], 2);



								$starteDate = isset($ele_lists['Element']['start_date']) ? date('d-M-y', strtotime($ele_lists['Element']['start_date'])) : 'N/A';
								$endeDate = isset($ele_lists['Element']['end_date']) ? date('d-M-y', strtotime($ele_lists['Element']['end_date'])) : 'N/A';

								if (isset($estimatePeople) && !empty($estimatePeople)) {
									$estimatePeoplelist = implode("\n", $estimatePeople);
								} else {
									$estimatePeoplelist = '-';
								}

								if (isset($spendPeople) && !empty($spendPeople)) {
									$spendPeoplelist = implode("\n", $spendPeople);
								} else {
									$spendPeoplelist = '-';
								}

								$elementTitle = $ele_lists['Element']['title'];
								$elementTitle = str_replace("'", "", $elementTitle);
								$elementTitle = str_replace('"', "", $elementTitle);
								$elementTitle = $this->remove($elementTitle);

								$objPHPExcel->getActiveSheet()->setCellValueExplicit("A$row_counter", 'Task', PHPExcel_Cell_DataType::TYPE_STRING);
								$objPHPExcel->getActiveSheet()->setCellValueExplicit("B$row_counter", $elementTitle, PHPExcel_Cell_DataType::TYPE_STRING);
								$objPHPExcel->getActiveSheet()->setCellValueExplicit("C$row_counter", $starteDate, PHPExcel_Cell_DataType::TYPE_STRING);
								$objPHPExcel->getActiveSheet()->setCellValueExplicit("D$row_counter", $endeDate, PHPExcel_Cell_DataType::TYPE_STRING);
								$objPHPExcel->getActiveSheet()->setCellValueExplicit("E$row_counter", number_format($westimateCost, 2, '.', ''), PHPExcel_Cell_DataType::TYPE_NUMERIC);
								$objPHPExcel->getActiveSheet()->setCellValueExplicit("F$row_counter", number_format($wspendCost, 2, '.', ''), PHPExcel_Cell_DataType::TYPE_NUMERIC);
								$objPHPExcel->getActiveSheet()->setCellValueExplicit("G$row_counter", $estimatedPeopleTyep, PHPExcel_Cell_DataType::TYPE_STRING);
								$objPHPExcel->getActiveSheet()->setCellValueExplicit("H$row_counter", $spendPeopleTyep, PHPExcel_Cell_DataType::TYPE_STRING);
								$objPHPExcel->getActiveSheet()->setCellValueExplicit("I$row_counter", $estimatePeoplelist, PHPExcel_Cell_DataType::TYPE_STRING);
								$objPHPExcel->getActiveSheet()->setCellValueExplicit("I$row_counter", $estimatePeoplelist, PHPExcel_Cell_DataType::TYPE_STRING);
								$objPHPExcel->getActiveSheet()->setCellValueExplicit("I$row_counter", $estimatePeoplelist, PHPExcel_Cell_DataType::TYPE_STRING);
								$objPHPExcel->getActiveSheet()->getStyle("I$row_counter")->getAlignment()->setWrapText(true);
								$objPHPExcel->getActiveSheet()->setCellValueExplicit("J$row_counter", $spendPeoplelist, PHPExcel_Cell_DataType::TYPE_STRING);
								$objPHPExcel->getActiveSheet()->getStyle("J$row_counter")->getAlignment()->setWrapText(true);
								$objPHPExcel->getActiveSheet()->setCellValueExplicit("K$row_counter", $costbudgetType, PHPExcel_Cell_DataType::TYPE_STRING);
								$objPHPExcel->getActiveSheet()->getStyle("K$row_counter")->getAlignment()->setWrapText(true);
								$objPHPExcel->getActiveSheet()->setCellValueExplicit("L$row_counter", $costactualType, PHPExcel_Cell_DataType::TYPE_STRING);
								$objPHPExcel->getActiveSheet()->getStyle("L$row_counter")->getAlignment()->setWrapText(true);


								$row_counter++;
							} //Element Row Loop

						} //Element Row

					} //  Workspace Row Loop

				} //Workspace Row


			// OUTPUT
	  		$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);

			header('Content-Type: application/vnd.ms-excel'); //mime type
			header('Content-Disposition: attachment;filename="'.$projectName.'.xlsx"'); //tell browser what's the file name
			header('Cache-Control: max-age=0'); //no cache
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
			$objWriter->save('php://output');


		}
	}

	function remove($str, $allow = 0) {
		if ($allow > 0) {
			$str = trim($str);
		} else {
			$str = html_entity_decode(trim($str));
		}

		$str = str_replace("&nbsp;", " ", $str);
		return ucfirst($str);
	}


// ========================================= Project Annotations

	public function add_project_annotate($costType = null, $project_id = null) {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$data = $this->ProjectCostComment->find('all', [
				'conditions' => ['ProjectCostComment.project_id' => $project_id, 'cost_type' => $costType],
				'order' => ['ProjectCostComment.modified DESC'],
			]);

			if ($costType == 2) {
				$wspboxtitle = 'Project Actual';
			} else {
				$wspboxtitle = 'Project Budget';
			}

			$this->set('project_id', $project_id);
			$this->set('cost_type', $costType);
			$this->set('wspboxtitle', $wspboxtitle);
			$this->set('data', $data);

			$this->render(DS . 'Costs' . DS . 'partials' . DS . 'add_project_annotate');
		}
	}

	public function get_project_annotations($cost_type = null, $project_id = null) {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$response = [
				'success' => true,
				'msg' => '',
				'content' => null,
			];

			$data = $this->ProjectCostComment->find('all', [
				'conditions' => ['ProjectCostComment.project_id' => $project_id, 'cost_type' => $cost_type],
				'order' => ['ProjectCostComment.modified DESC'],
			]);

			$view = new View($this, false);
			$view->viewPath = 'Costs/partials'; // Directory inside view directory to search for .ctp files
			$view->layout = false; // if you want to disable layout
			$view->set('project_id', $project_id);
			$view->set('cost_type', $cost_type);
			$view->set('data', $data);

			$html = $view->render('get_project_annotations');

			echo json_encode($html);
			exit();
		}
	}

	public function save_project_annotate() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$response = [
				'success' => false,
				'msg' => '',
				'content' => null,
			];

			if ($this->request->is('post') || $this->request->is('put')) {

				$this->request->data['ProjectCostComment']['comments'] = Sanitize::escape($this->request->data['ProjectCostComment']['comments']);

				$this->request->data['ProjectCostComment']['comments'] = trim(strip_tags($this->request->data['ProjectCostComment']['comments']));

				$this->ProjectCostComment->set($this->request->data);
				if ($this->ProjectCostComment->validates()) {

					$post = $this->request->data['ProjectCostComment'];
					$this->request->data['ProjectCostComment']['comments'] = addslashes($this->request->data['ProjectCostComment']['comments']);
					$response['content']['project_id'] = $post['project_id'];
					$response['content']['cost_type'] = $post['cost_type'];
					$currency_id = $this->Project->find('first',
						[
							'conditions' => ['Project.id' => $post['project_id']],
							'fields' => ['Project.currency_id'],

						]
					);
					$post['currency_id'] = isset($currency_id['Project']['currency_id']) ? $currency_id['Project']['currency_id'] : 0;
					//========================================================

					if (isset($post['id']) && !empty($post['id'])) {

						$this->ProjectCostComment->id = $post['id'];

						$costvalue = $this->ProjectCostComment->find('first', ['conditions' => ['ProjectCostComment.id' => $post['id']]]);
						$post['cost'] = $costvalue['ProjectCostComment']['cost'];
						$post['currency_id'] = $costvalue['ProjectCostComment']['currency_id'];

					}

					if ($this->ProjectCostComment->save($post)) {
						$response['success'] = true;
					}
				} else {
					$response['content'] = $this->ProjectCostComment->validationErrors;

				}
			}
			echo json_encode($response);
			exit;

		}
	}

	public function delete_project_annotate($id = null) {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$response = [
				'success' => true,
				'msg' => '',
				'content' => null,
			];

			if ($this->ProjectCostComment->delete($id)) {

				echo json_encode($id);
			}

			exit();

		}
	}

	public function get_project_annotation_count($costType = null, $project_id = null) {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$response = [
				'success' => true,
				'msg' => '',
				'content' => null,
			];

			$data = $this->ProjectCostComment->find('count', [
				'conditions' => ['ProjectCostComment.project_id' => $project_id, 'cost_type' => $costType],
				'order' => ['ProjectCostComment.modified DESC'],
			]);

			echo json_encode($data);
			exit();

		}
	}

}
