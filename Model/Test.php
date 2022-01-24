<?php

App::uses('AppModel', 'Model');


class Test extends AppModel {
	public $useTable = 'users'; // This model uses a database table 'exmp'
    var $name = 'test';
    var $primaryKey = 'id';
	var $useDbConfig = 'mongodb'; // setup the mongodb datasource

      // Now is model is connected with mongoDB only....
}

?>