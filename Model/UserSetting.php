<?php

App::uses('AppModel', 'Model');

/**
 * UserSetting Model
 *
 */ 

class UserSetting extends AppModel {
    
	public $belongsTo = array( 'User' );
	  
    public $validate = array();
	
    public $default_setting = [];
		
	public function setSettings($user_id = null) {
			
		if( !isset($user_id) || empty($user_id) ) return false;
			
		$this->default_setting = [
			[
				'UserSetting' => [
					'user_id' => $user_id,
					'slug' => 'projects',
					'title' => 'Projects',
					'is_closed' => 1,
					'color_code' => 'box-default',
				]
			],
			[
				'UserSetting' => [
					'user_id' => $user_id,
					'slug' => 'shared_projects',
					'title' => 'Shared Projects',
					'is_closed' => 1,
					'color_code' => 'box-default',
				]
			],
			[
				'UserSetting' => [
					'user_id' => $user_id,
					'slug' => 'received_projects',
					'title' => 'Received Projects',
					'is_closed' => 1,
					'color_code' => 'box-default',
				]
			],
			[
				'UserSetting' => [
					'user_id' => $user_id,
					'slug' => 'group_received_projects',
					'title' => 'Group Shared Projects',
					'is_closed' => 1,
					'color_code' => 'box-default',
				]
			],
			[
				'UserSetting' => [
					'user_id' => $user_id,
					'slug' => 'propagated_projects',
					'title' => 'Propagated Projects',
					'is_closed' => 1,
					'color_code' => 'box-default',
				]
			],
		];
			
		return $this->default_setting;	
	}
	
}
