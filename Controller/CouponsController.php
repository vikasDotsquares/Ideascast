<?php
/**
 * Coupons controller.
 *
 * This file will render views from views/Coupons/
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
 * @link          http://cakephp.org CakePHP(tm) Task
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('Controller/Component','Auth','Session', 'RequestHandler');
App::uses('CakeEmail', 'Network/Email');
/**
 * Coupons Controller
 *
 * @property User $User
 * @property PaginatorComponent $Paginator
 */

class CouponsController  extends AppController {

	public $name = 'Coupons';	
	public $uses = array('Coupon','User','UserTransctionDetail');
	
	/**
	* check login for admin and frontend user
	* allow and deny user
	*/
	//public $components = array('Email');
	
	/**
	* Helpers
	*
	* @var array
	*/
	public $helpers = array('Html', 'Form', 'Session', 'Time', 'Text','Common');	
	
	public function beforeFilter() {
		parent::beforeFilter();
	}
	
	
	/*======================= Coupons FUNCTIONS =========================*/
        
    /**
	 * Displays a view
	 *
	 * @param mixed What page to display
	 * @return void
	 */	
	public function admin_index(){
		
        $orConditions = array();
        $andConditions = array();
        $finalConditions = array();

        $in = 0;
        $per_page_show = $this->Session->read('coupon.per_page_show');
        if (empty($per_page_show)) {
            $per_page_show = ADMIN_PAGING;
        }

        if (isset($this->data['Coupon']['keyword'])) {
            $keyword = trim($this->data['Coupon']['keyword']);
        } else {
            $keyword = $this->Session->read('coupon.keyword');
        }

        if (isset($keyword)) {
            $this->Session->write('coupon.keyword', $keyword);
            $keywords = explode(" ", $keyword);
            if (isset($keywords[0]) && !empty($keywords[0]) && count($keywords) < 2) {
                $keyword = $keywords[0];
                $in = 1;
                $orConditions = array('OR' => array(
                        'Coupon.name LIKE' => '%' . $keyword . '%',
                        'Coupon.percentage LIKE' => '%' . $keyword . '%',
						'Coupon.on_amount LIKE' => '%' . $keyword . '%',		
                        'Coupon.id LIKE' => '%' . $keyword . '%'
                ));
            } else if (!empty($keywords) && count($keywords) > 1) {
                $name = $keywords[0];
                $sign = $keywords[1];
                $in = 1;
                $andConditions = array('AND' => array(
                        'Coupon.name LIKE' => '%' . $name . '%',
						'Coupon.on_amount LIKE' => '%' . $keyword . '%',	
                        'Coupon.percentage LIKE' => '%' . $sign . '%'
                ) );
            }
        }

        if (isset($this->data['Coupon']['status'])) {
            $status = $this->data['Coupon']['status'];
        } else {
            $status = $this->Session->read('coupon.status');
        }

        if (isset($status)) {
            $this->Session->write('coupon.status', $status);
            if ($status != '') {
                $in = 1;
                $andConditions = array_merge($andConditions, array('Coupon.status' => $status));
            }
        }

        if (isset($this->data['Coupon']['per_page_show']) && !empty($this->data['Coupon']['per_page_show'])) {
            $per_page_show = $this->data['Coupon']['per_page_show'];
        }


        if (!empty($orConditions)) {
            $finalConditions = array_merge($finalConditions, $orConditions);
        }
        if (!empty($andConditions)) {
            $finalConditions = array_merge($finalConditions, array('AND' => $andConditions));
        }
		
		$count = $this->Coupon->find('count', array('conditions' => $finalConditions));
        $this->set('count', $count);			

        $this->set('title_for_layout', __('All Coupons', true));
        $this->Session->write('coupon.per_page_show', $per_page_show);
        $this->Coupon->recursive = 3;
        $this->paginate = array('conditions' => $finalConditions, "limit" => $per_page_show, "order" => "Coupon.id DESC");
        $this->set('coupons', $this->paginate('Coupon'));
        $this->set('in', $in);
	}
	
	
	public function admin_coupon_used($id=null){
		
        $orConditions = array();
        $andConditions = array();
        $finalConditions = array();

        $in = 0;
        $per_page_show = $this->Session->read('usertransctiondetail.per_page_show');
        if (empty($per_page_show)) {
            $per_page_show = ADMIN_PAGING;
        }

        if (isset($this->data['Coupon']['keyword'])) {
            $keyword = trim($this->data['Coupon']['keyword']);
        } else {
            $keyword = $this->Session->read('coupon.keyword');
        }

        if (isset($keyword)) {
            $this->Session->write('coupon.keyword', $keyword);
            $keywords = explode(" ", $keyword);
            if (isset($keywords[0]) && !empty($keywords[0]) && count($keywords) < 2) {
                $keyword = $keywords[0];
                $in = 1;
                $orConditions = array('OR' => array(
                        'Coupon.name LIKE' => '%' . $keyword . '%',
                        'Coupon.percentage LIKE' => '%' . $keyword . '%',
						'Coupon.on_amount LIKE' => '%' . $keyword . '%',		
                        'Coupon.id LIKE' => '%' . $keyword . '%'
                ));
            } else if (!empty($keywords) && count($keywords) > 1) {
                $name = $keywords[0];
                $sign = $keywords[1];
                $in = 1;
                $andConditions = array('AND' => array(
                        'Coupon.name LIKE' => '%' . $name . '%',
						'Coupon.on_amount LIKE' => '%' . $keyword . '%',	
                        'Coupon.percentage LIKE' => '%' . $sign . '%'
                ) );
            }
        }

        if (isset($this->data['Coupon']['status'])) {
            $status = $this->data['Coupon']['status'];
        } else {
            $status = $this->Session->read('coupon.status');
        }

        if (isset($status)) {
            $this->Session->write('coupon.status', $status);
            if ($status != '') {
                $in = 1;
                $andConditions = array_merge($andConditions, array('Coupon.status' => $status));
            }
        }
		
		
		if (isset($id) && !empty($id)) {

               $andConditions = array_merge($andConditions, array('UserTransctionDetail.coupon_id' => $id));
			 
			// $andConditions = array_merge($andConditions, array('Coupon.id' => $id));

        }

        if (isset($this->data['UserTransctionDetail']['per_page_show']) && !empty($this->data['UserTransctionDetail']['per_page_show'])) {
            $per_page_show = $this->data['Coupon']['per_page_show'];
        }


        if (!empty($orConditions)) {
            $finalConditions = array_merge($finalConditions, $orConditions);
        }
        if (!empty($andConditions)) {
            $finalConditions = array_merge($finalConditions, array('AND' => $andConditions));
        }

		
		$count = $this->UserTransctionDetail->find('count', array('conditions' => $finalConditions));
        $this->set('count', $count);	
		//$this->Coupon->unbindModel(array('hasMany' => array('UserTransctionDetail')), true);
		
        $this->set('title_for_layout', __('Coupon Transactions', true));
        $this->Session->write('usertransctiondetail.per_page_show', $per_page_show);
        $this->UserTransctionDetail->recursive = 3;
        $this->paginate = array(
			 'conditions' => $andConditions,  "order" => "UserTransctionDetail.id DESC");
        $this->set('coupons', $this->paginate('UserTransctionDetail'));
        $this->set('in', $in);
	}
	

	function admin_coupon_resetfilter(){
		$this->Session->write('coupon.keyword', '');
		$this->Session->write('coupon.status', '');
		$this->redirect(array('action' => 'index'));
	}
	
	
	
	public function admin_coupon_updatestatus() {
        if ($this->request->is('ajax')) {
            $this->autoRender = false;
            $this->loadModel('Coupon');
            $this->request->data['Coupon'] = $this->request->data;
			//pr(  $this->request->data['Coupon']); die;
            if ($this->Coupon->save($this->request->data,false)) {
                $this->Session->setFlash(__('Coupon status has been updated successfully.'), 'success');
                die('success');
            } else {
                $this->Session->setFlash(__('Coupon status could not updated successfully.'), 'error');
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
            $this->Coupon->id = $id;
            if (!$this->Coupon->exists()) {
                throw new NotFoundException(__('Invalid Coupon'), 'error');
            }

            if ($this->Coupon->delete()) {
                $this->Session->setFlash(__('Coupon has been deleted successfully.'), 'success');
                die('success');
            } else {
                $this->Session->setFlash(__('Coupon could not deleted successfully.'), 'error');
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
        $this->set('title_for_layout', __('Add Coupon', true));
		//$this->loadModel('User');

        if ($this->request->is('post') || $this->request->is('put')) {
           

			if(isset($this->request->data['Coupon']['on_amount']) && empty($this->request->data['Coupon']['on_amount'])){
				unset($this->request->data['Coupon']['on_amount']);
			} 
			//pr($this->request->data); die;
			if(isset($this->request->data['inst']) && !empty($this->request->data['inst'])){
		    $inst = array_keys($this->request->data['inst']);	
			$dt = implode(",",$inst);
			$this->request->data['Coupon']['user_id']  = $dt;
			}
		
		    $from = $this->request->data['Coupon']['start_time'];
            $to = $this->request->data['Coupon']['end_time'];
           // $start_titmestamp = strtotime($from) - 86400 + (time() % 86400);
          //  $end_titmestamp = strtotime($to) + 86400;
			$start_titmestamp = strtotime($from);
            $end_titmestamp = strtotime($to);
			
            $this->request->data['Coupon']['start_time'] = date("Y-m-d h:i:s", $start_titmestamp);
            $this->request->data['Coupon']['end_time'] = date("Y-m-d h:i:s", $end_titmestamp);
		
			
			if(isset($this->request->data['Coupon']['is_institution']) && $this->request->data['Coupon']['is_institution']=='on'){
			$this->request->data['Coupon']['is_institution'] = '1';
			}else{
			$this->request->data['Coupon']['is_institution'] = '0';
			}
			if(isset($this->request->data['Coupon']['status']) && $this->request->data['Coupon']['status']=='on'){
			$this->request->data['Coupon']['status'] = '1';
			}else{
			$this->request->data['Coupon']['status'] = '0';
			}
			
			if(isset($this->request->data['Coupon']['percentage'])){
			$this->request->data['Coupon']['percentage'] = $this->request->data['Coupon']['percentage'];
			}else{
			$this->request->data['Coupon']['percentage'] = '';
			}
			
			if(isset($this->request->data['Coupon']['flat'])){
			$this->request->data['Coupon']['flat'] = $this->request->data['Coupon']['flat'];
			}else{
			$this->request->data['Coupon']['flat'] = '0';
			}
		 //pr($this->request->data); die;
            if ($this->Coupon->save($this->request->data,true)) {
                $this->Session->setFlash(__('Coupon has been saved successfully.'), 'success');
				 $this->redirect(array('action' => 'index'));
              //  die('success');
            }else{
			 // pr($this->request->data); //die;
				 $this->request->data['Coupon']['start_time'] = date("m/d/Y", strtotime( $this->request->data['Coupon']['start_time'] ));
				 $this->request->data['Coupon']['end_time']  = date("m/d/Y", strtotime( $this->request->data['Coupon']['end_time'] )); 
				 
				if(isset($this->request->data['inst']) && !empty($this->request->data['inst'])){
					$inst = array_keys($this->request->data['inst']);	
					
					$this->request->data['ins'] = $inst;
				}	
					$ins =  $this->User->find('list',array('conditions'=>array('User.role_id'=>4),'fields'=>array('User.id'),'recursive' => 2));
					$this->set('ins', $ins);
				
			}

        }else{
				$ins =  $this->User->find('list',array('conditions'=>array('User.role_id'=>4),'fields'=>array('User.id'),'recursive' => 2));

				$this->set('ins', $ins);
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
        $this->set('title_for_layout', __('Edit Coupon', true));
		$ins = $this->User->find('list',array('conditions'=>array('User.role_id'=>4),'fields'=>array('User.id'),'recursive' => 2));

		$this->set('ins', $ins);
        $this->Coupon->id = $id;
        if (!$this->Coupon->exists()) {
            $this->Session->setFlash(__('Invalid Coupon.'), 'error');
            $this->redirect(array('action' => 'index'));
        }
        if ($this->request->is('post') || $this->request->is('put')) {
           
			if(isset($this->request->data['inst']) && !empty($this->request->data['inst'])){
		    $inst = array_keys($this->request->data['inst']);	
			$dt = implode(",",$inst);
			$this->request->data['Coupon']['user_id']  = $dt;
			}
			
			
			
			$from = $this->request->data['Coupon']['start_time'];
            $to = $this->request->data['Coupon']['end_time'];
			$start_titmestamp = strtotime($from);
            $end_titmestamp = strtotime($to);			
            $this->request->data['Coupon']['start_time'] = date("Y-m-d h:i:s", $start_titmestamp);
            $this->request->data['Coupon']['end_time'] = date("Y-m-d h:i:s", $end_titmestamp);
			
			
			if(isset($this->request->data['Coupon']['is_institution']) && ($this->request->data['Coupon']['is_institution']=='on' || $this->request->data['Coupon']['is_institution']=='1')){
			$this->request->data['Coupon']['is_institution'] = '1';
			if(empty($this->request->data['Coupon']['user_id'])){
			 $this->Session->setFlash(__('Atleast one Instiution should be selected.'), 'error');
			 $this->redirect(array('action' => 'edit',$this->request->data['Coupon']['id']));
			}
			}else{
			$this->request->data['Coupon']['is_institution'] = '0';
			}
			if(isset($this->request->data['Coupon']['status']) && $this->request->data['Coupon']['status']=='on'){
			$this->request->data['Coupon']['status'] = '1';
			}else{
			$this->request->data['Coupon']['status'] = '0';
			}
			
			if(isset($this->request->data['Coupon']['percentage'])){
			$this->request->data['Coupon']['percentage'] = $this->request->data['Coupon']['percentage'];
			}else{
			$this->request->data['Coupon']['percentage'] = '';
			}
			
			if(isset($this->request->data['Coupon']['flat'])){
			$this->request->data['Coupon']['flat'] = $this->request->data['Coupon']['flat'];
			}else{
			$this->request->data['Coupon']['flat'] = '0';
			}
		
			//pr($this->request->data);  die;
            if ($this->Coupon->save($this->request->data)) {
                $this->Session->setFlash(__('The Coupon has been updated successfully.'), 'success');
                $this->redirect(array('action' => 'index'));
            }else{
				 $this->request->data['Coupon']['start_time'] = date("m/d/Y", strtotime( $this->request->data['Coupon']['start_time'] ));
				 $this->request->data['Coupon']['end_time']  = date("m/d/Y", strtotime( $this->request->data['Coupon']['end_time'] ));  
			}
        } else {
		     $this->Coupon->recursive = 3;
            $data = $this->Coupon->read(null, $id);			
			$data['Coupon']['start_time'] = date("m/d/Y", strtotime($data['Coupon']['start_time']));
            $data['Coupon']['end_time'] = date("m/d/Y", strtotime($data['Coupon']['end_time']));
			//pr($data); die;
           $this->request->data= $data;
        }
    }

			

	
	}