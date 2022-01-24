<?php
App::uses('Controller/Component', 'Auth', 'Session', 'RequestHandler');
App::uses('CakeEmail', 'Network/Email');
App::uses('HttpSocket', 'Network/Http');


class SubdomainsController extends AppController {


	public $uses = ['User', 'UserDetail', 'ProjectPermission', 'UserSetting', 'Category', 'Aligned', 'UserProject', 'Project', 'Workspace', 'Element', 'ElementPermission', 'ProjectGroupUser','UserPermission'];

	public $helpers = array('Html', 'Form', 'Session', 'Time', 'Text', 'Common', 'ViewModel', 'Mpdf', 'Image', 'Wiki', 'TaskCenter', 'Group');

	public $components = array('RequestHandler', 'Common', 'Group');

	public function beforeFilter() {

		parent::beforeFilter();
		$this->user_id = $this->Auth->user('id');
		$this->Auth->allow('setnull', 'display', 'available', 'contactus', 'slider', 'contact', 'pricing-plans', 'about', 'privacy', 'faq', 'price', 'terms', 'product', 'why_jeera', 'privacy', 'why_jeera_solution', 'why_jeera_benefits', 'why_jeera_focus', 'why_jeera_approach', 'contactfordemo', 'downloads', 'newcontactus', 'downloads_doc', 'empoweringteamwork', 'jeera_offer', 'jeera_demo', 'sethome', 'features', 'request_demo', 'how_buy', 'partners', 'templates', 'treeview');

		/* if( $_SERVER['HTTP_HOST'] != LOCALIP )  {

			if( $this->Session->read('Auth.User.role_id') == 3 ){
				$this->redirect(['controller' => 'organisations','action' => 'dashboard']);
			}
			if( $this->Session->read('Auth.User.role_id') == 1 ){
				$this->redirect(['controller' => 'dashboard','action' => 'index']);
			}
		} */

	}

	public function socket() {
		if ($this->request->isAjax()) {
		$this->layout = 'ajax';
		$response = [
		'success' => true,
		'msg' => '',
		'content' => null,
		];
		if ($this->request->is('post') || $this->request->is('put')) {
				//pr($this->request->data);
				$response['project_id'] = $this->request->data['project_id'];
		}
		echo json_encode($response);
		exit;
		}

	}

	function sharing($subDomain = null) {
		$this->layout = 'ajax';
		$this->set('title_for_layout', __('Cost Center', true));
		$this->set('page_heading', __('Cost Center', true));
		$this->set('page_subheading', __('Plan and manage Project costs', true));

	}

	function index($project_id = null) {
		$this->layout = 'inner';
		$this->set('title_for_layout', __('Social Analytics', true));
		$this->set('page_heading', __('Social Analytics', true));
		$this->set('page_subheading', __('Analyze social interactions and relationships', true));

		$viewVars = [];

		$listdomainusers = $this->Common->userDetail($this->Session->read('Auth.User.id'));

		if($listdomainusers['UserDetail']['analytics'] != 1){

		$userStartPageData = $this->User->find('first', ['conditions' => ['User.id' => $this->Auth->user('id')], 'recursive' => -1]);
		$page_setting_toggle = (isset($userStartPageData) && !empty($userStartPageData)) ? $userStartPageData['User']['page_setting_toggle'] : 0;
		$landing_url = (isset($userStartPageData) && !empty($userStartPageData)) ? $userStartPageData['User']['landing_url'] : null;

		if (isset($page_setting_toggle) && !empty($page_setting_toggle)) {
			if (isset($landing_url) && !empty($landing_url)) {
				$landing_url = explode('/', $landing_url);
				$landing_controller = $landing_url[0];
				if(isset($landing_url[1])){
				$landing_action = $landing_url[1];
				}
				if(isset($landing_url[2]) && !empty($landing_url[2])){
					$landing_action = $landing_url[1].'/'.$landing_url[2];
				}
				if(isset($landing_url[3]) && !empty($landing_url[3])){
					$landing_action = $landing_url[1].'/'.$landing_url[2].'/'.$landing_url[3];
				}
				if(isset($landing_url[4])){
					$landing_action = $landing_url[1].'/'.$landing_url[2].'/'.$landing_url[3].'/'.$landing_url[4];
				}

				$this->Auth->loginRedirect = array('controller' => $landing_controller, 'action' => $landing_action, 'admin' => false);

				return $this->redirect($this->Auth->redirectUrl());


			} else {


				return $this->redirect(array('controller' => 'projects', 'action' => 'lists', 'admin' => false));

			}
		}else {

			return $this->redirect(array('controller' => 'projects', 'action' => 'lists', 'admin' => false));

		}

		}

		if( $_SERVER['HTTP_HOST'] != LOCALIP )  {

			if( $this->Session->read('Auth.User.role_id') == 3 ){
				$this->redirect(['controller' => 'organisations','action' => 'dashboard']);
			}
			if( $this->Session->read('Auth.User.role_id') == 1 ){
				$this->redirect(['controller' => 'dashboard','action' => 'index']);
			}
		}

		$user_id = $this->Session->read('Auth.User.id');
		$projects = $this->UserPermission->find('list', array('joins' => [
					[
						'alias' => 'Project',
						'table' => 'projects',
						'type' => 'INNER',
						'conditions' => 'Project.id = UserPermission.project_id',
					]],'conditions' => array('UserPermission.user_id' => $user_id,'UserPermission.workspace_id IS NULL', 'role' => ['Creator', 'Group Owner','Owner'] ),'fields'=>array('Project.id','Project.title')));

		$this->set('project_id', $project_id);
		$this->setJsVar('project_id', $project_id);
		$crumb = [
			'last' => [
				'data' => [
					'title' => 'Social Analytics',
					'data-original-title' => 'Social Analytics',
				],
			],
		];

		$this->set('projects',$projects);
		$this->set('crumb', $crumb);
	}


	function load_analytics() {
		if ($this->request->isAjax()) {

			$this->layout = false;
			$view = new View($this, false);
			$view->viewPath = 'Subdomains/partial';
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				$type = (isset($post['type']) && !empty($post['type'])) ? $post['type'] : 'sharing';
				$project_id = (isset($post['project_id']) && !empty($post['project_id'])) ? $post['project_id'] : null;
				// pr($type);

				$user_id = $this->Session->read('Auth.User.id');
				$projects = $this->UserPermission->find('list', array('joins' => [
							[
								'alias' => 'Project',
								'table' => 'projects',
								'type' => 'INNER',
								'conditions' => 'Project.id = UserPermission.project_id',
							]],'conditions' => array('UserPermission.user_id' => $user_id,'UserPermission.workspace_id IS NULL', 'role' => ['Creator', 'Group Owner','Owner'] ),'fields'=>array('Project.id','Project.title')));

				$view->set('projects', $projects);
				$view->set('project_id', $project_id);
			}

			$html = $view->render($type);

			echo json_encode($html);
			exit();

		}
	}

	public function org_chart_json() {

		$this->layout = false;
		$this->autoRender = false;

		$view = new View($this, false);
		$view->viewPath = 'Subdomains';
		$current_org = $view->loadHelper('Permission')->current_org();
		$current_org = $current_org['organization_id'];
		$user_id = $this->user_id;
		$data = [];
        if ($this->request->is('post') || $this->request->is('put')) {
        	$post = $this->request->data;
        	$people_from = (isset($post['people_from']) && !empty($post['people_from'])) ? $post['people_from'] : '';
        	$specific_items = (isset($post['specific_items']) && !empty($post['specific_items'])) ? $post['specific_items'] : [];
        	$dotted_lines = (isset($post['dotted_lines']) && !empty($post['dotted_lines'])) ? $post['dotted_lines'] : '';

        	$data = $view->loadHelper('Permission')->org_chart($user_id, $current_org, $people_from, $specific_items, $dotted_lines);
        }
        // pr($data,1);
        $view->set('data', $data[0][0]['data']);
		$html = $view->render('org_chart_json');
		echo json_encode($html);
		exit;
	}


	function knowledge_analytics($type = null) {
		$this->layout = 'inner';
		$this->set('title_for_layout', __('Capability Analytics', true));
		$this->set('page_heading', __('Capability Analytics', true));
		$this->set('page_subheading', __('Analyze organizational capabilities', true));

		// pr($this->params['named'], 1);

		$listdomainusers = $this->Common->userDetail($this->Session->read('Auth.User.id'));

		if($listdomainusers['UserDetail']['analytics'] != 1){

		$userStartPageData = $this->User->find('first', ['conditions' => ['User.id' => $this->Auth->user('id')], 'recursive' => -1]);
		$page_setting_toggle = (isset($userStartPageData) && !empty($userStartPageData)) ? $userStartPageData['User']['page_setting_toggle'] : 0;
		$landing_url = (isset($userStartPageData) && !empty($userStartPageData)) ? $userStartPageData['User']['landing_url'] : null;

		if (isset($page_setting_toggle) && !empty($page_setting_toggle)) {
			if (isset($landing_url) && !empty($landing_url)) {
				$landing_url = explode('/', $landing_url);
				$landing_controller = $landing_url[0];
				if(isset($landing_url[1])){
				$landing_action = $landing_url[1];
				}
				if(isset($landing_url[2]) && !empty($landing_url[2])){
					$landing_action = $landing_url[1].'/'.$landing_url[2];
				}
				if(isset($landing_url[3]) && !empty($landing_url[3])){
					$landing_action = $landing_url[1].'/'.$landing_url[2].'/'.$landing_url[3];
				}
				if(isset($landing_url[4])){
					$landing_action = $landing_url[1].'/'.$landing_url[2].'/'.$landing_url[3].'/'.$landing_url[4];
				}

				$this->Auth->loginRedirect = array('controller' => $landing_controller, 'action' => $landing_action, 'admin' => false);

				return $this->redirect($this->Auth->redirectUrl());


			} else {


				return $this->redirect(array('controller' => 'projects', 'action' => 'lists', 'admin' => false));

			}
		}else {

			return $this->redirect(array('controller' => 'projects', 'action' => 'lists', 'admin' => false));

		}

		}


		if( $_SERVER['HTTP_HOST'] != LOCALIP )  {

			if( $this->Session->read('Auth.User.role_id') == 3 ){
				$this->redirect(['controller' => 'organisations','action' => 'dashboard']);
			}
		}

		$viewVars = [];

		$user_id = $this->Session->read('Auth.User.id');

		$crumb = [
			'last' => [
				'data' => [
					'title' => 'Capability Analytics',
					'data-original-title' => 'Capability Analytics',
				],
			],
		];

		$project_id = $competency_id = $pfrom = '';
		$spf2 = [];
		if(isset($this->params['named']) && !empty($this->params['named'])){
			$params = $this->params['named'];
			if(isset($params['project']) && !empty($params['project'])){
				$project_id = $params['project'];
			}
			if(isset($params['competency']) && !empty($params['competency'])){
				$competency_id = $params['competency'];
			}
			if(isset($params['pfrom']) && !empty($params['pfrom'])){
				$pfrom = $params['pfrom'];
				if(isset($params['spf2']) && !empty($params['spf2'])){
					$spf2 = explode(',', $params['spf2']);
				}
			}
		}
			// pr($spf2, 1);

		$this->set('type', $type);
		$this->setJsVar('type', $type);

		$this->set('project_id', $project_id);
		$this->setJsVar('project_id', $project_id);

		$this->set('competency_id', $competency_id);
		$this->setJsVar('competency_id', $competency_id);

		$this->set('pfrom', $pfrom);
		$this->setJsVar('pfrom', $pfrom);

		$this->set('spf2', $spf2);
		$this->setJsVar('spf2', $spf2);

		$this->set('crumb', $crumb);

	}

	function load_ka() {
		if ($this->request->isAjax()) {

			$this->layout = false;
			$view = new View($this, false);
			$view->viewPath = 'Subdomains/partial';
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;

				$type = (isset($post['type']) && !empty($post['type'])) ? $post['type'] : 'ka_search';
				$passed_type = (isset($post['passed_type']) && !empty($post['passed_type'])) ? $post['passed_type'] : '';
				$project_id = (isset($post['project_id']) && !empty($post['project_id'])) ? $post['project_id'] : '';
				$competency_id = (isset($post['competency_id']) && !empty($post['competency_id'])) ? $post['competency_id'] : '';
				$pfrom = (isset($post['pfrom']) && !empty($post['pfrom'])) ? $post['pfrom'] : null;
				$spf2 = (isset($post['spf2']) && !empty($post['spf2'])) ? $post['spf2'] : [];
				// pr($post, 1);

				$user_id = $this->Session->read('Auth.User.id');

				if(isset($type ) && !empty($type ) && $type =='ka_competency'){
					$projects = [];
					$conditions = array('UserPermission.user_id' => $user_id,'UserPermission.workspace_id IS NULL', 'role' => ['Creator', 'Group Owner','Owner']  );
					if(isset($project_id) && !empty($project_id)){
						$conditions['UserPermission.project_id'] = $project_id;
						$projects = $this->UserPermission->find('list', array('joins' => [
							[
								'alias' => 'Project',
								'table' => 'projects',
								'type' => 'INNER',
								'conditions' => 'Project.id = UserPermission.project_id',
							]
						],
						'conditions' => $conditions, 'fields'=>array('Project.id','Project.title'
						)));
						$view->set('projects', $projects);
					}

					if(isset($competency_id) && !empty($competency_id)){
						// $data = [];
						if($passed_type == 'skill') {
							$this->loadModel('Skill');
							$data = $this->Skill->find('list', ['conditions' => ['id' => $competency_id], 'fields' => ['id', 'title'], 'order' => ['title ASC']]);
						}
						else if($passed_type == 'subject') {
							$this->loadModel('Subject');
							$data = $this->Subject->find('list', ['conditions' => ['id' => $competency_id], 'fields' => ['id', 'title'], 'order' => ['title ASC']]);
						}
						else if($passed_type == 'domain') {
							$this->loadModel('KnowledgeDomain');
							$data = $this->KnowledgeDomain->find('list', ['conditions' => ['id' => $competency_id], 'fields' => ['id', 'title'], 'order' => ['title ASC']]);
						}
						$view->set('competency', $data);
					}

					if(isset($pfrom) && !empty($pfrom)){
						$view->set('pfrom', $pfrom);
						if(isset($spf2) && !empty($spf2)) {
							if($pfrom != 'community' || $pfrom != 'all_projects' || $pfrom != 'created_projects' || $pfrom != 'owner_projects' || $pfrom != 'shared_projects'){
								// pr($spf2, 1);
								$spf2 = implode(',', $spf2);
								if($pfrom == 'organizations'){
									$this->loadModel('Organization');
									$data = $this->Organization->query("SELECT id, name FROM organizations WHERE id IN($spf2) ORDER BY name ASC");
									if(isset($data) && !empty($data)){
										foreach ($data as $key => $value) {
											$spf2_data[] = ['value' => $value['organizations']['id'], 'label' => htmlentities($value['organizations']['name'], ENT_QUOTES, "UTF-8")];
										}
									}
								}
								else if($pfrom == 'locations'){
									$this->loadModel('Location');
									$data = $this->Location->query("SELECT id, name FROM locations WHERE id IN($spf2) ORDER BY name ASC");
									if(isset($data) && !empty($data)){
										foreach ($data as $key => $value) {
											$spf2_data[] = ['value' => $value['locations']['id'], 'label' => htmlentities($value['locations']['name'], ENT_QUOTES, "UTF-8") ];
										}
									}
								}
								else if($pfrom == 'departments'){
									$this->loadModel('Department');
									$data = $this->Department->query("SELECT id, name FROM departments WHERE id IN($spf2) ORDER BY name ASC");
									if(isset($data) && !empty($data)){
										foreach ($data as $key => $value) {
											$spf2_data[] = ['value' => $value['departments']['id'], 'label' => htmlentities($value['departments']['name'], ENT_QUOTES, "UTF-8") ];
										}
									}
								}
								else if($pfrom == 'users'){
									$this->loadModel('User');
									$data = $this->User->query("SELECT u.id, CONCAT_WS(' ', ud.first_name, ud.last_name) as full_name FROM users u INNER JOIN user_details ud ON ud.user_id = u.id WHERE u.role_id = 2 AND u.status = 1 AND u.is_activated = 1 AND u.id IN($spf2) ORDER BY full_name ASC");
									if(isset($data) && !empty($data)){
										foreach ($data as $key => $value) {
											$spf2_data[] = ['value' => $value['u']['id'], 'label' => $value[0]['full_name']];
										}
									}
								}
								else if($pfrom == 'project'){
									// pr($post, 1);
									$conditions = [
										'UserPermission.user_id' => $user_id,
										'UserPermission.workspace_id IS NULL',
										'role' => ['Creator', 'Group Owner','Owner']
									];
									$conditions['UserPermission.project_id'] = explode(',', $spf2);

									$data = $this->UserPermission->find('list', array('joins' => [
												[
													'alias' => 'Project',
													'table' => 'projects',
													'type' => 'INNER',
													'conditions' => 'Project.id = UserPermission.project_id',
												]
											],
											'conditions' => $conditions,
											'fields'=>array('Project.id','Project.title'),
											'order' => ['Project.title ASC']
										)
									);

									// pr($conditions);
									if(isset($data) && !empty($data)){
										foreach ($data as $key => $value) {
											$spf2_data[] = ['value' => $key, 'label' => htmlentities($value, ENT_QUOTES, "UTF-8")];
										}
									}
								}
								else if($pfrom == 'competencies' && (isset($passed_type) && !empty($passed_type)) ){

									$competency_type = $passed_type;
									if($competency_type == 'skill'){
										$this->loadModel('Skill');
										$data = $this->Skill->find('list', ['conditions' => ['id' => explode(',', $spf2)], 'fields' => ['id', 'title'], 'order' => ['title ASC']]);
									}
									else if($competency_type == 'subject'){
										$this->loadModel('Subject');
										$data = $this->Subject->find('list', ['conditions' => ['id' => explode(',', $spf2)], 'fields' => ['id', 'title'], 'order' => ['title ASC']]);
									}
									else if($competency_type == 'domain'){
										$this->loadModel('KnowledgeDomain');
										$data = $this->KnowledgeDomain->find('list', ['conditions' => ['id' => explode(',', $spf2)], 'fields' => ['id', 'title'], 'order' => ['title ASC']]);
									}
									// pr($data, 1);
									if(isset($data) && !empty($data)){
										foreach ($data as $key => $value) {
											$spf2_data[] = ['value' => $key, 'label' => htmlentities($value, ENT_QUOTES, "UTF-8")];
										}
									}
								}
								$view->set('spf2_data', $spf2_data);
							}
						}
					}

				}


				$view->set('passed_type', $passed_type);
				$view->set('passed_project_id', $project_id);
			}

			$html = $view->render($type);

			echo json_encode($html);
			exit();

		}
	}

	public function search_json() {

		$this->layout = false;
		$this->autoRender = false;
		$search = (isset($this->params->query['q']) && !empty($this->params->query['q'])) ? $this->params->query['q'] : '';
		$view = new View($this, false);
		$view->viewPath = 'Subdomains';
		$current_org = $view->loadHelper('Permission')->current_org();
		$current_org = $current_org['organization_id'];
		$data = [];
		$noimage = SITEURL;
		$userimage = SITEURL . USER_PIC_PATH;
		$query = "SELECT
			        JSON_OBJECT(
			            'name', 'Search',
			            'type', 'SEARCH',
			            'image', 'srch.png',
			            'children',
			                JSON_ARRAY(
			                    JSON_OBJECT(
			                        'name', 'People',
			                        'type', 'PEOPLE',
			                        'image', 'People.png',
			                        'children', IFNULL (
			                            (SELECT
			                                JSON_ARRAYAGG(
			                                    JSON_OBJECT(
			                                        'id', matching_people.id,
			                                        'name', matching_people.name,
			                                        'type', 'PERSON',
			                                        'image',
			                                            CASE
			                                                WHEN matching_people.profile_pic IS NULL OR matching_people.profile_pic = '' THEN CONCAT('".$noimage."','images/placeholders/user/user_1.png') #change at runtime
			                                                ELSE CONCAT('".$userimage."', matching_people.profile_pic) #change at runtime
			                                            END,
			                                        'name_value', matching_people.name_matches,
			                                        'email_value', matching_people.email_matches,
			                                        'bio_value', matching_people.bio_matches,
			                                        'interests_value', matching_people.interest_matches,
			                                        'value', matching_people.name_matches + matching_people.email_matches + matching_people.bio_matches + matching_people.interest_matches,
			                                        'notInYourOrganization', matching_people.notInYourOrganization
			                                        )
			                                ) AS person
			                                FROM
			                                (
			                                    SELECT
			                                        u.id,
			                                        CONCAT(ud.first_name,' ', ud.last_name) AS name,
			                                        ud.profile_pic,
			                                        IF(IF(ISNULL(ud.organization_id), '', ud.organization_id) <> IF(ISNULL(".$current_org."),'',".$current_org."), '1','0') AS notInYourOrganization, #here 2 is the current user organization_id and needs to be replaced at runtime
			                                        ROUND((LENGTH(CONCAT(ud.first_name,' ',ud.last_name)) - LENGTH(REPLACE(LOWER(CONCAT(ud.first_name,' ',ud.last_name)),LOWER('".$search."'),''))) / LENGTH('".$search."'),0) AS name_matches, #change search text at runtime
			                                        ROUND((LENGTH(u.email) - LENGTH(REPLACE(LOWER(u.email),LOWER('".$search."'),''))) / LENGTH('".$search."'),0) AS email_matches, #change search text at runtime
			                                        IF(LENGTH(ud.bio) > 0,ROUND((LENGTH(ud.bio) - LENGTH(REPLACE(LOWER(ud.bio),LOWER('".$search."'),''))) / LENGTH('".$search."'),0),0) AS bio_matches, #change search text at runtime
			                                        COUNT(DISTINCT(matching_interests.id)) AS interest_matches
			                                    FROM
			                                        users u
			                                    LEFT JOIN (
			                                        SELECT
			                                            ui.id,
			                                            ui.user_id
			                                        FROM
			                                            user_interests ui
			                                        WHERE
			                                            ui.title LIKE '%".$search."%' #change search text at runtime
			                                        ) AS matching_interests
			                                    ON
			                                        u.id = matching_interests.user_id
			                                    LEFT JOIN user_details ud ON
			                                        u.id = ud.user_id
			                                    WHERE
			                                        u.role_id = 2 AND u.status = 1 AND u.is_deleted = 0 AND u.is_activated = 1
			                                    GROUP BY
			                                        u.id
			                                    HAVING
			                                        (name_matches + email_matches + bio_matches + interest_matches) > 0
			                                ) AS matching_people
			                            )
			                            ,
			                            JSON_ARRAY()
			                        )
			                    ),
			                    JSON_OBJECT(
			                        'name', 'Skills',
			                        'type', 'SKILLS',
			                        'image', 'Skills.png',
			                        'children', IFNULL (
			                            (SELECT
			                                JSON_ARRAYAGG(
			                                    JSON_OBJECT(
			                                        'id', matching_skills.id,
			                                        'name', matching_skills.name,
			                                        'type', 'SKILL',
			                                        'image',
			                                            CASE
			                                                WHEN matching_skills.profile_pic IS NULL OR matching_skills.profile_pic = '' THEN ''
			                                                ELSE CONCAT('". SITEURL . SKILL_IMAGE_PATH."', matching_skills.profile_pic) #change at runtime
			                                            END,
			                                        'title_matches', matching_skills.title_matches,
			                                        'description_matches', matching_skills.description_matches,
			                                        'keyword_matches', matching_skills.keyword_matches,
			                                        'value', matching_skills.title_matches + matching_skills.description_matches + matching_skills.keyword_matches
			                                        )
			                                ) AS skill
			                            FROM
			                                (
			                                    SELECT
			                                        s.id,
			                                        s.title AS name,
			                                        s.image AS profile_pic,
			                                        ROUND((LENGTH(s.title) - LENGTH(REPLACE(LOWER(s.title), LOWER('".$search."'), '')))/ LENGTH('".$search."'),0) AS title_matches, #change search text at runtime
			                                        IF(LENGTH(s.description) > 0, ROUND((LENGTH(s.description) - LENGTH(REPLACE(LOWER(s.description), LOWER('".$search."'), '')))/ LENGTH('".$search."'),0),0) AS description_matches, #change search text at runtime
			                                        COUNT(DISTINCT(matching_keywords.id)) AS keyword_matches
			                                    FROM
			                                        skills s
			                                    LEFT JOIN
			                                        (SELECT k.id, k.item_id, k.type FROM keywords k WHERE k.keyword LIKE '%".$search."%') AS matching_keywords #change at runtime
			                                    ON s.id = matching_keywords.item_id AND matching_keywords.type = 'skill'
			                                    GROUP BY s.id
			                                    HAVING (title_matches + description_matches + keyword_matches) > 0
			                                ) AS matching_skills
			                            )
			                            ,
			                            JSON_ARRAY()
			                        )
			                    ),
			                    JSON_OBJECT(
			                        'name', 'Subjects',
			                        'type', 'SUBJECTS',
			                        'image', 'Subjects.png',
			                        'children', IFNULL (
			                            (SELECT
			                                JSON_ARRAYAGG(
			                                    JSON_OBJECT(
			                                        'id', matching_subjects.id,
			                                        'name', matching_subjects.name,
			                                        'type', 'SUBJECT',
			                                        'image',
			                                            CASE
			                                                WHEN matching_subjects.profile_pic IS NULL OR matching_subjects.profile_pic = '' THEN ''
			                                                ELSE CONCAT('". SITEURL . SUBJECT_IMAGE_PATH."', matching_subjects.profile_pic) #change at runtime
			                                            END,
			                                        'title_matches', matching_subjects.title_matches,
			                                        'description_matches', matching_subjects.description_matches,
			                                        'keyword_matches', matching_subjects.keyword_matches,
			                                        'value', matching_subjects.title_matches + matching_subjects.description_matches + matching_subjects.keyword_matches
			                                        )
			                                ) AS subject
			                            FROM
			                                (
			                                    SELECT
			                                        s.id,
			                                        s.title AS name,
			                                        s.image AS profile_pic,
			                                        ROUND((LENGTH(s.title) - LENGTH(REPLACE(LOWER(s.title), LOWER('".$search."'), '')))/ LENGTH('".$search."'),0) AS title_matches, #change search text at runtime
			                                        IF(LENGTH(s.description) > 0, ROUND((LENGTH(s.description) - LENGTH(REPLACE(LOWER(s.description), LOWER('".$search."'), '')))/ LENGTH('".$search."'),0),0) AS description_matches, #change search text at runtime
			                                        COUNT(DISTINCT(matching_keywords.id)) AS keyword_matches
			                                    FROM
			                                        subjects s
			                                    LEFT JOIN
			                                        (SELECT k.id, k.item_id, k.type FROM keywords k WHERE k.keyword LIKE '%".$search."%') AS matching_keywords #change at runtime
			                                    ON s.id = matching_keywords.item_id AND matching_keywords.type = 'subject'
			                                    GROUP BY s.id
			                                    HAVING (title_matches + description_matches + keyword_matches) > 0
			                                ) AS matching_subjects
			                            )
			                            ,
			                            JSON_ARRAY()
			                        )
			                    ),
			                    JSON_OBJECT(
			                        'name', 'Domains',
			                        'type', 'DOMAINS',
			                        'image', 'Domains.png',
			                        'children', IFNULL (
			                            (SELECT
			                                JSON_ARRAYAGG(
			                                    JSON_OBJECT(
			                                        'id', matching_domains.id,
			                                        'name', matching_domains.name,
			                                        'type', 'DOMAIN',
			                                        'image',
			                                            CASE
			                                                WHEN matching_domains.profile_pic IS NULL OR matching_domains.profile_pic = '' THEN ''
			                                                ELSE CONCAT('". SITEURL . DOMAIN_IMAGE_PATH."', matching_domains.profile_pic) #change at runtime
			                                            END,
			                                        'title_matches', matching_domains.title_matches,
			                                        'description_matches', matching_domains.description_matches,
			                                        'keyword_matches', matching_domains.keyword_matches,
			                                        'value', matching_domains.title_matches + matching_domains.description_matches + matching_domains.keyword_matches
			                                        )
			                                ) AS domain
			                            FROM
			                                (
			                                    SELECT
			                                        s.id,
			                                        s.title AS name,
			                                        s.image AS profile_pic,
			                                        ROUND((LENGTH(s.title) - LENGTH(REPLACE(LOWER(s.title), LOWER('".$search."'), '')))/ LENGTH('".$search."'),0) AS title_matches, #change search text at runtime
			                                        IF(LENGTH(s.description) > 0, ROUND((LENGTH(s.description) - LENGTH(REPLACE(LOWER(s.description), LOWER('".$search."'), '')))/ LENGTH('".$search."'),0),0) AS description_matches, #change search text at runtime
			                                        COUNT(DISTINCT(matching_keywords.id)) AS keyword_matches
			                                    FROM
			                                        knowledge_domains s
			                                    LEFT JOIN
			                                        (SELECT k.id, k.item_id, k.type FROM keywords k WHERE k.keyword LIKE '%".$search."%') AS matching_keywords #change at runtime
			                                    ON s.id = matching_keywords.item_id AND matching_keywords.type = 'domain'
			                                    GROUP BY s.id
			                                    HAVING (title_matches + description_matches + keyword_matches) > 0
			                                ) AS matching_domains
			                            )
			                            ,
			                            JSON_ARRAY()
			                        )
			                    ),
			                    JSON_OBJECT(
			                        'name', 'Organizations',
			                        'type', 'ORGANIZATIONS',
			                        'image', 'Organizations.png',
			                        'children', IFNULL (
			                            (SELECT
			                            JSON_ARRAYAGG(
			                                JSON_OBJECT(
			                                    'id', matching_orgs.id,
			                                    'name', matching_orgs.name,
			                                    'type', 'ORGANIZATION',
			                                    'image',
			                                        CASE
			                                            WHEN matching_orgs.profile_pic IS NULL OR matching_orgs.profile_pic = '' THEN ''
			                                            ELSE CONCAT('". SITEURL . ORG_IMAGE_PATH."', matching_orgs.profile_pic) #change path at runtime
			                                            END,
			                                    'value', name_matches + info_matches,
			                                    'name_matches', name_matches,
			                                    'info_matches', info_matches
			                                )
			                            ) AS org
			                            FROM
			                                (
			                                SELECT
			                                    s.id,
			                                    s.name,
			                                    s.image AS profile_pic,
			                                    ROUND((LENGTH(s.name) - LENGTH(REPLACE(LOWER(s.name), LOWER('".$search."'), '')))/ LENGTH('".$search."'),0) AS name_matches, #change search text at runtime
			                                    IF(LENGTH(s.information) > 0, ROUND((LENGTH(s.information) - LENGTH(REPLACE(LOWER(s.information), LOWER('".$search."'), '')))/ LENGTH('".$search."'),0),0) AS info_matches #change search text at runtime
			                                FROM
			                                    organizations s
			                                HAVING (name_matches + info_matches) > 0
			                                ) AS matching_orgs
			                            )
			                        ,
			                        JSON_ARRAY()
			                        )
			                    ),
			                    JSON_OBJECT(
			                        'name', 'Locations',
			                        'type', 'LOCATIONS',
			                        'image', 'Locations.png',
			                        'children', IFNULL (
			                            (SELECT
			                            JSON_ARRAYAGG(
			                                JSON_OBJECT(
			                                    'id', matching_locs.id,
			                                    'name', matching_locs.name,
			                                    'type', 'LOCATION',
			                                    'image',
			                                        CASE
			                                            WHEN matching_locs.profile_pic IS NULL OR matching_locs.profile_pic = '' THEN ''
			                                            ELSE CONCAT('". SITEURL . LOC_IMAGE_PATH."', matching_locs.profile_pic) #change path at runtime
			                                            END,
			                                    'value', name_matches + info_matches,
			                                    'name_matches', name_matches,
			                                    'info_matches', info_matches
			                                )
			                            ) AS loc
			                            FROM
			                                (
			                                SELECT
			                                    s.id,
			                                    s.name,
			                                    s.image AS profile_pic,
			                                    ROUND((LENGTH(s.name) - LENGTH(REPLACE(LOWER(s.name), LOWER('".$search."'), '')))/ LENGTH('".$search."'),0) AS name_matches, #change search text at runtime
			                                    IF(LENGTH(s.information) > 0, ROUND((LENGTH(s.information) - LENGTH(REPLACE(LOWER(s.information), LOWER('".$search."'), '')))/ LENGTH('".$search."'),0),0) AS info_matches #change search text at runtime
			                                FROM
			                                    locations s
			                                HAVING (name_matches + info_matches) > 0
			                                ) AS matching_locs
			                            )
			                        ,
			                        JSON_ARRAY()
			                        )
			                    ),
			                    JSON_OBJECT(
			                        'name', 'Departments',
			                        'type', 'DEPARTMENTS',
			                        'image', 'Departments.png',
			                        'children', IFNULL (
			                            (SELECT
			                            JSON_ARRAYAGG(
			                                JSON_OBJECT(
			                                    'id', matching_depts.id,
			                                    'name', matching_depts.name,
			                                    'type', 'DEPARTMENT',
			                                    'image',
			                                        CASE
			                                            WHEN matching_depts.profile_pic IS NULL OR matching_depts.profile_pic = '' THEN ''
			                                            ELSE CONCAT('". SITEURL . COMM_IMAGE_PATH."', matching_depts.profile_pic) #change path at runtime
			                                            END,
			                                    'value', matching_depts.name_matches
			                                )
			                            ) AS dept
			                            FROM
			                                (
			                                SELECT
			                                    s.id,
			                                    s.name,
			                                    s.image AS profile_pic,
			                                    ROUND((LENGTH(s.name) - LENGTH(REPLACE(LOWER(s.name), LOWER('".$search."'), '')))/ LENGTH('".$search."'),0) AS name_matches #change search text at runtime
			                                FROM
			                                    departments s
			                                HAVING name_matches > 0
			                                ) AS matching_depts
			                            )
			                        ,
			                        JSON_ARRAY()
			                        )
			                    ),
			                    JSON_OBJECT(
			                        'name', 'Stories',
			                        'type', 'STORIES',
			                        'image', 'Stories.png',
			                        'children', IFNULL (
			                            (SELECT
			                            JSON_ARRAYAGG(
			                                JSON_OBJECT(
			                                    'id', matching_stories.id,
			                                    'name', matching_stories.name,
			                                    'type', 'STORY',
			                                    'image',
			                                        CASE
			                                            WHEN matching_stories.profile_pic IS NULL OR matching_stories.profile_pic = '' THEN ''
			                                            ELSE CONCAT('". SITEURL . STORY_IMAGE_PATH."', matching_stories.profile_pic) #change path at runtime
			                                            END,
			                                    'value', name_matches + summary_matches + story_matches,
			                                    'name_matches', name_matches,
			                                    'summary_matches', summary_matches,
			                                    'story_matches', story_matches
			                                )
			                            ) AS story
			                            FROM
			                                (
			                                SELECT
			                                    s.id,
			                                    s.name,
			                                    s.image AS profile_pic,
			                                    ROUND((LENGTH(s.name) - LENGTH(REPLACE(LOWER(s.name), LOWER('".$search."'), '')))/ LENGTH('".$search."'),0) AS name_matches, #change search text at runtime
			                                    ROUND((LENGTH(s.summary) - LENGTH(REPLACE(LOWER(s.summary), LOWER('".$search."'), '')))/ LENGTH('".$search."'),0) AS summary_matches, #change search text at runtime
			                                    ROUND((LENGTH(s.story) - LENGTH(REPLACE(LOWER(s.story), LOWER('".$search."'), '')))/ LENGTH('".$search."'),0) AS story_matches #change search text at runtime
			                                FROM
			                                    stories s
			                                HAVING (name_matches + summary_matches + story_matches) > 0
			                                ) AS matching_stories
			                            )
			                        ,
			                        JSON_ARRAY()
			                        )
			                    )
			                )
			        ) AS searchResults
				";

		$data = $this->UserPermission->query($query);

		$view->set('data', $data[0][0]['searchResults']);
		$html = $view->render('search_json');
		echo json_encode($html);
		exit;
	}

	public function get_json_skills() {
        $this->layout = false;
        $response = ['success' => false, 'content' => []];
        $post = [];
        $view = new View($this, false);
		$view->viewPath = 'Subdomains';
        if ($this->request->is('post') || $this->request->is('put')) {
        	$post = $this->request->data;
        	$competency_type = $post["competency_type"];
            $select_from = $post["competency_from_1"];
            $specific_items_1 = $post["specific_items_1"];
            $people_from = $post["competency_from_2"];
            $specific_items_2 = $post["specific_items_2"];

            /* INFORMATION */
    		// $cf_1_sql = PEOPLE FROM
    		// $cf_2_sql = SHOW COMPS FROM PEOPLE
    		// $cf_3_sql = SHOW COMPS FROM
    		if(!empty($competency_type) && $competency_type == 'skills') {
    			$cf_1_sql = $cf_4_sql = '';
    			if(!empty($people_from) && $people_from == 'community'){
    				$cf_1_sql = "SELECT
								    u.id AS user_id
								FROM
								    users u
								WHERE
								    u.role_id = 2 AND
								    u.status = 1 AND
								    u.is_deleted = 0";
    			}
    			else if(!empty($people_from) && $people_from == 'all_projects'){
    				$cf_1_sql = "SELECT DISTINCT
								    up1.user_id
								FROM
								    user_permissions up1
								INNER JOIN user_permissions up2 ON
								    up1.project_id = up2.project_id
								WHERE
								    up1.workspace_id IS NULL AND
								    up2.user_id = ".$this->user_id;
								    /*." AND
								    up2.role IN ('Creator', 'Owner', 'Group Owner') AND
								    u.role_id = 2 AND
								    u.status = 1 AND
								    u.is_deleted = 0";*/
    			}
    			else if(!empty($people_from) && $people_from == 'created_projects'){
    				$cf_1_sql = "SELECT DISTINCT
								    up1.user_id
								FROM
								    user_permissions up1
								INNER JOIN user_permissions up2 ON
								    up1.project_id = up2.project_id
								WHERE
								    up1.workspace_id IS NULL AND
								    up2.user_id = ".$this->user_id." AND
								    up2.role = 'Creator'";
    			}
    			else if(!empty($people_from) && $people_from == 'owner_projects'){
    				$cf_1_sql = "SELECT DISTINCT
								    up1.user_id
								FROM
								    user_permissions up1
								INNER JOIN user_permissions up2 ON
								    up1.project_id = up2.project_id
								WHERE
								    up1.workspace_id IS NULL AND
								    up2.user_id = ".$this->user_id." AND
								    up2.role IN ('Creator', 'Owner', 'Group Owner')";
    			}
    			else if(!empty($people_from) && $people_from == 'shared_projects'){
    				$cf_1_sql = "SELECT DISTINCT
								    up1.user_id
								FROM
								    user_permissions up1
								INNER JOIN user_permissions up2 ON
								    up1.project_id = up2.project_id
								WHERE
								    up1.workspace_id IS NULL AND
								    up2.user_id = ".$this->user_id." AND
								    up2.role IN ('Sharer', 'Group Sharer')";
    			}
    			else if(!empty($people_from) && ($people_from == 'organizations' || $people_from == 'locations' || $people_from == 'departments' || $people_from == 'users' || $people_from == 'competencies' || $people_from == 'project')){
    				if(!empty($specific_items_2)){
    					$people_from_ids = implode(',', $specific_items_2);
    					if($people_from == 'organizations') {
	    					$cf_1_sql = "SELECT
									    u.id AS user_id
									FROM
									    users u
									LEFT JOIN user_details ud ON
									    u.id = ud.user_id
									WHERE
									    u.role_id = 2 AND
									    u.status = 1 AND
									    u.is_deleted = 0 AND
									    ud.organization_id IN
									    	(
									            $people_from_ids
									        )";
    					}
    					else if($people_from == 'locations'){
	    					$cf_1_sql = "SELECT
										    u.id AS user_id
										FROM
										    users u
										LEFT JOIN user_details ud ON
										    u.id = ud.user_id
										WHERE
										    u.role_id = 2 AND
										    u.status = 1 AND
										    u.is_deleted = 0 AND
										    ud.location_id IN
									    	(
									            $people_from_ids
									        )";
    					}
    					else if($people_from == 'departments'){
	    					$cf_1_sql = "SELECT
										    u.id AS user_id
										FROM
										    users u
										LEFT JOIN user_details ud ON
										    u.id = ud.user_id
										WHERE
										    u.role_id = 2 AND
										    u.status = 1 AND
										    u.is_deleted = 0 AND
										    ud.department_id IN
									    	(
									            $people_from_ids
									        )";
    					}
    					else if($people_from == 'users'){
	    					$cf_1_sql = "SELECT
										    u.id AS user_id
										FROM
										    users u
										WHERE
										    u.role_id = 2 AND
										    u.status = 1 AND
										    u.is_deleted = 0 AND
										    u.id IN
									    	(
									            $people_from_ids
									        )";
    					}
    					else if($people_from == 'competencies'){
	    					$cf_1_sql = "SELECT DISTINCT
										    u.id AS user_id
										FROM
										    users u
										LEFT JOIN user_skills us ON
										    u.id = us.user_id
										WHERE
										    u.role_id = 2 AND
										    u.status = 1 AND
										    u.is_deleted = 0 AND
										    us.skill_id IN
									    	(
									            $people_from_ids
									        )";
    					}
    					else if($people_from == 'project'){
    						$cf_1_sql = "SELECT DISTINCT
										    up.user_id
										FROM
										    user_permissions up
										WHERE
										    up.workspace_id IS NULL AND
										    up.project_id IN
										        (
										            $people_from_ids
										        )";
    					}
    				}
    			}
    			/*********/

    			$cf_2_sql = $cf_3_sql = '';
    			if(!empty($select_from) && $select_from == 'community'){
    				$cf_2_sql = "SELECT
								    skills.id AS skill_id,
								    user_skills.user_id,
								    skill_details.user_level,
								    skill_details.user_experience
								FROM skills
								INNER JOIN user_skills ON
								    skills.id = user_skills.skill_id
								LEFT JOIN skill_details ON
									user_skills.user_id = skill_details.user_id AND
								    user_skills.skill_id = skill_details.skill_id";
				    $cf_3_sql = "SELECT
								    skills.id,
								    skills.title
								FROM skills";
    			}
    			else if(!empty($select_from) && $select_from == 'all_projects'){
    				$cf_2_sql = "SELECT DISTINCT
								    project_skills.skill_id,
								    user_skills.user_id,
								    skill_details.user_level,
								    skill_details.user_experience
								FROM
								    user_permissions
								INNER JOIN project_skills ON
								    user_permissions.project_id = project_skills.project_id
								INNER JOIN user_skills ON
								    project_skills.skill_id = user_skills.skill_id
								LEFT JOIN skill_details ON
									user_skills.user_id = skill_details.user_id AND
								    user_skills.skill_id = skill_details.skill_id
								WHERE
								    user_permissions.workspace_id IS NULL AND
								    user_permissions.user_id = ".$this->user_id;
    				$cf_3_sql = "SELECT DISTINCT
								    project_skills.skill_id AS id,
								    skills.title
								FROM
								    user_permissions
								INNER JOIN project_skills ON
								    user_permissions.project_id = project_skills.project_id
								LEFT JOIN skills ON
								    project_skills.skill_id = skills.id
								WHERE
								    user_permissions.workspace_id IS NULL AND
								    user_permissions.user_id = ".$this->user_id;
    			}
    			else if(!empty($select_from) && $select_from == 'created_projects'){
    				$cf_2_sql = "SELECT DISTINCT
								    project_skills.skill_id,
								    user_skills.user_id,
								    skill_details.user_level,
								    skill_details.user_experience
								FROM
								    user_permissions
								INNER JOIN project_skills ON
								    user_permissions.project_id = project_skills.project_id
								INNER JOIN user_skills ON
								    project_skills.skill_id = user_skills.skill_id
								LEFT JOIN skill_details ON
									user_skills.user_id = skill_details.user_id AND
								    user_skills.skill_id = skill_details.skill_id
								WHERE
								    user_permissions.workspace_id IS NULL AND
								    user_permissions.user_id = ".$this->user_id." AND
								    user_permissions.role = 'Creator'";
    				$cf_3_sql = "SELECT DISTINCT
								    project_skills.skill_id AS id,
								    skills.title
								FROM
								    user_permissions
								INNER JOIN project_skills ON
								    user_permissions.project_id = project_skills.project_id
								LEFT JOIN skills ON
								    project_skills.skill_id = skills.id
								WHERE
								    user_permissions.workspace_id IS NULL AND
								    user_permissions.user_id = ".$this->user_id." AND
								    user_permissions.role = 'Creator'";
    			}
    			else if(!empty($select_from) && $select_from == 'owner_projects'){
    				$cf_2_sql = "SELECT DISTINCT
								    project_skills.skill_id,
								    user_skills.user_id,
								    skill_details.user_level,
								    skill_details.user_experience
								FROM
								    user_permissions
								INNER JOIN project_skills ON
								    user_permissions.project_id = project_skills.project_id
								INNER JOIN user_skills ON
								    project_skills.skill_id = user_skills.skill_id
								LEFT JOIN skill_details ON
									user_skills.user_id = skill_details.user_id AND
								    user_skills.skill_id = skill_details.skill_id
								WHERE
								    user_permissions.workspace_id IS NULL AND
								    user_permissions.user_id = ".$this->user_id." AND
								    user_permissions.role IN ('Creator', 'Owner', 'Group Owner')";
    				$cf_3_sql = "SELECT DISTINCT
								    project_skills.skill_id AS id,
								    skills.title
								FROM
								    user_permissions
								INNER JOIN project_skills ON
								    user_permissions.project_id = project_skills.project_id
								LEFT JOIN skills ON
								    project_skills.skill_id = skills.id
								WHERE
								    user_permissions.workspace_id IS NULL AND
								    user_permissions.user_id = ".$this->user_id." AND
								    user_permissions.role IN ('Creator', 'Owner', 'Group Owner')";
    			}
    			else if(!empty($select_from) && $select_from == 'shared_projects'){
    				$cf_2_sql = "SELECT DISTINCT
								    project_skills.skill_id,
								    user_skills.user_id,
								    skill_details.user_level,
								    skill_details.user_experience
								FROM
								    user_permissions
								INNER JOIN project_skills ON
								    user_permissions.project_id = project_skills.project_id
								INNER JOIN user_skills ON
								    project_skills.skill_id = user_skills.skill_id
								LEFT JOIN skill_details ON
									user_skills.user_id = skill_details.user_id AND
								    user_skills.skill_id = skill_details.skill_id
								WHERE
								    user_permissions.workspace_id IS NULL AND
								    user_permissions.user_id = ".$this->user_id." AND
								    user_permissions.role IN ('Sharer', 'Group Sharer')";
    				$cf_3_sql = "SELECT DISTINCT
								    project_skills.skill_id AS id,
								    skills.title
								FROM
								    user_permissions
								INNER JOIN project_skills ON
								    user_permissions.project_id = project_skills.project_id
								LEFT JOIN skills ON
								    project_skills.skill_id = skills.id
								WHERE
								    user_permissions.workspace_id IS NULL AND
								    user_permissions.user_id = ".$this->user_id." AND
								    user_permissions.role IN ('Sharer', 'Group Sharer')";
    			}
    			else if(!empty($select_from) && ($select_from == 'organizations' || $select_from == 'locations' || $select_from == 'departments' || $select_from == 'users' || $select_from == 'competencies' || $select_from == 'project')){
    				if(!empty($specific_items_1)){
    					$select_from_ids = implode(',', $specific_items_1);
    					if($select_from == 'organizations'){
	    					$cf_2_sql = "SELECT DISTINCT
										    organization_skills.skill_id,
										    user_skills.user_id,
										    skill_details.user_level,
										    skill_details.user_experience
										FROM
											organization_skills
										INNER JOIN user_skills ON
										    organization_skills.skill_id = user_skills.skill_id
										LEFT JOIN skill_details ON
											user_skills.user_id = skill_details.user_id AND
										    user_skills.skill_id = skill_details.skill_id
										WHERE
											organization_skills.organization_id IN
									    	(
									            $select_from_ids
									        )";
    						$cf_3_sql = "SELECT DISTINCT
										    organization_skills.skill_id AS id,
										    skills.title
										FROM
											organization_skills
										LEFT JOIN skills ON
										    organization_skills.skill_id = skills.id
										WHERE
											organization_skills.organization_id IN
										        (
										            $select_from_ids
										        )";
    					}
    					else if($select_from == 'locations'){
	    					$cf_2_sql = "SELECT DISTINCT
										    location_skills.skill_id,
										    user_skills.user_id,
										    skill_details.user_level,
										    skill_details.user_experience
										FROM
											location_skills
										INNER JOIN user_skills ON
										    location_skills.skill_id = user_skills.skill_id
										LEFT JOIN skill_details ON
											user_skills.user_id = skill_details.user_id AND
										    user_skills.skill_id = skill_details.skill_id
										WHERE
											location_skills.location_id IN
									    	(
									            $select_from_ids
									        )";
    						$cf_3_sql = "SELECT DISTINCT
										    location_skills.skill_id AS id,
										    skills.title
										FROM
											location_skills
										LEFT JOIN skills ON
										    location_skills.skill_id = skills.id
										WHERE
											location_skills.location_id IN
										        (
										            $select_from_ids
										        )";
    					}
    					else if($select_from == 'departments'){
	    					$cf_2_sql = "SELECT DISTINCT
										    department_skills.skill_id,
										    user_skills.user_id,
										    skill_details.user_level,
										    skill_details.user_experience
										FROM
											department_skills
										INNER JOIN user_skills ON
										    department_skills.skill_id = user_skills.skill_id
										LEFT JOIN skill_details ON
											user_skills.user_id = skill_details.user_id AND
										    user_skills.skill_id = skill_details.skill_id
										WHERE
											department_skills.department_id IN
									    	(
									            $select_from_ids
									        )";
    						$cf_3_sql = "SELECT DISTINCT
										    department_skills.skill_id AS id,
										    skills.title
										FROM
											department_skills
										LEFT JOIN skills ON
										    department_skills.skill_id = skills.id
										WHERE
											department_skills.department_id IN
										        (
										            $select_from_ids
										        )";
    					}
    					else if($select_from == 'users'){
	    					$cf_2_sql = "SELECT DISTINCT
										    user_skills.skill_id,
										   	user_skills.user_id,
										    skill_details.user_level,
										    skill_details.user_experience
										FROM user_skills us1
										LEFT JOIN user_skills ON
										    us1.skill_id = user_skills.skill_id
										LEFT JOIN skill_details ON
											user_skills.user_id = skill_details.user_id AND
										    user_skills.skill_id = skill_details.skill_id
										WHERE us1.user_id IN
									    	(
									            $select_from_ids
									        )";
    						$cf_3_sql = "SELECT DISTINCT
										    us.skill_id AS id,
										    s.title
										FROM user_skills us
										LEFT JOIN skills s ON
											us.skill_id = s.id
										WHERE us.user_id IN
											(
										        $select_from_ids
										    )";
    					}
    					else if($select_from == 'competencies'){
	    					$cf_2_sql = "SELECT
										    skills.id AS skill_id,
										    user_skills.user_id,
										    skill_details.user_level,
										    skill_details.user_experience
										FROM skills
										INNER JOIN user_skills ON
										    skills.id = user_skills.skill_id
										LEFT JOIN skill_details ON
											user_skills.user_id = skill_details.user_id AND
										    user_skills.skill_id = skill_details.skill_id
										WHERE skills.id IN
									    	(
									            $select_from_ids
									        )";
    						$cf_3_sql = "SELECT
										    skills.id,
										    skills.title
										FROM skills
										WHERE skills.id IN
											(
										        $select_from_ids
										    )";
    					}
    					else if($select_from == 'project'){
	    					$cf_2_sql = "SELECT DISTINCT
										    project_skills.skill_id,
										    user_skills.user_id,
										    skill_details.user_level,
										    skill_details.user_experience
										FROM
										    user_permissions
										INNER JOIN project_skills ON
										    user_permissions.project_id = project_skills.project_id
										INNER JOIN user_skills ON
										    project_skills.skill_id = user_skills.skill_id
										LEFT JOIN skill_details ON
											user_skills.user_id = skill_details.user_id AND
										    user_skills.skill_id = skill_details.skill_id
										WHERE
										    user_permissions.workspace_id IS NULL AND
										    user_permissions.user_id = ".$this->user_id." AND
										    user_permissions.project_id IN
									    	(
									            $select_from_ids
									        )";
    						$cf_3_sql = "SELECT DISTINCT
										    project_skills.skill_id AS id,
										    skills.title
										FROM
										    user_permissions
										INNER JOIN project_skills ON
										    user_permissions.project_id = project_skills.project_id
										LEFT JOIN skills ON
										    project_skills.skill_id = skills.id
										WHERE
										    user_permissions.workspace_id IS NULL AND
										    user_permissions.user_id = ".$this->user_id." AND
										    user_permissions.project_id IN
										        (
										            $select_from_ids
										        )";
    					}
    				}
    			}
    			/*********/
    			$query = "SELECT
							JSON_OBJECT(
								'id', 'All Skills',
								'name', 'All Skills',
								'type', 'All Skills',
						        'children',
						        	JSON_ARRAYAGG(
						                JSON_OBJECT(
						                    'id', id,
						                    'name', title,
						                    'type', IF(id = 'No Skills', 'No Skills', 'Skill'),
						                    'value', 1,
						                    'children', IF(id = 'No Skills', IFNULL(
						               			(
													SELECT
													JSON_ARRAYAGG(
														JSON_OBJECT(
															'id',
															no_skills_users.user_id,
															'name', CONCAT(no_skills_users.first_name,' ',no_skills_users.last_name),
															'image', case when profile_pic IS NULL or profile_pic = ''
																		then CONCAT('".SITEURL."','images/placeholders/user/user_1.png')
																		else CONCAT('".SITEURL.USER_PIC_PATH."', no_skills_users.profile_pic)
																	end,
															'type', 'People',
															'value', 1
														)
													) AS noskillsusers
													FROM
													(
														SELECT
															people_from.user_id,
															user_details.last_name,
															user_details.first_name,
															user_details.profile_pic
														FROM
														(
															$cf_1_sql
														) AS people_from
														LEFT JOIN
														(
															$cf_2_sql
														) AS show_skills_from_people
														ON
															people_from.user_id = show_skills_from_people.user_id
														LEFT JOIN user_details ON
															people_from.user_id = user_details.user_id
														WHERE show_skills_from_people.user_id IS NULL
													) AS no_skills_users
												)
						    					, JSON_ARRAY())
						               		, IFNULL(skill_levels.userlevel, JSON_ARRAY()))
						            	)
						            )
						    ) AS all_skills
						FROM
						(
							$cf_3_sql
							UNION
							SELECT 'No Skills','No Skills'
						) AS skills_noskills
						LEFT JOIN
						(
							#levels
							SELECT
								distinct_skill_detail_levels.skill_id,
								distinct_skill_detail_levels.user_level,
								JSON_ARRAYAGG(
									JSON_OBJECT(
										'name', distinct_skill_detail_levels.user_level,
										'type', 'Level',
										'children', IFNULL(level_experiences.userexperience, JSON_ARRAY())
									)
								) AS userlevel
							FROM
							(
								SELECT DISTINCT
									skill_id,
									user_level
								FROM
								(
									$cf_1_sql
								) AS people_from
								INNER JOIN
								(
									$cf_2_sql
								) AS show_skills_from_people
								ON
									people_from.user_id = show_skills_from_people.user_id
							) AS distinct_skill_detail_levels
							LEFT JOIN
							(
								#experiences
								SELECT
									distinct_skill_detail_experiences.skill_id,
									distinct_skill_detail_experiences.user_level,
									distinct_skill_detail_experiences.user_experience,
									JSON_ARRAYAGG(
										JSON_OBJECT(
											'name', CONCAT(distinct_skill_detail_experiences.user_experience, IF(distinct_skill_detail_experiences.user_experience='1',' Year',' Years')),
											'type', 'Experience',
											'children', IFNULL(experience_users.experienceuser, JSON_ARRAY())
										)
									) AS userexperience
								FROM
								(
									SELECT DISTINCT
										skill_id,
										user_level,
										user_experience
									FROM
									(
										$cf_1_sql
									) AS people_from
									INNER JOIN
									(
										$cf_2_sql
									) AS show_skills_from_people
									ON
										people_from.user_id = show_skills_from_people.user_id
								) AS distinct_skill_detail_experiences
								LEFT JOIN
								(
									#people
									SELECT
										skill_id,
										user_level,
										user_experience,
										people_from.user_id,
										JSON_ARRAYAGG(
											JSON_OBJECT(
												'id',
												user_details.user_id,
												'name', CONCAT(user_details.first_name,' ',user_details.last_name),
												'image', case when user_details.profile_pic IS NULL or user_details.profile_pic = ''
															then CONCAT('".SITEURL."','images/placeholders/user/user_1.png')
															else CONCAT('".SITEURL.USER_PIC_PATH."', user_details.profile_pic)
															end,
												'type', 'People',
												'value', 1
											)
										) AS experienceuser
									FROM
									(
										$cf_1_sql
									) AS people_from
									INNER JOIN
									(
										$cf_2_sql
									) AS show_skills_from_people
									ON
										people_from.user_id = show_skills_from_people.user_id
									LEFT JOIN user_details ON
										people_from.user_id = user_details.user_id
									GROUP BY show_skills_from_people.skill_id, show_skills_from_people.user_level, show_skills_from_people.user_experience
								) AS experience_users
								ON  distinct_skill_detail_experiences.skill_id = experience_users.skill_id AND distinct_skill_detail_experiences.user_level = experience_users.user_level AND distinct_skill_detail_experiences.user_experience = experience_users.user_experience
								GROUP BY distinct_skill_detail_experiences.skill_id, distinct_skill_detail_experiences.user_level

								) AS level_experiences
								ON distinct_skill_detail_levels.skill_id = level_experiences.skill_id AND distinct_skill_detail_levels.user_level = level_experiences.user_level
								GROUP BY distinct_skill_detail_levels.skill_id

						) AS skill_levels
						ON skills_noskills.id = skill_id";

				$data = $this->UserPermission->query($query);
				// pr($data, 1);
				$view->set('data', $data[0][0]['all_skills']);
    		}
    		else if(!empty($competency_type) && $competency_type == 'subjects') {

    			$cf_1_sql = $cf_4_sql = '';
    			if(!empty($people_from) && $people_from == 'community'){
    				$cf_1_sql = "SELECT
								    u.id AS user_id
								FROM
								    users u
								WHERE
								    u.role_id = 2 AND
								    u.status = 1 AND
								    u.is_deleted = 0";
    			}
    			else if(!empty($people_from) && $people_from == 'all_projects'){
    				$cf_1_sql = "SELECT DISTINCT
								    up1.user_id
								FROM
								    user_permissions up1
								INNER JOIN user_permissions up2 ON
								    up1.project_id = up2.project_id
								WHERE
								    up1.workspace_id IS NULL AND
								    up2.user_id = ".$this->user_id;
    			}
    			else if(!empty($people_from) && $people_from == 'created_projects'){
    				$cf_1_sql = "SELECT DISTINCT
								    up1.user_id
								FROM
								    user_permissions up1
								INNER JOIN user_permissions up2 ON
								    up1.project_id = up2.project_id
								WHERE
								    up1.workspace_id IS NULL AND
								    up2.user_id = ".$this->user_id." AND
								    up2.role = 'Creator'";
    			}
    			else if(!empty($people_from) && $people_from == 'owner_projects'){
    				$cf_1_sql = "SELECT DISTINCT
								    up1.user_id
								FROM
								    user_permissions up1
								INNER JOIN user_permissions up2 ON
								    up1.project_id = up2.project_id
								WHERE
								    up1.workspace_id IS NULL AND
								    up2.user_id = ".$this->user_id." AND
								    up2.role IN ('Creator', 'Owner', 'Group Owner')";
    			}
    			else if(!empty($people_from) && $people_from == 'shared_projects'){
    				$cf_1_sql = "SELECT DISTINCT
								    up1.user_id
								FROM
								    user_permissions up1
								INNER JOIN user_permissions up2 ON
								    up1.project_id = up2.project_id
								WHERE
								    up1.workspace_id IS NULL AND
								    up2.user_id = ".$this->user_id." AND
								    up2.role IN ('Sharer', 'Group Sharer')";
    			}
    			else if(!empty($people_from) && ($people_from == 'organizations' || $people_from == 'locations' || $people_from == 'departments' || $people_from == 'users' || $people_from == 'competencies' || $people_from == 'project')){
    				if(!empty($specific_items_2)){
    					$people_from_ids = implode(',', $specific_items_2);
    					if($people_from == 'organizations') {
	    					$cf_1_sql = "SELECT
										    u.id AS user_id
										FROM
										    users u
										LEFT JOIN user_details ud ON
										    u.id = ud.user_id
										WHERE
										    u.role_id = 2 AND
										    u.status = 1 AND
										    u.is_deleted = 0 AND
										    ud.organization_id IN
										    	(
										            $people_from_ids
										        )";
    					}
    					else if($people_from == 'locations'){
	    					$cf_1_sql = "SELECT
										    u.id AS user_id
										FROM
										    users u
										LEFT JOIN user_details ud ON
										    u.id = ud.user_id
										WHERE
										    u.role_id = 2 AND
										    u.status = 1 AND
										    u.is_deleted = 0 AND
										    ud.location_id IN
										    	(
										            $people_from_ids
										        )";
    					}
    					else if($people_from == 'departments'){
	    					$cf_1_sql = "SELECT
										    u.id AS user_id
										FROM
										    users u
										LEFT JOIN user_details ud ON
										    u.id = ud.user_id
										WHERE
										    u.role_id = 2 AND
										    u.status = 1 AND
										    u.is_deleted = 0 AND
										    ud.department_id IN
										    	(
										            $people_from_ids
										        )";
    					}
    					else if($people_from == 'users'){
	    					$cf_1_sql = "SELECT
										    u.id AS user_id
										FROM
										    users u
										WHERE
										    u.role_id = 2 AND
										    u.status = 1 AND
										    u.is_deleted = 0 AND
										    u.id IN
										    	(
										            $people_from_ids
										        )";
    					}
    					else if($people_from == 'competencies'){
	    					$cf_1_sql = "SELECT DISTINCT
										    u.id AS user_id
										FROM
										    users u
										LEFT JOIN user_subjects us ON
										    u.id = us.user_id
										WHERE
										    u.role_id = 2 AND
										    u.status = 1 AND
										    u.is_deleted = 0 AND
										    us.subject_id IN
										    	(
										            $people_from_ids
										        )";
    					}
    					else if($people_from == 'project'){
    						$cf_1_sql = "SELECT DISTINCT
										    up.user_id
										FROM
										    user_permissions up
										WHERE
										    up.workspace_id IS NULL AND
										    up.project_id IN
										        (
										            $people_from_ids
										        )";
    					}
    				}
    			}
    			/*********/

    			$cf_2_sql = $cf_3_sql = '';
    			if(!empty($select_from) && $select_from == 'community'){
    				$cf_2_sql = "SELECT
								    subjects.id AS subject_id,
								    user_subjects.user_id,
								    subject_details.user_level,
								    subject_details.user_experience
								FROM subjects
								INNER JOIN user_subjects ON
								    subjects.id = user_subjects.subject_id
								LEFT JOIN subject_details ON
									user_subjects.user_id = subject_details.user_id AND
								    user_subjects.subject_id = subject_details.subject_id";
				    $cf_3_sql = "SELECT
								    subjects.id,
								    subjects.title
								FROM subjects";
    			}
    			else if(!empty($select_from) && $select_from == 'all_projects'){
    				$cf_2_sql = "SELECT DISTINCT
								    project_subjects.subject_id,
								    user_subjects.user_id,
								    subject_details.user_level,
								    subject_details.user_experience
								FROM
								    user_permissions
								INNER JOIN project_subjects ON
								    user_permissions.project_id = project_subjects.project_id
								INNER JOIN user_subjects ON
								    project_subjects.subject_id = user_subjects.subject_id
								LEFT JOIN subject_details ON
									user_subjects.user_id = subject_details.user_id AND
								    user_subjects.subject_id = subject_details.subject_id
								WHERE
								    user_permissions.workspace_id IS NULL AND
								    user_permissions.user_id = ".$this->user_id;
    				$cf_3_sql = "SELECT DISTINCT
								    project_subjects.subject_id AS id,
								    subjects.title
								FROM
								    user_permissions
								INNER JOIN project_subjects ON
								    user_permissions.project_id = project_subjects.project_id
								LEFT JOIN subjects ON
								    project_subjects.subject_id = subjects.id
								WHERE
								    user_permissions.workspace_id IS NULL AND
								    user_permissions.user_id = ".$this->user_id;
    			}
    			else if(!empty($select_from) && $select_from == 'created_projects'){
    				$cf_2_sql = "SELECT DISTINCT
								    project_subjects.subject_id,
								    user_subjects.user_id,
								    subject_details.user_level,
								    subject_details.user_experience
								FROM
								    user_permissions
								INNER JOIN project_subjects ON
								    user_permissions.project_id = project_subjects.project_id
								INNER JOIN user_subjects ON
								    project_subjects.subject_id = user_subjects.subject_id
								LEFT JOIN subject_details ON
									user_subjects.user_id = subject_details.user_id AND
								    user_subjects.subject_id = subject_details.subject_id
								WHERE
								    user_permissions.workspace_id IS NULL AND
								    user_permissions.user_id = ".$this->user_id." AND
								    user_permissions.role = 'Creator'";
    				$cf_3_sql = "SELECT DISTINCT
								    project_subjects.subject_id AS id,
								    subjects.title
								FROM
								    user_permissions
								INNER JOIN project_subjects ON
								    user_permissions.project_id = project_subjects.project_id
								LEFT JOIN subjects ON
								    project_subjects.subject_id = subjects.id
								WHERE
								    user_permissions.workspace_id IS NULL AND
								    user_permissions.user_id = ".$this->user_id." AND
								    user_permissions.role = 'Creator'";
    			}
    			else if(!empty($select_from) && $select_from == 'owner_projects'){
    				$cf_2_sql = "SELECT DISTINCT
								    project_subjects.subject_id,
								    user_subjects.user_id,
								    subject_details.user_level,
								    subject_details.user_experience
								FROM
								    user_permissions
								INNER JOIN project_subjects ON
								    user_permissions.project_id = project_subjects.project_id
								INNER JOIN user_subjects ON
								    project_subjects.subject_id = user_subjects.subject_id
								LEFT JOIN subject_details ON
									user_subjects.user_id = subject_details.user_id AND
								    user_subjects.subject_id = subject_details.subject_id
								WHERE
								    user_permissions.workspace_id IS NULL AND
								    user_permissions.user_id = ".$this->user_id." AND
								    user_permissions.role IN ('Creator', 'Owner', 'Group Owner')";
    				$cf_3_sql = "SELECT DISTINCT
								    project_subjects.subject_id AS id,
								    subjects.title
								FROM
								    user_permissions
								INNER JOIN project_subjects ON
								    user_permissions.project_id = project_subjects.project_id
								LEFT JOIN subjects ON
								    project_subjects.subject_id = subjects.id
								WHERE
								    user_permissions.workspace_id IS NULL AND
								    user_permissions.user_id = ".$this->user_id." AND
								    user_permissions.role IN ('Creator', 'Owner', 'Group Owner')";
    			}
    			else if(!empty($select_from) && $select_from == 'shared_projects'){
    				$cf_2_sql = "SELECT DISTINCT
								    project_subjects.subject_id,
								    user_subjects.user_id,
								    subject_details.user_level,
								    subject_details.user_experience
								FROM
								    user_permissions
								INNER JOIN project_subjects ON
								    user_permissions.project_id = project_subjects.project_id
								INNER JOIN user_subjects ON
								    project_subjects.subject_id = user_subjects.subject_id
								LEFT JOIN subject_details ON
									user_subjects.user_id = subject_details.user_id AND
								    user_subjects.subject_id = subject_details.subject_id
								WHERE
								    user_permissions.workspace_id IS NULL AND
								    user_permissions.user_id = ".$this->user_id." AND
								    user_permissions.role IN ('Sharer', 'Group Sharer')";
    				$cf_3_sql = "SELECT DISTINCT
								    project_subjects.subject_id AS id,
								    subjects.title
								FROM
								    user_permissions
								INNER JOIN project_subjects ON
								    user_permissions.project_id = project_subjects.project_id
								LEFT JOIN subjects ON
								    project_subjects.subject_id = subjects.id
								WHERE
								    user_permissions.workspace_id IS NULL AND
								    user_permissions.user_id = ".$this->user_id." AND
								    user_permissions.role IN ('Sharer', 'Group Sharer')";
    			}
    			else if(!empty($select_from) && ($select_from == 'organizations' || $select_from == 'locations' || $select_from == 'departments' || $select_from == 'users' || $select_from == 'competencies' || $select_from == 'project')){
    				if(!empty($specific_items_1)){
    					$select_from_ids = implode(',', $specific_items_1);
    					if($select_from == 'organizations'){
	    					$cf_2_sql = "SELECT DISTINCT
										    organization_subjects.subject_id,
										    user_subjects.user_id,
										    subject_details.user_level,
										    subject_details.user_experience
										FROM
											organization_subjects
										INNER JOIN user_subjects ON
										    organization_subjects.subject_id = user_subjects.subject_id
										LEFT JOIN subject_details ON
											user_subjects.user_id = subject_details.user_id AND
										    user_subjects.subject_id = subject_details.subject_id
										WHERE
											organization_subjects.organization_id IN
										        (
										            $select_from_ids
										        )";
    						$cf_3_sql = "SELECT DISTINCT
										    organization_subjects.subject_id AS id,
										    subjects.title
										FROM
											organization_subjects
										LEFT JOIN subjects ON
										    organization_subjects.subject_id = subjects.id
										WHERE
											organization_subjects.organization_id IN
										        (
										            $select_from_ids
										        )";
    					}
    					else if($select_from == 'locations'){
	    					$cf_2_sql = "SELECT DISTINCT
										    location_subjects.subject_id,
										    user_subjects.user_id,
										    subject_details.user_level,
										    subject_details.user_experience
										FROM
											location_subjects
										INNER JOIN user_subjects ON
										    location_subjects.subject_id = user_subjects.subject_id
										LEFT JOIN subject_details ON
											user_subjects.user_id = subject_details.user_id AND
										    user_subjects.subject_id = subject_details.subject_id
										WHERE
											location_subjects.location_id IN
										        (
										            $select_from_ids
										        )";
    						$cf_3_sql = "SELECT DISTINCT
										    location_subjects.subject_id AS id,
										    subjects.title
										FROM
											location_subjects
										LEFT JOIN subjects ON
										    location_subjects.subject_id = subjects.id
										WHERE
											location_subjects.location_id IN
										        (
										            $select_from_ids
										        )";
    					}
    					else if($select_from == 'departments'){
	    					$cf_2_sql = "SELECT DISTINCT
										    department_subjects.subject_id,
										    user_subjects.user_id,
										    subject_details.user_level,
										    subject_details.user_experience
										FROM
											department_subjects
										INNER JOIN user_subjects ON
										    department_subjects.subject_id = user_subjects.subject_id
										LEFT JOIN subject_details ON
											user_subjects.user_id = subject_details.user_id AND
										    user_subjects.subject_id = subject_details.subject_id
										WHERE
											department_subjects.department_id IN
										        (
										            $select_from_ids
										        )";
    						$cf_3_sql = "SELECT DISTINCT
										    department_subjects.subject_id AS id,
										    subjects.title
										FROM
											department_subjects
										LEFT JOIN subjects ON
										    department_subjects.subject_id = subjects.id
										WHERE
											department_subjects.department_id IN
										        (
										            $select_from_ids
										        )";
    					}
    					else if($select_from == 'users'){
	    					$cf_2_sql = "SELECT DISTINCT
										    user_subjects.subject_id,
										   	user_subjects.user_id,
										    subject_details.user_level,
										    subject_details.user_experience
										FROM user_subjects us1
										LEFT JOIN user_subjects ON
										    us1.subject_id = user_subjects.subject_id
										LEFT JOIN subject_details ON
											user_subjects.user_id = subject_details.user_id AND
										    user_subjects.subject_id = subject_details.subject_id
										WHERE us1.user_id IN
											(
										        $select_from_ids
										    )";
    						$cf_3_sql = "SELECT DISTINCT
										    us.subject_id AS id,
										    s.title
										FROM user_subjects us
										LEFT JOIN subjects s ON
											us.subject_id = s.id
										WHERE us.user_id IN
											(
										        $select_from_ids
										    )";
    					}
    					else if($select_from == 'competencies'){
	    					$cf_2_sql = "SELECT
										    subjects.id AS subject_id,
										    user_subjects.user_id,
										    subject_details.user_level,
										    subject_details.user_experience
										FROM subjects
										INNER JOIN user_subjects ON
										    subjects.id = user_subjects.subject_id
										LEFT JOIN subject_details ON
											user_subjects.user_id = subject_details.user_id AND
										    user_subjects.subject_id = subject_details.subject_id
										WHERE subjects.id IN
											(
										        $select_from_ids
										    )";
    						$cf_3_sql = "SELECT
										    subjects.id,
										    subjects.title
										FROM subjects
										WHERE subjects.id IN
											(
										        $select_from_ids
										    )";
    					}
    					else if($select_from == 'project'){
	    					$cf_2_sql = "SELECT DISTINCT
										    project_subjects.subject_id,
										    user_subjects.user_id,
										    subject_details.user_level,
										    subject_details.user_experience
										FROM
										    user_permissions
										INNER JOIN project_subjects ON
										    user_permissions.project_id = project_subjects.project_id
										INNER JOIN user_subjects ON
										    project_subjects.subject_id = user_subjects.subject_id
										LEFT JOIN subject_details ON
											user_subjects.user_id = subject_details.user_id AND
										    user_subjects.subject_id = subject_details.subject_id
										WHERE
										    user_permissions.workspace_id IS NULL AND
										    user_permissions.user_id = ".$this->user_id." AND
										    user_permissions.project_id IN
									    	(
									            $select_from_ids
									        )";
    						$cf_3_sql = "SELECT DISTINCT
										    project_subjects.subject_id AS id,
										    subjects.title
										FROM
										    user_permissions
										INNER JOIN project_subjects ON
										    user_permissions.project_id = project_subjects.project_id
										LEFT JOIN subjects ON
										    project_subjects.subject_id = subjects.id
										WHERE
										    user_permissions.workspace_id IS NULL AND
										    user_permissions.user_id = ".$this->user_id." AND
										    user_permissions.project_id IN
										        (
										            $select_from_ids
										        )";
    					}
    				}
    			}
    			$query = "SELECT
						JSON_OBJECT(
							'id', 'All Skills',
							'name', 'All Skills',
							'type', 'All Skills',
					        'children',
					        	JSON_ARRAYAGG(
					                JSON_OBJECT(
					                    'id', id,
					                    'name', IF(id = 'No Skills', 'No Subjects', title),
					                    'type', IF(id = 'No Skills', 'No Skills', 'Skill'),
					                    'value', 1,
					                    'children', IF(id = 'No Skills', IFNULL(
					               			(
												SELECT
												JSON_ARRAYAGG(
													JSON_OBJECT(
														'id',
														no_skills_users.user_id,
														'name', CONCAT(no_skills_users.first_name,' ',no_skills_users.last_name),
														'image', case when profile_pic IS NULL or profile_pic = ''
																	then CONCAT('".SITEURL."','images/placeholders/user/user_1.png')
																	else CONCAT('".SITEURL.USER_PIC_PATH."', no_skills_users.profile_pic)
																end,
														'type', 'People',
														'value', 1
													)
												) AS noskillsusers
												FROM
												(
													SELECT
														people_from.user_id,
														user_details.last_name,
														user_details.first_name,
														user_details.profile_pic
													FROM
													(
														$cf_1_sql
													) AS people_from
													LEFT JOIN
													(
														$cf_2_sql
													) AS show_skills_from_people
													ON
														people_from.user_id = show_skills_from_people.user_id
													LEFT JOIN user_details ON
														people_from.user_id = user_details.user_id
													WHERE show_skills_from_people.user_id IS NULL
												) AS no_skills_users
											)
					    					, JSON_ARRAY())
					               		, IFNULL(skill_levels.userlevel, JSON_ARRAY()))
					            	)
					            )
					    ) AS all_skills
					FROM
					(
						$cf_3_sql
						UNION
						SELECT 'No Skills','No Skills'
					) AS skills_noskills
					LEFT JOIN
					(
						#levels
						SELECT
							distinct_skill_detail_levels.subject_id,
							distinct_skill_detail_levels.user_level,
							JSON_ARRAYAGG(
								JSON_OBJECT(
									'name', distinct_skill_detail_levels.user_level,
									'type', 'Level',
									'children', IFNULL(level_experiences.userexperience, JSON_ARRAY())
								)
							) AS userlevel
						FROM
						(
							SELECT DISTINCT
								subject_id,
								user_level
							FROM
							(
								$cf_1_sql
							) AS people_from
							INNER JOIN
							(
								$cf_2_sql
							) AS show_skills_from_people
							ON
								people_from.user_id = show_skills_from_people.user_id
						) AS distinct_skill_detail_levels
						LEFT JOIN
						(
							#experiences
							SELECT
								distinct_skill_detail_experiences.subject_id,
								distinct_skill_detail_experiences.user_level,
								distinct_skill_detail_experiences.user_experience,
								JSON_ARRAYAGG(
									JSON_OBJECT(
										'name', CONCAT(distinct_skill_detail_experiences.user_experience, IF(distinct_skill_detail_experiences.user_experience='1',' Year',' Years')),
										'type', 'Experience',
										'children', IFNULL(experience_users.experienceuser, JSON_ARRAY())
									)
								) AS userexperience
							FROM
							(
								SELECT DISTINCT
									subject_id,
									user_level,
									user_experience
								FROM
								(
									$cf_1_sql
								) AS people_from
								INNER JOIN
								(
									$cf_2_sql
								) AS show_skills_from_people
								ON
									people_from.user_id = show_skills_from_people.user_id
							) AS distinct_skill_detail_experiences
							LEFT JOIN
							(
								#people
								SELECT
									subject_id,
									user_level,
									user_experience,
									people_from.user_id,
									JSON_ARRAYAGG(
										JSON_OBJECT(
											'id',
											user_details.user_id,
											'name', CONCAT(user_details.first_name,' ',user_details.last_name),
											'image', case when user_details.profile_pic IS NULL or user_details.profile_pic = ''
														then CONCAT('".SITEURL."','images/placeholders/user/user_1.png')
														else CONCAT('".SITEURL.USER_PIC_PATH."', user_details.profile_pic)
														end,
											'type', 'People',
											'value', 1
										)
									) AS experienceuser
								FROM
								(
									$cf_1_sql
								) AS people_from
								INNER JOIN
								(
									$cf_2_sql
								) AS show_skills_from_people
								ON
									people_from.user_id = show_skills_from_people.user_id
								LEFT JOIN user_details ON
									people_from.user_id = user_details.user_id
								GROUP BY show_skills_from_people.subject_id, show_skills_from_people.user_level, show_skills_from_people.user_experience
							) AS experience_users
							ON  distinct_skill_detail_experiences.subject_id = experience_users.subject_id AND distinct_skill_detail_experiences.user_level = experience_users.user_level AND distinct_skill_detail_experiences.user_experience = experience_users.user_experience
							GROUP BY distinct_skill_detail_experiences.subject_id, distinct_skill_detail_experiences.user_level

							) AS level_experiences
							ON distinct_skill_detail_levels.subject_id = level_experiences.subject_id AND distinct_skill_detail_levels.user_level = level_experiences.user_level
							GROUP BY distinct_skill_detail_levels.subject_id

					) AS skill_levels
					ON skills_noskills.id = subject_id";




				$data = $this->UserPermission->query($query);
				// pr($data, 1);
				$view->set('data', $data[0][0]['all_skills']);
    		}
    		else if(!empty($competency_type) && $competency_type == 'domains') {

    			$cf_1_sql = $cf_4_sql = '';
    			if(!empty($people_from) && $people_from == 'community'){
    				$cf_1_sql = "SELECT
								    u.id AS user_id
								FROM
								    users u
								WHERE
								    u.role_id = 2 AND
								    u.status = 1 AND
								    u.is_deleted = 0";
    			}
    			else if(!empty($people_from) && $people_from == 'all_projects'){
    				$cf_1_sql = "SELECT DISTINCT
								    up1.user_id
								FROM
								    user_permissions up1
								INNER JOIN user_permissions up2 ON
								    up1.project_id = up2.project_id
								WHERE
								    up1.workspace_id IS NULL AND
								    up2.user_id = ".$this->user_id;
    			}
    			else if(!empty($people_from) && $people_from == 'created_projects'){
    				$cf_1_sql = "SELECT DISTINCT
								    up1.user_id
								FROM
								    user_permissions up1
								INNER JOIN user_permissions up2 ON
								    up1.project_id = up2.project_id
								WHERE
								    up1.workspace_id IS NULL AND
								    up2.user_id = ".$this->user_id." AND
								    up2.role = 'Creator'";
    			}
    			else if(!empty($people_from) && $people_from == 'owner_projects'){
    				$cf_1_sql = "SELECT DISTINCT
								    up1.user_id
								FROM
								    user_permissions up1
								INNER JOIN user_permissions up2 ON
								    up1.project_id = up2.project_id
								WHERE
								    up1.workspace_id IS NULL AND
								    up2.user_id = ".$this->user_id." AND
								    up2.role IN ('Creator', 'Owner', 'Group Owner')";
    			}
    			else if(!empty($people_from) && $people_from == 'shared_projects'){
    				$cf_1_sql = "SELECT DISTINCT
								    up1.user_id
								FROM
								    user_permissions up1
								INNER JOIN user_permissions up2 ON
								    up1.project_id = up2.project_id
								WHERE
								    up1.workspace_id IS NULL AND
								    up2.user_id = ".$this->user_id." AND
								    up2.role IN ('Sharer', 'Group Sharer')";
    			}
    			else if(!empty($people_from) && ($people_from == 'organizations' || $people_from == 'locations' || $people_from == 'departments' || $people_from == 'users' || $people_from == 'competencies' || $people_from == 'project')){
    				if(!empty($specific_items_2)){
    					$people_from_ids = implode(',', $specific_items_2);
    					if($people_from == 'organizations') {
	    					$cf_1_sql = "SELECT
										    u.id AS user_id
										FROM
										    users u
										LEFT JOIN user_details ud ON
										    u.id = ud.user_id
										WHERE
										    u.role_id = 2 AND
										    u.status = 1 AND
										    u.is_deleted = 0 AND
										    ud.organization_id IN
										    	(
										            $people_from_ids
										        )";
    					}
    					else if($people_from == 'locations'){
	    					$cf_1_sql = "SELECT
										    u.id AS user_id
										FROM
										    users u
										LEFT JOIN user_details ud ON
										    u.id = ud.user_id
										WHERE
										    u.role_id = 2 AND
										    u.status = 1 AND
										    u.is_deleted = 0 AND
										    ud.location_id IN
										    	(
										            $people_from_ids
										        )";
    					}
    					else if($people_from == 'departments'){
	    					$cf_1_sql = "SELECT
										    u.id AS user_id
										FROM
										    users u
										LEFT JOIN user_details ud ON
										    u.id = ud.user_id
										WHERE
										    u.role_id = 2 AND
										    u.status = 1 AND
										    u.is_deleted = 0 AND
										    ud.department_id IN
										    	(
										            $people_from_ids
										        )";
    					}
    					else if($people_from == 'users'){
	    					$cf_1_sql = "SELECT
										    u.id AS user_id
										FROM
										    users u
										WHERE
										    u.role_id = 2 AND
										    u.status = 1 AND
										    u.is_deleted = 0 AND
										    u.id IN
										    	(
										            $people_from_ids
										        )";
    					}
    					else if($people_from == 'competencies'){
	    					$cf_1_sql = "SELECT DISTINCT
										    u.id AS user_id
										FROM
										    users u
										LEFT JOIN user_domains us ON
										    u.id = us.user_id
										WHERE
										    u.role_id = 2 AND
										    u.status = 1 AND
										    u.is_deleted = 0 AND
										    us.domain_id IN
										    	(
										            $people_from_ids
										        )";
    					}
    					else if($people_from == 'project'){
    						$cf_1_sql = "SELECT DISTINCT
										    up.user_id
										FROM
										    user_permissions up
										WHERE
										    up.workspace_id IS NULL AND
										    up.project_id IN
										        (
										            $people_from_ids
										        )";
    					}
    				}
    			}
    			/*********/

    			$cf_2_sql = $cf_3_sql = '';
    			if(!empty($select_from) && $select_from == 'community'){
    				$cf_2_sql = "SELECT
								    knowledge_domains.id AS domain_id,
								    user_domains.user_id,
								    domain_details.user_level,
								    domain_details.user_experience
								FROM knowledge_domains
								INNER JOIN user_domains ON
								    knowledge_domains.id = user_domains.domain_id
								LEFT JOIN domain_details ON
									user_domains.user_id = domain_details.user_id AND
								    user_domains.domain_id = domain_details.domain_id";
				    $cf_3_sql = "SELECT
								    knowledge_domains.id,
								    knowledge_domains.title
								FROM knowledge_domains";
    			}
    			else if(!empty($select_from) && $select_from == 'all_projects'){
    				$cf_2_sql = "SELECT DISTINCT
								    project_domains.domain_id,
								    user_domains.user_id,
								    domain_details.user_level,
								    domain_details.user_experience
								FROM
								    user_permissions
								INNER JOIN project_domains ON
								    user_permissions.project_id = project_domains.project_id
								INNER JOIN user_domains ON
								    project_domains.domain_id = user_domains.domain_id
								LEFT JOIN domain_details ON
									user_domains.user_id = domain_details.user_id AND
								    user_domains.domain_id = domain_details.domain_id
								WHERE
								    user_permissions.workspace_id IS NULL AND
								    user_permissions.user_id = ".$this->user_id;
    				$cf_3_sql = "SELECT DISTINCT
								    project_domains.domain_id AS id,
								    knowledge_domains.title
								FROM
								    user_permissions
								INNER JOIN project_domains ON
								    user_permissions.project_id = project_domains.project_id
								LEFT JOIN knowledge_domains ON
								    project_domains.domain_id = knowledge_domains.id
								WHERE
								    user_permissions.workspace_id IS NULL AND
								    user_permissions.user_id = ".$this->user_id;
    			}
    			else if(!empty($select_from) && $select_from == 'created_projects'){
    				$cf_2_sql = "SELECT DISTINCT
								    project_domains.domain_id,
								    user_domains.user_id,
								    domain_details.user_level,
								    domain_details.user_experience
								FROM
								    user_permissions
								INNER JOIN project_domains ON
								    user_permissions.project_id = project_domains.project_id
								INNER JOIN user_domains ON
								    project_domains.domain_id = user_domains.domain_id
								LEFT JOIN domain_details ON
									user_domains.user_id = domain_details.user_id AND
								    user_domains.domain_id = domain_details.domain_id
								WHERE
								    user_permissions.workspace_id IS NULL AND
								    user_permissions.user_id = ".$this->user_id." AND
								    user_permissions.role = 'Creator'";
    				$cf_3_sql = "SELECT DISTINCT
								    project_domains.domain_id AS id,
								    knowledge_domains.title
								FROM
								    user_permissions
								INNER JOIN project_domains ON
								    user_permissions.project_id = project_domains.project_id
								LEFT JOIN knowledge_domains ON
								    project_domains.domain_id = knowledge_domains.id
								WHERE
								    user_permissions.workspace_id IS NULL AND
								    user_permissions.user_id = ".$this->user_id." AND
								    user_permissions.role = 'Creator'";
    			}
    			else if(!empty($select_from) && $select_from == 'owner_projects'){
    				$cf_2_sql = "SELECT DISTINCT
								    project_domains.domain_id,
								    user_domains.user_id,
								    domain_details.user_level,
								    domain_details.user_experience
								FROM
								    user_permissions
								INNER JOIN project_domains ON
								    user_permissions.project_id = project_domains.project_id
								INNER JOIN user_domains ON
								    project_domains.domain_id = user_domains.domain_id
								LEFT JOIN domain_details ON
									user_domains.user_id = domain_details.user_id AND
								    user_domains.domain_id = domain_details.domain_id
								WHERE
								    user_permissions.workspace_id IS NULL AND
								    user_permissions.user_id = ".$this->user_id." AND
								    user_permissions.role IN ('Creator', 'Owner', 'Group Owner')";
    				$cf_3_sql = "SELECT DISTINCT
								    project_domains.domain_id AS id,
								    knowledge_domains.title
								FROM
								    user_permissions
								INNER JOIN project_domains ON
								    user_permissions.project_id = project_domains.project_id
								LEFT JOIN knowledge_domains ON
								    project_domains.domain_id = knowledge_domains.id
								WHERE
								    user_permissions.workspace_id IS NULL AND
								    user_permissions.user_id = ".$this->user_id." AND
								    user_permissions.role IN ('Creator', 'Owner', 'Group Owner')";
    			}
    			else if(!empty($select_from) && $select_from == 'shared_projects'){
    				$cf_2_sql = "SELECT DISTINCT
								    project_domains.domain_id,
								    user_domains.user_id,
								    domain_details.user_level,
								    domain_details.user_experience
								FROM
								    user_permissions
								INNER JOIN project_domains ON
								    user_permissions.project_id = project_domains.project_id
								INNER JOIN user_domains ON
								    project_domains.domain_id = user_domains.domain_id
								LEFT JOIN domain_details ON
									user_domains.user_id = domain_details.user_id AND
								    user_domains.domain_id = domain_details.domain_id
								WHERE
								    user_permissions.workspace_id IS NULL AND
								    user_permissions.user_id = ".$this->user_id." AND
								    user_permissions.role IN ('Sharer', 'Group Sharer')";
    				$cf_3_sql = "SELECT DISTINCT
								    project_domains.domain_id AS id,
								    knowledge_domains.title
								FROM
								    user_permissions
								INNER JOIN project_domains ON
								    user_permissions.project_id = project_domains.project_id
								LEFT JOIN knowledge_domains ON
								    project_domains.domain_id = knowledge_domains.id
								WHERE
								    user_permissions.workspace_id IS NULL AND
								    user_permissions.user_id = ".$this->user_id." AND
								    user_permissions.role IN ('Sharer', 'Group Sharer')";
    			}
    			else if(!empty($select_from) && ($select_from == 'organizations' || $select_from == 'locations' || $select_from == 'departments' || $select_from == 'users' || $select_from == 'competencies' || $select_from == 'project')){
    				if(!empty($specific_items_1)){
    					$select_from_ids = implode(',', $specific_items_1);
    					if($select_from == 'organizations'){
	    					$cf_2_sql = "SELECT DISTINCT
										    organization_domains.domain_id,
										    user_domains.user_id,
										    domain_details.user_level,
										    domain_details.user_experience
										FROM
											organization_domains
										INNER JOIN user_domains ON
										    organization_domains.domain_id = user_domains.domain_id
										LEFT JOIN domain_details ON
											user_domains.user_id = domain_details.user_id AND
										    user_domains.domain_id = domain_details.domain_id
										WHERE
											organization_domains.organization_id IN
										        (
										            $select_from_ids
										        )";
    						$cf_3_sql = "SELECT DISTINCT
										    organization_domains.domain_id AS id,
										    knowledge_domains.title
										FROM
											organization_domains
										LEFT JOIN knowledge_domains ON
										    organization_domains.domain_id = knowledge_domains.id
										WHERE
											organization_domains.organization_id IN
										        (
										            $select_from_ids
										        )";
    					}
    					else if($select_from == 'locations'){
	    					$cf_2_sql = "SELECT DISTINCT
										    location_domains.domain_id,
										    user_domains.user_id,
										    domain_details.user_level,
										    domain_details.user_experience
										FROM
											location_domains
										INNER JOIN user_domains ON
										    location_domains.domain_id = user_domains.domain_id
										LEFT JOIN domain_details ON
											user_domains.user_id = domain_details.user_id AND
										    user_domains.domain_id = domain_details.domain_id
										WHERE
											location_domains.location_id IN
										        (
										            $select_from_ids
										        )";
    						$cf_3_sql = "SELECT DISTINCT
										    location_domains.domain_id AS id,
										    knowledge_domains.title
										FROM
											location_domains
										LEFT JOIN knowledge_domains ON
										    location_domains.domain_id = knowledge_domains.id
										WHERE
											location_domains.location_id IN
										        (
										            $select_from_ids
										        )";
    					}
    					else if($select_from == 'departments'){
	    					$cf_2_sql = "SELECT DISTINCT
										    department_domains.domain_id,
										    user_domains.user_id,
										    domain_details.user_level,
										    domain_details.user_experience
										FROM
											department_domains
										INNER JOIN user_domains ON
										    department_domains.domain_id = user_domains.domain_id
										LEFT JOIN domain_details ON
											user_domains.user_id = domain_details.user_id AND
										    user_domains.domain_id = domain_details.domain_id
										WHERE
											department_domains.department_id IN
										        (
										            $select_from_ids
										        )";
    						$cf_3_sql = "SELECT DISTINCT
										    department_domains.domain_id AS id,
										    knowledge_domains.title
										FROM
											department_domains
										LEFT JOIN knowledge_domains ON
										    department_domains.domain_id = knowledge_domains.id
										WHERE
											department_domains.department_id IN
										        (
										            $select_from_ids
										        )";
    					}
    					else if($select_from == 'users'){
	    					$cf_2_sql = "SELECT DISTINCT
										    user_domains.domain_id,
										   	user_domains.user_id,
										    domain_details.user_level,
										    domain_details.user_experience
										FROM user_domains us1
										LEFT JOIN user_domains ON
										    us1.domain_id = user_domains.domain_id
										LEFT JOIN domain_details ON
											user_domains.user_id = domain_details.user_id AND
										    user_domains.domain_id = domain_details.domain_id
										WHERE us1.user_id IN
											(
										        $select_from_ids
										    )";
    						$cf_3_sql = "SELECT DISTINCT
										    us.domain_id AS id,
										    s.title
										FROM user_domains us
										LEFT JOIN knowledge_domains s ON
											us.domain_id = s.id
										WHERE us.user_id IN
											(
										        $select_from_ids
										    )";
    					}
    					else if($select_from == 'competencies'){
	    					$cf_2_sql = "SELECT
										    knowledge_domains.id AS domain_id,
										    user_domains.user_id,
										    domain_details.user_level,
										    domain_details.user_experience
										FROM knowledge_domains
										INNER JOIN user_domains ON
										    knowledge_domains.id = user_domains.domain_id
										LEFT JOIN domain_details ON
											user_domains.user_id = domain_details.user_id AND
										    user_domains.domain_id = domain_details.domain_id
										WHERE knowledge_domains.id IN
											(
										        $select_from_ids
										    )";
    						$cf_3_sql = "SELECT
										    knowledge_domains.id,
										    knowledge_domains.title
										FROM knowledge_domains
										WHERE knowledge_domains.id IN
											(
										        $select_from_ids
										    )";
    					}
    					else if($select_from == 'project'){
	    					$cf_2_sql = "SELECT DISTINCT
										    project_domains.domain_id,
										    user_domains.user_id,
										    domain_details.user_level,
										    domain_details.user_experience
										FROM
										    user_permissions
										INNER JOIN project_domains ON
										    user_permissions.project_id = project_domains.project_id
										INNER JOIN user_domains ON
										    project_domains.domain_id = user_domains.domain_id
										LEFT JOIN domain_details ON
											user_domains.user_id = domain_details.user_id AND
										    user_domains.domain_id = domain_details.domain_id
										WHERE
										    user_permissions.workspace_id IS NULL AND
										    user_permissions.user_id = ".$this->user_id." AND
										    user_permissions.project_id IN
									    	(
									            $select_from_ids
									        )";
    						$cf_3_sql = "SELECT DISTINCT
										    project_domains.domain_id AS id,
										    knowledge_domains.title
										FROM
										    user_permissions
										INNER JOIN project_domains ON
										    user_permissions.project_id = project_domains.project_id
										LEFT JOIN knowledge_domains ON
										    project_domains.domain_id = knowledge_domains.id
										WHERE
										    user_permissions.workspace_id IS NULL AND
										    user_permissions.user_id = ".$this->user_id." AND
										    user_permissions.project_id IN
										        (
										            $select_from_ids
										        )";
    					}
    				}
    			}
    			$query = "SELECT
							JSON_OBJECT(
								'id', 'All Skills',
								'name', 'All Skills',
								'type', 'All Skills',
						        'children',
						        	JSON_ARRAYAGG(
						                JSON_OBJECT(
						                    'id', id,
						                    'name', IF(id = 'No Skills', 'No Domains', title),
						                    'type', IF(id = 'No Skills', 'No Skills', 'Skill'),
						                    'value', 1,
						                    'children', IF(id = 'No Skills', IFNULL(
						               			(
													SELECT
													JSON_ARRAYAGG(
														JSON_OBJECT(
															'id',
															no_skills_users.user_id,
															'name', CONCAT(no_skills_users.first_name,' ',no_skills_users.last_name),
															'image', case when profile_pic IS NULL or profile_pic = ''
																		then CONCAT('".SITEURL."','images/placeholders/user/user_1.png')
																		else CONCAT('".SITEURL.USER_PIC_PATH."', no_skills_users.profile_pic)
																	end,
															'type', 'People',
															'value', 1
														)
													) AS noskillsusers
													FROM
													(
														SELECT
															people_from.user_id,
															user_details.last_name,
															user_details.first_name,
															user_details.profile_pic
														FROM
														(
															$cf_1_sql
														) AS people_from
														LEFT JOIN
														(
															$cf_2_sql
														) AS show_skills_from_people
														ON
															people_from.user_id = show_skills_from_people.user_id
														LEFT JOIN user_details ON
															people_from.user_id = user_details.user_id
														WHERE show_skills_from_people.user_id IS NULL
													) AS no_skills_users
												)
						    					, JSON_ARRAY())
						               		, IFNULL(skill_levels.userlevel, JSON_ARRAY()))
						            	)
						            )
						    ) AS all_skills
						FROM
						(
							$cf_3_sql
							UNION
							SELECT 'No Skills','No Skills'
						) AS skills_noskills
						LEFT JOIN
						(
							#levels
							SELECT
								distinct_skill_detail_levels.domain_id,
								distinct_skill_detail_levels.user_level,
								JSON_ARRAYAGG(
									JSON_OBJECT(
										'name', distinct_skill_detail_levels.user_level,
										'type', 'Level',
										'children', IFNULL(level_experiences.userexperience, JSON_ARRAY())
									)
								) AS userlevel
							FROM
							(
								SELECT DISTINCT
									domain_id,
									user_level
								FROM
								(
									$cf_1_sql
								) AS people_from
								INNER JOIN
								(
									$cf_2_sql
								) AS show_skills_from_people
								ON
									people_from.user_id = show_skills_from_people.user_id
							) AS distinct_skill_detail_levels
							LEFT JOIN
							(
								#experiences
								SELECT
									distinct_skill_detail_experiences.domain_id,
									distinct_skill_detail_experiences.user_level,
									distinct_skill_detail_experiences.user_experience,
									JSON_ARRAYAGG(
										JSON_OBJECT(
											'name', CONCAT(distinct_skill_detail_experiences.user_experience, IF(distinct_skill_detail_experiences.user_experience='1',' Year',' Years')),
											'type', 'Experience',
											'children', IFNULL(experience_users.experienceuser, JSON_ARRAY())
										)
									) AS userexperience
								FROM
								(
									SELECT DISTINCT
										domain_id,
										user_level,
										user_experience
									FROM
									(
										$cf_1_sql
									) AS people_from
									INNER JOIN
									(
										$cf_2_sql
									) AS show_skills_from_people
									ON
										people_from.user_id = show_skills_from_people.user_id
								) AS distinct_skill_detail_experiences
								LEFT JOIN
								(
									#people
									SELECT
										domain_id,
										user_level,
										user_experience,
										people_from.user_id,
										JSON_ARRAYAGG(
											JSON_OBJECT(
												'id',
												user_details.user_id,
												'name', CONCAT(user_details.first_name,' ',user_details.last_name),
												'image', case when user_details.profile_pic IS NULL or user_details.profile_pic = ''
															then CONCAT('".SITEURL."','images/placeholders/user/user_1.png')
															else CONCAT('".SITEURL.USER_PIC_PATH."', user_details.profile_pic)
															end,
												'type', 'People',
												'value', 1
											)
										) AS experienceuser
									FROM
									(
										$cf_1_sql
									) AS people_from
									INNER JOIN
									(
										$cf_2_sql
									) AS show_skills_from_people
									ON
										people_from.user_id = show_skills_from_people.user_id
									LEFT JOIN user_details ON
										people_from.user_id = user_details.user_id
									GROUP BY show_skills_from_people.domain_id, show_skills_from_people.user_level, show_skills_from_people.user_experience
								) AS experience_users
								ON  distinct_skill_detail_experiences.domain_id = experience_users.domain_id AND distinct_skill_detail_experiences.user_level = experience_users.user_level AND distinct_skill_detail_experiences.user_experience = experience_users.user_experience
								GROUP BY distinct_skill_detail_experiences.domain_id, distinct_skill_detail_experiences.user_level

								) AS level_experiences
								ON distinct_skill_detail_levels.domain_id = level_experiences.domain_id AND distinct_skill_detail_levels.user_level = level_experiences.user_level
								GROUP BY distinct_skill_detail_levels.domain_id

						) AS skill_levels
						ON skills_noskills.id = domain_id";

				$data = $this->UserPermission->query($query);
				// pr($data, 1);
				$view->set('data', $data[0][0]['all_skills']);
    		}
        }

		$html = $view->render('skill_json');
		echo json_encode($html);
		exit;
	}

	public function ka_demand_json() {
        $this->layout = false;
        $response = ['success' => false, 'content' => []];
        $post = [];
        $view = new View($this, false);
		$view->viewPath = 'Subdomains';
		$user_id = $this->user_id;
        if ($this->request->is('post') || $this->request->is('put')) {
        	$post = $this->request->data;
        	// pr($post);
        	$competency_type = $post["competency_type"];
            $from_1 = $post["competency_from_1"];
            $specific_1 = $post["specific_items_1"];
            $from_2 = $post["competency_from_2"];
            $specific_2 = $post["specific_items_2"];
            $demand_status = $post["demand_status"];
            $by_level = $post["by_level"];
            $sql_11 = ($by_level == 'level') ? 'sd.user_level AS category,' : 'CONCAT(sd.user_experience, IF(sd.user_experience = "1", " Year", " Years")) AS category,';

            /* INFORMATION */
            // sql1 = sql7
            // sql2 = sql8
            // sql3 = sql12
            // sql4 = sql10
            $sql_1 = $sql_2 = $sql_3 = $sql_4 = $sql_5 = $sql_6 = $sql_7 = $sql_8 = $statues = '';
            if(isset($demand_status) && !empty($demand_status)){
            	$all_status = [];
            	foreach ($demand_status as $key => $value) {
            		//"Not Set", "Not Started", "In Progress", "Overdue", "Completed"
            		if($value == 'not_set') $all_status[] = "'Not Set'";
            		if($value == 'not_started') $all_status[] = "'Not Started'";
            		if($value == 'in_progress') $all_status[] = "'In Progress'";
            		if($value == 'overdue') $all_status[] = "'Overdue'";
            		if($value == 'completed') $all_status[] = "'Completed'";
            	}
            	$statues = implode(',', $all_status);
            }
            if(!empty($competency_type) && $competency_type == 'skills') {
            	$sql_4 = 'skills';
            	$sql_5 = 'project_skills';
            	$sql_6 = 's.id = ps.skill_id';
            	$sql_7 = 'us.skill_id = s.id';
            	$sql_8 = 'user_skills';
            	$sql_9 = 'skill_details';
            	$sql_10 = 'us.skill_id = sd.skill_id';
            	// SELECT 1
            	if(!empty($from_1) && $from_1 == 'community'){
            		$sql_1 = "SELECT
							    p.id,
							    (CASE
							        WHEN p.sign_off = 1 THEN 'Completed'
							        WHEN Date(NOW()) > Date(p.end_date) THEN 'Overdue'
							        WHEN Date(NOW()) >= Date(p.start_date) AND NOW() <= Date(p.end_date) THEN 'In Progress'
							        WHEN Date(NOW()) < Date(p.start_date) THEN 'Not Started' ELSE 'Not Set'
							    END) AS status
							FROM projects p";
            	}
            	else if(!empty($from_1) && $from_1 == 'all_projects'){
            		$sql_1 = "SELECT DISTINCT
								    up.project_id AS id,
								(CASE
							        WHEN p.sign_off = 1 THEN 'Completed'
							        WHEN Date(NOW()) > Date(p.end_date) THEN 'Overdue'
							        WHEN Date(NOW()) >= Date(p.start_date) AND NOW() <= Date(p.end_date) THEN 'In Progress'
							        WHEN Date(NOW()) < Date(p.start_date) THEN 'Not Started' ELSE 'Not Set'
							    END) AS status
								FROM
								    user_permissions up
								LEFT JOIN projects p ON
								    up.project_id = p.id
								WHERE
								    up.user_id = $user_id AND
								    up.workspace_id IS NULL";
            	}
            	else if(!empty($from_1) && $from_1 == 'created_projects'){
            		$sql_1 = "SELECT DISTINCT
								    up.project_id AS id,
							    (CASE
							        WHEN p.sign_off = 1 THEN 'Completed'
							        WHEN Date(NOW()) > Date(p.end_date) THEN 'Overdue'
							        WHEN Date(NOW()) >= Date(p.start_date) AND NOW() <= Date(p.end_date) THEN 'In Progress'
							        WHEN Date(NOW()) < Date(p.start_date) THEN 'Not Started' ELSE 'Not Set'
							    END) AS status
								FROM
								    user_permissions up
								LEFT JOIN projects p ON
								    up.project_id = p.id
								WHERE
								    up.user_id = $user_id AND
								    up.workspace_id IS NULL AND
								    up.role = 'Creator'";
            	}
            	else if(!empty($from_1) && $from_1 == 'owner_projects'){
            		$sql_1 = "SELECT DISTINCT
								    up.project_id AS id,
								(CASE
							        WHEN p.sign_off = 1 THEN 'Completed'
							        WHEN Date(NOW()) > Date(p.end_date) THEN 'Overdue'
							        WHEN Date(NOW()) >= Date(p.start_date) AND NOW() <= Date(p.end_date) THEN 'In Progress'
							        WHEN Date(NOW()) < Date(p.start_date) THEN 'Not Started' ELSE 'Not Set'
							    END) AS status
								FROM
								    user_permissions up
								LEFT JOIN projects p ON
								    up.project_id = p.id
								WHERE
								    up.user_id = $user_id AND
								    up.workspace_id IS NULL AND
								    up.role IN ('Creator', 'Owner', 'Group Owner')";
            	}
            	else if(!empty($from_1) && $from_1 == 'shared_projects'){
            		$sql_1 = "SELECT DISTINCT
								    up.project_id AS id,
								(CASE
							        WHEN p.sign_off = 1 THEN 'Completed'
							        WHEN Date(NOW()) > Date(p.end_date) THEN 'Overdue'
							        WHEN Date(NOW()) >= Date(p.start_date) AND NOW() <= Date(p.end_date) THEN 'In Progress'
							        WHEN Date(NOW()) < Date(p.start_date) THEN 'Not Started' ELSE 'Not Set'
							    END) AS status
								FROM
								    user_permissions up
								LEFT JOIN projects p ON
								    up.project_id = p.id
								WHERE
								    up.user_id = ".$this->user_id." AND
								    up.workspace_id IS NULL AND
								    up.role IN ('Sharer', 'Group Sharer')";
            	}
            	else if(!empty($from_1) && $from_1 == 'project'){
            		$project_ids = implode(',', $specific_1);
            		$sql_1 = "SELECT DISTINCT
								    up.project_id AS id,
								(CASE
							        WHEN p.sign_off = 1 THEN 'Completed'
							        WHEN Date(NOW()) > Date(p.end_date) THEN 'Overdue'
							        WHEN Date(NOW()) >= Date(p.start_date) AND NOW() <= Date(p.end_date) THEN 'In Progress'
							        WHEN Date(NOW()) < Date(p.start_date) THEN 'Not Started' ELSE 'Not Set'
							    END) AS status
								FROM
								    user_permissions up
								LEFT JOIN projects p ON
								    up.project_id = p.id
								WHERE
								    up.user_id = $user_id AND
								    up.workspace_id IS NULL AND
								    up.project_id IN (
								        $project_ids
							        )";
            	}
            	// SELECT 2
            	if(!empty($from_2) && $from_2 == 'community'){
            		$sql_2 = "SELECT DISTINCT
							    u.id
							FROM
							    users u
							WHERE
							    u.role_id = 2 AND
							    u.status = 1 AND
							    u.is_deleted = 0";
            	}
            	else if(!empty($from_2) && $from_2 == 'organizations'){
            		$specific_id = implode(',', $specific_2);
            		$sql_2 = "SELECT DISTINCT
							    u.id
							FROM
							    users u
							LEFT JOIN user_details ud ON
							    u.id = ud.user_id
							WHERE
							    u.role_id = 2 AND
							    u.status = 1 AND
							    u.is_deleted = 0 AND
							    ud.organization_id IN ( $specific_id )";
            	}
            	else if(!empty($from_2) && $from_2 == 'locations'){
            		$specific_id = implode(',', $specific_2);
            		$sql_2 = "SELECT DISTINCT
							    u.id
							FROM
							    users u
							LEFT JOIN user_details ud ON
							    u.id = ud.user_id
							WHERE
							    u.role_id = 2 AND
							    u.status = 1 AND
							    u.is_deleted = 0 AND
							    ud.location_id IN ( $specific_id )";
            	}
            	else if(!empty($from_2) && $from_2 == 'departments'){
            		$specific_id = implode(',', $specific_2);
            		$sql_2 = "SELECT DISTINCT
							    u.id
							FROM
							    users u
							LEFT JOIN user_details ud ON
							    u.id = ud.user_id
							WHERE
							    u.role_id = 2 AND
							    u.status = 1 AND
							    u.is_deleted = 0 AND
							    ud.department_id IN ( $specific_id )";
            	}
            	else if(!empty($from_2) && $from_2 == 'users'){
            		$specific_id = implode(',', $specific_2);
            		$sql_2 = "SELECT DISTINCT
							    u.id
							FROM
							    users u
							LEFT JOIN user_details ud ON
							    u.id = ud.user_id
							WHERE
							    u.role_id = 2 AND
							    u.status = 1 AND
							    u.is_deleted = 0 AND
							    u.id IN ( $specific_id )";
            	}
            	else if(!empty($from_2) && $from_2 == 'competencies'){
            		$specific_id = implode(',', $specific_2);
            		$sql_2 = "SELECT DISTINCT
							    u.id
							FROM
							    users u
							LEFT JOIN user_skills us ON
							    u.id = us.user_id
							WHERE
							    u.role_id = 2 AND
							    u.status = 1 AND
							    u.is_deleted = 0 AND
							    us.skill_id IN ( $specific_id )";
            	}
            	else if(!empty($from_2) && $from_2 == 'all_projects'){
            		$sql_2 = "SELECT DISTINCT
							    up1.user_id AS id
							FROM
							    user_permissions up1
							INNER JOIN user_permissions up2 ON
							    up1.project_id = up2.project_id
							LEFT JOIN users u ON
								up2.user_id = u.id
							WHERE
							    up1.workspace_id IS NULL AND
							    up2.user_id = $user_id AND
							    u.role_id = 2 AND
							    u.status = 1 AND
							    u.is_deleted = 0";
            	}
            	else if(!empty($from_2) && $from_2 == 'created_projects'){
            		$sql_2 = "SELECT DISTINCT
							    up1.user_id AS id
							FROM
							    user_permissions up1
							INNER JOIN user_permissions up2 ON
							    up1.project_id = up2.project_id
							LEFT JOIN users u ON
								up2.user_id = u.id
							WHERE
							    up1.workspace_id IS NULL AND
							    up2.user_id = $user_id AND
							    up2.role = 'Creator' AND
							    u.role_id = 2 AND
							    u.status = 1 AND
							    u.is_deleted = 0";
            	}
            	else if(!empty($from_2) && $from_2 == 'owner_projects'){
            		$sql_2 = "SELECT DISTINCT
							    up1.user_id AS id
							FROM
							    user_permissions up1
							INNER JOIN user_permissions up2 ON
							    up1.project_id = up2.project_id
							LEFT JOIN users u ON
								up2.user_id = u.id
							WHERE
							    up1.workspace_id IS NULL AND
							    up2.user_id = $user_id AND
							    up2.role IN ('Creator', 'Owner', 'Group Owner') AND
							    u.role_id = 2 AND
							    u.status = 1 AND
							    u.is_deleted = 0";
            	}
            	else if(!empty($from_2) && $from_2 == 'shared_projects'){
            		$sql_2 = "SELECT DISTINCT
							    up1.user_id AS id
							FROM
							    user_permissions up1
							INNER JOIN user_permissions up2 ON
							    up1.project_id = up2.project_id
							LEFT JOIN users u ON
								up2.user_id = u.id
							WHERE
							    up1.workspace_id IS NULL AND
							    up2.user_id = $user_id AND
							    up2.role IN ('Sharer', 'Group Sharer') AND
							    u.role_id = 2 AND
							    u.status = 1 AND
							    u.is_deleted = 0";
            	}
            	else if(!empty($from_2) && $from_2 == 'project') {
            		$specific_id = implode(',', $specific_2);
            		$sql_2 = "SELECT DISTINCT
							    up.user_id AS id
							FROM
							    user_permissions up
							LEFT JOIN users u ON
							    up.user_id = u.id
							WHERE
							    up.workspace_id IS NULL AND
							    up.project_id IN ($specific_id) AND
							    u.role_id = 2 AND
							    u.status = 1 AND
							    u.is_deleted = 0";
            	}
            }
            else if(!empty($competency_type) && $competency_type == 'subjects') {
            	$sql_4 = 'subjects';
            	$sql_5 = 'project_subjects';
            	$sql_6 = 's.id = ps.subject_id';
            	$sql_7 = 'us.subject_id = s.id';
            	$sql_8 = 'user_subjects';
            	$sql_9 = 'subject_details';
            	$sql_10 = 'us.subject_id = sd.subject_id';
            	// SELECT 1
            	if(!empty($from_1) && $from_1 == 'community'){
            		$sql_1 = "SELECT
							    p.id,
							    (CASE
							        WHEN p.sign_off = 1 THEN 'Completed'
							        WHEN Date(NOW()) > Date(p.end_date) THEN 'Overdue'
							        WHEN Date(NOW()) >= Date(p.start_date) AND NOW() <= Date(p.end_date) THEN 'In Progress'
							        WHEN Date(NOW()) < Date(p.start_date) THEN 'Not Started' ELSE 'Not Set'
							    END) AS status
							FROM projects p";
            	}
            	else if(!empty($from_1) && $from_1 == 'all_projects'){
            		$sql_1 = "SELECT DISTINCT
								    up.project_id AS id,
								(CASE
							        WHEN p.sign_off = 1 THEN 'Completed'
							        WHEN Date(NOW()) > Date(p.end_date) THEN 'Overdue'
							        WHEN Date(NOW()) >= Date(p.start_date) AND NOW() <= Date(p.end_date) THEN 'In Progress'
							        WHEN Date(NOW()) < Date(p.start_date) THEN 'Not Started' ELSE 'Not Set'
							    END) AS status
								FROM
								    user_permissions up
								LEFT JOIN projects p ON
								    up.project_id = p.id
								WHERE
								    up.user_id = $user_id AND
								    up.workspace_id IS NULL";
            	}
            	else if(!empty($from_1) && $from_1 == 'created_projects'){
            		$sql_1 = "SELECT DISTINCT
								    up.project_id AS id,
							    (CASE
							        WHEN p.sign_off = 1 THEN 'Completed'
							        WHEN Date(NOW()) > Date(p.end_date) THEN 'Overdue'
							        WHEN Date(NOW()) >= Date(p.start_date) AND NOW() <= Date(p.end_date) THEN 'In Progress'
							        WHEN Date(NOW()) < Date(p.start_date) THEN 'Not Started' ELSE 'Not Set'
							    END) AS status
								FROM
								    user_permissions up
								LEFT JOIN projects p ON
								    up.project_id = p.id
								WHERE
								    up.user_id = $user_id AND
								    up.workspace_id IS NULL AND
								    up.role = 'Creator'";
            	}
            	else if(!empty($from_1) && $from_1 == 'owner_projects'){
            		$sql_1 = "SELECT DISTINCT
								    up.project_id AS id,
								(CASE
							        WHEN p.sign_off = 1 THEN 'Completed'
							        WHEN Date(NOW()) > Date(p.end_date) THEN 'Overdue'
							        WHEN Date(NOW()) >= Date(p.start_date) AND NOW() <= Date(p.end_date) THEN 'In Progress'
							        WHEN Date(NOW()) < Date(p.start_date) THEN 'Not Started' ELSE 'Not Set'
							    END) AS status
								FROM
								    user_permissions up
								LEFT JOIN projects p ON
								    up.project_id = p.id
								WHERE
								    up.user_id = $user_id AND
								    up.workspace_id IS NULL AND
								    up.role IN ('Creator', 'Owner', 'Group Owner')";
            	}
            	else if(!empty($from_1) && $from_1 == 'shared_projects'){
            		$sql_1 = "SELECT DISTINCT
								    up.project_id AS id,
								(CASE
							        WHEN p.sign_off = 1 THEN 'Completed'
							        WHEN Date(NOW()) > Date(p.end_date) THEN 'Overdue'
							        WHEN Date(NOW()) >= Date(p.start_date) AND NOW() <= Date(p.end_date) THEN 'In Progress'
							        WHEN Date(NOW()) < Date(p.start_date) THEN 'Not Started' ELSE 'Not Set'
							    END) AS status
								FROM
								    user_permissions up
								LEFT JOIN projects p ON
								    up.project_id = p.id
								WHERE
								    up.user_id = ".$this->user_id." AND
								    up.workspace_id IS NULL AND
								    up.role IN ('Sharer', 'Group Sharer')";
            	}
            	else if(!empty($from_1) && $from_1 == 'project'){
            		$project_ids = implode(',', $specific_1);
            		$sql_1 = "SELECT DISTINCT
								    up.project_id AS id,
								(CASE
							        WHEN p.sign_off = 1 THEN 'Completed'
							        WHEN Date(NOW()) > Date(p.end_date) THEN 'Overdue'
							        WHEN Date(NOW()) >= Date(p.start_date) AND NOW() <= Date(p.end_date) THEN 'In Progress'
							        WHEN Date(NOW()) < Date(p.start_date) THEN 'Not Started' ELSE 'Not Set'
							    END) AS status
								FROM
								    user_permissions up
								LEFT JOIN projects p ON
								    up.project_id = p.id
								WHERE
								    up.user_id = $user_id AND
								    up.workspace_id IS NULL AND
								    up.project_id IN (
								        $project_ids
							        )";
            	}
            	// SELECT 2
            	if(!empty($from_2) && $from_2 == 'community'){
            		$sql_2 = "SELECT DISTINCT
							    u.id
							FROM
							    users u
							WHERE
							    u.role_id = 2 AND
							    u.status = 1 AND
							    u.is_deleted = 0";
            	}
            	else if(!empty($from_2) && $from_2 == 'organizations'){
            		$specific_id = implode(',', $specific_2);
            		$sql_2 = "SELECT DISTINCT
							    u.id
							FROM
							    users u
							LEFT JOIN user_details ud ON
							    u.id = ud.user_id
							WHERE
							    u.role_id = 2 AND
							    u.status = 1 AND
							    u.is_deleted = 0 AND
							    ud.organization_id IN ( $specific_id )";
            	}
            	else if(!empty($from_2) && $from_2 == 'locations'){
            		$specific_id = implode(',', $specific_2);
            		$sql_2 = "SELECT DISTINCT
							    u.id
							FROM
							    users u
							LEFT JOIN user_details ud ON
							    u.id = ud.user_id
							WHERE
							    u.role_id = 2 AND
							    u.status = 1 AND
							    u.is_deleted = 0 AND
							    ud.location_id IN ( $specific_id )";
            	}
            	else if(!empty($from_2) && $from_2 == 'departments'){
            		$specific_id = implode(',', $specific_2);
            		$sql_2 = "SELECT DISTINCT
							    u.id
							FROM
							    users u
							LEFT JOIN user_details ud ON
							    u.id = ud.user_id
							WHERE
							    u.role_id = 2 AND
							    u.status = 1 AND
							    u.is_deleted = 0 AND
							    ud.department_id IN ( $specific_id )";
            	}
            	else if(!empty($from_2) && $from_2 == 'users'){
            		$specific_id = implode(',', $specific_2);
            		$sql_2 = "SELECT DISTINCT
							    u.id
							FROM
							    users u
							LEFT JOIN user_details ud ON
							    u.id = ud.user_id
							WHERE
							    u.role_id = 2 AND
							    u.status = 1 AND
							    u.is_deleted = 0 AND
							    u.id IN ( $specific_id )";
            	}
            	else if(!empty($from_2) && $from_2 == 'competencies'){
            		$specific_id = implode(',', $specific_2);
            		$sql_2 = "SELECT DISTINCT
							    u.id
							FROM
							    users u
							LEFT JOIN user_skills us ON
							    u.id = us.user_id
							WHERE
							    u.role_id = 2 AND
							    u.status = 1 AND
							    u.is_deleted = 0 AND
							    us.skill_id IN ( $specific_id )";
            	}
            	else if(!empty($from_2) && $from_2 == 'all_projects'){
            		$sql_2 = "SELECT DISTINCT
							    up1.user_id AS id
							FROM
							    user_permissions up1
							INNER JOIN user_permissions up2 ON
							    up1.project_id = up2.project_id
							LEFT JOIN users u ON
								up2.user_id = u.id
							WHERE
							    up1.workspace_id IS NULL AND
							    up2.user_id = $user_id AND
							    u.role_id = 2 AND
							    u.status = 1 AND
							    u.is_deleted = 0";
            	}
            	else if(!empty($from_2) && $from_2 == 'created_projects'){
            		$sql_2 = "SELECT DISTINCT
							    up1.user_id AS id
							FROM
							    user_permissions up1
							INNER JOIN user_permissions up2 ON
							    up1.project_id = up2.project_id
							LEFT JOIN users u ON
								up2.user_id = u.id
							WHERE
							    up1.workspace_id IS NULL AND
							    up2.user_id = $user_id AND
							    up2.role = 'Creator' AND
							    u.role_id = 2 AND
							    u.status = 1 AND
							    u.is_deleted = 0";
            	}
            	else if(!empty($from_2) && $from_2 == 'owner_projects'){
            		$sql_2 = "SELECT DISTINCT
							    up1.user_id AS id
							FROM
							    user_permissions up1
							INNER JOIN user_permissions up2 ON
							    up1.project_id = up2.project_id
							LEFT JOIN users u ON
								up2.user_id = u.id
							WHERE
							    up1.workspace_id IS NULL AND
							    up2.user_id = $user_id AND
							    up2.role IN ('Creator', 'Owner', 'Group Owner') AND
							    u.role_id = 2 AND
							    u.status = 1 AND
							    u.is_deleted = 0";
            	}
            	else if(!empty($from_2) && $from_2 == 'shared_projects'){
            		$sql_2 = "SELECT DISTINCT
							    up1.user_id AS id
							FROM
							    user_permissions up1
							INNER JOIN user_permissions up2 ON
							    up1.project_id = up2.project_id
							LEFT JOIN users u ON
								up2.user_id = u.id
							WHERE
							    up1.workspace_id IS NULL AND
							    up2.user_id = $user_id AND
							    up2.role IN ('Sharer', 'Group Sharer') AND
							    u.role_id = 2 AND
							    u.status = 1 AND
							    u.is_deleted = 0";
            	}
            	else if(!empty($from_2) && $from_2 == 'project') {
            		$specific_id = implode(',', $specific_2);
            		$sql_2 = "SELECT DISTINCT
							    up.user_id AS id
							FROM
							    user_permissions up
							LEFT JOIN users u ON
							    up.user_id = u.id
							WHERE
							    up.workspace_id IS NULL AND
							    up.project_id IN ($specific_id) AND
							    u.role_id = 2 AND
							    u.status = 1 AND
							    u.is_deleted = 0";
            	}
            }
            else if(!empty($competency_type) && $competency_type == 'domains') {
            	$sql_4 = 'knowledge_domains';
            	$sql_5 = 'project_domains';
            	$sql_6 = 's.id = ps.domain_id';
            	$sql_7 = 'us.domain_id = s.id';
            	$sql_8 = 'user_domains';
            	$sql_9 = 'domain_details';
            	$sql_10 = 'us.domain_id = sd.domain_id';
            	// SELECT 1
            	if(!empty($from_1) && $from_1 == 'community'){
            		$sql_1 = "SELECT
							    p.id,
							    (CASE
							        WHEN p.sign_off = 1 THEN 'Completed'
							        WHEN Date(NOW()) > Date(p.end_date) THEN 'Overdue'
							        WHEN Date(NOW()) >= Date(p.start_date) AND NOW() <= Date(p.end_date) THEN 'In Progress'
							        WHEN Date(NOW()) < Date(p.start_date) THEN 'Not Started' ELSE 'Not Set'
							    END) AS status
							FROM projects p";
            	}
            	else if(!empty($from_1) && $from_1 == 'all_projects'){
            		$sql_1 = "SELECT DISTINCT
								    up.project_id AS id,
								(CASE
							        WHEN p.sign_off = 1 THEN 'Completed'
							        WHEN Date(NOW()) > Date(p.end_date) THEN 'Overdue'
							        WHEN Date(NOW()) >= Date(p.start_date) AND NOW() <= Date(p.end_date) THEN 'In Progress'
							        WHEN Date(NOW()) < Date(p.start_date) THEN 'Not Started' ELSE 'Not Set'
							    END) AS status
								FROM
								    user_permissions up
								LEFT JOIN projects p ON
								    up.project_id = p.id
								WHERE
								    up.user_id = $user_id AND
								    up.workspace_id IS NULL";
            	}
            	else if(!empty($from_1) && $from_1 == 'created_projects'){
            		$sql_1 = "SELECT DISTINCT
								    up.project_id AS id,
							    (CASE
							        WHEN p.sign_off = 1 THEN 'Completed'
							        WHEN Date(NOW()) > Date(p.end_date) THEN 'Overdue'
							        WHEN Date(NOW()) >= Date(p.start_date) AND NOW() <= Date(p.end_date) THEN 'In Progress'
							        WHEN Date(NOW()) < Date(p.start_date) THEN 'Not Started' ELSE 'Not Set'
							    END) AS status
								FROM
								    user_permissions up
								LEFT JOIN projects p ON
								    up.project_id = p.id
								WHERE
								    up.user_id = $user_id AND
								    up.workspace_id IS NULL AND
								    up.role = 'Creator'";
            	}
            	else if(!empty($from_1) && $from_1 == 'owner_projects'){
            		$sql_1 = "SELECT DISTINCT
								    up.project_id AS id,
								(CASE
							        WHEN p.sign_off = 1 THEN 'Completed'
							        WHEN Date(NOW()) > Date(p.end_date) THEN 'Overdue'
							        WHEN Date(NOW()) >= Date(p.start_date) AND NOW() <= Date(p.end_date) THEN 'In Progress'
							        WHEN Date(NOW()) < Date(p.start_date) THEN 'Not Started' ELSE 'Not Set'
							    END) AS status
								FROM
								    user_permissions up
								LEFT JOIN projects p ON
								    up.project_id = p.id
								WHERE
								    up.user_id = $user_id AND
								    up.workspace_id IS NULL AND
								    up.role IN ('Creator', 'Owner', 'Group Owner')";
            	}
            	else if(!empty($from_1) && $from_1 == 'shared_projects'){
            		$sql_1 = "SELECT DISTINCT
								    up.project_id AS id,
								(CASE
							        WHEN p.sign_off = 1 THEN 'Completed'
							        WHEN Date(NOW()) > Date(p.end_date) THEN 'Overdue'
							        WHEN Date(NOW()) >= Date(p.start_date) AND NOW() <= Date(p.end_date) THEN 'In Progress'
							        WHEN Date(NOW()) < Date(p.start_date) THEN 'Not Started' ELSE 'Not Set'
							    END) AS status
								FROM
								    user_permissions up
								LEFT JOIN projects p ON
								    up.project_id = p.id
								WHERE
								    up.user_id = ".$this->user_id." AND
								    up.workspace_id IS NULL AND
								    up.role IN ('Sharer', 'Group Sharer')";
            	}
            	else if(!empty($from_1) && $from_1 == 'project'){
            		$project_ids = implode(',', $specific_1);
            		$sql_1 = "SELECT DISTINCT
								    up.project_id AS id,
								(CASE
							        WHEN p.sign_off = 1 THEN 'Completed'
							        WHEN Date(NOW()) > Date(p.end_date) THEN 'Overdue'
							        WHEN Date(NOW()) >= Date(p.start_date) AND NOW() <= Date(p.end_date) THEN 'In Progress'
							        WHEN Date(NOW()) < Date(p.start_date) THEN 'Not Started' ELSE 'Not Set'
							    END) AS status
								FROM
								    user_permissions up
								LEFT JOIN projects p ON
								    up.project_id = p.id
								WHERE
								    up.user_id = $user_id AND
								    up.workspace_id IS NULL AND
								    up.project_id IN (
								        $project_ids
							        )";
            	}
            	// SELECT 2
            	if(!empty($from_2) && $from_2 == 'community'){
            		$sql_2 = "SELECT DISTINCT
							    u.id
							FROM
							    users u
							WHERE
							    u.role_id = 2 AND
							    u.status = 1 AND
							    u.is_deleted = 0";
            	}
            	else if(!empty($from_2) && $from_2 == 'organizations'){
            		$specific_id = implode(',', $specific_2);
            		$sql_2 = "SELECT DISTINCT
							    u.id
							FROM
							    users u
							LEFT JOIN user_details ud ON
							    u.id = ud.user_id
							WHERE
							    u.role_id = 2 AND
							    u.status = 1 AND
							    u.is_deleted = 0 AND
							    ud.organization_id IN ( $specific_id )";
            	}
            	else if(!empty($from_2) && $from_2 == 'locations'){
            		$specific_id = implode(',', $specific_2);
            		$sql_2 = "SELECT DISTINCT
							    u.id
							FROM
							    users u
							LEFT JOIN user_details ud ON
							    u.id = ud.user_id
							WHERE
							    u.role_id = 2 AND
							    u.status = 1 AND
							    u.is_deleted = 0 AND
							    ud.location_id IN ( $specific_id )";
            	}
            	else if(!empty($from_2) && $from_2 == 'departments'){
            		$specific_id = implode(',', $specific_2);
            		$sql_2 = "SELECT DISTINCT
							    u.id
							FROM
							    users u
							LEFT JOIN user_details ud ON
							    u.id = ud.user_id
							WHERE
							    u.role_id = 2 AND
							    u.status = 1 AND
							    u.is_deleted = 0 AND
							    ud.department_id IN ( $specific_id )";
            	}
            	else if(!empty($from_2) && $from_2 == 'users'){
            		$specific_id = implode(',', $specific_2);
            		$sql_2 = "SELECT DISTINCT
							    u.id
							FROM
							    users u
							LEFT JOIN user_details ud ON
							    u.id = ud.user_id
							WHERE
							    u.role_id = 2 AND
							    u.status = 1 AND
							    u.is_deleted = 0 AND
							    u.id IN ( $specific_id )";
            	}
            	else if(!empty($from_2) && $from_2 == 'competencies'){
            		$specific_id = implode(',', $specific_2);
            		$sql_2 = "SELECT DISTINCT
							    u.id
							FROM
							    users u
							LEFT JOIN user_skills us ON
							    u.id = us.user_id
							WHERE
							    u.role_id = 2 AND
							    u.status = 1 AND
							    u.is_deleted = 0 AND
							    us.skill_id IN ( $specific_id )";
            	}
            	else if(!empty($from_2) && $from_2 == 'all_projects'){
            		$sql_2 = "SELECT DISTINCT
							    up1.user_id AS id
							FROM
							    user_permissions up1
							INNER JOIN user_permissions up2 ON
							    up1.project_id = up2.project_id
							LEFT JOIN users u ON
								up2.user_id = u.id
							WHERE
							    up1.workspace_id IS NULL AND
							    up2.user_id = $user_id AND
							    u.role_id = 2 AND
							    u.status = 1 AND
							    u.is_deleted = 0";
            	}
            	else if(!empty($from_2) && $from_2 == 'created_projects'){
            		$sql_2 = "SELECT DISTINCT
							    up1.user_id AS id
							FROM
							    user_permissions up1
							INNER JOIN user_permissions up2 ON
							    up1.project_id = up2.project_id
							LEFT JOIN users u ON
								up2.user_id = u.id
							WHERE
							    up1.workspace_id IS NULL AND
							    up2.user_id = $user_id AND
							    up2.role = 'Creator' AND
							    u.role_id = 2 AND
							    u.status = 1 AND
							    u.is_deleted = 0";
            	}
            	else if(!empty($from_2) && $from_2 == 'owner_projects'){
            		$sql_2 = "SELECT DISTINCT
							    up1.user_id AS id
							FROM
							    user_permissions up1
							INNER JOIN user_permissions up2 ON
							    up1.project_id = up2.project_id
							LEFT JOIN users u ON
								up2.user_id = u.id
							WHERE
							    up1.workspace_id IS NULL AND
							    up2.user_id = $user_id AND
							    up2.role IN ('Creator', 'Owner', 'Group Owner') AND
							    u.role_id = 2 AND
							    u.status = 1 AND
							    u.is_deleted = 0";
            	}
            	else if(!empty($from_2) && $from_2 == 'shared_projects'){
            		$sql_2 = "SELECT DISTINCT
							    up1.user_id AS id
							FROM
							    user_permissions up1
							INNER JOIN user_permissions up2 ON
							    up1.project_id = up2.project_id
							LEFT JOIN users u ON
								up2.user_id = u.id
							WHERE
							    up1.workspace_id IS NULL AND
							    up2.user_id = $user_id AND
							    up2.role IN ('Sharer', 'Group Sharer') AND
							    u.role_id = 2 AND
							    u.status = 1 AND
							    u.is_deleted = 0";
            	}
            	else if(!empty($from_2) && $from_2 == 'project') {
            		$specific_id = implode(',', $specific_2);
            		$sql_2 = "SELECT DISTINCT
							    up.user_id AS id
							FROM
							    user_permissions up
							LEFT JOIN users u ON
							    up.user_id = u.id
							WHERE
							    up.workspace_id IS NULL AND
							    up.project_id IN ($specific_id) AND
							    u.role_id = 2 AND
							    u.status = 1 AND
							    u.is_deleted = 0";
            	}
            }

            $demand_sql = "SELECT
							    JSON_ARRAYAGG(
							        JSON_OBJECT(
							            'id', sd.id,
							            'name', sd.name,
							            'category', sd.category,
							            'value', sd.value
							        )
							    ) AS graph_data
							FROM
							(
							    (
							    SELECT
							        '0' AS id,
									'Total Projects' AS name,
							    	'System' AS category,
							    	COUNT(dp.id) AS value
								FROM
							    (
							    	$sql_1
							    ) AS dp
							    WHERE
							        dp.status IN ($statues)
							    )
							    UNION ALL
							    (
							    SELECT
							        '0' AS id,
									'Total People' AS name,
							    	'System' AS category,
							        COUNT(du.id) AS value
							    FROM
							    (
								    $sql_2
							    ) AS du
							    )
							    UNION ALL
							    (
							    SELECT psd.id, psd.name, psd.category, COUNT(psd.project_id) AS value
							    FROM
							        (
							        SELECT
							            s.id,
							            s.title AS name,
							            IFNULL(pf2.status, 'None') AS category,
							            ps.project_id
							        FROM
							            $sql_4 s
							        LEFT JOIN $sql_5 ps ON
							            $sql_6
							        INNER JOIN (
								        $sql_1
							        ) AS pf2 ON
							        ps.project_id = pf2.id
							        WHERE
							        pf2.status IN ($statues) OR
							        pf2.status IS NULL
							        ) AS psd
								GROUP BY psd.name, psd.category
							    )
							    UNION ALL
							    (
							        #get supply counts
							        SELECT
							            pss.id,
							            pss.name,
							            pss.category,
							            COUNT(pss.user_id) AS value
							        FROM
							        (
							            SELECT
							                s.id,
							                s.title AS name,
							                #--------------------------------------------------------------------------
							                # 9 - CHOOSE ONE OF THESE TWO LINES TO MATCH LEVEL OR EXPERIENCE USER SELECTION
							                $sql_11
							                #--------------------------------------------------------------------------
							                ul.id AS user_id
							            FROM
							                (
							                    #--------------------------------------------------------------------------
							                    # 10 - INSERT RELEVANT SQL HERE FROM: DEMAND - SELECT PEOPLE FROM COUNT.SQL
							                    #--------------------------------------------------------------------------
							                    $sql_2
							                    ) AS ul
							                    #------------------------------------------------------------------------------------------------------
							                    # 11 - CHANGE TABLE TO MATCH SELECT COMPETENCY SELECTED VALUE (user_skills, user_subjects, user_domains)
							                    INNER JOIN $sql_8 us ON
							                    #------------------------------------------------------------------------------------------------------
							                        ul.id = us.user_id
							                    #-----------------------------------------------------------------------------------------------------
							                    # 12 - CHANGE TABLE TO MATCH SELECT COMPETENCY SELECTED VALUE (skill_details, subject_details, domain_details)
							                    LEFT JOIN $sql_9 sd ON
							                    #-----------------------------------------------------------------------------------------------------
							                        us.user_id = sd.user_id AND
							                    #-----------------------------------------------------------------------------------------------------
							                    # 13 - CHANGE COLUMN TO MATCH SELECT COMPETENCY SELECTED VALUE (skill_id, subject_id, domain_id)
							                        $sql_10
							                    #-----------------------------------------------------------------------------------------------------
							                    # 14 - CHANGE TABLE TO MATCH SELECT COMPETENCY SELECTED VALUE (skills, subjects, knowledge_domains)
							                    LEFT JOIN $sql_4 s ON
							                    #-----------------------------------------------------------------------------------------------------
							                    # 15 - CHANGE COLUMN TO MATCH SELECT COMPETENCY SELECTED VALUE (skill_id, subject_id, domain_id)
							                        $sql_7
							                    #-----------------------------------------------------------------------------------------------------
							                    ORDER BY s.title ASC
							        ) AS pss
							        GROUP BY pss.name, pss.category
							    )
							    /*(
									SELECT
								        s.id,
								        s.title AS name,
								        'People' AS category,
								        COUNT(s.id) AS value
								    FROM
								        $sql_8 us
								    LEFT JOIN $sql_4 s ON
								        $sql_7
								    INNER JOIN (
										    	$sql_2
										    ) AS pf3
									    ON us.user_id = pf3.id
								    GROUP BY s.title
								    ORDER BY `value` DESC
							    )*/
							) AS sd";
			$data = $this->UserPermission->query($demand_sql);
			// pr($data);
			$view->set('data', $data[0][0]['graph_data']);

        }

		$html = $view->render('demand_json');
		echo json_encode($html);
		exit;
	}

	public function ka_trends_json() {
        $this->layout = false;
        $response = ['success' => false, 'content' => []];
        $post = [];
        $view = new View($this, false);
		$view->viewPath = 'Subdomains';
		$user_id = $this->user_id;
        if ($this->request->is('post') || $this->request->is('put')) {
        	$post = $this->request->data;
        	// pr($post, 1);
        	$limit = $post['limit'];
        	$order = $post['order'];
        	$type = $post['type'];
        	$viewed_by = $post['viewed_by'];
        	$specific_id = (!empty($post['specific_id'])) ? $post['specific_id'] : [];
        	$start_date = $post['start_date'];
        	$end_date = $post['end_date'];
        	$specific_flag = false;

        	if(( $viewed_by == 'community' || $viewed_by == 'all_projects' || $viewed_by == 'created_projects' || $viewed_by == 'owner_projects' || $viewed_by == 'shared_projects') ) {
        		$specific_flag = true;
        	}
        	else if(($viewed_by != 'organizations' || $viewed_by != 'locations' || $viewed_by != 'departments' || $viewed_by != 'users' || $viewed_by != 'skills' || $viewed_by != 'subjects' || $viewed_by != 'domains' || $viewed_by != 'project') && count($specific_id) > 0) {
                $specific_flag = true;
            }

            $sql1 = $sql2 = $sql3 = $sql4 = $sql5 = $sql6 = $sql9 = $sql10 = $sql15 = '';
            // sql6 = sql14
            // sql3 = sql11
            // sql4 = sql12
            // sql5 = sql13
        	if($limit != '' && $order != '' && $type != '' && $specific_flag != '' && $start_date != '' && $end_date != '' ) {
        		$sql3 = "xa.updated_user_id <> $user_id";

        		$st_date = date('Y-m-d', strtotime($start_date));
        		$en_date = date('Y-m-d', strtotime($end_date));
        		$sql5 = "AND Date(xa.updated) BETWEEN '$st_date' AND '$en_date'";

        		$specific_id = implode(',', $specific_id);
        		if($viewed_by == 'community'){
        			$sql6 = "SELECT DISTINCT
							    u.id AS vbid
							FROM
							    users u
							WHERE
							    u.role_id = 2 AND
							    u.status = 1 AND
							    u.is_deleted = 0";
        		}
        		else if($viewed_by == 'organizations'){
        			$sql6 = "SELECT DISTINCT
							    u.id AS vbid
							FROM
							    users u
							LEFT JOIN user_details ud ON
							    u.id = ud.user_id
							WHERE
							    u.role_id = 2 AND
							    u.status = 1 AND
							    u.is_deleted = 0 AND
							    ud.organization_id IN
							    	(
							            $specific_id
							        )";
        		}
        		else if($viewed_by == 'locations') {
        			$sql6 = "SELECT DISTINCT
							    u.id AS vbid
							FROM
							    users u
							LEFT JOIN user_details ud ON
							    u.id = ud.user_id
							WHERE
							    u.role_id = 2 AND
							    u.status = 1 AND
							    u.is_deleted = 0 AND
							    ud.location_id IN
							    	(
							            $specific_id
							        )";
        		}
        		else if($viewed_by == 'departments') {
        			$sql6 = "SELECT DISTINCT
							    u.id AS vbid
							FROM
							    users u
							LEFT JOIN user_details ud ON
							    u.id = ud.user_id
							WHERE
							    u.role_id = 2 AND
							    u.status = 1 AND
							    u.is_deleted = 0 AND
							    ud.department_id IN
							    	(
							            $specific_id
							        )";
        		}
        		else if($viewed_by == 'users') {
        			$sql6 = "SELECT DISTINCT
							    u.id AS vbid
							FROM
							    users u
							WHERE
							    u.role_id = 2 AND
							    u.status = 1 AND
							    u.is_deleted = 0 AND
							    u.id IN
							    	(
							            $specific_id
							        )";
        		}
        		else if($viewed_by == 'skills') {
        			$sql6 = "SELECT DISTINCT
							    u.id AS vbid
							FROM
							    users u
							LEFT JOIN user_skills us ON
							    u.id = us.user_id
							WHERE
							    u.role_id = 2 AND
							    u.status = 1 AND
							    u.is_deleted = 0 AND
							    us.skill_id IN
							    	(
							            $specific_id
							        )";
        		}
        		else if($viewed_by == 'subjects') {
        			$sql6 = "SELECT DISTINCT
							    u.id AS vbid
							FROM
							    users u
							LEFT JOIN user_subjects us ON
							    u.id = us.user_id
							WHERE
							    u.role_id = 2 AND
							    u.status = 1 AND
							    u.is_deleted = 0 AND
							    us.subject_id IN
							    	(
							            $specific_id
							        )";
        		}
        		else if($viewed_by == 'domains') {
        			$sql6 = "SELECT DISTINCT
							    u.id AS vbid
							FROM
							    users u
							LEFT JOIN user_domains us ON
							    u.id = us.user_id
							WHERE
							    u.role_id = 2 AND
							    u.status = 1 AND
							    u.is_deleted = 0 AND
							    us.domain_id IN
							    	(
							            $specific_id
							        )";
        		}
        		else if($viewed_by == 'all_projects') {
        			$sql6 = "SELECT DISTINCT
							    up1.user_id AS vbid
							FROM
							    user_permissions up1
							INNER JOIN user_permissions up2 ON
							    up1.project_id = up2.project_id AND
							    up2.workspace_id IS NULL
							LEFT JOIN users u ON
								up2.user_id = u.id
							WHERE
							    up1.workspace_id IS NULL AND
							    up2.user_id = $user_id AND
							    u.role_id = 2 AND
							    u.status = 1 AND
							    u.is_deleted = 0";
        		}
        		else if($viewed_by == 'created_projects') {
        			$sql6 = "SELECT DISTINCT
							    up1.user_id AS vbid
							FROM
							    user_permissions up1
							INNER JOIN user_permissions up2 ON
							    up1.project_id = up2.project_id AND
							    up2.workspace_id IS NULL
							LEFT JOIN users u ON
								up2.user_id = u.id
							WHERE
							    up1.workspace_id IS NULL AND
							    up2.user_id = $user_id AND
							    up2.role = 'Creator' AND
							    u.role_id = 2 AND
							    u.status = 1 AND
							    u.is_deleted = 0";
        		}
        		else if($viewed_by == 'owner_projects') {
        			$sql6 = "SELECT DISTINCT
							    up1.user_id AS vbid
							FROM
							    user_permissions up1
							INNER JOIN user_permissions up2 ON
							    up1.project_id = up2.project_id AND
							    up2.workspace_id IS NULL
							LEFT JOIN users u ON
								up2.user_id = u.id
							WHERE
							    up1.workspace_id IS NULL AND
							    up2.user_id = $user_id AND
							    up2.role IN ('Creator', 'Owner', 'Group Owner') AND
							    u.role_id = 2 AND
							    u.status = 1 AND
							    u.is_deleted = 0";
        		}
        		else if($viewed_by == 'shared_projects') {
        			$sql6 = "SELECT DISTINCT
							    up1.user_id AS vbid
							FROM
							    user_permissions up1
							INNER JOIN user_permissions up2 ON
							    up1.project_id = up2.project_id AND
							    up2.workspace_id IS NULL
							LEFT JOIN users u ON
								up2.user_id = u.id
							WHERE
							    up1.workspace_id IS NULL AND
							    up2.user_id = $user_id AND
							    up2.role IN ('Sharer', 'Group Sharer') AND
							    u.role_id = 2 AND
							    u.status = 1 AND
							    u.is_deleted = 0";
        		}
        		else if($viewed_by == 'project') {
        			$sql6 = "SELECT DISTINCT
							    up1.user_id AS vbid
							FROM
							    user_permissions up1
							INNER JOIN user_permissions up2 ON
							    up1.project_id = up2.project_id AND
							    up2.workspace_id IS NULL
							LEFT JOIN users u ON
								up2.user_id = u.id
							WHERE
							    up1.workspace_id IS NULL AND
							    up2.user_id = $user_id AND
							    up1.project_id IN
							        (
							            $specific_id
							        )
						        AND
							    u.role_id = 2 AND
							    u.status = 1 AND
							    u.is_deleted = 0";
        		}

        		if($type == 'organizations'){
        			$sql9 = "xa.organization_id AS id";
        			$sql15 = "xa.organization_id";
        			$sql10 = "organization_activities xa";
        			$sql1 = "SELECT
							    ty.id,
							    'Organization' AS type,
							    ty.name AS name,
							    xa.updated_user_id AS vbid
							FROM
							    organizations ty
							LEFT JOIN organization_activities xa ON
							    ty.id = xa.organization_id ";
        		}
        		else if($type == 'locations'){
        			$sql9 = "xa.location_id AS id";
        			$sql15 = "xa.location_id";
        			$sql10 = "location_activities xa";
        			$sql1 = "SELECT
							    ty.id,
							    'Location' AS type,
							    ty.name AS name,
							    xa.updated_user_id AS vbid
							FROM
							    locations ty
							LEFT JOIN location_activities xa ON
							    ty.id = xa.location_id ";
        		}
        		else if($type == 'departments'){
        			$sql9 = "xa.department_id AS id";
        			$sql15 = "xa.department_id";
        			$sql10 = "department_activities xa";
        			$sql1 = "SELECT
							    ty.id,
							    'Department' AS type,
							    ty.name AS name,
							    xa.updated_user_id AS vbid
							FROM
							    departments ty
							LEFT JOIN department_activities xa ON
							    ty.id = xa.department_id ";
        		}
        		else if($type == 'people'){
        			$sql9 = "xa.user_id AS id";
        			$sql15 = "xa.user_id";
        			$sql10 = "user_activities xa";
        			$sql1 = "SELECT
							    ty.id,
							    'Person' AS type,
							    CONCAT(ud.first_name, ' ', ud.last_name) AS name,
							    xa.updated_user_id AS vbid
							FROM users ty
							LEFT JOIN user_activities xa ON
								ty.id = xa.user_id
							LEFT JOIN user_details ud ON
							    ty.id = ud.user_id ";
				    $sql2 = "ty.role_id = 2 AND
				            ty.status = 1 AND
				            ty.is_deleted = 0 AND";
				    $sql4 = "AND xa.updated_user_id <> xa.user_id ";
        		}
        		else if($type == 'skill'){
        			$sql9 = "xa.skill_id AS id";
        			$sql15 = "xa.skill_id";
        			$sql10 = "skill_activities xa";
        			$sql1 = "SELECT
							    ty.id,
							    'Skill' AS type,
							    ty.title AS name,
							    xa.updated_user_id AS vbid
							FROM
							    skills ty
							LEFT JOIN skill_activities xa ON
							    ty.id = xa.skill_id ";
        		}
        		else if($type == 'subject'){
        			$sql9 = "xa.subject_id AS id";
        			$sql15 = "xa.subject_id";
        			$sql10 = "subject_activities xa";
        			$sql1 = "SELECT
							    ty.id,
							    'Subject' AS type,
							    ty.title AS name,
							    xa.updated_user_id AS vbid
							FROM
							    subjects ty
							LEFT JOIN subject_activities xa ON
							    ty.id = xa.subject_id ";
        		}
        		else if($type == 'domain'){
        			$sql9 = "xa.domain_id AS id";
        			$sql15 = "xa.domain_id";
        			$sql10 = "domain_activities xa";
        			$sql1 = "SELECT
							    ty.id,
							    'Domain' AS type,
							    ty.title AS name,
							    xa.updated_user_id AS vbid
							FROM
							    knowledge_domains ty
							LEFT JOIN domain_activities xa ON
							    ty.id = xa.domain_id ";
        		}

        		$trends_sql = "SELECT
							    JSON_ARRAYAGG(
							        JSON_OBJECT(
							            'id', trtypes.id,
							            'type', trtypes.type,
							            'name', trtypes.name,
							            'data', IFNULL(tractivities.tractivity, JSON_ARRAY())
							        )
							    ) AS trends_data
								FROM
								(
								    #get list of items
								    SELECT
								    	tr_idtypename.id,
								    	tr_idtypename.type,
								    	tr_idtypename.name
								    FROM
								    (
								    	#-------------------------------------------
								    	# 1 - UPDATE WITH USER 'SELECT TYPE' SELECTIONS
								    	$sql1
								        #-------------------------------------------
								        WHERE
								            #-------------------------------------------
								            # 2 - ONLY INCLUDE THESE 3 LINES IF 'SELECT TYPE' = People
								        	$sql2
								        	#-------------------------------------------
								            #exclude views made by current user
								            #-------------------------------------------
								            # 3 - UPDATE WITH CURRENT USER ID
								            $sql3
								            #-------------------------------------------
								            #exclude self views
								            #-------------------------------------------
								            # 4 - ONLY INCLUDE THIS LINE IF 'SELECT TYPE' = People
								            $sql4
								            #-------------------------------------------
								            #only within selected range
								            #-------------------------------------------
								            # 5 - UPDATE WITH USER DATE RANGE SELECTIONS
								            $sql5
								            #-------------------------------------------
								            AND xa.updated_user_id IN
								            (
								                #-------------------------------------------
								                # 6 - UPDATE WITH 'VIEWED BY' SELECTION
								            	$sql6
								                #-------------------------------------------
								            )
								    ) AS tr_idtypename
								    GROUP BY tr_idtypename.id
								    #-------------------------------------------
								    # 7 - UPDATE WITH USER 'MOST/LEAST' SELECTION (MOST=DESC|LEAST=ASC)
								    ORDER BY COUNT(tr_idtypename.vbid) $order
								    #-------------------------------------------
								    # 8 - UPDATE WITH USER 'LIMIT' SELECTIONS 10|20|50|100|250
								    LIMIT $limit
								    #-------------------------------------------
								) AS trtypes
								LEFT JOIN
								(
								    #get activities for list of items
								    SELECT
								    	#-------------------------------------------
								        # 9 - UPDATE COLUMN NAME BASED ON USER 'SELECT TYPE' (xa.organization_id|xa.location_id|xa.department_id|xa.user_id|xa.skill_id|xa.subject_id|xa.domain_id)
								        $sql9,
								        #-------------------------------------------
								    	JSON_ARRAYAGG(
								        JSON_OBJECT(
								            'id', xa.updated_user_id,
								            'name', CONCAT(ud.first_name, ' ', ud.last_name),
								            'date', IFNULL(xa.updated,JSON_ARRAY())
								            )
								    	) AS tractivity
								    FROM
								    	#-------------------------------------------
								        # 10 - UPDATE TABLE NAME BASED ON USER 'TYPE' SELECTION (organization_activities|location_activities|department_activities|user_activities|skill_activities|subject_activities|domain_activities)
								        $sql10
								        #-------------------------------------------
								    LEFT JOIN user_details ud ON
								        xa.updated_user_id = ud.user_id
								    WHERE
								            #exclude views made by current user
								            #-------------------------------------------
								            # 11 - UPDATE WITH CURRENT USER ID
								            $sql3
								            #-------------------------------------------
								            #exclude self views
								            #-------------------------------------------
								            # 12 - ONLY INCLUDE THIS LINE IF 'SELECT TYPE' = People
								            $sql4
								            #-------------------------------------------
								            #only within selected range
								            #-------------------------------------------
								            # 13 - UPDATE WITH USER DATE RANGE SELECTIONS
								            $sql5
								            #-------------------------------------------
								            AND xa.updated_user_id IN
								            (
								                #-------------------------------------------
								                # 14 - UPDATE WITH 'VIEWED BY' SELECTION
								            	$sql6
								                #-------------------------------------------
								            )
								    #-------------------------------------------
								    # 15 - UPDATE COLUMN NAME BASED ON USER 'SELECT TYPE' (xa.organization_id|xa.location_id|xa.department_id|xa.user_id|xa.skill_id|xa.subject_id|xa.domain_id)
								    GROUP BY $sql15
								    #-------------------------------------------
								    # 16 - UPDATE WITH USER 'MOST/LEAST' SELECTION (MOST=DESC|LEAST=ASC)
								    ORDER BY COUNT(xa.id) $order
								    #-------------------------------------------
								) AS tractivities
								ON trtypes.id = tractivities.id";
				// pr($trends_sql, 1);
        		$data = $this->UserPermission->query($trends_sql);
				$data = (isset($data[0][0]['trends_data']) && !empty($data[0][0]['trends_data'])) ? $data[0][0]['trends_data'] : '';

        		$view->set('data', $data);
        	}
        }

		$html = $view->render('trends_json');
		echo json_encode($html);
		exit;
	}

	public function get_specific_data(){
		if ($this->request->isAjax()) {
            $this->layout = 'ajax';
            $response = ['success' => false, 'content' => [], 'selection' => []];
            $details = [];
            $user_id = $this->Session->read('Auth.User.id');

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
						$data = $this->User->query("SELECT u.id, CONCAT_WS(' ', ud.first_name, ud.last_name) as full_name FROM users u INNER JOIN user_details ud ON ud.user_id = u.id WHERE u.role_id = 2 AND u.status = 1 AND u.is_activated = 1 ORDER BY full_name ASC");
						if(isset($data) && !empty($data)){
							foreach ($data as $key => $value) {
								$details[] = ['value' => $value['u']['id'], 'label' => $value[0]['full_name']];
							}
						}
						$response['success'] = true;
					}
					else if($type == 'project'){
						// pr($post, 1);
						$conditions = [
							'UserPermission.user_id' => $user_id,
							'UserPermission.workspace_id IS NULL',
							'role' => ['Creator', 'Group Owner','Owner']
						];
						if(isset($post['project_id']) && !empty($post['project_id'])){
							$conditions['UserPermission.project_id'] = $post['project_id'];
						}

						$data = $this->UserPermission->find('list', array('joins' => [
									[
										'alias' => 'Project',
										'table' => 'projects',
										'type' => 'INNER',
										'conditions' => 'Project.id = UserPermission.project_id',
									]
								],
								'conditions' => $conditions,
								'fields'=>array('Project.id','Project.title'),
								'order' => ['Project.title ASC']
							)
						);

						// pr($conditions);
						if(isset($data) && !empty($data)){
							foreach ($data as $key => $value) {
								$details[] = ['value' => $key, 'label' => htmlentities($value, ENT_QUOTES, "UTF-8")];
							}
						}
						$response['success'] = true;
					}
					else if($type == 'competencies' && (isset($post['competency_type']) && !empty($post['competency_type'])) ){

						$competency_type = $post['competency_type'];
						if($competency_type == 'skills'){
							$this->loadModel('Skill');
							$data = $this->Skill->find('list', ['fields' => ['id', 'title'], 'order' => ['title ASC']]);
						}
						else if($competency_type == 'subjects'){
							$this->loadModel('Subject');
							$data = $this->Subject->find('list', ['fields' => ['id', 'title'], 'order' => ['title ASC']]);
						}
						else if($competency_type == 'domains'){
							$this->loadModel('KnowledgeDomain');
							$data = $this->KnowledgeDomain->find('list', ['fields' => ['id', 'title'], 'order' => ['title ASC']]);
						}
						// pr($data, 1);
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

	public function get_trends_data(){
		if ($this->request->isAjax()) {
            $this->layout = 'ajax';
            $response = ['success' => false, 'content' => [], 'selection' => []];
            $details = [];
            $user_id = $this->Session->read('Auth.User.id');

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
						$data = $this->User->query("SELECT u.id, CONCAT_WS(' ', ud.first_name, ud.last_name) as full_name FROM users u INNER JOIN user_details ud ON ud.user_id = u.id WHERE u.role_id = 2 AND u.status = 1 AND u.is_activated = 1 ORDER BY full_name ASC");
						if(isset($data) && !empty($data)){
							foreach ($data as $key => $value) {
								$details[] = ['value' => $value['u']['id'], 'label' => $value[0]['full_name']];
							}
						}
						$response['success'] = true;
					}
					else if($type == 'project'){
						// pr($post, 1);
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

	public function get_demand_data(){
		if ($this->request->isAjax()) {
            $this->layout = 'ajax';
            $response = ['success' => false, 'content' => [], 'selection' => []];
            $details = [];
            $user_id = $this->Session->read('Auth.User.id');

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
						$data = $this->User->query("SELECT u.id, CONCAT_WS(' ', ud.first_name, ud.last_name) as full_name FROM users u INNER JOIN user_details ud ON ud.user_id = u.id WHERE u.role_id = 2 AND u.status = 1 AND u.is_activated = 1 ORDER BY full_name ASC");
						if(isset($data) && !empty($data)){
							foreach ($data as $key => $value) {
								$details[] = ['value' => $value['u']['id'], 'label' => $value[0]['full_name']];
							}
						}
						$response['success'] = true;
					}
					else if($type == 'project'){
						// pr($post, 1);
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
					else if($type == 'competencies' && (isset($post['competency_type']) && !empty($post['competency_type'])) ){

						$competency_type = $post['competency_type'];
						if($competency_type == 'skills'){
							$this->loadModel('Skill');
							$data = $this->Skill->find('list', ['fields' => ['id', 'title'], 'order' => ['title ASC']]);
						}
						else if($competency_type == 'subjects'){
							$this->loadModel('Subject');
							$data = $this->Subject->find('list', ['fields' => ['id', 'title'], 'order' => ['title ASC']]);
						}
						else if($competency_type == 'domains'){
							$this->loadModel('KnowledgeDomain');
							$data = $this->KnowledgeDomain->find('list', ['fields' => ['id', 'title'], 'order' => ['title ASC']]);
						}
						// pr($data, 1);
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

	public function get_json_skills1($project_id, $competency_type) {
		$this->layout = false;
		$this->autoRender = false;

		$view = new View($this, false);
		$view->viewPath = 'Subdomains';
		$view->set("project_id", $project_id);
		$data = [];

		if($competency_type == "skills") {
			if($project_id == "all") {
				$data = $this->UserPermission->query("SELECT
						JSON_OBJECT(
							'id', 'All Skills',
							'name', 'All Skills',
							'type', 'All Skills',
					        'children',
					        	JSON_ARRAYAGG(
					                JSON_OBJECT(
					                    'id', id,
					                    'name', title,
					                    'type', IF(id = 'No Skills', 'No Skills', 'Skill'),
					                    'value', 1,
					                    'children', IF(id = 'No Skills',
					                   			(SELECT
					                                JSON_ARRAYAGG(
					                                    JSON_OBJECT(
					                                        'id',
					                                        no_skills_users.user_id,
					                                        'name', CONCAT(no_skills_users.first_name,' ',no_skills_users.last_name),
					                                        'image', case when profile_pic IS NULL or profile_pic = ''
					                                                then CONCAT('".SITEURL."','images/placeholders/user/user_1.png')
					                                                else CONCAT('".SITEURL.USER_PIC_PATH."', no_skills_users.profile_pic)
					                                                end,
					                                        'type', 'People',
					                                        'value', 1
					                                    )
					                                ) AS noskillsusers
					                            FROM
					                            (
					                                SELECT u.id AS user_id, ud.last_name, ud.first_name, ud.profile_pic FROM users u
					                                LEFT JOIN user_details ud ON u.id = ud.user_id
					                                LEFT JOIN
					                                (SELECT
					                                    sd.user_id,
					                                    sd.skill_id,
					                                    sd.user_level,
					                                    sd.user_experience
					                                FROM
					                                    user_skills us
					                                LEFT JOIN skill_details sd ON
					                                    us.user_id = sd.user_id AND us.skill_id = sd.skill_id
					                                ) AS usd ON
					                                    u.id = usd.user_id
					                                WHERE usd.user_id IS NULL AND u.role_id = 2 AND u.status = 1 AND u.is_deleted = 0
					                            ) AS no_skills_users)

					                   , IFNULL(skill_levels.userlevel, JSON_ARRAY()))
					            	)
					            )
					    ) AS all_skills
					FROM (SELECT id, title FROM skills UNION SELECT 'No Skills','No Skills') AS skills_noskills
					LEFT JOIN
					(
					    #levels
					    SELECT
					     	distinct_skill_detail_levels.skill_id,
					    	distinct_skill_detail_levels.user_level,
					     	JSON_ARRAYAGG(
					        	JSON_OBJECT(
					           		'name', distinct_skill_detail_levels.user_level,
					               	'type', 'Level',
					                'children', IFNULL(level_experiences.userexperience, JSON_ARRAY())
					            )
					        ) AS userlevel
					    FROM (SELECT DISTINCT skill_id, user_level FROM
					         	(
					             SELECT
					                    sd.user_id,
					                    sd.skill_id,
					                    sd.user_level,
					                    sd.user_experience
					                FROM
					                    user_skills us
					                LEFT JOIN skill_details sd ON
					                    us.user_id = sd.user_id AND us.skill_id = sd.skill_id
					         	) AS usd
					         ) AS distinct_skill_detail_levels


					    LEFT JOIN
						(
					    	#experiences
					        SELECT
					        	distinct_skill_detail_experiences.skill_id,
					         	distinct_skill_detail_experiences.user_level,
					        	distinct_skill_detail_experiences.user_experience,
					         	JSON_ARRAYAGG(
					                JSON_OBJECT(
					                    'name', CONCAT(distinct_skill_detail_experiences.user_experience, IF(distinct_skill_detail_experiences.user_experience='1',' Year',' Years')),
					                    'type', 'Experience',
					                    'children', IFNULL(experience_users.experienceuser, JSON_ARRAY())
					                )
					        	) AS userexperience
					        FROM (SELECT DISTINCT skill_id, user_level, user_experience FROM
									(
					              	SELECT
					                    sd.user_id,
					                    sd.skill_id,
					                    sd.user_level,
					                    sd.user_experience
					                FROM
					                    user_skills us
					                LEFT JOIN skill_details sd ON
					                    us.user_id = sd.user_id AND us.skill_id = sd.skill_id
					         		) AS usd
					             ) AS distinct_skill_detail_experiences
					        LEFT JOIN
					        (
					             #users
					             SELECT
					                usd.skill_id,
					                usd.user_level,
					                usd.user_experience,
					            	usd.user_id,
					            	JSON_ARRAYAGG(
					                    JSON_OBJECT(
					                        'id',
					                        user_details.user_id,
					                        'name', CONCAT(user_details.first_name,' ',user_details.last_name),
					                        'image', case when profile_pic IS NULL or profile_pic = ''
					                                then CONCAT('".SITEURL."','images/placeholders/user/user_1.png')
					                                else CONCAT('".SITEURL.USER_PIC_PATH."', user_details.profile_pic)
					                                end,
					                        'type', 'People',
					                        'value', 1
					                    )
					                ) AS experienceuser
					            FROM
					            	(
					              	SELECT
					                    sd.user_id,
					                    sd.skill_id,
					                    sd.user_level,
					                    sd.user_experience
					                FROM
					                    user_skills us
					                LEFT JOIN skill_details sd ON
					                    us.user_id = sd.user_id AND us.skill_id = sd.skill_id
					         		) AS usd
					            LEFT JOIN user_details
					            ON usd.user_id = user_details.user_id
					            GROUP BY usd.skill_id, usd.user_level, usd.user_experience

					        ) AS experience_users
					        ON  distinct_skill_detail_experiences.skill_id = experience_users.skill_id AND distinct_skill_detail_experiences.user_level = experience_users.user_level AND distinct_skill_detail_experiences.user_experience = experience_users.user_experience
					        GROUP BY distinct_skill_detail_experiences.skill_id, distinct_skill_detail_experiences.user_level

					     ) AS level_experiences
					     ON distinct_skill_detail_levels.skill_id = level_experiences.skill_id AND distinct_skill_detail_levels.user_level = level_experiences.user_level
					     GROUP BY distinct_skill_detail_levels.skill_id

					) AS skill_levels
					ON skills_noskills.id = skill_id");

				// pr($data, 1);
				$view->set('data', $data[0][0]['all_skills']);
			}else if($project_id == "my"){
				$data = $this->UserPermission->query("SELECT
							JSON_OBJECT(
								'id', 'All Skills',
								'name', 'All Skills',
								'type', 'All Skills',
						        'children',
						        	JSON_ARRAYAGG(
						                JSON_OBJECT(
						                    'id', id,
						                    'name', title,
						                    'type', IF(id = 'No Skills', 'No Skills', 'Skill'),
						                    'value', 1,
						                    'children', IF(id = 'No Skills', IFNULL(
	                                   			(
	                                                SELECT
	                                                JSON_ARRAYAGG(
	                                                    JSON_OBJECT(
	                                                        'id',
	                                                        no_skills_users.user_id,
	                                                        'name', CONCAT(no_skills_users.first_name,' ',no_skills_users.last_name),
	                                                        'image', case when profile_pic IS NULL or profile_pic = ''
	                                                                    then CONCAT('".SITEURL."','images/placeholders/user/user_1.png')
	                                                                    else CONCAT('".SITEURL.USER_PIC_PATH."', no_skills_users.profile_pic)
	                                                                    end,
	                                                        'type', 'People',
	                                                        'value', 1
	                                                    )
	                                                ) AS noskillsusers
	                                            FROM
	                                            (
	                                                #get all my projects
						                            SELECT up2.user_id, d.last_name, d.first_name, d.profile_pic
						                            FROM
						                                user_permissions up1
						                            #get the users in my projects
						                            INNER JOIN user_permissions up2 ON
						                                up1.workspace_id IS NULL AND
						                                #change user_id at runtime
						                                up1.user_id = ".$this->user_id." AND
						                                up1.role IN('Creator', 'Owner', 'Group Owner') AND up1.project_id = up2.project_id AND
						                                up2.workspace_id IS NULL
						                            #get their skills
						                            LEFT JOIN user_skills  ON
						                                up2.user_id = user_skills.user_id
						                            #compare their skills to skills in my projects
						                            LEFT JOIN project_skills ON
						                                up1.project_id = project_skills.project_id AND
						                                user_skills.skill_id = project_skills.skill_id
						                            #add user status
						                            LEFT JOIN users
						                            ON
						                                up2.user_id = users.id AND
						                                users.role_id = 2 AND
						                                users.status = 1 AND
						                                users.is_deleted = 0
						                            #add user details
						                            LEFT JOIN user_details d ON
						                                users.id = d.user_id
						                            GROUP BY up2.user_id
						                            #only return those users with no skill matches
						                            HAVING COUNT(project_skills.skill_id) = 0
	                                            ) AS no_skills_users)
	                        			, JSON_ARRAY())

	                                   , IFNULL(skill_levels.userlevel, JSON_ARRAY()))
						            	)
						            )
						    ) AS all_skills
							FROM
							(
							    #all my projects (as owner)
							    SELECT DISTINCT project_skills.skill_id AS id, skills.title
							    FROM user_permissions
							    INNER JOIN project_skills
							    ON user_permissions.project_id = project_skills.project_id
							    LEFT JOIN skills
							    ON project_skills.skill_id = skills.id
							    WHERE
							        user_permissions.user_id = ".$this->user_id." AND #change id at runtime
									user_permissions.workspace_id IS NULL AND
							        user_permissions.role IN ('Creator', 'Owner', 'Group Owner')

							    UNION
							    SELECT 'No Skills','No Skills'

							) AS skills_noskills
							LEFT JOIN
							(
							    #levels
							    SELECT
							     	distinct_skill_detail_levels.skill_id,
							    	distinct_skill_detail_levels.user_level,
							     	JSON_ARRAYAGG(
							        	JSON_OBJECT(
							           		'name', distinct_skill_detail_levels.user_level,
							               	'type', 'Level',
							                'children', IFNULL(level_experiences.userexperience, JSON_ARRAY())
							            )
							        ) AS userlevel
							    FROM
							    (
							    	#all levels for users in all my projects
							        SELECT DISTINCT skill_id, user_level FROM skill_details
							        INNER JOIN
							        (
							        SELECT DISTINCT up1.user_id
							            FROM user_permissions up1
							            INNER JOIN user_permissions up2
							            ON up1.project_id = up2.project_id
							            WHERE
										    up1.workspace_id IS NULL AND
							                up2.user_id = ".$this->user_id." AND #change id at runtime
							                up2.role IN ('Creator', 'Owner', 'Group Owner')
							        ) AS my_project_users1
							        ON skill_details.user_id = my_project_users1.user_id

							    ) AS distinct_skill_detail_levels


							    LEFT JOIN
								(
							    	#experiences
							        SELECT
							        	distinct_skill_detail_experiences.skill_id,
							         	distinct_skill_detail_experiences.user_level,
							        	distinct_skill_detail_experiences.user_experience,
							         	JSON_ARRAYAGG(
							                JSON_OBJECT(
							                    'name', CONCAT(distinct_skill_detail_experiences.user_experience, IF(distinct_skill_detail_experiences.user_experience='1',' Year',' Years')),
							                    'type', 'Experience',
							                    'children', IFNULL(experience_users.experienceuser, JSON_ARRAY())
							                )
							        	) AS userexperience
							        FROM
							        (
							            #all levels and experiences for users in all my projects
							       		SELECT DISTINCT skill_id, user_level, user_experience FROM skill_details
							            INNER JOIN
							            (
							                SELECT DISTINCT up1.user_id
							                FROM user_permissions up1
							                INNER JOIN user_permissions up2
							                ON up1.project_id = up2.project_id
							                WHERE
											up1.workspace_id IS NULL AND
							                up2.user_id = ".$this->user_id." AND #change id at runtime
							                up2.role IN ('Creator', 'Owner', 'Group Owner')
							            ) AS my_project_users2
							            ON skill_details.user_id = my_project_users2.user_id

							        ) AS distinct_skill_detail_experiences

							        LEFT JOIN
							        (
							             #users
							             SELECT
							                skill_details.skill_id,
							                skill_details.user_level,
							                skill_details.user_experience,
							            	skill_details.user_id,
							            	JSON_ARRAYAGG(
							                    JSON_OBJECT(
							                        'id',
							                        user_details.user_id,
							                        'name', CONCAT(user_details.first_name,' ',user_details.last_name),
							                        'image', case when profile_pic IS NULL or profile_pic = ''
							                                    then CONCAT('".SITEURL."','images/placeholders/user/user_1.png')
							                                    else CONCAT('".SITEURL.USER_PIC_PATH."', user_details.profile_pic)
							                                    end,
							                        'type', 'People',
							                        'value', 1
							                    )
							                ) AS experienceuser
							            FROM
							                skill_details
							            LEFT JOIN user_details
							            ON skill_details.user_id = user_details.user_id
							            INNER JOIN
							            (
							                #users in all my projects
							                SELECT DISTINCT up1.user_id
							                FROM user_permissions up1
							                INNER JOIN user_permissions up2
							                ON up1.project_id = up2.project_id
							                WHERE
											up1.workspace_id IS NULL AND
							                up2.user_id = ".$this->user_id." AND #change id at runtime
							                up2.role IN ('Creator', 'Owner', 'Group Owner')
							            ) AS my_project_users3
							            ON user_details.user_id = my_project_users3.user_id

							            GROUP BY skill_details.skill_id, skill_details.user_level, skill_details.user_experience

							        ) AS experience_users
							        ON  distinct_skill_detail_experiences.skill_id = experience_users.skill_id AND distinct_skill_detail_experiences.user_level = experience_users.user_level AND distinct_skill_detail_experiences.user_experience = experience_users.user_experience
							        GROUP BY distinct_skill_detail_experiences.skill_id, distinct_skill_detail_experiences.user_level

							     ) AS level_experiences
							     ON distinct_skill_detail_levels.skill_id = level_experiences.skill_id AND distinct_skill_detail_levels.user_level = level_experiences.user_level
							     GROUP BY distinct_skill_detail_levels.skill_id

							) AS skill_levels
							ON skills_noskills.id = skill_id"
						);
				// pr($data, 1);
				$view->set('data', $data[0][0]['all_skills']);
			}else if($project_id > 0){
				$data = $this->UserPermission->query("SELECT
							JSON_OBJECT(
								'id', 'All Skills',
								'name', 'All Skills',
								'type', 'All Skills',
						        'children',
						        	JSON_ARRAYAGG(
						                JSON_OBJECT(
						                    'id', id,
						                    'name', title,
						                    'type', IF(id = 'No Skills', 'No Skills', 'Skill'),
						                    'value', 1,
						                    'children', IF(id = 'No Skills', IFNULL(
	                                   			(
	                                                SELECT
	                                                JSON_ARRAYAGG(
	                                                    JSON_OBJECT(
	                                                        'id',
	                                                        no_skills_users.user_id,
	                                                        'name', CONCAT(no_skills_users.first_name,' ',no_skills_users.last_name),
	                                                        'image', case when profile_pic IS NULL or profile_pic = ''
	                                                                    then CONCAT('".SITEURL."','images/placeholders/user/user_1.png')
	                                                                    else CONCAT('".SITEURL.USER_PIC_PATH."', no_skills_users.profile_pic)
	                                                                    end,
	                                                        'type', 'People',
	                                                        'value', 1
	                                                    )
	                                                ) AS noskillsusers
	                                            FROM
	                                            (
	                                                #get specific project
						                            SELECT up2.user_id, d.last_name, d.first_name, d.profile_pic
						                            FROM
						                                user_permissions up1
						                            #get the users in it
						                            INNER JOIN user_permissions up2 ON
						                                up1.workspace_id IS NULL AND
						                                #change user_id at runtime
						                                up1.user_id = ".$this->user_id." AND
						                                up1.role IN('Creator', 'Owner', 'Group Owner') AND
													#change project_id at runtime
						                                up1.project_id = ".$project_id." AND
						                                up1.project_id = up2.project_id AND
						                                up2.workspace_id IS NULL
						                            #get their skills
						                            LEFT JOIN user_skills  ON
						                                up2.user_id = user_skills.user_id
						                            #compare their skills to project skills
						                            LEFT JOIN project_skills ON
						                                up1.project_id = project_skills.project_id AND
						                                user_skills.skill_id = project_skills.skill_id
						                            #add user status
						                            LEFT JOIN users
						                            ON
						                                up2.user_id = users.id AND
						                                users.role_id = 2 AND
						                                users.status = 1 AND
						                                users.is_deleted = 0
						                            #add user details
						                            LEFT JOIN user_details d ON
						                                users.id = d.user_id
						                            GROUP BY up2.user_id
						                            #only return those users with no skill matches
						                            HAVING COUNT(project_skills.skill_id) = 0
	                                            ) AS no_skills_users)
	                        			, JSON_ARRAY())

	                                   , IFNULL(skill_levels.userlevel, JSON_ARRAY()))
						            	)
						            )
						    ) AS all_skills
							FROM
							(
							    #all my projects (as owner)
							    SELECT DISTINCT project_skills.skill_id AS id, skills.title
							    FROM user_permissions
							    INNER JOIN project_skills
							    ON user_permissions.project_id = project_skills.project_id
							    LEFT JOIN skills
							    ON project_skills.skill_id = skills.id
							    WHERE
							        user_permissions.user_id = ".$this->user_id." AND #change id at runtime
							        user_permissions.project_id = ".$project_id." AND
									user_permissions.workspace_id IS NULL AND
							        user_permissions.role IN ('Creator', 'Owner', 'Group Owner')

							    UNION
							    SELECT 'No Skills','No Skills'

							) AS skills_noskills
							LEFT JOIN
							(
							    #levels
							    SELECT
							     	distinct_skill_detail_levels.skill_id,
							    	distinct_skill_detail_levels.user_level,
							     	JSON_ARRAYAGG(
							        	JSON_OBJECT(
							           		'name', distinct_skill_detail_levels.user_level,
							               	'type', 'Level',
							                'children', IFNULL(level_experiences.userexperience, JSON_ARRAY())
							            )
							        ) AS userlevel
							    FROM
							    (
							    	#all levels for users in all my projects
							        SELECT DISTINCT skill_id, user_level FROM skill_details
							        INNER JOIN
							        (
							        SELECT DISTINCT up1.user_id
							            FROM user_permissions up1
							            INNER JOIN user_permissions up2
							            ON up1.project_id = up2.project_id
							            WHERE
										    up1.workspace_id IS NULL AND
							                up2.user_id = ".$this->user_id." AND #change id at runtime
							                up2.project_id = ".$project_id." AND
							                up2.role IN ('Creator', 'Owner', 'Group Owner')
							        ) AS my_project_users1
							        ON skill_details.user_id = my_project_users1.user_id

							    ) AS distinct_skill_detail_levels


							    LEFT JOIN
								(
							    	#experiences
							        SELECT
							        	distinct_skill_detail_experiences.skill_id,
							         	distinct_skill_detail_experiences.user_level,
							        	distinct_skill_detail_experiences.user_experience,
							         	JSON_ARRAYAGG(
							                JSON_OBJECT(
							                    'name', CONCAT(distinct_skill_detail_experiences.user_experience, IF(distinct_skill_detail_experiences.user_experience='1',' Year',' Years')),
							                    'type', 'Experience',
							                    'children', IFNULL(experience_users.experienceuser, JSON_ARRAY())
							                )
							        	) AS userexperience
							        FROM
							        (
							            #all levels and experiences for users in all my projects
							       		SELECT DISTINCT skill_id, user_level, user_experience FROM skill_details
							            INNER JOIN
							            (
							                SELECT DISTINCT up1.user_id
							                FROM user_permissions up1
							                INNER JOIN user_permissions up2
							                ON up1.project_id = up2.project_id
							                WHERE
											up1.workspace_id IS NULL AND
							                up2.user_id = ".$this->user_id." AND #change id at runtime
							                up2.project_id = ".$project_id." AND
							                up2.role IN ('Creator', 'Owner', 'Group Owner')
							            ) AS my_project_users2
							            ON skill_details.user_id = my_project_users2.user_id

							        ) AS distinct_skill_detail_experiences

							        LEFT JOIN
							        (
							             #users
							             SELECT
							                skill_details.skill_id,
							                skill_details.user_level,
							                skill_details.user_experience,
							            	skill_details.user_id,
							            	JSON_ARRAYAGG(
							                    JSON_OBJECT(
							                        'id',
							                        user_details.user_id,
							                        'name', CONCAT(user_details.first_name,' ',user_details.last_name),
							                        'image', case when profile_pic IS NULL or profile_pic = ''
							                                    then CONCAT('".SITEURL."','images/placeholders/user/user_1.png')
							                                    else CONCAT('".SITEURL.USER_PIC_PATH."', user_details.profile_pic)
							                                    end,
							                        'type', 'People',
							                        'value', 1
							                    )
							                ) AS experienceuser
							            FROM
							                skill_details
							            LEFT JOIN user_details
							            ON skill_details.user_id = user_details.user_id
							            INNER JOIN
							            (
							                #users in all my projects
							                SELECT DISTINCT up1.user_id
							                FROM user_permissions up1
							                INNER JOIN user_permissions up2
							                ON up1.project_id = up2.project_id
							                WHERE
											up1.workspace_id IS NULL AND
							                up2.user_id = ".$this->user_id." AND #change id at runtime
							                up2.project_id = ".$project_id." AND
							                up2.role IN ('Creator', 'Owner', 'Group Owner')
							            ) AS my_project_users3
							            ON user_details.user_id = my_project_users3.user_id

							            GROUP BY skill_details.skill_id, skill_details.user_level, skill_details.user_experience

							        ) AS experience_users
							        ON  distinct_skill_detail_experiences.skill_id = experience_users.skill_id AND distinct_skill_detail_experiences.user_level = experience_users.user_level AND distinct_skill_detail_experiences.user_experience = experience_users.user_experience
							        GROUP BY distinct_skill_detail_experiences.skill_id, distinct_skill_detail_experiences.user_level

							     ) AS level_experiences
							     ON distinct_skill_detail_levels.skill_id = level_experiences.skill_id AND distinct_skill_detail_levels.user_level = level_experiences.user_level
							     GROUP BY distinct_skill_detail_levels.skill_id

							) AS skill_levels
							ON skills_noskills.id = skill_id"
						);

				// pr($data, 1);
				$view->set('data', $data[0][0]['all_skills']);
				// $view->set('data', "");
			}
		}
		else if($competency_type == "subjects"){
			if($project_id == "all"){
				$data = $this->UserPermission->query("SELECT
							JSON_OBJECT(
								'id', 'All Skills',
								'name', 'All Skills',
								'type', 'All Skills',
						        'children',
						        	JSON_ARRAYAGG(
						                JSON_OBJECT(
						                    'id', id,
						                    'name', IF(id = 'No Skills', 'No Subjects', title),
						                    'type', IF(id = 'No Skills', 'No Skills', 'Skill'),
						                    'value', 1,
						                    'children', IF(id = 'No Skills',
						                   			(SELECT
						                                JSON_ARRAYAGG(
						                                    JSON_OBJECT(
						                                        'id',
						                                        no_skills_users.user_id,
						                                        'name', CONCAT(no_skills_users.first_name,' ',no_skills_users.last_name),
						                                        'image', case when profile_pic IS NULL or profile_pic = ''
						                                                then CONCAT('".SITEURL."','images/placeholders/user/user_1.png')
						                                                else CONCAT('".SITEURL.USER_PIC_PATH."', no_skills_users.profile_pic)
						                                                end,
						                                        'type', 'People',
						                                        'value', 1
						                                    )
						                                ) AS noskillsusers
						                            FROM
						                            (
						                                SELECT u.id AS user_id, ud.last_name, ud.first_name, ud.profile_pic FROM users u
						                                LEFT JOIN user_details ud ON u.id = ud.user_id
						                                LEFT JOIN
						                                (SELECT
						                                    sd.user_id,
						                                    sd.subject_id,
						                                    sd.user_level,
						                                    sd.user_experience
						                                FROM
						                                    user_subjects us
						                                LEFT JOIN subject_details sd ON
						                                    us.user_id = sd.user_id AND us.subject_id = sd.subject_id
						                                ) AS usd ON
						                                    u.id = usd.user_id
						                                WHERE usd.user_id IS NULL AND u.role_id = 2 AND u.status = 1 AND u.is_deleted = 0
						                            ) AS no_skills_users)

						                   , IFNULL(skill_levels.userlevel, JSON_ARRAY()))
						            	)
						            )
						    ) AS all_skills
						FROM (SELECT id, title FROM subjects UNION SELECT 'No Skills','No Skills') AS skills_noskills
						LEFT JOIN
						(
						    #levels
						    SELECT
						     	distinct_skill_detail_levels.subject_id,
						    	distinct_skill_detail_levels.user_level,
						     	JSON_ARRAYAGG(
						        	JSON_OBJECT(
						           		'name', distinct_skill_detail_levels.user_level,
						               	'type', 'Level',
						                'children', IFNULL(level_experiences.userexperience, JSON_ARRAY())
						            )
						        ) AS userlevel
						    FROM (SELECT DISTINCT subject_id, user_level FROM
						         	(
						             SELECT
						                    sd.user_id,
						                    sd.subject_id,
						                    sd.user_level,
						                    sd.user_experience
						                FROM
						                    user_subjects us
						                LEFT JOIN subject_details sd ON
						                    us.user_id = sd.user_id AND us.subject_id = sd.subject_id
						         	) AS usd
						         ) AS distinct_skill_detail_levels


						    LEFT JOIN
							(
						    	#experiences
						        SELECT
						        	distinct_skill_detail_experiences.subject_id,
						         	distinct_skill_detail_experiences.user_level,
						        	distinct_skill_detail_experiences.user_experience,
						         	JSON_ARRAYAGG(
						                JSON_OBJECT(
						                    'name', CONCAT(distinct_skill_detail_experiences.user_experience, IF(distinct_skill_detail_experiences.user_experience='1',' Year',' Years')),
						                    'type', 'Experience',
						                    'children', IFNULL(experience_users.experienceuser, JSON_ARRAY())
						                )
						        	) AS userexperience
						        FROM (SELECT DISTINCT subject_id, user_level, user_experience FROM
										(
						              	SELECT
						                    sd.user_id,
						                    sd.subject_id,
						                    sd.user_level,
						                    sd.user_experience
						                FROM
						                    user_subjects us
						                LEFT JOIN subject_details sd ON
						                    us.user_id = sd.user_id AND us.subject_id = sd.subject_id
						         		) AS usd
						             ) AS distinct_skill_detail_experiences
						        LEFT JOIN
						        (
						             #users
						             SELECT
						                usd.subject_id,
						                usd.user_level,
						                usd.user_experience,
						            	usd.user_id,
						            	JSON_ARRAYAGG(
						                    JSON_OBJECT(
						                        'id',
						                        user_details.user_id,
						                        'name', CONCAT(user_details.first_name,' ',user_details.last_name),
						                        'image', case when profile_pic IS NULL or profile_pic = ''
						                                then CONCAT('".SITEURL."','images/placeholders/user/user_1.png')
						                                else CONCAT('".SITEURL.USER_PIC_PATH."', user_details.profile_pic)
						                                end,
						                        'type', 'People',
						                        'value', 1
						                    )
						                ) AS experienceuser
						            FROM
						            	(
						              	SELECT
						                    sd.user_id,
						                    sd.subject_id,
						                    sd.user_level,
						                    sd.user_experience
						                FROM
						                    user_subjects us
						                LEFT JOIN subject_details sd ON
						                    us.user_id = sd.user_id AND us.subject_id = sd.subject_id
						         		) AS usd
						            LEFT JOIN user_details
						            ON usd.user_id = user_details.user_id
						            GROUP BY usd.subject_id, usd.user_level, usd.user_experience

						        ) AS experience_users
						        ON  distinct_skill_detail_experiences.subject_id = experience_users.subject_id AND distinct_skill_detail_experiences.user_level = experience_users.user_level AND distinct_skill_detail_experiences.user_experience = experience_users.user_experience
						        GROUP BY distinct_skill_detail_experiences.subject_id, distinct_skill_detail_experiences.user_level

						     ) AS level_experiences
						     ON distinct_skill_detail_levels.subject_id = level_experiences.subject_id AND distinct_skill_detail_levels.user_level = level_experiences.user_level
						     GROUP BY distinct_skill_detail_levels.subject_id

						) AS skill_levels
						ON skills_noskills.id = subject_id");

				// pr($data, 1);
				$view->set('data', $data[0][0]['all_skills']);
			}else if($project_id == "my"){
				$data = $this->UserPermission->query("SELECT
							JSON_OBJECT(
								'id', 'All Skills',
								'name', 'All Skills',
								'type', 'All Skills',
						        'children',
						        	JSON_ARRAYAGG(
						                JSON_OBJECT(
						                    'id', id,
						                    'name', IF(id = 'No Skills', 'No Subjects', title),
						                    'type', IF(id = 'No Skills', 'No Skills', 'Skill'),
						                    'value', 1,
						                    'children', IF(id = 'No Skills', IFNULL(
						               			(
						                            SELECT
						                            JSON_ARRAYAGG(
						                                JSON_OBJECT(
						                                    'id',
						                                    no_skills_users.user_id,
						                                    'name', CONCAT(no_skills_users.first_name,' ',no_skills_users.last_name),
						                                    'image', case when profile_pic IS NULL or profile_pic = ''
						                                                then CONCAT('".SITEURL."','images/placeholders/user/user_1.png')
						                                                else CONCAT('".SITEURL.USER_PIC_PATH."', no_skills_users.profile_pic)
						                                                end,
						                                    'type', 'People',
						                                    'value', 1
						                                )
						                            ) AS noskillsusers
						                        FROM
						                        (
						                            #get all my projects
						                            SELECT up2.user_id, d.last_name, d.first_name, d.profile_pic
						                            FROM
						                                user_permissions up1
						                            #get the users in my projects
						                            INNER JOIN user_permissions up2 ON
						                                up1.workspace_id IS NULL AND
						                                #change user_id at runtime
						                                up1.user_id = ".$this->user_id." AND
						                                up1.role IN('Creator', 'Owner', 'Group Owner') AND
														up1.project_id = up2.project_id AND
						                                up2.workspace_id IS NULL
						                            #get their skills
						                            LEFT JOIN user_subjects  ON
						                                up2.user_id = user_subjects.user_id
						                            #compare their skills to skills in my projects
						                            LEFT JOIN project_subjects ON
						                                up1.project_id = project_subjects.project_id AND
						                                user_subjects.subject_id = project_subjects.subject_id
						                            #add user status
						                            LEFT JOIN users
						                            ON
						                                up2.user_id = users.id AND
						                                users.role_id = 2 AND
						                                users.status = 1 AND
						                                users.is_deleted = 0
						                            #add user details
						                            LEFT JOIN user_details d ON
						                                users.id = d.user_id
						                            GROUP BY up2.user_id
						                            #only return those users with no skill matches
													HAVING COUNT(project_subjects.subject_id) = 0
						                        ) AS no_skills_users)
						    			, JSON_ARRAY())

						               , IFNULL(skill_levels.userlevel, JSON_ARRAY()))
						            	)
						            )
						    ) AS all_skills
							FROM
							(
							    #all my projects (as owner)
							    SELECT DISTINCT project_subjects.subject_id AS id, subjects.title
							    FROM user_permissions
							    INNER JOIN project_subjects
							    ON user_permissions.project_id = project_subjects.project_id
							    LEFT JOIN subjects
							    ON project_subjects.subject_id = subjects.id
							    WHERE
							        user_permissions.user_id = ".$this->user_id." AND #change id at runtime
									user_permissions.workspace_id IS NULL AND
							        user_permissions.role IN ('Creator', 'Owner', 'Group Owner')

							    UNION
							    SELECT 'No Skills','No Skills'

							) AS skills_noskills
							LEFT JOIN
							(
							    #levels
							    SELECT
							     	distinct_skill_detail_levels.subject_id,
							    	distinct_skill_detail_levels.user_level,
							     	JSON_ARRAYAGG(
							        	JSON_OBJECT(
							           		'name', distinct_skill_detail_levels.user_level,
							               	'type', 'Level',
							                'children', IFNULL(level_experiences.userexperience, JSON_ARRAY())
							            )
							        ) AS userlevel
							    FROM
							    (
							    	#all levels for users in all my projects
							        SELECT DISTINCT subject_id, user_level FROM subject_details
							        INNER JOIN
							        (
							        SELECT DISTINCT up1.user_id
							            FROM user_permissions up1
							            INNER JOIN user_permissions up2
							            ON up1.project_id = up2.project_id
							            WHERE
										    up1.workspace_id IS NULL AND
							                up2.user_id = ".$this->user_id." AND #change id at runtime
							                up2.role IN ('Creator', 'Owner', 'Group Owner')
							        ) AS my_project_users1
							        ON subject_details.user_id = my_project_users1.user_id

							    ) AS distinct_skill_detail_levels


							    LEFT JOIN
								(
							    	#experiences
							        SELECT
							        	distinct_skill_detail_experiences.subject_id,
							         	distinct_skill_detail_experiences.user_level,
							        	distinct_skill_detail_experiences.user_experience,
							         	JSON_ARRAYAGG(
							                JSON_OBJECT(
							                    'name', CONCAT(distinct_skill_detail_experiences.user_experience, IF(distinct_skill_detail_experiences.user_experience='1',' Year',' Years')),
							                    'type', 'Experience',
							                    'children', IFNULL(experience_users.experienceuser, JSON_ARRAY())
							                )
							        	) AS userexperience
							        FROM
							        (
							            #all levels and experiences for users in all my projects
							       		SELECT DISTINCT subject_id, user_level, user_experience FROM subject_details
							            INNER JOIN
							            (
							                SELECT DISTINCT up1.user_id
							                FROM user_permissions up1
							                INNER JOIN user_permissions up2
							                ON up1.project_id = up2.project_id
							                WHERE
											up1.workspace_id IS NULL AND
							                up2.user_id = ".$this->user_id." AND #change id at runtime
							                up2.role IN ('Creator', 'Owner', 'Group Owner')
							            ) AS my_project_users2
							            ON subject_details.user_id = my_project_users2.user_id

							        ) AS distinct_skill_detail_experiences

							        LEFT JOIN
							        (
							             #users
							             SELECT
							                subject_details.subject_id,
							                subject_details.user_level,
							                subject_details.user_experience,
							            	subject_details.user_id,
							            	JSON_ARRAYAGG(
							                    JSON_OBJECT(
							                        'id',
							                        user_details.user_id,
							                        'name', CONCAT(user_details.first_name,' ',user_details.last_name),
							                        'image', case when profile_pic IS NULL or profile_pic = ''
							                                    then CONCAT('".SITEURL."','images/placeholders/user/user_1.png')
							                                    else CONCAT('".SITEURL.USER_PIC_PATH."', user_details.profile_pic)
							                                    end,
							                        'type', 'People',
							                        'value', 1
							                    )
							                ) AS experienceuser
							            FROM
							                subject_details
							            LEFT JOIN user_details
							            ON subject_details.user_id = user_details.user_id
							            INNER JOIN
							            (
							                #users in all my projects
							                SELECT DISTINCT up1.user_id
							                FROM user_permissions up1
							                INNER JOIN user_permissions up2
							                ON up1.project_id = up2.project_id
							                WHERE
											up1.workspace_id IS NULL AND
							                up2.user_id = ".$this->user_id." AND #change id at runtime
							                up2.role IN ('Creator', 'Owner', 'Group Owner')
							            ) AS my_project_users3
							            ON user_details.user_id = my_project_users3.user_id

							            GROUP BY subject_details.subject_id, subject_details.user_level, subject_details.user_experience

							        ) AS experience_users
							        ON  distinct_skill_detail_experiences.subject_id = experience_users.subject_id AND distinct_skill_detail_experiences.user_level = experience_users.user_level AND distinct_skill_detail_experiences.user_experience = experience_users.user_experience
							        GROUP BY distinct_skill_detail_experiences.subject_id, distinct_skill_detail_experiences.user_level

							     ) AS level_experiences
							     ON distinct_skill_detail_levels.subject_id = level_experiences.subject_id AND distinct_skill_detail_levels.user_level = level_experiences.user_level
							     GROUP BY distinct_skill_detail_levels.subject_id

							) AS skill_levels
							ON skills_noskills.id = subject_id"
						);
				// pr($data, 1);
				$view->set('data', $data[0][0]['all_skills']);
			}else if($project_id > 0){
				$data = $this->UserPermission->query("SELECT
							JSON_OBJECT(
								'id', 'All Skills',
								'name', 'All Skills',
								'type', 'All Skills',
						        'children',
						        	JSON_ARRAYAGG(
						                JSON_OBJECT(
						                    'id', id,
						                    'name', IF(id = 'No Skills', 'No Subjects', title),
						                    'type', IF(id = 'No Skills', 'No Skills', 'Skill'),
						                    'value', 1,
						                    'children', IF(id = 'No Skills', IFNULL(
						               			(
						                            SELECT
						                            JSON_ARRAYAGG(
						                                JSON_OBJECT(
						                                    'id',
						                                    no_skills_users.user_id,
						                                    'name', CONCAT(no_skills_users.first_name,' ',no_skills_users.last_name),
						                                    'image', case when profile_pic IS NULL or profile_pic = ''
						                                                then CONCAT('".SITEURL."','images/placeholders/user/user_1.png')
						                                                else CONCAT('".SITEURL.USER_PIC_PATH."', no_skills_users.profile_pic)
						                                                end,
						                                    'type', 'People',
						                                    'value', 1
						                                )
						                            ) AS noskillsusers
						                        FROM
						                        (
						                            #get specific project
						                            SELECT up2.user_id, d.last_name, d.first_name, d.profile_pic
						                            FROM
						                                user_permissions up1
						                            #get the users in it
						                            INNER JOIN user_permissions up2 ON
						                                up1.workspace_id IS NULL AND
						                                #change user_id at runtime
						                                up1.user_id = ".$this->user_id." AND
						                                up1.role IN('Creator', 'Owner', 'Group Owner') AND
														#change project_id at runtime
						                                up1.project_id = ".$project_id." AND
						                                up1.project_id = up2.project_id AND
						                                up2.workspace_id IS NULL
						                            #get their skills
						                            LEFT JOIN user_subjects  ON
						                                up2.user_id = user_subjects.user_id
						                            #compare their skills to project skills
						                            LEFT JOIN project_subjects ON
						                                up1.project_id = project_subjects.project_id AND
						                                user_subjects.subject_id = project_subjects.subject_id
						                            #add user status
						                            LEFT JOIN users
						                            ON
						                                up2.user_id = users.id AND
						                                users.role_id = 2 AND
						                                users.status = 1 AND
						                                users.is_deleted = 0
						                            #add user details
						                            LEFT JOIN user_details d ON
						                                users.id = d.user_id
						                            GROUP BY up2.user_id
						                            #only return those users with no skill matches
													HAVING COUNT(project_subjects.subject_id) = 0
						                        ) AS no_skills_users)
						    			, JSON_ARRAY())

						               , IFNULL(skill_levels.userlevel, JSON_ARRAY()))
						            	)
						            )
						    ) AS all_skills
							FROM
							(
							    #all my projects (as owner)
							    SELECT DISTINCT project_subjects.subject_id AS id, subjects.title
							    FROM user_permissions
							    INNER JOIN project_subjects
							    ON user_permissions.project_id = project_subjects.project_id
							    LEFT JOIN subjects
							    ON project_subjects.subject_id = subjects.id
							    WHERE
							        user_permissions.user_id = ".$this->user_id." AND #change id at runtime
							        user_permissions.project_id = ".$project_id." AND
									user_permissions.workspace_id IS NULL AND
							        user_permissions.role IN ('Creator', 'Owner', 'Group Owner')

							    UNION
							    SELECT 'No Skills','No Skills'

							) AS skills_noskills
							LEFT JOIN
							(
							    #levels
							    SELECT
							     	distinct_skill_detail_levels.subject_id,
							    	distinct_skill_detail_levels.user_level,
							     	JSON_ARRAYAGG(
							        	JSON_OBJECT(
							           		'name', distinct_skill_detail_levels.user_level,
							               	'type', 'Level',
							                'children', IFNULL(level_experiences.userexperience, JSON_ARRAY())
							            )
							        ) AS userlevel
							    FROM
							    (
							    	#all levels for users in all my projects
							        SELECT DISTINCT subject_id, user_level FROM subject_details
							        INNER JOIN
							        (
							        SELECT DISTINCT up1.user_id
							            FROM user_permissions up1
							            INNER JOIN user_permissions up2
							            ON up1.project_id = up2.project_id
							            WHERE
										    up1.workspace_id IS NULL AND
							                up2.user_id = ".$this->user_id." AND #change id at runtime
							                up2.project_id = ".$project_id." AND
							                up2.role IN ('Creator', 'Owner', 'Group Owner')
							        ) AS my_project_users1
							        ON subject_details.user_id = my_project_users1.user_id

							    ) AS distinct_skill_detail_levels


							    LEFT JOIN
								(
							    	#experiences
							        SELECT
							        	distinct_skill_detail_experiences.subject_id,
							         	distinct_skill_detail_experiences.user_level,
							        	distinct_skill_detail_experiences.user_experience,
							         	JSON_ARRAYAGG(
							                JSON_OBJECT(
							                    'name', CONCAT(distinct_skill_detail_experiences.user_experience, IF(distinct_skill_detail_experiences.user_experience='1',' Year',' Years')),
							                    'type', 'Experience',
							                    'children', IFNULL(experience_users.experienceuser, JSON_ARRAY())
							                )
							        	) AS userexperience
							        FROM
							        (
							            #all levels and experiences for users in all my projects
							       		SELECT DISTINCT subject_id, user_level, user_experience FROM subject_details
							            INNER JOIN
							            (
							                SELECT DISTINCT up1.user_id
							                FROM user_permissions up1
							                INNER JOIN user_permissions up2
							                ON up1.project_id = up2.project_id
							                WHERE
											up1.workspace_id IS NULL AND
							                up2.user_id = ".$this->user_id." AND #change id at runtime
							                up2.project_id = ".$project_id." AND
							                up2.role IN ('Creator', 'Owner', 'Group Owner')
							            ) AS my_project_users2
							            ON subject_details.user_id = my_project_users2.user_id

							        ) AS distinct_skill_detail_experiences

							        LEFT JOIN
							        (
							             #users
							             SELECT
							                subject_details.subject_id,
							                subject_details.user_level,
							                subject_details.user_experience,
							            	subject_details.user_id,
							            	JSON_ARRAYAGG(
							                    JSON_OBJECT(
							                        'id',
							                        user_details.user_id,
							                        'name', CONCAT(user_details.first_name,' ',user_details.last_name),
							                        'image', case when profile_pic IS NULL or profile_pic = ''
							                                    then CONCAT('".SITEURL."','images/placeholders/user/user_1.png')
							                                    else CONCAT('".SITEURL.USER_PIC_PATH."', user_details.profile_pic)
							                                    end,
							                        'type', 'People',
							                        'value', 1
							                    )
							                ) AS experienceuser
							            FROM
							                subject_details
							            LEFT JOIN user_details
							            ON subject_details.user_id = user_details.user_id
							            INNER JOIN
							            (
							                #users in all my projects
							                SELECT DISTINCT up1.user_id
							                FROM user_permissions up1
							                INNER JOIN user_permissions up2
							                ON up1.project_id = up2.project_id
							                WHERE
											up1.workspace_id IS NULL AND
							                up2.user_id = ".$this->user_id." AND #change id at runtime
							                up2.project_id = ".$project_id." AND
							                up2.role IN ('Creator', 'Owner', 'Group Owner')
							            ) AS my_project_users3
							            ON user_details.user_id = my_project_users3.user_id

							            GROUP BY subject_details.subject_id, subject_details.user_level, subject_details.user_experience

							        ) AS experience_users
							        ON  distinct_skill_detail_experiences.subject_id = experience_users.subject_id AND distinct_skill_detail_experiences.user_level = experience_users.user_level AND distinct_skill_detail_experiences.user_experience = experience_users.user_experience
							        GROUP BY distinct_skill_detail_experiences.subject_id, distinct_skill_detail_experiences.user_level

							     ) AS level_experiences
							     ON distinct_skill_detail_levels.subject_id = level_experiences.subject_id AND distinct_skill_detail_levels.user_level = level_experiences.user_level
							     GROUP BY distinct_skill_detail_levels.subject_id

							) AS skill_levels
							ON skills_noskills.id = subject_id"
						);

				// pr($data, 1);
				$view->set('data', $data[0][0]['all_skills']);
				// $view->set('data', "");
			}
		}
		else if($competency_type == "domains"){
			if($project_id == "all"){
				$data = $this->UserPermission->query("SELECT
							JSON_OBJECT(
								'id', 'All Skills',
								'name', 'All Skills',
								'type', 'All Skills',
						        'children',
						        	JSON_ARRAYAGG(
						                JSON_OBJECT(
						                    'id', id,
						                    'name', IF(id = 'No Skills', 'No Domains', title),
						                    'type', IF(id = 'No Skills', 'No Skills', 'Skill'),
						                    'value', 1,
						                    'children', IF(id = 'No Skills',
						                   			(SELECT
						                                JSON_ARRAYAGG(
						                                    JSON_OBJECT(
						                                        'id',
						                                        no_skills_users.user_id,
						                                        'name', CONCAT(no_skills_users.first_name,' ',no_skills_users.last_name),
						                                        'image', case when profile_pic IS NULL or profile_pic = ''
						                                                then CONCAT('".SITEURL."','images/placeholders/user/user_1.png')
						                                                else CONCAT('".SITEURL.USER_PIC_PATH."', no_skills_users.profile_pic)
						                                                end,
						                                        'type', 'People',
						                                        'value', 1
						                                    )
						                                ) AS noskillsusers
						                            FROM
						                            (
						                                SELECT u.id AS user_id, ud.last_name, ud.first_name, ud.profile_pic FROM users u
						                                LEFT JOIN user_details ud ON u.id = ud.user_id
						                                LEFT JOIN
						                                (SELECT
						                                    sd.user_id,
						                                    sd.domain_id,
						                                    sd.user_level,
						                                    sd.user_experience
						                                FROM
						                                    user_domains us
						                                LEFT JOIN domain_details sd ON
						                                    us.user_id = sd.user_id AND us.domain_id = sd.domain_id
						                                ) AS usd ON
						                                    u.id = usd.user_id
						                                WHERE usd.user_id IS NULL AND u.role_id = 2 AND u.status = 1 AND u.is_deleted = 0
						                            ) AS no_skills_users)

						                   , IFNULL(skill_levels.userlevel, JSON_ARRAY()))
						            	)
						            )
						    ) AS all_skills
						FROM (SELECT id, title FROM knowledge_domains UNION SELECT 'No Skills','No Skills') AS skills_noskills
						LEFT JOIN
						(
						    #levels
						    SELECT
						     	distinct_skill_detail_levels.domain_id,
						    	distinct_skill_detail_levels.user_level,
						     	JSON_ARRAYAGG(
						        	JSON_OBJECT(
						           		'name', distinct_skill_detail_levels.user_level,
						               	'type', 'Level',
						                'children', IFNULL(level_experiences.userexperience, JSON_ARRAY())
						            )
						        ) AS userlevel
						    FROM (SELECT DISTINCT domain_id, user_level FROM
						         	(
						             SELECT
						                    sd.user_id,
						                    sd.domain_id,
						                    sd.user_level,
						                    sd.user_experience
						                FROM
						                    user_domains us
						                LEFT JOIN domain_details sd ON
						                    us.user_id = sd.user_id AND us.domain_id = sd.domain_id
						         	) AS usd
						         ) AS distinct_skill_detail_levels


						    LEFT JOIN
							(
						    	#experiences
						        SELECT
						        	distinct_skill_detail_experiences.domain_id,
						         	distinct_skill_detail_experiences.user_level,
						        	distinct_skill_detail_experiences.user_experience,
						         	JSON_ARRAYAGG(
						                JSON_OBJECT(
						                    'name', CONCAT(distinct_skill_detail_experiences.user_experience, IF(distinct_skill_detail_experiences.user_experience='1',' Year',' Years')),
						                    'type', 'Experience',
						                    'children', IFNULL(experience_users.experienceuser, JSON_ARRAY())
						                )
						        	) AS userexperience
						        FROM (SELECT DISTINCT domain_id, user_level, user_experience FROM
										(
						              	SELECT
						                    sd.user_id,
						                    sd.domain_id,
						                    sd.user_level,
						                    sd.user_experience
						                FROM
						                    user_domains us
						                LEFT JOIN domain_details sd ON
						                    us.user_id = sd.user_id AND us.domain_id = sd.domain_id
						         		) AS usd
						             ) AS distinct_skill_detail_experiences
						        LEFT JOIN
						        (
						             #users
						             SELECT
						                usd.domain_id,
						                usd.user_level,
						                usd.user_experience,
						            	usd.user_id,
						            	JSON_ARRAYAGG(
						                    JSON_OBJECT(
						                        'id',
						                        user_details.user_id,
						                        'name', CONCAT(user_details.first_name,' ',user_details.last_name),
						                        'image', case when profile_pic IS NULL or profile_pic = ''
						                                then CONCAT('".SITEURL."','images/placeholders/user/user_1.png')
						                                else CONCAT('".SITEURL.USER_PIC_PATH."', user_details.profile_pic)
						                                end,
						                        'type', 'People',
						                        'value', 1
						                    )
						                ) AS experienceuser
						            FROM
						            	(
						              	SELECT
						                    sd.user_id,
						                    sd.domain_id,
						                    sd.user_level,
						                    sd.user_experience
						                FROM
						                    user_domains us
						                LEFT JOIN domain_details sd ON
						                    us.user_id = sd.user_id AND us.domain_id = sd.domain_id
						         		) AS usd
						            LEFT JOIN user_details
						            ON usd.user_id = user_details.user_id
						            GROUP BY usd.domain_id, usd.user_level, usd.user_experience

						        ) AS experience_users
						        ON  distinct_skill_detail_experiences.domain_id = experience_users.domain_id AND distinct_skill_detail_experiences.user_level = experience_users.user_level AND distinct_skill_detail_experiences.user_experience = experience_users.user_experience
						        GROUP BY distinct_skill_detail_experiences.domain_id, distinct_skill_detail_experiences.user_level

						     ) AS level_experiences
						     ON distinct_skill_detail_levels.domain_id = level_experiences.domain_id AND distinct_skill_detail_levels.user_level = level_experiences.user_level
						     GROUP BY distinct_skill_detail_levels.domain_id

						) AS skill_levels
						ON skills_noskills.id = domain_id");

				// pr($data, 1);
				$view->set('data', $data[0][0]['all_skills']);
			}else if($project_id == "my"){
				$data = $this->UserPermission->query("SELECT
							JSON_OBJECT(
								'id', 'All Skills',
								'name', 'All Skills',
								'type', 'All Skills',
						        'children',
						        	JSON_ARRAYAGG(
						                JSON_OBJECT(
						                    'id', id,
						                    'name', IF(id = 'No Skills', 'No Domains', title),
						                    'type', IF(id = 'No Skills', 'No Skills', 'Skill'),
						                    'value', 1,
						                    'children', IF(id = 'No Skills', IFNULL(
						               			(
						                            SELECT
						                            JSON_ARRAYAGG(
						                                JSON_OBJECT(
						                                    'id',
						                                    no_skills_users.user_id,
						                                    'name', CONCAT(no_skills_users.first_name,' ',no_skills_users.last_name),
						                                    'image', case when profile_pic IS NULL or profile_pic = ''
						                                                then CONCAT('".SITEURL."','images/placeholders/user/user_1.png')
						                                                else CONCAT('".SITEURL.USER_PIC_PATH."', no_skills_users.profile_pic)
						                                                end,
						                                    'type', 'People',
						                                    'value', 1
						                                )
						                            ) AS noskillsusers
						                        FROM
						                        (
						                            #get all my projects
						                            SELECT up2.user_id, d.last_name, d.first_name, d.profile_pic
						                            FROM
						                                user_permissions up1
						                            #get the users in my projects
						                            INNER JOIN user_permissions up2 ON
						                                up1.workspace_id IS NULL AND
						                                #change user_id at runtime
						                                up1.user_id = ".$this->user_id." AND
						                                up1.role IN('Creator', 'Owner', 'Group Owner') AND
														up1.project_id = up2.project_id AND
						                                up2.workspace_id IS NULL
						                            #get their skills
						                            LEFT JOIN user_domains  ON
						                                up2.user_id = user_domains.user_id
						                            #compare their skills to skills in my projects
						                            LEFT JOIN project_domains ON
						                                up1.project_id = project_domains.project_id AND
						                                user_domains.domain_id = project_domains.domain_id
						                            #add user status
						                            LEFT JOIN users
						                            ON
						                                up2.user_id = users.id AND
						                                users.role_id = 2 AND
						                                users.status = 1 AND
						                                users.is_deleted = 0
						                            #add user details
						                            LEFT JOIN user_details d ON
						                                users.id = d.user_id
						                            GROUP BY up2.user_id
						                            #only return those users with no skill matches
													HAVING COUNT(project_domains.domain_id) = 0
						                        ) AS no_skills_users)
						    			, JSON_ARRAY())

						               , IFNULL(skill_levels.userlevel, JSON_ARRAY()))
						            	)
						            )
						    ) AS all_skills
							FROM
							(
							    #all my projects (as owner)
							    SELECT DISTINCT project_domains.domain_id AS id, knowledge_domains.title
							    FROM user_permissions
							    INNER JOIN project_domains
							    ON user_permissions.project_id = project_domains.project_id
							    LEFT JOIN knowledge_domains
							    ON project_domains.domain_id = knowledge_domains.id
							    WHERE
							        user_permissions.user_id = ".$this->user_id." AND #change id at runtime
									user_permissions.workspace_id IS NULL AND
							        user_permissions.role IN ('Creator', 'Owner', 'Group Owner')

							    UNION
							    SELECT 'No Skills','No Skills'

							) AS skills_noskills
							LEFT JOIN
							(
							    #levels
							    SELECT
							     	distinct_skill_detail_levels.domain_id,
							    	distinct_skill_detail_levels.user_level,
							     	JSON_ARRAYAGG(
							        	JSON_OBJECT(
							           		'name', distinct_skill_detail_levels.user_level,
							               	'type', 'Level',
							                'children', IFNULL(level_experiences.userexperience, JSON_ARRAY())
							            )
							        ) AS userlevel
							    FROM
							    (
							    	#all levels for users in all my projects
							        SELECT DISTINCT domain_id, user_level FROM domain_details
							        INNER JOIN
							        (
							        SELECT DISTINCT up1.user_id
							            FROM user_permissions up1
							            INNER JOIN user_permissions up2
							            ON up1.project_id = up2.project_id
							            WHERE
										    up1.workspace_id IS NULL AND
							                up2.user_id = ".$this->user_id." AND #change id at runtime
							                up2.role IN ('Creator', 'Owner', 'Group Owner')
							        ) AS my_project_users1
							        ON domain_details.user_id = my_project_users1.user_id

							    ) AS distinct_skill_detail_levels


							    LEFT JOIN
								(
							    	#experiences
							        SELECT
							        	distinct_skill_detail_experiences.domain_id,
							         	distinct_skill_detail_experiences.user_level,
							        	distinct_skill_detail_experiences.user_experience,
							         	JSON_ARRAYAGG(
							                JSON_OBJECT(
							                    'name', CONCAT(distinct_skill_detail_experiences.user_experience, IF(distinct_skill_detail_experiences.user_experience='1',' Year',' Years')),
							                    'type', 'Experience',
							                    'children', IFNULL(experience_users.experienceuser, JSON_ARRAY())
							                )
							        	) AS userexperience
							        FROM
							        (
							            #all levels and experiences for users in all my projects
							       		SELECT DISTINCT domain_id, user_level, user_experience FROM domain_details
							            INNER JOIN
							            (
							                SELECT DISTINCT up1.user_id
							                FROM user_permissions up1
							                INNER JOIN user_permissions up2
							                ON up1.project_id = up2.project_id
							                WHERE
											up1.workspace_id IS NULL AND
							                up2.user_id = ".$this->user_id." AND #change id at runtime
							                up2.role IN ('Creator', 'Owner', 'Group Owner')
							            ) AS my_project_users2
							            ON domain_details.user_id = my_project_users2.user_id

							        ) AS distinct_skill_detail_experiences

							        LEFT JOIN
							        (
							             #users
							             SELECT
							                domain_details.domain_id,
							                domain_details.user_level,
							                domain_details.user_experience,
							            	domain_details.user_id,
							            	JSON_ARRAYAGG(
							                    JSON_OBJECT(
							                        'id',
							                        user_details.user_id,
							                        'name', CONCAT(user_details.first_name,' ',user_details.last_name),
							                        'image', case when profile_pic IS NULL or profile_pic = ''
							                                    then CONCAT('".SITEURL."','images/placeholders/user/user_1.png')
							                                    else CONCAT('".SITEURL.USER_PIC_PATH."', user_details.profile_pic)
							                                    end,
							                        'type', 'People',
							                        'value', 1
							                    )
							                ) AS experienceuser
							            FROM
							                domain_details
							            LEFT JOIN user_details
							            ON domain_details.user_id = user_details.user_id
							            INNER JOIN
							            (
							                #users in all my projects
							                SELECT DISTINCT up1.user_id
							                FROM user_permissions up1
							                INNER JOIN user_permissions up2
							                ON up1.project_id = up2.project_id
							                WHERE
											up1.workspace_id IS NULL AND
							                up2.user_id = ".$this->user_id." AND #change id at runtime
							                up2.role IN ('Creator', 'Owner', 'Group Owner')
							            ) AS my_project_users3
							            ON user_details.user_id = my_project_users3.user_id

							            GROUP BY domain_details.domain_id, domain_details.user_level, domain_details.user_experience

							        ) AS experience_users
							        ON  distinct_skill_detail_experiences.domain_id = experience_users.domain_id AND distinct_skill_detail_experiences.user_level = experience_users.user_level AND distinct_skill_detail_experiences.user_experience = experience_users.user_experience
							        GROUP BY distinct_skill_detail_experiences.domain_id, distinct_skill_detail_experiences.user_level

							     ) AS level_experiences
							     ON distinct_skill_detail_levels.domain_id = level_experiences.domain_id AND distinct_skill_detail_levels.user_level = level_experiences.user_level
							     GROUP BY distinct_skill_detail_levels.domain_id

							) AS skill_levels
							ON skills_noskills.id = domain_id"
						);
				// pr($data, 1);
				$view->set('data', $data[0][0]['all_skills']);
			}else if($project_id > 0){
				$data = $this->UserPermission->query("SELECT
							JSON_OBJECT(
								'id', 'All Skills',
								'name', 'All Skills',
								'type', 'All Skills',
						        'children',
						        	JSON_ARRAYAGG(
						                JSON_OBJECT(
						                    'id', id,
						                    'name', IF(id = 'No Skills', 'No Domains', title),
						                    'type', IF(id = 'No Skills', 'No Skills', 'Skill'),
						                    'value', 1,
						                    'children', IF(id = 'No Skills', IFNULL(
						               			(
						                            SELECT
						                            JSON_ARRAYAGG(
						                                JSON_OBJECT(
						                                    'id',
						                                    no_skills_users.user_id,
						                                    'name', CONCAT(no_skills_users.first_name,' ',no_skills_users.last_name),
						                                    'image', case when profile_pic IS NULL or profile_pic = ''
						                                                then CONCAT('".SITEURL."','images/placeholders/user/user_1.png')
						                                                else CONCAT('".SITEURL.USER_PIC_PATH."', no_skills_users.profile_pic)
						                                                end,
						                                    'type', 'People',
						                                    'value', 1
						                                )
						                            ) AS noskillsusers
						                        FROM
						                        (
						                            #get specific project
						                            SELECT up2.user_id, d.last_name, d.first_name, d.profile_pic
						                            FROM
						                                user_permissions up1
						                            #get the users in it
						                            INNER JOIN user_permissions up2 ON
						                                up1.workspace_id IS NULL AND
						                                #change user_id at runtime
						                                up1.user_id = ".$this->user_id." AND
						                                up1.role IN('Creator', 'Owner', 'Group Owner') AND
														#change project_id at runtime
						                                up1.project_id = ".$project_id." AND
						                                up1.project_id = up2.project_id AND
						                                up2.workspace_id IS NULL
						                            #get their skills
						                            LEFT JOIN user_domains  ON
						                                up2.user_id = user_domains.user_id
						                            #compare their skills to project skills
						                            LEFT JOIN project_domains ON
						                                up1.project_id = project_domains.project_id AND
						                                user_domains.domain_id = project_domains.domain_id
						                            #add user status
						                            LEFT JOIN users
						                            ON
						                                up2.user_id = users.id AND
						                                users.role_id = 2 AND
						                                users.status = 1 AND
						                                users.is_deleted = 0
						                            #add user details
						                            LEFT JOIN user_details d ON
						                                users.id = d.user_id
						                            GROUP BY up2.user_id
						                            #only return those users with no skill matches
													HAVING COUNT(project_domains.domain_id) = 0
						                        ) AS no_skills_users)
						    			, JSON_ARRAY())

						               , IFNULL(skill_levels.userlevel, JSON_ARRAY()))
						            	)
						            )
						    ) AS all_skills
							FROM
							(
							    #all my projects (as owner)
							    SELECT DISTINCT project_domains.domain_id AS id, knowledge_domains.title
							    FROM user_permissions
							    INNER JOIN project_domains
							    ON user_permissions.project_id = project_domains.project_id
							    LEFT JOIN knowledge_domains
							    ON project_domains.domain_id = knowledge_domains.id
							    WHERE
							        user_permissions.user_id = ".$this->user_id." AND #change id at runtime
							        user_permissions.project_id = ".$project_id." AND
									user_permissions.workspace_id IS NULL AND
							        user_permissions.role IN ('Creator', 'Owner', 'Group Owner')

							    UNION
							    SELECT 'No Skills','No Skills'

							) AS skills_noskills
							LEFT JOIN
							(
							    #levels
							    SELECT
							     	distinct_skill_detail_levels.domain_id,
							    	distinct_skill_detail_levels.user_level,
							     	JSON_ARRAYAGG(
							        	JSON_OBJECT(
							           		'name', distinct_skill_detail_levels.user_level,
							               	'type', 'Level',
							                'children', IFNULL(level_experiences.userexperience, JSON_ARRAY())
							            )
							        ) AS userlevel
							    FROM
							    (
							    	#all levels for users in all my projects
							        SELECT DISTINCT domain_id, user_level FROM domain_details
							        INNER JOIN
							        (
							        SELECT DISTINCT up1.user_id
							            FROM user_permissions up1
							            INNER JOIN user_permissions up2
							            ON up1.project_id = up2.project_id
							            WHERE
										    up1.workspace_id IS NULL AND
							                up2.user_id = ".$this->user_id." AND #change id at runtime
							                up2.project_id = ".$project_id." AND
							                up2.role IN ('Creator', 'Owner', 'Group Owner')
							        ) AS my_project_users1
							        ON domain_details.user_id = my_project_users1.user_id

							    ) AS distinct_skill_detail_levels


							    LEFT JOIN
								(
							    	#experiences
							        SELECT
							        	distinct_skill_detail_experiences.domain_id,
							         	distinct_skill_detail_experiences.user_level,
							        	distinct_skill_detail_experiences.user_experience,
							         	JSON_ARRAYAGG(
							                JSON_OBJECT(
							                    'name', CONCAT(distinct_skill_detail_experiences.user_experience, IF(distinct_skill_detail_experiences.user_experience='1',' Year',' Years')),
							                    'type', 'Experience',
							                    'children', IFNULL(experience_users.experienceuser, JSON_ARRAY())
							                )
							        	) AS userexperience
							        FROM
							        (
							            #all levels and experiences for users in all my projects
							       		SELECT DISTINCT domain_id, user_level, user_experience FROM domain_details
							            INNER JOIN
							            (
							                SELECT DISTINCT up1.user_id
							                FROM user_permissions up1
							                INNER JOIN user_permissions up2
							                ON up1.project_id = up2.project_id
							                WHERE
											up1.workspace_id IS NULL AND
							                up2.user_id = ".$this->user_id." AND #change id at runtime
							                up2.project_id = ".$project_id." AND
							                up2.role IN ('Creator', 'Owner', 'Group Owner')
							            ) AS my_project_users2
							            ON domain_details.user_id = my_project_users2.user_id

							        ) AS distinct_skill_detail_experiences

							        LEFT JOIN
							        (
							             #users
							             SELECT
							                domain_details.domain_id,
							                domain_details.user_level,
							                domain_details.user_experience,
							            	domain_details.user_id,
							            	JSON_ARRAYAGG(
							                    JSON_OBJECT(
							                        'id',
							                        user_details.user_id,
							                        'name', CONCAT(user_details.first_name,' ',user_details.last_name),
							                        'image', case when profile_pic IS NULL or profile_pic = ''
							                                    then CONCAT('".SITEURL."','images/placeholders/user/user_1.png')
							                                    else CONCAT('".SITEURL.USER_PIC_PATH."', user_details.profile_pic)
							                                    end,
							                        'type', 'People',
							                        'value', 1
							                    )
							                ) AS experienceuser
							            FROM
							                domain_details
							            LEFT JOIN user_details
							            ON domain_details.user_id = user_details.user_id
							            INNER JOIN
							            (
							                #users in all my projects
							                SELECT DISTINCT up1.user_id
							                FROM user_permissions up1
							                INNER JOIN user_permissions up2
							                ON up1.project_id = up2.project_id
							                WHERE
											up1.workspace_id IS NULL AND
							                up2.user_id = ".$this->user_id." AND #change id at runtime
							                up2.project_id = ".$project_id." AND
							                up2.role IN ('Creator', 'Owner', 'Group Owner')
							            ) AS my_project_users3
							            ON user_details.user_id = my_project_users3.user_id

							            GROUP BY domain_details.domain_id, domain_details.user_level, domain_details.user_experience

							        ) AS experience_users
							        ON  distinct_skill_detail_experiences.domain_id = experience_users.domain_id AND distinct_skill_detail_experiences.user_level = experience_users.user_level AND distinct_skill_detail_experiences.user_experience = experience_users.user_experience
							        GROUP BY distinct_skill_detail_experiences.domain_id, distinct_skill_detail_experiences.user_level

							     ) AS level_experiences
							     ON distinct_skill_detail_levels.domain_id = level_experiences.domain_id AND distinct_skill_detail_levels.user_level = level_experiences.user_level
							     GROUP BY distinct_skill_detail_levels.domain_id

							) AS skill_levels
							ON skills_noskills.id = domain_id"
						);

				// pr($data, 1);
				$view->set('data', $data[0][0]['all_skills']);
				// $view->set('data', "");
			}
		}


		$html = $view->render('skill_json');
		echo json_encode($html);
		exit;

	}

	function json($project_id = null) {
		$this->autoRender = false;
		$this->layout = false;

				if($project_id == "all"){

					$data3 = 	$this->UserPermission->query("SELECT
									JSON_ARRAYAGG(
										 JSON_OBJECT(
											'source', shared_by_user_id,
											'target', user_id,
											'role', role,
											'project_count', project_count,
											 'workspace_count', workspace_count,
											 'task_count', task_count
										)
									) AS links
								FROM (
									SELECT
										CASE
										WHEN role='Sharer' OR role='Group Sharer'
										THEN 'Sharer'
										ELSE 'Owner'
										END
										AS role,
										shared_by_user_id,
										user_id,
										COUNT(DISTINCT project_id) as project_count,
										COUNT(DISTINCT workspace_id) as workspace_count,
										COUNT(DISTINCT element_id) as task_count
									FROM
										user_permissions
									inner join users on users.id = user_permissions.shared_by_user_id and users.status=1
									inner join users ud on ud.id = user_permissions.user_id   and ud.status = 1
									WHERE
										shared_by_user_id IS NOT NULL
									GROUP BY
										role, shared_by_user_id, user_id
									ORDER BY FIELD(role, 'Shared','Group Shared','Owner')
									) AS grouped_links");

					$data1 = 	$this->UserPermission->query("SELECT
								JSON_ARRAYAGG(
								JSON_OBJECT(
									'id', user_id,
									'name', CONCAT(first_name, ' ', last_name),
									'image', case when profile_pic IS NULL or profile_pic = ''
												then 'user_1.png'
												else profile_pic
										   end
									)
									) AS nodes
								FROM (
									SELECT
										user_details.user_id,
										user_details.first_name,
										user_details.last_name,
										user_details.profile_pic
									FROM
										users
									RIGHT JOIN
										user_details
									ON
										users.id = user_details.user_id
									WHERE
										users.role_id=2 AND
										users.status=1 AND
										users.is_deleted=0
									ORDER BY
									users.id
								)
									AS
										usernodes");

				}else if($project_id == "my"){

				$data1 = 	$this->UserPermission->query("SELECT
								JSON_ARRAYAGG(
								JSON_OBJECT(
									'id', user_id,
									'name', CONCAT(first_name, ' ', last_name),
									'image', case when profile_pic IS NULL or profile_pic = ''
												then 'user_1.png'
												else profile_pic
										   end
									)
									) AS nodes
								FROM (
									SELECT
										user_details.user_id,
										user_details.first_name,
										user_details.last_name,
										user_details.profile_pic

					FROM `user_permissions`
						inner join user_details on user_details.user_id = user_permissions.user_id
						LEFT JOIN users on users.id = user_permissions.user_id
						LEFT JOIN projects on projects.id = user_permissions.project_id

					WHERE user_permissions.project_id IN (SELECT project_id from user_permissions where user_id = $this->user_id and workspace_id is null AND role IN ('Creator', 'Group Owner','Owner')) AND workspace_id is null
				GROUP BY user_permissions.user_id ORDER BY users.id ASC
								)
									AS
										usernodes");


				$data3 = 	$this->UserPermission->query("SELECT
									JSON_ARRAYAGG(
										 JSON_OBJECT(
											#'source', case when shared_by_user_id IS #NULL or shared_by_user_id = ''
											#	then user_id
											#	else shared_by_user_id
										   #end,
										    'source', shared_by_user_id,
											'target', user_id,
											'role', role,
											'project_count', project_count,
											 'workspace_count', workspace_count,
											 'task_count', task_count
										)
									) AS links
								FROM (
									SELECT
										CASE
										WHEN role='Sharer' OR role='Group Sharer'
										THEN 'Sharer'
										ELSE 'Owner'
										END
										AS role,
										shared_by_user_id,
										user_id,
										COUNT(DISTINCT project_id) as project_count,
										COUNT(DISTINCT workspace_id) as workspace_count,
										COUNT(DISTINCT element_id) as task_count
									FROM
										user_permissions
									inner join users on users.id = user_permissions.user_id
									WHERE
										shared_by_user_id IS NOT NULL AND
										#user_id =
										project_id in (SELECT project_id FROM `user_permissions` WHERE `user_id` = $this->user_id AND `workspace_id` IS NULL  AND role IN ('Creator', 'Group Owner','Owner'))
									GROUP BY
										role, shared_by_user_id, user_id
									ORDER BY FIELD(role, 'Shared','Group Shared','Owner')
									) AS grouped_links");



				}else if($project_id > 0){


				$data1 = 	$this->UserPermission->query("SELECT
							JSON_ARRAYAGG(
							JSON_OBJECT(
								'id', user_id,
								'name', CONCAT(first_name, ' ', last_name),
								'image', case when profile_pic IS NULL or profile_pic = ''
										then 'user_1.png'
										else profile_pic
								   end
								)
								) AS nodes
							FROM (
								SELECT DISTINCT
									user_details.user_id,
									user_details.first_name,
									user_details.last_name,
									user_details.profile_pic
								FROM
									user_permissions
								RIGHT JOIN
									user_details
								ON
									user_permissions.user_id = user_details.user_id
								WHERE
									user_permissions.project_id = $project_id #replace at runtime
									AND user_permissions.workspace_id IS NULL
								)
								AS
									usernodes");

					$data3 = 	$this->UserPermission->query("SELECT
									JSON_ARRAYAGG(
										 JSON_OBJECT(
											'source', shared_by_user_id,
											'target', user_id,
											'role', role,
											'project_count', project_count,
											 'workspace_count', workspace_count,
											 'task_count', task_count
										)
									) AS links
								FROM (
									SELECT
										CASE
										WHEN role='Sharer' OR role='Group Sharer'
										THEN 'Sharer'
										ELSE 'Owner'
										END
										AS role,
										shared_by_user_id,
										user_id,
										COUNT(DISTINCT project_id) as project_count,
										COUNT(DISTINCT workspace_id) as workspace_count,
										COUNT(DISTINCT element_id) as task_count
									FROM
										user_permissions
									inner join users on users.id = user_permissions.shared_by_user_id
									WHERE
										project_id = $project_id #replace at runtime
										AND shared_by_user_id IS NOT NULL
									GROUP BY
										role, shared_by_user_id, user_id
									ORDER BY FIELD(role, 'Shared','Group Shared','Owner')
									) AS grouped_links");



				}

				//	pr($data1);
					//pr($data3);

				//echo '{ "nodes":  '.$data1[0][0]["nodes"].',"links":'.$data3[0][0]["links"].'}';

				$data1[0][0]["nodes"] = (isset($data1[0][0]["nodes"]) && !empty($data1[0][0]["nodes"])) ? $data1[0][0]["nodes"] : "{}";

				$data3[0][0]["links"] = (isset($data3[0][0]["links"]) && !empty($data3[0][0]["links"])) ? $data3[0][0]["links"] : "{}";

				echo '{ "nodes":  '.$data1[0][0]["nodes"].',"links":'.$data3[0][0]["links"].'}';

	}

	// Create subdomain
	function index_new($subDomain = null) {

		$this->autoRender = false;

		$cPanelUser = 'ideascast';
		$cPanelPass = '23WT0GgCq2*#b2';
		$rootDomain = 'ideascast.com';
		// $subDomain = 'sagar';

		$buildRequest = "/frontend/paper_lantern/subdomain/doadddomain.html?rootdomain=" . $rootDomain . "&domain=" . $subDomain . "&dir=jeera";

		$openSocket = fsockopen('localhost', 2082);
		if (!$openSocket) {
			return "Socket error";
			exit();
		}

		$authString = $cPanelUser . ":" . $cPanelPass;
		$authPass = base64_encode($authString);
		$buildHeaders = "GET " . $buildRequest . "\r\n";
		$buildHeaders .= "HTTP/1.0\r\n";
		$buildHeaders .= "Host:localhost\r\n";
		$buildHeaders .= "Authorization: Basic " . $authPass . "\r\n";
		$buildHeaders .= "\r\n";

		$result = '';
		fputs($openSocket, $buildHeaders);
		while (!feof($openSocket)) {
			fgets($openSocket, 128);
			$result .= fgets($openSocket, 128);

		}
		fclose($openSocket);
	}

	// List Subdomain
	function listDomain() {

		$this->autoRender = false;

		$cpanelusr = 'ideascast';
		$cpanelpass = '23WT0GgCq2*#b2';
		$rootDomain = 'ideascast.com';
		//$subDomain = 'dsideacast';

		$xmlapi = new XmlApi('127.0.0.1', $cpanelusr, $cpanelpass);
		$xmlapi->set_port(2083);
		$xmlapi->set_output('json');
		$xmlapi->set_hash("username", $cpanelusr);
		$xmlapi->password_auth($cpanelusr, $cpanelpass);
		$xmlapi->set_debug(1);

		/*
			ADD PRIVILLEGES
			$args = array(
						'privileges' => 'ALL',
						'db' => 'ideascas_ramaya',
						'dbuser' => 'ideascas_ramaya',
				  );
			$subdomainsList = $xmlapi->api2_query($cpanelusr, 'MysqlFE', 'setdbuserprivileges', $args);
		*/

		$subdomainsList = $xmlapi->api2_query($cpanelusr, 'SubDomain', 'listsubdomains');

		$result = json_decode($subdomainsList);

		foreach ($result->cpanelresult->data as $domainList) {
			echo $domainList->subdomain . "<br>";
		}

		//$subdomainsList1 = $xmlapi->api2_query($cpanelusr, 'SubDomain', 'listsubdomains',array('subdomainname','ideascast.com',0,0, '/public_html/subdomainname'));
		//$result1 = json_decode($subdomainsList1);
		// pr($result);

	}



	function testmongo($data, $type) {
		//$this->loadModel('Test');

		//$detail = $this->Test->find('all');
		//pr($detail);

		$m = new MongoClient("mongodb://admin:admin123@jeera.ideascast.com:27017");
		$db = $m->selectDB('ideascast');
		/* $collection = new MongoCollection($db, 'users');

			$js = "function() {
				return this.firstname == '$data' || this.lastname == '$data';
			}";

			$where =  array('$or' =>array(	array('firstname' => array('$regex' => new MongoRegex("/^$data/i"))), array('lastname' => array('$regex' => new MongoRegex("/^$data/i")))
			));
		*/

		/*  Get all tables from the database */

		//$collectionsNames = $db->getCollectionNames();

		//$collectionsNames = array('users','projects','organisations','attachments','groups','quotes','messagecounters','userconversions','groupconversions','broadcasts');

		$collectionsNames = array('groups', 'userconversions', 'groupconversions', 'broadcasts');

		foreach ($collectionsNames as $collectionName) {

			//$sql = "SHOW COLUMNS FROM `$tableName`";
			/*  Create collection using the table name */

			$collectionN = new MongoCollection($db, $collectionName);

			/*  Get all fields from the table */

			//$cursorMM = $collectionN->findOne();

			/* Loop for the getting keys or columns and query for search data */

			//foreach ($cursorMM as $key => $docb) {

			$key = 'message';
			$i = '';

			if ($collectionN == 'ideascast.groups') {
				$key = 'title';
			}

			if ($type == 1) {
				$i = '';
				$whereN = array($key => $data);
			} else if ($type == 2) {
				$whereN = array($key => array('$regex' => new MongoRegex("/^$data$/i")));
			} else {
				$i = 'i';
				$whereN = array($key => array('$regex' => new MongoRegex("/$data+/$i")));
			}

			//$whereN = array($key => array('$regex' => new MongoRegex("/$data+/$i")));

			if ($collectionN == 'ideascast.userconversions') {

				$whereN = array($key => array('$regex' => new MongoRegex("/$data+/$i")), 'isPrivate' => false);

				if ($type == 1) {
					$i = '';
					$whereN = array($key => $data, 'isPrivate' => false);
				} else if ($type == 2) {
					$whereN = array($key => array('$regex' => new MongoRegex("/^$data$/i")), 'isPrivate' => false);
				} else {
					$i = 'i';
					$whereN = array($key => array('$regex' => new MongoRegex("/$data+/$i")), 'isPrivate' => false);
				}

			}

			$cursorN = $collectionN->find($whereN);
			$counteer = $collectionN->count($whereN);
			echo "Found collection: ", $collectionName, "( $counteer )</br>";
			$i = 0;
			foreach ($cursorN as $docn) {
				pr($docn);
			}

			//}

		}

		//$cursor = $collection->find(array('$where' =>  $js ));
		die;
	}

	function addsubdomain($subdomain = null) {
		$this->autoRender = false;

		$cPanelUser = CPANELUSR;
		$cPanelPass = CPANELPASS;
		$rootDomain = ROOTDOMAIN;

		$xmlapi = new XmlApi('127.0.0.1', $cPanelUser, $cPanelPass);
		$xmlapi->set_port(2083);
		$xmlapi->set_output('json');
		$xmlapi->set_hash("username", $cPanelUser);
		$xmlapi->password_auth($cPanelUser, $cPanelPass);
		$xmlapi->set_debug(1);

		$user = $database = 'ideascas_dots44';

		$args = array(
			'domain' => $subdomain,
			'rootdomain' => ROOTDOMAIN,
			'canoff' => 0,
			'dir' => '/jeera',
		);

		$createdb = array(
			'db' => 'ideascas_' . $subdomain,
		);

		$createUserargs = array(
			'dbuser' => 'ideascas_' . $subdomain,
			'password' => '12345luggage',
		);

		$argsprivilage = array(
			'privileges' => 'ALL',
			'db' => 'ideascas_' . $subdomain,
			'dbuser' => 'ideascas_' . $subdomain,
		);

		$error = false;
		$message = '';
		$subdomainsList1 = $xmlapi->api2_query($cPanelUser, 'SubDomain', 'addsubdomain', $args);
		$subdomainsList2 = $xmlapi->api2_query($cPanelUser, 'MysqlFE', 'createdb', $createdb);
		$subdomainsList3 = $xmlapi->api2_query($cPanelUser, 'MysqlFE', 'createdbuser', $createUserargs);
		$subdomainsList4 = $xmlapi->api2_query($cPanelUser, 'MysqlFE', 'setdbuserprivileges', $argsprivilage);

		$subdomainsList11 = json_decode($subdomainsList1);
		$subdomainsList12 = json_decode($subdomainsList2);
		$subdomainsList13 = json_decode($subdomainsList3);
		$subdomainsList14 = json_decode($subdomainsList4);

		if (!empty($subdomainsList11->cpanelresult->error)) {

			$error = true;
			$message = $subdomainsList11->cpanelresult->error;

		} else if (!empty($subdomainsList12->cpanelresult->error)) {

			$error = true;
			$message = $subdomainsList12->cpanelresult->error;

		} else if (!empty($subdomainsList13->cpanelresult->error)) {

			$error = true;
			$message = $subdomainsList13->cpanelresult->error;

		} else if (!empty($subdomainsList14->cpanelresult->error)) {

			$error = true;
			$message = $subdomainsList14->cpanelresult->error;
		}

		if ($error) {
			echo $message;
		} else {
			echo "SubDomain Created";
		}

	}

	function deletesubdomain($subdomain = null) {
		$this->autoRender = false;

		$cPanelUser = CPANELUSR;
		$cPanelPass = CPANELPASS;
		$rootDomain = ROOTDOMAIN;

		$xmlapi = new XmlApi('127.0.0.1', $cPanelUser, $cPanelPass);
		$xmlapi->set_port(2083);
		$xmlapi->set_output('json');
		$xmlapi->set_hash("username", $cPanelUser);
		$xmlapi->password_auth($cPanelUser, $cPanelPass);
		$xmlapi->set_debug(1);

		$user = $database = 'dotsdots';

		$delete_domain = array(
			'domain' => $subdomain . '.' . ROOTDOMAIN,
		);

		$delete_database = array(
			'db' => 'ideascas_' . $subdomain,
		);

		$delete_user = array(
			'dbuser' => 'ideascas_' . $subdomain,
		);

		$error = false;
		$message = '';
		$subdomainsList1 = $xmlapi->api2_query($cPanelUser, 'SubDomain', 'delsubdomain', $delete_domain);
		$subdomainsList2 = $xmlapi->api2_query($cPanelUser, 'MysqlFE', 'deletedbuser', $delete_user);
		$subdomainsList3 = $xmlapi->api2_query($cPanelUser, 'MysqlFE', 'deletedb', $delete_database);

		$subdomainsList11 = json_decode($subdomainsList1);
		$subdomainsList22 = json_decode($subdomainsList2);
		$subdomainsList33 = json_decode($subdomainsList3);

		/* pr($subdomainsList11);
			pr($subdomainsList22);
		*/

		if (!empty($subdomainsList11->cpanelresult->error)) {

			$error = true;
			$message = $subdomainsList11->cpanelresult->error;

		} else if (!empty($subdomainsList22->cpanelresult->error)) {

			$error = true;
			$message = $subdomainsList22->cpanelresult->error;

		} else if (!empty($subdomainsList33->cpanelresult->error)) {

			$error = true;
			$message = $subdomainsList33->cpanelresult->error;
		}

		if ($error) {
			echo $message;
		} else {
			echo "Domain, Database and DatabaseUser Deleted";
		}

	}

	function generatestrongpassword($length = 9, $add_dashes = false, $available_sets = 'quct') {
		$this->autoRender = false;
		$sets = array();
		if (strpos($available_sets, 'q') !== false) {
			$sets[] = 'abcdefghjkmnpqrstuvwxyz';
		}

		if (strpos($available_sets, 'u') !== false) {
			$sets[] = 'ABCDEFGHJKMNPQRSTUVWXYZ';
		}

		if (strpos($available_sets, 'c') !== false) {
			$sets[] = '23456789';
		}

		if (strpos($available_sets, 't') !== false) {
			$sets[] = '!@#$%&*?';
		}

		$all = '';
		$password = '';
		foreach ($sets as $set) {
			$password .= $set[array_rand(str_split($set))];
			$all .= $set;
		}
		$all = str_split($all);
		for ($i = 0; $i < $length - count($sets); $i++) {
			$password .= $all[array_rand($all)];
		}

		$password = str_shuffle($password);
		if (!$add_dashes) {
			return $password;
		}

		$dash_len = floor(sqrt($length));
		$dash_str = '';
		while (strlen($password) > $dash_len) {
			$dash_str .= substr($password, 0, $dash_len) . '-';
			$password = substr($password, $dash_len);
		}
		$dash_str .= $password;
		return $dash_str;
	}

	function auth(){
		$this->layout = 'default';
		$this->set('title_for_layout', __('Sign In - OpusView', true));
		$this->set('page_heading', __('Sign In - OpusView', true));
		$this->set('page_subheading', __('Analyze social interactions and relationships', true));


		$userData = $this->User->find('first', ['conditions' => ['User.id' => $this->Auth->user('id')], 'recursive' => -1, 'fields' => ['page_setting_toggle', 'landing_url', 'landing_id']]);
		$userData = (isset($userData) && !empty($userData)) ? $userData['User'] : null;
		$this->set('userData', $userData);

		App::import('Vendor', 'googleAuth/GoogleAuthenticator');

		$ga = new GoogleAuthenticator();
		//pr($this->request->data);
		if(isset($this->request->data) && !empty($this->request->data)){
			if( !isset($_SESSION['secrets'] ) || empty($_SESSION['secrets'] )){

				$_SESSION['secrets'] = $this->request->data['secret'];


			}


			if( isset($_SESSION['secrets'] ) && !empty($_SESSION['secrets'] )){
				$secrets = $_SESSION['secrets'];
			}

			$membership_code = $this->Session->read('Auth.User.UserDetail.membership_code');
			$backup_code = $this->Session->read('Auth.User.UserDetail.backup_code');

			if(isset($membership_code) && !empty($membership_code)){

				$secrets = $membership_code;
			}


			//$code = $this->request->data['code'];
			$code = trim($this->request->data['code']);

			$code = str_replace(' ','',$code);

			$user_id = $this->Session->read('Auth.User.UserDetail.id');
			$role_id = $this->Session->read('Auth.User.role_id');




			if($backup_code == $code){

				if(empty($userData['landing_url'])){
					$userData['landing_url'] ="projects/lists";
				}

				if($role_id == 3){
					$userData['landing_url'] ="organisations/password_policy";
				}

				//unset($_SESSION['secrets']);

				$this->UserDetail->id = $user_id;
				$this->UserDetail->saveField('backup_code', null);

				$_SESSION['check_secrets'] = 1;

				return $this->redirect(SITEURL . $userData['landing_url']);

			}


			//membership_code

			$checkResult = $ga->verifyCode($secrets, $code,2);    // 2 = 2*30sec clock tolerance

			if ($checkResult)
			{
			//echo 'success';
			//echo $userData['landing_url'];
			$this->UserDetail->id = $user_id;
			$this->UserDetail->saveField('membership_code', $secrets);

			if(empty($userData['landing_url'])){
				$userData['landing_url'] ="projects/lists";
			}

			 $_SESSION['check_secrets'] = 1;
			 //unset($_SESSION['secrets']);

			return $this->redirect(SITEURL . $userData['landing_url']);
			}
			else
			{
				$this->set('error', 'Invalid two-factor code.');
				if(isset($this->request->data['type']) && !empty($this->request->data['type'])){
				$this->set('type', $this->request->data['type']);
				}
			//$this->Session->setFlash(__('Invalid two-factor code.', 'error'));
			return false;
			}


		}



	}

	function sa( $project_id = null ) {
		$this->layout = 'inner';
		$this->set('title_for_layout', __('Social Analytics', true));
		$this->set('page_heading', __('Social Analytics', true));
		$this->set('page_subheading', __('Analyze social interactions and relationships', true));

		$user_id = $this->Session->read('Auth.User.id');
		$projects = $this->UserPermission->find('list', array('joins' => [
					[
						'alias' => 'Project',
						'table' => 'projects',
						'type' => 'INNER',
						'conditions' => 'Project.id = UserPermission.project_id',
					]],'conditions' => array('UserPermission.user_id' => $user_id,'UserPermission.workspace_id IS NULL', 'role' => ['Creator', 'Group Owner','Owner'] ),'fields'=>array('Project.id','Project.title')));

		$this->set('project_id', $project_id);
		$crumb = [
			'last' => [
				'data' => [
					'title' => 'Social Analytics',
					'data-original-title' => 'Social Analytics',
				],
			],
		];

		$this->set('projects',$projects);
		$this->set('crumb', $crumb);
	}

	public function sa_location_json($data_section = null) {

		$this->layout = false;
		$this->autoRender = false;

		$view = new View($this, false);
		$view->viewPath = 'Subdomains';
		$view->set("project_id", $data_section);
		$data = [];


		if($data_section == "all"){
			$data = $this->UserPermission->query("SELECT
								JSON_ARRAYAGG(
							    	JSON_OBJECT(
							            'project_id', 'None',
							        	'group', 'All People',
										'data', IFNULL(userTimeRanges.label, JSON_ARRAY())
							    	)
							    ) AS groups
							FROM
							(
							SELECT
								user_id,
							    JSON_ARRAYAGG(
									JSON_OBJECT(
							            'user_id', timeRanges.user_id,
							        	'label', timeRanges.user_name,
										'data', IFNULL(timeRanges.timeRange, JSON_ARRAY())
									)
								) AS label
							FROM
							(
							    SELECT
							        user_locations.user_id,
							    	CONCAT(user_details.first_name,' ',user_details.last_name) AS user_name,
							        JSON_ARRAYAGG(
							            JSON_OBJECT(
							                'timeRange', JSON_ARRAY(start_datetime,IFNULL(end_datetime, NOW())),
							                'val', user_location_types.location
							            )
							        ) AS timeRange
							    FROM
							        user_locations
							    LEFT JOIN user_location_types ON user_locations.user_location_type_id = user_location_types.id
							    LEFT JOIN user_details ON user_locations.user_id = user_details.user_id
							    GROUP BY user_id
							    ORDER BY user_details.first_name, user_details.last_name
							) AS timeRanges
							) AS userTimeRanges"
						);
			$view->set('data', $data[0][0]['groups']);
		}
		else if($data_section == "my"){
			$data = $this->UserPermission->query("SELECT
							JSON_ARRAYAGG(
					    	JSON_OBJECT(
					            'project_id', myProjects.project_id,
					        	'group', myProjects.title,
								'data', IFNULL(myProjects.label, JSON_ARRAY())
					    	)
					    ) AS groups
					FROM
					(
					    SELECT
					        timeRanges.project_id,
					        timeRanges.title,
					        timeRanges.user_id,
					        JSON_ARRAYAGG(
					            JSON_OBJECT(
					                'user_id', timeRanges.user_id,
					                'label', timeRanges.user_name,
					                'data', IFNULL(timeRanges.timeRange, JSON_ARRAY())
					            )
					        ) AS label
					    FROM
					    (
					        #all my projects (as owner)
					        SELECT DISTINCT
					            up2.project_id,
					            projects.title,
					            up2.user_id,
					            CONCAT(user_details.first_name,' ',user_details.last_name) AS user_name,
					                JSON_ARRAYAGG(
					                    JSON_OBJECT(
					                        'timeRange', JSON_ARRAY(
					                            CASE
					                                WHEN start_datetime IS NULL THEN projects.start_date
					                                WHEN start_datetime < projects.start_date THEN projects.start_date
					                                ELSE start_datetime
					                            END,
					                            CASE
					                                WHEN end_datetime IS NULL THEN projects.end_date
					                                WHEN end_datetime > projects.end_date THEN projects.end_date
					                                ELSE end_datetime
					                            END
					                            ),
					                        'val', IFNULL(user_location_types.location,'None')
					                    )
					                ) AS timeRange
					        FROM user_permissions up1
					        #get the users in my projects
					        INNER JOIN user_permissions up2 ON
					            up1.workspace_id IS NULL AND
					            up1.user_id = ".$this->user_id." AND #change user_id at runtime
					            up1.role IN('Creator', 'Owner', 'Group Owner') AND
					            up1.project_id = up2.project_id AND
					            up2.workspace_id IS NULL
					        LEFT JOIN projects ON up2.project_id = projects.id
					        LEFT JOIN user_locations ON up2.user_id = user_locations.user_id
					        LEFT JOIN user_location_types ON user_locations.user_location_type_id = user_location_types.id
					        LEFT JOIN user_details ON up2.user_id = user_details.user_id
					        WHERE
					            (user_locations.start_datetime <= projects.end_date AND (user_locations.end_datetime >= projects.start_date OR user_locations.end_datetime IS NULL))
					        GROUP BY up2.project_id,up2.user_id
					        ORDER BY user_details.first_name, user_details.last_name
					    ) AS timeRanges
					    GROUP BY project_id
					    ORDER BY title
					) AS myProjects");
			$view->set('data', $data[0][0]['groups']);
		}
		else{
			$pdata = $this->UserPermission->query("SELECT title from projects WHERE id = $data_section");
			$project_title = $pdata[0]['projects']['title'];
			$data = $this->UserPermission->query("SELECT
						JSON_ARRAYAGG(
					    	JSON_OBJECT(
					            'project_id', $data_section, #change at runtime
					        	'group', '$project_title', #change at runtime
								'data', IFNULL(userTimeRanges.label, JSON_ARRAY())
					    	)
					    ) AS groups
					FROM
					(
					    SELECT
					        user_id,
					        JSON_ARRAYAGG(
					            JSON_OBJECT(
					                'user_id', timeRanges.user_id,
					                'label', timeRanges.user_name,
					                'data', IFNULL(timeRanges.timeRange, JSON_ARRAY())
					            )
					        ) AS label
					    FROM
					    (
					        #all users for the selected project
					        SELECT DISTINCT
					            user_permissions.user_id,
					            CONCAT(user_details.first_name,' ',user_details.last_name) AS user_name,
					            JSON_ARRAYAGG(
					                JSON_OBJECT(
					                    'timeRange', JSON_ARRAY(
					                        CASE
					                                WHEN start_datetime IS NULL THEN projects.start_date
					                                WHEN start_datetime < projects.start_date THEN projects.start_date
					                                ELSE start_datetime
					                            END,
					                        CASE
					                            WHEN end_datetime IS NULL THEN projects.end_date
					                            WHEN end_datetime > projects.end_date THEN projects.end_date
					                            ELSE end_datetime
					                        END
					                        ),
					                    'val', user_location_types.location
					                )
					            ) AS timeRange
					        FROM
					            user_permissions
					        INNER JOIN user_locations ON user_permissions.user_id = user_locations.user_id
					        LEFT JOIN user_location_types ON user_locations.user_location_type_id = user_location_types.id
					        LEFT JOIN user_details ON user_permissions.user_id = user_details.user_id
					        LEFT JOIN projects ON projects.id = $data_section #change at runtime
					        WHERE
					            user_permissions.workspace_id IS NULL AND
					            user_permissions.project_id = $data_section AND #change at runtime
					            (user_locations.start_datetime <= projects.end_date AND (user_locations.end_datetime >= projects.start_date OR user_locations.end_datetime IS NULL))
					        GROUP BY user_permissions.user_id
					        ORDER BY user_details.first_name, user_details.last_name
					    ) AS timeRanges
					) AS userTimeRanges");
			$view->set('data', $data[0][0]['groups']);
		}

		$html = $view->render('location_json');
		echo json_encode($html);
		exit;
	}

}

?>