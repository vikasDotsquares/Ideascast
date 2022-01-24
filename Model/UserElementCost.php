<?php
App::uses('AppModel', 'Model');

class UserElementCost extends AppModel {

    var $name = 'UserElementCost';

    var $belongsTo = array (
			'User' => ['dependent' => true ],
			'ElementCost' => ['dependent' => true ],
		);

}