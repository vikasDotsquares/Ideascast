<?php

App::uses('Controller/Component', 'Auth', 'Session', 'RequestHandler');
App::uses('Sanitize', 'Utility');
App::uses('CakeEmail', 'Network/Email');
App::import('Vendor', 'MPDF56/PhpWord');

class SearchesController extends AppController {

	public $name = 'Searches';
	public $uses = [
		'User',
		'UserDetail',
		'ProjectPermission',
		'UserSetting',
		'Category',
		'Aligned',
		'UserProject',
		'Project',
		'Workspace',
		'Area',
		'ProjectWorkspace',
		'Element',
		'ProjectGroup',
		'ProjectGroupUser',
		'SearchList',
		'SearchListUser',
	];
	public $objView = null;
	public $user_id = null;
	public $people_offset = 100;
	public $utill_offset = 25;
	public $components = array(
		'Common',
		'Search',
	);
	public $helpers = array(
		'Html',
		'Form',
		'Session',
		'Time',
		'Text',
		'Common',
		'ViewModel',
		'Search',
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
		$this->set('title_for_layout', __('Search', true));
		$this->set('page_heading', __('Search', true));
		$this->set('page_subheading', __('Search for content', true));

		/*
			         * Get my and received groups
		*/
		$all_groups = null;

		$my_group = get_my_groups($this->user_id);
		if (isset($my_group) && !empty($my_group)) {
			$all_groups = $my_group;
		}

		$search = $this->objView->loadHelper('Group');
		$received_group = $search->my_received_groups();

		if (isset($all_groups) && !empty($all_groups)) {
			if (isset($all_groups) && !empty($all_groups)) {
				$received_group = Set::combine($received_group, '{n}.ProjectGroup.id', '{n}.ProjectGroup.title');
				$all_groups = array_combines($all_groups, $received_group);
			}
		} else if (isset($received_group) && !empty($received_group)) {
			$all_groups = $received_group;
		}

		$viewVars['all_groups'] = $all_groups;

		/*
			         * Get logged in user's saved search list
		*/
		$viewVars['my_search_list'] = $this->SearchList->find('list', [
			'conditions' => [
				'SearchList.user_id' => $this->user_id,
			],
			'recursive' => -1,
		]);

		$crumb = [
			'last' => [
				'data' => [
					'title' => 'Search',
					'data-original-title' => 'Search',
				],
			],
		];

		// pr($crumb, 1);
		$this->set($viewVars);
		$this->set('crumb', $crumb);
	}

	public function ajax_find_search_details() {
		if ($this->request->isAjax()) {

			/* $connect = mysql_pconnect(SEARCH_SERVERNAME, SEARCH_USERNAME, SEARCH_PASSWORD) or die("Could not connect");
			$db = mysql_select_db(SEARCH_DB) or die("Could not find db"); */

			$conn = mysqli_connect(SEARCH_SERVERNAME, SEARCH_USERNAME, SEARCH_PASSWORD,SEARCH_DB);
			if (mysqli_connect_errno())
			{
				echo "Failed to connect to MySQL: " . mysqli_connect_error();
			}

			$this->layout = 'ajax';

			$response = [
				'success' => true,
				'msg' => null,
				'content' => null,
			];
			$table = $this->request->data['tablename'];
			$table_id = $this->request->data['tableid'];
			$table_field = $this->request->data['tablefield'];
			$table_field_text = $this->request->data['tblfieldvalue'];

			// pr($this->request->data);

			$sql = "SELECT $table_id, $table_field FROM $table WHERE id = $table_id";
			$result = mysqli_query($conn,$sql);

			$numm = @mysqli_num_rows($result);
			if (isset($numm) && $numm > 0) {
				// output data of each row
				while ($row = mysqli_fetch_assoc($result)) {
					$table_field_text = $row[$table_field];
				}
			}
			// pr($table_field_text);
			$alldata = [
				'table' => $table,
				'table_id' => $table,
				'table_field' => $table,
				'table_field_text' => $table,
			];
			$allReqdata = $this->request->data;
			$this->set(compact("allReqdata", "alldata", "table", "table_id", "table_field", "table_field_text"));
		}
	}

	public function ajax_keyword_search() {
		$result = $keyword = null;
		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$response = [
				'success' => true,
				'msg' => null,
				'content' => null,
			];

			$viewModel = $this->objView->loadHelper('ViewModel');

			if ($this->request->is('post') || $this->request->is('put')) {

				$keyword = $this->request->data['keyword'];
				$caseSen = $this->request->data['caseSenstive'];
				if (isset($keyword) && !empty($keyword)) {

					if( $_SERVER['HTTP_HOST'] == LOCALIP ) {
						$searchFromDatabase = array(
							"ideascast",
						);
					} else {
						$searchFromDatabase = array(
							SEARCH_DB,
						);
					}
					$this->Search->init($searchFromDatabase); // Enter no parameter to search from the entire database

					if ($caseSen == 1) {
						$matchWord = false;
						$caseSenstive = true;
					} else if ($caseSen == 2) {
						$matchWord = true;
						$caseSenstive = true;
					} else {
						$matchWord = false;
						$caseSenstive = false;
					}

					// $matchWord = $caseSen; //optional
					// optional
					// pr($keyword);
					$result = $this->Search->getSearchResults($keyword, $matchWord, $caseSenstive);
					$count = ( isset($result) && !empty($result) ) ? count($result) : 0;
					if ($count == 0) {
						// $response['content'] = "Sorry <b>$keyword</b> was not found in any of the table";
						$response['content'] = "<li>No search results.</li>";
					}
				}
			}
			$this->set('keyword', $keyword);
			$this->set('result', $result);
		}
	}

	public function get_pagination() {
		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				$paging_links = (!empty($post)) ? jsPaginations($post) : "";
				if (!empty($paging_links)) {
					$response['success'] = TRUE; // Some might be empty
					// $response['js_pconfig'] = $paging_links["js_pconfig"];
					$response['output'] = $paging_links["output"];
				} else {
					$response['success'] = FALSE; // Some might be empty
				}
			}

			echo json_encode($response);
			exit;
		}
	}

	public function people() {
		$this->layout = 'inner';
		$this->set('title_for_layout', __('People', true));
		$this->set('page_heading', __('People', true));
		$this->set('page_subheading', __('Discover and learn about people in your community', true));

		$viewVars = $query_params = [];

		$selected_user = $selected_users = $selected_skill = $selected_sub = $selected_domain = $selected_org = $selected_loc = $selected_dept = $selected_story = null;
		$viewVars['start_date'] = $viewVars['end_date'] = $viewVars['selected_tab'] = '';
		if(isset($this->params['named']) && !empty($this->params['named'])) {
			if(isset($this->params['named']['user']) && !empty($this->params['named']['user'])) {
				$query_params['user'] = [$this->params['named']['user']];
				$selected_user = $this->params['named']['user'];
			}
			if(isset($this->params['named']['project']) && !empty($this->params['named']['project'])) {

				$selected_project = $this->params['named']['project'];
				$presult = $this->objView->loadHelper('Scratch')->project_data($selected_project);

				$viewVars['start_date'] = date('d M Y', strtotime($presult['projects']['start_date']));
				$viewVars['end_date'] = date('d M Y', strtotime($presult['projects']['end_date']));
				if($presult[0]['prj_status'] == 'overdue'){
					$viewVars['end_date'] = date('d M Y');
				}
			}
			if(isset($this->params['named']['workspace']) && !empty($this->params['named']['workspace'])) {

				$selected_workspace = $this->params['named']['workspace'];
				$presult = $this->objView->loadHelper('Scratch')->wsp_info($selected_workspace);
				$presult = $presult[0];
				// pr($presult, 1);

				$viewVars['start_date'] = date('d M Y', strtotime($presult['workspaces']['start_date']));
				$viewVars['end_date'] = date('d M Y', strtotime($presult['workspaces']['end_date']));
				if($presult[0]['wsp_status'] == 'overdue'){
					$viewVars['end_date'] = date('d M Y');
				}
			}
			if(isset($this->params['named']['task']) && !empty($this->params['named']['task'])) {

				$selected_workspace = $this->params['named']['task'];
				$presult = $this->objView->loadHelper('Scratch')->task_data($selected_workspace);
				// $presult = $presult[0];
				//  pr($presult, 1);
				if( (isset($presult['elements']['start_date']) && !empty($presult['elements']['start_date'])) && (isset($presult['elements']['end_date']) && !empty($presult['elements']['end_date'])) ) {
					$viewVars['start_date'] = date('d M Y', strtotime($presult['elements']['start_date']));
					$viewVars['end_date'] = date('d M Y', strtotime($presult['elements']['end_date']));
					if($presult[0]['task_status'] == 'OVD'){
						$viewVars['end_date'] = date('d M Y');
					}
				}
			}
			if(isset($this->params['named']['skill']) && !empty($this->params['named']['skill'])) {
				$selected_skill = $this->params['named']['skill'];
				$query_params['skill'][] = $this->params['named']['skill'];
			}
			if(isset($this->params['named']['subject']) && !empty($this->params['named']['subject'])) {
				$selected_sub = $this->params['named']['subject'];
				$query_params['sub'][] = $this->params['named']['subject'];
			}
			if(isset($this->params['named']['domain']) && !empty($this->params['named']['domain'])) {
				$selected_domain = $this->params['named']['domain'];
				$query_params['domain'][] = $this->params['named']['domain'];
			}
			if(isset($this->params['named']['users']) && !empty($this->params['named']['users'])) {
				$usersList = explode(',', $this->params['named']['users']);
				$selected_users = $usersList;
				$query_params['user'] = $usersList;
			}
			if(isset($this->params['named']['org']) && !empty($this->params['named']['org'])) {
				$selected_org = $this->params['named']['org'];
				$query_params['org'][] = $selected_org;
			}
			if(isset($this->params['named']['loc']) && !empty($this->params['named']['loc'])) {
				$selected_loc = $this->params['named']['loc'];
				$query_params['loc'][] = $selected_loc;
			}
			if(isset($this->params['named']['dept']) && !empty($this->params['named']['dept'])) {
				$selected_dept = $this->params['named']['dept'];
				$query_params['dept'][] = $selected_dept;
			}
			if(isset($this->params['named']['story']) && !empty($this->params['named']['story'])) {
				$selected_story = $this->params['named']['story'];
				$query_params['story'][] = $selected_story;
			}

			if(isset($this->params['named']['tab']) && !empty($this->params['named']['tab'])) {
				$viewVars['selected_tab'] = $this->params['named']['tab'];
			}

			if(isset($this->params['named']['params']) && !empty($this->params['named']['params'])) {
				$this->loadModel('LinkParam');
				$currentUser = $this->user_id;
				$paramID = $this->params['named']['params'];
				$paramData = $this->LinkParam->query("SELECT * FROM link_params WHERE id = $paramID");
				if(isset($paramData) && !empty($paramData)){
					$paramData = $paramData[0]['link_params'];
					if($currentUser == $paramData['user_id']){
						$param_type = $paramData['type'];
						$param_user = $paramData['params'];
						$query_params['user'] = [$param_user];
						$selected_user = $param_user;
						$selected_users = explode(',', $param_user);
						$this->LinkParam->query("DELETE FROM link_params WHERE user_id = $currentUser");
					}
				}
			}
		}
		// pr($selected_user, 1);

		$query_params['coloumn'] = 'first_name';
		$query_params['order'] = 'ASC';
		$all_people = $this->objView->loadHelper('Scratch')->people_list($query_params, $this->people_offset);
		$this->set('all_people', $all_people);
		$this->set('people_offset', $this->people_offset);
		$this->setJsVars(['people_offset' => $this->people_offset, 'selected_user' => $selected_user, 'selected_skill' => $selected_skill, 'selected_sub' => $selected_sub, 'selected_domain' => $selected_domain, 'selected_org' => $selected_org, 'selected_loc' => $selected_loc, 'selected_dept' => $selected_dept, 'selected_story' => $selected_story, 'selected_users' => $selected_users, 'start_date' => $viewVars['start_date'], 'end_date' => $viewVars['end_date'], 'selected_tab' => $viewVars['selected_tab']]);
		$this->set('selected_tab', $viewVars['selected_tab']);

		$crumb = [
			'last' => [
				'data' => [
					'title' => 'People',
					'data-original-title' => 'People',
				],
			],
		];
		$this->set($viewVars);
		$this->set('crumb', $crumb);
		$this->setJsVar('people', true);
	}

	public function find_people() {
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';

			$data = [];
			$count = 0;
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;

				$query_params['page'] = (isset($post['page']) && !empty($post['page'])) ? $post['page'] : 0;
				$query_params['order'] = (isset($post['order']) && !empty($post['order'])) ? $post['order'] : 'ASC';
				$query_params['coloumn'] = (isset($post['coloumn']) && !empty($post['coloumn'])) ? $post['coloumn'] : 'full_name';
				$query_params['search_text'] = (isset($post['search_text']) && !empty($post['search_text'])) ? $post['search_text'] : '';

				$query_params['user'] = (isset($post['user']) && !empty($post['user'])) ? $post['user'] : [];
				$query_params['org'] = (isset($post['org']) && !empty($post['org'])) ? $post['org'] : [];
				$query_params['loc'] = (isset($post['loc']) && !empty($post['loc'])) ? $post['loc'] : [];
				$query_params['dept'] = (isset($post['dept']) && !empty($post['dept'])) ? $post['dept'] : [];
				$query_params['tag'] = (isset($post['tag']) && !empty($post['tag'])) ? $post['tag'] : [];
				$query_params['skill'] = (isset($post['skill']) && !empty($post['skill'])) ? $post['skill'] : [];
				$query_params['sub'] = (isset($post['sub']) && !empty($post['sub'])) ? $post['sub'] : [];
				$query_params['domain'] = (isset($post['domain']) && !empty($post['domain'])) ? $post['domain'] : [];
				$query_params['story'] = (isset($post['story']) && !empty($post['story'])) ? $post['story'] : [];

				$all_people = $this->objView->loadHelper('Scratch')->people_list($query_params, $this->people_offset);
				$this->set('all_people', $all_people);

			}
			$this->render('/Searches/people/people_list');
		}
	}

	public function paging_count() {
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';

			$data = [];
			$count = 0;
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;

				$query_params['page'] = (isset($post['page']) && !empty($post['page'])) ? $post['page'] : 0;
				$query_params['order'] = (isset($post['order']) && !empty($post['order'])) ? $post['order'] : 'ASC';
				$query_params['coloumn'] = (isset($post['coloumn']) && !empty($post['coloumn'])) ? $post['coloumn'] : 'rdate';
				$query_params['search_text'] = (isset($post['search_text']) && !empty($post['search_text'])) ? $post['search_text'] : '';

				$query_params['user'] = (isset($post['user']) && !empty($post['user'])) ? $post['user'] : [];
				$query_params['org'] = (isset($post['org']) && !empty($post['org'])) ? $post['org'] : [];
				$query_params['loc'] = (isset($post['loc']) && !empty($post['loc'])) ? $post['loc'] : [];
				$query_params['dept'] = (isset($post['dept']) && !empty($post['dept'])) ? $post['dept'] : [];
				$query_params['tag'] = (isset($post['tag']) && !empty($post['tag'])) ? $post['tag'] : [];
				$query_params['skill'] = (isset($post['skill']) && !empty($post['skill'])) ? $post['skill'] : [];
				$query_params['sub'] = (isset($post['sub']) && !empty($post['sub'])) ? $post['sub'] : [];
				$query_params['domain'] = (isset($post['domain']) && !empty($post['domain'])) ? $post['domain'] : [];
				$query_params['story'] = (isset($post['story']) && !empty($post['story'])) ? $post['story'] : [];

				$getUserList = (isset($post['list']) && !empty($post['list'])) ? $post['list'] : false;

				$data = $this->objView->loadHelper('Scratch')->people_list($query_params);
				$userList = '';
				if(isset($data) && !empty($data) && $getUserList){
					$usersList = Set::extract($data, '{n}.udata.user_id');
					if(isset($usersList) && !empty($usersList)){
						$userList = implode(',', $usersList);
					}
					echo json_encode(['success' => true, 'content' => $userList]);
					exit;
				}
				$count = count($data);

			}
			echo json_encode($count);
			exit;
		}
	}

	public function filter_people(){

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$data = $this->objView->loadHelper('Scratch')->filter_list();
			// pr($data);
			$this->set('data', $data);

			$this->render('/Searches/people/filter_people');
		}
	}

	public function work_block() {
		$response = ['success' => false];
		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$this->loadModel('UserBlocks');
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				$response = ['success' => false];
				if( (isset($post['start_date']) && !empty($post['start_date'])) && (isset($post['end_date']) && !empty($post['end_date'])) ){
					$data = [
						'user_id' => $this->user_id,
						'work_start_date' => date('Y-m-d', strtotime($post['start_date'])),
						'work_end_date' => date('Y-m-d', strtotime($post['end_date'])),
						'comments' => $post['comments'],
					];
					if($this->UserBlocks->save($data)){
						$response['success'] = true;
					}
					echo json_encode($response);
					exit();
				}
			}
			$this->render('/Searches/people/work_block');
		}
	}

	public function work_block_list($user_id = null) {
		$response = ['success' => false];
		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$this->loadModel('UserBlocks');

			$data = $this->UserBlocks->find('all', ['conditions' => ['user_id' => $this->user_id], 'order' => ['modified DESC']]);
			// pr($data, 1);
			$html = '';
			$this->set("user_id", $user_id);
			$this->set("data", $data);
			$this->render('/Searches/people/work_block_list');
		}
	}

	public function delete_work_block() {
		$response = ['success' => false];
		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				// pr($post, 1);

				if ( isset($post['id']) && !empty($post['id']) ) {

					$id = $post['id'];

					$this->loadModel('UserBlocks');
					$dsql = "DELETE FROM user_blocks WHERE id = '$id'";
					$this->UserBlocks->query($dsql);
					$response['success'] = true;
				}

			}
			echo json_encode($response);
			exit;
		}
	}

	public function people_list() {
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';

			$details = [];
			$response = ['success' => false, 'content' => []];
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;

				$query_params['page'] = (isset($post['page']) && !empty($post['page'])) ? $post['page'] : 0;
				$query_params['order'] = (isset($post['order']) && !empty($post['order'])) ? $post['order'] : 'ASC';
				$query_params['coloumn'] = (isset($post['coloumn']) && !empty($post['coloumn'])) ? $post['coloumn'] : 'full_name';
				$query_params['search_text'] = (isset($post['search_text']) && !empty($post['search_text'])) ? $post['search_text'] : '';

				$query_params['user'] = (isset($post['user']) && !empty($post['user'])) ? $post['user'] : [];
				$query_params['org'] = (isset($post['org']) && !empty($post['org'])) ? $post['org'] : [];
				$query_params['loc'] = (isset($post['loc']) && !empty($post['loc'])) ? $post['loc'] : [];
				$query_params['dept'] = (isset($post['dept']) && !empty($post['dept'])) ? $post['dept'] : [];
				$query_params['tag'] = (isset($post['tag']) && !empty($post['tag'])) ? $post['tag'] : [];
				$query_params['skill'] = (isset($post['skill']) && !empty($post['skill'])) ? $post['skill'] : [];
				$query_params['sub'] = (isset($post['sub']) && !empty($post['sub'])) ? $post['sub'] : [];
				$query_params['domain'] = (isset($post['domain']) && !empty($post['domain'])) ? $post['domain'] : [];

				$all_people = $this->objView->loadHelper('Scratch')->people_list($query_params);

				if(isset($all_people) && !empty($all_people)){
					foreach ($all_people as $key => $value) {
						$uid = $value['udata']['user_id'];
						$name = $value['udata']['first_name'] . ' ' .$value['udata']['last_name'];
						$details[] = ['value' => $uid, 'label' => $name];
					}
				}
			}
			$response['content'] = $details;
			echo json_encode($response);
            exit;
		}
	}

	public function people_engagement() {
		if ($this->request->isAjax()) {

			$this->layout = false;
			$viewData = null;
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				$viewData['post'] = $post;
			}

			$this->set($viewData);
			$this->render('/Searches/people/people_engagement');

		}
	}

	public function people_engagement_json() {

		$this->layout = false;
		$view = new View($this, false);
		$view->viewPath = 'Searches/people';
		$data = null;
		if ($this->request->is('post') || $this->request->is('put')) {
			$post = $this->request->data;
			// pr($post, 1);
			$query_params['start_date'] = (isset($post['start_date']) && !empty($post['start_date'])) ? $post['start_date'] : '';
			$query_params['end_date'] = (isset($post['end_date']) && !empty($post['end_date'])) ? $post['end_date'] : '';

			$query_params['search_text'] = (isset($post['search_text']) && !empty($post['search_text'])) ? $post['search_text'] : '';

			$query_params['user'] = (isset($post['user']) && !empty($post['user'])) ? $post['user'] : [];
			$query_params['org'] = (isset($post['org']) && !empty($post['org'])) ? $post['org'] : [];
			$query_params['loc'] = (isset($post['loc']) && !empty($post['loc'])) ? $post['loc'] : [];
			$query_params['dept'] = (isset($post['dept']) && !empty($post['dept'])) ? $post['dept'] : [];
			$query_params['tag'] = (isset($post['tag']) && !empty($post['tag'])) ? $post['tag'] : [];
			$query_params['skill'] = (isset($post['skill']) && !empty($post['skill'])) ? $post['skill'] : [];
			$query_params['sub'] = (isset($post['sub']) && !empty($post['sub'])) ? $post['sub'] : [];
			$query_params['domain'] = (isset($post['domain']) && !empty($post['domain'])) ? $post['domain'] : [];
			$query_params['story'] = (isset($post['story']) && !empty($post['story'])) ? $post['story'] : [];

			$people_engagement = $this->objView->loadHelper('Scratch')->people_engagement($query_params);
			$data = $people_engagement[0][0]['groups'];
			$view->set('data', $data);
		}

		$html = $view->render('people_engagement_json');
		echo json_encode($html);
		exit();
	}

	/* UTILIZATION */
	public function planning(){
		$this->layout = 'inner';
		$this->set('title_for_layout', __('Planning', true));
		$this->set('page_heading', __('Planning', true));
		$this->set('page_subheading', __('Analyze resource capacity, allocation and plan changes', true));
		$viewVars = [];

		$viewVars['crumb'] = [
			'last' => [
				'data' => [
					'title' => 'Planning',
					'data-original-title' => 'Planning',
				],
			],
		];

		$param_type = $param_user = '';
		$people_list = $project_list = [];
		if(isset($this->params) && !empty($this->params)){
			$params = $this->params;
			if( ((isset($params['named']['type']) && !empty($params['named']['type'])) && isset($params['named']['user']) && !empty($params['named']['user'])) ||  (isset($params['named']['params']) && !empty($params['named']['params'])) ){
				$this->loadModel('User');
				$this->loadModel('LinkParam');
				$whrUsers = "";
				$currentUser = $this->user_id;
				if(isset($params['named']['params']) && !empty($params['named']['params'])){
					$paramID = $params['named']['params'];
					$paramData = $this->LinkParam->query("SELECT * FROM link_params WHERE id = $paramID");
					if(isset($paramData) && !empty($paramData)){
						$paramData = $paramData[0]['link_params'];
						if($currentUser == $paramData['user_id']){
							$param_type = $paramData['type'];
							$param_user = $paramData['params'];
							if($param_type == 'people'){
								$whrUsers = "AND u.id IN($param_user)";
								// GET ALL USERS
								$data = $this->User->query("SELECT u.id, CONCAT_WS(' ', ud.first_name, ud.last_name) as full_name FROM users u INNER JOIN user_details ud ON ud.user_id = u.id WHERE u.role_id = 2 AND u.status = 1 AND u.is_activated = 1 $whrUsers ORDER BY full_name ASC");

								if(isset($data) && !empty($data)){
									foreach ($data as $key => $value) {
										$people_list[$value['u']['id']] = $value[0]['full_name'];
									}
								}
							}
							else if($param_type == 'tags'){
								$termsArr = array_map('trim', explode('$$$', $param_user));
								$termsArr = escapes($termsArr);
								// $tags = explode('$$$', $param_user);
								$tags = implode("','", $termsArr);
								// $tags = Sanitize::escape($tags);
								$data = $this->User->query("SELECT DISTINCT(tag) FROM `tags` WHERE `tag` IN ('$tags') ORDER BY tag ASC");
								// pr("SELECT DISTINCT(tag) FROM `tags` WHERE `tag` IN ('$tags') ORDER BY tag ASC", 1);
								if(isset($data) && !empty($data)){
									foreach ($data as $key => $value) {
										$people_list[$value['tags']['tag']] = $value['tags']['tag'];
									}
								}
							}

							$this->LinkParam->query("DELETE FROM link_params WHERE user_id = $currentUser");
						}
					}
				}
				else if(isset($params['named']['user']) && !empty($params['named']['user'])) {
					$param_type = $params['named']['type'];
					$param_user = $params['named']['user'];
					$whrUsers = "AND u.id = $param_user";
					// GET ALL USERS
					$data = $this->User->query("SELECT u.id, CONCAT_WS(' ', ud.first_name, ud.last_name) as full_name FROM users u INNER JOIN user_details ud ON ud.user_id = u.id WHERE u.role_id = 2 AND u.status = 1 AND u.is_activated = 1 $whrUsers ORDER BY full_name ASC");

					if(isset($data) && !empty($data)){
						foreach ($data as $key => $value) {
							$people_list[$value['u']['id']] = $value[0]['full_name'];
						}
					}
				}


				// GET ALL PROJECTS
				$dates = date('Y-m-d');
				$user_id = $this->Session->read('Auth.User.id');
				$data = $this->User->query("SELECT
								    p.id AS id,
								    p.title AS title
								FROM
								    user_permissions up
								LEFT JOIN projects p ON
								    up.project_id = p.id
								WHERE
								    up.user_id = $user_id AND
								    up.workspace_id IS NULL AND
								    up.role IN ('Creator', 'Owner', 'Group Owner', 'Sharer', 'Group Sharer')
								    AND '$dates' BETWEEN p.start_date AND p.end_date
							    ");
				if(isset($data) && !empty($data)){
					$data = Set::combine($data, '{n}.p.id', '{n}.p.title');
					foreach ($data as $key => $value) {
						$project_list[$key] = htmlentities($value, ENT_QUOTES, "UTF-8");
					}
				}
			}
		}
		$viewVars['people_list'] = $people_list;
		$viewVars['project_list'] = $project_list;
		$viewVars['param_type'] = $param_type;
		$viewVars['param_user'] = $param_user;
		$resourcer = ($this->Session->read('Auth.User.UserDetail.resourcer') > 0) ? true : false;

		$this->set($viewVars);
		$this->setJsVar('utill_offset', $this->utill_offset);
		$this->setJsVar('planning', true);
		$this->setJsVars(['param_type' => $param_type, 'param_user' => $param_user, 'resourcer' => $resourcer]);
	}

	public function get_option_data(){
		if ($this->request->isAjax()) {
            $this->layout = 'ajax';
            $response = ['success' => false, 'content' => [], 'selection' => []];
            $details = [];
            $user_id = $this->Session->read('Auth.User.id');
            $this->loadModel('UserPermission');

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;

				if( isset($post['type']) && !empty($post['type']) ){
					$type = $post['type'];
					if($type == 'organizations'){
						$this->loadModel('Organization');
						$data = $this->Organization->query("SELECT id, name FROM organizations ORDER BY name ASC");
						if(isset($data) && !empty($data)){
							foreach ($data as $key => $value) {
								$details[] = ['value' => $value['organizations']['id'], 'label' => htmlentities($value['organizations']['name'], ENT_QUOTES, "UTF-8")];
							}
						}
						$response['success'] = true;
					}
					else if($type == 'locations'){
						$this->loadModel('Location');
						$data = $this->Location->query("SELECT id, name FROM locations ORDER BY name ASC");
						if(isset($data) && !empty($data)){
							foreach ($data as $key => $value) {
								$details[] = ['value' => $value['locations']['id'], 'label' => htmlentities($value['locations']['name'], ENT_QUOTES, "UTF-8") ];
							}
						}
						$response['success'] = true;
					}
					else if($type == 'departments'){
						$this->loadModel('Department');
						$data = $this->Department->query("SELECT id, name FROM departments ORDER BY name ASC");
						if(isset($data) && !empty($data)){
							foreach ($data as $key => $value) {
								$details[] = ['value' => $value['departments']['id'], 'label' => htmlentities($value['departments']['name'], ENT_QUOTES, "UTF-8") ];
							}
						}
						$response['success'] = true;
					}
					else if($type == 'users'){
						$this->loadModel('User');
						$qry_str = "";
						$data = $this->User->query("SELECT u.id, CONCAT_WS(' ', ud.first_name, ud.last_name) as full_name FROM users u INNER JOIN user_details ud ON ud.user_id = u.id WHERE u.role_id = 2 AND u.status = 1 AND u.is_activated = 1 ORDER BY full_name ASC");
						if(isset($data) && !empty($data)){
							foreach ($data as $key => $value) {
								$details[] = ['value' => $value['u']['id'], 'label' => $value[0]['full_name']];
							}
						}
						$response['success'] = true;
					}
					else if($type == 'tags'){
						$this->loadModel('User');
						$data = $this->User->query("SELECT
									                DISTINCT(tg.tag) as tag
									            FROM tags tg
									            INNER JOIN users u ON u.id = tg.tagged_user_id
									            WHERE tg.user_id = $user_id
								            ");
						if(isset($data) && !empty($data)){
							// pr($data, 1);
							foreach ($data as $key => $value) {
								$details[] = ['value' => $value['tg']['tag'], 'label' => $value['tg']['tag']];
							}
						}
						$response['success'] = true;
					}
					else if($type == 'project'){
						$qr = "";
						if(isset($post['dates']) && !empty($post['dates'])){
							$dates = date('Y-m-d', strtotime($post['dates']));
							$qr = "AND '$dates' BETWEEN p.start_date AND p.end_date";
						}

						$data = $this->UserPermission->query("SELECT
										    p.id AS id,
										    p.title AS title
										FROM
										    user_permissions up
										LEFT JOIN projects p ON
										    up.project_id = p.id
										WHERE
										    up.user_id = $user_id AND
										    up.workspace_id IS NULL AND
										    up.role IN ('Creator', 'Owner', 'Group Owner', 'Sharer', 'Group Sharer')
										    $qr
									    ");
						if(isset($data) && !empty($data)){
							$data = Set::combine($data, '{n}.p.id', '{n}.p.title');
							foreach ($data as $key => $value) {
								$details[] = ['value' => $key, 'label' => htmlentities($value, ENT_QUOTES, "UTF-8")];
							}
						}
						$response['success'] = true;
					}
					else if($type == 'created_projects') {
						$data = $this->UserPermission->query("SELECT
										    p.id AS id,
										    p.title AS title
										FROM
										    user_permissions up
										LEFT JOIN projects p ON
										    up.project_id = p.id
										WHERE
										    up.user_id = $user_id AND
										    up.workspace_id IS NULL AND
										    up.role = 'Creator'");
						if(isset($data) && !empty($data)){
							$data = Set::combine($data, '{n}.p.id', '{n}.p.title');
							foreach ($data as $key => $value) {
								$details[] = ['value' => $key, 'label' => htmlentities($value, ENT_QUOTES, "UTF-8")];
							}
						}
						$response['success'] = true;
					}
					else if($type == 'owner_projects') {
						$data = $this->UserPermission->query("SELECT
										    p.id AS id,
										    p.title AS title
										FROM
										    user_permissions up
										LEFT JOIN projects p ON
										    up.project_id = p.id
										WHERE
										    up.user_id = $user_id AND
										    up.workspace_id IS NULL AND
										    up.role IN ('Creator', 'Owner', 'Group Owner')");
						if(isset($data) && !empty($data)){
							$data = Set::combine($data, '{n}.p.id', '{n}.p.title');
							foreach ($data as $key => $value) {
								$details[] = ['value' => $key, 'label' => htmlentities($value, ENT_QUOTES, "UTF-8")];
							}
						}
						$response['success'] = true;
					}
					else if($type == 'shared_projects') {
						$data = $this->UserPermission->query("SELECT
										    p.id AS id,
										    p.title AS title
										FROM
										    user_permissions up
										LEFT JOIN projects p ON
										    up.project_id = p.id
										WHERE
										    up.user_id = $user_id AND
										    up.workspace_id IS NULL AND
										    up.role IN ('Sharer', 'Group Sharer')");
						if(isset($data) && !empty($data)){
							$data = Set::combine($data, '{n}.p.id', '{n}.p.title');
							foreach ($data as $key => $value) {
								$details[] = ['value' => $key, 'label' => htmlentities($value, ENT_QUOTES, "UTF-8")];
							}
						}
						$response['success'] = true;
					}
					else if($type == 'skills'){
						$this->loadModel('Skill');
						$data = $this->Skill->find('list', ['fields' => ['id', 'title'], 'order' => ['title ASC']]);
						// pr($data, 1);
						if(isset($data) && !empty($data)){
							foreach ($data as $key => $value) {
								$details[] = ['value' => $key, 'label' => htmlentities($value, ENT_QUOTES, "UTF-8")];
							}
						}
						$response['success'] = true;
					}
					else if($type == 'subjects'){
						$this->loadModel('Subject');
						$data = $this->Subject->find('list', ['fields' => ['id', 'title'], 'order' => ['title ASC']]);
						if(isset($data) && !empty($data)){
							foreach ($data as $key => $value) {
								$details[] = ['value' => $key, 'label' => htmlentities($value, ENT_QUOTES, "UTF-8")];
							}
						}
						$response['success'] = true;
					}
					else if($type == 'domains'){
						$this->loadModel('KnowledgeDomain');
						$data = $this->KnowledgeDomain->find('list', ['fields' => ['id', 'title'], 'order' => ['title ASC']]);
						if(isset($data) && !empty($data)){
							foreach ($data as $key => $value) {
								$details[] = ['value' => $key, 'label' => htmlentities($value, ENT_QUOTES, "UTF-8")];
							}
						}
						$response['success'] = true;
					}
				}
			}

			$response['content'] = $details;
			echo json_encode($response);
            exit;
		}
	}

	public function get_utilization() {
		if ($this->request->isAjax()) {
            $this->layout = 'ajax';
            $this->loadModel('UserPermission');
            $response = ['success' => false];
            $data = [];
            $user_id = $this->Session->read('Auth.User.id');
            $resourcer = $this->Session->read('Auth.User.UserDetail.resourcer');
            if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				$people_from = $post["people_from"];
	            $item_1 = $post["item_1"];
	            $work_from = $post["work_from"];
	            $item_2 = $post["item_2"];
	            $date_type = $post["date_type"];
	            $dates = $post["dates"];

	            $page = (isset($post["page"]) && !empty($post["page"])) ? $post["page"] : 0;

				$limit_str = '';
				if(isset($post["limit"]) && !empty($post["limit"])){
					$limit = $post["limit"];
					$limit_str = "LIMIT $page, $limit";
				}
				else if((!isset($post["count"]) || empty($post["count"])) && (!isset($post["user_counter"]) || empty($post["user_counter"]))){
					$limit_str = "LIMIT 0, ".$this->utill_offset;
				}

	            $start_date = date('Y-m-d', strtotime($dates));
	            $adjustments = $post["adjustments"];

				$sql_1 = $sql_2 = $sql_3 = $sql_4 = $sql_5 = $sql_6 = $sql_7 = $sql_8 = $sql_9 = $sql_10 = $sql_11 = $sql_12 = $sql_13 = $sql_14 = $sql_15 = $sql_16 = $sql_17 = $sql_18 = $sql_19 = $sql_20 = $sql_21 = $sql_22 = "";

				if(isset($people_from) && !empty($people_from)){
					if($people_from == "community"){
						$sql_1 = "#Select People From: All Community
								SELECT u.id AS user_id, CONCAT(ud.first_name, ' ', ud.last_name) AS full_name, ud.job_title, ud.profile_pic, ud.organization_id FROM users u
								INNER JOIN user_details ud ON u.id = ud.user_id
								WHERE u.role_id = 2 AND u.status = 1 AND u.is_deleted = 0
								ORDER BY ud.first_name, ud.last_name";
						$sql_3 = "#Select People From: All Community
								SELECT u.id AS user_id FROM users u
								INNER JOIN user_details ud ON u.id = ud.user_id
								WHERE u.role_id = 2 AND u.status = 1 AND u.is_deleted = 0
								ORDER BY ud.first_name, ud.last_name";
					}
					else if($people_from == "profile"){
						$sql_1 = "#Select People From: My Profile
								SELECT u.id AS user_id, CONCAT(ud.first_name, ' ', ud.last_name) AS full_name, ud.job_title, ud.profile_pic, ud.organization_id FROM users u
								INNER JOIN user_details ud ON u.id = ud.user_id
								WHERE u.role_id = 2 AND u.status = 1 AND u.is_deleted = 0 AND u.id = $user_id
								ORDER BY ud.first_name, ud.last_name";
						$sql_3 = "#Select People From: My Profile
								SELECT u.id AS user_id FROM users u
								INNER JOIN user_details ud ON u.id = ud.user_id
								WHERE u.role_id = 2 AND u.status = 1 AND u.is_deleted = 0 AND u.id = $user_id
								ORDER BY ud.first_name, ud.last_name";
					}
					else if($people_from == "organizations"){
						$item_1 = implode(",", $item_1);
						$sql_1 = "#Select People From: Specific Organizations
								SELECT u.id AS user_id, CONCAT(ud.first_name, ' ', ud.last_name) AS full_name, ud.job_title, ud.profile_pic, ud.organization_id FROM users u
								INNER JOIN user_details ud ON u.id = ud.user_id
								WHERE u.role_id = 2 AND u.status = 1 AND u.is_deleted = 0 AND ud.organization_id IN ($item_1)
								ORDER BY ud.first_name, ud.last_name";
						$sql_3 = "#Select People From: Specific Organizations
								SELECT u.id AS user_id FROM users u
								INNER JOIN user_details ud ON u.id = ud.user_id
								WHERE u.role_id = 2 AND u.status = 1 AND u.is_deleted = 0 AND ud.organization_id IN ($item_1)
								ORDER BY ud.first_name, ud.last_name";
					}
					else if($people_from == "locations"){
						$item_1 = implode(",", $item_1);
						$sql_1 = "#Select People From: Specific Locations
								SELECT u.id AS user_id, CONCAT(ud.first_name, ' ', ud.last_name) AS full_name, ud.job_title, ud.profile_pic, ud.organization_id FROM users u
								INNER JOIN user_details ud ON u.id = ud.user_id
								WHERE u.role_id = 2 AND u.status = 1 AND u.is_deleted = 0 AND ud.location_id IN ($item_1)
								ORDER BY ud.first_name, ud.last_name";
						$sql_3 = "#Select People From: Specific Locations
								SELECT u.id AS user_id FROM users u
								INNER JOIN user_details ud ON u.id = ud.user_id
								WHERE u.role_id = 2 AND u.status = 1 AND u.is_deleted = 0 AND ud.location_id IN ($item_1)
								ORDER BY ud.first_name, ud.last_name";
					}
					else if($people_from == "departments"){
						$item_1 = implode(",", $item_1);
						$sql_1 = "#Select People From: Specific Departments
								SELECT u.id AS user_id, CONCAT(ud.first_name, ' ', ud.last_name) AS full_name, ud.job_title, ud.profile_pic, ud.organization_id FROM users u
								INNER JOIN user_details ud ON u.id = ud.user_id
								WHERE u.role_id = 2 AND u.status = 1 AND u.is_deleted = 0 AND ud.department_id IN ($item_1)
								ORDER BY ud.first_name, ud.last_name";
						$sql_3 = "#Select People From: Specific Departments
								SELECT u.id AS user_id FROM users u
								INNER JOIN user_details ud ON u.id = ud.user_id
								WHERE u.role_id = 2 AND u.status = 1 AND u.is_deleted = 0 AND ud.department_id IN ($item_1)
								ORDER BY ud.first_name, ud.last_name";
					}
					else if($people_from == "users"){
						$item_1 = implode(",", $item_1);
						$sql_1 = "#Select People From: Specific People
								SELECT u.id AS user_id, CONCAT(ud.first_name, ' ', ud.last_name) AS full_name, ud.job_title, ud.profile_pic, ud.organization_id FROM users u
								INNER JOIN user_details ud ON u.id = ud.user_id
								WHERE u.role_id = 2 AND u.status = 1 AND u.is_deleted = 0 AND u.id IN ($item_1)
								ORDER BY ud.first_name, ud.last_name";
						$sql_3 = "#Select People From: Specific People
								SELECT u.id AS user_id FROM users u
								INNER JOIN user_details ud ON u.id = ud.user_id
								WHERE u.role_id = 2 AND u.status = 1 AND u.is_deleted = 0 AND u.id IN ($item_1)
								ORDER BY ud.first_name, ud.last_name";
					}
					else if($people_from == "tags"){
						$q = implode('$$$', $item_1);
						$termsArr = array_map('trim', explode('$$$', $q));
					 	$termsArr = escapes($termsArr );
						$termsStr = implode("','",$termsArr);

						$sql_1 = "#Select People From: Specific Tags
								SELECT DISTINCT t.tagged_user_id AS user_id, CONCAT(ud.first_name, ' ', ud.last_name) AS full_name, ud.job_title, ud.profile_pic, ud.organization_id FROM tags t
								INNER JOIN users u ON t.tagged_user_id = u.id
								INNER JOIN user_details ud ON u.id = ud.user_id
								WHERE u.role_id = 2 AND u.status = 1 AND u.is_deleted = 0 AND t.user_id = $user_id AND t.tag IN ('" . $termsStr . "')
								ORDER BY ud.first_name, ud.last_name";
						$sql_3 = "#Select People From: Specific Tags
								SELECT DISTINCT t.tagged_user_id AS user_id FROM tags t
								INNER JOIN users u ON t.tagged_user_id = u.id
								INNER JOIN user_details ud ON u.id = ud.user_id
								WHERE u.role_id = 2 AND u.status = 1 AND u.is_deleted = 0 AND t.user_id = $user_id AND t.tag IN ('" . $termsStr . "')
								ORDER BY ud.first_name, ud.last_name";
					}
					else if($people_from == "skills"){
						$item_1 = implode(",", $item_1);
						$sql_1 = "#Select People From: Specific Skills
								SELECT DISTINCT u.id AS user_id, CONCAT(ud.first_name, ' ', ud.last_name) AS full_name, ud.job_title, ud.profile_pic, ud.organization_id FROM users u
								INNER JOIN user_details ud ON u.id = ud.user_id
								LEFT JOIN user_skills us ON u.id = us.user_id
								WHERE u.role_id = 2 AND u.status = 1 AND u.is_deleted = 0 AND us.skill_id IN ($item_1)
								ORDER BY ud.first_name, ud.last_name";
						$sql_3 = "#Select People From: Specific Skills
								SELECT DISTINCT u.id AS user_id FROM users u
								INNER JOIN user_details ud ON u.id = ud.user_id
								LEFT JOIN user_skills us ON u.id = us.user_id
								WHERE u.role_id = 2 AND u.status = 1 AND u.is_deleted = 0 AND us.skill_id IN ($item_1)
								ORDER BY ud.first_name, ud.last_name";
					}
					else if($people_from == "subjects"){
						$item_1 = implode(",", $item_1);
						$sql_1 = "#Select People From: Specific Subjects
								SELECT DISTINCT u.id AS user_id, CONCAT(ud.first_name, ' ', ud.last_name) AS full_name, ud.job_title, ud.profile_pic, ud.organization_id FROM users u
								INNER JOIN user_details ud ON u.id = ud.user_id
								LEFT JOIN user_subjects us ON u.id = us.user_id
								WHERE u.role_id = 2 AND u.status = 1 AND u.is_deleted = 0 AND us.subject_id IN ($item_1)
								ORDER BY ud.first_name, ud.last_name";
						$sql_3 = "#Select People From: Specific Subjects
								SELECT DISTINCT u.id AS user_id FROM users u
								INNER JOIN user_details ud ON u.id = ud.user_id
								LEFT JOIN user_subjects us ON u.id = us.user_id
								WHERE u.role_id = 2 AND u.status = 1 AND u.is_deleted = 0 AND us.subject_id IN ($item_1)
								ORDER BY ud.first_name, ud.last_name";
					}
					else if($people_from == "domains"){
						$item_1 = implode(",", $item_1);
						$sql_1 = "#Select People From: Specific Domains
								SELECT DISTINCT u.id AS user_id, CONCAT(ud.first_name, ' ', ud.last_name) AS full_name, ud.job_title, ud.profile_pic, ud.organization_id FROM users u
								INNER JOIN user_details ud ON u.id = ud.user_id
								LEFT JOIN user_domains us ON u.id = us.user_id
								WHERE u.role_id = 2 AND u.status = 1 AND u.is_deleted = 0 AND us.domain_id IN ($item_1)
								ORDER BY ud.first_name, ud.last_name";
						$sql_3 = "#Select People From: Specific Domains
								SELECT DISTINCT u.id AS user_id FROM users u
								INNER JOIN user_details ud ON u.id = ud.user_id
								LEFT JOIN user_domains us ON u.id = us.user_id
								WHERE u.role_id = 2 AND u.status = 1 AND u.is_deleted = 0 AND us.domain_id IN ($item_1)
								ORDER BY ud.first_name, ud.last_name";
					}
					else if($people_from == "all_projects"){
						$sql_1 = "#Select People From: All My Projects
								SELECT DISTINCT up1.user_id, CONCAT(ud.first_name, ' ', ud.last_name) AS full_name, ud.job_title, ud.profile_pic, ud.organization_id FROM user_permissions up1
								INNER JOIN users u ON up1.user_id = u.id
								INNER JOIN user_details ud ON u.id = ud.user_id
								INNER JOIN user_permissions up2 ON up1.project_id = up2.project_id AND up2.workspace_id IS NULL
								WHERE up1.workspace_id IS NULL AND up2.user_id = $user_id AND u.role_id = 2 AND u.status = 1 AND u.is_deleted = 0
								ORDER BY ud.first_name, ud.last_name";
						$sql_3 = "#Select People From: All My Projects
								SELECT DISTINCT up1.user_id FROM user_permissions up1
								INNER JOIN users u ON up1.user_id = u.id
								INNER JOIN user_details ud ON u.id = ud.user_id
								INNER JOIN user_permissions up2 ON up1.project_id = up2.project_id AND up2.workspace_id IS NULL
								WHERE up1.workspace_id IS NULL AND up2.user_id = $user_id AND u.role_id = 2 AND u.status = 1 AND u.is_deleted = 0
								ORDER BY ud.first_name, ud.last_name";
					}
					else if($people_from == "created_projects"){
						$sql_1 = "#Select People From: Projects I Created
								SELECT DISTINCT up1.user_id, CONCAT(ud.first_name, ' ', ud.last_name) AS full_name, ud.job_title, ud.profile_pic, ud.organization_id FROM user_permissions up1
								INNER JOIN users u ON up1.user_id = u.id
								INNER JOIN user_details ud ON u.id = ud.user_id
								INNER JOIN user_permissions up2 ON up1.project_id = up2.project_id AND up2.workspace_id IS NULL
								WHERE up1.workspace_id IS NULL AND up2.user_id = $user_id AND up2.role = 'Creator' AND u.role_id = 2 AND u.status = 1 AND u.is_deleted = 0
								ORDER BY ud.first_name, ud.last_name";
						$sql_3 = "#Select People From: Projects I Created
								SELECT DISTINCT up1.user_id FROM user_permissions up1
								INNER JOIN users u ON up1.user_id = u.id
								INNER JOIN user_details ud ON u.id = ud.user_id
								INNER JOIN user_permissions up2 ON up1.project_id = up2.project_id AND up2.workspace_id IS NULL
								WHERE up1.workspace_id IS NULL AND up2.user_id = $user_id AND up2.role = 'Creator' AND u.role_id = 2 AND u.status = 1 AND u.is_deleted = 0
								ORDER BY ud.first_name, ud.last_name";
					}
					else if($people_from == "owner_projects"){
						$sql_1 = "#Select People From: Projects I Own
								SELECT DISTINCT up1.user_id, CONCAT(ud.first_name, ' ', ud.last_name) AS full_name, ud.job_title, ud.profile_pic, ud.organization_id FROM user_permissions up1
								INNER JOIN users u ON up1.user_id = u.id
								INNER JOIN user_details ud ON u.id = ud.user_id
								INNER JOIN user_permissions up2 ON up1.project_id = up2.project_id AND up2.workspace_id IS NULL
								WHERE up1.workspace_id IS NULL AND up2.user_id = $user_id AND up2.role IN ('Creator', 'Owner', 'Group Owner') AND u.role_id = 2 AND u.status = 1 AND u.is_deleted = 0
								ORDER BY ud.first_name, ud.last_name";
						$sql_3 = "#Select People From: Projects I Own
								SELECT DISTINCT up1.user_id FROM user_permissions up1
								INNER JOIN users u ON up1.user_id = u.id
								INNER JOIN user_details ud ON u.id = ud.user_id
								INNER JOIN user_permissions up2 ON up1.project_id = up2.project_id AND up2.workspace_id IS NULL
								WHERE up1.workspace_id IS NULL AND up2.user_id = $user_id AND up2.role IN ('Creator', 'Owner', 'Group Owner') AND u.role_id = 2 AND u.status = 1 AND u.is_deleted = 0
								ORDER BY ud.first_name, ud.last_name";
					}
					else if($people_from == "shared_projects"){
						$sql_1 = "#Select People From: Projects Shared with Me
								SELECT DISTINCT up1.user_id, CONCAT(ud.first_name, ' ', ud.last_name) AS full_name, ud.job_title, ud.profile_pic, ud.organization_id FROM user_permissions up1
								INNER JOIN users u ON up1.user_id = u.id
								INNER JOIN user_details ud ON u.id = ud.user_id
								INNER JOIN user_permissions up2 ON up1.project_id = up2.project_id AND up2.workspace_id IS NULL
								WHERE up1.workspace_id IS NULL AND up2.user_id = $user_id AND up2.role IN ('Sharer', 'Group Sharer') AND u.role_id = 2 AND u.status = 1 AND u.is_deleted = 0
								ORDER BY ud.first_name, ud.last_name";
						$sql_3 = "#Select People From: Projects Shared with Me
								SELECT DISTINCT up1.user_id FROM user_permissions up1
								INNER JOIN users u ON up1.user_id = u.id
								INNER JOIN user_details ud ON u.id = ud.user_id
								INNER JOIN user_permissions up2 ON up1.project_id = up2.project_id AND up2.workspace_id IS NULL
								WHERE up1.workspace_id IS NULL AND up2.user_id = $user_id AND up2.role IN ('Sharer', 'Group Sharer') AND u.role_id = 2 AND u.status = 1 AND u.is_deleted = 0
								ORDER BY ud.first_name, ud.last_name";
					}
					else if($people_from == "project"){
						$item_1 = implode(",", $item_1);
						if(!empty($resourcer)){
							$sql_1 = "#Select People From: Specific Projects - RESOURCER
									SELECT DISTINCT up1.user_id, CONCAT(ud.first_name, ' ', ud.last_name) AS full_name, ud.job_title, ud.profile_pic, ud.organization_id FROM user_permissions up1
									INNER JOIN users u ON up1.user_id = u.id
									INNER JOIN user_details ud ON u.id = ud.user_id
									INNER JOIN user_permissions up2 ON up1.project_id = up2.project_id AND up2.workspace_id IS NULL
									WHERE up1.workspace_id IS NULL AND
									up2.role = 'Creator' AND
									up1.project_id IN ($item_1) AND
									u.role_id = 2 AND u.status = 1 AND u.is_deleted = 0
									ORDER BY ud.first_name, ud.last_name";

							$sql_3 = "#Select People From: Specific Projects - RESOURCER
									SELECT DISTINCT up1.user_id FROM user_permissions up1
									INNER JOIN users u ON up1.user_id = u.id
									INNER JOIN user_details ud ON u.id = ud.user_id
									INNER JOIN user_permissions up2 ON up1.project_id = up2.project_id AND up2.workspace_id IS NULL
									WHERE up1.workspace_id IS NULL AND up2.role = 'Creator' AND up1.project_id IN ($item_1) AND u.role_id = 2 AND u.status = 1 AND u.is_deleted = 0
									ORDER BY ud.first_name, ud.last_name";

						}
						else{
							$sql_1 = "#Select People From: Specific Projects - NOT RESOURCER
									SELECT DISTINCT up1.user_id, CONCAT(ud.first_name, ' ', ud.last_name) AS full_name, ud.job_title, ud.profile_pic, ud.organization_id FROM user_permissions up1
									INNER JOIN users u ON up1.user_id = u.id
									INNER JOIN user_details ud ON u.id = ud.user_id
									INNER JOIN user_permissions up2 ON up1.project_id = up2.project_id AND up2.workspace_id IS NULL
									WHERE up1.workspace_id IS NULL AND up2.user_id = $user_id AND up1.project_id IN ($item_1) AND u.role_id = 2 AND u.status = 1 AND u.is_deleted = 0
									ORDER BY ud.first_name, ud.last_name";
							$sql_3 = "#Select People From: Specific Projects - NOT RESOURCER
									SELECT DISTINCT up1.user_id FROM user_permissions up1
									INNER JOIN users u ON up1.user_id = u.id
									INNER JOIN user_details ud ON u.id = ud.user_id
									INNER JOIN user_permissions up2 ON up1.project_id = up2.project_id AND up2.workspace_id IS NULL
									WHERE up1.workspace_id IS NULL AND up2.user_id = $user_id AND up1.project_id IN ($item_1) AND u.role_id = 2 AND u.status = 1 AND u.is_deleted = 0
									ORDER BY ud.first_name, ud.last_name";
						}
					}
				}
				if(isset($date_type) && !empty($date_type)){
					if($date_type == 'daily'){
						$end_date = date('Y-m-d', strtotime($dates. ' + 31 days'));
					}
					elseif($date_type == 'weekly'){
						$end_date = date('Y-m-d', strtotime($dates. ' + 28 days'));
					}
					elseif($date_type == 'monthly'){
						$effectiveDate = date('Y-m-d', strtotime( $dates ." + 2 months"));
						$end_date = date("Y-m-t", strtotime($effectiveDate));
					}

					$sql_2 = "CROSS JOIN (SELECT '$start_date' AS start_date, '$end_date' AS end_date) CONST";
					$sql_10 = "AND (DATE(e.start_date) <= DATE('$end_date') AND DATE(e.end_date) >= DATE('$start_date'))";

					$sql_19 = "IF(DATE(MIN(e.start_date)) < DATE('$start_date'), DATE(MIN(e.start_date)), DATE('$start_date')) AS start_date";
					$sql_20 = "IF(DATE(MAX(e.end_date)) > DATE('$end_date'), DATE(MAX(e.end_date)), DATE('$end_date')) AS end_date";

					$sql_21 = "(DATE(e.start_date) <= DATE('$start_date') AND DATE(e.end_date) >= DATE('$end_date'))";

				}
				if(isset($adjustments) && !empty($adjustments)){
					$sql_5 = "IF(pe.remaining_hours IS NULL, IFNULL(ee1.completed_hours,0) + IFNULL(ee1.remaining_hours,0), IFNULL(ee1.completed_hours,0) + pe.remaining_hours) AS element_effort";
					$sql_6 = "IF(pe.remaining_hours IS NOT NULL,1,0)  AS element_adjustments";
					$sql_22 = "AND (ee1.id IS NOT NULL OR pe.id IS NOT NULL)";
					$sql_11 = "UNION ALL
                            SELECT
                                pe.user_id,
                                pe.element_id,
                                DATE(e.start_date) AS start_date,
                                DATE(e.end_date) AS end_date
                            FROM
                                plan_efforts pe
                            INNER JOIN elements e ON
                                pe.element_id = e.id
                                AND e.start_date IS NOT NULL
                            WHERE
                            	";
                	$sql_11 .= "(DATE(e.start_date) <= DATE('$end_date') AND DATE(e.end_date) >= DATE('$start_date'))";
                	if($work_from == "project" && (isset($item_2) && !empty($item_2))){
                		$item_2_str = implode(",", $item_2);
                		$sql_11 .= "AND pe.project_id IN ($item_2_str) # ONLY INCLUDE SPECIFIC PROJECTS ARE SELECTED";
                	}

            		$sql_14 = "LEFT JOIN plan_efforts pe ON
	                    	u_tasks.user_id = pe.user_id
	                        AND u_tasks.element_id = pe.element_id";

				}
				else{
					$sql_5 = "IFNULL(ee1.completed_hours,0) + IFNULL(ee1.remaining_hours,0) AS element_effort";
					$sql_6 = "0 AS element_adjustments";
					$sql_22 = "AND ee1.id IS NOT NULL";
				}

				if($work_from == "project" && (isset($item_2) && !empty($item_2))){
					// pr($item_2,1);
            		$item_2_s = implode(",", $item_2);
            		$sql_9 = "AND pe.project_id IN ($item_2_s) # ONLY INCLUDE SPECIFIC PROJECTS ARE SELECTED";
            		$sql_15 = "AND up.project_id IN ($item_2_s) # ONLY INCLUDE SPECIFIC PROJECTS ARE SELECTED";
            	}

            	// CREATE SELECT FIELDS LIST
				$sel = [];
				// daily
				if($date_type == 'daily'){
					$daily_start = $start_date;
					for($i = 1; $i <= 31; $i++){
						$daily_day = date('d', strtotime($daily_start));
						$sel[] = "GROUP_CONCAT((CASE WHEN DATE_FORMAT(util.the_date, '%Y-%m-%d') = '$daily_start' THEN util.the_date ELSE NULL END)) AS '".$i."',
					    ROUND(SUM(CASE WHEN DATE_FORMAT(util.the_date, '%Y-%m-%d') = '$daily_start' THEN util.availability ELSE NULL END),1) AS '".$i."_da',
					    ROUND(SUM(CASE WHEN DATE_FORMAT(util.the_date, '%Y-%m-%d') = '$daily_start' THEN util.effort ELSE 0 END),2) AS '".$i."_de',
					    IF(SUM(CASE WHEN DATE_FORMAT(util.the_date, '%Y-%m-%d') = '$daily_start' THEN util.adjustments ELSE 0 END) > 0, 1, 0) AS '".$i."_ad',
					    SUM(CASE WHEN DATE_FORMAT(util.the_date, '%Y-%m-%d') = '$daily_start' THEN util.absence_count ELSE NULL END) AS '".$i."_ab',
					    SUM(CASE WHEN DATE_FORMAT(util.the_date, '%Y-%m-%d') = '$daily_start' THEN util.block_count ELSE NULL END) AS '".$i."_wb'";
						$date = date('Y-m-d', strtotime($daily_start.' +1 day'));
						$daily_start = $date;
					}
					$sel_str = '';
					if(isset($sel) && !empty($sel)){
						$sel_str = implode(',', $sel);
					}
				}
				// weekly
				else if($date_type == 'weekly'){
					$startTime = strtotime($start_date);
					$endTime = strtotime($end_date);
					$i = 1;
					while ($startTime < $endTime) {
					    $week_start = date('Y-m-d', $startTime);
					    $week_end = date('Y-m-d', strtotime($week_start. ' +6 days'));
					    $sel[] = "GROUP_CONCAT(DISTINCT(CASE WHEN DATE(util.the_date) BETWEEN DATE('$week_start') AND DATE('$week_end') THEN '$week_start' ELSE NULL END)) AS '0".$i."_dt',
						    ROUND(SUM(CASE WHEN DATE(util.the_date) BETWEEN DATE('$week_start') AND DATE('$week_end') THEN util.availability ELSE NULL END),1) AS '0".$i."_da',
						    ROUND(SUM(CASE WHEN DATE(util.the_date) BETWEEN DATE('$week_start') AND DATE('$week_end') THEN util.effort ELSE 0 END),2) AS '0".$i."_de',
						    IF(SUM(CASE WHEN DATE(util.the_date) BETWEEN DATE('$week_start') AND DATE('$week_end') THEN util.adjustments ELSE 0 END) > 0, 1, 0) AS '0".$i."_ad',
						    IF(SUM(CASE WHEN DATE(util.the_date) BETWEEN DATE('$week_start') AND DATE('$week_end') THEN util.absence_count ELSE 0 END) > 0, 1, 0) AS '0".$i."_ab',
						    IF(SUM(CASE WHEN DATE(util.the_date) BETWEEN DATE('$week_start') AND DATE('$week_end') THEN util.block_count ELSE 0 END) > 0, 1, 0) AS '0".$i."_wb'";
					    $startTime += strtotime('+1 week', 0);
					    $i++;
					}
					$sel_str = '';
					if(isset($sel) && !empty($sel)){
						$sel_str = implode(',', $sel);
					}
				}
				// monthly
				else if($date_type == 'monthly'){
					$month_start = $start_date;
					for ($cnt = 1; $cnt <= 3; $cnt++) {
						$month_end = date("Y-m-t", strtotime($month_start));
						$i = $cnt;

						$sel[] = "GROUP_CONCAT(DISTINCT(CASE WHEN DATE(util.the_date) BETWEEN DATE('$month_start') AND DATE('$month_end') THEN '$month_start' ELSE NULL END)) AS '0".$i."_dt',
						    ROUND(SUM(CASE WHEN DATE(util.the_date) BETWEEN DATE('$month_start') AND DATE('$month_end') THEN util.availability ELSE NULL END),2) AS '0".$i."_da',
						    ROUND(SUM(CASE WHEN DATE(util.the_date) BETWEEN DATE('$month_start') AND DATE('$month_end') THEN util.effort ELSE 0 END),2) AS '0".$i."_de',
						    IF(SUM(CASE WHEN DATE(util.the_date) BETWEEN DATE('$month_start') AND DATE('$month_end') THEN util.adjustments ELSE 0 END) > 0, 1, 0) AS '0".$i."_ad',
						    IF(SUM(CASE WHEN DATE(util.the_date) BETWEEN DATE('$month_start') AND DATE('$month_end') THEN util.absence_count ELSE 0 END) > 0, 1, 0) AS '0".$i."_ab',
						    IF(SUM(CASE WHEN DATE(util.the_date) BETWEEN DATE('$month_start') AND DATE('$month_end') THEN util.block_count ELSE 0 END) > 0, 1, 0) AS '0".$i."_wb'";

						$month_start = date('Y-m-d', strtotime($month_start.' +1 month'));
					}
					$sel_str = '';
					if(isset($sel) && !empty($sel)){
						$sel_str = implode(',', $sel);
					}
				}

				$query_select = $query_group = "";
				if(isset($post['user_counter']) && !empty($post['user_counter'])) {
					$query_select = "GROUP_CONCAT(DISTINCT(util.user_id)) AS users";
					$query_group = "";
				}
				else{
					$query_select = "util.user_id, util.full_name, util.job_title, util.profile_pic, util.organization_id, $sel_str";
					$query_group = "GROUP BY util.user_id";
				}

				$main_query = "SELECT
						    $query_select
						FROM
						(
						    SELECT
						        user_dates.user_id,
						        user_dates.full_name,
						        user_dates.job_title,
						        user_dates.profile_pic,
						        user_dates.organization_id,
						        user_dates.the_date,
						        IFNULL(user_tasks.availability,0) AS availability,
						        IFNULL(user_tasks.effort,0) AS effort,
						        user_tasks.adjustments,
						        user_dates.absence_count,
						        user_dates.block_count
						    FROM
						    (
						        SELECT
						            u.user_id, u.full_name, u.job_title, u.profile_pic, u.organization_id, td.the_date, COUNT(av.id) AS absence_count, COUNT(ub.id) AS block_count
						        FROM
						        (
						            (
						                # CHANGE THIS SUB QUERY BASED ON SELECTED USERS IN UI (SEE ADDITIONAL SQL FILE 1 - SELECT PEOPLE FROM) sql_1
						                $sql_1
						                #-----
						                $limit_str #SET LIMIT AS REQUIRED FOR PAGING
						            ) AS u
						            CROSS JOIN
						            (
						                SELECT start_date + INTERVAL num DAY AS the_date
						                FROM
						                (
						                    SELECT digit_1.d + 10 * digit_2.d AS num
						                    FROM (SELECT 0 as d UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) digit_1
						                    CROSS JOIN (SELECT 0 as d UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) digit_2
						                ) digits
						                $sql_2 # CHANGE DATE RANGE BASED ON DAILY, WEEKLY, MONTHLY UI SELECTION AND USER DATE UI SELECTION sql_2
						                WHERE start_date + INTERVAL num DAY <= end_date
						            ) AS td
						            LEFT JOIN availabilities av ON
						                u.user_id = av.user_id
						                AND (DATE(STR_TO_DATE(LEFT(av.avail_start_date,10),'%Y-%m-%d')) <= td.the_date AND DATE(STR_TO_DATE(LEFT(av.avail_end_date,10),'%Y-%m-%d')) >= td.the_date)
						            LEFT JOIN user_blocks ub ON
						                u.user_id = ub.user_id
						                AND (DATE(ub.work_start_date) <= td.the_date AND DATE(ub.work_end_date) >= td.the_date)
						        )
						        GROUP BY u.user_id, td.the_date
						    ) AS user_dates
						    LEFT JOIN
						    (

						        SELECT
						            user_tasks.user_id,
						            user_tasks.the_date,
						            user_tasks.availability,
						            SUM(user_tasks.day_effort) AS effort,
						            IF(SUM(user_tasks.element_adjustments) > 0, 1, 0) AS adjustments
						        FROM
						        (

						            SELECT
						                user_data.user_id,
						                user_data.the_date,
						                user_data.availability,
						                task_data.element_id,
						                task_data.element_effort,
						                task_data.element_adjustments,
						                IF(task_data.total_availability = 0, ROUND(task_data.element_effort / task_data.duration, 2)
						                    , ROUND(task_data.element_effort * user_data.availability / task_data.total_availability, 2)
						                 ) AS day_effort
						            FROM
						            (
						                SELECT
						                    u.user_id, td.the_date,
						                    (CASE
						                     WHEN WEEKDAY(td.the_date) = 0 THEN IFNULL(ua1.monday,0)
						                     WHEN WEEKDAY(td.the_date) = 1 THEN IFNULL(ua1.tuesday,0)
						                     WHEN WEEKDAY(td.the_date) = 2 THEN IFNULL(ua1.wednesday,0)
						                     WHEN WEEKDAY(td.the_date) = 3 THEN IFNULL(ua1.thursday,0)
						                     WHEN WEEKDAY(td.the_date) = 4 THEN IFNULL(ua1.friday,0)
						                     WHEN WEEKDAY(td.the_date) = 5 THEN IFNULL(ua1.saturday,0)
						                     WHEN WEEKDAY(td.the_date) = 6 THEN IFNULL(ua1.sunday,0)
						                     ELSE 0 END) AS availability
						                FROM
						                (
						                    (
						                        # CHANGE THIS SUB QUERY BASED ON SELECTED USERS IN UI (SEE ADDITIONAL SQL FILE 2 - SELECT PEOPLE FROM) sql_3
						                        $sql_3
						                        #-----
						                        $limit_str #SET LIMIT AS REQUIRED FOR PAGING
						                    ) AS u
						                    CROSS JOIN
						                    (
						                        SELECT start_date + INTERVAL num DAY AS the_date
						                        FROM
						                        (
						                            SELECT digit_1.d + 10 * digit_2.d AS num
						                            FROM (SELECT 0 as d UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) digit_1
						                            CROSS JOIN (SELECT 0 as d UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) digit_2
						                        ) digits
						                        $sql_2 # CHANGE DATE RANGE BASED ON DAILY, WEEKLY, MONTHLY UI SELECTION AND USER DATE UI SELECTION sql_2
						                        WHERE start_date + INTERVAL num DAY <= end_date
						                    ) AS td
						                    LEFT JOIN user_availabilities ua1 ON
						                        u.user_id = ua1.user_id
						                        AND DATE(ua1.effective) <= td.the_date
						                    LEFT JOIN user_availabilities ua2 ON
						                        DATE(ua2.effective) <= td.the_date
						                        AND ua1.user_id = ua2.user_id
						                        AND (ua1.effective < ua2.effective)
						                )
						                WHERE ua2.effective IS NULL
						                GROUP BY u.user_id, td.the_date
						            ) AS user_data
						            LEFT JOIN
						            (

						                SELECT
						                    u.user_id,
						                    td.the_date,
						                    u_tasks.element_id,
						                    # IF ADJUSTMENTS IS CHECKED IN UI BY USER THEN USE THIS LINE: sql_5
						                    $sql_5,
						                    # ELSE IF AJUSTMENTS NOT CHECKED IN UI BY USER THEN USE THIS LINE:
						                    #IFNULL(ee1.completed_hours,0) + IFNULL(ee1.remaining_hours,0) AS element_effort,

						                    # IF ADJUSTMENTS IS CHECKED IN UI BY USER THEN USE THIS LINE: sql_6
						                    $sql_6,
						                    # ELSE IF AJUSTMENTS NOT CHECKED IN UI BY USER THEN USE THIS LINE:
						                    #0 AS element_adjustments,
						                    element_dur_av.duration,
						                    element_dur_av.total_availability
						                FROM
						                    (
						                        # CHANGE THIS SUB QUERY BASED ON SELECTED USERS IN UI (SEE ADDITIONAL SQL FILE 2 - SELECT PEOPLE FROM) sql_3
						                        $sql_3
						                        #-----
						                        $limit_str #SET LIMIT AS REQUIRED FOR PAGING
						                    ) AS u
						                    CROSS JOIN
						                    (
						                        SELECT start_date + INTERVAL num DAY AS the_date
						                        FROM
						                        (
						                            SELECT digit_1.d + 10 * digit_2.d AS num
						                            FROM (SELECT 0 as d UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) digit_1
						                            CROSS JOIN (SELECT 0 as d UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) digit_2
						                        ) digits
						                        $sql_2 # CHANGE DATE RANGE BASED ON DAILY, WEEKLY, MONTHLY UI SELECTION AND USER DATE UI SELECTION sql_2
						                        WHERE start_date + INTERVAL num DAY <= end_date
						                    ) AS td
						                    LEFT JOIN
						                    (
						                        SELECT DISTINCT
						                            at.user_id,
						                            at.element_id,
						                            DATE(at.start_date) AS start_date,
						                            DATE(at.end_date) AS end_date
						                        FROM
						                        (
						                            SELECT
						                                up.user_id,
						                                up.element_id,
						                                DATE(e.start_date) AS start_date,
						                                DATE(e.end_date) AS end_date
						                            FROM
						                                user_permissions up
						                            INNER JOIN elements e ON
						                                up.element_id = e.id
						                                AND e.start_date IS NOT NULL
						                            WHERE
						                                up.element_id IS NOT NULL
						                                $sql_15 # ONLY INCLUDE SPECIFIC PROJECTS ARE SELECTED sql_15

						                                $sql_10 # CHANGE DATE RANGE BASED ON DAILY, WEEKLY, MONTHLY UI SELECTION AND USER DATE UI SELECTION sql_10

						                            #----- IF ADJUSTMENTS IS CHECKED IN UI BY USER THEN INCLUDE THIS SECTION: sql_11
						                            $sql_11
						                            #----- END OF SECTION
						                        ) AS at
						                    ) AS u_tasks ON
						                        u.user_id = u_tasks.user_id
						                        AND (DATE(u_tasks.start_date) <= td.the_date AND DATE(u_tasks.end_date) >= td.the_date)
						                    LEFT JOIN element_efforts ee1 ON
						                        u_tasks.user_id = ee1.user_id
						                        AND u_tasks.element_id = ee1.element_id
						                        AND DATE(ee1.created) <= td.the_date
						                    LEFT JOIN element_efforts ee2 ON
						                        ee1.user_id = ee2.user_id
						                        AND ee1.element_id = ee2.element_id
						                        AND (ee1.created < ee2.created)
						                        AND DATE(ee2.created) <= td.the_date
						                    #----- IF ADJUSTMENTS IS CHECKED IN UI BY USER THEN INCLUDE THIS SECTION: sql_14
						                    $sql_14
						                    #----- END OF SECTION
						                    LEFT JOIN
						                    (
						                            SELECT
						                                u_tasks.user_id,
						                                u_tasks.element_id,
						                                (DATEDIFF(u_tasks.end_date, u_tasks.start_date)+1) AS duration,
						                                SUM(u_availability.availability) AS total_availability
						                            FROM
						                            (
						                                SELECT DISTINCT
						                                    at.user_id,
						                                    at.element_id,
						                                    DATE(at.start_date) AS start_date,
						                                    DATE(at.end_date) AS end_date
						                                FROM
						                                (
						                                    SELECT
						                                        up.user_id,
						                                        up.element_id,
						                                        DATE(e.start_date) AS start_date,
						                                        DATE(e.end_date) AS end_date
						                                    FROM
						                                        user_permissions up
						                                    INNER JOIN elements e ON
						                                        up.element_id = e.id
						                                        AND e.start_date IS NOT NULL
						                                    WHERE
						                                        up.element_id IS NOT NULL
						                                        $sql_15 # ONLY INCLUDE SPECIFIC PROJECTS ARE SELECTED sql_15
						                                        $sql_10 # CHANGE DATE RANGE BASED ON DAILY, WEEKLY, MONTHLY UI SELECTION AND USER DATE UI SELECTION sql_10
						                                    UNION ALL
						                                    SELECT
						                                        pe.user_id,
						                                        pe.element_id,
						                                        DATE(e.start_date) AS start_date,
						                                        DATE(e.end_date) AS end_date
						                                    FROM
						                                        plan_efforts pe
						                                    INNER JOIN elements e ON
						                                        pe.element_id = e.id
						                                        AND e.start_date IS NOT NULL
						                                    WHERE
						                                        $sql_21 # CHANGE DATE RANGE BASED ON DAILY, WEEKLY, MONTHLY UI SELECTION AND USER DATE UI SELECTION sql_21

						                                        $sql_9 # ONLY INCLUDE SPECIFIC PROJECTS ARE SELECTED sql_9
						                                ) AS at
						                             ) AS u_tasks
						                             LEFT JOIN
						                             (
						                                SELECT
						                                    u.user_id,
						                                    td.the_date,
						                                    (CASE
						                                     WHEN WEEKDAY(td.the_date) = 0 THEN IFNULL(ua1.monday,0)
						                                     WHEN WEEKDAY(td.the_date) = 1 THEN IFNULL(ua1.tuesday,0)
						                                     WHEN WEEKDAY(td.the_date) = 2 THEN IFNULL(ua1.wednesday,0)
						                                     WHEN WEEKDAY(td.the_date) = 3 THEN IFNULL(ua1.thursday,0)
						                                     WHEN WEEKDAY(td.the_date) = 4 THEN IFNULL(ua1.friday,0)
						                                     WHEN WEEKDAY(td.the_date) = 5 THEN IFNULL(ua1.saturday,0)
						                                     WHEN WEEKDAY(td.the_date) = 6 THEN IFNULL(ua1.sunday,0)
						                                     ELSE 0 END) AS availability
						                                FROM
						                                (
						                                    (
						                                        # CHANGE THIS SUB QUERY BASED ON SELECTED USERS IN UI (SEE ADDITIONAL SQL FILE 2 - SELECT PEOPLE FROM) sql_3
						                                        $sql_3
						                                        #-----
						                                        $limit_str #SET LIMIT AS REQUIRED FOR PAGING
						                                    ) AS u
						                                    CROSS JOIN
						                                    (
						                                        SELECT start_date + INTERVAL num DAY AS the_date
						                                        FROM
						                                        (
						                                            SELECT digit_1.d + 10 * digit_2.d + 100 * digit_3.d + 1000 * digit_4.d AS num
						                                            FROM (SELECT 0 as d UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) digit_1
						                                            CROSS JOIN (SELECT 0 as d UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) digit_2
						                                            CROSS JOIN (SELECT 0 as d UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) digit_3
						                                            CROSS JOIN (SELECT 0 as d UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) digit_4
						                                        ) digits
						                                        CROSS JOIN
						                                        (
						                                            SELECT
						                                                $sql_19, # CHANGE DATE RANGE BASED ON DAILY, WEEKLY, MONTHLY UI SELECTION AND USER DATE UI SELECTION sql_19
						                                                $sql_20 # CHANGE DATE RANGE BASED ON DAILY, WEEKLY, MONTHLY UI SELECTION AND USER DATE UI SELECTION sql_20
						                                            FROM
						                                                elements e
						                                            WHERE
						                                                e.start_date IS NOT NULL
						                                                $sql_10 # CHANGE DATE RANGE BASED ON DAILY, WEEKLY, MONTHLY UI SELECTION AND USER DATE UI SELECTION sql_10
						                                        ) CONST
						                                        WHERE start_date + INTERVAL num DAY <= end_date
						                                    ) AS td
						                                    LEFT JOIN user_availabilities ua1 ON
						                                        u.user_id = ua1.user_id
						                                        AND DATE(ua1.effective) <= td.the_date
						                                    LEFT JOIN user_availabilities ua2 ON
						                                        DATE(ua2.effective) <= td.the_date
						                                        AND ua1.user_id = ua2.user_id
						                                        AND (ua1.effective < ua2.effective)
						                                 )
						                                 WHERE ua2.effective IS NULL
						                                 GROUP BY u.user_id, td.the_date
						                             ) AS u_availability ON
						                                u_tasks.user_id = u_availability.user_id
						                                AND (DATE(u_tasks.start_date) <= u_availability.the_date AND DATE(u_tasks.end_date) >= u_availability.the_date)
						                            GROUP BY
						                                u_tasks.user_id, u_tasks.element_id
						                    ) AS element_dur_av ON
						                        u_tasks.user_id = element_dur_av.user_id
						                        AND u_tasks.element_id = element_dur_av.element_id
						                    WHERE
						                        ee2.id IS NULL
						                        # IF ADJUSTMENTS IS CHECKED IN UI BY USER THEN USE THIS LINE: sql_22
						                        $sql_22
						                        # ELSE IF AJUSTMENTS NOT CHECKED IN UI BY USER THEN USE THIS LINE:
						                        #AND ee1.id IS NOT NULL
						                    GROUP BY u.user_id, td.the_date, u_tasks.element_id
						            ) AS task_data ON
						                user_data.user_id = task_data.user_id
						                AND user_data.the_date = task_data.the_date
						        ) AS user_tasks
						        GROUP BY
						            user_tasks.user_id, user_tasks.the_date
						    ) AS user_tasks ON
						        user_dates.user_id = user_tasks.user_id
						        AND user_dates.the_date = user_tasks.the_date
						) AS util
						$query_group
						ORDER BY util.full_name";
				$data = $this->UserPermission->query($main_query);

			}
        }
        if(isset($post['user_counter']) && !empty($post['user_counter'])) {
        	echo json_encode($data[0][0]['users']);
	        exit;
	    }
        if(isset($post["count"]) && !empty($post["count"])){
			echo json_encode(count($data));
	        exit;
        }
        else{
	        $this->set("date_type", $date_type);
	        $this->set("data", $data);
			$this->render('/Searches/planning/utilization');
		}
    }


	public function get_utilization_section() {
		if ($this->request->isAjax()) {
            $this->layout = 'ajax';
            $this->loadModel('UserPermission');
            $response = ['success' => false];
            $data = [];
            $user_id = $this->Session->read('Auth.User.id');
            $resourcer = $this->Session->read('Auth.User.UserDetail.resourcer');
            if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				$people_from = $post["people_from"];
	            $item_1 = $post["item_1"];
	            $work_from = $post["work_from"];
	            $item_2 = $post["item_2"];
	            $date_type = $post["date_type"];
	            $dates = $post["dates"];

	            $page = (isset($post["page"]) && !empty($post["page"])) ? $post["page"] : 0;
	            $section = (isset($post["section"]) && !empty($post["section"])) ? $post["section"] : false;

				$limit_str = '';
				if(isset($post["limit"]) && !empty($post["limit"])){
					$limit = $post["limit"];
					$limit_str = "LIMIT $page, $limit";
				}
				else if(!isset($post["count"]) || empty($post["count"])){
					$limit_str = "LIMIT 0, ".$this->utill_offset;
				}

	            $start_date = date('Y-m-d', strtotime($dates));
	            $adjustments = $post["adjustments"];

				$sql_1 = $sql_2 = $sql_3 = $sql_4 = $sql_5 = $sql_6 = $sql_7 = $sql_8 = $sql_9 = $sql_10 = $sql_11 = $sql_12 = $sql_13 = $sql_14 = $sql_15 = $sql_16 = $sql_17 = $sql_18 = $sql_19 = $sql_20 = $sql_21 = $sql_22 = "";

				if(isset($people_from) && !empty($people_from)){
					if($people_from == "community"){
						$sql_1 = "#Select People From: All Community
								SELECT u.id AS user_id, CONCAT(ud.first_name, ' ', ud.last_name) AS full_name, ud.job_title, ud.profile_pic, ud.organization_id FROM users u
								INNER JOIN user_details ud ON u.id = ud.user_id
								WHERE u.role_id = 2 AND u.status = 1 AND u.is_deleted = 0
								ORDER BY ud.first_name, ud.last_name";
						$sql_3 = "#Select People From: All Community
								SELECT u.id AS user_id FROM users u
								INNER JOIN user_details ud ON u.id = ud.user_id
								WHERE u.role_id = 2 AND u.status = 1 AND u.is_deleted = 0
								ORDER BY ud.first_name, ud.last_name";
					}
					else if($people_from == "profile"){
						$sql_1 = "#Select People From: My Profile
								SELECT u.id AS user_id, CONCAT(ud.first_name, ' ', ud.last_name) AS full_name, ud.job_title, ud.profile_pic, ud.organization_id FROM users u
								INNER JOIN user_details ud ON u.id = ud.user_id
								WHERE u.role_id = 2 AND u.status = 1 AND u.is_deleted = 0 AND u.id = $user_id
								ORDER BY ud.first_name, ud.last_name";
						$sql_3 = "#Select People From: My Profile
								SELECT u.id AS user_id FROM users u
								INNER JOIN user_details ud ON u.id = ud.user_id
								WHERE u.role_id = 2 AND u.status = 1 AND u.is_deleted = 0 AND u.id = $user_id
								ORDER BY ud.first_name, ud.last_name";
					}
					else if($people_from == "organizations"){
						$item_1 = implode(",", $item_1);
						$sql_1 = "#Select People From: Specific Organizations
								SELECT u.id AS user_id, CONCAT(ud.first_name, ' ', ud.last_name) AS full_name, ud.job_title, ud.profile_pic, ud.organization_id FROM users u
								INNER JOIN user_details ud ON u.id = ud.user_id
								WHERE u.role_id = 2 AND u.status = 1 AND u.is_deleted = 0 AND ud.organization_id IN ($item_1)
								ORDER BY ud.first_name, ud.last_name";
						$sql_3 = "#Select People From: Specific Organizations
								SELECT u.id AS user_id FROM users u
								INNER JOIN user_details ud ON u.id = ud.user_id
								WHERE u.role_id = 2 AND u.status = 1 AND u.is_deleted = 0 AND ud.organization_id IN ($item_1)
								ORDER BY ud.first_name, ud.last_name";
					}
					else if($people_from == "locations"){
						$item_1 = implode(",", $item_1);
						$sql_1 = "#Select People From: Specific Locations
								SELECT u.id AS user_id, CONCAT(ud.first_name, ' ', ud.last_name) AS full_name, ud.job_title, ud.profile_pic, ud.organization_id FROM users u
								INNER JOIN user_details ud ON u.id = ud.user_id
								WHERE u.role_id = 2 AND u.status = 1 AND u.is_deleted = 0 AND ud.location_id IN ($item_1)
								ORDER BY ud.first_name, ud.last_name";
						$sql_3 = "#Select People From: Specific Locations
								SELECT u.id AS user_id FROM users u
								INNER JOIN user_details ud ON u.id = ud.user_id
								WHERE u.role_id = 2 AND u.status = 1 AND u.is_deleted = 0 AND ud.location_id IN ($item_1)
								ORDER BY ud.first_name, ud.last_name";
					}
					else if($people_from == "departments"){
						$item_1 = implode(",", $item_1);
						$sql_1 = "#Select People From: Specific Departments
								SELECT u.id AS user_id, CONCAT(ud.first_name, ' ', ud.last_name) AS full_name, ud.job_title, ud.profile_pic, ud.organization_id FROM users u
								INNER JOIN user_details ud ON u.id = ud.user_id
								WHERE u.role_id = 2 AND u.status = 1 AND u.is_deleted = 0 AND ud.department_id IN ($item_1)
								ORDER BY ud.first_name, ud.last_name";
						$sql_3 = "#Select People From: Specific Departments
								SELECT u.id AS user_id FROM users u
								INNER JOIN user_details ud ON u.id = ud.user_id
								WHERE u.role_id = 2 AND u.status = 1 AND u.is_deleted = 0 AND ud.department_id IN ($item_1)
								ORDER BY ud.first_name, ud.last_name";
					}
					else if($people_from == "users"){
						$item_1 = implode(",", $item_1);
						$sql_1 = "#Select People From: Specific People
								SELECT u.id AS user_id, CONCAT(ud.first_name, ' ', ud.last_name) AS full_name, ud.job_title, ud.profile_pic, ud.organization_id FROM users u
								INNER JOIN user_details ud ON u.id = ud.user_id
								WHERE u.role_id = 2 AND u.status = 1 AND u.is_deleted = 0 AND u.id IN ($item_1)
								ORDER BY ud.first_name, ud.last_name";
						$sql_3 = "#Select People From: Specific People
								SELECT u.id AS user_id FROM users u
								INNER JOIN user_details ud ON u.id = ud.user_id
								WHERE u.role_id = 2 AND u.status = 1 AND u.is_deleted = 0 AND u.id IN ($item_1)
								ORDER BY ud.first_name, ud.last_name";
					}
					else if($people_from == "tags"){
						// $item_1 = implode(",", $item_1);

						$sql_1 = "#Select People From: Specific Tags
								SELECT DISTINCT t.tagged_user_id AS user_id, CONCAT(ud.first_name, ' ', ud.last_name) AS full_name, ud.job_title, ud.profile_pic, ud.organization_id FROM tags t
								INNER JOIN users u ON t.tagged_user_id = u.id
								INNER JOIN user_details ud ON u.id = ud.user_id
								WHERE u.role_id = 2 AND u.status = 1 AND u.is_deleted = 0 AND t.user_id = $user_id AND t.tag IN ('" . implode("', '", $item_1) . "')
								ORDER BY ud.first_name, ud.last_name";
						$sql_3 = "#Select People From: Specific Tags
								SELECT DISTINCT t.tagged_user_id AS user_id FROM tags t
								INNER JOIN users u ON t.tagged_user_id = u.id
								INNER JOIN user_details ud ON u.id = ud.user_id
								WHERE u.role_id = 2 AND u.status = 1 AND u.is_deleted = 0 AND t.user_id = $user_id AND t.tag IN ('" . implode("', '", $item_1) . "')
								ORDER BY ud.first_name, ud.last_name";
					}
					else if($people_from == "skills"){
						$item_1 = implode(",", $item_1);
						$sql_1 = "#Select People From: Specific Skills
								SELECT DISTINCT u.id AS user_id, CONCAT(ud.first_name, ' ', ud.last_name) AS full_name, ud.job_title, ud.profile_pic, ud.organization_id FROM users u
								INNER JOIN user_details ud ON u.id = ud.user_id
								LEFT JOIN user_skills us ON u.id = us.user_id
								WHERE u.role_id = 2 AND u.status = 1 AND u.is_deleted = 0 AND us.skill_id IN ($item_1)
								ORDER BY ud.first_name, ud.last_name";
						$sql_3 = "#Select People From: Specific Skills
								SELECT DISTINCT u.id AS user_id FROM users u
								INNER JOIN user_details ud ON u.id = ud.user_id
								LEFT JOIN user_skills us ON u.id = us.user_id
								WHERE u.role_id = 2 AND u.status = 1 AND u.is_deleted = 0 AND us.skill_id IN ($item_1)
								ORDER BY ud.first_name, ud.last_name";
					}
					else if($people_from == "subjects"){
						$item_1 = implode(",", $item_1);
						$sql_1 = "#Select People From: Specific Subjects
								SELECT DISTINCT u.id AS user_id, CONCAT(ud.first_name, ' ', ud.last_name) AS full_name, ud.job_title, ud.profile_pic, ud.organization_id FROM users u
								INNER JOIN user_details ud ON u.id = ud.user_id
								LEFT JOIN user_subjects us ON u.id = us.user_id
								WHERE u.role_id = 2 AND u.status = 1 AND u.is_deleted = 0 AND us.subject_id IN ($item_1)
								ORDER BY ud.first_name, ud.last_name";
						$sql_3 = "#Select People From: Specific Subjects
								SELECT DISTINCT u.id AS user_id FROM users u
								INNER JOIN user_details ud ON u.id = ud.user_id
								LEFT JOIN user_subjects us ON u.id = us.user_id
								WHERE u.role_id = 2 AND u.status = 1 AND u.is_deleted = 0 AND us.subject_id IN ($item_1)
								ORDER BY ud.first_name, ud.last_name";
					}
					else if($people_from == "domains"){
						$item_1 = implode(",", $item_1);
						$sql_1 = "#Select People From: Specific Domains
								SELECT DISTINCT u.id AS user_id, CONCAT(ud.first_name, ' ', ud.last_name) AS full_name, ud.job_title, ud.profile_pic, ud.organization_id FROM users u
								INNER JOIN user_details ud ON u.id = ud.user_id
								LEFT JOIN user_domains us ON u.id = us.user_id
								WHERE u.role_id = 2 AND u.status = 1 AND u.is_deleted = 0 AND us.domain_id IN ($item_1)
								ORDER BY ud.first_name, ud.last_name";
						$sql_3 = "#Select People From: Specific Domains
								SELECT DISTINCT u.id AS user_id FROM users u
								INNER JOIN user_details ud ON u.id = ud.user_id
								LEFT JOIN user_domains us ON u.id = us.user_id
								WHERE u.role_id = 2 AND u.status = 1 AND u.is_deleted = 0 AND us.domain_id IN ($item_1)
								ORDER BY ud.first_name, ud.last_name";
					}
					else if($people_from == "all_projects"){
						$sql_1 = "#Select People From: All My Projects
								SELECT DISTINCT up1.user_id, CONCAT(ud.first_name, ' ', ud.last_name) AS full_name, ud.job_title, ud.profile_pic, ud.organization_id FROM user_permissions up1
								INNER JOIN users u ON up1.user_id = u.id
								INNER JOIN user_details ud ON u.id = ud.user_id
								INNER JOIN user_permissions up2 ON up1.project_id = up2.project_id AND up2.workspace_id IS NULL
								WHERE up1.workspace_id IS NULL AND up2.user_id = $user_id AND u.role_id = 2 AND u.status = 1 AND u.is_deleted = 0
								ORDER BY ud.first_name, ud.last_name";
						$sql_3 = "#Select People From: All My Projects
								SELECT DISTINCT up1.user_id FROM user_permissions up1
								INNER JOIN users u ON up1.user_id = u.id
								INNER JOIN user_details ud ON u.id = ud.user_id
								INNER JOIN user_permissions up2 ON up1.project_id = up2.project_id AND up2.workspace_id IS NULL
								WHERE up1.workspace_id IS NULL AND up2.user_id = $user_id AND u.role_id = 2 AND u.status = 1 AND u.is_deleted = 0
								ORDER BY ud.first_name, ud.last_name";
					}
					else if($people_from == "created_projects"){
						$sql_1 = "#Select People From: Projects I Created
								SELECT DISTINCT up1.user_id, CONCAT(ud.first_name, ' ', ud.last_name) AS full_name, ud.job_title, ud.profile_pic, ud.organization_id FROM user_permissions up1
								INNER JOIN users u ON up1.user_id = u.id
								INNER JOIN user_details ud ON u.id = ud.user_id
								INNER JOIN user_permissions up2 ON up1.project_id = up2.project_id AND up2.workspace_id IS NULL
								WHERE up1.workspace_id IS NULL AND up2.user_id = $user_id AND up2.role = 'Creator' AND u.role_id = 2 AND u.status = 1 AND u.is_deleted = 0
								ORDER BY ud.first_name, ud.last_name";
						$sql_3 = "#Select People From: Projects I Created
								SELECT DISTINCT up1.user_id FROM user_permissions up1
								INNER JOIN users u ON up1.user_id = u.id
								INNER JOIN user_details ud ON u.id = ud.user_id
								INNER JOIN user_permissions up2 ON up1.project_id = up2.project_id AND up2.workspace_id IS NULL
								WHERE up1.workspace_id IS NULL AND up2.user_id = $user_id AND up2.role = 'Creator' AND u.role_id = 2 AND u.status = 1 AND u.is_deleted = 0
								ORDER BY ud.first_name, ud.last_name";
					}
					else if($people_from == "owner_projects"){
						$sql_1 = "#Select People From: Projects I Own
								SELECT DISTINCT up1.user_id, CONCAT(ud.first_name, ' ', ud.last_name) AS full_name, ud.job_title, ud.profile_pic, ud.organization_id FROM user_permissions up1
								INNER JOIN users u ON up1.user_id = u.id
								INNER JOIN user_details ud ON u.id = ud.user_id
								INNER JOIN user_permissions up2 ON up1.project_id = up2.project_id AND up2.workspace_id IS NULL
								WHERE up1.workspace_id IS NULL AND up2.user_id = $user_id AND up2.role IN ('Creator', 'Owner', 'Group Owner') AND u.role_id = 2 AND u.status = 1 AND u.is_deleted = 0
								ORDER BY ud.first_name, ud.last_name";
						$sql_3 = "#Select People From: Projects I Own
								SELECT DISTINCT up1.user_id FROM user_permissions up1
								INNER JOIN users u ON up1.user_id = u.id
								INNER JOIN user_details ud ON u.id = ud.user_id
								INNER JOIN user_permissions up2 ON up1.project_id = up2.project_id AND up2.workspace_id IS NULL
								WHERE up1.workspace_id IS NULL AND up2.user_id = $user_id AND up2.role IN ('Creator', 'Owner', 'Group Owner') AND u.role_id = 2 AND u.status = 1 AND u.is_deleted = 0
								ORDER BY ud.first_name, ud.last_name";
					}
					else if($people_from == "shared_projects"){
						$sql_1 = "#Select People From: Projects Shared with Me
								SELECT DISTINCT up1.user_id, CONCAT(ud.first_name, ' ', ud.last_name) AS full_name, ud.job_title, ud.profile_pic, ud.organization_id FROM user_permissions up1
								INNER JOIN users u ON up1.user_id = u.id
								INNER JOIN user_details ud ON u.id = ud.user_id
								INNER JOIN user_permissions up2 ON up1.project_id = up2.project_id AND up2.workspace_id IS NULL
								WHERE up1.workspace_id IS NULL AND up2.user_id = $user_id AND up2.role IN ('Sharer', 'Group Sharer') AND u.role_id = 2 AND u.status = 1 AND u.is_deleted = 0
								ORDER BY ud.first_name, ud.last_name";
						$sql_3 = "#Select People From: Projects Shared with Me
								SELECT DISTINCT up1.user_id FROM user_permissions up1
								INNER JOIN users u ON up1.user_id = u.id
								INNER JOIN user_details ud ON u.id = ud.user_id
								INNER JOIN user_permissions up2 ON up1.project_id = up2.project_id AND up2.workspace_id IS NULL
								WHERE up1.workspace_id IS NULL AND up2.user_id = $user_id AND up2.role IN ('Sharer', 'Group Sharer') AND u.role_id = 2 AND u.status = 1 AND u.is_deleted = 0
								ORDER BY ud.first_name, ud.last_name";
					}
					else if($people_from == "project"){
						$item_1 = implode(",", $item_1);
						if(!empty($resourcer)){
							$sql_1 = "#Select People From: Specific Projects - RESOURCER
									SELECT DISTINCT up1.user_id, CONCAT(ud.first_name, ' ', ud.last_name) AS full_name, ud.job_title, ud.profile_pic, ud.organization_id FROM user_permissions up1
									INNER JOIN users u ON up1.user_id = u.id
									INNER JOIN user_details ud ON u.id = ud.user_id
									INNER JOIN user_permissions up2 ON up1.project_id = up2.project_id AND up2.workspace_id IS NULL
									WHERE up1.workspace_id IS NULL AND
									up2.role = 'Creator' AND
									up1.project_id IN ($item_1) AND
									u.role_id = 2 AND u.status = 1 AND u.is_deleted = 0
									ORDER BY ud.first_name, ud.last_name";
							$sql_3 = "#Select People From: Specific Projects - RESOURCER
									SELECT DISTINCT up1.user_id FROM user_permissions up1
									INNER JOIN users u ON up1.user_id = u.id
									INNER JOIN user_details ud ON u.id = ud.user_id
									INNER JOIN user_permissions up2 ON up1.project_id = up2.project_id AND up2.workspace_id IS NULL
									WHERE up1.workspace_id IS NULL AND up2.role = 'Creator' AND up1.project_id IN ($item_1) AND u.role_id = 2 AND u.status = 1 AND u.is_deleted = 0
									ORDER BY ud.first_name, ud.last_name";
						}
						else{
							$sql_1 = "#Select People From: Specific Projects - NOT RESOURCER
									SELECT DISTINCT up1.user_id, CONCAT(ud.first_name, ' ', ud.last_name) AS full_name, ud.job_title, ud.profile_pic, ud.organization_id FROM user_permissions up1
									INNER JOIN users u ON up1.user_id = u.id
									INNER JOIN user_details ud ON u.id = ud.user_id
									INNER JOIN user_permissions up2 ON up1.project_id = up2.project_id AND up2.workspace_id IS NULL
									WHERE up1.workspace_id IS NULL AND up2.user_id = $user_id AND up1.project_id IN ($item_1) AND u.role_id = 2 AND u.status = 1 AND u.is_deleted = 0
									ORDER BY ud.first_name, ud.last_name";
							$sql_3 = "#Select People From: Specific Projects - NOT RESOURCER
									SELECT DISTINCT up1.user_id FROM user_permissions up1
									INNER JOIN users u ON up1.user_id = u.id
									INNER JOIN user_details ud ON u.id = ud.user_id
									INNER JOIN user_permissions up2 ON up1.project_id = up2.project_id AND up2.workspace_id IS NULL
									WHERE up1.workspace_id IS NULL AND up2.user_id = $user_id AND up1.project_id IN ($item_1) AND u.role_id = 2 AND u.status = 1 AND u.is_deleted = 0
									ORDER BY ud.first_name, ud.last_name";
						}
						/*$sql_1 = "#Select People From: Specific Projects
								SELECT DISTINCT up1.user_id, CONCAT(ud.first_name, ' ', ud.last_name) AS full_name, ud.job_title, ud.profile_pic, ud.organization_id FROM user_permissions up1
								INNER JOIN users u ON up1.user_id = u.id
								INNER JOIN user_details ud ON u.id = ud.user_id
								INNER JOIN user_permissions up2 ON up1.project_id = up2.project_id AND up2.workspace_id IS NULL
								WHERE up1.workspace_id IS NULL AND up2.user_id = $user_id AND up1.project_id IN ($item_1) AND u.role_id = 2 AND u.status = 1 AND u.is_deleted = 0
								ORDER BY ud.first_name, ud.last_name";
						$sql_3 = "#Select People From: Specific Projects
								SELECT DISTINCT up1.user_id FROM user_permissions up1
								INNER JOIN users u ON up1.user_id = u.id
								INNER JOIN user_details ud ON u.id = ud.user_id
								INNER JOIN user_permissions up2 ON up1.project_id = up2.project_id AND up2.workspace_id IS NULL
								WHERE up1.workspace_id IS NULL AND up2.user_id = $user_id AND up1.project_id IN ($item_1) AND u.role_id = 2 AND u.status = 1 AND u.is_deleted = 0
								ORDER BY ud.first_name, ud.last_name";*/
					}
				}
				if(isset($date_type) && !empty($date_type)){
					if($date_type == 'daily'){
						$end_date = date('Y-m-d', strtotime($dates. ' + 31 days'));
					}
					elseif($date_type == 'weekly'){
						$end_date = date('Y-m-d', strtotime($dates. ' + 28 days'));
					}
					elseif($date_type == 'monthly'){
						$effectiveDate = date('Y-m-d', strtotime( $dates ." + 2 months"));
						$end_date = date("Y-m-t", strtotime($effectiveDate));
					}

					$sql_2 = "CROSS JOIN (SELECT '$start_date' AS start_date, '$end_date' AS end_date) CONST";
					$sql_10 = "AND (DATE(e.start_date) <= DATE('$end_date') AND DATE(e.end_date) >= DATE('$start_date'))";

					$sql_19 = "IF(DATE(MIN(e.start_date)) < DATE('$start_date'), DATE(MIN(e.start_date)), DATE('$start_date')) AS start_date";
					$sql_20 = "IF(DATE(MAX(e.end_date)) > DATE('$end_date'), DATE(MAX(e.end_date)), DATE('$end_date')) AS end_date";

					$sql_21 = "(DATE(e.start_date) <= DATE('$start_date') AND DATE(e.end_date) >= DATE('$end_date'))";

				}
				if(isset($adjustments) && !empty($adjustments)){
					$sql_5 = "IF(pe.remaining_hours IS NULL, IFNULL(ee1.completed_hours,0) + IFNULL(ee1.remaining_hours,0), IFNULL(ee1.completed_hours,0) + pe.remaining_hours) AS element_effort";
					$sql_6 = "IF(pe.remaining_hours IS NOT NULL,1,0)  AS element_adjustments";
					$sql_22 = "AND (ee1.id IS NOT NULL OR pe.id IS NOT NULL)";
					$sql_11 = "UNION ALL
                            SELECT
                                pe.user_id,
                                pe.element_id,
                                DATE(e.start_date) AS start_date,
                                DATE(e.end_date) AS end_date
                            FROM
                                plan_efforts pe
                            INNER JOIN elements e ON
                                pe.element_id = e.id
                                AND e.start_date IS NOT NULL
                            WHERE
                            	";
                	$sql_11 .= "(DATE(e.start_date) <= DATE('$end_date') AND DATE(e.end_date) >= DATE('$start_date'))";
                	if($work_from == "project"){
                		$item_2_str = implode(",", $item_2);
                		$sql_11 .= "AND pe.project_id IN ($item_2_str) # ONLY INCLUDE SPECIFIC PROJECTS ARE SELECTED";
                	}

            		$sql_14 = "LEFT JOIN plan_efforts pe ON
	                    	u_tasks.user_id = pe.user_id
	                        AND u_tasks.element_id = pe.element_id";

				}
				else{
					$sql_5 = "IFNULL(ee1.completed_hours,0) + IFNULL(ee1.remaining_hours,0) AS element_effort";
					$sql_6 = "0 AS element_adjustments";
					$sql_22 = "AND ee1.id IS NOT NULL";
				}

				if($work_from == "project"){
					// pr($item_2,1);
            		$item_2_s = implode(",", $item_2);
            		$sql_9 = "AND pe.project_id IN ($item_2_s) # ONLY INCLUDE SPECIFIC PROJECTS ARE SELECTED";
            		$sql_15 = "AND up.project_id IN ($item_2_s) # ONLY INCLUDE SPECIFIC PROJECTS ARE SELECTED";
            	}

            	// CREATE SELECT FIELDS LIST
				$sel = [];
				// daily
				if($date_type == 'daily'){
					$daily_start = $start_date;
					for($i = 1; $i <= 31; $i++){
						$daily_day = date('d', strtotime($daily_start));
						$sel[] = "GROUP_CONCAT((CASE WHEN DATE_FORMAT(util.the_date, '%Y-%m-%d') = '$daily_start' THEN util.the_date ELSE NULL END)) AS '".$i."',
					    ROUND(SUM(CASE WHEN DATE_FORMAT(util.the_date, '%Y-%m-%d') = '$daily_start' THEN util.availability ELSE NULL END),1) AS '".$i."_da',
					    ROUND(SUM(CASE WHEN DATE_FORMAT(util.the_date, '%Y-%m-%d') = '$daily_start' THEN util.effort ELSE 0 END),2) AS '".$i."_de',
					    IF(SUM(CASE WHEN DATE_FORMAT(util.the_date, '%Y-%m-%d') = '$daily_start' THEN util.adjustments ELSE 0 END) > 0, 1, 0) AS '".$i."_ad',
					    SUM(CASE WHEN DATE_FORMAT(util.the_date, '%Y-%m-%d') = '$daily_start' THEN util.absence_count ELSE NULL END) AS '".$i."_ab',
					    SUM(CASE WHEN DATE_FORMAT(util.the_date, '%Y-%m-%d') = '$daily_start' THEN util.block_count ELSE NULL END) AS '".$i."_wb'";
						$date = date('Y-m-d', strtotime($daily_start.' +1 day'));
						$daily_start = $date;
					}
					$sel_str = '';
					if(isset($sel) && !empty($sel)){
						$sel_str = implode(',', $sel);
					}
				}
				// weekly
				else if($date_type == 'weekly'){
					$startTime = strtotime($start_date);
					$endTime = strtotime($end_date);
					$i = 1;
					while ($startTime < $endTime) {
					    $week_start = date('Y-m-d', $startTime);
					    $week_end = date('Y-m-d', strtotime($week_start. ' +6 days'));
					    $sel[] = "GROUP_CONCAT(DISTINCT(CASE WHEN DATE(util.the_date) BETWEEN DATE('$week_start') AND DATE('$week_end') THEN '$week_start' ELSE NULL END)) AS '0".$i."_dt',
						    ROUND(SUM(CASE WHEN DATE(util.the_date) BETWEEN DATE('$week_start') AND DATE('$week_end') THEN util.availability ELSE NULL END),1) AS '0".$i."_da',
						    ROUND(SUM(CASE WHEN DATE(util.the_date) BETWEEN DATE('$week_start') AND DATE('$week_end') THEN util.effort ELSE 0 END),2) AS '0".$i."_de',
						    IF(SUM(CASE WHEN DATE(util.the_date) BETWEEN DATE('$week_start') AND DATE('$week_end') THEN util.adjustments ELSE 0 END) > 0, 1, 0) AS '0".$i."_ad',
						    IF(SUM(CASE WHEN DATE(util.the_date) BETWEEN DATE('$week_start') AND DATE('$week_end') THEN util.absence_count ELSE 0 END) > 0, 1, 0) AS '0".$i."_ab',
						    IF(SUM(CASE WHEN DATE(util.the_date) BETWEEN DATE('$week_start') AND DATE('$week_end') THEN util.block_count ELSE 0 END) > 0, 1, 0) AS '0".$i."_wb'";
					    $startTime += strtotime('+1 week', 0);
					    $i++;
					}
					$sel_str = '';
					if(isset($sel) && !empty($sel)){
						$sel_str = implode(',', $sel);
					}
				}
				// monthly
				else if($date_type == 'monthly'){
					$startTime = strtotime($start_date);
					$endTime = strtotime($end_date);
					$i = 1;
					while ($startTime <= $endTime) {
					    $month_days = cal_days_in_month(CAL_GREGORIAN, date('n', ($startTime)), date('Y', ($startTime)));
					    $month_start = date("Y-m-d", $startTime);
					    $month_end = date("Y-m-t", $startTime);
					    $sel[] = "GROUP_CONCAT(DISTINCT(CASE WHEN DATE(util.the_date) BETWEEN DATE('$month_start') AND DATE('$month_end') THEN '$month_start' ELSE NULL END)) AS '0".$i."_dt',
						    ROUND(SUM(CASE WHEN DATE(util.the_date) BETWEEN DATE('$month_start') AND DATE('$month_end') THEN util.availability ELSE NULL END),2) AS '0".$i."_da',
						    ROUND(SUM(CASE WHEN DATE(util.the_date) BETWEEN DATE('$month_start') AND DATE('$month_end') THEN util.effort ELSE 0 END),2) AS '0".$i."_de',
						    IF(SUM(CASE WHEN DATE(util.the_date) BETWEEN DATE('$month_start') AND DATE('$month_end') THEN util.adjustments ELSE 0 END) > 0, 1, 0) AS '0".$i."_ad',
						    IF(SUM(CASE WHEN DATE(util.the_date) BETWEEN DATE('$month_start') AND DATE('$month_end') THEN util.absence_count ELSE 0 END) > 0, 1, 0) AS '0".$i."_ab',
						    IF(SUM(CASE WHEN DATE(util.the_date) BETWEEN DATE('$month_start') AND DATE('$month_end') THEN util.block_count ELSE 0 END) > 0, 1, 0) AS '0".$i."_wb'";

					    $startTime += strtotime('+'.$month_days.' days', 0);
					    $i++;
					}
					$sel_str = '';
					if(isset($sel) && !empty($sel)){
						$sel_str = implode(',', $sel);
					}
				}




				$main_query = "SELECT
						    util.user_id, util.full_name, util.job_title, util.profile_pic, util.organization_id, $sel_str
						FROM
						(
						    SELECT
						        user_dates.user_id,
						        user_dates.full_name,
						        user_dates.job_title,
						        user_dates.profile_pic,
						        user_dates.organization_id,
						        user_dates.the_date,
						        IFNULL(user_tasks.availability,0) AS availability,
						        IFNULL(user_tasks.effort,0) AS effort,
						        user_tasks.adjustments,
						        user_dates.absence_count,
						        user_dates.block_count
						    FROM
						    (
						        SELECT
						            u.user_id, u.full_name, u.job_title, u.profile_pic, u.organization_id, td.the_date, COUNT(av.id) AS absence_count, COUNT(ub.id) AS block_count
						        FROM
						        (
						            (
						                # CHANGE THIS SUB QUERY BASED ON SELECTED USERS IN UI (SEE ADDITIONAL SQL FILE 1 - SELECT PEOPLE FROM) sql_1
						                $sql_1
						                #-----
						                $limit_str #SET LIMIT AS REQUIRED FOR PAGING
						            ) AS u
						            CROSS JOIN
						            (
						                SELECT start_date + INTERVAL num DAY AS the_date
						                FROM
						                (
						                    SELECT digit_1.d + 10 * digit_2.d AS num
						                    FROM (SELECT 0 as d UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) digit_1
						                    CROSS JOIN (SELECT 0 as d UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) digit_2
						                ) digits
						                $sql_2 # CHANGE DATE RANGE BASED ON DAILY, WEEKLY, MONTHLY UI SELECTION AND USER DATE UI SELECTION sql_2
						                WHERE start_date + INTERVAL num DAY <= end_date
						            ) AS td
						            LEFT JOIN availabilities av ON
						                u.user_id = av.user_id
						                AND (DATE(STR_TO_DATE(LEFT(av.avail_start_date,10),'%Y-%m-%d')) <= td.the_date AND DATE(STR_TO_DATE(LEFT(av.avail_end_date,10),'%Y-%m-%d')) >= td.the_date)
						            LEFT JOIN user_blocks ub ON
						                u.user_id = ub.user_id
						                AND (DATE(ub.work_start_date) <= td.the_date AND DATE(ub.work_end_date) >= td.the_date)
						        )
						        GROUP BY u.user_id, td.the_date
						    ) AS user_dates
						    LEFT JOIN
						    (

						        SELECT
						            user_tasks.user_id,
						            user_tasks.the_date,
						            user_tasks.availability,
						            SUM(user_tasks.day_effort) AS effort,
						            IF(SUM(user_tasks.element_adjustments) > 0, 1, 0) AS adjustments
						        FROM
						        (

						            SELECT
						                user_data.user_id,
						                user_data.the_date,
						                user_data.availability,
						                task_data.element_id,
						                task_data.element_effort,
						                task_data.element_adjustments,
						                IF(task_data.total_availability = 0, ROUND(task_data.element_effort / task_data.duration, 2)
						                    , ROUND(task_data.element_effort * user_data.availability / task_data.total_availability, 2)
						                 ) AS day_effort
						            FROM
						            (
						                SELECT
						                    u.user_id, td.the_date,
						                    (CASE
						                     WHEN WEEKDAY(td.the_date) = 0 THEN IFNULL(ua1.monday,0)
						                     WHEN WEEKDAY(td.the_date) = 1 THEN IFNULL(ua1.tuesday,0)
						                     WHEN WEEKDAY(td.the_date) = 2 THEN IFNULL(ua1.wednesday,0)
						                     WHEN WEEKDAY(td.the_date) = 3 THEN IFNULL(ua1.thursday,0)
						                     WHEN WEEKDAY(td.the_date) = 4 THEN IFNULL(ua1.friday,0)
						                     WHEN WEEKDAY(td.the_date) = 5 THEN IFNULL(ua1.saturday,0)
						                     WHEN WEEKDAY(td.the_date) = 6 THEN IFNULL(ua1.sunday,0)
						                     ELSE 0 END) AS availability
						                FROM
						                (
						                    (
						                        # CHANGE THIS SUB QUERY BASED ON SELECTED USERS IN UI (SEE ADDITIONAL SQL FILE 2 - SELECT PEOPLE FROM) sql_3
						                        $sql_3
						                        #-----
						                        $limit_str #SET LIMIT AS REQUIRED FOR PAGING
						                    ) AS u
						                    CROSS JOIN
						                    (
						                        SELECT start_date + INTERVAL num DAY AS the_date
						                        FROM
						                        (
						                            SELECT digit_1.d + 10 * digit_2.d AS num
						                            FROM (SELECT 0 as d UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) digit_1
						                            CROSS JOIN (SELECT 0 as d UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) digit_2
						                        ) digits
						                        $sql_2 # CHANGE DATE RANGE BASED ON DAILY, WEEKLY, MONTHLY UI SELECTION AND USER DATE UI SELECTION sql_2
						                        WHERE start_date + INTERVAL num DAY <= end_date
						                    ) AS td
						                    LEFT JOIN user_availabilities ua1 ON
						                        u.user_id = ua1.user_id
						                        AND DATE(ua1.effective) <= td.the_date
						                    LEFT JOIN user_availabilities ua2 ON
						                        DATE(ua2.effective) <= td.the_date
						                        AND ua1.user_id = ua2.user_id
						                        AND (ua1.effective < ua2.effective)
						                )
						                WHERE ua2.effective IS NULL
						                GROUP BY u.user_id, td.the_date
						            ) AS user_data
						            LEFT JOIN
						            (

						                SELECT
						                    u.user_id,
						                    td.the_date,
						                    u_tasks.element_id,
						                    # IF ADJUSTMENTS IS CHECKED IN UI BY USER THEN USE THIS LINE: sql_5
						                    $sql_5,
						                    # ELSE IF AJUSTMENTS NOT CHECKED IN UI BY USER THEN USE THIS LINE:
						                    #IFNULL(ee1.completed_hours,0) + IFNULL(ee1.remaining_hours,0) AS element_effort,

						                    # IF ADJUSTMENTS IS CHECKED IN UI BY USER THEN USE THIS LINE: sql_6
						                    $sql_6,
						                    # ELSE IF AJUSTMENTS NOT CHECKED IN UI BY USER THEN USE THIS LINE:
						                    #0 AS element_adjustments,
						                    element_dur_av.duration,
						                    element_dur_av.total_availability
						                FROM
						                    (
						                        # CHANGE THIS SUB QUERY BASED ON SELECTED USERS IN UI (SEE ADDITIONAL SQL FILE 2 - SELECT PEOPLE FROM) sql_3
						                        $sql_3
						                        #-----
						                        $limit_str #SET LIMIT AS REQUIRED FOR PAGING
						                    ) AS u
						                    CROSS JOIN
						                    (
						                        SELECT start_date + INTERVAL num DAY AS the_date
						                        FROM
						                        (
						                            SELECT digit_1.d + 10 * digit_2.d AS num
						                            FROM (SELECT 0 as d UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) digit_1
						                            CROSS JOIN (SELECT 0 as d UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) digit_2
						                        ) digits
						                        $sql_2 # CHANGE DATE RANGE BASED ON DAILY, WEEKLY, MONTHLY UI SELECTION AND USER DATE UI SELECTION sql_2
						                        WHERE start_date + INTERVAL num DAY <= end_date
						                    ) AS td
						                    LEFT JOIN
						                    (
						                        SELECT DISTINCT
						                            at.user_id,
						                            at.element_id,
						                            DATE(at.start_date) AS start_date,
						                            DATE(at.end_date) AS end_date
						                        FROM
						                        (
						                            SELECT
						                                up.user_id,
						                                up.element_id,
						                                DATE(e.start_date) AS start_date,
						                                DATE(e.end_date) AS end_date
						                            FROM
						                                user_permissions up
						                            INNER JOIN elements e ON
						                                up.element_id = e.id
						                                AND e.start_date IS NOT NULL
						                            WHERE
						                                up.element_id IS NOT NULL
						                                $sql_15 # ONLY INCLUDE SPECIFIC PROJECTS ARE SELECTED sql_15

						                                $sql_10 # CHANGE DATE RANGE BASED ON DAILY, WEEKLY, MONTHLY UI SELECTION AND USER DATE UI SELECTION sql_10

						                            #----- IF ADJUSTMENTS IS CHECKED IN UI BY USER THEN INCLUDE THIS SECTION: sql_11
						                            $sql_11
						                            #----- END OF SECTION
						                        ) AS at
						                    ) AS u_tasks ON
						                        u.user_id = u_tasks.user_id
						                        AND (DATE(u_tasks.start_date) <= td.the_date AND DATE(u_tasks.end_date) >= td.the_date)
						                    LEFT JOIN element_efforts ee1 ON
						                        u_tasks.user_id = ee1.user_id
						                        AND u_tasks.element_id = ee1.element_id
						                        AND DATE(ee1.created) <= td.the_date
						                    LEFT JOIN element_efforts ee2 ON
						                        ee1.user_id = ee2.user_id
						                        AND ee1.element_id = ee2.element_id
						                        AND (ee1.created < ee2.created)
						                        AND DATE(ee2.created) <= td.the_date
						                    #----- IF ADJUSTMENTS IS CHECKED IN UI BY USER THEN INCLUDE THIS SECTION: sql_14
						                    $sql_14
						                    #----- END OF SECTION
						                    LEFT JOIN
						                    (
						                            SELECT
						                                u_tasks.user_id,
						                                u_tasks.element_id,
						                                (DATEDIFF(u_tasks.end_date, u_tasks.start_date)+1) AS duration,
						                                SUM(u_availability.availability) AS total_availability
						                            FROM
						                            (
						                                SELECT DISTINCT
						                                    at.user_id,
						                                    at.element_id,
						                                    DATE(at.start_date) AS start_date,
						                                    DATE(at.end_date) AS end_date
						                                FROM
						                                (
						                                    SELECT
						                                        up.user_id,
						                                        up.element_id,
						                                        DATE(e.start_date) AS start_date,
						                                        DATE(e.end_date) AS end_date
						                                    FROM
						                                        user_permissions up
						                                    INNER JOIN elements e ON
						                                        up.element_id = e.id
						                                        AND e.start_date IS NOT NULL
						                                    WHERE
						                                        up.element_id IS NOT NULL
						                                        $sql_15 # ONLY INCLUDE SPECIFIC PROJECTS ARE SELECTED sql_15
						                                        $sql_10 # CHANGE DATE RANGE BASED ON DAILY, WEEKLY, MONTHLY UI SELECTION AND USER DATE UI SELECTION sql_10
						                                    UNION ALL
						                                    SELECT
						                                        pe.user_id,
						                                        pe.element_id,
						                                        DATE(e.start_date) AS start_date,
						                                        DATE(e.end_date) AS end_date
						                                    FROM
						                                        plan_efforts pe
						                                    INNER JOIN elements e ON
						                                        pe.element_id = e.id
						                                        AND e.start_date IS NOT NULL
						                                    WHERE
						                                        $sql_21 # CHANGE DATE RANGE BASED ON DAILY, WEEKLY, MONTHLY UI SELECTION AND USER DATE UI SELECTION sql_21

						                                        $sql_9 # ONLY INCLUDE SPECIFIC PROJECTS ARE SELECTED sql_9
						                                ) AS at
						                             ) AS u_tasks
						                             LEFT JOIN
						                             (
						                                SELECT
						                                    u.user_id,
						                                    td.the_date,
						                                    (CASE
						                                     WHEN WEEKDAY(td.the_date) = 0 THEN IFNULL(ua1.monday,0)
						                                     WHEN WEEKDAY(td.the_date) = 1 THEN IFNULL(ua1.tuesday,0)
						                                     WHEN WEEKDAY(td.the_date) = 2 THEN IFNULL(ua1.wednesday,0)
						                                     WHEN WEEKDAY(td.the_date) = 3 THEN IFNULL(ua1.thursday,0)
						                                     WHEN WEEKDAY(td.the_date) = 4 THEN IFNULL(ua1.friday,0)
						                                     WHEN WEEKDAY(td.the_date) = 5 THEN IFNULL(ua1.saturday,0)
						                                     WHEN WEEKDAY(td.the_date) = 6 THEN IFNULL(ua1.sunday,0)
						                                     ELSE 0 END) AS availability
						                                FROM
						                                (
						                                    (
						                                        # CHANGE THIS SUB QUERY BASED ON SELECTED USERS IN UI (SEE ADDITIONAL SQL FILE 2 - SELECT PEOPLE FROM) sql_3
						                                        $sql_3
						                                        #-----
						                                        $limit_str #SET LIMIT AS REQUIRED FOR PAGING
						                                    ) AS u
						                                    CROSS JOIN
						                                    (
						                                        SELECT start_date + INTERVAL num DAY AS the_date
						                                        FROM
						                                        (
						                                            SELECT digit_1.d + 10 * digit_2.d + 100 * digit_3.d + 1000 * digit_4.d AS num
						                                            FROM (SELECT 0 as d UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) digit_1
						                                            CROSS JOIN (SELECT 0 as d UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) digit_2
						                                            CROSS JOIN (SELECT 0 as d UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) digit_3
						                                            CROSS JOIN (SELECT 0 as d UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) digit_4
						                                        ) digits
						                                        CROSS JOIN
						                                        (
						                                            SELECT
						                                                $sql_19, # CHANGE DATE RANGE BASED ON DAILY, WEEKLY, MONTHLY UI SELECTION AND USER DATE UI SELECTION sql_19
						                                                $sql_20 # CHANGE DATE RANGE BASED ON DAILY, WEEKLY, MONTHLY UI SELECTION AND USER DATE UI SELECTION sql_20
						                                            FROM
						                                                elements e
						                                            WHERE
						                                                e.start_date IS NOT NULL
						                                                $sql_10 # CHANGE DATE RANGE BASED ON DAILY, WEEKLY, MONTHLY UI SELECTION AND USER DATE UI SELECTION sql_10
						                                        ) CONST
						                                        WHERE start_date + INTERVAL num DAY <= end_date
						                                    ) AS td
						                                    LEFT JOIN user_availabilities ua1 ON
						                                        u.user_id = ua1.user_id
						                                        AND DATE(ua1.effective) <= td.the_date
						                                    LEFT JOIN user_availabilities ua2 ON
						                                        DATE(ua2.effective) <= td.the_date
						                                        AND ua1.user_id = ua2.user_id
						                                        AND (ua1.effective < ua2.effective)
						                                 )
						                                 WHERE ua2.effective IS NULL
						                                 GROUP BY u.user_id, td.the_date
						                             ) AS u_availability ON
						                                u_tasks.user_id = u_availability.user_id
						                                AND (DATE(u_tasks.start_date) <= u_availability.the_date AND DATE(u_tasks.end_date) >= u_availability.the_date)
						                            GROUP BY
						                                u_tasks.user_id, u_tasks.element_id
						                    ) AS element_dur_av ON
						                        u_tasks.user_id = element_dur_av.user_id
						                        AND u_tasks.element_id = element_dur_av.element_id
						                    WHERE
						                        ee2.id IS NULL
						                        # IF ADJUSTMENTS IS CHECKED IN UI BY USER THEN USE THIS LINE: sql_22
						                        $sql_22
						                        # ELSE IF AJUSTMENTS NOT CHECKED IN UI BY USER THEN USE THIS LINE:
						                        #AND ee1.id IS NOT NULL
						                    GROUP BY u.user_id, td.the_date, u_tasks.element_id
						            ) AS task_data ON
						                user_data.user_id = task_data.user_id
						                AND user_data.the_date = task_data.the_date
						        ) AS user_tasks
						        GROUP BY
						            user_tasks.user_id, user_tasks.the_date
						    ) AS user_tasks ON
						        user_dates.user_id = user_tasks.user_id
						        AND user_dates.the_date = user_tasks.the_date
						) AS util
						GROUP BY util.user_id
						ORDER BY util.full_name";
				$data = $this->UserPermission->query($main_query);
			}
        }

        $this->set("date_type", $date_type);
        $this->set("section", $section);
        $this->set("data", $data);
		$this->render('/Searches/planning/utilization_section');
    }

	public function add_adjustment($pe_id = null, $usr_id = null, $prj_id = null, $ws_id = null, $el_id = null) {
		$response = ['success' => false];
		if ($this->request->isAjax()) {

			$response = ['success' => false];

			$this->layout = 'ajax';
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				if(isset($post['project_id']) && !empty($post['project_id'])) {
					$this->loadModel('PlanEffort');
					$project_id = $post['project_id'];
					$workspace_id = $post['workspace_id'];
					$task_id = $post['task_id'];
					$user = $post['user'];
					$pe_remaining = $post['pe_remaining'];
					$pe_comment = Sanitize::escape($post['pe_comment']);
					$created_by = $this->Session->read('Auth.User.id');
					$created = date('Y-m-d h:i:s');
					$d = $this->PlanEffort->query("SELECT count(*) AS exist FROM plan_efforts WHERE project_id = $project_id AND workspace_id = $workspace_id AND element_id = $task_id AND user_id = $user");
					$pe_exists = (isset($d[0][0]['exist']) && !empty($d[0][0]['exist'])) ? true : false;
					if($pe_exists){
						$this->PlanEffort->query("UPDATE plan_efforts SET project_id = $project_id, workspace_id = $workspace_id, element_id = $task_id, user_id = $user, remaining_hours = $pe_remaining, comment = '$pe_comment' WHERE project_id = $project_id AND workspace_id = $workspace_id AND element_id = $task_id AND user_id = $user");
						$response['success'] = true;
					}
					else{
						$this->PlanEffort->query("INSERT INTO plan_efforts SET project_id = $project_id, workspace_id = $workspace_id, element_id = $task_id, user_id = $user, remaining_hours = $pe_remaining, comment = '$pe_comment', created_by = $created_by, created = '$created'");
						$response['success'] = true;
					}
				}
				echo json_encode($response);
				exit();
			}

			$data = $this->User->query("SELECT
										    DISTINCT(p.id) AS id,
										    p.title AS title
										FROM
										    user_permissions up
										LEFT JOIN projects p ON
										    up.project_id = p.id
										WHERE
										    up.workspace_id IS NULL
									    ORDER BY p.title ASC"
									);
			$project_list = [];
			if(isset($data) && !empty($data)){
				$data = Set::combine($data, '{n}.p.id', '{n}.p.title');
				foreach ($data as $key => $value) {
					$project_list[$key] = $value;//htmlentities($value, ENT_QUOTES, "UTF-8");
				}
			}

			$user_list = [];
			$data = $this->User->query("SELECT DISTINCT(ud.user_id), CONCAT_WS(' ',ud.first_name , ud.last_name) AS title
			                           	FROM users u
			                           	INNER JOIN user_details ud ON u.id = ud.user_id
			                           	WHERE u.role_id = 2 AND u.status = 1 AND u.is_activated = 1 AND u.is_deleted = 0
			                           	ORDER BY ud.first_name ASC, ud.last_name ASC
		                           ");
			if(isset($data) && !empty($data)){
				foreach ($data as $key => $value) {
					$user_list[$value['ud']['user_id']] = $value[0]['title'];
				}
			}

			$pe_data = [];
			if(isset($pe_id) && !empty($pe_id)){
				$pe_data = $this->User->query("SELECT *
			                           	FROM plan_efforts pe
			                           	WHERE id = $pe_id
		                           ");
				if(isset($pe_data) && !empty($pe_data)){
					$pe_data = $pe_data[0]['pe'];
				}
			}
			if((isset($usr_id) && !empty($usr_id)) && (isset($prj_id) && !empty($prj_id)) && (isset($ws_id) && !empty($ws_id)) && (isset($el_id) && !empty($el_id))){
				$pe_data = $this->User->query("SELECT *
			                           	FROM element_efforts pe
			                           	WHERE
			                           		user_id = $usr_id AND
			                           		project_id = $prj_id AND
			                           		workspace_id = $ws_id AND
			                           		element_id = $el_id
		                           ");
				if(isset($pe_data) && !empty($pe_data)){
					$pe_data = $pe_data[0]['pe'];
				}
			}

			$this->set('pe_data', $pe_data);
			$this->set('project_list', $project_list);
			$this->set('user_list', $user_list);
			$this->render('/Searches/planning/add_adjustment');
		}
	}

	public function get_related_data(){
		if ($this->request->isAjax()) {
            $this->layout = 'ajax';
            $response = ['success' => false, 'content' => [], 'selection' => []];
            $details = [];
            $user_id = $this->Session->read('Auth.User.id');

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;

				if( isset($post['type']) && !empty($post['type']) ){
					$type = $post['type'];
					$id = $post['id'];
					if($type == 'workspace'){
						$data = $this->User->query("SELECT wsp.id, wsp.title
						                           	FROM user_permissions up
						                           	LEFT JOIN workspaces wsp ON
										    			up.workspace_id = wsp.id
						                           	WHERE
						                           		up.project_id = $id AND
						                           		up.workspace_id IS NOT NULL AND
						                           		up.area_id IS NULL AND
						                           		up.role = 'Creator'
						                           	ORDER BY wsp.title ASC
					                           ");
						if(isset($data) && !empty($data)){
							foreach ($data as $key => $value) {
								$details[] = ['value' => $value['wsp']['id'], 'label' => $value['wsp']['title']];
							}
						}
						$response['success'] = true;
					}
					else if($type == 'task'){

						$data = $this->User->query("SELECT task.id, task.title
						                           	FROM user_permissions up
						                           	LEFT JOIN elements task ON
										    			up.element_id = task.id
						                           	WHERE
						                           		up.workspace_id = $id AND
						                           		up.element_id IS NOT NULL AND
						                           		up.role = 'Creator' AND
						                           		(
						                           			(DATE(NOW()) < DATE(task.start_date) and task.sign_off!=1 and task.date_constraints=1) OR
						                           			(DATE(NOW()) BETWEEN DATE(task.start_date) AND DATE(task.end_date) and task.sign_off!=1 and task.date_constraints=1 ) OR
						                           			(DATE(task.end_date) < DATE(NOW()) and task.sign_off!=1 and task.date_constraints=1)
						                           		)
						                           	ORDER BY task.title ASC
					                           ");
						if(isset($data) && !empty($data)){
							foreach ($data as $key => $value) {
								$details[] = ['value' => $value['task']['id'], 'label' => $value['task']['title']];
							}
						}
						$response['success'] = true;
					}
					else if($type == 'user'){

						$data = $this->User->query("SELECT DISTINCT(ud.user_id), CONCAT_WS(' ',ud.first_name , ud.last_name) AS title
						                           	FROM user_permissions up
						                           	LEFT JOIN users u ON
										    			up.user_id = u.id
						                           	LEFT JOIN user_details ud ON
										    			up.user_id = ud.user_id
						                           	WHERE
						                           		up.element_id = $id AND u.role_id = 2 AND u.status = 1 AND u.is_activated = 1 AND u.is_deleted = 0
						                           	ORDER BY ud.first_name ASC, ud.last_name ASC
					                           ");
						if(isset($data) && !empty($data)){
							foreach ($data as $key => $value) {
								$details[] = ['value' => $value['ud']['user_id'], 'label' => $value[0]['title']];
							}
						}
						$response['success'] = true;
					}
				}
			}

			$response['content'] = $details;
			echo json_encode($response);
            exit;
		}
	}

	public function get_effort_plan(){
		if ($this->request->isAjax()) {
            $this->layout = 'ajax';
            $response = ['success' => false, 'content' => [], 'selection' => []];
            $details = [];
            $user_id = $this->Session->read('Auth.User.id');

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				$project_id = $post['project_id'];
				$workspace_id = $post['workspace_id'];
				$task_id = $post['task_id'];
				$member_id = $post['member_id'];
				$edata = $this->User->query("SELECT ef.id, ef.completed_hours, ef.remaining_hours, ef.comment
				                           	FROM element_efforts ef
				                           	WHERE
				                           		ef.project_id = $project_id AND
				                           		ef.workspace_id = $workspace_id AND
				                           		ef.element_id = $task_id AND
				                           		ef.user_id = $member_id AND
				                           		ef.is_active = 1
			                           ");
				$pedata = $this->User->query("SELECT ef.id, ef.remaining_hours, ef.comment
				                           	FROM plan_efforts ef
				                           	WHERE
				                           		ef.project_id = $project_id AND
				                           		ef.workspace_id = $workspace_id AND
				                           		ef.element_id = $task_id AND
				                           		ef.user_id = $member_id
			                           ");
				if(isset($edata) && !empty($edata)){
					$details['el_efforts'] = $edata[0]['ef'];
					$response['success'] = true;
				}
				if(isset($pedata) && !empty($pedata)){
					$details['pe_efforts'] = $pedata[0]['ef'];
					$response['success'] = true;
				}
			}

			$response['content'] = $details;
			echo json_encode($response);
            exit;
		}
	}

	public function adjustments() {
		$response = ['success' => false];
		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$this->loadModel('PlanEffort');
			$data = $this->PlanEffort->query("SELECT pe.*,
			                                 	prj.title AS pname,
			                                 	wsp.title AS wname,
			                                 	task.title as ename,
			                                 	CONCAT_WS(' ',ud.first_name , ud.last_name) AS username,
			                                 	ud.profile_pic,
			                                 	ud.organization_id,
			                                 	ud.job_title,
			                                 	CONCAT_WS(' ',ud1.first_name , ud1.last_name) AS creator
						FROM `plan_efforts` pe
						LEFT JOIN user_details ud ON ud.user_id = pe.user_id
						LEFT JOIN user_details ud1 ON ud1.user_id = pe.created_by
						LEFT JOIN projects prj ON prj.id = pe.project_id
						LEFT JOIN workspaces wsp ON wsp.id = pe.workspace_id
						LEFT JOIN elements task ON task.id = pe.element_id
						ORDER BY ud.first_name ASC, ud.last_name ASC
					");
			$this->set('data', $data);
			$this->render('/Searches/planning/adjustments');
		}
	}

	public function adjustment_list() {
		$response = ['success' => false];
		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				$order = "ORDER BY ud.first_name ASC, ud.last_name ASC";
				if((isset($post['column']) && !empty($post['column'])) && (isset($post['direction']) && !empty($post['direction']))){
					$column = $post['column'];
					$direction = $post['direction'];
					if($column == 'first_name'){
						$order = "ORDER BY ud.first_name $direction, ud.last_name $direction";
					}
					else if($column == 'last_name'){
						$order = "ORDER BY ud.last_name $direction, ud.first_name $direction";
					}
					else{
						$order = "ORDER BY $column $direction";
					}
				}
				$this->loadModel('PlanEffort');
				$qry = "SELECT pe.*,
				                                 	prj.title AS pname,
				                                 	wsp.title AS wname,
				                                 	task.title as ename,
				                                 	CONCAT_WS(' ',ud.first_name , ud.last_name) AS username,
				                                 	ud.profile_pic,
				                                 	ud.organization_id,
				                                 	ud.job_title,
				                                 	CONCAT_WS(' ',ud1.first_name , ud1.last_name) AS creator
							FROM `plan_efforts` pe
							LEFT JOIN user_details ud ON ud.user_id = pe.user_id
							LEFT JOIN user_details ud1 ON ud1.user_id = pe.created_by
							LEFT JOIN projects prj ON prj.id = pe.project_id
							LEFT JOIN workspaces wsp ON wsp.id = pe.workspace_id
							LEFT JOIN elements task ON task.id = pe.element_id
							$order
						";
						// pr($qry);
				$data = $this->PlanEffort->query($qry);
				$this->set('data', $data);
				// pr($data, 1);
			}
			$this->render('/Searches/planning/adjustment_list');
		}
	}

	public function delete_plan_effort(){
		if ($this->request->isAjax()) {
            $this->layout = 'ajax';
            $response = ['success' => false];
            $details = [];
            $user_id = $this->Session->read('Auth.User.id');

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				if(isset($post['id']) && !empty($post['id'])) {
					$id = $post['id'];
					$this->User->query("DELETE FROM plan_efforts WHERE id = $id");
					$response['success'] = true;
				}
			}
			echo json_encode($response);
            exit;
		}
	}

	public function util_details($tab = '', $user_id = null, $date = '', $date_type = '') {
		$response = ['success' => false];
		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$this->loadModel('PlanEffort');
			$logged_in_user = $this->Session->read('Auth.User.id');
			$udata = $this->PlanEffort->query("SELECT CONCAT_WS(' ',ud.first_name , ud.last_name) AS username
							FROM user_details ud
							WHERE ud.user_id = $user_id
						");

			$start_date = $end_date = $date;

			if($date_type == 'w') {
			    $end_date = date('Y-m-d', strtotime($start_date. ' +6 days'));
			}
			else if($date_type == 'm') {
			    $end_date = date("Y-m-t", strtotime($date));
			}

			// TASK EFFORTS
			$ef_data = $this->PlanEffort->query("SELECT ef.*,
			                                 	prj.title AS pname,
			                                 	wsp.title AS wname,
			                                 	task.title as ename,
			                                 	task.start_date,
			                                 	task.end_date,
			                                 	CONCAT_WS(' ',ud.first_name , ud.last_name) AS username,
			                                 	ud.profile_pic,
			                                 	ud.organization_id,
			                                 	ud.job_title
						FROM `element_efforts` ef
						LEFT JOIN user_permissions up ON up.element_id = ef.element_id AND up.user_id = ef.user_id AND up.user_id = $user_id
						LEFT JOIN user_details ud ON ud.user_id = ef.user_id
						LEFT JOIN projects prj ON prj.id = up.project_id
						LEFT JOIN workspaces wsp ON wsp.id = up.workspace_id
						LEFT JOIN elements task ON task.id = up.element_id
                        WHERE (
                        		(
	                        		(
	                        			DATE(task.start_date) BETWEEN DATE('$start_date') AND DATE('$end_date') OR
	                        			DATE(task.end_date) BETWEEN DATE('$start_date') AND DATE('$end_date')
	                        		)
	                        		OR
	                        		(
	                        			DATE('$start_date') BETWEEN DATE(task.start_date) AND DATE(task.end_date) OR
	                        			DATE('$end_date') BETWEEN DATE(task.start_date) AND DATE(task.end_date)
	                        		)
                    			) AND
                    			task.sign_off!=1 AND task.date_constraints=1
                			)
                        	AND ef.is_active = 1
						ORDER BY task.start_date ASC
					");

			// ADJUSTMENTS
			$adj_data = $this->PlanEffort->query("SELECT pe.*,
			                                 	prj.title AS pname,
			                                 	wsp.title AS wname,
			                                 	task.title as ename,
			                                 	CONCAT_WS(' ',ud.first_name , ud.last_name) AS username,
			                                 	ud.profile_pic,
			                                 	ud.organization_id,
			                                 	ud.job_title,
			                                 	CONCAT_WS(' ',ud1.first_name , ud1.last_name) AS creator
						FROM `plan_efforts` pe
						LEFT JOIN user_permissions up ON up.element_id = pe.element_id AND up.user_id = pe.user_id AND up.user_id = $user_id
						LEFT JOIN user_details ud ON ud.user_id = pe.user_id
						LEFT JOIN user_details ud1 ON ud1.user_id = pe.created_by
						LEFT JOIN projects prj ON prj.id = up.project_id
						LEFT JOIN workspaces wsp ON wsp.id = up.workspace_id
						LEFT JOIN elements task ON task.id = up.element_id
                        WHERE (
                        		(
	                        		(
	                        			DATE(task.start_date) BETWEEN DATE('$start_date') AND DATE('$end_date') OR
	                        			DATE(task.end_date) BETWEEN DATE('$start_date') AND DATE('$end_date')
	                        		)
	                        		OR
	                        		(
	                        			DATE('$start_date') BETWEEN DATE(task.start_date) AND DATE(task.end_date) OR
	                        			DATE('$end_date') BETWEEN DATE(task.start_date) AND DATE(task.end_date)
	                        		)
                    			) AND
                    			task.sign_off!=1 AND task.date_constraints=1
                			)
						ORDER BY ud.first_name ASC, ud.last_name ASC
					");

			// AVAILABILITY
			/*$avail_data = $this->PlanEffort->query("SELECT ua.*
						FROM `user_availabilities` ua
						WHERE
						#DATE(ua.effective) <= DATE('$date')
						DATE(ua.effective) BETWEEN DATE('$start_date') AND DATE('$end_date')

						AND ua.user_id = $user_id
						order by ua.effective desc limit 1
					");*/
			$avail_data = $this->PlanEffort->query("SELECT ua.*
							FROM user_availabilities ua
							LEFT JOIN user_availabilities ua2 ON
								ua.user_id = ua2.user_id
							    AND DATE(ua2.effective) <= '$start_date' #change date at runtime
							    AND ua.effective < ua2.effective
							WHERE
								ua.user_id = $user_id #change user_id at runtime
							    AND DATE(ua.effective) <= '$start_date' #change date at runtime
							    AND ua2.id IS NULL
				    ");
			if($date_type == 'w' || $date_type == 'm'){
				$avail_data = $this->PlanEffort->query("SELECT DISTINCT ua.* #change to specify which columns you need here
							FROM
							(
								(SELECT $user_id AS user_id) AS u #change user_id at runtime
							    CROSS JOIN
							    (
							        SELECT start_date + INTERVAL num DAY AS the_date
							        FROM
							        (
							            SELECT digit_1.d + 10 * digit_2.d AS num
							            FROM (SELECT 0 as d UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) digit_1
							            CROSS JOIN (SELECT 0 as d UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) digit_2
							        ) digits
							        CROSS JOIN (SELECT '$start_date' AS start_date, '$end_date' AS end_date) CONST #change to week/month start and end dates at runtime
							        WHERE start_date + INTERVAL num DAY <= end_date
							    ) AS td
							    LEFT JOIN user_availabilities ua ON
							    	u.user_id = ua.user_id
							    	AND DATE(ua.effective) <= td.the_date
							    LEFT JOIN user_availabilities ua2 ON
							    	DATE(ua2.effective) <= td.the_date
							    	AND ua.user_id = ua2.user_id
							    	AND (ua.effective < ua2.effective)
							)
							WHERE ua2.effective IS NULL AND ua.id IS NOT NULL
							ORDER BY ua.effective");
			}

			// WORK BLOCKS
			$wb_data = $this->PlanEffort->query("SELECT ub.*
						FROM `user_blocks` ub
						WHERE

						(
							(
								DATE(ub.work_start_date) BETWEEN DATE('$start_date') AND DATE('$end_date') OR
								DATE(ub.work_end_date) BETWEEN DATE('$start_date') AND DATE('$end_date')
							)
							OR
							(
								DATE('$start_date') BETWEEN DATE(ub.work_start_date) AND DATE(ub.work_end_date) OR
								DATE('$end_date') BETWEEN DATE(ub.work_start_date) AND DATE(ub.work_end_date)
							)
                    	)

						AND ub.user_id = $user_id
					");

			// ABSENCE
			$abs_data = $this->PlanEffort->query("SELECT *
                                    FROM availabilities av
                                    WHERE (STR_TO_DATE(LEFT(avail_start_date,LOCATE(' ',avail_start_date)),'%Y-%m-%d') BETWEEN '$start_date' AND '$end_date' OR
                                    '$date' BETWEEN STR_TO_DATE(LEFT(avail_start_date,LOCATE(' ',avail_start_date)),'%Y-%m-%d') AND
                                    STR_TO_DATE(LEFT(avail_end_date,LOCATE(' ',avail_end_date)),'%Y-%m-%d')) AND
                                    STR_TO_DATE(LEFT(avail_end_date,LOCATE(' ',avail_end_date)),'%Y-%m-%d') >= '$date'
                                    AND user_id = $user_id
                                    ORDER BY STR_TO_DATE(LEFT(avail_start_date,LOCATE(' ',avail_start_date)),'%Y-%m-%d')
					");


			$this->set('tab', $tab);
			$this->set('udata', $udata[0][0]['username']);
			$this->set('cuser', $user_id);
			$this->set('date', $date);
			$this->set('cdate_type', $date_type);
			$this->set('ef_data', $ef_data);
			$this->set('adj_data', $adj_data);
			$this->set('avail_data', $avail_data);
			$this->set('wb_data', $wb_data);
			$this->set('abs_data', $abs_data);
			$this->render('/Searches/planning/util_details');
		}
	}

	public function utill_work_list() {
		$response = ['success' => false];
		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				$user_id = $post['user_id'];
				$date = $post['date'];
				$date_type = $post['date_type'];
				$order = "ORDER BY ud.first_name ASC, ud.last_name ASC";
				if((isset($post['column']) && !empty($post['column'])) && (isset($post['direction']) && !empty($post['direction']))){
					$column = $post['column'];
					$direction = $post['direction'];
					if($column == 'first_name'){
						$order = "ORDER BY ud.first_name $direction, ud.last_name $direction";
					}
					else if($column == 'last_name'){
						$order = "ORDER BY ud.last_name $direction, ud.first_name $direction";
					}
					else{
						$order = "ORDER BY $column $direction";
					}
				}
				$start_date = $end_date = $date;

				if($date_type == 'w') {
				    $end_date = date('Y-m-d', strtotime($start_date. ' +6 days'));
				}
				else if($date_type == 'm') {
				    $end_date = date("Y-m-t", strtotime($date));
				}
				$this->loadModel('PlanEffort');
				$data = $this->PlanEffort->query("SELECT ef.*,
			                                 	prj.title AS pname,
			                                 	wsp.title AS wname,
			                                 	task.title as ename,
			                                 	task.start_date,
			                                 	task.end_date,
			                                 	CONCAT_WS(' ',ud.first_name , ud.last_name) AS username,
			                                 	ud.profile_pic,
			                                 	ud.organization_id,
			                                 	ud.job_title
						FROM `element_efforts` ef
						LEFT JOIN user_permissions up ON up.element_id = ef.element_id AND up.user_id = ef.user_id and up.user_id = $user_id
						LEFT JOIN user_details ud ON ud.user_id = ef.user_id
						LEFT JOIN projects prj ON prj.id = up.project_id
						LEFT JOIN workspaces wsp ON wsp.id = up.workspace_id
						LEFT JOIN elements task ON task.id = up.element_id
                        WHERE (
                        		(
	                        		(
	                        			DATE(task.start_date) BETWEEN DATE('$start_date') AND DATE('$end_date') OR
	                        			DATE(task.end_date) BETWEEN DATE('$start_date') AND DATE('$end_date')
	                        		)
	                        		OR
	                        		(
	                        			DATE('$start_date') BETWEEN DATE(task.start_date) AND DATE(task.end_date) OR
	                        			DATE('$end_date') BETWEEN DATE(task.start_date) AND DATE(task.end_date)
	                        		)
                    			) AND
                    			task.sign_off!=1 AND task.date_constraints=1
                			)
                        	AND ef.is_active = 1
						$order
					");
				$this->set('ef_data', $data);
				// pr($data, 1);
			}
			$this->render('/Searches/planning/utill_work_list');
		}
	}

	public function utill_adj_list() {
		$response = ['success' => false];
		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				$user_id = $post['user_id'];
				$date = $post['date'];
				$date_type = $post['date_type'];
				$order = "ORDER BY ud.first_name ASC, ud.last_name ASC";
				if((isset($post['column']) && !empty($post['column'])) && (isset($post['direction']) && !empty($post['direction']))){
					$column = $post['column'];
					$direction = $post['direction'];
					if($column == 'first_name'){
						$order = "ORDER BY ud.first_name $direction, ud.last_name $direction";
					}
					else if($column == 'last_name'){
						$order = "ORDER BY ud.last_name $direction, ud.first_name $direction";
					}
					else{
						$order = "ORDER BY $column $direction";
					}
				}
				$start_date = $end_date = $date;

				if($date_type == 'w') {
				    $end_date = date('Y-m-d', strtotime($start_date. ' +6 days'));
				}
				else if($date_type == 'm') {
				    $end_date = date("Y-m-t", strtotime($date));
				}
				$this->loadModel('PlanEffort');
				$data = $this->PlanEffort->query("SELECT pe.*,
			                                 	prj.title AS pname,
			                                 	wsp.title AS wname,
			                                 	task.title as ename,
			                                 	CONCAT_WS(' ',ud.first_name , ud.last_name) AS username,
			                                 	ud.profile_pic,
			                                 	ud.organization_id,
			                                 	ud.job_title,
			                                 	CONCAT_WS(' ',ud1.first_name , ud1.last_name) AS creator
						FROM `plan_efforts` pe
						LEFT JOIN user_permissions up ON up.element_id = pe.element_id AND up.user_id = pe.user_id and up.user_id = $user_id
						LEFT JOIN user_details ud ON ud.user_id = pe.user_id
						LEFT JOIN user_details ud1 ON ud1.user_id = pe.created_by
						LEFT JOIN projects prj ON prj.id = up.project_id
						LEFT JOIN workspaces wsp ON wsp.id = up.workspace_id
						LEFT JOIN elements task ON task.id = up.element_id
                        WHERE (
                        		(
	                        		(
	                        			DATE(task.start_date) BETWEEN DATE('$start_date') AND DATE('$end_date') OR
	                        			DATE(task.end_date) BETWEEN DATE('$start_date') AND DATE('$end_date')
	                        		)
	                        		OR
	                        		(
	                        			DATE('$start_date') BETWEEN DATE(task.start_date) AND DATE(task.end_date) OR
	                        			DATE('$end_date') BETWEEN DATE(task.start_date) AND DATE(task.end_date)
	                        		)
                    			) AND
                    			task.sign_off!=1 AND task.date_constraints=1
                			)
						$order
					");
				$this->set('adj_data', $data);
			}
			$this->render('/Searches/planning/utill_adj_list');
		}
	}

	public function planning_users() {
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';

			$details = [];
			$response = ['success' => false, 'content' => []];
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				$ids = $post['users'];
				$where = "";
				if(isset($ids) && !empty($ids)){
					$where = " AND ud.user_id IN ($ids)";
				}

				$data = $this->User->query("SELECT DISTINCT(ud.user_id) as uid, CONCAT_WS(' ',ud.first_name , ud.last_name) AS username
				                           FROM user_details ud
				                           LEFT JOIN users u ON u.id = ud.user_id
				                           WHERE u.role_id = 2 AND u.status = 1 AND u.is_activated = 1 AND u.is_deleted = 0 $where
			                           ");

				if(isset($data) && !empty($data)){
					foreach ($data as $key => $value) {
						$uid = $value['ud']['uid'];
						$name = $value[0]['username'];
						$details[] = ['value' => $uid, 'label' => $name];
					}
				}
			}
			$response['content'] = $details;
			echo json_encode($response);
            exit;
		}
	}


}
