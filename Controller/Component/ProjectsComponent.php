<?php
/**
 * Component for working common.
 */
class ProjectsComponent extends Component {

	public $components = array('Session', 'Email', 'Paginator', 'Auth');

	public function manageProjectUpdate($project_id,$project_title){

		if( PHP_VERSIONS  == 5 ){

			$mongo = new MongoClient(MONGO_CONNECT);
			$this->mongoDB = $mongo->selectDB(MONGO_DATABASE);
			$mongo_collection = new MongoCollection($this->mongoDB, 'projects');

			$ret = $mongo_collection->update(['id' => intval($project_id, 10)], ['$set' => ['title' => strip_tags($project_title)]]);

		} else {

			$mongo = new MongoDB\Driver\Manager(MONGO_CONNECT);
			$bulk = new MongoDB\Driver\BulkWrite;

			$bulk->update(['id' => intval($project_id, 10)], ['$set' => ['title' => strip_tags($project_title)]]);

			$mongo->executeBulkWrite(MONGO_DATABASE.'.projects', $bulk);

		}

	}

	public function manageProjectInsert($project_id, $project_title){

		if( PHP_VERSIONS == 5 ){

			$mongo = new MongoClient(MONGO_CONNECT);
			$this->mongoDB = $mongo->selectDB(MONGO_DATABASE);
			$mongo_collection = new MongoCollection($this->mongoDB, 'projects');
			$datetime = new MongoDate(strtotime(date('Y-m-d h:i:s')));

			$ret = $mongo_collection->save(['id' => intval($project_id, 10), 'title' => strip_tags($project_title), 'datetime' => $datetime ]);


		} else {

			$mongo = new MongoDB\Driver\Manager(MONGO_CONNECT);
			$bulk = new MongoDB\Driver\BulkWrite;
			$datetime = new MongoDB\BSON\UTCDateTime(strtotime(date('d/m/Y h:i:s A')) * 1000);

			// insert project
			$ret = $bulk->insert(['id' => intval($project_id, 10), 'title' => strip_tags($project_title), 'datetime' => $datetime ]);
			$mongo->executeBulkWrite(MONGO_DATABASE.'.projects', $bulk);

			// insert into user project connection
			$bulk1 = new MongoDB\Driver\BulkWrite;
			$ret = $bulk1->insert(['userId' => intval($this->Session->read('Auth.User.id'), 10), 'projectId' => intval($project_id, 10) ]);
			$mongo->executeBulkWrite(MONGO_DATABASE.'.userconnections', $bulk1);

		}

	}

	public function removeCollectionData($project_id){

			if (PHP_VERSIONS == 5) {

				$mongo = new MongoClient(MONGO_CONNECT);
				$this->mongoDB = $mongo->selectDB(MONGO_DATABASE);
				$mongo_collection = new MongoCollection($this->mongoDB, 'projects');
				$mongo_collection->remove(array('id' => (int) $project_id));

			} else {

				$mongo = new MongoDB\Driver\Manager(MONGO_CONNECT);
				$bulk = new MongoDB\Driver\BulkWrite;
				$qry = array("id" => $project_id);
				$ret = $bulk->delete(array('id' => (int) $project_id));
				$mongo->executeBulkWrite(MONGO_DATABASE.'.projects', $bulk);

			}

	}

}
