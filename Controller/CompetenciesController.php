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

class CompetenciesController  extends AppController {

	public $name = 'Competencies';
	public $uses = array('User', 'Skill', 'SkillLink', 'SkillFile','Subject', 'SubjectLink', 'SubjectFile','KnowledgeDomain', 'DomainLink', 'DomainFile', 'SkillPdf', 'UserSkill', 'SkillDetail', 'ProjectSkill', 'SubjectPdf', 'UserSubject', 'SubjectDetail', 'ProjectSubject', 'DomainPdf', 'UserDomain' , 'DomainDetail', 'ProjectDomain','Keyword','LocationSkill','LocationSubject','LocationDomain');


	/**
	* check login for admin and frontend user
	* allow and deny user
	*/
	//public $components = array('Email');

	/**
	* Helpers
	*
	* @var array
	*/

	public $objView = null;
	public $user_id = null;
	public $offset = 0;
	public $tab_offset = 0;
	public $compare_user_offset = 0;
	public $compare_comp_offset = 0;
	public $watch_limit = 0;
	public $is_admin = false;

	public $helpers = array('Html', 'Form', 'Session', 'Time', 'Text','Common','Wiki','Permission');


	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow('watch_diff_cron');
		$view = new View();
		$this->objView = $view;

		$this->user_id = $this->Auth->user('id');

		$this->offset = 50;
		$this->tab_offset = 50;
		$this->compare_user_offset = 25;
		$this->compare_comp_offset = 50;
		$this->watch_limit = 50;

		$this->setJsVars(['compare_user_offset' => $this->compare_user_offset, 'compare_comp_offset' => $this->compare_comp_offset, 'watch_limit' => $this->watch_limit]);

		if( $_SERVER['HTTP_HOST'] == '192.168.7.20' )  {
			$this->is_admin = true;
		}
		else{
			$this->is_admin = false;
		}
		if( ( $this->Session->read('Auth.User.role_id') == 2 && $this->Session->read('Auth.User.UserDetail.administrator') == 1 ) ){
			$this->is_admin = true;
		}
		$this->set('user_is_admin', $this->is_admin);
	}

	public function index($type = null, $data_id = null){

		$this->layout = 'inner';
        $data['title_for_layout'] = __('Competencies', true);
        $data['page_heading'] = __('Competencies', true);
        $data['page_subheading'] = __('Find people with skills, subject matter expertize and domain knowledge', true);
		$this->_doc_file_ext();


		$this->setJsVar('image_extension', array('bmp', 'gif', 'jpg', 'pps', 'png') );
		$this->setJsVar('files_extension', array('doc', 'docx','csv','ppt', 'pptx', 'rtf', 'txt','pdf') );

        // GET SKILLS
        $skills = array();
        $this->loadModel('Skill');
		$skillsAll = $this->Skill->query("SELECT * FROM skill ORDER BY title ASC");
		if (isset($skillsAll) && !empty($skillsAll)) {
			foreach ($skillsAll as $skil) {
				$skills[$skil['skill']['id']] = $skil['skill']['title'];
			}
		}
		$data['skills'] = $skills;

        // GET SUBJECT
        $subjects = array();
        $this->loadModel('Subject');
		$subAll = $this->Subject->query("SELECT * FROM subjects WHERE status = 1 ORDER BY title ASC");
		if (isset($subAll) && !empty($subAll)) {
			foreach ($subAll as $sub) {
				$subjects[$sub['subjects']['id']] = $sub['subjects']['title'];
			}
		}
		$data['subjects'] = $subjects;

        // GET DOMAINS
        $domains = array();
        $this->loadModel('KnowledgeDomain');
		$domAll = $this->KnowledgeDomain->query("SELECT * FROM knowledge_domains WHERE status = 1 ORDER BY title ASC");
		if (isset($domAll) && !empty($domAll)) {
			foreach ($domAll as $dom) {
				$domains[$dom['knowledge_domains']['id']] = $dom['knowledge_domains']['title'];
			}
		}
		$data['domains'] = $domains;
		$crumb = [
					'last' => [
						'data' => [
							'title' => "Competencies",
							'data-original-title' => "Competencies",
						],
					],
				];
		$this->set('crumb',$crumb);

		$sel_title = '';
		if((isset($type) && !empty($type)) && (isset($data_id) && !empty($data_id))) {
			if($type == 'skills'){
				$sel_data = $this->Skill->find("first", ['conditions' => ['id' => $data_id]]);
				$sel_title = $sel_data['Skill']['title'];
			}
			else if($type == 'subjects'){
				$sel_data = $this->Subject->find("first", ['conditions' => ['id' => $data_id]]);
				$sel_title = $sel_data['Subject']['title'];
			}
			else if($type == 'domains'){
				$sel_data = $this->KnowledgeDomain->find("first", ['conditions' => ['id' => $data_id]]);
				$sel_title = $sel_data['KnowledgeDomain']['title'];
			}
		}

		$watch_id = 0;
		if(isset($this->params['named']['cmp_type']) && !empty($this->params['named']['cmp_type']) ){
			$watch_id = $this->params['named']['watch'];
			$this->loadModel('UserCompetencyWatch');
			if(!$this->UserCompetencyWatch->hasAny(['id' => $watch_id])){$watch_id = 0;}
		}

		$this->loadModel('UserViewsCompare');
		$userViewsList = $this->UserViewsCompare->find('list', ['conditions' => ['user_id' => $this->user_id], 'fields' => ['id', 'name'], 'order' => ['name ASC']]);

		$data['sel_type'] = $type;
		$data['sel_id'] = $data_id;
		$data['sel_title'] = $sel_title;
		$data['userViewsList'] = $userViewsList;
		// pr($userViewsList,1);
		$this->setJsVars(['sel_type' => $type, 'sel_title' => $sel_title, 'search_compare' => true, 'watch_id' => $watch_id]);
		$this->set($data);

	}

	//****************** XHR Functions *********************//

	public function data_listing() {

		if ($this->request->isAjax()) {

			$this->layout = false;
			$user_id = $this->user_id;

			$viewData = [];
			$viewData['users_count'] = 0;
			$offset = $this->offset;

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;

				$viewData['selected_skills'] = [];
				$viewData['selected_subjects'] = [];
				$viewData['selected_domains'] = [];
				$viewData['skill_match'] = false;
				$viewData['subject_match'] = false;
				$viewData['domain_match'] = false;

				$select_qry[] = "DISTINCT(users.id) ";
				$joins = [];

				$from = "FROM users";

				$where[] = 'users.role_id = 2';
				$where[] = 'users.status = 1';
				$where2 = [];
				$group[] = "users.id";
				$order = " ORDER BY users.id ASC ";



				if(isset($post['skills']) && !empty($post['skills'])){
					$viewData['selected_skills'] = $post['skills'];
					$selected_skills = implode(',', $post['skills']);
					$count_skills = count($post['skills']);

					$joins[] = "LEFT JOIN user_skills uskill
									ON users.id = uskill.user_id

								LEFT JOIN skills
									ON uskill.skill_id = skills.id ";

					$skill_match = "";
					if(isset($post['skill_match']) && !empty($post['skill_match'])){
						$viewData['skill_match'] = true;
						$skill_match = " HAVING COUNT(DISTINCT skill_id) = $count_skills";
					}


						$where2[] = "( uskill.user_id IN(
											SELECT us.user_id
											FROM `user_skills` us
											WHERE 1 AND skill_id IN ($selected_skills)
											GROUP BY us.user_id
											$skill_match
										) AND  uskill.skill_id IN ($selected_skills)
									)";



					$group[] = "uskill.user_id";
				}

				if(isset($post['subjects']) && !empty($post['subjects'])){
					$viewData['selected_subjects'] = $post['subjects'];
					$selected_subjects = implode(',', $post['subjects']);
					$count_subjects = count($post['subjects']);

					$joins[] = "LEFT JOIN user_subjects sub1
									ON users.id = sub1.user_id

								LEFT JOIN subjects
									ON sub1.subject_id = subjects.id ";

					$subject_match = "";



					if(isset($post['subject_match']) && !empty($post['subject_match'])){
						$viewData['subject_match'] = true;
						$subject_match = " HAVING COUNT(DISTINCT subject_id) = $count_subjects";
					}

						$where2[] = "(sub1.user_id IN(
										SELECT ss.user_id
										FROM `user_subjects` ss
										WHERE 1 AND subject_id IN ($selected_subjects)
										GROUP BY ss.user_id
										$subject_match
									) AND  sub1.subject_id IN ($selected_subjects)
								)";


					$group[] = "sub1.user_id";
				}

				if(isset($post['domains']) && !empty($post['domains'])){
					$viewData['selected_domains'] = $post['domains'];
					$selected_domains = implode(',', $post['domains']);
					$count_domains = count($post['domains']);


					$joins[] = "LEFT JOIN user_domains dom1
									ON users.id = dom1.user_id

								LEFT JOIN knowledge_domains
									ON dom1.domain_id = knowledge_domains.id ";

					$domain_match = "";
					if(isset($post['domain_match']) && !empty($post['domain_match'])){
						$viewData['domain_match'] = true;
						$domain_match = " HAVING COUNT(DISTINCT domain_id) = $count_domains";
					}

					$where2[] = "( dom1.user_id IN(
										SELECT ud.user_id
										FROM `user_domains` ud
										WHERE 1 AND domain_id IN ($selected_domains)
										GROUP BY ud.user_id
										$domain_match
									) AND dom1.domain_id IN ($selected_domains)
								)";



					$group[] = "dom1.user_id";
				}

				$qry = "";
				$qry .= " SELECT ". implode(', ', $select_qry);
				$qry .= " " . $from;
				$qry .= " " . implode(' ', $joins);
				$qry .= " WHERE " . implode(' AND ', $where);

				if( (isset($post['skills']) && !empty($post['skills'])) || (isset($post['subjects']) && !empty($post['subjects'])) || (isset($post['domains']) && !empty($post['domains']))){
					$qry .= " AND ";
				}

				if(isset($post['match_all']) && !empty($post['match_all'])){
					$qry .= " " . implode(' AND ', $where2);
				}
				else{
					$qry .= " " . implode(' OR ', $where2);
				}

				$qry .= " GROUP BY " . implode(', ', $group);
				$qry .= " " . $order;

				$user_ids = $this->User->query($qry);

				$users = [];
				if(isset($user_ids) && !empty($user_ids)){
					$user_ids = Set::extract($user_ids, '{n}/users/id');
					$user_ids = implode(',', $user_ids);

					$query = "SELECT DISTINCT(users.id),
						CONCAT_WS(' ', user_details.first_name , user_details.last_name) as fullname,
						user_details.first_name, user_details.last_name,
						user_details.profile_pic,
						user_details.job_title,
						GROUP_CONCAT(DISTINCT( CONCAT(skills.title,'/,/',skill_details.user_level,'/,/',skill_details.user_experience,'/,/',skills.id,'/,/',sk1.created)) ORDER BY skills.title SEPARATOR '/;/' ) as member_skills,
						GROUP_CONCAT(DISTINCT( CONCAT(subjects.title,'/,/',sd1.user_level,'/,/',sd1.user_experience,'/,/',subjects.id,'/,/',sub1.created)) ORDER BY subjects.title SEPARATOR '/;/') as member_subjects,
						GROUP_CONCAT(DISTINCT( CONCAT(knowledge_domains.title,'/,/',dm1.user_level,'/,/',dm1.user_experience,'/,/',knowledge_domains.id,'/,/',dom1.created)) ORDER BY knowledge_domains.title SEPARATOR '/;/') as member_domains

						FROM users

						LEFT JOIN user_details
							ON users.id = user_details.user_id

						LEFT JOIN user_skills sk1
									ON users.id = sk1.user_id

						LEFT JOIN skills
							ON sk1.skill_id = skills.id

						INNER JOIN skill_details
							ON users.id = skill_details.user_id AND skill_details.skill_id = skills.id

						LEFT JOIN user_subjects sub1
									ON users.id = sub1.user_id

						LEFT JOIN subjects
									ON sub1.subject_id = subjects.id

						INNER JOIN subject_details sd1
							ON users.id = sd1.user_id AND sd1.subject_id = subjects.id

						LEFT JOIN user_domains dom1
							ON users.id = dom1.user_id

						LEFT JOIN knowledge_domains
							ON dom1.domain_id = knowledge_domains.id

						INNER JOIN domain_details dm1
							ON users.id = dm1.user_id AND dm1.domain_id = knowledge_domains.id

						WHERE users.id IN ($user_ids) AND users.role_id = 2 AND users.status = 1 AND users.is_activated = 1

						GROUP BY users.id, skill_details.user_id, sub1.user_id, dom1.user_id

						LIMIT $offset
					";
					// pr($query);
					$users = $this->User->query($query);
					$viewData['users_count'] = $this->get_counter($post);

				}
				$viewData['users'] = $users;
			}

			$view = new View($this, false);
			$view->viewPath = 'Competencies/partials';
			$view->set($viewData);
			$html = $view->render('data_listing');

			echo json_encode($html);
			exit();

		}
	}

	public function paging_data() {
		$this->layout = false;
		$offset = $this->offset;
		if ($this->request->is('post') || $this->request->is('put')) {
			$post = $this->request->data;

			$page = $post['page'];

			$viewData['selected_skills'] = [];
			$viewData['selected_subjects'] = [];
			$viewData['selected_domains'] = [];
			$viewData['skill_match'] = false;
			$viewData['subject_match'] = false;
			$viewData['domain_match'] = false;

			$select_qry[] = "DISTINCT(users.id) ";
			$joins = [];

			$from = "FROM users";

			$where[] = 'users.role_id = 2';
			$where[] = 'users.status = 1';
			$where2 = [];
			$group[] = "users.id";
			$order = " ORDER BY users.id ASC ";



			if(isset($post['skills']) && !empty($post['skills'])){
				$viewData['selected_skills'] = $post['skills'];
				$selected_skills = implode(',', $post['skills']);
				$count_skills = count($post['skills']);

				$joins[] = "LEFT JOIN user_skills uskill
								ON users.id = uskill.user_id

							LEFT JOIN skills
								ON uskill.skill_id = skills.id ";

				$skill_match = "";
				if(isset($post['skill_match']) && !empty($post['skill_match'])){
					$viewData['skill_match'] = true;
					$skill_match = " HAVING COUNT(DISTINCT skill_id) = $count_skills";
				}


					$where2[] = "( uskill.user_id IN(
										SELECT us.user_id
										FROM `user_skills` us
										WHERE 1 AND skill_id IN ($selected_skills)
										GROUP BY us.user_id
										$skill_match
									) AND  uskill.skill_id IN ($selected_skills)
								)";



				$group[] = "uskill.user_id";
			}


			if(isset($post['subjects']) && !empty($post['subjects'])){
				$viewData['selected_subjects'] = $post['subjects'];
				$selected_subjects = implode(',', $post['subjects']);
				$count_subjects = count($post['subjects']);

				$joins[] = "LEFT JOIN user_subjects sub1
								ON users.id = sub1.user_id

							LEFT JOIN subjects
								ON sub1.subject_id = subjects.id ";

				$subject_match = "";



				if(isset($post['subject_match']) && !empty($post['subject_match'])){
					$viewData['subject_match'] = true;
					$subject_match = " HAVING COUNT(DISTINCT subject_id) = $count_subjects";
				}

					$where2[] = "(sub1.user_id IN(
									SELECT ss.user_id
									FROM `user_subjects` ss
									WHERE 1 AND subject_id IN ($selected_subjects)
									GROUP BY ss.user_id
									$subject_match
								) AND  sub1.subject_id IN ($selected_subjects)
							)";


				$group[] = "sub1.user_id";
			}


			if(isset($post['domains']) && !empty($post['domains'])){
				$viewData['selected_domains'] = $post['domains'];
				$selected_domains = implode(',', $post['domains']);
				$count_domains = count($post['domains']);


				$joins[] = "LEFT JOIN user_domains dom1
								ON users.id = dom1.user_id

							LEFT JOIN knowledge_domains
								ON dom1.domain_id = knowledge_domains.id ";

				$domain_match = "";
				if(isset($post['domain_match']) && !empty($post['domain_match'])){
					$viewData['domain_match'] = true;
					$domain_match = " HAVING COUNT(DISTINCT domain_id) = $count_domains";
				}

				$where2[] = "( dom1.user_id IN(
									SELECT ud.user_id
									FROM `user_domains` ud
									WHERE 1 AND domain_id IN ($selected_domains)
									GROUP BY ud.user_id
									$domain_match
								) AND dom1.domain_id IN ($selected_domains)
							)";



				$group[] = "dom1.user_id";
			}

			$qry = "";
			$qry .= " SELECT ". implode(', ', $select_qry);
			$qry .= " " . $from;
			$qry .= " " . implode(' ', $joins);
			$qry .= " WHERE " . implode(' AND ', $where);

			if( (isset($post['skills']) && !empty($post['skills'])) || (isset($post['subjects']) && !empty($post['subjects'])) || (isset($post['domains']) && !empty($post['domains']))){
				$qry .= " AND ";
			}

			if(isset($post['match_all']) && !empty($post['match_all'])){
				$qry .= " " . implode(' AND ', $where2);
			}
			else{
				$qry .= " " . implode(' OR ', $where2);
			}

			$qry .= " GROUP BY " . implode(', ', $group);
			$qry .= " " . $order;

			$user_ids = $this->User->query($qry);

			$users = [];
			if(isset($user_ids) && !empty($user_ids)){
				$user_ids = Set::extract($user_ids, '{n}/users/id');
				$user_ids = implode(',', $user_ids);

				$query = "SELECT users.id,
					CONCAT_WS(' ', user_details.first_name , user_details.last_name) as fullname,
					user_details.first_name, user_details.last_name,
					user_details.profile_pic,
					user_details.job_title,
					GROUP_CONCAT(DISTINCT( CONCAT(skills.title,'/,/',skill_details.user_level,'/,/',skill_details.user_experience,'/,/',skills.id,'/,/',sk1.created)) ORDER BY skills.title SEPARATOR '/;/') as member_skills,
						GROUP_CONCAT(DISTINCT( CONCAT(subjects.title,'/,/',sd1.user_level,'/,/',sd1.user_experience,'/,/',subjects.id,'/,/',sub1.created)) ORDER BY subjects.title SEPARATOR '/;/') as member_subjects,
						GROUP_CONCAT(DISTINCT( CONCAT(knowledge_domains.title,'/,/',dm1.user_level,'/,/',dm1.user_experience,'/,/',knowledge_domains.id,'/,/',dom1.created)) ORDER BY knowledge_domains.title SEPARATOR '/;/') as member_domains

					FROM users

					LEFT JOIN user_details
						ON users.id = user_details.user_id

					LEFT JOIN user_skills sk1
								ON users.id = sk1.user_id

					LEFT JOIN skills
					ON sk1.skill_id = skills.id

					INNER JOIN skill_details
					ON users.id = skill_details.user_id AND skill_details.skill_id = skills.id

					LEFT JOIN user_subjects sub1
								ON users.id = sub1.user_id

					LEFT JOIN subjects
								ON sub1.subject_id = subjects.id

					INNER JOIN subject_details sd1
						ON users.id = sd1.user_id AND sd1.subject_id = subjects.id

					LEFT JOIN user_domains dom1
						ON users.id = dom1.user_id

					LEFT JOIN knowledge_domains
						ON dom1.domain_id = knowledge_domains.id

					INNER JOIN domain_details dm1
						ON users.id = dm1.user_id AND dm1.domain_id = knowledge_domains.id

					WHERE users.id IN ($user_ids) AND users.role_id = 2 AND users.status = 1 AND users.is_activated = 1

					GROUP BY skill_details.user_id, sub1.user_id, dom1.user_id

					LIMIT $page, $offset

						";

				$users = $this->User->query($query);
				$viewData['users'] = $users;

			}
		}

		$view = new View($this, false);
		$view->viewPath = 'Competencies/partials';
		$view->set($viewData);
		$html = $view->render('paging_data');

		echo json_encode($html);
		exit();
	}

	public function paging_count() {
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';

			$users_count = 0;

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;


				$users_count = $this->get_counter($post);

			}

			echo json_encode($users_count);
			exit;
		}
	}

	protected function get_counter($post = null) {

		$users_count = 0;

		$viewData['selected_skills'] = [];
		$viewData['selected_subjects'] = [];
		$viewData['selected_domains'] = [];
		$viewData['skill_match'] = false;
		$viewData['subject_match'] = false;
		$viewData['domain_match'] = false;

		$select_qry[] = "DISTINCT(users.id) ";
		$joins = [];

		$from = "FROM users";

		$where[] = 'users.role_id = 2';
		$where[] = 'users.status = 1';
		$where2 = [];
		$group[] = "users.id";
		$order = " ORDER BY users.id ASC ";

		if(isset($post['skills']) && !empty($post['skills'])){
			$viewData['selected_skills'] = $post['skills'];
			$selected_skills = implode(',', $post['skills']);
			$count_skills = count($post['skills']);

			$joins[] = "LEFT JOIN user_skills uskill
							ON users.id = uskill.user_id

						LEFT JOIN skills
							ON uskill.skill_id = skills.id ";

			$skill_match = "";
			if(isset($post['skill_match']) && !empty($post['skill_match'])){
				$viewData['skill_match'] = true;
				$skill_match = " HAVING COUNT(DISTINCT skill_id) = $count_skills";
			}


				$where2[] = "( uskill.user_id IN(
									SELECT us.user_id
									FROM `user_skills` us
									WHERE 1 AND skill_id IN ($selected_skills)
									GROUP BY us.user_id
									$skill_match
								) AND  uskill.skill_id IN ($selected_skills)
							)";



			$group[] = "uskill.user_id";
		}


		if(isset($post['subjects']) && !empty($post['subjects'])){
			$viewData['selected_subjects'] = $post['subjects'];
			$selected_subjects = implode(',', $post['subjects']);
			$count_subjects = count($post['subjects']);

			$joins[] = "LEFT JOIN user_subjects sub1
							ON users.id = sub1.user_id

						LEFT JOIN subjects
							ON sub1.subject_id = subjects.id ";

			$subject_match = "";



			if(isset($post['subject_match']) && !empty($post['subject_match'])){
				$viewData['subject_match'] = true;
				$subject_match = " HAVING COUNT(DISTINCT subject_id) = $count_subjects";
			}

				$where2[] = "(sub1.user_id IN(
								SELECT ss.user_id
								FROM `user_subjects` ss
								WHERE 1 AND subject_id IN ($selected_subjects)
								GROUP BY ss.user_id
								$subject_match
							) AND  sub1.subject_id IN ($selected_subjects)
						)";


			$group[] = "sub1.user_id";
		}


		if(isset($post['domains']) && !empty($post['domains'])){
			$viewData['selected_domains'] = $post['domains'];
			$selected_domains = implode(',', $post['domains']);
			$count_domains = count($post['domains']);


			$joins[] = "LEFT JOIN user_domains dom1
							ON users.id = dom1.user_id

						LEFT JOIN knowledge_domains
							ON dom1.domain_id = knowledge_domains.id ";

			$domain_match = "";
			if(isset($post['domain_match']) && !empty($post['domain_match'])){
				$viewData['domain_match'] = true;
				$domain_match = " HAVING COUNT(DISTINCT domain_id) = $count_domains";
			}

			$where2[] = "( dom1.user_id IN(
								SELECT ud.user_id
								FROM `user_domains` ud
								WHERE 1 AND domain_id IN ($selected_domains)
								GROUP BY ud.user_id
								$domain_match
							) AND dom1.domain_id IN ($selected_domains)
						)";



			$group[] = "dom1.user_id";
		}

		$qry = "";
		$qry .= " SELECT ". implode(', ', $select_qry);
		$qry .= " " . $from;
		$qry .= " " . implode(' ', $joins);
		$qry .= " WHERE " . implode(' AND ', $where);

		if( (isset($post['skills']) && !empty($post['skills'])) || (isset($post['subjects']) && !empty($post['subjects'])) || (isset($post['domains']) && !empty($post['domains']))){
			$qry .= " AND ";
		}

		if(isset($post['match_all']) && !empty($post['match_all'])){
			$qry .= " " . implode(' AND ', $where2);
		}
		else{
			$qry .= " " . implode(' OR ', $where2);
		}

		$qry .= " GROUP BY " . implode(', ', $group);
		$qry .= " " . $order;

		$user_ids = $this->User->query($qry);

		$users = [];
		if(isset($user_ids) && !empty($user_ids)){
			$user_ids = Set::extract($user_ids, '{n}/users/id');
			$user_ids = implode(',', $user_ids);

			$query = "SELECT users.id

				FROM users

				LEFT JOIN user_details
					ON users.id = user_details.user_id

				INNER JOIN skill_details
				ON skill_details.user_id = users.id

				LEFT JOIN skills
				ON skills.id = skill_details.skill_id

				LEFT JOIN user_subjects sub1
							ON users.id = sub1.user_id

				LEFT JOIN subjects
							ON sub1.subject_id = subjects.id

				INNER JOIN subject_details sd1
					ON users.id = sd1.user_id AND sd1.subject_id = subjects.id

				LEFT JOIN user_domains dom1
					ON users.id = dom1.user_id

				LEFT JOIN knowledge_domains
					ON dom1.domain_id = knowledge_domains.id

				INNER JOIN domain_details dm1
					ON users.id = dm1.user_id AND dm1.domain_id = knowledge_domains.id

				WHERE users.id IN ($user_ids) AND users.role_id = 2 AND users.status = 1 AND users.is_activated = 1

				GROUP BY skill_details.user_id, sub1.user_id, dom1.user_id

					";

			$users = $this->User->query($query);
			if(isset($post['list']) && !empty($post['list'])){
				if(isset($users) && !empty($users)){
					$users_data = Set::extract($users, '{n}.users.id');
					$users_count = implode(',', $users_data);
				}
			}
			else{
				$users_count = (isset($users) && !empty($users)) ? count($users) : 0;
			}
		}
		return $users_count;
	}

	protected function find_skills($filter = null, $page = null, $sorting = array()) {
		$limit_query = ' LIMIT '.$this->offset;
		if(isset($page) && !empty($page)){
			$limit_query = " LIMIT $page, ".$this->offset;
		}

		$ser = '^';
		$filter_query = ' 1';
		if(isset($filter) && !empty($filter)){
			//$search_str = htmlentities($filter, ENT_QUOTES);
			//$filter_query = " skills.title LIKE '%$search_str%'";

			//$search_str= Sanitize::escape(like(strtolower($filter), $ser ));

			$search_str= Sanitize::escape(like($filter, $ser ));

			$filter_query = " (skills.title LIKE '%$search_str%' ESCAPE '$ser') ";

		}

		$order = 'ORDER BY title ASC';
		if( isset($sorting) && !empty($sorting) ){

			if( isset($sorting['coloumn']) && !empty($sorting['coloumn']) && isset($sorting['order']) && !empty($sorting['order']) ){
				$order = "ORDER BY ".$sorting['coloumn']." ".$sorting['order'];
			}

		}

		$data = [];
		$query ="SELECT
					skills.*, CONCAT_WS(' ', user_details.first_name , user_details.last_name) as fullname,
					link_counts.total_links AS linktotal,
					file_counts.total_files AS filetotal,
					user_counts.total_people AS totalpeople,
					keyword_counts.total_keywords AS totalkeyword,
					location_counts.total_location AS totallocation,
					org_counts.total_org AS totalorganization,
					dept_counts.total_dept AS totaldepartment,
					story_counts.total_stories AS total_story

				FROM skills
					LEFT JOIN user_details on
						user_details.user_id = skills.modified_by

					#link total
				LEFT JOIN
				(
			    SELECT
			        ol.skill_id,
			        COUNT(DISTINCT(ol.id)) AS total_links
			        FROM skill_links ol
			    	GROUP BY ol.skill_id
			     ) AS link_counts
				ON link_counts.skill_id = skills.id

				#file total
				LEFT JOIN
				(
			    SELECT
			        of.skill_id,
			        COUNT(DISTINCT(of.id)) AS total_files
			        FROM skill_files of
			        WHERE of.file_name != '' and of.file_name IS NOT NULL AND of.file_name != ''
			    	GROUP BY of.skill_id
			    ) AS file_counts
				ON file_counts.skill_id = skills.id

				#people total
				LEFT JOIN
				(
			    SELECT
			        of.skill_id,
			        COUNT(DISTINCT(of.id)) AS total_people
			        FROM user_skills of
			        INNER JOIN users ON users.id = of.user_id
			        WHERE users.role_id = 2 AND users.status = 1 AND users.is_activated = 1
			    	GROUP BY of.skill_id
			    ) AS user_counts
				ON user_counts.skill_id = skills.id

				#total keywords
				LEFT JOIN
				(
			    SELECT
			        of.item_id,
			        COUNT(DISTINCT(of.id)) AS total_keywords
			        FROM keywords of
			        WHERE of.type = 'skill'
			    	GROUP BY of.item_id
			    ) AS keyword_counts
				ON keyword_counts.item_id = skills.id

				#loc total
				LEFT JOIN
				(
			    SELECT
			        of.skill_id,
			        COUNT(DISTINCT(of.id)) AS total_location
			        FROM location_skills of
			    	GROUP BY of.skill_id
			    ) AS location_counts
				ON location_counts.skill_id = skills.id

				#org total
				LEFT JOIN
				(
			    SELECT
			        of.skill_id,
			        COUNT(DISTINCT(of.id)) AS total_org
			        FROM organization_skills of
			    	GROUP BY of.skill_id
			    ) AS org_counts
				ON org_counts.skill_id = skills.id

				#dept total
				LEFT JOIN
				(
			    SELECT
			        of.skill_id,
			        COUNT(DISTINCT(of.id)) AS total_dept
			        FROM department_skills of
			    	GROUP BY of.skill_id
			    ) AS dept_counts
				ON dept_counts.skill_id = skills.id

				#story total
				LEFT JOIN
				(
			    SELECT
			        of.skill_id,
			        COUNT(DISTINCT(of.id)) AS total_stories
			        FROM story_skills of
			    	GROUP BY of.skill_id
			    ) AS story_counts
				ON story_counts.skill_id = skills.id

				WHERE $filter_query

				group by skills.id
				$order
				$limit_query
			";
		// pr($query);
		$skillsAll = $this->Skill->query($query);
		if (isset($skillsAll) && !empty($skillsAll)) {
			$data = $skillsAll;
		}
		return $data;
	}

	protected function find_subjects($filter = null, $page = null, $sorting = array()){
		$limit_query = ' LIMIT '.$this->offset;
		if(isset($page) && !empty($page)){
			$limit_query = " LIMIT $page, ".$this->offset;
		}

		$ser = '^';
		$filter_query = ' 1';
		if(isset($filter) && !empty($filter)){
			//$search_str = htmlentities($filter, ENT_QUOTES);
			//$filter_query = " subjects.title LIKE '%$search_str%'";

			//$search_str= Sanitize::escape(like(strtolower($filter), $ser ));

			$search_str= Sanitize::escape(like($filter, $ser ));

			$filter_query = " (subjects.title LIKE '%$search_str%' ESCAPE '$ser') ";

		}

		$order = 'ORDER BY title ASC';
		if( isset($sorting) && !empty($sorting) ){

			if( isset($sorting['coloumn']) && !empty($sorting['coloumn']) && isset($sorting['order']) && !empty($sorting['order']) ){
				$order = "ORDER BY ".$sorting['coloumn']." ".$sorting['order'];
			}

		}

		$data = [];
		$query ="SELECT
				subjects.*,CONCAT_WS(' ', user_details.first_name , user_details.last_name) as fullname,

					link_counts.total_links AS linktotal,
					file_counts.total_files AS filetotal,
					user_counts.total_people AS totalpeople,
					keyword_counts.total_keywords AS totalkeyword,
					location_counts.total_location AS totallocation,
					org_counts.total_org AS totalorganization,
					dept_counts.total_dept AS totaldepartment,
					story_counts.total_stories AS total_story

				FROM subjects
					LEFT JOIN user_details on
						user_details.user_id = subjects.modified_by

						#link total
					LEFT JOIN
					(
				    SELECT
				        ol.subject_id,
				        COUNT(DISTINCT(ol.id)) AS total_links
				        FROM subject_links ol
				    	GROUP BY ol.subject_id
				     ) AS link_counts
					ON link_counts.subject_id = subjects.id

					#file total
					LEFT JOIN
					(
				    SELECT
				        of.subject_id,
				        COUNT(DISTINCT(of.id)) AS total_files
				        FROM subject_files of
				        WHERE of.file_name != '' and of.file_name IS NOT NULL AND of.file_name != ''
				    	GROUP BY of.subject_id
				    ) AS file_counts
					ON file_counts.subject_id = subjects.id

					#people total
					LEFT JOIN
					(
				    SELECT
				        of.subject_id,
				        COUNT(DISTINCT(of.id)) AS total_people
				        FROM user_subjects of
				        INNER JOIN users ON users.id = of.user_id
				        WHERE users.role_id = 2 AND users.status = 1 AND users.is_activated = 1
				    	GROUP BY of.subject_id
				    ) AS user_counts
					ON user_counts.subject_id = subjects.id

					#total keywords
					LEFT JOIN
					(
				    SELECT
				        of.item_id,
				        COUNT(DISTINCT(of.id)) AS total_keywords
				        FROM keywords of
				        WHERE of.type = 'subject'
				    	GROUP BY of.item_id
				    ) AS keyword_counts
					ON keyword_counts.item_id = subjects.id

					#loc total
					LEFT JOIN
					(
				    SELECT
				        of.subject_id,
				        COUNT(DISTINCT(of.id)) AS total_location
				        FROM location_subjects of
				    	GROUP BY of.subject_id
				    ) AS location_counts
					ON location_counts.subject_id = subjects.id

					#org total
					LEFT JOIN
					(
				    SELECT
				        of.subject_id,
				        COUNT(DISTINCT(of.id)) AS total_org
				        FROM organization_subjects of
				    	GROUP BY of.subject_id
				    ) AS org_counts
					ON org_counts.subject_id = subjects.id

					#dept total
					LEFT JOIN
					(
				    SELECT
				        of.subject_id,
				        COUNT(DISTINCT(of.id)) AS total_dept
				        FROM department_subjects of
				    	GROUP BY of.subject_id
				    ) AS dept_counts
					ON dept_counts.subject_id = subjects.id

					#story total
					LEFT JOIN
					(
				    SELECT
				        of.subject_id,
				        COUNT(DISTINCT(of.id)) AS total_stories
				        FROM story_subjects of
				    	GROUP BY of.subject_id
				    ) AS story_counts
					ON story_counts.subject_id = subjects.id

					WHERE $filter_query

				group by subjects.id
				$order $limit_query";
		$subjectsAll = $this->Subject->query($query);
		if (isset($subjectsAll) && !empty($subjectsAll)) {
			$data = $subjectsAll;
		}
		return $data;
	}

	protected function find_domains($filter = null, $page = null, $sorting = array()){
		$limit_query = ' LIMIT '.$this->offset;
		if(isset($page) && !empty($page)){
			$limit_query = " LIMIT $page, ".$this->offset;
		}
		$ser = '^';
		$filter_query = ' 1';
		if(isset($filter) && !empty($filter)){
			//$search_str = htmlentities($filter, ENT_QUOTES);
			//$filter_query = " knowledge_domains.title LIKE '%$search_str%'";

			//$search_str= Sanitize::escape(like(strtolower($filter), $ser ));

			$search_str= Sanitize::escape(like($filter, $ser ));

			$filter_query = " (knowledge_domains.title LIKE '%$search_str%' ESCAPE '$ser') ";

		}

		$order = 'ORDER BY title ASC';
		if( isset($sorting) && !empty($sorting) ){

			if( isset($sorting['coloumn']) && !empty($sorting['coloumn']) && isset($sorting['order']) && !empty($sorting['order']) ){
				$order = "ORDER BY ".$sorting['coloumn']." ".$sorting['order'];
			}
		}

		$data = [];
		$query ="SELECT
				knowledge_domains.*,CONCAT_WS(' ', user_details.first_name , user_details.last_name) as fullname,
					link_counts.total_links AS linktotal,
					file_counts.total_files AS filetotal,
					user_counts.total_people AS totalpeople,
					keyword_counts.total_keywords AS totalkeyword,
					location_counts.total_location AS totallocation,
					org_counts.total_org AS totalorganization,
					dept_counts.total_dept AS totaldepartment,
					story_counts.total_stories AS total_story

				FROM knowledge_domains
					LEFT JOIN user_details on
						user_details.user_id = knowledge_domains.modified_by


					#link total
					LEFT JOIN
					(
				    SELECT
				        ol.domain_id,
				        COUNT(DISTINCT(ol.id)) AS total_links
				        FROM domain_links ol
				    	GROUP BY ol.domain_id
				     ) AS link_counts
					ON link_counts.domain_id = knowledge_domains.id

					#file total
					LEFT JOIN
					(
				    SELECT
				        of.domain_id,
				        COUNT(DISTINCT(of.id)) AS total_files
				        FROM domain_files of
				        WHERE of.file_name != '' and of.file_name IS NOT NULL AND of.file_name != ''
				    	GROUP BY of.domain_id
				    ) AS file_counts
					ON file_counts.domain_id = knowledge_domains.id

					#people total
					LEFT JOIN
					(
				    SELECT
				        of.domain_id,
				        COUNT(DISTINCT(of.id)) AS total_people
				        FROM user_domains of
				        INNER JOIN users ON users.id = of.user_id
				        WHERE users.role_id = 2 AND users.status = 1 AND users.is_activated = 1
				    	GROUP BY of.domain_id
				    ) AS user_counts
					ON user_counts.domain_id = knowledge_domains.id

					#total keywords
					LEFT JOIN
					(
				    SELECT
				        of.item_id,
				        COUNT(DISTINCT(of.id)) AS total_keywords
				        FROM keywords of
				        WHERE of.type = 'domain'
				    	GROUP BY of.item_id
				    ) AS keyword_counts
					ON keyword_counts.item_id = knowledge_domains.id

					#loc total
					LEFT JOIN
					(
				    SELECT
				        of.domain_id,
				        COUNT(DISTINCT(of.id)) AS total_location
				        FROM location_domains of
				    	GROUP BY of.domain_id
				    ) AS location_counts
					ON location_counts.domain_id = knowledge_domains.id

					#org total
					LEFT JOIN
					(
				    SELECT
				        of.domain_id,
				        COUNT(DISTINCT(of.id)) AS total_org
				        FROM organization_domains of
				    	GROUP BY of.domain_id
				    ) AS org_counts
					ON org_counts.domain_id = knowledge_domains.id

					#dept total
					LEFT JOIN
					(
				    SELECT
				        of.domain_id,
				        COUNT(DISTINCT(of.id)) AS total_dept
				        FROM department_domains of
				    	GROUP BY of.domain_id
				    ) AS dept_counts
					ON dept_counts.domain_id = knowledge_domains.id

					#story total
					LEFT JOIN
					(
				    SELECT
				        of.domain_id,
				        COUNT(DISTINCT(of.id)) AS total_stories
				        FROM story_domains of
				    	GROUP BY of.domain_id
				    ) AS story_counts
					ON story_counts.domain_id = knowledge_domains.id


					LEFT JOIN domain_links on
						domain_links.domain_id = knowledge_domains.id
					LEFT JOIN domain_files on
						domain_files.domain_id = knowledge_domains.id and domain_files.file_name IS NOT NULL AND domain_files.file_name != ''
					LEFT JOIN user_domains on
						user_domains.domain_id = knowledge_domains.id
					LEFT JOIN keywords on
							keywords.item_id = knowledge_domains.id and keywords.type = 'domain'
					LEFT JOIN location_domains ON
						location_domains.domain_id = knowledge_domains.id
					LEFT JOIN organization_domains ON
						organization_domains.domain_id = knowledge_domains.id
					LEFT JOIN department_domains ON
						department_domains.domain_id = knowledge_domains.id

					WHERE $filter_query
				group by knowledge_domains.id
				$order $limit_query";
				//echo $query;
		$domainAll = $this->KnowledgeDomain->query($query);
		if (isset($domainAll) && !empty($domainAll)) {
			$data = $domainAll;
		}
		return $data;
	}

	public function get_skills(){
		if ($this->request->isAjax()) {
            $this->layout = false;

            $title = (isset($this->request->data['title']) && !empty($this->request->data['title'])) ? $this->request->data['title'] : "";

			$data['list_data'] = $this->find_skills($title);

			$view = new View($this, false);
			$view->viewPath = 'Competencies/partials/skills';
			$view->set($data);
			$html = $view->render('get_skills');

			echo json_encode($html);
			exit();
		}
	}

	public function get_subjects(){
		if ($this->request->isAjax()) {
            $this->layout = false;

            $title = (isset($this->request->data['title']) && !empty($this->request->data['title'])) ? $this->request->data['title'] : "";

			$data['list_data'] = $this->find_subjects($title);
			$view = new View($this, false);
			$view->viewPath = 'Competencies/partials/subjects';
			$view->set($data);
			$html = $view->render('get_subjects');

			echo json_encode($html);
			exit();
		}
	}

	public function get_domains(){
		if ($this->request->isAjax()) {
            $this->layout = false;

            $title = (isset($this->request->data['title']) && !empty($this->request->data['title'])) ? $this->request->data['title'] : "";

			$data['list_data'] = $this->find_domains($title);
			$view = new View($this, false);
			$view->viewPath = 'Competencies/partials/domains';
			$view->set($data);
			$html = $view->render('get_domains');

			echo json_encode($html);
			exit();
		}
	}


	public function filter_data(){
		if ($this->request->isAjax()) {
            $this->layout = false;

			$view = new View($this, false);


            if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;

				$filter = (isset($post['q']) && strlen($post['q']) > 0 ) ? $post['q'] : '';
				$type = $post['type'];

				$sorting = array();
				$sorting['order'] = 'asc';
				$sorting['coloumn'] = 'title';
				if( isset($post['order']) && !empty($post['order']) ){
					$sorting['order'] = $post['order'];
				}
				if( isset($post['coloumn']) && !empty($post['coloumn']) ){
					$sorting['coloumn'] = $post['coloumn'];
				}

				if($type == 'skills'){
					$data['list_data'] = $this->find_skills($filter, '', $sorting);
					$view->viewPath = 'Competencies/partials/skills';
					$view->set($data);
					$html = $view->render('get_skills');
				}
				else if($type == 'subjects'){
					$data['list_data'] = $this->find_subjects($filter, '', $sorting);
					$view->viewPath = 'Competencies/partials/subjects';
					$view->set($data);
					$html = $view->render('get_subjects');
				}
				else if($type == 'domains'){
					$data['list_data'] = $this->find_domains($filter, '', $sorting);
					$view->viewPath = 'Competencies/partials/domains';
					$view->set($data);
					$html = $view->render('get_domains');
				}

			}

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

				if($type == 'skills'){
					$data['list_data'] = $this->find_skills($q,$page,$sorting);
					$view->viewPath = 'Competencies/partials/skills';
					$view->set($data);
					$html = $view->render('get_skills');
				}
				else if($type == 'subjects'){
					$data['list_data'] = $this->find_subjects($q,$page,$sorting);
					$view->viewPath = 'Competencies/partials/subjects';
					$view->set($data);
					$html = $view->render('get_subjects');
				}
				else if($type == 'domains'){
					$data['list_data'] = $this->find_domains($q,$page,$sorting);
					$view->viewPath = 'Competencies/partials/domains';
					$view->set($data);
					$html = $view->render('get_domains');
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

				$filter_query = ' 1';
				if(isset($filter) && !empty($filter)){
					$search_str = Sanitize::clean($filter, array('encode' => true));
					$search_str = addslashes($filter);
					$filter_query = " title LIKE '%$search_str%'";
				}

				$order_qry = 'ORDER BY title ASC';
				if( isset($coloumn) && !empty($coloumn) ){

					if( isset($coloumn) && !empty($coloumn) && isset($order) && !empty($order) ){
						$order_qry = "ORDER BY ".$coloumn." ".$order;
					}

				}
				if($type == 'skills'){


					$query ="SELECT
								skills.*, CONCAT_WS(' ', user_details.first_name , user_details.last_name) as fullname,

				                count(distinct(skill_links.id)) as linktotal,
								count(distinct(skill_files.id)) as filetotal,
								count(distinct(user_skills.user_id)) as totalpeople

								FROM skills
									LEFT JOIN user_details on
										user_details.user_id = skills.modified_by
									LEFT JOIN skill_links on
										skill_links.skill_id = skills.id
									LEFT JOIN skill_files on
										skill_files.skill_id = skills.id
									LEFT JOIN user_skills on
										user_skills.skill_id = skills.id

								WHERE $filter_query

								group by skills.id
								$order_qry";

					$skillsAll = $this->Skill->query($query);
					if (isset($skillsAll) && !empty($skillsAll)) {
						$data = $skillsAll;
					}
				}
				else if($type == 'subjects'){
					$query ="SELECT
							subjects.*,CONCAT_WS(' ', user_details.first_name , user_details.last_name) as fullname,

			                count(distinct(subject_links.id)) as linktotal,
							count(distinct(subject_files.id)) as filetotal,
							count(distinct(user_subjects.user_id)) as totalpeople

							FROM subjects
								LEFT JOIN user_details on
									user_details.user_id = subjects.modified_by
								LEFT JOIN subject_links on
									subject_links.subject_id = subjects.id
								LEFT JOIN subject_files on
									subject_files.subject_id = subjects.id
								LEFT JOIN user_subjects on
									user_subjects.subject_id = subjects.id

							WHERE $filter_query

							group by subjects.id
							$order_qry";
					$subjectsAll = $this->Subject->query($query);
					if (isset($subjectsAll) && !empty($subjectsAll)) {
						$data = $subjectsAll;
					}
				}
				else if($type == 'domains'){
					$query ="SELECT
							knowledge_domains.*,CONCAT_WS(' ', user_details.first_name , user_details.last_name) as fullname,

							count(distinct(domain_links.id)) as linktotal,
							count(distinct(domain_files.id)) as filetotal,
							count(distinct(user_domains.user_id)) as totalpeople

							FROM knowledge_domains
								LEFT JOIN user_details on
									user_details.user_id = knowledge_domains.modified_by
								LEFT JOIN domain_links on
									domain_links.domain_id = knowledge_domains.id
								LEFT JOIN domain_files on
									domain_files.domain_id = knowledge_domains.id
								LEFT JOIN user_domains on
									user_domains.domain_id = knowledge_domains.id

							WHERE $filter_query

							group by knowledge_domains.id
							$order_qry";
					$domainAll = $this->KnowledgeDomain->query($query);
					if (isset($domainAll) && !empty($domainAll)) {
						$data = $domainAll;
					}
				}
				$count = count($data);
			}

			echo json_encode($count);
			exit;
		}
	}

	/**
     * Open Popup Modal Boxes method
		*
     * @return void
     */
    public function add_skills($skill_id = null) {

        if ($this->request->isAjax()) {
            $this->layout = 'ajax';

			if( isset($skill_id) && !empty($skill_id) ){

				$skill_data = $this->Skill->find('first', array('conditions'=> array('Skill.id'=>$skill_id) ) );
				$this->set('skill_data',$skill_data);

			}
			$response = ['success'=> true, 'content'=>null];
			$html = $this->render('partials/skills/add_skills');
			return $html;
			exit();
		}
	}

    public function edit_skills($skill_id = null) {

        if ($this->request->isAjax()) {
            $this->layout = 'ajax';


			$skill_data = $this->Skill->find('first', array('conditions'=> array('Skill.id'=>$skill_id) ) );
			$this->set('skill_data',$skill_data);

			$response = ['success'=> true, 'content'=>null];
			$html = $this->render('partials/skills/edit_skill');
			return $html;
			exit();
		}
	}


    public function edit_subjects($subject_id = null) {

        if ($this->request->isAjax()) {
            $this->layout = 'ajax';


			$subject_data = $this->Subject->find('first', array('conditions'=> array('Subject.id'=>$subject_id) ) );
			$this->set('subject_data',$subject_data);

			$response = ['success'=> true, 'content'=>null];
			$html = $this->render('partials/subjects/edit_subject');
			return $html;
			exit();
		}
	}

	public function add_subjects($subject_id = null) {

        if ($this->request->isAjax()) {
            $this->layout = 'ajax';

			if( isset($subject_id) && !empty($subject_id) ){

				$subject_data = $this->Subject->find('first', array('conditions'=> array('Subject.id'=>$subject_id) ) );
				$this->set('subject_data',$subject_data);

			}
			$response = ['success'=> true, 'content'=>null];
			$html = $this->render('partials/subjects/add_subjects');
			return $html;
			exit();
		}
	}

	public function add_domains($domain_id = null) {

        if ($this->request->isAjax()) {
            $this->layout = 'ajax';

			if( isset($domain_id) && !empty($domain_id) ){

				$domain_data = $this->KnowledgeDomain->find('first', array('conditions'=> array('KnowledgeDomain.id'=>$domain_id) ) );
				$this->set('domain_data',$domain_data);

			}
			$response = ['success'=> true, 'content'=>null];
			$html = $this->render('partials/domains/add_domains');
			return $html;
			exit();
		}
	}

	public function edit_domains($domain_id = null) {

        if ($this->request->isAjax()) {
            $this->layout = 'ajax';

			if( isset($domain_id) && !empty($domain_id) ){

				$domain_data = $this->KnowledgeDomain->find('first', array('conditions'=> array('KnowledgeDomain.id'=>$domain_id) ) );
				$this->set('domain_data',$domain_data);

			}
			$response = ['success'=> true, 'content'=>null];
			$html = $this->render('partials/domains/edit_domain');
			return $html;
			exit();
		}
	}

    public function view_skills($id = null) {
        if ($this->request->isAjax()) {
            $this->layout = 'ajax';
			$response = ['success'=> true, 'content'=>null];

			$query ="SELECT
						skills.*, CONCAT_WS(' ', user_details.first_name , user_details.last_name) as updatedby,
		                count(distinct(skill_links.id)) as linktotal,
						count(distinct(skill_files.id)) as filetotal,
						count(distinct(user_skills.user_id)) as totalpeople,
						count(distinct(location_skills.id)) as totallocation,

		(SELECT JSON_ARRAYAGG(JSON_OBJECT( 'skill_id',skill_links.skill_id,'id',skill_links.id,'link_name',skill_links.link_name,'web_link',skill_links.web_link )) AS JSON FROM `skill_links` WHERE skill_links.skill_id = skills.id ) as skill_links,

		(SELECT JSON_ARRAYAGG(JSON_OBJECT( 'skill_id',skill_files.skill_id,'id',skill_files.id,'file_name',skill_files.file_name,'upload_file',skill_files.upload_file )) AS JSON FROM `skill_files` WHERE skill_files.skill_id = skills.id ) as skill_files,

		( SELECT JSON_ARRAYAGG(JSON_OBJECT( 'full_name',CONCAT_WS(' ',user_details.first_name , user_details.last_name), 'job_role',user_details.job_title, 'user_id',user_details.user_id, 'profile_pic',user_details.profile_pic, 'organization',user_details.organization_id )) AS JSON FROM `users` inner join user_details on user_details.user_id = users.id  WHERE users.id IN (select user_skills.user_id from user_skills where user_skills.skill_id = $id ) AND users.role_id = 2 AND users.status = 1 AND users.is_activated = 1 ) as user_detail,

		(SELECT JSON_ARRAYAGG(JSON_OBJECT( 'id', locations.id, 'location_name', locations.name )) AS JSON FROM `locations` WHERE locations.id IN ( select location_skills.location_id from location_skills where location_skills.skill_id = skills.id  )  ) as location_skill,

		(SELECT JSON_ARRAYAGG(JSON_OBJECT( 'id',departments.id,'dept_name',departments.name,'dept_image',departments.image )) AS JSON FROM departments WHERE departments.id IN ( select department_skills.department_id from department_skills where department_skills.skill_id = skills.id  ) ORDER BY departments.name ASC  ) as department_skill,

		( SELECT JSON_ARRAYAGG(JSON_OBJECT( 'id', organizations.id, 'name', organizations.name, 'image', organizations.image, 'type', organization_types.type))  AS JSON
			FROM skills
			inner join organization_skills on organization_skills.skill_id = skills.id
			inner join organizations on organizations.id = organization_skills.organization_id
			inner join organization_types on organizations.type_id = organization_types.id
			WHERE skills.id = $id ) as all_org,

		( SELECT JSON_ARRAYAGG(JSON_OBJECT( 'id', stories.id, 'name', stories.name, 'image', stories.image, 'type', story_types.type))  AS JSON
			FROM skills
			inner join story_skills on story_skills.skill_id = skills.id
			inner join stories on stories.id = story_skills.story_id
			inner join story_types on stories.type_id = story_types.id
			WHERE skills.id = $id ) as all_stories

		FROM skills
			LEFT JOIN user_details on
				user_details.user_id = skills.modified_by
			LEFT JOIN skill_links on
				skill_links.skill_id = skills.id
			LEFT JOIN skill_files on
				skill_files.skill_id = skills.id and skill_files.file_name IS NOT NULL AND skill_files.file_name != ''
			LEFT JOIN user_skills on
				user_skills.skill_id = skills.id
			LEFT JOIN location_skills on
				location_skills.skill_id = skills.id

		WHERE skills.id = $id

		group by skills.id
		ORDER BY title ASC";

			$data = $this->Skill->query($query);


			$task_data = [
				'skill_id' => $id,
				'updated_user_id' => $this->user_id,
				'message' => 'Skill viewed',
				'updated' => date("Y-m-d H:i:s"),
			];

			$this->loadModel('SkillActivity');
			$this->SkillActivity->id = null;
			$this->SkillActivity->save($task_data);


			$this->set('data', $data);
			$html = $this->render('partials/skills/view_skills');
			return $html;
			exit();

		}
	}


	public function view_subjects($id = null) {

        if ($this->request->isAjax()) {
            $this->layout = 'ajax';
			$response = ['success'=> true, 'content'=>null];

			$query ="SELECT
						subjects.*,
						CONCAT_WS(' ', user_details.first_name , user_details.last_name) as updatedby,
		                count(distinct(subject_links.id)) as linktotal,
						count(distinct(subject_files.id)) as filetotal,
						count(distinct(user_subjects.user_id)) as totalpeople,
						count(distinct(location_subjects.id)) as totallocation,

			(SELECT JSON_ARRAYAGG(JSON_OBJECT( 'subject_id',subject_links.subject_id,'id',subject_links.id,'link_name',subject_links.link_name,'web_link',subject_links.web_link )) AS JSON FROM `subject_links` WHERE subject_links.subject_id = subjects.id ) as subject_links,

			(SELECT JSON_ARRAYAGG(JSON_OBJECT( 'subject_id',subject_files.subject_id,'id',subject_files.id,'file_name',subject_files.file_name,'upload_file',subject_files.upload_file )) AS JSON FROM `subject_files` WHERE subject_files.subject_id = subjects.id ) as subject_files,

			( SELECT JSON_ARRAYAGG(JSON_OBJECT( 'full_name',CONCAT_WS(' ',user_details.first_name , user_details.last_name), 'job_role',user_details.job_title, 'user_id',user_details.user_id, 'profile_pic',user_details.profile_pic, 'organization',user_details.organization_id )) AS JSON FROM `users` inner join user_details on user_details.user_id = users.id  WHERE users.id IN (select user_subjects.user_id from user_subjects where user_subjects.subject_id = subjects.id ) AND users.role_id = 2 AND users.status = 1 AND users.is_activated = 1 ) as user_detail,

			(SELECT JSON_ARRAYAGG(JSON_OBJECT( 'id',locations.id,'location_name',locations.name )) AS JSON FROM `locations` WHERE locations.id IN ( select location_subjects.location_id from location_subjects where location_subjects.subject_id = subjects.id  )  ) as location_subject,

		(SELECT JSON_ARRAYAGG(JSON_OBJECT( 'id',departments.id,'dept_name',departments.name,'dept_image',departments.image )) AS JSON FROM departments WHERE departments.id IN ( select department_subjects.department_id from department_subjects where department_subjects.subject_id = subjects.id  ) ORDER BY departments.name ASC  ) as department_sub,

			( SELECT JSON_ARRAYAGG(JSON_OBJECT( 'id', organizations.id, 'name', organizations.name, 'image', organizations.image, 'type', organization_types.type)) AS JSON
				FROM subjects
				inner join organization_subjects on organization_subjects.subject_id = subjects.id
				inner join organizations on organizations.id = organization_subjects.organization_id
				inner join organization_types on organizations.type_id = organization_types.id
				WHERE subjects.id = $id ORDER BY organizations.name ASC ) as all_org,

		( SELECT JSON_ARRAYAGG(JSON_OBJECT( 'id', stories.id, 'name', stories.name, 'image', stories.image, 'type', story_types.type))  AS JSON
			FROM subjects
			inner join story_subjects on story_subjects.subject_id = subjects.id
			inner join stories on stories.id = story_subjects.story_id
			inner join story_types on stories.type_id = story_types.id
			WHERE subjects.id = $id ) as all_stories


			FROM subjects
				LEFT JOIN user_details on
					user_details.user_id = subjects.modified_by
				LEFT JOIN subject_links on
					subject_links.subject_id = subjects.id
				LEFT JOIN subject_files on
					subject_files.subject_id = subjects.id and subject_files.file_name IS NOT NULL AND subject_files.file_name != ''
				LEFT JOIN user_subjects on
					user_subjects.subject_id = subjects.id
				LEFT JOIN location_subjects on
						location_subjects.subject_id = subjects.id

			WHERE subjects.id = $id

			group by subjects.id
			ORDER BY title ASC";

			$data = $this->Subject->query($query);

			$task_data = [
				'subject_id' => $id,
				'updated_user_id' => $this->user_id,
				'message' => 'Subject viewed',
				'updated' => date("Y-m-d H:i:s"),
			];

			$this->loadModel('SubjectActivity');
			$this->SubjectActivity->id = null;
			$this->SubjectActivity->save($task_data);

			$this->set('data', $data);
			$html = $this->render('partials/subjects/view_subjects');
			return $html;
			exit();

		}
	}

	public function view_domains($id = null) {

        if ($this->request->isAjax()) {
            $this->layout = 'ajax';
			$response = ['success'=> true, 'content'=>null];

			$query ="SELECT
								knowledge_domains.*, CONCAT_WS(' ', user_details.first_name , user_details.last_name) as updatedby,
				                #link_counts.total_links as linktotal,
								#file_counts.total_files as filetotal,
								#user_counts.total_people as totalpeople,
								#location_counts.total_location as totallocation,

				                count(distinct(domain_links.id)) as linktotal,
								count(distinct(domain_files.id) ) as filetotal,
								count(distinct(user_domains.user_id)) as totalpeople,
								count(distinct(location_domains.id)) as totallocation,

		(SELECT JSON_ARRAYAGG(JSON_OBJECT( 'domain_id',domain_links.domain_id,'id',domain_links.id,'link_name',domain_links.link_name,'web_link',domain_links.web_link )) AS JSON FROM `domain_links` WHERE domain_links.domain_id = knowledge_domains.id ) as domain_links,

		(SELECT JSON_ARRAYAGG(JSON_OBJECT( 'domain_id',domain_files.domain_id,'id',domain_files.id,'file_name',domain_files.file_name,'upload_file',domain_files.upload_file )) AS JSON FROM `domain_files` WHERE domain_files.domain_id = knowledge_domains.id and domain_files.file_name IS NOT NULL AND domain_files.file_name != '' ) as domain_files,

		( SELECT JSON_ARRAYAGG(JSON_OBJECT( 'full_name',CONCAT_WS(' ',user_details.first_name , user_details.last_name), 'job_role',user_details.job_title, 'user_id',user_details.user_id, 'profile_pic',user_details.profile_pic, 'organization', user_details.organization_id )) AS JSON FROM `users` inner join user_details on user_details.user_id = users.id  WHERE users.id IN (select user_domains.user_id from user_domains where user_domains.domain_id = knowledge_domains.id ) AND users.role_id = 2 AND users.status = 1 AND users.is_activated = 1 ) as user_detail,

		(SELECT JSON_ARRAYAGG(JSON_OBJECT( 'id',locations.id,'location_name',locations.name )) AS JSON FROM `locations` WHERE locations.id IN ( select location_domains.location_id from location_domains where location_domains.domain_id = knowledge_domains.id  )  ) as location_domain,

		(SELECT JSON_ARRAYAGG(JSON_OBJECT( 'id',departments.id,'dept_name',departments.name,'dept_image',departments.image )) AS JSON FROM departments WHERE departments.id IN ( select department_domains.department_id from department_domains where department_domains.domain_id = knowledge_domains.id  ) ORDER BY departments.name ASC  ) as department_domain,

			( SELECT JSON_ARRAYAGG(JSON_OBJECT( 'id', organizations.id, 'name', organizations.name, 'image', organizations.image, 'type', organization_types.type)) AS JSON
				FROM knowledge_domains
				inner join organization_domains on organization_domains.domain_id = knowledge_domains.id
				inner join organizations on organizations.id = organization_domains.organization_id
				inner join organization_types on organizations.type_id = organization_types.id
				WHERE knowledge_domains.id = $id ORDER BY organizations.name ASC ) as all_org,

		( SELECT JSON_ARRAYAGG(JSON_OBJECT( 'id', stories.id, 'name', stories.name, 'image', stories.image, 'type', story_types.type))  AS JSON
			FROM knowledge_domains
			inner join story_domains on story_domains.domain_id = knowledge_domains.id
			inner join stories on stories.id = story_domains.story_id
			inner join story_types on stories.type_id = story_types.id
			WHERE knowledge_domains.id = $id ) as all_stories


								FROM knowledge_domains
									LEFT JOIN user_details on
										user_details.user_id = knowledge_domains.modified_by
									LEFT JOIN domain_links on
										domain_links.domain_id = knowledge_domains.id
									LEFT JOIN domain_files on
										domain_files.domain_id = knowledge_domains.id and domain_files.file_name IS NOT NULL AND domain_files.file_name != ''
									LEFT JOIN user_domains on
										user_domains.domain_id = knowledge_domains.id
									LEFT JOIN location_domains on
										location_domains.domain_id = knowledge_domains.id

								WHERE knowledge_domains.id = $id

								group by knowledge_domains.id
								ORDER BY title ASC";

			$data = $this->KnowledgeDomain->query($query);

			$task_data = [
				'domain_id' => $id,
				'updated_user_id' => $this->user_id,
				'message' => 'Domain viewed',
				'updated' => date("Y-m-d H:i:s"),
			];

			$this->loadModel('DomainActivity');
			$this->DomainActivity->id = null;
			$this->DomainActivity->save($task_data);


			$this->set('data', $data);
			$html = $this->render('partials/domains/view_domains');
			return $html;
			exit();

		}
	}

	/************ Skill functions **************************/

	public function manage_skills(){

		if ($this->request->isAjax()) {

            $this->layout = 'ajax';
			$this->autoRender = false;
			$response = [
				'success' => false,
				'msg' => '',
				'content' => null,
				'sub_type' => 'add',
				'image' => '',

			];

			if ($this->request->is('post') || $this->request->is('put')) {
				$check_file = 1;

				if (empty($this->request->data['Skill']['id'])) {
					$this->request->data['Skill']['created_by'] = $this->Auth->user('id');
					$this->request->data['Skill']['modified_by'] = $this->Auth->user('id');
				} else {
					$this->request->data['Skill']['modified_by'] = $this->Auth->user('id');
				}

				if (isset($this->request->data['Skill']['id']) && empty($this->request->data['Skill']['id'])) {
					if (isset($this->request->data['extension_valid']) && empty($this->request->data['extension_valid'])) {

						$check_file = ($this->request->data['extension_valid'] > 1) ? 1 : 2;
					}
				} else {
					if (isset($this->request->data['extension_valid']) && empty($this->request->data['extension_valid'])) {

						$check_file = ($this->request->data['extension_valid'] > 1) ? 1 : 2;
					}
				}

				$sizeLimit = 5 * 1024 * 1024; // 5MB
				$folder_url = WWW_ROOT . SKILL_IMAGE_PATH;
				$result = $fileNewName = $upload_object = $upload_detail = null;


				if ($check_file == true && isset($this->request->data['Skill']['image']) && !empty($this->request->data['Skill']['image']) ) {
					$upload_object = (isset($this->request->data['Skill']['image'])) ? $this->request->data['Skill']['image'] : null;

					if ($upload_object) {
						if (!file_exists($folder_url)) {
							mkdir($folder_url, 0777, true);
						}

						$sizeMB = 0;
						$sizeStr = "";
						$sizeKB = $upload_object['size'] / 1024;
						if (($sizeKB) > 1024) {
							$sizeMB = $sizeKB / 1024;
							$sizeStr = number_format($sizeMB, 2) . " MB";
						} else {
							$sizeStr = number_format($sizeKB, 2) . " KB";
						}
						$fileExt = substr($upload_object['name'], strripos($upload_object['name'], '.') + 1);
						$fileName = substr($upload_object['name'], 0, strripos($upload_object['name'], '.'));

						if ($sizeMB <= $sizeLimit) {

							if (!is_writable($folder_url)) {
								$result = array(
									'error' => "Server error. Upload directory isn't writable. Please ask server admin to change permissions.",
								);
							}

							// if file exists, change file name with the saved entry of the record id
							$orgFileName = $upload_object['name'];
							$exists_file = $folder_url . DS . $orgFileName;

							$fileNewName = $orgFileName;
							if (!empty($fileNewName)) {

								$tempFile = $upload_object['tmp_name'];

								$unique_file_name = $this->unique_file_name($folder_url,$fileNewName);
								$targetFile = $folder_url . DS . $unique_file_name;
								$fileSize = true; // filesize($tempFile);

								if (!$fileSize) {
									$result = array(
										'error' => 'File is too large. Please ask server admin to increase the file upload limit.',
									);
								}
								if (empty($result)) {
									move_uploaded_file($tempFile, $targetFile);
								}

								$upload_detail['name'] = $unique_file_name;
								$upload_detail['type'] = $upload_object['type'];
								$upload_detail['size'] = $sizeStr;
							}
						} else {

							$check_file = false;
							$response['msg'] = "File size limit exceeded,Please upload a file upto 5MB.";
						}
					}
				}

				if (isset($upload_detail) && !empty($upload_detail)) {
					$this->request->data['Skill']['image'] = $upload_detail['name'];
				} else {
					unset($this->request->data['Skill']['image']);
					unset($this->request->data['Skill']['file_name']);
					unset($this->request->data['Skill']['file_size']);
					unset($this->request->data['Skill']['file_type']);
				}

				//if (isset($check_file) && !empty($check_file) && $check_file == 1 && isset($this->request->data['Skill']['title']) && !empty($this->request->data['Skill']['title']) ) {


				//pr($this->request->data['Skill']); die;
				if ( isset($this->request->data['Skill']['title']) && !empty($this->request->data['Skill']['title']) ) {

					$this->Skill->set($this->request->data['Skill']);

					if ($this->Skill->validates()) {

						if ($this->Skill->save($this->request->data['Skill'])) {

							if( isset($this->request->data['Skill']) && !empty($this->request->data['Skill']) ){
								$skill_id = $this->request->data['Skill']['id'];
							} else {
								$skill_id = $this->Skill->getLastInsertId();
							}

							 if( isset($this->request->data['Skill']['id']) && !empty($this->request->data['Skill']['id']) ) {
								$skill_id = $this->request->data['Skill']['id'];
								$response['sub_type'] = 'update';

								if( isset($this->request->data['Skill']['image']) && !empty($this->request->data['Skill']['image']) ){
									$response['image'] = $this->request->data['Skill']['image'];
								}

							 } else {
								$skill_id = $this->Skill->getLastInsertId();
								$response['sub_type'] = 'add';
							 }


							$response['success'] = true;
							$response['msg'] = "Success";

							$response['content']['last_id'] = $skill_id;

						} else {
							$response['msg'] = "Error!!!";
						}
					} else {
						$response['content'] = $this->validateErrors($this->Skill);
					}
				} else {
					$response['msg'] = "Title is required.";
					$response['content'] = $this->validateErrors($this->Skill);

				}
			}

			echo json_encode($response);
			exit();

		}

	}

	public function manage_skill_links(){

		if ($this->request->isAjax()) {

            $this->layout = 'ajax';
			$this->autoRender = false;
			$response = [
				'success' => false,
				'msg' => '',
				'content' => null,
			];

			if ($this->request->is('post') || $this->request->is('put')) {

					$this->request->data['SkillLink']['user_id'] = $this->Auth->user('id');
					$this->request->data['SkillLink']['skill_id'] = $this->request->data['skill_id'];

					if ( $this->SkillLink->save($this->request->data) ) {

						$link_id = $this->SkillLink->getLastInsertId();


						$response = ['success' => true, 'msg' => "Skill Link successfully saved", 'content'=>$link_id];

					} else {

						$response['content'] = $this->validateErrors($this->SkillLink);

					}
			}

			echo json_encode($response);
			exit();

		}

	}

	public function manage_skill_files(){

		if ($this->request->isAjax()) {

            $this->layout = 'ajax';
			$this->autoRender = false;
			$response = [
				'success' => false,
				'msg' => '',
				'content' => null,
			];

		if ($this->request->is('post') || $this->request->is('put')) {
				$check_file = 1;


			if(isset($this->request->data['SkillFile']['skill_id']) && !empty($this->request->data['SkillFile']['skill_id'])) {

				if (isset($this->request->data['extension_valid']) && empty($this->request->data['extension_valid'])) {
					$check_file = ($this->request->data['extension_valid'] > 1) ? 1 : 2;
				}


				$sizeLimit = 5 * 1024 * 1024; // 5MB
				$folder_url = WWW_ROOT . SKILL_FILE_PATH;
				$result = $fileNewName = $upload_object = $upload_detail = null;

				if ($check_file == true && isset($this->request->data['SkillFile']['upload_file']['name']) && !empty($this->request->data['SkillFile']['upload_file']['name']) ) {
					$upload_object = (isset($this->request->data['SkillFile']['upload_file'])) ? $this->request->data['SkillFile']['upload_file'] : null;

					if ($upload_object) {
						if (!file_exists($folder_url)) {
							mkdir($folder_url, 0777, true);
						}

						$sizeMB = 0;
						$sizeStr = "";
						$sizeKB = $upload_object['size'] / 1024;
						if (($sizeKB) > 1024) {
							$sizeMB = $sizeKB / 1024;
							$sizeStr = number_format($sizeMB, 2) . " MB";
						} else {
							$sizeStr = number_format($sizeKB, 2) . " KB";
						}
						$fileExt = substr($upload_object['name'], strripos($upload_object['name'], '.') + 1);
						$fileName = substr($upload_object['name'], 0, strripos($upload_object['name'], '.'));

						if ($sizeMB <= $sizeLimit) {

							if (!is_writable($folder_url)) {
								$result = array(
									'error' => "Server error. Upload directory isn't writable. Please ask server admin to change permissions.",
								);
							}

							// if file exists, change file name with the saved entry of the record id
							$orgFileName = $upload_object['name'];
							$exists_file = $folder_url . DS . $orgFileName;


							$fileNewName = $orgFileName;
							if (!empty($fileNewName)) {

								$tempFile = $upload_object['tmp_name'];


								$unique_file_name = $this->unique_file_name($folder_url,$fileNewName);
								$targetFile = $folder_url . DS . $unique_file_name;

								//$targetFile = $folder_url . DS . $fileNewName;
								$fileSize = true; // filesize($tempFile);

								if (!$fileSize) {
									$result = array(
										'error' => 'File is too large. Please ask server admin to increase the file upload limit.',
									);
								}
								if (empty($result)) {
									move_uploaded_file($tempFile, $targetFile);
								}

								//$upload_detail['name'] = $fileNewName;
								$upload_detail['name'] = $unique_file_name;
								$upload_detail['type'] = $upload_object['type'];
								$upload_detail['size'] = $sizeStr;
							}
						} else {

							$check_file = false;
							$response['msg'] = "File size limit exceeded,Please upload a file upto 5MB.";
						}
					}
				}

				if (isset($upload_detail) && !empty($upload_detail)) {
					$this->request->data['SkillFile']['upload_file'] = $upload_detail['name'];
				} else {
					unset($this->request->data['SkillFile']['upload_file']);
					unset($this->request->data['SkillFile']['file_name']);
					unset($this->request->data['SkillFile']['file_size']);
					unset($this->request->data['SkillFile']['file_type']);
				}


				if (isset($check_file) && !empty($check_file) && $check_file == 1 && isset($this->request->data['SkillFile']['file_name']) && !empty($this->request->data['SkillFile']['file_name']) ) {

					//$this->Skill->set($this->request->data['Skill']);

					//if ($this->Skill->validates()) {

						$this->request->data['SkillFile']['user_id'] =  $this->Auth->user('id');

						if ($this->SkillFile->save($this->request->data['SkillFile'])) {
							$files_id = $this->SkillFile->getLastInsertId();
							$response['success'] = true;
							$response['msg'] = "Success";
							$response['content']['last_id'] = $files_id;

						} else {
							$response['msg'] = "Error!!!";
						}
					/* } else {
						$response['content'] = $this->validateErrors($this->SkillFile);
					} */
				} else {
					$response['msg'] = "File title is required.";
					$response['content'] = $this->validateErrors($this->SkillFile);
				}

				}
			}
			echo json_encode($response);
			exit();
		}
	}

	public function getSkillLinks($id = null){

		if ($this->request->isAjax()) {

            $this->layout = 'ajax';
			$this->autoRender = false;
			$response = [
				'success' => false,
				'content' => null,
				'model' => null,
			];

			if ($this->request->is('post') || $this->request->is('put')) {


				if( isset($this->request->data['id']) && !empty($this->request->data['id']) && !empty($this->request->data['dtype']) ){

					if( $this->request->data['dtype'] == 'subject' ){
						$model = 'SubjectLink';
						$model_id = 'subject_id';
					} else if( $this->request->data['dtype'] == 'domain' ){
						$model = 'DomainLink';
						$model_id = 'domain_id';
					} else {
						$model = 'SkillLink';
						$model_id = 'skill_id';
					}

					$listLink = $this->$model->find('all', array('conditions'=> array($model.'.'.$model_id=>$this->request->data['id']), 'order'=>'id desc' ) );


					if( isset($listLink) && !empty($listLink) ){
						foreach ($listLink as $key => $value) {
							$listLink[$key][$model]['link_name'] = htmlentities($value[$model]['link_name'], ENT_QUOTES, "UTF-8");
						}
							// pr($listLink);

						$response['success'] = true;
						$response['content'] = $listLink;
						$response['model'] = $this->request->data['dtype'];

					} else {

						$response = [
							'success' => false,
							'content' => 'No Skill Links',
						];

					}


				}
				echo json_encode($response);
				exit();
			}

		}

	}

	public function getSkillFiles($skill_id = null){

		if ($this->request->isAjax()) {

            $this->layout = 'ajax';
			$this->autoRender = false;
			$response = [
				'success' => false,
				'content' => null,
				'model' => null,
			];

			if ($this->request->is('post') || $this->request->is('put')) {

				if( isset($this->request->data['id']) && !empty($this->request->data['id']) && !empty($this->request->data['dtype']) ){
					$no_msg = '';
					if( $this->request->data['dtype'] == 'subject' ){
						$model = 'SubjectFile';
						$model_id = 'subject_id';
						$no_msg = 'No Subject File';
					} else if( $this->request->data['dtype'] == 'domain' ){
						$model = 'DomainFile';
						$model_id = 'domain_id';
						$no_msg = 'No Domain File';
					} else {
						$model = 'SkillFile';
						$model_id = 'skill_id';
						$no_msg = 'No Skill File';
					}

					$listFile = $this->$model->find('all', array('conditions'=> array($model.'.'.$model_id=>$this->request->data['id']), 'order'=>'id desc' ) );


					if( isset($listFile) && !empty($listFile) ){
						foreach ($listFile as $key => $value) {
							$listFile[$key][$model]['file_name'] = htmlentities($value[$model]['file_name'], ENT_QUOTES, "UTF-8");
						}

						$response['success'] = true;
						$response['content'] = $listFile;
						$response['model'] = $this->request->data['dtype'];

					} else {

						$response = [
							'success' => false,
							'content' => $no_msg,
						];

					}


				}
				echo json_encode($response);
				exit();
			}

		}

	}

	public function getkeywords($id = null){

		if ($this->request->isAjax()) {

            $this->layout = 'ajax';
			$this->autoRender = false;
			$response = [
				'success' => false,
				'content' => null,
				'model' => null,
			];

			if ($this->request->is('post') || $this->request->is('put')) {


				if( isset($this->request->data['id']) && !empty($this->request->data['id']) && !empty($this->request->data['dtype']) ){

					$type = $this->request->data['dtype'];
					$item_id = $this->request->data['id'];
					$model = 'Keyword';

					$listKeyword = $this->$model->find('all', array('conditions'=> array($model.'.item_id'=>$this->request->data['id'], $model.'.type'=>$type ), 'order'=>['keyword asc'] ) );

					if( isset($listKeyword) && !empty($listKeyword) ){

						foreach ($listKeyword as $key => $value) {
							// pr($value);
							$listKeyword[$key][$model]['keyword'] = htmlentities($value[$model]['keyword'], ENT_QUOTES, "UTF-8");
						}

						$response['success'] = true;
						$response['content'] = $listKeyword;
						$response['model'] = $this->request->data['dtype'];

					} else {

						$response = [
							'success' => false,
							'content' => 'No Skill Keyword',
						];

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

				if(  isset($this->request->data['id']) && !empty($this->request->data['id']) && isset($this->request->data['dType']) ){

					$model = '';
					$unlikpath = '';
					if( $this->request->data['dType'] == 'subject' ){
						$model = 'Subject';
						$unlikpath = WWW_ROOT.SUBJECT_IMAGE_PATH;
					} else if( $this->request->data['dType'] == 'domain' ){
						$model = 'KnowledgeDomain';
						$unlikpath = WWW_ROOT.DOMAIN_IMAGE_PATH;
					} else {
						$model = 'Skill';
						$unlikpath = WWW_ROOT.SKILL_IMAGE_PATH;
					}
					$id = $this->request->data['id'];

					$fileData = $this->$model->find('first', array('conditions'=>array($model.'.id' => $id ) ) );
					$this->request->data[$model]['image'] = '';
					$this->request->data[$model]['id'] = $id;

					if( $this->$model->save($this->request->data) ){

						if( !empty($fileData) && isset($fileData[$model]['image']) && !empty($fileData[$model]['image']) ){
							$filename = $fileData[$model]['image'];
							if( file_exists($unlikpath.$filename) && !empty($filename) ){
								unlink($unlikpath.$filename);
							}
						}
						$response['success'] = true;
						$response['content'] = 'Success';

					} else {
						$response = [
							'success' => false,
							'content' => 'No Found',
						];
					}
				}
				echo json_encode($response);
				exit();
			}
		}
	}

	public function delete_keywords($keyword_id = null){

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$this->autoRender = false;
			$response = [
				'success' => false,
				'content' => null,
			];

			if ($this->request->is('post') || $this->request->is('put')) {

				if(  isset($this->request->data['item_id']) && !empty($this->request->data['item_id']) && isset($this->request->data['dType']) ){

					$model = 'Keyword';
					if( $this->request->data['dType'] == 'subject' ){
						$type = 'subject';
					} else if( $this->request->data['dType'] == 'domain' ){
						$type = 'domain';
					} else {
						$type = 'skill';
					}

					$item_id = $this->request->data['item_id'];
					if( $this->$model->delete($item_id) ){

						$response['success'] = true;
						$response['content'] = 'Success';

					} else {

						$response = [
							'success' => false,
							'content' => 'No Found',
						];

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



				if(  isset($this->request->data['link_id']) && !empty($this->request->data['link_id']) && isset($this->request->data['dType']) ){



					$model = '';
					if( $this->request->data['dType'] == 'subject' ){
						$model = 'SubjectLink';
					} else if( $this->request->data['dType'] == 'domain' ){
						$model = 'DomainLink';
					} else {
						$model = 'SkillLink';
					}

					$link_id = $this->request->data['link_id'];
					if( $this->$model->delete($link_id) ){

						$response['success'] = true;
						$response['content'] = 'Success';

					} else {

						$response = [
							'success' => false,
							'content' => 'No Found',
						];

					}

				}
				echo json_encode($response);
				exit();
			}

		}

	}

	public function delete_files($file_id = null){

		if ($this->request->isAjax()) {

            $this->layout = 'ajax';
			$this->autoRender = false;
			$response = [
				'success' => false,
				'content' => null,
			];

			if ($this->request->is('post') || $this->request->is('put')) {


				if( isset($this->request->data['file_id']) && !empty($this->request->data['file_id']) && isset($this->request->data['dType']) ){

					$model = '';
					if( $this->request->data['dType'] == 'subject' ){
						$model = 'SubjectFile';
						$unlikpath = WWW_ROOT.SUBJECT_FILE_PATH;
					} else if( $this->request->data['dType'] == 'domain' ){
						$model = 'DomainFile';
						$unlikpath = WWW_ROOT.DOMAIN_FILE_PATH;
					} else {
						$model = 'SkillFile';
						$unlikpath = WWW_ROOT.SKILL_FILE_PATH;
					}
					$file_id = $this->request->data['file_id'];
					$fileData = $this->$model->find('first', array('conditions'=>array($model.'.id' => $file_id ) ) );
					$unlikfile = '';
					if( isset($fileData) && !empty($fileData[$model]['upload_file']) ){
						$unlikfile = $fileData[$model]['upload_file'];
					}

					if( $this->$model->delete($file_id) ){
						if( file_exists($unlikpath.$unlikfile) && !empty($unlikfile) ){
							unlink($unlikpath.$unlikfile);
						}
						$response['success'] = true;
						$response['content'] = 'Success';

					} else {

						$response = [
							'success' => false,
							'content' => 'No Found',
						];

					}

				}
				echo json_encode($response);
				exit();
			}

		}

	}

	public function trash_skill($skill_id = null) {

		if ($this->request->isAjax()) {

			$this->layout = false;
			$viewData = null;
			$viewData['skill_id'] = $skill_id;
			$this->set($viewData);
			$this->render('partials/skills/trash_skill');

		}
	}

	public function delete_skill() {

		$this->autoRender = false;

		$response = ['success' => false];
		$this->layout = 'ajax';

		if ($this->request->is('ajax')) {
			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;
				$skill_id = $post['skill_id'];

				if (!empty($skill_id) && !empty($skill_id)) {


					/********************************************************/
						$model = 'Skill';
						$unlikpath = WWW_ROOT.SKILL_IMAGE_PATH;
						$unlikfilepath = WWW_ROOT.SKILL_FILE_PATH;

						$linkModel = 'SkillLink';
						$fileModel = 'SkillFile';
						$pdfModel = 'SkillPdf';
						$userModel = 'UserSkill';
						$detailModel = 'SkillDetail';
						$projectModel = 'ProjectSkill';
						$keywordModel = 'Keyword';

						$link_table = 'skill_links';
						$file_table = 'skill_files';
						$pdf_table = 'skill_pdfs';
						$user_table = 'user_skills';
						$detail_table = 'skill_details';
						$project_table = 'project_skills';

						$model_id = 'skill_id';


						$fileData = $this->$model->find('all', array('conditions'=>array($model.'.id' => $skill_id ) ) );
						$filesData = $this->$fileModel->find('all', array('conditions'=>array($fileModel.'.'.$model_id => $skill_id ) ) );
						$pdfData = $this->$pdfModel->find('all', array('conditions'=>array($pdfModel.'.'.$model_id => $skill_id ) ) );


					/********************************************************/


					if ($this->Skill->delete(['Skill.id' => $skill_id], false)) {

						if( isset($filesData) && !empty($filesData) ){
							foreach($filesData as $listfiles){
								if( file_exists($unlikfilepath.$listfiles[$fileModel]['upload_file'])  && !empty($listfiles[$fileModel]['upload_file']) ){
									unlink($unlikfilepath.$listfiles[$fileModel]['upload_file']);
								}
							}
						}
						if( isset($pdfData) && !empty($pdfData) ){
							foreach($pdfData as $listpdf){
								$user_id = $listpdf['SkillPdf']['user_id'];
								if (file_exists(WWW_ROOT.SKILL_PDF_PATH . $user_id . DS . $listpdf['SkillPdf']['pdf_name']) && !empty($listpdf['SkillPdf']['pdf_name']) ) {
									unlink(WWW_ROOT.SKILL_PDF_PATH . $user_id . DS . $listpdf['SkillPdf']['pdf_name']);
								}
							}
						}

						if( !empty($fileData) ){
								foreach($fileData as $listids){
								$filename = $listids[$model]['image'];
								if( file_exists($unlikpath.$filename) && !empty($filename) ){
									unlink($unlikpath.$filename);
								}
							}
						}

						$this->$linkModel->query("delete from ".$link_table." where $model_id = ".$skill_id );
						$this->$fileModel->query("delete from ".$file_table." where $model_id = ".$skill_id );
						$this->$pdfModel->query("delete from ".$pdf_table." where $model_id = ".$skill_id );
						$this->$userModel->query("delete from ".$user_table." where $model_id = ".$skill_id );
						$this->$detailModel->query("delete from ".$detail_table." where $model_id = ".$skill_id );
						$this->$projectModel->query("delete from ".$project_table." where $model_id = ".$skill_id );

						$this->Skill->query("delete from location_skills where skill_id = ".$skill_id );
						$this->Skill->query("delete from department_skills where skill_id = ".$skill_id );
						$this->Skill->query("delete from organization_skills where skill_id = ".$skill_id );

						$this->$keywordModel->query("delete from keywords where `type`='skill' and item_id = ".$skill_id );



						$response['success'] = true;
					}
				}
			}
		}

		echo json_encode($response);
		exit();
	}

	public function getcurrentrow(){


		if ($this->request->isAjax()) {

			$this->layout = false;
			$this->autoRender = false;
			$response = [
				'success' => false,
				'content' => null
			];

			$html = '';

			if ($this->request->is('post') || $this->request->is('put')) {


				if(  isset($this->request->data['id']) && !empty($this->request->data['id']) ){

					$id = $this->request->data['id'];
					if( isset($this->request->data['dtype']) && !empty($this->request->data['dtype']) ){

						$dataType = ( isset($this->request->data['dtype']) && !empty($this->request->data['dtype']) && $this->request->data['dtype'] == 'Domain' ) ? 'KnowledgeDomain' : $this->request->data['dtype'];

						$model = '';
						$renderfile = '';
						$links ='';
						$files ='';
						$main_users = '';
						$keyword_type = '';

						$locationtotal = '';
						$locationCond = '';

						if( $dataType == 'KnowledgeDomain' ){
							$model = 'knowledge_domains';
							$renderfile = 'getcurrentrowdomain';

							$links ='domain_links';
							$files ='domain_files';
							$main_users = 'user_domains';
							$model_id = 'domain_id';
							$keyword_type = 'domain';

							$locationtotal = ", count(distinct(location_domains.id)) as totallocation ";
							$locationCond = " LEFT JOIN location_domains on
									location_domains.domain_id = knowledge_domains.id ";

							$orgtotal = ", count(distinct(organization_domains.id)) as totalorganization ";
							$orgCond = " LEFT JOIN organization_domains on
									organization_domains.domain_id = knowledge_domains.id ";

							$depttotal = ", count(distinct(department_domains.id)) as totaldepartment ";
							$deptCond = " LEFT JOIN department_domains on
									department_domains.domain_id = knowledge_domains.id ";

							$storytotal = ", count(distinct(story_domains.id)) as total_stories ";
							$storyCond = " LEFT JOIN story_domains on story_domains.domain_id = knowledge_domains.id ";


						} else if( $dataType == 'Subject' ){
							$model = 'subjects';
							$renderfile = 'getcurrentrowsubject';

							$links ='subject_links';
							$files ='subject_files';
							$main_users = 'user_subjects';
							$model_id = 'subject_id';
							$keyword_type = 'subject';

							$locationtotal = ", count(distinct(location_subjects.id)) as totallocation ";
							$locationCond = " LEFT JOIN location_subjects on
									location_subjects.subject_id = subjects.id ";

							$orgtotal = ", count(distinct(organization_subjects.id)) as totalorganization ";
							$orgCond = " LEFT JOIN organization_subjects on
									organization_subjects.subject_id = subjects.id ";

							$depttotal = ", count(distinct(department_subjects.id)) as totaldepartment ";
							$deptCond = " LEFT JOIN department_subjects on department_subjects.subject_id = subjects.id ";

							$storytotal = ", count(distinct(story_subjects.id)) as total_stories ";
							$storyCond = " LEFT JOIN story_subjects on story_subjects.subject_id = subjects.id ";

						} else {
							$model = 'skills';
							$renderfile = 'getcurrentrow';
							$links ='skill_links';
							$files ='skill_files';
							$main_users = 'user_skills';
							$model_id = 'skill_id';
							$keyword_type = 'skill';

							$locationtotal = ", count(distinct(location_skills.id)) as totallocation ";
							$locationCond = " LEFT JOIN location_skills on location_skills.skill_id = skills.id ";

							$orgtotal = ", count(distinct(organization_skills.id)) as totalorganization ";
							$orgCond = " LEFT JOIN organization_skills on organization_skills.skill_id = skills.id ";

							$depttotal = ", count(distinct(department_skills.id)) as totaldepartment ";
							$deptCond = " LEFT JOIN department_skills on department_skills.skill_id = skills.id ";

							$storytotal = ", count(distinct(story_skills.id)) as total_stories ";
							$storyCond = " LEFT JOIN story_skills on story_skills.skill_id = skills.id ";

						}

							$query ="SELECT
										".$model.".*,CONCAT_WS(' ', user_details.first_name , user_details.last_name) as fullname,

										count(distinct(".$links.".id)) as linktotal,
										count(distinct(".$files.".id)) as filetotal,
										count(distinct(".$main_users.".user_id) ) as totalpeople,
										count(distinct(keywords.id)) as totalkeyword
										$locationtotal
										$orgtotal
										$depttotal
										$storytotal

										FROM ".$model."

											LEFT JOIN user_details on
												 user_details.user_id = ".$model.".modified_by
											LEFT JOIN ".$links." on
												".$links.".".$model_id." = ".$model.".id
											LEFT JOIN ".$files." on
												".$files.".".$model_id." = ".$model.".id
											LEFT JOIN ".$main_users." on
												".$main_users.".".$model_id." = ".$model.".id
											LEFT JOIN keywords on
												keywords.item_id = ".$model.".id and keywords.type='".$keyword_type."'
											$locationCond
											$orgCond
											$deptCond
											$storyCond

										WHERE ".$model.".id =".$id;

							$data = $this->$dataType->query($query);

							if( isset($data) && !empty($data[0][$model])  ){

								$response['success'] = true;
								$this->set($data);

								$view = new View($this, false);
								$view->viewPath = 'Competencies/partials';
								$view->set('data',$data);
								$response['content'] = $view->render($renderfile);

							}


					}
				}

			}
			echo json_encode($response);
			exit();
		}

	}

	public function download_files($id = null, $type = 'skill') {

		if (isset($id) && !empty($id)) {

			// Retrieve the file ready for download
			if( $type == 'subject' ){
				$model = 'SubjectFile';
				$path = SUBJECT_FILE_PATH;
			} else if( $type == 'domain' ){
				$model = 'DomainFile';
				$path = DOMAIN_FILE_PATH;
			} else {
				$model = 'SkillFile';
				$path = SKILL_FILE_PATH;
			}

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
				$response['content'] = $path.$data[$model]['upload_file'];
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
				$response['content'] = IMAGE_TEMP_PATH.$filename;
				$response['success'] = true;
			}
			$this->autoRender = false;
			return $this->response->file($response['content'], array('download' => true));
		}

	}

	/************************* Subject Functions ************** */


	public function manage_subjects(){

		if ($this->request->isAjax()) {

            $this->layout = 'ajax';
			$this->autoRender = false;
			$response = [
				'success' => false,
				'msg' => '',
				'content' => null,
				'sub_type' => 'add',
				'image' => '',
			];

			if ($this->request->is('post') || $this->request->is('put')) {
				$check_file = 1;

				//pr($this->request->data['Subject']);die;

				if (empty($this->request->data['Subject']['id'])) {
					$this->request->data['Subject']['created_by'] = $this->Auth->user('id');
					$this->request->data['Subject']['modified_by'] = $this->Auth->user('id');
				} else {
					$this->request->data['Subject']['modified_by'] = $this->Auth->user('id');
				}

				if (isset($this->request->data['Subject']['id']) && empty($this->request->data['Subject']['id'])) {
					if (isset($this->request->data['extension_valid']) && empty($this->request->data['extension_valid'])) {

						$check_file = ($this->request->data['extension_valid'] > 1) ? 1 : 2;
					}
				} else {
					if (isset($this->request->data['extension_valid']) && empty($this->request->data['extension_valid'])) {

						$check_file = ($this->request->data['extension_valid'] > 1) ? 1 : 2;
					}
				}



				$sizeLimit = 5 * 1024 * 1024; // 5MB
				$folder_url = WWW_ROOT . SUBJECT_IMAGE_PATH;
				$result = $fileNewName = $upload_object = $upload_detail = null;


				if ($check_file == true && isset($this->request->data['Subject']["image"]) && !empty($this->request->data['Subject']["image"]) ) {
					$upload_object = (isset($this->request->data['Subject']["image"])) ? $this->request->data['Subject']["image"] : null;

					if ($upload_object) {
						if (!file_exists($folder_url)) {
							mkdir($folder_url, 0777, true);
						}

						$sizeMB = 0;
						$sizeStr = "";
						$sizeKB = $upload_object['size'] / 1024;
						if (($sizeKB) > 1024) {
							$sizeMB = $sizeKB / 1024;
							$sizeStr = number_format($sizeMB, 2) . " MB";
						} else {
							$sizeStr = number_format($sizeKB, 2) . " KB";
						}
						$fileExt = substr($upload_object['name'], strripos($upload_object['name'], '.') + 1);
						$fileName = substr($upload_object['name'], 0, strripos($upload_object['name'], '.'));

						if ($sizeMB <= $sizeLimit) {

							if (!is_writable($folder_url)) {
								$result = array(
									'error' => "Server error. Upload directory isn't writable. Please ask server admin to change permissions.",
								);
							}

							// if file exists, change file name with the saved entry of the record id
							$orgFileName = $upload_object['name'];
							$exists_file = $folder_url . DS . $orgFileName;

							$fileNewName = $orgFileName;
							if (!empty($fileNewName)) {

								$tempFile = $upload_object['tmp_name'];

								$unique_file_name = $this->unique_file_name($folder_url,$fileNewName);
								$targetFile = $folder_url . DS . $unique_file_name;
								$fileSize = true; // filesize($tempFile);

								if (!$fileSize) {
									$result = array(
										'error' => 'File is too large. Please ask server admin to increase the file upload limit.',
									);
								}
								if (empty($result)) {
									move_uploaded_file($tempFile, $targetFile);
								}

								$upload_detail['name'] = $unique_file_name;
								$upload_detail['type'] = $upload_object['type'];
								$upload_detail['size'] = $sizeStr;
							}
						} else {

							$check_file = false;
							$response['msg'] = "File size limit exceeded,Please upload a file upto 5MB.";
						}
					}
				}

				if (isset($upload_detail) && !empty($upload_detail)) {
					$this->request->data['Subject']['image'] = $upload_detail['name'];
				} else {
					unset($this->request->data['Subject']['image']);
					unset($this->request->data['Subject']['file_name']);
					unset($this->request->data['Subject']['file_size']);
					unset($this->request->data['Subject']['file_type']);
				}

				if ( isset($this->request->data['Subject']['title']) && !empty($this->request->data['Subject']['title']) ) {

					$this->Subject->set($this->request->data['Subject']);

					if ( $this->Subject->validates() ) {

						if ($this->Subject->save($this->request->data['Subject'])) {

							if( isset($this->request->data['Subject']) && !empty($this->request->data['Subject']) ){
								$subject_id = $this->request->data['Subject']['id'];
							} else {
								$subject_id = $this->Subject->getLastInsertId();
							}

							 if( isset($this->request->data['Subject']['id']) && !empty($this->request->data['Subject']['id']) ) {
								$subject_id = $this->request->data['Subject']['id'];
								$response['sub_type'] = 'update';

								if( isset($this->request->data['Subject']['image']) && !empty($this->request->data['Subject']['image']) ){
									$response['image'] = $this->request->data['Subject']['image'];
								}

							 } else {
								$subject_id = $this->Subject->getLastInsertId();
								$response['sub_type'] = 'add';
							 }


							$response['success'] = true;
							$response['msg'] = "Success";

							$response['content']['last_id'] = $subject_id;

						} else {
							$response['msg'] = "Error!!!";
						}
					} else {
						$response['content'] = $this->validateErrors($this->Subject);
					}
				} else {
					$response['msg'] = "Title is required.";
					$response['content'] = $this->validateErrors($this->Subject);

				}
			}

			echo json_encode($response);
			exit();

		}

	}


	public function trash_subject($subject_id = null) {

		if ($this->request->isAjax()) {

			$this->layout = false;
			$viewData = null;
			$viewData['subject_id'] = $subject_id;
			$this->set($viewData);
			$this->render('partials/subjects/trash_subject');

		}
	}

	public function delete_subject() {

		$this->autoRender = false;

		$response = ['success' => false];
		$this->layout = 'ajax';

		if ($this->request->is('ajax')) {
			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;
				$subject_id = $post['subject_id'];

				if (!empty($subject_id) && !empty($subject_id)) {

						$model = 'Subject';
						$unlikpath = WWW_ROOT.SUBJECT_IMAGE_PATH;
						$unlikfilepath = WWW_ROOT.SUBJECT_FILE_PATH;

						$linkModel = 'SubjectLink';
						$fileModel = 'SubjectFile';
						$pdfModel = 'SubjectPdf';
						$userModel = 'UserSubject';
						$detailModel = 'SubjectDetail';
						$projectModel = 'ProjectSubject';
						$keywordModel = 'Keyword';

						$detail_table = 'subject_details';
						$pdf_table = 'subject_pdfs';
						$user_table = 'user_subjects';
						$file_table = 'subject_files';
						$link_table = 'subject_links';
						$project_table = 'project_subjects';

						$model_id = 'subject_id';

						$fileData = $this->$model->find('all', array('conditions'=>array($model.'.id' => $subject_id ) ) );
						$filesData = $this->$fileModel->find('all', array('conditions'=>array($fileModel.'.'.$model_id => $subject_id ) ) );
						$pdfData = $this->$pdfModel->find('all', array('conditions'=>array($pdfModel.'.'.$model_id => $subject_id ) ) );


					if ($this->Subject->delete(['Subject.id' => $subject_id], false)) {


						if( isset($filesData) && !empty($filesData) ){
							foreach($filesData as $listfiles){
								if( file_exists($unlikfilepath.$listfiles[$fileModel]['upload_file']) && !empty($listfiles[$fileModel]['upload_file']) ){
									unlink($unlikfilepath.$listfiles[$fileModel]['upload_file']);
								}
							}
						}
						if( isset($pdfData) && !empty($pdfData) ){
							foreach($pdfData as $listpdf){
								$user_id = $listpdf[$pdfModel]['user_id'];
								if (file_exists(WWW_ROOT.SUBJECT_PDF_PATH . $user_id . DS . $listpdf[$pdfModel]['pdf_name']) && !empty($listpdf[$pdfModel]['pdf_name']) ) {
									unlink(WWW_ROOT.SUBJECT_PDF_PATH . $user_id . DS . $listpdf[$pdfModel]['pdf_name']);
								}
							}
						}
						if( !empty($fileData) ){
								foreach($fileData as $listids){
								$filename = $listids[$model]['image'];
								if( file_exists($unlikpath.$filename) && !empty($filename) ){
									unlink($unlikpath.$filename);
								}
							}
						}

						$this->$linkModel->query("delete from ".$link_table." where $model_id = ".$subject_id );
						$this->$fileModel->query("delete from ".$file_table." where $model_id = ".$subject_id );
						$this->$pdfModel->query("delete from ".$pdf_table." where $model_id = ".$subject_id );
						$this->$userModel->query("delete from ".$user_table." where $model_id = ".$subject_id );
						$this->$detailModel->query("delete from ".$detail_table." where $model_id = ".$subject_id );
						$this->$projectModel->query("delete from ".$project_table." where $model_id = ".$subject_id );
						$this->$keywordModel->query("delete from keywords where `type`='subject' and item_id = ".$subject_id );


						$this->Skill->query("delete from location_subjects where subject_id = ".$subject_id );
						$this->Skill->query("delete from department_subjects where subject_id = ".$subject_id );
						$this->Skill->query("delete from organization_subjects where subject_id = ".$subject_id );


						$response['success'] = true;

					}
				}
			}
		}

		echo json_encode($response);
		exit();
	}


	public function manage_subject_links(){

		if ($this->request->isAjax()) {

            $this->layout = 'ajax';
			$this->autoRender = false;
			$response = [
				'success' => false,
				'msg' => '',
				'content' => null,
			];

			if ($this->request->is('post') || $this->request->is('put')) {

					$this->request->data['SubjectLink']['user_id'] = $this->Auth->user('id');
					$this->request->data['SubjectLink']['subject_id'] = $this->request->data['subject_id'];

					if ( $this->SubjectLink->save($this->request->data) ) {

						$link_id = $this->SubjectLink->getLastInsertId();


						$response = ['success' => true, 'msg' => "Subject Link successfully saved", 'content'=>$link_id];

					} else {

						$response['content'] = $this->validateErrors($this->SubjectLink);

					}
			}

			echo json_encode($response);
			exit();

		}

	}

	public function manage_subject_files(){

		if ($this->request->isAjax()) {

            $this->layout = 'ajax';
			$this->autoRender = false;
			$response = [
				'success' => false,
				'msg' => '',
				'content' => null,
			];

		if ($this->request->is('post') || $this->request->is('put')) {
				$check_file = 1;

			if(isset($this->request->data['SubjectFile']['subject_id']) && !empty($this->request->data['SubjectFile']['subject_id'])) {


				if (isset($this->request->data['extension_valid']) && empty($this->request->data['extension_valid'])) {

					$check_file = ($this->request->data['extension_valid'] > 1) ? 1 : 2;
				}


				$sizeLimit = 5 * 1024 * 1024; // 5MB
				$folder_url = WWW_ROOT . SUBJECT_FILE_PATH;
				$result = $fileNewName = $upload_object = $upload_detail = null;

				if ($check_file == true && isset($this->request->data['SubjectFile']['upload_file']['name']) && !empty($this->request->data['SubjectFile']['upload_file']['name']) ) {
					$upload_object = (isset($this->request->data['SubjectFile']['upload_file'])) ? $this->request->data['SubjectFile']['upload_file'] : null;

					if ($upload_object) {
						if (!file_exists($folder_url)) {
							mkdir($folder_url, 0777, true);
						}

						$sizeMB = 0;
						$sizeStr = "";
						$sizeKB = $upload_object['size'] / 1024;
						if (($sizeKB) > 1024) {
							$sizeMB = $sizeKB / 1024;
							$sizeStr = number_format($sizeMB, 2) . " MB";
						} else {
							$sizeStr = number_format($sizeKB, 2) . " KB";
						}
						$fileExt = substr($upload_object['name'], strripos($upload_object['name'], '.') + 1);
						$fileName = substr($upload_object['name'], 0, strripos($upload_object['name'], '.'));

						if ($sizeMB <= $sizeLimit) {

							if (!is_writable($folder_url)) {
								$result = array(
									'error' => "Server error. Upload directory isn't writable. Please ask server admin to change permissions.",
								);
							}

							// if file exists, change file name with the saved entry of the record id
							$orgFileName = $upload_object['name'];
							$exists_file = $folder_url . DS . $orgFileName;


							$fileNewName = $orgFileName;
							if (!empty($fileNewName)) {

								$tempFile = $upload_object['tmp_name'];

								$unique_file_name = $this->unique_file_name($folder_url,$fileNewName);
								$targetFile = $folder_url . DS . $unique_file_name;
								$fileSize = true; // filesize($tempFile);

								if (!$fileSize) {
									$result = array(
										'error' => 'File is too large. Please ask server admin to increase the file upload limit.',
									);
								}
								if (empty($result)) {
									move_uploaded_file($tempFile, $targetFile);
								}

								$upload_detail['name'] = $unique_file_name;
								$upload_detail['type'] = $upload_object['type'];
								$upload_detail['size'] = $sizeStr;
							}
						} else {

							$check_file = false;
							$response['msg'] = "File size limit exceeded,Please upload a file upto 5MB.";
						}
					}
				}

				if (isset($upload_detail) && !empty($upload_detail)) {
					$this->request->data['SubjectFile']['upload_file'] = $upload_detail['name'];
				} else {
					unset($this->request->data['SubjectFile']['upload_file']);
					unset($this->request->data['SubjectFile']['file_name']);
					unset($this->request->data['SubjectFile']['file_size']);
					unset($this->request->data['SubjectFile']['file_type']);
				}


				if (isset($check_file) && !empty($check_file) && $check_file == 1 && isset($this->request->data['SubjectFile']['file_name']) && !empty($this->request->data['SubjectFile']['file_name']) ) {

					if ($this->SubjectFile->validates()) {

						$this->request->data['SubjectFile']['user_id'] =  $this->Auth->user('id');

						if ($this->SubjectFile->save($this->request->data['SubjectFile'])) {
							$files_id = $this->SubjectFile->getLastInsertId();
							$response['success'] = true;
							$response['msg'] = "Success";
							$response['content']['last_id'] = $files_id;

						} else {
							$response['msg'] = "Error!!!";
						}
					} else {
						$response['content'] = $this->validateErrors($this->SubjectFile);
					}
				} else {
					$response['msg'] = "File title is required.";
					$response['content'] = $this->validateErrors($this->SubjectFile);
				}

				}
			}
			echo json_encode($response);
			exit();
		}
	}

	/*************** Domain ***************/
	public function manage_domains(){

		if ($this->request->isAjax()) {

            $this->layout = 'ajax';
			$this->autoRender = false;
			$response = [
				'success' => false,
				'msg' => '',
				'content' => null,
				'sub_type' => 'add',
				'image' => '',
			];

			if ($this->request->is('post') || $this->request->is('put')) {
				$check_file = 1;

				if (empty($this->request->data['KnowledgeDomain']['id'])) {
					$this->request->data['KnowledgeDomain']['created_by'] = $this->Auth->user('id');
					$this->request->data['KnowledgeDomain']['modified_by'] = $this->Auth->user('id');
				} else {
					$this->request->data['KnowledgeDomain']['modified_by'] = $this->Auth->user('id');
				}

				if (isset($this->request->data['KnowledgeDomain']['id']) && empty($this->request->data['KnowledgeDomain']['id'])) {
					if (isset($this->request->data['extension_valid']) && empty($this->request->data['extension_valid'])) {

						$check_file = ($this->request->data['extension_valid'] > 1) ? 1 : 2;
					}
				} else {
					if (isset($this->request->data['extension_valid']) && empty($this->request->data['extension_valid'])) {

						$check_file = ($this->request->data['extension_valid'] > 1) ? 1 : 2;
					}
				}

				$sizeLimit = 5 * 1024 * 1024; // 5MB
				$folder_url = WWW_ROOT . DOMAIN_IMAGE_PATH;
				$result = $fileNewName = $upload_object = $upload_detail = null;


				if ($check_file == true && isset($this->request->data['KnowledgeDomain']["image"]) && !empty($this->request->data['KnowledgeDomain']["image"]) ) {
					$upload_object = (isset($this->request->data['KnowledgeDomain']["image"])) ? $this->request->data['KnowledgeDomain']["image"] : null;

					if ($upload_object) {
						if (!file_exists($folder_url)) {
							mkdir($folder_url, 0777, true);
						}

						$sizeMB = 0;
						$sizeStr = "";
						$sizeKB = $upload_object['size'] / 1024;
						if (($sizeKB) > 1024) {
							$sizeMB = $sizeKB / 1024;
							$sizeStr = number_format($sizeMB, 2) . " MB";
						} else {
							$sizeStr = number_format($sizeKB, 2) . " KB";
						}
						$fileExt = substr($upload_object['name'], strripos($upload_object['name'], '.') + 1);
						$fileName = substr($upload_object['name'], 0, strripos($upload_object['name'], '.'));

						if ($sizeMB <= $sizeLimit) {

							if (!is_writable($folder_url)) {
								$result = array(
									'error' => "Server error. Upload directory isn't writable. Please ask server admin to change permissions.",
								);
							}

							// if file exists, change file name with the saved entry of the record id
							$orgFileName = $upload_object['name'];
							$exists_file = $folder_url . DS . $orgFileName;

							$fileNewName = $orgFileName;
							if (!empty($fileNewName)) {

								$tempFile = $upload_object['tmp_name'];

								$unique_file_name = $this->unique_file_name($folder_url,$fileNewName);
								$targetFile = $folder_url . DS . $unique_file_name;
								$fileSize = true; // filesize($tempFile);

								if (!$fileSize) {
									$result = array(
										'error' => 'File is too large. Please ask server admin to increase the file upload limit.',
									);
								}
								if (empty($result)) {
									move_uploaded_file($tempFile, $targetFile);
								}

								$upload_detail['name'] = $unique_file_name;
								$upload_detail['type'] = $upload_object['type'];
								$upload_detail['size'] = $sizeStr;
							}
						} else {

							$check_file = false;
							$response['msg'] = "File size limit exceeded,Please upload a file upto 5MB.";
						}
					}
				}

				if (isset($upload_detail) && !empty($upload_detail)) {
					$this->request->data['KnowledgeDomain']['image'] = $upload_detail['name'];
				} else {
					unset($this->request->data['KnowledgeDomain']['image']);
					unset($this->request->data['KnowledgeDomain']['file_name']);
					unset($this->request->data['KnowledgeDomain']['file_size']);
					unset($this->request->data['KnowledgeDomain']['file_type']);
				}

				if ( isset($this->request->data['KnowledgeDomain']['title']) && !empty($this->request->data['KnowledgeDomain']['title']) ) {

					$this->KnowledgeDomain->set($this->request->data['KnowledgeDomain']);

					if ( $this->KnowledgeDomain->validates() ) {

						if ($this->KnowledgeDomain->save($this->request->data['KnowledgeDomain'])) {

							if( isset($this->request->data['KnowledgeDomain']) && !empty($this->request->data['KnowledgeDomain']) ){
								$knowledge_domain_id = $this->request->data['KnowledgeDomain']['id'];
							} else {
								$knowledge_domain_id = $this->KnowledgeDomain->getLastInsertId();
							}

							 if( isset($this->request->data['KnowledgeDomain']['id']) && !empty($this->request->data['KnowledgeDomain']['id']) ) {

								$knowledge_domain_id = $this->request->data['KnowledgeDomain']['id'];
								$response['sub_type'] = 'update';

								if( isset($this->request->data['KnowledgeDomain']['image']) && !empty($this->request->data['KnowledgeDomain']['image']) ){
									$response['image'] = $this->request->data['KnowledgeDomain']['image'];
								}

							 } else {
								$knowledge_domain_id = $this->KnowledgeDomain->getLastInsertId();
								$response['sub_type'] = 'add';
							 }


							$response['success'] = true;
							$response['msg'] = "Success";

							$response['content']['last_id'] = $knowledge_domain_id;

						} else {
							$response['msg'] = "Error!!!";
						}
					} else {
						$response['content'] = $this->validateErrors($this->KnowledgeDomain);
					}
				} else {
					$response['msg'] = "Title is required.";
					$response['content'] = $this->validateErrors($this->KnowledgeDomain);

				}
			}

			echo json_encode($response);
			exit();

		}

	}

	public function trash_domain($knowledge_domain_id = null) {

		if ($this->request->isAjax()) {

			$this->layout = false;
			$viewData = null;
			$viewData['knowledge_domain_id'] = $knowledge_domain_id;
			$this->set($viewData);
			$this->render('partials/domains/trash_domain');

		}
	}

	public function delete_domain() {

		$this->autoRender = false;

		$response = ['success' => false];
		$this->layout = 'ajax';

		if ($this->request->is('ajax')) {
			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;
				$knowledge_domain_id = $post['knowledge_domain_id'];

				if (!empty($knowledge_domain_id) && !empty($knowledge_domain_id)) {


					//$this->DomainFile->query("delete from domain_files where domain_id = ".$knowledge_domain_id);
					//$this->DomainLink->query("delete from domain_links where domain_id = ".$knowledge_domain_id);

					/********************************************************/
					$model = 'KnowledgeDomain';
					$unlikpath = WWW_ROOT.DOMAIN_IMAGE_PATH;
					$unlikfilepath = WWW_ROOT.DOMAIN_FILE_PATH;

					$linkModel = 'DomainLink';
					$fileModel = 'DomainFile';
					$pdfModel = 'DomainPdf';
					$userModel = 'UserDomain';
					$detailModel = 'DomainDetail';
					$projectModel = 'ProjectDomain';
					$keywordModel = 'Keyword';

					$detail_table = 'domain_details';
					$pdf_table = 'domain_pdfs';
					$user_table = 'user_domains';
					$file_table = 'domain_files';
					$link_table = 'domain_links';
					$project_table = 'project_domains';
					$model_id = 'domain_id';


					$fileData = $this->$model->find('all', array('conditions'=>array($model.'.id' => $knowledge_domain_id ) ) );
					$filesData = $this->$fileModel->find('all', array('conditions'=>array($fileModel.'.'.$model_id => $knowledge_domain_id ) ) );
					$pdfData = $this->$pdfModel->find('all', array('conditions'=>array($pdfModel.'.'.$model_id => $knowledge_domain_id ) ) );


				/********************************************************/



					if ($this->KnowledgeDomain->delete(['KnowledgeDomain.id' => $knowledge_domain_id], false)) {


						if( isset($filesData) && !empty($filesData) ){
							foreach($filesData as $listfiles){
								if( file_exists($unlikfilepath.$listfiles[$fileModel]['upload_file']) && !empty($listfiles[$fileModel]['upload_file']) ){
									unlink($unlikfilepath.$listfiles[$fileModel]['upload_file']);
								}
							}
						}
						if( isset($pdfData) && !empty($pdfData) ){
							foreach($pdfData as $listpdf){
								$user_id = $listpdf[$pdfModel]['user_id'];
								if (file_exists(WWW_ROOT.DOMAIN_PDF_PATH . $user_id . DS . $listpdf[$pdfModel]['pdf_name']) && !empty($listpdf[$pdfModel]['pdf_name']) ) {
									unlink(WWW_ROOT.DOMAIN_PDF_PATH . $user_id . DS . $listpdf[$pdfModel]['pdf_name']);
								}
							}
						}

						if( !empty($fileData) ){
								foreach($fileData as $listids){
								$filename = $listids[$model]['image'];
								if( file_exists($unlikpath.$filename) && !empty($filename) ){
									unlink($unlikpath.$filename);
								}
							}
						}

						$this->$linkModel->query("delete from ".$link_table." where $model_id = ".$knowledge_domain_id );
						$this->$fileModel->query("delete from ".$file_table." where $model_id = ".$knowledge_domain_id );
						$this->$pdfModel->query("delete from ".$pdf_table." where $model_id = ".$knowledge_domain_id );
						$this->$userModel->query("delete from ".$user_table." where $model_id = ".$knowledge_domain_id );
						$this->$detailModel->query("delete from ".$detail_table." where $model_id = ".$knowledge_domain_id );
						$this->$projectModel->query("delete from ".$project_table." where $model_id = ".$knowledge_domain_id );
						$this->$keywordModel->query("delete from keywords where `type`='domain' and item_id = ".$knowledge_domain_id );


						$this->Skill->query("delete from location_domains where domain_id = ".$knowledge_domain_id );
						$this->Skill->query("delete from department_domains where domain_id = ".$knowledge_domain_id );
						$this->Skill->query("delete from organization_domains where domain_id = ".$knowledge_domain_id );

						$response['success'] = true;
					}
				}
			}
		}

		echo json_encode($response);
		exit();
	}


	public function manage_domain_links(){

		if ($this->request->isAjax()) {

            $this->layout = 'ajax';
			$this->autoRender = false;
			$response = [
				'success' => false,
				'msg' => '',
				'content' => null,
			];

			if ($this->request->is('post') || $this->request->is('put')) {

					$this->request->data['DomainLink']['user_id'] = $this->Auth->user('id');
					$this->request->data['DomainLink']['domain_id'] = $this->request->data['domain_id'];

					if ( $this->DomainLink->save($this->request->data) ) {

						$domain_id = $this->DomainLink->getLastInsertId();


						$response = ['success' => true, 'msg' => "Domain Link successfully saved", 'content'=>$domain_id];

					} else {

						$response['content'] = $this->validateErrors($this->DomainLink);

					}
			}

			echo json_encode($response);
			exit();

		}

	}

	public function manage_domain_files(){

		if ($this->request->isAjax()) {

            $this->layout = 'ajax';
			$this->autoRender = false;
			$response = [
				'success' => false,
				'msg' => '',
				'content' => null,
			];

		if ($this->request->is('post') || $this->request->is('put')) {
				$check_file = 1;

			if(isset($this->request->data['DomainFile']['domain_id']) && !empty($this->request->data['DomainFile']['domain_id'])) {


				if (isset($this->request->data['extension_valid']) && empty($this->request->data['extension_valid'])) {

					$check_file = ($this->request->data['extension_valid'] > 1) ? 1 : 2;
				}


				$sizeLimit = 5 * 1024 * 1024; // 5MB
				$folder_url = WWW_ROOT . DOMAIN_FILE_PATH;
				$result = $fileNewName = $upload_object = $upload_detail = null;

				if ($check_file == true && isset($this->request->data['DomainFile']['upload_file']['name']) && !empty($this->request->data['DomainFile']['upload_file']['name']) ) {
					$upload_object = (isset($this->request->data['DomainFile']['upload_file'])) ? $this->request->data['DomainFile']['upload_file'] : null;

					if ($upload_object) {
						if (!file_exists($folder_url)) {
							mkdir($folder_url, 0777, true);
						}

						$sizeMB = 0;
						$sizeStr = "";
						$sizeKB = $upload_object['size'] / 1024;
						if (($sizeKB) > 1024) {
							$sizeMB = $sizeKB / 1024;
							$sizeStr = number_format($sizeMB, 2) . " MB";
						} else {
							$sizeStr = number_format($sizeKB, 2) . " KB";
						}
						$fileExt = substr($upload_object['name'], strripos($upload_object['name'], '.') + 1);
						$fileName = substr($upload_object['name'], 0, strripos($upload_object['name'], '.'));

						if ($sizeMB <= $sizeLimit) {

							if (!is_writable($folder_url)) {
								$result = array(
									'error' => "Server error. Upload directory isn't writable. Please ask server admin to change permissions.",
								);
							}

							// if file exists, change file name with the saved entry of the record id
							$orgFileName = $upload_object['name'];
							$exists_file = $folder_url . DS . $orgFileName;


							$fileNewName = $orgFileName;
							if (!empty($fileNewName)) {

								$tempFile = $upload_object['tmp_name'];

								$unique_file_name = $this->unique_file_name($folder_url,$fileNewName);
								$targetFile = $folder_url . DS . $unique_file_name;
								$fileSize = true; // filesize($tempFile);

								if (!$fileSize) {
									$result = array(
										'error' => 'File is too large. Please ask server admin to increase the file upload limit.',
									);
								}
								if (empty($result)) {
									move_uploaded_file($tempFile, $targetFile);
								}

								$upload_detail['name'] = $unique_file_name;
								$upload_detail['type'] = $upload_object['type'];
								$upload_detail['size'] = $sizeStr;
							}
						} else {

							$check_file = false;
							$response['msg'] = "File size limit exceeded,Please upload a file upto 5MB.";
						}
					}
				}

				if (isset($upload_detail) && !empty($upload_detail)) {
					$this->request->data['DomainFile']['upload_file'] = $upload_detail['name'];
				} else {
					unset($this->request->data['DomainFile']['upload_file']);
					unset($this->request->data['DomainFile']['file_name']);
					unset($this->request->data['DomainFile']['file_size']);
					unset($this->request->data['DomainFile']['file_type']);
				}


				if (isset($check_file) && !empty($check_file) && $check_file == 1 && isset($this->request->data['DomainFile']['file_name']) && !empty($this->request->data['DomainFile']['file_name']) ) {

					if ($this->DomainFile->validates()) {

						$this->request->data['DomainFile']['user_id'] =  $this->Auth->user('id');

						if ($this->DomainFile->save($this->request->data['DomainFile'])) {
							$files_id = $this->DomainFile->getLastInsertId();
							$response['success'] = true;
							$response['msg'] = "Success";
							$response['content']['last_id'] = $files_id;

						} else {
							$response['msg'] = "Error!!!";
						}
					} else {
						$response['content'] = $this->validateErrors($this->DomainFile);
					}
				} else {
					$response['msg'] = "File title is required.";
					$response['content'] = $this->validateErrors($this->DomainFile);
				}

				}
			}
			echo json_encode($response);
			exit();
		}
	}


	public function bulk_skill_update(){

		if ($this->request->isAjax()) {

			$this->layout = false;
			$viewData = null;
			$viewData['skills'] = $this->Skill->find('list', ['fields' => ['id', 'title'], 'order'=>['title asc']] );
			$this->set($viewData);
			$this->render('partials/skills/bulk_update');

		}

	}

	public function get_all_data(){

		if ($this->request->isAjax()) {

			$this->layout = false;
			$response = ['success' => true, 'content' => null];

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;

				$type = $post['type'];
				if($type == 'skill'){
					$data = $this->Skill->find('list', ['fields' => ['id', 'title'], 'order'=>['title asc']] );
				}
				if($type == 'subject'){
					$data = $this->Subject->find('list', ['fields' => ['id', 'title'], 'order'=>['title asc']] );
				}
				if($type == 'domain'){
					$data = $this->KnowledgeDomain->find('list', ['fields' => ['id', 'title'], 'order'=>['title asc']] );
				}

				$detail = [];
				if(isset($data) && !empty($data)){
					foreach ($data as $key => $value) {
						$detail[] = ['id'=> $key, 'title'=> $value];
					}
				}

				$response['content'] = $detail;
			}

			echo json_encode($response);
			exit;
		}

	}


	public function bulk_subject_update(){

		if ($this->request->isAjax()) {

			$this->layout = false;
			$viewData = null;
			$viewData['subjects'] = $this->Subject->find('list', ['fields' => ['id', 'title'], 'order'=>['title asc']] );
			$this->set($viewData);
			$this->render('partials/subjects/bulk_update');

		}

	}

	public function bulk_domain_update(){
		if ($this->request->isAjax()) {
			$this->layout = false;
			$viewData = null;
			$viewData['domains'] = $this->KnowledgeDomain->find('list', ['fields' => ['id', 'title'], 'order'=>['title asc']] );
			$this->set($viewData);
			$this->render('partials/domains/bulk_update');
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


				if( isset($this->request->data['id']) && !empty($this->request->data['id']) && isset($this->request->data['dtype']) ){

					$model = '';
					$linkModel = '';
					$fileModel = '';
					$file_table = '';
					$link_table = '';
					$model_id = '';
					$pdfModel = '';
					$userModel = '';
					$pdf_table = '';
					$user_table = '';
					$type = '';
					$keywordModel = 'Keyword';

					if( $this->request->data['dtype'] == 'Subject' ){
						$model = 'Subject';
						$unlikpath = WWW_ROOT.SUBJECT_IMAGE_PATH;
						$unlikfilepath = WWW_ROOT.SUBJECT_FILE_PATH;
						$unlinkfilepdfpath = WWW_ROOT.SUBJECT_PDF_PATH;

						$linkModel = 'SubjectLink';
						$fileModel = 'SubjectFile';
						$pdfModel = 'SubjectPdf';
						$userModel = 'UserSubject';
						$detailModel = 'SubjectDetail';
						$projectModel = 'ProjectSubject';

						$detail_table = 'subject_details';
						$pdf_table = 'subject_pdfs';
						$user_table = 'user_subjects';
						$file_table = 'subject_files';
						$link_table = 'subject_links';
						$project_table = 'project_subjects';
						$type = 'subject';

						$model_id = 'subject_id';

					} else if( $this->request->data['dtype'] == 'Domain' ){
						$model = 'KnowledgeDomain';
						$unlikpath = WWW_ROOT.DOMAIN_IMAGE_PATH;
						$unlikfilepath = WWW_ROOT.DOMAIN_FILE_PATH;
						$unlinkfilepdfpath = WWW_ROOT.DOMAIN_PDF_PATH;

						$linkModel = 'DomainLink';
						$fileModel = 'DomainFile';
						$pdfModel = 'DomainPdf';
						$userModel = 'UserDomain';
						$detailModel = 'DomainDetail';
						$projectModel = 'ProjectDomain';

						$detail_table = 'domain_details';
						$pdf_table = 'domain_pdfs';
						$user_table = 'user_domains';
						$file_table = 'domain_files';
						$link_table = 'domain_links';
						$project_table = 'project_domains';
						$type = 'domain';

						$model_id = 'domain_id';

					} else {
						$model = 'Skill';
						$unlikpath = WWW_ROOT.SKILL_IMAGE_PATH;
						$unlikfilepath = WWW_ROOT.SKILL_FILE_PATH;
						$unlinkfilepdfpath = WWW_ROOT.SKILL_PDF_PATH;

						$linkModel = 'SkillLink';
						$fileModel = 'SkillFile';
						$pdfModel = 'SkillPdf';
						$userModel = 'UserSkill';
						$detailModel = 'SkillDetail';
						$projectModel = 'ProjectSkill';

						$detail_table = 'skill_details';
						$pdf_table = 'skill_pdfs';
						$user_table = 'user_skills';
						$file_table = 'skill_files';
						$link_table = 'skill_links';
						$project_table = 'project_skills';
						$type = 'skill';

						$model_id = 'skill_id';

					}


					$delids = $this->request->data['id'];
					$fileData = $this->$model->find('all', array('conditions'=>array($model.'.id' => $delids ) ) );
					$filesData = $this->$fileModel->find('all', array('conditions'=>array($fileModel.'.'.$model_id => $delids ) ) );
					$pdfData = $this->$pdfModel->find('all', array('conditions'=>array($pdfModel.'.'.$model_id => $delids ) ) );


					$unlikfile = array();
					if( isset($fileData) && !empty($fileData) ){
						$unlikfile = $fileData;
					}


					if( $this->$model->delete($delids) ){

						// delete data from related tables
						if( isset($filesData) && !empty($filesData) ){
							foreach($filesData as $listfiles){
								if( file_exists($unlikfilepath.$listfiles[$fileModel]['upload_file']) && !empty($listfiles[$fileModel]['upload_file']) ){
									unlink($unlikfilepath.$listfiles[$fileModel]['upload_file']);
								}
							}
						}
						if( isset($pdfData) && !empty($pdfData) ){
							foreach($pdfData as $listpdf){
								$user_id = $listpdf[$pdfModel]['user_id'];
								if (file_exists($unlinkfilepdfpath . $user_id . DS . $listpdf[$pdfModel]['pdf_name']) && !empty($listpdf[$pdfModel]['pdf_name']) ) {
									unlink($unlinkfilepdfpath . $user_id . DS . $listpdf[$pdfModel]['pdf_name']);
								}
							}
						}


						$listids = implode(',',$delids);
						$this->$fileModel->query("delete from ".$file_table." where $model_id IN (".$listids.")" );
						$this->$linkModel->query("delete from ".$link_table." where $model_id IN (".$listids.")" );
						$this->$detailModel->query("delete from ".$detail_table." where $model_id IN (".$listids.")" );
						$this->$pdfModel->query("delete from ".$pdf_table." where $model_id IN (".$listids.")" );
						$this->$userModel->query("delete from ".$user_table." where $model_id IN (".$listids.")" );
						$this->$projectModel->query("delete from ".$project_table." where $model_id IN (".$listids.")" );
						$this->$keywordModel->query("delete from keywords where `type`='".$type."' and item_id IN (".$listids.")" );


						if( $this->request->data['dtype'] == 'Subject' ){
							$this->Skill->query("delete from location_subjects where subject_id IN(".$listids.")" );
							$this->Skill->query("delete from department_subjects where subject_id IN(".$listids.") " );
						}
						else if( $this->request->data['dtype'] == 'Domain' ){
							$this->Skill->query("delete from location_domains where domain_id IN(".$listids.")" );
							$this->Skill->query("delete from department_domains where domain_id IN(".$listids.") " );
						}
						else {
							$this->Skill->query("delete from location_skills where skill_id IN(".$listids.")" );
							$this->Skill->query("delete from department_skills where skill_id IN(".$listids.") " );
						}



						/***************************************************/

						 if( !empty($unlikfile) ){
								foreach($unlikfile as $listids){
								$filename = $listids[$model]['image'];
								if( file_exists($unlikpath.$filename) && !empty($filename) ){
									unlink($unlikpath.$filename);
								}
							}
						}

						$response['success'] = true;
						$response['content'] = 'Success';

					} else {

						$response = [
							'success' => false,
							'content' => 'No Found',
						];

					}

				}
				echo json_encode($response);
				exit();
			}

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

				$folder_url = WWW_ROOT . 'uploads/competency_temp_files/';
				$upload_object = null;
				// pr($this->request->data, 1);

				if ( isset($this->request->data['KnowledgeDomain']['image']['name']) && !empty($this->request->data['KnowledgeDomain']['image']['name']) ) {
					$upload_object = $this->request->data['KnowledgeDomain']['image'];
				}
				if ( isset($this->request->data['DomainFile']['upload_file']['name']) && !empty($this->request->data['DomainFile']['upload_file']['name']) ) {
					$upload_object = $this->request->data['DomainFile']['upload_file'];
				}
				if ( isset($this->request->data['SkillFile']['upload_file']['name']) && !empty($this->request->data['SkillFile']['upload_file']['name']) ) {
					$upload_object = $this->request->data['SkillFile']['upload_file'];
				}
				if ( isset($this->request->data['Skill']['image']['name']) && !empty($this->request->data['Skill']['image']['name']) ) {
					$upload_object = $this->request->data['Skill']['image'];
				}
				if ( isset($this->request->data['Subject']['image']['name']) && !empty($this->request->data['Subject']['image']['name']) ) {
					$upload_object = $this->request->data['Subject']['image'];
				}
				if ( isset($this->request->data['SubjectFile']['upload_file']['name']) && !empty($this->request->data['SubjectFile']['upload_file']['name']) ) {
					$upload_object = $this->request->data['SubjectFile']['upload_file'];
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
					$unlikpath = WWW_ROOT . 'uploads/competency_temp_files/';
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
					$unlikpath = WWW_ROOT . 'uploads/competency_temp_files/';
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

	public function save_domain_data(){

		if ($this->request->isAjax()) {

            $this->layout = 'ajax';
            $response = [
				'success' => false,
				'content' => null,
			];

            if ($this->request->is('post') || $this->request->is('put')) {
            	$post = $this->request->data;
            	// pr($post, 1);
				$old_image = '';
            	if(isset($post['main']['name']) && !empty($post['main']['name'])){
            		$kd = $post['main'];
            		$kd_data = [
						'title' => $kd['name'],
            			'description' =>  $kd['description'],
            			'modified_by' => $this->user_id
            		];

            		if(isset($kd['id']) && !empty($kd['id'])){
            			$kd_data['id'] = $kd['id'];

						$imgData = $this->KnowledgeDomain->find('first', ['conditions'=>['KnowledgeDomain.id'=>$kd['id']]]);
						if( isset($imgData['KnowledgeDomain']['image']) && !empty($imgData['KnowledgeDomain']['image']) ){
							$old_image = $imgData['KnowledgeDomain']['image'];
						}

            		}
            		else{
            			$sd_data['created_by'] = $this->user_id;
            		}
            		$kd_file = WWW_ROOT . 'uploads/competency_temp_files/' . $kd['filename'];
	            	$folder_url = WWW_ROOT . DOMAIN_IMAGE_PATH . $post['main']['filename'];
	            	if(isset($kd['filename']) && !empty($kd['filename'])){

						if( !empty($old_image) ){
							unlink(WWW_ROOT . DOMAIN_IMAGE_PATH.$old_image);
						}

		            	$unique_file_name = $this->unique_file_name(WWW_ROOT . DOMAIN_IMAGE_PATH, $post['main']['filename']);
		            	$folder_url = WWW_ROOT . DOMAIN_IMAGE_PATH . $unique_file_name;
		            	if(rename($kd_file, $folder_url)){
		            		// unlink($kd_file);
		            		$kd_data['image'] = $unique_file_name;
		            	}

		            }
            		if($this->KnowledgeDomain->save($kd_data)) {
            			if(isset($kd['id']) && !empty($kd['id'])){
	            			$kd_id = $kd['id'];
	            		}
	            		else{
	            			$kd_id = $this->KnowledgeDomain->getLastInsertId();
	            		}

            			$response['success'] = true;
            			if(isset($post['link']) && !empty($post['link'])){
            				$link = $post['link'];
            				foreach ($link as $key => $value) {
								$link_data = [
									'user_id' => $this->user_id,
									'domain_id' => $kd_id,
									'link_name' => $value['title'],
									'web_link' => $value['url']
								];
								$this->DomainLink->id = null;
								if($this->DomainLink->save($link_data)){

								}
            				}
            			}
						if(isset($post['keyword']) && !empty($post['keyword'])){
            				$keyword = $post['keyword'];
            				foreach ($keyword as $key => $value) {
								$keyword_data = [
									'type' => 'domain',
									'item_id' => $kd_id,
									'keyword' => $value['keyword']
								];
								$this->Keyword->id = null;
								if($this->Keyword->save($keyword_data)){

								}
            				}
            			}
            			if(isset($post['file']) && !empty($post['file'])){
            				$file = $post['file'];
            				foreach ($file as $key => $value) {

			            		$df_file = WWW_ROOT . 'uploads/competency_temp_files/' . $value['filename'];
				            	$df_folder_url = WWW_ROOT . DOMAIN_FILE_PATH . $value['filename'];
				            	if(isset($value['filename']) && !empty($value['filename'])){
					            	$unique_file_name = $this->unique_file_name(WWW_ROOT . DOMAIN_FILE_PATH, $value['filename']);
		            				$df_folder_url = WWW_ROOT . DOMAIN_FILE_PATH . $unique_file_name;
					            	if(rename($df_file, $df_folder_url)){
					            		// unlink($df_file);
					            	}
					            }
								$file_data = [
									'user_id' => $this->user_id,
									'domain_id' => $kd_id,
									'file_name' => $value['title'],
									'upload_file' => $value['filename']
								];
								// pr($file_data, 1);
								$this->DomainFile->id = null;
								if($this->DomainFile->save($file_data)){

								}
            				}
            			}
            		}
            	}
            }
            echo json_encode($response);
			exit();

        }
    }

	public function save_skill_data(){

		if ($this->request->isAjax()) {

            $this->layout = 'ajax';
            $response = [
				'success' => false,
				'content' => null,
			];

            if ($this->request->is('post') || $this->request->is('put')) {
            	$post = $this->request->data;
            	// pr($post, 1);
				$old_image = '';
            	if(isset($post['main']['name']) && !empty($post['main']['name'])){
            		$sd = $post['main'];

            		$sd_data = [
            			'title' =>  $sd['name'],
            			'description' => $sd['description'] ,
            			'modified_by' => $this->user_id,
            		];
            		if(isset($sd['id']) && !empty($sd['id'])){
            			$sd_data['id'] = $sd['id'];

						$imgData = $this->Skill->find('first', ['conditions'=>['Skill.id'=>$sd['id']]]);
						if( isset($imgData['Skill']['image']) && !empty($imgData['Skill']['image']) ){
							$old_image = $imgData['Skill']['image'];
						}

            		}
            		else{
            			$sd_data['created_by'] = $this->user_id;
            		}
            		$sd_file = WWW_ROOT . 'uploads/competency_temp_files/' . $sd['filename'];
	            	$folder_url = WWW_ROOT . SKILL_IMAGE_PATH . $post['main']['filename'];
	            	if(isset($sd['filename']) && !empty($sd['filename'])){

						if( !empty($old_image) ){
							unlink(WWW_ROOT . SKILL_IMAGE_PATH.$old_image);
						}

		            	$unique_file_name = $this->unique_file_name(WWW_ROOT . SKILL_IMAGE_PATH, $post['main']['filename']);
		            	$folder_url = WWW_ROOT . SKILL_IMAGE_PATH . $unique_file_name;
		            	// pr($unique_file_name);
		            	if(rename($sd_file, $folder_url)){
		            		// unlink($sd_file);
		            		$sd_data['image'] = $unique_file_name;
		            	}
		            }
            		if($this->Skill->save($sd_data)) {
            			if(isset($sd['id']) && !empty($sd['id'])){
	            			$kd_id = $sd['id'];
	            		}
	            		else{
	            			$kd_id = $this->Skill->getLastInsertId();
	            		}

            			$response['success'] = true;
            			if(isset($post['link']) && !empty($post['link'])){
            				$link = $post['link'];
            				foreach ($link as $key => $value) {
								$link_data = [
									'user_id' => $this->user_id,
									'skill_id' => $kd_id,
									'link_name' => $value['title'],
									'web_link' => $value['url']
								];
								$this->SkillLink->id = null;
								if($this->SkillLink->save($link_data)){

								}
            				}
            			}

						if(isset($post['keyword']) && !empty($post['keyword'])){
            				$keyword = $post['keyword'];
            				foreach ($keyword as $key => $value) {
								$keyword_data = [
									'type' => 'skill',
									'item_id' => $kd_id,
									'keyword' => $value['keyword']
								];
								$this->Keyword->id = null;
								if($this->Keyword->save($keyword_data)){

								}
            				}
            			}
            			if(isset($post['file']) && !empty($post['file'])){
            				$file = $post['file'];
            				foreach ($file as $key => $value) {

			            		$df_file = WWW_ROOT . 'uploads/competency_temp_files/' . $value['filename'];
				            	$df_folder_url = WWW_ROOT . SKILL_FILE_PATH . $value['filename'];
				            	if(isset($value['filename']) && !empty($value['filename'])){
					            	$unique_file_name = $this->unique_file_name(WWW_ROOT . SKILL_FILE_PATH, $value['filename']);
		            				$df_folder_url = WWW_ROOT . SKILL_FILE_PATH . $unique_file_name;

					            	if(rename($df_file, $df_folder_url)){
					            		// unlink($df_file);
					            	}
					            }
								$file_data = [
									'user_id' => $this->user_id,
									'skill_id' => $kd_id,
									'file_name' => $value['title'],
									'upload_file' => $value['filename']
								];
								// pr($file_data, 1);
								$this->SkillFile->id = null;
								if($this->SkillFile->save($file_data)){

								}
            				}
            			}
            		}
            	}
            }
            echo json_encode($response);
			exit();

        }
    }

	public function save_subject_data(){

		if ($this->request->isAjax()) {

            $this->layout = 'ajax';
            $response = [
				'success' => false,
				'content' => null,
			];

            if ($this->request->is('post') || $this->request->is('put')) {
            	$post = $this->request->data;
            	// pr($post, 1);
				$old_image = '';
            	if(isset($post['main']['name']) && !empty($post['main']['name'])) {
            		$sd = $post['main'];
            		$sd_data = [
            			'title' => $sd['name'],
            			'description' =>  $sd['description'],
            			'modified_by' => $this->user_id
            		];
            		if(isset($sd['id']) && !empty($sd['id'])) {
            			$sd_data['id'] = $sd['id'];

						$imgData = $this->Subject->find('first', ['conditions'=>['Subject.id'=>$sd['id']]]);
						if( isset($imgData['Subject']['image']) && !empty($imgData['Subject']['image']) ){
							$old_image = $imgData['Subject']['image'];
						}

            		}
            		else{
            			$sd_data['created_by'] = $this->user_id;
            		}
            		$sd_file = WWW_ROOT . 'uploads/competency_temp_files/' . $sd['filename'];
	            	$folder_url = WWW_ROOT . SUBJECT_IMAGE_PATH . $post['main']['filename'];
	            	if(isset($sd['filename']) && !empty($sd['filename'])){

						if( !empty($old_image) ){
							unlink(WWW_ROOT . SUBJECT_IMAGE_PATH.$old_image);
						}

						$unique_file_name = $this->unique_file_name(WWW_ROOT . SUBJECT_IMAGE_PATH, $post['main']['filename']);
						$folder_url = WWW_ROOT . SUBJECT_IMAGE_PATH . $unique_file_name;
							if(rename($sd_file, $folder_url)){
								// unlink($sd_file);
								$sd_data['image'] = $unique_file_name;
							}
						}

            		if($this->Subject->save($sd_data)) {

            			if(isset($sd['id']) && !empty($sd['id'])){
	            			$kd_id = $sd['id'];
	            		}
	            		else{
	            			$kd_id = $this->Subject->getLastInsertId();
	            		}
            			$response['success'] = true;
            			if(isset($post['link']) && !empty($post['link'])){
            				$link = $post['link'];
            				foreach ($link as $key => $value) {
								$link_data = [
									'user_id' => $this->user_id,
									'subject_id' => $kd_id,
									'link_name' => $value['title'],
									'web_link' => $value['url']
								];
								$this->SubjectLink->id = null;
								if($this->SubjectLink->save($link_data)){

								}
            				}
            			}
						if(isset($post['keyword']) && !empty($post['keyword'])){
            				$keyword = $post['keyword'];
            				foreach ($keyword as $key => $value) {
								$keyword_data = [
									'type' => 'subject',
									'item_id' => $kd_id,
									'keyword' => $value['keyword']
								];
								$this->Keyword->id = null;
								if($this->Keyword->save($keyword_data)){

								}
            				}
            			}
            			if(isset($post['file']) && !empty($post['file'])){
            				$file = $post['file'];
            				foreach ($file as $key => $value) {

			            		$df_file = WWW_ROOT . 'uploads/competency_temp_files/' . $value['filename'];
				            	$df_folder_url = WWW_ROOT . SUBJECT_FILE_PATH . $value['filename'];
				            	if(isset($value['filename']) && !empty($value['filename'])){
					            	$unique_file_name = $this->unique_file_name(WWW_ROOT . SUBJECT_FILE_PATH, $value['filename']);
		            				$df_folder_url = WWW_ROOT . SUBJECT_FILE_PATH . $unique_file_name;
					            	if(rename($df_file, $df_folder_url)){
					            		// unlink($df_file);
					            	}
					            }
								$file_data = [
									'user_id' => $this->user_id,
									'subject_id' => $kd_id,
									'file_name' => $value['title'],
									'upload_file' => $value['filename']
								];
								// pr($file_data, 1);
								$this->SubjectFile->id = null;
								if($this->SubjectFile->save($file_data)){

								}
            				}
            			}
            		}
            	}
            }
            echo json_encode($response);
			exit();

        }
    }

	public function checkkeywords(){


		if ($this->request->isAjax()) {

			$this->layout = false;
			$this->autoRender = false;
			$response['success'] = 'true';

			if ($this->request->is('post') || $this->request->is('put')) {


				if(  isset($this->request->data['kval']) && !empty($this->request->data['kval']) && isset($this->request->data['dtype']) && !empty($this->request->data['dtype']) && isset($this->request->data['item_id']) && !empty($this->request->data['item_id']) ) {



						$filter = trim($this->request->data['kval']);
						$val = Sanitize::clean($filter, array('encode' => true));
						$val = addslashes($filter);
						$dataType = $this->request->data['dtype'];
						$item_id = $this->request->data['item_id'];
						$model = 'keywords';
						$keywordModel = 'Keyword';

						$query ="SELECT COUNT(`id`) as total FROM ".$model." WHERE ".$model.".`keyword` = '".$val."' AND ".$model.".`type` = '$dataType'  AND ".$model.".`item_id` = $item_id ";

						$data = $this->$keywordModel->query($query);
						if( isset($data) && !empty($data[0][0]['total']) && $data[0][0]['total'] > 0 ){
							$response['success'] = 'false';
						}

				}

			}
			 echo json_encode($response);
			exit();
			exit();
		}

	}

	/* COMPARE */
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
						$whrid = "";
						if(isset($post['users']) && !empty($post['users'])){
							$users = implode(',', $post['users']);
							$whrid = " AND u.id IN($users)";
						}
						$this->loadModel('User');
						$qry_str = "";
						$data = $this->User->query("SELECT u.id, CONCAT_WS(' ', ud.first_name, ud.last_name) as full_name FROM users u INNER JOIN user_details ud ON ud.user_id = u.id WHERE u.role_id = 2 AND u.status = 1 AND u.is_activated = 1 $whrid ORDER BY full_name ASC");
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

	public function get_compare() {
		if ($this->request->isAjax()) {
            $this->layout = 'ajax';
            $this->loadModel('UserPermission');
            $response = ['success' => false];
            $data = [];
            $user_id = $this->Session->read('Auth.User.id');
            if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				// pr($post );
				$people_from = $post["people_from"];
	            $item_1 = (isset($post["item_1"]) && !empty($post["item_1"])) ? $post["item_1"] : [];
	            $skills_dd = (isset($post["skills_dd"]) && !empty($post["skills_dd"])) ? $post["skills_dd"] : [];
	            $subjects_dd = (isset($post["subjects_dd"]) && !empty($post["subjects_dd"])) ? $post["subjects_dd"] : [];
	            $domains_dd = (isset($post["domains_dd"]) && !empty($post["domains_dd"])) ? $post["domains_dd"] : [];

	            $total_comp = count($skills_dd) + count($subjects_dd) + count($domains_dd);
	            $this->set('total_comp', $total_comp);

	            $page = (isset($post["page"]) && !empty($post["page"])) ? $post["page"] : 0;
	            $user_page = (isset($post["user_page"]) && !empty($post["user_page"])) ? $post["user_page"] : 0;

				$limit_str = '';
				if(isset($post["limit"]) && !empty($post["limit"])){
					$limit = $post["limit"];
					$limit_str = "LIMIT $page, $limit";
				}
				else if((!isset($post["count"]) || empty($post["count"])) && (!isset($post["user_counter"]) || empty($post["user_counter"]))){
					$limit_str = "LIMIT 0, ".$this->compare_user_offset;
				}

				$user_total = $this->compare_user_offset;
				$comp_total = $this->compare_comp_offset;
				$user_limit = "LIMIT 0, $user_total";
				$comp_limit = "LIMIT 0, $comp_total";
				if(isset($user_page) && !empty($user_page)) {
					$user_page = $user_page * $this->compare_user_offset;
					$user_limit = "LIMIT $user_page, $user_total";
				}
				else if(isset($post["limit"]) && !empty($post["limit"])) {
					$page = $page * $this->compare_comp_offset;
					$comp_limit = "LIMIT $page, $comp_total";
				}

				$sql_1  = "";

				if(isset($people_from) && !empty($people_from)){
					if($people_from == "community"){
						$sql_1 = "#Select People From: All Community
								SELECT u.id AS user_id, CONCAT(ud.first_name, ' ', ud.last_name) AS full_name, ud.job_title, ud.profile_pic, ud.organization_id FROM users u
								INNER JOIN user_details ud ON u.id = ud.user_id
								WHERE u.role_id = 2 AND u.status = 1 AND u.is_activated = 1 AND u.is_deleted = 0
								ORDER BY ud.first_name, ud.last_name";
					}
					else if($people_from == "organizations"){
						$item_1 = implode(",", $item_1);
						$sql_1 = "#Select People From: Specific Organizations
								SELECT u.id AS user_id, CONCAT(ud.first_name, ' ', ud.last_name) AS full_name, ud.job_title, ud.profile_pic, ud.organization_id FROM users u
								INNER JOIN user_details ud ON u.id = ud.user_id
								WHERE u.role_id = 2 AND u.status = 1 AND u.is_activated = 1 AND u.is_deleted = 0 AND ud.organization_id IN ($item_1)
								ORDER BY ud.first_name, ud.last_name";

					}
					else if($people_from == "locations"){
						$item_1 = implode(",", $item_1);
						$sql_1 = "#Select People From: Specific Locations
								SELECT u.id AS user_id, CONCAT(ud.first_name, ' ', ud.last_name) AS full_name, ud.job_title, ud.profile_pic, ud.organization_id FROM users u
								INNER JOIN user_details ud ON u.id = ud.user_id
								WHERE u.role_id = 2 AND u.status = 1 AND u.is_activated = 1 AND u.is_deleted = 0 AND ud.location_id IN ($item_1)
								ORDER BY ud.first_name, ud.last_name";

					}
					else if($people_from == "departments"){
						$item_1 = implode(",", $item_1);
						$sql_1 = "#Select People From: Specific Departments
								SELECT u.id AS user_id, CONCAT(ud.first_name, ' ', ud.last_name) AS full_name, ud.job_title, ud.profile_pic, ud.organization_id FROM users u
								INNER JOIN user_details ud ON u.id = ud.user_id
								WHERE u.role_id = 2 AND u.status = 1 AND u.is_activated = 1 AND u.is_deleted = 0 AND ud.department_id IN ($item_1)
								ORDER BY ud.first_name, ud.last_name";

					}
					else if($people_from == "users"){
						$item_1 = implode(",", $item_1);
						$sql_1 = "#Select People From: Specific People
								SELECT u.id AS user_id, CONCAT(ud.first_name, ' ', ud.last_name) AS full_name, ud.job_title, ud.profile_pic, ud.organization_id FROM users u
								INNER JOIN user_details ud ON u.id = ud.user_id
								WHERE u.role_id = 2 AND u.status = 1 AND u.is_activated = 1 AND u.is_deleted = 0 AND u.id IN ($item_1)
								ORDER BY ud.first_name, ud.last_name";

					}
					else if($people_from == "tags"){
						$sql_1 = "#Select People From: Specific Tags
								SELECT DISTINCT u.id AS user_id, CONCAT(ud.first_name, ' ', ud.last_name) AS full_name, ud.job_title, ud.profile_pic, ud.organization_id FROM tags t
								INNER JOIN users u ON t.tagged_user_id = u.id
								INNER JOIN user_details ud ON u.id = ud.user_id
								WHERE u.role_id = 2 AND u.status = 1 AND u.is_activated = 1 AND u.is_deleted = 0 AND t.user_id = $user_id AND t.tag IN ('" . implode("', '", $item_1) . "')
								ORDER BY ud.first_name, ud.last_name";

					}
					else if($people_from == "skills"){
						$item_1 = implode(",", $item_1);
						$sql_1 = "#Select People From: Specific Skills
								SELECT DISTINCT u.id AS user_id, CONCAT(ud.first_name, ' ', ud.last_name) AS full_name, ud.job_title, ud.profile_pic, ud.organization_id FROM users u
								INNER JOIN user_details ud ON u.id = ud.user_id
								LEFT JOIN user_skills us ON u.id = us.user_id
								WHERE u.role_id = 2 AND u.status = 1 AND u.is_activated = 1 AND u.is_deleted = 0 AND us.skill_id IN ($item_1)
								ORDER BY ud.first_name, ud.last_name";

					}
					else if($people_from == "subjects"){
						$item_1 = implode(",", $item_1);
						$sql_1 = "#Select People From: Specific Subjects
								SELECT DISTINCT u.id AS user_id, CONCAT(ud.first_name, ' ', ud.last_name) AS full_name, ud.job_title, ud.profile_pic, ud.organization_id FROM users u
								INNER JOIN user_details ud ON u.id = ud.user_id
								LEFT JOIN user_subjects us ON u.id = us.user_id
								WHERE u.role_id = 2 AND u.status = 1 AND u.is_activated = 1 AND u.is_deleted = 0 AND us.subject_id IN ($item_1)
								ORDER BY ud.first_name, ud.last_name";

					}
					else if($people_from == "domains"){
						$item_1 = implode(",", $item_1);
						$sql_1 = "#Select People From: Specific Domains
								SELECT DISTINCT u.id AS user_id, CONCAT(ud.first_name, ' ', ud.last_name) AS full_name, ud.job_title, ud.profile_pic, ud.organization_id FROM users u
								INNER JOIN user_details ud ON u.id = ud.user_id
								LEFT JOIN user_domains us ON u.id = us.user_id
								WHERE u.role_id = 2 AND u.status = 1 AND u.is_activated = 1 AND u.is_deleted = 0 AND us.domain_id IN ($item_1)
								ORDER BY ud.first_name, ud.last_name";

					}
					else if($people_from == "all_projects"){
						$sql_1 = "#Select People From: All My Projects
									SELECT DISTINCT u.id AS user_id, CONCAT(ud.first_name, ' ', ud.last_name) AS full_name, ud.job_title, ud.profile_pic, ud.organization_id
									FROM user_permissions up1
									INNER JOIN users u ON up1.user_id = u.id
									INNER JOIN user_details ud ON u.id = ud.user_id
									INNER JOIN user_permissions up2 ON up1.project_id = up2.project_id AND up2.workspace_id IS NULL
									WHERE up1.workspace_id IS NULL AND up2.user_id = $user_id AND u.role_id = 2 AND u.status = 1 AND u.is_activated = 1 AND u.is_deleted = 0
									ORDER BY ud.first_name, ud.last_name";

					}
					else if($people_from == "created_projects"){
						$sql_1 = "#Select People From: Projects I Created
								SELECT DISTINCT u.id AS user_id, CONCAT(ud.first_name, ' ', ud.last_name) AS full_name, ud.job_title, ud.profile_pic, ud.organization_id
								FROM user_permissions up1
								INNER JOIN users u ON up1.user_id = u.id
								INNER JOIN user_details ud ON u.id = ud.user_id
								INNER JOIN user_permissions up2 ON up1.project_id = up2.project_id AND up2.workspace_id IS NULL
								WHERE up1.workspace_id IS NULL AND up2.user_id = $user_id AND up2.role = 'Creator' AND u.role_id = 2 AND u.status = 1 AND u.is_activated = 1 AND u.is_deleted = 0
								ORDER BY ud.first_name, ud.last_name";

					}
					else if($people_from == "owner_projects"){
						$sql_1 = "#Select People From: Projects I Own
								SELECT DISTINCT u.id AS user_id, CONCAT(ud.first_name, ' ', ud.last_name) AS full_name, ud.job_title, ud.profile_pic, ud.organization_id
								FROM user_permissions up1
								INNER JOIN users u ON up1.user_id = u.id
								INNER JOIN user_details ud ON u.id = ud.user_id
								INNER JOIN user_permissions up2 ON up1.project_id = up2.project_id AND up2.workspace_id IS NULL
								WHERE up1.workspace_id IS NULL AND up2.user_id = $user_id AND up2.role IN ('Creator', 'Owner', 'Group Owner') AND u.role_id = 2 AND u.status = 1 AND u.is_activated = 1 AND u.is_deleted = 0
								ORDER BY ud.first_name, ud.last_name";

					}
					else if($people_from == "shared_projects"){
						$sql_1 = "#Select People From: Projects Shared with Me
								SELECT DISTINCT u.id AS user_id, CONCAT(ud.first_name, ' ', ud.last_name) AS full_name, ud.job_title, ud.profile_pic, ud.organization_id
								FROM user_permissions up1
								INNER JOIN users u ON up1.user_id = u.id
								INNER JOIN user_details ud ON u.id = ud.user_id
								INNER JOIN user_permissions up2 ON up1.project_id = up2.project_id AND up2.workspace_id IS NULL
								WHERE up1.workspace_id IS NULL AND up2.user_id = $user_id AND up2.role IN ('Sharer', 'Group Sharer') AND u.role_id = 2 AND u.status = 1 AND u.is_activated = 1 AND u.is_deleted = 0
								ORDER BY ud.first_name, ud.last_name";

					}
					else if($people_from == "project"){
						$item_1 = implode(",", $item_1);
						if(!empty($resourcer)){
							$sql_1 = "#Select People From: Specific Projects - RESOURCER
									SELECT DISTINCT u.id AS user_id, CONCAT(ud.first_name, ' ', ud.last_name) AS full_name, ud.job_title, ud.profile_pic, ud.organization_id
									FROM user_permissions up1
									INNER JOIN users u ON up1.user_id = u.id
									INNER JOIN user_details ud ON u.id = ud.user_id
									INNER JOIN user_permissions up2 ON up1.project_id = up2.project_id AND up2.workspace_id IS NULL
									WHERE up1.workspace_id IS NULL AND
									up2.role = 'Creator' AND
									up1.project_id IN ($item_1) AND
									u.role_id = 2 AND u.status = 1 AND u.is_activated = 1 AND u.is_deleted = 0
									ORDER BY ud.first_name, ud.last_name";
						}
						else{
							$sql_1 = "#Select People From: Specific Projects - NOT RESOURCER
									SELECT DISTINCT u.id AS user_id, CONCAT(ud.first_name, ' ', ud.last_name) AS full_name, ud.job_title, ud.profile_pic, ud.organization_id
									FROM user_permissions up1
									INNER JOIN users u ON up1.user_id = u.id
									INNER JOIN user_details ud ON u.id = ud.user_id
									INNER JOIN user_permissions up2 ON up1.project_id = up2.project_id AND up2.workspace_id IS NULL
									WHERE up1.workspace_id IS NULL AND up2.user_id = $user_id AND up1.project_id IN ($item_1) AND u.role_id = 2 AND u.status = 1 AND u.is_activated = 1 AND u.is_deleted = 0
									ORDER BY ud.first_name, ud.last_name";

						}
					}
				}

				$users_total_data = $this->UserPermission->query($sql_1);
				$this->set('total_users_found', count($users_total_data));
				$user_sql = $sql_1 . " " . $user_limit;
				// e($user_sql );
				$users_data = $this->UserPermission->query($user_sql);

				$main_sql = "SELECT ud.comp_type, ud.comp_id, ud.comp_name,";
				//pr($users_data); die;
				if(isset($users_data) && !empty($users_data)){
					$i= 1;
					$uscount = count($users_data);
					foreach($users_data as $us){
						$us_id = $us['u']['user_id'];
						//pr( $us_id);
						if($i > 1){
							$main_sql .=  ",";
						}
						$main_sql .= #GENERATE 4 COLUMNS FOR EACH SELECTED USER_ID AT RUNTIME:
						" $us_id AS '".$us_id."_userid',
						GROUP_CONCAT(CASE WHEN ud.user_id = $us_id THEN ud.comp_level ELSE NULL END) AS  '".$us_id."_level',
						GROUP_CONCAT(CASE WHEN ud.user_id = $us_id THEN ud.comp_experience ELSE NULL END) AS '".$us_id."_experience',
						SUM(CASE WHEN ud.user_id = $us_id THEN ud.comp_files ELSE NULL END) AS '".$us_id."_files' ";
						if($uscount < $i){
							$main_sql .=  ",";
						}

						$i++;
					}


				$skill_part = '';
				$skill_from = '';

				if(isset($skills_dd) && !empty($skills_dd)){
					$skill_part = "#INCLUDE THIS SECTION IF USER HAS SELECTED SKILLS:
									#get skills
									SELECT 'Skill' AS comp_type, c.id AS comp_id, c.title AS comp_name FROM skills c
									#INCLUDE THIS LINE AND SKILL IDS IF USER HAS SELECTED SKILLS IN THE UI:
									WHERE c.id IN ('" . implode("', '", $skills_dd) . "')
									#END OF SECTION-----";

					$skill_from = "SELECT
											'Skill' AS comp_type,
											uc.skill_id AS comp_id,
											uc.user_id,
											cd.user_level AS comp_level,
											cd.user_experience AS comp_experience,
											COUNT(cp.id) AS comp_files
										FROM user_skills uc
										LEFT JOIN skill_details cd ON
											uc.skill_id = cd.skill_id
											AND uc.user_id = cd.user_id
										LEFT JOIN skill_pdfs cp ON
											uc.skill_id = cp.skill_id
											AND uc.user_id = cp.user_id
											AND cp.upload_status = 1
										GROUP BY uc.skill_id, uc.user_id";
				}

				$sub_part = '';
				$sub_from = '';

				if(isset($subjects_dd) && !empty($subjects_dd)){

					if( (isset($skills_dd) && !empty($skills_dd)) && (isset($subjects_dd) && !empty($subjects_dd)) ){
							$sub_part .= "UNION ALL ";
							$sub_from .= "UNION ALL ";
					}

					$sub_part .= "#REMOVE THIS LINE IF USER HAS NOT SELECTED SUBJECTS OR DOMAINS:

									#INCLUDE THIS SECTION IF USER HAS SELECTED SUBJECTS:
									#get subjects
									SELECT 'Subject' AS comp_type, c.id AS comp_id, c.title AS comp_name FROM subjects c
									#INCLUDE THIS LINE AND SUBJECT IDS IF USER HAS SELECTED SUBJECTS IN THE UI:
									WHERE c.id IN ('" . implode("', '", $subjects_dd) . "')
									#END OF SECTION-----";

					$sub_from .= "SELECT
											'Subject' AS comp_type,
											uc.subject_id AS comp_id,
											uc.user_id,
											cd.user_level AS comp_level,
											cd.user_experience AS comp_experience,
											COUNT(cp.id) AS comp_files
										FROM user_subjects uc
										LEFT JOIN subject_details cd ON
											uc.subject_id = cd.subject_id
											AND uc.user_id = cd.user_id
										LEFT JOIN subject_pdfs cp ON
											uc.subject_id = cp.subject_id
											AND uc.user_id = cp.user_id
											AND cp.upload_status = 1
										GROUP BY uc.subject_id, uc.user_id";
				}

				$dom_part = '';
				$dom_from = '';
				if(isset($domains_dd) && !empty($domains_dd)){

					if( (isset($skills_dd) && !empty($skills_dd)) || (isset($subjects_dd) && !empty($subjects_dd)) ){
							$dom_part .= "UNION ALL ";
							$dom_from .= "UNION ALL ";
					}
					$dom_part .= "#REMOVE THIS LINE IF USER HAS NOT SELECTED DOMAINS:

									#INCLUDE THIS SECTION IF USER HAS SELECTED DOMAINS:
									#get domains
									SELECT 'Domain' AS comp_type, c.id AS comp_id, c.title AS comp_name FROM knowledge_domains c
									#INCLUDE THIS LINE AND DOMAIN IDS IF USER HAS SELECTED DOMAINS IN THE UI:
									WHERE c.id IN ('" . implode("', '", $domains_dd) . "')
									#END OF SECTION-----";
					$dom_from .= "SELECT
											'Domain' AS comp_type,
											uc.domain_id AS comp_id,
											uc.user_id,
											cd.user_level AS comp_level,
											cd.user_experience AS comp_experience,
											COUNT(cp.id) AS comp_files
										FROM user_domains uc
										LEFT JOIN domain_details cd ON
											uc.domain_id = cd.domain_id
											AND uc.user_id = cd.user_id
										LEFT JOIN domain_pdfs cp ON
											uc.domain_id = cp.domain_id
											AND uc.user_id = cp.user_id
											AND cp.upload_status = 1
										GROUP BY uc.domain_id, uc.user_id";
				}


				$main_sql .="FROM
							#get skills/subjects/domains and users who have them
							(
								SELECT
									c.comp_type,
									c.comp_id,
									c.comp_name,
									uc.user_id,
									uc.comp_level,
									uc.comp_experience,
									uc.comp_files
								FROM
								#get skills/subjects/domains
								(

									$skill_part
									$sub_part
									$dom_part

									ORDER BY FIELD(comp_type,'Skill','Subject','Domain'), comp_name

									#CHANGE TO CURRENT COMPETENCY PAGE PARAMETERS AT RUNTIME:
									$comp_limit #50 rows per page
								) AS c
								LEFT JOIN
								#get user skills/subjects/domains
								(
									SELECT
										uu.comp_type,
										uu.comp_id,
										uu.user_id,
										uu.comp_level,
										uu.comp_experience,
										uu.comp_files
									FROM
									(
										#INCLUDE THIS SECTION IF USER HAS SELECTED SKILLS:
										#get user skills
										$skill_from
										#END OF SECTION-----

										#REMOVE THIS LINE IF USER HAS NOT SELECTED SUBJECTS OR DOMAINS:


										#INCLUDE THIS SECTION IF USER HAS SELECTED SKILLS
										#get user subjects
										$sub_from
										#END OF SECTION-----

										#REMOVE THIS LINE IF USER HAS NOT SELECTED DOMAINS:


										#INCLUDE THIS SECTION IF USER HAS SELECTED DOMAINS:
										#get user domains
										$dom_from
										#END OF SECTION-----
									) AS uu
									INNER JOIN
									#get users
									(
										# CHANGE THIS SUB QUERY BASED ON SELECTED USERS IN UI (SEE ADDITIONAL SQL FILE - SELECT PEOPLE FROM)
										#Select People From: All Community
										$sql_1
										#-----

										#CHANGE TO CURRENT USER PAGE PARAMETERS AT RUNTIME:
										$user_limit #25 user columns per page
									) AS u ON
										uu.user_id = u.user_id
								) AS uc ON
									c.comp_type = uc.comp_type
									AND c.comp_id = uc.comp_id
							) AS ud
							GROUP BY ud.comp_type, ud.comp_id
							ORDER BY FIELD(ud.comp_type,'Skill','Subject','Domain'), ud.comp_name";

				 // pr($main_sql); die;

				$main_sql = utf8_decode($main_sql);
				$data = $this->UserPermission->query($main_sql);
				}else{
					$data = [];
				}
			}
        }

        $this->set("data", $data);
        $this->set("users_data", $users_data);

		$this->render('/Competencies/partials/compare_listing');

    }

	public function compare_section() {
		if ($this->request->isAjax()) {
            $this->layout = 'ajax';
            $this->loadModel('UserPermission');
            $response = ['success' => false];
            $data = [];
            $user_id = $this->Session->read('Auth.User.id');
           // $resourcer = $this->Session->read('Auth.User.UserDetail.resourcer');
            if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				// pr($post );
				$people_from = $post["people_from"];
	            $item_1 = (isset($post["item_1"]) && !empty($post["item_1"])) ? $post["item_1"] : [];
	            $skills_dd = (isset($post["skills_dd"]) && !empty($post["skills_dd"])) ? $post["skills_dd"] : [];
	            $subjects_dd = (isset($post["subjects_dd"]) && !empty($post["subjects_dd"])) ? $post["subjects_dd"] : [];
	            $domains_dd = (isset($post["domains_dd"]) && !empty($post["domains_dd"])) ? $post["domains_dd"] : [];


	            $section = (isset($post["section"]) && !empty($post["section"])) ? $post["section"] : 0;
	            $this->set('section', $section);

	            $page = (isset($post["page"]) && !empty($post["page"])) ? $post["page"] : 0;
	            $user_page = (isset($post["user_page"]) && !empty($post["user_page"])) ? $post["user_page"] : 0;


				$user_total = $this->compare_user_offset;
				$comp_total = $this->compare_comp_offset;
				$user_limit = "LIMIT 0, $user_total";
				$comp_limit = "LIMIT 0, $comp_total";
					$user_page = $user_page * $this->compare_user_offset;
					$user_limit = "LIMIT $user_page, $user_total";

					// $page = $page * $this->compare_comp_offset;
					$comp_limit = "LIMIT $page, $comp_total";
					// pr($comp_limit);


				$sql_1  = "";

				if(isset($people_from) && !empty($people_from)){
					if($people_from == "community"){
						$sql_1 = "#Select People From: All Community
								SELECT u.id AS user_id, CONCAT(ud.first_name, ' ', ud.last_name) AS full_name, ud.job_title, ud.profile_pic, ud.organization_id FROM users u
								INNER JOIN user_details ud ON u.id = ud.user_id
								WHERE u.role_id = 2 AND u.status = 1 AND u.is_deleted = 0
								ORDER BY ud.first_name, ud.last_name";
					}
					else if($people_from == "organizations"){
						$item_1 = implode(",", $item_1);
						$sql_1 = "#Select People From: Specific Organizations
								SELECT u.id AS user_id, CONCAT(ud.first_name, ' ', ud.last_name) AS full_name, ud.job_title, ud.profile_pic, ud.organization_id FROM users u
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

					}
					else if($people_from == "departments"){
						$item_1 = implode(",", $item_1);
						$sql_1 = "#Select People From: Specific Departments
								SELECT u.id AS user_id, CONCAT(ud.first_name, ' ', ud.last_name) AS full_name, ud.job_title, ud.profile_pic, ud.organization_id FROM users u
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

					}
					else if($people_from == "tags"){
						$sql_1 = "#Select People From: Specific Tags
								SELECT DISTINCT t.tagged_user_id AS user_id, CONCAT(ud.first_name, ' ', ud.last_name) AS full_name, ud.job_title, ud.profile_pic, ud.organization_id FROM tags t
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

					}
					else if($people_from == "subjects"){
						$item_1 = implode(",", $item_1);
						$sql_1 = "#Select People From: Specific Subjects
								SELECT DISTINCT u.id AS user_id, CONCAT(ud.first_name, ' ', ud.last_name) AS full_name, ud.job_title, ud.profile_pic, ud.organization_id FROM users u
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

					}
					else if($people_from == "all_projects"){
						$sql_1 = "#Select People From: All My Projects
									SELECT DISTINCT u.id AS user_id, CONCAT(ud.first_name, ' ', ud.last_name) AS full_name, ud.job_title, ud.profile_pic, ud.organization_id
									FROM user_permissions up1
									INNER JOIN users u ON up1.user_id = u.id
									INNER JOIN user_details ud ON u.id = ud.user_id
									INNER JOIN user_permissions up2 ON up1.project_id = up2.project_id AND up2.workspace_id IS NULL
									WHERE up1.workspace_id IS NULL AND up2.user_id = $user_id AND u.role_id = 2 AND u.status = 1 AND u.is_deleted = 0
									ORDER BY ud.first_name, ud.last_name";

					}
					else if($people_from == "created_projects"){
						$sql_1 = "#Select People From: Projects I Created
								SELECT DISTINCT u.id AS user_id, CONCAT(ud.first_name, ' ', ud.last_name) AS full_name, ud.job_title, ud.profile_pic, ud.organization_id
								FROM user_permissions up1
								INNER JOIN users u ON up1.user_id = u.id
								INNER JOIN user_details ud ON u.id = ud.user_id
								INNER JOIN user_permissions up2 ON up1.project_id = up2.project_id AND up2.workspace_id IS NULL
								WHERE up1.workspace_id IS NULL AND up2.user_id = $user_id AND up2.role = 'Creator' AND u.role_id = 2 AND u.status = 1 AND u.is_deleted = 0
								ORDER BY ud.first_name, ud.last_name";

					}
					else if($people_from == "owner_projects"){
						$sql_1 = "#Select People From: Projects I Own
								SELECT DISTINCT u.id AS user_id, CONCAT(ud.first_name, ' ', ud.last_name) AS full_name, ud.job_title, ud.profile_pic, ud.organization_id
								FROM user_permissions up1
								INNER JOIN users u ON up1.user_id = u.id
								INNER JOIN user_details ud ON u.id = ud.user_id
								INNER JOIN user_permissions up2 ON up1.project_id = up2.project_id AND up2.workspace_id IS NULL
								WHERE up1.workspace_id IS NULL AND up2.user_id = $user_id AND up2.role IN ('Creator', 'Owner', 'Group Owner') AND u.role_id = 2 AND u.status = 1 AND u.is_deleted = 0
								ORDER BY ud.first_name, ud.last_name";

					}
					else if($people_from == "shared_projects"){
						$sql_1 = "#Select People From: Projects Shared with Me
								SELECT DISTINCT u.id AS user_id, CONCAT(ud.first_name, ' ', ud.last_name) AS full_name, ud.job_title, ud.profile_pic, ud.organization_id
								FROM user_permissions up1
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
									SELECT DISTINCT u.id AS user_id, CONCAT(ud.first_name, ' ', ud.last_name) AS full_name, ud.job_title, ud.profile_pic, ud.organization_id
									FROM user_permissions up1
									INNER JOIN users u ON up1.user_id = u.id
									INNER JOIN user_details ud ON u.id = ud.user_id
									INNER JOIN user_permissions up2 ON up1.project_id = up2.project_id AND up2.workspace_id IS NULL
									WHERE up1.workspace_id IS NULL AND
									up2.role = 'Creator' AND
									up1.project_id IN ($item_1) AND
									u.role_id = 2 AND u.status = 1 AND u.is_deleted = 0
									ORDER BY ud.first_name, ud.last_name";
						}
						else{
							$sql_1 = "#Select People From: Specific Projects - NOT RESOURCER
									SELECT DISTINCT u.id AS user_id, CONCAT(ud.first_name, ' ', ud.last_name) AS full_name, ud.job_title, ud.profile_pic, ud.organization_id
									FROM user_permissions up1
									INNER JOIN users u ON up1.user_id = u.id
									INNER JOIN user_details ud ON u.id = ud.user_id
									INNER JOIN user_permissions up2 ON up1.project_id = up2.project_id AND up2.workspace_id IS NULL
									WHERE up1.workspace_id IS NULL AND up2.user_id = $user_id AND up1.project_id IN ($item_1) AND u.role_id = 2 AND u.status = 1 AND u.is_deleted = 0
									ORDER BY ud.first_name, ud.last_name";

						}
					}
				}

				$user_sql = $sql_1 . " " . $user_limit;
				// e($user_sql );
				$users_data = $this->UserPermission->query($user_sql);

				$main_sql = "SELECT ud.comp_type, ud.comp_id, ud.comp_name,";

				if(isset($users_data) && !empty($users_data)){
					$i= 1;
					$uscount = count($users_data);
					foreach($users_data as $us){
						$us_id = $us['u']['user_id'];
						//pr( $us_id);
						if($i > 1){
							$main_sql .=  ",";
						}
						$main_sql .= #GENERATE 4 COLUMNS FOR EACH SELECTED USER_ID AT RUNTIME:
						" $us_id AS '".$us_id."_userid',
						GROUP_CONCAT(CASE WHEN ud.user_id = $us_id THEN ud.comp_level ELSE NULL END) AS  '".$us_id."_level',
						GROUP_CONCAT(CASE WHEN ud.user_id = $us_id THEN ud.comp_experience ELSE NULL END) AS '".$us_id."_experience',
						SUM(CASE WHEN ud.user_id = $us_id THEN ud.comp_files ELSE NULL END) AS '".$us_id."_files' ";
						if($uscount < $i){
							$main_sql .=  ",";
						}

						$i++;
					}
				}

				$skill_part = '';
				$skill_from = '';
				if(isset($skills_dd) && !empty($skills_dd)){
					$skill_part = "#INCLUDE THIS SECTION IF USER HAS SELECTED SKILLS:
									#get skills
									SELECT 'Skill' AS comp_type, c.id AS comp_id, c.title AS comp_name FROM skills c
									#INCLUDE THIS LINE AND SKILL IDS IF USER HAS SELECTED SKILLS IN THE UI:
									WHERE c.id IN ('" . implode("', '", $skills_dd) . "')
									#END OF SECTION-----";

					$skill_from = "SELECT
											'Skill' AS comp_type,
											uc.skill_id AS comp_id,
											uc.user_id,
											cd.user_level AS comp_level,
											cd.user_experience AS comp_experience,
											COUNT(cp.id) AS comp_files
										FROM user_skills uc
										LEFT JOIN skill_details cd ON
											uc.skill_id = cd.skill_id
											AND uc.user_id = cd.user_id
										LEFT JOIN skill_pdfs cp ON
											uc.skill_id = cp.skill_id
											AND uc.user_id = cp.user_id
											AND cp.upload_status = 1
										GROUP BY uc.skill_id, uc.user_id";
				}

				$sub_part = '';
				$sub_from = '';

				if(isset($subjects_dd) && !empty($subjects_dd)){

					if( (isset($skills_dd) && !empty($skills_dd)) && (isset($subjects_dd) && !empty($subjects_dd)) ){
							$sub_part .= "UNION ALL ";
							$sub_from .= "UNION ALL ";
					}

					$sub_part .= "#REMOVE THIS LINE IF USER HAS NOT SELECTED SUBJECTS OR DOMAINS:

									#INCLUDE THIS SECTION IF USER HAS SELECTED SUBJECTS:
									#get subjects
									SELECT 'Subject' AS comp_type, c.id AS comp_id, c.title AS comp_name FROM subjects c
									#INCLUDE THIS LINE AND SUBJECT IDS IF USER HAS SELECTED SUBJECTS IN THE UI:
									WHERE c.id IN ('" . implode("', '", $subjects_dd) . "')
									#END OF SECTION-----";

					$sub_from .= "SELECT
											'Subject' AS comp_type,
											uc.subject_id AS comp_id,
											uc.user_id,
											cd.user_level AS comp_level,
											cd.user_experience AS comp_experience,
											COUNT(cp.id) AS comp_files
										FROM user_subjects uc
										LEFT JOIN subject_details cd ON
											uc.subject_id = cd.subject_id
											AND uc.user_id = cd.user_id
										LEFT JOIN subject_pdfs cp ON
											uc.subject_id = cp.subject_id
											AND uc.user_id = cp.user_id
											AND cp.upload_status = 1
										GROUP BY uc.subject_id, uc.user_id";
				}

				$dom_part = '';
				$dom_from = '';
				if(isset($domains_dd) && !empty($domains_dd)){

					if( (isset($skills_dd) && !empty($skills_dd)) || (isset($subjects_dd) && !empty($subjects_dd)) ){
							$dom_part .= "UNION ALL ";
							$dom_from .= "UNION ALL ";
					}
					$dom_part .= "#REMOVE THIS LINE IF USER HAS NOT SELECTED DOMAINS:

									#INCLUDE THIS SECTION IF USER HAS SELECTED DOMAINS:
									#get domains
									SELECT 'Domain' AS comp_type, c.id AS comp_id, c.title AS comp_name FROM knowledge_domains c
									#INCLUDE THIS LINE AND DOMAIN IDS IF USER HAS SELECTED DOMAINS IN THE UI:
									WHERE c.id IN ('" . implode("', '", $domains_dd) . "')
									#END OF SECTION-----";
					$dom_from .= "SELECT
											'Domain' AS comp_type,
											uc.domain_id AS comp_id,
											uc.user_id,
											cd.user_level AS comp_level,
											cd.user_experience AS comp_experience,
											COUNT(cp.id) AS comp_files
										FROM user_domains uc
										LEFT JOIN domain_details cd ON
											uc.domain_id = cd.domain_id
											AND uc.user_id = cd.user_id
										LEFT JOIN domain_pdfs cp ON
											uc.domain_id = cp.domain_id
											AND uc.user_id = cp.user_id
											AND cp.upload_status = 1
										GROUP BY uc.domain_id, uc.user_id";
				}


				$main_sql .="FROM
							#get skills/subjects/domains and users who have them
							(
								SELECT
									c.comp_type,
									c.comp_id,
									c.comp_name,
									uc.user_id,
									uc.comp_level,
									uc.comp_experience,
									uc.comp_files
								FROM
								#get skills/subjects/domains
								(

									$skill_part
									$sub_part
									$dom_part

									ORDER BY FIELD(comp_type,'Skill','Subject','Domain'), comp_name

									#CHANGE TO CURRENT COMPETENCY PAGE PARAMETERS AT RUNTIME:
									$comp_limit #50 rows per page
								) AS c
								LEFT JOIN
								#get user skills/subjects/domains
								(
									SELECT
										uu.comp_type,
										uu.comp_id,
										uu.user_id,
										uu.comp_level,
										uu.comp_experience,
										uu.comp_files
									FROM
									(
										#INCLUDE THIS SECTION IF USER HAS SELECTED SKILLS:
										#get user skills
										$skill_from
										#END OF SECTION-----

										#REMOVE THIS LINE IF USER HAS NOT SELECTED SUBJECTS OR DOMAINS:


										#INCLUDE THIS SECTION IF USER HAS SELECTED SKILLS
										#get user subjects
										$sub_from
										#END OF SECTION-----

										#REMOVE THIS LINE IF USER HAS NOT SELECTED DOMAINS:


										#INCLUDE THIS SECTION IF USER HAS SELECTED DOMAINS:
										#get user domains
										$dom_from
										#END OF SECTION-----
									) AS uu
									INNER JOIN
									#get users
									(
										# CHANGE THIS SUB QUERY BASED ON SELECTED USERS IN UI (SEE ADDITIONAL SQL FILE - SELECT PEOPLE FROM)
										#Select People From: All Community
										$sql_1
										#-----

										#CHANGE TO CURRENT USER PAGE PARAMETERS AT RUNTIME:
										$user_limit #25 user columns per page
									) AS u ON
										uu.user_id = u.user_id
								) AS uc ON
									c.comp_type = uc.comp_type
									AND c.comp_id = uc.comp_id
							) AS ud
							GROUP BY ud.comp_type, ud.comp_id
							ORDER BY FIELD(ud.comp_type,'Skill','Subject','Domain'), ud.comp_name";

				 // pr($main_sql); die;


				$data = $this->UserPermission->query($main_sql);

			}
        }


	        $this->set("data", $data);
	        // $this->set("users_data", $users_data);

			$this->render('/Competencies/partials/compare_section');

    }

	public function get_compare_count() {
		if ($this->request->isAjax()) {
            $this->layout = 'ajax';
            $this->loadModel('UserPermission');
            $response = ['success' => false];
            $data = [];
            $user_id = $this->Session->read('Auth.User.id');
           // $resourcer = $this->Session->read('Auth.User.UserDetail.resourcer');
            if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				$people_from = $post["people_from"];
	            $item_1 = (isset($post["item_1"]) && !empty($post["item_1"])) ? $post["item_1"] : [];
	            $skills_dd = (isset($post["skills_dd"]) && !empty($post["skills_dd"])) ? $post["skills_dd"] : [];
	            $subjects_dd = (isset($post["subjects_dd"]) && !empty($post["subjects_dd"])) ? $post["subjects_dd"] : [];
	            $domains_dd = (isset($post["domains_dd"]) && !empty($post["domains_dd"])) ? $post["domains_dd"] : [];


	            $page = (isset($post["page"]) && !empty($post["page"])) ? $post["page"] : 0;

				$limit_str = '';
				if(isset($post["limit"]) && !empty($post["limit"])){
					$limit = $post["limit"];
					$limit_str = "LIMIT $page, $limit";
				}
				else if((!isset($post["count"]) || empty($post["count"])) && (!isset($post["user_counter"]) || empty($post["user_counter"]))){
					$limit_str = "LIMIT 0, ".$this->compare_user_offset;
				}

				$sql_1  = "";

				if(isset($people_from) && !empty($people_from)){
					if($people_from == "community"){
						$sql_1 = "#Select People From: All Community
								SELECT u.id AS user_id, CONCAT(ud.first_name, ' ', ud.last_name) AS full_name, ud.job_title, ud.profile_pic, ud.organization_id FROM users u
								INNER JOIN user_details ud ON u.id = ud.user_id
								WHERE u.role_id = 2 AND u.status = 1 AND u.is_activated = 1 AND u.is_deleted = 0
								ORDER BY ud.first_name, ud.last_name";


					}
					else if($people_from == "organizations"){
						$item_1 = implode(",", $item_1);
						$sql_1 = "#Select People From: Specific Organizations
								SELECT u.id AS user_id, CONCAT(ud.first_name, ' ', ud.last_name) AS full_name, ud.job_title, ud.profile_pic, ud.organization_id FROM users u
								INNER JOIN user_details ud ON u.id = ud.user_id
								WHERE u.role_id = 2 AND u.status = 1 AND u.is_activated = 1 AND u.is_deleted = 0 AND ud.organization_id IN ($item_1)
								ORDER BY ud.first_name, ud.last_name";

					}
					else if($people_from == "locations"){
						$item_1 = implode(",", $item_1);
						$sql_1 = "#Select People From: Specific Locations
								SELECT u.id AS user_id, CONCAT(ud.first_name, ' ', ud.last_name) AS full_name, ud.job_title, ud.profile_pic, ud.organization_id FROM users u
								INNER JOIN user_details ud ON u.id = ud.user_id
								WHERE u.role_id = 2 AND u.status = 1 AND u.is_activated = 1 AND u.is_deleted = 0 AND ud.location_id IN ($item_1)
								ORDER BY ud.first_name, ud.last_name";

					}
					else if($people_from == "departments"){
						$item_1 = implode(",", $item_1);
						$sql_1 = "#Select People From: Specific Departments
								SELECT u.id AS user_id, CONCAT(ud.first_name, ' ', ud.last_name) AS full_name, ud.job_title, ud.profile_pic, ud.organization_id FROM users u
								INNER JOIN user_details ud ON u.id = ud.user_id
								WHERE u.role_id = 2 AND u.status = 1 AND u.is_activated = 1 AND u.is_deleted = 0 AND ud.department_id IN ($item_1)
								ORDER BY ud.first_name, ud.last_name";

					}
					else if($people_from == "users"){
						$item_1 = implode(",", $item_1);
						$sql_1 = "#Select People From: Specific People
								SELECT u.id AS user_id, CONCAT(ud.first_name, ' ', ud.last_name) AS full_name, ud.job_title, ud.profile_pic, ud.organization_id FROM users u
								INNER JOIN user_details ud ON u.id = ud.user_id
								WHERE u.role_id = 2 AND u.status = 1 AND u.is_activated = 1 AND u.is_deleted = 0 AND u.id IN ($item_1)
								ORDER BY ud.first_name, ud.last_name";

					}
					else if($people_from == "tags"){
						$sql_1 = "#Select People From: Specific Tags
								SELECT DISTINCT t.tagged_user_id AS user_id, CONCAT(ud.first_name, ' ', ud.last_name) AS full_name, ud.job_title, ud.profile_pic, ud.organization_id FROM tags t
								INNER JOIN users u ON t.tagged_user_id = u.id
								INNER JOIN user_details ud ON u.id = ud.user_id
								WHERE u.role_id = 2 AND u.status = 1 AND u.is_activated = 1 AND u.is_deleted = 0 AND t.user_id = $user_id AND t.tag IN ('" . implode("', '", $item_1) . "')
								ORDER BY ud.first_name, ud.last_name";

					}
					else if($people_from == "skills"){
						$item_1 = implode(",", $item_1);
						$sql_1 = "#Select People From: Specific Skills
								SELECT DISTINCT u.id AS user_id, CONCAT(ud.first_name, ' ', ud.last_name) AS full_name, ud.job_title, ud.profile_pic, ud.organization_id FROM users u
								INNER JOIN user_details ud ON u.id = ud.user_id
								LEFT JOIN user_skills us ON u.id = us.user_id
								WHERE u.role_id = 2 AND u.status = 1 AND u.is_activated = 1 AND u.is_deleted = 0 AND us.skill_id IN ($item_1)
								ORDER BY ud.first_name, ud.last_name";

					}
					else if($people_from == "subjects"){
						$item_1 = implode(",", $item_1);
						$sql_1 = "#Select People From: Specific Subjects
								SELECT DISTINCT u.id AS user_id, CONCAT(ud.first_name, ' ', ud.last_name) AS full_name, ud.job_title, ud.profile_pic, ud.organization_id FROM users u
								INNER JOIN user_details ud ON u.id = ud.user_id
								LEFT JOIN user_subjects us ON u.id = us.user_id
								WHERE u.role_id = 2 AND u.status = 1 AND u.is_activated = 1 AND u.is_deleted = 0 AND us.subject_id IN ($item_1)
								ORDER BY ud.first_name, ud.last_name";

					}
					else if($people_from == "domains"){
						$item_1 = implode(",", $item_1);
						$sql_1 = "#Select People From: Specific Domains
								SELECT DISTINCT u.id AS user_id, CONCAT(ud.first_name, ' ', ud.last_name) AS full_name, ud.job_title, ud.profile_pic, ud.organization_id FROM users u
								INNER JOIN user_details ud ON u.id = ud.user_id
								LEFT JOIN user_domains us ON u.id = us.user_id
								WHERE u.role_id = 2 AND u.status = 1 AND u.is_activated = 1 AND u.is_deleted = 0 AND us.domain_id IN ($item_1)
								ORDER BY ud.first_name, ud.last_name";

					}
					else if($people_from == "all_projects"){
						$sql_1 = "#Select People From: All My Projects
									SELECT DISTINCT u.id AS user_id, CONCAT(ud.first_name, ' ', ud.last_name) AS full_name, ud.job_title, ud.profile_pic, ud.organization_id
									FROM user_permissions up1
									INNER JOIN users u ON up1.user_id = u.id
									INNER JOIN user_details ud ON u.id = ud.user_id
									INNER JOIN user_permissions up2 ON up1.project_id = up2.project_id AND up2.workspace_id IS NULL
									WHERE up1.workspace_id IS NULL AND up2.user_id = $user_id AND u.role_id = 2 AND u.status = 1 AND u.is_activated = 1 AND u.is_deleted = 0
									ORDER BY ud.first_name, ud.last_name";

					}
					else if($people_from == "created_projects"){
						$sql_1 = "#Select People From: Projects I Created
								SELECT DISTINCT u.id AS user_id, CONCAT(ud.first_name, ' ', ud.last_name) AS full_name, ud.job_title, ud.profile_pic, ud.organization_id
								FROM user_permissions up1
								INNER JOIN users u ON up1.user_id = u.id
								INNER JOIN user_details ud ON u.id = ud.user_id
								INNER JOIN user_permissions up2 ON up1.project_id = up2.project_id AND up2.workspace_id IS NULL
								WHERE up1.workspace_id IS NULL AND up2.user_id = $user_id AND up2.role = 'Creator' AND u.role_id = 2 AND u.status = 1 AND u.is_activated = 1 AND u.is_deleted = 0
								ORDER BY ud.first_name, ud.last_name";

					}
					else if($people_from == "owner_projects"){
						$sql_1 = "#Select People From: Projects I Own
								SELECT DISTINCT u.id AS user_id, CONCAT(ud.first_name, ' ', ud.last_name) AS full_name, ud.job_title, ud.profile_pic, ud.organization_id
								FROM user_permissions up1
								INNER JOIN users u ON up1.user_id = u.id
								INNER JOIN user_details ud ON u.id = ud.user_id
								INNER JOIN user_permissions up2 ON up1.project_id = up2.project_id AND up2.workspace_id IS NULL
								WHERE up1.workspace_id IS NULL AND up2.user_id = $user_id AND up2.role IN ('Creator', 'Owner', 'Group Owner') AND u.role_id = 2 AND u.status = 1 AND u.is_activated = 1 AND u.is_deleted = 0
								ORDER BY ud.first_name, ud.last_name";

					}
					else if($people_from == "shared_projects"){
						$sql_1 = "#Select People From: Projects Shared with Me
								SELECT DISTINCT u.id AS user_id, CONCAT(ud.first_name, ' ', ud.last_name) AS full_name, ud.job_title, ud.profile_pic, ud.organization_id
								FROM user_permissions up1
								INNER JOIN users u ON up1.user_id = u.id
								INNER JOIN user_details ud ON u.id = ud.user_id
								INNER JOIN user_permissions up2 ON up1.project_id = up2.project_id AND up2.workspace_id IS NULL
								WHERE up1.workspace_id IS NULL AND up2.user_id = $user_id AND up2.role IN ('Sharer', 'Group Sharer') AND u.role_id = 2 AND u.status = 1 AND u.is_activated = 1 AND u.is_deleted = 0
								ORDER BY ud.first_name, ud.last_name";

					}
					else if($people_from == "project"){
						$item_1 = implode(",", $item_1);
						if(!empty($resourcer)){
							$sql_1 = "#Select People From: Specific Projects - RESOURCER
									SELECT DISTINCT u.id AS user_id, CONCAT(ud.first_name, ' ', ud.last_name) AS full_name, ud.job_title, ud.profile_pic, ud.organization_id
									FROM user_permissions up1
									INNER JOIN users u ON up1.user_id = u.id
									INNER JOIN user_details ud ON u.id = ud.user_id
									INNER JOIN user_permissions up2 ON up1.project_id = up2.project_id AND up2.workspace_id IS NULL
									WHERE up1.workspace_id IS NULL AND
									up2.role = 'Creator' AND
									up1.project_id IN ($item_1) AND
									u.role_id = 2 AND u.status = 1 AND u.is_activated = 1 AND u.is_deleted = 0
									ORDER BY ud.first_name, ud.last_name";
						}
						else{
							$sql_1 = "#Select People From: Specific Projects - NOT RESOURCER
									SELECT DISTINCT u.id AS user_id, CONCAT(ud.first_name, ' ', ud.last_name) AS full_name, ud.job_title, ud.profile_pic, ud.organization_id
									FROM user_permissions up1
									INNER JOIN users u ON up1.user_id = u.id
									INNER JOIN user_details ud ON u.id = ud.user_id
									INNER JOIN user_permissions up2 ON up1.project_id = up2.project_id AND up2.workspace_id IS NULL
									WHERE up1.workspace_id IS NULL AND up2.user_id = $user_id AND up1.project_id IN ($item_1) AND u.role_id = 2 AND u.status = 1 AND u.is_activated = 1 AND u.is_deleted = 0
									ORDER BY ud.first_name, ud.last_name";

						}
					}
				}


            	// CREATE SELECT FIELDS LIST
				$sel = [];

				$user_sql = $sql_1;
				$users_data = $this->UserPermission->query($user_sql);

				$main_sql = "SELECT ud.comp_type, ud.comp_id, ud.comp_name,";

				if(isset($users_data) && !empty($users_data)){
					// pr($users_data);
					$i= 1;
					$uscount = count($users_data);
					foreach($users_data as $us){
						$us_id = $us['u']['user_id'] ;
						//pr( $us_id);
						if($i > 1){
							$main_sql .=  ",";
						}
						$main_sql .= #GENERATE 4 COLUMNS FOR EACH SELECTED USER_ID AT RUNTIME:
						" $us_id AS '".$us_id."_userid',
						GROUP_CONCAT(CASE WHEN ud.user_id = $us_id THEN ud.comp_level ELSE NULL END) AS  '".$us_id."_level',
						GROUP_CONCAT(CASE WHEN ud.user_id = $us_id THEN ud.comp_experience ELSE NULL END) AS '".$us_id."_experience',
						SUM(CASE WHEN ud.user_id = $us_id THEN ud.comp_files ELSE NULL END) AS '".$us_id."_files' ";
						if($uscount < $i){
							$main_sql .=  ",";
						}

						$i++;
					}
				}

				$skill_part = '';
				$skill_from = '';
				if(isset($skills_dd) && !empty($skills_dd)){
					$skill_part = "#INCLUDE THIS SECTION IF USER HAS SELECTED SKILLS:
									#get skills
									SELECT 'Skill' AS comp_type, c.id AS comp_id, c.title AS comp_name FROM skills c
									#INCLUDE THIS LINE AND SKILL IDS IF USER HAS SELECTED SKILLS IN THE UI:
									WHERE c.id IN ('" . implode("', '", $skills_dd) . "')
									#END OF SECTION-----";

					$skill_from = "SELECT
											'Skill' AS comp_type,
											uc.skill_id AS comp_id,
											uc.user_id,
											cd.user_level AS comp_level,
											cd.user_experience AS comp_experience,
											COUNT(cp.id) AS comp_files
										FROM user_skills uc
										LEFT JOIN skill_details cd ON
											uc.skill_id = cd.skill_id
											AND uc.user_id = cd.user_id
										LEFT JOIN skill_pdfs cp ON
											uc.skill_id = cp.skill_id
											AND uc.user_id = cp.user_id
											AND cp.upload_status = 1
										GROUP BY uc.skill_id, uc.user_id";
				}

				$sub_part = '';
				$sub_from = '';

				if(isset($subjects_dd) && !empty($subjects_dd)){

					if( (isset($skills_dd) && !empty($skills_dd)) && (isset($subjects_dd) && !empty($subjects_dd)) ){
							$sub_part .= "UNION ALL ";
							$sub_from .= "UNION ALL ";
					}

					$sub_part .= "#REMOVE THIS LINE IF USER HAS NOT SELECTED SUBJECTS OR DOMAINS:

									#INCLUDE THIS SECTION IF USER HAS SELECTED SUBJECTS:
									#get subjects
									SELECT 'Subject' AS comp_type, c.id AS comp_id, c.title AS comp_name FROM subjects c
									#INCLUDE THIS LINE AND SUBJECT IDS IF USER HAS SELECTED SUBJECTS IN THE UI:
									WHERE c.id IN ('" . implode("', '", $subjects_dd) . "')
									#END OF SECTION-----";

					$sub_from .= "SELECT
											'Subject' AS comp_type,
											uc.subject_id AS comp_id,
											uc.user_id,
											cd.user_level AS comp_level,
											cd.user_experience AS comp_experience,
											COUNT(cp.id) AS comp_files
										FROM user_subjects uc
										LEFT JOIN subject_details cd ON
											uc.subject_id = cd.subject_id
											AND uc.user_id = cd.user_id
										LEFT JOIN subject_pdfs cp ON
											uc.subject_id = cp.subject_id
											AND uc.user_id = cp.user_id
											AND cp.upload_status = 1
										GROUP BY uc.subject_id, uc.user_id";
				}

				$dom_part = '';
				$dom_from = '';
				if(isset($domains_dd) && !empty($domains_dd)){

					if( (isset($skills_dd) && !empty($skills_dd)) || (isset($subjects_dd) && !empty($subjects_dd)) ){
							$dom_part .= "UNION ALL ";
							$dom_from .= "UNION ALL ";
					}
					$dom_part .= "#REMOVE THIS LINE IF USER HAS NOT SELECTED DOMAINS:

									#INCLUDE THIS SECTION IF USER HAS SELECTED DOMAINS:
									#get domains
									SELECT 'Domain' AS comp_type, c.id AS comp_id, c.title AS comp_name FROM knowledge_domains c
									#INCLUDE THIS LINE AND DOMAIN IDS IF USER HAS SELECTED DOMAINS IN THE UI:
									WHERE c.id IN ('" . implode("', '", $domains_dd) . "')
									#END OF SECTION-----";
					$dom_from .= "SELECT
											'Domain' AS comp_type,
											uc.domain_id AS comp_id,
											uc.user_id,
											cd.user_level AS comp_level,
											cd.user_experience AS comp_experience,
											COUNT(cp.id) AS comp_files
										FROM user_domains uc
										LEFT JOIN domain_details cd ON
											uc.domain_id = cd.domain_id
											AND uc.user_id = cd.user_id
										LEFT JOIN domain_pdfs cp ON
											uc.domain_id = cp.domain_id
											AND uc.user_id = cp.user_id
											AND cp.upload_status = 1
										GROUP BY uc.domain_id, uc.user_id";
				}


				$main_sql .="FROM
							#get skills/subjects/domains and users who have them
							(
								SELECT
									c.comp_type,
									c.comp_id,
									c.comp_name,
									uc.user_id,
									uc.comp_level,
									uc.comp_experience,
									uc.comp_files
								FROM
								#get skills/subjects/domains
								(

									$skill_part
									$sub_part
									$dom_part

									ORDER BY FIELD(comp_type,'Skill','Subject','Domain'), comp_name

									#CHANGE TO CURRENT COMPETENCY PAGE PARAMETERS AT RUNTIME:
									#LIMIT 0,50 #50 rows per page
								) AS c
								LEFT JOIN
								#get user skills/subjects/domains
								(
									SELECT
										uu.comp_type,
										uu.comp_id,
										uu.user_id,
										uu.comp_level,
										uu.comp_experience,
										uu.comp_files
									FROM
									(
										#INCLUDE THIS SECTION IF USER HAS SELECTED SKILLS:
										#get user skills
										$skill_from
										#END OF SECTION-----

										#REMOVE THIS LINE IF USER HAS NOT SELECTED SUBJECTS OR DOMAINS:


										#INCLUDE THIS SECTION IF USER HAS SELECTED SKILLS
										#get user subjects
										$sub_from
										#END OF SECTION-----

										#REMOVE THIS LINE IF USER HAS NOT SELECTED DOMAINS:


										#INCLUDE THIS SECTION IF USER HAS SELECTED DOMAINS:
										#get user domains
										$dom_from
										#END OF SECTION-----
									) AS uu
									INNER JOIN
									#get users
									(
										# CHANGE THIS SUB QUERY BASED ON SELECTED USERS IN UI (SEE ADDITIONAL SQL FILE - SELECT PEOPLE FROM)
										#Select People From: All Community
										$sql_1
										#-----

										#CHANGE TO CURRENT USER PAGE PARAMETERS AT RUNTIME:
										#LIMIT 0, 25 #25 user columns per page
									) AS u ON
										uu.user_id = u.user_id
								) AS uc ON
									c.comp_type = uc.comp_type
									AND c.comp_id = uc.comp_id
							) AS ud
							GROUP BY ud.comp_type, ud.comp_id
							ORDER BY FIELD(ud.comp_type,'Skill','Subject','Domain'), ud.comp_name";

				$data = $this->UserPermission->query($main_sql);

			}
        }


		echo json_encode(count($data));
        exit;

    }

	public function get_compare_users() {
		if ($this->request->isAjax()) {
            $this->layout = 'ajax';
            $this->loadModel('UserPermission');
            $response = ['success' => false];
            $data = [];
            $user_id = $this->Session->read('Auth.User.id');
           // $resourcer = $this->Session->read('Auth.User.UserDetail.resourcer');
            if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;
				$people_from = $post["people_from"];
	            $item_1 = (isset($post["item_1"]) && !empty($post["item_1"])) ? $post["item_1"] : [];

				$sql_1  = "";

				if(isset($people_from) && !empty($people_from)){
					if($people_from == "community"){
						$sql_1 = "#Select People From: All Community
								SELECT u.id AS user_id, CONCAT(ud.first_name, ' ', ud.last_name) AS full_name, ud.job_title, ud.profile_pic, ud.organization_id FROM users u
								INNER JOIN user_details ud ON u.id = ud.user_id
								WHERE u.role_id = 2 AND u.status = 1 AND u.is_activated = 1 AND u.is_deleted = 0
								ORDER BY ud.first_name, ud.last_name";
					}
					else if($people_from == "organizations"){
						$item_1 = implode(",", $item_1);
						$sql_1 = "#Select People From: Specific Organizations
								SELECT u.id AS user_id, CONCAT(ud.first_name, ' ', ud.last_name) AS full_name, ud.job_title, ud.profile_pic, ud.organization_id FROM users u
								INNER JOIN user_details ud ON u.id = ud.user_id
								WHERE u.role_id = 2 AND u.status = 1 AND u.is_activated = 1 AND u.is_deleted = 0 AND ud.organization_id IN ($item_1)
								ORDER BY ud.first_name, ud.last_name";

					}
					else if($people_from == "locations"){
						$item_1 = implode(",", $item_1);
						$sql_1 = "#Select People From: Specific Locations
								SELECT u.id AS user_id, CONCAT(ud.first_name, ' ', ud.last_name) AS full_name, ud.job_title, ud.profile_pic, ud.organization_id FROM users u
								INNER JOIN user_details ud ON u.id = ud.user_id
								WHERE u.role_id = 2 AND u.status = 1 AND u.is_activated = 1 AND u.is_deleted = 0 AND ud.location_id IN ($item_1)
								ORDER BY ud.first_name, ud.last_name";

					}
					else if($people_from == "departments"){
						$item_1 = implode(",", $item_1);
						$sql_1 = "#Select People From: Specific Departments
								SELECT u.id AS user_id, CONCAT(ud.first_name, ' ', ud.last_name) AS full_name, ud.job_title, ud.profile_pic, ud.organization_id FROM users u
								INNER JOIN user_details ud ON u.id = ud.user_id
								WHERE u.role_id = 2 AND u.status = 1 AND u.is_activated = 1 AND u.is_deleted = 0 AND ud.department_id IN ($item_1)
								ORDER BY ud.first_name, ud.last_name";

					}
					else if($people_from == "users"){
						$item_1 = implode(",", $item_1);
						$sql_1 = "#Select People From: Specific People
								SELECT u.id AS user_id, CONCAT(ud.first_name, ' ', ud.last_name) AS full_name, ud.job_title, ud.profile_pic, ud.organization_id FROM users u
								INNER JOIN user_details ud ON u.id = ud.user_id
								WHERE u.role_id = 2 AND u.status = 1 AND u.is_activated = 1 AND u.is_deleted = 0 AND u.id IN ($item_1)
								ORDER BY ud.first_name, ud.last_name";

					}
					else if($people_from == "tags"){
						$sql_1 = "#Select People From: Specific Tags
								SELECT DISTINCT t.tagged_user_id, u.id AS user_id, CONCAT(ud.first_name, ' ', ud.last_name) AS full_name, ud.job_title, ud.profile_pic, ud.organization_id FROM tags t
								INNER JOIN users u ON t.tagged_user_id = u.id
								INNER JOIN user_details ud ON u.id = ud.user_id
								WHERE u.role_id = 2 AND u.status = 1 AND u.is_activated = 1 AND u.is_deleted = 0 AND t.user_id = $user_id AND t.tag IN ('" . implode("', '", $item_1) . "')
								ORDER BY ud.first_name, ud.last_name";

					}
					else if($people_from == "skills"){
						$item_1 = implode(",", $item_1);
						$sql_1 = "#Select People From: Specific Skills
								SELECT DISTINCT u.id AS user_id, CONCAT(ud.first_name, ' ', ud.last_name) AS full_name, ud.job_title, ud.profile_pic, ud.organization_id FROM users u
								INNER JOIN user_details ud ON u.id = ud.user_id
								LEFT JOIN user_skills us ON u.id = us.user_id
								WHERE u.role_id = 2 AND u.status = 1 AND u.is_activated = 1 AND u.is_deleted = 0 AND us.skill_id IN ($item_1)
								ORDER BY ud.first_name, ud.last_name";

					}
					else if($people_from == "subjects"){
						$item_1 = implode(",", $item_1);
						$sql_1 = "#Select People From: Specific Subjects
								SELECT DISTINCT u.id AS user_id, CONCAT(ud.first_name, ' ', ud.last_name) AS full_name, ud.job_title, ud.profile_pic, ud.organization_id FROM users u
								INNER JOIN user_details ud ON u.id = ud.user_id
								LEFT JOIN user_subjects us ON u.id = us.user_id
								WHERE u.role_id = 2 AND u.status = 1 AND u.is_activated = 1 AND u.is_deleted = 0 AND us.subject_id IN ($item_1)
								ORDER BY ud.first_name, ud.last_name";

					}
					else if($people_from == "domains"){
						$item_1 = implode(",", $item_1);
						$sql_1 = "#Select People From: Specific Domains
								SELECT DISTINCT u.id AS user_id, CONCAT(ud.first_name, ' ', ud.last_name) AS full_name, ud.job_title, ud.profile_pic, ud.organization_id FROM users u
								INNER JOIN user_details ud ON u.id = ud.user_id
								LEFT JOIN user_domains us ON u.id = us.user_id
								WHERE u.role_id = 2 AND u.status = 1 AND u.is_activated = 1 AND u.is_deleted = 0 AND us.domain_id IN ($item_1)
								ORDER BY ud.first_name, ud.last_name";

					}
					else if($people_from == "all_projects"){
						$sql_1 = "#Select People From: All My Projects
									SELECT DISTINCT u.id AS user_id, CONCAT(ud.first_name, ' ', ud.last_name) AS full_name, ud.job_title, ud.profile_pic, ud.organization_id
									FROM user_permissions up1
									INNER JOIN users u ON up1.user_id = u.id
									INNER JOIN user_details ud ON u.id = ud.user_id
									INNER JOIN user_permissions up2 ON up1.project_id = up2.project_id AND up2.workspace_id IS NULL
									WHERE up1.workspace_id IS NULL AND up2.user_id = $user_id AND u.role_id = 2 AND u.status = 1 AND u.is_activated = 1 AND u.is_deleted = 0
									ORDER BY ud.first_name, ud.last_name";

					}
					else if($people_from == "created_projects"){
						$sql_1 = "#Select People From: Projects I Created
								SELECT DISTINCT u.id AS user_id, CONCAT(ud.first_name, ' ', ud.last_name) AS full_name, ud.job_title, ud.profile_pic, ud.organization_id
								FROM user_permissions up1
								INNER JOIN users u ON up1.user_id = u.id
								INNER JOIN user_details ud ON u.id = ud.user_id
								INNER JOIN user_permissions up2 ON up1.project_id = up2.project_id AND up2.workspace_id IS NULL
								WHERE up1.workspace_id IS NULL AND up2.user_id = $user_id AND up2.role = 'Creator' AND u.role_id = 2 AND u.status = 1 AND u.is_activated = 1 AND u.is_deleted = 0
								ORDER BY ud.first_name, ud.last_name";

					}
					else if($people_from == "owner_projects"){
						$sql_1 = "#Select People From: Projects I Own
								SELECT DISTINCT u.id AS user_id, CONCAT(ud.first_name, ' ', ud.last_name) AS full_name, ud.job_title, ud.profile_pic, ud.organization_id
								FROM user_permissions up1
								INNER JOIN users u ON up1.user_id = u.id
								INNER JOIN user_details ud ON u.id = ud.user_id
								INNER JOIN user_permissions up2 ON up1.project_id = up2.project_id AND up2.workspace_id IS NULL
								WHERE up1.workspace_id IS NULL AND up2.user_id = $user_id AND up2.role IN ('Creator', 'Owner', 'Group Owner') AND u.role_id = 2 AND u.status = 1 AND u.is_activated = 1 AND u.is_deleted = 0
								ORDER BY ud.first_name, ud.last_name";

					}
					else if($people_from == "shared_projects"){
						$sql_1 = "#Select People From: Projects Shared with Me
								SELECT DISTINCT u.id AS user_id, CONCAT(ud.first_name, ' ', ud.last_name) AS full_name, ud.job_title, ud.profile_pic, ud.organization_id
								FROM user_permissions up1
								INNER JOIN users u ON up1.user_id = u.id
								INNER JOIN user_details ud ON u.id = ud.user_id
								INNER JOIN user_permissions up2 ON up1.project_id = up2.project_id AND up2.workspace_id IS NULL
								WHERE up1.workspace_id IS NULL AND up2.user_id = $user_id AND up2.role IN ('Sharer', 'Group Sharer') AND u.role_id = 2 AND u.status = 1 AND u.is_activated = 1 AND u.is_deleted = 0
								ORDER BY ud.first_name, ud.last_name";

					}
					else if($people_from == "project"){
						$item_1 = implode(",", $item_1);
						if(!empty($resourcer)){
							$sql_1 = "#Select People From: Specific Projects - RESOURCER
									SELECT DISTINCT u.id AS user_id, CONCAT(ud.first_name, ' ', ud.last_name) AS full_name, ud.job_title, ud.profile_pic, ud.organization_id
									FROM user_permissions up1
									INNER JOIN users u ON up1.user_id = u.id
									INNER JOIN user_details ud ON u.id = ud.user_id
									INNER JOIN user_permissions up2 ON up1.project_id = up2.project_id AND up2.workspace_id IS NULL
									WHERE up1.workspace_id IS NULL AND
									up2.role = 'Creator' AND
									up1.project_id IN ($item_1) AND
									u.role_id = 2 AND u.status = 1 AND u.is_activated = 1 AND u.is_deleted = 0
									ORDER BY ud.first_name, ud.last_name";
						}
						else{
							$sql_1 = "#Select People From: Specific Projects - NOT RESOURCER
									SELECT DISTINCT u.id AS user_id, CONCAT(ud.first_name, ' ', ud.last_name) AS full_name, ud.job_title, ud.profile_pic, ud.organization_id
									FROM user_permissions up1
									INNER JOIN users u ON up1.user_id = u.id
									INNER JOIN user_details ud ON u.id = ud.user_id
									INNER JOIN user_permissions up2 ON up1.project_id = up2.project_id AND up2.workspace_id IS NULL
									WHERE up1.workspace_id IS NULL AND up2.user_id = $user_id AND up1.project_id IN ($item_1) AND u.role_id = 2 AND u.status = 1 AND u.is_activated = 1 AND u.is_deleted = 0
									ORDER BY ud.first_name, ud.last_name";

						}
					}
				}

				$user_sql = $sql_1;
				$users_data = $this->UserPermission->query($user_sql);

			}
        }

        $user_list = '';
        if(isset($users_data) && !empty($users_data)){
        	$user_list = Set::extract($users_data, '{n}.u.user_id');
        	$user_list = implode(',', $user_list);
        }
        $response = ['users' => $user_list, 'total' => count($users_data)];
		echo json_encode($response);
        exit;
    }


	public function competencies_users() {
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

	public function saveas_compare_view() {
		if ($this->request->isAjax()) {
			$this->layout = false;
			$viewData = [];
			$this->loadModel('UserViewsCompare');

			$type = '';
			if(isset($this->params['named']['type']) && !empty($this->params['named']['type']) ){
				$type = $this->params['named']['type'];
			}
			$viewData['type'] = $type;

			if ($this->request->is('post') || $this->request->is('put')) {
            	$post = $this->request->data;
            	// pr($post, 1);
            	$response['success'] = false;

            	if(isset($post['selection']) && !empty($post['selection'])) {
            		$selection = json_encode($post['selection']);
					$save['UserViewsCompare']['name'] = $post['view_name'];
					$save['UserViewsCompare']['user_id'] = $this->user_id;
					$save['UserViewsCompare']['created'] = date('Y-m-d H:i:s');
					$save['UserViewsCompare']['selection'] = $selection;
					// pr($save, 1);
	        		if($this->UserViewsCompare->save($save)){
	        			$response['success'] = true;
	        		}
	        	}
            	echo json_encode($response);
            	exit;
            }

			$this->set($viewData);
			$this->render('partials/save_compare_view');
		}
    }

	public function save_compare_view() {
		if ($this->request->isAjax()) {
			$this->layout = false;
			$viewData = [];
			$this->loadModel('UserViewsCompare');

			if ($this->request->is('post') || $this->request->is('put')) {
            	$post = $this->request->data;
            	// pr($post, 1);
            	$response['success'] = false;

            	if(isset($post['view_id']) && !empty($post['view_id'])) {
            		$selections = json_encode($post['selection']);
            		$view_id = $post['view_id'];
					$save['UserViewsCompare']['id'] = $view_id;
					$save['UserViewsCompare']['selection'] = $selections;
            		// pr($save, 1);

	        		if($this->UserViewsCompare->save($save)) {
	        			$response['success'] = true;
	        		}
	        	}
            }
        	echo json_encode($response);
        	exit;
		}
    }

	public function get_compare_view() {
		if ($this->request->isAjax()) {
			$this->layout = false;

        	$response = ['success' => false, 'content' => []];

			if ($this->request->is('post') || $this->request->is('put')) {
            	$post = $this->request->data;
            	// pr($post, 1);
            	$this->loadModel('UserViewsCompare');

            	$conditions['user_id'] = $this->user_id;
				$view_detail = $this->UserViewsCompare->find('all', ['conditions' => $conditions, 'order' => ['name ASC'] ]);

				$all_data = [];
				if (isset($view_detail) && !empty($view_detail)) {
					foreach ($view_detail as $u) {
						$all_data[] = ['value' => $u['UserViewsCompare']['id'], 'label' => $u['UserViewsCompare']['name']];
					}
					$response['content'] = $all_data;
				}

            	if(isset($post['id']) && !empty($post['id'])) {
            		$conditions['id'] = $post['id'];
            		$view_detail = $this->UserViewsCompare->find('first', [
            								'conditions' => $conditions,
            								'fields' => ['id', 'selection'],
            								'order' => ['name ASC']
            							]);
            		$response['content'] = $view_detail;
            	}

				$response['success'] = true;

            }
		}
    	echo json_encode($response);
    	exit;
    }

	public function delete_compare_view($view_id = null) {
		if ($this->request->isAjax()) {
			$this->layout = false;

        	$response = ['success' => false];

			if ($this->request->is('post') || $this->request->is('put')) {
            	$post = $this->request->data;
            	if(isset($post['view_id']) && !empty($post['view_id'])) {
	            	$this->loadModel('UserViewsCompare');
					if($this->UserViewsCompare->delete(['id' => $post['view_id']])){
						$response['success'] = true;
					}
				}
            }
		}
    	echo json_encode($response);
    	exit;
    }

	/* WATCH */
	public function get_watch() {
		if ($this->request->isAjax()) {
            $this->layout = 'ajax';
            $this->loadModel('UserPermission');
            $response = ['success' => false];
            $data = [];
            $watch_limit = $this->watch_limit;
            $user_id = $this->Session->read('Auth.User.id');
            $coloumn = null;
            if ($this->request->is('post') || $this->request->is('put')) {
            	$post = $this->request->data;
            	$page = (isset($post['page']) && !empty($post['page'])) ? $post['page'] : 0;

            	$limit = "";
            	if(isset($post['count']) && !empty($post['count'])){
            		$limit = "";
            	}
            	else{
	            	$limit = "LIMIT 0, $watch_limit";
	            	if(isset($page) && !empty($page)) {
	            		$limit = "LIMIT $page, $watch_limit";
	            	}
	            }

	            $order_by = "ORDER BY first_name ASC, last_name ASC";
				$group_order = "ORDER BY CASE
									WHEN comp_type='Skill' THEN 1
									WHEN comp_type='Subject' THEN 2
									WHEN comp_type='Domain' THEN 3
								END, comp_name ASC";
				if( (isset($post['order']) && !empty($post['order'])) && (isset($post['coloumn']) && !empty($post['coloumn'])) ){
					$order = $post['order'];
					$coloumn = $post['coloumn'];

					$order_by = "ORDER BY first_name ASC, last_name ASC, $coloumn $order";

					if($post['coloumn'] == 'matches'){
						$order_by = "ORDER BY $coloumn $order";
					}

					if($post['coloumn'] == 'first_name'){
						$order_by = "ORDER BY first_name $order, last_name $order";
					}
					if($post['coloumn'] == 'last_name'){
						$order_by = "ORDER BY last_name $order, first_name $order";
					}
					if($post['coloumn'] == 'comp_experience'){
						$order_by = "ORDER BY first_name ASC, last_name ASC, CASE
										WHEN comp_experience='1' THEN 1
										WHEN comp_experience='2' THEN 2
										WHEN comp_experience='3' THEN 3
										WHEN comp_experience='4' THEN 4
										WHEN comp_experience='5' THEN 5
										WHEN comp_experience='6-10' THEN 6
										WHEN comp_experience='11-15' THEN 7
										WHEN comp_experience='16-20' THEN 8
										WHEN comp_experience='Over 20' THEN 9
									END $order";
						$group_order = "ORDER BY CASE
											WHEN comp_experience='1' THEN 1
											WHEN comp_experience='2' THEN 2
											WHEN comp_experience='3' THEN 3
											WHEN comp_experience='4' THEN 4
											WHEN comp_experience='5' THEN 5
											WHEN comp_experience='6-10' THEN 6
											WHEN comp_experience='11-15' THEN 7
											WHEN comp_experience='16-20' THEN 8
											WHEN comp_experience='Over 20' THEN 9
										END $order";
					}
					if($post['coloumn'] == 'comp_level'){
						$order_by = "ORDER BY first_name ASC, last_name ASC, CASE
										WHEN comp_level='Beginner' THEN 1
										WHEN comp_level='Intermediate' THEN 2
										WHEN comp_level='Advanced' THEN 3
										WHEN comp_level='Expert' THEN 4
									END $order";
						$group_order = "ORDER BY CASE
											WHEN comp_level='Beginner' THEN 1
											WHEN comp_level='Intermediate' THEN 2
											WHEN comp_level='Advanced' THEN 3
											WHEN comp_level='Expert' THEN 4
										END $order";
					}
					if($post['coloumn'] == 'comp_type'){
						$order_by = "ORDER BY first_name ASC, last_name ASC, CASE
										WHEN comp_type='Skill' THEN 1
										WHEN comp_type='Subject' THEN 2
										WHEN comp_type='Domain' THEN 3
									END $order";
						$group_order = "ORDER BY CASE
											WHEN comp_type='Skill' THEN 1
											WHEN comp_type='Subject' THEN 2
											WHEN comp_type='Domain' THEN 3
										END $order";
					}
					if($post['coloumn'] == 'comp_name'){
						$group_order = "ORDER BY comp_name $order";
					}
				}

            	if( (isset($post['skills']) && !empty($post['skills'])) || (isset($post['subjects']) && !empty($post['subjects'])) || (isset($post['domains']) && !empty($post['domains'])) ){

            		$sql11 = [];
            		$sql1 = $sql2 = $sql3 = $sql4 = $sql5 = $sql6 = $sql7 = $sql8 = $sql9 = $sql10 = $sql12 = $sql13 = "";

            		if(isset($post['skills']) && !empty($post['skills'])){
            			$skills = implode(',', $post['skills']);
            			$skill_levels = "'" . implode("', '", $post['skill_levels']) . "'";
            			$skill_exps = "'" . implode("', '", $post['skill_exps']) . "'";

            			$sql1 = "SELECT
					                'Skill' AS comp_type,
					                uc.user_id
					            FROM user_skills uc
					            LEFT JOIN skill_details cd ON
					                uc.skill_id = cd.skill_id
					                AND uc.user_id = cd.user_id
					            LEFT JOIN skills c ON
					                uc.skill_id = c.id
					            WHERE
					                (uc.skill_id IN ($skills) #skill id UI selections
					                 AND cd.user_level IN ($skill_levels) #skill level UI selections
					                 AND cd.user_experience IN ($skill_exps) #skill experience UI selections
					                )";

		                $sql8 = "SELECT
						            'Skill' AS comp_type,
						            uc.skill_id AS comp_id,
						            c.title AS comp_name,
						            uc.user_id,
						            cd.user_level AS comp_level,
						            cd.user_experience AS comp_experience,
						            uc.created AS comp_added_on
						        FROM user_skills uc
						        LEFT JOIN skill_details cd ON
						            uc.skill_id = cd.skill_id
						            AND uc.user_id = cd.user_id
						        LEFT JOIN skills c ON
						            uc.skill_id = c.id
						        WHERE
						            (uc.skill_id IN ($skills) #skill id selections
						             AND cd.user_level IN ($skill_levels) #skill level selections
						             AND cd.user_experience IN ($skill_exps) #skill experience selections
						            )";

			            $sql11[] = "comp_counts.skill_count > 0";
            		}
            		if(isset($post['subjects']) && !empty($post['subjects'])){
            			$subjects = implode(',', $post['subjects']);
            			$subject_levels = "'" . implode("', '", $post['subject_levels']) . "'";
            			$subject_exps = "'" . implode("', '", $post['subject_exps']) . "'";

            			if(isset($post['skills']) && !empty($post['skills'])) {
            				$sql2 = "UNION ALL ";
            			}
            			$sql2 .= "SELECT
					                'Subject' AS comp_type,
					                uc.user_id
					            FROM user_subjects uc
					            LEFT JOIN subject_details cd ON
					                uc.subject_id = cd.subject_id
					                AND uc.user_id = cd.user_id
					            LEFT JOIN subjects c ON
					                uc.subject_id = c.id
					            WHERE
					                (uc.subject_id IN ($subjects) #subject id UI selections
					                 AND cd.user_level IN ($subject_levels) #subject level UI selections
					                 AND cd.user_experience IN ($subject_exps) #subject experience UI selections
					                )";
            			if(isset($post['skills']) && !empty($post['skills'])) {
            				$sql9 = "UNION ALL ";
            			}
            			$sql9 .= "SELECT
						            'Subject' AS comp_type,
						            uc.subject_id AS comp_id,
						            c.title AS comp_name,
						            uc.user_id,
						            cd.user_level AS comp_level,
						            cd.user_experience AS comp_experience,
						            uc.created AS comp_added_on
						        FROM user_subjects uc
						        LEFT JOIN subject_details cd ON
						            uc.subject_id = cd.subject_id
						            AND uc.user_id = cd.user_id
						        LEFT JOIN subjects c ON
						            uc.subject_id = c.id
						        WHERE
						            (uc.subject_id IN ($subjects) #subject id selections
						             AND cd.user_level IN ($subject_levels) #subject level selections
						             AND cd.user_experience IN ($subject_exps) #subject experience selections
						            )";
			            $sql11[] = "comp_counts.subject_count > 0";
            		}
            		if(isset($post['domains']) && !empty($post['domains'])){
            			$domains = implode(',', $post['domains']);
            			$domain_levels = "'" . implode("', '", $post['domain_levels']) . "'";
            			$domain_exps = "'" . implode("', '", $post['domain_exps']) . "'";

            			if((isset($post['skills']) && !empty($post['skills'])) || (isset($post['subjects']) && !empty($post['subjects']))){
            				$sql3 = "UNION ALL ";
            			}
            			$sql3 .= "SELECT
					                'Domain' AS comp_type,
					                uc.user_id
					            FROM user_domains uc
					            LEFT JOIN domain_details cd ON
					                uc.domain_id = cd.domain_id
					                AND uc.user_id = cd.user_id
					            LEFT JOIN knowledge_domains c ON
					                uc.domain_id = c.id
					            WHERE
					                (uc.domain_id IN ($domains) #domain id selections
					                 AND cd.user_level IN ($domain_levels) #domain level selections
					                 AND cd.user_experience IN ($domain_exps) #domain experience selections
					                )";
            			if((isset($post['skills']) && !empty($post['skills'])) || (isset($post['subjects']) && !empty($post['subjects']))){
            				$sql10 = "UNION ALL ";
            			}
            			$sql10 .= "SELECT
						            'Domain' AS comp_type,
						            uc.domain_id AS comp_id,
						            c.title AS comp_name,
						            uc.user_id,
						            cd.user_level AS comp_level,
						            cd.user_experience AS comp_experience,
						            uc.created AS comp_added_on
						        FROM user_domains uc
						        LEFT JOIN domain_details cd ON
						            uc.domain_id = cd.domain_id
						            AND uc.user_id = cd.user_id
						        LEFT JOIN knowledge_domains c ON
						            uc.domain_id = c.id
						        WHERE
						            (uc.domain_id IN ($domains) #domain id selections
						             AND cd.user_level IN ($domain_levels) #domain level selections
						             AND cd.user_experience IN ($domain_exps) #domain experience selections
						            )";
			            $sql11[] = "comp_counts.domain_count > 0";
            		}
            		if(isset($sql11) && !empty($sql11)) {
            			$sql12 = implode(' AND ', $sql11);
            		}
            		$query = "SELECT
							    comp_users.user_id,
							    comp_users.matches,
							    CONCAT_WS(' ', ud.first_name , ud.last_name) AS fullname,
							    ud.profile_pic,
							    ud.job_title,
							    ud.organization_id,
							    cm.comp_type,
							    cm.comp_id,
							    cm.comp_name,
							    cm.comp_level,
							    cm.comp_experience,
							    cm.comp_added_on,
							    GROUP_CONCAT(DISTINCT( CONCAT(comp_type,'$$',comp_id,'$$',comp_name,'$$',comp_level,'$$',comp_experience,'$$',comp_added_on))
							    	$group_order SEPARATOR '$$$'
						    	) as competencies
							FROM
							#get list of users who have the selected skills/subjects/domains
							(
							    SELECT
							        comp_counts.user_id,
							        (comp_counts.skill_count+comp_counts.subject_count+comp_counts.domain_count) AS matches
							        /*comp_counts.skill_count,
							        comp_counts.subject_count,
							        comp_counts.domain_count*/
							    FROM
							    (
							        SELECT
							            uc.user_id,
							            SUM(uc.comp_type = 'Skill') AS skill_count,
							            SUM(uc.comp_type = 'Subject') AS subject_count,
							            SUM(uc.comp_type = 'Domain') domain_count
							        FROM
							        (
							            #INCLUDE THIS SECTION IF USER HAS SELECTED SKILLS:
							            #get all user skills sql1
							            $sql1
							            #END OF SECTION-----

							            #REMOVE THIS LINE IF USER HAS NOT SELECTED SUBJECTS OR DOMAINS: sql4
							            #UNION ALL

							            #INCLUDE THIS SECTION IF USER HAS SELECTED SUBJECTS:
							            #get user subjects sql2
							            $sql2
							            #END OF SECTION-----

							            #REMOVE THIS LINE IF USER HAS NOT SELECTED SKILLS AND/OR SUBJECTS: sql4
							            #UNION ALL

							            #INCLUDE THIS SECTION IF USER HAS SELECTED DOMAINS:
							            #get user domains sql3
							            $sql3
							            #END OF SECTION-----
							        ) AS uc
							        GROUP BY uc.user_id
							    ) AS comp_counts
							    WHERE
							        $sql12
							) AS comp_users
							LEFT JOIN
							(
							    SELECT
							        uc.comp_type,
							        uc.comp_id,
							        uc.comp_name,
							        uc.user_id,
							        uc.comp_level,
							        uc.comp_experience,
							        uc.comp_added_on
							    FROM
							    (
							        #INCLUDE THIS SECTION IF USER HAS SELECTED SKILLS:
							        #get all user skills sql8
							        $sql8
							        #END OF SECTION-----

							        #REMOVE THIS LINE IF USER HAS NOT SELECTED SUBJECTS OR DOMAINS: sql11
							        #UNION ALL

							        #INCLUDE THIS SECTION IF USER HAS SELECTED SUBJECTS:
							        #get user subjects sql9
							        $sql9
							        #END OF SECTION-----

							        #REMOVE THIS LINE IF USER HAS NOT SELECTED SKILLS AND/OR SUBJECTS: sql11
							        #UNION ALL

							        #INCLUDE THIS SECTION IF USER HAS SELECTED DOMAINS:
							        #get user domains sql10
							        $sql10
							        #END OF SECTION-----
							    ) AS uc
							) AS cm ON
							    comp_users.user_id = cm.user_id
							LEFT JOIN users u ON
							    u.role_id = 2 AND u.status = 1 AND u.is_deleted = 0 AND u.is_activated = 1 AND cm.user_id = u.id
							LEFT JOIN user_details ud ON
							    u.id = ud.user_id
						    WHERE u.role_id = 2 AND u.status = 1 AND u.is_deleted = 0 AND u.is_activated = 1
						    GROUP BY u.id
							$order_by
							$limit
						";
            		// pr($query, 1);
            		$data = $this->User->query($query);


            	}
            }
            if(isset($post['count']) && !empty($post['count'])){

        			// CREATE JSON TO SAVE THE RESULT
            		/*$json = [];
            		if(isset($data) && !empty($data)){
	            		foreach ($data as $key => $details) {
	            			$user_id = $details['comp_users']['user_id'];
	            			$comp = $details[0]['competencies'];
							$competency = explode('$$$', $comp );
							$comp_ids = ['skills' => [], 'subjects' => [], 'domains' => []];
							foreach ($competency as $key => $cdata) {
								$values = explode('$$', $cdata);
								$value = [];
								$comp_type = array_shift($values);
								$comp_id = array_shift($values);
								if($comp_type == 'Skill'){
									$comp_ids['skills'][] = $comp_id;
								}
								if($comp_type == 'Subject'){
									$comp_ids['subjects'][] = $comp_id;
								}
								if($comp_type == 'Domain'){
									$comp_ids['domains'][] = $comp_id;
								}
							}
							$json[$user_id] = [
									'user_id' => $user_id,
									'competencies' => [
											'skills' => implode(',', $comp_ids['skills']),
											'skills_count' => count($comp_ids['skills']),
											'subjects' => implode(',', $comp_ids['subjects']),
											'subjects_count' => count($comp_ids['subjects']),
											'domains' => implode(',', $comp_ids['domains']),
											'domains_count' => count($comp_ids['domains'])
										]
								];
	            		}
	            	} */

				echo json_encode(count($data));
        		exit;
			}
			else{
				$this->set('coloumn', $coloumn);
		        $this->set("data", $data);
				$this->render('partials/watch/get_watch');
			}
        }
    }


	public function get_watch_users() {
		if ($this->request->isAjax()) {
            $this->layout = 'ajax';
            $this->loadModel('UserPermission');
            $response = ['success' => false];
            $data = [];
            $userList = 0;
            $watch_limit = $this->watch_limit;
            $user_id = $this->Session->read('Auth.User.id');
            if ($this->request->is('post') || $this->request->is('put')) {
            	$post = $this->request->data;
            	$page = (isset($post['page']) && !empty($post['page'])) ? $post['page'] : 0;

            	$limit = "";

	            $order_by = "ORDER BY first_name ASC, last_name ASC";

            	if( (isset($post['skills']) && !empty($post['skills'])) || (isset($post['subjects']) && !empty($post['subjects'])) || (isset($post['domains']) && !empty($post['domains'])) ){

            		$sql11 = [];
            		$sql1 = $sql2 = $sql3 = $sql4 = $sql5 = $sql6 = $sql7 = $sql8 = $sql9 = $sql10 = $sql12 = $sql13 = "";

            		if(isset($post['skills']) && !empty($post['skills'])){
            			$skills = implode(',', $post['skills']);
            			$skill_levels = "'" . implode("', '", $post['skill_levels']) . "'";
            			$skill_exps = "'" . implode("', '", $post['skill_exps']) . "'";

            			$sql1 = "SELECT
					                'Skill' AS comp_type,
					                uc.user_id
					            FROM user_skills uc
					            LEFT JOIN skill_details cd ON
					                uc.skill_id = cd.skill_id
					                AND uc.user_id = cd.user_id
					            LEFT JOIN skills c ON
					                uc.skill_id = c.id
					            WHERE
					                (uc.skill_id IN ($skills) #skill id UI selections
					                 AND cd.user_level IN ($skill_levels) #skill level UI selections
					                 AND cd.user_experience IN ($skill_exps) #skill experience UI selections
					                )";

		                $sql8 = "SELECT
						            'Skill' AS comp_type,
						            uc.skill_id AS comp_id,
						            c.title AS comp_name,
						            uc.user_id,
						            cd.user_level AS comp_level,
						            cd.user_experience AS comp_experience,
						            uc.created AS comp_added_on
						        FROM user_skills uc
						        LEFT JOIN skill_details cd ON
						            uc.skill_id = cd.skill_id
						            AND uc.user_id = cd.user_id
						        LEFT JOIN skills c ON
						            uc.skill_id = c.id
						        WHERE
						            (uc.skill_id IN ($skills) #skill id selections
						             AND cd.user_level IN ($skill_levels) #skill level selections
						             AND cd.user_experience IN ($skill_exps) #skill experience selections
						            )";

			            $sql11[] = "comp_counts.skill_count > 0";
            		}
            		if(isset($post['subjects']) && !empty($post['subjects'])){
            			$subjects = implode(',', $post['subjects']);
            			$subject_levels = "'" . implode("', '", $post['subject_levels']) . "'";
            			$subject_exps = "'" . implode("', '", $post['subject_exps']) . "'";

            			if(isset($post['skills']) && !empty($post['skills'])) {
            				$sql2 = "UNION ALL ";
            			}
            			$sql2 .= "SELECT
					                'Subject' AS comp_type,
					                uc.user_id
					            FROM user_subjects uc
					            LEFT JOIN subject_details cd ON
					                uc.subject_id = cd.subject_id
					                AND uc.user_id = cd.user_id
					            LEFT JOIN subjects c ON
					                uc.subject_id = c.id
					            WHERE
					                (uc.subject_id IN ($subjects) #subject id UI selections
					                 AND cd.user_level IN ($subject_levels) #subject level UI selections
					                 AND cd.user_experience IN ($subject_exps) #subject experience UI selections
					                )";
            			if(isset($post['skills']) && !empty($post['skills'])) {
            				$sql9 = "UNION ALL ";
            			}
            			$sql9 .= "SELECT
						            'Subject' AS comp_type,
						            uc.subject_id AS comp_id,
						            c.title AS comp_name,
						            uc.user_id,
						            cd.user_level AS comp_level,
						            cd.user_experience AS comp_experience,
						            uc.created AS comp_added_on
						        FROM user_subjects uc
						        LEFT JOIN subject_details cd ON
						            uc.subject_id = cd.subject_id
						            AND uc.user_id = cd.user_id
						        LEFT JOIN subjects c ON
						            uc.subject_id = c.id
						        WHERE
						            (uc.subject_id IN ($subjects) #subject id selections
						             AND cd.user_level IN ($subject_levels) #subject level selections
						             AND cd.user_experience IN ($subject_exps) #subject experience selections
						            )";
			            $sql11[] = "comp_counts.subject_count > 0";
            		}
            		if(isset($post['domains']) && !empty($post['domains'])){
            			$domains = implode(',', $post['domains']);
            			$domain_levels = "'" . implode("', '", $post['domain_levels']) . "'";
            			$domain_exps = "'" . implode("', '", $post['domain_exps']) . "'";

            			if((isset($post['skills']) && !empty($post['skills'])) || (isset($post['subjects']) && !empty($post['subjects']))){
            				$sql3 = "UNION ALL ";
            			}
            			$sql3 .= "SELECT
					                'Domain' AS comp_type,
					                uc.user_id
					            FROM user_domains uc
					            LEFT JOIN domain_details cd ON
					                uc.domain_id = cd.domain_id
					                AND uc.user_id = cd.user_id
					            LEFT JOIN knowledge_domains c ON
					                uc.domain_id = c.id
					            WHERE
					                (uc.domain_id IN ($domains) #domain id selections
					                 AND cd.user_level IN ($domain_levels) #domain level selections
					                 AND cd.user_experience IN ($domain_exps) #domain experience selections
					                )";
            			if((isset($post['skills']) && !empty($post['skills'])) || (isset($post['subjects']) && !empty($post['subjects']))){
            				$sql10 = "UNION ALL ";
            			}
            			$sql10 .= "SELECT
						            'Domain' AS comp_type,
						            uc.domain_id AS comp_id,
						            c.title AS comp_name,
						            uc.user_id,
						            cd.user_level AS comp_level,
						            cd.user_experience AS comp_experience,
						            uc.created AS comp_added_on
						        FROM user_domains uc
						        LEFT JOIN domain_details cd ON
						            uc.domain_id = cd.domain_id
						            AND uc.user_id = cd.user_id
						        LEFT JOIN knowledge_domains c ON
						            uc.domain_id = c.id
						        WHERE
						            (uc.domain_id IN ($domains) #domain id selections
						             AND cd.user_level IN ($domain_levels) #domain level selections
						             AND cd.user_experience IN ($domain_exps) #domain experience selections
						            )";
			            $sql11[] = "comp_counts.domain_count > 0";
            		}
            		if(isset($sql11) && !empty($sql11)) {
            			$sql12 = implode(' AND ', $sql11);
            		}
            		$query = "SELECT
							    comp_users.user_id
							FROM
							#get list of users who have the selected skills/subjects/domains
							(
							    SELECT
							        comp_counts.user_id,
							        comp_counts.skill_count,
							        comp_counts.subject_count,
							        comp_counts.domain_count
							    FROM
							    (
							        SELECT
							            uc.user_id,
							            SUM(uc.comp_type = 'Skill') AS skill_count,
							            SUM(uc.comp_type = 'Subject') AS subject_count,
							            SUM(uc.comp_type = 'Domain') domain_count
							        FROM
							        (
							            #INCLUDE THIS SECTION IF USER HAS SELECTED SKILLS:
							            #get all user skills sql1
							            $sql1
							            #END OF SECTION-----

							            #REMOVE THIS LINE IF USER HAS NOT SELECTED SUBJECTS OR DOMAINS: sql4
							            #UNION ALL

							            #INCLUDE THIS SECTION IF USER HAS SELECTED SUBJECTS:
							            #get user subjects sql2
							            $sql2
							            #END OF SECTION-----

							            #REMOVE THIS LINE IF USER HAS NOT SELECTED SKILLS AND/OR SUBJECTS: sql4
							            #UNION ALL

							            #INCLUDE THIS SECTION IF USER HAS SELECTED DOMAINS:
							            #get user domains sql3
							            $sql3
							            #END OF SECTION-----
							        ) AS uc
							        GROUP BY uc.user_id
							    ) AS comp_counts
							    WHERE
							        $sql12
							) AS comp_users
							LEFT JOIN
							(
							    SELECT
							        uc.comp_type,
							        uc.comp_id,
							        uc.comp_name,
							        uc.user_id,
							        uc.comp_level,
							        uc.comp_experience,
							        uc.comp_added_on
							    FROM
							    (
							        #INCLUDE THIS SECTION IF USER HAS SELECTED SKILLS:
							        #get all user skills sql8
							        $sql8
							        #END OF SECTION-----

							        #REMOVE THIS LINE IF USER HAS NOT SELECTED SUBJECTS OR DOMAINS: sql11
							        #UNION ALL

							        #INCLUDE THIS SECTION IF USER HAS SELECTED SUBJECTS:
							        #get user subjects sql9
							        $sql9
							        #END OF SECTION-----

							        #REMOVE THIS LINE IF USER HAS NOT SELECTED SKILLS AND/OR SUBJECTS: sql11
							        #UNION ALL

							        #INCLUDE THIS SECTION IF USER HAS SELECTED DOMAINS:
							        #get user domains sql10
							        $sql10
							        #END OF SECTION-----
							    ) AS uc
							) AS cm ON
							    comp_users.user_id = cm.user_id
							LEFT JOIN users u ON
							    u.role_id = 2 AND u.status = 1 AND u.is_deleted = 0 AND u.is_activated = 1 AND cm.user_id = u.id
							LEFT JOIN user_details ud ON u.id = ud.user_id
						    WHERE u.role_id = 2 AND u.status = 1 AND u.is_deleted = 0 AND u.is_activated = 1
						    GROUP BY u.id
							$order_by
							$limit
						";
            		$data = $this->User->query($query);
            		if(isset($data) && !empty($data)){
            			$data = Set::extract($data, '{n}.comp_users.user_id');
            			$userList = implode(',', $data);
            		}

            	}
            }
			echo json_encode($userList);
    		exit;
        }
    }

	public function set_watch() {
		if ($this->request->isAjax()) {
			$this->layout = false;
			$viewData = [];

			if ($this->request->is('post') || $this->request->is('put')) {
            	$post = $this->request->data;
            	$response['success'] = false;
            	$this->loadModel('UserCompetencyWatch');

            	$selections = json_encode($post['selection']);

            	$save['UserCompetencyWatch'] = [
            			'user_id' => $this->Session->read('Auth.User.id'),
            			'name' => $post['watch_name'],
            			'selection' => $selections
            		];
        		if($this->UserCompetencyWatch->save($save)){
        			$response['success'] = true;
        		}
            	echo json_encode($response);
            	exit;
            }

			$this->set($viewData);
			$this->render('partials/watch/set_watch');
		}
    }

	public function get_watch_data($post = null) {
		if( (isset($post['skills']) && !empty($post['skills'])) || (isset($post['subjects']) && !empty($post['subjects'])) || (isset($post['domains']) && !empty($post['domains'])) ){

			$limit = "";
			$order_by = "ORDER BY first_name ASC, last_name ASC";
			$group_order = "ORDER BY CASE
								WHEN comp_type='Skill' THEN 1
								WHEN comp_type='Subject' THEN 2
								WHEN comp_type='Domain' THEN 3
							END, comp_name ASC";

    		$sql11 = [];
    		$sql1 = $sql2 = $sql3 = $sql4 = $sql5 = $sql6 = $sql7 = $sql8 = $sql9 = $sql10 = $sql12 = $sql13 = "";

    		if(isset($post['skills']) && !empty($post['skills'])){
    			$skills = implode(',', $post['skills']);
    			$skill_levels = "'" . implode("', '", $post['skill_levels']) . "'";
    			$skill_exps = "'" . implode("', '", $post['skill_exps']) . "'";

    			$sql1 = "SELECT
			                'Skill' AS comp_type,
			                uc.user_id
			            FROM user_skills uc
			            LEFT JOIN skill_details cd ON
			                uc.skill_id = cd.skill_id
			                AND uc.user_id = cd.user_id
			            LEFT JOIN skills c ON
			                uc.skill_id = c.id
			            WHERE
			                (uc.skill_id IN ($skills) #skill id UI selections
			                 AND cd.user_level IN ($skill_levels) #skill level UI selections
			                 AND cd.user_experience IN ($skill_exps) #skill experience UI selections
			                )";

                $sql8 = "SELECT
				            'Skill' AS comp_type,
				            uc.skill_id AS comp_id,
				            c.title AS comp_name,
				            uc.user_id,
				            cd.user_level AS comp_level,
				            cd.user_experience AS comp_experience,
				            uc.created AS comp_added_on
				        FROM user_skills uc
				        LEFT JOIN skill_details cd ON
				            uc.skill_id = cd.skill_id
				            AND uc.user_id = cd.user_id
				        LEFT JOIN skills c ON
				            uc.skill_id = c.id
				        WHERE
				            (uc.skill_id IN ($skills) #skill id selections
				             AND cd.user_level IN ($skill_levels) #skill level selections
				             AND cd.user_experience IN ($skill_exps) #skill experience selections
				            )";

	            $sql11[] = "comp_counts.skill_count > 0";
    		}
    		if(isset($post['subjects']) && !empty($post['subjects'])){
    			$subjects = implode(',', $post['subjects']);
    			$subject_levels = "'" . implode("', '", $post['subject_levels']) . "'";
    			$subject_exps = "'" . implode("', '", $post['subject_exps']) . "'";

    			if(isset($post['skills']) && !empty($post['skills'])) {
    				$sql2 = "UNION ALL ";
    			}
    			$sql2 .= "SELECT
			                'Subject' AS comp_type,
			                uc.user_id
			            FROM user_subjects uc
			            LEFT JOIN subject_details cd ON
			                uc.subject_id = cd.subject_id
			                AND uc.user_id = cd.user_id
			            LEFT JOIN subjects c ON
			                uc.subject_id = c.id
			            WHERE
			                (uc.subject_id IN ($subjects) #subject id UI selections
			                 AND cd.user_level IN ($subject_levels) #subject level UI selections
			                 AND cd.user_experience IN ($subject_exps) #subject experience UI selections
			                )";
    			if(isset($post['skills']) && !empty($post['skills'])) {
    				$sql9 = "UNION ALL ";
    			}
    			$sql9 .= "SELECT
				            'Subject' AS comp_type,
				            uc.subject_id AS comp_id,
				            c.title AS comp_name,
				            uc.user_id,
				            cd.user_level AS comp_level,
				            cd.user_experience AS comp_experience,
				            uc.created AS comp_added_on
				        FROM user_subjects uc
				        LEFT JOIN subject_details cd ON
				            uc.subject_id = cd.subject_id
				            AND uc.user_id = cd.user_id
				        LEFT JOIN subjects c ON
				            uc.subject_id = c.id
				        WHERE
				            (uc.subject_id IN ($subjects) #subject id selections
				             AND cd.user_level IN ($subject_levels) #subject level selections
				             AND cd.user_experience IN ($subject_exps) #subject experience selections
				            )";
	            $sql11[] = "comp_counts.subject_count > 0";
    		}
    		if(isset($post['domains']) && !empty($post['domains'])){
    			$domains = implode(',', $post['domains']);
    			$domain_levels = "'" . implode("', '", $post['domain_levels']) . "'";
    			$domain_exps = "'" . implode("', '", $post['domain_exps']) . "'";

    			if((isset($post['skills']) && !empty($post['skills'])) || (isset($post['subjects']) && !empty($post['subjects']))){
    				$sql3 = "UNION ALL ";
    			}
    			$sql3 .= "SELECT
			                'Domain' AS comp_type,
			                uc.user_id
			            FROM user_domains uc
			            LEFT JOIN domain_details cd ON
			                uc.domain_id = cd.domain_id
			                AND uc.user_id = cd.user_id
			            LEFT JOIN knowledge_domains c ON
			                uc.domain_id = c.id
			            WHERE
			                (uc.domain_id IN ($domains) #domain id selections
			                 AND cd.user_level IN ($domain_levels) #domain level selections
			                 AND cd.user_experience IN ($domain_exps) #domain experience selections
			                )";
    			if((isset($post['skills']) && !empty($post['skills'])) || (isset($post['subjects']) && !empty($post['subjects']))){
    				$sql10 = "UNION ALL ";
    			}
    			$sql10 .= "SELECT
				            'Domain' AS comp_type,
				            uc.domain_id AS comp_id,
				            c.title AS comp_name,
				            uc.user_id,
				            cd.user_level AS comp_level,
				            cd.user_experience AS comp_experience,
				            uc.created AS comp_added_on
				        FROM user_domains uc
				        LEFT JOIN domain_details cd ON
				            uc.domain_id = cd.domain_id
				            AND uc.user_id = cd.user_id
				        LEFT JOIN knowledge_domains c ON
				            uc.domain_id = c.id
				        WHERE
				            (uc.domain_id IN ($domains) #domain id selections
				             AND cd.user_level IN ($domain_levels) #domain level selections
				             AND cd.user_experience IN ($domain_exps) #domain experience selections
				            )";
	            $sql11[] = "comp_counts.domain_count > 0";
    		}
    		if(isset($sql11) && !empty($sql11)) {
    			$sql12 = implode(' AND ', $sql11);
    		}
    		$query = "SELECT
					    comp_users.user_id,
					    comp_users.matches,
					    CONCAT_WS(' ', ud.first_name , ud.last_name) AS fullname,
					    ud.profile_pic,
					    ud.job_title,
					    ud.organization_id,
					    cm.comp_type,
					    cm.comp_id,
					    cm.comp_name,
					    cm.comp_level,
					    cm.comp_experience,
					    cm.comp_added_on,
					    GROUP_CONCAT(DISTINCT( CONCAT(comp_type,'$$',comp_id,'$$',comp_name,'$$',comp_level,'$$',comp_experience,'$$',comp_added_on))
					    	$group_order SEPARATOR '$$$'
				    	) as competencies
					FROM
					#get list of users who have the selected skills/subjects/domains
					(
					    SELECT
					        comp_counts.user_id,
					        (comp_counts.skill_count+comp_counts.subject_count+comp_counts.domain_count) AS matches
					        /*comp_counts.skill_count,
					        comp_counts.subject_count,
					        comp_counts.domain_count*/
					    FROM
					    (
					        SELECT
					            uc.user_id,
					            SUM(uc.comp_type = 'Skill') AS skill_count,
					            SUM(uc.comp_type = 'Subject') AS subject_count,
					            SUM(uc.comp_type = 'Domain') domain_count
					        FROM
					        (
					            #INCLUDE THIS SECTION IF USER HAS SELECTED SKILLS:
					            #get all user skills sql1
					            $sql1
					            #END OF SECTION-----

					            #REMOVE THIS LINE IF USER HAS NOT SELECTED SUBJECTS OR DOMAINS: sql4
					            #UNION ALL

					            #INCLUDE THIS SECTION IF USER HAS SELECTED SUBJECTS:
					            #get user subjects sql2
					            $sql2
					            #END OF SECTION-----

					            #REMOVE THIS LINE IF USER HAS NOT SELECTED SKILLS AND/OR SUBJECTS: sql4
					            #UNION ALL

					            #INCLUDE THIS SECTION IF USER HAS SELECTED DOMAINS:
					            #get user domains sql3
					            $sql3
					            #END OF SECTION-----
					        ) AS uc
					        GROUP BY uc.user_id
					    ) AS comp_counts
					    WHERE
					        $sql12
					) AS comp_users
					LEFT JOIN
					(
					    SELECT
					        uc.comp_type,
					        uc.comp_id,
					        uc.comp_name,
					        uc.user_id,
					        uc.comp_level,
					        uc.comp_experience,
					        uc.comp_added_on
					    FROM
					    (
					        #INCLUDE THIS SECTION IF USER HAS SELECTED SKILLS:
					        #get all user skills sql8
					        $sql8
					        #END OF SECTION-----

					        #REMOVE THIS LINE IF USER HAS NOT SELECTED SUBJECTS OR DOMAINS: sql11
					        #UNION ALL

					        #INCLUDE THIS SECTION IF USER HAS SELECTED SUBJECTS:
					        #get user subjects sql9
					        $sql9
					        #END OF SECTION-----

					        #REMOVE THIS LINE IF USER HAS NOT SELECTED SKILLS AND/OR SUBJECTS: sql11
					        #UNION ALL

					        #INCLUDE THIS SECTION IF USER HAS SELECTED DOMAINS:
					        #get user domains sql10
					        $sql10
					        #END OF SECTION-----
					    ) AS uc
					) AS cm ON
					    comp_users.user_id = cm.user_id
					LEFT JOIN users u ON
					    u.role_id = 2 AND u.status = 1 AND u.is_deleted = 0 AND u.is_activated = 1 AND cm.user_id = u.id
					LEFT JOIN user_details ud ON
					    u.id = ud.user_id
				    WHERE u.role_id = 2 AND u.status = 1 AND u.is_deleted = 0 AND u.is_activated = 1
				    GROUP BY u.id
					$order_by
					$limit
				";
    		// pr($query, 1);
    		$data = $this->User->query($query);
    		$json = [];
    		if(isset($data) && !empty($data)){
        		foreach ($data as $key => $details) {
        			$user_id = $details['comp_users']['user_id'];
        			$comp = $details[0]['competencies'];
					$competency = explode('$$$', $comp );
					$comp_ids = ['skills' => [], 'subjects' => [], 'domains' => []];
					foreach ($competency as $key => $cdata) {
						$values = explode('$$', $cdata);
						$value = [];
						$comp_type = array_shift($values);
						$comp_id = array_shift($values);
						if($comp_type == 'Skill'){
							$comp_ids['skills'][] = $comp_id;
						}
						if($comp_type == 'Subject'){
							$comp_ids['subjects'][] = $comp_id;
						}
						if($comp_type == 'Domain'){
							$comp_ids['domains'][] = $comp_id;
						}
					}
					$json[$user_id] = [
							'user_id' => $user_id,
							'competencies' => [
									'skills' => implode(',', $comp_ids['skills']),
									'skills_count' => count($comp_ids['skills']),
									'subjects' => implode(',', $comp_ids['subjects']),
									'subjects_count' => count($comp_ids['subjects']),
									'domains' => implode(',', $comp_ids['domains']),
									'domains_count' => count($comp_ids['domains'])
								]
						];
        		}
        	}

        	return $json;

    	}
    }

	public function watch_list() {
		if ($this->request->isAjax()) {
			$this->layout = false;
			$this->loadModel('UserCompetencyWatch');
			$viewData = [];
			$response = ['success' => true, 'content' => []];

			$user_id = $this->Session->read('Auth.User.id');
			if ($this->request->is('post') || $this->request->is('put')) {
            	$post = $this->request->data;
            	$select = "id, name, last_run";
            	$where = "";
            	if(isset($post['id']) && !empty($post['id'])){
            		$id = $post['id'];
            		$where = "AND id = $id";
            		$select = "id, name, selection, last_run";
            	}
				$viewData['watches'] = $this->UserCompetencyWatch->query("SELECT $select
				                                                         FROM user_competency_watches AS ucw
				                                                         WHERE user_id = $user_id
				                                                         		$where
				                                                         ORDER BY name ASC
		                                                         	");
				if(isset($post['id']) && !empty($post['id'])){
					if(isset($viewData['watches']) && !empty($viewData['watches'])){
						$response['success'] = true;
						$response['content'] = $viewData['watches'][0]['ucw']['selection'];
					}
					echo json_encode($response);
					exit;
				}
			}

			$this->set($viewData);
			$this->render('partials/watch/watch_list');
		}
    }

	public function reset_watch() {
		if ($this->request->isAjax()) {
			$this->layout = false;
			$this->loadModel('UserCompetencyWatch');
			$response = ['success' => true];

			if ($this->request->is('post') || $this->request->is('put')) {
            	$post = $this->request->data;
            	$id = $post['id'];
            	$this->UserCompetencyWatch->id = $id;
				if($this->UserCompetencyWatch->saveField('last_run', '')){
					$response['success'] = true;
				}
			}
			echo json_encode($response);
			exit;
		}
    }

	public function delete_watch() {
		if ($this->request->isAjax()) {
			$this->layout = false;
			$this->loadModel('UserCompetencyWatch');
			$response = ['success' => true];

			if ($this->request->is('post') || $this->request->is('put')) {
            	$post = $this->request->data;
            	$id = $post['id'];

				if($this->UserCompetencyWatch->delete($id)){
					$response['success'] = true;
				}
			}
			echo json_encode($response);
			exit;
		}
    }

	public function watch_diff_cron() {
		$this->loadModel('UserCompetencyWatch');

		$data = $this->UserCompetencyWatch->query("SELECT *
                                             FROM user_competency_watches AS ucw
                                             WHERE DATE(last_run) < CURDATE() OR last_run IS NULL
                                             ORDER BY user_id ASC ");

		$view = new View();
		$Permission = $view->loadHelper('Permission');

		$comp_difference = $testdata = [];
		if(isset($data) && !empty($data)){
			foreach ($data as $key => $value) {
				// get previous result and selection from db
				$watch = $value['ucw'];
				$user_id = $watch['user_id'];
				$last_run = $watch['last_run'];
				$selection = $watch['selection'];
				$selected = json_decode($selection, true);

				// update last run
				$id = $watch['id'];


				// get new result based on previous selection
				$current_result = $this->get_watch_data($selected);

				// get each user's data and match with previous result
				if(isset($current_result) && !empty($current_result)){
					foreach ($current_result as $cur_key => $cur_value) {

						$curr_user = $cur_value['user_id']; // userid of result
						$curr_comp = $cur_value['competencies']; // result user's competency

						$curr_skills = $curr_subjects = $curr_domains = $sk_diff = $sb_diff = $dm_diff = [];
						// if current data has skills

						if(isset($curr_comp['skills']) && !empty($curr_comp['skills'])){
							$curr_skills = explode(',', $curr_comp['skills']); // skills from current result set
							// get skills those are added after watch add date
							$selected_skills = implode(',', $selected['skills']);
							$whr = "";

							if(isset($last_run) && !empty($last_run)){
								$whr = " AND UNIX_TIMESTAMP(created) > UNIX_TIMESTAMP('$last_run')";
							}
							$skdata = $this->UserCompetencyWatch->query("SELECT skill_id FROM user_skills WHERE user_id = $curr_user AND skill_id IN ($selected_skills) $whr");
							if(isset($skdata) && !empty($skdata)){
								$sk_diff = Set::extract($skdata, '{n}.user_skills.skill_id');
								$comp_difference[$user_id][$key]['skills'][$curr_user] = $sk_diff;
							}

						}
						if(isset($curr_comp['subjects']) && !empty($curr_comp['subjects'])){
							$curr_subjects = explode(',', $curr_comp['subjects']);
							// get subjects those are added after watch add date
							$selected_subjects = implode(',', $selected['subjects']);
							$whr = "";
							if(isset($last_run) && !empty($last_run)){
								$whr = " AND UNIX_TIMESTAMP(created) > UNIX_TIMESTAMP('$last_run')";
							}
							$sbdata = $this->UserCompetencyWatch->query("SELECT subject_id FROM user_subjects WHERE user_id = $curr_user AND subject_id IN ($selected_subjects) $whr");
							if(isset($sbdata) && !empty($sbdata)){
								$sb_diff = Set::extract($sbdata, '{n}.user_subjects.subject_id');
								$comp_difference[$user_id][$key]['subjects'][$curr_user] = $sb_diff;
							}
						}
						if(isset($curr_comp['domains']) && !empty($curr_comp['domains'])){
							$curr_domains = explode(',', $curr_comp['domains']);
							// get domains those are added after watch add date
							$selected_domain = implode(',', $selected['domains']);
							$whr = "";
							if(isset($last_run) && !empty($last_run)){
								$whr = " AND UNIX_TIMESTAMP(created) > UNIX_TIMESTAMP('$last_run')";
							}
							$dmdata = $this->UserCompetencyWatch->query("SELECT domain_id FROM user_domains WHERE user_id = $curr_user AND domain_id IN ($selected_domain) $whr");
							if(isset($dmdata) && !empty($dmdata)){
								$dm_diff = Set::extract($dmdata, '{n}.user_domains.domain_id');
								$comp_difference[$user_id][$key]['domains'][$curr_user] = $dm_diff;
							}
						}
						if((isset($sk_diff) && !empty($sk_diff)) || (isset($sb_diff) && !empty($sb_diff)) || (isset($dm_diff) && !empty($dm_diff))){
							$comp_difference[$user_id][$key]['watch'] = ['id' => $watch['id'], 'name' => $watch['name'], 'user_id' => $watch['user_id']];

							$owner = $watch['user_id'];

							$current_org = $Permission->current_org($owner);
							$owner_data = $this->UserCompetencyWatch->query("SELECT CONCAT_WS(' ', ud.first_name , ud.last_name) as fullname, users.id , users.email FROM user_details ud left join users on users.id = ud.user_id WHERE ud.user_id = $owner");


							$skdata = $sbdata = [];

							// $testdata[$user_id][$key][$curr_user]['detail'] = [];
							if(isset($sk_diff) && !empty($sk_diff)) {
								$all_skills = implode(',', $sk_diff);
								$skdata = $this->UserCompetencyWatch->query("SELECT s.title, us.user_level, us.user_experience, CONCAT_WS(' ', ud.first_name , ud.last_name) as fullname, ud.job_title , ud.profile_pic, ud.organization_id,ud.user_id  FROM skills s LEFT JOIN skill_details us ON us.skill_id = s.id LEFT JOIN user_details ud ON ud.user_id = us.user_id WHERE us.skill_id IN ($all_skills) AND us.user_id = $curr_user");
								foreach ($skdata as $keyd => $valued) {
									if($keyd == 0){
										$testdata[$user_id][$watch['id']][$curr_user]['user_data']['user'] = $valued[0]['fullname'];
										$testdata[$user_id][$watch['id']][$curr_user]['user_data']['userrole'] = $valued['ud']['job_title'];
										$testdata[$user_id][$watch['id']][$curr_user]['user_data']['image'] = $valued['ud']['profile_pic'];
										$testdata[$user_id][$watch['id']][$curr_user]['user_data']['organization_id'] = $valued['ud']['organization_id'];
										$testdata[$user_id][$watch['id']][$curr_user]['user_data']['id'] = $valued['ud']['user_id'];
									}
									$testdata[$user_id][$watch['id']][$curr_user]['skills'][] = [
																							'title' => $valued['s']['title'],
																							'user_level' => $valued['us']['user_level'],
																							'user_experience' => $valued['us']['user_experience']
																						];
								}
							}
							if(isset($sb_diff) && !empty($sb_diff)) {
								$all_skills = implode(',', $sb_diff);
								$sbdata = $this->UserCompetencyWatch->query("SELECT s.title, us.user_level, us.user_experience, CONCAT_WS(' ', ud.first_name , ud.last_name) as fullname, ud.job_title , ud.profile_pic , ud.organization_id,ud.user_id FROM subjects s LEFT JOIN subject_details us ON us.subject_id = s.id LEFT JOIN user_details ud ON ud.user_id = us.user_id WHERE us.subject_id IN ($all_skills) AND us.user_id = $curr_user");
								foreach ($sbdata as $keyd => $valued) {
									if($keyd == 0){
										$testdata[$user_id][$watch['id']][$curr_user]['user_data']['user'] = $valued[0]['fullname'];
										$testdata[$user_id][$watch['id']][$curr_user]['user_data']['userrole'] = $valued['ud']['job_title'];
										$testdata[$user_id][$watch['id']][$curr_user]['user_data']['image'] = $valued['ud']['profile_pic'];
										$testdata[$user_id][$watch['id']][$curr_user]['user_data']['organization_id'] = $valued['ud']['organization_id'];
										$testdata[$user_id][$watch['id']][$curr_user]['user_data']['id'] = $valued['ud']['user_id'];
									}
									$testdata[$user_id][$watch['id']][$curr_user]['subjects'][] = [
																							'title' => $valued['s']['title'],
																							'user_level' => $valued['us']['user_level'],
																							'user_experience' => $valued['us']['user_experience']
																						];
								}
							}
							if(isset($dm_diff) && !empty($dm_diff)) {
								$all_skills = implode(',', $dm_diff);
								$dmdata = $this->UserCompetencyWatch->query("SELECT s.title, us.user_level, us.user_experience, CONCAT_WS(' ', ud.first_name , ud.last_name) as fullname, ud.job_title , ud.profile_pic, ud.organization_id,ud.user_id FROM knowledge_domains s LEFT JOIN domain_details us ON us.domain_id = s.id LEFT JOIN user_details ud ON ud.user_id = us.user_id WHERE us.domain_id IN ($all_skills) AND us.user_id = $curr_user");
								foreach ($dmdata as $keyd => $valued) {
									 //pr($valued);

									if($keyd == 0){
										$testdata[$user_id][$watch['id']][$curr_user]['user_data']['user'] = $valued[0]['fullname'];
										$testdata[$user_id][$watch['id']][$curr_user]['user_data']['userrole'] = $valued['ud']['job_title'];
										$testdata[$user_id][$watch['id']][$curr_user]['user_data']['image'] = $valued['ud']['profile_pic'];
										$testdata[$user_id][$watch['id']][$curr_user]['user_data']['organization_id'] = $valued['ud']['organization_id'];
										$testdata[$user_id][$watch['id']][$curr_user]['user_data']['id'] = $valued['ud']['user_id'];
									}
									//pr($testdata[$user_id][$key][$curr_user]['detail']);
									$testdata[$user_id][$watch['id']][$curr_user]['domains'][] = [
																							'title' => $valued['s']['title'],
																							'user_level' => $valued['us']['user_level'],
																							'user_experience' => $valued['us']['user_experience']
																						];
								}
							}



							if((isset($skdata) && !empty($skdata)) || (isset($sbdata) && !empty($sbdata)) || (isset($dmdata) && !empty($dmdata)) ){
								$testdata[$user_id][$watch['id']]['watch'] = ['id' => $watch['id'],'email'=>$owner_data[0]['users']['email'] , 'name' => $watch['name'], 'user_id' => $watch['user_id'], 'owner' => $owner_data[0][0]['fullname']];
								//echo "testererere";
							}

						}
					}
				}
			}

		}


		$vars = [];
		if(isset($testdata) && !empty($testdata)){
			foreach($testdata as $all_key => $all_val){
				foreach ($all_val as $w_key => $w_value) {
					$owner_vars = [];
					$comp_vars = [];

					$creator_email_to = $w_value['watch']['email'];
					$watch_id = $w_value['watch']['id'];
					$watch_name = $w_value['watch']['name'];
					$fullname = $w_value['watch']['owner'];
					$owner_id = $w_value['watch']['user_id'];
					$current_org = $Permission->current_org($owner_id);

					foreach ($w_value  as $detail_key => $detail_value) {
						if($detail_key != 'watch'){
							$comp_vars[$watch_id]['comp_data'][] = $detail_value;
						}
					}
					$owner_org = null;
					if(isset($current_org['organization_id']) && !empty($current_org['organization_id'])) {
						$owner_org = $current_org['organization_id'];
					}
					$siteurl = Router::Url( array( 'controller' => 'competencies', 'action' => 'index', 'cmp_type' => 'watch', 'watch' => $watch_id, 'admin' => FALSE ), TRUE );

					$this->UserCompetencyWatch->query("UPDATE user_competency_watches SET last_run = SYSDATE() WHERE id = $watch_id");

					$email = new CakeEmail();
					$email->config('Smtp');
					$email->from(array(ADMIN_FROM_EMAIL => SITENAME));
					$email->to($creator_email_to);
					$email->subject(SITENAME . ': Competency watch');
					$email->template('watch');
					$email->emailFormat('html');
					$email->viewVars(['creator_email_to' => $creator_email_to, 'fullname' => $fullname, 'watch_name' => $watch_name, 'siteurl' => $siteurl, 'currentOrg' => $owner_org, 'owner_id' => $owner_id, 'users' => $comp_vars[$watch_id]['comp_data']]);
					$email->send();
				}
			}
		}

		die;
    }

	public function save_parameters() {
		if ($this->request->isAjax()) {
			$this->layout = false;
			$viewData = [];

			if ($this->request->is('post') || $this->request->is('put')) {
            	$post = $this->request->data;
            	$response = ['success' => false, 'content' => null];
            	$this->loadModel('LinkParam');

            	$save['LinkParam'] = [
            			'user_id' => $this->Session->read('Auth.User.id'),
            			'type' => $post['type'],
            			'params' => $post['params']
            		];
        		if($this->LinkParam->save($save)){
        			$response['success'] = true;
        			$response['content'] = $this->LinkParam->getLastInsertId();
        		}
            }
        	echo json_encode($response);
        	exit;

		}
    }
}