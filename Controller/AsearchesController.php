<?php
App::uses('Controller/Component', 'Auth', 'Session', 'RequestHandler');
App::uses('Sanitize', 'Utility');
App::uses('CakeEmail', 'Network/Email');
//App::import('Vendor', 'Classes/MPDF56/mpdf');
App::import('Vendor', 'MPDF56/PhpWord');

// App::import('Lib', 'Communications');

class AsearchesController extends AppController {

    public $name = 'Searches';

    public $uses = ['User', 'UserDetail','ProjectPermission', 'UserSetting', 'Category', 'Aligned', 'UserProject', 'Project', 'Workspace', 'Area', 'ProjectWorkspace', 'Element', 'ProjectGroup', 'ProjectGroupUser', 'SearchList', 'SearchListUser'];

	public $objView = null;

    public $user_id = null;

    public $components = array( 'Common', 'Search');

    public $helpers = array('Html', 'Form', 'Session', 'Time', 'Text', 'Common', 'ViewModel', 'Search');

    public function beforeFilter() {

        parent::beforeFilter();

        $this->user_id = $this->Auth->user('id');

        $view = new View();
        $this->objView = $view;
    }

    public function index() {

        $this->layout = 'inner';
        $this->set('title_for_layout', __('Smart Search', true));
        $this->set('page_heading', __('Smart Search', true));
        $this->set('page_subheading', __('Search for people and content', true));

		/*
		 * Get my and received groups
		 * */
		$all_groups = null;

		$my_group = get_my_groups($this->user_id);
		if( isset($my_group) && !empty($my_group) ) {
			$all_groups = $my_group;
		}

		$search = $this->objView->loadHelper('Group');
		$received_group = $search->my_received_groups();

		if( isset($all_groups) && !empty($all_groups) ) {
			if( isset($all_groups) && !empty($all_groups) ) {
				$received_group = Set::combine($received_group, '{n}.ProjectGroup.id', '{n}.ProjectGroup.title');
				$all_groups = array_combines($all_groups, $received_group);
			}
		}
		else if( isset($received_group) && !empty($received_group) ) {
			$all_groups = $received_group;
		}

		$viewVars['all_groups'] = $all_groups;

		/*
		 * Get logged in user's saved search list
		 * */
		$viewVars['my_search_list'] = $this->SearchList->find('list', ['conditions' => ['SearchList.user_id' => $this->user_id ], 'recursive' => -1 ]);


        $crumb = [
            'last' => [
                'data' => [
                    'title' => 'Smart Search',
                    'data-original-title' => 'Smart Search',
                ]
            ]
        ];

        // pr($crumb, 1);
        $this->set( $viewVars );
        $this->set('crumb', $crumb);


	}

    public function smart_search() {

        $this->layout = 'inner';
        $this->set('title_for_layout', __('Smart Search', true));
        $this->set('page_heading', __('Smart Search', true));
        $this->set('page_subheading', __('Search for people and content', true));

		$viewVars = [];
        $crumb = [
            'last' => [
                'data' => [
                    'title' => 'Smart Search',
                    'data-original-title' => 'Smart Search',
                ]
            ]
        ];

		
        $this->set( $viewVars );
        $this->set('crumb', $crumb);


	}

 /*
  * @name  		get_users_by_skills
  * @access		public
  * @package  	App/Controller/GroupsController
  * */

	public function get_users_by_skills() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$response = [
			'success' => false,
			'msg' => null,
			'content' => null
			];

			if( $this->request->is( 'post' ) || $this->request->is( 'put' ) ) {

				// pr($this->data, 1);

				$skills = $this->data['skills'];


					$perm_users = null;

					if( isset($skills) && !empty($skills) ) {
						$this->User->unbindAll();
						// $this->User->bindModel(['hasMany' => ['UserSkill'], 'hasOne' => ['UserDetail']]);
						$user_skill_join = ['table' => 'user_skills', 'alias' => 'UserSkill', 'type' => 'INNER', 'conditions' => ['UserSkill.user_id = User.id']];
						$user_detail_join = ['table' => 'user_details', 'alias' => 'UserDetails', 'type' => 'INNER', 'conditions' => ['UserDetails.user_id = User.id']];
						$user_list = $this->User->find('all', [
							'joins' => [
								$user_skill_join,
								$user_detail_join
							],
							'conditions' => [
								'UserSkill.skill_id' => $skills,
							],
							'fields' => [ 'UserDetails.user_id', 'CONCAT(UserDetails.first_name, " ", UserDetails.last_name) AS user_name' ]
						]);

						if( isset($user_list) && !empty($user_list) ) {
							$perm_users = Set::combine($user_list, '{n}.UserDetails.user_id', '{n}.0.user_name');
						}
					}
					else {
						$search = $this->objView->loadHelper('Search');
						$users = $search->users_list();
						$userArr = $selected = null;
						if ( $users ) {
							foreach($users as $k => $val){
								$perm_users[$val["User"]["id"]] = $val[0]["User__name"];
							}
						}
					}

					if( isset($perm_users) && !empty($perm_users) ) {

						$response['content'] = (isset($perm_users) && !empty($perm_users)) ? $perm_users : null;

						$response['success'] = true;

					}
					else {
						$response['msg'] = 'There are no user for selected skills.';
					}
			}
			echo json_encode($response);
			exit;
		}

	}


	public function group_users() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$response = [
				'success' => true,
				'msg' => null,
				'content' => null
			];

			if( $this->request->is( 'post' ) || $this->request->is( 'put' ) ) {

				$group_users = null;

				$gid = $this->request->data['group'];

				$group = $this->objView->loadHelper('Group');
				$userId = $group->group_users($gid, true);

				$search = $this->objView->loadHelper('Search');
				$users = $search->users_list($userId);
				if ( $users ) {
					foreach($users as $k => $val){
						$group_users[$val["User"]["id"]] = $val[0]["User__name"];
					}
				}

				if( isset($group_users) && !empty($group_users) ) {
					$response['content'] = $group_users;
					$response['success'] = true;
				}
			}
			echo json_encode($response);
			exit;
		}

	}

	public function my_list_people() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$response = [
				'success' => true,
				'msg' => null,
				'content' => null
			];

			$viewModel = $this->objView->loadHelper('ViewModel');

			if( $this->request->is( 'post' ) || $this->request->is( 'put' ) ) {

				$list_users = $result = null;

				$list_id = $this->request->data['list_id'];

				if ( isset($list_id) && !empty($list_id) ) {

					$list_users = $this->SearchListUser->find('all', ['conditions' => ['SearchListUser.search_list_id' => $list_id], 'recursive' => -1, 'fields' => ['SearchListUser.user_id']]);

					if ( isset($list_users) && !empty($list_users) ) {

						$response['success'] = true;
						$users = Set::extract($list_users, '/SearchListUser/user_id');

						foreach($users as $k => $val){
							$user_details = $viewModel->get_user_data($val);
							$response['content'][$val] = $user_details['UserDetail']['first_name'] . ' ' . $user_details['UserDetail']['last_name'];
						}
					}
				}

			}
			echo json_encode($response);
			exit;
		}

	}

	public function keyword_search() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$response = [
				'success' => true,
				'msg' => null,
				'content' => null
			];

			$viewModel = $this->objView->loadHelper('ViewModel');

			if( $this->request->is( 'post' ) || $this->request->is( 'put' ) ) {

				$keyword  = $this->request->data['keyword'];
				$caseSen  = $this->request->data['caseSenstive'];
				if ( isset($keyword) && !empty($keyword) ) {

					$searchFromDatabase = array("ideascast");
					$this->Search->init( $searchFromDatabase );  //Enter no parameter to search from the entire database

					// $keyword = "ganpat ";
					if($caseSen ==1){
						$matchWord = false;
						$caseSenstive = true;
					}else if($caseSen ==2){
						$matchWord = true;
						$caseSenstive = true;					
					}else{
						$matchWord = false;	
						$caseSenstive = false;	
					}
					
					//$matchWord = $caseSen; 			//optional
							//optional

					$result = $this->Search->getSearchResults($keyword, $matchWord, $caseSenstive);
					$count = count($result);
					if($count == 0){
						$response['content'] = "Sorry <b>$keyword</b> was not found in any of the table";
					}
					else{
						$response['content'] = $this->Search->showResultAsHtml($result);
					}

				} 

			}
			//pr($response);
			$view = new View($this, false);
		    $view->viewPath = 'Searches';  // Directory inside view directory to search for .ctp files
		    $view->layout = false; // if you want to disable layout			 
			$view->set('response',$response);
			$html = $view->render('keyword_search');
			pr($html);
			//echo json_encode($html);
			exit;
		}

	}

	public function save_people_list() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$response = [
				'success' => true,
				'msg' => null,
				'content' => null
			];



			if( $this->request->is( 'post' ) || $this->request->is( 'put' ) ) {

				$post = $this->request->data;

				$this->request->data['SearchList']['user_id'] = $this->user_id;

				if( isset($post['add_selection']) && !empty($post['add_selection']) ) {

					if( isset($post['SearchListUser']['user_id']) && !empty($post['SearchListUser']['user_id']) ) {

						$users = explode(',', $post['SearchListUser']['user_id']);

						foreach ($users as $key => $val) {

							$user_data[] = [ 'user_id' => $val ];

						}

						unset($this->request->data['SearchListUser']);
						$this->request->data['SearchListUser'] = $user_data;

					}
				}
				else {

					unset($this->request->data['SearchListUser']);

				}

				if( $this->SearchList->saveAll($this->request->data) ) {
					$response['success'] = true;
					$response['content'] = $this->SearchList->getLastInsertId();
				}
			}
			
			echo json_encode($response);
			exit();
			
		}

	}

	public function create_people_list() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$selection = 2;



			$html = '';
			if( $this->request->is( 'post' ) || $this->request->is( 'put' ) ) {

				$post = $this->request->data;

				if( isset($post['selection']) && !empty($post['selection']) ) {
					$selection = $post['selection'];
				}
				// $d = Set::extract($post['users'], '{n}.id');

				// pr($post, 1);
				$view = new View($this, false);
				$view->viewPath = 'Searches/partials';  // Directory inside view directory to search for .ctp files
				$view->layout = false; // if you want to disable layout
				$view->set('data', $post);

				$html = $view->render('create_people_list');

			}

			echo json_encode($html);
			exit();
		}

	}


}
