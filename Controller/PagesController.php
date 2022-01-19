<?php

/**

 * Static content controller.

 *

 * This file will render views from views/pages/

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

App::uses('Sanitize', 'Utility');

/**

 * Static content controller

 *

 * Override this controller by placing a copy in controllers directory of an application

 *

 * @package       app.Controller

 * @link http://book.cakephp.org/2.0/en/controllers/pages-controller.html

 */

class PagesController extends AppController {

/**

 * Controller name

 *

 * @var string

 */

	public $name = 'Pages';

/**

 * Default helper

 *

 * @var array

 */

	public $helpers = array('Html', 'Session', 'Js');

	public $components = array('RequestHandler');

	public function changeLanguage($lng) {
		// Change language method
		if (isset($this->availableLanguages[$lng])) { // If we support this language (see /app/Config/global.php)
			parent::setLang($lng); // call setLang() from AppController
			$this->Session->setFlash(__('The language has been changed to %s', $this->availableLanguages[$lng])); // Send a success flash message
		} else {
			throw new NotFoundException(__('Language %s is not supported', $lng)); // Throw a not found exception
		}

		$this->redirect($this->referer()); // redirect the user to the last page (referer)
	}

	public function page_demo() {

	}
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

		$this->Auth->allow('setnull', 'display', 'available', 'contactus', 'slider', 'contact', 'pricing-plans', 'about', 'privacy', 'faq', 'price', 'terms', 'product', 'why_jeera', 'privacy', 'why_jeera_solution', 'why_jeera_benefits', 'why_jeera_focus', 'why_jeera_approach', 'contactfordemo', 'downloads', 'newcontactus', 'downloads_doc', 'empoweringteamwork', 'jeera_offer', 'jeera_demo', 'sethome', 'features', 'request_demo', 'how_buy', 'partners', 'templates', 'treeview');

	}

	/**

	 * Displays a view

	 *

	 * @param mixed What page to display

	 * @return void

	 */

	public function sample() {

	}

	public function display() {

		$limit = Configure::read('paging.records_per_page');
		$this->loadModel('Advert');

		$path = func_get_args();
		$count = count($path);
		if (!$count) {
			$this->redirect('/');
		}
		$page = $subpage = $title_for_layout = null;
		if (!empty($path[0])) {
			$page = $path[0];
		}
		if (!empty($path[1])) {
			$subpage = $path[1];
		}
		if (!empty($path[$count - 1])) {
			$title_for_layout = Inflector::humanize($path[$count - 1]);
		}

		$this->set(compact('page', 'subpage', 'title_for_layout'));

		$page_slug = implode('/', $path);

		$page_name = '';

		$this->loadModel('Page');

		$pages = $this->Page->find('first', array('conditions' => array('Page.slug' => $page_slug)));
		if (!empty($pages)) {
			$this->set('title_for_layout', $pages['Page']['meta_title']);
			$this->set('description_for_layout', $pages['Page']['meta_keywords']);
			$this->set('keywords_for_layout', $pages['Page']['meta_description']);
		}

		if ($page_slug == 'home') {
			$this->layout = 'home';
			$page_name = 'home';
			$pages = $this->Page->find('first', array('conditions' => array('Page.id' => 1)));
			if (!empty($pages)) {
				$this->set('title_for_layout', $pages['Page']['meta_title']);
				$this->set('description_for_layout', $pages['Page']['meta_keywords']);
				$this->set('keywords_for_layout', $pages['Page']['meta_description']);
			}
			//	$this->set('title_for_layout', __('IdeasCast', true));

			$this->set('limit', $limit);

			$pages = $this->Page->find('first', array('conditions' => array('Page.slug' => 'home', 'Page.status' => 1)));

			if (isset($pages) && !empty($pages)) {

				$meta_content_title = $pages['Page']['meta_title'];

				$meta_content_description = $pages['Page']['meta_description'];

				$meta_content_keywords = $pages['Page']['meta_keywords'];

				$mbd_description = $pages['Page']['content'];

				$this->set(compact('meta_content_keywords', 'meta_content_description', 'meta_content_title', 'mbd_description'));

			}

			//$this->set(compact('page_name','categories','top_offers','get_featured_products'));

			if (isset($this->data) && !empty($this->data)) {
				if (isset($this->data['Page']['email']) && !empty($this->data['Page']['email'])) {
					$Custemail = $this->data['Page']['email'];
				}

				if (isset($this->data['Page']['subject']) && !empty($this->data['Page']['subject'])) {
					$subject = $this->data['Page']['subject'];
				} else {

					$subject = "Enquiry";

				}

				if (isset($this->data['Page']['fname']) && !empty($this->data['Page']['fname'])) {
					$CustnameF = $this->data['Page']['fname'];
				}

				if (isset($this->data['Page']['lname']) && !empty($this->data['Page']['lname'])) {
					$CustnameL = $this->data['Page']['lname'];
				}

				if (isset($this->data['Page']['organisation']) && !empty($this->data['Page']['organisation'])) {
					$organisation = $this->data['Page']['organisation'];
				}

				if (isset($this->data['Page']['job']) && !empty($this->data['Page']['job'])) {
					$organisation = $this->data['Page']['job'];
				}

				//pr($this->data['Page']); die;
				// $from_email = 'info@ideascast.com';
				$from_email = ADMIN_FROM_EMAIL;
				$from_name = 'IdeasCast';
				$to = ADMIN_EMAIL;
				//$to = 'bal.mattu@ideascast.com';
				//$to = 'pawansharma13@gmail.com';

				$Email = new CakeEmail();
				$Email->config('Smtp');
				$Email->helpers(array('Html', 'Text'));
				$Email->viewVars(array('formdata' => $this->data['Page']));
				$Email->template('early_enquiry');
				$Email->emailFormat('html');
				$Email->to($to);
				$Email->from(array($from_email => $from_name));
				$Email->subject($subject);
				//$Email->send();
				//$this->Session->setFlash('Thank you we will contact you shortly.', 'success');

				// Thanks you mail send to customer
				$to = $Custemail;
				$Email = new CakeEmail();
				$Email->config('Smtp');
				$Email->helpers(array('Html', 'Text'));
				$Email->viewVars(array('Custname' => $CustnameF . " " . $CustnameL));
				$Email->template('early_customer');
				$Email->emailFormat('html');
				$Email->to($to);
				$Email->from(array($from_email => $from_name));
				$Email->subject('Thank you for your interest in IdeasCast.');
				//$Email->send();

				//return $this->redirect(array('action' => 'early_adopters_new'));

				unset($this->data);
				$this->data = '';
			}

			$this->set(compact('page_name'));
			$this->render('home');

		} else if ($page_slug == 'privacy-policy') {

			//$this->layout = 'default';

			$pages = $this->Page->find('first', array('conditions' => array('Page.slug' => $page_slug, 'Page.status' => 1)));

			$page_name = 'Privacy Policy';

			$meta_content_title = $pages['Page']['meta_title'];

			$meta_content_description = $pages['Page']['meta_description'];

			$meta_content_keywords = $pages['Page']['meta_keywords'];

			$this->set(compact('page_name', 'pages', 'meta_content_keywords', 'meta_content_description', 'meta_content_title'));

			$this->render('page');

		} else if ($page_slug == 'contact') {
			//$this->layout = 'default';

			$pages = $this->Page->find('first', array('conditions' => array('Page.slug' => $page_slug, 'Page.status' => 1)));

			$page_name = 'Contact Us';

			$meta_content_title = $pages['Page']['meta_title'];

			$meta_content_description = $pages['Page']['meta_description'];

			$meta_content_keywords = $pages['Page']['meta_keywords'];

			$this->set(compact('page_name', 'pages', 'meta_content_keywords', 'meta_content_description', 'meta_content_title'));

			$this->render('page');

		} else {

			//$this->layout = 'default';

			$this->loadModel('Page');

			$pages = $this->Page->find('first', array('conditions' => array('Page.slug' => $page_slug, 'Page.status' => 1)));

			if (!empty($pages)) {

				$this->set('title_for_layout', " cusome name === ");

				$page_name = $pages['Page']['name'];

				$meta_content_title = $pages['Page']['meta_title'];

				$meta_content_description = $pages['Page']['meta_description'];

				$meta_content_keywords = $pages['Page']['meta_keywords'];

				$this->set(compact('page_name', 'pages', 'cities', 'sliders', 'meta_content_keywords', 'meta_content_description', 'meta_content_title'));

			} else {

				$this->Session->setFlash(__('Page content is not available.'), 'error');

				$this->redirect($this->referer());

			}

			$this->render('page');

		}

	}

	public function admin_index() {

		$orConditions = array();
		$andConditions = array();
		$finalConditions = array();

		$in = 0;
		$per_page_show = $this->Session->read('page.per_page_show');
		if (empty($per_page_show)) {
			$per_page_show = ADMIN_PAGING;
		}

		if (isset($this->data['Page']['keyword'])) {
			$keyword = trim($this->data['Page']['keyword']);
		} else {
			$keyword = $this->Session->read('page.keyword');
		}

		if (isset($keyword)) {
			$this->Session->write('page.keyword', $keyword);
			$keywords = explode(" ", $keyword);
			if (isset($keywords[0]) && !empty($keywords[0]) && count($keywords) < 2) {
				$keyword = $keywords[0];
				$in = 1;
				$orConditions = array('OR' => array(
					'Page.name LIKE' => '%' . $keyword . '%',
					'Page.slug LIKE' => '%' . $keyword . '%',

				));
			} else if (!empty($keywords) && count($keywords) > 1) {
				$name = $keywords[0];
				$slug = $keywords[1];
				$in = 1;
				$andConditions = array('AND' => array(
					'Page.name LIKE' => '%' . $first_name . '%',
					'Page.slug LIKE' => '%' . $last_name . '%',
				));
			}
		}

		if (isset($this->data['Page']['status'])) {
			$status = $this->data['Page']['status'];
		} else {
			$status = $this->Session->read('page.status');
		}

		if (isset($status)) {
			$this->Session->write('page.status', $status);
			if ($status != '') {
				$in = 1;
				$andConditions = array_merge($andConditions, array('page.status' => $status));
			}
		}

		if (isset($this->data['Page']['per_page_show']) && !empty($this->data['Page']['per_page_show'])) {
			$per_page_show = $this->data['Page']['per_page_show'];
		}

		if (!empty($orConditions)) {
			$finalConditions = array_merge($finalConditions, $orConditions);
		}
		if (!empty($andConditions)) {
			$finalConditions = array_merge($finalConditions, array('AND' => $andConditions));
		}

		$count = $this->Page->find('count', array('conditions' => $finalConditions));
		$this->set('count', $count);

		$this->set('title_for_layout', __('All Pages', true));
		$this->Session->write('page.per_page_show', $per_page_show);
		$this->Page->recursive = 0;
		$this->paginate = array('conditions' => $finalConditions, "limit" => $per_page_show, "order" => "Page.created DESC");
		$this->set('pages', $this->paginate('Page'));
		$this->set('in', $in);
	}

	/**

	 * admin_add method

	 *

	 * @return void

	 */

	public function admin_add() {

		if ($this->request->is('post') || $this->request->is('put')) {

			//pr($this->data);die;

			$this->request->data['Page']['slug'] = str_replace(' ', '-', strtolower($this->request->data['Page']['name']));

			$this->request->data['Page']['is_active'] = 1;

			$this->Page->create();

			if ($this->Page->save($this->request->data)) {

				$this->Session->setFlash(__('Page content has been saved'), 'success');

				$this->redirect(array('action' => 'index'));

			} else {

				$this->Session->setFlash(__('Page content could not be saved. Please, try again.'), 'error');

			}

		}
		$breadcrumb = array(

			array(
				'title' => 'Page Manager',
				'url' => Router::url(array('controller' => 'pages', 'action' => 'index', 'admin' => true)),
			),
			array(
				'title' => 'Add Page',
			),
		);

		$this->set(compact('breadcrumb'));

	}
	/**

	 * admin_edit method

	 */

	public function admin_edit($id = null, $pageName = null) {

		$this->Page->id = $id;

		//check category exist

		if (!$this->Page->exists()) {

			$this->Session->setFlash(__('Invalid page'), 'error');

			$this->redirect(array('action' => 'index'));

		}

		if ($this->request->is('post') || $this->request->is('put')) {

			//Set page as inactive if it is unchecked

			if (!array_key_exists('is_active', $this->request->data['Page'])) {

				$this->request->data['Page']['is_active'] = 0;

			}

			//$this->request->data['Page']['slug']=str_replace(' ','-',strtolower($this->request->data['Page']['name']));

			if ($this->Page->save($this->request->data)) {

				$this->Session->setFlash(__('Page content has been saved'), 'success');

				$this->redirect(array('action' => 'index'));

				//		$this->redirect(array('controller'=>'pages','action' => 'edit',$id,$pageName,'admin'=>true));

			} else {

				$this->Session->setFlash(__('Page content could not be saved. Please, try again.'), 'error');

			}

		}

		$this->request->data = $this->Page->read(null, $id);
		$breadcrumb = array(

			array(
				'title' => 'Page Manager',
				'url' => Router::url(array('controller' => 'pages', 'action' => 'index', 'admin' => true)),
			),
			array(
				'title' => 'Edit Page',
			),
		);

		$this->set(compact('breadcrumb'));

	}

	public function about() {

		$pages = $this->Page->find('first', array('conditions' => array('Page.id' => '17')));
		if (!empty($pages)) {
			$this->set('title_for_layout', $pages['Page']['meta_title']);
			$this->set('description_for_layout', $pages['Page']['meta_keywords']);
			$this->set('keywords_for_layout', $pages['Page']['meta_description']);
			//echo $pages['Page']['meta_title']; die;
		}

		//$this->set('title_for_layout', __('About Us', true));
		$data = $this->Page->find('first', array('conditions' => array('Page.slug' => 'about')));
		$content = $data['Page']['content'];
		$this->set('content', $content);
	}

	public function faq() {

		$pages = $this->Page->find('first', array('conditions' => array('Page.id' => '39')));
		if (!empty($pages)) {
			$this->set('title_for_layout', $pages['Page']['meta_title']);
			$this->set('description_for_layout', $pages['Page']['meta_keywords']);
			$this->set('keywords_for_layout', $pages['Page']['meta_description']);
		}
		//	$this->set('title_for_layout', __('FAQ', true));
	}

	public function price() {
		$pages = $this->Page->find('first', array('conditions' => array('Page.id' => '41')));
		if (!empty($pages)) {
			$this->set('title_for_layout', $pages['Page']['meta_title']);
			$this->set('description_for_layout', $pages['Page']['meta_keywords']);
			$this->set('keywords_for_layout', $pages['Page']['meta_description']);
		}
		//$this->set('title_for_layout', __('Free & Plans', true));
		$data = $this->Page->find('first', array('conditions' => array('Page.slug' => 'pricing-plans')));
		$content = $data['Page']['content'];
		$this->set('content', $content);

	}

	public function downloads() {
		$pages = $this->Page->find('first', array('conditions' => array('Page.id' => '46')));
		if (!empty($pages)) {
			$this->set('title_for_layout', $pages['Page']['meta_title']);
			$this->set('description_for_layout', $pages['Page']['meta_keywords']);
			$this->set('keywords_for_layout', $pages['Page']['meta_description']);
		}
		//$this->set('title_for_layout', __('Free & Plans', true));
		$data = $this->Page->find('first', array('conditions' => array('Page.slug' => 'pricing-plans')));
		$content = $data['Page']['content'];
		$this->set('content', $content);
	}

	public function why_jeera() {
		$pages = $this->Page->find('first', array('conditions' => array('Page.id' => '45')));
		if (!empty($pages)) {
			$this->set('title_for_layout', $pages['Page']['meta_title']);
			$this->set('description_for_layout', $pages['Page']['meta_keywords']);
			$this->set('keywords_for_layout', $pages['Page']['meta_description']);
		}
		//	$this->set('title_for_layout', __('Privacy Policy', true));
		$data = $this->Page->find('first', array('conditions' => array('Page.slug' => 'software')));
		$content = $data['Page']['content'];
		$this->set('content', $content);
	}

	public function why_jeera_benefits() {
		$pages = $this->Page->find('first', array('conditions' => array('Page.id' => '45')));
		if (!empty($pages)) {
			$this->set('title_for_layout', $pages['Page']['meta_title']);
			$this->set('description_for_layout', $pages['Page']['meta_keywords']);
			$this->set('keywords_for_layout', $pages['Page']['meta_description']);
		}
	}

	public function why_jeera_focus() {
		$pages = $this->Page->find('first', array('conditions' => array('Page.id' => '45')));
		if (!empty($pages)) {
			$this->set('title_for_layout', $pages['Page']['meta_title']);
			$this->set('description_for_layout', $pages['Page']['meta_keywords']);
			$this->set('keywords_for_layout', $pages['Page']['meta_description']);
		}
	}

	public function why_jeera_approach() {
		$pages = $this->Page->find('first', array('conditions' => array('Page.id' => '45')));
		if (!empty($pages)) {
			$this->set('title_for_layout', $pages['Page']['meta_title']);
			$this->set('description_for_layout', $pages['Page']['meta_keywords']);
			$this->set('keywords_for_layout', $pages['Page']['meta_description']);
		}
	}

	public function why_jeera_solution() {
		$pages = $this->Page->find('first', array('conditions' => array('Page.id' => '45')));
		if (!empty($pages)) {
			$this->set('title_for_layout', $pages['Page']['meta_title']);
			$this->set('description_for_layout', $pages['Page']['meta_keywords']);
			$this->set('keywords_for_layout', $pages['Page']['meta_description']);
		}
	}

	public function privacy() {
		$pages = $this->Page->find('first', array('conditions' => array('Page.id' => '40')));
		if (!empty($pages)) {
			$this->set('title_for_layout', $pages['Page']['meta_title']);
			$this->set('description_for_layout', $pages['Page']['meta_keywords']);
			$this->set('keywords_for_layout', $pages['Page']['meta_description']);
		}

		$domain = ($_SERVER['HTTP_HOST'] != 'localhost') ? $_SERVER['HTTP_HOST'] : false;
		setcookie('ideascvalid', 1, time() + 60 * 60 * 24, '/', $domain, false);

		//	$this->set('title_for_layout', __('Privacy Policy', true));
		$data = $this->Page->find('first', array('conditions' => array('Page.slug' => 'privacy')));
		$content = $data['Page']['content'];
		$this->set('content', $content);
	}

	public function terms() {
		$pages = $this->Page->find('first', array('conditions' => array('Page.id' => '42')));
		if (!empty($pages)) {
			$this->set('title_for_layout', $pages['Page']['meta_title']);
			$this->set('description_for_layout', $pages['Page']['meta_keywords']);
			$this->set('keywords_for_layout', $pages['Page']['meta_description']);
		}
		// 	$this->set('title_for_layout', __('Terms Of Use', true));
		$data = $this->Page->find('first', array('conditions' => array('Page.slug' => 'terms-of-use')));
		$content = $data['Page']['content'];
		$this->set('content', $content);
	}

	public function product() {
		$pages = $this->Page->find('first', array('conditions' => array('Page.id' => '43')));
		if (!empty($pages)) {
			$this->set('title_for_layout', $pages['Page']['meta_title']);
			$this->set('description_for_layout', $pages['Page']['meta_keywords']);
			$this->set('keywords_for_layout', $pages['Page']['meta_description']);
		}
		//	$this->set('title_for_layout', __('Early Adopters Programme', true));
		$data = $this->Page->find('first', array('conditions' => array('Page.slug' => 'early-adopters-programme')));
		$content = $data['Page']['content'];
		$this->set('content', $content);

		if (isset($this->data) && !empty($this->data)) {
			if (isset($this->data['Page']['email']) && !empty($this->data['Page']['email'])) {
				$Custemail = $this->data['Page']['email'];
			}

			if (isset($this->data['Page']['subject']) && !empty($this->data['Page']['subject'])) {
				$subject = $this->data['Page']['subject'];
			} else {

				$subject = "Enquiry";

			}

			if (isset($this->data['Page']['fname']) && !empty($this->data['Page']['fname'])) {
				$CustnameF = $this->data['Page']['fname'];
			}

			if (isset($this->data['Page']['lname']) && !empty($this->data['Page']['lname'])) {
				$CustnameL = $this->data['Page']['lname'];
			}

			if (isset($this->data['Page']['organisation']) && !empty($this->data['Page']['organisation'])) {
				$organisation = $this->data['Page']['organisation'];
			}

			if (isset($this->data['Page']['job']) && !empty($this->data['Page']['job'])) {
				$organisation = $this->data['Page']['job'];
			}

			//pr($this->data['Page']); die;
			// $from_email = 'info@ideascast.com';
			$from_email = ADMIN_FROM_EMAIL;
			$from_name = 'IdeasCast';
			//$to = ADMIN_EMAIL;

			$to = 'bal.mattu@ideascast.com';

			//$to = 'pawandotsquares@gmail.com';

			$Email = new CakeEmail();
			$Email->config('Smtp');
			$Email->helpers(array('Html', 'Text'));
			$Email->viewVars(array('formdata' => $this->data['Page']));
			$Email->template('early_enquiry')
				->emailFormat('html')
				->to($to)
				->from(array($from_email => $from_name))
				->subject($subject)
				->send();
			$this->Session->setFlash('Thank you we will contact you shortly.', 'success');

			// Thanks you mail send to customer
			$to = $Custemail;
			$Email = new CakeEmail();
			$Email->config('Smtp');
			$Email->helpers(array('Html', 'Text'));
			$Email->viewVars(array('Custname' => $CustnameF . " " . $CustnameL));
			$Email->template('early_customer')
				->emailFormat('html')
				->to($to)
				->from(array($from_email => $from_name))
				->subject('Thank you for your interest in IdeasCast.')
				->send();

			return $this->redirect(array('action' => 'software'));
		}
	}

	public function old_contactus() {
		$pages = $this->Page->find('first', array('conditions' => array('Page.id' => '44')));
		if (!empty($pages)) {
			$this->set('title_for_layout', $pages['Page']['meta_title']);
			$this->set('description_for_layout', $pages['Page']['meta_keywords']);
			$this->set('keywords_for_layout', $pages['Page']['meta_description']);
		}

		//$this->set('title_for_layout', __('Contact Us', true));
		$data = $this->Page->find('first', array('conditions' => array('Page.slug' => 'contact')));
		$content = $data['Page']['content'];
		$this->set('content', $content);

		if (isset($this->data) && !empty($this->data)) {
			if (isset($this->data['Page']['email']) && !empty($this->data['Page']['email'])) {
				$Custemail = $this->data['Page']['email'];
			}

			if (isset($this->data['Page']['subject']) && !empty($this->data['Page']['subject'])) {
				$subject = $this->data['Page']['subject'];
			}

			if (isset($this->data['Page']['full_name']) && !empty($this->data['Page']['full_name'])) {
				$Custname = $this->data['Page']['full_name'];
			}

			//pr($this->data['Page']); die;
			// $from_email = 'info@ideascast.com';
			$from_email = ADMIN_FROM_EMAIL;
			$from_name = 'IdeasCast';
			//$to = ADMIN_EMAIL;

			$to = 'info@ideascast.com';

			//$to = 'pawandotsquares@gmail.com';

			$Email = new CakeEmail();
			$Email->config('Smtp');
			$Email->helpers(array('Html', 'Text'));
			$Email->viewVars(array('formdata' => $this->data['Page']));
			$Email->template('contactus')
				->emailFormat('html')
				->to($to)
				->from(array($from_email => $from_name))
				->subject($subject)
				->send();
			$this->Session->setFlash('Thank you for getting in touch. We will endeavour to respond as fast as possible.', 'success');

			// Thanks you mail send to customer
			$to = $Custemail;
			$Email = new CakeEmail();
			$Email->config('Smtp');
			$Email->helpers(array('Html', 'Text'));
			$Email->viewVars(array('Custname' => $Custname));
			$Email->template('contactus_customer')
				->emailFormat('html')
				->to($to)
				->from(array($from_email => $from_name))
				->subject('Thank you for your interest in IdeasCast.')
				->send();

			return $this->redirect(array('action' => 'contactus'));
		}
	}

	public function contactus() {
		$this->layout = 'contact';
		$pages = $this->Page->find('first', array('conditions' => array('Page.id' => '44')));
		if (!empty($pages)) {
			$this->set('title_for_layout', $pages['Page']['meta_title']);
			$this->set('description_for_layout', $pages['Page']['meta_keywords']);
			$this->set('keywords_for_layout', $pages['Page']['meta_description']);
		}

		//$this->set('title_for_layout', __('Contact Us', true));
		$data = $this->Page->find('first', array('conditions' => array('Page.slug' => 'contact')));
		$content = $data['Page']['content'];
		$this->set('content', $content);

		$industry = ClassRegistry::init('Industry');
		$industryList = $industry->find('list', array('conditions' => array('Industry.status' => 1)));
		$this->set('industryList', $industryList);

		if (isset($this->data) && !empty($this->data)) {

			if (!empty($this->request->data['g-recaptcha-response'])) {
				$data = array('secret' => '6Ldd4E4UAAAAAFiJRlkCMFjz11k3CiO7Gz_tZq3a', 'response' => $this->request->data["g-recaptcha-response"]);
				$url = 'https://www.google.com/recaptcha/api/siteverify?secret=6Ldd4E4UAAAAAFiJRlkCMFjz11k3CiO7Gz_tZq3a&response=' . $this->request->data["g-recaptcha-response"];

				$verify = file_get_contents($url);

				$captcha_success = json_decode($verify);

				if ($captcha_success->success == true) {

					//$this->data = Sanitize::stripScripts($this->data, array('encode' => FALSE));

					if (isset($this->data['Page']['email']) && !empty($this->data['Page']['email'])) {
						$Custemail = $this->data['Page']['email'];
					}

					$subject = 'Contact Us Enquiry';

					if (isset($this->data['Page']['first_name']) && !empty($this->data['Page']['first_name']) && isset($this->data['Page']['last_name']) && !empty($this->data['Page']['last_name'])) {
						$Custname = $this->data['Page']['first_name'] . " " . $this->data['Page']['last_name'];
					}

					// $from_email = ADMIN_EMAIL;
					$from_email = ADMIN_FROM_EMAIL;
					$from_name = 'IdeasCast';
					$to = ADMIN_EMAIL;
					// $to = "gauravdotsquaress@outlook.com";
					//$to = "pawansharma13@gmail.com";

					$Email = new CakeEmail();
					$Email->config('Smtp');
					$Email->helpers(array('Html', 'Text'));
					$Email->viewVars(array('formdata' => $this->data['Page']));
					$Email->template('contactus')
						->emailFormat('html')
						->to($to)
						->from(array($from_email => $from_name))
						->subject($subject)
						->send();
					$this->Session->setFlash('Thank you for getting in touch. We will endeavour to respond as fast as possible.', 'success');

					// Thanks you mail send to customer
					$to = $Custemail;
					$Email = new CakeEmail();
					$Email->config('Smtp');
					$Email->helpers(array('Html', 'Text'));
					$Email->viewVars(array('Custname' => $Custname));
					$Email->template('contactus_customer')
						->emailFormat('html')
						->to($to)
						->from(array($from_email => $from_name))
						->subject('Thank you for your interest in IdeasCast.')
						->send();

					return $this->redirect(array('action' => 'contactus'));
				} else {
					if ($captcha_success->{'error-codes'}[0] == 'timeout-or-duplicate') {
						$this->Session->setFlash('Captcha Timeout error, Please validate captcha again.', 'error');
					}
				}

			} else {
				$this->Session->setFlash('Please validate captcha.', 'error');

			}

		}
	}

	public function admin_view($id = null) {

		$this->Page->id = $id;

		if (!$this->Page->exists()) {

			throw new NotFoundException(__('Invalid page'));

		}

		$this->set('page', $this->Page->read(null, $id));

	}

	function admin_page_resetfilter() {
		$this->Session->write('page.keyword', '');
		$this->Session->write('page.status', '');
		$this->redirect(array('action' => 'index'));
	}

	public function admin_page_updatestatus() {
		if ($this->request->is('ajax')) {
			$this->autoRender = false;
			$this->loadModel('Page');
			$this->request->data['Page'] = $this->request->data;

			if ($this->Page->save($this->request->data)) {
				$this->Session->setFlash(__('Page status has been updated successfully.'), 'success');
				die('success');
			} else {
				$this->Session->setFlash(__('Page status could not updated successfully.'), 'error');
			}
		}
		die('error');
	}

	public function admin_delete($id = null) {
		if (isset($this->data['id']) && !empty($this->data['id'])) {
			$id = $this->data['id'];
			$this->Page->id = $id;
			if (!$this->Page->exists()) {
				throw new NotFoundException(__('Invalid Page'), 'error');
			}

			if ($this->Page->delete()) {
				$this->Session->setFlash(__('Page has been deleted successfully.'), 'success');
				die('success');
			} else {
				$this->Session->setFlash(__('Page could not deleted successfully.'), 'error');
			}
		}
		die('error');
	}

	public function admin_update_status() {

		if ($this->request->is('ajax')) {

			$this->autoRender = false;

			$this->request->data['Page']['id'] = $this->data['id'];

			$this->request->data['Page']['status'] = $this->params['data']['status'];

			if ($this->Page->save($this->request->data)) {

				return true;

			} else {

				return false;

			}

		}

	}

	public function setnull() {
		$this->autoRender = false;
		if ($this->Session->delete('Message')) {
			echo 'Success';
		}
	}

	public function contactfordemo() {

		if ($this->request->is('ajax')) {
			$this->autoRender = false;
			$response = '';

			$contactdemodata['demoContact']['name'] = '';
			$contactdemodata['demoContact']['email'] = '';

			if (empty($this->request->data['demoContactName']) && empty($this->request->data['demoContactEmail'])) {
				$response = 'Please enter your contact name and email address.';

			} else if (!isset($this->request->data['demoContactName']) || empty($this->request->data['demoContactName'])) {

				$response = 'Please enter your contact name.';

			} else if (!isset($this->request->data['demoContactEmail']) || empty($this->request->data['demoContactEmail'])) {

				$response = 'Please enter your contact email address.';

			} else {
				$contactdemodata['demoContact']['name'] = $this->request->data['demoContactName'];
				$contactdemodata['demoContact']['email'] = $this->request->data['demoContactEmail'];

				/*==== Start Mail to Admin ============================================*/

				// $from_email = ADMIN_EMAIL;
				$from_email = ADMIN_FROM_EMAIL;
				$from_name = 'IdeasCast';
				$subject = 'Enquiry request for Demo';
				$to = ADMIN_EMAIL;

				//$to = 'info@ideascast.com';

				$Email = new CakeEmail();
				$Email->config('Smtp');
				$Email->helpers(array('Html', 'Text'));
				$Email->viewVars(array('formdata' => $contactdemodata['demoContact']));
				$Email->template('contactfordemo')
					->emailFormat('html')
					->to($to)
					->from(array($from_email => $from_name))
					->subject($subject)
					->send();

				//$this->Session->setFlash('Thank you for getting in touch. We will endeavour to respond as fast as possible.','success');

				/*==== End Mail to Admin ==============================================*/

				$response = 'Thank you for getting in touch. We will endeavour to respond as fast as possible.';
			}
			return $response;
		}
		die('error');
	}

	public function downloads_doc($filename = null) {
		$this->autoRender = false;

		if (!empty($filename)) {

			$path = WWW_ROOT . 'images/2017/';
			$download_file = $path . $filename;

			if (file_exists($download_file)) {
				// Getting file extension.
				$extension = explode('.', $filename);
				$extension = $extension[count($extension) - 1];
				// For Gecko browsers
				header('Content-Transfer-Encoding: binary');
				header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($path)) . ' GMT');
				// Supports for download resume
				header('Accept-Ranges: bytes');
				// Calculate File size
				header('Content-Length: ' . filesize($download_file));
				header('Content-Encoding: none');
				// Change the mime type if the file is not PDF
				header('Content-Type: application/' . $extension);
				// Make the browser display the Save As dialog
				header('Content-Disposition: attachment; filename=' . $filename);
				readfile($download_file);
				exit;
			} else {
				echo 'File does not exists on given path';
			}
		}
	}
	//48
	public function empoweringteamwork() {

		$pages = $this->Page->find('first', array('conditions' => array('Page.id' => '48')));

		$this->set('title_for_layout', 'OpusView- Teamworking');
		if (!empty($pages)) {
			$this->set('title_for_layout', 'OpusView- Teamworking');
			$this->set('description_for_layout', $pages['Page']['meta_keywords']);
			$this->set('keywords_for_layout', $pages['Page']['meta_description']);
		}

	}
	//52
	public function features() {

		$pages = $this->Page->find('first', array('conditions' => array('Page.id' => '52')));

		$this->set('title_for_layout', 'OpusView Software – Project Collaboration');
		if (!empty($pages)) {
			$this->set('title_for_layout', 'OpusView Software – Project Collaboration');
			$this->set('description_for_layout', $pages['Page']['meta_keywords']);
			$this->set('keywords_for_layout', $pages['Page']['meta_description']);
		}

	}
	//47
	public function jeera_offer() {

		$pages = $this->Page->find('first', array('conditions' => array('Page.id' => '47')));
		$this->set('title_for_layout', 'OpusView Software Offer');
		if (!empty($pages)) {
			$this->set('title_for_layout', 'OpusView Software Offer');
			$this->set('description_for_layout', $pages['Page']['meta_keywords']);
			$this->set('keywords_for_layout', $pages['Page']['meta_description']);
		}

		if (isset($this->data) && !empty($this->data)) {
			$response = false;
			$your_name = '';
			$your_email = '';
			$company_name = '';

			if (isset($this->data['Page']['your_name']) && !empty($this->data['Page']['your_name'])) {
				$your_name = $this->data['Page']['your_name'];
			}
			if (isset($this->data['Page']['your_email']) && !empty($this->data['Page']['your_email'])) {
				$your_email = $this->data['Page']['your_email'];
			}
			if (isset($this->data['Page']['company_name']) && !empty($this->data['Page']['company_name'])) {
				$company_name = $this->data['Page']['company_name'];
			}

			if ($response == false) {
				//Mail to administrator
				$from_email = $your_email;
				$from_name = 'IdeasCast';
				$to = ADMIN_EMAIL;
				//$to = 'pawansharma13@gmail.com';

				$Email = new CakeEmail();
				$Email->config('Smtp');
				$Email->helpers(array('Html', 'Text'));
				$Email->viewVars(array('formdata' => $this->data['Page']));
				$Email->template('jeera_offer_enquiry')
					->emailFormat('html')
					->to($to)
					->from(array($from_email => $from_name))
					->subject('OpusView Offer')
					->send();
				$this->Session->setFlash('Thank you we will contact you shortly.', 'success');

				//Thanks you mail send to customer
				$Emails = new CakeEmail();
				$Emails->config('Smtp');
				$toc = $your_email;
				$Emails->helpers(array('Html', 'Text'));
				$Emails->viewVars(array('Custname' => $your_name));
				$Emails->template('jeera_offer_customer')
					->emailFormat('html')
					->to($toc)
					->from(array(ADMIN_EMAIL => $from_name))
					->subject('IdeasCast team pack offer')
					->send();

				unset($this->data);
				$this->data = '';

			}

		}

	}
	//53
	public function jeera_demo() {

		$pages = $this->Page->find('first', array('conditions' => array('Page.id' => '53')));

		$this->set('title_for_layout', 'OpusView Software Offer');
		if (!empty($pages)) {
			$this->set('title_for_layout', 'OpusView Software Offer');
			$this->set('description_for_layout', $pages['Page']['meta_keywords']);
			$this->set('keywords_for_layout', $pages['Page']['meta_description']);
		}

		$industry = ClassRegistry::init('Industry');
		$industryList = $industry->find('list', array('conditions' => array('Industry.status' => 1)));
		$this->set('industryList', $industryList);

		if (isset($this->data) && !empty($this->data)) {

			$response = false;
			$your_name = '';
			$your_email = '';
			$company_name = '';

			if (isset($this->request->data['g-recaptcha-response']) && !empty($this->request->data['g-recaptcha-response'])) {
				$data = array('secret' => '6Ldd4E4UAAAAAFiJRlkCMFjz11k3CiO7Gz_tZq3a', 'response' => $this->request->data["g-recaptcha-response"]);
				$url = 'https://www.google.com/recaptcha/api/siteverify?secret=6Ldd4E4UAAAAAFiJRlkCMFjz11k3CiO7Gz_tZq3a&response=' . $this->request->data["g-recaptcha-response"];

				$verify = file_get_contents($url);

				$captcha_success = json_decode($verify);

				if ($captcha_success->success == true) {

					//pr($this->data);

					//$this->data = Sanitize::stripScripts($this->data);

					//pr($this->data); die;

					if (isset($this->data['Page']['first_name']) && !empty($this->data['Page']['first_name']) && isset($this->data['Page']['last_name']) && !empty($this->data['Page']['last_name'])) {
						$your_name = $this->data['Page']['first_name'] . " " . $this->data['Page']['last_name'];
					}

					if (isset($this->data['Page']['your_email']) && !empty($this->data['Page']['your_email'])) {
						$your_email = $this->data['Page']['your_email'];
					}
					if (isset($this->data['Page']['company_name']) && !empty($this->data['Page']['company_name'])) {
						$company_name = $this->data['Page']['company_name'];
					}

					if ($response == false) {

						//Mail to administrator
						//$from_email = $your_email;
						$from_email = ADMIN_FROM_EMAIL;
						$from_name = 'IdeasCast';
						$to = ADMIN_EMAIL;

						//$to = 'gauravdotsquaress@outlook.com';
						//$to = 'pawansharma13@gmail.com';

						$Email1 = new CakeEmail();
						$Email1->config('Smtp');
						$Email1->helpers(array('Html', 'Text'));
						$Email1->viewVars(array('formdata' => $this->data['Page']));
						$Email1->template('jeera_demo_enquiry');
						//$Email->template('jeera_offer_enquiry')
						$Email1->emailFormat('html');
						$Email1->to($to);
						$Email1->from(array($from_email => $from_name));
						$Email1->subject('OpusView Trial Request');
						$Email1->send();
						$this->Session->setFlash('Thank you we will contact you shortly.', 'success');

						//Thanks you mail send to customer
						$Emails = new CakeEmail();
						$Emails->config('Smtp');
						$toc = $your_email;
						$Emails->helpers(array('Html', 'Text'));
						$Emails->viewVars(array('Custname' => $your_name));
						//$Emails->template('jeera_offer_customer')
						$Emails->template('jeera_demo_customer')
							->emailFormat('html')
							->to($toc)
							->from(array(ADMIN_FROM_EMAIL => $from_name))
							->subject('OpusView Trial Request')
							->send();

						unset($this->data);
						$this->data = '';

					}

				} else {
					if ($captcha_success->{'error-codes'}[0] == 'timeout-or-duplicate') {
						$this->Session->setFlash('Captcha Timeout error, Please validate captcha again.', 'error');
					}
				}

			} else {
				$this->Session->setFlash('Please validate captcha.', 'error');

			}
		}
	}
	//50
	public function request_demo() {

		$pages = $this->Page->find('first', array('conditions' => array('Page.id' => '50')));

		$this->set('title_for_layout', 'OpusView Request Demo');
		if (!empty($pages)) {
			$this->set('title_for_layout', 'OpusView Request Demo');
			$this->set('description_for_layout', $pages['Page']['meta_keywords']);
			$this->set('keywords_for_layout', $pages['Page']['meta_description']);
		}

		$industry = ClassRegistry::init('Industry');
		$industryList = $industry->find('list', array('conditions' => array('Industry.status' => 1)));
		$this->set('industryList', $industryList);

		if (isset($this->data) && !empty($this->data)) {

			$response = false;
			$your_name = '';
			$your_email = '';
			$company_name = '';

			if (isset($this->request->data['g-recaptcha-response']) && !empty($this->request->data['g-recaptcha-response'])) {
				$data = array('secret' => '6Ldd4E4UAAAAAFiJRlkCMFjz11k3CiO7Gz_tZq3a', 'response' => $this->request->data["g-recaptcha-response"]);
				$url = 'https://www.google.com/recaptcha/api/siteverify?secret=6Ldd4E4UAAAAAFiJRlkCMFjz11k3CiO7Gz_tZq3a&response=' . $this->request->data["g-recaptcha-response"];

				$verify = file_get_contents($url);

				$captcha_success = json_decode($verify);

				if ($captcha_success->success == true) {

					if (isset($this->data['Page']['first_name']) && !empty($this->data['Page']['first_name']) && isset($this->data['Page']['last_name']) && !empty($this->data['Page']['last_name'])) {
						$your_name = $this->data['Page']['first_name'] . " " . $this->data['Page']['last_name'];
					}

					if (isset($this->data['Page']['email']) && !empty($this->data['Page']['email'])) {
						$your_email = $this->data['Page']['email'];
					}
					if (isset($this->data['Page']['company_name']) && !empty($this->data['Page']['company_name'])) {
						$company_name = $this->data['Page']['company_name'];
					}

					if ($response == false) {

						//Mail to administrator
						//$from_email = $your_email;
						$from_email = ADMIN_FROM_EMAIL;
						$from_name = 'IdeasCast';
						$to = ADMIN_EMAIL;
						//$to = 'pawansharma13@gmail.com';
						//$to = 'gauravdotsquaress@outlook.com';

						$Email1 = new CakeEmail();
						$Email1->config('Smtp');
						$Email1->helpers(array('Html', 'Text'));
						$Email1->viewVars(array('formdata' => $this->data['Page']));
						$Email1->template('request_demo');
						$Email1->emailFormat('html');
						$Email1->to($to);
						$Email1->from(array($from_email => $from_name));
						$Email1->subject('Demo Request');
						$Email1->send();
						//$this->Session->setFlash('Thank you we will contact you shortly.', 'success');
						unset($this->data);
						$this->data = '';
						$this->redirect('/request-demo/thanks');

					}

				} else {
					if ($captcha_success->{'error-codes'}[0] == 'timeout-or-duplicate') {
						$this->Session->setFlash('Captcha Timeout error, Please validate captcha again.', 'error');
					}
				}

			} else {
				$this->Session->setFlash('Please validate captcha.', 'error');

			}
		}
	}
	//51
	public function how_buy() {

		$pages = $this->Page->find('first', array('conditions' => array('Page.id' => '51')));

		$this->set('title_for_layout', 'OpusView How to Buy');
		if (!empty($pages)) {
			$this->set('title_for_layout', 'OpusView How to Buy');
			$this->set('description_for_layout', $pages['Page']['meta_keywords']);
			$this->set('keywords_for_layout', $pages['Page']['meta_description']);
		}

		$industry = ClassRegistry::init('Industry');
		$industryList = $industry->find('list', array('conditions' => array('Industry.status' => 1)));
		$this->set('industryList', $industryList);

		if (isset($this->data) && !empty($this->data)) {

			$response = false;
			$your_name = '';
			$your_email = '';
			$company_name = '';

			if (isset($this->request->data['g-recaptcha-response']) && !empty($this->request->data['g-recaptcha-response'])) {
				$data = array('secret' => '6Ldd4E4UAAAAAFiJRlkCMFjz11k3CiO7Gz_tZq3a', 'response' => $this->request->data["g-recaptcha-response"]);
				$url = 'https://www.google.com/recaptcha/api/siteverify?secret=6Ldd4E4UAAAAAFiJRlkCMFjz11k3CiO7Gz_tZq3a&response=' . $this->request->data["g-recaptcha-response"];

				$verify = file_get_contents($url);

				$captcha_success = json_decode($verify);

				if ($captcha_success->success == true) {

					if (isset($this->data['Page']['first_name']) && !empty($this->data['Page']['first_name']) && isset($this->data['Page']['last_name']) && !empty($this->data['Page']['last_name'])) {
						$your_name = $this->data['Page']['first_name'] . " " . $this->data['Page']['last_name'];
					}

					if (isset($this->data['Page']['email']) && !empty($this->data['Page']['email'])) {
						$your_email = $this->data['Page']['email'];
					}
					if (isset($this->data['Page']['company_name']) && !empty($this->data['Page']['company_name'])) {
						$company_name = $this->data['Page']['company_name'];
					}

					if ($response == false) {

						//Mail to administrator
						//$from_email = $your_email;
						$from_email = ADMIN_FROM_EMAIL;
						$from_name = 'IdeasCast';
						$to = ADMIN_EMAIL;
						//$to = 'pawansharma13@gmail.com';
						//$to = 'gauravdotsquaress@outlook.com';

						$Email1 = new CakeEmail();
						$Email1->config('Smtp');
						$Email1->helpers(array('Html', 'Text'));
						$Email1->viewVars(array('formdata' => $this->data['Page']));
						$Email1->template('how_buy');
						$Email1->emailFormat('html');
						$Email1->to($to);
						$Email1->from(array($from_email => $from_name));
						$Email1->subject('How to Buy');
						$Email1->send();
						unset($this->data);
						$this->data = '';
						$this->redirect('/how-buy/thanks');

					}

				} else {
					if ($captcha_success->{'error-codes'}[0] == 'timeout-or-duplicate') {
						$this->Session->setFlash('Captcha Timeout error, Please validate captcha again.', 'error');
					}
				}

			} else {
				$this->Session->setFlash('Please validate captcha.', 'error');

			}
		}
	}

	public function sethome() {
		$this->autoRender = false;

		if (isset($this->request->data)) {
			if (isset($this->request->data['iCastAgree']) && $this->request->data['iCastAgree'] == 1) {
				//	setcookie("ideascvalid", 1, time() + 60 * 60 * 24, '/ideascomposer_new');
				$domain = ($_SERVER['HTTP_HOST'] != 'localhost') ? $_SERVER['HTTP_HOST'] : false;
				setcookie('ideascvalid', 1, time() + 60 * 60 * 24, '/', $domain, false);
				return true;
			}
		}
		return false;
	}

	//54
	public function partners() {

		$pages = $this->Page->find('first', array('conditions' => array('Page.id' => '54')));

		$this->set('title_for_layout', 'OpusView Software – Partners');
		if (!empty($pages)) {
			$this->set('title_for_layout', 'OpusView Software – Partners');
			$this->set('description_for_layout', $pages['Page']['meta_keywords']);
			$this->set('keywords_for_layout', $pages['Page']['meta_description']);
		}

	}

	//55
	public function templates() {

		$pages = $this->Page->find('first', array('conditions' => array('Page.id' => '55')));

		$this->set('title_for_layout', 'OpusView Templates');
		if (!empty($pages)) {
			$this->set('title_for_layout', 'OpusView Templates');
			$this->set('description_for_layout', $pages['Page']['meta_keywords']);
			$this->set('keywords_for_layout', $pages['Page']['meta_description']);
		}

	}

	public function treeview() {
		$this->layout = 'inner';
		$pages = $this->Page->find('first', array('conditions' => array('Page.id' => '52')));

		$this->set('title_for_layout', 'OpusView Software – Project Collaboration');
		if (!empty($pages)) {
			$this->set('title_for_layout', 'OpusView Software – Project Collaboration');
			$this->set('description_for_layout', $pages['Page']['meta_keywords']);
			$this->set('keywords_for_layout', $pages['Page']['meta_description']);
		}

	}

	public function flarejson_old() {
		$this->loadModel('Project');
		$this->loadModel('Element');
		$this->loadModel('Workspace');
		$this->loadModel('UserProject');

		$json = '{
			   "name":"ROOT",
			   "children":[';
		$pindex = $windex = $aindex = $eindex = 0;
		$project_data = myprojects($this->Session->read('Auth.User.id'));
		
		if (isset($project_data) && !empty($project_data)) {

			foreach ($project_data as $key => $value) {
				$windex = 0;
				$json .= '{"name": "' . strip_tags($value) . '","Members":"8"';

				$project_workspaces = null;
				$project_workspaces = get_project_workspace($key);
				if (isset($project_workspaces) && !empty($project_workspaces)) {
					$json .= ',"children": [';
					foreach ($project_workspaces as $wkey => $wvalue) {
						$aindex = 0;
						$wsp = $wvalue['Workspace'];
						$json .= '{"name": "' . strip_tags($wsp['title']) . '","Members":"9"';

						$workspace_areas = get_workspace_areas($wsp['id']);

						if (isset($workspace_areas) && !empty($workspace_areas)) {
							$json .= ',"children": [';
							foreach ($workspace_areas as $akey => $avalue) {
								$eindex = 0;
								$json .= '{"name": "' . strip_tags($avalue) . '"';
								$area_element = area_element($akey);
								if (isset($area_element) && !empty($area_element)) {
									$json .= ',"children": [';
									foreach ($area_element as $ekey => $evalue) {
										$ele = $evalue['Element'];
										$json .= '{"name": "' . strip_tags($ele['title']) . '"';
										$json .= '}';
										if ($eindex != count($area_element) - 1) {
											$json .= ',';
										}
										$eindex++;
									}
									$json .= ']';
								}
								$json .= '}';
								if ($aindex != count($workspace_areas) - 1) {
									$json .= ',';
								}
								$aindex++;
							}
							$json .= ']';
						}

						$json .= '}';
						if ($windex != count($project_workspaces) - 1) {
							$json .= ',';
						}
						$windex++;
					}
					$json .= ']';
				}
				$json .= '}';
				if ($pindex != count($project_data) - 1) {
					$json .= ',';
				}
				$pindex++;
			}

		}
		$json .= ']}';
		echo ($json);
		exit;

	}
	
	public function flarejson() {
		$this->loadModel('Project');
		$this->loadModel('Element');
		$this->loadModel('Workspace');
		$this->loadModel('UserProject');

		$json = '{
			   "name":"ROOT",
			   "children":[';
		$pindex = $windex = $aindex = $eindex = 0;
		$project_data = myprojects($this->Session->read('Auth.User.id'));
		
		if (isset($project_data) && !empty($project_data)) {

			foreach ($project_data as $project_id => $value) {
				$windex = 0;
				$json .= '{"name": "' . strip_tags($value) . '","Members":"8"';
				 
				$project_workspaces = null;
				$project_workspaces = get_project_workspace($project_id);
				if (isset($project_workspaces) && !empty($project_workspaces)) {					
					
					$json .= ',"children": [
									{  
									"name":"Workspace",
									"children":[';
					
					foreach ($project_workspaces as $wkey => $wvalue) {
						$aindex = 0;
						$wsp = $wvalue['Workspace'];
						$json .= '{"name": "' . strip_tags($wsp['title']) . '","Members":"9"';

						$workspace_areas = get_workspace_areas($wsp['id']);

						if (isset($workspace_areas) && !empty($workspace_areas)) {
							$json .= ',"children": [';
							$json .= ' {  
										"name":"Area",
										"children":[';
							
							foreach ($workspace_areas as $akey => $avalue) {
								$eindex = 0;
								$json .= '{"name": "' . strip_tags($avalue) . '"';
								$area_element = area_element($akey);
								if (isset($area_element) && !empty($area_element)) {
									$json .= ',"children": [';
									foreach ($area_element as $ekey => $evalue) {
										$ele = $evalue['Element'];
										$json .= '{"name": "' . strip_tags($ele['title']) . '"';
										$json .= '}';
										if ($eindex != count($area_element) - 1) {
											$json .= ',';
										}
										$eindex++;
									}
									$json .= ']';
								}
								$json .= '}';
								if ($aindex != count($workspace_areas) - 1) {
									$json .= ',';
								}
								$aindex++;
							}
							$json .= ']},';
							
							//============ here will come wsp members ===============
							$participants = $participants_owners = $participantsGpOwner = $participantsGpSharer = [];
								$projectwsp_id = isset($wsp['id']) ? $wsp['id'] : 0;
								$owner = $this->Common->ProjectOwner( $project_id, $this->Session->read('Auth.User.id') ); 
								
								$owner_id = isset($owner) ? $owner['UserProject']['user_id'] : 0;
								$participants = wsp_participants( $project_id,$projectwsp_id, $owner['UserProject']['user_id']);
								$participants_owners = array_filter(participants_owners( $project_id, $owner['UserProject']['user_id'] ));
								$i = 0;
								foreach($participants_owners as $nom){
									 if($owner_id != $nom &&  $nom !=''){		
										$i++;
									 }
								}
								
								$participantsGpOwner = participants_group_owner( $project_id );
								$participantsGpSharer = wsp_grps_sharer( $project_id ,$wsp['id']);	
								$participants = isset($participants) ? array_filter($participants) : $participants;
								$participants_owners = isset($participants_owners) ? array_filter($participants_owners) : $participants_owners;
								$participantsGpOwner = isset($participantsGpOwner) ? array_filter($participantsGpOwner) : $participantsGpOwner;
								$participantsGpSharer = isset($participantsGpSharer) ? array_filter($participantsGpSharer) : $participantsGpSharer;

								$all = [];
								if( isset($participants) && !empty($participants) ) {
									$all = @array_merge($all, $participants);	
								}
								if( isset($participants_owners) && !empty($participants_owners) ) {
									$all = @array_merge($all, $participants_owners);	
								}
								if( isset($participantsGpOwner) && !empty($participantsGpOwner) ) {
									$all = @array_merge($all, $participantsGpOwner);	
								}
								if( isset($participantsGpSharer) && !empty($participantsGpSharer) ) {
									$all = @array_merge($all, $participantsGpSharer);	
								}
							
							$json .= '{ "name":"Workspace Members '.count($all).'",
									"children":[ ';
								
								if( isset($all) && !empty($all) ){
									
									foreach($all as $wsp_user_id ){
										
										$userdata = get_user_data($wsp_user_id);
								
										$profile_pic = $userdata['UserDetail']['profile_pic'];
										if(!empty($profile_pic) && file_exists(USER_PIC_PATH.$profile_pic)){
											$profilesPic = SITEURL.USER_PIC_PATH.$profile_pic;
										} else {
											$profilesPic = SITEURL.'images/placeholders/user/user_1.png';
										}
										
										$json .= '{ 
											"name":"'.$userdata['UserDetail']['full_name'].'",
											"user_id":"'.$userdata['UserDetail']['user_id'].'",
											"profileurl":"'.$profilesPic.'"
										},';
										  
									}
									
									$json = substr($json, 0, -1);
								}	  
									  
							$json .= ']}';
							
							//==========================================================
							
							$json .= ']';
						}

						$json .= '}';
						if ($windex != count($project_workspaces) - 1) {
							$json .= ',';
						}
						$windex++;
					}
					$json .= ']},';
					
					// will show project People 
									
/*====== People on Projects ==========================================================*/	
$owner = $this->Common->ProjectOwner($project_id,$this->Session->read('Auth.User.id'));
$participants = participants($project_id,$owner['UserProject']['user_id']);
$participants_owners = participants_owners($project_id, $owner['UserProject']['user_id']);
$participantsGpOwner = participants_group_owner($project_id );
$participantsGpSharer = participants_group_sharer($project_id );

$participants = isset($participants) ? array_filter($participants) : $participants;
$participants_owners = isset($participants_owners) ? array_filter($participants_owners) : $participants_owners;
$participantsGpOwner = isset($participantsGpOwner) ? array_filter($participantsGpOwner) : $participantsGpOwner;
$participantsGpSharer = isset($participantsGpSharer) ? array_filter($participantsGpSharer) : $participantsGpSharer;


$total = 0;
$total = count($participants) + count($participants_owners) + count($participantsGpOwner) + count($participantsGpSharer);
$projectUsers = array();

if( count($participants) > 0 ){	 
	foreach( $participants as $user_id ){
		$projectUsers[] = $user_id;
	}	
}

if( count($participantsGpOwner) > 0 ){
	
	foreach( $participantsGpOwner as $user_id ){
		$projectUsers[] = $user_id;
	}
}

if( count($participants_owners) > 0 ){
	
	foreach( $participants_owners as $user_id ){
		$projectUsers[] = $user_id;
	} 
	 	 	
}

if( count($participantsGpSharer) > 0 ){
	
	foreach( $participantsGpSharer as $user_id ){
		$projectUsers[] = $user_id;
	} 
	 	
}	

/* ====================================================================================== */	
					$json .= '{ "name":"Project Members '.$total.'",
									"children":[ ';
					if( isset( $projectUsers ) && !empty($projectUsers) ){
						foreach($projectUsers as $userlists){
							$userdata = get_user_data($userlists);
							
							$profile_pic = $userdata['UserDetail']['profile_pic'];
							if(!empty($profile_pic) && file_exists(USER_PIC_PATH.$profile_pic)){
								$profilesPic = SITEURL.USER_PIC_PATH.$profile_pic;
							} else {
								$profilesPic = SITEURL.'images/placeholders/user/user_1.png';
							}
							
							$json .= '{ 
								"name":"'.$userdata['UserDetail']['full_name'].'",
								"user_id":"'.$userdata['UserDetail']['user_id'].'",
								"profileurl":"'.$profilesPic.'"
							},';				
						}
						$json = substr($json, 0, -1);
					} 			
					$json .= ']}';				
				//================================================================	
					$json .= ']';
				}
				$json .= '}';
				if ($pindex != count($project_data) - 1) {
					$json .= ',';
				}
				$pindex++;
			}

		}
		$json .= ']}';
		echo ($json);
		exit;

	}
	
}