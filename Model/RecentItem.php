<?php

App::uses('AppModel', 'Model');
App::uses('Controller/Component', 'Auth');
App::uses('Sanitize', 'Utility');

/**
 * Project Model
 *
 */
class RecentItem extends AppModel {
	var $name = 'RecentItem';
	// public $belongsTo = array( 'Project', 'Workspace', 'Element' );
}
