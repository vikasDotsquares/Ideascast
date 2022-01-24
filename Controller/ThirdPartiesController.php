<?php

/**

 * ThirdParties controller.

 *

 * This file will render views from views/ThirdParties/

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



App::uses('AppController', 'Controller');

App::uses('CakeEmail', 'Network/Email');



/**

 * Static content controller

 *

 * Override this controller by placing a copy in controllers directory of an application

 *

 * @package       app.Controller

 * @link http://book.cakephp.org/2.0/en/controllers/pages-controller.html

 */

class ThirdPartiesController extends AppController {

/**

 * Controller name

 *

 * @var string

 */

	public $name = 'ThirdParties';

/**
 * Default helper
 *
 * @var array
 */
	public $helpers = array('Html', 'Session','Js','Wiki','Common');
	public $components  = array('RequestHandler','Wiki');

	public function changeLanguage($lng) { // Change language method
        if(isset($this->availableLanguages[$lng])) { // If we support this language (see /app/Config/global.php)
            parent::setLang($lng); // call setLang() from AppController
            $this->Session->setFlash(__('The language has been changed to %s', $this->availableLanguages[$lng])); // Send a success flash message
        } else {
            throw new NotFoundException(__('Language %s is not supported', $lng)); // Throw a not found exception
        }
		
        $this->redirect($this->referer()); // redirect the user to the last page (referer)
    }
	
	public function beforeFilter() {		

		parent::beforeFilter();			

		$this->Auth->allow('setnull','display','showblog','blogdetails','deletepostimage','unlinkBlogImage','admin_updatestatus','admin_delete');	
		
		//pr($this->request->params);
		//$this->request->params['named']['page'] = (isset($this->request->params['page'])) ? $this->request->params['page'] : 1;		

	}
	
	/**
      * Displays a view
      * @param mixed What page to display
      * @return void
    */
    public function admin_index() {

        $orConditions = array();
        $andConditions = array();
        $finalConditions = array();

        $in = 0;
        $per_page_show = $this->Session->read('thirdparty.per_page_show');
        if (empty($per_page_show)) {
            $per_page_show = ADMIN_PAGING;
        }

        if (isset($this->data['ThirdParty']['keyword'])) {
            $keyword = trim($this->data['ThirdParty']['keyword']);
        } else {
            $keyword = $this->Session->read('thirdparty.keyword');
        }

        if (isset($keyword)) {
            $this->Session->write('thirdparty.keyword', $keyword);
            $keywords = explode(" ", $keyword);
            if (isset($keywords[0]) && !empty($keywords[0]) && count($keywords) < 2) {
                $keyword = $keywords[0];
                $in = 1;
                $orConditions = array('OR' => array(
                        'ThirdParty.email LIKE' => '%' . $keyword . '%',
                        'ThirdParty.username LIKE' => '%' . $keyword . '%',
                ));
            } 
        }

        if (isset($this->data['ThirdParty']['status'])) {
            $status = $this->data['ThirdParty']['status'];
        } else {
            $status = $this->Session->read('thirdparty.status');
        }		

        if (isset($status)) {
            $this->Session->write('thirdparty.status', $status);
            if ($status != '') {
                $in = 1;
                $andConditions = array_merge($andConditions, array('ThirdParty.status' => $status));
            }
        }

        if (isset($this->data['ThirdParty']['per_page_show']) && !empty($this->data['ThirdParty']['per_page_show'])) {
            $per_page_show = $this->data['ThirdParty']['per_page_show'];
        }


        if (!empty($orConditions)) {
            $finalConditions = array_merge($finalConditions, $orConditions);
        }

        if (!empty($andConditions)) {
            $finalConditions = array_merge($finalConditions, array('AND' => $andConditions));
        }

        //pr($finalConditions); die;
        $count = $this->ThirdParty->find('count', array('conditions' => $finalConditions));
        $this->set('count', $count);
        $this->set('title_for_layout', __('All ThirdParty Users', true));
        $this->Session->write('third_parties.per_page_show', $per_page_show);
        $this->ThirdParty->recursive = 0;
        $this->paginate = array('conditions' => $finalConditions, "limit" => $per_page_show, "order" => "ThirdParty.created DESC");
        $this->set('users', $this->paginate('ThirdParty'));
        $this->set('in', $in);
    } 
	
	function admin_user_resetfilter() {
        $this->Session->write('thirdparty.keyword', '');
        $this->Session->write('thirdparty.status', '');
        $this->redirect(array('action' => 'index'));
    }
	
	/**
      * admin_add method
      * @return void
    */
    public function admin_add() {
        $this->set('title_for_layout', __('Add User', true));
        if ($this->request->is('post') || $this->request->is('put')) {
			
			if(isset($this->request->data['ThirdParty']['profile_img']['name']) && empty($this->request->data['ThirdParty']['profile_img']['name']) ){
				unset($this->request->data['ThirdParty']['profile_img']);
			}
			
			if( isset($this->request->data['ThirdParty']['status']) && $this->request->data['ThirdParty']['status'] == 1 ){
				$this->request->data['ThirdParty']['status'] = 1;
			} else {
				$this->request->data['ThirdParty']['status'] = 0;
			}
			
            if ($this->ThirdParty->save($this->request->data)) {
                $this->Session->setFlash(__('The user has been saved successfully.'), 'success');
                $this->redirect(array('action' => 'index'));
            }
        }
    }

	public function admin_edit($id = null) {

		$this->ThirdParty->id = $id;

		if (!$this->ThirdParty->exists()) {

			$this->Session->setFlash(__('Invalid User'),'error');

			$this->redirect(array('action' => 'index'));

		}		

		if ($this->request->is('post') || $this->request->is('put')) {

			//Set page as inactive if it is unchecked
			
			if(!array_key_exists('status',$this->request->data['ThirdParty'])){

				$this->request->data['ThirdParty']['status']=0;

			}

					
			
			if(isset($this->request->data['ThirdParty']['profile_img']['name']) && empty($this->request->data['ThirdParty']['profile_img']['name']) ){
				unset($this->request->data['ThirdParty']['profile_img']);
			} else {					
				$filesdetail = $this->ThirdParty->findById($this->request->data['ThirdParty']['id']);
				$oldImage = $filesdetail['ThirdParty']['profile_img'];
				
				if( file_exists(WWW_ROOT.THIRD_PARTY_USER_PATH.$oldImage) ){
					@unlink(WWW_ROOT.THIRD_PARTY_USER_PATH.$oldImage);
				}
			}
			
			//pr($this->request->data); die;
			
			if ($this->ThirdParty->save($this->request->data)) {

					 $this->Session->setFlash(__('User has been updated'),'success');
					 $this->redirect(array('action' => 'index'));

			}else{
				 
				 $this->Session->setFlash(__('User could not be saved. Please, try again.'),'error');
			} 
			

		}else{
			
			$this->request->data = $this->ThirdParty->read(null, $id);
		}
		
		$breadcrumb = array(							
				array(
					'title' => 'Third Party User Manager',
					'url' => Router::url(array('controller'=>'ThirdParty', 'action'=>'index', 'admin'=>true))
				),
				array(
					'title' => 'Edit User'
				)
		);

		$this->set(compact('breadcrumb'));			

	}
	
	public function admin_updatestatus() {
        if ($this->request->is('ajax')) {
            $this->autoRender = false;
            $this->loadModel('ThirdParty');
            $this->request->data['ThirdParty'] = $this->request->data;

            if ($this->ThirdParty->save($this->request->data)) {
                $this->Session->setFlash(__('User status has been updated successfully.'), 'success');
                die('success');
            } else {
                $this->Session->setFlash(__('User status could not updated successfully.'), 'error');
            }
        }
        die('error');
    }

	public function admin_view($id = null) {

		$this->ThirdParty->id = $id;
		if (!$this->ThirdParty->exists()) {
			throw new NotFoundException(__('Invalid blog'));
		}
		
		//pr($this->ThirdParty->read(null, $id)); die;
		
		$this->set('user', $this->ThirdParty->read(null, $id));
		
	}	
	
	public function admin_delete($id = null) {
        if (isset($this->data['id']) && !empty($this->data['id'])) {
            $id = $this->data['id'];
            $this->ThirdParty->id = $id;
            if (!$this->ThirdParty->exists()) {
                throw new NotFoundException(__('Invalid User'), 'error');
            }
			
			$filesdetail = $this->ThirdParty->findById($id);
			if(isset($filesdetail['ThirdParty']['profile_img']) && !empty($filesdetail['ThirdParty']['profile_img'])){
				$oldImage = $filesdetail['ThirdParty']['profile_img'];			
				if( file_exists(WWW_ROOT.THIRD_PARTY_USER_PATH.$oldImage) ){
					@unlink(WWW_ROOT.THIRD_PARTY_USER_PATH.$oldImage);
				}
			}
			
            if ($this->ThirdParty->delete()) {
                $this->Session->setFlash(__('User has been deleted successfully.'), 'success');
                die('success');
            } else {
                $this->Session->setFlash(__('User could not deleted successfully.'), 'error');
            }
        }
        die('error');
    }
	
}