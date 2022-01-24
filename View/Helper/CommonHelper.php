<?php

/**
 * Application level View Helper
 *
 * This file is application-wide helper file. You can put all
 * application-wide helper-related methods here.
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Helper
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
App::uses('Helper', 'View');

/**
 * Application helper
 *
 * Add your application-wide methods in the class below, your helpers
 * will inherit them.
 *
 * @package       app.View.Helper
 */
App::import("Model", "Project");
class CommonHelper extends Helper {

	var $helpers = array('Html', 'Session', 'Thumbnail', 'ViewModel');
	protected $_projects;

	public function __construct(View $View, $settings = array()) {

		parent::__construct($View, $settings);

		$this->_projects = new Project();

	}

	public function getProfileImage($image = null) {

		echo $this->Thumbnail->show(
			array(
				'save_path' => ROOT . '/app/webroot/files/thumbs',
				'display_path' => Router::url('/', true) . 'files/thumbs', // or 'display_path' => 'http://images.domain.com',
				'error_image_path' => Router::url('/', true) . 'files/no_image.jpg',
				'src' => ROOT . '/app/webroot/files/profile_images/' . $image,
				'w' => 200,
				'h' => 150,
				'q' => 100,
				'zc' => 1,
			),
			// This is the tag options array for adding any other properties to the image tag
			array('style' => 'border: 5px solid #EEEEEE;')
		);
	}

	public function getCountry($id = null) {
		$country = ClassRegistry::init('Country')->find('first', array('conditions' => array('Country.countryCode' => $id)));
		return isset($country['Country']['countryName']) ? $country['Country']['countryName'] : 'N/A';
	}

	public function getIndustry($id = null) {
		$industry = ClassRegistry::init('Industry')->find('first', array('conditions' => array('Industry.id' => $id)));
		return isset($industry['Industry']['title']) ? $industry['Industry']['title'] : 'N/A';
	}

	public function getState($id = null) {
		$state = ClassRegistry::init('State')->find('first', array('conditions' => array('id' => $id)));
		return isset($state['State']['name']) ? $state['State']['name'] : 'N/A';
	}
	public function getCity($id = null) {
		$city = ClassRegistry::init('City')->find('first', array('conditions' => array('id' => $id)));
		return isset($city['City']['name']) ? $city['City']['name'] : 'N/A';
	}

	public function getRoles($id = null) {
		$roles = ClassRegistry::init('Role')->find('first', array('conditions' => array('Role.id' => $id)));
		return isset($roles['Role']['role']) ? $roles['Role']['role'] : '-';
	}

	public function sett($id = null) {

		$data = ClassRegistry::init('Setting')->find('first', array('conditions' => array('Setting.id' => 1), 'fields' => array("Setting.$id")));
		if (isset($data['Setting']["$id"]) && !empty($data['Setting']["$id"])) {
			return $data['Setting']["$id"];
		} else {
			return "N/A";
		}
	}

	public function getRoleLists() {
		$roles = ClassRegistry::init('Role')->find('list', array('conditions' => array('NOT' => array('id' => array(1))), 'fields' => array('id', 'role')));
		return isset($roles) ? $roles : '-';
	}

	public function getProjectCategorylist() {
		$categories = ClassRegistry::init('ProjectsCategory')->find('list', array('conditions' => array('ProjectsCategory.status' => 1), 'fields' => array('id', 'category')));
		return isset($categories) ? $categories : array();
	}

	public function getVoteCount($vote_question_option_id, $vote_id) {
		//$user_id = $this->Session->read('Auth.User.id');
		//echo $vote_question_option_id.'<- vote_question_option_id<br>vote_id-> '.$vote_id.'<br>user_id->'.$user_id;
		$vote_count = ClassRegistry::init('VoteResult')->find('count', array('conditions' => array('VoteResult.vote_id' => $vote_id, 'VoteResult.vote_question_option_id' => $vote_question_option_id), 'group' => array('VoteResult.user_id')));
		return isset($vote_count) ? $vote_count : 0;
	}

	public function getVoteCountCumulative($vote_question_option_id, $vote_id) {
		//$user_id = $this->Session->read('Auth.User.id');
		//echo $vote_question_option_id.'<- vote_question_option_id<br>vote_id-> '.$vote_id.'<br>user_id->'.$user_id;
		$vote_count = ClassRegistry::init('VoteResult')->find('first', array('conditions' => array('VoteResult.vote_id' => $vote_id, 'VoteResult.vote_question_option_id' => $vote_question_option_id), 'fields' => array('SUM(VoteResult.vote_range) as total')));
		return isset($vote_count['0']['total']) ? (int) $vote_count['0']['total'] : 0;
	}

	public function getVoteCountDistribute($vote_question_option_id, $vote_id) {
		//$user_id = $this->Session->read('Auth.User.id');
		//echo $vote_question_option_id.'<- vote_question_option_id<br>vote_id-> '.$vote_id.'<br>user_id->'.$user_id;
		$vote_count = ClassRegistry::init('VoteResult')->find('first', array('conditions' => array('VoteResult.vote_id' => $vote_id, 'VoteResult.vote_question_option_id' => $vote_question_option_id), 'fields' => array('SUM(VoteResult.vote_range) as total')));
		//pr($vote_count);die;
		return isset($vote_count['0']['total']) ? (int) $vote_count['0']['total'] : 0;
	}

	public function getVoteCountScore($vote_question_option_id, $vote_id) {
		//$user_id = $this->Session->read('Auth.User.id');
		//echo $vote_question_option_id.'<- vote_question_option_id<br>vote_id-> '.$vote_id.'<br>user_id->'.$user_id;
		$vote_count = ClassRegistry::init('VoteResult')->find('count', array('conditions' => array('VoteResult.vote_id' => $vote_id, 'VoteResult.vote_question_option_id' => $vote_question_option_id)));
		return isset($vote_count) ? $vote_count : 0;
	}

	public function getProjectSourcelist() {
		$projects_sources = ClassRegistry::init('ProjectsSource')->find('list', array('conditions' => array('ProjectsSource.status' => 1), 'fields' => array('id', 'source')));
		return isset($projects_sources) ? $projects_sources : array();
	}

	public function getSubjectExpenseTypelist() {
		$SubjectExpenseType = ClassRegistry::init('SubjectExpenseType')->find('list', array('conditions' => array('SubjectExpenseType.status' => 1), 'fields' => array('id', 'expense')));
		return isset($SubjectExpenseType) ? $SubjectExpenseType : array();
	}

	public function getCountryList() {
		$countries = ClassRegistry::init('Country')->find('list', array('fields' => array('countryCode', 'countryName'), 'order' => '(CASE WHEN countryCode="GB" THEN 0 WHEN countryCode="US" THEN 1 ELSE 2 END), countryName ASC'));
		return isset($countries) ? $countries : array();
	}

	public function getStateList($country_code) {
		//echo $country_code;die;
		$states = ClassRegistry::init('State')->find('list', array('conditions' => array('State.country_iso_code' => $country_code), 'fields' => array('id', 'name'), 'order' => 'name asc'));
		return isset($states) ? $states : array();
	}

	public function getCityList() {
		$countries = ClassRegistry::init('City')->find('list', array('fields' => array('id', 'city')));
		return isset($countries) ? $countries : array();
	}

	public function getCompanyStructure($id = '') {
		if (!empty($id)) {
			$structures = ClassRegistry::init('CompanyStructure')->find('first', array('conditions' => array('CompanyStructure.id' => $id), 'fields' => array('id', 'structure')));
			return isset($structures['CompanyStructure']['structure']) ? $structures['CompanyStructure']['structure'] : '-';
		} else {
			$structures = ClassRegistry::init('CompanyStructure')->find('list', array('fields' => array('id', 'structure')));
			return isset($structures) ? $structures : array();
		}
	}

	public function getIndustryClassification($id = '') {
		if (!empty($id)) {
			$classifications = ClassRegistry::init('IndustryClassification')->find('first', array('conditions' => array('IndustryClassification.id' => $id), 'fields' => array('id', 'classification')));
			return isset($classifications['IndustryClassification']['classification']) ? $classifications['IndustryClassification']['classification'] : '-';
		} else {
			$classifications = ClassRegistry::init('IndustryClassification')->find('list', array('fields' => array('id', 'classification')));
			return isset($classifications) ? $classifications : array();
		}
	}

	public function getCompanyList() {
		$companies = ClassRegistry::init('Company')->find('list', array('fields' => array('id', 'company_name')));
		return isset($companies) ? $companies : array();
	}

	public function getLocationsByDivisionList() {
		$locationsbydivisions = ClassRegistry::init('LocationsByDivision')->find('list', array('fields' => array('id', 'division_type')));
		return isset($locationsbydivisions) ? $locationsbydivisions : array();
	}

	public function getCompanyParentList($id = '') {
		if (!empty($id)) {
			$companies = ClassRegistry::init('Company')->find('first', array('conditions' => array('Company.id' => $id), 'fields' => array('id', 'company_name')));
			return isset($companies['Company']['company_name']) ? $companies['Company']['company_name'] : '-';
		} else {
			$companies = ClassRegistry::init('Company')->find('list', array('conditions' => array('Company.parent_id' => 0), 'fields' => array('id', 'company_name')));
			return isset($companies) ? $companies : array();
		}
	}

	public function getProjectlist() {
		$projects = ClassRegistry::init('Project')->find('list', array('conditions' => array('Project.status' => 1, 'Project.company_id' => $this->Session->read('Auth.CompanyUser.company_id')), 'fields' => array('id', 'project_name')));
		return isset($projects) ? $projects : array();
	}

	public function getDataTermslist() {
		$dataterms = ClassRegistry::init('DataTerm')->find('list', array('conditions' => array('DataTerm.status' => 1), 'fields' => array('id', 'data_terms')));
		return isset($dataterms) ? $dataterms : array();
	}

	public function getDataTermsTypelist($data_term_id = '') {
		if (!empty($data_term_id)) {
			$datatermtypes = ClassRegistry::init('DataTermsType')->find('list', array('conditions' => array('DataTermsType.data_term_id' => $data_term_id, 'DataTermsType.status' => 1), 'fields' => array('id', 'data_terms_type')));
		}
		return isset($datatermtypes) ? $datatermtypes : array();
	}

	public function getProjectStatusList() {
		//$projectStatus = ClassRegistry::init('ProjectsStatus')->find('list', array('conditions'=>array('status' => 1),'fields' => array('id', 'title')));
		$projects_status = ClassRegistry::init('ProjectsStatus')->find('list', array('conditions' => array('ProjectsStatus.status' => 1), 'fields' => array('id', 'title')));
		return isset($projects_status) ? $projects_status : array();
	}

	public function getAdminUsers() {
		$users = ClassRegistry::init('User')->find('all', array('conditions' => array('User.status' => 1), 'fields' => array('UserDetail.first_name', 'UserDetail.last_name', 'User.id')));
		$user_list = Set::combine($users, '{n}.User.id', array('{0} {1}', '{n}.UserDetail.first_name', '{n}.UserDetail.last_name'));
		return isset($user_list) ? $user_list : array();
	}

	public function getCompanyUsers() {
		$company_id = $this->Session->read('Auth.CompanyUser.company_id');
		$companyusers = ClassRegistry::init('CompanyUser')->find('all', array('conditions' => array('CompanyUser.status' => 1, 'CompanyUser.company_id' => $company_id), 'fields' => array('CompanyUserProfile.first_name', 'CompanyUserProfile.last_name', 'CompanyUser.id', 'CompanyUser.email')));
		$user_list = Set::combine($companyusers, '{n}.CompanyUser.id', array('{0} {1} --> {2}', '{n}.CompanyUserProfile.first_name', '{n}.CompanyUserProfile.last_name', '{n}.CompanyUser.email'));
		//pr($user_list);die;
		return isset($user_list) ? $user_list : array();
	}

	public function getCompanyFiles() {
		$company_id = $this->Session->read('Auth.CompanyUser.company_id');
		$companyfiles = ClassRegistry::init('Data')->find('list', array('conditions' => array('Data.company_id' => $company_id), 'fields' => array('Data.id', 'Data.file_name')));
		return isset($companyfiles) ? $companyfiles : array();
	}

	public function getProjectAssignedResources($project_id = null) {
		// All user admin and company both
		$company_id = $this->Session->read('Auth.CompanyUser.company_id');
		ClassRegistry::init('CompanyUser')->unbindModel(array('belongsTo' => array('Company')));
		ClassRegistry::init('ProjectResource')->recursive = 2;
		$assignedCompanyUsers = ClassRegistry::init('ProjectResource')->find('all', array('conditions' => array('ProjectResource.project_id' => $project_id, 'ProjectResource.company_id' => $company_id)));

		return isset($assignedCompanyUsers) ? $assignedCompanyUsers : array();
	}

	public function getProjectAssignedFiles($project_id = null) {
		// All user admin and company both
		$company_id = $this->Session->read('Auth.CompanyUser.company_id');
		$assignedfiles = ClassRegistry::init('Data')->find('all', array('conditions' => array('Data.project_id' => $project_id, 'Data.company_id' => $company_id)));

		return isset($assignedfiles) ? $assignedfiles : array();
	}

	public function getDataTermList() {
		$dataTerms = ClassRegistry::init('DataTerm')->find('list', array('fields' => array('id', 'data_terms')));
		return isset($dataTerms) ? $dataTerms : array();
	}

	public function userDetail($id) {
		$data = ClassRegistry::init('UserDetail')->find('first', array('conditions' => array('UserDetail.user_id' => $id), 'recursive' => -1));
		return isset($data) ? $data : array();
	}

	public function userFullname($id) {
		//echo $id."<br>";
		$data = ClassRegistry::init('UserDetail')->find('first', array('conditions' => array('UserDetail.user_id' => $id), 'fields' => array('first_name', 'last_name')));

		//pr($data);

		return (isset($data['UserDetail']) && !empty($data['UserDetail'])) ? htmlentities($data['UserDetail']['first_name']) . " " . htmlentities($data['UserDetail']['last_name']) : "N/A";
	}

	public function userFirstname($id) {
		$data = ClassRegistry::init('UserDetail')->find('first', array('conditions' => array('UserDetail.user_id' => $id), 'fields' => array('first_name' )));
		return (isset($data['UserDetail']) && !empty($data['UserDetail'])) ? $data['UserDetail']['first_name'] : "N/A";
	}

	public function userLastname($id) {
		$data = ClassRegistry::init('UserDetail')->find('first', array('conditions' => array('UserDetail.user_id' => $id), 'fields' => array('last_name' )));
		return (isset($data['UserDetail']) && !empty($data['UserDetail'])) ? $data['UserDetail']['last_name'] : "N/A";
	}


	public function tempRelationUser($id) {
		//echo $id."<br>";
		$data = ClassRegistry::init('TemplateRelation')->find('first', array('conditions' => array('TemplateRelation.id' => $id), 'fields' => array('user_id', 'type', 'thirdparty_id')));

		if ((isset($data['TemplateRelation']['type']) && !empty($data['TemplateRelation']['type'])) && $data['TemplateRelation']['type'] == 2) {

			return "IdeasCast";
		} else if ((isset($data['TemplateRelation']['type']) && !empty($data['TemplateRelation']['type'])) && $data['TemplateRelation']['type'] == 3) {

			$dataT = ClassRegistry::init('ThirdParty')->find('first', array('conditions' => array('ThirdParty.id' => $data['TemplateRelation']['thirdparty_id']), 'fields' => array('id', 'username')));

			return (isset($dataT['ThirdParty']['username']) && !empty($dataT['ThirdParty']['username'])) ? $dataT['ThirdParty']['username'] : "N/A";
		} else {

			return (isset($data['TemplateRelation']) && !empty($data['TemplateRelation'])) ? $this->userFullname($data['TemplateRelation']['user_id']) : "N/A";
		}
	}

	public function alignedName($id) {
		//echo $id."<br>";
		$data = ClassRegistry::init('Aligned')->find('first', array('conditions' => array('Aligned.id' => $id), 'fields' => array('title')));

		//pr($data);

		return (isset($data['Aligned']) && !empty($data['Aligned'])) ? $data['Aligned']['title'] : "N/A";
	}

	public function total_shared($id, $uid) {

		ClassRegistry::init('ProjectPermission')->recursive = 1;
		ClassRegistry::init('Project')->recursive = -1;
		$counter = 0;
		$data = ClassRegistry::init('ProjectPermission')->find('all', array('conditions' => array('ProjectPermission.user_id' => $id, 'ProjectPermission.share_by_id' => $uid, 'UserProject.id IS NOT NULL')));

		// e(ClassRegistry::init('ProjectPermission')->_query() );
		// pr($data, 1);
		if (isset($data) && !empty($data)) {
			foreach ($data as $key => $value) {
				if ($this->_projects->hasAny(['Project.id' => $value['UserProject']['project_id']])) {
					$counter++;
				}

			}
		}
// die;

		return $counter;
	}

	public function total_sharedNW($id, $uid, $parentID) {

		$dataP = ClassRegistry::init('ProjectPermission')->find('count', array('conditions' => array('ProjectPermission.user_id' => $id, 'ProjectPermission.parent_id' => $parentID)));

		$dataPps = ClassRegistry::init('ProjectPermission')->find('first', array('conditions' => array('ProjectPermission.id' => $parentID)));
		//die;

		$data = ClassRegistry::init('ProjectPermission')->find('count', array('conditions' => array('ProjectPermission.user_id' => $id, 'ProjectPermission.owner_id' => $dataPps['ProjectPermission']['user_id'])));

		$data = $data + $dataP;

		return isset($data) ? $data : 0;
	}

	public function PlanName($id) {
		$data = ClassRegistry::init('Plan')->find('first', array('conditions' => array('Plan.id' => $id), 'fields' => array('description')));
		return isset($data) ? $data : array();
	}

	public function couponName($id) {
		$data = ClassRegistry::init('coupon')->find('first', array('conditions' => array('coupon.id' => $id), 'fields' => array('name'))); //pr($data);
		return $data['Coupon']['name'];
	}

	public function currencyConvertor($val) {
		$data = ClassRegistry::init('Currency')->find('first', array('conditions' => array('Currency.status' => 1), 'fields' => array('Currency.value')));

		//pr($data['Currency']['value']); die;
		return $data['Currency']['value'] * $val;
	}

	public function getCurrencySymbol($currency_id = null) {

		if (isset($currency_id) && !empty($currency_id)) {
			$data = ClassRegistry::init('Currency')->find('first', array('conditions' => array('Currency.id' => $currency_id), 'fields' => array('Currency.symbol')));
		}
		return (isset($data['Currency']['symbol']) && !empty($data['Currency']['symbol'])) ? $data['Currency']['symbol'] : '$';
	}

	public function getCurrencySymbolName($project_id = null) {

		if (isset($project_id) && !empty($project_id)) {

			$currency_id = ClassRegistry::init('Project')->find('first', array('conditions' => array('Project.id' => $project_id), 'fields' => array('Project.currency_id')));

			if (isset($currency_id) && !empty($currency_id)) {
				$data = ClassRegistry::init('Currency')->find('first', array('conditions' => array('Currency.id' => $currency_id['Project']['currency_id']), 'fields' => array('Currency.sign')));
			}

		}
		return (isset($data['Currency']['sign']) && !empty($data['Currency']['sign'])) ? $data['Currency']['sign'] : 'GBP';
	}

	public function getCurrencySymbolNameByID($currency_id = null) {

		if (isset($currency_id) && !empty($currency_id)) {

			$data = ClassRegistry::init('Currency')->find('first', array('conditions' => array('Currency.id' => $currency_id), 'fields' => array('Currency.sign')));

		}
		return (isset($data['Currency']['sign']) && !empty($data['Currency']['sign'])) ? $data['Currency']['sign'] : 'GBP';
	}

	public function currencySymbol() {
		$data = ClassRegistry::init('Currency')->find('first', array('conditions' => array('Currency.status' => 1), 'fields' => array('Currency.symbol')));

		//pr($data['Currency']['value']); die;
		return $data['Currency']['symbol'];
	}

	public function currencyData() {
		$data = ClassRegistry::init('Currency')->find('first', array('conditions' => array('Currency.status' => 1), 'fields' => array('Currency.*')));

		//pr($data['Currency']['value']); die;
		return $data;
	}

	public function currencySignGet($data) {
		$data = ClassRegistry::init('Currency')->find('first', array('conditions' => array('Currency.sign' => $data), 'fields' => array('Currency.symbol')));

		//pr($data['Currency']['value']); die;
		return $data['Currency']['symbol'];
	}

	public function getcurrencybyid() {

		/* $data = ClassRegistry::init('Currency')->find('list',
				array('conditions' =>
						array('Currency.id'=> array(12,47,48,62) ),
						'fields' => array('Currency.id','Currency.name')
				)

		); */

		ClassRegistry::init('Currency')->virtualFields = array(
			'full_currency' => "CONCAT(Currency.name,' (',Currency.sign,')' )",
		);
// array(12, 47, 48, 62)
		$data = ClassRegistry::init('Currency')->find('list',
			array('conditions' => array('Currency.status' => 1),
				'fields' => array('Currency.id', 'Currency.full_currency'),
				'order' => 'Currency.name ASC',
			)
		);
		//pr($data); die;
		return $data;
	}

	public function totalData($model) {

		//pr($model); die;
		$total = ClassRegistry::init($model)->find('count');

		return $total;
	}

	public function totalOrgData($model = 'User') {

		ClassRegistry::init('User')->unbindModel(array('hasOne' => array('UserDetail', 'OrganisationUser')));

		$total = ClassRegistry::init($model)->find('count', array('conditions' => array('User.role_id' => 3)));
		return $total;

	}

	public function totalDataP($model) {

		//pr($model); die;
		$total = ClassRegistry::init($model)->find('count');

		return $total;
	}

	public function totalActive($model) {

		$pending = ClassRegistry::init($model)->find('count', array(
			'conditions' => array($model . ".status" => '1'),
		));

		return $pending;
	}

	public function totalInactive($model) {

		$pending = ClassRegistry::init($model)->find('count', array(
			'conditions' => array($model . ".status" => '0'),
		));
		return $pending;
	}

	public function totalDataU($model,$param=null) {

		if(isset($param) && !empty($param)){
			$pending = ClassRegistry::init($model)->find('count', array(
			'conditions' => array($model . ".role_id" => '2','UserDetail.administrator'=>1),
		));
		}else{
			$pending = ClassRegistry::init($model)->find('count', array(
			'conditions' => array($model . ".role_id" => '2'),
		));
		}


		return $pending;
	}

	public function totalActiveU($model) {

		$pending = ClassRegistry::init($model)->find('count', array(
			'conditions' => array($model . ".status" => '1', $model . ".role_id" => '2'),
		));

		return $pending;
	}

	public function totalInactiveU($model) {

		$pending = ClassRegistry::init($model)->find('count', array(
			'conditions' => array($model . ".status" => '0', $model . ".role_id" => '2'),
		));
		return $pending;
	}

	public function totalOrgInactiveU($model) {

		ClassRegistry::init('User')->unbindModel(array('hasOne' => array('UserDetail', 'OrganisationUser')));

		$pending = ClassRegistry::init($model)->find('count', array(
			'conditions' => array($model . ".status" => '0', $model . ".role_id" => '3'),
		));
		return $pending;
	}
	public function totalOrgActiveU($model) {

		ClassRegistry::init('User')->unbindModel(array('hasOne' => array('UserDetail', 'OrganisationUser')));

		$pending = ClassRegistry::init($model)->find('count', array(
			'conditions' => array($model . ".status" => '1', $model . ".role_id" => '3'),
		));

		return $pending;
	}

	public function totalOrders() {
		$joins = array(
			array(
				'table' => 'user_details',
				'alias' => 'UserDetail',
				'type' => 'INNER',
				'conditions' => array(
					'UserTransctionDetail.user_id = UserDetail.user_id',
				),
			),
		);
		$pending = ClassRegistry::init('UserTransctionDetail')->find('count', array(
			'joins' => $joins, 'conditions' => array('UserTransctionDetail.user_id !=' => '', 'UserDetail.user_id !=' => '', 'User.id !=' => ''),
		));
		return $pending;
	}

	public function totalDataThird($model) {

		$pending = ClassRegistry::init($model)->find('count', array(
			'conditions' => array($model . ".role_id" => '3'),
		));
		return $pending;
	}

	public function totalActiveT($model) {

		$pending = ClassRegistry::init($model)->find('count', array(
			'conditions' => array($model . ".status" => '1', $model . ".role_id" => '3'),
		));

		return $pending;
	}

	public function totalInactiveT($model) {

		$pending = ClassRegistry::init($model)->find('count', array(
			'conditions' => array($model . ".status" => '0', $model . ".role_id" => '3'),
		));
		return $pending;
	}

	public function totalDataInst($model) {

		$pending = ClassRegistry::init($model)->find('count', array(
			'conditions' => array($model . ".role_id" => '4'),
		));
		return $pending;
	}

	public function totalActiveI($model) {

		$pending = ClassRegistry::init($model)->find('count', array(
			'conditions' => array($model . ".status" => '1', $model . ".role_id" => '4'),
		));

		return $pending;
	}

	public function totalInactiveI($model) {

		$pending = ClassRegistry::init($model)->find('count', array(
			'conditions' => array($model . ".status" => '0', $model . ".role_id" => '4'),
		));
		return $pending;
	}

	public function elmDoc($id) {
		$data = ClassRegistry::init('ElementDocument')->find('all', array('conditions' => array('ElementDocument.element_id' => $id), 'fields' => array('ElementDocument.*')));
		return isset($data) ? $data : array();
	}

	public function workspaceName($id = NULL) {
		$data = ClassRegistry::init('Workspace')->find('first', array('conditions' => array('Workspace.id' => $id, 'Workspace.id !=' => ''), 'fields' => array('Workspace.title')));
		if (isset($data) && !empty($data)) {
			return isset($data) ? $data['Workspace']['title'] : array();
		}

	}

	public function workspace($id) {
		$data = ClassRegistry::init('Workspace')->find('first', array('conditions' => array('Workspace.id' => $id), 'fields' => array('Workspace.*')));
		return isset($data) ? $data : array();
	}

	public function workspaceAll($ids) {
		$data = ClassRegistry::init('Workspace')->find('all', array('conditions' => array('Workspace.id' => $ids), 'fields' => array('Workspace.*'), 'recursive' => -1));
		return isset($data) ? $data : array();
	}

	public function singoffelement($id) {

		App::import("Model", "Element");
		App::import("Model", "ElementDecision");
		App::import("Model", "ElementFeedback");
		App::import("Model", "ElementDecisionDetail");
		App::import("Model", "Vote");
		App::import("Model", "Feedback");

		$element = new Element();
		$element_decision = new ElementDecision();
		$element_decision_detail = new ElementDecisionDetail();
		$element_vote = new Vote();
		$element_feedback = new Feedback();

		$feedback = '0';
		$descision = '0';
		$vote = '0';
		$eleRiskCnt = '0';
		// First of all check element itself that it is signed off or not
		$element_signoff = $element->find('count', ['conditions' => ['Element.id' => $id, 'Element.sign_off >' => 0]]);
		if (!empty($element_signoff)) {
			return "Already Signed Off";
		}

		$ed_count = $element_decision->find('count', array('conditions' => array('ElementDecision.element_id' => $id, 'ElementDecision.sign_off >' => 0)));
		if (isset($ed_count) && !empty($ed_count)) {
			$descision = 1;
		} else {
			$ed_count = $element_decision->find('count', array('conditions' => array('ElementDecision.element_id' => $id)));
			if (empty($ed_count)) {
				$descision = 1;
			} else {
				$edd_count_comp = $element_decision->ElementDecisionDetail->find('count', ['conditions' => ['ElementDecision.element_id' => $id, 'ElementDecisionDetail.stage_status' => 1]]);

				$edd_count_comp_blank = $element_decision->ElementDecisionDetail->find('count', ['conditions' => ['ElementDecision.element_id' => $id, 'ElementDecisionDetail.stage_status' => 0]]);
				if ((!empty($edd_count_comp) && $edd_count_comp == '6') || (!empty($edd_count_comp_blank) && $edd_count_comp_blank == '6')) {
				//	$descision = 1;
				} else if (empty($edd_count_comp) && empty($edd_count_comp_blank)) {
				//	$descision = 1;
				}
			}
		}

		$signoff_feedback_count = $element_feedback->find('count', array('conditions' => array('Feedback.element_id' => $id, 'Feedback.sign_off' => '0')));

		if (empty($signoff_feedback_count)) {
			$feedback = 1;
		}

		$signoff_vote_count = $element_vote->find('count', array('conditions' => array('Vote.element_id' => $id, 'Vote.is_completed' => '0', 'VoteQuestion.id !=' => '')));


		if (empty($signoff_vote_count)) {
			$vote = 1;
		}


		//$eleRiskCnt = $this->ViewModel->elementRisksSignoff($id,$this->Session->read('Auth.User.id'));

       $query = "SELECT
				el.element_id,
				#...
				#additional columns for top level query:
				ef.total_hours,
				ef.blue_completed_hours,
				ef.green_remaining_hours,
				ef.amber_remaining_hours,
				ef.red_remaining_hours,
				ef.remaining_hours_color,
				ef.change_hours,
				ef.remaining_hours
			FROM #mock top level query
			(
				SELECT $id AS element_id
			) AS el
			#section to add to top level progress bar query:
			LEFT JOIN #get task progress bar effort data
			(
				SELECT
					ue.element_id,
					ue.remaining_hours_color,
					SUM(ue.change_hours) change_hours,
					SUM(ue.completed_hours + ue.remaining_hours) AS total_hours,
					SUM(ue.completed_hours) AS blue_completed_hours,
					SUM(IF(ue.remaining_hours_color = 'Green', ue.remaining_hours, 0)) AS green_remaining_hours,
					SUM(IF(ue.remaining_hours_color = 'Amber', ue.remaining_hours, 0)) AS amber_remaining_hours,
					SUM(IF(ue.remaining_hours_color = 'Red', ue.remaining_hours, 0)) AS red_remaining_hours,
					SUM((ue.remaining_hours)) AS remaining_hours
				FROM
				(
					SELECT
						ee.element_id,
						ee.user_id,
						ee.completed_hours,
						ee.remaining_hours,
						CASE
							WHEN
							el.sign_off = 1
							OR el.start_date IS NULL
							THEN 'None' #signed off or no schedule
							WHEN
							CEIL(ee.remaining_hours/8) #remaining user 8 hour days
							> DATEDIFF(el.end_date, GREATEST(NOW(), el.start_date))+1 #remaining project days
							THEN 'Red' #remaining user effort days cannot be completed in remaining project days
							WHEN
							CEIL(GREATEST(CEIL(ee.remaining_hours/8),2)*1.5) #remaining user 8 hour days (force minimum 2 days) with 50% contingency days
							> DATEDIFF(el.end_date, GREATEST(NOW(), el.start_date))+1 #remaining project days
							THEN 'Amber' #remaining user days plus 50% contingency cannot be completed in remaining project days
							ELSE 'Green' #remaining user effort days can be completed in remaining project days
						END AS remaining_hours_color,
						ee.change_hours
					FROM
						element_efforts ee
					LEFT JOIN elements el ON
						ee.element_id = el.id
					WHERE
						ee.is_active = 1
				) AS ue
				GROUP BY ue.element_id
			) AS ef ON
				el.element_id = ef.element_id";

			$resultEfforts =  ClassRegistry::init('Element')->query($query);

			$remainHours = 0;
			if(isset($resultEfforts) && !empty($resultEfforts)){
				$remainHours = $resultEfforts[0]['ef']['remaining_hours'];
			}


		//  echo $feedback.' - feedback<br>'.$vote.'- vote<br>'.$descision;
		$message = '';
		if (!empty($descision) && !empty($vote) && !empty($feedback) && ( empty($eleRiskCnt) || $eleRiskCnt == 0 ) && (empty($remainHours) ||  $remainHours==0) ) {
			return 1;
		} else if (empty($descision) && !empty($vote) && !empty($feedback)) {
			// $message = 'This Task cannot be signed off because a Decision is in progress.';
			$message = 'You cannot sign off this Task because a Decision is in progress.';
		} else if (!empty($descision) && empty($vote) && !empty($feedback)) {
			$message = 'This Task cannot be signed off because a Vote is in progress.';
		} else if (!empty($descision) && !empty($vote) && empty($feedback)) {
			//$message = 'Cannot Sign-off, Feedback in process.';
			$message = 'This Task cannot be signed off because a Feedback is in progress.';
		} else if (empty($descision) && empty($vote) && !empty($feedback)) {
			//$message = 'You cannot sign off a task while decision and vote are in process.';
			$message = 'This Task cannot be signed off because a Decision and Votes are in progress.';
		} else if (!empty($descision) && empty($vote) && empty($feedback)) {
			//$message = 'You cannot sign off a task while feedback and vote are in process.';
			$message = 'This Task cannot be signed off because a Feedback and Votes are in progress.';
		} else if (empty($descision) && !empty($vote) && empty($feedback)) {
			//$message = 'You cannot sign off a task while decision and feedback are in process.';
			$message = 'This Task cannot be signed off because a Decision and Feedback are in progress.';
		} else if (empty($descision) && empty($vote) && empty($feedback)) {
			//$message = 'You cannot sign off a task while decision and feedback and vote are in process.';
			$message = 'You cannot sign off this Task because a Decision, Feedback and Vote are in progress.';
		} else if( isset($eleRiskCnt) && $eleRiskCnt > 0 ){
			//$message = 'You cannot sign off a task while Risks that are related and not signed off.';
			//$message = 'You cannot sign off this Task because there is a related Risk.';
			$message = 'This Task cannot be signed off because there is a related Risk.';
		}else if( isset($remainHours) && $remainHours > 0 ){
			//$message = 'You cannot sign off a task while Risks that are related and not signed off.';
			//$message = 'You cannot sign off this Task because there is a related Risk.';
			$message = 'This Task cannot be signed off because it has remaining Effort hours.';
		}

		if (!empty($message)) {
			return $message;
		} else {
			return 1;
		}
	}

	public function get_up_id($pid, $uid) {

		$up_data = ClassRegistry::init('UserProject')->find('first', array('conditions' => array('UserProject.project_id' => $pid)));

		return (isset($up_data['UserProject']['id'])) ? $up_data['UserProject']['id'] : null;
	}

	public function wsp_permission_edit($id, $pid, $uid) {

		$view = new View();
		$common = $view->loadHelper('Common');

		//echo $id."<br>".$common->get_up_id($pid,$uid)."<br>".$uid;
		//echo  $common->get_up_id($pid,$uid)."<br>".$pid ;

		$data = ClassRegistry::init('WorkspacePermission')->find('first', array('conditions' => array('WorkspacePermission.user_id' => $uid, 'WorkspacePermission.project_workspace_id' => $id, 'WorkspacePermission.user_project_id' => $common->get_up_id($pid, $uid))));

		return isset($data['WorkspacePermission']['permit_edit']) ? $data['WorkspacePermission']['permit_edit'] : 0;
	}

	public function wsp_permission_delete($id, $pid, $uid) {

		$view = new View();
		$common = $view->loadHelper('Common');

		$data = ClassRegistry::init('WorkspacePermission')->find('first', array('conditions' => array('WorkspacePermission.project_workspace_id' => $id, 'WorkspacePermission.user_id' => $uid, 'WorkspacePermission.user_project_id' => $common->get_up_id($pid, $uid))));
		return isset($data['WorkspacePermission']['permit_delete']) ? $data['WorkspacePermission']['permit_delete'] : 0;
	}

	public function wsp_permission_details($id, $pid, $uid) {

		$view = new View();
		$common = $view->loadHelper('Common');

		$data = ClassRegistry::init('WorkspacePermission')->find('all', array('conditions' => array('WorkspacePermission.project_workspace_id' => $id, 'WorkspacePermission.user_id' => $uid, 'WorkspacePermission.user_project_id' => $common->get_up_id($pid, $uid))));
		return isset($data) ? $data : array();
	}

	public function workspace_permit($id, $pid, $uid) {

		$data = ClassRegistry::init('WorkspacePermission')->find('first', array('conditions' => array('WorkspacePermission.project_workspace_id' => $id, 'WorkspacePermission.user_id' => $uid, 'WorkspacePermission.user_project_id' => $this->get_up_id($pid, $uid))));
		return isset($data) ? $data : array();
	}

	public function project_permission_details($pid, $uid) {

		$data = ClassRegistry::init('ProjectPermission')->find('first', array('conditions' => array('ProjectPermission.user_id' => $uid, 'ProjectPermission.user_project_id' => $this->get_up_id($pid, $uid))));

		return isset($data) ? $data : array();
	}

	public function userproject($pid, $uid) {
		//ClassRegistry::init('UserProject')->unbindAll();
		$data = ClassRegistry::init('UserProject')->find('first', array('conditions' => array('UserProject.user_id' => $uid, 'UserProject.project_id' => $pid, 'UserProject.owner_user' => 1)));
		// e($uid);

		// echo ClassRegistry::init('UserProject')->_query();

		return isset($data) ? $data : array();
	}

	public function userprojectOwner($pid) {

		ClassRegistry::init('UserProject')->unbindAll();
		$data = ClassRegistry::init('UserProject')->find('first', array('conditions' => array('UserProject.project_id' => $pid, 'UserProject.owner_user' => 1)));
		// echo ClassRegistry::init('UserProject')->_query();
		//pr($data);
		return isset($data['UserProject']['user_id']) ? $data['UserProject']['user_id'] : "N/A";
	}

	public function userprojectOwnerNew($pid) {
		$data = ClassRegistry::init('UserProject')->find('first', array('conditions' => array('UserProject.project_id' => $pid, 'UserProject.owner_user' => 1)));
		// echo ClassRegistry::init('UserProject')->_query();
		// pr($data);die;
		return isset($data) ? $data : "N/A";
	}

	public function element_permission_details($wid, $pid, $uid) {

		$datas = ClassRegistry::init('ElementPermission')->find('all', array('conditions' => array('ElementPermission.user_id' => $uid, 'ElementPermission.project_id' => $pid, 'ElementPermission.workspace_id' => $wid, 'ElementPermission.user_id IS NOT NULL')));

		if (isset($datas) && !empty($datas)) {

			foreach ($datas as $dat) {
				$data[] = $dat['ElementPermission']['element_id'];
			}
		}

		return isset($data) ? $data : array();
	}

	public function element_permission_data($pid, $uid) {

		$datas = ClassRegistry::init('ElementPermission')->find('all', array('conditions' => array('ElementPermission.user_id' => $uid, 'ElementPermission.project_id' => $pid, 'ElementPermission.user_id IS NOT NULL', 'Project.id !=' => '', 'Element.id !=' => '', 'Workspace.id !=' => '')));

		if (isset($datas) && !empty($datas)) {

			foreach ($datas as $dat) {
				$data[] = $dat['ElementPermission']['element_id'];
			}
		}

		return isset($data) ? $data : array();
	}

	public function elementLink_creator($linkid, $pid, $uid) {

		$dataUO = ClassRegistry::init('ElementLink')->find('first', array('conditions' => array('ElementLink.id' => $linkid, 'ElementLink.creater_id >' => 0)));

		//$dataUO['UserProject']['user_id']

		$dataEC = ClassRegistry::init('UserProject')->find('first', array('conditions' => array('UserProject.project_id' => $pid, 'UserProject.owner_user' => 1)));

		// pr($uid); die;

		if (isset($dataUO) && !empty($dataUO)) {
			$id = $dataUO['ElementLink']['creater_id'];
		} else {
			$id = $dataEC['UserProject']['user_id'];
		}

		$dataUD = ClassRegistry::init('UserDetail')->find('first', array('conditions' => array('UserDetail.user_id' => $id), 'fields' => array('first_name', 'last_name')));

		return isset($dataUD) ? $dataUD['UserDetail']['first_name'] . " " . $dataUD['UserDetail']['last_name'] : 'N/A';
	}

	public function elementNote_creator($linkid, $pid, $uid) {

		$dataUO = ClassRegistry::init('ElementNote')->find('first', array('conditions' => array('ElementNote.id' => $linkid, 'ElementNote.creater_id >' => 0)));

		//$dataUO['UserProject']['user_id']

		$dataEC = ClassRegistry::init('UserProject')->find('first', array('conditions' => array('UserProject.project_id' => $pid, 'UserProject.owner_user' => 1)));

		//pr($linkid);

		if (isset($dataUO) && !empty($dataUO)) {
			$id = $dataUO['ElementNote']['creater_id'];
		} else {
			$id = $dataEC['UserProject']['user_id'];
		}

		$dataUD = ClassRegistry::init('UserDetail')->find('first', array('conditions' => array('UserDetail.user_id' => $id), 'fields' => array('first_name', 'last_name')));

		return isset($dataUD) ? $dataUD['UserDetail']['first_name'] . " " . $dataUD['UserDetail']['last_name'] : 'N/A';
	}

	public function elementDoc_creator($linkid, $pid, $uid) {

		$dataUO = ClassRegistry::init('ElementDocument')->find('first', array('conditions' => array('ElementDocument.id' => $linkid, 'ElementDocument.creater_id >' => 0)));

		//$dataUO['UserProject']['user_id']

		$dataEC = ClassRegistry::init('UserProject')->find('first', array('conditions' => array('UserProject.project_id' => $pid, 'UserProject.owner_user' => 1)));

		//pr($linkid);

		if (isset($dataUO) && !empty($dataUO)) {
			$id = $dataUO['ElementDocument']['creater_id'];
		} else {
			$id = $dataEC['UserProject']['user_id'];
		}

		$dataUD = ClassRegistry::init('UserDetail')->find('first', array('conditions' => array('UserDetail.user_id' => $id), 'fields' => array('first_name', 'last_name')));

		return isset($dataUD) ? $dataUD['UserDetail']['first_name'] . " " . $dataUD['UserDetail']['last_name'] : 'N/A';
	}

	public function elementDecision_creator($linkid, $pid, $uid) {

		$dataUO = ClassRegistry::init('ElementDecision')->find('first', array('conditions' => array('ElementDecision.id' => $linkid, 'ElementDecision.creater_id >' => 0)));

		//$dataUO['UserProject']['user_id']

		$dataEC = ClassRegistry::init('UserProject')->find('first', array('conditions' => array('UserProject.project_id' => $pid, 'UserProject.owner_user' => 1)));

		//pr($linkid);

		if (isset($dataUO) && !empty($dataUO)) {
			$id = $dataUO['ElementDecision']['creater_id'];
		} else {
			$id = $dataEC['ElementDecision']['user_id'];
		}

		$dataUD = ClassRegistry::init('UserDetail')->find('first', array('conditions' => array('UserDetail.user_id' => $id), 'fields' => array('first_name', 'last_name')));

		return isset($dataUD) ? $dataUD['UserDetail']['first_name'] . " " . $dataUD['UserDetail']['last_name'] : 'N/A';
	}

	public function elementMM_creator($linkid, $pid, $uid) {

		$dataUO = ClassRegistry::init('ElementMindmap')->find('first', array('conditions' => array('ElementMindmap.id' => $linkid, 'ElementMindmap.creater_id >' => 0)));

		//$dataUO['UserProject']['user_id']

		$dataEC = ClassRegistry::init('UserProject')->find('first', array('conditions' => array('UserProject.project_id' => $pid, 'UserProject.owner_user' => 1)));

		//pr($linkid);

		if (isset($dataUO) && !empty($dataUO)) {
			$id = $dataUO['ElementMindmap']['creater_id'];
		} else {
			$id = $dataEC['UserProject']['user_id'];
		}

		$dataUD = ClassRegistry::init('UserDetail')->find('first', array('conditions' => array('UserDetail.user_id' => $id), 'fields' => array('first_name', 'last_name')));

		return isset($dataUD) ? $dataUD['UserDetail']['first_name'] . " " . $dataUD['UserDetail']['last_name'] : 'N/A';
	}

	public function element_share_permission($element_id, $pid, $uid) {

		$data = ClassRegistry::init('ElementPermission')->find('first', array('conditions' => array('ElementPermission.user_id' => $uid, 'ElementPermission.project_id' => $pid, 'ElementPermission.element_id' => $element_id)));

		return isset($data) ? $data : 0;
	}

	public function element_manage_editable($element_id, $pid, $uid) {

		$data = ClassRegistry::init('ElementPermission')->find('first', array('conditions' => array('ElementPermission.user_id' => $uid, 'ElementPermission.project_id' => $pid, 'ElementPermission.element_id' => $element_id, 'ElementPermission.is_editable' => 1)));

		return isset($data['ElementPermission']['is_editable']) ? $data['ElementPermission']['is_editable'] : 0;
	}

	public function element_manage_edit($element_id, $pid, $uid) {

		$data = ClassRegistry::init('ElementPermission')->find('first', array('conditions' => array('ElementPermission.user_id' => $uid, 'ElementPermission.project_id' => $pid, 'ElementPermission.element_id' => $element_id, 'ElementPermission.permit_edit' => 1)));
		return isset($data['ElementPermission']['permit_edit']) ? $data['ElementPermission']['permit_edit'] : 0;
	}

	public function element_manage_editable_user($element_id, $pid, $uid = null) {

		$data = ClassRegistry::init('ElementPermission')->find('first', array('conditions' => array('ElementPermission.project_id' => $pid, 'ElementPermission.element_id' => $element_id, 'ElementPermission.is_editable' => 1)));

		$dataEC = ClassRegistry::init('UserProject')->find('first', array('conditions' => array('UserProject.project_id' => $pid, 'UserProject.owner_user' => 1)));

		if (isset($data) && !empty($data)) {
			$id = $data['ElementPermission']['user_id'];

			$dataUD = ClassRegistry::init('UserDetail')->find('first', array('conditions' => array('UserDetail.user_id' => $id), 'fields' => array('first_name', 'last_name')));

			if ($this->Session->read('Auth.User.id') != $data['ElementPermission']['user_id']) {
				return isset($dataUD) ? $dataUD['UserDetail']['first_name'] . " " . $dataUD['UserDetail']['last_name'] : 'N/A';
			}
		}
		return false;
	}

	public function element_creator($element_id, $pid, $return = null) {

		$data = ClassRegistry::init('ElementPermission')->find('first', array('conditions' => array('ElementPermission.project_id' => $pid, 'ElementPermission.element_id' => $element_id, 'ElementPermission.is_editable' => 1)));


		if (isset($data) && !empty($data)) {
			$id = $data['ElementPermission']['user_id'];

			$dataUD = ClassRegistry::init('UserDetail')->find('first', array('conditions' => array('UserDetail.user_id' => $id), 'fields' => array('first_name', 'last_name', 'user_id')));

			$dataUDT['username'] = isset($dataUD) ? $dataUD['UserDetail']['first_name'] . " " . $dataUD['UserDetail']['last_name'] : 'N/A';
			$dataUDT['user_id'] = isset($dataUD) ? $dataUD['UserDetail']['user_id'] : 'N/A';
			//return isset($dataUD) ? $dataUD['UserDetail']['first_name']." " .$dataUD['UserDetail']['last_name'] : 'N/A';

			if (isset($dataUD) && !empty($dataUD)) {
				return $dataUDT;
			} else if (isset($return) && !empty($return)) {
				return null;
			} else {
				return 'N/A';
			}

			// return isset($dataUD) ? $dataUDT : 'N/A';
		} else {
			if (isset($return) && !empty($return)) {
				return null;
			}
			return 'N/A';
		}
	}

	public function element_sharers($element_id, $pid, $all = 0) {
		$param = ($all > 0) ? array(0, 1) : 0;

		$data = ClassRegistry::init('ElementPermission')->find('all', array('conditions' => array('ElementPermission.project_id' => $pid, 'ElementPermission.element_id' => $element_id, 'ElementPermission.is_editable' => $param)));

		if (isset($data) && !empty($data)) {

			foreach ($data as $dat) {
				$datas[] = $dat['ElementPermission']['user_id'];
			}
		}
		return isset($datas) ? $datas : array();
	}

	public function work_permission_details($pid, $uid) {

		$view = new View();
		$common = $view->loadHelper('Common');

		// ClassRegistry::init('WorkspacePermission')->recursive = 2;
		$datas = ClassRegistry::init('WorkspacePermission')->find('all', array('conditions' => array('WorkspacePermission.user_id' => $uid, 'WorkspacePermission.user_project_id' => $common->get_up_id($pid, $uid), 'ProjectWorkspace.id !=' => '')));

		if (isset($datas) && !empty($datas)) {

			foreach ($datas as $dat) {
				$data[] = $dat['WorkspacePermission']['project_workspace_id'];
			}

			$new_data = ClassRegistry::init('ProjectWorkspace')->find('all', array('conditions' => array('ProjectWorkspace.id' => $data, 'Workspace.studio_status !=' => 1)));
			if (isset($new_data) && !empty($new_data)) {
				$data = Set::extract($new_data, '/ProjectWorkspace/id');
			}
		}

		return isset($data) ? $data : array();
	}

	public function VoteResultOption($qid, $op_id) {
		$user_id = $this->Session->read('Auth.User.id');
		$datas = ClassRegistry::init('VoteResult')->find('first', array('conditions' => array('VoteResult.vote_question_id' => $qid, 'VoteResult.user_id' => $user_id, 'VoteResult.vote_question_option_id' => $op_id)));

		return (!empty($datas) ? $datas['VoteResult']['vote_range'] : '0');
	}

	public function IsQuesEditble($qid) {
		return (ClassRegistry::init('VoteResult')->find('count', array('conditions' => array('VoteResult.vote_change_datetime >=' => time())))) ? '' : 'disabled';
	}

	/* public function getDataTermTypeList($data_term_id=null){ //echo $country_code;die;
		      $dataTermTypes = ClassRegistry::init('DataTermsType')->find('list', array('conditions'=>array('DataTermsType.data_term_id'=> $data_term_id), 'fields' => array('id', 'data_terms_type')));
		      return isset($dataTermTypes) ? $dataTermTypes : array();
	*/

	public function ProjectOwner($pid, $uid = null,$fldtc=null) {

		if( !empty($fldtc) && $fldtc == "taskcenter"){

			ClassRegistry::init('UserProject')->unbindModel(array('hasMany' => array('ProjectPermission')));
			$data = ClassRegistry::init('UserProject')->find('first', array('conditions' => array('UserProject.project_id' => $pid, 'UserProject.owner_user' => 1), 'fields' => array('UserProject.user_id','User.id') ));

		} else {
			$data = ClassRegistry::init('UserProject')->find('first', array('conditions' => array('UserProject.project_id' => $pid, 'UserProject.owner_user' => 1) ));
		}

		return isset($data) ? $data : array();
	}

	public function ProjectAllOwner($pid, $uid = null) {
		//pr($pid); die;
		$data = ClassRegistry::init('UserProject')->find('all', array('conditions' => array('UserProject.project_id' => $pid, 'UserProject.owner_user' => 1)));
		return isset($data) ? $data : array();
	}

	// To check user has voted for given vote reuest or not. If voted then date return otherwise blank
	public function responded($vote_id, $user_id) {
		$data = ClassRegistry::init('VoteResult')->find('first', array('conditions' => array('VoteResult.vote_id' => $vote_id, 'VoteResult.user_id' => $user_id)));
		//return !empty($data) ? date('d M,Y', $data['VoteResult']['modified']) : '-';

		$view = new View();
		$wiki = $view->loadHelper('Wiki');
		return !empty($data) ? $wiki->_displayDate($date = date('Y-m-d h:i:s A', $data['VoteResult']['modified']), $format = 'd M, Y') : '-';
	}

	// To check user has feedback for given feedback reuest or not. If voted then date return otherwise blank
	public function feedbackresponded($feedback_id, $user_id) {
		$data = ClassRegistry::init('FeedbackResult')->find('first', array('conditions' => array('FeedbackResult.feedback_id' => $feedback_id, 'FeedbackResult.user_id' => $user_id), 'order' => array('FeedbackResult.id' => 'DESC')));
		//return !empty($data) ? date('d M,Y', $data['FeedbackResult']['modified']) : '-';
		$view = new View();
		$wiki = $view->loadHelper('Wiki');
		return !empty($data) ? $wiki->_displayDate($date = date('Y-m-d h:i:s A', $data['FeedbackResult']['modified']), $format = 'd M, Y') : '-';
	}

	public function totalinvites($vote_id) {
		$data = ClassRegistry::init('VoteUser')->find('count', array('conditions' => array('VoteUser.vote_id' => $vote_id)));
		return !empty($data) ? $data : '0';
	}

	public function totalparticipants($vote_id) {
		$data = ClassRegistry::init('VoteResult')->find('count', array('conditions' => array('VoteResult.vote_id' => $vote_id, 'VoteResult.vote_question_option_id !=' => 'D'), 'group' => array('user_id')));
		return !empty($data) ? $data : '0';
	}

	public function totaldeclined($vote_id) {
		$data = ClassRegistry::init('VoteResult')->find('count', array('conditions' => array('VoteResult.vote_id' => $vote_id, 'VoteResult.vote_question_option_id ' => 'D'), 'group' => array('user_id')));
		return !empty($data) ? $data : '0';
	}

	public function totalFeedbackinvites($feedback_id) {
		$data = ClassRegistry::init('FeedbackUser')->find('count', array('conditions' => array('FeedbackUser.feedback_id' => $feedback_id)));
		return !empty($data) ? $data : '0';
	}

	public function totalFeedbackparticipants($feedback_id) {
		$data = ClassRegistry::init('FeedbackResult')->find('count', array('conditions' => array('FeedbackResult.feedback_id' => $feedback_id, 'FeedbackResult.is_decline' => 0), 'group' => array('user_id')));
		return !empty($data) ? $data : '0';
	}

	public function totalFeedbackdeclined($feedback_id) {
		$data = ClassRegistry::init('FeedbackResult')->find('count', array('conditions' => array('FeedbackResult.feedback_id' => $feedback_id, 'FeedbackResult.is_decline' => 1), 'group' => array('user_id')));
		return !empty($data) ? $data : '0';
	}

	public function feedbackRate($fid, $fr_id, $give_by_id, $give_to_id) {
		//echo $fid."<br>".$fr_id."<br>".$give_by_id."<br>".$give_to_id;
		$user_id = $this->Session->read('Auth.User.id');
		$datas = ClassRegistry::init('FeedbackRating')->find('first', array('conditions' => array('FeedbackRating.feedback_id' => $fid, 'FeedbackRating.feedback_result_id' => $fr_id, 'FeedbackRating.given_by_id' => $give_by_id, 'FeedbackRating.given_to_id' => $give_to_id)));
		//pr($datas); die;

		//echo $fid . " " . $fr_id . " " . $give_by_id . " " . $give_to_id; //die;
		return (!empty($datas) ? $datas['FeedbackRating']['rate'] : 'Not Given');
	}

	public function feedbackRatebyResultID($fid, $fr_id) {
		//echo $fid."<br>".$fr_id."<br>".$give_by_id."<br>".$give_to_id;
		$user_id = $this->Session->read('Auth.User.id');
		$datas = ClassRegistry::init('FeedbackRating')->find('first', array('conditions' => array('FeedbackRating.feedback_id' => $fid, 'FeedbackRating.feedback_result_id' => $fr_id)));
		//pr($datas); die;

		return (!empty($datas) ? $datas['FeedbackRating']['rate'] : 'Not Given');
	}

	public function feedbackRateC($fid, $fr_id) {
		//echo $fid."<br>".$fr_id."<br>".$give_by_id."<br>".$give_to_id;
		$user_id = $this->Session->read('Auth.User.id');
		$datas = ClassRegistry::init('FeedbackRating')->find('first', array('conditions' => array('FeedbackRating.feedback_id' => $fid, 'FeedbackRating.feedback_result_id' => $fr_id)));
		//pr($datas); die;
		//pr($datas); //die;
		return (!empty($datas) ? $datas['FeedbackRating']['comment'] : NULL);
	}

	public function feedbackRateDetail($fid, $fr_id) {
		//echo $fid."<br>".$fr_id."<br>".$give_by_id."<br>".$give_to_id;
		$user_id = $this->Session->read('Auth.User.id');
		$datas = ClassRegistry::init('FeedbackRating')->find('first', array('conditions' => array('FeedbackRating.feedback_id' => $fid, 'FeedbackRating.feedback_result_id' => $fr_id)));
		//pr($datas); die;
		//pr($datas); //die;
		return (!empty($datas) ? $datas['FeedbackRating'] : NULL);
	}

	public function feedbackRateAverage($give_to_id = null) {
		//echo $fid."<br>".$fr_id."<br>".$give_by_id."<br>".$give_to_id;
		$user_id = $this->Session->read('Auth.User.id');

		$datas = ClassRegistry::init('FeedbackRating')->find('all', array('conditions' => array('FeedbackRating.given_to_id' => $give_to_id)));

		if (isset($datas) && !empty($datas)) {
			$i = 1;
			$rateer = 0;
			$rateTotal = 0;
			foreach ($datas as $dat) {
				$rateer += $dat['FeedbackRating']['rate'];
				$i++;
			}

			$size = sizeof($datas);
			return $rateer / $size;
		}
	}

	public function countTotalElementParts($id, $type) {

		App::import("Model", "Vote");
		$vo = new Vote();

		$datas = ClassRegistry::init($type)->find('count', array('conditions' => array($type . '.element_id' => $id)));

		if ($type == 'Feedback') {
			$datas = ClassRegistry::init($type)->find('count', array('conditions' => ['element_id' => $id ]));
			// $datas = ClassRegistry::init($type)->find('count', array('conditions' => ['element_id' => $id, 'end_date >=' => date('Y-m-d 00:00:00')]));
		}

		if ($type == 'Vote') {
			$datas = $vo->find('count', array('conditions' => ['element_id' => $id , 'VoteQuestion.id !=' => '']));
			// $datas = $vo->find('count', array('conditions' => ['element_id' => $id, 'end_date >=' => date('Y-m-d 00:00:00'), 'VoteQuestion.id !=' => '']));
		}

		return $datas ? $datas : 0;
	}

	function getDateStartOrEnd($project_id = null) {
		$project = ClassRegistry::init('Project')->find('first', array("fields" => array("Project.start_date", "Project.end_date"), 'conditions' => array('Project.id' => $project_id)));
		return isset($project['Project']) && !empty($project['Project']) ? $project['Project'] : 'N/A';
	}

	function getDateStartOrEnd_elm($workspace_id = null) {

		$workspace = ClassRegistry::init('Workspace')->find('first', array("fields" => array("Workspace.start_date", "Workspace.end_date", "Workspace.sign_off"), 'conditions' => array('Workspace.id' => $workspace_id)));

		return isset($workspace['Workspace']) && !empty($workspace['Workspace']) ? $workspace['Workspace'] : 'N/A';
	}

	public function workspace_status($id) {

		App::import("Model", "Workspace");
		$ws = new Workspace();

		$data = $ws->find('first', ['conditions' => [
			'Workspace.id' => $id,
		],
			'recursive' => -1,
		]);
		// pr($data, 1);

		$status = STATUS_NOT_SPACIFIED;

		if ((isset($data['Workspace']['start_date']) && !empty($data['Workspace']['start_date'])) && (isset($data['Workspace']['end_date']) && !empty($data['Workspace']['end_date']))) {

			if (((isset($data['Workspace']['start_date']) && !empty($data['Workspace']['start_date'])) && date('Y-m-d', strtotime($data['Workspace']['start_date'])) > date('Y-m-d')) && $data['Workspace']['sign_off'] != 1) {
				$status = STATUS_NOT_STARTED;
			} else if (((isset($data['Workspace']['end_date']) && !empty($data['Workspace']['end_date'])) && date('Y-m-d', strtotime($data['Workspace']['end_date'])) < date('Y-m-d')) && $data['Workspace']['sign_off'] != 1) {
				$status = STATUS_OVERDUE;
			} else if (isset($data['Workspace']['sign_off']) && !empty($data['Workspace']['sign_off']) && $data['Workspace']['sign_off'] > 0) {
				$status = STATUS_COMPLETED;
			} else if ((((isset($data['Workspace']['end_date']) && !empty($data['Workspace']['end_date'])) && (isset($data['Workspace']['start_date']) && !empty($data['Workspace']['start_date']))) && (date('Y-m-d', strtotime($data['Workspace']['start_date'])) <= date('Y-m-d')) && date('Y-m-d', strtotime($data['Workspace']['end_date'])) >= date('Y-m-d')) && $data['Workspace']['sign_off'] != 1) {
				$status = STATUS_PROGRESS;
			}
		}
		return $status;
	}

	public function project_status($id) {

		App::import("Model", "Project");
		$ws = new Project();

		$data = $ws->find('first', ['conditions' => [
			'Project.id' => $id,
		],
			'recursive' => -1,
		]);
		// pr($data, 1);

		$status = STATUS_NOT_SPACIFIED;

		if ((isset($data['Project']['start_date']) && !empty($data['Project']['start_date'])) && (isset($data['Project']['end_date']) && !empty($data['Project']['end_date']))) {

			if (((isset($data['Project']['start_date']) && !empty($data['Project']['start_date'])) && date('Y-m-d', strtotime($data['Project']['start_date'])) > date('Y-m-d')) && $data['Project']['sign_off'] != 1) {
				$status = STATUS_NOT_STARTED;
			} else if (((isset($data['Project']['end_date']) && !empty($data['Project']['end_date'])) && date('Y-m-d', strtotime($data['Project']['end_date'])) < date('Y-m-d')) && $data['Project']['sign_off'] != 1) {
				$status = STATUS_OVERDUE;
			} else if (isset($data['Project']['sign_off']) && !empty($data['Project']['sign_off']) && $data['Project']['sign_off'] > 0) {
				$status = STATUS_COMPLETED;
			} else if ((((isset($data['Project']['end_date']) && !empty($data['Project']['end_date'])) && (isset($data['Project']['start_date']) && !empty($data['Project']['start_date']))) && (date('Y-m-d', strtotime($data['Project']['start_date'])) <= date('Y-m-d')) && date('Y-m-d', strtotime($data['Project']['end_date'])) >= date('Y-m-d')) && $data['Project']['sign_off'] != 1) {
				$status = STATUS_PROGRESS;
			}
		}
		return $status;
	}

	public function element_status($id) {

		App::import("Model", "Element");
		$ws = new Element();

		$data = $ws->find('first', ['conditions' => [
			'Element.id' => $id,
		],
			'recursive' => -1,
		]);
		// pr($data, 1);

		$status = STATUS_NOT_SPACIFIED;

		if ((isset($data['Element']['start_date']) && !empty($data['Element']['start_date'])) && (isset($data['Element']['end_date']) && !empty($data['Element']['end_date']))) {

			if (((isset($data['Element']['start_date']) && !empty($data['Element']['start_date'])) && date('Y-m-d', strtotime($data['Element']['start_date'])) > date('Y-m-d')) && $data['Element']['sign_off'] != 1) {
				$status = STATUS_NOT_STARTED;
			} else if (((isset($data['Element']['end_date']) && !empty($data['Element']['end_date'])) && date('Y-m-d', strtotime($data['Element']['end_date'])) < date('Y-m-d')) && $data['Element']['sign_off'] != 1) {
				$status = STATUS_OVERDUE;
			} else if (isset($data['Element']['sign_off']) && !empty($data['Element']['sign_off']) && $data['Element']['sign_off'] > 0) {
				$status = STATUS_COMPLETED;
			} else if ((((isset($data['Element']['end_date']) && !empty($data['Element']['end_date'])) && (isset($data['Element']['start_date']) && !empty($data['Element']['start_date']))) && (date('Y-m-d', strtotime($data['Element']['start_date'])) <= date('Y-m-d')) && date('Y-m-d', strtotime($data['Element']['end_date'])) >= date('Y-m-d')) && $data['Element']['sign_off'] != 1) {
				$status = STATUS_PROGRESS;
			}
		}
		return $status;
	}

	public function feed_status($id, $status) {
		App::import("Model", "Feedback");
		$feedback = new Feedback();
		$select = 'SELECT feedback.*';
		$diff = ', TIMESTAMPDIFF( DAY, feedback.`start_date`, feedback.`end_date`) AS totalDays ';
		$order = 'ORDER BY totalDays ASC';
		$from = 'FROM feedback ';
		$query = '';
		$query .= "WHERE feedback.element_id IN (" . $id . ") ";
		$order = '';

		if ($status == "overdue") {
			$diff = ', TIMESTAMPDIFF( DAY, feedback.`end_date`, now()) AS totalDays ';
			$query .= "AND date(feedback.end_date) < '" . date('Y-m-d') . "' ";
			$query .= "AND feedback.sign_off != 1 ";
			$order = 'ORDER BY totalDays DESC';
		}
		if ($status == "progressing") {
			$query .= "AND date(feedback.start_date) <= '" . date('Y-m-d') . "' ";
			$query .= "AND date(feedback.end_date) >= '" . date('Y-m-d') . "' ";
			$query .= "AND feedback.sign_off != 1 ";
			$order = 'ORDER BY end_date ASC';
		}

		if ($status == "not_started") {
			$query .= "AND date(feedback.start_date) > '" . date('Y-m-d') . "' ";
			$query .= "AND feedback.sign_off != 1 ";
			$order = 'ORDER BY end_date ASC';
		}

		if ($status == "completed") {
			$query .= "AND feedback.sign_off = 1 ";
		}

		//echo $select.$diff.$from.$query . '' . $order;
		//$data = $feedback->find('all', ['conditions'=>['user_id'=>$user_id,'element_id'=>$id],'recursive' => -1]);
		$data = $feedback->query($select . $diff . $from . $query . '' . $order);
		return $data;
	}

	public function ele_feed_count($id, $status) {
		App::import("Model", "Feedback");
		$feedback = new Feedback();

		App::import("Model", "Vote");
		App::import("Model", "ElementDecision");

		$vote = new Vote();
		$elementdecision = new ElementDecision();


		if( $status == 'feedback' ){
			$select = 'SELECT Count(id) total ';
			$from = ' FROM feedback ';
			$query = " WHERE feedback.element_id IN (" . $id . ") ";
			$data = $feedback->query($select . $from . $query );
		}

		if( $status == 'vote' ){
			$select = 'SELECT Count(id) total ';
			$from = ' FROM votes as vote ';
			$query = " WHERE vote.element_id IN (" . $id . ") ";
			$data = $vote->query($select . $from . $query );
		}

		if( $status == 'decision' ){
			$select = 'SELECT Count(id) total ';
			$from = ' FROM element_decisions ';
			$query = " WHERE element_decisions.element_id IN (" . $id . ") ";
			$data = $elementdecision->query($select . $from . $query );
		}

		if( isset($data) && !empty($data[0][0]['total']) ){
			return $data[0][0]['total'];
		} else {
			return 0;
		}
	}


	public function vote_status($id, $status) {
		App::import("Model", "Vote");
		App::import("Model", "VoteUser");

		$vote = new Vote();
		$voteUser = new VoteUser();

		$select = 'SELECT vote.*';
		$diff = ', TIMESTAMPDIFF( DAY, vote.`start_date`, vote.`end_date`) AS totalDays ';
		$order = 'ORDER BY totalDays ASC';
		$from = 'FROM votes as vote ';
		$query = '';

		$order = '';
		// $query .= 'INNER JOIN vote_users as voteUser ON vote.id=voteUser.vote_id ';
		$query .= "WHERE vote.element_id IN (" . $id . ") ";

		if ($status == "overdue") {
			$diff = ', TIMESTAMPDIFF( DAY, vote.`end_date`, now()) AS totalDays ';
			$query .= "AND date(vote.end_date) < '" . date('Y-m-d') . "' ";
			$query .= "AND vote.is_completed != 1 ";
			$order = 'ORDER BY totalDays DESC';
		}
		if ($status == "progressing") {
			$query .= "AND date(vote.start_date) <= '" . date('Y-m-d') . "' ";
			$query .= "AND date(vote.end_date) >= '" . date('Y-m-d') . "' ";
			$query .= "AND vote.is_completed != 1 ";
			$order = 'ORDER BY end_date ASC';
		}

		if ($status == "not_started") {
			$query .= "AND date(vote.start_date) > '" . date('Y-m-d') . "' ";
			$query .= "AND vote.is_completed != 1 ";
			$order = 'ORDER BY end_date ASC';
		}

		if ($status == "completed") {
			$query .= "AND vote.is_completed = 1 ";
		}

		// echo $select.$diff.$from.$query . '' . $order;
		//$data = $feedback->find('all', ['conditions'=>['user_id'=>$user_id,'element_id'=>$id],'recursive' => -1]);
		$data = $vote->query($select . $diff . $from . $query . '' . $order);

		$vote->query("DELETE FROM votes WHERE id not in (SELECT vote_id FROM vote_questions) ");
		//pr($data);
		return $data;
	}

	public function get_project($id = null) {
		App::import("Model", "Project");
		$project = new Project();
		$data = $project->findById($id);
		return $data;
	}

	public function get_projectbyid($id = null) {
		App::import("Model", "Project");
		$project = new Project();
		$data = $project->find('all', array('conditions' => array('Project.id' => $id), 'recursive' => -1));
		return $data;
	}

	public function get_projectnamebyid($id = null) {
		App::import("Model", "Project");
		$project = new Project();

		$data = $project->find('first', array('conditions' => array('Project.id' => $id), 'recursive' => -1, 'fields' => ['Project.id', 'Project.title']));

		return (isset($data['Project']['title']) && !empty($data['Project']['title'])) ? strip_tags($data['Project']['title']) : null;
	}

	public function ProjectBoardData($pid) {
		//echo $fid."<br>".$fr_id."<br>".$give_by_id."<br>".$give_to_id;
		$user_id = $this->Session->read('Auth.User.id');
		$datas = ClassRegistry::init('ProjectBoard')->find('first', array('conditions' => array('ProjectBoard.project_id' => $pid, 'ProjectBoard.sender' => $user_id)));
		//pr($datas); die;
		//pr($datas); //die;
		return (!empty($datas) ? $datas['ProjectBoard'] : NULL);
	}

	public function ProjectBoardProjectUserList($pid) {
		$user_id = $this->Session->read('Auth.User.id');
		$datas = ClassRegistry::init('ProjectBoard')->find('all', array('conditions' => array('ProjectBoard.project_id' => $pid, 'ProjectBoard.receiver' => $user_id)));

		//pr($datas); die;

		return (!empty($datas) ? $datas : NULL);
	}

	public function ProjectBoardProjectUserListNew($pid) {
		$user_id = $this->Session->read('Auth.User.id');
		//$datas = ClassRegistry::init('ProjectBoard')->find('all', array('conditions' => array('ProjectBoard.project_id' => $pid, 'ProjectBoard.receiver' => $user_id)));


		$query = "select user_permissions.user_id,ProjectBoard.*, Project.* from user_permissions left join projects as Project on Project.id = user_permissions.project_id inner join project_boards as ProjectBoard on ProjectBoard.project_id = user_permissions.project_id where user_permissions.role in('Owner','Group Owner','Creator') and workspace_id is null and user_permissions.project_id = $pid GROUP by ProjectBoard.sender order by ProjectBoard.id desc";
		//echo $query;
		$data = ClassRegistry::init('UserPermission')->query($query);

		return ( isset($data) && !empty($data[0]) ) ? $data : array();


		//pr($datas); die;

		//return (!empty($datas) ? $datas : NULL);
	}




	public function ProjectBoardProjectList($receiver, $sender) {
		$user_id = $this->Session->read('Auth.User.id');
		$datas = ClassRegistry::init('ProjectBoard')->find('all', array('conditions' => array('ProjectBoard.receiver' => $receiver, 'ProjectBoard.sender' => $sender)));

		//pr($datas); die;

		return (!empty($datas) ? $datas : NULL);
	}

	public function ProjectBoardProjectListNew($receiver, $sender) {
		$user_id = $this->Session->read('Auth.User.id');
		//$datas = ClassRegistry::init('ProjectBoard')->find('all', array('conditions' => array('ProjectBoard.receiver' => $receiver, 'ProjectBoard.sender' => $sender)));

		$query = "select
						user_permissions.user_id,ProjectBoard.*, Project.*
				from user_permissions

					left join projects as Project on
						Project.id = user_permissions.project_id

					inner join project_boards as ProjectBoard on
						ProjectBoard.project_id = user_permissions.project_id

					inner join users as User on
						User.id = ProjectBoard.sender

					inner join user_details as UserDetail on
						UserDetail.user_id = User.id



					where user_permissions.role in('Owner','Group Owner','Creator') and  workspace_id is null and user_permissions.user_id = $user_id and ProjectBoard.sender = $sender  group by ProjectBoard.project_id order by ProjectBoard.id desc ";

		//echo $query;
		$data = ClassRegistry::init('UserPermission')->query($query);

		return ( isset($data) && !empty($data[0]) ) ? $data : array();
	}

	public function get_profile_pic($id = null) {
		App::import("Model", "UserDetail");
		$profile = new UserDetail();
		$data = $profile->findByUserId($id);
		return isset($data['UserDetail']['profile_pic']) && !empty($data['UserDetail']['profile_pic']) ? SITEURL . 'uploads/user_images/' . $data['UserDetail']['profile_pic'] : SITEURL . 'img/image_placeholders/logo_placeholder.gif';
	}

	public function checkProjectBoard($project_id = null, $user_id = null) {
		App::import("Model", "ProjectBoard");
		$projectboard = new ProjectBoard();

		$data = $projectboard->find('all', array('conditions' => array('project_id' => $project_id, 'receiver' => $user_id, 'project_status' => array(0, 1, 2), 'sender' => $this->Session->read('Auth.User.id'))));

		return $data;
	}

	public function checkProjectBoardStatus($project_id = null) {
		App::import("Model", "ProjectBoard");
		$projectboard = new ProjectBoard();

		$data = $projectboard->find('count', array('conditions' => array('ProjectBoard.project_id' => $project_id, 'ProjectBoard.project_status' => 0)));

		return $data;
	}

	public function get_skill_of_project($project_id = null) {
		$data = null;
		if (isset($project_id) && !empty($project_id)) {
			$data = ClassRegistry::init('ProjectSkill')->find('all', array("fields" => array("ProjectSkill.skill_id"), 'conditions' => array('ProjectSkill.project_id' => $project_id)));
		}
		return $data;
	}

	public function get_subject_of_project($project_id = null) {
		$data = null;
		if (isset($project_id) && !empty($project_id)) {
			$data = ClassRegistry::init('ProjectSubject')->find('all', array("fields" => array("ProjectSubject.subject_id"), 'conditions' => array('ProjectSubject.project_id' => $project_id)));
		}
		return $data;
	}

	public function get_domain_of_project($project_id = null) {
		$data = null;
		if (isset($project_id) && !empty($project_id)) {
			$data = ClassRegistry::init('ProjectDomain')->find('all', array("fields" => array("ProjectDomain.domain_id"), 'conditions' => array('ProjectDomain.project_id' => $project_id)));
		}
		return $data;
	}

	public function get_ProjData($project_id = null) {
		$data = array();
		if(!is_null($project_id)) {
			//$user_ids = implode(',', $user_ids);

			$query = "SELECT projects.id,
					GROUP_CONCAT(DISTINCT( CONCAT(skills.title,',',skills.id)) ORDER BY skills.title SEPARATOR ';') as proj_skills,
					GROUP_CONCAT(DISTINCT( CONCAT(subjects.title,',',subjects.id)) ORDER BY subjects.title SEPARATOR ';') as proj_subjects,
					GROUP_CONCAT(DISTINCT( CONCAT(dom1.title,',',dom1.id)) ORDER BY dom1.title SEPARATOR ';') as proj_domains

					FROM projects

					LEFT JOIN project_skills
						ON projects.id = project_skills.project_id

					LEFT JOIN project_subjects
						ON projects.id = project_subjects.project_id

					LEFT JOIN project_domains
						ON projects.id = project_domains.project_id

					LEFT JOIN skills
						ON skills.id = project_skills.skill_id

					LEFT JOIN subjects
						ON project_subjects.subject_id = subjects.id

					LEFT JOIN knowledge_domains dom1
						ON project_domains.domain_id = dom1.id

					WHERE projects.id = $project_id

					GROUP BY projects.id

					";
			$data = ClassRegistry::init('Project')->query($query);
		}
		return $data;
	}

	public function get_skillsName($skill_id = null) {
		$data = null;
		if (isset($skill_id) && !empty($skill_id) && !is_null($skill_id)) {
			$data = ClassRegistry::init('Skill')->find('all', array('fields' => array('id', 'title'), 'conditions' => array('id' => $skill_id)));
		}
		return $data;
	}

	public function get_skill_of_users($user_ids = null) {
		$data = null;
		if (isset($user_ids) && !empty($user_ids) && !is_null($user_ids)) {
			$user_ids = implode(',', $user_ids);
			$getSkillQuery = "SELECT user_skills.skill_id, skills.title FROM user_skills LEFT JOIN skills ON user_skills.skill_id = skills.id WHERE user_skills.user_id IN ($user_ids) AND skills.title IS NOT NULL GROUP BY user_skills.skill_id ORDER BY skills.title ASC";
			$data = ClassRegistry::init('UserSkill')->query($getSkillQuery);
		}
		return $data;
	}

	public function get_subject_of_users($user_ids = null) {
		$data = null;
		if (isset($user_ids) && !empty($user_ids) && !is_null($user_ids)) {
			$user_ids = implode(',', $user_ids);
			$getSubjectQuery = "SELECT user_subjects.subject_id, subjects.title FROM user_subjects LEFT JOIN subjects ON user_subjects.subject_id = subjects.id WHERE user_subjects.user_id IN ($user_ids) GROUP BY user_subjects.subject_id ORDER BY subjects.title ASC";
			$data = ClassRegistry::init('UserSubject')->query($getSubjectQuery);
		}
		return $data;
	}

	public function get_domain_of_users($user_ids = null) {
		$data = null;
		if (isset($user_ids) && !empty($user_ids) && !is_null($user_ids)) {
			$user_ids = implode(',', $user_ids);
			$getDomainQuery = "SELECT user_domains.domain_id, knowledge_domains.title FROM user_domains LEFT JOIN knowledge_domains ON user_domains.domain_id = knowledge_domains.id WHERE user_domains.user_id IN ($user_ids) GROUP BY user_domains.domain_id ORDER BY knowledge_domains.title ASC";
			$data = ClassRegistry::init('UserDomain')->query($getDomainQuery);
		}
		return $data;
	}

	public function shared_projects($user_id = null, $share_by_id = null) {
		$data = null;
		if ((isset($user_id) && !empty($user_id)) && (isset($share_by_id) && !empty($share_by_id))) {

			App::import("Model", "ProjectPermission");
			$project_permission = new ProjectPermission();
			App::import("Model", "UserProject");
			$user_project = new UserProject();

			$conditions = null;
			$conditions['UserProject.status'] = 1;
			$conditions['UserProject.project_id !='] = '';

			$conditionsN = null;
			$conditionsN['ProjectPermission.user_id'] = $user_id;
			$conditionsN['ProjectPermission.share_by_id'] = $share_by_id;

			$projects_shared = $project_permission->find('all', array(
				'conditions' => $conditionsN,
				'fields' => array('ProjectPermission.user_project_id'),
				'order' => 'ProjectPermission.created DESC',
				'recursive' => -1,
			));

			foreach ($projects_shared as $sshare) {
				$idms[] = $sshare['ProjectPermission']['user_project_id'];
			}

			if (isset($idms) && !empty($idms)) {
				$conditions['UserProject.id'] = $idms;
			}

			$data = $user_project->find('all', array(
				'joins' => array(
					array(
						'table' => 'projects',
						'alias' => 'Projects',
						'type' => 'INNER',
						'conditions' => array(
							'UserProject.project_id = Projects.id',
						),
					),
				),
				'conditions' => $conditions,
				'fields' => array('UserProject.*', 'Projects.*'),
				'order' => 'UserProject.modified DESC',
				'group' => ['UserProject.project_id'],
				'recursive' => 1,
			));
		}
		return $data;
	}

	public function OwnerProjectElement($pid, $element_id) {
		$view = new View();
		$common = $view->loadHelper('Common');
		$owner = $common->userprojectOwner($pid);

		$Owners = array();

		$participantsOwners = array_filter(participants_owners($pid, $owner));

		$Owners = array_merge($Owners, $participantsOwners);

		$participantsGpOwners = array_filter(participants_group_owner($pid));
		$Owners = array_merge($Owners, $participantsGpOwners);

		$elementusers = $common->element_sharers($element_id, $pid, 1);
		$Owners = array_merge($Owners, $elementusers);

		return array_unique($Owners);
	}

	public function element_group_shared($element_id = null) {
		$data = null;
		if (isset($element_id) && !empty($element_id)) {
			$data = ClassRegistry::init('ElementPermission')->find('all', array("fields" => array("ElementPermission.project_group_id"), 'conditions' => array('ElementPermission.element_id' => $element_id, 'ElementPermission.is_editable !=' => 1, 'ElementPermission.project_group_id !=' => '')));
		}

		if (isset($data) && !empty($data)) {
			foreach ($data as $da) {
				$dat[] = $da['ElementPermission']['project_group_id'];
			}
		}

		return $dat;
	}

	public function ProjectElementAllUsers($pid, $element_id) {
		$view = new View();
		$common = $view->loadHelper('Common');
		$group = $view->loadHelper('Group');
		$owner = $common->userprojectOwner($pid);

		$Owners = array();

		$participantsOwners = participants_owners($pid, $owner);

		if ((isset($participantsOwners) && !empty($participantsOwners))) {
			$participantsOwners = array_filter($participantsOwners);

			$Owners = array_merge($Owners, $participantsOwners);
		}

		$participantsGpOwners = participants_group_owner($pid);
		if ((isset($participantsGpOwners) && !empty($participantsGpOwners))) {
			$participantsGpOwners = array_filter($participantsGpOwners);
			$Owners = array_merge($Owners, $participantsGpOwners);
		}

		/* $participantsSharer = participants_group_sharer($pid);
			          if((isset($participantsSharer) && !empty($participantsSharer)) ){
			          $participantsSharer = array_filter($participantsSharer);
			          $Owners = array_merge($Owners,$participantsSharer);
		*/

		$elementusers = $common->element_sharers($element_id, $pid);

		$elementGroup = $common->element_group_shared($element_id);

		if (isset($elementGroup) && !empty($elementGroup)) {
			foreach ($elementGroup as $gid) {
				$participantsSharer = $group->group_users($gid);
				if ((isset($participantsSharer) && !empty($participantsSharer))) {
					$participantsSharer = array_filter($participantsSharer);
					if ((isset($participantsSharer) && !empty($participantsSharer))) {
						$participantsSharer = array_filter($participantsSharer);
						$Owners = array_merge($Owners, $participantsSharer);
					}
				}
			}
		}

		if ((isset($Owners) && !empty($Owners)) && (isset($elementusers) && !empty($elementusers))) {
			$Owners = array_merge($Owners, $elementusers);
		}

		return array_unique($Owners);
	}

	public function VoteTypeDescription($vote_type = null) {

		switch ($vote_type) {
		case 1:
			return 'Vote with Yes or No.';
			break;
		case 2:
			return 'Vote using a 1 – 10 scale.';
			break;
		case 3:
			return 'Vote with Yes, Maybe, Don’t Know or No.';
			break;
		case 4:
			return 'Create your own option list. Vote for one option from the list.';
			break;
		case 5:
			return 'Create your own option list. Each option can receive up to 10 votes.';
			break;
		case 6:
			return 'Create your own option list. Allocate up to 10 votes that can be cast across all options.';
			break;
		default:
			break;
		}
	}

	public function VoteTypeDescriptionForRequest($vote_type = null) {

		switch ($vote_type) {
		case 1:
			return 'Vote with Yes or No.';
			break;
		case 2:
			return 'Vote using a 1 – 10 scale.';
			break;
		case 3:
			return 'Vote with Yes, Maybe, Don’t Know or No.';
			break;
		case 4:
			return 'Vote for one option from the list.';
			break;
		case 5:
			return 'Each option can receive up to 10 votes.';
			break;
		case 6:
			return 'Total given votes that can be cast across all options.';
			break;
		default:
			break;
		}
	}

	public function getOrganisationId($user_id = null, $role_id = null) {

		if (isset($role_id) && !empty($role_id) && $role_id == 3) {
			return $user_id;
		} else {
			App::import("Model", "UserDetail");
			$UserDetail = new UserDetail();
			$data = $UserDetail->find('first', array('conditions' => array('UserDetail.user_id' => $user_id)));
			return $data['UserDetail']['org_id'];
		}
	}

	public function getPostDetails($postid = null) {

		if (isset($postid) && !empty($postid)) {

			App::import("Model", "Post");
			$postDetail = new Post();
			//$data = $postDetail->find('first',array('conditions'=>array('Post.id'=>$postid)));
			$data = $postDetail->find('first', array('conditions' => array('Post.slug' => $postid)));
			return $data;
		}
	}

	public function userElements($userID = null, $element_ids = null) {

		if (isset($userID) && !empty($userID)) {

			App::import("Model", "ElementPermission");
			$elements = new ElementPermission();

			$data = $elements->find('all', array('conditions' => array('Element.id IS NOT NULL', 'ElementPermission.user_id' => $userID, 'ElementPermission.element_id' => $element_ids), 'fields' => array('id', 'element_id'), 'group' => 'ElementPermission.element_id'));

			return $data;
		}
	}

	public function userElementPermissions($userID = null, $element_ids = null) {

		if (isset($userID) && !empty($userID)) {

			App::import("Model", "ElementPermission");
			$elements = new ElementPermission();

			$data = $elements->find('all', array('conditions' => array('Element.id IS NOT NULL', 'ElementPermission.user_id' => $userID, 'ElementPermission.element_id' => $element_ids), 'fields' => array('id', 'element_id'), 'group' => 'ElementPermission.element_id'));

			return $data;
		}
	}

	public function getRAG($project_id = null, $numbers = false) {
		App::import("Model", "Project");
		App::import("Model", "ProjectRag");
		$this->Project = new Project();
		$this->ProjectRag = new ProjectRag();

		$project_data = $this->Project->find('first', array('recursive' => -1, 'conditions' => array('Project.id' => $project_id)));
		$project_rag = $this->ProjectRag->find('first', array('recursive' => 1, 'conditions' => array('ProjectRag.project_id' => $project_id)));

		$elements_overdue = $this->ViewModel->elements_overdue($project_id);
		$project_elements = $this->ViewModel->project_elements($project_id);
		$percent = 0;

		$elements_overdue = (isset($elements_overdue) && !empty($elements_overdue)) ? $elements_overdue : array();

		$project_elements = (isset($project_elements) && !empty($project_elements)) ? $project_elements : array();

		if( !empty($elements_overdue) && !empty($project_elements) ){
			if ((count($elements_overdue)) > 0 && (count($project_elements)) > 0) {
				$percent = round((count($elements_overdue)) / (count($project_elements)) * 100);
			}
		}

		if (isset($project_data['Project']) && !empty($project_data['Project'])) {

			$rag_status = $project_data['Project']['rag_status'];

		}

		$amber_value = $red_value = 0;
		if (isset($project_rag['ProjectRag']) && !empty($project_rag['ProjectRag'])) {
			$amber_value = $project_rag['ProjectRag']['amber_value'];
			$red_value = $project_rag['ProjectRag']['red_value'];
		}

		if (isset($rag_status) && $rag_status == 1) {
			$rag_color = (isset($numbers) && $numbers == true) ? 1 : 'bg-red';
		} else if (isset($rag_status) && $rag_status == 2) {
			$rag_color = (isset($numbers) && $numbers == true) ? 2 : 'bg-yellow';
		} else if (isset($rag_status) && $rag_status == 3) {
			$rag_color = (isset($numbers) && $numbers == true) ? 3 : 'bg-green';

			if (($amber_value > 0 && $red_value > 0) && $percent >= $amber_value && $percent < $red_value) {
				$rag_color = (isset($numbers) && $numbers == true) ? 2 : 'bg-yellow';
			}
			else if (($amber_value > 0 && $red_value > 0) && ($percent >= $red_value && $percent <= 100)) {
				$rag_color = (isset($numbers) && $numbers == true) ? 1 : 'bg-red';

			}

			if (((!isset($amber_value) || $amber_value == '' || $amber_value == 0) && $red_value > 0) && ($percent >= $red_value && $percent <= 100)) {
				$rag_color = (isset($numbers) && $numbers == true) ? 1 : 'bg-red';

			}

			if (((!isset($red_value) || $red_value == '' || $red_value == 0) && $amber_value > 0) && ($percent >= $amber_value && $percent <= 100)) {

				$rag_color = (isset($numbers) && $numbers == true) ? 2 : 'bg-yellow';
			}
		}
		$rag_color = (isset($rag_color) && !empty($rag_color)) ? $rag_color : '';

		return ['rag_color' => $rag_color, 'percent' => $percent];
	}

	public function getEmailDomainUser($domain_id = null) {
		/*  App::import("Model", "Project");
        $this->Project = new Project(); */
		$users = ClassRegistry::init('User')->find('count', array('conditions' => array('managedomain_id' => $domain_id, "User.id !=" => $this->Session->read('Auth.User.id'))));
		return isset($users) ? $users : '0';
	}

	public function getclientEmailDomainUser($subdomain = null, $edomain_id = null) {

		App::import("Model", "OrgSettingJeera");
		$OrgSettingJeera = new OrgSettingJeera();

		App::import("Model", "OrgUserSettingJeera");
		$OrgUserSettingJeera = new OrgUserSettingJeera();

		$domainlists = $OrgUserSettingJeera->find('first', array('conditions' => array('OrgSetting.subdomain =' => $subdomain)));

		if (isset($domainlists) && !empty($domainlists)) {

			//$this->conn = mysql_pconnect(root_host, $domainlists['OrgSetting']['dbuser'], $domainlists['OrgSetting']['dbpass']);
			//mysql_select_db($domainlists['OrgSetting']['dbname']);

			$con = mysqli_connect(root_host, $domainlists['OrgSetting']['dbuser'], $domainlists['OrgSetting']['dbpass'], $domainlists['OrgSetting']['dbname']);

			if (mysqli_connect_errno()) {
				echo "Failed to connect to MySQL: " . mysqli_connect_error();
			}

			$email_domains = mysqli_query($con,"SELECT * FROM users WHERE managedomain_id = '" . $edomain_id . "' ");

			if (mysqli_num_rows($email_domains) > 0) {
				return mysqli_num_rows($email_domains);
			} else {
				return 0;
			}

		}

	}

	public function get_color_code($slug = null) {
		/*  App::import("Model", "Project");
        $this->Project = new Project(); */

		$old = ClassRegistry::init('AdminSetting')->findBySlug($slug);
		return isset($old['AdminSetting']['color_code']) && !empty($old['AdminSetting']['color_code']) ? $old['AdminSetting']['color_code'] : 'bg-default';
	}

	public function totalDataS($model) {

		$pending = ClassRegistry::init($model)->find('count');
		return $pending;
	}

	public function totalActiveSkill($model) {

		$pending = ClassRegistry::init($model)->find('count', array(
			'conditions' => array($model . ".status" => '1'),
		));

		return $pending;
	}

	public function totalInactiveEmail($model) {

		$pending = ClassRegistry::init($model)->find('count', array(
			'conditions' => array($model . ".create_account" => '0'),
		));

		return $pending;
	}

	public function totalActiveEmail($model) {

		$pending = ClassRegistry::init($model)->find('count', array(
			'conditions' => array($model . ".create_account" => '1'),
		));

		return $pending;
	}

	public function totalInactiveSkill($model) {

		$pending = ClassRegistry::init($model)->find('count', array(
			'conditions' => array($model . ".status" => '0'),
		));
		return $pending;
	}

	public function checkOrgEmailDomain($user_id = null) {

		$pending = ClassRegistry::init('ManageDomain')->find('count', array(
			'conditions' => array("ManageDomain.create_account" => '1', 'ManageDomain.user_id' => $user_id),
		));
		return $pending;

	}

	public function countOrgEmailDomain($org_id = null) {

		$emailcnt = ClassRegistry::init('ManageDomain')->find('count', array(
			'conditions' => array("ManageDomain.org_id" => 1),
		));
		return $emailcnt;

	}

	public function getOrgEmailDomain($user_id = null) {

		$result = ClassRegistry::init('ManageDomain')->find('list', array(
			'conditions' => array("ManageDomain.create_account" => '1'), 'fields' => array('id', 'domain_name'),
		));
		return $result;

	}

	public function checkEmailDomainUser($domainName = null) {

		$response = false;
		if (isset($domainName) && !empty($domainName)) {
			$allusers = ClassRegistry::init('User')->find('all', array('conditions' => array('User.role_id !=' => 3), 'fields' => array('id', 'email')));

			if (isset($allusers) && !empty($allusers) && count($allusers) > 0) {
				$domainCount = 0;
				foreach ($allusers as $val) {

					$getDomain = explode("@", $val['User']['email']);
					if (isset($getDomain[1]) && $getDomain[1] == $domainName) {
						$domainCount++;
					}

				}
				if ($domainCount > 0) {
					$response = true;
				}
			}

		} else {
			$response = false;
		}
		return $response;
		die;
	}

	public function orgdomainslist($user_id = null, $type = 'count') {

		if ($type == 'count') {

			$domainlist = ClassRegistry::init('OrgSetting')->find('count', array(
				'conditions' => array("OrgSetting.user_id" => $user_id),
			));

		} else {

			$domainlist = ClassRegistry::init('OrgSetting')->find('list', array(
				'conditions' => array("OrgSetting.user_id" => $user_id),
				'fields' => array('id', 'subdomain'),
				'order' => array('OrgSetting.subdomain ASC'),
			));

		}

		return $domainlist;

	}

	public function orgDetails() {

		$data = ClassRegistry::init('User')->find('first', array('conditions' => array('User.role_id' => 3)));

		if (isset($data) && !empty($data['UserDetail']['org_name'])) {
			return $data['UserDetail']['org_name'];
		} else {
			return 'IdeasCast';
		}

	}

	public function getElementCostLast($element_id = null, $flag = 1) {

		$data = ClassRegistry::init('ElementCost')->find('first', array(
			'conditions' => array('ElementCost.element_id' => $element_id, 'ElementCost.estimate_spend_flag' => $flag),
			'order' => 'ElementCost.id DESC')
		);
		//'ElementCost.active' => 1

		if (isset($data) && !empty($data)) {
			return $data;
		} else {
			return array();
		}

	}

	public function getElementCostComment($element_id = null, $cost_type = null) {

		$data = ClassRegistry::init('ElementCostComment')->find('count', array(
			'conditions' => array('ElementCostComment.element_id' => $element_id, 'ElementCostComment.cost_type' => $cost_type),
		));

		if (isset($data) && $data > 0) {
			return $data;
		} else {
			return 0;
		}

	}

	public function getProjectCostComment($project_id = null, $cost_type = null) {

		$data = ClassRegistry::init('ProjectCostComment')->find('count', array(
			'conditions' => array('ProjectCostComment.project_id' => $project_id, 'ProjectCostComment.cost_type' => $cost_type),
		));

		if (isset($data) && $data > 0) {
			return $data;
		} else {
			return 0;
		}

	}

	public function getWorkspaceCostComment($workspace_id = null, $cost_type = null) {

		$data = ClassRegistry::init('WorkspaceCostComment')->find('count', array(
			'conditions' => array('WorkspaceCostComment.workspace_id' => $workspace_id, 'WorkspaceCostComment.cost_type' => $cost_type),
		));

		if (isset($data) && $data > 0) {
			return $data;
		} else {
			return 0;
		}

	}

	public function element_cost_history($element_id = null) {

		$data = array();
		if (isset($element_id) && !empty($element_id)) {

			$data = ClassRegistry::init('ElementCost')->find('all', array(
				'conditions' => array('ElementCost.element_id' => $element_id),
				'order' => 'ElementCost.id DESC',
				'recursive' => -1,
			));
		}

		return isset($data) ? $data : array();
	}

	public function element_dependencies_crictial($user_id = null, $element_id = null) {

		$data = array();
		if (isset($user_id) && !empty($user_id)) {

			$data = ClassRegistry::init('ElementDependency')->find('first', array(
				'conditions' => array('ElementDependency.element_id' => $element_id),
			));
		}

		return isset($data) ? $data : array();
	}

	public function element_dependencies_gated($user_id = null, $element_id = null) {

		$response['success'] = false;
		$response['element'] = null;
		if (isset($element_id) && !empty($element_id)) {

		$joins = array(
			array(
				'table' => 'element_dependencies',
				'alias' => 'ElementDependency',
				'type' => 'INNER',
				'conditions' => array(
					'ElementDependancyRelationship.element_dependancy_id = ElementDependency.id',

				),
			),
		);

		$data = ClassRegistry::init('ElementDependancyRelationship')->find('all', array(
					'joins' => $joins,
					'conditions' =>
					array(
						'ElementDependency.element_id' => $element_id,
						'ElementDependancyRelationship.is_gated'=>1
					),
					'fields'=>array('ElementDependancyRelationship.element_id')
			));
		}

		if( isset($data) && !empty($data) ){
			$elementids = Set::extract($data, '/ElementDependancyRelationship/element_id');
			if( isset($elementids) && !empty($elementids) ) {
				$totalElements = count($elementids);
				$checkedElement = ClassRegistry::init('Element')->find('count', array('conditions'=>array('Element.id'=>$elementids,'Element.sign_off'=>1) )  );

				if( isset($checkedElement) && $checkedElement == $totalElements ){
					$response['success'] = true;
					$response['element'] = $elementids;
				} else {
					$response['success'] = false;
					$response['element'] = $elementids;
				}
			}
		}
		return $response;
	}

	public function element_dependencies_relationship($relationship_id = null, $element_id = null) {

		$data = array();
		if (isset($relationship_id) && !empty($relationship_id)) {

			$data = ClassRegistry::init('ElementDependancyRelationship')->find('first', array(
				'conditions' => array('ElementDependancyRelationship.element_dependancy_id' => $relationship_id, 'ElementDependancyRelationship.element_id' => $element_id),
			));
		}

		return isset($data) ? $data : array();
	}

	public function used_dependency_elements($element_dependancy_id = null, $element_id = null, $listrow = true) {

		$data = $elementids = array();

		if (isset($listrow) && $listrow == true) {

			$elementids = ClassRegistry::init('ElementDependancyRelationship')->find('list', array(
				'conditions' => array('ElementDependancyRelationship.element_dependancy_id' => $element_dependancy_id, 'ElementDependancyRelationship.element_id !=' => $element_id), 'fields' => array('ElementDependancyRelationship.id', 'ElementDependancyRelationship.element_id'),
			));
			return isset($elementids) ? $elementids : array();
		}

		//pr($elementids); die;
		if (isset($element_dependancy_id) && !empty($element_dependancy_id)) {

			$data = ClassRegistry::init('ElementDependancyRelationship')->find('all', array(
				'conditions' => array('ElementDependancyRelationship.element_dependancy_id' => $element_dependancy_id, 'ElementDependancyRelationship.element_id !=' => $element_id),
			));
			return isset($data) ? $data : array();
		}
	}

	public function element_dependencies($element_dependancy_id = null, $element_id = null) {

		$data = array();
		if (isset($element_dependancy_id) && !empty($element_dependancy_id)) {

			$data = ClassRegistry::init('ElementDependancyRelationship')->find('first', array(
				'conditions' => array('ElementDependancyRelationship.element_dependancy_id' => $element_dependancy_id, 'ElementDependancyRelationship.element_id' => $element_id),
			));
		}

		return isset($data) ? $data : array();
	}

	public function critical_status($element_id = null) {
		$response = '';
		if (isset($element_id) && !empty($element_id)) {

			$data = ClassRegistry::init('ElementDependency')->find('first', array(
				'conditions' => array('ElementDependency.element_id' => $element_id),
				'fields' => array('ElementDependency.is_critical'),
			));

			if (isset($data)) {
				$response = (isset($data['ElementDependency']['is_critical']) && $data['ElementDependency']['is_critical'] == 1) ? 1 : 0;
			}
		}
		return $response;
	}

	public function dependancy_status($element_id = null, $gated = null ) {
		$response = '';

		if (isset($element_id) && !empty($element_id)) {

			$data = ClassRegistry::init('ElementDependency')->find('first', array(
				'conditions' => array('ElementDependency.element_id' => $element_id),
				'fields' => array('ElementDependency.id'),
			));
			if (isset($data) && !empty($data)) {

				if( isset($gated) && !empty($gated) ){

					$predessor = ClassRegistry::init('ElementDependancyRelationship')->find('count', array('conditions' => array(
						'ElementDependancyRelationship.element_dependancy_id' => $data['ElementDependency']['id'], 'ElementDependancyRelationship.dependency' => 1,'ElementDependancyRelationship.is_gated' => 1
					),
					));
					$successor = ClassRegistry::init('ElementDependancyRelationship')->find('count', array('conditions' => array(
						'ElementDependancyRelationship.element_dependancy_id' => $data['ElementDependency']['id'], 'ElementDependancyRelationship.dependency' => 2, 'ElementDependancyRelationship.is_gated' => 1
					),
					));

				} else {

					$predessor = ClassRegistry::init('ElementDependancyRelationship')->find('count', array('conditions' => array(
						'ElementDependancyRelationship.element_dependancy_id' => $data['ElementDependency']['id'], 'ElementDependancyRelationship.dependency' => 1,
					),
					));
					$successor = ClassRegistry::init('ElementDependancyRelationship')->find('count', array('conditions' => array(
						'ElementDependancyRelationship.element_dependancy_id' => $data['ElementDependency']['id'], 'ElementDependancyRelationship.dependency' => 2,
					),
					));
				}


				if ((isset($predessor) && isset($successor)) && $predessor > 0 && $successor > 0) {
					$response = 'both';
				} else if (isset($predessor) && $predessor > 0) {
					$response = 'predessor';
				} else if (isset($successor) && $successor > 0) {
					$response = 'successor';
				} else {
					$response = 'none';
				}
			}
		}
		return $response;
	}

	public function ele_dependency_count($element_id = null, $deptype = null) {

		$elementlist = ClassRegistry::init('ElementDependency')->find('first', array(
			'conditions' => array('ElementDependency.element_id' => $element_id),
		));

		$depCount = 0;
		$i = 0;
		if (isset($elementlist) && !empty($elementlist)) {
			foreach ($elementlist['ElementDependancyRelationship'] as $listelement) {
				if ($listelement['dependency'] == $deptype) {
					$i++;
					$depCount = $i;
				}
			}
		}
		return $depCount;

	}

	public function element_dependancy_list($element_id = null) {
		$response = '';
		if (isset($element_id) && !empty($element_id)) {

			$data = ClassRegistry::init('ElementDependency')->find('all', array(
				'conditions' => array('ElementDependency.element_id' => $element_id),
			));


			if (isset($data) && !empty($data)) {
				$response = (isset($data[0]['ElementDependancyRelationship']) && !empty($data[0]['ElementDependancyRelationship'])) ? $data[0]['ElementDependancyRelationship'] : array();
			}
		}
		return $response;
	}

	public function element_criticalStaus($element_id = null) {
		$response = '';
		if (isset($element_id) && !empty($element_id)) {

			$data = ClassRegistry::init('ElementDependency')->find('all', array(
				'conditions' => array('ElementDependency.element_id' => $element_id),
			));

			if (isset($data) && !empty($data)) {
				$response = (isset($data[0]) && !empty($data[0])) ? $data[0] : array();
			}
		}
		return $response;
	}

/***********************
 * Program Start
 ************************/

	public function program_lists() {
		$response = '';

		$data = ClassRegistry::init('Program')->find('list', array('conditions' => array('Program.user_id' => $this->Session->read('Auth.User.id')), 'fields' => array('Program.id', 'Program.program_name'), 'order' => 'Program.program_name ASC'));

		$newData = array();
		if (isset($data) && !empty($data)) {

			foreach ($data as $key => $pgid) {
				$newData[$key] = $pgid . " (" . $this->program_project_count($key) . ")";
			}
			$response = (isset($newData) && !empty($newData)) ? $newData : array();
		}

		return $response;
	}

	public function program_project_count($program_id = null, $count = true) {
		$response = 0;
		$filter_type = 'count';
		if (!$count) {
			$filter_type = 'all';
		}

		$programProjectCount = ClassRegistry::init('ProjectProgram')->find($filter_type, array('conditions' => array('ProjectProgram.program_id' => $program_id)));

		if (isset($programProjectCount) && !empty($programProjectCount)) {
			$response = $programProjectCount;
		}

		return $response;
	}

	public function list_program_update($orderby = 'program_name') {
		$response = '';

		$data = ClassRegistry::init('Program')->find('list', array('conditions' => array('Program.user_id' => $this->Session->read('Auth.User.id')), 'fields' => array('Program.id', 'Program.program_name'), 'order' => 'Program.' . $orderby . ' ASC'));

		if (isset($data) && !empty($data)) {
			$response = (isset($data) && !empty($data)) ? $data : array();
		}

		return $response;
	}

/***********************
 * Program End
 ************************/

	function check_notification($user_id = null, $notification_type = null, $personlization = null) {

		return ClassRegistry::init('EmailNotification')->find('first', array('conditions' => array('EmailNotification.notification_type' => $notification_type, 'EmailNotification.personlization' => $personlization, 'EmailNotification.user_id' => $user_id)));

	}

	/* public function jeeraversionold(){

		$ideaVersion = ClassRegistry::init('Setting')->find('first', array('conditions'=>array('Setting.id'=>1)));

		if( isset($ideaVersion['Setting']['idesversion']) && !empty($ideaVersion['Setting']['idesversion']) ){
			return $ideaVersion['Setting']['idesversion'];
		} else {
			return '1.0';
		}

	} */

	public function jeeraversion() {

		if( PHP_VERSIONS == 5 ){

			if ($_SERVER['SERVER_NAME'] != "prod.ideascast.com" && $_SERVER['SERVER_NAME'] != "jeera.ideascast.com" && $_SERVER['SERVER_NAME'] != LOCALIP) {

				$link = mysql_connect(root_host, root_dbuser, root_dbpass);

				if (!$link) {
					return '1.0';
				} else {
					// $dbselect = mysql_select_db('ideascas_stagingdev', $link);
					$dbselect = mysql_select_db(root_dbname, $link);

					$sql = mysql_query("SELECT idesversion FROM settings where id = 1 ");
					$resutlDomain = mysql_fetch_array($sql);

					if (isset($resutlDomain['idesversion']) && !empty($resutlDomain['idesversion'])) {
						return $resutlDomain['idesversion'];
					} else {
						return '1.0';
					}
				}
			} else {
				return '1.0';
			}
		} else {

			$link = mysqli_connect(root_host, root_dbuser, root_dbpass,root_dbname);

			if (!$link) {
				return '1.0';
			} else {
				// $dbselect = mysql_select_db('ideascas_stagingdev', $link);
				//$dbselect = mysql_select_db(root_dbname, $link);

				$sql = mysqli_query($link, "SELECT idesversion FROM settings where id = 1 ");
				$resutlDomain = mysqli_fetch_array($sql);
				if (isset($resutlDomain['idesversion']) && !empty($resutlDomain['idesversion'])) {
					return $resutlDomain['idesversion'];
				} else {
					return '1.0';
				}
			}
		}

	}

	public function jeeraversion_main() {

		$link = mysqli_connect(root_host, root_dbuser, root_dbpass, root_dbname);

		if (mysqli_connect_errno()) {
			//echo "Failed to connect to MySQL: " . mysqli_connect_error();
			return '1.0';
		} else {
			//$dbselect = mysql_select_db(root_dbname, $link);

			$sql = mysqli_query($link,"SELECT idesversion FROM settings where id = 1 ");
			$resutlDomain = mysqli_fetch_array($sql);

			if (isset($resutlDomain['idesversion']) && !empty($resutlDomain['idesversion'])) {
				return $resutlDomain['idesversion'];
			} else {
				return '1.0';
			}
		}

	}

	function domain_list() {

		$whatINeed = explode(DOMAIN_PREFIX, $_SERVER['HTTP_HOST']);
		$whatINeed = $whatINeed[0];

		App::import("Model", "OrgSettingJeera");
		$OrgSettingJeera = new OrgSettingJeera();

		App::import("Model", "OrgUserSettingJeera");
		$OrgUserSettingJeera = new OrgUserSettingJeera();

		$result = $OrgSettingJeera->find('first', array('conditions' => array('OrgSettingJeera.domain_name' => $whatINeed)));

		$domainlists = $OrgUserSettingJeera->find('first', array('conditions' => array('OrgSetting.prmry_sts' => 1, 'OrgSetting.subdomain' => $whatINeed)));

		if (isset($domainlists['OrgSetting']) && !empty(isset($domainlists['OrgSetting']))) {
			return true;
		} else {
			return false;
		}
	}

	//overdue
	public function overdue_element_count($id) {

		App::import("Model", "Element");
		$ele = new Element();

		$data = $ele->find('first', ['conditions' => [
			'Element.id' => $id,
			'studio_status !=' => 1,
		],
			'recursive' => -1,
		]);
		// pr($data, 1);

		$status = STATUS_NOT_SPACIFIED;

		if ((isset($data['Element']['start_date']) && !empty($data['Element']['start_date'])) && (isset($data['Element']['end_date']) && !empty($data['Element']['end_date']))) {

			if (((isset($data['Element']['start_date']) && !empty($data['Element']['start_date'])) && date('Y-m-d', strtotime($data['Element']['start_date'])) > date('Y-m-d')) && $data['Element']['sign_off'] != 1) {
				$status = STATUS_NOT_STARTED;
			} else if (((isset($data['Element']['end_date']) && !empty($data['Element']['end_date'])) && date('Y-m-d', strtotime($data['Element']['end_date'])) < date('Y-m-d')) && $data['Element']['sign_off'] != 1) {
				$status = STATUS_OVERDUE;
			} else if (isset($data['Element']['sign_off']) && !empty($data['Element']['sign_off']) && $data['Element']['sign_off'] > 0) {
				$status = STATUS_COMPLETED;
			} else if ((((isset($data['Element']['end_date']) && !empty($data['Element']['end_date'])) && (isset($data['Element']['start_date']) && !empty($data['Element']['start_date']))) && (date('Y-m-d', strtotime($data['Element']['start_date'])) <= date('Y-m-d')) && date('Y-m-d', strtotime($data['Element']['end_date'])) >= date('Y-m-d')) && $data['Element']['sign_off'] != 1) {
				$status = STATUS_PROGRESS;
			}
		}
		return $status;
	}

	public function project_element_count($ele_ids, $status = 'STATUS_NOT_SPACIFIED', $project_id = null) {

		App::import("Model", "Element");
		$ele = new Element();

		App::import("Model", "ElementAssignment");
		$es = new ElementAssignment();

		$dateConditions = '';

		if ($status == 'STATUS_NOT_STARTED') {

			$dateConditions = array("Element.sign_off !=" => 1, "Element.start_date > " => date('Y-m-d') . " 12:00:00");

		} else if ($status == 'STATUS_OVERDUE') {

			$dateConditions = array("Element.sign_off !=" => 1, "Element.end_date < " => date('Y-m-d') . " 12:00:00");

		} else if ($status == 'STATUS_COMPLETED') {

			$dateConditions = array("Element.sign_off > " => 0);

		} else if ($status == 'STATUS_PROGRESS') {

			$dateConditions = array("Element.sign_off !=" => 1, "Element.start_date <= " => date('Y-m-d') . " 12:00:00", "Element.end_date >= " => date('Y-m-d'));

		} else if ($status == 'STATUS_NOT_ASSIGNED') {

			$assingElement = $es->find('all',
				array(
					'conditions' => array('ElementAssignment.element_id' => $ele_ids),
				)
			);
			$elementids = Set::extract($assingElement, '/ElementAssignment/element_id');

			$result = array_diff($ele_ids, $elementids);
			return ( isset($result) && !empty($result) ) ? count($result) : 0;

		} else {

			$dateConditions = array("Element.date_constraints !=" => 1);
		}
		//pr($dateConditions);
		$data = $ele->find('count', ['conditions' =>
			[
				'Element.id' => $ele_ids,
				'Element.studio_status != ' => 1,
				$dateConditions,
			],
			'recursive' => -1,
		]);
		//pr($data);die;
		return $data;
	}

	public function usersFullname($ids) {

		$data = ClassRegistry::init('UserDetail')->find('all', array('conditions' => array('UserDetail.user_id' => $ids), 'fields' => array('user_id', 'first_name', 'last_name', 'full_name'), 'order' => 'UserDetail.first_name ASC'));

		return (isset($data) && !empty($data)) ? $data : false;
	}

	public function projectReopen($id, $msg) {

		$data = ClassRegistry::init('ProjectActivity')->find('count', array('conditions' => array('ProjectActivity.project_id' => $id, 'ProjectActivity.message' => $msg)));

		return (isset($data) && !empty($data)) ? $data : false;
	}
	public function WorkspaceReopen($id, $msg) {

		$data = ClassRegistry::init('WorkspaceActivity')->find('count', array('conditions' => array('WorkspaceActivity.workspace_id' => $id, 'WorkspaceActivity.message' => $msg)));

		return (isset($data) && !empty($data)) ? $data : false;
	}

	public function ElementReopen($id, $msg) {
		$data = ClassRegistry::init('Activity')->find('count', array('conditions' => array('Activity.element_id' => $id, 'Activity.message' => $msg)));

		return (isset($data) && !empty($data)) ? $data : false;
	}
	/*========= Below function will use borad module ==============*/
	public function declineReason() {
		$data = ClassRegistry::init('DeclineReason')->find('all');
		return (isset($data) && !empty($data)) ? $data : false;
	}

	public function show_reason($id) {
		$data = ClassRegistry::init('DeclineReason')->findById($id);
		return (isset($data) && !empty($data)) ? $data : false;
	}

	public function board_data($id = null) {
		$data = ClassRegistry::init('ProjectBoard')->find('first', array('conditions' => array('ProjectBoard.id' => $id)));
		return (isset($data) && !empty($data)) ? $data : false;
	}

	public function board_data_by_project_id($project_id = null, $user_id = null) {
		$data = ClassRegistry::init('BoardResponse')->find('first', array('conditions' => array('BoardResponse.project_id' => $project_id, 'BoardResponse.sender_id' => $user_id)));
		return (isset($data) && !empty($data)) ? $data : false;
	}

	public function board_data_by_project_receiver($project_id = null, $user_id = null) {
		$data = ClassRegistry::init('BoardResponse')->find('first', array('conditions' => array('BoardResponse.project_id' => $project_id, 'BoardResponse.receiver_id' => $user_id)));
		return (isset($data) && !empty($data)) ? $data : false;
	}

	public function board_data_by_project_sender($project_id = null, $user_id = null) {
		$data = ClassRegistry::init('BoardResponse')->find('first', array('conditions' => array('BoardResponse.project_id' => $project_id, 'BoardResponse.sender_id' => $user_id)));
		return (isset($data) && !empty($data)) ? $data : false;
	}

	/*================== User skill pdf ======================*/

	public function getSkillPdf($skill_id = null, $user_id = null) {
		$data = ClassRegistry::init('SkillPdf')->find('all', array('conditions' => array('SkillPdf.skill_id' => $skill_id, 'SkillPdf.user_id' => $user_id, 'SkillPdf.upload_status' => 1)));
		return (isset($data) && !empty($data)) ? $data : false;
	}

	public function getUserSkillPdfCount($skill_id = null, $user_id = null) {


		$data = ClassRegistry::init('SkillPdf')->find('all', array('conditions' => array('SkillPdf.skill_id' => $skill_id, 'SkillPdf.user_id' => $user_id )));


		if( isset($data) && !empty($data) ){
			$skillcnt = 0;
			foreach($data as $listpdf){
				if( file_exists(SKILL_PDF_PATH . $user_id . DS . $listpdf['SkillPdf']['pdf_name']) ){
					$skillcnt++;
				}
			}
			return (isset($skillcnt) && !empty($skillcnt)) ? $skillcnt : false;
 		}

	}

	public function boardUpdated($sender_id = null, $receiver_id = null, $project_id = null) {
		$data = ClassRegistry::init('ProjectBoard')->find('first', array('conditions' => array(
			'ProjectBoard.sender' => $sender_id,
			'ProjectBoard.receiver' => $receiver_id,
			'ProjectBoard.project_id' => $project_id,
		),
		)
		);
		return (isset($data) && !empty($data)) ? $data : false;
	}

	public function domain_users($id = null, $domain_id = null) {

		$orgdetails = ClassRegistry::init('OrgSetting')->find('first', array('conditions' => array('OrgSetting.id' => $domain_id)));

		$lincentotals = 0;

		if( $_SERVER['SERVER_ADDR'] ==  OPUSVIEW || $_SERVER['SERVER_ADDR'] ==  OPUSVIEW_DEV ||  $_SERVER['SERVER_ADDR']  ==OPUSVIEW_CLOUD )
		{
			$mysqlUserName = root_dbuser;
			$mysqlPassword = root_dbpass;
		} else {
			$mysqlUserName = $orgdetails['OrgSetting']['dbuser'];
			$mysqlPassword = $orgdetails['OrgSetting']['dbpass'];
		}


		$con = mysqli_connect(root_host, $mysqlUserName, $mysqlPassword, $orgdetails['OrgSetting']['dbname']);

		if (mysqli_connect_error()) {
			echo "Failed to connect to MySQL: " . mysqli_connect_error();
		}

		$userDetails = mysqli_query($con, "SELECT * FROM users WHERE role_id = 2 ");

		if ($con && !empty($userDetails)) {

			if (mysqli_num_rows($userDetails) > 0) {
				$lincentotals = mysqli_num_rows($userDetails);
			}

		}
		return $lincentotals;

	}

	//======================= Get user projects ======================
	public function getUserProjects($user_id, $level, $start_date = null, $end_date = null) {

		$start_date = date('Y-m-d');
		$end_date = date('Y-m-d');

		$current_user_id = $this->Session->read('Auth.User.id');
		App::import('Model', 'Project');
		$Project = new Project;

		if (isset($user_id)) {

			$ownersProjects = $this->ownerProjects($user_id, $level);
			$sharerProjects = $this->ownerProjects($user_id);

			/* if (isset($start_date) && isset($end_date) && !empty($start_date) && !empty($end_date) ) {

				$dateStr = explode("-", $this->request->data['dateStr']);

				$startDate = explode(" ", trim($dateStr[0]));
				$start_date = date('Y-m-d', strtotime(trim($startDate[2]) . '-' . trim($startDate[1]) . '-' . trim($startDate[0])));

				$endDate = explode(" ", trim($dateStr[1]));
				$end_date = date('Y-m-d', strtotime(trim($endDate[2]) . '-' . trim($endDate[1]) . '-' . trim($endDate[0])));

			} */

			if (isset($ownersProjects) && isset($sharerProjects) && !empty($ownersProjects) && !empty($sharerProjects)) {

				/* Common Projects of login user and selected user */
				//$allProjects = array_intersect($ownersProjects, $sharerProjects);

				$allProjects = array_unique(array_merge($ownersProjects, $sharerProjects));

				$between = '';
				if (!empty($start_date) && empty($end_date)) {
					$between .= "AND (date(Project.start_date) BETWEEN '" . $start_date . "' AND '" . $start_date . "') ";
				} else if (empty($start_date) && !empty($end_date)) {
					$between .= "AND (date(Project.end_date) BETWEEN '" . $end_date . "' AND '" . $end_date . "') ";
				} else if (!empty($start_date) && !empty($end_date)) {
					$between .= "AND ( (date(Project.start_date) >= '" . $start_date . "' AND date(Project.end_date) <='" . $end_date . "') ";
					$between .= "OR (date(Project.start_date) <= '" . $start_date . "' AND date(Project.end_date) >='" . $end_date . "')) ";
				}

				$arrayTostr = implode(",", $allProjects);
				$select = "SELECT id,title FROM projects as Project WHERE id IN (" . $arrayTostr . ") " . $between;

				$MyProjects = $Project->query($select);

				$projects_arr = Set::extract($MyProjects, '/Project/id');

				//pr($projects_arr);
				return $projects_arr;
				// return $MyProjects;

				/*
						$data = array();
						$view = new View();
						$TaskCenter = $view->loadHelper('TaskCenter');

						if ($level == 1) {
						$alluserarray = $TaskCenter->userByProject($projects_arr);

						$allusers = array_unique($alluserarray['all_project_user']);
						$allusers = $TaskCenter->user_exists($allusers);
						if (($key = array_search($current_user_id, $allusers)) !== false) {
							unset($allusers[$key]);
						}
						$alluserslist = $this->usersFullname($allusers);
					} else {
						$alluserslist = array();
				*/

			}
		} else {
			return 0;
		}
	}

	public function ownerProjects($user_id, $level = null) {

		$project_ids = null;
		$conditions = [];

		App::import('Controller', 'Users');
		$Users = new UsersController;

		App::import('Model', 'UserProject');
		$UserProject = new UserProject;

		$myprojectlist = $myreceivedprojectlist = $mygroupprojectlist = array();
		// Find All current user's projects
		$myprojectlist = $Users->__myproject_selectbox($user_id);
		// Find All current user's received projects
		$myreceivedprojectlist = $Users->__receivedproject_selectbox($user_id, $level);
		// Find All current user's group projects
		$mygroupprojectlist = $Users->__groupproject_selectbox($user_id, $level);

		if (is_array($myprojectlist)) {
			$projects1 = $myprojectlist;
		}

		if (is_array($mygroupprojectlist)) {
			$projects1 = array_replace($mygroupprojectlist, $projects1);
		} else {
			$projects1 = $projects1;
		}

		if (is_array($myreceivedprojectlist) && is_array($projects1)) {
			$projects1 = array_replace($myreceivedprojectlist, $projects1);
		} else {
			$projects1 = $projects1;
		}

		if (!empty($projects1)) {
			$conditions['UserProject.project_id'] = array_keys($projects1);
		}

		if ((isset($conditions) && !empty($conditions)) || !empty($projects1)) {
			$conditions['Project.studio_status !='] = 1;
			$MyProjects = $UserProject->find('all', array('conditions' => $conditions, 'recursive' => 1, 'fields' => ['Project.id']));

			if (isset($MyProjects) && !empty($MyProjects)) {
				foreach ($MyProjects as $k => $v) {
					$project_ids[] = $v['Project']['id'];
				}
			}
		}
		return $project_ids;

	}
	//========================== Function for workcenter 3rd May =====================
	public function getProjectbyDetails($id = null) {
		App::import("Model", "Project");
		$project = new Project();
		$data = $project->find('all',
			array('conditions' => array('Project.id' => $id),'fields'=>array('Project.id','Project.title','Project.start_date','Project.end_date'), 'recursive' => -1)
		);
		return $data;
	}

	public function userprojectwc($pid, $uid) {
		$data = ClassRegistry::init('UserProject')->find('count', array('conditions' => array('UserProject.user_id' => $uid, 'UserProject.project_id' => $pid, 'UserProject.owner_user' => 1)));
		// e($uid);
		if ($pid == 80) {
			//pr($data);
		}
		// echo ClassRegistry::init('UserProject')->_query();

		return isset($data) ? $data : array();
	}


	public function restrict_copy_paste($target_wsp_id){
		$return = [
			'success' => false,
			'message' => 'Error in operation',
		];
		if(isset($target_wsp_id) && !empty($target_wsp_id)){
			$target_wsp_data = ClassRegistry::init('Workspace')->find('first', [
				'conditions' => [
					'Workspace.id' => $target_wsp_id,
				],
				'recursive' => -1,
				'fields' => ['Workspace.sign_off', 'Workspace.start_date', 'Workspace.end_date'],
			]);

			if(isset($target_wsp_data) && !empty($target_wsp_data)){
				$target_wsp_data = $target_wsp_data['Workspace'];
				if($target_wsp_data['sign_off'] != 1){
					if((isset($target_wsp_data['end_date']) && $target_wsp_data['end_date'] >= date('Y-m-d')) && (isset($target_wsp_data['start_date']) && $target_wsp_data['start_date'] <= date('Y-m-d 00:00:00'))) {
						$return['success'] = true;
					}
					if((isset($target_wsp_data['start_date']) && $target_wsp_data['start_date'] > date('Y-m-d 00:00:00')) ) {
						$return['message'] = "Workspace schedule has not reached the start date.";
					}

					if((isset($target_wsp_data['end_date']) && $target_wsp_data['end_date'] < date('Y-m-d')) ) {
						$return['message'] = "Workspace end date has passed.";
				 	}
				}
				else if( $target_wsp_data['sign_off']== 1) {
					$return['message'] = "Workspace has Signed-off.";
				}
				if(!isset($target_wsp_data['start_date'])) {
	                $return['message'] = "The Workspace has no date schedule.";
				}
			}

		}
		return $return;
	}
	// get element all permission
	public function element_manage_permission($element_id, $pid, $uid){
		$query = "SELECT user_permissions.*
					FROM `user_permissions`
					inner join elements on elements.id = user_permissions.element_id
				  WHERE
						user_permissions.element_id = $element_id and
						user_permissions.project_id = $pid and
						user_permissions.user_id = $uid
						and element_id is not null
					order by role ASC";

		$data = ClassRegistry::init('UserPermission')->query($query);
		return ( isset($data) && !empty($data[0]['user_permissions']) ) ? $data : array();
	}

	public function project_permission_details_ele($pid, $uid) {

		$query = "SELECT user_permissions.*
					FROM `user_permissions`
					inner join projects on projects.id = user_permissions.project_id
				  WHERE
					user_permissions.project_id = $pid and
					user_permissions.user_id = $uid and workspace_id is null
					order by role ASC";

		$data = ClassRegistry::init('UserPermission')->query($query);
		return ( isset($data) && !empty($data[0]['user_permissions']) ) ? $data : array();
	}


	public function getDepartmentList($id = null) {
		if( isset($id) && !empty($id) ){
			$department = ClassRegistry::init('Department')->find('first',['conditions'=>['Department.id'=>$id]]);
		} else {
			$department = ClassRegistry::init('Department')->find('list', ['order'=> 'Department.name ASC']);
		}
		return isset($department) ? $department : array();
	}

}
