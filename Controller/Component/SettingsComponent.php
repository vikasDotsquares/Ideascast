<?php
App::import('Lib', 'XmlApi');
/**
 * Component for working common.
 */
class SettingsComponent extends Component {

	public $components = array('Session', 'Email', 'Paginator', 'Auth');


	public function updateTimezoneUser($Cname,$offset){

		/* 'name' => $this->request->data['Cname'],
		'offset' => $this->request->data['offset'], */

		if( PHP_VERSIONS  == 5 ){

			$mongo = new MongoClient(MONGO_CONNECT);
			$this->mongoDB = $mongo->selectDB(MONGO_DATABASE);
			$mongo_collection = new MongoCollection($this->mongoDB, 'users');

			$mongo_collection->update(
				[
					'id' => intval($this->Session->read("Auth.User.id")),
				],
				[
					'$set' =>
					[
						'timezone' => [
							'name' => $Cname,
							'offset' => $offset,
						],
					],
				]
			);

		} else {
			$mongo = new MongoDB\Driver\Manager(MONGO_CONNECT);
			$bulk = new MongoDB\Driver\BulkWrite;

			$bulk->update(
				[
					'id' => intval($this->Session->read("Auth.User.id")),
				],
				[
					'$set' =>
					[
						'timezone' => [
							'name' => $Cname,
							'offset' => $offset,
						],
					],
				]
			);
			$mongo->executeBulkWrite(MONGO_DATABASE.'.users', $bulk);

		}

	}


}
