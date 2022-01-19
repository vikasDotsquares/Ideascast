<?php
/**
 * Application level View Helper
 *
 * This file is application-wide helper file. You can put all
 * application-wide helper-related methods here.
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Helper
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('Helper', 'View');

// App::uses('/Controller/Component', "Auth");

App::import("Model", "UserProject");
App::import("Model", "ProjectWorkspace");
App::import("Model", "Project");
App::import("Model", "Workspace");
App::import("Model", "Availability");
App::import("Model", "Area");
App::import("Model", "Element");
App::import("Model", "ElementDecision");

App::import("Model", "ProjectPermission");
App::import("Model", "WorkspacePermission");
App::import("Model", "ElementPermission");

App::import("Model", "ProjectPropagate");
App::import("Model", "WorkspacePropagate");
App::import("Model", "ElementPropagate");
App::import("Model", "ProjectSketche");
App::import("Model", "ShareElement");
App::import("Model", "SkillDetail");
App::uses('CommonComponent', 'Controller/Component');

/**
 * Application helper
 *
 * Add your application-wide methods in the class below, your helpers
 * will inherit them.
 *
 * @package       app.View.Helper
 */
class ViewModelHelper extends Helper {

	var $helpers = array('Html', 'Session','Common','Wiki');

	protected $_auth;
	protected $_user_projects;
	protected $_project_workspaces;
	protected $_projects;
	protected $_workspaces;
	protected $_availabilities;
	protected $_areas;
	protected $_elements;
	protected $_element_decisions;

	protected $_pr_permit;
	protected $_ws_permit;
	protected $_el_permit;

	protected $_pr_prop;
	protected $_ws_prop;
	protected $_el_prop;


	public function __construct(View $View, $settings = array()) {

		parent::__construct($View, $settings);

		$this->_user_projects = new UserProject();
		$this->_project_workspaces = new ProjectWorkspace();
		$this->_projects = new Project();
		$this->_workspaces = new Workspace();
		$this->_availabilities = new Availability();
		$this->_areas = new Area();
		$this->_elements = new Element();
		$this->_element_decisions = new ElementDecision();

		$this->_pr_permit = new ProjectPermission();
		$this->_ws_permit = new WorkspacePermission();
		$this->_el_permit = new ElementPermission();

		$this->_pr_prop = new ProjectPropagate();
		$this->_ws_prop = new WorkspacePropagate();
		$this->_el_prop = new ElementPropagate();

	}

	// Get detail of a user
	public function get_user($user_id = null, $unbind = null, $recursive = -1) {

		App::import("Model", "User");

		$user = new User();

		if (!$user_id) {
			return;
		}

		$user_detail = null;

		if (isset($user_id) && !empty($user_id)) {
			// pr($user, 1);
			if (isset($unbind) && !empty($unbind) && is_array($unbind)) {
				$user->unbindModel($unbind, true);
			}

			$user_detail = $user->find('first', ['conditions' => ['User.id' => $user_id], 'recursive' => $recursive]);

		}

		return $user_detail;

	}

	// Get detail of a user
	public function get_users($recursive = -1) {

		App::import("Model", "UserDetail");

		$user = new UserDetail();

		$user_detail = null;

		$user_detail = $user->find('all', ['recursive' => $recursive]);

		return $user_detail;

	}

	// Get detail of a user
	public function get_user_data($user_id = null, $recursive = -1, $fld = null) {

		App::import("Model", "UserDetail");

		$user = new UserDetail();

		if (!$user_id) {
			return;
		}

		$user_detail = false;

		if (!empty($fld) && $fld == 'taskcenter') {
			$user_detail = $user->find('first', ['conditions' => ['UserDetail.user_id' => $user_id], 'recursive' => $recursive, 'fields' => array('UserDetail.first_name', 'UserDetail.last_name', 'UserDetail.profile_pic', 'UserDetail.job_title', 'UserDetail.organization_id')]);
		} else {
			$user_detail = $user->find('first', ['conditions' => ['UserDetail.user_id' => $user_id], 'recursive' => $recursive]);
		}

		return $user_detail;

	}

	// Get projects of loggedin user
	public function getDecisionDetail($decision_id = null, $el_id = null) {

		App::import("Model", "ElementDecision");
		App::import("Model", "ElementDecisionDetail");
		App::import("Model", "Decision");

		$element_decisions = new ElementDecision();
		$element_decisions_detail = new ElementDecisionDetail();
		$decisions = new Decision();

		if (!$decision_id) {
			return;
		}

		$el_decisions = null;

		if (isset($el_id) && !empty($el_id)) {
			$el_decisions = $element_decisions_detail->query("SELECT * FROM `element_decision_details` WHERE `element_decision_id` IN ( select id from element_decisions where element_id = '" . $el_id . "' ) and `decision_id` = '" . $decision_id . "' LIMIT 1");
			if (isset($el_decisions) && !empty($el_decisions)) {
				foreach ($el_decisions as $k => $v) {
					// echo '## '.$v['element_decision_details']['stage_status'];
				}
			}

		}

		return $el_decisions;

	}

	// Get projects of loggedin user
	public function getElementDecision($el_id = null, $fld = 'id') {
		App::import("Model", "ElementDecision");
		App::import("Model", "Element");

		$element = new ElementDecision();

		if (!$el_id) {
			return;
		}

		$el_decision_value = null;

		$el_decision = $element->find('first', [
			'conditions' => [
				'ElementDecision.element_id' => $el_id],
			'recursive' => -1,
		]);
		// echo $element->_query();
		// pr($el_decision );
		if (isset($el_decision) && !empty($el_decision)) {
			$el_decision_value = $el_decision['ElementDecision'][$fld];
		}
		return $el_decision_value;

	}

	// Get projects of loggedin user
	public function getElementDecisionStatus($id = null) {
		App::import("Model", "ElementDecisionDetail");

		$element = new ElementDecisionDetail();

		$stage_status = null;

		if (isset($id) && !empty($id)) {
			$stage_status = $element->find('first', [
				'conditions' => [
					'ElementDecisionDetail.id' => $id,
					'ElementDecisionDetail.stage_status IS NOT NULL',
					'ElementDecisionDetail.stage_status >' => 0,
				],
				'recursive' => -1,
			]);
			if (isset($stage_status) && !empty($stage_status) && $stage_status > 1) {
				// e($id);
				// pr($stage_status);
				return true;
			}
		}
		return false;

	}

	// Count workspace elements detail like decisions, links, mms etc.
	public function ws_el_detail_count($element_detail = null, $ws_id = null, $count = false) {

		if (empty($element_detail)) {
			return null;
		}

		$data = ['decisions' => 0, 'feedbacks' => 0, 'overdues' => 0, 'links' => 0, 'documents' => 0, 'mindmaps' => 0, 'notes' => 0, 'votes' => 0];
		$filter = $data = null;
		if (is_array($element_detail)) {

			$filter = arraySearch($element_detail, 'docs');
			if (!empty($filter)) {
				$data[$ws_id] = $filter[0];
				$value = array_sum(array_column($filter, 'docs'));
				// pr($element_detail );
				// pr($value, 1);
			}
			// foreach($element_detail as $key => $val ) {
			// echo $val['id'].'<br>';
			//
			// $arraySearch = arraySearch($val, 'docs', $val['id']);
			// if( isset($arraySearch) && !empty($arraySearch) ) {
			// $data['notes'] += $data['notes'];

			// }
			// }

		}

		return $data;
	}

	// Get count of workspaces, areas and elements of a project
	public function getProjectWorkspaces($project_id = null, $recursive = 2) {

		$user_id = $this->Session->read('Auth.User.id');
		if (!$user_id) {
			return;
		}

		// $data = $this->_project_workspaces->find('all', ['recursive' => $recursive, 'conditions' => ['ProjectWorkspace.project_id' => $project_id], 'order' => 'ProjectWorkspace.sort_order ASC']);
		$this->_project_workspaces->unbindModel(['hasMany' => 'WorkspacePermission']);
		$this->_project_workspaces->unbindModel(['belongsTo' => 'Project']);

		$data = $this->_project_workspaces->find('all', ['recursive' => $recursive, 'conditions' => ['ProjectWorkspace.project_id' => $project_id], 'order' => 'ProjectWorkspace.sort_order ASC']);
		// pr($data, 1);
		return $data;
	}

	// Get count of workspaces, areas and elements of a project
	public function projectWorkspaces($project_id = null, $perPageWspLimit = 3, $currentWspPage = 0) {

		$user_id = $this->Session->read('Auth.User.id');
		if (!$user_id) {
			return;
		}

		$query = "SELECT * FROM project_workspaces as ProjectWorkspace LEFT JOIN workspaces as Workspace ON Workspace.id = ProjectWorkspace.workspace_id WHERE ProjectWorkspace.project_id = '" . $project_id . "' AND Workspace.studio_status != 1 ORDER BY sort_order ASC LIMIT " . $currentWspPage . ", " . $perPageWspLimit . "";
		$data = $this->_project_workspaces->query($query);

		return $data;
	}

	public function getLastProjectSummary() {

		$user_id = $this->Session->read('Auth.User.id');

		if (!$user_id) {
			return;
		}

		$data = $this->_user_projects->find('first', ['recursive' => 1, 'conditions' => ['UserProject.user_id' => $user_id], 'order' => 'UserProject.modified DESC']);

		return $data;
	}

	public function _substr_text($string = null, $max_chars = 10) {

		if (empty($string)) {
			return;
		}

		return (strlen($string) > $max_chars) ?
		substr($string, 0, $max_chars) . '...' :
		$string;
	}

	/**
	 * Truncates text.
	 *
	 * Cuts a string to the length of $length and replaces the last characters
	 * with the ending if the text is longer than length.
	 *
	 * ### Options:
	 *
	 * - `ending` Will be used as Ending and appended to the trimmed string
	 * - `exact` If false, $text will not be cut mid-word
	 * - `html` If true, HTML tags would be handled correctly
	 *
	 * @param string  $text String to truncate.
	 * @param integer $length Length of returned string, including ellipsis.
	 * @param array $options An array of html attributes and options.
	 * @return string Trimmed string.
	 * @access public
	 * @link http://book.cakephp.org/view/1469/Text#truncate-1625
	 */
	public function _substr($text, $length = 80, $options = array()) {
		$default = array(
			'ending' => '...', 'exact' => true, 'html' => false,
		);
		$options = array_merge($default, $options);
		extract($options);

		if ($html) {
			if (mb_strlen(preg_replace('/<.*?>/', '', $text)) <= $length) {
				return $text;
			}
			$totalLength = mb_strlen(strip_tags($ending));
			$openTags = array();
			$truncate = '';

			preg_match_all('/(<\/?([\w+]+)[^>]*>)?([^<>]*)/', $text, $tags, PREG_SET_ORDER);
			foreach ($tags as $tag) {
				if (!preg_match('/img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param/s', $tag[2])) {
					if (preg_match('/<[\w]+[^>]*>/s', $tag[0])) {
						array_unshift($openTags, $tag[2]);
					} else if (preg_match('/<\/([\w]+)[^>]*>/s', $tag[0], $closeTag)) {
						$pos = array_search($closeTag[1], $openTags);
						if ($pos !== false) {
							array_splice($openTags, $pos, 1);
						}
					}
				}
				$truncate .= $tag[1];

				$contentLength = mb_strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', ' ', $tag[3]));
				if ($contentLength + $totalLength > $length) {
					$left = $length - $totalLength;
					$entitiesLength = 0;
					if (preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', $tag[3], $entities, PREG_OFFSET_CAPTURE)) {
						foreach ($entities[0] as $entity) {
							if ($entity[1] + 1 - $entitiesLength <= $left) {
								$left--;
								$entitiesLength += mb_strlen($entity[0]);
							} else {
								break;
							}
						}
					}

					$truncate .= mb_substr($tag[3], 0, $left + $entitiesLength);
					break;
				} else {
					$truncate .= $tag[3];
					$totalLength += $contentLength;
				}
				if ($totalLength >= $length) {
					break;
				}
			}
		} else {
			if (mb_strlen($text) <= $length) {
				return $text;
			} else {
				$truncate = mb_substr($text, 0, $length - mb_strlen($ending));
			}
		}
		if (!$exact) {
			$spacepos = mb_strrpos($truncate, ' ');
			if (isset($spacepos)) {
				if ($html) {
					$bits = mb_substr($truncate, $spacepos);
					preg_match_all('/<\/([a-z]+)>/', $bits, $droppedTags, PREG_SET_ORDER);
					if (!empty($droppedTags)) {
						foreach ($droppedTags as $closingTag) {
							if (!in_array($closingTag[1], $openTags)) {
								array_unshift($openTags, $closingTag[1]);
							}
						}
					}
				}
				$truncate = mb_substr($truncate, 0, $spacepos);
			}
		}
		$truncate .= $ending;

		if ($html) {
			foreach ($openTags as $tag) {
				$truncate .= '</' . $tag . '>';
			}
		}

		return $truncate;
	}

	// Get list of workspaces to show on left bar
	public function getSideBarWS($project_id = null) {
		$workspaces = null;
		if (isset($project_id) && !empty($project_id)) {

			$workspaces = ClassRegistry::init('Workspace')->ProjectWorkspace->find('all', array('conditions' => ['ProjectWorkspace.project_id' => $project_id, 'ProjectWorkspace.leftbar_status' => 1], 'recursive' => 1, 'fields' => array('ProjectWorkspace.id', 'Workspace.id', 'Workspace.title', 'Workspace.studio_status', 'ProjectWorkspace.workspace_id', 'ProjectWorkspace.project_id'), 'order' => array('ProjectWorkspace.sort_order ASC')));

		}

// pr($workspaces, 1);
		return isset($workspaces) ? $workspaces : '';
	}

	// PHP sprintf for array values
	public function sprintf_assoc($string = '', $replacement_vars = array(), $prefix_character = '%') {
		if (!$string) {
			return '';
		}

		if (is_array($replacement_vars) && !empty($replacement_vars) && count($replacement_vars) > 0) {
			foreach ($replacement_vars as $key => $value) {
				$string = str_replace($prefix_character . $key, $value, $string);
			}
		}
		return $string;
	}

	public function sanitize_html($string = '') {

		if (!$string) {
			return '';
		}

		App::uses('Sanitize', 'Utility');
		$string = Sanitize::html($string);
		// $string = Sanitize::stripTags( $string, 'b', 'p', 'div');
		return $string;
	}

	public function countWSArea($ws_id = null) {
		App::import("Model", "ProjectWorkspace");
		App::import("Model", "Workspace");
		App::import("Model", "Area");
		$model_pw = new ProjectWorkspace();
		$model_ws = new Workspace();
		$model_area = new Area();

		$data = $model_pw->Workspace->Area->find('count', ['recursive' => 2, 'conditions' => ['Area.workspace_id' => $ws_id],
			'fields' => ['Area.id', 'Area.title', 'Area.workspace_id']]);
		echo $model_area->_query();

		// pr($data, 1);
	}

	// Get list of workspaces to show on left bar
	public function countAreaElements($ws_id = null, $areaID = null, $elemID = null, $passed_project_id = null, $passed_user_project_id = null, $passed_user_project = null, $passed_pr_permission = null) {

		App::import("Model", "ElementLink");
		$el = new ElementLink();
		App::import("Model", "ElementDocument");
		$ed = new ElementDocument();
		App::import("Model", "ElementNote");
		$en = new ElementNote();
		App::import("Model", "Vote");
		$vo = new Vote();
		App::import("Model", "ElementMindmap");
		$em = new ElementMindmap();
		App::import("Model", "ElementDecision");
		$edc = new ElementDecision();
		App::import("Model", "ElementDecisionDetail");
		$edd = new ElementDecisionDetail();

		App::import("Model", "ElementFeedback");
		$ef = new ElementFeedback();
		App::import("Model", "ElementFeedbackDetail");
		$efd = new ElementFeedbackDetail();

		App::import("Model", "Feedback");
		$efN = new Feedback();

		$view = new View();
		$common = $view->loadHelper('Common');
		$group = $view->loadHelper('Group');

		$user_id = CakeSession::read("Auth.User.id");

		$element_count = $filled_area = 0;
		$area_id = $assets_count = $elIds = null;
		$votes = 0;
		$feedbacks = 0;
		$aaac_count = 0;


		if (isset($elemID) && !empty($elemID)) {

			$assets = $this->_elements->find('all', array('conditions' => ['Element.id' => $elemID, 'Element.studio_status !=' => 1], 'recursive' => 1));

			if (!empty($assets)) {
				$elIds = Set::extract($assets, '/Element/id');
				$count_condition = ['element_id' => $elIds];
				$total_decision_elements = $total_feedback_elements = 0;

				$links = $docs = $notes = $mindmaps = $due_status = $votes = $decisions = $feedbacks = 0;
				if ($el->hasAny($count_condition)) {
					$links = $el->find('count', array('conditions' => $count_condition));
				}

				if ($ed->hasAny($count_condition)) {
					$docs = $ed->find('count', array('conditions' => $count_condition));
				}

				if ($en->hasAny($count_condition)) {
					$notes = $en->find('count', array('conditions' => $count_condition));
				}

				if ($efN->hasAny($count_condition)) {
					$feedbacks = $efN->find('count', array('conditions' => ['element_id' => $elIds, 'sign_off !=' => 1, 'end_date >=' => date('Y-m-d 00:00:00')]));
				}

				if ($em->hasAny($count_condition)) {
					$mindmaps = $em->find('count', array('conditions' => $count_condition));
				}

				if ($vo->hasAny($count_condition)) {
					$votes = $vo->find('count', array('conditions' => ['element_id' => $elIds, 'is_completed !=' => 1, 'end_date >=' => date('Y-m-d 00:00:00'), 'VoteQuestion.id !=' => '']));
				}

				if ($efN->hasAny($count_condition)) {
					$feedbacks = $efN->find('count', array('conditions' => ['element_id' => $elIds, 'end_date >=' => date('Y-m-d 00:00:00')]));
				}

				if ($edc->hasAny($count_condition)) {
					//$dec = $edc->ElementDecisionDetail->find('all', array('conditions' => ['ElementDecision.element_id' => $elIds], 'recursive' => 1, 'fields' => ['ElementDecision.element_id', 'ElementDecision.title', 'ElementDecisionDetail.id', 'ElementDecisionDetail.element_decision_id', 'ElementDecisionDetail.decision_id']));
					$dec = $edc->find('count', array('conditions' => [
							array('OR' => array(

							'ElementDecision.sign_off !=' => 1,
							'ElementDecision.sign_off IS NULL'
							)),
							array('AND' => array(

							'ElementDecision.element_id' => $elIds,
							)),

					], 'recursive' => 1, 'fields' => ['ElementDecision.element_id', 'ElementDecision.title']));

					if (!empty($dec)) {
						/*
						$found = Set::extract($dec, '/ElementDecision/element_id');

						if (!empty($found)) {
							$found1 = array_unique($found);
							if (!empty($found1)) {
								$total_decision_elements = count($found1);
							}
						} */

						$total_decision_elements = $dec;
					}
				}
				if ($ef->hasAny($count_condition)) {
					$fed = $ef->ElementFeedbackDetail->find('all', array('conditions' => ['ElementFeedback.element_id' => $elIds], 'recursive' => 1, 'fields' => ['ElementFeedback.element_id', 'ElementFeedback.title', 'ElementFeedbackDetail.id', 'ElementFeedbackDetail.element_feedback_id', 'ElementFeedbackDetail.feedback_id']));

					if (!empty($fed)) {
						$found_fed = Set::extract($fed, '/ElementFeedback/element_id');
						if (!empty($found_fed)) {
							$found_fed1 = array_unique($found_fed);
							if (!empty($found_fed1)) {
								$total_feedback_elements = count($found_fed1);
							}
						}
					}
				}

				$assets_count = [
					'docs' => $docs,
					'links' => $links,
					'notes' => $notes,
					'mindmaps' => $mindmaps,
					'due_status' => $due_status,
					'votes' => $votes,
					'decisions' => (!empty($total_decision_elements)) ? $total_decision_elements : 0,
					'feedbacks' => (!empty($feedbacks)) ? $feedbacks : 0,
				];

				return $return = ['assets_count' => $assets_count];
			}

		} elseif (isset($areaID) && !empty($areaID)) {
			$element_count = $this->_elements->find('count', array('conditions' => ['Element.area_id' => $areaID], 'recursive' => 1));

			if ($element_count > 0) {

				$assets = $this->_elements->find('all', array('conditions' => ['Element.area_id' => $areaID], 'recursive' => 1));

				if (!empty($assets)) {
					$elIds = Set::extract($assets, '/Element/id');
					$count_condition = ['element_id' => $elIds];

					$links = $docs = $notes = 0;
					if ($el->hasAny($count_condition)) {
						$links = $el->find('count', array('conditions' => $count_condition));
					}

					if ($ed->hasAny($count_condition)) {
						$docs = $ed->find('count', array('conditions' => $count_condition));
					}

					if ($en->hasAny($count_condition)) {
						$notes = $en->find('count', array('conditions' => $count_condition));
					}

					if ($vo->hasAny($count_condition)) {
						$votes = $vo->find('count', array('conditions' => ['element_id' => $elIds, 'is_completed !=' => 1, 'end_date >=' => date('Y-m-d 00:00:00'), 'VoteQuestion.id !=' => '']));
					}

					if ($efN->hasAny($count_condition)) {
						$feedbacks = $efN->find('count', array('conditions' => ['element_id' => $elIds, 'sign_off !=' => 1, 'end_date >=' => date('Y-m-d 00:00:00')]));
					}

					$assets_count = [
						'docs' => $docs,
						'links' => $links,
						'notes' => $notes,
						'votes' => $votes,
						'feedbacks' => $feedbacks,

					];

				}
			}
			return $return = ['element_count' => $element_count, 'assets_count' => $assets_count];
		} else if (isset($ws_id) && !empty($ws_id)) {

			if (isset($passed_project_id) && !empty($passed_project_id)) {
				$project_id['ProjectWorkspace']['project_id'] = $passed_project_id;
			} else {
				$project_id = $this->_project_workspaces->find('first', ['conditions' => ['ProjectWorkspace.workspace_id' => $ws_id], 'recursive' => -1, 'fields' => ['project_id']]);
			}

			$upid = null;

			if (isset($passed_user_project_id) && !empty($passed_user_project_id)) {
				$upid = $passed_user_project_id;
			} else {
				if (isset($project_id) && !empty($project_id)) {
					$user_project_id = $this->_user_projects->find('first', ['conditions' => ['UserProject.project_id' => $project_id['ProjectWorkspace']['project_id']], 'recursive' => -1, 'fields' => ['UserProject.id']]);
				}
				if (isset($user_project_id) && !empty($user_project_id)) {
					$upid = $user_project_id['UserProject']['id'];
				}
			}

			$assets_count = [
				'docs' => 0,
				'links' => 0,
				'notes' => 0,
				'mindmaps' => 0,
				'due_status' => 0,
				'votes' => 0,
				'decisions' => 0,
				'feedbacks' => 0,
			];

			$areas = $this->_areas->find('all', array('conditions' => ['Area.workspace_id' => $ws_id], 'fields' => ['id', 'title']));

			//pr($areas);

			$aaac_count = ( isset($areas) && !empty($areas) ) ? count($areas) : 0;

			$area_id = $this->workspace_areas($ws_id, false, true);

			if (isset($area_id) && !empty($area_id)) {
				foreach ($area_id as $k => $v) {

					if ($this->_elements->hasAny(['Element.area_id' => $v, 'Element.studio_status <>' => 1, 'Element.status <>' => 3])) {

						$filled_area += 1;

					}
				}
			}

			$ws_permission = $common->element_permission_details($ws_id, $project_id['ProjectWorkspace']['project_id'], $user_id);

			if (isset($passed_user_project) && !empty($passed_user_project)) {
				$us_permission = $passed_user_project;
			} else {
				$us_permission = $common->userproject($project_id['ProjectWorkspace']['project_id'], $user_id);
			}

			if (isset($passed_pr_permission) && !empty($passed_pr_permission)) {
				$pr_permission = $passed_pr_permission;
			} else {
				$pr_permission = $common->project_permission_details($project_id['ProjectWorkspace']['project_id'], $user_id);
			}

			$grp_id = $group->GroupIDbyUserID($project_id['ProjectWorkspace']['project_id'], $user_id);

			if (isset($grp_id) && !empty($grp_id)) {

				$group_permission = $group->group_permission_details($project_id['ProjectWorkspace']['project_id'], $grp_id);

				if (isset($group_permission['ProjectPermission']['project_level']) && $group_permission['ProjectPermission']['project_level'] == 1) {
					$project_level = $group_permission['ProjectPermission']['project_level'];
				}

				$ws_permission = $group->group_element_permission_details($ws_id, $project_id['ProjectWorkspace']['project_id'], $grp_id);

				//
			}

			if ((!empty($us_permission)) || (isset($pr_permission['ProjectPermission']['project_level']) && $pr_permission['ProjectPermission']['project_level'] == 1) || (isset($project_level) && $project_level == 1) || (!empty($ws_permission))) {

				if ((!empty($us_permission)) || (isset($pr_permission['ProjectPermission']['project_level']) && $pr_permission['ProjectPermission']['project_level'] == 1) || (isset($project_level) && $project_level == 1)) {
					$element_count = $this->_elements->find('count', array('conditions' => ['Element.area_id' => $area_id, 'Element.studio_status <>' => 1], 'recursive' => -1));

				} else if (!empty($ws_permission)) {

					$element_count = $this->_elements->find('count', array('conditions' => ['Element.area_id' => $area_id, 'Element.studio_status <>' => 1, 'Element.id' => $ws_permission], 'recursive' => -1));
				}

			}

			if ($element_count > 0) {
				$assets = null;
				if ((!empty($us_permission)) || (isset($pr_permission['ProjectPermission']['project_level']) && $pr_permission['ProjectPermission']['project_level'] == 1) || (isset($project_level) && $project_level == 1) || (!empty($ws_permission))) {

					if ((!empty($us_permission)) || (isset($pr_permission['ProjectPermission']['project_level']) && $pr_permission['ProjectPermission']['project_level'] == 1) || (isset($project_level) && $project_level == 1)) {
						$assets = $this->_elements->find('all', array('conditions' => ['Element.area_id' => $area_id, 'Element.studio_status <>' => 1], 'recursive' => 1));
					} else if (!empty($ws_permission)) {

						$assets = $this->_elements->find('all', array('conditions' => ['Element.area_id' => $area_id, 'Element.studio_status <>' => 1, 'Element.id' => $ws_permission], 'recursive' => 1));

						//$assets = $this->_elements->find('all',array('conditions'=> ['Element.area_id' => $area_id, 'Element.id' => $ws_permission  ], 'recursive' => 1 ,'fields'=>array('DISTINCT Element.id,Area.id,ElementDecision.id','Element.*','Area.*','ElementDecision.*','ElementFeedback.*' )) );
						//pr($ws_permission);
						//pr($assets,1);
					}

				}

				if (!empty($assets)) {
					$elIds = Set::extract($assets, '/Element/id');
					// pr($elIds, 1);
					$count_condition = ['element_id' => $elIds];
					$total_decision_elements = $total_feedback_elements = 0;

					$links = $docs = $notes = $mindmaps = $due_status = $votes = $decisions = $feedbacks = 0;
					if ($el->hasAny($count_condition)) {
						$links = $el->find('count', array('conditions' => $count_condition));
					}

					if ($ed->hasAny($count_condition)) {
						$docs = $ed->find('count', array('conditions' => $count_condition));
					}

					if ($en->hasAny($count_condition)) {
						$notes = $en->find('count', array('conditions' => $count_condition));
					}

					if ($em->hasAny($count_condition)) {
						$mindmaps = $em->find('count', array('conditions' => $count_condition));
					}

					if ($vo->hasAny($count_condition)) {
						$votes = $vo->find('count', array('conditions' => ['element_id' => $elIds, 'is_completed !=' => 1, 'end_date >=' => date('Y-m-d 00:00:00'), 'VoteQuestion.id !=' => '']));
					}

					if ($efN->hasAny($count_condition)) {
						$feedbacks = $efN->find('count', array('conditions' => ['element_id' => $elIds, 'sign_off !=' => 1, 'end_date >=' => date('Y-m-d 00:00:00')]));
					}

					// pr($feedbacks);

					if ($edc->hasAny($count_condition)) {
					//$dec = $edc->ElementDecisionDetail->find('all', array('conditions' => ['ElementDecision.element_id' => $elIds], 'recursive' => 1, 'fields' => ['ElementDecision.element_id', 'ElementDecision.title', 'ElementDecisionDetail.id', 'ElementDecisionDetail.element_decision_id', 'ElementDecisionDetail.decision_id']));
					$dec = $edc->find('count', array('conditions' => [
							array('OR' => array(

							'ElementDecision.sign_off !=' => 1,
							'ElementDecision.sign_off IS NULL'
							)),
							array('AND' => array(

							'ElementDecision.element_id' => $elIds,
							)),

					], 'recursive' => 1, 'fields' => ['ElementDecision.element_id', 'ElementDecision.title']));

					if (!empty($dec)) {
						/*
						$found = Set::extract($dec, '/ElementDecision/element_id');

						if (!empty($found)) {
							$found1 = array_unique($found);
							if (!empty($found1)) {
								$total_decision_elements = count($found1);
							}
						} */

						$total_decision_elements = $dec;
					}
				}
					if ($ef->hasAny($count_condition)) {
						$fed = $ef->ElementFeedbackDetail->find('all', array('conditions' => ['ElementFeedback.element_id' => $elIds], 'recursive' => 1, 'fields' => ['ElementFeedback.element_id', 'ElementFeedback.title', 'ElementFeedbackDetail.id', 'ElementFeedbackDetail.element_feedback_id', 'ElementFeedbackDetail.feedback_id']));

						if (!empty($fed)) {
							$found_fed = Set::extract($fed, '/ElementFeedback/element_id');
							if (!empty($found_fed)) {
								$found_fed1 = array_unique($found_fed);
								if (!empty($found_fed1)) {
									$total_feedback_elements = count($found_fed1);
								}
							}
						}
					}

					$assets_count = [
						'docs' => $docs,
						'links' => $links,
						'notes' => $notes,
						'mindmaps' => $mindmaps,
						'due_status' => $due_status,
						'votes' => $votes,
						'decisions' => (!empty($total_decision_elements)) ? $total_decision_elements : 0,
						'feedbacks' => (!empty($feedbacks)) ? $feedbacks : 0,
					];

				}
			}

		}

		$overdue_element_count = _element_overdue_status_count($elIds);

		$return = ['area_count' => $aaac_count, 'area_used' => $filled_area, 'active_element_count' => $element_count, 'assets_count' => $assets_count, 'overdue_element_count' => $overdue_element_count];

		return $return;
	}

	public function user_project_count() {
		$user_id = $this->Session->read('Auth.User.id');
		$project_count = false;
		if (!empty($user_id) && $this->_user_projects->hasAny(['UserProject.user_id' => $user_id])) {
			$project_count = $this->_user_projects->find('count', ['conditions' => ['UserProject.user_id' => $user_id]]);
		}
		return $project_count;
		// pr($project_count, 1);
	}

	public function project_workspace_count($project_id = null) {
		$ws_count = false;
		App::import("Model", "UserPermission");
		$UserPermission = new UserPermission();
		if (!empty($project_id) && $this->_project_workspaces->hasAny(['ProjectWorkspace.project_id' => $project_id])) {
		$ws_count = $UserPermission->find('count',
                  array('conditions'=>
                        array('UserPermission.project_id'=>$project_id,
                              'UserPermission.area_id IS NULL',
                              'UserPermission.user_id'=>$this->Session->read('Auth.User.id'),
                              "UserPermission.workspace_id !="=>''
                    )
                )
            );
		// pr($dd);
			// $ws_count = $this->_project_workspaces->find('count', ['conditions' => ['ProjectWorkspace.project_id' => $project_id]]);
		}
		return $ws_count;
	}

	public function get_project_workspace($project_id = null, $studio_status = 0) {
		$ws = null;

		if (!empty($project_id) && $this->_project_workspaces->hasAny(['ProjectWorkspace.project_id' => $project_id])) {

			$this->_project_workspaces->Behaviors->load('Containable');

			$cond['ProjectWorkspace.project_id'] = $project_id;
			$cond['Workspace.id !='] = '';
			$cond['Workspace.title !='] = '';

			if (isset($studio_status) && !empty($studio_status)) {
				//$cond['Workspace.studio_status'] = $studio_status;
			} else {
				$cond['Workspace.studio_status'] = $studio_status;
			}

			$ws = $this->_project_workspaces->find('all', ['conditions' => $cond, 'contain' => 'Workspace']);

		}

		return $ws;
	}

	public function workspace_pwid($workspace_id = null) {
		$wpwid = null;
		if (!empty($workspace_id) && $this->_project_workspaces->hasAny(['ProjectWorkspace.workspace_id' => $workspace_id])) {
			$ws = $this->_project_workspaces->find('first', ['conditions' => ['ProjectWorkspace.workspace_id' => $workspace_id], 'recursive' => -1, 'fields' => ['ProjectWorkspace.id']]);
			$wpwid = (!empty($ws)) ? $ws['ProjectWorkspace']['id'] : null;
		}
		return $wpwid;
	}

	public function project_workspaces($project_id = null, $count_workapces = false, $dataOptions = 0, $passed_workspace = null) {

		$user_id = $this->Session->read('Auth.User.id'); //Auth::user('id');
		$list = null;

		$propagatePermission = $ownerUser = 0;
		if (isset($dataOptions) && !empty($dataOptions)) {
			$ownerUser = $dataOptions['ownerUser'];
			$propagatePermission = $dataOptions['propagatePermission'];
		}

		// CALL ANOTHER FUNCTION FOR SELECT PROJECT DATA ACCORDING TO THE GIVEN PROPAGATE PERMISSIONS
		if ($propagatePermission == 1 && $ownerUser == 1) {

		}

		#----------- check sharing permissions -----------
		if (has_permissions($project_id)) {
			$conditions['UserProject.user_id'] = $user_id;
		}
		#----------- check sharing permissions -----------

		if (isset($project_id) && !empty($project_id)) {
			$conditions['UserProject.project_id'] = $project_id;
		}

		// current users projects
		if ($this->_user_projects->hasAny($conditions)) {

			$projects = $this->_user_projects->find('list', ['conditions' => $conditions, 'fields' => ['project_id']]);

			// if these projects has workspaces
			if (!empty($projects) && $this->_project_workspaces->hasAny(['ProjectWorkspace.project_id' => $projects])) {

				$project_workspaces = $this->_project_workspaces->find('list', ['conditions' => ['ProjectWorkspace.project_id' => $projects], 'fields' => ['workspace_id']]);

				// if returned workspaces has any areas
				if (!empty($project_workspaces) && $this->_areas->hasAny(['Area.workspace_id' => $project_workspaces])) {
					// Get all projects id and title
					$project_list = $this->_projects->find('list', ['conditions' => ['Project.id' => array_values($projects)], 'fields' => ['id', 'title']]);

					foreach ($project_list as $pid => $ptitle) {



						// Get all workspaces of the project
						$project_workspace = $this->_workspaces->ProjectWorkspace->find('list', ['conditions' => ['ProjectWorkspace.project_id' => $pid], 'fields' => ['ProjectWorkspace.workspace_id', 'ProjectWorkspace.sort_order'], 'order' => ['ProjectWorkspace.sort_order ASC']]);

						 if( isset($project_workspace) && !empty($project_workspace) ){

							$list[$pid]['project'] = ['id' => $pid, 'title' => $ptitle];

							foreach ($project_workspace as $wid => $sorted) {
								if ($this->_workspaces->exists($wid)) {
									$q = "SELECT title, start_date, end_date, studio_status, sign_off FROM workspaces WHERE id = '" . $wid . "' AND studio_status != 1 ";
									if(isset($passed_workspace) && !empty($passed_workspace)){
										$q = "SELECT title, start_date, end_date, studio_status, sign_off FROM workspaces WHERE id = '" . $wid . "' AND studio_status != 1 AND id != '".$passed_workspace."'";
									}
									$workspaces = $this->_workspaces->query($q );
									if(isset($workspaces) && !empty($workspaces)){
										$wtitle = Set::extract($workspaces, '/workspaces/title');
										$sdate = Set::extract($workspaces, '/workspaces/start_date');
										$edate = Set::extract($workspaces, '/workspaces/end_date');
										$studio_status = Set::extract($workspaces, '/workspaces/studio_status');
										$sign_off = Set::extract($workspaces, '/workspaces/sign_off');

										$wtitle = (isset($wtitle[0])) ? $wtitle[0] : 'N/A';
										$sign_off = (isset($sign_off[0])) ? $sign_off[0] : 'N/A';

										$list[$pid]['workspace'][$wid] = ['id' => $wid, 'title' => $wtitle, 'sdate' => $sdate, 'edate' => $edate, 'sign_off' => $sign_off ];

										// Get all area id and title
										$area_list = $this->_areas->find('list', ['conditions' => ['Area.workspace_id' => $wid], 'fields' => ['id', 'title'], 'order' => 'Area.id']);

										$list[$pid]['workspace'][$wid]['area'] = $area_list;
									}
								}
							}
						 }
					}
				}
			}
		}

		return $list;
	}

	public function workspace_areas($workspace_id = null, $count = false, $ids = false, $sort = false) {

		$workspaces = $area_id = null;

		$find_type = ($count) ? 'count' : 'all';
		$order = ($sort) ? ['Area.template_detail_id ASC'] : ['Area.sort_order ASC'];

		$workspace_data = $this->_workspaces->Area->find($find_type,
			array('conditions' => [
				'Area.workspace_id' => $workspace_id,
			//	'Area.studio_status !=' => 1,
			],
				'recursive' => '-1',
				'fields' => array('Area.id', 'Area.workspace_id', 'Area.title'),
				'order' => $order,
			));

		if ($count) {
			return $workspace_data;
		} else if (!$count && $ids) {
			if (isset($workspace_data) && !empty($workspace_data)) {
				$area_id = Set::extract($workspace_data, '/Area/id');
			}
			return $area_id;
		} else {
			return isset($workspace_data) ? $workspace_data : '';
		}
	}

	//created by pawan
	public function workspace_areas_ids($workspace_id = null, $count = false, $ids = false, $sort = false) {

		$workspaces = $area_id = null;

		$find_type = ($count) ? 'count' : 'all';
		$order = ($sort) ? ['Area.template_detail_id ASC'] : ['Area.sort_order ASC'];

		$workspace_data = $this->_workspaces->Area->find($find_type,
			array('conditions' => [
				'Area.workspace_id' => $workspace_id,
				'Area.studio_status !=' => 1,
			],
				'recursive' => '-1',
				'fields' => array('Area.id'),
				'order' => $order,
			));

		if ($count) {
			return $workspace_data;
		} else if (!$count && $ids) {
			if (isset($workspace_data) && !empty($workspace_data)) {
				$area_id = Set::extract($workspace_data, '/Area/id');
			}
			return $area_id;
		} else {
			return isset($workspace_data) ? $workspace_data : '';
		}

	}

	public function workspace_area_data($workspace_id = null, $count = false, $fields = null) {

		$workspaces = $area_id = null;

		$find_type = ($count) ? 'count' : 'all';

		$workspace_data = $this->_workspaces->Area->find($find_type,
			array('conditions' => [
				'Area.workspace_id' => $workspace_id,
				'Area.studio_status !=' => 1,
			],
				'recursive' => -1,
				'order' => ['Area.id ASC'],
			));

		if ($count) {
			return $workspace_data;
		} else {
			return isset($workspace_data) ? $workspace_data : '';
		}

	}

	// Get projects of loggedin user
	public function getProjectDetail($project_id = null, $recursive = 1) {

		$user_id = $this->Session->read('Auth.User.id');
		if (!$user_id) {
			return;
		}

		$data = $this->_projects->find('first', [
			'recursive' => $recursive,
			'conditions' => ['Project.id' => $project_id],
		]);
		// pr($data, 1);
		return $data;
	}

	// Get projects of loggedin user
	public function getWorkspaceDetail($workspace_id = null) {

		$data = $this->_workspaces->find('first', [
			'recursive' => -1,
			'conditions' => ['Workspace.id' => $workspace_id],
		]);
		// pr($data, 1);
		return $data;
	}

	public function getAreaDetail($id = null) {

		if (!$id) {
			return;
		}

		$data = $this->_areas->find('first', [
			'recursive' => -1,
			'conditions' => ['Area.id' => $id],
		]);

		if (isset($data) && !empty($data)) {
			if (isset($data['Area']) && !empty($data['Area'])) {
				return $data['Area'];
			}
		}
		return '';
	}

	public function getElementDetail($element_id = null) {

		$user_id = $this->Session->read('Auth.User.id');
		if (!$user_id) {
			return;
		}

		$data = $this->_elements->find('first', [
			'recursive' => -1,
			'conditions' => ['Element.id' => $element_id],
		]);
		// pr($data, 1);
		return $data;
	}

	public function getElementDetailbyids($element_id = null) {

		$user_id = $this->Session->read('Auth.User.id');
		if (!$user_id) {
			return;
		}

		$data = $this->_elements->find('all', [
			'recursive' => -1,
			'conditions' => ['Element.id' => $element_id],
		]);

		return $data;
	}

	// Get all elements of selected areas
	public function area_elements($area_id = null, $keys = false, $element_id = null) {

		if (empty($area_id)) {
			return null;
		}

		$data = null;

		if (!empty($area_id)) {

			$project_id = getParentId($this->_areas, $area_id);

			if (isset($element_id) && !empty($element_id)) {
				$data = $this->_elements->find('all', [
					'recursive' => -1,
					'conditions' => ['Element.area_id' => $area_id, 'Element.id' => $element_id],
					'order' => ['Element.sort_order ASC'],
				]);

			} else {

				$data = $this->_elements->find('all', [
					'recursive' => -1,
					'conditions' => ['Element.area_id' => $area_id, 'Element.studio_status !=' => 1],
					'order' => ['Element.sort_order ASC'],
				]);

			}

			if (isset($data) && !empty($data)) {
				foreach ($data as $key => $val) {
					$data[$key]['Element'] = array_merge($val['Element'], ['project_level' => 1, 'project_id' => $project_id]);
				}
			}

		}

		if ($keys && !empty($data)) {
			$elementIds = Set::extract($data, '/Element/id');
			return $elementIds;
		}

		return $data;
	}

	public function area_elements_permissions($area_id = null, $keys = false, $arr) {

		if (empty($area_id)) {
			return null;
		}

		$view = new View();
		$viewmodel = $view->loadHelper('ViewModel');

		$data = null;
		$user_id = CakeSession::read("Auth.User.id");
		if (!empty($area_id)) {

			$data1 = $this->_elements->find('all', [
				'recursive' => -1,
				'conditions' => ['Element.area_id' => $area_id, 'Element.id' => $arr],
				'order' => ['Element.sort_order ASC'],
			]);

			$data = $this->element_permission_data($data1, $area_id);

		}

		if ($keys && !empty($data)) {
			$elementIds = Set::extract($data, '/Element/id');
			return $elementIds;
		}

		return $data;
	}
	public function element_permission_data($data = null, $area_id = null, $userId = null) {

		$view = new View();
		$common = $view->loadHelper('Common');
		$group = $view->loadHelper('Group');

		if (!empty($data)) {
			// pr($data);
			// get all permissions of this element
			$elId = Set::extract($data, '{n}/Element/id');

			$user_id = CakeSession::read("Auth.User.id");
			if (isset($userId) && !empty($userId)) {
				$user_id = $userId;
			}

			$otherIDs = element_permissions($area_id);

			if (!empty($otherIDs)) {
				$project_owner = project_owner($otherIDs['project_id']);

				//$ws_permission =  $common->element_permission_details( $ws_id, $otherIDs['project_id'], $user_id);
				//pr($ws_permission);
				// pr($this->workspace_pwid($ws_id));

				$us_permission = $common->userproject($otherIDs['project_id'], $user_id);

				$pr_permission = $common->project_permission_details($otherIDs, $user_id);

				$grp_id = $group->GroupIDbyUserID($otherIDs['project_id'], $user_id);

				if (isset($grp_id) && !empty($grp_id)) {

					$group_permission = $group->group_permission_details($otherIDs['project_id']['project_id'], $grp_id);

					if (isset($group_permission['ProjectPermission']['project_level']) && $group_permission['ProjectPermission']['project_level'] == 1) {
						$project_level = $group_permission['ProjectPermission']['project_level'];
					}

					//$ws_permission =  $group->group_element_permission_details( $ws_id, $project_id['ProjectWorkspace']['project_id'], $grp_id);

					//
				}

				$project_level = 0;
				if ((!empty($us_permission)) || (isset($pr_permission['ProjectPermission']['project_level']) && $pr_permission['ProjectPermission']['project_level'] == 1) || (isset($project_level) && $project_level == 1)) {
					$project_level = 1;
				}

				/* $project_level = 0;
					if( $user_id == $project_owner ) {
						$project_level = 1;

					}
					else {
						$project_sharing_data = $this->project_sharing($otherIDs['project_id'], $user_id, null);
						if( !empty($project_sharing_data) && (isset($project_sharing_data['ProjectPermission']['project_level']) )) {
							if(!empty($project_sharing_data['ProjectPermission']['project_level']) && $project_sharing_data['ProjectPermission']['project_level'] > 0)
							$project_level = 1;
						}
				*/

				foreach ($data as $key => $val) {

					$elIds = null;
					$elIds = ['project_id' => $otherIDs['project_id'], 'workspace_id' => $otherIDs['workspace_id'], 'element_id' => $val['Element']['id']];

					//$sharing_data = $this->element_sharing($elIds, $user_id, null);

					$sharing_data = $common->element_share_permission($val['Element']['id'], $otherIDs['project_id'], $user_id);

					if (isset($grp_id) && !empty($grp_id)) {

						$sharing_data = $group->group_element_share_permission($val['Element']['id'], $otherIDs['project_id'], $grp_id);
					}

					if (isset($sharing_data) && !empty($sharing_data) && $project_level != 1) {
						unset($sharing_data['ElementPermission']['id']);
						$data[$key]['Element'] = array_merge($val['Element'], $sharing_data['ElementPermission'], ['project_level' => $project_level]);
					} else if ($project_level == 1) {
						$data[$key]['Element'] = array_merge($val['Element'], ['project_level' => 1]);
					}
				}
			}
		}

		return $data;
	}

	// Get Project Sharing Data with current User
	public function project_sharing($project_id = null, $shareUser = null, $project_detail = null, $user_project_id = null) {

		$data = null;
		App::import('Model', 'ProjectPermission');
		$ppModel = new ProjectPermission();

		$userProjectId = project_upid($project_id);
		if (!empty($userProjectId) && !empty($project_id)) {

			$data = $ppModel->find('first', [
				'conditions' => [
					'ProjectPermission.user_id' => $shareUser,
					'ProjectPermission.user_project_id' => $userProjectId,
				],
				'recursive' => -1,
			]);

		}

		return $data;
	}

	// Get Project Sharing Data with current User
	public function project_workspace_sharing($user_project_id = null, $shareUser = null) {

		$data = null;
		App::import('Model', 'WorkspacePermission');
		$ppModel = new WorkspacePermission();

		$conditions['WorkspacePermission.user_project_id'] = $user_project_id;

		if (!empty($shareUser) && !empty($shareUser)) {
			$conditions['WorkspacePermission.user_id'] = $shareUser;
		}

		if (!empty($user_project_id) && !empty($user_project_id)) {

			$data = $ppModel->find('all', [
				'conditions' => $conditions,
				'recursive' => -1,
			]);

		}

		return $data;
	}

	// Get Project Sharing Data with current User
	public function workspace_sharing($workspace_id = null, $shareUser = null, $project_detail = null) {

		$data = null;
		App::import('Model', 'WorkspacePermission');
		$wpModel = new WorkspacePermission();

		$projectId = $project_detail['Project']['id'];
		$userProjectId = $project_detail['UserProject']['id'];

		// $projectWorkspaceId = Set::extract($project_detail, '/Project/ProjectWorkspace/id');

		if (!empty($workspace_id)) {
			$projectWorkspace = $this->_project_workspaces->find('first', [
				'conditions' => [
					'ProjectWorkspace.project_id' => $projectId,
					'ProjectWorkspace.workspace_id' => $workspace_id,
				],
				'recursive' => -1,
				'fields' => ['ProjectWorkspace.id'],
			]);

			$projectWorkspaceId = $projectWorkspace['ProjectWorkspace']['id'];
			$data = $wpModel->find('first', [
				'conditions' => [
					'WorkspacePermission.user_id' => $shareUser,
					'WorkspacePermission.user_project_id' => $userProjectId,
					'WorkspacePermission.project_workspace_id' => $projectWorkspaceId,
				],
				'recursive' => -1,
			]);
		}
		return $data;
	}

	// Get Project Sharing Data with current User
	public function element_sharing($ids = null, $shareUser = null, $project_detail = null) {

		$data = null;
		App::import('Model', 'ElementPermission');
		$epModel = new ElementPermission();

		if (!empty($ids)) {
			$project_id = $ids['project_id'];
			$workspace_id = $ids['workspace_id'];
			$element_id = $ids['element_id'];
			// pr($ids );
			$data = $epModel->find('first', [
				'conditions' => [
					'ElementPermission.user_id' => $shareUser,
					'ElementPermission.project_id' => $project_id,
					'ElementPermission.workspace_id' => $workspace_id,
					'ElementPermission.element_id' => $element_id,
				],
				'recursive' => -1,
			]);
		}
		return $data;
	}

	public function project_sharing_users($user_project_id = null) {

		$data = null;
		App::import('Model', 'ProjectPermission');
		$ppModel = new ProjectPermission();
		$user_id = CakeSession::read("Auth.User.id");
		$userProjectId = (isset($user_project_id) && !empty($user_project_id)) ? $user_project_id : null;

		if (!empty($userProjectId)) {

			$data['Permissions'] = $ppModel->find('all', [
				'conditions' => [
					'not' => ['ProjectPermission.user_id' => $user_id],
					'ProjectPermission.user_project_id' => $userProjectId,
					'ProjectPermission.share_by_id' => $user_id,
					'ProjectPermission.share_by_id !=' => '',
				],
				'group' => ['ProjectPermission.user_id'],
				'recursive' => -1,
			]);



			$data['user_count'] = (isset($data['Permissions']) && !empty($data['Permissions'])) ? count($data['Permissions']) : 0;
			// echo $ppModel->_query();
		}
		// pr($data, 1);
		return $data;
	}



	public function user_project_permissions($shareUser = null, $userProjectId = null) {

		$data = null;

		App::import('Model', 'ProjectPermission');
		$ppModel = new ProjectPermission();

		$user_id = CakeSession::read("Auth.User.id");

		if ((isset($shareUser) && !empty($shareUser)) && (isset($userProjectId) && !empty($userProjectId))) {
			if ($ppModel->hasAny(['ProjectPermission.user_id' => $shareUser, 'ProjectPermission.user_project_id' => $userProjectId])) {

				$data = $ppModel->find('first', [
					'conditions' => [
						'ProjectPermission.user_id' => $shareUser,
						'ProjectPermission.user_project_id' => $userProjectId,
					],
					'recursive' => -1,
				]);
			}
		}

		return $data;
	}

	public function projectPermitType($pid = null, $userID = null) {

		$current_user_id = $this->Session->read('Auth.User.id');

		$query = "SELECT COUNT(user_permissions.project_id) as total

					FROM user_permissions

					#INNER JOIN
						#projects
						#ON projects.id=user_permissions.project_id
						#AND ( (DATE(NOW()) BETWEEN DATE(projects.start_date) AND DATE(projects.end_date))
						#OR projects.sign_off=1 )

					WHERE user_permissions.user_id = $current_user_id AND user_permissions.role IN ('Creator','Owner','Group Owner')  AND user_permissions.project_id = $pid AND user_permissions.workspace_id IS NULL";

		$projectCount =  ClassRegistry::init('UserPermission')->query($query);

		return ( !empty($projectCount[0][0]['total']) && $projectCount[0][0]['total'] > 0 ) ? true : false;

	}

	public function get_user_project_list_chat() {

		$current_user_id = $this->Session->read('Auth.User.id');

		$query = "SELECT COUNT(user_permissions.project_id) as total

					FROM user_permissions

					WHERE user_permissions.user_id = $current_user_id   AND user_permissions.workspace_id IS NULL";

		$projectCount =  ClassRegistry::init('UserPermission')->query($query);

		return ( !empty($projectCount[0][0]['total']) && $projectCount[0][0]['total'] > 0 ) ? $projectCount[0][0]['total'] : 0;

	}

	public function projectPermitType_old($pid = null, $userID = null) {
		App::import("Model", "ProjectPermission");
		$pp = new ProjectPermission();
		// pr($pid);
		$view = new View();
		$view_model = $view->loadHelper('ViewModel');
		$common = $view->loadHelper('Common');
		$group = $view->loadHelper('Group');

		$p_permission = $common->project_permission_details($pid, $userID);
		$user_project = $common->userproject($pid, $userID);

		$gp_exists = $group->GroupIDbyUserID($pid, $userID);
		if (isset($gp_exists) && !empty($gp_exists)) {
			$p_permission = $group->group_permission_details($pid, $gp_exists);
		}

		if ((isset($user_project) && !empty($user_project)) || (isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level'] == 1)) {
			// pr($user_project);
			return true;
		}
		return false;
	}


	/*
		 * Check the level of a user in a project permission (sharer or owner)
	*/
	public function sharingPermitType($pid = null, $userID = null) {
		App::import("Model", "ProjectPermission");
		$pp = new ProjectPermission();

		$view = new View();
		$view_model = $view->loadHelper('ViewModel');
		$common = $view->loadHelper('Common');
		$group = $view->loadHelper('Group');

		$p_permission = $common->project_permission_details($pid, $userID);
		$user_project = $common->userproject($pid, $userID);
		$gp_exists = $group->GroupIDbyUserID($pid, $userID);
		if (isset($gp_exists) && !empty($gp_exists)) {
			$p_permission = $group->group_permission_details($pid, $gp_exists);
		}

		$e_permission = [];

		if (((isset($user_project)) && (!empty($user_project))) || (isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level'] == 1)) {
			return true;
		} else if (isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level'] != 1) {
			return false;
		} else {
			return false;
		}

	}

	public function user_sharing_projects_myself($shareUser = null, $project_id = null) {

		if( isset($project_id) && !empty($project_id) ){

			$current_user_id = $this->Session->read('Auth.User.id');
			$query = "SELECT COUNT(user_permissions.project_id) as total
						FROM user_permissions
						WHERE user_permissions.project_id = $project_id AND
						user_permissions.role = 'Creator' AND
						user_permissions.user_id = $current_user_id AND
						user_permissions.workspace_id IS NULL ";

			$projectCount =  ClassRegistry::init('UserPermission')->query($query);

			return ( isset($projectCount[0][0]['total']) &&  !empty($projectCount[0][0]['total']) ) ? $projectCount[0][0]['total'] : 0;

		} else {

			$current_user_id = $this->Session->read('Auth.User.id');
			$query = "SELECT user_permissions.project_id
						FROM user_permissions
						WHERE user_permissions.user_id = $current_user_id AND
						user_permissions.role = 'Creator' AND
						user_permissions.workspace_id IS NULL ";

			$projectCount =  ClassRegistry::init('UserPermission')->query($query);

			return ( isset($projectCount) &&  !empty($projectCount) ) ? $projectCount : null;

		}


	}

	public function user_sharing_projects($shareUser = null) {

		$data = null;

		App::import('Model', 'ProjectPermission');
		$ppModel = new ProjectPermission();

		App::import('Model', 'ProjectGroup');
		$ProjectGroup = new ProjectGroup();

		App::import('Model', 'ProjectGroupUser');
		$ProjectGroupUser = new ProjectGroupUser();

		$user_id = CakeSession::read("Auth.User.id");

		if (!isset($shareUser) || empty($shareUser)) {

			$data['shared_users'] = $ppModel->find('all', [
				'conditions' => [
					//'ProjectPermission.user_id' => $shareUser,
					'ProjectPermission.share_by_id' => $user_id,
				],
				'joins' => array(
				array(
					'table' => 'user_projects',
					'alias' => 'UserProject',
					'type' => 'INNER',
					'conditions' => array(
						'UserProject.project_id = ProjectPermission.user_project_id',
						'UserProject.user_id ='.$user_id,

					),
				)
				),
				// 'fields' => ['ProjectPermission.id', 'ProjectPermission.user_id'],
				'group' => ['ProjectPermission.user_id'],
				'recursive' => -1,
			]);

			$data['group_shared_ids'] = $ProjectGroup->find('all', [
				'conditions' => [
					//'ProjectPermission.user_id' => $shareUser,
					'ProjectGroup.group_owner_id' => $user_id,
					//'ProjectPermission.project_group_id !=""',
				],


				 'fields' => ['ProjectGroup.id'],
				//'group' => ['ProjectGroup.id'],
				'recursive' => -1,
			]);



			if(isset($data['group_shared_ids']) && !empty($data['group_shared_ids'])){

			$gids = Set::extract($data['group_shared_ids'], '/ProjectGroup/id');
			$data['group_shared_users'] = $ProjectGroupUser->find('list', [
				'conditions' => [
					//'ProjectPermission.user_id' => $shareUser,
					'ProjectGroupUser.project_group_id' => $gids,
					'ProjectGroupUser.approved !=2'
					//'ProjectPermission.project_group_id !=""',
				],


				 'fields' => ['ProjectGroupUser.id','ProjectGroupUser.user_id'],
				//'group' => ['ProjectGroup.id'],
				'recursive' => -1,
			]);

			  $gp_users = array_unique($data['group_shared_users']);
			  $gp_users = (isset($gp_users) && !empty($gp_users)) ? $gp_users : array();


			}
			 $gp_users = (isset($gp_users) && !empty($gp_users)) ? $gp_users : array();


			if(isset($data['shared_users']) && !empty($data['shared_users'])){
			$projectuserId = Set::extract($data['shared_users'], '/ProjectPermission/user_id');
			//pr(array_unique($data['group_shared_users']));
			 $projectuserId = (isset($projectuserId) && !empty($projectuserId)) ? $projectuserId : array();

			}
			$projectuserId = (isset($projectuserId) && !empty($projectuserId)) ? $projectuserId : array();



			$gp_users = array_filter(array_unique(array_merge($projectuserId ,  $gp_users)));

			$data['user_count'] = ( isset($data['shared_users']) && !empty($data['shared_users']) ) ? count($data['shared_users']) : 0;

			$data['all_users'] = ( isset($gp_users) && !empty($gp_users) ) ? $gp_users : array();

			//pr($data['all_users']);

		}

		if (isset($shareUser) && !empty($shareUser)) {

			$data['shared_projects'] = $ppModel->find('all', [
				'conditions' => [
					'ProjectPermission.user_id' => $shareUser,
					 'ProjectPermission.share_by_id' => $user_id,
					'ProjectPermission.share_by_id !=' => '',
				],
				'joins' => array(
				array(
					'table' => 'user_projects',
					'alias' => 'UserProject',
					'type' => 'INNER',
					'conditions' => array(
						'UserProject.project_id = ProjectPermission.user_project_id',
						'UserProject.user_id ='.$user_id,

					),
				)
			),
				//'group' => ['ProjectPermission.user_id'],
				'recursive' => -1,
			]);
			$data['project_count'] = ( isset($data['shared_projects']) && !empty($data['shared_projects']) ) ? count($data['shared_projects']) : 0;

		}


		return $data;
	}

	/************** PROPAGATION *******************/

	// Get Project Propagation Data with current User
	public function project_propagation($project_id = null, $shareUser = null) {

		// if( empty($area_id) ) return null;

		$data = null;
		App::import('Model', 'ProjectPropagate');
		$ppModel = new ProjectPropagate();

		$userProjectId = project_upid($project_id);

		if (!empty($userProjectId) && !empty($project_id)) {

			$data = $ppModel->find('first', [
				'conditions' => [
					'ProjectPropagate.share_for_id' => $shareUser,
					'ProjectPropagate.user_project_id' => $userProjectId,
				],
				'recursive' => -1,
			]);

		}
		// pr($data, 1);
		return $data;
	}

	// Get Project Propagation Data with current User
	public function workspace_propagation($workspace_id = null, $shareUser = null, $project_detail = null, $project_id = null) {

		$data = null;
		App::import('Model', 'WorkspacePropagate');
		$wpModel = new WorkspacePropagate();

		$projectId = null;
		if (isset($project_detail['Project']['id']) && !empty($project_detail['Project']['id'])) {
			$projectId = $project_detail['Project']['id'];
		} else if (isset($project_id) && !empty($project_id)) {
			$projectId = $project_id;
		}

		$userProjectId = project_upid($projectId);

		// $projectWorkspaceId = Set::extract($project_detail, '/Project/ProjectWorkspace/id');

		if (!empty($workspace_id)) {

			# GET project_workspace_id FROM project_id and workspace_id VALUE
			$projectWorkspaceId = workspace_pwid($projectId, $workspace_id);

			$data = $wpModel->find('first', [
				'conditions' => [
					'WorkspacePropagate.share_for_id' => $shareUser,
					'WorkspacePropagate.user_project_id' => $userProjectId,
					'WorkspacePropagate.project_workspace_id' => $projectWorkspaceId,
				],
				'recursive' => -1,
			]);
		}
		return $data;
	}

	public function Group_workspace_propagation($workspace_id = null, $shareUser = null, $project_detail = null, $project_id = null) {

		$data = null;
		App::import('Model', 'WorkspacePropagate');
		$wpModel = new WorkspacePropagate();

		$projectId = null;
		if (isset($project_detail['Project']['id']) && !empty($project_detail['Project']['id'])) {
			$projectId = $project_detail['Project']['id'];
		} else if (isset($project_id) && !empty($project_id)) {
			$projectId = $project_id;
		}

		$userProjectId = project_upid($projectId);

		// $projectWorkspaceId = Set::extract($project_detail, '/Project/ProjectWorkspace/id');

		if (!empty($workspace_id)) {

			# GET project_workspace_id FROM project_id and workspace_id VALUE
			$projectWorkspaceId = workspace_pwid($projectId, $workspace_id);

			$data = $wpModel->find('first', [
				'conditions' => [
					'WorkspacePropagate.project_group_id' => $shareUser,
					'WorkspacePropagate.user_project_id' => $userProjectId,
					'WorkspacePropagate.project_workspace_id' => $projectWorkspaceId,
				],
				'recursive' => -1,
			]);
		}
		return $data;
	}

	// Get Project Propagation Data with current User
	public function element_propagation($ids = null, $shareUser = null, $project_detail = null) {

		$data = null;
		App::import('Model', 'ElementPropagate');
		$epModel = new ElementPropagate();

		if (!empty($ids)) {

			$project_id = $ids['project_id'];
			$workspace_id = $ids['workspace_id'];
			$element_id = $ids['element_id'];

			$data = $epModel->find('first', [
				'conditions' => [
					'ElementPropagate.share_for_id' => $shareUser,
					'ElementPropagate.project_id' => $project_id,
					'ElementPropagate.workspace_id' => $workspace_id,
					'ElementPropagate.element_id' => $element_id,
				],
				'recursive' => -1,
			]);
		}
		return $data;
	}

	public function Group_element_propagation($ids = null, $shareUser = null, $project_detail = null) {

		$data = null;
		App::import('Model', 'ElementPropagate');
		$epModel = new ElementPropagate();

		if (!empty($ids)) {

			$project_id = $ids['project_id'];
			$workspace_id = $ids['workspace_id'];
			$element_id = $ids['element_id'];

			$data = $epModel->find('first', [
				'conditions' => [
					'ElementPropagate.project_group_id' => $shareUser,
					'ElementPropagate.project_id' => $project_id,
					'ElementPropagate.workspace_id' => $workspace_id,
					'ElementPropagate.element_id' => $element_id,
				],
				'recursive' => -1,
			]);
		}
		return $data;
	}



	public function group_all_permissions($user_id = null, $project_id = null,$group_id = null) {

		$data = null;

		$share_user_id = $user_id;
		if (!empty($project_id)) {

			$project_detail = $this->_user_projects->find('first', ['conditions' => ['UserProject.id' => project_upid($project_id)], 'recursive' => 2]);
			$userProjectId = $project_detail['UserProject']['id'];
			$projectWorkspaceId = Set::extract($project_detail, '/Project/ProjectWorkspace/id');
			$workspaceId = Set::extract($project_detail, '/Project/ProjectWorkspace/workspace_id');

			$pp_data = array();
			if(isset($group_id) && !empty($group_id)){
				$pp_data = $this->_pr_permit->find('first', [
					'conditions' => [
						'ProjectPermission.user_project_id' => project_upid($project_id),
						'ProjectPermission.project_group_id' => $group_id,
					],
					'recursive' => -1,
				]);


			}

			$pp_data_count = ( isset($pp_data) && !empty($pp_data) ) ? count($pp_data) : 0;
			if (!empty($pp_data_count)) {
				$data['pp_data'] = $pp_data;
				$data['pp_data_count'] = $pp_data_count;
			}

			// -------- Get project_workspace_id of the current project and find those in workspace_permissions for this user
			$wp_data = $this->_ws_permit->find('all', [
				'conditions' => [
					'WorkspacePermission.project_group_id' => $group_id,
					'WorkspacePermission.user_project_id' => project_upid($project_id),
				],
				'recursive' => -1,
			]);


			$wp_data_count = ( isset($wp_data) && !empty($wp_data) ) ? count($wp_data) : 0;
			if (!empty($wp_data_count)) {
				$data['wp_data'] = $wp_data;
				$data['wp_data_count'] = $wp_data_count;
			}

			// -------- Get all area ids of all workspaces
			$ws_area = $this->workspace_areas($workspaceId, false, true);
			// -------- Get all element ids of those
			$elm = $this->area_elements($ws_area, true);

			// -------- Find all element ids in element_permissions
			$ep_data = $this->_el_permit->find('all', [
				'conditions' => [
					'ElementPermission.project_group_id' => $group_id,
					'ElementPermission.project_id' => $project_id,
					'ElementPermission.workspace_id' => $workspaceId,
					'ElementPermission.element_id' => $elm,
				],
				'recursive' => -1,
			]);
			$ep_data_count = ( isset($ep_data) && !empty($ep_data) ) ? count($ep_data) : 0;
			if (!empty($ep_data_count)) {
				$data['ep_data'] = $ep_data;
				$data['ep_data_count'] = $ep_data_count;
			}

		}
		return $data;
	}


	// Get Project Propagation Data with current User
	public function all_permissions($user_id = null, $project_id = null) {

		$data = null;

		$share_user_id = $user_id;
		if (!empty($project_id)) {

			$project_detail = $this->_user_projects->find('first', ['conditions' => ['UserProject.id' => project_upid($project_id)], 'recursive' => 2]);
			$userProjectId = $project_detail['UserProject']['id'];
			$projectWorkspaceId = Set::extract($project_detail, '/Project/ProjectWorkspace/id');
			$workspaceId = Set::extract($project_detail, '/Project/ProjectWorkspace/workspace_id');

			$pp_data = $this->_pr_permit->find('first', [
				'conditions' => [
					'ProjectPermission.user_id' => $share_user_id,
					'ProjectPermission.user_project_id' => project_upid($project_id),
				],
				'recursive' => -1,
			]);

			$pp_data_count = ( isset($pp_data) && !empty($pp_data) ) ? count($pp_data) : 0;
			if (!empty($pp_data_count)) {
				$data['pp_data'] = $pp_data;
				$data['pp_data_count'] = $pp_data_count;
			}

			// -------- Get project_workspace_id of the current project and find those in workspace_permissions for this user



			$wp_data = $this->_ws_permit->find('all', [
				'conditions' => [
					'WorkspacePermission.user_id' => $share_user_id,
					'WorkspacePermission.user_project_id' => project_upid($project_id),
				],
				'recursive' => -1,
			]);

			$wp_data_count = ( isset($wp_data) && !empty($wp_data) ) ? count($wp_data) : 0;
			if (!empty($wp_data_count)) {
				$data['wp_data'] = $wp_data;
				$data['wp_data_count'] = $wp_data_count;
			}

			// -------- Get all area ids of all workspaces
			$ws_area = $this->workspace_areas($workspaceId, false, true);
			// -------- Get all element ids of those
			$elm = $this->area_elements($ws_area, true);

			// -------- Find all element ids in element_permissions
			$ep_data = $this->_el_permit->find('all', [
				'conditions' => [
					'ElementPermission.user_id' => $share_user_id,
					'ElementPermission.project_id' => $project_id,
					'ElementPermission.workspace_id' => $workspaceId,
					'ElementPermission.element_id' => $elm,
				],
				'recursive' => -1,
			]);
			$ep_data_count = ( isset($ep_data) && !empty($ep_data) ) ? count($ep_data) : 0;
			if (!empty($ep_data_count)) {
				$data['ep_data'] = $ep_data;
				$data['ep_data_count'] = $ep_data_count;
			}

		}
		return $data;
	}

	// Get Project Propagation Data with current User
	public function propagated_projects($id = null, $user_project_id = null, $user_id = null) {

		$data = null;
		$userProjectId = (isset($user_project_id) && !empty($user_project_id)) ? $user_project_id : project_upid($project_id);
		$user_id = CakeSession::read("Auth.User.id");

		/* if (!empty($id) && !empty($userProjectId)) {

			$data = $this->_pr_permit->find('all', [
				'conditions' => [
					'ProjectPermission.parent_id' => $id,
					'ProjectPermission.user_project_id' => $userProjectId,
				],
				'recursive' => 1,
				'group' => 'ProjectPermission.user_id',
			]);

		}  */

		if (!empty($userProjectId)) {

			$data  = $this->_pr_permit->find('all', [
				'conditions' => [
					//'not' => ['ProjectPermission.user_id' => $user_id],
					'ProjectPermission.user_project_id' => $userProjectId,
					'ProjectPermission.share_by_id' => $user_id,
					'ProjectPermission.share_by_id !=' => '',
				],
				'joins' => array(
					array(
						'table' => 'user_projects',
						'alias' => 'UserProjects',
						'type' => 'INNER',
						'conditions' => array(
							'UserProjects.project_id = ProjectPermission.user_project_id',
						),
					),
					array(
						'table' => 'user_permissions',
						'alias' => 'UserPermission',
						'type' => 'INNER',
						'conditions' => array(
							'UserPermission.project_id = UserProjects.project_id',
							'UserPermission.role != "Creator"',
							'UserPermission.workspace_id IS NULL'
						),
					),
				),
				'group' => ['ProjectPermission.user_id'],
				'recursive' => 1,
			]);

		}
		return $data;
	}



	// Get Project Propagation Data with current User
	public function is_shared($project_id = null) {

		$data = null;

		$user_project_id = project_upid($project_id);

		if (!empty($project_id) && !empty($user_project_id)) {

			$data = $this->_pr_permit->find('count', [
				'conditions' => [
					'ProjectPermission.user_project_id' => $user_project_id,
				],
			]);

		}

		return (isset($data) && !empty($data)) ? true : false;
	}

	public function get_all_areas($project_id = null) {
		$area_ids = $elements = null;
		$workspaces = $this->get_project_workspace($project_id);
		if (isset($workspaces) && !empty($workspaces)) {

			foreach ($workspaces as $key => $value) {

				$workspace = $value['Workspace'];

				$areas = $this->workspace_areas($workspace['id'], false, true);
				if (isset($areas) && !empty($areas)) {
					if (is_array($area_ids)) {
						$area_ids = array_merge($area_ids, array_values($areas));
					} else {
						$area_ids = array_values($areas);
					}

				}

			}

			if (isset($area_ids) && !empty($area_ids)) {
				$elements = $this->area_elements($area_ids);
			}
		}
		return $elements;
	}

	// Get Project Elements
	public function project_elements($project_id = null, $type = null) {

		$data = null;

		if (!isset($project_id) || empty($project_id)) {
			return $data;
		}

		$workspaces = $elements = $area_ids = null;

		$workspaces = $this->get_project_workspace($project_id);

		if (isset($workspaces) && !empty($workspaces)) {

			foreach ($workspaces as $key => $value) {

				$workspace = $value['Workspace'];

				$areas = $this->workspace_areas($workspace['id'], false, true);
				if (isset($areas) && !empty($areas)) {
					if (is_array($area_ids)) {
						$area_ids = array_merge($area_ids, array_values($areas));
					} else {
						$area_ids = array_values($areas);
					}

				}

			}
		}

		if (isset($area_ids) && !empty($area_ids)) {

			$query = '';

			$query .= 'SELECT element.*';
			$query .= 'FROM elements as element ';
			$query .= "WHERE element.area_id IN (" . implode(',', $area_ids) . ") ";

			if (isset($type) && !empty($type) && $type == 'completed') {
				// $query .= "AND date(element.end_date) < '".date('Y-m-d')."' ";
				$query .= "AND element.date_constraints = 1 ";
				$query .= "AND element.sign_off = 1 ";
			}

			// $query .= "ORDER BY totalDays ASC ";

			$data = $this->_elements->query($query);
		}

		return $data;
	}

	// Get Project Elements
	public function project_element_ids($project_id = null, $cost_type = null) {

		App::import("Model", "ElementCost");
		$ec = new ElementCost();

		$data = null;

		if (!isset($project_id) || empty($project_id)) {
			return $data;
		}

		$workspaces = $elements = $area_ids = null;
		$eles = $this->get_project_elements($project_id, false, true);
		if (isset($eles) && !empty($eles)) {
			$totalelecostval = 0;
			foreach ($eles as $elemid) {

				if ($cost_type == 'spend_cost') {

					$totalcost = $ec->find('first', array(
						'fields' => array('SUM(ElementCost.spend_cost) AS ctotal'),
						'conditions' => array('ElementCost.element_id' => $elemid['elements']['id']),
					));

					if (isset($totalcost[0]['ctotal']) && $totalcost[0]['ctotal'] > 0) {
						$totalelecostval += $totalcost[0]['ctotal'];
					}

				} else {

					$totalcost = $ec->find('first', array(
						'fields' => array('SUM(ElementCost.estimated_cost) AS ctotal'),
						'conditions' => array('ElementCost.element_id' => $elemid['elements']['id']),
					));

					if (isset($totalcost[0]['ctotal']) && $totalcost[0]['ctotal'] > 0) {
						$totalelecostval += $totalcost[0]['ctotal'];
					}
				}
			}
		}

			return (isset($totalelecostval) && $totalelecostval > 0) ? $totalelecostval : 0;

	}

	public function projectCostStatus($project_id = null, $projectbudget = null ) {

			$estimatcost = $this->project_element_ids($project_id, 'estimated_cost');

			$spendcost = $this->project_element_ids($project_id, 'spend_cost');



		//echo $projectbudget.'='.$estimatcost.'='.$spendcost;

		$costStatus = '<span class="tipText filterD" title="No Values Provided" >None Set</span>';
		if ((!isset($projectbudget) || $projectbudget == 0) && (!isset($estimatcost) || $estimatcost == 0) && (!isset($spendcost) || $spendcost == 0)) {

			$costStatus = '<span class="tipText filterD" title="No Values Provided" style="cursor: pointer;" >None Set</span>';
			$costStatus = '<span class="tipText filterD" title="No Values Provided" style="cursor: pointer;" >None Set</span>';

		} else if ((!isset($projectbudget) || $projectbudget == 0) && (isset($estimatcost) && $estimatcost > 0) && (!isset($spendcost) || $spendcost == 0)) {

			$costStatus = '<span class="tipText filterD" title="Only Estimate Value Provided" style="cursor: pointer;" >Estimates Initiated</span>';

		} else if ((!isset($projectbudget) || $projectbudget == 0) && (!isset($estimatcost) || $estimatcost == 0) && (isset($spendcost) && $spendcost > 0)) {

			$costStatus = '<span class="tipText filterD" title="Only Spending Value Provided" style="cursor: pointer;" >Spending Initiated</span>';

		} else if ((!isset($projectbudget) || $projectbudget == 0) && (isset($estimatcost) && $estimatcost > 0) && (isset($spendcost) && $spendcost > 0 && $spendcost > $estimatcost)) {

			$costStatus = '<span class="tipText filterD" title="No Budget, Spending Above Estimate" style="cursor: pointer;">Exceeded Estimate</span>';

		} else if ((!isset($projectbudget) || $projectbudget == 0) && (isset($estimatcost) && $estimatcost > 0) && (isset($spendcost) && $spendcost > 0 && $spendcost <= $estimatcost)) {

			$costStatus = '<span class="tipText filterD" title="No Budget, Spending Within Estimate" style="cursor: pointer;">Within Estimates</span>';

		} else if ((isset($projectbudget) && $projectbudget > 0) && (!isset($estimatcost) || $estimatcost == 0) && (!isset($spendcost) || $spendcost == 0)) {

			$costStatus = '<span class="tipText filterD" title="Budget Set, No Estimate And Spending Values" style="cursor: pointer;">Budget Set</span>';

		} else if ((isset($projectbudget) && $projectbudget > 0) && (!isset($estimatcost) || $estimatcost == 0) && (isset($spendcost) && $spendcost > 0 && $spendcost > $projectbudget)) {

			$costStatus = '<span style="color:#FF0000; cursor: pointer;" class="tipText filterD" title="Spending Over Budget">Over Budget</span>';

		} else if (

			((isset($projectbudget) && $projectbudget > 0) && (isset($estimatcost) && $estimatcost > 0 && $estimatcost <= $projectbudget) && (isset($spendcost) && $spendcost > 0 && $spendcost <= $projectbudget))
			||
			((isset($projectbudget) && $projectbudget > 0) && (!isset($estimatcost) || $estimatcost == 0) && (isset($spendcost) && $spendcost > 0 && $spendcost <= $projectbudget))
			||
			((isset($projectbudget) && $projectbudget > 0) && (isset($estimatcost) && $estimatcost > 0) && (!isset($spendcost) || $spendcost <= 0))

		) {
			$costStatus = '<span class="tipText filterD" title="Estimate And Spending Below Budget" style="cursor: pointer;">On Budget</span>';

		} else if (
			(isset($projectbudget) && $projectbudget > 0) && (isset($estimatcost) && $estimatcost > 0 && $estimatcost > $projectbudget) && (isset($spendcost) && $spendcost > 0 && $spendcost <= $projectbudget)) {

			$costStatus = '<span style="color:#FF0000; cursor: pointer;" class="tipText filterD" title="Estimate Over Budget, Spending Below Budget">On Budget, at Risk</span>';

		} else if ((isset($projectbudget) && $projectbudget > 0) && (isset($estimatcost) && $estimatcost > 0 && $estimatcost > $projectbudget) && (isset($spendcost) && $spendcost > 0 && $spendcost > $projectbudget)) {
			// this is first new condition
			$costStatus = '<span style="color:#FF0000; cursor: pointer;" class="tipText filterD" title="Spending Over Budget">Over Budget</span>';

		} else if ((isset($projectbudget) && $projectbudget > 0) && (isset($estimatcost) && $estimatcost > 0 && $estimatcost < $projectbudget) && (isset($spendcost) && $spendcost > 0 && $spendcost > $projectbudget)) {
			// this is second new condition

			$costStatus = '<span style="color:#FF0000; cursor: pointer;" class="tipText filterD" title="Spending Over Budget">Over Budget</span>';

		} else if ((isset($projectbudget) && $projectbudget > 0) && (isset($estimatcost) && $estimatcost > 0 && $estimatcost > $projectbudget) && (!isset($spendcost) || $spendcost <= 0)) {

			$costStatus = '<span style="color:#FF0000; cursor: pointer;" class="tipText filterD" title="Estimate Over Budget, Spending Below Budget">On Budget, at Risk</span>';

		}

		return $costStatus;
	}


	public function prjCstSts($project_id = null, $projectbudget = null,  $estimatcost = null,  $spendcost = null ) {


		$costStatus = '<span class="tipText filterD" title="No Values Provided" >None Set</span>';
		if ((!isset($projectbudget) || $projectbudget == 0) && (!isset($estimatcost) || $estimatcost == 0) && (!isset($spendcost) || $spendcost == 0)) {

			$costStatus = '<span class="tipText filterD" title="No Values Provided" style="cursor: pointer;" >None Set</span>';
			$costStatus = '<span class="tipText filterD" title="No Values Provided" style="cursor: pointer;" >None Set</span>';

		} else if ((!isset($projectbudget) || $projectbudget == 0) && (isset($estimatcost) && $estimatcost > 0) && (!isset($spendcost) || $spendcost == 0)) {

			$costStatus = '<span class="tipText filterD" title="Only Estimate Value Provided" style="cursor: pointer;" >Estimates Initiated</span>';

		} else if ((!isset($projectbudget) || $projectbudget == 0) && (!isset($estimatcost) || $estimatcost == 0) && (isset($spendcost) && $spendcost > 0)) {

			$costStatus = '<span class="tipText filterD" title="Only Spending Value Provided" style="cursor: pointer;" >Spending Initiated</span>';

		} else if ((!isset($projectbudget) || $projectbudget == 0) && (isset($estimatcost) && $estimatcost > 0) && (isset($spendcost) && $spendcost > 0 && $spendcost > $estimatcost)) {

			$costStatus = '<span class="tipText filterD" title="No Budget, Spending Above Estimate" style="cursor: pointer;">Exceeded Estimate</span>';

		} else if ((!isset($projectbudget) || $projectbudget == 0) && (isset($estimatcost) && $estimatcost > 0) && (isset($spendcost) && $spendcost > 0 && $spendcost <= $estimatcost)) {

			$costStatus = '<span class="tipText filterD" title="No Budget, Spending Within Estimate" style="cursor: pointer;">Within Estimates</span>';

		} else if ((isset($projectbudget) && $projectbudget > 0) && (!isset($estimatcost) || $estimatcost == 0) && (!isset($spendcost) || $spendcost == 0)) {

			$costStatus = '<span class="tipText filterD" title="Budget Set, No Estimate And Spending Values" style="cursor: pointer;">Budget Set</span>';

		} else if ((isset($projectbudget) && $projectbudget > 0) && (!isset($estimatcost) || $estimatcost == 0) && (isset($spendcost) && $spendcost > 0 && $spendcost > $projectbudget)) {

			$costStatus = '<span style="color:#FF0000; cursor: pointer;" class="tipText filterD" title="Spending Over Budget">Over Budget</span>';

		} else if (

			((isset($projectbudget) && $projectbudget > 0) && (isset($estimatcost) && $estimatcost > 0 && $estimatcost <= $projectbudget) && (isset($spendcost) && $spendcost > 0 && $spendcost <= $projectbudget))
			||
			((isset($projectbudget) && $projectbudget > 0) && (!isset($estimatcost) || $estimatcost == 0) && (isset($spendcost) && $spendcost > 0 && $spendcost <= $projectbudget))
			||
			((isset($projectbudget) && $projectbudget > 0) && (isset($estimatcost) && $estimatcost > 0) && (!isset($spendcost) || $spendcost <= 0))

		) {
			$costStatus = '<span class="tipText filterD" title="Estimate And Spending Below Budget" style="cursor: pointer;">On Budget</span>';

		} else if (
			(isset($projectbudget) && $projectbudget > 0) && (isset($estimatcost) && $estimatcost > 0 && $estimatcost > $projectbudget) && (isset($spendcost) && $spendcost > 0 && $spendcost <= $projectbudget)) {

			$costStatus = '<span style="color:#FF0000; cursor: pointer;" class="tipText filterD" title="Estimate Over Budget, Spending Below Budget">On Budget, at Risk</span>';

		} else if ((isset($projectbudget) && $projectbudget > 0) && (isset($estimatcost) && $estimatcost > 0 && $estimatcost > $projectbudget) && (isset($spendcost) && $spendcost > 0 && $spendcost > $projectbudget)) {
			// this is first new condition
			$costStatus = '<span style="color:#FF0000; cursor: pointer;" class="tipText filterD" title="Spending Over Budget">Over Budget</span>';

		} else if ((isset($projectbudget) && $projectbudget > 0) && (isset($estimatcost) && $estimatcost > 0 && $estimatcost < $projectbudget) && (isset($spendcost) && $spendcost > 0 && $spendcost > $projectbudget)) {
			// this is second new condition

			$costStatus = '<span style="color:#FF0000; cursor: pointer;" class="tipText filterD" title="Spending Over Budget">Over Budget</span>';

		} else if ((isset($projectbudget) && $projectbudget > 0) && (isset($estimatcost) && $estimatcost > 0 && $estimatcost > $projectbudget) && (!isset($spendcost) || $spendcost <= 0)) {

			$costStatus = '<span style="color:#FF0000; cursor: pointer;" class="tipText filterD" title="Estimate Over Budget, Spending Below Budget">On Budget, at Risk</span>';

		}

		return $costStatus;
	}



	// Get Project Elements
	public function project_element_costs($project_id = null, $cost_type = null) {

		App::import("Model", "ElementCost");
		$ec = new ElementCost();

		$data = null;

		if (!isset($project_id) || empty($project_id)) {
			return $data;
		}

		$workspaces = $elements = $area_ids = null;
		$eles = $this->get_project_elements($project_id, false, true);
		// pr(implode(',', $eles), 1);

		$total_spend = $total_est = 0;
		if (isset($eles) && !empty($eles)) {
			$eles = Set::extract($eles, '/elements/id');
			$qry = "SELECT SUM(spend_cost) AS stotal, SUM(estimated_cost) AS etotal FROM `element_costs` WHERE `element_id` IN (".implode(',', $eles).")";
			$detail = $ec->query($qry);
			if(isset($detail) && !empty($detail)){
				$data = ['spend' => $detail[0][0]['stotal'], 'estimate' => $detail[0][0]['etotal']];
			}
			/*foreach ($eles as $elemid) {
				$totalcost = $ec->find('first', array(
					'fields' => array('SUM(ElementCost.spend_cost) AS stotal', 'SUM(ElementCost.estimated_cost) AS etotal'),
					'conditions' => array('ElementCost.element_id' => $elemid['elements']['id']),
				));

				if (isset($totalcost[0]['stotal']) && $totalcost[0]['stotal'] > 0) {
					$total_spend += $totalcost[0]['stotal'];
				}
				if (isset($totalcost[0]['etotal']) && $totalcost[0]['etotal'] > 0) {
					$total_est += $totalcost[0]['etotal'];
				}
			}*/
		}

		return $data;

	}

	public function projectCostStatuses($project_id = null, $projectbudget = null) {

		$total_cost = $this->project_element_costs($project_id );
		$spendcost = $total_cost['spend'];
		$estimatcost = $total_cost['estimate'];

		//echo $projectbudget.'='.$estimatcost.'='.$spendcost;

		$costStatus = '<span class="tipText filterD" title="No Values Provided" >None Set</span>';
		if ((!isset($projectbudget) || $projectbudget == 0) && (!isset($estimatcost) || $estimatcost == 0) && (!isset($spendcost) || $spendcost == 0)) {

			$costStatus = '<span class="tipText filterD" title="No Values Provided" style="cursor: pointer;" >None Set</span>';
			$costStatus = '<span class="tipText filterD" title="No Values Provided" style="cursor: pointer;" >None Set</span>';

		} else if ((!isset($projectbudget) || $projectbudget == 0) && (isset($estimatcost) && $estimatcost > 0) && (!isset($spendcost) || $spendcost == 0)) {

			$costStatus = '<span class="tipText filterD" title="Only Estimate Value Provided" style="cursor: pointer;" >Estimates Initiated</span>';

		} else if ((!isset($projectbudget) || $projectbudget == 0) && (!isset($estimatcost) || $estimatcost == 0) && (isset($spendcost) && $spendcost > 0)) {

			$costStatus = '<span class="tipText filterD" title="Only Spending Value Provided" style="cursor: pointer;" >Spending Initiated</span>';

		} else if ((!isset($projectbudget) || $projectbudget == 0) && (isset($estimatcost) && $estimatcost > 0) && (isset($spendcost) && $spendcost > 0 && $spendcost > $estimatcost)) {

			$costStatus = '<span class="tipText filterD" title="No Budget, Spending Above Estimate" style="cursor: pointer;">Exceeded Estimate</span>';

		} else if ((!isset($projectbudget) || $projectbudget == 0) && (isset($estimatcost) && $estimatcost > 0) && (isset($spendcost) && $spendcost > 0 && $spendcost <= $estimatcost)) {

			$costStatus = '<span class="tipText filterD" title="No Budget, Spending Within Estimate" style="cursor: pointer;">Within Estimates</span>';

		} else if ((isset($projectbudget) && $projectbudget > 0) && (!isset($estimatcost) || $estimatcost == 0) && (!isset($spendcost) || $spendcost == 0)) {

			$costStatus = '<span class="tipText filterD" title="Budget Set, No Estimate And Spending Values" style="cursor: pointer;">Budget Set</span>';

		} else if ((isset($projectbudget) && $projectbudget > 0) && (!isset($estimatcost) || $estimatcost == 0) && (isset($spendcost) && $spendcost > 0 && $spendcost > $projectbudget)) {

			$costStatus = '<span style="color:#FF0000; cursor: pointer;" class="tipText filterD" title="Spending Over Budget">Over Budget</span>';

		} else if (

			((isset($projectbudget) && $projectbudget > 0) && (isset($estimatcost) && $estimatcost > 0 && $estimatcost <= $projectbudget) && (isset($spendcost) && $spendcost > 0 && $spendcost <= $projectbudget))
			||
			((isset($projectbudget) && $projectbudget > 0) && (!isset($estimatcost) || $estimatcost == 0) && (isset($spendcost) && $spendcost > 0 && $spendcost <= $projectbudget))
			||
			((isset($projectbudget) && $projectbudget > 0) && (isset($estimatcost) && $estimatcost > 0) && (!isset($spendcost) || $spendcost <= 0))

		) {
			$costStatus = '<span class="tipText filterD" title="Estimate And Spending Below Budget" style="cursor: pointer;">On Budget</span>';

		} else if (
			(isset($projectbudget) && $projectbudget > 0) && (isset($estimatcost) && $estimatcost > 0 && $estimatcost > $projectbudget) && (isset($spendcost) && $spendcost > 0 && $spendcost <= $projectbudget)) {

			$costStatus = '<span style="color:#FF0000; cursor: pointer;" class="tipText filterD" title="Estimate Over Budget, Spending Below Budget">On Budget, at Risk</span>';

		} else if ((isset($projectbudget) && $projectbudget > 0) && (isset($estimatcost) && $estimatcost > 0 && $estimatcost > $projectbudget) && (isset($spendcost) && $spendcost > 0 && $spendcost > $projectbudget)) {
			// this is first new condition
			$costStatus = '<span style="color:#FF0000; cursor: pointer;" class="tipText filterD" title="Spending Over Budget">Over Budget</span>';

		} else if ((isset($projectbudget) && $projectbudget > 0) && (isset($estimatcost) && $estimatcost > 0 && $estimatcost < $projectbudget) && (isset($spendcost) && $spendcost > 0 && $spendcost > $projectbudget)) {
			// this is second new condition

			$costStatus = '<span style="color:#FF0000; cursor: pointer;" class="tipText filterD" title="Spending Over Budget">Over Budget</span>';

		} else if ((isset($projectbudget) && $projectbudget > 0) && (isset($estimatcost) && $estimatcost > 0 && $estimatcost > $projectbudget) && (!isset($spendcost) || $spendcost <= 0)) {

			$costStatus = '<span style="color:#FF0000; cursor: pointer;" class="tipText filterD" title="Estimate Over Budget, Spending Below Budget">On Budget, at Risk</span>';

		}

		return $costStatus;
	}


	// Get Project Propagation Data with current User
	public function ending_elements($project_id = null, $dates = null) {

		$data = null;

		if (!isset($project_id) || empty($project_id)) {
			return $data;
		}

		$starting_date = $ending_date = '';

		if (isset($dates) && !empty($dates)) {

			$d = explode(' - ', $dates);
			if (is_array($d)) {
				$starting_date = (isset($d[0])) ? date('Y-m-d', strtotime($d[0])) : '';
				$ending_date = (isset($d[1])) ? date('Y-m-d', strtotime($d[1])) : '';
			} else {
				$starting_date = $dates;
			}

		}

		$workspaces = $elements = $area_ids = null;
		// echo $project_id;
		$workspaces = $this->get_project_workspace($project_id);

		if (isset($workspaces) && !empty($workspaces)) {

			foreach ($workspaces as $key => $value) {

				$workspace = $value['Workspace'];

				$areas = $this->workspace_areas($workspace['id'], false, true);

				if (isset($areas) && !empty($areas)) {
					if (is_array($area_ids)) {
						$area_ids = array_merge($area_ids, array_values($areas));
					} else {
						$area_ids = array_values($areas);
					}

				}

			}
		}
		$query = '';

		if (isset($area_ids) && !empty($area_ids)) {

			$twoWeeks = date("Y-m-d", strtotime(date('Y-m-d') . " +14 Days"));

			$order = '';
			$query .= 'SELECT element.*, TIMESTAMPDIFF( DAY, element.start_date, element.end_date) AS totalDays ';
			$query .= 'FROM elements as element ';
			$query .= "WHERE element.area_id IN (" . implode(',', $area_ids) . ") AND element.studio_status != '1'";
			$order = "ORDER BY totalDays ASC ";
			if (isset($dates) && !empty($dates)) {
				$order = "ORDER BY totalDays ASC ";
				if (!empty($starting_date) && empty($ending_date)) {
					$query .= "AND (((date(element.start_date) BETWEEN '" . $starting_date . "' AND '" . $starting_date . "') OR (date(element.end_date) BETWEEN '" . $starting_date . "' AND '" . $starting_date . "')) OR (date(element.start_date) <= '" . $starting_date . "' AND date(element.end_date) >= '" . $starting_date . "') ) ";
					// $query .= "AND (date(element.start_date) BETWEEN '" . $starting_date . "' AND '" . $starting_date . "') ";
				} else if (empty($starting_date) && !empty($ending_date)) {
					$query .= "AND (date(element.end_date) BETWEEN '" . $ending_date . "' AND '" . $ending_date . "') ";
				} else if (!empty($starting_date) && !empty($ending_date)) {
					$query .= "AND (( (date(element.start_date) BETWEEN '" . $starting_date . "' AND '" . $ending_date . "') ";
					$query .= "OR (date(element.end_date) BETWEEN '" . $starting_date . "' AND '" . $ending_date . "')) OR (date(element.start_date) <= '" . $starting_date . "' AND date(element.end_date) >= '" . $ending_date . "') ) ";

				}

				$query .= "AND element.date_constraints = 1 ";
				$query .= "AND element.sign_off != 1 ";
				$query .= $order;
				// pr($query);
				$data = $this->_elements->query($query);
				//pr($data);

			} else {
				$overdue_query = '';
				$overdue_query .= 'SELECT element.* ';
				$overdue_query .= 'FROM elements as element ';
				$overdue_query .= "WHERE element.area_id IN (" . implode(',', $area_ids) . ") ";
				$overdue_query .= "AND date(element.end_date) < '" . date('Y-m-d') . "' ";
				$overdue_query .= "AND element.sign_off != 1 ";
				$overdue_query .= "ORDER BY date(element.end_date) ASC";

				$overdues = $this->_elements->query($overdue_query);

				if (isset($overdues) && !empty($overdues)) {
					$overdue_id = Set::extract($overdues, '/element/id');
					$query .= "AND element.id NOT IN ('" . implode(',', $overdue_id) . "') ";
				}

				$query .= "AND date(element.end_date) BETWEEN '" . date('Y-m-d') . "' AND '" . $twoWeeks . "'  ";

				$query .= "AND element.date_constraints = 1 ";
				$query .= "AND element.sign_off != 1 ";
				$query .= $order;

				$data1 = $this->_elements->query($query);

				if (isset($overdues) && !empty($overdues)) {
					$data = array_merge($overdues, $data1);
				} else {
					$data = $data1;
				}

			}
		}

		return $data;
	}

	public function elements_overdue_total($project_id = null) {

		$data = null;

		if (!isset($project_id) || empty($project_id)) {
			return $data;
		}

		$starting_date = $ending_date = '';

		$workspaces = $elements = $area_ids = null;
		// echo $project_id;
		$workspaces = $this->get_project_workspace($project_id);

		if (isset($workspaces) && !empty($workspaces)) {

			foreach ($workspaces as $key => $value) {

				$workspace = $value['Workspace'];

				$areas = $this->workspace_areas($workspace['id'], false, true);

				if (isset($areas) && !empty($areas)) {
					if (is_array($area_ids)) {
						$area_ids = array_merge($area_ids, array_values($areas));
					} else {
						$area_ids = array_values($areas);
					}

				}

			}
		}
		$query = '';

		if (isset($area_ids) && !empty($area_ids)) {

			$fiveDays = date("Y-m-d", strtotime(date('Y-m-d') . " +5 Days"));

			$order = '';
			$query .= 'SELECT element.*, TIMESTAMPDIFF( DAY, element.start_date, element.end_date) AS totalDays ';
			$query .= 'FROM elements as element ';
			$query .= "WHERE element.area_id IN (" . implode(',', $area_ids) . ") AND element.studio_status != '1'";
			$order = "ORDER BY totalDays ASC ";

			$query .= "AND date(element.end_date) BETWEEN '" . date('Y-m-d') . "' AND '" . $fiveDays . "' ";

			$query .= "AND element.date_constraints = 1 ";
			$query .= "AND element.sign_off != 1 ";
			$query .= $order;

			$data = $this->_elements->query($query);

		}

		return $data;


	}

	public function elements_overdue($project_id = null) {

		$data = null;

		if (!isset($project_id) || empty($project_id)) {
			return $data;
		}

		$starting_date = $ending_date = '';

		$workspaces = $elements = $area_ids = null;
		// echo $project_id;
		$workspaces = $this->get_project_workspace($project_id);

		if (isset($workspaces) && !empty($workspaces)) {

			foreach ($workspaces as $key => $value) {

				$workspace = $value['Workspace'];

				$areas = $this->workspace_areas($workspace['id'], false, true);

				if (isset($areas) && !empty($areas)) {
					if (is_array($area_ids)) {
						$area_ids = array_merge($area_ids, array_values($areas));
					} else {
						$area_ids = array_values($areas);
					}

				}

			}
		}
		$query = '';

		if (isset($area_ids) && !empty($area_ids)) {

			$order = '';
			$query .= 'SELECT element.*, TIMESTAMPDIFF( DAY, element.start_date, element.end_date) AS totalDays ';
			$query .= 'FROM elements as element ';
			$query .= "WHERE element.area_id IN (" . implode(',', $area_ids) . ") AND element.studio_status != '1'";
			$order = "ORDER BY totalDays ASC ";

			$query .= "AND date(element.end_date) < '" . date('Y-m-d') . "' ";

			$query .= "AND element.date_constraints = 1 ";
			$query .= "AND element.sign_off != 1 ";
			$query .= $order;

			$data = $this->_elements->query($query);

		}

		return $data;
	}

	// Get Project Propagation Data with current User
	public function element_status_counts($project_id = null, $workspace_id = null) {

		$ws = null;
		if (isset($project_id) && !empty($project_id)) {
			$workspaces = $this->get_project_workspace($project_id);
			if (isset($workspaces) && !empty($workspaces)) {
				foreach ($workspaces as $wk => $wv) {
					$ws[] = $wv['Workspace']['id'];
				}
			}
		}
		if (!isset($workspace_id) || empty($workspace_id)) {
			$workspace_id = $ws;
		}

		$areas = $this->workspace_areas($workspace_id, false, true);

		$elements = $this->area_elements($areas);

		$not_spacified = $not_started = $progressing = $completed = $overdue = 0;

		foreach ($elements as $ek => $result) {

			if (isset($result['Element']['date_constraints']) && !empty($result['Element']['date_constraints']) && $result['Element']['date_constraints'] > 0) {
				if ((isset($result['Element']['start_date']) && !empty($result['Element']['start_date'])) && date('Y-m-d', strtotime($result['Element']['start_date'])) > date('Y-m-d')) {
					$not_started++;
				} else if ((isset($result['Element']['end_date']) && !empty($result['Element']['end_date'])) && date('Y-m-d', strtotime($result['Element']['end_date'])) < date('Y-m-d')) {
					$overdue++;
				} else if (isset($result['Element']['sign_off']) && !empty($result['Element']['sign_off']) == 1) {
					$completed++;
				} else if (((isset($result['Element']['end_date']) && !empty($result['Element']['end_date'])) && (isset($result['Element']['start_date']) && !empty($result['Element']['start_date']))) && (date('Y-m-d', strtotime($result['Element']['start_date'])) <= date('Y-m-d')) && date('Y-m-d', strtotime($result['Element']['end_date'])) >= date('Y-m-d')) {
					$progressing++;
				} else {
					$not_spacified++;
				}
			}

		}
		if( isset($elements) && !empty($elements) ){
			e(count($elements));
		}
		e($not_spacified . ', ' . $not_started . ', ' . $progressing . ', ' . $completed . ', ' . $overdue);

		//

		// return (isset($data) && !empty($data)) ? true : false;
	}

	// Get Task List Results
	public function getTaskListElements($options = null) {

		$query = '';
		$diff = '';
		$from = '';
		$order = '';

		$data = null;

		if (isset($options['area']) && !empty($options['area'])) {

			$select = 'SELECT element.*';

			$diff = ', TIMESTAMPDIFF( DAY, element.`start_date`, element.`end_date`) AS totalDays ';

			$from = 'FROM elements as element ';

			$query .= "WHERE element.area_id = '" . $options['area'] . "' AND element.studio_status = '0' ";

			if ((isset($options['sort_by']) && !empty($options['sort_by']))) {

				$sort_by = $options['sort_by'];

				// Ending Soonest
				if ($sort_by == 1) {
					$diff = ', TIMESTAMPDIFF( DAY, now(), element.`end_date`) AS totalDays ';
					$query .= "AND date(element.end_date) >= '" . date('Y-m-d') . "' ";
					$query .= "AND element.sign_off != '1' ";

					$order = 'ORDER BY totalDays ASC';
				}
				// Ending Last
				if ($sort_by == 2) {
					$query .= "AND date(element.modified) <= '" . date('Y-m-d') . "' ";
					$query .= "AND element.sign_off = '1' ";

					$order = 'ORDER BY totalDays ASC';
				}

			}
			if ((isset($options['task_status']) && !empty($options['task_status']))) {

				$task_status = $options['task_status'];

				if ($task_status == 1) {
					// not spacified
					$query .= "AND ( element.date_constraints IS NULL OR element.date_constraints = 0 ) ";
					$query .= "AND element.sign_off != 1 ";
				} else if ($task_status == 2) {
					// not started
					$diff = ', TIMESTAMPDIFF( DAY, now(), element.`start_date`) AS totalDays ';
					$query .= "AND date(element.start_date) > '" . date('Y-m-d') . "' ";
					$query .= "AND element.date_constraints = 1 ";
					$query .= "AND element.sign_off != 1 ";
					$order = 'ORDER BY totalDays ASC';
				} else if ($task_status == 3) {
					// progressing
					$query .= "AND date(element.start_date) <= '" . date('Y-m-d') . "' ";
					$query .= "AND date(element.end_date) >= '" . date('Y-m-d') . "' ";
					$query .= "AND element.date_constraints = 1 ";
					$query .= "AND element.sign_off != 1 ";
				} else if ($task_status == 4) {
					// completed
					$query .= "AND element.sign_off = 1 ";
				} else if ($task_status == 5) {
					// overdue
					$diff = ', TIMESTAMPDIFF( DAY, element.`end_date`, now()) AS totalDays ';
					$query .= "AND date(element.end_date) < '" . date('Y-m-d') . "' ";
					$query .= "AND element.sign_off != 1 ";
					$query .= "AND element.date_constraints = 1 ";
					$order = 'ORDER BY totalDays DESC';
				}
			}

			// e( $select.$diff.$from.$query . '' . $order , 1);
			$data = $this->_elements->query($select . $diff . $from . $query . '' . $order);
		}

		return (isset($data) && !empty($data)) ? $data : null;

	}

	// Get Project Propagation Data with current User
	public function getTotalVoteResults($vote_id = null) {
		App::import("Model", "VoteResult");
		$vr = new VoteResult();

		$data = 0;

		if (!isset($vote_id) || empty($vote_id)) {
			return $data;
		}

		$data = $vr->find('count', ['conditions' => ['VoteResult.vote_id' => $vote_id], 'group' => ['VoteResult.user_id', 'VoteResult.vote_id', 'VoteResult.vote_question_id']]);

		return (isset($data) && !empty($data)) ? $data : 0;

	}

	// Get User's Own Projects
	public function my_projects_list($fields = null, $align_id = null) {

		$user_id = $this->Session->read('Auth.User.id');

		$conditions = null;
		$conditions['UserProject.user_id'] = $user_id;
		$conditions['UserProject.status'] = 1;
		$conditions['UserProject.project_id !='] = '';
		$conditions['Project.studio_status'] = 0;
		if (!isset($fields) || empty($fields)) {
			$fields = ['UserProject.*', 'Project.*'];
		}

		if (isset($align_id) && !empty($align_id)) {
			$conditions['Project.aligned_id'] = $align_id;
		}

		$projects = $this->_projects->find('all', array(
			'joins' => array(
				array(
					'table' => 'user_projects',
					'alias' => 'UserProject',
					'type' => 'INNER',
					'conditions' => array(
						'UserProject.project_id = Project.id',
					),
				),
			),
			'conditions' => $conditions,
			'fields' => $fields,
			'order' => 'UserProject.modified DESC',
			'group' => ['UserProject.project_id'],
			'recursive' => -1,
		));

		return (isset($projects) && !empty($projects)) ? $projects : false;

	}

	// Get User's Shared Projects
	public function shared_projects_list($fields = null, $align_id = null) {

		$user_id = $this->Session->read('Auth.User.id');

		$conditions = null;
		$conditions['ProjectPermission.owner_id'] = $user_id;
		$conditions['AND'] = ['ProjectPermission.user_id IS NOT NULL'];
		$conditions['OR'] = ['ProjectPermission.parent_id IS NULL', 'ProjectPermission.parent_id' => 0];

		if (!isset($fields) || empty($fields)) {
			$fields = ['UserProject.*', 'Project.*'];
		}

		$permit_projects = $this->_pr_permit->find('all', array(
			'joins' => array(
				array(
					'table' => 'user_projects',
					'alias' => 'UserProject',
					'type' => 'INNER',
					'conditions' => array(
						'UserProject.id = ProjectPermission.user_project_id',
					),
				),
			),
			'conditions' => $conditions,
			//'fields' => ['ProjectPermission.user_project_id','ProjectPermission.id'],
			'group' => ['ProjectPermission.user_project_id'],
			'recursive' => -1,
		));

		if (isset($permit_projects) && !empty($permit_projects)) {

			$userProjectId = Set::extract($permit_projects, '/ProjectPermission/user_project_id');

			foreach ($permit_projects as $k => $v) {

				$pconditions['UserProject.id'] = $v['ProjectPermission']['user_project_id'];
				if (isset($align_id) && !empty($align_id)) {
					$pconditions['Project.aligned_id'] = $align_id;
				}

				$projects = $this->_projects->find('first', array(
					'joins' => array(
						array(
							'table' => 'user_projects',
							'alias' => 'UserProject',
							'type' => 'INNER',
							'conditions' => array(
								'UserProject.project_id = Project.id',
							),
						),
					),
					'conditions' => $pconditions,
					'fields' => $fields,
					'order' => 'UserProject.modified DESC',
					'group' => ['UserProject.project_id'],
					'recursive' => -1,
				));
				if (isset($projects['Project']) && !empty($projects['Project'])) {
					$projects = $projects['Project'];
					$data[] = ['ProjectPermission' => $v['ProjectPermission'], 'Project' => $projects];
				}
			}

		}
		// pr($data, 1);
		return (isset($data) && !empty($data)) ? $data : false;

	}

	// Get User's Received Projects
	public function received_projects_list($fields = null, $align_id = null) {

		$user_id = $this->Session->read('Auth.User.id');

		$conditions = null;
		$conditions['ProjectPermission.user_id'] = $user_id;
		$conditions['AND'] = ['ProjectPermission.user_id IS NOT NULL'];
		$conditions['AND'] = ['ProjectPermission.share_by_id IS NOT NULL'];
		$conditions['AND'] = ['ProjectPermission.user_project_id IS NOT NULL'];
		$conditions['AND'] = ['ProjectPermission.owner_id IS NOT NULL'];

		if (!isset($fields) || empty($fields)) {
			$fields = ['UserProject.*', 'Project.*'];
		}

		$permit_projects = $this->_pr_permit->find('all', array(
			'joins' => array(
				array(
					'table' => 'user_projects',
					'alias' => 'UserProject',
					'type' => 'INNER',
					'conditions' => array(
						'UserProject.id = ProjectPermission.user_project_id',
					),
				),
			),
			'conditions' => [
				'ProjectPermission.user_id' => $user_id,
				'ProjectPermission.user_id IS NOT NULL',
				'ProjectPermission.share_by_id IS NOT NULL',
				'ProjectPermission.user_project_id IS NOT NULL',
				'ProjectPermission.user_project_id IS NOT NULL',
			],
			// 'fields' => ['ProjectPermission.user_project_id'],
			'group' => ['ProjectPermission.user_project_id'],
			'recursive' => -1,
		));

		if (isset($permit_projects) && !empty($permit_projects)) {

			$userProjectId = Set::extract($permit_projects, '/ProjectPermission/user_project_id');
			foreach ($permit_projects as $k => $v) {

				$pconditions['UserProject.id'] = $v['ProjectPermission']['user_project_id'];
				if (isset($align_id) && !empty($align_id)) {
					$pconditions['Project.aligned_id'] = $align_id;
				}

				$projects = $this->_projects->find('first', array(
					'joins' => array(
						array(
							'table' => 'user_projects',
							'alias' => 'UserProject',
							'type' => 'INNER',
							'conditions' => array(
								'UserProject.project_id = Project.id',
							),
						),
					),
					'conditions' => $pconditions,
					'fields' => $fields,
					'order' => 'UserProject.modified DESC',
					'group' => ['UserProject.project_id'],
					'recursive' => -1,
				));
				if (isset($projects['Project']) && !empty($projects['Project'])) {
					$projects = $projects['Project'];
					$data[] = ['ProjectPermission' => $v['ProjectPermission'], 'Project' => $projects];
				}
			}

		}
		// pr($data, 1);
		return (isset($data) && !empty($data)) ? $data : false;

	}

	// Get User's Group Received Projects
	public function group_received_projects_list($fields = null, $align_id = null) {

		$user_id = $this->Session->read('Auth.User.id');

		App::import("Model", "ProjectGroupUser");
		$projectgroupuser = new ProjectGroupUser();

		$data = $conditions = null;
		$conditions['ProjectPermission.user_id'] = $user_id;

		if (!isset($fields) || empty($fields)) {
			$fields = ['UserProject.*', 'Project.*'];
		}

		$permit_projects = $projectgroupuser->find('all', ['conditions' => ['ProjectGroupUser.user_id' => $user_id, 'ProjectGroupUser.approved' => 1], 'recursive' => 1]);

		if (isset($permit_projects) && !empty($permit_projects)) {

			$userProjectId = Set::extract($permit_projects, '/UserProject/id');

			foreach ($permit_projects as $k => $v) {

				$pconditions['UserProject.id'] = $v['ProjectGroup']['user_project_id'];
				if (isset($align_id) && !empty($align_id)) {
					$pconditions['Project.aligned_id'] = $align_id;
				}

				$projects = $this->_projects->find('first', array(
					'joins' => array(
						array(
							'table' => 'user_projects',
							'alias' => 'UserProject',
							'type' => 'INNER',
							'conditions' => array(
								'UserProject.project_id = Project.id',
							),
						),
					),
					'conditions' => $pconditions,
					'fields' => $fields,
					'order' => 'UserProject.modified DESC',
					'group' => ['UserProject.project_id'],
					'recursive' => -1,
				));

				if (isset($projects['Project']) && !empty($projects['Project'])) {
					$projects = $projects['Project'];
					$data[] = ['ProjectGroup' => $v['ProjectGroup'], 'ProjectGroupUser' => $v['ProjectGroupUser'], 'Project' => $projects];
				}

			}
		}

		return (isset($data) && !empty($data)) ? $data : false;

	}

	// Get User's Group Received Projects
	public function propagated_projects_list($fields = null, $align_id = null) {

		$user_id = $this->Session->read('Auth.User.id');

		App::import("Model", "ProjectGroupUser");
		$projectgroupuser = new ProjectGroupUser();

		$data = $conditions = null;

		$conditions['ProjectPermission.share_by_id'] = $user_id;
		$conditions['ProjectPermission.owner_id !='] = $user_id;
		$conditions['AND'] = ['ProjectPermission.user_id  IS NOT NULL'];
		$conditions['OR'] = ['ProjectPermission.parent_id IS NOT NULL', 'ProjectPermission.parent_id !=' => 0];

		if (!isset($fields) || empty($fields)) {
			$fields = ['UserProject.*', 'Project.*'];
		}

		$permit_projects = $this->_pr_permit->find('all', array(
			'joins' => array(
				array(
					'table' => 'user_projects',
					'alias' => 'UserProject',
					'type' => 'INNER',
					'conditions' => array(
						'UserProject.id = ProjectPermission.user_project_id',
					),
				),
			),
			'conditions' => $conditions,
			// 'fields' => ['ProjectPermission.user_project_id'],
			'group' => ['ProjectPermission.user_project_id'],
			'recursive' => -1,
		));
		// pr($permit_projects,1);
		if (isset($permit_projects) && !empty($permit_projects)) {

			$userProjectId = Set::extract($permit_projects, '/ProjectPermission/user_project_id');

			foreach ($permit_projects as $k => $v) {

				$pconditions['UserProject.id'] = $v['ProjectPermission']['user_project_id'];
				if (isset($align_id) && !empty($align_id)) {
					$pconditions['Project.aligned_id'] = $align_id;
				}

				$projects = $this->_projects->find('first', array(
					'joins' => array(
						array(
							'table' => 'user_projects',
							'alias' => 'UserProject',
							'type' => 'INNER',
							'conditions' => array(
								'UserProject.project_id = Project.id',
							),
						),
					),
					'conditions' => $pconditions,
					'fields' => $fields,
					'order' => 'UserProject.modified DESC',
					'group' => ['UserProject.project_id'],
					'recursive' => -1,
				));

				if (isset($projects['Project']) && !empty($projects['Project'])) {
					$projects = $projects['Project'];
					$data[] = ['ProjectPermission' => $v['ProjectPermission'], 'Project' => $projects];
				}

			}
		}
		// pr($data, 1);
		return (isset($data) && !empty($data)) ? $data : false;

	}

	// Get Project Propagation Data with current User
	public function project_permission_detail($conditions = null, $fields = null) {

		$data = null;

		if (!isset($fields) || empty($fields)) {
			$fields = ['ProjectPermission.*'];
		}

		if (!empty($conditions) && !empty($conditions)) {

			$data = $this->_pr_permit->find('first', [
				'conditions' => $conditions,
				'fields' => $fields,
				'recursive' => -1,
			]);

		}

		return (isset($data) && !empty($data)) ? $data : false;
	}

	// Get Project Propagation Data with current User
	public function getAreaTemplate($workspace_id = null) {

		$data = null;

		if (!isset($fields) || empty($fields)) {
			$fields = ['ProjectPermission.*'];
		}

		App::import("Model", "TemplateDetail");
		$_templateDetail = new TemplateDetail();

		$workspace = $this->_workspaces->find('first', [
			'conditions' => [
				'Workspace.id' => $workspace_id,
			],
			'fields' => 'template_id',
			'recursive' => -1,
		]);

		$template_groups = $_templateDetail->find('all', array(
			'fields' => 'DISTINCT row_no, id',
			'conditions' => array(
				'TemplateDetail.template_id' => $workspace['Workspace']['template_id'],
			),
		));

		$grouped_ids = Set::extract($template_groups, '/TemplateDetail/id');

		$area_template_data = $this->_workspaces->Area->find('all', [
			'fields' => [
				'Area.id as area_id', 'Area.workspace_id', 'Area.title', 'Area.status', 'TemplateDetail.id as temp_detail_id', 'TemplateDetail.row_no', 'TemplateDetail.col_no', 'TemplateDetail.size_w', 'TemplateDetail.size_h', 'TemplateDetail.elements_counter', 'TemplateDetail.template_id',
			],
			'conditions' => [
				'Area.workspace_id' => $workspace_id,
				'Area.template_detail_id' => $grouped_ids,
			],
			'recursive' => 2,
			'order' => ['TemplateDetail.id ASC', 'TemplateDetail.row_no ASC'],
		]);

		foreach ($area_template_data as $row_id => $row_templates) {
			$area_detail = $row_templates['Area'];
			$temp_detail = $row_templates['TemplateDetail'];

			if ($temp_detail['size_w'] > 0 && $temp_detail['size_h'] > 0) {
				$row_no = $temp_detail['row_no'];
				$area_templates = array_merge($temp_detail, $area_detail);

				$data[$row_no][] = $area_templates;
			}
		}

		return (isset($data) && !empty($data)) ? $data : false;
	}

	// Get Project Propagation Data with current User
	public function getAreaTemplateData($template_id = null) {

		$data = null;

		App::import("Model", "TemplateDetail");
		$_templateDetail = new TemplateDetail();

		$template_groups = $_templateDetail->find('all', array(
			'fields' => 'DISTINCT row_no, id, row_no, col_no, size_w, size_h',
			'conditions' => array(
				'TemplateDetail.template_id' => $template_id,
			),
		));

		/* $grouped_ids = Set::extract($template_groups, '/TemplateDetail/id');
			pr($grouped_ids);
			$area_template_data = $this->_workspaces->Area->find('all', [
				'fields' => [
					'Area.id as area_id', 'Area.workspace_id', 'Area.title', 'Area.status', 'TemplateDetail.id as temp_detail_id', 'TemplateDetail.row_no', 'TemplateDetail.col_no', 'TemplateDetail.size_w', 'TemplateDetail.size_h', 'TemplateDetail.elements_counter', 'TemplateDetail.template_id',
				],
				'conditions' => [
					'Area.workspace_id' => $workspace_id,
					'Area.template_detail_id' => $grouped_ids,
				],
				'recursive' => 2,
				'order' => ['TemplateDetail.id ASC', 'TemplateDetail.row_no ASC']
		*/

		foreach ($template_groups as $row_id => $row_templates) {
			// $area_detail = $row_templates['Area'];
			$temp_detail = $row_templates['TemplateDetail'];
			// pr($temp_detail);

			if ($temp_detail['size_w'] > 0 && $temp_detail['size_h'] > 0) {
				$row_no = $temp_detail['row_no'];
				// $area_templates = array_merge($temp_detail, $area_detail);

				$data[$row_no][] = $temp_detail;
			}
		}

		return (isset($data) && !empty($data)) ? $data : false;
	}

	// Get Element Users
	public function element_users($id = null) {

		$data = null;

		if (!isset($id) || empty($id)) {
			return false;
		}
		// e($id);
		$data = $this->_el_permit->find('all', [
			'conditions' => ['ElementPermission.element_id' => $id],
			'group' => ['ElementPermission.user_id'],
		]);
		// pr($data); die;
		return (isset($data) && !empty($data)) ? $data : false;
	}

	// this function is not using in any files pawan
	public function element_users_permission($element_id = null) {

		//$current_user_id = $this->Session->read('Auth.User.id');
		$query = "SELECT
				user_permissions.role,user_permissions.user_id,user_permissions.project_id,user_permissions.element_id,
				elements.id,elements.title

			FROM `user_permissions`
			inner join elements on elements.id = user_permissions.element_id
			WHERE user_permissions.element_id = $element_id and element_id is not null order by role ASC";
		return ClassRegistry::init('UserPermission')->query($query);

	}

	public function element_participants($element_id = null, $cuser = false) {
		// e($element_id);
		$view = new View();
		$common = $view->loadHelper('Common');
		$users = [];

		$project_id = element_project($element_id);
		$current_user = $this->Session->read('Auth.User.id');

		/* $owner = $common->ProjectOwner($project_id, $current_user);
		$participants_owners = (isset($owner['UserProject']['user_id']) && !empty($owner['UserProject']['user_id'])) ? participants_owners($project_id, $owner['UserProject']['user_id']) : null;

		$participants_group_owner = participants_group_owner($project_id);
		$participants_group_sharer = participants_group_sharer($project_id);

		$participantsOwners = isset($participants_owners) ? array_filter($participants_owners) : $participants_owners;
		$participantsGpOwner = isset($participants_group_owner) ? array_filter($participants_group_owner) : $participants_group_owner;
		$participantsGpSharer = isset($participants_group_sharer) ? array_filter($participants_group_sharer) : $participants_group_sharer;

		$sharedData = $this->element_users($element_id);

		//pr($sharedData);
		$sharers = [];
		if (isset($sharedData) && !empty($sharedData)) {
			// pr($sharedData);
			foreach ($sharedData as $key => $val) {
				$d = $val['ElementPermission'];
				//pr($val);
				if ($cuser) {
					$sharers[] = $d['user_id'];
				} else {
					if ($current_user != $d['user_id']) {
						$sharers[] = $d['user_id'];
					}
				}
			}
		}
		$users = ['participantsOwners' => $participantsOwners, 'participantsGpOwner' => $participantsGpOwner, 'participantsGpSharer' => $participantsGpSharer, 'sharers' => $sharers]; */

		$sharedData_per = $this->element_users_permission($element_id);
		$shared_users = array();
		if( isset($sharedData_per) && !empty($sharedData_per) ){
			foreach($sharedData_per as $list_users){

				if( $list_users['user_permissions']['role'] == 'Creator' || $list_users['user_permissions']['role'] == 'Owner'  ){
					$shared_users['participantsOwners'][] = $list_users['user_permissions']['user_id'];
				}

				if( $list_users['user_permissions']['role'] == 'Group Owner'  ){
					$shared_users['participantsGpOwner'][] = $list_users['user_permissions']['user_id'];
				}

				if( $list_users['user_permissions']['role'] == 'Group Sharer'  ){
					$shared_users['participantsGpSharer'][] = $list_users['user_permissions']['user_id'];
				}

				if( $list_users['user_permissions']['role'] == 'Sharer'  ){
					$shared_users['sharers'][] = $list_users['user_permissions']['user_id'];
				}
			}
		}
		// return isset($users) ? $users : array();
		return isset($shared_users) ? $shared_users : array();
	}

	public function element_all_users($element_id = null) {
		// e($element_id);
		$view = new View();
		$common = $view->loadHelper('Common');
		$users = [];

		$project_id = element_project($element_id);

		$owner = $common->ProjectOwner($project_id);
		$participants_owners = (isset($owner['UserProject']['user_id']) && !empty($owner['UserProject']['user_id'])) ? participants_owners($project_id, $owner['UserProject']['user_id']) : null;

		$participants_group_owner = participants_group_owner($project_id);

		$participantsOwners = isset($participants_owners) ? array_filter($participants_owners) : $participants_owners;
		$participantsGpOwner = isset($participants_group_owner) ? array_filter($participants_group_owner) : $participants_group_owner;
		$sharedData = $this->element_users($element_id);

		$sharers = [];
		if (isset($sharedData) && !empty($sharedData)) {
			// pr($sharedData);
			foreach ($sharedData as $key => $val) {
				$d = $val['ElementPermission'];
				$sharers[] = $d['user_id'];
			}
		}

		$participantsOwners = (is_array($participantsOwners) ? $participantsOwners : array());
		$sharers = (is_array($sharers) ? $sharers : array());
		$participantsGpOwner = (is_array($participantsGpOwner) ? $participantsGpOwner : array());
		$users = array_unique(array_merge($participantsOwners, $sharers, $participantsGpOwner));

		// pr($users);
		return isset($users) ? $users : array();
	}

	public function getWorkspaceTemplateDetails($template_id = null) {

		$data = null;

		App::import("Model", "Template");
		$_template = new Template();

		$data = $_template->find('first', array(
			'conditions' => array(
				'Template.id' => $template_id,
			),
		));

		return (isset($data) && !empty($data)) ? $data : false;
	}

	public function getProjectBlog($project_id = null, $user_id = null) {

		$data = null;

		if (!isset($project_id) || empty($project_id)) {
			return false;
		}

		App::import("Model", "Blog");
		$_blog = new Blog();

		$conditions['Blog.project_id'] = $project_id;
		if (isset($user_id) && !empty($user_id)) {
			$conditions['Blog.user_id'] = $user_id;
		}

		$data = $_blog->find('all', [
			'conditions' => $conditions,
			'order' => ['Blog.created DESC'],
			'limit' => 20,
		]
		);

		// pr($data);
		return $data;
	}

	public function getBlogLikes($blog_id = null) {

		$data = null;

		if (!isset($blog_id) || empty($blog_id)) {
			return false;
		}

		App::import("Model", "BlogLike");
		$_bcl = new BlogLike();

		$data = $_bcl->find('count', [
			'conditions' => ['BlogLike.blog_id' => $blog_id],
		]);

		return (isset($data) && !empty($data)) ? $data : 0;
	}

	public function getProjectComments($project_id = null, $user_id = null) {

		$data = null;

		if (!isset($project_id) || empty($project_id)) {
			return null;
		}

		App::import("Model", "ProjectComment");
		$_pc = new ProjectComment();

		$last5 = date("Y-m-d h:i:s", strtotime(date('Y-m-d h:i:s') . " -5 Days"));

		$conditions['ProjectComment.project_id'] = $project_id;
		if (isset($user_id) && !empty($user_id)) {
			$conditions['ProjectComment.user_id'] = $user_id;
		}

		$data = $_pc->find('all', ['conditions' => $conditions,
			'order' => ['ProjectComment.modified DESC'],
			'limit' => 20,
		]);

		return (isset($data) && !empty($data)) ? $data : null;
	}

	public function getTodoComments($project_id = null, $user_id = null) {

		$data = null;

		if (!isset($project_id) || empty($project_id)) {
			return false;
		}

		App::import("Model", "DoList");
		$_dolist = new DoList();
		App::import("Model", "DoListComment");
		$_dolist_cmt = new DoListComment();

		$bdata = $_dolist->find('all', [
			'conditions' => ['DoList.project_id' => $project_id],
		]);

		$todo_id = Set::extract($bdata, '/DoList/id');

		if (isset($todo_id) && !empty($todo_id)) {

			$last5 = date("Y-m-d h:i:s", strtotime(date('Y-m-d h:i:s') . " -5 Days"));

			$conditions['DoListComment.do_list_id'] = $todo_id;
			if (isset($user_id) && !empty($user_id)) {
				$conditions['DoListComment.user_id'] = $user_id;
			}

			$data = $_dolist_cmt->find('all', [
				'conditions' => $conditions,
				'order' => ['DoListComment.modified DESC'],
				'limit' => 20,
			]);
		}

		return $data;
	}

	public function getTodoCommentLikes($comment_id = null) {

		$data = null;

		if (!isset($comment_id) || empty($comment_id)) {
			return false;
		}

		App::import("Model", "DoListCommentLike");
		$_dlcl = new DoListCommentLike();

		$data = $_dlcl->find('count', [
			'conditions' => ['DoListCommentLike.do_list_comment_id' => $comment_id],
		]);

		return (isset($data) && !empty($data)) ? $data : 0;
	}

	public function getTodoCommentAttachments($comment_id = null) {

		$data = null;

		if (!isset($comment_id) || empty($comment_id)) {
			return false;
		}

		App::import("Model", "DoListCommentUpload");
		$_dolist = new DoListCommentUpload();

		$data = $_dolist->find('all', [
			'conditions' => ['DoListCommentUpload.do_list_comment_id' => $comment_id],
		]);

		// pr($data);
		return (isset($data) && !empty($data)) ? $data : false;
	}

	public function getBlogComments($project_id = null, $user_id = null) {

		$data = null;

		if (!isset($project_id) || empty($project_id)) {
			return false;
		}

		App::import("Model", "Blog");
		$_blog = new Blog();
		App::import("Model", "BlogComment");
		$_blog_cmt = new BlogComment();

		$bdata = $_blog->find('all', [
			'conditions' => ['Blog.project_id' => $project_id],
		]);

		$blog_id = Set::extract($bdata, '/Blog/id');

		if (isset($blog_id) && !empty($blog_id)) {

			$conditions['BlogComment.blog_id'] = $blog_id;
			if (isset($user_id) && !empty($user_id)) {
				$conditions['BlogComment.user_id'] = $user_id;
			}

			$data = $_blog_cmt->find('all', [
				'conditions' => $conditions,
				'order' => ['BlogComment.updated DESC'],
				'limit' => 20,
			]);
		}

		return $data;
	}

	public function getBlogCommentAttachments($comment_id = null) {

		$data = null;

		if (!isset($comment_id) || empty($comment_id)) {
			return false;
		}

		App::import("Model", "BlogDocument");
		$_dolist = new BlogDocument();

		$data = $_dolist->find('all', [
			'conditions' => ['BlogDocument.blog_comment_id' => $comment_id],
		]);

		// pr($data);
		return (isset($data) && !empty($data)) ? $data : false;
	}

	public function getBlogCommentLikes($comment_id = null) {

		$data = null;

		if (!isset($comment_id) || empty($comment_id)) {
			return false;
		}

		App::import("Model", "CommentLike");
		$_bcl = new CommentLike();

		$data = $_bcl->find('count', [
			'conditions' => ['CommentLike.comment_id' => $comment_id],
		]);

		return (isset($data) && !empty($data)) ? $data : 0;
	}

	public function getWikiComments($project_id = null, $user_id = null) {

		$data = null;

		if (!isset($project_id) || empty($project_id)) {
			return false;
		}

		App::import("Model", "WikiPage");
		$_wikiPage = new WikiPage();

		App::import("Model", "WikiPageComment");
		$_wiki = new WikiPageComment();

		$wikipageID = $_wikiPage->find('all', [
			'conditions' => ['WikiPage.is_archived' => 1, 'WikiPage.is_deleted !=' => 1, 'WikiPage.is_linked' => 0, 'WikiPage.project_id' => $project_id],
		]);
		if (isset($wikipageID) && !empty($wikipageID)) {
			$wikipageID = Set::extract($wikipageID, '/WikiPage/id');

			$conditions['WikiPageComment.project_id'] = $project_id;
			$conditions['WikiPageComment.wiki_page_id'] = $wikipageID;

			if (isset($user_id) && !empty($user_id)) {
				$conditions['WikiPageComment.user_id'] = $user_id;
			}

			$data = $_wiki->find('all', [
				'conditions' => $conditions,
				'order' => ['WikiPageComment.updated DESC'],
				'limit' => 20,
			]);
		}
		return (isset($data) && !empty($data)) ? $data : null;
	}

	public function getWikiCommentAttachments($comment_id = null) {

		$data = null;

		if (!isset($comment_id) || empty($comment_id)) {
			return false;
		}

		App::import("Model", "WikiPageCommentDocument");
		$_wiki = new WikiPageCommentDocument();

		$data = $_wiki->find('all', [
			'conditions' => ['WikiPageCommentDocument.wiki_page_comment_id' => $comment_id],
		]);

		// pr($data);
		return (isset($data) && !empty($data)) ? $data : false;
	}

	public function getWikiCommentLikes($comment_id = null) {

		$data = null;

		if (!isset($comment_id) || empty($comment_id)) {
			return false;
		}

		App::import("Model", "WikiPageCommentLike");
		$_wiki = new WikiPageCommentLike();

		$data = $_wiki->find('count', [
			'conditions' => ['WikiPageCommentLike.wiki_page_comment_id' => $comment_id],
		]);

		return (isset($data) && !empty($data)) ? $data : 0;
	}

	public function getWorkspaceComments($workspace_id = null, $user_id = null) {

		$data = null;

		if (!isset($workspace_id) || empty($workspace_id)) {
			return false;
		}

		$workspaceId = array_keys($workspace_id);

		App::import("Model", "WorkspaceComment");
		$_wc = new WorkspaceComment();

		$conditions['WorkspaceComment.workspace_id'] = $workspaceId;
		if (isset($user_id) && !empty($user_id)) {
			$conditions['WorkspaceComment.user_id'] = $user_id;
		}

		$data = $_wc->find('all', [
			'conditions' => $conditions,
			'order' => ['WorkspaceComment.modified DESC'],
			'limit' => 20,
		]);

		return (isset($data) && !empty($data)) ? $data : false;
	}

	public function getWorkspaceCommentLikes($comment_id = null) {

		$data = null;

		if (!isset($comment_id) || empty($comment_id)) {
			return false;
		}

		App::import("Model", "WorkspaceCommentLike");
		$_wiki = new WorkspaceCommentLike();

		$data = $_wiki->find('count', [
			'conditions' => ['WorkspaceCommentLike.workspace_comment_id' => $comment_id],
		]);

		// pr($data);
		return (isset($data) && !empty($data)) ? $data : 0;
	}

	public function getProjectElementsActivities($project_id = null, $workspace_id = null, $type = null, $user_id = null) {

		$data = null;

		if (!isset($project_id) || empty($project_id)) {
			return false;
		}

		App::import("Model", "Activity");
		$_act = new Activity();

		$types = [
			'feedback', 'votes', 'element_documents', 'element_links', 'element_notes', 'element_decisions', 'element_mindmaps', 'element_tasks',
		];
		if (isset($user_id) && !empty($user_id)) {

			$first_query = $_act->find('all', [
				'conditions' => [
					'Activity.element_type' => $type,
					'Activity.project_id' => $project_id,
					'Activity.updated_user_id >' => 0,
				],
				'joins' => [
					[
						'table' => "elements",
						'type' => 'INNER',
						'conditions' => ["Activity.element_id = elements.id"],
					],
					[
						'table' =>  $type,
						'type' => 'INNER',
						'conditions' => ["Activity.relation_id =  $type.id"],
					],
				],
				'fields' => ['MAX(Activity.id) as id'],
				'group' => ['Activity.relation_id'],
			]);


			if (isset($first_query) && !empty($first_query)) {
				$first_query = Set::extract($first_query, '{n}.0.id');

				$data = $_act->find('all', [
					'conditions' => [
						'element_type' => $type,
						'project_id' => $project_id,
						'updated_user_id' => $user_id,
						'id' => $first_query,
					],
					'order' => ['updated desc'],
					'limit' => 20,
				]);
			}
		} else {
			$first_query = $_act->find('all', [
				'conditions' => [
					'Activity.element_type' => $type,
					'Activity.project_id' => $project_id,
					'Activity.updated_user_id >' => 0,
				],
				'joins' => [
					[
						'table' => "elements",
						'type' => 'INNER',
						'conditions' => ["Activity.element_id = elements.id"],
					],
					[
						'table' =>  $type,
						'type' => 'INNER',
						'conditions' => ["Activity.relation_id =  $type.id"],
					],
				],
				'fields' => ['MAX(Activity.id) as id, elements.id as eid'],
				'group' => ['Activity.relation_id'],
			]);
			// pr($first_query);
			if (isset($first_query) && !empty($first_query)) {
				$first_query = Set::extract($first_query, '{n}.0.id');

				$data = $_act->find('all', [
					'conditions' => [
						'Activity.element_type' => $type,
						'Activity.project_id' => $project_id,
						'Activity.updated_user_id >' => 0,
						'Activity.id' => $first_query,
					],
					'joins' => [
						[
							'table' => 'elements',
							'alias' => 'Element',
							'type' => 'INNER',
							'conditions' => ['Element.id = Activity.element_id'],
						],
					],
					'order' => ['Activity.updated desc'],
					'limit' => 20,
				]);
			}
		}

		return (isset($data) && !empty($data)) ? $data : null;
	}

	public function getElementLastActivity($project_id = null, $workspace_id = null, $element_id = null) {

		$data = null;

		if (!isset($project_id) || empty($project_id)) {
			return false;
		}

		if (!isset($element_id) || empty($element_id)) {
			return false;
		}

		App::import("Model", "Activity");
		$_act = new Activity();

		$conditions['Activity.element_type'] = 'element_tasks';
		$conditions['Activity.project_id'] = $project_id;
		$conditions['Activity.workspace_id'] = $workspace_id;
		$conditions['Activity.element_id'] = $element_id;

		$data = $_act->find('first', [
			'conditions' => $conditions,
			'order' => ['updated DESC'],
			"group" => [
				"DATE_FORMAT(Activity.updated,'%Y-%m-%d %h:%i')", 'Activity.updated',
			],
		]);
		// pr($data);
		//
		return (isset($data) && !empty($data)) ? $data : null;
	}

	public function getProjectPermit($project_id = null, $slug = null) {

		$view = new View();
		$common = $view->loadHelper('Common');
		$group = $view->loadHelper('Group');

		$projects = $this->_projects->find('first', array(
			'joins' => array(
				array(
					'table' => 'user_projects',
					'alias' => 'UserProject',
					'type' => 'INNER',
					'conditions' => array(
						'UserProject.project_id = Project.id',
					),
				),
			),
			'conditions' => ['Project.id' => $project_id],
			'fields' => ['UserProject.*', 'Project.*'],
			'order' => 'UserProject.modified DESC',
			'group' => ['UserProject.project_id'],
			'recursive' => -1,
		));

		$user_project = $projects['UserProject'];
		$project = $projects['Project'];

		$permit_owner = 0;
		$permit_id = 0;

		if ($slug == 'shared_projects') {

			$permit_detail = (isset($permit_id) && !empty($permit_id)) ? getByDbId('ProjectPermission', $permit_id) : null;

			$permit_owner = $permit_detail['ProjectPermission']['project_level'];
			$permit_owner = 1;
		} else if ($slug == 'received_projects') {
			$permit_detail = (isset($permit_id) && !empty($permit_id)) ? getByDbId('ProjectPermission', $permit_id) : null;

			$permit_owner = $permit_detail['ProjectPermission']['project_level'];
		} else if ($slug == 'group_received_projects') {

			$group_detail = (isset($permit_id) && !empty($permit_id)) ? getByDbId('ProjectGroup', $permit_id) : null;

			$conditions = [
				'ProjectPermission.project_group_id' => $permit_id,
				'ProjectPermission.user_project_id' => $group_detail['ProjectGroup']['user_project_id'],
			];
			$permit_detail = $this->project_permission_detail($conditions);
			$permit_owner = $permit_detail['ProjectPermission']['project_level'];

		} else if ($slug == 'propagated_projects') {

			$permit_detail = $common->project_permission_details($project['id'], $this->Session->read('Auth.User.id'));

			$permit_owner = $permit_detail['ProjectPermission']['project_level'];

		} else {
			$permit_owner = 1;
		}
		return $permit_owner;
	}

	public function is_project_shared($pid = null, $user_id = null) {
		$data = false;

		$view = new View();
		$view_model = $view->loadHelper('ViewModel');
		$common = $view->loadHelper('Common');
		$group = $view->loadHelper('Group');

		App::import("Model", "ProjectPermission");
		$pp = new ProjectPermission();

		$p_permission = $common->project_permission_details($pid, $user_id);
		$project_permission = [];

		$user_project = $common->userproject($pid, $user_id);
		$gp_exists = $group->GroupIDbyUserID($pid, $user_id);
		if (isset($gp_exists) && !empty($gp_exists)) {
			$p_permission = $group->group_permission_details($pid, $gp_exists);
		}

		if ((isset($user_project)) && (!empty($user_project)) || (isset($p_permission['ProjectPermission']))) {
			$data = true;
		}

		return $data;
	}

	public function element_cost($element_id = null, $cost_type = 1) {
		$data = false;

		App::import("Model", "ElementCost");
		$ec = new ElementCost();
		App::import("Model", "UserElementCost");
		$uec = new UserElementCost();

		$fields = ['id', 'team_member_flag'];
		if (isset($cost_type) && !empty($cost_type)) {
			if ($cost_type == 1) {
				$fields = ['id', 'estimated_cost as cost', 'team_member_flag', 'cost_type_id'];
			} else if ($cost_type == 2) {
				$fields = ['id', 'spend_cost as cost', 'team_member_flag', 'cost_type_id'];
			}
		}

		$element_cost = $ec->find('first', ['conditions' => [
			'ElementCost.element_id' => $element_id,
			'ElementCost.estimate_spend_flag' => $cost_type,
		],
			'fields' => $fields,
			'recursive' => -1,
		]);
		if (isset($element_cost) && !empty($element_cost)) {
			$data = $element_cost;
			$user_element_cost = $uec->find('all', ['conditions' => [
				'UserElementCost.element_cost_id' => $element_cost['ElementCost']['id'],
				'UserElementCost.element_id' => $element_id,
				'UserElementCost.estimate_spend_flag' => $cost_type,
			],
				'recursive' => -1,
			]);
			if (isset($user_element_cost) && !empty($user_element_cost)) {
				$temp = null;
				foreach ($user_element_cost as $key => $value) {
					$temp[] = $value['UserElementCost'];
				}
				$temp1['UserElementCost'] = $temp;
				$data = array_merge($data, $temp1);
			}
		}
		return (isset($data) && !empty($data)) ? $data : false;
	}

	public function user_element_cost($element_id = null, $cost_type = 1) {
		$data = false;

		App::import("Model", "UserElementCost");
		$uec = new UserElementCost();

		$data = $uec->find('all', ['conditions' => [
			'UserElementCost.element_id' => $element_id,
			'UserElementCost.estimate_spend_flag' => $cost_type,
			'ElementCost.team_member_flag !=' => 1,
		],
			'recursive' => 1,
		]);

		return $data;
	}

	public function user_project_cost($user_id = null, $project_id = null) {
		$data = false;

		App::import("Model", "UserProjectCost");
		$uec = new UserProjectCost();

		$data = $uec->find('first', [
			'conditions' => [
				'UserProjectCost.user_id' => $user_id,
				'UserProjectCost.project_id' => $project_id,
			],
			'fields' => ['day_rate', 'hour_rate'],
			'recursive' => -1,
		]);

		return $data;
	}

	public function wsp_element_cost($element_id = null, $cost_type = null) {
		$data = false;

		App::import("Model", "ElementCost");
		$ec = new ElementCost();

		$fields = ['SUM(estimated_cost) as cost'];
		if (isset($cost_type) && !empty($cost_type)) {
			if ($cost_type == 1) {
				$fields = ['SUM(estimated_cost) as cost'];
			} else if ($cost_type == 2) {
				$fields = ['SUM(spend_cost) as cost'];
			}
		}
		$data = $ec->find('all', ['conditions' => [
			'ElementCost.element_id' => $element_id,
		],
			'fields' => $fields,
			'recursive' => -1,
		]);
// pr($data[0][0]['cost']);
		return (isset($data[0][0]['cost']) && !empty($data[0][0]['cost'])) ? $data[0][0]['cost'] : 0;
	}

	public function workspace_element_cost($workspace_id = null, $cost_type = null) {
		$data = false;

		App::import("Model", "ElementCost");
		$ec = new ElementCost();

		$elementids = workspace_elements($workspace_id);
		$totalcosts = 0;
		if (isset($elementids) && !empty($elementids)) {

			foreach ($elementids as $listelementids) {

				$fields = ['SUM(estimated_cost) as cost'];
				if (isset($cost_type) && !empty($cost_type)) {
					if ($cost_type == 1) {
						$fields = ['SUM(estimated_cost) as cost'];
					} else if ($cost_type == 2) {
						$fields = ['SUM(spend_cost) as cost'];
					}
				}
				$data = $ec->find('all', ['conditions' => [
					'ElementCost.element_id' => $listelementids['Element']['id'],
				],
					'fields' => $fields,
					'recursive' => -1,
				]);
				$totalcosts += (isset($data[0][0]['cost']) && !empty($data[0][0]['cost'])) ? $data[0][0]['cost'] : 0;
			}
		}
		return $totalcosts;
	}

	public function risk_users($risk_id = null) {

		App::import("Model", "RmUser");
		$ru = new RmUser();

		$data = false;

		$data = $ru->find('all', [
			'conditions' => [
				'rm_detail_id' => $risk_id,
			],
			'fields' => ['user_id'],
		]);
		if (isset($data) && !empty($data)) {
			$data = Set::extract($data, '/RmUser/user_id');
		}
		return $data;
	}

	public function risk_elements($risk_id = null) {

		App::import("Model", "RmElement");
		$re = new RmElement();

		$data = false;

		$data = $re->find('all', [
			'conditions' => [
				'rm_detail_id' => $risk_id,
			],
			'fields' => ['element_id'],
		]);
		if (isset($data) && !empty($data)) {
			$data = Set::extract($data, '/RmElement/element_id');
		}
		return $data;
	}

	public function project_signoff($project_ids = null) {

		App::import("Model", "Project");
		$prj = new Project();

		$data = false;

		$data = $prj->find('list', [
			'conditions' => [
				'id' => $project_ids,
				'sign_off !=' => 1,
			],
			'fields' => ['id', 'title'],
		]);

		return $data;
	}

	public function workspace_signoff($wsp_id = null) {

		App::import("Model", "Project");
		$prj = new Project();
		$data = $prj->query("SELECT sign_off from workspaces where id =".$wsp_id);

		$so = false;
		if(isset($data) && !empty($data)){
			$so = (!empty($data[0]['workspaces']['sign_off'])) ? true : false;
		}
		return $so;

	}

	// Get Assigned To user from element assignment
	public function getassigneduser($project_id = null, $element_id = null, $assigned_type = 'assigned_to') {

		App::import("Model", "ElementAssignment");
		$eleassign = new ElementAssignment();

		App::import("Model", "UserDetail");
		$userDetail = new UserDetail();

		$assignedfiles = $eleassign->find('first',
			array(
				'conditions' => array('ElementAssignment.project_id' => $project_id, 'ElementAssignment.element_id' => $element_id),
				'fields' => array('ElementAssignment.' . $assigned_type),
			)
		);

		if (isset($assignedfiles) && !empty($assignedfiles)) {

			if (isset($assignedfiles['ElementAssignment'][$assigned_type]) && !empty($assignedfiles['ElementAssignment'][$assigned_type])) {
				$userdetail = $userDetail->find('first', array(
					'conditions' => array('UserDetail.user_id' => $assignedfiles['ElementAssignment'][$assigned_type]),
					'fields' => array('UserDetail.full_name', 'UserDetail.user_id'),
				)
				);

				if ($userdetail['UserDetail']['user_id'] == $this->Session->read('Auth.User.id')) {
					return 'Me';
				} else if (isset($userdetail) && !empty($userdetail)) {
					return $userdetail['UserDetail']['full_name'];
				} else {
					return 'N/A';
				}
			}

		} else {
			return 'N/A';
		}

	}

	// Get Assignment By project id
	public function getAssignmentByProject($project_id = null) {

		App::import("Model", "ElementAssignment");
		$eleassign = new ElementAssignment();

		App::import("Model", "UserDetail");
		$userDetail = new UserDetail();

		$alllist = $eleassign->find('all',
			array(
				'conditions' => array('ElementAssignment.project_id' => $project_id),
				'fields' => array('ElementAssignment.element_id'),
			)
		);

		if (isset($alllist) && !empty($alllist) && count($alllist) > 0) {
			return Set::extract($alllist, '/ElementAssignment/element_id');
		} else {
			return 0;
		}

	}

	// Get Project Propagation Data with current User
	public function elements_by_date($element_id = null, $dates = null) {

		$data = null;

		$starting_date = $ending_date = '';

		if (isset($dates) && !empty($dates)) {

			$d = explode(' = ', $dates);
			if (is_array($d)) {
				$starting_date = (isset($d[0])) ? date('Y-m-d', strtotime($d[0])) : '';
				$ending_date = (isset($d[1])) ? date('Y-m-d', strtotime($d[1])) : '';
			} else {
				$starting_date = $dates;
			}

		} else {
			$starting_date = date('Y-m-d');
		}

		$query = '';

		if (isset($element_id) && !empty($element_id)) {

			$order = '';
			$query .= 'SELECT Element.*, TIMESTAMPDIFF( DAY, Element.start_date, Element.end_date) AS totalDays ';
			$query .= 'FROM elements as Element ';
			$query .= "WHERE Element.id IN (" . implode(',', $element_id) . ") AND Element.studio_status != '1'";
			$order = "ORDER BY totalDays ASC ";
			if (isset($dates) && !empty($dates)) {
				$order = "ORDER BY totalDays ASC ";
				if (!empty($starting_date) && empty($ending_date)) {
					$query .= " AND (((date(Element.start_date) BETWEEN '" . $starting_date . "' AND '" . $starting_date . "') OR (date(Element.end_date) BETWEEN '" . $starting_date . "' AND '" . $starting_date . "')) OR (date(Element.start_date) <= '" . $starting_date . "' AND date(Element.end_date) >= '" . $starting_date . "') ) ";
					// $query .= "AND (date(Element.start_date) BETWEEN '" . $starting_date . "' AND '" . $starting_date . "') ";
				} else if (empty($starting_date) && !empty($ending_date)) {
					$query .= " AND (date(Element.end_date) BETWEEN '" . $ending_date . "' AND '" . $ending_date . "') ";
				} else if (!empty($starting_date) && !empty($ending_date)) {
					$query .= " AND (( (date(Element.start_date) BETWEEN '" . $starting_date . "' AND '" . $ending_date . "') ";
					$query .= " OR (date(Element.end_date) BETWEEN '" . $starting_date . "' AND '" . $ending_date . "')) OR (date(Element.start_date) <= '" . $starting_date . "' AND date(Element.end_date) >= '" . $ending_date . "') ) ";

				}

				$query .= "AND Element.date_constraints = 1 ";
				$query .= "AND Element.sign_off != 1 ";
				$query .= $order;
				//pr($query);
				$data = $this->_elements->query($query);
				//pr($data);

			}
		}

		return $data;
	}

	/* *********************************************************************
		************************** Task is Not Available Start *****************
	*/
	// Get Project element by Date with current User
	public function elementsbydate_workcenter($element_id = null, $dates = null) {

		$data = null;

		$starting_date = $ending_date = '';

		if (isset($dates) && !empty($dates)) {

			$d = explode(' = ', $dates);
			if (is_array($d)) {
				$starting_date = (isset($d[0])) ? date('Y-m-d', strtotime($d[0])) : '';
				$ending_date = (isset($d[1])) ? date('Y-m-d', strtotime($d[1])) : '';
			} else {
				$starting_date = $dates;
			}

		} else {
			$starting_date = date('Y-m-d');
		}

		$query = '';

		if (isset($element_id) && !empty($element_id)) {

			$order = '';
			$query .= 'SELECT Element.*, TIMESTAMPDIFF( DAY, Element.start_date, Element.end_date) AS totalDays ';
			$query .= 'FROM elements as Element ';
			$query .= "WHERE Element.id IN (" . implode(',', $element_id) . ") AND Element.studio_status != '1'";
			$order = "ORDER BY totalDays ASC ";
			if (isset($dates) && !empty($dates)) {
				$order = "ORDER BY totalDays ASC ";
				if (!empty($starting_date) && empty($ending_date)) {
					$query .= " AND (((date(Element.start_date) BETWEEN '" . $starting_date . "' AND '" . $starting_date . "') OR (date(Element.end_date) BETWEEN '" . $starting_date . "' AND '" . $starting_date . "')) OR (date(Element.start_date) <= '" . $starting_date . "' AND date(Element.end_date) >= '" . $starting_date . "') OR ( Element.end_date < '" . date('Y-m-d') . "' AND Element.sign_off  != 1) ) ";
					// $query .= "AND (date(Element.start_date) BETWEEN '" . $starting_date . "' AND '" . $starting_date . "') ";
				} else if (empty($starting_date) && !empty($ending_date)) {
					$query .= " AND (date(Element.end_date) BETWEEN '" . $ending_date . "' AND '" . $ending_date . "' OR ( Element.end_date < '" . date('Y-m-d') . "' AND Element.sign_off  != 1)) ";
				} else if (!empty($starting_date) && !empty($ending_date)) {
					$query .= " AND (( (date(Element.start_date) BETWEEN '" . $starting_date . "' AND '" . $ending_date . "') ";
					$query .= " OR (date(Element.end_date) BETWEEN '" . $starting_date . "' AND '" . $ending_date . "')) OR (date(Element.start_date) <= '" . $starting_date . "' AND date(Element.end_date) >= '" . $ending_date . "')  OR   ( Element.end_date < '" . date('Y-m-d') . "' AND Element.sign_off  != 1) )";

				}

				$query .= "AND Element.date_constraints = 1 ";
				$query .= $order;

				// echo $query;
				$data = $this->_elements->query($query);
			}
		}

		return $data;
	}

	// Get Project element by Date with current User
	public function elementsByDates($element_id = null, $dates = null) {

		$data = null;

		$starting_date = $ending_date = '';

		if (isset($dates) && !empty($dates)) {

			$d = explode(' = ', $dates);
			if (is_array($d)) {
				$starting_date = (isset($d[0])) ? date('Y-m-d', strtotime($d[0])) : '';
				$ending_date = (isset($d[1])) ? date('Y-m-d', strtotime($d[1])) : '';
			} else {
				$starting_date = $dates;
			}

		} else {
			$starting_date = date('Y-m-d');
		}

		$query = '';

		if (isset($element_id) && !empty($element_id)) {

			$order = '';
			$query .= 'SELECT Element.id,Element.title,Element.start_date,Element.end_date,Element.studio_status,Element.date_constraints,TIMESTAMPDIFF( DAY, Element.start_date, Element.end_date) AS totalDays ';
			$query .= 'FROM elements as Element ';
			$query .= "WHERE Element.id IN (" . implode(',', $element_id) . ") AND Element.studio_status != '1'";
			$order = "ORDER BY totalDays ASC ";
			if (isset($dates) && !empty($dates)) {
				$order = "ORDER BY totalDays ASC ";
				if (!empty($starting_date) && empty($ending_date)) {
					$query .= " AND (((date(Element.start_date) BETWEEN '" . $starting_date . "' AND '" . $starting_date . "') OR (date(Element.end_date) BETWEEN '" . $starting_date . "' AND '" . $starting_date . "')) OR (date(Element.start_date) <= '" . $starting_date . "' AND date(Element.end_date) >= '" . $starting_date . "')   ) ";
					// $query .= "AND (date(Element.start_date) BETWEEN '" . $starting_date . "' AND '" . $starting_date . "') ";
				} else if (empty($starting_date) && !empty($ending_date)) {
					$query .= " AND (date(Element.end_date) BETWEEN '" . $ending_date . "' AND '" . $ending_date . "'  ) ";
				} else if (!empty($starting_date) && !empty($ending_date)) {
					$query .= " AND (( (date(Element.start_date) BETWEEN '" . $starting_date . "' AND '" . $ending_date . "') ";
					$query .= " OR (date(Element.end_date) BETWEEN '" . $starting_date . "' AND '" . $ending_date . "')) OR (date(Element.start_date) <= '" . $starting_date . "' AND date(Element.end_date) >= '" . $ending_date . "')   )";

				}

				$query .= "AND Element.date_constraints = 1 ";
				$query .= $order;
				//echo $query;
				$data = $this->_elements->query($query);
			}
		}

		return $data;
	}

	public function running_elements_workcenter($element_id = null, $dates = null) {

		$data = null;

		$starting_date = $ending_date = '';

		if (isset($dates) && !empty($dates)) {

			$d = explode(' = ', $dates);
			if (is_array($d)) {
				$starting_date = (isset($d[0])) ? date('Y-m-d', strtotime($d[0])) : '';
				$ending_date = (isset($d[1])) ? date('Y-m-d', strtotime($d[1])) : '';
			} else {
				$starting_date = $dates;
			}

		} else {
			$starting_date = date('Y-m-d');
		}

		$query = '';

		if (isset($element_id) && !empty($element_id)) {

			$order = '';
			$query .= 'SELECT Element.*, TIMESTAMPDIFF( DAY, Element.start_date, Element.end_date) AS totalDays ';
			$query .= 'FROM elements as Element ';
			$query .= "WHERE Element.id IN (" . implode(',', $element_id) . ") AND Element.studio_status != '1'";
			$order = "ORDER BY totalDays ASC ";
			if (isset($dates) && !empty($dates)) {
				$order = "ORDER BY totalDays ASC ";
				if (!empty($starting_date) && empty($ending_date)) {
					$query .= " AND (((date(Element.start_date) BETWEEN '" . $starting_date . "' AND '" . $starting_date . "') OR (date(Element.end_date) BETWEEN '" . $starting_date . "' AND '" . $starting_date . "')) OR (date(Element.start_date) <= '" . $starting_date . "' AND date(Element.end_date) >= '" . $starting_date . "')   ) ";
					// $query .= "AND (date(Element.start_date) BETWEEN '" . $starting_date . "' AND '" . $starting_date . "') ";
				} else if (empty($starting_date) && !empty($ending_date)) {
					$query .= " AND (date(Element.end_date) BETWEEN '" . $ending_date . "' AND '" . $ending_date . "'  ) ";
				} else if (!empty($starting_date) && !empty($ending_date)) {
					$query .= " AND (( (date(Element.start_date) BETWEEN '" . $starting_date . "' AND '" . $ending_date . "') ";
					$query .= " OR (date(Element.end_date) BETWEEN '" . $starting_date . "' AND '" . $ending_date . "')) OR (date(Element.start_date) <= '" . $starting_date . "' AND date(Element.end_date) >= '" . $ending_date . "')   )";

				}

				$query .= "AND Element.date_constraints = 1 ";
				$query .= $order;
				//echo $query;
				$data = $this->_elements->query($query);
			}
		}
		// pr($data);
		return $data;
	}

	public function total_todays_tasks_workcenter($element_id = null, $dates = null) {

		$data = null;

		$starting_date = $ending_date = '';

		if (isset($dates) && !empty($dates)) {

			$starting_date = $dates;
			$ending_date = $dates;

		} else {
			$starting_date = date('Y-m-d');
		}

		$query = '';

		if (isset($element_id) && !empty($element_id)) {

			$order = '';
			$query .= 'SELECT count(Element.id) as totaltasks ';
			$query .= 'FROM elements as Element ';
			$query .= "WHERE Element.id IN (" . implode(',', $element_id) . ") AND Element.studio_status != '1'";
			$order = "ORDER BY Element.id ASC ";
			if (isset($dates) && !empty($dates)) {
				$order = "ORDER BY id ASC ";

				if (!empty($starting_date) && empty($ending_date)) {
					$query .= " AND (((date(Element.start_date) BETWEEN '" . $starting_date . "' AND '" . $starting_date . "') OR (date(Element.end_date) BETWEEN '" . $starting_date . "' AND '" . $starting_date . "')) OR (date(Element.start_date) <= '" . $starting_date . "' AND date(Element.end_date) >= '" . $starting_date . "') OR (Element.sign_off != 1) ) ";
					// $query .= "AND (date(Element.start_date) BETWEEN '" . $starting_date . "' AND '" . $starting_date . "') ";
				} else if (empty($starting_date) && !empty($ending_date)) {
					$query .= " AND (date(Element.end_date) BETWEEN '" . $ending_date . "' AND '" . $ending_date . "' OR (Element.sign_off != 1)) ";
				} else if (!empty($starting_date) && !empty($ending_date)) {
					$query .= " AND (( (date(Element.start_date) BETWEEN '" . $starting_date . "' AND '" . $ending_date . "') ";
					$query .= " OR (date(Element.end_date) BETWEEN '" . $starting_date . "' AND '" . $ending_date . "')) OR (date(Element.start_date) <= '" . $starting_date . "' AND date(Element.end_date) >= '" . $ending_date . "') ) ";

				}

				$query .= "AND Element.date_constraints = 1 ";
				$query .= $order;
				// echo $query."<br />";
				$data = $this->_elements->query($query);
			}
		}

		return $data;
	}

	// Get Project element which is compliting in near end dates
	public function complitingElementsCount($element_id = null, $starting_date = null, $ending_date = null) {

		$data = null;
		$query = '';
		if (isset($element_id) && !empty($element_id)) {

			$order = '';
			$query .= 'SELECT COUNT(*) as totalCompletingElement ';
			$query .= 'FROM elements as Element ';
			$query .= "WHERE Element.id IN (" . implode(',', $element_id) . ") AND Element.studio_status != '1' and Element.end_date BETWEEN '" . $starting_date . " 12:00:00'  AND '" . $ending_date . " 12:00:00'";
			$data = $this->_elements->query($query);

		}
		return (isset($data[0][0]['totalCompletingElement']) && !empty($data[0][0]['totalCompletingElement'])) ? $data[0][0]['totalCompletingElement'] : 0;

	}

	public function elementTaskLeader($elementids) {
		$view = new View();
		$TaskCenter = $view->loadHelper('TaskCenter');

		$element_keys = null;
		$element_key_staus = null;
		$Estatus = array();
		$element_assigned = array();

		if (isset($elementids) && !empty($elementids)) {
			foreach ($elementids as $ekey => $evalue) {
				$wsp_area_studio_status = wsp_area_studio_status($evalue);
				if (!$wsp_area_studio_status) {
					$TaskAssigned = element_assigned($evalue);
					if (isset($TaskAssigned) && !empty($TaskAssigned['ElementAssignment'])) {
						$element_assigned[] = 'Assinged';
					}
				}
			}
		}
		return (isset($element_assigned) && !empty($element_assigned)) ? count($element_assigned) : 0;
	}

	public function threeMonthsDates() {

		$list = array();
		$thisMonth = date('m');

		$date = date('Y-m-d');
		$threeMonthdate = date('m', strtotime('+3 month', strtotime($date)));
		$threedate = date('Y-m-d', strtotime('+3 month', strtotime($date)));

		for ($i = $thisMonth; $i <= $threeMonthdate; $i++) {
			$number = cal_days_in_month(CAL_GREGORIAN, $i, date('Y')); // 31
			$day = date('d');
			$month = date('m');
			$year = date('Y');
			//echo $number;
			if ($month != $i) {

				for ($d = 1; $d <= $number; $d++) {
					// if( $d < $day ){
					$time = mktime(12, 0, 0, $i, $d, $year);
					if (date('m', $time) == $i) {
						if (date('Y-m-d-D', $time) > $threedate) {
							break;
						} else {
							$list[] = date('Y-m-d', $time);
						}

					}
					// }
				}

			} else {

				for ($d = $day; $d <= $number; $d++) {
					$time = mktime(12, 0, 0, $i, $d, $year);
					if (date('m', $time) == $i) {
						if (date('Y-m-d-D', $time) > $threedate) {
							break;
						} else {
							$list[] = date('Y-m-d', $time);
						}

					}
				}
			}

		}

		//pr($list); die;
		return $list;
	}

	public function threeMonthsDate() {

		$list = array();
		$thisMonth = date('m');

		$date = date('Y-m-d');
		$date = date('Y-m-d', strtotime('-1 day', strtotime($date)));

		$dates = [];

		$threedate = date('Y-m-d', strtotime('+3 month', strtotime($date)));

		$end_date = $threedate;

		while (strtotime($date) <= strtotime($end_date)) {

			$date = date("Y-m-d", strtotime("+1 day", strtotime($date)));
			$dates[] = $date;

		}

		return $dates;
	}

	public function threeMonthsDateNew($sel_month = null, $sel_year = null) {

		$list = array();
		//$thisMonth = date('m');
		$thisMonth = $sel_month;

		$date = date($sel_year . '-' . $sel_month . '-01');
		$date = date($sel_year . '-' . $sel_month . '-01', strtotime('-1 day', strtotime($date)));

		$dates = [];

		$threedate = date('Y-m-d', strtotime('+3 month', strtotime($date)));

		$end_date = $threedate;

		while (strtotime($date) <= strtotime($end_date)) {

			$date = date('Y-m-d', strtotime("+1 day", strtotime($date)));
			$dates[] = $date;

		}

		return $dates;
	}

	public function check_continuous_dates($datearray_old) {

		$datearray = array();
		if (isset($datearray_old) && !empty($datearray_old)) {

			foreach ($datearray_old as $key => $prevsvalue) {
				if (isset($prevsvalue) && !empty($prevsvalue)) {
					foreach ($prevsvalue as $value) {
						$datearray[] = $value;
					}
				}
			}

			asort($datearray);

			$resultArray = array();
			$index = -1;
			$last = 0;
			$out = "";

			foreach ($datearray as $date) {
				// e($date);
				$ts = strtotime($date);
				if (false !== $ts) {
					$diff = $ts - $last;

					$dd = date_diff(date_create(date('Y-m-d', $ts)), date_create(date('Y-m-d', $last)));

					if ($diff > 86400 && $dd->days > 1) {
						// if ($diff > 86400  ) {
						$index = $index + 1;
						$resultArray[$index][] = $date;
					} elseif ($diff > 0) {
						$resultArray[$index][] = $date;
					} else {
						// Error! dates are not in order from small to large
					}
					$last = $ts;
				}
			}
			//pr($resultArray);
			foreach ($resultArray as $a) {
				if (count($a) >= 1) {
					$firstDate = $a[0];

					$firstDateBits = explode('-', $firstDate);
					$lastDate = $a[count($a) - 1];
					$lastDateBits = explode('-', $lastDate);

					if ($firstDateBits[1] === $lastDateBits[1]) {

						if (!empty($firstDate) && empty($lastDate)) {

							$out .= '<span class="workinfo">';
							$out .= intval($firstDateBits[2]) . " " . date("M", strtotime($firstDate)) . " " . $firstDateBits[0];
							$out .= '</span>';

						} else {
							$out .= '<span class="workinfo">';
							if ($firstDateBits[2] !== $lastDateBits[2]) {
								$out .= intval($firstDateBits[2]) . " " . date("M", strtotime($firstDate)) . " " . $firstDateBits[0] . " - ";
								$out .= intval($lastDateBits[2]) . " " . date("M", strtotime($firstDate)) . " " . $lastDateBits[0];
							} else {
								$out .= intval($firstDateBits[2]) . " " . date("M", strtotime($firstDate)) . " " . $firstDateBits[0];
							}
							$out .= '</span>';
						}

					} else {

						$out .= '<span class="workinfo">';
						$out .= intval($firstDateBits[2]) . " " . date("M", strtotime($firstDate)) . " " . $firstDateBits[0] . ' - ';
						$out .= intval($lastDateBits[2]) . " " . date("M", strtotime($lastDate)) . " " . $lastDateBits[0];
						$out .= '</span>';

					}
				}
			}
		}
		return $out;
	}
	/* *********************************************************************
		************************** Task is Not Available End *******************
	*/

	/* *********************************************************************
		   *********************** Not Availability Start **********************
	*/

	public function userAvaiabilityWithPast($user_id = null) {
		App::import("Model", "Availability");
		$Availability = new Availability();

		//return $Availability->find('all', array('conditions' => array('Availability.user_id' => $user_id)));
		$today = date('Y-m-d');

		/* $sql = "SELECT * FROM availabilities as Availability WHERE (STR_TO_DATE(LEFT(avail_start_date,LOCATE(' ',avail_start_date)),'%Y-%m-%d') BETWEEN '" . $today . "' AND '" . $today . "' OR '" . $today . "' BETWEEN STR_TO_DATE(LEFT(avail_start_date,LOCATE(' ',avail_start_date)),'%Y-%m-%d') AND STR_TO_DATE(LEFT(avail_end_date,LOCATE(' ',avail_end_date)),'%Y-%m-%d')) AND STR_TO_DATE(LEFT(avail_end_date,LOCATE(' ',avail_end_date)),'%Y-%m-%d') >= '" . $today . "'  AND user_id = " . $user_id . " order by STR_TO_DATE(LEFT(avail_start_date,LOCATE(' ',avail_start_date)),'%Y-%m-%d') ASC ";

			$dds = $this->_availabilities->query($sql);
			// upcoming availabilities
			$dd = $Availability->find('all',
				array('conditions' => array(
					'Availability.user_id' => $user_id,
					"STR_TO_DATE(LEFT(Availability.avail_start_date,LOCATE(' ',Availability.avail_start_date)),'%Y-%m-%d') >" => $today,
				),
					'order' => "STR_TO_DATE(LEFT(Availability.avail_start_date,LOCATE(' ',Availability.avail_start_date)),'%Y-%m-%d')",
				)
		*/

		$sql = "SELECT * FROM availabilities as Availability WHERE (STR_TO_DATE(LEFT(avail_start_date,LOCATE(' ',avail_start_date)),'%Y-%m-%d') BETWEEN '" . $today . "' AND '" . $today . "' OR '" . $today . "' BETWEEN STR_TO_DATE(LEFT(avail_start_date,LOCATE(' ',avail_start_date)),'%Y-%m-%d') AND STR_TO_DATE(LEFT(avail_end_date,LOCATE(' ',avail_end_date)),'%Y-%m-%d')) AND STR_TO_DATE(LEFT(avail_end_date,LOCATE(' ',avail_end_date)),'%Y-%m-%d') >= '" . $today . "'  AND user_id = " . $user_id . " order by STR_TO_DATE(LEFT(avail_start_date,LOCATE(' ',avail_start_date)),'%Y-%m-%d')  ";

		$dds = $this->_availabilities->query($sql);
		// upcoming availabilities
		$dd = $Availability->find('all',
			array('conditions' => array(
				'Availability.user_id' => $user_id,
				"STR_TO_DATE(LEFT(Availability.avail_start_date,LOCATE(' ',Availability.avail_start_date)),'%Y-%m-%d') <" => $today,
				'STR_TO_DATE(LEFT(avail_end_date,LOCATE(" ",avail_end_date)),"%Y-%m-%d") > ' . $today,
			),
				'order' => "STR_TO_DATE(LEFT(Availability.avail_start_date,LOCATE(' ',Availability.avail_start_date)),'%Y-%m-%d') ",
			)
		);
		// past availabilities
		$ddpast = array();
		//$ddpast = $this->_availabilities->query("SELECT * FROM availabilities WHERE STR_TO_DATE(LEFT(avail_end_date,LOCATE(' ',avail_end_date)),'%Y-%m-%d') < '" . $today . "' AND user_id = " . $user_id . " order by STR_TO_DATE(LEFT(avail_start_date,LOCATE(' ',avail_start_date)),'%Y-%m-%d') ASC ");

		if (isset($dds) && !empty($dds) && isset($dd) && !empty($dd) && isset($ddpast) && !empty($ddpast)) {
			$result = array_merge($dds, $dd, $ddpast);
		} else if (isset($dds) && !empty($dds) && isset($dd) && !empty($dd)) {
			$result = array_merge($dds, $dd);
		} else if (isset($dds) && !empty($dds) && isset($ddpast) && !empty($ddpast)) {
			$result = array_merge($dds, $ddpast);
		} else if (isset($dd) && !empty($dd) && isset($ddpast) && !empty($ddpast)) {
			$result = array_merge($dd, $ddpast);
		} else if (isset($dds) && !empty($dds)) {
			$result = $dds;
		} else if (isset($ddpast) && !empty($ddpast)) {
			$result = $ddpast;
		} else {
			$result = $dd;
		}

		//pr($result);

		return (isset($result) && !empty($result)) ? $result : array();

	}

	public function userAvaiability($user_id = null) {
		App::import("Model", "Availability");
		$Availability = new Availability();

		//return $Availability->find('all', array('conditions' => array('Availability.user_id' => $user_id)));
		$today = date('Y-m-d');

		$sql = "SELECT * FROM availabilities as Availability WHERE (STR_TO_DATE(LEFT(avail_start_date,LOCATE(' ',avail_start_date)),'%Y-%m-%d') BETWEEN '" . $today . "' AND '" . $today . "' OR '" . $today . "' BETWEEN STR_TO_DATE(LEFT(avail_start_date,LOCATE(' ',avail_start_date)),'%Y-%m-%d') AND STR_TO_DATE(LEFT(avail_end_date,LOCATE(' ',avail_end_date)),'%Y-%m-%d')) AND STR_TO_DATE(LEFT(avail_end_date,LOCATE(' ',avail_end_date)),'%Y-%m-%d') >= '" . $today . "'  AND user_id = " . $user_id . " order by STR_TO_DATE(LEFT(avail_start_date,LOCATE(' ',avail_start_date)),'%Y-%m-%d')  ";

		$dds = $this->_availabilities->query($sql);
		// upcoming availabilities
		$dd = $Availability->find('all',
			array('conditions' => array(
				'Availability.user_id' => $user_id,
				"STR_TO_DATE(LEFT(Availability.avail_start_date,LOCATE(' ',Availability.avail_start_date)),'%Y-%m-%d') <" => $today,
				'STR_TO_DATE(LEFT(avail_end_date,LOCATE(" ",avail_end_date)),"%Y-%m-%d") > ' . $today,
			),
				'order' => "STR_TO_DATE(LEFT(Availability.avail_start_date,LOCATE(' ',Availability.avail_start_date)),'%Y-%m-%d') ",
			)
		);
		// past availabilities
		//$this->_availabilities->query("SELECT * FROM availabilities WHERE STR_TO_DATE(LEFT(avail_end_date,LOCATE(' ',avail_end_date)),'%Y-%m-%d') < '" . $today . "' AND user_id = " . $user_id . " order by STR_TO_DATE(LEFT(avail_start_date,LOCATE(' ',avail_start_date)),'%Y-%m-%d') ASC ");

		/* pr($dds);
		pr($dd); */

		if (isset($dds) && !empty($dds) && isset($dd) && !empty($dd)) {
			$result = array_merge($dds, $dd);
		} else if (isset($dds) && !empty($dds)) {
			$result = $dds;
		} else {
			$result = $dd;
		}

		//pr($result);

		return (isset($result) && !empty($result)) ? $result : array();

	}

	public function upcomingAvaiability($user_id = null) {

		//$today = date('Y-m-d');
		$today = $this->Wiki->_displayDate( date('Y-m-d h:i A'), $format = 'Y-m-d H:i:00' );

		return $this->_availabilities->query("SELECT * FROM availabilities WHERE ( CASE
        WHEN STR_TO_DATE(avail_start_date, '%Y-%m-%d %h:%i %p') IS NULL THEN STR_TO_DATE(avail_start_date, '%Y-%m-%d 00:00:00')
        ELSE STR_TO_DATE(avail_start_date, '%Y-%m-%d %h:%i %p')
		END)  > '" . $today . "' AND user_id = '" . $user_id . "' order by STR_TO_DATE(LEFT(avail_start_date,LOCATE(' ',avail_start_date)),'%Y-%m-%d') ASC ");

	}

	public function pastAvaiability($user_id = null) {

		//$today = date('Y-m-d H:i A');
		$today = $this->Wiki->_displayDate( date('Y-m-d h:i A'), $format = 'Y-m-d H:i:00' );

		return $this->_availabilities->query("SELECT * FROM availabilities WHERE ( CASE
        WHEN STR_TO_DATE(avail_end_date, '%Y-%m-%d %h:%i %p') IS NULL THEN STR_TO_DATE(avail_end_date, '%Y-%m-%d 00:00:00')
        ELSE STR_TO_DATE(avail_end_date, '%Y-%m-%d %h:%i %p')
		END)  < '" . $today . "' AND user_id = " . $user_id . " order by STR_TO_DATE(LEFT(avail_start_date,LOCATE(' ',avail_start_date)),'%Y-%m-%d') DESC ");

	}

	public function currentAvaiability($user_id = null) {

		$today = date('Y-m-d');

		//$todayT = date('Y-m-d H:i A');
		$todayT = $this->Wiki->_displayDate( date('Y-m-d h:i A'), $format = 'Y-m-d H:i:00' );

		//$sql = "SELECT * FROM availabilities WHERE ((STR_TO_DATE(LEFT(avail_start_date,LOCATE(' ',avail_start_date)),'%Y-%m-%d') BETWEEN '" . $today . "' AND '" . $today . "' OR '" . $today . "' BETWEEN STR_TO_DATE(LEFT(avail_start_date,LOCATE(' ',avail_start_date)),'%Y-%m-%d') AND STR_TO_DATE(LEFT(avail_end_date,LOCATE(' ',avail_end_date)),'%Y-%m-%d')) AND STR_TO_DATE(LEFT(avail_end_date,LOCATE(' ',avail_end_date)),'%Y-%m-%d') >= '" . $today . "') and avail_end_date >= '" . $todayT . "'  AND user_id = " . $user_id . " order by STR_TO_DATE(LEFT(avail_start_date,LOCATE(' ',avail_start_date)),'%Y-%m-%d') ASC ";

		$sql = " SELECT * FROM availabilities WHERE ((STR_TO_DATE(LEFT(avail_start_date,LOCATE(' ',avail_start_date)),'%Y-%m-%d') BETWEEN '" . $today . "' AND '" . $today . "' OR '" . $today . "' BETWEEN STR_TO_DATE(LEFT(avail_start_date,LOCATE(' ',avail_start_date)),'%Y-%m-%d') AND STR_TO_DATE(LEFT(avail_end_date,LOCATE(' ',avail_end_date)),'%Y-%m-%d')) AND STR_TO_DATE(LEFT(avail_end_date,LOCATE(' ',avail_end_date)),'%Y-%m-%d') >= '" . $today . "') and
	   (( CASE
	        WHEN STR_TO_DATE(avail_end_date, '%Y-%m-%d %h:%i %p') IS NULL THEN STR_TO_DATE(avail_end_date, '%Y-%m-%d 00:00:00')
	        ELSE STR_TO_DATE(avail_end_date, '%Y-%m-%d %h:%i %p')
	    END) >= '" . $todayT . "' and ( CASE
	        WHEN STR_TO_DATE(avail_start_date, '%Y-%m-%d %h:%i %p') IS NULL THEN STR_TO_DATE(avail_start_date, '%Y-%m-%d 00:00:00')
	        ELSE STR_TO_DATE(avail_start_date, '%Y-%m-%d %h:%i %p')
	    END) <= '" . $todayT . "')   AND user_id = " . $user_id . "" ;




		return $this->_availabilities->query($sql);

	}

	public function not_available_dates($user_id = null) {

		$threemonthsdate = $this->threeMonthsDate();
		// $noAvailableDates = array();
		$noAvailableDates = '';
		$yesElementDates = array();
		$newDatesArray = array();

		if (isset($threemonthsdate) && !empty($threemonthsdate)) {
			foreach ($threemonthsdate as $key => $datelist) {

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

				$datas = $this->_availabilities->query($query);

				foreach ($datas as $showdates) {

					$breakstdates = explode(" ", $showdates['Availability']['avail_start_date']);
					$breakenddates = explode(" ", $showdates['Availability']['avail_end_date']);

					if ($breakstdates[0] == $datelist) {
						if ($breakstdates[1] == '00:00:00') {
							$noAvailableDates .= $datelist . "=fullday,";
						} else {
							//	echo $datelist . " s=".$breakstdates[1]."<br>";
							$noAvailableDates .= $datelist . ' ' . $breakstdates[1] . " " . $breakstdates[2] . "=partial,";
						}
					} else {

						if ($this->isBetweenRange($breakstdates[0], $breakenddates[0], $datelist)) {
							$noAvailableDates .= $datelist . "=fullday,";
						}

						if ($breakenddates[0] == $datelist) {
							if ($breakenddates[1] == '00:00:00') {
								$noAvailableDates .= $datelist . "=fullday,";
							} else {
								//		echo $datelist .  " e=".$breakenddates[1]."<br>";
								$noAvailableDates .= $datelist . ' ' . $breakenddates[1] . " " . $breakenddates[2] . "=partial,";
							}
						}
					}
				}

				/* if ($datas[0][0]['availtotal'] > 0) {
					$noAvailableDates[] = $datelist;
				} */
			}

		}
		//pr($noAvailableDates);

		return $noAvailableDates;
	}

	public function not_available_dateswithstatus($user_id = null, $sel_month = null, $sel_year = null) {

		$threemonthsdate = $this->threeMonthsDateNew($sel_month, $sel_year);

		$noAvailableDates = '';
		if (isset($threemonthsdate) && !empty($threemonthsdate)) {
			foreach ($threemonthsdate as $key => $datelist) {

				$dates = $datelist;
				$starting_date = $datelist;
				$ending_date = $datelist;

				$query = '';
				$order = '';
				$query .= "SELECT * ";
				$query .= "FROM availabilities as Availability WHERE user_id = " . $user_id . "  ";
				$order = "ORDER BY Availability.id ASC ";

				if (isset($dates) && !empty($dates)) {
					$order = "ORDER BY id ASC ";

					if (!empty($starting_date) && !empty($ending_date)) {

						$query .= " AND ( ";

						$query .= " ( (STR_TO_DATE(LEFT(Availability.avail_start_date,LOCATE(' ',Availability.avail_start_date)),'%Y-%m-%d') BETWEEN '" . $starting_date . "' AND '" . $ending_date . "') ";

						$query .= " OR (STR_TO_DATE(LEFT(Availability.avail_end_date,LOCATE(' ',avail_end_date)),'%Y-%m-%d') BETWEEN '" . $starting_date . "' AND '" . $ending_date . "') ) ";

						$query .= " OR (STR_TO_DATE(LEFT(Availability.avail_start_date,LOCATE(' ',Availability.avail_start_date)),'%Y-%m-%d') <= '" . $starting_date . "' AND STR_TO_DATE(LEFT(Availability.avail_end_date,LOCATE(' ',Availability.avail_end_date)),'%Y-%m-%d') >= '" . $ending_date . "') ) ";

						//changed on 20th June
						/* $query .= " OR (STR_TO_DATE(LEFT(Availability.avail_start_date,LOCATE(' ',Availability.avail_start_date)),'%Y-%m-%d') < '" . $starting_date . "' AND STR_TO_DATE(LEFT(Availability.avail_end_date,LOCATE(' ',Availability.avail_end_date)),'%Y-%m-%d') < '" . $ending_date . "') ) "; */

					}
				}
				$query .= $order;
				$datas = $this->_availabilities->query($query);
				if (isset($datas) && !empty($datas)) {

					foreach ($datas as $showdates) {

						$breakstdates = explode(" ", $showdates['Availability']['avail_start_date']);
						$breakenddates = explode(" ", $showdates['Availability']['avail_end_date']);

						if ($breakstdates[0] == $datelist) {
							if ($breakstdates[1] == '00:00:00') {
								$noAvailableDates .= $datelist . "=fullday,";
							} else {
								$noAvailableDates .= $datelist . "=partial,";
							}
						} else {

							if ($this->isBetweenRange($breakstdates[0], $breakenddates[0], $datelist)) {

								/* if( $breakstdates[1] == '00:00:00' ){
									$noAvailableDates .= $datelist."=partial,";
								} else { */
								$noAvailableDates .= $datelist . "=fullday,";
								//}

							} /*else {

								if(  $breakstdates[0] == $datelist ){
									echo "startdate = 000000000000000000".$datelist."<br>";
									if( $breakstdates[1] == '00:00:00' ){
										$noAvailableDates .= $datelist."=fullday,";
									} else {
										$noAvailableDates .= $datelist."=partial,";
									}

								}

							} */

							if ($breakenddates[0] == $datelist) {
								//echo "=enddate = 000000000000000000".$datelist."<br>";
								if ($breakenddates[1] == '00:00:00') {
									$noAvailableDates .= $datelist . "=fullday,";
								} else {
									$noAvailableDates .= $datelist . "=partial,";
								}

							}
						}
					}
				}
			}
		}
		//echo $noAvailableDates;
		// die;
		if (substr($noAvailableDates, -1, 1) == ',') {
			$noAvailableDates = substr($noAvailableDates, 0, -1);
		}
		return $noAvailableDates;

	}

	public function isBetweenRange($start_date, $end_date, $expected_dates) {
		$datematch = false;
		$begin = new DateTime($start_date);
		$end = new DateTime($end_date);
		$daterange = new DatePeriod($begin, new DateInterval('P1D'), $end);
		foreach ($daterange as $date) {
			if ($date->format("Y-m-d") == $expected_dates) {
				return true;
			}
		}
		return false;
	}

	public function check_continuous_avail_dates($datestring, $user_id = null) {

		$resultArray = array();
		$resultArraywithsts = array();
		$index = -1;
		$indexX = -2;
		$last = strtotime(date('Y-m-d'));
		//$last = strtotime(date('Y-m-d'). ' -1 day');
		//$last = 0;
		$out = "";

		if (isset($datestring) && !empty($datestring)) {

			$datearray = explode(",", $datestring);

		}

		// asort($datearray);
		// pr($datearray);

		foreach ($datearray as $actualdates) {

			$date = explode("=", $actualdates);
			// pr($date[0]);
			$ts = strtotime($date[0]);

			if (false !== $ts) {
				$cc = date('H:i', $last);
				$Amp = date('h:iA', strtotime($last));
				$FFAmp = date('H:i', strtotime($ts));

				if ($cc != '00:00') {

					$last = strtotime(date('Y-m-d'));
				}
				$diff = $ts - $last;

				if ($diff == 0) {

					$last = strtotime(date('Y-m-d') . ' -1 day');
					$diff = $ts - $last;

				}

				$dd = date_diff(date_create(date('Y-m-d h:i', $ts)), date_create(date('Y-m-d h:i', $last)));

				$datetime1 = new DateTime(date('Y-m-d h:i', $ts));
				$datetime2 = new DateTime(date('Y-m-d h:i', $last));
				$interval = $datetime1->diff($datetime2);

				if ($diff > 90000) {
					$index = $index + 1;
					$resultArray[$index][] = $date[0];
					$resultArraywithsts[$index][$date[0]][] = $date[1];
				} else if ($diff > 0 && $date[1] != 'partial') {
					// pr($diff);
					$resultArray[$index][] = $date[0];
					$resultArraywithsts[$index][$date[0]][] = $date[1];
				} else if ($date[1] == 'partial') {
					$indexX = $indexX - 1;
					$resultArray[$indexX][] = $date[0];
					$resultArraywithsts[$indexX][$date[0]][] = $date[1];
					// Error! dates are not in order from small to large
				}

				$last = $ts;
			}
		}

		// pr($resultArray);
		$p = 0;
		foreach ($resultArray as $a) {
			if (count($a) >= 1) {
				$firstDate = $a[0];

				// echo $a[0];
				// pr($resultArraywithsts[$p]);

				$firstDateBits = explode('-', $firstDate);
				$lastDate = $a[count($a) - 1];

				$lastDateBits = explode('-', $lastDate);
				$Ampss = date('h:iA', strtotime($firstDate));

				if ($firstDateBits[1] === $lastDateBits[1]) {
					if (!empty($firstDate) && empty($lastDate)) {

						$out .= '<span class="workinfo">11 ';
						$out .= intval($firstDateBits[2]) . " " . date("M", strtotime($firstDate)) . " " . $firstDateBits[0];
						$out .= '</span>';

					} else {

						if ($firstDateBits[2] !== $lastDateBits[2]) {
							$out .= '<span class="workinfo text-red">   ';
							$out .= intval($firstDateBits[2]) . " " . date("M", strtotime($firstDate)) . " " . $firstDateBits[0] . " - ";
							$out .= intval($lastDateBits[2]) . " " . date("M", strtotime($lastDate)) . " " . $lastDateBits[0];
							$out .= '</span>';
						} else {
							$ccd = date('h:i', strtotime($firstDate));
							$ccds = date('g:iA', strtotime($firstDate));
							$ccdAmp = date('H:i', strtotime($firstDate));

							if ($ccdAmp != '00:00') {

								$avdate = $this->_availabilities->find('first', array('conditions' => array('Availability.avail_start_date' => $firstDate, 'Availability.user_id' => $user_id, "STR_TO_DATE(LEFT(avail_end_date,LOCATE(' ',avail_end_date)),'%Y-%m-%d')" => date('Y-m-d', strtotime($firstDate)))));

								$avEDdate = $this->_availabilities->find('first', array('conditions' => array('Availability.avail_end_date' => $firstDate, 'Availability.user_id' => $user_id, "STR_TO_DATE(LEFT(avail_start_date,LOCATE(' ',avail_start_date)),'%Y-%m-%d') <" => date('Y-m-d', strtotime($firstDate)))));

								if (isset($avdate) && !empty($avdate)) {
									$avFdate = " - " . date('h:iA', strtotime($avdate['Availability']['avail_end_date']));
									$avFsdate = "";
								} else {

									$avFsdate = " from ";

									if (date('Y-m-d', $last) == date('Y-m-d', strtotime($firstDate))) {
										$avFsdate = "to ";
									} else {

										$avFdate = "";
									}
									$avFdate = "";
									if (isset($avEDdate) && !empty($avEDdate)) {
										$avFsdate = " to ";
									}

								}

								$out .= '<span class="workinfo text-yellow">  ';
								$out .= intval($firstDateBits[2]) . " " . date("M", strtotime($firstDate)) . " " . $firstDateBits[0] . " " . $avFsdate . $ccds . $avFdate;
							} else {
								$out .= '<span class="workinfo text-red">  ';
								$out .= intval($firstDateBits[2]) . " " . date("M", strtotime($firstDate)) . " " . $firstDateBits[0];
							}

							$out .= '</span>';
						}

					}

				} else {

					/* $out .= '<span class="workinfo"> 22222 ';
						$out .= intval($firstDateBits[2]) . " " . date("M", strtotime($firstDate)) . " " . $firstDateBits[0] . " - ";
						$out .= intval($lastDateBits[2]) . " " . date("M", strtotime($lastDate)) . " " . $firstDateBits[0];
					*/
					$ccd = date('h:i', strtotime($firstDate));
					$ccds = date('A', strtotime($firstDate));
					if ($ccd != "12:00") {
						$out .= '<span class="workinfo text-red">  ';
						$out .= intval($firstDateBits[2]) . " " . date("M", strtotime($firstDate)) . " " . $firstDateBits[0] . " - ";
						$out .= intval($lastDateBits[2]) . " " . date("M", strtotime($lastDate)) . " " . $lastDateBits[0];
						$out .= '</span>';
					} else {
						$out .= '<span class="workinfo text-red">  ';
						$out .= intval($firstDateBits[2]) . " " . date("M", strtotime($firstDate)) . " " . $firstDateBits[0] . " - ";
						$out .= intval($lastDateBits[2]) . " " . date("M", strtotime($lastDate)) . " " . $lastDateBits[0];
						$out .= '</span>';
					}

				}

			}
			$p++;
		}
		return $out;
	}

	/* *********************************************************************
		   *********************** Not Availability End ************************
	*/

	public function projectsharers($project_id = null) {

		$view = new View();
		$common = $view->loadHelper('Common');

		$data = array();
		$participants = $groupowners = $owners = array();
		$group = array();

		if (isset($project_id) && !empty($project_id)) {
			//$data['participants_owners'] = participants_owners($project_id, $owner);
			//$data['participantsGpOwner'] = participants_group_owner($project_id);

			$owner = $common->userprojectOwner($project_id);

			$participants = participants($project_id, $owner);
			$participants = (isset($participants) && !empty($participants)) ? $participants : array();

			//$owners = participants_owners($project_id, $owner);
			//$owners = (isset($owners) && !empty($owners)) ? $owners : array();

			//$groupowners =  participants_group_owner($project_id);
			//$groupowners = (isset($groupowners) && !empty($groupowners)) ? $groupowners : array();

			$group = participants_group_sharer($project_id);
			$group = (isset($group) && !empty($group)) ? $group : array();
		}

		$data = array_merge($participants, $group, $owners, $groupowners);

		/* if (!empty($participants) && !empty($group)) {
				$data = array_merge($participants, $group);
			} else if (empty($group) && !empty($participants)) {
				$data = $participants;
			} else if (!empty($group) && empty($participants)) {
				$data = $group;
		*/

		//pr($data);

		return (isset($data) && !empty($data)) ? $data : array();

	}

	public function projectusers($project_id = null) {

		$view = new View();
		$common = $view->loadHelper('Common');

		$data = array();
		$participants = $groupowners = $owners = array();
		$group = array();

		if (isset($project_id) && !empty($project_id)) {
			//$data['participants_owners'] = participants_owners($project_id, $owner);
			//$data['participantsGpOwner'] = participants_group_owner($project_id);

			$owner = $common->userprojectOwner($project_id);

			$participants = participants($project_id, $owner);
			$participants = (isset($participants) && !empty($participants)) ? $participants : array();

			$owners = participants_owners($project_id, $owner);
			$owners = (isset($owners) && !empty($owners)) ? $owners : array();

			$groupowners = participants_group_owner($project_id);
			$groupowners = (isset($groupowners) && !empty($groupowners)) ? $groupowners : array();

			$group = participants_group_sharer($project_id);
			$group = (isset($group) && !empty($group)) ? $group : array();
		}

		$data = array_merge($participants, $group, $owners, $groupowners);

		/* if (!empty($participants) && !empty($group)) {
				$data = array_merge($participants, $group);
			} else if (empty($group) && !empty($participants)) {
				$data = $participants;
			} else if (!empty($group) && empty($participants)) {
				$data = $group;
		*/

		//pr($data);

		return (isset($data) && !empty($data)) ? $data : array();

	}

	public function noTaskWorkingUsers($projectlists = null, $start_date = null, $end_date = null) {
		$view = new View();
		$collection = new ComponentCollection();
		$Common = new CommonComponent($collection);
		$Group = $view->loadHelper('Group');
		$freeUsersData = array();

		foreach ($projectlists as $projectdetail) {
			// get Project Total Tasks =============================================
			$totalEle = $totalWs = 0;
			$totalAssets = null;
			// $projectData = $this->getProjectDetail($projectdetail['Project']['id']);
			$project_id = $projectdetail['Project']['id'];
			//$sharerUsers = $this->projectsharers($project_id);
			$sharerUsers = $this->projectsharers($project_id);

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
								$arealist = $this->workspace_areas($id);

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
										$all_elements = $this->area_elements_permissions($v['Area']['id'], false, $e_permission);
									}

									$wsp_elements[] = $all_elements;
									$all_elements = Set::extract($all_elements, '{n}.Element.id');

									// $elements_by_date = null;
									if (isset($all_elements) && !empty($all_elements)) {
										$dates = $start_date . " = " . $end_date;
										// $dates = "2020-09-05 = 2020-08-08";
										$elements_by_date = array_merge($elements_by_date, $this->running_elements_workcenter($all_elements, $dates));
									}

									$projectElementCount = ( isset($elements_by_date) && !empty($elements_by_date) ) ? count($elements_by_date) : 0;
									if ($projectElementCount <= 0) {
										// $freeUsersData['project_ids'][] = $project_id;
										/* $freeUsersData['start_date'][] = $start_date;
										$freeUsersData['end_date'][] = $end_date; */
										//pr($freeUsersData);
										$freeUsersData['project_id'][$project_id][] = $user_id;
									}

								}
							}
						}
					}
				}
			}
		}

		return $freeUsersData;
	}

	public function continue_date($start_date = null, $end_date = null) {
		$start = strtotime($start_date);
		$stop = strtotime($end_date);
		$alldates = array();
		for ($seconds = $start; $seconds <= $stop; $seconds += 86400) {
			$alldates[] = date("Y-m-d", $seconds);
		}

		return $alldates;
	}

	public function get_continue_date($start_date = null, $end_date = null) {
		$sdate = DateTime::createFromFormat('Y-m-d', $start_date);
		$edate = DateTime::createFromFormat('Y-m-d', $end_date);
		$edate->modify('+1 day');
		$period = new DatePeriod(
			$sdate,
			new DateInterval('P1D'),
			$edate
		);
		$allDates = [];
		foreach ($period as $date) {
			$allDates[] = $date->format("Y-m-d");
		}
		return $allDates;
	}

	public function not_available_dates_range($user_id = null, $start_date = null, $end_date = null) {

		$threemonthsdate = $this->continue_date($start_date, $end_date);

		$noAvailableDates = '';
		$yesElementDates = array();
		$newDatesArray = array();
		if (isset($threemonthsdate) && !empty($threemonthsdate)) {
			foreach ($threemonthsdate as $key => $datelist) {

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

						//$query .= " ( (STR_TO_DATE(LEFT(Availability.avail_start_date,LOCATE(' ',Availability.avail_start_date)),'%Y-%m-%d') BETWEEN '" . $starting_date . "' AND '" . $ending_date . "') ";

						//$query .= " OR (STR_TO_DATE(LEFT(Availability.avail_end_date,LOCATE(' ',avail_end_date)),'%Y-%m-%d') BETWEEN '" . $starting_date . "' AND '" . $ending_date . "') ) ";

						//$query .= " OR (STR_TO_DATE(LEFT(Availability.avail_start_date,LOCATE(' ',Availability.avail_start_date)),'%Y-%m-%d') <= '" . $starting_date . "' AND STR_TO_DATE(LEFT(Availability.avail_end_date,LOCATE(' ',Availability.avail_end_date)),'%Y-%m-%d') >= '" . $ending_date . "') ) ";

						$todayT = $this->Wiki->_displayDate( date('Y-m-d h:i A'), $format = 'Y-m-d H:i:00' );


						$query .=  "
							(( CASE
							WHEN STR_TO_DATE(avail_end_date, '%Y-%m-%d %h:%i %p') IS NULL THEN STR_TO_DATE(avail_end_date, '%Y-%m-%d 00:00:00')
							ELSE STR_TO_DATE(avail_end_date, '%Y-%m-%d %h:%i %p')
						END) >= '" . $todayT . "' and ( CASE
							WHEN STR_TO_DATE(avail_start_date, '%Y-%m-%d %h:%i %p') IS NULL THEN STR_TO_DATE(avail_start_date, '%Y-%m-%d 00:00:00')
							ELSE STR_TO_DATE(avail_start_date, '%Y-%m-%d %h:%i %p')
						END) <= '" . $todayT . "')  )" ;
					}
				}
				$query .= $order;
				// echo $query; die;
				$datas = $this->_availabilities->query($query);

				foreach ($datas as $showdates) {

					$breakstdates = explode(" ", $showdates['Availability']['avail_start_date']);
					$breakenddates = explode(" ", $showdates['Availability']['avail_end_date']);

					if ($breakstdates[0] == $datelist) {
						if ($breakstdates[1] == '00:00:00') {
							$noAvailableDates .= $datelist . "=fullday,";
						} else {
							$noAvailableDates .= $datelist . ' ' . $breakstdates[1] . " " . $breakstdates[2] . "=partial,";
						}
					} else {

						if ($this->isBetweenRange($breakstdates[0], $breakenddates[0], $datelist)) {
							$noAvailableDates .= $datelist . "=fullday,";
						}

						if ($breakenddates[0] == $datelist) {
							if ($breakenddates[1] == '00:00:00') {
								$noAvailableDates .= $datelist . "=fullday,";
							} else {
								$noAvailableDates .= $datelist . ' ' . $breakenddates[1] . " " . $breakenddates[2] . "=partial,";
							}
						}
					}
				}
			}
		}
		return $noAvailableDates;
	}

	public function element_project_people($project_id = null) {

		$view = new View();
		$common = $view->loadHelper('Common');

		$data = null;
		$ndata = null;

		if (isset($project_id) && !empty($project_id)) {

			$owner = $common->ProjectOwner($project_id, $this->Session->read('Auth.User.id'));
			//$data['owner'] = $owner['UserProject']['user_id'];
			if (isset($owner) && !empty($owner)) {
				$datas['owner'] = array($owner['UserProject']['user_id']);
				$data[] = array($owner['UserProject']['user_id']);
				$ndata['owner'] = array($owner['UserProject']['user_id']);
			}
			$datas['participants'] = participants($project_id, $owner['UserProject']['user_id']);
			if (isset($datas['participants']) && !empty($datas['participants'])) {
				$data[] = $datas['participants'];
				$ndata['participants'] = $datas['participants'];
			}

			$datas['participants_owners'] = participants_owners($project_id, $owner['UserProject']['user_id']);
			if (isset($datas['participants_owners']) && !empty($datas['participants_owners'])) {
				$data[] = $datas['participants_owners'];
				$ndata['participants_owners'] = $datas['participants_owners'];
			}

			$datas['participantsGpOwner'] = participants_group_owner($project_id);
			if (isset($datas['participantsGpOwner']) && !empty($datas['participantsGpOwner'])) {
				$data[] = $datas['participantsGpOwner'];
				$ndata['participantsGpOwner'] = $datas['participantsGpOwner'];

			}

			$datas['participantsGpSharer'] = participants_group_sharer($project_id);
			if (isset($datas['participantsGpSharer']) && !empty($datas['participantsGpSharer'])) {
				$data[] = $datas['participantsGpSharer'];
				$ndata['participantsGpSharer'] = $datas['participantsGpSharer'];
			}
			$newulist = array();
			if (isset($data) && !empty($data)) {
				foreach ($data as $ulist) {
					if (isset($ulist) && !empty($ulist)) {
						foreach ($ulist as $lastlist) {
							if (!empty($lastlist)) {
								$newulist[] = $lastlist;
							}
						}
					}
				}
			}
			$ndata['finallist'] = array_unique($newulist);
			// pr($ndata);

		}

		return isset($ndata) ? $ndata : array();

	}

	/*======================= Start App Assets API =========================================*/
	public function project_risktypes($project_id = null, $user_id = null) {

		App::import("Model", "RmProjectRiskType");
		$RmProjectRiskType = new RmProjectRiskType();

		App::import("Model", "RmRiskType");
		$RmRiskType = new RmRiskType();

		$project_risks = $RmProjectRiskType->find('all', [
			'conditions' => [
				'RmProjectRiskType.project_id' => $project_id,
				'RmProjectRiskType.user_id IS NULL',
			],
		]);
		if (!isset($project_risks) || empty($project_risks)) {
			$RmRiskType = $RmRiskType->find('all', [
				'conditions' => [
					'RmRiskType.status' => 1,
				],
			]);

			if (isset($RmRiskType) && !empty($RmRiskType)) {
				$riskTypes = null;
				foreach ($RmRiskType as $key => $value) {
					$riskTypes[]['RmProjectRiskType'] = [
						'title' => $value['RmRiskType']['title'],
						'project_id' => $project_id,
					];
				}
				$RmProjectRiskType->saveAll($riskTypes);
			}

		}

		$project_risks = $RmProjectRiskType->find('all', [
			'conditions' => [
				'RmProjectRiskType.project_id' => $project_id,
			],
			'recursive' => -1,
		]);

		$user_risks = $RmProjectRiskType->find('all', [
			'conditions' => [
				'RmProjectRiskType.project_id' => $project_id,
				'RmProjectRiskType.user_id' => $user_id,
			],
		]);

		$allrisktype = array();
		if (isset($user_risks) && !empty($user_risks)) {
			$allrisktype = array_merge($user_risks, $project_risks);
		} else {
			$allrisktype = $project_risks;
		}

		return $allrisktype;
	}

	public function getProjectElementApi($project_id = null, $user_id = null) {
		$view = new View();
		$this->Common = $view->loadHelper('Common');

		App::import("Model", "Area");
		$Area = new Area();

		if (isset($project_id) && !empty($project_id)) {

			$projectwsp = $this->getProjectWorkspaces($project_id, 0);
			//$user_id = $this->Session->read('Auth.User.id');
			$p_permission = $this->Common->project_permission_details($project_id, $user_id);
			$user_project = $this->Common->userproject($project_id, $user_id);

			if (isset($projectwsp) && !empty($projectwsp)) {
				$wspareas = array();
				$areaElements = [];
				foreach ($projectwsp as $wsplist) {
					$wsparea = null;
					$wsparea = $Area->find('all', ['conditions' => [
						'Area.workspace_id' => $wsplist['Workspace']['id'],
					],
						'fields' => ['Area.id'],
						'recursive' => -1,
					]);

					$e_permission = $this->Common->element_permission_details($wsplist['Workspace']['id'], $project_id, $this->Session->read('Auth.User.id'));

					if (isset($wsparea) && !empty($wsparea)) {

						foreach ($wsparea as $k => $v) {

							$elements_details_temp = null;
							if ((isset($e_permission) && !empty($e_permission))) {
								$all_elements = $this->area_elements_permissions($v['Area']['id'], false, $e_permission);

							}
							if (((isset($user_project) && !empty($user_project)) || (isset($project_level) && $project_level == 1) || (isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level'] == 1))) {
								$all_elements = $this->area_elements($v['Area']['id']);
							}

							if (isset($all_elements) && !empty($all_elements)) {

								foreach ($all_elements as $element_index => $e_data) {

									$element = $e_data['Element'];

									$element_decisions = $element_feedbacks = [];
									if (isset($element['studio_status']) && empty($element['studio_status'])) {
										$elements_details_temp[] = ['id' => $element['id'], 'title' => strip_tags($element['title'])];
									}
								}

								$areaElements = array_merge($areaElements, $elements_details_temp);
							}
						}
					}
				}

				//======================================================================
				if (isset($areaElements) && !empty($areaElements)) {
					return $areaElements;
				}
				//======================================================================
			}
		}

	}

	public function get_project_todo($project_id = null, $user_id = null) {
		App::import("Model", "DoList");
		$doList = new DoList();
		App::import("Model", "DoListUser");
		$DoListUser = new DoListUser();
		// die('asdf');
		$data = $doList->find('all',
			[
				'joins' => [
					[
						'table' => 'do_list_users',
						'alias' => 'DoListUser',
						'type' => 'INNER',
						'conditions' => ['DoList.id = DoListUser.do_list_id'],
					],
				],
				'conditions' => [
					'DoList.project_id' => $project_id, 'DoList.parent_id' => 0, 'DoList.sign_off' => 0,
					'OR' => [
						'DoList.user_id' => $user_id,
						'DoListUser.owner_id' => $user_id,
						'DoListUser.user_id' => $user_id,
					],
				],
			]);
		$list = $userSharedTodo = [];
		if (isset($data) && !empty($data)) {
			$list = Set::extract($data, '/DoList/id');
			$list = array_unique($list);
			$userSharedTodo = $doList->find("list", array(
				'conditions' => array(
					'DoList.id' => $list,
				),
			)
			);

		}

		if (isset($userSharedTodo) && !empty($userSharedTodo)) {
			return $userSharedTodo;
		}

	}

	/*======================= End App Assets API =========================================*/
	//===================== TODO Functions =============================
	public function todoUpload($do_list_id = null) {
		App::import("Model", "DoListUpload");
		$_dolistupload = new DoListUpload();
		$todoUpload = 0;
		if (isset($do_list_id) && !empty($do_list_id)) {
			$todoUpload = $_dolistupload->find("count", array(
				'conditions' => array(
					'DoListUpload.do_list_id' => $do_list_id,
				),
			));
		}
		return $todoUpload;
	}
	public function todoUploadlist($do_list_id = null) {
		App::import("Model", "DoListUpload");
		$_dolistupload = new DoListUpload();
		$todoUpload = 0;
		if (isset($do_list_id) && !empty($do_list_id)) {
			$todoUpload = $_dolistupload->find("all", array(
				'conditions' => array(
					'DoListUpload.do_list_id' => $do_list_id,
				),
			)
			);
		}
		return $todoUpload;
	}
	//======================== Element Risk Popup  ============================
	public function getRiskbyElementid($project_id = null, $element_ids = null) {
		App::import("Model", "RmElement");
		$RmElement = new RmElement();

		$user_id = $this->Session->read('Auth.User.id');
		$user_risk = user_risks($user_id);
		$risk_elements = $RmElement->find('all', [
			'conditions' => [
				'RmElement.project_id' => $project_id,
				'RmElement.element_id' => $element_ids,
			],
			'fields' => 'RmElement.rm_detail_id',
			'recursive' => 1,
		]);

		if (isset($risk_elements) && !empty($risk_elements)) {
			$risk_elements = Set::extract($risk_elements, '/RmElement/rm_detail_id');
			//pr($risk_elements);
			$risk_elements = array_unique($risk_elements);
			//pr($risk_elements);
			$risk_elements = array_intersect($user_risk, $risk_elements);
			//pr($risk_elements); die;
			return $risk_elements;
		} else {
			return array();
		}
	}

	public function getElementByRiskId($project_id = null, $risk_id = null) {
		App::import("Model", "RmElement");
		$RmElement = new RmElement();

		$risk_elements = $RmElement->find('all', [
			'conditions' => [
				'RmElement.project_id' => $project_id,
				'RmElement.rm_detail_id' => $risk_id,
			],
			'fields' => 'RmElement.element_id',
			'recursive' => 1,
		]);
		if (isset($risk_elements) && !empty($risk_elements)) {

			$risk_elements_ids = Set::extract($risk_elements, '/RmElement/element_id');
			if (isset($risk_elements_ids) && !empty($risk_elements_ids)) {
				array_unique($risk_elements_ids);
				$data = $this->_elements->find('all', [
					'recursive' => -1,
					'conditions' => ['Element.id' => $risk_elements_ids],
				]);
				if (isset($data) && !empty($data)) {
					return $data;
				} else {
					return array();
				}
			}
		} else {
			return array();
		}
	}

	public function getRiskById($id = null) {
		App::import("Model", "RmDetail");
		$RmDetail = new RmDetail();

		$risk_details = $RmDetail->find('first', [
			'conditions' => [
				'RmDetail.id' => $id,
			],
			'recursive' => -1,
		]);
		//pr($risk_details); die;
		if (isset($risk_details) && !empty($risk_details)) {
			return $risk_details;
		} else {
			return array();
		}
	}

	public function getProjectRiskTypeName($type_id = null) {
		App::import("Model", "RmProjectRiskType");
		$RmProjectRiskType = new RmProjectRiskType();

		$risk_type = $RmProjectRiskType->find('first', [
			'conditions' => [
				'RmProjectRiskType.id' => $type_id,
			],
			'recursive' => 1,
		]);

		if (isset($risk_type) && !empty($risk_type)) {
			return $risk_type['RmProjectRiskType']['title'];
		} else {
			return 'N/A';
		}
	}

	// Get Project Workspace Elements by Workspace
	public function project_workspace_elements($project_id = null, $workspace_id = null, $type = null) {

		$data = null;

		if (!isset($project_id) || empty($project_id)) {
			return $data;
		}

		$workspaces = $elements = $area_ids = null;

		if (isset($workspace_id) && !empty($workspace_id)) {

			$areas = $this->workspace_areas($workspace_id, false, true);
			if (isset($areas) && !empty($areas)) {
				if (is_array($area_ids)) {
					$area_ids = array_merge($area_ids, array_values($areas));
				} else {
					$area_ids = array_values($areas);
				}
			}
		}

		if (isset($area_ids) && !empty($area_ids)) {

			$query = '';

			$query .= 'SELECT element.*';
			$query .= 'FROM elements as element ';
			$query .= "WHERE element.area_id IN (" . implode(',', $area_ids) . ") ";

			if (isset($type) && !empty($type) && $type == 'completed') {
				$query .= "AND element.date_constraints = 1 ";
				$query .= "AND element.sign_off = 1 ";
			}
			$data = $this->_elements->query($query);
		}

		return $data;
	}

	public function wsp_permission_risk_element($project_id = null, $workspace_id = null, $risk_id = null) {
		App::import("Model", "RmElement");
		$RmElement = new RmElement();
		$view = new View();
		$common = $view->loadHelper('Common');
		$group = $view->loadHelper('Group');

		$e_permission = array();
		$project_level = 0;

		$user_id = $this->Session->read('Auth.User.id');
		$p_permission = $common->project_permission_details($project_id, $user_id);
		$user_project = $common->userproject($project_id, $user_id);
		$group_id = $group->GroupIDbyUserID($project_id, $user_id);
		//$project_workspace = get_project_workspace($project_id, true);

		if (isset($workspace_id) && !empty($workspace_id)) {

			$wsp_permission = $common->wsp_permission_details($this->workspace_pwid($workspace_id), $project_id, $user_id);

			if (isset($group_id) && !empty($group_id)) {

				$group_permission = $group->group_permission_details($project_id, $group_id);

				if (isset($group_permission['ProjectPermission']['project_level']) && $group_permission['ProjectPermission']['project_level'] == 1) {
					$project_level = $group_permission['ProjectPermission']['project_level'];
				}
				$wsp_permission = $group->group_wsp_permission_details($this->workspace_pwid($workspace_id), $project_id, $group_id);
			}

			if ((isset($user_project) && !empty($user_project)) || (isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level'] == 1) || (isset($project_level) && $project_level == 1)) {

				$e_permission = $this->project_workspace_elements($project_id, $workspace_id);
				if (isset($e_permission) && !empty($e_permission)) {
					$e_permission = Set::extract($e_permission, '/element/id');
				}

			} else if (isset($wsp_permission) && !empty($wsp_permission)) {
				$e_permission = $common->element_permission_details($workspace_id, $project_id, $user_id);

				if ((isset($group_id) && !empty($group_id))) {

					if (isset($e_permission) && !empty($e_permission)) {
						$ge_permissions = $group->group_element_permission_details($workspace_id, $project_id, $group_id);
						$e_permission = array_merge($e_permission, $ge_permissions);
					} else {
						$e_permission = $group->group_element_permission_details($workspace_id, $project_id, $group_id);
					}
				}
			}
		}

		$risk_elements = array();
		$risk_elements = $RmElement->find('all', [
			'conditions' => [
				'RmElement.project_id' => $project_id,
				'RmElement.rm_detail_id' => $risk_id,
			],
			'fields' => 'element_id',
			'recursive' => -1,
		]);

		$intersectElements = array();
		if (isset($risk_elements) && !empty($risk_elements)) {

			$risk_elements = Set::extract($risk_elements, '/RmElement/element_id');
			$risk_elements = array_unique($risk_elements);

			if (!empty($risk_elements) && !empty($risk_elements)) {
				$intersectElements = array_intersect($e_permission, $risk_elements);
			}
		}

		return $intersectElements;
	}

	public function wsp_permission_risk_area_element($project_id = null, $workspace_id = null, $area_id = null, $risk_id = null) {
		App::import("Model", "RmElement");
		$RmElement = new RmElement();
		$view = new View();
		$common = $view->loadHelper('Common');
		$group = $view->loadHelper('Group');

		$e_permission = array();
		$project_level = 0;

		$user_id = $this->Session->read('Auth.User.id');
		$p_permission = $common->project_permission_details($project_id, $user_id);
		$user_project = $common->userproject($project_id, $user_id);
		$group_id = $group->GroupIDbyUserID($project_id, $user_id);

		if (isset($workspace_id) && !empty($workspace_id)) {

			$wsp_permission = $common->wsp_permission_details($this->workspace_pwid($workspace_id), $project_id, $user_id);

			if (isset($group_id) && !empty($group_id)) {

				$group_permission = $group->group_permission_details($project_id, $group_id);

				if (isset($group_permission['ProjectPermission']['project_level']) && $group_permission['ProjectPermission']['project_level'] == 1) {
					$project_level = $group_permission['ProjectPermission']['project_level'];
				}
				$wsp_permission = $group->group_wsp_permission_details($this->workspace_pwid($workspace_id), $project_id, $group_id);
			}

			if ((isset($user_project) && !empty($user_project)) || (isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level'] == 1) || (isset($project_level) && $project_level == 1)) {

				$e_permission = $this->project_area_elements($project_id, $area_id);
				if (isset($e_permission) && !empty($e_permission)) {
					$e_permission = Set::extract($e_permission, '/element/id');
				}

			} else if (isset($wsp_permission) && !empty($wsp_permission)) {
				$e_permission = $common->element_permission_details($workspace_id, $project_id, $user_id);

				if ((isset($group_id) && !empty($group_id))) {

					if (isset($e_permission) && !empty($e_permission)) {
						$ge_permissions = $group->group_element_permission_details($workspace_id, $project_id, $group_id);
						$e_permission = array_merge($e_permission, $ge_permissions);
					} else {
						$e_permission = $group->group_element_permission_details($workspace_id, $project_id, $group_id);
					}
				}
			}
		}

		$risk_elements = array();
		$risk_elements = $RmElement->find('all', [
			'conditions' => [
				'RmElement.project_id' => $project_id,
				'RmElement.rm_detail_id' => $risk_id,
			],
			'fields' => 'element_id',
			'recursive' => -1,
		]);

		$intersectElements = array();
		if (isset($risk_elements) && !empty($risk_elements)) {

			$risk_elements = Set::extract($risk_elements, '/RmElement/element_id');
			$risk_elements = array_unique($risk_elements);

			if (!empty($risk_elements) && !empty($risk_elements)) {
				$intersectElements = array_intersect($e_permission, $risk_elements);
			}
		}

		return $intersectElements;
	}

	// Risk count using area id
	public function wsp_permission_risk_area_element_count($project_id = null, $workspace_id = null, $area_id = null, $risk_id = null) {

		App::import("Model", "RmElement");
		$RmElement = new RmElement();
		$view = new View();
		$common = $view->loadHelper('Common');
		$group = $view->loadHelper('Group');

		$e_permission = array();
		$project_level = 0;

		$user_id = $this->Session->read('Auth.User.id');
		$p_permission = $common->project_permission_details($project_id, $user_id);
		$user_project = $common->userproject($project_id, $user_id);
		$group_id = $group->GroupIDbyUserID($project_id, $user_id);

		if (isset($workspace_id) && !empty($workspace_id)) {

			$wsp_permission = $common->wsp_permission_details($this->workspace_pwid($workspace_id), $project_id, $user_id);

			if (isset($group_id) && !empty($group_id)) {

				$group_permission = $group->group_permission_details($project_id, $group_id);

				if (isset($group_permission['ProjectPermission']['project_level']) && $group_permission['ProjectPermission']['project_level'] == 1) {
					$project_level = $group_permission['ProjectPermission']['project_level'];
				}
				$wsp_permission = $group->group_wsp_permission_details($this->workspace_pwid($workspace_id), $project_id, $group_id);
			}

			if ((isset($user_project) && !empty($user_project)) || (isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level'] == 1) || (isset($project_level) && $project_level == 1)) {

				$e_permission = $this->project_area_elements($project_id, $area_id);
				if (isset($e_permission) && !empty($e_permission)) {
					$e_permission = Set::extract($e_permission, '/element/id');
				}

			} else if (isset($wsp_permission) && !empty($wsp_permission)) {
				$e_permission = $common->element_permission_details($workspace_id, $project_id, $user_id);

				if ((isset($group_id) && !empty($group_id))) {

					if (isset($e_permission) && !empty($e_permission)) {
						$ge_permissions = $group->group_element_permission_details($workspace_id, $project_id, $group_id);
						$e_permission = array_merge($e_permission, $ge_permissions);
					} else {
						$e_permission = $group->group_element_permission_details($workspace_id, $project_id, $group_id);
					}
				}
			}
		}

		$user_id = $this->Session->read('Auth.User.id');
		$user_risk = user_risks($user_id);
		$risk_elements = $RmElement->find('all', [
			'conditions' => [
				'RmElement.project_id' => $project_id,
				'RmElement.element_id' => $e_permission,
			],
			'fields' => 'RmElement.rm_detail_id',
			'recursive' => 1,
		]);

		$risk_elementscnt = '';
		$risk_elements_riskids = array();
		if (isset($risk_elements) && !empty($risk_elements)) {

			$risk_elements_riskids = Set::extract($risk_elements, '/RmElement/rm_detail_id');
			$risk_elements_riskids = array_unique($risk_elements_riskids);
			$risk_elements_riskids = array_intersect($user_risk, $risk_elements_riskids);

		}

		$risk_elementscnt = array_unique($risk_elements_riskids);
		return (isset($risk_elementscnt) && !empty($risk_elementscnt)) ? count($risk_elementscnt) : 0;

	}

	// Get Project Workspace Elements by Area Id
	public function project_area_elements($project_id = null, $area_id = null, $type = null) {

		$data = null;

		if (!isset($project_id) || empty($project_id)) {
			return $data;
		}

		$workspaces = $elements = $area_ids = null;
		if (isset($area_id) && !empty($area_id)) {
			$query = '';

			$query .= 'SELECT element.*';
			$query .= 'FROM elements as element ';
			$query .= "WHERE element.area_id = " . $area_id . " ";

			if (isset($type) && !empty($type) && $type == 'completed') {
				$query .= "AND element.date_constraints = 1 ";
				$query .= "AND element.sign_off = 1 ";
			}

			$data = $this->_elements->query($query);
		}

		return $data;
	}

	//=========================================================================

	public function getUserSkills($user_id = null) {
		App::import("Model", "UserSkill");
		$UserSkill = new UserSkill();

		$userskills = array();
		$userskills = $UserSkill->find('all', array('conditions' => array('UserSkill.user_id' => $user_id), 'recursive' => -1));

		if (isset($userskills) && !empty($userskills)) {
			return $userskills;
		}
	}

	public function getSkillDetails($skill_id = null) {
		App::import("Model", "Skill");
		$Skill = new Skill();

		$userskills = array();
		$userskills = $Skill->findById($skill_id);

		if (isset($userskills) && !empty($userskills)) {
			return $userskills;
		}
	}

	// get Skill User Level and User Experience
	public function getDetailSkills($skill_id = null,$user_id =null) {
		/* App::import("Model", "SkillDetail");
		ClassRegistry::init('SkillDetail')
		$SkillDetail = new SkillDetail();
		pr($SkillDetail, 1); */

		if(!isset($user_id) || empty($user_id)){
		$user_id = $this->Session->read('Auth.User.id');
		}

		$userskills = array();
		$userskills = ClassRegistry::init('SkillDetail')->find('first',array('conditions'=>array('SkillDetail.skill_id'=>$skill_id,'SkillDetail.user_id'=>$user_id)));
		if (isset($userskills) && !empty($userskills)) {
			return $userskills;
		}

	}

	public function getSkillDetailsforUser($skill_id = null) {
		App::import("Model", "Skill");
		$Skill = new Skill();

		$userskills = array();
		$userskills = $Skill->find('first',array('conditions'=>array('Skill.id'=>$skill_id,'Skill.status'=>1)));

		if (isset($userskills) && !empty($userskills)) {
			return $userskills;
		}
	}

	/*============ function for Risk count on Update element page ========== */
	public function elementRisks($element_id) {
		App::import("Model", "RmElement");
		$RmElement = new RmElement();

		$elementriskcount = $RmElement->find('count', array('conditions' => array('RmElement.element_id' => $element_id)));

		if (isset($elementriskcount) && $elementriskcount > 0) {
			return $elementriskcount;
		} else {
			return 0;
		}

	}

	public function elementRisksSignoff($element_id, $user_id) {
		App::import("Model", "RmElement");
		$RmElement = new RmElement();
		App::import("Model", "RmUser");
		$RmUsers = new RmUser();
		App::import("Model", "RmDetail");
		$RmDetails = new RmDetail();

		$rm_deatils = $RmElement->find('all', array('conditions' => array('RmElement.element_id' => $element_id)));
		$elementriskcount = 0;
		if (isset($rm_deatils) && !empty($rm_deatils)) {

			foreach( $rm_deatils as $rmelement ){
				$rmdetailid = $rmelement['RmElement']['rm_detail_id'];
				$elementriskcount = $RmDetails->find('count', array('conditions' => array('RmDetail.status !=' => 3, 'RmDetail.id' => $rmdetailid)) );
			}

		}

		if (isset($elementriskcount) && $elementriskcount > 0) {
			return $elementriskcount;
		} else {
			return 0;
		}

	}


	public function elementRisksUsers($element_id, $user_id) {
		App::import("Model", "RmElement");
		$RmElement = new RmElement();
		App::import("Model", "RmUser");
		$RmUsers = new RmUser();
		App::import("Model", "RmDetail");
		$RmDetails = new RmDetail();

		$rm_deatils = $RmElement->find('all', array('conditions' => array('RmElement.element_id' => $element_id)));

		$rm_detailid = array();
		if (isset($rm_deatils) && !empty($rm_deatils)) {

			foreach ($rm_deatils as $listdeatils) {

				$rmdetailid = $listdeatils['RmElement']['rm_detail_id'];

				$elementriskcount = $RmUsers->find('count', array('conditions' => array('RmUser.user_id' => $user_id, 'RmUser.rm_detail_id' => $rmdetailid)));

				if ($elementriskcount > 0) {

					$rm_detailid[] = $rmdetailid;

				} else {

					$elementriskcount = $RmDetails->find('count', array('conditions' => array('RmDetail.user_id' => $user_id, 'RmDetail.id' => $rmdetailid)));

					if ($elementriskcount > 0) {
						$rm_detailid[] = $rmdetailid;
					}

				}
			}
		}

		if (isset($rm_detailid) && $rm_detailid > 0) {
			return count(array_unique($rm_detailid));
		} else {
			return 0;
		}

	}

	public function projectRisksCnt($project_id) {
		App::import("Model", "RmDetail");
		$RmDetails = new RmDetail();

		$projectriskcount = $RmDetails->find('count', array('conditions' => array('RmDetail.project_id' => $project_id)));

		if (!empty($projectriskcount) && $projectriskcount > 0) {
			return $projectriskcount;
		} else {
			return 0;
		}
	}

	public function projectTodoCnt($project_id, $user_id) {
		App::import("Model", "DoList");
		$DoList = new DoList();

		$projecttodocount = $DoList->find('count', array('conditions' => array('DoList.project_id' => $project_id, 'DoList.user_id' => $user_id, 'OR' => ['DoList.parent_id <=' => 0, 'DoList.parent_id IS NULL'], 'DoList.is_archive !=' => 1)));

		if (!empty($projecttodocount) && $projecttodocount > 0) {
			return $projecttodocount;
		} else {
			return 0;
		}
	}

	public function projectSktechCnt($project_id, $user_id) {
		App::import("Model", "ProjectSketch");
		$ProjectSketche = new ProjectSketch();

		$projectsktchcount = $ProjectSketche->find('count', array('conditions' => array('ProjectSketch.project_id' => $project_id, 'ProjectSketch.user_id' => $user_id, "ProjectSketch.parent_id " => 0)));

		if (!empty($projectsktchcount) && $projectsktchcount > 0) {
			return $projectsktchcount;
		} else {
			return 0;
		}
	}

	public function project_program_cnt($user_id, $project_id) {

		App::import("Model", "Program");
		$Program = new Program();
		App::import("Model", "ProjectProgram");
		$ProjectProgram = new ProjectProgram();

		$conditions['Program.user_id'] = $user_id;
		$conditions['ProjectProgram.project_id'] = $project_id;
		$programs = $ProjectProgram->find('count', [
			'conditions' => $conditions,
			'fields' => [
				'Program.*',
			],
			'group' => ['Program.id'],
		]);

		if ($programs > 0 && !empty($programs)) {
			return $programs;
		} else {
			return 0;
		}
	}

	public function get_projectbyidwithorder($id = null) {

		$data = ClassRegistry::init('Project')->find('all',
			array(
				'conditions' => array('Project.id' => $id),
				'order' => 'Project.modified DESC',
			)
		);
		return $data;
	}

	public function element_sharer($element_id = null) {
		$view = new View();
		$common = $view->loadHelper('Common');
		$group = $view->loadHelper('Group');

		$sharedData = $this->element_users($element_id);

		$element_owner = $all_sharer = $return_data = [];
		$unbind = ['hasMany' => ['ProjectPermission', 'WorkspacePermission', 'ElementPermission', 'UserProject', 'UserSetting'], 'hasOne' => ['UserInstitution']];
		if (isset($sharedData) && !empty($sharedData)) {
			foreach ($sharedData as $k => $v) {
				$elPermit = $v['ElementPermission'];

				if (isset($elPermit['project_group_id']) && !empty($elPermit['project_group_id'])) {

					$groupData = $group->ProjectGroupDetail($elPermit['project_group_id'])['ProjectGroup'];

					$group_users = $group->group_users($elPermit['project_group_id'], true);

					foreach ($group_users as $gk => $gv) {
						$userDetail = $this->get_user($gv, $unbind, 1);
						$user_id = $gv;
						$element_project_id = $elPermit['project_id'];
						$elemt_wp_id = $elPermit['workspace_id'];

						$wsp_permission = $common->wsp_permission_details($this->workspace_pwid($elemt_wp_id), $element_project_id, $user_id);
						$p_permission = $common->project_permission_details($element_project_id, $user_id);

						$user_project = $common->userproject($element_project_id, $user_id);

						$grp_id = $group->GroupIDbyUserID($element_project_id, $user_id);

						if (isset($grp_id) && !empty($grp_id)) {

							$group_permission = $group->group_permission_details($element_project_id, $grp_id);
							if (isset($group_permission['ProjectPermission']['project_level']) && $group_permission['ProjectPermission']['project_level'] == 1) {
								$project_level = $group_permission['ProjectPermission']['project_level'];
							}
							$wsp_permission = $group->group_wsp_permission_details($this->workspace_pwid($elemt_wp_id), $element_project_id, $grp_id);
						}

						if ((isset($user_project) && !empty($user_project)) || (isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level'] == 1) || (isset($project_level) && $project_level == 1) || (isset($wsp_permission['0']['WorkspacePermission']['permit_read']) && $wsp_permission['0']['WorkspacePermission']['permit_read'])) {
							$all_sharer[] = $userDetail['UserDetail']['user_id'];
						}

					}
				} else
				if (isset($elPermit['user_id']) && !empty($elPermit['user_id'])) {

					$userDetail = $this->get_user($elPermit['user_id'], $unbind, 1);

					if (isset($elPermit['is_editable']) && !empty($elPermit['is_editable'])) {
						$element_owner[] = $userDetail['UserDetail']['user_id'];
					} else {
						$user_id = $elPermit['user_id'];
						$element_project_id = $elPermit['project_id'];
						$elemt_wp_id = $elPermit['workspace_id'];

						$wsp_permission = $common->wsp_permission_details($this->workspace_pwid($elemt_wp_id), $element_project_id, $user_id);
						$p_permission = $common->project_permission_details($element_project_id, $user_id);

						$user_project = $common->userproject($element_project_id, $user_id);

						$grp_id = $group->GroupIDbyUserID($element_project_id, $user_id);

						if (isset($grp_id) && !empty($grp_id)) {

							$group_permission = $group->group_permission_details($element_project_id, $grp_id);
							if (isset($group_permission['ProjectPermission']['project_level']) && $group_permission['ProjectPermission']['project_level'] == 1) {
								$project_level = $group_permission['ProjectPermission']['project_level'];
							}
							$wsp_permission = $group->group_wsp_permission_details($this->workspace_pwid($elemt_wp_id), $element_project_id, $grp_id);
						}

						if ((isset($user_project) && !empty($user_project)) || (isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level'] == 1) || (isset($project_level) && $project_level == 1) || (isset($wsp_permission['0']['WorkspacePermission']['permit_read']) && $wsp_permission['0']['WorkspacePermission']['permit_read'])) {
							$all_sharer[] = $userDetail['UserDetail']['user_id'];
						}

					}
				}
			}
		}
		$return_data = ['all_sharer' => $all_sharer, 'element_owner' => $element_owner];
		return $return_data;
	}

	//======================= Task Center New Functions =========================
	// Get Project Elements Udated at 1st May 2019
	public function projectElementsTaskCenter($project_id = null, $type = null) {

		$data = null;

		if (!isset($project_id) || empty($project_id)) {
			return $data;
		}

		$workspaces = $elements = $area_ids = null;

		$workspaces = $this->get_project_workspace_task_center($project_id);

		if (isset($workspaces) && !empty($workspaces)) {

			foreach ($workspaces as $key => $value) {

				$workspace = $value['Workspace'];

				$areas = $this->workspace_areas($workspace['id'], false, true);

				if (isset($areas) && !empty($areas)) {
					if (is_array($area_ids)) {
						$area_ids = array_merge($area_ids, array_values($areas));
					} else {
						$area_ids = array_values($areas);
					}

				}

			}
		}

		if (isset($area_ids) && !empty($area_ids)) {

			$query = '';

			$query .= 'SELECT element.id ';
			$query .= 'FROM elements as element ';
			$query .= "WHERE element.area_id IN (" . implode(',', $area_ids) . ") ";
			$query .= "AND element.studio_status != 1 ";

			if (isset($type) && !empty($type) && $type == 'completed') {
				$query .= "AND element.date_constraints = 1 ";
				$query .= "AND element.sign_off = 1 ";
			}

			$data = $this->_elements->query($query);
		}

		return $data;
	}

	public function get_project_workspace_task_center($project_id = null, $studio_status = 0) {
		$ws = null;

		if (!empty($project_id) && $this->_project_workspaces->hasAny(['ProjectWorkspace.project_id' => $project_id])) {

			$this->_project_workspaces->Behaviors->load('Containable');

			$cond['ProjectWorkspace.project_id'] = $project_id;
			$cond['Workspace.id !='] = '';
			$cond['Workspace.title !='] = '';

			if (isset($studio_status) && !empty($studio_status)) {
				$cond['Workspace.studio_status'] = $studio_status;
			} else {
				$cond['Workspace.studio_status'] = $studio_status;
			}

			$ws = $this->_project_workspaces->find('all', ['conditions' => $cond, 'contain' => 'Workspace', 'fields' => 'Workspace.id']);

		}

		return $ws;
	}

	//=================================================================================
	//                          New Functions for Project Center
	//=================================================================================
	public function checkShareElement($project_id = null, $element_id = null){
		App::import("Model", "ShareElement");
		$shareElement = new ShareElement();

		$check  = $shareElement->find('count',array('conditions'=>array('ShareElement.project_id'=>$project_id,'ShareElement.element_id'=>$element_id) ));
		if( $check > 0 ){

		} else {
			return $element_id;
		}

	}


	// For Share Element Projects
	public function share_element_projects($fields = null, $align_id = null, $category_id = null) {

		$conditions = null;
		$conditions['UserProject.user_id !='] = '';
		$conditions['UserProject.project_id !='] = '';

		if (!isset($fields) || empty($fields)) {
			$fields = ['UserProject.*', 'Project.*'];
		}

		$projects = $this->_projects->find('all', array(
			'joins' => array(
				array(
					'table' => 'user_projects',
					'alias' => 'UserProject',
					'type' => 'INNER',
					'conditions' => array(
						'UserProject.project_id = Project.id',
					),
				),
			),
			'conditions' => $conditions,
			'fields' => $fields,
			'order' => 'UserProject.modified DESC',
			'group' => ['UserProject.project_id'],
			'recursive' => -1,
		));

		return (isset($projects) && !empty($projects)) ? $projects : false;

	}

	// Get User's Own Projects
	public function my_projects_list_projectCenter($fields = null, $align_id = null, $category_id = null) {

		$user_id = $this->Session->read('Auth.User.id');

		$conditions = null;
		$conditions['UserProject.user_id'] = $user_id;
		$conditions['UserProject.status'] = 1;
		$conditions['UserProject.project_id !='] = '';
		$conditions['Project.studio_status'] = 0;
		if (!isset($fields) || empty($fields)) {
			$fields = ['UserProject.*', 'Project.*'];
		}

		if (isset($align_id) && !empty($align_id)) {
			$conditions['Project.aligned_id'] = $align_id;
		}

		if (isset($category_id) && !empty($category_id)) {
			$conditions['Project.category_id'] = $category_id;
		}

		$projects = $this->_projects->find('all', array(
			'joins' => array(
				array(
					'table' => 'user_projects',
					'alias' => 'UserProject',
					'type' => 'INNER',
					'conditions' => array(
						'UserProject.project_id = Project.id',
					),
				),
			),
			'conditions' => $conditions,
			'fields' => $fields,
			'order' => 'UserProject.modified DESC',
			'group' => ['UserProject.project_id'],
			'recursive' => -1,
		));

		return (isset($projects) && !empty($projects)) ? $projects : false;

	}

	// Get User's Shared Projects
	public function shared_projects_list_projectCenter($fields = null, $align_id = null) {

		$user_id = $this->Session->read('Auth.User.id');

		$conditions = null;
		$conditions['ProjectPermission.owner_id'] = $user_id;
		$conditions['AND'] = ['ProjectPermission.user_id IS NOT NULL'];
		$conditions['OR'] = ['ProjectPermission.parent_id IS NULL', 'ProjectPermission.parent_id' => 0];

		if (!isset($fields) || empty($fields)) {
			$fields = ['UserProject.*', 'Project.*'];
		}

		$permit_projects = $this->_pr_permit->find('all', array(
			'joins' => array(
				array(
					'table' => 'user_projects',
					'alias' => 'UserProject',
					'type' => 'INNER',
					'conditions' => array(
						'UserProject.id = ProjectPermission.user_project_id',
					),
				),
			),
			'conditions' => $conditions,
			'fields' => ['ProjectPermission.user_project_id', 'ProjectPermission.id'],
			'group' => ['ProjectPermission.user_project_id'],
			'recursive' => -1,
		));

		if (isset($permit_projects) && !empty($permit_projects)) {

			$userProjectId = Set::extract($permit_projects, '/ProjectPermission/user_project_id');

			foreach ($permit_projects as $k => $v) {

				$pconditions['UserProject.id'] = $v['ProjectPermission']['user_project_id'];
				if (isset($align_id) && !empty($align_id)) {
					$pconditions['Project.aligned_id'] = $align_id;
				}

				$projects = $this->_projects->find('first', array(
					'joins' => array(
						array(
							'table' => 'user_projects',
							'alias' => 'UserProject',
							'type' => 'INNER',
							'conditions' => array(
								'UserProject.project_id = Project.id',
							),
						),
					),
					'conditions' => $pconditions,
					'fields' => $fields,
					'order' => 'UserProject.modified DESC',
					'group' => ['UserProject.project_id'],
					'recursive' => -1,
				));
				if (isset($projects['Project']) && !empty($projects['Project'])) {
					$projects = $projects['Project'];
					$data[] = ['ProjectPermission' => $v['ProjectPermission'], 'Project' => $projects];
				}
			}

		}
		// pr($data, 1);
		return (isset($data) && !empty($data)) ? $data : false;

	}

	// Get User's Received Projects
	public function received_projects_list_projectCenter($fields = null, $align_id = null, $category_id = null) {

		$user_id = $this->Session->read('Auth.User.id');

		$conditions = null;
		$conditions['ProjectPermission.user_id'] = $user_id;
		$conditions['AND'] = ['ProjectPermission.user_id IS NOT NULL'];
		$conditions['AND'] = ['ProjectPermission.share_by_id IS NOT NULL'];
		$conditions['AND'] = ['ProjectPermission.user_project_id IS NOT NULL'];
		$conditions['AND'] = ['ProjectPermission.owner_id IS NOT NULL'];

		if (!isset($fields) || empty($fields)) {
			$fields = ['UserProject.*', 'Project.*'];
		}

		$permit_projects = $this->_pr_permit->find('all', array(
			'joins' => array(
				array(
					'table' => 'user_projects',
					'alias' => 'UserProject',
					'type' => 'INNER',
					'conditions' => array(
						'UserProject.id = ProjectPermission.user_project_id',
					),
				),
			),
			'conditions' => [
				'ProjectPermission.user_id' => $user_id,
				'ProjectPermission.user_id IS NOT NULL',
				'ProjectPermission.share_by_id IS NOT NULL',
				'ProjectPermission.user_project_id IS NOT NULL',
				'ProjectPermission.user_project_id IS NOT NULL',
			],
			'fields' => ['ProjectPermission.user_project_id', 'ProjectPermission.id'],
			'group' => ['ProjectPermission.user_project_id'],
			'recursive' => -1,
		));

		if (isset($permit_projects) && !empty($permit_projects)) {

			$userProjectId = Set::extract($permit_projects, '/ProjectPermission/user_project_id');
			foreach ($permit_projects as $k => $v) {

				$pconditions['UserProject.id'] = $v['ProjectPermission']['user_project_id'];
				if (isset($align_id) && !empty($align_id)) {
					$pconditions['Project.aligned_id'] = $align_id;
				}
				if (isset($category_id) && !empty($category_id)) {
					$pconditions['Project.category_id'] = $category_id;
				}

				$projects = $this->_projects->find('first', array(
					'joins' => array(
						array(
							'table' => 'user_projects',
							'alias' => 'UserProject',
							'type' => 'INNER',
							'conditions' => array(
								'UserProject.project_id = Project.id',
							),
						),
					),
					'conditions' => $pconditions,
					'fields' => $fields,
					'order' => 'UserProject.modified DESC',
					'group' => ['UserProject.project_id'],
					'recursive' => -1,
				));
				if (isset($projects['Project']) && !empty($projects['Project'])) {
					$projects = $projects['Project'];
					$data[] = ['ProjectPermission' => $v['ProjectPermission'], 'Project' => $projects];
				}
			}

		}
		// pr($data, 1);
		return (isset($data) && !empty($data)) ? $data : false;

	}

	// Get User's Group Received Projects
	public function group_received_projects_list_projectCenter($fields = null, $align_id = null, $category_id = null) {

		$user_id = $this->Session->read('Auth.User.id');

		App::import("Model", "ProjectGroupUser");
		$projectgroupuser = new ProjectGroupUser();

		$data = $conditions = null;
		$conditions['ProjectPermission.user_id'] = $user_id;

		if (!isset($fields) || empty($fields)) {
			$fields = ['UserProject.*', 'Project.*'];
		}

		$permit_projects = $projectgroupuser->find('all', ['conditions' => ['ProjectGroupUser.user_id' => $user_id, 'ProjectGroupUser.approved' => 1], 'recursive' => 1, 'fields' => array('ProjectGroup.user_project_id')]);

		if (isset($permit_projects) && !empty($permit_projects)) {

			//$userProjectId = Set::extract($permit_projects, '/UserProject/id');
			//pr($permit_projects);
			foreach ($permit_projects as $k => $v) {

				$pconditions['UserProject.id'] = $v['ProjectGroup']['user_project_id'];
				if (isset($align_id) && !empty($align_id)) {
					$pconditions['Project.aligned_id'] = $align_id;
				}
				if (isset($category_id) && !empty($category_id)) {
					$pconditions['Project.category_id'] = $category_id;
				}

				$projects = $this->_projects->find('first', array(
					'joins' => array(
						array(
							'table' => 'user_projects',
							'alias' => 'UserProject',
							'type' => 'INNER',
							'conditions' => array(
								'UserProject.project_id = Project.id',
							),
						),
					),
					'conditions' => $pconditions,
					'fields' => $fields,
					'order' => 'UserProject.modified DESC',
					'group' => ['UserProject.project_id'],
					'recursive' => -1,
				));

				if (isset($projects['Project']) && !empty($projects['Project'])) {
					$projects = $projects['Project'];

					//$data[] = ['ProjectGroup' => $v['ProjectGroup'], 'ProjectGroupUser' => $v['ProjectGroupUser'], 'Project' => $projects];
					$data[] = [ 'projects' => $projects];
				}

			}
		}

		return (isset($data) && !empty($data)) ? $data : false;

	}

	// Get User's Group Received Projects
	public function propagated_projects_list_projectCenter($fields = null, $align_id = null) {

		$user_id = $this->Session->read('Auth.User.id');

		App::import("Model", "ProjectGroupUser");
		$projectgroupuser = new ProjectGroupUser();

		$data = $conditions = null;

		$conditions['ProjectPermission.share_by_id'] = $user_id;
		$conditions['ProjectPermission.owner_id !='] = $user_id;
		$conditions['AND'] = ['ProjectPermission.user_id  IS NOT NULL'];
		$conditions['OR'] = ['ProjectPermission.parent_id IS NOT NULL', 'ProjectPermission.parent_id !=' => 0];

		if (!isset($fields) || empty($fields)) {
			$fields = ['UserProject.*', 'Project.*'];
		}

		$permit_projects = $this->_pr_permit->find('all', array(
			'joins' => array(
				array(
					'table' => 'user_projects',
					'alias' => 'UserProject',
					'type' => 'INNER',
					'conditions' => array(
						'UserProject.id = ProjectPermission.user_project_id',
					),
				),
			),
			'conditions' => $conditions,
			'fields' => ['ProjectPermission.user_project_id', 'ProjectPermission.id'],
			'group' => ['ProjectPermission.user_project_id'],
			'recursive' => -1,
		));

		if (isset($permit_projects) && !empty($permit_projects)) {

			//$userProjectId = Set::extract($permit_projects, '/ProjectPermission/user_project_id');

			foreach ($permit_projects as $k => $v) {

				$pconditions['UserProject.id'] = $v['ProjectPermission']['user_project_id'];
				if (isset($align_id) && !empty($align_id)) {
					$pconditions['Project.aligned_id'] = $align_id;
				}

				$projects = $this->_projects->find('first', array(
					'joins' => array(
						array(
							'table' => 'user_projects',
							'alias' => 'UserProject',
							'type' => 'INNER',
							'conditions' => array(
								'UserProject.project_id = Project.id',
							),
						),
					),
					'conditions' => $pconditions,
					'fields' => $fields,
					'order' => 'UserProject.modified DESC',
					'group' => ['UserProject.project_id'],
					'recursive' => -1,
				));

				if (isset($projects['Project']) && !empty($projects['Project'])) {
					$projects = $projects['Project'];
					$data[] = ['ProjectPermission' => $v['ProjectPermission'], 'Project' => $projects];
				}

			}
		}
		// pr($data, 1);
		return (isset($data) && !empty($data)) ? $data : false;

	}

	//=========================================================================================

	function project_element_type($project_id = null) {
		App::import("Model", "ProjectElementType");
		$_pet = new ProjectElementType();

		$data = $_pet->find('list', [
			// 'fields' => ['ProjectElementType.id','ProjectElementType.title'],
			'conditions' => [
				'ProjectElementType.project_id' => $project_id,
				'ProjectElementType.type_status' => 1,
			], 'order' => 'ProjectElementType.title ASC',

		]);

		return (isset($data) && !empty($data)) ? $data : false;

	}

	function checkCurrentProject() {

		$user_id = $this->Session->read('Auth.User.id');
		$cntProject = ClassRegistry::init('CurrentProject')->find('first', array('conditions' => array('CurrentProject.user_id' => $user_id)));
		if (isset($cntProject) && !empty($cntProject)) {
			return $cntProject;
		} else {
			return false;
		}
	}

	function userPermissionId($user_id=null, $project_id=null , $share_by_id = null) {

		$project_id = project_upid($project_id);

		$cntProject = ClassRegistry::init('ProjectPermission')->find('first', array('conditions' => array('ProjectPermission.user_id' => $user_id,'ProjectPermission.user_project_id' => $project_id,'ProjectPermission.share_by_id' => $share_by_id), 'fields' => ['ProjectPermission.id' ]));
		if (isset($cntProject) && !empty($cntProject)) {
			return $cntProject['ProjectPermission']['id'];
		} else {
			return 0;
		}
	}

	function ProjectSharedByThisUser(  $project_id=null , $share_by_id = null) {

		$project_id = project_upid($project_id);

		$cntProject = ClassRegistry::init('ProjectPermission')->find('count', array('conditions' => array( 'ProjectPermission.user_project_id' => $project_id,'ProjectPermission.share_by_id' => $share_by_id), 'fields' => ['ProjectPermission.id' ]));

			return $cntProject ;

	}


	function checkCurrentProjectList() {

		$user_id = $this->Session->read('Auth.User.id');
		$cntProject = ClassRegistry::init('CurrentProject')->find('list', array('joins' => [
					[
						'alias' => 'Project',
						'table' => 'projects',
						'type' => 'INNER',
						'conditions' => 'Project.id = CurrentProject.project_id',
					]],'conditions' => array('CurrentProject.user_id' => $user_id),'fields'=>array('CurrentProject.project_id')));
		 $title = array();
		foreach($cntProject as $crt){
			$title[$crt] =  strip_tags(getFieldDetail('Project', $crt, 'title'));
		}

		if (isset($title) && !empty($title)) {
			 natcasesort($title);
			return $title;
		} else {
			return array();
		}
	}

	function checkCurrentWspList() {

		$user_id = $this->Session->read('Auth.User.id');
		$cntProject = ClassRegistry::init('CurrentWorkspace')->find('list', array('joins' => [
					[
						'alias' => 'Workspace',
						'table' => 'workspaces',
						'type' => 'INNER',
						'conditions' => 'CurrentWorkspace.workspace_id = Workspace.id'
					]],
					'conditions' => array('CurrentWorkspace.user_id' => $user_id ),'fields'=>array('CurrentWorkspace.workspace_id')));

		$title = array();
		//pr($cntProject);
		foreach($cntProject as $crt){
			$title[$crt] =  strip_tags(getFieldDetail('Workspace', $crt, 'title'));
		}

		if (isset($title) && !empty($title)) {
			 natcasesort($title);
			return $title;
		} else {
			return array();
		}
	}

	function checkCurrentTaskList() {

		$user_id = $this->Session->read('Auth.User.id');
		$cntProject = ClassRegistry::init('CurrentTask')->find('list', array('joins' => [
					[
						'alias' => 'Element',
						'table' => 'elements',
						'type' => 'INNER',
						'conditions' => 'CurrentTask.task_id = Element.id',
					],
					[	'alias' => 'Area',
						'table' => 'areas',
						'type' => 'INNER',
						'conditions' => 'Element.area_id=Area.id',]

					],'conditions' => array('CurrentTask.user_id' => $user_id ),'fields'=>array('CurrentTask.task_id')));

		$title = array();
		//pr($cntProject);
		foreach($cntProject as $crt){
			$title[$crt] =  strip_tags(getFieldDetail('Element', $crt, 'title'));
		}

		if (isset($title) && !empty($title)) {
			 natcasesort($title);
			return $title;
		} else {
			return array();
		}
	}


	function checkCurrentProjectid($project_id) {

		$user_id = $this->Session->read('Auth.User.id');
		$cntProject = ClassRegistry::init('CurrentProject')->find('count', array('conditions' => array('CurrentProject.user_id' => $user_id, 'CurrentProject.project_id' => $project_id)));
		if (!empty($cntProject) && $cntProject > 0) {
			return true;
		} else {
			return false;
		}

	}

	function checkCurrentWorkspace($project_id, $wsp_id) {

		$user_id = $this->Session->read('Auth.User.id');
		$cntTasks = ClassRegistry::init('CurrentWorkspace')->find('count', array('conditions' => array('CurrentWorkspace.user_id' => $user_id, 'CurrentWorkspace.project_id' => $project_id, 'CurrentWorkspace.workspace_id' => $wsp_id)));
		if (!empty($cntTasks) && $cntTasks > 0) {
			return true;
		} else {
			return false;
		}
	}


	/*************** Current Tasks **************************************/
	function checkCurrentTasks($project_id,$task_id) {

		$user_id = $this->Session->read('Auth.User.id');
		$cntTasks = ClassRegistry::init('CurrentTask')->find('count', array('conditions' => array('CurrentTask.user_id' => $user_id, 'CurrentTask.project_id' => $project_id, 'CurrentTask.task_id' => $task_id)));
		if (!empty($cntTasks) && $cntTasks > 0) {
			return true;
		} else {
			return false;
		}
	}


	/********************************************************************
	********************** Start Custom Query for WSP page **************
	*********************************************************************/

	function getTaskCount($workspace_id = null){

			$current_user_id = $this->Session->read('Auth.User.id');
			$currentDate = date('Y-m-d');
			$query = 'SELECT
				user_permissions.role,
				user_permissions.user_id,
				user_permissions.project_id,
				user_permissions.workspace_id,
				user_permissions.area_id AS a_id,
				user_permissions.role AS u_role,

				user_permissions.permit_read AS p_read,
				user_permissions.permit_edit AS p_edit,
				user_permissions.permit_delete AS p_delete,
				user_permissions.permit_add AS p_add,
				user_permissions.permit_copy AS p_copy,
				user_permissions.permit_move AS p_move,

				areas.title AS a_title,

				areas.tooltip_text AS a_purpose,
				user_permissions.element_id AS e_id,
				# elements.sort_order, # Uncomment for testing
				elements.title AS e_title,
				elements.description AS e_description,
				elements.comments AS e_outcome,
				(CASE
					WHEN (elements.sign_off=1) THEN "Complete" # Element is signed off
					WHEN (elements.date_constraints=0) THEN "None" # No start or end dates set
					WHEN (DATE(NOW()) BETWEEN DATE(elements.start_date) AND DATE(elements.end_date)) THEN "In Progress" # Currently between start and end dates
					WHEN (DATE(NOW())<DATE(elements.start_date)) THEN "Pending" # Element start date is in the future
					WHEN (DATE(elements.end_date)<DATE(NOW()) ) THEN "Overdue" # End date has passed
					ELSE "Unknown"
				END) AS e_status,
				(select count(elements.id) from elements
				inner join user_permissions usp on usp.element_id = elements.id
				where DATE(elements.end_date) < DATE(NOW()) AND elements.date_constraints > 0 AND elements.sign_off != 1 and  usp.user_id='.$current_user_id.'
				and usp.element_id=elements.id
				AND usp.workspace_id='.$workspace_id.'
				AND usp.area_id IS NOT NULL ) as overdue_count,
				SUM(elements.sign_off =1 ) as sign_off,
				COUNT(distinct(elements.id)) AS e_count,
				COUNT(distinct(element_links.id)) AS e_links_count,
				COUNT(distinct(element_notes.id)) AS e_notes_count,
				COUNT(distinct(element_documents.id)) AS e_documents_count,
				COUNT(distinct(element_mindmaps.id)) AS e_mind_maps_count,
				count(distinct(votes.id)) AS e_votes_in_progress_count,
				count(distinct(feedback.id)) as e_feedbacks_in_progress_count,
				count(distinct(element_decisions.id)) AS e_decision_status,
				#COUNT(distinct(rm_elements.id)) AS e_risks_count

				(select count( rm_details.id ) from rm_details
				where rm_details.id in ( select rm_elements.rm_detail_id from rm_elements where  rm_elements.element_id in (select up.element_id from user_permissions up where up.workspace_id = user_permissions.workspace_id and up.workspace_id =  '.$workspace_id.'  and  up.element_id is not null))
				) AS e_risks_count


			FROM
				user_permissions
			INNER JOIN
				areas
				ON areas.id=user_permissions.area_id
			INNER JOIN
				elements
				ON elements.id=user_permissions.element_id
			LEFT JOIN
				element_links
				ON element_links.element_id=user_permissions.element_id
				AND element_links.status=1 # Assumed to mean active
			LEFT JOIN element_notes
				ON element_notes.element_id=user_permissions.element_id
				AND element_notes.status=1 # Assumed to mean active
			LEFT JOIN element_documents
				ON element_documents.element_id=user_permissions.element_id
				AND element_documents.status=1 # Assumed to mean active
			LEFT JOIN
				element_mindmaps
				ON element_mindmaps.element_id=user_permissions.element_id
				AND element_mindmaps.status=1 # Assumed to mean active
			LEFT JOIN
				element_decisions
				ON element_decisions.element_id=user_permissions.element_id
				AND (element_decisions.sign_off=0 OR element_decisions.sign_off IS NULL)  # Assumed to mean active

			LEFT JOIN
				feedback
				ON feedback.element_id=user_permissions.element_id
				AND feedback.status=1 # Assumed to mean active
				AND ((DATE(NOW()) BETWEEN DATE(feedback.start_date) AND DATE(feedback.end_date)) && (feedback.sign_off =0))
				AND feedback.sign_off=0 # In Progress
			LEFT JOIN
				votes
				ON votes.element_id=user_permissions.element_id
				AND ((DATE(NOW()) BETWEEN DATE(votes.start_date) AND DATE(votes.end_date)) && (votes.is_completed =0))
				# There is no status column
				AND votes.is_completed=0

			WHERE
				user_permissions.user_id='.$current_user_id.' # SET: CURRENT USER ID
				AND user_permissions.workspace_id='.$workspace_id.' # SET: CURRENT WORKSPACE ID
				AND user_permissions.area_id IS NOT NULL # Elements only, workspace row is not included in result
				# Uncomment and edit to select subset of status (like UI dropdown)
				#AND (
					#(elements.date_constraints=0) # Status=None
					#OR
					#(elements.sign_off=0 AND DATE(NOW())<DATE(elements.start_date)) # Status=Pending
					#OR
					#(elements.sign_off=0 AND DATE(NOW()) BETWEEN DATE(elements.start_date) AND DATE(elements.end_date)) # Status=In Progress
					#OR
					#(elements.sign_off=0 AND DATE(NOW())>DATE(elements.end_date)) # Status=Overdue
					#OR
					#(elements.sign_off=1) # Status=Complete
				#)
			GROUP BY
				user_permissions.workspace_id
			ORDER BY
				user_permissions.area_id, elements.sort_order';


		return ClassRegistry::init('UserPermission')->query($query);

	}


	function getTaskCountAsset($workspace_id = null){

			$current_user_id = $this->Session->read('Auth.User.id');
			$currentDate = date('Y-m-d');
			$query = "SELECT
							(dc_prg + dc_cmp) AS dc_tot,
							dc_prg,
							dc_cmp,
							(fb_nst + fb_prg + fb_ovd + fb_cmp) AS fb_tot,
							fb_nst,
							fb_prg,
							fb_ovd,
							fb_cmp,
							(vt_nst + vt_prg + vt_ovd + vt_cmp) AS vt_tot,
							vt_nst,
							vt_prg,
							vt_ovd,
							vt_cmp,
							elinks_count as links_tot,
							enotes_count as notes_tot,
							edocs_count as docs_tot,
							emms_count as mms_tot,
							(dc_prg + dc_cmp + fb_nst + fb_prg + fb_ovd + fb_cmp + vt_nst + vt_prg + vt_ovd + vt_cmp + elinks_count + enotes_count + edocs_count + emms_count) as total_assets

						FROM
						(
						SELECT
							wd.wid,
							COUNT(IF(wd.dc_status = 'In Progress', 1, NULL)) AS dc_prg,
							COUNT(IF(wd.dc_status = 'Completed', 1, NULL)) AS dc_cmp
						FROM
						(
							#get decision status counts
							SELECT
								up.workspace_id AS wid,
								up.element_id AS tid,
								CASE
									WHEN ed.sign_off = 0 THEN 'In Progress'
									WHEN ed.sign_off = 1 THEN 'Completed'
									ELSE 'None'
								END AS dc_status
							FROM
								user_permissions up
							LEFT JOIN element_decisions ed ON
								up.element_id = ed.element_id
							WHERE
								up.user_id = ".$current_user_id." #SET : CURRENT USER id
								AND up.workspace_id = ".$workspace_id." #SET : CURRENT workspace id
								AND up.element_id IS NOT NULL # elements only
						) AS wd #workspace decisions
						) AS wdc
						LEFT JOIN
						(
						SELECT
							wf.wid,
							COUNT(IF(wf.fb_status = 'Not Started', 1, NULL)) AS fb_nst,
							COUNT(IF(wf.fb_status = 'In Progress', 1, NULL)) AS fb_prg,
							COUNT(IF(wf.fb_status = 'Overdue', 1, NULL)) AS fb_ovd,
							COUNT(IF(wf.fb_status = 'Completed', 1, NULL)) AS fb_cmp
						FROM
						(
							#get feedback status counts
							SELECT
								up.workspace_id AS wid,
								up.element_id AS tid,
								CASE
									WHEN fb.sign_off = 1 THEN 'Completed'
									WHEN Date(NOW()) > Date(fb.end_date) AND fb.end_date IS NOT NULL AND fb.sign_off = 0 THEN 'Overdue'
									WHEN Date(NOW()) BETWEEN Date(fb.start_date) AND Date(fb.end_date) AND fb.sign_off = 0 THEN 'In Progress'
									WHEN Date(NOW()) < Date(fb.start_date) AND fb.sign_off = 0 THEN 'Not Started'
									ELSE 'None'
								END AS fb_status
							FROM
								user_permissions up
							LEFT JOIN feedback fb ON
								up.element_id = fb.element_id
								AND fb.status = 1
							WHERE
								up.user_id = ".$current_user_id." #SET : CURRENT USER id
								AND up.workspace_id = ".$workspace_id." #SET : CURRENT workspace id
								AND up.element_id IS NOT NULL # elements only
						) AS wf #workspace feedback
						) AS wfc ON wdc.wid = wfc.wid
						LEFT JOIN
						(
						SELECT
							wv.wid,
							COUNT(IF(wv.vt_status = 'Not Started', 1, NULL)) AS vt_nst,
							COUNT(IF(wv.vt_status = 'In Progress', 1, NULL)) AS vt_prg,
							COUNT(IF(wv.vt_status = 'Overdue', 1, NULL)) AS vt_ovd,
							COUNT(IF(wv.vt_status = 'Completed', 1, NULL)) AS vt_cmp
						FROM
						(
							#get votes status counts
							SELECT
								up.workspace_id AS wid,
								up.element_id AS tid,
								CASE
									WHEN vt.is_completed = 1 THEN 'Completed'
									WHEN Date(NOW()) > Date(vt.end_date) AND vt.end_date IS NOT NULL AND vt.is_completed = 0 THEN 'Overdue'
									WHEN Date(NOW()) BETWEEN Date(vt.start_date) AND Date(vt.end_date) AND vt.is_completed = 0 THEN 'In Progress'
									WHEN Date(NOW()) < Date(vt.start_date) AND vt.is_completed = 0 THEN 'Not Started'
									ELSE 'None'
								END AS vt_status
							FROM
								user_permissions up
							LEFT JOIN votes vt ON
								up.element_id = vt.element_id
							WHERE
								up.user_id = ".$current_user_id." #SET : CURRENT USER id
								AND up.workspace_id = ".$workspace_id." #SET : CURRENT workspace id
								AND up.element_id IS NOT NULL # elements only
						) AS wv #workspace votes
						) AS wvc ON wdc.wid = wvc.wid

						LEFT JOIN
						(
						SELECT
							wel.wid,
							COUNT(wel.elid) AS elinks_count
						FROM
						(
							#get element_links counts
							SELECT
								up.workspace_id AS wid,
								up.element_id AS tid,
								el.id as elid
							FROM
								user_permissions up
							LEFT JOIN element_links el ON
								up.element_id = el.element_id
							WHERE
								up.user_id = ".$current_user_id." #SET : CURRENT USER id
								AND up.workspace_id = ".$workspace_id." #SET : CURRENT workspace id
								AND up.element_id IS NOT NULL # elements only
						) AS wel #workspace links
						) AS wwel ON wdc.wid = wwel.wid

						LEFT JOIN
						(
						SELECT
							wen.wid,
							COUNT(wen.enid) AS enotes_count
						FROM
						(
							#get element_notes counts
							SELECT
								up.workspace_id AS wid,
								up.element_id AS tid,
								en.id as enid
							FROM
								user_permissions up
							LEFT JOIN element_notes en ON
								up.element_id = en.element_id
							WHERE
								up.user_id = ".$current_user_id." #SET : CURRENT USER id
								AND up.workspace_id = ".$workspace_id." #SET : CURRENT workspace id
								AND up.element_id IS NOT NULL # elements only
						) AS wen #workspace links
						) AS wwen ON wdc.wid = wwen.wid

						LEFT JOIN
						(
						SELECT
							wed.wid,
							COUNT(wed.edid) AS edocs_count
						FROM
						(
							#get element_documents counts
							SELECT
								up.workspace_id AS wid,
								up.element_id AS tid,
								ed.id as edid
							FROM
								user_permissions up
							LEFT JOIN element_documents ed ON
								up.element_id = ed.element_id
							WHERE
								up.user_id = ".$current_user_id." #SET : CURRENT USER id
								AND up.workspace_id = ".$workspace_id." #SET : CURRENT workspace id
								AND up.element_id IS NOT NULL # elements only
						) AS wed #workspace documents
						) AS wwed ON wdc.wid = wwed.wid

						LEFT JOIN
						(
						SELECT
							wem.wid,
							COUNT(wem.emid) AS emms_count
						FROM
						(
							#get element_mindmaps counts
							SELECT
								up.workspace_id AS wid,
								up.element_id AS tid,
								em.id as emid
							FROM
								user_permissions up
							LEFT JOIN element_mindmaps em ON
								up.element_id = em.element_id
							WHERE
								up.user_id = ".$current_user_id." #SET : CURRENT USER id
								AND up.workspace_id = ".$workspace_id." #SET : CURRENT workspace id
								AND up.element_id IS NOT NULL # elements only
						) AS wem #workspace documents
						) AS wwem ON wdc.wid = wwem.wid";
		//pr($query, 1);
		return ClassRegistry::init('UserPermission')->query($query);

	}


	function getTaskCountAssetByTask($task_id = null){

			$current_user_id = $this->Session->read('Auth.User.id');
			$currentDate = date('Y-m-d');
			$query = "SELECT
							(dc_prg + dc_cmp) AS dc_tot,
							dc_prg,
							dc_cmp,
							(fb_nst + fb_prg + fb_ovd + fb_cmp) AS fb_tot,
							fb_nst,
							fb_prg,
							fb_ovd,
							fb_cmp,
							(vt_nst + vt_prg + vt_ovd + vt_cmp) AS vt_tot,
							vt_nst,
							vt_prg,
							vt_ovd,
							vt_cmp,
							elinks_count as links_tot,
							enotes_count as notes_tot,
							edocs_count as docs_tot,
							emms_count as mms_tot,
							(dc_prg + dc_cmp + fb_nst + fb_prg + fb_ovd + fb_cmp + vt_nst + vt_prg + vt_ovd + vt_cmp + elinks_count + enotes_count + edocs_count + emms_count) as total_assets

						FROM
						(
						SELECT
							wd.wid,
							COUNT(IF(wd.dc_status = 'In Progress', 1, NULL)) AS dc_prg,
							COUNT(IF(wd.dc_status = 'Completed', 1, NULL)) AS dc_cmp
						FROM
						(
							#get decision status counts
							SELECT
								up.element_id AS wid,
								#up.element_id AS tid,
								CASE
									WHEN ed.sign_off = 0 THEN 'In Progress'
									WHEN ed.sign_off = 1 THEN 'Completed'
									ELSE 'None'
								END AS dc_status
							FROM
								user_permissions up
							LEFT JOIN element_decisions ed ON
								up.element_id = ed.element_id
							WHERE
								up.user_id = ".$current_user_id." #SET : CURRENT USER id
								AND up.element_id = ".$task_id." #SET : CURRENT workspace id
								AND up.element_id IS NOT NULL # elements only
						) AS wd #workspace decisions
						) AS wdc
						LEFT JOIN
						(
						SELECT
							wf.wid,
							COUNT(IF(wf.fb_status = 'Not Started', 1, NULL)) AS fb_nst,
							COUNT(IF(wf.fb_status = 'In Progress', 1, NULL)) AS fb_prg,
							COUNT(IF(wf.fb_status = 'Overdue', 1, NULL)) AS fb_ovd,
							COUNT(IF(wf.fb_status = 'Completed', 1, NULL)) AS fb_cmp
						FROM
						(
							#get feedback status counts
							SELECT
								up.element_id AS wid,
								#up.element_id AS tid,
								CASE
									WHEN fb.sign_off = 1 THEN 'Completed'
									WHEN Date(NOW()) > Date(fb.end_date) AND fb.end_date IS NOT NULL AND fb.sign_off = 0 THEN 'Overdue'
									WHEN Date(NOW()) BETWEEN Date(fb.start_date) AND Date(fb.end_date) AND fb.sign_off = 0 THEN 'In Progress'
									WHEN Date(NOW()) < Date(fb.start_date) AND fb.sign_off = 0 THEN 'Not Started'
									ELSE 'None'
								END AS fb_status
							FROM
								user_permissions up
							LEFT JOIN feedback fb ON
								up.element_id = fb.element_id
								AND fb.status = 1
							WHERE
								up.user_id = ".$current_user_id." #SET : CURRENT USER id
								AND up.element_id = ".$task_id." #SET : CURRENT workspace id
								AND up.element_id IS NOT NULL # elements only
						) AS wf #workspace feedback
						) AS wfc ON wdc.wid = wfc.wid
						LEFT JOIN
						(
						SELECT
							wv.wid,
							COUNT(IF(wv.vt_status = 'Not Started', 1, NULL)) AS vt_nst,
							COUNT(IF(wv.vt_status = 'In Progress', 1, NULL)) AS vt_prg,
							COUNT(IF(wv.vt_status = 'Overdue', 1, NULL)) AS vt_ovd,
							COUNT(IF(wv.vt_status = 'Completed', 1, NULL)) AS vt_cmp
						FROM
						(
							#get votes status counts
							SELECT
								up.element_id AS wid,
								#up.element_id AS tid,
								CASE
									WHEN vt.is_completed = 1 THEN 'Completed'
									WHEN Date(NOW()) > Date(vt.end_date) AND vt.end_date IS NOT NULL AND vt.is_completed = 0 THEN 'Overdue'
									WHEN Date(NOW()) BETWEEN Date(vt.start_date) AND Date(vt.end_date) AND vt.is_completed = 0 THEN 'In Progress'
									WHEN Date(NOW()) < Date(vt.start_date) AND vt.is_completed = 0 THEN 'Not Started'
									ELSE 'None'
								END AS vt_status
							FROM
								user_permissions up
							LEFT JOIN votes vt ON
								up.element_id = vt.element_id
							WHERE
								up.user_id = ".$current_user_id." #SET : CURRENT USER id
								AND up.element_id = ".$task_id." #SET : CURRENT workspace id
								AND up.element_id IS NOT NULL # elements only
						) AS wv #workspace votes
						) AS wvc ON wdc.wid = wvc.wid

						LEFT JOIN
						(
						SELECT
							wel.wid,
							COUNT(wel.elid) AS elinks_count
						FROM
						(
							#get element_links counts
							SELECT
								up.element_id AS wid,
								#up.element_id AS tid,
								el.id as elid
							FROM
								user_permissions up
							LEFT JOIN element_links el ON
								up.element_id = el.element_id
							WHERE
								up.user_id = ".$current_user_id." #SET : CURRENT USER id
								AND up.element_id = ".$task_id." #SET : CURRENT workspace id
								AND up.element_id IS NOT NULL # elements only
						) AS wel #workspace links
						) AS wwel ON wdc.wid = wwel.wid

						LEFT JOIN
						(
						SELECT
							wen.wid,
							COUNT(wen.enid) AS enotes_count
						FROM
						(
							#get element_notes counts
							SELECT
								up.element_id AS wid,
								#up.element_id AS tid,
								en.id as enid
							FROM
								user_permissions up
							LEFT JOIN element_notes en ON
								up.element_id = en.element_id
							WHERE
								up.user_id = ".$current_user_id." #SET : CURRENT USER id
								AND up.element_id = ".$task_id." #SET : CURRENT workspace id
								AND up.element_id IS NOT NULL # elements only
						) AS wen #workspace links
						) AS wwen ON wdc.wid = wwen.wid

						LEFT JOIN
						(
						SELECT
							wed.wid,
							COUNT(wed.edid) AS edocs_count
						FROM
						(
							#get element_documents counts
							SELECT
								up.element_id AS wid,
								#up.element_id AS tid,
								ed.id as edid
							FROM
								user_permissions up
							LEFT JOIN element_documents ed ON
								up.element_id = ed.element_id
							WHERE
								up.user_id = ".$current_user_id." #SET : CURRENT USER id
								AND up.element_id = ".$task_id." #SET : CURRENT workspace id
								AND up.element_id IS NOT NULL # elements only
						) AS wed #workspace documents
						) AS wwed ON wdc.wid = wwed.wid

						LEFT JOIN
						(
						SELECT
							wem.wid,
							COUNT(wem.emid) AS emms_count
						FROM
						(
							#get element_mindmaps counts
							SELECT
								up.element_id AS wid,
								#up.element_id AS tid,
								em.id as emid
							FROM
								user_permissions up
							LEFT JOIN element_mindmaps em ON
								up.element_id = em.element_id
							WHERE
								up.user_id = ".$current_user_id." #SET : CURRENT USER id
								AND up.element_id = ".$task_id." #SET : CURRENT workspace id
								AND up.element_id IS NOT NULL # elements only
						) AS wem #workspace documents
						) AS wwem ON wdc.wid = wwem.wid";

		return ClassRegistry::init('UserPermission')->query($query);

	}




	function getAreaTaskAsset($workspace_id = null, $area_id = null, $task_status = null ){

		$current_user_id = $this->Session->read('Auth.User.id');

		$status_conditions = '';
		$not_spacified = '';
		$not_started = '';
		$progress = '';
		$overdue = '';
		$completed = '';
		$status_conditions_main = $status_task_type = $status_conditions_main_last = '';


		if( isset($task_status) &&  !empty($task_status)  ){

			$status_conditions_main = "AND ( ";

			foreach( $task_status as $status_list ){

				if( !empty($status_list) && $status_list == 'undefined' ){
					$not_spacified = 1 ;
				}
				if( !empty($status_list) && $status_list == 'not_started' ){
					$not_started = 1;
				}
				if( !empty($status_list) && $status_list == 'in_progress' ){
					$progress = 1;
				}
				if( !empty($status_list) && $status_list == 'overdue' ){
					$overdue = 1;
				}
				if( !empty($status_list) && $status_list == 'completed' ){
					$completed = 1;
				}

			}
			$status_conditions = '';
			if( isset($not_spacified) && $not_spacified == 1 ){
				$status_conditions .=" (Element.date_constraints=0) ";
			}
			if( isset($not_started) && $not_started == 1 ){
				if( !empty($status_conditions) ){
					$status_conditions .=" OR (Element.sign_off=0 AND DATE(NOW())<DATE(Element.start_date))  ";
				} else {
					$status_conditions .=" (Element.sign_off=0 AND DATE(NOW())<DATE(Element.start_date))  ";
				}

			}
			if( isset($progress) && $progress == 1 ){
				if( !empty($status_conditions) ){
					$status_conditions .=" OR (Element.sign_off=0 AND DATE(NOW()) BETWEEN DATE(Element.start_date) AND 	DATE(Element.end_date) and Element.date_constraints=1 )   ";
				} else {
					$status_conditions .=" (Element.sign_off=0 AND DATE(NOW()) BETWEEN DATE(Element.start_date) AND 	DATE(Element.end_date) and Element.date_constraints=1 )  ";
				}
			}
			if( isset($overdue) && $overdue == 1 ){
				if( !empty($status_conditions) ){
					$status_conditions .=" OR (Element.sign_off=0 AND DATE(NOW())>DATE(Element.end_date))  ";
				} else {
					$status_conditions .="  (Element.sign_off=0 AND DATE(NOW())>DATE(Element.end_date))  ";
				}
			}
			if( isset($completed) && $completed == 1 ){
				if( !empty($status_conditions) ){
					$status_conditions .=" OR (Element.sign_off=1) # Status=Complete ";
				} else {
					$status_conditions .=" (Element.sign_off=1) # Status=Complete ";
				}
			}



				#OR
				#(elements.sign_off=0 AND DATE(NOW())<DATE(elements.start_date)) # Status=Pending
				#OR
				#(elements.sign_off=0 AND DATE(NOW()) BETWEEN DATE(elements.start_date) AND DATE(elements.end_date)) # Status=In Progress
				#OR
				#(elements.sign_off=0 AND DATE(NOW())>DATE(elements.end_date)) # Status=Overdue
				#OR
				#(elements.sign_off=1) # Status=Complete

			$status_conditions_main_last = ")";
		}


		//echo $status_conditions;

		$query = 'SELECT
			user_permissions.role, # Uncomment for testing
			user_permissions.user_id, # Uncomment for testing
			user_permissions.project_id, # Uncomment for testing
			user_permissions.workspace_id, # Uncomment for testing
			user_permissions.area_id AS a_id,

			user_permissions.permit_read AS permit_read,
			user_permissions.permit_edit AS permit_edit,
			user_permissions.permit_delete AS permit_delete,
			user_permissions.permit_add AS permit_add,
			user_permissions.permit_copy AS permit_copy,
			user_permissions.permit_move AS permit_move,

			#areas.title AS a_title,
			#areas.tooltip_text AS a_purpose,
			user_permissions.element_id AS id,
			Element.id AS id,
			Element.title AS title,
			Element.sort_order AS sort_order,
			Element.color_code AS color_code,
			Element.description AS description,
			Element.comments AS outcome,
			Element.studio_status AS studio_status,
			(CASE
				WHEN (Element.sign_off=1) THEN "Completed" # Element is signed off
				WHEN (Element.date_constraints=0) THEN "Not Set" # No start or end dates set
				WHEN (DATE(NOW()) BETWEEN DATE(Element.start_date) AND DATE(Element.end_date)) THEN "In Progress" # Currently between start and end dates
				WHEN (DATE(NOW())<DATE(Element.start_date)) THEN "Not Started" # Element start date is in the future
				WHEN (DATE(Element.end_date)<DATE(NOW())) THEN "Overdue" # End date has passed
				ELSE "Unknown"
			END) AS e_status,

			(CASE
				WHEN (Element.sign_off=1) THEN "CMP" # Element is signed off
				WHEN (Element.date_constraints=0) THEN "NON" # No start or end dates set
				WHEN (DATE(NOW()) BETWEEN DATE(Element.start_date) AND DATE(Element.end_date)) THEN "PRG" # Currently between start and end dates
				WHEN (DATE(NOW())<DATE(Element.start_date)) THEN "PND" # Element start date is in the future
				WHEN (DATE(Element.end_date)<DATE(NOW())) THEN "OVD" # End date has passed
				ELSE "NON"
			END) AS e_status_short_term,

			COUNT(distinct(element_links.id)) AS links,
			COUNT(distinct(element_notes.id)) AS notes,
			COUNT(distinct(element_documents.id)) AS docs,
			COUNT(distinct(element_mindmaps.id)) AS e_mind_maps_count,
			(CASE
			WHEN (COUNT(element_decision_details.stage_status)) = 6 THEN "CMP"
			ELSE "NON"
			END) as e_decisions_counts,
			count(distinct(votes.id)) AS e_votes_in_progress_count,
			count(distinct(feedback.id)) as e_feedbacks_in_progress_count



		FROM
			user_permissions
		INNER JOIN
			areas
			ON areas.id=user_permissions.area_id
		INNER JOIN
			elements Element
			ON Element.id=user_permissions.element_id

		LEFT JOIN element_types
			ON element_types.element_id=user_permissions.element_id

		LEFT JOIN
			element_links
			ON element_links.element_id=user_permissions.element_id
			AND element_links.status=1 # Assumed to mean active

		LEFT JOIN element_notes
			ON element_notes.element_id=user_permissions.element_id
			AND element_notes.status=1 # Assumed to mean active

		LEFT JOIN element_documents
			ON element_documents.element_id=user_permissions.element_id
			AND element_documents.status=1 # Assumed to mean active

		LEFT JOIN
			element_mindmaps
			ON element_mindmaps.element_id=user_permissions.element_id
			AND element_mindmaps.status=1 # Assumed to mean active

		LEFT JOIN
			element_decisions
			ON element_decisions.element_id=user_permissions.element_id
			#AND element_decisions.sign_off != 1
			# There is no status column
		LEFT JOIN
			element_decision_details
			ON element_decision_details.decision_id=element_decisions.id
			AND element_decision_details.stage_status =1
			# There is no status column
		LEFT JOIN
				feedback
				ON feedback.element_id=user_permissions.element_id
				AND feedback.status=1 # Assumed to mean active
				AND ((DATE(NOW()) BETWEEN DATE(feedback.start_date) AND DATE(feedback.end_date)) && (feedback.sign_off =0))
				AND feedback.sign_off=0 # In Progress
		LEFT JOIN
				votes
				ON votes.element_id=user_permissions.element_id
				AND ((DATE(NOW()) BETWEEN DATE(votes.start_date) AND DATE(votes.end_date)) && (votes.is_completed =0))
				# There is no status column
				AND votes.is_completed=0

		WHERE
			user_permissions.user_id='.$current_user_id.' # SET: CURRENT USER ID
			AND user_permissions.workspace_id='.$workspace_id.' # SET: CURRENT WORKSPACE ID
			AND user_permissions.area_id='.$area_id.' # SET: CURRENT AREA ID
			AND user_permissions.area_id IS NOT NULL # Elements only, workspace row is not included in result
			'.$status_conditions_main.'
			'.$status_conditions.'
			'.$status_conditions_main_last.'
			'.$status_task_type.'

		GROUP BY
			user_permissions.element_id
		ORDER BY
			user_permissions.area_id, Element.sort_order';



		return ClassRegistry::init('UserPermission')->query($query);

	}

	function getAreaTask($workspace_id = null, $area_id = null, $task_status = null ){

		$current_user_id = $this->Session->read('Auth.User.id');

		$status_conditions = '';
		$not_spacified = '';
		$not_started = '';
		$progress = '';
		$overdue = '';
		$completed = '';
		$status_conditions_main = $status_task_type = $status_conditions_main_last = '';

		if( isset($task_status['project_task_type']) &&  !empty($task_status['project_task_type'])  ){
			$extratypecondition = '';
			if( isset($task_status['generalflag']) && $task_status['generalflag'] == 1 ){
				$extratypecondition = "  or ( element_types.element_id is null )  ";
			}

			$tasktype = implode(",",$task_status['project_task_type']);
			$task_type_qry = " AND ( element_types.type_id IN ($tasktype) $extratypecondition )";

			//get general's id
			/*$query = "SELECT id FROM project_types WHERE title = 'General'";
			$task_types = ClassRegistry::init('UserPermission')->query($query);
			if( isset($task_types) && !empty($task_types)  ){
				$general_id = $task_types[0]['project_types']['id'];
				if(in_array($general_id, $task_status['project_task_type'])){
					$task_type_qry = " AND ( element_types.type_id IN ($tasktype) $extratypecondition OR ( element_types.element_id is null ) )";
				}
			}*/



			$general_flag = false;
			$g_query = "SELECT id, title FROM project_element_types WHERE id IN($tasktype)";
			$task_types1 = ClassRegistry::init('UserPermission')->query($g_query);
			if( isset($task_types1) && !empty($task_types1) ){
				foreach ($task_types1 as $key => $value) {
					if($value['project_element_types']['title'] == 'General'){
						$general_flag = true;
					}
				}
			}
			if($general_flag) {
				$task_type_qry = " AND ( element_types.type_id IN ($tasktype) $extratypecondition OR ( element_types.element_id is null ) )";
			}
			$status_task_type .= $task_type_qry;

		}


		if( isset($task_status['statuses']) &&  !empty($task_status['statuses'])  ){

			$status_conditions_main = "AND ( ";

			foreach( $task_status['statuses'] as $status_list ){

				if( !empty($status_list) && $status_list == 'not_spacified' ){
					$not_spacified = 1 ;
				}
				if( !empty($status_list) && $status_list == 'not_started' ){
					$not_started = 1;
				}
				if( !empty($status_list) && $status_list == 'progress' ){
					$progress = 1;
				}
				if( !empty($status_list) && $status_list == 'overdue' ){
					$overdue = 1;
				}
				if( !empty($status_list) && $status_list == 'completed' ){
					$completed = 1;
				}

			}
			$status_conditions = '';
			if( isset($not_spacified) && $not_spacified == 1 ){
				$status_conditions .=" (elements.date_constraints=0) ";
			}
			if( isset($not_started) && $not_started == 1 ){
				if( !empty($status_conditions) ){
					$status_conditions .=" OR (elements.sign_off=0 AND DATE(NOW())<DATE(elements.start_date))  ";
				} else {
					$status_conditions .=" (elements.sign_off=0 AND DATE(NOW())<DATE(elements.start_date))  ";
				}

			}
			if( isset($progress) && $progress == 1 ){
				if( !empty($status_conditions) ){
					$status_conditions .=" OR (elements.sign_off=0 AND DATE(NOW()) BETWEEN DATE(elements.start_date) AND 	DATE(elements.end_date) and elements.date_constraints=1 )   ";
				} else {
					$status_conditions .=" (elements.sign_off=0 AND DATE(NOW()) BETWEEN DATE(elements.start_date) AND 	DATE(elements.end_date) and elements.date_constraints=1 )  ";
				}
			}
			if( isset($overdue) && $overdue == 1 ){
				if( !empty($status_conditions) ){
					$status_conditions .=" OR (elements.sign_off=0 AND DATE(NOW())>DATE(elements.end_date))  ";
				} else {
					$status_conditions .="  (elements.sign_off=0 AND DATE(NOW())>DATE(elements.end_date))  ";
				}
			}
			if( isset($completed) && $completed == 1 ){
				if( !empty($status_conditions) ){
					$status_conditions .=" OR (elements.sign_off=1) # Status=Complete ";
				} else {
					$status_conditions .=" (elements.sign_off=1) # Status=Complete ";
				}
			}



				#OR
				#(elements.sign_off=0 AND DATE(NOW())<DATE(elements.start_date)) # Status=Pending
				#OR
				#(elements.sign_off=0 AND DATE(NOW()) BETWEEN DATE(elements.start_date) AND DATE(elements.end_date)) # Status=In Progress
				#OR
				#(elements.sign_off=0 AND DATE(NOW())>DATE(elements.end_date)) # Status=Overdue
				#OR
				#(elements.sign_off=1) # Status=Complete

			$status_conditions_main_last = ")";
		}

		$assigned_where = '';
		if( isset($task_status['assigned_user']) &&  !empty($task_status['assigned_user'])  ){
			$assigned_users = implode(',', $task_status['assigned_user']);
			$assigned_where = " AND assigned.assigned_to IN($assigned_users)";
		}


		//echo $status_conditions;

		$query = 'SELECT
			user_permissions.role, # Uncomment for testing
			user_permissions.user_id, # Uncomment for testing
			user_permissions.project_id, # Uncomment for testing
			user_permissions.workspace_id, # Uncomment for testing
			user_permissions.area_id AS a_id,
			workspaces.sign_off as wsp_sign_off,

			user_permissions.permit_read AS p_read,
			user_permissions.permit_edit AS p_edit,
			user_permissions.permit_delete AS p_delete,
			user_permissions.permit_add AS p_add,
			user_permissions.permit_copy AS p_copy,
			user_permissions.permit_move AS p_move,

			areas.title AS a_title,
			areas.tooltip_text AS a_purpose,
			user_permissions.element_id AS e_id,
			elements.title AS e_title,
			elements.sort_order AS e_sort_order,
			elements.color_code AS e_color_code,
			elements.description AS e_description,
			elements.comments AS e_outcome,
			(CASE
				WHEN (elements.sign_off=1) THEN "Complete" # Element is signed off
				WHEN (elements.date_constraints=0) THEN "None" # No start or end dates set
				WHEN (DATE(NOW()) BETWEEN DATE(elements.start_date) AND DATE(elements.end_date)) THEN "In Progress" # Currently between start and end dates
				WHEN (DATE(NOW())<DATE(elements.start_date)) THEN "Pending" # Element start date is in the future
				WHEN (DATE(elements.end_date)<DATE(NOW())) THEN "Overdue" # End date has passed
				ELSE "Unknown"
			END) AS e_status,

			(CASE
				WHEN (elements.sign_off=1) THEN "CMP" # Element is signed off
				WHEN (elements.date_constraints=0) THEN "NON" # No start or end dates set
				WHEN (DATE(NOW()) BETWEEN DATE(elements.start_date) AND DATE(elements.end_date)) THEN "PRG" # Currently between start and end dates
				WHEN (DATE(NOW())<DATE(elements.start_date)) THEN "PND" # Element start date is in the future
				WHEN (DATE(elements.end_date)<DATE(NOW())) THEN "OVD" # End date has passed
				ELSE "NON"
			END) AS e_status_short_term,

			COUNT(distinct(element_links.id)) AS e_links_count,
			COUNT(distinct(element_notes.id)) AS e_notes_count,
			COUNT(distinct(element_documents.id)) AS e_documents_count,
			COUNT(distinct(element_mindmaps.id)) AS e_mind_maps_count,
			#(CASE
			#WHEN (COUNT(element_decision_details.stage_status)) = 6 THEN "CMP"
			#ELSE "NON"
			#END) as e_decisions_counts,

			(CASE
				WHEN (element_decisions.sign_off=0 or element_decisions.sign_off IS NULL and element_decisions.id IS NOT NULL) THEN "In Progress" # Started but not signed off
				WHEN (element_decisions.sign_off=1) THEN "Complete" # Started and signed off
				ELSE "Not Started" # No decision has been started
			END) AS e_decision_status,
			(CASE
				WHEN (element_decisions.sign_off=0 or element_decisions.sign_off IS NULL and element_decisions.id IS NOT NULL) THEN "PRG" # Started but not signed off
				WHEN (element_decisions.sign_off=1) THEN "CMP" # Started and signed off
				ELSE "NON" # No decision has been started
			END) AS e_decision_short_term,

			count(distinct(votes.id)) AS e_votes_in_progress_count,
			count(distinct(feedback.id)) as e_feedbacks_in_progress_count,
			COUNT(distinct(rm_elements.id)) AS e_risks_count

		FROM
			user_permissions
		INNER JOIN
			areas
			ON areas.id=user_permissions.area_id
		INNER JOIN
			elements
			ON elements.id=user_permissions.element_id

		LEFT JOIN
			workspaces
			ON workspaces.id=user_permissions.workspace_id

		LEFT JOIN element_types
			ON element_types.element_id=user_permissions.element_id

		LEFT JOIN
			element_links
			ON element_links.element_id=user_permissions.element_id
			AND element_links.status=1 # Assumed to mean active

		LEFT JOIN element_notes
			ON element_notes.element_id=user_permissions.element_id
			AND element_notes.status=1 # Assumed to mean active

		LEFT JOIN element_documents
			ON element_documents.element_id=user_permissions.element_id
			AND element_documents.status=1 # Assumed to mean active

		LEFT JOIN
			element_mindmaps
			ON element_mindmaps.element_id=user_permissions.element_id
			AND element_mindmaps.status=1 # Assumed to mean active

		LEFT JOIN
			element_decisions
			ON element_decisions.element_id=user_permissions.element_id
			#AND element_decisions.sign_off != 1
			# There is no status column
		LEFT JOIN
			element_decision_details
			ON element_decision_details.decision_id=element_decisions.id
			AND element_decision_details.stage_status =1
			# There is no status column
		LEFT JOIN
				feedback
				ON feedback.element_id=user_permissions.element_id
				AND feedback.status=1 # Assumed to mean active
				AND ((DATE(NOW()) BETWEEN DATE(feedback.start_date) AND DATE(feedback.end_date)) && (feedback.sign_off =0))
				AND feedback.sign_off=0 # In Progress
		LEFT JOIN
				votes
				ON votes.element_id=user_permissions.element_id
				AND ((DATE(NOW()) BETWEEN DATE(votes.start_date) AND DATE(votes.end_date)) && (votes.is_completed =0))
				# There is no status column
				AND votes.is_completed=0
		LEFT JOIN
			rm_elements
			ON rm_elements.element_id=user_permissions.element_id
			# A status column exists but is not used for active flag

		# ELEMENT ASSIGNMENT
		LEFT JOIN
		(
			SELECT
				up.element_id AS eid,
				ea.assigned_to
			FROM
				user_permissions up
			LEFT JOIN
				element_assignments ea
				ON ea.element_id = up.element_id
			LEFT JOIN
				user_details ud
				ON ud.user_id = ea.assigned_to
			WHERE
		        up.workspace_id = '.$workspace_id.' AND
		        up.element_id IS NOT NULL
	        GROUP BY up.element_id
		) AS assigned
		ON assigned.eid = user_permissions.element_id

		WHERE
			user_permissions.user_id='.$current_user_id.' # SET: CURRENT USER ID
			AND user_permissions.workspace_id='.$workspace_id.' # SET: CURRENT WORKSPACE ID
			#AND user_permissions.area_id='.$area_id.' # SET: CURRENT AREA ID
			AND user_permissions.area_id IS NOT NULL # Elements only, workspace row is not included in result
			'.$status_conditions_main.'
			'.$status_conditions.'
			'.$status_conditions_main_last.'
			'.$status_task_type.'
			#AND (
				#(elements.date_constraints=0) # Status=None
				#OR
				#(elements.sign_off=0 AND DATE(NOW())<DATE(elements.start_date)) # Status=Pending
				#OR
				#(elements.sign_off=0 AND DATE(NOW()) BETWEEN DATE(elements.start_date) AND DATE(elements.end_date)) # Status=In Progress
				#OR
				#(elements.sign_off=0 AND DATE(NOW())>DATE(elements.end_date)) # Status=Overdue
				#OR
				#(elements.sign_off=1) # Status=Complete
			#)
			'.$assigned_where.'
		GROUP BY
			user_permissions.element_id
		ORDER BY
			user_permissions.area_id, elements.sort_order';



		return ClassRegistry::init('UserPermission')->query($query);

	}

	function getMoveCopyArea($workspace_id = null){
		$current_user_id = $this->Session->read('Auth.User.id');
		$query = "SELECT user_permissions.project_id as project_id,user_permissions.workspace_id,user_permissions.user_id,user_permissions.role,projects.id,projects.title as project_title, workspaces.id as workspace_id,workspaces.title as wsp_title,areas.id as area_id,areas.title as area_title

		 FROM `user_permissions` inner join projects on  projects.id = user_permissions.project_id
		inner join workspaces on workspaces.id = user_permissions.workspace_id and (DATE(NOW()) <= DATE(workspaces.end_date)) and workspaces.sign_off = 0 and workspaces.studio_status != 1  inner join areas on user_permissions.area_id = areas.id

		 where user_id = $current_user_id and user_permissions.workspace_id!=$workspace_id and  area_id !='' and element_id IS NULL and projects.sign_off !=1 and (DATE(NOW()) <=  DATE(projects.end_date))   GROUP by areas.id";

		 //(DATE(NOW()) BETWEEN DATE(projects.start_date) AND DATE(projects.end_date))
		 //echo $query;
		 return ClassRegistry::init('UserPermission')->query($query);

	}

	function getWspPermission($workspace_id = null){
		$current_user_id = $this->Session->read('Auth.User.id');

		$query = "SELECT

					user_permissions.project_id as project_id,
					user_permissions.workspace_id,
					user_permissions.user_id,
					user_permissions.role,
					workspaces.title as wsp_title,
					workspaces.sign_off as wsp_sign_off,

					workspaces.start_date as wsp_start_date,
					workspaces.end_date as wsp_end_date,

					user_permissions.permit_read AS p_read,
					user_permissions.permit_edit AS p_edit,
					user_permissions.permit_delete AS p_delete,
					user_permissions.permit_add AS p_task_add,
					user_permissions.permit_copy AS p_copy,
					user_permissions.permit_move AS p_move


		 FROM `user_permissions` inner join workspaces on workspaces.id = user_permissions.workspace_id

		 where user_id = $current_user_id and user_permissions.workspace_id=$workspace_id and area_id IS NULL ";


		 return ClassRegistry::init('UserPermission')->query($query);

	}

	function projectPeople($project_id = null){

		if(is_array($project_id)){
			$params = "`project_id` IN (".implode(',',$project_id).") AND ";
		}
		else if(isset($project_id) && !empty($project_id)){
			$params = "`project_id` = $project_id AND ";
		}
		else{
			$params = " ";
		}
		$query = "SELECT
			CONCAT_WS(' ',user_details.first_name , user_details.last_name) as fullname, user_details.user_id as user_id, user_details.profile_pic as profile_pic,user_details.org_name as org_name,user_details.job_role as job_role,user_details.job_title as job_title,
			user_permissions.user_id,user_permissions.project_id,user_permissions.role,users.email

			FROM `user_permissions`
			inner join user_details on user_details.user_id = user_permissions.user_id
			LEFT JOIN users on users.id = user_permissions.user_id
			WHERE $params workspace_id is null order by role ASC";

		return ClassRegistry::init('UserPermission')->query($query);
	}


	function todo_People_all($project_id = null,$user_id= null){

		$users = [];
		if (empty($project_id)) {

			$usersAll = ClassRegistry::init('User')->find("all", array(
				"conditions" => array(
					"NOT" => array("User.id" => $this->Session->read("Auth.User.id")),
					"User.role_id" => 2,
					"User.status" => 1,
				),
				"fields" => array("User.id"),
			)
			);
			//	pr($usersAll,1);
			//$this->User->Behaviors->load('Containable');
			if (isset($usersAll) && !empty($usersAll)) {
				foreach ($usersAll as $key => $val) {
					ClassRegistry::init('User')->Behaviors->load('Containable');
					$userDetail = ClassRegistry::init('User')->find('first', ['conditions' => ['User.id' => $val['User']['id']], 'contain' => 'UserDetail']);
					if (isset($userDetail) && !empty($userDetail)) {
						$users[] = array('id' => $val['User']['id'], 'name' => (isset($userDetail['UserDetail']['first_name']) && !empty($userDetail['UserDetail']['first_name'])) ? $userDetail['UserDetail']['first_name'] . ' ' . $userDetail['UserDetail']['last_name'] : $userDetail['User']['email']);

					}
				}
			}

			return $users;
		} else{



		$query = "SELECT
			CONCAT_WS(' ',user_details.first_name , user_details.last_name) as name,CONCAT_WS(' ',user_details.user_id) as id,CONCAT(' ',DATE_FORMAT(projects.start_date,'%d %b %Y')) as start_date,CONCAT_WS(' ',DATE_FORMAT(projects.end_date,'%d %b %Y')) as end_date ,CONCAT_WS(' ',user_permissions.project_id) as project_id

			FROM `user_permissions`
			inner join user_details on user_details.user_id = user_permissions.user_id
			INNER JOIN projects	ON projects.id=user_permissions.project_id
			LEFT JOIN users on users.id = user_permissions.user_id
			WHERE `project_id` = $project_id and workspace_id is null and user_permissions.user_id != $user_id order by role ASC";


		return ClassRegistry::init('UserPermission')->query($query);

		}

	}

	function todo_project_date($project_id = null,$user_id= null){

		$users = [];
		if (empty($project_id)) {

			$usersAll = ClassRegistry::init('User')->find("all", array(
				"conditions" => array(
					"NOT" => array("User.id" => $this->Session->read("Auth.User.id")),
					"User.role_id" => 2,
					"User.status" => 1,
				),
				"fields" => array("User.id"),
			)
			);
			//	pr($usersAll,1);
			//$this->User->Behaviors->load('Containable');
			if (isset($usersAll) && !empty($usersAll)) {
				foreach ($usersAll as $key => $val) {
					ClassRegistry::init('User')->Behaviors->load('Containable');
					$userDetail = ClassRegistry::init('User')->find('first', ['conditions' => ['User.id' => $val['User']['id']], 'contain' => 'UserDetail']);
					if (isset($userDetail) && !empty($userDetail)) {
						$users[] = array('id' => $val['User']['id'], 'name' => (isset($userDetail['UserDetail']['first_name']) && !empty($userDetail['UserDetail']['first_name'])) ? $userDetail['UserDetail']['first_name'] . ' ' . $userDetail['UserDetail']['last_name'] : $userDetail['User']['email']);

					}
				}
			}

			return $users;
		} else{



		$query = "SELECT
			 CONCAT_WS(' ',user_details.user_id) as id,CONCAT(' ',DATE_FORMAT(projects.start_date,'%d %b %Y')) as start_date,CONCAT_WS(' ',DATE_FORMAT(projects.end_date,'%d %b %Y')) as end_date ,CONCAT_WS(' ',user_permissions.project_id) as project_id

			FROM `user_permissions`
			inner join user_details on user_details.user_id = user_permissions.user_id
			INNER JOIN projects	ON projects.id=user_permissions.project_id
			LEFT JOIN users on users.id = user_permissions.user_id
			WHERE `project_id` = $project_id and workspace_id is null  order by role ASC";

		return ClassRegistry::init('UserPermission')->query($query);

		}

	}


	// Workspace people
	function workspacePeople($workspace_id = null){
		$query = "SELECT
			CONCAT_WS(' ',user_details.first_name , user_details.last_name) as fullname, user_details.user_id as user_id, user_details.profile_pic as profile_pic,user_details.org_name as org_name,user_details.job_role as job_role,user_details.organization_id,
			user_permissions.user_id,user_permissions.project_id,user_permissions.role,users.email, organizations.name

			FROM `user_permissions`
			inner join user_details on user_details.user_id = user_permissions.user_id
			LEFT JOIN users on users.id = user_permissions.user_id
			left join organizations on user_details.organization_id = organizations.id
			WHERE `workspace_id` = $workspace_id and area_id is null ORDER BY FIELD(role, 'Creator','Owner','Group Owner','Sharer','Group Sharer')";

		return ClassRegistry::init('UserPermission')->query($query);
	}
	// Workspace people count
	function workspacePeopleCount($workspace_id = null){
		$query = "SELECT COUNT(*) AS wspCount

			FROM `user_permissions`
			inner join user_details on user_details.user_id = user_permissions.user_id
			LEFT JOIN users on users.id = user_permissions.user_id
			WHERE  user_permissions.workspace_id = $workspace_id and area_id is null order by role ASC";

		$total = ClassRegistry::init('UserPermission')->query($query);
		return ( isset($total[0][0]['wspCount']) && !empty($total[0][0]['wspCount']) ) ? $total[0][0]['wspCount'] : 0;
	}


	/********************************************************************
	********************** End Custom Query for WSP page ****************
	*********************************************************************/


	/*******************************************************************
	******************** Start User Profile Behaviors Section **********
	*******************************************************************/
	//Current Project(s)
	function participant_current_project($current_user_id = null){

		//$current_user_id = $this->Session->read('Auth.User.id');

		$query = "SELECT COUNT(projects.id) as total

					FROM user_permissions

					INNER JOIN
						projects
						ON projects.id=user_permissions.project_id
						AND DATE(NOW()) BETWEEN DATE(projects.start_date) AND DATE(projects.end_date)
						AND projects.sign_off=0

					WHERE user_permissions.user_id = $current_user_id AND user_permissions.workspace_id IS NULL";

		$projectCount =  ClassRegistry::init('UserPermission')->query($query);

		return ( !empty($projectCount[0][0]['total']) && $projectCount[0][0]['total'] > 0 ) ? $projectCount[0][0]['total'] : 0;


	}
	//Past Project(s)
	function participant_past_project($current_user_id = null){

		// $current_user_id = $this->Session->read('Auth.User.id');

		$query = "SELECT
					count(user_permissions.project_id) as total

					FROM user_permissions

					INNER JOIN
						projects
						ON projects.id=user_permissions.project_id
						AND projects.sign_off=1

						WHERE user_permissions.user_id = $current_user_id AND user_permissions.workspace_id IS NULL
				";

		$projectCount =  ClassRegistry::init('UserPermission')->query($query);
		return ( !empty($projectCount[0][0]['total']) && $projectCount[0][0]['total'] > 0 ) ? $projectCount[0][0]['total'] : 0;
	}
	//Current Shared Task(s)
	function participant_current_task($current_user_id = null){

		// $current_user_id = $this->Session->read('Auth.User.id');

		$query = "SELECT COUNT(elements.id) as total

					FROM user_permissions

					INNER JOIN
						elements
						ON elements.id=user_permissions.element_id
						AND DATE(NOW()) BETWEEN DATE(elements.start_date) AND DATE(elements.end_date)
						AND elements.sign_off=0
						#AND elements.studio_status=0

					WHERE user_permissions.user_id = $current_user_id AND user_permissions.role IN ('Group Sharer','Sharer') ";

		$elementCount =  ClassRegistry::init('UserPermission')->query($query);
		return ( !empty($elementCount[0][0]['total']) && $elementCount[0][0]['total'] > 0 ) ? $elementCount[0][0]['total'] : 0;
	}
	//Past Shared Task(s)
	function participant_past_task($current_user_id = null){

		//$current_user_id = $this->Session->read('Auth.User.id');

		$query = "SELECT COUNT(elements.id) as total

					FROM user_permissions

					INNER JOIN
						elements
						ON elements.id=user_permissions.element_id
						AND elements.sign_off=1
						#AND elements.studio_status=0

					WHERE user_permissions.user_id = $current_user_id AND user_permissions.role IN ('Group Sharer','Sharer') ";

		$elementCount =  ClassRegistry::init('UserPermission')->query($query);
		return ( !empty($elementCount[0][0]['total']) && $elementCount[0][0]['total'] > 0 ) ? $elementCount[0][0]['total'] : 0;
	}

	//Leadership:Project(s)
	function leadership_projects($current_user_id = null){

		//$current_user_id = $this->Session->read('Auth.User.id');

		$query = "SELECT COUNT(projects.id) as total

					FROM user_permissions

					INNER JOIN
						projects
						ON projects.id=user_permissions.project_id
						AND ( (DATE(NOW()) BETWEEN DATE(projects.start_date) AND DATE(projects.end_date))
						OR projects.sign_off=1 )

					WHERE user_permissions.user_id = $current_user_id AND user_permissions.role IN ('Creator','Owner','Group Owner')  AND user_permissions.workspace_id IS NULL";

		$projectCount =  ClassRegistry::init('UserPermission')->query($query);

		return ( !empty($projectCount[0][0]['total']) && $projectCount[0][0]['total'] > 0 ) ? $projectCount[0][0]['total'] : 'N/A';

	}

	//Leadership:Task(s)
	function leadership_task($current_user_id = null){

		//$current_user_id = $this->Session->read('Auth.User.id');

		$query = "SELECT COUNT(elements.id) as total

					FROM user_permissions

					INNER JOIN
						elements
						ON elements.id=user_permissions.element_id
						AND ( (DATE(NOW()) BETWEEN DATE(elements.start_date) AND DATE(elements.end_date))
						OR elements.sign_off=1 )

					INNER JOIN
						element_assignments
						ON	element_assignments.element_id=user_permissions.element_id
						AND element_assignments.reaction !=3
						AND element_assignments.assigned_to = $current_user_id

					WHERE user_permissions.user_id = $current_user_id AND user_permissions.role IN ('Group Sharer','Sharer') ";

		$elementCount =  ClassRegistry::init('UserPermission')->query($query);
		return ( !empty($elementCount[0][0]['total']) && $elementCount[0][0]['total'] > 0 ) ? $elementCount[0][0]['total'] : 'N/A';
	}

	//Leadership:Risk(s)
	function leadership_risks($current_user_id = null){

		//$current_user_id = $this->Session->read('Auth.User.id');

		$query = "SELECT COUNT(DISTINCT(rm_leaders.rm_detail_id)) as total

					FROM user_permissions

					INNER JOIN
						rm_leaders
						ON	user_permissions.user_id=rm_leaders.user_id
						AND rm_leaders.user_id = $current_user_id

					WHERE user_permissions.user_id = $current_user_id "	;


		$riskCount =  ClassRegistry::init('UserPermission')->query($query);
		return ( !empty($riskCount[0][0]['total']) && $riskCount[0][0]['total'] > 0 ) ? $riskCount[0][0]['total'] : 'N/A';
	}
	//Engagement: Social Board Nudge(s)
	function leadership_engagement($current_user_id = null){
		//$current_user_id = $this->Session->read('Auth.User.id');


		$query = "SELECT COUNT(DISTINCT(project_boards.project_id)) as total

					FROM user_permissions

					INNER JOIN
						project_boards
						ON	project_boards.project_id=user_permissions.project_id
						AND project_boards.sender = $current_user_id
						AND project_boards.project_status = 1

					WHERE user_permissions.user_id = $current_user_id AND user_permissions.workspace_id IS NULL ";


		$boardCount =  ClassRegistry::init('UserPermission')->query($query);
		return ( !empty($boardCount[0][0]['total']) && $boardCount[0][0]['total'] > 0 ) ? $boardCount[0][0]['total'] : 'N/A';

	}
	//Social Networking: Task(s) Shared via Propagation
	function shared_propagation(){

		$current_user_id = $this->Session->read('Auth.User.id');
		$query = "SELECT count(element_id) as total FROM user_permissions WHERE shared_by_user_id = $current_user_id and project_id in (SELECT project_id FROM `user_permissions` WHERE `role` LIKE 'Sharer' AND `user_id` = $current_user_id and permit_propagate = 1 and permit_propagate_read = 1 ) and element_id IS NOT NULL";

		$propagationCount =  ClassRegistry::init('UserPermission')->query($query);
		return ( !empty($propagationCount[0][0]['total']) && $propagationCount[0][0]['total'] > 0 ) ? $propagationCount[0][0]['total'] : '0';
	}

	function propagated_projects_UP(){

		$current_user_id = $this->Session->read('Auth.User.id');
		$query = "SELECT projects.id as id, projects.title as title   FROM user_permissions
						INNER JOIN
						projects
						ON	projects.id=user_permissions.project_id
		WHERE shared_by_user_id = $current_user_id and  `workspace_id` IS NULL and  project_id in (SELECT project_id FROM `user_permissions` WHERE (`role` LIKE 'Sharer' or `role` LIKE 'Owner') AND `user_id` = $current_user_id and permit_propagate = 1 and permit_propagate_read = 1 and workspace_id IS NULL ) group by project_id  ";

		$propagationCount =  ClassRegistry::init('UserPermission')->query($query);

		return $propagationCount;
	}


	function created_projects_UP(){

		$current_user_id = $this->Session->read('Auth.User.id');
		$query = "SELECT projects.id as id, projects.title as title   FROM user_permissions
						INNER JOIN
						projects
						ON	projects.id=user_permissions.project_id
		WHERE user_id = $current_user_id and  `workspace_id` IS NULL and  role = 'Creator' group by project_id ";

		$propagationCount =  ClassRegistry::init('UserPermission')->query($query);

		return $propagationCount;
	}

	function received_projects_UP(){

		$current_user_id = $this->Session->read('Auth.User.id');
		$query = "SELECT projects.id as id, projects.title as title   FROM user_permissions
						INNER JOIN
						projects
						ON	projects.id=user_permissions.project_id
		WHERE user_id = $current_user_id and   `workspace_id` IS NULL and  role != 'Creator' group by project_id ";

		$propagationCount =  ClassRegistry::init('UserPermission')->query($query);

		return $propagationCount;
	}

    function shared_projects_UP(){

		$current_user_id = $this->Session->read('Auth.User.id');
		$query = "SELECT projects.id as id, projects.title as title   FROM user_permissions
						INNER JOIN
						projects
						ON	projects.id=user_permissions.project_id
		WHERE shared_by_user_id = $current_user_id and  `workspace_id` IS NULL and  project_id in (SELECT project_id FROM `user_permissions` WHERE `role` ='Creator' AND `user_id` = $current_user_id and permit_propagate = 1 and permit_propagate_read = 1 and workspace_id IS NULL ) group by project_id";

		$propagationCount =  ClassRegistry::init('UserPermission')->query($query);

		return $propagationCount;
	}



	function allocated_reward($current_user_id = null){

		//$current_user_id = $this->Session->read('Auth.User.id');

		$query ="SELECT  reward_assignments.allocated_rewards,reward_assignments.given_by, user_permissions.project_id, user_permissions.user_id
					FROM reward_assignments
				LEFT JOIN user_permissions
					ON (reward_assignments.user_id=user_permissions.user_id AND reward_assignments.project_id=user_permissions.project_id)
				WHERE
					reward_assignments.user_id = $current_user_id
					AND user_permissions.workspace_id IS NULL";

		$project_rewards =  ClassRegistry::init('UserPermission')->query($query);

		$query_reward = "SELECT sum(reward_user_accelerations.accelerated_amount) as acceleratorAmount
							FROM reward_user_accelerations
						INNER JOIN user_permissions
							ON (reward_user_accelerations.project_id=user_permissions.project_id AND reward_user_accelerations.user_id=user_permissions.user_id)
						WHERE user_permissions.user_id = $current_user_id AND user_permissions.workspace_id IS NULL";


		$accelerated_points =  ClassRegistry::init('UserPermission')->query($query_reward);

		$total_amount = 0;
		if(isset($project_rewards) && !empty($project_rewards) ) {
			$by_me = 0;
			$by_others = 0;
			 foreach ($project_rewards as $key => $value) {

				$data = $value['reward_assignments'];
				$amount = $data['allocated_rewards'];
				if($data['given_by'] == $current_user_id) {
					$by_me += $amount;
				}
				else{
					$by_others += $amount;
				}
			}

			$by_acclerate = 0;
			if (isset($accelerated_points) && !empty($accelerated_points[0][0]['acceleratorAmount'])) {
					$by_acclerate += $accelerated_points[0][0]['acceleratorAmount'];
			}

			$by_me = (isset($by_me) && !empty($by_me)) ? $by_me : '0';
			$by_others = (isset($by_others) && !empty($by_others)) ? $by_others : '0';
			return $total_amount = $by_me + $by_others + $by_acclerate;
		}

	}

	public function userOwnTemplate($user_id = null){
		$datas = ClassRegistry::init('TemplateRelation')->find('count', array('conditions' => array('TemplateRelation.user_id' => $user_id)));
		if( isset($datas) && !empty($datas) ){
			return $datas;
		} else {
			return 'N/A';
		}
	}

	public function userRespondVote($user_id = null){
		$datas = ClassRegistry::init('VoteResult')->find('count', array('conditions' => array('VoteResult.user_id' => $user_id)));
		if( isset($datas) && !empty($datas) ){
			return $datas;
		} else {
			return 'N/A';
		}
	}

	// user given feedback count
	public function feedbackGivenCount($give_to_id = null) {

		$user_id = $this->Session->read('Auth.User.id');

		$datas = ClassRegistry::init('FeedbackRating')->find('count', array('conditions' => array('FeedbackRating.given_by_id' => $give_to_id)));
		if( isset($datas) && !empty($datas) ){
			return $datas;
		} else {
			return 'N/A';
		}
	}

	/*******************************************************************
	******************** End User Profile Behaviors Section **********
	*******************************************************************/


	public function updatevalue($id = null) {
		$data =  ClassRegistry::init('User')->find('first', array('conditions' => array('User.id' => $id), 'fields' => array("User.body_collapse")));
		return $data['User']["body_collapse"];
	}

	public function getUpload($id) {
		$dataTT = strtotime(date('Y-m-d 00:00:00'));

		$plans = ClassRegistry::init('UserPlan')->find('first', array('conditions' => array('UserPlan.user_id' => $id, 'UserPlan.plan_id' => 4, ' 	UserPlan.start_date !=' => ''), 'fields' => array('UserPlan.is_active')));

		return isset($plans) ? $plans : array();
	}
	function pending_feedback_request() {
		$user_id = $this->Session->read('Auth.User.id');
		ClassRegistry::init('FeedbackUser')->bindModel(array('belongsTo' => array('Feedback')));
		return ClassRegistry::init('FeedbackUser')->find('count', array('conditions' => array('FeedbackUser.feedback_status' => 0, 'Feedback.sign_off' => 0, 'FeedbackUser.user_id' => $user_id, 'Feedback.end_date >=' => date('Y-m-d 00:00:00'))));

	}
	function pending_vote_request() {
		$user_id = $this->Session->read('Auth.User.id');
		ClassRegistry::init('VoteUser')->bindModel(array('belongsTo' => array('Vote')));
		return ClassRegistry::init('VoteUser')->find('count', array('conditions' => array('VoteUser.vote_status' => 0, 'Vote.is_completed' => 0, 'VoteUser.user_id' => $user_id, 'Vote.end_date >=' => date('Y-m-d 00:00:00'))));
	}
	function pending_group_request() {
		$user_id = $this->Session->read('Auth.User.id');
		return ClassRegistry::init('ProjectGroupUser')->find('count', array('conditions' => array('ProjectGroupUser.approved' => 0, 'ProjectGroupUser.user_id' => $user_id)));
	}

	public function getTDCount($type = null) {
		$user_id = $this->Session->read('Auth.User.id');
		$conditions = array();
		ClassRegistry::init('DoList')->recursive = 1;

		ClassRegistry::init('DoList')->unbindModel(array("hasMany" => array("DoListUser")));
		ClassRegistry::init('DoList')->bindModel(array("hasOne" => array("DoListUser")));

		$countTodo = ClassRegistry::init('DoList')->find('count', [
			'conditions' => [
				'DoListUser.user_id' => $user_id,
				'DoListUser.owner_id !=' => $user_id,
				'DoListUser.approved <=' => 0,
				'OR' => [
					'DoList.end_date >=' => date('Y-m-d'),
					'DoList.end_date IS NULL',
				],
			],
		]);

		$count = $countTodo;

		return (isset($count) && !empty($count)) ? $count : 0;
	}

	public function getBoardRequestCount() {

		$projectRequest = ClassRegistry::init('ProjectBoard')->find("count", array(
			"conditions" => array(
				"ProjectBoard.receiver" => $this->Session->read("Auth.User.id"),
				"ProjectBoard.project_status" => 0,
			),
		));

		return $projectRequest;
	}
	public function getWikiRequestCount() {
		$user_id = $this->Session->read("Auth.User.id");

		$count = ClassRegistry::init('WikiUser')->find("count", array("conditions" => array("WikiUser.owner_id" => $user_id, "WikiUser.approved" => 0)));
		return $count;
	}


	function social_activity($current_user_id = null){

		//$current_user_id = $this->Session->read('Auth.User.id');

			$query =

			"SELECT
			    'Current Project(s)' AS 'Description',
			    COUNT(proj.id) AS 'UserProjCount'
			FROM
			    user_permissions uper
			    INNER JOIN projects proj
					ON proj.id = uper.project_id
			WHERE
			    uper.user_id = $current_user_id
				AND uper.workspace_id IS NULL
			    AND proj.sign_off = 0
			    AND DATE_FORMAT(proj.end_date, '%Y%m%d') >= DATE_FORMAT(NOW(), '%Y%m%d')
			UNION ALL
			SELECT
			    'Past Project(s)' AS 'Description',
			    COUNT(proj.id) AS 'UserProjCount'
			FROM
			    user_permissions uper
				INNER JOIN projects proj
					ON proj.id = uper.project_id
			WHERE
			    uper.user_id = $current_user_id
			    AND uper.workspace_id IS NULL
			    AND proj.sign_off = 1
			UNION ALL
			SELECT
			    'Current Shared Task(s)' AS 'Description',
			    COUNT(task.id) AS 'UserProjCount'
			FROM
			    user_permissions uper
				INNER JOIN elements task
					ON task.id = uper.element_id
			WHERE
			    uper.user_id = $current_user_id
				AND DATE_FORMAT(task.end_date, '%Y%m%d') >= DATE_FORMAT(NOW(), '%Y%m%d')
			    AND task.sign_off = 0
			    AND EXISTS(
			        SELECT 1
			        FROM
			            (SELECT N'Group Sharer' AS 'Role' UNION ALL SELECT N'Sharer' AS 'Role') AS role
					WHERE uper.role = role.role)
			UNION ALL
			SELECT
			    'Past Shared Task(s)' AS 'Description',
			    COUNT(task.id) AS 'UserProjCount'
			FROM
				user_permissions uper
				INNER JOIN elements task
					ON task.id=uper.element_id
			WHERE
				uper.user_id = $current_user_id
			    AND task.sign_off = 1
			    AND EXISTS(
			        SELECT 1
			        FROM
			            (SELECT N'Group Sharer' AS 'Role' UNION ALL SELECT N'Sharer' AS 'Role') AS role
					WHERE uper.role = role.role)
			UNION ALL
			SELECT
			    'Leadership:Project(s)' AS 'Description',
			    COUNT(proj.id) AS 'UserProjCount'
			FROM
			    user_permissions uper
			    INNER JOIN projects proj
					ON proj.id = uper.project_id
			WHERE
			    uper.user_id = $current_user_id
				AND uper.workspace_id IS NULL
			    AND (DATE_FORMAT(proj.end_date, '%Y%m%d') >= DATE_FORMAT(NOW(), '%Y%m%d') OR proj.sign_off = 1)
			    AND EXISTS(
			        SELECT 1
			        FROM (
			            SELECT N'Creator' AS 'role' UNION ALL
			            SELECT N'Owner' AS 'role' UNION ALL
			            SELECT N'Group Owner' AS 'role'
					) AS role
					WHERE uper.role = role.role)
			UNION ALL
			SELECT
			    'Leadership:Task(s)' AS 'Description',
			    COUNT(task.id) AS 'UserProjCount'
			FROM
				user_permissions uper
				INNER JOIN elements task
					ON task.id=uper.element_id
				INNER JOIN element_assignments ta
					ON ta.element_id=uper.element_id
			WHERE
				uper.user_id = $current_user_id
			    AND (DATE_FORMAT(task.end_date, '%Y%m%d') >= DATE_FORMAT(NOW(), '%Y%m%d') OR task.sign_off = 1)
			    #AND EXISTS(
			        #SELECT 1
			        #FROM
			            #(SELECT N'Group Sharer' AS 'Role' UNION ALL SELECT N'Sharer' AS 'Role') AS role
					#WHERE uper.role = role.role)
				AND ta.reaction !=3
				AND ta.assigned_to =$current_user_id
			UNION ALL
			SELECT
			    'Leadership:Risk(s)' AS 'Description',
			    COUNT(DISTINCT(lead.rm_detail_id)) AS 'UserProjCount'
			FROM
				user_permissions uper
				INNER JOIN rm_leaders lead
					ON uper.user_id=lead.user_id
			WHERE
				lead.user_id = $current_user_id
				AND uper.user_id = $current_user_id
			UNION ALL
			SELECT
			    'Engagement: Social Board Nudge(s)' AS 'Description',
			    COUNT(DISTINCT(pbds.project_id))  AS 'UserProjCount'
			FROM
				user_permissions uper
			    INNER JOIN project_boards pbds
					ON pbds.project_id=uper.project_id
			WHERE
				pbds.sender = $current_user_id
				AND pbds.project_status = 1
				AND uper.user_id = $current_user_id
			    AND uper.workspace_id IS NULL
			UNION ALL
			SELECT
			    'Social Networking: Task(s) Shared via Propagation' AS 'Description',
			    count(uper.element_id)  AS 'UserProjCount'
			FROM
				user_permissions uper
			WHERE
				uper.shared_by_user_id = $current_user_id
			    AND uper.element_id IS NOT NULL
			    AND EXISTS(
					SELECT 1
			        FROM user_permissions uper_in
			        WHERE
							uper_in.role = N'Sharer'
							AND uper_in.user_id = $current_user_id
			                AND uper_in.permit_propagate = 1
			                AND uper_in.permit_propagate_read = 1
			                AND uper_in.project_id = uper.project_id
			    )
			UNION ALL
			SELECT
			    'allocated reward' AS 'Description',
			    SUM(UserProjCount) AS 'UserProjCount'
			FROM
			(
				SELECT
					'allocated reward' AS 'Description',
					SUM(rass.allocated_rewards) AS 'UserProjCount'
				FROM
					reward_assignments rass
					LEFT JOIN user_permissions uper
						ON (rass.user_id=uper.user_id AND rass.project_id=uper.project_id)
				WHERE
					rass.user_id = $current_user_id
					AND uper.workspace_id IS NULL
				UNION ALL
				SELECT
					'allocated reward' AS 'Description',
					sum(rua.accelerated_amount) as acceleratorAmount
				FROM
					reward_user_accelerations rua
					INNER JOIN user_permissions uper
						ON rua.project_id=uper.project_id
						AND rua.user_id=uper.user_id
				WHERE
					uper.user_id = $current_user_id
					AND uper.workspace_id IS NULL
			) AS UserProjCount
			UNION ALL
			SELECT
			    'user template relations' AS 'Description',
			    COUNT(*)  AS 'UserProjCount'
			FROM
				template_relations trel
					LEFT JOIN templates tmpl
						ON trel.template_id = tmpl.id
					LEFT JOIN users usr
						ON trel.user_id = usr.id
					LEFT JOIN template_categories AS tcat
						ON trel.template_category_id = tcat.id
			WHERE trel.user_id  = $current_user_id
			UNION ALL
			SELECT
			    'user vote results' AS 'Description',
			    COUNT(*)  AS 'UserProjCount'
			FROM
				vote_results vres
			WHERE
				vres.user_id  = $current_user_id
			UNION ALL
			SELECT
			    'user feedback ratings' AS 'Description',
			    COUNT(*)  AS 'UserProjCount'
			FROM
				feedback_ratings frat
			WHERE
				frat.given_by_id = $current_user_id";

		$projectCount =  ClassRegistry::init('UserPermission')->query($query);

		return  (isset($projectCount) && !empty($projectCount)) ? $projectCount : array();


	}

	function getTaskPermission($element_id = null){
		$current_user_id = $this->Session->read('Auth.User.id');

		$query = "SELECT

					user_permissions.project_id as project_id,
					user_permissions.element_id,
					user_permissions.user_id,
					user_permissions.role,
					user_permissions.permit_read AS p_read,
					user_permissions.permit_edit AS p_edit,
					user_permissions.permit_delete AS p_delete,
					user_permissions.permit_add AS p_task_add,
					user_permissions.permit_copy AS p_copy,
					user_permissions.permit_move AS p_move


		 FROM `user_permissions`

		 where user_id = $current_user_id and user_permissions.element_id=$element_id";


		 return ClassRegistry::init('UserPermission')->query($query);

	}

	function userPermissions($project_id = null, $user_id = null ){
			$current_user_id = (isset($user_id) && !empty($user_id)) ? $user_id : $this->Session->read('Auth.User.id');
			$query = "SELECT
				CONCAT_WS(' ',user_details.first_name , user_details.last_name) as fullname, user_details.user_id as user_id, user_details.profile_pic as profile_pic,user_details.org_name as org_name,user_details.job_role as job_role,user_details.job_title as job_title,
				user_permissions.user_id,user_permissions.project_id,user_permissions.role,users.email,user_permissions.permit_edit,user_permissions.permit_delete,user_permissions.shared_by_user_id

				FROM `user_permissions`
				inner join user_details on user_details.user_id = user_permissions.user_id
				LEFT JOIN users on users.id = user_permissions.user_id
				WHERE `project_id` = $project_id and user_permissions.user_id = $current_user_id  and workspace_id is null order by role ASC";

			return ClassRegistry::init('UserPermission')->query($query);
		}



	function getUserTasksProject($project_id = null, $area_id = null, $task_status = null ){


		$current_user_id = $this->Session->read('Auth.User.id');

		$status_conditions = '';
		$not_spacified = '';
		$not_started = '';
		$progress = '';
		$overdue = '';
		$completed = '';
		$status_conditions_main = $status_conditions_main_last = '';



		//echo $status_conditions;

		$query = 'SELECT

			user_permissions.role, # Uncomment for testing
			user_permissions.user_id, # Uncomment for testing
			#user_permissions.project_id, # Uncomment for testing
			user_permissions.workspace_id, # Uncomment for testing
			#user_permissions.area_id AS a_id,

			user_permissions.element_id AS e_id,
			elements.title AS e_title,
			#elements.sort_order AS e_sort_order,
			#elements.color_code AS e_color_code,
			#elements.description AS e_description,
			elements.end_date AS end_date,
			elements.modified AS modified,
			elements.updated_user_id AS updated_user_id,
			#elements.comments AS e_outcome,
			element_permissions.user_id AS element_creator,
			user_details.profile_pic AS profile_pic,
			user_details.job_title AS job_title,
			user_details.first_name AS first_name,
			user_details.last_name AS last_name,

			(CASE
				WHEN (elements.sign_off=1) THEN "CMP" # Element is signed off
				WHEN (elements.date_constraints=0) THEN "NON" # No start or end dates set
				WHEN (DATE(NOW()) BETWEEN DATE(elements.start_date) AND DATE(elements.end_date)) THEN "PRG" # Currently between start and end dates
				WHEN (DATE(NOW())<DATE(elements.start_date)) THEN "PND" # Element start date is in the future
				WHEN (DATE(elements.end_date)<DATE(NOW())) THEN "OVD" # End date has passed
				ELSE "NON"
			END) AS e_status_short_term

		FROM
			user_permissions

		INNER JOIN
			elements
			ON elements.id=user_permissions.element_id
		INNER JOIN
			element_permissions
			ON elements.id = element_permissions.element_id
			and element_permissions.is_editable = 1
		INNER JOIN
			user_details
			ON user_details.user_id=element_permissions.user_id
		WHERE
			user_permissions.user_id='.$current_user_id.' # SET: CURRENT USER ID
			AND user_permissions.project_id='.$project_id.' # SET: CURRENT WORKSPACE ID
			#AND user_permissions.area_id='.$area_id.' # SET: CURRENT AREA ID
			AND user_permissions.area_id IS NOT NULL # Elements only, workspace row is not included in result
			'.$status_conditions_main.'
			'.$status_conditions.'
			'.$status_conditions_main_last.'

		GROUP BY
			user_permissions.element_id
		ORDER BY
			user_permissions.area_id, elements.sort_order';

		return ClassRegistry::init('UserPermission')->query($query);

	}

	// Element People
	function elementUsersFromUserPermission($element_id = null  ){
			//$current_user_id = $this->Session->read('Auth.User.id');
			$query = "SELECT
				CONCAT_WS(' ',user_details.first_name , user_details.last_name) as fullname, user_details.user_id as user_id, user_details.profile_pic as profile_pic,user_details.org_name as org_name,user_details.job_role as job_role,user_details.job_title as job_title,user_details.organization_id,
				user_permissions.user_id,user_permissions.element_id,user_permissions.role,users.email,user_permissions.permit_edit,user_permissions.permit_delete,user_permissions.shared_by_user_id, organizations.name

				FROM `user_permissions`
				inner join user_details on user_details.user_id = user_permissions.user_id
				LEFT JOIN users on users.id = user_permissions.user_id
				left join organizations on user_details.organization_id = organizations.id
				WHERE  user_permissions.element_id = $element_id and element_id is not null  ORDER BY FIELD(role, 'Creator','Owner','Group Owner','Sharer','Group Sharer')";

			return ClassRegistry::init('UserPermission')->query($query);
	}

	function workspaceUsersFromUserPermission($workspace_id = null  ){
			//$current_user_id = $this->Session->read('Auth.User.id');
			$query = "SELECT
				CONCAT_WS(' ',user_details.first_name , user_details.last_name) as fullname, user_details.user_id as user_id, user_details.profile_pic as profile_pic,user_details.org_name as org_name,user_details.job_role as job_role,user_details.job_title as job_title,
				user_permissions.user_id,user_permissions.element_id,user_permissions.role,users.email,user_permissions.permit_edit,user_permissions.permit_delete,user_permissions.shared_by_user_id

				FROM `user_permissions`
				inner join user_details on user_details.user_id = user_permissions.user_id
				LEFT JOIN users on users.id = user_permissions.user_id
				WHERE  user_permissions.workspace_id = $workspace_id and area_id is null order by role ASC";

			return ClassRegistry::init('UserPermission')->query($query);
	}


	// Elements People
	function elementAllUsers($element_ids = null  ){
			//$current_user_id = $this->Session->read('Auth.User.id');

			$query = "SELECT
				CONCAT_WS(' ',user_details.first_name , user_details.last_name) as fullname, user_details.user_id as user_id, user_details.profile_pic as profile_pic,user_details.org_name as org_name,user_details.job_role as job_role,user_details.job_title as job_title,
				user_permissions.user_id,user_permissions.element_id,user_permissions.role,users.email,user_permissions.permit_edit,user_permissions.permit_delete,user_permissions.shared_by_user_id

				FROM `user_permissions`
				inner join user_details on user_details.user_id = user_permissions.user_id
				LEFT JOIN users on users.id = user_permissions.user_id
				WHERE  user_permissions.element_id IN (".implode(',',$element_ids).") order by role ASC";
			// e($query);
			return ClassRegistry::init('UserPermission')->query($query);
	}

	// Element People Count
	function elementUsersFromUserPermissionCount($element_id = null  ){
			//$current_user_id = $this->Session->read('Auth.User.id');
			$query = "SELECT COUNT(*) AS totalElement

				FROM `user_permissions`
				inner join user_details on user_details.user_id = user_permissions.user_id
				LEFT JOIN users on users.id = user_permissions.user_id
				WHERE  user_permissions.element_id = $element_id and element_id is not null order by role ASC";
			$total =  ClassRegistry::init('UserPermission')->query($query);

			return ( isset($total[0][0]['totalElement']) && !empty($total[0][0]['totalElement']) )? $total[0][0]['totalElement'] : 0;

	}

	// Project People
	function projectUsersFromUserPermission($project_id = null  ){
			//$current_user_id = $this->Session->read('Auth.User.id');
			$query = "SELECT
				CONCAT_WS(' ',user_details.first_name , user_details.last_name) as fullname, user_details.user_id as user_id, user_details.profile_pic as profile_pic,user_details.org_name as org_name,user_details.job_role as job_role,user_details.job_title as job_title,user_details.organization_id,
				user_permissions.user_id,user_permissions.element_id,user_permissions.role,users.email,user_permissions.permit_edit,user_permissions.permit_delete,user_permissions.shared_by_user_id, organizations.name

				FROM `user_permissions`
				inner join user_details on user_details.user_id = user_permissions.user_id
				LEFT JOIN users on users.id = user_permissions.user_id
				left join organizations on user_details.organization_id = organizations.id
				WHERE  user_permissions.project_id = $project_id and workspace_id is null ORDER BY FIELD(role, 'Creator','Owner','Group Owner','Sharer','Group Sharer')";

				//ORDER BY FIELD(role, 'Owner','Sharer','Creator');


			return ClassRegistry::init('UserPermission')->query($query);
	}

	// Project People Count
	function projectUsersFromUserPermissionCount($project_id = null  ){
			//$current_user_id = $this->Session->read('Auth.User.id');
			$query = "SELECT COUNT(*) AS projectPeopleCount

				FROM `user_permissions`
				inner join user_details on user_details.user_id = user_permissions.user_id
				LEFT JOIN users on users.id = user_permissions.user_id
				WHERE  user_permissions.project_id = $project_id and workspace_id is null order by role ASC";

			$total = ClassRegistry::init('UserPermission')->query($query);
			return ( isset($total[0][0]['projectPeopleCount']) && !empty($total[0][0]['projectPeopleCount']) )? $total[0][0]['projectPeopleCount'] : 0;
	}



	function getUserTasksProjectPaging($project_id = null, $page = 0, $user_id = null ){


		$current_user_id = (isset($user_id) && !empty($user_id)) ? $user_id : $this->Session->read('Auth.User.id');



		$status_conditions = '';
		$not_spacified = '';
		$not_started = '';
		$progress = '';
		$overdue = '';
		$completed = '';
		$status_conditions_main = $status_conditions_main_last = '';

		if(isset($user_id) && !empty($user_id)){
			$query = "SELECT
				user_permissions.user_id,
				b.user_id,
			    user_permissions.workspace_id,
			    user_permissions.element_id AS e_id,
			    elements.title AS e_title,
			    elements.end_date AS end_date,
			    elements.modified AS modified,
			    elements.updated_user_id AS updated_user_id,
			    (CASE
			        WHEN (elements.sign_off = 1) THEN 'CMP'
			        WHEN (elements.date_constraints = 0) THEN 'NON'
			        WHEN (DATE(NOW()) BETWEEN DATE(elements.start_date) AND DATE(elements.end_date)) THEN 'PRG'
			        WHEN (DATE(NOW()) < DATE(elements.start_date)) THEN 'PND'
			        WHEN (DATE(elements.end_date) < DATE(NOW())) THEN 'OVD'
			        ELSE 'NON'
			    END) AS e_status_short_term

			FROM
				user_permissions
			    inner join user_permissions b
			    on user_permissions.project_id = b.project_id
			    and user_permissions.element_id = b.element_id
			    inner join elements
			    on user_permissions.element_id = elements.id
			WHERE

			(elements.updated_user_id in (".$user_id.") ) and
			(user_permissions.user_id in (".$user_id.") and b.user_id in (".$this->Session->read('Auth.User.id')."))
			AND (user_permissions.project_id=".$project_id." and b.project_id=".$project_id.")
			AND (user_permissions.area_id IS NOT NULL and b.area_id IS NOT NULL)
			AND (user_permissions.element_id IS NOT NULL and b.element_id IS NOT NULL)

			ORDER BY
				elements.modified DESC
			LIMIT ".$page.", 15 ";
			//
		}
		else {
			$query = 'SELECT

				user_permissions.role,
				user_permissions.user_id,
				user_permissions.workspace_id,

				user_permissions.element_id AS e_id,
				elements.title AS e_title,
				elements.end_date AS end_date,
				elements.modified AS modified,
				elements.updated_user_id AS updated_user_id,
				(CASE
					WHEN (elements.sign_off=1) THEN "CMP"
					WHEN (elements.date_constraints=0) THEN "NON"
					WHEN (DATE(NOW()) BETWEEN DATE(elements.start_date) AND DATE(elements.end_date)) THEN "PRG"
					WHEN (DATE(NOW())<DATE(elements.start_date)) THEN "PND"
					WHEN (DATE(elements.end_date)<DATE(NOW())) THEN "OVD"
					ELSE "NON"
				END) AS e_status_short_term

			FROM
				user_permissions

			INNER JOIN
				elements
				ON elements.id=user_permissions.element_id
			WHERE
				user_permissions.user_id='.$current_user_id.'
				AND user_permissions.project_id='.$project_id.'
				AND user_permissions.area_id IS NOT NULL
				AND user_permissions.element_id IS NOT NULL

			GROUP BY
				user_permissions.element_id
			ORDER BY
				elements.modified DESC
			LIMIT '.$page.', 15';
		}

		$t = ClassRegistry::init('Setting')->query($query);

		return $t;

	}
	function getUserTasksProjectCount($project_id = null, $user_id = null) {


		$current_user_id = (isset($user_id) && !empty($user_id)) ? $user_id : $this->Session->read('Auth.User.id');

		if(isset($user_id) && !empty($user_id)){
			$query = "SELECT
				user_permissions.user_id,
				b.user_id,
			    user_permissions.element_id AS e_id,
			    elements.updated_user_id AS updated_user_id

			FROM
				user_permissions
			    inner join user_permissions b
			    on user_permissions.project_id = b.project_id
			    and user_permissions.element_id = b.element_id
			    inner join elements
			    on user_permissions.element_id = elements.id
			WHERE

			(elements.updated_user_id in (".$user_id.") ) and
			(user_permissions.user_id in (".$user_id.") and b.user_id in (".$this->Session->read('Auth.User.id')."))
			AND (user_permissions.project_id=".$project_id." and b.project_id=".$project_id.")
			AND (user_permissions.area_id IS NOT NULL and b.area_id IS NOT NULL)
			AND (user_permissions.element_id IS NOT NULL and b.element_id IS NOT NULL)";
			//
		}
		else{
			$query = 'SELECT
				 user_permissions.element_id AS e_id
			FROM
				user_permissions
			WHERE
				user_permissions.user_id='.$current_user_id.'
				AND user_permissions.project_id='.$project_id.'
				AND user_permissions.element_id IS NOT NULL' ;
		}

		$counter = ClassRegistry::init('UserPermission')->query($query);
		return (isset($counter) && !empty($counter)) ? count($counter) : 0;

	}

	function getElementDecisionSts($element_id = null){

		$current_user_id = $this->Session->read('Auth.User.id');

		$query = "SELECT
					(CASE
						WHEN (element_decisions.sign_off=0 or element_decisions.sign_off IS NULL and element_decisions.id IS NOT NULL) THEN 'PRG' # Started but not signed off
						WHEN (element_decisions.sign_off=1) THEN 'CMP' # Started and signed off
						ELSE 'NON' # No decision has been started
					END) AS e_decision_short_term

			FROM `user_permissions`

			LEFT JOIN elements
				ON elements.id=user_permissions.element_id
			LEFT JOIN element_decisions
				ON element_decisions.element_id=user_permissions.element_id AND element_decisions.sign_off != 1
			LEFT JOIN element_decision_details
				ON element_decision_details.decision_id=element_decisions.id
			AND element_decision_details.stage_status =1

			WHERE
				user_permissions.element_id = $element_id and user_permissions.user_id = $current_user_id and user_permissions.area_id is not null
				order by user_permissions.role ASC ";

		$ele_decision = ClassRegistry::init('UserPermission')->query($query);
		//pr($ele_decision); die;
		return ( isset($ele_decision) && !empty($ele_decision[0][0]) )? $ele_decision[0][0]['e_decision_short_term'] : 'NON';

	}

	function getElementDetails($element_id = null){

		$current_user_id = $this->Session->read('Auth.User.id');

		$query = "SELECT user_permissions.*
			FROM `user_permissions`

			LEFT JOIN elements
				ON elements.id=user_permissions.element_id
			WHERE
				user_permissions.element_id = $element_id and user_permissions.user_id = $current_user_id and user_permissions.area_id is not null
				order by user_permissions.role ASC ";

		$ele_details = ClassRegistry::init('UserPermission')->query($query);

		return ( isset($ele_details) && !empty($ele_details[0]) )? $ele_details : array();

	}

	function getElementDetailswsp($element_id = null){

		$current_user_id = $this->Session->read('Auth.User.id');

		$query = "SELECT user_permissions.workspace_id
			FROM `user_permissions`

			LEFT JOIN elements
				ON elements.id=user_permissions.element_id
			WHERE
				user_permissions.element_id = $element_id and user_permissions.role = 'Creator' and user_permissions.area_id is not null
				order by user_permissions.role ASC ";

		$ele_details = ClassRegistry::init('UserPermission')->query($query);

		return ( isset($ele_details) && !empty($ele_details[0]) )? $ele_details : array();

	}


	function projectDetails($project_id = null,$fields = null){
		$current_user_id = $this->Session->read('Auth.User.id');
		$fieldval = '';
		if( !empty($fields) ){
			$fieldval = 'projects.id,projects.title,projects.start_date,projects.end_date,projects.color_code';
		}

		$query = "SELECT user_permissions.role,$fieldval
			FROM `user_permissions`

			LEFT JOIN projects
				ON projects.id=user_permissions.project_id
			WHERE
				user_permissions.project_id IN ($project_id) and user_permissions.user_id = $current_user_id and user_permissions.workspace_id IS NULL
				order by projects.title ASC ";

		$p_details = ClassRegistry::init('UserPermission')->query($query);

		return ( isset($p_details) && !empty($p_details[0]) )? $p_details : array();

	}

	function project_data($project_id = null){
		$current_user_id = $this->Session->read('Auth.User.id');


		$query = "SELECT projects.*
			FROM `user_permissions`

			LEFT JOIN projects
				ON projects.id=user_permissions.project_id
			WHERE
				user_permissions.project_id IN ($project_id) and user_permissions.user_id = $current_user_id and user_permissions.workspace_id IS NULL
				order by projects.title ASC ";

		$p_details = ClassRegistry::init('UserPermission')->query($query);

		return ( isset($p_details[0]) && !empty($p_details[0]) )? $p_details[0] : array();

	}


	//============ New Task Center function =======================
	// login user all project with task count
	public function userTotalProjects($user_id = null, $status =null){

		$current_user_id = $this->Session->read('Auth.User.id');

		$sts_query = '';
		if( isset($status) && !empty($status) ){

			//============== Element Status  =============
			$ele_sts_arr = [];
			//$namedparam = '';
			if( $status == 1 ){
				//$namedparam = 'OVD';
				$ele_sts_arr[] = ' OR (DATE(elements.end_date) < DATE(NOW()) and elements.sign_off!=1 and elements.date_constraints=1 ) ';
			}
			if( $status == 4 ){
				//$namedparam = 'NON';
				$ele_sts_arr[] = ' OR ( elements.date_constraints=0 ) ';
			}
			if( $status == 5 ){
				//$namedparam = 'CMP';
				$ele_sts_arr[] = ' OR (elements.sign_off=1 and elements.date_constraints=1 ) ';
			}
			if( $status == 7 ){
				//$namedparam = 'PRG';
				$ele_sts_arr[] = ' OR (DATE(NOW()) BETWEEN DATE(elements.start_date) AND DATE(elements.end_date) and elements.sign_off!=1 and elements.date_constraints=1 ) ';
			}
			if( $status == 6 ){
				//$namedparam = 'PND';
				$ele_sts_arr[] = ' OR (DATE(NOW()) < DATE(elements.start_date) and elements.sign_off!=1 and elements.date_constraints=1 ) ';
			}


			/************************************************/

			if( isset($ele_sts_arr) && !empty($ele_sts_arr) ){

				$ele_sts_arr[0] = str_replace("OR"," ",$ele_sts_arr[0]);;
				$sts_query .= ' AND (';
				$sts_query .= implode(" ",$ele_sts_arr);

				$sts_query .= ' ) ';
			}

		}



		if( isset($user_id) && !empty($user_id) && $user_id != $current_user_id ){


			$query = "SELECT
					a.role,
					a.project_id,
					projects.title,
					a.user_id,
					b.user_id,

					( count( DISTINCT(b.element_id)) ) total_tasks
					FROM
					user_permissions a
					inner join user_permissions b
					on a.project_id = b.project_id

					INNER JOIN projects on projects.id = a.project_id
					INNER JOIN elements on elements.id = a.element_id
					INNER JOIN workspaces on workspaces.id = b.workspace_id

					WHERE
					(a.user_id in ($current_user_id) and b.user_id in ($user_id)) and a.element_id IS NOT NULL and a.role not in('Sharer','Group Sharer') and workspaces.studio_status = 0 $sts_query  GROUP BY a.project_id order by projects.title ASC  ";



		} else {

			$query ="SELECT
			projects.title,a.role,a.project_id,
			(count(distinct(a.element_id))) total_tasks
			FROM `user_permissions` a
				INNER JOIN projects on projects.id = a.project_id
				INNER JOIN elements on elements.id = a.element_id
				INNER JOIN workspaces on workspaces.id = a.workspace_id

			WHERE a.user_id = $user_id and a.element_id is NOT NULL and workspaces.studio_status = 0 $sts_query GROUP BY a.project_id ORDER BY projects.title ASC";

		}

		//echo $query;
		return $projects = ClassRegistry::init('UserPermission')->query($query);

	}

	// login user all projects all users
	public function projectTotalUsers($user_id = null){
		$query = "SELECT
					CONCAT_WS(' ',user_details.first_name , user_details.last_name) as fullname, user_details.user_id as user_id, user_details.profile_pic as profile_pic,user_details.org_name as org_name,user_details.job_role as job_role,user_details.job_title as job_title,
					user_permissions.user_id,user_permissions.project_id,user_permissions.role,users.email

					FROM `user_permissions`
						inner join user_details on user_details.user_id = user_permissions.user_id
						LEFT JOIN users on users.id = user_permissions.user_id
						LEFT JOIN projects on projects.id = user_permissions.project_id

					WHERE user_permissions.project_id IN (SELECT project_id from user_permissions where user_id = $user_id and workspace_id is null) AND workspace_id is null
				GROUP BY user_permissions.user_id ORDER BY fullname ASC";
		return ClassRegistry::init('UserPermission')->query($query);

	}



	// Element status count like CMP, PND OVD ....
	public function taskStatusCount($user_id = null, $project_id = null, $named_params = null ){
		// named params will not affect project total count
		$named_params = '';
		$current_user_id = $this->Session->read('Auth.User.id');

		/*****************************************************/
		$sts_query = '';
		$ele_sts_arr = [];

		$element_sts = array();
		if( isset($named_params) && !empty($named_params) ){
			if( $named_params == 1 ){
				$element_sts[] = 'OVD';
			}
			if( $named_params == 4 ){
				$element_sts[] = 'NON';
			}
			if( $named_params == 5 ){
				$element_sts[] = 'CMP';
			}
			if( $named_params == 7 ){
				$element_sts[] = 'PRG';
			}
			if( $named_params == 6 ){
				$element_sts[] = 'PND';
			}

			if( $named_params == 8 ){
				$element_sts[] = 'NON';
				$element_sts[] = 'PND';
				$element_sts[] = 'PRG';
				$element_sts[] = 'OVD';
			}
		}

		if( isset($element_sts) && !empty($element_sts) ){

			foreach($element_sts as $ele_sts_val){

				if( $ele_sts_val == 'CMP' ){

					$ele_sts_arr[] = ' OR (elements.sign_off=1 and elements.date_constraints=1 ) ';
				}

				if( $ele_sts_val == 'PRG' ){

					$ele_sts_arr[] = ' OR (DATE(NOW()) BETWEEN DATE(elements.start_date) AND DATE(elements.end_date) and elements.sign_off!=1 and elements.date_constraints=1 ) ';

				}

				if( $ele_sts_val == 'PND' ){

					$ele_sts_arr[] = ' OR (DATE(NOW()) < DATE(elements.start_date) and elements.sign_off!=1 and elements.date_constraints=1 ) ';

				}


				if( $ele_sts_val == 'OVD' ){
					$ele_sts_arr[] = ' OR (DATE(elements.end_date) < DATE(NOW()) and elements.sign_off!=1 and elements.date_constraints=1 ) ';

				}

				if( $ele_sts_val == 'NON' ){
					$ele_sts_arr[] = ' OR ( elements.date_constraints=0 ) ';

				}
			}

		}

		if( isset($ele_sts_arr) && !empty($ele_sts_arr) ){

			$ele_sts_arr[0] = str_replace("OR"," ",$ele_sts_arr[0]);;
			$sts_query .= ' AND (';
			$sts_query .= implode(" ",$ele_sts_arr);

			$sts_query .= ' ) ';
		}
		/*****************************************************/



		if( isset($user_id) && !empty($user_id) && $user_id != $current_user_id ){

			$prjCondition = '';
			if( isset($project_id) && !empty($project_id) ){
				$prjCondition = " AND a.project_id IN ($project_id) ";
			}


			$query = "SELECT

						sum(elements.sign_off=1) AS CMP,
						sum(elements.date_constraints=0) AS NON,
						sum(DATE(NOW()) BETWEEN DATE(elements.start_date) AND DATE(elements.end_date) and elements.sign_off!=1   and elements.date_constraints=1  ) AS PRG,
						sum(DATE(NOW())<DATE(elements.start_date) and elements.sign_off!=1 and elements.date_constraints=1) AS PND,
						sum(DATE(elements.end_date)<DATE(NOW()) and elements.sign_off!=1 and elements.date_constraints=1) AS OVD

					FROM
						user_permissions a

						INNER JOIN user_permissions b on b.element_id = a.element_id
						INNER JOIN elements on elements.id = a.element_id
						INNER JOIN workspaces wp on a.workspace_id = wp.id and wp.studio_status = 0


					WHERE (a.user_id in ($current_user_id) and b.user_id in ($user_id)) and a.element_id IS NOT NULL and a.role not in('Sharer','Group Sharer') $prjCondition $sts_query ";



		} else {

			$prjCondition = '';
			if( isset($project_id) && !empty($project_id) ){
				$prjCondition = " AND user_permissions.project_id IN ($project_id) ";
			}

			$query = "SELECT
				sum(elements.sign_off=1) AS CMP,
				sum(elements.date_constraints=0) AS NON,
				sum(DATE(NOW()) BETWEEN DATE(elements.start_date) AND DATE(elements.end_date) and elements.sign_off!=1   and elements.date_constraints=1  ) AS PRG,
				sum(DATE(NOW())<DATE(elements.start_date) and elements.sign_off!=1 and elements.date_constraints=1) AS PND,
				sum(DATE(elements.end_date)<DATE(NOW()) and elements.sign_off!=1 and elements.date_constraints=1) AS OVD
			FROM
				user_permissions
			INNER JOIN
				elements
				ON elements.id=user_permissions.element_id	and elements.studio_status = 0
			INNER JOIN workspaces on user_permissions.workspace_id = workspaces.id and workspaces.studio_status = 0

			WHERE
				user_permissions.user_id IN ($user_id) AND
				user_permissions.project_id IN (SELECT project_id from user_permissions where user_permissions.user_id IN ($user_id) and user_permissions.workspace_id is null)  $prjCondition $sts_query  and user_permissions.element_id IS NOT NULL";

		}
		//echo $query;
		return ClassRegistry::init('UserPermission')->query($query);
	}

	// data for task center filtered data file
	function task_center_filtered_data($project_id = null, $user_id = null){
		$current_user_id = $this->Session->read('Auth.User.id');

			$query = "SELECT (SELECT
				JSON_ARRAYAGG(JSON_OBJECT(
					'full_name',CONCAT_WS(' ',user_details.first_name , user_details.last_name),
					'user_id',user_details.user_id,
					'profile_pic',user_details.profile_pic,
					'org_name',user_details.org_name,
					'job_role',user_details.job_role,
					'job_title',user_details.job_title,
					'email',users.email
					)) AS JSON
				FROM `user_permissions`
				inner join user_details on user_details.user_id = user_permissions.user_id
				LEFT JOIN users on users.id = user_permissions.user_id
				WHERE  user_permissions.element_id = elements.id order by role ASC) as user_detail,

				user_permissions.role,
				user_permissions.project_id,
				user_permissions.permit_add,
				user_permissions.permit_read,
				user_permissions.permit_edit,
				user_permissions.permit_delete,
				user_permissions.permit_copy,
				user_permissions.permit_move,

				workspaces.id as ws_id,
				workspaces.title as ws_title,
				workspaces.start_date as ws_start,
				workspaces.end_date as ws_end,
				workspaces.color_code as ws_code,
				(CASE
					WHEN (workspaces.sign_off=1) THEN 'CMP' # Element is signed off

					WHEN (DATE(NOW()) BETWEEN DATE(workspaces.start_date) AND DATE(workspaces.end_date)) THEN 'PRG' # Currently between start and end dates
					WHEN (DATE(NOW())<DATE(workspaces.start_date)) THEN 'PND' # Element start date is in the future
					WHEN (DATE(workspaces.end_date)<DATE(NOW())) THEN 'OVD' # End date has passed
					WHEN (DATE(workspaces.start_date)>DATE(NOW())) THEN 'not_started' # Start date is not reached
					ELSE 'NON'
				END) AS ws_status,

				elements.id as ele_id,
				elements.title as ele_title,
				elements.start_date as ele_start,
				elements.end_date as ele_end,
				elements.color_code as ele_code,
				elements.date_constraints as ele_date_constraints,

				(CASE
					WHEN (elements.sign_off=1) THEN 'completed' # Element is signed off
					WHEN (DATE(NOW()) BETWEEN DATE(elements.start_date) AND DATE(elements.end_date)) THEN 'progress' # Currently between start and end dates
					WHEN (DATE(NOW())<DATE(elements.start_date)) THEN 'PND' # Element start date is in the future
					WHEN (DATE(elements.end_date)<DATE(NOW())) THEN 'overdue' # End date has passed
					WHEN (DATE(elements.start_date)>DATE(NOW())) THEN 'not_started' # Start date is not reached
					ELSE 'not_spacified'
				END) AS ele_status,

				element_assignments.created_by as created_by,
				element_assignments.assigned_to as assigned_to,
				element_assignments.reaction as reaction,
				element_assignments.created as created,
				element_assignments.modified as modified


					FROM `user_permissions`

					INNER JOIN elements
						ON elements.id=user_permissions.element_id
					LEFT JOIN element_assignments
						ON element_assignments.element_id=elements.id
					INNER JOIN workspaces
						ON workspaces.id=user_permissions.workspace_id
					INNER JOIN projects
						ON projects.id=user_permissions.project_id
					WHERE
						user_permissions.project_id = $project_id and user_permissions.user_id = $current_user_id and user_permissions.element_id is not null order by user_permissions.role ASC ";
			//return $query;
			return ClassRegistry::init('User')->query($query);
	}


	function getElementWithPermission($user_id = null, $project_id = null){

		if (isset($user_id) && !empty($user_id)){
			$all = [];
			if (isset($project_id) && !empty($project_id)) {
				foreach ($project_id as $key => $value) {
					$query = "SELECT user_permissions.*
						FROM `user_permissions`

						INNER JOIN elements
							ON elements.id=user_permissions.element_id
						INNER JOIN projects
							ON projects.id=user_permissions.project_id

						WHERE
							user_permissions.project_id = $project_id and user_permissions.user_id = $user_id and user_permissions.element_id is not null
							order by user_permissions.role ASC ";

					$ele_details = ClassRegistry::init('UserPermission')->query($query);
					return ( isset($ele_details) && !empty($ele_details[0]) )? $ele_details : array();
				}
			}
		}

	}

	//============ End New Task Center function =====================


	// data for task center filtered data file
	function task_center_filtered_data_new($project_ids = null, $user_id = null){

		$allprojects = implode(",",$project_ids);

		$current_user_id = $this->Session->read('Auth.User.id');

			$query = "SELECT (SELECT
				JSON_ARRAYAGG(JSON_OBJECT(
					'full_name',CONCAT_WS(' ',user_details.first_name , user_details.last_name),
					'user_id',user_details.user_id,
					'profile_pic',user_details.profile_pic,
					'org_name',user_details.org_name,
					'job_role',user_details.job_role,
					'job_title',user_details.job_title,
					'email',users.email
					)) AS JSON
				FROM `user_permissions`
				inner join user_details on user_details.user_id = user_permissions.user_id
				LEFT JOIN users on users.id = user_permissions.user_id
				WHERE  user_permissions.element_id = elements.id order by role ASC) as user_detail,


				workspaces.id as ws_id,
				workspaces.title as ws_title,
				workspaces.start_date as ws_start,
				workspaces.end_date as ws_end,
				workspaces.color_code as ws_code,

				workspaces.id as ws_id,
				workspaces.title as ws_title,
				workspaces.start_date as ws_start,
				workspaces.end_date as ws_end,
				workspaces.color_code as ws_code,
				(CASE
					WHEN (workspaces.sign_off=1) THEN 'CMP' # Element is signed off

					WHEN (DATE(NOW()) BETWEEN DATE(workspaces.start_date) AND DATE(workspaces.end_date)) THEN 'PRG' # Currently between start and end dates
					WHEN (DATE(NOW())<DATE(workspaces.start_date)) THEN 'PND' # Element start date is in the future
					WHEN (DATE(workspaces.end_date)<DATE(NOW())) THEN 'OVD' # End date has passed
					WHEN (DATE(workspaces.start_date)>DATE(NOW())) THEN 'not_started' # Start date is not reached
					ELSE 'NON'
				END) AS ws_status,

				elements.id as ele_id,
				elements.title as ele_title,
				elements.start_date as ele_start,
				elements.end_date as ele_end,
				elements.color_code as ele_code,
				elements.date_constraints as ele_date_constraints,

				(CASE
					WHEN (elements.sign_off=1) THEN 'completed' # Element is signed off
					WHEN (DATE(NOW()) BETWEEN DATE(elements.start_date) AND DATE(elements.end_date)) THEN 'progress' # Currently between start and end dates
					WHEN (DATE(NOW())<DATE(elements.start_date)) THEN 'PND' # Element start date is in the future
					WHEN (DATE(elements.end_date)<DATE(NOW())) THEN 'overdue' # End date has passed
					WHEN (DATE(elements.start_date)>DATE(NOW())) THEN 'not_started' # Start date is not reached
					ELSE 'not_spacified'
				END) AS ele_status,

				element_assignments.created_by as created_by,
				element_assignments.assigned_to as assigned_to,
				element_assignments.reaction as reaction,
				element_assignments.created as created

					FROM `user_permissions`

					INNER JOIN elements
						ON elements.id=user_permissions.element_id
					LEFT JOIN element_assignments
						ON element_assignments.element_id=elements.id
					INNER JOIN workspaces
						ON workspaces.id=user_permissions.workspace_id
					INNER JOIN projects
						ON projects.id=user_permissions.project_id
					WHERE
						user_permissions.project_id IN ($allprojects) and user_permissions.user_id = $current_user_id and user_permissions.element_id is not null order by user_permissions.role ASC ";
			// echo $query;
			return ClassRegistry::init('UserPermission')->query($query);
	}

	function project_task_type_old($project_id = null){
		$all_tasks = array();
		if( isset($project_id) && !empty($project_id) ){
			$task_types =  ClassRegistry::init('ProjectElementType')->find('all', array('conditions' => array('ProjectElementType.project_id' => $project_id, 'ProjectElementType.type_status'=>1 ),'order'=> 'ProjectElementType.title ASC' ));
			if( isset($task_types) && !empty($task_types) ){
				 $all_tasks = $task_types;
			}
		}
		return $all_tasks;
	}

	function project_task_type($project_id = null,$workspace_id = null) {
		$all_tasks = array();
		$current_user_id = $this->Session->read('Auth.User.id');
		if( isset($project_id) && !empty($project_id) ){

			$query = "SELECT DISTINCT(ET.type_id), project_element_types.title,project_element_types.id FROM `element_types` ET   inner join  project_element_types ON project_element_types.id = ET.type_id and  project_element_types.project_id = $project_id join  user_permissions ON user_permissions.element_id = ET.element_id where user_permissions.user_id = $current_user_id and  user_permissions.workspace_id=$workspace_id and user_permissions.project_id=$project_id or ( project_element_types.title='General' ) order by project_element_types.title ASC ";

			$task_types = ClassRegistry::init('UserPermission')->query($query);
			$allids = [];
			if( isset($task_types) && !empty($task_types)  ){
				$all_tasks = $task_types;
				$allids = Set::extract($all_tasks, '{n}.project_element_types.title');
			}

			$query = "SELECT id, title FROM project_types project_element_types WHERE title = 'General'";
			$task_types = ClassRegistry::init('UserPermission')->query($query);
			if(isset($allids) && !empty($allids)){
				$general_id = Set::extract($task_types, '{n}.project_element_types.title');

				if(!in_array($general_id[0], $allids)){
					if( isset($task_types) && !empty($task_types)  ){
						 $all_tasks[] = $task_types[0] ;
					}
				}
			}
			// pr($task_types);

		}
		return $all_tasks;
	}

	function dashboard_taskcount($project_id = null){
		$current_user_id = $this->Session->read('Auth.User.id');
		if( isset($project_id) && !empty($project_id) ){

		$sql ="SELECT
				(count(distinct(a.element_id))) total_tasks
			FROM `user_permissions` a
				INNER JOIN projects on projects.id = a.project_id AND a.project_id = $project_id
				INNER JOIN elements on elements.id = a.element_id
				INNER JOIN workspaces on workspaces.id = a.workspace_id

			WHERE a.user_id = $current_user_id and a.element_id is NOT NULL and workspaces.studio_status = 0  GROUP BY a.project_id ORDER BY projects.title ASC";
			$projecttask =  ClassRegistry::init('User')->query($sql);
			if( isset($projecttask) && !empty($projecttask[0][0]['total_tasks']) ){
				return $projecttask[0][0]['total_tasks'];
			} else {
				return 0;
			}

		} else {
			return 0;
		}

	}


	function all_projects_UP(){

		$current_user_id = $this->Session->read('Auth.User.id');
		$query = "SELECT projects.id as id, projects.title as title   FROM user_permissions
						INNER JOIN
						projects
						ON	projects.id=user_permissions.project_id
		WHERE user_id = $current_user_id and  `workspace_id` IS NULL group by project_id ";

		$propagationCount =  ClassRegistry::init('UserPermission')->query($query);

		return $propagationCount;
	}


	function get_projectWorkspace($project_id = null, $task_status = null){
		$current_user_id = $this->Session->read('Auth.User.id');


		$status_conditions = '';
		$not_spacified = '';
		$not_started = '';
		$progress = '';
		$overdue = '';
		$completed = '';
		$status_conditions_main = $status_task_type = $status_conditions_main_last = '';


		if( isset($task_status) &&  !empty($task_status)  ){

			$status_conditions_main = "AND ( ";

			foreach( $task_status as $status_list ){

				if( !empty($status_list) && $status_list == 'undefined' ){
					$not_spacified = 1 ;
				}
				if( !empty($status_list) && $status_list == 'not_started' ){
					$not_started = 1;
				}
				if( !empty($status_list) && $status_list == 'in_progress' ){
					$progress = 1;
				}
				if( !empty($status_list) && $status_list == 'overdue' ){
					$overdue = 1;
				}
				if( !empty($status_list) && $status_list == 'completed' ){
					$completed = 1;
				}

			}
			$status_conditions = '';
			if( isset($not_spacified) && $not_spacified == 1 ){
				$status_conditions .=" (Element.date_constraints=0) ";
			}
			if( isset($not_started) && $not_started == 1 ){
				if( !empty($status_conditions) ){
					$status_conditions .=" OR (Element.sign_off=0 AND DATE(NOW())<DATE(Element.start_date))  ";
				} else {
					$status_conditions .=" (Element.sign_off=0 AND DATE(NOW())<DATE(Element.start_date))  ";
				}

			}
			if( isset($progress) && $progress == 1 ){
				if( !empty($status_conditions) ){
					$status_conditions .=" OR (Element.sign_off=0 AND DATE(NOW()) BETWEEN DATE(Element.start_date) AND 	DATE(Element.end_date) and Element.date_constraints=1 )   ";
				} else {
					$status_conditions .=" (Element.sign_off=0 AND DATE(NOW()) BETWEEN DATE(Element.start_date) AND 	DATE(Element.end_date) and Element.date_constraints=1 )  ";
				}
			}
			if( isset($overdue) && $overdue == 1 ){
				if( !empty($status_conditions) ){
					$status_conditions .=" OR (Element.sign_off=0 AND DATE(NOW())>DATE(Element.end_date))  ";
				} else {
					$status_conditions .="  (Element.sign_off=0 AND DATE(NOW())>DATE(Element.end_date))  ";
				}
			}
			if( isset($completed) && $completed == 1 ){
				if( !empty($status_conditions) ){
					$status_conditions .=" OR (Element.sign_off=1) # Status=Complete ";
				} else {
					$status_conditions .=" (Element.sign_off=1) # Status=Complete ";
				}
			}


			$status_conditions_main_last = ")";
		}

		$query = "SELECT Workspace.id,Workspace.title ,

			(CASE
				WHEN (Workspace.sign_off=1) THEN 'Completed' # Element is signed off
				WHEN (Workspace.start_date IS NULL) THEN 'Not Set' # No start or end dates set
				WHEN (DATE(NOW()) BETWEEN DATE(Workspace.start_date) AND DATE(Workspace.end_date)) THEN 'In Progress' # Currently between start and end dates
				WHEN (DATE(NOW())<DATE(Workspace.start_date)) THEN 'Not Started' # Workspace start date is in the future
				WHEN (DATE(Workspace.end_date)<DATE(NOW())) THEN 'Overdue' # End date has passed
				ELSE 'Unknown'
			END) AS w_status
			FROM `user_permissions`
			inner join project_workspaces on user_permissions.workspace_id = project_workspaces.workspace_id
			inner join workspaces Workspace on
				user_permissions.workspace_id = Workspace.id and
				Workspace.studio_status != 1
			INNER JOIN
			elements Element
			ON Element.id=user_permissions.element_id
		WHERE user_permissions.project_id = $project_id
		and user_permissions.user_id = $current_user_id
		#and user_permissions.area_id is null
			$status_conditions_main
			$status_conditions
			$status_conditions_main_last
		GROUP BY
			user_permissions.workspace_id
		order by project_workspaces.sort_order asc";


		$result =  ClassRegistry::init('UserPermission')->query($query);

		return (isset($result) && !empty($result)) ? $result : array();

	}

	function signoff_comment($modal = null, $id = null){

		App::import("Model", $modal);
		$signoff = new $modal();
		$modalid = '';
		if( $modal == 'SignoffProject' ){
			$modalid = 'project_id';
		} else if( $modal == 'SignoffWorkspace' ){
			$modalid = 'workspace_id';
		} else {
			$modalid = 'element_id';
		}

		$check = $signoff->find('count',array('conditions'=>array($modal.'.'.$modalid => $id ) ));

		if( $check > 0 ){
			return 1;
		} else {
			return 0;
		}

	}


	function projectsDetails($project_id = null,$fields = null){
		$current_user_id = $this->Session->read('Auth.User.id');
		$fieldval = '';
		if( !empty($fields) ){
			$fieldval = 'Project.id,Project.title,Project.start_date,Project.end_date,Project.color_code';
		}
		$query = "SELECT $fieldval
			FROM `user_permissions`

			LEFT JOIN projects Project
				ON Project.id=user_permissions.project_id
			WHERE
				user_permissions.project_id IN ($project_id) and user_permissions.user_id = $current_user_id and user_permissions.workspace_id IS NULL
				order by Project.title ASC ";

		$p_details = ClassRegistry::init('UserPermission')->query($query);

		return ( isset($p_details) && !empty($p_details[0]) )? $p_details[0] : array();

	}


	function checkEleDepndancy($element_ids = null){

		if ( isset($element_ids) && !empty($element_ids) ) {

			$views = new View();
			$commonHelper = $views->loadHelper('Common');
			$dataStus = '';
			$dataStus = $commonHelper->dependancy_status($element_ids, 1);
			if( $dataStus == 'both' ){
				$dataStus = 'predessor & successor';
			}
			return $dataStus;

		}
	}


	function get_project_elements($project_id = null, $count = false, $ids = false, $area = false){

		$sel = 'elements.*';
		if($count){
			$sel = 'count(*) as total';
		}
		if($ids){
			$sel = 'elements.id';
		}
		$area_qry = '';
		if($area){
			$area_qry = 'INNER JOIN
			areas
			ON areas.id=user_permissions.area_id';
			$sel = 'elements.*, areas.id, areas.title';
		}
		$user_id = $this->Session->read('Auth.User.id');

		$query = "SELECT
			$sel
		FROM
			user_permissions
		INNER JOIN
			elements
			ON elements.id=user_permissions.element_id	and elements.studio_status = 0

		$area_qry

		INNER JOIN workspaces on user_permissions.workspace_id = workspaces.id and workspaces.studio_status = 0

		WHERE
			user_permissions.user_id IN ($user_id) AND
			user_permissions.project_id IN (SELECT project_id from user_permissions where user_permissions.user_id IN ($user_id) and user_permissions.workspace_id is null) AND user_permissions.project_id IN ($project_id) AND user_permissions.element_id IS NOT NULL";

		return $data = ClassRegistry::init('UserPermission')->query($query);
	}



	function next_5_day_tasks($project_id = null){

		$user_id = $this->Session->read('Auth.User.id');
		$fiveDays = date("Y-m-d", strtotime(date('Y-m-d') . " +5 Days"));

		$query = "SELECT
			count(elements.id) as totalDays
		FROM
			user_permissions
		INNER JOIN
			elements
			ON elements.id=user_permissions.element_id	and elements.studio_status = 0

		WHERE
			user_permissions.user_id = $user_id AND user_permissions.project_id = $project_id AND user_permissions.element_id IS NOT NULL AND date(elements.end_date) BETWEEN '" . date('Y-m-d') . "' AND '" . $fiveDays . "' AND elements.date_constraints = 1 AND elements.sign_off != 1";

		// e($query);
		$data = ClassRegistry::init('UserPermission')->query($query);
		$totalDays = 0;
		if(isset($data) && !empty($data)){
			$totalDays = $data[0][0]['totalDays'];
		}
		return $totalDays;
	}


	public function getRAGStatus($project_id = null, $numbers = false, $project_elements = null, $elements_overdue = null, $rag_status = null) {
		App::import("Model", "Project");
		App::import("Model", "ProjectRag");
		$this->Project = new Project();
		$this->ProjectRag = new ProjectRag();

		if(!isset($rag_status) || empty($rag_status)){
			$project_data = $this->Project->find('first', array('recursive' => -1, 'conditions' => array('Project.id' => $project_id)));
			if (isset($project_data['Project']) && !empty($project_data['Project'])) {
				$rag_status = $project_data['Project']['rag_status'];
			}
		}
		$project_rag = $this->ProjectRag->find('first', array('recursive' => 1, 'conditions' => array('ProjectRag.project_id' => $project_id)));

		$percent = 0;

		if( !empty($elements_overdue) && !empty($project_elements) ){
			$percent = round( ($elements_overdue  /  $project_elements ) * 100);
		}

		$amber_value = $red_value = 0;
		if (isset($project_rag['ProjectRag']) && !empty($project_rag['ProjectRag'])) {
			$amber_value = $project_rag['ProjectRag']['amber_value'];
			$red_value = $project_rag['ProjectRag']['red_value'];
		}

		if (isset($rag_status) && $rag_status == 1) {
			$rag_color = (isset($numbers) && $numbers == true) ? 1 : 'bg-red';
		} else if (isset($rag_status) && $rag_status == 2) {
			$rag_color = (isset($numbers) && $numbers == true) ? 2 : 'bg-yellow';
		} else if (isset($rag_status) && $rag_status == 3) {
			$rag_color = (isset($numbers) && $numbers == true) ? 3 : 'bg-green';

			if (($amber_value > 0 && $red_value > 0) && $percent >= $amber_value && $percent < $red_value) {
				$rag_color = (isset($numbers) && $numbers == true) ? 2 : 'bg-yellow';
			} else if (($amber_value > 0 && $red_value > 0) && ($percent >= $red_value && $percent <= 100)) {
				$rag_color = (isset($numbers) && $numbers == true) ? 1 : 'bg-red';

			}

			if (((!isset($amber_value) || $amber_value == '' || $amber_value == 0) && $red_value > 0) && ($percent >= $red_value && $percent <= 100)) {
				$rag_color = (isset($numbers) && $numbers == true) ? 1 : 'bg-red';

			}

			if (((!isset($red_value) || $red_value == '' || $red_value == 0) && $amber_value > 0) && ($percent >= $amber_value && $percent <= 100)) {

				$rag_color = (isset($numbers) && $numbers == true) ? 2 : 'bg-yellow';
			}
		}
		$rag_color = (isset($rag_color) && !empty($rag_color)) ? $rag_color : '';

		return ['rag_color' => $rag_color, 'percent' => $percent];
	}


}
