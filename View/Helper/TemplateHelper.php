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
class TemplateHelper extends Helper {

    var $helpers = array('Html', 'Session', 'Thumbnail');	
	
	public function getThirdPartyUser(){
		$thirdparty = ClassRegistry::init('ThirdParty');
		$data = $thirdparty->find("list",array("conditions"=>array("ThirdParty.status"=>'1'),'fields'=>array('ThirdParty.id','ThirdParty.username')));
		return $data;
	}
	public function get_thirdparty_user($id=null){
		
		$thirdparty = ClassRegistry::init('ThirdParty');
		$data = $thirdparty->find("first",array("conditions"=>array("ThirdParty.status"=>'1','ThirdParty.id'=>$id)));
		return $data;
	}
	
	public function thirdPartyUserTemplate($user_id = null,$template_category_id=null){
		$TemplateRelation = ClassRegistry::init('TemplateRelation');
		$data = $TemplateRelation->find("all",array("conditions"=>array("TemplateRelation.thirdparty_id"=>$user_id,'template_category_id'=>$template_category_id),"order" => ["TemplateRelation.title ASC"]));
		return $data;
	}
	
	public function templateCategory(){
		$TemplateCategory = ClassRegistry::init('TemplateCategory');
		$data = $TemplateCategory->find("list",array("conditions"=>array("TemplateCategory.status"=>1),"order" => ("TemplateCategory.title ASC")));
		return $data;
	}
	
	public function templateReview($templateRelation_id = null){
		$TemplateReview = ClassRegistry::init('TemplateReview');
		$data = $TemplateReview->find("first",array("conditions"=>array("TemplateReview.template_relation_id"=>$templateRelation_id)));
		
		return isset($data['TemplateReview']['rating']) && (!empty($data['TemplateReview']['rating']) || $data['TemplateReview']['rating'] > 0 )? $data['TemplateReview']['rating']:0 ;
	}	
	
	public function templateAreaRelation($templateRelation_id = null){
		$AreaRelation = ClassRegistry::init('AreaRelation');
		$data = $AreaRelation->find("all",array("conditions"=>array("AreaRelation.template_relation_id"=>$templateRelation_id)));
		 
		 return isset($data) && (!empty($data) ) ? $data: null ;
	}
	
	
}
