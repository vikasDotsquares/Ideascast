<?php

/**

 * Slider controller.

 *

 * This file will render views from views/slider/

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

 * @link http://book.cakephp.org/2.0/en/controllers/HomeSliders-controller.html

 */

class SlidersController extends AppController {



/**

 * Controller name

 *

 * @var string

 */

	public $name = 'Sliders';
	public $uses = array('HomeSlider','HomeSliders');
	

/**

 * Default helper

 *

 * @var array

 */

	public $helpers = array('Html', 'Session','Js');
	public $components  = array('RequestHandler');

/**

 * This controller does not use a model

 *

 * @var array

 */

	/**

	* check login for admin and frontend user

	* allow and deny user

	*/

	public function beforeFilter() {		

		parent::beforeFilter();			

		$this->Auth->allow( 'setnull','display','available','contactus','slider','contact','pricing-plans','about','privacy','faq','price','terms','product','why_jeera','privacy');		

	}



	/**

	 * Displays a view

	 *

	 * @param mixed What HomeSlider to display

	 * @return void

	 */	
	
	public function admin_display(){		

		$limit=Configure::read('paging.records_per_HomeSlider');
		$this->loadModel('Advert');

		$path=func_get_args();
		$count = count($path);
		if (!$count) {
			$this->redirect('/');
		}
		$HomeSlider = $subHomeSlider = $title_for_layout = null;
		if (!empty($path[0])) {
			$HomeSlider = $path[0];
		}
		if (!empty($path[1])) {
			$subHomeSlider = $path[1];
		}
		if (!empty($path[$count - 1])) {
			$title_for_layout = Inflector::humanize($path[$count - 1]);
		}

		$this->set(compact('HomeSlider', 'subHomeSlider', 'title_for_layout'));

		

		$HomeSlider_slug=implode('/', $path);

		

		$HomeSlider_name = '';

		$this->loadModel('HomeSlider');

		$HomeSliders=$this->HomeSlider->find('first',array('conditions'=>array('HomeSlider.slug'=>$HomeSlider_slug)));
		if(!empty($HomeSliders)){
			$this->set('title_for_layout',$HomeSliders['HomeSlider']['meta_title']);
			$this->set('description_for_layout',$HomeSliders['HomeSlider']['meta_keywords']);
			$this->set('keywords_for_layout',$HomeSliders['HomeSlider']['meta_description']); 
		}
		if($HomeSlider_slug=='home'){
			$this->layout = 'home';
			$HomeSlider_name = 'home';
			$HomeSliders=$this->HomeSlider->find('first',array('conditions'=>array('HomeSlider.id'=>1)));
		if(!empty($HomeSliders)){
			$this->set('title_for_layout',$HomeSliders['HomeSlider']['meta_title']);
			$this->set('description_for_layout',$HomeSliders['HomeSlider']['meta_keywords']);
			$this->set('keywords_for_layout',$HomeSliders['HomeSlider']['meta_description']); 
		}
		//	$this->set('title_for_layout', __('IdeasCast', true));
			
			$this->set('limit',$limit);

			$HomeSliders=$this->HomeSlider->find('first',array('conditions'=>array('HomeSlider.slug'=>'home','HomeSlider.status'=>1)));

			if(isset($HomeSliders) && !empty($HomeSliders)){

				$meta_content_title = $HomeSliders['HomeSlider']['meta_title'];

				$meta_content_description = $HomeSliders['HomeSlider']['meta_description'];

				$meta_content_keywords = $HomeSliders['HomeSlider']['meta_keywords'];

				$mbd_description = $HomeSliders['HomeSlider']['content'];

				$this->set(compact('meta_content_keywords','meta_content_description','meta_content_title','mbd_description'));

			}

			
			//$this->set(compact('HomeSlider_name','categories','top_offers','get_featured_products'));

			
	if(isset($this->data) && !empty($this->data)){
			if(isset($this->data['HomeSlider']['email']) && !empty($this->data['HomeSlider']['email'])){
				$Custemail = $this->data['HomeSlider']['email'];
			}
			
			if(isset($this->data['HomeSlider']['subject']) && !empty($this->data['HomeSlider']['subject'])){
				$subject = $this->data['HomeSlider']['subject'];
			}else{

			$subject = "Enquiry";

			}
			
			if(isset($this->data['HomeSlider']['fname']) && !empty($this->data['HomeSlider']['fname'])){
				$CustnameF = $this->data['HomeSlider']['fname'];
			}

			if(isset($this->data['HomeSlider']['lname']) && !empty($this->data['HomeSlider']['lname'])){
				$CustnameL = $this->data['HomeSlider']['lname'];
			}	

			if(isset($this->data['HomeSlider']['organisation']) && !empty($this->data['HomeSlider']['organisation'])){
				$organisation = $this->data['HomeSlider']['organisation'];
			}				
			
			if(isset($this->data['HomeSlider']['job']) && !empty($this->data['HomeSlider']['job'])){
				$organisation = $this->data['HomeSlider']['job'];
			}			

			
			//pr($this->data['HomeSlider']); die;
			// $from_email = ADMIN_EMAIL;
			$from_email = ADMIN_FROM_EMAIL;
			$from_name = 'IdeasCast';
			//$to = ADMIN_EMAIL;
			
			$to = 'bal.mattu@ideascast.com';			
			
			//$to = 'pawandotsquares@gmail.com';
			
			$Email = new CakeEmail();
            $Email->config('Smtp');			
			$Email->helpers(array('Html','Text'));
			$Email->viewVars(array('formdata' => $this->data['HomeSlider']));
			$Email->template('early_enquiry');
			$Email->emailFormat('html');
			$Email->to($to);
			$Email->from(array($from_email=>$from_name));
			$Email->subject($subject);
			//$Email->send();
			//$this->Session->setFlash('Thank you we will contact you shortly.','success');
			
			// Thanks you mail send to customer
			$to = $Custemail;
			$Email = new CakeEmail();
            $Email->config('Smtp');			
			$Email->helpers(array('Html','Text'));
			$Email->viewVars(array('Custname' => $CustnameF." ".$CustnameL));
			$Email->template('early_customer');
			$Email->emailFormat('html');
			$Email->to($to);
			$Email->from(array($from_email=>$from_name));
			$Email->subject('Thank you for your interest in IdeasCast.');
			//$Email->send();
			
			//return $this->redirect(array('action' => 'early_adopters_new'));
			
			unset($this->data);
			$this->data = '';
		}

			$this->set(compact('HomeSlider_name'));
			$this->render('home');
		
		} else if($HomeSlider_slug=='privacy-policy'){

			//$this->layout = 'default';

			$HomeSliders=$this->HomeSlider->find('first',array('conditions'=>array('HomeSlider.slug'=>$HomeSlider_slug,'HomeSlider.status'=>1)));

			$HomeSlider_name = 'Privacy Policy';

			$meta_content_title = $HomeSliders['HomeSlider']['meta_title'];

			$meta_content_description = $HomeSliders['HomeSlider']['meta_description'];

			$meta_content_keywords = $HomeSliders['HomeSlider']['meta_keywords'];

			$this->set(compact('HomeSlider_name','HomeSliders','meta_content_keywords','meta_content_description','meta_content_title'));

			$this->render('HomeSlider');

		}else if($HomeSlider_slug=='contact'){
			//$this->layout = 'default';

			$HomeSliders=$this->HomeSlider->find('first',array('conditions'=>array('HomeSlider.slug'=>$HomeSlider_slug,'HomeSlider.status'=>1)));

			$HomeSlider_name = 'Contact Us';

			$meta_content_title = $HomeSliders['HomeSlider']['meta_title'];

			$meta_content_description = $HomeSliders['HomeSlider']['meta_description'];

			$meta_content_keywords = $HomeSliders['HomeSlider']['meta_keywords'];

			$this->set(compact('HomeSlider_name','HomeSliders','meta_content_keywords','meta_content_description','meta_content_title'));

			$this->render('HomeSlider');

		}else{

			//$this->layout = 'default';

			$this->loadModel('HomeSlider');



			$HomeSliders=$this->HomeSlider->find('first',array('conditions'=>array('HomeSlider.slug'=>$HomeSlider_slug,'HomeSlider.status'=>1)));

			

			if(!empty($HomeSliders)){

				

				$HomeSlider_name =$HomeSliders['HomeSlider']['name'];				

				$meta_content_title = $HomeSliders['HomeSlider']['meta_title'];

				$meta_content_description = $HomeSliders['HomeSlider']['meta_description'];

				$meta_content_keywords = $HomeSliders['HomeSlider']['meta_keywords'];

				$this->set(compact('HomeSlider_name','HomeSliders','cities','sliders','meta_content_keywords','meta_content_description','meta_content_title'));

				

			}else{

				

				$this->Session->setFlash(__('HomeSlider content is not available.'),'error');

				

				$this->redirect($this->referer());

				

			}

			

			$this->render('HomeSlider');

			

		}

		

	}

	public function admin_index() {

        $orConditions = array();
        $andConditions = array();
        $finalConditions = array();

        $in = 0;
        $per_HomeSlider_show = $this->Session->read('homeslider.per_HomeSlider_show');
        if (empty($per_HomeSlider_show)) {
            $per_HomeSlider_show = ADMIN_PAGING;
        }

        if (isset($this->data['HomeSlider']['keyword'])) {
            $keyword = trim($this->data['HomeSlider']['keyword']);
        } else {
            $keyword = $this->Session->read('HomeSlider.keyword');
        }

        if (isset($keyword)) {
            $this->Session->write('homeslider.keyword', $keyword);
            $keywords = explode(" ", $keyword);
            if (isset($keywords[0]) && !empty($keywords[0]) && count($keywords) < 2) {
                $keyword = $keywords[0];
                $in = 1;
                $orConditions = array('OR' => array(
                        'HomeSlider.slider_title LIKE' => '%' . $keyword . '%',                     
                    
                ));
            } else if (!empty($keywords) && count($keywords) > 1) {
                $name = $keywords[0];               
                $in = 1;
                $andConditions = array('AND' => array(
                        'HomeSlider.slider_title LIKE' => '%' . $first_name . '%',                       
                ));
            }
        }

        if (isset($this->data['HomeSlider']['status'])) {
            $status = $this->data['HomeSlider']['status'];
        } else {
            $status = $this->Session->read('HomeSlider.status');
        }

        if (isset($status)) {
            $this->Session->write('HomeSlider.status', $status);
            if ($status != '') {
                $in = 1;
                $andConditions = array_merge($andConditions, array('HomeSlider.status' => $status));
            }
        }

        if (isset($this->data['HomeSlider']['per_HomeSlider_show']) && !empty($this->data['HomeSlider']['per_HomeSlider_show'])) {
            $per_HomeSlider_show = $this->data['HomeSlider']['per_HomeSlider_show'];
        }


        if (!empty($orConditions)) {
            $finalConditions = array_merge($finalConditions, $orConditions);
        }
        if (!empty($andConditions)) {
            $finalConditions = array_merge($finalConditions, array('AND' => $andConditions));
        }
		
		$count = $this->HomeSlider->find('count', array('conditions' => $finalConditions));
        $this->set('count', $count);

        $this->set('title_for_layout', __('All Slider', true));
        $this->Session->write('homeslider.per_HomeSlider_show', $per_HomeSlider_show);
        $this->HomeSlider->recursive = 0;
        $this->paginate = array('conditions' => $finalConditions, "limit" => $per_HomeSlider_show, "order" => "HomeSlider.created DESC");
        $this->set('sliders', $this->paginate('HomeSlider'));
        $this->set('in', $in); 
    }

	public function admin_add() {		
		 
			$width = $height = 0; 	
			if ($this->request->is('post') || $this->request->is('put')) {
					
					//$this->request->data['HomeSlider']['status'] = 1;	
					if( $this->HomeSlider->validates($this->data) ){
					
						$folder_url = WWW_ROOT . HOME_SLIDER_PATH;
					
						if(isset($this->request->data['HomeSlider']['slider_image']) && !empty($this->request->data['HomeSlider']['slider_image'])) {
						
							$image = $this->request->data['HomeSlider']['slider_image'];					
							 
							$ex = explode('.', $image['name']);
							$nme = $ex[0];
							$ext = end($ex);
							$imagename = $nme . '_' .time(). "." . $ext; 
							$imagename = preg_replace('/\s+/', '_', $imagename);
							$imagename = str_replace(' ', '_', $imagename);
							
							if(isset($image['tmp_name']) && !empty($image['tmp_name'])){
								list($width, $height) = getimagesize($image['tmp_name']);
							}
							
							if((isset($this->request->data['HomeSlider']['slider_title']) && !empty($this->request->data['HomeSlider']['slider_title'])) &&  (isset($this->request->data['HomeSlider']['slider_text']) && !empty($this->request->data['HomeSlider']['slider_text']))) {
							
								if(( $width == 2000 ) && ($height == 650 )) {
									$resized = true;
									//$this->resizes($image, $folder_url);
								  
									if( copy($image['tmp_name'], $folder_url . $imagename) ) {
										
										$this->request->data['HomeSlider']['slider_image'] = $imagename;
									}
									
								} else {							
									$this->Session->setFlash(__('Image should be at least 2000x650, So please, try again.'),'error');
								}
								
								if( $this->HomeSlider->save($this->request->data)){
									$this->Session->setFlash(__('HomeSlider content has been saved'),'success');
									$this->redirect(array('action' => 'index'));
								}
							}	
						}
					}	
			   }
			 
			
		
		$breadcrumb = array(
							
							array(
								'title' => 'HomeSlider Manager',
								'url' => Router::url(array('controller'=>'sliders', 'action'=>'index', 'admin'=>true))
							),
							array(
								'title' => 'Add HomeSlider'
							)
				);
		
		$this->set(compact('breadcrumb'));

	}


	public function admin_edit($id = null,$pageName = null) {		

			$this->HomeSlider->id = $id;

			if (!$this->HomeSlider->exists()) {

				$this->Session->setFlash(__('Invalid Slider'),'error');

				$this->redirect(array('action' => 'index'));

			}
			
			$width = $height = 0; 	
			if ($this->request->is('post') || $this->request->is('put')) {
				
				$folder_url = WWW_ROOT . HOME_SLIDER_PATH;
			
				if(isset($this->request->data['HomeSlider']['slider_image']) && !empty($this->request->data['HomeSlider']['slider_image'])) {
				
					$image = $this->request->data['HomeSlider']['slider_image'];					
					
					if( isset($image['tmp_name']) && !empty($image['tmp_name']) ){
						
						$ex = explode('.', $image['name']);
						$nme = $ex[0];
						$ext = end($ex);
						$imagename = $nme . '_' .time(). "." . $ext; 
						$imagename = preg_replace('/\s+/', '_', $imagename);
						$imagename = str_replace(' ', '_', $imagename);
						
						if(isset($image['tmp_name']) && !empty($image['tmp_name'])){
							list($width, $height) = getimagesize($image['tmp_name']);
						}					
						
						if(( $width >= 1000 ) && ($height >= 350 )) {
							$resized = true;
							
							if( copy($image['tmp_name'], $folder_url . $imagename) ) {
								$this->request->data['HomeSlider']['slider_image'] = $imagename;
							}
							
						} else {							
							$this->Session->setFlash(__('Slider Image should be at least 2000x650, So please, try again.'),'error');
						}
						
					} else {
						unset($this->request->data['HomeSlider']['slider_image']);
					}	
						
					if(!array_key_exists('status',$this->request->data['HomeSlider'])){
						$this->request->data['HomeSlider']['status']=0;
					}
					
					if( $this->HomeSlider->save($this->request->data)){
						$this->Session->setFlash(__('Slider has been updated'),'success');
						$this->redirect(array('action' => 'index'));
					}
				}
			}	

			$this->request->data = $this->HomeSlider->read(null, $id);
			$breadcrumb = array(							
				array(
					'title' => 'Slider Manager',
					'url' => Router::url(array('controller'=>'sliders', 'action'=>'index', 'admin'=>true))
				),
				array(
					'title' => 'Edit Slider'
				)
			);
		
			$this->set(compact('breadcrumb'));			

	}

		 
	public function admin_slider_updatestatus() {
        if ($this->request->is('ajax')) {
            $this->autoRender = false;
            
            $this->request->data['HomeSlider'] = $this->request->data;
			
            if ($this->HomeSlider->save($this->request->data)) {
                $this->Session->setFlash(__('Slider status has been updated successfully.'), 'success');
                die('success');
            } else {
                $this->Session->setFlash(__('Slider status could not updated successfully.'), 'error');
            }
        }
        die('error');
    } 
	 
	 
    public function admin_delete($id = null) {
        if (isset($this->data['id']) && !empty($this->data['id'])) {
            $id = $this->data['id'];
            $this->HomeSlider->id = $id;
            if (!$this->HomeSlider->exists()) {
                throw new NotFoundException(__('Invalid Page'), 'error');
            }

            if ($this->HomeSlider->delete()) {
                $this->Session->setFlash(__('Slider has been deleted successfully.'), 'success');
                die('success');
            } else {
                $this->Session->setFlash(__('Slider could not deleted successfully.'), 'error');
            }
        }
        die('error');
    }
	
	
	function admin_slider_resetfilter() {
        $this->Session->write('homeslider.keyword', '');
        $this->Session->write('homeslider.status', '');
        $this->redirect(array('action' => 'index'));
    }
	 
	

	
}