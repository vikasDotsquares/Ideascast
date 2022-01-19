<?php
App::import('Lib', 'XmlApi');
/***
 * Component
***/
class UsersComponent extends Component {

	public $components = array('Session', 'Email', 'Paginator', 'Auth');

	public function updateUserStatus($userId){


			$mongo = new MongoDB\Driver\Manager(MONGO_CONNECT);
			$bulk = new MongoDB\Driver\BulkWrite;

			$bulk->update([
				'id' => intval($userId, 10)
			], [
				'$set' =>
					[
						'status' => 'offline',
						'visibility' => 'offline',
					]

			]);
			$mongo->executeBulkWrite(MONGO_DATABASE.'.users', $bulk);


	}

	public function addUser($userId, $update = false){


		$sql = "SELECT u.id,u.email,u.password,u.my_theme,u.secondary_theme, CONCAT_WS(' ', ru.first_name, ru.last_name) AS reporting_user, ud.first_name as firstname,ud.last_name as lastname, ud.profile_pic as thumb,ud.department_id, ud.job_title, ud.job_role, ud.bio, ud.contact, ud.organization_id, ud.location_id, ud.reports_to_id,  ud.theme_name,  ud.tt_name, timezones.name as timezone_name, timezones.timezone as timezone_offset, dept.name as dept_name, org.name as org_name, loc.name as loc_name, countries.countryName as country_name, countries.countryCode as country_code
			FROM users as u
			LEFT JOIN user_details as ud ON u.id=ud.user_id
			LEFT JOIN timezones ON u.id=timezones.user_id
			LEFT JOIN departments dept ON dept.id = ud.department_id
			LEFT JOIN organizations org ON org.id = ud.organization_id
			LEFT JOIN locations loc ON loc.id = ud.location_id
			LEFT JOIN countries on countries.id = loc.country_id
			LEFT JOIN user_details ru on ud.reports_to_id = ru.user_id
			LEFT JOIN timezones tz on ud.user_id = tz.user_id
			WHERE u.id = " . $userId;
		$user_result = ClassRegistry::init('UserDetail')->query($sql);

		//pr($user_result);
		//pr($userId);

		$org_name = (isset($user_result[0]['org']['org_name']) && !empty($user_result[0]['org']['org_name'])) ? html_entity_decode(htmlentities($user_result[0]['org']['org_name'], ENT_QUOTES)) : '';
		$loc_name = (isset($user_result[0]['loc']['loc_name']) && !empty($user_result[0]['loc']['loc_name'])) ? html_entity_decode(htmlentities($user_result[0]['loc']['loc_name'], ENT_QUOTES)) : '';
		$country_name = (isset($user_result[0]['countries']['country_name']) && !empty($user_result[0]['countries']['country_name'])) ? $user_result[0]['countries']['country_name'] : '';
		$country_code = (isset($user_result[0]['countries']['country_code']) && !empty($user_result[0]['countries']['country_code'])) ? $user_result[0]['countries']['country_code'] : '';
		$offset = (isset($user_result[0]['timezones']['timezone_offset']) && !empty($user_result[0]['timezones']['timezone_offset'])) ? $user_result[0]['timezones']['timezone_offset'] : 'UTC +01:00';
		$timezone = (isset($user_result[0]['timezones']['timezone_name']) && !empty($user_result[0]['timezones']['timezone_name'])) ? $user_result[0]['timezones']['timezone_name'] : 'Europe/London';

			$mongo = new MongoDB\Driver\Manager(MONGO_CONNECT);
			$bulk = new MongoDB\Driver\BulkWrite;
			$datetime = new MongoDB\BSON\UTCDateTime(strtotime(date('d/m/Y h:i:s A')) * 1000);


			if($update) {
				// UPDATE USER DETAIL
				$bulk->update([
					'id' => intval($userId, 10)
				], [
					'$set' =>
						[
							'email' => strip_tags($user_result[0]['u']['email']),
							'password' => strip_tags($user_result[0]['u']['password']),
							'firstname' => strip_tags($user_result[0]['ud']['firstname']),
							'lastname' => strip_tags($user_result[0]['ud']['lastname']),
							'thumb' => strip_tags($user_result[0]['ud']['thumb']),
							'department' => strip_tags($user_result[0]['dept']['dept_name']),
							'jobTitle' => strip_tags($user_result[0]['ud']['job_title']),
							'my_theme' => strip_tags($user_result[0]['u']['my_theme']),
							'secondary_theme' => strip_tags($user_result[0]['u']['secondary_theme']),
							'theme_name' => strip_tags($user_result[0]['ud']['theme_name']),
							'tt_name' => strip_tags($user_result[0]['ud']['tt_name']),
							'phone' => $user_result[0]['ud']['contact'],
							'jobRole' => strip_tags($user_result[0]['ud']['job_role']),
							'skills' => '',
							'reportTo' => $user_result[0][0]['reporting_user'],
							'organization' => [
								'org_id' => $user_result[0]['ud']['organization_id'],
								'org_name' => $org_name,
								'loc_id' => $user_result[0]['ud']['location_id'],
								'loc_name' => $loc_name
							],
							'country' => array(
								'name' => $country_name,
								'code' => $country_code,
							),
							'timezone' => array(
								'name' => $timezone,
								'offset' => $offset,
							),
							'datetime' => $datetime,
						]

				]);
			}
			else {
				//INSERT USER DATA TO MONGO
				$bulk->insert(['id' => intval($userId, 10),
					'email' => strip_tags($user_result[0]['u']['email']),
					'password' => strip_tags($user_result[0]['u']['password']),
					'firstname' => strip_tags($user_result[0]['ud']['firstname']),
					'lastname' => strip_tags($user_result[0]['ud']['lastname']),
					'thumb' => strip_tags($user_result[0]['ud']['thumb']),
					'department' => strip_tags($user_result[0]['dept']['dept_name']),
					'is_mobile' => intval(0),
					'jobTitle' => strip_tags($user_result[0]['ud']['job_title']),
					'phone' => $user_result[0]['ud']['contact'],
					'jobRole' => strip_tags($user_result[0]['ud']['job_role']),
					'my_theme' => strip_tags($user_result[0]['u']['my_theme']),
					'secondary_theme' => strip_tags($user_result[0]['u']['secondary_theme']),
					'theme_name' => strip_tags($user_result[0]['ud']['theme_name']),
					'tt_name' => strip_tags($user_result[0]['ud']['tt_name']),
					'skills' => '',
					'timezone' => array(
						'name' => 'Europe/London',
						'offset' => 'UTC +01:00',
					),
					'contacts' => array(),
					'status' => 'offline', // dnd, offline, away....
					'visibility' => 'offline', // login/logout
					'session' => array(
						'datetime' => $datetime,
						'organisationId' => intval(1),
						'projectId' => intval(1),
						'projects' => array(),
					),
					'datetime' => $datetime,
					'reportTo' => $user_result[0][0]['reporting_user'],
					'organization' => [
						'org_id' => $user_result[0]['ud']['organization_id'],
						'org_name' => $org_name,
						'loc_id' => $user_result[0]['ud']['location_id'],
						'loc_name' => $loc_name
					],
					'country' => array(
						'name' => $country_name,
						'code' => $country_code,
					),
				]);
			}

			$mongo->executeBulkWrite(MONGO_DATABASE.'.users', $bulk);
			//pr($bulk, 1);

		// }
	}

	public function updateUserImage($userId, $user_result ){

		$mongo = new MongoDB\Driver\Manager(MONGO_CONNECT);
		$bulk = new MongoDB\Driver\BulkWrite;
		$datetime = new MongoDB\BSON\UTCDateTime(strtotime(date('d/m/Y h:i:s A')) * 1000);

		// UPDATE USER IMAGE
		$bulk->update([
			'id' => intval($userId, 10)
		],
		[
			'$set' =>
				[
					'thumb' => strip_tags($user_result[0]['ud']['thumb']),
					'datetime' => $datetime,
				]

		]);

		$mongo->executeBulkWrite(MONGO_DATABASE.'.users', $bulk);
	}

	public function userLoginUpdate($is_mobile){

		if( PHP_VERSIONS == 5 ){

			$mongo = new MongoClient(MONGO_CONNECT);
			$this->mongoDB = $mongo->selectDB(MONGO_DATABASE);

			// mongoDB update/insert
			$mongo_collection = new MongoCollection($this->mongoDB, 'users');
			$ret = $mongo_collection->update(
				[
					'id' => intval($this->Session->read('Auth.User.id'), 10),
				],
				[
					'$set' =>
					[
						'is_mobile' => $is_mobile,
					],
				]
			);

		} else {

			$mongo = new MongoDB\Driver\Manager(MONGO_CONNECT);
			$bulk = new MongoDB\Driver\BulkWrite;
			$bulk->update(
				[
					'id' => intval($this->Session->read('Auth.User.id'), 10),
				],
				[
					'$set' =>
					[
						'is_mobile' => $is_mobile,
						'visibility' => 'online',
						'status' => 'online',
					],
				]
			);
			$mongo->executeBulkWrite(MONGO_DATABASE.'.users', $bulk);

		}
	}

	public function userProjectDelete($id){

		if( PHP_VERSIONS == 5 ){

			$mongo = new MongoClient(MONGO_CONNECT);
			$this->mongoDB = $mongo->selectDB(MONGO_DATABASE);
			$mongo_collection = new MongoCollection($this->mongoDB, 'projects');
			$mongo_collection->remove(array('id' => (int) $id));

		} else {

			$mongo = new MongoDB\Driver\Manager(MONGO_CONNECT);

			// remove project from projects table
			$bulk = new MongoDB\Driver\BulkWrite;
			$qry = array("id" => $id);
			$ret = $bulk->delete(array('id' => (int) $id));
			$mongo->executeBulkWrite(MONGO_DATABASE.'.projects', $bulk);

			// delete from user project connection
			$bulk1 = new MongoDB\Driver\BulkWrite;
			$bulk1->delete(['projectId' => (int) $id]);
			$mongo->executeBulkWrite(MONGO_DATABASE.'.userconnections', $bulk1);

			// delete from message counter table
			$bulk2 = new MongoDB\Driver\BulkWrite;
			$bulk2->delete(['projectId' => (int) $id]);
			$mongo->executeBulkWrite(MONGO_DATABASE.'.messagecounters', $bulk2);

		}
	}

	public function userConnections($user_id, $project_id, $delete = false) {
		$mongo = new MongoDB\Driver\Manager(MONGO_CONNECT);
		if($delete) {
			// delete from user project connection
			$bulk = new MongoDB\Driver\BulkWrite;
			if(is_array($user_id)) {
				foreach ($user_id as $key => $user) {
					$bulk->delete(['userId' => (int) $user, 'projectId' => (int) $project_id]);
				}
			}
			else {
				$bulk->delete(['userId' => (int) $user_id, 'projectId' => (int) $project_id]);
			}

			$mongo->executeBulkWrite(MONGO_DATABASE.'.userconnections', $bulk);
		}
		else {
			// add into user project connection
			$bulk = new MongoDB\Driver\BulkWrite;

			if(is_array($user_id)) {
				foreach ($user_id as $key => $user) {
					$bulk->insert(['userId' => (int) $user, 'projectId' => (int) $project_id]);
				}
			}
			else {
				$bulk->insert(['userId' => (int) $user_id, 'projectId' => (int) $project_id]);
			}

			$mongo->executeBulkWrite(MONGO_DATABASE.'.userconnections', $bulk);
		}

	}


	public function user_is_loggedin($user_id = null) {
		if(!empty($user_id)){
			$mongo = new MongoDB\Driver\Manager(MONGO_CONNECT);

			$filter = [ 'id' => (int) $user_id ];
			$options = [
			   'projection' => ['_id' => 0],
			];

			$query = new MongoDB\Driver\Query($filter, $options);
			$cursor = $mongo->executeQuery(MONGO_DATABASE.'.users', $query);

			$isLogin = false;
			foreach ($cursor as $document) {
				$isLogin = ($document->status == 'online') ? true : false;
			}
			return $isLogin;
		}
	}

	public function user_status($user_id = null) {
		if(!empty($user_id)){
			$mongo = new MongoDB\Driver\Manager(MONGO_CONNECT);

			$filter = [ 'id' => (int) $user_id ];
			$options = [
			   'projection' => ['_id' => 0],
			];

			$query = new MongoDB\Driver\Query($filter, $options);
			$cursor = $mongo->executeQuery(MONGO_DATABASE.'.users', $query);

			$visibility = false;
			foreach ($cursor as $document) {
				$visibility = $document->visibility;
			}
			return $visibility;
		}
	}

	// CREATE A SESSION AND UPDATE IN MONGO
	public function UserToken($randomStr = null, $online = false){

		// UPDATE MONGO DB
		$online = ($online) ? 'online' : 'offline';
		$mongo = new MongoDB\Driver\Manager(MONGO_CONNECT);
		$bulk = new MongoDB\Driver\BulkWrite;
		$bulk->update(
			[
				'id' => intval($this->Session->read('Auth.User.id'), 10),
			],
			[
				'$set' =>
				[
					'userToken' => $randomStr,
					'visibility' => $online,
					'status' => $online,
				],
			]
		);
		$mongo->executeBulkWrite(MONGO_DATABASE.'.users', $bulk);


		// UPDATE MYSQL DB
		$online = ($online) ? $randomStr : null;
		$this->User = ClassRegistry::init('User');
		$this->User->id = $this->Session->read('Auth.User.id');
		$this->User->saveField('login_token', $online);

	}

	public function getDepartment($id = null){

		if( isset($id) && !empty($id) ){
			$query = "select name from departments where id = $id ";
			$dname =  ClassRegistry::init('Department')->query($query);
			if( isset( $dname ) && !empty($dname[0]['departments']['name']) ){
				return $dname[0]['departments']['name'];
			}
		}
	}

}
