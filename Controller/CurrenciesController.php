<?php
/**
 * Currencies controller.
 *
 * This file will render views from views/Currencies/
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
 * Currencies Controller
 *
 * @property User $User
 * @property PaginatorComponent $Paginator
 */

class CurrenciesController  extends AppController {

	public $name = 'Currencies';
	public $uses = array('Currency');

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


	/*======================= Currency FUNCTIONS =========================*/

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
        $per_page_show = $this->Session->read('currency.per_page_show');
        if (empty($per_page_show)) {
            $per_page_show = ADMIN_PAGING;
        }

        if (isset($this->data['Currency']['keyword'])) {
            $keyword = trim($this->data['Currency']['keyword']);
        } else {
            $keyword = $this->Session->read('currency.keyword');
        }

        if (isset($keyword)) {
            $this->Session->write('currency.keyword', $keyword);
            $keywords = explode(" ", $keyword);
			//echo count($keywords);
            if (isset($keywords[0]) && !empty($keywords[0]) && count($keywords) <= 2) {
                $keyword = $keywords[0];
                $in = 1;
                $orConditions = array('OR' => array(
                        'Currency.name LIKE' => '%' . $keyword . '%',
                        'Currency.sign LIKE' => '%' . $keyword . '%',
                        'Currency.id LIKE' => '%' . $keyword . '%',
						'Currency.country LIKE' => '%'.$keyword.'%'
                ));
            } else if (!empty($keywords) && count($keywords) > 1) {
                $name = $keywords[0];
                $sign = $keywords[1];
                $in = 1;
                $andConditions = array('AND' => array(
                        'Currency.name LIKE' => '%' . $name . '%',
                        'Currency.sign LIKE' => '%' . $sign . '%',
						'Currency.country LIKE' => '%'.$keyword.'%'
                ));
            }
        }

        if (isset($this->data['Currency']['status'])) {
            $status = $this->data['Currency']['status'];
        } else {
            $status = $this->Session->read('currency.status');
        }

        if (isset($status)) {
            $this->Session->write('currency.status', $status);
            if ($status != '') {
                $in = 1;
                $andConditions = array_merge($andConditions, array('Currency.status' => $status));
            }
        }

        if (isset($this->data['Currency']['per_page_show']) && !empty($this->data['Currency']['per_page_show'])) {
            $per_page_show = $this->data['Currency']['per_page_show'];
        }


        if (!empty($orConditions)) {
            $finalConditions = array_merge($finalConditions, $orConditions);
        }
        if (!empty($andConditions)) {
            $finalConditions = array_merge($finalConditions, array('AND' => $andConditions));
        }
//pr($finalConditions); die;

		$count = $this->Currency->find('count', array('conditions' => $finalConditions));
        $this->set('count', $count);


        $this->set('title_for_layout', __('All Currencies', true));
        $this->Session->write('currency.per_page_show', $per_page_show);
        $this->Currency->recursive = 0;
        $this->paginate = array('conditions' => $finalConditions, "limit" => $per_page_show, "order" => "Currency.id DESC");
        $this->set('currencies', $this->paginate('Currency'));
        $this->set('in', $in);
	}

	function admin_currency_resetfilter(){
		$this->Session->write('currency.keyword', '');
		$this->Session->write('currency.status', '');
		$this->redirect(array('action' => 'index'));
	}



	    public function admin_currency_updatestatus() {
        if ($this->request->is('ajax')) {
            $this->autoRender = false;
            $this->loadModel('Currency');
            $this->request->data['Currency'] = $this->request->data;

            if ($this->Currency->save($this->request->data)) {
				$this->Currency->updateAll( array('Currency.status' => 0),array('Currency.id !=' => $this->request->data['Currency']['id']));
                $this->Session->setFlash(__('Currency status has been updated successfully.'), 'success');
                die('success');
            } else {
                $this->Session->setFlash(__('Currency status could not updated successfully.'), 'error');
            }
        }
		// pr($this->request->data); die;
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
			$chekstat = $this->Currency->find('first',array('conditions'=>array('Currency.id'=>$id),'fields'=>array('Currency.status')));

			$cstat = $chekstat['Currency']['status'];
			$TotCurrency = $this->Currency->find('count');

		if((isset($TotCurrency) && isset($cstat)) && ($TotCurrency >1 && $cstat==0)){
		if (isset($this->data['id']) && !empty($this->data['id'])) {
			//check Access




			if (!$this->Currency->hasAny(array( 'Currency.id' => $this->Session->read('Auth.Currency.id'), 'Currency.id' => $this->data['id'] ))){
				$this->Session->setFlash(__('Invalid Access.'),'error');
				//$this->redirect(array('action' => 'index'));
				die('error');
			}
			$this->Currency->id = $this->data['id'];
			if ($this->Currency->delete()) {
				$this->Session->setFlash(__('Currency has been deleted successfully.'),'success');
				die('success');
			}else{
				$this->Session->setFlash(__('Currency could not deleted.'),'error');
				die('error');
			}
		}
		die('error');
		}else{

		$this->Session->setFlash(__('Currency could not be deleted, Atleast one default currency should exist.'),'error');
		die('error');
		}
	}




       /**
     * admin_add method
     *
     * @return void
     */
    public function admin_add() {
        $this->set('title_for_layout', __('Add Currency', true));
        if ($this->request->is('post') || $this->request->is('put')) { //pr($this->request->data); die;
            if ($this->Currency->save($this->request->data,true)) {
                $this->Session->setFlash(__('Currency has been saved successfully.'), 'success');
                die('success');
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
        $this->set('title_for_layout', __('Edit Currency', true));
        $this->Currency->id = $id;
        if (!$this->Currency->exists()) {
            $this->Session->setFlash(__('Invalid Currency.'), 'error');
            $this->redirect(array('action' => 'index'));
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            // pr($this->request->data);die;
            if ($this->Currency->save($this->request->data)) {
                $this->Session->setFlash(__('The Currency has been updated successfully.'), 'success');
                die('success');
            }
        } else {
            $this->request->data = $this->Currency->read(null, $id);

        }
    }




	}