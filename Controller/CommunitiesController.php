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

class CommunitiesController  extends AppController {

	public $name = 'Communities';

	public $uses = array('User', 'Skill', 'SkillLink', 'SkillFile','Subject', 'SubjectLink', 'SubjectFile','KnowledgeDomain', 'DomainLink', 'DomainFile', 'SkillPdf', 'UserSkill', 'SkillDetail', 'ProjectSkill', 'SubjectPdf', 'UserSubject', 'SubjectDetail', 'ProjectSubject', 'DomainPdf', 'UserDomain' , 'DomainDetail', 'ProjectDomain', 'Department');

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
			$this->is_admin = true;
		}
		else{
			$this->is_admin = false;
		}

		if( $this->Session->read('Auth.User.role_id') == 3 || ( $this->Session->read('Auth.User.role_id') == 2 && $this->Session->read('Auth.User.UserDetail.administrator') == 1 ) ){
			$this->is_admin = true;
		}
		$this->set('user_is_admin', $this->is_admin);
	}

	public function index($type = null, $data_id = null){

		$this->layout = 'inner';
        $data['title_for_layout'] = __('Community', true);
        $data['page_heading'] = __('Community', true);
        $data['page_subheading'] = __('View information about your community', true);
		$this->_doc_file_ext();


		$this->setJsVar('image_extension', array('bmp', 'gif', 'jpg', 'pps', 'png') );
		$this->setJsVar('files_extension', array('doc', 'docx','csv','ppt', 'pptx', 'rtf', 'txt','pdf') );

        // GET SKILLS
        $values = array();
        $this->loadModel('Department');
		$details = $this->Department->query("SELECT * FROM departments ORDER BY name ASC");
		if (isset($details) && !empty($details)) {
			foreach ($details as $val) {
				$values[$val['departments']['id']] = $val['departments']['name'];
			}
		}
		$data['departments'] = $values;

		$crumb = [
					'last' => [
						'data' => [
							'title' => "Community",
							'data-original-title' => "Community",
						],
					],
				];
		$this->set('crumb',$crumb);


		$sel_title = '';
		if((isset($type) && !empty($type)) && (isset($data_id) && !empty($data_id))) {
			if($type == 'dept'){
				$sel_data = $this->Department->find("first", ['conditions' => ['id' => $data_id], 'fields' => ['name']]);
				// $sel_title = htmlentities($sel_data['Department']['name'], ENT_QUOTES);
				$sel_title = $sel_data['Department']['name'];
			}
			else if($type == 'loc'){
				$this->loadModel('Location');
				$sel_data = $this->Location->find("first", ['conditions' => ['id' => $data_id]]);
				$sel_title = html_entity_decode($sel_data['Location']['name']);
			}
			else if($type == 'org'){
				$this->loadModel('Organization');
				$sel_data = $this->Organization->find("first", ['conditions' => ['id' => $data_id]]);
				$sel_title = html_entity_decode($sel_data['Organization']['name']);
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

				if($type == 'dept'){

					$this->loadModel('Skill');
					$this->loadModel('Subject');
					$this->loadModel('KnowledgeDomain');
					$skill_data = $this->Skill->find('list', ['fields' => ['id', 'title'], 'order' => ['title ASC']]);
					$skill_data = array_map(function ($v) {
						return $detail_title = htmlentities($v, ENT_QUOTES, "UTF-8");
					}, $skill_data);
					$this->set('skill_data', $skill_data);
					$subject_data = $this->Subject->find('list', ['fields' => ['id', 'title'], 'order' => ['title ASC']]);
					$subject_data = array_map(function ($v) {
						return $detail_title = htmlentities($v, ENT_QUOTES, "UTF-8");
					}, $subject_data);
					$this->set('subject_data', $subject_data);
					$kd_data = $this->KnowledgeDomain->find('list', ['fields' => ['id', 'title'], 'order' => ['title ASC']]);
					$kd_data = array_map(function ($v) {
						return $detail_title = htmlentities($v, ENT_QUOTES, "UTF-8");
					}, $kd_data);
					$this->set('kd_data', $kd_data);
				}
				else if($type == 'loc'){
					$this->loadModel('LocationType');
					$loc_types = $this->LocationType->find('list', ['fields' => ['id', 'type'], 'order' => ['type ASC']]);
					$this->set('loc_types', $loc_types);

					$this->loadModel('Country');
					$countries = $this->Country->find('all', ['fields' => ['id', 'countryName', 'countryCode'], 'order'=>array("FIELD(Country.countryCode,'US','GB') DESC,countryName ASC") ]);

					$this->set('countries', $countries);

					$this->loadModel('Skill');
					$this->loadModel('Subject');
					$this->loadModel('KnowledgeDomain');
					$skill_data = $this->Skill->find('list', ['fields' => ['id', 'title'], 'order' => ['title ASC']]);
					$skill_data = array_map(function ($v) {
						return $detail_title = htmlentities($v, ENT_QUOTES, "UTF-8");
					}, $skill_data);
					$this->set('skill_data', $skill_data);
					$subject_data = $this->Subject->find('list', ['fields' => ['id', 'title'], 'order' => ['title ASC']]);
					$subject_data = array_map(function ($v) {
						return $detail_title = htmlentities($v, ENT_QUOTES, "UTF-8");
					}, $subject_data);
					$this->set('subject_data', $subject_data);
					$kd_data = $this->KnowledgeDomain->find('list', ['fields' => ['id', 'title'], 'order' => ['title ASC']]);
					$kd_data = array_map(function ($v) {
						return $detail_title = htmlentities($v, ENT_QUOTES, "UTF-8");
					}, $kd_data);
					$this->set('kd_data', $kd_data);

				}
				else if($type == 'org'){
					$this->loadModel('OrganizationType');
					$org_types = $this->OrganizationType->find('list', ['fields' => ['id', 'type'], 'order' => ['type ASC']]);
					$this->set('org_types', $org_types);

					$this->loadModel('Location');
					$locations = $this->Location->find('list', ['fields' => ['id', 'name'], 'order' => ['name ASC']]);
					$locations = array_map(function ($v) {
						return $detail_title = htmlentities($v, ENT_QUOTES, "UTF-8");
					}, $locations);
					$this->set('locations', $locations);

					$this->loadModel('ManageDomain');
					$email_domails = $this->ManageDomain->find('list', ['fields' => ['id', 'domain_name'], 'order' => ['domain_name ASC']]);
					$this->set('email_domails', $email_domails);

					$loc_list = $this->Location->query("SELECT
														    locations.id,
														    locations.name,
														    locations.image,
														    locations.city,
														    countries.countryName as countryName
														FROM `locations`
														INNER JOIN countries on countries.id = locations.country_id
														ORDER BY locations.name ASC"
													);
					$this->set('loc_list', $loc_list);

					$this->loadModel('Skill');
					$this->loadModel('Subject');
					$this->loadModel('KnowledgeDomain');
					$skill_data = $this->Skill->find('list', ['fields' => ['id', 'title'], 'order' => ['title ASC']]);
					$skill_data = array_map(function ($v) {
						return $detail_title = htmlentities($v, ENT_QUOTES, "UTF-8");
					}, $skill_data);
					$this->set('skill_data', $skill_data);
					$subject_data = $this->Subject->find('list', ['fields' => ['id', 'title'], 'order' => ['title ASC']]);
					$subject_data = array_map(function ($v) {
						return $detail_title = htmlentities($v, ENT_QUOTES, "UTF-8");
					}, $subject_data);
					$this->set('subject_data', $subject_data);
					$kd_data = $this->KnowledgeDomain->find('list', ['fields' => ['id', 'title'], 'order' => ['title ASC']]);
					$kd_data = array_map(function ($v) {
						return $detail_title = htmlentities($v, ENT_QUOTES, "UTF-8");
					}, $kd_data);
					$this->set('kd_data', $kd_data);

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
				if($type == 'dept'){
					$this->loadModel('Skill');
					$this->loadModel('Subject');
					$this->loadModel('KnowledgeDomain');
					$skill_data = $this->Skill->find('list', ['fields' => ['id', 'title'], 'order' => ['title ASC']]);
					$skill_data = array_map(function ($v) {
						return $detail_title = htmlentities($v, ENT_QUOTES, "UTF-8");
					}, $skill_data);
					$this->set('skill_data', $skill_data);
					$subject_data = $this->Subject->find('list', ['fields' => ['id', 'title'], 'order' => ['title ASC']]);
					$subject_data = array_map(function ($v) {
						return $detail_title = htmlentities($v, ENT_QUOTES, "UTF-8");
					}, $subject_data);
					$this->set('subject_data', $subject_data);
					$kd_data = $this->KnowledgeDomain->find('list', ['fields' => ['id', 'title'], 'order' => ['title ASC']]);
					$kd_data = array_map(function ($v) {
						return $detail_title = htmlentities($v, ENT_QUOTES, "UTF-8");
					}, $kd_data);
					$this->set('kd_data', $kd_data);

					$qry = "SELECT departments.id, departments.name, departments.image,

							( SELECT JSON_ARRAYAGG(JSON_OBJECT( 'id', skills.id)) AS JSON FROM skills inner join department_skills on department_skills.skill_id = skills.id  WHERE department_skills.department_id = $id ) as skid,

						    ( SELECT JSON_ARRAYAGG(JSON_OBJECT( 'id', subjects.id)) AS JSON FROM subjects inner join department_subjects on department_subjects.subject_id = subjects.id  WHERE department_subjects.department_id = $id ) as sbid,

    						( SELECT JSON_ARRAYAGG(JSON_OBJECT( 'id', knowledge_domains.id)) AS JSON FROM knowledge_domains inner join department_domains on department_domains.domain_id = knowledge_domains.id  WHERE department_domains.department_id = $id ) as dmid

							FROM departments

							LEFT JOIN department_skills  on department_skills.department_id = departments.id
							LEFT JOIN department_subjects  on department_subjects.department_id = departments.id
							LEFT JOIN department_domains  on department_domains.department_id = departments.id

							WHERE departments.id = $id

							group by departments.id";
					$dept_data = $this->Department->query($qry);
					$this->set('dept_data', $dept_data);

					$this->request->data = $this->Department->find('first', array('conditions'=> array('Department.id'=>$id) ) );
				}
				if($type == 'loc'){
					$this->loadModel('LocationType');
					$loc_types = $this->LocationType->find('list', ['fields' => ['id', 'type'], 'order' => ['type ASC']]);
					$this->set('loc_types', $loc_types);

					$this->loadModel('Country');
					$countries = $this->Country->find('all', ['fields' => ['id', 'countryName', 'countryCode'], 'order'=>array("FIELD(Country.countryCode,'US','GB') DESC,countryName ASC") ]);
					$this->set('countries', $countries);

					$this->loadModel('Skill');
					$this->loadModel('Subject');
					$this->loadModel('KnowledgeDomain');
					$skill_data = $this->Skill->find('list', ['fields' => ['id', 'title'], 'order' => ['title ASC']]);
					$skill_data = array_map(function ($v) {
						return $detail_title = htmlentities($v, ENT_QUOTES, "UTF-8");
					}, $skill_data);
					$this->set('skill_data', $skill_data);
					$subject_data = $this->Subject->find('list', ['fields' => ['id', 'title'], 'order' => ['title ASC']]);
					$subject_data = array_map(function ($v) {
						return $detail_title = htmlentities($v, ENT_QUOTES, "UTF-8");
					}, $subject_data);
					$this->set('subject_data', $subject_data);
					$kd_data = $this->KnowledgeDomain->find('list', ['fields' => ['id', 'title'], 'order' => ['title ASC']]);
					$kd_data = array_map(function ($v) {
						return $detail_title = htmlentities($v, ENT_QUOTES, "UTF-8");
					}, $kd_data);
					$this->set('kd_data', $kd_data);

					$this->loadModel('Location');

					$qry = "SELECT locations.*,

							( SELECT JSON_ARRAYAGG(JSON_OBJECT( 'id', skills.id)) AS JSON FROM skills inner join location_skills on location_skills.skill_id = skills.id  WHERE location_skills.location_id = $id ) as skid,

						    ( SELECT JSON_ARRAYAGG(JSON_OBJECT( 'id', subjects.id)) AS JSON FROM subjects inner join location_subjects on location_subjects.subject_id = subjects.id  WHERE location_subjects.location_id = $id ) as sbid,

    						( SELECT JSON_ARRAYAGG(JSON_OBJECT( 'id', knowledge_domains.id)) AS JSON FROM knowledge_domains inner join location_domains on location_domains.domain_id = knowledge_domains.id  WHERE location_domains.location_id = $id ) as dmid

							FROM locations

							LEFT JOIN location_skills  on location_skills.location_id = locations.id
							LEFT JOIN location_subjects  on location_subjects.location_id = locations.id
							LEFT JOIN location_domains  on location_domains.location_id = locations.id

							WHERE locations.id = $id

							group by locations.id";
					$loc_data = $this->Location->query($qry);
					// pr($loc_data, 1);
					$this->set('loc_data', $loc_data);
					$this->request->data = $this->Location->find('first', array('conditions'=> array('Location.id'=>$id) ) );

				}
				else if($type == 'org'){
					$this->loadModel('OrganizationType');
					$org_types = $this->OrganizationType->find('list', ['fields' => ['id', 'type'], 'order' => ['type ASC']]);
					$this->set('org_types', $org_types);

					$this->loadModel('Location');
					$locations = $this->Location->find('list', ['fields' => ['id', 'name'], 'order' => ['name ASC']]);

					$locations = array_map(function ($v) {
						return $detail_title = htmlentities($v, ENT_QUOTES, "UTF-8");
					}, $locations);
					$this->set('locations', $locations);

					$this->loadModel('ManageDomain');
					$email_domails = $this->ManageDomain->find('list', ['fields' => ['id', 'domain_name'], 'order' => ['domain_name ASC']]);
					$this->set('email_domails', $email_domails);

					$this->loadModel('Organization');
					$qry = "SELECT organizations.*,
							( SELECT JSON_ARRAYAGG(JSON_OBJECT( 'id', skills.id)) AS JSON FROM skills inner join organization_skills on organization_skills.skill_id = skills.id  WHERE organization_skills.organization_id = $id ) as skid,

						    ( SELECT JSON_ARRAYAGG(JSON_OBJECT( 'id', subjects.id)) AS JSON FROM subjects inner join organization_subjects on organization_subjects.subject_id = subjects.id  WHERE organization_subjects.organization_id = $id ) as sbid,

    						( SELECT JSON_ARRAYAGG(JSON_OBJECT( 'id', knowledge_domains.id)) AS JSON FROM knowledge_domains inner join organization_domains on organization_domains.domain_id = knowledge_domains.id  WHERE organization_domains.organization_id = $id ) as dmid,
    						( SELECT JSON_ARRAYAGG(JSON_OBJECT( 'id', locations.id )) AS JSON FROM locations inner join organization_locations on organization_locations.location_id = locations.id inner join countries on countries.id = locations.country_id  WHERE organization_locations.organization_id = $id ) as olid,

    						( SELECT JSON_ARRAYAGG(JSON_OBJECT( 'id', manage_domains.id )) AS JSON FROM manage_domains inner join organization_email_domains on organization_email_domains.email_domain_id = manage_domains.id  WHERE organization_email_domains.organization_id = $id ) as oedid

							FROM organizations

							WHERE organizations.id = $id

							group by organizations.id";
					$org_data = $this->Organization->query($qry);
					// pr($org_data, 1);
					$this->set('org_data', $org_data);

					$this->loadModel('Skill');
					$this->loadModel('Subject');
					$this->loadModel('KnowledgeDomain');
					$skill_data = $this->Skill->find('list', ['fields' => ['id', 'title'], 'order' => ['title ASC']]);
					$skill_data = array_map(function ($v) {
						return $detail_title = htmlentities($v, ENT_QUOTES, "UTF-8");
					}, $skill_data);
					$this->set('skill_data', $skill_data);
					$subject_data = $this->Subject->find('list', ['fields' => ['id', 'title'], 'order' => ['title ASC']]);
					$subject_data = array_map(function ($v) {
						return $detail_title = htmlentities($v, ENT_QUOTES, "UTF-8");
					}, $subject_data);
					$this->set('subject_data', $subject_data);
					$kd_data = $this->KnowledgeDomain->find('list', ['fields' => ['id', 'title'], 'order' => ['title ASC']]);
					$kd_data = array_map(function ($v) {
						return $detail_title = htmlentities($v, ENT_QUOTES, "UTF-8");
					}, $kd_data);
					$this->set('kd_data', $kd_data);

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
				if($type == 'org'){
					$this->loadModel('Organization');
					$query ="SELECT
							    organizations.*,
							    organization_types.type as org_type,
							    skill_counts.total_skills AS totalskills,
							    subject_counts.total_subjects AS totalsubjects,
							    domain_counts.total_domains AS totaldomains,
							    link_counts.total_links AS linktotal,
							    file_counts.total_files AS filetotal,
							    details_counts.totalpeoples AS totalpeople,
							    location_counts.total_locations AS total_location,
							    edomain_counts.total_edomains AS total_edomain,
							    story_counts.total_stories AS total_story,
							    organizations.modified AS updated_on,
							    CONCAT_WS(' ', user_details.first_name, user_details.last_name) AS updated_by,

							    ( SELECT JSON_ARRAYAGG(JSON_OBJECT( 'id', organization_links.id, 'title', organization_links.title, 'link', organization_links.link)) AS JSON FROM organization_links LEFT JOIN organizations on organizations.id = organization_links.organization_id  WHERE organizations.id = $id ) as all_links,

							    ( SELECT JSON_ARRAYAGG(JSON_OBJECT( 'id', organization_files.id, 'title', organization_files.title, 'filename', organization_files.filename)) AS JSON FROM organization_files LEFT JOIN organizations on organizations.id = organization_files.organization_id  WHERE organizations.id = $id ) as all_files,

							    ( SELECT JSON_ARRAYAGG(JSON_OBJECT( 'id', skills.id, 'title', skills.title)) AS JSON FROM skills inner join organization_skills on organization_skills.skill_id = skills.id  WHERE organization_skills.organization_id = $id ) as all_skills,

							    ( SELECT JSON_ARRAYAGG(JSON_OBJECT( 'id', subjects.id, 'title', subjects.title)) AS JSON FROM subjects inner join organization_subjects on organization_subjects.subject_id = subjects.id  WHERE organization_subjects.organization_id = $id ) as all_subjects,

	    						( SELECT JSON_ARRAYAGG(JSON_OBJECT( 'id', knowledge_domains.id, 'title', knowledge_domains.title)) AS JSON FROM knowledge_domains inner join organization_domains on organization_domains.domain_id = knowledge_domains.id  WHERE organization_domains.organization_id = $id ) as all_domains,

	    						( SELECT JSON_ARRAYAGG(JSON_OBJECT( 'id', locations.id, 'title', locations.name, 'image', locations.image, 'city', locations.city, 'country', countries.countryName)) AS JSON FROM locations inner join organization_locations on organization_locations.location_id = locations.id inner join countries on countries.id = locations.country_id  WHERE organization_locations.organization_id = $id ) as all_locations,

	    						( SELECT JSON_ARRAYAGG(JSON_OBJECT( 'id', manage_domains.id, 'title', manage_domains.domain_name)) AS JSON FROM manage_domains inner join organization_email_domains on organization_email_domains.email_domain_id = manage_domains.id  WHERE organization_email_domains.organization_id = $id ) as all_email_domains,

	    						( SELECT JSON_ARRAYAGG(JSON_OBJECT( 'full_name', CONCAT_WS(' ', user_details.first_name, user_details.last_name), 'user_id',user_details.user_id, 'profile_pic', user_details.profile_pic, 'job_title', user_details.job_title, 'organization', user_details.organization_id )) AS JSON FROM user_details left JOIN organizations on organizations.id = user_details.organization_id LEFT JOIN users usr on usr.id = user_details.user_id  WHERE organizations.id = $id and usr.role_id = 2 AND usr.status = 1 AND usr.is_activated = 1) as all_users,

	    						( SELECT JSON_ARRAYAGG(JSON_OBJECT( 'id', stories.id, 'title', stories.name, 'image', stories.image, 'type', story_types.type)) AS JSON FROM stories INNER JOIN story_organizations on story_organizations.story_id = stories.id INNER JOIN story_types on story_types.id = stories.type_id WHERE story_organizations.organization_id = $id ) as all_stories

							FROM `organizations`

							INNER JOIN organization_types on organization_types.id = organizations.type_id
							INNER JOIN user_details on organizations.modified_by = user_details.user_id
							#total people
							LEFT JOIN
								(
									SELECT
										usd.organization_id,
										COUNT(DISTINCT(usd.user_id)) AS totalpeoples
									FROM user_details usd
				                    LEFT JOIN users uss on uss.id = usd.user_id
				                    WHERE uss.role_id = 2 AND uss.status = 1 AND uss.is_activated = 1
									GROUP BY usd.organization_id
								 ) AS details_counts
								ON details_counts.organization_id = organizations.id

							#total locations
							LEFT JOIN
								(
							    SELECT
							        olc.organization_id,
							        COUNT(DISTINCT(olc.location_id)) AS total_locations
							        FROM organization_locations olc
							    	GROUP BY olc.organization_id
							     ) AS location_counts
								ON location_counts.organization_id = organizations.id
							#total email domains
							LEFT JOIN
								(
							    SELECT
							        oed.organization_id,
							        COUNT(DISTINCT(oed.email_domain_id)) AS total_edomains
							        FROM organization_email_domains oed
							    	GROUP BY oed.organization_id
							     ) AS edomain_counts
								ON edomain_counts.organization_id = organizations.id
							#total skills
							LEFT JOIN
								(
							    SELECT
							        os.organization_id,
							        COUNT(DISTINCT(os.skill_id)) AS total_skills
							        FROM organization_skills os
							    	GROUP BY os.organization_id
							     ) AS skill_counts
								ON skill_counts.organization_id = organizations.id
							#total subjects
							LEFT JOIN
								(
							    SELECT
							        osb.organization_id,
							        COUNT(DISTINCT(osb.subject_id)) AS total_subjects
							        FROM organization_subjects osb
							    	GROUP BY osb.organization_id
							     ) AS subject_counts
								ON subject_counts.organization_id = organizations.id
							#total domains
							LEFT JOIN
								(
							    SELECT
							        od.organization_id,
							        COUNT(DISTINCT(od.domain_id)) AS total_domains
							        FROM organization_domains od
							    	GROUP BY od.organization_id
							     ) AS domain_counts
								ON domain_counts.organization_id = organizations.id
							#link total
							LEFT JOIN
								(
							    SELECT
							        ol.organization_id,
							        COUNT(DISTINCT(ol.id)) AS total_links
							        FROM organization_links ol
							    	GROUP BY ol.organization_id
							     ) AS link_counts
								ON link_counts.organization_id = organizations.id
							#file total
							LEFT JOIN
								(
							    SELECT
							        of.organization_id,
							        COUNT(DISTINCT(of.id)) AS total_files
							        FROM organization_files of
							        WHERE of.filename != ''
							    	GROUP BY of.organization_id
							    ) AS file_counts
								ON file_counts.organization_id = organizations.id

							#stories total
							LEFT JOIN
								(
							    SELECT
							        sor.organization_id,
							        COUNT(DISTINCT(sor.id)) AS total_stories
							        FROM story_organizations sor
							    	GROUP BY sor.organization_id
							    ) AS story_counts
								ON story_counts.organization_id = organizations.id

							WHERE organizations.id = $id

							GROUP BY organizations.id ";

					$data = $this->Organization->query($query);

					$task_data = [
								'organization_id' => $id,
								'updated_user_id' => $this->user_id,
								'message' => 'Organization viewed',
								'updated' => date("Y-m-d H:i:s"),
							];

					$this->loadModel('OrganizationActivity');
					$this->OrganizationActivity->id = null;
					$this->OrganizationActivity->save($task_data);

				}
				else if($type == 'dept'){
					$query = "SELECT
							    departments.id,
							    departments.name,
							    departments.image,
							    departments.modified,
							    skill_counts.total_skills AS totalskills,
							    subject_counts.total_subjects AS totalsubjects,
							    domain_counts.total_domains AS totaldomains,
							    details_counts.totalpeoples AS totalpeople,
							    departments.modified AS updated_on,
							    CONCAT_WS(' ', user_details.first_name , user_details.last_name) AS updated_by,
							    ( SELECT JSON_ARRAYAGG(JSON_OBJECT( 'full_name', CONCAT_WS(' ',user_details.first_name , user_details.last_name), 'job_role',user_details.job_title, 'user_id', user_details.user_id, 'profile_pic',user_details.profile_pic, 'organization', user_details.organization_id )) AS JSON FROM `users` left join user_details on user_details.user_id = users.id  WHERE users.id IN (select user_details.user_id from user_details where user_details.department_id = departments.id ) and users.role_id = 2 AND users.status = 1 AND users.is_activated = 1 ) as user_detail,

	    						( SELECT JSON_ARRAYAGG(JSON_OBJECT( 'id', stories.id, 'title', stories.name, 'image', stories.image, 'type', story_types.type)) AS JSON FROM stories INNER JOIN story_departments on story_departments.story_id = stories.id INNER JOIN story_types on story_types.id = stories.type_id WHERE story_departments.department_id = $id ) as all_stories


							FROM `departments`

							LEFT JOIN user_details on departments.modified_by = user_details.user_id

							LEFT JOIN
								(
								SELECT
									usd.department_id,
									COUNT(DISTINCT(usd.user_id)) AS totalpeoples
								FROM user_details usd
		                        left join users uss on uss.id = usd.user_id
		                        where uss.role_id =2 AND uss.status = 1 AND uss.is_activated = 1
								GROUP BY usd.department_id
								 ) AS details_counts
								ON details_counts.department_id = departments.id

							#total skills
							LEFT JOIN
								(
							    SELECT
							        ls.department_id,
							        COUNT(DISTINCT(ls.skill_id)) AS total_skills
							        FROM department_skills ls
							    	GROUP BY ls.department_id
							     ) AS skill_counts
								ON skill_counts.department_id = departments.id
							#total subjects
							LEFT JOIN
								(
							    SELECT
							        ls.department_id,
							        COUNT(DISTINCT(ls.subject_id)) AS total_subjects
							        FROM department_subjects ls
							    	GROUP BY ls.department_id
							     ) AS subject_counts
								ON subject_counts.department_id = departments.id
							#total domains
							LEFT JOIN
								(
							    SELECT
							        ld.department_id,
							        COUNT(DISTINCT(ld.domain_id)) AS total_domains
							        FROM department_domains ld
							    	GROUP BY ld.department_id
							     ) AS domain_counts
								ON domain_counts.department_id = departments.id

							WHERE departments.id = $id

							GROUP BY departments.id ";

					$data = $this->Department->query($query);

					$task_data = [
						'department_id' => $id,
						'updated_user_id' => $this->user_id,
						'message' => 'Department viewed',
						'updated' => date("Y-m-d H:i:s"),
					];

					$this->loadModel('DepartmentActivity');
					$this->DepartmentActivity->id = null;
					$this->DepartmentActivity->save($task_data);

				}
				else if($type == 'loc'){
					// $data = $this->get_row_detail($id);
					$query ="SELECT
						    locations.id,
						    locations.name,
						    locations.image,
						    locations.city,
						    locations.address,
						    locations.information,
						    locations.modified,
						    locations.modified_by,
						    locations.zip,
						    countries.countryName as countryName,
						    states.name as stateName,
						    location_types.type as type,
						    org_counts.used_org AS totalorg,
						    people_counts.used_user AS totalpeople,
						    skill_counts.total_skills AS totalskills,
						    subject_counts.total_subjects AS totalsubjects,
						    domain_counts.total_domains AS totaldomains,
						    link_counts.total_links AS linktotal,
						    file_counts.total_files AS filetotal,
						    story_counts.total_stories AS total_story,
						    locations.modified AS updated_on,
						    CONCAT_WS(' ', user_details.first_name , user_details.last_name) AS updated_by,

						    ( SELECT JSON_ARRAYAGG(JSON_OBJECT( 'id', location_links.id, 'title', location_links.title, 'link', location_links.link)) AS JSON FROM location_links LEFT JOIN locations on locations.id = location_links.location_id  WHERE locations.id = $id ) as all_links,

						    ( SELECT JSON_ARRAYAGG(JSON_OBJECT( 'id', location_files.id, 'title', location_files.title, 'filename', location_files.filename)) AS JSON FROM location_files LEFT JOIN locations on locations.id = location_files.location_id  WHERE locations.id = $id ) as all_files,

						    ( SELECT JSON_ARRAYAGG(JSON_OBJECT( 'id', skills.id, 'title', skills.title)) AS JSON FROM skills inner join location_skills on location_skills.skill_id = skills.id  WHERE location_skills.location_id = $id ) as all_skills,

						    ( SELECT JSON_ARRAYAGG(JSON_OBJECT( 'id', subjects.id, 'title', subjects.title)) AS JSON FROM subjects inner join location_subjects on location_subjects.subject_id = subjects.id  WHERE location_subjects.location_id = $id ) as all_subjects,

    						( SELECT JSON_ARRAYAGG(JSON_OBJECT( 'id', knowledge_domains.id, 'title', knowledge_domains.title)) AS JSON FROM knowledge_domains inner join location_domains on location_domains.domain_id = knowledge_domains.id  WHERE location_domains.location_id = $id ) as all_domains,

    						( SELECT JSON_ARRAYAGG(JSON_OBJECT( 'id', organizations.id, 'title', organizations.name, 'image', organizations.image, 'type', organization_types.type)) AS JSON FROM organizations inner join organization_locations on organization_locations.organization_id = organizations.id INNER JOIN organization_types on organization_types.id = organizations.type_id WHERE organization_locations.location_id = $id ) as all_organizations,

    						( SELECT JSON_ARRAYAGG(JSON_OBJECT( 'full_name', CONCAT_WS(' ', user_details.first_name, user_details.last_name), 'user_id',user_details.user_id, 'profile_pic', user_details.profile_pic, 'job_title', user_details.job_title, 'organization', user_details.organization_id )) AS JSON FROM user_details left JOIN locations on locations.id = user_details.location_id LEFT JOIN users usr on usr.id = user_details.user_id  WHERE locations.id = $id and usr.role_id = 2 AND usr.status = 1 AND usr.is_activated = 1) as all_users,


	    					( SELECT JSON_ARRAYAGG(JSON_OBJECT( 'id', stories.id, 'title', stories.name, 'image', stories.image, 'type', story_types.type)) AS JSON FROM stories INNER JOIN story_locations on story_locations.story_id = stories.id INNER JOIN story_types on story_types.id = stories.type_id WHERE story_locations.location_id = $id ) as all_stories

						FROM `locations`

						INNER JOIN location_types on location_types.id = locations.type_id
						inner join user_details on locations.modified_by = user_details.user_id
						inner join countries on countries.id = locations.country_id
						inner join states on states.id = locations.state_id
						#total org
						LEFT JOIN
							(
						    	SELECT ol.location_id, count(DISTINCT(ol.organization_id)) as used_org
						    	FROM organization_locations ol
						    	GROUP BY ol.location_id
						     ) AS org_counts
							ON org_counts.location_id = locations.id

						#total people
						LEFT JOIN
							(
						    	SELECT ol.id, COUNT(DISTINCT(ud.user_id)) as used_user
						    	FROM user_details ud
						    	LEFT JOIN users usr ON usr.id = ud.user_id
						    	INNER JOIN locations ol ON ol.id = ud.location_id
						    	WHERE usr.role_id = 2 AND usr.status = 1 AND usr.is_activated = 1
						    	GROUP BY ol.id
						     ) AS people_counts
							ON people_counts.id = locations.id

						#total skills
						LEFT JOIN
							(
						    SELECT
						        ls.location_id,
						        COUNT(DISTINCT(ls.skill_id)) AS total_skills
						        FROM location_skills ls
						    	GROUP BY ls.location_id
						     ) AS skill_counts
							ON skill_counts.location_id = locations.id
						#total subjects
						LEFT JOIN
							(
						    SELECT
						        ls.location_id,
						        COUNT(DISTINCT(ls.subject_id)) AS total_subjects
						        FROM location_subjects ls
						    	GROUP BY ls.location_id
						     ) AS subject_counts
							ON subject_counts.location_id = locations.id
						#total domains
						LEFT JOIN
							(
						    SELECT
						        ld.location_id,
						        COUNT(DISTINCT(ld.domain_id)) AS total_domains
						        FROM location_domains ld
						    	GROUP BY ld.location_id
						     ) AS domain_counts
							ON domain_counts.location_id = locations.id
						#link total
						LEFT JOIN
							(
						    SELECT
						        ll.location_id,
						        COUNT(DISTINCT(ll.id)) AS total_links
						        FROM location_links ll
						    	GROUP BY ll.location_id
						     ) AS link_counts
							ON link_counts.location_id = locations.id
						#file total
						LEFT JOIN
							(
						    SELECT
						        lf.location_id,
						        COUNT(DISTINCT(lf.id)) AS total_files
						        FROM location_files lf
						        WHERE lf.filename != ''
						    	GROUP BY lf.location_id
						    ) AS file_counts
							ON file_counts.location_id = locations.id
						#story total
						LEFT JOIN
							(
						    SELECT
						        lf.location_id,
						        COUNT(DISTINCT(lf.id)) AS total_stories
						        FROM story_locations lf
						    	GROUP BY lf.location_id
						    ) AS story_counts
							ON story_counts.location_id = locations.id

						WHERE locations.id = $id

						group by locations.id ";
					$this->loadModel('Location');
					$data = $this->Location->query($query);

					$task_data = [
						'location_id' => $id,
						'updated_user_id' => $this->user_id,
						'message' => 'Location viewed',
						'updated' => date("Y-m-d H:i:s"),
					];

					$this->loadModel('LocationActivity');
					$this->LocationActivity->id = null;
					$this->LocationActivity->save($task_data);

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
				if($stype == 'dept' && (isset($post['id']) && !empty($post['id']))) {
					if(isset($post['dept_id']) && !empty($post['dept_id'])){
						$this->loadModel('UserDetail');
						$this->UserDetail->updateAll(array('department_id' => $post['dept_id']), array('department_id' => $post['id']));
						if ($this->Department->delete(['Department.id' => $post['id']], false)) {
							echo json_encode(['success' => true]);
							exit();
						}
					}
					else{
						if ($this->Department->delete(['Department.id' => $post['id']], false)) {
							echo json_encode(['success' => true]);
							exit();
						}
					}
				}
				else if($stype == 'loc' && (isset($post['id']) && !empty($post['id']))) {
					$imgData = $this->Location->find('first', ['conditions'=>['Location.id'=>$post['id']], 'fields' => ['image']]);
					if( isset($imgData['Location']['image']) && !empty($imgData['Location']['image']) ){
						$loc_image = $imgData['Location']['image'];
						if (file_exists(WWW_ROOT . LOC_IMAGE_PATH . $loc_image)) {
							unlink(WWW_ROOT . LOC_IMAGE_PATH . $loc_image);
						}
					}
					$this->Location->query("delete from locations where id = " . $post['id']);
					// delete location links
					$filesData = $this->LocationFile->find('all', ['conditions'=>['LocationFile.location_id'=>$post['id']], 'fields' => ['filename']]);
					if( isset($filesData) && !empty($filesData) ){
						foreach ($filesData as $key => $value) {
							$loc_file = $value['LocationFile']['filename'];
							if (file_exists(WWW_ROOT . LOC_FILE_PATH . $loc_file)) {
								unlink(WWW_ROOT . LOC_FILE_PATH . $loc_file);
							}
						}
					}
					$this->Location->query("delete from location_files where location_id = " . $post['id']);
					$this->Location->query("delete from location_links where location_id = " . $post['id']);
					$this->Location->query("delete from location_skills where location_id = " . $post['id']);
					$this->Location->query("delete from location_subjects where location_id = " . $post['id']);
					$this->Location->query("delete from location_domains where location_id = " . $post['id']);
					$this->loadModel('OrganizationLocation');
					$orgLocId = $this->OrganizationLocation->find('all', ['conditions' => ['location_id' => $post['id']], 'fields' => ['location_id']]);
					// pr($orgLocId, 1);
					if(isset($orgLocId) && !empty($orgLocId)) {
						$orgLocId = Set::extract($orgLocId, '/OrganizationLocation/location_id');
						foreach ($orgLocId as $key => $value) {
							$this->Location->query("update user_details set location_id = NULL where location_id = " . $value);
						}
					}
					$this->Location->query("delete from organization_locations where location_id = " . $post['id']);

					echo json_encode(['success' => true]);
					exit();
				}
				else if($stype == 'org' && (isset($post['id']) && !empty($post['id']))) {
					$this->loadModel('Organization');
					$this->loadModel('OrganizationFile');
					$imgData = $this->Organization->find('first', ['conditions'=>['Organization.id'=>$post['id']], 'fields' => ['image']]);
					if( isset($imgData['Organization']['image']) && !empty($imgData['Organization']['image']) ){
						$loc_image = $imgData['Organization']['image'];
						if (file_exists(WWW_ROOT . ORG_IMAGE_PATH . $loc_image)) {
							unlink(WWW_ROOT . ORG_IMAGE_PATH . $loc_image);
						}
					}
					$this->Organization->query("delete from organizations where id = " . $post['id']);
					// delete org files
					$filesData = $this->OrganizationFile->find('all', ['conditions'=>['OrganizationFile.organization_id'=>$post['id']], 'fields' => ['filename']]);
					if( isset($filesData) && !empty($filesData) ){
						foreach ($filesData as $key => $value) {
							$loc_file = $value['OrganizationFile']['filename'];
							if (file_exists(WWW_ROOT . ORG_FILE_PATH . $loc_file)) {
								unlink(WWW_ROOT . ORG_FILE_PATH . $loc_file);
							}
						}
					}
					$org_id = $post['id'];
					$this->Organization->query("delete from organization_files where organization_id = " . $post['id']);
					$this->Organization->query("delete from organization_links where organization_id = " . $post['id']);
					$this->Organization->query("delete from organization_skills where organization_id = " . $post['id']);
					$this->Organization->query("delete from organization_subjects where organization_id = " . $post['id']);
					$this->Organization->query("delete from organization_domains where organization_id = " . $post['id']);
					$this->Organization->query("delete from organization_locations where organization_id = " . $post['id']);
					$this->Organization->query("delete from organization_email_domains where organization_id = " . $post['id']);

					$this->Organization->query("update user_details set location_id = NULL, organization_id = NULL where organization_id = $org_id");

					echo json_encode(['success' => true]);
					exit();
				}
			}
			else{
				if($type == 'dept' && (isset($id) && !empty($id))){
					$query ="SELECT count(distinct(user_details.user_id)) as totalpeople
						FROM departments
						LEFT JOIN user_details on user_details.department_id = departments.id
						LEFT JOIN user_details us on us.user_id = departments.modified_by
						WHERE departments.id = $id
						group by departments.id";
					$details = $this->Department->query($query);
					if(isset($details) && !empty($details)) {
						$totalpeople = $details[0][0]['totalpeople'];
						$viewData['totalpeople'] = $totalpeople;
						if (isset($totalpeople) && !empty($totalpeople)) {
							$all_dept = $this->Department->find('list', ['conditions' => ['Department.id !=' => $id], 'order' => ['Department.name ASC']]);
							$viewData['all_dept'] = $all_dept;
						}
					}
					$dept_count = $this->Department->query("SELECT count(*) as total_dept FROM departments");
					if(isset($dept_count) && !empty($dept_count)) {
						$viewData['total_dept'] = $dept_count[0][0]['total_dept'];
					}
				}
				else if($type == 'loc' && (isset($id) && !empty($id))){
					$viewData['total_data'] = 0;

					$used_org_total = $used_user_total = 0;

					$used_org = $this->Location->query("SELECT count(*) as used_org FROM organization_locations WHERE location_id = $id GROUP BY organization_locations.location_id");
					$used_org_total = $used_org = (isset($used_org[0][0]['used_org']) && !empty($used_org[0][0]['used_org'])) ? $used_org[0][0]['used_org'] : 0;
					$this->set('used_org', $used_org);

					$used_user = $this->Location->query("SELECT count(ud.user_id) as used_user FROM user_details ud LEFT JOIN organization_locations ol ON ol.id = ud.location_id WHERE ol.location_id = $id GROUP BY ol.location_id");
					$used_user_total = $used_user = (isset($used_user[0][0]['used_user']) && !empty($used_user[0][0]['used_user'])) ? $used_user[0][0]['used_user'] : 0;
					$this->set('used_user', $used_user);

					$viewData['total_data'] = $used_user_total + $used_org_total;

				}
				else if($type == 'org' && (isset($id) && !empty($id))){
					$used_users = $this->Location->query("SELECT count(*) as used_users FROM user_details WHERE organization_id = $id");
					$used_users = (isset($used_users[0][0]['used_users']) && !empty($used_users[0][0]['used_users'])) ? $used_users[0][0]['used_users'] : 0;
					$this->set('used_users', $used_users);

				}
			}

			$viewData['id'] = $id;
			$this->set($viewData);
			$this->render('partials/'.$type.'/trash');

		}
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
					if($type == 'org'){
						$this->loadModel('Organization');
						$query ="SELECT
								    organizations.*,
								    organization_types.type as org_type,
								    skill_counts.total_skills AS totalskills,
								    subject_counts.total_subjects AS totalsubjects,
								    domain_counts.total_domains AS totaldomains,
								    link_counts.total_links AS linktotal,
								    file_counts.total_files AS filetotal,
								    details_counts.totalpeoples AS totalpeople,
								    location_counts.total_locations AS total_location,
								    edomain_counts.total_edomains AS total_edomain,
								    story_counts.total_stories AS total_story,
								    organizations.modified AS updated_on,
								    CONCAT_WS(' ', user_details.first_name , user_details.last_name) AS updated_by

								FROM `organizations`

								INNER JOIN organization_types on organization_types.id = organizations.type_id
								INNER JOIN user_details on organizations.modified_by = user_details.user_id
								#total people
								LEFT JOIN
									(
										SELECT
											usd.organization_id,
											COUNT(DISTINCT(usd.user_id)) AS totalpeoples
										FROM user_details usd
					                    LEFT JOIN users uss on uss.id = usd.user_id
					                    WHERE uss.role_id = 2 AND uss.status = 1 AND uss.is_activated = 1
										GROUP BY usd.organization_id
									 ) AS details_counts
									ON details_counts.organization_id = organizations.id

								#total locations
								LEFT JOIN
									(
								    SELECT
								        olc.organization_id,
								        COUNT(DISTINCT(olc.location_id)) AS total_locations
								        FROM organization_locations olc
								    	GROUP BY olc.organization_id
								     ) AS location_counts
									ON location_counts.organization_id = organizations.id
								#total email domains
								LEFT JOIN
									(
								    SELECT
								        oed.organization_id,
								        COUNT(DISTINCT(oed.email_domain_id)) AS total_edomains
								        FROM organization_email_domains oed
								    	GROUP BY oed.organization_id
								     ) AS edomain_counts
									ON edomain_counts.organization_id = organizations.id
								#total skills
								LEFT JOIN
									(
								    SELECT
								        os.organization_id,
								        COUNT(DISTINCT(os.skill_id)) AS total_skills
								        FROM organization_skills os
								    	GROUP BY os.organization_id
								     ) AS skill_counts
									ON skill_counts.organization_id = organizations.id
								#total subjects
								LEFT JOIN
									(
								    SELECT
								        osb.organization_id,
								        COUNT(DISTINCT(osb.subject_id)) AS total_subjects
								        FROM organization_subjects osb
								    	GROUP BY osb.organization_id
								     ) AS subject_counts
									ON subject_counts.organization_id = organizations.id
								#total domains
								LEFT JOIN
									(
								    SELECT
								        od.organization_id,
								        COUNT(DISTINCT(od.domain_id)) AS total_domains
								        FROM organization_domains od
								    	GROUP BY od.organization_id
								     ) AS domain_counts
									ON domain_counts.organization_id = organizations.id
								#link total
								LEFT JOIN
									(
								    SELECT
								        ol.organization_id,
								        COUNT(DISTINCT(ol.id)) AS total_links
								        FROM organization_links ol
								    	GROUP BY ol.organization_id
								     ) AS link_counts
									ON link_counts.organization_id = organizations.id
								#file total
								LEFT JOIN
									(
								    SELECT
								        of.organization_id,
								        COUNT(DISTINCT(of.id)) AS total_files
								        FROM organization_files of
								        WHERE of.filename != ''
								    	GROUP BY of.organization_id
								    ) AS file_counts
									ON file_counts.organization_id = organizations.id

								#stories total
								LEFT JOIN
									(
								    SELECT
								        of.organization_id,
								        COUNT(DISTINCT(of.id)) AS total_stories
								        FROM story_organizations of
								    	GROUP BY of.organization_id
								    ) AS story_counts
									ON story_counts.organization_id = organizations.id


								WHERE organizations.id = $id

								GROUP BY organizations.id ";

						$data = $this->Organization->query($query);
					}
					else if($type == 'dept'){
						$query ="SELECT
								    departments.id,
								    departments.name,
								    departments.image,
								    departments.modified,
								    departments.modified_by,
								    skill_counts.total_skills AS totalskills,
								    subject_counts.total_subjects AS totalsubjects,
								    domain_counts.total_domains AS totaldomains,
								    details_counts.totalpeoples AS totalpeople,
								    story_counts.total_stories AS total_story,
								    departments.modified AS updated_on,
								    CONCAT_WS(' ', user_details.first_name , user_details.last_name) AS updated_by

								FROM `departments`

								left JOIN user_details on departments.modified_by = user_details.user_id

								LEFT JOIN
									(
										SELECT
											usd.department_id,
											COUNT(DISTINCT(usd.user_id)) AS totalpeoples
										FROM user_details usd
					                    left join users uss on uss.id = usd.user_id
					                    WHERE uss.role_id =2 AND uss.status = 1 AND uss.is_activated = 1
										GROUP BY usd.department_id
									 ) AS details_counts
									ON details_counts.department_id = departments.id

								#total skills
								LEFT JOIN
									(
								    SELECT
								        ls.department_id,
								        COUNT(DISTINCT(ls.skill_id)) AS total_skills
								        FROM department_skills ls
								    	GROUP BY ls.department_id
								     ) AS skill_counts
									ON skill_counts.department_id = departments.id
								#total subjects
								LEFT JOIN
									(
								    SELECT
								        ls.department_id,
								        COUNT(DISTINCT(ls.subject_id)) AS total_subjects
								        FROM department_subjects ls
								    	GROUP BY ls.department_id
								     ) AS subject_counts
									ON subject_counts.department_id = departments.id
								#total domains
								LEFT JOIN
									(
								    SELECT
								        ld.department_id,
								        COUNT(DISTINCT(ld.domain_id)) AS total_domains
								        FROM department_domains ld
								    	GROUP BY ld.department_id
								     ) AS domain_counts
									ON domain_counts.department_id = departments.id

								#stories total
								LEFT JOIN
									(
								    SELECT
								        of.department_id,
								        COUNT(DISTINCT(of.id)) AS total_stories
								        FROM story_departments of
								    	GROUP BY of.department_id
								    ) AS story_counts
									ON story_counts.department_id = departments.id


								WHERE departments.id = $id

								GROUP BY departments.id ";

						$data = $this->Department->query($query);
					}
					else if($type == 'loc'){
						$data = $this->get_row_detail($id);
					}
					$view = new View($this, false);
					$view->viewPath = 'Communities/partials/' . $type;
					$view->set('list_data', $data);
					$response['content'] = $view->render('get_row');
				}

			}
			echo json_encode($response);
			exit();
		}
	}

	protected function get_row_detail($id = null){
		if(!isset($id) || empty($id)) {
			return null;
		}
		$query ="SELECT
			    locations.id,
			    locations.name,
			    locations.image,
			    locations.city,
			    locations.address,
			    locations.information,
			    locations.modified,
			    locations.modified_by,
			    locations.zip,
			    countries.countryName as countryName,
			    states.name as stateName,
			    location_types.type as type,
			    org_counts.used_org AS totalorg,
			    people_counts.used_user AS totalpeople,
			    skill_counts.total_skills AS totalskills,
			    subject_counts.total_subjects AS totalsubjects,
			    domain_counts.total_domains AS totaldomains,
			    link_counts.total_links AS linktotal,
			    file_counts.total_files AS filetotal,
			    story_counts.total_stories AS total_story,
			    locations.modified AS updated_on,
			    CONCAT_WS(' ', user_details.first_name , user_details.last_name) AS updated_by

			FROM `locations`

			INNER JOIN location_types on location_types.id = locations.type_id
			inner join user_details on locations.modified_by = user_details.user_id
			inner join countries on countries.id = locations.country_id
			inner join states on states.id = locations.state_id
			#total org
			LEFT JOIN
				(
			    	SELECT ol.location_id, count(DISTINCT(ol.organization_id)) as used_org
			    	FROM organization_locations ol
			    	GROUP BY ol.location_id
			     ) AS org_counts
				ON org_counts.location_id = locations.id

			#total people
			LEFT JOIN
				(
			    	SELECT ol.id, COUNT(DISTINCT(ud.user_id)) as used_user
			    	FROM user_details ud
			    	LEFT JOIN users usr ON usr.id = ud.user_id
			    	INNER JOIN locations ol ON ol.id = ud.location_id
			    	WHERE usr.role_id = 2 AND usr.status = 1 AND usr.is_activated = 1
			    	GROUP BY ol.id
			     ) AS people_counts
				ON people_counts.id = locations.id

			#total skills
			LEFT JOIN
				(
			    SELECT
			        ls.location_id,
			        COUNT(DISTINCT(ls.skill_id)) AS total_skills
			        FROM location_skills ls
			    	GROUP BY ls.location_id
			     ) AS skill_counts
				ON skill_counts.location_id = locations.id
			#total subjects
			LEFT JOIN
				(
			    SELECT
			        ls.location_id,
			        COUNT(DISTINCT(ls.subject_id)) AS total_subjects
			        FROM location_subjects ls
			    	GROUP BY ls.location_id
			     ) AS subject_counts
				ON subject_counts.location_id = locations.id
			#total domains
			LEFT JOIN
				(
			    SELECT
			        ld.location_id,
			        COUNT(DISTINCT(ld.domain_id)) AS total_domains
			        FROM location_domains ld
			    	GROUP BY ld.location_id
			     ) AS domain_counts
				ON domain_counts.location_id = locations.id
			#link total
			LEFT JOIN
				(
			    SELECT
			        ll.location_id,
			        COUNT(DISTINCT(ll.id)) AS total_links
			        FROM location_links ll
			    	GROUP BY ll.location_id
			     ) AS link_counts
				ON link_counts.location_id = locations.id
			#file total
			LEFT JOIN
				(
			    SELECT
			        lf.location_id,
			        COUNT(DISTINCT(lf.id)) AS total_files
			        FROM location_files lf
			        WHERE lf.filename != ''
			    	GROUP BY lf.location_id
			    ) AS file_counts
				ON file_counts.location_id = locations.id

			#stories total
			LEFT JOIN
				(
			    SELECT
			        of.location_id,
			        COUNT(DISTINCT(of.id)) AS total_stories
			        FROM story_locations of
			    	GROUP BY of.location_id
			    ) AS story_counts
				ON story_counts.location_id = locations.id


			WHERE locations.id = $id

			group by locations.id ";
		$this->loadModel('Location');
		$dataAll = $this->Location->query($query);
		return (isset($dataAll) && !empty($dataAll)) ? $dataAll : [];
	}

	public function save_data() {

		$response = ['success' => false ];
        if ($this->request->isAjax()) {
            $this->layout = 'ajax';

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				// pr($post, 1);
            	$this->loadModel('DepartmentSkill');
            	$this->loadModel('DepartmentSubject');
            	$this->loadModel('DepartmentDomain');
            	if(isset($post['main']['name']) && !empty($post['main']['name'])){
            		$details = $post['main'];
					$data = [
						'name' => $details['name'],
						'modified_by' => $this->user_id
					];
					if(isset($details['id']) && !empty($details['id'])){
						$data['id'] = $details['id'];
					}

            		$kd_file = WWW_ROOT . 'uploads/dept_temp_files/' . $details['filename'];
	            	$folder_url = WWW_ROOT . COMM_IMAGE_PATH . $post['main']['filename'];
	            	if(isset($details['filename']) && !empty($details['filename'])){

	            		if(isset($details['id']) && !empty($details['id'])) {
	            			$imgData = $this->Department->find('first', ['conditions'=>['Department.id'=>$details['id']]]);
							if( isset($imgData['Department']['image']) && !empty($imgData['Department']['image']) ){
								$old_image = $imgData['Department']['image'];
								unlink(WWW_ROOT . COMM_IMAGE_PATH . $old_image);
							}
						}

		            	$unique_file_name = $this->unique_file_name(WWW_ROOT . COMM_IMAGE_PATH, $post['main']['filename']);
		            	$folder_url = WWW_ROOT . COMM_IMAGE_PATH . $unique_file_name;
		            	if(rename($kd_file, $folder_url)){
		            		$data['image'] = $unique_file_name;
		            	}
		            }
					if($this->Department->save($data)){
						$response['success'] = true;
						if(isset($details['id']) && !empty($details['id'])){
	            			$kd_id = $details['id'];
	            		}
	            		else{
	            			$kd_id = $this->Department->getLastInsertId();
	            		}

            			if(isset($post['competency']) && !empty($post['competency'])){
            				$competency = $post['competency'];

            				if( (isset($details['id']) && !empty($details['id'])) ){
            					$this->DepartmentSkill->query("delete from department_skills where department_id = " . $details['id']);
            				}

            				if(isset($competency['skills']) && !empty($competency['skills'])){
            					$allSkills = $competency['skills'];
            					$qry = "INSERT INTO `department_skills` (`department_id`, `skill_id`) VALUES ";
            					$qry_arr = [];
	            				foreach ($allSkills as $key => $value) {
	            					$qry_arr[] = "('$kd_id', '$value')";
	            				}
	            				$qry .= implode(' ,', $qry_arr);
	            				$this->DepartmentSkill->query($qry);
            				}

            				if( (isset($details['id']) && !empty($details['id'])) ){
            					$this->DepartmentSubject->query("delete from department_subjects where department_id = " . $details['id']);
            				}

            				if(isset($competency['subjects']) && !empty($competency['subjects'])){
            					$allSubjects = $competency['subjects'];
            					$qry = "INSERT INTO `department_subjects` (`department_id`, `subject_id`) VALUES ";
            					$qry_arr = [];
	            				foreach ($allSubjects as $key => $value) {
	            					$qry_arr[] = "('$kd_id', '$value')";
	            				}
	            				$qry .= implode(' ,', $qry_arr);
	            				$this->DepartmentSubject->query($qry);
            				}

            				if( (isset($details['id']) && !empty($details['id'])) ){
            					$this->DepartmentDomain->query("delete from department_domains where department_id = " . $details['id']);
            				}

            				if(isset($competency['domains']) && !empty($competency['domains'])){
            					$allDomains = $competency['domains'];
            					$qry = "INSERT INTO `department_domains` (`department_id`, `domain_id`) VALUES ";
            					$qry_arr = [];
	            				foreach ($allDomains as $key => $value) {
	            					$qry_arr[] = "('$kd_id', '$value')";
	            				}
	            				$qry .= implode(' ,', $qry_arr);
	            				$this->DepartmentDomain->query($qry);
            				}
            			}
					}
				}
			}
			echo json_encode($response);
			exit;
		}
	}

	public function save_location_data(){

		if ($this->request->isAjax()) {

            $this->layout = 'ajax';
            $response = [
				'success' => false,
				'content' => null,
			];

            if ($this->request->is('post') || $this->request->is('put')) {
            	$post = $this->request->data;
            	// pr($post, 1);
            	$this->loadModel('Location');
            	$this->loadModel('LocationLink');
            	$this->loadModel('LocationFile');
            	$this->loadModel('LocationSkill');
            	$this->loadModel('LocationSubject');
            	$this->loadModel('LocationDomain');

            	$old_image = '';
            	if(isset($post['main']['name']) && !empty($post['main']['name'])){
            		$details = $post['main'];
            		$kd_data = [
						'name' => $details['name'],
						'type_id' => $details['type'],
            			'information' =>  $details['info'],
						'country_id' => $details['country'],
            			'address' =>  $details['address'],
            			'city' =>  $details['city'],
						'state_id' => $details['state'],
						'zip' => $details['zip'],
            			'modified_by' => $this->user_id
            		];
            		if(isset($details['id']) && !empty($details['id'])){
            			$kd_data['id'] = $details['id'];

						$imgData = $this->Location->find('first', ['conditions'=>['Location.id'=>$details['id']]]);
						if( isset($imgData['Location']['image']) && !empty($imgData['Location']['image']) ){
							$old_image = $imgData['Location']['image'];
						}

            		}
            		else{
            			$kd_data['created_by'] = $this->user_id;
            		}
            		$kd_file = WWW_ROOT . 'uploads/dept_temp_files/' . $details['filename'];
	            	$folder_url = WWW_ROOT . LOC_IMAGE_PATH . $post['main']['filename'];
	            	if(isset($details['filename']) && !empty($details['filename'])){
	            		if( !empty($old_image) ){
							unlink(WWW_ROOT . LOC_IMAGE_PATH.$old_image);
						}

		            	$unique_file_name = $this->unique_file_name(WWW_ROOT . LOC_IMAGE_PATH, $post['main']['filename']);
		            	$folder_url = WWW_ROOT . LOC_IMAGE_PATH . $unique_file_name;
		            	if(rename($kd_file, $folder_url)){
		            		$kd_data['image'] = $unique_file_name;
		            	}
		            }
            		if($this->Location->save($kd_data)) {
            			if(isset($details['id']) && !empty($details['id'])){
	            			$kd_id = $details['id'];
	            		}
	            		else{
	            			$kd_id = $this->Location->getLastInsertId();
	            		}

            			$response['success'] = true;
            			if(isset($post['link']) && !empty($post['link'])){
            				$link = $post['link'];
            				foreach ($link as $key => $value) {
								$link_data = [
									'user_id' => $this->user_id,
									'location_id' => $kd_id,
									'title' => $value['title'],
									'link' => $value['url']
								];
								$this->LocationLink->id = null;
								if($this->LocationLink->save($link_data)){

								}
            				}
            			}
            			if(isset($post['file']) && !empty($post['file'])){
            				$file = $post['file'];
            				foreach ($file as $key => $value) {

			            		$df_file = WWW_ROOT . 'uploads/dept_temp_files/' . $value['filename'];
				            	$df_folder_url = WWW_ROOT . LOC_FILE_PATH . $value['filename'];
				            	if(isset($value['filename']) && !empty($value['filename'])){
					            	$unique_file_name = $this->unique_file_name(WWW_ROOT . LOC_FILE_PATH, $value['filename']);
		            				$df_folder_url = WWW_ROOT . LOC_FILE_PATH . $unique_file_name;
					            	if(rename($df_file, $df_folder_url)){ }
					            }
								$file_data = [
									'user_id' => $this->user_id,
									'location_id' => $kd_id,
									'title' => $value['title'],
									'filename' => $value['filename']
								];
								$this->LocationFile->id = null;
								if($this->LocationFile->save($file_data)){

								}
            				}
            			}
            			if(isset($post['competency']) && !empty($post['competency'])){
            				$competency = $post['competency'];

            				if( (isset($details['id']) && !empty($details['id'])) ){
            					$this->LocationSkill->query("delete from location_skills where location_id = " . $details['id']);
            				}

            				if(isset($competency['skills']) && !empty($competency['skills'])){
            					$allSkills = $competency['skills'];
            					$qry = "INSERT INTO `location_skills` (`location_id`, `skill_id`) VALUES ";
            					$qry_arr = [];
	            				foreach ($allSkills as $key => $value) {
	            					$qry_arr[] = "('$kd_id', '$value')";
	            				}
	            				$qry .= implode(' ,', $qry_arr);
	            				$this->LocationSkill->query($qry);
            				}

            				if( (isset($details['id']) && !empty($details['id'])) ){
            					$this->LocationSubject->query("delete from location_subjects where location_id = " . $details['id']);
            				}

            				if(isset($competency['subjects']) && !empty($competency['subjects'])){
            					$allSubjects = $competency['subjects'];
            					$qry = "INSERT INTO `location_subjects` (`location_id`, `subject_id`) VALUES ";
            					$qry_arr = [];
	            				foreach ($allSubjects as $key => $value) {
	            					$qry_arr[] = "('$kd_id', '$value')";
	            				}
	            				$qry .= implode(' ,', $qry_arr);
	            				$this->LocationSubject->query($qry);
            				}

            				if( (isset($details['id']) && !empty($details['id'])) ){
            					$this->LocationDomain->query("delete from location_domains where location_id = " . $details['id']);
            				}

            				if(isset($competency['domains']) && !empty($competency['domains'])){
            					$allDomains = $competency['domains'];
            					$qry = "INSERT INTO `location_domains` (`location_id`, `domain_id`) VALUES ";
            					$qry_arr = [];
	            				foreach ($allDomains as $key => $value) {
	            					$qry_arr[] = "('$kd_id', '$value')";
	            				}
	            				$qry .= implode(' ,', $qry_arr);
	            				$this->LocationDomain->query($qry);
            				}
            			}
            		}
            	}
            }
            echo json_encode($response);
			exit();

        }
    }

	public function save_org_data(){

		if ($this->request->isAjax()) {

            $this->layout = 'ajax';
            $response = [
				'success' => false,
				'content' => null,
			];

            if ($this->request->is('post') || $this->request->is('put')) {
            	$post = $this->request->data;
            	// pr($post, 1);
            	$this->loadModel('Organization');
            	$this->loadModel('OrganizationLink');
            	$this->loadModel('OrganizationFile');
            	$this->loadModel('OrganizationSkill');
            	$this->loadModel('OrganizationSubject');
            	$this->loadModel('OrganizationDomain');
            	$this->loadModel('OrganizationLocation');
            	$this->loadModel('OrganizationEmailDomain');

            	$old_image = '';
            	if(isset($post['main']['name']) && !empty($post['main']['name'])){
            		$details = $post['main'];
            		$main_data = [
						'name' => $details['name'],
						'type_id' => $details['type'],
            			'information' =>  $details['info'],
            			'modified_by' => $this->user_id
            		];
            		if(isset($details['id']) && !empty($details['id'])){
            			$main_data['id'] = $details['id'];

						$imgData = $this->Organization->find('first', ['conditions'=>['Organization.id'=>$details['id']]]);
						if( isset($imgData['Organization']['image']) && !empty($imgData['Organization']['image']) ){
							$old_image = $imgData['Organization']['image'];
						}

            		}
            		else{
            			$main_data['created_by'] = $this->user_id;
            		}
            		$kd_file = WWW_ROOT . 'uploads/dept_temp_files/' . $details['filename'];
	            	$folder_url = WWW_ROOT . ORG_IMAGE_PATH . $post['main']['filename'];
	            	if(isset($details['filename']) && !empty($details['filename'])){
	            		if( !empty($old_image) ){
							unlink(WWW_ROOT . ORG_IMAGE_PATH.$old_image);
						}

		            	$unique_file_name = $this->unique_file_name(WWW_ROOT . ORG_IMAGE_PATH, $post['main']['filename']);
		            	$folder_url = WWW_ROOT . ORG_IMAGE_PATH . $unique_file_name;
		            	if(rename($kd_file, $folder_url)){
		            		$main_data['image'] = $unique_file_name;
		            	}
		            }
		            // pr($main_data, 1);
            		if($this->Organization->save($main_data)) {
            			if(isset($details['id']) && !empty($details['id'])){
	            			$kd_id = $details['id'];
	            		}
	            		else{
	            			$kd_id = $this->Organization->getLastInsertId();
	            		}

            			$response['success'] = true;
            			if(isset($post['link']) && !empty($post['link'])){
            				$link = $post['link'];
            				foreach ($link as $key => $value) {
								$link_data = [
									'user_id' => $this->user_id,
									'organization_id' => $kd_id,
									'title' => $value['title'],
									'link' => $value['url']
								];
								$this->OrganizationLink->id = null;
								if($this->OrganizationLink->save($link_data)){

								}
            				}
            			}
            			if(isset($post['file']) && !empty($post['file'])){
            				$file = $post['file'];
            				foreach ($file as $key => $value) {

			            		$df_file = WWW_ROOT . 'uploads/dept_temp_files/' . $value['filename'];
				            	$df_folder_url = WWW_ROOT . ORG_FILE_PATH . $value['filename'];
				            	if(isset($value['filename']) && !empty($value['filename'])){
					            	$unique_file_name = $this->unique_file_name(WWW_ROOT . ORG_FILE_PATH, $value['filename']);
		            				$df_folder_url = WWW_ROOT . ORG_FILE_PATH . $unique_file_name;
					            	if(rename($df_file, $df_folder_url)){ }
					            }
								$file_data = [
									'user_id' => $this->user_id,
									'organization_id' => $kd_id,
									'title' => $value['title'],
									'filename' => $value['filename']
								];
								$this->OrganizationFile->id = null;
								if($this->OrganizationFile->save($file_data)){

								}
            				}
            			}
            			# ADD ORGANIZATION LOCATIONS
            			if(isset($details['locations']) && !empty($details['locations'])){
            				$insertValues = $details['locations'];
            				if( (isset($details['id']) && !empty($details['id'])) ){
            					// get all locations associated with this organization
            					$db_loc = $this->Organization->query("SELECT location_id from organization_locations where organization_id = " . $details['id']);
								if(isset($db_loc) && !empty($db_loc)) {
									$db_loc = Set::extract($db_loc, '/organization_locations/location_id');
									if(isset($details['locations']) && !empty($details['locations'])){
			        					$post_loc = $details['locations'];

			        					$deletedValues = array_diff($db_loc, $post_loc);
			        					$insertValues = array_diff($post_loc,$db_loc);

										if(isset($deletedValues) && !empty($deletedValues)) {
											$deletedValues = implode(',', $deletedValues);
											$this->OrganizationLocation->query("update user_details set location_id = NULL where organization_id = $kd_id AND location_id IN ($deletedValues)");
											$this->OrganizationLocation->query("delete from organization_locations where organization_id = $kd_id AND location_id IN ($deletedValues)");
										}
			        				}
								}
            				}

            				if(isset($insertValues) && !empty($insertValues)){
            					$allData = $insertValues;
            					$qry = "INSERT INTO `organization_locations` (`organization_id`, `location_id`) VALUES ";
            					$qry_arr = [];
	            				foreach ($allData as $key => $value) {
	            					$qry_arr[] = "('$kd_id', '$value')";
	            				}
	            				$qry .= implode(' ,', $qry_arr);
	            				$this->OrganizationLocation->query($qry);
            				}
            			}
            			else{
            				$db_loc = $this->Organization->query("SELECT location_id from organization_locations where organization_id = $kd_id");
            				if(isset($db_loc) && !empty($db_loc)) {
            					$this->OrganizationLocation->query("update user_details set location_id = NULL where organization_id = $kd_id AND location_id IN (select location_id from organization_locations ol where ol.organization_id = $kd_id )");
            					$this->OrganizationLocation->query("delete from organization_locations where organization_id = $kd_id");
            				}
            			}
            			# ADD ORGANIZATION EMAIL DOMAINS
            			if(isset($details['edomains']) && !empty($details['edomains'])){

            				if( (isset($details['id']) && !empty($details['id'])) ){
            					$this->OrganizationEmailDomain->query("delete from organization_email_domains where organization_id = " . $details['id']);
            				}

            				if(isset($details['edomains']) && !empty($details['edomains'])){
            					$allData = $details['edomains'];
            					$qry = "INSERT INTO `organization_email_domains` (`organization_id`, `email_domain_id`) VALUES ";
            					$qry_arr = [];
	            				foreach ($allData as $key => $value) {
	            					$qry_arr[] = "('$kd_id', '$value')";
	            				}
	            				$qry .= implode(' ,', $qry_arr);
	            				$this->OrganizationEmailDomain->query($qry);
            				}
            			}
            			# ADD ORGANIZATION COMPETENCY
            			if(isset($post['competency']) && !empty($post['competency'])){
            				$competency = $post['competency'];

            				if( (isset($details['id']) && !empty($details['id'])) ){
            					$this->OrganizationSkill->query("delete from organization_skills where organization_id = " . $details['id']);
            				}

            				if(isset($competency['skills']) && !empty($competency['skills'])){
            					$allSkills = $competency['skills'];
            					$qry = "INSERT INTO `organization_skills` (`organization_id`, `skill_id`) VALUES ";
            					$qry_arr = [];
	            				foreach ($allSkills as $key => $value) {
	            					$qry_arr[] = "('$kd_id', '$value')";
	            				}
	            				$qry .= implode(' ,', $qry_arr);
	            				$this->OrganizationSkill->query($qry);
            				}

            				if( (isset($details['id']) && !empty($details['id'])) ){
            					$this->OrganizationSubject->query("delete from organization_subjects where organization_id = " . $details['id']);
            				}

            				if(isset($competency['subjects']) && !empty($competency['subjects'])){
            					$allSubjects = $competency['subjects'];
            					$qry = "INSERT INTO `organization_subjects` (`organization_id`, `subject_id`) VALUES ";
            					$qry_arr = [];
	            				foreach ($allSubjects as $key => $value) {
	            					$qry_arr[] = "('$kd_id', '$value')";
	            				}
	            				$qry .= implode(' ,', $qry_arr);
	            				$this->OrganizationSubject->query($qry);
            				}

            				if( (isset($details['id']) && !empty($details['id'])) ){
            					$this->OrganizationDomain->query("delete from organization_domains where organization_id = " . $details['id']);
            				}

            				if(isset($competency['domains']) && !empty($competency['domains'])){
            					$allDomains = $competency['domains'];
            					$qry = "INSERT INTO `organization_domains` (`organization_id`, `domain_id`) VALUES ";
            					$qry_arr = [];
	            				foreach ($allDomains as $key => $value) {
	            					$qry_arr[] = "('$kd_id', '$value')";
	            				}
	            				$qry .= implode(' ,', $qry_arr);
	            				$this->OrganizationDomain->query($qry);
            				}
            			}
            		}
            	}
            }
            echo json_encode($response);
			exit();

        }
    }

	public function confirm_org_update( $id = null, $total_users = 0) {

		if ($this->request->isAjax()) {
			$this->layout = false;
			$viewData = [];
			$this->loadModel('UserDetail');
			$used_users = $this->UserDetail->query("SELECT count(*) as used_users FROM user_details WHERE organization_id = $id");
			$used_users = (isset($used_users[0][0]['used_users']) && !empty($used_users[0][0]['used_users'])) ? $used_users[0][0]['used_users'] : 0;
			$this->set('used_users', $used_users);
			$this->set('total_users', $total_users);

			$this->set($viewData);
			$this->render('partials/org/confirm_org_update');

		}
	}

	public function bulk_update($type = null) {

		if ($this->request->isAjax()) {
            $this->layout = 'ajax';
            $all_data = [];

			if( isset($type) && !empty($type) ){
				if($type == 'dept'){
					$all_data = $this->Department->find('list', array('order'=> array('Department.name ASC') ) );
				}
				$this->set('all_data', $all_data);
				$html = $this->render('partials/'.$type.'/bulk_update');
			}

			return $html;
			exit();
		}
	}

	public function bulk_delete(){

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

				if( (isset($post['id']) && !empty($post['id'])) && isset($post['type']) && !empty($post['type']) ){
					$id = $post['id'];
					$type = $post['type'];
					if($type == 'dept'){
						$data = ['id' => $id];
						if($this->Department->deleteAll($data, false)){
							$response['success'] = true;
						}
					}
				}
				echo json_encode($response);
				exit();
			}

		}
	}

	public function filter_data(){
		if ($this->request->isAjax()) {
            $this->layout = false;

			$view = new View($this, false);


            if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				// pr($post, 1);
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

				if($type == 'org'){
					$data['list_data'] = $this->find_organization($filter, '', $sorting);
					$view->viewPath = 'Communities/partials/org';
					$view->set($data);
					$html = $view->render('get_list');
				}
				else if($type == 'dept'){
					$data['list_data'] = $this->find_departments($filter, '', $sorting);
					$view->viewPath = 'Communities/partials/dept';
					$view->set($data);
					$html = $view->render('get_list');
				}
				else if($type == 'loc'){
					$data['list_data'] = $this->find_locations($filter, '', $sorting);
					$view->viewPath = 'Communities/partials/loc';
					$view->set($data);
					$html = $view->render('get_list');
				}
				else if($type == 'comm'){
					$data['list_data'] = $this->find_community($filter, '', $sorting);
					$view->viewPath = 'Communities/partials/comm';
					$view->set($data);
					$html = $view->render('get_list');
				}

			}

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
				// pr($post, 1);
				$type = $post['type'];

				$filter = $post['q'];
				$coloumn = $post['coloumn'];
				$order = $post['order'];


				$ser = '^';
				$filter_query = ' 1';
				if(isset($filter) && !empty($filter)){
					//$search_str = htmlentities($filter, ENT_QUOTES);
					//$filter_query = " name LIKE '%$search_str%'";

					//$search_str= Sanitize::escape(like(strtolower($filter), $ser ));

					$search_str= Sanitize::escape(like($filter, $ser ));

					$filter_query = " (name LIKE '%$search_str%' ESCAPE '$ser') ";

				}

				$order_qry = 'ORDER BY name ASC';
				if( isset($coloumn) && !empty($coloumn) ){

					if( isset($coloumn) && !empty($coloumn) && isset($order) && !empty($order) ){
						$order_qry = "ORDER BY ".$coloumn." ".$order;
					}

				}
				if($type == 'dept'){
					$this->loadModel('Department');
					$query = "SELECT
								    departments.id,
								    departments.name,
								    departments.image,
								    departments.modified,
								    skill_counts.total_skills AS totalskills,
								    subject_counts.total_subjects AS totalsubjects,
								    domain_counts.total_domains AS totaldomains,
								    details_counts.totalpeoples AS totalpeople,
								    departments.modified AS updated_on,
								    CONCAT_WS(' ', user_details.first_name , user_details.last_name) AS updated_by

								FROM `departments`

								left JOIN user_details on departments.modified_by = user_details.user_id

								LEFT JOIN
									(
										SELECT
											usd.department_id,
											COUNT(DISTINCT(usd.user_id)) AS totalpeoples
										FROM user_details usd
					                    left JOIN users uss on uss.id = usd.user_id
					                    WHERE uss.role_id =2 AND uss.status = 1 AND uss.is_activated = 1
										GROUP BY usd.department_id
									 ) AS details_counts
									ON details_counts.department_id = departments.id

								#total skills
								LEFT JOIN
									(
								    SELECT
								        ls.department_id,
								        COUNT(DISTINCT(ls.skill_id)) AS total_skills
								        FROM department_skills ls
								    	GROUP BY ls.department_id
								     ) AS skill_counts
									ON skill_counts.department_id = departments.id
								#total subjects
								LEFT JOIN
									(
								    SELECT
								        ls.department_id,
								        COUNT(DISTINCT(ls.subject_id)) AS total_subjects
								        FROM department_subjects ls
								    	GROUP BY ls.department_id
								     ) AS subject_counts
									ON subject_counts.department_id = departments.id
								#total domains
								LEFT JOIN
									(
								    SELECT
								        ld.department_id,
								        COUNT(DISTINCT(ld.domain_id)) AS total_domains
								        FROM department_domains ld
								    	GROUP BY ld.department_id
								     ) AS domain_counts
									ON domain_counts.department_id = departments.id
								WHERE $filter_query

								group by departments.id
								$order_qry
								";
					$dataAll = $this->Department->query($query);
					if (isset($dataAll) && !empty($dataAll)) {
						$data = $dataAll;
					}
				}
				else if($type == 'loc'){
					$this->loadModel('Location');
					$query ="SELECT
							    locations.id,
							    locations.`name`,
							    locations.image,
							    locations.city,
							    locations.modified,
							    countries.countryName as countryName,
							    location_types.type as type,org_counts.used_org AS totalorg,
				    			people_counts.used_user AS totalpeople,
							    skill_counts.total_skills AS totalskills,
							    subject_counts.total_subjects AS totalsubjects,
							    domain_counts.total_domains AS totaldomains,
							    link_counts.total_links AS linktotal,
							    file_counts.total_files AS filetotal,
							    locations.modified AS updated_on,
							    CONCAT_WS(' ', user_details.first_name , user_details.last_name) AS updated_by

							FROM `locations`

							INNER JOIN location_types on location_types.id = locations.type_id
							inner join user_details on locations.modified_by = user_details.user_id
							inner join countries on countries.id = locations.country_id

							#total org
							LEFT JOIN
								(
							    	SELECT ol.location_id, count(DISTINCT(ol.organization_id)) as used_org
							    	FROM organization_locations ol
							    	GROUP BY ol.location_id
							     ) AS org_counts
								ON org_counts.location_id = locations.id

							#total people
							LEFT JOIN
								(
							    	SELECT ol.location_id, COUNT(DISTINCT(ud.user_id)) as used_user
							    	FROM user_details ud
							    	LEFT JOIN organization_locations ol ON ol.id = ud.location_id
							    	GROUP BY ol.location_id
							     ) AS people_counts
								ON people_counts.location_id = locations.id

							#total skills
							LEFT JOIN
								(
							    SELECT
							        ls.location_id,
							        COUNT(DISTINCT(ls.skill_id)) AS total_skills
							        FROM location_skills ls
							    	GROUP BY ls.location_id
							     ) AS skill_counts
								ON skill_counts.location_id = locations.id
							#total subjects
							LEFT JOIN
								(
							    SELECT
							        ls.location_id,
							        COUNT(DISTINCT(ls.subject_id)) AS total_subjects
							        FROM location_subjects ls
							    	GROUP BY ls.location_id
							     ) AS subject_counts
								ON subject_counts.location_id = locations.id
							#total domains
							LEFT JOIN
								(
							    SELECT
							        ld.location_id,
							        COUNT(DISTINCT(ld.domain_id)) AS total_domains
							        FROM location_domains ld
							    	GROUP BY ld.location_id
							     ) AS domain_counts
								ON domain_counts.location_id = locations.id
							#link total
							LEFT JOIN
								(
							    SELECT
							        ll.location_id,
							        COUNT(DISTINCT(ll.id)) AS total_links
							        FROM location_links ll
							    	GROUP BY ll.location_id
							     ) AS link_counts
								ON link_counts.location_id = locations.id
							#file total
							LEFT JOIN
								(
							    SELECT
							        lf.location_id,
							        COUNT(DISTINCT(lf.id)) AS total_files
							        FROM location_files lf
							        WHERE lf.filename != ''
							    	GROUP BY lf.location_id
							    ) AS file_counts
								ON file_counts.location_id = locations.id

							WHERE $filter_query

							group by locations.id
							$order_qry";
					$dataAll = $this->Location->query($query);
					if (isset($dataAll) && !empty($dataAll)) {
						$data = $dataAll;
					}
				}
				else if($type == 'org'){
					$this->loadModel('Organization');
					$query ="SELECT
							    organizations.*,
							    organization_types.type as org_type,
							    skill_counts.total_skills AS totalskills,
							    subject_counts.total_subjects AS totalsubjects,
							    domain_counts.total_domains AS totaldomains,
							    link_counts.total_links AS linktotal,
							    file_counts.total_files AS filetotal,
							    details_counts.totalpeoples AS totalpeople,
							    location_counts.total_locations AS total_location,
							    edomain_counts.total_edomains AS total_edomain,
							    organizations.modified AS updated_on,
							    CONCAT_WS(' ', user_details.first_name , user_details.last_name) AS updated_by

							FROM `organizations`

							INNER JOIN organization_types on organization_types.id = organizations.type_id
							INNER JOIN user_details on organizations.modified_by = user_details.user_id
							#total people
							LEFT JOIN
								(
									SELECT
										usd.organization_id,
										COUNT(DISTINCT(usd.user_id)) AS totalpeoples
									FROM user_details usd
				                    left JOIN users uss on uss.id = usd.user_id
				                    WHERE uss.role_id = 2 AND uss.status = 1 AND uss.is_activated = 1
									GROUP BY usd.organization_id
								 ) AS details_counts
								ON details_counts.organization_id = organizations.id

							#total locations
							LEFT JOIN
								(
							    SELECT
							        olc.organization_id,
							        COUNT(DISTINCT(olc.location_id)) AS total_locations
							        FROM organization_locations olc
							    	GROUP BY olc.organization_id
							     ) AS location_counts
								ON location_counts.organization_id = organizations.id
							#total email domains
							LEFT JOIN
								(
							    SELECT
							        oed.organization_id,
							        COUNT(DISTINCT(oed.email_domain_id)) AS total_edomains
							        FROM organization_email_domains oed
							    	GROUP BY oed.organization_id
							     ) AS edomain_counts
								ON edomain_counts.organization_id = organizations.id
							#total skills
							LEFT JOIN
								(
							    SELECT
							        os.organization_id,
							        COUNT(DISTINCT(os.skill_id)) AS total_skills
							        FROM organization_skills os
							    	GROUP BY os.organization_id
							     ) AS skill_counts
								ON skill_counts.organization_id = organizations.id
							#total subjects
							LEFT JOIN
								(
							    SELECT
							        osb.organization_id,
							        COUNT(DISTINCT(osb.subject_id)) AS total_subjects
							        FROM organization_subjects osb
							    	GROUP BY osb.organization_id
							     ) AS subject_counts
								ON subject_counts.organization_id = organizations.id
							#total domains
							LEFT JOIN
								(
							    SELECT
							        od.organization_id,
							        COUNT(DISTINCT(od.domain_id)) AS total_domains
							        FROM organization_domains od
							    	GROUP BY od.organization_id
							     ) AS domain_counts
								ON domain_counts.organization_id = organizations.id
							#link total
							LEFT JOIN
								(
							    SELECT
							        ol.organization_id,
							        COUNT(DISTINCT(ol.id)) AS total_links
							        FROM organization_links ol
							    	GROUP BY ol.organization_id
							     ) AS link_counts
								ON link_counts.organization_id = organizations.id
							#file total
							LEFT JOIN
								(
							    SELECT
							        of.organization_id,
							        COUNT(DISTINCT(of.id)) AS total_files
							        FROM organization_files of
							        WHERE of.filename != ''
							    	GROUP BY of.organization_id
							    ) AS file_counts
								ON file_counts.organization_id = organizations.id

							WHERE $filter_query

							group by organizations.id
							$order_qry";
					$dataAll = $this->Organization->query($query);
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

	protected function find_organization($filter = null, $page = null, $sorting = array()) {
		$limit_query = ' LIMIT '.$this->offset;
		if(isset($page) && !empty($page)){
			$limit_query = " LIMIT $page, ".$this->offset;
		}

		$ser = '^';
		$filter_query = ' 1';
		if(isset($filter) && !empty($filter)){
			//$search_str = htmlentities($filter, ENT_QUOTES);
			//$filter_query = " organizations.name LIKE '%$search_str%'";

			//$search_str= Sanitize::escape(like(strtolower($filter), $ser ));

			$search_str= Sanitize::escape(like($filter, $ser ));

			$filter_query = " (organizations.name LIKE '%$search_str%' ESCAPE '$ser') ";

		}

		$order = 'ORDER BY organizations.name ASC';
		if( isset($sorting) && !empty($sorting) ){

			if( isset($sorting['coloumn']) && !empty($sorting['coloumn']) && isset($sorting['order']) && !empty($sorting['order']) ){
				$order = "ORDER BY ".$sorting['coloumn']." ".$sorting['order'];
			}

		}

		$data = [];
		$query ="SELECT
				    organizations.*,
				    organization_types.type as org_type,
				    skill_counts.total_skills AS totalskills,
				    subject_counts.total_subjects AS totalsubjects,
				    domain_counts.total_domains AS totaldomains,
				    link_counts.total_links AS linktotal,
				    file_counts.total_files AS filetotal,
				    details_counts.totalpeoples AS totalpeople,
				    location_counts.total_locations AS total_location,
				    edomain_counts.total_edomains AS total_edomain,
				    story_counts.total_stories AS total_story,
				    organizations.modified AS updated_on,
				    CONCAT_WS(' ', user_details.first_name , user_details.last_name) AS updated_by

				FROM `organizations`

				INNER JOIN organization_types on organization_types.id = organizations.type_id
				INNER JOIN user_details on organizations.modified_by = user_details.user_id
				#total people
				LEFT JOIN
					(
						SELECT
							usd.organization_id,
							COUNT(DISTINCT(usd.user_id)) AS totalpeoples
						FROM user_details usd
	                    LEFT JOIN users uss on uss.id = usd.user_id
	                    WHERE uss.role_id = 2 AND uss.status = 1 AND uss.is_activated = 1
						GROUP BY usd.organization_id
					 ) AS details_counts
					ON details_counts.organization_id = organizations.id

				#total locations
				LEFT JOIN
					(
				    SELECT
				        olc.organization_id,
				        COUNT(DISTINCT(olc.location_id)) AS total_locations
				        FROM organization_locations olc
				    	GROUP BY olc.organization_id
				     ) AS location_counts
					ON location_counts.organization_id = organizations.id
				#total email domains
				LEFT JOIN
					(
				    SELECT
				        oed.organization_id,
				        COUNT(DISTINCT(oed.email_domain_id)) AS total_edomains
				        FROM organization_email_domains oed
				    	GROUP BY oed.organization_id
				     ) AS edomain_counts
					ON edomain_counts.organization_id = organizations.id
				#total skills
				LEFT JOIN
					(
				    SELECT
				        os.organization_id,
				        COUNT(DISTINCT(os.skill_id)) AS total_skills
				        FROM organization_skills os
				    	GROUP BY os.organization_id
				     ) AS skill_counts
					ON skill_counts.organization_id = organizations.id
				#total subjects
				LEFT JOIN
					(
				    SELECT
				        osb.organization_id,
				        COUNT(DISTINCT(osb.subject_id)) AS total_subjects
				        FROM organization_subjects osb
				    	GROUP BY osb.organization_id
				     ) AS subject_counts
					ON subject_counts.organization_id = organizations.id
				#total domains
				LEFT JOIN
					(
				    SELECT
				        od.organization_id,
				        COUNT(DISTINCT(od.domain_id)) AS total_domains
				        FROM organization_domains od
				    	GROUP BY od.organization_id
				     ) AS domain_counts
					ON domain_counts.organization_id = organizations.id
				#link total
				LEFT JOIN
					(
				    SELECT
				        ol.organization_id,
				        COUNT(DISTINCT(ol.id)) AS total_links
				        FROM organization_links ol
				    	GROUP BY ol.organization_id
				     ) AS link_counts
					ON link_counts.organization_id = organizations.id
				#file total
				LEFT JOIN
					(
				    SELECT
				        of.organization_id,
				        COUNT(DISTINCT(of.id)) AS total_files
				        FROM organization_files of
				        WHERE of.filename != ''
				    	GROUP BY of.organization_id
				    ) AS file_counts
					ON file_counts.organization_id = organizations.id

				#stories total
				LEFT JOIN
					(
				    SELECT
				        of.organization_id,
				        COUNT(DISTINCT(of.id)) AS total_stories
				        FROM story_organizations of
				    	GROUP BY of.organization_id
				    ) AS story_counts
					ON story_counts.organization_id = organizations.id

				WHERE $filter_query

				GROUP BY organizations.id
				$order $limit_query ";

		$details = $this->Department->query($query);
		$data = (isset($details) && !empty($details)) ? $details : [];

		return $data;
	}

	protected function find_departments($filter = null, $page = null, $sorting = array()) {
		$limit_query = ' LIMIT '.$this->offset;
		if(isset($page) && !empty($page)){
			$limit_query = " LIMIT $page, ".$this->offset;
		}

		$ser = '^';
		$filter_query = ' 1';
		if(isset($filter) && !empty($filter)){
			//$search_str = htmlentities($filter, ENT_QUOTES);
			//$filter_query = " departments.name LIKE '%$search_str%'";

			//$search_str= Sanitize::escape(like(strtolower($filter), $ser ));

			$search_str= Sanitize::escape(like($filter, $ser ));

			$filter_query = " (departments.name LIKE '%$search_str%' ESCAPE '$ser') ";

		}

		$order = 'ORDER BY departments.name ASC';
		if( isset($sorting) && !empty($sorting) ){

			if( isset($sorting['coloumn']) && !empty($sorting['coloumn']) && isset($sorting['order']) && !empty($sorting['order']) ){
				$order = "ORDER BY ".$sorting['coloumn']." ".$sorting['order'];
			}

		}

		$data = [];
		$query ="SELECT
				    departments.id,
				    departments.name,
				    departments.image,
				    departments.modified,
				    departments.modified_by,
				    skill_counts.total_skills AS totalskills,
				    subject_counts.total_subjects AS totalsubjects,
				    domain_counts.total_domains AS totaldomains,
				    details_counts.totalpeoples AS totalpeople,
				    story_counts.total_stories AS total_story,
				    departments.modified AS updated_on,
				    CONCAT_WS(' ', user_details.first_name , user_details.last_name) AS updated_by

				FROM `departments`

				left JOIN user_details on departments.modified_by = user_details.user_id

				LEFT JOIN
					(
						SELECT
							usd.department_id,
							COUNT(DISTINCT(usd.user_id)) AS totalpeoples
						FROM user_details usd
	                    LEFT JOIN users uss on uss.id = usd.user_id
	                    WHERE uss.role_id = 2 AND uss.status = 1 AND uss.is_activated = 1
						GROUP BY usd.department_id
					 ) AS details_counts
					ON details_counts.department_id = departments.id

				#total skills
				LEFT JOIN
					(
				    SELECT
				        ls.department_id,
				        COUNT(DISTINCT(ls.skill_id)) AS total_skills
				        FROM department_skills ls
				    	GROUP BY ls.department_id
				     ) AS skill_counts
					ON skill_counts.department_id = departments.id
				#total subjects
				LEFT JOIN
					(
				    SELECT
				        ls.department_id,
				        COUNT(DISTINCT(ls.subject_id)) AS total_subjects
				        FROM department_subjects ls
				    	GROUP BY ls.department_id
				     ) AS subject_counts
					ON subject_counts.department_id = departments.id
				#total domains
				LEFT JOIN
					(
				    SELECT
				        ld.department_id,
				        COUNT(DISTINCT(ld.domain_id)) AS total_domains
				        FROM department_domains ld
				    	GROUP BY ld.department_id
				     ) AS domain_counts
					ON domain_counts.department_id = departments.id

				#stories total
				LEFT JOIN
					(
				    SELECT
				        of.department_id,
				        COUNT(DISTINCT(of.id)) AS total_stories
				        FROM story_departments of
				    	GROUP BY of.department_id
				    ) AS story_counts
					ON story_counts.department_id = departments.id


				WHERE $filter_query

				GROUP BY departments.id
				$order
				$limit_query ";

		$details = $this->Department->query($query);
		$data = (isset($details) && !empty($details)) ? $details : [];

		return $data;
	}

	protected function find_locations($filter = null, $page = null, $sorting = array()){
		$limit_query = ' LIMIT '.$this->offset;
		if(isset($page) && !empty($page)){
			$limit_query = " LIMIT $page, ".$this->offset;
		}

		$ser = '^';
		$filter_query = ' 1';
		if(isset($filter) && !empty($filter)){
			//$search_str = htmlentities($filter, ENT_QUOTES);
			//$filter_query = " locations.name LIKE '%$search_str%'";

			//$search_str= Sanitize::escape(like(strtolower($filter), $ser ));

			$search_str= Sanitize::escape(like($filter, $ser ));

			$filter_query = " (locations.name LIKE '%$search_str%' ESCAPE '$ser') ";

		}

		$order = 'ORDER BY locations.name ASC';
		if( isset($sorting) && !empty($sorting) ){

			if( isset($sorting['coloumn']) && !empty($sorting['coloumn']) && isset($sorting['order']) && !empty($sorting['order']) ){
				if($sorting['coloumn'] == 'countryName'){
					$order = "ORDER BY countryName " . $sorting['order'] . ", locations.city ASC";
				}
				else {
					$order = "ORDER BY ".$sorting['coloumn']." ".$sorting['order'];
				}
			}

		}

		$data = [];
		$query ="SELECT
				    locations.id,
				    locations.`name`,
				    locations.image,
				    locations.city,
				    locations.modified,
				    locations.modified_by,
				    countries.countryName as countryName,
				    location_types.type as type,
	    			org_counts.used_org AS totalorg,
	    			people_counts.used_user AS totalpeople,
				    skill_counts.total_skills AS totalskills,
				    subject_counts.total_subjects AS totalsubjects,
				    domain_counts.total_domains AS totaldomains,
				    link_counts.total_links AS linktotal,
				    file_counts.total_files AS filetotal,
				    story_counts.total_stories AS total_story,
				    locations.modified AS updated_on,
				    CONCAT_WS(' ', user_details.first_name , user_details.last_name) AS updated_by

				FROM `locations`

				INNER JOIN location_types on location_types.id = locations.type_id
				inner join user_details on locations.modified_by = user_details.user_id
				inner join countries on countries.id = locations.country_id

				#total org
				LEFT JOIN
					(
				    	SELECT ol.location_id, count(DISTINCT(ol.organization_id)) as used_org
				    	FROM organization_locations ol
				    	GROUP BY ol.location_id
				     ) AS org_counts
					ON org_counts.location_id = locations.id

				#total people
				LEFT JOIN
					(
				    	SELECT ol.id, COUNT(DISTINCT(ud.user_id)) as used_user
				    	FROM user_details ud
				    	LEFT JOIN users usr ON usr.id = ud.user_id #AND usr.role_id = 2 AND usr.status = 1 AND usr.is_activated = 1
				    	LEFT JOIN locations ol ON ol.id = ud.location_id
				    	WHERE usr.role_id = 2 AND usr.status = 1 AND usr.is_activated = 1
				    	GROUP BY ol.id
				     ) AS people_counts
					ON people_counts.id = locations.id

				#total skills
				LEFT JOIN
					(
				    SELECT
				        ls.location_id,
				        COUNT(DISTINCT(ls.skill_id)) AS total_skills
				        FROM location_skills ls
				    	GROUP BY ls.location_id
				     ) AS skill_counts
					ON skill_counts.location_id = locations.id
				#total subjects
				LEFT JOIN
					(
				    SELECT
				        ls.location_id,
				        COUNT(DISTINCT(ls.subject_id)) AS total_subjects
				        FROM location_subjects ls
				    	GROUP BY ls.location_id
				     ) AS subject_counts
					ON subject_counts.location_id = locations.id
				#total domains
				LEFT JOIN
					(
				    SELECT
				        ld.location_id,
				        COUNT(DISTINCT(ld.domain_id)) AS total_domains
				        FROM location_domains ld
				    	GROUP BY ld.location_id
				     ) AS domain_counts
					ON domain_counts.location_id = locations.id
				#link total
				LEFT JOIN
					(
				    SELECT
				        ll.location_id,
				        COUNT(DISTINCT(ll.id)) AS total_links
				        FROM location_links ll
				    	GROUP BY ll.location_id
				     ) AS link_counts
					ON link_counts.location_id = locations.id
				#file total
				LEFT JOIN
					(
				    SELECT
				        lf.location_id,
				        COUNT(DISTINCT(lf.id)) AS total_files
				        FROM location_files lf
				        WHERE lf.filename != ''
				    	GROUP BY lf.location_id
				    ) AS file_counts
					ON file_counts.location_id = locations.id

				#stories total
				LEFT JOIN
					(
				    SELECT
				        of.location_id,
				        COUNT(DISTINCT(of.id)) AS total_stories
				        FROM story_locations of
				    	GROUP BY of.location_id
				    ) AS story_counts
					ON story_counts.location_id = locations.id


				WHERE $filter_query

				GROUP BY locations.id
				$order $limit_query ";
				// pr($query);

		$details = $this->Department->query($query);
		$data = (isset($details) && !empty($details)) ? $details : [];

		return $data;
	}

	public function get_organizations(){
		if ($this->request->isAjax()) {
            $this->layout = false;

            $title = (isset($this->request->data['title']) && !empty($this->request->data['title'])) ? $this->request->data['title'] : "";
			$data['list_data'] = $this->find_organization($title);

			$view = new View($this, false);
			$view->viewPath = 'Communities/partials/org';
			$view->set($data);
			$html = $view->render('get_list');

			echo json_encode($html);
			exit();
		}
	}

	public function get_departments(){
		if ($this->request->isAjax()) {
            $this->layout = false;

            $title = (isset($this->request->data['title']) && !empty($this->request->data['title'])) ? $this->request->data['title'] : "";
			$data['list_data'] = $this->find_departments($title);

			$view = new View($this, false);
			$view->viewPath = 'Communities/partials/dept';
			$view->set($data);
			$html = $view->render('get_list');

			echo json_encode($html);
			exit();
		}
	}

	public function get_locations(){
		if ($this->request->isAjax()) {
            $this->layout = false;

            $title = (isset($this->request->data['title']) && !empty($this->request->data['title'])) ? $this->request->data['title'] : "";

			$data['list_data'] = $this->find_locations($title);
			$view = new View($this, false);
			$view->viewPath = 'Communities/partials/loc';
			$view->set($data);
			$html = $view->render('get_list');

			echo json_encode($html);
			exit();
		}
	}

	public function tab_paging_data(){
		if ($this->request->isAjax()) {
            $this->layout = false;

			$view = new View($this, false);

            if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
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

				if($type == 'org'){
					$data['list_data'] = $this->find_organization($q, $page, $sorting);
					$view->viewPath = 'Communities/partials/org';
					$view->set($data);
					$html = $view->render('get_list');
				}
				else if($type == 'dept'){
					$data['list_data'] = $this->find_departments($q, $page, $sorting);
					$view->viewPath = 'Communities/partials/dept';
					$view->set($data);
					$html = $view->render('get_list');
				}
				else if($type == 'loc'){
					$data['list_data'] = $this->find_locations($q, $page, $sorting);
					$view->viewPath = 'Communities/partials/loc';
					$view->set($data);
					$html = $view->render('get_list');
				}
				else if($type == 'comm'){
					$data['list_data'] = $this->find_community($q, $page, $sorting);
					$view->viewPath = 'Communities/partials/comm';
					$view->set($data);
					$html = $view->render('get_list');
				}

			}

			echo json_encode($html);
			exit();
		}
	}

	public function get_states(){
		if ($this->request->isAjax()) {
            $this->layout = false;
            $response = ['success' => false, 'content' => []];
            $data = [];
            if ($this->request->is('post') || $this->request->is('put')) {
            	$post = $this->request->data;
            	// pr($post);
            	if(isset($post['code']) && !empty($post['code'])) {
            		$this->loadModel('State');
            		$data = $this->State->query("SELECT id, name from states WHERE country_iso_code = '".$post['code']."'");
            		$states = [];
            		if(isset($data) && !empty($data)){
            			foreach ($data as $key => $value) {
            				$states[] = ['id' => $value['states']['id'], 'name' => $value['states']['name']];
            			}
            		}
            		$response['success'] = true;
            		$response['content'] = $states;
            		// pr($states);
            	}
            }
            echo json_encode($response);
            exit;
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

				$folder_url = WWW_ROOT . 'uploads/dept_temp_files/';
				$upload_object = null;

				if ( isset($this->request->data['LocationFile']['upload_file']['name']) && !empty($this->request->data['LocationFile']['upload_file']['name']) ) {
					$upload_object = $this->request->data['LocationFile']['upload_file'];
				}
				if ( isset($this->request->data['Location']['image']['name']) && !empty($this->request->data['Location']['image']['name']) ) {
					$upload_object = $this->request->data['Location']['image'];
				}
				if ( isset($this->request->data['Department']['image']['name']) && !empty($this->request->data['Department']['image']['name']) ) {
					$upload_object = $this->request->data['Department']['image'];
				}
				if ( isset($this->request->data['Organization']['image']['name']) && !empty($this->request->data['Organization']['image']['name']) ) {
					$upload_object = $this->request->data['Organization']['image'];
				}
				if ( isset($this->request->data['OrganizationFile']['upload_file']['name']) && !empty($this->request->data['OrganizationFile']['upload_file']['name']) ) {
					$upload_object = $this->request->data['OrganizationFile']['upload_file'];
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
					$unlikpath = WWW_ROOT . 'uploads/dept_temp_files/';
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
					$unlikpath = WWW_ROOT . 'uploads/dept_temp_files/';
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

	public function delete_image($link_id = null){

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
					if( $this->request->data['type'] == 'dept' ){
						$model = 'Department';
						$unlikpath = WWW_ROOT . COMM_IMAGE_PATH;
					}
					if( $this->request->data['type'] == 'loc' ){
						$model = 'Location';
						$unlikpath = WWW_ROOT . LOC_IMAGE_PATH;
					}
					if( $this->request->data['type'] == 'org' ){
						$model = 'Organization';
						$unlikpath = WWW_ROOT . ORG_IMAGE_PATH;
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
					if( $post['type'] == 'loc' ){
						$model = 'LocationFile';
						$unlikpath = WWW_ROOT . LOC_FILE_PATH;
					}
					if( $post['type'] == 'org' ){
						$model = 'OrganizationFile';
						$unlikpath = WWW_ROOT . ORG_FILE_PATH;
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
					if( $post['type'] == 'loc' ){
						$model = 'LocationLink';
					}
					if( $post['type'] == 'org' ){
						$model = 'OrganizationLink';
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

	public function download_files($type = 'loc', $id = null) {

		if (isset($id) && !empty($id)) {

			// Retrieve the file ready for download
			if( $type == 'loc' ){
				$model = 'LocationFile';
				$path = LOC_FILE_PATH;
			}
			if( $type == 'org' ){
				$model = 'OrganizationFile';
				$path = ORG_FILE_PATH;
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
				$response['content'] = LOC_TEMP_PATH.$filename;
				$response['success'] = true;
			}
			$this->autoRender = false;
			return $this->response->file($response['content'], array('download' => true));
		}

	}

}