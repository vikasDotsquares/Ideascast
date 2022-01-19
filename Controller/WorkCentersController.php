<?php
App::uses('Controller/Component', 'Auth', 'Session', 'RequestHandler');
App::uses('CakeEmail', 'Network/Email');
App::uses('HttpSocket', 'Network/Http');

class WorkCentersController extends AppController {

	public $name = 'WorkCenters';

	public $uses = ['User', 'UserDetail', 'ProjectPermission', 'UserSetting', 'Category', 'Aligned', 'UserProject', 'Project', 'Workspace', 'Element', 'ElementPermission', 'ProjectGroupUser'];

	public $helpers = array('Html', 'Form', 'Session', 'Time', 'Text', 'Common', 'ViewModel', 'Mpdf', 'Image', 'Wiki', 'TaskCenter', 'Group');

	public $components = array('RequestHandler', 'Common', 'Group');
	public $objView = null;
	public $user_id = null;

	public function beforeFilter() {

		parent::beforeFilter();
		$this->Auth->allow('get_user_projects', 'projectsbydate', 'noWorkingElementUsers', 'noworkingelementusers');
		$this->user_id = $this->Auth->user('id');

		$view = new View();
		$this->objView = $view;
	}

	public function index($level = null) {

		$this->layout = 'inner';
		$this->set('title_for_layout', __('Work Center', true));
		$this->set('page_heading', __('Work Center', true));
		$this->set('page_subheading', __('View Tasks, workloads and availability', true));

		App::import('Controller', 'Users');
		$Users = new UsersController;

		$this->user_id = $this->Auth->user('id');
		$start_date = date('Y-m-d');
		$endDate = date('Y-m-d');
		/*=============================================================*/

		$project_ids = null;
		$projectIds = null;
		$program_id = null;
		$conditions = [];

		$myprojectlist = $myreceivedprojectlist = $mygroupprojectlist = array();
		// Find All current user's projects
		$myprojectlist = $Users->__myproject_selectbox($this->user_id);
		// Find All current user's received projects
		$myreceivedprojectlist = $Users->__receivedproject_selectbox($this->user_id, $level);
		// Find All current user's group projects
		$mygroupprojectlist = $Users->__groupproject_selectbox($this->user_id, $level);

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

		if (!empty($projects1)) {
			$conditions['UserProject.project_id'] = array_keys($projects1);
		}

		if ((isset($conditions) && !empty($conditions)) || !empty($projects1)) {
			$conditions['Project.studio_status !='] = 1;
			$MyProjects = $this->UserProject->find('all', array('conditions' => $conditions, 'recursive' => 1, 'fields' => ['Project.id', 'Project.title']));

			if (isset($MyProjects) && !empty($MyProjects)) {
				foreach ($MyProjects as $k => $v) {
					$project_ids[$v['Project']['id']] = $v['Project']['title'];
					$projectIds[] = $v['Project']['id'];
				}
			}
		}

		$OwnerSharerUsers = $this->ownerShares($projectIds, $this->Session->read('Auth.User.id'));		
		$totaltasks = $this->totaltasks($projectIds);
		$taskleaders = $this->totalTaskLeader($projectIds);

		$this->set('projects', $project_ids);
		$this->set('totaltasks', $totaltasks);
		$this->set('taskleaders', $taskleaders);
		$this->set('OwnerSharerUsers', $OwnerSharerUsers);

		/*=============================================================*/

		$crumb = [
			'Project' => [
				'data' => [
					'url' => '/work_centers/index/',
					'class' => 'tipText',
					'title' => 'Work Center',
					'data-original-title' => 'Work Center',
				],
			],
		];
		if (isset($crumb) && !empty($crumb)) {
			$this->set('crumb', $crumb);
		}

	}

	public function get_user_projects() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$response = [
				'success' => false,
				'content' => null,
				'user_id' => null,
				'userIDs' => null,
				'start_date' => date('Y-m-d'),
				'end_date' => date('Y-m-d'),
			];
			$current_user_id = $this->Session->read('Auth.User.id');

			if ($this->request->is('post') || $this->request->is('put')) {
								
				$user_id = $this->request->data['user_id'];
				$level = $this->request->data['level'];

				/* $ownersProjects = $this->ownerProjects($current_user_id);
				$sharerProjects = $this->ownerProjects($user_id);		 */

				/* Get login users all projects and selected user's all projects */

				$ownersProjects = $this->ownerProjects($current_user_id, $level);
				$sharerProjects = $this->ownerProjects($user_id);

				$start_date = date('Y-m-d');
				$end_date = date('Y-m-d');

				if (isset($this->request->data['dateStr']) && !empty($this->request->data['dateStr']) && $this->request->data['dateStr'] != 'Today') {
					$dateStr = explode("-", $this->request->data['dateStr']);

					$startDate = explode(" ", trim($dateStr[0]));
					$start_date = date('Y-m-d', strtotime(trim($startDate[2]) . '-' . trim($startDate[1]) . '-' . trim($startDate[0])));

					$endDate = explode(" ", trim($dateStr[1]));
					$end_date = date('Y-m-d', strtotime(trim($endDate[2]) . '-' . trim($endDate[1]) . '-' . trim($endDate[0])));

				}

				if (isset($ownersProjects) && isset($sharerProjects) && !empty($ownersProjects) && !empty($sharerProjects)) {

					/* Common Projects of login user and selected user */

					$allProjects = array_intersect($ownersProjects, $sharerProjects);

					$between = '';
					if (!empty($start_date) && empty($end_date)) {
						$between .= "AND (date(Project.start_date) BETWEEN '" . $start_date . "' AND '" . $start_date . "') ";
					} else if (empty($start_date) && !empty($end_date)) {
						$between .= "AND (date(Project.end_date) BETWEEN '" . $end_date . "' AND '" . $end_date . "') ";
					} else if (!empty($start_date) && !empty($end_date)) {
						$between .= "AND ( (date(Project.start_date) >= '" . $start_date . "' AND date(Project.end_date) <='" . $end_date . "') ";
						$between .= "OR (date(Project.start_date) <= '" . $start_date . "' AND date(Project.end_date) >='" . $end_date . "')) ";
					}

					$arrayTostr = implode(",", $allProjects);
					$select = "SELECT id,title FROM projects as Project WHERE id IN (" . $arrayTostr . ") " . $between;
					//echo $select; die;
					$MyProjects = $this->Project->query($select);

					$data = array();
					$view = new View();
					$TaskCenter = $view->loadHelper('TaskCenter');
					$Common = $view->loadHelper('Common');

					$projects_arr = Set::extract($MyProjects, '/Project/id');

					if ($level == 1) {
						$alluserarray = $TaskCenter->userByProject($projects_arr);

						$allusers = array_unique($alluserarray['all_project_user']);
						$allusers = $TaskCenter->user_exists($allusers);
						if (($key = array_search($current_user_id, $allusers)) !== false) {
							unset($allusers[$key]);
						}
						$alluserslist = $Common->usersFullname($allusers);
					} else {
						$alluserslist = array();
					}

					
					if (isset($MyProjects) && !empty($MyProjects)) {
						$response['success'] = true;
						$response['start_date'] = $start_date;
						$response['end_date'] = $end_date;
						$response['userIDs'] = $alluserslist;
						foreach ($MyProjects as $plists) {
							$response['content'][] = $plists['Project'];
						}

					}

					$response['user_id'] = $user_id;

				} else {
					$response['success'] = false;
				}

				echo json_encode($response);
				exit;
			}

		}

	}

	public function projectsbydate() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$response = [
				'success' => false,
				'content' => null,
				'user_id' => null,
				'start_date' => date('Y-m-d'),
				'end_date' => date('Y-m-d'),
			];

			$current_user_id = $this->Session->read('Auth.User.id');
			if ($this->request->is('post') || $this->request->is('put')) {

				$user_id = $this->request->data['user_id'];
				$level = $this->request->data['level'];
				$start_date = date('Y-m-d');
				$end_date = date('Y-m-d');

				if (isset($this->request->data['dateStr']) && !empty($this->request->data['dateStr']) && $this->request->data['dateStr'] != 'Today') {
					$dateStr = explode("-", $this->request->data['dateStr']);

					$startDate = explode(" ", trim($dateStr[0]));
					$start_date = date('Y-m-d', strtotime(trim($startDate[2]) . '-' . trim($startDate[1]) . '-' . trim($startDate[0])));

					$endDate = explode(" ", trim($dateStr[1]));
					$end_date = date('Y-m-d', strtotime(trim($endDate[2]) . '-' . trim($endDate[1]) . '-' . trim($endDate[0])));

				}

				$ownersProjects = $this->ownerProjects($current_user_id, $level);
				$sharerProjects = $this->ownerProjects($user_id);

				if (isset($ownersProjects) && isset($sharerProjects) && !empty($ownersProjects) && !empty($sharerProjects)) {

					$allProjects = array_intersect($ownersProjects, $sharerProjects);

					$between = '';

					if (!empty($start_date) && empty($end_date)) {
						$between .= "AND (((date(Project.start_date) BETWEEN '" . $start_date . "' AND '" . $start_date . "') OR (date(Project.end_date) BETWEEN '" . $start_date . "' AND '" . $start_date . "')) OR (date(Project.start_date) <= '" . $start_date . "' AND date(Project.end_date) >= '" . $start_date . "') ) ";

					} else if (empty($start_date) && !empty($end_date)) {
						$between .= "AND (date(Project.end_date) BETWEEN '" . $end_date . "' AND '" . $end_date . "') ";
					} else if (!empty($start_date) && !empty($end_date)) {
						$between .= "AND (( (date(Project.start_date) BETWEEN '" . $start_date . "' AND '" . $end_date . "') ";
						$between .= "OR (date(Project.end_date) BETWEEN '" . $start_date . "' AND '" . $end_date . "')) OR (date(Project.start_date) <= '" . $start_date . "' AND date(Project.end_date) >= '" . $end_date . "')  ) ";

					}

					$arrayTostr = implode(",", $allProjects);
					$select = "SELECT id,title FROM projects as Project WHERE id IN (" . $arrayTostr . ") " . $between;
					//echo $select
					$MyProjects = $this->Project->query($select);

					if (isset($MyProjects) && !empty($MyProjects)) {
						$response['success'] = true;
						$response['start_date'] = $start_date;
						$response['end_date'] = $end_date;
						$response['user_id'] = $user_id;
						foreach ($MyProjects as $plists) {
							$response['content'][] = $plists['Project'];
						}
					}

				} else {
					$response['success'] = false;
				}
				echo json_encode($response);
				exit;
			}
		}
	}

	public function ownerProjects($user_id, $level = null) {

		$project_ids = null;
		$conditions = [];

		App::import('Controller', 'Users');
		$Users = new UsersController;

		$myprojectlist = $myreceivedprojectlist = $mygroupprojectlist = array();
		// Find All current user's projects
		$myprojectlist = $Users->__myproject_selectbox($user_id);
		// Find All current user's received projects
		$myreceivedprojectlist = $Users->__receivedproject_selectbox($user_id, $level);
		// Find All current user's group projects
		$mygroupprojectlist = $Users->__groupproject_selectbox($user_id, $level);

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

		if (!empty($projects1)) {
			$conditions['UserProject.project_id'] = array_keys($projects1);
		}

		if ((isset($conditions) && !empty($conditions)) || !empty($projects1)) {
			$conditions['Project.studio_status !='] = 1;
			$conditions['Project.sign_off !='] = 1;
			$MyProjects = $this->UserProject->find('all', array('conditions' => $conditions, 'recursive' => 1, 'fields' => ['Project.id']));

			if (isset($MyProjects) && !empty($MyProjects)) {
				foreach ($MyProjects as $k => $v) {
					$project_ids[] = $v['Project']['id'];
				}
			}
		}
		return $project_ids;

	}

	public function workcenterlist() {

		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			$response = [
				'success' => false,
				'content' => null,
			];

			if ($this->request->is('post') || $this->request->is('put')) {

				$user_id = $this->request->data['user_id'];
				$start_date = date('Y-m-d');
				$end_date = date('Y-m-d');

				if (isset($this->request->data['requestTyep']) && $this->request->data['requestTyep'] == 'projects') {

					if (isset($this->request->data['dateStr']) && !empty($this->request->data['dateStr']) && $this->request->data['dateStr'] != 'Today') {
						$dateStr = explode("-", $this->request->data['dateStr']);

						$startDate = explode(" ", trim($dateStr[0]));
						$start_date = date('Y-m-d', strtotime(trim($startDate[2]) . '-' . trim($startDate[1]) . '-' . trim($startDate[0])));

						$endDate = explode(" ", trim($dateStr[1]));
						$end_date = date('Y-m-d', strtotime(trim($endDate[2]) . '-' . trim($endDate[1]) . '-' . trim($endDate[0])));

					}

					if (isset($this->request->data['start_date']) && !empty($this->request->data['start_date']) && isset($this->request->data['end_date']) && !empty($this->request->data['end_date'])) {

						$start_date = $this->request->data['start_date'];
						$end_date = $this->request->data['end_date'];

					}
					$this->set('start_date', $start_date);
					$this->set('end_date', $end_date);

					$projectIds = array();
					if (isset($this->request->data['project_id']) && !empty($this->request->data['project_id'])) {

						$projectIds[] = $this->request->data['project_id'];

					}
					//pr($projectIds); die;

					$OwnerSharerUsers = $this->ownerShares($projectIds, $user_id);
					$totaltasks = $this->totaltasks($projectIds);
					$taskleaders = $this->totalTaskLeader($projectIds);
					$projectIds_tot = ( isset($projectIds) && !empty($projectIds) ) ? count($projectIds) : 0;
					$this->set('projects', $projectIds);
					$this->set('totalcntprojects', $projectIds_tot);
					$this->set('user_id', $user_id);
					$this->set('totaltasks', $totaltasks);
					$this->set('taskleaders', $taskleaders);
					$this->set('OwnerSharerUsers', $OwnerSharerUsers);

					$this->render('/WorkCenters/partials/workcenterlist');

				} else {

					if (isset($this->request->data['start_date']) && !empty($this->request->data['start_date']) && isset($this->request->data['end_date']) && !empty($this->request->data['end_date'])) {

						$start_date = $this->request->data['start_date'];
						$end_date = $this->request->data['end_date'];

					}
					$this->set('start_date', $start_date);
					$this->set('end_date', $end_date);

					$projectIds = array();
					if (isset($this->request->data['projects']) && !empty($this->request->data['projects'])) {
						foreach ($this->request->data['projects'] as $pid) {
							$projectIds[] = $pid['id'];
						}
					}

					$OwnerSharerUsers = $this->ownerShares($projectIds, $user_id);
					$totaltasks = $this->totaltasks($projectIds);
					$taskleaders = $this->totalTaskLeader($projectIds);

					if (isset($projectIds) && !empty($projectIds)) {

						$this->set('projects', $projectIds);
						$this->set('user_id', $user_id);
						$this->set('totaltasks', $totaltasks);
						$this->set('taskleaders', $taskleaders);
						$this->set('OwnerSharerUsers', $OwnerSharerUsers);

					} else {
						$this->set('projects', array());
						$this->set('user_id', null);
						$this->set('totaltasks', null);
						$this->set('taskleaders', null);
						$this->set('OwnerSharerUsers', array());
					}

					$this->render('/WorkCenters/partials/workcenterlist');
				}
			}
		}
	}

	public function ownerShares($project_id, $user_id) {

		$data = array();
		$view = new View();
		$common = $view->loadHelper('Common');

		if (isset($project_id) && !empty($project_id)) {

			foreach ($project_id as $pid) {

				$p_permission = $this->Common->project_permission_details($pid, $user_id);
				$user_project = $this->Common->userproject($pid, $user_id);

				if (isset($user_project) && !empty($user_project)) {
					$data['created']['owner'][] = 'owner';
				}

				if (isset($p_permission) && !empty($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level'] == 1) {
					$data['permission']['owner'][] = 'owner';

				}
				if (isset($p_permission) && !empty($p_permission['ProjectPermission']) && $p_permission['ProjectPermission']['project_level'] != 1) {
					$data['permission']['sharer'][] = 'sharer';
				}

				/*  group Work Permission and group permission and level check */
				$grp_id = $this->Group->GroupIDbyUserID($pid, $user_id);

				if (isset($grp_id) && !empty($grp_id)) {
					$group_permission = $this->Group->group_permission_details($pid, $grp_id);

					if (isset($group_permission['ProjectPermission']['project_level']) && $group_permission['ProjectPermission']['project_level'] == 1) {
						$data['group']['owner'][] = 'owner';
					} else if (isset($group_permission['ProjectPermission']['project_level']) && $group_permission['ProjectPermission']['project_level'] == 0) {
						$data['group']['sharer'][] = 'sharer';
					}
				}
			}
			return $data;
		}

	}

	public function totaltasks($projectids) {
		$view = new View();
		$TaskCenter = $view->loadHelper('TaskCenter');

		$element_keys = null;
		$element_key_staus = null;
		$Estatus = array();
		$element_assigned = array();

		$els = $TaskCenter->userElements($this->Session->read("Auth.User.id"), $projectids);

		if (isset($els) && !empty($els)) {
			foreach ($els as $ekey => $evalue) {
				$wsp_area_studio_status = wsp_area_studio_status($evalue);
				if (!$wsp_area_studio_status) {
					$Estatus[] = $evalue;
					$TaskAssigned = element_assigned($evalue);
					if (isset($TaskAssigned) && !empty($TaskAssigned['ElementAssignment'])) {
						$element_assigned[] = 'Assinged';
					}
				}
			}

		}

		return $total_elements = $Estatus;
	}

	public function totalTaskLeader($projectids) {
		$view = new View();
		$TaskCenter = $view->loadHelper('TaskCenter');

		$element_keys = null;
		$element_key_staus = null;
		$Estatus = array();
		$element_assigned = array();

		$els = $TaskCenter->userElements($this->Session->read("Auth.User.id"), $projectids);

		if (isset($els) && !empty($els)) {
			foreach ($els as $ekey => $evalue) {
				$wsp_area_studio_status = wsp_area_studio_status($evalue);
				if (!$wsp_area_studio_status) {
					// $Estatus[] = $evalue;
					$TaskAssigned = element_assigned($evalue);
					if (isset($TaskAssigned) && !empty($TaskAssigned['ElementAssignment'])) {
						$element_assigned[] = 'Assinged';
					}

				}

			}

		}

		return (isset($element_assigned) && !empty($element_assigned)) ? count($element_assigned) : 0;

	}

	public function project_users() {

		$view = new View();
		$ViewModel = $view->loadHelper('ViewModel');
		$Common = $view->loadHelper('Common');

		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			$response = [
				'success' => false,
				'content' => null,
			];

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->data;
				
				if (isset($post['pids']) && !empty($post['pids']) && isset($post['start_date']) && !empty($post['start_date']) && isset($post['end_date']) && !empty($post['end_date'])) {

					$projects = explode(',', $post['pids']);
					$start_date = $post['start_date'];
					$end_date = $post['end_date'];
					$user_id = $post['user_id'];

					$this->set('projects', $projects);
					$this->set('start_date', $start_date);
					$this->set('end_date', $end_date);
					$this->set('user_id', $user_id);

				}

				$this->render('/WorkCenters/project_users');

			}
		}
	}

	public function taskcalendar() {
		$view = new View();
		$ViewModel = $view->loadHelper('ViewModel');
		$Common = $view->loadHelper('Common');
		$this->layout = "calander";
		$this->autoRender = false;
		//if ($this->request->isAjax()) {
		$this->layout = 'ajax';
		$response = [
			'success' => false,
			'content' => null,
		];


 
 if(isset($_REQUEST['to']) && !empty($_REQUEST['to'])){
 
$sel_month = date("m", $_REQUEST['from']/1000);
$sel_year =  date("Y", $_REQUEST['from']/1000);
 }else{
	 
	$sel_month = date("m");
	$sel_year =  date("Y"); 
	 
 } 

		//pr($this->request->data); die;
		$inc = 200000000;
		$updated_Dates = '';
		$unavailableN = array();
		$unavailables = array();
		$user_id = $this->request->data['userid'];
		if (isset($this->request->data['userid']) && !empty($this->request->data['userid'])) {
			$user_id = $this->request->data['userid'];
			 
			$listdates = $ViewModel->not_available_dateswithstatus($user_id,$sel_month,$sel_year);
			 
			$i = 0;
			if (isset($listdates) && !empty($listdates)) {
				$unavailable_data = explode(",", $listdates);
				if (isset($unavailable_data) && !empty($unavailable_data)) {
					foreach ($unavailable_data as $undate) {
						$unavailables = explode("=", $undate . "=" . $inc);
						$unavailableN[$i]['omg'] = $unavailables[0];
						$unavailableN[$i]['av_status'] = $unavailables[1];
						$unavailableN[$i]['id'] = $unavailables[2];
						$unavailableN[$i]['start'] = strtotime($unavailables[0]) * 1000;
						$unavailableN[$i]['end'] = strtotime($unavailables[0]) * 1000;
						$inc++;
						$i++;
					}
				}

 
				$dd = json_encode($unavailableN);
				$updated_Dates = str_replace(array('[', ']'), '', $dd);
			}

		} else {

			$i = 0;
			if (isset($this->request->data['datelists']) && !empty($this->request->data['datelists'])) {
				$unavailable_data = explode(",", $this->request->data['datelists']);
				if (isset($unavailable_data) && !empty($unavailable_data)) {
					foreach ($unavailable_data as $undate) {
						$unavailables = explode("=", $undate . "=" . $inc);
						$unavailableN[$i]['omg'] = $unavailables[0];
						$unavailableN[$i]['av_status'] = $unavailables[1];
						$unavailableN[$i]['id'] = $unavailables[2];
						$unavailableN[$i]['start'] = strtotime($unavailables[0]) * 1000;
						$unavailableN[$i]['end'] = strtotime($unavailables[0]) * 1000;
						//$unavailables[]['id'] = $inc;
						/* $unavailables[$inc]['omg'] =  $unavailables[0];
								$unavailables[$inc]['status'] =  $unavailables[1]; */
						$inc++;
						$i++;
					}
				}
 
				$dd = json_encode($unavailableN);
				$updated_Dates = str_replace(array('[', ']'), '', $dd);
			}

		}

		//pr($updated_Dates); die;

		$post = $this->data;

		if (isset($this->request->data['id']) && !empty($this->request->data['id'])) {

			$eleids = explode(",", $this->request->data['id']);

			$this->Element->unbindModel(array('hasMany' => array('Links', 'Documents', 'Notes', 'Mindmaps', 'Permissions')), true);

			$elementsArr = array();
			$calenderElementsArr = array();

			$elements = $this->Element->find('all', array('conditions' => array('Element.id' => $eleids), 'fields' => array('Element.id', 'Element.title', 'Element.start_date', 'Element.end_date')));
			
			if (isset($elements) && !empty($elements)) {
				foreach ($elements as $key => $valE) {
					
					$calenderElementsArr[$valE['Element']['id']]['id'] = strip_tags($valE['Element']['id']);
					$calenderElementsArr[$valE['Element']['id']]['omg'] = $valE['Element']['start_date'];
					$calenderElementsArr[$valE['Element']['id']]['title'] = strip_tags($valE['Element']['title']);
					$calenderElementsArr[$valE['Element']['id']]['start_date'] = date('d M Y', strtotime($valE['Element']['start_date']));
					$calenderElementsArr[$valE['Element']['id']]['end_date'] = date('d M Y', strtotime($valE['Element']['end_date']));
					$calenderElementsArr[$valE['Element']['id']]['url'] = strip_tags(SITEURL . "entities/update_element/" . $valE['Element']['id'] . "#tasks");
					$calenderElementsArr[$valE['Element']['id']]['website_url'] = strip_tags(SITEURL . "work_centers/projectelements/");
					$calenderElementsArr[$valE['Element']['id']]['website_task_url'] = strip_tags(SITEURL . "work_centers/projectallelements/");
					//$calenderElementsArr[$valE['Element']['id']]['project_id'] = '';
					$calenderElementsArr[$valE['Element']['id']]['start'] = strtotime($valE['Element']['start_date']) * 1000;
					$calenderElementsArr[$valE['Element']['id']]['end'] = strtotime($valE['Element']['end_date']) * 1000;

				}
			}

			$i = 0;
			$color = '';
			if (isset($updated_Dates) && !empty($updated_Dates)) {
				$vass = '{ "success": 1, "result": [' . $updated_Dates . ",";
			} else {
				$vass = '{ "success": 1, "result": [ ';
			}
			$vas = "";

			foreach ($calenderElementsArr as $elem) {
				/* $color = str_replace("panel", "bg", $elem['color_code']);
						$elem['class'] = $color; */
				$vas .= json_encode($elem, JSON_UNESCAPED_SLASHES) . ",";
				$i++;
			}
			$vasss = ' ] } ';
			
			$allele = $vass . rtrim($vas, ",") . $vasss;
			 
			
			return $allele = $vass . rtrim($vas, ",") . $vasss;

			$useravaildatelist = $ViewModel->userAvaiability($user_id);
			$this->set('elements', json_encode($calenderElementsArr, JSON_UNESCAPED_SLASHES));

			//$this->render('/WorkCenters/partials/taskcalendar');

		}

		//}
	}

	public function noAvailableProjectSharers($project_id = null, $start_date = null, $end_date = null) {

		$view = new View();
		$Group = $view->loadHelper('Group');
		$ViewModel = $view->loadHelper('ViewModel');
		$Common = $view->loadHelper('Common');
		$freeUsersData = array();
		$this->autoRender = false;

		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			$response = [
				'success' => false,
				'content' => null,
				'userdates' => null,
			];
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->data;

				$project_id = $post['pids'];
				$start_date = $post['start_date'];
				$end_date = $post['end_date'];

				if (isset($project_id) && !empty($project_id)) {
					// get Project Total Tasks =============================================
					$totalEle = $totalWs = 0;
					$totalAssets = null;

					$sharerUsers = $ViewModel->projectusers($project_id);

					if (isset($sharerUsers) && !empty($sharerUsers)) {

						foreach ($sharerUsers as $sharerlist) {

							if (isset($sharerlist) && !empty($sharerlist)) {

								$user_id = $sharerlist;

								if ($user_id != $this->Session->read('Auth.User.id')) {
									$freeUsersData[] = $user_id;
								}

							}
						}

					}

				}

				$uniquefreeuser = array();
				if (isset($freeUsersData) && !empty($freeUsersData)) {
					$response['success'] = true;
					$usersids = array_unique($freeUsersData);
					$userdetail = $this->UserDetail->find('all', array(
						'conditions' => array('UserDetail.user_id' => $usersids),
						'fields' => array('UserDetail.user_id', 'UserDetail.first_name', 'UserDetail.last_name', 'UserDetail.full_name'))
					);
					$response['content'] = $userdetail;
				}

			}
		}

		return json_encode($response);

	}

	public function noworkingelementusers($project_id = null, $start_date = null, $end_date = null) {

		$view = new View();
		$Group = $view->loadHelper('Group');
		$ViewModel = $view->loadHelper('ViewModel');
		$Common = $view->loadHelper('Common');
		$freeUsersData = array();
		$this->autoRender = false;

		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			$response = [
				'success' => false,
				'content' => null,
				'userdates' => null,
			];
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->data;

				$project_id = $post['pids'];
				$start_date = $post['start_date'];
				$end_date = $post['end_date'];

				if (isset($project_id) && !empty($project_id)) {
					// get Project Total Tasks =============================================
					$totalEle = $totalWs = 0;
					$totalAssets = null;

					$sharerUsers = $ViewModel->projectsharers($project_id);

					if (isset($sharerUsers) && !empty($sharerUsers)) {

						foreach ($sharerUsers as $sharerlist) {

							if (isset($sharerlist) && !empty($sharerlist)) {

								$user_id = $sharerlist;

								$p_permission = $Common->project_permission_details($project_id, $user_id);
								$user_project = $Common->userproject($project_id, $user_id);
								$grp_id = $Group->GroupIDbyUserID($project_id, $user_id);

								if (isset($grp_id) && !empty($grp_id)) {
									$group_permission = $Group->group_permission_details($project_id, $grp_id);
									if (isset($group_permission['ProjectPermission']['project_level']) && $group_permission['ProjectPermission']['project_level'] == 1) {
										$project_level = $group_permission['ProjectPermission']['project_level'];
									}
								}
								if ((isset($group_permission['ProjectPermission']) && $group_permission['ProjectPermission']['project_level'] != 1) || (isset($p_permission['ProjectPermission']) && $p_permission['ProjectPermission']['project_level'] != 1)) {
									$wsp_permission = $Common->work_permission_details($project_id, $user_id);
									if (isset($group_permission['ProjectPermission']) && $group_permission['ProjectPermission']['project_level'] != 1) {
										$wsp_permission = $Group->group_work_permission_details($project_id, $grp_id);
									}
									$wsp_ids = pwid_workspace($wsp_permission, $project_id);
								}

								$projectIds[] = $project_id;
								$wsp_elements = array();
								$elements_by_date = [];
								$projectElementCount = 0;
								if (isset($wsp_ids) && !empty($wsp_ids)) {

									foreach ($wsp_ids as $id) {
										$all_elements = null;
										$arealist = $ViewModel->workspace_areas($id);

										foreach ($arealist as $v) {

											$e_permission = $Common->element_permission_details($id, $project_id, $user_id);

											if ((isset($grp_id) && !empty($grp_id))) {

												if (isset($e_permission) && !empty($e_permission)) {
													$e_permissions = $Group->group_element_permission_details($id, $project_id, $grp_id);
													$e_permission = array_merge($e_permission, $e_permissions);
												} else {
													$e_permission = $Group->group_element_permission_details($id, $project_id, $grp_id);
												}
											}

											if ((isset($e_permission) && !empty($e_permission))) {
												$all_elements = $ViewModel->area_elements_permissions($v['Area']['id'], false, $e_permission);
											}

											$wsp_elements[] = $all_elements;
											$all_elements = Set::extract($all_elements, '{n}.Element.id');

											// $elements_by_date = null;
											if (isset($all_elements) && !empty($all_elements)) {
												$dates = $start_date . " = " . $end_date;
												$elements_by_date = array_merge($elements_by_date, $ViewModel->running_elements_workcenter($all_elements, $dates));
											}

											$projectElementCount = ( isset($elements_by_date) && !empty($elements_by_date) ) ? count($elements_by_date) : 0;
											if ($projectElementCount <= 0) {
												$freeUsersData[] = $user_id;
											}
										}
									}
								}
							}
						}
					}
				}
				$uniquefreeuser = array();
				if (isset($freeUsersData) && !empty($freeUsersData)) {
					$response['success'] = true;
					$usersids = array_unique($freeUsersData);
					$userdetail = $this->UserDetail->find('all', array(
						'conditions' => array('UserDetail.user_id' => $usersids),
						'fields' => array('UserDetail.user_id', 'UserDetail.first_name', 'UserDetail.last_name', 'UserDetail.full_name'))
					);
					$response['content'] = $userdetail;
				}

			}
		}

		return json_encode($response);

	}

	public function usernotavaildateswithstatus() {
		$view = new View();
		$Group = $view->loadHelper('Group');
		$ViewModel = $view->loadHelper('ViewModel');
		$Common = $view->loadHelper('Common');
		$freeUsersData = array();
		$this->autoRender = false;

		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			$response = [
				'success' => false,
				'content' => null,
				'userdates' => null,
				'Owner' => null,
			];
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->data;
				if (isset($post['user_id']) && !empty($post['user_id'])) {
					$user_id = $post['user_id'];
					$pid = $post['pid'];
					$projectPermission = $ViewModel->projectPermitType($pid, $user_id);

					if ($ViewModel->userAvaiability($user_id)) {
						$response = ['success' => true];
						$response['Owner'] = $projectPermission;
						$response['userdates'] = $ViewModel->userAvaiability($user_id);
					} else {
						$response = ['success' => false];
						$response['userdates'] = array();
					}
				}

			}
			return json_encode($response);
		}
	}

	public function projectelements() {
		$view = new View();
		$Group = $view->loadHelper('Group');
		$ViewModel = $view->loadHelper('ViewModel');
		$Common = $view->loadHelper('Common');

		if ($this->request->isAjax()) {

			$response = [
				'success' => false,
				'content' => null,
				'projectid' => null,
				'start_date' => null,
				'end_date' => null,
				'user_id' => null,
			];

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->data;

				if (isset($post['eleids']) && !empty($post['eleids'])) {
					$eleids = explode(",", $post['eleids']);
					$project_id = $post['project_id'];
					$start_date = $post['start_date'];
					$end_date = $post['end_date'];
					$cdaynumber = $post['cdaynumber'];
					$user_id = $post['user_id'];

					$elelist = $ViewModel->getElementDetailbyids($eleids);
					if (isset($elelist) && !empty($elelist)) {

						$response['success'] = true;
						$response['content'] = $elelist;
						$response['user_id'] = $user_id;

						$response['projectid'] = $project_id;
						$response['start_date'] = $start_date;
						$response['end_date'] = $end_date;
						$response['daynumber'] = $cdaynumber;

					}

				}
			}
			$this->set('response', $response);
			$this->render('/WorkCenters/partials/projectelements');

		}
	}
	
	public function projectallelements() {
		$view = new View();
		$Group = $view->loadHelper('Group');
		$ViewModel = $view->loadHelper('ViewModel');
		$Common = $view->loadHelper('Common');

		if ($this->request->isAjax()) {

			$response = [
				'success' => false,
				'content' => null,
				'projectid' => null,
				'start_date' => null,
				'end_date' => null,
				'user_id' => null,
			];

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->data;

				if ( isset($post['projectids']) && !empty($post['projectids']) ) {
					$projectids = array();
					$eleids = explode(",", $post['eleids']);
					$project_id = $post['project_id'];
					$projectids = explode(',',$post['projectids']);
					$project_ids = $post['projectids'];
					$start_date = $post['start_date'];
					$end_date = $post['end_date'];
					$cdaynumber = $post['cdaynumber'];
					$user_id = $post['user_id'];

					$elementids = array();
					 
					if( !empty($projectids) && count($projectids) > 0 ){ 
						foreach( $projectids as $pidlist ){							
							$elementids[] = $ViewModel->project_elements($pidlist);
						}
					}
					
					//echo count($elementids)."==elementids=";
					
					//$elelist = $ViewModel->getElementDetailbyids($eleids);
					if (isset($projectids) && !empty($projectids)) {

						$response['success'] = true;
						//$response['content'] = $elelist;
						$response['content'] = $elementids;
						$response['user_id'] = $user_id;

						$response['projectid'] = $project_id;
						$response['projectids'] = $projectids;
						$response['project_ids'] = $project_ids;
						$response['start_date'] = $start_date;
						$response['end_date'] = $end_date;
						$response['daynumber'] = $cdaynumber;

					}

				}
			}
			$this->set('response', $response);
			$this->render('/WorkCenters/partials/projectallelements');

		}
	}
	

	public function userprojectelements() {
		$view = new View();
		$Group = $view->loadHelper('Group');
		$ViewModel = $view->loadHelper('ViewModel');
		$TaskCenter = $view->loadHelper('TaskCenter');

		if ($this->request->isAjax()) {

			$response = [
				'success' => false,
				'content' => null,
				'projectid' => null,
				'start_date' => null,
				'end_date' => null,
				'daynumber' => 0,
				'user_id' => null,
			];

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->data;
				$pid = $post['project_id'];
				$uid = $post['user_id'];
				$project_level = $post['projectlevel'];
				$dates = $post['dates'];

				$elements = $TaskCenter->userElements($uid, [$pid]);
				$all_elements = $ViewModel->running_elements_workcenter($elements, $dates);
				$all_elements = Set::extract($all_elements, '/Element/id');

				$project_id = $post['project_id'];
				if (isset($all_elements) && !empty($all_elements)) {
					$eleids = $all_elements;

					$elelist = $ViewModel->getElementDetailbyids($eleids);
					if (isset($elelist) && !empty($elelist)) {

						$response['success'] = true;
						$response['content'] = $elelist;
						$response['projectid'] = $project_id;
						$response['user_id'] = $uid;
					}

				} else {
					$response['success'] = true;
					$response['content'] = array();
					$response['projectid'] = $project_id;
					$response['user_id'] = $uid;
				}
			}
			$this->set('response', $response);
			$this->render('/WorkCenters/partials/userprojectelements');

		}
	}

	public function not_available_dates() {

		$this->layout = false;
		if ($this->request->isAjax()) {
			$response = [
				'success' => false,
				'content' => null,
			];
			$view = new View($this, false);
			$view->viewPath = 'WorkCenters/partials';

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->data;
				// pr($post, 1);
				$view->set('user_id', $post['user']);
			}
			$html = $view->render('not_available_dates');
			// $this->render('/WorkCenters/partials/not_available_dates');
			echo json_encode($html);
			exit();
		}
	}

	// test function
	public function getUserProjectPermissionDates_old($user_id = 1, $start_Date = '2018-10-01', $end_Date = '2024-10-31') {
		$current_user_id = CakeSession::read("Auth.User.id");

		$view = new View();
		$Group = $view->loadHelper('Group');
		$ViewModel = $view->loadHelper('ViewModel');
		$TaskCenter = $view->loadHelper('TaskCenter');
		$Common = $view->loadHelper('Common');
		$returnData = null;

		//$user_id = 1;
		$level = (isset($this->request->data['level'])) ? $this->request->data['level'] : 1;
		/* $start_date = date('Y-m-d');
		$end_date = date('Y-m-d');  */

		$start_date = $start_Date;
		$end_date = $end_Date;

		$ownersProjects = $this->ownerProjects($current_user_id, 1);
		$sharerProjects = $this->ownerProjects($user_id); 

		if (isset($ownersProjects) && isset($sharerProjects) && !empty($ownersProjects) && !empty($sharerProjects)) {
			 
			$allProjects = array_intersect($ownersProjects, $sharerProjects);

			$between = '';

			if (!empty($start_date) && empty($end_date)) {
				$between .= "AND (((date(Project.start_date) BETWEEN '" . $start_date . "' AND '" . $start_date . "') OR (date(Project.end_date) BETWEEN '" . $start_date . "' AND '" . $start_date . "')) OR (date(Project.start_date) <= '" . $start_date . "' AND date(Project.end_date) >= '" . $start_date . "') ) ";

			} else if (empty($start_date) && !empty($end_date)) {
				$between .= "AND (date(Project.end_date) BETWEEN '" . $end_date . "' AND '" . $end_date . "') ";
			} else if (!empty($start_date) && !empty($end_date)) {
				$between .= "AND (( (date(Project.start_date) BETWEEN '" . $start_date . "' AND '" . $end_date . "') ";
				$between .= "OR (date(Project.end_date) BETWEEN '" . $start_date . "' AND '" . $end_date . "')) OR (date(Project.start_date) <= '" . $start_date . "' AND date(Project.end_date) >= '" . $end_date . "')  ) ";

			}

			$arrayTostr = implode(",", $allProjects);
			$select = "SELECT id,title FROM projects as Project WHERE id IN (" . $arrayTostr . ") " . $between;

			$MyProjects = $this->Project->query($select);

			$project_id = array();
			$response['success'] = true;
			$response['start_date'] = $start_date;
			$response['end_date'] = $end_date;
			$response['user_id'] = $user_id;

			if (isset($MyProjects) && !empty($MyProjects)) {
				foreach ($MyProjects as $plists) {
					$project_id[] = $plists;
				}
			}

		}

		// Element lists =============================
		//pr($project_id);
		$i = 0;
		// free task users by projects
		$usersLists = $ViewModel->noTaskWorkingUsers($project_id, $start_date, $end_date);
		// pr($usersLists);
		$i = 0;
		foreach ($project_id as $projectdetail) {
			// get Project Total Tasks =============================================
			$totalEle = $totalWs = 0;
			$totalAssets = null;
			$projectData = $ViewModel->getProjectDetail($projectdetail['Project']['id']);
			$project_id = $projectdetail['Project']['id'];
			// pr($project_id);
			// pr($user_id, 1);
			$p_permission = $Common->project_permission_details($project_id, $user_id);
			$user_project = $Common->userproject($project_id, $user_id);
			$grp_id = $Group->GroupIDbyUserID($project_id, $user_id);

			if (isset($grp_id) && !empty($grp_id)) {

				$group_permission = $Group->group_permission_details($project_id, $grp_id);
				if (isset($group_permission['ProjectPermission']['project_level']) && $group_permission['ProjectPermission']['project_level'] == 1) {
					$project_level = $group_permission['ProjectPermission']['project_level'];
				}
			}

			if ((isset($user_project) && !empty($user_project)) || (isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level'] == 1) || (isset($project_level) && $project_level == 1)) {

				$wsList = get_project_workspace($projectdetail['Project']['id'], 1);
				$wsp_ids = array();
				if (isset($wsList) && !empty($wsList)) {
					$wsp_ids = array_keys($wsList);
				}

			} else if ((isset($group_permission['ProjectPermission']) && $group_permission['ProjectPermission']['project_level'] != 1) || (isset($p_permission['ProjectPermission']) && $p_permission['ProjectPermission']['project_level'] != 1)) {
				$wsp_permission = $Common->work_permission_details($project_id, $user_id);
				if (isset($group_permission['ProjectPermission']) && $group_permission['ProjectPermission']['project_level'] != 1) {
					$wsp_permission = $Group->group_work_permission_details($project_id, $grp_id);
				}
				$wsp_ids = pwid_workspace($wsp_permission, $project_id);
			}
			$projectIds[] = $project_id;
			$wsp_elements = array();
			$elements_by_date = [];
			$projectElementCount = 0;

			if (isset($wsp_ids) && !empty($wsp_ids)) {

				foreach ($wsp_ids as $id) {
					$all_elements = null;
					$arealist = $ViewModel->workspace_areas($id);

					foreach ($arealist as $v) {

						$e_permission = $Common->element_permission_details($id, $project_id, $user_id);

						if ((isset($grp_id) && !empty($grp_id))) {

							if (isset($e_permission) && !empty($e_permission)) {
								$e_permissions = $Group->group_element_permission_details($id, $project_id, $grp_id);
								$e_permission = array_merge($e_permission, $e_permissions);
							} else {
								$e_permission = $Group->group_element_permission_details($id, $project_id, $grp_id);
							}
						}

						if ((isset($e_permission) && !empty($e_permission))) {
							$all_elements = $ViewModel->area_elements_permissions($v['Area']['id'], false, $e_permission);
						}

						if (((isset($user_project) && !empty($user_project)) || (isset($project_level) && $project_level == 1) || (isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level'] == 1))) {
							$all_elements = $ViewModel->area_elements($v['Area']['id']);
						}

						$wsp_elements[] = $all_elements;
						$all_elements = Set::extract($all_elements, '{n}.Element.id');
						// pr($all_elements);
						// $elements_by_date = null;
						if (isset($all_elements) && !empty($all_elements)) {
							$dates = $start_date . " = " . $end_date;
							// $dates = "2020-09-05 = 2020-08-08";
							$elements_by_date = array_merge($elements_by_date, $ViewModel->elementsbydate_workcenter($all_elements, $dates));

						}
						$projectElementCount = ( isset($elements_by_date) && !empty($elements_by_date) ) ? count($elements_by_date) : 0;

					}
				}
			}

			$projectPermission = $ViewModel->projectPermitType($project_id, $user_id);
			$projectPermissionSelf = $ViewModel->projectPermitType($project_id, $current_user_id);

			$SelfuserPtype = 'Sharer';
			if (isset($projectPermissionSelf) && !empty($projectPermissionSelf) && $projectPermissionSelf == 1) {
				$SelfuserPtype = 'Owner';
			}

			$userPtype = 'Sharer';
			if (isset($projectPermission) && !empty($projectPermission) && $projectPermission == 1) {
				$userPtype = 'Owner';
			}

			//pr($elements_by_date);
			$elementforCalendar = Set::extract($elements_by_date, '{n}.Element.id');
			$elementidsstring = '';
			if (isset($elementforCalendar) && !empty($elementforCalendar)) {
				$elementidsstring = implode(",", $elementforCalendar);
			}
			/*====================================================================== */
			$listdates = '';
			$listdates = $ViewModel->not_available_dateswithstatus($user_id);

			$returnData[$i] = [
				'project' => $project_id,
				'element_count' => $projectElementCount,
			];
			$i++;

			// echo "project_id=" . $project_id;
			// echo "<br />";
			// echo "Project Element Count=" . $projectElementCount;
			// echo "<br />Elements";
			// pr($elements_by_date);

			//$projectElementCount Project element count
			//elements_by_date

		}
		pr($returnData, 1);
		return $returnData;
	}

	// test function
	public function getUserProjectPermissionDates($user_id = null, $start_Date = null, $end_Date = null) {
		$returnData = null;
		$view = new View();
		$Group = $view->loadHelper('Group');
		$ViewModel = $view->loadHelper('ViewModel');
		//$TaskCenter = $view->loadHelper('TaskCenter');
		$Common = $view->loadHelper('Common');

		App::import('Controller', 'WorkCenters');
		$WorkCenters = new WorkCentersController;

		$current_user_id = CakeSession::read("Auth.User.id");
		//$user_id = 1;
		$level = (isset($this->request->data['level'])) ? $this->request->data['level'] : 1;
		/* $start_date = date('Y-m-d');
		$end_date = date('Y-m-d');  */

		$start_date = $start_Date;
		$end_date = $end_Date; 
		
		$ownersProjects = $WorkCenters->ownerProjects($current_user_id, 1);
		$sharerProjects = $WorkCenters->ownerProjects($user_id); 
		
		if (isset($ownersProjects) && isset($sharerProjects) && !empty($ownersProjects) && !empty($sharerProjects)) {

			$allProjects = array_intersect($ownersProjects, $sharerProjects);

			$between = '';

			if (!empty($start_date) && empty($end_date)) {
				$between .= "AND (((date(Project.start_date) BETWEEN '" . $start_date . "' AND '" . $start_date . "') OR (date(Project.end_date) BETWEEN '" . $start_date . "' AND '" . $start_date . "')) OR (date(Project.start_date) <= '" . $start_date . "' AND date(Project.end_date) >= '" . $start_date . "') ) ";

			} else if (empty($start_date) && !empty($end_date)) {
				$between .= "AND (date(Project.end_date) BETWEEN '" . $end_date . "' AND '" . $end_date . "') ";
			} else if (!empty($start_date) && !empty($end_date)) {
				$between .= "AND (( (date(Project.start_date) BETWEEN '" . $start_date . "' AND '" . $end_date . "') ";
				$between .= "OR (date(Project.end_date) BETWEEN '" . $start_date . "' AND '" . $end_date . "')) OR (date(Project.start_date) <= '" . $start_date . "' AND date(Project.end_date) >= '" . $end_date . "')  ) ";

			}

			$arrayTostr = implode(",", $allProjects);
			$select = "SELECT id,title FROM projects as Project WHERE id IN (" . $arrayTostr . ") " . $between;

			$MyProjects = $this->Project->query($select);
			
			$project_ids = array();
			$response['success'] = true;
			$response['start_date'] = $start_date;
			$response['end_date'] = $end_date;
			$response['user_id'] = $user_id;

			if (isset($MyProjects) && !empty($MyProjects)) {
				foreach ($MyProjects as $plists) {
					$project_ids[] = $plists;
				}
			}
		}

		// $usersLists = $ViewModel->noTaskWorkingUsers($project_id, $start_date, $end_date);
		// pr($usersLists);

		// Element lists =============================
		//pr($project_id);
		$pindex = 0;
		$i = 0;
		// free task users by projects
		foreach ($project_ids as $projectdetail) {
			// get Project Total Tasks =============================================
			$totalEle = $totalWs = 0;
			$totalAssets = null;
			//pawan
			//$projectData = $ViewModel->getProjectDetail($projectdetail['Project']['id']);
			$project_id = $projectdetail['Project']['id'];

			$p_permission = $Common->project_permission_details($project_id, $user_id);$user_project = $Common->userproject($project_id, $user_id);
			$grp_id = $Group->GroupIDbyUserID($project_id, $user_id);

			if (isset($grp_id) && !empty($grp_id)) {

				$group_permission = $Group->group_permission_details($project_id, $grp_id);
				if (isset($group_permission['ProjectPermission']['project_level']) && $group_permission['ProjectPermission']['project_level'] == 1) {
					$project_level = $group_permission['ProjectPermission']['project_level'];
				}
			}

			$projectIds[] = $project_id;
			$wsp_elements = array();
			$elements_by_date = [];
			$projectElementCount = 0;

			$permitType = 'Owner';

			if ((isset($user_project) && !empty($user_project)) || (isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level'] == 1) || (isset($project_level) && $project_level == 1)) {
				// $returnData[$pindex]['type'] = 'owner';
				$permitType = 'Owner';
				$wsList = get_project_workspace($projectdetail['Project']['id'], 1);
				$wsp_ids = array();
				if (isset($wsList) && !empty($wsList)) {
					$wsp_ids = array_keys($wsList);
				}

			} else if ((isset($group_permission['ProjectPermission']) && $group_permission['ProjectPermission']['project_level'] != 1) || (isset($p_permission['ProjectPermission']) && $p_permission['ProjectPermission']['project_level'] != 1)) {
				$wsp_permission = $Common->work_permission_details($project_id, $user_id);
				if (isset($group_permission['ProjectPermission']) && $group_permission['ProjectPermission']['project_level'] != 1) {
					$wsp_permission = $Group->group_work_permission_details($project_id, $grp_id);
				}
				// $returnData[$pindex]['type'] = 'sharer';
				$permitType = 'Sharer';
				$wsp_ids = pwid_workspace($wsp_permission, $project_id);
			}			 
			
			if (isset($wsp_ids) && !empty($wsp_ids)) {

				foreach ($wsp_ids as $id) {
					$all_elements = null;
					//$arealist = $ViewModel->workspace_areas($id);
					//added by pawan
					$arealist = $ViewModel->workspace_areas_ids($id);
					 
					foreach ($arealist as $v) {

						$e_permission = $Common->element_permission_details($id, $project_id, $user_id);	
						
						if ((isset($grp_id) && !empty($grp_id))) {

							if (isset($e_permission) && !empty($e_permission)) {
								$e_permissions = $Group->group_element_permission_details($id, $project_id, $grp_id);
								$e_permission = array_merge($e_permission, $e_permissions);
							} else {
								$e_permission = $Group->group_element_permission_details($id, $project_id, $grp_id);
							}
						}

						if ((isset($e_permission) && !empty($e_permission))) {
							$all_elements = $ViewModel->area_elements_permissions($v['Area']['id'], false, $e_permission);
							if( isset($all_elements) && !empty($all_elements) ){
								$all_elements = Set::extract($all_elements, '{n}.Element.id');
							}	
						}
						
						if (((isset($user_project) && !empty($user_project)) || (isset($project_level) && $project_level == 1) || (isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level'] == 1))) {
							$all_elements = $ViewModel->area_elements($v['Area']['id']);
							if( isset($all_elements) && !empty($all_elements) ){
								$all_elements = Set::extract($all_elements, '{n}.Element.id');
							}	
						}
						$wsp_elements[] = $all_elements;
						//pr($all_elements,1);
						// $elements_by_date = null;
						if (isset($all_elements) && !empty($all_elements)) {
							$dates = $start_date . " = " . $end_date;
							//pawan
							$elements_by_date = array_merge($elements_by_date, $ViewModel->elementsByDates($all_elements, $dates));
							
							//pr($elements_by_date);

						}
						$projectElementCount = ( isset($elements_by_date) && !empty($elements_by_date) ) ? count($elements_by_date) : 0;
					} 
				}
				 
				
				if ($permitType == 'Sharer') {
					if (isset($projectElementCount) && !empty($projectElementCount)) {
						$returnData[$pindex]['element_count'] = $projectElementCount;
					}
				}
				$returnData[$pindex]['project'] = $project_id;
				$returnData[$pindex]['type'] = $permitType;

			}

			$pindex++;

			//pr($elements_by_date);
			$elementforCalendar = Set::extract($elements_by_date, '{n}.Element.id');
			$elementidsstring = '';
			if (isset($elementforCalendar) && !empty($elementforCalendar)) {
				$elementidsstring = implode(",", $elementforCalendar);
			}
		}
		// pr($returnData, 1);
		return $returnData;

	}

/************ PROJECT SCENARIO ***************/
	public function project_scenario() {
		$this->layout = false;
		$viewData = [];
		$current_user_id = $this->Session->read('Auth.User.id');
		$viewData['owner_projects'] = $this->ownerProjects($current_user_id, 1);
		$this->set($viewData);
		$this->render(DS . 'WorkCenters' . DS . 'partials' . DS . 'project_scenario');
	}

	public function get_scenario() {
		$this->layout = 'ajax';
		$dates = [];
		$chart_data = [];
		if ($this->request->isAjax()) {
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;

				$posted_users = $post['users'];
				$start_date = $post['start_date'];
				$end_date = $post['end_date'];

				$before = date('Y-m-d', strtotime($start_date . ' -7 days'));
				$after = date('Y-m-d', strtotime($end_date . ' +7 days'));

				$dates = [$before, $after];
				$chart_data['range'] = $dates;

				$data = null;
				
				if (isset($posted_users) && !empty($posted_users)) {
					foreach ($posted_users as $key => $user_id) {

						$userDetail = $this->objView->loadHelper('ViewModel')->get_user($user_id, null, 1);
						$user_image = SITEURL . 'images/placeholders/user/user_1.png';
						$user_name = 'Not Available';
						$job_title = 'Not Available';
						$email = 'Not Available';
						if (isset($userDetail) && !empty($userDetail)) {
							$user_name = $userDetail['UserDetail']['first_name'] . ' ' . $userDetail['UserDetail']['last_name'];
							$profile_pic = $userDetail['UserDetail']['profile_pic'];
							$email = $userDetail['User']['email'];
							$job_title = $userDetail['UserDetail']['job_title'];

							if (!empty($profile_pic) && file_exists(USER_PIC_PATH . $profile_pic)) {
								$user_image = SITEURL . USER_PIC_PATH . $profile_pic;
							}
						}
						$data[$key]['user'] = $user_image;
						$data[$key]['user_name'] = $user_name;
						$data[$key]['email'] = $email;
						$data[$key]['job_title'] = $job_title;
						$data[$key]['blocks'] = null;
						$all_dates = $all_date = null;
						$all_dates[] = ['start' => $before, 'end' => $after, 'type' => 'free'];
						$all_dates = $this->getContinuousAvailDates($user_id, $before, $after); 
						
						//pr($all_dates); die;
						
					if (isset($all_dates) && !empty($all_dates)) {
							usort($all_dates, function ($a, $b) {
								$dateA = strtotime($a['start']);
								$dateB = strtotime($b['start']);
								// ascending ordering, use `<=` for descending
								return $dateA >= $dateB;
							});
							foreach ($all_dates as $adkey => $advalue) {

								$st = date('Y-m-d', strtotime($advalue['start']));
								$en = date('Y-m-d', strtotime($advalue['end']));
								$data[$key]['blocks'][$adkey]['dates'] = [$st, $en];
								$stexp = explode(' ', $advalue['start']);
								$color = 'white';

								if (isset($advalue['type']) && $advalue['type'] == 'working') {
									$color = 'gray';
									$data[$key]['blocks'][$adkey]['clscolor'] = $color;
									$freeProjects = $this->getUserProjectPermissionDates($user_id, $st, $en);
									//$freeProjects = $this->getUserProjectPermissionDates($user_id, $st, $en);
									//pr($freeProjects);
									if (isset($freeProjects) && !empty($freeProjects)) {
										$project_popover = null;
										foreach ($freeProjects as $fkey => $fvalue) {
											$pdata = getByDbId('Project', $fvalue['project'], ['title']);
											$project_popover[$fkey]['project_id'] = $fvalue['project'];
											$project_popover[$fkey]['project_title'] = strip_tags($pdata['Project']['title']);
											$project_popover[$fkey]['chat_icon'] = ($this->user_id != $user_id) ? true : false;
											$project_popover[$fkey]['user'] = $email;
											$project_popover[$fkey]['user_id'] = $user_id;
											$project_popover[$fkey]['type'] = $fvalue['type'];
											if (isset($fvalue['element_count'])) {
												$project_popover[$fkey]['element_count'] = $fvalue['element_count'];
											}
										}
										$data[$key]['blocks'][$adkey]['project_popover'] = $project_popover;
									}
								} else if (isset($advalue['type']) && $advalue['type'] == 'partial') {
									// pr($advalue);
									$color = 'yellow';
									$data[$key]['blocks'][$adkey]['clscolor'] = $color;
									$undate = date('d M Y h:i A', strtotime($advalue['start']));
									$unavail_popover = [
										'title' => 'Unavailable',
										'dates' => $advalue['from_till'] . ' ' . $undate . ((isset($advalue['same_partial']) && !empty($advalue['same_partial'])) ? $advalue['same_partial'] : ''),
									];
									$data[$key]['blocks'][$adkey]['unavail_popover'] = $unavail_popover;
								} else if (isset($advalue['type']) && $advalue['type'] == 'full') {
									$color = 'red';
									$data[$key]['blocks'][$adkey]['clscolor'] = $color;
									$unstart = date('d M Y', strtotime($advalue['start']));
									$unend = date('d M Y', strtotime($advalue['end']));
									$date_str = $unstart;
									$datetime1 = new DateTime(date('Y-m-d', strtotime($advalue['start'])));
									$datetime2 = new DateTime(date('Y-m-d', strtotime($advalue['end'])));
									$interval = $datetime1->diff($datetime2);
									if ($interval->days >= 1) {
										$date_str = $unstart . ' - ' . $unend;
									}
									$unavail_popover = [
										'title' => 'Unavailable',
										'dates' => $date_str,
									];
									$data[$key]['blocks'][$adkey]['unavail_popover'] = $unavail_popover;
								} else if (isset($advalue['type']) && $advalue['type'] == 'free') {
									$color = 'white';
									$data[$key]['blocks'][$adkey]['clscolor'] = $color;
									$unstart = date('d M Y', strtotime($advalue['start']));
									$unend = date('d M Y', strtotime($advalue['end']));
									$date_str = $unstart;
									$datetime1 = new DateTime(date('Y-m-d', strtotime($advalue['start'])));
									$datetime2 = new DateTime(date('Y-m-d', strtotime($advalue['end'])));
									$interval = $datetime1->diff($datetime2);
									if ($interval->days >= 1) {
										$date_str = $unstart . ' - ' . $unend;
									}
									$avail_popover = [
										'title' => 'Unavailable',
										'dates' => $date_str,
									];
									$data[$key]['blocks'][$adkey]['avail_popover'] = $avail_popover;
								}
							}
						} 
					}  
					$chart_data['data'] = $data;
				}
			}
		}
		echo json_encode($chart_data);
		exit();
	}

	public function getContinuousAvailDates($user_id = null, $start_date = null, $end_date = null) {
		$this->loadModel('Availability');
		$from_range = $this->objView->loadHelper('ViewModel')->get_continue_date($start_date, $end_date);
		
		  
		$noAvailableDates = '';
		$yesElementDates = array();
		$newDatesArray = array();
		//
		if (isset($from_range) && !empty($from_range)) {
			foreach ($from_range as $key => $datelist) {

				$dates = $datelist;
				$starting_date = $datelist;
				$ending_date = $datelist;

				$query = '';
				$order = '';
				$query .= "SELECT count(Availability.id) as availtotal, Availability.avail_start_date,Availability.avail_end_date ";
				$query .= "FROM availabilities as Availability WHERE user_id = " . $user_id . "  ";
				$order = "ORDER BY Availability.id ASC ";

				if (isset($dates) && !empty($dates)) {
					$order = "ORDER BY id ASC ";

					if (!empty($starting_date) && !empty($ending_date)) {

						$query .= " AND ( ";

						$query .= " ( (STR_TO_DATE(LEFT(Availability.avail_start_date,LOCATE(' ',Availability.avail_start_date)),'%Y-%m-%d') BETWEEN '" . $starting_date . "' AND '" . $ending_date . "') ";

						$query .= " OR (STR_TO_DATE(LEFT(Availability.avail_end_date,LOCATE(' ',avail_end_date)),'%Y-%m-%d') BETWEEN '" . $starting_date . "' AND '" . $ending_date . "') ) ";

						$query .= " OR (STR_TO_DATE(LEFT(Availability.avail_start_date,LOCATE(' ',Availability.avail_start_date)),'%Y-%m-%d') <= '" . $starting_date . "' AND STR_TO_DATE(LEFT(Availability.avail_end_date,LOCATE(' ',Availability.avail_end_date)),'%Y-%m-%d') >= '" . $ending_date . "') ) ";
					}
				}
				$query .= $order;

				$datas = $this->Availability->query($query);

				foreach ($datas as $showdates) {
					$breakstdates = explode(" ", $showdates['Availability']['avail_start_date']);
					$breakenddates = explode(" ", $showdates['Availability']['avail_end_date']);
					if ($breakstdates[0] == $datelist) {
						if ($breakstdates[1] == '00:00:00') {
							$noAvailableDates .= $datelist . ",";
						} else {
							$noAvailableDates .= $datelist . ' ' . $breakstdates[1] . " " . $breakstdates[2] . ",";
						}
					} else {
						if ($this->objView->loadHelper('ViewModel')->isBetweenRange($breakstdates[0], $breakenddates[0], $datelist)) {
							$noAvailableDates .= $datelist . ",";
						}

						if ($breakenddates[0] == $datelist) {

							if ($breakenddates[1] == '00:00:00') {
								$noAvailableDates .= $datelist . ",";
							} else {
								$noAvailableDates .= $datelist . ' ' . $breakenddates[1] . " " . $breakenddates[2] . ",";
							}
						}
					}
				}
			}

		}

		$all_dates = $free_working_range = [];
		if (!empty($noAvailableDates)) {
			$noAvailableDates = rtrim($noAvailableDates, ',');
			$gdates = $this->groupContinuousDates($noAvailableDates, $user_id);

			if (!empty($gdates)) {
				$all_dates = $gdates['full_date'];
				$free_dates = $this->getFreeDates($gdates, $start_date, $end_date);
				// pr($free_dates, 1);
				if (!empty($free_dates)) {
					$free_range = $working_range = [];
					foreach ($free_dates as $key => $value) {

						$free_working = $this->getProjectByDate(date('Y-m-d', strtotime($value['start'])), date('Y-m-d', strtotime($value['end'])), $user_id);
						$free_range = $this->datesInRange($free_working['free']);
						$working_range = $this->datesInRange($free_working['working']);
						// e('free_range', 1);
						if (isset($free_range) && !empty($free_range)) {
							foreach ($free_range as $fkey => $fvalue) {
								$free_range_data = null;
								$free_range_data[] = ['start' => $fvalue[0], 'end' => $fvalue[1], 'type' => 'free'];
								$all_dates = array_merge($free_range_data, $all_dates);
							}
						}
						if (isset($working_range) && !empty($working_range)) {
							foreach ($working_range as $wkey => $wvalue) {
								$working_range_data = null;
								$working_range_data[] = ['start' => $wvalue[0], 'end' => $wvalue[1], 'type' => 'working'];
								$all_dates = array_merge($working_range_data, $all_dates);
							}
						}
					}
					// $all_dates = array_merge($free_dates, $all_dates);
				}
			}
		} else {
			$free_range = $working_range = [];
			$free_working = $this->getProjectByDate(date('Y-m-d', strtotime($start_date)), date('Y-m-d', strtotime($end_date)), $user_id);
			 
			if(isset($free_working['free']) && !empty($free_working['free'])) {
				$free_range = $this->datesInRange($free_working['free']);
			}
			if(isset($free_working['working']) && !empty($free_working['working'])) {
				$working_range = $this->datesInRange($free_working['working']);
			}
			if (isset($free_range) && !empty($free_range)) {
				foreach ($free_range as $fkey => $fvalue) {
					$free_range_data = null;
					$free_range_data[] = ['start' => $fvalue[0], 'end' => $fvalue[1], 'type' => 'free'];
					$all_dates = array_merge($free_range_data, $all_dates);
				}

			}
			if (isset($working_range) && !empty($working_range)) {
				foreach ($working_range as $wkey => $wvalue) {
					$working_range_data = null;
					$working_range_data[] = ['start' => $wvalue[0], 'end' => $wvalue[1], 'type' => 'working'];
					$all_dates = array_merge($working_range_data, $all_dates);
				}

			}
		}
		if (isset($all_dates) && !empty($all_dates)) {
			usort($all_dates, function ($a, $b) {
				$dateA = strtotime($a['start']);
				$dateB = strtotime($b['start']);
				// ascending ordering, use `<=` for descending
				return $dateA >= $dateB;
			});
		} else {
			$all_dates[] = ['start' => $start_date, 'end' => $end_date, 'type' => 'free'];
		}
		// pr($all_dates);
		return $all_dates;
	}

	public function groupContinuousDates($datestring, $user_id = null) {
		$this->loadModel('Availability');

		$datearray = null;
		$resultArray = array();
		$resultArraywithsts = array();
		$index = 1;
		$indexX = -1;
		$last = strtotime(date('Y-m-d'));
		$laster = date('Y-m-d');
		//$last = strtotime(date('Y-m-d'). ' -1 day');
		//$last = 0;
		$out = "";

		if (isset($datestring) && !empty($datestring)) {
			$datearray = explode(",", $datestring);
		}
		foreach ($datearray as $actualdates) {

			$date = explode(" ", $actualdates);

			$ts = strtotime($actualdates);
			// $ts = strtotime($date[0]);

			if (false !== $ts) {
				$cc = date('H:i', $last);
				// pr($cc);
				if ($cc != '00:00') {

					$last = strtotime(date('Y-m-d'));
					$laster = date('Y-m-d');
				}
				$diff = $ts - $last;

				if ($diff == 0) {

					$last = strtotime(date('Y-m-d') . ' -1 day');
					$laster = date('Y-m-d', strtotime(date('Y-m-d') . ' -1 day'));
					$diff = $ts - $last;

				}

				$dd = date_diff(date_create(date('Y-m-d h:i', $ts)), date_create(date('Y-m-d h:i', $last)));

				$datetime1 = new DateTime(date('Y-m-d h:i', $ts));
				$datetime2 = new DateTime(date('Y-m-d h:i', $last));
				$interval = $datetime1->diff($datetime2);

				if ($diff > 90000 && count($date) <= 1) {
					$index = $index + 1;
					$resultArray[$index][] = $actualdates;
				} else if (count($date) > 1) {

					$indexX = $indexX - 1;
					$resultArray[$indexX][] = $actualdates;
				} else if ($diff > 0 && count($date) <= 1) {
					$resultArray[$index][] = $actualdates;
				}

				$last = $ts;
			}
		}
		$p = 0;
		$finalDates = null;
		$final_index = 0;
		foreach ($resultArray as $a) {

			if (count($a) >= 1) {
				$firstDate = $a[0];
				$firstDateBits = explode('-', $firstDate);
				$lastDate = $a[count($a) - 1];
				$lastDateBits = explode('-', $lastDate);
				if ($firstDateBits[1] === $lastDateBits[1]) {
					if (!empty($firstDate) && empty($lastDate)) {
						$out .= intval($firstDateBits[2]) . " " . date("M", strtotime($firstDate)) . " " . $firstDateBits[0];
					} else {
						$chkPartialStart = explode(' ', $firstDate);
						$chkPartialEnd = explode(' ', $lastDate);
						// pr($chkPartialStart);

						if ($firstDateBits[2] !== $lastDateBits[2] && count($chkPartialEnd) == 1) {
							// e('full');
							// pr($firstDateBits[2]);
							// pr($lastDateBits[2]);
							// e('----------');
							$finalDates['full_date'][$final_index]['start'] = intval($firstDateBits[0]) . "-" . date("m", strtotime($firstDate)) . "-" . $firstDateBits[2];
							$finalDates['full_date'][$final_index]['end'] = intval($lastDateBits[0]) . "-" . date("m", strtotime($lastDate)) . "-" . $lastDateBits[2];
							$finalDates['full_date'][$final_index]['type'] = 'full';

						} else {
							// e('partial');
							// pr($firstDate);

							$ccd = date('h:i', strtotime($firstDate));
							$ccds = date('h:iA', strtotime($firstDate));

							if ($ccds != "12:00AM") {

								$avdate = $this->Availability->find('first', array('conditions' => array('Availability.avail_start_date' => $firstDate, 'Availability.user_id' => $user_id, "STR_TO_DATE(LEFT(avail_end_date,LOCATE(' ',avail_end_date)),'%Y-%m-%d')" => date('Y-m-d', strtotime($firstDate))))); 

								$avEDdate = $this->Availability->find('first', array('conditions' => array('Availability.avail_end_date' => $firstDate, 'Availability.user_id' => $user_id, "STR_TO_DATE(LEFT(avail_start_date,LOCATE(' ',avail_start_date)),'%Y-%m-%d') <" => date('Y-m-d', strtotime($firstDate)))));
								$samePartialDates = false;

								if (isset($avdate) && !empty($avdate)) {
									$avFdate = " - " . date('h:iA', strtotime($avdate['Availability']['avail_end_date']));
									$avFsdate = "";
									$samePartialDates = true;
								} else {

									$avFsdate = " from ";

									if (date('Y-m-d', $last) == date('Y-m-d', strtotime($firstDate))) {
										$avFsdate = "till ";
									} else {

										$avFdate = "";
									}
									$avFdate = "";
									if (isset($avEDdate) && !empty($avEDdate)) {
										$avFsdate = " till ";
									}

								}
								$datebite = explode(' ', $firstDateBits[2]);
								$datestr = 0;
								if (isset($datebite) && !empty($datebite)) {
									$datestr = $datebite[0];
								}
								// e(intval($firstDateBits[0]) . "-" . date("m", strtotime($firstDate)) . "-" . $datestr . " " . $ccds);
								/*$finalDates['full_date'][$final_index]['start'] = intval($firstDateBits[0]) . "-" . date("m", strtotime($firstDate)) . "-" . $datestr . " " . $ccds . $avFdate;
								$finalDates['full_date'][$final_index]['end'] = intval($firstDateBits[0]) . "-" . date("m", strtotime($firstDate)) . "-" . $datestr . " " . $ccds . $avFdate;*/
								$finalDates['full_date'][$final_index]['start'] = intval($firstDateBits[0]) . "-" . date("m", strtotime($firstDate)) . "-" . $datestr . " " . $ccds;
								$finalDates['full_date'][$final_index]['end'] = intval($firstDateBits[0]) . "-" . date("m", strtotime($firstDate)) . "-" . $datestr . " " . $ccds;
								$finalDates['full_date'][$final_index]['type'] = 'partial';
								$finalDates['full_date'][$final_index]['from_till'] = $avFsdate;
								if ($samePartialDates) {
									$finalDates['full_date'][$final_index]['same_partial'] = $avFdate;
								}
							} else {
								$finalDates['full_date'][$final_index]['start'] = intval($firstDateBits[2]) . "-" . date("m", strtotime($firstDate)) . "-" . $firstDateBits[0];
								$finalDates['full_date'][$final_index]['end'] = intval($firstDateBits[2]) . "-" . date("m", strtotime($firstDate)) . "-" . $firstDateBits[0];
								$finalDates['full_date'][$final_index]['type'] = 'full';
							}

						}

					}

				} else {

					/* $out .= '<span class="workinfo"> 22222 ';
						$out .= intval($firstDateBits[2]) . " " . date("M", strtotime($firstDate)) . " " . $firstDateBits[0] . " - ";
						$out .= intval($lastDateBits[2]) . " " . date("M", strtotime($lastDate)) . " " . $firstDateBits[0];
					*/
					$ccd = date('h:i', strtotime($firstDate));
					$ccds = date('A', strtotime($firstDate));
					if ($ccds != "12:00") {
						$finalDates['full_date'][$final_index]['start'] = intval($firstDateBits[2]) . "-" . date("m", strtotime($firstDate)) . "-" . $firstDateBits[0];
						$finalDates['full_date'][$final_index]['end'] = intval($lastDateBits[2]) . "-" . date("m", strtotime($lastDate)) . "-" . $lastDateBits[0];
						$finalDates['full_date'][$final_index]['type'] = 'full';
					} else {
						$finalDates['full_date'][$final_index]['start'] = intval($firstDateBits[2]) . "-" . date("m", strtotime($firstDate)) . "-" . $firstDateBits[0];
						$finalDates['full_date'][$final_index]['end'] = intval($lastDateBits[2]) . "-" . date("m", strtotime($lastDate)) . "-" . $lastDateBits[0];
						$finalDates['full_date'][$final_index]['type'] = 'full';
					}

				}

			}
			$p++;
			$final_index++;
		}

		return $finalDates;
	}

	public function datesInRange($resultArray = null) {
		$resultArray = array_values($resultArray);
		$finalDates = [];
		$index = 0;
		if (isset($resultArray) && !empty($resultArray)) {
			for ($i = 0; $i < count($resultArray); $i++) {
				$firstDate = strtotime($resultArray[$i]);
				$secondDate = ($i < count($resultArray) - 1) ? strtotime($resultArray[$i + 1]) : strtotime($resultArray[$i]);
				$datediff = $secondDate - $firstDate;
				$daysDiff = round($datediff / (60 * 60 * 24));
				$finalDates[$index][] = $resultArray[$i];
				if ($daysDiff == 1) {
					$finalDates[$index][] = $resultArray[$i];
				} else if ($daysDiff > 1) {
					$finalDates[$index][] = $resultArray[$i];
					$index++;
				}
			}
		}
		$fixDates = [];
		if (isset($finalDates) && !empty($finalDates)) {
			foreach ($finalDates as $key => $value) {
				$fixDates[] = [$value[0], $value[count($value) - 1]];
			}
		}
		// pr($fixDates);
		return $fixDates;
	}

	public function getFreeDates($dates = null, $start_date = null, $end_date = null) {

		$to_from_range = $this->objView->loadHelper('ViewModel')->get_continue_date($start_date, $end_date);

		$start_point = date('d-m-Y', strtotime($dates['full_date'][0]['start'] . ' -1 day'));
		$end_point = date('d-m-Y', strtotime($dates['full_date'][count($dates['full_date']) - 1]['end']));
		// pr($dates);
		// e('end_point->' . $end_point, 1);
		$free_dates = null;
		$free_index = 0;
		$free_dates[$free_index]['start'] = date('d-m-Y', strtotime($to_from_range[0]));
		$free_dates[$free_index]['end'] = $start_point;
		$free_dates[$free_index]['type'] = 'working';

		if (isset($dates) && !empty($dates)) {
			for ($i = 0; $i < count($dates['full_date']) - 1; $i++) {
				$st = $dates['full_date'][$i]['end'];
				$en = $dates['full_date'][$i + 1]['start'];

				$datetime1 = new DateTime(date('Y-m-d', strtotime($st)));
				$datetime2 = new DateTime(date('Y-m-d', strtotime($en)));
				$interval = $datetime1->diff($datetime2);

				$free_index++;
				if ($interval->days > 1) {
					$free_dates[$free_index] = [
						'start' => date('d-m-Y', strtotime($dates['full_date'][$i]['end'] . ' +1 day')),
						'end' => date('d-m-Y', strtotime($dates['full_date'][$i + 1]['start'] . ' -1 day')),
						'type' => 'working'];

				}

			}
		}
		$free_index = $free_index + 1;
		$free_dates[$free_index]['start'] = date('d-m-Y', strtotime($end_point . ' +1 day'));
		$free_dates[$free_index]['end'] = date('d-m-Y', strtotime($to_from_range[count($to_from_range) - 1]));
		$free_dates[$free_index]['type'] = 'working';

		return $free_dates;
	}

	public function getProjectByDate($start_date = null, $end_date = null, $user_id = null) {

		$allDates = $this->objView->loadHelper('ViewModel')->get_continue_date($start_date, $end_date);

		// pr($allDates);
		$ownerProjects = $this->ownerProjects($this->Session->read('Auth.User.id'), 1);
		$sharerProjects = $this->ownerProjects($user_id);
		$allProjects = array_intersect($ownerProjects, $sharerProjects);

		$data = $data2 = [];
		// mpr($ownerProjects, $sharerProjects, $allProjects);
		if (isset($allProjects) && !empty($allProjects)) {
			foreach ($allProjects as $key => $value) {
				$project_id = $value;
				foreach ($allDates as $date) {
					$user_elements = $this->objView->loadHelper('TaskCenter')->userElements($user_id, [$project_id]);
					$passed_date = $date . ' = ' . $date;
					 
					$found_element = $this->objView->loadHelper('ViewModel')->elementsByDates($user_elements, $passed_date);
					// $found_element = [];
					if (isset($found_element) && !empty($found_element)) {
						$data[$project_id]['date'][$date] = 1;
					} else {
						$data[$project_id]['date'][$date] = 2;
					}
				}  
			}
		}  
		
		
		 $data = Set::extract($data, '{n}.date');
		
		 
		
		$work = $free = [];
		foreach ($allDates as $date) {
			$found = $found1 = false;
			foreach ($data as $key => $value) {
				if ($value[$date] == 1 && !in_array($date, $work)) {
					$work[] = $date;
					$found = true;
					unset($value[$date]);
				} else if (!in_array($date, $work)) {
					if ($value[$date] == 2 && !$found && !in_array($date, $free) && !in_array($date, $work)) {
						$free[] = $date;
						$found1 = true;
					}
				}
			}
		}

		//pr($data);
		// pr($work);
		$freeDates = array_diff($free, $work);
		// e('--------------------------------- free --------------------------------');
		// pr($free);
		// e('--------------------------------- BUSY --------------------------------');
		// pr($work);
		// // e('--------------------------------- FREE --------------------------------');

		$combineDates = ['free' => $freeDates, 'working' => $work];
		  
		return $combineDates;
	}
/************ END PROJECT SCENARIO ***************/

}