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

/**
 * Application helper
 *
 * Add your application-wide methods in the class below, your helpers
 * will inherit them.
 *
 * @package app.View.Helper
 */
class WikiHelper extends Helper {
	var $helpers = array(
		'Html',
		'Session',
		'Thumbnail',
		'Common',
		"Group",
	);
	public function getProjectWiki($project_id = null, $user_id = null) {
		App::import("Model", "Wiki");
		$wiki = new Wiki();

		$data = $wiki->find('first', [
			'conditions' => [
				'Wiki.project_id' => $project_id,
			],
		]);

		// / if (isset($data['Wiki']['user_id']) && $user_id == $data['Wiki']['user_id']) {
		// $data = $wiki->find('first', ['conditions' => ['Wiki.project_id' => $project_id]]);
		// }
		// / if (isset($data['Wiki']['user_id']) && $user_id != $data['Wiki']['user_id']) {
		// $data = $wiki->find('first', ['conditions' => ['Wiki.project_id' => $project_id, 'Wiki.status' => 1]]);
		// }
		return $data;
	}

	

	public function _displayDate($date = null, $format = 'd M, Y g:i A') {
		//die("again gus gaye");
		// date must be pass in formate of 'Y-m-d h:i:s A'....

		$admin = $this->Session->read("Auth.Admin.User.id");
		if (isset($admin)) {
			$timezone = ClassRegistry::init('Timezone')->findByUserId($this->Session->read("Auth.Admin.User.id"));
		} else {
			$timezone = ClassRegistry::init('Timezone')->findByUserId($this->Session->read("Auth.User.id"));
		}

		//echo $this->Session->read ( "Auth.Admin.User.id" ); die;
		
		if((!isset($timezone['Timezone']['name']) || empty($timezone['Timezone']['name'])) || ($timezone['Timezone']['name'] ==  'Etc/Unknown')){
			$timezone['Timezone']['name'] = 'Europe/London';
		}
		
		$target_time_zone = new DateTimeZone($timezone['Timezone']['name']);

		$kolkata_date_time = new DateTime('now', $target_time_zone);
		$time = $kolkata_date_time->format('P');

		/*------------------------*/

		 $datetime = new DateTime($date);
		//echo $datetime->format('Y-m-d H:i:s') . "\n";
		//$la_time = new DateTimeZone('America/Los_Angeles');
		
		//$datetime->setTimezone($target_time_zone);
		//$date = $datetime->format('Y-m-d H:i:s');

		$date = date($format, strtotime($date));

		//die;

		/*------------------------*/

		//echo $date;
		if( PHP_VERSIONS == 5 ){
			date_default_timezone_set ( 'UTC' );
		}else{
			date_default_timezone_set("Europe/London");
			
		}	
 	
		
		if( LOCALIP == $_SERVER['SERVER_ADDR'] ){
			date_default_timezone_set("Asia/Kolkata");
		} else {	
			date_default_timezone_set("Europe/London");
		}

		if (empty($date)) {
			return;
		}

		return $date;
	}
	
	public function _displayDateByUser($date = null, $format = 'd M, Y g:i A',$user_id =null) {
		//die("again gus gaye");
		// date must be pass in formate of 'Y-m-d h:i:s A'....

		$admin = $this->Session->read("Auth.Admin.User.id");
		if (isset($admin)) {
			$timezone = ClassRegistry::init('Timezone')->findByUserId($user_id);
		} else {
			$timezone = ClassRegistry::init('Timezone')->findByUserId($user_id);
		}

		//echo $this->Session->read ( "Auth.Admin.User.id" ); die;
		
		if((!isset($timezone['Timezone']['name']) || empty($timezone['Timezone']['name'])) || ($timezone['Timezone']['name'] ==  'Etc/Unknown')){
			$timezone['Timezone']['name'] = 'Europe/London';
		}
		
		$target_time_zone = new DateTimeZone($timezone['Timezone']['name']);

		$kolkata_date_time = new DateTime('now', $target_time_zone);
		$time = $kolkata_date_time->format('P');

		/*------------------------*/

		$datetime = new DateTime($date);
		//echo $datetime->format('Y-m-d H:i:s') . "\n";
		//$la_time = new DateTimeZone('America/Los_Angeles');
		$datetime->setTimezone($target_time_zone);
		$date = $datetime->format('Y-m-d H:i:s');

		$date = date($format, strtotime($date));

		//die;

		/*------------------------*/

		//echo $date;
		if( PHP_VERSIONS == 5 ){
			date_default_timezone_set ( 'UTC' );
		}else{
			date_default_timezone_set("Europe/London");
			
		}	
		
		date_default_timezone_set("Europe/London");
		
		//date_default_timezone_set("Europe/London");

		if (empty($date)) {
			return;
		}

		return $date;
	}


	public function get_wiki_page_detail($wiki_page_id = null) {
		$data = ClassRegistry::init('WikiPage')->find('first', [
			'conditions' => [
				'WikiPage.id' => $wiki_page_id,
				'WikiPage.is_archived' => 1,
				'WikiPage.is_deleted !=' => 1,
				'WikiPage.is_linked' => 0,
			],
			"order" => [
				"WikiPage.sort_order ASC",
			],
		]);
		return $data;
	}
	public function get_wiki_detail($wiki_id = null) {
		$data = ClassRegistry::init('Wiki')->find('first', [
			'conditions' => [
				'Wiki.id' => $wiki_id,
			],
		]);
		return $data;
	}
	public function getCurrentWiki($project_id = null, $wiki_id = null) {
		$data = ClassRegistry::init('Wiki')->find('first', [
			'conditions' => [
				'Wiki.revision_id' => 0,
				'Wiki.wiki_step !=' => 0,
				'Wiki.project_id' => $project_id,
				'Wiki.status' => 1,
			],
		]);
		return $data;
	}
	public function getWikiPageUnapproved($project_id = null, $user_id = null, $wiki_id = null, $wiki_page_id = null) {
		$data['unapproved'] = null;
		$data['approved'] = null;
		$data['info'] = ['active' => 0, 'approved' => 0, 'unapproved' => 0, 'deleted' => 0];
		$unapproved = ClassRegistry::init('WikiPage')->find('count', [
			'conditions' => [
				'WikiPage.project_id' => $project_id,
				'WikiPage.wiki_id' => $wiki_id,
				'WikiPage.is_archived' => 0,
				'WikiPage.revision_id' => $wiki_page_id,
				'WikiPage.archieved_on IS NULL',
				'WikiPage.is_linked' => 0,
			],

		]);
		$approved = ClassRegistry::init('WikiPage')->find('first', [
			'conditions' => [
				'WikiPage.project_id' => $project_id,
				'WikiPage.wiki_id' => $wiki_id,
				'WikiPage.is_deleted !=' => 1,
				'WikiPage.is_archived' => array(
					1,
				),
				'WikiPage.revision_id' => $wiki_page_id,
				'WikiPage.archieved_on IS NOT NULL',
				'WikiPage.is_linked' => 0,
			],

		]);

		$active_count = ClassRegistry::init('WikiPage')->find('count', [
			'conditions' => [
				'WikiPage.project_id' => $project_id,
				'WikiPage.wiki_id' => $wiki_id,
				'WikiPage.is_deleted !=' => 1,
				'WikiPage.is_archived' => 1,
				'WikiPage.revision_id' => $wiki_page_id,
				'WikiPage.archieved_on IS NOT NULL',
				'WikiPage.is_linked' => 0,
			],

		]);

		$approved_count = ClassRegistry::init('WikiPage')->find('count', [
			'conditions' => [
				'WikiPage.project_id' => $project_id,
				'WikiPage.wiki_id' => $wiki_id,
				'WikiPage.is_archived !=' => 1,
				'WikiPage.is_deleted !=' => 1,
				'WikiPage.revision_id' => $wiki_page_id,
				'WikiPage.archieved_on IS NOT NULL',
				'WikiPage.is_linked' => 0,
			],

		]);

		$unapproved_count = ClassRegistry::init('WikiPage')->find('count', [
			'conditions' => [
				'WikiPage.project_id' => $project_id,
				'WikiPage.wiki_id' => $wiki_id,
				'WikiPage.is_archived' => 0,
				'WikiPage.is_deleted !=' => 1,
				'WikiPage.revision_id' => $wiki_page_id,
				'WikiPage.archieved_on IS NULL',
				'WikiPage.is_linked' => 0,
			],

		]);

		$deleted_count = ClassRegistry::init('WikiPage')->find('count', [
			'conditions' => [
				'WikiPage.project_id' => $project_id,
				'WikiPage.wiki_id' => $wiki_id,
				'WikiPage.is_deleted' => 1,
				'WikiPage.revision_id' => $wiki_page_id,
				'WikiPage.is_linked' => 0,
			],

		]);

		$deleted_count_self = ClassRegistry::init('WikiPage')->find('count', [
			'conditions' => [
				'WikiPage.project_id' => $project_id,
				'WikiPage.wiki_id' => $wiki_id,
				'WikiPage.is_deleted' => 1,
				'WikiPage.id' => $wiki_page_id,
				'WikiPage.is_linked' => 0,
			],

		]);

		$deleted_count = $deleted_count + $deleted_count_self;

		$data['unapproved'] = $unapproved;
		$data['approved'] = $approved;
		$data['info']['active'] = $active_count;
		$data['info']['approved'] = $approved_count;
		$data['info']['unapproved'] = $unapproved_count;
		$data['info']['deleted'] = $deleted_count;
		return $data;
	}
	public function getLatestWikiPage($project_id = null, $user_id = null, $wiki_id = null) {
		$data = ClassRegistry::init('WikiPage')->find('all', [
			'conditions' => [
				'WikiPage.project_id' => $project_id,
				'WikiPage.wiki_id' => $wiki_id,
				'WikiPage.is_archived' => 1,
				'WikiPage.is_deleted !=' => 1,
				'WikiPage.is_linked' => 0,
			],
			"limit" => "0",
			"order" => [
				"WikiPage.sort_order ASC",
			],
		]);
		return $data;
	}
	public function getWikiPageListsParent($project_id = null, $user_id = null, $wiki_id = null) {
		$data = ClassRegistry::init('WikiPage')->find('all', [
			'conditions' => [
				'WikiPage.project_id' => $project_id,
				'WikiPage.wiki_id' => $wiki_id,
				'WikiPage.revision_id' => 0,
				// 'WikiPage.is_deleted !=' => 1,
				'WikiPage.is_linked' => 0,
			],
			"order" => [
				"WikiPage.id DESC",
			],
		]);

		return $data;
	}
	public function getWikiPageLists($project_id = null, $user_id = null, $wiki_id = null) {
		$data = ClassRegistry::init('WikiPage')->find('all', [
			'conditions' => [
				'WikiPage.project_id' => $project_id,
				'WikiPage.wiki_id' => $wiki_id,
				'WikiPage.is_archived' => 1,
				'WikiPage.is_deleted !=' => 1,
				'WikiPage.is_linked' => 0,
			],
			"order" => [
				"WikiPage.sort_order ASC",
			],
		]);
		return $data;
	}
	public function getWikiPageHistoryLists($project_id = null, $user_id = null, $wiki_id = null, $wiki_page_id = null) {
		$data = ClassRegistry::init('WikiPage')->find('all', [
			'conditions' => [
				'WikiPage.project_id' => $project_id,
				'WikiPage.wiki_id' => $wiki_id,
				'WikiPage.is_archived' => array(
					0,
					1,
				),
				'WikiPage.revision_id' => $wiki_page_id,
				// 'WikiPage.is_deleted !=' => 1,
				'WikiPage.is_linked' => 0,
			],
			"order" => [
				"WikiPage.id DESC",
			],
		]);
		return $data;
	}
	public function getWikiHistoryLists($project_id = null, $user_id = null, $wiki_id = null) {
		$data = ClassRegistry::init('Wiki')->find('all', [
			'conditions' => [
				'Wiki.project_id' => $project_id,
				'Wiki.wiki_step !=' => 0,
			],
			"order" => [
				"Wiki.wiki_step desc",
			],
		]);
		return $data;
	}
	public function getMainWikiHistory($project_id = null, $user_id = null, $wiki_id = null) {
		$data = ClassRegistry::init('Wiki')->find('first', [
			'conditions' => [
				'Wiki.project_id' => $project_id,
				'Wiki.wiki_step' => 0,
			],
		]);
		return $data;
	}
	public function getWikiPageViewUsers($project_id = null, $user_id = null, $wiki_id = null, $wiki_page_id = null) {
		$data = ClassRegistry::init('WikiPageView')->find('all', [
			'conditions' => [
				'WikiPageView.wiki_id' => $wiki_id,
				'WikiPageView.wiki_page_id' => $wiki_page_id,
			],
		]);
		return $data;
	}
	public function get_user_page_comment($project_id = null, $user_id = null, $wiki_id = null, $wiki_page_id = null) {
		$data = ClassRegistry::init('WikiPageComment')->find('all', [
			'conditions' => [
				'WikiPageComment.wiki_id' => $wiki_id,
				'WikiPageComment.wiki_page_id' => $wiki_page_id,
			],
		]);
		return $data;
	}
	public function getWikiAllUserLists($project_id = null, $user_id = null, $wiki_id = null, $wiki_page_id = null) {
		$conditions[] = [
			'WikiPageCommentDocument.wiki_id' => $wiki_id,
		];

		if (isset($wiki_page_id) && !empty($wiki_page_id)) {
			$conditions[] = [
				'WikiPageCommentDocument.wiki_page_id' => $wiki_page_id,
			];
		}

		$users = null;

		$wikiusers = ClassRegistry::init('Wiki')->find('all', [
			"fields" => [
				"Wiki.user_id",
			],
			'conditions' => array(
				"Wiki.id" => $wiki_id,
			),
		]);
		$wikirevusers = ClassRegistry::init('Wiki')->find('all', [
			"fields" => [
				"Wiki.updated_user_id",
			],
			'conditions' => array(
				"Wiki.revision_id" => $wiki_id,
			),
		]);
		$wikipage_conditions[] = [
			'WikiPage.project_id' => $project_id,
			'WikiPage.wiki_id' => $wiki_id,
			'WikiPage.is_archived' => 1,
			'WikiPage.is_deleted !=' => 1,
			'WikiPage.is_linked' => 0,
		];
		$wikipageusers = ClassRegistry::init('WikiPage')->find('all', [
			"fields" => [
				"WikiPage.user_id",
			],
			'conditions' => $wikipage_conditions,
		]);
		$wikipagecommentdocumentusers = ClassRegistry::init('WikiPageCommentDocument')->find('all', [
			"fields" => [
				"WikiPageCommentDocument.user_id",
			],
			'conditions' => $conditions,
			"order" => [
				"WikiPageCommentDocument.id DESC",
			],
			"group" => [
				"WikiPageCommentDocument.user_id",
			],
		]);

		$wikirequestusers = ClassRegistry::init('WikiUser')->find('all', [
			"fields" => [
				"WikiUser.user_id",
			],
			'conditions' => [
				"WikiUser.wiki_id" => $wiki_id,
				"WikiUser.approved" => 1,
			],
		]);
		if (isset($wikirequestusers) && !empty($wikirequestusers)) {
			foreach ($wikirequestusers as $wikirequser_id) {
				$users[] = $wikirequser_id['WikiUser']['user_id'];
			}
		}
		if (isset($wikiusers) && !empty($wikiusers)) {
			foreach ($wikiusers as $wikiuser_id) {
				$users[] = $wikiuser_id['Wiki']['user_id'];
			}
		}
		if (isset($wikirevusers) && !empty($wikirevusers)) {
			foreach ($wikirevusers as $wikiuser_id) {
				$users[] = $wikiuser_id['Wiki']['updated_user_id'];
			}
		}
		if (isset($wikipageusers) && !empty($wikipageusers)) {
			foreach ($wikipageusers as $wikiuser_id) {
				$users[] = $wikiuser_id['WikiPage']['user_id'];
			}
		}
		if (isset($wikipagecommentdocumentusers) && !empty($wikipagecommentdocumentusers)) {
			foreach ($wikipagecommentdocumentusers as $wikipagecommentdocumentuser_id) {
				$users[] = $wikipagecommentdocumentuser_id['WikiPageCommentDocument']['user_id'];
			}
		}
		if (isset($wikipagecommentdocumentusers) && !empty($wikipagecommentdocumentusers)) {
			$users = array_unique($users);
		}
		return (isset($users) && !empty($users)) ? array_unique($users) : array();
	}
	public function getWikiRequestStatus($project_id = null, $user_id = null, $wiki_id = null) {
		$wikiuser = ClassRegistry::init('WikiUser')->find("first", array(
			"conditions" => array(
				"WikiUser.user_id" => $user_id,
				"WikiUser.wiki_id" => $wiki_id,
			),
		));

		return (isset($wikiuser) && !empty($wikiuser)) ? $wikiuser['WikiUser']['approved'] : null;
	}
	public function getWikiHistoryAllUserLists($project_id = null, $user_id = null, $wiki_id = null, $wiki_page_id = null) {
		$conditions[] = [
			'WikiPage.project_id' => $project_id,
			'WikiPage.wiki_id' => $wiki_id,
			'WikiPage.is_archived' => 0,
			'WikiPage.is_deleted !=' => 1,
			'WikiPage.is_linked' => 0,
		];

		if (isset($wiki_page_id) && !empty($wiki_page_id)) {
			$conditions[] = [
				'WikiPage.id' => $wiki_page_id,
			];
		}

		$users = null;

		$wikiusers = ClassRegistry::init('Wiki')->find('all', [
			"fields" => [
				"Wiki.user_id",
			],
			'conditions' => array(
				"Wiki.id" => $wiki_id,
			),
		]);
		$wikirevisionusers = ClassRegistry::init('Wiki')->find('all', [
			"fields" => [
				"Wiki.updated_user_id",
			],
			'conditions' => array(
				"Wiki.revision_id" => $wiki_id,
			),
		]);
		$wikipageusers = ClassRegistry::init('WikiPage')->find('all', [
			"fields" => [
				"WikiPage.user_id",
			],
			'conditions' => $conditions,
			"order" => [
				"WikiPage.id DESC",
			],
		]);
		$wikipageupdatedusers = ClassRegistry::init('WikiPage')->find('all', [
			"fields" => [
				"WikiPage.updated_user_id",
			],
			'conditions' => $conditions,
			"order" => [
				"WikiPage.id DESC",
			],
		]);
		if (isset($wikiusers) && !empty($wikiusers)) {
			foreach ($wikiusers as $wikiuser_id) {
				// $users[] = $wikiuser_id['Wiki']['user_id'];
			}
		}
		if (isset($wikirevisionusers) && !empty($wikirevisionusers)) {
			foreach ($wikirevisionusers as $wikirevuser_id) {
				// $users[] = $wikirevuser_id['Wiki']['updated_user_id'];
			}
		}

		if (isset($wikipageusers) && !empty($wikipageusers)) {
			foreach ($wikipageusers as $wikipageuser_id) {
				// $users[] = $wikipageuser_id['WikiPage']['user_id'];
			}
		}
		if (isset($wikipageupdatedusers) && !empty($wikipageupdatedusers)) {
			foreach ($wikipageupdatedusers as $wikipageupdateduser_id) {
				$users[] = $wikipageupdateduser_id['WikiPage']['updated_user_id'];
			}
		}
		if (isset($users) && !empty($users)) {
			$users = array_unique($users);
		}
		return $users;
	}
	public function getWikiDocumentAllUserLists($project_id = null, $user_id = null, $wiki_id = null, $wiki_page_id = null) {
		$conditions[] = [
			'WikiPageCommentDocument.wiki_id' => $wiki_id,
		];
		if (isset($wiki_page_id) && !empty($wiki_page_id)) {
			$conditions[] = [
				'WikiPageCommentDocument.wiki_page_id' => $wiki_page_id,
			];
		}

		$users = null;

		$wikiusers = ClassRegistry::init('Wiki')->find('all', [
			"fields" => [
				"Wiki.user_id",
			],
			'conditions' => array(
				"Wiki.id" => $wiki_id,
			),
		]);
		$wikipagecommentdocumentusers = ClassRegistry::init('WikiPageCommentDocument')->find('all', [
			"fields" => [
				"WikiPageCommentDocument.user_id",
			],
			'conditions' => $conditions,
			"order" => [
				"WikiPageCommentDocument.id DESC",
			],
			"group" => [
				"WikiPageCommentDocument.user_id",
			],
		]);
		if (isset($wikiusers) && !empty($wikiusers)) {
			foreach ($wikiusers as $wikiuser_id) {
				$users[] = $wikiuser_id['Wiki']['user_id'];
			}
		}
		if (isset($wikipagecommentdocumentusers) && !empty($wikipagecommentdocumentusers)) {
			foreach ($wikipagecommentdocumentusers as $wikipagecommentdocumentuser_id) {
				$users[] = $wikipagecommentdocumentuser_id['WikiPageCommentDocument']['user_id'];
			}
		}
		if (isset($wikipagecommentdocumentusers) && !empty($wikipagecommentdocumentusers)) {
			$users = array_unique($users);
		}
		return $users;
	}
	public function getWikiDashboardAllUserLists($project_id = null, $user_id = null, $wiki_id = null, $wiki_page_id = null) {
		$conditions[] = [
			'WikiPageCommentDocument.wiki_id' => $wiki_id,
		];

		if (isset($wiki_page_id) && !empty($wiki_page_id)) {
			$conditions[] = [
				'WikiPageCommentDocument.wiki_page_id' => $wiki_page_id,
			];
		}

		$users = null;

		$wikiusers = ClassRegistry::init('Wiki')->find('all', [
			"fields" => [
				"Wiki.user_id",
			],
			'conditions' => array(
				"Wiki.id" => $wiki_id,
			),
		]);
		$wikirevusers = ClassRegistry::init('Wiki')->find('all', [
			"fields" => [
				"Wiki.updated_user_id",
			],
			'conditions' => array(
				"Wiki.revision_id" => $wiki_id,
			),
		]);
		$wikipage_conditions[] = [
			'WikiPage.project_id' => $project_id,
			'WikiPage.wiki_id' => $wiki_id,
			'WikiPage.is_archived' => 1,
			'WikiPage.is_deleted !=' => 1,
			'WikiPage.is_linked' => 0,
		];
		$wikipageusers = ClassRegistry::init('WikiPage')->find('all', [
			"fields" => [
				"WikiPage.user_id",
			],
			'conditions' => $wikipage_conditions,
		]);
		$wikipagecommentdocumentusers = ClassRegistry::init('WikiPageCommentDocument')->find('all', [
			"fields" => [
				"WikiPageCommentDocument.user_id",
			],
			'conditions' => $conditions,
			"order" => [
				"WikiPageCommentDocument.id DESC",
			],
			"group" => [
				"WikiPageCommentDocument.user_id",
			],
		]);
		if (isset($wikiusers) && !empty($wikiusers)) {
			foreach ($wikiusers as $wikiuser_id) {
				$users[] = $wikiuser_id['Wiki']['user_id'];
			}
		}
		if (isset($wikirevusers) && !empty($wikirevusers)) {
			foreach ($wikirevusers as $wikiuser_id) {
				$users[] = $wikiuser_id['Wiki']['updated_user_id'];
			}
		}
		if (isset($wikipageusers) && !empty($wikipageusers)) {
			foreach ($wikipageusers as $wikiuser_id) {
				$users[] = $wikiuser_id['WikiPage']['user_id'];
			}
		}
		if (isset($wikipagecommentdocumentusers) && !empty($wikipagecommentdocumentusers)) {
			foreach ($wikipagecommentdocumentusers as $wikipagecommentdocumentuser_id) {
				$users[] = $wikipagecommentdocumentuser_id['WikiPageCommentDocument']['user_id'];
			}
		}
		if (isset($wikipagecommentdocumentusers) && !empty($wikipagecommentdocumentusers)) {
			$users = array_unique($users);
		}
		return (isset($users) && !empty($users)) ? array_unique($users) : array();
	}
	public function userTotalWikiPage($project_id = null, $user_id = null, $wiki_id = null) {
		$data = ClassRegistry::init('WikiPage')->find('count', [
			"fields" => [
				"WikiPage.id",
			],
			'conditions' => [
				'WikiPage.project_id' => $project_id,
				'WikiPage.wiki_id' => $wiki_id,
				"WikiPage.user_id" => $user_id,
				'WikiPage.is_archived' => 1,
				'WikiPage.is_deleted !=' => 1,
				'WikiPage.is_linked' => 0,
			],
			"order" => [
				"WikiPage.sort_order ASC",
			],
		]);

		return $data;
	}
	public function userTotalWikiPageHistory($project_id = null, $user_id = null, $wiki_id = null) {
		$data = ClassRegistry::init('WikiPage')->find('count', [
			"fields" => [
				"WikiPage.id",
			],
			'conditions' => [
				'WikiPage.project_id' => $project_id,
				'WikiPage.wiki_id' => $wiki_id,
				"WikiPage.updated_user_id" => $user_id,
				'WikiPage.is_archived' => 0,
				'WikiPage.is_deleted !=' => 1,
				'WikiPage.is_linked' => 0,
			],
		]);

		return $data;
	}
	public function userTotalWikiDocument($project_id = null, $user_id = null, $wiki_id = null) {
		$data = ClassRegistry::init('WikiPageCommentDocument')->find('count', [
			"fields" => [
				"WikiPageCommentDocument.id",
			],
			'conditions' => [
				// 'WikiPageCommentDocument.project_id'=>$project_id,
				'WikiPageCommentDocument.wiki_id' => $wiki_id,
				"WikiPageCommentDocument.user_id" => $user_id,
			],
		]);

		return $data;
	}
	public function userTotalWikiComment($project_id = null, $user_id = null, $wiki_id = null, $wiki_page_id = null) {
		$pageids = ClassRegistry::init('WikiPage')->find('list', [
			"fields" => "WikiPage.id",
			'conditions' => [
				'WikiPage.project_id' => $project_id,
				'WikiPage.wiki_id' => $wiki_id,
				'WikiPage.is_archived' => 1,
				'WikiPage.is_deleted !=' => 1,
				'WikiPage.is_linked' => 0,
			],
			"order" => [
				"WikiPage.sort_order ASC",
			],
		]);

		if (isset($wiki_page_id) && !empty($wiki_page_id)) {
			$conditions = [
				'WikiPageComment.wiki_page_id' => $wiki_page_id,
			];
		} else {
			$conditions = [
				'WikiPageComment.wiki_page_id' => $pageids,
			];
		}
		$data = ClassRegistry::init('WikiPageComment')->find('count', [
			"fields" => [
				"WikiPageComment.id",
			],
			'conditions' => $conditions,
		]);

		return $data;
	}
	public function getAllUserOfComment($project_id = null, $user_id = null, $wiki_id = null, $wiki_page_id = null) {
		$pageids = ClassRegistry::init('WikiPage')->find('list', [
			"fields" => "WikiPage.id",
			'conditions' => [
				'WikiPage.project_id' => $project_id,
				'WikiPage.wiki_id' => $wiki_id,
				'WikiPage.is_archived' => 1,
				'WikiPage.is_deleted !=' => 1,
				'WikiPage.is_linked' => 0,
			],
			"order" => [
				"WikiPage.sort_order ASC",
			],
		]);

		if (isset($wiki_page_id) && !empty($wiki_page_id)) {
			$conditions = [
				'WikiPageComment.wiki_page_id' => $wiki_page_id,
			];
		} else {
			$conditions = [
				'WikiPageComment.wiki_page_id' => $pageids,
			];
		}
		$data = ClassRegistry::init('WikiPageComment')->find('count', [
			"fields" => [
				"WikiPageComment.user_id",
			],
			'conditions' => $conditions,
			"order" => [
				"WikiPageComment.user_id",
			],
		]);

		return $data;
	}
	public function wiki_page_likes($wiki_page_id = null) {
		$_wiki_page = ClassRegistry::init("WikiPageLike");
		$data = $_wiki_page->find('count', [
			'conditions' => [
				'WikiPageLike.wiki_page_id' => $wiki_page_id,
			],
		]);

		return (isset($data) && !empty($data)) ? $data : false;
	}
	public function wiki_page_like_posted($user_id = null, $wiki_page_id = null) {
		$_wiki_page = ClassRegistry::init("WikiPageLike");
		$data = false;

		if ((!isset($user_id) || !empty($user_id)) && (!isset($wiki_page_id) || !empty($wiki_page_id))) {
			if ($_wiki_page->hasAny([
				'WikiPageLike.user_id' => $user_id,
				'WikiPageLike.wiki_page_id' => $wiki_page_id,
			])) {
				$data = true;
			}
		}
		return $data;
	}
	public function wiki_page_comment_likes($wiki_page_comment_id = null) {
		$_wiki_page_comment = ClassRegistry::init("WikiPageCommentLike");
		$data = $_wiki_page_comment->find('count', [
			'conditions' => [
				'WikiPageCommentLike.wiki_page_comment_id' => $wiki_page_comment_id,
			],
		]);

		return (isset($data) && !empty($data)) ? $data : false;
	}
	public function all_wiki_page_comment_likes($project_id = null, $wiki_id = null, $wiki_paget_id = null) {
		// $pageids = ClassRegistry::init('WikiPage')->find('list', ["fields"=>"WikiPage.id",'conditions' => ['WikiPage.project_id' => $project_id, 'WikiPage.wiki_id' => $wiki_id, 'WikiPage.is_archived' => 1, 'WikiPage.is_deleted !=' => 1, 'WikiPage.is_linked' => 0], "order" => ["WikiPage.id DESC"]]);
		$commentids = ClassRegistry::init('WikiPageComment')->find('list', [
			"fields" => "WikiPageComment.id",
			'conditions' => [
				'WikiPageComment.wiki_page_id' => $wiki_paget_id,
			],
		]);

		$_wiki_page_comment = ClassRegistry::init("WikiPageCommentLike");
		$data = $_wiki_page_comment->find('count', [
			'conditions' => [
				'WikiPageCommentLike.wiki_page_comment_id' => $commentids,
			],
		]);

		return (isset($data) && !empty($data)) ? $data : false;
	}
	public function wiki_page_comment_like_posted($user_id = null, $wiki_page_comment_id = null) {
		$_wiki_page_comment = ClassRegistry::init("WikiPageCommentLike");
		$data = false;

		if ((!isset($user_id) || !empty($user_id)) && (!isset($wiki_page_comment_id) || !empty($wiki_page_comment_id))) {
			if ($_wiki_page_comment->hasAny([
				'WikiPageCommentLike.user_id' => $user_id,
				'WikiPageCommentLike.wiki_page_comment_id' => $wiki_page_comment_id,
			])) {
				$data = true;
			}
		}
		return $data;
	}
	public function is_requested_user_is_approved($user_id = null, $wiki_id = null) {
		$data = ClassRegistry::init('WikiUser')->find('count', [
			"fields" => [
				"WikiUser.id",
			],
			'conditions' => [
				'WikiUser.wiki_id' => $wiki_id,
				'WikiUser.user_id' => $user_id,
				"WikiUser.approved" => 1,
			],
		]);

		return $data;
	}
	public function get_all_wiki_users($wiki_id = null) {
		$data = ClassRegistry::init('WikiPageComment')->find('list', [
			"fields" => [
				"WikiPageComment.user_id",
			],
			'conditions' => [
				'WikiPageComment.wiki_id' => $wiki_id,
			],
			'group' => 'WikiPageComment.user_id',
		]);
		$userArray = array();
		if (isset($data) && !empty($data)) {
			foreach ($data as $u_k => $u_v) {
				$userArray[$u_v] = $this->Common->userFullname($u_v);
			}
		}

		return $userArray;
	}
	public function get_all_wiki_user_admin($wiki_id = null) {
		$users = array();
		$wikiuser = ClassRegistry::init('Wiki')->find('list', [
			"fields" => [
				"Wiki.user_id",
			],
			'conditions' => [
				'Wiki.id' => $wiki_id,
			],
		]);
		$wikipageuser = ClassRegistry::init('WikiPage')->find('list', [
			"fields" => [
				"WikiPage.user_id",
			],
			'conditions' => [
				'WikiPage.wiki_id' => $wiki_id,
			],
			'group' => 'WikiPage.user_id',
		]);
		$wikipagecommentuser = ClassRegistry::init('WikiPageComment')->find('list', [
			"fields" => [
				"WikiPageComment.user_id",
			],
			'conditions' => [
				'WikiPageComment.wiki_id' => $wiki_id,
			],
			'group' => 'WikiPageComment.user_id',
		]);
		$wikipagecommentdocumentuser = ClassRegistry::init('WikiPageCommentDocument')->find('list', [
			"fields" => [
				"WikiPageCommentDocument.user_id",
			],
			'conditions' => [
				'WikiPageCommentDocument.wiki_id' => $wiki_id,
			],
			'group' => 'WikiPageCommentDocument.user_id',
		]);
		$users = $wikiuser;
		if (isset($wikipageuser) && !empty($wikipageuser)) {
			$users = array_merge($users, $wikipageuser);
		}
		if (isset($wikipagecommentuser) && !empty($wikipagecommentuser)) {
			$users = array_merge($users, $wikipagecommentuser);
		}
		if (isset($wikipagecommentdocumentuser) && !empty($wikipagecommentdocumentuser)) {
			$users = array_merge($users, $wikipagecommentdocumentuser);
		}
		$userArray = array();
		if (isset($users) && !empty($users)) {
			foreach ($users as $u_k => $u_v) {
				$userArray[$u_v] = $this->Common->userFullname($u_v);
			}
		}
		// pr($userArray);
		return $userArray;
	}
	public function get_wiki_page_public_document($project_id = null, $user_id = null, $wiki_id = null) {
		$data = ClassRegistry::init('WikiPageCommentDocument')->find('all', [
			'conditions' => [
				'WikiPageCommentDocument.wiki_id' => $wiki_id,
				'WikiPage.is_archived' => 1,
				'WikiPage.is_deleted !=' => 1,
				'WikiPage.is_linked' => 0,
			],
			'order' => 'WikiPageCommentDocument.id DESC',
		]);

		return $data;
	}
	public function get_wiki_document_count($wiki_id = null) {
		$data = ClassRegistry::init('WikiPageCommentDocument')->find('count', [
			'conditions' => [
				'WikiPageCommentDocument.wiki_id' => $wiki_id,
			],
		]);

		return $data;
	}
	public function get_wiki_page_views($project_id = null, $user_id = null, $wiki_id = null, $wiki_page_id) {
		$pageids = ClassRegistry::init('WikiPage')->find('list', [
			"fields" => "WikiPage.id",
			'conditions' => [
				'WikiPage.project_id' => $project_id,
				'WikiPage.wiki_id' => $wiki_id,
				'WikiPage.is_archived' => 1,
				'WikiPage.is_deleted !=' => 1,
				'WikiPage.is_linked' => 0,
			],
			"order" => [
				"WikiPage.sort_order ASC",
			],
		]);

		if (isset($wiki_page_id) && !empty($wiki_page_id)) {
			$conditions = [
				'WikiPageView.wiki_page_id' => $wiki_page_id,
			];
		} else {
			$conditions = [
				'WikiPageView.wiki_page_id' => $pageids,
			];
		}
		$data = ClassRegistry::init('WikiPageView')->find('first', [
			'conditions' => $conditions,
			"fields" => array(
				"WikiPageView.wiki_page_id",
				"sum(WikiPageView.views) as views",
			),
		]);
		return $data;
	}
	public function check_permission($project_id = null, $user_id = null, $forWiki = null) {
		$p_permission = $this->Common->project_permission_details($project_id, $user_id);
		$user_project = $this->Common->userproject($project_id, $user_id);
		$gp_exists = $this->Group->GroupIDbyUserID($project_id, $user_id);
		if (isset($gp_exists) && !empty($gp_exists)) {
			$p_permission = $this->Group->group_permission_details($project_id, $gp_exists);
		}
		$is_requested_user = false;
		$project_wiki = $this->getProjectWiki($project_id, $user_id);

		if ((isset($project_wiki['Wiki']['id']) && !empty($project_wiki['Wiki']['id'])) && (isset($forWiki) && $forWiki == 1)) {
			$is_requested_user = $this->is_requested_user_is_approved($user_id, $project_wiki['Wiki']['id']);
		}

		$is_full_permission_to_current_login = false;
		if (((isset($user_project)) && (!empty($user_project))) || (isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level'] == 1)) {
			$is_full_permission_to_current_login = true;
		} else if (isset($is_requested_user) && !empty($is_requested_user) && $is_requested_user == 1) {

			$is_full_permission_to_current_login = true;
		} else if (isset($project_wiki['Wiki']['wtype']) && !empty($project_wiki['Wiki']['wtype']) && $project_wiki['Wiki']['wtype'] == 0) {

			$is_full_permission_to_current_login = true;
		}
		return $is_full_permission_to_current_login;
	}
}
