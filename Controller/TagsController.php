<?php
App::uses('Controller/Component', 'Auth', 'Session', 'RequestHandler');

App::uses('Sanitize', 'Utility');

class TagsController extends AppController {

	public $name = 'Tags';
	public $uses = ['Tag'];
	public $user_id = null;
	public $pagination = null;
	public $mongoDB = null;
	public $live_setting;
	public $components = array( 'Common');
	public $objView = null;
	public $tag_paging = 50;

	/**
	 * check login for admin and frontend user
	 * allow and deny user
	 */

	/**
	 * Helpers
	 *
	 * @var array
	 */
	public $helpers = array('Html', 'Form', 'Session', 'Text', 'Common', 'ViewModel');

	public function beforeFilter() {
		parent::beforeFilter();

		$view = new View();
		$this->objView = $view;

		// $this->Auth->allow();

		$this->set('controller', 'tags');

		$this->user_id = $this->Auth->user('id');

		$this->live_setting = LIVE_SETTING;

		if( $_SERVER['HTTP_HOST'] != LOCALIP )  {

			if( $this->Session->read('Auth.User.role_id') == 3 ){
				$this->redirect(['controller' => 'organisations','action' => 'dashboard']);
			}
			if( $this->Session->read('Auth.User.role_id') == 1 ){
				$this->redirect(['controller' => 'dashboard','action' => 'index']);
			}
		}
	}

	public function get_user_tags() {
		$data = null;

		$this->loadModel('Tag');
		$loggedInUserId = $this->Session->read("Auth.User.id");
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			$cond = ['user_id' => $loggedInUserId, 'tagged_user_id' => $this->request->data['q']];
			$tags = $this->Tag->find('all', ['conditions' => $cond,'fields' => array('Tag.id', 'Tag.tag'), 'order' => ['tag ASC'], 'group' => array('tag')]);
			$sk = array();
			if (isset($tags) && !empty($tags)) {
				foreach ($tags as $key => $value) {
					$sk[] = ['id' => $value['Tag']['id'], 'name' => $value['Tag']['tag'],'name1' => htmlentities($value['Tag']['tag'],ENT_QUOTES,'UTF-8')];
				}
			}
			echo json_encode($sk);exit;
		}
	}

	public function get_tags() {
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';

			$response = [
				'success' => false,
				'msg' => null,
				'content' => null,
			];
			$tags = [];
			if ($this->request->isJson()) {
				if (isset($this->request->query['term'])) {
					$this->loadModel('Tag');
					$loggedInUserId = $this->Session->read("Auth.User.id");

					$excludeTagIds = (isset($this->request->query['selectedTags']) && !empty($this->request->query['selectedTags'])) ? trim($this->request->query['selectedTags']) : '';

					$queryConcat = '';
					if($excludeTagIds != '') {
						/*$getExceludedData = $this->Tag->find('list', ['conditions' => ['id' => explode(',', $excludeTagIds)],'fields' => array('Tag.id', 'Tag.tag')]);
						$excludedTags = array_values($getExceludedData);*/

						$excludedTags = array_unique(explode(',,,', trim(strtolower($excludeTagIds))));

						$args = array_map(function($excludedTags) {
							return sprintf("%s", $excludedTags);
						}, $excludedTags);
						$args = join(",,,", $args);

						//$ser = '^';
						$args= Sanitize::escape($args);

						//pr($args);

						$queryConcat = " AND tag NOT IN ('$args') ";

					}



					if(!empty($this->request->query['term'])) {
						/*if(preg_match('/^[a-zA-Z0-9_]*$/', $this->request->query['term'])) {
							echo json_encode($response);
							exit;
						}*/
						$term = strtolower($this->request->query['term']);
						//$term = htmlentities($term, null, 'utf-8');
						$ser = '^';
						$term= Sanitize::escape(like($term, $ser ));

						$query = "SELECT DISTINCT tag, id FROM tags WHERE user_id = $loggedInUserId AND (`tag` LIKE '% $term%' ESCAPE '$ser' OR  `tag` LIKE  '%$term %' ESCAPE '$ser'  OR  `tag` LIKE '% $term %' ESCAPE '$ser' OR `tag` LIKE '$term%' ESCAPE '$ser' OR `tag` LIKE '%$term%' ESCAPE '$ser' ) $queryConcat GROUP BY tag ORDER BY tag ASC";
					} else {
						$query = "SELECT DISTINCT tag, id FROM tags WHERE user_id = $loggedInUserId  $queryConcat GROUP BY tag ORDER BY tag ASC";
					}

					$tags = $this->Tag->query($query);
					if (isset($tags) && !empty($tags)) {
						$tags = Set::classicExtract($tags, '{n}.tags.tag');
					}

					if (isset($tags) && !empty($tags)) {
						$response['success'] = true;
						$response['content'] = $tags;
					}
				}

			}
			echo json_encode($response);
			exit;
		}
	}

	public function add_remove_tag() {
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';

			$tags = [];
			if ($this->request->isJson()) {
				if (isset($this->request->data['tag']) && !empty(trim($this->request->data['tag']))) {
					$this->loadModel('Tag');
					$loggedInUserId = $this->Session->read("Auth.User.id");
					$tagged_user_id = $this->request->data['user_profile_view_id'];
					$tag = trim($this->request->data['tag']);
					$task = trim($this->request->data['task']);

					if($task == 'add') {
						//Check if this tag is already given to this user by loggedin user
						$chkCnt = $this->Tag->find('count', ['conditions' => ['user_id' => $loggedInUserId, 'tagged_user_id' => $tagged_user_id, 'LCASE(tag)' => strtolower($tag)]]);
						if($chkCnt == 0) {
							$insertTag = [
								'user_id' => $loggedInUserId,
								'tagged_user_id' => $tagged_user_id,
								'tag' => $tag,
							];
							$this->Tag->save($insertTag);
						}
					} else if($task == 'remove') {
						$deleteConditions = array('user_id'=>$loggedInUserId, 'tagged_user_id'=>$tagged_user_id, 'LCASE(tag)'=>strtolower($tag));
						$this->Tag->deleteAll($deleteConditions);
					}
				}
			}

			$cond = ['user_id' => $loggedInUserId, 'tagged_user_id' => $tagged_user_id];
					$tags = $this->Tag->find('all', ['conditions' => $cond,'fields' => array('Tag.id', 'Tag.tag'), 'order' => ['tag ASC'], 'group' => array('tag')]);

					if (isset($tags) && !empty($tags)) {
						foreach ($tags as $key => $value) {
							$response[] = ['id' => $value['Tag']['id'], 'name' => $value['Tag']['tag']];
						}
					} else {
						$response = [];
					}

			echo json_encode($response);exit;
		}
	}

	public function my_tags() {
		$this->layout = 'inner';
		$this->set('title_for_layout', __('My Tags', true));
		$this->set('page_heading', __('My Tags', true));
		$this->set('page_subheading', __('Find and organize People you tagged', true));
		$viewData = null;

		$viewData['crumb'] = [
			'last' => [
				'data' => [
					'title' => "My Tags",
					'data-original-title' => "My Tags",
				],
			],
		];

		$this->loadModel('Tag');
		$loggedInUserId = $this->Session->read("Auth.User.id");
		$tags = $this->Tag->find('list', ['conditions' => ['user_id' => $loggedInUserId],'fields' => array('Tag.tag'), 'order' => ['tag ASC'], 'group' => array('tag')]);

		$sk = [];
		if (isset($tags) && !empty($tags)) {
			foreach ($tags as $key => $value) {
				$sk[] = $value;
			}
		}
		$viewData['tags'] = $sk;
		$this->set('tags', $sk);
		$this->set($viewData);

		$this->setJsVar('tag_paging', $this->tag_paging);
	}

	public function get_my_tags() {
		$data = null;

		$this->loadModel('Tag');
		$loggedInUserId = $this->Session->read("Auth.User.id");
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			$cond = ['user_id' => $loggedInUserId];
			$tags = $this->Tag->find('all', ['conditions' => $cond,'fields' => array('Tag.id', 'Tag.tag'), 'order' => ['tag ASC'], 'group' => array('tag')]);

			if (isset($tags) && !empty($tags)) {
				foreach ($tags as $key => $value) {
					$sk[] = ['id' => $value['Tag']['id'], 'name' => $value['Tag']['tag']];
				}
			}
			echo json_encode($sk);exit;
		}
	}

	public function search_users() {
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			$this->autoRender = false;

			$result = [];
			$match_all = 1;
			$items_per_page = $this->tag_paging;
			if ($this->request->isAjax()) {
				$q = trim($this->request->data['q']);
				if (isset($this->request->data['q']) && !empty(trim($this->request->data['q']))) {
					$match_all = $this->request->data['match_all'];
					$offset = (isset($this->request->data['page']) && !empty(trim($this->request->data['page']))) ? trim($this->request->data['page']) : 0;

					$result = $this->_filter_search_users('list', $q, $match_all, $offset);
				}
			}
			$this->set('result', $result);
			$this->set('q', $q);
			$this->set('match_all', $match_all);
			$this->render('ajax/search_users', 'ajax');
		}
	}

	protected function _filter_search_users($action = '', $q = '', $match_all = 1, $offset = 0) {
		$tag_elements = [];
		if (isset($q) && !empty(trim($q))) {
			$termsArr = array_map('trim', explode('$$$', $q));

			 $termsArr = escapes($termsArr );
			$termsStr = implode("','",$termsArr);

			//$termsStr= Sanitize::escape($termsStr);


			$pagination = "";
			if($action == 'list') {
				$pagination = " LIMIT $offset, $this->tag_paging ";
			}

			if($match_all == 1) {
				$tagCnt = count($termsArr);

				$findCountQuery = "SELECT
					tagged_user_id, user_details.first_name, user_details.last_name, user_details.profile_pic, count(tagged_user_id) as tagcnt, GROUP_CONCAT(tag ORDER BY tag ASC  SEPARATOR '$$$') as taglist
				FROM tags
				LEFT JOIN users ON tags.tagged_user_id = users.id
				LEFT JOIN user_details ON users.id = user_details.user_id
				WHERE tags.user_id = $this->user_id AND tagged_user_id IN(
					SELECT tagged_user_id
					FROM `tags`
					WHERE 1 AND tag IN ('$termsStr') AND user_id = $this->user_id
					GROUP BY tagged_user_id
					HAVING COUNT(DISTINCT tag) = $tagCnt
				)
				GROUP BY tagged_user_id ORDER BY tagcnt DESC";

				$findQuery = "SELECT
					tagged_user_id, user_details.first_name, user_details.last_name, user_details.profile_pic, count(tagged_user_id) as tagcnt, GROUP_CONCAT(tag ORDER BY tag ASC  SEPARATOR '$$$') as taglist
				FROM tags
				LEFT JOIN users ON tags.tagged_user_id = users.id
				LEFT JOIN user_details ON users.id = user_details.user_id
				WHERE tags.user_id = $this->user_id AND tagged_user_id IN(
					SELECT tagged_user_id
					FROM `tags`
					WHERE 1 AND tag IN ('$termsStr') AND user_id = $this->user_id
					GROUP BY tagged_user_id
					HAVING COUNT(DISTINCT tag) = $tagCnt
				)
				GROUP BY tagged_user_id ORDER BY tagcnt DESC $pagination";
			}
			if($match_all == 0) {
				$totQuery = "(";
				foreach($termsArr as $k => $v) {
					$totQueryArr[] = " SUM(CASE WHEN tag = '".$v."' THEN 1 ELSE 0 END) ";
				}
				$totQuery .= implode(' + ', $totQueryArr);
				$totQuery .= " ) AS totMatch ";

				$findCountQuery = "SELECT
					tagged_user_id, user_details.first_name, user_details.last_name, user_details.profile_pic, count(tagged_user_id) as tagcnt, GROUP_CONCAT(tag ORDER BY tag ASC SEPARATOR '$$$') as taglist, $totQuery
				FROM tags
				LEFT JOIN users ON tags.tagged_user_id = users.id
				LEFT JOIN user_details ON users.id = user_details.user_id
				WHERE tags.user_id = $this->user_id AND tagged_user_id IN(
					SELECT tagged_user_id
					FROM `tags`
					WHERE 1 AND tag IN ('$termsStr') AND user_id = $this->user_id
					GROUP BY tagged_user_id
				)
				GROUP BY tagged_user_id
				ORDER BY totMatch DESC, tagcnt DESC";

				$findQuery = "SELECT
					tagged_user_id, user_details.first_name, user_details.last_name, user_details.profile_pic, count(tagged_user_id) as tagcnt, GROUP_CONCAT(tag ORDER BY tag ASC SEPARATOR '$$$') as taglist, $totQuery
				FROM tags
				LEFT JOIN users ON tags.tagged_user_id = users.id
				LEFT JOIN user_details ON users.id = user_details.user_id
				WHERE tags.user_id = $this->user_id AND tagged_user_id IN(
					SELECT tagged_user_id
					FROM `tags`
					WHERE 1 AND tag IN ('$termsStr') AND user_id = $this->user_id
					GROUP BY tagged_user_id
				)
				GROUP BY tagged_user_id
				ORDER BY totMatch DESC, tagcnt DESC $pagination";
				// pr( $findQuery) ;
			}


			$result['tagElements'] = $this->Tag->query($findQuery);
			$result['tagElemsCnt'] = $this->Tag->query($findCountQuery);
		}
		return $result;
	}

	public function clear_user_tag() {
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';

			$response['status'] = false;
			$response['message'] = '';
			if ($this->request->isAjax()) {
				if (isset($this->request->data['id']) && !empty(trim($this->request->data['id']))) {
					$this->loadModel('Tag');
					$user_id = $this->Session->read("Auth.User.id");
					$tagged_user_id = $this->request->data['id'];

					$deleteConditions = array('user_id'=>$user_id, 'tagged_user_id'=>$tagged_user_id);
					$this->Tag->deleteAll($deleteConditions);

					$response['status'] = true;
					$response['message'] = '';
				}
			}
			echo json_encode($response);exit;
		}
	}

	public function tags_actions() {
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';

			$response = [];
			if ($this->request->isAjax()) {
				if (isset($this->request->data['action']) && !empty(trim($this->request->data['action']))) {
					$action = $this->request->data['action'];
					$tags = (isset($this->request->data['tags']) && !empty(trim($this->request->data['tags']))) ? trim($this->request->data['tags']) : '';
					$match_all = (isset($this->request->data['match_all']) && trim($this->request->data['match_all']) != '') ? trim($this->request->data['match_all']) : 1;
					$user_list_type = (isset($this->request->data['userListType']) && trim($this->request->data['userListType']) != '') ? trim($this->request->data['userListType']) : 'tag';

					switch($action) {
						case 'add_remove_tags':
							$response = $this->_addRemoveTags($tags, $match_all);
							break;
						case 'clear_all_people_tags':
							$response = $this->_clearAllPeopleTags($tags, $match_all, $user_list_type);
							break;
						case 'delete_my_tags':
							$response = $this->_deleteMyTags();
							break;
					}
				}
			}
			echo json_encode($response);exit;
		}
	}

	public function add_remove_tags() {
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';

			$response['status'] = false;
			$response['message'] = '';
			$response['data'] = array();
			if ($this->request->isJson()) {
				if (isset($this->request->data['sel_tag_ids']) && !empty($this->request->data['sel_tag_ids'])) {
					$match_all = (isset($this->request->data['match_all']) && trim($this->request->data['match_all']) != '') ? trim($this->request->data['match_all']) : 1;
					$selTags = $this->request->data['sel_tag_ids'];
					$filteredTags = (isset($this->request->data['filtered_tags']) && !empty($this->request->data['filtered_tags'])) ? $this->request->data['filtered_tags'] : '';
					$action = $this->request->data['action'];
                    $user_list_type = (isset($this->request->data['userListType']) && trim($this->request->data['userListType']) != '') ? trim($this->request->data['userListType']) : 'tag';

                    if($user_list_type == 'tag') {
						if($filteredTags != '') {
							$tag_elements = $this->_filter_search_users('actions', $filteredTags, $match_all);
						} else {
							echo json_encode($response);exit;
						}
                    } else {
                        $this->loadModel('Tag');
                        $findQuery = "SELECT
							tagged_user_id, user_details.first_name, user_details.last_name, user_details.profile_pic, count(tagged_user_id) as tagcnt, GROUP_CONCAT(tag ORDER BY tag ASC SEPARATOR '$$$') as taglist
						FROM tags
						LEFT JOIN users ON tags.tagged_user_id = users.id
						LEFT JOIN user_details ON users.id = user_details.user_id
						WHERE tags.user_id = $this->user_id
						GROUP BY tagged_user_id ORDER BY user_details.last_name ASC";

				        $tag_elements['tagElements'] = $this->Tag->query($findQuery);
                    }
					if(!empty($tag_elements) && !empty($tag_elements['tagElements'])) {
						$taggedUserIds = [];

						$this->loadModel('Tag');
						$loggedInUserId = $this->user_id;

						foreach($tag_elements['tagElements'] as $key => $val) {
							$taggedUserId = $val['tags']['tagged_user_id'];
							foreach($selTags as $k => $v) {
								$message = "";
								if($action == 'add') {
									$chkCnt = $this->Tag->find('count', ['conditions' => ['user_id' => $loggedInUserId, 'tagged_user_id' => $taggedUserId, 'LCASE(tag)' => strtolower($v)]]);
									if($chkCnt == 0) {
										$insertTag = [
											'user_id' => $loggedInUserId,
											'tagged_user_id' => $taggedUserId,
											'tag' => $v,
										];
										$this->Tag->create(false);
										$this->Tag->save($insertTag);
									}
									$message = "Tag(s) have been added";
								} elseif($action == 'remove') {
									$deleteConditions = array('user_id'=>$loggedInUserId, 'tagged_user_id'=>$taggedUserId, 'LCASE(tag)'=>strtolower($v));
									$this->Tag->deleteAll($deleteConditions);
									$message = "Tag(s) have been removed";
								}
							}
						}


						$tags = $this->Tag->find('list', ['conditions' => ['user_id' => $loggedInUserId],'fields' => array('Tag.tag'), 'order' => ['tag ASC'], 'group' => array('tag')]);

						$data = [];
						if (isset($tags) && !empty($tags)) {
							foreach ($tags as $key => $value) {
								$data[] = $value;
							}
						}

						$response['status'] = true;
						$response['message'] = $message;
						$response['data'] = $data;
					}
				}
			}
			echo json_encode($response);exit;
		}
	}

	protected function _clearAllPeopleTags($tags, $match_all, $user_list_type) {
		$this->loadModel('Tag');
		$user_id = $this->Session->read("Auth.User.id");

		$response['status'] = false;
		$response['data'] = array();

		$q = $tags;
		//if (isset($q) && !empty(trim($q))) {
			if($user_list_type == 'tag') {
				if (isset($q) && !empty(trim($q))) {
					$tag_elements = $this->_filter_search_users('actions', $q, $match_all);
				} else {
					return $response;
				}
			} else {
				$this->loadModel('Tag');
				$findQuery = "SELECT
						tagged_user_id, user_details.first_name, user_details.last_name, user_details.profile_pic, count(tagged_user_id) as tagcnt, GROUP_CONCAT(tag ORDER BY tag ASC  SEPARATOR '$$$') as taglist
					FROM tags
					LEFT JOIN users ON tags.tagged_user_id = users.id
					LEFT JOIN user_details ON users.id = user_details.user_id
					WHERE tags.user_id = $this->user_id
					GROUP BY tagged_user_id ORDER BY user_details.last_name ASC";

				$tag_elements['tagElements'] = $this->Tag->query($findQuery);
            }
			if(!empty($tag_elements)) {
				$taggedUserIds = [];
				foreach($tag_elements['tagElements'] as $k => $v) {
					$taggedUserIds[] = $v['tags']['tagged_user_id'];
				}
				if(!empty($taggedUserIds)) {
					$deleteConditions = array('user_id'=>$user_id, 'tagged_user_id' => $taggedUserIds);
					$this->Tag->deleteAll($deleteConditions);

					$response['status'] = true;
					$response['data'] = array();
				}
			}
		//}
		return $response;
	}

	protected function _deleteMyTags() {
		$this->loadModel('Tag');
		$user_id = $this->Session->read("Auth.User.id");

		$deleteConditions = array('user_id'=>$user_id);
		$this->Tag->deleteAll($deleteConditions);

		$response['status'] = true;
		$response['data'] = array();
		return $response;
	}

	public function tags_setting() {
		$response = ['success' => false];
		if ($this->request->isAjax()) {

			$this->layout = false;
			$html = '';

			$this->loadModel('Tag');
			if(isset($this->request->params['named']['type']) && !empty($this->request->params['named']['type']) ) {
				$action_type = $viewData['type'] = $this->request->params['named']['type'];
				$loggedInUserId = $this->Session->read("Auth.User.id");

				switch($action_type) {
					case 'add_remove_tags':
						$tags = $this->Tag->find('list', ['conditions' => ['user_id' => $loggedInUserId],'fields' => array('Tag.tag'), 'order' => ['tag ASC'], 'group' => array('tag')]);

						$sk = [];
						if (isset($tags) && !empty($tags)) {
							foreach ($tags as $key => $value) {
								$sk[] = $value;
							}
						}
						$viewData['tags'] = $sk;
						$viewData['title'] = 'Add/Remove Tags from People Listed';
						$viewData['message'] = '<p>Select one or more Tags to be added or removed from the people listed.</p>';
						break;
					case 'clear_all_tags':
						$viewData['tags'] = array();
						$viewData['title'] = 'Clear All Tags from People Listed';
						$viewData['message'] = '<p>This action will remove all Tags from each person in the list.</p><p>Are you sure you want to Clear All Tags from People Listed?</p>';
						break;
					case 'delete_my_tags':
						$viewData['tags'] = array();
						$viewData['title'] = 'Delete All My Tags';
						$viewData['message'] = '<p>This action will delete all Tags you have created for all people.</p><p>Are you sure you want to Delete All My Tags?</p>';
						break;
				}
				$this->set('viewData', $viewData);
				$this->render(DS . 'Tags' . DS . 'partials' . DS . 'tags_setting');
			}
		}
	}

	public function get_saved_tags(){
	    if($this->request->is('json')){
	        $this->loadModel('Tag');
			$loggedInUserId = $this->Session->read("Auth.User.id");
			$tags = $this->Tag->find('list', ['conditions' => ['user_id' => $loggedInUserId],'fields' => array('Tag.tag'), 'order' => ['tag ASC'], 'group' => array('tag')]);

			$sk = [];
			if (isset($tags) && !empty($tags)) {
				foreach ($tags as $key => $value) {
					$sk[] = $value;
				}
			}
	        echo json_encode($sk);
	        exit;

	    }
	    $this->autoRender = false;
	}

	public function get_all_tagged_people() {
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			$this->autoRender = false;

			$result = [];
			$items_per_page = $this->tag_paging;
			if ($this->request->isAjax()) {
				$offset = (isset($this->request->data['page']) && !empty(trim($this->request->data['page']))) ? trim($this->request->data['page']) : 0;

				$pagination = " LIMIT $offset, $this->tag_paging ";

				$findCountQuery = "SELECT
							tagged_user_id, user_details.first_name, user_details.last_name, user_details.profile_pic, count(tagged_user_id) as tagcnt, GROUP_CONCAT(tag ORDER BY tag ASC  SEPARATOR '$$$') as taglist
						FROM tags
						LEFT JOIN users ON tags.tagged_user_id = users.id
						LEFT JOIN user_details ON users.id = user_details.user_id
						WHERE tags.user_id = $this->user_id
						GROUP BY tagged_user_id ORDER BY user_details.last_name ASC";

				$findQuery = "SELECT
							tagged_user_id, user_details.first_name, user_details.last_name, user_details.profile_pic, user_details.job_title, count(tagged_user_id) as tagcnt, GROUP_CONCAT(tag ORDER BY tag ASC  SEPARATOR '$$$') as taglist
						FROM tags
						LEFT JOIN users ON tags.tagged_user_id = users.id
						LEFT JOIN user_details ON users.id = user_details.user_id
						WHERE tags.user_id = $this->user_id
						GROUP BY tagged_user_id ORDER BY user_details.first_name ASC $pagination";

				$result['tagElements'] = $this->Tag->query($findQuery);
				$result['tagElemsCnt'] = $this->Tag->query($findCountQuery);
			}
			$this->set('result', $result);
			$this->render('ajax/all_tagged_people', 'ajax');
		}
	}

	public function apply_user_filter() {
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';

			$response = [
				'success' => false,
				'msg' => null,
				'content' => null,
			];
			if ($this->request->isJson()) {
				$type = $this->request->data['type'];

				$loggedInUserId = $this->Session->read("Auth.User.id");
				$selected = (isset($this->request->data['selected']) && !empty($this->request->data['selected'])) ? trim($this->request->data['selected']) : '';
				$all_users = (isset($this->request->data['all_users']) && !empty($this->request->data['all_users'])) ? trim($this->request->data['all_users']) : '';
				$match_all = (isset($this->request->data['is_match_all']) && trim($this->request->data['is_match_all']) != '') ? $this->request->data['is_match_all'] : false;
				$project_id = $this->request->data['project_id'];

				if ($type == 'tag') {
					$this->loadModel('Tag');
					if($selected != '') {
						$termsArr = array_map('trim', explode('$$$', $selected));
						$tgs = explode(',', $termsArr[0]);
						$selectedTags = implode("','",$termsArr);

						$tagCnt = count($termsArr);
						$matchAllCond = "";
						if($match_all) {
							$matchAllCond = " HAVING COUNT(DISTINCT tag) = $tagCnt ";
						}
						//('$selectedTags')

						$findQuery = "SELECT
									tagged_user_id, user_details.first_name, user_details.last_name
								FROM tags
								LEFT JOIN users ON tags.tagged_user_id = users.id
								LEFT JOIN user_details ON users.id = user_details.user_id
								WHERE tags.user_id = $this->user_id AND tagged_user_id IN(
									SELECT tagged_user_id
									FROM `tags`
									WHERE 1 AND tag IN ('" . implode("', '", $tgs) . "') AND user_id = $this->user_id AND tagged_user_id IN ($all_users)
									GROUP BY tagged_user_id
									$matchAllCond
								)
								GROUP BY tagged_user_id ORDER BY user_details.first_name ASC";
						// pr($findQuery);
					} else {
						/*$selectedTags = array();
						echo $tagCnt = 0;die;

						$findQuery = "SELECT
								tagged_user_id, user_details.first_name, user_details.last_name
							FROM tags
							LEFT JOIN users ON tags.tagged_user_id = users.id
							LEFT JOIN user_details ON users.id = user_details.user_id
							WHERE tags.user_id = $this->user_id
							GROUP BY tagged_user_id ORDER BY user_details.first_name ASC";*/
					}

					$tagUsers = $this->Tag->query($findQuery);
					$result = array();
					if (isset($tagUsers) && !empty($tagUsers)) {
						foreach($tagUsers as $k => $v) {
							$result[] = [
								'label' => $v['user_details']['first_name'] . ' ' . $v['user_details']['last_name'],
								'value' => $v['tags']['tagged_user_id']
							];
							// $result[$k]['label'] = $v['user_details']['first_name'] . ' ' . $v['user_details']['last_name'];
							// $result[$k]['value'] = $v['tags']['tagged_user_id'];
						}
					}
					$response['success'] = true;
					$response['content'] = $result;
				} elseif ($type == 'skill') {
					$this->loadModel('UserSkill');

					$non_proj_users = array();
					$concatQuery = " AND user_skills.user_id IN ($all_users) ";
					/*if($project_id > 0) {
						$view = new View();
						$viewModel = $view->loadHelper('Permission');
						$projUsers = $viewModel->users_on_project($project_id);
						if(!empty($projUsers)) {
							$user_ids = Set::extract('{n}/user_permissions/user_id', $projUsers);
							$nonProjUsersIdsImp = implode(',', $user_ids);
							$concatQuery = " AND user_skills.user_id NOT IN ($nonProjUsersIdsImp) ";
						}
					}*/

					if($selected != '') {
						$termsArr = array_map('trim', explode('$$$', $selected));
						$selectedSkills = implode("','",$termsArr);
						$skillCnt = count($termsArr);
						$matchAllCond = "";
						if($match_all) {
							$matchAllCond = " HAVING COUNT(DISTINCT skill_id) = $skillCnt ";
						}

						$findQuery = "SELECT
									user_skills.user_id, user_details.first_name, user_details.last_name
								FROM user_skills
								LEFT JOIN users ON user_skills.user_id = users.id
								LEFT JOIN user_details ON users.id = user_details.user_id
								WHERE user_skills.user_id IN(
									SELECT user_skills.user_id
									FROM `user_skills`
									WHERE 1 AND skill_id IN ('$selectedSkills') $concatQuery
									GROUP BY user_skills.user_id
									$matchAllCond
								)
								GROUP BY user_skills.user_id ORDER BY user_details.first_name ASC";
					} else {
						/*$selectedTags = array();
						$tagCnt = 0;

						$findQuery = "SELECT
								user_skills.user_id, user_details.first_name, user_details.last_name
							FROM user_skills
							LEFT JOIN users ON user_skills.user_id = users.id
							LEFT JOIN user_details ON users.id = user_details.user_id WHERE 1 $concatQuery
							GROUP BY user_skills.user_id ORDER BY user_details.first_name ASC";*/
					}
					//echo $findQuery;die;
					$skillUsers = $this->UserSkill->query($findQuery);
					$result = array();
					if (isset($skillUsers) && !empty($skillUsers)) {
						foreach($skillUsers as $k => $v) {
							$result[$k]['label'] = $v['user_details']['first_name'] . ' ' . $v['user_details']['last_name'];
							$result[$k]['value'] = $v['user_skills']['user_id'];
						}
					}
					$response['success'] = true;
					$response['content'] = $result;
				} elseif ($type == 'subject') {
					$this->loadModel('UserSubject');

					$non_proj_users = array();
					$concatQuery = " AND user_subjects.user_id IN ($all_users) ";
					/*if($project_id > 0) {
						$view = new View();
						$viewModel = $view->loadHelper('Permission');
						$projUsers = $viewModel->users_on_project($project_id);
						if(!empty($projUsers)) {
							$user_ids = Set::extract('{n}/user_permissions/user_id', $projUsers);
							$nonProjUsersIdsImp = implode(',', $user_ids);
							$concatQuery = " AND user_subjects.user_id NOT IN ($nonProjUsersIdsImp) ";
						}
					}*/

					if($selected != '') {
						$termsArr = array_map('trim', explode('$$$', $selected));
						$selectedSubjects = implode("','",$termsArr);
						$subjectCnt = count($termsArr);
						$matchAllCond = "";
						if($match_all) {
							$matchAllCond = " HAVING COUNT(DISTINCT subject_id) = $subjectCnt ";
						}

						$findQuery = "SELECT
									user_subjects.user_id, user_details.first_name, user_details.last_name
								FROM user_subjects
								LEFT JOIN users ON user_subjects.user_id = users.id
								LEFT JOIN user_details ON users.id = user_details.user_id
								WHERE user_subjects.user_id IN(
									SELECT user_subjects.user_id
									FROM `user_subjects`
									WHERE 1 AND subject_id IN ('$selectedSubjects') $concatQuery
									GROUP BY user_subjects.user_id
									$matchAllCond
								)
								GROUP BY user_subjects.user_id ORDER BY user_details.first_name ASC";
					} else {
						/*$findQuery = "SELECT
								user_subjects.user_id, user_details.first_name, user_details.last_name
							FROM user_subjects
							LEFT JOIN users ON user_subjects.user_id = users.id
							LEFT JOIN user_details ON users.id = user_details.user_id WHERE 1 $concatQuery
							GROUP BY user_subjects.user_id ORDER BY user_details.first_name ASC";*/
					}
					//echo $findQuery;die;
					$subjectUsers = $this->UserSubject->query($findQuery);
					$result = array();
					if (isset($subjectUsers) && !empty($subjectUsers)) {
						foreach($subjectUsers as $k => $v) {
							$result[$k]['label'] = $v['user_details']['first_name'] . ' ' . $v['user_details']['last_name'];
							$result[$k]['value'] = $v['user_subjects']['user_id'];
						}
					}
					$response['success'] = true;
					$response['content'] = $result;
				} elseif ($type == 'domain') {
					$this->loadModel('UserDomain');

					$non_proj_users = array();
					$concatQuery = " AND user_domains.user_id IN ($all_users) ";

					if($selected != '') {
						$termsArr = array_map('trim', explode('$$$', $selected));
						$selectedDomains = implode("','",$termsArr);
						$domainCnt = count($termsArr);
						$matchAllCond = "";
						if($match_all) {
							$matchAllCond = " HAVING COUNT(DISTINCT domain_id) = $domainCnt ";
						}

						$findQuery = "SELECT
									user_domains.user_id, user_details.first_name, user_details.last_name
								FROM user_domains
								LEFT JOIN users ON user_domains.user_id = users.id
								LEFT JOIN user_details ON users.id = user_details.user_id
								WHERE user_domains.user_id IN(
									SELECT user_domains.user_id
									FROM `user_domains`
									WHERE 1 AND domain_id IN ('$selectedDomains') $concatQuery
									GROUP BY user_domains.user_id
									$matchAllCond
								)
								GROUP BY user_domains.user_id ORDER BY user_details.first_name ASC";
					} else {
						/*$findQuery = "SELECT
								user_subjects.user_id, user_details.first_name, user_details.last_name
							FROM user_subjects
							LEFT JOIN users ON user_subjects.user_id = users.id
							LEFT JOIN user_details ON users.id = user_details.user_id WHERE 1 $concatQuery
							GROUP BY user_subjects.user_id ORDER BY user_details.first_name ASC";*/
					}
					//echo $findQuery;die;
					$domainUsers = $this->UserDomain->query($findQuery);
					$result = array();
					if (isset($domainUsers) && !empty($domainUsers)) {
						foreach($domainUsers as $k => $v) {
							$result[$k]['label'] = $v['user_details']['first_name'] . ' ' . $v['user_details']['last_name'];
							$result[$k]['value'] = $v['user_domains']['user_id'];
						}
					}
					$response['success'] = true;
					$response['content'] = $result;
				}
			}
			echo json_encode($response);
			exit;
		}
	}

	public function add_tags_team_members() {
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			$program_id = $project_id = $workspace_id = $task_id = $risk_id = $todo_id = $cusers = null;

			$type = $this->params['named']['type'];
			if(isset($this->params['named']) && !empty($this->params['named'])){
				$params = $this->params['named'];

				$program_id = (isset($params['program']) && !empty($params['program'])) ? $params['program'] : null;
				$project_id = (isset($params['project']) && !empty($params['project'])) ? $params['project'] : null;
				$workspace_id = (isset($params['workspace']) && !empty($params['workspace'])) ? $params['workspace'] : null;
				$task_id = (isset($params['task']) && !empty($params['task'])) ? $params['task'] : null;
				$hash = (isset($params['hash']) && !empty($params['hash'])) ? $params['hash'] : null;
				$risk_id = (isset($params['risk']) && !empty($params['risk'])) ? $params['risk'] : null;
				$todo_id = (isset($params['todo']) && !empty($params['todo'])) ? $params['todo'] : null;
				$cusers = (isset($params['cusers']) && !empty($params['cusers'])) ? $params['cusers'] : null;
				$selected = (isset($params['selected']) && !empty($params['selected'])) ? $params['selected'] : null;
			}

			$this->set('type', $type);
			$this->set('program_id', $program_id);
			$this->set('project_id', $project_id);
			$this->set('workspace_id', $workspace_id);
			$this->set('task_id', $task_id);
			$this->set('hash', $hash);
			$this->set('risk_id', $risk_id);
			$this->set('todo_id', $todo_id);
			$this->set('cusers', $cusers);
			$this->set('selected', $selected);

			$this->render('/Tags/partials/add_tags_team_members');
		}
	}

	public function save_tags_team_members() {
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
			$response = ['success' => false, 'content' => []];

			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;

				if(isset($post['user']) && !empty($post['user']) && isset($post['tags']) && !empty($post['tags'])) {
					$userIdsExp = explode(',', $post['user']);
					$tagIdsExp = explode(',', $post['tags']);

					$savingArr = [];

					$this->loadModel('Tag');
					$loggedInUserId = $this->Session->read("Auth.User.id");

					foreach($userIdsExp as $k => $user_id ) {
						foreach($tagIdsExp as $t => $tag) {
							$chkCnt = $this->Tag->find('count', ['conditions' => ['user_id' => $loggedInUserId, 'tagged_user_id' => $user_id, 'LCASE(tag)' => strtolower($tag)]]);
							if($chkCnt == 0) {
								$insertTag = [
									'user_id' => $loggedInUserId,
									'tagged_user_id' => $user_id,
									'tag' => $tag,
								];
								$this->Tag->create(false);
								$this->Tag->save($insertTag);
							}
						}
					}
					$response = ['success' => true, 'content' => []];
				}
			}
			echo json_encode($response);
			exit;
		}
	}

	public function get_tag_users() {
		if ($this->request->isAjax()) {
            $this->layout = 'ajax';

            $response = ['success' => false, 'content' => ''];
            if ($this->request->is('post') || $this->request->is('put')) {
            	$post = $this->request->data;
				$tag_elements = [];

				if (isset($post['q']) && !empty(trim($post['q']))) {
					$q = trim($post['q']);
					$match_all = $post['match_all'];
					$termsArr = array_map('trim', explode('$$$', $q));

					 $termsArr = escapes($termsArr);
					$termsStr = implode("','",$termsArr);


					$pagination = "";

					if($match_all == 1) {
						$tagCnt = count($termsArr);

						$findCountQuery = "SELECT
							tagged_user_id, user_details.first_name, user_details.last_name, user_details.profile_pic, count(tagged_user_id) as tagcnt, GROUP_CONCAT(tag ORDER BY tag ASC  SEPARATOR '$$$') as taglist
						FROM tags
						LEFT JOIN users ON tags.tagged_user_id = users.id
						LEFT JOIN user_details ON users.id = user_details.user_id
						WHERE tags.user_id = $this->user_id AND tagged_user_id IN(
							SELECT tagged_user_id
							FROM `tags`
							WHERE 1 AND tag IN ('$termsStr') AND user_id = $this->user_id
							GROUP BY tagged_user_id
							HAVING COUNT(DISTINCT tag) = $tagCnt
						)
						GROUP BY tagged_user_id ORDER BY tagcnt DESC";
					}
					if($match_all == 0) {
						$totQuery = "(";
						foreach($termsArr as $k => $v) {
							$totQueryArr[] = " SUM(CASE WHEN tag = '".$v."' THEN 1 ELSE 0 END) ";
						}
						$totQuery .= implode(' + ', $totQueryArr);
						$totQuery .= " ) AS totMatch ";

						$findCountQuery = "SELECT
							tagged_user_id, user_details.first_name, user_details.last_name, user_details.profile_pic, count(tagged_user_id) as tagcnt, GROUP_CONCAT(tag ORDER BY tag ASC SEPARATOR '$$$') as taglist, $totQuery
						FROM tags
						LEFT JOIN users ON tags.tagged_user_id = users.id
						LEFT JOIN user_details ON users.id = user_details.user_id
						WHERE tags.user_id = $this->user_id AND tagged_user_id IN(
							SELECT tagged_user_id
							FROM `tags`
							WHERE 1 AND tag IN ('$termsStr') AND user_id = $this->user_id
							GROUP BY tagged_user_id
						)
						GROUP BY tagged_user_id
						ORDER BY totMatch DESC, tagcnt DESC";
					}

					$result = $this->Tag->query($findCountQuery);
					if(isset($result) && !empty($result)){
            			$result = Set::extract($result, '{n}.tags.tagged_user_id');
            			$response['content'] = implode(',', $result);
            			$response['success'] = true;
            		}
				}
				else{
					$findCountQuery = "SELECT tagged_user_id
						FROM tags
						LEFT JOIN users ON tags.tagged_user_id = users.id
						LEFT JOIN user_details ON users.id = user_details.user_id
						WHERE tags.user_id = $this->user_id
						GROUP BY tagged_user_id ORDER BY user_details.last_name ASC";
					$result = $this->Tag->query($findCountQuery);
					$allTags = [];
					if(isset($result) && !empty($result)){
            			$result = Set::extract($result, '{n}.tags.tagged_user_id');
            			$response['content'] = implode(',', $result);
            			$response['success'] = true;
            		}

				}
			}
		}
		echo json_encode($response);
		exit;
	}

	public function get_selected_tags() {
		if ($this->request->isAjax()) {
            $this->layout = 'ajax';

            $response = ['success' => false, 'content' => ''];
            if ($this->request->is('post') || $this->request->is('put')) {
            	$post = $this->request->data;

				if (isset($post['q']) && !empty(trim($post['q']))) {
					$q = trim($post['q']);
					$termsArr = array_map('trim', explode('$$$', $q));

				 	$termsArr = escapes($termsArr);
					$response['content'] = implode("$$$",$termsArr);
					$response['success'] = true;
				}
				else{
					$findCountQuery = "SELECT GROUP_CONCAT(tag ORDER BY tag ASC  SEPARATOR '$$$') as taglist
						FROM tags
						LEFT JOIN users ON tags.tagged_user_id = users.id
						LEFT JOIN user_details ON users.id = user_details.user_id
						WHERE tags.user_id = $this->user_id
						GROUP BY tagged_user_id ORDER BY user_details.last_name ASC";
					$result = $this->Tag->query($findCountQuery);
					$allTags = [];
					if(isset($result) && !empty($result)){
            			$result = Set::extract($result, '{n}.0.taglist');
            			$allTags = implode('$$$', $result);

            			$tt = [];
            			foreach ($result as $key => $value) {
            				$abc = [];
            				if(strpos($value, "$$$")) {
            					$abc = explode("$$$", $value);
            					$tt = array_merge($tt, $abc);
            				}
            				else{
            					$tt = array_merge($tt, [$value]);
            				}
            			}
            			pr(array_unique($tt));

            			$response['content'] = $allTags;
            			$response['success'] = true;
            		}
				}
			}
		}
		echo json_encode($response);
		exit;
	}

	public function get_all_tags() {
		if ($this->request->isAjax()) {
            $this->layout = 'ajax';

            $response = ['success' => false, 'content' => ''];
            if ($this->request->is('post') || $this->request->is('put')) {
            	$post = $this->request->data;
				$findCountQuery = "SELECT GROUP_CONCAT(tag ORDER BY tag ASC  SEPARATOR '$$$') as taglist
					FROM tags
					LEFT JOIN users ON tags.tagged_user_id = users.id
					LEFT JOIN user_details ON users.id = user_details.user_id
					WHERE tags.user_id = $this->user_id
					GROUP BY tagged_user_id ORDER BY user_details.last_name ASC";
				$result = $this->Tag->query($findCountQuery);
				$allTags = [];
				if(isset($result) && !empty($result)){
        			$result = Set::extract($result, '{n}.0.taglist');

        			foreach ($result as $key => $value) {
        				$abc = [];
        				if(strpos($value, "$$$")) {
        					$abc = explode("$$$", $value);
        					$allTags = array_merge($allTags, $abc);
        				}
        				else{
        					$allTags = array_merge($allTags, [$value]);
        				}
        			}

        			$response['content'] = array_unique($allTags);
        			$response['success'] = true;
        		}
			}
		}
		echo json_encode($response);
		exit;
	}
}
