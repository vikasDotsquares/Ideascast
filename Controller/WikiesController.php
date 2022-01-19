<?php

App::uses('Controller/Component', 'Auth', 'Session', 'RequestHandler');
App::uses('Sanitize', 'Utility');
App::uses('CakeEmail', 'Network/Email');
App::import('Vendor', 'MPDF56/PhpWord');

class WikiesController extends AppController {

	public $name = 'Wikies';
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
		'Wiki',
		'WikiPage',
		'WikiUser',
		'WikiPageCommentDocument',
		'WikiPageComment',
		'WikiPageLike',
		'WikiPageView',
		'WikiPageCommentLike',
		'EmailNotification',
	];
	public $user_id = null;
	public $pagination = null;
	public $components = array('Mpdf', 'Common', 'CommonEmail', 'Group');

	/**
	 * check login for admin and frontend user
	 * allow and deny user
	 */
	//public $components = array('Email');
	// $this->loadModel('Project');
	/**
	 * Helpers
	 *
	 * @var array
	 */
	public $helpers = array('Html', 'Form', 'Session', 'Time', 'Text', 'Common', 'ViewModel', 'Mpdf', 'Wiki', 'Js' => array('Jquery'));

	public function beforeFilter() {
		parent::beforeFilter();
		$this->set('controller', 'wikies');
		$this->user_id = $this->Auth->user('id');
		
		if( $_SERVER['HTTP_HOST'] != LOCALIP )  {

			if( $this->Session->read('Auth.User.role_id') == 3 ){
				$this->redirect(['controller' => 'organisations','action' => 'dashboard']);
			}
			if( $this->Session->read('Auth.User.role_id') == 1 ){
				$this->redirect(['controller' => 'dashboard','action' => 'index']);
			}
		}
		
	}

	public function index($project_id = null) {
		if (isset($this->params['named']['project']) && !empty($this->params['named']['project']) ) {
			if(!dbExists('Project', $this->params['named']['project'])){
				$this->redirect(array('controller' => 'wikies', 'action' => 'index'));
			}
		}
		$viewVars = $data = null;
		$this->layout = 'inner';
		$data['title_for_layout'] = __('Info Center Wiki', true);
		$data['page_heading'] = __('Info Center', true);
		$data['page_subheading'] = __('Share information and discuss Project work', true);
		$this->set($data);
		$crumb = ['last' => ['data' => ['title' => 'Info Center', 'data-original-title' => 'Info Center /Wiki']]];
		$this->set('crumb', $crumb);
		$project_id = $status = null;
		$project_wiki = $this->Wiki->find('first', ['conditions' => ['Wiki.project_id' => $project_id]]);

		$wiki_id = (isset($project_wiki['Wiki']['id']) && !empty($project_wiki['Wiki']['id'])) ? $project_wiki['Wiki']['id'] : null;
		$viewVars['wiki_id'] = $wiki_id;

		$projects = [];
		$mprojects = get_my_projects($this->user_id, null, 1);
		$rprojects = get_rec_projects($this->user_id, 1, 1, 1);
		$gprojects = group_rec_projects($this->user_id, null, 1);

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
			$projects = array_map("strip_tags", $projects);
			$projects = array_map("trim", $projects);
			$projects = array_map(function ($v) {
				return html_entity_decode($v, ENT_COMPAT, "UTF-8");
			}, $projects);
			natcasesort($projects);
		}
		$list_projects = $projects;

		if (isset($this->params['named']) && !empty($this->params['named'])) {
			$params = $this->params['named'];
			$viewVars['project_id'] = isset($params['project']) && !empty($params['project']) ? $params['project'] : null;
			$viewVars['project_id'] = isset($params['project_id']) && !empty($params['project_id']) ? $params['project_id'] : null;
			$viewVars['page_id'] = isset($params['page_id']) && !empty($params['page_id']) ? $params['page_id'] : null;
		}
		$this->set(compact("project_id", "list_projects"));
		$this->set($viewVars);
		$this->setJsVar("viewVars", $viewVars);
	}

	public function timezone() {

	}

	public function save_wiki($project_id = null) {

		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			$this->set('project_id', $project_id);
			$this->Wiki->set($this->request->data);
			$response = null;
			$response['success'] = false;
			$response['content'] = null;
			$response['project_id'] = $project_id;

			if ($this->Wiki->validates()) {
				if (!empty($this->request->data)) {
					if ($this->Wiki->save($this->request->data)) {
						$this->Common->projectModified($project_id, $this->user_id);
						$this->Session->setFlash('Wiki created successfully.', 'success');

						$lastid = $this->Wiki->getLastInsertID();
						$wikidata = $this->Wiki->findById($lastid);
						$data = $this->get_all_user_email($project_id);

						if (isset($data) && !empty($data)) {
							foreach ($data as $user_id) {
								$users = $this->User->findById($user_id);
								if (isset($users['User']) && !empty($users['User']['id'])) {

									$notifiragstatus = $this->EmailNotification->find('first', ['conditions' => ['notification_type' => 'team_talk', 'personlization' => 'wiki_created', 'user_id' => $user_id]]);

									$usersDetails = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));

									if ((!isset($notifiragstatus['EmailNotification']['email']) || $notifiragstatus['EmailNotification']['email'] == 1) && (!isset($usersDetails['User']['email_notification']) || $usersDetails['User']['email_notification'] == 1)) {

										$sendMail = $this->CommonEmail->sendEmailWiki($users, $wikidata, "created");

									}

								}
							}
						}

						$response['success'] = true;
					}
				}
			} else {
				$response['content'] = $this->validateErrors($this->Wiki);
			}

			echo json_encode($response);
			exit;
		}
	}

	public function get_all_user_email($project_id = null, $wiki_id = null) {
		$conditions = ["WikiPage.project_id" => $project_id, "WikiPage.wiki_id" => $wiki_id, 'WikiPage.is_deleted !=' => 1];
		$users = $project_users = $w_users = $wp_users = $wpcd_users = array();

		$view = new View();
		$common = $view->loadHelper('Common');

		if (isset($project_id) && !empty($project_id)) {

			$owner = $common->ProjectOwner($project_id, $this->Session->read('Auth.User.id'));
			$participants = participants($project_id, $owner['UserProject']['user_id']);
			if (isset($participants) && !empty($participants)) {
				$project_users = array_merge($project_users, $participants);
			}

			$participants_owners = participants_owners($project_id, $owner['UserProject']['user_id']);
			if (isset($participants_owners) && !empty($participants_owners)) {
				$project_users = array_merge($project_users, $participants_owners);
			}

		}

		$allWikiUsers = $this->Wiki->find("all", array(
			"conditions" => array("Wiki.project_id" => $project_id, "Wiki.revision_id" => 0),
			"recursive" => -1,
			"fields" => array("Wiki.user_id"),
		)
		);

		$allWikiRequestUsers = $this->WikiUser->find("all", array(
			"conditions" => array("WikiUser.wiki_id" => $wiki_id, "WikiUser.approved" => 1),
			"recursive" => -1,
			"fields" => array("WikiUser.user_id"),
		)
		);

		$allWikiPageUsers = $this->WikiPage->find("all", array(
			"conditions" => $conditions,
			"recursive" => -1,
			"fields" => array("WikiPage.user_id"),
		)
		);
		$allWikiPageCommentDocumentUsers = $this->WikiPageCommentDocument->find("all", array(
			"conditions" => array("WikiPageCommentDocument.wiki_id" => $wiki_id),
			"recursive" => -1,
			"fields" => array("WikiPageCommentDocument.user_id"),
		)
		);

		$w_users = Set::extract('/Wiki/user_id', $allWikiUsers);
		if (isset($project_users) && !empty($project_users)) {
			$users = array_merge($w_users, $project_users);
		}
		if (isset($allWikiRequestUsers) && !empty($allWikiRequestUsers)) {
			$wr_users = Set::extract('/WikiUser/user_id', $allWikiRequestUsers);
			$users = array_merge($users, $allWikiRequestUsers);
		}

		if (isset($allWikiPageUsers) && !empty($allWikiPageUsers)) {
			$wp_users = Set::extract('/WikiPage/user_id', $allWikiPageUsers);
			$users = array_merge($users, $wp_users);
		}
		if (isset($allWikiPageCommentDocumentUsers) && !empty($allWikiPageCommentDocumentUsers)) {
			$wpcd_users = Set::extract('/WikiPageCommentDocument/user_id', $allWikiPageCommentDocumentUsers);
			$users = array_merge($users, $wpcd_users);
		}

		if (isset($project_users) && !empty($project_users)) {
			$users = array_merge($users, $project_users);
		}

		$users = isset($users) ? array_unique($users) : null;
		return $users;
	}

	public function project_people($project_id = null) {

		$this->layout = 'ajax';

		$view = new View();
		$common = $view->loadHelper('Common');

		$data = array();

		if (isset($project_id) && !empty($project_id)) {

			$owner = $common->ProjectOwner($project_id, $this->Session->read('Auth.User.id'));
			//participants
			$participants = participants($project_id, $owner['UserProject']['user_id']);
			if (isset($participants) && !empty($participants)) {
				//$data = array_merge($data, $participants);
			}

			//participants_owners
			$participants_owners = participants_owners($project_id, $owner['UserProject']['user_id']);
			if (isset($participants_owners) && !empty($participants_owners)) {
				$data = array_merge($data, $participants_owners);
			}
			//participantsGpOwner
			$participantsGpOwner = participants_group_owner($project_id);
			if (isset($participantsGpOwner) && !empty($participantsGpOwner)) {
				$data = array_merge($data, $participantsGpOwner);
			}
			//participantsGpSharer
			//$participantsGpSharer = participants_group_sharer( $project_id );
		}

		return $data;
	}

	public function list_projects() {
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			$response = [
				'success' => false,
				'msg' => '',
				'content' => null,
			];
			$row = null;
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;

				/* if (isset($post['type']) && $post['type'] == 1) {
						$response['content'] = get_my_projects($this->user_id, null, 1);
					} else if (isset($post['type']) && $post['type'] == 2) {
						$response['content'] = get_rec_projects($this->user_id, 1, 1, 1);
					} else if (isset($post['type']) && $post['type'] == 3) {
						$response['content'] = group_rec_projects($this->user_id, null, 1);
				*/

				$projects = [];
				$mprojects = get_my_projects($this->user_id, null, 1);
				$rprojects = get_rec_projects($this->user_id, 1, 1, 1);
				$gprojects = group_rec_projects($this->user_id, null, 1);

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
					$projects = array_map("strip_tags", $projects);
					$projects = array_map("trim", $projects);
					$projects = array_map(function ($v) {
						return html_entity_decode($v, ENT_COMPAT, "UTF-8");
					}, $projects);
					natcasesort($projects);

					$response['content'] = $projects;
				}

			}

			//pr($this->params);
			if (isset($this->params['named']['project_id']) && !empty($this->params['named']['project_id'])) {
				$params = $this->params['named'];
				$viewVars['project_id'] = isset($params['project_id']) && !empty($params['project_id']) ? $params['project_id'] : null;
				$project_wiki = $this->Wiki->find('first', ['conditions' => ['Wiki.project_id' => $params['project_id']]]);
				$wiki_id = (isset($project_wiki['Wiki']['id']) && !empty($project_wiki['Wiki']['id'])) ? $project_wiki['Wiki']['id'] : null;
				$viewVars['wiki_id'] = $wiki_id;
				$this->set($viewVars);
			}

			echo json_encode($response);
			exit();
		}
	}

	public function projects_list() {
		$html = '';

		if ($this->request->isAjax()) {
			$view = new View();
			$viewModel = $view->loadHelper('ViewModel');
			$this->loadModel('Project');
			$this->layout = 'ajax';
			$response = [
				'success' => false,
				'msg' => '',
				'content' => null,
			];
			$row = $data = null;

			$view_vars['project_id'] = $this->request->data['project_id'];
			$view_vars['user_id'] = $this->Session->read("Auth.User.id");
			$this->setJsVar('view_vars', $view_vars);

			if ($this->request->is('post') || $this->request->is('put')) {
				if (isset($this->request->data['project_id']) && !empty($this->request->data['project_id']) && is_numeric($this->request->data['project_id'])) {
					$post = $this->request->data;
					$result = $params = null;
					$conditions = $pw_condition = [];
					$order = '';
					if (isset($this->request->data['project_id']) && !empty($this->request->data['project_id'])) {
						$data = $this->Project->find("first", array('conditions' => array('Project.id' => $this->request->data['project_id'])));
					}
					$this->set('data', $data);
					$view->layout = false; //if you want to disable layout
					$html = $this->render('task_list');
				} else {
					$this->set('data', $data);
					$view->layout = false; //if you want to disable layout
					$html = $this->render('task_list');
				}
			}
			return $html;
			exit();
		}
	}

	public function create_wiki_page($project_id = null, $user_id = null, $wiki_id = null) {
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			$response = null;
			$this->set(compact('project_id', 'user_id', 'wiki_id'));
		}
	}

	public function create_wiki_page_linked($project_id = null, $user_id = null, $wiki_id = null) {
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			$response = null;
			$this->set(compact('project_id', 'user_id', 'wiki_id'));
		}
	}

	public function linkedpage($wiki_page_id = null) {
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			$project_id = $user_id = $wiki_id = $wikipage = null;
			if (isset($wiki_page_id) && !empty($wiki_page_id)) {
				$wikipage = $this->WikiPage->findById($wiki_page_id);
				if (isset($wikipage) && !empty($wikipage)) {
					$project_id = $wikipage['WikiPage']['project_id'];
					$user_id = $wikipage['WikiPage']['user_id'];
					$wiki_id = $wikipage['WikiPage']['wiki_id'];
				}
			}
			$this->set(compact('project_id', 'user_id', 'wiki_id', 'wiki_page_id', 'wikipage'));
		}
	}

	public function create_wiki_page_save($project_id = null, $user_id = null, $wiki_id = null) {

		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			$this->autoRender = false;
			$this->set(compact('project_id', 'user_id', 'wiki_id'));
			$response = null;
			$response['success'] = false;
			$response['content'] = null;
			$response['socket_content'] = null;
			$this->WikiPage->set($this->request->data['WikiPage']);
			if ($this->WikiPage->validates()) {
				if (!empty($this->request->data)) {
					$post = $this->request->data;
					$project_id = (isset($post['WikiPage']['project_id']) && !empty($post['WikiPage']['project_id'])) ? $post['WikiPage']['project_id'] : null;
					$wiki_id = (isset($post['WikiPage']['wiki_id']) && !empty($post['WikiPage']['wiki_id'])) ? $post['WikiPage']['wiki_id'] : null;

					if ($this->WikiPage->save($this->request->data)) {
						$this->Common->projectModified($project_id, $this->user_id);
						$lastid = $this->WikiPage->getLastInsertID();
						$wikidata = $this->WikiPage->findById($lastid);
						$data = $this->get_all_user_email($project_id);
						if (isset($data) && !empty($data)) {
							foreach ($data as $user_id) {
								$users = $this->User->findById($user_id);
								if (isset($users['User']['id']) && !empty($users['User']['id'])) {

									$sendMail = $this->CommonEmail->sendEmailWikiPage($users, $wikidata, "created");

								}
							}

							/************** socket messages **************/
							if (SOCKET_MESSAGES) {
								$current_user_id = $this->user_id;
								$w_users = $data;
								if (isset($w_users) && !empty($w_users)) {
									if (($key = array_search($current_user_id, $w_users)) !== false) {
										unset($w_users[$key]);
									}
								}
								$open_users = null;
								if (isset($w_users) && !empty($w_users)) {
									foreach ($w_users as $key1 => $value1) {
										if (web_notify_setting($value1, 'team_talk', 'wiki_created')) {
											$open_users[] = $value1;
										}
									}
								}
								$userDetail = get_user_data($current_user_id);
								$content = [
									'notification' => [
										'type' => 'wiki_created',
										'created_id' => $current_user_id,
										'project_id' => $project_id,
										'refer_id' => $wiki_id,
										'creator_name' => $userDetail['UserDetail']['full_name'],
										'subject' => 'Wiki created',
										'heading' => 'Wiki: ' . strip_tags(getFieldDetail('Wiki', $wiki_id, 'title')),
										'sub_heading' => 'Project: ' . strip_tags(getFieldDetail('Project', $project_id, 'title')),
										'date_time' => timezoneTimeConvertor($this->user_id, date('Y-m-d g:iA'), $format = 'd M Y g:iA') . user_country($this->user_id),
									],
								];
								if (is_array($open_users)) {
									$content['received_users'] = array_values($open_users);
								}

								$response['socket_content'] = $content;
							}
							/************** socket messages **************/
						}

						$this->Session->setFlash('Wiki page created successfully.', 'success');
						$response['success'] = true;
					}
				}
			} else {
				$response['content'] = $this->validateErrors($this->WikiPage);
			}
			echo json_encode($response);
			exit;
		}
	}

	public function create_wiki_page_linked_save($project_id = null, $user_id = null, $wiki_id = null) {
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			$this->autoRender = false;
			$this->set(compact('project_id', 'user_id', 'wiki_id'));
			$response = null;
			$response['success'] = false;
			$response['content'] = null;
			$response['wiki_page_id'] = null;
			$this->WikiPage->set($this->request->data['WikiPage']);
			if ($this->WikiPage->validates()) {
				if (!empty($this->request->data)) {
					if ($this->WikiPage->save($this->request->data)) {
						$response['success'] = true;
						$response['wiki_page_id'] = $this->WikiPage->getLastInsertID();
					}
				}
			} else {
				$response['content'] = $this->validateErrors($this->WikiPage);
			}
			echo json_encode($response);
			exit;
		}
	}

	public function update_description($project_id = null, $user_id = null, $wiki_id = null, $wiki_page_id = null) {
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			$this->autoRender = false;
			$this->set(compact('project_id', 'user_id', 'wiki_id', 'wiki_page_id'));
			$response = null;
			$response['success'] = false;
			$response['content'] = null;
			if (!empty($this->request->data)) {
				$description = $this->request->data['description'];
				if (isset($wiki_page_id) && $wiki_page_id != 'null' && is_numeric($wiki_page_id)) {
					if ($this->WikiPage->updateAll(array("WikiPage.description" => "'" . Sanitize::escape($description) . "'"), array("WikiPage.id" => $wiki_page_id))) {
						$response['success'] = true;
					}
				}
				if (isset($wiki_page_id) && $wiki_page_id == 'null' && is_string($wiki_page_id)) {
					if ($this->Wiki->updateAll(array("Wiki.description" => "'" . Sanitize::escape($description) . "'"), array("Wiki.id" => $wiki_id))) {
						$response['success'] = true;
					}
				}
			}
			echo json_encode($response);
			exit;
		}
	}

	public function update_wiki_page($project_id = null, $user_id = null, $wiki_id = null, $wiki_page_id = null) {
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			$response = null;
			$this->request->data = $this->WikiPage->read(null, $wiki_page_id);
			$this->set(compact('project_id', 'user_id', 'wiki_id', 'wiki_page_id'));
		}
	}

	public function update_wiki_page_save($project_id = null, $user_id = null, $wiki_id = null, $wiki_page_id = null) {
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			$this->autoRender = false;
			$this->set(compact('project_id', 'user_id', 'wiki_id', 'wiki_page_id'));
			$response = null;
			$response['success'] = false;
			$response['content'] = null;
			$response['socket_content'] = null;
			$this->WikiPage->set($this->request->data['WikiPage']);
			if ($this->WikiPage->validates()) {
				if (!empty($this->request->data)) {
					$oldwikipage = $this->WikiPage->findById($wiki_page_id);
					//pr($this->request->data);die;
					// if ($this->WikiPage->updateAll(array("WikiPage.updated" => time(), "WikiPage.updated_user_id" => $this->user_id), array("WikiPage.id" => $wiki_page_id))) {

					if ($this->WikiPage->updateAll(array("WikiPage.updated_user_id" => $this->user_id), array("WikiPage.id" => $wiki_page_id))) {

						$response['updated_user'] = $this->Common->userFullname($this->request->data['WikiPage']['updated_user_id']);
						$response['updated_date'] = date("d M, Y h:iA", time());

						$this->request->data['WikiPage']['updated'] = time();
						$this->request->data['WikiPage']['updated_user_id'] = $this->user_id;
						if (isset($oldwikipage['WikiPage']['revision_id']) && !empty($oldwikipage['WikiPage']['revision_id']) && $oldwikipage['WikiPage']['revision_id'] > 0) {
							$this->request->data['WikiPage']['revision_id'] = $oldwikipage['WikiPage']['revision_id'];
						} else {
							$this->request->data['WikiPage']['revision_id'] = $wiki_page_id;
						}

						$this->request->data['WikiPage']['is_archived'] = 0;
						$this->request->data['WikiPage']['id'] = null;
						//   pr($this->request->data);die;
						$this->WikiPage->save($this->request->data);
						//$conditions = ["WikiPage.project_id" => $project_id, "WikiPage.wiki_id" => $wiki_id, "WikiPage.user_id" => $user_id, 'WikiPage.is_archived' => 1, "WikiPage.is_linked" => 0, 'WikiPage.is_deleted !=' => 1];
						$update = $this->WikiPage->updateAll(
							array("WikiPage.is_search" => 0), array("WikiPage.is_archived" => 0)
						);
						$update = $this->WikiPage->updateAll(
							array("WikiPage.is_search" => 0), array("WikiPage.is_linked" => 1)
						);

						$wikidata = $this->WikiPage->findById($wiki_page_id);
						$data = $this->get_all_user_email($project_id);
						if (isset($data) && !empty($data)) {
							foreach ($data as $user_id) {
								$users = $this->User->findById($user_id);
								if (isset($users['User']['id']) && !empty($users['User']['id'])) {

									$notifiragstatus = $this->EmailNotification->find('first', ['conditions' => ['notification_type' => 'team_talk', 'personlization' => 'wiki_page_request', 'user_id' => $user_id]]);

									$usersDetails = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));

									if ((!isset($notifiragstatus['EmailNotification']['email']) || $notifiragstatus['EmailNotification']['email'] == 1) && (!isset($usersDetails['User']['email_notification']) || $usersDetails['User']['email_notification'] == 1)) {

										$sendMail = $this->CommonEmail->sendEmailWikiPage($users, $wikidata, "updated");

									}
								}
							}

							/************** socket messages **************/
							if (SOCKET_MESSAGES) {
								$current_user_id = $this->user_id;
								$w_users = $data;
								if (isset($w_users) && !empty($w_users)) {
									if (($key = array_search($current_user_id, $w_users)) !== false) {
										unset($w_users[$key]);
									}
								}
								$open_users = null;
								if (isset($w_users) && !empty($w_users)) {
									foreach ($w_users as $key1 => $value1) {
										if (web_notify_setting($value1, 'team_talk', 'wiki_page_request')) {
											$open_users[] = $value1;
										}
									}
								}
								$userDetail = get_user_data($current_user_id);
								$content = [
									'notification' => [
										'type' => 'wiki_updated',
										'created_id' => $current_user_id,
										'project_id' => $project_id,
										'refer_id' => $wiki_id,
										'creator_name' => $userDetail['UserDetail']['full_name'],
										'subject' => 'Wiki updated',
										'heading' => 'Wiki page: ' . strip_tags(getFieldDetail('Wiki', $wiki_id, 'title')),
										'sub_heading' => 'Project: ' . strip_tags(getFieldDetail('Project', $project_id, 'title')),
										'date_time' => timezoneTimeConvertor($this->user_id, date('Y-m-d g:iA'), $format = 'd M Y g:iA') . user_country($this->user_id),
									],
								];
								if (is_array($open_users)) {
									$content['received_users'] = array_values($open_users);
								}

								$response['socket_content'] = $content;
							}
							/************** socket messages **************/
						}

						//$this->Session->setFlash('Wiki page created successfully.', 'success');
						$response['success'] = true;
						$this->request->data = $this->WikiPage->read(null, $wiki_page_id);
						$response['content'] = $this->request->data;
					}
				}
			} else {
				$response['content'] = $this->validateErrors($this->WikiPage);
			}
			echo json_encode($response);
			exit;
		}
	}

	public function update_wiki_page_linked($project_id = null, $user_id = null, $wiki_id = null, $wiki_page_id = null) {
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			$response = null;
			$this->request->data = $this->WikiPage->read(null, $wiki_page_id);
			$this->set(compact('project_id', 'user_id', 'wiki_id', 'wiki_page_id'));
		}
	}

	public function update_wiki_page_linked_save($project_id = null, $user_id = null, $wiki_id = null, $wiki_page_id = null) {
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			$this->autoRender = false;
			$this->set(compact('project_id', 'user_id', 'wiki_id', 'wiki_page_id'));
			$response = null;
			$response['success'] = false;
			$response['content'] = null;
			$this->WikiPage->set($this->request->data['WikiPage']);
			if ($this->WikiPage->validates()) {
				if (!empty($this->request->data)) {
					$oldwikipage = $this->WikiPage->findById($wiki_page_id);
					//pr($this->request->data);die;
					if ($this->WikiPage->save($this->request->data)) {
						$response['success'] = true;
						$this->request->data = $this->WikiPage->read(null, $wiki_page_id);
						$response['content'] = $this->request->data;
					}
				}
			} else {
				$response['content'] = $this->validateErrors($this->WikiPage);
			}
			echo json_encode($response);
			exit;
		}
	}

	public function update_wiki($project_id = null, $user_id = null, $wiki_id = null) {
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			$response = null;
			$this->request->data = $this->Wiki->read(null, $wiki_id);
			$this->set(compact('project_id', 'user_id', 'wiki_id'));
		}
	}

	public function update_wiki_save($project_id = null, $user_id = null, $wiki_id = null) {
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			$this->autoRender = false;
			$this->set(compact('project_id', 'user_id', 'wiki_id'));
			$response = null;
			$response['success'] = false;
			$response['content'] = null;
			$response['socket_content'] = null;
			$response['project_id'] = $project_id;

			$view_vars['project_id'] = $project_id;
			$view_vars['user_id'] = $user_id;
			$view_vars['wiki_id'] = $wiki_id;
			$this->setJsVar('view_vars', $view_vars);

			$this->Wiki->set($this->request->data['Wiki']);
			if ($this->Wiki->validates()) {
				if (!empty($this->request->data)) {

					$oldWiki = $this->Wiki->findById($wiki_id);
					$oldWiki['Wiki']['id'] = null;
					$oldWiki['Wiki']['status'] = 0;
					$oldWiki['Wiki']['revision_id'] = $wiki_id;
					$oldWiki['Wiki']['updated_user_id'] = $this->user_id;

					$this->Wiki->save($oldWiki);
					$this->request->data['Wiki']['wiki_step'] = $oldWiki['Wiki']['wiki_step'] + 1;
					if ($this->Wiki->save($this->request->data)) {

						$update = $this->Wiki->updateAll(array("Wiki.is_search" => 0), array("Wiki.status" => 0, 'Wiki.revision_id !=' => 0));
						//pr($update,1);
						$response['success'] = true;
						$response['updated_user'] = $this->Common->userFullname($this->request->data['Wiki']['updated_user_id']);
						$response['updated_date'] = date("d M, Y h:i A", time());

						$wikidata = $this->Wiki->findById($wiki_id);
						$data = $this->get_all_user_email($project_id);
						if (isset($data) && !empty($data)) {
							foreach ($data as $user_id) {
								$users = $this->User->findById($user_id);
								if (isset($users['User']['id']) && !empty($users['User']['id'])) {
									$sendMail = $this->CommonEmail->sendEmailWiki($users, $wikidata, "updated");
								}
							}

							/************** socket messages **************/
							if (SOCKET_MESSAGES) {
								// e('update_wiki_save', 1);
								$current_user_id = $this->user_id;
								$w_users = $data;
								if (isset($w_users) && !empty($w_users)) {
									if (($key = array_search($current_user_id, $w_users)) !== false) {
										unset($w_users[$key]);
									}
								}
								$open_users = null;
								if (isset($w_users) && !empty($w_users)) {
									foreach ($w_users as $key1 => $value1) {
										if (web_notify_setting($value1, 'team_talk', 'wiki_page_request')) {
											$open_users[] = $value1;
										}
									}
								}
								$userDetail = get_user_data($current_user_id);
								$content = [
									'notification' => [
										'type' => 'wiki_page_request',
										'created_id' => $current_user_id,
										'project_id' => $project_id,
										'refer_id' => $wiki_id,
										'creator_name' => $userDetail['UserDetail']['full_name'],
										'subject' => 'Wiki updated',
										'heading' => 'Wiki page: ' . strip_tags(getFieldDetail('Wiki', $wiki_id, 'title')),
										'sub_heading' => 'Project: ' . strip_tags(getFieldDetail('Project', $project_id, 'title')),
										'date_time' => timezoneTimeConvertor($this->user_id, date('Y-m-d g:iA'), $format = 'd M Y g:iA') . user_country($this->user_id),
									],
								];
								if (is_array($open_users)) {
									$content['received_users'] = array_values($open_users);
								}

								$response['socket_content'] = $content;
							}
							/************** socket messages **************/
						}
					}
				}
			} else {
				$response['content'] = $this->validateErrors($this->Wiki);
			}
			echo json_encode($response);
			exit;
		}
	}

	public function get_user_page_view($project_id = null, $user_id = null, $wiki_id = null, $wiki_page_id = null) {
		$this->layout = 'ajax';
		$this->autoRender = false;
		$this->set(compact('project_id', 'user_id', 'wiki_id', 'wiki_page_id'));
		$this->render("partials/wiki_dashboard/get_user_page_view");
	}

	public function get_user_page_comment($project_id = null, $user_id = null, $wiki_id = null, $wiki_page_id = null) {
		$this->layout = 'ajax';
		$this->autoRender = false;
		$this->set(compact('project_id', 'user_id', 'wiki_id', 'wiki_page_id'));
		$this->render("partials/wiki_dashboard/get_user_page_comment");
	}

	public function wiki_read($project_id = null, $user_id = null, $wiki_id = null) {
		$this->layout = 'ajax';
		$this->autoRender = false;
		$this->set(compact('project_id', 'user_id', 'wiki_id'));
		$this->render("partials/wiki_read/wiki_read");
	}

	public function wiki_comment($project_id = null, $user_id = null, $wiki_id = null) {
		$this->layout = 'ajax';
		$this->autoRender = false;
		$this->set(compact('project_id', 'user_id', 'wiki_id'));
		$this->render("partials/wiki_comment/wiki_comment");
	}

	public function wiki_document($project_id = null, $user_id = null, $wiki_id = null) {
		$this->layout = 'ajax';
		$this->autoRender = false;
		$this->set(compact('project_id', 'user_id', 'wiki_id'));
		$this->render("partials/wiki_document/wiki_document");
	}

	public function wiki_history($project_id = null, $user_id = null, $wiki_id = null) {
		$this->layout = 'ajax';
		$this->autoRender = false;

		$conditions = [
			"WikiPage.project_id" => $project_id,
			"WikiPage.wiki_id" => $wiki_id,
			'WikiPage.is_archived' => 0,
			'WikiPage.revision_id !=' => 0,
			"WikiPage.is_linked" => 0,
			'WikiPage.is_deleted !=' => 1,
		];
		if (isset($wiki_page_id) && !empty($wiki_page_id)) {
			// $conditions = ["WikiPage.id" => $wiki_page_id];
		}

		$allWikiPages = $this->WikiPage->find("all", array(
			"conditions" => $conditions,
		)
		);

		$this->set(compact('project_id', 'user_id', 'wiki_id', 'allWikiPages'));
		$this->render("partials/wiki_history/wiki_history");
	}

	public function wiki_dashboard($project_id = null, $user_id = null, $wiki_id = null) {
		$this->layout = 'ajax';
		$this->autoRender = false;
		$this->set(compact('project_id', 'user_id', 'wiki_id'));
		$this->render("partials/wiki_dashboard/wiki_dashboard");
	}

	public function wiki_admin($project_id = null, $user_id = null, $wiki_id = null) {
		$this->layout = 'ajax';
		$this->autoRender = false;
		$this->set(compact('project_id', 'user_id', 'wiki_id'));
		$this->render("partials/wiki_admin/wiki_admin");
	}

	public function wiki_all_users($project_id = null, $user_id = null, $wiki_id = null, $wiki_page_id = null) {
		$this->layout = 'ajax';
		$this->autoRender = false;
		$this->set(compact('project_id', 'user_id', 'wiki_id', 'wiki_page_id'));
		$this->render("partials/wiki_read/wiki_all_users");
	}

	public function wiki_history_all_users($project_id = null, $user_id = null, $wiki_id = null, $wiki_page_id = null) {
		$this->layout = 'ajax';
		$this->autoRender = false;
		$this->set(compact('project_id', 'user_id', 'wiki_id', 'wiki_page_id'));
		$this->render("partials/wiki_history/wiki_all_users");
	}

	public function get_wiki_page_by_user($project_id = null, $user_id = null, $wiki_id = null, $wiki_page_id = null) {
		$this->layout = 'ajax';
		$this->autoRender = false;
		//"WikiPage.is_linked"=>0,
		$conditions = ["WikiPage.project_id" => $project_id, "WikiPage.wiki_id" => $wiki_id, "WikiPage.is_linked" => 0, "WikiPage.user_id" => $user_id, 'WikiPage.is_archived' => 1, 'WikiPage.is_deleted !=' => 1];
		if (isset($wiki_page_id) && !empty($wiki_page_id)) {
			$conditions = ["WikiPage.id" => $wiki_page_id];
		}
		if (isset($this->data['keyword']) && !empty($this->data['keyword'])) {
			$keyword = trim($this->data['keyword']);
			//$conditions['OR'] = ["WikiPage.title LIKE" => "%" . $keyword . "%"];
		}

		$allWikiPages = $this->WikiPage->find("all", array(
			"conditions" => $conditions, "order" => "WikiPage.sort_order ASC",
		)
		);
		//pr($allWikiPages);

		$this->set(compact('project_id', 'user_id', 'wiki_id', 'allWikiPages'));
		$this->render("partials/wiki_read/get_wiki_page_by_user");
	}

	public function get_wiki_page_by_user_dashboard($project_id = null, $user_id = null, $wiki_id = null, $wiki_page_id = null, $author = null) {
		$this->layout = 'ajax';
		$this->autoRender = false;
		$this->WikiPage->unbindModel(array('belongsTo' => array('Project')));
		$filter = '';
		$conditions = [
			"WikiPage.project_id" => $project_id,
			"WikiPage.wiki_id" => $wiki_id,
			"WikiPage.is_linked" => 0,
			//"WikiPage.user_id" => $user_id,
			'WikiPage.is_archived' => 1,
			'WikiPage.is_deleted !=' => 1,
		];
		if (isset($wiki_page_id) && !empty($wiki_page_id) && is_numeric($wiki_page_id)) {

			$conditions['AND'] = ["WikiPage.id" => $wiki_page_id];
		}

		if (isset($this->request->data['val']) && !empty($this->request->data['val'])) {
			$filter = $this->request->data['val'];

			if ($filter == 'all') {
				// $conditions = array("Activity.relation_id" => $id, "Activity.element_type" => $type);
			} else if ($filter == 'today') {
				$start = date('Y-m-d');
				$end = date('Y-m-d');
				$conditions['AND'] = array('DATE_FORMAT(FROM_UNIXTIME(WikiPage.created), "%Y-%m-%d") BETWEEN ? AND ?' => array("$start", "$end"));
			} else if ($filter == 'last_7_day') {
				$end = date('Y-m-d');
				$start = date('Y-m-d', strtotime('-7 day'));
				$conditions['AND'] = array('DATE_FORMAT(FROM_UNIXTIME(WikiPage.created), "%Y-%m-%d") BETWEEN ? AND ?' => array("$start", "$end"));
			} else if ($filter == 'this_month') {
				$end = date('Y-m-t');
				$start = date('Y-m-01');
				$conditions['AND'] = array('DATE_FORMAT(FROM_UNIXTIME(WikiPage.created), "%Y-%m-%d") BETWEEN ? AND ?' => array("$start", "$end"));
			}
		}

		if (isset($this->data['keyword']) && !empty($this->data['keyword'])) {
			$keyword = trim($this->data['keyword']);
			$conditions['OR'] = ["WikiPage.title LIKE" => "%" . $keyword . "%"];
		}

		//pr($conditions);
		$allWikiPages = $this->WikiPage->find("all", array(
			"conditions" => $conditions,
		)
		);
		// e($this->WikiPage->_query());

		$this->set(compact('project_id', 'user_id', 'wiki_id', 'wiki_page_id', 'author', 'filter', 'allWikiPages'));
		$this->render("partials/wiki_dashboard/wiki_page_list");
	}

	public function get_wiki_page($project_id = null, $user_id = null, $wiki_id = null, $wiki_page_id = null) {
		$this->layout = 'ajax';
		$this->autoRender = false;

		$oldviews = $this->WikiPageView->find("first", array("conditions" => array("WikiPageView.wiki_id" => $wiki_id, "WikiPageView.user_id" => $this->user_id, "WikiPageView.wiki_page_id" => $wiki_page_id)));

		if (isset($oldviews) && !empty($oldviews)) {

			$this->request->data['WikiPageView'] = array("wiki_id" => $wiki_id, "user_id" => $this->user_id, "wiki_page_id" => $wiki_page_id);
			$this->WikiPageView->updateAll(array("WikiPageView.views" => "WikiPageView.views +1"), $this->request->data['WikiPageView']);
		} else {
			$this->request->data['WikiPageView'] = array("views" => "views+1", "wiki_id" => $wiki_id, "user_id" => $this->user_id, "wiki_page_id" => $wiki_page_id);
			$this->WikiPageView->save($this->request->data);
		}

		$conditions = ["WikiPage.project_id" => $project_id, "WikiPage.wiki_id" => $wiki_id, "WikiPage.user_id" => $user_id, 'WikiPage.is_archived' => 1, "WikiPage.is_linked" => 0, 'WikiPage.is_deleted !=' => 1];
		if (isset($wiki_page_id) && !empty($wiki_page_id)) {
			$conditions = ["WikiPage.id" => $wiki_page_id];
		}

		$allWikiPages = $this->WikiPage->find("all", array(
			"conditions" => $conditions,
		)
		);

		$this->set(compact('project_id', 'user_id', 'wiki_id', 'allWikiPages'));
		$this->render("partials/wiki_read/get_wiki_page");
	}

	public function get_all_user_on_this_wiki_count_____($project_id = null, $user_id = null, $wiki_id = null) {
		$conditions = ["WikiPage.project_id" => $project_id, "WikiPage.wiki_id" => $wiki_id, 'WikiPage.is_deleted !=' => 1];
		$w_users = $wp_users = $wpcd_users = null;
		$users = array();
		$allWikiUsers = $this->Wiki->find("all", array(
			"conditions" => array("Wiki.id" => $wiki_id),
			"recursive" => -1,
			"fields" => array("Wiki.user_id"),
		)
		);
		$allWikiPageUsers = $this->WikiPage->find("all", array(
			"conditions" => $conditions,
			"recursive" => -1,
			"fields" => array("WikiPage.user_id"),
		)
		);
		$allWikiPageCommentDocumentUsers = $this->WikiPageCommentDocument->find("all", array(
			"conditions" => array("WikiPageCommentDocument.wiki_id" => $wiki_id),
			"recursive" => -1,
			"fields" => array("WikiPageCommentDocument.user_id"),
		)
		);
		$w_users = Set::extract('/Wiki/user_id', $allWikiUsers);

		if (isset($allWikiUsers) && !empty($allWikiUsers)) {

			$users = array_merge($users, $w_users);
		}
		if (isset($allWikiPageUsers) && !empty($allWikiPageUsers)) {
			$wp_users = Set::extract('/WikiPage/user_id', $allWikiPageUsers);
			$users = array_merge($w_users, $wp_users);
		}
		if (isset($allWikiPageCommentDocumentUsers) && !empty($allWikiPageCommentDocumentUsers)) {
			$wpcd_users = Set::extract('/WikiPageCommentDocument/user_id', $allWikiPageCommentDocumentUsers);
			$users = array_merge($users, $wpcd_users);
		}

		$this->set(compact('wiki_id', 'users', 'allWikiUsers', 'allWikiPageUsers', 'allWikiPageCommentDocumentUsers'));

		return ( isset($users) && !empty($users) ) ? count(array_unique($users)) : null;
	}

	public function get_all_user_on_this_wiki_count($project_id = null, $user_id = null, $wiki_id = null) {
		$project_people = $this->requestAction(array("controller" => "wikies", "action" => "project_people", $project_id));
		$people = array();
		if (isset($project_people) && !empty($project_people)) {
			foreach ($project_people as $project_peop) {
				if (isset($project_peop) && !empty($project_peop)) {
					$people[] = $project_peop;
				}
			}
		}
		return isset($people) ? array_unique($people) : null;
	}

	public function get_all_user_on_this_wiki($project_id = null, $user_id = null, $wiki_id = null) {
		$conditions = ["WikiPage.project_id" => $project_id, "WikiPage.wiki_id" => $wiki_id, 'WikiPage.is_deleted !=' => 1];
		$users = $w_users = $wp_users = $wpcd_users = null;

		$project_people = $this->requestAction(array("controller" => "wikies", "action" => "project_people", $project_id));

		$allWikiUsers = $this->Wiki->find("all", array(
			"conditions" => array("Wiki.id" => $wiki_id),
			"recursive" => -1,
			"fields" => array("Wiki.user_id"),
		)
		);
		$allWikiPageUsers = $this->WikiPage->find("all", array(
			"conditions" => $conditions,
			"recursive" => -1,
			"fields" => array("WikiPage.user_id"),
		)
		);
		/*  $allWikiPageCommentDocumentUsers = $this->WikiPageCommentDocument->find("all", array(
			          "conditions" => array("WikiPageCommentDocument.wiki_id" => $wiki_id),
			          "recursive" => -1,
			          "fields" => array("WikiPageCommentDocument.user_id")
			          )
		*/
		$w_users = Set::extract('/Wiki/user_id', $allWikiUsers);
		if (isset($allWikiPageUsers) && !empty($allWikiPageUsers)) {
			$wp_users = Set::extract('/WikiPage/user_id', $allWikiPageUsers);
			$users = array_merge($w_users, $wp_users);
		}
		/*  if (isset($allWikiPageCommentDocumentUsers) && !empty($allWikiPageCommentDocumentUsers)) {
			          $wpcd_users = Set::extract('/WikiPageCommentDocument/user_id', $allWikiPageCommentDocumentUsers);
			          $users = array_merge($users, $wpcd_users);
			          }
		*/
		$users = isset($users) ? array_unique($users) : null;

		//$total_users = count($users) + count($allWikiUsers) +  count($allWikiPageUsers) + count($project_people);

		$this->set(compact('wiki_id', 'users', 'allWikiUsers', 'allWikiPageUsers', 'project_id', 'project_people'));
	}

	public function get_wiki_page_history_by_user($project_id = null, $user_id = null, $wiki_id = null, $wiki_page_id = null) {
		$this->layout = 'ajax';
		$this->autoRender = false;
		$conditions = [
			"WikiPage.project_id" => $project_id,
			"WikiPage.wiki_id" => $wiki_id,
			"WikiPage.updated_user_id" => $user_id,
			'WikiPage.is_archived' => array(0, 1), /* ----only for active and inactive both--- */
			"WikiPage.is_linked" => 0,
			'WikiPage.is_deleted !=' => 1,
			'WikiPage.revision_id !=' => 0, /* ----For selecting history pages not inlucded main or first page--- */
		];
		if (isset($wiki_page_id) && !empty($wiki_page_id)) {
			$conditions = ["WikiPage.id" => $wiki_page_id];
		}

		$allWikiPages = $this->WikiPage->find("all", array(
			"conditions" => $conditions,
		)
		);

		//	pr($allWikiPages);

		$this->set(compact('project_id', 'user_id', 'wiki_id', 'allWikiPages'));
		$this->render("partials/wiki_history/get_wiki_page_history_by_user");
	}

	public function get_updated_pages($project_id = null, $user_id = null, $wiki_id = null, $wiki_page_id = null) {
		$this->layout = 'ajax';
		$this->autoRender = false;
		$conditions = [
			"WikiPage.project_id" => $project_id,
			"WikiPage.wiki_id" => $wiki_id,
			"WikiPage.revision_id" => $wiki_page_id,
			"WikiPage.is_linked" => 0,
			'WikiPage.is_archived' => array(0, 1),
			'WikiPage.is_deleted !=' => 1,
		];
		if (isset($wiki_page_id) && !empty($wiki_page_id)) {
			//$conditions = ["WikiPage.id" => $wiki_page_id];
		}

		$allWikiPages = $this->WikiPage->find("all", array(
			"conditions" => $conditions,
		)
		);
		//pr($allWikiPages);
		$this->set(compact('project_id', 'user_id', 'wiki_id', 'allWikiPages'));
		$this->render("partials/wiki_history/get_wiki_page_history_by_user");
	}

	public function delete_wiki_page($project_id = null, $user_id = null, $wiki_id = null, $wiki_page_id = null) {
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			$this->autoRender = false;
			$this->set(compact('project_id', 'user_id', 'wiki_id', 'wiki_page_id'));
			$response = null;
			$response['msg'] = '';
			$response['success'] = false;
			$response['content'] = null;

			$view_vars['project_id'] = $project_id;
			$view_vars['user_id'] = $user_id;
			$view_vars['wiki_id'] = $wiki_id;
			$view_vars['wiki_page_id'] = $wiki_page_id;
			$this->setJsVar('view_vars', $view_vars);

			if (!empty($wiki_page_id)) {
				$this->request->data['WikiPage']['id'] = $wiki_page_id;
				$this->request->data['WikiPage']['is_deleted'] = 1;
				if ($this->WikiPage->save($this->request->data)) {
					$main_page_id = $this->WikiPage->findById($wiki_page_id);

					if (isset($main_page_id['WikiPage']['revision_id']) && !empty($main_page_id['WikiPage']['revision_id']) && $main_page_id['WikiPage']['revision_id'] > 0) {
						//$this->WikiPage->updateAll(array("WikiPage.is_deleted" => 1), array("WikiPage.id" => $main_page_id['WikiPage']['revision_id']));
						//$this->WikiPage->updateAll(array("WikiPage.is_deleted" => 1), array("WikiPage.revision_id" => $main_page_id['WikiPage']['revision_id']));
					}

					//$this->WikiPage->updateAll(array("WikiPage.is_deleted" => 1), array("WikiPage.revision_id" => $wiki_page_id));

					$response['success'] = true;
					$response['msg'] = 'Wiki page deleted successfully.';
				}
			} else {
				$response['msg'] = 'Wiki page could not be deleted successfully.';
			}
			echo json_encode($response);
			exit;
		}
	}

	public function delete_wiki($project_id = null, $user_id = null, $wiki_id = null) {
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			$this->autoRender = false;
			$this->set(compact('project_id', 'user_id', 'wiki_id'));
			$response = null;
			$response['msg'] = '';
			$response['success'] = false;
			$response['content'] = null;

			$view_vars['project_id'] = $project_id;
			$view_vars['user_id'] = $user_id;
			$view_vars['wiki_id'] = $wiki_id;
			$this->setJsVar('view_vars', $view_vars);

			if (!empty($wiki_id)) {
				$wikidata = $this->Wiki->findById($wiki_id);
				if ($this->Wiki->delete($wiki_id)) {
					$wikipagecommentdocument = $this->WikiPageCommentDocument->find("all", array("conditions" => array("WikiPageCommentDocument.wiki_id" => $wiki_id)));
					$wikipagecomment = $this->WikiPageComment->find("all", array("fields" => array("WikiPageComment.id"), "conditions" => array("WikiPageComment.wiki_id" => $wiki_id)));
					$wikipage = $this->WikiPage->find("list", array("fields" => array("WikiPage.id"), "conditions" => array("WikiPage.wiki_id" => $wiki_id)));
					$this->WikiPage->deleteAll(array("WikiPage.wiki_id" => $wiki_id));
					$this->WikiPageLike->deleteAll(array("WikiPageLike.wiki_page_id" => $wikipage));
					$this->WikiPageCommentLike->deleteAll(array("WikiPageCommentLike.wiki_page_comment_id" => $wikipagecomment));
					$this->WikiPageView->deleteAll(array("WikiPageView.wiki_id" => $wiki_id));
					$this->WikiPageCommentDocument->deleteAll(array("WikiPageCommentDocument.wiki_id" => $wiki_id));
					$this->Wiki->deleteAll(array("Wiki.revision_id" => $wiki_id));

					$this->Wiki->delete($wiki_id);

					if (isset($wikipagecommentdocument) && !empty($wikipagecommentdocument)) {
						foreach ($wikipagecommentdocument as $document) {
							$filePath = WIKI_PAGE_DOCUMENT . $document['WikiPageCommentDocument']['document_name'];
							if (file_exists($filePath)) {
								unlink($filePath);
							}
						}
					}

					//======= Wiki Email Notification =================================
					$data = $this->get_all_user_email($project_id);
					if (isset($data) && !empty($data)) {
						foreach ($data as $user_id) {
							$users = $this->User->findById($user_id);
							if (isset($users['User']) && !empty($users['User']['id'])) {

								$notifiragstatus = $this->EmailNotification->find('first', ['conditions' => ['notification_type' => 'team_talk', 'personlization' => 'wiki_deleted', 'user_id' => $user_id]]);

								$usersDetails = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));

								if ((!isset($notifiragstatus['EmailNotification']['email']) || $notifiragstatus['EmailNotification']['email'] == 1) && (!isset($usersDetails['User']['email_notification']) || $usersDetails['User']['email_notification'] == 1)) {
									//== below function will be opened in future
									//$sendMail = $this->CommonEmail->sendEmailWiki($users, $wikidata, "deleted");

								}

							}
						}
					}
					//=================================================================

					$response['success'] = true;
					$response['msg'] = 'Wiki deleted successfully.';
				}
			} else {
				$response['msg'] = 'Wiki could not be deleted successfully.';
			}
			echo json_encode($response);
			exit;
		}
	}

	public function wiki_page_sign_off($project_id = null, $user_id = null, $wiki_id = null, $wiki_page_id = null) {
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			$this->autoRender = false;
			$this->set(compact('project_id', 'user_id', 'wiki_id', 'wiki_page_id'));
			$response = null;
			$response['msg'] = '';
			$response['success'] = false;
			$response['content'] = null;

			$view_vars['project_id'] = $project_id;
			$view_vars['user_id'] = $user_id;
			$view_vars['wiki_id'] = $wiki_id;
			$view_vars['wiki_page_id'] = $wiki_page_id;
			$this->setJsVar('view_vars', $view_vars);

			if (!empty($wiki_page_id)) {
				$this->request->data['WikiPage']['id'] = $wiki_page_id;
				$this->request->data['WikiPage']['sign_off'] = 1;
				if ($this->WikiPage->save($this->request->data)) {

					$main_page_id = $this->WikiPage->findById($wiki_page_id);

					if (isset($main_page_id['WikiPage']['revision_id']) && !empty($main_page_id['WikiPage']['revision_id']) && $main_page_id['WikiPage']['revision_id'] > 0) {
						//$this->WikiPage->updateAll(array("WikiPage.sign_off" => 1), array("WikiPage.id" => $main_page_id['WikiPage']['revision_id']));
						//$this->WikiPage->updateAll(array("WikiPage.sign_off" => 1), array("WikiPage.revision_id" => $main_page_id['WikiPage']['revision_id']));
					}

					//$this->WikiPage->updateAll(array("WikiPage.sign_off" => 1), array("WikiPage.revision_id" => $wiki_page_id));
					$response['success'] = true;
					$response['msg'] = 'Wiki page signed off successfully.';
				}
			} else {
				$response['msg'] = 'Wiki page could not be signed off successfully.';
			}
			echo json_encode($response);
			exit;
		}
	}

	public function wiki_history_page_approved($project_id = null, $user_id = null, $wiki_id = null, $wiki_page_id = null) {
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			$this->autoRender = false;
			$this->set(compact('project_id', 'user_id', 'wiki_id', 'wiki_page_id'));
			$response = null;
			$response['msg'] = '';
			$response['success'] = false;
			$response['content'] = null;

			$view_vars['project_id'] = $project_id;
			$view_vars['user_id'] = $user_id;
			$view_vars['wiki_id'] = $wiki_id;
			$view_vars['wiki_page_id'] = $wiki_page_id;
			$this->setJsVar('view_vars', $view_vars);
			$wikipage = $this->WikiPage->findById($wiki_page_id);
			$parent_page = $this->WikiPage->findById($wikipage['WikiPage']['revision_id']);
			if (!empty($wiki_page_id)) {
				$this->request->data['WikiPage']['id'] = $wiki_page_id;
				$this->request->data['WikiPage']['is_archived'] = 1;
				if (isset($parent_page['WikiPage']['sign_off']) && $parent_page['WikiPage']['sign_off'] == 0) {
					if ($this->WikiPage->save($this->request->data)) {
						$this->WikiPage->updateAll(array("WikiPage.is_archived" => 0), array("WikiPage.revision_id" => $parent_page['WikiPage']['id']));

						$date = date('Y-m-d h:i:s');

						$this->WikiPage->updateAll(array("WikiPage.is_archived" => 0), array("WikiPage.id" => $parent_page['WikiPage']['id']));
						$this->WikiPage->updateAll(array("WikiPage.is_archived" => 1, "WikiPage.archieved_on" => "'" . $date . "'"), array("WikiPage.id" => $wiki_page_id));

						$response['success'] = true;
						$response['msg'] = 'Wiki page approved successfully.';
					}
				} else {
					$response['msg'] = 'This update cannot be approved because page has already been signed-off.';
				}
			} else {
				$response['msg'] = 'Wiki page could not be approved successfully.';
			}
			echo json_encode($response);
			exit;
		}
	}

	public function requestsave($id = null, $status = null) {
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			$this->autoRender = false;
			$response = null;
			$response['msg'] = '';
			$response['success'] = false;
			$response['content'] = null;

			if (!empty($id)) {
				$this->request->data['WikiUser']['id'] = $id;
				$this->request->data['WikiUser']['approved'] = $status;
				if ($this->WikiUser->save($this->request->data)) {
					//$this->Session->setFlash('Wiki page deleted successfully.', 'success');
					$response['success'] = true;
					$response['msg'] = 'Wiki request "' . ($status == 1 ? "accepted" : "declined") . '" successfully.';
				}
			} else {
				$response['msg'] = 'Wiki request could not be "' . ($status == 1 ? "accepted" : "declined") . '" successfully.';
			}
			echo json_encode($response);
			exit;
		}
	}

	public function request() {
		$this->layout = 'inner';
		$user_id = $this->user_id;
		$this->set('title_for_layout', __('Wiki Requests', true));
		$this->set('page_heading', __('Wiki Requests', true));
		$this->set('page_subheading', __('View Wiki Requests', true));

		$this->WikiUser->bindModel(array(
			'hasOne' => array(
				'Wiki' => array(
					'className' => 'Wiki',
					'foreignKey' => false,
					"conditions" => array("Wiki.id = WikiUser.wiki_id"),
				),
			),
		));
		$conditions = array("WikiUser.owner_id" => $user_id);

		if (isset($this->params->query['status'])) {
			$status = $this->params->query['status'];
			$this->set('status', $status);
			$v = trim($this->params->query['status']);
			if (!empty($v)) {
				if ($status == 'accept') {
					$conditions['AND'][] = array('WikiUser.approved' => 1);
				} else if ($status == 'decline') {
					$conditions['AND'][] = array('WikiUser.approved' => 2);
				} else if ($status == 'pending') {
					$conditions['AND'][] = array('WikiUser.approved' => 0);
				}
			}
		}
		if (isset($this->params->query['keywords'])) {
			$keywords = $this->params->query['keywords'];
			$this->set('keywords', $keywords);
			$v = trim($this->params->query['keywords']);
			if (!empty($v)) {
				$conditions['OR'][] = array('Wiki.title LIKE' => '%' . trim($keywords) . '%');
			}
		}
		//$data = $this->WikiUser->find("all", array("conditions" => array("WikiUser.owner_id" => $user_id)));

		$this->paginate = [
			'order' => 'WikiUser.approved DESC',
			'conditions' => $conditions,
			'limit' => 10000,
		];
		$data = $this->paginate($this->WikiUser);

		$this->set("data", $data);
		$crumb = [
			'Summary' => [
				'data' => [
					'url' => '/wikies/index',
					'class' => 'tipText',
					'title' => 'Wiki',
					'data-original-title' => 'Wiki',
				],
			],
			'last' => [
				'data' => [
					'title' => 'Wiki Request lists',
					'data-original-title' => 'Wiki Requests',
				],
			],
		];
		$this->set('crumb', $crumb);
	}

	public function wikidetails($id = null) {
		$this->layout = 'inner';
		$user_id = $this->user_id;
		$this->set('title_for_layout', __('Wiki Details', true));
		$this->set('page_heading', __('Wiki Details', true));
		$this->set('page_subheading', __('View Wiki Details', true));
		$this->WikiUser->bindModel(array(
			'belongsTo' => array(
				'Wiki' => array(
					'className' => 'Wiki',
					'foreignKey' => "wiki_id",
				),
			),
		));

		$data = $this->WikiUser->find("first", array("conditions" => array("WikiUser.id" => $id)));

		$this->set("data", $data);
		$crumb = [
			'Summary' => [
				'data' => [
					'url' => '/wikies/index',
					'class' => 'tipText',
					'title' => 'Wiki',
					'data-original-title' => 'Wiki',
				],
			],
			'last' => [
				'data' => [
					'title' => 'Wiki Details',
					'data-original-title' => 'Wiki Details',
				],
			],
		];
		$this->set('crumb', $crumb);
	}

	public function getWikiRequestCount() {
		$user_id = $this->user_id;
		$this->layout = false;
		$this->autoRender = false;

		$count = $this->WikiUser->find("count", array("conditions" => array("WikiUser.owner_id" => $user_id, "WikiUser.approved" => 0)));
		return $count;
	}

	public function wiki_request($project_id = null, $user_id = null, $wiki_id = null, $wiki_page_id = null) {
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			$this->autoRender = false;
			$this->set(compact('project_id', 'user_id', 'wiki_id', 'wiki_page_id'));
			$response = null;
			$response['msg'] = '';
			$response['success'] = false;
			$response['content'] = null;

			$view_vars['project_id'] = $project_id;
			$view_vars['user_id'] = $user_id;
			$view_vars['wiki_id'] = $wiki_id;
			$view_vars['wiki_page_id'] = $wiki_page_id;
			$this->setJsVar('view_vars', $view_vars);

			if (!empty($wiki_id)) {
				$wiki_details = $this->Wiki->findById($wiki_id);
				$this->request->data['WikiUser']['user_id'] = $user_id;
				$this->request->data['WikiUser']['wiki_id'] = $wiki_id;
				$this->request->data['WikiUser']['owner_id'] = $wiki_details['Wiki']['user_id'];
				if ($this->WikiUser->save($this->request->data)) {
					//$this->Session->setFlash('Wiki request has been successfully send.', 'success');
					$response['success'] = true;
					$response['msg'] = 'Wiki request has been send successfully.';
				}
			} else {
				$response['msg'] = 'Wiki request could not be send successfully.';
			}
			echo json_encode($response);
			exit;
		}
	}

	public function like_comment($wiki_page_id = null) {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$data = $response = null;
			$response = ['content' => null, 'success' => false];

			if (isset($wiki_page_id) && !empty($wiki_page_id)) {
				// check that the current user not posted like for this comment previously
				$data = $this->WikiPageLike->find('count', [
					'conditions' => [
						'WikiPageLike.user_id' => $this->user_id,
						'WikiPageLike.wiki_page_id' => $wiki_page_id,
					],
					'recursive' => -1,
				]);

				// if the current user not posted like for this comment previously, only then enter data in database
				if (isset($data) && empty($data)) {
					$in_data['WikiPageLike']['user_id'] = $this->user_id;
					$in_data['WikiPageLike']['wiki_page_id'] = $wiki_page_id;
					// pr($this->DoListCommentLike->save($in_data), 1);
					if ($this->WikiPageLike->save($in_data)) {
						$response['success'] = true;
					}
				}
			}

			echo json_encode($response);
			exit();
		}
	}

	public function wiki_page_comment_like($wiki_page_comment_id = null) {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';

			$data = $response = null;
			$response = ['content' => null, 'success' => false];

			if (isset($wiki_page_comment_id) && !empty($wiki_page_comment_id)) {
				// check that the current user not posted like for this comment previously
				$data = $this->WikiPageCommentLike->find('count', [
					'conditions' => [
						'WikiPageCommentLike.user_id' => $this->user_id,
						'WikiPageCommentLike.wiki_page_comment_id' => $wiki_page_comment_id,
					],
					'recursive' => -1,
				]);

				// if the current user not posted like for this comment previously, only then enter data in database
				if (isset($data) && empty($data)) {
					$in_data['WikiPageCommentLike']['user_id'] = $this->user_id;
					$in_data['WikiPageCommentLike']['wiki_page_comment_id'] = $wiki_page_comment_id;
					// pr($this->DoListCommentLike->save($in_data), 1);
					if ($this->WikiPageCommentLike->save($in_data)) {
						$response['success'] = true;
					}
				}
			}

			echo json_encode($response);
			exit();
		}
	}

	public function add_wiki_page_comments($project_id = null, $user_id = null, $wiki_id = null, $wiki_page_id = null) {
		$this->layout = 'ajax';
		$this->autoRender = false;

		$this->set(compact('project_id', 'user_id', 'wiki_id', 'wiki_page_id'));
		$this->render("partials/wiki_comment/add_wiki_page_comments");
	}

	public function edit_wiki_page_comments($project_id = null, $user_id = null, $wiki_id = null, $wiki_page_id = null, $comment_id = null) {
		$this->layout = 'ajax';
		$this->autoRender = false;

		$this->request->data = $this->WikiPageComment->read(null, $comment_id);

		$this->set(compact('project_id', 'user_id', 'wiki_id', 'wiki_page_id', 'comment_id'));
		$this->render("partials/wiki_comment/edit_wiki_page_comments");
	}

	public function wiki_page_comment_save($project_id = null, $user_id = null, $wiki_id = null, $wiki_page_id = null, $comment_id = null) {

		if ($this->request->isAjax()) {
			$this->set(compact('project_id', 'user_id', 'wiki_id', 'wiki_page_id', 'comment_id'));
			$this->layout = 'ajax';
			$this->autoRender = false;
			$response = ['success' => false, 'msg' => "Invalid", 'content' => null];
			if ($this->request->is('post') || $this->request->is('put')) {
				$this->WikiPageComment->set($this->data);
				if ($this->WikiPageComment->validates()) {
					if (isset($this->request->data['WikiPageCommentDocument']['file_name']) && !empty($this->request->data['WikiPageCommentDocument']['file_name'])) {
						$files = $this->request->data['WikiPageCommentDocument']['file_name'];
						unset($this->request->data['WikiPageCommentDocument']['file_name']);
						foreach ($files as $file_key => $file) {
							$this->request->data['WikiPageCommentDocument'][$file_key]["document_name"] = $file;
							$this->request->data['WikiPageCommentDocument'][$file_key]["project_id"] = $this->request->data['WikiPageComment']['project_id'];
							$this->request->data['WikiPageCommentDocument'][$file_key]["wiki_id"] = $this->request->data['WikiPageComment']['wiki_id'];
							$this->request->data['WikiPageCommentDocument'][$file_key]["user_id"] = $this->request->data['WikiPageComment']['user_id'];
							$this->request->data['WikiPageCommentDocument'][$file_key]["wiki_page_id"] = $this->request->data['WikiPageComment']['wiki_page_id'];
						}
					}
					//pr($this->request->data,1);
					if ($this->WikiPageComment->saveAll($this->request->data)) {
						$this->Common->projectModified($project_id, $this->user_id);
						$response['success'] = true;
						$response['msg'] = 'The comment has been saved successfully.';
						$response['content'] = $this->request->data;
						$document_count = $this->WikiPageCommentDocument->find('count', ['conditions' => ['WikiPageCommentDocument.wiki_id' => $this->request->data['WikiPageComment']['wiki_id']]]);
						$response['document_count'] = $document_count;

						echo json_encode($response);
						exit();
					}
				} else {
					$response['success'] = false;
					$response['msg'] = 'The comment could not be saved. Please, try again.';
					$response['content'] = $this->validateErrors($this->WikiPageComment);
					echo json_encode($response);
					exit();
				}
			}
		}
	}

	public function get_wiki_page_comment($project_id = null, $user_id = null, $wiki_id = null, $wiki_page_id = null) {
		$this->layout = 'ajax';
		$this->autoRender = false;

		$conditions[] = ["WikiPageComment.wiki_id" => $wiki_id, 'WikiPage.is_archived' => 1, "WikiPage.is_linked" => 0, 'WikiPage.is_deleted !=' => 1];
		if (isset($wiki_page_id) && !empty($wiki_page_id)) {
			$conditions[] = ["WikiPageComment.wiki_page_id" => $wiki_page_id];
		}
		$wikipagecomments = $this->WikiPageComment->find("all", array("conditions" => $conditions, "order" => "WikiPageComment.updated DESC"));
		$wikipagecomments_rating = $this->WikiPageComment->find("all", array("conditions" => $conditions, "order" => ["WikiPageComment.rating" => "DESC", "WikiPageComment.updated" => "DESC"]));

		$this->set(compact('project_id', 'user_id', 'wiki_id', 'wiki_page_id', 'wikipagecomments', 'wikipagecomments_rating'));
		if ($this->request->isAjax() && isset($wiki_page_id) && !empty($wiki_page_id)) {
			$this->render("partials/wiki_comment/get_wiki_page_comment");
		} else {
			return $wikipagecomments;
		}
	}

	// added 12 September 2016 by PS
	public function get_wiki_page_comment_rating($project_id = null, $user_id = null, $wiki_id = null, $wiki_page_id = null) {
		$this->layout = 'ajax';
		$this->autoRender = false;

		$conditions[] = ["WikiPageComment.wiki_id" => $wiki_id, 'WikiPage.is_archived' => 1, 'WikiPage.is_deleted !=' => 1, 'WikiPage.is_linked' => 0];
		if (isset($wiki_page_id) && !empty($wiki_page_id)) {
			$conditions[] = ["WikiPageComment.wiki_page_id" => $wiki_page_id];
		}
		$wikipagecomments_rating = $this->WikiPageComment->find("all", array("conditions" => $conditions, "order" => ["WikiPageComment.rating" => "DESC", "WikiPageComment.id" => "DESC"]));

		$this->set(compact('project_id', 'user_id', 'wiki_id', 'wiki_page_id', 'wikipagecomments_rating'));
		if ($this->request->isAjax() && isset($wiki_page_id) && !empty($wiki_page_id)) {
			$this->render("partials/wiki_comment/get_wiki_page_comment");
		} else {
			return $wikipagecomments_rating;
		}
	}

	public function get_wiki_page_comment_admin($project_id = null, $user_id = null, $wiki_id = null, $wiki_page_id = null) {
		$this->layout = 'ajax';
		$this->autoRender = false;
		$conditions[] = ["WikiPageComment.wiki_id" => $wiki_id];
		if (isset($wiki_page_id) && !empty($wiki_page_id)) {
			$conditions[] = ["WikiPageComment.wiki_page_id" => $wiki_page_id];
		}
		$wikipagecomments = $this->WikiPageComment->find("all", array("conditions" => $conditions, "order" => "WikiPageComment.updated DESC"));
		$commcount =  ( isset($wikipagecomments) && !empty($wikipagecomments) ) ? count($wikipagecomments) : 0;
		$this->set(compact('project_id', 'user_id', 'wiki_id', 'wiki_page_id', 'commcount', 'wikipagecomments'));
		$this->render("partials/wiki_admin/get_wiki_page_comment");
	}

	public function get_wiki_page_comment_admin_count($project_id = null, $user_id = null, $wiki_id = null, $wiki_page_id = null) {
		$this->layout = 'ajax';
		$this->autoRender = false;
		$conditions[] = ["WikiPageComment.wiki_id" => $wiki_id];
		if (isset($wiki_page_id) && !empty($wiki_page_id)) {
			$conditions[] = ["WikiPageComment.wiki_page_id" => $wiki_page_id];
		}
		$wikipagecomments = $this->WikiPageComment->find("count", array("conditions" => $conditions, "order" => "WikiPageComment.id DESC"));
		$commcount = $wikipagecomments;
		$this->set(compact('project_id', 'user_id', 'wiki_id', 'wiki_page_id', 'commcount', 'wikipagecomments'));
		return $wikipagecomments;
	}

	public function get_wiki_page_document_admin_count($project_id = null, $user_id = null, $wiki_id = null, $wiki_page_id = null) {
		$this->layout = 'ajax';
		$this->layout = 'ajax';
		$this->autoRender = false;
		$conditions[] = ["WikiPageCommentDocument.wiki_id" => $wiki_id];
		//$conditions[] = ["WikiPageCommentDocument.user_id" => $user_id];
		$conditions[] = ["WikiPageCommentDocument.wiki_page_id" => $wiki_page_id];

		$wikipagedocuments = $this->WikiPageCommentDocument->find("count", array(
			"conditions" => $conditions, "order" => "WikiPageCommentDocument.id DESC"));
		$docucount = $wikipagedocuments;
		$this->set(compact('project_id', 'user_id', 'wiki_id', 'wiki_page_id', 'docucount', 'wikipagedocuments'));

		return $wikipagedocuments;
	}

	public function get_wiki_page_comment_by_user_admin_count($project_id = null, $user_id = null, $wiki_id = null, $wiki_page_id = null) {
		$this->layout = 'ajax';
		$this->autoRender = false;
		$conditions[] = ["WikiPageComment.wiki_id" => $wiki_id];
		$conditions[] = ["WikiPageComment.user_id" => $user_id];
		if (isset($wiki_page_id) && !empty($wiki_page_id)) {
			$conditions[] = ["WikiPageComment.wiki_page_id" => $wiki_page_id];
		}
		$wikipagecomments = $this->WikiPageComment->find("count", array("conditions" => $conditions, "order" => "WikiPageComment.id DESC"));
		$commcount = $wikipagecomments;
		$this->set(compact('project_id', 'user_id', 'wiki_id', 'wiki_page_id', 'commcount', 'wikipagecomments'));
		return $wikipagecomments;
	}

	public function get_wiki_page_document_by_user_admin_count($project_id = null, $user_id = null, $wiki_id = null, $wiki_page_id = null) {
		$this->layout = 'ajax';
		$this->layout = 'ajax';
		$this->autoRender = false;
		$conditions[] = ["WikiPageCommentDocument.wiki_id" => $wiki_id];
		$conditions[] = ["WikiPageCommentDocument.user_id" => $user_id];
		$conditions[] = ["WikiPageCommentDocument.wiki_page_id" => $wiki_page_id];

		$wikipagedocuments = $this->WikiPageCommentDocument->find("count", array(
			"conditions" => $conditions, "order" => "WikiPageCommentDocument.id DESC"));
		$docucount = $wikipagedocuments;
		$this->set(compact('project_id', 'user_id', 'wiki_id', 'wiki_page_id', 'docucount', 'wikipagedocuments'));

		return $wikipagedocuments;
	}

	public function get_wiki_page_document_admin($project_id = null, $user_id = null, $wiki_id = null, $wiki_page_id = null) {
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			$this->layout = 'ajax';
			$this->autoRender = false;
			$conditions[] = ["WikiPageCommentDocument.wiki_id" => $wiki_id];
			//$conditions[] = ["WikiPageCommentDocument.user_id" => $user_id];
			$conditions[] = ["WikiPageCommentDocument.wiki_page_id" => $wiki_page_id];

			$wikipagedocuments = $this->WikiPageCommentDocument->find("all", array(
				"conditions" => $conditions, "order" => "WikiPageCommentDocument.id DESC"));
			$docucount = ( isset($wikipagedocuments) && !empty($wikipagedocuments) ) ? count($wikipagedocuments) : 0;
			$this->set(compact('project_id', 'user_id', 'wiki_id', 'wiki_page_id', 'docucount', 'wikipagedocuments'));
			$this->render("partials/wiki_admin/get_wiki_page_document");
		}
	}

	public function wiki_page_sorting($project_id = null, $user_id = null, $wiki_id = null, $wiki_page_id = null) {
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			$this->layout = 'ajax';
			$this->autoRender = false;

			if (isset($this->request->data['sort_orders']) && !empty($this->request->data['sort_orders'])) {
				foreach ($this->request->data['sort_orders'] as $key => $val) {
					$this->WikiPage->updateAll(array("WikiPage.sort_order" => $key + 1), array("WikiPage.id" => $val[$key + 1]));
				}
			}

			$this->set(compact('project_id', 'user_id', 'wiki_id', 'wiki_page_id'));
		}
	}

	public function get_wiki_page_comment_by_user($project_id = null, $user_id = null, $wiki_id = null, $wiki_page_id = null) {
		$this->layout = 'ajax';
		$this->autoRender = false;
		if (isset($this->data['user_id']) && !empty($this->data['user_id'])) {
			$conditions[] = ["WikiPageComment.user_id" => $this->data['user_id']];
		}
		if (isset($wiki_page_id) && !empty($wiki_page_id)) {
			$conditions[] = ["WikiPageComment.wiki_page_id" => $wiki_page_id];
		}
		if (isset($wiki_id) && !empty($wiki_id)) {
			$conditions[] = ["WikiPageComment.wiki_id" => $wiki_id];
		}
		$wikipagecomments = $this->WikiPageComment->find("all", array("conditions" => $conditions, "order" => "WikiPageComment.updated DESC"));

		$this->set(compact('project_id', 'user_id', 'wiki_id', 'wiki_page_id', 'wikipagecomments'));
		$this->render("partials/wiki_comment/get_wiki_page_comment_by_user");
	}

	public function wiki_page_comments_doc_uploads($project_id = null, $user_id = null, $wiki_id = null, $wiki_page_id = null) {
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			$response = ['success' => false, 'content' => null, 'msg' => null];

			if (isset($this->data['WikiPageCommentDocument']['document_name']) && !empty($this->data['WikiPageCommentDocument']['document_name'])) {
				$getfiles = $this->data['WikiPageCommentDocument']['document_name'];
				$savearray = array();
				if (isset($getfiles) && !empty($getfiles)) {
					$response['success'] = true;
					foreach ($getfiles as $k => $value) {
						$output_dir = WIKI_PAGE_DOCUMENT;
						if (isset($value['name']) && !empty($value['name'])) {
							$ext = pathinfo($value['name']);
							$fileName = $this->unique_file_name($output_dir, $value['name']);
							if (move_uploaded_file($value["tmp_name"], $output_dir . $fileName)) {
								$response['content'][] = $fileName;
							} else if (copy($value["tmp_name"], $output_dir . $fileName)) {
								$response['content'][] = $fileName;
							}
						}
					}
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
			$newname = $name . '_(' . $counter . ')' . $ext;
			$newpath = $path . '/' . $newname;
			$counter++;
		}
		return $newname;
	}

	public function wiki_page_comment_document_delete($project_id = null, $user_id = null, $wiki_id = null, $wiki_page_id = null) {
		$this->layout = 'ajax';
		$this->autoRender = false;
		if ($this->request->isAjax()) {
			$response = [
				'success' => false,
				'msg' => 'Sorry accor some error.',
				'content' => null,
			];
			if (isset($this->request->data['id']) && !empty($this->request->data['id']) && $this->request->data['id'] != 'null') {
				$id = $this->request->data['id'];
				$fielname = $this->request->data['file_name'];
				if (isset($fielname) && !empty($fielname)) {
					$conditions = array("WikiPageCommentDocument.document_name" => $fielname);
				}
				if (isset($id) && !empty($id)) {
					$conditions['AND'][] = array("WikiPageCommentDocument.id" => $id);
				}
				$old = $this->WikiPageCommentDocument->find("first", array("conditions" => $conditions));
				if (isset($old) && !empty($old)) {
					$this->request->data['WikiPageCommentDocument']['id'] = $old['WikiPageCommentDocument']['id'];
					$filePath = WIKI_PAGE_DOCUMENT . $old['WikiPageCommentDocument']['document_name'];
					if ($this->WikiPageCommentDocument->delete($this->request->data['WikiPageCommentDocument']['id'])) {
						if (file_exists($filePath)) {
							unlink($filePath);
						}
						$response = [
							'success' => true,
							'msg' => 'The comment attachment has been removed successfully.',
							'content' => null,
						];
						echo json_encode($response);
						exit();
					} else {
						echo json_encode($response);
						exit();
					}
				} else {
					echo json_encode($response);
					exit();
				}
			} else {

				$filePath = WIKI_PAGE_DOCUMENT . $this->request->data['file_name'];
				if (file_exists($filePath)) {
					unlink($filePath);
				}
				$response = [
					'success' => true,
					'msg' => 'The comment attachment has been removed successfully.',
					'content' => null,
				];
				echo json_encode($response);
				exit();
			}
		}
	}

	public function wiki_public_document_delete($id = null) {
		$this->layout = 'ajax';
		$this->autoRender = false;
		if ($this->request->isAjax()) {
			$response = [
				'success' => false,
				'msg' => 'Sorry accor some error.',
				'content' => null,
			];
			if (isset($id) && !empty($id) && $id != 'null') {

				if (isset($id) && !empty($id)) {
					$conditions['AND'][] = array("WikiPageCommentDocument.id" => $id);
				}
				$old = $this->WikiPageCommentDocument->find("first", array("conditions" => $conditions));
				if (isset($old) && !empty($old)) {
					$this->request->data['WikiPageCommentDocument']['id'] = $old['WikiPageCommentDocument']['id'];
					$filePath = WIKI_PAGE_DOCUMENT . $old['WikiPageCommentDocument']['document_name'];
					if ($this->WikiPageCommentDocument->delete($this->request->data['WikiPageCommentDocument']['id'])) {
						if (file_exists($filePath)) {
							unlink($filePath);
						}
						$response = [
							'success' => true,
							'msg' => 'The comment attachment has been removed successfully.',
							'content' => null,
						];
						echo json_encode($response);
						exit();
					} else {
						echo json_encode($response);
						exit();
					}
				} else {
					echo json_encode($response);
					exit();
				}
			}
		}
	}

	public function wiki_page_comment_delete($project_id = null, $user_id = null, $wiki_id = null, $wiki_page_id = null, $comment_id = null) {
		if ($this->request->isAjax()) {
			$this->set(compact('project_id', 'user_id', 'wiki_id', 'wiki_page_id', 'comment_id'));
			$this->layout = 'ajax';
			$this->autoRender = false;
			$response = ['success' => false, 'msg' => "Invalid", 'content' => null];
			if ($this->request->is('post') || $this->request->is('put')) {
				$this->WikiPageComment->delete($comment_id);
				$response['success'] = true;
				$response['msg'] = 'The comment has been deleted successfully.';
				$response['content'] = null;
				echo json_encode($response);
				exit();
			}
		}
	}

	public function wiki_page_public_document_save($project_id = null, $user_id = null, $wiki_id = null) {
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			$response = ['success' => false, 'content' => null, 'msg' => null];
			$this->set(compact('project_id', 'user_id', 'wiki_id'));

			if (isset($this->data['WikiPageCommentDocument']['document_name']) && !empty($this->data['WikiPageCommentDocument']['document_name'])) {
				$getfiles = $this->data['WikiPageCommentDocument']['document_name'];

				$savearray = array();
				if (isset($getfiles) && !empty($getfiles)) {
					$response['success'] = true;
					foreach ($getfiles as $k => $value) {
						$output_dir = WIKI_PAGE_DOCUMENT;
						if (isset($value['name']) && !empty($value['name'])) {
							$ext = pathinfo($value['name']);
							$fileName = $this->unique_file_name($output_dir, $value['name']);
							if (move_uploaded_file($value["tmp_name"], $output_dir . $fileName)) {
								$response['content'][] = $fileName;
							} else if (copy($value["tmp_name"], $output_dir . $fileName)) {
								$response['content'][] = $fileName;
							}

							$this->request->data['WikiPageCommentDocument']['document_name'] = "$fileName";
							$this->request->data['WikiPageCommentDocument']['created'] = date("Y-m-d h:i:s");
							$this->request->data['WikiPageCommentDocument']['updated'] = date("Y-m-d h:i:s");
							$this->WikiPageCommentDocument->create();
							$this->WikiPageCommentDocument->save($this->data);
						}
					}
				}
			}

			$document_count = $this->WikiPageCommentDocument->find('count', ['conditions' => ['WikiPageCommentDocument.wiki_id' => $this->request->data['WikiPageCommentDocument']['wiki_id']]]);
			$response['document_count'] = $document_count;
			echo json_encode($response);
			exit;
		}
	}

	public function get_wiki_document_by_user($project_id = null, $user_id = null, $wiki_id = null) {
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			$this->layout = 'ajax';
			$this->autoRender = false;
			$conditions[] = ["WikiPageCommentDocument.wiki_id" => $wiki_id];
			$conditions[] = ["WikiPageCommentDocument.user_id" => $user_id];

			$wikipagedocuments = $this->WikiPageCommentDocument->find("all", array(
				"conditions" => $conditions, "order" => "WikiPageCommentDocument.id DESC"));

			$this->set(compact('project_id', 'user_id', 'wiki_id', 'wikipagedocuments'));
			$this->render("partials/wiki_document/get_wiki_document_by_user");
		}
	}

	public function get_wiki_page_document_by_user_admin($project_id = null, $user_id = null, $wiki_id = null, $wiki_page_id = null) {
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			$this->layout = 'ajax';
			$this->autoRender = false;
			$conditions[] = ["WikiPageCommentDocument.wiki_id" => $wiki_id];
			$conditions[] = ["WikiPageCommentDocument.user_id" => $user_id];
			$conditions[] = ["WikiPageCommentDocument.wiki_page_id" => $wiki_page_id];

			$wikipagedocuments = $this->WikiPageCommentDocument->find("all", array(
				"conditions" => $conditions, "order" => "WikiPageCommentDocument.id DESC"));

			$this->set(compact('project_id', 'user_id', 'wiki_id', 'wikipagedocuments'));
			$this->render("partials/wiki_admin/get_wiki_page_document");
		}
	}

	public function get_wiki_page_comment_by_user_admin($project_id = null, $user_id = null, $wiki_id = null, $wiki_page_id = null) {
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			$this->layout = 'ajax';
			$this->autoRender = false;
			$conditions[] = ["WikiPageComment.wiki_id" => $wiki_id];
			$conditions[] = ["WikiPageComment.user_id" => $user_id];
			$conditions[] = ["WikiPageComment.wiki_page_id" => $wiki_page_id];

			$wikipagecomments = $this->WikiPageComment->find("all", array(
				"conditions" => $conditions, "order" => "WikiPageComment.updated DESC"));

			$this->set(compact('project_id', 'user_id', 'wiki_id', 'wikipagecomments'));
			$this->render("partials/wiki_admin/get_wiki_page_comment");
		}
	}

	public function set_rating($rate = null, $id = null) {

		$this->layout = 'ajax';
		$this->render = false;
		if (isset($this->request->data['id'])) {

			$this->request->data['WikiPageComment']['id'] = $this->request->data['id'];
			$this->request->data['WikiPageComment']['rating'] = $this->request->data['value'];
			$this->request->data['WikiPageComment']['rating_by'] = $this->Session->read('Auth.User.id');

			$this->WikiPageComment->save($this->request->data);
		}
		exit;
	}

}
