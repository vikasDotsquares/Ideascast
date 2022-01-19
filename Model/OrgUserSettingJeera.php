<?php

App::uses('AppModel', 'Model');

/**
 * UserDetail Model
 *
 */ 

class OrgUserSettingJeera extends AppModel {
	
	var $alias =  'OrgSetting' ;
	var $name = 'OrgUserSettingJeera';
	var $useDbConfig = 'jeera';
	public $useTable = 'org_settings';
	
}
