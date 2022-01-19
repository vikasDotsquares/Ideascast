<?php



/**

 * Users controller.

 *

 * This file will render views from views/Users/

 *

 * PHP 5

 *

 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)

 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)

 *

 * Licensed under The MIT License

 * Redistributions of files must retain the above copyright notice.

 *

 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)

 * @link          http://cakephp.org CakePHP(tm) Project

 * @package       app.Controller

 * @since         CakePHP(tm) v 0.2.9

 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)

 */

App::uses('Controller/Component', 'Auth', 'Session', 'RequestHandler');

App::uses('CakeEmail', 'Network/Email');

App::uses('SimplePasswordHasher', 'Controller/Component/Auth');

App::import('Vendor', 'google-api');

App::uses('HttpSocket', 'Network/Http');



App::import('Lib', 'XmlApi');

/**

 * Users Controller

 *

 * @property User $User

 * @property PaginatorComponent $Paginator

 */

class AppUsersController extends AppController {



	var $name = 'AppUsers';

	

	public $components = array('Email', 'Common', 'Image', 'CommonEmail', 'Auth', 'Group');



	public $live_setting;



	public $mongoDB = null;

	/**

	 * Helpers

	 *

	 * @var array

	 */

	public $helpers = array('Html', 'Form', 'Session', 'Time', 'Text', 'Common', 'Group', 'Wiki', 'User', 'ViewModel');



	public function beforeFilter() {

		parent::beforeFilter();

		$this->Auth->allow('generate_keygen');
		$view = new View();
		$this->objView = $view;
		$this->live_setting = LIVE_SETTING;
		
		if( $this->Session->read('Auth.User.role_id') == 2 ){
			$this->redirect(['controller' => 'projects','action' => 'index']);
		}
		if( $this->Session->read('Auth.User.role_id') == 1 ){
			$this->redirect(['controller' => 'dashboard','action' => 'index']);
		}		
		
	}

	



	

	public function index() {

		$this->layout = 'inner';

		$orConditions = array();

		// $andConditions = array('NOT' => array('User.role_id' => array(1)));

		$andConditions = array('AppUser.role_id' => array(5));

		$finalConditions = array();



		$in = 0;

		// $per_page_show = $this->Session->read('api_user.per_page_show');
		$per_page_show = 10;
		 
		if (empty($per_page_show)) {

			$per_page_show = 10;

		}



		if (isset($this->data['AppUser']['keyword'])) {

			$keyword = trim($this->data['AppUser']['keyword']);

		} else {

			$keyword = $this->Session->read('api_user.keyword');

		}



		

		//pr($keyword); die;



		if (isset($keyword)) {

			$this->Session->write('api_user.keyword', $keyword);

			$keywords = explode(" ", $keyword);

			if (isset($keywords[0]) && !empty($keywords[0]) && count($keywords) < 2) {

				$keyword = $keywords[0];

				$in = 1;

				$orConditions = array('OR' => array(

					'AppUser.api_key LIKE' => '%' . $keyword . '%',

					'AppUser.first_name LIKE' => '%' . $keyword . '%',

					'AppUser.last_name LIKE' => '%' . $keyword . '%',

					//'UserDetail.country_id LIKE' => '%' . $county . '%'

				));

			} else if (!empty($keywords) && count($keywords) > 1) {

				$first_name = $keywords[0];

				$last_name = $keywords[1];

				$in = 1;

				$andConditions = array('AND' => array(

					'AppUser.first_name LIKE' => '%' . $first_name . '%',

					'AppUser.last_name LIKE' => '%' . $last_name . '%',

					//'UserDetail.country_id LIKE' => '%' . $county . '%'

				));

			}

		}



		if (isset($this->data['AppUser']['status'])) {

			$status = $this->data['AppUser']['status'];

		} else {

			$status = $this->Session->read('api_user.status');

		}



		if (isset($status)) {

			$this->Session->write('api_user.status', $status);

			if ($status != '') {

				$in = 1;

				$andConditions = array_merge($andConditions, array('AppUser.status' => $status));

			}

		}



		if (isset($this->data['AppUser']['per_page_show']) && !empty($this->data['AppUser']['per_page_show'])) {

			$per_page_show = $this->data['AppUser']['per_page_show'];

		}



		if (!empty($orConditions)) {

			$finalConditions = array_merge($finalConditions, $orConditions);

		}



		if (!empty($andConditions)) {

			$finalConditions = array_merge($finalConditions, array('AND' => $andConditions));

		}



		//pr($finalConditions); die;
		
		//=============== check api licence ======================
		App::import("Model", "OrgUserSettingJeera");
		$OrgUserSettingJeera = new OrgUserSettingJeera();		
		$whatINeed = explode(DOMAIN_PREFIX, $_SERVER['HTTP_HOST']);
		$whatINeed = $whatINeed[0];		 
		$organisationDetails = $OrgUserSettingJeera->find('first', array('conditions' => array('subdomain' => $whatINeed)));
		$apiusercount = $this->AppUser->find('count');
		
		$this->set('apiusercount', $apiusercount);
		$this->set('organisationDetails', $organisationDetails);
		
		//==========================================================
		
		
		


		//pr($this->paginate('User'));die;
		$this->loadModel('AppUser');

		$allusers = $this->AppUser->find('all', array('conditions' => $finalConditions)); 
		
		$count = $this->AppUser->find('count', array('conditions' => $finalConditions));



		$this->set('count', $count);

		$this->set('title_for_layout', __('All Internal IdeasCast API Users', true));

		$this->Session->write('api_user.per_page_show', $per_page_show);
	 
		$this->AppUser->recursive = 0;

		$this->paginate = array('conditions' => $finalConditions, "limit" => $per_page_show, "order" => "AppUser.created DESC");
		
		$this->set('users', $this->paginate('AppUser'));

		$this->set('in', $in);

	}



	



	/**

	 * add method

	 *

	 * @return void

	 */

	public function add() {
		
		$this->layout = 'inner';		
		$this->set('api_key',$this->keygen());
		$this->set('title_for_layout', __('Add API', true));
		
		
		//=============== check api licence ======================
		App::import("Model", "OrgUserSettingJeera");
		$OrgUserSettingJeera = new OrgUserSettingJeera();		
		$whatINeed = explode(DOMAIN_PREFIX, $_SERVER['HTTP_HOST']);
		$whatINeed = $whatINeed[0];		 
		$organisationDetails = $OrgUserSettingJeera->find('first', array('conditions' => array('subdomain' => $whatINeed)));
		$apiusercount = $this->AppUser->find('count');
		
		if( isset($organisationDetails['OrgSetting']['apilicense']) && $organisationDetails['OrgSetting']['apilicense'] == $apiusercount ){
			
			$this->Session->setFlash(__('Domain API licence has been completed.'), 'error');
			$this->redirect(array('action' => 'index'));
		}
		//=======================================================
		
		if ($this->request->is('post') || $this->request->is('put')) {

			//pr($this->request->data); die;
			// $this->request->data['AppUser']['user_id'] = $this->Session->read('Auth.User.id');	

			if ($this->Session->read('Auth.User.role_id') == 3) {
				$this->request->data['AppUser']['user_id'] = $this->Session->read('Auth.User.id');
			} else {
				$this->request->data['AppUser']['user_id'] = $this->Session->read('Auth.User.UserDetail.org_id');
			}
			
			$this->request->data['AppUser']['status'] = ($this->request->data['AppUser']['status'] == 'on')? 1 : 0;
			$this->request->data['AppUser']['web_execution_permission'] = ($this->request->data['AppUser']['	web_execution_permission'] == 'on')? 1 : 0;
			
			if ($this->AppUser->save($this->request->data)) {

				$this->Session->setFlash(__('The API request has been saved successfully.'), 'success');

				$this->redirect(array('action' => 'index'));

			}

		}

	}

	public function generate_keygen() {

		$this->autoRender = false;

		$response = ['apikey'=>$this->keygen()];

		echo json_encode ($response) ;

		

	}

	private function keygen($length=24)

	{

		$key = '';

		list($usec, $sec) = explode(' ', microtime());

		mt_srand((float) $sec + ((float) $usec * 100000));

		

		$inputs = array_merge(range('z','a'),range(0,9),range('A','Z'));

	

		for($i=0; $i<$length; $i++)

		{

			$key .= $inputs{mt_rand(0,61)};

		}

		return $key;

	}



	/**

	 * edit method

	 *

	 * @throws NotFoundException

	 * @param string $id

	 * @return void

	 */

	public function edit($id = null, $pid = null, $insid = null) {
		
		$this->layout = 'inner';
		
		$this->set('title_for_layout', __('Edit User', true));

		$this->User->id = $id;

		if (!$this->User->exists()) {

			$this->Session->setFlash(__('Invalid User.'), 'error');

			$this->redirect(array('action' => 'index'));

		}

		if ($this->request->is('post') || $this->request->is('put')) {

			// pr($this->request->data);die;

			if (isset($this->request->data['User']['status']) && $this->request->data['User']['status'] == "on") {

				$this->request->data['User']['status'] = "1";

			} else {

				$this->request->data['User']['status'] = "0";

			}



			if ($this->User->saveAssociated($this->request->data)) {

				$this->Session->setFlash(__('The user has been updated successfully.'), 'success');



				if ((isset($pid) && !empty($pid)) && (isset($insid) && !empty($insid))) {

					$this->redirect(array('action' => 'institution', $insid));

				} else if (isset($pid) && !empty($pid)) {

					$this->redirect(array('action' => 'planuser', $pid));

				} else {

					$this->redirect(array('action' => 'index'));

				}

			} else {



				$datas = $this->User->findById($id);

				$this->request->data['UserDetail']['profile_pic'] = $datas['UserDetail']['profile_pic'];

			}

		} else {

			$this->request->data = $this->User->read(null, $id);

			//pr($this->request->data);die;

			if (isset($this->request->data['User']['password'])) {

				unset($this->request->data['User']['password']);

			}

		}

	}



	/**

	 * update_status method

	 *

	 * @return void

	 */

	public function user_updatestatus() {
		
		if ($this->request->is('ajax')) {

			$this->autoRender = false;

			$this->loadModel('AppUser');

			$this->request->data['AppUser'] = $this->request->data;

			if ($this->AppUser->save($this->request->data)) {

				$this->Session->setFlash(__('AppUser status has been updated successfully.'), 'success');

				die('success');

			} else {

				$this->Session->setFlash(__('AppUser status could not updated successfully.'), 'error');

			}

		}

		die('error');

	}

	public function update_web_permission() {
		
		

			$this->autoRender = false;

			$this->loadModel('AppUser');

			$this->request->data['AppUser'] = $this->request->data;

			

			if ($this->AppUser->save($this->request->data)) {

				$this->Session->setFlash(__('AppUser web execution permission status has been updated successfully.'), 'success');

				

			} else {

				$this->Session->setFlash(__('AppUser web execution permission status could not updated successfully.'), 'error');

			}
		
			$this->redirect(array('action' => 'index'));

	}



	

	public function delete($id = null) {



		if (isset($this->data['id']) && !empty($this->data['id'])) {



			//if (isset($id)) {



			$id = $this->data['id'];



			$this->AppUser->id = $id;



			if (!$this->AppUser->exists()) {

				throw new NotFoundException(__('Invalid API User'), 'error');

			}



			if ($this->AppUser->delete()) {



				$this->Session->setFlash(__('API User has been deleted successfully.'), 'success');

				die('success');

			} else {

				$this->Session->setFlash(__('API User could not deleted successfully.'), 'error');

			}

		}

		die('error');

	}

	function user_resetfilter() {
		$this->Session->write('api_user.keyword', '');
		$this->Session->write('api_user.status', '');		
		$this->redirect(array('action' => 'index'));
	}

}

