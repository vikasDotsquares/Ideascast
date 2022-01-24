<?php

App::uses('Controller/Component', 'Auth', 'Session', 'RequestHandler');
App::uses('Sanitize', 'Utility');
App::uses('CakeEmail', 'Network/Email');
App::import('Vendor', 'MPDF56/PhpWord');

class SktsController extends AppController {

	public $name = 'Skts';
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
		'ProjectSketch',
		'ProjectSketchParticipant',
		'ProjectSketchInterest',
	];
	public $user_id = null;
	public $pagination = null;
	public $components = array('Mpdf', 'Paginator', 'Common', 'CommonEmail', 'Group');

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
	public $helpers = array('Html', 'Form', 'Sketch', 'Session', 'Time', 'Text', 'Common', 'ViewModel', 'Mpdf', 'Scratch', 'Js' => array('Jquery'));

	public function beforeFilter() {
		parent::beforeFilter();
		$this->set('controller', 'skts');
		$this->user_id = $this->Auth->user('id');
		$this->Auth->allow(array("crons", "cron_logout"));
	}

	public function cron_logout() {
		$this->layout = false;
		$this->autoRender = false;
		$response = array('success' => false, 'time_diffrent' => '', 'logout_time' => null, 'current_time' => time());

		$idleMaxTime = $logoutTime = null;

		if (isset($_COOKIE['LOGOUT-TIME-' . $this->Auth->user('id')]) && !empty($_COOKIE['LOGOUT-TIME-' . $this->Auth->user('id')])) {
			$idleMaxTime = $_COOKIE['LOGOUT-TIME-' . $this->Auth->user('id')];
			$response['logout_time'] = $idleMaxTime;
		}
		if (isset($idleMaxTime) && $idleMaxTime != null) {

			$logoutTime = $idleMaxTime - time();
			$response['time_diffrent'] = $logoutTime;

			if ($logoutTime < 10) {
				$response['success'] = true;
				echo json_encode($response);
			} else {
				echo json_encode($response);
			}
		} else {
			echo json_encode($response);
		}

	}

	public function sketch_pdf() {
		Configure::write('debug', 0);
		$this->layout = false;
		$this->autoRender = false;

		$project_id = (isset($this->params['named']['project_id']) && !empty($this->params['named']['project_id'])) ? $this->params['named']['project_id'] : null;
		$sketch_id = (isset($this->params['named']['sketch_id']) && !empty($this->params['named']['sketch_id'])) ? $this->params['named']['sketch_id'] : null;

		$dir = $this->get_sketch_path($project_id, $sketch_id);		 
		$filename = "data.json";
		$filepath = $dir . '/' . $filename;
		if (file_exists($filepath)) {
			$json = file_get_contents($filepath);
			$data = json_decode($json, true);
		} else {
			$data = array();
		} 
		
		$sketch = $this->ProjectSketch->findById($sketch_id);
		
		$sketch_title = !empty($sketch['ProjectSketch']['sketch_title']) ? $sketch['ProjectSketch']['sketch_title'] : '';
		$sketch_description = !empty($sketch['ProjectSketch']['sketch_description']) ? $sketch['ProjectSketch']['sketch_description'] : '';
		$content = !empty($data['content']) ? $data['content'] : '';
		$canvas_data = !empty($data['canvas_data']) ? $data['canvas_data'] : '';
		$images_data = !empty($data['images_data']) ? $data['images_data'] : '';
		$config = !empty($data['config']) ? $data['config'] : '';

		App::import('Vendor', 'sketchmpdf', array('file' => 'sketch-mpdf/mpdf.php'));

		//$file = $sketch_id . '-' . time() . '-sketch.pdf';
		$file = $sketch['ProjectSketch']['sketch_title'] . '.pdf';
		$path = WWW_ROOT . "uploads/sketch_document/" . $file;

		$stylesheet = file_get_contents(WWW_ROOT . 'css/projects/sketch.css'); // Get css content

		// Setup PDF
		$mpdf = new mPDF('utf-8', 'A4-L'); // New PDF object with encoding & page size
		//$mpdf=new mPDF('c','','','','6','6','7','7');

		$mpdf->setAutoTopMargin = 'stretch'; // Set pdf top margin to stretch to avoid content overlapping
		$mpdf->setAutoBottomMargin = 'stretch'; // Set pdf bottom margin to stretch to avoid content overlapping

		$html = '<div class="pdf-header_">
                    <a class="logo"  >
                        <img src="' . SITEURL . 'images/logo_black.png" style="background-color: transparent !important;">
                    </a>
                    <hr style="height:1px; color:#000;width:100%;">
                </div>';

		$project_D = getByDbId('Project', $project_id, array('title'));
		$project_title = strip_tags($project_D['Project']['title']);
		$html .= '  <table style="margin-top:20%; width:100%">
                        <tr>
                            <th><h2>Project: ' . ucfirst($project_title) . '</h2></th>
                        </tr>
                        <tr>
                            <th></th>
                        </tr>
                        <tr>
                            <th><h3>Sketch: ' . ucfirst($sketch_description) . '</h3></th>
                        </tr>
                        <tr>
                            <th><h5>By: ' . ucfirst($this->Common->userFullname($this->user_id)) . '</h5></th>
                        </tr>
                        <tr>
                            <th><h5>' . _displayDate(date("Y-m-d H:i"), 'd M, Y h:i A') . '</h5></th>
                        </tr>
                    </table>
                    <div style="page-break-before: always;"></div>
                    ';

		$html .= $content;

		// PDF footer content

		$mpdf->SetHTMLFooter('
                <hr style="height:1px; color:#000;width:100%;">
                <div class="pdf-footer" style="">
                    <table style="width:100%;">
                        <tr>
                            <td style="width:40%;">
                                <h3><a  >IdeasCast</a></h3>
                            </td>
                            <td style="width:30%;text-align:center;font-size:10px;">{PAGENO}/{nbpg}</td>
                            <td style="width:30%;text-align:right;font-size:10px;">' . _displayDate(date("Y-m-d H:i:s"), 'd M, Y h:i A') . '</td>
                        </tr>
                    </table>
                   <!-- <table style="width:100%;">
                        <tr>
                            <td style="width:40%;font-size:10px;">
                                <p>FOR MORE</p>
                                <p>INFORMATION</p>
                                <p>PLEASE CONTACT</p>
                                <p>E: info@Ideascast.com</p>
                                <p>T: +44 (0)2476 158 430</p>
                            </td>
                            <td style="width:30%;text-align:center;"></td>
                            <td style="width:30%;text-align:right;"></td>
                        </tr>
                    </table>-->

                </div>');

		$mpdf->WriteHTML($stylesheet, 1); // Writing style to pdf
		$mpdf->WriteHTML($html); // Writing html to pdf

		$mpdf->Output($file, 'D'); // For Download
		exit;

		$this->redirect(array("controller" => "skts", "action" => "edit_sketch", "project_id" => $project_id, "sketch_id" => $sketch_id));

	}

	public function index() {
		$viewVars = $data = null;
		$this->layout = 'inner';
		$this->set('title_for_layout', 'Sketches');

		$project_id = (isset($this->params['named']['project_id']) && !empty($this->params['named']['project_id'])) ? $this->params['named']['project_id'] : null;
		$viewVars['project_id'] = $project_id;

		$this->Project->id = $project_id;
		if (isset($project_id) && !empty($project_id)) {
			if (!$this->Project->exists()) {
				$this->Session->setFlash(__('Invalid Project.'), 'error');
				$this->redirect(array("controller" => "skts", "action" => "index"));
			}
		}

		$project = $this->Project->findById($project_id);

		$sketch_ids = $this->ProjectSketchParticipant->find("all", array(
			"conditions" => array('ProjectSketchParticipant.user_id' => $this->user_id),
			"order" => "ProjectSketchParticipant.created ASC",
		)
		);

		if (isset($sketch_ids) && !empty($sketch_ids)) {
			$sketch_all_id = Hash::extract($sketch_ids, '{n}.ProjectSketchParticipant.project_sketch_id');
		} else {
			$sketch_all_id = array();
		}

		$sketch_ids_save_as = $this->ProjectSketch->find("all", array(
			"conditions" => array('ProjectSketch.user_id' => $this->user_id, "ProjectSketch.parent_id !=" => 0),
		)
		);

		if (isset($sketch_ids_save_as) && !empty($sketch_ids_save_as)) {

			if (isset($sketch_ids) && !empty($sketch_ids)) {
				$sketch_all_id_my = Hash::extract($sketch_ids_save_as, '{n}.ProjectSketch.parent_id');
			} else {
				$sketch_all_id_my = array();
			}

		}

		$this->Paginator->settings = array(
			'conditions' => array("ProjectSketch.id" => $sketch_all_id, "ProjectSketch.parent_id" => 0, "ProjectSketch.status" => 1, "ProjectSketch.project_id" => $project_id),
			'limit' => 8,
		);

		$sketchdata = $this->Paginator->paginate('ProjectSketch');

		if ((isset($sketch_all_id_my) && !empty($sketch_all_id_my)) && (isset($sketch_ids) && !empty($sketch_ids))) {

			$sketch_all_id = array_merge($sketch_all_id, $sketch_all_id_my);
			$sketch_all_id = array_unique($sketch_all_id);
		}

		$sketch_save_as_data = $this->ProjectSketch->find("all", array("conditions" => array(
			"ProjectSketch.id" => $sketch_all_id, "ProjectSketch.status" => 1,
			"ProjectSketch.project_id" => $project_id,
			"ProjectSketch.parent_id" => 0,
		),
		)
		);

		App::import('Controller', 'Users');
		$Users = new UsersController;
		$projects = null;
		$myprojectlist = $myreceivedprojectlist = $mygroupprojectlist = array();
		// Find All current user's projects
		$myprojectlist = $Users->__myproject_selectbox($this->user_id);
		// Find All current user's received projects
		$myreceivedprojectlist = $Users->__receivedproject_selectbox($this->user_id);
		// Find All current user's group projects
		$mygroupprojectlist = $Users->__groupproject_selectbox($this->user_id);

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

		$projects = array_map("strip_tags", $projects1);
		$projects = array_map("trim", $projects);
		natcasesort($projects);

		//$projects = get_my_projects($this->user_id);
		$this->set('projects', $projects);
		//pr($projects,1);
		if ($project_id != '' && !array_key_exists($project_id, $projects)) {
			$this->Session->setFlash(__('Invalid Project Id.'), 'error');
			$this->redirect(array("controller" => "skts", "action" => "index"));
		}

		$crumb = ['last' => ['data' => ['title' => 'Sketches', 'data-original-title' => 'Sketches']]];

		$this->set(compact('data', 'crumb', 'viewVars', 'project', 'project_id', 'sketch_save_as_data', 'sketchdata'));
		$this->setJsVar("viewVars", $viewVars);
	}

	public function add() {
		$viewVars = $data = null;
		$this->layout = 'inner';
		$this->set('title_for_layout', 'Sketch : Add Properties');

		$project_id = (isset($this->params['named']['project_id']) && !empty($this->params['named']['project_id'])) ? $this->params['named']['project_id'] : null;
		$viewVars['project_id'] = $project_id;

		if ((empty($project_id) || !is_numeric($project_id) || $project_id == null)) {
			$this->Session->setFlash(__('Invalid Project Id.'), 'error');
			$this->redirect(array("controller" => "projects", "action" => "lists"));
		}

		$this->Project->id = $project_id;
		if (!$this->Project->exists()) {
			$this->Session->setFlash(__('Invalid Project.'), 'error');
			$this->redirect(array("controller" => "projects", "action" => "lists"));
		}

		$project = $this->Project->findById($project_id);
		$sketchlist = $this->ProjectSketch->find("list", array("fields" => array("id", "sketch_title"), "conditions" => array("ProjectSketch.status" => 1, "ProjectSketch.project_id" => $project_id)));

		App::import('Controller', 'Users');
		$Users = new UsersController;
		$projects = null;
		$myprojectlist = $myreceivedprojectlist = $mygroupprojectlist = array();
		// Find All current user's projects
		$myprojectlist = $Users->__myproject_selectbox($this->user_id);
		// Find All current user's received projects
		$myreceivedprojectlist = $Users->__receivedproject_selectbox($this->user_id);
		// Find All current user's group projects
		$mygroupprojectlist = $Users->__groupproject_selectbox($this->user_id);

		// ## get project all user

		if (isset($project_id) && !empty($project_id)) {
			$owner = $this->Common->ProjectOwner($project_id, $this->Session->read('Auth.User.id'));
			$participants = participants($project_id, $owner['UserProject']['user_id']);
			$participants_owners = participants_owners($project_id, $owner['UserProject']['user_id']);
			$participantsGpOwner = participants_group_owner($project_id);
			$participantsGpSharer = participants_group_sharer($project_id);

			$participants = isset($participants) ? array_filter($participants) : $participants;
			$participants_owners = isset($participants_owners) ? array_filter($participants_owners) : $participants_owners;
			$participantsGpOwner = isset($participantsGpOwner) ? array_filter($participantsGpOwner) : $participantsGpOwner;
			$participantsGpSharer = isset($participantsGpSharer) ? array_filter($participantsGpSharer) : $participantsGpSharer;
		}
		$user_arrays = [];
		if (!empty($participants)) {
			$user_arrays = array_merge($user_arrays, $participants);
		}
		if (!empty($participants_owners)) {
			$user_arrays = array_merge($user_arrays, $participants_owners);
		}
		if (!empty($participantsGpOwner)) {
			$user_arrays = array_merge($user_arrays, $participantsGpOwner);
		}
		if (!empty($participantsGpSharer)) {
			$user_arrays = array_merge($user_arrays, $participantsGpSharer);
		}
		$user_arrays = array_merge($user_arrays, array($this->user_id));
		$user_arrays = array_unique($user_arrays);
		$this->set(compact('participants', 'participants_owners', 'user_arrays', 'participantsGpOwner', 'participantsGpSharer'));

		// ## end

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

		$projects = array_map("strip_tags", $projects1);
		$projects = array_map("trim", $projects);
		natcasesort($projects);

		//$projects = get_my_projects($this->user_id);
		$this->set('projects', $projects);

		$data['title_for_layout'] = __('Sketch', true);
		$data['page_heading'] = __('Create Sketch', true);
		$data['page_subheading'] = __('Create new sketch', true);
		$crumb = ['last' => ['data' => ['title' => 'Sketch', 'data-original-title' => 'Sketch']]];

		if ($project_id != '' && !array_key_exists($project_id, $projects)) {
			$this->Session->setFlash(__('Invalid Project Id.'), 'error');
			$this->redirect(array("controller" => "skts", "action" => "index"));
		}
		if ($this->request->is('post') || $this->request->is('put')) {

			if ($this->request->data['ProjectSketch']['project_id'] == '') {
				$this->Session->setFlash(__('Please select project.'), 'error');
				$this->redirect(array("controller" => "skts", "action" => "add", 'project_id' => $project_id));
			}
			$title = trim($this->request->data['ProjectSketch']['sketch_title']);
			if ($title == '') {
				$this->Session->setFlash(__('Please provide sketch title.'), 'error');
				//$this->redirect(array("controller" => "skts", "action" => "add", 'project_id' => $project_id));
			}

			$users = isset($this->request->data['ProjectSketchParticipant']) ? $this->request->data['ProjectSketchParticipant'] : null;
			unset($this->request->data['ProjectSketch']['user_id']);
			$this->request->data['ProjectSketch']['user_id'] = $this->Session->read("Auth.User.id");
			$this->request->data['ProjectSketch']['locknoedits'] = (isset($this->request->data['ProjectSketch']['locknoedits']) && !empty($this->request->data['ProjectSketch']['locknoedits'])) ? 1 : 0;

			if (isset($this->request->data['ProjectSketch']['project_id']) && $this->request->data['ProjectSketch']['project_id'] == '') {
				$this->Session->setFlash(__('Could not be updated sketch properties due to an error with project id.'), 'error');
				$this->redirect(array("action" => "add", "project_id" => $project_id));
			}
			$this->ProjectSketch->set($this->data);
			if ($this->ProjectSketch->validates()) {
				foreach ($users as $k => $val) {
					if ($val['user_id'] == $this->user_id) {
						$this->request->data['ProjectSketchParticipant'][$k]['created_user_id'] = $this->user_id;
					}
					$this->request->data['ProjectSketchParticipant'][$k]['user_id'] = $val['user_id'];
					$this->request->data['ProjectSketchParticipant'][$k]['project_id'] = $project_id;
					$this->request->data['ProjectSketchParticipant'][$k]['send_email'] = 1;
				}

				if ($this->ProjectSketch->saveAssociated($this->request->data)) {
					$sketch_id = $this->ProjectSketch->getLastInsertId();

					$sketch_data = $this->ProjectSketch->findById($sketch_id);
					foreach ($users as $k => $val) {
						if ($val['user_id'] != $this->user_id) {
							if ($this->Common->check_email_permission($val['user_id'], 'sketch_sharing') == true) {
								$user_data = $this->User->findById($val['user_id']);
								$sendMail = $this->CommonEmail->sendEmailAddSketchParticipant($user_data, $sketch_data);
							}
						}
					}

					/************** socket messages **************/
					if (SOCKET_MESSAGES) {
						$current_user_id = $this->Session->read('Auth.User.id');
						$sketch_users = $users;
						if (isset($sketch_users) && !empty($sketch_users)) {
							$sketch_users = Set::extract($sketch_users, '/user_id');
						}
						if (isset($sketch_users) && !empty($sketch_users)) {
							if (($key = array_search($current_user_id, $sketch_users)) !== false) {
								unset($sketch_users[$key]);
							}
						}
						$open_users = null;
						if (isset($sketch_users) && !empty($sketch_users)) {
							foreach ($sketch_users as $key1 => $value1) {
								// e($value1);
								if (web_notify_setting($value1, 'sketches', 'sketch_sharing')) {
									$open_users[] = $value1;
								}
							}
						}
						$userDetail = get_user_data($current_user_id);
						$content = [
							'notification' => [
								'created_id' => $current_user_id,
								'project_id' => $project_id,
								'creator_name' => $userDetail['UserDetail']['full_name'],
								'subject' => 'Sketch sharing request',
								'heading' => 'Sketch: ' . strip_tags(getFieldDetail('ProjectSketch', $sketch_id, 'sketch_title')),
								'sub_heading' => 'Project: ' . strip_tags(getFieldDetail('Project', $project_id, 'title')),
								'date_time' => timezoneTimeConvertor($this->user_id, date('Y-m-d g:iA'), $format = 'd M Y g:iA') . user_country($this->user_id),
							],
						];
						if (is_array($open_users)) {
							$content['received_users'] = array_values($open_users);
						}

						$request = array(
							'header' => array(
								'Content-Type' => 'application/json',
							),
						);
						$content = json_encode($content);
						$HttpSocket = new HttpSocket([
							'ssl_verify_host' => false,
							'ssl_verify_peer_name' => false,
							'ssl_verify_peer' => false,
						]);

						$results = $HttpSocket->post(CHATURL . '/serveremit', $content, $request);
					}
					/************** socket messages **************/

					$this->Session->setFlash(__('You have successfully added a new sketch.'), 'success');
					$this->redirect(array("action" => "edit_sketch", "project_id" => $project_id, "sketch_id" => $sketch_id));
				} else {
					$this->Session->setFlash(__('Could not be updated sketch properties due to an error.'), 'error');
					$this->redirect(array("action" => "add", "project_id" => $project_id));
				}
			}
			//pr($this->request->data,1);
		}

		$this->set(compact('data', 'sketchlist', 'crumb', 'viewVars', 'project', 'project_id'));
		$this->setJsVar("viewVars", $viewVars);
	}

	public function edit() {

		$viewVars = $data = null;
		$this->layout = 'inner';
		$this->set('title_for_layout', 'Sketch : Edit Properties');

		$project_id = (isset($this->params['named']['project_id']) && !empty($this->params['named']['project_id'])) ? $this->params['named']['project_id'] : null;
		$sketch_id = (isset($this->params['named']['sketch_id']) && !empty($this->params['named']['sketch_id'])) ? $this->params['named']['sketch_id'] : null;

		$sketchlist = $this->ProjectSketch->find("list", array("fields" => array("id", "sketch_title"), "conditions" => array("ProjectSketch.status" => 1, "ProjectSketch.project_id" => $project_id)));
		$project = $this->Project->findById($project_id);
		$sketchdata = $this->ProjectSketch->find("first", array("conditions" => array("ProjectSketch.status" => 1, "ProjectSketch.id" => $sketch_id)));
		$selecteduser = [];
		if (!empty($sketchdata)) {
			$selecteduser = $this->ProjectSketchParticipant->find("list", array("fields" => array("ProjectSketchParticipant.user_id"), "conditions" => array("ProjectSketchParticipant.project_sketch_id" => $sketchdata['ProjectSketch']['id'])));
		}
		
		// ## get project all user
		$owner = $this->Common->ProjectOwner($project_id, $this->Session->read('Auth.User.id'));
		$participants = participants($project_id, $owner['UserProject']['user_id']);
		$participants_owners = participants_owners($project_id, $owner['UserProject']['user_id']);
		$participantsGpOwner = participants_group_owner($project_id);
		$participantsGpSharer = participants_group_sharer($project_id);

		$participants = isset($participants) ? array_filter($participants) : $participants;
		$participants_owners = isset($participants_owners) ? array_filter($participants_owners) : $participants_owners;
		$participantsGpOwner = isset($participantsGpOwner) ? array_filter($participantsGpOwner) : $participantsGpOwner;
		$participantsGpSharer = isset($participantsGpSharer) ? array_filter($participantsGpSharer) : $participantsGpSharer;

		$user_arrays = [];
		if (!empty($participants)) {
			$user_arrays = array_merge($user_arrays, $participants);
		}
		if (!empty($participants_owners)) {
			$user_arrays = array_merge($user_arrays, $participants_owners);
		}
		if (!empty($participantsGpOwner)) {
			$user_arrays = array_merge($user_arrays, $participantsGpOwner);
		}
		if (!empty($participantsGpSharer)) {
			$user_arrays = array_merge($user_arrays, $participantsGpSharer);
		}
		$user_arrays = array_merge($user_arrays, array($this->user_id));
		$user_arrays = array_unique($user_arrays);

		$this->set(compact('user_arrays', 'participants', 'participants_owners', 'participantsGpOwner', 'participantsGpSharer', 'selecteduser'));

		if (isset($user_arrays) && !empty($user_arrays) && !in_array($this->user_id, $user_arrays)) {
			$this->Session->setFlash(__('This location is not authorized to access logedin user.'), 'error');
			$this->redirect(array("action" => "index", "project_id" => $project_id));
		}
		//pr($users);
		//pr($participants_owners,1);
		// ## end

		App::import('Controller', 'Users');
		$Users = new UsersController;
		$projects = null;
		$myprojectlist = $myreceivedprojectlist = $mygroupprojectlist = array();
		// Find All current user's projects
		$myprojectlist = $Users->__myproject_selectbox($this->user_id);
		// Find All current user's received projects
		$myreceivedprojectlist = $Users->__receivedproject_selectbox($this->user_id);
		// Find All current user's group projects
		$mygroupprojectlist = $Users->__groupproject_selectbox($this->user_id);

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

		$projects = array_map("strip_tags", $projects1);
		$projects = array_map("trim", $projects);
		natcasesort($projects);

		//$projects = get_my_projects($this->user_id);
		$this->set('projects', $projects);

		$viewVars['project_id'] = $project_id;
		$viewVars['sketch_id'] = $sketch_id;
		$this->set(compact("project_id", "sketch_id"));
		//pr($project,1);

		$this->Project->id = $project_id;

		if (!$this->Project->exists()) {
			$this->Session->setFlash(__('Invalid Project.'), 'error');
			$this->redirect(array("action" => "index", "project_id" => $project_id));
		}
		$this->ProjectSketch->id = $sketch_id;
		if (!$this->ProjectSketch->exists()) {
			$this->Session->setFlash(__('Invalid Sketch.'), 'error');
			$this->redirect(array("action" => "index", "project_id" => $project_id));
		}
		//pr($sketchdata,1);
		if (empty($sketchdata)) {
			$this->Session->setFlash(__('Invalid Project And Sketch Id.'), 'error');
			$this->redirect(array("action" => "index", "project_id" => $project_id));
		}
		if (isset($sketchdata) && $sketchdata['ProjectSketch']['locked'] == 1 && $sketchdata['ProjectSketch']['locked_user_id'] != $this->user_id) {

			$this->Session->setFlash(__('Sketch is locked ,Please wait for a moment.'), 'error');
			$this->redirect(array("action" => "index", "project_id" => $project_id));
		}

		if (isset($sketchdata) && $sketchdata['ProjectSketch']['is_edit_mode'] == 1 && $sketchdata['ProjectSketch']['edit_user_id'] != $this->user_id) {

			$this->Session->setFlash(__('Sketch is already editing ,Please wait for a moment.'), 'error');
			$this->redirect(array("action" => "index", "project_id" => $project_id));
		}
		/* if (isset($sketchdata) && $sketchdata['ProjectSketch']['is_edit_mode'] == 1 && $sketchdata['ProjectSketch']['edit_user_id'] != $this->user_id) {

			          $this->Session->setFlash(__('Sketch is already in editing mode,Please wait for a moment.'), 'error');
			          $this->redirect(array("action" => "index", "project_id" => $project_id));
		*/

		if ((empty($project_id) || !is_numeric($project_id) || $project_id == null)) {
			$this->Session->setFlash(__('Invalid Project Id.'), 'error');
			$this->redirect(array("action" => "index", "project_id" => $project_id));
		}

		if (empty($sketch_id) || !is_numeric($sketch_id) || $sketch_id == null) {
			$this->Session->setFlash(__('Invalid Sketch Id.'), 'error');
			$this->redirect(array("action" => "index", "project_id" => $project_id));
		}
		if ($project_id != '' && !array_key_exists($project_id, $projects)) {
			$this->Session->setFlash(__('Invalid Project Id.'), 'error');
			$this->redirect(array("controller" => "skts", "action" => "index"));
		}
		/*         * **************************** */
		$users = $this->ProjectSketchInterest->find("all", array(
			"conditions" => array("ProjectSketchInterest.sketch_id" => $sketch_id),
			"order" => "ProjectSketchInterest.created ASC",
		)
		);
		if (isset($users) && !empty($users)) {
			$user_id = Hash::extract($users, '{n}.ProjectSketchInterest.user_id');
		} else {
			$user_id = array();
		}

		$participant_users = $this->ProjectSketchParticipant->find("all", array("conditions" => array("ProjectSketchParticipant.project_sketch_id" => $sketch_id))
		);
		if (isset($participant_users) && !empty($participant_users)) {
			$participant_user_id = Hash::extract($participant_users, '{n}.ProjectSketchParticipant.user_id');
		} else {
			$participant_user_id = array();
		}

		if (
			isset($user_id) &&
			(!in_array($this->user_id, $user_id) && !empty($user_id)) ||
			(!in_array($this->user_id, $user_id) && !empty($user_id))
		) {
			if (!in_array($this->user_id, $participant_user_id)) {

				$this->Session->setFlash(__('This location is not authorized to access so please sent your interest.'), 'error');
				$this->redirect(array("action" => "index", "project_id" => $project_id));
			}
		}
		/*         * **************************** */
		if ($this->request->is('post') || $this->request->is('put')) {

			$title = trim($this->request->data['ProjectSketch']['sketch_title']);
			if ($title == '') {
				$this->Session->setFlash(__('Please provide sketch title.'), 'error');
				//$this->redirect(array("controller" => "skts", "action" => "edit", 'project_id' => $project_id, "sketch_id" => $sketch_id));
			}
			if ($this->request->data['ProjectSketch']['sketch_title'] == '') {
				$this->Session->setFlash(__('Please provide sketch title.'), 'error');
				$this->redirect(array("controller" => "skts", "action" => "add", 'project_id' => $project_id));
			}

			$users = isset($this->request->data['ProjectSketchParticipant']) ? $this->request->data['ProjectSketchParticipant'] : null;
			//pr($this->request->data);
			//pr($users,1);
			if (isset($this->request->data['ProjectSketch']['project_id']) && $this->request->data['ProjectSketch']['project_id'] == '') {
				$this->Session->setFlash(__('Could not be updated sketch properties due to an error with project id.'), 'error');
				$this->redirect(array("action" => "edit", "project_id" => $project_id, "sketch_id" => $sketch_id));
			}

			if (isset($this->request->data['ProjectSketch']['locked']) && $this->request->data['ProjectSketch']['locked'] == 1) {
				$this->request->data['ProjectSketch']['locked_user_id'] = $this->Session->read("Auth.User.id");
			}
			// pr($users,1);
			if (!isset($this->request->data['ProjectSketch']['locked']) && $this->request->data['ProjectSketch']['locked_user_id'] == $this->Session->read("Auth.User.id")) {
				$this->request->data['ProjectSketch']['locked'] = 0;
				$this->request->data['ProjectSketch']['locked_user_id'] = $this->Session->read("Auth.User.id");
			}
			if (!isset($this->request->data['ProjectSketch']['locked']) && !empty($this->request->data['ProjectSketch']['locked_user_id']) && $this->request->data['ProjectSketch']['locked_user_id'] != $this->Session->read("Auth.User.id")) {
				$this->Session->setFlash(__('Could not be updated sketch lock by another user.'), 'error');
				$this->redirect(array("action" => "edit", "project_id" => $project_id, "sketch_id" => $sketch_id));
			}
			$this->request->data['ProjectSketch']['participant_all'] = isset($this->request->data['ProjectSketch']['participant_all']) ? 1 : 0;

			$this->request->data['ProjectSketch']['modified'] = date("Y-m-d H:i:s");
			$this->request->data['ProjectSketch']['updated_by'] = $this->user_id;
			$date = date('Y-m-d H:i:s');

			$this->ProjectSketch->set($this->data);
			if ($this->ProjectSketch->validates()) {
				unset($this->request->data['ProjectSketchParticipant']);

				$data = $this->ProjectSketchParticipant->find("all", array("conditions" => array("ProjectSketchParticipant.project_sketch_id" => $sketch_id, "ProjectSketchParticipant.project_id" => $project_id)));

				if (isset($data) && !empty($data)) {
					$oldids = Hash::extract($data, '{n}.ProjectSketchParticipant.user_id');
				} else {
					$ids = array();
				}

				$sketch_data = $this->ProjectSketch->findById($sketch_id);
				
				foreach ($users as $k => $val) {
					if ((isset($val['user_id']) && !empty($val['user_id'])) && !in_array($val['user_id'], $oldids)) {
						$this->request->data['ProjectSketchParticipant'][$k]['user_id'] = $val['user_id'];
						$this->request->data['ProjectSketchParticipant'][$k]['project_id'] = $project_id;
						$this->request->data['ProjectSketchParticipant'][$k]['project_sketch_id'] = $sketch_id;
						$this->request->data['ProjectSketchParticipant'][$k]['send_email'] = 1;

						if ($this->Common->check_email_permission($val['user_id'], 'sketch_sharing') == true) {
							$user_data = $this->User->findById($val['user_id']);
							$sendMail = $this->CommonEmail->sendEmailAddSketchParticipant($user_data, $sketch_data);

						}
						//pr($user_data,1);
					}
				}

				/************** socket messages **************/
				if (SOCKET_MESSAGES) {
					$current_user_id = $this->Session->read('Auth.User.id');
					$sketch_users = $users;
					if (isset($sketch_users) && !empty($sketch_users)) {
						$sketch_users = Set::extract($sketch_users, '/user_id');
					}
					if (isset($sketch_users) && !empty($sketch_users)) {
						if (($key = array_search($current_user_id, $sketch_users)) !== false) {
							unset($sketch_users[$key]);
						}
					}
					$open_users = null;
					if (isset($sketch_users) && !empty($sketch_users)) {
						foreach ($sketch_users as $key1 => $value1) {
							if (web_notify_setting($value1, 'sketches', 'sketch_sharing') && !in_array($val, $oldids)) {
								$open_users[] = $value1;
							}
						}
					}
					$userDetail = get_user_data($current_user_id);
					$content = [
						'notification' => [
							'created_id' => $current_user_id,
							'project_id' => $project_id,
							'creator_name' => $userDetail['UserDetail']['full_name'],
							'subject' => 'Sketch sharing request',
							'heading' => 'Sketch: ' . strip_tags(getFieldDetail('ProjectSketch', $sketch_id, 'sketch_title')),
							'sub_heading' => 'Project: ' . strip_tags(getFieldDetail('Project', $project_id, 'title')),
							'date_time' => timezoneTimeConvertor($this->user_id, date('Y-m-d g:iA'), $format = 'd M Y g:iA') . user_country($this->user_id),
						],
					];
					if (is_array($open_users)) {
						$content['received_users'] = array_values($open_users);
					}

					$request = array(
						'header' => array(
							'Content-Type' => 'application/json',
						),
					);
					$content = json_encode($content);
					$HttpSocket = new HttpSocket([
						'ssl_verify_host' => false,
						'ssl_verify_peer_name' => false,
						'ssl_verify_peer' => false,
					]);

					$results = $HttpSocket->post(CHATURL . '/serveremit', $content, $request);
				}
				/************** socket messages **************/

				if ($this->ProjectSketch->saveAssociated($this->request->data)) {

					$participant_user = $this->ProjectSketchParticipant->find("all", array("conditions" => array("ProjectSketchParticipant.project_sketch_id" => $sketch_id, "ProjectSketchParticipant.project_id" => $project_id)));
					$interest_user = $this->ProjectSketchInterest->find("all", array("conditions" => array("ProjectSketchInterest.sketch_id" => $sketch_id, "ProjectSketchInterest.project_id" => $project_id)));

					$parti_user_ids = Hash::extract($participant_user, '{n}.ProjectSketchParticipant.user_id');
					$interest_user_ids = Hash::extract($interest_user, '{n}.ProjectSketchInterest.user_id');
					if (isset($interest_user_ids) && !empty($interest_user_ids)) {
						foreach ($interest_user_ids as $value) {
							if (!empty($value) && !in_array($value, $parti_user_ids)) {
								$this->ProjectSketchInterest->deleteAll(array("ProjectSketchInterest.user_id" => $value));
							}
						}
					}
					//pr($interest_user_ids);
					//pr($parti_user_ids,1);
					$this->Session->setFlash(__('You have successfully updated sketch properties.'), 'success');
					$this->redirect(array("action" => "edit_sketch", "project_id" => $project_id, "sketch_id" => $sketch_id));
				} else {
					$this->Session->setFlash(__('Could not be updated sketch properties due to an error.'));
					$this->redirect(array("action" => "edit", "project_id" => $project_id, "sketch_id" => $sketch_id));
				}
			}
			//pr($this->request->data,1);
		}

		$this->request->data = $sketchdata;
		//$sketchdata['ProjectSketch']['user_id']
		$createdUserData = $this->User->findById($sketchdata['ProjectSketch']['user_id']);
		if( isset($createdUserData['UserDetail']['full_name']) && !empty($createdUserData['UserDetail']['full_name']) ){
			$data['page_subheading'] = __('Creator '.$createdUserData['UserDetail']['full_name'].': Sketch update', true);	
		} else {
			$data['page_subheading'] = __('Sketch update', true);
		}
		
		$data['title_for_layout'] = __('Sketch : Edit', true);
		$data['page_heading'] = __('Sketch update', true);
		
		$crumb = ['last' => ['data' => ['title' => 'Sketch update', 'data-original-title' => 'Sketch update']]];

		$this->set(compact('data', 'crumb', 'viewVars', 'project', 'sketch_id', 'project_id', 'sketchdata', 'sketchlist'));
		$this->setJsVar("viewVars", $viewVars);
	}

	public function saveas() {

		$viewVars = $data = null;
		$this->layout = 'inner';
		$this->set('title_for_layout', 'Sketch : Edit Properties');

		$project_id = (isset($this->params['named']['project_id']) && !empty($this->params['named']['project_id'])) ? $this->params['named']['project_id'] : null;
		$sketch_id = (isset($this->params['named']['sketch_id']) && !empty($this->params['named']['sketch_id'])) ? $this->params['named']['sketch_id'] : null;

		$sketchlist = $this->ProjectSketch->find("list", array("fields" => array("id", "sketch_title"), "conditions" => array("ProjectSketch.status" => 1, "ProjectSketch.project_id" => $project_id)));
		$project = $this->Project->findById($project_id);
		$sketchdata = $this->ProjectSketch->find("first", array("conditions" => array("ProjectSketch.status" => 1, "ProjectSketch.id" => $sketch_id)));
		$selecteduser = [];
		if (!empty($sketchdata)) {
			$selecteduser = $this->ProjectSketchParticipant->find("list", array("fields" => array("ProjectSketchParticipant.user_id"), "conditions" => array("ProjectSketchParticipant.project_sketch_id" => $sketchdata['ProjectSketch']['id'])));
		}

		// ## get project all user
		$owner = $this->Common->ProjectOwner($project_id, $this->Session->read('Auth.User.id'));
		$participants = participants($project_id, $owner['UserProject']['user_id']);
		$participants_owners = participants_owners($project_id, $owner['UserProject']['user_id']);
		$participantsGpOwner = participants_group_owner($project_id);
		$participantsGpSharer = participants_group_sharer($project_id);

		$participants = isset($participants) ? array_filter($participants) : $participants;
		$participants_owners = isset($participants_owners) ? array_filter($participants_owners) : $participants_owners;
		$participantsGpOwner = isset($participantsGpOwner) ? array_filter($participantsGpOwner) : $participantsGpOwner;
		$participantsGpSharer = isset($participantsGpSharer) ? array_filter($participantsGpSharer) : $participantsGpSharer;

		$user_arrays = [];
		if (!empty($participants)) {
			$user_arrays = array_merge($user_arrays, $participants);
		}
		if (!empty($participants_owners)) {
			$user_arrays = array_merge($user_arrays, $participants_owners);
		}
		if (!empty($participantsGpOwner)) {
			$user_arrays = array_merge($user_arrays, $participantsGpOwner);
		}
		if (!empty($participantsGpSharer)) {
			$user_arrays = array_merge($user_arrays, $participantsGpSharer);
		}
		$user_arrays = array_merge($user_arrays, array($this->user_id));
		$user_arrays = array_unique($user_arrays);

		$this->set(compact('user_arrays', 'participants', 'participants_owners', 'participantsGpOwner', 'participantsGpSharer', 'selecteduser'));

		if (isset($user_arrays) && !empty($user_arrays) && !in_array($this->user_id, $user_arrays)) {
			$this->Session->setFlash(__('This location is not authorized to access logedin user.'), 'error');
			$this->redirect(array("action" => "index", "project_id" => $project_id));
		}
		//pr($users);
		//pr($participants_owners,1);
		// ## end

		App::import('Controller', 'Users');
		$Users = new UsersController;
		$projects = null;
		$myprojectlist = $myreceivedprojectlist = $mygroupprojectlist = array();
		// Find All current user's projects
		$myprojectlist = $Users->__myproject_selectbox($this->user_id);
		// Find All current user's received projects
		$myreceivedprojectlist = $Users->__receivedproject_selectbox($this->user_id);
		// Find All current user's group projects
		$mygroupprojectlist = $Users->__groupproject_selectbox($this->user_id);

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

		$projects = array_map("strip_tags", $projects1);
		$projects = array_map("trim", $projects);
		natcasesort($projects);

		//$projects = get_my_projects($this->user_id);
		$this->set('projects', $projects);

		$viewVars['project_id'] = $project_id;
		$viewVars['sketch_id'] = $sketch_id;
		$this->set(compact("project_id", "sketch_id"));
		//pr($project,1);

		$this->Project->id = $project_id;

		if (!$this->Project->exists()) {
			$this->Session->setFlash(__('Invalid Project.'), 'error');
			$this->redirect(array("action" => "index", "project_id" => $project_id));
		}
		$this->ProjectSketch->id = $sketch_id;
		if (!$this->ProjectSketch->exists()) {
			$this->Session->setFlash(__('Invalid Sketch.'), 'error');
			$this->redirect(array("action" => "index", "project_id" => $project_id));
		}
		//pr($sketchdata,1);
		if (empty($sketchdata)) {
			$this->Session->setFlash(__('Invalid Project And Sketch Id.'), 'error');
			$this->redirect(array("action" => "index", "project_id" => $project_id));
		}
		if (isset($sketchdata) && $sketchdata['ProjectSketch']['locked'] == 1 && $sketchdata['ProjectSketch']['locked_user_id'] != $this->user_id) {

			$this->Session->setFlash(__('Sketch is locked ,Please wait for a moment.'), 'error');
			$this->redirect(array("action" => "index", "project_id" => $project_id));
		}

		if (isset($sketchdata) && $sketchdata['ProjectSketch']['is_edit_mode'] == 1 && $sketchdata['ProjectSketch']['edit_user_id'] != $this->user_id) {

			$this->Session->setFlash(__('Sketch is already editing ,Please wait for a moment.'), 'error');
			$this->redirect(array("action" => "index", "project_id" => $project_id));
		}
		/* if (isset($sketchdata) && $sketchdata['ProjectSketch']['is_edit_mode'] == 1 && $sketchdata['ProjectSketch']['edit_user_id'] != $this->user_id) {

			          $this->Session->setFlash(__('Sketch is already in editing mode,Please wait for a moment.'), 'error');
			          $this->redirect(array("action" => "index", "project_id" => $project_id));
		*/

		if ((empty($project_id) || !is_numeric($project_id) || $project_id == null)) {
			$this->Session->setFlash(__('Invalid Project Id.'), 'error');
			$this->redirect(array("action" => "index", "project_id" => $project_id));
		}

		if (empty($sketch_id) || !is_numeric($sketch_id) || $sketch_id == null) {
			$this->Session->setFlash(__('Invalid Sketch Id.'), 'error');
			$this->redirect(array("action" => "index", "project_id" => $project_id));
		}
		if ($project_id != '' && !array_key_exists($project_id, $projects)) {
			$this->Session->setFlash(__('Invalid Project Id.'), 'error');
			$this->redirect(array("controller" => "skts", "action" => "index"));
		}
		/*         * **************************** */
		$users = $this->ProjectSketchInterest->find("all", array(
			"conditions" => array("ProjectSketchInterest.sketch_id" => $sketch_id),
			"order" => "ProjectSketchInterest.created ASC",
		)
		);
		if (isset($users) && !empty($users)) {
			$user_id = Hash::extract($users, '{n}.ProjectSketchInterest.user_id');
		} else {
			$user_id = array();
		}

		$participant_users = $this->ProjectSketchParticipant->find("all", array("conditions" => array("ProjectSketchParticipant.project_sketch_id" => $sketch_id))
		);
		if (isset($participant_users) && !empty($participant_users)) {
			$participant_user_id = Hash::extract($participant_users, '{n}.ProjectSketchParticipant.user_id');
		} else {
			$participant_user_id = array();
		}

		if (
			isset($user_id) &&
			(!in_array($this->user_id, $user_id) && !empty($user_id)) ||
			(!in_array($this->user_id, $user_id) && !empty($user_id))
		) {
			if (!in_array($this->user_id, $participant_user_id)) {

				$this->Session->setFlash(__('This location is not authorized to access so please sent your interest.'), 'error');
				$this->redirect(array("action" => "index", "project_id" => $project_id));
			}
		}
		/*         * **************************** */
		if ($this->request->is('post') || $this->request->is('put')) {

			$title = trim($this->request->data['ProjectSketch']['sketch_title']);
			if ($title == '') {
				$this->Session->setFlash(__('Please provide sketch title.'), 'error');
				//$this->redirect(array("controller" => "skts", "action" => "edit", 'project_id' => $project_id, "sketch_id" => $sketch_id));
			}
			if ($this->request->data['ProjectSketch']['sketch_title'] == '') {
				$this->Session->setFlash(__('Please provide sketch title.'), 'error');
				$this->redirect(array("controller" => "skts", "action" => "saveas", 'project_id' => $project_id));
			}

			$users = isset($this->request->data['ProjectSketchParticipant']) ? $this->request->data['ProjectSketchParticipant'] : null;
			//pr($this->request->data);
			//pr($users,1);
			if (isset($this->request->data['ProjectSketch']['project_id']) && $this->request->data['ProjectSketch']['project_id'] == '') {
				$this->Session->setFlash(__('Could not be updated sketch properties due to an error with project id.'), 'error');
				$this->redirect(array("action" => "saveas", "project_id" => $project_id, "sketch_id" => $sketch_id));
			}

			if (isset($this->request->data['ProjectSketch']['locked']) && $this->request->data['ProjectSketch']['locked'] == 1) {
				$this->request->data['ProjectSketch']['locked_user_id'] = $this->Session->read("Auth.User.id");
			}

			if (!isset($this->request->data['ProjectSketch']['locked']) && isset($this->request->data['ProjectSketch']['locked_user_id']) && $this->request->data['ProjectSketch']['locked_user_id'] == $this->Session->read("Auth.User.id")) {
				$this->request->data['ProjectSketch']['locked'] = 0;
				$this->request->data['ProjectSketch']['locked_user_id'] = $this->Session->read("Auth.User.id");
			}
			if (!isset($this->request->data['ProjectSketch']['locked']) && !empty($this->request->data['ProjectSketch']['locked_user_id']) && $this->request->data['ProjectSketch']['locked_user_id'] != $this->Session->read("Auth.User.id")) {
				$this->Session->setFlash(__('Could not be updated sketch lock by another user.'), 'error');
				$this->redirect(array("action" => "saveas", "project_id" => $project_id, "sketch_id" => $sketch_id));
			}
			$this->request->data['ProjectSketch']['participant_all'] = isset($this->request->data['ProjectSketch']['participant_all']) ? 1 : 0;

			$this->request->data['ProjectSketch']['modified'] = date("Y-m-d h:i:s");
			$this->ProjectSketch->set($this->data);
			if ($this->ProjectSketch->validates()) {
				unset($this->request->data['ProjectSketchParticipant']);

				$data = $this->ProjectSketchParticipant->find("all", array("conditions" => array("ProjectSketchParticipant.project_sketch_id" => $sketch_id, "ProjectSketchParticipant.project_id" => $project_id)));

				if (isset($data) && !empty($data)) {
					$oldids = Hash::extract($data, '{n}.ProjectSketchParticipant.user_id');
				} else {
					$ids = array();
				}

				$sketch_data = $this->ProjectSketch->findById($sketch_id);

				foreach ($users as $k => $val) {

					if ((isset($val['user_id']) && !empty($val['user_id'])) && !in_array($val['user_id'], $oldids)) {
						//   pr($val['user_id']);

						$this->request->data['ProjectSketchParticipant'][$k]['user_id'] = $val['user_id'];
						$this->request->data['ProjectSketchParticipant'][$k]['project_id'] = $project_id;
						$this->request->data['ProjectSketchParticipant'][$k]['project_sketch_id'] = $sketch_id;
						$this->request->data['ProjectSketchParticipant'][$k]['send_email'] = 1;

						$user_data = $this->User->findById($val['user_id']);

						if ($val['user_id'] != $this->Session->read("Auth.User.id")) {
							$sendMail = $this->CommonEmail->sendEmailAddSketchParticipant($user_data, $sketch_data);
						}
					}
				}

				if ($this->ProjectSketch->saveAssociated($this->request->data)) {

					$users = $this->ProjectSketchParticipant->find("all", array(
						"conditions" => array("ProjectSketchParticipant.project_sketch_id" => $sketch_id),
					)
					);
					$sketch_data = $this->ProjectSketch->findById($sketch_id);
					if (isset($users) && !empty($users)) {
						foreach ($users as $k => $val) {
							if ($val['ProjectSketchParticipant']['user_id'] != $this->Session->read("Auth.User.id")) {
								$user_data = $this->User->findById($val['ProjectSketchParticipant']['user_id']);
								$sendMail = $this->CommonEmail->sendEmailAddSketchParticipant($user_data, $sketch_data);
							}
						}
					}

					$participant_user = $this->ProjectSketchParticipant->find("all", array("conditions" => array("ProjectSketchParticipant.project_sketch_id" => $sketch_id, "ProjectSketchParticipant.project_id" => $project_id)));
					$interest_user = $this->ProjectSketchInterest->find("all", array("conditions" => array("ProjectSketchInterest.sketch_id" => $sketch_id, "ProjectSketchInterest.project_id" => $project_id)));

					$parti_user_ids = Hash::extract($participant_user, '{n}.ProjectSketchParticipant.user_id');
					$interest_user_ids = Hash::extract($interest_user, '{n}.ProjectSketchInterest.user_id');
					if (isset($interest_user_ids) && !empty($interest_user_ids)) {
						foreach ($interest_user_ids as $value) {
							if (!empty($value) && !in_array($value, $parti_user_ids)) {
								$this->ProjectSketchInterest->deleteAll(array("ProjectSketchInterest.user_id" => $value));
							}
						}
					}

					$this->Session->setFlash(__('You have successfully updated sketch properties.'), 'success');
					$this->redirect(array("action" => "edit_sketch", "project_id" => $project_id, "sketch_id" => $sketch_id));
				} else {
					$this->Session->setFlash(__('Could not be updated sketch properties due to an error.'));
					$this->redirect(array("action" => "edit", "project_id" => $project_id, "sketch_id" => $sketch_id));
				}
			}
			//pr($this->request->data,1);
		}

		$this->request->data = $sketchdata;

		$data['title_for_layout'] = __('Sketch : Create', true);
		$data['page_heading'] = __('Create Sketch', true);
		$data['page_subheading'] = __('Create Sketch', true);
		$crumb = ['last' => ['data' => ['title' => 'Sketch Create', 'data-original-title' => 'Create Sketch']]];

		$this->set(compact('data', 'crumb', 'viewVars', 'project', 'sketch_id', 'project_id', 'sketchdata', 'sketchlist'));
		$this->setJsVar("viewVars", $viewVars);
	}

	public function edit_sketch() {
		$viewVars = $data = null;
		$this->layout = 'inner';
		$this->set('title_for_layout', 'Sketch : Edit Sketch');

		$project_id = (isset($this->params['named']['project_id']) && !empty($this->params['named']['project_id'])) ? $this->params['named']['project_id'] : null;
		$sketch_id = (isset($this->params['named']['sketch_id']) && !empty($this->params['named']['sketch_id'])) ? $this->params['named']['sketch_id'] : null;

		$this->ProjectSketchParticipant->updateAll(
			array("ProjectSketchParticipant.is_read" => 1), array("ProjectSketchParticipant.project_sketch_id" => $sketch_id, "ProjectSketchParticipant.user_id" => $this->user_id, "ProjectSketchParticipant.project_id" => $project_id)
		);

		$sketchlist = $this->ProjectSketch->find("list", array("fields" => array("id", "sketch_title"), "conditions" => array("ProjectSketch.status" => 1, "ProjectSketch.project_id" => $project_id)));
		$project = $this->Project->findById($project_id);
		$sketchdata = $this->ProjectSketch->find("first", array("conditions" => array("ProjectSketch.status" => 1, "ProjectSketch.id" => $sketch_id, "ProjectSketch.project_id" => $project_id)));

		$mysktspartipation = $this->ProjectSketchParticipant->find("list", array("fields" => array("id", "project_sketch_id"), "conditions" => array("ProjectSketchParticipant.user_id" => $this->Session->read("Auth.User.id"))));

		if (isset($mysktspartipation) && !empty($mysktspartipation)) {
			$mysktspartipation = array_unique($mysktspartipation);

			$sketchdatalist = $this->ProjectSketch->find("all", array("conditions" => array("ProjectSketch.status" => 1, "ProjectSketch.project_id" => $project_id, "ProjectSketch.id" => $mysktspartipation)));
		} else {
			$sketchdatalist = array();
		}

		$participant_users = $this->ProjectSketchParticipant->find("all", array(
			"conditions" => array(
				"ProjectSketchParticipant.project_sketch_id" => $sketch_id,
			),
			"order" => "ProjectSketchParticipant.user_id ASC",
		)
		);

		//$user_data = $this->User->findById(180);
		//$sendMail = $this->CommonEmail->sendEmailEditSketchRequest($user_data, $sketchdata);

		$this->Project->id = $project_id;
		if (!$this->Project->exists()) {
			$this->Session->setFlash(__('Invalid Project.'), 'error');
			$this->redirect(array("action" => "index", "project_id" => $project_id));
		}
		$this->ProjectSketch->id = $sketch_id;
		if (!$this->ProjectSketch->exists()) {
			$this->Session->setFlash(__('Invalid Sketch.'), 'error');
			$this->redirect(array("action" => "index", "project_id" => $project_id));
		}
		if (empty($sketchdata)) {
			$this->Session->setFlash(__('Invalid Project And Sketch Id.'), 'error');
			$this->redirect(array("action" => "index", "project_id" => $project_id));
		}

		if (isset($sketchdata) && $sketchdata['ProjectSketch']['is_edit_mode'] == 1 && $sketchdata['ProjectSketch']['edit_user_id'] != $this->user_id) {
			//$this->Session->setFlash(__('Sketch is already editing mode,Please wait a movement.'),'error');
			//$this->redirect(array("action" => "index", "project_id" => $project_id));
		}
		//die('here');
		if ((empty($project_id) || !is_numeric($project_id) || $project_id == null)) {
			$this->Session->setFlash(__('Invalid Project Id.'), 'error');
			$this->redirect(array("action" => "index", "project_id" => $project_id));
		}

		if (empty($sketch_id) || !is_numeric($sketch_id) || $sketch_id == null) {
			$this->Session->setFlash(__('Invalid Sketch Id.'), 'error');
			$this->redirect(array("action" => "index", "project_id" => $project_id));
		}

		if (isset($sketchdata['ProjectSketch']['is_edit_mode']) && $sketchdata['ProjectSketch']['is_edit_mode'] == 0) {
			$this->ProjectSketch->updateAll(array("ProjectSketch.updated_by" => $sketchdata['ProjectSketch']['edit_user_id'], "ProjectSketch.modified" => "'" . date("Y-m-d h:i:s") . "'", "ProjectSketch.is_edit_mode" => 1, "ProjectSketch.edit_user_id" => $this->user_id), array("ProjectSketch.id" => $sketch_id));
			$this->ProjectSketchInterest->deleteAll(array("ProjectSketchInterest.project_id" => $project_id, "ProjectSketchInterest.sketch_id" => $sketch_id, "ProjectSketchInterest.user_id" => $this->user_id));
			$sketchdata = $this->ProjectSketch->find("first", array("conditions" => array("ProjectSketch.status" => 1, "ProjectSketch.id" => $sketch_id, "ProjectSketch.project_id" => $project_id)));
		}

		$this->set("participant_users", $participant_users);

		//$interest = $this->ProjectSketchInterest->find("first", array("conditions" => array("ProjectSketchInterest.user_id" => $this->user_id, "ProjectSketchInterest.project_id" => $project_id, "ProjectSketchInterest.sketch_id" => $sketch_id)));
		//        $currenttime = date("Y-m-d h:i:s");
		//        $lastupdatedtime = date("Y-m-d h:i:s", $sketchdata['ProjectSketch']['updated']);
		//
		//        $datetime1 = strtotime($lastupdatedtime);
		//        $datetime2 = strtotime($currenttime);
		//        $interval = abs($datetime2 - $datetime1);
		//        $minutes = round($interval / 60);
		//        $this->set(compact("minutes"));
		// echo $minutes;die;

		$users = $this->ProjectSketchInterest->find("all", array(
			"conditions" => array("ProjectSketchInterest.sketch_id" => $sketch_id),
			"order" => "ProjectSketchInterest.created ASC",
		)
		);
		if (isset($users) && !empty($users)) {
			$user_id = Hash::extract($users, '{n}.ProjectSketchInterest.user_id');
		} else {
			$user_id = array();
		}

		if (isset($participant_users) && !empty($participant_users)) {
			$participant_user_id = Hash::extract($participant_users, '{n}.ProjectSketchParticipant.user_id');
		} else {
			$participant_user_id = array();
		}

		if (
			isset($user_id) &&
			(!in_array($this->user_id, $user_id) && !empty($user_id)) ||
			(!in_array($this->user_id, $user_id) && !empty($user_id))
		) {
			if (!in_array($this->user_id, $participant_user_id)) {

				$this->Session->setFlash(__('This location is not authorized to access so please sent your interest.'), 'error');
				$this->redirect(array("action" => "index", "project_id" => $project_id));
			}
		}

		if (isset($participant_users) && !empty($participant_users)) {
			$ids = Hash::extract($participant_users, '{n}.ProjectSketchParticipant.user_id');
		} else {
			$ids = array();
		}

		//pr($ids,1);
		if (isset($ids) && !in_array($this->user_id, $ids)) {
			$this->Session->setFlash(__('You are not participant in this sketch.'), 'error');
			$this->redirect(array("action" => "index", "project_id" => $project_id));
		}
		$interest = $this->ProjectSketchInterest->find("all", array(
			"conditions" => array("ProjectSketchInterest.project_id" => $project_id, "ProjectSketchInterest.sketch_id" => $sketch_id),
			"order" => "ProjectSketchInterest.created ASC",
		)
		);
		$this->set("interest", $interest);

		//pr($sketchdata,1);
		//pr($sketchdata['ProjectSketch'],1);

		App::import('Controller', 'Users');
		$Users = new UsersController;
		$projects = null;
		$myprojectlist = $myreceivedprojectlist = $mygroupprojectlist = array();
		// Find All current user's projects
		$myprojectlist = $Users->__myproject_selectbox($this->user_id);
		// Find All current user's received projects
		$myreceivedprojectlist = $Users->__receivedproject_selectbox($this->user_id);
		// Find All current user's group projects
		$mygroupprojectlist = $Users->__groupproject_selectbox($this->user_id);

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

		$projects = array_map("strip_tags", $projects1);
		$projects = array_map("trim", $projects);
		natcasesort($projects);

		//$projects = get_my_projects($this->user_id);
		$this->set('projects', $projects);

		$viewVars['project_id'] = $project_id;
		$viewVars['sketch_id'] = $sketch_id;
		//pr($project,1);
		// ## get project all user
		$owner = $this->Common->ProjectOwner($project_id, $this->Session->read('Auth.User.id'));
		$participants = participants($project_id, $owner['UserProject']['user_id']);
		$participants_owners = participants_owners($project_id, $owner['UserProject']['user_id']);
		$participantsGpOwner = participants_group_owner($project_id);
		$participantsGpSharer = participants_group_sharer($project_id);

		$participants = isset($participants) ? array_filter($participants) : $participants;
		$participants_owners = isset($participants_owners) ? array_filter($participants_owners) : $participants_owners;
		$participantsGpOwner = isset($participantsGpOwner) ? array_filter($participantsGpOwner) : $participantsGpOwner;
		$participantsGpSharer = isset($participantsGpSharer) ? array_filter($participantsGpSharer) : $participantsGpSharer;

		$this->set(compact('participants', 'participants_owners', 'participantsGpOwner', 'participantsGpSharer'));

		// ## end

		if ($project_id != '' && !array_key_exists($project_id, $projects)) {
			$this->Session->setFlash(__('Invalid Project Id.'), 'error');
			$this->redirect(array("controller" => "skts", "action" => "index"));
		}

		$project_detail = project_detail($project_id);

		$this->request->data = $sketchdata;

		$data['title_for_layout'] = __('Sketch : Edit', true);
		$data['page_heading'] = __('Sketch Update', true);
		$data['page_subheading'] = __('Sketch Update', true);
		$crumb = [
			'Summary' => [
				'data' => [
					'url' => '/skts/index/project_id:' . $project_id,
					'class' => 'tipText',
					'title' => $project_detail['title'],
					'data-original-title' => $project_detail['title'],
				],
			],
			'last' => ['data' => ['title' => 'Sketch update', 'data-original-title' => 'Sketch update']]];

		$this->set(compact('data', 'crumb', 'viewVars', 'sketchdatalist', 'project', 'project_id', 'sketch_id', 'sketchdata', 'sketchlist', 'project_detail'));
		$this->setJsVar("viewVars", $viewVars);
	}

	public function load_sketch($sketch_id = null) {
		$this->layout = 'ajax';
		if (isset($this->request->data) && !empty($this->request->data)) {
			$sketch_id = intval($this->request->data['sketch_id']);
			$project_id = intval($this->request->data['project_id']);

			if ($sketch_id) {
				$dir = $this->get_sketch_path($project_id, $sketch_id);
				$filename = "data.json";
				$filepath = $dir . '/' . $filename;
				if (file_exists($filepath)) {
					$json = file_get_contents($filepath);
					$data = json_decode($json, true);
				} else {
					$data = array();
				}

				$edit_user_id = !empty($data['edit_user_id']) ? $data['edit_user_id'] : 0;
				$user_id = !empty($data['user_id']) ? $data['user_id'] : 0;
				$is_edit_mode = !empty($data['is_edit_mode']) ? $data['is_edit_mode'] : 0;
				$canvas_id = !empty($data['canvas_id']) ? $data['canvas_id'] : 10;
				$sketch_title = !empty($data['sketch_title']) ? $data['sketch_title'] : '';
				$sketch_description = !empty($data['sketch_description']) ? $data['sketch_description'] : '';
				$content = !empty($data['content']) ? $data['content'] : '';
				$canvas_data = !empty($data['canvas_data']) ? $data['canvas_data'] : '';
				$images_data = !empty($data['images_data']) ? $data['images_data'] : '';
				$config = !empty($data['config']) ? $data['config'] : '';

				$responseData = array(
					'project_id' => $project_id,
					'sketch_id' => $sketch_id,
					'edit_user_id' => $edit_user_id,
					'user_id' => $user_id,
					'canvas_id' => $canvas_id,
					'is_edit_mode' => $is_edit_mode,
					'sketch_title' => $sketch_title,
					'sketch_description' => $sketch_description,
					'content' => $content,
					'canvas_data' => $canvas_data,
					'images_data' => $images_data,
					'config' => $config,
				);
				$sketch = $this->ProjectSketch->find("first", array(
					"fields" => array("ProjectSketch.edit_user_id", "ProjectSketch.is_edit_mode"),
					"conditions" => array("ProjectSketch.id" => $sketch_id))
				);
				$is_edit_mode = isset($sketch['ProjectSketch']['is_edit_mode']) && $sketch['ProjectSketch']['is_edit_mode'] == 1 ? 1 : 0;
				$edit_user_id = isset($sketch['ProjectSketch']['edit_user_id']) && !empty($sketch['ProjectSketch']['edit_user_id']) ? $sketch['ProjectSketch']['edit_user_id'] : 0;

				$interest = $this->ProjectSketchInterest->find("all", array(
					"conditions" => array("ProjectSketchInterest.sketch_id" => $sketch_id),
					"order" => "ProjectSketchInterest.created ASC",
				)
				);
				$sketchdata = $this->ProjectSketch->find("first", array(
					"conditions" => array("ProjectSketch.status" => 1, "ProjectSketch.id" => $sketch_id))
				);
				$participant_users = $this->ProjectSketchParticipant->find("all", array(
					"conditions" => array(
						"ProjectSketchParticipant.project_sketch_id" => $sketch_id,
					),
					"order" => "ProjectSketchParticipant.user_id ASC",
				)
				);

				$user_li_data = $editing_user = '';
				$editmode = $sketchdata['ProjectSketch']['is_edit_mode'];
				$edituser = $sketchdata['ProjectSketch']['edit_user_id'];
				$currentuser = $this->Session->read("Auth.User.id");
				$users = array();
				$interest_users = (isset($interest) && !empty($interest)) ? Hash::extract($interest, '{n}.ProjectSketchInterest.user_id') : array();
				if (isset($participant_users) && !empty($participant_users)) {
					foreach ($participant_users as $k => $value) {
						$user = $value['ProjectSketchParticipant']['user_id'];
						if (($editmode == 1 && $edituser == $user)) {
							$editing_user[0] = $user;
						} else {
							$k = $k + 1;
							$users[$k] = $user;
						}
					}
				}
				if (isset($editing_user) && !empty($editing_user)) {
					$interest_users = array_unique(array_merge($editing_user, $interest_users));
				}
				if (isset($interest_users) && !empty($interest_users)) {
					$finalArr = array_unique(array_merge($interest_users, $users));
				} else {
					$finalArr = $users;
				}

				if (isset($finalArr) && !empty($finalArr)) {
					foreach ($finalArr as $user) {
						$checkedself = $tip = $checkedother = $checkedthumb = $interest_user = $html = $self_edit = $other_user = '';
						if (($editmode == 1 && $edituser == $user)) {
							$checkedself = ' checked="checked"  disabled="disabled"  ';
							$tip = 'data-original-title="Currently Editing"';
							$self_edit = 'self_edit';
						}
						if (($editmode == 1 && $user != $currentuser)) {
							$checkedother = ' disabled="disabled" ';
							$other_user = ' other_user ';
							$checkedthumb = '  ';
						}
						if ($editmode == 1 && $edituser != $user && $user == $currentuser) {
							$checkedthumb = ' q-thumb ';
						}
						if (isset($interest_users) && !empty($interest_users) && ($editmode == 1 && $edituser != $user) && in_array($user, $interest_users)) {
							$checkedthumb = ' q-thumb ';
							$interest_user = ' checked="checked" ';
							$tip = 'data-original-title="Request to Edit"';
						}

						$pic = $profiles = $job_title = $pic = $btn_html = '';
						$user_data = $this->Common->get_user_data($user);
						if (isset($user_data) && !empty($user_data)) {
							$pic = $user_data['UserDetail']['profile_pic'];
							$profiles = SITEURL . USER_PIC_PATH . $pic;
							$job_title = $user_data['UserDetail']['job_title'];
							if ($user != $currentuser) {
								$btn_html = "<p><a class='btn btn-default btn-xs disabled'>Send Message</a> <a class='btn btn-default btn-xs disabled'>Start Chat</a></p>";
							}
						}

						if (!empty($pic) && file_exists(USER_PIC_PATH . $pic)) {
							$profiles = SITEURL . USER_PIC_PATH . $pic;
						} else {
							$profiles = SITEURL . 'img/image_placeholders/logo_placeholder.gif';
						}
						$html .= '<div class="repeate">';

						$html .= '<a href="#"';
						$html .= 'data-remote="' . SITEURL . 'shares/show_profile/' . $user . '"';
						$html .= 'data-target="#popup_model_box"';
						$html .= 'data-toggle="modal"';
						$html .= 'class="pophover"';
						$html .= 'data-content="';
						$datacontent = "<div class='user-detail'>";
						$datacontent .= "<p>";
						$datacontent .= isset($user_data['UserDetail']) ? $user_data['UserDetail']['first_name'] . ' ' . $user_data['UserDetail']['last_name'] : 'N/A';
						$datacontent .= "</p>";
						$datacontent .= "<p>";
						$datacontent .= $job_title;
						$datacontent .= "</p>";
						$datacontent .= $btn_html;

						$datacontent .= "</div>";

						$html .= $datacontent;
						$html .= '"';
						$html .= 'data-original-title="" title="">';
						$html .= '<img src="' . $profiles . '" class="user-image" style="border: 2px solid #333">';
						$html .= '</a>';

						$html .= '<div ' . $tip . '  class="tipText chk-wrapper ' . $checkedthumb . '">';
						$html .= '<input ' . $checkedother . ' &nbsp; ' . $interest_user . ' &nbsp; ' . $checkedself . ' id="user-' . $user . '" class="user-checkbox checkbox-custom  &nbsp; ' . ' &nbsp; ' . $self_edit . '  &nbsp; ' . $other_user . '" name="data[ProjectSketch][user_id][]" value="' . $user . '" type="checkbox">';
						$html .= '<label for="user-' . $user . '" class="checkbox-custom-label"></label>';
						$html .= '</div> </div> ';
						$user_li_data .= $html;
					}
				}
				$user_li_data .= "<script type='text/javascript'>$(document).ready(function () { $('.pophover').popover({placement: 'bottom',trigger: 'hover', html: true, container: 'body', delay: {show: 50, hide: 400} })});</script>";

				$response = array(
					'success' => true,
					'data' => $responseData,
					'sketch_id' => $sketch_id,
					'msg' => '',
					'is_edit_mode' => $is_edit_mode,
					'edit_user_id' => $edit_user_id,
					'interest' => $user_li_data,
				);

				echo json_encode($response);
			}
		}
		exit();
	}

	public function participant($sketch_id = null) {
		$this->layout = false;
		$this->autoRender = false;

		$interest = $this->ProjectSketchInterest->find("all", array(
			"conditions" => array("ProjectSketchInterest.sketch_id" => $sketch_id),
			"order" => "ProjectSketchInterest.created ASC",
		)
		);
		$sketchdata = $this->ProjectSketch->find("first", array(
			"conditions" => array("ProjectSketch.status" => 1, "ProjectSketch.id" => $sketch_id))
		);
		$participant_users = $this->ProjectSketchParticipant->find("all", array(
			"conditions" => array(
				"ProjectSketchParticipant.project_sketch_id" => $sketch_id,
			),
			"order" => "ProjectSketchParticipant.user_id ASC",
		)
		);
		$currentuser = $this->Session->read("Auth.User.id");
		$this->set(compact("participant_users", "interest", "sketchdata", "currentuser"));
		$this->render('partials/participant');
	}

	public function saveasdelete() {
		$project_id = (isset($this->params['named']['project_id']) && !empty($this->params['named']['project_id'])) ? $this->params['named']['project_id'] : null;
		$sketch_id = (isset($this->params['named']['sketch_id']) && !empty($this->params['named']['sketch_id'])) ? $this->params['named']['sketch_id'] : null;
		$data = $this->ProjectSketch->find("first", array("conditions" => array("ProjectSketch.id" => $sketch_id, "ProjectSketch.project_id" => $project_id)));
		if (isset($data) && !empty($data)) {
			$title = trim($data['ProjectSketch']['sketch_title']);
			if (isset($title) && !empty($title)) {
				$this->redirect(array("action" => "edit_sketch", "project_id" => $project_id, "sketch_id" => $sketch_id));
			} else {
				$this->ProjectSketch->delete($sketch_id);
				$this->ProjectSketchParticipant->deleteAll(array("ProjectSketchParticipant.project_sketch_id" => $sketch_id));

				$path = ROOT . "/json/skts/" . $project_id . "/" . $sketch_id . "/data.json";
				if (is_file($path)) {
					unlink($path);
				}

				if (is_dir(ROOT . "/json/skts/" . $project_id . "/" . $sketch_id . "")) {
					rmdir(ROOT . "/json/skts/" . $project_id . "/" . $sketch_id . "");
				}

				$this->redirect(array("action" => "edit_sketch", "project_id" => $project_id, "sketch_id" => $data['ProjectSketch']['parent_id']));
			}
		}
	}

	public function cancel() {
		Configure::write('debug', 0);
		$this->layout = 'ajax';
		$response = array();
		$project_id = (isset($this->params['named']['project_id']) && !empty($this->params['named']['project_id'])) ? $this->params['named']['project_id'] : null;
		$sketch_id = (isset($this->params['named']['sketch_id']) && !empty($this->params['named']['sketch_id'])) ? $this->params['named']['sketch_id'] : null;

		if ($sketch_id && $project_id) {
			$users = $this->ProjectSketchInterest->find("all", array(
				"conditions" => array("ProjectSketchInterest.sketch_id" => $sketch_id),
				"order" => "ProjectSketchInterest.created ASC",
				"limit" => 1,
			)
			);
			if (isset($users) && !empty($users)) {
				$user_id = Hash::extract($users, '{n}.ProjectSketchInterest.user_id');
			} else {
				$user_id = array();
			}

			//pr($participant_user_id);
			//pr($user_id,1);
			$sketch = $this->ProjectSketch->find("first", array("conditions" => array("ProjectSketch.is_edit_mode" => 1, "ProjectSketch.edit_user_id" => $this->user_id, "ProjectSketch.id" => $sketch_id)));

			if (isset($sketch) && isset($sketch['ProjectSketch']['locked']) && $sketch['ProjectSketch']['locked'] == 0) {
				//pr($sketch,1);
				if (isset($user_id) && empty($user_id)) {

					$this->ProjectSketch->updateAll(array("ProjectSketch.updated_by" => $sketch['ProjectSketch']['edit_user_id'], "ProjectSketch.modified" => "'" . date("Y-m-d h:i:s") . "'", "ProjectSketch.is_edit_mode" => 0, "ProjectSketch.edit_user_id" => $this->user_id), array("ProjectSketch.id" => $sketch_id));
					$this->ProjectSketchInterest->deleteAll(array("ProjectSketchInterest.project_id" => $project_id, "ProjectSketchInterest.sketch_id" => $sketch_id, "ProjectSketchInterest.user_id" => $this->user_id));
					$response = array(
						'success' => true,
						'sketch_id' => $sketch_id,
						'msg' => 'Sketch has been updated successfully.',
					);
				} else if (isset($user_id[0]) && !empty($user_id[0])) {

					$this->ProjectSketch->updateAll(array("ProjectSketch.updated_by" => $sketch['ProjectSketch']['edit_user_id'], "ProjectSketch.modified" => "'" . date("Y-m-d h:i:s") . "'", "ProjectSketch.is_edit_mode" => 1, "ProjectSketch.edit_user_id" => $user_id[0]), array("ProjectSketch.id" => $sketch_id));
					$this->ProjectSketchInterest->deleteAll(array("ProjectSketchInterest.project_id" => $project_id, "ProjectSketchInterest.sketch_id" => $sketch_id, "ProjectSketchInterest.user_id" => $user_id[0]));

					if (isset($users[0]['ProjectSketchInterest']['user_id']) && !empty($users[0]['ProjectSketchInterest']['user_id'])) {
						$user_data = $this->User->findById($users[0]['ProjectSketchInterest']['user_id']);
						//pr($sketch,1);
						$sendMail = $this->CommonEmail->sendEmailEditSketchRequest($user_data, $sketch);
					}

					$response = array(
						'success' => true,
						'sketch_id' => $sketch_id,
						'msg' => 'Sketch has been updated successfully.',
					);
				} else {
					$response = array(
						'success' => true,
						'sketch_id' => $sketch_id,
						'msg' => 'Sketch could not be updated successfully.',
					);
				}
			} else {
				$response = array(
					'success' => true,
					'sketch_id' => $sketch_id,
					'msg' => 'Sketch could not be updated because sketch is permanetally locked.',
				);
			}
		}
		echo json_encode($response);
		exit();
	}

	public function send_interest() {
		Configure::write('debug', 0);
		$this->layout = 'ajax';
		$response = array();
		$project_id = (isset($this->params['named']['project_id']) && !empty($this->params['named']['project_id'])) ? $this->params['named']['project_id'] : null;
		$sketch_id = (isset($this->params['named']['sketch_id']) && !empty($this->params['named']['sketch_id'])) ? $this->params['named']['sketch_id'] : null;
		$user_id = (isset($this->params['named']['user_id']) && !empty($this->params['named']['user_id'])) ? $this->params['named']['user_id'] : null;
		$status = (isset($this->params['named']['status']) && !empty($this->params['named']['status'])) ? $this->params['named']['status'] : null;

		if ($sketch_id && $project_id && $user_id) {

			$this->request->data['ProjectSketchInterest']['user_id'] = $user_id;
			$this->request->data['ProjectSketchInterest']['sketch_id'] = $sketch_id;
			$this->request->data['ProjectSketchInterest']['project_id'] = $project_id;
			$data = $this->ProjectSketchInterest->find("all", array("conditions" => array("ProjectSketchInterest.user_id" => $user_id, "ProjectSketchInterest.sketch_id" => $sketch_id, "ProjectSketchInterest.project_id" => $project_id)));
			if (isset($data) && !empty($data)) {
				$ids = Hash::extract($data, '{n}.ProjectSketchInterest.id');
				$user_ids = Hash::extract($data, '{n}.ProjectSketchInterest.user_id');
			} else {
				$ids = $user_ids = array();
			}

			if ($status == 'true') {

				if ($this->ProjectSketchInterest->save($this->request->data)) {
					$sketch_data = $this->ProjectSketch->findById($sketch_id);
					$edit_user_id = $sketch_data['ProjectSketch']['edit_user_id'];

					if ($this->Common->check_email_permission($user_id, 'sketch_participant_request') == true) {
						$user_data = $this->User->findById($user_id);
						//$sendMail = $this->CommonEmail->sendEmailEditSketchRequest($user_data, $sketch_data);
					}

					/************** socket messages **************/
					if (SOCKET_MESSAGES) {
						$current_user_id = $user_id;
						$send_notify = false;
						if (isset($edit_user_id) && !empty($edit_user_id)) {
							if (web_notify_setting($edit_user_id, 'sketches', 'sketch_participant_request')) {
								$send_notify = true;
							}
						}
						$userDetail = get_user_data($current_user_id);
						$skt_project_id = $sketch_data['ProjectSketch']['project_id'];
						$content = [
							'notification' => [
								'created_id' => $current_user_id,
								// 'project_id' => $skt_project_id,
								'creator_name' => $userDetail['UserDetail']['full_name'],
								'subject' => 'Sketch edit request',
								'heading' => 'Sketch: ' . strip_tags(getFieldDetail('ProjectSketch', $sketch_id, 'sketch_title')),
								'sub_heading' => 'Project: ' . strip_tags(getFieldDetail('Project', $skt_project_id, 'title')),
								'date_time' => timezoneTimeConvertor($this->user_id, date('Y-m-d g:iA'), $format = 'd M Y g:iA') . user_country($this->user_id),
							],
						];
						if ($send_notify) {
							$content['received_users'] = [$edit_user_id];
						}

						$request = array(
							'header' => array(
								'Content-Type' => 'application/json',
							),
						);
						$content = json_encode($content);
						$HttpSocket = new HttpSocket([
							'ssl_verify_host' => false,
							'ssl_verify_peer_name' => false,
							'ssl_verify_peer' => false,
						]);

						$results = $HttpSocket->post(CHATURL . '/serveremit', $content, $request);
					}
					/************** socket messages **************/

					$response = array(
						'success' => true,
						'sketch_id' => $sketch_id,
						'msg' => 'Sketch\'s interest has been added successfully.',
					);
				} else {
					$response = array(
						'success' => false,
						'sketch_id' => $sketch_id,
						'msg' => 'Sketch\'s interest could not be added successfully.',
					);
				}
			}
			if ($status == 'false') {
				if (!empty($ids) && $this->ProjectSketchInterest->deleteAll(array("ProjectSketchInterest.id" => $ids))) {
					$response = array(
						'success' => true,
						'sketch_id' => $sketch_id,
						'msg' => 'Sketch\'s interest has been updated successfully.',
					);
				} else {
					$response = array(
						'success' => false,
						'sketch_id' => $sketch_id,
						'msg' => 'Sketch\'s interest could not be updated successfully.',
					);
				}
			}
		}
		echo json_encode($response);
		exit();
	}

	public function remove_participant() {
		Configure::write('debug', 0);
		$this->layout = 'ajax';
		$response = array();
		$project_id = (isset($this->params['named']['project_id']) && !empty($this->params['named']['project_id'])) ? $this->params['named']['project_id'] : null;
		$sketch_id = (isset($this->params['named']['sketch_id']) && !empty($this->params['named']['sketch_id'])) ? $this->params['named']['sketch_id'] : null;
		$user_id = (isset($this->params['named']['user_id']) && !empty($this->params['named']['user_id'])) ? $this->params['named']['user_id'] : null;
		$status = (isset($this->params['named']['status']) && !empty($this->params['named']['status'])) ? $this->params['named']['status'] : null;

		if ($sketch_id && $project_id) {
			if ($status == 'single') {
				$data = $this->ProjectSketchParticipant->find("all", array("conditions" => array("ProjectSketchParticipant.user_id" => $user_id, "ProjectSketchParticipant.project_sketch_id" => $sketch_id, "ProjectSketchParticipant.created_user_id IS NULL", "ProjectSketchParticipant.project_id" => $project_id)));
				//pr($data);
			}
			if ($status == 'all') {
				$data = $this->ProjectSketchParticipant->find("all", array("conditions" => array("NOT" => array("ProjectSketchParticipant.user_id" => $this->user_id), "ProjectSketchParticipant.created_user_id IS NULL", "ProjectSketchParticipant.project_sketch_id" => $sketch_id, "ProjectSketchParticipant.project_id" => $project_id)));
			}
			if (isset($data) && !empty($data)) {
				$ids = Hash::extract($data, '{n}.ProjectSketchParticipant.id');
				$user_ids = Hash::extract($data, '{n}.ProjectSketchParticipant.user_id');

			} else {
				$ids = $user_ids = array();
			}

			//pr($user_ids);
			//pr($ids,1);

			if (!empty($ids)) {
				$date = date('Y-m-d h:i:s');
				$this->ProjectSketch->updateAll(array('ProjectSketch.modified' => "'.$date.'", 'ProjectSketch.updated_by' => $this->user_id), array("ProjectSketch.id" => $sketch_id));
				$this->ProjectSketchParticipant->deleteAll(array("ProjectSketchParticipant.id" => $ids));

				$sketch_data = $this->ProjectSketch->findById($sketch_id);
				if (!empty($user_ids)) {
					foreach ($user_ids as $val) {
						if ($val != $this->user_id) {

							if ($this->Common->check_email_permission($val, 'sketch_remove_participant') == true) {
								$user_data = $this->User->findById($val);
								$sendMail = $this->CommonEmail->sendEmailRemoveSketchParticipant($user_data, $sketch_data);
							}
						}
					}

					/************** socket messages **************/
					if (SOCKET_MESSAGES) {
						$current_user_id = $this->user_id;
						$skts_users = $user_ids;
						if (isset($skts_users) && !empty($skts_users)) {
							if (($key = array_search($current_user_id, $skts_users)) !== false) {
								unset($skts_users[$key]);
							}
						}
						$notify_users = null;
						if (isset($skts_users) && !empty($skts_users)) {
							foreach ($skts_users as $key1 => $value1) {
								if (web_notify_setting($value1, 'sketches', 'sketch_remove_participant')) {
									$notify_users[] = $value1;
								}
							}
						}
						$userDetail = get_user_data($current_user_id);
						$skt_project_id = $sketch_data['ProjectSketch']['project_id'];
						$content = [
							'notification' => [
								'created_id' => $current_user_id,
								// 'project_id' => $skt_project_id,
								'creator_name' => $userDetail['UserDetail']['full_name'],
								'subject' => 'Sketch sharing removed',
								'heading' => 'Sketch: ' . strip_tags(getFieldDetail('ProjectSketch', $sketch_id, 'sketch_title')),
								'sub_heading' => 'Project: ' . strip_tags(getFieldDetail('Project', $skt_project_id, 'title')),
								'date_time' => timezoneTimeConvertor($this->user_id, date('Y-m-d g:iA'), $format = 'd M Y g:iA') . user_country($this->user_id),
							],
						];
						if (is_array($notify_users)) {
							$content['received_users'] = array_values($notify_users);
						}

						$request = array(
							'header' => array(
								'Content-Type' => 'application/json',
							),
						);
						$content = json_encode($content);
						$HttpSocket = new HttpSocket([
							'ssl_verify_host' => false,
							'ssl_verify_peer_name' => false,
							'ssl_verify_peer' => false,
						]);

						$results = $HttpSocket->post(CHATURL . '/serveremit', $content, $request);
					}
					/************** socket messages **************/

				}

				$response = array(
					'success' => true,
					'sketch_id' => $sketch_id,
					'msg' => 'Sketch\'s participant has been updated successfully.',
				);
			} else {
				$response = array(
					'success' => false,
					'sketch_id' => $sketch_id,
					'msg' => 'Sketch\'s participant could not be updated successfully.',
				);
			}
		}
		echo json_encode($response);
		exit();
	}

	public function delete_sketch() {
		// Configure::write('debug', 0);
		$this->layout = 'ajax';
		$response = array();

		$project_id = (isset($this->params['named']['project_id']) && !empty($this->params['named']['project_id'])) ? $this->params['named']['project_id'] : null;
		$sketch_id = (isset($this->params['named']['sketch_id']) && !empty($this->params['named']['sketch_id'])) ? $this->params['named']['sketch_id'] : null;

		if ($sketch_id != '') {
			$sketch = $this->ProjectSketch->findById($sketch_id);

			if (isset($sketch['ProjectSketchParticipant']) && !empty($sketch['ProjectSketchParticipant'])) {
				$ids = Hash::extract($sketch['ProjectSketchParticipant'], '{n}.created_user_id');
			} else {
				$ids = array();
			}
			if (isset($sketch['ProjectSketchParticipant']) && !empty($sketch['ProjectSketchParticipant'])) {
				foreach ($sketch['ProjectSketchParticipant'] as $user_id) {
					if ($user_id['user_id'] != $this->user_id) {
						if ($this->Common->check_email_permission($user_id['user_id'], 'sketch_delete_alert') == true) {
							$user_data = $this->User->findById($user_id['user_id']);
							$sendMail = $this->CommonEmail->sendEmailDeleteSketch($user_data, $sketch);
						}
					}
				}

				/************** socket messages **************/
				if (SOCKET_MESSAGES) {
					$current_user_id = $this->Session->read('Auth.User.id');
					$sketch_users = $sketch['ProjectSketchParticipant'];
					if (isset($sketch_users) && !empty($sketch_users)) {
						$sketch_users = Set::extract($sketch_users, '/user_id');
					}
					if (isset($sketch_users) && !empty($sketch_users)) {
						if (($key = array_search($current_user_id, $sketch_users)) !== false) {
							unset($sketch_users[$key]);
						}
					}
					$open_users = null;
					if (isset($sketch_users) && !empty($sketch_users)) {
						foreach ($sketch_users as $key1 => $value1) {
							if (web_notify_setting($value1, 'sketches', 'sketch_delete_alert')) {
								$open_users[] = $value1;
							}
						}
					}
					$userDetail = get_user_data($current_user_id);
					$skt_project_id = $sketch['ProjectSketch']['project_id'];
					$content = [
						'notification' => [
							'created_id' => $current_user_id,
							// 'project_id' => $skt_project_id,
							'creator_name' => $userDetail['UserDetail']['full_name'],
							'subject' => 'Sketch deleted',
							'heading' => 'Sketch: ' . strip_tags(getFieldDetail('ProjectSketch', $sketch_id, 'sketch_title')),
							'sub_heading' => 'Project: ' . strip_tags(getFieldDetail('Project', $skt_project_id, 'title')),
							'date_time' => timezoneTimeConvertor($this->user_id, date('Y-m-d g:iA'), $format = 'd M Y g:iA') . user_country($this->user_id),
						],
					];
					if (is_array($open_users)) {
						$content['received_users'] = array_values($open_users);
					}

					$request = array(
						'header' => array(
							'Content-Type' => 'application/json',
						),
					);
					$content = json_encode($content);
					$HttpSocket = new HttpSocket([
						'ssl_verify_host' => false,
						'ssl_verify_peer_name' => false,
						'ssl_verify_peer' => false,
					]);

					$results = $HttpSocket->post(CHATURL . '/serveremit', $content, $request);
				}
				/************** socket messages **************/

			}

			$ids = array_filter($ids);
			if (isset($ids) && !empty($ids) && in_array($this->Session->read("Auth.User.id"), $ids)) {
				if ($this->ProjectSketch->delete($sketch_id)) {

					$sketch_p = $this->ProjectSketch->find("all", array("conditions" => array("ProjectSketch.parent_id" => $sketch_id)));
					if (isset($sketch['ProjectSketch']) && !empty($sketch['ProjectSketch'])) {
						$p_ids = Hash::extract($sketch['ProjectSketch'], '{n}.id');
					} else {
						$p_ids = array();
					}

					$this->ProjectSketch->deleteAll(array("ProjectSketch.parent_id" => $sketch_id));
					$this->ProjectSketchInterest->deleteAll(array("ProjectSketchInterest.sketch_id" => $p_ids));

					$this->ProjectSketchInterest->deleteAll(array("ProjectSketchInterest.sketch_id" => $sketch_id));
					$response = array(
						'success' => true,
						'sketch_id' => $sketch_id,
						'msg' => 'Sketch has been updated successfully.',
					);
				} else {
					$response = array(
						'success' => false,
						'sketch_id' => $sketch_id,
						'msg' => 'Sketch could not be updated successfully.',
					);
				}
			}
		}
		echo json_encode($response);
		exit();
	}

	public function filtersaveas() {
		Configure::write('debug', 0);
		$this->layout = 'ajax';
		$this->autoRender = false;
		$response = array();
		$project_id = (isset($this->params['named']['project_id']) && !empty($this->params['named']['project_id'])) ? $this->params['named']['project_id'] : null;
		$sketch_id = (isset($this->params['named']['sketch_id']) && !empty($this->params['named']['sketch_id'])) ? $this->params['named']['sketch_id'] : null;

		$sketch_save_as_data = $this->ProjectSketch->find("all", array("conditions" => array(
			"ProjectSketch.id" => $sketch_all_id, "ProjectSketch.status" => 1,
			"ProjectSketch.project_id" => $project_id,
			"ProjectSketch.parent_id" => 0,
			"ProjectSketch.id" => $sketch_id,
		),
		)
		);
		$this->set("sketch_save_as_data", $sketch_save_as_data);
		$this->render("partials/saveas");
	}

	public function makemainsketch() {
		Configure::write('debug', 0);
		$this->layout = 'ajax';
		$this->autoRender = false;
		$response = array();
		$project_id = (isset($this->params['named']['project_id']) && !empty($this->params['named']['project_id'])) ? $this->params['named']['project_id'] : null;
		$sketch_id = (isset($this->params['named']['sketch_id']) && !empty($this->params['named']['sketch_id'])) ? $this->params['named']['sketch_id'] : null;
		$this->ProjectSketch->updateAll(array("ProjectSketch.parent_id" => 0), array("ProjectSketch.id" => $sketch_id));
		if ($sketch_id) {
			$response = array(
				'success' => true,
				'sketch_id' => $sketch_id,
				'msg' => 'Sketch has been updated successfully.',
			);
		} else {
			$response = array(
				'success' => false,
				'sketch_id' => $sketch_id,
				'msg' => 'Sketch could not be updated successfully.',
			);
		}

		echo json_encode($response);
		exit();
	}

	public function makemainsketch_html() {
		Configure::write('debug', 0);
		$this->layout = 'ajax';
		$this->autoRender = false;
		$response = array();
		$project_id = (isset($this->params['named']['project_id']) && !empty($this->params['named']['project_id'])) ? $this->params['named']['project_id'] : null;
		$sketch_id = (isset($this->params['named']['sketch_id']) && !empty($this->params['named']['sketch_id'])) ? $this->params['named']['sketch_id'] : null;

		$this->ProjectSketch->updateAll(array("ProjectSketch.parent_id" => 0, 'updated_by' => $this->Session->read("Auth.User.id")), array("ProjectSketch.id" => $sketch_id));

		$project = $this->Project->findById($project_id);

		$sketch_ids = $this->ProjectSketchParticipant->find("all", array(
			"conditions" => array('ProjectSketchParticipant.user_id' => $this->user_id),
			"order" => "ProjectSketchParticipant.created ASC",
		)
		);
		if (isset($sketch_ids) && !empty($sketch_ids)) {
			$sketch_all_id = Hash::extract($sketch_ids, '{n}.ProjectSketchParticipant.project_sketch_id');
		} else {
			$sketch_all_id = array();
		}

		//pr($sketch_all_id,1);

		$sketchdata = $this->ProjectSketch->find("all", array("conditions" => array("ProjectSketch.parent_id" => 0, "ProjectSketch.status" => 1, "ProjectSketch.project_id" => $project_id, "ProjectSketch.id" => $sketch_all_id)));

		$this->Paginator->settings = array(
			'conditions' => array("ProjectSketch.id" => $sketch_all_id, "ProjectSketch.parent_id" => 0, "ProjectSketch.status" => 1, "ProjectSketch.project_id" => $project_id),
			'limit' => 8,
		);
		$sketchdata = $this->Paginator->paginate('ProjectSketch');

		$this->request->params['action'] = 'index';
		$this->set(compact("sketchdata", "project_id"));
		$this->render("partials/mainsketch");
	}

	public function update_sketch() {
		Configure::write('debug', 0);
		$this->layout = 'ajax';
		$sketch_id = !empty($this->request->data['sketch_id']) ? $this->request->data['sketch_id'] : false;

		$project_id = !empty($this->request->data['project_id']) ? $this->request->data['project_id'] : 0;
		$cancel = !empty($this->request->data['cancel']) ? $this->request->data['cancel'] : 0;

		if ($sketch_id && $project_id) {

			//$sketch_title = !empty($this->request->data['sketch_title']) ? $this->request->data['sketch_title'] : 'Demo Sketcher';
			//$sketch_description = !empty($this->request->data['sketch_description']) ? $this->request->data['sketch_description'] : '';
			$is_edit_mode = !empty($this->request->data['is_edit_mode']) ? $this->request->data['is_edit_mode'] : 1;
			$edit_user_id = !empty($this->request->data['edit_user_id']) ? $this->request->data['edit_user_id'] : $this->user_id;

			$content = !empty($this->request->data['content']) ? $this->request->data['content'] : false;

			if ($content) {
				$canvas_data = !empty($this->request->data['canvas_data']) ? $this->request->data['canvas_data'] : '';
				$images_data = !empty($this->request->data['images_data']) ? $this->request->data['images_data'] : '';
				$config = !empty($this->request->data['config']) ? $this->request->data['config'] : '';
			} else {
				$content = '';
				$canvas_data = '';
				$images_data = '';
				$config = '';
			}

			$data = array(
				'id' => intval($sketch_id),
				'sketch_id' => intval($sketch_id),
				'project_id' => intval($project_id),
				'edit_user_id' => intval($edit_user_id),
				'is_edit_mode' => $is_edit_mode,
				'content' => $content,
				'canvas_data' => $canvas_data,
				'images_data' => $images_data,
				'config' => $config,
				'updated_by' => $this->user_id,
				'modified' => date('Y-m-d h:i:s'),
			);
			$this->request->data['ProjectSketch'] = $data;
			$this->set($this->request->data);
			$this->ProjectSketch->save($this->request->data);
			$users = $this->ProjectSketchInterest->find("all", array(
				"conditions" => array("ProjectSketchInterest.sketch_id" => $sketch_id),
				"order" => "ProjectSketchInterest.created ASC",
				"limit" => 1,
			)
			);
			if (isset($users) && !empty($users)) {
				$user_id = Hash::extract($users, '{n}.ProjectSketchInterest.user_id');
			} else {
				$user_id = array();
			}

			if (isset($cancel) && $cancel == 1) {

				$sketch = $this->ProjectSketch->find("first", array("conditions" => array("ProjectSketch.is_edit_mode" => 1, "ProjectSketch.edit_user_id" => $this->user_id, "ProjectSketch.id" => $sketch_id)));

				if (isset($sketch) && isset($sketch['ProjectSketch']['locked']) && $sketch['ProjectSketch']['locked'] == 0) {

					if (isset($user_id) && empty($user_id)) {

						$this->ProjectSketch->updateAll(array("ProjectSketch.updated_by" => $sketch['ProjectSketch']['edit_user_id'], "ProjectSketch.modified" => "'" . date("Y-m-d h:i:s") . "'", "ProjectSketch.is_edit_mode" => 0, "ProjectSketch.edit_user_id" => $this->user_id), array("ProjectSketch.id" => $sketch_id));

						$this->ProjectSketchInterest->deleteAll(array("ProjectSketchInterest.project_id" => $project_id, "ProjectSketchInterest.sketch_id" => $sketch_id, "ProjectSketchInterest.user_id" => $this->user_id));
					} else if (isset($user_id[0]) && !empty($user_id[0])) {

						$this->ProjectSketch->updateAll(array("ProjectSketch.updated_by" => $sketch['ProjectSketch']['edit_user_id'], "ProjectSketch.modified" => "'" . date("Y-m-d h:i:s") . "'", "ProjectSketch.is_edit_mode" => 1, "ProjectSketch.edit_user_id" => $user_id[0]), array("ProjectSketch.id" => $sketch_id));

						$this->ProjectSketchInterest->deleteAll(array("ProjectSketchInterest.project_id" => $project_id, "ProjectSketchInterest.sketch_id" => $sketch_id, "ProjectSketchInterest.user_id" => $user_id[0]));

						//pr($user_id,1);
						if (isset($users[0]['ProjectSketchInterest']['user_id']) && !empty($users[0]['ProjectSketchInterest']['user_id'])) {
							$user_data = $this->User->findById($users[0]['ProjectSketchInterest']['user_id']);
							//pr($user_data,1);
							$sendMail = $this->CommonEmail->sendEmailEditSketchRequest($user_data, $sketch);
						}
					}
				}
			}

			$this->ProjectSketch->_query();

			$dir = $this->get_sketch_path($project_id, $sketch_id);
			$filename = "data.json";
			$filepath = $dir . '/' . $filename;

			$written_bytes = file_put_contents($filepath, json_encode($data));

			$success = $written_bytes > 0 ? true : false;
			$response = array(
				'success' => $success,
				'sketch_id' => $sketch_id,
				'msg' => 'Sketch has been updated successfully.',
			);
		}
		echo json_encode($response);
		exit();
	}

	public function crons() {
		$this->layout = false;
		$this->autoRender = false;
		$conditions['ProjectSketch']['is_edit_mode'] = 1;
		$conditions['ProjectSketch']['status'] = 1;

		$projectsketch = $this->ProjectSketch->find("all", array("conditions" => array("ProjectSketch.is_edit_mode" => 1, "ProjectSketch.status" => 1)));

		//pr($projectsketch, 1);
		if (isset($projectsketch) && !empty($projectsketch)) {
			foreach ($projectsketch as $ke => $sketch) {
				$project_id = $sketch['ProjectSketch']['project_id'];
				$sketch_id = $sketch['ProjectSketch']['id'];
				$users = $this->ProjectSketchInterest->find("all", array(
					"conditions" => array("ProjectSketchInterest.sketch_id" => $sketch_id),
					"order" => "ProjectSketchInterest.created ASC",
					"limit" => 1,
				)
				);
				if (isset($users) && !empty($users)) {
					$user_id = Hash::extract($users, '{n}.ProjectSketchInterest.user_id');
				} else {
					$user_id = array();
				}

				$currenttime = date("Y-m-d h:i:s");
				$lastupdatedtime = $sketch['ProjectSketch']['modified'];

				$datetime1 = strtotime($lastupdatedtime);
				$datetime2 = strtotime($currenttime);
				$interval = abs($datetime2 - $datetime1);
				$minutes = round($interval / 60);
				$this->set(compact("minutes"));

				// pr($sketch);
				if (isset($minutes) && !empty($minutes) && $sketch['ProjectSketch']['locked'] != 1 && $minutes >= 15) {
					//  echo $minutes . '<br>';die();
					if (isset($user_id) && empty($user_id)) {
						$this->ProjectSketch->updateAll(array("ProjectSketch.modified" => "'" . date("Y-m-d h:i:s") . "'", "ProjectSketch.is_edit_mode" => 0, "ProjectSketch.edit_user_id" => $this->user_id), array("ProjectSketch.id" => $sketch_id));
						$this->ProjectSketchInterest->deleteAll(array("ProjectSketchInterest.project_id" => $project_id, "ProjectSketchInterest.sketch_id" => $sketch_id, "ProjectSketchInterest.user_id" => $this->user_id));
					} else if (isset($user_id[0]) && !empty($user_id[0])) {
						$this->ProjectSketch->updateAll(array("ProjectSketch.modified" => "'" . date("Y-m-d h:i:s") . "'", "ProjectSketch.is_edit_mode" => 1, "ProjectSketch.edit_user_id" => $user_id[0]), array("ProjectSketch.id" => $sketch_id));
						$this->ProjectSketchInterest->deleteAll(array("ProjectSketchInterest.project_id" => $project_id, "ProjectSketchInterest.sketch_id" => $sketch_id, "ProjectSketchInterest.user_id" => $user_id[0]));

						if (isset($users[0]['ProjectSketchInterest']['user_id']) && !empty($users[0]['ProjectSketchInterest']['user_id'])) {
							$user_data = $this->User->findById($users[0]['ProjectSketchInterest']['user_id']);
							$sendMail = $this->CommonEmail->sendEmailEditSketchRequest($user_data, $sketch);
						}
					}
				}
			}
		}
	}

	public function update_save_as_sketch() {
		Configure::write('debug', 0);
		$this->layout = 'ajax';
		$sketch_id = !empty($this->request->data['sketch_id']) ? $this->request->data['sketch_id'] : false;
		$project_id = !empty($this->request->data['project_id']) ? $this->request->data['project_id'] : 0;
		if ($sketch_id && $project_id) {
			$old_sketch_data = $this->ProjectSketch->findById($sketch_id);
			$is_edit_mode = !empty($this->request->data['is_edit_mode']) ? $this->request->data['is_edit_mode'] : 1;
			$edit_user_id = !empty($this->request->data['edit_user_id']) ? $this->request->data['edit_user_id'] : $this->user_id;
			$participant_all = isset($old_sketch_data['ProjectSketch']['participant_all']) && !empty($old_sketch_data['ProjectSketch']['participant_all']) ? 1 : 0;

			$content = !empty($this->request->data['content']) ? $this->request->data['content'] : false;

			if ($content) {
				$canvas_data = !empty($this->request->data['canvas_data']) ? $this->request->data['canvas_data'] : '';
				$images_data = !empty($this->request->data['images_data']) ? $this->request->data['images_data'] : '';
				$config = !empty($this->request->data['config']) ? $this->request->data['config'] : '';
			} else {
				$content = '';
				$canvas_data = '';
				$images_data = '';
				$config = '';
			}
			if ($old_sketch_data['ProjectSketch']['parent_id'] != 0) {
				$parent_id = $old_sketch_data['ProjectSketch']['parent_id'];
			} else {
				$parent_id = $sketch_id;
			}
			$data = array(
				'parent_id' => intval($parent_id),
				//'sketch_title' => $old_sketch_data['ProjectSketch']['sketch_title'],
				//'sketch_description' => $old_sketch_data['ProjectSketch']['sketch_description'],
				'user_id' => intval($this->Session->read("Auth.User.id")),
				'project_id' => intval($project_id),
				'edit_user_id' => intval($edit_user_id),
				'is_edit_mode' => $is_edit_mode,
				'content' => $content,
				'canvas_data' => $canvas_data,
				'images_data' => $images_data,
				'config' => $config,
				'participant_all' => $participant_all,
				'modified' => date('Y-m-d h:i:s'),
			);
			if ($this->ProjectSketch->save($data)) {
				$data['sketch_id'] = $this->ProjectSketch->getLastInsertId();

				$users = $this->ProjectSketchParticipant->find("all", array(
					"conditions" => array("ProjectSketchParticipant.project_sketch_id" => $sketch_id),
				)
				);
				$sketch_data = $this->ProjectSketch->findById($data['sketch_id']);
				//pr($users,1);
				if (isset($users) && !empty($users)) {
					foreach ($users as $k => $val) {

						if ($val['ProjectSketchParticipant']['user_id'] === $this->user_id) {
							$this->request->data['ProjectSketchParticipant']['created_user_id'] = $this->user_id;
						} else {
							unset($this->request->data['ProjectSketchParticipant']['created_user_id']);
						}
						$this->request->data['ProjectSketchParticipant']['id'] = null;
						$this->request->data['ProjectSketchParticipant']['project_id'] = $project_id;
						$this->request->data['ProjectSketchParticipant']['send_email'] = 1;
						$this->request->data['ProjectSketchParticipant']['project_sketch_id'] = $data['sketch_id'];
						$this->request->data['ProjectSketchParticipant']['user_id'] = $val['ProjectSketchParticipant']['user_id'];

						$user_data = $this->User->findById($val['ProjectSketchParticipant']['user_id']);
						//$sendMail = $this->CommonEmail->sendEmailAddSketchParticipant($user_data, $sketch_data);
						//pr($user_data);
						$this->ProjectSketchParticipant->save($this->request->data['ProjectSketchParticipant']);
					}
				}
			}

			$this->ProjectSketch->_query();

			$dir = $this->get_sketch_path($project_id, $data['sketch_id']);

			$filename = "data.json";
			$filepath = ROOT . "/json/skts/" . $project_id . "/" . $data['sketch_id'] . "" . '/' . $filename;

			$written_bytes = file_put_contents($filepath, json_encode($data));

			$success = $written_bytes > 0 ? true : false;
			$response = array(
				'success' => $success,
				'sketch_id' => $data['sketch_id'],
				'msg' => 'Sketch has been successfully copied of old sketch.',
			);
		}
		echo json_encode($response);
		exit();
	}

	public function autosave_sketch() {
		$this->layout = 'ajax';
		Configure::write('debug', 0);
		$response = array(
			'success' => true,
			'data' => array(),
			'sketch_id' => '',
			'msg' => 'Sketch has been saved successfully.',
		);
		echo json_encode($response);
		exit();
	}

	public function get_sketch_path($project_id = 0, $sketch_id = 0, $path = false) {
		if (!$path) {
			$path = ROOT . "/json/skts/" . $project_id . "/" . $sketch_id . "";
		}

		if (is_dir($path)) {
			return $path;
		}

		$prev_path = substr($path, 0, strrpos($path, '/', -2) + 1);
		$return = $this->get_sketch_path($project_id, $sketch_id, $prev_path);

		return ($return && is_writable($prev_path)) ? mkdir($path) : false;

		return $path;
	}

}
