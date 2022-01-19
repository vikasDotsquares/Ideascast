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
App::import('Vendor', 'firebase/src/JWT');
App::import('Lib', 'XmlApi');
/**
 * Users Controller
 *
 * @property User $User
 * @property PaginatorComponent $Paginator
 */
class ApiRewardsController extends AppController {

	public $name = 'ApiRewards';

	public $uses = [
		'User',
		'UserDetail',
		'Project',
		'Element',
		'UserProject',
		'RewardOptedSetting',
		'RewardAccelerate',
		'RewardAssignment',
		'RewardSetting',
		'RewardAccelerationHistory',
		'RewardHistory',
		'RewardCharity',
		'RewardRedeem',
		'RewardOffer',
		'RewardOfferShop',
		'RewardUserAcceleration',
		'ProjectWorkspace',
		'Workspace',
		'RmDetail',
		'DoList',
		'DoListUser',
	];

	public $objView = null;
	public $user_id = null;
	public $reward_types = null;
	public $accepted = false;
	public $notify = false;
	public $components = array(
		'Common',
	);
	public $helpers = array(
		'Html',
		'Form',
		'Session',
		'Time',
		'Text',
		'Common',
		'ViewModel',
	);

	public function beforeFilter() {
		
		parent::beforeFilter();
		$this->Auth->allow('get_reward_avail_count','get_charity_data','get_reward_redeemed_data','get_user_earned_points');
		$view = new View();
		$this->objView = $view;
		
		$this->reward_types = [
			'project' => 'Project',
			'workspace' => 'Workspace',
			'task' => 'Element',
			'risk' => 'Risk',
			'todo' => 'To-do',
			'subtodo' => 'Sub To-do',
			'other' => 'Other',
		];
		$this->set('reward_types', $this->reward_types);

		$this->live_setting = LIVE_SETTING;		

		$response = $this->api_check_user();
		$this->renderApiResponse($response);		
	}
	
	public function get_reward_avail_count() {
		ini_set('display_errors',true);
		error_reporting(E_ALL);

		$message = '';
		$statusCode = '';
		$response = array();
		$errors					=   array();
		$project_id 			=   ( !empty($this->request->data['project_id']) ? $this->request->data['project_id']  : ( !empty($this->request->query['project_id']) ? $this->request->query['project_id'] : '' ) );
		

		if( empty($project_id)  )
		{
			$statusCode = 950;
			array_push($errors,'Please provide the project id');			
		}
		else 
		{
			$project_reward_exists = project_reward_setting($project_id, 1);
			if( !$project_reward_exists )
			{
				$statusCode = 950;
				array_push($errors,'This project has not any reward setting.');
			}
			else
			{
				$data['project_avail_reward_count'] = $project_reward_exists['RewardSetting']['remaining_ov'];
			}
		}
		$textString = "Project's available rewards points.";
		$this->set([
			'message' => !empty($errors) ? $errors : $textString,
			'statusCode' => (!empty($errors) ? 950 : 200 ),
			'data' => $data,
			'_serialize' => ['statusCode', 'message', 'data']
		]);
	}

	public function get_reward_redeemed_data() 
	{
		
		$project_id 			=   ( !empty($this->request->data['project_id']) ? $this->request->data['project_id']  : ( !empty($this->request->query['project_id']) ? $this->request->query['project_id'] : '' ) );
		$user_id 			=   ( !empty($this->request->data['user_id']) ? $this->request->data['user_id']  : ( !empty($this->request->query['user_id']) ? $this->request->query['user_id'] : '' ) );
		$data = array();
		$errors = array();
		
		if( empty($project_id)  )
		{
			$statusCode = 950;
			array_push($errors,'Please provide project id');			
		}
		else
		{	
			$currency_unicode = project_currency_unicode($project_id);		
			$project_exists = $this->Project->exists($project_id);
			if(!$project_exists)
			{
				$statusCode = 950;
				array_push($errors,'Not a valid project id');	
			}
			else
			{
				$this->Project->bindModel(array('belongsTo'=>array('Currency'=>array('className' => 'Currency'))));	
				$project = $this->Project->findById($project_id);
				$project_reward_exists = project_reward_setting($project_id, 1);
				if( !$project_reward_exists )
				{
					$statusCode = 950;
					array_push($errors,'This project has not any reward setting.');
				}
				else
				{
					$projectsPeoples = array();
					$project_reward_exists = project_reward_setting($project_id, 1);
					$data['project'] = array('project_id'=>$project['Project']['id'],'name'=>$project['Project']['title']);
					if( !empty($user_id))
					{				
						$uids = explode(',',$user_id);
						$view = new View();
						$viewModal = $view->loadHelper('ViewModel');
						$projectpeople = $viewModal->projectusers($project['Project']['id']);
						
						foreach($uids as $_uid)
						{
							if( user_opt_status($_uid) && in_array($_uid,$projectpeople))
							{	
								$projectsPeoples[] =  $_uid;	
							}
						}
						
						foreach($uids as $uid)
						{
							if(!in_array($uid,$projectsPeoples))
							{
								$statusCode = 950;
								array_push($errors,"User Id(#".$uid.")  has not associated with this project.");
								break;
							}
							else
							{
								$project_reward = user_redeemed_data($uid,$project_id);
								$userDetails = $this->User->find('all', array('conditions' => array('User.id' => $uid),  'fields' => array('User.id,User.email,UserDetail.first_name,UserDetail.last_name')));
								if( !empty($project_reward))
								{
									$project_reward_assignments = project_reward_assignments($project_id, $user_id, null, 'reward');
									$data['user'] = array(
												'user_id'				=> $userDetails[0]['User']['id'],
												'first_name'			=> $userDetails[0]['UserDetail']['first_name'],
												'last_name'				=> $userDetails[0]['UserDetail']['last_name'],
												'email'					=> $userDetails[0]['User']['email'],
												'currency' 				=> $currency_unicode ,
												'allocated_rewards'     => array_sum(array_map(function($item) {return $item['RewardAssignment']['allocated_rewards'];}, $project_reward_assignments)),
												'user_redeemed_count' 	=> array_sum(array_map(function($item) {return $item['RewardRedeem']['redeem_amount'];}, $project_reward)),
												'cash_amount' 			=> array_sum(array_map(function($item) {return $item['RewardRedeem']['redeemed_value'];}, $project_reward)). ' '.$currency_unicode,	
											);
								}
								else
								{
									$data['user'] = array(
												'user_id'				=> $userDetails[0]['User']['id'],
												'first_name'			=> $userDetails[0]['UserDetail']['first_name'],
												'last_name'				=> $userDetails[0]['UserDetail']['last_name'],
												'email'					=> $userDetails[0]['User']['email'],
												'currency' 				=> $currency_unicode ,
												'allocated_rewards'     => 0,
												'user_redeemed_count' 	=> 0,
												'cash_amount' 			=> '0 '.$currency_unicode,	
											);
								}
							}
						}
					}
					else
					{

						$charity_details = $this->RewardCharity->find('first', array('conditions' => array('RewardCharity.project_id' => $project_id),  'fields' => array('RewardCharity.id,RewardCharity.title')));
						if(!empty($charity_details))
						{
							$charity_id = $charity_details['RewardCharity']['id'];
						$data['charity']['title'] = $charity_details['RewardCharity']['title'];
						$charity_redeem_data = charity_redeem_data($charity_id);
						
						$data['charity']['redeem_amount'] = array_sum(array_map(function($item) {return $item['RewardRedeem']['redeem_amount'];}, $charity_redeem_data));
						//$data['charity']['redeemed_value'] = array_sum(array_map(function($item) {return $item['RewardRedeem']['redeemed_value'];}, $charity_redeem_data));

						$data['charity']['cash_amount'] = array_sum(array_map(function($item) {return $item['RewardRedeem']['redeemed_value'];}, $charity_redeem_data)). ' '.$currency_unicode;	
						//$data['charity']['currency'] = htmlspecialchars( $project['Currency']['symbol'] );	

						$project_reward_assignments = project_reward_assignments($project_id, null, null, 'reward');	
						$data['project_data']	= array('ov_allocation' => $project_reward_exists['RewardSetting']['ov_allocation'],'remaining_ov' =>  $project_reward_exists['RewardSetting']['remaining_ov']);
						foreach($project_reward_assignments as $assg)
						{					
							$uid = $assg['RewardAssignment']['user_id'];
							$userDetails = $this->User->find('all', array('conditions' => array('User.id' => $uid),  'fields' => array('User.id,User.email,UserDetail.first_name,UserDetail.last_name')));
							if(!isset($data['users'][$uid] ) )
							{
								$data['users'][$uid] = array();	
								$data['users'][$uid]['user_id'] 				= $uid;
								$data['users'][$uid]['first_name']				= $userDetails[0]['UserDetail']['first_name'];
								$data['users'][$uid]['last_name']				= $userDetails[0]['UserDetail']['last_name'];
								$data['users'][$uid]['email']					= $userDetails[0]['User']['email'];
								$data['users'][$uid]['currency'] 				= $currency_unicode;
								$data['users'][$uid]['allocated_rewards']		= $assg['RewardAssignment']['allocated_rewards'];
								$project_reward 								= user_redeemed_data($uid,$project_id);
								if( !empty($project_reward))
								{
									$data['users'][$uid]['user_redeemed_count'] 	= array_sum(array_map(function($item) {return (!empty($item['RewardRedeem']['redeem_amount'])?$item['RewardRedeem']['redeem_amount']:0);}, $project_reward));
									$data['users'][$uid]['cash_amount'] 			= array_sum(array_map(function($item) {return $item['RewardRedeem']['redeemed_value'];}, $project_reward)). ' '.$currency_unicode;
								}
								else
								{
									$data['users'][$uid]['user_redeemed_count'] = 0 ;
									$data['users'][$uid]['cash_amount'] = 0;
								}
								
								$data['users'][$uid]['currency'] 				= $currency_unicode;
							}
							else
							{
								$data['users'][$uid]['allocated_rewards']		+= $assg['RewardAssignment']['allocated_rewards'];
							}
						}
						}
						
					}
				}
			}
		}
		if( !empty($errors))
		{
						$this->set([
				'message' => !empty($errors) ? $errors : 'Record Found',
				'statusCode' => (!empty($errors) ? 950 : 200 ),
				'data' => array(),
				'_serialize' => ['statusCode', 'message', 'data']
			]);

		}
		else
		{
			$this->set([
				'message' => !empty($errors) ? $errors : 'Record Found',
				'statusCode' => (!empty($errors) ? 950 : 200 ),
				'data' => $data,
				'_serialize' => ['statusCode', 'message', 'data']
			]);
		}
	}
	public function get_charity_data() {
		$project_id 			=   ( !empty($this->request->data['project_id']) ? $this->request->data['project_id']  : ( !empty($this->request->query['project_id']) ? $this->request->query['project_id'] : '' ) );
		$data = array();
		$errors= array();
		if( !empty($project_id) )
		{
			$currency_unicode = project_currency_unicode($project_id);
			$this->Project->bindModel(array('belongsTo'=>array('Currency'=>array('className' => 'Currency'))));			
			$project = $this->Project->findById($project_id);
			$project_reward_exists = project_reward_setting($project_id, 1);
			if( !$project_reward_exists )
			{
				$statusCode = 950;
				array_push($errors,'This project has not any reward setting.');
			}
			else
			{

				$charity_details = $this->RewardCharity->find('first', array('conditions' => array('RewardCharity.project_id' => $project_id),  'fields' => array('RewardCharity.id,RewardCharity.title')));
				$charity_id = $charity_details['RewardCharity']['id'];
				$data['title'] = $charity_details['RewardCharity']['title'];
				$charity_redeem_data = charity_redeem_data($charity_id);
				$data['redeem_amount'] = array_sum(array_map(function($item) {return $item['RewardRedeem']['redeem_amount'];}, $charity_redeem_data));
				$data['cash_amount'] = array_sum(array_map(function($item) {return $item['RewardRedeem']['redeemed_value'];}, $charity_redeem_data)). ' '.$currency_unicode;
				
				
			}
		}
		else 
		{
			$statusCode = 950;
			array_push($errors,'Please provide project id.');	
		}
		$this->set([
			'message' => !empty($errors) ? $errors : '',
			'statusCode' => (!empty($errors) ? 950 : 200 ),
			'data' => $data,
			'_serialize' => ['statusCode', 'message', 'data']
		]);
		

	}
	
	private function getAllEarnedPoints($user_id)
	{
		$total_earned  = 0 ;
		$user_earned_points = user_reward_assignments($user_id);
		if($user_earned_points) 
		{
            foreach ($user_earned_points as $rdKey => $rdVal) 
			{
                $total_earned += $rdVal['RewardAssignment']['allocated_rewards'];
            }
        }
		$user_accelerated_points = user_accelerated_points( $user_id );

        if($user_accelerated_points)
		{
            $total_earned += $user_accelerated_points;
        }
		return $total_earned ;
	}
	
	public function get_user_earned_points() 
	{
		$user_id 			=   ( !empty($this->request->data['user_id']) ? $this->request->data['user_id']  : ( !empty($this->request->query['user_id']) ? $this->request->query['user_id'] : '' ) );
		$data = array();
		$errors = array();
		if( empty($user_id)  )
		{
			$statusCode = 950;
			array_push($errors,'Please provide user id');			
		}
		$uids = explode(',',$user_id);
		foreach($uids as $key => $uid)
		{
			$user_exists = $this->User->exists($uid);
			if(!$user_exists)
			{
				$statusCode = 950;
				array_push($errors,'User Id: '. $uid .' not exists.');
				break ;	
			}
			else
			{
				$userDetails = $this->User->find('all', array('conditions' => array('User.id' => $uid),  'fields' => array('User.id,User.email,UserDetail.first_name,UserDetail.last_name')));
				
				$data[$key]['total_earned_points'] = $this->getAllEarnedPoints($uid);
				$data[$key]['first_name'] = $userDetails[0]['UserDetail']['first_name'];
				$data[$key]['last_name'] = $userDetails[0]['UserDetail']['last_name'];
				$data[$key]['email'] = $userDetails[0]['User']['email'];

			}
		
		}
		$this->set([
			'message' => !empty($errors) ? $errors : 'Success',
			'statusCode' => (!empty($errors) ? 950 : 200 ),
			'data' => (!empty($errors) ? array() : $data ),
			'_serialize' => ['statusCode', 'message', 'data']
		]);
	}
}