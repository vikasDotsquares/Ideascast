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

class ApiUsersController extends AppController {



	var $name = 'ApiUsers';

	

	public $components = array('Email', 'Common', 'Image', 'CommonEmail', 'Auth', 'Group');



	public $live_setting;



	public $mongoDB = null;

	/**

	 * Helpers

	 *

	 * @var array

	 */

	public $helpers = array('Html', 'Form', 'Session', 'Time', 'Text', 'Common', 'Group', 'Wiki', 'User', 'ViewModel');

	public $controllerName = '';
	public $actionName = '';
	public function beforeFilter() {
		
		parent::beforeFilter();
		$this->Auth->allow('get_user_details');
		$view = new View();
		$this->objView = $view;		
		$this->live_setting = LIVE_SETTING;		
		$response = $this->api_check_user();		
		$this->renderApiResponse($response);
		$this->controllerName = $this->request->params['controller'];
		$this->actionName = $this->request->params['action'];
		
	}
	public function getUserData($email) {
		$joins = array(
		 array(
		  'table' => 'users',
		  'alias' => 'User',
		  'type' => 'INNER',
		  'conditions' => array(
		   'User.id = UserDetail.user_id',
		  ),
		 ),
		); 
	 
	   $data = $this->UserDetail->find('first',array('conditions' => array('User.email' =>$email), 'joins' => $joins, 'fields' => array('*')));
	  return $data ;
	  } 
	public function get_user_details() {

		$response = array();
		$data = array();
		$email 			=   ( !empty($this->request->data['email']) ? $this->request->data['email']  : ( !empty($this->request->query['email']) ? $this->request->query['email'] : '' ) );
		$_token 		=   ( !empty($this->request->data['_token']) ? $this->request->data['_token']  : ( !empty($this->request->query['_token']) ? $this->request->query['_token'] : '' ) );
		
		if( empty($email) && empty($_token)) 
		{
			$statusCode = 950;
			$message = 'Search email and _token both are  missing.';
		}
		else if( empty($email)) 
		{
			$statusCode = 950;
			$message = 'Search email is missing.';	
		}
		else if( empty($_token)) 
		{
			$statusCode = 950;
			$message = 'Token was missing so we are generating new token. You need to call this api again along with _token parameters in post format';	
			$data  = array( '_token' => $this->generateToken($email,$this->action,$this->controllerName) );
		}
		else
		{
			$secretKey = $email.':'.$this->actionName.':'.$this->controllerName;
			$responseData =  JWT::decode($_token, $secretKey, array('HS256'));

			if( !empty( $responseData ) )
			{
				$currentControllerName = $this->controllerName ;
				$currentActionName = $this->actionName;
				$res = $this->object2array($responseData->data) ;				
				
				if( $res['term'] == $email && $res['action'] == $currentActionName && $res['controller'] == $currentControllerName )
				{
					$this->loadModel('User');
					$this->loadModel('UserDetail');
					$user = $this->getUserData($email);
					$user['User']['created'] = date('Y-m-d',$user['User']['created']);
					$user['User']['modified'] = date('Y-m-d',$user['User']['modified']);

					$user['UserDetail']['created'] = date('Y-m-d',$user['UserDetail']['created']);
					$user['UserDetail']['modified'] = date('Y-m-d',$user['UserDetail']['modified']);

					
					
					if( !empty($user) )
					{
						$statusCode = 200;
						$message = 'User Found';
						unset($user[0]['User']['password']);
						$data = $user;
					}
					else
					{
						$statusCode = 200;
						$message = 'No User found';
						$data = array();
					}	
				}
				else
				{ 
					
					$statusCode = 950;
					$message = $res['message'];	
					$data  = array();
				}
				
			}
			else
			{
				
				$statusCode = 950;
				$message = 'Token is not valid';	
				$data  = array();
			}
		}
		$this->set([
			'message' =>$message,
			'statusCode' => $statusCode,
			'data' => $data,
			'_serialize' => ['statusCode', 'message', 'data']
		]);
	}

}

