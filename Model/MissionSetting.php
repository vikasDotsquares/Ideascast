<?php

App::uses('AppModel', 'Model');

/**
 * MissionSetting Model
 *
 */ 

class MissionSetting extends AppModel {
    
	public $belongsTo = array( 'User' );
	  
    public $validate = array();
	
    public $default_setting = [];
	
	public function bucketSettings($user_id = null) {
			
		if( !isset($user_id) || empty($user_id) ) return false;
			
		$this->default_setting = [
			[
				'MissionSetting' => [
					'user_id' => $user_id,
					'slug' => 'links',
					'title' => 'Links',
					'sort_order' => 1
				]
			],
			[
				'MissionSetting' => [
					'user_id' => $user_id,
					'slug' => 'notes',
					'title' => 'Notes',
					'sort_order' => 2
				]
			],
			[
				'MissionSetting' => [
					'user_id' => $user_id,
					'slug' => 'documents',
					'title' => 'Documents',
					'sort_order' => 3
				]
			],
			[
				'MissionSetting' => [
					'user_id' => $user_id,
					'slug' => 'decisions',
					'title' => 'Decisions',
					'sort_order' => 4
				]
			],
			[
				'MissionSetting' => [
					'user_id' => $user_id,
					'slug' => 'feedbacks',
					'title' => 'Feedbacks',
					'sort_order' => 5
				]
			],
			[
				'MissionSetting' => [
					'user_id' => $user_id,
					'slug' => 'votes',
					'title' => 'Votes',
					'sort_order' => 6
				]
			],
			[
				'MissionSetting' => [
					'user_id' => $user_id,
					'slug' => 'mindmaps',
					'title' => 'Mind Maps',
					'sort_order' => 7
				]
			],
		];
			
		return $this->default_setting;	
	}
	
}
