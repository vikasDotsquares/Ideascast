<?php
App::uses('Controller/Component', 'Auth', 'Session', 'RequestHandler');
App::uses('Sanitize', 'Utility');
App::uses('CakeEmail', 'Network/Email');

class ShareElementsController extends AppController {

	public $name = 'ShareElements';
	public $uses = [
		'User',
		'UserDetail',
		'ProjectPermission',
		'Category',
		'UserProject',
		'Project',
		'Workspace',
		'Area',
		'ProjectWorkspace',
		'Element',
		'ElementCost',
		'UserElementCost',
		'ElementCostComment',
		'ElementCostHistory',
		'WorkspaceCostComment',
		'ProjectCostComment',
	];
	public $objView = null;
	public $user_id = null;
	public $components = array(
		'Common', 'PhpExcel',
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

		$this->user_id = $this->Auth->user('id');

		$view = new View();
		$this->objView = $view;
	}

	public function index() {
		$this->layout = 'inner';		 
		
		$this->set('title_for_layout', __('ShareElements', true));
		$this->set('page_heading', __('ShareElements', true));
		$this->set('page_subheading', __('Plan and manage Project costs', true)); 
		
		$viewData['crumb'] = [
			'last' => [
				'data' => [
					'class' => 'tipText',
					'title' => 'Share Elements',
					'data-original-title' => 'ShareElements',
				],
			],
		];

		$this->set($viewData);	
		 
	}	

}
