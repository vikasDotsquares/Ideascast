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
class UserHelper extends Helper {

    var $helpers = array('Html', 'Session', 'Thumbnail', 'Wiki');

	public function workspace($project_id) {
		$workspace = ClassRegistry::init('Project');
        $pd = get_project_workspace($project_id);
        return $pd;
    }

    public function area($id) {
		$area = ClassRegistry::init('Area');
        $data = $area->find('all', array('conditions' => array('Area.workspace_id' => $id, 'Area.studio_status !=' => 1), 'fields' => array('Area.*'), 'recursive' => 1));
        return isset($data) ? $data : array();
    }

	public function areas($id) {
		$area = ClassRegistry::init('Area');
        $data = $area->find('all', array('conditions' => array('Area.workspace_id' => $id, 'Area.studio_status !=' => 1), 'fields' => array('Area.*'), 'recursive' => -1));
        return isset($data) ? $data : array();
    }

	function areass($workspace_id = null, $task_status = null ){

		$current_user_id = $this->Session->read('Auth.User.id');

		$status_conditions = '';
		$not_spacified = '';
		$not_started = '';
		$progress = '';
		$overdue = '';
		$completed = '';
		$status_conditions_main = $status_task_type = $status_conditions_main_last = '';


		if( isset($task_status) &&  !empty($task_status)  ){

			$status_conditions_main = "AND ( ";

			foreach( $task_status as $status_list ){

				if( !empty($status_list) && $status_list == 'undefined' ){
					$not_spacified = 1 ;
				}
				if( !empty($status_list) && $status_list == 'not_started' ){
					$not_started = 1;
				}
				if( !empty($status_list) && $status_list == 'in_progress' ){
					$progress = 1;
				}
				if( !empty($status_list) && $status_list == 'overdue' ){
					$overdue = 1;
				}
				if( !empty($status_list) && $status_list == 'completed' ){
					$completed = 1;
				}

			}
			$status_conditions = '';
			if( isset($not_spacified) && $not_spacified == 1 ){
				$status_conditions .=" (Element.date_constraints=0) ";
			}
			if( isset($not_started) && $not_started == 1 ){
				if( !empty($status_conditions) ){
					$status_conditions .=" OR (Element.sign_off=0 AND DATE(NOW())<DATE(Element.start_date))  ";
				} else {
					$status_conditions .=" (Element.sign_off=0 AND DATE(NOW())<DATE(Element.start_date))  ";
				}

			}
			if( isset($progress) && $progress == 1 ){
				if( !empty($status_conditions) ){
					$status_conditions .=" OR (Element.sign_off=0 AND DATE(NOW()) BETWEEN DATE(Element.start_date) AND 	DATE(Element.end_date) and Element.date_constraints=1 )   ";
				} else {
					$status_conditions .=" (Element.sign_off=0 AND DATE(NOW()) BETWEEN DATE(Element.start_date) AND 	DATE(Element.end_date) and Element.date_constraints=1 )  ";
				}
			}
			if( isset($overdue) && $overdue == 1 ){
				if( !empty($status_conditions) ){
					$status_conditions .=" OR (Element.sign_off=0 AND DATE(NOW())>DATE(Element.end_date))  ";
				} else {
					$status_conditions .="  (Element.sign_off=0 AND DATE(NOW())>DATE(Element.end_date))  ";
				}
			}
			if( isset($completed) && $completed == 1 ){
				if( !empty($status_conditions) ){
					$status_conditions .=" OR (Element.sign_off=1) # Status=Complete ";
				} else {
					$status_conditions .=" (Element.sign_off=1) # Status=Complete ";
				}
			}


			$status_conditions_main_last = ")";
		}


		//echo $status_conditions;

		$query = 'SELECT
			user_permissions.role, # Uncomment for testing
			user_permissions.user_id, # Uncomment for testing
			user_permissions.project_id, # Uncomment for testing
			user_permissions.workspace_id, # Uncomment for testing
			user_permissions.area_id,

			Area.title AS title,
			Area.id AS id,

			Element.studio_status AS studio_status


		FROM
			user_permissions
		INNER JOIN
			areas Area
			ON Area.id=user_permissions.area_id and Area.studio_status !=1
		INNER JOIN
			elements Element
			ON Element.id=user_permissions.element_id


		WHERE
			user_permissions.user_id='.$current_user_id.' # SET: CURRENT USER ID
			AND user_permissions.workspace_id='.$workspace_id.' # SET: CURRENT WORKSPACE ID

			AND user_permissions.area_id IS NOT NULL # Elements only, workspace row is not included in result
			'.$status_conditions_main.'
			'.$status_conditions.'
			'.$status_conditions_main_last.'
			'.$status_task_type.'

		GROUP BY
			user_permissions.area_id
		ORDER BY
			user_permissions.area_id, Element.sort_order';



		return ClassRegistry::init('UserPermission')->query($query);

	}




	public function links($id) {
		$elementLink = ClassRegistry::init('ElementLink');
        $data = $elementLink->find('all', array('conditions' => array('ElementLink.element_id' => $id), 'fields' => array('ElementLink.*'), 'recursive' => 1, 'order' => 'ElementLink.title'));
        return isset($data) ? $data : array();
    }

	public function notes($id) {
		$elementNote = ClassRegistry::init('ElementNote');
        $data = $elementNote->find('all', array('conditions' => array('ElementNote.element_id' => $id), 'fields' => array('ElementNote.*'), 'recursive' => 1, 'order' => 'ElementNote.title'));
        return isset($data) ? $data : array();
    }

	public function documents($id) {
		$elementDocument = ClassRegistry::init('ElementDocument');
        $data = $elementDocument->find('all', array('conditions' => array('ElementDocument.element_id' => $id), 'fields' => array('ElementDocument.*'), 'recursive' => 1, 'order' => 'ElementDocument.title'));
        return isset($data) ? $data : array();
    }

	public function mms($id) {
		$elementMindmap = ClassRegistry::init('ElementMindmap');
        $data = $elementMindmap->find('all', array('conditions' => array('ElementMindmap.element_id' => $id), 'fields' => array('ElementMindmap.*'), 'recursive' => 1, 'order' => 'ElementMindmap.title'));
        return isset($data) ? $data : array();
    }

	public function decision($id) {
		$elementDecision = ClassRegistry::init('ElementDecision');
        $data = $elementDecision->find('all', array('conditions' => array('ElementDecision.element_id' => $id,), 'fields' => array('ElementDecision.*'), 'order' => 'ElementDecision.title'));

        return isset($data) ? $data : array();
    }

	public function feedbacks($id) {
		$feedback = ClassRegistry::init('Feedback');
        $data = $feedback->find('all', array('conditions' => array('Feedback.element_id' => $id), 'fields' => array('Feedback.*'), 'recursive' => 1, 'order' => 'Feedback.title'));
        return isset($data) ? $data : array();
    }

	public function votes($id) {
		$vote = ClassRegistry::init('Vote');
        $data = $vote->find('all', array('conditions' => array('Vote.element_id' => $id, 'VoteQuestion.id !=' => ''), 'fields' => array('Vote.*'), 'order' => 'Vote.Title', 'recursive' => 2));

        return isset($data) ? $data : array();
    }

	public function location_types() {
		$ult = ClassRegistry::init('UserLocationType');
        $data = $ult->find('all', array('conditions' => array('UserLocationType.status' =>1), 'recursive' => -1, 'fields' => ['id', 'location', 'icon']));

        return (isset($data) && !empty($data)) ? $data : false;
    }

	public function current_location($user_id = null) {
		$current_user_id = (isset($user_id) && !empty($user_id)) ? $user_id : $this->Session->read('Auth.User.id');

		$ul = ClassRegistry::init('UserLocation');
		$query = "SELECT ult.location FROM user_locations ul LEFT JOIN user_location_types ult ON ult.id = ul.user_location_type_id WHERE ul.user_id = $current_user_id ORDER BY ul.id DESC limit 1";
		$data = $ul->query($query);
        // pr($data);
        return (isset($data) && !empty($data)) ? $data[0]['ult']['location'] : 'Private';
    }

	public function user_unavailability($user_id = null) {

		$current_user_id = (isset($user_id) && !empty($user_id)) ? $user_id : $this->Session->read('Auth.User.id');

		$current_time = $this->Wiki->_displayDate( date('Y-m-d H:i:s'), $format = 'Y-m-d H:i:s' );

		$avail = ClassRegistry::init('Availability');

		$query = "SELECT * FROM availabilities
				WHERE
				user_id = $current_user_id
				order by avail_start_date ASC ";
		$data = $avail->query($query);

		$unavail = false;


        if(isset($data) && !empty($data)){
        	foreach ($data as $key => $value) {
        		$breakstdates = explode(" ", $value['availabilities']['avail_start_date']);
        		if ($breakstdates[1] == '00:00:00') {
					$start_date = date('Y-m-d H:i:s', strtotime($breakstdates[0].' 12:00:00 AM'));
				}
				else {
        			$start_date = date('Y-m-d H:i:s', strtotime($value['availabilities']['avail_start_date']));
				}
        		$end_date = date('Y-m-d H:i:s', strtotime($value['availabilities']['avail_end_date']));

        		/*if($user_id != $this->Session->read('Auth.User.id')) {
        			$timezone = ClassRegistry::init('Timezone')->findByUserId($this->Session->read('Auth.User.id'));
					// $target_time_zone = new DateTimeZone($timezone['Timezone']['name']);
        			e($timezone['Timezone']['name']);
	        		$usersTimezone = new DateTimeZone($timezone['Timezone']['name']);
					$l10nDate = new DateTime($start_date);
					$l10nDate->setTimeZone($usersTimezone);
					echo $l10nDate->format('Y-m-d H:i:s');



	        		$start_date = $this->Wiki->_displayDate( $start_date , $format = 'Y-m-d H:i:s' );
	        		$end_date = $this->Wiki->_displayDate( $end_date , $format = 'Y-m-d H:i:s' );
	        	}*/

			    if(strtotime($start_date) <= strtotime($current_time) && strtotime($end_date) >= strtotime($current_time)){
			    	$unavail = true;
			    }
        	}
        }

        //

        return ($unavail) ? 'Absent' : 'Working';
    }


    function grp_users($user_id = null){
    	$user_id = (isset($user_id) && !empty($user_id)) ? $user_id : $this->Session->read('Auth.User.id');
    	$query = "SELECT pg.id, pg.title, ud.user_id, ud.first_name, ud.last_name, CONCAT_WS(' ', ud.first_name, ud.last_name) as full_name from project_group_users pgu LEFT JOIN user_details ud On pgu.user_id = ud.user_id LEFT JOIN project_groups pg ON pg.id = pgu.project_group_id where pgu.approved = 1 AND pg.group_owner_id = $user_id ";
    	$data = ClassRegistry::init('ProjectGroup')->query($query);
    	return $data;
    }


    function grp_users_data($user_id = null, $or = ""){
    	$or = (isset($or) && !empty($or)) ? " OR $or " : "";
    	//$not_in = (isset($user_id) && !empty($user_id)) ? " u.id NOT IN (".implode(',', $user_id).") AND " : "";
    	$not_in =   "";
    	$user_id = implode(",", $user_id);
    	$query = "SELECT u.email, ud.user_id, ud.first_name, ud.last_name, CONCAT_WS(' ', ud.first_name, ud.last_name) as full_name from user_details ud LEFT JOIN users u ON u.id = ud.user_id where $not_in u.status = 1 AND u.role_id = 2 $or ORDER BY ud.first_name ASC";
    	// echo $query;die;
    	$data = ClassRegistry::init('User')->query($query);
    	return $data;
    }

    function check_user_token($user_id = null, $token = null){

    	$result = false;

    	$current_user_id = (isset($user_id) && !empty($user_id)) ? $user_id : $this->Session->read('Auth.User.id');
    	if( !isset($token) || empty($token) ) {
    		return $result;
    	}
    	$query = "SELECT u.id, u.login_token from users u WHERE u.id = $current_user_id AND login_token = '$token'";

    	$data = ClassRegistry::init('User')->query($query);

    	if( isset($data) && !empty($data) ) {
    		$result = true;
    	}
    	return $result;
    }

}
