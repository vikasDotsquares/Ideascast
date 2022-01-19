<?php
App::uses('AppModel', 'Model');

class MindmapHistory extends AppModel {
	public $useTable = 'mindmap_history'; // This model does not use a database table
    var $name = 'MindmapHistory';
	
    var $belongsTo = array (
			'ElementMindmap' => [ 'dependent' => true ]
		);
		 
}