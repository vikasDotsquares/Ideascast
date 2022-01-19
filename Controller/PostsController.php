<?php

/**

 * Posts controller.

 *

 * This file will render views from views/posts/

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

class PostsController extends AppController {

/**

 * Controller name

 *

 * @var string

 */

	public $name = 'Posts';

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
	
	public function page_demo() {
			
	}

	public function beforeFilter() {		

		parent::beforeFilter();			

		$this->Auth->allow('setnull','display','showblog','blogdetails','deletepostimage','unlinkBlogImage');	
		
		//pr($this->request->params);
		//$this->request->params['named']['page'] = (isset($this->request->params['page'])) ? $this->request->params['page'] : 1;		

	}	


    public function admin_index() {

        $orConditions = array();
        $andConditions = array();
        $finalConditions = array();

        $in = 0;
        $per_page_show = $this->Session->read('post.per_page_show');
        if (empty($per_page_show)) {
            $per_page_show = ADMIN_PAGING;
        }
		 
        if (isset($this->data['Post']['keyword']) && !empty($this->data['Post']['keyword']) && strlen($this->data['Post']['keyword']) > 0 ) {
			$keyword = trim($this->data['Post']['keyword']);
        } else {
            $keyword = $this->Session->read('post.keyword');
        }
		
        if (isset($keyword) && strlen($keyword) > 0 ) {
			$this->Session->write('post.keyword', $keyword);
			$in = 1;
			$orConditions = array('OR' => array('Post.title LIKE' => '%' . $keyword . '%'));
        }		

        if (isset($this->data['Post']['status'])) {
            $status = $this->data['Post']['status'];
        } else {
            $status = $this->Session->read('post.status');
        }

        if (isset($status)) {
            $this->Session->write('post.status', $status);
            if ($status != '') {
                $in = 1;
                $andConditions = array_merge($andConditions, array('Post.status' => $status));
            }
        }

        if (isset($this->data['Post']['per_page_show']) && !empty($this->data['Post']['per_page_show'])) {
            $per_page_show = $this->data['Post']['per_page_show'];
        }


        if (!empty($orConditions)) {
            $finalConditions = array_merge($finalConditions, $orConditions);
        }
        if (!empty($andConditions)) {
            $finalConditions = array_merge($finalConditions, array('AND' => $andConditions));
        }
		
		$count = $this->Post->find('count', array('conditions' => $finalConditions));
        $this->set('count', $count);

        $this->set('title_for_layout', __('All Blogs', true));
        $this->Session->write('post.per_page_show', $per_page_show);
        $this->Post->recursive = 0;
        $this->paginate = array('conditions' => $finalConditions, "limit" => $per_page_show, "order" => "Post.created DESC");
        $this->set('allblog', $this->paginate('Post'));
        $this->set('in', $in);
    }

	public function admin_add() {		

		if ($this->request->is('post') || $this->request->is('put')) {
			
			//pr($this->request->data); die;
			
			if(isset($this->request->data['Post']['blog_img']['name']) && empty($this->request->data['Post']['blog_img']['name']) ){
				unset($this->request->data['Post']['blog_img']);
			}
			$this->request->data['Post']['slug'] = Inflector::slug(strtolower($this->request->data['Post']['title']),'-');
			
			$this->Post->create();
			if ($this->Post->save($this->request->data)) {
				
					$this->Session->setFlash(__('Blog has been saved'),'success');
					$this->redirect(array('action' => 'index'));
					
			} else {
			 if(isset($this->Post->validationErrors['slug']['0']) && !empty($this->Post->validationErrors['slug']['0'])){
			 
			    //$this->set('slug_error',$this->Post->validationErrors['slug']['0']);
				$this->Post->validationErrors['title'] = $this->Post->validationErrors['slug']['0'];
			 }
				$this->Session->setFlash(__('Blog could not be saved. Please, try again.'),'error');
			}
		}
		$breadcrumb = array(
							
							array(
								'title' => 'Blog Manager',
								'url' => Router::url(array('controller'=>'posts', 'action'=>'index', 'admin'=>true))
							),
							array(
								'title' => 'Add Blog'
							)
				);
		
		$this->set(compact('breadcrumb'));

	}	

	public function admin_edit($id = null,$pageName = null) {

		$this->Post->id = $id;

		//check category exist

		if (!$this->Post->exists()) {

			$this->Session->setFlash(__('Invalid post'),'error');

			$this->redirect(array('action' => 'index'));

		}		

		if ($this->request->is('post') || $this->request->is('put')) {

			//Set page as inactive if it is unchecked
			
			if(!array_key_exists('status',$this->request->data['Post'])){

				$this->request->data['Post']['status']=0;

			}

					
			
			if(isset($this->request->data['Post']['blog_img']['name']) && empty($this->request->data['Post']['blog_img']['name']) ){
				unset($this->request->data['Post']['blog_img']);
			} else {
				$filesdetail = $this->Post->findById($this->request->data['Post']['id']);
				$oldImage = $filesdetail['Post']['blog_img'];
				
				if( file_exists(WWW_ROOT.POST_RESIZE_PIC_PATH.$oldImage) ){
					@unlink(WWW_ROOT.POST_RESIZE_PIC_PATH.$oldImage);  
					@unlink(WWW_ROOT.POST_PIC_PATH.$oldImage);  
				}
			}
			
			$this->request->data['Post']['slug'] = Inflector::slug(strtolower($this->request->data['Post']['title']),'-');
			
			//pr($this->request->data); die;
			
			if ($this->Post->save($this->request->data)) {

					 $this->Session->setFlash(__('Blog has been updated'),'success');
					 $this->redirect(array('action' => 'index'));

			}else{
				 
				 $this->Session->setFlash(__('Blog could not be saved. Please, try again.'),'error');
			} 
			

		}else{
			
			$this->request->data = $this->Post->read(null, $id);
		}
		
		$breadcrumb = array(							
				array(
					'title' => 'Blog Manager',
					'url' => Router::url(array('controller'=>'posts', 'action'=>'index', 'admin'=>true))
				),
				array(
					'title' => 'Edit Blog'
				)
		);

		$this->set(compact('breadcrumb'));			

	}	
	
	public function admin_view($id = null) {

		$this->Post->id = $id;
		if (!$this->Post->exists()) {
			throw new NotFoundException(__('Invalid blog'));
		}
		$this->set('post', $this->Post->read(null, $id));
		
	}	

	public function admin_post_resetfilter() {
        $this->Session->write('post.keyword', '');
        $this->Session->write('post.status', '');
        
        $this->redirect(array('action' => 'index'));
    }
	 
	public function admin_post_updatestatus() {
        if ($this->request->is('ajax')) {
            $this->autoRender = false;
            $this->loadModel('Post');
            $this->request->data['Post'] = $this->request->data;

            if ($this->Post->save($this->request->data)) {
                $this->Session->setFlash(__('Blog status has been updated successfully.'), 'success');
                die('success');
            } else {
                $this->Session->setFlash(__('Blog status could not updated successfully.'), 'error');
            }
        }
        die('error');
    } 
	 
    public function admin_delete($id = null) {
        if (isset($this->data['id']) && !empty($this->data['id'])) {
            $id = $this->data['id'];
            $this->Post->id = $id;
            if (!$this->Post->exists()) {
                throw new NotFoundException(__('Invalid Blog'), 'error');
            }
            if ($this->Post->delete()) {
                $this->Session->setFlash(__('Blog has been deleted successfully.'), 'success');
                die('success');
            } else {
                $this->Session->setFlash(__('Blog could not deleted successfully.'), 'error');
            }
        }
        die('error');
    }

	public function admin_update_status() {

		if($this->request->is('ajax'))
		{
			$this->autoRender = false;

			$this->request->data['Post']['id'] = $this->data['id'];

			$this->request->data['Post']['status'] = $this->params['data']['status'];

			if($this->Post->save($this->request->data)){
				return true;					
			}else{
				return false;
			}
		}
	}

	public function showblog(){
		
		$orConditions = array();
        $andConditions = array();
        $finalConditions = array();

        $in = 0;
        $per_page_show = $this->Session->read('post.per_page_show');
       	
        if (empty($per_page_show)) {
            $per_page_show = ADMIN_PAGING;
        }
		
		$finalConditions = array('Post.status'=>1);
		$count = $this->Post->find('count', array('conditions' => $finalConditions));
        $this->set('count', $count);

        $this->set('title_for_layout','Blogs - Social Networking Site | IdeasCast');		
        $this->Session->write('post.per_page_show', $per_page_show);
        $this->Post->recursive = 0;
        $this->paginate = array('conditions' => $finalConditions, "limit" => $per_page_show, "order" => "Post.created DESC");
		//pr($this->paginate('Post')); die;
        $this->set('bloglist', $this->paginate('Post'));	
			
	}
	
	//public function blogdetails($id = null) {	
	public function blogdetails($slug = null) {	
		
		if (!$this->Post->findBySlug($slug)) {
			$this->Session->setFlash(__('Invalid Blog.'), 'error');
			$this->redirect('/blog');
		}
		
		$finalConditions = array('Post.status'=>1);
		$rdata = $this->Post->find("all", array('conditions' => $finalConditions, "limit" => 100, "order" => "Post.created DESC"));
		
		$posts = $this->Post->findBySlug($slug);
		
		if(!empty($posts)){
			$this->set('title_for_layout',ucfirst(strtolower(strip_tags($posts['Post']['title']))).' - Social Networking Site | IdeasCast');			
			
		}		
		
		$this->set('recentBlogList', $rdata);		
		$this->set('blog', $posts); 
		
	}
	
	/* public function recentblog($limit=5, $order='created'){
		
		$this->autoRender = false;
		$this->layout = false;
		$finalConditions = array('Post.status'=>1);
		
		$data = $this->Post->find("all", array('conditions' => $finalConditions, "limit" => $limit, "order" => "Post.".$order." DESC"));
		//pr($data); 
        $this->set('recentBlogList', $data);
		
	} */
	
	public function deletepostimage() {
		
        if ($this->request->is('ajax')) {
            $this->autoRender = false;
            $this->loadModel('Post');
			
			if( isset($this->request->data['blogid']) && !empty($this->request->data['blogImg']) ){
				
				$this->request->data['Post']['id'] = $this->request->data['blogid'];
				$this->request->data['Post']['blog_img_old'] = $this->request->data['blogImg'];
				$this->request->data['Post']['blog_img'] = null;
				
				unset($this->request->data['blogid']);
				unset($this->request->data['blogImg']);
				
				//pr($this->request->data,1);
				  if ($this->Post->save($this->request->data['Post'])) {
					 
					unlink(WWW_ROOT.POST_RESIZE_PIC_PATH.$this->request->data['Post']['blog_img_old']);  
					unlink(WWW_ROOT.POST_PIC_PATH.$this->request->data['Post']['blog_img_old']);  
					
					$this->Session->setFlash(__('Blog image has been deleted successfully.'), 'success');
					die('success');
				 } else {
					  $this->Session->setFlash(__('Blog image could not deleted.'), 'error');
				  }
			} else {
				$this->Session->setFlash(__('Blog image could not deleted.'), 'error');
			}	
        }
        die('error');
    }
	
	 
	
}