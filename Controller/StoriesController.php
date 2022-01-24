<?php
/**
 * Skills Controller
 *
 * This file will render views from views/Currencies/
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
 * @link          http://cakephp.org CakePHP(tm) Task
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('Controller/Component','Auth','Session', 'RequestHandler');
App::uses('CakeEmail', 'Network/Email');
App::uses('Sanitize', 'Utility');
/**
 * Skill Controller
 *
 * @property User $User
 * @property PaginatorComponent $Paginator
 */

class StoriesController  extends AppController {

	public $name = 'Stories';

	public $uses = array('User', 'Story');

	/**
	* Helpers
	*
	* @var array
	*/

	public $objView = null;
	public $user_id = null;
	public $offset = 0;
	public $tab_offset = 0;
	public $is_admin = false;

	public $helpers = array('Html', 'Form', 'Session', 'Time', 'Text','Common','Wiki','Permission');


	public function beforeFilter() {
		parent::beforeFilter();

		$view = new View();
		$this->objView = $view;

		$this->user_id = $this->Auth->user('id');

		$this->offset = 50;
		$this->tab_offset = 50;
		if( $_SERVER['HTTP_HOST'] == '192.168.7.20' ) {
			$this->is_admin = false;
		}
		else{
			$this->is_admin = false;
		}

		if( $this->Session->read('Auth.User.role_id') == 3 || ( $this->Session->read('Auth.User.role_id') == 2 && $this->Session->read('Auth.User.UserDetail.administrator') == 1 ) ){
			$this->is_admin = true;
		}
		$this->set('user_is_admin', $this->is_admin);
		$this->setJsVar('tab_offset', $this->tab_offset);
	}

	public function index($type = null, $data_id = null){

		$this->layout = 'inner';
        $data['title_for_layout'] = __('Stories', true);
        $data['page_heading'] = __('Stories', true);
        $data['page_subheading'] = __('Share business and personal experiences with your Community', true);
		$this->_doc_file_ext();


		$this->setJsVar('image_extension', array('bmp', 'gif', 'jpg', 'pps', 'png') );
		$this->setJsVar('files_extension', array('doc', 'docx','csv','ppt', 'pptx', 'rtf', 'txt','pdf') );

		$crumb = [ 'last' => [
						'data' => [
							'title' => "Stories",
							'data-original-title' => "Stories",
						],
					],
				];
		$this->set('crumb',$crumb);

		$sel_title = '';
		if((isset($type) && !empty($type)) && (isset($data_id) && !empty($data_id))) {
			if($type == 'story'){
				$sel_data = $this->Story->find("first", ['conditions' => ['id' => $data_id], 'fields' => ['name']]);
				$sel_title = $sel_data['Story']['name'];
			}
		}
		$data['sel_type'] = $type;
		$data['sel_title'] = $sel_title;
		$this->setJsVars(['sel_type' => $type, 'sel_title' => $sel_title]);

		$this->set($data);


	}

	public function add($type = null) {

        if ($this->request->isAjax()) {
            $this->layout = 'ajax';

			if( isset($type) && !empty($type) ){

				if($type == 'story'){

					$query = "SELECT JSON_ARRAYAGG(JSON_OBJECT('id',stories.id, 'name',stories.name)) AS all_story,

								( SELECT JSON_ARRAYAGG(JSON_OBJECT( 'id', users.id, 'name', CONCAT_WS(' ',ud.first_name , ud.last_name) )) AS JSON FROM users INNER JOIN user_details ud ON ud.user_id = users.id WHERE users.role_id = 2 AND users.status = 1 AND users.is_activated = 1 ) as all_users,

								( SELECT JSON_ARRAYAGG(JSON_OBJECT( 'id', story_types.id, 'name', story_types.type)) AS JSON FROM story_types ) as all_types,

								( SELECT JSON_ARRAYAGG(JSON_OBJECT( 'id', organizations.id, 'name', organizations.name)) AS JSON FROM organizations ) as all_org,

								( SELECT JSON_ARRAYAGG(JSON_OBJECT( 'id', locations.id, 'name', locations.name)) AS JSON FROM locations ) as all_locations,

								( SELECT JSON_ARRAYAGG(JSON_OBJECT( 'id', departments.id, 'name', departments.name)) AS JSON FROM departments ) as all_dept,

								( SELECT JSON_ARRAYAGG(JSON_OBJECT( 'id', skills.id, 'name', skills.title)) AS JSON FROM skills ) as all_skills,

							    ( SELECT JSON_ARRAYAGG(JSON_OBJECT( 'id', subjects.id, 'name', subjects.title)) AS JSON FROM subjects ) as all_subjects,

								( SELECT JSON_ARRAYAGG(JSON_OBJECT( 'id', knowledge_domains.id, 'name', knowledge_domains.title)) AS JSON FROM knowledge_domains ) as all_domains

							FROM stories";
					$data = $this->Story->query($query);
					$this->set('data', $data);

				}

				$html = $this->render('partials/'.$type.'/add');

			}

			return $html;
			exit();
		}
	}

	public function edit($type = null, $id = null) {

        if ($this->request->isAjax()) {
            $this->layout = 'ajax';

			if( isset($type) && !empty($type) ){
				if($type == 'story'){
					$query = "SELECT
							    stories.*,

								( SELECT JSON_ARRAYAGG(JSON_OBJECT( 'id', stories.id, 'name', stories.name)) AS JSON FROM stories WHERE stories.id <> $id ) as all_story,

							    ( SELECT JSON_ARRAYAGG(JSON_OBJECT( 'id', users.id, 'name', CONCAT_WS(' ',ud.first_name , ud.last_name) )) AS JSON FROM users INNER JOIN user_details ud ON ud.user_id = users.id WHERE users.role_id = 2 AND users.status = 1 AND users.is_activated = 1 ) as all_users,

								( SELECT JSON_ARRAYAGG(JSON_OBJECT( 'id', story_types.id, 'name', story_types.type)) AS JSON FROM story_types ) as all_types,

								( SELECT JSON_ARRAYAGG(JSON_OBJECT( 'id', organizations.id, 'name', organizations.name)) AS JSON FROM organizations ) as all_org,

								( SELECT JSON_ARRAYAGG(JSON_OBJECT( 'id', locations.id, 'name', locations.name)) AS JSON FROM locations ) as all_locations,

								( SELECT JSON_ARRAYAGG(JSON_OBJECT( 'id', departments.id, 'name', departments.name)) AS JSON FROM departments ) as all_dept,

								( SELECT JSON_ARRAYAGG(JSON_OBJECT( 'id', skills.id, 'name', skills.title)) AS JSON FROM skills ) as all_skills,

							    ( SELECT JSON_ARRAYAGG(JSON_OBJECT( 'id', subjects.id, 'name', subjects.title)) AS JSON FROM subjects ) as all_subjects,

								( SELECT JSON_ARRAYAGG(JSON_OBJECT( 'id', knowledge_domains.id, 'name', knowledge_domains.title)) AS JSON FROM knowledge_domains ) as all_domains,
								#########
							    ( SELECT JSON_ARRAYAGG(JSON_OBJECT( 'id', u.id )) AS JSON FROM users u LEFT JOIN story_users su ON su.user_id = u.id WHERE su.story_id = $id ) as selected_users,

								( SELECT JSON_ARRAYAGG(JSON_OBJECT( 'id', org.id )) AS JSON FROM organizations org LEFT JOIN story_organizations sorg ON org.id = sorg.organization_id WHERE sorg.story_id = $id  ) as selected_org,

								( SELECT JSON_ARRAYAGG(JSON_OBJECT( 'id', loc.id )) AS JSON FROM locations loc  LEFT JOIN story_locations sloc ON sloc.location_id = loc.id WHERE sloc.story_id = $id  ) as selected_locations,

								( SELECT JSON_ARRAYAGG(JSON_OBJECT( 'id', dept.id )) AS JSON FROM departments dept LEFT JOIN story_departments sdept ON sdept.department_id = dept.id WHERE sdept.story_id = $id  ) as selected_dept,

								( SELECT JSON_ARRAYAGG(JSON_OBJECT( 'id', sk.id )) AS JSON FROM skills sk LEFT JOIN story_skills ssk ON ssk.skill_id = sk.id WHERE ssk.story_id = $id  ) as selected_skills,

							    ( SELECT JSON_ARRAYAGG(JSON_OBJECT( 'id', sb.id )) AS JSON FROM subjects sb LEFT JOIN story_subjects ssb ON ssb.subject_id = sb.id WHERE ssb.story_id = $id  ) as selected_subjects,

								( SELECT JSON_ARRAYAGG(JSON_OBJECT( 'id', kd.id )) AS JSON FROM knowledge_domains kd  LEFT JOIN story_domains sd ON sd.domain_id = kd.id WHERE sd.story_id = $id  ) as selected_domains,

								( SELECT JSON_ARRAYAGG(JSON_OBJECT( 'id', st.id )) AS JSON FROM stories st LEFT JOIN story_stories ss ON ss.related_story_id = st.id WHERE ss.story_id = $id  ) as selected_stories,

								( SELECT JSON_ARRAYAGG(JSON_OBJECT( 'id', sl.id, 'title', sl.title, 'link', sl.link )) AS JSON FROM story_links sl LEFT JOIN stories st ON st.id = sl.story_id WHERE sl.story_id = $id  ) as selected_links,

								( SELECT JSON_ARRAYAGG(JSON_OBJECT( 'id', sl.id, 'title', sl.title, 'file', sl.filename )) AS JSON FROM story_files sl LEFT JOIN stories st ON st.id = sl.story_id WHERE sl.story_id = $id  ) as selected_files

							FROM `stories`

							WHERE stories.id = $id
							";

						// pr($query);
					$data = $this->Story->query($query);
					$this->set('data', $data);

				}
				$html = $this->render('partials/'.$type.'/edit');

			}

			return $html;
			exit();
		}
	}

	public function view($type = null, $id = null, $tab = null) {

        if ($this->request->isAjax()) {
            $this->layout = 'ajax';
            $data = [];

			if( isset($type) && !empty($type) ){
				if($type == 'story'){
					$this->loadModel('Organization');
					$query ="SELECT
							    stories.*,
							    story_types.type as story_type,
							    details_counts.totalpeoples AS total_people,
							    story_counts.total_stories AS total_story,
							    org_counts.total_organizations AS total_organization,
							    location_counts.total_locations AS total_location,
							    dept_counts.total_depts AS total_department,
							    skill_counts.total_skills AS total_skills,
							    subject_counts.total_subjects AS total_subjects,
							    domain_counts.total_domains AS total_domains,
							    link_counts.total_links AS total_link,
							    file_counts.total_files AS total_file,
							    stories.modified AS updated_on,
							    CONCAT_WS(' ', uc.first_name , uc.last_name) AS created_by,
							    CONCAT_WS(' ', user_details.first_name , user_details.last_name) AS updated_by,

							    ( SELECT JSON_ARRAYAGG(JSON_OBJECT( 'id', story_links.id, 'title', story_links.title, 'link', story_links.link)) AS JSON FROM story_links LEFT JOIN stories on stories.id = story_links.story_id  WHERE stories.id = $id ) as all_links,

							    ( SELECT JSON_ARRAYAGG(JSON_OBJECT( 'id', story_files.id, 'title', story_files.title, 'filename', story_files.filename)) AS JSON FROM story_files LEFT JOIN stories on stories.id = story_files.story_id  WHERE stories.id = $id ) as all_files,

							    ( SELECT JSON_ARRAYAGG(JSON_OBJECT( 'id', skills.id, 'title', skills.title)) AS JSON FROM skills inner join story_skills on story_skills.skill_id = skills.id  WHERE story_skills.story_id = $id ) as all_skills,

							    ( SELECT JSON_ARRAYAGG(JSON_OBJECT( 'id', organizations.id, 'title', organizations.name, 'type', organization_types.type, 'image', organizations.image)) AS JSON FROM organizations inner join story_organizations on story_organizations.organization_id = organizations.id INNER JOIN organization_types on organization_types.id = organizations.type_id WHERE story_organizations.story_id = $id ) as all_org,

							    ( SELECT JSON_ARRAYAGG(JSON_OBJECT( 'id', departments.id, 'title', departments.name, 'image', departments.image)) AS JSON FROM departments inner join story_departments on story_departments.department_id = departments.id  WHERE story_departments.story_id = $id ) as all_dept,

							    ( SELECT JSON_ARRAYAGG(JSON_OBJECT( 'id', stories.id, 'title', stories.name, 'image', stories.image, 'type', story_types.type)) AS JSON FROM stories inner join story_stories on story_stories.related_story_id = stories.id INNER JOIN story_types on story_types.id = stories.type_id WHERE story_stories.story_id = $id ) as all_story,

							    ( SELECT JSON_ARRAYAGG(JSON_OBJECT( 'id', subjects.id, 'title', subjects.title)) AS JSON FROM subjects inner join story_subjects on story_subjects.subject_id = subjects.id  WHERE story_subjects.story_id = $id ) as all_subjects,

	    						( SELECT JSON_ARRAYAGG(JSON_OBJECT( 'id', knowledge_domains.id, 'title', knowledge_domains.title)) AS JSON FROM knowledge_domains inner join story_domains on story_domains.domain_id = knowledge_domains.id  WHERE story_domains.story_id = $id ) as all_domains,

	    						( SELECT JSON_ARRAYAGG(JSON_OBJECT( 'id', locations.id, 'title', locations.name, 'image', locations.image, 'city', locations.city, 'country', countries.countryName)) AS JSON FROM locations inner join story_locations on story_locations.location_id = locations.id inner join countries on countries.id = locations.country_id  WHERE story_locations.story_id = $id ) as all_locations,

	    						( SELECT JSON_ARRAYAGG(JSON_OBJECT( 'title', CONCAT_WS(' ', user_details.first_name, user_details.last_name), 'user_id',user_details.user_id, 'profile_pic', user_details.profile_pic, 'job_title', user_details.job_title, 'organization', user_details.organization_id )) AS JSON FROM user_details left JOIN story_users on story_users.user_id = user_details.user_id LEFT JOIN users usr on usr.id = user_details.user_id  WHERE story_users.story_id = $id and usr.role_id = 2 AND usr.status = 1 AND usr.is_activated = 1) as all_users

							FROM `stories`

							INNER JOIN story_types on story_types.id = stories.type_id
							INNER JOIN user_details uc on stories.created_by = uc.user_id
							INNER JOIN user_details on stories.modified_by = user_details.user_id

							#total people
							LEFT JOIN (
							    SELECT
							        su.story_id,
							        COUNT(DISTINCT(su.user_id)) AS totalpeoples
							        FROM story_users su
							    	GROUP BY su.story_id
							     ) AS details_counts
							ON details_counts.story_id = stories.id

							#total stories
							LEFT JOIN (
							    SELECT
							        ss.story_id,
							        COUNT(DISTINCT(ss.related_story_id)) AS total_stories
							        FROM story_stories ss
							    	GROUP BY ss.story_id
							     ) AS story_counts
							ON story_counts.story_id = stories.id

							#total organizations
							LEFT JOIN (
							    SELECT
							        sorg.story_id,
							        COUNT(DISTINCT(sorg.organization_id)) AS total_organizations
							        FROM story_organizations sorg
							    	GROUP BY sorg.story_id
							     ) AS org_counts
							ON org_counts.story_id = stories.id

							#total locations
							LEFT JOIN (
							    SELECT
							        sl.story_id,
							        COUNT(DISTINCT(sl.location_id)) AS total_locations
							        FROM story_locations sl
							    	GROUP BY sl.story_id
							     ) AS location_counts
							ON location_counts.story_id = stories.id

							#total departments
							LEFT JOIN (
							    SELECT
							        sd.story_id,
							        COUNT(DISTINCT(sd.department_id)) AS total_depts
							        FROM story_departments sd
							    	GROUP BY sd.story_id
							     ) AS dept_counts
							ON dept_counts.story_id = stories.id

							#total skills
							LEFT JOIN (
							    SELECT
							        os.story_id,
							        COUNT(DISTINCT(os.skill_id)) AS total_skills
							        FROM story_skills os
							    	GROUP BY os.story_id
							     ) AS skill_counts
							ON skill_counts.story_id = stories.id

							#total subjects
							LEFT JOIN (
							    SELECT
							        osb.story_id,
							        COUNT(DISTINCT(osb.subject_id)) AS total_subjects
							        FROM story_subjects osb
							    	GROUP BY osb.story_id
							     ) AS subject_counts
							ON subject_counts.story_id = stories.id

							#total domains
							LEFT JOIN (
							    SELECT
							        od.story_id,
							        COUNT(DISTINCT(od.domain_id)) AS total_domains
							        FROM story_domains od
							    	GROUP BY od.story_id
							     ) AS domain_counts
							ON domain_counts.story_id = stories.id

							#link total
							LEFT JOIN (
							    SELECT
							        ol.story_id,
							        COUNT(DISTINCT(ol.id)) AS total_links
							        FROM story_links ol
							    	GROUP BY ol.story_id
							     ) AS link_counts
							ON link_counts.story_id = stories.id

							#file total
							LEFT JOIN (
							    SELECT
							        of.story_id,
							        COUNT(DISTINCT(of.id)) AS total_files
							        FROM story_files of
							        WHERE of.filename != ''
							    	GROUP BY of.story_id
							    ) AS file_counts
							ON file_counts.story_id = stories.id

							WHERE stories.id = $id

							GROUP BY stories.id ";
							// pr($query, 1);
					$data = $this->Story->query($query);
					
					$task_data = [
						'story_id' => $id,						
						'updated_user_id' => $this->user_id,						
						'message' => 'Story viewed',
						'updated' => date("Y-m-d H:i:s"),
					];

					$this->loadModel('StoryActivity');
					$this->StoryActivity->id = null;
					$this->StoryActivity->save($task_data);						
					

				}
			}
			$this->set('data', $data);
			$this->set('tab', $tab);
			$html = $this->render('partials/'.$type.'/view');
			return $html;
			exit();

		}
	}

	public function trash($type = null, $id = null) {

		if ($this->request->isAjax()) {
			$this->layout = false;
			$viewData = null;
			$totalpeople = $total_dept = 0;
			$this->loadModel('Location');
			$this->loadModel('LocationFile');

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;

				$stype = (isset($post['type']) && !empty($post['type'])) ? $post['type'] : '';
				if($stype == 'story' && (isset($post['id']) && !empty($post['id']))) {
					$id = $post['id'];
					// DELETE STORY IMAGE
					$imgData = $this->Story->find('first', ['conditions'=>['Story.id'=>$id], 'fields' => ['image']]);
					if( isset($imgData['Story']['image']) && !empty($imgData['Story']['image']) ){
						$story_image = $imgData['Story']['image'];
						if (file_exists(WWW_ROOT . STORY_IMAGE_PATH . $story_image)) {
							unlink(WWW_ROOT . STORY_IMAGE_PATH . $story_image);
						}
					}
					// DELETE STORY FILES
					$this->loadModel('StoryFile');
					$filesData = $this->StoryFile->find('all', ['conditions'=>['StoryFile.story_id'=>$id], 'fields' => ['filename']]);
					if( isset($filesData) && !empty($filesData) ){
						foreach ($filesData as $key => $value) {
							$loc_file = $value['StoryFile']['filename'];
							if (file_exists(WWW_ROOT . STORY_FILE_PATH . $loc_file)) {
								unlink(WWW_ROOT . STORY_FILE_PATH . $loc_file);
							}
						}
					}
					$this->Story->query("DELETE FROM stories WHERE id = $id");
					$this->Story->query("DELETE FROM story_departments WHERE story_id = $id");
					$this->Story->query("DELETE FROM story_domains WHERE story_id = $id");
					$this->Story->query("DELETE FROM story_files WHERE story_id = $id");
					$this->Story->query("DELETE FROM story_links WHERE story_id = $id");
					$this->Story->query("DELETE FROM story_locations WHERE story_id = $id");
					$this->Story->query("DELETE FROM story_organizations WHERE story_id = $id");
					$this->Story->query("DELETE FROM story_skills WHERE story_id = $id");
					$this->Story->query("DELETE FROM story_subjects WHERE story_id = $id");
					$this->Story->query("DELETE FROM story_users WHERE story_id = $id");
					$this->Story->query("DELETE FROM story_stories WHERE story_id = $id");
					// IF ANOTHER STORY SELECTED THIS STORY
					$this->Story->query("DELETE FROM story_stories WHERE related_story_id = $id");
					echo json_encode(['success' => true]);
					exit();
				}
			}

			$viewData['id'] = $id;
			$this->set($viewData);
			$this->render('partials/'.$type.'/trash');

		}
	}

	public function search_story() {

		if ($this->request->isAjax()) {
			$this->layout = false;
			$viewData = null;
			$type = 'story';

			// if ($this->request->is('post') || $this->request->is('put')) {
			// 	$post = $this->request->data;
			// }

			$viewData['created_users'] = [];
			$viewData['modified_users'] = [];
			$viewData['all_types'] = [];
			$query ="SELECT
						GROUP_CONCAT(DISTINCT(stories.type_id)) AS types,
					    GROUP_CONCAT(DISTINCT(stories.created_by)) AS created_users,
					    GROUP_CONCAT(DISTINCT(stories.modified_by)) AS modified_users

					FROM `stories`
					";
			$data = $this->Story->query($query);
			if(isset($data) && !empty($data)){
				// $all_types = (!empty($data[0][0]['all_types'])) ? json_decode($data[0][0]['all_types'], true) : [];
				$types = $data[0][0]['types'];
				$created_users = $data[0][0]['created_users'];
				$modified_users = $data[0][0]['modified_users'];
				// $viewData['all_types'] = $all_types;

				$tq = "SELECT id, type FROM story_types WHERE id IN($types) ORDER BY type ASC";
				$tu = $this->Story->query($tq);
				$viewData['all_types'] = $tu;

				$cq = "SELECT user_details.user_id, CONCAT_WS(' ', user_details.first_name, user_details.last_name) AS full_name FROM user_details WHERE user_details.user_id IN($created_users) ORDER BY user_details.first_name, user_details.last_name";
				$cu = $this->Story->query($cq);
				$viewData['created_users'] = $cu;

				$mq = "SELECT user_details.user_id, CONCAT_WS(' ', user_details.first_name, user_details.last_name) AS full_name FROM user_details WHERE user_details.user_id IN($modified_users) ORDER BY user_details.first_name, user_details.last_name";
				$mu = $this->Story->query($mq);
				$viewData['modified_users'] = $mu;
			}

			$this->set($viewData);
			$this->render('partials/'.$type.'/search_story');

		}
	}

	public function filter_story() {
		if ($this->request->isAjax()) {
            $this->layout = false;

			$view = new View($this, false);
			$data = [];

            if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				/*$conditions = [];
				if(isset($post['types']) && !empty($post['types'])){
					$types = implode(',', $post['types']);
					$conditions[] = " stories.type_id IN (".$types.") ";
				}
				if(isset($post['created']) && !empty($post['created'])){
					$created = implode(',', $post['created']);
					$conditions[] = " stories.created_by IN (".$created.") ";
				}
				if(isset($post['updated']) && !empty($post['updated'])){
					$updated = implode(',', $post['updated']);
					$conditions[] = " stories.modified_by IN (".$updated.") ";
				}

				if(isset($conditions) && !empty($conditions)){
					$conditions = implode('AND', $conditions);
				}

				$limit_query = 'LIMIT '.$this->offset;

				$order = 'ORDER BY stories.name ASC';

				$query = $this->stories_query();
				$query .= " WHERE $conditions

						GROUP BY stories.id
						$order $limit_query ";

				$details = $this->Story->query($query);
				$data['list_data'] = (isset($details) && !empty($details)) ? $details : [];*/

				$extra = [];
				if(isset($post['extra']) && !empty($post['extra'])){
					$extra = $post['extra'];
				}

				$filter = (isset($post['q']) && strlen($post['q']) > 0 ) ? $post['q'] : '';
				$type = $post['type'];

				$sorting = array();
				$sorting['order'] = 'asc';
				$sorting['coloumn'] = 'name';
				if( isset($post['order']) && !empty($post['order']) ){
					$sorting['order'] = $post['order'];
				}
				if( isset($post['coloumn']) && !empty($post['coloumn']) ){
					$sorting['coloumn'] = $post['coloumn'];
				}

				if($type == 'story'){
					$data['list_data'] = $this->find_stories($filter, '', $sorting, $extra);
				}

			}
		}

		$view->viewPath = $this->name . '/partials/story';
		$view->set($data);
		$html = $view->render('get_list');
		echo json_encode($html);
		exit();
	}

	public function get_row(){


		if ($this->request->isAjax()) {

			$this->layout = false;
			$this->autoRender = false;
			$response = [
				'success' => false,
				'content' => null
			];

			$data = [];

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				// pr($post, 1);

				if( (isset($post['id']) && !empty($post['id'])) && ( isset($post['type']) && !empty($post['type']) ) ){
					$response['success'] = true;
					$id = $post['id'];
					$type = $post['type'];
					if($type == 'story') {
						$query = $this->stories_query();
						$query .= " WHERE stories.id = $id";

						$data = $this->Story->query($query);
					}
					$view = new View($this, false);
					$view->viewPath = $this->name . '/partials/' . $type;
					$view->set('list_data', $data);
					$response['content'] = $view->render('get_row');
				}

			}
			echo json_encode($response);
			exit();
		}
	}

	public function save_story(){

		if ($this->request->isAjax()) {

            $this->layout = 'ajax';
            $response = [
				'success' => false,
				'content' => null,
			];

            if ($this->request->is('post') || $this->request->is('put')) {
            	$post = $this->request->data;
            	// pr($post, 1);
            	$this->loadModel('StoryLink');
            	$this->loadModel('StoryFile');

            	$old_image = '';
            	if(isset($post['main']['name']) && !empty($post['main']['name'])){
            		$details = $post['main'];
            		$related = $post['related'];
            		$main_data = [
						'name' =>  $details['name'],
						'type_id' => $details['type'],
            			'summary' => $details['summary'],
            			'story' => $details['story'],
            			'modified_by' => $this->user_id
            		];
            		if(isset($details['id']) && !empty($details['id'])){
            			$main_data['id'] = $details['id'];

						$imgData = $this->Story->find('first', ['conditions'=>['Story.id'=>$details['id']]]);
						if( isset($imgData['Story']['image']) && !empty($imgData['Story']['image']) ){
							$old_image = $imgData['Story']['image'];
						}

            		}
            		else{
            			$main_data['created_by'] = $this->user_id;
            		}

            		$kd_file = WWW_ROOT . 'uploads/story_temp_files/' . $details['filename'];
	            	$folder_url = WWW_ROOT . STORY_IMAGE_PATH . $post['main']['filename'];
	            	if(isset($details['filename']) && !empty($details['filename'])){
	            		if( !empty($old_image) ){
							unlink(WWW_ROOT . STORY_IMAGE_PATH.$old_image);
						}

		            	$unique_file_name = $this->unique_file_name(WWW_ROOT . STORY_IMAGE_PATH, $post['main']['filename']);
		            	$folder_url = WWW_ROOT . STORY_IMAGE_PATH . $unique_file_name;
		            	if(rename($kd_file, $folder_url)){
		            		$main_data['image'] = $unique_file_name;
		            	}
		            }
		            // pr($main_data, 1);
            		if($this->Story->save($main_data)) {
            			if(isset($details['id']) && !empty($details['id'])){
	            			$kd_id = $details['id'];
	            		}
	            		else{
	            			$kd_id = $this->Story->getLastInsertId();
	            		}

            			$response['success'] = true;
            			if(isset($post['link']) && !empty($post['link'])){
            				$link = $post['link'];
            				foreach ($link as $key => $value) {
								$link_data = [
									'user_id' => $this->user_id,
									'story_id' => $kd_id,
									'title' => $value['title'],
									'link' => $value['url']
								];
								$this->StoryLink->id = null;
								if($this->StoryLink->save($link_data)){

								}
            				}
            			}
            			if(isset($post['file']) && !empty($post['file'])){
            				$file = $post['file'];
            				foreach ($file as $key => $value) {

			            		$df_file = WWW_ROOT . 'uploads/story_temp_files/' . $value['filename'];
				            	$df_folder_url = WWW_ROOT . STORY_FILE_PATH . $value['filename'];
				            	if(isset($value['filename']) && !empty($value['filename'])){
					            	$unique_file_name = $this->unique_file_name(WWW_ROOT . STORY_FILE_PATH, $value['filename']);
		            				$df_folder_url = WWW_ROOT . STORY_FILE_PATH . $unique_file_name;
					            	if(rename($df_file, $df_folder_url)){ }
					            }
								$file_data = [
									'user_id' => $this->user_id,
									'story_id' => $kd_id,
									'title' => $value['title'],
									'filename' => $value['filename']
								];
								// pr($file_data);
								$this->StoryFile->id = null;
								if($this->StoryFile->save($file_data)){

								}
            				}
            			}
            			# ADD STORY PEOPLE
            			if(isset($related['people']) && !empty($related['people'])){
            				if( (isset($details['id']) && !empty($details['id'])) ){
            					$this->Story->query("delete from story_users where story_id = " . $details['id']);
            				}
            				$insertValues = $related['people'];
            				if(isset($insertValues) && !empty($insertValues)){
            					$allData = $insertValues;
            					$qry = "INSERT INTO `story_users` (`story_id`, `user_id`) VALUES ";
            					$qry_arr = [];
	            				foreach ($allData as $key => $value) {
	            					$qry_arr[] = "('$kd_id', '$value')";
	            				}
	            				$qry .= implode(' ,', $qry_arr);
	            				$this->Story->query($qry);
            				}
            			}
            			else if(isset($details['id']) && !empty($details['id'])){
            				$this->Story->query("delete from story_users where story_id = " . $details['id']);
            			}
            			# ADD STORY ORG
            			if(isset($related['organizations']) && !empty($related['organizations'])){
            				if( (isset($details['id']) && !empty($details['id'])) ){
            					$this->Story->query("delete from story_organizations where story_id = " . $details['id']);
            				}
            				$insertValues = $related['organizations'];
            				if(isset($insertValues) && !empty($insertValues)){
            					$allData = $insertValues;
            					$qry = "INSERT INTO `story_organizations` (`story_id`, `organization_id`) VALUES ";
            					$qry_arr = [];
	            				foreach ($allData as $key => $value) {
	            					$qry_arr[] = "('$kd_id', '$value')";
	            				}
	            				$qry .= implode(' ,', $qry_arr);
	            				$this->Story->query($qry);
            				}
            			}
            			else if(isset($details['id']) && !empty($details['id'])){
            				$this->Story->query("delete from story_organizations where story_id = " . $details['id']);
            			}
            			# ADD STORY LOCATIONS
            			if(isset($related['locations']) && !empty($related['locations'])){
            				if( (isset($details['id']) && !empty($details['id'])) ){
            					$this->Story->query("delete from story_locations where story_id = " . $details['id']);
            				}
            				$insertValues = $related['locations'];
            				if(isset($insertValues) && !empty($insertValues)){
            					$allData = $insertValues;
            					$qry = "INSERT INTO `story_locations` (`story_id`, `location_id`) VALUES ";
            					$qry_arr = [];
	            				foreach ($allData as $key => $value) {
	            					$qry_arr[] = "('$kd_id', '$value')";
	            				}
	            				$qry .= implode(' ,', $qry_arr);
	            				$this->Story->query($qry);
            				}
            			}
            			else if(isset($details['id']) && !empty($details['id'])){
            				$this->Story->query("delete from story_locations where story_id = " . $details['id']);
            			}
            			# ADD STORY DEPARTMENT
            			if(isset($related['departments']) && !empty($related['departments'])){
            				if( (isset($details['id']) && !empty($details['id'])) ){
            					$this->Story->query("delete from story_departments where story_id = " . $details['id']);
            				}
            				$insertValues = $related['departments'];
            				if(isset($insertValues) && !empty($insertValues)){
            					$allData = $insertValues;
            					$qry = "INSERT INTO `story_departments` (`story_id`, `department_id`) VALUES ";
            					$qry_arr = [];
	            				foreach ($allData as $key => $value) {
	            					$qry_arr[] = "('$kd_id', '$value')";
	            				}
	            				$qry .= implode(' ,', $qry_arr);
	            				$this->Story->query($qry);
            				}
            			}
            			else if(isset($details['id']) && !empty($details['id'])){
            				$this->Story->query("delete from story_departments where story_id = " . $details['id']);
            			}
            			# ADD STORY SKILL
            			if(isset($related['skills']) && !empty($related['skills'])){
            				if( (isset($details['id']) && !empty($details['id'])) ){
            					$this->Story->query("delete from story_skills where story_id = " . $details['id']);
            				}
            				$insertValues = $related['skills'];
            				if(isset($insertValues) && !empty($insertValues)){
            					$allData = $insertValues;
            					$qry = "INSERT INTO `story_skills` (`story_id`, `skill_id`) VALUES ";
            					$qry_arr = [];
	            				foreach ($allData as $key => $value) {
	            					$qry_arr[] = "('$kd_id', '$value')";
	            				}
	            				$qry .= implode(' ,', $qry_arr);
	            				$this->Story->query($qry);
            				}
            			}
            			else if(isset($details['id']) && !empty($details['id'])){
            				$this->Story->query("delete from story_skills where story_id = " . $details['id']);
            			}
            			# ADD STORY SUBJECT
            			if(isset($related['subjects']) && !empty($related['subjects'])){
            				if( (isset($details['id']) && !empty($details['id'])) ){
            					$this->Story->query("delete from story_subjects where story_id = " . $details['id']);
            				}
            				$insertValues = $related['subjects'];
            				if(isset($insertValues) && !empty($insertValues)){
            					$allData = $insertValues;
            					$qry = "INSERT INTO `story_subjects` (`story_id`, `subject_id`) VALUES ";
            					$qry_arr = [];
	            				foreach ($allData as $key => $value) {
	            					$qry_arr[] = "('$kd_id', '$value')";
	            				}
	            				$qry .= implode(' ,', $qry_arr);
	            				$this->Story->query($qry);
            				}
            			}
            			else if(isset($details['id']) && !empty($details['id'])){
            				$this->Story->query("delete from story_subjects where story_id = " . $details['id']);
            			}
            			# ADD STORY DOMAIN
            			if(isset($related['domains']) && !empty($related['domains'])){
            				if( (isset($details['id']) && !empty($details['id'])) ){
            					$this->Story->query("delete from story_domains where story_id = " . $details['id']);
            				}
            				$insertValues = $related['domains'];
            				if(isset($insertValues) && !empty($insertValues)){
            					$allData = $insertValues;
            					$qry = "INSERT INTO `story_domains` (`story_id`, `domain_id`) VALUES ";
            					$qry_arr = [];
	            				foreach ($allData as $key => $value) {
	            					$qry_arr[] = "('$kd_id', '$value')";
	            				}
	            				$qry .= implode(' ,', $qry_arr);
	            				$this->Story->query($qry);
            				}
            			}
            			else if(isset($details['id']) && !empty($details['id'])){
            				$this->Story->query("delete from story_domains where story_id = " . $details['id']);
            			}
            			# ADD STORY STORIES
            			if(isset($related['stories']) && !empty($related['stories'])){
            				if( (isset($details['id']) && !empty($details['id'])) ){
            					$this->Story->query("delete from story_stories where story_id = " . $details['id']);
            				}
            				$insertValues = $related['stories'];
            				if(isset($insertValues) && !empty($insertValues)){
            					$allData = $insertValues;
            					$qry = "INSERT INTO `story_stories` (`story_id`, `related_story_id`) VALUES ";
            					$qry_arr = [];
	            				foreach ($allData as $key => $value) {
	            					$qry_arr[] = "('$kd_id', '$value')";
	            				}
	            				$qry .= implode(' ,', $qry_arr);
	            				$this->Story->query($qry);
            				}
            			}
            			else if(isset($details['id']) && !empty($details['id'])){
            				$this->Story->query("delete from story_stories where story_id = " . $details['id']);
            			}
            		}
            	}
            }
            echo json_encode($response);
			exit();

        }
    }

	public function filter_data(){
		if ($this->request->isAjax()) {
            $this->layout = false;

			$view = new View($this, false);


            if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				$extra = [];
				if(isset($post['extra']) && !empty($post['extra'])){
					$extra = $post['extra'];
				}

				$filter = (isset($post['q']) && strlen($post['q']) > 0 ) ? $post['q'] : '';
				$type = $post['type'];


				$sorting = array();
				$sorting['order'] = 'asc';
				$sorting['coloumn'] = 'name';
				if( isset($post['order']) && !empty($post['order']) ){
					$sorting['order'] = $post['order'];
				}
				if( isset($post['coloumn']) && !empty($post['coloumn']) ){
					$sorting['coloumn'] = $post['coloumn'];
				}

				if($type == 'story'){
					//$data['list_data'] = $this->find_stories(html_entity_decode($filter, ENT_QUOTES), '', $sorting);
					$data['list_data'] = $this->find_stories($filter, '', $sorting, $extra);
					$view->viewPath = $this->name . '/partials/story';
					$view->set($data);
				}
			}

			$html = $view->render('get_list');
			echo json_encode($html);
			exit();
		}
	}

	public function tab_paging_count() {
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';

			$data = [];
			$count = 0;
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;

				$type = $post['type'];

				$filter = $post['q'];
				$coloumn = $post['coloumn'];
				$order = $post['order'];

				$filter_query = '';
				$ser = '^';
				if(isset($filter) && !empty($filter)){
					$filter_len = strlen($filter);
				//$search_str= Sanitize::escape(like(strtolower($filter), $ser ));

				$search_str= Sanitize::escape(like($filter, $ser ));

				$filter_query = " (stories.name LIKE '%$search_str%' ESCAPE '$ser'  OR stories.summary LIKE '%$search_str%' ESCAPE '$ser'  OR stories.story LIKE  '%$search_str%' ESCAPE '$ser' ) ";

				//$filter_query = " (LOWER(stories.name) LIKE '%$search_str%'  COLLATE utf8mb4_bin ESCAPE '$ser'  OR LOWER(stories.summary) LIKE '%$search_str%'  COLLATE utf8mb4_bin ESCAPE '$ser'  OR LOWER(stories.story) LIKE '%$search_str%'  COLLATE utf8mb4_bin ESCAPE '$ser' ) ";

				//$filter_query = " (stories.name LIKE '%$search_str%' ESCAPE '$ser'  OR stories.summary LIKE '%$search_str%' ESCAPE '$ser'  OR stories.story LIKE '%$search_str%' ESCAPE '$ser' ) ";
				}

				// --- FILTER STORY
				$conditions = [];
				if(isset($post['extra']) && !empty($post['extra'])){
					$extraParams = $post['extra'];
					if(isset($extraParams['types']) && !empty($extraParams['types'])){
						$types = implode(',', $extraParams['types']);
						$conditions[] = " stories.type_id IN (".$types.") ";
					}
					if(isset($extraParams['created']) && !empty($extraParams['created'])){
						$created = implode(',', $extraParams['created']);
						$conditions[] = " stories.created_by IN (".$created.") ";
					}
					if(isset($extraParams['updated']) && !empty($extraParams['updated'])){
						$updated = implode(',', $extraParams['updated']);
						$conditions[] = " stories.modified_by IN (".$updated.") ";
					}

					if(isset($conditions) && !empty($conditions)){
						$conditions = implode('AND', $conditions);
					}
				}
				if(empty($filter_query)) {
					if(empty($conditions)){
						$filter_query = ' 1 ';
					}
					else{
						$filter_query = $conditions;
					}
				}
				else{
					if(!empty($conditions)){
						$filter_query .= ' AND ' . $conditions;
					}
				}
				// pr($filter_query, 1);
				// --- FILTER STORY

				$order_qry = 'ORDER BY stories.name ASC';
				if( isset($coloumn) && !empty($coloumn) ){

					if( isset($coloumn) && !empty($coloumn) && isset($order) && !empty($order) ){
						$order_qry = "ORDER BY ".$coloumn." ".$order;
					}

				}

				if($type == 'story'){
					$query = $this->stories_query();
					$query .= " WHERE $filter_query

							GROUP BY stories.id
							$order_qry";
					$dataAll = $this->Story->query($query);
					if (isset($dataAll) && !empty($dataAll)) {
						$data = $dataAll;
					}
				}
				$count = count($data);
			}

			echo json_encode($count);
			exit;
		}
	}

	protected function find_stories($filter = null, $page = null, $sorting = array(), $extra = array()) {
		$limit_query = ' LIMIT '.$this->offset;
		if(isset($page) && !empty($page)){
			$limit_query = " LIMIT $page, ".$this->offset;
		}

		$filter_query = '';
		if(isset($filter) && !empty($filter)){
			$filter_len = strlen($filter);

			$ser = '^';

			//$search_str= Sanitize::escape(like(strtolower($filter), $ser ));
			$search_str= Sanitize::escape(like($filter, $ser ));

			$filter_query = " (stories.name LIKE '%$search_str%' ESCAPE '$ser'  OR stories.summary LIKE '%$search_str%' ESCAPE '$ser'  OR stories.story LIKE  '%$search_str%' ESCAPE '$ser' ) ";


			//$filter_query = " ((LOWER(stories.name) LIKE binary '%$search_str%' ESCAPE '$ser'  OR LOWER(stories.summary) LIKE binary '%$search_str%' ESCAPE '$ser'  OR LOWER(stories.story) LIKE binary '%$search_str%' ESCAPE '$ser' ) ";

			//$filter_query = " (LOWER(stories.name) LIKE '%$search_str%'  COLLATE utf8mb4_bin ESCAPE '$ser'  OR LOWER(stories.summary) LIKE '%$search_str%'  COLLATE utf8mb4_bin ESCAPE '$ser'  OR LOWER(stories.story) LIKE '%$search_str%'  COLLATE utf8mb4_bin ESCAPE '$ser' ) ";

			//$filter_query = " (stories.name LIKE '%$search_str%' ESCAPE '$ser'  OR stories.summary LIKE '%$search_str%' ESCAPE '$ser'  OR stories.story LIKE '%$search_str%' ESCAPE '$ser' ) ";

			//COLLATE utf8mb4_bin
		}

		// --- FILTER STORY
		$conditions = [];
		if(isset($extra) && !empty($extra)){
			$extraParams = $extra;
			if(isset($extraParams['types']) && !empty($extraParams['types'])){
				$types = implode(',', $extraParams['types']);
				$conditions[] = " stories.type_id IN (".$types.") ";
			}
			if(isset($extraParams['created']) && !empty($extraParams['created'])){
				$created = implode(',', $extraParams['created']);
				$conditions[] = " stories.created_by IN (".$created.") ";
			}
			if(isset($extraParams['updated']) && !empty($extraParams['updated'])){
				$updated = implode(',', $extraParams['updated']);
				$conditions[] = " stories.modified_by IN (".$updated.") ";
			}

			if(isset($conditions) && !empty($conditions)){
				$conditions = implode('AND', $conditions);
			}
		}
		if(empty($filter_query)) {
			if(empty($conditions)){
				$filter_query = ' 1 ';
			}
			else{
				$filter_query = $conditions;
			}
		}
		else{
			if(!empty($conditions)){
				$filter_query .= ' AND ' . $conditions;
			}
		}
		// --- FILTER STORY


		$order = 'ORDER BY stories.name ASC';
		if( isset($sorting) && !empty($sorting) ){

			if( isset($sorting['coloumn']) && !empty($sorting['coloumn']) && isset($sorting['order']) && !empty($sorting['order']) ){
				$order = "ORDER BY ".$sorting['coloumn']." ".$sorting['order'];
			}

		}

		$data = [];

		$query = $this->stories_query();
		$query .= " WHERE $filter_query

				GROUP BY stories.id
				$order $limit_query ";

				  //echo $query;

		$details = $this->Story->query($query);
		$data = (isset($details) && !empty($details)) ? $details : [];

		return $data;
	}

	public function get_stories(){
		if ($this->request->isAjax()) {
            $this->layout = false;

            $title = (isset($this->request->data['title']) && !empty($this->request->data['title'])) ? $this->request->data['title'] : "";
			$data['list_data'] = $this->find_stories($title);

			$view = new View($this, false);
			$view->viewPath = $this->name . '/partials/story';
			$view->set($data);
			$html = $view->render('get_list');

			echo json_encode($html);
			exit();
		}
	}

	public function stories_query(){
		return $query = "SELECT
				    stories.*,
				    story_types.type as story_type,
				    details_counts.totalpeoples AS total_people,
				    story_counts.total_stories AS total_story,
				    org_counts.total_organizations AS total_organization,
				    location_counts.total_locations AS total_location,
				    dept_counts.total_depts AS total_department,
				    skill_counts.total_skills AS total_skills,
				    subject_counts.total_subjects AS total_subjects,
				    domain_counts.total_domains AS total_domains,
				    link_counts.total_links AS total_link,
				    file_counts.total_files AS total_file,
				    stories.modified AS updated_on,
				    CONCAT_WS(' ', uc.first_name , uc.last_name) AS created_by,
				    CONCAT_WS(' ', user_details.first_name , user_details.last_name) AS updated_by

				FROM `stories`

				INNER JOIN story_types on story_types.id = stories.type_id
				INNER JOIN user_details uc on stories.created_by = uc.user_id
				INNER JOIN user_details on stories.modified_by = user_details.user_id

				#total people
				LEFT JOIN (
				    SELECT
				        su.story_id,
				        COUNT(DISTINCT(su.user_id)) AS totalpeoples
				        FROM story_users su
				    	GROUP BY su.story_id
				     ) AS details_counts
				ON details_counts.story_id = stories.id

				#total stories
				LEFT JOIN (
				    SELECT
				        ss.story_id,
				        COUNT(DISTINCT(ss.related_story_id)) AS total_stories
				        FROM story_stories ss
				    	GROUP BY ss.story_id
				     ) AS story_counts
				ON story_counts.story_id = stories.id

				#total organizations
				LEFT JOIN (
				    SELECT
				        sorg.story_id,
				        COUNT(DISTINCT(sorg.organization_id)) AS total_organizations
				        FROM story_organizations sorg
				    	GROUP BY sorg.story_id
				     ) AS org_counts
				ON org_counts.story_id = stories.id

				#total locations
				LEFT JOIN (
				    SELECT
				        sl.story_id,
				        COUNT(DISTINCT(sl.location_id)) AS total_locations
				        FROM story_locations sl
				    	GROUP BY sl.story_id
				     ) AS location_counts
				ON location_counts.story_id = stories.id

				#total departments
				LEFT JOIN (
				    SELECT
				        sd.story_id,
				        COUNT(DISTINCT(sd.department_id)) AS total_depts
				        FROM story_departments sd
				    	GROUP BY sd.story_id
				     ) AS dept_counts
				ON dept_counts.story_id = stories.id

				#total skills
				LEFT JOIN (
				    SELECT
				        os.story_id,
				        COUNT(DISTINCT(os.skill_id)) AS total_skills
				        FROM story_skills os
				    	GROUP BY os.story_id
				     ) AS skill_counts
				ON skill_counts.story_id = stories.id

				#total subjects
				LEFT JOIN (
				    SELECT
				        osb.story_id,
				        COUNT(DISTINCT(osb.subject_id)) AS total_subjects
				        FROM story_subjects osb
				    	GROUP BY osb.story_id
				     ) AS subject_counts
				ON subject_counts.story_id = stories.id

				#total domains
				LEFT JOIN (
				    SELECT
				        od.story_id,
				        COUNT(DISTINCT(od.domain_id)) AS total_domains
				        FROM story_domains od
				    	GROUP BY od.story_id
				     ) AS domain_counts
				ON domain_counts.story_id = stories.id

				#link total
				LEFT JOIN (
				    SELECT
				        ol.story_id,
				        COUNT(DISTINCT(ol.id)) AS total_links
				        FROM story_links ol
				    	GROUP BY ol.story_id
				     ) AS link_counts
				ON link_counts.story_id = stories.id

				#file total
				LEFT JOIN (
				    SELECT
				        of.story_id,
				        COUNT(DISTINCT(of.id)) AS total_files
				        FROM story_files of
				        WHERE of.filename != ''
				    	GROUP BY of.story_id
				    ) AS file_counts
				ON file_counts.story_id = stories.id ";
	}

	public function tab_paging_data(){
		if ($this->request->isAjax()) {
            $this->layout = false;

			$view = new View($this, false);

            if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				// pr($post, 1);
				$extra = [];
				if(isset($post['extra']) && !empty($post['extra'])){
					$extra = $post['extra'];
				}
				$page = $post['page'];
				$type = $post['type'];

				$sorting = array();
				$sorting['order'] = 'asc';
				$sorting['coloumn'] = 'title';
				$q = '';

				if( isset($post['order']) && !empty($post['order']) ){
					$sorting['order'] = $post['order'];
				}
				if( isset($post['coloumn']) && !empty($post['coloumn']) ){
					$sorting['coloumn'] = $post['coloumn'];
				}

				if( isset($post['q']) && !empty($post['q']) ){
					$q = $post['q'];
				}

				if($type == 'story'){
					$data['list_data'] = $this->find_stories($q, $page, $sorting, $extra);
					$view->viewPath = $this->name . '/partials/story';
					$view->set($data);
					$html = $view->render('get_list');
				}

			}

			echo json_encode($html);
			exit();
		}
	}

	public function unique_file_name($path, $filename) {

		if ($pos = strrpos($filename, '.')) {
			$name = substr($filename, 0, $pos);
			$ext = substr($filename, $pos);
		} else {
			$name = $filename;
		}

		$newpath = $path . '/' . $filename;
		$newname = $filename;
		$counter = 0;
		while (file_exists($newpath)) {
			$newname = $name.'_'.$counter.$ext;
			$newpath = $path.'/'.$newname;
			$counter++;
		}
		return $newname;
	}

	public function temp_save_file() {

		if ($this->request->isAjax()) {

            $this->layout = 'ajax';
			$this->autoRender = false;
			$response = [
				'success' => false,
				'content' => null,
			];

			if ($this->request->is('post') || $this->request->is('put')) {
				// pr($this->request->data, 1);
				$folder_url = WWW_ROOT . 'uploads/story_temp_files/';
				$upload_object = null;

				if ( isset($this->request->data['StoryFile']['upload_file']['name']) && !empty($this->request->data['StoryFile']['upload_file']['name']) ) {
					$upload_object = $this->request->data['StoryFile']['upload_file'];
				}
				if ( isset($this->request->data['Story']['image']['name']) && !empty($this->request->data['Story']['image']['name']) ) {
					$upload_object = $this->request->data['Story']['image'];
				}

				if ($upload_object) {
					if (!file_exists($folder_url)) {
						mkdir($folder_url, 0777, true);
					}

					// if file exists, change file name with the saved entry of the record id
					$orgFileName = $upload_object['name'];
					$exists_file = $folder_url . DS . $orgFileName;

					if (!empty($orgFileName)) {

						$tempFile = $upload_object['tmp_name'];

						$unique_file_name = $this->unique_file_name($folder_url,$orgFileName);
						$targetFile = $folder_url . DS . $unique_file_name;
						if (move_uploaded_file($tempFile, $targetFile)) {
							$response['success'] = true;
							$response['content'] = $unique_file_name;
						}
					}

				}
				echo json_encode($response);
				exit();
			}
		}
	}

	public function temp_remove_file(){

		if ($this->request->isAjax()) {

            $this->layout = 'ajax';
			$this->autoRender = false;
			$response = [
				'success' => false,
				'content' => null,
			];

			if ($this->request->is('post') || $this->request->is('put')) {

				if( isset($this->request->data['file']) && !empty($this->request->data['file']) ){
					$unlikpath = WWW_ROOT . 'uploads/story_temp_files/';
					$unlikfile = $this->request->data['file'];
					if( file_exists($unlikpath . $unlikfile) ){
						unlink($unlikpath . $unlikfile);
						$response['success'] = true;
						$response['content'] = 'Success';
					}

				}
				echo json_encode($response);
				exit();
			}

		}
	}

	public function delete_temp_files(){

		if ($this->request->isAjax()) {

            $this->layout = 'ajax';
			$this->autoRender = false;
			$response = [
				'success' => false,
				'content' => null,
			];

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				// pr($post, 1);

				if( isset($this->request->data['files']) && !empty($this->request->data['files']) ){
					$files = $this->request->data['files'];
					$unlikpath = WWW_ROOT . 'uploads/story_temp_files/';
					foreach ($files as $key => $value) {

						$unlikfile = $value;
						if( file_exists($unlikpath . $unlikfile) ){
							unlink($unlikpath . $unlikfile);
							$response['success'] = true;
							$response['content'] = 'Success';
						}

					}

				}
				echo json_encode($response);
				exit();
			}

		}
	}

	public function delete_image( ){

		if ($this->request->isAjax()) {

            $this->layout = 'ajax';
			$this->autoRender = false;
			$response = [
				'success' => false,
				'content' => null,
			];

			if ($this->request->is('post') || $this->request->is('put')) {

				if(  isset($this->request->data['id']) && !empty($this->request->data['id']) && isset($this->request->data['type']) ){

					$model = '';
					$unlikpath = '';
					if( $this->request->data['type'] == 'story' ){
						$model = 'Story';
						$unlikpath = WWW_ROOT . STORY_IMAGE_PATH;
					}
					$this->loadModel($model);

					$id = $this->request->data['id'];
					$fileData = $this->$model->find('first', array('conditions'=>array($model.'.id' => $id ) ) );

					$this->request->data[$model]['image'] = '';
					$this->request->data[$model]['id'] = $id;
					if( $this->$model->save($this->request->data) ){
						if( !empty($fileData) && isset($fileData[$model]['image']) && !empty($fileData[$model]['image']) ){
							$filename = $fileData[$model]['image'];
							if( file_exists($unlikpath . $filename) && !empty($filename) ){
								unlink($unlikpath . $filename);
							}
						}
						$response['success'] = true;
						$response['content'] = 'Success';
					} else {
						$response = [
							'success' => false,
							'content' => 'Not Found',
						];
					}
				}
				echo json_encode($response);
				exit();
			}
		}
	}

	public function delete_files($file_id = null) {

		if ($this->request->isAjax()) {

            $this->layout = 'ajax';
			$this->autoRender = false;
			$response = [
				'success' => false,
				'content' => null,
			];

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;

				if( isset($post['file_id']) && !empty($post['file_id']) && isset($post['type']) && !empty($post['type']) ){

					$model = '';
					if( $post['type'] == 'story' ){
						$model = 'StoryFile';
						$unlikpath = WWW_ROOT . STORY_FILE_PATH;
					}

					$this->loadModel($model);

					$file_id = $post['file_id'];
					$fileData = $this->$model->find('first', array('conditions'=>array($model.'.id' => $file_id ) ) );
					$unlikfile = '';
					if( isset($fileData) && !empty($fileData[$model]['filename']) ){
						$unlikfile = $fileData[$model]['filename'];
					}

					if( $this->$model->delete($file_id) ){
						if( file_exists($unlikpath . $unlikfile) && !empty($unlikfile) ){
							unlink($unlikpath . $unlikfile);
						}
						$response['success'] = true;
						$response['content'] = 'Success';

					} else {
						$response['content'] = 'Not Found';

					}

				}
				echo json_encode($response);
				exit();
			}

		}

	}

	public function delete_links($link_id = null){

		if ($this->request->isAjax()) {

            $this->layout = 'ajax';
			$this->autoRender = false;
			$response = [
				'success' => false,
				'content' => null,
			];

			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;
				if(  isset($post['link_id']) && !empty($post['link_id']) && isset($post['type']) ){

					$model = '';
					if( $post['type'] == 'story' ){
						$model = 'StoryLink';
					}
					$this->loadModel($model);

					$link_id = $post['link_id'];
					if( $this->$model->delete($link_id) ){

						$response['success'] = true;
						$response['content'] = 'Success';

					} else {
						$response['content'] = 'Not Found';
					}

				}
				echo json_encode($response);
				exit();
			}

		}

	}

	public function download_files($type = 'story', $id = null) {

		if (isset($id) && !empty($id)) {

			// Retrieve the file ready for download
			if( $type == 'story' ){
				$model = 'StoryFile';
				$path = STORY_FILE_PATH;
			}

			$this->loadModel($model);

			$data = $this->$model->findById($id);
			if (empty($data)) {
				throw new NotFoundException();
			}

			$response = [
				'success' => false,
				'content' => null,
			];

			if( isset($data) && !empty($data) ) {
				// Send file as response
				$response['content'] = $path . $data[$model]['filename'];
				$response['success'] = true;
			}
			$this->autoRender = false;
			header('Content-Type: application/octet-stream');
			return $this->response->file($response['content'], array('download' => true));
		}

	}

	public function download_temp_files($filename = null) {

		if (isset($filename) && !empty($filename)) {

			if (empty($filename)) {
				throw new NotFoundException();
			}

			$response = [
				'success' => false,
				'content' => null,
			];

			if (isset($filename) && !empty($filename)) {
				// Send file as response
				$response['content'] = STORY_TEMP_PATH.$filename;
				$response['success'] = true;
			}
			$this->autoRender = false;
			return $this->response->file($response['content'], array('download' => true));
		}

	}

}