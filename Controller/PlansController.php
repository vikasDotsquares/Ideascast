<?php
App::uses('Controller/Component', 'Auth', 'Session', 'RequestHandler');
App::uses('CakeEmail', 'Network/Email');

class PlansController extends AppController {

    var $name = 'Plans';
    public $uses = array('Plan','UserPlan','UserTransctionDetail','User','UserDetail','Currency','Coupon','UserInstitution');

    /**
     * check login for admin and frontend user
     * allow and deny user
     */
    public $components = array('Email', 'common', 'Image', 'CommonEmail', 'Auth');
	
    /**
     * Helpers
     *
     * @var array
     */
    public $helpers = array('Html', 'Form', 'Session', 'Time', 'Text','Common');

    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('index', 'register', 'confirm', 'logout', 'activate', 'activation','plansummary','paymentsuccess','paymentcancel','thanks','coupons');
    }
	
	/*     * ********************* Admin Panel Common Functions Start ************************* */
	
		public function admin_index(){
		
        $orConditions = array();
        $andConditions = array();
        $finalConditions = array();

        $in = 0;
        $per_page_show = $this->Session->read('plan.per_page_show');
        if (empty($per_page_show)) {
            $per_page_show = ADMIN_PAGING;
        }

        if (isset($this->data['Plan']['keyword'])) {
            $keyword = trim($this->data['Plan']['keyword']);
        } else {
            $keyword = $this->Session->read('plan.keyword');
        }

        if (isset($keyword)) {
            $this->Session->write('Plan.keyword', $keyword);
            $keywords = explode(" ", $keyword);
            if (isset($keywords[0]) && !empty($keywords[0]) && count($keywords) < 2) {
                $keyword = $keywords[0];
                $in = 1;
                $orConditions = array('OR' => array(
                        'Plan.title LIKE' => '%' . $keyword . '%',
                        'Plan.description LIKE' => '%' . $keyword . '%',                        
						'Plan.plantype_monthly LIKE' => '%' . $keyword . '%',                        
						'Plan.plantype_yearly LIKE' => '%' . $keyword . '%',						
                        'Plan.id LIKE' => '%' . $keyword . '%'
                ));
            } else if (!empty($keywords) && count($keywords) > 1) {
                $name = $keywords[0];
                $sign = $keywords[1];
                $in = 1;
                $andConditions = array('AND' => array(
                        'Plan.title LIKE' => '%' . $name . '%',
                        'Plan.description LIKE' => '%' . $sign . '%',
						'Plan.plantype_monthly LIKE' => '%' . $keyword . '%',                        
						'Plan.plantype_yearly LIKE' => '%' . $keyword . '%',		
                ));
            }
        }

        if (isset($this->data['Plan']['status'])) {
            $status = $this->data['Plan']['status'];
        } else {
            $status = $this->Session->read('plan.status');
        }

        if (isset($status)) {
            $this->Session->write('plan.status', $status);
            if ($status != '') {
                $in = 1;
                $andConditions = array_merge($andConditions, array('Plan.status' => $status));
            }
        }

        if (isset($this->data['Plan']['per_page_show']) && !empty($this->data['Plan']['per_page_show'])) {
            $per_page_show = $this->data['Plan']['per_page_show'];
        }


        if (!empty($orConditions)) {
            $finalConditions = array_merge($finalConditions, $orConditions);
        }
        if (!empty($andConditions)) {
            $finalConditions = array_merge($finalConditions, array('AND' => $andConditions));
        }
		
		$count = $this->Plan->find('count', array('conditions' => $finalConditions));
        $this->set('count', $count);		

        $this->set('title_for_layout', __('All Plans', true));
        $this->Session->write('plan.per_page_show', $per_page_show);
        $this->Plan->recursive = 0;
        $this->paginate = array('conditions' => $finalConditions, "limit" => $per_page_show, "order" => "Plan.id DESC");
        $this->set('plans', $this->paginate('Plan'));
        $this->set('in', $in);
	}
	
	
	
	function admin_plan_resetfilter(){
		$this->Session->write('plan.keyword', '');
		$this->Session->write('plan.status', '');
		$this->redirect(array('action' => 'index'));
	}
	
	
	
	    public function admin_plan_updatestatus() {
        if ($this->request->is('ajax')) {
            $this->autoRender = false;
            $this->loadModel('Plan');
            $this->request->data['Plan'] = $this->request->data;

            if ($this->Plan->save($this->request->data,false)) {
                $this->Session->setFlash(__('Plan status has been updated successfully.'), 'success');
                die('success');
            } else {
                $this->Session->setFlash(__('Plan status could not updated successfully.'), 'error');
            }
        }
        die('error');
    }
	
	
	
		/**
	 * admin_source_delete method
	 *
	 * @throws MethodNotAllowedException
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
    public function admin_delete($id = null) {
        if (isset($this->data['id']) && !empty($this->data['id'])) {
            $id = $this->data['id'];
            $this->Plan->id = $id;
            if (!$this->Plan->exists()) {
                throw new NotFoundException(__('Invalid Plan'), 'error');
            }

            if ($this->Plan->delete()) {
                $this->Session->setFlash(__('Plan has been deleted successfully.'), 'success');
                die('success');
            } else {
                $this->Session->setFlash(__('Plan could not deleted successfully.'), 'error');
            }
        }
        die('error');
    }
	
	

	      /**
     * admin_add method
     *
     * @return void
     */
    public function admin_add() {
        $this->set('title_for_layout', __('Add Plan', true));
        if ($this->request->is('post') || $this->request->is('put')) { //pr($this->request->data); die;
			
			if(isset($this->request->data['Plan']['status']) && $this->request->data['Plan']['status']=="on"){ 
			$this->request->data['Plan']['status'] =  "1";
			}else{
			$this->request->data['Plan']['status'] =  "0";
			}
			if(!isset($this->request->data['Plan']['plantype_once'])){
			$this->request->data['Plan']['plantype_once'] =  "0";
			}
		
            if ($this->Plan->save($this->request->data,false)) {
                $this->Session->setFlash(__('Plan has been saved successfully.'), 'success');
                $this->redirect(array('action' => 'index'));
            }
        }
    }
       /**
     * admin_edit method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function admin_edit($id = null) {
        $this->set('title_for_layout', __('Edit Plan', true));
        $this->Plan->id = $id;
        if (!$this->Plan->exists()) {
            $this->Session->setFlash(__('Invalid Plan.'), 'error');
            $this->redirect(array('action' => 'index'));
        }
        if ($this->request->is('post') || $this->request->is('put')) {
         //  pr($this->request->data);die;
		  
			if(isset($this->request->data['Plan']['status']) && $this->request->data['Plan']['status']=="on"){ 
			$this->request->data['Plan']['status'] =  "1";
			}else{
			$this->request->data['Plan']['status'] =  "0";
			}
            if ($this->Plan->save($this->request->data,false)) {
                $this->Session->setFlash(__('The Plan has been updated successfully.'), 'success');
                $this->redirect(array('action' => 'index'));
            }
        } else {
            $this->request->data = $this->Plan->read(null, $id);			

			//pr($data); die;
          
        }
    }

	
	
	
	
	
	/*     * ********************* Admin Panel Common Functions Start ************************* */
	
	
	
	

    /*     * ********************* Front End Panel Common Functions Start ************************* */

	
	
	    public function add_feature() {
	   $this->layout = "inner";
       //$userId = $this->params['pass']['0'];
	    $userId = $this->Session->read('Auth.User.id');
	    $id = $this->Session->read('Auth.User.id');
		
		$this->UserPlan->deleteAll(array('UserPlan.user_id' => $id,'UserPlan.is_active' => 0,'UserPlan.trans_id' => 0));
		
	    $plans = $this->UserPlan->find('all', array('conditions'=>array('UserPlan.user_id'=> $id,'UserPlan.plan_id !='=> '','UserPlan.plan_type >'=> 0 ), 'fields' => array('UserPlan.*'),'Order'=>'UserPlan.id'));
	  
	   foreach($plans as $pln)
		{
		  $plnID[] =  $pln['UserPlan']['plan_id'];
		}
		
		//$pIDS = implode(",",array_unique($plnID));
		//pr($plans); die;
		

	   if(isset($plnID)){
       $planList =  $this->Plan->find("all", array('fields'=>array('Plan.*'),'conditions'=>array('Plan.status' => 1, "NOT" => array( "Plan.id" => $plnID ))));	   
	   
      }	else{
       $planList =  $this->Plan->find("all", array('fields'=>array('Plan.*'),'conditions'=>array('Plan.status' => 1)));
	  }

       $this->set('planList', $planList);
	   
	   $plansRenew = $this->UserPlan->find('all', array('conditions'=>array('UserPlan.user_id'=> $id,'UserPlan.plan_id !='=> '','UserPlan.plan_type >'=> 0 ,'UserPlan.is_active !=' => 0), 'fields' => array('UserPlan.*'),'Order'=>'UserPlan.id'));
	   
	   foreach($plansRenew as $pnew)
		{
		  $plnIDn[] =  $pnew['UserPlan']['plan_id'];
		}
		
		//$pIDS = implode(",",array_unique($plnID));
		//pr($plans); die;
		

	   if(isset($plnIDn)){
       $planListnew =  $this->Plan->find("all", array('fields'=>array('Plan.*'),'conditions'=>array('Plan.status' => 1, "NOT" => array( "Plan.id" => $plnIDn ))));	   
	   
      }	else{
       $planListnew =  $this->Plan->find("all", array('fields'=>array('Plan.*'),'conditions'=>array('Plan.status' => 1)));
	  }

       $this->set('planListRenew', $planListnew);	   
	   
	 // pr($this->request->data); die;

	   if ($this->request->is('post') || $this->request->is('put') && $userId > 0) {
	   
	  // pr($this->request->data); die;
			if(isset($this->request->data['add_plan']) && (isset($this->request->data['plan']) && !empty($this->request->data['plan']))){
			
			
				foreach($this->request->data['plan'] as $key => $val){
					if(isset($val['m']) && !empty($val['plantype'])){
					
					 $this->request->data['UserPlan']['plan_id'] = $key;
						 $this->request->data['UserPlan']['plan_type'] = $val['plantype'];						 
						 if($val['plantype']=='1'){
							$this->request->data['UserPlan']['start_date'] = strtotime(date('Y-m-d'));
							$this->request->data['UserPlan']['end_date'] =  strtotime(date("Y-m-d", strtotime("+1 month")));
						 }else if($val['plantype']=='4'){
							 $this->request->data['UserPlan']['start_date'] = strtotime(date('Y-m-d'));
							$this->request->data['UserPlan']['end_date'] =  strtotime(date("Y-m-d", strtotime("+ 365 day")));
						 }else{
							 $this->request->data['UserPlan']['start_date'] = '';
							 $this->request->data['UserPlan']['end_date'] = '';
						 }
					}else{
						unset($this->request->data['UserPlan']['plan_id']);
						unset($this->request->data['UserPlan']['plan_type']);
						unset($this->request->data['UserPlan']['start_date']);
						unset($this->request->data['UserPlan']['end_date']);
						unset($this->request->data['UserPlan']['user_id']);						
					}
					
					$this->request->data['UserPlan']['user_id'] = $userId;	
					
				//	$UserPlandata = array();
					//pr($this->request->data['UserPlan']); die;
					
					
					if(isset($this->request->data['UserPlan']['plan_id'])){
					$UserPlandata[] = $this->request->data['UserPlan'];
					}
					
				}
				
				if(!isset($UserPlandata)){
				$UserPlandata = array();
				}
				
					//echo '<pre>';print_r($UserPlandata);die;
					
					$i ='0';
					foreach($UserPlandata as $pdats){
					if($pdats['plan_id'] !='0'){
					
					 $i++;
					}
					}
					
			    if($i <1){		
				$this->Session->setFlash(__('Please choose one of feature.'));				
				return $this->redirect(array('controller' => 'plans', 'action' => 'add_feature', $userId, 'admin' => false));
				 
				}
					
				if($this->UserPlan->saveAll($UserPlandata)){
					 return $this->redirect(array('controller' => 'plans', 'action' => 'plansummary_new', $userId, 'admin' => false));
				}else{
					 $this->Session->setFlash(__('The user plan could not be saved. Please, try again.'));
				}
			}
			else{
					$this->Session->setFlash(__('Please choose one of feature.'));				
				return $this->redirect(array('controller' => 'plans', 'action' => 'add_feature', $userId, 'admin' => false));
			}	
	   } 
	   
	    $this->set('crumb', [ 'last' => [ 'Add Features' ] ] );
	    	
    }
	
	
	public function plansummary_new(){  
	
		//$this->set('crumb', [ 'last' => [ 'Plan Summary' ] ] );
		
			$this->set('crumb', [ 'Add Features' => '/plans/add_feature', 'last' => [ 'Plan Summary' ] ] );

		$userId = $this->params['pass']['0'];
		
        $this->layout = "inner";
		
		$this->UserPlan->bindModel(array('belongsTo'=>array('Plan'=>array('foreignKey' => false,'conditions'=>array('Plan.id=UserPlan.plan_id')))));	
		$UserPlanData = $this->UserPlan->find('all',array('fields'=>array('UserPlan.*','Plan.*'),'conditions'=>array('UserPlan.user_id'=> $userId,'Plan.status' => 1,'UserPlan.is_active'=>0,'UserPlan.plan_type >'=>0,'UserPlan.trans_id' => 0)));
		
		//pr($UserPlanData); die;
		
		$currency_codes = $this->Currency->find('first',array('fields'=>array('Currency.*'),'conditions'=>array('Currency.status'=> 1)));
		
			$ud = $this->UserDetail->find('first',array('fields'=>array('UserDetail.*'),'conditions'=>array('UserDetail.user_id' => $this->request->params['pass']['0'])));
			
			if(isset($ud['UserDetail']['country_id']) && !empty($ud['UserDetail']['country_id'])){
			 $vat =  $this->Common->getVat($ud['UserDetail']['country_id']); 
			 		$this->set('vat', $vat);
			}
		
		//pr($currency_codes);
		 $currency_code = $currency_codes['Currency']['sign']; 
		 $currency_id = $currency_codes['Currency']['id']; 
		
		
		$this->set('UserPlanData', $UserPlanData);
		 if ($this->request->is('post') || $this->request->is('put')) { 
		 
	
	/*-----------------------------------Coupon Code Start----------------------------------------------------*/
	
	if(isset($this->request->data['Coupon']['name']) && !empty($this->request->data['Coupon']['name'])){
			
	$coupon = $this->request->data['Coupon']['name'];
	
	$membership_code = $this->UserDetail->find('first',array('fields'=>array('UserDetail.membership_code'),'conditions'=>array('UserDetail.user_id' => $this->request->params['pass']['0'])));
	
	if(isset($membership_code['UserDetail']['membership_code']) && !empty($membership_code['UserDetail']['membership_code'])){
	$this->request->data['UserInstitution']['membership_code']	= $membership_code['UserDetail']['membership_code'] ;
	}else{
	unset($this->request->data['UserInstitution']['membership_code']);
	}
	
	//pr($this->request->data['UserInstitution']['membership_code']);
	
	if(isset($this->request->data['UserInstitution']['membership_code']) && !empty($this->request->data['UserInstitution']['membership_code'])){
	$memcode = $this->request->data['UserInstitution']['membership_code'];

	$userData = $this->UserInstitution->find('first',array('fields'=>array('UserInstitution.*'),'conditions'=>array('UserInstitution.membership_code' => $memcode,'UserInstitution.end >=' => date('Y-m-d 12:00:00'),'UserInstitution.start <=' => date('Y-m-d 12:00:00'))));
	
	if(isset($userData) && !empty($userData)){
	$userDetails = $this->UserDetail->find('first',array('fields'=>array('UserDetail.*'),'conditions'=>array('UserDetail.user_id' => $userData['UserInstitution']['user_id'])));
	
		$userStatus = $this->User->find('first',array('fields'=>array('User.status'),'conditions'=>array('User.id' => $userData['UserInstitution']['user_id'])));
	}
	}
	
	if((isset($userData) && !empty($userData)) && (!empty($userStatus['User']['status']))){ //pr($userStatus);
	$associated_user_id = $userData['UserInstitution']['user_id'];
	}else{
	$associated_user_id = 0;
	}
	
	$couponData = $this->Coupon->find('first',array('fields'=>array('Coupon.*'),'conditions'=>array('Coupon.status' => 1,'Coupon.name' => $coupon,'Coupon.end_time >=' => date('Y-m-d 12:00:00'),'Coupon.start_time <=' => date('Y-m-d 12:00:00'),'CAST(Coupon.useable AS SIGNED) > CAST(Coupon.used AS SIGNED)')));
	
	if(isset($couponData) && !empty($couponData)){
	$institution = $couponData['Coupon']['is_institution'];
	}
	
	
	
	if((isset($this->request->data['UserInstitution']['membership_code']) && !empty($this->request->data['UserInstitution']['membership_code'])) && (isset($institution) && $institution==1)){
	
	$couponInsData = $this->Coupon->find('first',array('fields'=>array('Coupon.*'),'conditions'=>array('Coupon.status' => 1,'Coupon.name' => $coupon,'FIND_IN_SET(\''. $associated_user_id .'\',Coupon.user_id)','Coupon.end_time >=' => date('Y-m-d 12:00:00'),'Coupon.start_time <=' => date('Y-m-d 12:00:00'),'CAST(Coupon.useable AS SIGNED) > CAST(Coupon.used AS SIGNED)')));

	
	}else{
	$couponInsData = $couponData;
	
	}
	
	//pr($this->request->data); die;
	
	if(isset($couponInsData) && !empty($couponInsData)){
	$coupon_id = $couponInsData['Coupon']['id'];
	$coupon_user_id = $couponInsData['Coupon']['id'];
	$institution = $couponInsData['Coupon']['is_institution'];
	
	
	
		/*-----------------------------------if minimum total amount----------------------------------------------------*/
	
	if($couponInsData['Coupon']['on_amount'] > 0){
	
	if(isset($couponInsData['Coupon']['flat']) && $couponInsData['Coupon']['flat'] > 0){ 
	if((!empty($couponInsData['Coupon']['flat']) && ($couponInsData['Coupon']['flat'] > 0 && $couponInsData['Coupon']['flat'] <= $this->request->data['plan']['totalfees'])) && ($this->request->data['plan']['totalfees'] >= $couponInsData['Coupon']['on_amount'] )){
	
	$disamount =  $this->request->data['plan']['totalfees'] -  $couponInsData['Coupon']['flat'];

	if($disamount==0 || $disamount<0.01){
	$disamount ="free";
	}
	$this->Session->write('disamount', $disamount);
	}else{
	$disamount = -1;
	$this->Session->write('disamount', $disamount);
	}
	}else{
	$disamount = 'a';
		$this->Session->write('disamount', $disamount);
	}
		

	
	if((isset( $couponInsData['Coupon']['percentage']) &&  $couponInsData['Coupon']['percentage'] >0 )  && ($this->request->data['plan']['totalfees'] >= $couponInsData['Coupon']['on_amount'] ) && ($disamount !='-1')){
	
	 if($disamount=='free'){
	  $disamount ="free";
	  }else{
	   
	  
	   if($disamount !='0' && $disamount !='free' && $disamount !='a'){
	   
	   $per_amount = $disamount *  $couponInsData['Coupon']['percentage']/100;
	   $disamount = $disamount - $per_amount;
	   }else{
	    $per_amount = $this->request->data['plan']['totalfees'] *  $couponInsData['Coupon']['percentage']/100;
		$disamount = $this->request->data['plan']['totalfees'] - $per_amount;
	   }
	      
		
	 
	  	if($disamount==0 || $disamount<0.01){
		$disamount ="free";
		}
		
	  $this->Session->write('disamount', $disamount);	
	}
	
	}else if((isset( $couponInsData['Coupon']['percentage']) &&  $couponInsData['Coupon']['percentage'] >0 ) && (!isset($couponInsData['Coupon']['flat']) || $couponInsData['Coupon']['flat'] ==0)  && ($this->request->data['plan']['totalfees'] >= $couponInsData['Coupon']['on_amount'] ) && ($disamount !='-1') ){
	  
	 if($disamount=='free'){
	  $disamount ="free";
	  }else{
	   
	  
	   if($disamount !='0' && $disamount !='free' && $disamount !='a'){
	   
	   $per_amount = $disamount *  $couponInsData['Coupon']['percentage']/100;
	   $disamount = $disamount - $per_amount;
	   }else{
	    $per_amount = $this->request->data['plan']['totalfees'] *  $couponInsData['Coupon']['percentage']/100;
		$disamount = $this->request->data['plan']['totalfees'] - $per_amount;
	   }
	 
	  	if($disamount==0 || $disamount<0.01){
		$disamount ="free";
		}
		
	  $this->Session->write('disamount', $disamount);	
	}
	
	}
	}else{
	
	/*-----------------------------------if minimum total amount not set----------------------------------------------------*/
	
	if(isset($couponInsData['Coupon']['flat']) && $couponInsData['Coupon']['flat'] > 0){ 
	if((!empty($couponInsData['Coupon']['flat']) && ($couponInsData['Coupon']['flat'] > 0 && $couponInsData['Coupon']['flat'] <= $this->request->data['plan']['totalfees']))){  
	$disamount =  $this->request->data['plan']['totalfees'] -  $couponInsData['Coupon']['flat'];
	
	if($disamount==0 || $disamount<0.01){
	$disamount ="free";
	}
	$this->Session->write('disamount', $disamount);
	}else{
	$disamount = -1;
	$this->Session->write('disamount', $disamount);
	}
	}else{ 
	
	    $disamount = 'a';
		$this->Session->write('disamount', $disamount);
		
	}
	
	
	if((isset( $couponInsData['Coupon']['percentage']) &&  $couponInsData['Coupon']['percentage'] >0  && ($disamount !='-1'))){
	
	  if($disamount=='free'){
	  $disamount ="free";
	  }else{
	   
	  
	   if($disamount !='0' && $disamount !='free' && $disamount !='a'){
	   
	   $per_amount = $disamount *  $couponInsData['Coupon']['percentage']/100;
	   $disamount = $disamount - $per_amount;
	   }else{
	    $per_amount = $this->request->data['plan']['totalfees'] *  $couponInsData['Coupon']['percentage']/100;
		$disamount = $this->request->data['plan']['totalfees'] - $per_amount;
	   }
	 
	  	if($disamount==0 || $disamount<0.01){
		$disamount ="free";
		}
	} 
			
	  $this->Session->write('disamount', $disamount); 
	
	}else if(((isset( $couponInsData['Coupon']['percentage']) &&  $couponInsData['Coupon']['percentage'] >0 ) || ((!isset($couponInsData['Coupon']['flat']) || $couponInsData['Coupon']['flat'] ==0)))  && ($disamount !='-1')){
	 
	 if($disamount=='free'){
	  $disamount ="free";
	  }else{
	   
	  
	   if($disamount !='0' && $disamount !='free' && $disamount !='a'){
	   
	   $per_amount = $disamount *  $couponInsData['Coupon']['percentage']/100;
	   $disamount = $disamount - $per_amount;
	   }else{
	    $per_amount = $this->request->data['plan']['totalfees'] *  $couponInsData['Coupon']['percentage']/100;
		$disamount = $this->request->data['plan']['totalfees'] - $per_amount;
	   }
	 
	  	if($disamount==0 || $disamount<0.01){
		$disamount ="free";
		}
		
	  $this->Session->write('disamount', $disamount);	
	}
	
	}
	
	}
	
	   if($disamount!='free'){
	   $vatn = $disamount * $vat/100;
	   $vm = $disamount + $vatn ; 
	   $this->set('vatamount',$vm);	   
	   }
 
		//echo  $disamount ; //die;
	
	 $this->set('disamount',$disamount);
	 
	 $this->set('coup_id',$couponInsData['Coupon']['id']);
	 $this->set('associated_user_id',$associated_user_id);
	 

	
	
	
	if((isset( $couponInsData['Coupon']['percentage']) &&  $couponInsData['Coupon']['percentage'] >0) && (!empty($couponInsData['Coupon']['flat']) && ($couponInsData['Coupon']['flat'] > 0)) && (($disamount >= 0) && ($disamount !='free') && ($disamount !='a') && ($disamount !='-1'))){
	
	   $this->Session->setFlash(__( " Flat discount (". round($this->common->currencyConvertor($couponInsData['Coupon']['flat'])). ") on total amount + ".$couponInsData['Coupon']['percentage']." % on rest discounted amount has applied successfully."), 'success');
	   	 
		 if(isset($userDetails) && (isset($institution) && $institution==1)){
			$this->Session->setFlash(__( "You have assigend with Institution '".$userDetails['UserDetail']['first_name']." ".$userDetails['UserDetail']['last_name']."'."), 'good', array('type' => 'defalut'), 'good'); 
		 }
		 
	}else if((!isset( $couponInsData['Coupon']['percentage']) ||  $couponInsData['Coupon']['percentage'] ==0) && (!empty($couponInsData['Coupon']['flat']) && ($couponInsData['Coupon']['flat'] > 0)) && (($disamount >= 0) && ($disamount !='free') && ($disamount !='a') && ($disamount !='-1'))){
	 
	 $this->Session->setFlash(__( " Flat discount (".round($this->common->currencyConvertor($couponInsData['Coupon']['flat'])). ") has applied successfully on total amount."), 'success');
	 
	 if(isset($userDetails) && (isset($institution) && $institution==1)){
        $this->Session->setFlash(__( "You have assigend with Institution '".$userDetails['UserDetail']['first_name']." ".$userDetails['UserDetail']['last_name']."'."), 'good', array('type' => 'defalut'), 'good'); 

		 }
	
	}else if(((isset( $couponInsData['Coupon']['percentage']) &&  $couponInsData['Coupon']['percentage'] >0) || (empty($couponInsData['Coupon']['flat']) || ($couponInsData['Coupon']['flat'] == 0))) && (($disamount >= 0) && ($disamount !='free') && ($disamount !='a') && ($disamount !='-1'))){
	
	// echo $disamount ; die;
	
	  $this->Session->setFlash(__( $couponInsData['Coupon']['percentage']." % Discount has applied successfully on total amount."), 'success');
	  
	  if(isset($userDetails) && (isset($institution) && $institution==1)){
        $this->Session->setFlash(__( "You have assigend with Institution '".$userDetails['UserDetail']['first_name']." ".$userDetails['UserDetail']['last_name']."'."), 'good', array('type' => 'defalut'), 'good'); 

		 }
	}else if($disamount =='free'){ 	//echo $disamount; die;
	    $disamount ='free';
	   $this->set('disamount',$disamount);
	  $this->Session->write('disamount', $disamount);
	 $this->Session->setFlash(__( "Congrats! you have gotten full discount."), 'good');
	    
	}else{
	
	$this->Session->setFlash(__( "No discount available on this coupon."), 'error');
	}
  
	//$this->Session->setFlash(__( " Flat discount (".$couponInsData['Coupon']['flat']. ") has applied successfully on total amount.", array('type' => 'flat')), 'success');
	
	$this->render('plansummary_new');
	//$this->redirect('/plans/plansummary/'.$this->request->data['plan']['id'].'/'.$disamount);	
	}else{
	$this->Session->write('disamount', '');
	$this->Session->setFlash(__('Invalid Coupon!'), 'error');
	//$this->redirect(array('action' => 'plansummary',$this->request->data['plan']['id']));        
	}
					
	}
	
	//pr($this->request->data); die;
	
		/*-----------------------------------Coupon Code End----------------------------------------------------*/
		
		if(isset($this->request->data['plan']['paypal'])){
		
			 if(isset($this->request->data['plan']['discount']) && $this->request->data['plan']['discount'] > 0){
			 if(isset($vat) && !empty($vat)){
			 $payment = round($this->common->currencyConvertor($this->Session->read('disamount') + $this->Session->read('disamount') * $vat /100),2);
			 }else{
			 $payment = round($this->common->currencyConvertor($this->Session->read('disamount')),2);			 
			 }
			 }else{
			 if(isset($vat) && !empty($vat)){
			 $payment =  round($this->common->currencyConvertor($this->request->data['plan']['totalfees']+ $this->request->data['plan']['totalfees'] * $vat /100),2);
			 }else{
			 $payment =  round($this->common->currencyConvertor($this->request->data['plan']['totalfees']),2);			 
			 }
			 }
			 
			// pr($payment); die;
			
			 if(isset($this->request->data['plan']['associated_user_id']) && !empty($this->request->data['plan']['associated_user_id'])){
			 $associated_id = $this->request->data['plan']['associated_user_id'];
			 }
			 if(isset($this->request->data['plan']['coup_id']) && !empty($this->request->data['plan']['coup_id'])){
			 $coupn_id = $this->request->data['plan']['coup_id'];
			 }
			 
 			 foreach($UserPlanData as $ppid){
			  $pp_id[] = $ppid['UserPlan']['id'];
			 }
			 
			$set_pids =  implode(',',$pp_id); 
			
			
			 
			 
 //pr($associated_id); die;
					 $gateway_options = array(
						'notify_url' => array(
							'controller' => 'plans',
							'action' => 'paymentsuccess',
						),
						'cancel_return' => array(
							'controller' => 'plans',
							'action' => 'paymentcancel?id='.$userId ,
						),
						'return' => array(
							'controller' => 'plans',
							'action' => 'thanks_feature',
						),
						'item_name' =>'IdeasCast',
							'currency_code' =>$currency_code,
							'amount' =>$payment,
							'item_number' =>'1',
						); 
						
						//for paypal auth
						

						
						if(isset($coupn_id) && !empty($coupn_id) && (empty($associated_id))){
						$gateway_options['custom'] = $userId.",".$coupn_id.",'=".$set_pids;
						}else if((isset($coupn_id) && !empty($coupn_id)) && (isset($associated_id) && !empty($associated_id))){
						$gateway_options['custom'] = $userId.",".$coupn_id.",".$associated_id.",'=".$set_pids;;
						}
						else{						
						$gateway_options['custom'] = $userId.",'=".$set_pids;;
						}
						
						
						//pr($gateway_options['custom']);
						/*---------------------Paypal recurring parameters ---------------------------*/
						
						if($UserPlanData['0']['UserPlan']['plan_type'] == 4){
						
						
						$gateway_options['a1'] = $payment;
						$gateway_options['p1'] = "1";
						$gateway_options['t1'] = "Y";							
						
						$gateway_options['recurring'] = "1";

						}else if($UserPlanData['0']['UserPlan']['plan_type'] == 1){
						
						
						$gateway_options['a1'] = $payment;
						$gateway_options['p1'] = "1";
						$gateway_options['t1'] = "M";							
						
						$gateway_options['recurring'] = "1";

						}	
						
						/*---------------------Paypal recurring parameters ---------------------------*/
						//print_r($gateway_options); die;
						
						$this->set('gateway_options', $gateway_options);
						$this->set('amount', $payment);
						$this->layout = false;
						$this->render('paypal');
						
				}		
		 } 
		 
		 	   
	}	
	
	
	
	
    public function index() {
	   $this->layout = "plan";
       $userId = $this->params['pass']['0'];
	   
	   $this->UserPlan->deleteAll(array('UserPlan.user_id' => $userId,'UserPlan.is_active' => 0,'UserPlan.trans_id' => 0));
	   
       $planList =  $this->Plan->find("all", array('fields'=>array('Plan.*'),'conditions'=>array('Plan.status' => 1,'Plan.id'=>array(4,7))));
	   
       $this->set('planList', $planList);
	   if ($this->request->is('post') || $this->request->is('put') && $userId > 0) {
	   
				if(isset($this->request->data['add_plan']) && (isset($this->request->data['plan']) && !empty($this->request->data['plan']))){			
				foreach($this->request->data['plan'] as $key => $val){
					if(isset($val['m']) && !empty($val['plantype'])){
					
					 $this->request->data['UserPlan']['plan_id'] = $key;
						 $this->request->data['UserPlan']['plan_type'] = $val['plantype'];						 
						 if($val['plantype']=='1'){
							$this->request->data['UserPlan']['start_date'] = strtotime(date('Y-m-d'));
							$this->request->data['UserPlan']['end_date'] =  strtotime(date("Y-m-d", strtotime("+1 month")));
						 }else if($val['plantype']=='4'){
							 $this->request->data['UserPlan']['start_date'] = strtotime(date('Y-m-d'));
							$this->request->data['UserPlan']['end_date'] =  strtotime(date("Y-m-d", strtotime("+ 365 day")));
						 }else{
							 $this->request->data['UserPlan']['start_date'] = '';
							 $this->request->data['UserPlan']['end_date'] = '';
						 }
					}else{
						unset($this->request->data['UserPlan']['plan_id']);
						unset($this->request->data['UserPlan']['plan_type']);
						unset($this->request->data['UserPlan']['start_date']);
						unset($this->request->data['UserPlan']['end_date']);
						unset($this->request->data['UserPlan']['user_id']);						
					}
					
					$this->request->data['UserPlan']['user_id'] = $userId;	
					
					if(isset($this->request->data['UserPlan']['plan_id'])){
					$UserPlandata[] = $this->request->data['UserPlan'];
					}
					
				}
				    if(!isset($UserPlandata)){
					$UserPlandata = array();
					}
				
					//echo '<pre>';print_r($UserPlandata);die;
					$i ='0';
					foreach($UserPlandata as $pdats){
					if($pdats['plan_id'] !='0'){
					
					 $i++;
					}
					}
					
			    if($i <1){		
				$this->Session->setFlash(__('Please choose one of feature.'));				
				return $this->redirect(array('controller' => 'plans', 'action' => 'index', $userId, 'admin' => false));
				 
				}
					
				if($this->UserPlan->saveAll($UserPlandata)){
					 return $this->redirect(array('controller' => 'plans', 'action' => 'plansummary', $userId, 'admin' => false));
				}else{
					 $this->Session->setFlash(__('The user plan could not be saved. Please, try again.'));
				}
			}
			else{
			        
					$userData = $this->User->find('all',array('fields'=>array('User.email,User.activation_key, UserDetail.first_name,UserDetail.last_name'),'conditions'=>array('User.id' => $userId)));
					$userId = $this->params['pass']['0'];
				
					$activatiinHash = $userData[0]['User']['activation_key'];
					$this->request->data['UserDetail']['first_name'] = $userData[0]['UserDetail']['first_name'];
					$this->request->data['UserDetail']['last_name'] = $userData[0]['UserDetail']['last_name'];
					$this->request->data['User']['email'] = $userData[0]['User']['email'];
					$sendMail = $this->__sendEmailConfirm($this->request->data,$userId, $activatiinHash);
					
					return $this->redirect(array('controller' => 'plans', 'action' => 'thanks', $userId,'sample', 'admin' => false));
					
					
                   // $this->Session->setFlash(__('You have been sent an activation email'));
                    //return $this->redirect(SITEURL);
			}	
	   } 
	   
    }
	
	public function plansummary(){  

		$userId = $this->params['pass']['0'];
		
        $this->layout = "plan";
		
		$this->UserPlan->bindModel(array('belongsTo'=>array('Plan'=>array('foreignKey' => false,'conditions'=>array('Plan.id=UserPlan.plan_id')))));	
		$UserPlanData = $this->UserPlan->find('all',array('fields'=>array('UserPlan.*','Plan.*'),'conditions'=>array('UserPlan.user_id'=> $userId,'Plan.status' => 1,'UserPlan.is_active' => 0)));
		//pr($UserPlanData);
		$currency_codes = $this->Currency->find('first',array('fields'=>array('Currency.*'),'conditions'=>array('Currency.status'=> 1)));
		
			$ud = $this->UserDetail->find('first',array('fields'=>array('UserDetail.*'),'conditions'=>array('UserDetail.user_id' => $this->request->params['pass']['0'])));
			
			if(isset($ud['UserDetail']['country_id']) && !empty($ud['UserDetail']['country_id'])){
			 $vat =  $this->Common->getVat($ud['UserDetail']['country_id']); 
			 		$this->set('vat', $vat);
			}
		
		//pr($currency_codes);
		 $currency_code = $currency_codes['Currency']['sign']; 
		 $currency_id = $currency_codes['Currency']['id']; 
		
		
		$this->set('UserPlanData', $UserPlanData);
		 if ($this->request->is('post') || $this->request->is('put')) { 
		 
	
	/*-----------------------------------Coupon Code Start----------------------------------------------------*/
	
	if(isset($this->request->data['Coupon']['name']) && !empty($this->request->data['Coupon']['name'])){
			
	$coupon = $this->request->data['Coupon']['name'];
	
	$membership_code = $this->UserDetail->find('first',array('fields'=>array('UserDetail.membership_code'),'conditions'=>array('UserDetail.user_id' => $this->request->params['pass']['0'])));
	
	if(isset($membership_code['UserDetail']['membership_code']) && !empty($membership_code['UserDetail']['membership_code'])){
	$this->request->data['UserInstitution']['membership_code']	= $membership_code['UserDetail']['membership_code'] ;
	}else{
	unset($this->request->data['UserInstitution']['membership_code']);
	}
	
	//pr($this->request->data['UserInstitution']['membership_code']);
	
	if(isset($this->request->data['UserInstitution']['membership_code']) && !empty($this->request->data['UserInstitution']['membership_code'])){
	$memcode = $this->request->data['UserInstitution']['membership_code'];

	$userData = $this->UserInstitution->find('first',array('fields'=>array('UserInstitution.*'),'conditions'=>array('UserInstitution.membership_code' => $memcode,'UserInstitution.end >=' => date('Y-m-d 12:00:00'),'UserInstitution.start <=' => date('Y-m-d 12:00:00'))));
	
	if(isset($userData) && !empty($userData)){
	$userDetails = $this->UserDetail->find('first',array('fields'=>array('UserDetail.*'),'conditions'=>array('UserDetail.user_id' => $userData['UserInstitution']['user_id'])));
	
		$userStatus = $this->User->find('first',array('fields'=>array('User.status'),'conditions'=>array('User.id' => $userData['UserInstitution']['user_id'])));
	}
	}
	
	if((isset($userData) && !empty($userData)) && (!empty($userStatus['User']['status']))){ //pr($userStatus);
	$associated_user_id = $userData['UserInstitution']['user_id'];
	}else{
	$associated_user_id = 0;
	}
	
	$couponData = $this->Coupon->find('first',array('fields'=>array('Coupon.*'),'conditions'=>array('Coupon.status' => 1,'Coupon.name' => $coupon,'Coupon.end_time >=' => date('Y-m-d 12:00:00'),'Coupon.start_time <=' => date('Y-m-d 12:00:00'),'CAST(Coupon.useable AS SIGNED) > CAST(Coupon.used AS SIGNED)')));
	
	if(isset($couponData) && !empty($couponData)){
	$institution = $couponData['Coupon']['is_institution'];
	}
	
	
	
	if((isset($this->request->data['UserInstitution']['membership_code']) && !empty($this->request->data['UserInstitution']['membership_code'])) && (isset($institution) && $institution==1)){
	
	$couponInsData = $this->Coupon->find('first',array('fields'=>array('Coupon.*'),'conditions'=>array('Coupon.status' => 1,'Coupon.name' => $coupon,'FIND_IN_SET(\''. $associated_user_id .'\',Coupon.user_id)','Coupon.end_time >=' => date('Y-m-d 12:00:00'),'Coupon.start_time <=' => date('Y-m-d 12:00:00'),'CAST(Coupon.useable AS SIGNED) > CAST(Coupon.used AS SIGNED)')));

	
	}else{
	$couponInsData = $couponData;
	
	}
	
	//pr($this->request->data); die;
	
	if(isset($couponInsData) && !empty($couponInsData)){
	$coupon_id = $couponInsData['Coupon']['id'];
	$coupon_user_id = $couponInsData['Coupon']['id'];
	$institution = $couponInsData['Coupon']['is_institution'];
	
	
	
		/*-----------------------------------if minimum total amount----------------------------------------------------*/
	
	if($couponInsData['Coupon']['on_amount'] > 0){
	
	if(isset($couponInsData['Coupon']['flat']) && $couponInsData['Coupon']['flat'] > 0){ 
	if((!empty($couponInsData['Coupon']['flat']) && ($couponInsData['Coupon']['flat'] > 0 && $couponInsData['Coupon']['flat'] <= $this->request->data['plan']['totalfees'])) && ($this->request->data['plan']['totalfees'] >= $couponInsData['Coupon']['on_amount'] )){
	
	$disamount =  $this->request->data['plan']['totalfees'] -  $couponInsData['Coupon']['flat'];

	if($disamount==0 || $disamount<0.01){
	$disamount ="free";
	}
	$this->Session->write('disamount', $disamount);
	}else{
	$disamount = -1;
	$this->Session->write('disamount', $disamount);
	}
	}else{
	$disamount = 'a';
		$this->Session->write('disamount', $disamount);
	}
		

	
	if((isset( $couponInsData['Coupon']['percentage']) &&  $couponInsData['Coupon']['percentage'] >0 )  && ($this->request->data['plan']['totalfees'] >= $couponInsData['Coupon']['on_amount'] ) && ($disamount !='-1')){
	
	 if($disamount=='free'){
	  $disamount ="free";
	  }else{
	   
	  
	   if($disamount !='0' && $disamount !='free' && $disamount !='a'){
	   
	   $per_amount = $disamount *  $couponInsData['Coupon']['percentage']/100;
	   $disamount = $disamount - $per_amount;
	   }else{
	    $per_amount = $this->request->data['plan']['totalfees'] *  $couponInsData['Coupon']['percentage']/100;
		$disamount = $this->request->data['plan']['totalfees'] - $per_amount;
	   }
	      
		
	 
	  	if($disamount==0 || $disamount<0.01){
		$disamount ="free";
		}
		
	  $this->Session->write('disamount', $disamount);	
	}
	
	}else if((isset( $couponInsData['Coupon']['percentage']) &&  $couponInsData['Coupon']['percentage'] >0 ) && (!isset($couponInsData['Coupon']['flat']) || $couponInsData['Coupon']['flat'] ==0)  && ($this->request->data['plan']['totalfees'] >= $couponInsData['Coupon']['on_amount'] ) && ($disamount !='-1') ){
	  
	 if($disamount=='free'){
	  $disamount ="free";
	  }else{
	   
	  
	   if($disamount !='0' && $disamount !='free' && $disamount !='a'){
	   
	   $per_amount = $disamount *  $couponInsData['Coupon']['percentage']/100;
	   $disamount = $disamount - $per_amount;
	   }else{
	    $per_amount = $this->request->data['plan']['totalfees'] *  $couponInsData['Coupon']['percentage']/100;
		$disamount = $this->request->data['plan']['totalfees'] - $per_amount;
	   }
	 
	  	if($disamount==0 || $disamount<0.01){
		$disamount ="free";
		}
		
	  $this->Session->write('disamount', $disamount);	
	}
	
	}
	}else{
	
	/*-----------------------------------if minimum total amount not set----------------------------------------------------*/
	
	if(isset($couponInsData['Coupon']['flat']) && $couponInsData['Coupon']['flat'] > 0){ 
	if((!empty($couponInsData['Coupon']['flat']) && ($couponInsData['Coupon']['flat'] > 0 && $couponInsData['Coupon']['flat'] <= $this->request->data['plan']['totalfees']))){  
	$disamount =  $this->request->data['plan']['totalfees'] -  $couponInsData['Coupon']['flat'];
	
	if($disamount==0 || $disamount<0.01){
	$disamount ="free";
	}
	$this->Session->write('disamount', $disamount);
	}else{
	$disamount = -1;
	$this->Session->write('disamount', $disamount);
	}
	}else{ 
	
	    $disamount = 'a';
		$this->Session->write('disamount', $disamount);
		
	}
	
	
	if((isset( $couponInsData['Coupon']['percentage']) &&  $couponInsData['Coupon']['percentage'] >0  && ($disamount !='-1'))){
	
	  if($disamount=='free'){
	  $disamount ="free";
	  }else{
	   
	  
	   if($disamount !='0' && $disamount !='free' && $disamount !='a'){
	   
	   $per_amount = $disamount *  $couponInsData['Coupon']['percentage']/100;
	   $disamount = $disamount - $per_amount;
	   }else{
	    $per_amount = $this->request->data['plan']['totalfees'] *  $couponInsData['Coupon']['percentage']/100;
		$disamount = $this->request->data['plan']['totalfees'] - $per_amount;
	   }
	 
	  	if($disamount==0 || $disamount<0.01){
		$disamount ="free";
		}
	} 
			
	  $this->Session->write('disamount', $disamount); 
	
	}else if(((isset( $couponInsData['Coupon']['percentage']) &&  $couponInsData['Coupon']['percentage'] >0 ) || ((!isset($couponInsData['Coupon']['flat']) || $couponInsData['Coupon']['flat'] ==0)))  && ($disamount !='-1')){
	 
	 if($disamount=='free'){
	  $disamount ="free";
	  }else{
	   
	  
	   if($disamount !='0' && $disamount !='free' && $disamount !='a'){
	   
	   $per_amount = $disamount *  $couponInsData['Coupon']['percentage']/100;
	   $disamount = $disamount - $per_amount;
	   }else{
	    $per_amount = $this->request->data['plan']['totalfees'] *  $couponInsData['Coupon']['percentage']/100;
		$disamount = $this->request->data['plan']['totalfees'] - $per_amount;
	   }
	 
	  	if($disamount==0 || $disamount<0.01){
		$disamount ="free";
		}
		
	  $this->Session->write('disamount', $disamount);	
	}
	
	}
	
	}
	
	   if($disamount!='free'){
	   if(isset($vat)){
	   $vatn = $disamount * $vat/100;
	   $vm = $disamount + $vatn ; 
	   $this->set('vatamount',$vm);	 
	   }
	   }
 
		//echo  $disamount ; //die;
	
	 $this->set('disamount',$disamount);
	 
	 $this->set('coup_id',$couponInsData['Coupon']['id']);
	 $this->set('associated_user_id',$associated_user_id);
	 

	
	
	
	if((isset( $couponInsData['Coupon']['percentage']) &&  $couponInsData['Coupon']['percentage'] >0) && (!empty($couponInsData['Coupon']['flat']) && ($couponInsData['Coupon']['flat'] > 0)) && (($disamount >= 0) && ($disamount !='free') && ($disamount !='a') && ($disamount !='-1'))){
	
	   $this->Session->setFlash(__( " Flat discount (". round($this->common->currencyConvertor($couponInsData['Coupon']['flat'])). ") on total amount + ".$couponInsData['Coupon']['percentage']." % on rest discounted amount has applied successfully."), 'success');
	   	 
		 if(isset($userDetails) && (isset($institution) && $institution==1)){
			$this->Session->setFlash(__( "You have assigend with Institution '".$userDetails['UserDetail']['first_name']." ".$userDetails['UserDetail']['last_name']."'."), 'good', array('type' => 'defalut'), 'good'); 
		 }
		 
	}else if((!isset( $couponInsData['Coupon']['percentage']) ||  $couponInsData['Coupon']['percentage'] ==0) && (!empty($couponInsData['Coupon']['flat']) && ($couponInsData['Coupon']['flat'] > 0)) && (($disamount >= 0) && ($disamount !='free') && ($disamount !='a') && ($disamount !='-1'))){
	 
	 $this->Session->setFlash(__( " Flat discount (".round($this->common->currencyConvertor($couponInsData['Coupon']['flat'])). ") has applied successfully on total amount."), 'success');
	 
	 if(isset($userDetails) && (isset($institution) && $institution==1)){
        $this->Session->setFlash(__( "You have assigend with Institution '".$userDetails['UserDetail']['first_name']." ".$userDetails['UserDetail']['last_name']."'."), 'good', array('type' => 'defalut'), 'good'); 

		 }
	
	}else if(((isset( $couponInsData['Coupon']['percentage']) &&  $couponInsData['Coupon']['percentage'] >0) || (empty($couponInsData['Coupon']['flat']) || ($couponInsData['Coupon']['flat'] == 0))) && (($disamount >= 0) && ($disamount !='free') && ($disamount !='a') && ($disamount !='-1'))){
	
	// echo $disamount ; die;
	
	  $this->Session->setFlash(__( $couponInsData['Coupon']['percentage']." % Discount has applied successfully on total amount."), 'success');
	  
	  if(isset($userDetails) && (isset($institution) && $institution==1)){
        $this->Session->setFlash(__( "You have assigend with Institution '".$userDetails['UserDetail']['first_name']." ".$userDetails['UserDetail']['last_name']."'."), 'good', array('type' => 'defalut'), 'good'); 

		 }
	}else if($disamount =='free'){ 	//echo $disamount; die;
	    $disamount ='free';
	   $this->set('disamount',$disamount);
	  $this->Session->write('disamount', $disamount);
	 $this->Session->setFlash(__( "Congrats! you have gotten full discount."), 'good');
	    
	}else{
	
	$this->Session->setFlash(__( "No discount available on this coupon."), 'error');
	}
  
	//$this->Session->setFlash(__( " Flat discount (".$couponInsData['Coupon']['flat']. ") has applied successfully on total amount.", array('type' => 'flat')), 'success');
	
	$this->render('plansummary');
	//$this->redirect('/plans/plansummary/'.$this->request->data['plan']['id'].'/'.$disamount);	
	}else{
	$this->Session->write('disamount', '');
	$this->Session->setFlash(__('Invalid Coupon!'), 'error');
	//$this->redirect(array('action' => 'plansummary',$this->request->data['plan']['id']));        
	}
					
	}
	
	//pr($this->request->data); die;
	
		/*-----------------------------------Coupon Code End----------------------------------------------------*/
		
		if(isset($this->request->data['plan']['paypal'])){
		
			 if(isset($this->request->data['plan']['discount']) && $this->request->data['plan']['discount'] > 0){
			 if(isset($vat) && !empty($vat)){
			 $payment = round($this->common->currencyConvertor($this->Session->read('disamount') + $this->Session->read('disamount') * $vat /100),2);
			 }else{
			 $payment = round($this->common->currencyConvertor($this->Session->read('disamount')),2);			 
			 }
			 }else{
			 if(isset($vat) && !empty($vat)){
			 $payment =  round($this->common->currencyConvertor($this->request->data['plan']['totalfees']+ $this->request->data['plan']['totalfees'] * $vat /100),2);
			 }else{
			 $payment =  round($this->common->currencyConvertor($this->request->data['plan']['totalfees']),2);			 
			 }
			 }
			 
			// pr($payment); die;
			
			 if(isset($this->request->data['plan']['associated_user_id']) && !empty($this->request->data['plan']['associated_user_id'])){
			 $associated_id = $this->request->data['plan']['associated_user_id'];
			 }
			 if(isset($this->request->data['plan']['coup_id']) && !empty($this->request->data['plan']['coup_id'])){
			 $coupn_id = $this->request->data['plan']['coup_id'];
			 }
			 
			 foreach($UserPlanData as $ppid){
			  $pp_id[] = $ppid['UserPlan']['id'];
			 }
			 
			$set_pids =  implode(',',$pp_id); 
			
			
			 
			 
 //pr($associated_id); die;
					 $gateway_options = array(
						'notify_url' => array(
							'controller' => 'plans',
							'action' => 'paymentsuccess',
						),
						'cancel_return' => array(
							'controller' => 'plans',
							'action' => 'paymentcancel?id='.$userId ,
						),
						'return' => array(
							'controller' => 'plans',
							'action' => 'thanks',
						),
						'item_name' =>'IdeasCast',
							'currency_code' =>$currency_code,
							'amount' =>$payment,
							'item_number' =>'1',
						); 
						
						//for paypal auth
						

						
						if(isset($coupn_id) && !empty($coupn_id) && (empty($associated_id))){
						$gateway_options['custom'] = $userId.",".$coupn_id.",'=".$set_pids;
						}else if((isset($coupn_id) && !empty($coupn_id)) && (isset($associated_id) && !empty($associated_id))){
						$gateway_options['custom'] = $userId.",".$coupn_id.",".$associated_id.",'=".$set_pids;;
						}
						else{						
						$gateway_options['custom'] = $userId.",'=".$set_pids;;
						}
						

						
					//	pr($gateway_options['custom']); die;
						/*---------------------Paypal recurring parameters ---------------------------*/
						
						$gateway_options['a1'] = $payment;
						$gateway_options['p1'] = "1";
						$gateway_options['t1'] = "Y";						
						
						$gateway_options['recurring'] = "1";						
						
						/*---------------------Paypal recurring parameters ---------------------------*/
						//print_r($gateway_options); die;
						
						$this->set('gateway_options', $gateway_options);
						$this->set('amount', $payment);
						$this->layout = false;
						$this->render('paypal');
						
				}		
		 } 
	}

    public function __sendEmailConfirmWithPayment($useData, $lastInsertID, $activatiinHash) {
		//echo '<pre>';print_r($useData);die;
		if (!count($useData['UserDetail'])) {
            debug(__METHOD__ . " failed to retrieve User data for user.id: {$user_id}");
            return false;
        }
        $activate_url = SITEURL . 'users/activate/' . $lastInsertID . '/' . $activatiinHash;
        $name = $useData['UserDetail']['first_name'] . ' ' . $useData['UserDetail']['last_name'];
		$transctionId = $useData['UserTransctionDetail']['txn_id'];
		$amount = $useData['UserTransctionDetail']['amount'];
        $email = new CakeEmail();
        $email->config('Smtp');
        $email->from(array(ADMIN_FROM_EMAIL => SITENAME));
        $email->to($useData['User']['email']);
        $email->subject(SITENAME . ': Please confirm your email address');
        $email->template('user_confirm_payment');
        $email->emailFormat('html');
        $email->viewVars(array('activate_url' => $activate_url, 'name' => $name,'transctionId' => $transctionId,'amount' => $amount));
        return $email->send();
    }
	
    public function __sendPaymentFeature($useData, $lastInsertID, $activatiinHash) {
		//echo '<pre>';print_r($useData);die;
		if (!count($useData['UserDetail'])) {
            debug(__METHOD__ . " failed to retrieve User data for user.id: {$user_id}");
            return false;
        }
        $activate_url = SITEURL . 'users/activate/' . $lastInsertID . '/' . $activatiinHash;
        $name = $useData['UserDetail']['first_name'] . ' ' . $useData['UserDetail']['last_name'];
		$transctionId = $useData['UserTransctionDetail']['txn_id'];
		$amount = $useData['UserTransctionDetail']['amount'];
        $email = new CakeEmail();
        $email->config('Smtp');
        $email->from(array(ADMIN_FROM_EMAIL => SITENAME));
        $email->to($useData['User']['email']);
        $email->subject(SITENAME . ': Thanks for adding new features');
        $email->template('user_confirm_payment_feature');
        $email->emailFormat('html');
        $email->viewVars(array('name' => $name,'transctionId' => $transctionId,'amount' => $amount));
        return $email->send();
    }	
	
	
	    public function __sendUpgradeWithPayment($useData, $lastInsertID, $activatiinHash) {
		//echo '<pre>';print_r($useData);die;
		if (!count($useData['UserDetail'])) {
            debug(__METHOD__ . " failed to retrieve User data for user.id: {$user_id}");
            return false;
        }
        $activate_url = SITEURL . 'users/activate/' . $lastInsertID . '/' . $activatiinHash;
        $name = $useData['UserDetail']['first_name'] . ' ' . $useData['UserDetail']['last_name'];
		$transctionId = $useData['UserTransctionDetail']['txn_id'];
		$amount = $useData['UserTransctionDetail']['amount'];
        $email = new CakeEmail();
        $email->config('Smtp');
        $email->from(array(ADMIN_FROM_EMAIL => SITENAME));
        $email->to($useData['User']['email']);
        $email->subject(SITENAME . ': Add Features');
        $email->template('user_upgrade_payment');
        $email->emailFormat('html');
        $email->viewVars(array('activate_url' => $activate_url, 'name' => $name,'transctionId' => $transctionId,'amount' => $amount));
        return $email->send();
    }
	
	public function __sendEmailConfirm($useData, $lastInsertID, $activatiinHash) {
		//echo '<pre>';print_r($useData);die;
		if (!count($useData['UserDetail'])) {
            debug(__METHOD__ . " failed to retrieve User data for user.id: {$user_id}");
            return false;
        }
        $activate_url = SITEURL . 'users/activate/' . $lastInsertID . '/' . $activatiinHash;
        $name = $useData['UserDetail']['first_name'] . ' ' . $useData['UserDetail']['last_name'];
        $email = new CakeEmail();
        $email->config('Smtp');
        $email->from(array(ADMIN_FROM_EMAIL => SITENAME));
        $email->to($useData['User']['email']);
        $email->subject(SITENAME . ': Please confirm your email address');
        $email->template('user_confirm');
        $email->emailFormat('html');
        $email->viewVars(array('activate_url' => $activate_url, 'name' => $name));
        return $email->send();
    }

	
	  function thanks_feature(){
		  $this->layout = "inner";
		  		$this->set('crumb', [ 'last' => [ 'Thanks' ] ] );
		
		 if(isset($_POST) && !empty($_POST)){ 
		 
		   if(!isset($_POST['custom'])){
		   
		   $cus = $this->request->params['pass']['0'];
		   }else{
		   $cus = $_POST['custom'];
		   }	 
		 	
            $plan_tt_id = explode('=',$cus);
			
    		$das = str_getcsv($cus , ",", "'");	
			
//			str_getcsv("182,183,181,'=120,130", ",", "'"); 
			

			
		    $id = $das['0']; 
			// pr($_POST); die;
			
			
			
			
			$serializedData = print_r($_POST, true); 
			$content = "Response:  Online -- ".date('d-m-Y H:i:s',time())."\n"."----------------------------------------------------------------------\n".$serializedData."\n----------------------------------------------------------------------\n";
			$file = DOC_ROOT."/paypal/paypal_log.txt";
			$fp = fopen($file,"a+");
			file_put_contents($file, $content, FILE_APPEND);
			fclose($fp);
			//$id = $_POST['custom']; // Get the Id 
			
			if(isset($_POST['payment_date']) && !empty($_POST['payment_date'])){
			$date_of_payment = date('Y-m-d',strtotime($_POST['payment_date'])); 
			}
			
			
		//	if($_POST['payment_status'] == 'Completed' || $_POST['payment_status'] == 'Pending' || $_POST['payment_status'] == 'Processed'){
		
			
			if(!empty($_POST['auth'])){
			
			$this->request->data['UserTransctionDetail']['user_id'] = $id ;
			
			if(isset($_POST['subscr_id'])){
				$this->request->data['UserTransctionDetail']['txn_id'] = $_POST['subscr_id'];
				$this->request->data['UserTransctionDetail']['subscr_id'] = $_POST['subscr_id'];
			}else{
				$this->request->data['UserTransctionDetail']['txn_id'] = $_POST['txn_id'];
			}
			
			
			if(!empty($_POST['recurring'])){
			
		//	$this->request->data['UserTransctionDetail']['txn_id'] = $_POST['payer_id'].rand().rand();
			$this->request->data['UserTransctionDetail']['rec_period'] = $_POST['period3'];
			
			//$this->request->data['UserTransctionDetail']['amount'] = $_POST['mc_gross'];
			
			if(isset($das['1']) && !empty($das['1']) && is_numeric($das['1'])){
			$this->request->data['UserTransctionDetail']['coupon_id'] = $das['1'];
			}
			if(isset($das['2']) && !empty($das['2']) && is_numeric($das['2'])){
			$this->request->data['UserTransctionDetail']['institution_id'] = $das['2'];
			}

			//$ddd = explode('=',"182,183,181,'=120,130");

			//$dd = str_getcsv("182,183,181,'=120,130", ",", "'"); 

			if(isset($plan_tt_id['1']) && !empty($plan_tt_id['1'])){
			$this->request->data['UserTransctionDetail']['plan_id'] = $plan_tt_id['1'];	
			$pids = $plan_tt_id['1'];	
			
			}
			
			
			
			
			$this->request->data['UserTransctionDetail']['amount'] = $_POST['mc_amount3'];
			
			$this->request->data['UserTransctionDetail']['payment_date'] = strtotime(date('m/d/Y h:i:s', time()));
			$this->request->data['UserTransctionDetail']['mc_currency'] = $_POST['mc_currency'];
			
			$this->request->data['UserTransctionDetail']['is_recurring'] = $_POST['recurring'];
			 	
			
		//	$this->request->data['UserTransctionDetail']['payment_status'] = $_POST['payment_status'];
		//	$this->request->data['UserTransctionDetail']['verify_sign'] = $_POST['verify_sign'];
		
			$this->request->data['UserTransctionDetail']['auth'] = $_POST['auth'];
			
			}else{
			
			if($_POST['payment_status'] == 'Completed' || $_POST['payment_status'] == 'Pending' || $_POST['payment_status'] == 'Processed'){
			
			
			
			if(isset($das['1']) && !empty($das['1'])){
			$this->request->data['UserTransctionDetail']['coupon_id'] = $das['1'];
			}
			if(isset($das['2']) && !empty($das['2'])){
			$this->request->data['UserTransctionDetail']['institution_id'] = $das['2'];
			}
			
			$this->request->data['UserTransctionDetail']['user_id'] = $_POST['custom'];
			$this->request->data['UserTransctionDetail']['txn_id'] = $_POST['txn_id'];
			$this->request->data['UserTransctionDetail']['amount'] = $_POST['mc_gross'];
			$this->request->data['UserTransctionDetail']['payment_date'] = strtotime(date('m/d/Y h:i:s', time()));
			$this->request->data['UserTransctionDetail']['mc_currency'] = $_POST['mc_currency'];
			$this->request->data['UserTransctionDetail']['payment_status'] = $_POST['payment_status'];
			$this->request->data['UserTransctionDetail']['verify_sign'] = $_POST['verify_sign'];
			$this->request->data['UserTransctionDetail']['auth'] = $_POST['auth'];
			
			}else{
			return $this->redirect(SITEURL.'/plans/paymentcancel_plan/?id='.$id );
			}
			}
			//pr($this->request->data['UserTransctionDetail']); die;
			
			//pr($this->request->data); die;
			
			$flag = 0; 
			
			
			if($flag == 1 && $this->UserTransctionDetail->save($this->request->data)){
					
					$userData = $this->User->find('all',array('fields'=>array('User.email,User.activation_key, UserDetail.first_name,UserDetail.last_name'),'conditions'=>array('User.id' => $id )));
					//echo '<pre>';print_r($userData);die;
					
					//$tran_id = $this->UserTransctionDetail->getLastInsertID();
					$tran_id = $this->request->data['UserTransctionDetail']['subscr_id'];	

					
						$this->UserPlan->updateAll( array( 'UserPlan.is_active' => 1,'UserPlan.trans_id' => '"'.$tran_id .'"'),array('UserPlan.user_id' => $id , "UserPlan.id IN($pids)"));							
					
					$activatiinHash = $userData[0]['User']['activation_key'];
					$this->request->data['UserDetail']['first_name'] = $userData[0]['UserDetail']['first_name'];
					$this->request->data['UserDetail']['last_name'] = $userData[0]['UserDetail']['last_name'];
					$this->request->data['UserDetail']['txn_id'] = $_POST['payer_id'];
					$this->request->data['UserDetail']['amount'] = $_POST['mc_amount3'];
					
					$this->request->data['User']['email'] = $userData[0]['User']['email'];
					$sendMail = $this->__sendUpgradeWithPayment($this->request->data,$id , $activatiinHash);
                  //  $this->Session->setFlash(__('You have been sent an activation email'));
				}	
					
			}else if(isset($_POST['data']['plan']['free'])){
			
			$this->request->data['UserTransctionDetail']['user_id'] = $id ;
			$this->request->data['UserTransctionDetail']['txn_id'] = rand().time();
			
			$this->request->data['UserTransctionDetail']['amount'] = '0';			
			$this->request->data['UserTransctionDetail']['payment_date'] = strtotime(date('m/d/Y h:i:s', time()));
			$this->request->data['UserTransctionDetail']['mc_currency'] = 'USD';
			$this->request->data['UserTransctionDetail']['payment_status'] = 'Completed';
			
	
			$this->request->data['UserTransctionDetail']['auth'] = base64_encode(rand().time());
			
			//pr($this->request->data); die;
			
			
			if(isset($this->request->data['plan']['coup_id']) && !empty($this->request->data['plan']['coup_id'])){
			$this->request->data['UserTransctionDetail']['coupon_id'] =$this->request->data['plan']['coup_id'];
			}
			if(isset($this->request->data['plan']['associated_user_id']) && !empty($this->request->data['plan']['associated_user_id'] )){
			$this->request->data['UserTransctionDetail']['institution_id'] = $this->request->data['plan']['associated_user_id'];
			}
			
			
			if($this->UserTransctionDetail->save($this->request->data)){
					
					$userData = $this->User->find('all',array('fields'=>array('User.email,User.activation_key, UserDetail.first_name,UserDetail.last_name'),'conditions'=>array('User.id' => $id )));
					//echo '<pre>';print_r($userData);die;
					
					
						
						//$tran_id = $this->UserTransctionDetail->getLastInsertID();
						
						$tran_id = $this->request->data['UserTransctionDetail']['subscr_id'];
						
					
						$this->UserPlan->updateAll( array('UserPlan.is_active' => 1,'UserPlan.trans_id' => $tran_id),array('UserPlan.user_id' => $id ,'UserPlan.is_active' =>0,'Userplan.trans_id'=>0));						
						
						
					
					$activatiinHash = $userData[0]['User']['activation_key'];
					$this->request->data['UserDetail']['first_name'] = $userData[0]['UserDetail']['first_name'];
					$this->request->data['UserDetail']['last_name'] = $userData[0]['UserDetail']['last_name'];
					$this->request->data['UserDetail']['txn_id'] = time();
					$this->request->data['UserDetail']['amount'] = '0';
					
					$this->request->data['User']['email'] = $userData[0]['User']['email'];
					$sendMail = $this->__sendUpgradeWithPayment($this->request->data,$id , $activatiinHash);

				}			
			
			}			
			else{
					return $this->redirect(SITEURL.'/plans/paymentcancel_plan/?id='.$id );
			}
			
		 }
	}	
	
	
	
	function thanks(){
		  $this->layout = "plan";
		
		if(isset($this->request->params['pass']['1']) && $this->request->params['pass']['1']=='sample'){
		
		    $this->render('thanks_simple');
		
		   }
		   
		 
		
		 if(isset($_POST) && !empty($_POST)){ 
		 
		   if(!isset($_POST['custom'])){
		   
		   $cus = $this->request->params['pass']['0'];
		   }else{
		   $cus = $_POST['custom'];
		   }	 
		 		 
            $plan_tt_id = explode('=',$cus);
			
    		$das = str_getcsv($cus , ",", "'");	
			
		    $id = $das['0']; 
			// pr($_POST); die;
			$serializedData = print_r($_POST, true); 
			$content = "Response:  Online -- ".date('d-m-Y H:i:s',time())."\n"."----------------------------------------------------------------------\n".$serializedData."\n----------------------------------------------------------------------\n";
			$file = DOC_ROOT."/paypal/paypal_log.txt";
			$fp = fopen($file,"a+");
			file_put_contents($file, $content, FILE_APPEND);
			fclose($fp);
			//$id = $_POST['custom']; // Get the Id 
			
			if(isset($_POST['payment_date']) && !empty($_POST['payment_date'])){
			$date_of_payment = date('Y-m-d',strtotime($_POST['payment_date'])); 
			}
			
			
		//	if($_POST['payment_status'] == 'Completed' || $_POST['payment_status'] == 'Pending' || $_POST['payment_status'] == 'Processed'){
		
		   
		
			
			if(!empty($_POST['auth'])){
			
			$this->request->data['UserTransctionDetail']['user_id'] = $id ;
			$this->request->data['UserTransctionDetail']['txn_id'] = $_POST['payer_id'].rand().rand();
			$this->request->data['UserTransctionDetail']['rec_period'] = $_POST['period3'];
			
			//$this->request->data['UserTransctionDetail']['amount'] = $_POST['mc_gross'];
			
			if(isset($das['1']) && !empty($das['1']) && is_numeric($das['1'])){
			$this->request->data['UserTransctionDetail']['coupon_id'] = $das['1'];
			}
			if(isset($das['2']) && !empty($das['2']) && is_numeric($das['2'])){
			$this->request->data['UserTransctionDetail']['institution_id'] = $das['2'];
			}

			//$ddd = explode('=',"182,183,181,'=120,130");

			//$dd = str_getcsv("182,183,181,'=120,130", ",", "'"); 

			if(isset($plan_tt_id['1']) && !empty($plan_tt_id['1'])){
			$this->request->data['UserTransctionDetail']['plan_id'] = $plan_tt_id['1'];				
			$pids = $plan_tt_id['1'];			
			}
			
			$this->request->data['UserTransctionDetail']['amount'] = $_POST['mc_amount3'];
			
			$this->request->data['UserTransctionDetail']['payment_date'] = strtotime(date('m/d/Y h:i:s', time()));
			$this->request->data['UserTransctionDetail']['mc_currency'] = $_POST['mc_currency'];
			$this->request->data['UserTransctionDetail']['is_recurring'] = $_POST['recurring'];
			 	
			if(isset($_POST['subscr_id'])){
					$this->request->data['UserTransctionDetail']['txn_id'] = $_POST['subscr_id'];
					$this->request->data['UserTransctionDetail']['subscr_id'] = $_POST['subscr_id'];					
					}else{
                    $this->request->data['UserTransctionDetail']['txn_id'] = $_POST['txn_id'];
					}

		//	$this->request->data['UserTransctionDetail']['payment_status'] = $_POST['payment_status'];
		//	$this->request->data['UserTransctionDetail']['verify_sign'] = $_POST['verify_sign'];
		
			$this->request->data['UserTransctionDetail']['auth'] = $_POST['auth'];
			
			//pr($this->request->data['UserTransctionDetail']); die;
			
			
		    $flag = 0;
		
			if($flag ==1 && $this->UserTransctionDetail->save($this->request->data)){
					

					//echo '<pre>';print_r($userData);die;
					    
						//$tran_id = $this->UserTransctionDetail->getLastInsertID();
						$tran_id = $this->request->data['UserTransctionDetail']['subscr_id'];
					
					//	$this->UserPlan->updateAll( array('UserPlan.is_active' => 1,'UserPlan.trans_id' => '"'.$tran_id.'"'),array('UserPlan.user_id' => $id ,'UserPlan.is_active' =>0));
						
						$this->UserPlan->updateAll( array( 'UserPlan.is_active' => 1,'UserPlan.trans_id' => '"'.$tran_id .'"'),array('UserPlan.user_id' => $id , "UserPlan.id IN($pids)"));	

					$userData = $this->User->find('all',array('fields'=>array('User.email,User.activation_key, UserDetail.first_name,UserDetail.last_name'),'conditions'=>array('User.id' => $id )));
					
					$activatiinHash = $userData[0]['User']['activation_key'];
					$this->request->data['UserDetail']['first_name'] = $userData[0]['UserDetail']['first_name'];
					$this->request->data['UserDetail']['last_name'] = $userData[0]['UserDetail']['last_name'];
					
					
					
					$this->request->data['UserDetail']['amount'] = $_POST['mc_amount3'];
					
					$this->request->data['User']['email'] = $userData[0]['User']['email'];
					$sendMail = $this->__sendEmailConfirmWithPayment($this->request->data,$id , $activatiinHash);
					
                  //  $this->Session->setFlash(__('You have been sent an activation email'));
				}	
					
			}else if(isset($_POST['data']['plan']['free'])){
			 
			$this->request->data['UserTransctionDetail']['user_id'] = $id ;
			$this->request->data['UserTransctionDetail']['txn_id'] = rand().time();
			
			$this->request->data['UserTransctionDetail']['amount'] = '0';			
			$this->request->data['UserTransctionDetail']['payment_date'] = strtotime(date('m/d/Y h:i:s', time()));
			$this->request->data['UserTransctionDetail']['mc_currency'] = 'USD';
			$this->request->data['UserTransctionDetail']['payment_status'] = 'Completed';
			
	
			$this->request->data['UserTransctionDetail']['auth'] = base64_encode(rand().time());
			
			//pr($this->request->data); die;
			
			
			if(isset($this->request->data['plan']['coup_id']) && !empty($this->request->data['plan']['coup_id'])){
			$this->request->data['UserTransctionDetail']['coupon_id'] =$this->request->data['plan']['coup_id'];
			}
			if(isset($this->request->data['plan']['associated_user_id']) && !empty($this->request->data['plan']['associated_user_id'] )){
			$this->request->data['UserTransctionDetail']['institution_id'] = $this->request->data['plan']['associated_user_id'];
			}
			
			
			
			//$this->UserTransctionDetail->update( array('UserPlan.is_active' => 1));
			
			
			
			
			if($this->UserTransctionDetail->save($this->request->data)){
					

					//echo '<pre>';print_r($userData);die;
					
					$tran_id = $this->UserTransctionDetail->getLastInsertID();						
					
					$this->UserPlan->updateAll( array('UserPlan.is_active' => 1,'UserPlan.trans_id' => $tran_id),array('UserPlan.user_id' => $id ,'UserPlan.is_active' =>0,'UserPlan.trans_id'=>0));
										
										
					$userData = $this->User->find('all',array('fields'=>array('User.email,User.activation_key, UserDetail.first_name,UserDetail.last_name'),'conditions'=>array('User.id' => $id )));
				
					
					$activatiinHash = $userData[0]['User']['activation_key'];
					$this->request->data['UserDetail']['first_name'] = $userData[0]['UserDetail']['first_name'];
					$this->request->data['UserDetail']['last_name'] = $userData[0]['UserDetail']['last_name'];
					$this->request->data['UserDetail']['txn_id'] = time();
					$this->request->data['UserDetail']['amount'] = '0';
					
					$this->request->data['User']['email'] = $userData[0]['User']['email'];
					$sendMail = $this->__sendEmailConfirmWithPayment($this->request->data,$id , $activatiinHash);

				}	
   				
			 $this->render('thanks_simple');
			}			
			else{
					return $this->redirect(SITEURL.'/plans/paymentcancel/?id='.$id );
			}
			
		 }
	}
	
	
	function paymentsuccess(){
	
	//	mail('vikas.gautam@dotsquares.com','test','ipn_hit');
	
/* 			$_POST['mc_gross'] = '19.05';
			$_POST['custom'] =  "185,'=487,488";
			$_POST['payment_status'] = 'Completed';
			$_POST['mc_currency'] = 'USD';
			$_POST['subscr_id'] = 'I-SD64PDD39HRXXXX';
		    $_POST['txn_id'] ='03719619KF3299619';	 */
	
	        $serializedDatas = print_r($_POST, true); 
			$contents = "Response:  Online -- ".date('d-m-Y H:i:s',time())."\n"."----------------------------------------------------------------------\n".$serializedDatas."\n----------------------------------------------------------------------\n";
			$file2 = DOC_ROOT."/paypal/paypal_log_success.txt";
			$fp2 = fopen($file2,"a+");
			file_put_contents($file2, $contents, FILE_APPEND);
			fclose($fp2);
			
	
	 if(isset($_POST) && !empty($_POST)){			
			// Save Response in file for tracking paypal log
			
			 $cus = $_POST['custom'];
			
			  
            $plan_tt_id = explode('=',$cus);
			
    		$das = str_getcsv($cus , ",", "'");	
		
		    $id = $das['0']; 
			
			 // pr($id); die;
			//$serializedData = print_r($_POST, true); 
			//$content = "Response IPN:  Online -- ".date('d-m-Y H:i:s',time())."\n"."----------------------------------------------------------------------\n".$serializedData."\n----------------------------------------------------------------------\n";
			//$file = DOC_ROOT."/paypal/paypal_log.txt";
			//$fp = fopen($file,"a+");
			//file_put_contents($file, $content, FILE_APPEND);
			//fclose($fp);
			//$id = $_POST['custom']; // Get the Id 
			
			if(isset($_POST['payment_date']) && !empty($_POST['payment_date'])){
			$date_of_payment = date('Y-m-d',strtotime($_POST['payment_date'])); 
			}
			
			
		//	if($_POST['payment_status'] == 'Completed' || $_POST['payment_status'] == 'Pending' || $_POST['payment_status'] == 'Processed'){
		
			
			
			
			$this->request->data['UserTransctionDetail']['user_id'] = $id ;
		
			if(isset($_POST['period3']) && !empty($_POST['period3'])){
			$this->request->data['UserTransctionDetail']['rec_period'] = $_POST['period3'];
			}
			//$this->request->data['UserTransctionDetail']['amount'] = $_POST['mc_gross'];
			
			if(isset($das['1']) && !empty($das['1']) && is_numeric($das['1'])){
			$this->request->data['UserTransctionDetail']['coupon_id'] = $das['1'];
			}
			if(isset($das['2']) && !empty($das['2']) && is_numeric($das['2'])){
			$this->request->data['UserTransctionDetail']['institution_id'] = $das['2'];
			}

			//$ddd = explode('=',"182,183,181,'=120,130");

			//$dd = str_getcsv("182,183,181,'=120,130", ",", "'"); 

			if(isset($plan_tt_id['1']) && !empty($plan_tt_id['1'])){
			$this->request->data['UserTransctionDetail']['plan_id'] = $plan_tt_id['1'];					
			$pids = $plan_tt_id['1'];		
			
			}
			
			
			
			
			if(isset($_POST['mc_gross']) && !empty($_POST['mc_gross'])){
			$this->request->data['UserTransctionDetail']['amount'] = $_POST['mc_gross'];
			}
			
			
			
			if(isset($_POST['payment_status']) && !empty($_POST['payment_status'])){
			$this->request->data['UserTransctionDetail']['payment_status'] = $_POST['payment_status'];
			}
			
			
			
			$this->request->data['UserTransctionDetail']['payment_date'] = strtotime(date('m/d/Y h:i:s', time()));
			
			$this->request->data['UserTransctionDetail']['mc_currency'] = $_POST['mc_currency'];
			
			$_POST['verify_sign'] = 'ArH5sKzeLhwRH46YqCMeWMtHi1b9A7P9y5OjYcuGuA7.ElQuvWFQYJ46';
		
			$this->request->data['UserTransctionDetail']['verify_sign'] = $_POST['verify_sign'];
		
		
		
			
			if(isset($_POST['subscr_id']) && isset($_POST['txn_id'])){
					$subid = $_POST['subscr_id'];
					$this->request->data['UserTransctionDetail']['txn_id'] = $_POST['txn_id'];
					$this->request->data['UserTransctionDetail']['subscr_id'] = $_POST['subscr_id'];
			}else{
                    $this->request->data['UserTransctionDetail']['txn_id'] = $_POST['txn_id'];
					$this->request->data['UserTransctionDetail']['subscr_id'] = $_POST['subscr_id'];
					
					$subid = $_POST['txn_id'];
			}
			
			
			$serializedDataa = print_r($_POST, true); 
			$contentt = "Response IPN Jon:  Online -- ".date('d-m-Y H:i:s',time())."\n"."----------------------------------------------------------------------\n".$this->request->data['UserTransctionDetail']['payment_status']."\n----------------------------------------------------------------------\n";
			$filee = DOC_ROOT."/paypal/paypal_log.txt";
			$fpp = fopen($filee,"a+");
			file_put_contents($filee, $contentt, FILE_APPEND);
			fclose($fpp);
			
			
			

			if($this->UserTransctionDetail->find('count',array('conditions'=>array('UserTransctionDetail.txn_id' => $subid ,'UserTransctionDetail.user_id' => $id ))) > 0) {
			
			//pr($this->request->data['UserTransctionDetail']); die;
		
			//'UserPlan.start_date' => strtotime(date('Y-m-d')),'UserPlan.end_date' => strtotime(date("Y-m-d", strtotime("+ 365 day"))),
			
			$std =  strtotime(date('Y-m-d'));
			$ntd =  strtotime(date("Y-m-d", strtotime("+ 365 day")));
		
			if($this->UserTransctionDetail->updateAll( array('UserTransctionDetail.payment_status' => "'".$this->request->data['UserTransctionDetail']['payment_status']."'",'UserTransctionDetail.txn_id' => "'".$this->request->data['UserTransctionDetail']['txn_id']."'",'UserTransctionDetail.start_date' =>$std ,'UserTransctionDetail.end_date' => $ntd) , array('UserTransctionDetail.txn_id' => $subid , 'UserTransctionDetail.user_id' => $id ))){
			//if($this->UserTransctionDetails->save($this->request->data)){
					
					$userData = $this->User->find('first',array('fields'=>array('User.email,User.activation_key, UserDetail.first_name,UserDetail.last_name'),'conditions'=>array('User.id' => $id )));
					//echo '<pre>';print_r($userData);die;
					
					//$this->UserPlan->updateAll( array('UserPlan.is_active' => 1),array('UserPlan.trans_id' => $this->request->data['UserTransctionDetail']['subscr_id'] ));	
						
				
		
						$this->UserPlan->updateAll( array( 'UserPlan.is_active' => 1,'UserPlan.trans_id' => '"'.$this->request->data['UserTransctionDetail']['subscr_id'] .'"'),array('UserPlan.user_id' => $id , "UserPlan.id IN($pids)"));			
					
					//	$this->UserPlan->updateAll( array('UserPlan.is_active' => 1),array('UserPlan.user_id' => $id ));
					
					$activatiinHash = $userData ['User']['activation_key'];
					$this->request->data['UserDetail']['first_name'] = $userData ['UserDetail']['first_name'];
					$this->request->data['UserDetail']['last_name'] = $userData ['UserDetail']['last_name'];
					
					//$this->request->data['UserDetail']['txn_id'] = $_POST['payer_id'];
					//$this->request->data['UserDetail']['amount'] = $_POST['mc_amount3'];
					
					$this->request->data['User']['email'] = $userData ['User']['email'];
					
					//$sendMail = $this->__sendEmailConfirmWithPayment($this->request->data,$id , $activatiinHash);
                  
				  //  $this->Session->setFlash(__('You have been sent an activation email'));
					

				    // return $this->redirect(SITEURL);
                	//echo '<pre>';print_r( $userData );die;
			
		}
		
		}else{
		
			$this->request->data['UserTransctionDetail']['start_date'] =  strtotime(date('Y-m-d'));
			$this->request->data['UserTransctionDetail']['end_date'] =  strtotime(date("Y-m-d", strtotime("+ 365 day")));
		
	
			$emptyTrans = $this->UserTransctionDetail->find('count',array('conditions'=>array('UserTransctionDetail.subscr_id' => $subid ,'UserTransctionDetail.user_id' => $id))) ;
	
		
		if($this->UserTransctionDetail->save($this->request->data['UserTransctionDetail'])){

		
		
		//$this->UserPlan->updateAll( array('UserPlan.start_date' => strtotime(date('Y-m-d')),'UserPlan.end_date' => strtotime(date("Y-m-d", strtotime("+ 365 day"))),'UserPlan.is_active' => 1),array('UserPlan.trans_id' => $this->request->data['UserTransctionDetail']['subscr_id'] ));		
		
		$this->UserPlan->updateAll( array('UserPlan.start_date' => strtotime(date('Y-m-d')),'UserPlan.end_date' => strtotime(date("Y-m-d", strtotime("+ 365 day"))),'UserPlan.is_active' => 1,'UserPlan.trans_id' => '"'.$this->request->data['UserTransctionDetail']['subscr_id'] .'"'),array('UserPlan.user_id' => $id , "UserPlan.id IN($pids)"));
		
			$userData = $this->User->find('first',array('fields'=>array('User.email,User.activation_key, UserDetail.first_name,UserDetail.last_name'),'conditions'=>array('User.id' => $id , 'User.status' => 0)));
			
			$userDataACT = $this->User->find('first',array('fields'=>array('User.email,User.activation_key, UserDetail.first_name,UserDetail.last_name'),'conditions'=>array('User.id' => $id , 'User.status' => 1)));
			

			
			if(isset($userData) && !empty($userData)){
		   /*----------------------------Activation Mail----------------------------------------*/
		   
			$activatiinHash = $userData ['User']['activation_key'];
			$this->request->data['UserDetail']['first_name'] = $userData ['UserDetail']['first_name'];
			$this->request->data['UserDetail']['last_name'] = $userData ['UserDetail']['last_name'];



			$this->request->data['UserDetail']['amount'] = $_POST['mc_amount3'];

			$this->request->data['User']['email'] = $userData ['User']['email'];
			$sendMail = $this->__sendEmailConfirmWithPayment($this->request->data,$id , $activatiinHash);
		
		}else if((isset($userDataACT) && !empty($userDataACT)) && ($emptyTrans>0)){
		
		/*----------------------------Recurring Mail----------------------------------------*/
		
					$activatiinHash = $userData ['User']['activation_key'];
					$this->request->data['UserDetail']['first_name'] = $userDataACT['UserDetail']['first_name'];
					$this->request->data['UserDetail']['last_name'] = $userDataACT['UserDetail']['last_name'];
					$this->request->data['UserDetail']['txn_id'] = $_POST['payer_id'];
					$this->request->data['UserDetail']['amount'] = $_POST['mc_amount3'];
					
					$this->request->data['User']['email'] = $userData['User']['email'];
				//	$sendMail = $this->__sendUpgradeWithPayment($this->request->data,$id , $activatiinHash);
		
		}else if((isset($userDataACT) && !empty($userDataACT)) && ($emptyTrans==0)){
		/*----------------------------Add feature----------------------------------------*/		
		
					$activatiinHash = $userData['User']['activation_key'];
					$this->request->data['UserDetail']['first_name'] = $userDataACT['UserDetail']['first_name'];
					$this->request->data['UserDetail']['last_name'] = $userDataACT['UserDetail']['last_name'];
					$this->request->data['UserDetail']['txn_id'] = $_POST['payer_id'];
					$this->request->data['UserDetail']['amount'] = $_POST['mc_amount3'];
					
					$this->request->data['User']['email'] = $userDataACT['User']['email'];
					$sendMail = $this->__sendUpgradeWithPayment($this->request->data,$id , $activatiinHash);
		
		}
		
		
		}
														
		}
		
		
		}
		die('success');
	}
	
	function paymentcancel(){
	if(isset($_REQUEST['id']) && !empty($_REQUEST['id'])){
	$this->User->delete($_REQUEST['id']);
	$this->UserDetail->delete($_REQUEST['id']);
	$this->UserPlan->deleteAll(array('UserPlan.user_id' => $_REQUEST['id']));
	
	//$this->Comment->delete($this->request->data('Comment.id'));
	}
	//die('Cancel');
	}
	
	
	
	function paymentcancel_plan(){
	 $this->layout = "inner";
	if(isset($_REQUEST['id']) && !empty($_REQUEST['id'])){
	//$this->User->delete($_REQUEST['id']);
	//$this->UserDetail->delete($_REQUEST['id']);
	$this->UserPlan->deleteAll(array('UserPlan.user_id' => $_REQUEST['id'],'UserPlan.is_active' => 0));
	
	//$this->Comment->delete($this->request->data('Comment.id'));
	}
	//die('Cancel');
	}
	
		
	}
