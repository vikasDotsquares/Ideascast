<?php

/**

 * Static content controller.

 *

 * This file will render views from views/offlines/

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

class OfflinesController extends AppController {

/**

 * Controller name

 *

 * @var string

 */

	public $name = 'Offlines';

/**

 * Default helper

 *

 * @var array

 */

	public $helpers = array('Html', 'Session', 'Js');

	public $components = array('RequestHandler');
	
/**

	 * This controller does not use a model

	 *

	 * @var array

	 *

	 * check login for admin and frontend user

	 * allow and deny user

	 */

	public function beforeFilter() {

		parent::beforeFilter();

		$this->Auth->allow('index');

	}

	/**

	 * Displays a view

	 *

	 * @param mixed What page to display

	 * @return void

	 */

	public function sample() {

	}

	public function index() {
		
		$this->set('title_for_layout', 'OpusView Software – Under Construction');
		
	}


}