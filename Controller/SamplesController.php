<?php
class SamplesController extends AppController {

	public $name = 'Samples';

	public $uses = array('Project', "UserProject", "ProjectPermission", "ElementPermission", "Element", "User");
	public $helpers = [];
	public $objView = null;
	public $components = array( 'Users');
	public $counter = 0;

	public function beforeFilter() {
		parent::beforeFilter();
		$view = new View();
		$this->objView = $view;
		$this->Auth->allow('mongo_update');
	}

	public function index($run = false) {
		// pr($_SERVER['REMOTE_ADDR'] );die;
		$this->layout = 'inner';
		$area_id = 264;
		$wsp_id = 98;
		$project_id = 76;
		$user_id = 2;
		$insert_total = 1500;

		if($run == 2) {
			$count = $neighbors = $this->Element->find("count", [
				'conditions' => [
					'Element.area_id' => $area_id,
				],
			]);
			$dataEE = $this->Element->find('first', array('conditions' => array('Element.area_id' => $area_id), 'order' => array('Element.sort_order' => 'DESC'), 'fields' => 'Element.sort_order', 'recursive' => -1));

			if (isset($dataEE) && !empty($dataEE)) {
				if (isset($dataEE['Element']['sort_order']) && $dataEE['Element']['sort_order'] > 0) {
					$sort_order = $dataEE['Element']['sort_order'] + 1;
				} else {
					$sort_order = (!is_null($count) && $count > 0) ? ($count + 1) : 1;
				}
			} else {
				$sort_order = (!is_null($count) && $count > 0) ? ($count + 1) : 1;
			}

			for ($i = 0; $i < $insert_total; $i++) {
				$title = 'Element - ' . $i;

				$earr = [];
				$earr['Element']['area_id'] = $area_id;
				$earr['Element']['updated_user_id'] = $user_id;
				$earr['Element']['title'] = $title;
				$earr['Element']['description'] = $title;
				$earr['Element']['comments'] = $title;
				$earr['Element']['sort_order'] = $sort_order;

				// $this->Element->query("INSERT INTO elements SET area_id = '".$area_id."', updated_user_id = '".$user_id."', title = '".($title.$i)."', description = '".($title.$i)."', comments = '".($title.$i)."', sort_order = '".$sort_order."'");
				$this->Element->id = null;
				if($this->Element->save($earr)) {
					$insert_id = $this->Element->getLastInsertId();
					// pr($insert_id, 1);
					$this->ElementPermission->id = null;
					$arr = [];
					$arr['ElementPermission']['user_id'] = $user_id;
					$arr['ElementPermission']['element_id'] = $insert_id;
					$arr['ElementPermission']['project_id'] = $project_id;
					$arr['ElementPermission']['workspace_id'] = $wsp_id;
					$arr['ElementPermission']['permit_read'] = 1;
					$arr['ElementPermission']['permit_add'] = 1;
					$arr['ElementPermission']['permit_edit'] = 1;
					$arr['ElementPermission']['permit_delete'] = 1;
					$arr['ElementPermission']['permit_copy'] = 1;
					$arr['ElementPermission']['permit_move'] = 1;
					$arr['ElementPermission']['is_editable'] = 1;

					$this->ElementPermission->save($arr);
				}
			}
		}

	}

	public function datetime($run = false) {
		// pr($_SERVER['REMOTE_ADDR'] );die;
		$this->layout = 'inner';


	}

	public function index1($run = false) {
		$this->layout = 'inner';
		$area_id = 56;
		$wsp_id = 15;
		$project_id = 50;
		$user_id = 20;
		$insert_total = 500;

		if($run == 2) {
			$this->loadModel('ProjectComment');

			for ($i = 0; $i < $insert_total; $i++) {
				$title = 'Project Comment - ' . $i;

				$earr = [];
				$earr['ProjectComment']['project_id'] = $project_id;
				$earr['ProjectComment']['user_id'] = $user_id;
				$earr['ProjectComment']['comments'] = $title;

				// $this->Element->query("INSERT INTO elements SET area_id = '".$area_id."', updated_user_id = '".$user_id."', title = '".($title.$i)."', description = '".($title.$i)."', comments = '".($title.$i)."', sort_order = '".$sort_order."'");
				$this->ProjectComment->id = null;
				if($this->ProjectComment->save($earr)) {

				}
			}
		}

	}

	public function d3($run = false) {
		$this->layout = 'inner';

	}

	public function recursive_all($user_id = null, $project_id = null ) {
	    $parent_data = $this->ProjectPermission->find('first', ['conditions' => [
		    	'ProjectPermission.user_id' => $user_id,
		    	'ProjectPermission.user_project_id' => $project_id,
		    ],
			'fields' => ['id', 'user_id'],
			'recursive' => -1
		]);
	    if(isset($parent_data) && !empty($parent_data)) {
			echo '<ul class="nav nav-list tree" style="list-style: none;">';
	    	$parent_data = $parent_data['ProjectPermission'];
			    $child_data = $this->ProjectPermission->find('first', ['conditions' => [
				    	'ProjectPermission.parent_id' => $parent_data['id'],
				    	'ProjectPermission.user_project_id' => $project_id,
				    ],
					'fields' => ['id', 'user_id', 'parent_id', 'user_project_id' ],
					'recursive' => -1
				]);
			if(isset($child_data) && !empty($child_data)){
		    	if(isset($child_data['ProjectPermission']['parent_id']) && !empty($child_data['ProjectPermission']['parent_id'])){
		    		echo '<li class="has-sub-cat">';
		    		echo($this->Common->userFullName($child_data['ProjectPermission']['user_id']));
		    		echo '</li>';
					$this->recursive_all($child_data['ProjectPermission']['user_id'], $child_data['ProjectPermission']['user_project_id']);
				}
			}
			echo '</ul>';
		}
	}

	public function tree_view() {
		$this->layout = 'inner';
		get_recent('link');
	}

	public function add_user_to_mongo($user_id, $project_id) {
		$this->layout = 'inner';
		$this->Users->userConnections($user_id, $project_id);
	}

	public function circles() {
		$this->layout = 'inner';
	}

	public function scroll_paging() {
		$this->layout = 'inner';
	}

	public function get_page() {
		$this->layout = false;
		$view = new View($this, false);
		$view->viewPath = 'Samples';
		if ($this->request->is('post') || $this->request->is('put')) {
			$post = $this->request->data;
			$project_id = $post['project'];
			$page = $post['page'];
			$tasks = $this->objView->loadHelper('ViewModel')->getUserTasksProjectPaging($project_id, $page);
			$view->set('tasks', $tasks);
		}

		$html = $view->render('get_page');

		echo json_encode($html);
		exit();
	}

	public function video() {
		$this->layout = 'inner';


		$crumb = [

			'Summary' => [
				'data' => [
					'url' => '/projects/index/2',
					'class' => 'tipText',
					'title' => "Testing crumb",
					'data-original-title' => "Testing crumb",
				],
			],
			'last' => [
				'data' => [
					'title' => "Title",
					'data-original-title' => "Title",
				],
			],
		];
		$this->set('crumb', $crumb);
	}


	public function calling() {
		$this->layout = 'inner';


		$crumb = [

			'Summary' => [
				'data' => [
					'url' => '/projects/index/2',
					'class' => 'tipText',
					'title' => "Testing crumb",
					'data-original-title' => "Testing crumb",
				],
			],
			'last' => [
				'data' => [
					'title' => "Title",
					'data-original-title' => "Title",
				],
			],
		];
		$this->set('crumb', $crumb);
	}

	public function dummyusers($count = 1) {
        $this->loadModel('User');
        $this->loadModel('UserDetail');
        $names = ['Patty', 'Furniture', 'Paddy', 'Furniture', 'Olive', 'Yew', 'Aida', 'Bugg', 'Maureen', 'Biologist', 'Teri', 'Dactyl', 'Peg', 'Legge', 'Allie', 'Grater', 'Liz', 'Erd', 'A.', 'Mused', 'Constance', 'Noring', 'Lois', 'Nominator', 'Minnie', 'Ryder', 'Lynn', 'Leeum', 'Ann', 'Recital', 'Ray', 'Sun', 'Lee', 'Sun', 'Ray', 'Sin', 'Isabelle', 'Ringing', 'Eileen', 'Sideways', 'Rita', 'Book', 'Paige', 'Turner', 'Rhoda', 'Report', 'Augusta', 'Wind', 'Chris', 'Anthemum', 'Anne', 'Teak', 'U.R.', 'Nice', 'Anita', 'Bath', 'Harriet', 'Upp', 'I.M.', 'Tired', 'I.', 'Missy Ewe', 'Ivana', 'B. Withew', 'Anita', 'Letterback', 'Hope', 'Furaletter', 'B.', 'Homesoon', 'Bea', 'Mine', 'Bess', 'Twishes', 'C.', 'Yasoon', 'Audie', 'Yose', 'Dee', 'End', 'Amanda', 'Hug', 'Ben', 'Dover'];
        $address = ['7 , Syndicate House, Old Rohtak Road Chowk, Inderlok', 'D219, Yari Road, Inlaks Park,versova, Andheri (west)', '36, Vengu Chetty Street', 'G-129, Anna Nagar East, Anna Nagar East', '1167 , Main Market', 'Shop No 1, Sanniwas Bldg., P.s.road, Vile Parle (east)', 'T 3E0, Talawade', '41 nd Fl, , Arun Chambers, S B Singh Road, Fort', '26, Rameshwar Nivas, Veer Savarkar Marg, Prabhadevi', '115 , A, Neelam Center, Hind Cycle Road, Worli', 'Sh.2, Zain Apartment, 1st Tps Road, Khar (west)', '236 , Sanjay Apa Chambers, Off J B Nagar,and-ghatkop, Opp Pravati Bldg,next To Vinita Bld, Andheri'];

        for($i =0; $i<$count; $i++) {
        	$random = rand(0,83);
			// $this->User->create();

			$email1 = strtolower($names[$random]);
            $email = $email1.'_'.time().strtolower($names[rand(0,83)]).'@mailinator.com';
			$user['email'] = $email;
			$user['password'] = 'admin123';
			$user['role_id'] = 2;
			$user['status'] = 1;
			$user['is_activated'] = 1;
			$this->User->id = null;
            $this->User->save($user);
			$user_id = $this->User->getLastInsertId();

			$this->UserDetail->create();
			$user_details['user_id'] = $user_id;
			$user_details['org_name'] = "Gaming Zones";
			$user_details['address'] = $address[rand(0,11)];
			$user_details['city'] = "Jaipur";
			$user_details['state_id'] = 1259;
			$user_details['country_id'] = "IN";
			$user_details['bio'] = "testing department";
			$user_details['department'] = "PHP";
			$user_details['job_title'] = "Sr. Software Developer";
			$user_details['job_role'] = "Developer";
			$user_details['city'] = "Jaipur";
			$user_details['zip'] = "302012";
			$user_details['contact'] = "8998998998";
			$user_details['first_name'] = $names[$random];
			$user_details['last_name'] = $names[rand(0,83)];
			$this->UserDetail->id = null;
			$this->UserDetail->save($user_details);


			/*$sql = "SELECT u.id,u.email,u.password,ud.first_name as firstname,ud.last_name as lastname, ud.profile_pic as thumb,ud.department, ud.address,ud.job_title, ud.job_role, ud.bio, ud.org_name, ud.contact, c.countryName as country_name, c.countryCode as country_code, timezones.name as timezone_name, timezones.timezone as timezone_offset FROM users as u LEFT JOIN user_details as ud ON u.id=ud.user_id LEFT JOIN countries as c ON c.countryCode=ud.country_id LEFT JOIN timezones ON u.id=timezones.user_id WHERE u.id =" . $user_id;

			$user_result = $this->User->query($sql);
			$this->Users->addUser($user_id, $user_result);*/
		}
		die;
	}

	public function mongo_update() {
        $this->loadModel('User');
        $this->loadModel('UserDetail');
       // $sql = "SELECT u.id FROM users as u INNER JOIN user_details as ud ON u.id=ud.user_id WHERE u.role_id = 2 AND u.status = 1 AND u.is_activated = 1";
        $sql = "SELECT u.id FROM users as u INNER JOIN user_details as ud ON u.id=ud.user_id WHERE u.role_id = 2";

			$user_result = $this->User->query($sql);


			if(isset($user_result) && !empty($user_result)){
				foreach ($user_result as $key => $value) {
					$data[] = $value;
					$user_id = $value['u']['id'];
					$this->Users->addUser($user_id, true);
				}
			}
			pr($user_result, 1);

		die;
	}

	public function gantt_data($p = null) {
		$all_elements = "7138,7139,7140,7141,7142,7143,7144,7145,7146,7147,7148,7149,7150,7151,7152,7153,7154,7155,7156,7157,7158,7159,7160,7161,7162,7163,7164,7165,7166,7167,7168,7169,7170,7171,7172,7173,7174,7175,12666,12717";
		$aadd = $this->objView->loadHelper('Permission')->task_detail($all_elements);
		// pr(explode(',', $aadd[0][0]['all_tasks']));
        	pr($aadd, 1);

        $data = $this->objView->loadHelper('Permission')->gantt_data();
        $allData = [];
        if(isset($data) && !empty($data)){
			foreach ($data as $key => $value) {
				$workspaces = $value['workspaces'];
				$allData[$workspaces['id']] = [
						'workspaces' => $workspaces
					];
				$area_task_data = $value[0]['area_task_data'];
				if(isset($area_task_data) && !empty($area_task_data)){
					$row = explode('~~', $area_task_data);
					if(isset($row) && !empty($row)){
						foreach ($row as $ki => $val) {
							$col = explode('^', $val);
							if(isset($col[0]) && isset($col[1]) && isset($col[2])){
								$allData[$workspaces['id']]['areas'][$col[0]][] = ['elid' => $col[1], 'eltitle' => $col[2]];
							}
						}
					}
				}

			}
		}
		pr($allData);
		pr($data);
		die;
	}

	public function add_types() {
		$this->layout = 'inner';
		$this->set('title_for_layout', __('Add Types', true));
		$this->set('page_heading', __('Add Types', true));
		$this->set('page_subheading', __('Add Types for Story, Organization and Location', true));

		if ($this->request->isAjax()) {
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				$tfor = (isset($post['tfor']) && !empty($post['tfor'])) ? $post['tfor'] : null;
				$type = (isset($post['type']) && !empty($post['type'])) ? $post['type'] : null;
				if(!empty($tfor) && !empty($type)){
					$model = '';
					if($tfor == 'story'){
						$model = 'story_types';
					}
					else if($tfor == 'org'){
						$model = 'organization_types';
					}
					else if($tfor == 'loc'){
						$model = 'location_types';
					}
					$fquery = "SELECT COUNT(*) as total FROM $model WHERE type = '$type'";
					$exists = $this->Element->query($fquery);
					// pr($exists, 1);
					if(!isset($exists[0][0]['total']) || empty($exists[0][0]['total'])) {
						$query = "INSERT INTO $model SET type = '$type'";
						$this->Element->query($query);
						die('test');
					}

					die('end');
				}
			}
		}
		// $this->loadModel('Dept');
		// $treeData = $this->Dept->find('all');
		// $treeData = Set::extract($treeData, '{n}.Dept');
		// $tree = $this->build_tree($treeData);
		// pr($tree, 1);
		$crumb = [
			'last' => [
				'data' => [
					'title' => 'Add Types',
					'data-original-title' => 'Add Types',
				],
			],
		];
		// $this->set($viewData);
		$this->set('crumb', $crumb);

	}
	public function dropdown() {

		$this->layout = 'inner';
	}

	function build_tree(array $elements, $parentId = 0, $counter = 0) {
	    $branch = array();
	    foreach ($elements as $element) {
	        if ($element['parent_id'] == $parentId) {
	            $children = $this->build_tree($elements, $element['id']);
	            if ($children) {
	                $element['children'] = $children;
                	$branch[] = ['parent' => ['id' => $element['parent_id'], 'title' => $element['title']], 'child' => $element['children']];
	            }
	            else{
	            	$branch[] = ['parent' => ['id' => $element['parent_id'], 'title' => $element['title']] ];
	            }
	            // $branch[] = $element;
	        }
	    }

	    return $branch;
	}
}
